<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $hidden = [
        'branch_id', 'people_id',
    ];
    public function detail(){
        return $this->hasOne('App\Person','id','people_id')->with('role');
    }
    public function branch(){
        return $this->hasOne('App\Branch','id','branch_id');
    }
}
