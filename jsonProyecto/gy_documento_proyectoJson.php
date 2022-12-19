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
    #   *** Eliminar Documento    ***  #
    case 1:
        $id      = $_REQUEST['id'];

        $ruta = "SELECT ruta FROM gy_documento_proyecto WHERE id_unico = '$id'";
        $resr = $mysqli->query($ruta);
        $rowR = mysqli_fetch_row($resr);
        
        if(file_exists('../'.$rowR[0])){
            
            unlink('../'.$rowR[0]);
        }
        
        
        $sql_cons ="DELETE FROM `gy_documento_proyecto`
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
    #   *** Guardar Documento    ***  #
    case 2:
        $proyecto   = 'NULL';
        $tipop      = 'NULL';
        $id_segui    = $_REQUEST['txtSeguimiento'];
        $nombre     = $_REQUEST['txtNombre'];
        $obser      = $_REQUEST['txtOb'];
        if(!empty($_FILES['txtruta']['name'])){
                $dir_subida = '../documentos/proyectos/';
                $dir_subida1 = 'documentos/proyectos/';
                $doc = $_FILES['txtruta']['tmp_name'];	

                $archivo = $dir_subida.basename($_FILES['txtruta']['name']);
                $archivo = str_replace(" ", "_", $archivo);
                
                $archivo1 = $dir_subida1.basename($_FILES['txtruta']['name']);
                $archivo1 = str_replace(" ", "_", $archivo1);
                @move_uploaded_file($doc,$archivo);
        }else{
            $archivo1 = 'NULL';
        }
        
        $sql = "INSERT INTO gy_documento_proyecto (id_proyecto,id_tipo_proyecto,id_seguimiento,nombre,ruta,observaciones)"
                . "VALUES($proyecto, $tipop, $id_segui, '$nombre','$archivo1','$obser')";
        /*$sql_cons ="INSERT INTO `gy_documento_proyecto`
            ( `id_proyecto`, `id_tipo_proyecto`, `id_seguimiento`, `nombre`, `ruta`, `observaciones`)
            VALUES (:proyecto,:tipo, :seguimiento, :nombre, :ruta, :obs)";
        /*$sql_dato = array(
                array(":proyecto",$proyecto),
                array(":tipo",$tipop),
                array(":seguimiento",$id_segui),
                array(":nombre",$nombre),
                array(":ruta",$archivo),
                array(":obs",$obser)
                
        );*/

        

        #$obj_resp = $con->InAcEl($sql_cons,$sql_dato);
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
    #   *** Guardar Documento  cuando viene de modificar proyecto  ***  #
    case 4:
        $proyecto   = $_REQUEST['txtidP'];
        $tipop      = 'NULL';
        $id_segui    = 'NULL';
        $nombre     = $_REQUEST['txtNombre'];
        $obser      = $_REQUEST['txtOb'];
        if(!empty($_FILES['txtruta']['name'])){
                $dir_subida = '../documentos/proyectos/';
                $dir_subida1 = 'documentos/proyectos/';
                $doc = $_FILES['txtruta']['tmp_name'];	
                $archivo = $dir_subida.basename($_FILES['txtruta']['name']);
                $archivo = str_replace(" ", "_", $archivo);
                $archivo1 = $dir_subida1.basename($_FILES['txtruta']['name']);
                $archivo1 = str_replace(" ", "_", $archivo1);
                @move_uploaded_file($doc,$archivo);
        }else{
            $archivo1 = 'NULL';
        }
        
        $sql = "INSERT INTO gy_documento_proyecto (id_proyecto,id_tipo_proyecto,id_seguimiento,nombre,ruta,observaciones)"
                . "VALUES($proyecto, $tipop, $id_segui, '$nombre','$archivo1','$obser')";
        /*$sql_cons ="INSERT INTO `gy_documento_proyecto`
            ( `id_proyecto`, `id_tipo_proyecto`, `id_seguimiento`, `nombre`, `ruta`, `observaciones`)
            VALUES (:proyecto,:tipo, :seguimiento, :nombre, :ruta, :obs)";
        /*$sql_dato = array(
                array(":proyecto",$proyecto),
                array(":tipo",$tipop),
                array(":seguimiento",$id_segui),
                array(":nombre",$nombre),
                array(":ruta",$archivo),
                array(":obs",$obser)
                
        );*/

        

        #$obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        $res = $mysqli->query($sql);
        if($res == 1){
            echo 1;
        } else {
            echo 2;
            //var_dump($obj_resp);
        }
    break;

    case 5:
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
}
