<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Exception;
class AuthController extends Controller
{
    private $branchId;
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function register(Request $request)
    {
      $this->validateWith([
        'name' => 'required',
        'email' => 'required',
        'password' => 'required',
      ]);
//user
        $user = new User();
        $user->name = $request->name; 
        $user->email = $request->email;
        $user->password = bcrypt($request->password); 
        $user->save();

      $user->syncRoles(explode(',', 'participant'));

     return $this->login($request);
    }

    public function login(Request $request)
    {
      $credentials = $request->only(['email', 'password']);

      if (!$token = auth()->attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
      }
      try{
        $this->branchId = auth()->user()->employee()->first()->branch_id;
      }catch(Exception $e){
        $this->branchId = -1;
      }
      return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
      
      return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'role' => auth()->user()->role()->first()->role->name,
        'status' => auth()->user()->status,
        'id' => auth()->user()->id,
        'branch_id' => $this->branchId
      ]);
    }
    public function me()
    {
        return response()->json(auth()->user());
    }
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

}
