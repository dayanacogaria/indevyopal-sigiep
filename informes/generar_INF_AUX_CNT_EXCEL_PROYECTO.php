<?php
####################### Modificaciones #################
# 21/04/2017 | Erica G.| CAMBIAR TERCERO POR TERCERO DEL DETALLE
#06/04/2017 |Erica G |VERIFICACION CONSULTAS
#13/03/2017 |ERICA G | AÑADIR CAMPO NUMERO COMPROBANTE
#06/03/2017 |ERICA G | ARREGLO CONSULTAS FECHA
#03/03/2017 |ERICA G. |MODIFICACION CONSULTAS
#12:00 | 11/02/2017 | Erica G //Arreglo de consultas
#########################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Auxiliar_Contable_Proyecto.xls");
require_once("../Conexion/conexion.php");
require_once'../Conexion/ConexionPDO.php';
ini_set('max_execution_time', 0);
session_start();
$con            = new ConexionPDO();
$panno          = $_SESSION['anno'];
$compania       = $_SESSION['compania'];
$calendario     = CAL_GREGORIAN;
$anno           = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$mes            = $mysqli->real_escape_string(''.$_POST['sltmes'].'');
$dia            = cal_days_in_month($calendario, $mes, $anno); 
$fecha          = $anno.'-'.$mes.'-'.$dia;
$fechaInicial   = $anno.'-'.'01-01';

$compini        = $mysqli->real_escape_string(''.$_POST["sltTci"].'');
$compfin        = $mysqli->real_escape_string(''.$_POST["sltTcf"].'');
$fechaini       = $mysqli->real_escape_string(''.$_POST["fechaini"].''); 
$fechafin       = $mysqli->real_escape_string(''.$_POST["fechafin"].'');
$cuentaini      = $mysqli->real_escape_string(''.$_POST["sltctai"].'');
$cuentafin      = $mysqli->real_escape_string(''.$_POST["sltctaf"].'');
$proyectoI      = $_POST["sltproyectoI"];
$proyectoF      = $_POST["sltproyectoF"];
#********Consultas encabezado ****#########
#Consulta Mínima Cuenta
$cta1 = "SELECT codi_cuenta from gf_cuenta WHERE id_unico = $cuentaini";
$mincta = $mysqli->query($cta1);
$filac1 = mysqli_fetch_array($mincta);
$cuentaMin = $filac1['codi_cuenta'];   
#Fin Consulta Mínima Cuenta
#Inicio consulta Máxima Cuenta
$cta2 = "SELECT codi_cuenta from gf_cuenta WHERE id_unico = $cuentafin";
$maxcta = $mysqli->query($cta2);
$filac2 = mysqli_fetch_array($maxcta);
$cuentaMax = $filac2['codi_cuenta'];   
#Fin Consulta Maxima Cuenta

#************Datos Compañia************#
$compania = $_SESSION['compania'];
$sqlC = "SELECT 	ter.id_unico,
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
WHERE ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowC = mysqli_fetch_row($resultC);
$razonsocial = $rowC[1];
$nombreIdent = $rowC[2];
$numeroIdent = $rowC[3];
$direccinTer = $rowC[4];
$telefonoTer = $rowC[5];

