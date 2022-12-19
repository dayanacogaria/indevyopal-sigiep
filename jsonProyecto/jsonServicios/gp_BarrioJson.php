<?php
###################################################################################
#   **********************      Modificaciones      ******************************#
###################################################################################
#05/07/2018 |Erica G. | Creado
###################################################################################
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
session_start();
$con = new ConexionPDO();

switch ($_REQUEST['action']) {
    #** Eliminar **#
    case 1:
        $id = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gp_barrio`  
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
        $sql_cons = "INSERT INTO `gp_barrio`  
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
        $sql_cons = "UPDATE `gp_barrio`  
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
    #** Cargar Barrio Por Ciudad ***#
    case 4:
        $ciudad = $_REQUEST['ciudad'];
        if(!empty($ciudad)){
            $row = $con->Listar("SELECT id_unico, nombre FROM gp_barrio WHERE ciudad =$ciudad");
            if(count($row)>0){
                for ($i = 0; $i < count($row); $i++) {
                    echo '<option value="'.$row[$i][0].'">'. ucwords(mb_strtolower($row[$i][1])).'</option>';
                }
            } else {
                echo '<option value=" "> No Hay Barrios </option>';
            }
        } else {
            echo '<option value=""> - </option>';
        }
    break;
}
