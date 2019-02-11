<?php

namespace App\Http\Controllers;

use App\Person;
use Illuminate\Http\Request;
use App\Role;
class PersonController extends Controller
{
    
    public function index()
    {
        return Person::all();
    }

    public function show($role){
        if($r = Role::where('name',$role)->first()){
            return response()->json(['status'=>'1','msg'=>'Data berhasil ditemukan','result' => Person::where('role_id',$r->id)->get()]);
        }
        return response()->json(['status'=>'0','msg'=>'Role tidak ditemukan','result' => []]);

    }
    public function searchById($id){
        if($person = Person::where('id',$id)->first()){
            return response()->json(['status'=>'1','msg'=>'Data berhasil ditemukan','result' => $person]);
        }
        return response()->json(['status'=>'0','msg'=>'Data tidak ditemukan','result' => []]);
    }
    public function store(Request $request,$role){
        $this->validateWith([
            'name' => 'required',
            'phoneNumber' => 'required|max:16|unique:people',
            'address' => 'required',
            'city' => 'required',
          ]);
        if($r = Role::where('name',$role)->first()){
            $person = new Person();
            $person->name = $request->name;
            $person->phoneNumber = $request->phoneNumber;
            $person->address = $request->address;
            $person->city = $request->city;
            $person->role_id = $r->id;
            $person->save();
            return response()->json(['status'=>'1','msg'=>'Data berhasil ditambahkan','result' => $person]);
        };
        return response()->json(['status'=>'0','msg'=>'Role tidak ditemukan','result' => []]);

    }
    public function update(Request $request,$id){
        $this->validateWith([
            'name' => 'required',
            'phoneNumber' => 'required|max:16|unique:people,phoneNumber,'.$id,
            'address' => 'required',
            'city' => 'required',
          ]);
        if($person = Person::where('id',$id)->first()){
            $person->name = $request->name;
            $person->phoneNumber = $request->phoneNumber;
            $person->address = $request->address;
            $person->city = $request->city;
            $person->save();
            return response()->json(['status'=>'1','msg'=>'Data berhasil diperbaharui','result' => $person]);
        };
        return response()->json(['status'=>'0','msg'=>'Data tidak ditemukan','result' => []]);
    }
    public function destroy($id)
    {
        if($person = Person::where('id',$id)->first()){
            $person->delete();
            return response()->json(['status'=>'1','msg'=>'Data berhasil dihapus']);
        };
        return response()->json(['status'=>'0','msg'=>'Data tidak ditemukan']);
    }
}
