<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleNoReconciledRecord extends Model
{
	use SoftDeletes;

    protected $table = 'vehicle_no_reconciled_records';

    protected $fillable = [
        'vehicle_usage_record_id', 'comments'
    ];

    protected $dates = ['deleted_at'];

    public function vehicleUsageRecord()
    {
    	return $this->belongsTo('App\VehicleUsageRecord');
    }

}
