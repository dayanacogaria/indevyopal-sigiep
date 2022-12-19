<?php 
#######################################################################################
#22/06/2017 |ERICA G. |ARCHIVO CREADO
#######################################################################################
require_once('../Conexion/conexion.php');
session_start();
$action     = $_POST['action'];
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
switch ($action){
    ############MODIFICAR EL VALOR DE LA RETENCION, CXP Y EGRESO#############
    case (1):
        $id         =$_POST['id'];
        $valorbase  =$_POST['valorbase'];
        $valor      =$_POST['valor'];
        $updt = "UPDATE gf_retencion SET retencionbase = '$valorbase', valorretencion = '$valor'"
                . "WHERE id_unico = $id";
        $updt = $mysqli->query($updt);
        if($updt==true){
            $result=1;
        } else {
            $result=2;
        }
        echo json_decode($result);
    break;
    ############ELIMINAR DETALLE RETENCION#############
    case (2):
        $id         =$_POST['id'];
        $updt = "DELETE FROM gf_retencion "
                . "WHERE id_unico = $id";
        $updt = $mysqli->query($updt);
        if($updt==true){
            $result=1;
        } else {
            $result=2;
        }
        echo json_decode($result);
    break;
}

