<?php
#16/03/2018 |Erica G.
@session_start();
ini_set('max_execution_time', 0);
require '../ExcelR/Classes/PHPExcel/IOFactory.php';                                     
require '../Conexion/ConexionPDO.php';                                                     
$con = new ConexionPDO();
$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];
$fechaE = date('Y-m-d'); 
$anno =$_SESSION['anno'];
ini_set('max_execution_time', 0);
$inputFileName= $_FILES['file']['tmp_name'];                                       
$objReader = new PHPExcel_Reader_Excel2007();					
$objPHPExcel = PHPExcel_IOFactory::load($inputFileName); 			
$action = $_REQUEST['action'];
#*********** Hoja De Contratos **************#
$objWorksheet1 = $objPHPExcel->setActiveSheetIndex(0);				
$total_filas1 = $objWorksheet1->getHighestRow();					
$total_columnas1 = PHPExcel_Cell::columnIndexFromString($objWorksheet1->getHighestColumn());
#*********** Hoja De  Facturas y Detalles**************#
$objWorksheet2 = $objPHPExcel->setActiveSheetIndex(1);				
$total_filas2 = $objWorksheet2->getHighestRow();					
$total_columnas2 = PHPExcel_Cell::columnIndexFromString($objWorksheet2->getHighestColumn());
$buscar = $con->Listar("SELECT cc.id_unico  
        FROM gf_centro_costo cc 
        WHERE cc.nombre = 'Varios' AND cc.parametrizacionanno = $anno");
$centro_costo = $buscar[0][0];
switch ($action){
    #   **************** Validar Terceros , Conceptos Y Cuentas ****************    #
    case 1:
        $arrayTerceros = array();
        $arrayCuentasD  = array();
        $arrayCuentasC  = array();
        $rta =0;
        $htmlcd ="Cuentas Débito No Encontradas";
        $htmlcc ="Cuentas Crédito No Encontradas";
        $htmlt  ="Terceros No Encontrados";
        for ($a = 2; $a <= $total_filas1; $a++) {
            $cuenta = $objWorksheet1->getCellByColumnAndRow(9, $a)->getCalculatedValue();
            # *** Buscar Cuenta *** #
            $cta = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta = $cuenta AND parametrizacionanno = $anno");
            if(count($cta)==0){
                if(in_array($cuenta, $arrayCuentasD)) {

                } else {
                    array_push ( $arrayCuentasD , $cuenta );
                    $rta =1;
                    $htmlcd .=$cuenta.'<br/>';

                }
            }
        }
        for ($a = 2; $a <= $total_filas2; $a++) {
            $nit  = $objWorksheet2->getCellByColumnAndRow(4, $a)->getCalculatedValue();
            $terc = $objWorksheet2->getCellByColumnAndRow(5, $a)->getCalculatedValue();
            $cuenta  = $objWorksheet2->getCellByColumnAndRow(7, $a)->getCalculatedValue();
            $cta = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta = $cuenta AND parametrizacionanno = $anno");
            if(count($cta)==0){
                if(in_array($cuenta, $arrayCuentasC)) {

                } else {
                    array_push ( $arrayCuentasC , $cuenta );
                    $rta =1;
                    $htmlcc .=$cuenta.'<br/>';
                }
            }
            # ** Buscar Tercerro
            $div = explode("-", $nit);
            $tercero = trim($div[0]);
            $tercero = str_replace('.', '', $tercero);
            $t = $con->Listar("SELECT * FROM gf_tercero WHERE numeroidentificacion = $tercero AND compania = $compania");
            if(count($t)==0){
                if(in_array($terc, $arrayTerceros)) {

                } else {
                    array_push ( $arrayTerceros , $terc );
                    $htmlt .=$terc.' - '.$tercero.'<br/>';
                    $rta =1;
                }
            }

        }
        
        $html ="";
        if($rta==1){
            $html .=$htmlcd.'<br/>';
            $html .=$htmlcc.'<br/>';
            $html .=$htmlt.'<br/>';
        }
         $datos = array("msj"=>$html,"rta"=>$rta);
        echo json_encode($datos);
    break;
    #   **************** Validar Terceros , Conceptos Y Cuentas ****************    #
    case 2:
        # ********* Subir Contratos ¨*************#
        $dl = $con->Listar("TRUNCATE TABLE contratos");
        $cn =0;
        for ($a = 2; $a <= $total_filas1; $a++) {
            $entidad    = $objWorksheet1->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $nit        = $objWorksheet1->getCellByColumnAndRow(1, $a)->getCalculatedValue();
            $div = explode("-", $nit);
            $nit = trim($div[0]);
            $nit = str_replace('.', '', $nit);
            
            $contrato   = $objWorksheet1->getCellByColumnAndRow(2, $a)->getCalculatedValue();
            $cuenta     = $objWorksheet1->getCellByColumnAndRow(9, $a)->getCalculatedValue();
            
            $sql_cons ="INSERT INTO `contratos` 
                    ( `entidad`, `nit`, `contrato`,`cuentacontable` ) 
            VALUES  (:entidad,  :nit, :contrato, :cuentacontable)";
            $sql_dato = array(
                    array(":entidad",$entidad),
                    array(":nit",$nit),
                    array(":contrato",$contrato),
                    array(":cuentacontable",$cuenta),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                $cn +=1;
            }
        }
        # ********* Subir Facturas ¨*************#
        $dl = $con->Listar("TRUNCATE TABLE facturas");
        $f =0;
        for ($a = 2; $a <= $total_filas2; $a++) {
            $contrato   = $objWorksheet2->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $fecha      = $objWorksheet2->getCellByColumnAndRow(1, $a)->getCalculatedValue();
            $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fecha);
            $fecha      = date("Y-m-d",$timestamp);
            $factura    = $objWorksheet2->getCellByColumnAndRow(3, $a)->getCalculatedValue();
            $nit        = $objWorksheet2->getCellByColumnAndRow(4, $a)->getCalculatedValue();
            $div = explode("-", $nit);
            $nit = trim($div[0]);
            $nit = str_replace('.', '', $nit);
            $entidad    = $objWorksheet2->getCellByColumnAndRow(5, $a)->getCalculatedValue();
            $puc        = $objWorksheet2->getCellByColumnAndRow(7, $a)->getCalculatedValue();
            $desc       = $objWorksheet2->getCellByColumnAndRow(8, $a)->getCalculatedValue();
            $cantidad   = $objWorksheet2->getCellByColumnAndRow(11, $a)->getCalculatedValue();
            $valor_u    = $objWorksheet2->getCellByColumnAndRow(12, $a)->getCalculatedValue();
            $valor_c    = $objWorksheet2->getCellByColumnAndRow(13, $a)->getCalculatedValue();
            
            $sql_cons ="INSERT INTO `facturas` 
                    ( `contrato`,`fecha`,`numero`, `nit`, `entidad`,
                    `puc`, `descripcion`, `cantidad`, `valor_u`, `valor_t` ) 
            VALUES  (:contrato,  :fecha, :numero, :nit, :entidad, 
                    :puc, :descripcion, :cantidad, :valor_u, :valor_t)";
            $sql_dato = array(
                    array(":contrato",$contrato),
                    array(":fecha",$fecha),
                    array(":numero",$factura),
                    array(":nit",$nit),
                    array(":entidad",$entidad),
                    array(":puc",$puc),
                    array(":descripcion",($desc)),
                    array(":cantidad",$cantidad),
                    array(":valor_u",$valor_u),
                    array(":valor_t",$valor_c),
                
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                $f +=1;
            } else {
                var_dump($resp);
            }
        }
        if($cn>0 && $f>0){
           $datos = array("msj"=>"","rta"=>0); 
        } else {
            $datos = array("msj"=>"No Se Ha Podido Subir La Información","rta"=>1);
        }
        echo json_encode($datos);
    break;
    #*********  Guardar Facturas Y Comprobantes Contables ***********#
    case 3:
        $rta =0;
        $msj ="";
        $f =0;
        $arrayConceptos = array();
        $g =0;
        #***** Buscar Tipo Factura *****#
        
        $tipoFactura    = $_REQUEST['tipofactura'];
        $rowtcs         = $con->Listar("SELECT tipo_comprobante  FROM gp_tipo_factura WHERE id_unico = $tipoFactura");
        $tipoCnt        = $rowtcs[0][0];
        $tarifa         = 1;
        $tipoConcepto   = 1;
        $tipoOperacion  = 1;

        #***** Buscar Facturas ****#
        $fac = $con->Listar("SELECT DISTINCT numero  FROM facturas ORDER BY numero, fecha asc");
        for ($i = 0; $i < count($fac); $i++) {
            $numero = $fac[$i][0];
            $dt = $con->Listar("SELECT nit, fecha FROM facturas WHERE numero = '$numero'");
            $nit    = $dt[0][0];
            $fecha  = $dt[0][1];
            #Buscar Tercero 
            $tr = $con->Listar("SELECT * FROM gf_tercero WHERE numeroidentificacion = $nit AND compania = $compania");
            $tercero = $tr[0][0];
            #******* Buscar Si La Factura Existe *********#
            $fc = $con->Listar("SELECT * FROM gp_factura WHERE numero_factura = '$numero' AND tipofactura = $tipoFactura AND parametrizacionanno = $anno");
            if($fc>0){
                $idFactura = $fc[0][0];
            } else {
                #**** Crear Factura ****#
                $sql_cons ="INSERT INTO `gp_factura` 
                        ( `numero_factura`, `tipofactura`, `tercero`,
                        `fecha_factura`,`fecha_vencimiento`,`centrocosto`,
                        `estado_factura`,`parametrizacionanno`) 
                VALUES (:numero_factura, :tipofactura, :tercero,
                        :fecha_factura,:fecha_vencimiento,:centrocosto,
                        :estado_factura,:parametrizacionanno)";
                $sql_dato = array(
                        array(":numero_factura",$numero),
                        array(":tipofactura",$tipoFactura),
                        array(":tercero",$tercero), 
                        array(":fecha_factura",$fecha), 
                        array(":fecha_vencimiento",$fecha), 
                        array(":centrocosto",$centro_costo), 
                        array(":estado_factura",4), 
                        array(":parametrizacionanno",$anno)
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                //var_dump($resp);
                $fc = $con->Listar("SELECT * FROM gp_factura WHERE numero_factura = '$numero' AND parametrizacionanno = $anno");
                $idFactura = $fc[0][0];
            }
            #******* Buscar Comprobante Cnt **********#
            $cn = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE numero = '$numero' AND tipocomprobante = $tipoCnt AND parametrizacionanno =$anno");
            if(count($cn)>0){
                $idcnt=$cn[0][0];
            } else {
                #****Guardar Comprobante***#
                $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                        ( `numero`, `fecha`, `tipocomprobante`,`compania`,
                        `parametrizacionanno`,`tercero`, `usuario`, `fecha_elaboracion` ) 
                VALUES (:numero, :fecha,  :tipocomprobante,:compania, 
                        :parametrizacion_anno, :tercero, :usuario,:fecha_elaboracion)";
                $sql_dato = array(
                        array(":numero",$numero),
                        array(":fecha",$fecha),
                        array(":tipocomprobante",$tipoCnt),
                        array(":compania",$compania),   
                        array(":parametrizacion_anno",$anno),
                        array(":tercero",$tercero),
                        array(":usuario",$usuario),
                        array(":fecha_elaboracion",$fechaE),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                $cn = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE numero = '$numero' AND tipocomprobante = $tipoCnt AND parametrizacionanno =$anno");
                $idcnt=$cn[0][0];
            }

            #********* Buscar Diferentes Conceptos de La Factura**********#
            $ctos = $con->Listar("SELECT DISTINCT puc, descripcion FROM facturas WHERE numero = '$numero'");
            for ($c = 0; $c < count($ctos); $c++) {
                $nombreConcepto = $ctos[$c][0].' - '.$ctos[$c][1];
                $puc            = $ctos[$c][0];
                #*** Buscar Si Existe Concepto ***#
                $sc = $con->Listar("SELECT * FROM gp_concepto 
                        WHERE nombre = '$nombreConcepto' AND parametrizacionanno = $anno");
                if(count($sc)==0){
                    #****Guardar Concepto***#
                    $sql_cons ="INSERT INTO `gp_concepto` 
                            ( `tipo_concepto`, `nombre`, `tipo_operacion`,
                            `compania`) 
                    VALUES (:tipo_concepto, :nombre, :tipo_operacion,
                            :compania)";
                    $sql_dato = array(
                            array(":tipo_concepto",$tipoConcepto),
                            array(":nombre",$nombreConcepto),
                            array(":tipo_operacion",$tipoOperacion),  
                            array(":compania",$compania)
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                }
                $sc = $con->Listar("SELECT * FROM gp_concepto 
                        WHERE nombre = '$nombreConcepto' AND compania = $compania");
                $idConcepto = $sc[0][0];

                #******* Buscar Cuenta Contable Homologada *******#
                $ctc = $con->Listar("SELECT id_unico, naturaleza FROM gf_cuenta WHERE codi_cuenta = $puc AND parametrizacionanno = $anno");
                $cuentaCredito  = $ctc[0][0];
                $naturaleza     = $ctc[0][1];   
                #********** Buscar Cantidad**********#
                $vc = $con->Listar("SELECT SUM(cantidad) FROM facturas 
                            WHERE numero = '$numero' AND puc = '$puc'");
                $cantidad =$vc[0][0];
                #********** Buscar Valor Total**********#
                $vlu = $con->Listar("SELECT SUM(valor_u) FROM facturas 
                            WHERE numero = '$numero' AND puc = '$puc'");
                $valorU =$vlu[0][0];
                #********** Buscar Valor Total**********#
                $vl = $con->Listar("SELECT SUM(valor_t) FROM facturas 
                            WHERE numero = '$numero' AND puc = '$puc'");
                $valorT =$vl[0][0];
                if($naturaleza==1){
                    $valor = $valorT *-1;
                } else {
                    $valor = $valorT;
                }
                #******* Insertar Detalle Cnt ********#
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto)";
                $sql_dato = array(
                        array(":fecha",$fecha),
                        array(":comprobante",$idcnt),
                        array(":valor",($valor)),
                        array(":cuenta",$cuentaCredito),   
                        array(":naturaleza",$naturaleza),
                        array(":tercero",$tercero),
                        array(":centrocosto",$centro_costo)
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                #var_dump($resp);
                $dtl = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $idcnt");
                $idDetalle = $dtl[0][0];

                #*** Insertar Detalle Factura ¨*****#
                $sql_cons ="INSERT INTO `gp_detalle_factura` 
                        ( `factura`, `concepto_tarifa`,`valor`,
                        `cantidad`,`iva`,`impoconsumo`, `ajuste_peso`,
                        `valor_total_ajustado`, `detallecomprobante`) 
                VALUES (:factura, :concepto_tarifa, :valor,
                        :cantidad, :iva, :impoconsumo, :ajuste_peso, 
                        :valor_total_ajustado,:detallecomprobante)";
                $sql_dato = array(
                        array(":factura",$idFactura),
                        array(":concepto_tarifa",$idConcepto),
                        array(":valor",($valorU)),
                        array(":cantidad",$cantidad),   
                        array(":iva",0),
                        array(":impoconsumo",0),
                        array(":ajuste_peso",0),
                        array(":valor_total_ajustado",$valorT),
                        array(":detallecomprobante",$idDetalle),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                #var_dump($resp);

            }
            #********** Buscar Contratos ******#
            $cot = $con->Listar("SELECT DISTINCT contrato FROM facturas WHERE numero = '$numero'");
            for ($t = 0; $t < count($cot); $t++) {
                $contrato = $cot[$t][0];
                #¨********* Buscar Cuenta Contrato *********#
                $ctr = $con->Listar("SELECT cuentacontable FROM contratos WHERE contrato = '$contrato'");
                $cd = $ctr[0][0];
                #******* Buscar Cuenta Contable Homologada *******#
                $ctc = $con->Listar("SELECT id_unico, naturaleza FROM gf_cuenta WHERE codi_cuenta = '$cd' AND parametrizacionanno = $anno");
                $cuentaDebito   = $ctc[0][0];
                $naturaleza     = $ctc[0][1]; 

                #********** Buscar Valor Total**********#
                $vl = $con->Listar("SELECT SUM(valor_t) FROM facturas 
                            WHERE numero = '$numero' AND contrato = '$contrato'");
                $valorT =$vl[0][0];

                if($naturaleza==1){
                    $valor = $valorT;
                } else {
                    $valor = $valorT*-1;
                }
                #******* Insertar Detalle Cnt ********#
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto)";
                $sql_dato = array(
                        array(":fecha",$fecha),
                        array(":comprobante",$idcnt),
                        array(":valor",($valor)),
                        array(":cuenta",$cuentaDebito),   
                        array(":naturaleza",$naturaleza),
                        array(":tercero",$tercero),
                        array(":centrocosto",$centro_costo)
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                #var_dump($resp);
            }
            if(!empty($idFactura)){
                $g +=1;
            }
        }

        $datos = array("msj"=>$msj,"rta"=>$rta, "g"=>$g);
        echo json_encode($datos);
    break;
    case 4:
        $datos = array("msj"=>" ","rta"=>0);
        echo json_encode($datos);
    break;
}
 