<?php

declare(strict_types=1);
namespace App\Models;

use App\Services\Contracts\ImapConnection;
use App\Services\Mailer;
use DB;
use DOMDocument;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Log;

class Mailbox extends Model implements ImapConnection
{
    // use UsedByTeams;

    protected $fillable = [
        'username',
        'password',
        'host',
        'port',
        'encryption',
        'validate_cert',
        'user_id',
        'active',
        'ignored_folders',
        'imported_at',
    ];

    protected $dates = [
        'imported_at'
    ];

    protected $casts = [
        'id'              => 'int',
        'ignored_folders' => 'array'
    ];

    protected $existingMessageIds;

    protected $messagesToFetch;

    protected $mask;

    protected $client;

    public function getPassword()
    {
        return Crypt::decryptString($this->password);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    public function setValidateCertAttribute($value)
    {
        $this->attributes['validate_cert'] = $value == true ? 1 : 0;
    }

    // public static function boot()
    // {
    //     parent::boot();

    //     static::saving(function ($mailbox) {
    //         $mailbox->password = Crypt::encryptString($this->password);
    //     });
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getCounts()
    {
        $messages          = DB::table('messages')->where('mailbox_id', $this->id)->get();
        $counts            = [];
        $counts['message'] = $messages->count();
        $counts['keyword'] = 0;

        foreach ($messages as $message) {
            $counts['keyword'] += DB::table('message_keywords')->where('message_id', $message->id)->count();
        }

        return $counts;
    }

    // public function getKeywordCountAttribute()
    // {
    //     $counts = $this->getCounts();

    //     return $counts['keyword'];
    // }

    public function getMessageCountAttribute()
    {
        $counts = $this->getCounts();

        return $counts['message'];
    }

    public function getConfigAttribute()
    {
        return [
            'host'          => $this->host,
            'port'          => $this->port ?? 993,
            'encryption'    => $this->encryption ?? 'ssl',
            'validate_cert' => $this->validate_cert ?? true,
            'username'      => $this->username,
            'password'      => $this->getPassword(),
            'protocol'      => 'imap'
        ];
    }

    public function canConnect()
    {
        // $mailer = new Mailer($this);

        return $this->checkConnection();
    }

    public static function test()
    {
        DB::beginTransaction();

        $mailbox = Mailbox::create([
            'username'      => env('TESTER_ACCOUNT_USERNAME'),
            'password'      => env('TESTER_ACCOUNT_PASSWORD'),
            'host'          => env('TESTER_ACCOUNT_HOST'),
            'port'          => env('TESTER_ACCOUNT_PORT'),
            'encryption'    => 'tls',
            'validate_cert' => false
        ]);

        try {
            $mailbox->canConnect();
            echo 'success!';
        } catch (\Exception $e) {
            \Log::info($mailbox->canConnect());
            \Log::info($e->getMessage() . ' | ' . $e->getLine() . ' | ' . $e->getFile());
        }
        DB::rollback();
    }

    public function getImages()
    {
        return Media::where('mailbox_id', $this->id);
    }

    public function checkConnection()
    {
        $client = $this->getImapClient();
        $client->connect();

        return $client->isConnected();
    }

    public function fetch()
    {
        try {
            $client                         = $this->getImapClient();
            $folders                        = $client->getFolders();
            $this->existingMessageIds       = $this->messages->pluck('uid')->toArray() ?? [];



            $pool   = \Spatie\Async\Pool::create()
                ->autoload(__DIR__ . '/../../vendor/autoload.php')
                ->concurrency(10)
                ->sleepTime(5000);
            $folder = $client->getFolder('INBOX');

            $emailCount = $folder->messages()->all()->count();
            $pages      = ceil(bcdiv((string) $emailCount, '50', 2));
            $page       = 0;
            \Log::info('# pages: ' . $pages);

            while ($page < $pages) {
                \Log::info('page("outside"): ' . $page);
                $pool->add(function () use ($page) {
                    \Log::info('page("inside"): ' . $page);
                    $client  = $this->getImapClient();
                    $folder = $client->getFolder('INBOX');
                    $emails = $folder->messages()->all()->limit(50, $page)->get();
                    $this->queueMessage($emails);
                })->catch(function (Throwable $exception) {
                    slog($exception);
                });
                $page++;
            }

            $pool->wait();
        } catch (\Exception $e) {
            slog($e);
        }
    }

    public function queueMessages($emails)
    {
        try {
            \Log::info('queueing emails: ' . count($emails));
            $pool   = \Spatie\Async\Pool::create()
                ->autoload(__DIR__ . '/../../vendor/autoload.php')
                ->concurrency(10)
                ->sleepTime(5000);

            foreach ($emails as $email) {
                $this->messagesToFetch[] = $email->getUid();
                $uid                     = $email->getUid();
                \Log::info($uid);

                if (!in_array($uid, $this->existingMessageIds)) {
                    $pool->add(function () use ($uid) {
                        $this->addMessage($uid);
                    })->catch(function (Throwable $exception) {
                        slog($exception);
                    });
                }
            }

            // foreach ($this->messagesToFetch as $uid) {
            //     try {
            //         $pool->add(function () use ($uid) {
            //             $this->addMessage($uid);
            //         })->catch(function (Throwable $exception) {
            //             slog($exception);
            //         });
            //     } catch (\Exception $e) {
            //         slog($e);
            //     }
            // }
            $pool->wait();
        } catch (\Exception $e) {
            slog($e);
        }
    }

    protected function addMessage($folder, $uid)
    {
        try {
            $client  = $this->getImapClient();
            $mask    = \Webklex\IMAP\Support\Masks\MessageMask::class;
            $folders = $client->getFolder('INBOX');
            $client->setDefaultMessageMask($mask);
            $email   = $folder->getMessage($uid);
            $message = Message::create([
                'uid'         => $email->getUid(),
                'subject'     => $email->getSubject(),
                'from'        => $email->getFrom() ?? [],
                'to'          => $email->getTo() ?? [],
                'cc'          => $email->getCc() ?? [],
                'bcc'         => $email->getBcc() ?? [],
                'text_body'   => $email->getTextBody() ?? '',
                // 'html_body'   => $html ?? '',
                'received_at' => $email->getDate(),
                'mailbox_id'  => $this->id,
                'team_id'     => $this->team_id
            ]);

            if ($email->hasHTMLBody()) {
                $message->html_body    = $email->getHTMLBody();
                $message->processed_at = null;
                $message->save();
            }
            $this->existingMessageIds[] = $message->id;
        } catch (\Exception $e) {
            slog($e);
        }
    }

    // protected function addMessages($emails)
    // {
    //     $pool               = \Spatie\Async\Pool::create();

    //     foreach ($emails as $email) {
    //         // $pool->add(function () use ($email) {
    //         $email->mask($this->mask);
    //         $uid  = $email->getUid();

    //         if (!in_array($uid, $this->existingMessageIds)) {
    //             \Log::info("Adding message with uid: ${uid}");
    //             $message = Message::create([
    //                 'uid'         => $email->getUid(),
    //                 'subject'     => $email->getSubject(),
    //                 'from'        => $email->getFrom() ?? [],
    //                 'to'          => $email->getTo() ?? [],
    //                 'cc'          => $email->getCc() ?? [],
    //                 'bcc'         => $email->getBcc() ?? [],
    //                 'text_body'   => $email->getTextBody() ?? '',
    //                 // 'html_body'   => $html ?? '',
    //                 'received_at' => $email->getDate(),
    //                 'mailbox_id'  => $this->id,
    //                 'team_id'     => $this->team_id
    //             ]);

    //             if ($email->hasHTMLBody()) {
    //                 $message->html_body    = $email->getHTMLBody();
    //                 $message->processed_at = null;
    //                 $message->save();
    //             }
    //             $this->existingMessageIds[] = $message->id;
    //         }
    //         // });
    //     }
    // }

    public function getImapClient()
    {
        try {
            $config                  = $this->config;
            $config['validate_cert'] = false;

            if (!$this->client instanceof \Webklex\IMAP\Client) {
                $this->client = new \Webklex\IMAP\Client($config);
            }

            return $this->client;
        } catch (\Exception $e) {
            slog($e);
        }
    }

    // public function connect(){
    //     $client =>
    // }
}
