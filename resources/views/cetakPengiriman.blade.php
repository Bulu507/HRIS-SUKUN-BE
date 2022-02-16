<style>
    body{
      max-height: 266px!important;
      max-width: 368px!important;
    }
    table{
      height: 266px;
      width: 365px;
      border-collapse: collapse;
      border: 2px solid black;
    
    }
    th, td {
      border: 1.5px solid black;
    }
    
    table, th, td {
      margin: 5px 2px 2px 2px;
      font-family: Arial, Helvetica, sans-serif;
      vertical-align: text-top;
      font-size: 13px;
    }
    td {
    padding: 4px 6px 0px 6px;
    }
    </style>
    <html>
      <head>
        <title>Order Transaksi #{{$NoInvoice}}</title>
      </head>
    
      <body style="width:10cm;height:5cm;margin:0px;" onload="window.print();" >
        <div class="a">
    
        </div>
        <table>
          <tr>
            <td style="width:65%!important;font-size:17px!important"rowspan="4">
              Kepada :
              <br>
              <br>
              <b>
                <?php echo ucfirst($NamaPenerima); ?>
                <br>
                <?php echo ucfirst($Alamat); ?>
                <br>
                <?php echo 'kec.'.ucfirst($kecamatan).','.$kab_kota; ?>
                <br>
                <?php echo 'No : '.ucfirst($NoPenerima); ?>
              </b>
            </td>
            <td style="text-align: center">
              Ekspedisi :
              <p style="margin: 5px !important;font-weight: bold;"><?php echo $Expedisi ?></p>
            </td>
          </tr>
          <tr>
            <td style="text-align: center">
              pengirim :
              <p style="margin: 5px !important;font-weight: bold;"><?php echo $Pengirim ?></p>
              <p style="margin: 5px !important"><?php echo $NoPengirim ?></p>
            </td>
          </tr>
          <tr>
            <td style="text-align: center">
              Invoice :
              <p style="margin: 5px !important;font-weight: bold;"><?php echo $NoInvoice ?></p>
            </td>
          </tr>
          <tr>
            <td style="text-align: center;margin: auto !important;">
              <p style="vertical-align: baseline;margin-top: 5px!important;font-weight: bold;font-size:35px!important;">AQW</p>
            </td>
          </tr>
        </table>
      </body>
    </html>