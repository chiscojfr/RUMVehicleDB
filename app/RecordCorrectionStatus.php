<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecordCorrectionStatus extends Model
{
    protected $table = 'correction_status_types';

    protected $fillable = [
        'status_type_name'
    ];
}
