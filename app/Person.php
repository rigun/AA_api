<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    //
    public function role(){
        return $this->hasOne('App\Role','id','role_id');
    }
    public function user(){
        return $this->belongsTo('App\User','id','people_id');
    }
    public function transaction(){
        return $this->hasMany('App\Transaction','customer_id','id');
    }
    public function salesSupplier(){
        return $this->hasOne('App\SalesSupplier','sales_id','id');
    }
}
