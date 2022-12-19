<?php
###################################################################################
#   **********************      Modificaciones      ******************************#
###################################################################################
#27/09/2018 |Erica G. | Creado
###################################################################################
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
session_start(); 
$con = new ConexionPDO();

switch ($_REQUEST['action']) {
    
    #** Guardar **#
    case 1:
        $predio         = $_REQUEST['predio'];
        $tercero        = $_REQUEST['tercero'];
        $uso            = $_REQUEST['uso'];
        $estrato        = $_REQUEST['estrato'];
        $sector         = $_REQUEST['sector'];
        if(empty($_REQUEST['manzana'])){
            $manzana    = NULL;
        } else {
            $manzana    = $_REQUEST['manzana'];
        }
        if(empty($_REQUEST['codigoR'])){
            $codigo_ruta= NULL;
        } else {
            $codigo_ruta = $_REQUEST['codigoR'];
        }
        
                
        $sql_cons = "INSERT INTO `gp_unidad_vivienda`  
            (`predio`,`tercero`,
            `uso`,`estrato`,
            `sector`,`manzana`,
            `codigo_ruta`) 
            VALUES(:predio,:tercero,
            :uso,:estrato,
            :sector,:manzana,
            :codigo_ruta)";
        $sql_dato = array(
                array(":predio",$predio),
                array(":tercero",$tercero),
                array(":uso",$uso),
                array(":estrato",$estrato),
                array(":sector",$sector),
                array(":manzana",$manzana),
                array(":codigo_ruta",$codigo_ruta),
        );
        
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $rta = true;
        } else {
            $rta = false;
        }
        echo $rta;
    break;
    #** Eliminar **#
    case 2:
        $id = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gp_unidad_vivienda`  
            WHERE `id_unico` =:id_unico";
        $sql_dato = array(
                array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $rta = true;
        } else {
            $rta = false;
        }
        echo $rta;
    break;
    #** Modificar **#
    case 3:
        $id             = $_REQUEST['id'];
        $predio         = $_REQUEST['predio'];
        $tercero        = $_REQUEST['tercero'];
        $uso            = $_REQUEST['uso'];
        $estrato        = $_REQUEST['estrato'];
        $sector         = $_REQUEST['sector'];
        if(empty($_REQUEST['manzana'])){
            $manzana    = NULL;
        } else {
            $manzana    = $_REQUEST['manzana'];
        }
        if(empty($_REQUEST['codigoR'])){
            $codigo_ruta= NULL;
        } else {
            $codigo_ruta = $_REQUEST['codigoR'];
        }
        
                
        $sql_cons = "UPDATE `gp_unidad_vivienda`  
            SET `predio`=:predio,
            `tercero`=:tercero,
            `uso`=:uso,
            `estrato`=:estrato,
            `sector`=:sector,
            `manzana`=:manzana,
            `codigo_ruta`=:codigo_ruta 
            WHERE `id_unico`=:id_unico ";
        $sql_dato = array(
                array(":predio",$predio),
                array(":tercero",$tercero),
                array(":uso",$uso),
                array(":estrato",$estrato),
                array(":sector",$sector),
                array(":manzana",$manzana),
                array(":codigo_ruta",$codigo_ruta),
                array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        
        if(empty($resp)){
            $rta = true;
        } else {
            $rta = false;
        }
        echo $rta;
    break;
    
    #** Guardar Predio Y Unidad vivienda **#
    case 4:
        $codigo_catastral   = $_REQUEST['codigoC'];
        $nombre             = $_REQUEST['codigoC'];
        $direccion          = $_REQUEST['direccion'];
        $Ciudad             = $_REQUEST['ciudad'];
        $estrato            = $_REQUEST['estrato'];
        $estado             = 4;
        
        $sql_cons = "INSERT INTO `gp_predio1`  
            (`codigo_catastral`,
            `direccion`,
            `ciudad`,
            `nombre`,
            `estado`,`estrato`) 
            VALUES(:codigo_catastral,
            :direccion,
            :ciudad,
            :nombre,
            :estado,:estrato)";
        $sql_dato = array(
                array(":codigo_catastral",$codigo_catastral),
                array(":direccion",$direccion),
                array(":ciudad",$Ciudad),
                array(":nombre",$nombre),
                array(":estado",$estado),
                array(":estrato",$estrato),
                
        );
        
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $predio         = $con->Listar("SELECT MAX(id_unico) FROM gp_predio1 WHERE codigo_catastral = $codigo_catastral");
            $predio         = $predio[0][0];
            $tercero        = $_REQUEST['tercero'];
            $uso            = $_REQUEST['uso'];
            $estrato        = $_REQUEST['estrato'];
            $sector         = $_REQUEST['sector'];
            if(empty($_REQUEST['manzana'])){
                $manzana    = NULL;
            } else {
                $manzana    = $_REQUEST['manzana'];
            }
            if(empty($_REQUEST['codigoR'])){
                $codigo_ruta= NULL;
            } else {
                $codigo_ruta = $_REQUEST['codigoR'];
            }

            $sql_cons = "INSERT INTO `gp_unidad_vivienda`  
                (`predio`,`tercero`,
                `uso`,`estrato`,
                `sector`,`manzana`,
                `codigo_ruta`) 
                VALUES(:predio,:tercero,
                :uso,:estrato,
                :sector,:manzana,
                :codigo_ruta)";
            $sql_dato = array(
                    array(":predio",$predio),
                    array(":tercero",$tercero),
                    array(":uso",$uso),
                    array(":estrato",$estrato),
                    array(":sector",$sector),
                    array(":manzana",$manzana),
                    array(":codigo_ruta",$codigo_ruta),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            var_dump($resp);
            if(empty($resp)){
                $rta = true;
            } else {
                $rta = false;
            }
        }
        else{
            $rta = false;            
        }
        echo $rta;
    break;
}

