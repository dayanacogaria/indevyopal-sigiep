<?php
##########################################################################################################################################################################
#                                                                                              Modificaciones
##########################################################################################################################################################################
#17/11/2017 |ERICA G.|INFORME PAC
#29/08/2017 | Erica G. | Encabezado
#07/03/2017 |ERICA G.|CONSULTAS A FUNCION
#02-02-2017 | 9:30 | Erica González //Modificacion búsqueda disponibilidades
##########################################################################################################################################################################

?>
<?php

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Ejec_Pptal_Rentas_Ingresos.xls");
require_once("../../Conexion/conexion.php");
require'consultas.php';
ini_set('max_execution_time', 0);
$calendario = CAL_GREGORIAN;
$parmanno = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico =$parmanno";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno =$an[0]; 
$mes = $mysqli->real_escape_string(''.$_POST['sltmes'].'');
$dia = cal_days_in_month($calendario, $mes, $anno); 
$fecha = $anno.'-'.$mes.'-'.$dia;
$fechaInicial = $anno.'-'.'01-01';
$codigoI =$mysqli->real_escape_string(''.$_POST['sltcodi'].'');
$codigoF=$mysqli->real_escape_string(''.$_POST['sltcnf'].'');
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
$tipo = $_POST['tipo']; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>EJECUCIÓN PRESUPUESTAL RENTAS E INGRESOS</title>
</head>
<body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <tr>
        <?php if ($tipo ==1) { ?>
        <th colspan="8" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
            <br/>&nbsp;
            <br/>INFORME PAC INGRESOS
            <br/>Rubros del <?php echo $codigoI.' al '.$codigoF ?>
            <br/>Mes Acumulado <?php echo $month1.' - '.$anno ?><br/>&nbsp;</strong>
        </th>
        <?PHP }else { ?>
        <th colspan="9" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
            <br/>&nbsp;
            <br/>EJECUCIÓN PRESUPUESTAL RENTAS E INGRESOS
            <br/>Rubros del <?php echo $codigoI.' al '.$codigoF ?>
            <br/>Mes Acumulado <?php echo $month1.' - '.$anno ?><br/>&nbsp;</strong>
        </th>
        <?php }?>
  </tr>
    <tr>
        <td rowspan="2" align="center"><strong>RUBRO</strong></td>
        <td colspan="1" rowspan="2"align="center"><strong>DETALLE</strong></td>
        <td colspan="1" rowspan="2"align="center"><strong>FUENTE</strong></td>
        <?php if ($tipo ==1) { ?>
        <td colspan ="1" rowspan="2" align="center"><strong>PAC INICIAL</strong></td>   
        <td colspan ="2" align="center"><strong>MODIFICACIONES PAC</strong></td>
        <td rowspan="2" style="width:120px;" align="center"><strong>PAC DEFINITIVO</strong></td>
        <td rowspan="2" style="width:120px;" align="center"><strong>PAC RECAUDADO</strong></td>
        <?php }  else { ?>
        <td colspan ="1" rowspan="2" align="center"><strong>PRESUPUESTO INICIAL</strong></td>   
        <td colspan ="2" align="center"><strong>MODIFICACIONES PRESUPUESTALES</strong></td>
        <td rowspan="2" style="width:120px;" align="center"><strong>PRESUPUESTO DEFINITIVO</strong></td>
        <td rowspan="2" style="width:120px;" align="center"><strong>RECAUDO</strong></td>
        <td rowspan="2" style="width:120px;" align="center"><strong>SALDOS POR RECAUDAR</strong></td>
        <?php } ?>
        
    </tr>
    <tr>
        <td  align="center"><strong>ADICION</strong></td>
        <td  align="center"><strong>REDUCCION</strong></td>
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
            WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' 
                AND rpp.tipoclase = 6 
        AND rpp.parametrizacionanno = '$parmanno' ORDER BY rpp.codi_presupuesto ASC";
$ctass= $mysqli->query($ctas);
#GUARDA LOS DATOS EN LA TABLA TEMPORAL
while ($row1 = mysqli_fetch_row($ctass)) {
    $insert= "INSERT INTO temporal_consulta_pptal_gastos "
            . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
            . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
    $mysqli->query($insert);
    
}
    
