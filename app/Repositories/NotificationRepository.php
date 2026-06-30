<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\NotificationListQuery;
use App\DTO\PaginationResult;
use App\DTO\QueryPaginator;
use App\Exceptions\NotificationNotFoundException;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function getList(User $user, NotificationListQuery $query): PaginationResult
    {
        $builder = Notification::query()
            ->where(Notification::NOTIFIABLE_TYPE, User::class)
            ->where(Notification::NOTIFIABLE_ID, $user->getKey())
            ->orderByDesc(Notification::CREATED_AT);

        if ($query->category !== null) {
            $builder->where(Notification::DATA.'->'.'category', $query->category->value);
        }

        if ($query->unreadOnly) {
            $builder->whereNull(Notification::READ_AT);
        }

        return QueryPaginator::paginate($builder, $query->perPage, $query->page);
    }

    public function findById(User $user, string $id): Notification
    {
        $notification = Notification::query()
            ->where(Notification::ID, $id)
            ->where(Notification::NOTIFIABLE_TYPE, User::class)
            ->where(Notification::NOTIFIABLE_ID, $user->getKey())
            ->first();

        if ($notification === null) {
            throw new NotificationNotFoundException;
        }

        return $notification;
    }

    public function countUnread(User $user): int
    {
        return Notification::query()
            ->where(Notification::NOTIFIABLE_TYPE, User::class)
            ->where(Notification::NOTIFIABLE_ID, $user->getKey())
            ->whereNull(Notification::READ_AT)
            ->count();
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications()->update([Notification::READ_AT => now()]);
    }
}
