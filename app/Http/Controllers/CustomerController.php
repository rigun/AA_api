<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\VehicleCustomer;
use App\Person;
class CustomerController extends Controller
{
    public function showMyTransaction(Request $request){
        if(!$customer = Person::where('phoneNumber',$request->phoneNumber)->first()){
            return response()->json(['status'=>0, 'msg'=>'Nomor telepon tidak terdaftar']);
        }
        if(!$cVehicle = VehicleCustomer::where('licensePlate',$request->licensePlate)->first()){
            return response()->json(['status'=>0, 'msg'=>'Plat nomor tidak ditemukan']);
        }
        $data = VehicleCustomer::where([['customer_id',$customer->id],['licensePlate',$request->licensePlate]])->with('detailTransaction')->first();
        
    }
}