$sqlproy = $con->Listar("
SELECT *
FROM gf_proyecto 
WHERE id_unico IN ($proyectoI,$proyectoF)");
$proyectoInicial = $sqlproy[0][1];
if ($proyectoI == $proyectoF){
    $proyectoFinal   = $sqlproy[0][1];
}else {
    $proyectoFinal   = $sqlproy[1][1];    
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Auxiliares Contables Proyeccto</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th colspan="9" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
           <br/>AUXILIARES CONTABLES
           <br/>Cuentas de <?php echo $cuentaMin.' a '.$cuentaMax?>
           <br/>Comprobantes de <?php echo $compini.' a '.$compfin?>
           <br/>Fecha <?php echo $fechaini.' a '.$fechafin?></strong>
           <br/>Proyecto Inicial:  <?php echo strtoupper($proyectoInicial).' - '.strtoupper($proyectoFinal)?></strong>
            <br/>&nbsp;
        </th>
  </tr>
  <tr>
    <td><strong>FECHA</strong></td>
    <td><strong>TIPO COMPROBANTE</strong></td>
    <td><strong>NÚMERO COMPROBANTE</strong></td>
    <td><strong>CENTRO COSTO</strong></td>
    <td><strong>NOMBRE DEL TERCERO</strong></td>
    <td><strong>DESCRIPCION</strong></td>
    <td><strong>VALOR DEBITO</strong></td>
    <td><strong>VALOR CREDITO</strong></td>
    <td><strong>SALDO</strong></td>
  </tr>
  
<?php
$codd    = 0;
$totales = 0;
$valorA = 0;

$debito = "";
$credito = "";
$totaldeb = 0.00;
$totalcred = 0.00;
		  
$saldoT = 0;
$saldoTT = 0;

$cnt = 0;
$cta1 = "SELECT codi_cuenta from gf_cuenta WHERE id_unico = $cuentaini";
$mincta = $mysqli->query($cta1);

while ($filac1 = mysqli_fetch_array($mincta)) 
{
 $cuentaMin = $filac1['codi_cuenta'];   
}
#Fin Consulta Mínima Cuenta
$cuenta1 = $cuentaMin;
#Inicio consulta Máxima Cuenta
$cta2 = "SELECT codi_cuenta from gf_cuenta WHERE id_unico = $cuentafin";
$maxcta = $mysqli->query($cta2);
while ($filac2 = mysqli_fetch_array($maxcta)) 
{
 $cuentaMax = $filac2['codi_cuenta'];   
}

$cuentas = "SELECT DISTINCT cuenta FROM gf_detalle_comprobante dc 
            LEFT JOIN gf_cuenta c ON dc.cuenta= c.id_unico 
            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
            WHERE cn.parametrizacionanno = $panno 
        AND dc.valor IS NOT NULL AND c.codi_cuenta BETWEEN '$cuentaMin' AND '$cuentaMax' 
 ORDER BY c.codi_cuenta ASC";
    $cuenta = $mysqli->query($cuentas);
    while ($filacuenta = mysqli_fetch_array($cuenta)) 
    {
        $cuent = $filacuenta['cuenta'];
        $cnt = $cuent;
        $idcuen = "SELECT codi_cuenta, nombre FROM gf_cuenta WHERE id_unico = '$cnt'";
    $codcuen = $mysqli->query($idcuen);
    while ($filacuen = mysqli_fetch_array($codcuen)) 
    {
        $codicuenta = $filacuen['codi_cuenta'].' - '.ucwords(mb_strtolower($filacuen['nombre']));   
    }

#Fecha Previa - Comienzo
$fechaP     = $fechaini;
$fechaP     = trim($fechaP, '"');
$fecha_div  = explode("/", $fechaP);
$dia      = $fecha_div[0];
$mes        = $fecha_div[1];
$anio        = $fecha_div[2];
$diaA       = intval($dia);
$diaAnt     = $diaA-1;
#Rutina para obtener la fecha del día anterior
if ($diaAnt < 1)
 {
    switch ($mes)
    {
        case 1:
            $mes = 1;
            $diaAnt = 01;
         break;
        case 2:
            $mes = $mes - 1;
            $diaAnt = 31;
         break;
        case 3:
            $mes = $mes - 1;
            $diaAnt = 29;
         break;
        case 4:
            $mes = $mes - 1;
            $diaAnt = 31;
         break;
        case 5:
            $mes = $mes - 1;
            $diaAnt = 30;
         break;
        case 6:
            $mes = $mes - 1;
            $diaAnt = 31;
         break;
        case 7:
            $mes = $mes - 1;
            $diaAnt = 30;
         break;
        case 8:
            $mes = $mes - 1;
            $diaAnt = 31;
         break;
        case 9:
            $mes = $mes - 1;
            $diaAnt = 31;
         break;
        case 10:
            $mes = $mes - 1;
            $diaAnt = 30;
         break;
        case 11:
            $mes = $mes - 1;
            $diaAnt = 31;
         break;
        case 12:
            $mes = $mes - 1;
            $diaAnt = 30;
         break;
    }
    $fechaP = $anio.'-'.$mes.'-'.$diaAnt;
}
#Fin Rutina para obtener la fecha previa*/
if($diaAnt<10){
    $diaAnt = '0'.$diaAnt;
}
 $fechaP = $anio.'-'.$mes.'-'.$diaAnt;
$fecha11 = trim($fechaini, '"');
$fecha_div1 = explode("/", $fecha11);
$dia11 = $fecha_div1[0];
$mes11 = $fecha_div1[1];
$anio11 = $fecha_div1[2];
$fechaini1 =$anio11.'/'.$mes11.'/'.$dia11;
$fecha12 = trim($fechafin, '"');
$fecha_div2 = explode("/", $fecha12);
$dia12 = $fecha_div2[0];
$mes12 = $fecha_div2[1];
$anio12 = $fecha_div2[2];
$fechafin1 =$anio12.'/'.$mes12.'/'.$dia12;     
    
$sql = "SELECT DISTINCT
                                                cn.id_unico             as cnid,
                                                cn.tipocomprobante      as cntcom,
                                                cn.numero               as cnnum,
                                                cn.tercero              as cnter, 
                                                tr.id_unico             as trid,
                                                tr.nombreuno            as trnom1,
                                                tr.nombredos            as trnom2,
                                                tr.apellidouno          as trape1,
                                                tr.apellidodos          as trape2,
                                                tr.razonsocial          as trsoc,
                                                ti.nombre               as tinom,
                                                tr.numeroidentificacion as trnum,
                                                ct.id_unico             as ctid,
                                                ct.sigla               as ctnom,
                                                cc.id_unico             as ccid,
                                                cc.nombre               as ccnom,
                                                cn.numerocontrato       as cnnumcont,
                                                ec.nombre               as ecnom,
                                                cn.descripcion          as cndesc,
                                                dc.comprobante          as dccomp,
                                                dc.centrocosto          as dccos,
                                                cn.fecha                as dcfec,
                                                cen.id_unico            as cencid,
                                                cen.nombre              as cennom,
                                                cta.codi_cuenta         as nomcta,
                                                cta.naturaleza          as natcta,
                                                dc.valor                as dcvalor,
                                                dc.cuenta               as dcuenta,
                                                cn.fecha                as cnfec, 
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
                                                tr.apellidodos)) AS NOMBRE , 
                                                dc.id_unico 
                                        FROM gf_comprobante_cnt cn
                                        LEFT JOIN gf_tipo_comprobante ct        ON cn.tipocomprobante = ct.id_unico
                                        LEFT JOIN gf_tipo_comprobante nom       ON cn.tipocomprobante = ct.id_unico
                                        LEFT JOIN gf_clase_contrato cc          ON cn.clasecontrato = cc.id_unico
                                        LEFT JOIN gf_estado_comprobante_cnt ec  ON cn.estado = ec.id_unico 
                                        LEFT JOIN gf_detalle_comprobante dc     ON cn.id_unico = dc.comprobante
                                        LEFT JOIN gf_tercero tr                 ON dc.tercero = tr.id_unico
                                        LEFT JOIN gf_tipo_identificacion ti     ON tr.tipoidentificacion = ti.id_unico
                                        LEFT JOIN gf_centro_costo cen           ON dc.centrocosto = cen.id_unico
                                        LEFT JOIN gf_cuenta cta                 ON cta.id_unico = dc.cuenta
                                        LEFT JOIN gf_proyecto py                ON dc.proyecto = py.id_unico
                                        WHERE dc.valor IS NOT NULL AND dc.cuenta = '$cnt'
                                        AND cn.fecha BETWEEN '$fechaini1' AND '$fechafin1'
                                        AND ct.sigla BETWEEN '$compini' AND '$compfin' AND ct.clasecontable != 5 
                                        AND cn.parametrizacionanno = $panno AND ct.compania = $compania 
                                        AND py.id_unico BETWEEN $proyectoI AND $proyectoF
                                        ORDER BY cn.fecha ASC";
     
$cp      = $mysqli->query($sql);
###########################################Fin Consulta Principal################################# 
#Consulta Secundaria
$a=$_SESSION['anno'];
$anno="SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $a";
$anno = $mysqli->query($anno);
if(mysqli_num_rows($anno)>0){
    $anno = mysqli_fetch_row($anno);
    $anno = $anno[0];
} else {
    $anno = date('Y');
}

        if ($fechaini1 != $anno.'/01/01') 
        { 
            
$sql2  = "SELECT DISTINCT
                                                cn.id_unico             as cnid,                                                
                                                cn.tipocomprobante      as cntcom,
                                                cn.numero               as cnnum,
                                                cn.tercero              as cnter, 
                                                tr.id_unico             as trid,
                                                tr.nombreuno            as trnom1,
                                                tr.nombredos            as trnom2,
                                                tr.apellidouno          as trape1,
                                                tr.apellidodos          as trape2,
                                                tr.razonsocial          as trsoc,
                                                ti.nombre               as tinom,
                                                tr.numeroidentificacion as trnum,
                                                ct.id_unico             as ctid,
                                                ct.sigla                as ctnom,
                                                cc.id_unico             as ccid,
                                                cc.nombre               as ccnom,
                                                cn.numerocontrato       as cnnumcont,
                                                ec.nombre               as ecnom,
                                                cn.descripcion          as cndesc,
                                                dc.comprobante          as dccomp,
                                                dc.centrocosto          as dccos,
                                                dc.fecha                as dcfec,
                                                cen.id_unico            as cencid,
                                                cen.nombre              as cennom,
                                                cta.codi_cuenta         as nomcta,
                                                cta.naturaleza          as natcta,
                                                dc.valor                as dcvalor,
                                                dc.cuenta               as dcuenta,
                                                dc.fecha                as cnfec, 
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
                                                tr.apellidodos)) AS NOMBRE, dc.id_unico  
                                        FROM gf_comprobante_cnt cn
                                        LEFT JOIN gf_tipo_comprobante ct        ON cn.tipocomprobante = ct.id_unico
                                        LEFT JOIN gf_tipo_comprobante nom       ON cn.tipocomprobante = ct.id_unico
                                        LEFT JOIN gf_clase_contrato cc          ON cn.clasecontrato = cc.id_unico
                                        LEFT JOIN gf_estado_comprobante_cnt ec  ON cn.estado = ec.id_unico 
                                        LEFT JOIN gf_detalle_comprobante dc     ON cn.id_unico = dc.comprobante
                                        LEFT JOIN gf_tercero tr                 ON dc.tercero = tr.id_unico
                                        LEFT JOIN gf_tipo_identificacion ti     ON tr.tipoidentificacion = ti.id_unico
                                        LEFT JOIN gf_centro_costo cen           ON dc.centrocosto = cen.id_unico
                                        LEFT JOIN gf_cuenta cta                 ON cta.id_unico = dc.cuenta
                                        WHERE dc.cuenta = '$cnt'
                                        AND cn.fecha BETWEEN '$anno-01-01' AND '$fechaP' 
                                        AND cn.parametrizacionanno = $panno 
                                        AND ct.sigla BETWEEN '$compini' AND '$compfin'
                                        ORDER BY dc.fecha ASC";
        } #WHERE dc.cuenta BETWEEN '$cuentaini' AND '$cuentafin'
        elseif($fechaini1 == $anno.'/01/01') { //            
           $sql2  = "SELECT DISTINCT
                                                cn.id_unico             as cnid,                                                
                                                cta.naturaleza          as natcta,
                                                dc.valor                as dcvalor,
                                                dc.cuenta               as dcuenta,
                                                dc.fecha                as cnfec, 
                                                dc.id_unico  
                                        FROM gf_comprobante_cnt cn
                                        LEFT JOIN gf_tipo_comprobante ct        ON cn.tipocomprobante = ct.id_unico
                                        LEFT JOIN gf_tipo_comprobante nom       ON cn.tipocomprobante = ct.id_unico
                                        LEFT JOIN gf_clase_contrato cc          ON cn.clasecontrato = cc.id_unico
                                        LEFT JOIN gf_estado_comprobante_cnt ec  ON cn.estado = ec.id_unico 
                                        LEFT JOIN gf_detalle_comprobante dc     ON cn.id_unico = dc.comprobante
                                        LEFT JOIN gf_tercero tr                 ON dc.tercero = tr.id_unico
                                        LEFT JOIN gf_tipo_identificacion ti     ON tr.tipoidentificacion = ti.id_unico
                                        LEFT JOIN gf_centro_costo cen           ON dc.centrocosto = cen.id_unico
                                        LEFT JOIN gf_cuenta cta                 ON cta.id_unico = dc.cuenta  
                                        WHERE dc.cuenta = '$cnt'
                                        AND cn.fecha = '$anno-01-01' 
                                        AND cn.parametrizacionanno = $panno 
                                       AND ct.clasecontable = 5 
                                        ORDER BY dc.fecha ASC";//Empty Query
        }

        $csaldo = $mysqli->query($sql2);

