<?php

require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
require_once './funcionesPptal.php';
require '../jsonAlmacen/funcionesAlmacen.php';
session_start();
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$con        = new ConexionPDO();
switch ($_POST['action']){
    #########CARGAR NUEVA RETENCION#######
    case 1:

        $valorTotal   = $_POST['valorTotal'];
        $consecutivo  = $_POST['consecutivo'];
        $renglon      = '';
        $renglon      = '<tr id="renglon' . $consecutivo . '">
                            <td class="oculto">
                            </td>
                            <td class="campos" >
                               <div style="height: 20px; width: 25px; border: black solid 1px; margin: 0 auto; background: #00548F; color: #fff; border-color: #1075C1; border-radius: 3px; cursor: pointer" onclick="javascript: quitarRenglon(' . $consecutivo . ');" class="ocultarSiGuarda" title="Quitar Esta Retención">
                                  <li class="glyphicon glyphicon-remove"></li>
                               </div>
                               <!-- Quitar el renglón--> 
                               <a class="campos" id="modificarValoresRet' . $consecutivo . '" style="cursor: pointer; display: none;" onclick="javascript: modificarValores(' . $consecutivo . ');">
                               <i title="Modificar" class="glyphicon glyphicon-edit">
                               </i>
                               </a>
                            </td>
                            <td class="campos" align="center">
                               <!-- Clase Retención -->';
        $claseRet     = "SELECT nombre, id_unico
                FROM gf_clase_retencion 
                ORDER BY nombre ASC";
        $rscR         = $mysqli->query($claseRet);
        $renglon .= '   <label></label>
                  	<select name="sltClaseRet' . $consecutivo . '" id="sltClaseRet' . $consecutivo . '" onchange="javascript: claseRet(' . $consecutivo . ')" class="form-control input-sm" title="Seleccione Clase Retención" style="width: 94%;">
                    	<option value="">Clase Retención</option>';
        while ($filacR = mysqli_fetch_row($rscR)) {
            $renglon .= '<option value="' . $filacR[1] . '">' . ucwords(($filacR[0])) . '</option>';
        } //$filacR = mysqli_fetch_row($rscR)
        $renglon .= '</select>
                    </td>
                    <td class="campos" align="center" >
                       <!-- Tipo Retención --> 
                       <label></label>
                       <select name="sltTipoRet' . $consecutivo . '" id="sltTipoRet' . $consecutivo . '" onclick="javascript: tipoRetencion(' . $consecutivo . ');"  onchange="javascript: valTipoRetRep(' . $consecutivo . ');" class="form-control input-sm" title="Seleccione Tipo Retención" style="width: 94%;">
                          <option value="">Tipo retención</option>
                       </select>
                    </td>
                    <td class="campos" align="center" style="padding: 0px">
                       <!-- Aplicar Sobre -->';
        $aplicarS = "SELECT nombre, id_unico
            	FROM gf_tipo_base
                WHERE nombre != ''
                ORDER BY nombre ASC";
        $rsaS     = $mysqli->query($aplicarS);
        $renglon .= '   <label></label>
                  	<select name="sltAplicarS' . $consecutivo . '" id="sltAplicarS' . $consecutivo . '" onclick="javascript: validarTres(' . $consecutivo . ');" onchange="javascript: aplicarSob(' . $consecutivo . ');" class="form-control input-sm" title="Aplicar Sobre" style="width: 94%;">
                            <option value="">Aplicar sobre</option>';
        while ($filaaS = mysqli_fetch_row($rsaS)) {
            $renglon .= '<option value="' . $filaaS[1] . '">' . ucwords(($filaaS[0])) . '</option>';
        } //$filaaS = mysqli_fetch_row($rsaS)
        $renglon .= '</select>          
                    </td> <!-- Fin celda Valor aprobado -->
                    <td class="campos" align="right" >
                       <!-- Valor Total -->
                       <span id="valorTotal' . $consecutivo . '">' . number_format($valorTotal, 2, '.', ',') . '</span>
                    </td>
                    <!-- Saldo por pagar -->
                    <td class="campos" align="center" >
                       <!-- % IVA -->';
        $porIVA = "SELECT valor 
                                FROM gs_parametros_basicos 
                                WHERE nombre ='porcentaje iva'";
        $rsPI   = $mysqli->query($porIVA);
        $filaPI = mysqli_fetch_row($rsPI);
        $renglon .= '<label></label>
                    <input type="number" step="1" min="0" max="100" value="' . $filaPI[0] . '" name="porIVA' . $consecutivo . '" id="porIVA' . $consecutivo . '" onkeyup="javascript: porcIVA(' . $consecutivo . ');" class="form-control input-sm" maxlength="100" title="" onkeypress="return txtValida(event,\'dec\', \'porIVA\', \'2\')" onclick="" placeholder="% IVA" style="width: 94%;" >
                    <input type="hidden" id="paramIVA' . $consecutivo . '" value="' . $filaPI[0] . '">
                    </td> <!-- Saldo por pagar -->
                    <td class="campos" align="right" >
                       <!-- Valor Base -->
                       <span id="valorBase' . $consecutivo . '"></span>
                       <input type="text" id="valorBaseNuevo' . $consecutivo . '" name="valorBaseNuevo' . $consecutivo . '" style="display: none; width: 90%;" maxlength="50" placeholder="Valor" onkeypress="return txtValida(event,\'dec\', \'valorBaseNuevo' . $consecutivo . '\', \'2\');" onkeyup="formatC(\'valorBaseNuevo' . $consecutivo . '\')">
                       <input type="hidden" id="valorBaseOcul' . $consecutivo . '">
                       <input type="hidden" id="valorBaseM' . $consecutivo . '" value="">
                    </td>
                    <td class="campos" align="right" >
                       <!-- Retención a Aplicar -->
                       <span id="retencionApl' . $consecutivo . '"></span>
                       <a class="campos" id="ok' . $consecutivo . '" style="cursor: pointer; display: none; position: absolute;" onclick="javascript: aceptarMod(' . $consecutivo . ');">
                       <i title="Ok" class="glyphicon glyphicon-ok">
                       </i>
                       </a>
                       <a class="campos" id="cancelar' . $consecutivo . '" style="cursor: pointer; display: none; position: absolute; margin-left: 16px;" onclick="javascript: cancelarMod(' . $consecutivo . ');">
                       <i title="Cancelar" class="glyphicon glyphicon-remove">
                       </i>
                       </a>
                        <input type="text" id="valorRetencionNuevo' . $consecutivo . '" name="valorRetencionNuevo' . $consecutivo . '" style="display: none; width: 75%;" maxlength="50" placeholder="Valor" onkeypress="return txtValida(event,\'dec\', \'valorRetencionNuevo' . $consecutivo . '\', \'2\');" onkeyup="formatC(\'valorRetencionNuevo' . $consecutivo . '\')">
                        <input type="hidden" id="retencionAplOcul' . $consecutivo . '">
                        <input type="hidden" id="retencionAplicaM' . $consecutivo . '" value="">
                     </td>
                   
                     </tr>'; //Fin
        echo $renglon;
        break;
    ##########CARGAR TIPO RETENCION SEGUN LA CLASE#######    
    case 2:
        $id_clase_ret = $_POST['id_clase_ret'];
        $tipoRet      = "SELECT  id_unico, nombre, porcentajeaplicar   
                        FROM gf_tipo_retencion 
                        WHERE claseretencion = $id_clase_ret AND parametrizacionanno = $anno";
        $rsTR         = $mysqli->query($tipoRet);
        while ($filaTR = mysqli_fetch_row($rsTR)) {
            echo '<option value="' . $filaTR[0] . '">' . ucwords($filaTR[1]) . ' - ' . $filaTR[2] . '%' . '</option>';
        } //$filaTR = mysqli_fetch_row($rsTR)
        break;
    break;
    case 3:
        $tipoRete = $_POST['tipoRete'];
        $aplicar  = $_POST['aplicar'];
        $res      = 0;
        if ($aplicar == 3) {
            $sqlTipoRet = 'SELECT  modificarretencion, modificarbase       
                    FROM gf_tipo_retencion 
                    WHERE id_unico = ' . $tipoRete;
            $tipoRet    = $mysqli->query($sqlTipoRet);
            $rowTR      = mysqli_fetch_row($tipoRet);
            $res        = $rowTR[0] . '|' . $rowTR[1];
        } //$aplicar == 3
        echo $res;
    break;
    case 4:
        $tipoRete = $_POST['tipoRete'];
        $aplicar  = $_POST['aplicar'];
        $valor    = $_POST['valor'];
        $iva      = $_POST['iva'];
        $res      = 0;
        switch ($aplicar) {
            case 2: //Base gravable
                {
                $res = $valor / (1 + $iva / 100);
                break;
            }
            case 4: //IVA
                {
                $res = ($valor / (1 + $iva / 100)) * ($iva / 100);
                break;
            }
            case 5: //Valor Total
                {
                $res = $valor;
                break;
            }
        } //$aplicar
        echo $res;
        
    break;
    case 5:
        $valorBas   = $_POST['valorBas'];
        $idTipRet   = $_POST['idTipRet'];
        $sqlTipoRet = "SELECT  id_unico, limiteinferior, porcentajeaplicar, factorredondeo      
                        FROM gf_tipo_retencion 
                        WHERE id_unico = $idTipRet";
        $tipoRet    = $mysqli->query($sqlTipoRet);
        $rowTR      = mysqli_fetch_row($tipoRet);
        if ($valorBas < $rowTR[1]) {
            $ret = 0;
        } //$valorBas < $rowTR[1]
        else {
            $ret = ($valorBas * $rowTR[2]) / 100;
        }
        
        switch ($rowTR[3]):
            case 1:
                if (is_float($ret) == true) {
                    $num     = number_format($ret, 3, ',', ' ');
                    
                    $decimal = substr($ret, -3);
                    if ($decimal >= 500) {
                        $ret = round($ret); //Factor de redondeo.
                    } //$decimal > 500
                    else {
                        $ret = intval($ret);
                    }
                } //is_float($ret) == true
                else {
                    $ret = $ret;
                }
                break;
            case 10:
                if (is_float($ret) == true) {
                    $pe  = intval($ret);
                    $dec = substr($pe, -1);
                    if ($dec >= 5) {
                        $ret = ceil($pe / 10) * 10;
                    } //$dec > 5
                    else {
                        $ret = floor($pe / 10) * 10;
                    }
                } //is_float($ret) == true
                else {
                	$pe  = intval($ret);
                    $dec = substr($pe, -1);

                    if ($dec >= 5) {
                        $ret = ceil($pe / 10) * 10;
                    } //$dec > 5
                    else {
                        $ret = floor($pe / 10) * 10;
                    }
                }
                break;
            case 100:
                if (is_float($ret) == true) {
                    $pe  = intval($ret);
                    $dec = substr($pe, -2);
                    if ($dec >= 50) {
                        $ret = ceil($pe / 100) * 100;
                    } //$dec > 50
                    else {
                        $ret = floor($pe / 100) * 100;
                    }
                } //is_float($ret) == true
                else {
                	$pe  = intval($ret);
                    $dec = substr($pe, -2);
                    if ($dec >= 50) {
                        $ret = ceil($pe / 100) * 100;
                    } //$dec > 50
                    else {
                        $ret = floor($pe / 100) * 100;
                    }
                }
                break;
            case 1000:
                if (is_float($ret) == true) {
                    $pe  = intval($ret);
                    $dec = substr($pe, -3);
                    if ($dec >= 500) {
                        $ret = ceil($pe / 1000) * 1000;
                    } //$dec > 500
                    else {
                        $ret = floor($pe / 1000) * 1000;
                    }
                } //is_float($ret) == true
                else {
                	$pe  = intval($ret);
                    $dec = substr($pe, -3);
                    if ($dec >=500) {
                        $ret = ceil($pe / 1000) * 1000;
                    } //$dec > 500
                    else {
                        $ret = floor($pe / 1000) * 1000;
                    }
                }
                break;
            default:
                $ret = $ret;
                break;
        endswitch;
        if ($ret < 0) {
            $ret = 0;
        } //$ret < 0
        echo $ret;
    break;
    case 6: //Generar tabla retención
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
            
        } //$numReng == 0
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
        if(empty($_POST['pptal'])) {
            $compRet     = 'NULL';
        } else {
            $compRet     = $_POST['pptal'];
        }
        $compRetcnt  = $_POST['cnt'];
        //var_dump($_POST['tipoRet']);
        for ($i = 0; $i <= $numReng; $i++) {
            
            $sqlRet       = 'SELECT porcentajeaplicar 
					FROM gf_tipo_retencion 
					WHERE id_unico = ' . $tipoRet[$i];
            $tipRetencion = $mysqli->query($sqlRet);
            $rowTR        = mysqli_fetch_row($tipRetencion);
            $porcentaje   = $rowTR[0];
            //$por = (int)$porcenRet[$i];
            $porcentaje   = $porcentaje / 100;
            $valorR       = (int) $valorRet[$i];
            $sqlRetencion = "INSERT INTO gf_retencion (valorretencion, retencionbase, porcentajeretencion, 
                comprobanteretencion,  tiporetencion, comprobante)  
						VALUES($valorR, $retencionBas[$i], $porcentaje, "
                    . "$compRet,  $tipoRet[$i],$compRetcnt )";
            $resultado    = $mysqli->query($sqlRetencion);
            if ($resultado == true) {
                $exito         = 1;
            } //$resultado == true
        } // Termina ciclo for.
        
        if ($exito == 1) {
            echo 1;
        } //$exito == 1
        else {
            echo 2;
        }
        break;
    #* Guardar Retencion en factura 
    case 7:
        $panno      = $_SESSION['anno'];
        $usuario_t  = $_SESSION['usuario_tercero'];
        $anno       = anno($panno);
        $cnt        = $_REQUEST['cnt'];
        $factura    = $_REQUEST['factura'];
        $pptal      = $_REQUEST['id_pptal'];
        #* Buscar Tipo PAgo Retención
        $tp = $con->Listar("SELECT id_unico,cuenta_bancaria  FROM gp_tipo_pago WHERE retencion = 1 AND compania = $compania");
        if(count($tp)>0){
            $tipoPago = $tp[0][0];
            $pg = $con->Listar("SELECT * FROM gp_detalle_pago dp 
                LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
                LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                LEFT JOIN gp_tipo_pago tp ON p.tipo_pago = tp.id_unico 
                WHERE tp.id_unico = $tipoPago AND df.factura =$factura ");
            if(count($pg)>0){
                $id_pago = $pg[0][0];
            } else {
                #Calcular Numero Pago
                $fac = $con->Listar("SELECT * FROM gp_pago WHERE tipo_pago = $tipoPago AND parametrizacionanno = $panno");
                if(count($fac)>0){
                    $sql = $con->Listar("SELECT MAX(numero_pago)  FROM gp_pago WHERE tipo_pago = $tipoPago AND parametrizacionanno = $panno");
                    $numeroPago = $sql[0][0] + 1;
                } else {
                    $numeroPago = $anno. '000001';
                }
                
                $df = $con->Listar("SELECT tercero, fecha_factura FROM gp_factura WHERE id_unico = $factura");
                $sql_cons ="INSERT INTO `gp_pago`
                    ( `numero_pago`, `tipo_pago`, `responsable`,
                    `fecha_pago`,`banco`,`estado`,`parametrizacionanno`,`usuario`)
                VALUES (:numero_pago, :tipo_pago, :responsable,:fecha_pago,:banco,
                :estado, :parametrizacionanno, :usuario)";
                $sql_dato = array(
                    array(":numero_pago",$numeroPago),
                    array(":tipo_pago", $tipoPago),
                    array(":responsable",$df[0][0]),
                    array(":fecha_pago",$df[0][1]),
                    array(":banco",$tp[0][1]),
                    array(":estado",1),
                    array(":parametrizacionanno",$panno),
                    array(":usuario",$usuario_t),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                $pg = $con->Listar("SELECT * FROM gp_pago 
                    WHERE numero_pago =$numeroPago AND tipo_pago = $tipoPago");
                $id_pago = $pg[0][0];
            }
            $sql_cons ="DELETE FROM  `gp_detalle_pago`
            WHERE `pago`=:pago ";
            $sql_dato = array(
                array(":pago",$id_pago)
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            $rt = $con->Listar("SELECT id_unico, valorretencion FROM gf_retencion WHERE comprobante = $cnt");
            for ($i = 0; $i < count($rt); $i++) {
                guardarPagoFactura('',$factura,$id_pago,$rt[$i][1]);
            }
            $sql_cons ="UPDATE  `gp_detalle_factura`
            SET `detallecomprobante` =:detallecomprobante 
            WHERE `factura`=:factura ";
            $sql_dato = array(
                array(":detallecomprobante",NULL),
                array(":factura",$factura),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            eliminardetallescnt($cnt);
            eliminardetallespptal($pptal);
            reconstruirComprobantesFactura($factura);
        }
    break;
    case 8:
        $pptal  = $_REQUEST['id_pptal'];
        $cnt    = $_REQUEST['id_cnt'];
        $factura= $_REQUEST['id_factura'];
        $sql_cons ="UPDATE  `gp_detalle_factura`
        SET `detallecomprobante` =:detallecomprobante 
        WHERE `factura`=:factura ";
        $sql_dato = array(
            array(":detallecomprobante",NULL),
            array(":factura",$factura),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        eliminardetallescnt($cnt);
        eliminardetallespptal($pptal);
        echo reconstruirComprobantesFactura($factura);
        
    break;
    #* Eliminar Retenciones
    case 9:
        $id         =$_POST['id'];
        if(!empty($_REQUEST['factura'])){
            $vlr = $con->Listar("SELECT comprobante 
                FROM gf_retencion where id_unico = $id");
            $cp = $vlr[0][0];
            $factura = $_REQUEST['factura'];
            $pgo = $con->Listar("SELECT dp.pago FROM gp_detalle_pago dp 
            LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
            LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
            LEFT JOIN gp_tipo_pago tp ON p.tipo_pago = tp.id_unico 
            WHERE df.factura=$factura AND tp.retencion = 1");
            $id_pago = $pgo[0][0];
            
            $updt = "DELETE FROM gp_detalle_pago "
                    . "WHERE pago = $id_pago";
            $updt = $mysqli->query($updt);
            
            $updt = "DELETE FROM gf_retencion "
                    . "WHERE id_unico = $id";
            $updt = $mysqli->query($updt);
            if($updt==true){
                $result=1;
            } else {
                $result=2;
            }
            $rt = $con->Listar("SELECT id_unico, valorretencion 
            FROM gf_retencion WHERE comprobante = $cp");
            for ($i = 0; $i < count($rt); $i++) {
                guardarPagoFactura('',$factura,$id_pago,$rt[$i][1]);
            }
           
        } else {         
            $updt = "DELETE FROM gf_retencion "
                    . "WHERE id_unico = $id";
            $updt = $mysqli->query($updt);
            if($updt==true){
                $result=1;
            } else {
                $result=2;
            }
        }
        echo json_decode($result);
    break;
    #* Reconstruir Comprobantes Factura
    case 10:
        $id_factura = $_REQUEST['id_factura'];
        $rowfc          = $con->Listar("SELECT DISTINCT f.id_unico, tr.id_unico, 
            tc.id_unico, tc.comprobante_pptal, tc.tipo_comp_hom , tr.cuenta_bancaria, 
            f.vendedor , f.fecha_factura  
        FROM gp_factura f 
        LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
        LEFT JOIN gp_tipo_pago tr ON tf.tipo_recaudo = tr.id_unico 
        LEFT JOIN gf_tipo_comprobante tc ON tr.tipo_comprobante = tc.id_unico 
        WHERE md5(f.id_unico) = '".$id_factura."'");

        $factura    = $rowfc[0][0];
        $sql_cons ="UPDATE  `gp_detalle_factura`
        SET `detallecomprobante` =:detallecomprobante 
        WHERE `factura`=:factura ";
        $sql_dato = array(
            array(":detallecomprobante",NULL),
            array(":factura",$factura),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        echo reconstruirComprobantesFactura($factura);
        
    break;

    case 11:
        $id_factura = $_REQUEST['id_factura'];
        $panno      = $_SESSION['anno'];
        $rowfc          = $con->Listar("SELECT DISTINCT f.id_unico, tr.id_unico, 
            tc.id_unico, tc.comprobante_pptal, tc.tipo_comp_hom , tr.cuenta_bancaria, 
            f.vendedor , f.fecha_factura  
        FROM gp_factura f 
        LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
        LEFT JOIN gp_tipo_pago tr ON tf.tipo_recaudo = tr.id_unico 
        LEFT JOIN gf_tipo_comprobante tc ON tr.tipo_comprobante = tc.id_unico 
        WHERE md5(f.id_unico) = '".$id_factura."'");

        $factura    = $rowfc[0][0];
        $usuario_c  = $rowfc[0][6];
        $saldof     = saldoFactura($factura);
        $tipoPago   = $rowfc[0][1];
        $banco      = $rowfc[0][5];
        $id_cnt     = '';
        $id_pptal   = '';
        if(round($saldof)!= 0){
            #*** Buscar Pago ***#
            $rowdp = $con->Listar("SELECT DISTINCT p.id_unico, SUM(dp.valor+dp.iva+dp.impoconsumo+dp.ajuste_peso)
            FROM gp_pago p 
            LEFT JOIN gp_detalle_pago dp ON dp.pago = p.id_unico 
            LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
            LEFT JOIN gp_factura f ON df.factura = f.id_unico 
            WHERE f.id_unico = $factura 
            GROUP BY p.id_unico");
            if(!empty($tipoPago) && !empty($banco)){ 
                if(count($rowdp)>0){
                    $pago = $rowdp[0][0];
                    eliminarPago($pago);
                } else {
                    #Buscar Datos Factura
                    $df = $con->Listar("SELECT f.id_unico,
                                    f.numero_factura, tp.nombre,
                                    f.tercero, f.descripcion, f.fecha_factura, f.centrocosto
                                FROM gp_factura f LEFT JOIN gp_tipo_factura tp ON tp.id_unico = f.tipofactura
                                WHERE f.id_unico = $factura");
                    $fecha      = $df[0][5];
                    $responsable= $df[0][3];
                    $centrocosto      = $df[0][6];
                    #Calcular Numero Pago
                    $fac = $con->Listar("SELECT * FROM gp_pago WHERE tipo_pago = $tipoPago AND parametrizacionanno = $panno");
                    if(count($fac)>0){
                        $sql = $con->Listar("SELECT MAX(numero_pago)  FROM gp_pago WHERE tipo_pago = $tipoPago AND parametrizacionanno = $panno");
                        $numeroPago = $sql[0][0] + 1;
                    } else {
                        $numeroPago = $anno. '000001';
                    }
                    $estado = 1;
                    $sql = "INSERT INTO gp_pago
                            (numero_pago,
                            tipo_pago,
                            responsable,
                            fecha_pago,
                            banco,
                            estado, parametrizacionanno, usuario)
                            VALUES('$numeroPago',
                            $tipoPago,$responsable,
                            '$fecha',$banco,$estado, $panno, $usuario_c)";
                    $resultadoP = $mysqli->query($sql);

                    if($resultadoP==true){
                        #********* Buscar el Registro Pago Realizado **************#
                        $idPago = $con->Listar("SELECT MAX(id_unico) FROM gp_pago WHERE numero_pago=$numeroPago AND tipo_pago=$tipoPago");
                        $pago = $idPago[0][0];
                        #************ Registrar Comprobante CNT***************#
                        $tipoComprobanteCnt = $con->Listar("select tipo_comprobante from gp_tipo_pago where id_unico=$tipoPago");
                        if(!empty($tipoComprobanteCnt[0][0])){
                            #Consultamos el ultimo numero de acuerdo al tipo de comprobante
                            $tipocnt =$tipoComprobanteCnt[0][0];
                            $numeroC=$numeroPago;
                            #Descripción del comprobante
                            $descripcion= '"Comprobante de recaudo factura N° '.$df[0][1].' '.$df[0][4].'"';
                            #Insertamos el comprobante
                            $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,tipocomprobante,parametrizacionanno,tercero,estado,compania) "
                                    . "values('$numeroC','$fecha',$descripcion,$tipocnt,$panno,$responsable,'1',$compania)";
                            $resultInsertC=$mysqli->query($sqlInsertC);
                            #Consultamos el ultimo comprobante ingresado
                            $idCnt=$con->Listar("select max(id_unico) from gf_comprobante_cnt where tipocomprobante=$tipocnt and numero=$numeroC");
                            $id_cnt = $idCnt[0][0];
                            
                            #*********** Comprobante Pptal ***********#
                            #Validamos que el tipo de comprobante cnt contenga asocidado un tipo de comprobante cnt o el campo comprobante_pptal no este vacio
                            $tipoComPtal=$con->Listar("select comprobante_pptal from gf_tipo_comprobante where id_unico=$tipocnt");
                            #Validamos que el tipo de comprobante no venga vacio
                            if(!empty($tipoComPtal[0][0])){
                                $tipopptal = $tipoComPtal[0][0];
                                $numeroPp=$numeroPago;
                                #Insertamos los datos en comprobante pptal
                                $insertPptal="insert into "
                                        . "gf_comprobante_pptal(numero,fecha,fechavencimiento,descripcion,parametrizacionanno,tipocomprobante,tercero,estado,responsable) "
                                        . "values('$numeroPp','$fecha','$fecha',$descripcion,$panno,$tipopptal,$responsable,'1',$responsable)";
                                $resultInsertPptal=$mysqli->query($insertPptal);
                                #Consultamos el ultimo comprobante pptal insertado
                                $idPPAL=$con->Listar("select id_unico from gf_comprobante_pptal where tipocomprobante=$tipopptal and numero=$numeroPp");
                                $id_pptal = $idPPAL[0][0];
                            }
                            #************ Registrar Comprobante Causación***************#
                            $tipoComprobanteC=$con->Listar("select tipo_comp_hom from gf_tipo_comprobante where id_unico=".$tipoComprobanteCnt[0][0]);
                            if(!empty($tipoComprobanteC[0][0])){
                                #Consultamos el ultimo numero de acuerdo al tipo de comprobante
                                $tipocau =$tipoComprobanteC[0][0];
                                $numeroCausacion=$numeroPago;
                                #Descripción del comprobante
                                $descripcion= '"Comprobante de causación recaudo factura N° '.$df[0][1].' '.$df[0][4].'"';
                                #Insertamos el comprobante
                                $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,tipocomprobante,parametrizacionanno,tercero,estado,compania) "
                                        . "values('$numeroCausacion','$fecha',$descripcion,$tipocau,$panno,$responsable,'1',$compania)";
                                $resultInsertC=$mysqli->query($sqlInsertC);
                                #Consultamos el ultimo comprobante ingresado
                                $idCau=$con->Listar("select max(id_unico) from gf_comprobante_cnt where tipocomprobante=$tipocau and numero=$numeroCausacion");
                                $id_causacion = $idCau[0][0];

                            }
                        }
                    }
                }
            }
            # Buscar4 Valor Factura
            $vf = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_detalle_factura
            WHERE factura = $factura ");
            $valor = $vf[0][0];
            $dp = guardarPagoFactura('',$factura,$pago, $valor);
            if($dp>0){
                if (empty($id_cnt)){
                    $id_cnt =0;
                }
                if (empty($id_pptal)){
                    $id_pptal =0;
                }
                if (empty($id_causacion)){
                    $id_causacion =0;
                }
                $reg=registrarDetallesPago($pago,$id_cnt,$id_pptal,$id_causacion);
            }
            #********** Actualizar Estado Factura *************#
            $sql = "UPDATE gp_factura 
                SET estado = 5 
                WHERE id_unico=$factura";
            $resultadoP = $mysqli->query($sql);
        } else {
            #*** Buscar Pago ***#
            $rowdp = $con->Listar("SELECT DISTINCT p.id_unico, SUM(dp.valor+dp.iva+dp.impoconsumo+dp.ajuste_peso) 
            FROM gp_pago p 
            LEFT JOIN gp_detalle_pago dp ON dp.pago = p.id_unico 
            LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
            LEFT JOIN gp_factura f ON df.factura = f.id_unico 
            WHERE f.id_unico = $factura 
            GROUP BY p.id_unico");
            for ($p = 0; $p < count($rowdp); $p++) {
                $id_pagoF = $rowdp[$p][0];
                #** Buscar Contabilidad del Pago **#
                $cnt    = 0;
                $pptal  = 0;
                $id_causacion =0;
                $row = $con->Listar("SELECT cn.id_unico as cnt, cp.id_unico as ptal
                        FROM gp_pago p
                        LEFT JOIN gp_detalle_pago dp ON p.id_unico = dp.pago
                        LEFT JOIN gf_detalle_comprobante dc ON dc.id_unico = dp.detallecomprobante
                        LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico
                        LEFT JOIN gf_detalle_comprobante_pptal dpt ON dc.detallecomprobantepptal = dpt.id_unico
                        LEFT JOIN gf_comprobante_pptal cp ON dpt.comprobantepptal = cp.id_unico
                        WHERE p.id_unico = $id_pagoF");
                if(count($row)>0){
                    $cnt    = $row[0][0];
                    $pptal  = $row[0][1];
                    $id_cnt = $cnt ;$id_pptal = $pptal;
                } else {
                    #Buscar Por Número Y Tipo
                    $row2 = $con->Listar("SELECT cn.id_unico as cnt, cp.id_unico as ptal
                        FROM gp_pago p
                        LEFT JOIN gp_detalle_pago dp ON p.id_unico = dp.pago
                        LEFT JOIN gp_tipo_pago tp ON p.tipo_pago = tp.id_unico
                        LEFT JOIN gf_tipo_comprobante tc ON tp.tipo_comprobante = tc.id_unico
                        LEFT JOIN gf_comprobante_cnt cn ON cn.tipocomprobante = tc.id_unico AND cn.numero = p.numero_pago
                        LEFT JOIN gf_comprobante_pptal cp ON cp.tipocomprobante = tc.comprobante_pptal AND cp.numero = p.numero_pago
                        WHERE p.id_unico =$id_pagoF");
                    if(count($row2)>0){
                        $cnt    = $row2[0][0];
                        $pptal  = $row2[0][1];
                        $id_cnt = $cnt ;$id_pptal = $pptal;
                    }

                }
                #*** Buscar Si Tiene Causación
                $cs = causacion($cnt);
                if(!empty($cs[0][0])){
                    $id_causacion =$cs;
                }

                #** Verificar Si el Comprobante está descuadrado **#
                $rowcd = $con->Listar("SELECT DISTINCT 
                            cn.id_unico,
                            cn.numero,
                            tc.sigla,
                            tc.nombre,
                            date_format(cn.fecha,'%d/%m/%Y'),
                            (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND  dc1.valor>0) AS debito1,
                             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND dc1.valor<0 ) AS credito2,
                             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor>0) AS credito, 
                             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor<0) AS debito2  
                        FROM
                        gf_comprobante_cnt cn 
                        LEFT JOIN
                        gf_tipo_comprobante tc
                        ON
                        cn.tipocomprobante = tc.id_unico  
                        WHERE 
                        cn.id_unico=".$cnt);
                $debito1    = $rowcd[0][5];
                $debitoN    = $rowcd[0][8]*-1;
                $credito1   = $rowcd[0][7];
                $creditoN   = $rowcd[0][6]*-1;
                $debito     = $debito1+$debitoN;
                $credito    = $credito1+$creditoN;

                $diferencia = ROUND(($debito -$credito),2);

                if($diferencia != '0' || $diferencia !='-0' || $diferencia != "") {
                    $upd = $sql_cons ="UPDATE `gp_detalle_pago`
                        SET `detallecomprobante`=:detallecomprobante
                        WHERE `pago`=:pago ";
                        $sql_dato = array(
                            array(":detallecomprobante",NULL),
                            array(":pago",$id_pagoF)
                        );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    eliminardetallescnt($id_causacion);
                    eliminardetallescnt($cnt);
                    eliminarDetallesRetencion($cnt);
                    eliminardetallespptal($pptal);
                    $reg=registrarDetallesPago($id_pagoF,$cnt,$pptal,$id_causacion);
                } elseif(count($rowcd)<=0){
                    $upd = $sql_cons ="UPDATE `gp_detalle_pago`
                        SET `detallecomprobante`=:detallecomprobante
                        WHERE `pago`=:pago ";
                        $sql_dato = array(
                            array(":detallecomprobante",NULL),
                            array(":pago",$id_pagoF)
                        );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    eliminardetallescnt($id_causacion);
                    eliminardetallescnt($cnt);
                    eliminarDetallesRetencion($cnt);
                    eliminardetallespptal($pptal);
                    $reg=registrarDetallesPago($id_pagoF,$cnt,$pptal,$id_causacion);
                }
            }
        }

        if($id_cnt !='' || $id_pptal!=''){
            $url = "access.php?controller=Devolutivos&action=index&dev=".$_REQUEST['fat']."&mov=".$_REQUEST['mov'];
            if($id_cnt !=''){$url  .= '&cnt='.md5($id_cnt);}
            if($id_pptal!=''){$url  .= '&pptal='.md5($id_pptal);}    
            
          //  echo $url;
        } else {
            //echo '';
        }    
        echo $factura;
    break;
}

