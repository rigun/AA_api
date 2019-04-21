<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    public $timestamps = false;    
    public function vehicleCustomer(){
        return $this->hasOne('App\VehicleCustomer','id','vehicleCustomer_id')->with('vehicle');
    }
    public function detailTransactionSparepart(){
        return $this->hasMany('App\TransactiondetailSparepart','trasanctiondetail_id','id')->with('sparepart');
    }
    public function detailTransactionService(){
        return $this->hasMany('App\TransactiondetailService','trasanctiondetail_id','id')->with('service');
    }
}
