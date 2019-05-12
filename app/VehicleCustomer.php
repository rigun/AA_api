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
    public function detailTransaction(){
        return $this->hasMany('App\TransactionDetail','vehicleCustomer_id','id')->with('detailTransactionService');
    }
    public function detail(){
        return $this->hasMany('App\TransactionDetail','vehicleCustomer_id','id')->with('detailTransactionService');
    }
}
