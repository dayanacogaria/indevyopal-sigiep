<?php
#############MODIFICACIONES###################
#08/03/2017 |ERICA G. |CONSULTAS
##############################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Ingresos_SIFSE.xls");
require'../../Conexion/conexion.php';
require'consultas.php';
ini_set('max_execution_time', 0);

$calendario     = CAL_GREGORIAN;
$parmanno = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico =$parmanno";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno =$an[0]; 
$mes            = $mysqli->real_escape_string(''.$_POST['sltmes'].'');
$dia            = cal_days_in_month($calendario, $mes, $anno); 
$fecha          = $anno.'-'.$mes.'-'.$dia;
$fechaInicial   = $anno.'-'.'01-01';
$codigoI        = $mysqli->real_escape_string(''.$_POST['sltcodi'].'');
$codigoF        = $mysqli->real_escape_string(''.$_POST['sltcnf'].'');


#VACIAR LA TABLA TEMPORAL
$vaciarTabla = 'TRUNCATE temporal_consulta_pptal_gastos ';
$mysqli->query($vaciarTabla);

#CONSULTA TODAS LA CUENTAS
$ctas = "SELECT DISTINCT
            rpp.nombre,
            rpp.codi_presupuesto,
            f.id_unico,
            rpp2.codi_presupuesto, 
            rf.id_unico, rpp.equivalente, f.equivalente  
          FROM
            gf_rubro_pptal rpp
          LEFT JOIN
            gf_rubro_fuente rf ON rf.rubro = rpp.id_unico
          LEFT JOIN
            gf_fuente f ON rf.fuente = f.id_unico
          LEFT JOIN
            gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
            WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' 
            AND rpp.parametrizacionanno = $parmanno";
$ctass= $mysqli->query($ctas);
#GUARDA LOS DATOS EN LA TABLA TEMPORAL
while ($row1 = mysqli_fetch_row($ctass)) {
    if(empty($row1[5])){
        $row1[5] = NULL;
    }if(empty($row1[6])){
        $row1[6] = NULL;
    }
    $insert= "INSERT INTO temporal_consulta_pptal_gastos "
            . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente, equivalente_rubro, equivalente_fuente) "
            . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]','$row1[5]','$row1[6]' )";
    $mysqli->query($insert);   
}
    
#CONSULTA CUENTAS DEL DETALLE SEGUN VARIABLES QUE RECIBE
echo $select ="SELECT DISTINCT
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
            gf_tercero t ON dcp.tercero = t.id_unico 
          LEFT JOIN 
            gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
          WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF'  
            AND cp.parametrizacionanno = $parmanno 
            AND rpp.parametrizacionanno = $parmanno";
$select1 = $mysqli->query($select);


while($row = mysqli_fetch_row($select1)){
    
    #PRESUPUESTO INICIAL
    $pptoInicial= presupuestos($row[4], 1, $fechaInicial, $fecha);
    
    #ADICION
    $adicion = presupuestos($row[4], 2, $fechaInicial, $fecha);
    #REDUCCION
    $reduccion = presupuestos($row[4], 3, $fechaInicial, $fecha);
    
    #PRESUPUESTO DEFINITIVO
    $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion;
    #RECAUDOS
    $recaudos = disponibilidades($row[4], 18, $fechaInicial, $fecha);
    #SALDOS POR RECAUDAR
    $saldos = $pptoInicial-$recaudos;
    
    #ACTUALIZAR TABLA CON DATOS HALLADOS
    $update="UPDATE temporal_consulta_pptal_gastos SET "
            . "ptto_inicial ='$pptoInicial', "
            . "adicion = '$adicion', "
            . "reduccion = '$reduccion', "
            . "presupuesto_dfvo = '$presupuestoDefinitivo', "
            . "recaudos = '$recaudos', "
            . "saldos_x_recaudar = '$saldos' "
            . "WHERE rubro_fuente = '$row[4]'";
    $update = $mysqli->query($update);
}   


  $annio = $anno;
  $compania = $_SESSION['compania'];
    
  $consulta = "SELECT           t.razonsocial as traz,
                                t.tipoidentificacion as tide,
                                ti.id_unico as tid,
                                ti.nombre as tnom,
                                t.numeroidentificacion tnum, 
                                t.codigo_dane as codigo 
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";

$cmp = $mysqli->query($consulta);

$fila = mysqli_fetch_array($cmp);
        $nomcomp = utf8_decode($fila['traz']);       
        $tipodoc = utf8_decode($fila['tnom']);       
        $numdoc = utf8_decode($fila['tnum']);   
        $codDane = utf8_decode($fila['codigo']);   
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

switch($mes){
    case 3:
        $month1 = "Primer Trimestre";
    break;
    case 6:
        $month1 = "Segundo Trimestre";
    break;
    case 9:
        $month1 = "Tercer Trimestre";
    break;
    case 12:
        $month1 = "Cuarto Trimestre";
    break;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ingresos SIFSE</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th colspan="7" align="center"><strong>
        <br/>&nbsp;
        <br/><?php echo $razonsocial ?>
        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
        <br/>&nbsp;
        <br/>REPORTE DE INGRESOS SIFSE
        <br/><?php echo $month1; ?><br/>&nbsp;</strong></th>
    </tr>
  <tr>
    <td><strong>CODIGO ESTABLECIMIENTO</strong></td>
    <td><strong>AÑO</strong></td>
    <td><strong>TRIMESTRE</strong></td>
    <td><strong>FUENTE DE INGRESOS</strong></td>
    <td><strong>PRESUPUESTO INICIAL</strong></td>
    <td><strong>PRESUPUESTO DEFINITIVO</strong></td>
    <td><strong>MONTO RECAUDOS</strong></td>
  </tr>
  
<?PHP
		
$sql2 = "SELECT equivalente_fuente AS equivF, 
       SUM(ptto_inicial) AS pInicial, 
       SUM(presupuesto_dfvo) AS pDefinitivo, 
       SUM(recaudos) AS recaudos  
FROM temporal_consulta_pptal_gastos
WHERE equivalente_fuente IS NOT NULL OR equivalente_fuente != ''
GROUP BY equivalente_fuente 

UNION ALL

SELECT equivalente_fuente AS equivF, 
       ptto_inicial AS pInicial, 
       presupuesto_dfvo AS pDefinitivo,
       recaudos AS recaudos 
FROM temporal_consulta_pptal_gastos
WHERE equivalente_fuente IS NULL OR equivalente_fuente = ''";
$conejc  = $mysqli->query($sql2);
while($filactas=mysqli_fetch_array($conejc)){		
    $p1  = (float) $filactas['pInicial'];
    $p2  = (float) $filactas['pDefinitivo'];
    $p3  = (float) $filactas['recaudos'];
    switch ($mes){
        case 3:
            $trim= '01';
            break;
        case 6:
            $trim= '02';
            break;
        case 9:
            $trim= '03';
            break;
        case 12:
            $trim= '04';
            break;
    }
    $equivF=$filactas['equivF'];
       # $codd = $codd + 1;
    if($p1==0 && $p2 ==0 && $p3==0  ){} else {
        				

?>  
 <tr>
	<td><?php echo $codDane; ?></td>
	<td><?php echo $anno; ?></td>
	<td><?php echo $trim; ?></td>
	<td><?php echo $filactas['equivF']; ?></td>
	<td><?php echo $p1; ?></td>
        <td><?php echo $p2; ?></td>
        <td><?php echo $p3; ?></td>
 </tr> 
  <?php

    }
}
    
  ?> 
</table>
</body>
</html>