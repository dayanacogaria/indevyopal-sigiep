<?php
###################################################################################################################################
#     **************************************   MODIFICACIONES **************************************
###################################################################################################################################
#08/02/2017 | Erica G. | Cuentas y saldos Vigencias Anteriores
#06/10/2017 | Erica González | Saldo débito y ccrédito
#26/07/2017 | Erica González | Validar las consultas que no estaban tomando todos los datos que necesitaba, se incluyeron los campos débito y crédito y cierre
###################################################################################################################################################################
# Fecha de Creación : 				07/03/2017
# Hora Terminación de Creación  : 	10:30 a.m
# Creado por : 						Jhon Numpaque
# Descripción : 					Se creo este archivo con el fin de generar por medio de consultas el cuerpo de las tablas (datatable)
#
###################################################################################################################################################################
require_once('../Conexion/conexion.php');
require_once('../jsonSistema/funcionCierre.php');
require_once('../Conexion/ConexionPDO.php');
require_once('../jsonPptal/funcionesPptal.php');
$con = new ConexionPDO();
session_start();
$compania  = $_SESSION['compania'];
###################################################################################################################################################################
# Variable de conteo, y variable de armado de json
#
###################################################################################################################################################################
$i=0;
$tabla = "";
###################################################################################################################################################################
# Captura de variables
#
###################################################################################################################################################################
$listado = $_GET['listado'];
switch ($listado) {
    case 1:
        ###########################################################################################################################################################
        # Captura de variables
        #
	###########################################################################################################################################################
        $cuenta = $_GET['cuenta'];
        $mes = $_GET['mes'];
        $annov  = $_SESSION['anno'];

        $nannov = anno($annov);
        #Año Anterior
        $anno2 = $nannov-1;
        $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2' AND compania = $compania");
        if(count($an2)>0){
            $annova = $an2[0][0];
        } else {
            $annova = 0;
        }
        $cuentaA =0;
        $ca = $con->Listar("SELECT codi_cuenta, equivalente_va FROM gf_cuenta WHERE id_unico = $cuenta");
        $codCuenta = $ca[0][0];
        $equivalente =$ca[0][1];
        if(!empty($equivalente)){
            #echo '1'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova";
            $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova");
            if(count($ctaa)>0){
                if(!empty($ctaa[0][0])){
                    $cuentaA = $ctaa[0][0];
                }
            } else {
                #echo '2'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova";
                $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova");
                if(!empty($ctaa[0][0])){
                    $cuentaA = $ctaa[0][0];
                }
            }
        } else {
            #echo '3'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova";
            $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova");
            if(!empty($ctaa[0][0])){
                    $cuentaA = $ctaa[0][0];
                }
        }
        $cuentas =($cuentaA.','.$cuenta);
        ######################################################################################################################
        # Consulta de nombre de mes
        #
	######################################################################################################################
        #
	$sqlM = "SELECT mes FROM gf_mes WHERE id_unico = $mes";
        $resultM = $mysqli->query($sqlM);
        $noM = mysqli_fetch_row($resultM);
        ######################################################################################################################
        # Array con los numeros de los meses
        ######################################################################################################################
        $meses = array("Enero" => '01', "Febrero" => '02', "Marzo" => '03', "Abril" => '04', "Mayo" => '05', "Junio" => '06', "Julio" => '07', "Agosto" => '08', "Septiembre" => '09', "Octubre" => '10', "Noviembre" => '11', "Diciembre" => '12');
        $mess = $meses[$noM[0]];
        $calendario = CAL_GREGORIAN;
        #Consulta para obtener el año de parametrización año
        $anno = $_SESSION['anno'];
        $sqlA = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $anno";
        $resultA = $mysqli->query($sqlA);
        $rowA = mysqli_fetch_row($resultA);
        $anno = $rowA[0];
        #Fecha con el ultimo dia del mes
        $diaF = cal_days_in_month($calendario, $mess, $anno);
        $d = $anno.'-'.$mess.'-'.$diaF;
        #Fecha con el primer dia del mes
        $month = $mess;
        $year = $anno;
        $e =  $anno.'-'.$mess.'-01';
        $cierre = cierrepartidames($mes);
        if ($cierre == 1) {
            $dis = 'true';
        } else {
            $dis = 'false';
        }
        ###########################################################################################################################################################
        # Consulta de actualizción de valores, conciliado = 1, periodo_conciliado = $mes
        #############################################################################################################################################################
        $sqlC = "UPDATE
                gf_detalle_comprobante dtc
            LEFT JOIN gf_comprobante_cnt cnt ON
                cnt.id_unico = dtc.comprobante 
            LEFT JOIN gf_tipo_comprobante tc ON cnt.tipocomprobante = tc.id_unico 
            SET
                dtc.conciliado = 1,
                dtc.periodo_conciliado = $mes
            WHERE
                dtc.cuenta IN ($cuentas) 
                AND ( cnt.fecha BETWEEN '$e' AND '$d') 
                AND ( (dtc.conciliado IN(1,2) AND dtc.periodo_conciliado =$mes) OR dtc.conciliado IS NULL ) 
                AND tc.clasecontable != 5  AND dtc.valor != 0 ";
        
        $resultC = $mysqli->query($sqlC);
        $sqlC = "UPDATE
                gf_detalle_comprobante dtc
            LEFT JOIN gf_comprobante_cnt cnt ON
                cnt.id_unico = dtc.comprobante
            LEFT JOIN gf_tipo_comprobante tc ON cnt.tipocomprobante = tc.id_unico 
            SET
                dtc.conciliado = 1,
                dtc.periodo_conciliado = $mes
            WHERE
                dtc.cuenta IN($cuentas) 
                AND(cnt.fecha < '$e') 
                AND ( dtc.conciliado IS NULL OR (dtc.conciliado IN(1,2) AND dtc.periodo_conciliado =$mes)) 
                AND tc.clasecontable != 5  AND dtc.valor != 0";
        $resultC = $mysqli->query($sqlC);
        $tabla = "";
        ###########################################################################################################################################################
        # Consulta 
        #
        ############################################################################################################################################################
        $sqlD ="SELECT DISTINCT
                dtc.id_unico,
                CONCAT(tpc.sigla),
                cnt.numero,
                IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                  OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                (tr.razonsocial),
                CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
                cnt.descripcion,
                dtc.valor,
                (dtc.valor),
                dtc.conciliado,
                DATE_FORMAT(cnt.fecha,
                '%d/%m/%Y'), 
                dtc.periodo_conciliado , 
                c.naturaleza , 
                cnt.id_unico 
              FROM
                gf_detalle_comprobante dtc
              INNER JOIN
                gf_comprobante_cnt cnt ON dtc.comprobante = cnt.id_unico
              LEFT JOIN
                gf_tipo_comprobante tpc ON tpc.id_unico = cnt.tipocomprobante
              LEFT JOIN
                gf_tercero tr ON dtc.tercero = tr.id_unico 
              LEFT JOIN 
                gf_cuenta c On dtc.cuenta = c.id_unico 
              WHERE
              dtc.cuenta IN ($cuentas)
              AND ( cnt.fecha BETWEEN '$e' AND '$d')
              AND ( (dtc.conciliado IN(1,2) AND dtc.periodo_conciliado =$mes) OR dtc.conciliado IS NULL ) 
              AND tpc.clasecontable != 5 AND dtc.valor != 0
              ORDER BY
            cnt.fecha ASC";
        $resultD = $mysqli->query($sqlD);
        $filas =$resultD->num_rows;
        ############################################################################################################################################################
        # Validación para verficación de registro de datos
        #
        ############################################################################################################################################################
        if($filas>0){
                ########################################################################################################################################################
                # Armado de Json
                #
                ########################################################################################################################################################
                while ($r = mysqli_fetch_row($resultD)) {
                    ########################################################################################################################################################
                    # Busqueda Cheques y valor débito y crédito
                     $cheques =".";
                    $ch = "SELECT DISTINCT id_unico, numero "
                            . "FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $r[0]";
                    $ch = $mysqli->query($ch);
                    if(mysqli_num_rows($ch)>0){
                        while ($row = mysqli_fetch_row($ch)) {
                            if($row[1]==""){
                            
                            } else {
                                $cheques .= $row[1].'&nbsp;&nbsp;&nbsp;';
                            }
                        }
                    }
                    switch ($r[10]){
                        case 1:
                            if($r[6]>0){
                                $debito = $r[6];
                                $credito =0;
                            } else {
                                $debito = 0;
                                $credito =$r[6]*-1;
                            }

                        break;
                        case 2:
                            if($r[6]>0){
                                $debito =0;
                                $credito =$r[6];
                            } else {
                                $debito = $r[6]*-1;
                                $credito =0;
                            }
                        break;
                    }
                    ########################################################################################################################################################
                    $tabla.='{"Tipo Movimiento":"<label class=\"campos text-left\" style=\"font-weight: normal;\">'.ucwords(mb_strtoupper($r[1])).'</label>", '
                    . '"Nro Movimiento":"<label class=\"text-left\" style=\"font-weight: normal;font-size:10px\">'.$r[2].'</label style=\"font-weight: normal;font-size:10px\">", '
                    . '"Fecha":"<label style=\"font-weight: normal;font-size:10px\">'.$r[8].'</label>", '
                    . '"Tercero":"<label class=\"text-left\" style=\"font-weight: normal;font-size:10px\">'.ucwords(mb_strtolower($r[3])).'</label>",'
                    . '"Descripción":"<label class=\"text-left\" style=\"font-weight: normal;font-size:10px\">'.ucwords(mb_strtolower($r[4])).'</label>",'
                    . '"Nro Doc":"<label class=\"campos text-left\" style=\"font-weight: normal;\">'.$cheques.'</label>",'
                    . '"Débito":"<label class=\"campos text-right\" style=\"font-weight: normal;\">'.number_format($debito,2,',','.').'</label>",'
                    . '"Crédito":"<label class=\"campos text-right\" style=\"font-weight: normal;\">'.number_format($credito,2,',','.').'</label>"," '
                    . '":"<input type=\"checkbox\" name=\"chkP[]\" id=\"chkP'.$r[0].'\" class=\"campos text-right\" value=\"'.$r[0].'\"   onclick=\"return conciliado('.$r[0].')\" checked />"},';
                        $i++;
                }
        }
        ###########################################################################################################################################################
        # Consulta 2
        ############################################################################################################################################################
        $sqlD2 ="SELECT DISTINCT
                    dtc.id_unico,
                    CONCAT(tpc.sigla),
                    cnt.numero,
                    IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                      OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                    (tr.razonsocial),
                    CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
                    cnt.descripcion,
                    dtc.valor,
                    (dtc.valor),
                    dtc.conciliado,
                    DATE_FORMAT(cnt.fecha,
                    '%d/%m/%Y'), 
                    dtc.periodo_conciliado, 
                    c.naturaleza 
                  FROM
                    gf_detalle_comprobante dtc
                  INNER JOIN
                    gf_comprobante_cnt cnt ON dtc.comprobante = cnt.id_unico
                  LEFT JOIN
                    gf_tipo_comprobante tpc ON tpc.id_unico = cnt.tipocomprobante
                  LEFT JOIN
                    gf_tercero tr ON dtc.tercero = tr.id_unico
                 LEFT JOIN 
                    gf_cuenta c On dtc.cuenta = c.id_unico 
                  WHERE
                  dtc.cuenta IN ($cuentas) 
                  AND ( cnt.fecha < '$e')
                  AND ( dtc.conciliado IS NULL OR (dtc.conciliado IN(1,2) AND dtc.periodo_conciliado =$mes)) 
                  AND tpc.clasecontable != 5 AND dtc.valor != 0
                  ORDER BY
                    cnt.fecha ASC";
        $resultD2 = $mysqli->query($sqlD2);
        ############################################################################################################################################################
        # Validación para verficación de registro de datos
        #
        ############################################################################################################################################################
        if(mysqli_num_rows($resultD2)>0){
            ########################################################################################################################################################
            # Armado de Json
            #
            ########################################################################################################################################################
            while ($r = mysqli_fetch_row($resultD2)) {
                ########################################################################################################################################################
                # Busqueda Cheques y valor débito y crédito
                $cheques =".";
                $ch = "SELECT DISTINCT id_unico, numero "
                        . "FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $r[0]";
                $ch = $mysqli->query($ch);
                if(mysqli_num_rows($ch)>0){
                    while ($row = mysqli_fetch_row($ch)) {
                        if($row[1]==""){
                            
                        } else {
                            $cheques .= $row[1].'&nbsp;&nbsp;&nbsp;';
                        }
                    }
                }
                switch ($r[10]){
                    case 1:
                        if($r[6]>0){
                            $debito = $r[6];
                            $credito =0;
                        } else {
                            $debito = 0;
                            $credito =$r[6]*-1;
                        }

                    break;
                    case 2:
                        if($r[6]>0){
                            $debito =0;
                            $credito =$r[6];
                        } else {
                            $debito = $r[6]*-1;
                            $credito =0;
                        }
                    break;
                }
                ########################################################################################################################################################
                $tabla.='{"Tipo Movimiento":"<label class=\"campos text-left\" style=\"font-weight: normal;\">'.ucwords(mb_strtoupper($r[1])).'</label>", '
                . '"Nro Movimiento":"<label class=\"text-left\" style=\"font-weight: normal;font-size:10px\">'.$r[2].'</label style=\"font-weight: normal;font-size:10px\">", '
                . '"Fecha":"<label style=\"font-weight: normal;font-size:10px\">'.$r[8].'</label>", '
                . '"Tercero":"<label class=\"text-left\" style=\"font-weight: normal;font-size:10px\">'.ucwords(mb_strtolower($r[3])).'</label>",'
                . '"Descripción":"<label class=\"text-left\" style=\"font-weight: normal;font-size:10px\">'.ucwords(mb_strtolower($r[4])).'</label>",'
                . '"Nro Doc":"<label class=\"campos text-left\" style=\"font-weight: normal;\">'.$cheques.'</label>",'
                . '"Débito":"<label class=\"campos text-right\" style=\"font-weight: normal;\">'.number_format($debito,2,',','.').'</label>",'
                . '"Crédito":"<label class=\"campos text-right\" style=\"font-weight: normal;\">'.number_format($credito,2,',','.').'</label>"," '
                . '":"<input type=\"checkbox\" name=\"chkP[]\" id=\"chkP'.$r[0].'\" class=\"campos text-right\" value=\"'.$r[0].'\"  checked onclick=\"return conciliado('.$r[0].')\" />"},';
                    $i++;
            }
        }


        if ($tabla == "") {
            ########################################################################################################################################################
            # Se imprimie cuando no hay datos
            #
			########################################################################################################################################################
            echo '<tr><td class="text-center" colspan="12" class="text-center"><p>No Existen Registros...</p><td><tr/>';
        } else {
            ########################################################################################################################################################
            # Se quita la ultima ,
            #
			########################################################################################################################################################
            $tabla = substr($tabla, 0, strlen($tabla) - 1);
            ########################################################################################################################################################
            # Se imprime el data
            #
			########################################################################################################################################################
            echo '{"data":[' . $tabla . ']}';
        }
        break;
    case 2:
	###########################################################################################################################################################
        # Captura de variables
        #
	###########################################################################################################################################################
        $cuenta = $_GET['cuenta'];
        $mes = $_GET['mes'];
        $annov  = $_SESSION['anno'];
        $nannov = anno($annov);
        #Año Anterior
        $anno2 = $nannov-1;
        $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2' AND compania = $compania");
        if(count($an2)>0){
            $annova = $an2[0][0];
        } else {
            $annova = 0;
        }
        $cuentaA =0;
        $ca = $con->Listar("SELECT codi_cuenta, equivalente_va FROM gf_cuenta WHERE id_unico = $cuenta");
        $codCuenta = $ca[0][0];
        $equivalente =$ca[0][1];
        if(!empty($equivalente)){
            #echo '1'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova";
            $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova");
            if(count($ctaa)>0){
                if(!empty($ctaa[0][0])){
                    $cuentaA = $ctaa[0][0];
                }
            } else {
                #echo '2'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova";
                $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova");
                if(!empty($ctaa[0][0])){
                    $cuentaA = $ctaa[0][0];
                }
            }
        } else {
            #echo '3'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova";
            $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova");
            if(!empty($ctaa[0][0])){
                    $cuentaA = $ctaa[0][0];
                }
        }
        $cuentas =($cuentaA.','.$cuenta);
        ######################################################################################################################
        # Consulta de nombre de mes
        #
	######################################################################################################################
        #
	$sqlM = "SELECT mes FROM gf_mes WHERE id_unico = $mes";
        $resultM = $mysqli->query($sqlM);
        $noM = mysqli_fetch_row($resultM);
        ######################################################################################################################
        # Array con los numeros de los meses
        ######################################################################################################################
        $meses = array("Enero" => '01', "Febrero" => '02', "Marzo" => '03', "Abril" => '04', "Mayo" => '05', "Junio" => '06', "Julio" => '07', "Agosto" => '08', "Septiembre" => '09', "Octubre" => '10', "Noviembre" => '11', "Diciembre" => '12');
        $mess = $meses[$noM[0]];
        $calendario = CAL_GREGORIAN;
        #Consulta para obtener el año de parametrización año
        $anno = $_SESSION['anno'];
        $sqlA = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $anno";
        $resultA = $mysqli->query($sqlA);
        $rowA = mysqli_fetch_row($resultA);
        $anno = $rowA[0];
        #Fecha con el ultimo dia del mes
        $diaF = cal_days_in_month($calendario, $mess, $anno);
        $d = $anno.'-'.$mess.'-'.$diaF;
        #Fecha con el primer dia del mes
        $month = $mess;
        $year = $anno;
        $e =  $anno.'-'.$mess.'-01';
        $cierre = cierrepartidames($mes);
        if ($cierre == 1) {
            $dis = 'true';
        } else {
            $dis = 'false';
        }
        ###########################################################################################################################################################
        # Consulta de actualizción de valores, conciliado = NULL, periodo_conciliado = NULL
        #
        ############################################################################################################################################################

         $sqlC = "UPDATE
                gf_detalle_comprobante dtc
            LEFT JOIN gf_comprobante_cnt cnt ON
                cnt.id_unico = dtc.comprobante
            LEFT JOIN gf_tipo_comprobante tc ON cnt.tipocomprobante = tc.id_unico 
            SET
                dtc.conciliado = NULL,
                dtc.periodo_conciliado = NULL 
            WHERE
               dtc.cuenta IN ($cuentas) 
                AND ( cnt.fecha BETWEEN '$e' AND '$d') 
                AND ( (dtc.conciliado IN(1,2) AND dtc.periodo_conciliado =$mes) OR dtc.conciliado IS NULL ) 
                AND tc.clasecontable != 5  AND dtc.valor != 0 "; 
        $resultC = $mysqli->query($sqlC);
        $sqlC = "UPDATE
                gf_detalle_comprobante dtc
            LEFT JOIN gf_comprobante_cnt cnt ON
                cnt.id_unico = dtc.comprobante
            LEFT JOIN gf_tipo_comprobante tc ON cnt.tipocomprobante = tc.id_unico 
            SET
                dtc.conciliado = NULL,
                dtc.periodo_conciliado = NULL 
            WHERE
                dtc.cuenta IN ($cuentas)  AND(cnt.fecha < '$e') 
                AND(dtc.conciliado IS NULL 
                OR(dtc.conciliado IN(1, 2) AND dtc.periodo_conciliado = $mes)) 
                AND tc.clasecontable != 5 AND dtc.valor != 0";
        $resultC = $mysqli->query($sqlC); 
        $tabla ="";
       
        ###########################################################################################################################################################
        # Consulta 
        #
        ############################################################################################################################################################
        $sqlD ="SELECT DISTINCT
                dtc.id_unico,
                CONCAT(tpc.sigla),
                cnt.numero,
                IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                  OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                (tr.razonsocial),
                CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
                cnt.descripcion,
                dtc.valor,
                (dtc.valor),
                dtc.conciliado,
                DATE_FORMAT(cnt.fecha,
                '%d/%m/%Y'), 
                dtc.periodo_conciliado , 
                c.naturaleza , 
                cnt.id_unico 
              FROM
                gf_detalle_comprobante dtc
              INNER JOIN
                gf_comprobante_cnt cnt ON dtc.comprobante = cnt.id_unico
              LEFT JOIN
                gf_tipo_comprobante tpc ON tpc.id_unico = cnt.tipocomprobante
              LEFT JOIN
                gf_tercero tr ON dtc.tercero = tr.id_unico 
              LEFT JOIN 
                gf_cuenta c On dtc.cuenta = c.id_unico 
              WHERE
              dtc.cuenta IN ($cuentas) 
              AND ( cnt.fecha BETWEEN '$e' AND '$d')
              AND ( (dtc.conciliado IN(1,2) AND dtc.periodo_conciliado =$mes) OR dtc.conciliado IS NULL ) 
              AND tpc.clasecontable != 5 AND dtc.valor != 0
              ORDER BY
            cnt.fecha ASC";
        $resultD = $mysqli->query($sqlD);
        $filas =$resultD->num_rows;
        ############################################################################################################################################################
        # Validación para verficación de registro de datos
        #
        ############################################################################################################################################################
        if($filas>0){
                ########################################################################################################################################################
                # Armado de Json
                #
                ########################################################################################################################################################
                while ($r = mysqli_fetch_row($resultD)) {
                    ########################################################################################################################################################
                    # Busqueda Cheques y valor débito y crédito
                    $cheques ="";
                    $ch = "SELECT DISTINCT id_unico, numero "
                            . "FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $r[0]";
                    $ch = $mysqli->query($ch);
                    if(mysqli_num_rows($ch)>0){
                        while ($row = mysqli_fetch_row($ch)) {
                            if($row[1]==""){
                            
                            } else {
                                $cheques .= $row[1].'&nbsp;&nbsp;&nbsp;';
                            }
                        }
                    } else {
                         $cheques ="-";
                    }
                    switch ($r[10]){
                        case 1:
                            if($r[6]>0){
                                $debito = $r[6];
                                $credito =0;
                            } else {
                                $debito = 0;
                                $credito =$r[6]*-1;
                            }

                        break;
                        case 2:
                            if($r[6]>0){
                                $debito =0;
                                $credito =$r[6];
                            } else {
                                $debito = $r[6]*-1;
                                $credito =0;
                            }
                        break;
                    }
                    ########################################################################################################################################################
                    $tabla.='{"Tipo Movimiento":"<label class=\"campos text-left\" style=\"font-weight: normal;\">'.ucwords(mb_strtoupper($r[1])).'</label>", '
                    . '"Nro Movimiento":"<label class=\"text-left\" style=\"font-weight: normal;font-size:10px\">'.$r[2].'</label style=\"font-weight: normal;font-size:10px\">", '
                    . '"Fecha":"<label style=\"font-weight: normal;font-size:10px\">'.$r[8].'</label>", '
                    . '"Tercero":"<label class=\"text-left\" style=\"font-weight: normal;font-size:10px\">'.ucwords(mb_strtolower($r[3])).'</label>",'
                    . '"Descripción":"<label class=\"text-left\" style=\"font-weight: normal;font-size:10px\">'.ucwords(mb_strtolower($r[4])).'</label>",'
                    . '"Nro Doc":"<label class=\"campos text-left\" style=\"font-weight: normal;\">'.$cheques.'</label>",'
                    . '"Débito":"<label class=\"campos text-right\" style=\"font-weight: normal;\">'.number_format($debito,2,',','.').'</label>",'
                    . '"Crédito":"<label class=\"campos text-right\" style=\"font-weight: normal;\">'.number_format($credito,2,',','.').'</label>"," '
                    . '":"<input type=\"checkbox\" name=\"chkP[]\" id=\"chkP'.$r[0].'\" class=\"campos text-right\" value=\"'.$r[0].'\" onclick=\"return conciliado('.$r[0].')\" />"},';
                        $i++;
                }
        }
        ###########################################################################################################################################################
        # Consulta 2
        ############################################################################################################################################################
        $sqlD2 ="SELECT DISTINCT
                    dtc.id_unico,
                    CONCAT(tpc.sigla),
                    cnt.numero,
                    IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                      OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                    (tr.razonsocial),
                    CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
                    cnt.descripcion,
                    dtc.valor,
                    (dtc.valor),
                    dtc.conciliado,
                    DATE_FORMAT(cnt.fecha,
                    '%d/%m/%Y'), 
                    dtc.periodo_conciliado, 
                    c.naturaleza 
                  FROM
                    gf_detalle_comprobante dtc
                  INNER JOIN
                    gf_comprobante_cnt cnt ON dtc.comprobante = cnt.id_unico
                  LEFT JOIN
                    gf_tipo_comprobante tpc ON tpc.id_unico = cnt.tipocomprobante
                  LEFT JOIN
                    gf_tercero tr ON dtc.tercero = tr.id_unico
                 LEFT JOIN 
                    gf_cuenta c On dtc.cuenta = c.id_unico 
                  WHERE
                  dtc.cuenta IN ($cuentas)  
                  AND ( cnt.fecha < '$e')
                  AND ( dtc.conciliado IS NULL OR (dtc.conciliado IN(1,2) AND dtc.periodo_conciliado =$mes)) 
                  AND tpc.clasecontable != 5 AND dtc.valor != 0
                  ORDER BY
                    cnt.fecha ASC";
        $resultD2 = $mysqli->query($sqlD2);
        ############################################################################################################################################################
        # Validación para verficación de registro de datos
        #
        ############################################################################################################################################################
        if(mysqli_num_rows($resultD2)>0){
            ########################################################################################################################################################
            # Armado de Json
            #
            ########################################################################################################################################################
            while ($r = mysqli_fetch_row($resultD2)) {
                ########################################################################################################################################################
                # Busqueda Cheques y valor débito y crédito
                $cheques ="";
                $ch = "SELECT DISTINCT id_unico, numero 
                      FROM gf_detalle_comprobante_mov WHERE comprobantecnt = '$r[0]'";
                $ch = $mysqli->query($ch);
                if(mysqli_num_rows($ch)>0){
                    while ($row = mysqli_fetch_row($ch)) {
                        if($row[1]==""){
                            
                        } else {
                        $cheques .= $row[1].'&nbsp;&nbsp;&nbsp;';
                        }
                    }
                } else {
                    $cheques ="-";
                }
                switch ($r[10]){
                    case 1:
                        if($r[6]>0){
                            $debito = $r[6];
                            $credito =0;
                        } else {
                            $debito = 0;
                            $credito =$r[6]*-1;
                        }

                    break;
                    case 2:
                        if($r[6]>0){
                            $debito =0;
                            $credito =$r[6];
                        } else {
                            $debito = $r[6]*-1;
                            $credito =0;
                        }
                    break;
                }
                ########################################################################################################################################################
                $tabla.='{"Tipo Movimiento":"<label class=\"campos text-left\" style=\"font-weight: normal;\">'.ucwords(mb_strtoupper($r[1])).'</label>", '
                . '"Nro Movimiento":"<label class=\"text-left\" style=\"font-weight: normal;font-size:10px\">'.$r[2].'</label style=\"font-weight: normal;font-size:10px\">", '
                . '"Fecha":"<label style=\"font-weight: normal;font-size:10px\">'.$r[8].'</label>", '
                . '"Tercero":"<label class=\"text-left\" style=\"font-weight: normal;font-size:10px\">'.ucwords(mb_strtolower($r[3])).'</label>",'
                . '"Descripción":"<label class=\"text-left\" style=\"font-weight: normal;font-size:10px\">'.ucwords(mb_strtolower($r[4])).'</label>",'
                . '"Nro Doc":"<label class=\"campos text-left\" style=\"font-weight: normal;\">'.$cheques.'</label>",'
                . '"Débito":"<label class=\"campos text-right\" style=\"font-weight: normal;\">'.number_format($debito,2,',','.').'</label>",'
                . '"Crédito":"<label class=\"campos text-right\" style=\"font-weight: normal;\">'.number_format($credito,2,',','.').'</label>"," '
                . '":"<input type=\"checkbox\" name=\"chkP[]\" id=\"chkP'.$r[0].'\" class=\"campos text-right\" value=\"'.$r[0].'\" onclick=\"return conciliado('.$r[0].')\" />"},';
                    $i++;
            }
        }
        if($tabla ==""){
                ########################################################################################################################################################
                # Se imprimie cuando no hay datos
                #
                ########################################################################################################################################################
                echo '<tr><td class="text-center" colspan="12" class="text-center"><p>No Existen Registros...</p><td><tr/>';
        } else {
                ########################################################################################################################################################
                # Se quita la ultima ,
                #
                ########################################################################################################################################################
                $tabla = substr($tabla,0,strlen($tabla)-1);
                ########################################################################################################################################################
                # Se imprime el data
                #
                ########################################################################################################################################################
                echo '{"data":['.$tabla.']}';
        }
        break;
}
 ?>