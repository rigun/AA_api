<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\TransactionDetail;
use App\TransactiondetailService;
use App\TransactiondetailSparepart;
use App\Vehicle;
use DB;
class ReportController extends Controller
{
    public function sparepartOfYear(){
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
                            ) meses LEFT JOIN
                            (SELECT * FROM 
                              (
                                SELECT sum(tds.total) maxTotal, sp.code, sp.name, MONTH(t.created_at) createMonth
                                FROM transactions t 
                                join transaction_details td on td.transaction_id = t.id 
                                join transactiondetail_spareparts tds on tds.trasanctiondetail_id = td.id 
                                join spareparts sp on tds.sparepart_code = sp.code
                                WHERE YEAR(t.created_at) = YEAR(CURDATE()) AND t.status = 3
                                GROUP BY MONTH(t.created_at), tds.sparepart_code 
                                ORDER BY sum(tds.total) DESC
                              ) n GROUP BY n.createMonth
                            ) realCount ON meses.MONTH = realCount.createMonth') ;
    }
    // LAPORAN PENDAPATAN BULANAN
    public function incomeOfMonth(){
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
                            ) meses LEFT JOIN
                            (
                              SELECT sum(t.totalServices) Service, sum(t.totalSpareparts) Spareparts, sum(t.totalCost) Total, MONTH(t.created_at) createMonth
                              FROM transactions t 
                              WHERE YEAR(t.created_at) = YEAR(CURDATE()) AND t.status = 3
                              GROUP BY MONTH(t.created_at)
                            ) realCount ON meses.MONTH = realCount.createMonth') ;
    }
    public function incomeOfYear(){
        $data = DB::select(' SELECT sum(t.totalCost) total, b.name branch, Year(t.created_at) createYear
                            FROM transactions t 
                            join branches b on b.id = t.branch_id 
                            where t.status = 3
                            GROUP BY Year(t.created_at), b.name
                            ORDER BY Year(t.created_at) DESC
                           ') ;
        $newData = []; 
        foreach($data as $d){
          $newData[$d->createYear][] = $d;
        }
        return $newData;
    }
    // PENGELUARAN BULANAN
    public function outcomeOfMonth(){
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
                            ) meses LEFT JOIN
                            (SELECT * FROM 
                              (
                                SELECT sum(od.buy * od.totalAccept) totalBuy, MONTH(o.created_at) createMonth
                                FROM orders o 
                                join order_details od on od.order_id = o.id 
                                WHERE YEAR(o.created_at) = YEAR(CURDATE()) AND o.status = 2
                                GROUP BY MONTH(o.created_at)
                              ) n GROUP BY n.createMonth
                            ) realCount ON meses.MONTH = realCount.createMonth') ;
    }
    // PENJUALAN JASA
    public function serviceReport(){

      return DB::select('SELECT v.merk, v.type, s.name, count(s.id)
                          FROM vehicles v 
                          JOIN vehicle_customers vc ON vc.vehicle_id = v.id
                          JOIN transaction_details td ON td.vehicleCustomer_id = vc.id
                          JOIN transactions t ON td.transaction_id = t.id
                          JOIN transactiondetail_services tds ON tds.trasanctiondetail_id = td.id
                          JOIN services s ON tds.service_id = tds.id
                          GROUP BY v.id
                        ') ;
    }
}
