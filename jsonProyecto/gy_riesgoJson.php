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
        $sql_cons ="DELETE FROM `gy_riesgo`
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
        $tipo_riesgo   = $_REQUEST['tipo_riesgo'];
        $sql_cons ="INSERT INTO `gy_riesgo`
            ( `nombre`, `tipo_riesgo`)
            VALUES (:nombre,:tipo_riesgo)";
        $sql_dato = array(
                array(":nombre",$nombre),
                array(":tipo_riesgo",$tipo_riesgo),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
          var_dump($obj_resp);
        }
    break;
    #   *** Modificar Tipo Identificacion    ***  #
    case 3:
        $nombre   = $_REQUEST['nombre'];
        $tipo_riesgo   = $_REQUEST['tipo_riesgo'];
        $id       = $_REQUEST['id'];
        $sql_cons ="UPDATE  `gy_riesgo`
        SET
        `nombre`=:nombre,
        `tipo_riesgo`=:tipo_riesgo
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":nombre",$nombre),
                array(":id_unico",$id),
                array(":tipo_riesgo",$tipo_riesgo),
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
