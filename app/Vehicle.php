<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{   
    
    protected $table = 'vehicles';

    protected $fillable = [
        'make', 'model', 'year', 'color', 'vin', 'type_id', 'serial_number', 'property_number', 'marbete_date', 'inspection_date', 'decomission_date', 'registration_id', 'title_id', 'doors', 'cylinders', 'ACAA', 'insurance', 'purchase_price', 'inscription_date','license_plate', 'filename', 'mime', 'original_filename', 'department_id', 'custodian_id'
    ];


    public function custodian()
    {
    	return $this->belongsTo('App\Custodian');
    }

    public function department()
    {
    	return $this->belongsTo('App\Department');
    }
}

