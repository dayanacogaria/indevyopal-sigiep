<?php

require '../ExcelR/Classes/PHPExcel/IOFactory.php';                                     
require '../Conexion/ConexionPDO.php';     
require '../Conexion/conexion.php';     
require './../jsonPptal/funcionesPptal.php'; 
ini_set('max_execution_time', 0);
ini_set('memory_limit','160000M');
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$fechaE     = date('Y-m-d'); 
$anno       = $_SESSION['anno'];
$action     = $_REQUEST['action'];
$ciudad     = 798;
$cc         = $con->Listar("SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno");
$centroc    = $cc[0][0];
switch ($action){
    #   **************** Subir  ****************    #
    case 1:
        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName); 			
        $worksheetList  = $objReader->listWorksheetNames($inputFileName);
        $registros      = 0;
        $s              = 0;
        $t              = 0;
        $htmls          = "Sectores No Encontrados:".'<br/>';
        $htmlt          = "Terceros No Encontrados:".'<br/>';
        #*** Validar Sectores ***#
        $total_r =0;
        for($h=0;$h<count($worksheetList);$h++){
            $sc = $con->Listar("SELECT * FROM gp_sector WHERE nombre ='".$worksheetList[$h]."'");
            if(count($sc)<=0){
                $htmls .=$worksheetList[$h].'<br/>';
                $s+=1;
            }
        }
        #*** Si Encontró los sectores, valida terceros ***#
        if($s==0){
            for($h=0;$h<count($worksheetList);$h++){
                $objWorksheet   = $objPHPExcel->setActiveSheetIndex($h);	
                $total_filas    = $objWorksheet->getHighestRow();					
                $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
                for ($f = 0; $f <= $total_filas; $f++) {
                    $linea1 = $objWorksheet->getCellByColumnAndRow(0, $f)->getCalculatedValue();
                    if(!empty($linea1) || $linea1 !=""){
                        #** Buscar Código Sector **#
                        $sc             = $con->Listar("SELECT * FROM gp_sector WHERE nombre ='".$worksheetList[$h]."'");
                        $codigo_s       = $sc[0]['codigo'];
                        $item           = $objWorksheet->getCellByColumnAndRow(1, $f)->getCalculatedValue();
                        $tercero        = $objWorksheet->getCellByColumnAndRow(2, $f)->getCalculatedValue();
                        $codigo         = $codigo_s.$item;
                        $bt = $con->Listar("SELECT * FROM gf_tercero WHERE FIND_IN_SET('$codigo', nombre_comercial )");
                        if(count($bt)<=0){
                            $htmlt .=$tercero.'<br/>';
                            $t+=1;
                        }
                    }
                }
            }
        }
        #** Si Encontró terceros y valores buscar predios y crearlos los que encuentre 
        if($t==0){
            for($h=0;$h<count($worksheetList);$h++){
                #** Buscar Código Sector **#
                $sc             = $con->Listar("SELECT * FROM gp_sector WHERE nombre ='".$worksheetList[$h]."'");
                $id_sector      = $sc[0]['id_unico'];
                $codigo_s       = $sc[0]['codigo'];
                #***************************#
                $objWorksheet   = $objPHPExcel->setActiveSheetIndex($h);	
                $total_filas    = $objWorksheet->getHighestRow();					
                $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
                for ($f = 0; $f <= $total_filas; $f++) {
                    $linea1 = $objWorksheet->getCellByColumnAndRow(0, $f)->getCalculatedValue();
                    $direccion   = $objWorksheet->getCellByColumnAndRow(41, $f)->getCalculatedValue();
                    $estrato_a   = $objWorksheet->getCellByColumnAndRow(5, $f)->getCalculatedValue();
                    $be          = $con->Listar("SELECT * FROM gp_estrato WHERE codigo LIKE '%$estrato_a' AND tipo_estrato = 2");
                    $estrato     = $be[0][0];
                    if(!empty($linea1) || $linea1 !=""){
                        #*********************** Buscar/Crear Predio *********************************#
                        $item   = $objWorksheet->getCellByColumnAndRow(1, $f)->getCalculatedValue();
                        $codigo = $codigo_s.$item;
                        $pr = $con->Listar("SELECT * FROM gp_predio1 WHERE codigo_catastral ='$codigo'");
                        if(count($pr)>0){
                            $predio = $pr[0][0];
                        } else {
                            
                            $sql_cons ="INSERT INTO `gp_predio1` 
                                    ( `codigo_catastral`, `nombre`, `direccion`,`ciudad`,`estrato` ) 
                            VALUES  (:codigo_catastral,  :nombre, :direccion, :ciudad,:estrato)";
                            $sql_dato = array(
                                    array(":codigo_catastral",$codigo),
                                    array(":nombre",$codigo),
                                    array(":direccion",$direccion),
                                    array(":ciudad",$ciudad),
                                    array(":estrato",$estrato),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            var_dump($resp);
                            $pr = $con->Listar("SELECT * FROM gp_predio1 WHERE codigo_catastral ='$codigo'");
                            $predio = $pr[0][0];
                        }
                        #**********************************************************************#
                        #******************** Buscar/Crear Unidad Vivienda ********************#
                        $tr = $con->Listar("SELECT * FROM gf_tercero WHERE FIND_IN_SET('$codigo', nombre_comercial )");
                        $tercero = $tr[0][0];                        
                        $uso_n = $objWorksheet->getCellByColumnAndRow(66, $f)->getCalculatedValue();
                        $bu = $con->Listar("SELECT * FROM gp_uso WHERE nombre LIKE '%$uso_n%'");
                        if(count($bu)>0){
                            $uso = $bu[0][0];
                        } else {
                            $uso = 1;
                        }
                        
                        #*** Buscar Si Existe una unidad de vivienda , predio,  tercero ***#
                        $buv = $con->Listar("SELECT * FROM gp_unidad_vivienda 
                            WHERE tercero = $tercero AND predio = $predio");
                        if(count($buv)>0){
                            $unidad_vivienda = $buv[0][0];
                        } else {
                            #Crear Unidad Vivienda
                            $sql_cons ="INSERT INTO `gp_unidad_vivienda` 
                                    ( `predio`, `tercero`, `estrato`,`sector`, `uso` ) 
                            VALUES  (:predio,  :tercero, :estrato, :sector, :uso)";
                            $sql_dato = array(
                                    array(":predio",$predio),
                                    array(":tercero",$tercero),
                                    array(":estrato",$estrato),
                                    array(":sector",$id_sector),
                                    array(":uso",$uso),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            $buv = $con->Listar("SELECT * FROM gp_unidad_vivienda WHERE tercero = $tercero AND predio = $predio");
                            $unidad_vivienda = $buv[0][0];
                            
                        }
                        #**********************************************************************#
                        #*************** Buscar/Crear Unidad Vivienda Servicio ****************#
                        $acueducto          = $objWorksheet->getCellByColumnAndRow(69, $f)->getCalculatedValue();
                        $alcantarillado     = $objWorksheet->getCellByColumnAndRow(70, $f)->getCalculatedValue();
                        $aseo               = $objWorksheet->getCellByColumnAndRow(71, $f)->getCalculatedValue();
                        $acueducto          = trim($acueducto);
                        $alcantarillado     = trim($alcantarillado);
                        $aseo               = trim($aseo);
                        #*** Buscar Si Existe una unidad de vivienda , predio,  tercero ***#
                        $buvs = $con->Listar("SELECT * FROM gp_unidad_vivienda_servicio WHERE unidad_vivienda = $unidad_vivienda AND tipo_servicio = 1");
                        
                        if(count($buvs)>0){
                            $unidad_vivienda_servicio = $buvs[0][0];
                            #Actualizar Unidad Vivienda Servicio                                
                            $sql_cons ="UPDATE `gp_unidad_vivienda_servicio` 
                                    `estado_servicio`=:estado_servicio 
                                    WHERE `id_unico`=:id_unico";
                            $sql_dato = array(
                                    array(":estado_servicio",1),
                                    array(":id_unico",$unidad_vivienda_servicio),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);

                        } else {
                            #Crear Unidad Vivienda
                            $sql_cons ="INSERT INTO `gp_unidad_vivienda_servicio` 
                                    ( `unidad_vivienda`, `tipo_servicio`, `estado_servicio` ) 
                            VALUES  (:unidad_vivienda,  :tipo_servicio, :estado_servicio)";
                            $sql_dato = array(
                                    array(":unidad_vivienda",$unidad_vivienda),
                                    array(":tipo_servicio",1),
                                    array(":estado_servicio",1),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            $buvs = $con->Listar("SELECT * FROM gp_unidad_vivienda_servicio WHERE unidad_vivienda = $unidad_vivienda AND tipo_servicio = 1");
                            $unidad_vivienda_servicio = $buvs[0][0];

                        }
                        if($alcantarillado=='SI'){
                            #*** Buscar Si Existe una unidad de vivienda , predio,  tercero ***#
                            $buvs = $con->Listar("SELECT * FROM gp_unidad_vivienda_servicio WHERE unidad_vivienda = $unidad_vivienda AND tipo_servicio = 2");
                            if(count($buvs)>0){
                                #Actualizar Unidad Vivienda Servicio                                
                                $sql_cons ="UPDATE `gp_unidad_vivienda_servicio` 
                                        `estado_servicio`=:estado_servicio 
                                        WHERE `id_unico`=:id_unico";
                                $sql_dato = array(
                                        array(":estado_servicio",1),
                                        array(":id_unico",$buvs[0][0]),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                                
                            } else {
                                #Crear Unidad Vivienda
                                $sql_cons ="INSERT INTO `gp_unidad_vivienda_servicio` 
                                        ( `unidad_vivienda`, `tipo_servicio`, `estado_servicio` ) 
                                VALUES  (:unidad_vivienda,  :tipo_servicio, :estado_servicio)";
                                $sql_dato = array(
                                        array(":unidad_vivienda",$unidad_vivienda),
                                        array(":tipo_servicio",2),
                                        array(":estado_servicio",1),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);

                            }
                        }
                        if($aseo=='SI'){
                            #*** Buscar Si Existe una unidad de vivienda , predio,  tercero ***#
                            $buvs = $con->Listar("SELECT * FROM gp_unidad_vivienda_servicio WHERE unidad_vivienda = $unidad_vivienda AND tipo_servicio = 3");
                            if(count($buvs)>0){
                                #Actualizar Unidad Vivienda Servicio                                
                                $sql_cons ="UPDATE `gp_unidad_vivienda_servicio` 
                                        `estado_servicio`=:estado_servicio 
                                        WHERE `id_unico`=:id_unico";
                                $sql_dato = array(
                                        array(":estado_servicio",1),
                                        array(":id_unico",$buvs[0][0]),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                                
                            } else {
                                #Crear Unidad Vivienda
                                $sql_cons ="INSERT INTO `gp_unidad_vivienda_servicio` 
                                        ( `unidad_vivienda`, `tipo_servicio`, `estado_servicio` ) 
                                VALUES  (:unidad_vivienda,  :tipo_servicio, :estado_servicio)";
                                $sql_dato = array(
                                        array(":unidad_vivienda",$unidad_vivienda),
                                        array(":tipo_servicio",3),
                                        array(":estado_servicio",1),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);

                            }
                        }
                        
                        #**********************************************************************#
                        #************************ Buscar/Crear Medidor ************************#
                        $medr = $con->Listar("SELECT * FROM gp_medidor WHERE referencia = $codigo");
                        if(count($medr)>0){
                            $medidor = $medr[0][0];
                        } else {
                            #Crear Medidor
                            $sql_cons ="INSERT INTO `gp_medidor` 
                                    ( `referencia`, `fecha_instalacion`) 
                            VALUES  (:referencia,  :fecha_instalacion)";
                            $sql_dato = array(
                                    array(":referencia",$codigo),
                                    array(":fecha_instalacion",'2018-01-01'),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            $medr = $con->Listar("SELECT * FROM gp_medidor WHERE referencia = $codigo");
                            $medidor = $medr[0][0];
                        }
                        #**********************************************************************#
                        #************ Buscar/Crear Unidad Vivienda-Medidor-Servicio ***********#
                        $buvms = $con->Listar("SELECT * FROM gp_unidad_vivienda_medidor_servicio  
                            WHERE unidad_vivienda_servicio = $unidad_vivienda_servicio 
                            AND medidor = $medidor");
                        if(count($buvms)>0){
                            $uvms = $buvms[0][0];
                        } else {
                            #Crear Unidad Vivienda-Medidor-Servicio
                            $sql_cons ="INSERT INTO `gp_unidad_vivienda_medidor_servicio` 
                                    ( `unidad_vivienda_servicio`, `medidor`) 
                            VALUES  (:unidad_vivienda_servicio,  :medidor)";
                            $sql_dato = array(
                                    array(":unidad_vivienda_servicio",$unidad_vivienda_servicio),
                                    array(":medidor",$medidor),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            #var_dump($resp);
                            $buvms = $con->Listar("SELECT * FROM gp_unidad_vivienda_medidor_servicio 
                            WHERE unidad_vivienda_servicio = $unidad_vivienda_servicio 
                            AND medidor = $medidor");
                            $uvms = $buvms[0][0];
                        }
#**********************************************************************************************************************#
                        #** Buscar Periodo **#
                        $mes    = $objWorksheet->getCellByColumnAndRow(0, $f)->getCalculatedValue();
                        $ga =0;
                        switch ($mes){
                            case '1':
                                $periodo = 1;
                                $fecha = '2018-01-29';
                                $ga =1;
                            break;
                            case '2':
                                $periodo = 2;
                                $fecha = '2018-02-26';
                            break;
                            case '3':
                                $periodo = 3;
                                $fecha = '2018-03-27';
                            break;
                            case '4':
                                $periodo = 4;
                                $fecha = '2018-04-27';
                            break;
                            case '5':
                                $periodo = 5;
                                $fecha = '2018-05-28';
                            break;
                            case '6':
                                $periodo = 6;
                                $fecha = '2018-06-27';
                            break;
                            case '7':
                                $periodo = 7;
                                $fecha = '2018-07-27';
                                $ga =7;
                            break;
                            case '8':
                                $periodo = 8;
                                $fecha = '2018-08-27';
                            break;
                            case '9':
                                $periodo = 9;
                                $fecha = '2018-09-27';
                            break;
                            case '10':
                                $periodo = 10;
                                $fecha = '2018-10-26';
                            break;
                            case '11':
                                $periodo = 11;
                                $fecha = '2018-11-28';
                            break;
                            case '12':
                                $periodo = 12;
                                $fecha = '2018-12-27';
                            break;
                        }
                        $valor = $objWorksheet->getCellByColumnAndRow(4, $f)->getCalculatedValue();
                        #Guardar Lectura
                        $sql_cons ="INSERT INTO `gp_lectura`  
                                ( `unidad_vivienda_medidor_servicio`, `periodo`,
                                `valor`,`aforador`,`fecha`) 
                        VALUES  (:unidad_vivienda_medidor_servicio,  :periodo, 
                                :valor,:aforador, :fecha)";
                        $sql_dato = array(
                                array(":unidad_vivienda_medidor_servicio",$uvms),
                                array(":periodo",$periodo),
                                array(":valor",$valor),
                                array(":aforador",1),
                                array(":fecha",$fecha),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        #var_dump($resp);
                        if(empty($resp)){
                            $total_r +=1;
                        }
                        var_dump($ga);
                        if($ga==7){
                            $valora = $objWorksheet->getCellByColumnAndRow(3, $f)->getCalculatedValue();
                            $sql_cons ="INSERT INTO `gp_lectura`  
                                ( `unidad_vivienda_medidor_servicio`, `periodo`,
                                `valor`,`aforador`,`fecha`) 
                            VALUES  (:unidad_vivienda_medidor_servicio,  :periodo, 
                                    :valor,:aforador, :fecha)";
                            $sql_dato = array(
                                    array(":unidad_vivienda_medidor_servicio",$uvms),
                                    array(":periodo",0),
                                    array(":valor",$valora),
                                    array(":aforador",1),
                                    array(":fecha",'2017-12-31'),
                            );
                            #$resp = $con->InAcEl($sql_cons,$sql_dato);
//                            if(empty($resp)){
//                                $total_r +=1;
//                            }
                            $deuda = $objWorksheet->getCellByColumnAndRow(34, $f)->getCalculatedValue();
                            if($deuda>0){
                                $nanno      = anno($anno);
                                $naan       = $nanno -1;
                                $anna       = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = $naan AND compania = $compania");
                                $annoa      = $anna[0][0];
                                #*** Buscar Tipo Factura ***#
                                $tf = $con->Listar("SELECT * FROM gp_tipo_factura WHERE servicio = 1");
                                $tipo_factura = $tf[0][0];
                                $numero     = numeroFactura($tipo_factura,$annoa);
                                $fecha_f    = $naan.'-12-31';
                                $descripcion = 'Saldo Anterior';
                                $sql_cons ="INSERT INTO `gp_factura` 
                                        ( `numero_factura`, `tercero`, `tipofactura`,
                                    `unidad_vivienda_servicio`,`periodo`,`fecha_factura`,
                                    `fecha_vencimiento`,`descripcion`,
                                    `parametrizacionanno`,`estado_factura`,`centrocosto`) 
                                VALUES  (:numero_factura,  :tercero, :tipofactura, 
                                    :unidad_vivienda_servicio,:periodo,:fecha_factura,
                                    :fecha_vencimiento,:descripcion,
                                    :parametrizacionanno,:estado_factura,:centrocosto)";
                                $sql_dato = array(
                                        array(":numero_factura",$numero),
                                        array(":tercero",$tercero),
                                        array(":tipofactura",$tipo_factura),
                                        array(":unidad_vivienda_servicio",$uvms),
                                        array(":periodo",0),
                                        array(":fecha_factura",$fecha_f),
                                        array(":fecha_vencimiento",$fecha_f),
                                        array(":descripcion",$descripcion),
                                        array(":parametrizacionanno",$annoa),
                                        array(":estado_factura",4),
                                        array(":centrocosto",$centroc),
                                );

                                $resp       = $con->InAcEl($sql_cons,$sql_dato);
                                var_dump($resp);
                                $fi         = $con->Listar("SELECT * FROM gp_factura WHERE numero_factura = $numero AND tipofactura = $tipo_factura");
                                $id_factura = $fi[0][0];

                                $sql_cons ="INSERT INTO `gp_detalle_factura` 
                                        ( `factura`, `concepto_tarifa`, `valor`,
                                    `cantidad`,`iva`,`impoconsumo`,
                                    `ajuste_peso`,`valor_total_ajustado`) 
                                VALUES  (:factura,  :concepto_tarifa, :valor, 
                                    :cantidad,:iva,:impoconsumo,
                                    :ajuste_peso,:valor_total_ajustado)";
                                $sql_dato = array(
                                        array(":factura",$id_factura),
                                        array(":concepto_tarifa",1),
                                        array(":valor",$deuda),
                                        array(":cantidad",1),
                                        array(":iva",0),
                                        array(":impoconsumo",0),
                                        array(":ajuste_peso",0),
                                        array(":valor_total_ajustado",$deuda),
                                );
                                $resp       = $con->InAcEl($sql_cons,$sql_dato);
                            }
                        }
                        
                        
                        
#******************************************************************************************************#
                        
                    }
                }
            }
        }
        
        if($t>0 || $s>0){
            $rta = 1;
            $html = $htmlt.$htmls;
        } else {
            $rta = 0;
            $html="";
        }
        $datos = array();
        $datos = array("rta"=>$rta,"html"=>$html, "registros"=>$total_r);
        echo json_encode($datos);
    break;
}
