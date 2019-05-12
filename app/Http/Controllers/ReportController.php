<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\TransactionDetail;
use App\TransactiondetailService;
use App\TransactiondetailSparepart;
use App\Vehicle;
use App\Branch;
use DB;
use PDF;
use CpChart\Data;
use CpChart\Image;
use CpChart\Chart\Pie;
class ReportController extends Controller
{
  private $photos_path;
  private $monthsData;
  public function __construct()
  {
    $this->photos_path = public_path('/images/charts');
    $this->monthsData =  ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Juni', 'Juli', 'Agust', 'Sep', 'Okt', 'Nov', 'Des'];
  }
  public function sparepartOfYear(){
    $detail = $this->sparepartOfYearData();
    $pdf = PDF::loadView('reports.sparepartTerlaris',['detail'=>$detail]);
    $pdf->setPaper([0, 0, 550, 900])->save(public_path('/files/report/sparepartTerlaris').'/'.date('d-Y').'.pdf');
    return $pdf->download(date('d-Y').'.pdf');
  }
  public function sparepartOfYearData(){
    return DB::select('SELECT * FROM
                        (
                          SELECT 1 AS MONTH
                          UNION SELECT 2 AS MONTH
                          UNION SELECT 3 AS MONTH
                          UNION SELECT 4 AS MONTH
                          UNION SELECT 5 AS MONTH
                          UNION SELECT 6 AS MONTH
                          UNION SELECT 7 AS MONTH
                          UNION SELECT 8 AS MONTH
                          UNION SELECT 9 AS MONTH
                          UNION SELECT 10 AS MONTH
                          UNION SELECT 11 AS MONTH
                          UNION SELECT 12 AS MONTH
                        ) months LEFT JOIN
                        (SELECT * FROM 
                          (
                            SELECT sum(tds.total) maxTotal, sp.code, sp.name, sp.type, MONTH(t.created_at) createMonth
                            FROM transactions t 
                            join transaction_details td on td.transaction_id = t.id 
                            join transactiondetail_spareparts tds on tds.trasanctiondetail_id = td.id 
                            join spareparts sp on tds.sparepart_code = sp.code
                            WHERE YEAR(t.created_at) = YEAR(CURDATE()) AND t.status = 3
                            GROUP BY MONTH(t.created_at), tds.sparepart_code 
                            ORDER BY sum(tds.total) DESC
                          ) n GROUP BY n.createMonth
                        ) realCount ON months.MONTH = realCount.createMonth') ;
  }
    // LAPORAN PENDAPATAN BULANAN
    public function incomeOfMonth(){
      $datasets = $this->incomeOfMonthData();
      $pointY = [];
      $pointXSs = [];
      $pointXSp = [];
      $pointXT = [];
      foreach($datasets as $dt){
        if($dt->Service == null){
          $pointXSs[] = 0;
        }else{
          $pointXSs[] = $dt->Service;
        }
        if($dt->Spareparts == null){
          $pointXSp[] = 0;
        }else{
          $pointXSp[] = $dt->Spareparts;
        }
        if($dt->Total == null){
          $pointXT[] = 0;
        }else{
          $pointXT[] = $dt->Total - $dt->Diskon;
        }
        $pointY[] = $this->monthsData[$dt->MONTH - 1];
      }
      $data = new Data();
      $data->addPoints($pointXSs, "Service");
      $data->addPoints($pointXSp, "Spareparts");
      $data->addPoints($pointXT, "Total");
      $data->addPoints($pointY, "Months");
      $data->setSerieDescription("Months", "Months");
      $data->setAbscissa("Months");

      /* Create the Image object */
      $image = new Image(1000, 400, $data);
      $image->drawGradientArea(0,0,1000,400,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
      $image->drawGradientArea(0,0,1000,400,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
      $image->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>11));
      $image->setGraphArea(100,10,800,380);
      $image->drawScale(array("Mode"=>SCALE_MODE_START0, "CycleBackground"=>TRUE,"DrawSubTicks"=>TRUE,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10));
      $image->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
      $settings = array("Floating0Serie"=>"Floating 0","Draw0Line"=>TRUE,"Gradient"=>TRUE, "DisplayPos"=>LABEL_POS_INSIDE,"DisplayValues"=>TRUE,"DisplayR"=>255, "DisplayG"=>255,"DisplayB"=>255,"DisplayShadow"=>TRUE,"Surrounding"=>0);
      $image->drawBarChart($settings);
      $image->drawLegend(820,60,array("BoxSize"=>4,"R"=>173,"G"=>163,"B"=>83,"Surrounding"=>20,"Family"=>LEGEND_FAMILY_CIRCLE));
      
      // save File
      \File::put($this->photos_path."/pendapatanPerbulan/".date('d-Y').'.png', $image);
      $pdf = PDF::loadView('reports.pendapatanBulanan',['datasets'=>$datasets]);
      $pdf->setPaper([0, 0, 550, 900])->save(public_path('/files/report/pendapatanBulanan').'/'.date('d-Y').'.pdf');
      return $pdf->download(date('d-Y').'.pdf');
    }
    public function incomeOfMonthData(){
        return DB::select('SELECT * FROM
                            (
                              SELECT 1 AS MONTH
                              UNION SELECT 2 AS MONTH
                              UNION SELECT 3 AS MONTH
                              UNION SELECT 4 AS MONTH
                              UNION SELECT 5 AS MONTH
                              UNION SELECT 6 AS MONTH
                              UNION SELECT 7 AS MONTH
                              UNION SELECT 8 AS MONTH
                              UNION SELECT 9 AS MONTH
                              UNION SELECT 10 AS MONTH
                              UNION SELECT 11 AS MONTH
                              UNION SELECT 12 AS MONTH
                            ) months LEFT JOIN
                            (
                              SELECT sum(t.totalServices) Service, sum(t.totalSpareparts) Spareparts, sum(t.totalCost) Total, sum(t.diskon) Diskon, MONTH(t.created_at) createMonth
                              FROM transactions t 
                              WHERE YEAR(t.created_at) = YEAR(CURDATE()) AND t.status = 3
                              GROUP BY MONTH(t.created_at)
                            ) realCount ON months.MONTH = realCount.createMonth') ;
    }
    // Pendapatan Tahunan
    public function incomeOfYear(){
      $datasets = $this->incomeOfYearData();
      $cab = [];
      $temp = $datasets[0]['branch'];
      $year = [];
      foreach($datasets as $dt){
        $year[] = $dt['year'];
        foreach($dt['branch'] as $b){
          $cab[$b['branch']][] = $b['total'];
        }
      }
      $data = new Data();
      foreach($temp as $t){
        $data->addPoints($cab[$t['branch']], $t['branch']);
      }
      $data->addPoints($year, "Years");
      $data->setSerieDescription("Years", "Years");
      $data->setAbscissa("Years");

      /* Create the Image object */
      $image = new Image(1000, 400, $data);
      $image->drawGradientArea(0,0,1000,400,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
      $image->drawGradientArea(0,0,1000,400,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
      $image->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>11));
      $image->setGraphArea(100,10,800,380);
      $image->drawScale(array("Mode"=>SCALE_MODE_START0, "CycleBackground"=>TRUE,"DrawSubTicks"=>TRUE,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10));
      $image->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
      $settings = array("Floating0Serie"=>"Floating 0","Draw0Line"=>TRUE,"Gradient"=>TRUE, "DisplayPos"=>LABEL_POS_INSIDE,"DisplayValues"=>TRUE,"DisplayR"=>255, "DisplayG"=>255,"DisplayB"=>255,"DisplayShadow"=>TRUE,"Surrounding"=>0);
      $image->drawBarChart($settings);
      $image->drawLegend(820,60,array("BoxSize"=>4,"R"=>173,"G"=>163,"B"=>83,"Surrounding"=>20,"Family"=>LEGEND_FAMILY_CIRCLE));
      // save File
      \File::put($this->photos_path."/pendapatanTahunan/".date('d-Y').'.png', $image);
      $pdf = PDF::loadView('reports.pendapatanTahunan',['datasets'=>$datasets]);
      $pdf->setPaper([0, 0, 550, 900])->save(public_path('/files/report/pendapatanTahunan').'/'.date('d-Y').'.pdf');
      return $pdf->download(date('d-Y').'.pdf');
    }
    public function incomeOfYearData(){
      $branch = DB::select('SELECT sum(t.totalCost) total, b.name branch, Year(t.created_at) year
      FROM branches b 
      join transactions t on b.id = t.branch_id 
      where t.status = 3
      GROUP BY b.name
      ORDER BY Year(t.created_at) ASC'
      );
      $year = DB::select('SELECT DISTINCT Year(t.created_at) year FROM transactions t');
      $Tempbranch = [];
      foreach($branch as $key => $b){
        $Tempbranch[$key]['total'] = $b->total;
        $Tempbranch[$key]['branch'] = $b->branch;
        $Tempbranch[$key]['year'] = $b->year;
      }
      foreach($year as $key => $y){
        $temp[$key]['year'] = $y->year;
        $temp[$key]['branch'] = $Tempbranch;
      }


      foreach($temp as $key => $t){
        foreach($t['branch'] as $key2 => $b){
          if($b['year'] != $t['year']){
            $temp[$key]['branch'][$key2]['total'] = 0;
          }
        };
      }
      return $temp;
        $data = DB::select('SELECT * FROM (SELECT sum(t.totalCost) total, b.name branch, Year(t.created_at) createYear
                            FROM transactions t 
                            join branches b on b.id = t.branch_id 
                            where t.status = 3
                            GROUP BY Year(t.created_at), b.name
                            ORDER BY Year(t.created_at) DESC) n GROUP BY n.createYear
                           ') ;
        $newData = []; 
        foreach($data as $d){
          $newData[] = $d;
        }
        return $newData;
    }
    // PENGELUARAN BULANAN
    public function outcomeOfMonth(){
      $datasets = $this->outcomeOfMonthData();
      $pointY = [];
      $pointX = [];
      $total = 0;
      foreach($datasets as $dt){
        if($dt->totalBuy == null){
          $pointX[] =  -0.0001;
        }else{
          $pointX[] = $dt->totalBuy;
          $total = $total + $dt->totalBuy;
        }
      }
      foreach($pointX as $key => $pX){
        if($pX > 0){
          $pointX[$key] = $pX/$total * 100;
        }
      }
      $data = new Data();   
      $data->addPoints($pointX,"ScoreA");  
      $data->setSerieDescription("ScoreA","ScoreA");
      $data->addPoints(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Juni', 'Juli', 'Agust', 'Sep', 'Okt', 'Nov', 'Des'],"Labels");
      $data->setAbscissa("Labels");

      $image = new Image(400,400,$data);
      $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
      $image->drawFilledRectangle(0,0,400,400,$Settings);
      $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
      $image->drawGradientArea(0,0,400,400,DIRECTION_VERTICAL,$Settings);
      $image->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>10,"R"=>80,"G"=>80,"B"=>80));


      $PieChart = new Pie($image,$data);
      $PieChart->draw2DPie(220,200,array("Radius"=>130));
      $image->setShadow(FALSE);
      $PieChart->drawPieLegend(20,100,array("Alpha"=>20));

      \File::put($this->photos_path."/pengeluaranBulanan/".date('d-Y').'.png', $image);
      $pdf = PDF::loadView('reports.pengeluaranBulanan',['datasets'=>$datasets]);
      $pdf->setPaper([0, 0, 550, 900])->save(public_path('/files/report/pengeluaranBulanan').'/'.date('d-Y').'.pdf');
      return $pdf->download(date('d-Y').'.pdf');

    }
    public function outcomeOfMonthData(){
      return DB::select('SELECT * FROM
                            (
                              SELECT 1 AS MONTH
                              UNION SELECT 2 AS MONTH
                              UNION SELECT 3 AS MONTH
                              UNION SELECT 4 AS MONTH
                              UNION SELECT 5 AS MONTH
                              UNION SELECT 6 AS MONTH
                              UNION SELECT 7 AS MONTH
                              UNION SELECT 8 AS MONTH
                              UNION SELECT 9 AS MONTH
                              UNION SELECT 10 AS MONTH
                              UNION SELECT 11 AS MONTH
                              UNION SELECT 12 AS MONTH
                            ) months LEFT JOIN
                            (SELECT * FROM 
                              (
                                SELECT sum(od.buy * od.totalAccept) totalBuy, MONTH(o.created_at) createMonth
                                FROM orders o 
                                join order_details od on od.order_id = o.id 
                                WHERE YEAR(o.created_at) = YEAR(CURDATE()) AND o.status = 2
                                GROUP BY MONTH(o.created_at)
                              ) n GROUP BY n.createMonth
                            ) realCount ON months.MONTH = realCount.createMonth') ;
    }
    // PENJUALAN JASA
    public function serviceReport(){

      $datasets = $this->serviceReportData();
      $pdf = PDF::loadView('reports.penjualanJasa',['datasets'=>$datasets]);
      $pdf->setPaper([0, 0, 550, 900])->save(public_path('/files/report/penjualanJasa').'/'.date('d-Y').'.pdf');
      return $pdf->download(date('d-Y').'.pdf');
    }
    public function serviceReportData(){

      $datasets = \App\VehicleCustomer::with(['detailTransaction','vehicle'])->whereHas('detailTransaction', function ($query){
                                                                                    $query->whereHas('transaction', function ($query2) {
                                                                                      $query2->where([['status', 3],['created_at','LIKE',date('Y-m').'%']]);
                                                                                    });
                                                                                  })->get();
      $temp = [];
      foreach($datasets as $dt){
        $temp[$dt->vehicle->id]['vehicle']['merk'] = $dt->vehicle->merk;
        $temp[$dt->vehicle->id]['vehicle']['type'] = $dt->vehicle->type;
        foreach($dt->detailTransaction->detailTransactionService as $dts){
          $temp[$dt->vehicle->id]['detail'][$dts->service->id]['name'] = $dts->service->name;
          try{
            $temp[$dt->vehicle->id]['detail'][$dts->service->id]['total'] = $dts->total + $temp[$dt->vehicle->id]['detail'][$dts->service->id]['total'];
          }catch (\Exception $e){
            $temp[$dt->vehicle->id]['detail'][$dts->service->id]['total'] = $dts->total;
          }
        }
      }
      return $temp;
    }
    public function leftOverStock($ItemId){
    }
}
