<?php
#MODIFICADO 30/01/2017 ERICA G.
#Modificaco 14/02/2017 | 3:38 p.m|Jhon Numpaque |Descripción: Se cambio impresión de valor final por el valor devuelto
# ya que por medio de este archivo se valida que si el valor devuelto es 1 entonces la cuenta tiene auxiliar tercero
# y si es dos la cuenta no tiene auxiliar tercero entonces en el detalle no se puede agregar el tercero en el detalle
session_start();
require_once '../Conexion/conexion.php';
$id = $_POST['data'];
$sqli = "select distinct auxiliartercero from gf_cuenta WHERE id_unico = $id";
$rs = $mysqli->query($sqli);
$row = mysqli_fetch_row($rs);
echo $row[0];

?>