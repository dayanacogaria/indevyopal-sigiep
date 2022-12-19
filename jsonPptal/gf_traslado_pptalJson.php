<?php
#######################################################################################
#24/07/2017 |ERICA G. |ARCHIVO CREADO    
#######################################################################################
require_once '../Conexion/conexion.php';
require_once './funcionesPptal.php';
session_start();

switch ($_POST['action']){
    #MODIFICAR
    case 1:
        $r=0;
        $id=$_POST['id'];
        $fecha= fechaC($_POST['fecha']);
        $fechaV =fechaC($_POST['fechaV']);
        if(!empty($_POST['descripcion'])){
        $descripcion = "'".$_POST['descripcion']."'";
        } else {
            $descripcion = 'NULL';
        }
        $query = "UPDATE gf_comprobante_pptal SET fecha = '$fecha', fechaVencimiento = '$fechaV' , descripcion = $descripcion "
                . "WHERE id_unico = $id";
        $q = $mysqli->query($query);
        if($q == true){
            $r=1;
        } 
        echo json_decode($r);
    break;
}

