<?php

namespace App\Http\Controllers;

use App\Person;
use Illuminate\Http\Request;
use App\Role;
use App\User;
use App\Http\Controllers\UserController;

class PersonController extends Controller
{
    public $id;

    public function cekPhoneNumber($phoneNumber){
        if(!$person = Person::where('phoneNumber',$phoneNumber)->first()){
            $person = 0;
        }
        return $person;
    }
    public function getId(){
        return $this->id;
    }   
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
    public function totalData($role){
        if($r = Role::where('name',$role)->first()){
            return Person::where('role_id',$r->id)->count();
        }
        return -1;
    }
    public function searchById($id){
        if($person = Person::where('id',$id)->first()){
            return response()->json(['status'=>'1','msg'=>'Data berhasil ditemukan','result' => $person]);
        }
        return response()->json(['status'=>'0','msg'=>'Data tidak ditemukan','result' => []]);
    }
    public function store(Request $request,$role){
        $rules = [
            'name' => 'required',
            'phoneNumber' => 'required|max:16|unique:people',
            'address' => 'required',
            'city' => 'required',
        ];
    
        $customMessages = [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute sudah terdaftar'
        ];

        $attributes = [
            'phoneNumber' => 'Nomor Telepon',
        ];
        $this->validate($request, $rules, $customMessages, $attributes);

      
        if($r = Role::where('name',$role)->first()){
            $person = new Person();
            $person->name = $request->name;
            $person->phoneNumber = $request->phoneNumber;
            $person->address = $request->address;
            $person->city = $request->city;
            $person->role_id = $r->id;
            $person->save();
            $this->id = $person->id;
            if($r->name == 'cs' || $r->name == 'kasir' || $r->name == 'owner'){
                $userController = new UserController();
                $userController->storeByEmployee($request,$person->id);    
            }
            return response()->json(['status'=>'1','msg'=>'Data berhasil ditambahkan','result' => $person]);
        };
        return response()->json(['status'=>'0','msg'=>'Role tidak ditemukan','result' => []]);

    }
    public function update(Request $request,$id){
        $rules = [
            'name' => 'required',
            'phoneNumber' => 'required|max:16|unique:people,phoneNumber,'.$id,
            'address' => 'required',
            'city' => 'required',
        ];
    
        $customMessages = [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute sudah terdaftar'
        ];

        $attributes = [
            'phoneNumber' => 'Nomor Telepon',
        ];
        $this->validate($request, $rules, $customMessages, $attributes);
        $r = Role::where('name',$request->role)->first();
        if($person = Person::where('id',$id)->first()){
            $person->name = $request->name;
            $person->phoneNumber = $request->phoneNumber;
            $person->address = $request->address;
            $person->city = $request->city;
            $person->role_id = $r->id;
            $userController = new UserController();
            if(!$user = User::where('people_id',$person->id)->first()){
                if($r->name == 'cs' || $r->name == 'kasir' || $r->name == 'owner'){
                    $userController->storeByEmployee($request,$person->id);
                }
            }else{
                if($r->name == 'montir'){
                    $userController->deleteByEmployee($user->id);
                }else{
                    $userController->updateByEmployee($request,$user->id);    
                }
            }
                
            $person->save();
            
            return response()->json(['status'=>'1','msg'=>'Data berhasil diperbaharui','result' => $person]);
        };
        return response()->json(['status'=>'0','msg'=>'Data tidak ditemukan','result' => []]);
    }
    public function destroy($id)
    {
        if($person = Person::where('id',$id)->first()){
            $userController = new UserController();
            if($user = User::where('people_id',$person->id)->first()){
                $userController->deleteByEmployee($user->id);
            }
            $person->delete();
            return response()->json(['status'=>'1','msg'=>'Data berhasil dihapus']);
        };
        return response()->json(['status'=>'0','msg'=>'Data tidak ditemukan']);
    }
}
