<?php
session_start();
#
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
require_once '../funciones/funcionLiquidador.php';
require_once '../jsonPptal/funcionesPptal.php';
$anno           = $_SESSION['anno'];
$responsable    = $_SESSION['usuario_tercero'];
$con            = new ConexionPDO();
$parm_anno      = $_SESSION['anno'];
$nanno          = anno($anno);
$compania       = $_SESSION['compania'];
//Registrar Acuerdo de pago
$f      = ''.$mysqli->real_escape_string(''.$_GET['sltFechaA'].'').'';
$fch    = ''.$mysqli->real_escape_string(''.$_GET['sltFechaA'].'').'';
$fch2   = ''.$mysqli->real_escape_string(''.$_GET['sltFechaA'].'').'';
$fcomp  = ''.$mysqli->real_escape_string(''.$_GET['sltFechaA'].'').'';
if(empty($f)){     
    
} else {
    $f = explode("/", $f);                                        
    $f = "'".$f[2].'-'.$f[1].'-'.$f[0]."'";
    $fcm = explode("/", $fcomp); 
    $fcom = "".$fcm[2].'-'.$fcm[1].'-'.$fcm[0]."";
    $f1 = explode("/", $fch);  
    $f2 = $f1[2].'-'.$f1[1];
    $fcint = explode("/", $fch2);  
    $fci = $fcint[2].'-'.$fcint[1];
    
    
    $d=$f1[0];
    
}
$id_tipo    = '"'.$mysqli->real_escape_string(''.$_GET['sltTipo'].'').'"';
$tp         = ''.$mysqli->real_escape_string(''.$_GET['sltTipo'].'').'';
$nfac       = ''.$mysqli->real_escape_string(''.$_GET['sltFactura'].'').'';
$cb         = '"'.$mysqli->real_escape_string(''.$_GET['sltBanco'].'').'"';
$vlp        = '"'.$mysqli->real_escape_string(''.$_GET['txtValorP'].'').'"';
$vlp_operaciones = ''.$mysqli->real_escape_string(''.$_GET['txtValorP'].'').'';

