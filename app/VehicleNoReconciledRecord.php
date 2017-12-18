<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleNoReconciledRecord extends Model
{

    protected $table = 'vehicle_no_reconciled_records';

    protected $fillable = [
        'vehicle_usage_record_id', 'comments'
    ];

    public function vehicleUsageRecord()
    {
    	return $this->belongsTo('App\VehicleUsageRecord');
    }

}
