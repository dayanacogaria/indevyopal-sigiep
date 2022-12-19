
<?php

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Ejec_Pptal_Gasto_Invr_Acumulado.xls");
require_once("../../Conexion/conexion.php");
require'consultas.php';
ini_set('max_execution_time', 0);

$calendario = CAL_GREGORIAN;
$parmanno = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico =$parmanno";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno =$an[0]; 
$mes            = $mysqli->real_escape_string(''.$_POST['sltmes'].'');
$dia            = cal_days_in_month($calendario, $mes, $anno); 
$fecha          = $anno.'-'.$mes.'-'.$dia;
$fechaInicial   = $anno.'-'.'01-01';
$codigoI        = $mysqli->real_escape_string(''.$_POST['sltcni'].'');
$codigoF        = $mysqli->real_escape_string(''.$_POST['sltcnf'].'');
$meses = array( "01" => 'Enero', "02" => 'Febrero', "03" => 'Marzo',"04" => 'Abril', "05" => 'Mayo', "06" => 'Junio', 
                "07" => 'Julio', "08" => 'Agosto', "09" => 'Septiembre', "10" => 'Octubre', "11" => 'Noviembre', "12" => 'Diciembre');
    $month1 = $meses[$mes];
    
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>EJECUCIÓN PPTAL GASTOS</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th colspan="19" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
           <br/>EJECUCIÓN PRESUPUESTAL GASTOS E INVERSIONES ACUMULADO
           <br/>Rubros del <?php echo $codigoI.' al '.$codigoF ?>
           <br/>Mes Acumulado <?php echo $month1.' - '.$anno ?><br/>&nbsp;</strong>
        </th>
  </tr>
  <tr>
        <td rowspan="2" align="center"><strong>RUBRO</strong></td>
        <td colspan="1" rowspan="2"align="center"><strong>DETALLE</strong></td>
        <td rowspan="2" align="center"><strong>FUENTE</strong></td> 
        <td colspan ="2" rowspan="2" align="center"><strong>PRESUPUESTO INICIAL</strong></td>
        <td colspan ="4" align="center"><strong>MODIFICACIONES PRESUPUESTALES</strong></td>
        <td rowspan="2" align="center"><strong>PRESUPUESTO DEFINITIVO</strong></td>
        <td rowspan="2" align="center"><strong>DISPONIBILIDADES</strong></td>
        <td rowspan="2" align="center"><strong>SALDO DISPONIBLE</strong></td>
        <td rowspan="2" align="center"><strong>DISPONIBILIDADES ABIERTAS</strong></td>
        <td rowspan="2" align="center"><strong>REGISTROS</strong></td>
        <td rowspan="2" align="center"><strong>REGISTROS ABIERTOS</strong></td>
        <td rowspan="2" align="center"><strong>TOTAL OBLIGACIONES</strong></td>
        <td rowspan="2" align="center"><strong>TOTAL PAGOS</strong></td>
        <td rowspan="2" align="center"><strong>RESERVAS</strong></td>
        <td rowspan="2" align="center"><strong>CUENTAS POR PAGAR</strong></td>
    </tr>
  <tr>
        <td  align="center"><strong>ADICION</strong></td>
        <td  align="center"><strong>REDUCCION</strong></td>
        <td  align="center"><strong>TRAS.CREDITO</strong></td> 
        <td  align="center"><strong>TRAS.CONT</strong></td>
    </tr>
<?php

#VACIAR LA TABLA TEMPORAL
$vaciarTabla = 'TRUNCATE temporal_consulta_pptal_gastos ';
$mysqli->query($vaciarTabla);

#CONSULTA TODAS LA CUENTAS
$ctas = "SELECT DISTINCT
            rpp.nombre,
            rpp.codi_presupuesto,
            f.nombre,
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
         WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF'"
        . "AND rpp.parametrizacionanno = $parmanno  
         AND (rpp.tipoclase = 7 ) 
         AND rpp.tipovigencia =1
         ORDER BY rpp.codi_presupuesto ASC";
$ctass= $mysqli->query($ctas);
#GUARDA LOS DATOS EN LA TABLA TEMPORAL
while ($row1 = mysqli_fetch_row($ctass)) {
    $insert= "INSERT INTO temporal_consulta_pptal_gastos "
            . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
            . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
    $mysqli->query($insert);
    
}
    
