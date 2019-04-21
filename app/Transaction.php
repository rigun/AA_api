<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function customer(){
        return $this->belongsTo('App\Person','customer_id','id');
    }
    public function detailTransaction(){
        return $this->hasMany('App\TransactionDetail','transaction_id','id')->with(['detailTransactionSparepart','detailTransactionService','vehicleCustomer']);
    }
}
