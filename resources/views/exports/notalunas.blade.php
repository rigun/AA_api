<html>
    <head>
        <title>Judul</title>
    </head>
    <body>
    <?php 
        function rupiah($angka){
    	    $hasil_rupiah = "Rp " . number_format($angka,2,',','.');
	        return $hasil_rupiah;
        }
    ?>
        <div class="container">
            <div class="header">
                <table>
                    <tr>
                        <td>
                            <img src="{{public_path('/images/LogoBlue.png')}}" alt="" style="width: 100px; margin-right: 20px;">
                        </td>
                        <td>
                            <div class="titleHeader" style="margin-left: 20px;">
                                <h1 style="text-align: center;margin: 0px">ATMA AUTO</h1>
                                <p style="text-align: center;margin: 0px">MOTORCYCLE SPAREPART AND SERVICES <br> Jl.Babarsari No. 43 Yogyakarta 552181 <br> Telp. (0274) 487711 <br>http://wwww.atmaauto.com</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <hr>
            <h3 style="text-align:center">NOTA LUNAS</h3>
            <hr>
            <div class="detailTransactionUser">
            <?php $date=date_create($transaction->updated_at);
                $tD = date_format($date,"d-m-y H:i"); ?>
                <p style="text-align:right">{{$tD}}</p>
                <h2>{{$transaction->transactionNumber}}-{{$transaction->id}}</h2>
                        <table>
                            <tr>
                                <td>Cust</td>
                                <td style="width: 200px">{{$transaction->customer->name}}</td>
                                <td>CS</td>
                                <td>{{$transaction->cs->name}}</td>
                            </tr>
                            <tr>
                                <td>Telepon</td>
                                <td style="width: 200px">{{$transaction->customer->phoneNumber}}</td>
                                @if($service != null)
                                <td>Montir</td>
                                <td>
                                <?php 
                                 $unique = [];
                                 foreach ($detailTransaction as $dt) {
                                    $j = 0;
                                   foreach ($unique as $u) {
                                       if ($u->montir_id === $dt->montir_id) {
                                           break;
                                        }
                                        $j = $j + 1;
                                   }
                                   if ($j === sizeOf($unique)){
                                        $unique[] = $dt;
                                   }
                                 }
                                ?>
                                @foreach($unique as $u)
                                <p>{{$u->montir->name}}</p>
                                @endforeach
                                </td>
                                @endif
                            </tr>
                            <tr>
                                <td>Motor</td>
                                <td style="width: 200px">
                                @foreach($customerVehicle as $cV)                                
                                <p>{{$cV->vehicle->merk}} {{$cV->vehicle->type}} {{$cV->licensePlate}}</p>
                                @endforeach                                
                                </td>
                            </tr>
                        </table>
            </div>
            <hr>
            @if ($sparepart != null)
            <h3 style="text-align:center">SPAREPARTS</h3>
            <hr>
            <table class="dataTable">
                <tr class="rowTable">
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Merk</th>
                    <th style="text-align: right">Jumlah</th>
                    <th style="text-align: right">Subtotal</th>
                </tr>
                <?php $i=0 ?>
                @foreach($sparepart as $dtsp)
                <tr class="rowTable">
                    <td>{{$dtsp['data']->sparepart_code}}</td>
                    <td>{{$dtsp['data']->sparepart->name}}</td>
                    <td>{{$dtsp['data']->sparepart->merk}}</td>
                    <td style="text-align: right">{{$dtsp['data']->total}}</td>
                    <?php
                    $number = $dtsp['data']->total * $dtsp['data']->price;
                    $priceTotalSparepart = rupiah($number);
                    ?>
                    <td style="text-align: right">{{$priceTotalSparepart}}</td>
                </tr>
                <?php 
                    $i= $i + ($dtsp['data']->total * $dtsp['data']->price);
                    $totalSparepart = rupiah($i);
                ?>
                @endforeach
                <tr class="footerTableData">
                    <td colspan="6" style="text-align: right">{{$totalSparepart}}</td>
                </tr>
            </table>
            <hr>
            @endif
            @if ($service != null)
            <h3 style="text-align:center">SERVICE</h3>
            <hr>
            <table class="dataTable">
                <tr class="rowTable">
                    <th>Kode</th>
                    <th>Nama</th>
                    <th style="text-align: right">Jumlah</th>
                    <th style="text-align: right">SubTotal</th>
                </tr>
                <?php $j=0 ?>
                @foreach($service as $dtsv)
                <tr class="rowTable">
                    <td>{{$dtsv['service']->id}}</td>
                    <td>{{$dtsv['service']->name}}</td>
                    <td style="text-align: right">{{$dtsv->total}}</td>
                    <?php 
                        $temptTotal= $dtsv->total * $dtsv->price;
                        $priceTotalService = rupiah($temptTotal);
                    ?>
                    <td style="text-align: right">{{$priceTotalService}}</td>
                </tr>
                <?php 
                    $j= $j + ($dtsv->total * $dtsv->price);
                    $totalService = rupiah($j);
                ?>
                @endforeach
                
                <tr class="footerTableData">
                    <td colspan="4" style="text-align: right">{{$totalService}}</td>
                </tr>
            </table>
            @endif
            <table style="width: 100%">
                   <tr>
                    <td>Cust <br> <br> <br>({{$transaction->customer->name}})</td>
                    <td>Kasir <br> <br> <br>({{$transaction->cashier->name}})</td>
                    <?php 
                        $diskon = rupiah($transaction->diskon);
                        $subTotal = rupiah($transaction->totalCost);
                        $total = rupiah($transaction->totalCost - $transaction->diskon);
                    ?>
                    <td>
                    <table style="width: 100%;">
                    <tr>
                       <td>SubTotal</td>
                       <td style="text-align:right">{{$subTotal}}</td>
                    </tr>
                    <tr>
                       <td>Diskon</td>
                       <td style="text-align:right">{{$diskon}}</td>
                    </tr>
                    <tr>
                       <td>Total</td>
                       <td style="text-align:right"><strong>{{$total}}</strong></td>
                    </tr>
                    </table>
                      </td>
                   </tr> 
            </table>
        </div>

    </body>
    <style>
        h3{
            margin:0px;
        }
        .dataTable{
            width: 100%;
            border-top: double;
            border-bottom: double;
            border-spacing: 0px;
        }
        .rowTable th{
            border-bottom: solid;
            text-align: left;
            padding: 8px 0px;
        }
        .rowTable td{
            padding: 8px 0px;
        }
        .footerTableData td{
            border-top: double;
            padding: 8px 0px;
        }
        .container{
            margin: auto;
            max-width: 600px;
            width: 100%;
        }
        .columns{
            display: flex
        }
        .column{
            width: 50%;
        }
    </style>
</html>