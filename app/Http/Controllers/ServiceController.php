<?php

namespace App\Http\Controllers;

use App\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return Service::all();
    }

    public function store(Request $request){
        $this->validateWith([
            'name' => 'required',
            'price' => 'required',
        ]);
        $item = new Service();
        $item->name = $request->name;
        $item->price = $request->price;
        $item->save();
        return response()->json(['status'=>'1','msg'=>'Service '.$item->name.' berhasil dibuat','result' => $item]);
    }
    public function update(Request $request,$id){
        $this->validateWith([
            'name' => 'required',
            'price' => 'required',
        ]);
        if($item = Service::where('id',$id)->first()){
            $item->name = $request->name;
            $item->price = $request->price;
            $item->save();    
            return response()->json(['status'=>'1','msg'=>'Service berhasil diubah menjadi '.$item->name,'result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Service tidak ditemukan','result' => []]);
    }
    public function updateStatus(Request $request,$id){
        $this->validateWith([
            'status' => 'required',
        ]);
        if($item = Service::where('id',$id)->first()){
            $item->status = $request->status;
            $item->save();    
            return response()->json(['status'=>'1','msg'=>'Status service berhasil diubah','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Service tidak ditemukan','result' => []]);
    }
    public function show($id){
        if($item = Service::where('id',$id)->first()){
            return response()->json(['status'=>'1','msg'=>'Service berhasil ditemukan','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Service tidak ditemukan','result' => []]);
    }
    public function destroy($id){
        if($item = Service::where('id',$id)->first()){
            $item->delete();
            return response()->json(['status'=>'1','msg'=>'Service berhasil dihapus','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Service tidak ditemukan','result' => []]);
    }
}
