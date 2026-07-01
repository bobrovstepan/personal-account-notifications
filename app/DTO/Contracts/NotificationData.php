<?php

declare(strict_types=1);

namespace App\DTO\Contracts;

use Illuminate\Notifications\Notification;

interface NotificationData
{
    public function toNotification(): Notification;
}
