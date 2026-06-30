<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\NotificationCategory;

class NotificationListQuery
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?NotificationCategory $category = null,
        public readonly bool $unreadOnly = false,
    ) {}
}
