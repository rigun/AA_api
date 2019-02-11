<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return Order::all();
    }

    public function store(Request $request){
        $this->validateWith([
            'people_id' => 'required',
            'status' => 'required',
        ]);
        $item = new Order();
        $item->people_id = $request->people_id;
        $item->status = $request->status;
        $item->save();
        return response()->json(['status'=>'1','msg'=>'Order berhasil dibuat','result' => $item]);
    }
    public function update(Request $request,$id){
        $this->validateWith([
            'people_id' => 'required',
            'status' => 'required',
        ]);
        if($item = Order::where('id',$id)->first()){
            $item->people_id = $request->people_id;
            $item->status = $request->status;
            $item->save();    
            return response()->json(['status'=>'1','msg'=>'Order berhasil diubah menjadi ','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Order tidak ditemukan','result' => []]);
    }
    public function show($id){
        if($item = Order::where('id',$id)->first()){
            return response()->json(['status'=>'1','msg'=>'Order berhasil ditemukan','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Order tidak ditemukan','result' => []]);
    }
    public function destroy($id){
        if($item = Order::where('id',$id)->first()){
            $item->delete();
            return response()->json(['status'=>'1','msg'=>'Order berhasil dihapus','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Order tidak ditemukan','result' => []]);
    }
}
