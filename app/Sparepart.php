<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    //
    protected $primaryKey = 'code'; 
    public $incrementing = false; 

    public function vehicle(){
        return $this->belongsToMany('App\Vehicle','vehicle_spareparts','sparepart_code','vehicle_id');
    }
    public function supplier(){
        return $this->belongsTo('App\Person','supplier_id','id');
    }
}
