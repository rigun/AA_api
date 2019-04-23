<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function supplier(){
        return $this->belongsTo('App\Person','supplier_id','id');
    }
}
