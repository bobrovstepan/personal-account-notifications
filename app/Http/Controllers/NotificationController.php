<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\MarketingNotificationData;
use App\DTO\NotificationListQuery;
use App\DTO\SystemNotificationData;
use App\Enums\NotificationCategory;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request, NotificationService $service): JsonResponse
    {
        $rawCategory = $request->input('category');

        $query = new NotificationListQuery(
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
            category: $rawCategory !== null ? NotificationCategory::tryFrom($rawCategory) : null,
            unreadOnly: (bool) $request->input('unread_only', false),
        );

        $result = $service->list(auth()->user(), $query);

        return response()->json([
            'notifications' => NotificationResource::collection($result->data),
            ...$result->meta(),
        ], 200);
    }

    public function store(StoreNotificationRequest $request, NotificationService $service): JsonResponse
    {
        $validated = $request->validated();

        match ($request->category()) {
            NotificationCategory::System => $service->sendSystem(
                auth()->user(),
                new SystemNotificationData($validated['title'], $validated['message']),
            ),
            NotificationCategory::Marketing => $service->sendMarketing(
                auth()->user(),
                new MarketingNotificationData($validated['title'], $validated['message'], $validated['cta_url'] ?? null),
            ),
        };

        return response()->json(['message' => __('notifications.sent')], 202);
    }

    public function show(Request $request, NotificationService $service, string $id): JsonResponse
    {
        $notification = $service->find(auth()->user(), $id);

        return response()->json([
            'notification' => NotificationResource::make($notification),
        ], 200);
    }

    public function unreadCount(Request $request, NotificationService $service): JsonResponse
    {
        return response()->json([
            'unread_count' => $service->countUnread(auth()->user()),
        ], 200);
    }

    public function markRead(Request $request, NotificationService $service, string $id): JsonResponse
    {
        $notification = $service->markAsRead(auth()->user(), $id);

        return response()->json([
            'notification' => NotificationResource::make($notification),
        ], 200);
    }

    public function markAllRead(Request $request, NotificationService $service): JsonResponse
    {
        $service->markAllAsRead(auth()->user());

        return response()->json(['message' => __('notifications.marked_all_read')], 200);
    }
}
