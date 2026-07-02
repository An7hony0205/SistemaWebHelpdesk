<?php

namespace App\Domains\Notifications\Channels;

interface NotificationChannelInterface
{
    /**
     * Send a notification message through this channel.
     *
     * @param string $subject
     * @param string $body
     * @param mixed $recipient The recipient identifier (e.g., email address, slack ID)
     * @return bool True if sent successfully, false otherwise.
     */
    public function send(string $subject, string $body, mixed $recipient): bool;

    /**
     * Get the internal name of the channel (e.g., 'email', 'slack').
     */
    public function getName(): string;
}
