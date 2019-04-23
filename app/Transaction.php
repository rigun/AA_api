<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function customer(){
        return $this->belongsTo('App\Person','customer_id','id');
    }
    public function cs(){
        return $this->belongsTo('App\Person','cs_id','id');
    }
    public function cashier(){
        return $this->belongsTo('App\Person','cashier_id','id');
    }
    public function detailTransaction(){
        return $this->hasMany('App\TransactionDetail','transaction_id','id')->with(['vehicleCustomer','montir']);
    }
}
