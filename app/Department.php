<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'name'
    ];

    public function custodian()
    {
    	return $this->hasOne('App\Custodian', 'department_id');
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
