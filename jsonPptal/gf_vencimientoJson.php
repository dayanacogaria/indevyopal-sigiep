<?php
require '../Conexion/ConexionPDO.php';                                                  
require '../Conexion/conexion.php';                    
require './funcionesPptal.php';
ini_set('max_execution_time', 0);
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$anno       = anno($panno);
$action     = $_REQUEST['action'];
$fechaa     = date('Y-m-d');
switch ($action){
    #* Crear Configuaración
    case 1:
        $clase  = $_REQUEST['clase'];
        $dias   = $_REQUEST['dias'];
        $sql_cons ="INSERT INTO `gf_vencimiento` 
                ( `clase`, `dias`, `compania`) 
        VALUES (:clase, :dias, :compania)";
        $sql_dato = array(
            array(":clase",$clase),
            array(":dias",$dias),
            array(":compania",$compania),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    #* Modificar Configuaración
    case 2:
        $id     = $_REQUEST['id'];
        $clase  = $_REQUEST['clase'];
        $dias   = $_REQUEST['dias'];
        $sql_cons ="UPDATE `gf_vencimiento` 
                SET `clase`=:clase, 
                `dias`=:dias 
                WHERE `id_unico`=:id_unico";
        $sql_dato = array(
            array(":clase",$clase),
            array(":dias",$dias),
            array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    #* Eliminar Configuaración
    case 3:
        $id     = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gf_vencimiento` 
                WHERE `id_unico` =:id_unico";
        $sql_dato = array(
                array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
}
