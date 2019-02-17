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
    public function spareparts(){
        return $this->hasMany('App\SparepartBranch');
    }
}
