
<?php

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Concepto_Cuenta.xls");
require'../../Conexion/conexion.php';
session_start();
ini_set('max_execution_time',0);
$anno = $_SESSION['anno'];

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Auxiliar Por Terceros</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="8" bgcolor="skyblue"><CENTER><strong>CONFIGURACION CONCEPTO</strong></CENTER></td>
  </tr>
  <tr>
        <td rowspan="2" align="center"><strong>CONCEPTO</strong></td>
        <td rowspan="2" align="center"><strong>NOMBRE CONCEPTO</strong></td>
        <td rowspan="2" align="center"><strong>CÓDIGO RUBRO</strong></td>
        <td rowspan="2" align="center"><strong>NOMBRE RUBRO</strong></td>
        <td rowspan="2" align="center"><strong>CÓDIGO CUENTA DÉBITO</strong></td>
        <td rowspan="2" align="center"><strong>NOMBRE CUENTA DÉBITO</strong></td>
        <td rowspan="2" align="center"><strong>CÓDIGO CUENTA CRÉDITO</strong></td>
        <td rowspan="2" align="center"><strong>NOMBRE CUENTA CRÉDITO</strong></td>
    </tr>
    <TR></TR>
<?php
$rubroP ="SELECT
  c.id_unico as id,
  c.nombre as nomC,
  rp.codi_presupuesto as codR,
  rp.nombre as nomR,
  cd.codi_cuenta as codCD,
  cd.nombre as nomCD,
  cc.codi_cuenta as codCC,
  cc.nombre as nomCC
FROM
  gf_concepto c
LEFT JOIN
  gf_concepto_rubro cr ON c.id_unico = cr.concepto
LEFT JOIN
  gf_rubro_pptal rp ON cr.rubro = rp.id_unico
LEFT JOIN
  gf_concepto_rubro_cuenta crc ON crc.concepto_rubro = cr.id_unico
LEFT JOIN
  gf_cuenta cd ON crc.cuenta_debito = cd.id_unico
LEFT JOIN
  gf_cuenta cc ON cc.id_unico = crc.cuenta_credito 
WHERE c.parametrizacionanno = $anno ORDER BY id ASC";

$rubroP1 =$mysqli->query($rubroP);
while ($rubro = mysqli_fetch_assoc($rubroP1)){
   ?>
         <tr>
            <td align="center"><?php echo $rubro['id'];?></td>
            <td align="center"><?php echo $rubro['nomC'];?></td>
            <td align="center"><?php echo $rubro['codR'];?></td>
            <td align="center"><?php echo $rubro['nomR'];?></td>
            <td align="center"><?php echo $rubro['codCD'];?></td>
            <td align="center"><?php echo $rubro['nomCD'];?></td>
            <td align="center"><?php echo $rubro['codCC'];?></td>
            <td align="center"><?php echo $rubro['nomCC'];?></td>
            
    </tr>
         <?php
       }
     
    ?>
</table>
</body>
</html>
