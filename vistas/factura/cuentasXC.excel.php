<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Listado de Cuentas por Cobrar</title>
</head>
<body>
    <table style="width: 100%;">
        <thead>
            <tr>
                <th style="border: solid #000 1px; text-align: center;" colspan="5">
                    <?php echo "$razonsocial<br/>$nombreTipoIden : $numeroIdent<br/>$direccion TELEFONO : $telefono<br/>LISTADO DE CUENTAS POR COBRAR"; ?>
                </th>
            </tr>
            <tr>
                <th style="border: solid #000 1px; text-align: center;">FECHA</th>
                <th style="border: solid #000 1px; text-align: center;">NRO</th>
                <th style="border: solid #000 1px; text-align: center;">VALOR FACTURA</th>
                <th style="border: solid #000 1px; text-align: center;">ABONOS</th>
                <th style="border: solid #000 1px; text-align: center;">SALDO</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $html = "";
            $xDataT = $this->factura->listarTercerosCuentas($_REQUEST['sltClienteI'], $_REQUEST['sltClienteF']);
            if(count($xDataT) > 0){
                list($xtvf, $xtva, $xtvs) = array(0, 0, 0);
                foreach ($xDataT as $rowX){
                    $data = $this->factura->obtenerFacturasCliente($rowX[0], $_REQUEST['sltTipoFI'], $_REQUEST['sltTipoFF']);
                    if(count($data) > 0){
                        list($xrt) = array(0);
                        foreach ($data as $row){/* Verificar data ya que se hizo para validar que los terceros tengan adeudos  */
                            $xpa = $this->factura->VerificarRecaudoFactura($row[0]);
                            $xxx = $this->factura->obtenerValorTotalFactura($row[0]);
                            $xab = $this->factura->buscarAbonosPago($row[0]);
                            $xsl = round($xxx - $xab, 0);
                            if( ($xsl > 0) OR ($xsl < 0) OR ($xsl != 0)){
                                $xrt++;
                            }
                        }
                        if($xrt > 0){
                            $html .= "<tr>";
                            $html .= "<th style='border: solid #000 1px; text-align: left;'>CLIENTE:</th>";
                            $html .= "<th style='border: solid #000 1px; text-align: left;' colspan='2'>$rowX[1]</th>";
                            $html .= "<th style='border: solid #000 1px; text-align: left;' colspan='2'>$rowX[2]</th>";
                            $html .= "</tr>";
                            list($xvf, $xva, $xas) = array(0, 0, 0);
                            foreach ($data as $row){
                                $xpa = $this->factura->VerificarRecaudoFactura($row[0]);
                                $xxx = $this->factura->obtenerValorTotalFactura($row[0]);
                                $xab = $this->factura->buscarAbonosPago($row[0]);
                                $xsl = round($xxx - $xab, 0);
                                if( ($xsl > 0) OR ($xsl < 0) OR ($xsl != 0)){
                                    $xvf += $xxx; $xva += $xab; $xas += $xsl;
                                    $html .= "<tr>";
                                    $html .= "<td style='border: solid #000 1px; text-align: left;'>$row[1]</td>";
                                    $html .= "<td style='border: solid #000 1px; text-align: left;'>$row[2]</td>";
                                    $html .= "<td style='border: solid #000 1px; text-align: right;'>".$xxx."</td>";
                                    $html .= "<td style='border: solid #000 1px; text-align: right;'>".$xab."</td>";
                                    $html .= "<td style='border: solid #000 1px; text-align: right;'>".$xsl."</td>";
                                    $html .= "</tr>";
                                }
                            }
                            $html .= "<tr>";
                            $html .= "<th colspan='2' style='border: solid #000 1px; text-align: center;'>SUBTOTALES</th>";
                            $html .= "<th style='border: solid #000 1px; text-align: right;'>".$xvf."</th>";
                            $html .= "<th style='border: solid #000 1px; text-align: right;'>".$xva."</th>";
                            $html .= "<th style='border: solid #000 1px; text-align: right;'>".$xas."</th>";
                            $html .= "</tr>";
                            $xtvf += $xvf; $xtva += $xva; $xtvs += $xas;
                        }
                    }
                }
            }
            $html .= "<tr>";
            $html .= "<th colspan='2' style='border: solid #000 1px; text-align: center;'>TOTALES</th>";
            $html .= "<th style='border: solid #000 1px; text-align: right;'>".$xtvf."</th>";
            $html .= "<th style='border: solid #000 1px; text-align: right;'>".$xtva."</th>";
            $html .= "<th style='border: solid #000 1px; text-align: right;'>".$xtvs."</th>";
            $html .= "</tr>";
            echo $html;
            ?>
        </tbody>
    </table>
</body>
</html>