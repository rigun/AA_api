<?php

namespace App\Http\Controllers;

use App\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
  
    public function index()
    {
        return Branch::withCount('employee','transaction','spareparts')->get();
    }
    public function employeeByBranch($id){
        return Branch::where('id',$id)->with('employee')->first();
    }
    public function store(Request $request){
        $this->validateWith([
            'name' => 'required',
        ]);
        $item = new Branch();
        $item->name = $request->name;
        $item->save();
        return response()->json(['status'=>'1','msg'=>'Cabang '.$item->name.' berhasil dibuat','result' => $item]);
    }
    public function update(Request $request,$id){
        $this->validateWith([
            'name' => 'required',
        ]);
        if($item = Branch::where('id',$id)->first()){
            $item->name = $request->name;
            $item->save();    
            return response()->json(['status'=>'1','msg'=>'Cabang berhasil diubah menjadi '.$item->name,'result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Cabang tidak ditemukan','result' => []]);
    }
    public function show($id){
        if($item = Branch::where('id',$id)->first()){
            return response()->json(['status'=>'1','msg'=>'Cabang berhasil ditemukan','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Cabang tidak ditemukan','result' => []]);
    }
    public function destroy($id){
        if($item = Branch::where('id',$id)->first()){
            $item->delete();
            return response()->json(['status'=>'1','msg'=>'Cabang berhasil dihapus','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Cabang tidak ditemukan','result' => []]);
    }
}
