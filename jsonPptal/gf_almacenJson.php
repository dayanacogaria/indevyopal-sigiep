<?php
require '../Conexion/ConexionPDO.php';                                                  
require '../Conexion/conexion.php';                    
require './funcionesPptal.php';
ini_set('max_execution_time', 0);
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$anno       = anno($panno);
$action     = $_REQUEST['action'];
$fechaa     = date('Y-m-d');
switch ($action){
    #* Modificar Vida útil
    case 1:
        $id_producto = $_REQUEST['producto'];
        $valor       =  $_REQUEST['valor'];
        $sql_cons ="UPDATE `gf_producto` 
                SET `vida_util_remanente`=:vida_util_remanente 
                WHERE `id_unico`=:id_unico";
        $sql_dato = array(
            array(":vida_util_remanente",$valor),
            array(":id_unico",$id_producto),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    case 2:
        $id_producto = $_REQUEST['producto'];
        $valor       =  $_REQUEST['valor'];
        $sql_cons ="UPDATE `gf_producto_especificacion` 
                SET `valor`=:valor 
                WHERE `id_unico`=:id_unico";
        $sql_dato = array(
            array(":valor",$valor),
            array(":id_unico",$id_producto),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    case 3:
        #BUscar si ya existe
        $ex = $con->Listar("SELECT * FROM gf_producto_especificacion WHERE producto =".$_REQUEST['producto']." AND fichainventario = ".$_REQUEST['ficha']);
        if(!empty($ex[0][0])){
            $sql_cons ="UPDATE `gf_producto_especificacion` 
                SET `valor`=:valor 
                WHERE `id_unico`=:id_unico";
            $sql_dato = array(
                array(":valor",$_REQUEST['valor']),
                array(":id_unico",$ex[0][0]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        } else { 
            $sql_cons ="INSERT INTO `gf_producto_especificacion` 
                    ( `valor`,`producto`,`fichainventario`) 
            VALUES (:valor, :producto, :fichainventario)";
            $sql_dato = array(
                array(":valor",$_REQUEST['valor']),
                array(":producto",$_REQUEST['producto']),
                array(":fichainventario",$_REQUEST['ficha']),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    #* Modificar Descripcion
    case 4:
        $id_producto = $_REQUEST['producto'];
        $valor       =  $_REQUEST['valor'];
        $sql_cons ="UPDATE `gf_producto` 
                SET `descripcion`=:descripcion 
                WHERE `id_unico`=:id_unico";
        $sql_dato = array(
            array(":descripcion",$valor),
            array(":id_unico",$id_producto),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    
    #* Guardar Imagen producto
    case 5:
        $e=0;
        if(!empty($_FILES['file']['name'])) {
            $id         = $_REQUEST['txtProducto'];    
            $imagen     = $_FILES['file'];
            $nombre     = $_FILES['file']['name'];
            $directorio ='../documentos/imagenes_producto/';
            $nombre     = $id.$nombre;
            $ruta       = 'documentos/imagenes_producto/'.$nombre;
            //var_dump($_FILES['file']['tmp_name'],$directorio.$nombre);
            $upd = move_uploaded_file($_FILES['file']['tmp_name'],$directorio.$nombre); 
            if($upd == true){
                $sql_cons ="INSERT INTO `gf_imagen_producto` 
                    ( `producto`,`ruta`) 
                VALUES (:producto, :ruta)";
                $sql_dato = array(
                    array(":producto",$id),
                    array(":ruta",$nombre)
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                if(empty($resp)){
                    $e=1;
                }
            }
        }
        echo $e;
    break;
    #Eliminar Producto
    case 6:
        $id         = $_REQUEST['id'];    
        $sql_cons  = "DELETE FROM `gf_imagen_producto` 
            WHERE `id_unico`=:id_unico";
        $sql_dato = array(
                array(":id_unico",$id),	
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
}