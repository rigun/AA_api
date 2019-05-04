<html>
    <head>
        <title>Judul</title>
    </head>
    <body>
        <div class="container">
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
            <h3 style="text-align:center">SURAT PEMESANAN</h3>
            <hr>
            <div class="detailTransactionUser">
            <?php $date=date_create($order->created_at);
                $tD = date_format($date,"d M Y"); ?>
                <p style="text-align:right">{{$tD}}</p>
                <div style="border: 2px dashed black; padding: 10px; min-width: 150px; max-width: 200px">
                    <p>Kepada Yth:</p>
                    <p>{{$supplier->name}}</p>
                    <p>{{$supplier->address}}</p>
                    <p>{{$supplier->city}}</p>
                    <p>{{$supplier->phoneNumber}}</p>
                </div>
            </div>
            <hr>
            <h3 style="text-align:center">SPAREPARTS</h3>
            <hr>
            <table class="dataTable">
                <tr class="rowTable">
                    <th>Nomor</th>
                    <th>Nama Barang</th>
                    <th>Merk</th>
                    <th>Tipe Barang</th>
                    <th>Satuan</th>
                    <th style="text-align: right">Jumlah</th>
                </tr>
                @foreach($detail as $key => $dtsp)
                <tr>
                    <td>{{$key}}</td>
                    <td>{{$dtsp->sparepart->name}}</td>
                    <td>{{$dtsp->sparepart->merk}}</td>
                    <td>{{$dtsp->sparepart->type}}</td>
                    <td>{{$dtsp->unit}}</td>
                    <td style="text-align: right">{{$dtsp->total}}</td>
                </tr>
                @endforeach
            </table>
            <table style="width: 100%; border: none">
            <tr style="border: none">
            <td style="width: 400px;border: none"></td>
            <td style="width: 200px;border: none"> <p style="text-align: center">Hormat Kami <br> <br> <br> (Philips Purnomo)</p></td>
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