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
        $sql_cons ="DELETE FROM `gy_comite`
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
        $tercero   = $_REQUEST['tercero'];
        $comite   = $_REQUEST['comite'];
        $sql_cons ="INSERT INTO `gy_comite`
            (`id_tercero`, `id_tipo`)
            VALUES (:id_tercero,:id_tipo)";
        $sql_dato = array(
                array(":id_tercero",$tercero),
                array(":id_tipo",$comite),
               
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
        $tercero   = $_REQUEST['tercero'];
        $id       = $_REQUEST['id'];
        $comite   = $_REQUEST['comite'];
        $sql_cons ="UPDATE  `gy_comite`
        SET
        `id_tercero`=:id_tercero,
        `id_tipo`=:id_tipo
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":id_tercero",$tercero),
                array(":id_unico",$id),
                array(":id_tipo",$comite),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
            var_dump($obj_resp);
        }
    break;
    case 4:
    break;
}
