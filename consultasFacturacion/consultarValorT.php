<?php
session_start();
require_once '../Conexion/conexion.php';
$concepto = $_POST['data'];
$sql = "SELECT dtf.valor FROM gp_detalle_factura dtf where dtf.id_unico = $concepto";
$result = $mysqli->query($sql);
$row = mysqli_fetch_row($result);
echo '<option value="'.$row[0].'">'.$row[0].'</option>';
$sql = "SELECT gptr.valor
FROM gp_tarifa gptr 
WHERE gptr.valor != $row[0]";
$res = $mysqli->query($sql);
while($fila = mysqli_fetch_row($res)){
    echo '<option value="'.$fila[0].'">'.$fila[0].'</option>';
}
?>

