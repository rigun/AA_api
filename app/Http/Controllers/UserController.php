<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Indonesia;
use JWTAuth;
use Hash;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function getPeopleId(){
        return JWTAuth::parseToken()->authenticate()->people_id;
    }
    public function getMyRole(){
        return JWTAuth::parseToken()->authenticate()->role()->first()->name;
    }
    public function dashboard(){
        $vehicle = new VehicleController();
        $person = new PersonController();
        $employee = new EmployeeController();

        $totalVehicle = $vehicle->totalData();
        $totalSupplier = $person->totalData('supplier');
        $totalEmployee = $employee->totalData();
        $totalCustomer = $person->totalData('konsumen');

        return response()->json(['kendaraan' => $totalVehicle, 'supplier' => $totalSupplier, 'pegawai' => $totalEmployee, 'konsumen' => $totalCustomer]);
    }
    public function updatePassword(Request $request, $id)
    {
        $this->validateWith([
            'password_lama' => 'required',
            'password_baru' => 'required',

          ]);
          $user = User::findOrFail($id);
          if(Hash::check($request->password_lama, $user->password)){
            $user->password = Hash::make($request->password_baru);    
            $json=['status' => 'success','msg'=>'Password berhasil diubah'];
          }else{
            $json=['status' => 'failed','msg'=>'Password yang anda masukkan salah, silahkan coba lagi'];
          }
          $user->save();
          
          return response()->json($json);
    }
    public function index()
    {
        return User::all();
    }
    public function storeByEmployee(Request $request,$id)
    {
        $rules = [
            'email' => 'required|unique:users',
            'password' => 'required',
        ];
    
        $customMessages = [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute sudah terdaftar'
        ];

        $attributes = [
            'email' => 'Email',
        ];
        
        $this->validate($request, $rules, $customMessages,$attributes);

        $user = new User();
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->people_id = $id;
        $user->save();
        return 'Success';
    }
    public function updateByEmployee(Request $request,$id)
    {
        $rules = [
            'email' => 'required|unique:users,email,'.$id,
            'password' => 'required',
        ];
    
        $customMessages = [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute sudah terdaftar'
        ];

        $attributes = [
            'email' => 'Email',
        ];
        
        $this->validate($request, $rules, $customMessages,$attributes);

        $user = User::where('id',$id)->first();
        $user->email = $request->email;
        if($request->changePassword){
            $user->password = bcrypt($request->password);
        }
        $user->save();
        return 'Success';
    }
    public function deleteByEmployee($id)
    {
        $user = User::where('id',$id)->first();
        $user->delete();
        return 'Success';
    }

    public function city()
    {
        return Indonesia::allCities();
    }
    public function getAuthenticatedUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                    return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        $userdata = User::with('role')->where('id',$user->id)->first();
        return response()->json(compact('userdata'));
    }
}
