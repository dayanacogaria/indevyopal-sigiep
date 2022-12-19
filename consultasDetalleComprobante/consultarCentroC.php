<?php
session_status();
######################################################################################################################################
# Modificaciones
# 14/02/2017 | 04:07 | Descripción : Cambios de impresión de valor final
require_once '../Conexion/conexion.php';
$id = $_POST["data"];
$sql = "SELECT  DISTINCT 
                    CT.centrocosto
        FROM        gf_cuenta CT 
        WHERE CT.id_unico = $id ";
$rs = $mysqli->query($sql);
$row = mysqli_fetch_row($rs);
echo $row[0];
?>

