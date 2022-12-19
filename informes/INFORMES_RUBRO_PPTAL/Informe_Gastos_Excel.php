<?php
##################MODIFICACIONES###############
#04/03/2017 |ERICA G. | MODIFICACION CONSULTAS
###############################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Resumen Presupuestal Gastos.xls");
require_once("../../Conexion/conexion.php");
require_once("./consultas.php");
ini_set('max_execution_time', 0);
$usuario=$_SESSION['usuario'];
$fechaActual=date('d/m/Y');

?>

<?php
$calendario = CAL_GREGORIAN;
$anno = $mysqli->real_escape_string(''.$_SESSION['anno'].'');
$anio = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico ='$anno'";
$anio = $mysqli->query($anio);
$anio = mysqli_fetch_row($anio);
$anio = $anio[0];
$fechaInicial = $anio.'-'.'01-01';
$fecha = $anio.'-'.'12-01';
$codigo = $mysqli->real_escape_string(''.$_POST['codigo'].'');
$codigoI =$mysqli->real_escape_string(''.$_POST['codigo'].'');

$cant = strlen($codigoI);
if($cant>1){

for($i = 0; $i < $cant-1;$i++){
     $men = substr($codigoI,0,-1);
     $codigoI=$men;
}
} else {
    $men = $codigoI;
}
$codigoF=$men+1;

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
         WHERE rpp.codi_presupuesto BETWEEN '$codigo' AND '$codigoF' ORDER BY rpp.codi_presupuesto ASC";
$ctass= $mysqli->query($ctas);
#GUARDA LOS DATOS EN LA TABLA TEMPORAL
while ($row1 = mysqli_fetch_row($ctass)) {
    $insert= "INSERT INTO temporal_consulta_pptal_gastos "
            . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
            . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
    $mysqli->query($insert);
    
}
    
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
          WHERE rpp.codi_presupuesto BETWEEN '$codigo' AND '$codigoF' ORDER BY rpp.codi_presupuesto ASC";
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
          WHERE f.id_unico ='$fuente' AND rpp.codi_presupuesto BETWEEN '$codigo' AND '$codigoF' ORDER BY rpp.codi_presupuesto ASC";
   
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
    $tras = presupuestos($row[4], 4, $fechaInicial, $fecha);
        if($tras>0){
            $trasCredito = $tras;
            $trasCont = 0;
        }else {
            $trasCredito = 0;
            $trasCont = $tras;
        }
    
    #PRESUPUESTO DEFINITIVO
    $presupuestoDefinitivo = $pptoInicial+$adicion+$reduccion+$trasCredito+$trasCont;
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
    $registrosAbiertos = $disponibilidad-$totalObligaciones;
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


$hoy = date('d-m-Y');
$hoy = trim($hoy, '"');
$fecha_div = explode("-", $hoy);
$anioh = $fecha_div[2];
$mesh = $fecha_div[1];
$diah = $fecha_div[0];
$hoy = $diah.'/'.$mesh.'/'.$anioh;

#CONSULTAS ENCABEZADO

#DATOS CUENTA
$ct= "SELECT
  codi_presupuesto,
  nombre,
  fuente
FROM
  gf_rubro_pptal r
LEFT JOIN
  gf_rubro_fuente rf ON rf.rubro = r.id_unico
WHERE
  codi_presupuesto ='$codigo'";