if($tp==1){
    #* Insertamos el recaudo en gr_pago_predial
    $sql = "INSERT INTO gr_pago_predial(tipopago,fechapago,responsable,banco,paquete,"
            . "banco_migrado,pgo_especial,parametrizacionanno) "
        . "VALUES ('2',$f,$responsable,$cb,NULL,NULL,NULL,$anno)";
    $resultado  = $mysqli->query($sql);
    $sqlidpg    = "SELECT max(id_unico) as id from gr_pago_predial WHERE tipopago = 2 ";
    $r          = $mysqli->query($sqlidpg);
    $id_pag     = mysqli_fetch_row($r);   

    $sql_an     = "SELECT anno from gf_parametrizacion_anno where id_unico='$anno'";
    $resultado  = $mysqli->query($sql_an);
    $ann        = mysqli_fetch_row($resultado);  

    $sql_rec = "select id_unico from gr_concepto_predial where gr_concepto_predial.anno='$ann[0]' "
         . "and gr_concepto_predial.id_concepto=11";
    $resultado = $mysqli->query($sql_rec);
    $idrec  = mysqli_fetch_row($resultado); 
    $vlpg   = $vlp_operaciones;

    $sql_det_rec="SELECT da.id_unico, da.factura, da.valor, da.detalleacuerdo, da.iddetallerecaudo, 
        dfp.id_unico, dfp.valor, fp.id_unico , dac.concepto_deuda , dfp.avaluo 
    From ga_detalle_factura da 
    left join ga_detalle_acuerdo dac on dac.id_unico=da.detalleacuerdo 
    LEFT JOIN ga_acuerdo a ON dac.acuerdo = a.id_unico 
    LEFT JOIN ga_documento_acuerdo dca ON dca.acuerdo = a.id_unico 
    LEFT JOIN gr_factura_predial fp ON dca.soportedeuda = fp.numero 
    LEFT JOIN gr_detalle_factura_predial dfp ON fp.id_unico = dfp.factura 
            where da.factura='$nfac'  and dfp.concepto = dac.concepto_deuda ORDER BY da.valor ASC ";
    $resul = $mysqli->query($sql_det_rec);
    while($rowsDRec = mysqli_fetch_row($resul)){
        $id_factura_predial = $rowsDRec[7];
       $vlr_det = $rowsDRec[2];
       $id_det = $rowsDRec[0];
       if($vlpg >= $vlr_det){
            IF(empty($rowsDRec[5]) ){
                #* Guardar DT
                $sql_insert_det = "INSERT INTO gr_detalle_factura_predial(avaluo,
                   valor,factura,concepto) "
                . "VALUES ($rowsDRec[9],$rowsDRec[2],$id_factura_predial,$idrec[0])";
                $res = $mysqli->query($sql_insert_det);

                $sql_detf="SELECT * FROM gr_detalle_factura_predial WHERE factura =$nfac AND concepto = $idrec[0] ";
                $resuldt = $mysqli->query($sql_detf);
                $rowsdf = mysqli_fetch_row($resuldt);
                $iddf = $rowsdf[5];
            } elseif($rowsDRec[6]<$rowsDRec[2]) {
                $v = $rowsDRec[6]+$rowsDRec[2];
                $sql_insert_det = "update gr_detalle_factura_predial 
                    SET  valor = $v 
                    WHERE id_unico = $rowsDRec[5]";
                $res = $mysqli->query($sql_insert_det);
                $iddf = $rowsDRec[5];
            } else {
                $iddf = $rowsDRec[5];
            }

            $sql_insert_det = "INSERT INTO gr_detalle_pago_predial(valor,
                detallefactura,pago,detallecomprobante,iddetalleasosiadopago) "
             . "VALUES ($vlr_det,$iddf,$id_pag[0],NULL,NULL)";
             $res = $mysqli->query($sql_insert_det);
           $vlpg=$vlpg-$vlr_det;

                     //5. Consulta el ultimo detalle pago
           $sql_ul_det = "SELECT max(id_unico) as id from gr_detalle_pago_predial where pago='$id_pag[0]'";
           $rs = $mysqli->query($sql_ul_det);
           $id_det_pg = mysqli_fetch_row($rs);

                     //6. actualiza el detalle factura
           $sql_update_det = "UPDATE ga_detalle_factura SET iddetallerecaudo = '$id_det_pg[0]' "
           ." WHERE ga_detalle_factura.id_unico = $id_det; ";
           $resp = $mysqli->query($sql_update_det);
       }
    }
    
    
    #* REGISTRAR INTERFAZ 
    $idPago = $id_pag[0];
    $rta =1;
    #****** Buscar Tipo Comprobante Interfaz ******#
    $comp = $con->Listar("SELECT id_unico, comprobante_pptal, tipo_comp_hom 
            FROM gf_tipo_comprobante 
            WHERE interfaz_predial =1");
    $tipocomprobante = $comp[0][0];
    $tipocomprobantepptal = $comp[0][1];
    $tipocomprobantecausacion = $comp[0][2];

    ##******** Buscar Centro De Costo ********#
    $cc = $con->Listar("SELECT * FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $parm_anno");
    $centrocosto = $cc[0][0];
    #****** Buscar Datos Básicos Para Comprobante ******#
    $cm = $con->Listar("SELECT DISTINCT 
            fc.numero , p.codigo_catastral, 
            pr.nombres, pg.fechapago, 
            pg.banco , pg.tipopago 
        FROM  gr_detalle_pago_predial dpp 
        LEFT JOIN gr_pago_predial pg ON dpp.pago = pg.id_unico 
        LEFT JOIN gr_detalle_factura_predial dfp ON dpp.detallefactura = dfp.id_unico               
        LEFT JOIN gr_factura_predial fc ON dfp.factura = fc.id_unico 
        LEFT JOIN gp_predio1 p ON fc.predio = p.id_unico 
        LEFT JOIN gp_tercero_predio tp On p.id_unico = tp.predio 
        LEFT JOIN gr_propietarios pr ON tp.tercero = pr.id_unico 
        WHERE dpp.pago =$idPago AND tp.propietario = 0;");

    if($cm[0][5]==1 ||$cm[0][5]==7 || $cm[0][5]==4 || $cm[0][5]==2) {

        #*********** Crear Comprobantes *****************#
        #Consultamos el ultimo numero de acuerdo al tipo de comprobante
        $numeroCnt=$con->Listar("select max(numero) from gf_comprobante_cnt "
                . "where tipocomprobante=$tipocomprobante AND parametrizacionanno = $parm_anno ");
        if(!empty($numeroCnt[0][0])){
            $numeroC=$numeroCnt[0][0]+1;
        }else{
            $numeroC=$nanno.'000001';
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
                $numeroPp=$nanno.'000001';
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
                $numeroCausacion=$nanno.'000001';
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


        $row = $con->Listar("SELECT DISTINCT dpp.id_unico ,cp.id_concepto, 
                cp.anno, dpp.valor 
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
                    #********** Insertar Detalle Pptal*****************#
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

                        $insertD = "INSERT INTO gf_detalle_comprobante 
                            (fecha, valor, 
                            comprobante, naturaleza, cuenta, 
                            tercero, proyecto,  centrocosto, 
                            detallecomprobantepptal) 
                            VALUES('$fecha', $valor, 
                            $id_cnt, $naturad, $cuentad,
                            2,  2147483647,$centrocosto,  $id_dp)";
                        $resultado1 = $mysqli->query($insertD);
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
                                $insertD = "INSERT INTO gf_detalle_comprobante 
                                    (fecha, valor, 
                                    comprobante, naturaleza, cuenta, 
                                    tercero, proyecto, centrocosto, 
                                    detalleafectado) 
                                    VALUES('$fecha', $valord, 
                                    $id_causacion, $naturad, $cuentad,
                                    2,  2147483647, $centrocosto, $id_dc)";
                                $resultado1 = $mysqli->query($insertD);
                                $valor      = $row[$i][3];
                                #** Credito 
                                if($naturac==1){
                                    $valorc = ($valor)*-1;
                                } else {
                                    $valorc = ($valor);
                                }
                                $insertD = "INSERT INTO gf_detalle_comprobante 
                                    (fecha, valor, 
                                    comprobante, naturaleza, cuenta, 
                                    tercero, proyecto, centrocosto, 
                                    detalleafectado) 
                                    VALUES('$fecha', $valorc, 
                                    $id_causacion, $naturac, $cuentac,
                                    2,  2147483647, $centrocosto, $id_dc)";
                                $resultado1 = $mysqli->query($insertD);
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
        if($cm[0][5]==7){
            $valorB = $valorB *-1;
            $insertD = "INSERT INTO gf_detalle_comprobante 
                    (fecha, valor, 
                    comprobante, naturaleza, cuenta, 
                    tercero, proyecto, centrocosto) 
                    VALUES('$fecha', $valorB, 
                    $id_cnt, $Ncuenta, $cuentaB,
                    2,  2147483647, $centrocosto)";
            $resultadoT = $mysqli->query($insertD);
        } else {
            $insertD = "INSERT INTO gf_detalle_comprobante 
                    (fecha, valor, 
                    comprobante, naturaleza, cuenta, 
                    tercero, proyecto, centrocosto) 
                    VALUES('$fecha', $valorB, 
                    $id_cnt, $Ncuenta, $cuentaB,
                    2,  2147483647, $centrocosto)";
            $resultadoT = $mysqli->query($insertD);  
        }
        if($resultadoT==true){
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
    } ELSE {
        $rta = 0;
    }
    
    #* Verificar saldo acuerdo 
    $vs = $con->Listar("SELECT DISTINCT a.id_unico,
        (SELECT SUM(dac.valor) FROM ga_detalle_acuerdo dac WHERE dac.acuerdo = a.id_unico) AS VA, 
        (SELECT SUM(dp.valor) FROM gr_detalle_pago_predial dp 
	LEFT JOIN ga_detalle_factura dfa ON dp.id_unico = dfa.iddetallerecaudo 
        LEFT JOIN ga_factura_acuerdo fac ON dfa.factura = fac.id_unico 
        LEFT JOIN ga_detalle_acuerdo dac ON dfa.detalleacuerdo = dac.id_unico 
        WHERE dac.acuerdo = a.id_unico)as VR
    FROM  ga_factura_acuerdo fp 
    LEFT JOIN ga_detalle_factura df ON fp.id_unico = df.factura
    LEFT JOIN ga_detalle_acuerdo da ON df.detalleacuerdo = da.id_unico 
    LEFT JOIN ga_acuerdo a ON da.acuerdo = a.id_unico 
    WHERE fp.id_unico = $nfac");
    IF(!empty($vs[0][2])){
        $saldo = $vs[0][1] -$vs[0][2];
        if($saldo <=0){
            //ACTUALIZAR ESTADO PREDIO
            $sqle = "UPDATE gr_factura_predial fp 
            LEFT JOIN gp_predio1 p ON fp.predio = p.id_unico SET p.estado = 2  
            WHERE  fp.id_unico = $id_factura_predial";
            $upd = $mysqli->query($sqle);
            //ACTUALIZAR UTLIMO ANNO PAGO
            $ua = $con->Listar("SELECT MAX(a.anno), a.predio FROM gr_detalle_factura_predial dfp 
            LEFT JOIN gr_avaluo a ON dfp.avaluo = a.id_unico 
            where dfp.factura = $id_factura_predial");
            $sql_insert_det = "INSERT INTO gr_ultimo_ano_pago(predio,anno) "
             . "VALUES (".$ua[0][1].",".$ua[0][0].")";
             $res = $mysqli->query($sql_insert_det);
        }
    }
    
    
}else if($tp==2){
    //1. Consultamos el numero del acuerdo
    $sql_num_ac="SELECT DISTINCT a.id_unico as id  FROM  ga_detalle_factura df
                left join ga_detalle_acuerdo da on da.id_unico=df.detalleacuerdo
                left join ga_acuerdo a on a.id_unico=da.acuerdo
                where df.factura=$nfac";
    $respt = $mysqli->query($sql_num_ac);
    $id_ac = mysqli_fetch_row($respt);      
    
    //2. Consultamos el consecutivo de los pagos    
    $sql_cons="SELECT max(consecutivo) as cn FROM gc_recaudo_comercial ";
    $rest = $mysqli->query($sql_cons);
    $consec = mysqli_fetch_row($rest);  
    $cns=$consec[0];
    if(empty($cns)){
        //3. Buscamos la parametrizacion año
        $sql_an = "SELECT anno from gf_parametrizacion_anno where id_unico='$anno'";
        $rsult = $mysqli->query($sql_an);
        $ann = mysqli_fetch_row($rsult);
        $cns=$ann[0].'00001';
    }else{
        $cns=$cns+1;
    }
       
    //4. insertamos el recaudo en gc_recaudo_comercial
     $sql = "INSERT INTO gc_recaudo_comercial(consecutivo,declaracion,fecha,num_pag,cuenta_ban,"
            . "valor,observaciones,tipo_rec,rec_afect,acuerdo_pago,parametrizacionanno) "
        . "VALUES ($cns,NULL,$f,NULL,$cb,$vlp,NULL,'2',NULL,$id_ac[0],$anno)";
    $resultado = $mysqli->query($sql);
    
    //2. consultar el id del ultimo pago
    $sqlidpg = "SELECT max(id_unico) as id from gc_recaudo_comercial";
    $r = $mysqli->query($sqlidpg);
    $id_pag = mysqli_fetch_row($r);  
    $vlpg=$vlp_operaciones;
    
    //BUSCAR EL CONCEPTO DE MORA
    $sql_rec = "SELECT cc.id_unico from gc_concepto_comercial cc where cc.codigo=1 and cc.tipo=6 ";
    $resultado = $mysqli->query($sql_rec);
    $idrec = mysqli_fetch_row($resultado); 
    
    $sql_det_rec="SELECT da.* From ga_detalle_factura da 
        left join ga_detalle_acuerdo dac on dac.id_unico=da.detalleacuerdo 
        where da.factura='$nfac' and dac.concepto_deuda=$idrec[0] ";
    $resul = $mysqli->query($sql_det_rec);
    while($rowsDRec = mysqli_fetch_row($resul)){

       $vlr_det=$rowsDRec[2];
       $id_det=$rowsDRec[0];
       if($vlpg>=$vlr_det){
           
            $sql_det_pag_ant = "select iddetallerecaudo from  ga_detalle_factura 
            WHERE ga_detalle_factura.id_unico = $id_det; ";
            $rss = $mysqli->query($sql_det_pag_ant);
            $id_det_pg_ant = mysqli_fetch_row($rss);
            
            if(empty($id_det_pg_ant[0])){
                $sql_insert_det = "INSERT INTO gc_detalle_recaudo(recaudo,det_dec,valor,iddetalleasosiadopago) "
                . "VALUES ($id_pag[0],NULL,$vlr_det,NULL)";
                $res = $mysqli->query($sql_insert_det);
            }else{
                $sql_insert_det = "INSERT INTO gc_detalle_recaudo(recaudo,det_dec,valor,iddetalleasosiadopago) "
                . "VALUES ($id_pag[0],NULL,$vlr_det,$id_det_pg_ant[0])";
                $res = $mysqli->query($sql_insert_det);
            }
           
           
            $vlpg=$vlpg-$vlr_det;

            //5. Consulta el ultimo detalle pago
             $sql_ul_det = "SELECT max(id_unico) as id from gc_detalle_recaudo where recaudo='$id_pag[0]'";
             $rs = $mysqli->query($sql_ul_det);
             $id_det_pg = mysqli_fetch_row($rs);

                       //6. actualiza el detalle factura
             $sql_update_det = "UPDATE ga_detalle_factura SET iddetallerecaudo = '$id_det_pg[0]' "
             ." WHERE ga_detalle_factura.id_unico = $id_det; ";
             $resp = $mysqli->query($sql_update_det);
       }
    }
    
    
    

    //3. Buscamos los detalles de la consulta y los registramos como 
    //pagos hasta donde alcance el monto de pago
    $sql_detalles_fac="SELECT da.* From ga_detalle_factura da 
        left join ga_detalle_acuerdo dac on dac.id_unico=da.detalleacuerdo 
        where da.factura='$nfac' and dac.concepto_deuda!=$idrec[0] ";
    $resul = $mysqli->query($sql_detalles_fac);
    
while($rowsDF = mysqli_fetch_row($resul)){
   $vlr_det=$rowsDF[2];
   $id_det=$rowsDF[0];
   
   $vlr_fac="SELECT sum(da.valor) as vlor From ga_detalle_factura da where da.factura='$nfac' ";
   $rsp = $mysqli->query($vlr_fac);
   $vlr_fact = mysqli_fetch_row($rsp); 
   
   if($vlp_operaciones==$vlr_fact[0]){
       
        $sql_det_pag_ant = "select iddetallerecaudo from  ga_detalle_factura 
        WHERE ga_detalle_factura.id_unico = $id_det; ";
        $rss = $mysqli->query($sql_det_pag_ant);
        $id_det_pg_ant = mysqli_fetch_row($rss);
       
       if(empty($id_det_pg_ant[0])){
           $sql_insert_det = "INSERT INTO gc_detalle_recaudo(recaudo,det_dec,valor,iddetalleasosiadopago) "
            . "VALUES ($id_pag[0],NULL,$vlr_det,NULL)";
            $res = $mysqli->query($sql_insert_det);
       }else{
           $sql_insert_det = "INSERT INTO gc_detalle_recaudo(recaudo,det_dec,valor,iddetalleasosiadopago) "
            . "VALUES ($id_pag[0],NULL,$vlr_det,$id_det_pg_ant[0])";
            $res = $mysqli->query($sql_insert_det);
       }
       
       
       //5. Consulta el ultimo detalle pago
        $sql_ul_det = "SELECT max(id_unico) as id from gc_detalle_recaudo where recaudo='$id_pag[0]'";
        $rs = $mysqli->query($sql_ul_det);
        $id_det_pg = mysqli_fetch_row($rs);

                  //6. actualiza el detalle factura
        $sql_update_det = "UPDATE ga_detalle_factura SET iddetallerecaudo = '$id_det_pg[0]' "
        ." WHERE ga_detalle_factura.id_unico = $id_det; ";
        $resp = $mysqli->query($sql_update_det);
   }else{
        if($vlpg>=$vlr_det){
            if($vlr_det<0 && $vlpg>0){
                echo $sql_valor_det_pag = "SELECT sum(valor)as vl From gc_detalle_recaudo dr where dr.recaudo='$id_pag[0]' ";
                 $respta = $mysqli->query($sql_valor_det_pag);
                 $sumvl_pag = mysqli_fetch_row($respta);   
                 $sumvl=$sumvl_pag[0];
                 echo '   respuesta  '.$sumvl;
                 if(empty($sumvl)){
                     $sumvl=0;
                 }
                 if($sumvl==$vlp_operaciones ){

                 } else {
                     $sql_det_pag_ant = "select iddetallerecaudo from  ga_detalle_factura 
                     WHERE ga_detalle_factura.id_unico = $id_det; ";
                    $rss = $mysqli->query($sql_det_pag_ant);
                    $id_det_pg_ant = mysqli_fetch_row($rss);
                     //4. registra el detalle del pago
                    if(empty($id_det_pg_ant[0])){
                        $sql_insert_det = "INSERT INTO gc_detalle_recaudo(recaudo,det_dec,valor,iddetalleasosiadopago)  "
                        . "VALUES ($id_pag[0],NULL,$vlr_det,NULL)";
                        $res = $mysqli->query($sql_insert_det);
                    }else{
                        $sql_insert_det = "INSERT INTO gc_detalle_recaudo(recaudo,det_dec,valor,iddetalleasosiadopago)  "
                        . "VALUES ($id_pag[0],NULL,$vlr_det,$id_det_pg_ant[0])";
                        $res = $mysqli->query($sql_insert_det);
                    }
                 
                  $vlpg=$vlpg-$vlr_det;

                  //5. Consulta el ultimo detalle pago
                  $sql_ul_det = "SELECT max(id_unico) as id from gc_detalle_recaudo where recaudo='$id_pag[0]'";
                  $rs = $mysqli->query($sql_ul_det);
                  $id_det_pg = mysqli_fetch_row($rs);

                  //6. actualiza el detalle factura
                  $sql_update_det = "UPDATE ga_detalle_factura SET iddetallerecaudo = '$id_det_pg[0]' "
                          ." WHERE ga_detalle_factura.id_unico = $id_det; ";
                  $resp = $mysqli->query($sql_update_det);
                 }
            }else if($vlpg>0){
                
                $sql_det_pag_ant = "select iddetallerecaudo from  ga_detalle_factura 
                     WHERE ga_detalle_factura.id_unico = $id_det; ";
                $rss = $mysqli->query($sql_det_pag_ant);
                $id_det_pg_ant = mysqli_fetch_row($rss);
                
                if(empty($id_det_pg_ant[0])){
                    $sql_insert_det = "INSERT INTO gc_detalle_recaudo(recaudo,det_dec,valor,iddetalleasosiadopago) "
                    . "VALUES ($id_pag[0],NULL,$vlr_det,NULL)";
                    $res = $mysqli->query($sql_insert_det);
                }else{
                    $sql_insert_det = "INSERT INTO gc_detalle_recaudo(recaudo,det_dec,valor,iddetalleasosiadopago) "
                    . "VALUES ($id_pag[0],NULL,$vlr_det,$id_det_pg_ant[0])";
                    $res = $mysqli->query($sql_insert_det);
                }
                //4. registra el detalle del pago
                 
                  $vlpg=$vlpg-$vlr_det;

                  //5. Consulta el ultimo detalle pago
                  $sql_ul_det = "SELECT max(id_unico) as id from gc_detalle_recaudo where recaudo='$id_pag[0]'";
                  $rs = $mysqli->query($sql_ul_det);
                  $id_det_pg = mysqli_fetch_row($rs);

                  //6. actualiza el detalle factura
                  $sql_update_det = "UPDATE ga_detalle_factura SET iddetallerecaudo = '$id_det_pg[0]' "
                          ." WHERE ga_detalle_factura.id_unico = $id_det; ";
                  $resp = $mysqli->query($sql_update_det);
            }




        } else {
            if($vlpg>0){
             $sql_det_pag_ant = "select iddetallerecaudo from  ga_detalle_factura 
                     WHERE ga_detalle_factura.id_unico = $id_det; ";
             $rss = $mysqli->query($sql_det_pag_ant);
             $id_det_pg_ant = mysqli_fetch_row($rss);
                
             if(empty($id_det_pg_ant[0])){
                 $sql_insert_det = "INSERT INTO gc_detalle_recaudo(recaudo,det_dec,valor,iddetalleasosiadopago) "
                    . "VALUES ($id_pag[0],NULL,$vlpg,NULL)";
                    $res = $mysqli->query($sql_insert_det);
             }else{
                 $sql_insert_det = "INSERT INTO gc_detalle_recaudo(recaudo,det_dec,valor,iddetalleasosiadopago) "
                    . "VALUES ($id_pag[0],NULL,$vlpg,$id_det_pg_ant[0])";
                    $res = $mysqli->query($sql_insert_det);
             }
             

             //5. Consulta el ultimo detalle pago
             $sql_ul_det = "SELECT max(id_unico) as id from gc_detalle_recaudo where recaudo='$id_pag[0]'";
             $rs = $mysqli->query($sql_ul_det);
             $id_det_pg = mysqli_fetch_row($rs);

             // sacar el iddetallerecaudo guardado  y se actualiza en detalle factura 
             //6. actualiza el detalle factura
             $sql_update_det = "UPDATE ga_detalle_factura SET iddetallerecaudo = '$id_det_pg[0]' 
                     WHERE ga_detalle_factura.id_unico = $id_det; ";
             $resp = $mysqli->query($sql_update_det);
             $vlpg=0;
            }
        }
   }
}
}

?>  

<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/style.css">
 <script src="../js/md5.pack.js"></script>
 <script src="../js/jquery.min.js"></script>
 <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
 <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
</head>
<body>
</body>
</html>
<!--Modal para informar al usuario que se ha registrado-->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información guardada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <!--Modal para informar al usuario que no se ha podido registrar -->
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido guardar la información.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--lnks para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../ver_GA_RECAUDO_ACUERDO.php?id=<?php echo md5($id_pag[0])?>&tip=<?php echo $tp ?>';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>
