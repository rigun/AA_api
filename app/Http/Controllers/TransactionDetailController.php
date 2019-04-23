<?php

namespace App\Http\Controllers;

use App\TransactionDetail;
use App\Transaction;
use App\VehicleCustomer;
use App\Person;
use App\TransactiondetailSparepart;
use App\TransactiondetailService;
use Illuminate\Http\Request;
use PDF;

class TransactionDetailController extends Controller
{
    public function mytransaction($idTransaction){
        $transaction = Transaction::where('id',$idTransaction)->with(['customer','cs'])->first();
        
        $detailTransaction = TransactionDetail::where('transaction_id',$transaction->id)->with(['vehicleCustomer','montir'])->get();
        $i = 0;
        $sparepart = [];
        $service = [];
        $sparepartController = new SparepartController();
        foreach($detailTransaction as $dt){
            $ts = TransactiondetailSparepart::where('trasanctiondetail_id',$dt->id)->with('sparepart')->get();
            foreach($ts as $t){
                $sparepart[$i]['data'] = $t;
                $sparepart[$i]['position'] = $sparepartController->getPosition($t->sparepart_code,$transaction->branch_id);
                $i++;
            }
            $sv = TransactiondetailService::where('trasanctiondetail_id',$dt->id)->with('service')->get(); 
            foreach($sv as $s){
                $service[]= $s;
            }
        }
        return response()->json(['transaction'=>$transaction,'detailTransaction'=>$detailTransaction,'sparepart'=>$sparepart,'service'=>$service]);
    }

    public function notaLunasExport(Request $request, $idTransaction){
        $this->validateWith([
            'diskon' => 'required',
            'totalServices' => 'required',
            'totalSpareparts' => 'required',
            'payment' => 'required'
        ]);
        $t = Transaction::where('id',$idTransaction)->first();
        $t->diskon = $request->diskon;
        $t->totalServices = $request->totalServices;
        $t->totalSpareparts = $request->totalSpareparts;
        $t->payment = $request->payment;
        $t->totalCost = $request->totalServices + $request->totalSpareparts;
        $userController = new UserController();
        $t->cashier_id = $userController->getPeopleId();
        $t->status = 3;
        $t->save();
        $transaction= Transaction::where('id',$idTransaction)->with(['customer','cs','cashier'])->first();

        $detailTransaction = TransactionDetail::where('transaction_id',$transaction->id)->with(['vehicleCustomer','montir'])->get();
        $i = 0;
        $sparepart = [];
        $service = [];
        $customerVehicle = [];
        $sparepartController = new SparepartController();
        foreach($detailTransaction as $dt){
            $ts = TransactiondetailSparepart::where('trasanctiondetail_id',$dt->id)->with('sparepart')->get();
            foreach($ts as $t){
                $sparepart[$i]['data'] = $t;
                $sparepart[$i]['position'] = $sparepartController->getPosition($t->sparepart_code,$transaction->branch_id);
                $i++;
                $sparepartController = new SparepartController();
                $sparepartController->decreaseStock($t->sparepart_code,$t->total,$transaction->branch_id);
            }
            $sv = TransactiondetailService::where('trasanctiondetail_id',$dt->id)->with('service')->get(); 
            foreach($sv as $s){
                $service[]= $s;
            }

            $customerVehicle[] = $dt->vehicleCustomer()->first();
        }
        view()->share(['transaction'=>$transaction,'detailTransaction'=>$detailTransaction,'sparepart'=>$sparepart,'service'=>$service,'customerVehicle'=> $customerVehicle]);
        $pdf = PDF::loadView('exports.notalunas');
        $pdf->setPaper([0, 0, 550, 900])->save(public_path('/files/notalunas').'/'.$transaction->transactionNumber.'-'.$transaction->id.'.pdf');
        return $pdf->download($transaction->transactionNumber.'-'.$transaction->id.'.pdf');
    }