#CONSULTA CUENTAS DEL DETALLE SEGUN VARIABLES QUE RECIBE
#SI RECIBE O NO FUENTE
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
        . "AND cp.parametrizacionanno = $parmanno";
} else {
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
           . "AND cp.parametrizacionanno = $parmanno"; 
}
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
    $saldos = $presupuestoDefinitivo-$recaudos;
    
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
//#CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
$acum = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, reduccion, "
        . "presupuesto_dfvo, recaudos, "
        . "saldos_x_recaudar "
        . "FROM temporal_consulta_pptal_gastos "
        . "ORDER BY cod_rubro DESC ";
$acum = $mysqli->query($acum);

while ($rowa1= mysqli_fetch_row($acum)){
    $acumd = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, reduccion, "
        . "presupuesto_dfvo, recaudos, "
        . "saldos_x_recaudar "
        . "FROM temporal_consulta_pptal_gastos WHERE id_unico ='$rowa1[0]' "
        . "ORDER BY cod_rubro DESC ";
    $acumd = $mysqli->query($acumd);
    while ($rowa= mysqli_fetch_row($acumd)){
        if(!empty($rowa[2])){
            $va11= "SELECT id_unico, "
            . "cod_rubro,"
            . "cod_predecesor, "
            . "ptto_inicial, adicion, reduccion, "
            . "presupuesto_dfvo, recaudos, "
            . "saldos_x_recaudar "
            . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
        $va1 = $mysqli->query($va11);
        $va= mysqli_fetch_row($va1);
        $pptoInicialM = $rowa[3]+$va[3];
        $adicionM = $rowa[4]+$va[4];
        $reduccionM = $rowa[5]+$va[5];
        $presupuestoDefinitivoM = $rowa[6]+$va[6];
        $recaudosM = $rowa[7]+$va[7];
        $saldosM = $rowa[8]+$va[8];
        
        #ACTUALIZAR TABLA CON DATOS HALLADOS
        $updateA="UPDATE temporal_consulta_pptal_gastos SET "
                . "ptto_inicial ='$pptoInicialM', "
                . "adicion = '$adicionM', "
                . "reduccion = '$reduccionM', "
                . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
                . "recaudos = '$recaudosM', "
                . "saldos_x_recaudar = '$saldosM' "
                . "WHERE cod_rubro = '$rowa[2]'";
        $updateA = $mysqli->query($updateA);
        }
    }
}


#Consulta Cuentas
$sql2 = "SELECT DISTINCT 
                        cod_rubro           as codrub, 
                        nombre_rubro        as nomrub,
                        ptto_inicial        as ppti,
                        adicion             as adi,
                        reduccion           as red,
                        presupuesto_dfvo    as ppdf,
                        recaudos            as reca,
                        reservas            as reserv,
                        saldos_x_recaudar   as spag,
                        cod_fuente          as fuente  
from temporal_consulta_pptal_gastos ORDER BY cod_rubro ASC";
$conejc  = $mysqli->query($sql2);

while ($filactas = mysqli_fetch_array($conejc)) 
{
    
    $p1  = (float) $filactas['ppti'];
    $p2  = (float) $filactas['adi'];
    $p3  = (float) $filactas['red'];
    $p4  = (float) $filactas['ppdf'];
    $p5  = (float) $filactas['reca'];
    $p6  = (float) $filactas['spag'];
       # $codd = $codd + 1;
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0)
        { } else {
 ?>           
   <tr>
        <td><?php echo $filactas['codrub'];?></td>
        <td><?php echo $filactas['nomrub'];?></td>
        <td><?php echo $filactas['fuente'];?></td>
        <td align="right"><?php echo number_format($p1 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p2 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p3 ,2,'.',',');?></td>
        <td align="right"><?php echo number_format($p4 ,2,'.',',');?></td>
        <td align="right"> <?php echo number_format($p5 ,2,'.',',');?></td>
        <?php if($tipo ==1) { } else { ?>
        <td align="right"><?php echo number_format($p6 ,2,'.',',');?></td>
        <?php } ?>
    </tr>
<?php    
        }
    }
?>
</table>
</body>
</html>
