<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VehicleSparepart extends Model
{
    public $timestamps = false;
    public function sparepart(){
        return $this->belongsToMany('App\Sparepart','vehicle_spareparts','vehicle_id','sparepart_code');
    }
}
