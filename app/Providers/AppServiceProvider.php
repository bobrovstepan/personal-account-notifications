<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\SystemAlertOccurred;
use App\Listeners\SendSystemAlertNotification;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
    }

    public function boot(): void
    {
        Event::listen(SystemAlertOccurred::class, SendSystemAlertNotification::class);
    }
}
