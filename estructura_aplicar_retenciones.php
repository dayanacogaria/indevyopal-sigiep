<?php

require('funciones/funciones_consulta.php');
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
session_start();
$estruc = $_POST['estruc'];
$anno = $_SESSION['anno'];
$con = new ConexionPDO();
ini_set('memory_limit', '-1');
#****************Consulta Tipo de Compañia*********####
$com = $_SESSION['compania'];
$tcom = "SELECT tipo_compania FROM gf_tercero WHERE id_unico = $com";
$tcom = $mysqli->query($tcom);
if (mysqli_num_rows($tcom) > 0) {
    $tcom = mysqli_fetch_row($tcom);
    $tipocomp = $tcom[0];
} else {
    $tipocomp = 0;
}
switch ($estruc) {
    case 1:
    $id_clase_ret = $_POST['id_clase_ret'];
    $tercero      = $_POST['tercero'];
    #Valida Si la clase es de clase Base de Ingresos
        $sqlCla="SELECT base_ingresos FROM gf_clase_retencion
             WHERE id_unico=$id_clase_ret";
        $resCla    =$mysqli->query($sqlCla);
        $resultCla =mysqli_fetch_row($resCla);
        $base_ing=$resultCla[0];
        #Validar Indicador base Ingresos.
        if ($base_ing==1) {
            #Buscamos el tercero en la tabla  tercero ingresos. 
            $sqlTercIn="SELECT valor_ingresos FROM gf_tercero_ingresos  
                       WHERE tercero=$tercero AND parametrizacionanno=$anno";
            $resTerI=$mysqli->query($sqlTercIn);
                if (mysqli_num_rows($resTerI) > 0) {
                    $resTerIn =mysqli_fetch_row($resTerI);
                    $valor_ingresos=$resTerIn[0];
                    $sqlTipoR="SELECT id_unico FROM gf_tipo_retencion
                               WHERE claseretencion=$id_clase_ret
                               AND parametrizacionanno=$anno
                               AND $valor_ingresos BETWEEN rango_min_ingresos AND rango_max_ingresos
                               AND rango_min_ingresos IS NOT NULL
                               AND rango_max_ingresos IS NOT NULL LIMIT 1";
                    $resTipoR=$mysqli->query($sqlTipoR);
                    if (mysqli_num_rows($resTipoR) > 0) {
                        $resTipoRe =mysqli_fetch_row($resTipoR);
                        $id_tipo_r=$resTipoRe[0];
                            $tipoRet = "SELECT  id_unico, nombre, porcentajeaplicar   
                            FROM gf_tipo_retencion 
                            WHERE claseretencion = $id_clase_ret AND id_unico = $id_tipo_r 
                            AND parametrizacionanno=$anno";
                            $rsTR = $mysqli->query($tipoRet);
                            while ($filaTR = mysqli_fetch_row($rsTR)) {
                                $opts.='<option selected="selected" value="' . $filaTR[0] . '">' . ucwords($filaTR[1]) . ' - ' . $filaTR[2] . '%' . '</option>';
                            } 
                    }else{
                        $valid=1;
                    }
                }else{
                    $valid=1;
                }
        }else{
          $valid=1;
        }
        if ($valid==1) {
            $tipoRet = "SELECT  id_unico, nombre, porcentajeaplicar   
            FROM gf_tipo_retencion 
            WHERE claseretencion = $id_clase_ret AND parametrizacionanno = $anno";
            $rsTR = $mysqli->query($tipoRet);
            while ($filaTR = mysqli_fetch_row($rsTR)) {
             $opts.= '<option value="' . $filaTR[0] . '">' . ucwords($filaTR[1]) . ' - ' . $filaTR[2] . '%' . '</option>';
            } //$filaTR = mysqli_fetch_row($rsTR)
        }

        $datos = array("option"=>$opts,"base_i"=>$base_ing);

        echo json_encode($datos); 
        break;
    case 2: //Calcula valor base según se seleccione en Aplicar sobre.
        $tipoRete = $_POST['tipoRete'];
        $aplicar = $_POST['aplicar'];
        $valor = $_POST['valor'];
        $iva = $_POST['iva'];
        $res = 0;
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
    case 3: //Calcula retención  a aplicar
        $valorBas = $_POST['valorBas'];
        $idTipRet = $_POST['idTipRet'];
        $sqlTipoRet = "SELECT  id_unico, limiteinferior, porcentajeaplicar, factorredondeo      
                        FROM gf_tipo_retencion 
                        WHERE id_unico = $idTipRet";
        $tipoRet = $mysqli->query($sqlTipoRet);
        $rowTR = mysqli_fetch_row($tipoRet);
        if ($valorBas < $rowTR[1]) {
            $ret = 0;
        } //$valorBas < $rowTR[1]
        else {
            $ret = ($valorBas * $rowTR[2]) / 100;
        }

        switch ($rowTR[3]):
            case 1:
                if (is_float($ret) == true) {
                    $num = number_format($ret, 3, ',', ' ');
                    #echo '##'.$num.'##';
                    $decimal = substr($num, -3);
                    #echo '*'.$decimal.'*';
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
                    $pe = intval($ret);
                    $dec = substr($pe, -1);
                    if ($dec >= 5) {
                        $ret = ceil($pe / 10) * 10;
                    } //$dec > 5
                    else {
                        $ret = floor($pe / 10) * 10;
                    }
                } //is_float($ret) == true
                else {
                    $pe = intval($ret);
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
                    $pe = intval($ret);
                    $dec = substr($pe, -2);
                    if ($dec >= 50) {
                        $ret = ceil($pe / 100) * 100;
                    } //$dec > 50
                    else {
                        $ret = floor($pe / 100) * 100;
                    }
                } //is_float($ret) == true
                else {
                    $pe = intval($ret);
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
                    $pe = intval($ret);
                    $dec = substr($pe, -3);
                    if ($dec >= 500) {
                        $ret = ceil($pe / 1000) * 1000;
                    } //$dec > 500
                    else {
                        $ret = floor($pe / 1000) * 1000;
                    }
                } //is_float($ret) == true
                else {
                    $pe = intval($ret);
                    $dec = substr($pe, -3);
                    if ($dec >= 500) {
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
    case 4: //Generar tabla retención

        $num = 0;
        $_SESSION['ultimoRet'] = "";
        $exito = 0;
        $numReng = $_POST['numReng'];
        if ($numReng == 0) {
            $valorRet[0] = $_POST['valorRet'];
            $retencionBas[0] = $_POST['retencionBas'];
            $porcenRet = $_POST['porcenRet'];
            $porcenRet[0] = $porcenRet / 100;
            $tipoRet[0] = $_POST['tipoRet'];
            $cuentaDesRet[0] = $_POST['cuentaDesRet'];
        } //$numReng == 0
        else {
            $valorRet = $_POST['valorRet'];
            $valorRet = stripslashes($valorRet);
            $valorRet = unserialize($valorRet); // Array listo.
            $retencionBas = $_POST['retencionBas'];
            $retencionBas = stripslashes($retencionBas);
            $retencionBas = unserialize($retencionBas); // Array listo.
            $porcenRet = $_POST['porcenRet'];
            $porcenRet = stripslashes($porcenRet);
            $porcenRet = unserialize($porcenRet); // Array listo.
            $tipoRet = $_POST['tipoRet'];
            $tipoRet = stripslashes($tipoRet);
            $tipoRet = unserialize($tipoRet); // Array listo.
            $cuentaDesRet = $_POST['cuentaDesRet'];
            $cuentaDesRet = stripslashes($cuentaDesRet);
            $cuentaDesRet = unserialize($cuentaDesRet); // Array listo.
        }
        $compRet = $_POST['compRet'];
        $sqlComP = 'SELECT id_unico  
            	FROM gf_comprobante_pptal   
                WHERE id_unico = ' . $compRet;
        $compPtal = $mysqli->query($sqlComP);
        $rowCP = mysqli_fetch_row($compPtal);
        $compRet = $rowCP[0];
        for ($i = 0; $i <= $numReng; $i++) {
            $sqlRet = 'SELECT id_unico 
					FROM gf_retencion 
					WHERE comprobanteretencion = ' . $compRet . ' 
					AND tiporetencion = ' . $tipoRet[$i];
            $retencion = $mysqli->query($sqlRet);
            $num = $retencion->num_rows;
            $sqlRet = 'SELECT porcentajeaplicar 
					FROM gf_tipo_retencion 
					WHERE id_unico = ' . $tipoRet[$i];
            $tipRetencion = $mysqli->query($sqlRet);
            $rowTR = mysqli_fetch_row($tipRetencion);
            $porcentaje = $rowTR[0];
            //$por = (int)$porcenRet[$i];
            $porcentaje = $porcentaje / 100;
            $valorR = (int) $valorRet[$i];
            $sqlRetencion = "INSERT INTO gf_retencion (valorretencion, retencionbase, porcentajeretencion, comprobanteretencion, cuentadescuentoretencion, tiporetencion)  
						VALUES($valorR, $retencionBas[$i], $porcentaje, $compRet, $cuentaDesRet[$i], $tipoRet[$i])";
            $resultado = $mysqli->query($sqlRetencion);
            if ($resultado == true) {
                $sqlUltRet = "SELECT MAX(id_unico) FROM gf_retencion";
                $ultRet = $mysqli->query($sqlUltRet);
                $rowUR = mysqli_fetch_row($ultRet);
                $ultimoRet[$i] = $rowUR[0];
                $exito = 1;
            } //$resultado == true
        } // Termina ciclo for.
        $_SESSION['ultimoRet'] = serialize($ultimoRet);
        if ($exito == 1) {
            echo 1;
        } //$exito == 1
        else {
            echo 2;
        }
        break;
    case 5: //Generar comprobante cnt y detalle comprobante (cnt) //Cinco.
        $user = $_SESSION['usuario'];
        $fechaElab = date('Y-m-d');


        $res = 0;
        $numerocontrato = '';
        $numCont = '';
        $clasecontrato = '';
        $claseCon = '';
        $numReng = $_POST['numReng']; //Número de renglones.
        if ($numReng == 0) {
            $valorRet[0] = $_POST['valorRet'];
            $retencionBas[0] = $_POST['retencionBas'];
            $porcenRet = $_POST['porcenRet'];
            $porcenRet[0] = $porcenRet / 100;
            $tipoRet[0] = $_POST['tipoRet'];
            $cuentaCred[0] = $_POST['cuentaCred'];
        } //$numReng == 0
        else {
            $valorRet = $_POST['valorRet'];
            $valorRet = stripslashes($valorRet);
            $valorRet = unserialize($valorRet);
            $retencionBas = $_POST['retencionBas'];
            $retencionBas = stripslashes($retencionBas);
            $retencionBas = unserialize($retencionBas);
            $porcenRet = $_POST['porcenRet'];
            $porcenRet = stripslashes($porcenRet);
            $porcenRet = unserialize($porcenRet);
            $tipoRet = $_POST['tipoRet'];
            $tipoRet = stripslashes($tipoRet);
            $tipoRet = unserialize($tipoRet);
            $cuentaCred = $_POST['cuentaCred'];
            $cuentaCred = stripslashes($cuentaCred);
            $cuentaCred = unserialize($cuentaCred);
        }
        $compRet = $_POST['compRet'];
        if (!empty($_SESSION['ultimoRet'])) {
            $ultimoRet = $_SESSION['ultimoRet'];
            $ultimoRet = stripslashes($ultimoRet);
            $ultimoRet = unserialize($ultimoRet);
        } //!empty($_SESSION['ultimoRet'])
        $sqlComP = "SELECT id_unico, numero, fecha, descripcion, numerocontrato, 
            tercero, clasecontrato, tipocomprobante, proyecto 
            FROM gf_comprobante_pptal   
            WHERE id_unico = $compRet";
        $compPtal = $mysqli->query($sqlComP);
        $rowCP = mysqli_fetch_row($compPtal);
        if (!empty($rowCP[8])) {
            $proyecto = "'" . $rowCP[8] . "'";
        } else {
            $proyecto = 'NULL';
        }
        $compRet = $rowCP[0];
        $anno = $_SESSION['anno'];
        $centrocosto = "SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno";
        $centrocosto = $mysqli->query($centrocosto);
        $centrocosto = mysqli_fetch_row($centrocosto);
        $centrocostoPtal = $centrocosto[0];
        $_SESSION['compRet_Sesion'] = $compRet;
        //Tomar el el parámetro del año.
        $parametroAnno = $_SESSION['anno'];
        //Tomar el el parámetro de la compañía.
        $compania = $_SESSION['compania'];
        //Aquí
        $num_tipoComCnt = 0;
        $sqlTipoCompCnt = "SELECT id_unico, retencion FROM gf_tipo_comprobante WHERE comprobante_pptal = $rowCP[7]";
        $tipoCompCnt = $mysqli->query($sqlTipoCompCnt);
        $num_tipoComCnt = $tipoCompCnt->num_rows;
        if ($num_tipoComCnt == 0) {
            $res = 2;
        } //$num_tipoComCnt == 0
        else {
            $rowTCC = mysqli_fetch_row($tipoCompCnt);
            $tipCompCnt = $rowTCC[0];
            $tipRete = $rowTCC[1];
            ##########NUMERO CONTRATO########
            if (empty($rowCP[4])) {
                $numCont = 'NULL';
            } else {
                $numCont = "'" . $rowCP[4] . "'";
            }
            if (empty($rowCP[6])) {
                $claseCon = 'NULL';
            } else {
                $claseCon = "'" . $rowCP[6] . "'";
            }
            $numeroCnt = $rowCP[1];
            $valorRetL = (int) $valorRet[0]; /////// Valor para comprobante cnt
            $retencionBasL = (int) $retencionBas[0]; /// Valor para comprobante cnt
            //Inserción en comprobante CNT. 
            //El estado es 1 Activo.

            $sqlComprobanteCnt = "INSERT INTO gf_comprobante_cnt (numero, fecha, descripcion, "
                    . "valorbase, valorbaseiva, valorneto, tipocomprobante, compania, "
                    . "parametrizacionanno, usuario, fecha_elaboracion, "
                    . "tercero, estado, numerocontrato, clasecontrato)  
			VALUES('$numeroCnt', '$rowCP[2]', '$rowCP[3]', '$valorRetL', "
                    . "'$retencionBasL', '$valorRetL', '$tipCompCnt', '$compania', "
                    . "'$parametroAnno', '$user', '$fechaElab', "
                    . "'$rowCP[5]',1,$numCont, $claseCon)";
            $resultadoCnt = $mysqli->query($sqlComprobanteCnt);
            if ($resultadoCnt == true) {
                $res = 1; //Añadido.
                $sqlUltComC = "SELECT MAX(id_unico) FROM gf_comprobante_cnt where "
                        . "tipocomprobante = '$tipCompCnt' AND numero = $numeroCnt";
                $ultComC = $mysqli->query($sqlUltComC);
                $rowUC = mysqli_fetch_row($ultComC);
                $ultimoComproCnt = $rowUC[0];
                $sqlDetComP = "SELECT
                        detComP.id_unico,
                        detComP.valor,
                        detComP.proyecto,
                        detComP.rubrofuente, 
                        detComP.tercero, 
                        detComP.comprobanteafectado, 
                        detComP.centro_costo
                      FROM
                        gf_detalle_comprobante_pptal detComP
                      LEFT JOIN
                        gf_comprobante_pptal comP ON comP.id_unico = detComP.comprobantepptal 
                      WHERE
                        comP.id_unico = $compRet";
                $detCompPtal = $mysqli->query($sqlDetComP);
                $numDetalle = $detCompPtal->num_rows; //Número de filas que retorna la consulta.
                $n = 0;
                $numSql = 0;
                $noDetalles = 0;
                $valorAlto = 0;

                if ($_POST['idform'] == '1') {

                    #***************Digitos de Configuración ************#
                    $parm = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Dígitos Interfaz Inventario'";
                    $parm = $mysqli->query($parm);
                    $parm = mysqli_fetch_row($parm);
                    $dig = $parm[0];
                    #************************Registrar Movimientos De Almacen***************#
                    $valAlmacen = 0;

                    if (!empty($_REQUEST['mova'])) {
                        $ids = explode(",", $_REQUEST['mova']);
                        foreach ($ids as $id) {
                            $id = trim($id);
                            if ($id != '') {
                                #***Detalles, Tipo, Plan Movimiento***#
                                $tma = "SELECT m.id_unico, 
                                       m.tipomovimiento, 
                                       dm.planmovimiento, 
                                       m.tercero, 
                                       m.centrocosto, 
                                       m.proyecto, 
                                       dm.id_unico, 
                                       dm.valor, 
                                       dm.iva 
                                   FROM 
                                       gf_movimiento m
                                   LEFT JOIN 
                                       gf_detalle_movimiento dm ON m.id_unico = dm.movimiento 
                                   LEFT JOIN 
                                       gf_plan_inventario pm ON pm.id_unico = dm.planmovimiento 
                                   WHERE 
                                       m.id_unico = $id AND LENGTH(pm.codi)=$dig";
                                $tma = $mysqli->query($tma);
                                if (mysqli_num_rows($tma) > 0) {
                                    while ($row1 = mysqli_fetch_row($tma)) {
                                        #**********Buscar La Configuración*********#
                                        $tipom = $row1[1];
                                        $plan = $row1[2];
                                        $terc = $row1[3];
                                        $cenc = $row1[4];
                                        if(empty($row1[5])){
                                            $proy = 2147483647;
                                        } else {
                                            $proy = $row1[5];
                                        }
                                        $valor = $row1[7] + $row1[8];
                                        $cn = "SELECT 
                                               ca.id_unico, 
                                               ca.cuenta_debito, 
                                               cd.naturaleza 
                                           FROM 
                                               gf_configuracion_almacen ca 
                                           LEFT JOIN 
                                               gf_cuenta cd ON ca.cuenta_debito = cd.id_unico 
                                           WHERE 
                                               ca.plan_inventario = $plan 
                                               AND ca.tipo_movimiento = $tipom
                                               AND ca.parametrizacion_anno = $parametroAnno";
                                        $cn = $mysqli->query($cn);
                                        $cuentas = mysqli_fetch_row($cn);
                                        $cuenta_debito = $cuentas[1];
                                        $naturaleza_debito = $cuentas[2];
                                        if ($naturaleza_debito == 2) {
                                            $vDeb = $valor * -1;
                                        } else {
                                            $vDeb = $valor;
                                        }
                                        $sqlDetComCnt = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, 
                                           valor, valorejecucion, comprobante, cuenta, naturaleza, 
                                           tercero, proyecto, centrocosto)  
                                           VALUES('$rowCP[2]', '$rowCP[3]', $vDeb, $valor, $ultimoComproCnt, "
                                                . "$cuenta_debito, $naturaleza_debito, $terc, $proy, $cenc)";
                                        $resultadoDetComCnt = $mysqli->query($sqlDetComCnt);
                                        if ($resultadoDetComCnt == true) {
                                            $valAlmacen += $vDeb;
                                            #************Actualizar Movimiento Almacen*******#
                                            $upd = "UPDATE gf_movimiento 
                                                   SET afectado_contabilidad = '$ultimoComproCnt' 
                                                   WHERE id_unico = $id";
                                            $upd = $mysqli->query($upd);
                                        }
                                    }
                                }
                            }
                        }
                        #*******Consultar El valor Totoal De La CXP ***#
                        $cuen = "SELECT SUM(valor) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $compRet";
                        $cuen = $mysqli->query($cuen);
                        $cuen = mysqli_fetch_row($cuen);
                        $vtotal = $cuen[0];
                        $vcxp = $vtotal - $valAlmacen;
                        if ($vtotal == $valAlmacen) {
                            $z = 0;
                            while ($rowDCP = mysqli_fetch_row($detCompPtal)) { //Aquí
                                if ($tipocomp == 2) {
                                    $tercero = $rowCP[5];
                                } else {
                                    $tercero = $rowDCP[4];
                                }
                                //=============================================================
                                $debitoCP = $_SESSION['debitoCP'];
                                $debitoCP = stripslashes($debitoCP);
                                $debitoCP = unserialize($debitoCP);
                                $idV = $rowDCP[0];
                                $crc = $debitoCP[$idV];
                                //BUSCAR DEBITO CRB
                                $crcu = "SELECT cuenta_debito, cuenta_credito FROM gf_concepto_rubro_cuenta WHERE id_unico = $crc";
                                $crcu = $mysqli->query($crcu);
                                $cuentas = mysqli_fetch_row($crcu);
                                $cuentaDeb = $cuentas[0];
                                $ctaC = $cuentas[1];

                                ####BUSCAR CUENTA CREDITO ####
                                $queryCuen = "SELECT DISTINCT 
                                       crc.cuenta_credito , c.naturaleza ,crc.centrocosto 
                                     FROM
                                       gf_concepto_rubro cr
                                     LEFT JOIN
                                       gf_rubro_fuente rf ON cr.rubro = rf.rubro
                                     LEFT JOIN
                                       gf_concepto_rubro_cuenta crc ON crc.concepto_rubro = cr.id_unico
                                     LEFT JOIN 
                                       gf_cuenta c ON crc.cuenta_credito = c.id_unico
                                     WHERE
                                       rf.id_unico=$rowDCP[3]  AND crc.cuenta_credito=$ctaC";
                                $cuent = $mysqli->query($queryCuen);
                                $rowC = mysqli_fetch_row($cuent);
                                $cuenta = $rowC[0];
                                $naturaleza = $rowC[1];
                                $centrocosto = $rowC[2];
                                if ($centrocosto == '') {
                                    $anno = $_SESSION['anno'];
                                    $centrocosto = "SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno";
                                    $centrocosto = $mysqli->query($centrocosto);
                                    $centrocosto = mysqli_fetch_row($centrocosto);
                                    $centrocosto = $centrocosto[0];
                                }
                                if ($naturaleza == 1) {
                                    $vcred = $rowDCP[1] * -1;
                                }
                                if(empty($rowDCP[2])){
                                    $proy = 2147483647;
                                } else {
                                    $proy = $rowDCP[2];
                                }
                                $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, 
                                   valor, valorejecucion, comprobante, cuenta, naturaleza, 
                                   tercero, proyecto, centrocosto, detallecomprobantepptal)  
                                   VALUES('$rowCP[2]', '$rowCP[3]', $vcred, $rowDCP[1], $ultimoComproCnt, "
                                        . "$cuenta, $naturaleza, $tercero, $proy, $centrocosto, $rowDCP[0])";
                                $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                            }
                        } else {
                            $sqlDetComP = "SELECT
                                       detComP.id_unico,
                                       detComP.valor,
                                       detComP.proyecto,
                                       detComP.rubrofuente, 
                                       detComP.tercero , 
                                       detComP.comprobanteafectado 
                                     FROM
                                       gf_detalle_comprobante_pptal detComP
                                     LEFT JOIN
                                       gf_comprobante_pptal comP ON comP.id_unico = detComP.comprobantepptal 
                                     WHERE
                                       comP.id_unico = $compRet ORDER BY valor ASC ";
                            $detCompPtal1 = $mysqli->query($sqlDetComP);
                            while ($rowDCP = mysqli_fetch_row($detCompPtal1)) { //Aquí
                                if ($tipocomp == 2) {
                                    $tercero = $rowCP[5];
                                } else {
                                    $tercero = $rowDCP[4];
                                }
                                //=============================================================
                                $debitoCP = $_SESSION['debitoCP'];
                                $debitoCP = stripslashes($debitoCP);
                                $debitoCP = unserialize($debitoCP);
                                $idV = $rowDCP[0];
                                $crc = $debitoCP[$idV];
                                //BUSCAR DEBITO CRB
                                $crcu = "SELECT cuenta_debito, cuenta_credito FROM gf_concepto_rubro_cuenta WHERE id_unico = $crc";
                                $crcu = $mysqli->query($crcu);
                                $cuentas = mysqli_fetch_row($crcu);
                                $cuentaDeb = $cuentas[0];
                                $ctaC = $cuentas[1];
                                $queryCuen = "SELECT cuen.id_unico, cuen.naturaleza, conRubCun.centrocosto   
                                           FROM gf_cuenta cuen 
                                           LEFT JOIN gf_concepto_rubro_cuenta conRubCun ON conRubCun.cuenta_debito = cuen.id_unico 
                                           WHERE cuen.id_unico = $cuentaDeb";
                                $cuent = $mysqli->query($queryCuen);
                                $rowC = mysqli_fetch_row($cuent);
                                $cuenta = $rowC[0];
                                $naturaleza = $rowC[1];
                                $centrocosto = $rowC[2];
                                if ($centrocosto == '') {
                                    $anno = $_SESSION['anno'];
                                    $centrocosto = "SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno";
                                    $centrocosto = $mysqli->query($centrocosto);
                                    $centrocosto = mysqli_fetch_row($centrocosto);
                                    $centrocosto = $centrocosto[0];
                                } //$centrocosto == ''
                                $valorr = $rowDCP[1];

                                #********Verificar Valores***************#
                                if ($valAlmacen > $valorr) {
                                    $valAlmacen = $valAlmacen - $valorr;
                                } else {
                                    $vDeb = $valorr - $valAlmacen;
                                    if ($naturaleza == 2) {
                                        $vDeb = $vDeb * -1;
                                    }
                                    $cuentaDebito = $cuenta;
                                    if(empty($rowDCP[2])){
                                        $proy = 2147483647;
                                    } else {
                                        $proy = $rowDCP[2];
                                    }
                                    //Inserción en detalle comprobante (cnt) CUENTA DEBITO 
                                    $sqlDetComCnt = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, 
                                           valor, valorejecucion, comprobante, cuenta, naturaleza, 
                                           tercero, proyecto, centrocosto, detallecomprobantepptal)  
                                           VALUES('$rowCP[2]', '$rowCP[3]', $vDeb, $rowDCP[1], $ultimoComproCnt, "
                                            . "$cuenta, $naturaleza, $tercero, $proy, $centrocosto, $rowDCP[0])";
                                    $resultadoDetComCnt = $mysqli->query($sqlDetComCnt);
                                }
                                ####BUSCAR CUENTA CREDITO ####
                                $queryCuen = "SELECT DISTINCT 
                                       crc.cuenta_credito , c.naturaleza ,crc.centrocosto 
                                     FROM
                                       gf_concepto_rubro cr
                                     LEFT JOIN
                                       gf_rubro_fuente rf ON cr.rubro = rf.rubro
                                     LEFT JOIN
                                       gf_concepto_rubro_cuenta crc ON crc.concepto_rubro = cr.id_unico
                                     LEFT JOIN 
                                       gf_cuenta c ON crc.cuenta_credito = c.id_unico
                                     WHERE
                                       rf.id_unico=$rowDCP[3]  AND crc.cuenta_credito=$ctaC";
                                $cuent = $mysqli->query($queryCuen);
                                $rowC = mysqli_fetch_row($cuent);
                                $cuenta = $rowC[0];
                                $naturaleza = $rowC[1];
                                $centrocosto = $rowC[2];
                                if ($centrocosto == '') {
                                    $anno = $_SESSION['anno'];
                                    $centrocosto = "SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno";
                                    $centrocosto = $mysqli->query($centrocosto);
                                    $centrocosto = mysqli_fetch_row($centrocosto);
                                    $centrocosto = $centrocosto[0];
                                } //$centrocosto == ''
                                $vcred = $rowDCP[1];
                                if ($naturaleza == 1) {
                                    $vcred = $rowDCP[1] * -1;
                                }
                                if(empty($rowDCP[2])){
                                    $proy = 2147483647;
                                } else {
                                    $proy = $rowDCP[2];
                                }
                                //Inserción en detalle comprobante (cnt) para saldo
                                $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, 
                                   valor, valorejecucion, comprobante, cuenta, naturaleza, 
                                   tercero, proyecto, centrocosto, detallecomprobantepptal)  
                                   VALUES('$rowCP[2]', '$rowCP[3]', $vcred, $rowDCP[1], $ultimoComproCnt, "
                                        . "$cuenta, $naturaleza, $tercero, $proy, $centrocosto, $rowDCP[0])";
                                $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                            }
                        }
                    } else {

                        while ($rowDCP = mysqli_fetch_row($detCompPtal)) {
                            if (empty($rowDCP[6])) {
                                #** Buscar Centro Costo Varios **#
                                $cv = $con->Listar("SELECT * FROM gf_centro_costo 
                                WHERE parametrizacionanno = $anno AND nombre ='Varios'");
                                if (count($cv) > 0) {
                                    $centrocosto = $cv[0][0];
                                } else {
                                    $centrocosto = 'NULL';
                                }
                            } else {
                                $centrocosto = $rowDCP[6];
                            }

                            if (empty($rowDCP[4])) {
                                $tercero = $rowCP[5];
                            } else {
                                $tercero = $rowDCP[4];
                            }

                            if (!empty($_SESSION['debitoCP'])) {
                                $debitoCP = $_SESSION['debitoCP'];
                                $debitoCP = stripslashes($debitoCP);
                                $debitoCP = unserialize($debitoCP);
                                $idV = $rowDCP[0];
                                $crc = $debitoCP[$idV];
                            } //!empty($_SESSION['debitoCP'])
                            else {
                                $crc = '';
                            }
                            //BUSCAR DEBITO CRB
                            $crcu = "SELECT cuenta_debito, cuenta_credito FROM gf_concepto_rubro_cuenta WHERE id_unico = $crc";
                            ;
                            $crcu = $mysqli->query($crcu);
                            $cuentas = mysqli_fetch_row($crcu);
                            $cuentaDeb = $cuentas[0];
                            $ctaC = $cuentas[1];
                            $cuantasCuentas = 0;
                            $queryCuen = "SELECT cuen.id_unico, cuen.naturaleza, conRubCun.centrocosto   
                                        FROM gf_cuenta cuen 
                                        LEFT JOIN gf_concepto_rubro_cuenta conRubCun ON conRubCun.cuenta_debito = cuen.id_unico 
                                        WHERE cuen.id_unico = '$cuentaDeb'
                                        GROUP BY cuen.id_unico";
                            $cuent = $mysqli->query($queryCuen);
                            $cuantasCuentas = $cuent->num_rows;
                            $atribCuenta = '';
                            $atribNaturaleza = '';
                            $atribCentroCosto = '';
                            $valCuenta = '';
                            $valNaturaleza = '';
                            $valCentroCosto = '';
                            $cuenta = '';
                            $naturaleza = '';
                            if ($cuantasCuentas != 0) {
                                $rowC = mysqli_fetch_row($cuent);
                                $cuenta = $rowC[0];
                                $naturaleza = $rowC[1];
                                $atribCuenta = 'cuenta, ';
                                $valCuenta = $cuenta . ', ';
                                $atribNaturaleza = 'naturaleza, ';
                                $valNaturaleza = $naturaleza . ', ';
                                //Inserción en detalle comprobante (cnt)
                                //Primer insert para detalle comprobante cnt
                                $vdeb = $rowDCP[1];
                                if ($naturaleza == 2) {
                                    $vdeb = $rowDCP[1] * -1;
                                } //$naturaleza == 2
                                if(!empty($rowDCP[2])){
                                    $proyecto = $rowDCP[2];
                                }
                                $sqlDetComCnt = "INSERT INTO gf_detalle_comprobante "
                                        . "(fecha, descripcion, valor, valorejecucion, "
                                        . "comprobante, $atribCuenta $atribNaturaleza tercero, "
                                        . "proyecto, centrocosto,detallecomprobantepptal)  
                                       VALUES('$rowCP[2]', '$rowCP[3]', '$vdeb', '$rowDCP[1]', "
                                        . "'$ultimoComproCnt', $valCuenta $valNaturaleza "
                                        . "'$tercero', '$proyecto', $centrocosto, '$rowDCP[0]')";
                                $resultadoDetComCnt = $mysqli->query($sqlDetComCnt);
                            } //$cuantasCuentas != 0
                            $compRet = $_POST['compRet'];
                            ####BUSCAR CUENTA CREDITO ####
                            $queryCuen = "SELECT DISTINCT 
                                            c.id_unico , c.naturaleza ,crc.centrocosto 
                                          FROM
                                            gf_concepto_rubro cr
                                          LEFT JOIN
                                            gf_rubro_fuente rf ON cr.rubro = rf.rubro
                                          LEFT JOIN
                                            gf_concepto_rubro_cuenta crc ON crc.concepto_rubro = cr.id_unico
                                          LEFT JOIN 
                                            gf_cuenta c ON crc.cuenta_credito = c.id_unico
                                          WHERE
                                            rf.id_unico=$rowDCP[3]  AND crc.cuenta_credito=$ctaC";
                            $cuent = $mysqli->query($queryCuen);
                            $rowC = mysqli_fetch_row($cuent);
                            $cuenta = $rowC[0];
                            $naturaleza = $rowC[1];
                            $vcred = $rowDCP[1];
                            if ($naturaleza == 1) {
                                $vcred = $rowDCP[1] * -1;
                            }
                            if(!empty($rowDCP[2])){
                                $proyecto = $rowDCP[2];
                            }
                            //Inserción en detalle comprobante (cnt) para saldo
                            $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, valor, valorejecucion, 
                                comprobante, cuenta, naturaleza, tercero, proyecto, centrocosto, detallecomprobantepptal)  
                                VALUES('$rowCP[2]', '$rowCP[3]', $vcred, $rowDCP[1], "
                                    . "$ultimoComproCnt, $cuenta, $naturaleza, "
                                    . "$tercero, $proyecto, $centrocosto, $rowDCP[0])";
                            $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                        } //$rowDCP = mysqli_fetch_row($detCompPtal)
                    //
                        //
                    }
                    ##VALOR RETENCIONES###
                    $valorDis = 0;
                    for ($j = 0; $j <= $numReng; $j++) {
                        #*** Buscar que factor de aplicacion tiene tipo retencion**#
                        $fa = $con->Listar("SELECT tr.factoraplicacion, fa.nombre
                             FROM gf_tipo_retencion tr 
                             LEFT JOIN gf_factor_aplicacion fa ON tr.factoraplicacion = fa.id_unico 
                             WHERE tr.id_unico =" . $tipoRet[$j]);
                        if ($fa[0][1] == 'Descontable') {
                        } else {
                            $valorDis = $valorDis + (int) $valorRet[$j];
                        }
                        
                    } //$j = 0; $j <= $numReng; $j++
                    $modificacionValor = "SELECT MAX(valor) FROM gf_detalle_comprobante WHERE comprobante=$ultimoComproCnt AND naturaleza=2 AND valor >0";
                    $modificacionValor = $mysqli->query($modificacionValor);
                    $modificacionValor = mysqli_fetch_row($modificacionValor);
                    #ID 
                    $idM = "SELECT MAX(id_unico) FROM gf_detalle_comprobante " . "WHERE comprobante=$ultimoComproCnt " . "AND naturaleza=2 AND valor = '$modificacionValor[0]'";
                    $idM = $mysqli->query($idM);
                    $idM = mysqli_fetch_row($idM);
                    $idMod = $idM[0];
                    if ($valorDis <= $modificacionValor[0]) {
                        $valorMod = $modificacionValor[0] - $valorDis;

                        $actualizar = "UPDATE gf_detalle_comprobante SET valor = $valorMod WHERE id_unico = $idMod";
                        $actualizar = $mysqli->query($actualizar);
                    } //$valorDis <= $modificacionValor[0]
                    else {
                        $modificacionValor2 = "SELECT MAX(valor) FROM gf_detalle_comprobante WHERE comprobante=$ultimoComproCnt AND naturaleza=2 AND id_unico !='$idMod'";
                        $modificacionValor2 = $mysqli->query($modificacionValor2);
                        $modificacionValor2 = mysqli_fetch_row($modificacionValor2);
                        $valor2 = $valorDis - $modificacionValor[0];
                        $valorMod2 = $modificacionValor2[0] - $valor2;
                        $valorMod = 0;
                        #ID 
                        $idM = "SELECT MAX(id_unico) FROM gf_detalle_comprobante " . "WHERE comprobante=$ultimoComproCnt " . "AND naturaleza=2 AND valor = '$modificacionValor[0]'";
                        $idM = $mysqli->query($idM);
                        $idM = mysqli_fetch_row($idM);
                        $idMod = $idM[0];
                        $actualizar = "UPDATE gf_detalle_comprobante SET valor = $valorMod WHERE id_unico = $idMod";
                        $actualizar = $mysqli->query($actualizar);
                        #ID 
                        $idM = "SELECT MAX(id_unico) FROM gf_detalle_comprobante " . "WHERE comprobante=$ultimoComproCnt " . "AND naturaleza=2 AND valor = '$modificacionValor2[0]'";
                        $idM = $mysqli->query($idM);
                        $idM = mysqli_fetch_row($idM);
                        $idMod = $idM[0];
                        $actualizar2 = "UPDATE gf_detalle_comprobante SET valor = $valorMod2 WHERE id_unico = $idMod";
                        $actualizar2 = $mysqli->query($actualizar2);
                    }
                    ######################################################################################
                    //Inserta las retenciones
                    for ($i = 0; $i <= $numReng; $i++) {
                        $sqlTipRet = "SELECT tr.cuenta , fa.nombre
                             FROM gf_tipo_retencion tr 
                             LEFT JOIN gf_factor_aplicacion fa ON tr.factoraplicacion = fa.id_unico  
                             WHERE tr.id_unico = '$tipoRet[$i]'";
                        $tipRet = $mysqli->query($sqlTipRet);
                        $rowTR = mysqli_fetch_row($tipRet);
                        $tipRete = $rowTR[0];
                        $cuantasCuentas = 0;
                        $queryCuen = "SELECT cuen.id_unico, cuen.naturaleza    
                                        FROM gf_cuenta cuen 
                                        WHERE cuen.id_unico = '$tipRete'";
                        $cuent = $mysqli->query($queryCuen);
                        $cuantasCuentas = $cuent->num_rows; //Número de cuentas
                        $atribCuenta = '';
                        $atribNaturaleza = '';
                        $valCuenta = '';
                        $valNaturaleza = '';
                        $cuenta = '';
                        $naturaleza = '';
                        if ($cuantasCuentas != 0) {
                            $rowC = mysqli_fetch_row($cuent);
                            $cuenta = $rowC[0];
                            $naturaleza = $rowC[1];
                            $atribCuenta = 'cuenta, ';
                            $valCuenta = $cuenta . ', ';
                            $atribNaturaleza = 'naturaleza, ';
                            $valNaturaleza = $naturaleza . ', ';
                            
                            $valR = (int) $valorRet[$i];
                            if ($rowTR[1] == 'Descontable') {
                                $valR = $valR *- 1;
                                #Buscar Cuenta  Actualizar 
                                $ctac = $con->Listar("select dc.id_unico, 
                                    IF(dc.valor<0, dc.valor*-1, dc.valor) , 
                                    dc.naturaleza 
                                    from gf_detalle_comprobante dc 
                                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                where dc.comprobante = $ultimoComproCnt
                                AND IF(c.naturaleza =1, dc.valor>0, dc.valor<0) 
                                order by dc.id_unico asc
                                LIMIT 1");
                                $id = $ctac[0][0];
                                $vm = $ctac[0][1] - $valorRet[$i];   
                                if($ctac[0][2]==2){
                                    $vm = $vm*-1;
                                }
                                $sql_cons ="UPDATE `gf_detalle_comprobante`
                                    SET `valor`=:valor 
                                    WHERE `id_unico`=:id_unico ";
                                    $sql_dato = array(
                                        array(":valor",$vm),
                                        array(":id_unico",$id)
                                    );
                                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                            }
                            //Inserción en detalle comprobante (cnt)
                            //Insert para las retenciones.
                            $sqlDetComCntRet = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, 
                         valor, valorejecucion, comprobante, $atribCuenta $atribNaturaleza tercero,  
                             centrocosto, proyecto)  
                         VALUES('$rowCP[2]', '$rowCP[3]', $valR, $valR, '$ultimoComproCnt',"
                                    . "$valCuenta $valNaturaleza '$rowCP[5]', $centrocostoPtal, $proyecto)";
                            //echo $sqlDetComCntRet;
                            $resultadoDetComCnt = $mysqli->query($sqlDetComCntRet);
                        } //$cuantasCuentas != 0
                        else {
                            $res = 3;
                        }
                    } //$i = 0; $i <= $numReng; $i++
                    if (!empty($_SESSION['ultimoRet'])) {
                        foreach ($ultimoRet as $value) {
                            $updateRetencion = 'UPDATE gf_retencion 
                                                                    SET comprobante = ' . $ultimoComproCnt . '  
                                                                    WHERE id_unico = ' . $value;
                            $resultadoUpRet = $mysqli->query($updateRetencion);
                        } //$ultimoRet as $value
                    } //!empty($_SESSION['ultimoRet'])
                    $_SESSION['idCompCntV'] = $ultimoComproCnt;
                    $_SESSION['ultimoRet'] = "";
                    $_SESSION['cntEgreso'] = $ultimoComproCnt;
                    //					$res = 1; Estaba
                } else {

                    $cuentax = $con->Listar("SELECT GROUP_CONCAT(DISTINCT cn.comprobante) 
                    FROM gf_detalle_comprobante_pptal dc 
                    LEFT JOIN gf_detalle_comprobante_pptal dcc ON dc.comprobanteafectado = dcc.id_unico 
                    LEFT JOIN gf_detalle_comprobante cn ON cn.detallecomprobantepptal = dcc.id_unico 
                    LEFT JOIN gf_comprobante_cnt cnn ON cn.comprobante = cnn.id_unico 
                    LEFT JOIN gf_tipo_comprobante tc ON cnn.tipocomprobante = tc.id_unico 
                    WHERE dc.comprobantepptal =$compRet and tc.clasecontable =13");
                    $id_comp_cnt = $cuentax[0][0];

                    ##############VERIFICAR SI LAS CUENTAS QUE TRAE SON DE NÓMINA O NO #######################
                    #CONTAR LOS DETALLES
                    $numc = "SELECT DISTINCT dc.id_unico FROM gf_detalle_comprobante  dc "
                            . "WHERE dc.comprobante IN ($id_comp_cnt) ";
                    $numc = $mysqli->query($numc);
                    $numc = mysqli_num_rows($numc);
                    #CONTAR LOS DETALLES CON CUENTAS PASIVO NOMINA 
                    $nump = "SELECT DISTINCT dc.id_unico FROM gf_detalle_comprobante dc "
                            . "LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico "
                            . "WHERE dc.comprobante IN ($id_comp_cnt) "
                            . "AND c.clasecuenta = 20";
                    $nump = $mysqli->query($nump);
                    $nump = mysqli_num_rows($nump);
                    #########SI HAY CUENTAS DE PASIVO NOMINA
                    if ($nump > 0) {
                        #####SI TODAS LAS CUENTAS SON DE PASIVO NOMINA 
                        #CONTAR LOS DETALLES CON CUENTAS PASIVO NOMINA Y CLASE !6 Y !8
                        $numcom = "SELECT DISTINCT dc.id_unico FROM gf_detalle_comprobante dc "
                                . "LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico "
                                . "WHERE dc.comprobante IN ($id_comp_cnt) "
                                . "AND (c.clasecuenta !=6 AND c.clasecuenta !=8)";
                        $numcom = $mysqli->query($numcom);
                        $numcom = mysqli_num_rows($numcom);
                        //var_dump($numc==$numcom);
                        if ($numc == $numcom) {
                            #SE TRAEN TODAS LAS CUENTAS DE PASIVO
                            $sqlDetCom = "SELECT detCom.id_unico, detCom.valor, detCom.proyecto, 
                               detCom.cuenta, detCom.naturaleza, detCom.centrocosto, detCom.detallecomprobantepptal, 
                               detCom.tercero 
                                FROM gf_detalle_comprobante detCom 
                                LEFT JOIN gf_comprobante_cnt com ON com.id_unico = detCom.comprobante 
                                LEFT JOIN gf_cuenta CT ON detCom.cuenta = CT.id_unico 
                                LEFT JOIN gf_clase_cuenta clacu ON clacu.id_unico = CT.clasecuenta 
                                WHERE com.id_unico IN ($id_comp_cnt) and clacu.id_unico = 20 ";
                            $detComp = $mysqli->query($sqlDetCom);
                            while ($rowDC = mysqli_fetch_row($detComp)) {
                                $terdeta = $rowDC[7];
                                $valor = $rowDC[1];
                                $valor = $valor * -1;
                                if(!empty($rowDC[2])){
                                    $proyecto = $rowDC[2];
                                }
                                $cuenta = $rowDC[3];
                                $naturaleza = $rowDC[4];
                                $centrocosto = $rowDC[5];
                                $detallecomprobantepptal = $rowDC[6];

                                if (empty($rowDC[6])) {
                                    $detallecomprobantepptal = 'NULL';
                                }
                                $afec = $rowDC[0];

                                if (empty($rowComp[3])) {
                                    $descripcion = 'NULL';
                                }
                                $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (descripcion, fecha, valor, valorejecucion, comprobante, 
                                    cuenta, naturaleza, tercero, proyecto, centrocosto,  detalleafectado, detallecomprobantepptal )  
                                                        VALUES($descripcion, '$fecha', $valor, "
                                        . "$valor, $ultimoComproCnt, $cuenta, $naturaleza, " . "$terdeta, $proyecto, $centrocosto,$afec, $detallecomprobantepptal )";
                                $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                            }
                            #############SI TODAS LA CUENTAS NO SON DE PASIVO TEMPORAL##################
                        } else {
                            #SE TRAEN TODAS LAS CUENTAS DE PASIVO
                            $sqlDetCom = "SELECT detCom.id_unico, detCom.valor, detCom.proyecto, 
                               detCom.cuenta, detCom.naturaleza, detCom.centrocosto, detCom.detallecomprobantepptal, 
                               detCom.tercero 
                                FROM gf_detalle_comprobante detCom 
                                LEFT JOIN gf_comprobante_cnt com ON com.id_unico = detCom.comprobante 
                                LEFT JOIN gf_cuenta CT ON detCom.cuenta = CT.id_unico 
                                LEFT JOIN gf_clase_cuenta clacu ON clacu.id_unico = CT.clasecuenta 
                                WHERE ( com.id_unico IN ($id_comp_cnt) and clacu.id_unico = 4) 
                                OR ( com.id_unico IN ($id_comp_cnt) and clacu.id_unico = 8) 
                                AND clacu.id_unico !=20 ";
                            $detComp = $mysqli->query($sqlDetCom);
                            while ($rowDC = mysqli_fetch_row($detComp)) {
                                $terdeta = $rowDC[7];
                                $valorp = $rowDC[1];
                                #*** Buscar Si Tiene Afectado ***#
                                $af = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) FROM gf_detalle_comprobante dc WHERE dc.detalleafectado = $rowDC[0] ");
                                if (empty($af[0][0])) {
                                    $afect = 0;
                                } else {
                                    $afect = $af[0][0];
                                }
                                if ($valorp < 0) {
                                    $valorp = $valorp * -1 - $afect;
                                    $valorp = $valorp * -1;
                                } else {
                                    $valorp = $valorp - $afect;
                                }
                                $valor = $valorp * -1;
                                if(!empty($rowDC[2])){
                                    $proyecto = $rowDC[2];
                                }
                                $cuenta = $rowDC[3];
                                $naturaleza = $rowDC[4];
                                $centrocosto = $rowDC[5];
                                $detallecomprobantepptal = $rowDC[6];

                                if (empty($rowDC[6])) {
                                    $detallecomprobantepptal = 'NULL';
                                }
                                ##DETALLE CNT AFECTADO##
                                $afec = $rowDC[0];
                                if (empty($rowComp[3])) {
                                    $descripcion = 'NULL';
                                }
                                if ($valor != 0 || $valor != -0) {
                                    $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (descripcion, fecha, valor, valorejecucion, comprobante, 
                                    cuenta, naturaleza, tercero, proyecto, centrocosto,  detalleafectado, detallecomprobantepptal )  
                                                        VALUES($descripcion, '$fecha', $valor, $valor, "
                                            . "$ultimoComproCnt, $cuenta, $naturaleza, " . "$terdeta, "
                                            . "$proyecto, $centrocosto,$afec, $detallecomprobantepptal )";
                                    $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                                }
                            }
                        }
                    } else {

                        $sqlDetCom = "SELECT detCom.id_unico, detCom.valor, 
                        detCom.proyecto, detCom.cuenta, detCom.naturaleza, detCom.centrocosto, detCom.detallecomprobantepptal, 
                        detCom.tercero 
                       FROM gf_detalle_comprobante detCom 
                       LEFT JOIN gf_comprobante_cnt com ON com.id_unico = detCom.comprobante 
                       LEFT JOIN gf_cuenta CT ON detCom.cuenta = CT.id_unico 
                       LEFT JOIN gf_clase_cuenta clacu ON clacu.id_unico = CT.clasecuenta 
                       WHERE ( com.id_unico IN ($id_comp_cnt) and clacu.id_unico = 4) 
                       OR ( com.id_unico IN ($id_comp_cnt) and clacu.id_unico = 8)";
                        $detComp = $mysqli->query($sqlDetCom);

                        while ($rowDC = mysqli_fetch_row($detComp)) {
                            $valorp = $rowDC[1];
                            #*** Buscar Si Tiene Afectado ***#
                            $af = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) FROM gf_detalle_comprobante dc WHERE dc.detalleafectado = $rowDC[0] ");
                            # echo "SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) FROM gf_detalle_comprobante dc WHERE dc.detalleafectado = $rowDC[0] ";
                            if (empty($af[0][0])) {
                                $afect = 0;
                            } else {
                                $afect = $af[0][0];
                            }
                            if ($valorp < 0) {
                                $valorp = $valorp * -1 - $afect;
                                $valorp = $valorp * -1;
                            } else {
                                $valorp = $valorp - $afect;
                            }
                            $valor = $valorp * -1;
                            if(!empty($rowDC[2])){
                                $proyecto = $rowDC[2];
                            }
                            $cuenta = $rowDC[3];
                            $naturaleza = $rowDC[4];
                            
                            if (isset($rowDC[5])) {
                                $centrocosto = $rowDC[5];
                            } else {
                                $centrocosto = "NULL";
                            }
                            if(empty($rowDC[6])){
                                $detallecomprobantepptal = 'NULL';
                            } else {
                            $detallecomprobantepptal = $rowDC[6];
                            }
                            $comprobantecnt = $rowCP[0];
                            $terceroDet = $rowDC[7];
                            $afec = $rowDC[0];

                            if ($valor != 0 || $valor != -0) {
                                $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (fecha, valor, valorejecucion, "
                                        . "comprobante, cuenta, naturaleza, tercero, proyecto, centrocosto, detalleafectado, "
                                        . "detallecomprobantepptal $descripcion)  
                                VALUES('$fecha', $valor, $valor, $ultimoComproCnt, $cuenta, $naturaleza, $terceroDet, "
                                        . "$proyecto, $centrocosto, $afec,$detallecomprobantepptal $descr)";
                                $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                            }
                        }
                    }

                    ######################################################################################
                    //Inserta las retenciones
                    for ($i = 0; $i <= $numReng; $i++) {
                        $sqlTipRet = "SELECT cuenta  
                                                            FROM gf_tipo_retencion  
                                                            WHERE id_unico = '$tipoRet[$i]'";
                        $tipRet = $mysqli->query($sqlTipRet);
                        $rowTR = mysqli_fetch_row($tipRet);
                        $tipRete = $rowTR[0];
                        $cuantasCuentas = 0;
                        $queryCuen = "SELECT cuen.id_unico, cuen.naturaleza    
                                        FROM gf_cuenta cuen 
                                        WHERE cuen.id_unico = '$tipRete'";
                        $cuent = $mysqli->query($queryCuen);
                        $cuantasCuentas = $cuent->num_rows; //Número de cuentas
                        $atribCuenta = '';
                        $atribNaturaleza = '';
                        $valCuenta = '';
                        $valNaturaleza = '';
                        $cuenta = '';
                        $naturaleza = '';
                        if ($cuantasCuentas != 0) {
                            $rowC = mysqli_fetch_row($cuent);
                            $cuenta = $rowC[0];
                            $naturaleza = $rowC[1];
                            $atribCuenta = 'cuenta, ';
                            $valCuenta = $cuenta . ', ';
                            $atribNaturaleza = 'naturaleza, ';
                            $valNaturaleza = $naturaleza . ', ';
                            $valR = $valorRet[$i];
                            //Inserción en detalle comprobante (cnt)
                            //Insert para las retenciones.
                            $sqlDetComCntRet = "INSERT INTO gf_detalle_comprobante "
                                    . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                    . "$atribCuenta $atribNaturaleza tercero,  centrocosto, proyecto)  
                                     VALUES('$rowCP[2]', '$rowCP[3]', $valR, "
                                    . "$valR, '$ultimoComproCnt', $valCuenta $valNaturaleza '$rowCP[5]', "
                                    . "$centrocostoPtal, $proyecto)";
                            //echo $sqlDetComCntRet;
                            $resultadoDetComCnt = $mysqli->query($sqlDetComCntRet);
                        } //$cuantasCuentas != 0
                        else {
                            $res = 3;
                        }
                    } //$i = 0; $i <= $numReng; $i++
                    if (!empty($_SESSION['ultimoRet'])) {
                        foreach ($ultimoRet as $value) {
                            $updateRetencion = 'UPDATE gf_retencion 
                                                                    SET comprobante = ' . $ultimoComproCnt . '  
                                                                    WHERE id_unico = ' . $value;
                            $resultadoUpRet = $mysqli->query($updateRetencion);
                        } //$ultimoRet as $value
                    } //!empty($_SESSION['ultimoRet'])
                    $_SESSION['idCompCntV'] = $ultimoComproCnt;
                    $_SESSION['ultimoRet'] = "";
                    $_SESSION['cntEgreso'] = $ultimoComproCnt;
                }
            }
        } // Hasta aquí if($num_tipoComCnt == 0){$res = 2;}else
        crear_pptal_retencion($_SESSION['idCompCntV']);
        echo $res; /// Hay que dejar 'echo $res;' 
        break;
    case 6:
        $_SESSION['terceroCuenBan'] = $_POST['tercero'];
        $_SESSION['idCompCnt'] = $_POST['idCompCnt'];
        $_SESSION['nuevo_GE'] = '';
        echo 1;
        break;
    case 7:
        $idComPtal = $_POST['idComPtal'];
        $comprobateCnt = 0;
        ######SI EL PPTAL NO TIENE DETALLES######
        $ppptal = "SELECT
                            cp.numero,
                            tc.id_unico
                          FROM
                            gf_comprobante_pptal cp
                          LEFT JOIN
                            gf_tipo_comprobante tc ON cp.tipocomprobante = tc.comprobante_pptal
                          WHERE
                            cp.id_unico ='$idComPtal'";
        $pptal = $mysqli->query($ppptal);
        if (mysqli_num_rows($pptal) > 0) {
            #BUSCAR CNT CORRESPONDIENTE AL MISMO NUMERO Y AL TIPO #cnt ya hecho
            $pptal = mysqli_fetch_row($pptal);
            $sqlComP = "SELECT id_unico, numero, fecha, descripcion, numerocontrato, tercero, 
                                clasecontrato, tipocomprobante, valorbase, valorbaseiva, valorneto, estado       
                                FROM gf_comprobante_cnt   
                                WHERE numero = $pptal[0] AND tipocomprobante=$pptal[1]";
            $compPtal = $mysqli->query($sqlComP);
            $rowCP = mysqli_fetch_row($compPtal);
            $comprobateCnt = $rowCP[0];
        } //mysqli_num_rows($pptal) > 0
        $_SESSION['cntEgreso'] = $comprobateCnt;
        echo $comprobateCnt;
        break;
    case 8:
        $user = $_SESSION['usuario'];
        $fechaElab = date('Y-m-d');

        $tipocomprobante = $_POST['tipocomprobante'];
        $id_tip_comp = $tipocomprobante;
        $numero = $_POST['numero'];
        $fecha_formulario = $_POST['fecha'];
        $tipoComp = $tipocomprobante;
        $fecha = $fecha_formulario;
        $fecha_div = explode("/", $fecha);
        $anio = $fecha_div[2];
        $mes = $fecha_div[1];
        $dia = $fecha_div[0];
        $fecha = $anio . '-' . $mes . '-' . $dia;
        $fecha_ = new DateTime($fecha);
        $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
        $sumDias = $mysqli->query($querySum);
        if (mysqli_num_rows($sumDias) > 0) {
            $rowS = mysqli_fetch_row($sumDias);
            $sumarDias = $rowS[0];
        } //mysqli_num_rows($sumDias) > 0
        else {
            $sumarDias = 30;
        }
        $fecha_->modify('+' . $sumarDias . ' day');
        $fechaVen = (string) $fecha_->format('Y-m-d');
        $estado = 3;
        $responsable = $_SESSION['usuario_tercero'];
        $parametroAnno = $_SESSION['anno'];
        $tipocomprobante = $tipoComp;
        $parametroAnno = $_SESSION['anno'];
        $compania = $_SESSION['compania'];
        if (empty($_POST['id_comp_cnt']) || empty($_POST['id_comprobante_pptal']) ||
                $_POST['id_comprobante_pptal'] == 'N') {
            $tercero = $_POST['tercero'];

            $insertSQL = "INSERT INTO gf_comprobante_pptal 
                                (numero, fecha, fechavencimiento, parametrizacionanno, 
                                tipocomprobante, 
                                tercero, estado, responsable, usuario, fecha_elaboracion ) 
				VALUES($numero, '$fecha', '$fechaVen', $parametroAnno,"
                    . " $tipocomprobante, " . "$tercero, $estado, $responsable, '$user', '$fechaElab'
                        )";
            $resultado = $mysqli->query($insertSQL);
            if ($resultado == true) {
                $queryUltComp = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE numero = $numero AND tipocomprobante = $tipocomprobante";
                $ultimComp = $mysqli->query($queryUltComp);
                $rowUC = mysqli_fetch_row($ultimComp);
                $idNuevoComprobante = $rowUC[0];
                $sqlTipoCompCnt = "SELECT id_unico FROM gf_tipo_comprobante " . "WHERE comprobante_pptal = $tipocomprobante";
                $tipoCompCnt = $mysqli->query($sqlTipoCompCnt);
                $rowTCC = mysqli_fetch_row($tipoCompCnt);
                $tipocomprobante = $rowTCC[0];
                $sqlComprobanteCnt = "INSERT INTO gf_comprobante_cnt " . "(numero, fecha, tipocomprobante, "
                        . "compania, parametrizacionanno, " . "tercero,usuario, fecha_elaboracion)  
                                        VALUES($numero, '$fecha', $tipocomprobante, $compania, "
                        . "$parametroAnno, " . "$tercero,'$user', '$fechaElab')";
                $resultadoCnt = $mysqli->query($sqlComprobanteCnt);
                if ($resultadoCnt == true) {
                    $sqlUltComC = "SELECT MAX(id_unico) FROM gf_comprobante_cnt where numero = $numero AND tipocomprobante = $tipocomprobante";
                    $ultComC = $mysqli->query($sqlUltComC);
                    $rowUC = mysqli_fetch_row($ultComC);
                    $ultimoComproCnt = $rowUC[0];
                    $_SESSION['cntEgreso'] = $ultimoComproCnt;
                    $res = 1;
                } //$resultadoCnt == true
                else {
                    $res = 0;
                }
            } //$resultado == true
            else {
                $res = 0;
            }
        } //empty($_POST['id_comp_cnt']) || empty($_POST['id_comprobante_pptal']) || $_POST['id_comprobante_pptal'] == 'N'
        else {
            $id_comp_cnt = $_POST['id_comp_cnt'];
            $id_comprobante_pptal = $_POST['id_comprobante_pptal'];
            //Captura de datos e instrucción SQL para su posterior inserción en la tabla gf_comprobante_pptal como egreso.
            $idAnteriorComprobante = $id_comprobante_pptal;
            $queryCompro = "SELECT comp.id_unico, comp.numero, comp.fecha, 
                            comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, 
                            tipCom.codigo, tipCom.nombre, comp.tercero, comp.numerocontrato, 
                            comp.clasecontrato  
      			FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      			WHERE comp.tipocomprobante = tipCom.id_unico 
      			AND comp.id_unico =  $idAnteriorComprobante";
            $comprobante = $mysqli->query($queryCompro);
            $rowComp = mysqli_fetch_row($comprobante);
            $id = $rowComp[0];
            $descripcion = '"' . $mysqli->real_escape_string('' . $rowComp[3] . '') . '"';
            $tercero = $rowComp[8];
            if (empty($rowComp[9]) || $rowComp[9] == NULL || $rowComp[9] == "") {
                $numContrato = 'NULL';
            } //empty($rowComp[9]) || $rowComp[9] == NULL || $rowComp[9] == ""
            else {
                $numContrato = $rowComp[9];
            }
            if (empty($rowComp[10]) || $rowComp[10] == NULL || $rowComp[10] == "") {
                $claseContrato = 'NULL';
            } //empty($rowComp[10]) || $rowComp[10] == NULL || $rowComp[10] == ""
            else {
                $claseContrato = $rowComp[10];
            }
            if ($descripcion == '""') { // Desde acá inserción del egreso en la tabla gf_comprobante_pptal.
                $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, 
                    parametrizacionanno, tipocomprobante, tercero, estado, responsable, numerocontrato, 
                    clasecontrato, usuario, fecha_elaboracion) 
		VALUES($numero, '$fecha', '$fechaVen', $parametroAnno, $tipocomprobante, "
                        . "$tercero, $estado, $responsable, '$numContrato', $claseContrato,"
                        . "'$user', '$fechaElab')";
            } //$descripcion == '""'
            else {
                $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, 
                    fechavencimiento, descripcion, parametrizacionanno, 
                    tipocomprobante, tercero, estado, responsable, 
                    numerocontrato, clasecontrato,usuario, fecha_elaboracion) 
	            VALUES($numero, '$fecha', '$fechaVen', $descripcion, $parametroAnno, "
                        . "$tipocomprobante, $tercero, $estado, $responsable, "
                        . "'$numContrato', $claseContrato, '$user', '$fechaElab')";
            }
            //echo $insertSQL;
            $resultado = $mysqli->query($insertSQL);
            if ($resultado == true) {
                $queryUltComp = "SELECT MAX(id_unico) FROM gf_comprobante_pptal "
                        . "WHERE tipocomprobante = '$tipocomprobante' AND numero = $numero";
                $ultimComp = $mysqli->query($queryUltComp);
                $rowUC = mysqli_fetch_row($ultimComp);
                $idNuevoComprobante = $rowUC[0];
                //Selección de datos para posterior ingreso del detalle presupuestal del egreso anteriormente insertado.
                $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, 
                                    detComP.rubrofuente, detComP.tercero, detComP.proyecto, 
                                    detComP.id_unico, 
                                    detComP.conceptoRubro, detComP.centro_costo  
				   	FROM gf_detalle_comprobante_pptal detComP
				   	left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
				   	left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
				   	left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptoRubro 
				   	left join gf_concepto con on con.id_unico = conRub.concepto 
				   	where detComP.comprobantepptal = $idAnteriorComprobante";
                $resultado = $mysqli->query($queryAntiguoDetallPttal);
                $comprobantepptal = $idNuevoComprobante;
                while ($row = mysqli_fetch_row($resultado)) {
                    $terdetalle = $row[3];
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
                        $rubro = $row[2];
                        if(empty($row[4])){
                            $proyecto = 2147483647;
                        } else {
                            $proyecto = $row[4];
                        }
                        $idAfectado = $row[5];
                        $conceptorubro = $row[6];
                        $campo = "";
                        $variable = "";
                        if (($descripcion != '""') || ($descripcion != NULL)) {
                            $campo = "descripcion,";
                            $variable = "$descripcion,";
                        } //($descripcion != '""') || ($descripcion != NULL)
                        //Inserción de datos en detalle presupuestal para el egreso.
                        if (empty($row[7])) {
                            #** Buscar Centro Costo Varios **#
                            $cv = $con->Listar("SELECT * FROM gf_centro_costo 
                            WHERE parametrizacionanno = $anno AND nombre ='Varios'");
                            if (count($cv) > 0) {
                                $cc = $cv[0][0];
                            } else {
                                $cc = 'NULL';
                            }
                        } else {
                            $cc = $row[7];
                        }
                        $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal 
                        ($campo valor, comprobantepptal, rubrofuente, " . "tercero, 
                        proyecto, comprobanteafectado, conceptoRubro, centro_costo )" . " VALUES 
                        ($variable $valor, $comprobantepptal, $rubro, $terdetalle, 
                        $proyecto, $idAfectado, $conceptorubro, $cc)";
                        $resultadoInsert = $mysqli->query($insertSQL);
                    } //$valorPpTl > 0
                } //$row = mysqli_fetch_row($resultado)
                $updateSQL = "UPDATE gf_comprobante_pptal  
					SET estado = $estado     
				    WHERE id_unico = $idAnteriorComprobante";
                $resultadoUpdate = $mysqli->query($updateSQL);
                $_SESSION['id_comp_pptal_GE'] = $idNuevoComprobante;
                $_SESSION['nuevo_GE'] = 1;
            } //$resultado == true
            //Hasta acá inserción del egreso en la tabla gf_comprobante_pptal.
            // Desde acá selección e ingreso de datos para el comprobante cnt. <-------------------------------------------------------------------------
            $res = 0;
            $numerocontrato = '';
            $numCont = '';
            $clasecontrato = '';
            $claseCon = '';
            $descr = '';
            $sqlTipoCompCnt = "SELECT id_unico FROM gf_tipo_comprobante WHERE comprobante_pptal = $tipocomprobante";
            $tipoCompCnt = $mysqli->query($sqlTipoCompCnt);
            $rowTCC = mysqli_fetch_row($tipoCompCnt);
            $tipocomprobante = $rowTCC[0];
            $sqlComP = "SELECT id_unico, numero, fecha, descripcion, numerocontrato, tercero, clasecontrato, tipocomprobante, valorbase, valorbaseiva, valorneto, estado       
  				FROM gf_comprobante_cnt   
  				WHERE id_unico = $id_comp_cnt";
            $compPtal = $mysqli->query($sqlComP);
            $rowCP = mysqli_fetch_row($compPtal);
            $idCompCnt = $rowCP[0];
            if (empty($rowCP[8])) {
                $valorbase = 'NULL';
            } //empty($rowCP[8])
            else {
                $valorbase = $rowCP[8];
            }
            if (empty($rowCP[9])) {
                $valorbaseiva = 'NULL';
            } //empty($rowCP[9])
            else {
                $valorbaseiva = $rowCP[9];
            }
            if (empty($rowCP[10])) {
                $valorneto = 'NULL';
            } //empty($rowCP[10])
            else {
                $valorneto = $rowCP[10];
            }
            if (empty($rowCP[11])) {
                $estado = 'NULL';
            } //empty($rowCP[11])
            else {
                $estado = $rowCP[11];
            }
            if (empty($rowCP[4])) {
                $numCont = "'" . $rowCP[4] . "'";
            } else {
                $numCont = 'NULL';
            }
            if (!empty($rowCP[6])) {
                $claseCon = "'" . $rowCP[6] . "'";
            } else {
                $claseCon = 'NULL';
            }
            if (empty($descripcion)) {
                $descripcion = 'NULL';
            } else {
                
            }
            // Ingreso de datos en la tabla gf_comprobante_cnt para el egreso.
            $sqlComprobanteCnt = "INSERT INTO gf_comprobante_cnt (descripcion, usuario, fecha_elaboracion, numero, fecha, valorbase, "
                    . "valorbaseiva, valorneto, tipocomprobante, compania, parametrizacionanno, "
                    . "tercero, estado , numerocontrato, clasecontrato)  
				VALUES($descripcion, '$user', '$fechaElab',$numero, "
                    . "'$fecha', $valorbase, $valorbaseiva, "
                    . "$valorneto, $tipocomprobante, $compania, "
                    . "$parametroAnno, $tercero, $estado, "
                    . "$numCont, $claseCon )";
            $resultadoCnt = $mysqli->query($sqlComprobanteCnt);
            if ($resultadoCnt == true) {
                $sqlUltComC = "SELECT MAX(id_unico) FROM gf_comprobante_cnt "
                        . "WHERE tipocomprobante = $tipocomprobante AND numero = '$numero'";
                $ultComC = $mysqli->query($sqlUltComC);
                $rowUC = mysqli_fetch_row($ultComC);
                $ultimoComproCnt = $rowUC[0];

                ##############VERIFICAR SI LAS CUENTAS QUE TRAE SON DE NÓMINA O NO #######################
                #CONTAR LOS DETALLES
                $numc = "SELECT DISTINCT dc.id_unico FROM gf_detalle_comprobante  dc "
                        . "WHERE dc.comprobante = $id_comp_cnt ";
                $numc = $mysqli->query($numc);
                $numc = mysqli_num_rows($numc);
                #CONTAR LOS DETALLES CON CUENTAS PASIVO NOMINA 
                $nump = "SELECT DISTINCT dc.id_unico FROM gf_detalle_comprobante dc "
                        . "LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico "
                        . "WHERE dc.comprobante = $id_comp_cnt "
                        . "AND c.clasecuenta = 20";
                $nump = $mysqli->query($nump);
                $nump = mysqli_num_rows($nump);
                #var_dump($nump>0);
                #########SI HAY CUENTAS DE PASIVO NOMINA
                if ($nump > 0) {
                    #####SI TODAS LAS CUENTAS SON DE PASIVO NOMINA 
                    #CONTAR LOS DETALLES CON CUENTAS PASIVO NOMINA Y CLASE !6 Y !8
                    $numcom = "SELECT DISTINCT dc.id_unico FROM gf_detalle_comprobante dc "
                            . "LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico "
                            . "WHERE dc.comprobante = $id_comp_cnt "
                            . "AND (c.clasecuenta !=6 AND c.clasecuenta !=8)";
                    $numcom = $mysqli->query($numcom);
                    $numcom = mysqli_num_rows($numcom);
                    //var_dump($numc==$numcom);
                    if ($numc == $numcom) {
                        #SE TRAEN TODAS LAS CUENTAS DE PASIVO
                        $sqlDetCom = "SELECT detCom.id_unico, detCom.valor, detCom.proyecto, 
                               detCom.cuenta, detCom.naturaleza, detCom.centrocosto, detCom.detallecomprobantepptal, 
                               detCom.tercero 
                                FROM gf_detalle_comprobante detCom 
                                LEFT JOIN gf_comprobante_cnt com ON com.id_unico = detCom.comprobante 
                                LEFT JOIN gf_cuenta CT ON detCom.cuenta = CT.id_unico 
                                LEFT JOIN gf_clase_cuenta clacu ON clacu.id_unico = CT.clasecuenta 
                                WHERE com.id_unico = $id_comp_cnt and clacu.id_unico = 20 ";
                        $detComp = $mysqli->query($sqlDetCom);
                        while ($rowDC = mysqli_fetch_row($detComp)) {
                            $terdeta = $rowDC[7];
                            $valorp = $rowDC[1];
                            #*** Buscar Si Tiene Afectado ***#
                            $af = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) FROM gf_detalle_comprobante dc WHERE dc.detalleafectado = $rowDC[0] ");
                            if (empty($af[0][0])) {
                                $afect = 0;
                            } else {
                                $afect = $af[0][0];
                            }
                            if ($valorp < 0) {
                                $valorp = $valorp * -1 - $afect;
                                $valorp = $valorp * -1;
                            } else {
                                $valorp = $valorp - $afect;
                            }
                            $valor = $valorp * -1;
                            if(empty($rowDC[2])){
                                $proyecto = 2147483647;
                            } else {
                                $proyecto = $rowDC[2];
                            }
                            $cuenta = $rowDC[3];
                            $naturaleza = $rowDC[4];
                            $centrocosto = $rowDC[5];
                            $detallecomprobantepptal = $rowDC[6];

                            if (empty($rowDC[6])) {
                                $detallecomprobantepptal = 'NULL';
                            }
                            ##DETALLE CNT AFECTADO##
                            $afec = $rowDC[0];
                            if (empty($rowComp[3])) {
                                $descripcion = 'NULL';
                            }
                            $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (descripcion, fecha, valor, valorejecucion, comprobante, 
                                    cuenta, naturaleza, tercero, proyecto, centrocosto,  detalleafectado, detallecomprobantepptal )  
                                                        VALUES($descripcion, '$fecha', $valor, $valor, $ultimoComproCnt, $cuenta, $naturaleza, " . "$terdeta, $proyecto, $centrocosto,$afec, $detallecomprobantepptal )";
                            $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                        }
                        #############SI TODAS LA CUENTAS NO SON DE PASIVO TEMPORAL##################
                    } else {
                        #SE TRAEN TODAS LAS CUENTAS MENOS PASIVO
                        $sqlDetCom = "SELECT detCom.id_unico, detCom.valor, detCom.proyecto, 
                               detCom.cuenta, detCom.naturaleza, detCom.centrocosto, detCom.detallecomprobantepptal, 
                               detCom.tercero 
                                FROM gf_detalle_comprobante detCom 
                                LEFT JOIN gf_comprobante_cnt com ON com.id_unico = detCom.comprobante 
                                LEFT JOIN gf_cuenta CT ON detCom.cuenta = CT.id_unico 
                                LEFT JOIN gf_clase_cuenta clacu ON clacu.id_unico = CT.clasecuenta 
                                WHERE ( com.id_unico = $id_comp_cnt and clacu.id_unico = 4) 
                                OR ( com.id_unico = $id_comp_cnt and clacu.id_unico = 8) 
                                AND clacu.id_unico !=20 ";
                        $detComp = $mysqli->query($sqlDetCom);
                        while ($rowDC = mysqli_fetch_row($detComp)) {
                            $terdeta = $rowDC[7];
                            $valorp = $rowDC[1];
                            #*** Buscar Si Tiene Afectado ***#
                            $af = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) FROM gf_detalle_comprobante dc WHERE dc.detalleafectado = $rowDC[0] ");
                            if (empty($af[0][0])) {
                                $afect = 0;
                            } else {
                                $afect = $af[0][0];
                            }
                            if ($valorp < 0) {
                                $valorp = $valorp * -1 - $afect;
                                $valorp = $valorp * -1;
                            } else {
                                $valorp = $valorp - $afect;
                            }
                            $valor = $valorp * -1;
                            if(empty($rowDC[2])){
                                $proyecto = 2147483647;
                            } else {
                                $proyecto = $rowDC[2];
                            }
                            $cuenta = $rowDC[3];
                            $naturaleza = $rowDC[4];
                            if (isset($rowDC[5])) {
                                $centrocosto = $rowDC[5];
                            } else {
                                $centrocosto = "NULL";
                            }
                            $detallecomprobantepptal = $rowDC[6];

                            if (empty($rowDC[6])) {
                                $detallecomprobantepptal = 'NULL';
                            }
                            ##DETALLE CNT AFECTADO##
                            $afec = $rowDC[0];
                            if (empty($rowComp[3])) {
                                $descripcion = 'NULL';
                            }
                            if ($valor != 0 || $valor != -0) {
                                $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (descripcion, fecha, valor, valorejecucion, comprobante, 
                                    cuenta, naturaleza, tercero, proyecto, centrocosto,  detalleafectado, detallecomprobantepptal )  
                                                        VALUES($descripcion, '$fecha', $valor, $valor, $ultimoComproCnt, $cuenta, $naturaleza, " . "$terdeta, $proyecto, $centrocosto,$afec, $detallecomprobantepptal )";
                                $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                            }
                        }
                    }
                } else {
                    ########SI NO HAY CUENTAS DE PASIVO NOMINA 
                    // Selección de los datos  del comprobante cnt de cuenta por pagar para posterior inserción en gf_comprobante_cnt como egreso.
                    $sqlDetCom = "SELECT detCom.id_unico, detCom.valor, detCom.proyecto, 
                       detCom.cuenta, detCom.naturaleza, detCom.centrocosto, detCom.detallecomprobantepptal, 
                       detCom.tercero 
	            	FROM gf_detalle_comprobante detCom 
	            	LEFT JOIN gf_comprobante_cnt com ON com.id_unico = detCom.comprobante 
	            	LEFT JOIN gf_cuenta CT ON detCom.cuenta = CT.id_unico 
	            	LEFT JOIN gf_clase_cuenta clacu ON clacu.id_unico = CT.clasecuenta 
	            	WHERE ( com.id_unico = $id_comp_cnt and clacu.id_unico = 4) 
	            	OR ( com.id_unico = $id_comp_cnt and clacu.id_unico = 8)";
                    $detComp = $mysqli->query($sqlDetCom);
                    while ($rowDC = mysqli_fetch_row($detComp)) {
                        $terdeta = $rowDC[7];
                        $valorp = $rowDC[1];
                        #*** Buscar Si Tiene Afectado ***#
                        $af = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) FROM gf_detalle_comprobante dc WHERE dc.detalleafectado = $rowDC[0] ");
                        if (empty($af[0][0])) {
                            $afect = 0;
                        } else {
                            $afect = $af[0][0];
                        }
                        if ($valorp < 0) {
                            $valorp = $valorp * -1 - $afect;
                            $valorp = $valorp * -1;
                        } else {
                            $valorp = $valorp - $afect;
                        }
                        $valor = $valorp * -1;
                        if(empty($rowDC[2])){
                            $proyecto = 2147483647;
                        } else {
                            $proyecto = $rowDC[2];
                        }
                        $cuenta = $rowDC[3];
                        $naturaleza = $rowDC[4];
                        if (empty($rowDC[5])) {
                            $centrocosto = 'NULL';
                        } else {
                            $centrocosto = $rowDC[5];
                        }
                        $detallecomprobantepptal = $rowDC[6];
                        //var_dump(empty($rowDC[6]));
                        if (empty($rowDC[6])) {
                            $detallecomprobantepptal = 'NULL';
                        } //empty($rowDC[6])
                        ##DETALLE CNT AFECTADO##
                        $afec = $rowDC[0];
                        if (empty($rowComp[3])) {
                            $descripcion = 'NULL';
                        } //empty($descripcion)
                        if ($valor != 0 || $valor != -0) {
                            $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (descripcion, fecha, valor, valorejecucion, comprobante, 
                            cuenta, naturaleza, tercero, proyecto, centrocosto,  detalleafectado, detallecomprobantepptal )  
						VALUES($descripcion, '$fecha', $valor, $valor, $ultimoComproCnt, $cuenta, $naturaleza, " . "$terdeta, $proyecto, $centrocosto,$afec, $detallecomprobantepptal )";
                            $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                        }
                    } //$rowDC = mysqli_fetch_row($detComp)
                }
            } //$resultadoCnt == true
        }
        $_SESSION['idCompCnt'] = $ultimoComproCnt;
        $res = $ultimoComproCnt;
        //AGREGADO ERICA
        $_SESSION['idCompCnt'] = $ultimoComproCnt;
        $_SESSION['cntEgreso'] = $ultimoComproCnt;
        $_SESSION['id_comp_pptal_GE'] = $idNuevoComprobante;
        $_SESSION['nuevo_GE'] = 1;
        $_SESSION['terceroGuardado'] = $tercero;
        $_SESSION['comprobanteGenerado'] = $idNuevoComprobante;
        echo $res;
        break;
    case 9: //Ingresar el banco en Comprobante Egreso
        //$res = 0;
        $idComprobante = $_POST['idComprobante'];
        $diferencia = $_POST['diferencia'];
        $diferencia = $diferencia * -1;
        $cuentaBancaria = $_POST['cuentaBancaria'];
        $descripcion = '';
        $descr = '';
        $formapago = $_POST['formapago'];
        $queryCuenBan = "SELECT cuenta 
                FROM gf_cuenta_bancaria 
                WHERE id_unico = $cuentaBancaria";
        $cuentaBan = $mysqli->query($queryCuenBan);
        $rowCB = mysqli_fetch_row($cuentaBan);
        $cuenta = $rowCB[0];
        $queryCuen = "SELECT  naturaleza 
                FROM gf_cuenta 
                WHERE id_unico = '$cuenta'";
        $cuent = $mysqli->query($queryCuen);
        $rowC = mysqli_fetch_row($cuent);
        $naturaleza = $rowC[0];
        $sqlComCnt = "SELECT descripcion, tercero, fecha         
  				FROM gf_comprobante_cnt   
  				WHERE id_unico = $idComprobante";
        $comCnt = $mysqli->query($sqlComCnt);
        $rowCC = mysqli_fetch_row($comCnt);
        if (!empty($rowCC[0])) {

            $descr = "'" . $rowCC[0] . "'";
        } //!empty($rowCC[0])
        else {
            $descr = 'NULL';
        }
        $fecha = $rowCC[2];
        $tercero = $rowCC[1];
        $anno = $_SESSION['anno'];
        $centrocosto = "SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno";
        $centrocosto = $mysqli->query($centrocosto);
        $centrocosto = mysqli_fetch_row($centrocosto);
        $centrocosto = $centrocosto[0];

        $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (fecha, valor, 
            valorejecucion, comprobante, cuenta, naturaleza, tercero, 
            proyecto, centrocosto, descripcion)  
	VALUES('$fecha', $diferencia, $diferencia, $idComprobante, '$cuenta', "
                . "'$naturaleza', '$tercero', 2147483647, $centrocosto,$descr)";

        $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
        if ($resultadoDetComCnt == true) {
            ###FORMA PAGO###
            $udpfp = "UPDATE gf_comprobante_cnt SET formapago = $formapago WHERE id_unico = $idComprobante";
            $udpfp = $mysqli->query($udpfp);
            $res = 1;
        } //$resultadoDetComCnt == true
        else {
            $res = 0;
        }
        echo $res;
        break;
    case 10:
        $id_tip_comp = $_REQUEST['id_comp'];
        $fecha = $_REQUEST['fecha'];
        $res = 0;
        $queryFechComp = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = $id_tip_comp";
        $fechComp = $mysqli->query($queryFechComp);
        $row = mysqli_fetch_row($fechComp);
        $fechaPrev = $row[0];
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio . "-" . $mes . "-" . $dia;
        $fecha_prev = new DateTime($fechaPrev);
        $fecha_ = new DateTime($fecha);
        if ($fecha_prev <= $fecha_) {
            $res = 1;
        } //$fecha_prev <= $fecha_
        echo $res;
        break;
    case 11: //Recibe el número del comprobante presupuestal cuenta por pagar y retorna un valor diferente a cero si tiene comprobante cnt afectado.
        // Si la variable valorTot es diferente de vacío asigna valores a las variables de sesión idCompPtal y valorTotCP.
        //Se eleminó group by.
        $idCompPtal = $_POST['idCompPtal'];
        $id_comp = $idCompPtal;
        $cantCnt = 0;
        $sqlCompCnt = 'SELECT COUNT(detComCnt.comprobante) 
				FROM gf_detalle_comprobante_pptal detComPtal
				LEFT JOIN gf_detalle_comprobante detComCnt ON detComCnt.detallecomprobantepptal = detComPtal.id_unico
				WHERE detComPtal.comprobantepptal = ' . $id_comp;
        $coomprobanteCnt = $mysqli->query($sqlCompCnt);
        $rowCCnt = mysqli_fetch_row($coomprobanteCnt);
        $cantCnt = (int) $rowCCnt[0];
        if ($cantCnt == 0 && !empty($_POST['valorTot'])) {
            $valorTot = $_POST['valorTot'];
            $_SESSION['idCompPtalCP'] = $idCompPtal;
            $_SESSION['valorTotCP'] = $valorTot;
        } //$cantCnt == 0 && !empty($_POST['valorTot'])
        if ($cantCnt == 0) {
            //BUSCAR Numero y tipo pptal
            $p = "SELECT numero, tipocomprobante FROM gf_comprobante_pptal where id_unico = $id_comp";
            $p1 = $mysqli->query($p);
            $p2 = mysqli_fetch_row($p1);
            $tipo = $p2[1];
            $numero = $p2[0];
            //buscar tipo cnt 
            $t = "SELECT id_unico FROM gf_tipo_comprobante where comprobante_pptal = $tipo";
            $t1 = $mysqli->query($t);
            $t2 = mysqli_fetch_row($t1);
            $tc = $t2[0];
            //BUSCAR NUMERO Y TIPO 
            $cn = "SELECT id_unico FROM gf_comprobante_cnt where numero = $numero AND tipocomprobante = $tc";
            $cn1 = $mysqli->query($cn);
            $cn2 = mysqli_fetch_row($cn1);
            $ccn = $cn2[0];
            $cantCnt = (int) $ccn;
        } //$cantCnt == 0
        echo $cantCnt;
        break;
    case 12; //Modificar vector de débito como sesión.
        $id = $_POST['id'];
        $valor = $_POST['valor'];
        //                        echo '<br/>';
        $debitoCP = $_SESSION['debitoCP'];
        $debitoCP = stripslashes($debitoCP);
        $debitoCP = unserialize($debitoCP);
        $debitoCP[$id] = (int) $valor;
        $debitoCP = serialize($debitoCP); //serialize($miarray);
        $_SESSION['debitoCP'] = $debitoCP;
        echo $_SESSION['debitoCP'];
        break;
    case 13: //MODIFCAR CUENTA POR PAGAR
        $tercero = $_POST['tercero'];
        $descripcion = $_POST['descripcion'];
        $idComCnt = $_SESSION['idCompCntV'];
        $idpptal = $_SESSION['id_comp_pptal_GE'];
        ($_POST['claseContrato'] == "");
        if (empty($_POST['claseContrato']) || $_POST['claseContrato'] == "") {
            $claseContrato = 'NULL';
        } //empty($_POST['claseContrato']) || $_POST['claseContrato'] == ""
        else {
            $claseContrato = $_POST['claseContrato'];
        }
        if (empty($_POST['numeroContrato']) || $_POST['numeroContrato'] == "") {
            $numeroContrato = 'NULL';
        } //empty($_POST['numeroContrato']) || $_POST['numeroContrato'] == ""
        else {
            $numeroContrato = "'" . $_POST['numeroContrato'] . "'";
        }
        $fecha = $_POST['fecha'];
        $fecha_div = explode("/", $fecha);
        $anio = $fecha_div[2];
        $mes = $fecha_div[1];
        $dia = $fecha_div[0];
        $fecha = $anio . '-' . $mes . '-' . $dia;
        $res = 0;
        $sqlUpdateComCnt = "UPDATE gf_comprobante_cnt 
				SET  descripcion = '$descripcion', " . "clasecontrato=$claseContrato, numerocontrato = $numeroContrato, fecha ='$fecha', tercero='$tercero'  " . "WHERE id_unico = $idComCnt";
        $resultadoComCnt = $mysqli->query($sqlUpdateComCnt);
        if ($resultadoComCnt == true) {
            $udp = "UPDATE gf_detalle_comprobante  
				SET  descripcion = '$descripcion', " . " fecha ='$fecha' WHERE comprobante='$idComCnt'";
            $udp = $mysqli->query($udp);
            $sqlUpdatepptal = "UPDATE gf_comprobante_pptal 
				SET  descripcion = '$descripcion', 
                                clasecontrato=$claseContrato, " . "numerocontrato = $numeroContrato, fecha ='$fecha' , tercero = '$tercero'  
				WHERE id_unico = $idpptal";
            $sqlUpdatepptal = $mysqli->query($sqlUpdatepptal);
            $udpp = "UPDATE gf_detalle_comprobante_pptal 
				SET  descripcion = '$descripcion'  WHERE comprobantepptal='$idpptal'";
            $udpp = $mysqli->query($udpp);
            if ($sqlUpdatepptal == true) {
                $res = 1;
            } //$sqlUpdatepptal == true
            else {
                $res = 1;
            }
        } //$resultadoComCnt == true
        echo $res;
        break;
    case 14:
        $id_comp_Ptal = $_POST['id_comp_Ptal'];
        $valorTotal = $_POST['valorTotal'];
        $consecutivo = $_POST['consecutivo'];
        $renglon = '';
        $renglon = '<tr id="renglon' . $consecutivo . '">
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
        $claseRet = "SELECT nombre, id_unico
                FROM gf_clase_retencion 
                ORDER BY nombre ASC";
        $rscR = $mysqli->query($claseRet);
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
                     <!-- Saldo por pagar -->
                    <td class="campos" align="center" >
                       <!-- % IVA -->';
        $porIVA = "SELECT valor 
                                FROM gs_parametros_basicos 
                                WHERE nombre ='porcentaje iva'";
        $rsPI = $mysqli->query($porIVA);
        $filaPI = mysqli_fetch_row($rsPI);
        $renglon .= '<label></label>
                    <input type="number" step="1" min="0" max="100" value="' . $filaPI[0] . '" name="porIVA' . $consecutivo . '" id="porIVA' . $consecutivo . '" onkeyup="javascript: porcIVA(' . $consecutivo . ');" class="form-control input-sm" maxlength="100" title="" onkeypress="return txtValida(event,\'dec\', \'porIVA\', \'2\')" onclick="" placeholder="% IVA" style="width: 94%;" >
                    <input type="hidden" id="paramIVA' . $consecutivo . '" value="' . $filaPI[0] . '">
                    </td> <!-- Saldo por pagar -->
                    <td class="campos" align="center" style="padding: 0px">
                       <!-- Aplicar Sobre -->';
        $aplicarS = "SELECT nombre, id_unico
            	FROM gf_tipo_base
                WHERE nombre != ''
                ORDER BY nombre ASC";
        $rsaS = $mysqli->query($aplicarS);
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
                     <!-- Saldo por pagar -->
                     <td class="campos" align="center" >
                        <!-- Retención a Aplicar -->
                        <span id=""></span>
                        <input type="hidden" id="">
                        <input type="hidden" id="cuenCreOc' . $consecutivo . '">
                        <select name="cuenCre' . $consecutivo . '" id="cuenCre' . $consecutivo . '" class="form-control input-sm" title="Seleccione una cuenta" style="width: 94%;" >';
        $sqlDetCompP = "SELECT detComP.id_unico, detComP.rubrofuente   
                      FROM gf_detalle_comprobante_pptal detComP 
                      LEFT JOIN gf_comprobante_pptal comP ON comP.id_unico = detComP.comprobantepptal     
                      WHERE comP.id_unico = '$id_comp_Ptal'";
        $comprobPtal = $mysqli->query($sqlDetCompP);
        while ($rowCP = mysqli_fetch_row($comprobPtal)) {
            $queryCuenCre = "SELECT cuen.id_unico, cuen.codi_cuenta, cuen.nombre 
                      		FROM gf_cuenta cuen 
                      		LEFT JOIN gf_concepto_rubro_cuenta conRubCun ON conRubCun.cuenta_credito = cuen.id_unico 
                      		LEFT JOIN gf_concepto_rubro conRub ON conRub.id_unico = conRubCun.concepto_rubro 
                      		LEFT JOIN gf_rubro_pptal rub ON rub.id_unico = conRub.rubro 
                      		LEFT JOIN gf_rubro_fuente rubFue ON rubFue.rubro = rub.id_unico 
                      		WHERE rubFue.id_unico = $rowCP[1]";
            $cuentaCre = $mysqli->query($queryCuenCre);
            while ($rowCC = mysqli_fetch_row($cuentaCre)) {
                $renglon .= '<option value="' . $rowCC[0] . '">' . $rowCC[1] . ' ' . $rowCC[2] . '</option>';
            } //$rowCC = mysqli_fetch_row($cuentaCre)
        } //$rowCP = mysqli_fetch_row($comprobPtal)
        $renglon .= '</select>
                        </td> <!-- Saldo por pagar -->
                     </tr>'; //Fin
        echo $renglon;
        break;
    case 15:
        $reps = 0;
        $id_comp = $_POST['id_comp']; //   Id comprobante Pptal ROP.
        /**/
        $sqlCompCnt = 'SELECT detComCnt.comprobante  
				FROM gf_detalle_comprobante detComCnt
    			LEFT JOIN gf_detalle_comprobante_pptal detComP ON detComP.id_unico = detComCnt.detallecomprobantepptal
    			LEFT JOIN gf_comprobante_pptal comP ON comP.id_unico = detComP.comprobantepptal
				WHERE comP.id_unico = ' . $id_comp . '
				GROUP BY detComCnt.comprobante';
        $compCnt = $mysqli->query($sqlCompCnt);
        while ($rowCCnt = mysqli_fetch_row($compCnt)) {
            //Elimina retención.
            $sqlDeleteRet = 'DELETE FROM gf_retencion
            			WHERE comprobante = ' . $rowCCnt[0];
            $resultadoRet = $mysqli->query($sqlDeleteRet);
            if ($resultadoRet == TRUE) {
                $reps = 4; //comprobante cnt
            } //$resultadoRet == TRUE
            //Convierte en nulos los afectados para detalle comprobante cnt
            $sqlUpdateDetComCnt = 'UPDATE gf_detalle_comprobante 
            		SET detalleafectado = NULL 
            		WHERE comprobante = ' . $rowCCnt[0];
            $resultadoUp = $mysqli->query($sqlUpdateDetComCnt);
            if ($resultadoUp == TRUE) {
                $reps = 3; //comprobante cnt
            } //$resultadoUp == TRUE
            //Elimina detalle comprobante
            $sqlDeleteDetalleCnt = 'DELETE FROM gf_detalle_comprobante
            		WHERE comprobante = ' . $rowCCnt[0];
            $resultadoDetalleCnt = $mysqli->query($sqlDeleteDetalleCnt);
            if ($resultadoDetalleCnt == TRUE) {
                $reps = 2; //comprobante cnt
            } //$resultadoDetalleCnt == TRUE
            $sqlDeleteCompCnt = 'DELETE FROM gf_comprobante_cnt
            			WHERE id_unico = ' . $rowCCnt[0];
            $resultadoCompCnt = $mysqli->query($sqlDeleteCompCnt);
            if ($resultadoCompCnt == TRUE) {
                $reps = 1; //comprobante cnt
            } //$resultadoCompCnt == TRUE
        } //$rowCCnt = mysqli_fetch_row($compCnt)
        if ($reps == 1) {
            unset($_SESSION['idCompCnt']);
            unset($_SESSION['id_comp_pptal_GE']);
            unset($_SESSION['nuevo_GE']);
            unset($_SESSION['idCompCntV']);
            unset($_SESSION['compRet_Sesion']);
            unset($_SESSION['valorTotCP']);
            unset($_SESSION['idCompPtalCP']);
            unset($_SESSION['debitoCP']);
        } //$reps == 1
        echo $reps;
        break;
    case 16:
        $id_comp = $_POST['id_comp'];
        $id_com_cnt = 0;
        $sqlCompCnt = 'SELECT detComCnt.comprobante
				FROM gf_detalle_comprobante_pptal detComPtal
				LEFT JOIN gf_detalle_comprobante detComCnt ON detComCnt.detallecomprobantepptal = detComPtal.id_unico
                LEFT JOIN gf_comprobante_cnt comCnt ON comCnt.id_unico = detComCnt.comprobante
                LEFT JOIN gf_tipo_comprobante tipCom ON tipCom.id_unico = comCnt.tipocomprobante
				WHERE detComPtal.comprobantepptal = ' . $id_comp . ' 
                AND tipCom.clasecontable = 13
				GROUP BY detComCnt.comprobante';
        $coomprobanteCnt = $mysqli->query($sqlCompCnt);
        $rowCCnt = mysqli_fetch_row($coomprobanteCnt);
        $id_com_cnt = (int) $rowCCnt[0];
        if ($id_com_cnt != 0) {
            $_SESSION['idCompCntV'] = $id_com_cnt;
        } //$id_com_cnt != 0
        else {
            //BUSCAR Numero y tipo pptal
            $p = "SELECT numero, tipocomprobante FROM gf_comprobante_pptal where id_unico = $id_comp";
            $p = $mysqli->query($p);
            $p = mysqli_fetch_row($p);
            $tipo = $p[1];
            $numero = $p[0];
            //buscar tipo cnt 
            $t = "SELECT id_unico FROM gf_tipo_comprobante where comprobante_pptal = $tipo";
            $t = $mysqli->query($t);
            $t = mysqli_fetch_row($t);
            $tc = $t[0];
            //BUSCAR NUMERO Y TIPO 
            $cn = "SELECT id_unico FROM gf_comprobante_cnt where numero = $numero AND tipocomprobante = $tc";
            $cn = $mysqli->query($cn);
            $cn = mysqli_fetch_row($cn);
            $ccn = $cn[0];
            $id_com_cnt = (int) $ccn;
            if ($id_com_cnt != 0) {
                $_SESSION['idCompCntV'] = $id_com_cnt;
            } //$id_com_cnt != 0
            else {
                $id_com_cnt = '';
            }
        }
        echo $id_com_cnt;
        break;
    case 17:
        $id_comp = $_POST['id_comp'];
        $num = 0;
        $sqlCompCnt = 'SELECT detComCnt.comprobante
				FROM gf_detalle_comprobante_pptal detComPtal
				LEFT JOIN gf_detalle_comprobante detComCnt ON detComCnt.detallecomprobantepptal = detComPtal.id_unico
				WHERE detComPtal.comprobantepptal = ' . $id_comp . ' 
				GROUP BY detComCnt.comprobante';
        $comprobanteCnt = $mysqli->query($sqlCompCnt);
        $rowCCnt = mysqli_fetch_row($comprobanteCnt);
        $id_com_cnt = (int) $rowCCnt[0];
        //$num = $coomprobanteCnt ->num_rows; 
        if ($num == 0) {
            $sqlCompPtal = 'SELECT cps.id_unico 
					FROM gf_comprobante_pptal cp
					LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcp.comprobantepptal = cp.id_unico
					LEFT JOIN gf_detalle_comprobante_pptal dcps ON dcps.comprobanteafectado = dcp.id_unico
					LEFT JOIN gf_comprobante_pptal cps ON dcps.comprobantepptal = cps.id_unico
					WHERE cp.id_unico = ' . $id_comp . ' 
    				GROUP BY cps.id_unico';
            $comprobantePptal = $mysqli->query($sqlCompPtal);
            $num = $comprobantePptal->num_rows;
        } //$num == 0
        echo $num;
        break;
    case 18:
        $id_comp = $_POST['id_comp'];
        $resp = 0;
        $sqlComCnt = 'SELECT dcc.comprobante
				FROM gf_detalle_comprobante dcc
				LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcc.detallecomprobantepptal = dcp.id_unico
				LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
				WHERE cp.id_unico = ' . $id_comp . '
				GROUP BY dcc.comprobante';
        $comprobanteCnt = $mysqli->query($sqlComCnt);
        $rowCCnt = mysqli_fetch_row($comprobanteCnt);
        $id_com_cnt = (int) $rowCCnt[0];
        if (empty($id_com_cnt)) {
            ##BUSCAR TIPO NUMERO
            $ctn = "SELECT p.numero,tc.id_unico FROM gf_comprobante_pptal p " . "LEFT JOIN gf_tipo_comprobante tc ON p.tipocomprobante = tc.comprobante_pptal  " . "WHERE p.id_unico = $id_comp";
            $ctn = $mysqli->query($ctn);
            $ctn = mysqli_fetch_row($ctn);
            $num = $ctn[0];
            $tc = $ctn[1];
            //BUSCAR CNT
            $comcnt = "SELECT id_unico FROM gf_comprobante_cnt WHERE numero = $num AND tipocomprobante = $tc";
            $comcnt = $mysqli->query($comcnt);
            $comcnt = mysqli_fetch_row($comcnt);
            $id_com_cnt = $comcnt[0];
        } //empty($id_com_cnt)
        #*****Eliminar Retenciones***#
        $sqlDelRet = 'DELETE FROM gf_retencion 
            	WHERE comprobante = \'' . $id_com_cnt . '\'';
        $resultDelRet = $mysqli->query($sqlDelRet);
        #*****Eliminar Mv Almacen***#
        $upd = "UPDATE gf_movimiento SET afectado_contabilidad =NULL WHERE afectado_contabilidad = '$id_com_cnt'";
        $upd = $mysqli->query($upd);

        $sqlDelDetCnt = 'DELETE FROM gf_detalle_comprobante  
            	WHERE comprobante = \'' . $id_com_cnt . '\'';
        $resultDelDetCnt = $mysqli->query($sqlDelDetCnt);
        $sqlDelComCnt = 'DELETE FROM gf_comprobante_cnt 
            	WHERE id_unico = \'' . $id_com_cnt . '\'';
        $resultDelComCnt = $mysqli->query($sqlDelComCnt);
        if ($resultDelComCnt == TRUE) {
            $resp = 1;
        } //$resultDelComCnt == TRUE
        echo $resp;
        break;
    case 19:
        $idComPtal = $_POST['idComPtal'];
        $comprobateCnt = 0;
        $queryComCnt = "SELECT min(detComp.comprobante) 
				FROM gf_detalle_comprobante detComp 
				LEFT JOIN gf_detalle_comprobante_pptal detComPtal ON detComPtal.id_unico = detComp.detallecomprobantepptal
				LEFT JOIN gf_comprobante_pptal comPtal ON comPtal.id_unico = detComPtal.comprobantepptal 
                                
				WHERE detComp.detallecomprobantepptal  = (SELECT id_unico
				FROM gf_detalle_comprobante_pptal 
				WHERE comprobantepptal = $idComPtal 
				GROUP BY comprobantepptal)
				";
        $compCnt = $mysqli->query($queryComCnt);
        $rowCC = mysqli_fetch_row($compCnt);
        $comprobateCnt = $rowCC[0];
        echo $comprobateCnt;
        break;
    case 20: // Agregar nuevo detalle comprobante pptal a una cuenta por pagar a partir de un registro presupuestal.
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
                            cc.id_unico, cc.nombre 
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

                    $rubro = $row[2];
                    $tercero = $row[3];
                    if(empty($row[4])){
                        $proyecto = 2147483647;
                    } else {
                        $proyecto = $row[4];
                    }
                    $idAfectado = $row[5];
                    $conceptorubro = $row[6];
                    $campo = "";
                    $variable = "";
                    if (empty($row[0])) {
                        $descripcion = 'NULL';
                    } else {
                        $descripcion = "'" . $row[0] . "'";
                    }

                    if (empty($row[12])) {
                        #** Buscar Centro Costo Varios **#
                        $cv = $con->Listar("SELECT * FROM gf_centro_costo 
                        WHERE parametrizacionanno = $anno AND nombre ='Varios'");
                        if (count($cv) > 0) {
                            $cc = $cv[0][0];
                        } else {
                            $cc = 'NULL';
                        }
                    } else {
                        $cc = $row[12];
                    }
                    $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal (valor, "
                            . "comprobantepptal, " . "rubrofuente, tercero, proyecto, "
                            . "comprobanteafectado, conceptorubro, descripcion, centro_costo) "
                            . "VALUES ('$valor', '$comprobantepptal', '$rubro', "
                            . "'$tercero', "
                            . "'$proyecto', '$idAfectado', '$conceptorubro', $descripcion, $cc)";
                    $resultadoInsert = $mysqli->query($insertSQL);
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
                    } //empty($row[9])
                    else {
                        $ccon = $row[9];
                    }
                } //empty($_POST['claseContrato'])
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
    case 21:
        // 21 Generar comprobante cnt y detalle comprobante (cnt) //Cinco.
        $user = $_SESSION['usuario'];
        $fechaElab = date('Y-m-d');
        $res = 0;
        $numerocontrato = '';
        $numCont = '';
        $clasecontrato = '';
        $claseCon = '';
        $compRet = $_POST['compRet'];
        $valorRet = (int) $_POST['valorRet'];
        $retencionBas = (int) $_POST['retencionBas'];
        $sqlComP = "SELECT id_unico, numero, fecha, descripcion, 
                numerocontrato, tercero, clasecontrato, tipocomprobante, 
                proyecto 
                FROM gf_comprobante_pptal   
                WHERE id_unico = $compRet";
        $compPtal = $mysqli->query($sqlComP);
        $rowCP = mysqli_fetch_row($compPtal);
        $compRet = $rowCP[0];
        $anno = $_SESSION['anno'];
        $_SESSION['compRet_Sesion'] = $compRet;
        //Tomar el el parámetro del año.
        $parametroAnno = $_SESSION['anno'];
        //Tomar el el parámetro de la compañía.
        $compania = $_SESSION['compania'];


        $sqlTipoCompCnt = "SELECT tc.id_unico, tc.retencion, tp.obligacionafectacion FROM gf_tipo_comprobante tc 
            LEFT JOIN gf_tipo_comprobante_pptal tp ON tc.comprobante_pptal = tp.id_unico 
            WHERE comprobante_pptal = $rowCP[7]";
        $tipoCompCnt = $mysqli->query($sqlTipoCompCnt);
        $rowTCC = mysqli_fetch_row($tipoCompCnt);
        $tipCompCnt = $rowTCC[0];
        $tipRete = $rowTCC[1];
        if (!empty($rowCP[4])) {
            $numCont = "'" . $rowCP[4] . "'";
        } else {
            $numCont = 'NULL';
        }
        if (!empty($rowCP[6])) {
            $claseCon = "'" . $rowCP[6] . "'";
        } else {
            $claseCon = 'NULL';
        }
        
        if (!empty($rowCP[8])) {
            $proyecto = "'" . $rowCP[8] . "'";
        } else {
            $proyecto = 'NULL';
        }

        $numeroCnt = $rowCP[1];
        $sqlComprobanteCnt = "INSERT INTO gf_comprobante_cnt (numero, fecha, descripcion, valorbase, "
                . "valorbaseiva, valorneto, tipocomprobante, compania, parametrizacionanno, "
                . "usuario, fecha_elaboracion, "
                . " tercero, estado, numerocontrato, clasecontrato)  
		VALUES($numeroCnt, '$rowCP[2]', '$rowCP[3]', $valorRet, "
                . "$retencionBas, $valorRet, $tipCompCnt, $compania, "
                . "$parametroAnno,'$user', '$fechaElab', "
                . "$rowCP[5], 1,$numCont, "
                . "$claseCon)";
        $resultadoCnt = $mysqli->query($sqlComprobanteCnt);
        if ($resultadoCnt == true) {
            $sqlUltComC = "SELECT MAX(id_unico) FROM gf_comprobante_cnt "
                    . "where tipocomprobante = $tipCompCnt AND numero =$numeroCnt";
            $ultComC = $mysqli->query($sqlUltComC);
            $rowUC = mysqli_fetch_row($ultComC);
            $ultimoComproCnt = $rowUC[0];

            if($rowTCC[2]==2){
                $res = 1;
                $_SESSION['idCompCntV'] = $ultimoComproCnt;
            } else {                 
                $sqlDetComP = "SELECT
                            detComP.id_unico,
                            detComP.valor,
                            detComP.proyecto,
                            detComP.rubrofuente, 
                            detComP.tercero , 
                            detComP.comprobanteafectado , 
                            detComP.centro_costo 
                          FROM
                            gf_detalle_comprobante_pptal detComP
                          LEFT JOIN
                            gf_comprobante_pptal comP ON comP.id_unico = detComP.comprobantepptal 
                          WHERE
                            comP.id_unico = $compRet";
                $detCompPtal = $mysqli->query($sqlDetComP);

                #**********************************Si es Cuenta Por Pagar****************************************************#
                if ($_POST['idform'] == 1) {
                    #***************Digitos de Configuración ************#
                    $parm = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Dígitos Interfaz Inventario'";
                    $parm = $mysqli->query($parm);
                    $parm = mysqli_fetch_row($parm);
                    $dig = $parm[0];
                    #************************Registrar Movimientos De Almacen***************#
                    $valAlmacen = 0;
                    if (!empty($_REQUEST['mova'])) {
                        $ids = explode(",", $_REQUEST['mova']);
                        foreach ($ids as $id) {
                            $id = trim($id);
                            if ($id != '') {
                                #***Detalles, Tipo, Plan Movimiento***#
                                $tma = "SELECT m.id_unico, 
                                        m.tipomovimiento, 
                                        dm.planmovimiento, 
                                        m.tercero, 
                                        m.centrocosto, 
                                        m.proyecto, 
                                        dm.id_unico, 
                                        dm.valor, 
                                        dm.iva 
                                    FROM 
                                        gf_movimiento m
                                    LEFT JOIN 
                                        gf_detalle_movimiento dm ON m.id_unico = dm.movimiento 
                                    LEFT JOIN 
                                        gf_plan_inventario pm ON pm.id_unico = dm.planmovimiento 
                                    WHERE 
                                        m.id_unico = $id AND LENGTH(pm.codi)=$dig";
                                $tma = $mysqli->query($tma);
                                if (mysqli_num_rows($tma) > 0) {
                                    while ($row1 = mysqli_fetch_row($tma)) {
                                        #**********Buscar La Configuración*********#
                                        $tipom = $row1[1];
                                        $plan = $row1[2];
                                        $terc = $row1[3];
                                        $cenc = $row1[4];
                                        if(empty($row1[5])){
                                            $proy = 2147483647;
                                        } else {
                                            $proy = $row1[5];
                                        }
                                        $valor = $row1[7] + $row1[8];
                                        $cn = "SELECT 
                                                ca.id_unico, 
                                                ca.cuenta_debito, 
                                                cd.naturaleza 
                                            FROM 
                                                gf_configuracion_almacen ca 
                                            LEFT JOIN 
                                                gf_cuenta cd ON ca.cuenta_debito = cd.id_unico 
                                            WHERE 
                                                ca.plan_inventario = $plan 
                                                AND ca.tipo_movimiento = $tipom
                                                AND ca.parametrizacion_anno = $parametroAnno";
                                        $cn = $mysqli->query($cn);
                                        $cuentas = mysqli_fetch_row($cn);
                                        $cuenta_debito = $cuentas[1];
                                        $naturaleza_debito = $cuentas[2];
                                        if ($naturaleza_debito == 2) {
                                            $vDeb = $valor * -1;
                                        } else {
                                            $vDeb = $valor;
                                        }
                                        $sqlDetComCnt = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, 
                                            valor, valorejecucion, comprobante, cuenta, naturaleza, 
                                            tercero, proyecto, centrocosto)  
                                            VALUES('$rowCP[2]', '$rowCP[3]', $vDeb, $valor, $ultimoComproCnt, "
                                                . "$cuenta_debito, $naturaleza_debito, $terc, $proy, $cenc)";
                                        $resultadoDetComCnt = $mysqli->query($sqlDetComCnt);
                                        if ($resultadoDetComCnt == true) {
                                            $valAlmacen += $vDeb;
                                            #************Actualizar Movimiento Almacen*******#
                                            $upd = "UPDATE gf_movimiento 
                                                    SET afectado_contabilidad = '$ultimoComproCnt' 
                                                    WHERE id_unico = $id";
                                            $upd = $mysqli->query($upd);
                                        }
                                    }
                                }
                            }
                        }
                        #*******Consultar El valor Totoal De La CXP ***#
                        $cuen = "SELECT SUM(valor) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $compRet";
                        $cuen = $mysqli->query($cuen);
                        $cuen = mysqli_fetch_row($cuen);
                        $vtotal = $cuen[0];
                        $vcxp = $vtotal - $valAlmacen;
                        if ($vtotal == $valAlmacen) {
                            $z = 0;
                            while ($rowDCP = mysqli_fetch_row($detCompPtal)) { //Aquí
                                if ($tipocomp == 2) {
                                    $tercero = $rowCP[5];
                                } else {
                                    $tercero = $rowDCP[4];
                                }
                                //=============================================================
                                $debitoCP = $_SESSION['debitoCP'];
                                $debitoCP = stripslashes($debitoCP);
                                $debitoCP = unserialize($debitoCP);
                                $idV = $rowDCP[0];
                                $crc = $debitoCP[$idV];
                                //BUSCAR DEBITO CRB
                                $crcu = "SELECT cuenta_debito, cuenta_credito FROM gf_concepto_rubro_cuenta WHERE id_unico = $crc";
                                $crcu = $mysqli->query($crcu);
                                $cuentas = mysqli_fetch_row($crcu);
                                $cuentaDeb = $cuentas[0];
                                $ctaC = $cuentas[1];

                                ####BUSCAR CUENTA CREDITO ####
                                $queryCuen = "SELECT DISTINCT 
                                        crc.cuenta_credito , c.naturaleza ,crc.centrocosto 
                                      FROM
                                        gf_concepto_rubro cr
                                      LEFT JOIN
                                        gf_rubro_fuente rf ON cr.rubro = rf.rubro
                                      LEFT JOIN
                                        gf_concepto_rubro_cuenta crc ON crc.concepto_rubro = cr.id_unico
                                      LEFT JOIN 
                                        gf_cuenta c ON crc.cuenta_credito = c.id_unico
                                      WHERE
                                        rf.id_unico=$rowDCP[3]  AND crc.cuenta_credito=$ctaC";
                                $cuent = $mysqli->query($queryCuen);
                                $rowC = mysqli_fetch_row($cuent);
                                $cuenta = $rowC[0];
                                $naturaleza = $rowC[1];
                                $vcred = $rowDCP[1];
                                if ($naturaleza == 1) {
                                    $vcred = $rowDCP[1] * -1;
                                }
                                if (empty($rowDCP[6])) {
                                    #** Buscar Centro Costo Varios **#
                                    $cv = $con->Listar("SELECT * FROM gf_centro_costo 
                                    WHERE parametrizacionanno = $anno AND nombre ='Varios'");
                                    if (count($cv) > 0) {
                                        $cc = $cv[0][0];
                                    } else {
                                        $cc = 'NULL';
                                    }
                                } else {
                                    $cc = $rowDCP[6];
                                }
                                if(empty($rowDCP[2])){
                                    $proyecto = 2147483647;
                                } else {
                                    $proyecto = $rowDCP[2];
                                }
                                $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, 
                                    valor, valorejecucion, comprobante, cuenta, naturaleza, 
                                    tercero, proyecto, centrocosto, detallecomprobantepptal)  
                                    VALUES('$rowCP[2]', '$rowCP[3]', $vcred, $rowDCP[1], $ultimoComproCnt, "
                                        . "$cuenta, $naturaleza, $tercero, $proyecto, $cc, $rowDCP[0])";
                                $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                            }
                        } else {
                            $sqlDetComP = "SELECT
                                        detComP.id_unico,
                                        detComP.valor,
                                        detComP.proyecto,
                                        detComP.rubrofuente, 
                                        detComP.tercero , 
                                        detComP.comprobanteafectado ,
                                        detComP.centro_costo 
                                      FROM
                                        gf_detalle_comprobante_pptal detComP
                                      LEFT JOIN
                                        gf_comprobante_pptal comP ON comP.id_unico = detComP.comprobantepptal 
                                      WHERE
                                        comP.id_unico = $compRet ORDER BY valor ASC ";
                            $detCompPtal1 = $mysqli->query($sqlDetComP);
                            while ($rowDCP = mysqli_fetch_row($detCompPtal1)) { //Aquí
                                if (empty($rowDCP[4])) {
                                    $tercero = $rowCP[5];
                                } else {
                                    $tercero = $rowDCP[4];
                                }
                                //=============================================================
                                $debitoCP = $_SESSION['debitoCP'];
                                $debitoCP = stripslashes($debitoCP);
                                $debitoCP = unserialize($debitoCP);
                                $idV = $rowDCP[0];
                                $crc = $debitoCP[$idV];
                                //BUSCAR DEBITO CRB
                                $crcu = "SELECT cuenta_debito, cuenta_credito FROM gf_concepto_rubro_cuenta WHERE id_unico = $crc";
                                $crcu = $mysqli->query($crcu);
                                $cuentas = mysqli_fetch_row($crcu);
                                $cuentaDeb = $cuentas[0];
                                $ctaC = $cuentas[1];
                                $queryCuen = "SELECT cuen.id_unico, cuen.naturaleza, conRubCun.centrocosto   
                                            FROM gf_cuenta cuen 
                                            LEFT JOIN gf_concepto_rubro_cuenta conRubCun ON conRubCun.cuenta_debito = cuen.id_unico 
                                            WHERE cuen.id_unico = $cuentaDeb";
                                $cuent = $mysqli->query($queryCuen);
                                $rowC = mysqli_fetch_row($cuent);
                                $cuenta = $rowC[0];
                                $naturaleza = $rowC[1];
                                $centrocosto = $rowC[2];
                                if (empty($rowDCP[6])) {
                                    #** Buscar Centro Costo Varios **#
                                    $cv = $con->Listar("SELECT * FROM gf_centro_costo 
                                    WHERE parametrizacionanno = $anno AND nombre ='Varios'");
                                    if (count($cv) > 0) {
                                        $centrocosto = $cv[0][0];
                                    } else {
                                        $centrocosto = 'NULL';
                                    }
                                } else {
                                    $centrocosto = $rowDCP[6];
                                }
                                $valorr = $rowDCP[1];

                                #********Verificar Valores***************#
                                if ($valAlmacen > $valorr) {
                                    $valAlmacen = $valAlmacen - $valorr;
                                } else {
                                    $vDeb = $valorr - $valAlmacen;
                                    if ($naturaleza == 2) {
                                        $vDeb = $vDeb * -1;
                                    }
                                    $cuentaDebito = $cuenta;
                                    if(empty($rowDCP[2])){
                                        $proyecto = 2147483647;
                                    } else {
                                        $proyecto = $rowDCP[2];
                                    }
                                    //Inserción en detalle comprobante (cnt) CUENTA DEBITO 
                                    $sqlDetComCnt = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, 
                                            valor, valorejecucion, comprobante, cuenta, naturaleza, 
                                            tercero, proyecto, centrocosto, detallecomprobantepptal)  
                                            VALUES('$rowCP[2]', '$rowCP[3]', $vDeb, $rowDCP[1], $ultimoComproCnt, "
                                            . "$cuenta, $naturaleza, $tercero, $proyecto, $centrocosto, $rowDCP[0])";
                                    $resultadoDetComCnt = $mysqli->query($sqlDetComCnt);
                                }
                                ####BUSCAR CUENTA CREDITO ####
                                $queryCuen = "SELECT DISTINCT 
                                        crc.cuenta_credito , c.naturaleza ,crc.centrocosto 
                                      FROM
                                        gf_concepto_rubro cr
                                      LEFT JOIN
                                        gf_rubro_fuente rf ON cr.rubro = rf.rubro
                                      LEFT JOIN
                                        gf_concepto_rubro_cuenta crc ON crc.concepto_rubro = cr.id_unico
                                      LEFT JOIN 
                                        gf_cuenta c ON crc.cuenta_credito = c.id_unico
                                      WHERE
                                        rf.id_unico=$rowDCP[3]  AND crc.cuenta_credito=$ctaC";
                                $cuent = $mysqli->query($queryCuen);
                                $rowC = mysqli_fetch_row($cuent);
                                $cuenta = $rowC[0];
                                $naturaleza = $rowC[1];
                                if(empty($rowDCP[2])){
                                    $proyecto = 2147483647;
                                } else {
                                    $proyecto = $rowDCP[2];
                                }
                                //Inserción en detalle comprobante (cnt) para saldo
                                $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, 
                                    valor, valorejecucion, comprobante, cuenta, naturaleza, 
                                    tercero, proyecto, centrocosto, detallecomprobantepptal)  
                                    VALUES('$rowCP[2]', '$rowCP[3]', $vcred, $rowDCP[1], $ultimoComproCnt, "
                                        . "$cuenta, $naturaleza, $tercero, $proyecto, $centrocosto, $rowDCP[0])";
                                $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                            }
                        }
                    } else {
                        $z = 0;
                        while ($rowDCP = mysqli_fetch_row($detCompPtal)) { //Aquí
                            if (empty($rowDCP[4])) {
                                $tercero = $rowCP[5];
                            } else {
                                $tercero = $rowDCP[4];
                            }
                            //=============================================================
                            $debitoCP = $_SESSION['debitoCP'];
                            $debitoCP = stripslashes($debitoCP);
                            $debitoCP = unserialize($debitoCP);
                            $idV = $rowDCP[0];
                            $crc = $debitoCP[$idV];
                            //BUSCAR DEBITO CRB
                            $crcu = "SELECT cuenta_debito, cuenta_credito FROM gf_concepto_rubro_cuenta WHERE id_unico = $crc";
                            $crcu = $mysqli->query($crcu);
                            $cuentas = mysqli_fetch_row($crcu);
                            $cuentaDeb = $cuentas[0];
                            $ctaC = $cuentas[1];
                            $queryCuen = "SELECT cuen.id_unico, cuen.naturaleza, conRubCun.centrocosto   
                                        FROM gf_cuenta cuen 
                                        LEFT JOIN gf_concepto_rubro_cuenta conRubCun ON conRubCun.cuenta_debito = cuen.id_unico 
                                        WHERE cuen.id_unico = $cuentaDeb";
                            $cuent = $mysqli->query($queryCuen);
                            $rowC = mysqli_fetch_row($cuent);
                            $cuenta = $rowC[0];
                            $naturaleza = $rowC[1];
                            if (empty($rowDCP[6])) {
                                #** Buscar Centro Costo Varios **#
                                $cv = $con->Listar("SELECT * FROM gf_centro_costo 
                                WHERE parametrizacionanno = $anno AND nombre ='Varios'");
                                if (count($cv) > 0) {
                                    $centrocosto = $cv[0][0];
                                } else {
                                    $centrocosto = 'NULL';
                                }
                            } else {
                                $centrocosto = $rowDCP[6];
                            }
                            $vDeb = $rowDCP[1];
                            if ($naturaleza == 2) {
                                $vDeb = $rowDCP[1] * -1;
                            }
                            $cuentaDebito = $cuenta;
                            #********Si No hay Movimientos De Almacen***************#
                            if (empty($_REQUEST['mova'])) {
                                if(empty($rowDCP[2])){
                                    $proyecto = 2147483647;
                                } else {
                                    $proyecto = $rowDCP[2];
                                }
                                //Inserción en detalle comprobante (cnt) CUENTA DEBITO 
                                $sqlDetComCnt = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, 
                                    valor, valorejecucion, comprobante, cuenta, naturaleza, 
                                    tercero, proyecto, centrocosto, detallecomprobantepptal)  
                                    VALUES('$rowCP[2]', '$rowCP[3]', $vDeb, $rowDCP[1], $ultimoComproCnt, "
                                        . "$cuenta, $naturaleza, $tercero, $proyecto, $centrocosto, $rowDCP[0])";
                                $resultadoDetComCnt = $mysqli->query($sqlDetComCnt);
                            } else {
                                
                            }
                            ####BUSCAR CUENTA CREDITO ####
                            $queryCuen = "SELECT DISTINCT 
                                    crc.cuenta_credito , c.naturaleza ,crc.centrocosto 
                                  FROM
                                    gf_concepto_rubro cr
                                  LEFT JOIN
                                    gf_rubro_fuente rf ON cr.rubro = rf.rubro
                                  LEFT JOIN
                                    gf_concepto_rubro_cuenta crc ON crc.concepto_rubro = cr.id_unico
                                  LEFT JOIN 
                                    gf_cuenta c ON crc.cuenta_credito = c.id_unico
                                  WHERE
                                    rf.id_unico=$rowDCP[3]  AND crc.cuenta_credito=$ctaC";
                            $cuent = $mysqli->query($queryCuen);
                            $rowC = mysqli_fetch_row($cuent);
                            $cuenta = $rowC[0];
                            $naturaleza = $rowC[1];
                            $vcred = $rowDCP[1];
                            if ($naturaleza == 1) {
                                $vcred = $rowDCP[1] * -1;
                            }
                            if(empty($rowDCP[2])){
                                $proyecto = 2147483647;
                            } else {
                                $proyecto = $rowDCP[2];
                            }
                            //Inserción en detalle comprobante (cnt) para saldo
                            $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, 
                                valor, valorejecucion, comprobante, cuenta, naturaleza, 
                                tercero, proyecto, centrocosto, detallecomprobantepptal)  
                                VALUES('$rowCP[2]', '$rowCP[3]', $vcred, $rowDCP[1], $ultimoComproCnt, "
                                    . "$cuenta, $naturaleza, $tercero, $proyecto, $centrocosto, $rowDCP[0])";
                            $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                        } //$rowDCP = mysqli_fetch_row($detCompPtal)
                        $_SESSION['idCompCntV'] = $ultimoComproCnt;
                        $res = 1;
                    }
                } else {
                    $fecha = $rowCP[2];
                    $cuentax = $con->Listar("SELECT GROUP_CONCAT(DISTINCT cn.comprobante) 
                        FROM gf_detalle_comprobante_pptal dc 
                        LEFT JOIN gf_detalle_comprobante_pptal dcc ON dc.comprobanteafectado = dcc.id_unico 
                        LEFT JOIN gf_detalle_comprobante cn ON cn.detallecomprobantepptal = dcc.id_unico 
                        LEFT JOIN gf_comprobante_cnt cnn ON cn.comprobante = cnn.id_unico 
                        LEFT JOIN gf_tipo_comprobante tc ON cnn.tipocomprobante = tc.id_unico 
                        WHERE dc.comprobantepptal =$compRet and tc.clasecontable =13");
                    $id_comp_cnt = $cuentax[0][0];

                    ##############VERIFICAR SI LAS CUENTAS QUE TRAE SON DE NÓMINA O NO #######################
                    #CONTAR LOS DETALLES
                    $numc = "SELECT DISTINCT dc.id_unico FROM gf_detalle_comprobante  dc "
                            . "WHERE dc.comprobante IN ($id_comp_cnt) ";
                    $numc = $mysqli->query($numc);
                    $numc = mysqli_num_rows($numc);
                    #CONTAR LOS DETALLES CON CUENTAS PASIVO NOMINA 
                    $nump = "SELECT DISTINCT dc.id_unico FROM gf_detalle_comprobante dc "
                            . "LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico "
                            . "WHERE dc.comprobante IN ($id_comp_cnt) "
                            . "AND c.clasecuenta = 20";
                    $nump = $mysqli->query($nump);
                    $nump = mysqli_num_rows($nump);
                    #########SI HAY CUENTAS DE PASIVO NOMINA
                    if ($nump > 0) {
                        #####SI TODAS LAS CUENTAS SON DE PASIVO NOMINA 
                        #CONTAR LOS DETALLES CON CUENTAS PASIVO NOMINA Y CLASE !6 Y !8
                        $numcom = "SELECT DISTINCT dc.id_unico FROM gf_detalle_comprobante dc "
                                . "LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico "
                                . "WHERE dc.comprobante IN ($id_comp_cnt) "
                                . "AND (c.clasecuenta !=6 AND c.clasecuenta !=8)";
                        $numcom = $mysqli->query($numcom);
                        $numcom = mysqli_num_rows($numcom);
                        //var_dump($numc==$numcom);
                        if ($numc == $numcom) {
                            #SE TRAEN TODAS LAS CUENTAS DE PASIVO
                            $sqlDetCom = "SELECT detCom.id_unico, detCom.valor, detCom.proyecto, 
                                   detCom.cuenta, detCom.naturaleza, detCom.centrocosto, detCom.detallecomprobantepptal, 
                                   detCom.tercero 
                                    FROM gf_detalle_comprobante detCom 
                                    LEFT JOIN gf_comprobante_cnt com ON com.id_unico = detCom.comprobante 
                                    LEFT JOIN gf_cuenta CT ON detCom.cuenta = CT.id_unico 
                                    LEFT JOIN gf_clase_cuenta clacu ON clacu.id_unico = CT.clasecuenta 
                                    WHERE com.id_unico IN ($id_comp_cnt) and clacu.id_unico = 20 ";
                            $detComp = $mysqli->query($sqlDetCom);
                            while ($rowDC = mysqli_fetch_row($detComp)) {
                                $terdeta = $rowDC[7];
                                $valor = $rowDC[1];
                                $valor = $valor * -1;
                                if(!empty($rowDC[2])){
                                    $proyecto = $rowDC[2];
                                }
                                $cuenta = $rowDC[3];
                                $naturaleza = $rowDC[4];
                                $centrocosto = $rowDC[5];
                                $detallecomprobantepptal = $rowDC[6];

                                if (empty($rowDC[6])) {
                                    $detallecomprobantepptal = 'NULL';
                                }
                                $afec = $rowDC[0];

                                if (empty($rowComp[3])) {
                                    $descripcion = 'NULL';
                                }
                                $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (descripcion, fecha, valor, valorejecucion, comprobante, 
                                        cuenta, naturaleza, tercero, proyecto, centrocosto,  detalleafectado, detallecomprobantepptal )  
                                                            VALUES($descripcion, '$fecha', $valor, "
                                        . "$valor, $ultimoComproCnt, $cuenta, $naturaleza, " . "$terdeta, $proyecto, $centrocosto,$afec, $detallecomprobantepptal )";
                                $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                            }
                            #############SI TODAS LA CUENTAS NO SON DE PASIVO TEMPORAL##################
                        } else {
                            #SE TRAEN TODAS LAS CUENTAS DE PASIVO
                            $sqlDetCom = "SELECT detCom.id_unico, detCom.valor, detCom.proyecto, 
                                   detCom.cuenta, detCom.naturaleza, detCom.centrocosto, detCom.detallecomprobantepptal, 
                                   detCom.tercero 
                                    FROM gf_detalle_comprobante detCom 
                                    LEFT JOIN gf_comprobante_cnt com ON com.id_unico = detCom.comprobante 
                                    LEFT JOIN gf_cuenta CT ON detCom.cuenta = CT.id_unico 
                                    LEFT JOIN gf_clase_cuenta clacu ON clacu.id_unico = CT.clasecuenta 
                                    WHERE ( com.id_unico IN ($id_comp_cnt) and clacu.id_unico = 4) 
                                    OR ( com.id_unico IN ($id_comp_cnt) and clacu.id_unico = 8) 
                                    AND clacu.id_unico !=20 ";
                            $detComp = $mysqli->query($sqlDetCom);
                            while ($rowDC = mysqli_fetch_row($detComp)) {
                                $terdeta = $rowDC[7];
                                $valorp = $rowDC[1];
                                #*** Buscar Si Tiene Afectado ***#
                                $af = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) FROM gf_detalle_comprobante dc WHERE dc.detalleafectado = $rowDC[0] ");
                                if (empty($af[0][0])) {
                                    $afect = 0;
                                } else {
                                    $afect = $af[0][0];
                                }
                                if ($valorp < 0) {
                                    $valorp = $valorp * -1 - $afect;
                                    $valorp = $valorp * -1;
                                } else {
                                    $valorp = $valorp - $afect;
                                }
                                $valor = $valorp * -1;
                                if(!empty($rowDC[2])){
                                    $proyecto = $rowDC[2];
                                }
                                $cuenta = $rowDC[3];
                                $naturaleza = $rowDC[4];
                                $centrocosto = $rowDC[5];
                                if(empty($rowDC[6])){
                                    $detallecomprobantepptal = 'NULL';
                                } else {
                                    $detallecomprobantepptal = $rowDC[6];
                                }
                                ##DETALLE CNT AFECTADO##
                                $afec = $rowDC[0];
                                if (empty($rowComp[3])) {
                                    $descripcion = 'NULL';
                                }
                                if ($valor != 0 || $valor != -0) {
                                    echo $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (descripcion, fecha, valor, valorejecucion, comprobante, 
                                        cuenta, naturaleza, tercero, proyecto, centrocosto,  detalleafectado, detallecomprobantepptal )  
                                                            VALUES($descripcion, '$fecha', $valor, $valor, "
                                            . "$ultimoComproCnt, $cuenta, $naturaleza, " . "$terdeta, "
                                            . "$proyecto, $centrocosto,$afec, $detallecomprobantepptal )";
                                    $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                                }
                            }
                        }
                    } else {

                        $sqlDetCom = "SELECT detCom.id_unico, dce.valor, 
                            detCom.proyecto, detCom.cuenta, detCom.naturaleza, detCom.centrocosto, 
                            detCom.detallecomprobantepptal, 
                            detCom.tercero , detCom.valor 
                           FROM gf_detalle_comprobante detCom 
                           LEFT JOIN gf_comprobante_cnt com ON com.id_unico = detCom.comprobante 
                           LEFT JOIN gf_cuenta CT ON detCom.cuenta = CT.id_unico 
                           LEFT JOIN gf_clase_cuenta clacu ON clacu.id_unico = CT.clasecuenta 
                           LEFT JOIN gf_detalle_comprobante_pptal dc ON detCom.detallecomprobantepptal =dc.id_unico 
                           LEFT JOIN gf_detalle_comprobante_pptal dce ON dc.id_unico = dce.comprobanteafectado 
                           WHERE ( com.id_unico IN ($id_comp_cnt) and clacu.id_unico = 4) 
                           OR ( com.id_unico IN ($id_comp_cnt) and clacu.id_unico = 8) 
                            AND dce.comprobantepptal = $compRet";
                        $detComp = $mysqli->query($sqlDetCom);

                        while ($rowDC = mysqli_fetch_row($detComp)) {
                            $valorp = $rowDC[1];
                            #*** Buscar Si Tiene Afectado ***#
                            $af = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) FROM gf_detalle_comprobante dc WHERE dc.detalleafectado = $rowDC[0] ");
                            # echo "SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) FROM gf_detalle_comprobante dc WHERE dc.detalleafectado = $rowDC[0] ";
                            if (empty($af[0][0])) {
                                $afect = 0;
                            } else {
                                $afect = $af[0][0];
                            }
                            if ($valorp < 0) {
                                $valorp = $valorp * -1 - $afect;
                                $valorp = $valorp * -1;
                            } else {
                                if($afect>$valorp){
                                    $valorp = $valorp;
                                } else {
                                    $valorp = $valorp - $afect;
                                }
                            }
                            $valor = $valorp * -1;
                            if(!empty($rowDC[2])){
                                $proyecto = $rowDC[2];
                            }
                            $cuenta = $rowDC[3];
                            $naturaleza = $rowDC[4];
                            if (isset($rowDC[5])) {
                                $centrocosto = $rowDC[5];
                            } else {
                                $centrocosto = "NULL";
                            }
                            if(empty($rowDC[6])){
                                $detallecomprobantepptal = 'NULL';
                            } else {
                                $detallecomprobantepptal = $rowDC[6];
                            }
                            $comprobantecnt = $rowCP[0];
                            $terceroDet = $rowDC[7];
                            $afec = $rowDC[0];

                            if ($valor != 0 || $valor != -0) {
                             $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (fecha, valor, valorejecucion, "
                                        . "comprobante, cuenta, naturaleza, tercero, proyecto, centrocosto, detalleafectado, "
                                        . "detallecomprobantepptal $descripcion)  
                                 VALUES('$fecha', $valor, $valor, $ultimoComproCnt, $cuenta, $naturaleza, $terceroDet, "
                                        . "$proyecto, $centrocosto, $afec,$detallecomprobantepptal $descr)";
                                $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                            }
                        }
                    }
                }
                $_SESSION['idCompCntV'] = $ultimoComproCnt;
                $res = 1;
            }
        } else {
            $res = 2;
        }
        echo $res;
        break;
    case 22: //Calcula valor base según se seleccione en Aplicar sobre.
        $tipoRete = $_POST['tipoRete'];
        $aplicar = $_POST['aplicar'];
        $res = 0;
        if ($aplicar == 3) {
            $sqlTipoRet = 'SELECT  modificarretencion, modificarbase       
                    FROM gf_tipo_retencion 
                    WHERE id_unico = ' . $tipoRete;
            $tipoRet = $mysqli->query($sqlTipoRet);
            $rowTR = mysqli_fetch_row($tipoRet);
            $res = $rowTR[0] . '|' . $rowTR[1];
        } //$aplicar == 3
        echo $res;
        break;
    #########FECHA EGRESO############            
    case 23:
        $id_tip_comp = $_REQUEST['id_comp'];
        $fecha = $_REQUEST['fecha'];
        $res = 0;
        $queryFechComp = "SELECT
                                ca.fecha
                              FROM
                                gf_detalle_comprobante_pptal dcp
                              LEFT JOIN
                                gf_detalle_comprobante_pptal dcpa ON dcp.comprobanteafectado = dcpa.id_unico
                              LEFT JOIN
                                gf_comprobante_pptal ca ON dcpa.comprobantepptal = ca.id_unico
                              WHERE
                                dcp.comprobantepptal = $id_tip_comp";
        $fechComp = $mysqli->query($queryFechComp);
        $row = mysqli_fetch_row($fechComp);
        $fechaPrev = $row[0];
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio . "-" . $mes . "-" . $dia;
        $fecha_prev = new DateTime($fechaPrev);
        $fecha_ = new DateTime($fecha);
        if ($fecha_prev <= $fecha_) {
            $res = 1;
        } //$fecha_prev <= $fecha_
        echo $res;
        break;
    ###########CASE 24 GUARDAR EGRESO QUE TIENE RETENCION##########
    case 24:
        $user = $_SESSION['usuario'];
        $fechaElab = date('Y-m-d');
        $res = 0;
        $_SESSION['idCompCnt'] = 0;
        //AGREGADO ERICA
        $_SESSION['idCompCnt'] = 0;
        $_SESSION['cntEgreso'] = 0;
        $tipocomprobante = $_POST['tipocomprobante'];
        $id_tip_comp = $tipocomprobante;
        $numero = $_POST['numero'];
        $fecha_formulario = $_POST['fecha'];
        $tipoComp = $tipocomprobante;
        $fecha = $fecha_formulario;
        $fecha_div = explode("/", $fecha);
        $anio = $fecha_div[2];
        $mes = $fecha_div[1];
        $dia = $fecha_div[0];
        $fecha = $anio . '-' . $mes . '-' . $dia;
        $fecha_ = new DateTime($fecha);
        $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
        $sumDias = $mysqli->query($querySum);
        if (mysqli_num_rows($sumDias) > 0) {
            $rowS = mysqli_fetch_row($sumDias);
            $sumarDias = $rowS[0];
        } //mysqli_num_rows($sumDias) > 0
        else {
            $sumarDias = 30;
        }
        $fecha_->modify('+' . $sumarDias . ' day');
        $fechaVen = (string) $fecha_->format('Y-m-d');
        $estado = 3;
        $responsable = $_SESSION['usuario_tercero'];
        $parametroAnno = $_SESSION['anno'];
        $tipocomprobante = $tipoComp;
        $parametroAnno = $_SESSION['anno'];
        $compania = $_SESSION['compania'];
        if (empty($_POST['id_comp_cnt']) || empty($_POST['id_comprobante_pptal']) ||
                $_POST['id_comprobante_pptal'] == 'N') {
            $tercero = $_POST['tercero'];
            $insertSQL = "INSERT INTO gf_comprobante_pptal 
                                (numero, fecha, fechavencimiento, parametrizacionanno, tipocomprobante, 
                                tercero, estado, responsable, usuario, fecha_elaboracion) 
				VALUES($numero, '$fecha', '$fechaVen', $parametroAnno, "
                    . "$tipocomprobante, " . "$tercero, $estado, $responsable, "
                    . "'$user', '$fechaElab')";
            $resultado = $mysqli->query($insertSQL);
            if ($resultado == true) {
                $queryUltComp = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE numero = $numero AND tipocomprobante = $tipocomprobante"
                        . "WHERE tipocomprobante =$tipocomprobante "
                        . "AND numero = $numero";
                $ultimComp = $mysqli->query($queryUltComp);
                $rowUC = mysqli_fetch_row($ultimComp);
                $idNuevoComprobante = $rowUC[0];
                $sqlTipoCompCnt = "SELECT id_unico FROM gf_tipo_comprobante " . "WHERE comprobante_pptal = $tipocomprobante";
                $tipoCompCnt = $mysqli->query($sqlTipoCompCnt);
                $rowTCC = mysqli_fetch_row($tipoCompCnt);
                $tipocomprobante = $rowTCC[0];
                $sqlComprobanteCnt = "INSERT INTO gf_comprobante_cnt " . "(numero, fecha, tipocomprobante, "
                        . "compania, parametrizacionanno, " . "tercero, usuario, 
                                            fecha_elaboracion)  
                                        VALUES($numero, '$fecha', $tipocomprobante, $compania, "
                        . "$parametroAnno, " . "$tercero,'$user', '$fechaElab')";
                $resultadoCnt = $mysqli->query($sqlComprobanteCnt);
                if ($resultadoCnt == true) {
                    $sqlUltComC = "SELECT MAX(id_unico) FROM gf_comprobante_cnt where numero = $numero and tipocomprobante = $tipocomprobante";
                    $ultComC = $mysqli->query($sqlUltComC);
                    $rowUC = mysqli_fetch_row($ultComC);
                    $ultimoComproCnt = $rowUC[0];
                    $_SESSION['cntEgreso'] = $ultimoComproCnt;
                    $res = 1;
                } //$resultadoCnt == true
                else {
                    $res = 0;
                }
            } //$resultado == true
            else {
                $res = 0;
            }
            $idNuevoComprobante = $ultimoComproCnt;
        } //empty($_POST['id_comp_cnt']) || empty($_POST['id_comprobante_pptal']) || $_POST['id_comprobante_pptal'] == 'N'
        else {
            $id_comp_cnt = $_POST['id_comp_cnt'];
            $id_comprobante_pptal = $_POST['id_comprobante_pptal'];
            //Captura de datos e instrucción SQL para su posterior inserción en la tabla gf_comprobante_pptal como egreso.
            $idAnteriorComprobante = $id_comprobante_pptal;
            $queryCompro = "SELECT comp.id_unico, comp.numero, comp.fecha, 
                            comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, 
                            tipCom.codigo, tipCom.nombre, comp.tercero, comp.numerocontrato, 
                            comp.clasecontrato  
      			FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      			WHERE comp.tipocomprobante = tipCom.id_unico 
      			AND comp.id_unico =  $idAnteriorComprobante";
            $comprobante = $mysqli->query($queryCompro);
            $rowComp = mysqli_fetch_row($comprobante);
            $id = $rowComp[0];
            $descripcion = '"' . $mysqli->real_escape_string('' . $rowComp[3] . '') . '"';
            $tercero = $rowComp[8];
            if (empty($rowComp[9]) || $rowComp[9] == NULL || $rowComp[9] == "") {
                $numContrato = 'NULL';
            } //empty($rowComp[9]) || $rowComp[9] == NULL || $rowComp[9] == ""
            else {
                $numContrato = "'" . $rowComp[9] . "'";
            }
            if (empty($rowComp[10]) || $rowComp[10] == NULL || $rowComp[10] == "") {
                $claseContrato = 'NULL';
            } //empty($rowComp[10]) || $rowComp[10] == NULL || $rowComp[10] == ""
            else {
                $claseContrato = $rowComp[10];
            }
            if ($descripcion == '""') { // Desde acá inserción del egreso en la tabla gf_comprobante_pptal.
                $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, 
                    parametrizacionanno, tipocomprobante, tercero, estado, responsable, 
                    numerocontrato, clasecontrato, usuario, fecha_elaboracion) 
		 VALUES($numero, '$fecha', '$fechaVen', $parametroAnno, "
                        . "$tipocomprobante, $tercero, $estado, $responsable, $numContrato, "
                        . "$claseContrato, '$user', '$fechaElab')";
            } //$descripcion == '""'
            else {
                $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, 
                    descripcion, parametrizacionanno, tipocomprobante, tercero, estado, responsable, 
                    numerocontrato, clasecontrato, usuario, fecha_elaboracion) 
				  	VALUES($numero, '$fecha', '$fechaVen', $descripcion, "
                        . "$parametroAnno, $tipocomprobante, $tercero, $estado, $responsable, "
                        . "$numContrato, $claseContrato, '$user', '$fechaElab')";
            }

            $resultado = $mysqli->query($insertSQL);
            if ($resultado == true) {
                $queryUltComp = "SELECT MAX(id_unico) FROM gf_comprobante_pptal "
                        . "WHERE tipocomprobante = '$tipocomprobante' AND numero=$numero ";
                $ultimComp = $mysqli->query($queryUltComp);
                $rowUC = mysqli_fetch_row($ultimComp);
                $idNuevoComprobante = $rowUC[0];
                //Selección de datos para posterior ingreso del detalle presupuestal del egreso anteriormente insertado.
                if (empty($_POST['devengos']) && empty($_POST['aportes'])) {
                    $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, 
                                    detComP.rubrofuente, detComP.tercero, detComP.proyecto, detComP.id_unico, 
                                    detComP.conceptoRubro , detComP.centro_costo 
				   	FROM gf_detalle_comprobante_pptal detComP
				   	left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
				   	left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
				   	left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptoRubro 
				   	left join gf_concepto con on con.id_unico = conRub.concepto 
				   	where detComP.comprobantepptal = $idAnteriorComprobante";
                } elseif (!empty($_POST['devengos']) && !empty($_POST['aportes'])) {
                    $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, 
                                    detComP.rubrofuente, detComP.tercero, detComP.proyecto, detComP.id_unico, 
                                    detComP.conceptoRubro ,detComP.centro_costo 
				   	FROM gf_detalle_comprobante_pptal detComP
				   	left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
				   	left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
				   	left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptoRubro 
				   	left join gf_concepto con on con.id_unico = conRub.concepto 
				   	where detComP.comprobantepptal = $idAnteriorComprobante";
                } elseif (!empty($_POST['devengos'])) {
                    $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, 
                                    detComP.rubrofuente, detComP.tercero, detComP.proyecto, detComP.id_unico, 
                                    detComP.conceptoRubro ,detComP.centro_costo 
				   	FROM gf_detalle_comprobante_pptal detComP
				   	left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
				   	left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
				   	left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptoRubro 
				   	left join gf_concepto con on con.id_unico = conRub.concepto 
				   	where detComP.comprobantepptal = $idAnteriorComprobante AND detComP.clasenom ='devengo'";
                } elseif (!empty($_POST['aportes'])) {
                    $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, 
                                    detComP.rubrofuente, detComP.tercero, detComP.proyecto, detComP.id_unico, 
                                    detComP.conceptoRubro,detComP.centro_costo  
				   	FROM gf_detalle_comprobante_pptal detComP
				   	left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
				   	left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
				   	left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptoRubro 
				   	left join gf_concepto con on con.id_unico = conRub.concepto 
				   	where detComP.comprobantepptal = $idAnteriorComprobante AND detComP.clasenom ='informativo'";
                } else {
                    $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, 
                                    detComP.rubrofuente, detComP.tercero, detComP.proyecto, detComP.id_unico, 
                                    detComP.conceptoRubro ,detComP.centro_costo 
				   	FROM gf_detalle_comprobante_pptal detComP
				   	left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
				   	left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
				   	left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptoRubro 
				   	left join gf_concepto con on con.id_unico = conRub.concepto 
				   	where detComP.comprobantepptal = $idAnteriorComprobante";
                }

                $resultado = $mysqli->query($queryAntiguoDetallPttal);
                $comprobantepptal = $idNuevoComprobante;
                while ($row = mysqli_fetch_row($resultado)) {
                    $terdetalle = $row[3];
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
                        $rubro = $row[2];
                        if(empty($row[4])){
                            $proyecto = 2147483647;
                        } else {
                            $proyecto = $row[4];
                        }
                        $idAfectado = $row[5];
                        $conceptorubro = $row[6];
                        $campo = "";
                        $variable = "";
                        if (($descripcion != '""') || ($descripcion != NULL)) {

                            $descripcion = "$descripcion";
                        } else {
                            $descripcion = 'NULL';
                        }
                        if (empty($row[7])) {
                            #** Buscar Centro Costo Varios **#
                            $cv = $con->Listar("SELECT * FROM gf_centro_costo 
                            WHERE parametrizacionanno = $anno AND nombre ='Varios'");
                            if (count($cv) > 0) {
                                $cc = $cv[0][0];
                            } else {
                                $cc = 'NULL';
                            }
                        } else {
                            $cc = $row[7];
                        }
                        //Inserción de datos en detalle presupuestal para el egreso.
                        $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal (valor, "
                                . "comprobantepptal, rubrofuente, " . "tercero, proyecto, "
                                . "comprobanteafectado, conceptoRubro, descripcion, centro_costo)"
                                . " VALUES ($variable $valor, $comprobantepptal, $rubro, "
                                . "$terdetalle, $proyecto, $idAfectado, $conceptorubro, $descripcion, $cc)";
                        $resultadoInsert = $mysqli->query($insertSQL);
                    } //$valorPpTl > 0
                } //$row = mysqli_fetch_row($resultado)
                $updateSQL = "UPDATE gf_comprobante_pptal  
					SET estado = $estado     
				    WHERE id_unico = $idAnteriorComprobante";
                $resultadoUpdate = $mysqli->query($updateSQL);
                $_SESSION['id_comp_pptal_GE'] = $idNuevoComprobante;
                $_SESSION['nuevo_GE'] = 1;
                $res = 1;
            }
        }

        $_SESSION['id_comp_pptal_GE'] = $idNuevoComprobante;
        $_SESSION['nuevo_GE'] = 1;
        $_SESSION['terceroGuardado'] = $tercero;
        $_SESSION['comprobanteGenerado'] = $idNuevoComprobante;
        echo $res;
        break;
        #CAmbiar Aplicación Retenciones
    case 25:
        $tr = $_REQUEST['tiporetencion'];
        $rowd = $con->Listar("SELECT tb.id_unico, tb.nombre FROM gf_tipo_base tb 
            ORDER BY (SELECT tr.tipobase  FROM gf_tipo_retencion tr
            WHERE tr.id_unico = $tr)");
        $html = '';
        for ($i = 0; $i < count($rowd); $i++) {
            $html .= '<option value="'.$rowd[$i][0].'">'.$rowd[$i][1].'</option>';
        }
        echo $html;
    break;
} //$estruc
?>