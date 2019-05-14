<?php

namespace App\Http\Controllers;

use App\Order;
use App\SparepartBranch;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function showByBranch($branchId)
    {
        return Order::where('branch_id',$branchId)->with(['supplier','detail','sales'])->orderBy('status','ASC')->get();
    }
    public function show($orderId)
    {
        $eventId = Order::where('id',$orderId)->first()->branch_id;
        return Order::where('id',$orderId)->with(['supplier','detail'=> function ($query) use ($eventId) {
                $query->with(['data' => function ($query2) use ($eventId) {
                    $query2->where('branch_id',$eventId);
                }]);
            },'sales'])->first();
    }

    public function konfirmasiData($orderId)
    {
        $o = Order::find($orderId);
        $o->status = 2;
        $o->save();
        $detail = $o->detail()->get();
        foreach($detail as $dt){
            $s = SparepartBranch::where([['branch_id',$o->branch_id],['sparepart_code',$dt->sparepart_code]])->first();
            $s->stock = $s->stock + $dt->totalAccept;
            $s->save();
        }
        return response()->json($o);
    }
    public function destroy($supplierId,$branchId){
        if($item = Order::where([['supplier_id',$supplierId],['branch_id',$branchId],['status',0]])->first()){
            $item->delete();
            return response()->json(['status'=>'1','msg'=>'Order berhasil dihapus','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Order tidak ditemukan','result' => []]);
    }


}
