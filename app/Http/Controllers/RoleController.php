<?php

namespace App\Http\Controllers;

use App\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return Role::all();
    }

    public function store(Request $request){
        $this->validateWith([
            'name' => 'required',
        ]);
        $item = new Role();
        $item->name = $request->name;
        $item->save();
        return response()->json(['status'=>'1','msg'=>'Role '.$item->name.' berhasil dibuat','result' => $item]);
    }
    public function update(Request $request,$id){
        $this->validateWith([
            'name' => 'required',
        ]);
        if($item = Role::where('id',$id)->first()){
            $item->name = $request->name;
            $item->save();    
            return response()->json(['status'=>'1','msg'=>'Role berhasil diubah menjadi '.$item->name,'result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Role tidak ditemukan','result' => []]);
    }
    public function show($id){
        if($item = Role::where('id',$id)->first()){
            return response()->json(['status'=>'1','msg'=>'Role berhasil ditemukan','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Role tidak ditemukan','result' => []]);
    }
    public function destroy($id){
        if($item = Role::where('id',$id)->first()){
            $item->delete();
            return response()->json(['status'=>'1','msg'=>'Role berhasil dihapus','result' => $item]);
        }
        return response()->json(['status'=>'0','msg'=>'Role tidak ditemukan','result' => []]);
    }
}
