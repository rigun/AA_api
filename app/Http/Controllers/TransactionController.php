<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\Branch;
use App\Person;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function destroy($id){
        $transaction = Transaction::find($id);
        $transaction->delete();
        return 'Berhasil';
    }
    public function index(){
        return Transaction::where('status',3)->with(['customer','cashier','cs','branch','detail'])->orderBy('created_at','desc')->get();
    }
    public function showByBranch($branch_id){
        return Transaction::where('branch_id',$branch_id)->with('customer')->orderBy('created_at','desc')->get();
    }
    public function updateStatus(Request $request, $id){
        
        $this->validateWith([
            'status' => 'required'
        ]);
        $t = Transaction::find($id);
        if($request->status == 1){
            $t->status = 2;
        }else{
            $t->status = 1;
        }
        $t->save();
        return 'Berhasil';
    }

    public function store(Request $request){
        $this->validateWith([
            'branch' => 'required',
            'role' => 'required',
            'jenisTransaksi' => 'required'
            ]);

        if($b = Branch::where('id',$request->branch)->first()){
            if(!$person = Person::where('phoneNumber',$request->phoneNumber)->first()){
                $personController = new PersonController();
                $personController->store($request,$request->role);
                $person = Person::find($personController->getId());
            }

            $transaction = new Transaction();
            $transaction->transactionNumber = $request->jenisTransaksi.'-'.date("dmy");
            $transaction->branch_id = $request->branch;
            $userController = new UserController();
            $transaction->cs_id = $userController->getPeopleId();
            $transaction->customer_id = $person->id;
            $transaction->save();
            return response()->json(['status'=>'1','msg'=>'Data berhasil dimasukkan','result' => $transaction->first()]);                
        };
        return response()->json(['status'=>'0','msg'=>'Cabang tidak ditemukan','result' => []]);
    }
    public function update(Request $request, $id){
        $this->validateWith([
            'jenisTransaksi' => 'required'
        ]);

        $personController = new PersonController();
        $personController->update($request,$request->customerId);
        
        $transaction = Transaction::find($id);
        $transaction->transactionNumber = $request->jenisTransaksi.'-'.date("dmy");
        $transaction->save();
        return response()->json(['status'=>'1','msg'=>'Data berhasil dimasukkan','result' => $transaction->first()]);                
    }

}
