<?php
require '../Conexion/ConexionPDO.php';
require '../Conexion/conexion.php';

@session_start();
setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$usuario_t  = $_SESSION['usuario_tercero'];
$action     = $_REQUEST['action'];
switch ($action) {
    #Guardar Tipo D
    case 1:
        $rangoI     = $_REQUEST['rangoI'];
        $rangoF     = $_REQUEST['rangoF'];
        $uvtd       = $_REQUEST['uvtd'];
        $uvts       = $_REQUEST['uvts'];
        $tarifam    = $_REQUEST['tarifam'];
        
        $sql_cons ="INSERT INTO `gn_rango_retencion` 
            ( `rango_inicial`, `rango_final`, `uvt_descontar`, `uvt_sumar`, `tarifa_marginal`) 
        VALUES (:rango_inicial, :rango_final ,:uvt_descontar,:uvt_sumar, :tarifa_marginal)";
        $sql_dato = array(
            array(":rango_inicial",$rangoI),
            array(":rango_final",$rangoF),
            array(":uvt_descontar",$uvtd),
            array(":uvt_sumar",$uvts),
            array(":tarifa_marginal",$tarifam),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    #Modificar  Tipo D
    case 2:
        $id             = $_REQUEST['id'];
        $rangoI     = $_REQUEST['rangoI'];
        $rangoF     = $_REQUEST['rangoF'];
        $uvtd       = $_REQUEST['uvtd'];
        $uvts       = $_REQUEST['uvts'];
        $tarifam    = $_REQUEST['tarifam'];
        
        
        $sql_cons ="UPDATE `gn_rango_retencion` 
                SET  `rango_inicial` =:rango_inicial, 
                `rango_final`=:rango_final, `uvt_descontar` =:uvt_descontar, 
                `uvt_sumar`=:uvt_sumar, `tarifa_marginal`=:tarifa_marginal 
                WHERE `id_unico`=:id_unico";
        $sql_dato = array(
            array(":rango_inicial",$rangoI),
            array(":rango_final",$rangoF),
            array(":uvt_descontar",$uvtd),
            array(":uvt_sumar",$uvts),
            array(":tarifa_marginal",$tarifam),
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
    
    #* Eliminar  Tipo D
    case 3:
        $id     = $_POST['id'];
        $sql_cons ="DELETE FROM `gn_rango_retencion` 
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
