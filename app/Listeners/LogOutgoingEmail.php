<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class LogOutgoingEmail
{
    public function handle(MessageSending $event): void
    {
        // Allows deployments to run safely while the migration is being applied.
        if (! Schema::hasTable('email_log')) {
            return;
        }

        $message = $event->message;
        $date = now();
        $from = $this->addressHeader($message, 'From');
        $to = $this->addressHeader($message, 'To');
        $cc = $this->addressHeader($message, 'Cc');
        $bcc = $this->addressHeader($message, 'Bcc');
        $subject = $message->getSubject() ?? '';
        $body = $message->getBody()?->bodyToString();
        $headers = $message->getHeaders()->toString();

        // A message can pass through more than one mail transport hook. Keep
        // a short-lived cache marker instead of storing a fingerprint in the
        // email log table.
        $cacheKey = 'email-log:'.hash('sha256', implode("\0", [
            $date->format('Y-m-d H:i'),
            $from,
            $to,
            $cc,
            $bcc,
            $subject,
            $body,
            $headers,
        ]));

        if (! Cache::add($cacheKey, true, now()->addMinute())) {
            return;
        }

        EmailLog::query()->create([
            'date' => $date,
            'from' => $from,
            'to' => $to,
            'cc' => $cc,
            'bcc' => $bcc,
            'subject' => $subject,
            'body' => $body,
            'headers' => $headers,
            'attachments' => $this->attachments($message),
        ]);
    }

    private function addressHeader(Email $message, string $name): ?string
    {
        return $message->getHeaders()->get($name)?->getBodyAsString();
    }

    private function attachments(Email $message): ?string
    {
        $attachments = collect($message->getAttachments())
            ->map(fn (DataPart $attachment): string => $attachment->getPreparedHeaders()->getHeaderBody('filename') ?? 'attachment')
            ->all();

        return $attachments === [] ? null : json_encode($attachments, JSON_THROW_ON_ERROR);
    }
}
