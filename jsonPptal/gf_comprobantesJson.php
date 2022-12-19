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
     #Guardar Resolución
    case 1:
        $tipo           = $_REQUEST['tipo'];
        $numero         = $_REQUEST['numero'];
        $fechaInicial   = fechaC($_REQUEST['fechaInicial']);
        $numeroInicial  = $_REQUEST['numeroInicial'];
        $numeroFinal    = $_REQUEST['numeroFinal'];
        if(empty($_REQUEST['fechaFinal'])){
            $fechaFinal     = 'NULL';
        } else {
            $fechaFinal     = fechaC($_REQUEST['fechaFinal']);
        }
        if(empty($_REQUEST['descripcion'])){
            $descripcion     = NULL;
        } else {
            $descripcion     = $_REQUEST['descripcion'];
        }
        
        $sql_cons ="INSERT INTO `gf_resolucion` 
            ( `tipo_comprobante_pptal`, `fecha_inicial`, `fecha_final`, 
            `numero_inicial`,`numero_final`,`descripcion`, `numero_resolucion`) 
        VALUES (:tipo_comprobante_pptal, :fecha_inicial ,:fecha_final,
        :numero_inicial,:numero_final,:descripcion, :numero_resolucion)";
        $sql_dato = array(
            array(":tipo_comprobante_pptal",$tipo),
            array(":fecha_inicial",$fechaInicial),
            array(":fecha_final",$fechaFinal),
            array(":numero_inicial",$numeroInicial),
            array(":numero_final",$numeroFinal),
            array(":descripcion",$descripcion),
            array(":numero_resolucion",$numero),
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
    #Modificar Resolución
    case 2:
        $id             = $_REQUEST['id'];
        $tipo           = $_REQUEST['tipo'];
        $numero         = $_REQUEST['numero'];
        $fechaInicial   = fechaC($_REQUEST['fechaInicial']);
        $numeroInicial  = $_REQUEST['numeroInicial'];
        $numeroFinal    = $_REQUEST['numeroFinal'];
        if(empty($_REQUEST['fechaFinal'])){
            $fechaFinal     = 'NULL';
        } else {
            $fechaFinal     = fechaC($_REQUEST['fechaFinal']);
        }
        if(empty($_REQUEST['descripcion'])){
            $descripcion     = NULL;
        } else {
            $descripcion     = $_REQUEST['descripcion'];
        }
        
        $sql_cons ="UPDATE `gf_resolucion` 
                SET  `tipo_comprobante_pptal`=:tipo_comprobante_pptal, `fecha_inicial`=:fecha_inicial ,
                `fecha_final`=:fecha_final,`numero_inicial`=:numero_inicial,
                `numero_final`=:numero_final,`descripcion`=:descripcion, 
                `numero_resolucion` =:numero_resolucion 
                WHERE `id_unico`=:id_unico";
        $sql_dato = array(
            array(":tipo_comprobante_pptal",$tipo),
            array(":fecha_inicial",$fechaInicial),
            array(":fecha_final",$fechaFinal),
            array(":numero_inicial",$numeroInicial),
            array(":numero_final",$numeroFinal),
            array(":descripcion",$descripcion),
            array(":numero_resolucion",$numero),
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
    
    #* Eliminar Resoluciones
    case 3:
        $id     = $_POST['id'];
        $sql_cons ="DELETE FROM `gf_resolucion` 
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
    
    #Numero Comprobante
    case 4:
        $numero = $anno. '000001';
        $tipo = $_POST['tipo'];
        $res  = $con->Listar("SELECT numero_inicial FROM `gf_resolucion` WHERE tipo_comprobante_pptal = $tipo ORDER BY id_unico DESC LIMIT 1");
        $fac = $con->Listar("SELECT * FROM gf_comprobante_pptal WHERE tipocomprobante = $tipo AND parametrizacionanno = $panno");
        if(count($fac)>0){
            $sql = $con->Listar("SELECT MAX(cast(numero as unsigned))+1 FROM gf_comprobante_pptal where tipocomprobante = $tipo AND parametrizacionanno = $panno");
            $numero = $sql[0][0];
        } else {
            if(count($res)>0){
                $numero = $res[0][0];
            } else {
                $numero = $anno. '000001';
            }
        }

        echo $numero;
    break;
}
