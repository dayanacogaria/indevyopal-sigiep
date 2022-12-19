<?php
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
require_once './funcionesPptal.php';
session_start();
$con       = new ConexionPDO();
$anno      = $_SESSION['anno'];
switch ($_REQUEST['action']){
    #*** Guardar Otros Conceptos******#
    case(1):
        $sql_cons ="INSERT INTO `gr_otros_descuentos` 
                ( `fecha_inicial`,`fecha_final`, 
                `concepto`, `porcentaje`, 
                `vigencia_inicial`, `vigencia_final`) 
        VALUES (:fecha_inicial,:fecha_final, 
        :concepto, :porcentaje, 
        :vigencia_inicial, :vigencia_final)";
        $sql_dato = array(
            array(":fecha_inicial",fechaC($_REQUEST['fechaI'])),
            array(":fecha_final",fechaC($_REQUEST['fechaF'])),
            array(":concepto",$_REQUEST['concepto']),
            array(":porcentaje",$_REQUEST['porcentaje']), 
            array(":vigencia_inicial",$_REQUEST['vigenciaI']), 
            array(":vigencia_final",$_REQUEST['vigenciaF'])
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            echo 1;
        } else {
            echo 0;
        }
        
    break;
    #*** Modificar Otros Conceptos******#
    case(2):
        $sql_cons ="UPDATE `gr_otros_descuentos` 
            SET `fecha_inicial`=:fecha_inicial, 
            `fecha_final`=:fecha_final, 
            `concepto`=:concepto, 
            `porcentaje`=:porcentaje, 
            `vigencia_inicial`=:vigencia_inicial, 
            `vigencia_final`=:vigencia_final 
            WHERE `id_unico`=:id_unico";
        $sql_dato = array(
            array(":fecha_inicial",fechaC($_REQUEST['fechaI'])),
            array(":fecha_final",fechaC($_REQUEST['fechaF'])),
            array(":concepto",$_REQUEST['concepto']),
            array(":porcentaje",$_REQUEST['porcentaje']), 
            array(":vigencia_inicial",$_REQUEST['vigenciaI']), 
            array(":vigencia_final",$_REQUEST['vigenciaF']),
            array(":id_unico",$_REQUEST['id'])
                
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            echo 1;
        } else {
            echo 0;
        }
    break;
    #*** Eliminar Otros Conceptos******#
    case(3):
        $sql_cons ="DELETE FROM `gr_otros_descuentos` WHERE `id_unico`=:id_unico ";
        $sql_dato = array(
            array(":id_unico",$_REQUEST['id']),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            echo 1;
        } else {
            echo 0;
        }
    break; 
}