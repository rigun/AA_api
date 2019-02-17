<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    //
    protected $primaryKey = 'code'; 
    public $incrementing = false; 

    public function vehicle(){
        return $this->hasMany('App/VehicleSparepart')->with('sparepart');
    }
}
