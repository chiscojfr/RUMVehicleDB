<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $table = 'user_types';

    protected $fillable = [
        'role'
    ];

    public function custodian()
    {
    	return $this->belongsToMany('App\Custodian','role_user', 'custodian_id', 'user_type_id');
    }

}
