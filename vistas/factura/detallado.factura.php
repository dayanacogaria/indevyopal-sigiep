<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>INFORME DETALLADO</title>
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
                <?php echo utf8_decode($razonsocial)."<br/>".(mb_strtoupper($nombreTipoIden.' : '.$numeroIdent."<br/>$direccion TELEFONO : $telefono\nLISTADO DETALLADO DE FACTURAS")); ?>
            </caption>
            <tr class="borde">
                <th class="borde">CONCEPTO</th>
                <th class="borde">CANTIDAD</th>
                <th class="borde">VALOR</th>
                <th class="borde">IVA</th>
                <th class="borde">IMPOCONSUMO</th>
                <th class="borde">AJUSTE PESO</th>
                <th class="borde">VALOR TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $fechaI = explode("/", $_REQUEST['txtFechaI']);
            $fechaI = "$fechaI[2]-$fechaI[1]-$fechaI[0]";
            $fechaF = explode("/", $_REQUEST['txtFechaF']);
            $fechaF = "$fechaF[2]-$fechaF[1]-$fechaF[0]";
            $data   = $this->factura->listdaoFacturasDetalle($fechaI, $fechaF, $_REQUEST['clase']);
            $total  = 0; $iva = 0; $impo = 0; $ajuste = 0;
            foreach ($data as $row){
            $dataX = $this->factura->obtenerDetalles(md5($row[0]));
                if(count($dataX) > 0){ ?>
                    <tr class="borde">
                        <td class="borde texto texto-l"><?php echo $row[1]; ?></td>
                        <td class="borde texto texto-l"><?php echo $row[2]; ?></td>
                        <td class="borde texto texto-r"><?php echo $row[3]; ?></td>
                        <td colspan="4" class="borde texto texto-l"><?php echo $row[5]; ?></td>
                    </tr>
                    <?php
                    foreach ($dataX as $rowX){
                        $xxx = (($rowX[2] + $rowX[4] + $rowX[5] + $rowX[6]) * $rowX[3]);
                        $total += $xxx; ?>
                        <tr class="borde">
                            <td class="borde texto-l"><?php echo $rowX[8]; ?></td>
                            <td class="borde texto-r"><?php echo $rowX[3]; ?></td>
                            <td class="borde texto-r"><?php echo number_format($rowX[2], 2); ?></td>
                            <td class="borde texto-r"><?php echo number_format($rowX[4], 2); ?></td>
                            <td class="borde texto-r"><?php echo number_format($rowX[5], 2); ?></td>
                            <td class="borde texto-r"><?php echo number_format($rowX[6], 2); ?></td>
                            <td class="borde texto-r"><?php echo number_format($xxx, 2); ?></td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th class="borde texto-c" colspan="5">TOTAL</th>
                <th class="borde texto-r"><?php echo number_format($total, 0); ?></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>