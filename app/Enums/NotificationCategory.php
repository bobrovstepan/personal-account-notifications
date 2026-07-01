<?php

declare(strict_types=1);

namespace App\Enums;

use App\DTO\Contracts\NotificationData;
use App\DTO\MarketingNotificationData;
use App\DTO\SystemNotificationData;

enum NotificationCategory: string
{
    case System = 'system';
    case Marketing = 'marketing';

    public function toData(string $title, string $message, ?string $ctaUrl = null): NotificationData
    {
        return match ($this) {
            self::System => new SystemNotificationData($title, $message),
            self::Marketing => new MarketingNotificationData($title, $message, $ctaUrl),
        };
    }
}
