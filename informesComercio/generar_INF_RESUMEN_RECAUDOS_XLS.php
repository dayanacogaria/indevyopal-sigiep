<?php
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#21/09/2018 | Erica G. | Actualizacion código
#####################################################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Resumen_Recaudo.xls");
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require_once('../numeros_a_letras.php');
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];
$nanno  = anno($anno);

#   ************    Datos Recibe    ************    #
$fechaI         = $_REQUEST['sltFechaI'];
IF(empty($_REQUEST['sltFechaF'])){
    $fechaF     = date('d/m/Y');
} else {
    $fechaF     = $_REQUEST['sltFechaF'];
}
$fechaI         = fechaC($fechaI);
$fechaF         = fechaC($fechaF);
$bancoI         = $_REQUEST['sltBancoI'];
$rownbI         = $con->Listar("SELECT numerocuenta, descripcion FROM gf_cuenta_bancaria WHERE id_unico =$bancoI");
$bancoIn        = $rownbI[0][0].' - '.ucwords(mb_strtolower($rownbI[0][1]));
IF(empty($_REQUEST['sltBancoF'])){
    $rowb       = $con->Listar("SELECT MAX(id_unico) FROM gf_cuenta_bancaria WHERE parametrizacionanno = $anno");
    $bancoF     = $rowb[0][0];
} else {
    $bancoF     = $_REQUEST['sltBancoF'];
}
$rownbF         = $con->Listar("SELECT numerocuenta, descripcion FROM gf_cuenta_bancaria WHERE id_unico =$bancoF");
$bancoFi        = $rownbF[0][0].' - '.ucwords(mb_strtolower($rownbF[0][1]));

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

$row = $con->Listar("SELECT cb.id_unico, 
        cb.numerocuenta, cb.descripcion, 
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
            t.apellidodos)) AS NOMBRE
        FROM gf_cuenta_bancaria cb  
        LEFT JOIN gf_tercero t ON cb.banco = t.id_unico 
        WHERE cb.id_unico BETWEEN $bancoI AND $bancoF 
        AND cb.parametrizacionanno = $anno");
