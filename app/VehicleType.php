<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    protected $table = 'vehicles_types';

    protected $fillable = [
        'vehicle_type_name'
    ];

    public function vehicle()
    {
    	return $this->belongsTo('App\Vehicle');
    }
   
}