#CONSULTA CUENTAS SEGUN VARIABLES QUE RECIBE
#CONSULTA CUENTAS SEGUN VARIABLES QUE RECIBE
#SI SELECCIONA O NO FUENTE
if(empty($_POST['fuente'])){
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
          LEFT JOIN 
            gf_comprobante_pptal cp ON cp.id_unico = dcp.comprobantepptal 
          WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' "
        . "AND cp.parametrizacionanno = $parmanno ORDER BY rpp.codi_presupuesto ASC";
}else {
    $fuente = $_POST['fuente'];
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
          LEFT JOIN 
            gf_comprobante_pptal cp ON cp.id_unico = dcp.comprobantepptal 
          WHERE f.id_unico ='$fuente' AND rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' "
         . "AND cp.parametrizacionanno = $parmanno ORDER BY rpp.codi_presupuesto ASC";
   
}
$select1 = $mysqli->query($select);


while($row = mysqli_fetch_row($select1)){
    
    #PRESUPUESTO INICIAL
    $pptoInicial= presupuestos($row[4], 1, $fechaInicial, $fecha);
    #ADICION
    $adicion = presupuestos($row[4], 2, $fechaInicial, $fecha);
    #REDUCCION
    $reduccion = presupuestos($row[4], 3, $fechaInicial, $fecha);
    #TRAS.CRED Y CONT.
    $trasCredito = 0;
    $trasCont    = 0;
    $query = "SELECT valor as value 
                FROM
                  gf_detalle_comprobante_pptal dc
                LEFT JOIN
                  gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                WHERE
                  dc.rubrofuente = '$row[4]' 
                  AND tcp.tipooperacion = '4' 
                  AND cp.fecha BETWEEN '$fechaInicial' AND '$fecha' 
                  AND (tcp.clasepptal = '13')";
    $ap = $GLOBALS['mysqli']->query($query);
    if(mysqli_num_rows($ap)>0){
        while ($sum1= mysqli_fetch_array($ap)) {
            $tras = $sum1['value'];
            if($tras>0){
                $trasCredito += $tras;
            }else {
                $trasCont    += $tras;
            }
        }
    }
    #PRESUPUESTO DEFINITIVO
    $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion+$trasCredito+$trasCont;
    #DISPONIBILIDAD
    $disponibilidad = disponibilidades($row[4], 14, $fechaInicial, $fecha);
    #SALDO DISPONIBLE
    $saldoDisponible= $presupuestoDefinitivo-$disponibilidad;
    #REGISTROS
    $registros = disponibilidades($row[4], 15, $fechaInicial, $fecha);
    #REGISTROS ABIERTOS
    $disponibilidadesAbiertas = $disponibilidad-$registros;
    #TOTAL OBLIGACIONES
    $totalObligaciones = disponibilidades($row[4], 16, $fechaInicial, $fecha);
    #REGISTROS ABIERTOS
    $registrosAbiertos = $registros-$totalObligaciones;
    #TOTAL PAGOS
    $totalPagos= disponibilidades($row[4], 17, $fechaInicial, $fecha);
    #RESERVAS
    $reservas= $registros-$totalObligaciones;
    #CUENTAS POR PAGAR
    $cuentasxpagar = $totalObligaciones-$totalPagos;
    
    #ACTUALIZAR TABLA CON DATOS HALLADOS
    $update="UPDATE temporal_consulta_pptal_gastos SET "
            . "ptto_inicial ='$pptoInicial', "
            . "adicion = '$adicion', "
            . "reduccion = '$reduccion', "
            . "tras_credito = '$trasCredito', "
            . "tras_cont = '$trasCont', "
            . "presupuesto_dfvo = '$presupuestoDefinitivo', "
            . "disponibilidades = '$disponibilidad', "
            . "saldo_disponible = '$saldoDisponible', "
            . "disponibilidad_abierta = '$disponibilidadesAbiertas', "
            . "registros = '$registros', "
            . "registros_abiertos = '$registrosAbiertos', "
            . "total_obligaciones = '$totalObligaciones', "
            . "total_pagos = '$totalPagos', "
            . "reservas = '$reservas', "
            . "cuentas_x_pagar = '$cuentasxpagar' "
            . "WHERE rubro_fuente = '$row[4]'";
    $update = $mysqli->query($update);
          
}   
#CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
 $acum = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, tras_credito, tras_cont, "
        . "presupuesto_dfvo, disponibilidades, "
        . "saldo_disponible,registros, "
        . "registros_abiertos,total_obligaciones, "
        . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
        . "FROM temporal_consulta_pptal_gastos "
        . "ORDER BY cod_rubro DESC ";
$acum = $mysqli->query($acum);

