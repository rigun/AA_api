<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function supplier(){
        return $this->belongsTo('App\Person','supplier_id','id');
    }
    public function detail(){
        return $this->hasMany('App\OrderDetail','order_id','id')->with('sparepart');
    }
    public function sales(){
        return $this->hasOne('App\Person','id','sales_id');
    }
}
