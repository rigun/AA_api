<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    public function employee(){
        return $this->hasMany('App\Employee')->with('detail');
    }
    public function transaction(){
        return $this->hasMany('App\Transaction');
    }
    public function transactionSuccess(){
        return $this->hasMany('App\Transaction')->where('status','3');
    }
    public function spareparts(){
        return $this->hasMany('App\SparepartBranch');
    }
}
