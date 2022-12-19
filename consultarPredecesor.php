<?php
#Llamamos a la clase de conexión
require_once('Conexion/conexion.php');
#Iniciamos la sesion
session_start();
#Variable $id y la definimos con el valor enviado
$id = $_POST["id"];
if($id == 0){
    $sql = "SELECT anno FROM gf_parametrizacion_anno ORDER BY anno";
    $rs = $mysqli->query($sql);
    while ($fila = mysqli_fetch_row($rs)) { 
     echo '<option >'.ucwords(($fila[0])).'</option>';
    }
}else{
    #consulta de carga de datos
    $sql = "SELECT vigencia from gf_rubro_pptal WHERE id_unico = $id";
    #Cargamos la consulta y definimos la variable vig
    $vig = $mysqli->query($sql);
    #Definimos a la variable fila y cargamos el resultado de $vig en ella
    $fila = mysqli_fetch_row($vig);
    #Imprimimos el valor devuelto
    $sql1 = " SELECT DISTINCT A.id_unico,A.anno FROM gf_parametrizacion_anno A LEFT JOIN gf_rubro_pptal PP ON PP.parametrizacionanno = A.id_unico WHERE A.id_unico = $fila[0]";
    $rs1 = $mysqli->query($sql1);
    while($fila1 = mysqli_fetch_row($rs1)){
        echo '<option value="'.$fila1[0].'">'.$fila1[1].'</option>';
    }
    $sql10 = "SELECT DISTINCT A.id_unico,A.anno FROM gf_parametrizacion_anno A LEFT JOIN gf_rubro_pptal PP ON PP.parametrizacionanno = A.id_unico WHERE A.id_unico != $fila[0]";
    $rs10 = $mysqli->query($sql10);
    while($fila10 = mysqli_fetch_row($rs10)){
        echo '<option value="'.$fila10[0].'">'.$fila10[1].'</option>';
    }
}

?>

