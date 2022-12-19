<?php 
#####################################################################################################################################################################
#                                               MODIFICACIONES
######################################################################################################################################################################
#27/10/2017 |Erica G. |Archivo Creado
##############################################################################################################################
require_once('../Conexion/conexion.php');
require_once('../Conexion/conexionsql.php');
require_once './funcionesPptal.php';
//require('../funciones/funciones_consulta.php');
session_start();
$action = $_REQUEST['action'];
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];
$fechaE = date('Y-m-d');
$nanno = anno($anno);
switch ($action) {
    #***************Cargar Los Créditos sin desembolsar por fecha*************#
    case (1):
        $fecha = $_POST['fecha'];
        $gg = "SELECT DISTINCT CONCAT_WS(' - ',credito,descripcion), credito FROM gf_desembolsos 
               WHERE fecha = '$fecha' 
               AND numero_cdp IS  NULL 
               AND numero_registro IS  NULL 
               AND obligacion IS  NULL  ORDER BY credito DESC";
        $gg = $mysqli->query($gg);
        if (mysqli_num_rows($gg) > 0) {
            while ($row = mysqli_fetch_row($gg)) {
                echo '<option value="' . $row[1] . '">' . ucwords($row[0]) . '</option>';
            }
        } else {
            echo '<option value="">No Hay Créditos Para La Fecha</option>';
        }
    break;
    #***************Cargar Tercero*************#
    case (2):
        $credito = $_POST['credito'];
        $gg = "SELECT t.id_unico, IF(CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos) 
                IS NULL OR CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos) = '',
                (t.razonsocial),
                CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos)) AS NOMBRE,
                IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                     t.numeroidentificacion, 
                CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) AS NI 
           FROM gf_desembolsos d LEFT JOIN gf_tercero t ON d.tercero = t.id_unico
           WHERE d.credito = '".$credito."'";
        $gg = $mysqli->query($gg);
        if (mysqli_num_rows($gg) > 0) {
            while ($row = mysqli_fetch_row($gg)) {
                echo '<option value="' . $row[0] . '">' . ucwords(mb_strtolower($row[1])).' - '.$row[2] . '</option>';
            }
        } else {
            echo '<option value="">No Hay Créditos Para La Fecha</option>';
        }
    break;
    #Comprobar si todos los rubro fuente de las homologaciones tienen saldo disponible 
    case 3:
        $credito = $_POST['credito'];
        $fechaD = fechaC($_POST['fecha']);
        $datos = array();
        #***Buscan los conceptos que tengan interfaz***#
        $sql = "SELECT DISTINCT Id_Tipo_Concepto, Tipo_Aplicacion, Valor 
                FROM CONFIGURACION_TIPO_CONCEPTO_CREDITO WHERE Aplica_Desembolso =1";
        
        $stmt = sqlsrv_query( $conn, $sql );  
        $n=0;
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
            $tipo = $row['Id_Tipo_Concepto'];
            $aplicacion = $row['Tipo_Aplicacion'];
            $valor = $row['Valor'];
            #****Buscar las Clases Concepto***
            $sqlc = "SELECT Identificador, Nombre_Clase_Concepto FROM CLASE_CONCEPTO WHERE Id_Clase_Concepto ='".$tipo."'";
            $stmt1 = sqlsrv_query( $conn, $sqlc );  
            while( $row1 = sqlsrv_fetch_array( $stmt1, SQLSRV_FETCH_ASSOC) ) { 
                $conceptoc = $row1['Identificador'];
                $nombrecc = $row1['Nombre_Clase_Concepto'];
                #***Buscar El Valor dependiendo del tipo de aplicación***#
                if($aplicacion =='Cuota'){
                    $datosc = explode("-", $valor) ;
                    $ci = $datosc[0];
                    $cf = $datosc[1];
                    $sqlv = "SELECT SUM(Valor_Concepto) as valor FROM DETALLE_CREDITO 
                        WHERE Id_Concepto = '".$conceptoc."' AND Numero_Cuota BETWEEN '".$ci."' AND '".$cf."' 
                        AND Numero_Credito = '".$credito."'";
                    
                } else {
                    $sqlv = "SELECT SUM(Valor_Concepto) as valor FROM DETALLE_CREDITO 
                        WHERE Id_Concepto = '".$conceptoc."'  
                        AND Numero_Credito = '".$credito."'";
                }
                
                $stmt2 = sqlsrv_query( $conn, $sqlv );  
                $row2 = sqlsrv_fetch_array( $stmt2, SQLSRV_FETCH_ASSOC);
                $valorreg = $row2['valor'];
                #*******Buscar el Tipo De Crédito*****###
                $sqltc = "SELECT Id_Tipo_Credito FROM CREDITO WHERE Numero_Credito ='".$credito."'";
                $stmt3 = sqlsrv_query( $conn, $sqltc ); 
                $row3 = sqlsrv_fetch_array( $stmt3, SQLSRV_FETCH_ASSOC);
                $tipocred = $row3['Id_Tipo_Credito'];
                
                #*******Buscar el Concepto Financiero Asociado al Concepto Cartera*****###
                $sqlcf1 = "SELECT COUNT(gf_concepto_ds) AS num
                        FROM CONFIGURACION_PAGOS WHERE Id_Clase_Concepto ='".$conceptoc."' 
                        AND Id_Tipo_Credito = '".$tipocred."' AND ano = '$nanno' ";
                $stmt41 = sqlsrv_query( $conn, $sqlcf1 ); 
                $row41 = sqlsrv_fetch_array( $stmt41, SQLSRV_FETCH_ASSOC);
                if($row41['num']>0){
                    #*******Buscar el Concepto Financiero Asociado al Concepto Cartera*****###
                    $sqlcf = "SELECT gf_concepto_ds 
                            FROM CONFIGURACION_PAGOS WHERE Id_Clase_Concepto ='".$tipo."' 
                            AND Id_Tipo_Credito = '".$tipocred."' AND ano = '$nanno' ";
                    $stmt4 = sqlsrv_query( $conn, $sqlcf ); 
                    $row4 = sqlsrv_fetch_array( $stmt4, SQLSRV_FETCH_ASSOC);
                    $confin = $row4['gf_concepto_ds'];
                    $rfa = "SELECT rf.id_unico, 
                        CONCAT(rp.codi_presupuesto,' ', LOWER(rp.nombre), ' - ', f.id_unico, ' ', f.nombre ), 
                        c.clase_concepto 
                        FROM gf_concepto c LEFT JOIN gf_concepto_rubro cr ON c.id_unico = cr.concepto 
                        LEFT JOIN gf_rubro_pptal rp ON rp.id_unico = cr.rubro 
                        LEFT JOIN gf_rubro_fuente rf ON rf.rubro = rp.id_unico 
                        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
                        WHERE c.id_unico = '".$confin."'";
                    $rfa = $mysqli->query($rfa);
                    $rfa = mysqli_fetch_row($rfa);
                    $rf = $rfa[0];
                    if ($rfa[2] == 2) {
                        #Saldo Disponible 
                        $sd = apropiacionfecha($rf,$fechaD) - disponibilidadesfecha($rf,$fechaD);

                        $vnc = $valorreg;
                        if (($sd - $vnc) < 0) {
                            $datos[$n] = ucwords($rfa[1]);
                            $n = $n + 1;
                        }
                    }
                } else {
                    $datos[$n] = 'Concepto No Configurado:'.$nombrecc;
                    $n = $n + 1;
                }
            }
        }
        
        echo json_encode($datos);
    break;
    #Guardar Interfaz 
    case 4:
        $credito = $_POST['credito'];
        $fecha = $_POST['fecha'];
        $tercero = $_POST['tercero'];
        $tipoDis = $_POST['tipodis'];
        $numeroDis = trim($_POST['numdis']);
        $fechaDis = fechaC($_POST['fechadis']);
        $tipoReg = $_POST['tiporeg'];
        $numeroReg = trim($_POST['numreg']);
        $fechaReg = fechaC($_POST['fechareg']);
        $tipoCxp = $_POST['tipocxp'];
        $numeroCxp = trim($_POST['numcxp']);
        $fechaCxp = fechaC($_POST['fechacxp']);
        #***Fecha Vencimiento***#
        $fechaVenDis = fechaSum($fechaDis);
        $fechaVenReg = fechaSum($fechaReg);
        $fechaVenCxp = fechaSum($fechaCxp);
        #$descripcion = "'Desembolso Crédito N° $credito'";
        $descripcion = "'".$_POST['descripcion']."'";
        $guar = 0;
        
        ############Guardar Los Comprobantes##############
        #********Disponibilidad********#
        $insertdis = "INSERT INTO gf_comprobante_pptal  (numero, fecha, fechavencimiento, descripcion,  "
                . "parametrizacionanno, tipocomprobante, tercero, compania, usuario, fecha_elaboracion) "
                . "VALUES($numeroDis, '$fechaDis', '$fechaVenDis',$descripcion, "
                . "$anno,$tipoDis, 2, $compania, '$usuario', '$fechaE')";
        $insertdis = $mysqli->query($insertdis);
        if ($insertdis == true) {
            $guar += 1;
            #**Buscar el id de la disponibilidad**#
            $iddis = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE numero = $numeroDis AND tipocomprobante = $tipoDis";
            $iddis = $mysqli->query($iddis);
            $iddis = mysqli_fetch_row($iddis);
            $iddis = $iddis[0];
            //************Registro*********//
            $insertrep = "INSERT INTO gf_comprobante_pptal  (numero, fecha, fechavencimiento, descripcion,  "
                    . "parametrizacionanno, tipocomprobante, tercero, compania, usuario, fecha_elaboracion) "
                    . "VALUES($numeroReg, '$fechaReg', '$fechaVenReg',$descripcion, "
                    . "$anno,$tipoReg, $tercero, $compania, '$usuario', '$fechaE')";
            $insertrep = $mysqli->query($insertrep);
            if ($insertrep == true) {
                $guar += 1;
                #**Buscar el id del registro**#
                $idreg = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE numero = $numeroReg AND tipocomprobante = $tipoReg";
                $idreg = $mysqli->query($idreg);
                $idreg = mysqli_fetch_row($idreg);
                $idreg = $idreg[0];
                //*******Cuenta X Pagar*******//
                $insertcxp = "INSERT INTO gf_comprobante_pptal  (numero, fecha, fechavencimiento, descripcion,  "
                        . "parametrizacionanno, tipocomprobante, tercero, compania, usuario, fecha_elaboracion) "
                        . "VALUES($numeroCxp, '$fechaCxp', '$fechaVenCxp',$descripcion, "
                        . "$anno,$tipoCxp, $tercero, $compania, '$usuario', '$fechaE')";
                $insertcxp = $mysqli->query($insertcxp);
                if ($insertcxp == true) {
                    $guar += 1;
                    #**Buscar el id de al cuenta por pagar**#
                    $idcxp = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE numero = $numeroCxp AND tipocomprobante = $tipoCxp";
                    $idcxp = $mysqli->query($idcxp);
                    $idcxp = mysqli_fetch_row($idcxp);
                    $idcxp = $idcxp[0];
                    //********Cuenta X Pagar CNT*****//
                    #Buscr el tipo de comprobante cnt del pptal
                    $tcn = "SELECT id_unico FROM gf_tipo_comprobante WHERE comprobante_pptal = $tipoCxp";
                    $tcn = $mysqli->query($tcn);
                    if (mysqli_num_rows($tcn) > 0) {
                        $tcn = mysqli_fetch_row($tcn);
                        $tipoCxpCnt = $tcn[0];
                        $insertcxpcnt = "INSERT INTO gf_comprobante_cnt  (numero, fecha,  descripcion,  "
                                . "parametrizacionanno, tipocomprobante, tercero, compania, usuario, fecha_elaboracion) "
                                . "VALUES($numeroCxp, '$fechaCxp', $descripcion, "
                                . "$anno,$tipoCxpCnt, $tercero, $compania, '$usuario', '$fechaE')";
                        $insertcxpcnt = $mysqli->query($insertcxpcnt);
                        #**Buscar el id de al cuenta por pagar**#
                        $idcxpcnt = "SELECT MAX(id_unico) FROM gf_comprobante_cnt WHERE numero = $numeroCxp AND tipocomprobante = $tipoCxpCnt";
                        $idcxpcnt = $mysqli->query($idcxpcnt);
                        $idcxpcnt = mysqli_fetch_row($idcxpcnt);
                        $idcxpcnt = $idcxpcnt[0];
                    } else {
                        #**Borrar disponibilidad**#
                        $deldis = "DELETE FROM gf_comprobante_pptal WHERE id_unico = $iddis";
                        $deldis = $mysqli->query($deldis);
                        $guar -= 1;
                        #**Borrar Registro**#
                        $delreg = "DELETE FROM gf_comprobante_pptal WHERE id_unico = $idreg";
                        $delreg = $mysqli->query($delreg);
                        $guar -= 1;
                        #**Borrar Cuenta X Pagar**#
                        $delcxp = "DELETE FROM gf_comprobante_pptal WHERE id_unico = $idcxp";
                        $delcxp = $mysqli->query($delcxp);
                        $guar -= 1;
                        $result = 1;
                    }
                } else {
                    #**Borrar disponibilidad**#
                    $deldis = "DELETE FROM gf_comprobante_pptal WHERE id_unico = $iddis";
                    $deldis = $mysqli->query($deldis);
                    $guar -= 1;
                    #**Borrar Registro**#
                    $delreg = "DELETE FROM gf_comprobante_pptal WHERE id_unico = $idreg";
                    $delreg = $mysqli->query($delreg);
                    $guar -= 1;
                }
            } else {
                #**Borrar disponibilidad**#
                $deldis = "DELETE FROM gf_comprobante_pptal WHERE id_unico = $iddis";
                $deldis = $mysqli->query($deldis);
                $guar -= 1;
            }
        }
        ######Si guardo los comprobantes Guardar Detalles########
        if ($guar == 3) {
            ##**Modificarla tabla desembolsos** ##
            $insertnf = "UPDATE gf_desembolsos SET  numero_cdp= '$iddis', 
                    numero_registro = '$idreg', obligacion='$idcxp' 
                    WHERE credito = '$credito' AND tercero = '$tercero'";
            $insertnf = $mysqli->query($insertnf);
            if ($insertnf == true) {
####################################################################################################################
                #Registrar Detalles Presupuestales
                #***Buscan los conceptos que tengan interfaz***#
                $sql = "SELECT DISTINCT Id_Tipo_Concepto, Tipo_Aplicacion, Valor 
                        FROM CONFIGURACION_TIPO_CONCEPTO_CREDITO WHERE Aplica_Desembolso =1";
                $stmt = sqlsrv_query( $conn, $sql );  
                $idmax2 ='0';
                $idmax5 ='0';
                while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
                    $tipo = $row['Id_Tipo_Concepto'];
                    $aplicacion = $row['Tipo_Aplicacion'];
                    $valor = $row['Valor'];
                    #****Buscar las Clases Concepto***
                    $sqlc = "SELECT Identificador, Nombre_Clase_Concepto FROM CLASE_CONCEPTO WHERE Id_Clase_Concepto ='".$tipo."' 
                            ORDER BY Identificador ASC ";
                    $stmt1 = sqlsrv_query( $conn, $sqlc );  
                    while( $row1 = sqlsrv_fetch_array( $stmt1, SQLSRV_FETCH_ASSOC) ) { 
                        $conceptoc = $row1['Identificador'];
                        #***Buscar El Valor dependiendo del tipo de aplicación***#
                        if($aplicacion =='Cuota'){
                            $datosc = explode("-", $valor) ;
                            $ci = $datosc[0];
                            $cf = $datosc[1];
                            $sqlv = "SELECT SUM(Valor_Concepto) as valor FROM DETALLE_CREDITO 
                                WHERE Id_Concepto = '".$conceptoc."' AND Numero_Cuota BETWEEN '".$ci."' AND '".$cf."' 
                                AND Numero_Credito = '".$credito."'";

                        } else {
                            $sqlv = "SELECT SUM(Valor_Concepto) as valor FROM DETALLE_CREDITO 
                                WHERE Id_Concepto = '".$conceptoc."'  
                                AND Numero_Credito = '".$credito."'";
                        }
                        
                        $stmt2 = sqlsrv_query( $conn, $sqlv );  
                        $row2 = sqlsrv_fetch_array( $stmt2, SQLSRV_FETCH_ASSOC);
                        $valorreg = $row2['valor'];
                        if($valorreg !=0){
                            #*******Buscar el Tipo De Crédito*****###
                            $sqltc = "SELECT Id_Tipo_Credito FROM CREDITO WHERE Numero_Credito ='".$credito."'";
                            $stmt3 = sqlsrv_query( $conn, $sqltc ); 
                            $row3 = sqlsrv_fetch_array( $stmt3, SQLSRV_FETCH_ASSOC);
                            $tipocred = $row3['Id_Tipo_Credito'];
                            #*******Buscar el Concepto Financiero Asociado al Concepto Cartera*****###
                            $sqlcf = "SELECT gf_concepto_ds, descontable 
                                    FROM CONFIGURACION_PAGOS WHERE Id_Clase_Concepto ='".$conceptoc."' 
                                    AND Id_Tipo_Credito = '".$tipocred."' AND ano = '$nanno'";
                            $stmt4 = sqlsrv_query( $conn, $sqlcf ); 
                            $row4 = sqlsrv_fetch_array( $stmt4, SQLSRV_FETCH_ASSOC);
                            $confin = $row4['gf_concepto_ds'];
                            $descon = $row4['descontable'];

                            $rfa = "SELECT rf.id_unico, cr.id_unico, 
                                c.clase_concepto 
                                FROM gf_concepto c 
                                LEFT JOIN gf_concepto_rubro cr ON c.id_unico = cr.concepto 
                                LEFT JOIN gf_rubro_pptal rp ON rp.id_unico = cr.rubro 
                                LEFT JOIN gf_rubro_fuente rf ON rf.rubro = rp.id_unico 
                                LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
                                WHERE c.id_unico = '".$confin."'";
                            $rfa = $mysqli->query($rfa);
                            $rfa = mysqli_fetch_row($rfa);
                            $rf = $rfa[0];
                            $cr = $rfa[1];
                            #***Si el concepto es de tipo 2 Guarda Presupuesto*****#
                            $vnc = $valorreg;
                            if ($rfa[2] == 2 ) {
                                $sd = apropiacion($rf) - disponibilidades($rf);
                                if ($vnc > 0) {
                                ######Insertar Detalle Disponibilidad#######
                                $insertddis = "INSERT INTO gf_detalle_comprobante_pptal "
                                        . "(descripcion, valor, comprobantepptal, rubrofuente, "
                                        . "conceptoRubro, tercero, proyecto,  saldo_disponible) "
                                        . "VALUES ($descripcion, $vnc, $iddis, $rf, "
                                        . "$cr, 2,  2147483647, $sd)";
                                $insertddis = $mysqli->query($insertddis);
                                $ddis = "SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $iddis";
                                $ddis = $mysqli->query($ddis);
                                $ddis = mysqli_fetch_row($ddis);
                                $ddis = $ddis[0];
                                #*******************************************#
                                ########Insertar Detalle Registro#########
                                $insertdreg = "INSERT INTO gf_detalle_comprobante_pptal (descripcion, valor, comprobantepptal, rubrofuente, conceptoRubro, "
                                        . "tercero, proyecto, comprobanteafectado)"
                                        . "VALUES ($descripcion, $vnc, $idreg, $rf, "
                                        . "$cr, $tercero,  2147483647, $ddis)";
                                $insertdreg = $mysqli->query($insertdreg);
                                $dreg = "SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $idreg";
                                $dreg = $mysqli->query($dreg);
                                $dreg = mysqli_fetch_row($dreg);
                                $dreg = $dreg[0];
                                #*******************************************#
                               
                                $insertdcxp = "INSERT INTO gf_detalle_comprobante_pptal (descripcion, valor, comprobantepptal, rubrofuente, conceptoRubro, "
                                        . "tercero, proyecto, comprobanteafectado)"
                                        . "VALUES ($descripcion, $vnc, $idcxp, $rf, "
                                        . "$cr, $tercero,  2147483647, $dreg)";
                                $insertdcxp = $mysqli->query($insertdcxp);
                                $dcxp = "SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $idcxp";
                                $dcxp = $mysqli->query($dcxp);
                                $dcxp = mysqli_fetch_row($dcxp);
                                $dcxp = $dcxp[0];

                                #**************************************************************************************************************#
                                ####*******************INSERTAR DETALLES CNT ***************###########
                                #Buscar cuenta debito y credito
                                $cdc = "SELECT crc.cuenta_debito, cd.naturaleza,  
                                            crc.cuenta_credito, cc.naturaleza, crc.tercero_seguro  
                                        FROM 
                                            gf_concepto_rubro cr 
                                        LEFT JOIN 
                                            gf_concepto_rubro_cuenta crc ON cr.id_unico = crc.concepto_rubro 
                                        LEFT JOIN 
                                            gf_cuenta cd ON crc.cuenta_debito = cd.id_unico 
                                        LEFT JOIN 
                                            gf_cuenta cc On crc.cuenta_credito = cc.id_unico 
                                        WHERE cr.id_unico = $cr ";
                                $cdc = $mysqli->query($cdc);
                                $cdc = mysqli_fetch_row($cdc);
                                $cuentad = $cdc[0]; #*Cuenta Débito
                                $cuentac = $cdc[2]; #*Cuenta Crédito
                                #**********************************************************************************************************************************************
                                ###Valor por naturaleza
                                $valordebito = 0;
                                $valorcredito = 0;
                                #Débito
                                if ($cdc[1] == 1) {
                                    $valordebito = $vnc;
                                } else {
                                    $valordebito = $vnc * -1;
                                }
                                #Crédito
                                if ($cdc[3] == 1) {
                                    $valorcredito = $vnc * -1;
                                } else {
                                    $valorcredito = $vnc;
                                }
                                if($descon==true){
                                    #Buscar el Tercero
                                    if(isset($cdc[4])){
                                        $tercero = $cdc[4];
                                    }
                                    ######Insertar Detalle Cnt Crédito#######
                                    $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                            . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                            . "cuenta, naturaleza, tercero,  proyecto, detallecomprobantepptal) "
                                            . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vnc, $idcxpcnt,  "
                                            . "$cuentac, $cdc[3], $tercero, 2147483647,$dcxp)";
                                    $insertcntd = $mysqli->query($insertcntd);
                                } else {
                                    ######Insertar Detalle Cnt Débito#######
                                    $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                            . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                            . "cuenta, naturaleza, tercero,  proyecto,detallecomprobantepptal) "
                                            . "VALUES ('$fechaCxp',$descripcion, $valordebito,$vnc, $idcxpcnt,  "
                                            . "$cuentad, $cdc[1],$tercero, 2147483647,$dcxp)";
                                    $insertcntd = $mysqli->query($insertcntd);

                                    ######Insertar Detalle Cnt Crédito#######
                                    $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                            . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                            . "cuenta, naturaleza, tercero,  proyecto, detallecomprobantepptal) "
                                            . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vnc, $idcxpcnt,  "
                                            . "$cuentac, $cdc[3], $tercero, 2147483647,$dcxp)";
                                    $insertcntd = $mysqli->query($insertcntd);
                                }
                                if($descon==false){
                                    #####Buscar El Id Insertado   
                                      $idmax2q = "SELECT MAX(id_unico) FROM gf_detalle_comprobante 
                                                WHERE comprobante = $idcxpcnt AND cuenta = $cuentac  ";
                                      $idmax2q = $mysqli->query($idmax2q);
                                      $idmax2q = mysqli_fetch_row($idmax2q);
                                      $idmax2 .=','.$idmax2q[0];
                                } elseif($descon==true){
                                      #####Buscar El Id Insertado   
                                      $idmax5q = "SELECT MAX(id_unico) FROM gf_detalle_comprobante 
                                                WHERE comprobante = $idcxpcnt AND cuenta = $cuentac  ";
                                      $idmax5q = $mysqli->query($idmax5q);
                                      $idmax5q = mysqli_fetch_row($idmax5q);
                                      $idmax5 .= ','.$idmax5q[0];
                                }
                            }
                            } elseif($rfa[2]==3 || $rfa[2]==1){  
                                 ####*******************INSERTAR DETALLES CNT ***************###########
                                #Buscar cuenta debito y credito
                                $cdc = "SELECT crc.cuenta_debito, cd.naturaleza,  
                                        crc.cuenta_credito, cc.naturaleza , crc.tercero_seguro 
                                    FROM 
                                        gf_concepto_rubro cr 
                                    LEFT JOIN 
                                        gf_concepto_rubro_cuenta crc ON cr.id_unico = crc.concepto_rubro 
                                    LEFT JOIN 
                                        gf_cuenta cd ON crc.cuenta_debito = cd.id_unico 
                                    LEFT JOIN 
                                        gf_cuenta cc On crc.cuenta_credito = cc.id_unico 
                                    WHERE cr.id_unico = $cr ";
                                $cdc = $mysqli->query($cdc);
                                $cdc = mysqli_fetch_row($cdc);
                                $cuentad = $cdc[0]; #*Cuenta Débito
                                $cuentac = $cdc[2]; #*Cuenta Crédito
                                #**********************************************************************************************************************************************
                                ###Valor por naturaleza
                                $valordebito = 0;
                                $valorcredito = 0;
                                #Débito
                                if ($cdc[1] == 1) {
                                    $valordebito = $vnc;
                                } else {
                                    $valordebito = $vnc * -1;
                                }
                                #Crédito
                                if ($cdc[3] == 1) {
                                    $valorcredito = $vnc * -1;
                                } else {
                                    $valorcredito = $vnc;
                                }

                                if($descon==true){
                                        #Buscar el Tercero
                                        if(isset($cdc[4])){
                                            $tercero = $cdc[4];
                                        }
                                        ######Insertar Detalle Cnt Crédito#######
                                        $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                                . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                . "cuenta, naturaleza, tercero,  proyecto) "
                                                . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vnc, $idcxpcnt,  "
                                                . "$cuentac, $cdc[3], $tercero, 2147483647)";
                                        $insertcntd = $mysqli->query($insertcntd);
                                } else { 
                                    ######Insertar Detalle Cnt Débito#######
                                    $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                            . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                            . "cuenta, naturaleza, tercero,  proyecto) "
                                            . "VALUES ('$fechaCxp',$descripcion, $valordebito,$vnc, $idcxpcnt,  "
                                            . "$cuentad, $cdc[1],$tercero, 2147483647)";
                                    $insertcntd = $mysqli->query($insertcntd);

                                    ######Insertar Detalle Cnt Crédito#######
                                    $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                            . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                            . "cuenta, naturaleza, tercero,  proyecto) "
                                            . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vnc, $idcxpcnt,  "
                                            . "$cuentac, $cdc[3], $tercero, 2147483647)";
                                    $insertcntd = $mysqli->query($insertcntd);
                                }
                                if($descon==false){
                                    #####Buscar El Id Insertado   
                                    $idmax2q = "SELECT MAX(id_unico) FROM gf_detalle_comprobante 
                                              WHERE comprobante = $idcxpcnt AND cuenta = $cuentac  ";
                                    $idmax2q = $mysqli->query($idmax2q);
                                    $idmax2q = mysqli_fetch_row($idmax2q);
                                    $idmax2 .= ','.$idmax2q[0];
                                } elseif($descon==true){
                                    #####Buscar El Id Insertado   
                                    $idmax5q = "SELECT MAX(id_unico) FROM gf_detalle_comprobante 
                                              WHERE comprobante = $idcxpcnt AND cuenta = $cuentac  ";
                                    $idmax5q = $mysqli->query($idmax5q);
                                    $idmax5q = mysqli_fetch_row($idmax5q);
                                    $idmax5 .=','.$idmax5q[0];
                                }
                            }   
                        }
                    }
                }
               
                if($idmax5 !='0' && $idmax2 !='0') { 
                    #************Buscar El Valor Del ID de clase 5 Y restarselo al De Clase 2****#
                    $sql5 = "SELECT SUM(if(valor<0,valor*-1, valor)) FROM gf_detalle_comprobante WHERE id_unico IN ($idmax5)";
                    $sql5 = $mysqli->query($sql5);
                    $sql5 = mysqli_fetch_row($sql5);
                    $valor5 = $sql5[0];
                    $sql2 = "SELECT SUM(if(valor<0,valor*-1, valor)), naturaleza FROM gf_detalle_comprobante WHERE id_unico IN($idmax2)";
                    $sql2 = $mysqli->query($sql2);
                    $sql2 = mysqli_fetch_row($sql2);
                    $valor2 = $sql2[0];
                    $naturaleza = $sql2[1];
                    #Actualizar 2
                    $valorac = $valor2-$valor5;
                    if($naturaleza ==1){
                        $valorac = $valorac*-1;
                    } else {
                        $valorac = $valorac;
                    }
                    $upd2 = "UPDATE gf_detalle_comprobante SET valor = $valorac WHERE id_unico IN($idmax2)";
                    $upd2 = $mysqli->query($upd2);
                    
                    //Insertar Retenciones
                    $sqlR = "SELECT dc.id_unico, tr.id_unico, (if(dc.valor<0,dc.valor*-1, dc.valor)) , 
                        dc.comprobante, tr.porcentajeaplicar, dc.cuenta 
                        FROM gf_detalle_comprobante dc 
                        LEFT JOIN gf_tipo_retencion tr ON dc.cuenta = tr.cuenta 
                        WHERE dc.id_unico IN ($idmax5)";
                    $sqlR = $mysqli->query($sqlR);
                    if(mysqli_num_rows($sqlR)>0){
                        while ($row5 = mysqli_fetch_row($sqlR)) {
                            $vb= ROUND((100/$row5[4])*$row5[2]);
                            $sqlRetencion = "INSERT INTO gf_retencion 
                                (valorretencion, retencionbase, porcentajeretencion, 
                                comprobanteretencion, cuentadescuentoretencion, tiporetencion, comprobante)  
				VALUES($row5[2], $vb, $row5[4], $idcxp, $row5[5], $row5[1], $row5[3])";
                            $resultado = $mysqli->query($sqlRetencion);
                        }
                        crear_pptal_retencion($idcxpcnt);
                    }
                }
               
            } else {
                #**Borrar disponibilidad**#
                $deldis = "DELETE FROM gf_comprobante_pptal WHERE id_unico = $iddis";
                $deldis = $mysqli->query($deldis);
                $guar -= 1;
                #**Borrar Registro**#
                $delreg = "DELETE FROM gf_comprobante_pptal WHERE id_unico = $idreg";
                $delreg = $mysqli->query($delreg);
                $guar -= 1;
                #**Borrar Cuenta X Pagar**#
                $delcxp = "DELETE FROM gf_comprobante_pptal WHERE id_unico = $idcxp";
                $delcxp = $mysqli->query($delcxp);
                $guar -= 1;
            }
        }
        $result = $guar;

        if ($result == 3) {
            #***Buscar Id de Desembolso
            $sql = "SELECT id_unico FROM gf_desembolsos WHERE credito = $credito AND tercero = $tercero";
            $sql = $mysqli->query($sql);
            $sql = mysqli_fetch_row($sql);
            $id = $sql[0];
            #**Actualizar gf_desembolso descripcion;
            $updd = "UPDATE gf_desembolsos SET descripcion = $descripcion WHERE id_unico = $id";
            $updd = $mysqli->query($updd);
                
            $result = "GF_DESEMBOLSOS.php?id=".md5($id);
        } elseif ($result == 1) {
            $result = 1;
        } else {
            $result = 0;
        }
        echo ($result);
    break;
}
            