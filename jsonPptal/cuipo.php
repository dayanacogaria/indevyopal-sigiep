<?php
require '../Conexion/ConexionPDO.php';
require '../Conexion/conexion.php';
require './funcionesPptal.php';
require '../funciones/funcionEmail.php';
require '../jsonAlmacen/funcionesAlmacen.php';

@session_start();
setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$usuario_t  = $_SESSION['usuario_tercero'];
$anno       = anno($panno);
$action     = $_REQUEST['action'];
switch ($action) {
     #Guardar cuipo_norma
    case 1:
        $numero         = $_REQUEST['numero'];
        $id             = str_replace(" ", "", $_REQUEST['numero']);
        $sql_cons ="INSERT INTO `cuipo_norma` 
            ( `id_unico`, `nombre`, `parametrizacionanno`) 
        VALUES (:id_unico, :nombre , :parametrizacionanno)";
        $sql_dato = array(
            array(":id_unico",$id),
            array(":nombre",$numero),
            array(":parametrizacionanno",$panno),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        //var_dump($resp);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    #Modificar cuipo_norma
    case 2:
        $numero     = $_REQUEST['numero'];
        $id         = $_REQUEST['id'];
        $idr        = str_replace(" ", "", $_REQUEST['numero']);
        $sqlTI      = "UPDATE `cuipo_norma` 
                SET  `id_unico`='$idr' ,`nombre`='$numero'
                WHERE `id_unico`='$id'";
        $resp = $mysqli->query($sqlTI);

        if($resp==1){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    
    #* Eliminar Resoluciones
    case 3:
        $id     = $_POST['id'];
        $sql_cons ="DELETE FROM `cuipo_norma` 
                WHERE `id_unico` =:id_unico";
        $sql_dato = array(
                array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
     #Guardar cuipo_Fecha
    case 4:
        $numero         = str_replace('/', '-',$_REQUEST['numero']);
        $sql_cons ="INSERT INTO `cuipo_fecha_norma` 
            ( `id_unico`, `nombre`, `parametrizacionanno`) 
        VALUES (:id_unico, :nombre , :parametrizacionanno)";
        $sql_dato = array(
            array(":id_unico",$numero),
            array(":nombre",$numero),
            array(":parametrizacionanno",$panno),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        //var_dump($resp);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    #Modificar cuipo_fecha_norma
    case 5:
        $numero         = str_replace('/', '-',$_REQUEST['numero']);
        $id         = $_REQUEST['id'];
        $sqlTI      = "UPDATE `cuipo_fecha_norma` 
                SET  `id_unico`='$numero' ,`nombre`='$numero'
                WHERE `id_unico`='$id'";
        $resp = $mysqli->query($sqlTI);

        if($resp==1){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    
    #* Eliminar cuipo_fecha_norma
    case 6:
        $id     = $_POST['id'];
        $sql_cons ="DELETE FROM `cuipo_fecha_norma` 
                WHERE `id_unico` =:id_unico";
        $sql_dato = array(
                array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
}
