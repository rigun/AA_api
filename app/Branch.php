<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    public function employee(){
        return $this->hasMany('App\Employee')->with('detail');
    }
}
