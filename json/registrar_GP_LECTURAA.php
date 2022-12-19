<?php
require_once('../Conexion/conexion.php');
session_start();

$documento = $_FILES['file'];
$name = $_FILES['file']['name'];
$ext = pathinfo($name, PATHINFO_EXTENSION);
$directorio ='../documentos/lecturas';
$nom = rand(5, 1000);
$nombre = $nom.'.'.$ext;
move_uploaded_file($_FILES['file']['tmp_name'],$directorio.$nombre); 
$ruta = $directorio.$nombre;
$filas=file($ruta); 
foreach($filas as $value){
    list($referencia, $periodo, $valor,$aforador, $fecha) = explode(",", $value);
    echo 'Ref: '.$referencia.'<br/>'; 
    echo 'periodo: '.$periodo.'<br/>'; 
    echo 'valor: '.$valor.'<br/>'; 
    echo 'aforador: '.$aforador.'<br/>'; 
    echo 'fecha: '.$fecha.'<br/>';  
}
?>

