<?php
#Llamamos a la clase conexión
require_once ('Conexion/conexion.php');
#Iniciamos la sesion
session_start();
#Capturamos la variable
echo $id = $_POST["id"];
if($id == 0){
    $sql = "SELECT DISTINCT CP.id_unico,CP.nombre 
            FROM gf_tipo_clase_pptal CP ORDER BY CP.nombre ASC";
    #Definimos la variable $rs en la que cargamos el resultado de la consulta
    $rs = $mysqli->query($sql);
    #Imprimimos el texto del select
    echo '<option value="" selected="selected">Tipo Clase</option>';
    #Cargamos en un ciclo mientras el cual imprimira todos los valores
    while ($row = mysqli_fetch_row($rs)) {
        echo '<option value="'.$row[0].'">'.ucwords((mb_strtolower($row[1]))).'</option>';
    }    
}  else {
    #consulta sql
   $sql = "SELECT DISTINCT CP.id_unico,CP.nombre 
            FROM gf_tipo_clase_pptal CP 
            LEFT JOIN gf_rubro_pptal PP ON PP.tipoclase = CP.id_unico
            WHERE PP.id_unico = $id";
    #cargamos el resultado de la consulta en una variable $rs
    $rs = $mysqli->query($sql);
    #Definimos la variable row con el valor retornado de la consulta
    $row = mysqli_fetch_row($rs);
    #imprimimos los valores
    echo '<option value="'.$row[0].'" selected="selected">'.ucwords(mb_strtolower($row[1])).'</option>';
    
    $sql1 = "SELECT DISTINCT CP.id_unico,CP.nombre 
            FROM gf_tipo_clase_pptal CP 
            LEFT JOIN gf_rubro_pptal PP ON PP.tipoclase = CP.id_unico
            WHERE CP.id_unico != $row[0]";
    $res = $mysqli->query($sql1);
    while($fila = mysqli_fetch_row($res)){
        echo '<option value="'.$fila[0].'" >'.ucwords((mb_strtolower($fila[1]))).'</option>';
    }
}
?>