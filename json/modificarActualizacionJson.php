<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../Conexion/conexion.php';
session_start();

if(!empty($_POST['action'])){
    $action = $_POST['action'];
}else if(!empty($_GET['action'])) {
    $action = $_GET['action'];
}

if($action == 'modificar') {

    $id_unico=$_POST['id_unico'];


    if(!empty($_POST['fecha'])) {

        $fecha=$mysqli->real_escape_string(''.$_POST['fecha'].'');
        $fechaC = DateTime::createFromFormat('d/m/Y', "$fecha");
        $fecha= $fechaC->format('Y/m/d');

    } else {    
        $fecha ="NULL";
    }


    if(!empty($_POST['gestion'])) {
        $gestion='"'.$mysqli->real_escape_string(''.$_POST['gestion'].'').'"';
    } else {
        $gestion = "NULL";
    }

    if(!empty($_POST['observaciones'])) {
        $observaciones='"'.$mysqli->real_escape_string(''.$_POST['observaciones'].'').'"';
    } else {
        $observaciones="NULL";
    }
    
    
     $sql_update="UPDATE gs_actualizacion SET fecha='$fecha',gestion=$gestion,observaciones=$observaciones WHERE md5(id_unico)='$id_unico'";



    $resultado=$mysqli->query($sql_update);
    echo json_encode($resultado);
}
?>

