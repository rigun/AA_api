<?php

namespace App\Http\Controllers;

use App\Sparepart;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    public function index()
    {
        return Sparepart::all();
    }

    public function store(Request $request){
        $this->validateWith([
            'code' => 'required|max:16|unique:spareparts',
            'name' => 'required',
            'buy' => 'required',
            'sell' => 'required',
            'merk' => 'required',
            'type' => 'required',
            'position' => 'required',
            'stock' => 'required',
        ]);
        $item = new Sparepart();
        $item->code = $request->code;
        $item->name = $request->name;
        $item->buy = $request->buy;
        $item->sell = $request->sell;
        $item->merk = $request->merk;
        $item->type = $request->type;
        $item->position = $request->position;
        $item->stock = $request->stock;
        $item->save();
        return response()->json(['status'=>'1','msg'=>'Sparepart '.$item->name.' berhasil dibuat','result' => $item]);
    }
    public function update(Request $request,$code){
        $this->validateWith([
            'name' => 'required',
            'buy' => 'required',
            'sell' => 'required',
            'merk' => 'required',
            'type' => 'required',
            'position' => 'required',
            'stock' => 'required',
        ]);
        if($item = Sparepart::where('code',$code)->first()){
            $item->name = $request->name;
            $item->buy = $request->buy;
            $item->sell = $request->sell;
            $item->merk = $request->merk;
            $item->type = $request->type;
            $item->position = $request->position;
            $item->stock = $request->stock;
            $item->save();    
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
    public function destroy($code){
        if($item = Sparepart::where('code',$code)->first()){
            $item->delete();
            return response()->json(['status'=>'1','msg'=>'Sparepart berhasil dihapus','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Sparepart tidak ditemukan','result' => []]);
    }
}
