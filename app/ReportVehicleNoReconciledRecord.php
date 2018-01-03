<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportVehicleNoReconciledRecord extends Model
{

    protected $table = 'report_no_conciliated_records';

    protected $fillable = [
        'vehicle_usage_record_id', 'comments'
    ];

    public function vehicleUsageRecord()
    {
    	return $this->belongsTo('App\VehicleUsageRecord');
    }

}
