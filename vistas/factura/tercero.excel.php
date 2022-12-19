<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Listado Terceros</title>
    <style>
        .borde{
            border: 1px solid #000 !important;
        }

        .texto{
            font-weight: 700 !important;
        }

        .texto-l{
            text-align: left !important;
        }

        .texto-r{
            text-align: right !important;
        }

        .texto-c{
            text-align: center !important;
        }
    </style>
</head>
<body>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <caption class="borde texto">
                <?php echo utf8_decode($razonsocial)."<br/>".(mb_strtoupper($nombreTipoIden.' : '.$numeroIdent."<br/>$direccion TELEFONO : $telefono"))."<br/>"."LISTADO DE FACTURAS POR TERCERO"; ?>
            </caption>
            <tr>
                <th class="borde">FECHA</th>
                <th class="borde">TIPO</th>
                <th class="borde">NÚMERO</th>
                <th class="borde">DESCRIPCIÓN</th>
                <th class="borde">TERCERO</th>
                <th class="borde">VALOR</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $fechaI = explode("/", $_REQUEST['txtFechaI']);
        $fechaI = "$fechaI[2]-$fechaI[1]-$fechaI[0]";
        $fechaF = explode("/", $_REQUEST['txtFechaF']);
        $fechaF = "$fechaF[2]-$fechaF[1]-$fechaF[0]";
        $data   = $this->factura->obtenerListadoTercerosFactura($_REQUEST['sltTerceroI'], $_REQUEST['sltTerceroF']);
        $total  = 0;
        foreach ($data as $row){
            $dataX = $this->factura->listdaoFacturasTercero($fechaI, $fechaF, $row[0], $_REQUEST['clase']);
            if(count($dataX) > 0){ ?>
                <tr class="borde">
                    <td class="texto-l texto borde" colspan="6"><?php echo $row[1]; ?></td>
                </tr>
                <?php
                foreach ($dataX as $rowX){
                    $datX = $this->factura->obtenerDetalles(md5($rowX[0]));
                    $xxx  = 0;
                    foreach ($datX as $rowt){
                        $xxx += (($rowt[2] + $rowt[4] + $rowt[5] + $rowt[6]) * $rowt[3]);
                    }
                    $total += $xxx; ?>
                    <tr class="borde">
                        <td class="borde texto-l"><?php echo $rowX[1]; ?></td>
                        <td class="borde texto-c"><?php echo $rowX[2]; ?></td>
                        <td class="borde texto-l"><?php echo $rowX[3]; ?></td>
                        <td class="borde texto-l"><?php echo $rowX[4]; ?></td>
                        <td class="borde texto-l"><?php echo $rowX[5]; ?></td>
                        <td class="borde texto-r"><?php echo number_format($xxx,0); ?></td>
                    </tr>
                    <?php
                }
            }
        }
        ?>
        </tbody>
        <tfoot>
            <tr class="borde">
                <th class="borde texto-c" colspan="5">TOTAL</th>
                <th class="borde texto-r"><?php echo number_format($total, 0); ?></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>