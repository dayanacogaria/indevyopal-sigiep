<?php
######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#18/07/2018 |Erica G. | Centro Costo Presupuestal Privada
#29/06/2018 | Erica G. | Egreso Por Proveedor
#27/06/2018 | Erica G. | Egreso Por Proveedor
#22/02/2018 | ERICA G. | CUENTA EQUIVALENTE
#23/01/2018 | ERICA G. | EGRESOS VIGENCIA ANTERIOR
#15/09/2017 | ERICA G. | EGRESO PARA INTERFAZ DE NOMINA
#30/08/2017 | ERICA G. |ARCHIVO CREADO
############################################################################################
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
require_once './funcionesPptal.php';
session_start();
$anno       = $_SESSION['anno'];
$user       = $_SESSION['usuario'];
$fechaElab  = date('Y-m-d');
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$con        = new ConexionPDO();
switch ($_REQUEST['action']) {
    #***Guardar Parte CNT de Egreso Nomina Interfaz*****#
    case 1:
        $egreso = $_POST['egr'];
        #**********************Crear CNT*************###
        $te = "SELECT id_unico, numero, tipocomprobante, fecha, descripcion,numerocontrato, "
                . "parametrizacionanno,clasecontrato, tercero,estado,responsable "
                . "FROM gf_comprobante_pptal "
                . "WHERE id_unico =$egreso";
        $te = $mysqli->query($te);
        $te = mysqli_fetch_row($te);
        $id = $te[0];
        $numero = $te[1];
        $tipo = $te[2];
        $fecha = $te[3];
        if (empty($te[4])) {
            $descripcion = 'NULL';
        } else {
            $descripcion = '"' . $te[4] . '"';
        }
        if (empty($te[5])) {
            $numerocontrato = 'NULL';
        } else {
            $numerocontrato = '"' . $te[5] . '"';
        }

        if (empty($te[7])) {
            $clasecontrato = 'NULL';
        } else {
            $clasecontrato = '"' . $te[7] . '"';
        }

        $parametrizacion = $te[6];
        $tercero = $te[8];
        $estado = $te[9];
        $compania = $_SESSION['compania'];

        ###INSERTAR COMPROBANTE CNT#####
        ###BUSCAR TIPÓ CNT ###
        $tipoc = "SELECT id_unico FROM gf_tipo_comprobante WHERE comprobante_pptal = $tipo";
        $tipoc = $mysqli->query($tipoc);
        $tipoc = mysqli_fetch_row($tipoc);
        $tipoc = $tipoc[0];

        $insertc = "INSERT INTO gf_comprobante_cnt (numero, fecha, "
                . "descripcion, valorbase, valorbaseiva, valorneto, numerocontrato,"
                . "tipocomprobante, compania, parametrizacionanno, tercero, estado, "
                . "clasecontrato, formapago) "
                . "VALUES ('$numero', '$fecha', "
                . "$descripcion, 0,0,0, $numerocontrato, "
                . "$tipoc, $compania, $parametrizacion, $tercero, 1, "
                . "$clasecontrato, NULL)";
        $insertc = $mysqli->query($insertc);
        $insertc = true;
        if ($insertc == true) {
            $sqlUltComC = "SELECT MAX(id_unico) FROM gf_comprobante_cnt "
                    . "WHERE tipocomprobante = $tipoc AND numero =$numero ";
            $ultComC = $mysqli->query($sqlUltComC);
            $rowUC = mysqli_fetch_row($ultComC);
            $ultimoComproCnt = $rowUC[0];
            #****Detalles que trae el egreso*****#
            $de = "SELECT DISTINCT id_unico, comprobanteafectado FROM gf_detalle_comprobante_pptal
                                WHERE comprobantepptal = $egreso ";
            $de = $mysqli->query($de);
            $pasn = 0;
            while ($row = mysqli_fetch_row($de)) {
                $dp = $row[1];
                $bus = "SELECT dc.* FROM gf_detalle_comprobante dc "
                        . "LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico "
                        . "WHERE dc.detallecomprobantepptal = $dp AND c.clasecuenta = 20";

                $bus = $mysqli->query($bus);
                if (mysqli_num_rows($bus) > 0) {
                    $pasn += 1;
                }
            }

            #SI hay cuentas de pasivo nómina relacionadas
            if ($pasn > 0) {
                ###***Traer las cuenta por pagar del Egreso ***##
                $cxpe = "SELECT DISTINCT ca.comprobantepptal, dcnt.comprobante FROM gf_detalle_comprobante_pptal dc 
                                        LEFT JOIN gf_detalle_comprobante_pptal ca ON dc.comprobanteafectado = ca.id_unico 
                                        INNER JOIN gf_detalle_comprobante dcnt ON ca.id_unico = dcnt.detallecomprobantepptal 
                                        WHERE dc.comprobantepptal = $egreso";
                $query = $mysqli->query($cxpe);
                if (mysqli_num_rows($query) > 0) {
                    while ($row1 = mysqli_fetch_row($query)) {
                        ##**Traer detalles cnt donde cuenta sea de tipo pasivo nomina 
                        $comp = $row1[1];
                        $dc = "SELECT dc.id_unico, dc.valor, dc.valorejecucion, 
                                            dc.cuenta, dc.naturaleza, dc.tercero, dc.proyecto , dc.centrocosto
                                                        FROM gf_detalle_comprobante dc 
                                                        LEFT JOIN gf_cuenta c ON c.id_unico =dc.cuenta
                                                        WHERE dc.comprobante = $comp AND c.clasecuenta = 20";
                        $dc = $mysqli->query($dc);
                        if (mysqli_num_rows($dc) > 0) {
                            while ($row2 = mysqli_fetch_row($dc)) {
                                $id = $row2[0];
                                $valor = $row2[1] * -1;
                                $valore = $row2[2];
                                $cuenta = $row2[3];
                                $naturaleza = $row2[4];
                                $tercero = $row2[5];
                                $proyecto = $row2[6];
                                if(empty($row2[7])){
                                    $cc = 'NULL';
                                } else {
                                    $cc = $row2[7];
                                }
                                $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                        . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                        . "cuenta, naturaleza, tercero,  proyecto, detalleafectado, centrocosto) "
                                        . "VALUES ('$fecha',$descripcion, $valor,$valore, $ultimoComproCnt,  "
                                        . "$cuenta, $naturaleza,$tercero, $proyecto, $id,$cc)";
                                $insertcntd = $mysqli->query($insertcntd);
                            }
                        }
                    }
                }
            } else {
                ###***Traer las cuenta por pagar del Egreso ***##
                $cxpe = "SELECT DISTINCT ca.comprobantepptal, dcnt.comprobante FROM gf_detalle_comprobante_pptal dc 
                                        LEFT JOIN gf_detalle_comprobante_pptal ca ON dc.comprobanteafectado = ca.id_unico 
                                        LEFT JOIN gf_detalle_comprobante dcnt ON ca.id_unico = dcnt.detallecomprobantepptal 
                                        WHERE dc.comprobantepptal = $egreso";
                $query = $mysqli->query($cxpe);
                if (mysqli_num_rows($query) > 0) {
                    while ($row1 = mysqli_fetch_row($query)) {
                        ##**Traer detalles cnt donde cuenta sea de tipo pasivo nomina 
                        $comp = $row1[1];
                        $dc = "SELECT DISTINCT dc.id_unico, dc.valor, dc.valorejecucion, 
                                            dc.cuenta, dc.naturaleza, dc.tercero, dc.proyecto, dc.centrocosto 
                                                        FROM gf_detalle_comprobante dc 
                                                        LEFT JOIN gf_cuenta c ON c.id_unico =dc.cuenta
                                                        WHERE dc.comprobante = $comp AND (c.clasecuenta = 4 OR c.clasecuenta = 8)";
                        $dc = $mysqli->query($dc);
                        if (mysqli_num_rows($dc) > 0) {
                            while ($row2 = mysqli_fetch_row($dc)) {
                                $id = $row2[0];
                                $valor = $row2[1] * -1;
                                $valore = $row2[2];
                                $cuenta = $row2[3];
                                $naturaleza = $row2[4];
                                $tercero = $row2[5];
                                $proyecto = $row2[6];
                                if(empty($row2[7])){
                                    $cc = 'NULL';
                                } else {
                                    $cc = $row2[7];
                                }
                                $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                        . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                        . "cuenta, naturaleza, tercero,  proyecto, detalleafectado, centrocosto) "
                                        . "VALUES ('$fecha',$descripcion, $valor,$valore, $ultimoComproCnt,  "
                                        . "$cuenta, $naturaleza,$tercero, $proyecto, $id,$cc)";
                                $insertcntd = $mysqli->query($insertcntd);
                            }
                        }
                    }
                }
            }
            $result = 1;
            $_SESSION['idCompCntV'] = $ultimoComproCnt;
            $_SESSION['nuevo_GE'] = 1;
            $_SESSION['idCompCnt'] = $ultimoComproCnt;
        } else {
            $result = 2;
        }
        echo $result;

        break;
    #***Verificar que la cuenta por pagar sea de interfaz***##
    case 2:
        $result = 0;
        $cuentapagar = $_POST['id'];
        if(!empty($cuentapagar)){
            $query = "SELECT 
                    cp.id_unico, tc.interface 
                FROM 
                    gf_comprobante_pptal cp 
                LEFT JOIN 
                    gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico 
                LEFT JOIN 
                    gf_tipo_comprobante tc ON tc.comprobante_pptal = tcp.id_unico 
                WHERE 
                    cp.id_unico =$cuentapagar";
            $query = $mysqli->query($query);
            if (mysqli_num_rows($query) > 0) {
                $query = mysqli_fetch_row($query);
                $result = $query[1];
            }
        
        }
        echo $result;
    break;
    #***Cuentas Por Pagar Vigencia Anterior
    case 3:
        $tercero = $_REQUEST['tercero'];
        #Buscar Año Anterior
        $nannoa = anno($anno);
        $nannoan = $nannoa-1;
        $busc = $con->Listar("SELECT id_unico FROM gf_parametrizacion_anno WHERE anno = $nannoan AND compania = $compania");
        //echo "SELECT id_unico FROM gf_parametrizacion_anno WHERE anno = $nannoan and compania = $compania";
        if(count($busc) >0){ 
            $annoan = $busc[0][0];
            $queryComp = "SELECT  com.id_unico, com.numero, com.fecha, com.descripcion
                            FROM gf_comprobante_pptal com
                            left join gf_tipo_comprobante_pptal tipoCom on tipoCom.id_unico = com.tipocomprobante
                            WHERE tipoCom.clasepptal = 16 
                            and tipoCom.tipooperacion = 1
                            and com.tercero =  $tercero 
                            AND com.parametrizacionanno <= $annoan";
            $comprobanteP = $mysqli->query($queryComp);
            while ($row = mysqli_fetch_row($comprobanteP)) {
                $saldDisp = 0;
                $totalSaldDispo = 0;
                $queryDetCompro = "SELECT detComp.id_unico, detComp.valor   
                        FROM gf_detalle_comprobante_pptal detComp, gf_comprobante_pptal comP 
                        WHERE comP.id_unico = detComp.comprobantepptal 
                        AND comP.id_unico = " . $row[0];
                $detCompro = $mysqli->query($queryDetCompro);
                while ($rowDetComp = mysqli_fetch_row($detCompro)) {
                    $saldDisp += $rowDetComp[1];
                    $queryDetAfe = "SELECT
                              dcp.valor,
                              tc.tipooperacion
                            FROM
                              gf_detalle_comprobante_pptal dcp
                            LEFT JOIN
                              gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                            LEFT JOIN
                              gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico
                            WHERE
                              dcp.comprobanteafectado =" . $rowDetComp[0];
                    $detAfec = $mysqli->query($queryDetAfe);
                    while ($rowDtAf = mysqli_fetch_row($detAfec)) {
                        if ($rowDtAf[1] == 3) {
                            $saldDisp = $saldDisp - $rowDtAf[0];
                        } else {
                            if (($rowDtAf[1] == 2) || ($rowDtAf[1] == 4)) {
                                $saldDisp = $saldDisp + $rowDtAf[0];
                            } else {
                                $saldDisp = $saldDisp - $rowDtAf[0];
                            }
                        }
                    }
                }
                $saldo = $saldDisp;
                if ($saldo > 0) {
                    $fecha_div = explode("-", $row[2]);
                    $anio = $fecha_div[0];
                    $mes = $fecha_div[1];
                    $dia = $fecha_div[2];
                    $fecha = $dia . "/" . $mes . "/" . $anio;

                    echo '<option value="' . $row[0] . '">' . $row[1] . ' ' . $fecha . ' ' . ucwords(mb_strtolower($row[3])) . ' $' . number_format($saldo, 2, '.', ',') . '</option>';
                }
            }
        } else {
            echo 0;
        }
    break;
    #***Validar Que Haya Rubros Clase Cuentas por Pagar
    case 4:
        $rb = $con->Listar("SELECT * FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 15 AND tipovigencia =5 ");
        if(count($rb)>0){
            $rta = 0;
        } else {
            $rta = 1;
        }
        echo $rta;
    break;
    #***Guardar Egreso Vigencia Anterior No Retenciones
    case 5:
        $tercero            = $_REQUEST['tercero'];
        $tipocomprobante    = $_REQUEST['tipocomprobante'];
        $numero             = $_REQUEST['numero'];
        $fechaf             = $_REQUEST['fecha'];
        $fecha              = fechaC($fechaf);
        $fechaVen           = fechaSum($fecha);
        $estado             = 3;
        $responsable        = $_SESSION['usuario_tercero'];
        $cxp                = $_REQUEST['cxp'];
        $html               ="";
        $rta                =0;
        #** Buscar Datos Básico Cuenta Por Pagar **#
        $rowComp = $con ->Listar("SELECT DISTINCT 
                    comp.descripcion, 
                    comp.numerocontrato, 
                    comp.clasecontrato, 
                    dc.comprobante 
                FROM 
                    gf_comprobante_pptal comp 
                LEFT JOIN 
                    gf_detalle_comprobante_pptal dcp ON comp.id_unico = dcp.comprobantepptal 
                LEFT JOIN 
                    gf_detalle_comprobante dc ON dcp.id_unico = dc.detallecomprobantepptal 
      		WHERE comp.id_unico =  $cxp");
        $cxpcnt = $rowComp[0][3];
        if(empty($rowComp[0][0])){
            $descripcion = NULL;
        } else {
            $descripcion = $rowComp[0][0];
        }
        if(empty($rowComp[0][1])){
            $numContrato = NULL;
        } else {
            $numContrato = $rowComp[0][1];
        }
        if(empty($rowComp[0][2])){
            $claseContrato = NULL;
        } else {
            $claseContrato = $rowComp[0][2];
        }
        $sql_cons ="INSERT INTO `gf_comprobante_pptal`  
                    ( `numero`,`fecha`,`fechavencimiento`,`descripcion`,
                    `parametrizacionanno`,`tipocomprobante`,`tercero`,
                    `estado`,`responsable`,`numerocontrato`,`clasecontrato`,
                    `usuario`,`fecha_elaboracion` ) 
                VALUES (:numero,:fecha,:fechavencimiento,:descripcion,
                :parametrizacionanno,:tipocomprobante,:tercero,
                :estado,:responsable,:numerocontrato,:clasecontrato,
                :usuario,:fecha_elaboracion)";
        $sql_dato = array(
        array(":numero",$numero),
        array(":fecha",$fecha),
        array(":fechavencimiento",$fechaVen),
        array(":descripcion",$descripcion),
        array(":parametrizacionanno",$anno),
        array(":tipocomprobante",$tipocomprobante),
        array(":tercero",$tercero),
        array(":estado",$estado),
        array(":responsable",$responsable),
        array(":numerocontrato",$numContrato),
        array(":clasecontrato",$claseContrato),
        array(":usuario",$user),
        array(":fecha_elaboracion",$fechaElab),
            );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if (empty($obj_resp)) {
            #Buscar Id Comprobante Registrado
            $ui = $con->Listar("SELECT MAX(id_unico) FROM gf_comprobante_pptal 
                        WHERE tipocomprobante = '$tipocomprobante' AND numero = $numero");
            $comprobantepptal = $ui[0][0];
            
            #************** Buscar Detalles Pptal De La CxP *****************#
            $det = $con->Listar("SELECT 
                    dc.id_unico, 
                    dc.descripcion, 
                    dc.valor, 
                    dc.rubrofuente, 
                    dc.tercero, 
                    dc.proyecto, 
                    rb.codi_presupuesto, 
                    dc.centro_costo 
                FROM gf_detalle_comprobante_pptal dc
                LEFT JOIN gf_rubro_fuente rf on dc.rubrofuente = rf.id_unico 
                LEFT JOIN gf_rubro_pptal rb on rf.rubro = rb.id_unico  
                WHERE dc.comprobantepptal = $cxp");
            
            for ($i = 0; $i < count($det); $i++) {
                $id_detalle     = $det[$i][0];
                $valorDetalle   = $det[$i][2];
                $codRubro       = $det[$i][6];
                if(empty($det[$i][5])){
                    $proyecto = 2147483647;
                } else {
                    $proyecto =$det[$i][5];
                }
                
                if(empty($det[$i][7])){
                    $cc = NULL;
                } else {
                    #* Buscar Centro Costo Vigencia Actual *#
                    $idcc = $det[$i][7];
                    $ccva = $con->Listar("SELECT ccva.id_unico 
                                FROM 
                                    gf_centro_costo cc 
                                LEFT JOIN 
                                    gf_centro_costo ccva ON  cc.nombre = ccva.nombre 
                                WHERE ccva.parametrizacionanno = $anno AND cc.id_unico = $idcc");
                    if(count($ccva)>0){
                        $cc = $ccva[0][0];
                    } else {
                        $cc =NULL;
                    }
                }
                #Validar Si Detalle Tiene Saldo Sin Afectar
                $afect = 0;
                #Buscar Valor Donde El Afectado Sea El Detalle
                $dca = $con->Listar("SELECT valor   
                                    FROM gf_detalle_comprobante_pptal   
                                    WHERE comprobanteafectado = $id_detalle");
                for($j=0; $j < count($dca); $j++){
                    $afect += $dca[$j][0];
                }
                #Si El Saldo >0
                $saldo = $valorDetalle - $afect;
                if($saldo>0){
                    #Buscar El Rubro Vigencia Anterior Correspondiente 
                    $sqlrva = $con->Listar("SELECT id_unico FROM gf_rubro_pptal 
                            WHERE parametrizacionanno = $anno 
                            AND equivalente = '$codRubro' AND tipoclase = 15 AND tipovigencia = 5");
                    if(count($sqlrva)>0){
                        $rva = $sqlrva[0][0];
                        #Buscar Rubro Fuente 
                        $sqlrfva = $con->Listar("SELECT id_unico FROM gf_rubro_fuente WHERE rubro = $rva");
                        if(count($sqlrfva)>0){
                            $rfva = $sqlrfva[0][0];
                            #Registrar Detalle Pptal 
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal`  
                                    ( `descripcion`,
                                    `valor`,`comprobantepptal`,`rubrofuente`,
                                    `tercero`,`proyecto`,`comprobanteafectado`, `centro_costo`) 
                                VALUES (:descripcion,
                                :valor,:comprobantepptal,:rubrofuente,
                                :tercero,:proyecto,:comprobanteafectado, :centro_costo )";
                            $sql_dato = array(
                            array(":descripcion",$descripcion),
                            array(":valor",$saldo),
                            array(":comprobantepptal",$comprobantepptal),
                            array(":rubrofuente",$rfva),
                            array(":tercero",$tercero),
                            array(":proyecto",$proyecto),
                            array(":comprobanteafectado",$id_detalle),
                            array(":centro_costo",$cc),
                                );
                            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                            
                        } else {
                            $html  .="Rubro $codRubro No Tiene Fuente Configurada Para Vigencia Anterior".'<br/>';
                            $rta     += 1;
                        }
                    } else {
                        $html  .="Rubro $codRubro No se Encontró Equivalente".'<br/>';
                        $rta    += 1;
                    }
                    
                }
                
            }
            #*************** Registrar Cnt **********************************#
            #***** Buscar Tipo Comprobante Cnt Homologado 
            $tipo = $con->Listar("SELECT id_unico FROM gf_tipo_comprobante WHERE comprobante_pptal = $tipocomprobante");
            $tipocomprobantecnt = $tipo[0][0];
            #***** Guardar Comprobante CNT ****#
            $sql_cons ="INSERT INTO `gf_comprobante_cnt`  
                    ( `numero`,`fecha`,`descripcion`,
                    `parametrizacionanno`,`tipocomprobante`,`tercero`,
                    `estado`,`numerocontrato`,`clasecontrato`,
                    `usuario`,`fecha_elaboracion`, `compania` ) 
                VALUES (:numero,:fecha,:descripcion,
                :parametrizacionanno,:tipocomprobante,:tercero,
                :estado,:numerocontrato,:clasecontrato,
                :usuario,:fecha_elaboracion,:compania)";
            $sql_dato = array(
            array(":numero",$numero),
            array(":fecha",$fecha),
            array(":descripcion",$descripcion),
            array(":parametrizacionanno",$anno),
            array(":tipocomprobante",$tipocomprobantecnt),
            array(":tercero",$tercero),
            array(":estado",1),
            array(":numerocontrato",$numContrato),
            array(":clasecontrato",$claseContrato),
            array(":usuario",$user),
            array(":fecha_elaboracion",$fechaElab),
            array(":compania",$compania),
                );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            if (empty($obj_resp)) {
                $sqlcn = $con->Listar("SELECT MAX(id_unico) FROM gf_comprobante_cnt 
                        WHERE tipocomprobante = $tipocomprobantecnt AND numero = '$numero'");
                $comprobantecnt = $sqlcn[0][0];
                
                #************ Buscar Detalles Cnt De La Cuenta X Pagar *************#
                 
                $rowdc = $con->Listar("SELECT 
                        dt.id_unico, 
                        dt.valor, 
                        dt.proyecto, 
                        dt.cuenta, 
                        dt.centrocosto, 
                        dt.tercero, 
                        ct.naturaleza, 
                        ct.codi_cuenta, 
                        dc.detallecomprobantepptal
	            	FROM gf_detalle_comprobante dt 
	            	LEFT JOIN gf_cuenta ct ON dt.cuenta = ct.id_unico 
	            	LEFT JOIN gf_clase_cuenta cc ON cc.id_unico = ct.clasecuenta 
	            	WHERE (dt.comprobante = $cxpcnt and cc.id_unico = 4) 
	            	OR (dt.comprobante = $cxpcnt and cc.id_unico = 8)");
                for ($i = 0; $i < count($rowdc); $i++) {
                    $idd = $rowdc[$i][0];
                    $valorp = $rowdc[$i][1];
                    #*** Buscar Si Tiene Afectado ***#
                    $af = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) 
                        FROM gf_detalle_comprobante dc WHERE dc.detalleafectado = $idd ");
                    if(empty($af[0][0])){
                        $afect =0;
                    } else {
                        $afect =$af[0][0];
                    }
                    if($valorp<0){$valorp=$valorp*-1-$afect;} 
                    else {$valorp=$valorp-$afect;}
                    $cuentava = $rowdc[$i][7];
                    $cuentaA        =  "";
                    $naturalezaA    =  "";
                    #Buscar Si Existe Equivalente Cuenta Para Vigencia Actual Vigencia Anterior 
                    $bcvaa = $con->Listar("SELECT id_unico, naturaleza FROM gf_cuenta 
                               WHERE parametrizacionanno = $anno AND equivalente_va = $cuentava");
                    if(count($bcvaa)> 0){
                        $cuentaA        =  $bcvaa[0][0];
                        $naturalezaA    =  $bcvaa[0][1];
                    } else {
                    #Buscar Si Existe Cuenta Para Vigencia Actual Vigencia Anterior
                    $bcva = $con->Listar("SELECT id_unico, naturaleza FROM gf_cuenta 
                               WHERE parametrizacionanno = $anno AND codi_cuenta = '$cuentava'");
                        if(count($bcva)> 0){
                            $cuentaA        =  $bcva[0][0];
                            $naturalezaA    =  $bcva[0][1];
                        } 
                    }
                    if($cuentaA !="" && $naturalezaA !="" ){
                        if($naturalezaA == $rowdc[$i][6]){
                            $valor = $valorp *-1;
                        } else {
                            if($naturalezaA==1){
                                if($rowdc[$i][1]<0){
                                    $valor = $valorp *-1;
                                } else {
                                    $valor = $valorp;
                                }
                            } else {
                                if($rowdc[$i][1]>0){
                                    $valor = $valorp *-1;
                                } else {
                                    $valor = $valorp;
                                }
                            }
                        }
                        if(empty($rowdc[$i][4])){
                            $centrocosto =NULL;
                        }else {
                            #* Buscar Centro Costo Vigencia Actual *#
                            $idcc = $rowdc[$i][4];
                            $ccva = $con->Listar("SELECT ccva.id_unico 
                                        FROM 
                                            gf_centro_costo cc 
                                        LEFT JOIN 
                                            gf_centro_costo ccva ON  cc.nombre = ccva.nombre 
                                        WHERE ccva.parametrizacionanno = $anno AND cc.id_unico = $idcc");
                            if(count($ccva)>0){
                                $centrocosto = $ccva[0][0];
                            } else {
                                $centrocosto =NULL;
                            }
                        }
                        if(empty($rowdc[$i][2])){
                            $proyecto =NULL;
                        }else {
                            $proyecto = $rowdc[$i][2];
                        }
                        if(empty($rowdc[$i][5])){
                            $terceroD =$tercero;
                        }else {
                            $terceroD = $rowdc[$i][5];
                        }
                        #***** Guardar Detalle Comprobante CNT ****#
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante`  
                                ( `fecha`,`descripcion`,
                                `valor`,`valorejecucion`,`comprobante`,
                                `cuenta`,`naturaleza`,`tercero`,
                                `proyecto`,`centrocosto`, `detalleafectado`,`detallecomprobantepptal` ) 
                            VALUES (:fecha,:descripcion,
                            :valor,:valorejecucion,:comprobante,
                            :cuenta,:naturaleza,:tercero,
                            :proyecto,:centrocosto,:detalleafectado, :detallecomprobantepptal)";
                        $sql_dato = array(
                        array(":fecha",$fecha),
                        array(":descripcion",$descripcion),
                        array(":valor",$valor),
                        array(":valorejecucion",$valor),
                        array(":comprobante",$comprobantecnt),
                        array(":cuenta",$cuentaA),
                        array(":naturaleza",$naturalezaA),
                        array(":tercero",$terceroD),
                        array(":proyecto",$proyecto),
                        array(":centrocosto",$centrocosto),
                        array(":detalleafectado",$idd),
                        array(":detallecomprobantepptal",$rowdc[$i][8]),
                            );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        
                        
                    }    else {
                        $html .="La Cuenta $cuentava No Se Encontró En Vigencia Actual".'<br/>';
                        $rta +=1;
                    }     
                }
            }
            
            $_SESSION['idCompCnt'] = $comprobantecnt;
            $_SESSION['cntEgreso'] = $comprobantecnt;
            $_SESSION['id_comp_pptal_GE'] = $comprobantepptal;
            $_SESSION['nuevo_GE'] = 1;
            $_SESSION['terceroGuardado'] = $tercero;
            $_SESSION['comprobanteGenerado'] = $comprobantepptal;
            $_SESSION['id_comp_pptal_GE'] = $comprobantepptal;
            $_SESSION['nuevo_GE'] = 1;

        } else {
            $html .="No Se Ha Podido Guardar La Información".'<br/>';   
            $rta +=1;
        }
        $datos = array();
        $datos = array("msj"=>$html,"rta"=>$rta);
        echo json_encode($datos); 
    break;
    break;
    #***Agregar Cuenta Por Pagar Vigencia Anterior
    case 6:
        $tercero            = $_REQUEST['tercero'];
        $comprobantepptal   = $_REQUEST['comprobantepptal'];
        $cxp                = $_REQUEST['cxp'];
        $html               = "";
        $rta                = 0;
        $feg = $con->Listar("SELECT fecha, numero, tipocomprobante "
                . "FROM gf_comprobante_pptal WHERE id_unico = $comprobantepptal");
        #** Buscar Fecha Egreso 
        if(empty($_REQUEST['fecha'])){
            $fechaE =$feg[0][0];
        } else {
            $fechaE = fechaC($_REQUEST['fecha']);
        }
        $numero = $feg[0][1];
        $tipocomprobante = $feg[0][2];
        #** Buscar Datos Básico Cuenta Por Pagar **#
        $owo="SELECT DISTINCT 
                    comp.descripcion, 
                    comp.numerocontrato, 
                    comp.clasecontrato, 
                    dc.comprobante, 
                    comp.fecha 
                FROM 
                    gf_comprobante_pptal comp 
                LEFT JOIN 
                    gf_detalle_comprobante_pptal dcp ON comp.id_unico = dcp.comprobantepptal 
                LEFT JOIN 
                    gf_detalle_comprobante dc ON dcp.id_unico = dc.detallecomprobantepptal 
            WHERE comp.id_unico =  $cxp";
        $rowComp = $con ->Listar("SELECT DISTINCT 
                    comp.descripcion, 
                    comp.numerocontrato, 
                    comp.clasecontrato, 
                    dc.comprobante, 
                    comp.fecha 
                FROM 
                    gf_comprobante_pptal comp 
                LEFT JOIN 
                    gf_detalle_comprobante_pptal dcp ON comp.id_unico = dcp.comprobantepptal 
                LEFT JOIN 
                    gf_detalle_comprobante dc ON dcp.id_unico = dc.detallecomprobantepptal 
      		WHERE comp.id_unico =  $cxp");
        
        #********* Comparar Fechas ********# 
        $fechaCxp = $rowComp[0][4];
        #echo $fechaE.' - cxp'.$fechaCxp;
        #var_dump($fechaCxp>$fechaE);
        if($fechaCxp < $fechaE){
            $cxpcnt = $rowComp[0][3];
            if(empty($rowComp[0][0])){
                $descripcion = NULL;
            } else {
                $descripcion = $rowComp[0][0];
            }
            if(empty($rowComp[0][1])){
                $numContrato = NULL;
            } else {
                $numContrato = $rowComp[0][1];
            }
            if(empty($rowComp[0][2])){
                $claseContrato = NULL;
            } else {
                $claseContrato = $rowComp[0][2];
            }
            //Actualizar Egreso
            $fecha = $fechaE;
            $sql_cons ="UPDATE `gf_comprobante_pptal` 
                SET `fecha`=:fecha, 
                    `descripcion`=:descripcion, 
                    `tercero`=:tercero,
                    `numerocontrato`=:numerocontrato,
                    `clasecontrato`=:clasecontrato 
                    WHERE `id_unico`=:id_unico ";
            $sql_dato = array(
                array(":fecha",$fecha),
                array(":descripcion",$descripcion),
                array(":tercero",$tercero),
                array(":numerocontrato",$numContrato),
                array(":clasecontrato",$claseContrato),
                array(":id_unico",$comprobantepptal),

            );
            
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            if (empty($obj_resp)) {
                
                #************** Buscar Detalles Pptal De La CxP *****************#
                $det = $con->Listar("SELECT 
                        dc.id_unico, 
                        dc.descripcion, 
                        dc.valor, 
                        dc.rubrofuente, 
                        dc.tercero, 
                        dc.proyecto, 
                        rb.codi_presupuesto, 
                        dc.centro_costo, 
                        rb.vigencia 
                    FROM gf_detalle_comprobante_pptal dc
                    LEFT JOIN gf_rubro_fuente rf on dc.rubrofuente = rf.id_unico 
                    LEFT JOIN gf_rubro_pptal rb on rf.rubro = rb.id_unico  
                    WHERE dc.comprobantepptal = $cxp
                    AND rb.tipoclase NOT IN (17)");

                for ($i = 0; $i < count($det); $i++) {
                    $id_detalle     = $det[$i][0];
                    $valorDetalle   = $det[$i][2];
                    $codRubro       = $det[$i][6];
                    $vigencia       = $det[$i][8];
                    if(empty($det[$i][5])){
                        $proyecto = 2147483647;
                    } else {
                        $proyecto =$det[$i][5];
                    }
                    if(empty($det[$i][7])){
                        $centrocosto =NULL;
                    } else {
                        #* Buscar Centro Costo Vigencia Actual *#
                        $idcc = $det[$i][6];
                        $ccva = $con->Listar("SELECT ccva.id_unico 
                                    FROM 
                                        gf_centro_costo cc 
                                    LEFT JOIN 
                                        gf_centro_costo ccva ON  cc.nombre = ccva.nombre 
                                    WHERE ccva.parametrizacionanno = $anno AND cc.id_unico = $idcc");
                        if(count($ccva)>0){
                            $centrocosto = $ccva[0][0];
                        } else {
                            $centrocosto =NULL;
                        }
                    }
                    #Validar Si Detalle Tiene Saldo Sin Afectar
                    $afect = 0;
                    #Buscar Valor Donde El Afectado Sea El Detalle
                    $dca = $con->Listar("SELECT valor   
                                        FROM gf_detalle_comprobante_pptal   
                                        WHERE comprobanteafectado = $id_detalle");
                    for($j=0; $j < count($dca); $j++){
                        $afect += $dca[$j][0];
                    }
                    #Si El Saldo >0
                    $saldo = $valorDetalle - $afect;
                    if($saldo>0){
                        #Buscar El Rubro Vigencia Anterior Correspondiente 
                        $sqlrva = $con->Listar("SELECT id_unico FROM gf_rubro_pptal 
                                WHERE parametrizacionanno = $anno AND vigencia = $vigencia
                                AND equivalente = '$codRubro' AND tipoclase = 15 AND tipovigencia = 5 ");
                        if(count($sqlrva)>0){
                            $rva = $sqlrva[0][0];
                            #Buscar Rubro Fuente 
                            $sqlrfva = $con->Listar("SELECT id_unico FROM gf_rubro_fuente WHERE rubro = $rva");
                            if(count($sqlrfva)>0){
                                $rfva = $sqlrfva[0][0];
                                #Registrar Detalle Pptal 
                                $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal`  
                                        ( `descripcion`,
                                        `valor`,`comprobantepptal`,`rubrofuente`,
                                        `tercero`,`proyecto`,`comprobanteafectado`, `centro_costo` ) 
                                    VALUES (:descripcion,
                                    :valor,:comprobantepptal,:rubrofuente,
                                    :tercero,:proyecto,:comprobanteafectado, :centro_costo )";
                                $sql_dato = array(
                                array(":descripcion",$descripcion),
                                array(":valor",$saldo),
                                array(":comprobantepptal",$comprobantepptal),
                                array(":rubrofuente",$rfva),
                                array(":tercero",$det[$i][4]),
                                array(":proyecto",$proyecto),
                                array(":comprobanteafectado",$id_detalle),
                                array(":centro_costo",$centrocosto),
                                    );
                                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);

                            } else {
                                $html  .="Rubro $codRubro No Tiene Fuente Configurada Para Vigencia Anterior".'<br/>';
                                $rta     += 1;
                            }
                        } else {
                            $html  .="Rubro $codRubro No se Encontró Equivalente".'<br/>';
                            $rta    += 1;
                        }

                    }

                }
                #*************** Registrar Cnt **********************************#
                #***** Buscar Tipo Comprobante Cnt Homologado 
                $tipo = $con->Listar("SELECT id_unico FROM gf_tipo_comprobante WHERE comprobante_pptal = $tipocomprobante");
                $tipocomprobantecnt = $tipo[0][0];
                #* Buscar Si El Comprobante Ya esta Hecho 
                $com = "SELECT * FROM gf_comprobante_cnt  WHERE parametrizacionanno = $anno  "
                        . "AND numero = $numero AND tipocomprobante=$tipocomprobantecnt";
                $com = $mysqli->query($com);
                if(mysqli_num_rows($com)>0){
                    $obj_resp="";
                } else {
                #***** Guardar Comprobante CNT ****#
                $sql_cons ="INSERT INTO `gf_comprobante_cnt`  
                        ( `numero`,`fecha`,`descripcion`,
                        `parametrizacionanno`,`tipocomprobante`,`tercero`,
                        `estado`,`numerocontrato`,`clasecontrato`,
                        `usuario`,`fecha_elaboracion`, `compania` ) 
                    VALUES (:numero,:fecha,:descripcion,
                    :parametrizacionanno,:tipocomprobante,:tercero,
                    :estado,:numerocontrato,:clasecontrato,
                    :usuario,:fecha_elaboracion,:compania)";
                $sql_dato = array(
                array(":numero",$numero),
                array(":fecha",$fecha),
                array(":descripcion",$descripcion),
                array(":parametrizacionanno",$anno),
                array(":tipocomprobante",$tipocomprobantecnt),
                array(":tercero",$tercero),
                array(":estado",1),
                array(":numerocontrato",$numContrato),
                array(":clasecontrato",$claseContrato),
                array(":usuario",$user),
                array(":fecha_elaboracion",$fechaElab),
                array(":compania",$compania),
                    );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                }
                if (empty($obj_resp)) {
                    $sqlcn = $con->Listar("SELECT MAX(id_unico) FROM gf_comprobante_cnt 
                            WHERE tipocomprobante = $tipocomprobantecnt AND numero = '$numero'");
                    $comprobantecnt = $sqlcn[0][0];

                    #************ Buscar Detalles Cnt De La Cuenta X Pagar *************#

                    $rowdc = $con->Listar("SELECT 
                            dt.id_unico, 
                            dt.valor, 
                            dt.proyecto, 
                            dt.cuenta, 
                            dt.centrocosto, 
                            dt.tercero, 
                            ct.naturaleza, 
                            ct.codi_cuenta,  
                            dt.detallecomprobantepptal 
                            FROM gf_detalle_comprobante dt 
                            LEFT JOIN gf_cuenta ct ON dt.cuenta = ct.id_unico 
                            LEFT JOIN gf_clase_cuenta cc ON cc.id_unico = ct.clasecuenta 
                            WHERE (dt.comprobante = $cxpcnt and cc.id_unico = 4) 
                            OR (dt.comprobante = $cxpcnt and cc.id_unico = 8)");
                    for ($i = 0; $i < count($rowdc); $i++) {
                        $idd = $rowdc[$i][0];
                        $valorp = $rowdc[$i][1];
                        #*** Buscar Si Tiene Afectado ***#
                        $af = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) 
                            FROM gf_detalle_comprobante dc WHERE dc.detalleafectado = $idd ");
                        if(empty($af[0][0])){
                            $afect =0;
                        } else {
                            $afect =$af[0][0];
                        }
                        if($valorp<0){$valorp=$valorp*-1-$afect;} 
                        else {$valorp=$valorp-$afect;}
                        
                        
                        $cuentava = $rowdc[$i][7];
                        $cuentaA        =  "";
                        $naturalezaA    =  "";
                        #Buscar Si Existe Equivalente Cuenta Para Vigencia Actual Vigencia Anterior 
                        $bcvaa = $con->Listar("SELECT id_unico, naturaleza FROM gf_cuenta 
                                   WHERE parametrizacionanno = $anno AND equivalente_va = $cuentava");
                        if(count($bcvaa)> 0){
                            $cuentaA        =  $bcvaa[0][0];
                            $naturalezaA    =  $bcvaa[0][1];
                        } else {
                        #Buscar Si Existe Cuenta Para Vigencia Actual Vigencia Anterior
                        $bcva = $con->Listar("SELECT id_unico, naturaleza FROM gf_cuenta 
                                   WHERE parametrizacionanno = $anno AND codi_cuenta = $cuentava");
                            if(count($bcva)> 0){
                                $cuentaA        =  $bcva[0][0];
                                $naturalezaA    =  $bcva[0][1];
                            } 
                        }
                        if($cuentaA !="" && $naturalezaA !="" ){
                            
                            if($naturalezaA == $rowdc[$i][6]){
                                $valor = $valorp *-1;
                            } else {
                                if($naturalezaA==1){
                                    if($rowdc[$i][1]<0){
                                        $valor = $valorp *-1;
                                    } else {
                                        $valor = $valorp;
                                    }
                                } else {
                                    if($rowdc[$i][1]>0){
                                        $valor = $valorp *-1;
                                    } else {
                                        $valor = $valorp;
                                    }
                                }
                            }
                            if(empty($rowdc[$i][4])){
                                $centrocosto =NULL;
                            }else {
                                #* Buscar Centro Costo Vigencia Actual *#
                                $idcc = $rowdc[$i][4];
                                $ccva = $con->Listar("SELECT ccva.id_unico 
                                            FROM 
                                                gf_centro_costo cc 
                                            LEFT JOIN 
                                                gf_centro_costo ccva ON  cc.nombre = ccva.nombre 
                                            WHERE ccva.parametrizacionanno = $anno AND cc.id_unico = $idcc");
                                if(count($ccva)>0){
                                    $centrocosto = $ccva[0][0];
                                } else {
                                    $centrocosto =NULL;
                                }
                            }
                            if(empty($rowdc[$i][2])){
                                $proyecto =NULL;
                            }else {
                                $proyecto = $rowdc[$i][2];
                            }
                            if(empty($rowdc[$i][5])){
                                $terceroD =$tercero;
                            }else {
                                $terceroD = $rowdc[$i][5];
                            }
                            #***** Guardar Detalle Comprobante CNT ****#
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante`  
                                    ( `fecha`,`descripcion`,
                                    `valor`,`valorejecucion`,`comprobante`,
                                    `cuenta`,`naturaleza`,`tercero`,
                                    `proyecto`,`centrocosto`, `detalleafectado`,`detallecomprobantepptal` ) 
                                VALUES (:fecha,:descripcion,
                                :valor,:valorejecucion,:comprobante,
                                :cuenta,:naturaleza,:tercero,
                                :proyecto,:centrocosto,:detalleafectado, :detallecomprobantepptal)";
                            $sql_dato = array(
                            array(":fecha",$fecha),
                            array(":descripcion",$descripcion),
                            array(":valor",$valor),
                            array(":valorejecucion",$valor),
                            array(":comprobante",$comprobantecnt),
                            array(":cuenta",$cuentaA),
                            array(":naturaleza",$naturalezaA),
                            array(":tercero",$terceroD),
                            array(":proyecto",$proyecto),
                            array(":centrocosto",$centrocosto),
                            array(":detalleafectado",$idd),
                            array(":detallecomprobantepptal",$rowdc[$i][8]),
                                );
                            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);


                        }    else {
                            $html .="La Cuenta $cuentava No Se Encontró En Vigencia Actual".'<br/>';
                            $rta +=1;
                        }     
                    }
                }

                $_SESSION['idCompCnt'] = $comprobantecnt;
                $_SESSION['cntEgreso'] = $comprobantecnt;
                $_SESSION['id_comp_pptal_GE'] = $comprobantepptal;
                $_SESSION['nuevo_GE'] = 1;
                $_SESSION['terceroGuardado'] = $tercero;
                $_SESSION['comprobanteGenerado'] = $comprobantepptal;
                $_SESSION['id_comp_pptal_GE'] = $comprobantepptal;
                $_SESSION['nuevo_GE'] = 1;

            } else {
                $html .="No Se Ha Podido Guardar La Información".'<br/>';    
                $rta +=1;
            }
        } else {
            $html .="La Fecha De La Cuenta Por Pagar Es Mayor Que La Fecha Del Egreso".'<br/>';    
            $rta +=1;
        }
        $datos = array();
        $datos = array("msj"=>$owo,"rta"=>$rta);
        echo json_encode($datos); 
    break;
       
    case 7:
        $comprobantepptal = $_REQUEST['comprobante_pptal'];
        $feg = $con->Listar("SELECT fecha, numero, tipocomprobante FROM gf_comprobante_pptal WHERE id_unico = $comprobantepptal");
        #** Buscar Fecha Egreso 
        if(empty($_REQUEST['fecha'])){
            $fechaE =$feg[0][0];
        } else {
            $fechaE = fechaC($_REQUEST['fecha']);
            #***** Modificar Fecha ****#
            $sql_cons ="UPDATE `gf_comprobante_pptal`  
                    SET `fecha`=:fecha 
                    WHERE `id_unico` =:id_unico";
            $sql_dato = array(
                array(":fecha",$fechaE),
                array(":id_unico",$comprobantepptal),
                );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            #var_dump($obj_resp);
        }
        $cxp = $_REQUEST['cuentaxpagar'];
        #** Buscar Datos Básico Cuenta Por Pagar **#
        $rowComp = $con ->Listar("SELECT DISTINCT 
                    comp.descripcion, 
                    comp.numerocontrato, 
                    comp.clasecontrato, 
                    dc.comprobante, 
                    comp.fecha 
                FROM 
                    gf_comprobante_pptal comp 
                LEFT JOIN 
                    gf_detalle_comprobante_pptal dcp ON comp.id_unico = dcp.comprobantepptal 
                LEFT JOIN 
                    gf_detalle_comprobante dc ON dcp.id_unico = dc.detallecomprobantepptal 
      		WHERE comp.id_unico =  $cxp");
        
        #********* Comparar Fechas ********# 
        $fechaCxp = $rowComp[0][4];
        if($fechaCxp <= $fechaE){
            echo 1;
        } else {
            echo 2;
        }
    break;
    #********** Guardar Encabezado Egreso Por Proveedor ********#
    case 8:
        $tercero    = $_REQUEST['tercero_s'];
        $banco      = $_REQUEST['banco'];
        $tipo_pptal = $_REQUEST['tipoComprobante'];
        $numero     = $_REQUEST['numero'];
        $fecha      = fechaC($_REQUEST['fecha']);
        $estado     = 1;
        $rta        = 0;
        $url        = "";
        $id_cnt     = "";
        $id_pptal   = "";
        #************* Guardar Pptal ************#
        $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
        ( `numero`, `tipocomprobante`, 
        `tercero`,`fecha`,
        `parametrizacionanno`,`estado`) 
        VALUES (:numero, :tipocomprobante, 
        :tercero, :fecha,
        :parametrizacionanno,:estado)";
        $sql_dato = array(
            array(":numero",$numero),
            array(":tipocomprobante",$tipo_pptal),
            array(":tercero",$tercero),
            array(":fecha",$fecha),
            array(":parametrizacionanno",$anno),
            array(":estado",$estado),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){ 
            #* Buscar Id Ppptal *#
            $pt = $con->Listar("SELECT MAX(id_unico) FROM gf_comprobante_pptal  
                WHERE numero=$numero AND tipocomprobante=$tipo_pptal");
            $id_pptal = $pt[0][0];
            #* Buscar Tipo Cnt 
            $tipo_c = $con->Listar("SELECT id_unico 
                FROM gf_tipo_comprobante WHERE comprobante_pptal =$tipo_pptal");
            $tipo_cnt = $tipo_c[0][0];
            #************* Guardar Cnt ************#
            $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
            ( `numero`, `tipocomprobante`, 
            `tercero`,`fecha`,
            `parametrizacionanno`,`estado`,`compania`) 
            VALUES (:numero, :tipocomprobante, 
            :tercero, :fecha,
            :parametrizacionanno,:estado,:compania)";
            $sql_dato = array(
                array(":numero",$numero),
                array(":tipocomprobante",$tipo_cnt),
                array(":tercero",$tercero),
                array(":fecha",$fecha),
                array(":parametrizacionanno",$anno),
                array(":estado",$estado),
                array(":compania",$compania),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                #* Buscar Id Cnt *#
                $cn = $con->Listar("SELECT MAX(id_unico) FROM gf_comprobante_cnt 
                    WHERE numero=$numero AND tipocomprobante=$tipo_cnt");
                $id_cnt = $cn[0][0];
            }  else {
                $rta =1;
            }
        } else {
            $rta =1;
        }       
        
        if($rta==0){
            #*** Guardar En Tabla Egreso  ***#
            $sql_cons ="INSERT INTO `gf_egreso_proveedor` 
            ( `tercero`, `fecha`, 
            `cnt`,`pptal`,
            `usuario`,`fecha_elaboracion`,
            `parametrizacionanno`,`banco`) 
            VALUES (:tercero, :fecha, 
            :cnt,:pptal,
            :usuario,:fecha_elaboracion,
            :parametrizacionanno,:banco)";
            $sql_dato = array(
                array(":tercero",$tercero),
                array(":fecha",$fecha),
                array(":cnt",$id_cnt),
                array(":pptal",$id_pptal),
                array(":usuario",$usuario),
                array(":fecha_elaboracion",date('y-m-d')),
                array(":parametrizacionanno",$anno),
                array(":banco",$banco),
                
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                #** Buscar Id Registro
                $id_r = $con->Listar("SELECT MAX(id_unico) FROM gf_egreso_proveedor 
                        WHERE cnt = $id_cnt AND pptal = $id_pptal AND parametrizacionanno = $anno");
                $id_r = $id_r[0][0];
                $url ='GF_EGRESO_PROVEEDOR.php?tercero='.$tercero.'&id='.$id_r;
            } else {
                $rta=1;
            }
        }
        $datos = array("url"=>$url,"rta"=>$rta);
        echo json_encode($datos);
    break;
    #***** Validar Parte Contable Y Configuración De Las Cuentas Por pagar Egreso Proveedor******#
    case 9:
        $cxp = $_REQUEST['cxp'];
        $sl = $con->Listar("SELECT * FROM gf_comprobante_pptal WHERE id_unico IN (".$cxp.")");
        $rta =0;
        $html ="";
        for ($i = 0; $i < count($sl); $i++) {
            $id_p = $sl[$i][0];
            #Buscar cnt
            $cn = $con->Listar("SELECT DISTINCT dc.comprobante FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_detalle_comprobante_pptal dp ON dc.detallecomprobantepptal = dp.id_unico 
                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico
                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                WHERE dp.comprobantepptal =$id_p and tc.clasecontable = 13 ");
            if(count($cn)>0){
                
            } else {
                $rta =1;
                $html .="El Comprobante N° ".$sl[$i][1]." No Tiene Comprobante Contable".'<br/>';
            }
        }
        $datos = array();
        $datos = array("msj"=>$html,"rta"=>$rta);
        echo json_encode($datos); 
    break;
    #********** Guardar Egreso Por Proveedor ********#
    case 10:
        $cxp    = $_REQUEST['cxp'];
        $cnt    = $_REQUEST['cnt'];
        $pptal  = $_REQUEST['pptal'];
        $banco  = $_REQUEST['banco'];
        $rta    = 0;  
        $reg    = 0;
        $valorr = 0;
        #Datos Cuenta Por Pagar 
        $dts = $con->Listar("SELECT tercero, fecha, descripcion FROM gf_comprobante_cnt WHERE id_unico = $cnt");
        $tercero_c  = $dts[0][0];
        $fecha      = $dts[0][1];
        $descripcion= $dts[0][1];
        if(empty($descripcion)){
            $descripcion =NULL;
        }
        $row = $con->Listar("SELECT * FROM gf_comprobante_pptal WHERE id_unico IN (".$cxp.")");
        for ($i = 0; $i < count($row); $i++) {
            $id_cxp = $row[$i][0];
            #*** Buscar Detalles Ppptal ***#
            $rowd = $con->Listar("SELECT DISTINCT 
                dp.id_unico, dp.valor, dp.rubrofuente, 
                dp.tercero, dp.proyecto, dp.conceptoRubro 
            FROM gf_detalle_comprobante_pptal dp 
            WHERE dp.comprobantepptal = $id_cxp");
            $totalvdp =0;
            for ($j = 0; $j < count($rowd); $j++) {
                $id_d = $rowd[$j][0];
                #** Buscar Valor Afectado **#
                $va = $con->Listar("SELECT SUM(valor)    
                    FROM gf_detalle_comprobante_pptal   
                    WHERE comprobanteafectado =$id_d");
                $afectado = $va[0][0];
                $saldo = $rowd[$j][1]-$afectado;
                if($saldo>0){
                    #*** Insertar Detalle Ppptal ***#
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                        ( `valor`, `comprobantepptal`, `rubrofuente`,
                        `tercero`,`proyecto`,`conceptoRubro`,
                        `comprobanteafectado`) 
                    VALUES (:valor, :comprobantepptal, :rubrofuente,
                        :tercero,:proyecto,:conceptoRubro,
                        :comprobanteafectado)";
                    $sql_dato = array(
                        array(":valor",$saldo),
                        array(":comprobantepptal",$pptal),
                        array(":rubrofuente",$rowd[$j][2]),
                        array(":tercero",$rowd[$j][3]),
                        array(":proyecto",$rowd[$j][4]),
                        array(":conceptoRubro",$rowd[$j][5]),
                        array(":comprobanteafectado",$id_d),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $totalvdp +=$saldo;
                }
            }
            $fg =0;
            #**** Buscar Id Comprobante Cnt Cuenta Por Pagar ****#
            $cn = $con->Listar("SELECT DISTINCT dc.comprobante FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_detalle_comprobante_pptal dp ON dc.detallecomprobantepptal = dp.id_unico 
                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico
                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                WHERE dp.comprobantepptal =$id_cxp and tc.clasecontable = 13 ");
            $id_cxpc = $cn[0][0];
            #*** Verificar Si Trae Cuentas De Nómina ****#
            #** Contar Detalles
            $numc = $con->Listar("SELECT DISTINCT dc.id_unico FROM gf_detalle_comprobante  dc 
                    WHERE dc.comprobante = $id_cxpc");
            $numc = count($numc);
            #** Contar Detalles Pasivo Nómina
            $nump = $con->Listar("SELECT DISTINCT dc.id_unico FROM gf_detalle_comprobante dc 
                    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                    WHERE dc.comprobante = $id_cxpc  
                    AND c.clasecuenta = 20");
            $nump = count($nump);
            #** Si Hay Cuentas Pasivo Nómina 
            if ($nump > 0) {
                #** CONTAR LOS DETALLES CON CUENTAS PASIVO NOMINA Y CLASE !6 Y !8
                $numcom = $con->Listar("SELECT DISTINCT dc.id_unico FROM gf_detalle_comprobante dc 
                        LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                        WHERE dc.comprobante = $id_cxpc 
                        AND (c.clasecuenta !=6 AND c.clasecuenta !=8)");
                $numcom = count($numcom);
                if ($numc == $numcom) {
                    $fg =1;
                }
            }
            if($fg==1){
                #** Se traen Las Cuentas de Pasivo Nómina
                $rowcc = $con->Listar("SELECT 
                    detCom.id_unico, detCom.valor, detCom.proyecto, 
                    detCom.cuenta, detCom.naturaleza, detCom.centrocosto, 
                    detCom.detallecomprobantepptal, detCom.tercero 
                FROM gf_detalle_comprobante detCom 
                LEFT JOIN gf_comprobante_cnt com ON com.id_unico = detCom.comprobante 
                LEFT JOIN gf_cuenta CT ON detCom.cuenta = CT.id_unico 
                LEFT JOIN gf_clase_cuenta clacu ON clacu.id_unico = CT.clasecuenta 
                WHERE com.id_unico = $id_cxpc and clacu.id_unico = 20 ");
            } else {
                #** Se Traen Las Cuentas Pasivo y Cuenta Por Pagar
                $rowcc = $con->Listar("SELECT 
                    detCom.id_unico, detCom.valor, detCom.proyecto, 
                    detCom.cuenta, detCom.naturaleza, detCom.centrocosto, 
                    detCom.detallecomprobantepptal, detCom.tercero 
                FROM gf_detalle_comprobante detCom 
                LEFT JOIN gf_comprobante_cnt com ON com.id_unico = detCom.comprobante 
                LEFT JOIN gf_cuenta CT ON detCom.cuenta = CT.id_unico 
                LEFT JOIN gf_clase_cuenta clacu ON clacu.id_unico = CT.clasecuenta 
                WHERE ( com.id_unico = $id_cxpc and clacu.id_unico = 4) 
                OR ( com.id_unico = $id_cxpc and clacu.id_unico = 8) 
                AND clacu.id_unico !=20 ");
            }
            for ($d = 0;$d < count($rowcc);$d++) {
                $terdeta    = $rowcc[$d][7];
                $valor      = $rowcc[$d][1];
                $proyecto   = $rowcc[$d][2];
                if(empty($proyecto)){
                    $proyecto = NULL;
                }
                $cuenta     = $rowcc[$d][3];
                $naturaleza = $rowcc[$d][4];
                $centrocosto= $rowcc[$d][5];
                if(empty($centrocosto)){
                    $centrocosto = NULL;
                }
                $detallecomprobantepptal = $rowcc[$d][6];
                if (empty($rowcc[$d][6])) {
                    $detallecomprobantepptal = 'NULL';
                }
                if($valor<0){
                    $valor =$valor*-1;
                }
                if($valor>$totalvdp){
                    $valor = $totalvdp;
                }
                
                if($valor>0){
                    $valorr +=$valor;
                    if($naturaleza==2){
                        $valor =$valor*-1; 
                    }                    
                    #*** Insertar Detalle Cnt ***#
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `valor`, `comprobante`, `cuenta`,
                        `naturaleza`,`tercero`,`proyecto`,
                        `centrocosto`,`detalleafectado`,`detallecomprobantepptal`) 
                    VALUES (:valor, :comprobante, :cuenta,
                        :naturaleza,:tercero,:proyecto,
                        :centrocosto,:detalleafectado,:detallecomprobantepptal)";
                    $sql_dato = array(
                        array(":valor",$valor),
                        array(":comprobante",$cnt),
                        array(":cuenta",$cuenta),
                        array(":naturaleza",$naturaleza),
                        array(":tercero",$terdeta),
                        array(":proyecto",$proyecto),
                        array(":centrocosto",$centrocosto),
                        array(":detalleafectado",$rowcc[$d][0]),
                        array(":detallecomprobantepptal",$detallecomprobantepptal),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    if(empty($resp)>0){
                        $reg +=1;
                    }
                }
            }
        } 
        if($reg >0){
            #*** Verificar Si Tiene Retenciones 
            $row2 = $con->Listar("SELECT r.id_unico, r.valorretencion, tr.cuenta , c.naturaleza 
                    FROM gf_retencion r
                    LEFT JOIN gf_tipo_retencion tr ON r.tiporetencion = tr.id_unico
                    LEFT JOIN gf_cuenta c ON tr.cuenta = c.id_unico
                    WHERE r.comprobante  = $cnt");
            if(count($row2)>0){
                for ($r = 0; $r < count($row2); $r++) { 
                    if($row2[$r][3]==1){
                        $valorret = $row2[$r][1]*-1;
                    } else {
                        $valorret = $row2[$r][1];
                    }
                    $ccuenta = $row2[$r][2];
                    $nnatur  = $row2[$r][3];
                    #*** Insertar Detalle cnt Rtrenciones ***#
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `valor`, `comprobante`, `cuenta`,
                        `naturaleza`,`tercero`,`proyecto`,
                        `centrocosto`) 
                    VALUES (:valor, :comprobante, :cuenta,
                        :naturaleza,:tercero,:proyecto,
                        :centrocosto)";
                    $sql_dato = array(
                        array(":valor",$valorret),
                        array(":comprobante",$cnt),
                        array(":cuenta",$ccuenta),
                        array(":naturaleza",$nnatur),
                        array(":tercero",$tercero_c),
                        array(":proyecto",$proyecto),
                        array(":centrocosto",$centrocosto),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $valorr -= $row2[$r][1];
                }
            }
                
            #******* Registrar Banco ***********#
            #buscar Cuenta Banco
            $bc = $con->Listar("SELECT
                    c.id_unico, c.naturaleza
                    FROM gf_cuenta_bancaria cb 
                    LEFT JOIN gf_cuenta c ON cb.cuenta = c.id_unico 
                    WHERE cb.id_unico=$banco");
            $cuentaB = $bc[0][0];
            $Ncuenta = $bc[0][1];
            #Registrar Cuenta de Banco 
            if($Ncuenta ==1){
                $valorr = $valorr*-1;
            }
            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                ( `valor`, `comprobante`, `cuenta`,
                `naturaleza`,`tercero`,`proyecto`,
                `centrocosto`) 
            VALUES (:valor, :comprobante, :cuenta,
                :naturaleza,:tercero,:proyecto,
                :centrocosto)";
            $sql_dato = array(
                array(":valor",$valorr),
                array(":comprobante",$cnt),
                array(":cuenta",$cuentaB),
                array(":naturaleza",$Ncuenta),
                array(":tercero",$tercero_c),
                array(":proyecto",$proyecto),
                array(":centrocosto",$centrocosto),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            
            #*** Actualizar Tabla ***#
            $sql_cons ="UPDATE `gf_egreso_proveedor` 
                SET `cxp`=:cxp
                WHERE `id_unico`=:id_unico ";
            $sql_dato = array(
                array(":cxp",$cxp),
                array(":id_unico",$_REQUEST['id_eg'])
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        } else {
            $rta =1;
        }
        
        echo $rta;
        
    break;
    #*** Buscar Egreso ***#
    case 11:
        $cnt    = $_REQUEST['cnt'];
        $pptal  = $_REQUEST['pptal'];
        $_SESSION['id_comp_pptal_GE']   = $pptal;
        $_SESSION['nuevo_GE']           = 1;
        $_SESSION['cntEgreso']          = $cnt;
        $_SESSION['idCompCnt']          = $cnt;
        $_SESSION['idCompCntV']         = $cnt;
        echo 1;
    break;
    #** Eliminar Desde Egreso Proveedor **#
    case 12:
        $cnt    = $_REQUEST['cnt'];
        $pptal  = $_REQUEST['pptal'];
        $id     = $_REQUEST['id_eg'];
        $rta    = 0;
        #*** Delete  Retencion ***#
        $sql_cons ="DELETE FROM `gf_retencion`  
            WHERE `comprobante`=:comprobante ";
        $sql_dato = array(
            array(":comprobante",$cnt),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            #*** Delete  CNT ***#
            $sql_cons ="DELETE FROM `gf_detalle_comprobante`  
                WHERE `comprobante`=:comprobante ";
            $sql_dato = array(
                array(":comprobante",$cnt),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                #*** Delete  Pptal ***#
               $sql_cons ="DELETE FROM `gf_detalle_comprobante_pptal`  
                   WHERE `comprobantepptal`=:comprobantepptal ";
               $sql_dato = array(
                   array(":comprobantepptal",$pptal),
               );
               $resp = $con->InAcEl($sql_cons,$sql_dato);
               if(empty($resp)){
                    #*** Actualizar Tabla ***#
                   $sql_cons ="UPDATE `gf_egreso_proveedor` 
                       SET `cxp`=:cxp
                       WHERE `id_unico`=:id_unico ";
                   $sql_dato = array(
                       array(":cxp",NULL),
                       array(":id_unico",$id)
                   );
                   $resp = $con->InAcEl($sql_cons,$sql_dato);
                   if(empty($resp)){}else {$rta = 1;}
               } else {
                   $rta = 1;
               }
            } else {
                $rta = 1;
            }
        } else {
            $rta = 1;
        }
        echo $rta;
    break;
    
    #** Calcular Retenciones Del Egreso ***#
    case 13:
        $id_egreso = $_REQUEST['id'];
        $row = $con->Listar("SELECT GROUP_CONCAT(DISTINCT cn.id_unico)  FROM 
            gf_comprobante_pptal cp 
            LEFT JOIN gf_detalle_comprobante_pptal dc ON cp.id_unico = dc.comprobantepptal 
            LEFT JOIN gf_detalle_comprobante_pptal dca ON dc.comprobanteafectado = dca.id_unico 
            LEFT JOIN gf_comprobante_pptal cpa ON dca.comprobantepptal = cpa.id_unico 
            LEFT JOIN gf_detalle_comprobante dcc ON dcc.detallecomprobantepptal= dca.id_unico 
            LEFT JOIN gf_comprobante_cnt cn ON dcc.comprobante = cn.id_unico 
            LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
            where cp.id_unico = $id_egreso AND tc.clasecontable = 13");
        
        $rowr = $con->Listar("SELECT SUM(valorretencion) FROM gf_retencion WHERE comprobante IN (".$row[0][0].")");
        
        if(empty($rowr[0][0])){
            $retencion = 0;
        } else {
            $retencion = $rowr[0][0];
        }
        echo $retencion;
    break;
    
    #** Guardar Campo **#
    case 14:
        $id_egreso  = $_REQUEST['id'];
        $valor      = $_REQUEST['valor'];
        $sql_cons ="UPDATE `gf_comprobante_pptal` 
           SET `valor_abono`=:valor_abono
           WHERE `id_unico`=:id_unico ";
       $sql_dato = array(
           array(":valor_abono",$valor),
           array(":id_unico",$id_egreso)
       );
       $resp = $con->InAcEl($sql_cons,$sql_dato);
    break;


    #** Validar Embargo **#
    case 15:
        $rta = 0;
        $id_tercero  = $_REQUEST['id_tercero'];
        $em = $con->Listar("SELECT * FROM gf_condicion_tercero ct 
        LEFT JOIN gf_perfil_condicion pc ON ct.perfilcondicion = pc.id_unico 
        LEFT JOIN gf_condicion c ON pc.condicion  = c.id_unico 
        LEFT JOIN gf_tercero t ON ct.tercero = t.id_unico 
        WHERE t.id_unico = $id_tercero AND  c.nombre LIKE '%embargo%' 
        AND (ct.valor != NULL OR ct.valor != 0 OR ct.valor !='')");
        IF(count($em)>0){
            $rta = 1;
        }
        echo $rta;
    break;
}
