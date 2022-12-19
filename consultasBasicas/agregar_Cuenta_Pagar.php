<?php
######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#18/07/2018 |Erica G. | Centro Costo Presupuestal Privada
#15/09/2017 | ERICA G. | EGRESO PARA INTERFAZ DE NOMINA
#07/06/2017 | ERICA G. | CUENTAS DE NÓMINA
#20/05/2017 | ERICA G. | VALIDACION DE FECHA
#17/05/2017 | ERICA G. | VALIDACION DE RETENCION POR EGRESO O POR CUENTA POR PAGAAR
#10/04/2017 |ERICA G. |REGISTRO TERCEROS EN EGRESO DET
#08/03/2017 |ERICA G. | MODIFICACION EGRESO 
#01/03/2017 | Erica G. |Descripciones no cambiaran, validacion
#15/02/2017 05:00 Erica G. //Se arregló la consulta para agregar cuenta por pagar en egresos vacios
#MODIFICADO 27/01/2017 ERICA G.
#######################################
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');

session_start();
$anno = $_SESSION['anno'];
$con = new ConexionPDO();
$case = $_POST['case'];
switch ($case) {
    case 1:
        $cuentaxpagar = $_POST['cuentaxpagar'];
        $query = "SELECT
                dc.comprobante
              FROM
                gf_detalle_comprobante dc
              LEFT JOIN
                gf_detalle_comprobante_pptal dcp ON dc.detallecomprobantepptal = dcp.id_unico
              WHERE
                dcp.comprobantepptal =$cuentaxpagar";
        $query = $mysqli->query($query);
        if (mysqli_num_rows($query) > 0) {
            echo json_encode('1');
        } else {
            echo json_encode('2');
        }
        break;


    case 2:
        $comprobantepptal = $_POST['comprobante'];
        $tercero = $_POST['tercero'];
        $cuentaxpagar = $_POST['cuentaxpagar'];

########INSERTAR COMPROBANTE PPTAL#############
        #BUSCO DETALLES PPTALES DE LA CUENTA POR PAGAR
        $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, 
            detComP.rubrofuente, detComP.tercero, detComP.proyecto, 
            detComP.id_unico, detComP.conceptoRubro , 
            cc.id_unico, cc.nombre 
      FROM gf_detalle_comprobante_pptal detComP
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptoRubro 
      left join gf_concepto con on con.id_unico = conRub.concepto 
      LEFT JOIN gf_centro_costo cc ON detComP.centro_costo = cc.id_unico 
      where detComP.comprobantepptal = $cuentaxpagar";
        $resultado = $mysqli->query($queryAntiguoDetallPttal);

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
            }

            $saldDisp = $row[1] - $totalAfec;
            $valorPpTl = $saldDisp;

            if ($valorPpTl > 0) {

                $descripcion = '"' . $row[0] . '"';

                $valor = $valorPpTl;
                $rubro = $row[2];
                $terceroDet = $row[3];
                if(empty($row[4])){
                    $proyecto = 'NULL';
                } else {
                    $proyecto = $row[4];
                }
                $idAfectado = $row[5];
                $conceptorubro = $row[6];

                $campo = "";
                $variable = "";
                if (($descripcion != '""') || ($descripcion != NULL)) {
                    $campo = "descripcion,";
                    $descripcion = trim($descripcion, '"'); //Quita las comillas
                    $variable = "'$descripcion',";
                }
                
                
                if(empty($row[7])){
                    #** Buscar Centro Costo Varios **#
                    $cv =$con->Listar("SELECT * FROM gf_centro_costo 
                    WHERE parametrizacionanno = $anno AND nombre ='Varios'");
                    if(count($cv)>0){
                        $cc = $cv[0][0];
                    } else {
                        $cc = 'NULL';
                    }
                } else {
                    $cc = $row[7];
                }
                #INSERTO LOS DETALLES PPTALES AL EGRESO
                $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal ($campo valor, comprobantepptal, rubrofuente, "
                        . "tercero, proyecto, comprobanteafectado, conceptoRubro, centro_costo) "
                        . "VALUES ($variable $valor, $comprobantepptal, "
                        . "$rubro, $terceroDet, $proyecto, $idAfectado, "
                        . "$conceptorubro,$cc)";
                $resultadoInsert = $mysqli->query($insertSQL);
            }
        }
        ##############################################   
        ##EDITO EL ENCABEZADO DEL COMPROBANTE PPTAL##
        ##BUSCAR DATOS DE LA CUENTA POR PAGAR##
        $ctxd = " SELECT descripcion, numerocontrato, clasecontrato FROM gf_comprobante_pptal "
                . "WHERE id_unico = $cuentaxpagar";
        $ctxd = $mysqli->query($ctxd);
        if (mysqli_num_rows($ctxd) > 0) {
            $ctxd = mysqli_fetch_row($ctxd);
            $descripcionu = $ctxd[0];
            $numcontu = $ctxd[1];
            $clasecu = $ctxd[2];
            $updTer = "UPDATE gf_comprobante_pptal SET tercero = $tercero, descripcion = '$descripcionu', "
                    . "numerocontrato ='$numcontu', clasecontrato='$clasecu' "
                    . "WHERE id_unico = $comprobantepptal";
        } else {
            $updTer = "UPDATE gf_comprobante_pptal SET tercero = $tercero "
                    . "WHERE id_unico = $comprobantepptal";
        }
        $updTer = $mysqli->query($updTer);

        #######COMPROBANTE CNT###############
        #BUSCAR EL NUMERO Y EL TIPO DEL PPTAL YA HECHO
        $pptal = "SELECT
            cp.numero,
            tc.id_unico, cp.fecha 
          FROM
            gf_comprobante_pptal cp
          LEFT JOIN
            gf_tipo_comprobante tc ON cp.tipocomprobante = tc.comprobante_pptal
          WHERE
            cp.id_unico ='$comprobantepptal'";
        $pptal = $mysqli->query($pptal);
        if (mysqli_num_rows($pptal) > 0) {
            #BUSCAR CNT CORRESPONDIENTE AL MISMO NUMERO Y AL TIPO #cnt ya hecho
            $pptal = mysqli_fetch_row($pptal);
            $sqlComP1 = "SELECT id_unico, numero, fecha, descripcion, numerocontrato, tercero, 
            clasecontrato, tipocomprobante, valorbase, valorbaseiva, valorneto, estado       
            FROM gf_comprobante_cnt   
            WHERE numero = $pptal[0] AND tipocomprobante=$pptal[1]";

            $compPtal1 = $mysqli->query($sqlComP1);

            if (mysqli_num_rows($compPtal1) == 0) {
                $parm = $_SESSION['anno'];
                $comp = $_SESSION['compania'];
                #* Buscar Si El Comprobante Ya esta Hecho 
                $com = "SELECT * FROM gf_comprobante_cnt  WHERE parametrizacionanno = $parm  AND numero = $pptal[0] AND tipocomprobante=$pptal[1]";
                $com = $mysqli->query($com);
                if(mysqli_num_rows($com)>0){
                    
                } else {
                    ##INSERTAR COMPROBANTE CNT##

                    $insert = "INSERT INTO gf_comprobante_cnt (numero, fecha, tipocomprobante, "
                            . "compania, parametrizacionanno, tercero,descripcion ) "
                            . "VALUES ($pptal[0], '$pptal[2]', $pptal[1], $comp, $parm, $tercero, '$descripcionu')";
                    $insert = $mysqli->query($insert);
                }
            }
            $sqlComP = "SELECT id_unico, numero, fecha, descripcion, numerocontrato, tercero, 
            clasecontrato, tipocomprobante, valorbase, valorbaseiva, valorneto, estado       
            FROM gf_comprobante_cnt   
            WHERE numero = $pptal[0] AND tipocomprobante=$pptal[1]";
            $compPtal = $mysqli->query($sqlComP);
            $rowCP = mysqli_fetch_row($compPtal);
            $descr = "";
            $idCompCnt = $rowCP[0];
            $numero = $rowCP[1];
            $fecha = $rowCP[2];
            $valorbase = $rowCP[8];
            $valorbaseiva = $rowCP[9];
            $valorneto = $rowCP[10];
            $estado = $rowCP[11];
            $ultimoComproCnt = $idCompCnt;
            $comprobantecnt = $idCompCnt;

            if (!empty($rowCP[3])) {
                $descripcion = ', descripcion';
                $descr = ", '" . $rowCP[3] . "'";
            } else {
                $descripcion = ', descripcion';
                $descr = ", ''";
            }

            if (!empty($rowCP[4])) {
                $numerocontrato = ', numerocontrato';
                $numCont = ', ' . $rowCP[4];
            } else {
                $numerocontrato = ', numerocontrato';
                $numCont = ', ' . NULL;
            }

            if (!empty($rowCP[6])) {
                $clasecontrato = ', clasecontrato';
                $claseCon = ', ' . $rowCP[6];
            } else {
                $clasecontrato = ', clasecontrato';
                $claseCon = ', ' . NULL;
            }
#buscar detalles cnt de la cxpagar#


            $id_comp_cnt = 0;
#BUSCAR LO DE LA CUENTA POR PAGAR
            $sqlDetalles = 'SELECT max(dcp.id_unico)
        FROM gf_detalle_comprobante_pptal dcp 
        WHERE dcp.comprobantepptal = ' . $cuentaxpagar;
            $sqlDetalles = $mysqli->query($sqlDetalles);
            $id_comp_cnt = 0;
            if (mysqli_num_rows($sqlDetalles) > 0) {

                $sqlDetalles = mysqli_fetch_row($sqlDetalles);
                $queryComCnt = 'SELECT MAX(detComp.comprobante)
                FROM gf_detalle_comprobante detComp 
                LEFT JOIN gf_detalle_comprobante_pptal detComPtal ON detComPtal.id_unico = detComp.detallecomprobantepptal
                LEFT JOIN gf_comprobante_pptal comPtal ON comPtal.id_unico = detComPtal.comprobantepptal 
                 LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico = detComp.comprobante 
                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                WHERE  tc.clasecontable =13 AND detComp.detallecomprobantepptal  = ' . $sqlDetalles[0];

                $compCnt = $mysqli->query($queryComCnt);
                $rowCC = mysqli_fetch_row($compCnt);
                if ($id_comp_cnt < $rowCC[0]) {
                    $id_comp_cnt = $rowCC[0];
                }

                $id_comp_cnt = $id_comp_cnt;
            } else {
                $id_comp_cnt = 0;
            }



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
                        $valor = $rowDC[1];
                        $valor = $valor * -1;
                        if(empty($rowDC[2])){
                            $proyecto ='NULL';
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
                                WHERE ( com.id_unico = $id_comp_cnt and clacu.id_unico = 4) 
                                OR ( com.id_unico = $id_comp_cnt and clacu.id_unico = 8) 
                                AND clacu.id_unico !=20 ";
                    $detComp = $mysqli->query($sqlDetCom);
                    while ($rowDC = mysqli_fetch_row($detComp)) {
                        $terdeta = $rowDC[7];
                        $valorp = $rowDC[1];
                        #*** Buscar Si Tiene Afectado ***#
                        $af = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) FROM gf_detalle_comprobante dc WHERE dc.detalleafectado = $rowDC[0] ");
                        if(empty($af[0][0])){
                            $afect =0;
                        } else {
                            $afect =$af[0][0];
                        }
                        if($valorp<0){$valorp=$valorp*-1-$afect; $valorp=$valorp*-1;} else {$valorp=$valorp-$afect;}
                        $valor = $valorp * -1;
                        if(empty($rowDC[2])){
                            $proyecto ='NULL';
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
                        if($valor!=0 || $valor !=-0){
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
    WHERE ( com.id_unico = $id_comp_cnt and clacu.id_unico = 4) 
    OR ( com.id_unico = $id_comp_cnt and clacu.id_unico = 8)";
                $detComp = $mysqli->query($sqlDetCom);

                while ($rowDC = mysqli_fetch_row($detComp)) {
                    $valorp = $rowDC[1];
                    #*** Buscar Si Tiene Afectado ***#
                    $af = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) FROM gf_detalle_comprobante dc WHERE dc.detalleafectado = $rowDC[0] ");
                   # echo "SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) FROM gf_detalle_comprobante dc WHERE dc.detalleafectado = $rowDC[0] ";
                    if(empty($af[0][0])){
                        $afect =0;
                    } else {
                        $afect =$af[0][0];
                    }
                    if($valorp<0){$valorp=$valorp*-1-$afect; $valorp=$valorp*-1;} else {$valorp=$valorp-$afect;}
                    $valor = $valorp * -1;
                    if(empty($rowDC[2])){
                        $proyecto ='NULL';
                    } else {
                        $proyecto = $rowDC[2];
                    }
                    $cuenta = $rowDC[3];
                    $naturaleza = $rowDC[4];
                    if(isset($rowDC[5])){
                    $centrocosto = $rowDC[5];
                    } else {
                        $centrocosto = "NULL";
                    }
                    if(empty($rowDC[6])){
                        $detallecomprobantepptal = "NULL";
                    } else {
                        $detallecomprobantepptal = $rowDC[6];
                    }
                    $comprobantecnt = $rowCP[0];
                    $terceroDet = $rowDC[7];
                    $afec =  $rowDC[0];
                    
                    if($valor!=0 || $valor !=-0){
                    $sqlDetComCntSal = "INSERT INTO gf_detalle_comprobante (fecha, valor, valorejecucion, "
                            . "comprobante, cuenta, naturaleza, tercero, proyecto, centrocosto, detalleafectado, "
                            . "detallecomprobantepptal $descripcion)  
                    VALUES('$fecha', $valor, $valor, $comprobantecnt, $cuenta, $naturaleza, $terceroDet, "
                            . "$proyecto, $centrocosto, $afec,$detallecomprobantepptal $descr)";
                    $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                    }
                }
            }
            ##ACTUALIZAR TERCERO DEL CNT###
            #BUSCAR DATOS CTAXPAGAR CNT#
            $cxpcnt = "SELECT descripcion, numerocontrato, estado, clasecontrato FROM gf_comprobante_cnt "
                    . "where id_unico = $id_comp_cnt";
            $cxpcnt = $mysqli->query($cxpcnt);
            $updter = "UPDATE gf_comprobante_cnt SET tercero = $tercero WHERE id_unico = $comprobantecnt";
            $updter = $mysqli->query($updter);
            $updTer = "UPDATE gf_comprobante_pptal SET tercero = $tercero "
                   . "WHERE id_unico = $comprobantepptal";
            $updter = $mysqli->query($updTer);
                        



            $_SESSION['id_comp_pptal_GE'] = $comprobantepptal;
            $_SESSION['idComPtal'] = $comprobantepptal;
            $_SESSION['nuevo_GE'] = 1;
            $_SESSION['terceroGuardado'] = $tercero;
            $_SESSION['comprobanteGenerado'] = $comprobantepptal;
            $_SESSION['idCompCnt'] = $comprobantecnt;

            echo json_encode('1');
        }
        break;
    ###GUARDAR SOLO PPTAL EGRESO###
    case 3:
        $fecha_formulario = $_POST['fecha'];
        $comprobantepptal = $_POST['comprobante'];
        $cuentaxpagar = $_POST['cuentaxpagar'];
        $tercero = $_POST['tercero'];
        ##########VALIDAR LA FECHA DE LA CUENTA POR PAGAR#########
        $fcxp = "SELECT DATE_FORMAT(cp.fecha,'%d-%m-%Y') FROM gf_comprobante_pptal "
                . "WHERE id_unico =$cuentaxpagar";


        ########INSERTAR COMPROBANTE PPTAL#############
        #BUSCO DETALLES PPTALES DE LA CUENTA POR PAGAR
        if (empty($_POST['devengos']) && empty($_POST['aportes'])) {
            $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, 
        detComP.rubrofuente, detComP.tercero, detComP.proyecto, 
         detComP.id_unico, detComP.conceptoRubro , detComP.centro_costo 
          FROM gf_detalle_comprobante_pptal detComP
          left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
          left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
          left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptoRubro 
          left join gf_concepto con on con.id_unico = conRub.concepto 
          where detComP.comprobantepptal = $cuentaxpagar";
        } elseif (!empty($_POST['devengos']) && !empty($_POST['aportes'])) {
            $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, 
        detComP.rubrofuente, detComP.tercero, detComP.proyecto, 
         detComP.id_unico, detComP.conceptoRubro, detComP.centro_costo 
          FROM gf_detalle_comprobante_pptal detComP
          left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
          left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
          left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptoRubro 
          left join gf_concepto con on con.id_unico = conRub.concepto 
          where detComP.comprobantepptal = $cuentaxpagar";
        } elseif (!empty($_POST['devengos'])) {
            $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, 
        detComP.rubrofuente, detComP.tercero, detComP.proyecto, 
         detComP.id_unico, detComP.conceptoRubro, detComP.centro_costo  
          FROM gf_detalle_comprobante_pptal detComP
          left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
          left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
          left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptoRubro 
          left join gf_concepto con on con.id_unico = conRub.concepto 
          where detComP.comprobantepptal = $cuentaxpagar AND detComP.clasenom ='devengo'";
        } elseif (!empty($_POST['aportes'])) {
            $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, 
        detComP.rubrofuente, detComP.tercero, detComP.proyecto, 
         detComP.id_unico, detComP.conceptoRubro , detComP.centro_costo 
          FROM gf_detalle_comprobante_pptal detComP
          left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
          left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
          left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptoRubro 
          left join gf_concepto con on con.id_unico = conRub.concepto 
          where detComP.comprobantepptal = $cuentaxpagar AND detComP.clasenom ='informativo'";
        } else {
            $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, 
        detComP.rubrofuente, detComP.tercero, detComP.proyecto, 
         detComP.id_unico, detComP.conceptoRubro , detComP.centro_costo 
          FROM gf_detalle_comprobante_pptal detComP
          left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
          left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
          left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptoRubro 
          left join gf_concepto con on con.id_unico = conRub.concepto 
          where detComP.comprobantepptal = $cuentaxpagar";
        }

        $resultado = $mysqli->query($queryAntiguoDetallPttal);

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
            }

            $saldDisp = $row[1] - $totalAfec;
            $valorPpTl = $saldDisp;

            if ($valorPpTl > 0) {

                $descripcion = '"' . $row[0] . '"';

                $valor = $valorPpTl;
                $rubro = $row[2];
                $terceroDet = $row[3];
                if(empty($row[4])){
                    $proyecto ='NULL';
                } else {
                    $proyecto = $row[4];
                }
                $idAfectado = $row[5];
                $conceptorubro = $row[6];

                $campo = "";
                $variable = "";
                if (($descripcion != '""') || ($descripcion != NULL)) {
                    $campo = "descripcion,";
                    $descripcion = trim($descripcion, '"'); //Quita las comillas
                    $variable = "'$descripcion',";
                }
                if(empty($row[7])){
                    #** Buscar Centro Costo Varios **#
                    $cv =$con->Listar("SELECT * FROM gf_centro_costo 
                    WHERE parametrizacionanno = $anno AND nombre ='Varios'");
                    if(count($cv)>0){
                        $cc = $cv[0][0];
                    } else {
                        $cc = 'NULL';
                    }
                } else {
                    $cc = $row[7];
                }
                #INSERTO LOS DETALLES PPTALES AL EGRESO
                $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal 
                        ($campo valor, comprobantepptal, rubrofuente, "
                        . "tercero, proyecto, comprobanteafectado, conceptoRubro,centro_costo) "
                        . "VALUES ($variable $valor, $comprobantepptal, "
                        . "$rubro, $terceroDet, $proyecto, $idAfectado, "
                        . "$conceptorubro,$cc)";
                $resultadoInsert = $mysqli->query($insertSQL);
            }
        }
        ##############################################   
        ##EDITO EL ENCABEZADO DEL COMPROBANTE PPTAL##
        ##BUSCAR DATOS DE LA CUENTA POR PAGAR##
        $ctxd = " SELECT descripcion, numerocontrato, clasecontrato FROM gf_comprobante_pptal "
                . "WHERE id_unico = $cuentaxpagar";
        $ctxd = $mysqli->query($ctxd);
        if (mysqli_num_rows($ctxd) > 0) {
            $ctxd = mysqli_fetch_row($ctxd);
            $descripcionu = $ctxd[0];
            $numcontu = $ctxd[1];
            $clasecu = $ctxd[2];
            $updTer = "UPDATE gf_comprobante_pptal SET tercero = $tercero, descripcion = '$descripcionu', "
                    . "numerocontrato ='$numcontu', clasecontrato='$clasecu' "
                    . "WHERE id_unico = $comprobantepptal";
        } else {
            $updTer = "UPDATE gf_comprobante_pptal SET tercero = $tercero "
                    . "WHERE id_unico = $comprobantepptal";
        }
        $updTer = $mysqli->query($updTer);



        $_SESSION['id_comp_pptal_GE'] = $comprobantepptal;
        $_SESSION['idComPtal'] = $comprobantepptal;
        $_SESSION['nuevo_GE'] = 1;
        $_SESSION['terceroGuardado'] = $tercero;
        $_SESSION['comprobanteGenerado'] = $comprobantepptal;
        $_SESSION['idCompCnt'] = 0;

        echo json_encode('1');

        break;
}


    
    