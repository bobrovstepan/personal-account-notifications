<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationCategory: string
{
    case System = 'system';
    case Marketing = 'marketing';
}
