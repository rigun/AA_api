<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SparepartBranch extends Model
{
    public function sparepart(){
        return $this->belongsTo('App\Sparepart')->with('vehicle');
    }
    public function branch(){
        return $this->belongsTo('App\Branch');
    }
}