    public function spkExport(Request $request, $idTransaction, $idDetailTransaction){
        $this->validateWith([
            'montir_id' => 'required',
        ]);
        $transaction = Transaction::where('id',$idTransaction)->with(['customer','cs'])->first();
        $transaction->status = 1;
        $transaction->save();
        
        $dt = TransactionDetail::where('id',$idDetailTransaction)->first();
        $dt->montir_id = $request->montir_id;
        $dt->save();

        $detailTransaction = TransactionDetail::where('id',$idDetailTransaction)->with('montir')->first();
        $customerVehicle = $detailTransaction->vehicleCustomer()->first();
        $ts = TransactiondetailSparepart::where('trasanctiondetail_id',$idDetailTransaction)->with('sparepart')->get();
        $sparepartController = new SparepartController();
        $sparepart = [];
        foreach($ts as $key => $t){
            $sparepart[$key]['data'] = $t;
            $sparepart[$key]['position'] = $sparepartController->getPosition($t->sparepart_code,$transaction->branch_id);
        }
        $service = TransactiondetailService::where('trasanctiondetail_id',$idDetailTransaction)->with('service')->get();
        
        view()->share(['transaction'=>$transaction,'detailTransaction'=>$detailTransaction,'sparepart'=>$sparepart,'service'=>$service,'customerVehicle'=>$customerVehicle]);
        $pdf = PDF::loadView('exports.spk');
        $pdf->setPaper([0, 0, 450, 735])->save(public_path('/files/spk').'/'.$transaction->transactionNumber.'-'.$transaction->id.'.pdf');
        return $pdf->download($transaction->transactionNumber.'-'.$transaction->id.'.pdf');
    }
    public function dataExport($idTransaction,$idDetailTransaction){

        $transaction = Transaction::where('id',$idTransaction)->with(['customer','cs'])->first();
        $detailTransaction = TransactionDetail::where('id',$idDetailTransaction)->with(['vehicleCustomer','montir'])->first();
        $ts = TransactiondetailSparepart::where('trasanctiondetail_id',$idDetailTransaction)->with('sparepart')->get();
        $sparepartController = new SparepartController();
        $try=[];
        foreach($ts as $key => $t){
            $try[$key]['data'] = $t;
            $try[$key]['position'] = $sparepartController->getPosition($t->sparepart_code,$transaction->branch_id);
        }
        $service = TransactiondetailService::where('trasanctiondetail_id',$idDetailTransaction)->with('service')->get();

        return response()->json(['transaction'=>$transaction,'detail'=>$detailTransaction, 'sparepart'=>$try,'service'=>$service]);
    }
    public function getCustomer($id){
        $transaction = Transaction::find($id);
        return Person::find($transaction->customer_id);
    }
    public function showByTransaction($transactionId){
        return TransactionDetail::where('transaction_id',$transactionId)->with('vehicleCustomer')->get();
    }
    public function update(Request $request,$vehiclecustomerid){
        $this->validateWith([
            'licensePlate' => 'required',
            'vehicle_id' => 'required'
        ]);
        $vehicleCustomer = VehicleCustomer::find($vehiclecustomerid);
        $vehicleCustomer->licensePlate = $request->licensePlate;
        $vehicleCustomer->vehicle_id = $request->vehicle_id;
        $vehicleCustomer->save();

        return response()->json(['status'=>'1','msg'=>'Data berhasil dimasukkan','result' => $vehicleCustomer->first()]);                
    }
    public function destroy($transactionDetailId){
        $dt = TransactionDetail::find($transactionDetailId);
        $dt->delete();
        return 'Berhasil';
    }
    public function myvehicle($transactionDetailId){
        $dt = TransactionDetail::find($transactionDetailId);
        return $dt->vehicleCustomer()->first();
    }
    public function store(Request $request){
        $this->validateWith([
            'licensePlate' => 'required',
            'vehicle_id' => 'required',
            'transaction_id' => 'required'
        ]);
        $transaction = Transaction::find($request->transaction_id);
        $vehicleCustomer = new VehicleCustomer();
        $vehicleCustomer->licensePlate = $request->licensePlate;
        $vehicleCustomer->vehicle_id = $request->vehicle_id;
        $vehicleCustomer->customer_id = $transaction->customer_id;
        $vehicleCustomer->save();
        
        $detailTransaction = new TransactionDetail();
        $detailTransaction->transaction_id = $transaction->id;
        $detailTransaction->vehicleCustomer_id = $vehicleCustomer->id;
        $detailTransaction->montir_id = $request->montir_id;
        $detailTransaction->save();

        return response()->json(['status'=>'1','msg'=>'Data berhasil dimasukkan','result' => $transaction->first()]);                
    }
}
