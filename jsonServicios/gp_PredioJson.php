<?php
###################################################################################
#   **********************      Modificaciones      ******************************#
###################################################################################
#22/08/2018 |Erica G. | Creado
###################################################################################
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
session_start(); 
$con = new ConexionPDO();

switch ($_REQUEST['action']) {
    #** Eliminar **#
    case 1:
        $id = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gp_predio1`  
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
    #** Guardar **#
    case 2:
        $codigo_catastral   = $_REQUEST['codigo_catastral'];
        $nombre             = $_REQUEST['nombre'];
        $matricula_in       = $_REQUEST['matricula_inmobiliaria'];
        $annio              = $_REQUEST['annio'];
        $codigo             = $_REQUEST['codigo'];
        $codigoIG           = $_REQUEST['codigoIG'];
        $direccion          = $_REQUEST['direccion'];
        $Ciudad             = $_REQUEST['Ciudad'];
        if(empty($_REQUEST['barrio'])){
            $barrio         = $_REQUEST['barrio'];
        } else {
            $barrio         = NULL;
        }
        if(empty($_REQUEST['estrato'])){
            $estrato        = NULL;
        } else {
            $estrato        = $_REQUEST['estrato'];
        }
        if(empty($_REQUEST['estado'])){
            $estado         = NULL;
        } else {
            $estado         = $_REQUEST['estado'];
        }
        
        if(empty($_REQUEST['ruta'])){
            $ruta           = NULL;
        } else { 
            $ruta           = $_REQUEST['ruta'];
        }
        if(empty($_REQUEST['tipoPredio'])){
            $tipoPredio     = NULL;
        } else { 
            $tipoPredio     = $_REQUEST['tipoPredio'];
        }
        if(empty($_REQUEST['predioa'])){
            $predioa        = NULL;
        } else {
            $predioa        = $_REQUEST['predioa'];
        }
                
        $sql_cons = "INSERT INTO `gp_predio1`  
            (`codigo_catastral`,`matricula_inmobiliaria`,
            `direccion`,`codigo_sig`,
            `ciudad`,`barrio`,`ruta`,
            `tipo_predio`,`nombre`,`aniocreacion`,
            `codigoigac`,`estado`,`estrato`,`predioaso`) 
            VALUES(:codigo_catastral,:matricula_inmobiliaria,
            :direccion,:codigo_sig,
            :ciudad,:barrio,:ruta,
            :tipo_predio,:nombre,:aniocreacion,
            :codigoigac,:estado,:estrato,:predioaso)";
        $sql_dato = array(
                array(":codigo_catastral",$codigo_catastral),
                array(":matricula_inmobiliaria",$matricula_in),
                array(":direccion",$direccion),
                array(":codigo_sig",$codigo),
                array(":ciudad",$Ciudad),
                array(":barrio",$barrio),
                array(":ruta",$ruta),
                array(":tipo_predio",$tipoPredio),
                array(":nombre",$nombre),
                array(":aniocreacion",$annio),
                array(":codigoigac",$codigoIG),
                array(":estado",$estado),
                array(":estrato",$estrato),
                array(":predioaso",$predioa),
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
        $codigo_catastral   = $_REQUEST['codigo_catastral'];
        $nombre             = $_REQUEST['nombre'];
        $matricula_in       = $_REQUEST['matricula_inmobiliaria'];
        $annio              = $_REQUEST['annio'];
        $codigo             = $_REQUEST['codigo'];
        $codigoIG           = $_REQUEST['codigoIG'];
        $direccion          = $_REQUEST['direccion'];
        $Ciudad             = $_REQUEST['Ciudad'];
        if(empty($_REQUEST['barrio'])){
            $barrio         = $_REQUEST['barrio'];
        } else {
            $barrio         = NULL;
        }
        if(empty($_REQUEST['estrato'])){
            $estrato        = NULL;
        } else {
            $estrato        = $_REQUEST['estrato'];
        }
        if(empty($_REQUEST['estado'])){
            $estado         = NULL;
        } else {
            $estado         = $_REQUEST['estado'];
        }
        
        if(empty($_REQUEST['ruta'])){
            $ruta           = NULL;
        } else { 
            $ruta           = $_REQUEST['ruta'];
        }
        if(empty($_REQUEST['tipoPredio'])){
            $tipoPredio     = NULL;
        } else { 
            $tipoPredio     = $_REQUEST['tipoPredio'];
        }
        if(empty($_REQUEST['predioa'])){
            $predioa        = NULL;
        } else {
            $predioa        = $_REQUEST['predioa'];
        }
        $id                 = $_REQUEST['id'];
        if(empty($_REQUEST['barrio'])){
            $barrio = NULL;
        }
        $sql_cons = "UPDATE `gp_predio1`  
            SET `codigo_catastral`=:codigo_catastral,
            `matricula_inmobiliaria`=:matricula_inmobiliaria,
            `direccion`=:direccion,
            `codigo_sig`=:codigo_sig,
            `ciudad`=:ciudad,
            `barrio`=:barrio,
            `ruta`=:ruta,
            `tipo_predio`=:tipo_predio,
            `nombre`=:nombre,
            `aniocreacion`=:aniocreacion,
            `codigoigac`=:codigoigac,
            `estado`=:estado,
            `estrato`=:estrato,
            `predioaso`=:predioaso
            WHERE `id_unico`=:id_unico";
        $sql_dato = array(
                array(":codigo_catastral",$codigo_catastral),
                array(":matricula_inmobiliaria",$matricula_in),
                array(":direccion",$direccion),
                array(":codigo_sig",$codigo),
                array(":ciudad",$Ciudad),
                array(":barrio",$barrio),
                array(":ruta",$ruta),
                array(":tipo_predio",$tipoPredio),
                array(":nombre",$nombre),
                array(":aniocreacion",$annio),
                array(":codigoigac",$codigoIG),
                array(":estado",$estado),
                array(":estrato",$estrato),
                array(":predioaso",$predioa),
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
}

