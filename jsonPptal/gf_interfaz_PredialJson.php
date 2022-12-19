<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#28/02/2018 |Erica G. | Guardar Interfaz Predial
#19/02/2018 |Erica G. | Funciones Para CRUD Configuración Predial
#18/02/2018 |Erica G. | Archivo Creado 
#######################################################################################################
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
require '../jsonPptal/funcionesPptal.php';
$con = new ConexionPDO();
session_start();
$parm_anno = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$anno       = anno($parm_anno);
switch ($_REQUEST['action']){
    #***************Guardar Vigencias Interfaz Predial************************#
    case(1):
        $nombre         = $_POST['nombre'];
        $valor          = $_POST['valor'];
        $vigencias      = $_POST['vigencias_anteriores'];
        $sql_cons ="INSERT INTO `gf_vigencias_interfaz_predial` 
              ( `nombre`, `valor`, `vigencias_anteriores`,`parametrizacionanno` ) 
        VALUES (:nombre, :valor, :vigencias_anteriores, :parametrizacionanno)";
        $sql_dato = array(
                array(":nombre",$nombre),
                array(":valor",$valor),
                array(":vigencias_anteriores",$vigencias),
                array(":parametrizacionanno",$parm_anno),
	);
	$obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            $res = true;
        } else {
            $res = false;
        }
        echo json_decode($res);
    break;
    #***************Modificar Vigencias Interfaz Predial************************#
    case(2):
        $id         = $_POST['id'];
        $nombre     = $_POST['nombre'];
        $valor      = $_POST['valor'];
        $vigencias  = $_POST['vigencias_anteriores'];
        $sql_cons ="UPDATE `gf_vigencias_interfaz_predial` 
            SET `nombre`=:nombre, 
                `valor`=:valor, 
                `vigencias_anteriores`=:vigencias_anteriores 
                WHERE id_unico=:id_unico ";
        $sql_dato = array(
            array(":nombre",$nombre),
            array(":valor",$valor),
            array(":vigencias_anteriores",$vigencias),
            array(":id_unico",$id),

        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            $res = true;
        } else {
            $res = false;
        }
        echo json_decode($res);
    break;
    #***************Eliminar Vigencias Interfaz Predial************************#
    case(3):
        $id         = $_POST['id'];
        $sql_cons ="DELETE FROM `gf_vigencias_interfaz_predial` WHERE `id_unico`=:id_unico";
        $sql_dato = array(
                array(":id_unico",$id),	
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            $res = true;
        } else {
            $res = false;
        }
        echo json_decode($res);
    break;
    # ******************* Guardar Configuración Predial ******************* #
    case (4):
        $g =0;
        $concepto_predial =$_POST['concepto_predial'];
        $vg = $con->Listar("SELECT id_unico, nombre FROM gf_vigencias_interfaz_predial WHERE parametrizacionanno = $parm_anno");
        for ($i = 0; $i < count($vg); $i++) {
            $nc             = 'concepto'.$concepto_predial.''.$vg[$i][0];
            $conc           = $_POST[$nc];
            $div            = explode(",", $conc);
            $conceptoRubro  = trim($div[0]);
            $rubroFuente    = trim($div[1]);
            $vigencia       = $vg[$i][0];
            
            $sql_cons ="INSERT INTO `gf_configuracion_predial` 
            ( `concepto_predial`, `concepto_financiero`, `rubro_fuente`,`vigencia` ) 
            VALUES (:concepto_predial, :concepto_financiero, :rubro_fuente, :vigencia)";
            $sql_dato = array(
                    array(":concepto_predial",$concepto_predial),
                    array(":concepto_financiero",$conceptoRubro),
                    array(":rubro_fuente",$rubroFuente),
                    array(":vigencia",$vigencia),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($obj_resp)){
                $g +=1;
            }
        }
        if($g == count($vg)){
            $rta = 0;
        } else {
            $rta = 1;
        }
        echo $rta;
    break;
    # ******************* Eliminar Configuración Predial ******************* #
    case (5):
        $ids        = $_POST['id'];
        $sql_cons =$con->Listar("DELETE cf.* FROM `gf_configuracion_predial` cf 
            LEFT JOIN gf_vigencias_interfaz_predial vg ON cf.vigencia = vg.id_unico
            WHERE cf.concepto_predial = $ids  AND vg.parametrizacionanno = $parm_anno");
       
        
        if(empty($obj_resp)){
            $res = 0;
        } else {
            $res = 1;
        }
        echo json_decode($res);
    break;
    # ******************* Modal Modificar Configuración Predial ******************* #
    case (6):
        $id = $_POST['id'];
        $html  ="";
        
        $vg = $con->Listar("SELECT id_unico, nombre FROM gf_vigencias_interfaz_predial WHERE parametrizacionanno = $parm_anno");
        for ($i = 0; $i < count($vg); $i++) {
            $html .='<div style="margin-top: 13px;">';
            $html .='<input type ="hidden" name="conceptop" id ="conceptop" value="'.$id.'">';
            $html .='<label style="display:inline-block; width:140px">'.$vg[$i][1].'</label>';
            $html .='<select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="concepto'.$id.''.$vg[$i][0].'" id="concepto'.$id.''.$vg[$i][0].'" class="select2_single form-control" required>';
            #Buscar Concepto Configurado
            $cncf = $con->Listar("SELECT cr.id_unico , 
            rf.id_unico, LOWER(c.nombre), rb.codi_presupuesto, LOWER(rb.nombre), LOWER(f.nombre) 
            FROM gf_configuracion_predial cf 
            LEFT JOIN gf_concepto_rubro cr ON cf.concepto_financiero = cr.id_unico 
            LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
            LEFT JOIN gf_rubro_fuente rf ON cr.rubro = rf.rubro 
            LEFT JOIN gf_rubro_pptal rb On rf.rubro = rb.id_unico 
            LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
            WHERE cf.concepto_predial = $id AND cf.vigencia = ".$vg[$i][0]);
            $html .='<option value ="'.$cncf[0][0].','.$cncf[0][1].'">'.ucwords($cncf[0][2]).' - '.$cncf[0][3].' '.ucwords($cncf[0][4]).' - '.ucwords($cncf[0][5]).'</option>';
            $cfvm = $con->Listar("SELECT cr.id_unico , 
            rf.id_unico, LOWER(c.nombre), rb.codi_presupuesto, LOWER(rb.nombre), LOWER(f.nombre) 
                FROM gf_concepto_rubro cr 
                LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                LEFT JOIN gf_rubro_fuente rf ON cr.rubro = rf.rubro 
                LEFT JOIN gf_rubro_pptal rb On rf.rubro = rb.id_unico 
                LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
            WHERE c.parametrizacionanno = $parm_anno AND rf.id_unico IS NOT NULL 
            AND (rb.tipoclase = 6 OR rb.tipoclase = 8) AND cr.id_unico !=".$cncf[0][0]." AND rf.id_unico !=".$cncf[0][1]);
            for($z =0; $z < count($cfvm); $z++){
                $html .='<option value ="'.$cfvm[$z][0].','.$cfvm[$z][1].'">'.ucwords($cfvm[$z][2]).' - '.$cfvm[$z][3].' '.ucwords($cfvm[$z][4]).' - '.ucwords($cfvm[$z][5]).'</option>';
            }
            $html .='</select>';
            $html .='</div>';
        }
       
    echo $html;    
    break;
    # *******************  Modificar Configuración Predial ******************* #
    case (7):
        $g =0;
        $concepto_predial =$_POST['conceptop'];
        $vg = $con->Listar("SELECT id_unico, nombre FROM gf_vigencias_interfaz_predial WHERE parametrizacionanno = $parm_anno");
        for ($i = 0; $i < count($vg); $i++) {
            $nc             = 'concepto'.$concepto_predial.''.$vg[$i][0];
            $conc           = $_POST[$nc];
            $div            = explode(",", $conc);
            $conceptoRubro  = trim($div[0]);
            $rubroFuente    = trim($div[1]);
            $vigencia       = $vg[$i][0];
            $sqls = $con->Listar("SELECT * FROM gf_configuracion_predial 
                    WHERE concepto_predial =$concepto_predial 
                    AND  vigencia =$vigencia");
            if(count($sqls)>0) {
                $sql_cons ="UPDATE `gf_configuracion_predial` 
                SET `concepto_financiero`=:concepto_financiero, 
                `rubro_fuente`=:rubro_fuente 
                WHERE `concepto_predial`=:concepto_predial AND `vigencia`=:vigencia ";
                $sql_dato = array(
                        array(":concepto_predial",$concepto_predial),
                        array(":concepto_financiero",$conceptoRubro),
                        array(":rubro_fuente",$rubroFuente),
                        array(":vigencia",$vigencia),
                );
            } else {
               $sql_cons ="INSERT INTO `gf_configuracion_predial` 
                ( `concepto_predial`, `concepto_financiero`, `rubro_fuente`,`vigencia` ) 
                VALUES (:concepto_predial, :concepto_financiero, :rubro_fuente, :vigencia)";
                $sql_dato = array(
                        array(":concepto_predial",$concepto_predial),
                        array(":concepto_financiero",$conceptoRubro),
                        array(":rubro_fuente",$rubroFuente),
                        array(":vigencia",$vigencia),
                ); 
            }
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($obj_resp)){
                $g +=1;
            }
        }
        if($g == count($vg)){
            $rta = 0;
        } else {
            $rta = 1;
        }
        echo $rta;
    break;
    #*********** Guardar Interfaz Predial ****************#
    case (8):
        $idPago = $_POST['id'];
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
                                    $insertD = "INSERT INTO gf_detalle_comprobante 
                                        (fecha, valor, 
                                        comprobante, naturaleza, cuenta, 
                                        tercero, proyecto, centrocosto, 
                                        detalleafectado) 
                                        VALUES('$fecha', $valord, 
                                        $id_causacion, $naturad, $cuentad,
                                        2,  2147483647, $centrocosto, $id_dc)";
                                    $resultado = $mysqli->query($insertD);
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
            if($cm[0][5]==7){
                $valorB = $valorB *-1;
                $insertD = "INSERT INTO gf_detalle_comprobante 
                        (fecha, valor, 
                        comprobante, naturaleza, cuenta, 
                        tercero, proyecto, centrocosto) 
                        VALUES('$fecha', $valorB, 
                        $id_cnt, $Ncuenta, $cuentaB,
                        2,  2147483647, $centrocosto)";
                $resultado = $mysqli->query($insertD);
            } else {
                $insertD = "INSERT INTO gf_detalle_comprobante 
                        (fecha, valor, 
                        comprobante, naturaleza, cuenta, 
                        tercero, proyecto, centrocosto) 
                        VALUES('$fecha', $valorB, 
                        $id_cnt, $Ncuenta, $cuentaB,
                        2,  2147483647, $centrocosto)";
                $resultado = $mysqli->query($insertD);  
            }
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
        } ELSE {
            $rta = 0;
        }
        echo $rta;
    break;
    #*********** Sesiones Comprobante De Ingreso Predial ****************#
    case (9):
        $_SESSION['idComprobanteI'] = $_REQUEST['id'];
        $_SESSION['idPptal']        = $_REQUEST['idp'];
        echo true;
    break;
    #*********** Validaciones Comprobante ****************#
    case (10):
        $idPago = $_POST['id'];
        $rta =1;
        $html ="";
        $arrayConceptosp   = array();
        $arrayConceptosf   = array();
        #****** Buscar Tipo Comprobante Interfaz ******#
        $comp = $con->Listar("SELECT id_unico, comprobante_pptal, tipo_comp_hom 
                FROM gf_tipo_comprobante 
                WHERE interfaz_predial =1");
        if(count($comp)>0){
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
        } else {
            $html .="No Se Ha Encontrado Comprobante Para Realizar Interfáz".'<br/>';
            $rta    =0;
        }
        $datos = array("msj"=>$html,"rta"=>$rta);
        echo json_encode($datos); 
        
    break;
    case (11):
        $mes = $_REQUEST['mes'];
        $row = $con->Listar("SELECT DISTINCT fechapago, DATE_FORMAT(fechapago, '%d/%m/%Y')
                FROM gr_pago_predial 
                WHERE parametrizacionanno = $parm_anno AND month(fechapago) = $mes 
                ORDER BY fechapago ");
        for ($i = 0; $i < count($row); $i++) {
            echo '<option value ="'.$row[$i][0].'">'.$row[$i][1].'</option>';
        }
    break;
    }
