<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VehicleUsageRecord extends Model
{
    protected $table = 'vehicle_usage_records';

    protected $fillable = [
        'receipt_number','date', 'provider_number', 'purchase_type', 'total_liters', 'total_receipt', 'vehicle_mileage', 'vehicle_id', 'card_id', 'custodian_id','filename', 'mime', 'original_filename', 'comments'
    ];

    public function custodian()
    {
    	return $this->belongsTo('App\Custodian');
    }

    public function vehicle()
    {
    	return $this->belongsTo('App\Vehicle');
    }

    public function card()
    {
    	return $this->belongsTo('App\Card');
    }

}
