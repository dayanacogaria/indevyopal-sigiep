<?php 
######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#18/07/2018 |Erica G. | Centro Costo Presupuestal Privada
#23/01/2018 |Erica G. |Registros Vigencia Anterior
#23/11/2017 |Erica G. |Archivo Creado
##############################################################################################################################
include_once("../Conexion/ConexionPDO.php");
include_once("../Conexion/conexion.php");
require_once '../jsonPptal/funcionesPptal.php';
session_start();

$action = $_REQUEST['action'];
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];
$fechaE = date('Y-m-d');
$obj_con = new ConexionPDO();
$con = new ConexionPDO();
switch ($action) {
    #**********Modificar Cuenta Por Pagar**********#
    case 1:
        $pptal =$_REQUEST['pptal'];
        $fecha =fechaC($_REQUEST['fecha']);
        if(!empty($_REQUEST['desc'])){
            $desc  =$_REQUEST['desc'];
        } else {
            $desc =NULL;
        }
        if(!empty($_REQUEST['numc'])){
            $numc  =$_REQUEST['numc'];
        } else {
            $numc =NULL;
        }
        if(!empty($_REQUEST['clasec'])){
            $clasec  =$_REQUEST['clasec'];
        } else {
            $clasec =NULL;
        }
        if(!empty($_REQUEST['tercero'])){
            $tercero  =$_REQUEST['tercero'];
        } else {
            $tercero =NULL;
        }
        if(!empty($_REQUEST['proyecto'])){
            $proyecto  =$_REQUEST['proyecto'];
        } else {
            $proyecto =NULL;
        }
        #*** Buscar Tipo Compañia **#
        $tcoms ="SELECT tipo_compania FROM gf_tercero WHERE id_unico = $compania";
        $tcm1 = $mysqli->query($tcoms);
        $tc1 = mysqli_fetch_row($tcm1);
        $tcom = $tc1[0];
       
        if(!empty($_REQUEST['idcnt'])){
            $idcnt =$_REQUEST['idcnt'];
            if($tcom==2){
                $sql_cons ="UPDATE `gf_comprobante_cnt` 
                    SET  
                        `fecha`=:fecha, 
                        `descripcion`=:descripcion, 
                        `clasecontrato`=:clasecontrato, 
                        `numerocontrato`=:numerocontrato, 
                        `tercero`=:tercero , `proyecto`=:proyecto
                        WHERE id_unico=:id_unico ";
                $sql_dato = array(
                    array(":descripcion",$desc),
                    array(":numerocontrato",$numc),
                    array(":clasecontrato",$clasec),
                    array(":tercero",$tercero),
                    array(":fecha",$fecha),
                    array(":proyecto",$proyecto),
                    array(":id_unico",$idcnt),

                );
            } else {
                $sql_cons ="UPDATE `gf_comprobante_cnt` 
                    SET  
                        `fecha`=:fecha 
                        WHERE id_unico=:id_unico ";
                $sql_dato = array(
                    array(":descripcion",$desc),
                    array(":numerocontrato",$numc),
                    array(":clasecontrato",$clasec),
                    array(":fecha",$fecha),
                    array(":id_unico",$idcnt),

                );
                
            }
            $obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);
            
        } 
        if($tcom==2){
            
            $sql_cons ="UPDATE `gf_comprobante_pptal` 
                SET `descripcion`=:descripcion, 
                    `numerocontrato`=:numerocontrato, 
                    `clasecontrato`=:clasecontrato, 
                    `fecha`=:fecha,
                    `tercero`=:tercero, 
                     `proyecto`=:proyecto
                    WHERE id_unico=:id_unico ";
            $sql_dato = array(
                array(":descripcion",$desc),
                array(":numerocontrato",$numc),
                array(":clasecontrato",$clasec),
                array(":tercero",$tercero),
                array(":fecha",$fecha),
                array(":proyecto",$proyecto),
                array(":id_unico",$pptal),

            );
        } else {
           $sql_cons ="UPDATE `gf_comprobante_pptal` 
                SET `descripcion`=:descripcion, 
                    `numerocontrato`=:numerocontrato, 
                    `clasecontrato`=:clasecontrato, 
                    `fecha`=:fecha ,`proyecto`=:proyecto  
                    WHERE id_unico=:id_unico ";
            $sql_dato = array(
                array(":descripcion",$desc),
                array(":numerocontrato",$numc),
                array(":clasecontrato",$clasec),
                array(":fecha",$fecha),
                array(":proyecto",$proyecto),
                array(":id_unico",$pptal),

            ); 
        }
        $obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);
        if( $tcom==1){
            $sql_cons ="UPDATE `gf_detalle_comprobante_pptal` 
                SET `proyecto`=:proyecto  
                    WHERE comprobantepptal=:comprobantepptal ";
            $sql_dato = array(
                array(":proyecto",$proyecto),
                array(":comprobantepptal",$pptal),

            ); 
            $obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);
        }
        if (empty($obj_resp)) {
            echo "Información Modificada Correctamente.";
        }else{
            echo "No se ha podido Modificar la información";
        }
    break;
    #**********Verificar Si El Tercero Tiene Mov Almacen**********#
    case 2:
        $tercero = $_REQUEST['tercero'];
        $cons = $obj_con->Listar("SELECT * FROM gf_movimiento WHERE tercero = '".$tercero."' AND afectado_contabilidad IS NULL AND parametrizacionanno = $anno");
        $num = COUNT($cons);
        echo $num;
    break;
    #********* Listar Tabla Movimientos ******#
    case 3:
        $movi   = $_REQUEST['movimientos'];
        $id_ter = $_REQUEST['id'];
        $html="";
        $html.='<table id="tabla21" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">';
        $html.='<thead style="position: relative;overflow: auto;width: 100%;">';
        $html.='<tr>';
        $html.='<td class="oculto">Identificador</td>';
        $html.='<td width="7%" class="cabeza"></td>';
        $html.='<td class="cabeza"><strong>Tipo Movimiento</strong></td>';
        $html.='<td class="cabeza"><strong>Número</strong></td>';
        $html.='<td class="cabeza"><strong>Fecha</strong></td>';
        $html.='<td class="cabeza"><strong>Descripción</strong></td>';
        $html.='<td class="cabeza"><strong>Valor</strong></td>';
        $html.='</tr>';
        $html.='<tr>';
        $html.='<th class="oculto">Identificador</th>';
        $html.='<th width="7%" class="cabeza"></th>';
        $html.='<th class="cabeza">Tipo Movimiento</th>';
        $html.='<th class="cabeza">Número</th>';
        $html.='<th class="cabeza">Fecha</th>';
        $html.='<th class="cabeza">Descripción</th>';
        $html.='<th class="cabeza">Valor</th>';
        $html.='</tr>';
        $html.='</thead>';
        $html.='<tbody >';
        if(empty($movi)){
            $bs= $obj_con->Listar (" SELECT 
                        m.id_unico, 
                        UPPER(tm.sigla), 
                        m.numero, 
                        DATE_FORMAT(m.fecha, '%d/%m/%Y'), 
                        m.descripcion, 
                        (SELECT SUM(dmv.valor) FROM gf_detalle_movimiento dmv WHERE dmv.movimiento = m.id_unico),
                        (SELECT SUM(dmv.iva) FROM gf_detalle_movimiento dmv WHERE dmv.movimiento = m.id_unico)
                    FROM 
                            gf_movimiento m 
                    LEFT JOIN 
                            gf_tipo_movimiento tm ON tm.id_unico = m.tipomovimiento 
                    WHERE 
                        m.parametrizacionanno = $anno AND m.tercero = $id_ter 
                    ORDER BY 
                        m.numero ASC ");
            for ($i = 0; $i < count($bs); $i++) {
                $html .='<tr>';
                $html .='<td class="oculto">Identificador</td>';
                $html .='<td width="7%" class="cabeza">';
                $html .='<input name="chkActivar[]" id="chkActivar'.$bs[$i][0].'" value="'.$bs[$i][0].'" type="checkbox"/>';
                $html .='</td>';
                $html .='<td>'.$bs[$i][1].'</td>';
                $html .='<td>'.$bs[$i][2].'</td>';
                $html .='<td>'.$bs[$i][3].'</td>';
                $html .='<td>'.$bs[$i][4].'</td>';
                $html .='<td class="campos text-right">'.number_format($bs[$i][5]+$bs[$i][6],2,',','.').'</td>';
                $html .='</tr>';
            }  
        } else {
            $bs= $obj_con->Listar (" SELECT 
                        m.id_unico, 
                        UPPER(tm.sigla), 
                        m.numero, 
                        DATE_FORMAT(m.fecha, '%d/%m/%Y'), 
                        m.descripcion, 
                        (SELECT SUM(dmv.valor) FROM gf_detalle_movimiento dmv WHERE dmv.movimiento = m.id_unico),
                        (SELECT SUM(dmv.iva) FROM gf_detalle_movimiento dmv WHERE dmv.movimiento = m.id_unico)
                    FROM 
                            gf_movimiento m 
                    LEFT JOIN 
                            gf_tipo_movimiento tm ON tm.id_unico = m.tipomovimiento 
                    WHERE 
                        m.parametrizacionanno = $anno AND m.tercero = $id_ter 
                    AND m.id_unico IN($movi) 
                    ORDER BY 
                        m.numero ASC ");
            
            for ($i = 0; $i < count($bs); $i++) {

                $html .='<tr>';
                $html .='<td class="oculto">Identificador</td>';
                $html .='<td width="7%" class="cabeza">';
                $html .='<input name="chkActivar[]" id="chkActivar'.$bs[$i][0].'" value="'.$bs[$i][0].'" type="checkbox" checked ="checked"/>';
                $html .='</td>';
                $html .='<td>'.$bs[$i][1].'</td>';
                $html .='<td>'.$bs[$i][2].'</td>';
                $html .='<td>'.$bs[$i][3].'</td>';
                $html .='<td>'.$bs[$i][4].'</td>';
                $html .='<td class="campos text-right">'.number_format($bs[$i][5]+$bs[$i][6],2,',','.').'</td>';
                $html .='</tr>';
            }  
            $bs= $obj_con->Listar (" SELECT 
                        m.id_unico, 
                        UPPER(tm.sigla), 
                        m.numero, 
                        DATE_FORMAT(m.fecha, '%d/%m/%Y'), 
                        m.descripcion, 
                        (SELECT SUM(dmv.valor) FROM gf_detalle_movimiento dmv WHERE dmv.movimiento = m.id_unico),
                        (SELECT SUM(dmv.iva) FROM gf_detalle_movimiento dmv WHERE dmv.movimiento = m.id_unico)
                    FROM 
                            gf_movimiento m 
                    LEFT JOIN 
                            gf_tipo_movimiento tm ON tm.id_unico = m.tipomovimiento 
                    WHERE 
                        m.parametrizacionanno = $anno AND m.tercero = $id_ter 
                    AND m.id_unico NOT IN ($movi) 
                    ORDER BY 
                        m.numero ASC ");
            
            for ($i = 0; $i < count($bs); $i++) {

                $html .='<tr>';
                $html .='<td class="oculto">Identificador</td>';
                $html .='<td width="7%" class="cabeza">';
                $html .='<input name="chkActivar[]" id="chkActivar'.$bs[$i][0].'" value="'.$bs[$i][0].'" type="checkbox" />';
                $html .='</td>';
                $html .='<td>'.$bs[$i][1].'</td>';
                $html .='<td>'.$bs[$i][2].'</td>';
                $html .='<td>'.$bs[$i][3].'</td>';
                $html .='<td>'.$bs[$i][4].'</td>';
                $html .='<td class="campos text-right">'.number_format($bs[$i][5]+$bs[$i][6],2,',','.').'</td>';
                $html .='</tr>';
            }
            
        }
        $html.='</tbody>';
        $html.='</table>';
        echo $html;
    break;
    
    #******Comparar valor pptal y mov almacen***#
    case 4:
        $idpptal = $_REQUEST['idp'];
        $idAlm =  $_REQUEST['mov'];
        #Valor pptal 
        $cons = $obj_con->Listar("SELECT SUM(valor) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $idpptal");
        $vp = $cons[0][0];
        #Valor Almacen
        $valmacen=0;
        $ids = explode(",", $idAlm);
        foreach($ids as $id) {
            $id = trim($id);
            if($id!=''){
                $valaT = $obj_con->Listar("SELECT SUM(valor) FROM gf_detalle_movimiento WHERE movimiento = $id");
                $valaI = $obj_con->Listar("SELECT SUM(iva) FROM gf_detalle_movimiento WHERE movimiento = $id");
                $va = $valaT[0][0]+$valaI[0][0];
                $valmacen +=$va;
            }
        }
        $result =0;
        
        if($valmacen > $vp){
            $result=2;
        } elseif($vp != $valmacen){
            $result=1;
        } 
        echo $result;
        
    break;
    #*************Validar Configuracion***********#
    case 5;
        $ids = explode(",", $_REQUEST['mova']);
        $f =0;
        $parm = new gs_parametros_basicos();
        $con = $parm->buscar_par("nombre", "Dígitos Interfaz Inventario");
        $dig = $con[2];
        $html = "<strong>Movimientos Sin Configuración </strong><br/>";
        foreach($ids as $id) {
            $id = trim($id);
            if($id!=''){
                #***Detalles, Tipo, Plan Movimiento***#
                $tma= $obj_con->Listar("SELECT m.id_unico, 
                        m.tipomovimiento, 
                        dm.planmovimiento, 
                        UPPER(tm.sigla), LOWER(tm.nombre), 
                        pm.codi, LOWER(pm.nombre) 
                    FROM 
                        gf_movimiento m
                    LEFT JOIN 
                        gf_detalle_movimiento dm ON m.id_unico = dm.movimiento 
                    LEFT JOIN 
                        gf_tipo_movimiento tm ON tm.id_unico = m.tipomovimiento 
                    LEFT JOIN 
                        gf_plan_inventario pm ON pm.id_unico = dm.planmovimiento 
                    WHERE 
                        m.id_unico = $id AND LENGTH(pm.codi)=$dig");
                for($i=0;$i<count($tma); $i++){
                    $tipom  = $tma[$i][1];
                    $plan   = $tma[$i][2];
                    $cn = $obj_con->Listar("SELECT 
                        id_unico, 
                        cuenta_debito, 
                        cuenta_credito 
                    FROM 
                        gf_configuracion_almacen 
                    WHERE 
                        plan_inventario = $plan 
                        AND tipo_movimiento = $tipom
                        AND parametrizacion_anno = $anno");
                    if(count($cn)>0){
                        
                    } else {
                        $f +=1;
                        $html .='<strong>Grupo:</strong>'.$tma[$i][5].' - '.ucwords($tma[$i][6]).'<strong>Tipo Movimiento:</strong>'.$tma[$i][3].' - '.$tma[$i][4].'<br/>';
                    }
                }
            }
        }
        $datos = array("respuesta"=>$html,"rs"=>$f);

        echo json_encode($datos);
    break;
    
    #*************Cargar Registros Vigencia Anterior ***************#
    case 6:
        $tercero = $_REQUEST['tercero'];
        #Buscar Año Anterior
        $nannoa = anno($anno);
        $nannoan = $nannoa-1;
        $busc = $con->Listar("SELECT id_unico FROM gf_parametrizacion_anno WHERE anno = $nannoan");
        if(count($busc) >0){ 
            $annoan = $busc[0][0];
            $queryComp = "SELECT  com.id_unico, com.numero, com.fecha, com.descripcion
                            FROM gf_comprobante_pptal com
                            left join gf_tipo_comprobante_pptal tipoCom on tipoCom.id_unico = com.tipocomprobante
                            WHERE tipoCom.clasepptal = 15  
                            and tipoCom.tipooperacion = 1
                            and com.tercero =  $tercero 
                            AND com.parametrizacionanno = $annoan";
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
    
    #*************Añadir Registro Vigencia Anterior *************#
    case 7:
        $id_comp = $_POST['id_comp'];
        $res = 0;
        ###########VALIDAR FECHA COMPROBANTE A AGREGAR#############
        $fech = "SELECT cp.fecha 
                FROM gf_comprobante_pptal cp 
                where cp.id_unico = $id_comp";
        $fech = $mysqli->query($fech);
        $fech = mysqli_fetch_row($fech);
        $fechaR = $fech[0];
        $fechaV = $_POST['fecha'];
        $fecha = DateTime::createFromFormat('d/m/Y', "$fechaV");
        $fecha = $fecha->format('Y-m-d');

        if ($fechaR > $fecha) {
            $res = 2;
        } else {

            $queryAntiguoDetallPttal = "SELECT detComP.descripcion, 
                            detComP.valor, 
                            detComP.rubrofuente, 
                            detComP.tercero, 
                            detComP.proyecto, 
                            detComP.id_unico, 
                            detComP.conceptorubro, 
                            cp.descripcion, cp.numerocontrato, cp.clasecontrato, cp.tercero , 
                            DATE_FORMAT(cp.fecha, '%d-%m-%Y'), 
                            rub.codi_presupuesto, 
                            cc.id_unico, 
                            cc.nombre 
                FROM gf_detalle_comprobante_pptal detComP 
                        LEFT JOIN gf_comprobante_pptal cp ON detComP.comprobantepptal = cp.id_unico 
                left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
                left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
                left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro 
                left join gf_concepto con on con.id_unico = conRub.concepto 
                        LEFT JOIN gf_centro_costo cc ON detComP.centro_costo = cc.id_unico 
                where detComP.comprobantepptal = $id_comp";
            $resultado = $mysqli->query($queryAntiguoDetallPttal);
            $comprobantepptal = $_SESSION['id_comp_pptal_CP'];

            while ($row = mysqli_fetch_row($resultado)) {
                $saldDisp = 0;
                $totalAfec = 0;
                $queryDetAfe = "SELECT valor   
              FROM gf_detalle_comprobante_pptal   
              WHERE comprobanteafectado = " . $row[5];
                $detAfec = $mysqli->query($queryDetAfe);
                $totalAfe = 0;
                while ($rowDtAf = mysqli_fetch_row($detAfec)) {
                    $totalAfec += $rowDtAf[0];
                } //$rowDtAf = mysqli_fetch_row($detAfec)
                $saldDisp = $row[1] - $totalAfec;
                $valorPpTl = $saldDisp;
                if ($valorPpTl > 0) {
                    $valor = $valorPpTl;
                    $idrubro = $row[2];
                    $codRubro = $row[12];
                    $idconceptorubro = $row[6];
                    #Buscar Rubro Equivalente Vigencia Actual 
                    $re = $con->Listar("SELECT * FROM gf_rubro_pptal WHERE equivalente = '$codRubro' AND tipoclase = 16 AND tipovigencia = 6 AND parametrizacionanno = $anno");
                   
                    if(count($re)>0){
                        $rb = $re[0][0];
                        $rf = $con->Listar("SELECT id_unico FROM gf_rubro_fuente WHERE rubro = $rb");
                        if(count($rf)>0){
                            $rubro = $rf[0][0];
                            #Buscar Concepto Rubro
                            $cr = $con->Listar("SELECT id_unico FROM gf_concepto_rubro WHERE rubro = $rb");
                            if(count($cr)>0){
                                $conceptorubro =$cr[0][0];

                                $tercero = $row[3];
                                if(!empty($row[4])){
                                    $proyecto = $row[4];
                                }elseif(!empty ($_REQUEST['proyecto'])){
                                    $proyecto = $_REQUEST['proyecto'];
                                } else {
                                    $proyecto = 'NULL';
                                }
                                
                                $idAfectado = $row[5];
                                $campo = "";
                                $variable = "";
                                if (empty($row[0])) {
                                    $descripcion = 'NULL';
                                } else {
                                    $descripcion = "'" . $row[0] . "'";
                                }
                                if(empty($row[13])){
                                    #** Buscar Centro Costo Varios **#
                                    $cv =$con->Listar("SELECT * FROM gf_centro_costo 
                                    WHERE parametrizacionanno = $anno AND nombre ='Varios'");
                                    if(count($cv)>0){
                                        $cc = $cv[0][0];
                                    } else {
                                        $cc = 'NULL';
                                    }
                                } else {
                                    $centrcn = $row[14];
                                    #** Buscar Centro Costo Nombre **#
                                    $cv =$con->Listar("SELECT * FROM gf_centro_costo 
                                    WHERE parametrizacionanno = $anno AND nombre ='$centrcn'");
                                    if(count($cv)>0){
                                        $cc = $cv[0][0];
                                    } else {
                                        $cc = 'NULL';
                                    }
                                }
                                $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal (valor, "
                                        . "comprobantepptal, " . "rubrofuente, tercero, proyecto, "
                                        . "comprobanteafectado, conceptorubro, descripcion, centro_costo) "
                                        . "VALUES ('$valor', '$comprobantepptal', '$rubro', "
                                        . "'$tercero', "
                                        . "'$proyecto', '$idAfectado', '$conceptorubro', $descripcion, $cc)";
                                $resultadoInsert = $mysqli->query($insertSQL);
                            }
                        }
                    }
                } //$valorPpTl > 0
                else {
                    $resultadoInsert = false;
                }
                if (empty($_POST['descripcion'])) {
                    if (empty($row[7])) {
                        $desMod = 'NULL';
                    } //empty($row[7])
                    else {
                        $desMod = "'" . $row[7] . "'";
                    }
                } //empty($_POST['descripcion'])
                else {
                    $desMod = '"' . $_POST['descripcion'] . '"';
                }
                if (empty($_POST['claseContrato'])) {
                    if (empty($row[9])) {
                        $ccon = 'NULL';
                    } else {
                        $ccon = $row[9];
                    }
                }
                else {
                    $ccon = $_POST['claseContrato'];
                }
                if (empty($_POST['numeroContrato'])) {
                    if (empty($row[8])) {
                        $numC = 'NULL';
                    } //empty($row[8])
                    else {
                        $numC = '"' . $row[8] . '"';
                    }
                } //empty($_POST['numeroContrato'])
                else {
                    $numC = '"' . $_POST['numeroContrato'] . '"';
                }
            } //$row = mysqli_fetch_row($resultado)
            if ($resultadoInsert == TRUE) { 
                $tercero = $_POST['tercero'];
                $fecha = $mysqli->real_escape_string('' . $_POST['fecha'] . '');
                $fecha = DateTime::createFromFormat('d/m/Y', "$fecha");
                $fecha = $fecha->format('Y-m-d');
                #ACTUALIZAR ENCABEZADO CUENTA POR PAGAR#         
                $upd = "UPDATE gf_comprobante_pptal SET tercero = $tercero, "
                        . "descripcion = $desMod, "
                        . "numerocontrato = $numC, clasecontrato = $ccon "
                        . "WHERE id_unico = $comprobantepptal";
                $upd = $mysqli->query($upd);
                if ($upd == TRUE) {
                    $res = 1;
                } //$upd == TRUE
                else {
                    $res = 0;
                }
            } //$resultadoInsert == TRUE
        }
        echo $res;
    break;
}