$saldoTA = 0.00;
       #########################################################################################
###################Consulta para obtener sumatoria de Saldos#############################
#########################################################################################  
if(mysqli_num_rows($csaldo)>0){
  
while ($filasal =   mysqli_fetch_array($csaldo)) 
    {
     $filasal['natcta'];
         #Naturaleza Débito
         if ($filasal['natcta']==1) {
             if ($filasal['dcvalor']>=0) {
                 $debA = $filasal['dcvalor'];
                 $saldoTA = $saldoTA + $debA;
                 $debitoA = number_format($filasal['dcvalor'],2,'.',',');
             }else{
                 $debitoA = "0.00";
             }
         }  elseif ($filasal['natcta']==2) {
             if($filasal['dcvalor']<=0){
                 $debA = $filasal['dcvalor'];
                 $saldoTA = $saldoTA + $debA;
                 $xA = (float) substr($filasal['dcvalor'],'1');
                 $debitoA = number_format($xA,2,'.',',');
             }else{
                 $debitoA = "0.00";
             }
             
            }
        #Fin Naturaleza Débito
        # 
        #Naturaleza Crédito
            if($filasal['natcta']==2){
             if ($filasal['dcvalor']>=0) {
                 $crA = $filasal['dcvalor'];
                 $saldoTA = $saldoTA + $crA;
                 $creditoA = number_format($filasal['dcvalor'],2,'.',',');
             }else{
                 $creditoA = "0.00";
             }
             //$saldoT = $saldoT - $cr;
            }elseif($filasal['natcta']==1){
                 if($filasal['dcvalor']<=0){
                 $crA = $filasal['dcvalor'];
                 $saldoTA = $saldoTA + $crA;
                 $yA = (float) substr($filasal['dcvalor'], '1');
                 $creditoA = number_format($yA,2,'.',',');
                 }  else {
                 $creditoA = "0.00";
                }
         }
     
     #    }
     }
}   
        
       
################################### 
# Fin Consulta para llenar Saldos #
###################################
if(mysqli_num_rows($cp)>0) {
$proyectocnt = "";
$sqlpycnt = $con->Listar("
SELECT
DISTINCT cn.id_unico, py.*
FROM gf_comprobante_cnt cn
LEFT JOIN gf_tipo_comprobante ct        ON cn.tipocomprobante = ct.id_unico
LEFT JOIN gf_tipo_comprobante nom       ON cn.tipocomprobante = ct.id_unico
LEFT JOIN gf_clase_contrato cc          ON cn.clasecontrato = cc.id_unico
LEFT JOIN gf_estado_comprobante_cnt ec  ON cn.estado = ec.id_unico 
LEFT JOIN gf_detalle_comprobante dc     ON cn.id_unico = dc.comprobante
LEFT JOIN gf_tercero tr                 ON dc.tercero = tr.id_unico
LEFT JOIN gf_tipo_identificacion ti     ON tr.tipoidentificacion = ti.id_unico
LEFT JOIN gf_centro_costo cen           ON dc.centrocosto = cen.id_unico
LEFT JOIN gf_cuenta cta                 ON cta.id_unico = dc.cuenta
LEFT JOIN gf_proyecto py                ON dc.proyecto = py.id_unico
WHERE dc.valor IS NOT NULL AND dc.cuenta = '$cnt'
AND cn.fecha BETWEEN '$fechaini1' AND '$fechafin1'
AND ct.sigla BETWEEN '$compini' AND '$compfin' AND ct.clasecontable != 5 
AND cn.parametrizacionanno = $panno AND ct.compania = $compania 
AND py.id_unico BETWEEN $proyectoI AND $proyectoF
ORDER BY cn.fecha ASC");
$proyectocnt = $sqlpycnt[0][2];
    ?>    
    <tr>    
        <td colspan="5"><strong><?php echo $proyectocnt.' - Código cuenta: '.$codicuenta;?></strong></td>
        <td colspan="4"><strong><?php echo 'Saldo Inicial:  '.number_format($saldoTA,2,'.',',');?></strong></td>
    </tr>
    <?php
       
        $tmp = 0;
        $cuenta1 = $cuentaini;
        $cuenta2 = 0;
        $saldoT = $saldoTA;
        $totaldeb=0;
        $totalcred=0;
        while ($fila = mysqli_fetch_array($cp)) 
        {
            if ($fila['natcta']==1) {
            if ($fila['dcvalor']>=0) {
                $deb = $fila['dcvalor'];            
                $tmp = $deb;
                $saldoT = $saldoT + $deb;
                $debito = number_format($fila['dcvalor'],2,'.',',');
                $totaldeb = $totaldeb+$fila['dcvalor'];
            }else{
                $debito = "0.00";
            }
            
        }  elseif ($fila['natcta']==2) {
            if($fila['dcvalor']<=0){
                $deb = $fila['dcvalor'];
                $tmp = $deb;
                $saldoT = $saldoT + $deb;
                $x = (float) substr($fila['dcvalor'],'1');
                $debito = number_format($x,2,'.',',');
                $totaldeb = $totaldeb+$x;
            }else{
                $debito = "0.00";
            }
            
        }
        #Fin Naturaleza Débito
        
        #Naturaleza Crédito
        if($fila['natcta']==2){
            if ($fila['dcvalor']>=0) {
                $cr = $fila['dcvalor'];
                $saldoT = $saldoT + $cr;
                $credito = number_format($fila['dcvalor'],2,'.',',');
                $totalcred=$totalcred+$fila['dcvalor'];
            }else{
                $credito = "0.00";
            }
            //$saldoT = $saldoT - $cr;
        }elseif($fila['natcta']==1){
            if($fila['dcvalor']<=0){
                $cr = $fila['dcvalor'];
                $saldoT = $saldoT + $cr;
                $y = (float) substr($fila['dcvalor'], '1');
                $credito = number_format($y,2,'.',',');
                $totalcred=$totalcred+$y;
                }  else {
                $credito = "0.00";
            }
            //$saldoT = $saldoT - $cr;
        }
        $fechaCC = $fila['cnfec'];
        $fechaCC = trim($fila['cnfec'], '"');
        $fecha_div = explode("-", $fechaCC);
        $anio = $fecha_div[0];
        $mes = $fecha_div[1];
        $dia = $fecha_div[2];
        $fechaCC = $dia.'/'.$mes.'/'.$anio;
        #Fecha - Fin
        ?>
    <tr>
        <td><?php echo $fechaCC;?></td>
        <td><?php echo $fila['ctnom'];?></td>
        <td><?php echo $fila['cnnum'];?></td>
        <td><?php echo $fila['cennom'];?></td>
        <td><?php echo ucwords(mb_strtolower($fila['NOMBRE'])).' - '.$fila['trnum'];?></td>
        <td><?php echo $fila['cndesc'];?></td>
        <td><?php echo $debito;?></td>
        <td><?php echo $credito;?></td>
        <td><?php echo number_format($saldoT,2,'.',',');?></td>           
    </tr>
  <?php

    }?>
    <tr>
        <td colspan="6" align="right"><strong>TOTALES</strong></td>
        <td><strong><?php echo number_format($totaldeb,2,'.',',')?></strong></td>
        <td><strong><?php echo number_format($totalcred,2,'.',',')?></strong></td>
    </tr>
    <?php
   }
}
    
  ?>
</table>
</body>
</html>