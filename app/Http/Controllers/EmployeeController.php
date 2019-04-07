<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Branch;
use App\Person;
use App\Http\Controllers\PersonController;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    
    public function index()
    {
        return Employee::with(['detail','branch'])->orderBy('created_at','desc')->get();
    }
    public function showByBranch($branch_id)
    {
        return Employee::where('branch_id',$branch_id)->with(['detail'])->orderBy('created_at','desc')->get();
    }
    public function show($id)
    {
        return Employee::where('id',$id)->with(['detail','branch'])->first();
    }
    public function store(Request $request){
        $this->validateWith([
            'salary' => 'required',
            'branch' => 'required',
            'role' => 'required'
            ]);
        if($b = Branch::where('id',$request->branch)->first()){

            $personController = new PersonController();
            $personController->store($request,$request->role);
            if($person = Person::where('phoneNumber',$request->phoneNumber)->first()){
                $employee = new Employee();
                $employee->salary = $request->salary;
                $employee->people_id = $person->id;
                $employee->branch_id = $b->id;
                $employee->save();
                return response()->json(['status'=>'1','msg'=>'Data berhasil dimasukkan','result' => $employee->detail()->first()]);                
            }else{
                return response()->json(['status'=>'0','msg'=>'Data Gagal dimasukkan','result' => []]);                
            }

        };
        return response()->json(['status'=>'0','msg'=>'Cabang tidak ditemukan','result' => []]);

    }
    public function update(Request $request,$id){
        $this->validateWith([
            'salary' => 'required',
        ]);

        if($employee = Employee::where('people_id',$id)->first()){
            $personController = new PersonController();
            $personController->update($request,$id);
            $employee->salary = $request->salary;
            $employee->save();
            return response()->json(['status'=>'1','msg'=>'Data berhasil dimasukkan','result' => $employee->detail()->first()]);                
        }else{
            return response()->json(['status'=>'0','msg'=>'Data Gagal dimasukkan','result' => []]);                
        }


    }
    public function destroy($id)
    {
        if($employee = Employee::where('id',$id)->first()){
            $personController = new PersonController();
            $personController->destroy($employee->people_id);
            $employee->delete();
            return response()->json(['status'=>'1','msg'=>'Data berhasil dihapus']);                
        }else{
            return response()->json(['status'=>'0','msg'=>'Data berhasil dihapus']);                
        }
    }
}
