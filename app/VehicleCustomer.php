<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VehicleCustomer extends Model
{
    public function vehicle(){
        return $this->belongsTo('App\Vehicle');
    }
    public function customer(){
        return $this->belongsTo('App\Person');
    }
}
