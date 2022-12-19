<?php
#16/03/2018 |Erica G.
@session_start();
ini_set('max_execution_time', 0);
require '../ExcelR/Classes/PHPExcel/IOFactory.php';                                     
require '../Conexion/ConexionPDO.php';                                                     
require '../jsonPptal/funcionesPptal.php';
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


switch ($action){
    #   **************** Validar Terceros , Conceptos Y Cuentas ****************    #
    case 1:
        $arrayTerceros = array();
        $arrayCuentasC  = array();
        $rta =0;
        $htmlcc ="Cuentas Crédito No Encontradas";
        $htmlt  ="Terceros No Encontrados";
        
        for ($a = 2; $a <= $total_filas1; $a++) {
            $nit  = $objWorksheet1->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $terc = $objWorksheet1->getCellByColumnAndRow(2, $a)->getCalculatedValue();
            $cuenta  = $objWorksheet1->getCellByColumnAndRow(5, $a)->getCalculatedValue();
            $cta = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta ='$cuenta' AND parametrizacionanno = $anno");
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
            $t = $con->Listar("SELECT * FROM gf_tercero WHERE numeroidentificacion = $tercero");
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
            $html .=$htmlcc.'<br/>';
            $html .=$htmlt.'<br/>';
        }
         $datos = array("msj"=>$html,"rta"=>$rta);
        echo json_encode($datos);
    break;
    #   **************** Validar Terceros , Conceptos Y Cuentas ****************    #
    case 2:
        # ********* Subir Recaudos ¨*************#
        $dl = $con->Listar("TRUNCATE TABLE recaudos");
        $cn =0;
        for ($a = 2; $a <= $total_filas1; $a++) {
            $entidad    = $objWorksheet1->getCellByColumnAndRow(1, $a)->getCalculatedValue();
            $nit        = $objWorksheet1->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $div = explode("-", $nit);
            $nit = trim($div[0]);
            $nit = str_replace('.', '', $nit);
            
            $factura    = $objWorksheet1->getCellByColumnAndRow(2, $a)->getCalculatedValue();
            $fecha      = $objWorksheet1->getCellByColumnAndRow(3, $a)->getCalculatedValue();
            $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fecha);
            $fecha      = date("Y-m-d",$timestamp);
            $cuenta     = $objWorksheet1->getCellByColumnAndRow(5, $a)->getCalculatedValue();
            $saldo      = $objWorksheet1->getCellByColumnAndRow(6, $a)->getCalculatedValue();
            
            $enero      = $objWorksheet1->getCellByColumnAndRow(21, $a)->getCalculatedValue();
            $enero_rte  = $objWorksheet1->getCellByColumnAndRow(22, $a)->getCalculatedValue();
            $febrero    = $objWorksheet1->getCellByColumnAndRow(23, $a)->getCalculatedValue();
            $feb_rte    = $objWorksheet1->getCellByColumnAndRow(24, $a)->getCalculatedValue();
            $marzo      = $objWorksheet1->getCellByColumnAndRow(25, $a)->getCalculatedValue();
            $marzo_rte  = $objWorksheet1->getCellByColumnAndRow(26, $a)->getCalculatedValue();
            $abril      = $objWorksheet1->getCellByColumnAndRow(27, $a)->getCalculatedValue();
            $abril_rte  = $objWorksheet1->getCellByColumnAndRow(28, $a)->getCalculatedValue();
            $mayo       = $objWorksheet1->getCellByColumnAndRow(29, $a)->getCalculatedValue();
            $mayo_rte   = $objWorksheet1->getCellByColumnAndRow(30, $a)->getCalculatedValue();
            $junio      = $objWorksheet1->getCellByColumnAndRow(31, $a)->getCalculatedValue();
            $junio_rte  = $objWorksheet1->getCellByColumnAndRow(32, $a)->getCalculatedValue();
            $julio      = $objWorksheet1->getCellByColumnAndRow(33, $a)->getCalculatedValue();
            $julio_rte  = $objWorksheet1->getCellByColumnAndRow(34, $a)->getCalculatedValue();
            $agosto     = $objWorksheet1->getCellByColumnAndRow(35, $a)->getCalculatedValue();
            $agosto_rte = $objWorksheet1->getCellByColumnAndRow(36, $a)->getCalculatedValue();
            $otros      = $objWorksheet1->getCellByColumnAndRow(37, $a)->getCalculatedValue();
            $otros_rte  = $objWorksheet1->getCellByColumnAndRow(38, $a)->getCalculatedValue();            
            
            $sql_cons ="INSERT INTO `recaudos` 
                    ( `nit`, `tercero`, `factura`,`fecha`,`cuenta`,`saldo`,
                    `enero`,`rte_enero`,
                    `febrero`,`rte_febrero`,
                    `marzo`,`rte_marzo`, 
                    `abril`,`rte_abril`,
                    `mayo`,`rte_mayo`,
                    `junio`,`rte_junio`,
                    `julio`,`rte_julio`,
                    `agosto`,`rte_agosto`,
                    `otros`,`rte_otros`) 
            VALUES  ( :nit, :tercero, :factura,:fecha,:cuenta,:saldo,
                :enero,:rte_enero,
                :febrero,:rte_febrero,
                :marzo,:rte_marzo,
                :abril,:rte_abril,
                :mayo,:rte_mayo,
                :junio,:rte_junio,
                :julio,:rte_julio,
                :agosto,:rte_agosto,
                :otros,:rte_otros )";
            $sql_dato = array(
                array(":nit",$nit),
                array(":tercero",$entidad),
                array(":factura",$factura),
                array(":fecha",$fecha),
                array(":cuenta",$cuenta),
                array(":saldo",$saldo),
                array(":enero",$enero),
                array(":rte_enero",$enero_rte),
                array(":febrero",$febrero),
                array(":rte_febrero",$feb_rte),
                array(":marzo",$marzo),
                array(":rte_marzo",$marzo_rte),
                array(":abril",$abril),
                array(":rte_abril",$abril_rte),
                array(":mayo",$mayo),
                array(":rte_mayo",$mayo_rte),
                array(":junio",$junio),
                array(":rte_junio",$junio_rte),
                array(":julio",$julio),
                array(":rte_julio",$julio_rte),
                array(":agosto",$agosto),
                array(":rte_agosto",$agosto_rte),
                array(":otros",$otros),
                array(":rte_otros",$otros_rte),
            );
            
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            #var_dump($resp);
            if(empty($resp)){
                $cn +=1;
            }
        }
        if($cn>0){
           $datos = array("msj"=>"","rta"=>0); 
        } else {
            $datos = array("msj"=>"No Se Ha Podido Subir La Información","rta"=>1);
        }
        echo json_encode($datos);
    break;
    #*********  Guardar Facturas Y Comprobantes Contables ***********#
    case 3:
        $rta    =   0;
        $msj    =   "";
        $g      =   0;
        $tf = $con->Listar("SELECT DISTINCT * FROM gp_tipo_factura WHERE prefijo = 'FAC'");
        if(count($tf)>0){
            $tipoFactura    = $tf[0][0];
            $tipoCnt        = $tf[0][4];
            #Buscar Facturas Que No Esten Creadas 
            $fact = $con->Listar("SELECT * FROM recaudos");
            for ($i = 0; $i < count($fact); $i++) {
                $numero = $fact[$i][3];
                $nit    = $fact[$i][1];
                $fecha  = $fact[$i][4];
                $valor  = $fact[$i][6];
                #Buscar Tercero 
                $tr = $con->Listar("SELECT * FROM gf_tercero WHERE numeroidentificacion = $nit");
                $tercero = $tr[0][0];
                #******* Buscar Si La Factura Existe *********#
                $fc = $con->Listar("SELECT * FROM gp_factura WHERE numero_factura = '$numero'");
                $idFactura = $fc[0][0];
                
                #************************************************************************************#
                # ***** Buscar Recaudos***** #
                $tipo           = 1;
                $tipocnt        = 21;
                $cuenta         =$fact[$i][5];
                $cta = $con->Listar("SELECT id_unico, naturaleza FROM gf_cuenta WHERE codi_cuenta ='$cuenta' AND parametrizacionanno = $anno");
                $cuenta_credito = $cta[0][0];
                $naturaleza_c   = $cta[0][1];
                $cuentaDebito = 11584;
                $naturaleza_d = 2;
                $cuenta_rte   = 10477;
                $naturaleza_r = 1;
                #** Enero **#
                $enero          = $fact[$i][7];
                $rte_enero      = $fact[$i][8];
                
                if(!empty($enero) || $enero !="" || $enero !=0 || !empty($rte_enero) || $rte_enero !="" || $rte_enero !=0){
                    #****** Crear Comprobante Recaudo *****#
                    $fecha = '2018/01/31';
                    $tp=$con->Listar("SELECT MAX(numero_pago) FROM gp_pago 
                            WHERE tipo_pago=$tipo AND parametrizacionanno = $anno ");
                    if(!empty($tp[0][0])){
                        $numero_pago=$tp[0][0]+1;
                    }else{
                        $numero_pago='2018000001';
                    }
                    $sql_cons ="INSERT INTO `gp_pago` 
                            ( `numero_pago`, `tipo_pago`,`responsable`,
                            `fecha_pago`,`banco`,`estado`, `parametrizacionanno`) 
                    VALUES (:numero_pago, :tipo_pago, :responsable,
                            :fecha_pago, :banco, :estado, :parametrizacionanno)";
                    $sql_dato = array(
                            array(":numero_pago",$numero_pago),
                            array(":tipo_pago",$tipo),
                            array(":responsable",($tercero)),
                            array(":fecha_pago",$fecha),   
                            array(":banco",21),
                            array(":estado",1),
                            array(":parametrizacionanno",$anno),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $pg = $con->Listar("SELECT * FROM gp_pago WHERE numero_pago = '$numero_pago' AND parametrizacionanno = $anno");
                    $idPago = $pg[0][0];
                    
                    #********* Insertar  CNT**********#
                    #****Guardar Comprobante***#
                    $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                            ( `numero`, `fecha`, `tipocomprobante`,`compania`,
                            `parametrizacionanno`,`tercero`, `usuario`, `fecha_elaboracion` ) 
                    VALUES (:numero, :fecha,  :tipocomprobante,:compania, 
                            :parametrizacion_anno, :tercero, :usuario,:fecha_elaboracion)";
                    $sql_dato = array(
                            array(":numero",$numero_pago),
                            array(":fecha",$fecha),
                            array(":tipocomprobante",$tipocnt),
                            array(":compania",$compania),   
                            array(":parametrizacion_anno",$anno),
                            array(":tercero",$tercero),
                            array(":usuario",$usuario),
                            array(":fecha_elaboracion",$fechaE),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    
                    #************* Insertar Detalles CNT *************#
                    $cn = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE numero = '$numero_pago' AND tipocomprobante = $tipocnt AND parametrizacionanno =$anno");
                    $idcnt=$cn[0][0];
                    
                    
                    #********* Insertar Detalle Debito ***********#
                    if(!empty($enero) || $enero !="" || $enero !=0) {
                        $valorD =$enero *-1;
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                ( `fecha`, `comprobante`,`valor`,
                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorD)),
                                array(":cuenta",$cuentaDebito),   
                                array(":naturaleza",$naturaleza_d),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    if(!empty($rte_enero) || $rte_enero !="" || $rte_enero !=0){
                    #********* Insertar Detalle Retencion ***********#
                        $valorR =$rte_enero; 
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorR)),
                                array(":cuenta",$cuenta_rte),   
                                array(":naturaleza",$naturaleza_r),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }  else {
                        $valorR =0;
                    }
                    #********* Insertar Detalle Credito *************#
                    if($naturaleza_c ==1){
                        $valorC = ($enero+$valorR) *-1;
                    } else {
                        $valorC = ($enero+$valorR);
                    }
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                    VALUES (:fecha,  :comprobante,:valor, 
                            :cuenta,:naturaleza, :tercero, :centrocosto)";
                    $sql_dato = array(
                            array(":fecha",$fecha),
                            array(":comprobante",$idcnt),
                            array(":valor",($valorC)),
                            array(":cuenta",$cuenta_credito),   
                            array(":naturaleza",$naturaleza_c),
                            array(":tercero",$tercero),
                            array(":centrocosto",370)
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $dtl = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $idcnt");
                    $idDetalle = $dtl[0][0];
                    
                    #************ Buscar Detalles Factura **********#
                    $valorRecaudo = ($enero+$valorR);
                    $dt = guardarPagoFactura('', $idFactura, $idPago, $valorRecaudo);
                    if($dt>0){
                        $g +=1;
                    }
                                    
                }
                #** Febrero **#
                $febrero        = $fact[$i][9];
                $rte_febrero    = $fact[$i][10];
                if(!empty($febrero) || $febrero !="" || $febrero !=0 || !empty($rte_febrero) || $rte_febrero !="" || $rte_febrero !=0){
                    $fecha = '2018/02/28';
                    $tp=$con->Listar("SELECT MAX(numero_pago) FROM gp_pago 
                            WHERE tipo_pago=$tipo AND parametrizacionanno = $anno ");
                    if(!empty($tp[0][0])){
                        $numero_pago=$tp[0][0]+1;
                    }else{
                        $numero_pago='2018000001';
                    }
                    #****** Crear Comprobante Recaudo *****#
                    $sql_cons ="INSERT INTO `gp_pago` 
                            ( `numero_pago`, `tipo_pago`,`responsable`,
                            `fecha_pago`,`banco`,`estado`, `parametrizacionanno`) 
                    VALUES (:numero_pago, :tipo_pago, :responsable,
                            :fecha_pago, :banco, :estado, :parametrizacionanno)";
                    $sql_dato = array(
                            array(":numero_pago",$numero_pago),
                            array(":tipo_pago",$tipo),
                            array(":responsable",($tercero)),
                            array(":fecha_pago",$fecha),   
                            array(":banco",21),
                            array(":estado",1),
                            array(":parametrizacionanno",$anno),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $pg = $con->Listar("SELECT * FROM gp_pago WHERE numero_pago = '$numero_pago' AND parametrizacionanno = $anno");
                    $idPago = $pg[0][0];
                    
                    #********* Insertar  CNT**********#
                    #****Guardar Comprobante***#
                    $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                            ( `numero`, `fecha`, `tipocomprobante`,`compania`,
                            `parametrizacionanno`,`tercero`, `usuario`, `fecha_elaboracion` ) 
                    VALUES (:numero, :fecha,  :tipocomprobante,:compania, 
                            :parametrizacion_anno, :tercero, :usuario,:fecha_elaboracion)";
                    $sql_dato = array(
                            array(":numero",$numero_pago),
                            array(":fecha",$fecha),
                            array(":tipocomprobante",$tipocnt),
                            array(":compania",$compania),   
                            array(":parametrizacion_anno",$anno),
                            array(":tercero",$tercero),
                            array(":usuario",$usuario),
                            array(":fecha_elaboracion",$fechaE),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    
                    #************* Insertar Detalles CNT *************#
                    $cn = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE numero = '$numero_pago' AND tipocomprobante = $tipocnt AND parametrizacionanno =$anno");
                    $idcnt=$cn[0][0];
                    #********* Insertar Detalle Debito ***********#
                    if(!empty($febrero) || $febrero !="" || $febrero !=0 ) {
                        $valorD =$febrero *-1;
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                ( `fecha`, `comprobante`,`valor`,
                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorD)),
                                array(":cuenta",$cuentaDebito),   
                                array(":naturaleza",$naturaleza_d),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    if(!empty($rte_febrero) || $rte_febrero !="" || $rte_febrero !=0){
                    #********* Insertar Detalle Retencion ***********#
                        $valorR =$rte_febrero; 
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorR)),
                                array(":cuenta",$cuenta_rte),   
                                array(":naturaleza",$naturaleza_r),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }  else {
                        $valorR =0;
                    }
                    #********* Insertar Detalle Credito *************#
                    if($naturaleza_c ==1){
                        $valorC = ($febrero+$valorR) *-1;
                    } else {
                        $valorC = ($febrero+$valorR);
                    }
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                    VALUES (:fecha,  :comprobante,:valor, 
                            :cuenta,:naturaleza, :tercero, :centrocosto)";
                    $sql_dato = array(
                            array(":fecha",$fecha),
                            array(":comprobante",$idcnt),
                            array(":valor",($valorC)),
                            array(":cuenta",$cuenta_credito),   
                            array(":naturaleza",$naturaleza_c),
                            array(":tercero",$tercero),
                            array(":centrocosto",370)
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $dtl = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $idcnt");
                    $idDetalle = $dtl[0][0];
                    
                    #************ Buscar Detalles Factura **********#
                    $valorRecaudo = ($febrero+$valorR);
                    $dt = guardarPagoFactura('', $idFactura, $idPago, $valorRecaudo);
                    if($dt>0){
                        $g +=1;
                    }
                }
                
                #** Marzo **#
                $marzo          = $fact[$i][11];
                $rte_marzo      = $fact[$i][12];
                if(!empty($marzo) || $marzo !="" || $marzo !=0 || !empty($rte_marzo) || $rte_marzo !="" || $rte_marzo !=0){
                    #****** Crear Comprobante Recaudo *****#
                    $fecha = '2018/03/31';
                    $tp=$con->Listar("SELECT MAX(numero_pago) FROM gp_pago 
                            WHERE tipo_pago=$tipo AND parametrizacionanno = $anno ");
                    if(!empty($tp[0][0])){
                        $numero_pago=$tp[0][0]+1;
                    }else{
                        $numero_pago='2018000001';
                    }
                    $sql_cons ="INSERT INTO `gp_pago` 
                            ( `numero_pago`, `tipo_pago`,`responsable`,
                            `fecha_pago`,`banco`,`estado`, `parametrizacionanno`) 
                    VALUES (:numero_pago, :tipo_pago, :responsable,
                            :fecha_pago, :banco, :estado, :parametrizacionanno)";
                    $sql_dato = array(
                            array(":numero_pago",$numero_pago),
                            array(":tipo_pago",$tipo),
                            array(":responsable",($tercero)),
                            array(":fecha_pago",$fecha),   
                            array(":banco",21),
                            array(":estado",1),
                            array(":parametrizacionanno",$anno),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $pg = $con->Listar("SELECT * FROM gp_pago WHERE numero_pago = '$numero_pago' AND parametrizacionanno = $anno");
                    $idPago = $pg[0][0];
                    
                    #********* Insertar  CNT**********#
                    #****Guardar Comprobante***#
                    $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                            ( `numero`, `fecha`, `tipocomprobante`,`compania`,
                            `parametrizacionanno`,`tercero`, `usuario`, `fecha_elaboracion` ) 
                    VALUES (:numero, :fecha,  :tipocomprobante,:compania, 
                            :parametrizacion_anno, :tercero, :usuario,:fecha_elaboracion)";
                    $sql_dato = array(
                            array(":numero",$numero_pago),
                            array(":fecha",$fecha),
                            array(":tipocomprobante",$tipocnt),
                            array(":compania",$compania),   
                            array(":parametrizacion_anno",$anno),
                            array(":tercero",$tercero),
                            array(":usuario",$usuario),
                            array(":fecha_elaboracion",$fechaE),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    
                    #************* Insertar Detalles CNT *************#
                    $cn = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE numero = '$numero_pago' AND tipocomprobante = $tipocnt AND parametrizacionanno =$anno");
                    $idcnt=$cn[0][0];
                    
                    
                    #********* Insertar Detalle Debito ***********#
                    if(!empty($marzo) || $marzo !="" || $marzo !=0) {
                        $valorD =$marzo *-1;
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                ( `fecha`, `comprobante`,`valor`,
                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorD)),
                                array(":cuenta",$cuentaDebito),   
                                array(":naturaleza",$naturaleza_d),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    if(!empty($rte_marzo) || $rte_marzo !="" || $rte_marzo !=0){
                    #********* Insertar Detalle Retencion ***********#
                        $valorR =$rte_marzo; 
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorR)),
                                array(":cuenta",$cuenta_rte),   
                                array(":naturaleza",$naturaleza_r),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }  else {
                        $valorR =0;
                    }
                    #********* Insertar Detalle Credito *************#
                    if($naturaleza_c ==1){
                        $valorC = ($marzo+$valorR) *-1;
                    } else {
                        $valorC = ($marzo+$valorR);
                    }
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                    VALUES (:fecha,  :comprobante,:valor, 
                            :cuenta,:naturaleza, :tercero, :centrocosto)";
                    $sql_dato = array(
                            array(":fecha",$fecha),
                            array(":comprobante",$idcnt),
                            array(":valor",($valorC)),
                            array(":cuenta",$cuenta_credito),   
                            array(":naturaleza",$naturaleza_c),
                            array(":tercero",$tercero),
                            array(":centrocosto",370)
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $dtl = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $idcnt");
                    $idDetalle = $dtl[0][0];
                    
                    #************ Buscar Detalles Factura **********#
                    $df = $con->Listar("SELECT * FROM gp_detalle_factura WHERE factura = $idFactura");
                    $valorRecaudo = ($marzo+$valorR);
                    $dt = guardarPagoFactura('', $idFactura, $idPago, $valorRecaudo);
                    if($dt>0){
                        $g +=1;
                    }
                }
                
                #** Abril **#
                $abril          = $fact[$i][13];
                $rte_abril      = $fact[$i][14];
                if(!empty($abril) || $abril !="" || $abril !=0 || !empty($rte_abril) || $rte_abril !="" || $rte_abril !=0){
                    #****** Crear Comprobante Recaudo *****#
                    $fecha = '2018/04/30';
                    $tp=$con->Listar("SELECT MAX(numero_pago) FROM gp_pago 
                            WHERE tipo_pago=$tipo AND parametrizacionanno = $anno ");
                    if(!empty($tp[0][0])){
                        $numero_pago=$tp[0][0]+1;
                    }else{
                        $numero_pago='2018000001';
                    }
                    $sql_cons ="INSERT INTO `gp_pago` 
                            ( `numero_pago`, `tipo_pago`,`responsable`,
                            `fecha_pago`,`banco`,`estado`, `parametrizacionanno`) 
                    VALUES (:numero_pago, :tipo_pago, :responsable,
                            :fecha_pago, :banco, :estado, :parametrizacionanno)";
                    $sql_dato = array(
                            array(":numero_pago",$numero_pago),
                            array(":tipo_pago",$tipo),
                            array(":responsable",($tercero)),
                            array(":fecha_pago",$fecha),   
                            array(":banco",21),
                            array(":estado",1),
                            array(":parametrizacionanno",$anno),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $pg = $con->Listar("SELECT * FROM gp_pago WHERE numero_pago = '$numero_pago' AND parametrizacionanno = $anno");
                    $idPago = $pg[0][0];
                    
                    #********* Insertar  CNT**********#
                    #****Guardar Comprobante***#
                    $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                            ( `numero`, `fecha`, `tipocomprobante`,`compania`,
                            `parametrizacionanno`,`tercero`, `usuario`, `fecha_elaboracion` ) 
                    VALUES (:numero, :fecha,  :tipocomprobante,:compania, 
                            :parametrizacion_anno, :tercero, :usuario,:fecha_elaboracion)";
                    $sql_dato = array(
                            array(":numero",$numero_pago),
                            array(":fecha",$fecha),
                            array(":tipocomprobante",$tipocnt),
                            array(":compania",$compania),   
                            array(":parametrizacion_anno",$anno),
                            array(":tercero",$tercero),
                            array(":usuario",$usuario),
                            array(":fecha_elaboracion",$fechaE),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    
                    #************* Insertar Detalles CNT *************#
                    $cn = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE numero = '$numero_pago' AND tipocomprobante = $tipocnt AND parametrizacionanno =$anno");
                    $idcnt=$cn[0][0];
                    
                    
                    #********* Insertar Detalle Debito ***********#
                    if(!empty($abril) || $abril !="" || $abril !=0) { 
                        $valorD =$abril *-1;
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                ( `fecha`, `comprobante`,`valor`,
                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorD)),
                                array(":cuenta",$cuentaDebito),   
                                array(":naturaleza",$naturaleza_d),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    if(!empty($rte_abril) || $rte_abril !="" || $rte_abril !=0){
                    #********* Insertar Detalle Retencion ***********#
                        $valorR =$rte_abril; 
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorR)),
                                array(":cuenta",$cuenta_rte),   
                                array(":naturaleza",$naturaleza_r),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }  else {
                        $valorR =0;
                    }
                    #********* Insertar Detalle Credito *************#
                    if($naturaleza_c ==1){
                        $valorC = ($abril+$valorR) *-1;
                    } else {
                        $valorC = ($abril+$valorR);
                    }
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                    VALUES (:fecha,  :comprobante,:valor, 
                            :cuenta,:naturaleza, :tercero, :centrocosto)";
                    $sql_dato = array(
                            array(":fecha",$fecha),
                            array(":comprobante",$idcnt),
                            array(":valor",($valorC)),
                            array(":cuenta",$cuenta_credito),   
                            array(":naturaleza",$naturaleza_c),
                            array(":tercero",$tercero),
                            array(":centrocosto",370)
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $dtl = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $idcnt");
                    $idDetalle = $dtl[0][0];
                    
                    #************ Buscar Detalles Factura **********#
                    $df = $con->Listar("SELECT * FROM gp_detalle_factura WHERE factura = $idFactura");
                    $valorRecaudo = ($abril+$valorR);
                    $dt = guardarPagoFactura('', $idFactura, $idPago, $valorRecaudo);
                    if($dt>0){
                        $g +=1;
                    }
                }
                
                #** Mayo **#
                $mayo          = $fact[$i][15];
                $rte_mayo      = $fact[$i][16];
                if(!empty($mayo) || $mayo !="" || $mayo !=0 || !empty($rte_mayo) || $rte_mayo !="" || $rte_mayo !=0){
                    #****** Crear Comprobante Recaudo *****#
                    $fecha = '2018/05/31';
                    $tp=$con->Listar("SELECT MAX(numero_pago) FROM gp_pago 
                            WHERE tipo_pago=$tipo AND parametrizacionanno = $anno ");
                    if(!empty($tp[0][0])){
                        $numero_pago=$tp[0][0]+1;
                    }else{
                        $numero_pago='2018000001';
                    }
                    $sql_cons ="INSERT INTO `gp_pago` 
                            ( `numero_pago`, `tipo_pago`,`responsable`,
                            `fecha_pago`,`banco`,`estado`, `parametrizacionanno`) 
                    VALUES (:numero_pago, :tipo_pago, :responsable,
                            :fecha_pago, :banco, :estado, :parametrizacionanno)";
                    $sql_dato = array(
                            array(":numero_pago",$numero_pago),
                            array(":tipo_pago",$tipo),
                            array(":responsable",($tercero)),
                            array(":fecha_pago",$fecha),   
                            array(":banco",21),
                            array(":estado",1),
                            array(":parametrizacionanno",$anno),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $pg = $con->Listar("SELECT * FROM gp_pago WHERE numero_pago = '$numero_pago' AND parametrizacionanno = $anno");
                    $idPago = $pg[0][0];
                    
                    #********* Insertar  CNT**********#
                    #****Guardar Comprobante***#
                    $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                            ( `numero`, `fecha`, `tipocomprobante`,`compania`,
                            `parametrizacionanno`,`tercero`, `usuario`, `fecha_elaboracion` ) 
                    VALUES (:numero, :fecha,  :tipocomprobante,:compania, 
                            :parametrizacion_anno, :tercero, :usuario,:fecha_elaboracion)";
                    $sql_dato = array(
                            array(":numero",$numero_pago),
                            array(":fecha",$fecha),
                            array(":tipocomprobante",$tipocnt),
                            array(":compania",$compania),   
                            array(":parametrizacion_anno",$anno),
                            array(":tercero",$tercero),
                            array(":usuario",$usuario),
                            array(":fecha_elaboracion",$fechaE),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    
                    #************* Insertar Detalles CNT *************#
                    $cn = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE numero = '$numero_pago' AND tipocomprobante = $tipocnt AND parametrizacionanno =$anno");
                    $idcnt=$cn[0][0];
                    
                    
                    #********* Insertar Detalle Debito ***********#
                    if(!empty($mayo) || $mayo !="" || $mayo !=0){ 
                        $valorD =$mayo *-1;
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                ( `fecha`, `comprobante`,`valor`,
                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorD)),
                                array(":cuenta",$cuentaDebito),   
                                array(":naturaleza",$naturaleza_d),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    if(!empty($rte_mayo) || $rte_mayo !="" || $rte_mayo !=0){
                    #********* Insertar Detalle Retencion ***********#
                        $valorR =$rte_mayo; 
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorR)),
                                array(":cuenta",$cuenta_rte),   
                                array(":naturaleza",$naturaleza_r),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }  else {
                        $valorR =0;
                    }
                    #********* Insertar Detalle Credito *************#
                    if($naturaleza_c ==1){
                        $valorC = ($mayo+$valorR) *-1;
                    } else {
                        $valorC = ($mayo+$valorR);
                    }
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                    VALUES (:fecha,  :comprobante,:valor, 
                            :cuenta,:naturaleza, :tercero, :centrocosto)";
                    $sql_dato = array(
                            array(":fecha",$fecha),
                            array(":comprobante",$idcnt),
                            array(":valor",($valorC)),
                            array(":cuenta",$cuenta_credito),   
                            array(":naturaleza",$naturaleza_c),
                            array(":tercero",$tercero),
                            array(":centrocosto",370)
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $dtl = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $idcnt");
                    $idDetalle = $dtl[0][0];
                    
                    #************ Buscar Detalles Factura **********#
                    $df = $con->Listar("SELECT * FROM gp_detalle_factura WHERE factura = $idFactura");
                    $valorRecaudo = ($mayo+$valorR);
                    $dt = guardarPagoFactura('', $idFactura, $idPago, $valorRecaudo);
                    if($dt>0){
                        $g +=1;
                    }
                }
                
                #** Junio **#
                $junio          = $fact[$i][17];
                $rte_junio      = $fact[$i][18];
                if(!empty($junio) || $junio !="" || $junio !=0 || !empty($rte_junio) || $rte_junio !="" || $rte_junio !=0){
                    #****** Crear Comprobante Recaudo *****#
                    $fecha = '2018/06/30';
                    $tp=$con->Listar("SELECT MAX(numero_pago) FROM gp_pago 
                            WHERE tipo_pago=$tipo AND parametrizacionanno = $anno ");
                    if(!empty($tp[0][0])){
                        $numero_pago=$tp[0][0]+1;
                    }else{
                        $numero_pago='2018000001';
                    }
                    $sql_cons ="INSERT INTO `gp_pago` 
                            ( `numero_pago`, `tipo_pago`,`responsable`,
                            `fecha_pago`,`banco`,`estado`, `parametrizacionanno`) 
                    VALUES (:numero_pago, :tipo_pago, :responsable,
                            :fecha_pago, :banco, :estado, :parametrizacionanno)";
                    $sql_dato = array(
                            array(":numero_pago",$numero_pago),
                            array(":tipo_pago",$tipo),
                            array(":responsable",($tercero)),
                            array(":fecha_pago",$fecha),   
                            array(":banco",21),
                            array(":estado",1),
                            array(":parametrizacionanno",$anno),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $pg = $con->Listar("SELECT * FROM gp_pago WHERE numero_pago = '$numero_pago' AND parametrizacionanno = $anno");
                    $idPago = $pg[0][0];
                    
                    #********* Insertar  CNT**********#
                    #****Guardar Comprobante***#
                    $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                            ( `numero`, `fecha`, `tipocomprobante`,`compania`,
                            `parametrizacionanno`,`tercero`, `usuario`, `fecha_elaboracion` ) 
                    VALUES (:numero, :fecha,  :tipocomprobante,:compania, 
                            :parametrizacion_anno, :tercero, :usuario,:fecha_elaboracion)";
                    $sql_dato = array(
                            array(":numero",$numero_pago),
                            array(":fecha",$fecha),
                            array(":tipocomprobante",$tipocnt),
                            array(":compania",$compania),   
                            array(":parametrizacion_anno",$anno),
                            array(":tercero",$tercero),
                            array(":usuario",$usuario),
                            array(":fecha_elaboracion",$fechaE),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    
                    #************* Insertar Detalles CNT *************#
                    $cn = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE numero = '$numero_pago' AND tipocomprobante = $tipocnt AND parametrizacionanno =$anno");
                    $idcnt=$cn[0][0];
                    
                    
                    #********* Insertar Detalle Debito ***********#
                    if(!empty($junio) || $junio !="" || $junio !=0) { 
                        $valorD =$junio *-1;
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                ( `fecha`, `comprobante`,`valor`,
                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorD)),
                                array(":cuenta",$cuentaDebito),   
                                array(":naturaleza",$naturaleza_d),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    if(!empty($rte_junio) || $rte_junio !="" || $rte_junio !=0){
                    #********* Insertar Detalle Retencion ***********#
                        $valorR =$rte_junio; 
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorR)),
                                array(":cuenta",$cuenta_rte),   
                                array(":naturaleza",$naturaleza_r),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }  else {
                        $valorR =0;
                    }
                    #********* Insertar Detalle Credito *************#
                    if($naturaleza_c ==1){
                        $valorC = ($junio+$valorR) *-1;
                    } else {
                        $valorC = ($junio+$valorR);
                    }
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                    VALUES (:fecha,  :comprobante,:valor, 
                            :cuenta,:naturaleza, :tercero, :centrocosto)";
                    $sql_dato = array(
                            array(":fecha",$fecha),
                            array(":comprobante",$idcnt),
                            array(":valor",($valorC)),
                            array(":cuenta",$cuenta_credito),   
                            array(":naturaleza",$naturaleza_c),
                            array(":tercero",$tercero),
                            array(":centrocosto",370)
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $dtl = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $idcnt");
                    $idDetalle = $dtl[0][0];
                    
                    #************ Buscar Detalles Factura **********#
                    $df = $con->Listar("SELECT * FROM gp_detalle_factura WHERE factura = $idFactura");
                    $valorRecaudo = ($junio+$valorR);
                    $dt = guardarPagoFactura('', $idFactura, $idPago, $valorRecaudo);
                    if($dt>0){
                        $g +=1;
                    }
                }
                
                #** Julio **#
                $julio          = $fact[$i][19];
                $rte_julio      = $fact[$i][20];
                if(!empty($julio) || $julio !="" || $julio !=0 || !empty($rte_julio) || $rte_julio !="" || $rte_julio !=0){
                    #****** Crear Comprobante Recaudo *****#
                    $fecha = '2018/07/31';
                    $tp=$con->Listar("SELECT MAX(numero_pago) FROM gp_pago 
                            WHERE tipo_pago=$tipo AND parametrizacionanno = $anno ");
                    if(!empty($tp[0][0])){
                        $numero_pago=$tp[0][0]+1;
                    }else{
                        $numero_pago='2018000001';
                    }
                    $sql_cons ="INSERT INTO `gp_pago` 
                            ( `numero_pago`, `tipo_pago`,`responsable`,
                            `fecha_pago`,`banco`,`estado`, `parametrizacionanno`) 
                    VALUES (:numero_pago, :tipo_pago, :responsable,
                            :fecha_pago, :banco, :estado, :parametrizacionanno)";
                    $sql_dato = array(
                            array(":numero_pago",$numero_pago),
                            array(":tipo_pago",$tipo),
                            array(":responsable",($tercero)),
                            array(":fecha_pago",$fecha),   
                            array(":banco",21),
                            array(":estado",1),
                            array(":parametrizacionanno",$anno),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $pg = $con->Listar("SELECT * FROM gp_pago WHERE numero_pago = '$numero_pago' AND parametrizacionanno = $anno");
                    $idPago = $pg[0][0];
                    
                    #********* Insertar  CNT**********#
                    #****Guardar Comprobante***#
                    $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                            ( `numero`, `fecha`, `tipocomprobante`,`compania`,
                            `parametrizacionanno`,`tercero`, `usuario`, `fecha_elaboracion` ) 
                    VALUES (:numero, :fecha,  :tipocomprobante,:compania, 
                            :parametrizacion_anno, :tercero, :usuario,:fecha_elaboracion)";
                    $sql_dato = array(
                            array(":numero",$numero_pago),
                            array(":fecha",$fecha),
                            array(":tipocomprobante",$tipocnt),
                            array(":compania",$compania),   
                            array(":parametrizacion_anno",$anno),
                            array(":tercero",$tercero),
                            array(":usuario",$usuario),
                            array(":fecha_elaboracion",$fechaE),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    
                    #************* Insertar Detalles CNT *************#
                    $cn = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE numero = '$numero_pago' AND tipocomprobante = $tipocnt AND parametrizacionanno =$anno");
                    $idcnt=$cn[0][0];
                    
                    
                    #********* Insertar Detalle Debito ***********#
                    if(!empty($julio) || $julio !="" || $julio !=0) { 
                        $valorD =$julio *-1;
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                ( `fecha`, `comprobante`,`valor`,
                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorD)),
                                array(":cuenta",$cuentaDebito),   
                                array(":naturaleza",$naturaleza_d),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    if(!empty($rte_julio) || $rte_julio !="" || $rte_julio !=0){
                    #********* Insertar Detalle Retencion ***********#
                        $valorR =$rte_julio; 
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorR)),
                                array(":cuenta",$cuenta_rte),   
                                array(":naturaleza",$naturaleza_r),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }  else {
                        $valorR =0;
                    }
                    #********* Insertar Detalle Credito *************#
                    if($naturaleza_c ==1){
                        $valorC = ($julio+$valorR) *-1;
                    } else {
                        $valorC = ($julio+$valorR);
                    }
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                    VALUES (:fecha,  :comprobante,:valor, 
                            :cuenta,:naturaleza, :tercero, :centrocosto)";
                    $sql_dato = array(
                            array(":fecha",$fecha),
                            array(":comprobante",$idcnt),
                            array(":valor",($valorC)),
                            array(":cuenta",$cuenta_credito),   
                            array(":naturaleza",$naturaleza_c),
                            array(":tercero",$tercero),
                            array(":centrocosto",370)
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $dtl = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $idcnt");
                    $idDetalle = $dtl[0][0];
                    
                    #************ Buscar Detalles Factura **********#
                    $df = $con->Listar("SELECT * FROM gp_detalle_factura WHERE factura = $idFactura");
                    $valorRecaudo = ($julio+$valorR);
                    $dt = guardarPagoFactura('', $idFactura, $idPago, $valorRecaudo);
                    if($dt>0){
                        $g +=1;
                    }
                }
                
                #** Agosto **#
                $agosto          = $fact[$i][21];
                $rte_agosto      = $fact[$i][22];
                if(!empty($agosto) || $agosto !="" || $agosto !=0 || !empty($rte_agosto) || $rte_agosto !="" || $rte_agosto !=0){
                    #****** Crear Comprobante Recaudo *****#
                    $fecha = '2018/08/31';
                    $tp=$con->Listar("SELECT MAX(numero_pago) FROM gp_pago 
                            WHERE tipo_pago=$tipo AND parametrizacionanno = $anno ");
                    if(!empty($tp[0][0])){
                        $numero_pago=$tp[0][0]+1;
                    }else{
                        $numero_pago='2018000001';
                    }
                    $sql_cons ="INSERT INTO `gp_pago` 
                            ( `numero_pago`, `tipo_pago`,`responsable`,
                            `fecha_pago`,`banco`,`estado`, `parametrizacionanno`) 
                    VALUES (:numero_pago, :tipo_pago, :responsable,
                            :fecha_pago, :banco, :estado, :parametrizacionanno)";
                    $sql_dato = array(
                            array(":numero_pago",$numero_pago),
                            array(":tipo_pago",$tipo),
                            array(":responsable",($tercero)),
                            array(":fecha_pago",$fecha),   
                            array(":banco",21),
                            array(":estado",1),
                            array(":parametrizacionanno",$anno),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $pg = $con->Listar("SELECT * FROM gp_pago WHERE numero_pago = '$numero_pago' AND parametrizacionanno = $anno");
                    $idPago = $pg[0][0];
                    
                    #********* Insertar  CNT**********#
                    #****Guardar Comprobante***#
                    $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                            ( `numero`, `fecha`, `tipocomprobante`,`compania`,
                            `parametrizacionanno`,`tercero`, `usuario`, `fecha_elaboracion` ) 
                    VALUES (:numero, :fecha,  :tipocomprobante,:compania, 
                            :parametrizacion_anno, :tercero, :usuario,:fecha_elaboracion)";
                    $sql_dato = array(
                            array(":numero",$numero_pago),
                            array(":fecha",$fecha),
                            array(":tipocomprobante",$tipocnt),
                            array(":compania",$compania),   
                            array(":parametrizacion_anno",$anno),
                            array(":tercero",$tercero),
                            array(":usuario",$usuario),
                            array(":fecha_elaboracion",$fechaE),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    
                    #************* Insertar Detalles CNT *************#
                    $cn = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE numero = '$numero_pago' AND tipocomprobante = $tipocnt AND parametrizacionanno =$anno");
                    $idcnt=$cn[0][0];
                    
                    
                    #********* Insertar Detalle Debito ***********#
                    if(!empty($agosto) || $agosto !="" || $agosto !=0) {
                        $valorD =$agosto *-1;
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                ( `fecha`, `comprobante`,`valor`,
                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorD)),
                                array(":cuenta",$cuentaDebito),   
                                array(":naturaleza",$naturaleza_d),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    if(!empty($rte_agosto) || $rte_agosto !="" || $rte_agosto !=0){
                    #********* Insertar Detalle Retencion ***********#
                        $valorR =$rte_agosto; 
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorR)),
                                array(":cuenta",$cuenta_rte),   
                                array(":naturaleza",$naturaleza_r),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }  else {
                        $valorR =0;
                    }
                    #********* Insertar Detalle Credito *************#
                    if($naturaleza_c ==1){
                        $valorC = ($agosto+$valorR) *-1;
                    } else {
                        $valorC = ($agosto+$valorR);
                    }
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                    VALUES (:fecha,  :comprobante,:valor, 
                            :cuenta,:naturaleza, :tercero, :centrocosto)";
                    $sql_dato = array(
                            array(":fecha",$fecha),
                            array(":comprobante",$idcnt),
                            array(":valor",($valorC)),
                            array(":cuenta",$cuenta_credito),   
                            array(":naturaleza",$naturaleza_c),
                            array(":tercero",$tercero),
                            array(":centrocosto",370)
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $dtl = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $idcnt");
                    $idDetalle = $dtl[0][0];
                    
                    #************ Buscar Detalles Factura **********#
                    $df = $con->Listar("SELECT * FROM gp_detalle_factura WHERE factura = $idFactura");
                    $valorRecaudo = ($agosto+$valorR);
                    $dt = guardarPagoFactura('', $idFactura, $idPago, $valorRecaudo);
                    if($dt>0){
                        $g +=1;
                    }
                }
                
                #** Otros **#
                $otros          = $fact[$i][23];
                $rte_otros      = $fact[$i][24];
                if(!empty($otros) || $otros !="" || $otros !=0 || !empty($rte_otros) || $rte_otros !="" || $rte_otros !=0){
                    #****** Crear Comprobante Recaudo *****#
                    $fecha = '2018/09/30';
                    $tp=$con->Listar("SELECT MAX(numero_pago) FROM gp_pago 
                            WHERE tipo_pago=$tipo AND parametrizacionanno = $anno ");
                    if(!empty($tp[0][0])){
                        $numero_pago=$tp[0][0]+1;
                    }else{
                        $numero_pago='2018000001';
                    }
                    $sql_cons ="INSERT INTO `gp_pago` 
                            ( `numero_pago`, `tipo_pago`,`responsable`,
                            `fecha_pago`,`banco`,`estado`, `parametrizacionanno`) 
                    VALUES (:numero_pago, :tipo_pago, :responsable,
                            :fecha_pago, :banco, :estado, :parametrizacionanno)";
                    $sql_dato = array(
                            array(":numero_pago",$numero_pago),
                            array(":tipo_pago",$tipo),
                            array(":responsable",($tercero)),
                            array(":fecha_pago",$fecha),   
                            array(":banco",21),
                            array(":estado",1),
                            array(":parametrizacionanno",$anno),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $pg = $con->Listar("SELECT * FROM gp_pago WHERE numero_pago = '$numero_pago' AND parametrizacionanno = $anno");
                    $idPago = $pg[0][0];
                    
                    #********* Insertar  CNT**********#
                    #****Guardar Comprobante***#
                    $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                            ( `numero`, `fecha`, `tipocomprobante`,`compania`,
                            `parametrizacionanno`,`tercero`, `usuario`, `fecha_elaboracion` ) 
                    VALUES (:numero, :fecha,  :tipocomprobante,:compania, 
                            :parametrizacion_anno, :tercero, :usuario,:fecha_elaboracion)";
                    $sql_dato = array(
                            array(":numero",$numero_pago),
                            array(":fecha",$fecha),
                            array(":tipocomprobante",$tipocnt),
                            array(":compania",$compania),   
                            array(":parametrizacion_anno",$anno),
                            array(":tercero",$tercero),
                            array(":usuario",$usuario),
                            array(":fecha_elaboracion",$fechaE),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    
                    #************* Insertar Detalles CNT *************#
                    $cn = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE numero = '$numero_pago' AND tipocomprobante = $tipocnt AND parametrizacionanno =$anno");
                    $idcnt=$cn[0][0];
                    
                    
                    #********* Insertar Detalle Debito ***********#
                    if(!empty($otros) || $otros !="" || $otros !=0) { 
                        $valorD =$otros *-1;
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                ( `fecha`, `comprobante`,`valor`,
                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorD)),
                                array(":cuenta",$cuentaDebito),   
                                array(":naturaleza",$naturaleza_d),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    if(!empty($rte_otros) || $rte_otros !="" || $rte_otros !=0){
                    #********* Insertar Detalle Retencion ***********#
                        $valorR =$rte_otros; 
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$idcnt),
                                array(":valor",($valorR)),
                                array(":cuenta",$cuenta_rte),   
                                array(":naturaleza",$naturaleza_r),
                                array(":tercero",$tercero),
                                array(":centrocosto",370)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }  else {
                        $valorR =0;
                    }
                    #********* Insertar Detalle Credito *************#
                    if($naturaleza_c ==1){
                        $valorC = ($otros+$valorR) *-1;
                    } else {
                        $valorC = ($otros+$valorR);
                    }
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                    VALUES (:fecha,  :comprobante,:valor, 
                            :cuenta,:naturaleza, :tercero, :centrocosto)";
                    $sql_dato = array(
                            array(":fecha",$fecha),
                            array(":comprobante",$idcnt),
                            array(":valor",($valorC)),
                            array(":cuenta",$cuenta_credito),   
                            array(":naturaleza",$naturaleza_c),
                            array(":tercero",$tercero),
                            array(":centrocosto",370)
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $dtl = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $idcnt");
                    $idDetalle = $dtl[0][0];
                    
                    #************ Buscar Detalles Factura **********#
                    $df = $con->Listar("SELECT * FROM gp_detalle_factura WHERE factura = $idFactura");
                    $valorRecaudo = ($otros+$valorR);
                    $dt = guardarPagoFactura('', $idFactura, $idPago, $valorRecaudo);
                    if($dt>0){
                        $g +=1;
                    }
                }
            }
        }


        
        
        $datos = array("msj"=>" ","rta"=>0, "g"=>$g);
        echo json_encode($datos);
    break;
    case 4:
        $datos = array("msj"=>" ","rta"=>0);
        echo json_encode($datos);
    break;
}
 