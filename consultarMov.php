<?php
$id = $_POST["id"];
session_start();
$sql = "SELECT movimiento FROM gf_rubro_pptal WHERE id_unico";
$resultado = $mysqli->query($sql);
$row = mysqli_fetch_row($resultado);
echo $row[0];
?>
