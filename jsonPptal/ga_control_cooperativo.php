<?php
require '../Conexion/ConexionPDO.php';                                                  
require '../Conexion/conexion.php';                    
require './funcionesPptal.php';
require_once('../Conexion/conexionsql.php');
require '../ExcelR/Classes/PHPExcel/IOFactory.php';    
ini_set('max_execution_time', 0);
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$anno       = anno($panno);
$action     = $_REQUEST['action'];
$fechaa     = date('Y-m-d');
$calendario = CAL_GREGORIAN;
##******** Buscar Centro De Costo ********#
$cc         = $con->Listar("SELECT * FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $panno");
$c_costo    = $cc[0][0];
$pro        = $con->Listar("SELECT * FROM gf_proyecto WHERE nombre='Varios' AND compania = $compania");
$proyecto   = $pro[0][0]; 
switch ($action){
    #* Crear Configuracion Tipo 1 
    case 1:
        $conceptoA = $_REQUEST['conceptoA'];
        $conceptoF = $_REQUEST['conceptoF'];
        $rubroF    = $_REQUEST['rubroF'];
        $tipo      = $_REQUEST['tipo'];
        $sql_cons ="INSERT INTO `ga_configuracion_concepto` 
            ( `tipo`,`concepto_aporte`,
            `concepto_rubro`,`rubro_fuente`,
            `parametrizacionanno` ) 
        VALUES (:tipo, :concepto_aporte, 
            :concepto_rubro, :rubro_fuente, 
            :parametrizacionanno)";
        $sql_dato = array(
            array(":tipo",$tipo),
            array(":concepto_aporte",$conceptoA),
            array(":concepto_rubro",$conceptoF),
            array(":rubro_fuente",$rubroF),
            array(":parametrizacionanno",$panno),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    #* Eliminar
    case 2:
        $id         = $_REQUEST['id'];    
        $sql_cons  = "DELETE FROM `ga_configuracion_concepto` 
            WHERE `id_unico`=:id_unico";
        $sql_dato = array(
                array(":id_unico",$id),	
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    #* Validar Conceptos Configurados Pago
    case 3:
        $rta    = 1;
        $msj    = 'Conceptos Sin Configurar: '.'<br/>';
        $a_con  = array();
        if(!empty($_REQUEST['id'])){
            $rowp =$con->Listar("SELECT DISTINCT id_unico FROM ga_pago 
                WHERE id_unico = ".$_REQUEST['id']. " AND parametrizacionanno = $panno");
        } else {
            $fechaI = fechaC($_REQUEST['fecha']);
            $fechaF = fechaC($_REQUEST['fechaf']);
            $rowp =$con->Listar("SELECT DISTINCT id_unico FROM ga_pago 
            WHERE fecha BETWEEN '$fechaI' AND '$fechaF' AND parametrizacionanno = $panno");
        }
        for ($a = 0; $a < count($rowp); $a++) {
            $id_pago = $rowp[$a][0];
            #* Buscar Si falta algún concepto de configurar 
            $row = $con->Listar("SELECT DISTINCT p.id_unico, da.concepto, c.sigla, c.nombre, cc.id_unico 
                FROM ga_pago p 
                LEFT JOIN ga_detalle_pago dp ON p.id_unico = dp.pago 
                LEFT JOIN ga_detalle_aporte da ON dp.detalle_aporte = da.id_unico 
                LEFT JOIN ga_concepto c ON da.concepto = c.id_unico 
                LEFT JOIN ga_configuracion_concepto cc ON cc.concepto_aporte = c.id_unico 
                    AND cc.parametrizacionanno = $panno AND cc.tipo = 1 
                WHERE p.id_unico = $id_pago AND cc.id_unico IS NULL 
                HAVING (SELECT COUNT(dpa.id_unico) FROM ga_detalle_pago dpa WHERE dpa.pago = p.id_unico )>0");

            for ($i = 0; $i < count($row); $i++) {
                if(in_array($row[$i][1], $a_con)) {
                } else {
                    array_push ( $a_con , $row[$i][1] );
                    $msj .= $row[$i][2].' - '.$row[$i][3].'<br/>';
                    $rta +=1;
                }

            }
        }        
        $datos = array("msj"=>$msj,"rta"=>$rta);
        echo json_encode($datos); 
    break;
    #Buscar Tipo Comprobante Interfaz
    case 4:
        $row = $con->Listar("SELECT * FROM gf_tipo_comprobante WHERE interfaz_aportes = 1 AND compania = $compania");
        if(count($row)==1){
            echo 1;
        } else {
            echo 2;
        }
    break;
    # Guardar Interfaz Pagos
    case 5:        
        $rta     = 0;
        #****** Buscar Tipo Comprobante Interfaz ******#
        $comp = $con->Listar("SELECT id_unico, comprobante_pptal, tipo_comp_hom 
                FROM gf_tipo_comprobante 
                WHERE interfaz_aportes =1 AND compania = $compania");
        $tp_cnt     = $comp[0][0];
        $tp_pptal   = $comp[0][1];
        $tp_csc     = $comp[0][2];       
        
        IF(!empty($_REQUEST['id'])){
            $rowp = $con->Listar("SELECT DISTINCT p.id_unico FROM ga_pago WHERE id_unico = ".$_REQUEST['id']);
        } else {
            $fechaI = fechaC($_REQUEST['fecha']);
            $fechaF = fechaC($_REQUEST['fechaf']);
            $rowp =$con->Listar("SELECT DISTINCT id_unico FROM ga_pago 
            WHERE fecha BETWEEN '$fechaI' AND '$fechaF' AND parametrizacionanno = $panno");
        }
        for ($a = 0; $a < count($rowp); $a++) {
            $id_pago = $rowp[$a][0];
            $rowi = $con->Listar("SELECT DISTINCT cn.id_unico, dpp.comprobantepptal 
                FROM gf_comprobante_cnt cn 
                LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
                LEFT JOIN ga_detalle_pago dp ON dp.detalle_comprobante = dc.id_unico 
                LEFT JOIN gf_detalle_comprobante_pptal dpp ON dc.detallecomprobantepptal = dpp.id_unico 
                WHERE dp.pago = ".$id_pago);
            if(count($rowi)>0){
                
            } else {
                #****** Buscar Datos Básicos Para Comprobante ******#
                $cm = $con->Listar("SELECT p.fecha, p.numero, p.tercero, p.banco 
                    FROM  ga_pago p 
                    WHERE p.id_unico  =$id_pago ");
                $descripcion = 'Comprobante de Recaudo Aportes N°:'.$cm[0][1];
                $fecha       = $cm[0][0];
                $tercero     = $cm[0][2];
                $banco       = $cm[0][3];
                #*********** Crear Comprobantes *****************#
                $numeroC = numero ('gf_comprobante_cnt', $tp_cnt, $panno);
                #Insertamos el comprobante
                $sql_cons ="INSERT INTO `gf_comprobante_cnt`
                ( `numero`, `tipocomprobante`,
                `tercero`,`fecha`,
                `parametrizacionanno`,`estado`,
                `compania`, `descripcion`)
                VALUES (:numero, :tipocomprobante,
                :tercero, :fecha,
                :parametrizacionanno,:estado,
                :compania, :descripcion)";
                $sql_dato = array(
                    array(":numero",$numeroC),
                    array(":tipocomprobante",$tp_cnt),
                    array(":tercero",$tercero),
                    array(":fecha",$fecha),
                    array(":parametrizacionanno",$panno),
                    array(":estado",1),
                    array(":compania",$compania),
                    array(":descripcion",$descripcion),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                if(empty($resp)){
                    $idCnt=$con->Listar("SELECT MAX(id_unico) FROM gf_comprobante_cnt 
                        WHERE tipocomprobante = $tp_cnt and numero = $numeroC AND parametrizacionanno = $panno");
                    $id_cnt = $idCnt[0][0];
                }
                #Comprobante Pptal 
                if(!empty($tp_pptal)){
                    $sql_cons ="INSERT INTO `gf_comprobante_pptal`
                    ( `numero`, `tipocomprobante`,
                    `tercero`,`fecha`,
                    `parametrizacionanno`,
                    `estado`, `descripcion`)
                    VALUES (:numero, :tipocomprobante,
                    :tercero, :fecha,
                    :parametrizacionanno,
                    :estado, :descripcion)";
                    $sql_dato = array(
                        array(":numero",$numeroC),
                        array(":tipocomprobante",$tp_pptal),
                        array(":tercero",$tercero),
                        array(":fecha",$fecha),
                        array(":parametrizacionanno",$panno),
                        array(":estado",$estado),
                        array(":descripcion",$descripcion),
                    );
                    $resp       = $con->InAcEl($sql_cons,$sql_dato);
                    $idPPAL     = $con->Listar("SELECT id_unico FROM gf_comprobante_pptal 
                        WHERE tipocomprobante = $tp_pptal and numero = $numeroC AND parametrizacionanno = $panno");
                    $id_pptal   = $idPPAL[0][0];
                }
                #Causación
                if(!empty($tp_csc)){
                    $sql_cons ="INSERT INTO `gf_comprobante_cnt`
                    ( `numero`, `tipocomprobante`,
                    `tercero`,`fecha`,
                    `parametrizacionanno`,`estado`,
                    `compania`, `descripcion`)
                    VALUES (:numero, :tipocomprobante,
                    :tercero, :fecha,
                    :parametrizacionanno,:estado,
                    :compania, :descripcion)";
                    $sql_dato = array(
                        array(":numero",$numeroC),
                        array(":tipocomprobante",$tp_csc),
                        array(":tercero",$tercero),
                        array(":fecha",$fecha),
                        array(":parametrizacionanno",$panno),
                        array(":estado",1),
                        array(":compania",$compania),
                        array(":descripcion",$descripcion),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $idCau=$con->Listar("SELECT MAX(id_unico) FROM gf_comprobante_cnt 
                        WHERE tipocomprobante = $tp_csc and numero = $numeroC AND parametrizacionanno = $panno");
                    $id_causacion = $idCau[0][0];
                }

                $row = $con->Listar("SELECT DISTINCT 
                    dp.id_unico, da.concepto, dp.valor 
                FROM ga_pago p 
                LEFT JOIN ga_detalle_pago dp ON dp.pago = p.id_unico 
                LEFT JOIN ga_detalle_aporte da ON da.id_unico = dp.detalle_aporte 
                WHERE p.id_unico = $id_pago ");
                $c      = 0;
                $totalD = 0;
                $totalC = 0;
                for ($i = 0; $i < count($row); $i++) {
                    $detalle    = $row[$i][0];
                    $concepto   = $row[$i][1];
                    $valor      = $row[$i][2];

                    #Buscar Rubro Fuente Y Concepto Rubro 
                    $vg = $con->Listar("SELECT cf.concepto_rubro, cf.rubro_fuente 
                        FROM ga_configuracion_concepto cf   
                        WHERE cf.concepto_aporte = $concepto 
                        AND cf.tipo = 1  AND cf.parametrizacionanno = $panno");
                    $conceptoFinanciero = $vg[0][0];
                    $rubroFuente        = $vg[0][1];

                    if(!empty($conceptoFinanciero) && !empty($rubroFuente)){
                            $c +=1;
                            #********** Insertar Detalle Pptal*****************#
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                                ( `descripcion`,`valor`,
                                `comprobantepptal`,`rubrofuente`, `conceptoRubro`,
                                `tercero`, `proyecto`,`centro_costo`) 
                            VALUES (:descripcion, :valor, :comprobantepptal, :rubrofuente, 
                            :conceptoRubro, :tercero, :proyecto, :centro_costo)";
                            $sql_dato = array(
                                array(":descripcion",$descripcion),
                                array(":valor",$valor),
                                array(":comprobantepptal",$id_pptal),
                                array(":rubrofuente",$rubroFuente),
                                array(":conceptoRubro",$conceptoFinanciero),
                                array(":tercero",$tercero),
                                array(":proyecto",$proyecto),
                                array(":centro_costo",$c_costo),
                            );
                            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                            $id_dp = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_pptal");
                            $id_dp = $id_dp[0][0];
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
                                $valorguardar = $valor;
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
                                        $valorguardar = $valorguardar;
                                        $totalC +=$valorguardar;
                                    } else {
                                        $totalD +=$valorguardar*-1;
                                        $valorguardar = $valorguardar;
                                    }
                                }
                                $insertP ="";
                                $insertD ="";

                                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                        ( `fecha`, `comprobante`,`valor`,
                                        `cuenta`,`naturaleza`,`tercero`, `centrocosto`,
                                        `detallecomprobantepptal`, `proyecto`) 
                                VALUES (:fecha,  :comprobante,:valor, 
                                        :cuenta,:naturaleza, :tercero, :centrocosto,
                                        :detallecomprobantepptal, :proyecto)";
                                $sql_dato = array(
                                        array(":fecha",$fecha),
                                        array(":comprobante",$id_cnt),
                                        array(":valor",($valorguardar)),
                                        array(":cuenta",$cuentad),   
                                        array(":naturaleza",$naturad),
                                        array(":tercero",$tercero),
                                        array(":centrocosto",$c_costo),
                                        array(":detallecomprobantepptal",$id_dp),
                                        array(":proyecto",$proyecto),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                                $id_dc = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante 
                                    WHERE comprobante = $id_cnt");
                                $id_dc = $id_dc[0][0];
                                #****** Insertar Detalle Causacion **********#
                                if($cuentad != $cuentac){
                                    if(!empty($tp_csc)){
                                        $valorguardar       = $valor;
                                        ##Debito 
                                        if($naturad==1){
                                            $valord = ($valorguardar);
                                        } else {
                                            $valord = ($valorguardar)*-1;
                                        }
                                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                                ( `fecha`, `comprobante`,`valor`,
                                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`,
                                                `detalleafectado`, `proyecto`) 
                                        VALUES (:fecha,  :comprobante,:valor, 
                                                :cuenta,:naturaleza, :tercero, :centrocosto,
                                                :detalleafectado, :proyecto)";
                                        $sql_dato = array(
                                                array(":fecha",$fecha),
                                                array(":comprobante",$id_causacion),
                                                array(":valor",($valord)),
                                                array(":cuenta",$cuentad),   
                                                array(":naturaleza",$naturad),
                                                array(":tercero",$tercero),
                                                array(":centrocosto",$c_costo),
                                                array(":detalleafectado",$id_dc),
                                                array(":proyecto",$proyecto),
                                        );
                                        $resp = $con->InAcEl($sql_cons,$sql_dato);


                                        #** Credito 
                                        if($naturac==1){
                                            $valorc = ($valorguardar)*-1;
                                        } else {
                                            $valorc = ($valorguardar);
                                        }

                                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                                ( `fecha`, `comprobante`,`valor`,
                                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`,
                                                `detalleafectado`, `proyecto`) 
                                        VALUES (:fecha,  :comprobante,:valor, 
                                                :cuenta,:naturaleza, :tercero, :centrocosto,
                                                :detalleafectado, :proyecto)";
                                        $sql_dato = array(
                                                array(":fecha",$fecha),
                                                array(":comprobante",$id_causacion),
                                                array(":valor",($valorc)),
                                                array(":cuenta",$cuentac),   
                                                array(":naturaleza",$naturac),
                                                array(":tercero",$tercero),
                                                array(":centrocosto",$c_costo),
                                                array(":detalleafectado",$id_dc),
                                                array(":proyecto",$proyecto),
                                        );
                                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                                    }
                                }
                                #******* Actualizar Detalle Pago Predial *****#
                                $update = $con->Listar("UPDATE ga_detalle_pago 
                                   SET detalle_comprobante = $id_dc 
                                   WHERE id_unico = $detalle");  
                            }
                        }

                }
                #Buscar Cuenta De Banco 
                $sqlBanco = $con->Listar("SELECT cb.cuenta, c.naturaleza 
                            FROM gf_cuenta_bancaria cb 
                            LEFT JOIN gf_cuenta c ON cb.cuenta = c.id_unico 
                            WHERE cb.id_unico = ".$banco);
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
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `centrocosto`,
                         `proyecto`) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto,
                        :proyecto)";
                $sql_dato = array(
                        array(":fecha",$fecha),
                        array(":comprobante",$id_cnt),
                        array(":valor",($valorB)),
                        array(":cuenta",$cuentaB),   
                        array(":naturaleza",$Ncuenta),
                        array(":tercero",$tercero),
                        array(":centrocosto",$c_costo),
                        array(":proyecto",$proyecto),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);

                if(empty($resp)){
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
        }
        echo $rta;
    break;
    
    #*CREDITOS 
    #* Comprobar que las hojas existan
    case 6:
        $html = '';
        $rta  = 0;
        if (!empty($_FILES['flDoc']['tmp_name'])) {
            $file = $_FILES['flDoc']['tmp_name'];
            $objReader = new PHPExcel_Reader_Excel2007();
            $objPHPExcel = PHPExcel_IOFactory::load($file);
            
            try { 
                if ($objPHPExcel->setActiveSheetIndexByName('CREDITOS')) {
                    $objWorksheet = $objPHPExcel->setActiveSheetIndexByName('CREDITOS');   
                }
            } catch (Exception $e) { $html .= 'Hoja Créditos, No Encontrada'.'<br/>';$rta  += 1; }
        }
        $datos = array("html" => $html, "rta" => $rta);
        echo json_encode($datos);
    break;
    #* Verificar que los terceros existan en la bd SQL
    case 7:
        $html = 'Terceros No Encontrados:'.'<br/>';
        $rta  = 0;
        if (!empty($_FILES['flDoc']['tmp_name'])) {
            $file = $_FILES['flDoc']['tmp_name'];
            $objReader = new PHPExcel_Reader_Excel2007();
            $objPHPExcel = PHPExcel_IOFactory::load($file);
            
            if ($objPHPExcel->setActiveSheetIndexByName('CREDITOS')) {
                $objWorksheet3 = $objPHPExcel->setActiveSheetIndexByName('CREDITOS');   
                $total_filas3  = $objWorksheet3->getHighestRow();					
                for ($a = 2; $a <= $total_filas3; $a++) {
                    $nit       = $objWorksheet3->getCellByColumnAndRow(3, $a)->getCalculatedValue();
                    if(!empty($nit)){
                        $sqlcf1 = "SELECT COUNT(*) AS ps FROM PERSONA WHERE PERSONA.Numero_Documento = '".$nit."'";
                        $stmt41 = sqlsrv_query( $conn, $sqlcf1 ); 
                        $row41 = sqlsrv_fetch_array( $stmt41, SQLSRV_FETCH_ASSOC);
                        if($row41['ps']>0){}else {
                            $rta  += 1;
                            $html .= $nit.'<br/>';
                        }
                    }
                }
            }
        }
        $datos = array("html" => $html, "rta" => $rta);
        echo json_encode($datos);
    break;
    
    #* Validar Valor
    case 8:
        
        $htmlc  = 'Créditos Con Inconsistencias: '.'<br/>';
        $rta  = 0;
        if (!empty($_FILES['flDoc']['tmp_name'])) {
            $file = $_FILES['flDoc']['tmp_name'];
            $objReader = new PHPExcel_Reader_Excel2007();
            $objPHPExcel = PHPExcel_IOFactory::load($file);         
            #*************CREDITOS *****************#
            if ($objPHPExcel->setActiveSheetIndexByName('CREDITOS')) {
                $objWorksheet1  = $objPHPExcel->setActiveSheetIndexByName('CREDITOS'); 
                $total_filas    = $objWorksheet1->getHighestRow();					
                for ($a = 2; $a <= $total_filas; $a++){
                    $nit        = $objWorksheet1->getCellByColumnAndRow(3, $a)->getCalculatedValue();
                    if(!empty($nit)){
                        $mesF       = $_REQUEST['sltmes'];
                        $diaF       = cal_days_in_month($calendario, $mesF, $anno); 
                        $fechac     = $anno.'-'.$mesF.'-'.$diaF;
                        $valor      = $objWorksheet1->getCellByColumnAndRow(7, $a)->getCalculatedValue();
                        $abono      = $valor;
                        IF(!empty($abono)){
                            #** Buscar Créditos Saldo Del Tecero 
                            $sql = "SELECT DISTINCT c.Numero_Credito as Credito FROM CREDITO c
                                LEFT JOIN SOLICITUD_CREDITOS sc ON c.Id_Solicitud_Credito = sc.Identificador 
                                LEFT JOIN PERSONA_SOLICITUD  ps ON ps.Id_Solicitud = sc.Identificador 
                                LEFT JOIN PERSONA p ON ps.Id_persona =p.Numero_Documento 
                                WHERE p.Numero_Documento ='$nit' 
                                AND c.Id_Estado_Credito NOT IN (4,6)    
                                AND (SELECT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO dc WHERE dc.Numero_Credito = c.Numero_Credito)>0
                                AND ps.Principal=1 ORDER BY c.Numero_Credito";
                            $sql  = sqlsrv_query( $conn, $sql ); 
                            $saldo = 0;
                            $cds   ='';
                            while ($row = sqlsrv_fetch_array( $sql, SQLSRV_FETCH_ASSOC)){
                                $credito = $row['Credito'];
                                $dc = "SELECT SUM(Saldo_Concepto) as Saldo_C 
                                    FROM DETALLE_CREDITO 
                                    WHERE Numero_Credito ='$credito'
                                    AND Saldo_Concepto>0;";
                                $dc = sqlsrv_query( $conn, $dc ); 
                                $rowdc  = sqlsrv_fetch_array( $dc, SQLSRV_FETCH_ASSOC);
                                $saldo += $rowdc['Saldo_C'];
                                $cds   .= $credito.' ';
                            }
                            
                            if($abono>$saldo){
                                $htmlc  .= 'Número Crédito: '.$cds.' Tercero = '.$nit.'</br>';
                                $rta  += 1;
                            }                       
                        }
                    }
                }
            }   
        }
        $html   =  $htmlc;
        $datos  = array("html" => $html, "rta" => $rta);
        echo json_encode($datos);
    break;
    
    #* Guardat Datos
    case 9:        
        $htmlc  = 'Créditos Guardados: '.'<br/>';
        $rta  = 0;
        if (!empty($_FILES['flDoc']['tmp_name'])) {
            $file = $_FILES['flDoc']['tmp_name'];
            $objReader = new PHPExcel_Reader_Excel2007();
            $objPHPExcel = PHPExcel_IOFactory::load($file);
         
            #*************CREDITOS *****************#
            if ($objPHPExcel->setActiveSheetIndexByName('CREDITOS')) {
                $objWorksheet1  = $objPHPExcel->setActiveSheetIndexByName('CREDITOS'); 
                $total_filas    = $objWorksheet1->getHighestRow();					
                for ($a = 2; $a <= $total_filas; $a++){
                    $nit        = $objWorksheet1->getCellByColumnAndRow(3, $a)->getCalculatedValue();
                    if(!empty($nit)){
                        $mesF       = $_REQUEST['sltmes'];
                        $diaF       = cal_days_in_month($calendario, $mesF, $anno); 
                        $fechac     = $anno.'-'.$mesF.'-'.$diaF;
                        $valor      = $objWorksheet1->getCellByColumnAndRow(7, $a)->getCalculatedValue();
                        $abono      = $valor;
                        IF(!empty($abono)){
                            #** Buscar Créditos Saldo Del Tecero 
                            $sql = "SELECT DISTINCT c.Numero_Credito as Credito FROM CREDITO c
                                LEFT JOIN SOLICITUD_CREDITOS sc ON c.Id_Solicitud_Credito = sc.Identificador 
                                LEFT JOIN PERSONA_SOLICITUD  ps ON ps.Id_Solicitud = sc.Identificador 
                                LEFT JOIN PERSONA p ON ps.Id_persona =p.Numero_Documento 
                                WHERE p.Numero_Documento ='$nit' 
                                AND c.Id_Estado_Credito NOT IN (4,6)    
                                AND (SELECT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO dc WHERE dc.Numero_Credito = c.Numero_Credito)>0
                                AND ps.Principal=1 ORDER BY c.Numero_Credito";
                            $sql  = sqlsrv_query( $conn, $sql ); 
                            $saldo = 0;
                            $cds   ='';
                            while ($row = sqlsrv_fetch_array( $sql, SQLSRV_FETCH_ASSOC)){
                                $credito = $row['Credito'];
                                $dc = "SELECT SUM(Saldo_Concepto) as Saldo_C 
                                    FROM DETALLE_CREDITO 
                                    WHERE Numero_Credito ='$credito'
                                    AND Saldo_Concepto>0;";
                                $dc = sqlsrv_query( $conn, $dc ); 
                                $rowdc  = sqlsrv_fetch_array( $dc, SQLSRV_FETCH_ASSOC);
                                $saldo += $rowdc['Saldo_C'];
                            }
                            
                            if($abono>$saldo){
                            }  else {    
                                $htmlc .=$nit.': $'.$valor.'<br/>';
                                $rta +=1;
                                $nc  = '';
                                $numero_recibo = '';
                                while($abono>0){
                                    $sql = "SELECT DISTINCT c.Numero_Credito as Credito FROM CREDITO c
                                        LEFT JOIN SOLICITUD_CREDITOS sc ON c.Id_Solicitud_Credito = sc.Identificador 
                                        LEFT JOIN PERSONA_SOLICITUD  ps ON ps.Id_Solicitud = sc.Identificador 
                                        LEFT JOIN PERSONA p ON ps.Id_persona =p.Numero_Documento 
                                        WHERE p.Numero_Documento ='$nit' 
                                        AND c.Id_Estado_Credito NOT IN (4,6)    
                                        AND (SELECT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO dc WHERE dc.Numero_Credito = c.Numero_Credito)>0
                                        AND ps.Principal=1 ORDER BY c.Numero_Credito";
                                    $sql  = sqlsrv_query( $conn, $sql ); 
                                    while ($row = sqlsrv_fetch_array( $sql, SQLSRV_FETCH_ASSOC)){
                                        if($abono>0){
                                            $credito = $row['Credito'];
                                            $dc = "SELECT Id_Linea, Tipo_Linea, Id_Concepto, 
                                                Numero_Cuota, Valor_Concepto, Saldo_Concepto 
                                                FROM DETALLE_CREDITO 
                                                WHERE Numero_Credito ='$credito'
                                                AND Saldo_Concepto>0 ORDER BY Numero_Cuota;";
                                            $dc = sqlsrv_query( $conn, $dc ); 
                                            while ($rowdc  = sqlsrv_fetch_array( $dc, SQLSRV_FETCH_ASSOC)){
                                                $saldo = $rowdc['Saldo_Concepto'];
                                                if($abono>$saldo){
                                                    $vg     = $saldo;
                                                    $abono -= $saldo;
                                                } else {
                                                    $vg     = $abono;
                                                    $abono  = 0;
                                                }
                                                if($vg>0){
                                                    if($nc != $credito){
                                                        #*** CREAR ENCABEZADO PAGO 
                                                        $sqlca = "SELECT MAX(Numero_Recibo) AS Num_c FROM PAGOS";
                                                        $sqlca = sqlsrv_query( $conn, $sqlca ); 
                                                        $rownc  = sqlsrv_fetch_array( $sqlca, SQLSRV_FETCH_ASSOC);
                                                        if(empty($rownc['Num_c'])){
                                                            $numero_recibo = 1;
                                                        } else {
                                                            $numero_recibo = $rownc['Num_c']+1;
                                                        }
                                                        #** crear detalle
                                                        $sqlca = "INSERT INTO PAGOS 
                                                            (Numero_Recibo, Numero_Credito,Id_Documento,
                                                            Numero_Documento_Soporte,Fecha_Pago,Id_Banco,
                                                            Valor,Id_Estado_Pago,Observaciones,
                                                            Fecha_Creacion,Responsable_Creacion) 
                                                            VALUES(?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?)";

                                                        $var= array("$numero_recibo", "$credito",900, 
                                                            "$numero_recibo", "$fechac", 1,
                                                            $valor, 1,"Pago Archivo","$fechac", 1);
                                                        if (!sqlsrv_query($conn, $sqlca, $var)){} 
                                                        $nc = $credito;
                                                    }
                                                    #*¨Guardar DTP
                                                    $sqlca = "INSERT INTO DETALLE_PAGO 
                                                        (Numero_Recibo, Numero_Credito, Id_Linea,
                                                        Tipo_Linea,Id_Concepto,Numero_Cuota,
                                                        Numero_Pago,Valor_pago,Pagado,
                                                        Observaciones,Fecha_Creacion,Responsable_Creacion) 
                                                        VALUES(?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?,?)";

                                                    $var= array("$numero_recibo", "$credito",$rowdc['Id_Linea'], 
                                                        $rowdc['Tipo_Linea'], $rowdc['Id_Concepto'], $rowdc['Numero_Cuota'],
                                                        1,$vg, 1,
                                                        "Pago Archivo","$fechaa", 1);
                                                    if (!sqlsrv_query($conn, $sqlca, $var)){
                                                    } else {
                                                        $sc = $rowdc['Saldo_Concepto']-$vg;
                                                        #** ACTUALIZAR DETALLE CRÉDITO
                                                        $tsql = "UPDATE DETALLE_CREDITO 
                                                                SET Saldo_Concepto = (?), 
                                                                Valor_Abono = (?)
                                                                WHERE Numero_Credito = (?) 
                                                                AND Id_Linea = (?) 
                                                                AND Tipo_Linea = (?)
                                                                AND Id_Concepto = (?)
                                                                AND Numero_Cuota = (?)";  

                                                       $params = array($sc, $vg,"$credito",$rowdc['Id_Linea'], 
                                                        $rowdc['Tipo_Linea'], $rowdc['Id_Concepto'], $rowdc['Numero_Cuota']);  
                                                       if (!sqlsrv_query($conn, $tsql, $params)) {  
                                                       }
                                                    }
                                                } else {
                                                    break;
                                                }
                                            }
                                        } else {
                                            break;
                                        }
                                    }
                                }
                                                       
                            }
                        }
                    }
                }   
            }
        }
        $html   =  $htmlc;
        $datos  = array("html" => $html, "rta" => $rta);
        echo json_encode($datos);
    break;
    
    
}