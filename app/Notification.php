<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'was_read', 'notification_type_id', 'was_justified', 'justification', 'was_archived', 'status_type_id'
    ];
}
