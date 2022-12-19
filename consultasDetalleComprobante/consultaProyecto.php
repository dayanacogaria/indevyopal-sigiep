<?php
session_status();
require_once '../Conexion/conexion.php';
$id = $_POST["data"];
$sql = "SELECT  DISTINCT 
                    auxiliarproyecto
        FROM        gf_cuenta CT 
                WHERE CT.id_unico = $id ";
$rs = $mysqli->query($sql);
$row = mysqli_fetch_row($rs);
if ($row[0] == "1") {
    echo '1';
}else if($row[0] == "2"){
    echo '2';
}
?>