$ct = $mysqli->query($ct);
if(mysqli_num_rows($ct)>0){
    $ct = mysqli_fetch_array($ct);
    $codNombre= $ct['codi_presupuesto'].' - '. ucwords(mb_strtolower($ct['nombre']));
    
} else {
    $codNombre= $codigo;
}
#FUENTE
if(empty($_POST['fuente'])){ 
    $fuentef='';
} else {
    $f = $_POST['fuente'];
    $fuentef = "SELECT id_unico, nombre FROM gf_fuente WHERE id_unico = '$f'";
    $fuentef = $mysqli->query($fuentef);
    if(mysqli_num_rows($fuentef)>0){
        $fuentef= mysqli_fetch_array($fuentef);
        $fuentef = $fuentef['id_unico'].' - '.$fuentef['nombre'];
    }else {
        $fuentef=''; 
    }
    
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Resumen Presupuestal Gastos</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="5" bgcolor="skyblue"><CENTER><strong>Resumen Presupuestal Gastos</strong></CENTER></td>
  </tr>
  <tr>
      <td colspan="5" align="center"><strong>CODIGO: <?php echo ucwords(mb_strtoupper($codNombre))?></strong></td>
  </tr>
    <?php 
     if(empty($fuentef)){  
    } else { ?>
    <tr>
        <td colspan="5" align="center"><strong>FUENTE: <?php echo $fuentef;?></strong></td>
    </tr>
    
<?php } ?>
    <tr>
        <td colspan="5"></td>
    </tr>

<?php
#CONSULTA CUENTAS

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
from temporal_consulta_pptal_gastos WHERE cod_rubro = '$codigo' ORDER BY cod_rubro ASC ";
$conejc  = $mysqli->query($sql2);

$p1  = 0;
    $p2  = 0;
    $p3  = 0;
    $p4  = 0;
    $p5  = 0;
    $p6  = 0;
    $p7  = 0;
    $p8  = 0;
    $p9  = 0;
    $p10 = 0;
    $p11 = 0;
    $p12 = 0;
    $p13 = 0;
    $p14 = 0;
    $p15 = 0;
while ($filactas = mysqli_fetch_array($conejc)){

    
    $p1  = $p1+ (float) $filactas['ppti'];
    $p2  = $p2+ (float) $filactas['adi'];
    $p3  = $p3+(float) $filactas['red'];
    $p4  = $p4+(float) $filactas['tcred'];
    $p5  = $p5+(float) $filactas['trcont'];
    $p6  = $p6+(float) $filactas['ppdf'];
    $p7  = $p7+(float) $filactas['disp'];
    $p8  = $p8+(float) $filactas['sald'];
    $p9  = $p9+(float) $filactas['reg'];
    $p10 = $p10+(float) $filactas['rega'];
    $p11 = $p11+(float) $filactas['tobl'];
    $p12 = $p12+(float) $filactas['tpag'];
    $p13 = $p13+(float) $filactas['reserv'];
    $p14 = $p14+(float) $filactas['cpag'];
    $p15 = $p15+(float) $filactas['disAb'];
    
    $traslados= $p4+$p5;
    $apropiacion = $p1+$p2-$p3+$traslados;
    $aproVig = $apropiacion-$p7;
    $cdp=$p7-$p9;
    $comCum= $p9-$p11;
    $obligC= $p11-$p12;
    $porCdp = (($p7*100)/$apropiacion);
    $porCom = (($p9*100)/$apropiacion);
    $porObli = (($p11*100)/$apropiacion);
    $porPag = (($p12*100)/$apropiacion);
  }
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 && $p7==0 && $p8==0 && $p9==0 && $p10==0 && $p11==0 && $p12==0 && $p13==0)
        { } else { ?>
    <tr>
        <td colspan="2" align="left"><strong>Apropiación</strong></td>
        <td style="width:1%;"></td>
        <td colspan="2" align="left"><strong>Certificados de Disponibilidad Presupuestal</strong></td>
    </tr>
    <tr>
        <td align="right">Apropiación Inicial</td>
        <td align="right"><?php echo number_format($p1,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td align="right">C.D.P    %<?php echo number_format($porCdp,2,'.',',')?></td>
        <td align="right"><?php echo number_format($p7,2,'.',',') ?></td>
    </tr>
    <tr>
        <td align="right">+Adiciones</td>
        <td align="right"><?php echo number_format($p2,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td align="right">Aprop. Vigente No Afectada</td>
        <td align="right"><?php echo number_format($aproVig,2,'.',',') ?></td>
    </tr> 
    <tr>
        <td align="right">Reducciones</td>
        <td align="right"><?php echo number_format($p3,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td align="right">C.D.P Por Comprometer</td>
        <td align="right"><?php echo number_format($cdp,2,'.',',') ?></td>
    </tr>
    <tr>
        <td align="right">+- Traslados</td>
        <td align="right"><?php echo number_format($traslados,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td colspan="2" align="left"><strong>Compromisos</strong></td>
    </tr> 
    <tr>
        <td align="right">Apropiación</td>
        <td align="right"><?php echo number_format($apropiacion,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td align="right"><?php echo number_format($porCom,2,'.',',')?>%   Total Compromisos</td>
        <td align="right"><?php echo number_format($p9,2,'.',',') ?></td>
    </tr>    
    <tr>
        <td align="right">Aplazamientos</td>
        <td align="right"><?php echo number_format(0,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td align="right">Compromisos por Cumplir</td>
        <td align="right"><?php echo number_format($comCum,2,'.',',') ?></td>
    </tr>     
    <tr>
        <td align="right">+- Liberación aplazamientos</td>
        <td align="right"><?php echo number_format(0,2,'.',',') ?></td>
        <td></td>
        <td colspan="2" align="left"><strong>Obligaciones</strong></td>
    </tr>
    <tr>
        <td align="right">Apropiación Vigencia</td>
        <td align="right"><?php echo number_format(0,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td align="right"><?php echo number_format($porObli,2,'.',',')?>%   Total Obligaciones</td>
        <td align="right"><?php echo number_format($p11,2,'.',',') ?></td>
    </tr>  
    <tr>
        <td colspan="2" align="left"><strong>PAC</strong></td>
        <td style="width:1%;"></td>
        <td align="right">Obligaciones por cumplir</td>
        <td align="right"><?php echo number_format($obligC,2,'.',',') ?></td>
    </tr>
        
    <tr>
        <td align="right">Disponible Anual</td>
        <td align="right"><?php echo number_format(0,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td colspan="2" align="left"><strong>Pagos Tesorería</strong></td>
    </tr>      
    <tr>
        <td align="right">00.00%   Acumulado</td>
        <td align="right"><?php echo number_format(0,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td align="right"><?php echo number_format($porPag,2,'.',',')?>%   Total Pagos</td>
        <td align="right"><?php echo number_format($p12,2,'.',',') ?></td>
    </tr>      
    <tr>
        <td align="right">Saldo de PAC Acumulado</td>
        <td align="right"><?php echo number_format(0,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td colspan="2" align="right"></td>
    </tr>     
    <?php  } ?>
        
</table>
</body>
</html>