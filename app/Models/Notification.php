<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    public const string DB_TABLE = 'notifications';

    public const string ID = 'id';

    public const string TYPE = 'type';

    public const string NOTIFIABLE_TYPE = 'notifiable_type';

    public const string NOTIFIABLE_ID = 'notifiable_id';

    public const string DATA = 'data';

    public const string READ_AT = 'read_at';

    public const string CREATED_AT = 'created_at';

    public const string UPDATED_AT = 'updated_at';
}
