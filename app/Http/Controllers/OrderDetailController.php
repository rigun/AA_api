<?php

namespace App\Http\Controllers;

use App\OrderDetail;
use App\Order;
use Illuminate\Http\Request;

class OrderDetailController extends Controller
{
    public function showDetailOrder($orderId){
        return OrderDetail::where('order_id',$orderId)->get();
    }
    public function store(Request $request){
        $this->validateWith([
            'supplier_id' => 'required',
            'branch_id' => 'required',
            'sparepart_code' => 'required',
            'unit' => 'required',
            'total' => 'required'
        ]);
        if(!$order = Order::where([['supplier_id',$request->supplier_id],['branch_id',$request->branch_id],['status',0]])->first()){
            $order = new Order();
            $order->supplier_id = $request->supplier_id;
            $order->status = 0;
            $order->branch_id = $request->branch_id;
            $order->save();
        }

        if(!$od = OrderDetail::where([['order_id',$order->id],['sparepart_code',$request->sparepart_code]])->first()){
            $od = new OrderDetail();
            $od->order_id = $order->id;
        }
        $od->sparepart_code = $request->sparepart_code;
        $od->unit = $request->unit;
        $od->total = $request->total;
        $od->save();

        return 'Berhasil';
    }
}
