<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    public function detail(){
        return $this->hasMany('App\VehicleCustomer','vehicle_id','id')->with('detailTransaction');
    }
}
