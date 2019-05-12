<?php

namespace App\Http\Controllers;

use App\Vehicle;
use App\VehicleCustomer;
use App\VehicleSparepart;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function totalData(){
        return Vehicle::count();
    }
    public function index()
    {
        return Vehicle::all();
    }

    public function store(Request $request){
        $this->validateWith([
            'merk' => 'required',
            'type' => 'required',
        ]);
        $item = new Vehicle();
        $item->merk = $request->merk;
        $item->type = $request->type;
        $item->save();
        return response()->json(['status'=>'1','msg'=>'Kendaraan '.$item->merk.' berhasil dibuat','result' => $item]);
    }
    public function update(Request $request,$id){
        $this->validateWith([
            'merk' => 'required',
            'type' => 'required',
        ]);
        if($item = Vehicle::where('id',$id)->first()){
            $item->merk = $request->merk;
            $item->type = $request->type;
            $item->save();    
            return response()->json(['status'=>'1','msg'=>'Kendaraan berhasil diubah menjadi '.$item->merk,'result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Kendaraan tidak ditemukan','result' => []]);
    }
    public function show($id){
        if($item = Vehicle::where('id',$id)->first()){
            return response()->json(['status'=>'1','msg'=>'Kendaraan berhasil ditemukan','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Kendaraan tidak ditemukan','result' => []]);
    }
    public function destroy($id){
        if($item = Vehicle::where('id',$id)->first()){
            $item->delete();
            return response()->json(['status'=>'1','msg'=>'Kendaraan berhasil dihapus','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Kendaraan tidak ditemukan','result' => []]);
    }
    public function myVehicle($idCustomer){
        try{
            $data = VehicleCustomer::where('customer_id', $idCustomer)->with('vehicle')->get();
        }catch(\Exception $e){
            $data = [];
        }
        return response()->json($data);
    }
    public function vehicleCustomer(Request $request){
        $this->validateWith([
            'licensePlate' => 'required',
            'vehicle_id' => 'required',
            'people_id' => 'required',
        ]);
        $item = new VehicleCustomer;
        $item->licensePlate = $request->licensePlate;
        $item->vehicle_id = $request->vehicle_id;
        $item->people_id = $request->people_id;
        $item->save();
        return response()->json(['status'=>'1','msg'=>'Kendaraan pelanggan dengan nomor plat '.$item->licensePlate.' berhasil dibuat','result' => $item]);
    }
    public function vehicleSparepart(Request $request){
        $this->validateWith([
            'vehicle_id' => 'required',
            'sparepart_code' => 'required',
        ]);
        $item = new VehicleSparepart;
        $item->sparepart_code = $request->sparepart_code;
        $item->vehicle_id = $request->vehicle_id;
        $item->save();
        return response()->json(['status'=>'1','msg'=>'Sparepart berhasil didaftarkan','result' => $item]);
    }
}
