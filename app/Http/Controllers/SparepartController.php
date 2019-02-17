<?php

namespace App\Http\Controllers;

use App\Sparepart;
use App\VehicleSparepart;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    public function index()
    {
        return Sparepart::all();
    }
    public function isUniqueCode(Request $request){
        if(Sparepart::where('code',$request->code)->exists()){
            return 1;
        }else{
            return 0;
        }
    }
    public function store(Request $request){
        $this->validateWith([
            'code' => 'required|max:16|unique:spareparts',
            'name' => 'required',
            'merk' => 'required',
            'type' => 'required',
            'people_id' => 'required',
            'vehicles' => 'required'
        ]);
        $item = new Sparepart();
        $item->code = $request->code;
        $item->name = $request->name;
        $item->merk = $request->merk;
        $item->type = $request->type;
        $item->people_id = $request->people_id;
        $item->save();

        $item->vehicle()->sync($request->vehicles);
        return response()->json(['status'=>'1','msg'=>'Sparepart '.$item->name.' berhasil dibuat','result' => $item]);
    }
    public function update(Request $request,$code){
        $this->validateWith([
            'name' => 'required',
            'merk' => 'required',
            'type' => 'required',
            'people_id' => 'required',
            'vehicles' => 'required'
        ]);
        if($item = Sparepart::where('code',$code)->first()){
            $item->name = $request->name;
            $item->merk = $request->merk;
            $item->type = $request->type;
            $item->people_id = $request->people_id;
            $item->save();    

            $item->vehicle()->sync($request->vehicles);
            return response()->json(['status'=>'1','msg'=>'Sparepart berhasil diubah menjadi '.$item->name,'result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Sparepart tidak ditemukan','result' => []]);
    }
    public function show($code){
        if($item = Sparepart::where('code',$code)->first()){
            return response()->json(['status'=>'1','msg'=>'Sparepart berhasil ditemukan','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Sparepart tidak ditemukan','result' => []]);
    }
    public function showBySupplier($supplier_id){
        return Sparepart::where('people_id',$supplier_id)->with('vehicle')->get();
    }
    public function destroy($code){
        if($item = Sparepart::where('code',$code)->first()){
            if($vehicleSparepart = VehicleSparepart::where('sparepart_code',$item->code)->get()){
                foreach($vehicleSparepart as $vS){
                    $vS->delete();
                }
            }
            $item->delete();
            return response()->json(['status'=>'1','msg'=>'Sparepart berhasil dihapus','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Sparepart tidak ditemukan','result' => []]);
    }
}
