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
        $sql_cons ="DELETE FROM `gf_tipo_identificacion` 
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":id_unico",$id),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
            var_dump($obj_resp);
        }
    break;
    #   *** Guardar Tipo Identificacion    ***  #
    case 2:
        $nombre   = $_REQUEST['nombre'];
        $sigla    = mb_strtoupper($_REQUEST['sigla']);
        $codigo   = $_REQUEST['codigo'];
        
        $sql_cons ="INSERT INTO `gf_tipo_identificacion` 
            ( `nombre`,  `sigla`,`codigo`) 
            VALUES (:nombre, :sigla, :codigo)";
        $sql_dato = array(
                array(":nombre",$nombre),
                array(":sigla",$sigla),
                array(":codigo",$codigo),
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
        $sigla    = mb_strtoupper($_REQUEST['sigla']);
        $codigo   = $_REQUEST['codigo'];
        $id       = $_REQUEST['id'];
        
        $sql_cons ="UPDATE  `gf_tipo_identificacion` 
        SET  
        `nombre`=:nombre, 
        `sigla`=:sigla,
        `codigo`=:codigo 
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":nombre",$nombre),
                array(":sigla",$sigla), 
                array(":codigo",$codigo), 
                array(":id_unico",$id),
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
