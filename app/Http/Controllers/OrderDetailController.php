<?php

namespace App\Http\Controllers;

use App\OrderDetail;
use App\Order;
use Illuminate\Http\Request;
use App\SalesSupplier;
use PDF;
use App\Person;
class OrderDetailController extends Controller
{
    public function showDetailOrder($orderId){
        return OrderDetail::where('order_id',$orderId)->get();
    }
    public function cetakPemesanan(Request $request){
        $this->validateWith([
            'supplier_id' => 'required',
            'branch_id' => 'required',
            'name' => "required",
            'phoneNumber' => "required",
            'address' => "required",
            'city' => "required"
        ]);
        $personController = new PersonController();
        if(!$person = Person::where('phoneNumber',$request->phoneNumber)->first()){
           $personController->store($request,'sales');
           $person = Person::find($personController->getId());
        }
        if($order = Order::where([['supplier_id',$request->supplier_id],['branch_id',$request->branch_id],['status',0]])->with('supplier')->first()){
            if(!$sales = SalesSupplier::where([['sales_id',$person->id],['supplier_id',$request->supplier_id]])->first()){
                $sales = new SalesSupplier();
                $sales->sales_id = $person->id;
                $sales->supplier_id = $request->supplier_id;
                $sales->save();
            }
            $detail = OrderDetail::where('order_id', $order->id)->with('sparepart')->get();
            $supplier = Person::find($request->supplier_id);
            view()->share(['sales'=>$person,'order'=>$order,'detail'=>$detail, 'supplier'=>$supplier]);
            $pdf = PDF::loadView('exports.pemesanan');
            $pdf->setPaper([0, 0, 550, 900])->save(public_path('/files/pemesanan').'/'.$order->id.'-'.$person->phoneNumber.'.pdf');
            $order->status = 1;
            $order->sales_id = $person->id;
            $order->save();
            return $pdf->download($order->id.'-'.$person->phoneNumber.'.pdf');
        }
        return 0;
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

        return response()->json(['sparepart'=>$od]);
    }
}
