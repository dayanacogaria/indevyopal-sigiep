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
            $sector         = $_REQUEST['sectorG'];
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
        }
        else{
            $rta = false;            
        }
        echo $rta;
    break;
    
    #**** Cargar Unidades de Vivienda Por Sector ****#
    case 5:
        $row = $con->Listar("SELECT uvms.id_unico, 
            p.codigo_catastral, 
            IF(CONCAT_WS(' ',
                 t.nombreuno,
                 t.nombredos,
                 t.apellidouno,
                 t.apellidodos) 
                 IS NULL OR CONCAT_WS(' ',
                 t.nombreuno,
                 t.nombredos,
                 t.apellidouno,
                 t.apellidodos) = '',
                 (t.razonsocial),
                 CONCAT_WS(' ',
                 t.nombreuno,
                 t.nombredos,
                 t.apellidouno,
                 t.apellidodos)) AS NOMBRE, 
            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                 t.numeroidentificacion, 
            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
            p.direccion 
            FROM gp_unidad_vivienda_medidor_servicio uvms 
            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
            LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
            LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
            WHERE p.estado = 4 AND s.id_unico = ".$_REQUEST['s']." 
            ORDER BY cast(p.codigo_catastral  as unsigned) ASC");
        if(count($row)>0){
            for ($i = 0; $i < count($row); $i++) {
                echo '<option value="'.$row[$i][0].'">'.$row[$i][1].' - '. ucwords(mb_strtolower($row[$i][2])).'</option>';
            }
        } else {
            echo '<option value="">No Hay Viviendas</option>';
        }
    break;
    
    #Casa vacía
    case 6:
        $id     = $_REQUEST['uv'];
        $estado = $_REQUEST['estado'];
        $sql_cons ="UPDATE `gp_unidad_vivienda`  
            SET `deshabilitado`=:deshabilitado 
            WHERE `id_unico`=:id_unico";
       $sql_dato = array(
               array(":deshabilitado",$estado),
               array(":id_unico",$id),
       );
       $resp       = $con->InAcEl($sql_cons,$sql_dato);
       if(empty($resp)){
           echo 0;
        } else {
           echo 1;
        }
    break;
}