$rowc = $con->Listar("SELECT DISTINCT dd.concepto,  cc.descripcion, 
    cc.nom_inf 
    FROM gc_detalle_declaracion dd
    LEFT JOIN gc_detalle_recaudo dr ON dr.det_dec = dd.id_unico
    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
    WHERE rc.fecha BETWEEN '$fechaI' AND '$fechaF'  
    AND rc.clase = 1 AND parametrizacionanno = $anno ORDER BY cc.codigo ASC");
$cols = count($rowc)+6;
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Resumen Recaudos</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="<?php echo $cols;?>" align="center"><strong>
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                <br/>&nbsp;
                <br/>RESUMEN DE RECAUDOS INDUSTRIA Y COMERCIO
                <br/>DEL <?php echo $_REQUEST['sltFechaI'].' AL '.$_REQUEST['sltFechaF']; ?>
                <br/>Cuenta Inicial: <?php echo $bancoIn.'<br/> Cuenta Final: '.$bancoFi; ?>
                <br/>&nbsp;</strong>
            </th>
            <tr></tr>
            
            <?php 
            $decla ='';
            $decl ='0';
            for ($i = 0; $i < count($row); $i++) {
                #*** Buscar Recaudos De Cuenta Bancaria ****#
                $rowr = $con->Listar("SELECT DISTINCT c.id_unico,
                        IF(CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos) 
                        IS NULL OR CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos) = '',
                        (tr.razonsocial),
                        CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos)),
                        cb.descripcion,
                        rc.num_pag,
                        c.codigo_mat,
                        d.id_unico,
                        rc.fecha,
                        d.cod_dec,
                        ac.vigencia,
                        ac.mes, 
                        DATE_FORMAT(rc.fecha,'%d/%m/%Y'), 
                        d.id_unico ,
                        cc.numero,
                        tp.sigla
                    FROM gc_recaudo_comercial rc 
                    INNER JOIN gc_detalle_recaudo dr ON  dr.recaudo = rc.id_unico
                    LEFT JOIN gf_detalle_comprobante dc ON dc.id_unico = dr.detalle_cnt
                    LEFT JOIN gf_comprobante_cnt cc ON cc.id_unico = dc.comprobante
                    LEFT JOIN gf_tipo_comprobante tp ON  tp.id_unico = cc.tipocomprobante
                    LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                    LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                    LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                    LEFT JOIN gf_cuenta_bancaria cb ON rc.cuenta_ban = cb.id_unico
                    WHERE dc.id_unico is not NULL AND  rc.cuenta_ban = ".$row[$i][0]." 
                    AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' 
                    AND rc.clase = 1     
                    AND rc.parametrizacionanno = $anno ");
                if(count($rowr)>0){
                    echo '<th colspan="'.$cols.'" align="left"><i><strong>';
                    echo '<br/>'.$row[$i][1].' - '.ucwords(mb_strtolower($row[$i][2])).' Banco:'.ucwords(mb_strtolower($row[$i][3]));
                    echo '<br/>&nbsp;</i></strong>'; 
                    echo '</th>';
                    echo '<tr></tr>';
                    echo '<tr>';
                    echo '<td><strong>Matrícula</strong></td>';
                    echo '<td><strong>Contribuyente</strong></td>';
                    echo '<td><strong>Cod. Dec.</strong></td>';
                    echo '<td><strong>Perioodo G.</strong></td>';
                    echo '<td><strong>Mes</strong></td>';
                    echo '<td><strong>Fecha R.</td>';
                    echo '<td><strong>Comprobante Contable</td>';
                    for ($c = 0; $c < count($rowc); $c++) {
                        echo '<td><strong>'.$rowc[$c][2].'</strong></td>';
                    }
                   
                    
                    for ($r = 0; $r < count($rowr); $r++) {
                        if($rowr[$r][9] == 0 ){
                            $mes = "ANUAL";
                        }elseif($rowr[$r][9] == 1){
                            $mes = "ENERO";
                        }elseif($rowr[$r][9] == 2){
                            $mes = "FEBRERO";
                        }elseif($rowr[$r][9] == 3){
                            $mes = "MARZO";
                        }elseif($rowr[$r][9] == 4){
                            $mes = "ABRIL";
                        }elseif($rowr[$r][9] == 5){
                            $mes = "MAYO";
                        }elseif($rowr[$r][9] == 6){
                            $mes = "JUNIO";
                        }elseif($rowr[$r][9] == 7){
                            $mes = "JULIO";
                        }elseif($rowr[$r][9] == 8){
                            $mes = "AGOSTO";
                        }elseif($rowr[$r][9] == 9){
                            $mes = "SEPTIEMBRE";
                        }elseif($rowr[$r][9] == 10){
                            $mes = "OCTUBRE";
                        }elseif($rowr[$r][9] == 11){
                            $mes = "NOVIEMBRE";
                        }elseif($rowr[$r][9] == 12){
                            $mes = "DICIEMBRE";
                        }
                        echo '<tr>';
                        echo '<td style="mso-number-format:\@" align="center">'.$rowr[$r][4].'</td>';
                        echo '<td>'.$rowr[$r][1].'</td>';
                        echo '<td>'.$rowr[$r][7].'</td>';
                        echo '<td>'.$rowr[$r][8].'</td>';
                        echo '<td>'.$mes.'</td>';
                        echo '<td>'.$rowr[$r][10].'</td>';
                        echo '<td>'.$rowr[$r][13].'-'.$rowr[$r][12].'</td>';
                        for ($c = 0; $c < count($rowc); $c++) {
                            $vci = $con->Listar("SELECT dr.valor
                                FROM gc_detalle_recaudo dr
                                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                WHERE d.contribuyente = '".$rowr[$r][0]."' 
                                AND cc.id_unico = '".$rowc[$c][0]."' 
                                AND d.id_unico = '".$rowr[$r][5]."'");
                            if(count($vci)>0){
                                $valord = $vci[0][0];
                            } else {
                                $valord = 0;
                            }
                            echo '<td>'. number_format($valord,2,'.',',').'</td>';
                        }
                        echo '</tr>';
                    }
                    echo '<tr>';
                    echo '<td colspan="6"><strong>TOTAL</strong></td>';
                    for ($c = 0; $c < count($rowc); $c++) {
                        $vci = $con->Listar("SELECT
                            SUM(dr.valor)
                        FROM
                            gc_detalle_recaudo dr
                        LEFT JOIN 
                                gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico 
                        LEFT JOIN 
                                gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico 
                        LEFT JOIN 
                                gc_concepto_comercial cc ON dd.concepto = cc.id_unico 
                        WHERE
                            rc.cuenta_ban = ".$row[$i][0]."  
                            AND rc.fecha  BETWEEN '$fechaI' AND '$fechaF' 
                             AND cc.id_unico ='".$rowc[$c][0]."'  AND rc.clase = 1 ");
                        
                        if(count($vci)>0 || !empty($vci[0][0])){
                            $valord = $vci[0][0];
                        } else {
                            $valord =0;
                        }
                        echo '<td><strong>'. number_format($valord,2,'.',',').'</strong></td>';
                    }
                    echo '</tr>';
                }
            }
            #******* Totales Finales *********#
            echo '<tr>';
            echo '<td colspan="6"><strong>TOTAL</strong></td>';
            for ($c = 0; $c < count($rowc); $c++) {
                $vci = $con->Listar("SELECT
                    SUM(dr.valor)
                FROM
                    gc_detalle_recaudo dr
                LEFT JOIN 
                        gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico 
                LEFT JOIN 
                        gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico 
                LEFT JOIN 
                        gc_concepto_comercial cc ON dd.concepto = cc.id_unico 
                WHERE rc.fecha  BETWEEN '$fechaI' AND '$fechaF'
                      AND  rc.cuenta_ban BETWEEN '$bancoI' AND '$bancoF'
                     AND cc.id_unico ='".$rowc[$c][0]."'  AND rc.clase = 1 ");

                if(count($vci)>0 || !empty($vci[0][0])){
                    $valord = $vci[0][0];
                } else {
                    $valord =0;
                }
                echo '<td><strong>'. number_format($valord,2,'.',',').'</strong></td>';
            }
            echo '</tr>';           
            #**********Totales Vigencia Actual ***********#
            echo '<tr>';
            echo '<td colspan="6" rowspan="2"><strong>TOTALES VIGENCIA ACTUAL</strong></td>';
            for ($c = 0; $c < count($rowc); $c++) {
                echo '<td><strong>'.$rowc[$c][2].'</strong></td>';
            }
            echo '<tr><br/>';
            echo $annoc = $nanno-1;
            for ($c = 0; $c < count($rowc); $c++) {
                $vigenciaA = $con->Listar("SELECT
                    SUM(dr.valor)
                FROM
                    gc_detalle_recaudo dr
                LEFT JOIN 
                        gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico 
                LEFT JOIN 
                        gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico 
                LEFT JOIN 
                        gc_concepto_comercial cc ON dd.concepto = cc.id_unico 
                LEFT JOIN 
                        gc_declaracion d ON dd.declaracion = d.id_unico 
                LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                WHERE ac.vigencia = '$annoc' AND 
                      rc.fecha  BETWEEN '$fechaI' AND '$fechaF' 
                    AND  rc.cuenta_ban BETWEEN '$bancoI' AND '$bancoF'
                    AND cc.id_unico ='".$rowc[$c][0]."'  AND rc.clase = 1 "); 
                if(count($vigenciaA)>0){
                    $totalVa = $vigenciaA[0][0];
                } else {
                    $totalVa = 0;
                }
                echo '<td>'. number_format($totalVa,2,'.',',').'</td>';
            }
            #**********Totales Vigencia Anterior ***********#
            echo '<tr>';
            echo '<td colspan="6" rowspan="2"><strong>TOTALES VIGENCIA ANTERIOR</strong></td>';
            for ($c = 0; $c < count($rowc); $c++) {
                echo '<td><strong>'.$rowc[$c][2].'</strong></td>';
            }
            echo '<tr><br/>';
            echo $annoc = $nanno-1;
            for ($c = 0; $c < count($rowc); $c++) {
                $vigenciaA = $con->Listar("SELECT
                    SUM(dr.valor)
                FROM
                    gc_detalle_recaudo dr
                LEFT JOIN 
                        gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico 
                LEFT JOIN 
                        gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico 
                LEFT JOIN 
                        gc_concepto_comercial cc ON dd.concepto = cc.id_unico 
                LEFT JOIN 
                        gc_declaracion d ON dd.declaracion = d.id_unico 
                LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                WHERE ac.vigencia < '$annoc' AND 
                    rc.fecha  BETWEEN '$fechaI' AND '$fechaF' 
                     AND  rc.cuenta_ban BETWEEN '$bancoI' AND '$bancoF'
                    AND cc.id_unico ='".$rowc[$c][0]."'  AND rc.clase = 1 "); 
                if(count($vigenciaA)>0){
                    $totalVa = $vigenciaA[0][0];
                } else {
                    $totalVa = 0;
                }
                echo '<td>'. number_format($totalVa,2,'.',',').'</td>';
            }
            ?>
        </table>
    </body>
</html>