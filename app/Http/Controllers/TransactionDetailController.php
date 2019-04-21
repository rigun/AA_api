<?php

namespace App\Http\Controllers;

use App\TransactionDetail;
use App\Transaction;
use App\VehicleCustomer;
use App\Person;
use Illuminate\Http\Request;

class TransactionDetailController extends Controller
{
    public function getCustomer($id){
        $transaction = Transaction::find($id);
        return Person::find($transaction->customer_id);
    }
    public function showByTransaction($transactionId){
        return TransactionDetail::where('transaction_id',$transactionId)->with('vehicleCustomer')->get();
    }
    public function update(Request $request,$vehiclecustomerid){
        $this->validateWith([
            'licensePlate' => 'required',
            'vehicle_id' => 'required'
        ]);
        $vehicleCustomer = VehicleCustomer::find($vehiclecustomerid);
        $vehicleCustomer->licensePlate = $request->licensePlate;
        $vehicleCustomer->vehicle_id = $request->vehicle_id;
        $vehicleCustomer->save();

        return response()->json(['status'=>'1','msg'=>'Data berhasil dimasukkan','result' => $vehicleCustomer->first()]);                
    }
    public function destroy($transactionDetailId){
        $dt = TransactionDetail::find($transactionDetailId);
        $dt->delete();
        return 'Berhasil';
    }
    public function myvehicle($transactionDetailId){
        $dt = TransactionDetail::find($transactionDetailId);
        return $dt->vehicleCustomer()->first();
    }
    public function store(Request $request){
        $this->validateWith([
            'licensePlate' => 'required',
            'vehicle_id' => 'required',
            'transaction_id' => 'required'
        ]);
        $transaction = Transaction::find($request->transaction_id);
        $vehicleCustomer = new VehicleCustomer();
        $vehicleCustomer->licensePlate = $request->licensePlate;
        $vehicleCustomer->vehicle_id = $request->vehicle_id;
        $vehicleCustomer->customer_id = $transaction->customer_id;
        $vehicleCustomer->save();
        
        $detailTransaction = new TransactionDetail();
        $detailTransaction->transaction_id = $transaction->id;
        $detailTransaction->vehicleCustomer_id = $vehicleCustomer->id;
        $detailTransaction->montir_id = $request->montir_id;
        $detailTransaction->save();

        return response()->json(['status'=>'1','msg'=>'Data berhasil dimasukkan','result' => $transaction->first()]);                
    }
}
