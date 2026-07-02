<?php

namespace App\Domains\Notifications\Channels;

use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailChannel implements NotificationChannelInterface
{
    public function send(string $subject, string $body, mixed $recipient): bool
    {
        try {
            Mail::html($body, function (Message $message) use ($subject, $recipient) {
                $message->to($recipient)
                    ->subject($subject);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('EmailChannel failed: '.$e->getMessage());

            return false;
        }
    }

    public function getName(): string
    {
        return 'email';
    }
}
