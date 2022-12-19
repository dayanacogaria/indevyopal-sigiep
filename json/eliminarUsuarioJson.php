<?php
session_start();
#Llamado a la clase de conexión
require_once '../Conexion/conexion.php';
#captura del id enviada
$id=$_GET['id'];
#sql de eliminado y ejecutamos la consulta
$sql="delete from gs_usuario where id_unico=$id";
$result=$mysqli->query($sql);
#Retornamos el valor devuelto por la consulta
echo json_encode($result);

