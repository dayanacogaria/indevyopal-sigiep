<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#28/08/2018 |Nestor B. | Archivo Creado 
#######################################################################################################

require '../ExcelR/Classes/PHPExcel/IOFactory.php';                                     
require '../Conexion/conexion.php';                                                     
ini_set('max_execution_time', 0);
@session_start();
#$con        = new ConexionPDO();

$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$fechaE     = date('Y-m-d'); 
$anno       = $_SESSION['anno'];
#$action     = $_REQUEST['action'];
$action = 1;
#$sucursal   = $_POST['sucursal'];
#
$nombreI = "Resoluciones_No_Registradas";                 //Nombre del informe
    $consulta = "";                                 //Variable con la consulta a realizar
    $num_filas = 0;                             //Número de filas
    $num_cols = 0;                              //Número de columnas
    $errores = "";                              //Variable de captura de errores
    $info_campo = "";                                   //variable para obtener los nombres de los campos
    $cols_nom = array();                                                        //Array para capturar los nombres de las columnas
    $nom_cols = "";                             //String de captura de los mombres de las columnas de manera lineal
    $csv = "";                                  //Variable para generar csv
    $shtml = "";                                //Variable de armado de html
    $separador = ",";                               //Variable para recibir el separador
    $lineas = "";                               //Variable para obtener las lineas del archivo txt
    $txtName = $nombreI.".txt";
    $sfile = '../documentos/generador_informes/txt/'.$txtName;
    $sarch = 'documentos/generador_informes/txt/'.$txtName;
    $espacio = '            ';
switch ($action){
    #   **************** Subir Comparendos ****************    #
    case 1:
        $sucursal       = $_REQUEST['sucursal'];
        #$sucursal = 1;
        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();                  
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName);             
        $objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);             
        $total_filas    = $objWorksheet->getHighestRow();                   
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        $registros      = 0;
        $cant           = 0; 
        for ($a=2; $a<= $total_filas; $a++){
            
                $numIdent   = $objWorksheet->getCellByColumnAndRow(6, $a)->getCalculatedValue();
                $plac       = $objWorksheet->getCellByColumnAndRow(11, $a)->getCalculatedValue();
                $placa      = str_replace(" ", "", $plac);
                $compar     = $objWorksheet->getCellByColumnAndRow(7, $a)->getCalculatedValue();
                $comparendo = str_replace(" ", "", $compar);
                $fechac     = $objWorksheet->getCellByColumnAndRow(8, $a)->getCalculatedValue();
                $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fechac);
                $fechac     = date("Y-m-d",$timestamp);
                $tipoDoc    = $objWorksheet->getCellByColumnAndRow(5, $a)->getCalculatedValue();
                $resol      = $objWorksheet->getCellByColumnAndRow(9, $a)->getCalculatedValue();
                $fechar     = $objWorksheet->getCellByColumnAndRow(10, $a)->getCalculatedValue();
                $timestamp1 = PHPExcel_Shared_Date::ExcelToPHP($fechar);
                $fechar     = date("Y-m-d",$timestamp1);
                #$num  =  (int)$num;
                if(!empty($numIdent) && !empty($placa)) { 

                    $nomUno     = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
                    $nomDos     = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();
                    $apeUno     = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();
                    $apeDos     = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
                    $cod_comp   = $objWorksheet->getCellByColumnAndRow(12, $a)->getCalculatedValue();
                    $valor      = $objWorksheet->getCellByColumnAndRow(14, $a)->getCalculatedValue();
                    $placa_agen = $objWorksheet->getCellByColumnAndRow(17, $a)->getCalculatedValue();
                    $cant_sal_M = $objWorksheet->getCellByColumnAndRow(22, $a)->getCalculatedValue();

                    
                    $nombres    = $nomUno.' '.$nomDos;
                    $apellidos  = $apeUno.' '.$apeDos;

                    if($tipoDoc == 1){
                        $TD = "Cedula";
                    }elseif($tipoDoc == 2){
                        $TD = "Tarjeta de Identidad";
                    }elseif($tipoDoc == 3){
                        $TD = "Cédula de Extranjería";
                    }elseif($tipoDoc == 4){
                        $TD = "NIT";
                    }elseif($tipoDoc == 5){
                        $TD = "NIT";
                    }elseif($tipoDoc == 6){
                        $TD = "NIT";
                    }elseif($tipoDoc == 7){
                        $TD = "NIT";
                    }elseif($tipoDoc == 8){
                        $TD = "NIT";
                    }elseif($tipoDoc == 9){
                        $TD = "Cédula Venezolana";
                    }

                    #****Guardar Datos***#
                    $sqlInsert = "INSERT INTO gu_resoluciones(resolucion,fecha_res,comparendo,fecha_comparendo,tipo_documento,cedula,nombres,apellidos,placa, infraccion,valor,placa_agente,cant_salario_min,sucursal)VALUES('$resol','$fechar','$comparendo','$fechac','$TD','$numIdent','$nombres','$apellidos','$placa','$cod_comp',$valor,'$placa_agen',$cant_sal_M,$sucursal)";
                    $resp =$mysqli->query($sqlInsert);
                   
                    if($resp == true){
                        $registros      += 1;
                    } else {
                        $cant ++;
                        $lineas .= $cant.$espacio."FILA: ".$a.$espacio."Esta duplicado el resgistro(la misma identificacion, placa y comparendo) ".$espacio.$nombres."\r\n";
                        #var_dump($resp);
                    }
                    
                }else{
                    $cant ++;

                    $nomUno     = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
                    $nomDos     = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();
                    $apeUno     = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();
                    $apeDos     = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
                     $nombres    = $nomUno.' '.$nomDos;
                    $lineas .= $cant.$espacio."FILA: ".$a.$espacio."Falta la placa o la identificacion(alguno de los campos esta vacio)      ".$espacio.$nombres."\r\n";
                }
                #$a++;
            
        }
        $fp=fopen($sfile,"w" );                                                     //Abrimos el archivo en modo de escritura
        fwrite($fp,$lineas);                                                        //Escribimos el html del archivo
        fclose($fp);
        $ruta = '<a href="'.$sarch.'">Resoluciones No Registradas<a/>'; 
        $arreglo = array();                                                             //Cerramos el archivo
        $arreglo =  array('registro' =>$registros,'archivo'=>$ruta);

        echo json_encode($arreglo);
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
 