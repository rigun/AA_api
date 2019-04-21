<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\Branch;
use App\Person;
use Illuminate\Http\Request;
use PDF;

class TransactionController extends Controller
{
    public function spkExport(Request $request, $id){
        $this->validateWith([
            'montir_id' => 'required',
        ]);
        $transaction = Transaction::where('id',$id)->with(['detailTransaction','customer'])->first();
        $transaction->detailTransaction()->first()->montir_id = $request->montir_id;
        $transaction->save();
        view()->share(['data' => $transaction]);
        $pdf = PDF::loadView('exports.spk');
        $pdf->setPaper([0, 0, 450, 735])->save(public_path('/files/spk').'/'.$transaction->transactionNumber.'-'.$transaction->id.'.pdf');
        return $pdf->download($transaction->transactionNumber.'-'.$transaction->id.'.pdf');
    }
    public function destroy($id){
        $transaction = Transaction::find($id);
        $transaction->delete();
        return 'Berhasil';
    }
    public function showByBranch($branch_id){
        return Transaction::where('branch_id',$branch_id)->with('customer')->orderBy('created_at','desc')->get();
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

}
