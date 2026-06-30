<?php

declare(strict_types=1);

use App\DTO\MarketingNotificationData;
use App\DTO\SystemNotificationData;
use App\Models\User;
use App\Notifications\MarketingNotification;
use App\Notifications\SystemNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('sends a system notification and queues it', function () {
    Notification::fake();

    $this->actingAs($this->user)
        ->postJson('/api/notifications', [
            'category' => 'system',
            'title' => 'System alert',
            'message' => 'Something happened.',
        ])
        ->assertStatus(202)
        ->assertJsonStructure(['message']);

    Notification::assertSentTo($this->user, SystemNotification::class);
});

it('sends a marketing notification with cta_url', function () {
    Notification::fake();

    $this->actingAs($this->user)
        ->postJson('/api/notifications', [
            'category' => 'marketing',
            'title' => 'Special offer',
            'message' => 'Check this out.',
            'cta_url' => 'https://example.com/offer',
        ])
        ->assertStatus(202);

    Notification::assertSentTo($this->user, MarketingNotification::class);
});

it('validates required fields on store', function () {
    $this->actingAs($this->user)
        ->postJson('/api/notifications', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['category', 'title', 'message']);
});

it('rejects invalid category on store', function () {
    $this->actingAs($this->user)
        ->postJson('/api/notifications', [
            'category' => 'unknown',
            'title' => 'Title',
            'message' => 'Message',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['category']);
});

it('returns empty notifications list for authenticated user', function () {
    $response = $this->actingAs($this->user)
        ->getJson('/api/notifications');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'notifications',
            'current_page',
            'last_page',
            'total',
            'per_page',
        ])
        ->assertJsonPath('total', 0);
});

it('returns 401 for unauthenticated request', function () {
    $this->getJson('/api/notifications')
        ->assertStatus(401);
});

it('returns notifications with pagination meta', function () {
    $this->user->notify(new SystemNotification(
        new SystemNotificationData('Test Title', 'Test message'),
    ));

    $response = $this->actingAs($this->user)
        ->getJson('/api/notifications');

    $response->assertStatus(200)
        ->assertJsonPath('total', 1)
        ->assertJsonPath('current_page', 1)
        ->assertJsonStructure([
            'notifications' => [
                '*' => ['id', 'category', 'title', 'message', 'cta_url', 'read_at', 'created_at'],
            ],
        ])
        ->assertJsonPath('notifications.0.category', 'system')
        ->assertJsonPath('notifications.0.title', 'Test Title');
});

it('filters notifications by category', function () {
    $this->user->notify(new SystemNotification(
        new SystemNotificationData('System', 'System message'),
    ));
    $this->user->notify(new MarketingNotification(
        new MarketingNotificationData('Marketing', 'Marketing message'),
    ));

    $response = $this->actingAs($this->user)
        ->getJson('/api/notifications?category=system');

    $response->assertStatus(200)
        ->assertJsonPath('total', 1)
        ->assertJsonPath('notifications.0.category', 'system');
});

it('filters unread notifications only', function () {
    $this->user->notify(new SystemNotification(
        new SystemNotificationData('Unread', 'Unread message'),
    ));

    $notification = $this->user->notifications()->first();
    $notification->markAsRead();

    $this->user->notify(new SystemNotification(
        new SystemNotificationData('Still unread', 'Still unread message'),
    ));

    $response = $this->actingAs($this->user)
        ->getJson('/api/notifications?unread_only=1');

    $response->assertStatus(200)
        ->assertJsonPath('total', 1)
        ->assertJsonPath('notifications.0.title', 'Still unread');
});

it('marks a single notification as read', function () {
    $this->user->notify(new SystemNotification(
        new SystemNotificationData('Mark me', 'Message'),
    ));

    $notificationId = $this->user->notifications()->first()->id;

    $response = $this->actingAs($this->user)
        ->patchJson("/api/notifications/{$notificationId}");

    $response->assertStatus(200)
        ->assertJsonStructure(['notification' => ['id', 'read_at']])
        ->assertJsonPath('notification.id', $notificationId);

    expect($this->user->notifications()->find($notificationId)->read_at)->not->toBeNull();
});

it('marks all notifications as read', function () {
    $this->user->notify(new SystemNotification(
        new SystemNotificationData('First', 'Message 1'),
    ));
    $this->user->notify(new MarketingNotification(
        new MarketingNotificationData('Second', 'Message 2'),
    ));

    $this->actingAs($this->user)
        ->patchJson('/api/notifications')
        ->assertStatus(200)
        ->assertJsonStructure(['message']);

    expect($this->user->unreadNotifications()->count())->toBe(0);
});

it('returns 404 when marking non-existent notification as read', function () {
    $this->actingAs($this->user)
        ->patchJson('/api/notifications/non-existent-id')
        ->assertStatus(404);
});

it('does not allow marking another user notification as read', function () {
    $otherUser = User::factory()->create();
    $otherUser->notify(new SystemNotification(
        new SystemNotificationData('Other', 'Message'),
    ));

    $notificationId = $otherUser->notifications()->first()->id;

    $this->actingAs($this->user)
        ->patchJson("/api/notifications/{$notificationId}")
        ->assertStatus(404);
});

it('shows a single notification by id', function () {
    $this->user->notify(new SystemNotification(
        new SystemNotificationData('Show me', 'Message'),
    ));

    $notificationId = $this->user->notifications()->first()->id;

    $this->actingAs($this->user)
        ->getJson("/api/notifications/{$notificationId}")
        ->assertStatus(200)
        ->assertJsonStructure(['notification' => ['id', 'category', 'title', 'message', 'read_at', 'created_at']])
        ->assertJsonPath('notification.id', $notificationId);
});

it('returns 404 for show when notification belongs to another user', function () {
    $otherUser = User::factory()->create();
    $otherUser->notify(new SystemNotification(
        new SystemNotificationData('Other', 'Message'),
    ));

    $notificationId = $otherUser->notifications()->first()->id;

    $this->actingAs($this->user)
        ->getJson("/api/notifications/{$notificationId}")
        ->assertStatus(404);
});

it('returns unread count', function () {
    $this->user->notify(new SystemNotification(new SystemNotificationData('A', 'msg')));
    $this->user->notify(new MarketingNotification(new MarketingNotificationData('B', 'msg')));

    $notification = $this->user->notifications()->first();
    $notification->markAsRead();

    $this->actingAs($this->user)
        ->getJson('/api/notifications/unread')
        ->assertStatus(200)
        ->assertJsonPath('unread_count', 1);
});

it('queues system notification instead of sending synchronously', function () {
    Notification::fake();

    $this->user->notify(new SystemNotification(
        new SystemNotificationData('Queued', 'Message'),
    ));

    Notification::assertSentTo($this->user, SystemNotification::class);
});

it('queues marketing notification instead of sending synchronously', function () {
    Notification::fake();

    $this->user->notify(new MarketingNotification(
        new MarketingNotificationData('Promo', 'Check this out', 'https://example.com'),
    ));

    Notification::assertSentTo($this->user, MarketingNotification::class);
});
