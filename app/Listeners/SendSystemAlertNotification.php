<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\SystemAlertOccurred;
use App\Services\NotificationService;

class SendSystemAlertNotification
{
    public function __construct(
        private readonly NotificationService $service,
    ) {}

    public function handle(SystemAlertOccurred $event): void
    {
        $this->service->send($event->user, $event->data);
    }
}
