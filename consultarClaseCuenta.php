<?php
require_once '../Conexion/conexion.php';
session_start();
$id = $_POST["id"];
if ($id==0) {
    $sql = "SELECT id_unico,nombre FROM gf_clase_cuenta ORDER BY nombre ASC";
    $rs = $mysqli->query($sql);
    while($fila = mysqli_fetch_row($rs)){
        echo '<option value="'.$fila[0].'">'.ucwords((strtolower($fila[1]))).'</option>';
    }
}else{
    $sql = "SELECT CT.id_unico,CT.nombre FROM gf_clase_cuenta CT LEFT JOIN gf_cuenta C ON CT.id_unico = C.clasecuenta WHERE C.id_unico = $id ORDER BY CT.nombre ASC";
    $res = $mysqli->query($sql);
    $row = mysqli_fetch_row($res);
    echo '<option value="'.$row[0].'">'.ucwords((strtolower($row[1]))).'</option>';
    $sqli = "SELECT CT.id_unico,CT.nombre FROM gf_clase_cuenta CT LEFT JOIN gf_cuenta C ON CT.id_unico = C.clasecuenta WHERE CT.id_unico = $row[0] ORDER BY CT.nombre ASC";
    $rs = $mysqli->query($sqli);
    while ($fila = mysqli_fetch_row($rs)){
        echo '<option value="'.$fila[0].'">'.ucwords((strtolower($fila[1]))).'</option>';
    }
}   
?>

