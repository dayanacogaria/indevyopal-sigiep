<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#13/12/2018 | Nestor b. | Archivo Creado
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
    #   *** Eliminar Clase Descripción    ***  #
    case 1:
        $id      = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gpqr_clase`
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
    #   *** Guardar Clase    ***  #
    case 2:
        $nombre   = $_REQUEST['nombre'];
        $indica   = $_REQUEST['txtInd'];
        $sql_cons ="INSERT INTO `gpqr_clase`
            ( `nombre`,`indicador_cierre`,`compania`)
            VALUES (:nombre,:ind,:comp)";
        $sql_dato = array(
                array(":nombre",$nombre),
                array(":ind",$indica),
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
    #   *** Modificar Clase   ***  #
    case 3:
        $nombre   = $_REQUEST['nombre'];
        $indica   = $_REQUEST['txtInd'];
        $id       = $_REQUEST['id'];
        $sql_cons ="UPDATE  `gpqr_clase`
        SET
        `nombre`=:nombre,
        `indicador_cierre`=:ind
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":nombre",$nombre),
                array(":id_unico",$id),
                array(":ind",$indica),
                
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
