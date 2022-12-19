<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Factura</title>
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
            <tr>
                <th class="borde texto texto-c" colspan="8">
                    <?php echo utf8_decode($razonsocial)."<br/>".(mb_strtoupper($nombreTipoIden.' : '.$numeroIdent."<br/>$direccion TELEFONO : $telefono\nLISTADO DE FACTURAS")); ?>
                </th>
            </tr>
            <tr class="">
                <th class="borde" style="border: 1px solid #000 !important;">FECHA</th>
                <th class="borde" style="border: 1px solid #000 !important;">TIPO</th>
                <th class="borde" style="border: 1px solid #000 !important;">NÚMERO</th>
                <th class="borde" style="border: 1px solid #000 !important;">DESCRIPCIÓN</th>
                <th class="borde" style="border: 1px solid #000 !important;">TERCERO</th>
                <th class="borde" style="border: 1px solid #000 !important;">VALOR BASE</th>
                <th class="borde" style="border: 1px solid #000 !important;">VALOR IVA</th>
                <th class="borde" style="border: 1px solid #000 !important;">VALOR TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $fechaI = explode("/", $_REQUEST['txtFechaI']);
            $fechaI = "$fechaI[2]-$fechaI[1]-$fechaI[0]";
            $fechaF = explode("/", $_REQUEST['txtFechaF']);
            $fechaF = "$fechaF[2]-$fechaF[1]-$fechaF[0]";
            $data   = $this->factura->listdaoFacturas($fechaI, $fechaF, $_REQUEST['sltTipoI'], $_REQUEST['sltTipoF'], $_REQUEST['clase']);
            list($total, $xtVB, $xtVI) = array(0, 0, 0);
            foreach ($data as $row){
                $datX = $this->factura->obtenerDetalles(md5($row[0]));
                list($xVB, $xVI, $xxx) = array(0, 0, 0);
                foreach ($datX as $rowX){
                    $xVB += ($rowX[2] * $rowX[3]);
                    $xVI += ($rowX[4] * $rowX[3]);
                    $xxx += (($rowX[2] + $rowX[4] + $rowX[5] + $rowX[6]) * $rowX[3]);
                }
                $total += $xxx; $xtVB += $xVB; $xtVI += $xVI;
                ?>
                <tr>
                    <td class="borde texto-l" style="border: 1px solid #000 !important;"><?php echo $row[1]; ?></td>
                    <td class="borde texto-l" style="border: 1px solid #000 !important;"><?php echo $row[2]; ?></td>
                    <td class="borde texto-l" style="border: 1px solid #000 !important;"><?php echo $row[3]; ?></td>
                    <td class="borde texto-l" style="border: 1px solid #000 !important;"><?php echo $row[4]; ?></td>
                    <td class="borde texto-l" style="border: 1px solid #000 !important;"><?php echo $row[5]; ?></td>
                    <td class="borde texto-r" style="border: 1px solid #000 !important;"><?php echo number_format($xVB, 2); ?></td>
                    <td class="borde texto-r" style="border: 1px solid #000 !important;"><?php echo number_format($xVI, 2); ?></td>
                    <td class="borde texto-r" style="border: 1px solid #000 !important;"><?php echo number_format($xxx, 2); ?></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th class="borde texto-c" colspan="5" style="border: 1px solid #000 !important;">TOTAL</th>
                <th class="borde texto-r" style="border: 1px solid #000 !important;"><?php echo number_format($xtVB, 2); ?></th>
                <th class="borde texto-r" style="border: 1px solid #000 !important;"><?php echo number_format($xtVI, 2); ?></th>
                <th class="borde texto-r" style="border: 1px solid #000 !important;"><?php echo number_format($total, 2); ?></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>