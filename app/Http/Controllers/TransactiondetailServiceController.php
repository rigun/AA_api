<?php

namespace App\Http\Controllers;

use App\TransactiondetailService;
use Illuminate\Http\Request;

class TransactiondetailServiceController extends Controller
{
    public function showByTransactionDetail($transactionDetailId){
        return TransactiondetailService::where('trasanctiondetail_id',$transactionDetailId)->with('service')->get();
    }
    public function store(Request $request){
        $this->validateWith([
            'trasanctiondetail_id' => 'required',
            'service_id' => 'required',
            'total' => 'required'
        ]);
        $ts = new TransactiondetailService();
        $ts->trasanctiondetail_id = $request->trasanctiondetail_id;
        $ts->service_id = $request->service_id;
        $serviceController = new ServiceController();
        $ts->total = $request->total;
        $ts->price =$serviceController->getPrice($request->service_id);
        $ts->save();
        return 'Berhasil';
    }
    public function update(Request $request, $tspId){
        $this->validateWith([
            'service_id' => 'required',
            'total' => 'required'
        ]);
        $ts = TransactiondetailService::find($tspId);
        $ts->service_id = $request->service_id;
        $ts->total = $request->total;
        $ts->save();
        return 'Berhasil';
    }
    public function destroy($tspId){
        $ts = TransactiondetailService::find($tspId);
        $ts->delete();
        return 'Berhasil';
    }
}
