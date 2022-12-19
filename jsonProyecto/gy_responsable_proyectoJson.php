<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#26/11/2018 | Nestor B. | Archivo Creado
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
    #   *** Eliminar Responsable Proyecto   ***  #
    case 1:
        $id      = $_REQUEST['id'];
        
        $sql_cons ="DELETE FROM `gy_matriz_riesgo`
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
    #   *** Guardar Responsable Proyecto     ***  #
    case 2:
        $proyecto = $_REQUEST['txtIP'];
        $tercero  = $_REQUEST['sltTer'];
        $tipo     = $_REQUEST['sltTipo'];
        $sql = "INSERT INTO gy_tercero_proyecto(id_proyecto,id_tercero,id_tipo_responsable)
                VALUES($proyecto, $tercero, $tipo)";
       
        $res = $mysqli->query($sql);
        if($res == 1){
            echo 1;
        } else {
            echo 2;
            //var_dump($obj_resp);
        }
    break;
    #   *** Modificar la Actividad Proyecto   ***  #
    case 3:
        $proyecto   = NULL;
        $tipop      = NULL;
        $id_seui    = $_REQUEST['txtSeguimiento'];
        $nombre     = $_REQUEST['txtNombre'];
        $obser      = $_REQUEST['txtOb'];
        if(!empty($_FILES['txtruta']['name'])){
                $dir_subida = '../documentos/proyectos/';
                $doc = $_FILES['txtruta']['tmp_name'];	
                $archivo = $dir_subida.basename($_FILES['txtruta']['name']);
                @move_uploaded_file($doc,$archivo);
        }else{
            $archivo = 'NULL';
        }
        /*echo $con = "UPDATE gy_actividad_proyecto SET 
                id_actividad = $actividad, 
                fecha_inicio_programada = '$fecha_inicio', 
                fecha_final_programada = '$fecha_fin',
                valor_programado = $valor_p,
                valor_ejecutado = $valor_e,
                 responsable_actividad = $responsable
               WHERE id_unico =  $idAP";*/
        $sql_cons ="UPDATE  `gy_actividad_proyecto`
        SET
        `id_actividad`=:actividad,
        `fecha_inicio_programada`=:fechaI,
        `fecha_final_programada`=:fechaF,
        `valor_programado`=:vp,
        `valor_ejecutado`=:ve,
        `responsable_actividad`=:res
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":actividad",$actividad),
                array(":id_unico",$idAP),
                array(":fechaI",$fecha_inicio),
                array(":fechaF",$fecha_fin),
                array(":vp",$valor_p),
                array(":ve",$valor_e),
                array(":res",$responsable)
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        #$res = $mysqli->query($con);
        #echo "res: ".$res;
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
