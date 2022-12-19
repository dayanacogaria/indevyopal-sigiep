<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#17/04/2018 | Erica G. | Archivo Creado
####/################################################################################
require '../Conexion/ConexionPDO.php';
require '../Conexion/conexion.php';
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$anno       = $_SESSION['anno'];
$action     = $_REQUEST['action'];
switch ($action) {
    #   *** Eliminar Tipo Identificacion    ***  #
    case 1:
        $id      = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gy_actividad`
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":id_unico",$id),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
           // var_dump($obj_resp);
        }
    break;
    #   *** Guardar Tipo Identificacion    ***  #
    case 2:
        $nombre   = $_REQUEST['nombre'];
        $descripcion   = $_REQUEST['descripcion'];
        $sql_cons ="INSERT INTO `gy_actividad`
            ( `nombre`, `descripcion`,`compania`)
            VALUES (:nombre,:descripcion,:comp)";
        $sql_dato = array(
                array(":nombre",$nombre),
                array(":descripcion",$descripcion),
                array(":comp",$compania),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
          //  var_dump($obj_resp);
        }
    break;
    #   *** Modificar Tipo Identificacion    ***  #
    case 3:
        $nombre   = $_REQUEST['nombre'];
        $descripcion   = $_REQUEST['descripcion'];
        $id       = $_REQUEST['id'];
        $sql_cons ="UPDATE  `gy_actividad`
        SET
        `nombre`=:nombre,
        `descripcion`=:descripcion
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":nombre",$nombre),
                array(":id_unico",$id),
                array(":descripcion",$descripcion),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
           // var_dump($obj_resp);
        }
    break;
    case 4:
    break;
}
