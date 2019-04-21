<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactiondetailSparepart extends Model
{
    public $timestamps = false;    
    public function sparepart(){
        return $this->hasOne('App\Sparepart','code','sparepart_code');
    }
}
