<?php

namespace App\Tasks\Mail;

use App\Jobs\AddMessageToMailbox;
use App\Models\Mailbox;
use App\Models\Message;
use Carbon\Carbon;
use DB;
use Spatie\Async\Task;

class FetchMails extends Task
{
    protected $mailbox;

    protected $client;

    protected $existingIds;

    protected $mask;

    public function __construct(Mailbox $mailbox)
    {
        $this->mailbox     = $mailbox;
        $this->client      = $mailbox->getImapClient();
        $this->existingIds = $this->mailbox->messages->pluck('id')->toArray();
        $this->mask        = \Webklex\IMAP\Support\Masks\MessageMask::class;
    }

    public function configure()
    {
        $this->autoload(__DIR__ . '/../../vendor/autoload.php');
        $this->concurrency(10);
        $this->sleepTime(5000);
    }

    public function run()
    {
        $folder      = $this->client->getFolder('INBOX');
        $totalEmails = $folder->messages()->all()->count();
        $perPage     = 25;
        $pages       = ceil(bcdiv((string) $totalEmails, (string) $perPage, 2));
        $page        = 0;
        \Log::info('# pages: ' . $pages);

        $processed = 0;
        $this->client->setDefaultMessageMask($this->mask);


        while ($page < $pages) {
            if ($this->mailbox->imported_at === null) {
                $emails = $folder->messages()->all()->limit($perPage, $page)->get();
            } else {
                $emails = $folder->query()->since($this->mailbox->imported_at->subDay())->get();
            }

            foreach ($emails as $email) {
                $uid                     = $email->getUid();
                \Log::info($uid);

                if (!in_array($uid, $this->existingIds)) {
                    DB::beginTransaction();

                    try {
                        $messageData = [
                            'uid'         => $email->getUid(),
                            'subject'     => $email->getSubject(),
                            'from'        => $email->getFrom() ?? [],
                            'to'          => $email->getTo() ?? [],
                            'cc'          => $email->getCc() ?? [],
                            'bcc'         => $email->getBcc() ?? [],
                            'text_body'   => $email->getTextBody() ?? '',
                            // 'html_body'   => $html ?? '',
                            'received_at'  => $email->getDate(),
                            'mailbox_id'   => $this->mailbox->id,
                            'team_id'      => $this->mailbox->team_id,
                            'html_body'    => $email->hasHTMLBody() ? $email->getHTMLBody() : '',
                            'processed_at' => null
                        ];
                        AddMessageToMailbox::dispatch($this->mailbox, $messageData);
                        // $message = Message::create([

                        // ]);
                        // // $this->existingIds[] = $message->id;
                        // \Log::info('Remaining Emails: ' . ($totalEmails - ($processed++)));

                        // if ($email->hasHTMLBody()) {

                        //     $message->processed_at = null;
                        //     $message->save();
                        // }
                    } catch (\Exception $e) {
                        DB::rollback();
                    }
                    DB::commit();
                }
            }

            $page++;
        }
        $this->mailbox->imported_at = Carbon::now();
        $this->mailbox->save();
    }
}
