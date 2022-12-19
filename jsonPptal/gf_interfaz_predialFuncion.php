<?php
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
require '../jsonPptal/funcionesPptal.php';
$con = new ConexionPDO();
session_start();
$parm_anno = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$anno       = anno($parm_anno);
ini_set('max_execution_time', 0);
switch ($_REQUEST['action']){
    #*********** Guardar Interfaz Predial ****************#
    case (1):
        
        $guardados = 0;
        $rta =1;
        $fechaI = ($_POST['fechaI']);
        $fechaF = ($_POST['fechaF']);
        #****** Buscar Tipo Comprobante Interfaz ******#
        $comp = $con->Listar("SELECT id_unico, comprobante_pptal, tipo_comp_hom 
                FROM gf_tipo_comprobante 
                WHERE interfaz_predial =1");
        $tipocomprobante = $comp[0][0];
        $tipocomprobantepptal = $comp[0][1];
        $tipocomprobantecausacion = $comp[0][2];
        if(count($comp)>0){
            #Buscar Pagos
                $pgs = $con->Listar("SELECT DISTINCT id_unico, tipopago 
                        FROM gr_pago_predial 
                        WHERE parametrizacionanno =$parm_anno AND (fechapago >='$fechaI' AND fechapago<='$fechaF') 
                        ORDER BY fechapago ASC");
           
            for ($pg = 0; $pg < count($pgs); $pg++) { 
                if($pgs[$pg][1]==1 || $pgs[$pg][1]==4 ||$pgs[$pg][1]==7 ) {
                $idPago = $pgs[$pg][0];
                ##******** Buscar Centro De Costo ********#
                $cc = $con->Listar("SELECT * FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $parm_anno");
                $centrocosto = $cc[0][0];
                #****** Buscar Datos Básicos Para Comprobante ******#
                $cm = $con->Listar("SELECT DISTINCT 
                        fc.numero , p.codigo_catastral, 
                        pr.nombres, pg.fechapago, 
                        pg.banco  
                    FROM  gr_detalle_pago_predial dpp 
                    LEFT JOIN gr_pago_predial pg ON dpp.pago = pg.id_unico 
                    LEFT JOIN gr_detalle_factura_predial dfp ON dpp.detallefactura = dfp.id_unico               
                    LEFT JOIN gr_factura_predial fc ON dfp.factura = fc.id_unico 
                    LEFT JOIN gp_predio1 p ON fc.predio = p.id_unico 
                    LEFT JOIN gp_tercero_predio tp On p.id_unico = tp.predio 
                    LEFT JOIN gr_propietarios pr ON tp.tercero = pr.id_unico 
                    WHERE dpp.pago =$idPago AND tp.propietario = 0;");

                #*********** Crear Comprobantes *****************#
                #Consultamos el ultimo numero de acuerdo al tipo de comprobante
                $numeroCnt=$con->Listar("select max(numero) from gf_comprobante_cnt "
                        . "where tipocomprobante=$tipocomprobante AND parametrizacionanno = $parm_anno ");
                if(!empty($numeroCnt[0][0])){
                    $numeroC=$numeroCnt[0][0]+1;
                }else{
                    $numeroC=$anno.'000001';
                }
                #Descripción del comprobante
                $descripcion= '"Comprobante de Recaudo Predial Factura N°:'.$cm[0][0].' Predio:'.$cm[0][1].' Propietario: '.$cm[0][2].'"';
                $fecha  = $cm[0][3];
                #Insertamos el comprobante
                $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,tipocomprobante, 
                        parametrizacionanno,tercero,estado,compania) 
                        values('$numeroC','$fecha',$descripcion,$tipocomprobante, 
                        $parm_anno,2,'1',$compania)";
                $resultInsertC=$mysqli->query($sqlInsertC);
                #* Consultamos el ultimo comprobante ingresado
                $idCnt=$con->Listar("select max(id_unico) from gf_comprobante_cnt 
                        WHERE tipocomprobante=$tipocomprobante and numero=$numeroC");
                $id_cnt = $idCnt[0][0];

                #*********** Comprobante Pptal ***********#
                #* Validamos que el tipo de comprobante no venga vacio
                if(!empty($tipocomprobantepptal)){
                    $tipopptal = $tipocomprobantepptal;
                    #* Consultamos el ultmo número registrado de acuerdo al tipo de comprobante pptal
                    $numeroP=$con->Listar("select max(numero) from gf_comprobante_pptal "
                            . "where tipocomprobante=$tipopptal AND parametrizacionanno = $parm_anno");
                    #* Validamos si el valor consultado viene vacio que inicialize el conteo, de lo contrarop que sume uno al valor obtenido
                    if(!empty($numeroP[0][0])){
                        $numeroPp=$numeroP[0][0]+1;
                    }else{
                        $numeroPp=$anno.'000001';
                    }
                    #* Insertamos los datos en comprobante pptal
                    $insertPptal="insert into "
                            . "gf_comprobante_pptal(numero,fecha,fechavencimiento,"
                            . "descripcion,parametrizacionanno,tipocomprobante,tercero,estado,responsable) "
                            . "values('$numeroPp','$fecha','$fecha',$descripcion,"
                            . "$parm_anno,$tipopptal,2,'1',2)";
                    $resultInsertPptal=$mysqli->query($insertPptal);
                    #* Consultamos el ultimo comprobante pptal insertado
                    $idPPAL=$con->Listar("select id_unico from gf_comprobante_pptal where tipocomprobante=$tipopptal and numero=$numeroPp");
                    $id_pptal = $idPPAL[0][0];
                }   
                #************ Registrar Comprobante Causación***************#
                if(!empty($tipocomprobantecausacion)){
                    #* Consultamos el ultimo numero de acuerdo al tipo de comprobante
                    $tipocau =$tipocomprobantecausacion;
                    $numeroCa=$con->Listar("select max(numero) from gf_comprobante_cnt "
                            . "where tipocomprobante=$tipocau AND parametrizacionanno = $parm_anno ");
                    if(!empty($numeroCa[0][0])){
                        $numeroCausacion=$numeroCa[0][0]+1;
                    }else{
                        $numeroCausacion=$anno.'000001';
                    }
                    #* Descripción del comprobante
                    $descripcion= '"Comprobante de Causación Recaudo Predial Factura N°: '.$cm[0][0].' Predio:'.$cm[0][1].' Propietario: '.$cm[0][2].'"';
                    #* Insertamos el comprobante
                    $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,"
                            . "tipocomprobante,parametrizacionanno,tercero,estado,compania) "
                            . "values('$numeroCausacion','$fecha',$descripcion,$tipocau,"
                            . "$parm_anno,2,'1',$compania)";
                    $resultInsertC=$mysqli->query($sqlInsertC);
                    #* Consultamos el ultimo comprobante ingresado
                    $idCau=$con->Listar("select max(id_unico) from gf_comprobante_cnt where tipocomprobante=$tipocau and numero=$numeroCausacion");
                    $id_causacion = $idCau[0][0];

                }


                $row = $con->Listar("SELECT DISTINCT dpp.id_unico ,cp.id_concepto, cp.anno, dpp.valor 
                        FROM gr_detalle_pago_predial dpp 
                        LEFT JOIN gr_detalle_factura_predial dfp ON dpp.detallefactura = dfp.id_unico 
                        LEFT JOIN gr_concepto_predial cp ON dfp.concepto = cp.id_unico 
                        WHERE dpp.pago = $idPago and dpp.valor != 0 ");
                $c=0;
                $arrayConcepto  = array();
                $arrayRubro     = array();
                $arrayCuentaD   = array();
                $arrayCuentaDC   = array();
                $arrayCuentaCC   = array();
                $totalD =0;
                $totalC =0;
                for ($i = 0; $i < count($row); $i++) {
                    $detalle    = $row[$i][0];
                    $concepto   = $row[$i][1];
                    $annocon    = $row[$i][2];
                    $valor      = $row[$i][3];
                    #Buscar Rubro Fuente Y Concepto Rubro 
                    $vg = $con->Listar("SELECT cf.concepto_financiero, cf.rubro_fuente 
                        FROM gf_configuracion_predial cf 
                        LEFT JOIN gf_vigencias_interfaz_predial v ON cf.vigencia = v.id_unico 
                        WHERE concepto_predial = $concepto AND v.valor = '$annocon' AND v.parametrizacionanno = $parm_anno");
                    if(count($vg)<=0){
                        $vg = $con->Listar("SELECT cf.concepto_financiero, cf.rubro_fuente 
                        FROM gf_configuracion_predial cf 
                        LEFT JOIN gf_vigencias_interfaz_predial v ON cf.vigencia = v.id_unico 
                        WHERE concepto_predial = $concepto AND v.vigencias_anteriores = 1 
                        AND v.parametrizacionanno = $parm_anno");
                    }
                    if(count($vg)>0){
                        $conceptoFinanciero = $vg[0][0];
                        $rubroFuente        = $vg[0][1];
                        if(!empty($conceptoFinanciero) && !empty($rubroFuente)){
                            $c+=1;
                           

                                $insertP = "INSERT INTO gf_detalle_comprobante_pptal 
                                        (valor, comprobantepptal, conceptorubro, 
                                        tercero, proyecto, rubrofuente) 
                                        VALUES($valor, $id_pptal, $conceptoFinanciero, 
                                        2, 2147483647, $rubroFuente)";
                                $resultP = $mysqli->query($insertP);

                            $id_dp = $con->Listar("SELECT MAX(id_unico) 
                            FROM gf_detalle_comprobante_pptal 
                            WHERE comprobantepptal = $id_pptal 
                            AND rubrofuente = $rubroFuente AND conceptorubro= $conceptoFinanciero");
                            $id_dp = $id_dp[0][0];
                            #********** Fin Detalle Pptal*****************#
                            #********** Insertar Detalles CNT **************#
                            #Buscar Configuración*
                            $crc = $con->Listar("SELECT cd.id_unico, cd.naturaleza, 
                                    cc.id_unico, cc.naturaleza  
                                    FROM gf_concepto_rubro_cuenta crc 
                                    LEFT JOIN gf_cuenta cd ON crc.cuenta_debito     = cd.id_unico 
                                    LEFT JOIN gf_cuenta cc ON crc.cuenta_credito    = cc.id_unico 
                                    WHERE crc.concepto_rubro =$conceptoFinanciero");
                            if(count($crc)>0){
                                $cuentad = $crc[0][0];
                                $naturad = $crc[0][1]; 
                                $cuentac = $crc[0][2];
                                $naturac = $crc[0][3]; 
                                #***** Insertar Detalle Cnt **** #
                                if($naturad ==1){
                                    if($valor>0){
                                        $valor = $valor*-1;
                                        $totalC +=$valor*-1;
                                    } else {
                                        $totalD +=$valor*-1;
                                        $valor = $valor*-1;
                                    }
                                } else {
                                    if($valor>0){
                                        $totalC +=$valor;
                                    } else {
                                        $totalD +=$valor*-1;
                                        $valor = $valor;
                                    }
                                }
                                    array_push ($arrayCuentaD ,$cuentad);
                                    $insertD = "INSERT INTO gf_detalle_comprobante 
                                        (fecha, valor, 
                                        comprobante, naturaleza, cuenta, 
                                        tercero, proyecto,  centrocosto, 
                                        detallecomprobantepptal) 
                                        VALUES('$fecha', $valor, 
                                        $id_cnt, $naturad, $cuentad,
                                        2,  2147483647,$centrocosto,  $id_dp)";
                                    $resultado = $mysqli->query($insertD);
                                $id_dc = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $id_cnt");
                                $id_dc = $id_dc[0][0];
                                #****** Insertar Detalle Causacion **********#
                                if($cuentad != $cuentac){
                                    if(!empty($tipocomprobantecausacion)){
                                        $valor      = $row[$i][3];
                                        ##Debito 
                                        if($naturad==1){
                                            $valord = ($valor);
                                        } else {
                                            $valord = ($valor)*-1;
                                        }
                                            array_push ($arrayCuentaDC ,$cuentad);
                                            $insertD = "INSERT INTO gf_detalle_comprobante 
                                                (fecha, valor, 
                                                comprobante, naturaleza, cuenta, 
                                                tercero, proyecto, centrocosto, 
                                                detalleafectado) 
                                                VALUES('$fecha', $valord, 
                                                $id_causacion, $naturad, $cuentad,
                                                2,  2147483647, $centrocosto, $id_dc)";
                                            $resultado = $mysqli->query($insertD);

                                        #** Credito 
                                        if($naturac==1){
                                            $valorc = ($valor)*-1;
                                        } else {
                                            $valorc = ($valor);
                                        }
                                        array_push ($arrayCuentaCC ,$cuentac);
                                        $insertD = "INSERT INTO gf_detalle_comprobante 
                                            (fecha, valor, 
                                            comprobante, naturaleza, cuenta, 
                                            tercero, proyecto, centrocosto, 
                                            detalleafectado) 
                                            VALUES('$fecha', $valorc, 
                                            $id_causacion, $naturac, $cuentac,
                                            2,  2147483647, $centrocosto, $id_dc)";
                                        $resultado = $mysqli->query($insertD);
                                    }
                                }
                                #******* Actualizar Detalle Pago Predial *****#
                                $update = $con->Listar("UPDATE gr_detalle_pago_predial 
                                   SET detallecomprobante  = $id_dc 
                                   WHERE id_unico =$detalle");  
                            }
                        }
                    }
                }
                #Buscar Cuenta De Banco 
                $sqlBanco = $con->Listar("SELECT cb.cuenta, c.naturaleza 
                            FROM gf_cuenta_bancaria cb 
                            LEFT JOIN gf_cuenta c ON cb.cuenta = c.id_unico 
                            WHERE cb.id_unico = ".$cm[0][4]);
                $cuentaB = $sqlBanco[0][0];
                $Ncuenta = $sqlBanco[0][1];

                #Registrar Cuenta de Banco 
                $valorB = $totalC-$totalD;
                if($Ncuenta ==1){
                    if($valorB>0){
                        $valorB =$valorB;
                    } else {
                        $valorB = $valorB*-1;
                    }
                } else {
                    if($valorB>0){
                        $valorB =$valorB*-1;
                    } else {
                        $valorB = $valorB;
                    }
                }
                if($pgs[$pg][1]==7){
                    $valorB = $valorB*-1;
                }
                $insertD = "INSERT INTO gf_detalle_comprobante 
                        (fecha, valor, 
                        comprobante, naturaleza, cuenta, 
                        tercero, proyecto, centrocosto) 
                        VALUES('$fecha', $valorB, 
                        $id_cnt, $Ncuenta, $cuentaB,
                        2,  2147483647, $centrocosto)";
                $resultado = $mysqli->query($insertD);  

                if($resultado==true){
                    $rta =1;
                } else {
                    $rta =0;
                }
                if($c==0){
                    #****************** Eliminar Comprobantes **********************#
                    $dl = $con->Listar("DELETE FROM gf_comprobante_cnt WHERE id_unico = $id_causacion");
                    $dl = $con->Listar("DELETE FROM gf_comprobante_pptal WHERE id_unico = $id_pptal");
                    $dl = $con->Listar("DELETE FROM gf_comprobante_cnt WHERE id_unico = $id_cnt");
                    $rta =0;
                }
                $dlc = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE id_unico = $id_causacion");
                IF(count($dlc)<0){
                    $dl = $con->Listar("DELETE FROM gf_comprobante_cnt WHERE id_unico = $id_causacion");
                }
                if($rta ==1){
                    $guardados +=1;
                }
            }
            }
        }
        echo $guardados;
    break;
   
    #*********** Validaciones Comprobante ****************#
    case (2):
        
        $rta =1;
        $html ="";
        $arrayConceptosp   = array();
        $arrayConceptosf   = array();
        
        $fechaI = ($_POST['fechaI']);
        $fechaF = ($_POST['fechaF']);
        #****** Buscar Tipo Comprobante Interfaz ******#
        $comp = $con->Listar("SELECT id_unico, comprobante_pptal, tipo_comp_hom 
                FROM gf_tipo_comprobante 
                WHERE interfaz_predial =1");
        if(count($comp)>0){
            #Buscar Pagos
            $pgs = $con->Listar("SELECT DISTINCT id_unico FROM gr_pago_predial 
                    WHERE parametrizacionanno =$parm_anno  AND (fechapago >='$fechaI' AND fechapago<='$fechaF')  ");
            for ($pg = 0; $pg < count($pgs); $pg++) {
                $idPago = $pgs[$pg][0];
                $row = $con->Listar("SELECT DISTINCT dpp.id_unico ,cp.id_concepto, 
                        cp.anno, dpp.valor , c.nombre 
                        FROM gr_detalle_pago_predial dpp 
                        LEFT JOIN gr_detalle_factura_predial dfp ON dpp.detallefactura = dfp.id_unico 
                        LEFT JOIN gr_concepto_predial cp ON dfp.concepto = cp.id_unico 
                        LEFT JOIN gr_concepto c ON cp.id_concepto = c.id_unico 
                        WHERE dpp.pago = $idPago and dpp.valor != 0 ");
                for ($i = 0; $i < count($row); $i++) {
                $detalle    = $row[$i][0];
                $concepto   = $row[$i][1];
                $annocon    = $row[$i][2];
                $valor      = $row[$i][3];
                $nconcepto  = $row[$i][4].' Vigencia: '.$annocon;
                #Buscar Rubro Fuente Y Concepto Rubro 
                $vg = $con->Listar("SELECT cf.concepto_financiero, cf.rubro_fuente, 
                        c.nombre 
                    FROM gf_configuracion_predial cf 
                    LEFT JOIN gf_vigencias_interfaz_predial v ON cf.vigencia = v.id_unico 
                    LEFT JOIN gf_concepto_rubro cr On cf.concepto_financiero = cr.id_unico 
                    LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                    WHERE concepto_predial = $concepto AND v.valor = '$annocon' AND v.parametrizacionanno = $parm_anno");
                #var_dump(count($vg)>0);
                if(count($vg)<=0){
                    $vg = $con->Listar("SELECT cf.concepto_financiero, cf.rubro_fuente, 
                        c.nombre 
                    FROM gf_configuracion_predial cf 
                    LEFT JOIN gf_vigencias_interfaz_predial v ON cf.vigencia = v.id_unico 
                    LEFT JOIN gf_concepto_rubro cr On cf.concepto_financiero = cr.id_unico 
                    LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                    WHERE concepto_predial = $concepto AND v.vigencias_anteriores = 1 
                    AND v.parametrizacionanno = $parm_anno");
                }
                if(count($vg)>0){
                    $conceptoFinanciero = $vg[0][0];
                    $rubroFuente        = $vg[0][1];
                    $nconceptorubro     = $vg[0][2];
                    if(!empty($conceptoFinanciero) && !empty($rubroFuente)){
                        $crc = $con->Listar("SELECT cd.id_unico, cd.naturaleza, 
                                cc.id_unico, cc.naturaleza  
                                FROM gf_concepto_rubro_cuenta crc 
                                LEFT JOIN gf_cuenta cd ON crc.cuenta_debito     = cd.id_unico 
                                LEFT JOIN gf_cuenta cc ON crc.cuenta_credito    = cc.id_unico 
                                WHERE crc.concepto_rubro =$conceptoFinanciero");
                        if(count($crc)>0){
                            $cuentad = $crc[0][0];
                            $cuentac = $crc[0][0];
                            if(!empty($conceptoFinanciero) && !empty($rubroFuente)){
                                
                            } else {
                                if(in_array($nconceptorubro, $arrayConceptosf)) {
                                } else {
                                    array_push ($arrayConceptosf ,$nconceptorubro);
                                    $html   .= "No Se Ha Encontrado Configuración De Cuentas Para El Concepto Financiero  $nconceptorubro".'<br/>';
                                    $rta     = 0;
                                }
                            }
                        } else {
                            if(in_array($nconceptorubro, $arrayConceptosf)) {
                            } else {
                                array_push ($arrayConceptosf ,$nconceptorubro);
                                $html   .= "No Se Ha Encontrado Configuración De Cuentas Para El Concepto Financiero $nconceptorubro".'<br/>';
                                $rta     = 0;
                            }
                        }

                    } else {
                        if(in_array($nconcepto, $arrayConceptosp)) {
                        } else {
                            array_push ($arrayConceptosp ,$nconcepto);
                            $html   .= "No Se Ha Encontrado Configuración Para El Concepto Predial $nconcepto".'<br/>';
                            $rta     = 0;
                        }
                        
                    }
                }else{
                    
                    if(in_array($nconcepto, $arrayConceptosp)) {    
                    } else {
                        array_push ($arrayConceptosp ,$nconcepto);
                        $html   .= "No Se Ha Encontrado Configuración Para El Concepto Predial $nconcepto".'<br/>';
                        $rta     = 0;
                    }                        
                } 

            }
          }
        } else {
            $html .="No Se Ha Encontrado Comprobante Para Realizar Interfáz".'<br/>';
            $rta    =0;
        }
        $datos = array("msj"=>$html,"rta"=>$rta);
        echo json_encode($datos); 
        
    break;
 }
