<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesSupplier extends Model
{
    public $timestamps = false;    
    public function sales(){
        return $this->hasOne('App\Person','id','sales_id');
    }
}
