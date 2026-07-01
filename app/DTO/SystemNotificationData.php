<?php

declare(strict_types=1);

namespace App\DTO;

use App\DTO\Contracts\NotificationData;
use App\Notifications\SystemNotification;
use Illuminate\Notifications\Notification;

class SystemNotificationData implements NotificationData
{
    public function __construct(
        public readonly string $title,
        public readonly string $message,
    ) {}

    public function toNotification(): Notification
    {
        return new SystemNotification($this);
    }
}
