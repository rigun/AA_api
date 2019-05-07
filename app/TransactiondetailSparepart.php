<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactiondetailSparepart extends Model
{
    public $timestamps = false;    
    public function sparepart(){
        return $this->hasOne('App\Sparepart','code','sparepart_code');
    }
    public function detailtransaction(){
        return $this->belongsTo('App\TransactionDetail','trasanctiondetail_id','id')->with('transaction');
    }
}
