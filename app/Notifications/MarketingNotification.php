<?php

declare(strict_types=1);

namespace App\Notifications;

use App\DTO\MarketingNotificationData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MarketingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly MarketingNotificationData $data,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /** @return array<string, string|null> */
    public function toDatabase(object $notifiable): array
    {
        return [
            'category' => 'marketing',
            'title' => $this->data->title,
            'message' => $this->data->message,
            'cta_url' => $this->data->ctaUrl,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject(__('notifications.marketing_mail_subject', ['title' => $this->data->title]))
            ->line($this->data->message);

        if ($this->data->ctaUrl !== null) {
            $mail->action(__('notifications.marketing_cta_label'), $this->data->ctaUrl);
        }

        return $mail;
    }
}
