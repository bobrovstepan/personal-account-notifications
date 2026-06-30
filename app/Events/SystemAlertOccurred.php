<?php

declare(strict_types=1);

namespace App\Events;

use App\DTO\SystemNotificationData;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SystemAlertOccurred
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly SystemNotificationData $data,
    ) {}
}
