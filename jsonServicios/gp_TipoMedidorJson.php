<?php
###################################################################################
#   **********************      Modificaciones      ******************************#
###################################################################################
#21/08/2018 |Erica G. | Creado
###################################################################################
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
session_start();
$con = new ConexionPDO();

switch ($_REQUEST['action']) {
    #** Eliminar **#
    case 1:
        $id = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gp_tipo_medidor`  
            WHERE `id_unico` =:id_unico";
        $sql_dato = array(
                array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $rta = true;
        } else {
            $rta = false;
        }
        echo $rta;
    break;
    #** Guardar **#
    case 2:
        $nombre   = $_REQUEST['nombre'];
        $sql_cons = "INSERT INTO `gp_tipo_medidor`  
            (`nombre`) 
            VALUES(:nombre)";
        $sql_dato = array(
                array(":nombre",$nombre),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $rta = true;
        } else {
            $rta = false;
        }
        echo $rta;
    break;
    #** Modificar **#
    case 3:
        $nombre   = $_REQUEST['nombre'];
        $id       = $_REQUEST['id'];
        $sql_cons = "UPDATE `gp_tipo_medidor`  
            SET `nombre`=:nombre 
            WHERE `id_unico`=:id_unico";
        $sql_dato = array(
            array(":nombre",$nombre),
            array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $rta = true;
        } else {
            $rta = false;
        }
        echo $rta;
    break;
}

