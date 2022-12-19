<?php 
#Llamamos a la clase de conexión
require_once ('../Conexion/conexion.php');
#Creamos la session
session_start();
#Definimos a la variable $id con el valor enviado
$id = $_POST["id"];
if ($id ==  0) {
    echo '';
}else{
    #Consulta de datos 
    $sql = "SELECT dinamica FROM gf_cuenta WHERE id_unico = $id    ";
    #Cargamos la consulta y definimos la varible dic
    $dic = $mysqli->query($sql);
    #Definimos a la variable fila y cargamos el resultado de $dic en ella
    $fila = mysqli_fetch_row($dic);
    #Imprimimos el resultado
    echo trim($fila[0]);
}
?>