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
            <h3 style="text-align:center">Laporan Sparepart Terlaris</h3>
            <hr>
            <div class="detailTransactionUser">
            <?php
            $month = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            ?>
                <p style="text-align:left">Tahun : {{$year}}</p>
            </div>
            <table class="dataTable">
                <tr class="rowTable">
                    <th style="text-align:center">Nomor</th>
                    <th style="text-align:center">Bulan</th>
                    <th style="text-align:center">Nama Barang</th>
                    <th style="text-align:center">Tipe Barang</th>
                    <th style="text-align:center">Jumlah Penjualan</th>
                </tr>
                @foreach($detail as $key => $dtsp)
                <tr>
                    <td style="text-align:center">{{$key + 1}}</td>
                    <td>{{$month[$dtsp->MONTH-1]}}</td>
                    <td>{{$dtsp->name}}</td>
                    <td>{{$dtsp->type}}</td>
                    <td style="text-align:right">{{$dtsp->maxTotal}}</td>
                </tr>
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