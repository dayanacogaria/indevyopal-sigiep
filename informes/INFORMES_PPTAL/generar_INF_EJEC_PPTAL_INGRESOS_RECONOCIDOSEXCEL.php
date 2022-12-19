<?php
##################################################################################################################################################################
#                                                                                                                           Modificaciones
##################################################################################################################################################################
#02/02/2018 |Erica G. | Parametrizacion Año
#09/09/2017 |Erica G. | Acumulado, actual, anterior
#01/09/2017 |Erica G. | Archivo Creado, Informe para Casalac y terminal
##################################################################################################################################################################
?>
<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Ejecucion_Ingresos_Reconocidos.xls");
require_once("../../Conexion/conexion.php");
require'consultas.php';
ini_set('max_execution_time', 0);
ob_start();
?>

<?php
$calendario = CAL_GREGORIAN;
$mesI = $mysqli->real_escape_string(''.$_POST['sltmesi'].'');
$mesF = $mysqli->real_escape_string(''.$_POST['sltmesf'].'');
$codigoI =$mysqli->real_escape_string(''.$_POST['sltcodi'].'');
$codigoF = $mysqli->real_escape_string(''.$_POST['sltcodf'].'');
$parmanno = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico =$parmanno";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno =$an[0]; 

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
             AND rpp.tipoclase = 6 AND rpp.parametrizacionanno = $parmanno 
        ORDER BY rpp.codi_presupuesto ASC";
$ctass= $mysqli->query($ctas);
#GUARDA LOS DATOS EN LA TABLA TEMPORAL
while ($row1 = mysqli_fetch_row($ctass)) {
    $insert= "INSERT INTO temporal_consulta_pptal_gastos "
            . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
            . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
    $mysqli->query($insert);
    
}
    
#CONSULTA CUENTAS DEL DETALLE SEGUN VARIABLES QUE RECIBE

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
          WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' AND rpp.parametrizacionanno = $parmanno  ";

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
    #PRESUPUESTO DEFINITIVO
    $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion;

    
 ##########################################################################################################################################################################    
#   ANTERIORES
##########################################################################################################################################################################    
    $fechaIAn = $anno.'-01-01';    
    $fechaFinAn = $anno.'-'.$mesI.'-01';
    $nuevafechaAn = strtotime ( '-1 day' , strtotime ( $fechaFinAn ) ) ;
    $fechaFAn = date ( 'Y-m-d' , $nuevafechaAn );
    #RECONOCIMIENTOS
    $reconocimientosAn= disponibilidades($row[4], 19, $fechaIAn, $fechaFAn);
    #RECAUDOS
    $recaudosAn = disponibilidades($row[4], 18, $fechaIAn, $fechaFAn);

##########################################################################################################################################################################    
#   ACTUALES
##########################################################################################################################################################################    
    $fechaIAc = $anno.'-'.$mesI.'-01';
    $diaFAc = cal_days_in_month($calendario, $mesF, $anno); 
    $fechaFinAc = $anno.'-'.$mesF.'-'.$diaFAc;
    #RECONOCIMIENTOS
    $reconocimientosAc= disponibilidades($row[4], 19, $fechaIAc, $fechaFinAc);
    #RECAUDOS
    $recaudosAc = disponibilidades($row[4], 18, $fechaIAc, $fechaFinAc);
    
######################################################################################################################################################
#   ACUMULADO
######################################################################################################################################################
    $fechaIAcum = $anno.'-01-01';  
    $diaFAc = cal_days_in_month($calendario, $mesF, $anno); 
    $fechaFAcum = $anno.'-'.$mesF.'-'.$diaFAc;
    #RECONOCIMIENTOS
    $reconocimientosAcum= disponibilidades($row[4], 19, $fechaIAcum, $fechaFAcum);
    #RECAUDOS
    $recaudosAcum = disponibilidades($row[4], 18, $fechaIAcum, $fechaFAcum);
######################################################################################################################################################
    
    #SALDOS POR RECAUDAR
    $saldosRecaudar = $reconocimientosAcum-$recaudosAcum;
    #SALDOS POR EJECUTAR
    $saldosEjecutar = $presupuestoDefinitivo-$reconocimientosAcum;
    #PRESUPUESTO POR RECAUDAR
    $pptoRecaudar= $presupuestoDefinitivo-$recaudosAcum;

