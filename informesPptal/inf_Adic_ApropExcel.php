<?php
###################################MODIFICACIONES#############################################################
#                          INFORME ADICION A APROPIACION GENERAL#
#############################################################################################################
#21/06/2017 | ERICA G. | CAMBIO CODIGO
#############################################################################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Adicion_Apropiacion.xls");
require_once("../Conexion/conexion.php");
session_start();
$id= $_GET['id'];
$sqlComp = "SELECT 
            comp.numero, 
            DATE_FORMAT(comp.fecha, '%d/%m/%Y'),
            comp.descripcion, 
            tipCom.codigo, 
            tipCom.nombre 
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND md5(comp.id_unico) = '$id'";

$comp = $mysqli->query($sqlComp);
$rowComp = mysqli_fetch_array($comp);
$numero          = $rowComp[0]; 
$fecha           = $rowComp[1]; 
$descripcion     = $rowComp[2]; 
$tipocomprobante = mb_strtoupper($rowComp[3]).' - '. ucwords(mb_strtolower($rowComp[4]));


?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Adición a Apropiación</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="5" bgcolor="skyblue"><CENTER><strong><?php echo $tipocomprobante?></strong></CENTER></td>
  </tr>
  <tr>
    <td align="left" bgcolor="skyblue"><strong>Número</strong></td>
    <td align="left" bgcolor="skyblue"><strong><?php echo $numero?></strong></td>
    <td align="left" bgcolor="skyblue"><strong>Fecha:</strong></td>
    <td align="left" colspan="2" bgcolor="skyblue"><strong><?php echo $fecha?></strong></td>
  </tr>
    <tr>
    
  </tr>
  <tr>
    <td><strong>CÓDIGO</strong></td>
    <td><strong>RUBRO</strong></td>
    <td><strong>FUENTE</strong></td>
    <td><strong>CRÉDITO</strong></td>
    <td><strong>CONTRACRÉDITO</strong></td>
   </tr>
<?PHP 

$sqlDetall = "SELECT detComP.id_unico, rub.codi_presupuesto numeroRubro, fue.nombre nombreFuente, detComP.valor, rub.tipoclase, rub.nombre      
      FROM gf_detalle_comprobante_pptal detComP 
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente
      left join gf_tipo_clase_pptal tipclap on tipclap.id_unico = rub.tipoclase
      where md5(detComP.comprobantepptal) ='$id'";
$detalle = $mysqli->query($sqlDetall);
$totalValor = 0;
$totalCredito = 0;
$totalContacredito = 0;
while ($rowDetall = mysqli_fetch_array($detalle)) 
{ 
  $ingresos = 0;
  $gastos = 0;
  if($rowDetall[4] == 6)
        $ingresos = $rowDetall[3];
  elseif($rowDetall[4] == 7)
       $gastos = $rowDetall[3];
  $totalCredito = $totalCredito+$gastos;
  $totalContacredito = $totalContacredito+$ingresos;
?>
   <tr>
    <td align="right"><?php echo $rowDetall[1]?></td>
    <td><?php echo ($rowDetall[5])?></td>
    <td><?php echo ($rowDetall[2])?></td>
    <td><?php echo number_format($gastos,2,'.',',');?></td>
    <td><?php echo number_format($ingresos,2,'.',',');?></td>
   </tr> 
 <?php 
}
?>
    <tr>
        <td colspan="3"><strong>TOTALES:</strong></td>
        <td><strong><?php echo number_format($totalCredito,2,'.',',');?></strong></td>
        <td><strong><?php echo number_format($totalContacredito,2,'.',',');?></strong></td>
        
    </tr>
    <tr><td colspan="5"><strong>DESCRIPCIÓN: </strong><?php echo $descripcion?></td></tr>
</table>
</body>
</html>


