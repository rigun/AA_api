<?php

namespace App\Http\Controllers;

use App\Sparepart;
use App\SparepartBranch;
use App\VehicleSparepart;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    private $photos_path;

    public function __construct()
    {
        $this->photos_path = public_path('/images/sparepart');

    }
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
            'supplier_id' => 'required',
            'vehicles' => 'required',
            'picture' => 'required'
        ]);
        $item = new Sparepart();
        $item->code = $request->code;
        $item->name = $request->name;
        $item->merk = $request->merk;
        $item->type = $request->type;
        $item->supplier_id = $request->supplier_id;
        // image
        $image = $request->picture;  // your base64 encoded
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $name = sha1(date('YmdHis') . str_random(30));
        $save_name = $name .'.'.'png';
        \File::put($this->photos_path.'/'. $save_name, base64_decode($image));
        $item->picture = $save_name;
        // image

        $item->save();
        $arrayData  = (array) $request->vehicles; // related ids
        $item->vehicle()->sync($arrayData);
        return response()->json(['status'=>'1','msg'=>'Sparepart '.$item->name.' berhasil dibuat','result' => $item]);
    }
    public function update(Request $request,$code){
        $this->validateWith([
            'name' => 'required',
            'merk' => 'required',
            'type' => 'required',
            'supplier_id' => 'required',
            'vehicles' => 'required'
        ]);
        if($item = Sparepart::where('code',$code)->first()){
            $item->name = $request->name;
            $item->merk = $request->merk;
            $item->type = $request->type;
            $item->supplier_id = $request->supplier_id;
            $item->save();    

            $item->vehicle()->sync($request->vehicles[0]);
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
        return Sparepart::where('supplier_id',$supplier_id)->with('vehicle')->get();
    }
    public function destroy($code){
        if($item = Sparepart::where('code',$code)->first()){
            $item->vehicle()->detach();
            $item->delete();
            return response()->json(['status'=>'1','msg'=>'Sparepart berhasil dihapus','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Sparepart tidak ditemukan','result' => []]);
    }

    public function showByBranch($branchId){
        return SparepartBranch::where('branch_id',$branchId)->with('sparepart')->get();
    }
    public function storeToBranch(Request $request){
        $this->validateWith([
            'sparepart_code' => 'required',
            'branch_id' => 'required',
            'position' => 'required',
            'limitstock' => 'required'
        ]);
        $item = new SparepartBranch();
        $item->sparepart_code = $request->sparepart_code;
        $item->branch_id = $request->branch_id;
        $item->position = $request->position;
        $item->limitstock = $request->limitstock;
        $item->save();
            
        return response()->json(['status'=>'1','msg'=>'Sparepart '.$item->name.' berhasil dibuat','result' => $item]);
    }
}
