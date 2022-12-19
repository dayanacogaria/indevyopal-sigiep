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
        $sql_cons ="DELETE FROM `gp_estrato`  
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
        $codigo   = $_REQUEST['codigo'];
        $sql_cons = "INSERT INTO `gp_estrato`  
            (`nombre`,`codigo`, `tipo_estrato`) 
            VALUES(:nombre,:codigo, :tipo_estrato)";
        $sql_dato = array(
                array(":nombre",$nombre),
                array(":codigo",$codigo),
                array(":tipo_estrato",2),
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
        $codigo   = $_REQUEST['codigo'];
        $id       = $_REQUEST['id'];
        $sql_cons = "UPDATE `gp_estrato`  
            SET `nombre`=:nombre, `codigo`=:codigo   
            WHERE `id_unico`=:id_unico";
        $sql_dato = array(
            array(":nombre",$nombre),
            array(":codigo",$codigo),
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



