<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactiondetailService extends Model
{
    public $timestamps = false;    
    public function service(){
        return $this->hasOne('App\Service','id','service_id');
    }
}