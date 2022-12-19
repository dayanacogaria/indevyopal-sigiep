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
    #   *** Eliminar pegunta  ***  #
    case 1:
        $id      = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gy_pregunta`
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
    #   *** Guardar pregunta    ***  #
    case 2:
        $nombre   = $_REQUEST['nombre'];
        $clase   = $_REQUEST['clase_pregunta'];
        $tipo    = $_REQUEST['sltTipoP'];
        $sql_cons ="INSERT INTO `gy_pregunta`
            ( `nombre`, `id_clase_pregunta`, `id_tipo_pregunta`,`compania`)
            VALUES (:nombre,:clase,:tipo,:comp)";
        $sql_dato = array(
                array(":nombre",$nombre),
                array(":clase",$clase),
                array(":tipo",$tipo),
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
    #   *** Modificar pregunta    ***  #
    case 3:
        $nombre   = $_REQUEST['nombre'];
        $clase  = $_REQUEST['clase_pregunta'];
        $tipo    = $_REQUEST['sltTipoP'];
        $id       = $_REQUEST['id'];

        $sql = "UPDATE gy_pregunta SET nombre = '$nombre', id_clase_pregunta = '$clase', id_tipo_pregunta = '$tipo' WHERE id_unico = '$id'";

        $res = $mysqli->query($sql);
        /*$sql_cons ="UPDATE  `gy_pregunta`
        SET
        `nombre`=:nombre,
        `id_clase_pregunta`=:clase,
        `id_tipo_pregunta` = :tipo
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":nombre",$nombre),
                array(":id_unico",$id),
                array(":clase",$clase),
                array(":tipo",$tipo),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);*/
        if($res == 1){
            echo 1;
        } else {
            echo 2;
           //var_dump($obj_resp);
           //var_dump($sql_cons)
        }
    break;
    case 4:
    break;
}
