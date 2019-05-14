<?php

namespace App\Http\Controllers;

use App\Sparepart;
use App\SparepartBranch;
use App\VehicleSparepart;
use App\Order;
use App\OrderDetail;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    private $photos_path;

    public function __construct()
    {
        $this->photos_path = public_path('/images/sparepart');

    }
    public function showSupplierOfBranch($branch_id){
        $sp = SparepartBranch::where('branch_id',$branch_id)->get();
        $supplier = [];
        foreach($sp as $s){
            $supplier[] = Sparepart::where('code',$s->sparepart_code)->first()->supplier()->first();
        }
        $array = array_values( array_unique( $supplier, SORT_REGULAR ) );
        $result = json_encode( $array );
        return $result;
    }
    public function decreaseStock($sparepart_code,$total,$branch_id){
        $sp = SparepartBranch::where([['sparepart_code',$sparepart_code],['branch_id',$branch_id]])->first();
        $sp->stock = $sp->stock - $total;
        $sp->save();
    }
    public function getPrice($code,$id){
        return SparepartBranch::where([['sparepart_code',$code],['branch_id',$id]])->first()->sell;
    }
    public function getPosition($code,$id){
        return SparepartBranch::where([['sparepart_code',$code],['branch_id',$id]])->first()->position;
    }
    public function getStock($code,$id){
        return SparepartBranch::where([['sparepart_code',$code],['branch_id',$id]])->first()->stock;
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
        $item->vehicle()->sync($request->vehicles);
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
            if($request->changeImg == 1){
                $image = $request->picture;  // your base64 encoded
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $name = sha1(date('YmdHis') . str_random(30));
                $save_name = $name .'.'.'png';
                \File::put($this->photos_path.'/'. $save_name, base64_decode($image));
                $item->picture = $save_name;
            }
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
    public function showByBranchCustomer($branchId,$idDetail){
        $detail = \App\TransactionDetail::where('id',$idDetail)->first()->vehicleCustomer()->first();
        $vehicle_id = $detail->vehicle->id;
        return SparepartBranch::where('branch_id',$branchId)->with('sparepart')->whereHas('sparepart',function($query) use($vehicle_id){
            $query->whereHas('vehicle',function($query2) use($vehicle_id) {
                    $query2->where('id',$vehicle_id);
                });
        })->get();
    }
    public function sparepartLanding(){
        return Sparepart::with('sparepartbranch')->get();
    }
    public function showByBranchSupplier($supplierId,$branchId){
        $sparepart = Sparepart::where('supplier_id',$supplierId)->get();
        $data = [];
        $tempData = [];
        foreach($sparepart as $s){
            if($sparepartBranch = SparepartBranch::where([['branch_id',$branchId],['sparepart_code',$s->code]])->with('sparepart')->first()){
                $data[] = $sparepartBranch;
            }
        }
        if($order = Order::where([['supplier_id',$supplierId],['branch_id',$branchId],['status',0]])->first()){
            foreach($data as $key => $d){
                $tempData[$key]['data'] = $d;
                if($od = OrderDetail::where([['order_id',$order->id],['sparepart_code',$d->sparepart_code]])->first()){
                    $tempData[$key]['total'] = $od->total;
                }else{
                    $tempData[$key]['total'] = 0;
                }
            }
        }else{
            foreach($data as $key => $d){
                $tempData[$key]['data'] = $d;
                $tempData[$key]['total'] = 0;
            }
        }
        return $tempData;
    }
    public function storeToBranch(Request $request){
        $this->validateWith([
            'sparepart_code' => 'required',
            'branch_id' => 'required',
            'position' => 'required',
            'limitstock' => 'required',
            'sell' => 'required',
            'buy' => 'required',
            'stock' => 'required'
        ]);
        $item = new SparepartBranch();
        $item->sparepart_code = $request->sparepart_code;
        $item->branch_id = $request->branch_id;
        $item->position = $request->position;
        $item->limitstock = $request->limitstock;
        $item->sell = $request->sell;
        $item->buy = $request->buy;
        $item->stock = $request->stock;
        $item->save();

        return response()->json(['status'=>'1','msg'=>'Sparepart '.$item->name.' berhasil dibuat','result' => $item]);
    }
    public function updateSpBranch(Request $request, $id){
        $this->validateWith([
            'sparepart_code' => 'required',
            'position' => 'required',
            'limitstock' => 'required',
            'sell' => 'required',
            'buy' => 'required',
            'stock' => 'required'
        ]);
        $item = SparepartBranch::where('id', $id)->first();
        $item->sparepart_code = $request->sparepart_code;
        $item->position = $request->position;
        $item->limitstock = $request->limitstock;
        $item->sell = $request->sell;
        $item->buy = $request->buy;
        $item->stock = $request->stock;
        $item->save();
            
        return response()->json(['status'=>'1','msg'=>'Sparepart '.$item->name.' berhasil dibuat','result' => $item]);
    }
    public function destroyByBranch($id){
        $item = SparepartBranch::where('id', $id)->first();
        $item->delete();
            
        return 'Data berhasil dihapus';
    }
}
