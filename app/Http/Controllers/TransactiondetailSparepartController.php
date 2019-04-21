<?php

namespace App\Http\Controllers;

use App\TransactiondetailSparepart;
use App\Transaction;
use Illuminate\Http\Request;

class TransactiondetailSparepartController extends Controller
{
    public function showByTransactionDetail($transactionDetailId,$branchId){
        $ts = TransactiondetailSparepart::where('trasanctiondetail_id',$transactionDetailId)->with('sparepart')->get();
        $sparepartController = new SparepartController();
        foreach($ts as $key => $t){
            $try[$key]['data'] = $t;
            $try[$key]['position'] = $sparepartController->getPosition($t->sparepart_code,$branchId);
        }
        return $try;
    }
    public function store(Request $request){
        $this->validateWith([
            'trasanctiondetail_id' => 'required',
            'sparepart_code' => 'required',
            'total' => 'required',
            'branch_id' => 'required'
        ]);
        $ts = new TransactiondetailSparepart();
        $ts->trasanctiondetail_id = $request->trasanctiondetail_id;
        $ts->sparepart_code = $request->sparepart_code;
        $sparepartController = new SparepartController();
        $ts->total = $request->total;
        $ts->price =$sparepartController->getPrice($request->sparepart_code,$request->branch_id);
        $ts->save();
        return 'Berhasil';
    }
    public function update(Request $request, $tspId){
        $this->validateWith([
            'sparepart_code' => 'required',
            'total' => 'required'
        ]);
        $ts = TransactiondetailSparepart::find($tspId);
        $ts->sparepart_code = $request->sparepart_code;
        $ts->total = $request->total;
        $ts->save();
        return 'Berhasil';
    }
    public function destroy($tspId){
        $ts = TransactiondetailSparepart::find($tspId);
        $ts->delete();
        return 'Berhasil';
    }
}
