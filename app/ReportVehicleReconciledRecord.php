<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportVehicleReconciledRecord extends Model
{
    protected $table = 'report_conciliated_records';

    protected $fillable = [
        'vehicle_usage_record_id', 'comments'
    ];

    public function vehicleUsageRecord()
    {
    	return $this->belongsTo('App\VehicleUsageRecord');
    }
}
