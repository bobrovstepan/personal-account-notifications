<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTO\NotificationListQuery;
use App\DTO\PaginationResult;
use App\Models\Notification;
use App\Models\User;

interface NotificationRepositoryInterface
{
    public function getList(User $user, NotificationListQuery $query): PaginationResult;

    public function findById(User $user, string $id): Notification;

    public function countUnread(User $user): int;

    public function markAsRead(Notification $notification): void;

    public function markAllAsRead(User $user): void;
}
