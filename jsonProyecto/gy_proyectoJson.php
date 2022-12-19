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
    #   *** Eliminar Proyecto    ***  #
    case 1:
        $id      = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gy_proyecto`
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
    #   *** Guardar Proyecto    ***  #
    case 2:
        #$nombre   = $_REQUEST['nombre'];
        #$proyecto   = $_REQUEST['proyecto'];
        $categoria   = $_REQUEST['categoria'];
        $tipo_proyecto   = $_REQUEST['tipo_proyecto'];
        $fecha   = $_REQUEST['fecha'];
        $fecha_div = explode("/", $fecha);
        $anio1 = $fecha_div[2];
        $mes1 = $fecha_div[1];
        $dia1 = $fecha_div[0];
        $fecha = ''.$anio1.'-'.$mes1.'-'.$dia1.'';   
        $titulo   = $_REQUEST['titulo'];
        $monto_solicitado   = $_REQUEST['monto_solicitado'];
        $monto_solicitado = str_replace(',', '', $monto_solicitado);
        $monto_aportado   = $_REQUEST['monto_aportado'];
        $monto_aportado = str_replace(',', '', $monto_aportado);
        $monto_total   = $_REQUEST['monto_total'];
        $monto_total = str_replace(',', '', $monto_total);
        $ciudad   = $_REQUEST['ciudad'];
        
        
        $id_MAX = "SELECT MAX(id_unico) FROM gf_proyecto WHERE id_unico != 2147483647 ";
        $res_id = $mysqli->query($id_MAX);
        $rowP = mysqli_fetch_row($res_id);
        
        $rowP[0] =  $rowP[0] + 1;
        
        $insert = "INSERT INTO gf_proyecto(id_unico,nombre, compania)VALUES($rowP[0],'$titulo',$compania)";
        $res = $mysqli->query($insert);
        
        $proyecto = "INSERT INTO gy_proyecto(id_proyecto, id_categoria,id_tipo_proyecto,fecha_inicio,titulo,monto_solicitado,monto_aportado,monto_total,id_ciudad_ubicacion,compania)
                        VALUES($rowP[0],$categoria,$tipo_proyecto,'$fecha','$titulo',$monto_solicitado,$monto_aportado,$monto_total, $ciudad,$compania) ";
        
        $result = $mysqli->query($proyecto);
        
        /*
        $sql_cons ="INSERT INTO `gy_proyecto`
            (  `id_proyecto`, `id_categoria`, `id_tipo_proyecto`, `fecha_inicio`, `titulo`, `monto_solicitado`, `monto_aportado`, `monto_total`, `id_ciudad_ubicacion`)
            VALUES (:id_proyecto,:id_categoria,:id_tipo_proyecto,:fecha_inicio,:titulo,:monto_solicitado,:monto_aportado,:monto_total,:id_ciudad_ubicacion)";
        $sql_dato = array(
                array(":id_proyecto",$rowP[0]),
                array(":id_categoria",$categoria),
                array(":id_tipo_proyecto",$tipo_proyecto),
                array(":fecha_inicio","'".$fecha."'"),
                array(":titulo",$titulo),
                array(":monto_solicitado",$monto_solicitado),
                array(":monto_aportado",$monto_aportado),
                array(":monto_total",$monto_total),
                array(":id_ciudad_ubicacion",$ciudad),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);*/
        
        if($result == 1){
            echo 1;
        } else {
            echo 2;
          //  var_dump($obj_resp);
        }
    break;
    #   *** Modificar proyecto   ***  #
    case 3:
        
        $id             = $_REQUEST['txtidP'];
        $categoria      = $_REQUEST['categoria'];
        $tipo_proyecto  = $_REQUEST['tipo_proyecto'];
        $fecha   = $_REQUEST['fecha'];
        $fecha_div = explode("/", $fecha);
        $anio1 = $fecha_div[2];
        $mes1 = $fecha_div[1];
        $dia1 = $fecha_div[0];
        $fecha = ''.$anio1.'-'.$mes1.'-'.$dia1.'';   
        $titulo   = $_REQUEST['titulo'];
        $monto_solicitado   = $_REQUEST['monto_solicitado'];
        $monto_solicitado = str_replace(',', '', $monto_solicitado);
        $monto_aportado   = $_REQUEST['monto_aportado'];
        $monto_aportado = str_replace(',', '', $monto_aportado);
        $monto_total   = $_REQUEST['monto_total'];
        $monto_total = str_replace(',', '', $monto_total);
        $ciudad   = $_REQUEST['ciudad'];
        
        $sqlUpdate = "UPDATE gy_proyecto SET id_categoria = '$categoria', id_tipo_proyecto = '$tipo_proyecto',
                        fecha_inicio = '$fecha', titulo = '$titulo', monto_solicitado = $monto_solicitado,
                        monto_aportado = $monto_aportado, monto_total = $monto_total, id_ciudad_ubicacion = $ciudad
                        WHERE id_unico = '$id'";
        
        $res = $mysqli->query($sqlUpdate);
        
        $id_proyecto = $_REQUEST['id_proyecto'];
       
        $sql_cons ="UPDATE  `gf_proyecto`
        SET `nombre`=:nombre 
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":nombre",$titulo),
                array(":id_unico",$id_proyecto),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        //var_dump($obj_resp);
        if($res == 1){
            echo 1;
        } else {
            echo 2;
           
        }
    break;
    case 4:
    break;
}
