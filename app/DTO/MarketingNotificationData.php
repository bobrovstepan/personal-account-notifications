<?php

declare(strict_types=1);

namespace App\DTO;

class MarketingNotificationData
{
    public function __construct(
        public readonly string $title,
        public readonly string $message,
        public readonly ?string $ctaUrl = null,
    ) {}
}
