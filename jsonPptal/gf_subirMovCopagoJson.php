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


switch ($action){
    #   **************** Validar Terceros , Conceptos Y Cuentas ****************    #
    case 1:
        $arrayTerceros = array();
        $arrayCuentasC  = array();
        $arrayCuentasD  = array();
        $rta =0;
        $htmlcc ="Cuentas Crédito No Encontradas";
        $htmlcd ="Cuentas Débito No Encontradas";
        $htmlt  ="Terceros No Encontrados";
        
        for ($a = 2; $a <= $total_filas1; $a++) {
            $nit  = $objWorksheet1->getCellByColumnAndRow(3, $a)->getCalculatedValue();
            $terc = $objWorksheet1->getCellByColumnAndRow(4, $a)->getCalculatedValue();
            $cuentad  = $objWorksheet1->getCellByColumnAndRow(10, $a)->getCalculatedValue();
            $cuentac  = $objWorksheet1->getCellByColumnAndRow(11, $a)->getCalculatedValue();
            $ctad = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta ='$cuentad' AND parametrizacionanno = $anno");
            if(count($ctad)==0){
                if(in_array($cuentad, $arrayCuentasD)) {

                } else {
                    array_push ( $arrayCuentasD , $cuentad );
                    $rta =1;
                    $htmlcd .=$cuentad.'<br/>';
                }
            }
            $ctac = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta ='$cuentac' AND parametrizacionanno = $anno");
            if(count($ctac)==0){
                if(in_array($cuentac, $arrayCuentasC)) {

                } else {
                    array_push ( $arrayCuentasC , $cuentac );
                    $rta =1;
                    $htmlcc .=$cuentac.'<br/>';
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
            $html .=$htmlcd.'<br/>';
            $html .=$htmlcc.'<br/>';
            $html .=$htmlt.'<br/>';
        }
         $datos = array("msj"=>$html,"rta"=>$rta);
        echo json_encode($datos);
    break;
    #   **************** Subir Terceros , Conceptos Y Cuentas ****************    #
    case 2:
        # ********* Subir Recaudos ¨*************#
        $dl = $con->Listar("TRUNCATE TABLE copagos");
        $cn =0;
        for ($a = 2; $a <= $total_filas1; $a++) {
            $fecha      = $objWorksheet1->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fecha);
            $fecha      = date("Y-m-d",$timestamp);
            $factura    = $objWorksheet1->getCellByColumnAndRow(2, $a)->getCalculatedValue();
            $nit        = $objWorksheet1->getCellByColumnAndRow(3, $a)->getCalculatedValue();
            $div = explode("-", $nit);
            $nit = trim($div[0]);
            $nit = str_replace('.', '', $nit);
            $entidad    = $objWorksheet1->getCellByColumnAndRow(4, $a)->getCalculatedValue();
            $descrip    = $objWorksheet1->getCellByColumnAndRow(8, $a)->getCalculatedValue();
            $valor      = $objWorksheet1->getCellByColumnAndRow(9, $a)->getCalculatedValue();
            $cuentad    = $objWorksheet1->getCellByColumnAndRow(10, $a)->getCalculatedValue();
            $cuentac    = $objWorksheet1->getCellByColumnAndRow(11, $a)->getCalculatedValue();
            
            
            
            $sql_cons ="INSERT INTO `copagos` 
                    ( `fecha`, `factura`,`nit`, `tercero`,`descripcion`,
                    `valor`,`cuenta_d`,`cuenta_c`) 
            VALUES  ( :fecha, :factura, :nit, :tercero, :descripcion,
                    :valor, :cuenta_d, :cuenta_c)";
            $sql_dato = array(
                    array(":fecha",$fecha),
                    array(":factura",$factura),
                    array(":nit",$nit),
                    array(":tercero",$entidad),
                    array(":descripcion",$descrip),
                    array(":valor",$valor),
                    array(":cuenta_d",$cuentad),
                    array(":cuenta_c",$cuentac),
            );
            
            $resp = $con->InAcEl($sql_cons,$sql_dato);
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
        $rta =0;
        $msj =0;
        
        #Buscar Facturas Que No Esten Creadas 
        $fact = $con->Listar("SELECT * FROM copagos");
        for ($i = 0; $i < count($fact); $i++) {
            $numero     = $fact[$i][2];
            $nit        = $fact[$i][3];
            $fecha      = $fact[$i][1];
            $valor      = $fact[$i][6];
            $cuentad    = $fact[$i][7];
            $cuentac    = $fact[$i][8];
            #Buscar Tercero 
            $tr = $con->Listar("SELECT * FROM gf_tercero WHERE numeroidentificacion = $nit");
            $tercero = $tr[0][0];
            #******* Buscar Si La Factura Existe *********#
            $fc = $con->Listar("SELECT * FROM gp_factura WHERE numero_factura = '$numero' ");
            $descripcion = 'Recaudo Factura '.$numero;
            if($fc>0){
                $idFactura = $fc[0][0];
                $msj +=1;
                #************************************************************************************#
                # ***** Buscar Recaudos***** #
                $tipo           = 2;
                $tipocnt        = 23;
                
                if(!empty($valor) || $valor !="" || $valor !=0){
                #****** Crear Comprobante Recaudo *****#
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
                        array(":banco",16), 
                        array(":estado",1),
                        array(":parametrizacionanno",$anno),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                $pg = $con->Listar("SELECT * FROM gp_pago WHERE numero_pago = '$numero_pago' AND tipo_pago = $tipo AND parametrizacionanno = $anno");
                $idPago = $pg[0][0];

                #********* Insertar  CNT**********#
                #****Guardar Comprobante***#
                $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                        ( `numero`, `fecha`, `tipocomprobante`,`compania`,
                        `parametrizacionanno`,`tercero`, `usuario`, `fecha_elaboracion`,`descripcion`  ) 
                VALUES (:numero, :fecha,  :tipocomprobante,:compania, 
                        :parametrizacion_anno, :tercero, :usuario,:fecha_elaboracion, :descripcion)";
                $sql_dato = array(
                        array(":numero",$numero_pago),
                        array(":fecha",$fecha),
                        array(":tipocomprobante",$tipocnt),
                        array(":compania",$compania),   
                        array(":parametrizacion_anno",$anno),
                        array(":tercero",$tercero),
                        array(":usuario",$usuario),
                        array(":fecha_elaboracion",$fechaE),
                        array(":descripcion",$descripcion),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);

                #************* Insertar Detalles CNT *************#
                $cn = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE numero = '$numero_pago' AND tipocomprobante = $tipocnt AND parametrizacionanno =$anno");
                $idcnt=$cn[0][0];

                
                
                #********* Cuenta Débito ***********#
                $ctad = $con->Listar("SELECT id_unico, naturaleza FROM gf_cuenta WHERE codi_cuenta ='$cuentad' AND parametrizacionanno = $anno");
                $cuenta_debito  = $ctad[0][0];
                $naturaleza_d   = $ctad[0][1];
                #********* Cuenta Crédito ***********#
                $ctac = $con->Listar("SELECT id_unico, naturaleza FROM gf_cuenta WHERE codi_cuenta ='$cuentac' AND parametrizacionanno = $anno");
                $cuenta_credito = $ctac[0][0];
                $naturaleza_c   = $ctac[0][1];
                
                #********* Insertar Detalle Debito ***********#
                if($naturaleza_d==1){
                    $valord = $valor;
                } else {
                    $valord = $valor *-1;
                }
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto)";
                $sql_dato = array(
                        array(":fecha",$fecha),
                        array(":comprobante",$idcnt),
                        array(":valor",($valord)),
                        array(":cuenta",$cuenta_debito),   
                        array(":naturaleza",$naturaleza_d),
                        array(":tercero",$tercero),
                        array(":centrocosto",370)
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);

                #********* Insertar Detalle Credito *************#
                if($naturaleza_c ==1){
                    $valorc = $valor *-1;
                } else {
                    $valorc = $valor;
                }
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto)";
                $sql_dato = array(
                        array(":fecha",$fecha),
                        array(":comprobante",$idcnt),
                        array(":valor",($valorc)),
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
                echo "SELECT * FROM gp_detalle_factura WHERE factura = $idFactura";
                $valorRecaudo = $valor;
                for ($r = 0; $r < count($df); $r++) {
                    if($valorRecaudo >0) {
                        #Buscar Si El Detalle Tiene Recaudo 
                        $det = $df[$r][0];
                        $valorDetalle = $df[$r][8];
                        $valorGuardar =0;
                        $rcd = $con->Listar("SELECT * FROM gp_detalle_pago WHERE detalle_factura =$det");
                        #echo "SELECT * FROM gp_detalle_pago WHERE detalle_factura =$det";
                         if(count($rcd)==0){
                            if($valorRecaudo <= $valorDetalle){
                                $valorGuardar = $valorRecaudo;
                            } else {
                                $valorGuardar =$valorDetalle;
                            }
                        } else {
                            $vcd = $con->Listar("SELECT SUM(valor) FROM gp_detalle_pago WHERE detalle_factura =$det");
                            $rc = $vcd[0][0];
                            if(!empty($rc) || $rc!=0){
                                if($valorDetalle==$rc){
                                    $valorGuardar =$valorRecaudo;
                                } else {
                                    if($valorRecaudo <= $valorDetalle){
                                        $valorGuardar = $valorRecaudo;
                                    } else {
                                        $valorGuardar =$valorDetalle - $rc;
                                    }
                                }
                            } else {
                                if($valorRecaudo <= $valorDetalle){
                                    $valorGuardar = $valorRecaudo;
                                } else {
                                    $valorGuardar =$valorDetalle - $rc;
                                }
                            }

                        }

                        $valorRecaudo = $valorRecaudo -$valorGuardar;
                        if($valorGuardar>0){
                            #********* Guardar Detalle Pago *********#
                            $sql_cons ="INSERT INTO `gp_detalle_pago` 
                                    ( `detalle_factura`, `valor`,`iva`,`impoconsumo`,
                                    `ajuste_peso`,`saldo_credito`, `pago`,`detallecomprobante`) 
                            VALUES (:detalle_factura,  :valor,:iva, :impoconsumo,
                                    :ajuste_peso,:saldo_credito, :pago, :detallecomprobante)";
                            $sql_dato = array(
                                    array(":detalle_factura",$det),
                                    array(":valor",$valorGuardar),
                                    array(":iva",(0)),
                                    array(":impoconsumo",0),   
                                    array(":ajuste_peso",0),
                                    array(":saldo_credito",0),
                                    array(":pago",$idPago),
                                    array(":detallecomprobante",$idDetalle)
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            var_dump($resp);
                        }
                    }
                }                   
            }
            }
        }
        $datos = array("msj"=>$msj,"rta"=>0);
        echo json_encode($datos);
    break;
    case 4:
        $datos = array("msj"=>" ","rta"=>0);
        echo json_encode($datos);
    break;
}
 