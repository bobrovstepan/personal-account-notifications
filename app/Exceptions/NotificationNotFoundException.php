<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotificationNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct(__('notifications.not_found'));
    }
}