###ACTUALIZAR TABLA CON DATOS HALLADOS
    $update="UPDATE temporal_consulta_pptal_gastos SET "
            . "ptto_inicial ='$pptoInicial', "
            . "adicion = '$adicion', "
            . "reduccion = '$reduccion', "
            . "presupuesto_dfvo = '$presupuestoDefinitivo', "
            . "disponibilidades ='$reconocimientosAn', "
            . "saldo_disponible = '$reconocimientosAc', "
            . "disponibilidad_abierta = '$reconocimientosAcum', "
            . "total_pagos = '$recaudosAn',"
            . "reservas = '$recaudosAc', "
            . "recaudos = '$recaudosAcum', "
            . "registros = '$saldosEjecutar', "
            . "registros_abiertos ='$pptoRecaudar',"
            . "saldos_x_recaudar = '$saldosRecaudar' "
            . "WHERE rubro_fuente = '$row[4]'";
    $update = $mysqli->query($update);
          
}   
//#CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
$acum = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, reduccion, "
        . "presupuesto_dfvo, "
        . "disponibilidades, "
        . "saldo_disponible, "
        . "disponibilidad_abierta, "
        . "total_pagos, "
        . "reservas, "
        . "recaudos, "
        . "registros, "
        . "registros_abiertos, "
        . "saldos_x_recaudar "
        . "FROM temporal_consulta_pptal_gastos "
        . "ORDER BY cod_rubro DESC ";
$acum = $mysqli->query($acum);

while ($rowa1= mysqli_fetch_row($acum)){
    $acumd = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, reduccion, "
        . "presupuesto_dfvo, "
        . "disponibilidades, "
        . "saldo_disponible, "
        . "disponibilidad_abierta, "
        . "total_pagos, "
        . "reservas,  "
        . "recaudos, "
        . "registros, "
        . "registros_abiertos, "
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
                    . "presupuesto_dfvo, "
                    . "disponibilidades, "
                    . "saldo_disponible, "
                    . "disponibilidad_abierta, "
                    . "total_pagos, "
                    . "reservas, "
                    . "recaudos, "
                    . "registros, "
                    . "registros_abiertos, "
                    . "saldos_x_recaudar "
            . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
        $va1 = $mysqli->query($va11);
        $va= mysqli_fetch_row($va1);
        $pptoInicialM = $rowa[3]+$va[3];
        $adicionM = $rowa[4]+$va[4];
        $reduccionM = $rowa[5]+$va[5];
        $presupuestoDefinitivoM = $rowa[6]+$va[6];
        $reconocimientosAnM =$rowa[7]+$va[7];
        $reconocimientosAcM =$rowa[8]+$va[8];
        $reconocimientosAcumM =$rowa[9]+$va[9];
        $recaudosAnM = $rowa[10]+$va[10];
        $recaudosAcM = $rowa[11]+$va[11];
        $recaudosAcumM = $rowa[12]+$va[12];
        $saldosEjecutarM = $rowa[13]+$va[13];
        $pptoRecaudarM = $rowa[14]+$va[14];
        $saldosRecaudarM = $rowa[15]+$va[15];
        
        #ACTUALIZAR TABLA CON DATOS HALLADOS
         $updateA="UPDATE temporal_consulta_pptal_gastos SET "
                . "ptto_inicial ='$pptoInicialM', "
                . "adicion = '$adicionM', "
                . "reduccion = '$reduccionM', "
                . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
                . "disponibilidades ='$reconocimientosAnM', "
                . "saldo_disponible ='$reconocimientosAcM', "
                . "disponibilidad_abierta ='$reconocimientosAcumM', "
                . "total_pagos ='$recaudosAnM', "
                . "reservas ='$recaudosAcM', "
                . "recaudos = '$recaudosAcumM', "
                . "registros = '$saldosEjecutarM', "
                . "registros_abiertos ='$pptoRecaudarM',"
                . "saldos_x_recaudar = '$saldosRecaudarM' "
                . "WHERE cod_rubro = '$rowa[2]'";
        $updateA = $mysqli->query($updateA);
        }
    }
}

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

$month1 ="";
$month2 ="";
$meses = array( "01" => 'Enero', "02" => 'Febrero', "03" => 'Marzo',"04" => 'Abril', "05" => 'Mayo', "06" => 'Junio', 
                "07" => 'Julio', "08" => 'Agosto', "09" => 'Septiembre', "10" => 'Octubre', "11" => 'Noviembre', "12" => 'Diciembre');