while ($rowa1= mysqli_fetch_row($acum)){
    $acumd = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, tras_credito, tras_cont, "
        . "presupuesto_dfvo, disponibilidades, "
        . "saldo_disponible,registros, "
        . "registros_abiertos,total_obligaciones, "
        . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
        . "FROM temporal_consulta_pptal_gastos WHERE id_unico ='$rowa1[0]' "
        . "ORDER BY cod_rubro DESC ";
    $acumd = $mysqli->query($acumd);
    while ($rowa= mysqli_fetch_row($acumd)){
        if(!empty($rowa[2])){
            $va11= "SELECT id_unico, "
            . "cod_rubro,"
            . "cod_predecesor, "
            . "ptto_inicial, adicion, tras_credito, tras_cont, "
            . "presupuesto_dfvo, disponibilidades, "
            . "saldo_disponible,registros, "
            . "registros_abiertos,total_obligaciones, "
            . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
            . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
        $va1 = $mysqli->query($va11);
        $va= mysqli_fetch_row($va1);
        $pptoInicialM = $rowa[3]+$va[3];
        $adicionM = $rowa[4]+$va[4];
        $trasCreditoM = $rowa[5]+$va[5];
        $trasContM = $rowa[6]+$va[6];
        $presupuestoDefinitivoM = $rowa[7]+$va[7];
        $disponibilidadM = $rowa[8]+$va[8];
        $saldoDisponibleM = $rowa[9]+$va[9];
        $registrosM = $rowa[10]+$va[10];
        $registrosAbiertosM = $rowa[11]+$va[11];
        $totalObligacionesM = $rowa[12]+$va[12];
        $totalPagosM = $rowa[13]+$va[13];
        $reservasM = $rowa[14]+$va[14];
        $cuentasxpagarM = $rowa[15]+$va[15];
        $reduccionM = $rowa[16]+$va[16];
        $disponibilidadAbiertaM = $rowa[17]+$va[17];
        #ACTUALIZAR TABLA CON DATOS HALLADOS
        $updateA="UPDATE temporal_consulta_pptal_gastos SET "
                . "ptto_inicial ='$pptoInicialM', "
                . "adicion = '$adicionM', "
                . "reduccion = '$reduccionM', "
                . "tras_credito = '$trasCreditoM', "
                . "tras_cont = '$trasContM', "
                . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
                . "disponibilidades = '$disponibilidadM', "
                . "saldo_disponible = '$saldoDisponibleM', "
                . "disponibilidad_abierta = '$disponibilidadAbiertaM', "
                . "registros = '$registrosM', "
                . "registros_abiertos = '$registrosAbiertosM', "
                . "total_obligaciones = '$totalObligacionesM', "
                . "total_pagos = '$totalPagosM', "
                . "reservas = '$reservasM', "
                . "cuentas_x_pagar = '$cuentasxpagarM' "
                . "WHERE cod_rubro = '$rowa[2]'";
        $updateA = $mysqli->query($updateA);
    }
    }
}


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
                        disponibilidades        as disp,
                        saldo_disponible        as sald,
                        registros               as reg,
                        registros_abiertos      as rega,
                        total_obligaciones      as tobl,
                        total_pagos             as tpag,
                        reservas                as reserv,
                        cuentas_x_pagar         as cpag,
                        disponibilidad_abierta  as disAb 
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
    $p7  = (float) $filactas['disp'];
    $p8  = (float) $filactas['sald'];
    $p9  = (float) $filactas['reg'];
    $p10 = (float) $filactas['rega'];
    $p11 = (float) $filactas['tobl'];
    $p12 = (float) $filactas['tpag'];
    $p13 = (float) $filactas['reserv'];
    $p14 = (float) $filactas['cpag'];
    $p15 = (float) $filactas['disAb'];
       # $codd = $codd + 1;
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 && $p7==0 && $p8==0 && $p9==0 && $p10==0 && $p11==0 && $p12==0 && $p13==0)
        { } else {
 ?>           
   <tr>
        <td><?php echo $filactas['codrub'];?></td>
        <td><?php echo $filactas['nomrub'];?></td>
        <td align="center"><?php echo $filactas['codfte'];?></td>
        <td align="right" colspan="2"><?php echo number_format($p1 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p2 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p3 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p4 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p5 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p6 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p7 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p8 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p15 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p9 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p10,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p11,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p12,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p13,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p14,2,'.',',');?></td>
    </tr>
<?php    
        }
    }
?>
</table>
</body>
</html>
