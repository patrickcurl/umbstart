<?php

namespace App\Services;

use App\Models\Mailbox;
use App\Models\Media;
use App\Models\Message;
use App\Models\User;
use App\Services\Contracts\ImapConnection;
use DB;
use DOMDocument;
use Exception;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use PHPHtmlParser\Dom;
use Webklex\IMAP\Client;

class Mailer implements ImapConnection
{
    protected $mailbox;

    protected $team;

    protected $user;

    public function __construct(Mailbox $mailbox)
    {
        $this->mailbox = $mailbox;
        $this->user    = auth()->user();

        if (!$this->user) {
            throw new Exception('User not found!');
        }

        $this->team    = $this->user->activeTeam;

        if (!$this->team) {
            throw new Exception('User active team not found');
        }
    }

    public static function test()
    {
        try {
            Media::all()->each(function ($x) {
                $x->delete();
            });
            DB::table('messages')->truncate();
            DB::table('media')->truncate();
            \Storage::deleteDirectory(public_path('/mail/assets'));
            $m      = Mailbox::first();
            auth()->login(User::find(3));
            $mailer = new static($m);
            $mailer->fetch();
        } catch (\Exception $e) {
            \Log::info($e->getMessage() . ' on ' . $e->getLine() . ' in' . $e->getFile());
        }
        // $media = Media::get();
        // $media->each(function ($m) {
        //     $m->detectLabels();
        // });
        // foreach ($media as $m) {
        //     $m->detectLabels();
        // }

        // return $media->with('message');
    }

    public function fetch($limit = 10)
    {
        $client         = $this->getImapClient($this->mailbox);
        $folder         = $client->getFolder('INBOX');
        $emails         = $folder->query()->limit($limit)->get();
        $msgCount       = count($emails);
        echo "there are {$msgCount} emails";

        $message_mask       = \Webklex\IMAP\Support\Masks\MessageMask::class;
        $existingMessageIds = $this->mailbox->messages->pluck('uid')->toArray();

        foreach ($emails as $email) {
            $mask = $email->mask($message_mask);
            \Log::info('importing: ' . $email->getUid());
            // $html = $mask->getHTMLBodyWithEmbeddedUrlImages('embedded.images') ?? '';
            // echo "Adding message: {$email->getUid()}";
            $uid = $email->getUid();

            if (in_array($uid, $existingMessageIds)) {
                continue;
            }
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
                'mailbox_id'  => $this->mailbox->id,
                'team_id'     => $this->team->id
            ]);
            $attachments       = $email->getAttachments();
            $email_attachments = [];

            // foreach ($attachments as $attachment) {
            //     $filename = $attachment->getName() . '.' . $attachment->getExtension();
            //     $attachment->save(public_path('attachments'), $filename);
            //     $media = $email
            //     ->addMedia(public_path('attachments/' . $filename))
            //     ->toMediaCollection('email_attachments');
            //     $media->message_id = $message->id;
            //     $media->save();
            //     $email_attachments[] = [
            //         'name'    => $filename,
            //         'content' => $attachment->getImgSrc()
            //     ];
            // }
            // $message->attachments = $email_attachments;

            if ($email->hasHTMLBody()) {
                $html               = $email->getHTMLBody();
                $message->html_body = $html;
                $message->save();
                $this->importLargestImage($message);
            }

            $message->save();