$month1 = $meses[$mesI];
$month2 = $meses[$mesF];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ejecución Ingresos Reconocidos</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th colspan="16" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
           <br/>EJECUCIÓN PRESUPUESTAL INGRESOS RECONOCIDOS
           <br/>ENTRE <?php echo mb_strtoupper( $month1.' Y '.$month2.' - '.$anno) ?><br/>&nbsp;</strong>
        </th>
  </tr>
  <tr>
        <td rowspan="2" align="center"><strong>RUBRO</strong></td>
        <td rowspan="2"align="center"><strong>DETALLE</strong></td>
        <td rowspan="2" align="center"><strong>FUENTE</strong></td> 
        <td rowspan="2" align="center"><strong>PRESUPUESTO INICIAL</strong></td>
        <td colspan ="2" align="center"><strong>MODIFICACIONES PRESUPUESTALES</strong></td>
        <td rowspan="2" align="center"><strong>PRESUPUESTO DEFINITIVO</strong></td>
        <td colspan="3" align="center"><strong>RECONOCIMIENTO</strong></td>
        <td colspan="3" align="center"><strong>RECAUDO</strong></td>
        <td rowspan="2" align="center"><strong>SALDOS POR RECAUDAR</strong></td>
        <td rowspan="2" align="center"><strong>SALDOS POR EJECUTAR</strong></td>
        <td rowspan="2" align="center"><strong>PRESUPUESTO POR RECAUDAR</strong></td>
        
    </tr>
  <tr>
        <td  align="center"><strong>ADICIÓN</strong></td>
        <td  align="center"><strong>REDUCCIÓN</strong></td>
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
                        cod_rubro                           as codrub, 
                        nombre_rubro                   as nomrub,
                        ptto_inicial                         as ppti,
                        adicion                                as adi,
                        reduccion                           as red,
                        presupuesto_dfvo             as ppdf,
                        cod_fuente                          as fuente , 
                        disponibilidades              as reconocimientosAn,
                        saldo_disponible              as reconocimientosAc, 
                        disponibilidad_abierta    as reconocimientosAcum, 
                        total_pagos                         as recaudosAn, 
                        reservas                              as recaudosAc, 
                        recaudos                             as recaudosAcum,
                        registros                             as saldosEjecutar, 
                        registros_abiertos            as pptoRecaudar, 
                        saldos_x_recaudar           as saldosRecaudar  
        FROM temporal_consulta_pptal_gastos ORDER BY cod_rubro ASC";
$conejc  = $mysqli->query($sql2);

while ($filactas = mysqli_fetch_array($conejc)) 
{
   $p1    = (float) $filactas['ppti'];
    $p2    = (float) $filactas['adi'];
    $p3    = (float) $filactas['red'];
    $p4    = (float) $filactas['ppdf'];
    $p5    = (float) $filactas['reconocimientosAn'];
    $p6    = (float) $filactas['reconocimientosAc'];
    $p7    = (float) $filactas['reconocimientosAcum'];
    $p8    = (float) $filactas['recaudosAn'];
    $p9    = (float) $filactas['recaudosAc'];
    $p10    = (float) $filactas['recaudosAcum'];
    $p11  = (float) $filactas['saldosRecaudar'];
    $p12  = (float) $filactas['saldosEjecutar'];
    $p13  = (float) $filactas['pptoRecaudar'];
    
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 
            && $p5==0 && $p6==0 && $p7==0 
            && $p8==0 && $p9==0 && $p10==0
            && $p11==0 && $p12==0 && $p13==0)
    { } else {
       
        echo "<tr>";
        echo "<td>".$filactas['codrub']."</td>";
        echo "<td>".$filactas['nomrub']."</td>";
        echo "<td>".$filactas['fuente']."</td>";
        echo "<td>".number_format($p1,2,'.',',')."</td>";
        echo "<td>".number_format($p2,2,'.',',')."</td>";
        echo "<td>".number_format($p3,2,'.',',')."</td>";
        echo "<td>".number_format($p4,2,'.',',')."</td>";
        echo "<td>".number_format($p5,2,'.',',')."</td>";
        echo "<td>".number_format($p6,2,'.',',')."</td>";
        echo "<td>".number_format($p7,2,'.',',')."</td>";
        echo "<td>".number_format($p8,2,'.',',')."</td>";
        echo "<td>".number_format($p9,2,'.',',')."</td>";
        echo "<td>".number_format($p10,2,'.',',')."</td>";
        echo "<td>".number_format($p11,2,'.',',')."</td>";
        echo "<td>".number_format($p12,2,'.',',')."</td>";
        echo "<td>".number_format($p13,2,'.',',')."</td>";
        echo "</tr>";
    }
}
?>
</table>
</body>
</html>