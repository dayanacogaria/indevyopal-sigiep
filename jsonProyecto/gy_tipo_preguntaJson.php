<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#06/12/2018 | Nestor B. | Archivo Creado
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
    #   *** Eliminar tipo pregunta  ***  #
    case 1:
        $id      = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gy_tipo_pregunta`
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
    #   *** Guardar tipo pregunta    ***  #
    case 2:
        $nombre   = $_REQUEST['nombre'];
        $clase   = $_REQUEST['clase_pregunta'];
        $sql_cons ="INSERT INTO `gy_tipo_pregunta`
            ( `nombre`, `id_clase_pregunta`, `compania`)
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
          var_dump($obj_resp);
        }
    break;
    #   *** Modificar tipo pregunta    ***  #
    case 3:
        $nombre   = $_REQUEST['nombre'];
        $clase  = $_REQUEST['clase_pregunta'];
        $id       = $_REQUEST['id'];
        $sql_cons ="UPDATE  `gy_tipo_pregunta`
        SET
        `nombre`=:nombre,
        `id_clase_pregunta`=:clase
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
