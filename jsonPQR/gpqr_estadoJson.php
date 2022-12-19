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
    #   *** Eliminar Estado    ***  #
    case 1:
        $id      = $_REQUEST['id'];
        if($id == 1 || $id == 2){
            echo 3;
        }else{
            $sql_cons ="DELETE FROM `gpqr_estado`
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
        }
        
    break;
    #   *** Guardar Estado    ***  #
    case 2:
        $nombre   = $_REQUEST['nombre'];
        #$descripcion   = $_REQUEST['descripcion'];
        $sql_cons ="INSERT INTO `gpqr_estado`
            ( `nombre`,`compania`)
            VALUES (:nombre,:comp)";
        $sql_dato = array(
                array(":nombre",$nombre),
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
    #   *** Modificar Estado    ***  #
    case 3:
        $nombre   = $_REQUEST['nombre'];
        #$descripcion   = $_REQUEST['descripcion'];
        $id       = $_REQUEST['id'];

        if($id == 1 || $id == 2){
            echo 3;
        }else{
            $sql_cons ="UPDATE  `gpqr_estado`
            SET
            `nombre`=:nombre
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                    array(":nombre",$nombre),
                    array(":id_unico",$id),
                    
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($obj_resp)){
                echo 1;
            } else {
                echo 2;
               // var_dump($obj_resp);
            }
        }
        
    break;
    case 4:
    break;
}
