<html>
    <head>
        <title>Judul</title>
    </head>
    <body>
        <div class="container" >
            <div class="header">
                <table style=" border: none">
                    <tr style=" border: none">
                        <td style=" border: none">
                            <img src="{{public_path('/images/LogoBlue.png')}}" alt="" style="width: 100px; margin-right: 20px;">
                        </td>
                        <td style=" border: none">
                            <div class="titleHeader" style="margin-left: 20px;">
                                <h1 style="text-align: center;margin: 0px">ATMA AUTO</h1>
                                <p style="text-align: center;margin: 0px">MOTORCYCLE SPAREPART AND SERVICES <br> Jl.Babarsari No. 43 Yogyakarta 552181 <br> Telp. (0274) 487711 <br>http://wwww.atmaauto.com</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <hr>
            <h3 style="text-align:center">Laporan Penjualan Jasa</h3>
            <hr>
            <div class="detailTransactionUser">
            <?php
            $tY = date("Y"); 
            $tM = date("m"); 
            $month = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            ?>
            <table style="border: none !important">
            <tr  style="border: none !important">
                <td  style="border: none !important">Tahun</td>
                <td  style="border: none !important">:</td>
                <td  style="border: none !important">{{$tY}}</td>
            </tr>
            <tr  style="border: none !important">
                <td  style="border: none !important">Bulan</td>
                <td  style="border: none !important">:</td>
                <td  style="border: none !important">{{$month[(int)$tM - 1]}}</td>
            </tr>
            </table>
            </div>
            <br>
            <table class="dataTable">
                <tr class="rowTable">
                    <th style="text-align:center">Nomor</th>
                    <th style="text-align:center">Merk</th>
                    <th style="text-align:center">Tipe Motor</th>
                    <th style="text-align:center">Nama Service</th>
                    <th style="text-align:center">Jumlah Penjualan</th>
                </tr>
                    <?php $n = 1?>
                @foreach($datasets as $key => $dt)
                    <?php $i = 0?>
                    @foreach($dt['detail'] as $key2 => $d)
                    <tr>
                        @if($i == 0)
                        <td style="text-align:center" rowspan="{{sizeOf($dt['detail'])}}">{{$n}}</td>
                        <td rowspan="{{sizeOf($dt['detail'])}}" style="text-align: center">{{$dt['vehicle']['merk']}}</td>
                        <td rowspan="{{sizeOf($dt['detail'])}}" style="text-align: center">{{$dt['vehicle']['type']}}</td>
                    <?php $n++?>

                        @endif
                        <td>{{$d['name']}}</td>
                        <td style="text-align:right">{{$d['total']}}</td>
                    <?php $i = 1?>

                    </tr>
                    @endforeach

                @endforeach
            </table>
            <table style="width: 100%; border: none">
            <tr style="border: none">
            <td style="width: 400px;border: none"></td>
            <td style="width: 200px;border: none"> <p style="text-align: right">Dicetak Tanggal : 
            <?php $today = explode('-',date('d-m-Y'));?>
            {{$today[0]}} {{$month[(int)$today[1] - 1]}} {{$today[2]}}</p></td>
            </tr>
            </table>
        </div>
        </div>
    </body>
    <style>
        h3{
            margin:0px;
        }
        table, td,tr,th{
            border: 1px solid black;
        }
        table{
            border-collapse: collapse;
        }
        .dataTable{
            width: 100%;
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