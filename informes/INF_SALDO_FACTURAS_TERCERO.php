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
        AND t.id_unico BETWEEN '$terceroI' AND '$terceroF'");
#* Buscar Si la factura es de servicios 
$ts = $con->Listar("SELECT servicio FROM gp_tipo_factura WHERE id_unico = $tipoF");
$servicio = 0;
if($ts[0][0]==1){
    $servicio = 1;
}
if($_GET['t']==1){
    
} else {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Saldo_facturas_tercero.xls");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Facturas Saldo Por Tercero</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <?php if($servicio==1){
            echo '<th colspan="8" align="center">';
            } else {
            echo '<th colspan="6" align="center">';    
            } ?>
            <strong>
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                <br/>&nbsp;FACTURAS CON SALDO POR TERCERO 
                <br/>&nbsp;</strong>
            </th>
            <tr></tr>
            
            <?php 
            if(empty($_REQUEST['num_fac'])){
                $totalc =0;
                for ($i = 0; $i < count($row); $i++) {
                    $tercero = $row[$i][0];
                    $df = $con->Listar("SELECT SUM(df.valor_total_ajustado), 
                        GROUP_CONCAT(df.id_unico) 
                    FROM gp_detalle_factura df 
                    LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                    WHERE f.tercero = $tercero 
                    AND f.fecha_factura <='$fecha' AND f.tipofactura = $tipoF");
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
                        if($servicio==1){
                        echo '<td colspan="8">';
                        } else {
                        echo '<td colspan="6">';    
                        }
                        echo '<br/>&nbsp;<strong>'.ucwords(mb_strtolower($row[$i][1])).' - '.$row[$i][2].'</strong><br/>&nbsp;</td>';
                        echo '</tr>';
                        echo '<tr>';
                        if($servicio==1){
                            echo '<td><strong>FECHA FACTURA</strong></td>';
                            echo '<td><strong>SECTOR</strong></td>';
                            echo '<td><strong>CODIGO RUTA</strong></td>';
                        } else {
                            echo '<td colspan="2"><strong>FECHA FACTURA</strong></td>';
                        }
                        echo '<td><strong>NÚMERO FACTURA</strong></td>';
                        echo '<td><strong>VALOR TOTAL</strong></td>';
                        echo '<td><strong>VALOR RECAUDOS</strong></td>';
                        if($servicio==1){
                            echo '<td><strong>DIAS MORA</strong></td>';
                        }
                        echo '<td><strong>SALDO</strong></td>';

                        echo '</tr>';
                        if($servicio==1){
                            $rowdf = $con->Listar("SELECT f.id_unico, 
                                DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.numero_factura, 
                                SUM(df.valor_total_ajustado),GROUP_CONCAT(df.id_unico), 
                                uv.codigo_ruta, CONCAT_WS(' - ',s.codigo, s.nombre), 
                                f.periodo 
                            FROM gp_detalle_factura df 
                            LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
                            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
                            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                            LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
                            WHERE f.tercero = $tercero 
                            AND f.fecha_factura <='$fecha' AND f.tipofactura = $tipoF 
                            GROUP BY f.id_unico ORDER BY f.unidad_vivienda_servicio, f.fecha_factura");
                        } else {
                            $rowdf = $con->Listar("SELECT f.id_unico, 
                                DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.numero_factura, 
                                SUM(df.valor_total_ajustado),GROUP_CONCAT(df.id_unico)  
                            FROM gp_detalle_factura df 
                            LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                            WHERE f.tercero = $tercero 
                            AND f.fecha_factura <='$fecha' AND f.tipofactura = $tipoF 
                            GROUP BY f.id_unico ORDER BY f.fecha_factura");
                        }
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
                                if($servicio==1){
                                    echo '<td>'.$rowdf[$f][1].'</td>';
                                    echo '<td style="mso-number-format:\@">'.$rowdf[$f][6].'</td>';
                                    echo '<td style="mso-number-format:\@">'.$rowdf[$f][5].'</td>';                                
                                } else {
                                    echo '<td colspan ="2">'.$rowdf[$f][1].'</td>';
                                }
                                echo '<td>'.$rowdf[$f][2].'</td>';
                                echo '<td>'.number_format($valor, 2, '.', ',').'</td>';
                                echo '<td>'.number_format($vr, 2, '.', ',').'</td>';
                                if($servicio==1){
                                    $periodo = $rowdf[$f][7];
                                    $periodo_a= $con->Listar("SELECT * FROM gp_periodo where '$fecha' BETWEEN fecha_inicial and fecha_final");
                                    $dias = diasmora($periodo, $periodo_a[0][0]);
                                    if(empty($dias)){
                                        echo '<td> </td>';
                                    } elseif($dias<0) {
                                        echo '<td> </td>';
                                    }else{
                                        echo '<td>'.$dias.'</td>';
                                    }
                                }
                                echo '<td>'.number_format($saldof, 2, '.', ',').'</td>';
                                echo '</tr>';

                            }
                        }
                        echo '<tr>';
                        if($servicio==1){
                        echo '<td colspan="7">';
                        } else {
                        echo '<td colspan="5">';    
                        }
                        echo '<br/>&nbsp;<strong>Total: '.ucwords(mb_strtolower($row[$i][1])).' - '.$row[$i][2].'</strong><br/>&nbsp;</td>';
                        echo '<td ><br/>&nbsp;<strong>'. number_format($totalst,2,'.',',').'</strong><br/>&nbsp;</td>';
                        echo '</tr>';
                        $totalc += $totalst;
                    }
                }
                echo '<tr>';
                if($servicio==1){
                echo '<td colspan="7">';
                } else {
                echo '<td colspan="5">';    
                }
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
                    WHERE f.tercero = $tercero 
                    AND f.fecha_factura <='$fecha' AND f.tipofactura = $tipoF 
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
                        if($servicio==1){
                        echo '<td colspan="8">';
                        } else {
                        echo '<td colspan="6">';    
                        }
                        echo '<br/>&nbsp;<strong>'.ucwords(mb_strtolower($row[$i][1])).' - '.$row[$i][2].'</strong><br/>&nbsp;</td>';
                        echo '</tr>';
                        echo '<tr>';
                        if($servicio==1){
                            echo '<td><strong>FECHA FACTURA</strong></td>';
                            echo '<td><strong>SECTOR</strong></td>';
                            echo '<td><strong>CODIGO RUTA</strong></td>';
                        } ELSE {
                            echo '<td colspan ="2"><strong>FECHA FACTURA</strong></td>';
                        }
                        echo '<td><strong>NÚMERO FACTURA</strong></td>';
                        echo '<td><strong>VALOR TOTAL</strong></td>';
                        echo '<td><strong>VALOR RECAUDOS</strong></td>';
                        if($servicio==1){
                            echo '<td><strong>DIAS MORA</strong></td>';
                        }
                        echo '<td><strong>SALDO</strong></td>';

                        echo '</tr>';
                        if($servicio==1){
                            $rowdf = $con->Listar("SELECT f.id_unico, 
                                DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.numero_factura, 
                                SUM(df.valor_total_ajustado),GROUP_CONCAT(df.id_unico), 
                                uv.codigo_ruta, CONCAT_WS(' - ',s.codigo, s.nombre), 
                                f.periodo 
                            FROM gp_detalle_factura df 
                            LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
                            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
                            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                            LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
                            WHERE f.tercero = $tercero 
                            AND f.fecha_factura <='$fecha' AND f.tipofactura = $tipoF 
                            GROUP BY f.id_unico ORDER BY f.unidad_vivienda_servicio, f.fecha_factura");
                        } else {
                            $rowdf = $con->Listar("SELECT f.id_unico, 
                                DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.numero_factura, 
                                SUM(df.valor_total_ajustado),GROUP_CONCAT(df.id_unico)  
                            FROM gp_detalle_factura df 
                            LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                            WHERE f.tercero = $tercero 
                            AND f.fecha_factura <='$fecha' AND f.tipofactura = $tipoF 
                            GROUP BY f.id_unico ORDER BY f.fecha_factura");
                        }
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
                                
                                if($servicio==1){
                                    echo '<td>'.$rowdf[$f][1].'</td>';
                                    echo '<td style="mso-number-format:\@">'.$rowdf[$f][6].'</td>';
                                    echo '<td style="mso-number-format:\@">'.$rowdf[$f][5].'</td>';                                
                                } ELSE {
                                    echo '<td colspan="2">'.$rowdf[$f][1].'</td>';
                                }
                                echo '<td>'.$rowdf[$f][2].'</td>';
                                echo '<td>'.number_format($valor, 2, '.', ',').'</td>';
                                echo '<td>'.number_format($vr, 2, '.', ',').'</td>';
                                if($servicio==1){
                                    $periodo = $rowdf[$f][7];
                                    $periodo_a= $con->Listar("SELECT * FROM gp_periodo where '$fecha' BETWEEN fecha_inicial and fecha_final");
                                    $dias = diasmora($periodo, $periodo_a[0][0]);
                                    if(empty($dias)){
                                        echo '<td> </td>';
                                    } elseif($dias<0) {
                                        echo '<td> </td>';
                                    }else{
                                        echo '<td>'.$dias.'</td>';
                                    }
                                }
                                echo '<td>'.number_format($saldof, 2, '.', ',').'</td>';
                                echo '</tr>';

                            }
                        }
                        echo '<tr>';
                        if($servicio==1){
                        echo '<td colspan="7">';
                        } else {
                        echo '<td colspan="5">';    
                        }
                        echo '<br/>&nbsp;<strong>Total: '.ucwords(mb_strtolower($row[$i][1])).' - '.$row[$i][2].'</strong><br/>&nbsp;</td>';
                        echo '<td ><br/>&nbsp;<strong>'. number_format($totalst,2,'.',',').'</strong><br/>&nbsp;</td>';
                        echo '</tr>';
                        $totalc += $totalst;
                    }
                }
                echo '<tr>';
                if($servicio==1){
                echo '<td colspan="7">';
                } else {
                echo '<td colspan="5">';    
                }
                echo '<br/>&nbsp;<strong>TOTAL SALDO </strong><br/>&nbsp;</td>';
                echo '<td ><br/>&nbsp;<strong>'. number_format($totalc,2,'.',',').'</strong><br/>&nbsp;</td>';
                echo '</tr>';
            }
            ?>
        </table>
    </body>
</html>
<?php } ?>