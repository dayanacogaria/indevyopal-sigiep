<?php
##################MODIFICACIONES###############
#04/03/2017 |ERICA G. | MODIFICACION CONSULTAS
###############################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Resumen Presupuestal Ingresos.xls");
require_once("../../Conexion/conexion.php");
require_once("./consultas.php");
ini_set('max_execution_time', 0);
$usuario=$_SESSION['usuario'];
$fechaActual=date('d/m/Y');
//ob_start();
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
    
    #PRESUPUESTO DEFINITIVO
    $presupuestoDefinitivo = $pptoInicial+$adicion+$reduccion;
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
<title>Resumen Presupuestal Ingresos</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="5" bgcolor="skyblue"><CENTER><strong>Resumen Presupuestal Ingresos</strong></CENTER></td>
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
from temporal_consulta_pptal_gastos WHERE cod_rubro = '$codigo' ORDER BY cod_rubro ASC";

$sql2 = $mysqli->query($sql2);
$$p1  = 0;
    $p2  = 0;
    $p3  = 0;
    $p4  = 0;
    $p5  = 0;
    $p6  = 0;
 while($filactas = mysqli_fetch_array($sql2)){
    $p1  = (float) $filactas['ppti'];
    $p2  = (float) $filactas['adi'];
    $p3  = (float) $filactas['red'];
    $p4  = (float) $filactas['ppdf'];
    $p5  = (float) $filactas['reca'];
    $p6  = (float) $filactas['spag'];
    $apropiacion = $p1+$p2-$p3;
    $saldoRe = $apropiacion -$p5;
    $porRecau = (($p5*100)/$apropiacion);
 }
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 )
        { } else { 
    ?>
    <tr>
        <td colspan="2" align="left"><strong>Apropiación</strong></td>
        <td style="width:1%;"></td>
        <td colspan="2" align="left"><strong>Reconocimientos</strong></td>
        
    </tr>
    <tr>
        <td align="right">Apropiación Inicial</td>
        <td align="right"><?php echo number_format($p1,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td align="right">Apropiación Definitiva</td>
        <td align="right"><?php echo number_format($p1,2,'.',',') ?></td>
    </tr>
    <tr>
        <td align="right">+Adiciones</td>
        <td align="right"><?php echo number_format($p2,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td align="right">(-)Total Reconocimientos</td>
        <td align="right"><?php echo number_format(0,2,'.',',') ?></td>
    </tr> 
    <tr>
        <td align="right">-Reducciones</td>
        <td align="right"><?php echo number_format($p3,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td align="right">Saldos Por Reconocer</td>
        <td align="right"><?php echo number_format($p1,2,'.',',') ?></td>
    </tr>
    <tr>
        <td align="right">Apropiación</td>
        <td align="right"><?php echo number_format($apropiacion,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td align="right">Total Reconocimientos</td>
        <td align="right"><?php echo number_format(0,2,'.',',') ?></td>
    </tr>    
    
    <tr>
        <td align="right">-Aplazamientos</td>
        <td align="right"><?php echo number_format(0,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td align="right">(-)Total Recaudo</td>
        <td align="right"><?php echo number_format(0,2,'.',',') ?></td>
    </tr>    
    <tr>
        <td align="right">Apropiación Vigencia</td>
        <td align="right"><?php echo number_format(0,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td align="right">Reconocimientos X Recaudar</td>
        <td align="right"><?php echo number_format(0,2,'.',',') ?></td>
    </tr> 
    
    
    <tr>
        <td colspan="2" align="left"><strong>PAC</strong></td>
        <td style="width:1%;"></td>
        <td colspan="2" align="left"><strong>Ingresos Recaudados</strong></td>
    </tr>        
    <tr>
        <td align="right">PAC Programado</td>
        <td align="right"><?php echo number_format(0,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td align="right"><?php echo number_format($porRecau,2,'.',',')?>% Total Recaudos</td>
        <td align="right"><?php echo number_format($p5,2,'.',',') ?></td>
    </tr>      
    <tr>
        <td align="right">00.00%   Rezago</td>
        <td align="right"><?php echo number_format(0,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td align="right">Saldo por Recaudar</td>
        <td align="right"><?php echo number_format($saldoRe,2,'.',',') ?></td>
    </tr>      
    <tr>
        <td align="right">Prog X Recaudar</td>
        <td align="right"><?php echo number_format(0,2,'.',',') ?></td>
        <td style="width:1%;"></td>
        <td colspan="2" align="right"></td>
    </tr>     
    <?php  } ?>
        
