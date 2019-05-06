<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function showByBranch($branchId)
    {
        return Order::where('branch_id',$branchId)->with(['supplier','detail','sales'])->get();
    }
    public function show($orderId)
    {
        return Order::where('id',$orderId)->with(['supplier','detail','sales'])->first();
    }
    public function destroy($supplierId,$branchId){
        if($item = Order::where([['supplier_id',$supplierId],['branch_id',$branchId],['status',0]])->first()){
            $item->delete();
            return response()->json(['status'=>'1','msg'=>'Order berhasil dihapus','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Order tidak ditemukan','result' => []]);
    }


}
