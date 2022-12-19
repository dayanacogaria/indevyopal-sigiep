<?php
session_start();
require_once '../Conexion/conexion.php';
$tercero = $_POST['tercero'];
$sql = "SELECT con.id_unico,con.nombre,conter.valor FROM gf_condicion con 
LEFT JOIN gf_perfil_condicion percon ON percon.condicion = con.id_unico
LEFT JOIN gf_condicion_tercero conter ON conter.perfilcondicion = percon.id_unico
WHERE con.id_unico = 1 AND conter.tercero = $tercero";
$result = $mysqli->query($sql);
$row = mysqli_fetch_row($result);
$fila = $result->num_rows;
if($fila!=0){
    $valor = $row[2];
    echo $valor;
}else{
    echo 0;
}

?>
