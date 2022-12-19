<?php
require '../Conexion/ConexionPDO.php';
require '../Conexion/conexion.php';
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
$action     = $_REQUEST['action'];
switch ($action) {
    #Guardar Tipo D
    case 1:
        $nombre         = $_REQUEST['nombre'];
        $tipo           = $_REQUEST['tipo'];
        $clase_novedad  = $_REQUEST['clase'];
        $concepto       = $_REQUEST['concepto'];
        
        $sql_cons ="INSERT INTO `gn_tipo_novedad` 
            ( `nombre`, `tipo`, `clase_novedad`, `concepto`) 
        VALUES (:nombre, :tipo ,:clase_novedad,:concepto)";
        $sql_dato = array(
            array(":nombre",$nombre),
            array(":tipo",$tipo),
            array(":clase_novedad",$clase_novedad),
            array(":concepto",$concepto),
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
        $nombre         = $_REQUEST['nombre'];
        $tipo           = $_REQUEST['tipo'];
        $clase_novedad  = $_REQUEST['clase'];
        $concepto       = $_REQUEST['concepto'];
        
        
        $sql_cons ="UPDATE `gn_tipo_novedad` 
                SET  `nombre`=:nombre, `tipo`=:tipo ,
                `clase_novedad`=:clase_novedad, 
                `concepto`=:concepto 
                WHERE `id_unico`=:id_unico";
        $sql_dato = array(
            array(":nombre",$nombre),
            array(":tipo",$tipo),
            array(":clase_novedad",$clase_novedad),
            array(":concepto",$concepto),
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
        $sql_cons ="DELETE FROM `gn_tipo_novedad` 
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
    
    #Guardar Conceptos del tipo
    case 4:
        $idtn   = $_REQUEST['idt'];
               
        $dias     = $_REQUEST['dias'];
        $valor    = $_REQUEST['valor'];
        if(empty($_REQUEST['diasI'])){ $diasI     = NULL;} else {$diasI     = $_REQUEST['diasI'];}
        if(empty($_REQUEST['porcentaje'])){$porcentaje     = NULL; } else { $porcentaje     = $_REQUEST['porcentaje'];}
        
        if(empty($_REQUEST['ibc'])){ $ibc     = NULL;} else {$ibc     = $_REQUEST['ibc'];}
        if(empty($_REQUEST['app'])){ $app     = NULL;} else {$app     = $_REQUEST['app'];}
        if(empty($_REQUEST['appe'])){ $appe   = NULL;} else {$appe    = $_REQUEST['appe'];}
        if(empty($_REQUEST['asp'])){ $asp     = NULL;} else {$asp     = $_REQUEST['asp'];}
        if(empty($_REQUEST['ase'])){ $ase     = NULL;} else {$ase     = $_REQUEST['ase'];}
        if(empty($_REQUEST['cc'])){   $cc     = NULL;} else {$cc      = $_REQUEST['cc'];}
        if(empty($_REQUEST['sena'])){ $sena   = NULL;} else {$sena    = $_REQUEST['sena'];}
        if(empty($_REQUEST['icbf'])){ $icbf   = NULL;} else {$icbf    = $_REQUEST['icbf'];}
        if(empty($_REQUEST['esap'])){ $esap   = NULL;} else {$esap    = $_REQUEST['esap'];}
        if(empty($_REQUEST['me'])){ $me       = NULL;} else {$me      = $_REQUEST['me'];}
        if(empty($_REQUEST['it'])){ $it       = NULL;} else {$it      = $_REQUEST['it'];}
        if(empty($_REQUEST['fsp'])){ $fsp     = NULL;} else {$fsp     = $_REQUEST['fsp'];}
        if(empty($_REQUEST['arl'])){ $arl     = NULL;} else {$arl     = $_REQUEST['arl'];}

        $sql_cons ="INSERT INTO `gn_concepto_incapacidad` 
            ( `tipo_incapacidad`, `dias_incapacidad`, `porcentaje`, `dias`, 
            `valor`, `ibc`, `aporte_pension_patrono`, `aporte_pension_empleado`, 
            `aporte_salud_patrono`, `aporte_salud_empleado`, `caja_compensacion`, `sena`, 
            `icbf`, `esap`, `ministerio_educacion`, `institutos_tecnicos`, 
            `fondo_solidaridad`, `arl`) 
        VALUES (:tipo_incapacidad, :dias_incapacidad, :porcentaje, :dias, 
            :valor, :ibc, :aporte_pension_patrono, :aporte_pension_empleado, 
            :aporte_salud_patrono, :aporte_salud_empleado, :caja_compensacion, :sena, 
            :icbf, :esap, :ministerio_educacion, :institutos_tecnicos, 
            :fondo_solidaridad, :arl)";
        $sql_dato = array(
            array(":tipo_incapacidad",$idtn),
            array(":dias_incapacidad",$diasI),
            array(":porcentaje",$porcentaje),
            array(":dias",$dias),
            array(":valor",$valor),
            array(":ibc",$ibc),
            array(":aporte_pension_patrono",$app),
            array(":aporte_pension_empleado",$appe),
            array(":aporte_salud_patrono",$asp),
            array(":aporte_salud_empleado",$ase),
            array(":caja_compensacion",$cc),
            array(":sena",$sena),
            array(":icbf",$icbf),
            array(":esap",$esap),
            array(":ministerio_educacion",$me),
            array(":institutos_tecnicos",$it),
            array(":fondo_solidaridad",$fsp),
            array(":arl",$arl),
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

    #* Eliminar  COnceptos Tipo
    case 5:
        $id     = $_POST['id'];
        $sql_cons ="DELETE FROM `gn_concepto_incapacidad` 
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
