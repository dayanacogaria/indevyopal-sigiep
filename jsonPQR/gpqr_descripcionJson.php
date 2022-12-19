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
        $sql_cons ="DELETE FROM `gpqr_descripcion`
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
    #   *** Guardar Descripcion    ***  #
    case 2:
        $nombre   = $_REQUEST['nombre'];
        $clase   = $_REQUEST['sltClase'];
        $sql_cons ="INSERT INTO `gpqr_descripcion`
            ( `descripcion`,`id_clase_descripcion`,`compania`)
            VALUES (:nombre,:clase,:comp)";
        $sql_dato = array(
                array(":nombre",$nombre),
                array(":clase",$clase),
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
    #   *** Modificar Descripcion   ***  #
    case 3:
        $nombre   = $_REQUEST['nombre'];
        $clase   = $_REQUEST['sltClase'];
        $id       = $_REQUEST['id'];
        $sql_cons ="UPDATE  `gpqr_descripcion`
        SET
        `descripcion`=:nombre,
        `id_clase_descripcion`=:clase
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":nombre",$nombre),
                array(":id_unico",$id),
                array(":clase",$clase),
                
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
