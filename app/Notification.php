<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'custodian_id', 'record_id', 'was_read', 'record_not_found_info', 'notification_type_id'
    ];
}
