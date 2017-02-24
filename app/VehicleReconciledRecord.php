<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VehicleReconciledRecord extends Model
{
    protected $table = 'vehicle_reconciled_records';

    protected $fillable = [
        'vehicle_usage_record_id', 'comments'
    ];

    public function vehicleUsageRecord()
    {
    	return $this->belongsTo('App\VehicleUsageRecord');
    }
}
