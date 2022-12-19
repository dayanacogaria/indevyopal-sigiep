<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#14/08/2018 | Erica G. |Registro Gastos
#18/06/2018 | Erica G. |Archivo Creado
####/################################################################################
require '../Conexion/ConexionPDO.php';                                                     
require '../Conexion/conexion.php';                                                     
require './funcionesPptal.php';
require '../ExcelR/Classes/PHPExcel/IOFactory.php';
ini_set('max_execution_time', 0);
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$anno       = anno($panno);
$action     = $_REQUEST['action'];
$proyecto   = 2147483647;
$cc         = $con->Listar("SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $panno");
$centroc    = $cc[0][0];
#****** Tipo Compañia **** #
$tc         = $con->Listar("SELECT id_unico, tipo_compania FROM gf_tercero WHERE id_unico = $compania");
$tipo_com   = $tc[0][1];
$id_ut      = $_SESSION['usuario_tercero'] ;

switch ($action) {
    #****** Validar Configuracion ****#
    case 1:
        #********* Lectura Archivo ***********#
        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName); 			
        $objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);				
        $total_filas    = $objWorksheet->getHighestRow();					
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        $arrayTerceros  = array();
        $htmlt          = "Terceros No Encontrados";
        $rtat           = 0;
        $arrayConceptos = array();
        $htmlc          = "Conceptos No Encontrados";
        $rtac           = 0;
        $arrayConfig    = array();
        $htmlcf         = "Conceptos No Configurados";
        $rtacf          = 0;
        $arrayValue     = array();
        $htmlval        = "Conceptos Sin Saldo";
        $rtaval         = 0;
        $rtac           = 0;
        $html           = "";
        $rta            = 0;
        $arrayCentros   = array();
        $htmlcentros    = "Centros de costo No Encontrados";
        $rtacc          = 0;
        $arrayProyecto  = array();
        $htmlProyecto   = "Proyectos No Encontrados";
        $rtapr          = 0;
        $arrayCuentaB   = array();
        $htmlBanco      = "Cuentas Bancarias No Encontradas";
        $rtabanco       = 0;
        $arrayIva       = array();
        $htmlIva        = "Conceptos Sin Cuentas IVA Configuradas";
        $rtaIva         = 0;
        #**** Buscar Tipo Comprobante ****#
        # ** Disponibilidad 
        $dis = $con->Listar("SELECT * 
                FROM
                    gf_tipo_comprobante_pptal
                WHERE
                    clasepptal = 14 AND tipooperacion = 1 
                    AND vigencia_actual = 1 AND compania = $compania 
                    AND automatico =1");
        if(count($dis)>0){
            #** Registro
            $reg = $con->Listar("SELECT * 
                FROM
                    gf_tipo_comprobante_pptal
                WHERE
                    clasepptal = 15 AND tipooperacion = 1 
                    AND vigencia_actual = 1 AND compania = $compania 
                    AND automatico =1 ");
            if(count($reg)>0){
                $cxp = $con->Listar("SELECT * 
                FROM
                    gf_tipo_comprobante_pptal
                WHERE
                    clasepptal = 16 AND tipooperacion = 1 
                    AND vigencia_actual = 1 AND compania = $compania 
                    AND automatico =1"); 
                if(count($cxp)>0){
                    # Buscar Cnt CXP
                    $cnc = $con->Listar("SELECT * FROM gf_tipo_comprobante "
                            . "WHERE comprobante_pptal =".$cxp[0][0]);
                    if(count($cnc)>0){ 
                    }else {
                        $html  = "No Se Encontró Comprobante Contable Para Cuenta Por Pagar";
                        $rta   += 1;
                    }
                } else {
                    $html  = "No Se Encontró Comprobante Para Cuenta Por Pagar";
                    $rta   += 1;
                }
            } else {
                $html  = "No Se Encontró Comprobante Para Registro Presupuestal";
                $rta   += 1;
            }
        } else {
            $html  = "No Se Encontró Comprobante Para Disponibilidad Presupuestal";
            $rta   += 1;
        }
        $val =0;
        if($rta==0){
            for ($a = 2; $a <= $total_filas; $a++) {
                $fecha = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
                if(!empty($fecha)){
                    $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fecha);
                    $fecha      = date("Y-m-d",$timestamp);
                    $concepto   = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
                    if($val==0){
                        # ** Validar Terceros ** #
                        $tercero = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();
                        $bt = $con->Listar("SELECT * FROM gf_tercero WHERE numeroidentificacion=$tercero AND compania = $compania");
                        if(count($bt)>0){
                        } else {
                            if(in_array($tercero, $arrayTerceros)) {
                            } else {
                                array_push ( $arrayTerceros , $tercero );
                                $htmlt.='<br/>'.$tercero;
                                $rtat +=1;
                            } 
                        }
                        # ** Validar Centros Costo ** #
                        $ccostoa = $objWorksheet->getCellByColumnAndRow(8, $a)->getCalculatedValue();
                        if(!empty($ccostoa)){
                            $cc = $con->Listar("SELECT * FROM gf_centro_costo WHERE nombre='$ccostoa' and parametrizacionanno= $panno");
                            if(count($cc)>0){
                            } else {
                                if(in_array($ccostoa, $arrayCentros)) {
                                } else {
                                    array_push ( $arrayCentros , $ccostoa );
                                    $htmlcentros.='<br/>'.$ccostoa;
                                    $rtacc +=1;
                                } 
                            }
                        }
                        # ** Validar Proyecto ** #
                        $proyectoa= $objWorksheet->getCellByColumnAndRow(9, $a)->getCalculatedValue();
                            if(!empty($proyectoa)){
                            $pro = $con->Listar("SELECT * FROM gf_proyecto WHERE nombre='$proyectoa' AND compania = $compania");
                            if(count($pro)>0){
                            } else {
                                if(in_array($proyectoa, $arrayProyecto)) {
                                } else {
                                    array_push ( $arrayProyecto , $proyectoa );
                                    $htmlProyecto.='<br/>'.$proyectoa;
                                    $rtapr +=1;
                                } 
                            }
                        }
                        #Validar Banco 
                        $bancoN= $objWorksheet->getCellByColumnAndRow(10, $a)->getCalculatedValue();
                        if(!empty($bancoN)){
                            $ctab = $con->Listar("SELECT c.id_unico, c.naturaleza 
                                    FROM gf_cuenta_bancaria cb 
                                    LEFT JOIN gf_cuenta c ON cb.cuenta = c.id_unico 
                                    WHERE cb.numerocuenta = '$bancoN'  
                                    AND cb.parametrizacionanno = $panno");
                            if(count($ctab)>0){
                            } else {
                                if(in_array($bancoN, $arrayCuentaB)) {
                                } else {
                                    array_push ( $arrayCuentaB , $bancoN );
                                    $htmlBanco.='<br/>'.$bancoN;
                                    $rtabanco +=1;
                                } 
                            }
                        }
                        
                        
                        # ** Validar Conceptos ** #
                        $bc = $con->Listar("SELECT * FROM gf_concepto WHERE parametrizacionanno = $panno AND nombre LIKE '$concepto%'");
                        if(count($bc)>0){
                            #*** Buscar La Configuracion de Concepto Rubro ***#
                            $ccr = $con->Listar("SELECT * FROM gf_concepto_rubro WHERE concepto =".$bc[0][0]);
                            if(count($ccr)>0){
                                #*** Buscar La Configuracion de Rubro Fuente ***#
                                $concepto_r = $ccr[0][0];
                                $rubro      = $ccr[0][1];
                                $rf = $con->Listar("SELECT * FROM gf_rubro_fuente WHERE rubro =$rubro");
                                if(count($rf)>0){
                                    #*** Buscar La Configuracion de Concepto Rubro Cuenta ***#
                                    $crc = $con->Listar("SELECT id_unico, cuenta_iva FROM gf_concepto_rubro_cuenta WHERE concepto_rubro = $concepto_r");
                                    if(count($crc)>0){
                                        #******* Validar Saldo ***********#
                                        #** Si Tipo Compañia =1 
                                        if($tipo_com==1){
                                            $valor = $objWorksheet->getCellByColumnAndRow(7, $a)->getCalculatedValue();
                                            $valdis= valorDisponible($rf[0][0],$fecha);
                                            if($valor<=$valdis){ 
                                            } else {
                                                if(in_array($concepto, $arrayValue)) {
                                                } else {
                                                    array_push ( $arrayValue , $concepto );
                                                    $htmlval.='<br/>'.$concepto;
                                                    $val +=1;
                                                } 
                                            }
                                        }
                                        #* Validar Iva 
                                        $ivaN= $objWorksheet->getCellByColumnAndRow(11, $a)->getCalculatedValue();
                                        if(!empty($ivaN)){
                                            if(!empty($crc[0][1])>0){
                                            } else {
                                                if(in_array($concepto, $arrayIva)) {
                                                } else {
                                                    array_push ( $arrayIva , $concepto );
                                                    $htmlIva.='<br/>'.$concepto;
                                                    $rtaIva +=1;
                                                } 
                                            }
                                        }

                                    } else {
                                        if(in_array($concepto, $arrayConfig)) {
                                        } else {
                                            array_push ( $arrayConfig , $concepto );
                                            $htmlcf  .='<br/>'.$concepto;
                                            $rtacf   += 1;
                                        }
                                    }
                                } else {
                                    if(in_array($concepto, $arrayConfig)) {
                                    } else {
                                        array_push ( $arrayConfig , $concepto );
                                        $htmlcf  .='<br/>'.$concepto;
                                        $rtacf   += 1;
                                    }
                                }
                            } else {
                                if(in_array($concepto, $arrayConfig)) {
                                } else {
                                    array_push ( $arrayConfig , $concepto );
                                    $htmlcf  .='<br/>'.$concepto;
                                    $rtacf   += 1;
                                }
                            }
                        } else {
                            if(in_array($concepto, $arrayConceptos)) {
                            } else {
                                array_push ( $arrayConceptos , $concepto );
                                $htmlc.='<br/>'.$concepto;
                                $rtac +=2;
                            }
                        }   
                    } 
                }            
            }
            
            if($rtat>0){
                $rta    += 1;
                $html   .= $htmlt;
            } elseif($rtac){
                $rta    += 1;
                $html   .= $htmlc;
            }elseif($rtacf){
                $rta    += 1;
                $html   .= $htmlcf;
            } elseif($val>0){
                $html   .=$htmlval;
                $rta    += 1;
            } elseif($rtacc>0){
                $html   .=$htmlcentros;
                $rta    += 1;
            } elseif($rtapr>0){
                $html   .=$htmlProyecto;
                $rta    += 1;
            }elseif($rtabanco>0){
                $html   .=$htmlBanco;
                $rta    += 1;
            }elseif($rtaIva){
                $html   .=$htmlIva;
                $rta    += 1;
            }
                                     
        }
        
        
        $datos = array("rta"=>$rta,"html"=>$html);
        echo json_encode($datos);
    break;
    #******** Guardar Archivo *******#
    case 2:
       
        #***** Buscar Tipo Comprobante *********#
        $dis = $con->Listar("SELECT * 
                FROM
                    gf_tipo_comprobante_pptal
                WHERE
                    clasepptal = 14 AND tipooperacion = 1 
                    AND vigencia_actual = 1 AND compania = $compania 
                    AND automatico =1");
        $tipo_disponibilidad = $dis[0][0];
        #** Registro
        $reg = $con->Listar("SELECT * 
            FROM
                gf_tipo_comprobante_pptal
            WHERE
                clasepptal = 15 AND tipooperacion = 1 
                AND vigencia_actual = 1 AND compania = $compania 
                AND automatico =1 ");
        $tipo_registro = $reg[0][0];
        #** Cuenta Por Pagar
        $cxp = $con->Listar("SELECT * 
            FROM
                gf_tipo_comprobante_pptal
            WHERE
                clasepptal = 16 AND tipooperacion = 1 
                AND vigencia_actual = 1 AND compania = $compania 
                AND automatico =1"); 
        $tipo_cxp = $cxp[0][0];
        # Buscar Cnt CXP
        $cnc = $con->Listar("SELECT * 
            FROM 
                gf_tipo_comprobante 
            WHERE 
                comprobante_pptal =".$cxp[0][0]);
        $tipo_cxpcnt = $cnc[0][0];  
        
        #**  Egreso 
        $egr = $con->Listar("SELECT * 
            FROM
                gf_tipo_comprobante_pptal
            WHERE
                clasepptal = 17 AND tipooperacion = 1 
                AND vigencia_actual = 1 AND compania = $compania 
                AND automatico =1"); 
        $tipo_egr = $egr[0][0];
        # Buscar Cnt EGR
        $cne = $con->Listar("SELECT * 
            FROM 
                gf_tipo_comprobante 
            WHERE 
                comprobante_pptal =".$egr[0][0]);
        $tipo_egrcnt = $cne[0][0]; 
        
         #********* Lectura Archivo ***********#
        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName); 			
        $objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);				
        $total_filas    = $objWorksheet->getHighestRow();					
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        $insertados = 0;
        for ($a = 2; $a <= $total_filas; $a++) {
            $fecha    = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            if(!empty($fecha) || $fecha !=""){
                $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fecha);
                $fecha      = date("Y-m-d",$timestamp);
                #** Factura
                $factura    = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
                #** Tercero 
                $tercero    = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();
                $bt         = $con->Listar("SELECT * FROM gf_tercero WHERE numeroidentificacion=$tercero AND compania = $compania");
                $id_tercero = $bt[0][0];
                #** Concepto
                $concepto   = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
                #** Tipo Contrato
                $contrato   = $objWorksheet->getCellByColumnAndRow(5, $a)->getCalculatedValue();
                # Buscar Tipo Contrato 
                $tcon = $con->Listar("SELECT * FROM gf_clase_contrato WHERE nombre LIKE '%$contrato%'");
                if(count($tcon)>0){
                    $tipo_contrato = $tcon[0][0];
                } else {
                    #Crearlo 
                    $sql_cons ="INSERT INTO `gf_clase_contrato` 
                            ( `nombre`,`tipocontrato`) 
                    VALUES (:nombre,:tipocontrato)";
                    $sql_dato = array(
                            array(":nombre",$contrato),
                            array(":tipocontrato",1)
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato); 
                    $tcon = $con->Listar("SELECT * FROM gf_clase_contrato WHERE nombre LIKE '%$contrato%'");
                    $tipo_contrato = $tcon[0][0];
                }
                #** Descripcion 
                $descripcion= $objWorksheet->getCellByColumnAndRow(6, $a)->getCalculatedValue();
                #** Valor
                $valor      = $objWorksheet->getCellByColumnAndRow(7, $a)->getCalculatedValue();
                #** Centro Costo 
                $ccostoa = $objWorksheet->getCellByColumnAndRow(8, $a)->getCalculatedValue();
                if(!empty($ccostoa)) {
                    $cc = $con->Listar("SELECT * FROM gf_centro_costo WHERE nombre='$ccostoa' AND parametrizacionanno = $panno");
                    $centroc = $cc[0][0];
                }
                $proyectoa= $objWorksheet->getCellByColumnAndRow(9, $a)->getCalculatedValue();
                if(!empty($proyectoa)){
                    $pro = $con->Listar("SELECT * FROM gf_proyecto WHERE nombre='$proyectoa' and compania = $compania");
                    $proyecto = $pro[0][0];
                }
                #***************************************************************************************#
                #   ***    Buscar Configuracion    ***    #
                $bc     = $con->Listar("SELECT * FROM gf_concepto WHERE parametrizacionanno = $panno AND nombre LIKE '$concepto%'");
                $ccr    = $con->Listar("SELECT * FROM gf_concepto_rubro WHERE concepto =".$bc[0][0]);
                $c_rubro= $ccr[0][0];
                $rubro  = $ccr[0][1];
                $rf     = $con->Listar("SELECT * FROM gf_rubro_fuente WHERE rubro =$rubro");
                $rubro_f=$rf[0][0];
                $crc    = $con->Listar("SELECT cd.id_unico, cd.naturaleza,
                        cc.id_unico, cc.naturaleza , 
                        ci.id_unico, ci.naturaleza 
                        FROM gf_concepto_rubro_cuenta crc 
                        LEFT JOIN gf_cuenta cd ON crc.cuenta_debito  = cd.id_unico 
                        LEFT JOIN gf_cuenta cc ON crc.cuenta_credito = cc.id_unico 
                        LEFT JOIN gf_cuenta ci ON crc.cuenta_iva = ci.id_unico 
                        WHERE crc.concepto_rubro=$c_rubro");
                $cuentad = $crc[0][0];
                $nat_deb = $crc[0][1];
                $cuentac = $crc[0][2];
                $nat_cre = $crc[0][3];
                $cuentai = $crc[0][4];
                $nat_iva = $crc[0][5];
                if($tipo_com==1){
                    $valdis= valorDisponible($rubro_f,$fecha);
                } else {
                    $valdis = $valor;
                }
                if($valor <= $valdis){
                    #*** Validar Numero Contrato Disponibilidad
                    $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                        WHERE numerocontrato = '$factura' 
                        AND tipocomprobante = $tipo_disponibilidad 
                        AND tercero = $id_tercero  
                        AND fecha = $fecha     
                        AND parametrizacionanno = $panno");
                    if(count($nc)>0){
                        $id_disponibilidad =$nc[0][0];
                    } else {
                        $num_dis = numero ('gf_comprobante_pptal', $tipo_disponibilidad, $panno);
                        #Guardar Comprobante 
                        $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
                                ( `numero`, `fecha`, 
                                `fechavencimiento`,`descripcion`, 
                                `parametrizacionanno`,`tipocomprobante`,
                                `numerocontrato`,`clasecontrato`, `tercero`,
                                `usuario`, `fecha_elaboracion`) 
                        VALUES (:numero, :fecha, 
                                :fechavencimiento,:descripcion,
                                :parametrizacionanno,:tipocomprobante,
                                :numerocontrato,:clasecontrato,:tercero, 
                                :usuario, :fecha_elaboracion)";
                        $sql_dato = array(
                                array(":numero",$num_dis),
                                array(":fecha",$fecha),
                                array(":fechavencimiento",$fecha),
                                array(":descripcion",$descripcion),
                                array(":parametrizacionanno",$panno),
                                array(":tipocomprobante",$tipo_disponibilidad),
                                array(":numerocontrato",$factura),
                                array(":clasecontrato",$tipo_contrato),
                                array(":tercero",$id_tercero),
                                array(":usuario",$usuario),
                                array(":fecha_elaboracion",date('Y-m-d')),
                                
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato); 
                        if(empty($resp)){
                            $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                                WHERE numero = $num_dis     
                                AND tipocomprobante =$tipo_disponibilidad ");
                            $id_disponibilidad = $bs[0][0];
                        }
                    }
                    #*** Validar Numero Contrato Registro
                    $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                        WHERE numerocontrato = '$factura'  
                        AND tipocomprobante = $tipo_registro 
                        AND tercero = $id_tercero 
                        AND fecha = $fecha  
                        AND parametrizacionanno = $panno");
                    if(count($nc)>0){
                        $id_registro =$nc[0][0];
                    } else {
                        $num_reg = numero ('gf_comprobante_pptal', $tipo_registro, $panno);
                        #Guardar Comprobante 
                        $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
                                ( `numero`, `fecha`, 
                                `fechavencimiento`,`descripcion`, 
                                `parametrizacionanno`,`tipocomprobante`,
                                `numerocontrato`,`clasecontrato`, `tercero`,
                                `usuario`, `fecha_elaboracion`) 
                        VALUES (:numero, :fecha, 
                                :fechavencimiento,:descripcion,
                                :parametrizacionanno,:tipocomprobante,
                                :numerocontrato,:clasecontrato,:tercero,
                                :usuario, :fecha_elaboracion)";
                        $sql_dato = array(
                                array(":numero",$num_reg),
                                array(":fecha",$fecha),
                                array(":fechavencimiento",$fecha),
                                array(":descripcion",$descripcion),
                                array(":parametrizacionanno",$panno),
                                array(":tipocomprobante",$tipo_registro),
                                array(":numerocontrato",$factura),
                                array(":clasecontrato",$tipo_contrato),
                                array(":tercero",$id_tercero),
                                array(":usuario",$usuario),
                                array(":fecha_elaboracion",date('Y-m-d'))
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato); 
                        $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                            WHERE numero = $num_reg     
                            AND tipocomprobante =$tipo_registro ");
                        $id_registro = $bs[0][0];
                    }
                    #*** Validar Numero Contrato Cuenta Pagar
                    $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                            WHERE numerocontrato = '$factura'
                            AND tipocomprobante = $tipo_cxp 
                            AND tercero = $id_tercero 
                            AND fecha = $fecha  
                            AND parametrizacionanno = $panno");
                    if(count($nc)>0){
                        $id_cxp =$nc[0][0];
                        $num_cxp =$nc[0][1];
                    } else {
                        $num_cxp = numero ('gf_comprobante_pptal', $tipo_cxp, $panno);
                        #Guardar Comprobante 
                        $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
                                ( `numero`, `fecha`, 
                                `fechavencimiento`,`descripcion`, 
                                `parametrizacionanno`,`tipocomprobante`,
                                `numerocontrato`,`clasecontrato`, `tercero`,
                                `usuario`, `fecha_elaboracion`) 
                        VALUES (:numero, :fecha, 
                                :fechavencimiento,:descripcion,
                                :parametrizacionanno,:tipocomprobante,
                                :numerocontrato,:clasecontrato,:tercero,
                                :usuario, :fecha_elaboracion)";
                        $sql_dato = array(
                                array(":numero",$num_cxp),
                                array(":fecha",$fecha),
                                array(":fechavencimiento",$fecha),
                                array(":descripcion",$descripcion),
                                array(":parametrizacionanno",$panno),
                                array(":tipocomprobante",$tipo_cxp),
                                array(":numerocontrato",$factura),
                                array(":clasecontrato",$tipo_contrato),
                                array(":tercero",$id_tercero),
                                array(":usuario",$usuario),
                                array(":fecha_elaboracion",date('Y-m-d'))
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato); 
                        $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                            WHERE numero = $num_cxp AND tipocomprobante =$tipo_cxp ");
                        $id_cxp = $bs[0][0];
                    }
                    #*** Validar Numero Contrato Cuenta Pagar Contable
                    $nc = $con->Listar("SELECT * FROM gf_comprobante_cnt 
                            WHERE numerocontrato = '$factura' 
                            AND tipocomprobante = $tipo_cxpcnt 
                            AND tercero = $id_tercero 
                            AND fecha = $fecha   
                            AND parametrizacionanno = $panno");
                    if(count($nc)>0){
                        $id_cxpcnt =$nc[0][0];
                    } else {
                        #Guardar Comprobante 
                        $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                                ( `numero`, `fecha`, 
                                `descripcion`, 
                                `parametrizacionanno`,`tipocomprobante`,
                                `numerocontrato`,`clasecontrato`, `tercero`,
                                `usuario`, `fecha_elaboracion`,
                                `compania`,`estado`) 
                        VALUES (:numero, :fecha, 
                                :descripcion,
                                :parametrizacionanno,:tipocomprobante,
                                :numerocontrato,:clasecontrato,:tercero,
                                :usuario, :fecha_elaboracion, 
                                :compania, :estado )";
                        $sql_dato = array(
                                array(":numero",$num_cxp),
                                array(":fecha",$fecha),
                                array(":descripcion",$descripcion),
                                array(":parametrizacionanno",$panno),
                                array(":tipocomprobante",$tipo_cxpcnt),
                                array(":numerocontrato",$factura),
                                array(":clasecontrato",$tipo_contrato),
                                array(":tercero",$id_tercero),
                                array(":usuario",$usuario),
                                array(":fecha_elaboracion",date('Y-m-d')),
                                array(":compania",$compania),
                                array(":estado",2),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato); 
                        $bs = $con->Listar("SELECT * FROM gf_comprobante_cnt  
                            WHERE numero = $num_cxp AND tipocomprobante =$tipo_cxpcnt ");
                        $id_cxpcnt = $bs[0][0];
                    }
                    $id_egr = 0;
                    $bancoN = $objWorksheet->getCellByColumnAndRow(10, $a)->getCalculatedValue();
                    if(!empty($bancoN)){
                        #** Egreso **#
                        if(!empty($tipo_egr)){
                            #*** Validar Numero Contrato Cuenta Pagar
                            $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                                    WHERE numerocontrato = '$factura'
                                    AND tipocomprobante = $tipo_egr 
                                    AND tercero = $id_tercero 
                                    AND fecha = $fecha   
                                    AND parametrizacionanno = $panno");
                            if(count($nc)>0){
                                $id_egr  =$nc[0][0];
                                $num_egr =$nc[0][1];
                            } else {
                                $num_egr = numero ('gf_comprobante_pptal', $tipo_egr, $panno);
                                #Guardar Comprobante 
                                $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
                                        ( `numero`, `fecha`, 
                                        `fechavencimiento`,`descripcion`, 
                                        `parametrizacionanno`,`tipocomprobante`,
                                        `numerocontrato`,`clasecontrato`, `tercero`,
                                        `usuario`, `fecha_elaboracion`) 
                                VALUES (:numero, :fecha, 
                                        :fechavencimiento,:descripcion,
                                        :parametrizacionanno,:tipocomprobante,
                                        :numerocontrato,:clasecontrato,:tercero,
                                        :usuario, :fecha_elaboracion)";
                                $sql_dato = array(
                                        array(":numero",$num_egr),
                                        array(":fecha",$fecha),
                                        array(":fechavencimiento",$fecha),
                                        array(":descripcion",$descripcion),
                                        array(":parametrizacionanno",$panno),
                                        array(":tipocomprobante",$tipo_egr),
                                        array(":numerocontrato",$factura),
                                        array(":clasecontrato",$tipo_contrato),
                                        array(":tercero",$id_tercero),
                                        array(":usuario",$usuario),
                                        array(":fecha_elaboracion",date('Y-m-d'))
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato); 
                                $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                                    WHERE numero = $num_egr AND tipocomprobante =$tipo_egr ");
                                $id_egr = $bs[0][0];
                            }
                            #*** Validar Numero Contrato Cuenta Pagar Contable
                            $nc = $con->Listar("SELECT * FROM gf_comprobante_cnt 
                                    WHERE numerocontrato = '$factura' 
                                    AND tipocomprobante = $tipo_egrcnt
                                    AND tercero = $id_tercero 
                                    AND parametrizacionanno = $panno");
                            if(count($nc)>0){
                                $id_egrcnt =$nc[0][0];
                            } else {
                                #Guardar Comprobante 
                                $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                                        ( `numero`, `fecha`, 
                                        `descripcion`, 
                                        `parametrizacionanno`,`tipocomprobante`,
                                        `numerocontrato`,`clasecontrato`, `tercero`,
                                        `usuario`, `fecha_elaboracion`,
                                        `compania`,`estado`) 
                                VALUES (:numero, :fecha, 
                                        :descripcion,
                                        :parametrizacionanno,:tipocomprobante,
                                        :numerocontrato,:clasecontrato,:tercero,
                                        :usuario, :fecha_elaboracion, 
                                        :compania, :estado )";
                                $sql_dato = array(
                                        array(":numero",$num_egr),
                                        array(":fecha",$fecha),
                                        array(":descripcion",$descripcion),
                                        array(":parametrizacionanno",$panno),
                                        array(":tipocomprobante",$tipo_egrcnt),
                                        array(":numerocontrato",$factura),
                                        array(":clasecontrato",$tipo_contrato),
                                        array(":tercero",$id_tercero),
                                        array(":usuario",$usuario),
                                        array(":fecha_elaboracion",date('Y-m-d')),
                                        array(":compania",$compania),
                                        array(":estado",2),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato); 
                                $bs = $con->Listar("SELECT * FROM gf_comprobante_cnt  
                                    WHERE numero = $num_egr AND tipocomprobante =$tipo_egrcnt ");
                                $id_egrcnt = $bs[0][0];
                            }
                        }
                    }
                    
                    #*******************************************************************************#
                    # ** Insertar Detalles ** #
                    if(!empty($id_disponibilidad) && !empty($id_registro) && !empty($id_cxp) && !empty($id_cxpcnt)){
                        #*** Insertar Detalles Disponiblidad
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                              ( `descripcion`,`valor`,
                              `comprobantepptal`,`rubrofuente`, `conceptoRubro`,
                              `tercero`, `proyecto`,`centro_costo`) 
                        VALUES (:descripcion, :valor, :comprobantepptal, :rubrofuente, 
                        :conceptoRubro, :tercero, :proyecto, :centro_costo)";
                        $sql_dato = array(
                            array(":descripcion",$descripcion),
                            array(":valor",$valor),
                            array(":comprobantepptal",$id_disponibilidad),
                            array(":rubrofuente",$rubro_f),
                            array(":conceptoRubro",$c_rubro),
                            array(":tercero",$id_tercero),
                            array(":proyecto",$proyecto),
                            array(":centro_costo",$centroc),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        $bs = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal  
                            WHERE comprobantepptal = $id_disponibilidad ");
                        $id_detalle_dis = $bs[0][0];
                        
                        #*** Insertar Detalles Registro
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                              ( `descripcion`,`valor`,
                              `comprobantepptal`,`rubrofuente`, `conceptoRubro`,
                              `tercero`, `proyecto`,`comprobanteafectado`,`centro_costo`) 
                        VALUES (:descripcion, :valor, :comprobantepptal, :rubrofuente, 
                        :conceptoRubro, :tercero, :proyecto,:comprobanteafectado, :centro_costo)";
                        $sql_dato = array(
                            array(":descripcion",$descripcion),
                            array(":valor",$valor),
                            array(":comprobantepptal",$id_registro),
                            array(":rubrofuente",$rubro_f),
                            array(":conceptoRubro",$c_rubro),
                            array(":tercero",$id_tercero),
                            array(":proyecto",$proyecto),
                            array(":comprobanteafectado",$id_detalle_dis),
                            array(":centro_costo",$centroc),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        $bs = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal  
                            WHERE comprobantepptal = $id_registro ");
                        $id_detalle_reg = $bs[0][0];
                        
                        #*** Insertar Detalles Cuenta X Pagar
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                              ( `descripcion`,`valor`,
                              `comprobantepptal`,`rubrofuente`, `conceptoRubro`,
                              `tercero`, `proyecto`,`comprobanteafectado`,`centro_costo`) 
                        VALUES (:descripcion, :valor, :comprobantepptal, :rubrofuente, 
                        :conceptoRubro, :tercero, :proyecto,:comprobanteafectado, :centro_costo)";
                        $sql_dato = array(
                            array(":descripcion",$descripcion),
                            array(":valor",$valor),
                            array(":comprobantepptal",$id_cxp),
                            array(":rubrofuente",$rubro_f),
                            array(":conceptoRubro",$c_rubro),
                            array(":tercero",$id_tercero),
                            array(":proyecto",$proyecto),
                            array(":comprobanteafectado",$id_detalle_reg),
                            array(":centro_costo",$centroc),
                            
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        $bs = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal  
                            WHERE comprobantepptal = $id_cxp ");
                        $id_detalle_cxp = $bs[0][0];
                        #*** Insertar Detalles Cuenta X Pagar Contable
                        #* Debito 
                        if($nat_deb==1){
                            $valor_d = $valor;
                        } else {
                            $valor_d = $valor*-1;
                        }
                        
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                ( `fecha`, `comprobante`,`valor`,
                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`,
                                `detallecomprobantepptal`, `proyecto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto,
                                :detallecomprobantepptal, :proyecto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$id_cxpcnt),
                                array(":valor",($valor_d)),
                                array(":cuenta",$cuentad),   
                                array(":naturaleza",$nat_deb),
                                array(":tercero",$id_tercero),
                                array(":centrocosto",$centroc),
                                array(":detallecomprobantepptal",$id_detalle_cxp),
                                array(":proyecto",$proyecto),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        // Buscar Id_In
                        $id_deb = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante 
                                WHERE comprobante = $id_cxpcnt");
                        $id_deb = $id_deb[0][0];
                        
                        #Validar Iva 
                        $vi  = $objWorksheet->getCellByColumnAndRow(11, $a)->getCalculatedValue();
                        if(!empty($vi)){
                            $valor  += $vi;
                            #* Debito 
                            if($nat_iva==1){
                                $valor_i = $vi;
                            } else {
                                $valor_i = $vi*-1;
                            }
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                    ( `fecha`, `comprobante`,`valor`,
                                    `cuenta`,`naturaleza`,`tercero`, 
                                    `centrocosto`,`detallecomprobantepptal`,`proyecto`) 
                            VALUES (:fecha,  :comprobante,:valor, 
                                    :cuenta,:naturaleza, :tercero, 
                                    :centrocosto,:detallecomprobantepptal, :proyecto)";
                            $sql_dato = array(
                                    array(":fecha",$fecha),
                                    array(":comprobante",$id_cxpcnt),
                                    array(":valor",($valor_i)),
                                    array(":cuenta",$cuentai),   
                                    array(":naturaleza",$nat_iva),
                                    array(":tercero",$id_tercero),
                                    array(":centrocosto",$centroc),
                                    array(":detallecomprobantepptal",$id_detalle_cxp),
                                    array(":proyecto",$proyecto),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                        
                        }
                        #* Credito 
                        #** Buscar Si Hay Detalle Con Cuenta Credito Igual                         
                        if($nat_cre==2){
                            $cns = $con->Listar("SELECT id_unico, valor FROM gf_detalle_comprobante 
                                WHERE comprobante = $id_cxpcnt AND cuenta = $cuentac
                                AND valor > 0");
                            $valor_c = $valor;
                        } else {
                            $cns = $con->Listar("SELECT id_unico, valor  FROM gf_detalle_comprobante 
                                WHERE comprobante = $id_cxpcnt  AND cuenta = $cuentac 
                                AND valor < 0");
                            $valor_c = $valor*-1;
                        }
                        if(count($cns) > 0){
                            $id     = $cns[0][0];
                            $val_a  = $cns[0][1]+$valor_c;
                            $sql_cons ="UPDATE `gf_detalle_comprobante` 
                                  SET `valor`=:valor
                                  WHERE `id_unico`=:id_unico";
                            $sql_dato = array(
                                    array(":valor",$val_a),
                                    array(":id_unico",$id),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            $id_cred = $id;
                        } else {
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                    ( `fecha`, `comprobante`,`valor`,
                                    `cuenta`,`naturaleza`,`tercero`, 
                                    `centrocosto`,`detallecomprobantepptal`,`proyecto`) 
                            VALUES (:fecha,  :comprobante,:valor, 
                                    :cuenta,:naturaleza, :tercero, 
                                    :centrocosto,:detallecomprobantepptal, :proyecto)";
                            $sql_dato = array(
                                    array(":fecha",$fecha),
                                    array(":comprobante",$id_cxpcnt),
                                    array(":valor",($valor_c)),
                                    array(":cuenta",$cuentac),   
                                    array(":naturaleza",$nat_cre),
                                    array(":tercero",$id_tercero),
                                    array(":centrocosto",$centroc),
                                    array(":detallecomprobantepptal",$id_detalle_cxp),
                                    array(":proyecto",$proyecto),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            $id_cred = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante 
                                    WHERE comprobante = $id_cxpcnt");
                            $id_cred = $id_cred[0][0];
                        }
                        
                        if(empty($resp)){
                            $insertados += 1; 
                        }
                        
                        #************Detalles EGR ****************#
                        if($id_egr!=0){
                            #*** Insertar Detalles EGR
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                                  ( `descripcion`,`valor`,
                                  `comprobantepptal`,`rubrofuente`, `conceptoRubro`,
                                  `tercero`, `proyecto`,`comprobanteafectado`,`centro_costo`) 
                            VALUES (:descripcion, :valor, :comprobantepptal, :rubrofuente, 
                            :conceptoRubro, :tercero, :proyecto,:comprobanteafectado, :centro_costo)";
                            $sql_dato = array(
                                array(":descripcion",$descripcion),
                                array(":valor",$valor),
                                array(":comprobantepptal",$id_egr),
                                array(":rubrofuente",$rubro_f),
                                array(":conceptoRubro",$c_rubro),
                                array(":tercero",$id_tercero),
                                array(":proyecto",$proyecto),
                                array(":comprobanteafectado",$id_detalle_cxp),
                                array(":centro_costo",$centroc),

                            );
                            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                            $bs = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal  
                                WHERE comprobantepptal = $id_egr ");
                            $id_detalle_egr = $bs[0][0];
                            #*** Insertar Detalles Egreso Contable
                            #SE TRAEN TODAS LAS CUENTAS DE PASIVO
                            $rowdc = $con->Listar("SELECT detCom.id_unico, 
                                    detCom.valor, detCom.proyecto, 
                                    detCom.cuenta, detCom.naturaleza, 
                                    detCom.centrocosto, 
                                    detCom.detallecomprobantepptal, 
                                    detCom.tercero 
                            FROM gf_detalle_comprobante detCom 
                            LEFT JOIN gf_comprobante_cnt com ON com.id_unico = detCom.comprobante 
                            LEFT JOIN gf_cuenta CT ON detCom.cuenta = CT.id_unico 
                            LEFT JOIN gf_clase_cuenta clacu ON clacu.id_unico = CT.clasecuenta 
                            WHERE detCom.id_unico IN ($id_deb,$id_cred)
                               AND (( com.id_unico = $id_cxpcnt and clacu.id_unico = 4) 
                                    OR ( com.id_unico = $id_cxpcnt and clacu.id_unico = 8) 
                                )
                            AND clacu.id_unico !=20 ");
                            for ($d = 0; $d < count($rowdc); $d++) {
                                
                                $valor      = $valor * -1;
                                $proyecto   = $rowdc[$d][2];
                                $cuenta     = $rowdc[$d][3];
                                $naturaleza = $rowdc[$d][4];
                                $centrocosto = $rowdc[$d][5];
                                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                    ( `fecha`, `comprobante`,`valor`,
                                        `cuenta`,`naturaleza`,`tercero`, `centrocosto`,
                                        `detallecomprobantepptal`, `proyecto`) 
                                VALUES (:fecha,  :comprobante,:valor, 
                                        :cuenta,:naturaleza, :tercero, :centrocosto,
                                        :detallecomprobantepptal, :proyecto)";
                                $sql_dato = array(
                                        array(":fecha",$fecha),
                                        array(":comprobante",$id_egrcnt),
                                        array(":valor",($valor)),
                                        array(":cuenta",$cuenta),   
                                        array(":naturaleza",$naturaleza),
                                        array(":tercero",$id_tercero),
                                        array(":centrocosto",$centrocosto),
                                        array(":detallecomprobantepptal",$id_detalle_egr),
                                        array(":proyecto",$proyecto),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                            }
                            
                            #** agregar Banco
                            $bancoN= $objWorksheet->getCellByColumnAndRow(10, $a)->getCalculatedValue();
                            if(!empty($bancoN)){
                                $cuentaB = $con->Listar("SELECT c.id_unico, c.naturaleza 
                                    FROM gf_cuenta_bancaria cb 
                                    LEFT JOIN gf_cuenta c ON cb.cuenta = c.id_unico 
                                    WHERE cb.numerocuenta = '$bancoN'  
                                    AND cb.parametrizacionanno = $panno");
                                $cuenta = $cuentaB[0][0];
                                $nat_ban= $cuentaB[0][1];
                                //Buscar Valor débito y crédito del comprobante
                                $vc = $con->Listar("SELECT DISTINCT 
                                    cn.id_unico,
                                    ((SELECT IF((ROUND(SUM(dc1.valor),2))> 0,ROUND(SUM(dc1.valor),2),0) 
                                        FROM gf_detalle_comprobante dc1 
                                        LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                        WHERE cn.id_unico = dc1.comprobante 
                                        AND c1.naturaleza=1 AND c1.id_unico != $cuenta 
                                        AND  dc1.valor>0 ) + 
                                     ((SELECT IF((ROUND(SUM(dc1.valor),2))< 0 ,ROUND(SUM(dc1.valor),2),0) 
                                        FROM gf_detalle_comprobante dc1 
                                        LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                        WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND c1.id_unico != $cuenta
                                        AND dc1.valor<0)*-1 )) AS debito, 

                                     (((SELECT IF((ROUND(SUM(dc1.valor),2)) < 0, ROUND(SUM(dc1.valor),2),0) 
                                        FROM gf_detalle_comprobante dc1 
                                        LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                        WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 
                                        AND c1.id_unico != $cuenta AND dc1.valor<0 ) *-1 )+ 
                                     (SELECT IF((ROUND(SUM(dc1.valor),2)) > 0,ROUND(SUM(dc1.valor),2),0) 
                                        FROM gf_detalle_comprobante dc1 
                                        LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                        WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND c1.id_unico != $cuenta 
                                      AND dc1.valor>0 )) AS credito 
                                FROM
                                    gf_comprobante_cnt cn  
                                WHERE cn.id_unico=$id_egrcnt");
                                
                                $vdebito    = $vc[0][1];
                                $vcredito   = $vc[0][2];
                                $dif        = $vdebito - $vcredito;
                                
                                #* Buscar si existe un valor con esa cuenta
                                $ccbr = $con->Listar("SELECT id_unico, IF(valor>0, valor, valor*-1) FROM gf_detalle_comprobante 
                                    WHERE comprobante =$id_egrcnt and cuenta =$cuenta ");
                                if(!empty($ccbr[0][0])){
                                    $valorB = $dif;
                                    if($nat_ban==1){$valorB= $valorB*-1;}
                                    # * Modificar Detalle
                                    $sql_cons ="UPDATE `gf_detalle_comprobante` 
                                            SET `valor`=:valor 
                                            WHERE `id_unico`=:id_unico";
                                    $sql_dato = array(
                                            array(":valor",$valorB),
                                            array(":id_unico",$ccbr[0][0]),
                                    );
                                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                                    
                                } else {
                                    if($nat_ban==1){$valorB= $dif*-1;}
                                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                        ( `fecha`, `comprobante`,`valor`,
                                            `cuenta`,`naturaleza`,`tercero`, `centrocosto`,
                                             `proyecto`) 
                                    VALUES (:fecha,  :comprobante,:valor, 
                                            :cuenta,:naturaleza, :tercero, :centrocosto,
                                             :proyecto)";
                                    $sql_dato = array(
                                            array(":fecha",$fecha),
                                            array(":comprobante",$id_egrcnt),
                                            array(":valor",($valorB)),
                                            array(":cuenta",$cuenta),   
                                            array(":naturaleza",$nat_ban),
                                            array(":tercero",$id_tercero),
                                            array(":centrocosto",$centrocosto),
                                            array(":proyecto",$proyecto),
                                    );
                                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                                }
                            }
                        }
                    }
                }
            }
        }
    echo $insertados;
    break;
    #****** Validar Configuracion Archivo Generado Desde La Clinica Union Temporal****#
    case 3:
        #********* Lectura Archivo ***********#
        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName); 			
        $worksheetList  = $objReader->listWorksheetNames($inputFileName);
        $arrayConceptos = array();
        $htmlc          = "Conceptos No Encontrados";
        $rtac           = 0;
        $arrayConfig    = array();
        $htmlcf         = "Conceptos No Configurados";
        $rtacf          = 0;
        $rtac           = 0;
        $html           = "";
        $rta            = 0;
        #**** Buscar Tipo Comprobante ****#
        # ** Disponibilidad 
        $dis = $con->Listar("SELECT * 
                FROM
                    gf_tipo_comprobante_pptal
                WHERE
                    clasepptal = 14 AND tipooperacion = 1 
                    AND vigencia_actual = 1 AND compania = $compania 
                    AND automatico =1");
        if(count($dis)>0){
            #** Registro
            $reg = $con->Listar("SELECT * 
                FROM
                    gf_tipo_comprobante_pptal
                WHERE
                    clasepptal = 15 AND tipooperacion = 1 
                    AND vigencia_actual = 1 AND compania = $compania 
                    AND automatico =1 ");
            if(count($reg)>0){
                $cxp = $con->Listar("SELECT * 
                FROM
                    gf_tipo_comprobante_pptal
                WHERE
                    clasepptal = 16 AND tipooperacion = 1 
                    AND vigencia_actual = 1 AND compania = $compania 
                    AND automatico =1"); 
                if(count($reg)>0){
                    # Buscar Cnt CXP
                    $cnc = $con->Listar("SELECT * FROM gf_tipo_comprobante WHERE comprobante_pptal =".$cxp[0][0]);
                    if(count($cnc)>0){}else {
                        $html  = "No Se Encontró Comprobante Contable Para Cuenta Por Pagar";
                        $rta   += 1;
                    }
                } else {
                    $html  = "No Se Encontró Comprobante Para Cuenta Por Pagar";
                    $rta   += 1;
                }
            } else {
                $html  = "No Se Encontró Comprobante Para Registro Presupuestal";
                $rta   += 1;
            }
        } else {
            $html  = "No Se Encontró Comprobante Para Disponibilidad Presupuestal";
            $rta   += 1;
        }
        $val =0;
        if($rta==0){
            for ($a = 2; $a <= $total_filas; $a++) {
                $fecha = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
                if(!empty($fecha)){
                    $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fecha);
                    $fecha      = date("Y-m-d",$timestamp);
                    $concepto   = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
                    if($val==0){
                        # ** Validar Conceptos ** #
                        $bc = $con->Listar("SELECT * FROM gf_concepto WHERE parametrizacionanno = $panno AND nombre LIKE '$concepto%'");
                        if(count($bc)>0){
                            #*** Buscar La Configuracion de Concepto Rubro ***#
                            $ccr = $con->Listar("SELECT * FROM gf_concepto_rubro WHERE concepto =".$bc[0][0]);
                            if(count($ccr)>0){
                                #*** Buscar La Configuracion de Rubro Fuente ***#
                                $concepto_r = $ccr[0][0];
                                $rubro      = $ccr[0][1];
                                $rf = $con->Listar("SELECT * FROM gf_rubro_fuente WHERE rubro =$rubro");
                                if(count($rf)>0){
                                    #*** Buscar La Configuracion de Concepto Rubro Cuenta ***#
                                    $crc = $con->Listar("SELECT * FROM gf_concepto_rubro_cuenta WHERE concepto_rubro = $concepto_r");
                                    if(count($crc)>0){
                                        #******* Validar Saldo ***********#
                                        #** Si Tipo Compañia =1 
                                        if($tipo_com==1){
                                            $valor = $objWorksheet->getCellByColumnAndRow(7, $a)->getCalculatedValue();
                                            $valdis= valorDisponible($rf[0][0],$fecha);
                                            if($valor<=$valdis){ 
                                            } else {
                                                if(in_array($concepto, $arrayValue)) {
                                                } else {
                                                    array_push ( $arrayValue , $concepto );
                                                    $htmlval.='<br/>'.$concepto;
                                                    $val +=1;
                                                } 
                                            }
                                        }

                                    } else {
                                        if(in_array($concepto, $arrayConfig)) {
                                        } else {
                                            array_push ( $arrayConfig , $concepto );
                                            $htmlcf  .='<br/>'.$concepto;
                                            $rtacf   += 1;
                                        }
                                    }
                                } else {
                                    if(in_array($concepto, $arrayConfig)) {
                                    } else {
                                        array_push ( $arrayConfig , $concepto );
                                        $htmlcf  .='<br/>'.$concepto;
                                        $rtacf   += 1;
                                    }
                                }
                            } else {
                                if(in_array($concepto, $arrayConfig)) {
                                } else {
                                    array_push ( $arrayConfig , $concepto );
                                    $htmlcf  .='<br/>'.$concepto;
                                    $rtacf   += 1;
                                }
                            }
                        } else {
                            if(in_array($concepto, $arrayConceptos)) {
                            } else {
                                array_push ( $arrayConceptos , $concepto );
                                $htmlc.='<br/>'.$concepto;
                                $rtac +=2;
                            }
                        }   
                    } 
                }            
            }
            
            if($rtat>0){
                $rta    += 1;
                $html   .= $htmlt;
            } elseif($rtac){
                $rta    += 1;
                $html   .= $htmlc;
            }
            elseif($rtacf){
                $rta    += 1;
                $html   .= $htmlcf;
            } elseif($val>0){
                $html   .=$htmlval;
                $rta    += 1;
            }
        }
        
        
        $datos = array("rta"=>$rta,"html"=>$html);
        echo json_encode($datos);
    break;
    #******Interfaz Registro de Gastos******#
    case 4:
        $fecha          = fechaC($_POST['fecha']);
        $concepto_r     = $_POST['conceptoRubro'];
        $rubro_f        = $_POST['rubroFuente'];
        $descripcion    = $_POST['descripcion'];
        $tercero        = $_POST['tercero'];
        $documento      = $_POST['documento'];
        $valor          = $_POST['valor'];
        $id_retenciones = $_POST['id_retenciones'];
        if(empty($_POST['banco'])){ 
            $banco      = NULL;
        } else {
            $banco      = $_POST['banco'];
        }
        $sql_cons ="INSERT INTO `gf_registro_gastos` 
                ( `fecha`, `concepto_rubro`,`rubro_fuente`,
                `descripcion`,`tercero`,`numero_documento`, 
                `valor`,`parametrizacionanno`,`banco`,`retenciones`,
                `usuario_tercero`,`fecha_elaboracion`) 
        VALUES (:fecha,  :concepto_rubro,:rubro_fuente, 
                :descripcion,:tercero, :numero_documento, 
                :valor,:parametrizacionanno, :banco,:retenciones, 
                :usuario_tercero,:fecha_elaboracion)";
        $sql_dato = array(
                array(":fecha",$fecha),
                array(":concepto_rubro",$concepto_r),
                array(":rubro_fuente",$rubro_f),
                array(":descripcion",$descripcion),   
                array(":tercero",$tercero),
                array(":numero_documento",$documento),
                array(":valor",$valor),
                array(":parametrizacionanno",$panno),
                array(":banco",$banco),
                array(":retenciones",$id_retenciones),
                array(":usuario_tercero",$id_ut),
                array(":fecha_elaboracion",date('Y-m-d')),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        #var_dump($resp);
        if(empty($resp)){
            #** Buscar Id Insertado **#
            $in  = $con->Listar("SELECT MAX(id_unico) FROM gf_registro_gastos WHERE parametrizacionanno = $panno");
            $idc = $in[0][0];
            $rta = $idc;
        } else {
            $rta =0;
        }
        echo $rta;
    break;
    #******* Recargar Tabla Registrar Gastos ***********#
    case 5:
        require_once './gf_style_tabla.php';
        if(empty($_REQUEST['fechaI'])&& empty($_REQUEST['fechaF'])) {
        $row = $con->Listar("SELECT rg.id_unico, 
            DATE_FORMAT(rg.fecha, '%d/%m/%Y'), 
            c.nombre, 
            rb.codi_presupuesto, rb.nombre, f.nombre, 
            rg.descripcion, 
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
            rg.numero_documento, 
            rg.valor, 
            CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',cta.codi_cuenta,' - ',cta.nombre, ')'), 
            rg.id_unico, 
            rg.generado, 
            rg.retenciones, 
            rg.disponibilidad 
        FROM gf_registro_gastos rg 
        LEFT JOIN gf_concepto_rubro cr ON rg.concepto_rubro = cr.id_unico  
        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
        LEFT JOIN gf_rubro_fuente rf ON rf.id_unico = rg.rubro_fuente 
        LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
        LEFT JOIN gf_tercero t ON rg.tercero = t.id_unico 
        LEFT JOIN gf_cuenta_bancaria ctb ON rg.banco = ctb.id_unico 
        LEFT JOIN gf_cuenta cta ON ctb.cuenta = cta.id_unico 
        WHERE rg.parametrizacionanno = $panno AND  rg.fecha = '".date('Y-m-d')."'");
        } else {
            $fi = fechaC($_REQUEST['fechaI']);
            $ff = fechaC($_REQUEST['fechaF']);
            $row = $con->Listar("SELECT rg.id_unico, 
            DATE_FORMAT(rg.fecha, '%d/%m/%Y'), 
            c.nombre, 
            rb.codi_presupuesto, rb.nombre, f.nombre, 
            rg.descripcion, 
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
            rg.numero_documento, 
            rg.valor, 
            CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',cta.codi_cuenta,' - ',cta.nombre, ')'), 
            rg.id_unico, 
            rg.generado, 
            rg.retenciones, 
            rg.disponibilidad 
        FROM gf_registro_gastos rg 
        LEFT JOIN gf_concepto_rubro cr ON rg.concepto_rubro = cr.id_unico  
        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
        LEFT JOIN gf_rubro_fuente rf ON rf.id_unico = rg.rubro_fuente 
        LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
        LEFT JOIN gf_tercero t ON rg.tercero = t.id_unico 
        LEFT JOIN gf_cuenta_bancaria ctb ON rg.banco = ctb.id_unico 
        LEFT JOIN gf_cuenta cta ON ctb.cuenta = cta.id_unico 
        WHERE rg.parametrizacionanno = $panno AND rg.fecha BETWEEN '".$fi."' AND '".$ff."'");
        }
        $html  ="";
        $html  .='<div align="center"  class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">';          
        $html  .='<div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">';
        $html  .='<table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">';
        $html  .='<thead>';
        $html  .='<tr>';
        $html  .='<td style="display: none;">Identificador</td>';
        $html  .='<td width="30px"></td>';
        $html  .='<td><strong>Fecha</strong></td>';
        $html  .='<td><strong>Concepto</strong></td>';
        $html  .='<td><strong>Rubro</strong></td>';
        $html  .='<td><strong>Tercero</strong></td>';
        $html  .='<td><strong>N° Documento</strong></td>';
        $html  .='<td><strong>Descripción</strong></td>';
        $html  .='<td><strong>Valor Base</strong></td>';
        $html  .='<td><strong>Valor Neto</strong></td>';
        $html  .='<td><strong>Banco</strong></td>';
        $html  .='<td><strong>Imprimir</strong></td>';
        $html  .='</tr>';
        $html  .='<tr>';
        $html  .='<th style="display: none;">Identificador</th>';
        $html  .='<th width="7%"></th>';
        $html  .='<th>Fecha</th>';
        $html  .='<th>Concepto</th>';
        $html  .='<th>Rubro</th>';
        $html  .='<th>Tercero</th>';
        $html  .='<th>N° Documento</th>';
        $html  .='<th>Descripción</th>';
        $html  .='<th>Valor Base</th>';
        $html  .='<th>Valor Neto</th>';
        $html  .='<th>Banco</th>';
        $html  .='<th>Imprimir</th>';
        $html  .='</tr>';
        $html  .='</thead>';
        $html  .='<tbody>';
        for ($i = 0; $i < count($row); $i++) { 
        $html  .='<tr>';
        $html  .='<td style="display: none;"></td>';
        $html  .='<td>';
        $val    = 0;
        if($row[$i][13]==1){
            $html  .= '<a onclick="cargarDis('.$row[$i][0].')"><i class="glyphicon glyphicon-eye-open" artia-hidden="true"></i></a>';
            if(!empty($row[$i][14])){
                $buscarc =$con->Listar("SELECT comprobante FROM gf_retencion WHERE id_unico IN(".$row[$i][14].")");
                if(!empty($buscarc[0][0])){
                    $idc = $buscarc[0][0];
                    $html  .= '<a onclick="verretenciones('.$idc.')"><i class="glyphicon glyphicon-eye-close" artia-hidden="true"></i></a>';
                }
            }
            if(!empty($row[$i][14])){   
                $rt = $con->Listar("SELECT SUM(valorretencion) FROM gf_retencion WHERE id_unico IN (".$row[$i][14].")");
                if(empty($rt[0][0])){
                    $val =0;
                } else {
                    $val= $rt[0][0];
                }
            }
            $html  .= '<a onclick="eliminar('.$row[$i][0].')"><i class="glyphicon glyphicon-trash" artia-hidden="true"></i></a>';
        } else {
            $html  .= '<input name="seleccion'.$i.'" id="seleccion'.$i.'" type="checkbox" onchange="cambiarV('.$i.')">';
            $html  .= '<input type="hidden" name="id'.$i.'" id="id'.$i.'" value="'.$row[$i][12].'"/>';
            $html  .= '<input type="hidden" name="val_t'.$i.'" id="val_t'.$i.'" value="'.$row[$i][10].'"/>';
            $val    = 0;
            if(!empty($row[$i][14])){   
                $rt = $con->Listar("SELECT SUM(valorretencion) FROM gf_retencion WHERE id_unico IN (".$row[$i][14].")");
                if(empty($rt[0][0])){
                    $val =0;
                } else {
                    $val= $rt[0][0];
                }
                $ids_r = str_replace(',', '.', $row[$i][14]);
                $html  .= '<a onclick="verretencionessg('."'".$ids_r."'".')"><i class="glyphicon glyphicon-eye-close" artia-hidden="true"></i></a>';
            }
            $html  .= '<a onclick="eliminar('.$row[$i][0].')"><i class="glyphicon glyphicon-trash" artia-hidden="true"></i></a>';
            $html  .= '<input type="hidden" name="val_r'.$i.'" id="val_r'.$i.'" value="'.$val.'"/>';
        }
        $html  .='</td>';
        $html  .='<td>'.$row[$i][1].'</td>';
        $html  .='<td>'.ucwords(mb_strtolower($row[$i][2])).'</td>';
        $html  .='<td>'.$row[$i][3].' - '.ucwords(mb_strtolower($row[$i][4])).'</td>';
        $html  .='<td>'.ucwords(mb_strtolower($row[$i][7])).' '.$row[$i][8].'</td>';
        $html  .='<td>'.$row[$i][9].'</td>';
        $html  .='<td>'.$row[$i][6].'</td>';
        $html  .='<td>'.number_format($row[$i][10],2,'.',',').'</td>';
        $html  .='<td>'.number_format($row[$i][10]-$val,2,'.',',').'</td>';
        $html  .='<td>'.ucwords(mb_strtolower($row[$i][11])).'</td>';
        $html  .='<td>';
        $html  .= '<a onclick="imprimir('.$row[$i][0].')"><i class="glyphicon glyphicon-print" artia-hidden="true"></i></a>';
        $html  .='</td>';
        $html  .='</tr>';
        }
        $html  .='</tbody>';
        $html  .='</table>';
        $html  .='</div>';
        $html  .='</div>';   
        echo $html;
    break;
    #** Cargar Conceptos **#
    case 6:
        #*** Consulta Concepto ***#
        $rowc   = $con->Listar("SELECT
            id_unico,
            nombre
          FROM
            gf_concepto 
          WHERE parametrizacionanno = $anno");
        echo '<option value="">Concepto</option>';
        for ($i = 0; $i < count($rowc); $i++) {
            echo '<option value="'.$rowc[$i][0].'">'.ucwords(mb_strtolower($rowc[$i][1])).'</option>';
        }
    break;
    #** Cargar Terceros **#
    case 7:
        #*** Consulta Tercero ***#
        $rowt = $con->Listar("SELECT t.id_unico, IF(CONCAT_WS(' ',
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
        CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
        FROM 
            gf_tercero t 
        WHERE 
            t.compania = $compania");
        echo '<option value="">Tercero</option>';
        for ($i = 0; $i < count($rowt); $i++) {
            echo '<option value="'.$rowt[$i][0].'">'.ucwords(mb_strtolower($rowt[$i][1])).'</option>';
        }
    break;
    #** Cargar Bancos **#
    case 8:
        #** Consulta Bancos **#   
        $rowb = $con->Listar("SELECT  ctb.id_unico,
                CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')'),
                c.id_unico 
            FROM 
                gf_cuenta_bancaria ctb
            LEFT JOIN 
                gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria 
            LEFT JOIN 
                gf_cuenta c ON ctb.cuenta = c.id_unico 
            WHERE 
                ctbt.tercero =$compania  
                AND ctb.parametrizacionanno = $anno 
                AND c.id_unico IS NOT NULL ORDER BY ctb.numerocuenta"); 
        echo '<option value="">Banco</option>';
        for ($i = 0; $i < count($rowb); $i++) {
            echo '<option value="'.$rowb[$i][0].'">'.ucwords(mb_strtolower($rowb[$i][1])).'</option>';
        }
    break;
    #******* Retenciones **********#
    case 9:
        $ids ="0";
        $num                   = 0;
        $_SESSION['ultimoRet'] = "";
        $exito                 = 0;
        $numReng               = $_POST['numReng'];
        if ($numReng == 0) {
            $valorRet[0]     = $_POST['valorRet'];
            $retencionBas[0] = $_POST['retencionBas'];
            $porcenRet       = $_POST['porcenRet'];
            $porcenRet[0]    = $porcenRet / 100;
            $tipoRet[0]      = $_POST['tipoRet'];
            
        } 
        else {
            $valorRet     = $_POST['valorRet'];
            $valorRet     = stripslashes($valorRet);
            $valorRet     = unserialize($valorRet); // Array listo.
            $retencionBas = $_POST['retencionBas'];
            $retencionBas = stripslashes($retencionBas);
            $retencionBas = unserialize($retencionBas); // Array listo.
            $porcenRet    = $_POST['porcenRet'];
            $porcenRet    = stripslashes($porcenRet);
            $porcenRet    = unserialize($porcenRet); // Array listo.
            $tipoRet      = $_POST['tipoRet'];
            $tipoRet      = stripslashes($tipoRet);
            $tipoRet      = unserialize($tipoRet); // Array listo.
            
        }
        for ($i = 0; $i <= $numReng; $i++) {            
            $sqlRet       = 'SELECT porcentajeaplicar 
					FROM gf_tipo_retencion 
					WHERE id_unico = ' . $tipoRet[$i];
            $tipRetencion = $mysqli->query($sqlRet);
            $rowTR        = mysqli_fetch_row($tipRetencion);
            $porcentaje   = $rowTR[0];
            $porcentaje   = $porcentaje / 100;
            $valorR       = (int) $valorRet[$i];
            $sqlRetencion = "INSERT INTO gf_retencion 
                (valorretencion, retencionbase, porcentajeretencion, 
                tiporetencion)  
		VALUES($valorR, $retencionBas[$i], $porcentaje, 
                $tipoRet[$i] )";
            $resultado    = $mysqli->query($sqlRetencion);
            if ($resultado == true) {
                $exito         = 1;
                #Buscar Id Mayor 
                $rtn = $con->Listar("SELECT MAX(id_unico) FROM gf_retencion WHERE tiporetencion=$tipoRet[$i]");
                $ids .=','.$rtn[0][0];
            } 
        } 
        
        if ($exito == 1) {
            echo $ids;
        } 
        else {
            echo 0;
        }
    break;
    #*** Validar Configuración de Conceptos y Tipo Comprobantes ***#
    case 10:
        #** Validar Tipos De Comprobantes **#
        $ids = $_REQUEST['ids'];
        $row = $con->Listar("SELECT * FROM gf_registro_gastos WHERE id_unico IN ($ids)");
        $b   = 0;
        $re  = 0;#* Retenciones 
        $c   = 0;#* Conceptos
        $tc  = 0;
        $rta = 0;        
        $html= "";
        $crt = 0;  
        $arrayConceptos = array();
        $arrayRetencion = array();
        $htmlr ="No se encontraron las cuentas de las siguientes retenciones: ";
        $htmlc ="No se encontró configuración de cuentas para los siguientes conceptos: ";
        $htmltc="No se encontraron los siguientes tipos de comprobantes: ";
        for ($i = 0; $i < count($row); $i++) {
            if(!empty($row[$i]['banco'])){
                $b=1;
            }
            if(!empty($row[$i]['retenciones'])){
                #Validar Retenciones 
                $rt = $con->Listar("SELECT * FROM gf_retencion WHERE id_unico IN (".$row[$i]['retenciones'].")");
                for ($r = 0; $r < count($rt); $r++) {
                    #** Buscar Cuenta **#
                    $tipor = $rt[$r]['tiporetencion'];
                    $ctar = $con->Listar("SELECT cuenta FROM gf_tipo_retencion WHERE id_unico = $tipor");
                    if(count($ctar)>0){
                        if(empty($ctar[0][0])){
                            if(in_array($tipor, $arrayRetencion)) {
                            } else {
                                array_push ( $arrayRetencion , $tipor );
                                $htmlr .='<br/>'.$tipor;
                                $rta   = 1;
                                $re    = 1;
                            }
                        }
                    }else {
                        if(in_array($tipor, $arrayRetencion)) {
                        } else {
                            array_push ( $arrayRetencion , $tipor );
                            $htmlr .='<br/>'.$tipor;
                            $rta   = 1;
                            $re     = 1;
                        }
                    }
                }
                $crt =1;
            }
            $cr = $row[$i]['concepto_rubro'];
            #Validar Concepto Rubro Cuenta 
            $crc = $con->Listar("SELECT * FROM gf_concepto_rubro_cuenta WHERE concepto_rubro =".$row[$i]['concepto_rubro']);
            if(count($crc)>0){
                if(empty($crc[0][0])){
                    if(in_array($cr, $arrayConceptos)) {
                    } else {
                        array_push ( $arrayConceptos , $cr );
                        #Buscar Concepto 
                        $nc = $con->Listar("SELECT c.nombre FROM gf_concepto_rubro cr
                            LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                            WHERE cr.id_unico = $cr");
                        $htmlc .='<br/>'.$nc[0][0];
                        $rta   = 1;
                        $c     = 1;
                    }
                }
            } else {
                if(in_array($cr, $arrayConceptos)) {
                    } else {
                        array_push ( $arrayConceptos , $cr );
                        #Buscar Concepto 
                        $nc = $con->Listar("SELECT c.nombre FROM gf_concepto_rubro cr
                            LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                            WHERE cr.id_unico = $cr");
                        $htmlc .='<br/>'.ucwords(mb_strtolower($nc[0][0]));
                        $rta   = 1;
                        $c     = 1;
                    }
            }
        }
        if($rta==0){
            #*** Validar Tipos De COmprobante **#
            #* Disponibilidad **#
            $tipoc = $_REQUEST['tipoc'];
            $dis = $con->Listar("SELECT * 
                    FROM
                        gf_tipo_comprobante_pptal
                    WHERE id_unico = $tipoc");
            if(empty($dis[0][0])){
                $rta    =1;
                $htmltc .="Disponibilidad"."<br/>";
                $tc     =1;
            } else {
                $tipo_disponibilidad = $dis[0][0];
                #*** Registro ****#
                $reg = $con->Listar("SELECT * 
                    FROM
                        gf_tipo_comprobante_pptal
                    WHERE afectado = $tipo_disponibilidad");
                if(empty($reg[0][0])){
                    $rta    =1;
                    $htmltc .="Registro"."<br/>";
                    $tc     =1;
                } else {
                    $tipo_registro = $reg[0][0];
                    #** Aprobación 
                    $apo = $con->Listar("SELECT * 
                        FROM
                            gf_tipo_comprobante_pptal
                        WHERE afectado = $tipo_registro"); 
                    if(empty($apo[0][0])){
                        $rta    =1;
                        $htmltc .="Aprobación Orden de Pago"."<br/>";
                        $tc     =1;
                    } else {
                        $tipo_apo= $apo[0][0];
                        #** Cuenta Por Pagar
                        $cxp = $con->Listar("SELECT * 
                            FROM
                                gf_tipo_comprobante_pptal
                            WHERE afectado = $tipo_apo"); 
                        if(empty($cxp[0][0])){
                            $rta    =1;
                            $htmltc .="Cuenta Por Pagar"."<br/>";
                            $tc     =1;
                        } else {
                        $tipo_cxp = $cxp[0][0];
                        # Buscar Cnt CXP
                        $cnc = $con->Listar("SELECT * 
                            FROM 
                                gf_tipo_comprobante 
                            WHERE 
                                comprobante_pptal =".$cxp[0][0]);
                        if(empty($cxp[0][0])){
                            $rta =1;
                            $htmltc .="Cuenta Por Pagar Contable"."<br/>";
                            $tc     =1;
                        } else {
                            $tipo_cxpcnt = $cnc[0][0];  
                        }
                    }
                    }
                }
            }
            if($rta ==0){
                if($b==1){
                    #** Egreso
                    $egr = $con->Listar("SELECT * 
                        FROM
                            gf_tipo_comprobante_pptal
                        WHERE afectado = $tipo_cxp"); 
                    if(empty($egr[0][0])){
                        $rta    =1;
                        $htmltc .="Egreso"."<br/>";
                        $tc     =1;
                    } else {
                        $tipo_egr = $egr[0][0];
                        # Buscar Cnt Egreso
                        $egn = $con->Listar("SELECT * 
                            FROM 
                                gf_tipo_comprobante 
                            WHERE 
                                comprobante_pptal =".$egr[0][0]);
                        if(empty($egn[0][0])){
                            $rta    =1;
                            $htmltc .="Egreso Contable"."<br/>";
                            $tc     =1;
                        } else {
                            $tipo_egn = $egn[0][0];  
                        }
                    }
                } 
            }
        } 
        if($rta ==0){
            #Validar Si hay retenciones y si algun tipo de comprobante
            if($crt>0){
                $cnt = $con->Listar("SELECT * FROM gf_tipo_comprobante WHERE id_unico = $tipo_cxpcnt");
                if($cnt[0]['retencion']==1){
                } else {
                    if($b==1){
                        #Buscar Si Egreso Valida
                        $cnt = $con->Listar("SELECT * FROM gf_tipo_comprobante WHERE id_unico = $tipo_egn");
                        if($cnt[0]['retencion']==1){
                        } else {
                            $rta    =1;
                            $htmltc .="Comprobante Para Realizar Retención"."<br/>";
                            $tc     =1;
                        }
                    } else {
                        $rta    =1;
                        $htmltc .="Comprobante Para Realizar Retención"."<br/>";
                        $tc     =1;
                    }
                }
            }
        }
        if($re>0){
           $html .=$htmlr; 
        }
        if($c>0){
           $html .=$htmlc; 
        }
        if($tc>0){
           $html .=$htmltc; 
        }
        $datos = array("rta"=>$rta,"html"=>$html);
        echo json_encode($datos);
    break;
    #*** Guardar Movimientos Registo Gastos ***#
    case 11:
        #** Tipos De Comprobantes **#
        #* Disponibilidad **#
        $tipoc = $_REQUEST['tipoc'];
        $dis = $con->Listar("SELECT * 
            FROM
                gf_tipo_comprobante_pptal
            WHERE id_unico = $tipoc");
        $tipo_disponibilidad = $dis[0][0];
        #*** Registro ****#
        $reg = $con->Listar("SELECT * 
            FROM
                gf_tipo_comprobante_pptal
            WHERE
                afectado = $tipo_disponibilidad");
        $tipo_registro = $reg[0][0];
        #** Aprobación 
        $apo = $con->Listar("SELECT * 
            FROM
                gf_tipo_comprobante_pptal
            WHERE afectado = $tipo_registro"); 
        $tipo_apo= $apo[0][0];
                        
        #** Cuenta Por Pagar
        $cxp = $con->Listar("SELECT * 
            FROM
                gf_tipo_comprobante_pptal
            WHERE
                afectado = $tipo_apo"); 
        $tipo_cxp = $cxp[0][0];
        # Buscar Cnt CXP
        $cnc = $con->Listar("SELECT * 
            FROM 
                gf_tipo_comprobante 
            WHERE 
                comprobante_pptal =".$cxp[0][0]);
        $tipo_cxpcnt = $cnc[0][0];  
        #** Egreso
        $egr = $con->Listar("SELECT * 
            FROM
                gf_tipo_comprobante_pptal
            WHERE
                afectado = $tipo_cxp"); 
        $tipo_egr = $egr[0][0];
        # Buscar Cnt Egreso
        $egn = $con->Listar("SELECT * 
            FROM 
                gf_tipo_comprobante 
            WHERE 
                comprobante_pptal =".$egr[0][0]);
        $tipo_egn = $egn[0][0];  
        $insertados =0;
        #***    Movimientos    ***#
        $ids = $_REQUEST['ids'];
        $row = $con->Listar("SELECT * FROM gf_registro_gastos WHERE id_unico IN ($ids)");
        for ($i = 0; $i < count($row); $i++) {
            $factura        = $row[$i]['numero_documento'];
            $id_tercero     = $row[$i]['tercero'];
            $fecha          = $row[$i]['fecha'];
            $descripcion    = $row[$i]['descripcion'];
            $valor          = $row[$i]['valor'];
            $banco          = $row[$i]['banco'];
            $c_rubro        = $row[$i]['concepto_rubro'];
            $rubro_f        = $row[$i]['rubro_fuente'];
            $tipo_contrato  = 20;
            #Concepto Rubro Cuenta 
            $crc = $con->Listar("SELECT cd.id_unico, cd.naturaleza,
                cc.id_unico, cc.naturaleza , cd.clasecuenta, cc.clasecuenta 
                FROM gf_concepto_rubro_cuenta crc 
                LEFT JOIN gf_cuenta cd ON crc.cuenta_debito  = cd.id_unico 
                LEFT JOIN gf_cuenta cc ON crc.cuenta_credito = cc.id_unico
                WHERE crc.concepto_rubro =".$row[$i]['concepto_rubro']);
            $cuentad = $crc[0][0];
            $nat_deb = $crc[0][1];
            $cuentac = $crc[0][2];
            $nat_cre = $crc[0][3];
            $clased  = $crc[0][4];
            $clasec  = $crc[0][5];
            #************* Disponibilidad *************#
            if(empty($row[$i]['numero_documento'])){
                $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                WHERE numerocontrato = 0 
                AND tipocomprobante = 0 
                AND tercero = 0  
                AND parametrizacionanno = 0");
            } else {
                $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                WHERE numerocontrato = '$factura'  
                AND tipocomprobante = $tipo_disponibilidad 
                AND tercero = $id_tercero  
                AND parametrizacionanno = $panno");
            }
            if(count($nc)>0){
                $id_disponibilidad =$nc[0][0];
            } else {
                $num_dis = numero ('gf_comprobante_pptal', $tipo_disponibilidad, $panno);
                #Guardar Comprobante 
                $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
                        ( `numero`, `fecha`, 
                        `fechavencimiento`,`descripcion`, 
                        `parametrizacionanno`,`tipocomprobante`,
                        `numerocontrato`,`clasecontrato`, `tercero`,
                        `usuario`, `fecha_elaboracion`) 
                VALUES (:numero, :fecha, 
                        :fechavencimiento,:descripcion,
                        :parametrizacionanno,:tipocomprobante,
                        :numerocontrato,:clasecontrato,:tercero, 
                        :usuario, :fecha_elaboracion)";
                $sql_dato = array(
                        array(":numero",$num_dis),
                        array(":fecha",$fecha),
                        array(":fechavencimiento",$fecha),
                        array(":descripcion",$descripcion),
                        array(":parametrizacionanno",$panno),
                        array(":tipocomprobante",$tipo_disponibilidad),
                        array(":numerocontrato",$factura),
                        array(":clasecontrato",$tipo_contrato),
                        array(":tercero",$id_tercero),
                        array(":usuario",$usuario),
                        array(":fecha_elaboracion",date('Y-m-d')),

                );
                $resp = $con->InAcEl($sql_cons,$sql_dato); 
                if(empty($resp)){
                    $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                        WHERE numero = $num_dis     
                        AND tipocomprobante =$tipo_disponibilidad ");
                    $id_disponibilidad = $bs[0][0];
                }
            }
            #************* Registro *************#
            if(empty($row[$i]['numero_documento'])){
                $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                    WHERE numerocontrato = 0 
                    AND tipocomprobante = 0 
                    AND tercero = 0  
                    AND parametrizacionanno = 0");
            } else {
                $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                    WHERE numerocontrato = '$factura'   
                    AND tipocomprobante = $tipo_registro 
                    AND tercero = $id_tercero 
                    AND parametrizacionanno = $panno");
            }
            if(count($nc)>0){
                $id_registro =$nc[0][0];
            } else {
                $num_reg = numero ('gf_comprobante_pptal', $tipo_registro, $panno);
                #Guardar Comprobante 
                $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
                        ( `numero`, `fecha`, 
                        `fechavencimiento`,`descripcion`, 
                        `parametrizacionanno`,`tipocomprobante`,
                        `numerocontrato`,`clasecontrato`, `tercero`,
                        `usuario`, `fecha_elaboracion`) 
                VALUES (:numero, :fecha, 
                        :fechavencimiento,:descripcion,
                        :parametrizacionanno,:tipocomprobante,
                        :numerocontrato,:clasecontrato,:tercero,
                        :usuario, :fecha_elaboracion)";
                $sql_dato = array(
                        array(":numero",$num_reg),
                        array(":fecha",$fecha),
                        array(":fechavencimiento",$fecha),
                        array(":descripcion",$descripcion),
                        array(":parametrizacionanno",$panno),
                        array(":tipocomprobante",$tipo_registro),
                        array(":numerocontrato",$factura),
                        array(":clasecontrato",$tipo_contrato),
                        array(":tercero",$id_tercero),
                        array(":usuario",$usuario),
                        array(":fecha_elaboracion",date('Y-m-d'))
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato); 
                $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                    WHERE numero = $num_reg     
                    AND tipocomprobante =$tipo_registro ");
                $id_registro = $bs[0][0];
            }
            
            #************* Aprobación *************#
            if(empty($row[$i]['numero_documento'])){
                $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                    WHERE numerocontrato = 0 
                    AND tipocomprobante = 0 
                    AND tercero = 0  
                    AND parametrizacionanno = 0");
            } else {
                $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                    WHERE numerocontrato = '$factura'   
                    AND tipocomprobante = $tipo_apo 
                    AND tercero = $id_tercero 
                    AND parametrizacionanno = $panno");
            }
            if(count($nc)>0){
                $id_aprobacion =$nc[0][0];
            } else {
                $num_reg = numero ('gf_comprobante_pptal', $tipo_apo, $panno);
                #Guardar Comprobante 
                $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
                        ( `numero`, `fecha`, 
                        `fechavencimiento`,`descripcion`, 
                        `parametrizacionanno`,`tipocomprobante`,
                        `numerocontrato`,`clasecontrato`, `tercero`,
                        `usuario`, `fecha_elaboracion`) 
                VALUES (:numero, :fecha, 
                        :fechavencimiento,:descripcion,
                        :parametrizacionanno,:tipocomprobante,
                        :numerocontrato,:clasecontrato,:tercero,
                        :usuario, :fecha_elaboracion)";
                $sql_dato = array(
                        array(":numero",$num_reg),
                        array(":fecha",$fecha),
                        array(":fechavencimiento",$fecha),
                        array(":descripcion",$descripcion),
                        array(":parametrizacionanno",$panno),
                        array(":tipocomprobante",$tipo_apo),
                        array(":numerocontrato",$factura),
                        array(":clasecontrato",$tipo_contrato),
                        array(":tercero",$id_tercero),
                        array(":usuario",$usuario),
                        array(":fecha_elaboracion",date('Y-m-d'))
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato); 
                $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                    WHERE numero = $num_reg     
                    AND tipocomprobante =$tipo_apo ");
                $id_aprobacion = $bs[0][0];
            }
            #************* Cuenta Por Pagar *************#
            if(empty($row[$i]['numero_documento'])){
                $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                    WHERE numerocontrato = 0 
                    AND tipocomprobante = 0 
                    AND tercero = 0  
                    AND parametrizacionanno = 0");
            } else {
                $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                        WHERE numerocontrato = '$factura' 
                        AND tipocomprobante = $tipo_cxp 
                        AND tercero = $id_tercero 
                        AND parametrizacionanno = $panno");
            }
            if(count($nc)>0){
                $id_cxp  =$nc[0][0];
                $num_cxp =$nc[0][1];
            } else {
                $num_cxp = numero ('gf_comprobante_pptal', $tipo_cxp, $panno);
                #Guardar Comprobante 
                $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
                        ( `numero`, `fecha`, 
                        `fechavencimiento`,`descripcion`, 
                        `parametrizacionanno`,`tipocomprobante`,
                        `numerocontrato`,`clasecontrato`, `tercero`,
                        `usuario`, `fecha_elaboracion`) 
                VALUES (:numero, :fecha, 
                        :fechavencimiento,:descripcion,
                        :parametrizacionanno,:tipocomprobante,
                        :numerocontrato,:clasecontrato,:tercero,
                        :usuario, :fecha_elaboracion)";
                $sql_dato = array(
                        array(":numero",$num_cxp),
                        array(":fecha",$fecha),
                        array(":fechavencimiento",$fecha),
                        array(":descripcion",$descripcion),
                        array(":parametrizacionanno",$panno),
                        array(":tipocomprobante",$tipo_cxp),
                        array(":numerocontrato",$factura),
                        array(":clasecontrato",$tipo_contrato),
                        array(":tercero",$id_tercero),
                        array(":usuario",$usuario),
                        array(":fecha_elaboracion",date('Y-m-d'))
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato); 
                $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                    WHERE numero = $num_cxp AND tipocomprobante =$tipo_cxp ");
                $id_cxp = $bs[0][0];
            }
            #************* Cuenta Por Pagar Contable *************#
            if(empty($row[$i]['numero_documento'])){
                $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                    WHERE numerocontrato = 0 
                    AND tipocomprobante = 0 
                    AND tercero = 0  
                    AND parametrizacionanno = 0");
            } else {
                $nc = $con->Listar("SELECT * FROM gf_comprobante_cnt 
                        WHERE numerocontrato = '$factura'  
                        AND tipocomprobante = $tipo_cxpcnt 
                        AND tercero = $id_tercero 
                        AND parametrizacionanno = $panno");
            }
            if(count($nc)>0){
                $id_cxpcnt =$nc[0][0];
            } else {
                #Guardar Comprobante 
                $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                        ( `numero`, `fecha`, 
                        `descripcion`, 
                        `parametrizacionanno`,`tipocomprobante`,
                        `numerocontrato`,`clasecontrato`, `tercero`,
                        `usuario`, `fecha_elaboracion`,
                        `compania`,`estado`) 
                VALUES (:numero, :fecha, 
                        :descripcion,
                        :parametrizacionanno,:tipocomprobante,
                        :numerocontrato,:clasecontrato,:tercero,
                        :usuario, :fecha_elaboracion, 
                        :compania, :estado )";
                $sql_dato = array(
                        array(":numero",$num_cxp),
                        array(":fecha",$fecha),
                        array(":descripcion",$descripcion),
                        array(":parametrizacionanno",$panno),
                        array(":tipocomprobante",$tipo_cxpcnt),
                        array(":numerocontrato",$factura),
                        array(":clasecontrato",$tipo_contrato),
                        array(":tercero",$id_tercero),
                        array(":usuario",$usuario),
                        array(":fecha_elaboracion",date('Y-m-d')),
                        array(":compania",$compania),
                        array(":estado",2),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato); 
                $bs = $con->Listar("SELECT * FROM gf_comprobante_cnt  
                    WHERE numero = $num_cxp AND tipocomprobante =$tipo_cxpcnt ");
                $id_cxpcnt = $bs[0][0];
            }
            
            if(!empty($banco)){
                #************* Egreso *************#
                if(empty($row[$i]['numero_documento'])){
                    $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                        WHERE numerocontrato = 0 
                        AND tipocomprobante = 0 
                        AND tercero = 0  
                        AND parametrizacionanno = 0");
                } else {
                    $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                            WHERE numerocontrato = '$factura' 
                            AND tipocomprobante = $tipo_egr  
                            AND tercero = $id_tercero 
                            AND parametrizacionanno = $panno");
                }
                if(count($nc)>0){
                    $id_egr  =$nc[0][0];
                    $num_egr =$nc[0][1];
                } else {
                    $num_egr = numero ('gf_comprobante_pptal', $tipo_egr, $panno);
                    #Guardar Comprobante 
                    $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
                            ( `numero`, `fecha`, 
                            `fechavencimiento`,`descripcion`, 
                            `parametrizacionanno`,`tipocomprobante`,
                            `numerocontrato`,`clasecontrato`, `tercero`,
                            `usuario`, `fecha_elaboracion`) 
                    VALUES (:numero, :fecha, 
                            :fechavencimiento,:descripcion,
                            :parametrizacionanno,:tipocomprobante,
                            :numerocontrato,:clasecontrato,:tercero,
                            :usuario, :fecha_elaboracion)";
                    $sql_dato = array(
                            array(":numero",$num_egr),
                            array(":fecha",$fecha),
                            array(":fechavencimiento",$fecha),
                            array(":descripcion",$descripcion),
                            array(":parametrizacionanno",$panno),
                            array(":tipocomprobante",$tipo_egr),
                            array(":numerocontrato",$factura),
                            array(":clasecontrato",$tipo_contrato),
                            array(":tercero",$id_tercero),
                            array(":usuario",$usuario),
                            array(":fecha_elaboracion",date('Y-m-d'))
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato); 
                    $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                        WHERE numero = $num_egr AND tipocomprobante =$tipo_egr ");
                    $id_egr = $bs[0][0];
                }
                #************* Egreso Contable *************#
                if(empty($row[$i]['numero_documento'])){
                    $nc = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                        WHERE numerocontrato = 0 
                        AND tipocomprobante = 0 
                        AND tercero = 0  
                        AND parametrizacionanno = 0");
                } else {
                    $nc = $con->Listar("SELECT * FROM gf_comprobante_cnt 
                            WHERE numerocontrato = '$factura'  
                            AND tipocomprobante = $tipo_egn 
                            AND tercero = $id_tercero 
                            AND parametrizacionanno = $panno");
                }
                if(count($nc)>0){
                    $id_egn =$nc[0][0];
                } else {
                    #Guardar Comprobante 
                    $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                            ( `numero`, `fecha`, 
                            `descripcion`, 
                            `parametrizacionanno`,`tipocomprobante`,
                            `numerocontrato`,`clasecontrato`, `tercero`,
                            `usuario`, `fecha_elaboracion`,
                            `compania`,`estado`) 
                    VALUES (:numero, :fecha, 
                            :descripcion,
                            :parametrizacionanno,:tipocomprobante,
                            :numerocontrato,:clasecontrato,:tercero,
                            :usuario, :fecha_elaboracion, 
                            :compania, :estado )";
                    $sql_dato = array(
                            array(":numero",$num_egr),
                            array(":fecha",$fecha),
                            array(":descripcion",$descripcion),
                            array(":parametrizacionanno",$panno),
                            array(":tipocomprobante",$tipo_egn),
                            array(":numerocontrato",$factura),
                            array(":clasecontrato",$tipo_contrato),
                            array(":tercero",$id_tercero),
                            array(":usuario",$usuario),
                            array(":fecha_elaboracion",date('Y-m-d')),
                            array(":compania",$compania),
                            array(":estado",2),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato); 
                    $bs = $con->Listar("SELECT * FROM gf_comprobante_cnt  
                        WHERE numero = $num_egr AND tipocomprobante =$tipo_egn ");
                    $id_egn = $bs[0][0];
                }
            }
            #************************** Detalles **********************************#
            if(!empty($id_disponibilidad) && !empty($id_registro) && !empty($id_aprobacion) && !empty($id_cxp) && !empty($id_cxpcnt)){
                #*** Insertar Detalles Disponiblidad
                $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                      ( `descripcion`,`valor`,
                      `comprobantepptal`,`rubrofuente`, `conceptoRubro`,
                      `tercero`, `proyecto`) 
                VALUES (:descripcion, :valor, :comprobantepptal, :rubrofuente, 
                :conceptoRubro, :tercero, :proyecto)";
                $sql_dato = array(
                    array(":descripcion",$descripcion),
                    array(":valor",$valor),
                    array(":comprobantepptal",$id_disponibilidad),
                    array(":rubrofuente",$rubro_f),
                    array(":conceptoRubro",$c_rubro),
                    array(":tercero",$id_tercero),
                    array(":proyecto",2147483647),
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                $bs = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal  
                    WHERE comprobantepptal = $id_disponibilidad ");
                $id_detalle_dis = $bs[0][0];

                #*** Insertar Detalles Registro
                $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                      ( `descripcion`,`valor`,
                      `comprobantepptal`,`rubrofuente`, `conceptoRubro`,
                      `tercero`, `proyecto`,`comprobanteafectado`) 
                VALUES (:descripcion, :valor, :comprobantepptal, :rubrofuente, 
                :conceptoRubro, :tercero, :proyecto,:comprobanteafectado)";
                $sql_dato = array(
                    array(":descripcion",$descripcion),
                    array(":valor",$valor),
                    array(":comprobantepptal",$id_registro),
                    array(":rubrofuente",$rubro_f),
                    array(":conceptoRubro",$c_rubro),
                    array(":tercero",$id_tercero),
                    array(":proyecto",2147483647),
                    array(":comprobanteafectado",$id_detalle_dis),
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                $bs = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal  
                    WHERE comprobantepptal = $id_registro ");
                $id_detalle_reg = $bs[0][0];
                
                #*** Insertar Detalles Aprobación
                $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                      ( `descripcion`,`valor`,
                      `comprobantepptal`,`rubrofuente`, `conceptoRubro`,
                      `tercero`, `proyecto`,`comprobanteafectado`) 
                VALUES (:descripcion, :valor, :comprobantepptal, :rubrofuente, 
                :conceptoRubro, :tercero, :proyecto,:comprobanteafectado)";
                $sql_dato = array(
                    array(":descripcion",$descripcion),
                    array(":valor",$valor),
                    array(":comprobantepptal",$id_aprobacion),
                    array(":rubrofuente",$rubro_f),
                    array(":conceptoRubro",$c_rubro),
                    array(":tercero",$id_tercero),
                    array(":proyecto",2147483647),
                    array(":comprobanteafectado",$id_detalle_reg),
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                $bs = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal  
                    WHERE comprobantepptal = $id_aprobacion ");
                $id_detalle_apo = $bs[0][0];
                
                #*** Insertar Detalles Cuenta X Pagar
                $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                      ( `descripcion`,`valor`,
                      `comprobantepptal`,`rubrofuente`, `conceptoRubro`,
                      `tercero`, `proyecto`,`comprobanteafectado`) 
                VALUES (:descripcion, :valor, :comprobantepptal, :rubrofuente, 
                :conceptoRubro, :tercero, :proyecto,:comprobanteafectado)";
                $sql_dato = array(
                    array(":descripcion",$descripcion),
                    array(":valor",$valor),
                    array(":comprobantepptal",$id_cxp),
                    array(":rubrofuente",$rubro_f),
                    array(":conceptoRubro",$c_rubro),
                    array(":tercero",$id_tercero),
                    array(":proyecto",2147483647),
                    array(":comprobanteafectado",$id_detalle_apo),
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                $bs = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal  
                    WHERE comprobantepptal = $id_cxp ");
                $id_detalle_cxp = $bs[0][0];
                #*** Insertar Detalles Cuenta X Pagar Contable
                #******* Validar Si Hay Retenciones ******#
                $rtc =0;
                if(!empty($row[$i]['retenciones'])){
                    $cnt = $con->Listar("SELECT * FROM gf_tipo_comprobante WHERE id_unico = $tipo_cxpcnt");
                    if($cnt[0]['retencion']==1){
                        $rtc =1;
                    }
                }
                $valr  = 0;    
                if($rtc==1){
                    #****** Actualizar Retenciones e Insertarlas *******#
                    $rowrt = $con->Listar("SELECT * FROM gf_retencion WHERE id_unico IN(".$row[$i]['retenciones'].")");
                    for ($rt = 0; $rt < count($rowrt); $rt++) {
                        $valr +=$rowrt[$rt]['valorretencion'];
                        #**Buscar Cuenta**#
                        $ctar = $con->Listar("SELECT c.id_unico, c.naturaleza FROM gf_tipo_retencion tr 
                            LEFT JOIN gf_cuenta c ON tr.cuenta = c.id_unico 
                            WHERE tr.id_unico =".$rowrt[$rt]['tiporetencion']);
                        if($ctar[0][1]==1){
                            $valorrc = $rowrt[$rt]['valorretencion'];
                        }  else {
                            $valorrc = $rowrt[$rt]['valorretencion'];
                        }
                        $cuenta = $ctar[0][0];
                        $nt     = $ctar[0][1];
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                ( `fecha`, `comprobante`,`valor`,
                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`, `proyecto`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto, :proyecto)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$id_cxpcnt),
                                array(":valor",($valorrc)),
                                array(":cuenta",$cuenta),   
                                array(":naturaleza",$nt),
                                array(":tercero",$id_tercero),
                                array(":centrocosto",$centroc),
                                array(":proyecto",2147483647),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        #***** Actualizar Retenciones 
                        $sql_cons ="UPDATE `gf_retencion` 
                                SET `comprobante`=:comprobante 
                                WHERE `id_unico`=:id_unico";
                        $sql_dato = array(
                                array(":comprobante",$id_cxpcnt),
                                array(":id_unico",$rowrt[$rt]['id_unico']),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                }
                #****** Debito ********#
                if($nat_deb==1){
                    $valor_d = $valor;
                } else {
                    $valor_d = $valor*-1;
                }
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `centrocosto`,
                        `detallecomprobantepptal`,`proyecto`) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto,:detallecomprobantepptal,
                        :proyecto)";
                $sql_dato = array(
                        array(":fecha",$fecha),
                        array(":comprobante",$id_cxpcnt),
                        array(":valor",($valor_d)),
                        array(":cuenta",$cuentad),   
                        array(":naturaleza",$nat_deb),
                        array(":tercero",$id_tercero),
                        array(":centrocosto",$centroc),
                        array(":detallecomprobantepptal",$id_detalle_cxp),
                        array(":proyecto",2147483647),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                #* Credito 
                #** Buscar Si Hay Detalle Con Cuenta Credito Igual 
                if($nat_cre==2){
                    $cns = $con->Listar("SELECT id_unico, valor FROM gf_detalle_comprobante 
                        WHERE comprobante = $id_cxpcnt AND cuenta = $cuentac
                        AND valor > 0");
                    $valor_c = $valor-$valr;
                } else {
                    $cns = $con->Listar("SELECT id_unico, valor  FROM gf_detalle_comprobante 
                        WHERE comprobante = $id_cxpcnt  AND cuenta = $cuentac 
                        AND valor < 0");
                    $valor_c = ($valor*-1)+$valr;
                }
                if(count($cns) > 0){
                    $id     = $cns[0][0];
                    $val_a  = $cns[0][1]+$valor_c;
                    $sql_cons ="UPDATE `gf_detalle_comprobante` 
                          SET `valor`=:valor
                          WHERE `id_unico`=:id_unico";
                    $sql_dato = array(
                            array(":valor",$val_a),
                            array(":id_unico",$id),
                    );
                } else {
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, 
                            `centrocosto`,`detallecomprobantepptal`, `proyecto`) 
                    VALUES (:fecha,  :comprobante,:valor, 
                            :cuenta,:naturaleza, :tercero, 
                            :centrocosto,:detallecomprobantepptal, :proyecto)";
                    $sql_dato = array(
                            array(":fecha",$fecha),
                            array(":comprobante",$id_cxpcnt),
                            array(":valor",($valor_c)),
                            array(":cuenta",$cuentac),   
                            array(":naturaleza",$nat_cre),
                            array(":tercero",$id_tercero),
                            array(":centrocosto",$centroc),
                            array(":detallecomprobantepptal",$id_detalle_cxp),
                            array(":proyecto",2147483647),
                    );
                }
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                if(empty($resp)){
                    $insertados += 1; 
                }
                #*************** Detalles Egreso ****************#
                if(!empty($banco)){
                    if(!empty($id_egn) && !empty($id_egr)){
                        #*** Insertar Detalles Egreso 
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                              ( `descripcion`,`valor`,
                              `comprobantepptal`,`rubrofuente`, `conceptoRubro`,
                              `tercero`, `proyecto`,`comprobanteafectado`) 
                        VALUES (:descripcion, :valor, :comprobantepptal, :rubrofuente, 
                        :conceptoRubro, :tercero, :proyecto,:comprobanteafectado)";
                        $sql_dato = array(
                            array(":descripcion",$descripcion),
                            array(":valor",$valor),
                            array(":comprobantepptal",$id_egr),
                            array(":rubrofuente",$rubro_f),
                            array(":conceptoRubro",$c_rubro),
                            array(":tercero",$id_tercero),
                            array(":proyecto",2147483647),
                            array(":comprobanteafectado",$id_detalle_cxp),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        $bs = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal  
                            WHERE comprobantepptal = $id_egr ");
                        $id_detalle_egr = $bs[0][0];
                        #*** Insertar Detalles Cuenta X Pagar Contable
                        #******* Validar Si Hay Retenciones ******#
                        $rtc =0;
                        if(!empty($row[$i]['retenciones'])){
                            $cnt = $con->Listar("SELECT * FROM gf_tipo_comprobante WHERE id_unico = $tipo_egn");
                            if($cnt[0]['retencion']==1){
                                $rtc =1;
                            }
                        }
                        $valr  = 0;    
                        if($rtc==1){
                            #****** Actualizar Retenciones e Insertarlas *******#
                            $rowrt = $con->Listar("SELECT * FROM gf_retencion WHERE id_unico IN(".$row[$i]['retenciones'].")");
                            for ($rt = 0; $rt < count($rowrt); $rt++) {
                                $valr +=$rowrt[$rt]['valorretencion'];
                                #**Buscar Cuenta**#
                                $ctar = $con->Listar("SELECT c.id_unico, c.naturaleza FROM gf_tipo_retencion tr 
                                    LEFT JOIN gf_cuenta c ON tr.cuenta = c.id_unico 
                                    WHERE tr.id_unico =".$rowrt[$rt]['tiporetencion']);
                                if($ctar==1){
                                    $valorrc = $rowrt[$rt]['valorretencion']*-1;
                                }  else {
                                    $valorrc = $rowrt[$rt]['valorretencion'];
                                }
                                $cuenta = $ctar[0][0];
                                $nt     = $ctar[0][1];
                                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                        ( `fecha`, `comprobante`,`valor`,
                                        `cuenta`,`naturaleza`,`tercero`, `centrocosto`, `proyecto`) 
                                VALUES (:fecha,  :comprobante,:valor, 
                                        :cuenta,:naturaleza, :tercero, :centrocosto, :proyecto)";
                                $sql_dato = array(
                                        array(":fecha",$fecha),
                                        array(":comprobante",$id_egn),
                                        array(":valor",($valorrc)),
                                        array(":cuenta",$cuenta),   
                                        array(":naturaleza",$nt),
                                        array(":tercero",$id_tercero),
                                        array(":centrocosto",$centroc),
                                        array(":proyecto",2147483647),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                                #***** Actualizar Retenciones 
                                $sql_cons ="UPDATE `gf_retencion` 
                                        SET `comprobante`=:comprobante 
                                        WHERE `id_unico`=:id_unico";
                                $sql_dato = array(
                                        array(":id_unico",$rowrt[$rt]['id_unico']),
                                        array(":comprobante",$id_egn),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                            }
                        }
                        #****** Debito ********#
                        if ($clased==4 || $clased==8){
                            if($nat_deb==1){
                                $valor_d = $valor;
                            } else {
                                $valor_d = $valor*-1;
                            }
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                    ( `fecha`, `comprobante`,`valor`,
                                    `cuenta`,`naturaleza`,`tercero`, `centrocosto`,
                                    `detallecomprobantepptal`, `proyecto`) 
                            VALUES (:fecha,  :comprobante,:valor, 
                                    :cuenta,:naturaleza, :tercero, :centrocosto,
                                    :detallecomprobantepptal, :proyecto)";
                            $sql_dato = array(
                                    array(":fecha",$fecha),
                                    array(":comprobante",$id_egn),
                                    array(":valor",($valor_d)),
                                    array(":cuenta",$cuentad),   
                                    array(":naturaleza",$nat_deb),
                                    array(":tercero",$id_tercero),
                                    array(":centrocosto",$centroc),
                                    array(":detallecomprobantepptal",$id_detalle_egr),
                                    array(":proyecto",2147483647),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                        }
                        if ($clasec==4 || $clasec ==8){
                            if($nat_cre==2){
                                $valor_c = $valor;
                            } else {
                                $valor_c = ($valor*-1);
                            }
                            $valor_c=$valor_c*-1;                        
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                    ( `fecha`, `comprobante`,`valor`,
                                    `cuenta`,`naturaleza`,`tercero`, 
                                    `centrocosto`,`detallecomprobantepptal`, `proyecto`) 
                            VALUES (:fecha,  :comprobante,:valor, 
                                    :cuenta,:naturaleza, :tercero, 
                                    :centrocosto,:detallecomprobantepptal, :proyecto)";
                            $sql_dato = array(
                                    array(":fecha",$fecha),
                                    array(":comprobante",$id_egn),
                                    array(":valor",($valor_c)),
                                    array(":cuenta",$cuentac),   
                                    array(":naturaleza",$nat_cre),
                                    array(":tercero",$id_tercero),
                                    array(":centrocosto",$centroc),
                                    array(":detallecomprobantepptal",$id_detalle_egr),
                                    array(":proyecto",2147483647),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                        }

                        #* Credito 
                        #********* Buscar Cuenta Banco *************#
                        $dtb = $con->Listar("SELECT c.id_unico, c.naturaleza 
                            FROM gf_cuenta_bancaria cb 
                            LEFT JOIN gf_cuenta c ON cb.cuenta = c.id_unico 
                            WHERE cb.id_unico = $banco");
                        $cuentab = $dtb[0][0];
                        $naturb  = $dtb[0][1];
                        #** Buscar Si ya Existe 
                        $exb = $con->Listar("SELECT id_unico, valor FROM gf_detalle_comprobante 
                            WHERE cuenta = $cuentab AND comprobante = $id_egn");
                        if(count($exb)>0){
                            $vb = $exb[0][1];
                            if($vb<0){
                                $vb =$vb*-1;
                            }
                            $vb +=$valor;
                            if($naturb==1){
                                $valorb = ($vb*-1)+$valr;
                            } else {
                                $valorb = ($vb-$valr);
                            }
                            $sql_cons ="UPDATE `gf_detalle_comprobante` 
                                SET `valor`=:valor 
                                WHERE `id_unico` =:id_unico";
                            $sql_dato = array(
                                    array(":valor",$valorb),
                                    array(":id_unico",$exb[0][0]),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                        } else {
                            if($naturb==1){
                                $valorb = ($valor*-1)+$valr;
                            } else {
                                $valorb = ($valor-$valr);
                            }
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                    ( `fecha`, `comprobante`,`valor`,
                                    `cuenta`,`naturaleza`,`tercero`, 
                                    `centrocosto`, `proyecto`) 
                            VALUES (:fecha,  :comprobante,:valor, 
                                    :cuenta,:naturaleza, :tercero, 
                                    :centrocosto, :proyecto)";
                            $sql_dato = array(
                                    array(":fecha",$fecha),
                                    array(":comprobante",$id_egn),
                                    array(":valor",($valorb)),
                                    array(":cuenta",$cuentab),   
                                    array(":naturaleza",$naturb),
                                    array(":tercero",$id_tercero),
                                    array(":centrocosto",$centroc),
                                    array(":proyecto",2147483647),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                        }    
                    }
                }
                if(empty($resp)){
                    $insertados += 1; 
                    if(empty($id_egr)){
                        $id_egr = NULL;
                    }
                    #** Actualizar **#
                    $sql_cons ="UPDATE `gf_registro_gastos` 
                        SET `generado`=:generado, 
                            `disponibilidad`=:disponibilidad,
                            `cxp`=:cxp,
                            `egreso`=:egreso 
                        WHERE `id_unico`=:id_unico";
                    $sql_dato = array(
                            array(":generado",1),
                            array(":disponibilidad",$id_disponibilidad),
                            array(":id_unico",$row[$i]['id_unico']),
                            array(":cxp",$id_cxp),
                            array(":egreso",$id_egr),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    #var_dump($resp);
                }
            }  
        }
        echo $insertados;
        #$datos = array("rta"=>$rta,"html"=>$html);
        #echo json_encode($datos); 
    break;
    #*** Verificar Eliminar Registo Gastos ****#
    case 12:
        $id = $_REQUEST['id'];
        $rta =0;
        $gn = $con->Listar("SELECT * FROM gf_registro_gastos WHERE id_unico = $id");
        if(!empty($gn[0]['disponibilidad'])){
            $rta=$gn[0]['disponibilidad'];
        }
        echo $rta;
    break;
    #** Eliminar Sin Movimiento Registo Gastos **#
    case 13:
        $id     = $_REQUEST['id'];
        $gn     = $con->Listar("SELECT * FROM gf_registro_gastos WHERE id_unico = $id");
        $e      = 0;
        $rta    = 0;
        if(!empty($gn[0]['retenciones'])){
            $sql_cons ="DELETE FROM `gf_retencion` 
                    WHERE `id_unico` IN (:id_unico)";
            $sql_dato = array(
                    array(":id_unico",$gn[0]['retenciones']),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            if(!empty($resp)){
                $e=1;
            }
        }
        if($e==0){
            $sql_cons ="DELETE FROM `gf_registro_gastos` 
                    WHERE `id_unico` =:id_unico";
            $sql_dato = array(
                    array(":id_unico",$id),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            if(!empty($resp)){
                $rta =1;
            }
        } else {
            $rta =1;
        }
        echo $rta ;
    break;
    #** Ver Movimientos Registro Gastos **#
    case 14:
        $id = $_REQUEST['id'];
        #* Buscar Egreso y Cuenta Por Pagar *#
        $html ="";
        $rta  =0;
        $idm  =0;
        $row = $con->Listar("SELECT * FROM gf_registro_gastos WHERE id_unico = $id");
        if(empty($row[0]['egreso'])){
            $idm =$row[0]['cxp'];
        } else {
            $html .='<div style="text-align: left">';
            $html .='<a onclick="cargarcxp('.$row[0]['cxp'].')"><i class="glyphicon glyphicon-eye-open" artia-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;Cuenta Por Pagar'.'<br/>';
            $html .='<a onclick="cargaregreso('.$row[0]['egreso'].')"><i class="glyphicon glyphicon-eye-open" artia-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;Egreso';
            $html .='</div>';
            $rta  = 1;
        }
        $datos = array("rta"=>$rta,"html"=>$html, "idm"=>$idm);
        echo json_encode($datos);
    break;
    #** Buscar Egreso **#
    case 15:
        $pptal  = $_REQUEST['pptal'];
        $row = $con->Listar("SELECT cn.id_unico 
            FROM gf_comprobante_pptal cp 
            LEFT JOIN gf_tipo_comprobante tc ON cp.tipocomprobante =tc.comprobante_pptal 
            LEFT JOIN gf_comprobante_cnt cn ON cp.numero = cn.numero AND cn.tipocomprobante = tc.id_unico 
            WHERE cp.id_unico =$pptal");
        $cnt = $row[0][0];
        $_SESSION['id_comp_pptal_GE']   = $pptal;
        $_SESSION['nuevo_GE']           = 1;
        $_SESSION['cntEgreso']          = $cnt;
        $_SESSION['idCompCnt']          = $cnt;
        $_SESSION['idCompCntV']         = $cnt;
        echo 1;
    break;
}