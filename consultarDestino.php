<?php
#Llamamos a la clase conexión
require_once './Conexion/conexion.php';
#Creamos la sesion
session_start();
#Cargamos la variable $id con el valor enviado
$id = $_POST["id"];
#Validamos el valor enviado
if ($id == 0) {
    #consulta
    $sql = "SELECT DISTINCT id_unico,nombre FROM gf_destino ORDER BY nombre ASC";
    #cargamos el valor devuelto en $des
    $des = $mysqli->query($sql);
    #Imprimimos el texto del select
    echo '<option value="" selected="selected">Destino</option>';
    #Cargamos en un ciclo el cual imprimira los valores existntes
    while ($row = mysqli_fetch_row($des)) {
        echo '<option value="'.$row[0].'">'.ucwords(mb_strtolower($row[1])).'</option>';
    }
}else{
    #consulta
    $sql = "SELECT DISTINCT D.id_unico,D.nombre,PP.id_unico 
            FROM gf_destino D
            LEFT JOIN gf_rubro_pptal PP ON PP.destino = D.id_unico 
            WHERE PP.id_unico = $id";
    #Cargaos el resultado 
    $des = $mysqli->query($sql);
    #Definimos la variable con el valor retornado
    $row = mysqli_fetch_row($des);
    #imprimimos el valor retornado
    echo '<option value="'.$row[0].'" selected="selected">'.ucwords((mb_strtolower($row[1]))).'</option>'; 
    $sqL = "SELECT DISTINCT D.id_unico,D.nombre 
            FROM gf_destino D 
            WHERE D.id_unico != $row[0]";
    $deS = $mysqli->query($sqL);
    while ($fila = mysqli_fetch_row($deS)){
        echo '<option value="'.$fila[0].'">'.ucwords((mb_strtolower($fila[1]))).'</option>'; 
    }
}
?>