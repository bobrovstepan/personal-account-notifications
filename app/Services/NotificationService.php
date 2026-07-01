<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Contracts\NotificationData;
use App\DTO\NotificationListQuery;
use App\DTO\PaginationResult;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;

class NotificationService
{
    public function __construct(
        private readonly NotificationRepositoryInterface $repository,
    ) {}

    public function list(User $user, NotificationListQuery $query): PaginationResult
    {
        return $this->repository->getList($user, $query);
    }

    public function find(User $user, string $id): Notification
    {
        return $this->repository->findById($user, $id);
    }

    public function countUnread(User $user): int
    {
        return $this->repository->countUnread($user);
    }

    public function send(User $user, NotificationData $data): void
    {
        $user->notify($data->toNotification());
    }

    public function markAsRead(User $user, string $id): Notification
    {
        $notification = $this->repository->findById($user, $id);
        $this->repository->markAsRead($notification);

        return $notification;
    }

    public function markAllAsRead(User $user): void
    {
        $this->repository->markAllAsRead($user);
    }
}
