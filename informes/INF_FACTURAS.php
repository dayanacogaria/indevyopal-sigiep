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
#   ************   Datos Recibios   ************    #
$ti         = $_REQUEST['ti'];
$tipo       = $_REQUEST['tipo'];
$fechaI     = fechaC($_REQUEST['fechaI']);
$fechaF     = fechaC($_REQUEST['fechaF']);

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
$titulo      = '';
$n_c         = 0;
$html        ='';
$tls         = array();
$tli         = array();
$tlm         = array();
if($ti==1){
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Facturacion_Por_Concepto.xls");
    $titulo  = 'INFORME DE FACTURACIÓN POR CONCEPTO';
    #* Buscar el N° de Celdas
    $nc = $con->Listar("SELECT DISTINCT c.id_unico , c.tipo_concepto, c.nombre ,
        SUM(df.iva), SUM(df.impoconsumo)
        FROM gp_factura f 
        LEFT JOIN gp_detalle_factura df ON f.id_unico = df.factura 
        LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
        WHERE f.fecha_factura BETWEEN '$fechaI' AND '$fechaF' 
        AND f.tipofactura = $tipo 
        AND  c.id_unico IS NOT NULL 
        GROUP BY c.id_unico 
        ORDER BY c.id_unico ");
    if(!empty($nc[0][0])){
        $n_c = count($nc)+5;
        $html .= '<tr>';
        $html .= '<td><strong>TIPO FACTURA</strong></td>';
        $html .= '<td><strong>NÚMERO</strong></td>';
        $html .= '<td><strong>FECHA</strong></td>';
        $html .= '<td><strong>TERCERO</strong></td>';
        for ($i = 0;$i < count($nc);$i++) {
            $html .= '<td><strong>'. mb_strtoupper($nc[$i][2]).'</strong></td>';
            if($nc[$i][3]!=0){
                $html .= '<td><strong>IVA</strong></td>';
                $n_c   = $n_c+1;
            }
            if($nc[$i][4]!=0){
                $html .= '<td><strong>IMPOCONSUMO</strong></td>';
                $n_c   = $n_c+1;
            }
        }
        $html .= '<td><strong>TOTAL</strong></td>';
        $html .= '</tr>';
        $row = $con->Listar("SELECT DISTINCT f.id_unico, tf.nombre, 
            f.numero_factura, DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), 
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
        LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
        LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
        WHERE f.fecha_factura BETWEEN '$fechaI' AND '$fechaF' 
        AND f.tipofactura = $tipo 
        ORDER BY f.fecha_factura, f.numero_factura");
        $ttf = 0;
        for ($f = 0;$f < count($row);$f++) {
            $tf = 0;
            $factura = $row[$f][0];
            $html .= '<tr>';
            $html .= '<td>'.$row[$f][1].'</td>';
            $html .= '<td>'.$row[$f][2].'</td>';
            $html .= '<td>'.$row[$f][3].'</td>';
            $html .= '<td>'.$row[$f][4].' - '.$row[$f][5].'</td>';
            
            for ($i = 0;$i < count($nc);$i++) {
                $id_c = $nc[$i][0];
                $rowd = $con->Listar("SELECT SUM(valor * cantidad)
                    FROM gp_detalle_factura 
                    WHERE concepto_tarifa = $id_c AND factura = $factura ");
                $html .= '<td> '.number_format($rowd[0][0], 2, '.', ',').'</td>';        
                $tf +=$rowd[0][0];
                $tls[$i]+=$rowd[0][0];
                if($nc[$i][3]!=0){
                    $rowi = $con->Listar("SELECT SUM(iva)
                    FROM gp_detalle_factura 
                    WHERE concepto_tarifa = $id_c AND factura = $factura ");
                    $html .= '<td> '.number_format($rowi[0][0], 2, '.', ',').'</td>';        
                    $tf +=$rowi[0][0];
                    $tli[$i]+=$rowi[0][0];
                }
                if($nc[$i][4]!=0){
                    $rowm = $con->Listar("SELECT SUM(iva)
                    FROM gp_detalle_factura 
                    WHERE concepto_tarifa = $id_c AND factura = $factura ");
                    $html .= '<td> '.number_format($rowm[0][0], 2, '.', ',').'</td>';   
                    $tf +=$rowm[0][0];
                    $tlm[$i]+=$rowm[0][0];
                }
            }
            $html .= '<td> '.number_format($tf, 2, '.', ',').'</td>';        
            $html .= '</tr>';
            $ttf  += $tf;
        }
        $html .= '<tr>';
        $html .= '<td colspan ="4"><strong>TOTALES</strong></td>';
        for ($i = 0;$i < count($nc);$i++) {
            $html .= '<td><strong>'.number_format($tls[$i], 2, '.', ',').'</strong></td>';        
            if($nc[$i][3]!=0){
                $html .= '<td><strong>'.number_format($tli[$i], 2, '.', ',').'</strong></td>';        
            }
            if($nc[$i][4]!=0){
                $html .= '<td><strong>'.number_format($tlm[$i], 2, '.', ',').'</strong></td>';        
            }
        }
        $html .= '<td><strong>'.number_format($ttf, 2, '.', ',').'</strong></td>';        
        $html .= '</tr>';
    }
} elseif($ti==2) {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Recaudo_Por_Concepto.xls");
    $titulo  = 'INFORME DE RECAUDO POR CONCEPTO';
    #* Buscar el N° de Celdas
    $nc = $con->Listar("SELECT DISTINCT c.id_unico , c.tipo_concepto, c.nombre ,
        SUM(dp.iva), SUM(dp.impoconsumo)  
        FROM gp_pago p 
        LEFT JOIN gp_detalle_pago dp ON dp.pago = p.id_unico 
        LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
        LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
        WHERE p.fecha_pago BETWEEN '$fechaI' AND '$fechaF' 
        AND p.tipo_pago = $tipo AND c.id_unico IS NOT NULL 
        AND  c.id_unico IS NOT NULL 
        GROUP BY c.id_unico 
        ORDER BY c.id_unico ");
    if(!empty($nc[0][0])){
        $n_c = count($nc)+5;
        $html .= '<tr>';
        $html .= '<td><strong>TIPO PAGO</strong></td>';
        $html .= '<td><strong>NÚMERO</strong></td>';
        $html .= '<td><strong>FECHA</strong></td>';
        $html .= '<td><strong>TERCERO</strong></td>';
        for ($i = 0;$i < count($nc);$i++) {
            $html .= '<td><strong>'. mb_strtoupper($nc[$i][2]).'</strong></td>';
            if($nc[$i][3]!=0){
                $html .= '<td><strong>IVA</strong></td>';
                $n_c   = $n_c+1;
            }
            if($nc[$i][4]!=0){
                $html .= '<td><strong>IMPOCONSUMO</strong></td>';
                $n_c   = $n_c+1;
            }
        }
        $html .= '<td><strong>TOTAL</strong></td>';
        $html .= '</tr>';
        $row = $con->Listar("SELECT DISTINCT p.id_unico, tp.nombre, 
            p.numero_pago, DATE_FORMAT(p.fecha_pago, '%d/%m/%Y'), 
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
        FROM gp_pago p 
        LEFT JOIN gp_tipo_pago tp ON p.tipo_pago = tp.id_unico 
        LEFT JOIN gf_tercero t ON p.responsable = t.id_unico 
        WHERE p.fecha_pago BETWEEN '$fechaI' AND '$fechaF' 
        AND p.tipo_pago = $tipo 
        ORDER BY p.fecha_pago, p.numero_pago");
        $ttp = 0;
        for ($f = 0;$f < count($row);$f++) {
            $tp = 0;
            $pago = $row[$f][0];
            $html .= '<tr>';
            $html .= '<td>'.$row[$f][1].'</td>';
            $html .= '<td>'.$row[$f][2].'</td>';
            $html .= '<td>'.$row[$f][3].'</td>';
            $html .= '<td>'.$row[$f][4].' - '.$row[$f][5].'</td>';
            for ($i = 0;$i < count($nc);$i++) {
                $id_c = $nc[$i][0];
                $rowd = $con->Listar("SELECT SUM(dp.valor) + SUM(dp.ajuste_peso)
                    FROM gp_detalle_pago dp 
                    LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
                    WHERE df.concepto_tarifa = $id_c AND dp.pago = $pago ");
                $html .= '<td>'.number_format($rowd[0][0], 2, '.', ',').'</td>';        
                $tp += $rowd[0][0];
                $tls[$i] += $rowd[0][0];
                if($nc[$i][3]!=0){
                    $rowi = $con->Listar("SELECT SUM(dp.iva)   
                    FROM gp_detalle_pago dp 
                    LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
                    WHERE df.concepto_tarifa = $id_c AND dp.pago = $pago ");
                    
                    $html .= '<td>'.number_format($rowi[0][0], 2, '.', ',').'</td>';        
                    $tp     += $rowi[0][0];
                    $tli[$i]+= $rowi[0][0];
                }
                if($nc[$i][4]!=0){
                    $rowm = $con->Listar("SELECT  SUM(dp.impoconsumo)   
                    FROM gp_detalle_pago dp 
                    LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
                    WHERE df.concepto_tarifa = $id_c AND dp.pago = $pago ");
                    $html .= '<td>'.number_format($rowm[0][0], 2, '.', ',').'</td>';        
                    $tp     += $rowm[0][0];
                    $tlm[$i]+= $rowm[0][0];
                }
            }
            $html .= '<td>'.number_format($tp, 2, '.', ',').'</td>';        
            $html .= '</tr>';
            $ttp += $tp;
        }   
        $html .= '<tr>';
        $html .= '<td colspan ="4"><strong>TOTALES</strong></td>';
        for ($i = 0;$i < count($nc);$i++) {
            $html .= '<td><strong>'.number_format($tls[$i], 2, '.', ',').'</strong></td>';        
            if($nc[$i][3]!=0){
                $html .= '<td><strong>'.number_format($tli[$i], 2, '.', ',').'</strong></td>';        
            }
            if($nc[$i][4]!=0){
                $html .= '<td><strong>'.number_format($tlm[$i], 2, '.', ',').'</strong></td>';        
            }
        }
        $html .= '<td><strong>'.number_format($ttp, 2, '.', ',').'</strong></td>';        
        $html .= '</tr>';
    }
} elseif($ti==3) {
    $acumulado = $_REQUEST['tercero'];
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Cartera_Concepto.xls");
    $titulo  = 'INFORME DE CARTERA POR CONCEPTO';
    #* Buscar el N° de Celdas
    $nc = $con->Listar("SELECT DISTINCT c.id_unico , c.tipo_concepto, c.nombre ,
        SUM(df.iva), SUM(df.impoconsumo)
        FROM gp_factura f 
        LEFT JOIN gp_detalle_factura df ON f.id_unico = df.factura 
        LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
        WHERE f.fecha_factura <= '$fechaF' 
        AND f.tipofactura = $tipo 
        AND  c.id_unico IS NOT NULL 
        AND df.id_unico NOT IN (SELECT dp.detalle_factura FROM gp_detalle_pago dp 
            LEFT JOIN gp_pago p ON dp.pago = p.id_unico  AND p.fecha_pago <= '$fechaF')
        GROUP BY c.id_unico 
        ORDER BY c.id_unico");
    if($acumulado==2){
        if(!empty($nc[0][0])){

            $n_c = count($nc)+5;
            $html .= '<tr>';
            $html .= '<td><strong>TIPO FACTURA</strong></td>';
            $html .= '<td><strong>NÚMERO</strong></td>';
            $html .= '<td><strong>FECHA</strong></td>';
            $html .= '<td><strong>TERCERO</strong></td>';
            for ($i = 0;$i < count($nc);$i++) {
                $html .= '<td><strong>'. mb_strtoupper($nc[$i][2]).'</strong></td>';
                if($nc[$i][3]!=0){
                    $html .= '<td><strong>IVA</strong></td>';
                    $n_c   = $n_c+1;
                }
                if($nc[$i][4]!=0){
                    $html .= '<td><strong>IMPOCONSUMO</strong></td>';
                    $n_c   = $n_c+1;
                }
            }
            $html .= '<td><strong>TOTAL</strong></td>';
            $html .= '</tr>';
            $row = $con->Listar("SELECT DISTINCT f.id_unico, tf.nombre, 
                f.numero_factura, DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), 
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
            LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
            LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
            LEFT JOIN gp_detalle_factura df ON f.id_unico = df.factura
            WHERE f.fecha_factura <= '$fechaF' 
            AND f.tipofactura = $tipo 
            AND df.id_unico NOT IN (SELECT dp.detalle_factura FROM gp_detalle_pago dp 
                LEFT JOIN gp_pago p ON dp.pago = p.id_unico  AND p.fecha_pago <= '$fechaF' )
            HAVING (SELECT COUNT(dff.id_unico) FROM gp_detalle_factura dff WHERE dff.factura = f.id_unico)>0
            ORDER BY f.fecha_factura, f.numero_factura ");
            $ttf = 0;
            for ($f = 0;$f < count($row);$f++) {
                $tf = 0;
                $factura = $row[$f][0];
                $html .= '<tr>';
                $html .= '<td>'.$row[$f][1].'</td>';
                $html .= '<td>'.$row[$f][2].'</td>';
                $html .= '<td>'.$row[$f][3].'</td>';
                $html .= '<td>'.$row[$f][4].' - '.$row[$f][5].'</td>';

                for ($i = 0;$i < count($nc);$i++) {
                    $id_c = $nc[$i][0];
                    $rowd = $con->Listar("SELECT SUM(df.valor * df.cantidad) - 
                            SUM(IF(dp.valor!='', dp.valor, 0))
                        FROM gp_detalle_factura df 
                        LEFT JOIN gp_detalle_pago dp ON dp.detalle_factura = df.id_unico 
                        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                        WHERE df.concepto_tarifa = $id_c AND df.factura =  $factura 
                        AND  IF(p.id_unico IS NULL, 1, p.fecha_pago<='$fechaF')");
                    $html .= '<td> '.number_format($rowd[0][0], 2, '.', ',').'</td>';        
                    $tf +=$rowd[0][0];
                    $tls[$i]+=$rowd[0][0];
                    if($nc[$i][3]!=0){
                        $rowi = $con->Listar("SELECT SUM(IF(df.iva!='', df.iva, 0)) - SUM(IF(dp.iva!='', dp.iva, 0))
                        FROM gp_detalle_factura df 
                        LEFT JOIN gp_detalle_pago dp ON dp.detalle_factura = df.id_unico 
                        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                        WHERE df.concepto_tarifa = $id_c AND df.factura =  $factura 
                        AND  IF(p.id_unico IS NULL, 1, p.fecha_pago<='$fechaF')");
                        $html .= '<td> '.number_format($rowi[0][0], 2, '.', ',').'</td>';        
                        $tf +=$rowi[0][0];
                        $tli[$i]+=$rowi[0][0];
                    }
                    if($nc[$i][4]!=0){
                        $rowm = $con->Listar("SELECT SUM(IF(df.impoconsumo!='', df.impoconsumo, 0)) - 
                            SUM(IF(dp.impoconsumo!='', dp.impoconsumo, 0))
                        FROM gp_detalle_factura df 
                        LEFT JOIN gp_detalle_pago dp ON dp.detalle_factura = df.id_unico 
                        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                        WHERE df.concepto_tarifa = $id_c AND df.factura =  $factura 
                        AND  IF(p.id_unico IS NULL, 1, p.fecha_pago<='$fechaF')");
                        $html .= '<td> '.number_format($rowm[0][0], 2, '.', ',').'</td>';   
                        $tf +=$rowm[0][0];
                        $tlm[$i]+=$rowm[0][0];
                    }
                }
                $html .= '<td> '.number_format($tf, 2, '.', ',').'</td>';        
                $html .= '</tr>';
                $ttf  += $tf;
            }
            $html .= '<tr>';
            $html .= '<td colspan ="4"><strong>TOTALES</strong></td>';
            for ($i = 0;$i < count($nc);$i++) {
                $html .= '<td><strong>'.number_format($tls[$i], 2, '.', ',').'</strong></td>';        
                if($nc[$i][3]!=0){
                    $html .= '<td><strong>'.number_format($tli[$i], 2, '.', ',').'</strong></td>';        
                }
                if($nc[$i][4]!=0){
                    $html .= '<td><strong>'.number_format($tlm[$i], 2, '.', ',').'</strong></td>';        
                }
            }
            $html .= '<td><strong>'.number_format($ttf, 2, '.', ',').'</strong></td>';        
            $html .= '</tr>';
        }
    } else {
        if(!empty($nc[0][0])){
            $n_c = count($nc)+2;
            $html .= '<tr>';
            $html .= '<td><strong>TERCERO</strong></td>';
            for ($i = 0;$i < count($nc);$i++) {
                $html .= '<td><strong>'. mb_strtoupper($nc[$i][2]).'</strong></td>';
                if($nc[$i][3]!=0){
                    $html .= '<td><strong>IVA</strong></td>';
                    $n_c   = $n_c+1;
                }
                if($nc[$i][4]!=0){
                    $html .= '<td><strong>IMPOCONSUMO</strong></td>';
                    $n_c   = $n_c+1;
                }
            }
            $html .= '<td><strong>TOTAL</strong></td>';
            $html .= '</tr>';
            $row = $con->Listar("SELECT DISTINCT t.id_unico,
                IF(t.razonsocial IS NULL, 
                CONCAT_WS(' ',
                t.nombreuno, t.nombredos, t.apellidouno,
                t.apellidodos), t.razonsocial) AS NOMBRE, 
                 IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                    t.numeroidentificacion, 
                    CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
            FROM gp_factura f 
            LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
            LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
            LEFT JOIN gp_detalle_factura df ON f.id_unico = df.factura
            WHERE f.fecha_factura <= '$fechaF' 
            AND f.tipofactura = $tipo 
            AND df.id_unico NOT IN (SELECT dp.detalle_factura FROM gp_detalle_pago dp 
                LEFT JOIN gp_pago p ON dp.pago = p.id_unico  AND p.fecha_pago <= '$fechaF' )
            ORDER BY f.fecha_factura, f.numero_factura ");
            $ttf = 0;
            for ($f = 0;$f < count($row);$f++) {
                $tf = 0;
                $tercero = $row[$f][0];
                $html .= '<tr>';
                $html .= '<td>'.$row[$f][1].' - '.$row[$f][2].'</td>';

                for ($i = 0;$i < count($nc);$i++) {
                    $id_c = $nc[$i][0];
                    $rowd = $con->Listar("SELECT SUM(df.valor * df.cantidad) - 
                            SUM(IF(dp.valor!='', dp.valor, 0))
                        FROM gp_detalle_factura df 
                        LEFT JOIN gp_detalle_pago dp ON dp.detalle_factura = df.id_unico 
                        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                        LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                        WHERE df.concepto_tarifa = $id_c AND f.tercero = $tercero  
                        AND  IF(p.id_unico IS NULL, 1, p.fecha_pago<='$fechaF')");
                    $html .= '<td> '.number_format($rowd[0][0], 2, '.', ',').'</td>';        
                    $tf +=$rowd[0][0];
                    $tls[$i]+=$rowd[0][0];
                    if($nc[$i][3]!=0){
                        $rowi = $con->Listar("SELECT SUM(IF(df.iva!='', df.iva, 0)) - SUM(IF(dp.iva!='', dp.iva, 0))
                        FROM gp_detalle_factura df 
                        LEFT JOIN gp_detalle_pago dp ON dp.detalle_factura = df.id_unico 
                        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                        LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                        WHERE df.concepto_tarifa = $id_c AND  f.tercero = $tercero  
                        AND  IF(p.id_unico IS NULL, 1, p.fecha_pago<='$fechaF')");
                        $html .= '<td> '.number_format($rowi[0][0], 2, '.', ',').'</td>';        
                        $tf +=$rowi[0][0];
                        $tli[$i]+=$rowi[0][0];
                    }
                    if($nc[$i][4]!=0){
                        $rowm = $con->Listar("SELECT SUM(IF(df.impoconsumo!='', df.impoconsumo, 0)) - 
                            SUM(IF(dp.impoconsumo!='', dp.impoconsumo, 0))
                        FROM gp_detalle_factura df 
                        LEFT JOIN gp_detalle_pago dp ON dp.detalle_factura = df.id_unico 
                        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                        LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                        WHERE df.concepto_tarifa = $id_c AND f.tercero = $tercero  
                        AND  IF(p.id_unico IS NULL, 1, p.fecha_pago<='$fechaF')");
                        $html .= '<td> '.number_format($rowm[0][0], 2, '.', ',').'</td>';   
                        $tf +=$rowm[0][0];
                        $tlm[$i]+=$rowm[0][0];
                    }
                }
                $html .= '<td> '.number_format($tf, 2, '.', ',').'</td>';        
                $html .= '</tr>';
                $ttf  += $tf;
            }
            $html .= '<tr>';
            $html .= '<td><strong>TOTALES</strong></td>';
            for ($i = 0;$i < count($nc);$i++) {
                $html .= '<td><strong>'.number_format($tls[$i], 2, '.', ',').'</strong></td>';        
                if($nc[$i][3]!=0){
                    $html .= '<td><strong>'.number_format($tli[$i], 2, '.', ',').'</strong></td>';        
                }
                if($nc[$i][4]!=0){
                    $html .= '<td><strong>'.number_format($tlm[$i], 2, '.', ',').'</strong></td>';        
                }
            }
            $html .= '<td><strong>'.number_format($ttf, 2, '.', ',').'</strong></td>';        
            $html .= '</tr>';
        }
    }
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $titulo;?></title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="<?php echo $n_c?>" align="center">
            <strong>
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent.
                    "<br/>".$direccinTer.' Tel:'.$telefonoTer.
                    "<br/>".$titulo;?>
                <br/>&nbsp;</strong>
            </th>
            <?php echo $html;?>
        </table>
    </body>
</html>
