<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#26/07/2018 |Erica G. | Encabezados
#29/08/2017 |Erica G. | Encabezado
#28/06/2017 |ERICA G. | ARCHIVO CREADO
#######################################################################################################
?>

<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Ejecucion_Gastos.xls");
require'../../Conexion/conexion.php';
ini_set('max_execution_time',0);
require 'consultas.php';
@session_start();
?>

<?php
$calendario     = CAL_GREGORIAN;
$mesI           = $mysqli->real_escape_string(''.$_POST['sltmesi'].'');
$mesF           = $mysqli->real_escape_string(''.$_POST['sltmesf'].'');
$codigoI        = $mysqli->real_escape_string(''.$_POST['sltcodi'].'');
$codigoF        = $mysqli->real_escape_string(''.$_POST['sltcodf'].'');
$parmanno       = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico =$parmanno";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno =$an[0]; 
$meses = array('no', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 
    'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');

 $mesInicial = $meses[(int)$mesI];
 $mesFinal = $meses[(int)$mesF];
 $annoInforme = anno($parmanno);

$meses = array( "01" => 'Enero', "02" => 'Febrero', "03" => 'Marzo',"04" => 'Abril', "05" => 'Mayo', "06" => 'Junio', 
                "07" => 'Julio', "08" => 'Agosto', "09" => 'Septiembre', "10" => 'Octubre', "11" => 'Noviembre', "12" => 'Diciembre');
    $month1 = $meses[$mesI];
    $month2 = $meses[$mesF];

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

#VACIAR LA TABLA TEMPORAL
$vaciarTabla = 'TRUNCATE temporal_consulta_pptal_gastos ';
$mysqli->query($vaciarTabla);

#CONSULTA TODAS LA CUENTAS
$ctas = "SELECT DISTINCT
            rpp.nombre,
            rpp.codi_presupuesto,
            f.id_unico,
            rpp2.codi_presupuesto, 
            rf.id_unico 
          FROM
            gf_rubro_pptal rpp
          LEFT JOIN
            gf_rubro_fuente rf ON rf.rubro = rpp.id_unico
          LEFT JOIN
            gf_fuente f ON rf.fuente = f.id_unico
          LEFT JOIN
            gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
         WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' 
         AND (rpp.tipoclase = 7  OR rpp.tipoclase = 9 OR rpp.tipoclase = 10) 
         AND rpp.parametrizacionanno = $parmanno 
        ORDER BY rpp.codi_presupuesto ASC";
$ctass= $mysqli->query($ctas);
#GUARDA LOS DATOS EN LA TABLA TEMPORAL
while ($row1 = mysqli_fetch_row($ctass)) {
    $insert= "INSERT INTO temporal_consulta_pptal_gastos "
            . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
            . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
    $mysqli->query($insert);
    
}   

##CONSULTA CUENTAS SEGUN VARIABLES QUE RECIBE
##SI SELECCIONA O NO FUENTE

$select ="SELECT DISTINCT
            rpp.nombre,
            rpp.codi_presupuesto,
            f.id_unico, 
            rpp2.codi_presupuesto, 
            dcp.rubrofuente 
          FROM
            gf_detalle_comprobante_pptal dcp
          LEFT JOIN
            gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
          LEFT JOIN
            gf_rubro_pptal rpp ON rf.rubro = rpp.id_unico
          LEFT JOIN
            gf_fuente f ON rf.fuente = f.id_unico
          LEFT JOIN
            gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
          WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF'  
        AND (rpp.tipoclase = 7  OR rpp.tipoclase = 9 OR rpp.tipoclase = 10) 
        AND rpp.parametrizacionanno = $parmanno  
        ORDER BY rpp.codi_presupuesto ASC";
$select1 = $mysqli->query($select);


