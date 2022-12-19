<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#07/07/2018 |Erica G. | Actualizacion Código
#11/04/2018 |Erica G. | Archivo Creado 
#######################################################################################################

require '../ExcelR/Classes/PHPExcel/IOFactory.php';                                     
require '../Conexion/ConexionPDO.php';                                                     
ini_set('max_execution_time', 0);
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$fechaE     = date('Y-m-d'); 
$anno       = $_SESSION['anno'];
$action     = $_REQUEST['action'];

switch ($action){
    #   **************** Subir Comparendos ****************    #
    case 1:
        $sucursal       = $_REQUEST['sucursal'];
        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName); 			
        $worksheetList  = $objReader->listWorksheetNames($inputFileName);
        $registros      = 0;
        for ($i=0; $i< count($worksheetList); $i++){
            $objWorksheet   = $objPHPExcel->setActiveSheetIndex($i);				
            $total_filas    = $objWorksheet->getHighestRow();					
            $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
            $nfor = 100;
            $n_filas =$total_filas; 
            $a =0;
            for ($z = 0; $z < ($nfor); $z++) {
                if($n_filas > 0){
                    if($z==($nfor-1)){
                        $z =0; 
                        if($n_filas<100){
                           $nfor =($n_filas+1);
                        }
                   }
                }
                $n_filas -=1;
                $num  = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
                $num  =  (int)$num;
                if($num != 0) { 
                    $compar     = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
                    $comparendo = str_replace(" ", "", $compar);
                    $fechac     = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();
                    $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fechac);
                    $fechac     = date("Y-m-d",$timestamp);
                    $placa      = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();
                    $comp       = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
                    $infraccion = $objWorksheet->getCellByColumnAndRow(5, $a)->getCalculatedValue();
                    $cedula     = $objWorksheet->getCellByColumnAndRow(6, $a)->getCalculatedValue();
                    $nombre     = $objWorksheet->getCellByColumnAndRow(7, $a)->getCalculatedValue();
                    $apellido   = $objWorksheet->getCellByColumnAndRow(8, $a)->getCalculatedValue();
                    $direccion  = $objWorksheet->getCellByColumnAndRow(9, $a)->getCalculatedValue();
                    $ciudad     = $objWorksheet->getCellByColumnAndRow(10, $a)->getCalculatedValue();
                    $fechan     = $objWorksheet->getCellByColumnAndRow(11, $a)->getCalculatedValue();
                    $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fechan);
                    $fechan     = date("Y-m-d",$timestamp);
                    $entrega    = $objWorksheet->getCellByColumnAndRow(13, $a)->getCalculatedValue();
                    $devoluc    = $objWorksheet->getCellByColumnAndRow(15, $a)->getCalculatedValue();
                    
                    #****Guardar Datos***#
                    $sql_cons ="INSERT INTO `gu_comparendo` 
                            ( `numero`,             `fecha_comparendo`, `placa`,
                            `comparendos_nfisicos`, `infraccion`,       `cedula`,
                            `nombres`,              `apellidos`,        `direccion`, 
                            `fecha_notificacion`,   `entrega`,          `devoluciones`,
                            `sucursal`,             `comparendo`,       `ciudad`) 
                    VALUES (  :numero,              :fecha_comparendo,  :placa, 
                            :comparendos_nfisicos,  :infraccion,        :cedula,
                            :nombres,               :apellidos,         :direccion,
                            :fecha_notificacion,    :entrega,           :devoluciones, 
                            :sucursal,              :comparendo,        :ciudad)";
                    $sql_dato = array(
                            array(":numero",$num),
                            array(":fecha_comparendo",$fechac),
                            array(":placa",$placa),
                            array(":comparendos_nfisicos",$comp),
                            array(":infraccion",$infraccion),
                            array(":cedula",$cedula),   
                            array(":nombres",$nombre),
                            array(":apellidos",$apellido),
                            array(":direccion",$direccion),
                            array(":fecha_notificacion",$fechan),
                            array(":entrega",$entrega),
                            array(":devoluciones",$devoluc),
                            array(":sucursal",$sucursal),
                            array(":comparendo",$comparendo),
                            array(":ciudad",$ciudad),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    if(empty($resp)){
                        $registros      += 1;
                    } else {
                        #var_dump($resp);
                    }
                    
                }
                $a++;
            }
        }
        echo $registros;
    break;
    # ********* Subir Recaudos *************#
    case 2:
        $registros      =0;
        $sucursal       = $_REQUEST['sucursal'];
        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName); 			
        $objWorksheet   = $objPHPExcel->setActiveSheetIndexByName('Recaudo');	
        $total_filas    = $objWorksheet->getHighestRow();					
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        $registros      = 0;
        $nfor = 100;
        $n_filas =$total_filas; 
        $a =0;
        for ($z = 0; $z < ($nfor); $z++) {
            if($n_filas > 0){
                if($z==($nfor-1)){
                    $z =0; 
                    if($n_filas<100){
                       $nfor =($n_filas+1);
                    }
               }
            }
            $n_filas -=1;
            $num  = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $num  =  (int)$num;
            if($num != 0) { 
                $fechac     = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
                $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fechac);
                $fechac     = date("Y-m-d",$timestamp);
                $fechap     = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();
                $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fechap);
                $fechap     = date("Y-m-d",$timestamp);
                $compa      = $objWorksheet->getCellByColumnAndRow(5, $a)->getCalculatedValue();
                $compar     = str_replace("'", "", $compa);
                $comparendo = str_replace(" ", "", $compar);
                $cedula     = $objWorksheet->getCellByColumnAndRow(6, $a)->getCalculatedValue();
                $valor_r    = $objWorksheet->getCellByColumnAndRow(11, $a)->getCalculatedValue();
                $valor_t    = $objWorksheet->getCellByColumnAndRow(12, $a)->getCalculatedValue();
                $medio      = $objWorksheet->getCellByColumnAndRow(15, $a)->getCalculatedValue();

                $cuent      = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
                $cuenta     = str_replace("'", "", $cuent);
                $fechad     = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();
                $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fechad);
                $fechad     = date("Y-m-d",$timestamp); 
                $numero_l   = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
                $divipo     = $objWorksheet->getCellByColumnAndRow(7, $a)->getCalculatedValue();
                $municipio  = $objWorksheet->getCellByColumnAndRow(8, $a)->getCalculatedValue();
                $departamen = $objWorksheet->getCellByColumnAndRow(9, $a)->getCalculatedValue();
                $tipo_rec   = $objWorksheet->getCellByColumnAndRow(10, $a)->getCalculatedValue();
                $fecha_com  = $objWorksheet->getCellByColumnAndRow(13, $a)->getCalculatedValue();
                $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fecha_com);
                $fecha_com     = date("Y-m-d",$timestamp); 
                $fecha_dis  = $objWorksheet->getCellByColumnAndRow(14, $a)->getCalculatedValue();
                $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fecha_dis);
                $fecha_dis     = date("Y-m-d",$timestamp); 

                #****Guardar Datos***#
                $sql_cons ="INSERT INTO `gu_recaudos` 
                        ( `fecha_recaudo`,      `fecha_proceso`,    `comparendo`,
                        `identificacion`,       `valor_recaudo`,    `valor_tercero`,
                        `medio_imposicion`,     `sucursal`,         `cuenta_recaudo`,
                        `fecha_dispersion`,     `numero_liquidacion`,`divipo`,
                        `municipio`,            `departamento`,     `tipo_recaudo`,
                        `fecha_comp`,           `fecha_diso`) 
                VALUES (  :fecha_recaudo,       :fecha_proceso,     :comparendo, 
                        :identificacion,        :valor_recaudo,     :valor_tercero,
                        :medio_imposicion,      :sucursal,          :cuenta_recaudo,
                        :fecha_dispersion,     :numero_liquidacion,:divipo,
                        :municipio,            :departamento,     :tipo_recaudo,
                        :fecha_comp,           :fecha_diso)";
                $sql_dato = array(
                        array(":fecha_recaudo",$fechac),
                        array(":fecha_proceso",$fechap),
                        array(":comparendo",$comparendo),
                        array(":identificacion",$cedula),
                        array(":valor_recaudo",$valor_r),
                        array(":valor_tercero",$valor_t),   
                        array(":medio_imposicion",$medio),
                        array(":sucursal",$sucursal), 
                        array(":cuenta_recaudo",$cuenta), 
                        array(":fecha_dispersion",$fechad), 
                        array(":numero_liquidacion",$numero_l), 
                        array(":divipo",$divipo), 
                        array(":municipio",$municipio), 
                        array(":departamento",$departamen), 
                        array(":tipo_recaudo",$tipo_rec), 
                        array(":fecha_comp",$fecha_com), 
                        array(":fecha_diso",$fecha_dis), 
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                if(empty($resp)){
                    $registros      += 1;
                } else {
                    #var_dump($resp);
                }
            }
            $a++;
        }
        $datos = array("registros"=>$registros);
        echo $registros;
    break;
    #*** Eliminar comparendo **#
    case 3:
        $id = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gu_comparendo` 
                WHERE `id_unico`=:id_unico ";
        $sql_dato = array(
                array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $rta =1;
        } else {
            $rta =2;
        }
        echo $rta ;
    break;
    #*** Eliminar Recaudo **#
    case 4:
        $id = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gu_recaudos`  
                WHERE `id_unico`=:id_unico ";
        $sql_dato = array(
                array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $rta =1;
        } else {
            $rta =2;
        }
        echo $rta ;
    break;
}
 