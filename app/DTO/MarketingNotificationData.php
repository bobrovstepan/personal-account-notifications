<?php

declare(strict_types=1);

namespace App\DTO;

use App\DTO\Contracts\NotificationData;
use App\Notifications\MarketingNotification;
use Illuminate\Notifications\Notification;

class MarketingNotificationData implements NotificationData
{
    public function __construct(
        public readonly string $title,
        public readonly string $message,
        public readonly ?string $ctaUrl = null,
    ) {}

    public function toNotification(): Notification
    {
        return new MarketingNotification($this);
    }
}
