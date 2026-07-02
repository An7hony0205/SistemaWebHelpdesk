<?php

namespace App\Domains\Notifications\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Domains\Notifications\Models\NotificationTemplate;
use App\Domains\Notifications\Models\NotificationLog;
use App\Domains\Notifications\Channels\NotificationChannelInterface;
use App\Domains\Identity\User;

class ProcessNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function __construct(
        public int $tenantId,
        public string $eventName,
        public array $payload,
        public int $recipientId,
        public string $channelName
    ) {}

    public function handle(): void
    {
        $recipient = User::find($this->recipientId);
        if (!$recipient) return;

        // 1. Fetch Template
        $template = NotificationTemplate::where('tenant_id', $this->tenantId)
            ->where('event_name', $this->eventName)
            ->where('channel', $this->channelName)
            ->first();

        if (!$template) {
            // Si no hay plantilla, no enviamos nada
            return;
        }

        // 2. Render Template
        $subject = $this->renderTemplate($template->subject_template ?? '', $this->payload);
        $body = $this->renderTemplate($template->body_template, $this->payload);

        // 3. Resolve Channel
        $channel = $this->resolveChannel($this->channelName);
        if (!$channel) {
            $this->logNotification($recipient->id, 'failed', 'Channel not implemented: ' . $this->channelName);
            return;
        }

        // 4. Send
        $target = $this->resolveTarget($recipient, $this->channelName);
        $success = $channel->send($subject, $body, $target);

        // 5. Log
        if ($success) {
            $this->logNotification($recipient->id, 'sent', null);
        } else {
            $this->logNotification($recipient->id, 'failed', 'Channel send method returned false');
            // Lanzamos excepcion para que el Job reintente
            throw new \Exception("Failed to send notification via " . $this->channelName);
        }
    }

    private function renderTemplate(string $template, array $data): string
    {
        $rendered = $template;
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $rendered = str_replace('{{ ' . $key . ' }}', (string)$value, $rendered);
                $rendered = str_replace('{{' . $key . '}}', (string)$value, $rendered);
            }
        }
        return $rendered;
    }

    private function resolveChannel(string $channelName): ?NotificationChannelInterface
    {
        if ($channelName === 'email') {
            return new \App\Domains\Notifications\Channels\EmailChannel();
        }
        return null;
    }

    private function resolveTarget(User $user, string $channelName): string
    {
        if ($channelName === 'email') {
            return $user->email;
        }
        return '';
    }

    private function logNotification(int $userId, string $status, ?string $error)
    {
        NotificationLog::create([
            'tenant_id' => $this->tenantId,
            'user_id' => $userId,
            'event_name' => $this->eventName,
            'channel' => $this->channelName,
            'status' => $status,
            'error_message' => $error,
            'payload' => $this->payload,
            'sent_at' => $status === 'sent' ? now() : null,
        ]);
    }
}
