<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#19/11/218 | Nestor B. | Archivo Creado
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
    #   *** Eliminar el seguimiento    ***  #
    case 1:
        $id      = $_REQUEST['id'];
        $existeDocumento = "SELECT * FROM gy_documento_proyecto WHERE id_seguimiento = '$id'";
        $res = $mysqli->query($existeDocumento);
        $nexiste = mysqli_num_rows($res);
        
        if($nexiste < 1){
            $sql_cons ="DELETE FROM `gy_seguimiento`
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
        }else{
            echo 3;
        }
        
        
    break;
    #   *** Guardar el seguimiento de la Actividad     ***  #
    case 2:
        $actividad     = $_REQUEST['Acti'];
        $descripcion   = $_REQUEST['txtDescripcion'];
        $estado        = $_REQUEST['sltEstado'];
        $fecha         = $_REQUEST['sltFechaS'];
        $fecha1 = trim($fecha, '"');
        $fecha_div = explode("/", $fecha1);
        $anio1 = $fecha_div[2];
        $mes1 = $fecha_div[1];
        $dia1 = $fecha_div[0];
        $fecha = ''.$anio1.'-'.$mes1.'-'.$dia1.''; 

        if(empty($_REQUEST['txtObservaciones'])){
            $observaciones = NULL;
        }else{
            $observaciones   = $_REQUEST['txtObservaciones'];
        }
        
        
        
        $sql_cons ="INSERT INTO `gy_seguimiento`
            ( `id_actividad_proyecto`, `fecha_seguimiento`, `descripcion`, `id_estado`, `observaciones` )
            VALUES (:actividad,:fecha, :descr, :estado, :obser)";
        $sql_dato = array(
                array(":actividad",$actividad),
                array(":fecha",$fecha),
                array(":descr",$descripcion),
                array(":estado",$estado),
                array(":obser",$observaciones)
                
        );

        #echo $sql = "INSERT INTO gy_seguimiento(id_actividad_proyecto, fecha_seguimiento, descripcion, id_estado,observaciones)values($actividad,'$fecha','$descripcion',$estado,'$observaciones')";

        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
          //  var_dump($obj_resp);
        }
    break;
    #   *** Modificar el seguimineto de la actividad  ***  #
    case 3:
        $segui          = $_REQUEST['Segui_Act'];
        $fecha          = $_REQUEST['sltFechaini'];
        $Descripcion    = $_REQUEST['txtDescripcion'];
        $estado         = $_REQUEST['sltEst'];
        
            
         if(empty($_REQUEST['txtObser'])){
            $observaciones = NULL;
        }else{
            $observaciones   = $_REQUEST['txtObser'];
        }
        /*echo $con = "UPDATE gy_seguimiento SET 
                fecha_seguimiento = '$fecha', 
                descripcion = '$Descripcion',
                id_estado = $estado,
               observaciones = '$observaciones'
                 
               WHERE id_unico =  $segui";*/
        $sql_cons ="UPDATE  `gy_seguimiento`
        SET
        `fecha_seguimiento`=:fechaS,
        `descripcion`=:des,
        `id_estado`=:est,
        `observaciones`=:ob
        
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":fechaS",$fecha),
                array(":des",$Descripcion),
                array(":est",$estado),
                array(":ob",$observaciones),
                array(":id_unico",$segui)
                
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
