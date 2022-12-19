<?php
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#23/01/2019 | Erica G. | Archivo Creado
#####################################################################################

require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require_once("../jsonServicios/funcionesServicios.php");
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];
$nanno  = anno($anno);
#   ************   Datos Recibiod   ************    #
$fecha      = fechaC($_REQUEST['fecha']);
$tipoF      = $_REQUEST['tipoF'];
$terceroI   = $_REQUEST['terceroI'];
$terceroF   = $_REQUEST['terceroF'];

#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6]; 

$row = $con->Listar("SELECT DISTINCT t.id_unico, 
        IF(CONCAT_WS(' ',
             t.nombreuno,
             t.nombredos,
             t.apellidouno,
             t.apellidodos) 
             IS NULL OR CONCAT_WS(' ',
             t.nombreuno,
             t.nombredos,
             t.apellidouno,
             t.apellidodos) = '',
             (t.razonsocial),
             CONCAT_WS(' ',
             t.nombreuno,
             t.nombredos,
             t.apellidouno,
             t.apellidodos)) AS NOMBRE, 
             IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                t.numeroidentificacion, 
                CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
        FROM gp_factura f 
        LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
        WHERE f.fecha_factura <='$fecha' 
        AND f.tipofactura = $tipoF 
        AND t.id_unico BETWEEN '$terceroI' AND '$terceroF' ");

if($_GET['t']==1){
    
} else {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Saldo_factura.xls");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Facturas Saldo</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <?php
            if($_GET['a']==1){
                echo '<th colspan="9" align="center">';
            } elseif($_GET['a']==2) {
                echo '<th colspan="10" align="center">';
            } else {
                echo '<th colspan="7" align="center">';
            }?>
            <strong>
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                <br/>&nbsp;FACTURAS CON SALDO 
                <br/>&nbsp;</strong>
            </th>
            <tr></tr>
            
            <?php 
            if($_GET['a']==1){
                if(empty($_REQUEST['num_fac'])){
                    $totalc =0;
                    for ($i = 0; $i < count($row); $i++) {
                        $tercero = $row[$i][0];
                        $df = $con->Listar("SELECT SUM(df.valor_total_ajustado), 
                            GROUP_CONCAT(df.id_unico) 
                        FROM gp_detalle_factura df 
                        LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                        LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
                        WHERE f.tercero = $tercero 
                        AND p.fecha_cierre <='$fecha' AND f.tipofactura = $tipoF");
                        if(!empty($df[0][1])){
                            $valor = $df[0][0];
                            $ids   = $df[0][1];
                            $rowr  = $con->Listar("SELECT 
                                SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso) 
                                FROM gp_detalle_pago dp 
                                LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                                WHERE dp.detalle_factura In ($ids) AND p.fecha_pago <='$fecha' ");
                            $saldo = $valor -$rowr[0][0];
                        } else {
                            $saldo   = 0;
                        }
                        if($saldo >0){
                            $totalst = 0;
                            echo '<tr>';
                            echo '<td colspan="9">';
                            
                            echo '<br/>&nbsp;<strong>'.ucwords(mb_strtolower($row[$i][1])).' - '.$row[$i][2].'</strong><br/>&nbsp;</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<td><strong>FECHA FACTURA</strong></td>';
                            echo '<td><strong>FECHA LÍMITE</strong></td>';
                            echo '<td><strong>SECTOR</strong></td>';
                            echo '<td><strong>CODIGO RUTA</strong></td>';
                            echo '<td><strong>NÚMERO FACTURA</strong></td>';
                            echo '<td><strong>VALOR TOTAL</strong></td>';
                            echo '<td><strong>VALOR RECAUDOS</strong></td>';
                            echo '<td><strong>DIAS MORA</strong></td>';
                            echo '<td><strong>SALDO</strong></td>';

                            echo '</tr>';
                            $rowdf = $con->Listar("SELECT f.id_unico, 
                                DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.numero_factura, 
                                SUM(df.valor_total_ajustado),GROUP_CONCAT(df.id_unico), 
                                uv.codigo_ruta, CONCAT_WS(' - ',s.codigo, s.nombre), 
                                f.periodo, DATE_FORMAT(p.fecha_cierre, '%d/%m/%Y')    
                            FROM gp_detalle_factura df 
                            LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
                            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
                            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                            LEFT JOIN gp_predio1 pr ON uv.predio = pr.id_unico 
                            LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
                            WHERE f.tercero = $tercero 
                            AND p.fecha_cierre <='$fecha' AND f.tipofactura = $tipoF 
                            GROUP BY f.id_unico ORDER BY f.unidad_vivienda_servicio, f.fecha_factura");
                            
                            for ($f = 0; $f < count($rowdf); $f++) {
                                $vr =0;
                                if(!empty($rowdf[$f][4])){
                                    $valor = $rowdf[$f][3];
                                    $ids   = $rowdf[$f][4];
                                    $rowr  = $con->Listar("SELECT 
                                        SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso) 
                                        FROM gp_detalle_pago dp 
                                        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                                        WHERE dp.detalle_factura In ($ids) AND p.fecha_pago <='$fecha'");
                                    $vr     = $rowr[0][0];
                                    $saldof = $valor -$rowr[0][0];
                                } else {
                                    $saldof = 0;
                                }
                                if($saldof>0){
                                    $totalst += $saldof;
                                    echo '<tr>';
                                    echo '<td>'.$rowdf[$f][1].'</td>';
                                    echo '<td>'.$rowdf[$f][8].'</td>';
                                    echo '<td style="mso-number-format:\@">'.$rowdf[$f][6].'</td>';
                                    echo '<td style="mso-number-format:\@">'.$rowdf[$f][5].'</td>';                                
                                    echo '<td>'.$rowdf[$f][2].'</td>';
                                    echo '<td>'.number_format($valor, 2, '.', ',').'</td>';
                                    echo '<td>'.number_format($vr, 2, '.', ',').'</td>';
                                    
                                    $periodo = $rowdf[$f][7];
                                    $periodo_a= $con->Listar("SELECT * FROM gp_periodo where '$fecha' BETWEEN fecha_inicial and fecha_final");
                                    $dias = diasmora($periodo, $periodo_a[0][0],$fecha);
                                    if(empty($dias)){
                                        echo '<td> </td>';
                                    } elseif($dias<0) {
                                        echo '<td> </td>';
                                    }else{
                                        echo '<td>'.$dias.'</td>';
                                    }
                                    
                                    echo '<td>'.number_format($saldof, 2, '.', ',').'</td>';
                                    echo '</tr>';

                                }
                            }
                            echo '<tr>';
                            echo '<td colspan="8">';
                            echo '<br/>&nbsp;<strong>Total: '.ucwords(mb_strtolower($row[$i][1])).' - '.$row[$i][2].'</strong><br/>&nbsp;</td>';
                            echo '<td ><br/>&nbsp;<strong>'. number_format($totalst,2,'.',',').'</strong><br/>&nbsp;</td>';
                            echo '</tr>';
                            $totalc += $totalst;
                        }
                    }
                    echo '<tr>';
                    echo '<td colspan="8">';
                    echo '<br/>&nbsp;<strong>TOTAL SALDO </strong><br/>&nbsp;</td>';
                    echo '<td ><br/>&nbsp;<strong>'. number_format($totalc,2,'.',',').'</strong><br/>&nbsp;</td>';
                    echo '</tr>';
                } ELSE {
                    $totalc =0;
                    for ($i = 0; $i < count($row); $i++) {
                        $tercero = $row[$i][0];
                        $df = $con->Listar("SELECT SUM(df.valor_total_ajustado), 
                            GROUP_CONCAT(df.id_unico) 
                        FROM gp_detalle_factura df 
                        LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                        LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
                        WHERE f.tercero = $tercero 
                        AND p.fecha_cierre <='$fecha' AND f.tipofactura = $tipoF 
                        GROUP BY df.factura");
                        $num_fac = 0;
                        for ($n = 0; $n < count($df); $n++) {
                            if(!empty($df[$n][1])){
                                $valor = $df[$n][0];
                                $ids   = $df[$n][1];
                                $rowr  = $con->Listar("SELECT 
                                    SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso) 
                                    FROM gp_detalle_pago dp 
                                    LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                                    WHERE dp.detalle_factura In ($ids) AND p.fecha_pago <='$fecha' ");
                                $saldo = $valor -$rowr[0][0];
                            } else {
                                $saldo   = 0;
                            }
                            if($saldo >0){
                                $num_fac +=1;
                            }
                        }
                        if($num_fac >=$_REQUEST['num_fac']){
                            $totalst = 0;
                            echo '<tr>';
                            echo '<td colspan="9">';
                            echo '<br/>&nbsp;<strong>'.ucwords(mb_strtolower($row[$i][1])).' - '.$row[$i][2].'</strong><br/>&nbsp;</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<td><strong>FECHA FACTURA</strong></td>';
                            echo '<td><strong>FECHA LÍMITE</strong></td>';
                            echo '<td><strong>SECTOR</strong></td>';
                            echo '<td><strong>CODIGO RUTA</strong></td>';                            
                            echo '<td><strong>NÚMERO FACTURA</strong></td>';
                            echo '<td><strong>VALOR TOTAL</strong></td>';
                            echo '<td><strong>VALOR RECAUDOS</strong></td>';
                            echo '<td><strong>DIAS MORA</strong></td>';
                            echo '<td><strong>SALDO</strong></td>';

                            echo '</tr>';
                            $rowdf = $con->Listar("SELECT f.id_unico, 
                                DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.numero_factura, 
                                SUM(df.valor_total_ajustado),GROUP_CONCAT(df.id_unico), 
                                uv.codigo_ruta, CONCAT_WS(' - ',s.codigo, s.nombre), 
                                f.periodo, DATE_FORMAT(p.fecha_cierre, '%d/%m/%Y') 
                            FROM gp_detalle_factura df 
                            LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
                            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
                            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                            LEFT JOIN gp_predio1 pr ON uv.predio = pr.id_unico 
                            LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
                            WHERE f.tercero = $tercero 
                            AND p.fecha_cierre <='$fecha' AND f.tipofactura = $tipoF 
                            GROUP BY f.id_unico ORDER BY f.unidad_vivienda_servicio, f.fecha_factura");
                            
                            for ($f = 0; $f < count($rowdf); $f++) {
                                $vr =0;
                                if(!empty($rowdf[$f][4])){
                                    $valor = $rowdf[$f][3];
                                    $ids   = $rowdf[$f][4];
                                    $rowr  = $con->Listar("SELECT 
                                        SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso) 
                                        FROM gp_detalle_pago dp 
                                        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                                        WHERE dp.detalle_factura In ($ids) AND p.fecha_pago <='$fecha'");
                                    $vr     = $rowr[0][0];
                                    $saldof = $valor -$rowr[0][0];
                                } else {
                                    $saldof = 0;
                                }
                                if($saldof>0){
                                    $totalst += $saldof;
                                    echo '<tr>';
                                    echo '<td>'.$rowdf[$f][1].'</td>';
                                    echo '<td>'.$rowdf[$f][8].'</td>';
                                    echo '<td style="mso-number-format:\@">'.$rowdf[$f][6].'</td>';
                                    echo '<td style="mso-number-format:\@">'.$rowdf[$f][5].'</td>';                                
                                    echo '<td>'.$rowdf[$f][2].'</td>';
                                    echo '<td>'.number_format($valor, 2, '.', ',').'</td>';
                                    echo '<td>'.number_format($vr, 2, '.', ',').'</td>';
                                    
                                    $periodo = $rowdf[$f][7];
                                    $periodo_a= $con->Listar("SELECT * FROM gp_periodo where '$fecha' BETWEEN fecha_inicial and fecha_final");
                                    $dias = diasmora($periodo, $periodo_a[0][0], $fecha);
                                    if(empty($dias)){
                                        echo '<td> </td>';
                                    } elseif($dias<0) {
                                        echo '<td> </td>';
                                    }else{
                                        echo '<td>'.$dias.'</td>';
                                    }
                                    
                                    echo '<td>'.number_format($saldof, 2, '.', ',').'</td>';
                                    echo '</tr>';

                                }
                            }
                            echo '<tr>';
                            echo '<td colspan="8">';
                            echo '<br/>&nbsp;<strong>Total: '.ucwords(mb_strtolower($row[$i][1])).' - '.$row[$i][2].'</strong><br/>&nbsp;</td>';
                            echo '<td ><br/>&nbsp;<strong>'. number_format($totalst,2,'.',',').'</strong><br/>&nbsp;</td>';
                            echo '</tr>';
                            $totalc += $totalst;
                        }
                    }
                    echo '<tr>';
                    echo '<td colspan="8">';
                    echo '<br/>&nbsp;<strong>TOTAL SALDO </strong><br/>&nbsp;</td>';
                    echo '<td ><br/>&nbsp;<strong>'. number_format($totalc,2,'.',',').'</strong><br/>&nbsp;</td>';
                    echo '</tr>';
                }
            } elseif($_GET['a']==2) {
                echo '<tr>';
                echo '<td><strong>TERCERO</strong></td>';
                echo '<td><strong>FECHA FACTURA</strong></td>';
                echo '<td><strong>FECHA LÍMITE PAGO</strong></td>';
                echo '<td><strong>SECTOR</strong></td>';
                echo '<td><strong>CODIGO RUTA</strong></td>';
                echo '<td><strong>NÚMERO FACTURA</strong></td>';
                echo '<td><strong>VALOR TOTAL</strong></td>';
                echo '<td><strong>VALOR RECAUDOS</strong></td>';
                echo '<td><strong>DIAS MORA</strong></td>';
                echo '<td><strong>SALDO</strong></td>';
                echo '</tr>';
                if(empty($_REQUEST['num_fac'])){
                    $totalc =0;
                    for ($i = 0; $i < count($row); $i++) {
                        $tercero = $row[$i][0];
                        $df = $con->Listar("SELECT SUM(df.valor_total_ajustado), 
                            GROUP_CONCAT(df.id_unico) 
                        FROM gp_detalle_factura df 
                        LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                        LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
                        WHERE f.tercero = $tercero 
                        AND p.fecha_cierre <='$fecha' AND f.tipofactura = $tipoF");
                        if(!empty($df[0][1])){
                            $valor = $df[0][0];
                            $ids   = $df[0][1];
                            $rowr  = $con->Listar("SELECT 
                                SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso) 
                                FROM gp_detalle_pago dp 
                                LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                                WHERE dp.detalle_factura In ($ids) AND p.fecha_pago <='$fecha' ");
                            $saldo = $valor -$rowr[0][0];
                        } else {
                            $saldo   = 0;
                        }
                        if($saldo >0){
                            $totalst = 0;
                            $rowdf = $con->Listar("SELECT f.id_unico, 
                                DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.numero_factura, 
                                SUM(df.valor_total_ajustado),GROUP_CONCAT(df.id_unico), 
                                uv.codigo_ruta, CONCAT_WS(' - ',s.codigo, s.nombre), 
                                f.periodo, DATE_FORMAT(p.fecha_cierre, '%d/%m/%Y') 
                            FROM gp_detalle_factura df 
                            LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
                            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
                            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                            LEFT JOIN gp_predio1 pr ON uv.predio = pr.id_unico 
                            LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
                            WHERE f.tercero = $tercero 
                            AND p.fecha_cierre <='$fecha' AND f.tipofactura = $tipoF 
                            GROUP BY f.id_unico ORDER BY f.unidad_vivienda_servicio, f.fecha_factura");
                            
                            for ($f = 0; $f < count($rowdf); $f++) {
                                $vr =0;
                                if(!empty($rowdf[$f][4])){
                                    $valor = $rowdf[$f][3];
                                    $ids   = $rowdf[$f][4];
                                    $rowr  = $con->Listar("SELECT 
                                        SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso) 
                                        FROM gp_detalle_pago dp 
                                        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                                        WHERE dp.detalle_factura In ($ids) AND p.fecha_pago <='$fecha'");
                                    $vr     = $rowr[0][0];
                                    $saldof = $valor -$rowr[0][0];
                                } else {
                                    $saldof = 0;
                                }
                                if($saldof>0){
                                    $totalst += $saldof;
                                    echo '<td>';
                                    echo ucwords(mb_strtolower($row[$i][1])).' - '.$row[$i][2];
                                    echo '</td>';
                                    echo '<td>'.$rowdf[$f][1].'</td>';
                                    echo '<td>'.$rowdf[$f][8].'</td>';
                                    echo '<td style="mso-number-format:\@">'.$rowdf[$f][6].'</td>';
                                    echo '<td style="mso-number-format:\@">'.$rowdf[$f][5].'</td>';                                
                                    echo '<td>'.$rowdf[$f][2].'</td>';
                                    echo '<td>'.number_format($valor, 2, '.', ',').'</td>';
                                    echo '<td>'.number_format($vr, 2, '.', ',').'</td>';
                                    
                                    $periodo = $rowdf[$f][7];
                                    $periodo_a= $con->Listar("SELECT * FROM gp_periodo where '$fecha' BETWEEN fecha_inicial and fecha_final");
                                    $dias = diasmora($periodo, $periodo_a[0][0],$fecha);
                                    if(empty($dias)){
                                        echo '<td> </td>';
                                    } elseif($dias<0) {
                                        echo '<td> </td>';
                                    }else{
                                        echo '<td>'.$dias.'</td>';
                                    }
                                    echo '<td>'.number_format($saldof, 2, '.', ',').'</td>';
                                    echo '</tr>';

                                }
                            }
                            $totalc += $totalst;
                        }
                    }
                    echo '<tr>';
                    echo '<td colspan="9">';
                    echo '<br/>&nbsp;<strong>TOTAL SALDO </strong><br/>&nbsp;</td>';
                    echo '<td ><br/>&nbsp;<strong>'. number_format($totalc,2,'.',',').'</strong><br/>&nbsp;</td>';
                    echo '</tr>';
                } else {
                    $totalc =0;
                    for ($i = 0; $i < count($row); $i++) {
                        $tercero = $row[$i][0];
                        $df = $con->Listar("SELECT SUM(df.valor_total_ajustado), 
                            GROUP_CONCAT(df.id_unico) 
                        FROM gp_detalle_factura df 
                        LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                        LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
                        WHERE f.tercero = $tercero 
                        AND p.fecha_cierre <='$fecha' AND f.tipofactura = $tipoF 
                        GROUP BY df.factura");
                        $num_fac = 0;
                        for ($n = 0; $n < count($df); $n++) {
                            if(!empty($df[$n][1])){
                                $valor = $df[$n][0];
                                $ids   = $df[$n][1];
                                $rowr  = $con->Listar("SELECT 
                                    SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso) 
                                    FROM gp_detalle_pago dp 
                                    LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                                    WHERE dp.detalle_factura In ($ids) AND p.fecha_pago <='$fecha' ");
                                $saldo = $valor -$rowr[0][0];
                            } else {
                                $saldo   = 0;
                            }
                            if($saldo >0){
                                $num_fac +=1;
                            }
                        }
                        if($num_fac >=$_REQUEST['num_fac']){
                            $totalst = 0;
                            $rowdf = $con->Listar("SELECT f.id_unico, 
                                DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.numero_factura, 
                                SUM(df.valor_total_ajustado),GROUP_CONCAT(df.id_unico), 
                                uv.codigo_ruta, CONCAT_WS(' - ',s.codigo, s.nombre), 
                                f.periodo , p.fecha_cierre 
                            FROM gp_detalle_factura df 
                            LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
                            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
                            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                            LEFT JOIN gp_predio1 pr ON uv.predio = pr.id_unico 
                            LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
                            WHERE f.tercero = $tercero 
                            AND p.fecha_cierre <='$fecha' AND f.tipofactura = $tipoF 
                            GROUP BY f.id_unico ORDER BY f.unidad_vivienda_servicio, f.fecha_factura");
                           
                            for ($f = 0; $f < count($rowdf); $f++) {
                                $vr =0;
                                if(!empty($rowdf[$f][4])){
                                    $valor = $rowdf[$f][3];
                                    $ids   = $rowdf[$f][4];
                                    $rowr  = $con->Listar("SELECT 
                                        SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso) 
                                        FROM gp_detalle_pago dp 
                                        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                                        WHERE dp.detalle_factura In ($ids) AND p.fecha_pago <='$fecha'");
                                    $vr     = $rowr[0][0];
                                    $saldof = $valor -$rowr[0][0];
                                } else {
                                    $saldof = 0;
                                }
                                if($saldof>0){
                                    $totalst += $saldof;
                                    echo '<tr>';
                                    echo '<td>'.ucwords(mb_strtolower($row[$i][1])).' - '.$row[$i][2].'</td>';
                                    echo '<td>'.$rowdf[$f][1].'</td>';
                                    echo '<td>'.$rowdf[$f][8].'</td>';
                                    echo '<td style="mso-number-format:\@">'.$rowdf[$f][6].'</td>';
                                    echo '<td style="mso-number-format:\@">'.$rowdf[$f][5].'</td>';                                
                                    echo '<td>'.$rowdf[$f][2].'</td>';
                                    echo '<td>'.number_format($valor, 2, '.', ',').'</td>';
                                    echo '<td>'.number_format($vr, 2, '.', ',').'</td>';
                                    $periodo = $rowdf[$f][7];
                                    $periodo_a= $con->Listar("SELECT * FROM gp_periodo where '$fecha' BETWEEN fecha_inicial and fecha_final");
                                    $dias = diasmora($periodo, $periodo_a[0][0],$fecha);
                                    if(empty($dias)){
                                        echo '<td> </td>';
                                    } elseif($dias<0) {
                                        echo '<td> </td>';
                                    }else{
                                        echo '<td>'.$dias.'</td>';
                                    }                                    
                                    echo '<td>'.number_format($saldof, 2, '.', ',').'</td>';
                                    echo '</tr>';

                                }
                            }
                            $totalc += $totalst;
                        }
                    }
                    echo '<tr>';
                    echo '<td colspan="9">';
                    echo '<br/>&nbsp;<strong>TOTAL SALDO </strong><br/>&nbsp;</td>';
                    echo '<td ><br/>&nbsp;<strong>'. number_format($totalc,2,'.',',').'</strong><br/>&nbsp;</td>';
                    echo '</tr>';
                }  
            } else {
                echo '<tr>';
                echo '<td><strong>TERCERO</strong></td>';
                echo '<td><strong>SECTOR</strong></td>';
                echo '<td><strong>CODIGO RUTA</strong></td>';
                echo '<td><strong>VALOR TOTAL</strong></td>';
                echo '<td><strong>VALOR RECAUDOS</strong></td>';
                echo '<td><strong>DIAS MORA</strong></td>';
                echo '<td><strong>SALDO</strong></td>';
                echo '</tr>';
                if(empty($_REQUEST['num_fac'])){
                    $totalc =0;
                    for ($i = 0; $i < count($row); $i++) {
                        $tercero = $row[$i][0];
                        $dfv = $con->Listar("SELECT SUM(df.valor_total_ajustado), 
                            GROUP_CONCAT(df.id_unico), 
                            uvms.unidad_vivienda_servicio 
                        FROM gp_detalle_factura df 
                        LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                        LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
                        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
                        WHERE f.tercero = $tercero 
                        AND p.fecha_cierre <='$fecha' AND f.tipofactura = $tipoF 
                        GROUP BY uvms.unidad_vivienda_servicio");
                        for ($fv = 0; $fv < count($dfv); $fv++) {
                            $id_unidad_viviendas =$dfv[$fv][2];
                            if(!empty($dfv[$fv][1])){
                                $valor = $dfv[$fv][0];
                                $ids   = $dfv[$fv][1];
                                $rowr  = $con->Listar("SELECT 
                                    SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso) 
                                    FROM gp_detalle_pago dp 
                                    LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                                    WHERE dp.detalle_factura In ($ids) AND p.fecha_pago <='$fecha' ");
                                $saldo = $valor -$rowr[0][0];
                            } else {
                                $saldo   = 0;
                            }
                            if($saldo >0){
                                $totalst = 0;
                                $totalv  = 0;
                                $totalvr = 0;
                                $totald  = 0;
                                $rowdf = $con->Listar("SELECT f.id_unico, 
                                    DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.numero_factura, 
                                    SUM(df.valor_total_ajustado),GROUP_CONCAT(df.id_unico), 
                                    uv.codigo_ruta, CONCAT_WS(' - ',s.codigo, s.nombre), 
                                    f.periodo , p.fecha_cierre 
                                    FROM gp_detalle_factura df 
                                    LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                                    LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
                                    LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                                    LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
                                    LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                                    LEFT JOIN gp_predio1 pr ON uv.predio = pr.id_unico 
                                    LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
                                    WHERE f.tercero = $tercero 
                                    AND uvs.id_unico = $id_unidad_viviendas 
                                    AND p.fecha_cierre <='$fecha' AND f.tipofactura = $tipoF 
                                    GROUP BY f.id_unico ORDER BY f.unidad_vivienda_servicio, f.fecha_factura");
                                $dm = 0;
                                for ($f = 0; $f < count($rowdf); $f++) {
                                    $vr =0;
                                    if(!empty($rowdf[$f][4])){
                                        $valor = $rowdf[$f][3];
                                        $ids   = $rowdf[$f][4];
                                        $rowr  = $con->Listar("SELECT 
                                            SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso) 
                                            FROM gp_detalle_pago dp 
                                            LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                                            WHERE dp.detalle_factura In ($ids) AND p.fecha_pago <='$fecha'");
                                        $vr     = $rowr[0][0];
                                        $saldof = $valor -$rowr[0][0];
                                    } else {
                                        $saldof = 0;
                                    }
                                    if($saldof>0){
                                        $totalst += $saldof;
                                        $totalv  += $valor;
                                        $totalvr += $vr;
                                        $periodo = $rowdf[$f][7];
                                        $periodo_a= $con->Listar("SELECT * FROM gp_periodo where '$fecha' BETWEEN fecha_inicial and fecha_final");
                                        $dias = diasmora($periodo, $periodo_a[0][0], $fecha);
                                        if(empty($dias)){
                                        } elseif($dias<0) {
                                        }else{
                                            $dm = max($dm, $dias);
                                            //$totald  += $dias; 
                                        }
                                    }
                                }
                                $totald = $dm;
                                if($totalst>0){ 
                                    echo '<td>';
                                    echo ucwords(mb_strtolower($row[$i][1])).' - '.$row[$i][2];
                                    echo '</td>';
                                    echo '<td>'.$rowdf[0][5].'</td>';
                                    echo '<td>'.$rowdf[0][6].'</td>';
                                    echo '<td>'.number_format($totalv, 2, '.', ',').'</td>';
                                    echo '<td>'.number_format($totalvr, 2, '.', ',').'</td>';
                                    echo '<td>'.$totald.'</td>';
                                    echo '<td>'.number_format($totalst, 2, '.', ',').'</td>';
                                    echo '</tr>';
                                }
                                $totalc += $totalst;
                            }
                        }
                    }
                    echo '<tr>';
                    echo '<td colspan="6">';
                    echo '<br/>&nbsp;<strong>TOTAL SALDO </strong><br/>&nbsp;</td>';
                    echo '<td ><br/>&nbsp;<strong>'. number_format($totalc,2,'.',',').'</strong><br/>&nbsp;</td>';
                    echo '</tr>';
                } ELSE {
                    $totalc =0;
                    for ($i = 0; $i < count($row); $i++) {
                        $tercero = $row[$i][0];
                        $df = $con->Listar("SELECT SUM(df.valor_total_ajustado), 
                            GROUP_CONCAT(df.id_unico) 
                        FROM gp_detalle_factura df 
                        LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                        LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
                        WHERE f.tercero = $tercero 
                        AND p.fecha_cierre <='$fecha' AND f.tipofactura = $tipoF 
                        GROUP BY df.factura");
                        $num_fac = 0;
                        for ($n = 0; $n < count($df); $n++) {
                            if(!empty($df[$n][1])){
                                $valor = $df[$n][0];
                                $ids   = $df[$n][1];
                                $rowr  = $con->Listar("SELECT 
                                    SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso) 
                                    FROM gp_detalle_pago dp 
                                    LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                                    WHERE dp.detalle_factura In ($ids) AND p.fecha_pago <='$fecha' ");
                                $saldo = $valor -$rowr[0][0];
                            } else {
                                $saldo   = 0;
                            }
                            if($saldo >0){
                                $num_fac +=1;
                            }
                        }
                        if($num_fac >= $_REQUEST['num_fac']){ 
                            $tercero = $row[$i][0];
                            $dfv = $con->Listar("SELECT SUM(df.valor_total_ajustado), 
                                GROUP_CONCAT(df.id_unico), 
                                uvms.unidad_vivienda_servicio 
                            FROM gp_detalle_factura df 
                            LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                            LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
                            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
                            WHERE f.tercero = $tercero 
                            AND p.fecha_cierre <='$fecha' AND f.tipofactura = $tipoF 
                            GROUP BY uvms.unidad_vivienda_servicio");
                            for ($fv = 0; $fv < count($dfv); $fv++) {
                                $id_unidad_viviendas =$dfv[$fv][2];
                                if(!empty($dfv[$fv][1])){
                                    $valor = $dfv[$fv][0];
                                    $ids   = $dfv[$fv][1];
                                    $rowr  = $con->Listar("SELECT 
                                        SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso) 
                                        FROM gp_detalle_pago dp 
                                        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                                        WHERE dp.detalle_factura In ($ids) AND p.fecha_pago <='$fecha' ");
                                    $saldo = $valor -$rowr[0][0];
                                } else {
                                    $saldo   = 0;
                                }
                                if($saldo >0){
                                    $totalst = 0;
                                    $totalv  = 0;
                                    $totalvr = 0;
                                    $totald  = 0;
                                    $rowdf = $con->Listar("SELECT f.id_unico, 
                                        DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.numero_factura, 
                                        SUM(df.valor_total_ajustado),GROUP_CONCAT(df.id_unico), 
                                        uv.codigo_ruta, CONCAT_WS(' - ',s.codigo, s.nombre), 
                                        f.periodo , p.fecha_cierre 
                                        FROM gp_detalle_factura df 
                                        LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                                        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
                                        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                                        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
                                        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                                        LEFT JOIN gp_predio1 pr ON uv.predio = pr.id_unico 
                                        LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
                                        WHERE f.tercero = $tercero 
                                        AND uvs.id_unico = $id_unidad_viviendas 
                                        AND p.fecha_cierre <='$fecha' AND f.tipofactura = $tipoF 
                                        GROUP BY f.id_unico ORDER BY f.unidad_vivienda_servicio, f.fecha_factura");

                                    for ($f = 0; $f < count($rowdf); $f++) {
                                        $vr =0;
                                        if(!empty($rowdf[$f][4])){
                                            $valor = $rowdf[$f][3];
                                            $ids   = $rowdf[$f][4];
                                            $rowr  = $con->Listar("SELECT 
                                                SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso) 
                                                FROM gp_detalle_pago dp 
                                                LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                                                WHERE dp.detalle_factura In ($ids) AND p.fecha_pago <='$fecha'");
                                            $vr     = $rowr[0][0];
                                            $saldof = $valor -$rowr[0][0];
                                        } else {
                                            $saldof = 0;
                                        }
                                        if($saldof>0){
                                            $totalst += $saldof;
                                            $totalv  += $valor;
                                            $totalvr += $vr;
                                            $periodo = $rowdf[$f][7];
                                            $periodo_a= $con->Listar("SELECT * FROM gp_periodo where '$fecha' BETWEEN fecha_inicial and fecha_final");
                                            $dias = diasmora($periodo, $periodo_a[0][0], $fecha);
                                            if(empty($dias)){
                                            } elseif($dias<0) {
                                            }else{
                                                $totald  += $dias; 
                                            }
                                        }
                                    }
                                    if($totalst>0){ 
                                        echo '<td>';
                                        echo ucwords(mb_strtolower($row[$i][1])).' - '.$row[$i][2];
                                        echo '</td>';
                                        echo '<td>'.$rowdf[0][5].'</td>';
                                        echo '<td>'.$rowdf[0][6].'</td>';
                                        echo '<td>'.number_format($totalv, 2, '.', ',').'</td>';
                                        echo '<td>'.number_format($totalvr, 2, '.', ',').'</td>';
                                        echo '<td>'.$totald.'</td>';
                                        echo '<td>'.number_format($totalst, 2, '.', ',').'</td>';
                                        echo '</tr>';
                                    }
                                    $totalc += $totalst;
                                }
                            }
                        }
                    }
                    echo '<tr>';
                    echo '<td colspan="6">';
                    echo '<br/>&nbsp;<strong>TOTAL SALDO </strong><br/>&nbsp;</td>';
                    echo '<td ><br/>&nbsp;<strong>'. number_format($totalc,2,'.',',').'</strong><br/>&nbsp;</td>';
                    echo '</tr>';
                }  
            }
            
            ?>
        </table>
    </body>
</html>
<?php } ?>