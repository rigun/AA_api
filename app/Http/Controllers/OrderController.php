<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function showByBranch($branchId)
    {
        return Order::where('branch_id',$branchId)->with('supplier')->get();
    }
    public function destroy($id){
        if($item = Order::where('id',$id)->first()){
            $item->delete();
            return response()->json(['status'=>'1','msg'=>'Order berhasil dihapus','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Order tidak ditemukan','result' => []]);
    }


}
