<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    protected $table = 'notification_types';

    protected $fillable = [
        'notification_type_name'
    ];
   
}
