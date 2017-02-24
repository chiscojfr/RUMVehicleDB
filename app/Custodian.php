<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User; 

class Custodian extends User
{
    protected $table = 'custodians';

    protected $fillable = [
        'name', 'email', 'password', 'position', 'contact_number', 'employee_id', 'user_type_id', 'department_id'
    ];

    public function card()
    {
    	return $this->hasMany('App\Card');
    }

    // public function department()
    // {
    // 	return $this->hasOne('App\Department', 'department_id');
    // }

    public function vehicle()
    {
    	return $this->hasMany('App\Vehicle');
    }

     public function userType()
    {
    	return $this->belongsToMany('App\UserType','role_user', 'custodian_id', 'user_type_id');
    }

    public function vehicleUsageRecord()
    {
    	return $this->hasMany('App\VehicleUsageRecord');
    }

    public function vehicleReconciledRecord()
    {
    	return $this->hasMany('App\VehicleReconciledRecord');
    }

    public function vehicleNoReconciledRecord()
    {
    	return $this->hasMany('App\VehicleNoReconciledRecord');
    }

    public function getAuthPassword() {
        return $this->password;
    }




}

