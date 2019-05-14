<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\VehicleCustomer;
use App\Person;
class CustomerController extends Controller
{
    public function callBackMyTransaction(Request $request){
        if(!$customer = Person::where('phoneNumber',$request->phoneNumber)->first()){
            return response()->json(['status'=>0, 'msg'=>'Nomor handphone tidak terdaftar']);
        }
        if(!$cVehicle = VehicleCustomer::where('licensePlate',$request->licensePlate)->first()){
            return response()->json(['status'=>0, 'msg'=>'Plat nomor tidak ditemukan']);
        }
        return response()->json(['status'=>1, 'cVehicleId' => VehicleCustomer::where([['customer_id',$customer->id],['licensePlate',$request->licensePlate]])->first()->id, 'phoneNumber'=>$request->phoneNumber]);
    }
    public function getMyTransaction($cVehicleId,$phoneNumber){
        if(!$customer = Person::where('phoneNumber',$phoneNumber)->first()){
            return response()->json(['status'=>0, 'msg'=>'Nomor handphone tidak terdaftar']);
        }
        $vC = VehicleCustomer::where('id', $cVehicleId)->first();
        if($vC->customer_id != $customer->id){
            return response()->json(['status'=>0, 'msg'=>'Data Tidak Ditemukan']);
        }
        $data = Transaction::whereHas('detailTransaction', function ($query) use ($cVehicleId){
            $query->whereHas('vehicleCustomer', function ($query2) use ($cVehicleId){
                $query2->where('id',$cVehicleId);
            });
        })->with(['detailcVehicle' => function($query) use ($cVehicleId){
            $query->whereHas('vehicleCustomer', function ($query2) use ($cVehicleId){
                $query2->where('id',$cVehicleId);
            });
        }, 'cs', 'cashier', 'branch'])->get();
        return response()->json(['status'=>1, 'msg'=>$data, 'licensePlate' => $vC->licensePlate]);
    }
}