            // if (!empty($html)) {
                    //     $data['images'] = $this->getImages($html);
                    // }
                    // $insertData[] = $data;
                    // unset($data);
            //     }
            //     // unset($messages);
            // }
            // unset($folder);
        }

        // foreach ($insertData as $ins) {
        // }
    }

    /**
 * Returns all img tags in a HTML string with the option to include img tag attributes
 *
 *
 * @example  $post_images[0]->html = <img src="example.jpg">
 *           $post_images[0]->attr->width = 500
 *
 * @param    $html_string  string   The HTML string
 * @param    $get_attrs    boolean  If TRUE all of the img tag attributes will be returned
 * @return   $post_images  array    An array of objects
 */
    public static function getImages($html)
    {
        $images = [];
        preg_match_all('@src="([^"]+)"@', $html, $images);
        $images = array_pop($images);

        return $images;
    }

    private function importLargestImage($message)
    {
        $images = $this->getImages($message->html_body);
        Image::configure(['driver' => 'gd']);
        $image_array    = [];
        $stopwords      = ['facebook', 'twitter', 'instagram', 'youtube', 'social', 'rss feed'];
        $fsize          = new \App\Services\FastImageSize\FastImageSize;

        foreach ($images as $image) {
            $break = 0;

            foreach ($stopwords as $word) {
                if (is_string($image) && strpos(strtolower($image), strtolower($word)) !== false) {
                    $break++;
                }
            }

            if ($break === 0) {
                try {
                    $src           = $image;
                    $tries         = 0;

                    while ($tries < 3) {
                        try {
                            $size          = $fsize->getImageSize($src);
                            $tries         = 3;
                        } catch (\Exception $e) {
                            $tries++;
                        }
                    }

                    if (is_array($size)) {
                        $area          = $size['width'] * $size['height'];
                    }

                    if ($area > 50) {
                        $image_array[] = [
                            'src'  => $src,
                            'size' => $area
                        ];
                    }
                    unset($tries);
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        $imgCollection = collect($image_array)->sortByDesc('size');

        foreach ($imgCollection as $imgToAdd) {
            $imgSrc        = $imgToAdd['src'];
            $tries         = 0;

            while ($tries < 3) {
                try {
                    $media         = $message
                    ->addMediaFromUrl($imgSrc)
                    ->withResponsiveImages()
                    ->toMediaCollection('email_images');
                    $tries = 3;
                } catch (\Exception $e) {
                    $tries++;
                    \Log::info($e->getMessage() . ' on ' . $e->getLine() . ' in ' . $e->getFile());

                    if ($tries === 3) {
                        continue;
                    }
                }
            }

            if (!empty($media)) {
                $path               = $media->getPath();
                $media->message_id  = $message->id;
                $media->mailbox_id  = $message->mailbox_id;
                $media->save();
            }
            unset($media);
            unset($path);
            unset($imgSrc);

            break;
        }

        // dd($imgCollection);
    }

    private function getUniqueFilename($url)
    {
        $info                   = pathinfo($url);
        $filename               = $info['filename'];
        $extension              = $info['extension'];
        $originalFilename       = $fileFullName       = "{$filename}.{$extension}";
        $count                  = 1;

        while (Storage::disk('mailbox')->exists("{$this->mailbox->id}/$fileFullName")) {
            $fileFullName = "{$filename}-{$count}.{$extension}";
            $count++;
        }

        return $fileFullName;
    }

    private function download($url)
    {
        // $client    = new \GuzzleHttp\Client();
        // $tmpStream = tmpfile();
        // $client->get($url, ['sink' => $tmpStream]);
        $filename = $this->getUniqueFilename($url);
        // rewind($tmpStream);
        // $file = "{$this->mailbox->id}/{$filename}";
        // Storage::disk('mailbox')->put($file, $tmpStream);
        // fclose($tmpStream);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($curl);
        curl_close($curl);

        return ['content' => $content, 'filename' => $this->getUniqueFilename($url)];
        // $response = $client->get($url, ['save_to' => public_path("mail/assets/$filename")]);
    }

    private function importImages($message)
    {
        $html                         = $message->html_body;
        // $htmlDom                      = new DOMDocument('1.0', 'UTF-8');
        // $htmlDom->recover             = true;
        // $htmlDom->strictErrorChecking = false;
        // @$htmlDom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $dom = new Dom;
        $dom->load($html);
        // $imageTags       = $htmlDom->getElementsByTagName('img');
        $images          = $dom->getElementsByTag('img');
        $extractedImages = [];

        Image::configure(['driver' => 'imagick']);
        $message_images = [];

        foreach ($images as $image) {
            $alt         = $image->getAttribute('alt');
            $style       = $image->getAttribute('style');
            $width       = $image->getAttribute('width');
            $height      = $image->getAttribute('height');
            $src         = $image->getAttribute('src');
            $ignoreWords = ['facebook', 'twitter', 'instagram', 'youtube', 'social', 'rss feed'];
            $breakout    = 0;

            if (strpos($style, 'display:none')) {
                $breakout++;
            }

            if ($width == '1') {
                $breakout++;
            }

            if ($height == '1') {
                $breakout++;
            }

            foreach ($ignoreWords as $word) {
                if (strpos(strtolower($alt), strtolower($word)) !== false) {
                    $breakout++;
                }

                if (strpos(strtolower($src), strtolower($word)) !== false) {
                    $breakout++;
                }
            }

            if ($breakout === 0) {
                $file        = $this->downloadGetPath($src);
                $img         = Image::make($file);
                $media       = $message
                ->addMediaFromUrl($src)
                ->toMediaCollection('inline_email_images');
                $media->message_id = $message->id;
                $media->save();
            }
            // $extractedImages[] = $imageTag->getAttribute('src');
        }

        // return $extractedImages;
    }

    private function getIgnoredFolders() : array
    {
        $ignoredFolders    = $this->mailbox->ignored_folders;
        $additionalFolders = ['Spam', 'Drafts', 'Sent Mail', 'Trash', 'Starred'];

        foreach ($additionalFolders as $folder) {
            if (!in_array($folder, $ignoredFolders)) {
                $ignoredFolders[] = $folder;
            }
        }

        return $ignoredFolders;
    }

    public function checkConnection()
    {
        $client = $this->getImapClient();
        $client->connect();

        return $client->isConnected();
    }

    public function getImapClient(Mailbox $mailbox = null)
    {
        try {
            $config                  = $this->mailbox->config;
            $config['validate_cert'] = false;

            return new Client($config);
        } catch (\Exception $e) {
            slog($e);
        }
    }
}
