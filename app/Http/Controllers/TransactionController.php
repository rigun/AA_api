<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\TransactiondetailSparepart;
use App\TransactiondetailService;
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
        $userController = new UserController();
        $role = $userController->getMyRole();
        try{
            $id = $userController->getPeopleId();
        }catch(\Exception $e){
            $id = 0;
        }
        if ($role == 'cs') {
            return Transaction::where([['status',3],['cs_id',$id]])->with(['customer','cashier','cs','branch','detail'])->orderBy('created_at','desc')->get();
        } else if ($role == 'kasir') {
            return Transaction::where([['status',3],['cashier_id',$id]])->with(['customer','cashier','cs','branch','detail'])->orderBy('created_at','desc')->get();
        } else {
            return Transaction::where('status',3)->with(['customer','cashier','cs','branch','detail'])->orderBy('created_at','desc')->get();
        }
    }
    public function historyByUser(){

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
        if($request->jenisTransaksi != 'SS'){
            $detail = $transaction->detail()->get();
            foreach($detail as $dt){
                if($request->jenisTransaksi == 'SV'){
                    $dtsp = TransactiondetailSparepart::where('trasanctiondetail_id',$dt->id)->get();
                    foreach($dtsp as $dsp){
                        $dsp->delete();
                    }
                }else{
                    $dtsv = TransactiondetailService::where('trasanctiondetail_id',$dt->id)->get();
                    foreach($dtsv as $dsv){
                        $dsv->delete();
                    }
                }
                $dt->montir_id = null;
                $dt->save();
            }
        }

        return response()->json(['status'=>'1','msg'=>'Data berhasil dimasukkan','result' => $transaction->first()]);                
    }

}
