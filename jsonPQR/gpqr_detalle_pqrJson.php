<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#17/12/2018 | Nestor B. | Archivo Creado
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
    #   *** Eliminar Detalle PQR    ***  #
    case 1:
        $id      = $_REQUEST['id'];

        $ruta = "SELECT ruta_archivo FROM gpqr_detalle_pqr WHERE id_unico = '$id'";
        $resr = $mysqli->query($ruta);
        $rowR = mysqli_fetch_row($resr);
        
        if(file_exists('../'.$rowR[0])){
            
            unlink('../'.$rowR[0]);
        }
        
        $sql_cons ="DELETE FROM `gpqr_detalle_pqr`
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
    #   *** Guardar Detalle PQR    ***  #
    case 2:
        $idPQR      = $_REQUEST['txtIdP'];
        $tipos      = $_REQUEST['sltTipoS'];
        $descri     = $_REQUEST['sltDesc'];
        $detaAs     = $_REQUEST['sltDetA'];
        if(empty($detaAs)){
            $detaAs = 'NULL';
        }

        

        $clase      = $_REQUEST['sltClase'];
        $obser      = $_REQUEST['txtObser'];


        if(!empty($_FILES['txtruta']['name'])){
                $dir_subida = '../documentos/PQR/';
                $dir_subida1 = 'documentos/PQR/';
                $doc = $_FILES['txtruta']['tmp_name'];	
                $archivo = $dir_subida.basename($_FILES['txtruta']['name']);
                $archivo = str_replace(" ", "_", $archivo);
                $archivo1 = $dir_subida1.basename($_FILES['txtruta']['name']);
                $archivo1 = str_replace(" ", "_", $archivo1);
                @move_uploaded_file($doc,$archivo);
        }else{
            $archivo1 = 'NULL';
        }

        $fecha = $_REQUEST['sltfecha'];
        $f_d   = explode("/",$fecha);
        $dia   = $f_d[0];
        $mes   = $f_d[1];
        $ann   = $f_d[2];

        $FecD = ''.$ann.'-'.$mes.'-'.$dia.'';  
        
        $sql = "INSERT INTO gpqr_detalle_pqr(id_pqr,id_servicio,id_descripcion,ruta_archivo,observaciones,id_clase,id_unico_asociado,fecha,compania)
            VALUES($idPQR, $tipos, $descri,'$archivo1','$obser',$clase,$detaAs,'$FecD',$compania)";
        
        $res = $mysqli->query($sql);        

        $indicadorC = "SELECT indicador_cierre FROM gpqr_clase WHERE id_unico = '$clase'";
        $indiC = $mysqli->query($indicadorC);
        $resIC = mysqli_fetch_row($indiC);

        if($resIC[0] == 1){
            $afavor  = $_REQUEST['sltAfavor'];
            $sqlup = "UPDATE gpqr_pqr SET id_afavor = $afavor, id_estado_pqr = 2 WHERE id_unico = '$idPQR'";
            $resup = $mysqli->query($sqlup);
        }

        
        if($res == 1){
            echo 1;
        } else {
            echo 2;
            //var_dump($obj_resp);
        }
    break;
    #   *** Modificar Detalle PQR   ***  #
    case 3:
        $idPQR      = $_REQUEST['idPQR'];
        $id         = $_REQUEST['idDPQR'];
        $tipos      = $_REQUEST['sltTipoS'];
        $descri     = $_REQUEST['sltDesc'];
        $detaAs     = $_REQUEST['sltDetA'];
        if(empty($detaAs)){
            $detaAs = 'NULL';
        }

        $clas      = $_REQUEST['sltClase'];
        $obser      = $_REQUEST['txtObser'];

        $fecha = $_REQUEST['sltfecha'];
        $f_d   = explode("/",$fecha);
        $dia   = $f_d[0];
        $mes   = $f_d[1];
        $ann   = $f_d[2];

        $FecD = ''.$ann.'-'.$mes.'-'.$dia.'';  
        
        $sql = "UPDATE gpqr_detalle_pqr SET id_servicio = '$tipos', id_descripcion = '$descri', observaciones = '$obser',id_clase ='$clas',id_unico_asociado = $detaAs ,fecha = '$FecD' WHERE id_unico = '$id'";

        $res = $mysqli->query($sql);        

        $indicadorC = "SELECT indicador_cierre FROM gpqr_clase WHERE id_unico = '$clas'";
        $indiC = $mysqli->query($indicadorC);
        $resIC = mysqli_fetch_row($indiC);

        if($resIC[0] == 1){
            $afavor  = $_REQUEST['sltAfavor'];
            $sqlup = "UPDATE gpqr_pqr SET id_afavor = $afavor, id_estado_pqr = 2 WHERE id_unico = '$idPQR'";
            $resup = $mysqli->query($sqlup);
        }

        
        if($res == 1){
            echo 1;
        } else {
            echo 2;
            //var_dump($obj_resp);
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
                $archivo1 = $dir_subida1.basename($_FILES['txtruta']['name']);
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
}
