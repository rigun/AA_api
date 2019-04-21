<html>
    <head>
        <title>Judul</title>
    </head>
    <body>
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
            <h3 style="text-align:center">SURAT PERINTAH KERJA</h3>
            <hr>
            <div class="detailUser">
                <p style="text-align:right">24-01-19</p>
                <h2>SS-2401119-141</h2>
                        <table>
                            <tr>
                                <td>Cust</td>
                                <td style="width: 200px">Stefanus Rojali</td>
                                <td>CS</td>
                                <td>Natalia</td>
                            </tr>
                            <tr>
                                <td>Telepon</td>
                                <td style="width: 200px">08223809</td>
                                <td>Montir</td>
                                <td>Toni</td>
                            </tr>
                            <tr>
                                <td>Motor</td>
                                <td style="width: 200px">Yamaha Jupiter </td>
                            </tr>
                        </table>
            </div>
            <hr>
            <h3 style="text-align:center">SPAREPARTS</h3>
            <hr>
            <table class="dataTable">
                <tr class="rowTable">
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Merk</th>
                    <th>Rak</th>
                    <th style="text-align: right">Jumlah</th>
                </tr>
                <tr class="rowTable">
                    <td>sdas</td>
                    <td>asddsaf</td>
                    <td>asdfasdf</td>
                    <td>sadfsdf</td>
                    <td style="text-align: right">1</td>
                </tr>
                <tr class="footerTableData">
                    <td colspan="5" style="text-align: right">1</td>
                </tr>
            </table>
            <hr>
            <h3 style="text-align:center">SERVICE</h3>
            <hr>
            <table class="dataTable">
                <tr class="rowTable">
                    <th>Kode</th>
                    <th>Nama</th>
                    <th style="text-align: right">Jumlah</th>
                </tr>
                <tr class="rowTable">
                    <td>sdas</td>
                    <td>asddsaf</td>
                    <td style="text-align: right">1</td>
                </tr>
                <tr class="footerTableData">
                    <td colspan="3" style="text-align: right">1</td>
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