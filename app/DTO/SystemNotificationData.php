<?php

declare(strict_types=1);

namespace App\DTO;

class SystemNotificationData
{
    public function __construct(
        public readonly string $title,
        public readonly string $message,
    ) {}
}
