<?php

namespace App\Http\Controllers;

use App\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function store(Request $request, $type){
        $log = new Log();
        $log->sparepart_code = $request->sparepart_code;
        $log->buy = $request->buy;
        $log->sell = $request->sell;
        $log->totalBuy = $request->totalBuy;
        $log->transactionType = $type;
        $log->branch_id = $request->branch_id;
        $log->save();
        return;
    }
}
