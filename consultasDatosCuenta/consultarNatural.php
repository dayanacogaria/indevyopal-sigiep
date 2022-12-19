<?php
require_once('../Conexion/conexion.php');
session_start();
$id = $_POST["id"];
if ($id ==0) {
    $sql = "SELECT id_unico,nombre FROM gf_naturaleza ORDER BY nombre ASC";
    $rs = $mysqli->query($sql);
    echo '<option selected>Naturaleza</option>';
    while($fila = mysqli_fetch_row($rs)){
        echo '<option value="'.$fila[0].'">'.  ucwords((mb_strtolower($fila[1]))).'</option>';
    }
}else{
    $sql = "SELECT N.id_unico,N.nombre FROM gf_naturaleza N
            LEFT JOIN gf_cuenta CT ON N.id_unico = CT.naturaleza
            WHERE CT.id_unico = $id 
            ORDER BY nombre ASC";
    
    $rs = $mysqli->query($sql);
    $row = mysqli_fetch_row($rs);
    echo '<option value="'.$row[0].'" selected>'.ucwords((mb_strtolower($row[1]))).'</option>';
    $sqli = "SELECT DISTINCT N.id_unico,N.nombre FROM gf_naturaleza N
            LEFT JOIN gf_cuenta CT ON N.id_unico = CT.naturaleza
            WHERE N.id_unico != $row[0]
            ORDER BY nombre ASC";
    $res = $mysqli->query($sqli);
    while($fila = mysqli_fetch_row($res)){
        echo '<option value="'.$fila[0].'" >'.ucwords((mb_strtolower($fila[1]))).'</option>';
    }
}
?>