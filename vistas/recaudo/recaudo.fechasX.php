<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>*** Listado de Recaudo entre fechas ***</title>
</head>
<body>
    <table style="width: 100%;">
        <thead>
            <tr>
                <th colspan="10" style="border: solid #000 1px; text-align: center;">
                    <?php echo utf8_decode($razonsocial)."<br/>".(mb_strtoupper($nombreTipoIden.' : '.$numeroIdent."<br/>$direccion TELEFONO : $telefono"))."<br/>"."LISTADO DE RECAUDOS ENTRE FECHAS"; ?>
                </th>
            </tr>
            <tr>
                <th style="border: solid #000 1px; text-align: center;" colspan="3">RECAUDO</th>
                <th style="border: solid #000 1px; text-align: center;" colspan="3">FACTURA</th>
                <th style="border: solid #000 1px; text-align: center;" rowspan="2">TERCERO</th>
                <th style="border: solid #000 1px; text-align: center;" rowspan="2">DESCRIPCIÓN</th>
                <th style="border: solid #000 1px; text-align: center;" rowspan="2">VALOR FACTURA</th>
                <th style="border: solid #000 1px; text-align: center;" rowspan="2">VALOR RECUADO</th>
            </tr>
            <tr>
                <th style="border: solid #000 1px; text-align: center;">FECHA</th>
                <th style="border: solid #000 1px; text-align: center;">TIPO</th>
                <th style="border: solid #000 1px; text-align: center;">NRO</th>
                <th style="border: solid #000 1px; text-align: center;">FECHA</th>
                <th style="border: solid #000 1px; text-align: center;">TIPO</th>
                <th style="border: solid #000 1px; text-align: center;">NRO</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $fechaI = explode("/", $_REQUEST['txtFechaI']);
        $fechaI = "$fechaI[2]-$fechaI[1]-$fechaI[0]";
        $fechaF = explode("/", $_REQUEST['txtFechaF']);
        $fechaF = "$fechaF[2]-$fechaF[1]-$fechaF[0]";
        $data   = $this->pag->listadoRecuados($fechaI, $fechaF, $_REQUEST['sltTipoI'], $_REQUEST['sltTipoF'], $_REQUEST['clase']);
        list($xvtf, $xvtp) = array(0, 0);
        $html   = "";
        foreach ($data as $row){
            $datX = $this->fat->obtenerDetallesFactura($row[4]);
            $xxx  = 0;
            foreach ($datX as $rowX){
                $xxx += (($rowX[7] + $rowX[5] + $rowX[6]) * $rowX[4]);
            }
            $xrec = $this->pag->obtenerTotalPago($row[0]);
            if(!empty($xrec)){
                $html .= "<tr>";
                $html .= "<td style=\"border: solid #000 1px; text-align: left;\">$row[1]</td>";
                $html .= "<td style=\"border: solid #000 1px; text-align: left;\">".utf8_decode($row[2])."</td>";
                $html .= "<td style=\"border: solid #000 1px; text-align: right;\">$row[3]</td>";
                $html .= "<td style=\"border: solid #000 1px; text-align: left;\">$row[5]</td>";
                $html .= "<td style=\"border: solid #000 1px; text-align: left;\">$row[6]</td>";
                $html .= "<td style=\"border: solid #000 1px; text-align: right;\">$row[7]</td>";
                $html .= "<td style=\"border: solid #000 1px; text-align: left;\">$row[8]</td>";
                $html .= "<td style=\"border: solid #000 1px; text-align: left;\">$row[10]</td>";
                $html .= "<td style=\"border: solid #000 1px; text-align: right;\">".number_format($xxx, 2, ',', '.')."</td>";
                $html .= "<td style=\"border: solid #000 1px; text-align: right;\">".number_format($xrec, 2, ',', '.')."</td>";
                $html .= "</tr>";
                $xvtf += $xxx; $xvtp += $xrec;
            }
        }
        echo $html;
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th style="border: solid #000 1px; text-align: center;" colspan="8">TOTALES</th>
                <th style="border: solid #000 1px; text-align: right;"><?php echo number_format($xvtf, 0, ',', ','); ?></th>
                <th style="border: solid #000 1px; text-align: right;"><?php echo number_format($xvtp, 0, ',', ','); ?></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>