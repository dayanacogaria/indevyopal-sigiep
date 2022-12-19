<?php
#########################Modificaciones#######################
#13/03/2017 |ERICA G. | ARCHIVO CREADO
################################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Traslado_Pptal.xls");
require_once("../Conexion/conexion.php");
session_start();


$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');

$sqlComp = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre, comp.tercero 
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND comp.id_unico = ".$_GET['id'];

$comp = $mysqli->query($sqlComp);

$rowComp = mysqli_fetch_array($comp);
$nomcomp = $rowComp[1]; //Número de comprobante      
$fechaComp = $rowComp[2]; //Fecha       
$descripcion = $rowComp[3]; //Descripción  
$fechaVen = $rowComp[4]; //Fecha de vencimiento  
$tipocomprobante = $rowComp[5]; //id tipo comprobante  
$codigo = $rowComp[6]; //Código de tipo comprobante  
$nombre = $rowComp[7]; //Nombre de tipo comprobante  
$nombretipoF = $rowComp[7];
$terceroComp = intval($rowComp[8]); //Tercero del comprobante

    $fecha_div = explode("-", $fechaComp);
    $diaS = $fecha_div[2];
    $mesS = $fecha_div[1];
    $anioS = $fecha_div[0];

    $fechaComp = $diaS.'/'.$mesS.'/'.$anioS;
    
$usuario = $_SESSION['usuario'];

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Traslado Presupuestal</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr>
      <td colspan="5" bgcolor="skyblue"><CENTER><strong><?php echo mb_strtoupper($nombretipoF.' N°:'.$nomcomp)?></strong></CENTER></td>
  </tr>
  <tr>
    <td><strong>CÓDIGO</strong></td>
    <td><strong>RUBRO</strong></td>
    <td><strong>FUENTE</strong></td>
    <td><strong>CRÉDITO</strong></td>
    <td><strong>CONTRACRÉDITO</strong></td>
   </tr>
<?PHP 

$sqlDetall = 'SELECT detComP.id_unico, rub.codi_presupuesto numeroRubro, 
    fue.nombre nombreFuente, detComP.valor, rub.tipoclase, rub.nombre , fue.id_unico      
      FROM gf_detalle_comprobante_pptal detComP 
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente
      left join gf_tipo_clase_pptal tipclap on tipclap.id_unico = rub.tipoclase
      where detComP.comprobantepptal ='.$_GET['id'];
$detalle = $mysqli->query($sqlDetall);

//$pdf->SetY(85);

$totalValor = 0;
$totalCredito = 0;
$totalContacredito = 0;
while ($rowDetall = mysqli_fetch_array($detalle)) 
{ 

  $ingresos = 0;
  $gastos = 0;

  if($rowDetall[3] < 0 )
        $ingresos = $rowDetall[3];
    elseif($rowDetall[4] == 7)
        $gastos = $rowDetall[3];
    $ingresos1=$ingresos*-1;
  $totalCredito = $totalCredito+$gastos;
  $totalContacredito = $totalContacredito+$ingresos;
?>
    
   <tr>
    <td align="right"><?php echo $rowDetall[1]?></td>
    <td><?php echo ucwords(mb_strtolower($rowDetall[5]))?></td>
    <td><?php echo ucwords(mb_strtolower($rowDetall[6].' - '.$rowDetall[2]))?></td>
    <td><?php echo $gastos;?></td>
    <td><?php echo $ingresos1?></td>
   </tr> 
 <?php 
}
$totalContacredito=$totalContacredito*-1;
?>
    <tr>
        <td colspan="3"><strong>TOTALES:</strong></td>
        <td><strong><?php echo $totalCredito;?></strong></td>
        <td><strong><?php echo $totalContacredito;?></strong></td>
    </tr>
    <tr><td colspan="5"><strong>DESCRIPCIÓN: </strong><?php echo $descripcion?></td></tr>