while($row = mysqli_fetch_row($select1)){
##########################################################################################################################################################################    
    $fechaInicial = $anno.'-01-01';
    $diaF = cal_days_in_month($calendario, $mesF, $anno); 
    $fechaFinal = $anno.'-'.$mesF.'-'.$diaF;
    #PRESUPUESTO INICIAL
    $pptoInicial= presupuestos($row[4], 1, $fechaInicial, $fechaFinal);
    #ADICION
    $adicion = presupuestos($row[4], 2, $fechaInicial, $fechaFinal);
    #REDUCCION
    $reduccion = presupuestos($row[4], 3, $fechaInicial, $fechaFinal);
    #TRAS.CRED Y CONT.
    $tras= presupuestos($row[4], 4, $fechaInicial, $fechaFinal);
        if($tras>0){
            $trasCredito= $tras;
            $trasCont = 0;
        }else {
            $trasCredito = 0;
            $trasCont= $tras;
        }
    #PRESUPUESTO DEFINITIVO
    $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion+$trasCredito+$trasCont;
    
    
##########################################################################################################################################################################    
#   ANTERIORES
##########################################################################################################################################################################    
    $fechaIAn = $anno.'-01-01';    
    $fechaFinAn = $anno.'-'.$mesI.'-01';
    $nuevafechaAn = strtotime ( '-1 day' , strtotime ( $fechaFinAn ) ) ;
    $fechaFAn = date ( 'Y-m-d' , $nuevafechaAn );
    #DISPONIBILIDAD
    $disponibilidadAnterior = disponibilidades($row[4], 14, $fechaIAn, $fechaFinAn);
    #REGISTROS
    $registrosAnterior = disponibilidades($row[4], 15, $fechaIAn, $fechaFinAn);
    #TOTAL OBLIGACIONES
    $totalObligacionesAnterior = disponibilidades($row[4], 16, $fechaIAn, $fechaFinAn);
    #TOTAL PAGOS
    $totalPagosAnterior= disponibilidades($row[4], 17, $fechaIAn, $fechaFinAn);

##########################################################################################################################################################################    
#   ACTUALES
##########################################################################################################################################################################    
    $fechaIAc = $anno.'-'.$mesI.'-01';
    $diaFAc = cal_days_in_month($calendario, $mesF, $anno); 
    $fechaFinAc = $anno.'-'.$mesF.'-'.$diaFAc;
    #DISPONIBILIDAD
    $disponibilidadActual= disponibilidades($row[4], 14, $fechaIAc, $fechaFinAc);
    #REGISTROS
    $registrosActual = disponibilidades($row[4], 15, $fechaIAc, $fechaFinAc);
    #TOTAL OBLIGACIONES
    $totalObligacionesActual = disponibilidades($row[4], 16, $fechaIAc, $fechaFinAc);
    #TOTAL PAGOS
    $totalPagosActual= disponibilidades($row[4], 17, $fechaIAc, $fechaFinAc);

######################################################################################################################################################
#   ACUMULADO
######################################################################################################################################################
    $fechaIAcum = $anno.'-01-01';  
    $diaFAc = cal_days_in_month($calendario, $mesF, $anno); 
    $fechaFAcum = $anno.'-'.$mesF.'-'.$diaFAc;
    #DISPONIBILIDAD
    $disponibilidadAcum= disponibilidades($row[4], 14, $fechaIAcum, $fechaFAcum);
    #REGISTROS
    $registrosAcum = disponibilidades($row[4], 15, $fechaIAcum, $fechaFAcum);
    #TOTAL OBLIGACIONES
    $totalObligacionesAcum = disponibilidades($row[4], 16, $fechaIAcum, $fechaFAcum);
    #TOTAL PAGOS
    $totalPagosAcum= disponibilidades($row[4], 17, $fechaIAcum, $fechaFAcum);
######################################################################################################################################################

    
    #ACTUALIZAR TABLA CON DATOS HALLADOS
    $update="UPDATE temporal_consulta_pptal_gastos SET "
            . "ptto_inicial ='$pptoInicial', "
            . "adicion = '$adicion', "
            . "reduccion = '$reduccion', "
            . "tras_credito = '$trasCredito', "
            . "tras_cont = '$trasCont', "
            . "presupuesto_dfvo = '$presupuestoDefinitivo', "
            . "disponibilidades = '$disponibilidadAnterior', "
            . "saldo_disponible = '$disponibilidadActual', "
            . "disponibilidad_abierta = '$disponibilidadAcum', "
            . "registros = '$registrosAnterior', "
            . "registros_abiertos = '$registrosActual', "
            . "registros_otros = '$registrosAcum', "
            . "total_obligaciones = '$totalObligacionesAnterior', "
            . "reservas = '$totalObligacionesActual', "
            . "cuentas_x_pagar = '$totalObligacionesAcum', "
            . "total_pagos = '$totalPagosAnterior', "
            . "recaudos = '$totalPagosActual', "
            . "saldos_x_recaudar = '$totalPagosAcum' "
            . "WHERE rubro_fuente = '$row[4]'";
    $update = $mysqli->query($update);
          
}   

#CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
 $acum = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, "
        . "adicion, reduccion, "
        . "tras_credito, tras_cont, "
        . "presupuesto_dfvo, "
        . "disponibilidades, "
        . "saldo_disponible, "
        . "disponibilidad_abierta, "
        . "registros, "
        . "registros_abiertos, "
        . "registros_otros, "
        . "total_obligaciones, "
        . "reservas, "
        . "cuentas_x_pagar,"
        . "total_pagos,"
        . "recaudos, "
        . "saldos_x_recaudar "
        . "FROM temporal_consulta_pptal_gastos "
        . "ORDER BY cod_rubro DESC ";
$acum = $mysqli->query($acum);

while ($rowa1= mysqli_fetch_row($acum)){
    $acumd = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, "
        . "adicion, reduccion, "
        . "tras_credito, tras_cont, "
        . "presupuesto_dfvo, "
        . "disponibilidades, "
        . "saldo_disponible, "
        . "disponibilidad_abierta, "
        . "registros, "
        . "registros_abiertos, "
        . "registros_otros, "
        . "total_obligaciones, "
        . "reservas, "
        . "cuentas_x_pagar,"
        . "total_pagos,"
        . "recaudos, "
        . "saldos_x_recaudar "
        . "FROM temporal_consulta_pptal_gastos WHERE id_unico ='$rowa1[0]' "
        . "ORDER BY cod_rubro DESC ";
    $acumd = $mysqli->query($acumd);
    while ($rowa= mysqli_fetch_row($acumd)){
        if(!empty($rowa[2])){
            $va11= "SELECT id_unico, "
            . "cod_rubro,"
            . "cod_predecesor, "
            . "ptto_inicial, "
            . "adicion, reduccion, "
            . "tras_credito, tras_cont, "
            . "presupuesto_dfvo, "
            . "disponibilidades, "
            . "saldo_disponible, "
            . "disponibilidad_abierta, "
            . "registros, "
            . "registros_abiertos, "
            . "registros_otros, "
            . "total_obligaciones, "
            . "reservas, "
            . "cuentas_x_pagar,"
            . "total_pagos,"
            . "recaudos, "
            . "saldos_x_recaudar "
            . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
        $va1 = $mysqli->query($va11);
        $va= mysqli_fetch_row($va1);
        $pptoInicialM = $rowa[3]+$va[3];
        $adicionM = $rowa[4]+$va[4];
        $reduccionM = $rowa[5]+$va[5];
        $trasCreditoM = $rowa[6]+$va[6];
        $trasContM = $rowa[7]+$va[7];
        $presupuestoDefinitivoM = $rowa[8]+$va[8];
        $disponibilidadAnteriorM = $rowa[9]+$va[9];
        $disponibilidadActualM = $rowa[10]+$va[10];
        $disponibilidadAcumM = $rowa[11]+$va[11];
        $registrosAnteriorM = $rowa[12]+$va[12];
        $registrosActualM = $rowa[13]+$va[13];
        $registrosAcumM = $rowa[14]+$va[14];
        $totalObligacionesAnteriorM = $rowa[15]+$va[15];
        $totalObligacionesActualM = $rowa[16]+$va[16];
        $totalObligacionesAcumM = $rowa[17]+$va[17];
        $totalPagosAnteriorM = $rowa[18]+$va[18];
        $totalPagosActualM = $rowa[19]+$va[19];
        $totalPagosAcumM = $rowa[20]+$va[20];
        
        #ACTUALIZAR TABLA CON DATOS HALLADOS
        $updateA="UPDATE temporal_consulta_pptal_gastos SET "
                . "ptto_inicial ='$pptoInicialM', "
                . "adicion = '$adicionM', "
                . "reduccion = '$reduccionM', "
                . "tras_credito = '$trasCreditoM', "
                . "tras_cont = '$trasContM', "
                . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
                . "disponibilidades = '$disponibilidadAnteriorM', "
                . "saldo_disponible = '$disponibilidadActualM', "
                . "disponibilidad_abierta = '$disponibilidadAcumM', "
                . "registros = '$registrosAnteriorM', "
                . "registros_abiertos = '$registrosActualM', "
                . "registros_otros = '$registrosAcumM', "
                . "total_obligaciones = '$totalObligacionesAnteriorM', "
                . "reservas = '$totalObligacionesActualM', "
                . "cuentas_x_pagar = '$totalObligacionesAcumM', "
                . "total_pagos = '$totalPagosAnteriorM', "
                . "recaudos = '$totalPagosActualM', "
                . "saldos_x_recaudar = '$totalPagosAcumM' "
                . "WHERE cod_rubro = '$rowa[2]'";
        $updateA = $mysqli->query($updateA);
    }
    }
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ejecución de Gastos</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr>
        <th colspan="21" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
            <br/>EJECUCION DEL PRESUPUESTO DE GASTOS E INVERSIONES POR PERIODO<br/>RUBROS DEL <?php echo $codigoI.' al '.$codigoF ?>
            <br/>De <?php echo $mesInicial.' A '.$mesFinal .' - '.$annoInforme; ?><br/>&nbsp;</strong></th>
  </tr>
  <tr>
        <td rowspan ="2" align="center"><strong>RUBRO</strong></td>
        <td rowspan ="2" align="center"><strong>DETALLE</strong></td>
        <td rowspan ="2" align="center"><strong>FUENTE</strong></td> 
        <td rowspan ="2" align="center"><strong>PRESUPUESTO INICIAL</strong></td>
        <td colspan ="4" align="center"><strong>MODIFICACIONES PRESUPUESTALES</strong></td>
        <td rowspan ="2" align="center"><strong>PRESUPUESTO DEFINITIVO</strong></td>
        <td colspan ="3" align="center"><strong>DISPONIBILIDADES</strong></td>
        <td colspan ="3" align="center"><strong>REGISTROS</strong></td>
        <td colspan ="3" align="center"><strong>OBLIGACIONES</strong></td>
        <td colspan ="3" align="center"><strong>PAGOS</strong></td>
    </tr>
  <tr>
        <td  align="center"><strong>ADICION</strong></td>
        <td  align="center"><strong>REDUCCION</strong></td>
        <td  align="center"><strong>TRAS.CREDITO</strong></td> 
        <td  align="center"><strong>TRAS.CONT</strong></td>
        <td  align="center"><strong>ANTERIOR</strong></td>
        <td  align="center"><strong>ACTUAL</strong></td>
        <td  align="center"><strong>ACUMULADO</strong></td>
        <td  align="center"><strong>ANTERIOR</strong></td>
        <td  align="center"><strong>ACTUAL</strong></td>
        <td  align="center"><strong>ACUMULADO</strong></td>
        <td  align="center"><strong>ANTERIOR</strong></td>
        <td  align="center"><strong>ACTUAL</strong></td>
        <td  align="center"><strong>ACUMULADO</strong></td>
        <td  align="center"><strong>ANTERIOR</strong></td>
        <td  align="center"><strong>ACTUAL</strong></td>
        <td  align="center"><strong>ACUMULADO</strong></td>
    </tr>
  <?php   
#Consulta Cuentas
$sql2 = "SELECT DISTINCT 
                        cod_rubro               as codrub, 
                        nombre_rubro            as nomrub,
                        cod_fuente              as codfte,
                        ptto_inicial            as ppti,
                        adicion                 as adi,
                        reduccion               as red,
                        tras_credito            as tcred,
                        tras_cont               as trcont,
                        presupuesto_dfvo        as ppdf,
                        disponibilidades        as disanterior,
                        saldo_disponible        as disactual,
                        disponibilidad_abierta  as disacum, 
                        registros               as reganterior,
                        registros_abiertos      as regactual,
                        registros_otros         as regacum, 
                        total_obligaciones      as oblianterior,
                        reservas                as obliactual,
                        cuentas_x_pagar         as obliacum, 
                        total_pagos             as paganterior,
                        recaudos                as pagactual, 
                        saldos_x_recaudar       as pagosacum 
                        
from temporal_consulta_pptal_gastos ORDER BY cod_rubro ASC";
$conejc  = $mysqli->query($sql2);

while ($filactas = mysqli_fetch_array($conejc)) 
{

    $p1  = (float) $filactas['ppti'];
    $p2  = (float) $filactas['adi'];
    $p3  = (float) $filactas['red'];
    $p4  = (float) $filactas['tcred'];
    $p5  = (float) $filactas['trcont'];
    $p6  = (float) $filactas['ppdf'];
    $disan  = (float) $filactas['disanterior'];
    $disac  = (float) $filactas['disactual'];
    $disam  = (float) $filactas['disacum'];
    $regan  = (float) $filactas['reganterior'];
    $regac  = (float) $filactas['regactual'];
    $regam  = (float) $filactas['regacum'];
    $oblan  = (float) $filactas['oblianterior'];
    $oblac  = (float) $filactas['obliactual'];
    $oblam  = (float) $filactas['obliacum'];
    $pagan  = (float) $filactas['paganterior'];
    $pagac  = (float) $filactas['pagactual'];
    $pagam  = (float) $filactas['pagosacum'];
       # $codd = $codd + 1;
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 && 
            $disan==0 && $disac==0 && $disam==0 && $regan==0 && $regac==0 && $regam==0 && 
            $oblan==0 && $oblac==0 && $oblam==0 && $pagan==0 && $pagac==0 && $pagam==0)
        { } else { ?>
    <tr>
        <td><?php echo $filactas['codrub'];?></td>
        <td><?php echo $filactas['nomrub'];?></td>
        <td align="center"><?php echo $filactas['codfte'];?></td>
        <td align="right" ><?php echo number_format($p1 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p2 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p3 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p4 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p5 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p6 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($disan ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($disac ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($disam ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($regan ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($regac,2,'.',',');?></td>
        <td align="right"><?php echo number_format($regam,2,'.',',');?></td>
        <td align="right"><?php echo number_format($oblan,2,'.',',');?></td>
        <td align="right"><?php echo number_format($oblac,2,'.',',');?></td>
        <td align="right"><?php echo number_format($oblam,2,'.',',');?></td>
        <td align="right"><?php echo number_format($pagan,2,'.',',');?></td>
        <td align="right"><?php echo number_format($pagac,2,'.',',');?></td>
        <td align="right"><?php echo number_format($pagam,2,'.',',');?></td>
    </tr>
    <?php
    }
}
?>
</table>
</body>
</html>