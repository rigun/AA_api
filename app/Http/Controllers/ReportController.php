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
  public function sparepartOfYear($year){
    $detail = $this->sparepartOfYearData($year);
    $pdf = PDF::loadView('reports.sparepartTerlaris',['detail'=>$detail, 'year'=>$year]);
    $pdf->setPaper([0, 0, 550, 900])->save(public_path('/files/report/sparepartTerlaris').'/'.date('d-Y').'.pdf');
    return $pdf->download(date('d-Y').'.pdf');
  }
  public function sparepartOfYearData($year){
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
                            WHERE YEAR(t.created_at) = ? AND t.status = 3
                            GROUP BY MONTH(t.created_at), tds.sparepart_code 
                            ORDER BY sum(tds.total) DESC
                          ) n GROUP BY n.createMonth
                        ) realCount ON months.MONTH = realCount.createMonth', [$year]);
  }
    // LAPORAN PENDAPATAN BULANAN
    public function incomeOfMonth($year){
      $datasets = $this->incomeOfMonthData($year);
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
      $pdf = PDF::loadView('reports.pendapatanBulanan',['datasets'=>$datasets, 'year'=>$year]);
      $pdf->setPaper([0, 0, 550, 900])->save(public_path('/files/report/pendapatanBulanan').'/'.date('d-Y').'.pdf');
      return $pdf->download(date('d-Y').'.pdf');
    }
    public function incomeOfMonthData($year){
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
                              WHERE YEAR(t.created_at) = ? AND t.status = 3
                              GROUP BY MONTH(t.created_at)
                            ) realCount ON months.MONTH = realCount.createMonth',[$year]) ;
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
    public function outcomeOfMonth($year){
      $datasets = $this->outcomeOfMonthData($year);
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
      $pdf = PDF::loadView('reports.pengeluaranBulanan',['datasets'=>$datasets,'year'=>$year]);
      $pdf->setPaper([0, 0, 550, 900])->save(public_path('/files/report/pengeluaranBulanan').'/'.date('d-Y').'.pdf');
      return $pdf->download(date('d-Y').'.pdf');

    }
    public function outcomeOfMonthData($year){
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
                                WHERE YEAR(o.created_at) = ? AND o.status = 2
                                GROUP BY MONTH(o.created_at)
                              ) n GROUP BY n.createMonth
                            ) realCount ON months.MONTH = realCount.createMonth',[$year]) ;
    }
    // PENJUALAN JASA
    public function serviceReport($year,$month){

      $datasets = $this->serviceReportData($year,$month);
      $pdf = PDF::loadView('reports.penjualanJasa',['datasets'=>$datasets, 'year'=>$year,'month'=>$month]);
      $pdf->setPaper([0, 0, 550, 900])->save(public_path('/files/report/penjualanJasa').'/'.date('d-Y').'.pdf');
      return $pdf->download(date('d-Y').'.pdf');
    }
    public function serviceReportData($year,$month){

      $datasets = \App\VehicleCustomer::with(['detailTransaction','vehicle'])->whereHas('detailTransaction', function ($query) use ($year,$month){
                                                                                    $query->whereHas('transaction', function ($query2) use ($year,$month){
                                                                                      $query2->where([['status', 3],['created_at','LIKE',$year.'-'.$month.'%']]);
                                                                                    });
                                                                                  })->get();
      $temp = [];
      foreach($datasets as $dt){
        $temp[$dt->vehicle->id]['vehicle']['merk'] = $dt->vehicle->merk;
        $temp[$dt->vehicle->id]['vehicle']['type'] = $dt->vehicle->type;
        foreach($dt->detailTransaction as $dT){
          foreach($dT->detailTransactionService as $dts){
            $temp[$dt->vehicle->id]['detail'][$dts->service->id]['name'] = $dts->service->name;
            try{
              $temp[$dt->vehicle->id]['detail'][$dts->service->id]['total'] = $dts->total + $temp[$dt->vehicle->id]['detail'][$dts->service->id]['total'];
            }catch (\Exception $e){
              $temp[$dt->vehicle->id]['detail'][$dts->service->id]['total'] = $dts->total;
            }
          }
        }
      }
      return $temp;
    }
    public function leftOverStock($year,$type){
      $datasets = $this->leftOverStockData($year,$type);
      $pointX = [];
      foreach($datasets as $dt){
        if($dt->Sisa == null){
          $pointX[] =  0;
        }else{
          $pointX[] = $dt->Sisa;
        }
      }
      $data = new Data();
      $data->addPoints($pointX, "Sisa");
      $data->addPoints($this->monthsData, "Labels");
      $data->setPalette('Sisa',array("R"=>0,"G"=>0,"B"=>255,"Ticks"=>4,"Weight"=>3));
      $data->setSerieDescription("Labels", "Bulan");
      $data->setAbscissa("Labels");

      /* Create the 1st chart */
      $image = new Image(600, 400, $data);
      $image->setGraphArea(80, 60, 500, 320);
      $image->drawFilledRectangle(80, 60, 500, 320, [
          "R" => 255,
          "G" => 255,
          "B" => 255,
          "Surrounding" => -200,
          "Alpha" => 60
      ]);
      $image->drawScale(["Mode"=>SCALE_MODE_START0,"DrawSubTicks" => true]);
      $image->setShadow(true, ["X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10]);
      $image->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>10,"R"=>80,"G"=>80,"B"=>80));
      $image->drawLineChart(["DisplayValues" => true, "DisplayColor" => "DISPLAY_AUTO"]);
      $image->setShadow(false);

      /* Write the legend */
      $image->drawLegend(510, 205, ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_HORIZONTAL]);
      \File::put($this->photos_path."/sisaStock/".date('d-Y').'.png', $image);
      $pdf = PDF::loadView('reports.sisaStock',['datasets'=>$datasets,'year'=> $year,'type'=>$type]);
      $pdf->setPaper([0, 0, 550, 900])->save(public_path('/files/report/sisaStock').'/'.date('d-Y').'.pdf');
      return $pdf->download(date('d-Y').'.pdf');
    }
    public function leftOverStockData($year,$type){
      try{
      $startStock = DB::select('SELECT (CASE
        WHEN ISNULL(trx.totalTransaksi) AND ISNULL(ord.allTotalAccept) THEN spr.nowadayStock 
        WHEN ISNULL(trx.totalTransaksi) THEN spr.nowadayStock - ord.allTotalAccept 
        WHEN ISNULL(ord.allTotalAccept) THEN spr.nowadayStock + trx.totalTransaksi 
        ELSE spr.nowadayStock + trx.totalTransaksi - ord.allTotalAccept
      END
    ) startStock FROM (SELECT sum(sb.stock) nowadayStock, s.type FROM sparepart_branches sb JOIN spareparts s ON sb.sparepart_code = s.code WHERE s.type = ? GROUP BY s.type) spr
                         LEFT JOIN (SELECT sum(od.totalAccept) allTotalAccept, s.type FROM orders o JOIN order_details od on od.order_id = o.id 
                         LEFT JOIN spareparts s on od.sparepart_code = s.code 
                      WHERE o.status = 2 AND s.type = ?) ord ON ord.type = spr.type 
                      LEFT JOIN (SELECT sum(tds.total) totalTransaksi, sp.type FROM transactions t 
                        join transaction_details td on td.transaction_id = t.id 
                        join transactiondetail_spareparts tds on tds.trasanctiondetail_id = td.id 
                        join spareparts sp on tds.sparepart_code = sp.code
                        WHERE t.status = 3 AND sp.type = ?) trx ON trx.type = spr.type',[$type,$type,$type])[0]->startStock;

      }catch(\Exception $e){
        $startStock = 0;
      }
      $order = DB::select('SELECT MONTH(o.created_at) createMonth, sum(od.totalAccept) totalPemesanan FROM orders o 
      JOIN order_details od on od.order_id = o.id 
      JOIN spareparts s on od.sparepart_code = s.code 
      WHERE YEAR(o.created_at) = ? AND o.status = 2 AND s.type = ?
      GROUP BY MONTH(o.created_at)', [$year,$type]);
      $trx = DB::select('SELECT sum(tds.total) totalTransaksi, sp.type, MONTH(t.created_at) createMonth
      FROM transactions t 
      join transaction_details td on td.transaction_id = t.id 
      join transactiondetail_spareparts tds on tds.trasanctiondetail_id = td.id 
      join spareparts sp on tds.sparepart_code = sp.code
      WHERE YEAR(t.created_at) = ? AND t.status = 3 AND sp.type = ?
      GROUP BY MONTH(t.created_at)',[$year,$type]);
      if (!$order && !$trx){
        return 'Sparepart';
      } else if(!$order){
        return 'Transaksi';
      } else if(!$trx){
        return 'Order';
      } else {
        return 'all';
      }
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
      (SELECT ord.createMonth, 
      (CASE
        WHEN ISNULL(trx.totalTransaksi) AND ISNULL(ord.totalPemesanan) THEN ? 
        WHEN ISNULL(trx.totalTransaksi) THEN ord.totalPemesanan + ? 
        WHEN ISNULL(ord.totalPemesanan) THEN ? - trx.totalTransaksi 
        ELSE ord.totalPemesanan - trx.totalTransaksi + ?
      END) Sisa FROM (
        SELECT MONTH(o.created_at) createMonth, sum(od.totalAccept) totalPemesanan FROM orders o 
      JOIN order_details od on od.order_id = o.id 
      JOIN spareparts s on od.sparepart_code = s.code 
      WHERE YEAR(o.created_at) = ? AND o.status = 2 AND s.type = ?
      GROUP BY MONTH(o.created_at)
      ) ord LEFT JOIN (
        SELECT sum(tds.total) totalTransaksi, sp.type, MONTH(t.created_at) createMonth
      FROM transactions t 
      join transaction_details td on td.transaction_id = t.id 
      join transactiondetail_spareparts tds on tds.trasanctiondetail_id = td.id 
      join spareparts sp on tds.sparepart_code = sp.code
      WHERE YEAR(t.created_at) = ? AND t.status = 3 AND sp.type = ?
      GROUP BY MONTH(t.created_at)
      ) trx ON ord.createMonth = trx.createMonth) realCount ON months.MONTH = realCount.createMonth',[$startStock,$startStock,$startStock,$startStock,$startStock,$year,$type,$year,$type]);

    }
}
