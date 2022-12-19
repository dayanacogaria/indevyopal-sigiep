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
    case (1):
        $fechaI = $_REQUEST['fechaI'];
        $fechaF = $_REQUEST['fechaF'];
        
        $rowRecaudo = $con->Listar("SELECT DISTINCT id_unico 
                FROM gc_recaudo_comercial 
                WHERE parametrizacionanno = $parm_anno AND clase = 2 
                AND fecha BETWEEN '$fechaI' AND '$fechaF' 
                ORDER BY fecha ");
        $rta =1;
        $html ="";
        $arrayConceptosp   = array();
        $arrayConceptosf   = array();
        #****** Valor Max Año ************#
        $am = $con->Listar("SELECT MAX(valor) FROM gf_vigencias_interfaz_reteica WHERE parametrizacionanno = $parm_anno");
        $am = $am[0][0];
        #****** Buscar Tipo Comprobante Interfaz ******#
        $comp = $con->Listar("SELECT id_unico, comprobante_pptal, tipo_comp_hom 
                FROM gf_tipo_comprobante 
                WHERE interfaz_reteica =1");
        if(count($comp)>0){
            for ($rc = 0; $rc < count($rowRecaudo); $rc++) {
                $idPago =$rowRecaudo[$rc][0];
            
                # ******* Buscar Detalles Pago ********#
                $row = $con->Listar("SELECT DISTINCT dpc.id_unico ,cc.id_unico,
                        a.vigencia, dpc.valor,  cc.descripcion 
                        FROM gc_detalle_recaudo dpc 
                        LEFT JOIN gc_detalle_declaracion dc ON dpc.det_dec = dc.id_unico 
                        LEFT JOIN gc_concepto_comercial cc ON dc.concepto = cc.id_unico  
                        LEFT JOIN gc_declaracion d ON dc.declaracion = d.id_unico 
                        LEFT JOIN gc_anno_comercial a ON d.periodo = a.id_unico 
                        WHERE dpc.recaudo = $idPago and dpc.valor != 0 
                        AND (cc.tipo_ope=2 OR cc.tipo_ope=3)");
                for ($z = 0; $z < count($row); $z++) {
                    $detalle    = $row[$z][0];
                    $concepto   = $row[$z][1];
                    $annocon    = $row[$z][2];
                    if($annocon > $am){
                        $annocon = $am;
                    }
                    $valor      = $row[$z][3];
                    $nconcepto  = $row[$z][4].' Vigencia: '.$annocon;
                    #Buscar Rubro Fuente Y Concepto Rubro 
                    $vg = $con->Listar("SELECT cf.concepto_financiero, cf.rubro_fuente, 
                            c.nombre 
                        FROM gf_configuracion_comercio cf 
                        LEFT JOIN gf_vigencias_interfaz_reteica v ON cf.vigencia_ica = v.id_unico 
                        LEFT JOIN gf_concepto_rubro cr On cf.concepto_financiero = cr.id_unico 
                        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                        WHERE concepto_comercio = $concepto AND v.valor = '$annocon' AND v.parametrizacionanno = $parm_anno");
                    $porc = $con->Listar("SELECT SUM(cf.porcentaje)
                        FROM gf_configuracion_comercio cf 
                        LEFT JOIN gf_vigencias_interfaz_reteica v ON cf.vigencia_ica = v.id_unico 
                        WHERE concepto_comercio = $concepto AND v.valor = '$annocon' AND v.parametrizacionanno = $parm_anno");
                    #var_dump(count($vg)>0);
                    if(count($vg)<=0){
                        $vg = $con->Listar("SELECT cf.concepto_financiero, cf.rubro_fuente, 
                            c.nombre 
                        FROM gf_configuracion_comercio cf 
                        LEFT JOIN gf_vigencias_interfaz_reteica v ON cf.vigencia_ica = v.id_unico 
                        LEFT JOIN gf_concepto_rubro cr On cf.concepto_financiero = cr.id_unico 
                        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                        WHERE concepto_comercio = $concepto AND v.vigencias_anteriores = 1 
                        AND v.parametrizacionanno = $parm_anno");

                        $porc = $con->Listar("SELECT SUM(cf.porcentaje)
                        FROM gf_configuracion_comercio cf 
                        LEFT JOIN gf_vigencias_interfaz_reteica v ON cf.vigencia_ica = v.id_unico 
                        WHERE concepto_comercio = $concepto AND v.vigencias_anteriores = 1 AND v.parametrizacionanno = $parm_anno");
                    }
                    if(count($vg)>0){ 
                        if($porc[0][0]==100) {

                            for ($i = 0; $i < count($vg); $i++) {
                                $conceptoFinanciero = $vg[$i][0];
                                $rubroFuente        = $vg[$i][1];
                                $nconceptorubro     = $vg[$i][2];
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
                                        $html   .= "No Se Ha Encontrado Configuración Para El Concepto Comercial $nconcepto".'<br/>';
                                        $rta     = 0;
                                    }
                                }
                            }
                        } else {
                            $html   .= "El Concepto Comercial $nconcepto No Está Configurado 100%".'<br/>';
                            $rta     = 0;
                        }
                    }else{
                        if(in_array($nconcepto, $arrayConceptosp)) {    
                        } else {
                            array_push ($arrayConceptosp ,$nconcepto);
                            $html   .= "No Se Ha Encontrado Configuración Para El Concepto Comercial $nconcepto".'<br/>';
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
    #*********** Guardar Interfaz Reteica ****************#
    case (2):
        
        
        $rta =1;
        #****** Buscar Tipo Comprobante Interfaz ******#
        $comp = $con->Listar("SELECT id_unico, comprobante_pptal, tipo_comp_hom 
                FROM gf_tipo_comprobante 
                WHERE interfaz_reteica =1");
        $tipocomprobante = $comp[0][0];
        $tipocomprobantepptal = $comp[0][1];
        $tipocomprobantecausacion = $comp[0][2];
        
        ##******** Buscar Centro De Costo ********#
        $cc = $con->Listar("SELECT * FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $parm_anno");
        $centrocosto = $cc[0][0];
        
        $fechaI = $_REQUEST['fechaI'];
        $fechaF = $_REQUEST['fechaF'];
        
        $rowRecaudo = $con->Listar("SELECT DISTINCT id_unico 
                FROM gc_recaudo_comercial 
                WHERE parametrizacionanno = $parm_anno AND clase = 2 
                AND fecha BETWEEN '$fechaI' AND '$fechaF' 
                ORDER BY fecha ");
         
        for ($rc = 0; $rc < count($rowRecaudo); $rc++) {
            $arrayConcepto  = array();
            $arrayRubro     = array();
            $arrayCuentaD   = array();
            $arrayCuentaDC   = array();
            $arrayCuentaCC   = array();
            $idPago =$rowRecaudo[$rc][0];        
            #****** Buscar Datos Básicos Para Comprobante ******#
            $cm = $con->Listar("SELECT d.cod_dec , rc.consecutivo, 
                    t.tercero, rc.fecha, 
                    rc.cuenta_ban, t.tercero 
                FROM  gc_detalle_recaudo dc 
                LEFT JOIN gc_recaudo_comercial rc ON dc.recaudo = rc.id_unico 
                LEFT JOIN gc_detalle_declaracion dfp ON dc.det_dec = dfp.id_unico               
                LEFT JOIN gc_declaracion d ON dfp.declaracion = d.id_unico 
                LEFT JOIN gc_contribuyente t On d.contribuyente = t.id_unico 
                WHERE dc.recaudo =$idPago ");

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
            $descripcion = '"Comprobante de Recaudo Reteica N°:'.$cm[0][1].' Declaración:'.$cm[0][0].'"';
            $fecha       = $cm[0][3];
            $tercero     = $cm[0][2];
            #Insertamos el comprobante
            $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,tipocomprobante, 
                    parametrizacionanno,tercero,estado,compania) 
                    values('$numeroC','$fecha',$descripcion,$tipocomprobante, 
                    $parm_anno,$tercero,'1',$compania)";
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
                        . "$parm_anno,$tipopptal,$tercero,'1',2)";
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
                $descripcion= '"Comprobante de Causación Recaudo Reteica N°:'.$cm[0][1].' Declaración:'.$cm[0][0].'"';
                #* Insertamos el comprobante
                $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,"
                        . "tipocomprobante,parametrizacionanno,tercero,estado,compania) "
                        . "values('$numeroCausacion','$fecha',$descripcion,$tipocau,"
                        . "$parm_anno,$tercero,'1',$compania)";
                $resultInsertC=$mysqli->query($sqlInsertC);
                #* Consultamos el ultimo comprobante ingresado
                $idCau=$con->Listar("select max(id_unico) from gf_comprobante_cnt where tipocomprobante=$tipocau and numero=$numeroCausacion");
                $id_causacion = $idCau[0][0];

            }


            $row = $con->Listar("SELECT DISTINCT dc.id_unico ,cc.id_unico, 
                    a.vigencia, dc.valor, cc.tipo_ope 
                    FROM gc_detalle_recaudo dc 
                    LEFT JOIN gc_detalle_declaracion dcc ON dc.det_dec = dcc.id_unico 
                    LEFT JOIN gc_concepto_comercial cc ON dcc.concepto = cc.id_unico                 
                    LEFT JOIN gc_declaracion dcl ON dcc.declaracion = dcl.id_unico 
                    LEFT JOIN gc_anno_comercial a ON dcl.periodo = a.id_unico 
                    WHERE dc.recaudo = $idPago and dc.valor != 0 
                    AND (cc.tipo_ope=2 OR cc.tipo_ope=3)");
            $c=0;
           
            $totalD =0;
            $totalC =0;
            #****** Valor Max Año ************#
            $am = $con->Listar("SELECT MAX(valor) FROM gf_vigencias_interfaz_reteica WHERE parametrizacionanno = $parm_anno");
            $am = $am[0][0];
            for ($i = 0; $i < count($row); $i++) {
                $detalle    = $row[$i][0];
                $concepto   = $row[$i][1];
                $annocon    = $row[$i][2];
                if($annocon > $am){
                    $annocon = $am;
                }
                $tipo_o     = $row[$i][4];
                if($tipo_o==3){
                    $valor      = $row[$i][3]*-1;
                } else {
                    $valor      = $row[$i][3];
                }

                #Buscar Rubro Fuente Y Concepto Rubro 
                $vg = $con->Listar("SELECT cf.concepto_financiero, cf.rubro_fuente, cf.porcentaje 
                    FROM gf_configuracion_comercio cf 
                    LEFT JOIN gf_vigencias_interfaz_reteica v ON cf.vigencia_ica = v.id_unico 
                    LEFT JOIN gf_concepto_rubro cr On cf.concepto_financiero = cr.id_unico 
                    LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                    WHERE concepto_comercio = $concepto AND v.valor = '$annocon' AND v.parametrizacionanno = $parm_anno");

                if(count($vg)<=0){
                    $vg = $con->Listar("SELECT cf.concepto_financiero, cf.rubro_fuente, cf.porcentaje 
                    FROM gf_configuracion_comercio cf 
                    LEFT JOIN gf_vigencias_interfaz_reteica v ON cf.vigencia_ica = v.id_unico 
                    LEFT JOIN gf_concepto_rubro cr On cf.concepto_financiero = cr.id_unico 
                    LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                    WHERE concepto_comercio = $concepto AND v.vigencias_anteriores = 1 AND v.parametrizacionanno = $parm_anno");

                }
                if(count($vg)>0){
                    $valorTotal = $valor;
                    for ($y = 0; $y < count($vg); $y++) {
                        $rfi = 0;
                        $conceptoFinanciero = $vg[$y][0];
                        $rubroFuente        = $vg[$y][1];
                        $porcentaje         = $vg[$y][2];
                        $valorguardar       = ($valorTotal * $porcentaje)/100;
                        if(!empty($conceptoFinanciero) && !empty($rubroFuente)){
                            $c+=1;
                            #********** Insertar Detalle Pptal*****************#
                            

                                $insertP = "INSERT INTO gf_detalle_comprobante_pptal 
                                        (valor, comprobantepptal, conceptorubro, 
                                        tercero, proyecto, rubrofuente) 
                                        VALUES($valorguardar, $id_pptal, $conceptoFinanciero, 
                                        $tercero, 2147483647, $rubroFuente)";
                                $resultP = $mysqli->query($insertP);
                                array_push ($arrayRubro ,$rubroFuente);
                                array_push ($arrayConcepto , $conceptoFinanciero );
                            

                            $id_dp = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_pptal");
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
                                    if($valorguardar>0){
                                        $valorguardar = $valorguardar*-1;
                                        $totalC +=$valorguardar*-1;
                                    } else {
                                        $totalD +=$valorguardar*-1;
                                        $valorguardar = $valorguardar*-1;
                                    }
                                } else {
                                    if($valorguardar>0){
                                        $totalC +=$valorguardar;
                                    } else {
                                        $totalD +=$valorguardar*-1;
                                        $valorguardar = $valorguardar;
                                    }
                                }
                                array_push ($arrayCuentaD ,$cuentad);
                                $insertD = "INSERT INTO gf_detalle_comprobante 
                                    (fecha, valor, 
                                    comprobante, naturaleza, cuenta, 
                                    tercero, proyecto,  centrocosto, 
                                    detallecomprobantepptal) 
                                    VALUES('$fecha', $valorguardar, 
                                    $id_cnt, $naturad, $cuentad,
                                    $tercero,  2147483647,$centrocosto,  $id_dp)";
                                $resultado = $mysqli->query($insertD);
                                
                                $id_dc = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $id_cnt");
                                $id_dc = $id_dc[0][0];
                                #****** Insertar Detalle Causacion **********#
                                if($cuentad != $cuentac){
                                    if(!empty($tipocomprobantecausacion)){
                                        $valorguardar       = ($valorTotal * $porcentaje)/100;
                                        ##Debito 
                                        if($naturad==1){
                                            $valord = ($valorguardar);
                                        } else {
                                            $valord = ($valorguardar)*-1;
                                        }
                                        array_push ($arrayCuentaDC ,$cuentad);
                                        $insertD = "INSERT INTO gf_detalle_comprobante 
                                            (fecha, valor, 
                                            comprobante, naturaleza, cuenta, 
                                            tercero, proyecto, centrocosto, 
                                            detalleafectado) 
                                            VALUES('$fecha', $valord, 
                                            $id_causacion, $naturad, $cuentad,
                                            $tercero,  2147483647, $centrocosto, $id_dc)";
                                        $resultado = $mysqli->query($insertD);


                                        #** Credito 
                                        if($naturac==1){
                                            $valorc = ($valorguardar)*-1;
                                        } else {
                                            $valorc = ($valorguardar);
                                        }
                                        array_push ($arrayCuentaCC ,$cuentac);
                                        $insertD = "INSERT INTO gf_detalle_comprobante 
                                            (fecha, valor, 
                                            comprobante, naturaleza, cuenta, 
                                            tercero, proyecto, centrocosto, 
                                            detalleafectado) 
                                            VALUES('$fecha', $valorc, 
                                            $id_causacion, $naturac, $cuentac,
                                            $tercero,  2147483647, $centrocosto, $id_dc)";
                                        $resultado = $mysqli->query($insertD);

                                    }
                                }
                                #******* Actualizar Detalle Pago Comercio *****#
                                $update = $con->Listar("UPDATE gc_detalle_recaudo 
                                   SET detalle_cnt = $id_dc 
                                   WHERE id_unico =$detalle");  
                            }
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
            $insertD = "INSERT INTO gf_detalle_comprobante 
                        (fecha, valor, 
                        comprobante, naturaleza, cuenta, 
                        tercero, proyecto, centrocosto) 
                        VALUES('$fecha', $valorB, 
                        $id_cnt, $Ncuenta, $cuentaB,
                        $tercero,  2147483647, $centrocosto)";
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
        }
        echo $rta;
    break;
 }
