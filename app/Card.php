<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
	protected $table = 'cards';

    protected $fillable = [
        'number', 'name', 'type', 'expiry', 'status', 'cardID', 'custodian_id', 'department_id', 'auxiliary_custodian_id'
    ];

    public function custodian()
    {
    	return $this->belongsToMany('App\Custodian');
    }

    public function department()
    {
    	return $this->belongsTo('App\Department');
    }
}


