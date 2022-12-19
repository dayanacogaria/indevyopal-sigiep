    <?php 
#####################################################################################################################################################################
#                                               MODIFICACIONES
######################################################################################################################################################################
#16/01/2019 |Erica G. | Parametrizacionanno
#15/09/2017 |ERICA G. | EGRESO PARA INTERFAZ DE NOMINA
#24/07/2017 |Erica G. |Archivo Creado
##############################################################################################################################
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
require_once './funcionesPptal.php';
session_start();
$action     = $_REQUEST['action'];
$anno       = $_SESSION['anno'];
$panno      = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$fechaE     = date('Y-m-d');
$con        = new ConexionPDO();
#*** Buscar Tercero Por Compania ***#
$tc = $con->Listar("SELECT * FROM gf_tercero WHERE numeroidentificacion ='9999999999' AND compania = $compania");
$tercero = $tc[0][0];
#*** Buscar Centro Costo Por Año ***#
$cc = $con->Listar("SELECT * FROM gf_centro_costo
    WHERE nombre ='Varios' AND parametrizacionanno = $anno");
$centro_costo = $cc[0][0];
switch ($action) {
    ############REGISTRAR HOMOLOGACIÓN#############
    case (1):
        $conceptoN = $_POST['conceptoN'];
        $conceptoF = $_POST['conceptoF'];

        if (empty($_POST['rubroF'])) {
            $rubroF = 'NULL';
        } else {
            $rubroF = $_POST['rubroF'];
        }

        $grupoG = $_POST['grupoG'];
        if (empty($_POST['tercero'])) {
            $tercero = 'NULL';
        } else {
            $tercero = $_POST['tercero'];
        }
        
        if (empty($_POST['tipo'])) {
            $tipo = 'NULL';
        } else {
            $tipo = $_POST['tipo'];
        }
        $insert = "INSERT INTO gn_concepto_nomina_financiero (concepto_nomina, concepto_financiero, rubro_fuente, "
                . "grupo_gestion, tercero, parametrizacionanno, tipo) "
                . "VALUES ($conceptoN, $conceptoF, $rubroF, $grupoG, $tercero, $anno, $tipo )";
        $r = $mysqli->query($insert);

        echo json_decode($r);
    break;
    ############ELIMINAR HOMOLOGACIÓN#############
    case (2):
        $id = $_POST['id'];
        $delete = "DELETE FROM  gn_concepto_nomina_financiero WHERE id_unico = $id";
        $r = $mysqli->query($delete);
        echo json_decode($r);
    break;
    ############MODIFICAR HOMOLOGACIÓN#############
    case (3):
        $id = $_POST['id'];
        $conceptoN = $_POST['conceptoN'];
        $conceptoF = $_POST['conceptoF'];
        if (empty($_POST['rubroF'])) {
            $rubroF = 'NULL';
        } else {
            $rubroF = $_POST['rubroF'];
        }
        $grupoG = $_POST['grupoG'];
        if (empty($_POST['tercero'])) {
            $tercero = 'NULL';
        } else {
            $tercero = $_POST['tercero'];
        }
        if (empty($_POST['tipo'])) {
            $tipo = 'NULL';
        } else {
            $tipo = $_POST['tipo'];
        }
        $insert = "UPDATE gn_concepto_nomina_financiero SET concepto_nomina=$conceptoN, "
                . "concepto_financiero=$conceptoF, "
                . "rubro_fuente = $rubroF, "
                . "grupo_gestion=$grupoG, "
                . "tercero=$tercero, "
                . "tipo = $tipo "
                . "WHERE id_unico = $id";
        $r = $mysqli->query($insert);
        echo json_decode($r);
    break;
    ###########Grupos de gestión de ese periodo########
    case (4):
        $periodo = $_POST['periodo'];
        $gg = "SELECT DISTINCT g.id_unico, LOWER(g.nombre) FROM gn_novedad n 
        INNER JOIN gn_empleado e ON n.empleado = e.id_unico 
        INNER JOIN gn_grupo_gestion g ON e.grupogestion = g.id_unico 
        WHERE n.periodo = $periodo";
        $gg = $mysqli->query($gg);
        if (mysqli_num_rows($gg) > 0) {
            while ($row = mysqli_fetch_row($gg)) {
                echo '<option value="' . $row[0] . '">' . ucwords($row[1]) . '</option>';
            }
        } else {
            echo '<option value="">No hay grupos de gestión para este periodo</option>';
        }
    break;
    ###########Validar si todos los conceptos de nómina del periodo estan homologados########
    case (5):
        $grupoGestion = $_POST['gg'];
        $periodo      = $_POST['periodo'];
        $n = 0;
        #Busca los conceptos nomina por periodo y grupo de gestión
        $cn = "SELECT  DISTINCT n.concepto , CONCAT(cn.codigo,' - ',LOWER(cn.descripcion))  
                    FROM gn_novedad n 
                    LEFT JOIN gn_empleado e ON e.id_unico = n.empleado 
                    LEFT JOIN gn_concepto cn ON n.concepto = cn.id_unico
                    WHERE n.periodo = $periodo AND e.grupogestion = $grupoGestion AND cn.unidadmedida=1 
                    AND n.valor !=0 AND (cn.clase =1 OR cn.clase =2 OR cn.clase = 7 OR cn.clase= 5)";
        $cn = $mysqli->query($cn);
        $datos = array();
        while ($row = mysqli_fetch_row($cn)) {
            #Busca si el concepto tienen homologación
            $conc = $row[0];
            $ncon = ucwords($row[1]);
            $cf = "SELECT * FROM gn_concepto_nomina_financiero cnf   
                    WHERE cnf.concepto_nomina = $conc 
                    AND cnf.parametrizacionanno = $anno 
                    AND cnf.grupo_gestion = $grupoGestion";
            $cf = $mysqli->query($cf);
            if (mysqli_num_rows($cf) > 0) {
                
            } else {
                $datos[$n] = $ncon;
                $n = $n + 1;
            }
        }
        echo json_encode($datos);
    break;
    ##########Cargar conceptos de financiera, según la nómina##########
    case (6):
        $concepto = $_POST['concepto'];
        #buscamos la clase concepto 
        $cc = "SELECT clase FROM gn_concepto WHERE id_unico = $concepto";
        $cc = $mysqli->query($cc);
        $cc = mysqli_fetch_row($cc);
        $cc = $cc[0];
        switch ($cc) {
            case (1):
                $conf = "SELECT cr.id_unico, CONCAT(LOWER(c.nombre),' - ', rp.codi_presupuesto, ' ', LOWER(rp.nombre)) FROM gf_concepto_rubro cr "
                        . "LEFT JOIN gf_concepto c ON c.id_unico = cr.concepto "
                        . "LEFT JOIN gf_rubro_pptal rp ON cr.rubro = rp.id_unico "
                        . " WHERE c.clase_concepto =2 AND c.parametrizacionanno = $anno";
                break;
            case (7):
                $conf = "SELECT cr.id_unico, CONCAT(LOWER(c.nombre),' - ', rp.codi_presupuesto, ' ', LOWER(rp.nombre)) FROM gf_concepto_rubro cr "
                        . "LEFT JOIN gf_concepto c ON c.id_unico = cr.concepto "
                        . "LEFT JOIN gf_rubro_pptal rp ON cr.rubro = rp.id_unico "
                        . " WHERE c.clase_concepto =2 AND c.parametrizacionanno = $anno";
                break;
            case (5):
                $conf = "SELECT cr.id_unico, CONCAT(LOWER(c.nombre),' - ', rp.codi_presupuesto, ' ', LOWER(rp.nombre)) FROM gf_concepto_rubro cr "
                        . "LEFT JOIN gf_concepto c ON c.id_unico = cr.concepto "
                        . "LEFT JOIN gf_rubro_pptal rp ON cr.rubro = rp.id_unico "
                        . " WHERE c.clase_concepto =3 AND c.parametrizacionanno = $anno";
                break;
            case (2):
                $conf = "SELECT cr.id_unico, CONCAT(LOWER(c.nombre),' - ', rp.codi_presupuesto, ' ', LOWER(rp.nombre)) FROM gf_concepto_rubro cr "
                        . "LEFT JOIN gf_concepto c ON c.id_unico = cr.concepto "
                        . "LEFT JOIN gf_rubro_pptal rp ON cr.rubro = rp.id_unico "
                        . " WHERE c.clase_concepto =3 AND c.parametrizacionanno = $anno";
                break;
            default :
                $conf = "SELECT cr.id_unico, CONCAT(LOWER(c.nombre),' - ', rp.codi_presupuesto, ' ', LOWER(rp.nombre)) FROM gf_concepto_rubro cr "
                        . "LEFT JOIN gf_concepto c ON c.id_unico = cr.concepto "
                        . "LEFT JOIN gf_rubro_pptal rp ON cr.rubro = rp.id_unico "
                        . " WHERE c.clase_concepto =0 AND c.parametrizacionanno = $anno";
                break;
        }
        $query = $mysqli->query($conf);
        if (mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_row($query)) {
                echo '<option value="' . $row[0] . '">' . ucwords($row[1]) . '</option>';
            }
        } else {
            echo '<option value="">No hay conceptos</option>';
        }
    break;
    ##################Guardar interfáz Anterior#############
    case (7):
        $periodo = $_POST['periodo'];
        $grupogestion = $_POST['gg'];
        if (empty($_POST['ter'])) {
            $tercero = 2;
        } else {
            $tercero = $_POST['ter'];
        }
        $tipoDis = $_POST['tipodis'];
        $numeroDis = trim($_POST['numdis']);
        $fechaDis = fechaC($_POST['fechadis']);
        $tipoReg = $_POST['tiporeg'];
        $numeroReg = $_POST['numreg'];
        $fechaReg = fechaC($_POST['fechareg']);
        $tipoCxp = $_POST['tipocxp'];
        $numeroCxp = $_POST['numcxp'];
        $fechaCxp = fechaC($_POST['fechacxp']);
        ############Buscar la fecha del periodo para descripción####
        $fp = "SELECT DATE_FORMAT(p.fechainicio,'%d/%m/%Y'),
                    DATE_FORMAT(p.fechafin,'%d/%m/%Y'), 
                    tpn.nombre
                    FROM gn_periodo p 
                    LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico 
                    WHERE p.id_unico = $periodo";
        $fp = $mysqli->query($fp);
        if (mysqli_num_rows($fp) > 0) {
            $dp = mysqli_fetch_row($fp);
            #####Buscar el grpo gestion descripcion 
            $grupod = "select id_unico, LOWER(nombre)  from gn_grupo_gestion WHERE id_unico = $grupogestion";
            $grupod = $mysqli->query($grupod);
            $grupod = mysqli_fetch_row($grupod);
            $grupod = $grupod[1];
            $descripcion = "'" . $dp[2] . ' Del ' . $dp[0] . ' al ' . $dp[1] . ' ' . ucwords($grupod) . "'";
        } else {
            $descripcion = 'NULL';
        }
        $fechaVenDis = fechaSum($fechaDis);
        $fechaVenReg = fechaSum($fechaReg);
        $fechaVenCxp = fechaSum($fechaCxp);
        $guar = 0;
        ############Guardar Los Comprobantes##############
        //********Disponibilidad********//
        $insertdis = "INSERT INTO gf_comprobante_pptal  (numero, fecha, fechavencimiento, descripcion,  "
                . "parametrizacionanno, tipocomprobante, tercero, compania, usuario, fecha_elaboracion) "
                . "VALUES($numeroDis, '$fechaDis', '$fechaVenDis',$descripcion, "
                . "$anno,$tipoDis, $tercero, $compania, '$usuario', '$fechaE')";
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
        ######Si guardo los comprobantes########
        if ($guar == 3) {
            ##**Guardar en la tabla nómina financiera** ##
            $insertnf = "INSERT INTO gn_financiera_nomina (periodo, grupo_gestion, tercero, disponibilidad, registro, cuenta_pagar) "
                    . "VALUES($periodo, $grupogestion, $tercero, $iddis, $idreg, $idcxp)";
            $insertnf = $mysqli->query($insertnf);
            if ($insertnf == true) {
                ####################################################################################################################
                #Registrar Detalles Presupuestales
                #Buscar conceptos  
                $conn = "SELECT DISTINCT n.concepto,cn.tipofondo, cn.tipoentidadcredito , 
                    cn.tipo_interfaz, cn.clase 
                    FROM  gn_novedad n 
                                    LEFT JOIN gn_concepto cn ON n.concepto = cn.id_unico 
                                    LEFT JOIN gn_empleado e 
                                    ON n.empleado = e.id_unico 
                                    WHERE n.periodo = $periodo AND n.valor !=0 
                                    AND e.grupogestion = $grupogestion  
                                    AND (cn.clase =1  OR cn.clase =7) 
                                    AND cn.unidadmedida = 1 
                                    order by CONCEPTO asc";
                $conn = $mysqli->query($conn);
                while ($row1 = mysqli_fetch_row($conn)) {
                    ###Buscar Concepto Financiero, Rubro
                    $rfa = "SELECT cfn.rubro_fuente, cfn.concepto_financiero , c.clase_concepto 
                            FROM gn_concepto_nomina_financiero cfn 
                            LEFT JOIN gf_concepto_rubro cr ON cfn.concepto_financiero = cr.id_unico 
                            LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                            WHERE cfn.concepto_nomina = $row1[0] 
                            AND cfn.parametrizacionanno = $anno  
                            AND cfn.grupo_gestion = $grupogestion ";
                    $rfa = $mysqli->query($rfa);
                    $rfa = mysqli_fetch_row($rfa);
                    $rf = $rfa[0]; #*Rubro fuente
                    $cr = $rfa[1]; #*Concepto Rubro 
                    if ($rfa[2] == 2) {
                        $sd = apropiacion($rf) - disponibilidades($rf);
                        #Valor Detalle
                        $vcn = "SELECT SUM(n.valor) from gn_novedad n 
                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                    WHERE n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion";
                        $vnc = $mysqli->query($vcn);
                        $vnc = mysqli_fetch_row($vnc);
                        $vnc = $vnc[0];
                        if ($vnc > 0) {
                            ######Insertar Detalle Disponibilidad#######
                            $insertddis = "INSERT INTO gf_detalle_comprobante_pptal "
                                    . "(descripcion, valor, comprobantepptal, rubrofuente, "
                                    . "conceptoRubro, tercero, proyecto,  saldo_disponible) "
                                    . "VALUES ($descripcion, $vnc, $iddis, $rf, "
                                    . "$cr, $tercero,  2147483647, $sd)";
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
                            ######Insertar Detalle Cuenta X Pagar#######
                            $clase = 'NULL';
                            if ($row1[4] == 1) {
                                $clase = 'devengo';
                            } else {
                                $clase = 'informativo';
                            }
                            $insertdcxp = "INSERT INTO gf_detalle_comprobante_pptal (descripcion, valor, comprobantepptal, rubrofuente, conceptoRubro, "
                                    . "tercero, proyecto, comprobanteafectado, clasenom)"
                                    . "VALUES ($descripcion, $vnc, $idcxp, $rf, "
                                    . "$cr, $tercero,  2147483647, $dreg, '$clase')";
                            $insertdcxp = $mysqli->query($insertdcxp);
                            $dcxp = "SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $idcxp";
                            $dcxp = $mysqli->query($dcxp);
                            $dcxp = mysqli_fetch_row($dcxp);
                            $dcxp = $dcxp[0];

                            #**************************************************************************************************************#
                            ####*******************INSERTAR DETALLES CNT ***************###########
                            #Buscar cuenta debito y credito
                            $cdc = "SELECT crc.cuenta_debito, cd.naturaleza,  crc.cuenta_credito, cc.naturaleza "
                                    . "FROM gn_concepto_nomina_financiero cfn "
                                    . "LEFT JOIN gf_concepto_rubro cr ON cfn.concepto_financiero = cr.id_unico "
                                    . "LEFT JOIN gf_concepto_rubro_cuenta crc ON cr.id_unico = crc.concepto_rubro "
                                    . "LEFT JOIN gf_cuenta cd ON crc.cuenta_debito = cd.id_unico "
                                    . "LEFT JOIN gf_cuenta cc On crc.cuenta_credito = cc.id_unico "
                                    . "WHERE cfn.concepto_nomina = $row1[0] "
                                    . "AND cfn.parametrizacionanno = $anno "
                                    . "AND cfn.grupo_gestion = $grupogestion ";
                            $cdc = $mysqli->query($cdc);
                            $cdc = mysqli_fetch_row($cdc);
                            $cuentad = $cdc[0]; #*Cuenta Débito
                            $cuentac = $cdc[2]; #*Cuenta Crédito
                            #**********************************************************************************************************************************************
                            #Si es clase Devengo 1
                            if ($row1[4] == 1) {
                                ####Si tiene  Entidad Credito
                                if (!empty($row1[2]) && !empty($cuentac) && !empty($cuentad)) {
                                    #Valor Detalle
                                    $vcnt = "SELECT SUM(n.valor) from gn_novedad n 
                                                     LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                     WHERE n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion";
                                    $vcnt = $mysqli->query($vcnt);
                                    $vcnt = mysqli_fetch_row($vcnt);
                                    $vcnt = $vcnt[0];
                                    if ($vcnt > 0) {
                                        ##Valor por naturaleza
                                        $valordebito = 0;
                                        $valorcredito = 0;
                                        #Débito
                                        if ($cdc[1] == 1) {
                                            $valordebito = $vcnt;
                                        } else {
                                            $valordebito = $vcnt * -1;
                                        }
                                        ######Insertar Detalle Cnt Débito#######
                                        $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                                . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                . "cuenta, naturaleza, tercero,  proyecto, detallecomprobantepptal) "
                                                . "VALUES ('$fechaCxp',$descripcion, $valordebito,$vcnt, $idcxpcnt,  "
                                                . "$cuentad, $cdc[1], $row1[2], 2147483647,$dcxp)";
                                        $insertcntd = $mysqli->query($insertcntd);
                                    }
                                } else {
                                    #Si la interfaz es de tipo detallado o consolidado
                                    #Detallado
                                    if ($row1[3] == 1) {
                                        $terval = "SELECT n.id_unico, e.tercero, n.valor "
                                                . "FROM gn_novedad n "
                                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                . "WHERE  n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion";
                                        $terval = $mysqli->query($terval);
                                        while ($row = mysqli_fetch_row($terval)) {
                                            ##Valor por naturaleza
                                            if (!empty($row[2]) && intval($row[2]) > 0) {
                                                $vcnt = $row[2];
                                                $tercer = $row[1];
                                                $valordebito = 0;
                                                #Débito
                                                if ($cdc[1] == 1) {
                                                    $valordebito = $vcnt;
                                                } else {
                                                    $valordebito = $vcnt * -1;
                                                }
                                                ######Insertar Detalle Cnt Débito#######
                                                $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                                        . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                        . "cuenta, naturaleza, tercero,  proyecto,detallecomprobantepptal) "
                                                        . "VALUES ('$fechaCxp',$descripcion, $valordebito,$vcnt, $idcxpcnt,  "
                                                        . "$cuentad, $cdc[1],$tercer, 2147483647,$dcxp)";
                                                $insertcntd = $mysqli->query($insertcntd);
                                            }
                                        }
                                        #Acumulado
                                    } else {
                                        #Valor Detalle
                                        $vcnt = "SELECT SUM(n.valor) from gn_novedad n 
                                                     LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                     WHERE n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion";
                                        $vcnt = $mysqli->query($vcnt);
                                        $vcnt = mysqli_fetch_row($vcnt);
                                        $vcnt = $vcnt[0];
                                        if ($vcnt > 0) {
                                            ##Valor por naturaleza
                                            $valordebito = 0;
                                            #Débito
                                            if ($cdc[1] == 1) {
                                                $valordebito = $vcnt;
                                            } else {
                                                $valordebito = $vcnt * -1;
                                            }
                                            ######Insertar Detalle Cnt Débito#######
                                            $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                                    . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                    . "cuenta, naturaleza, tercero,  proyecto, detallecomprobantepptal) "
                                                    . "VALUES ('$fechaCxp',$descripcion, $valordebito,$vcnt, $idcxpcnt,  "
                                                    . "$cuentad, $cdc[1], $tercero, 2147483647,$dcxp)";
                                            $insertcntd = $mysqli->query($insertcntd);
                                        }
                                    }
                                }
                            }
                            #**********************************************************************************************************************************************
                            #Si es clase Informativo E 7
                            elseif ($row1[4] == 7) {
                                ####Si tiene  Entidad Credito
                                if (!empty($row1[2]) && !empty($cuentac) && !empty($cuentad)) {
                                    #Valor Detalle
                                    $vcnt = "SELECT SUM(n.valor) from gn_novedad n 
                                                     LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                     WHERE n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion";
                                    $vcnt = $mysqli->query($vcnt);
                                    $vcnt = mysqli_fetch_row($vcnt);
                                    $vcnt = $vcnt[0];
                                    if ($vcnt > 0) {
                                        ##Valor por naturaleza
                                        $valordebito = 0;
                                        $valorcredito = 0;
                                        #Débito
                                        if ($cdc[1] == 1) {
                                            $valordebito = $vcnt;
                                        } else {
                                            $valordebito = $vcnt * -1;
                                        }
                                        #Crédito
                                        if ($cdc[3] == 2) {
                                            $valorcredito = $vcnt;
                                        } else {
                                            $valorcredito = $vcnt * -1;
                                        }
                                        ######Insertar Detalle Cnt Débito#######
                                        $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                                . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                . "cuenta, naturaleza, tercero,  proyecto, detallecomprobantepptal) "
                                                . "VALUES ('$fechaCxp',$descripcion, $valordebito,$vcnt, $idcxpcnt,  "
                                                . "$cuentad, $cdc[1], $row1[2], 2147483647,$dcxp)";
                                        $insertcntd = $mysqli->query($insertcntd);

                                        ######Insertar Detalle Cnt Crédito#######
                                        $insertcntc = "INSERT INTO gf_detalle_comprobante "
                                                . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                . "cuenta, naturaleza, tercero,  proyecto, detallecomprobantepptal) "
                                                . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vcnt, $idcxpcnt,  "
                                                . "$cuentac, $cdc[3],  $row1[2] ,2147483647,$dcxp)";
                                        $insertcntc = $mysqli->query($insertcntc);
                                    }


                                    #Si tiene tipo fondo
                                } elseif (!empty($row1[1]) && !empty($cuentac) && !empty($cuentad)) {
                                    #Si la interfaz es de tipo detallado o consolidado
                                    #Detallado
                                    if ($row1[3] == 1) {
                                        $terval = "SELECT DISTINCT n.id_unico, a.tercero, n.valor 
                                                          FROM gn_novedad n 
                                                             LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                             LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                             LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  
                                                    WHERE  n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion  
                                                      AND a.tipo = $row1[1]";
                                        $terval = $mysqli->query($terval);
                                        while ($row = mysqli_fetch_row($terval)) {
                                            ##Valor por naturaleza
                                            if (!empty($row[2]) || $row[2] != 0) {
                                                $vcnt = $row[2];
                                                $tercer = $row[1];
                                                ##Valor por naturaleza
                                                $valordebito = 0;
                                                $valorcredito = 0;
                                                #Débito
                                                if ($cdc[1] == 1) {
                                                    $valordebito = $vcnt;
                                                } else {
                                                    $valordebito = $vcnt * -1;
                                                }
                                                #Crédito
                                                if ($cdc[3] == 2) {
                                                    $valorcredito = $vcnt;
                                                } else {
                                                    $valorcredito = $vcnt * -1;
                                                }
                                                ######Insertar Detalle Cnt Débito#######
                                                $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                                        . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                        . "cuenta, naturaleza, tercero,  proyecto, detallecomprobantepptal) "
                                                        . "VALUES ('$fechaCxp',$descripcion, $valordebito,$vcnt, $idcxpcnt,  "
                                                        . "$cuentad, $cdc[1], $tercer, 2147483647,$dcxp)";
                                                $insertcntd = $mysqli->query($insertcntd);

                                                ######Insertar Detalle Cnt Crédito#######
                                                $insertcntc = "INSERT INTO gf_detalle_comprobante "
                                                        . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                        . "cuenta, naturaleza, tercero,  proyecto, detallecomprobantepptal) "
                                                        . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vcnt, $idcxpcnt,  "
                                                        . "$cuentac, $cdc[3],  $tercer,2147483647,$dcxp)";
                                                $insertcntc = $mysqli->query($insertcntc);
                                            }
                                        }
                                        #Acumulado
                                    } else {
                                        ########Tercero#########
                                        $ter = " SELECT DISTINCT a.tercero   
                                                             FROM gn_novedad n 
                                                             LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                             LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                             INNER  JOIN gn_afiliacion a ON a.empleado = e.id_unico  
                                                 WHERE  n.periodo = $periodo AND c.id_unico = $row1[0]  AND e.grupogestion = $grupogestion 
                                                    AND a.tipo = $row1[1]";
                                        $ter = $mysqli->query($ter);
                                        while ($row2 = mysqli_fetch_row($ter)) {
                                            $terc = $row2[0];
                                            ########Valor#########
                                            $valcnt = "SELECT DISTINCT n.id_unico, n.valor , a.tercero
                                                             FROM gn_novedad n 
                                                             LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                             LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                             LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  AND c.tipofondo = a.tipo 
                                                     WHERE n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion AND   a.tercero = $row2[0]";
                                            $vcnt = $mysqli->query($valcnt);
                                            $valor = 0;
                                            if (mysqli_num_rows($vcnt) > 0) {
                                                while ($valorcnt = mysqli_fetch_row($vcnt)) {
                                                    $valor += $valorcnt[1];
                                                }
                                            }
                                            if ($valor > 0) {
                                                ##Valor por naturaleza
                                                ##Valor por naturaleza
                                                $valordebito = 0;
                                                $valorcredito = 0;
                                                #Débito
                                                if ($cdc[1] == 1) {
                                                    $valordebito = $valor;
                                                } else {
                                                    $valordebito = $valor * -1;
                                                }
                                                #Crédito
                                                if ($cdc[3] == 2) {
                                                    $valorcredito = $valor;
                                                } else {
                                                    $valorcredito = $valor * -1;
                                                }
                                                ######Insertar Detalle Cnt Débito#######
                                                $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                                        . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                        . "cuenta, naturaleza, tercero,  proyecto,detallecomprobantepptal) "
                                                        . "VALUES ('$fechaCxp',$descripcion, $valordebito,$valor, $idcxpcnt,  "
                                                        . "$cuentad, $cdc[1], $terc, 2147483647, $dcxp)";
                                                $insertcntd = $mysqli->query($insertcntd);

                                                ######Insertar Detalle Cnt Crédito#######
                                                $insertcntc = "INSERT INTO gf_detalle_comprobante "
                                                        . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                        . "cuenta, naturaleza, tercero,  proyecto, detallecomprobantepptal) "
                                                        . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$valor, $idcxpcnt,  "
                                                        . "$cuentac, $cdc[3],  $terc ,2147483647, $dcxp)";
                                                $insertcntc = $mysqli->query($insertcntc);
                                            }
                                        }
                                    }
                                } elseif (!empty($cuentac) && !empty($cuentad)) {
                                    #Si la interfaz es de tipo detallado o consolidado
                                    #Detallado
                                    if ($row1[3] == 1) {
                                        $terval = "SELECT n.id_unico, e.tercero, n.valor "
                                                . "FROM gn_novedad n "
                                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                . "WHERE  n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion";
                                        $terval = $mysqli->query($terval);
                                        while ($row = mysqli_fetch_row($terval)) {
                                            ##Valor por naturaleza
                                            if (!empty($row[2]) || $row[2] != 0) {
                                                $vcnt = $row[2];
                                                $tercer = $row[1];
                                                $valorcredito = 0;
                                                ##Valor por naturaleza
                                                $valordebito = 0;
                                                $valorcredito = 0;
                                                #Débito
                                                if ($cdc[1] == 1) {
                                                    $valordebito = $vcnt;
                                                } else {
                                                    $valordebito = $vcnt * -1;
                                                }
                                                #Crédito
                                                if ($cdc[3] == 2) {
                                                    $valorcredito = $vcnt;
                                                } else {
                                                    $valorcredito = $vcnt * -1;
                                                }
                                                ######Insertar Detalle Cnt Débito#######
                                                $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                                        . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                        . "cuenta, naturaleza, tercero,  proyecto, detallecomprobantepptal) "
                                                        . "VALUES ('$fechaCxp',$descripcion, $valordebito,$vcnt, $idcxpcnt,  "
                                                        . "$cuentad, $cdc[1], $tercer, 2147483647,$dcxp)";
                                                $insertcntd = $mysqli->query($insertcntd);

                                                ######Insertar Detalle Cnt Crédito#######
                                                $insertcntc = "INSERT INTO gf_detalle_comprobante "
                                                        . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                        . "cuenta, naturaleza, tercero,  proyecto, detallecomprobantepptal) "
                                                        . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vcnt, $idcxpcnt,  "
                                                        . "$cuentac, $cdc[3], $tercer ,2147483647, $dcxp)";
                                                $insertcntc = $mysqli->query($insertcntc);
                                            }
                                        }
                                        #Acumulado
                                    } else {
                                        #Valor Detalle
                                        $vcnt = "SELECT SUM(n.valor) from gn_novedad n 
                                                     LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                     WHERE n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion";
                                        $vcnt = $mysqli->query($vcnt);
                                        $vcnt = mysqli_fetch_row($vcnt);
                                        $vcnt = $vcnt[0];
                                        if ($vcnt > 0) {
                                            $valordebito = 0;
                                            $valorcredito = 0;
                                            #Débito
                                            if ($cdc[1] == 1) {
                                                $valordebito = $vcnt;
                                            } else {
                                                $valordebito = $vcnt * -1;
                                            }
                                            #Crédito
                                            if ($cdc[3] == 2) {
                                                $valorcredito = $vcnt;
                                            } else {
                                                $valorcredito = $vcnt * -1;
                                            }
                                            ######Insertar Detalle Cnt Débito#######
                                            $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                                    . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                    . "cuenta, naturaleza, tercero,  proyecto, detallecomprobantepptal) "
                                                    . "VALUES ('$fechaCxp',$descripcion, $valordebito,$vcnt, $idcxpcnt,  "
                                                    . "$cuentad, $cdc[1], $tercero, 2147483647, $dcxp)";
                                            $insertcntd = $mysqli->query($insertcntd);

                                            ######Insertar Detalle Cnt Crédito#######
                                            $insertcntc = "INSERT INTO gf_detalle_comprobante "
                                                    . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                    . "cuenta, naturaleza, tercero,  proyecto,detallecomprobantepptal) "
                                                    . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vcnt, $idcxpcnt,  "
                                                    . "$cuentac, $cdc[3], $tercero ,2147483647, $dcxp )";
                                            $insertcntc = $mysqli->query($insertcntc);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                ####################################################################################################################
                #Insertar Detalles CNT
                #Buscar conceptos 
                $concnt = "SELECT DISTINCT n.concepto , cn.tipofondo, cn.tipoentidadcredito , 
                    cn.tipo_interfaz, cn.clase FROM  gn_novedad n 
                                   LEFT JOIN gn_concepto cn ON n.concepto = cn.id_unico 
                                   LEFT JOIN gn_empleado e 
                                   ON n.empleado = e.id_unico 
                                   WHERE n.periodo = $periodo AND n.valor !=0  
                                   AND e.grupogestion = $grupogestion  
                                   AND ( cn.clase =2  OR cn.clase =5) 
                                   AND cn.unidadmedida = 1 
                                   order by CONCEPTO asc";
                $concnt = $mysqli->query($concnt);
                while ($rowcnt = mysqli_fetch_row($concnt)) {
                    #Buscar cuenta debito y credito
                    $cdc = "SELECT crc.cuenta_debito, cd.naturaleza,  crc.cuenta_credito, cc.naturaleza 
                            FROM gn_concepto_nomina_financiero cfn 
                            LEFT JOIN gf_concepto_rubro cr ON cfn.concepto_financiero = cr.id_unico 
                            LEFT JOIN gf_concepto_rubro_cuenta crc ON cr.id_unico = crc.concepto_rubro 
                            LEFT JOIN gf_cuenta cd ON crc.cuenta_debito = cd.id_unico 
                            LEFT JOIN gf_cuenta cc On crc.cuenta_credito = cc.id_unico  
                            WHERE cfn.concepto_nomina = $rowcnt[0] 
                            AND cfn.parametrizacionanno = $anno 
                            AND cfn.grupo_gestion = $grupogestion ";
                    $cdc = $mysqli->query($cdc);
                    $cdc = mysqli_fetch_row($cdc);
                    $cuentad = $cdc[0]; #*Cuenta Débito
                    $cuentac = $cdc[2]; #*Cuenta Crédito
                    #**********************************************************************************************************************************************
                    #Si es clase Descuento 2
                    if ($rowcnt[4] == 2) {
                        ####Si tiene  Entidad Credito
                        if (!empty($rowcnt[2]) && !empty($cuentac) && !empty($cuentad)) {
                            #Valor Detalle
                            $vcnt = "SELECT SUM(n.valor) from gn_novedad n 
                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                            WHERE n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                            $vcnt = $mysqli->query($vcnt);
                            $vcnt = mysqli_fetch_row($vcnt);
                            $vcnt = $vcnt[0];
                            if ($vcnt > 0) {
                                ##Valor por naturaleza
                                $valorcredito = 0;
                                #Crédito
                                if ($cdc[3] == 2) {
                                    $valorcredito = $vcnt;
                                } else {
                                    $valorcredito = $vcnt * -1;
                                }

                                ######Insertar Detalle Cnt Crédito#######
                                $insertcntc = "INSERT INTO gf_detalle_comprobante "
                                        . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                        . "cuenta, naturaleza, tercero,  proyecto) "
                                        . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vcnt, $idcxpcnt,  "
                                        . "$cuentac, $cdc[3],  $rowcnt[2] ,2147483647)";
                                $insertcntc = $mysqli->query($insertcntc);
                            }
                            #***Si tiene tipo fondo
                        } elseif (!empty($rowcnt[1]) && !empty($cuentac) && !empty($cuentad)) {
                            #Si la interfaz es de tipo detallado o consolidado
                            #Detallado
                            if ($rowcnt[3] == 1) {
                                $terval = "SELECT DISTINCT n.id_unico, a.tercero, n.valor 
                                                 FROM gn_novedad n 
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                    LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  AND c.tipofondo = a.tipo 
                                           WHERE  n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                                $terval = $mysqli->query($terval);
                                while ($row = mysqli_fetch_row($terval)) {
                                    ##Valor por naturaleza
                                    if (!empty($row[2]) || $row[2] != 0) {
                                        $vcnt = $row[2];
                                        $tercer = $row[1];
                                        $valorcredito = 0;
                                        #Crédito
                                        if ($cdc[3] == 2) {
                                            $valorcredito = $vcnt;
                                        } else {
                                            $valorcredito = $vcnt * -1;
                                        }
                                        ######Insertar Detalle Cnt Débito#######
                                        $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                                . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                . "cuenta, naturaleza, tercero,  proyecto) "
                                                . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vcnt, $idcxpcnt,  "
                                                . "$cuentac, $cdc[1],$tercer, 2147483647)";
                                        $insertcntd = $mysqli->query($insertcntd);
                                    }
                                }
                                #Acumulado
                            } else {
                                ########Tercero#########
                                $ter = " SELECT DISTINCT a.tercero   
                                                    FROM gn_novedad n 
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                    LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  AND c.tipofondo = a.tipo 
                                        WHERE  n.periodo = $periodo AND c.id_unico = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                                $ter = $mysqli->query($ter);
                                while ($row2 = mysqli_fetch_row($ter)) {
                                    $terc = $row2[0];
                                    ########Valor#########
                                    $valcnt = "SELECT DISTINCT n.id_unico, n.valor , a.tercero
                                                    FROM gn_novedad n 
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                    LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  AND c.tipofondo = a.tipo 
                                            WHERE n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion AND   a.tercero = $row2[0]";
                                    $vcnt = $mysqli->query($valcnt);
                                    $valor = 0;
                                    if (mysqli_num_rows($vcnt) > 0) {
                                        while ($valorcnt = mysqli_fetch_row($vcnt)) {
                                            $valor += $valorcnt[1];
                                        }
                                    }
                                    if ($valor > 0) {
                                        ##Valor por naturaleza
                                        $valorcredito = 0;
                                        #Crédito
                                        if ($cdc[3] == 2) {
                                            $valorcredito = $valor;
                                        } else {
                                            $valorcredito = $valor * -1;
                                        }

                                        ######Insertar Detalle Cnt Crédito#######
                                        $insertcntc = "INSERT INTO gf_detalle_comprobante "
                                                . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                . "cuenta, naturaleza, tercero,  proyecto) "
                                                . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$valor, $idcxpcnt,  "
                                                . "$cuentac, $cdc[3],  $terc ,2147483647)";

                                        $insertcntc = $mysqli->query($insertcntc);
                                    }
                                }
                            }
                        } else {
                            #Si la interfaz es de tipo detallado o consolidado
                            #Detallado
                            if ($rowcnt[3] == 1) {
                                $terval = "SELECT DISTINCT n.id_unico, e.tercero, n.valor "
                                        . "FROM gn_novedad n "
                                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                        . "WHERE  n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                                $terval = $mysqli->query($terval);
                                while ($row = mysqli_fetch_row($terval)) {
                                    ##Valor por naturaleza
                                    if (!empty($row[2]) || $row[2] != 0) {
                                        $vcnt = $row[2];
                                        $tercer = $row[1];
                                        $valorcredito = 0;
                                        #Crédito
                                        if ($cdc[3] == 2) {
                                            $valorcredito = $vcnt;
                                        } else {
                                            $valorcredito = $vcnt * -1;
                                        }
                                        ######Insertar Detalle Cnt Débito#######
                                        $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                                . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                . "cuenta, naturaleza, tercero,  proyecto) "
                                                . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vcnt, $idcxpcnt,  "
                                                . "$cuentac, $cdc[1],$tercer, 2147483647)";
                                        $insertcntd = $mysqli->query($insertcntd);
                                    }
                                }
                                #Acumulado
                            } else {

                                $vcnt = "SELECT SUM(n.valor) from gn_novedad n 
                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                            WHERE n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                                $vcnt = $mysqli->query($vcnt);
                                $vcnt = mysqli_fetch_row($vcnt);
                                $vcnt = $vcnt[0];
                                if ($vcnt > 0) {
                                    ##Valor por naturaleza
                                    $valorcredito = 0;
                                    #Crédito
                                    if ($cdc[3] == 2) {
                                        $valorcredito = $vcnt;
                                    } else {
                                        $valorcredito = $vcnt * -1;
                                    }

                                    ######Insertar Detalle Cnt Crédito#######
                                    $insertcntc = "INSERT INTO gf_detalle_comprobante "
                                            . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                            . "cuenta, naturaleza, tercero,  proyecto) "
                                            . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vcnt, $idcxpcnt,  "
                                            . "$cuentac, $cdc[3],  $tercero ,2147483647)";
                                    $insertcntc = $mysqli->query($insertcntc);
                                }
                            }
                        }
                    }

                    #**********************************************************************************************************************************************
                    #Si es clase 5 Neto a pagar
                    elseif ($rowcnt[4] == 5) {
                        ####Si tiene  Entidad Credito
                        if (!empty($rowcnt[2]) && !empty($cuentac) && !empty($cuentad)) {
                            #Valor Detalle
                            $vcnt = "SELECT SUM(n.valor) from gn_novedad n 
                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                            WHERE n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                            $vcnt = $mysqli->query($vcnt);
                            $vcnt = mysqli_fetch_row($vcnt);
                            $vcnt = $vcnt[0];
                            if ($vcnt > 0) {
                                ##Valor por naturaleza
                                $valorcredito = 0;
                                #Crédito
                                if ($cdc[3] == 2) {
                                    $valorcredito = $vcnt;
                                } else {
                                    $valorcredito = $vcnt * -1;
                                }

                                ######Insertar Detalle Cnt Crédito#######
                                $insertcntc = "INSERT INTO gf_detalle_comprobante "
                                        . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                        . "cuenta, naturaleza, tercero,  proyecto) "
                                        . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vcnt, $idcxpcnt,  "
                                        . "$cuentac, $cdc[3],  $rowcnt[2] ,2147483647)";
                                $insertcntc = $mysqli->query($insertcntc);
                            }
                            #***Si tiene tipo fondo
                        } elseif (!empty($rowcnt[1]) && !empty($cuentac) && !empty($cuentad)) { {
                                #Si la interfaz es de tipo detallado o consolidado
                                #Detallado
                                if ($rowcnt[3] == 1) {
                                    $terval = "SELECT DISTINCT n.id_unico, a.tercero, n.valor 
                                                 FROM gn_novedad n 
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                    INNER JOIN gn_afiliacion a ON a.empleado = e.id_unico  AND c.tipofondo = a.tipo 
                                           WHERE  n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                                    $terval = $mysqli->query($terval);
                                    while ($row = mysqli_fetch_row($terval)) {
                                        ##Valor por naturaleza
                                        if (!empty($row[2]) || $row[2] != 0) {
                                            $vcnt = $row[2];
                                            $tercer = $row[1];
                                            $valorcredito = 0;
                                            #Crédito
                                            if ($cdc[3] == 2) {
                                                $valorcredito = $vcnt;
                                            } else {
                                                $valorcredito = $vcnt * -1;
                                            }
                                            ######Insertar Detalle Cnt Débito#######
                                            $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                                    . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                    . "cuenta, naturaleza, tercero,  proyecto) "
                                                    . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vcnt, $idcxpcnt,  "
                                                    . "$cuentac, $cdc[1],$tercer, 2147483647)";
                                            $insertcntd = $mysqli->query($insertcntd);
                                        }
                                    }
                                    #Acumulado
                                } else {
                                    ########Tercero#########
                                    $ter = " SELECT DISTINCT a.tercero   
                                                    FROM gn_novedad n 
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                    LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  AND c.tipofondo = a.tipo 
                                        WHERE  n.periodo = $periodo AND c.id_unico = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                                    $ter = $mysqli->query($ter);
                                    while ($row2 = mysqli_fetch_row($ter)) {
                                        $terc = $row2[0];
                                        ########Valor#########
                                        $valcnt = "SELECT DISTINCT n.id_unico, n.valor , a.tercero
                                                    FROM gn_novedad n 
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                    LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  AND c.tipofondo = a.tipo 
                                            WHERE n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion AND   a.tercero = $row2[0]";
                                        $vcnt = $mysqli->query($valcnt);
                                        $valor = 0;
                                        if (mysqli_num_rows($vcnt) > 0) {
                                            while ($valorcnt = mysqli_fetch_row($vcnt)) {
                                                $valor += $valorcnt[1];
                                            }
                                        }
                                        if ($valor > 0) {
                                            ##Valor por naturaleza
                                            $valorcredito = 0;
                                            #Crédito
                                            if ($cdc[3] == 2) {
                                                $valorcredito = $valor;
                                            } else {
                                                $valorcredito = $valor * -1;
                                            }

                                            ######Insertar Detalle Cnt Crédito#######
                                            $insertcntc = "INSERT INTO gf_detalle_comprobante "
                                                    . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                    . "cuenta, naturaleza, tercero,  proyecto) "
                                                    . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$valor, $idcxpcnt,  "
                                                    . "$cuentac, $cdc[3],  $terc ,2147483647)";

                                            $insertcntc = $mysqli->query($insertcntc);
                                        }
                                    }
                                }
                            }
                        } else {
                            #Si la interfaz es de tipo detallado o consolidado
                            #Detallado
                            if ($rowcnt[3] == 1) {
                                $terval = "SELECT DISTINCT n.id_unico, e.tercero, n.valor "
                                        . "FROM gn_novedad n "
                                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                        . "WHERE  n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                                $terval = $mysqli->query($terval);
                                while ($row = mysqli_fetch_row($terval)) {
                                    ##Valor por naturaleza
                                    if (!empty($row[2]) || $row[2] != 0) {
                                        $vcnt = $row[2];
                                        $tercer = $row[1];
                                        $valorcredito = 0;
                                        #Crédito
                                        if ($cdc[3] == 2) {
                                            $valorcredito = $vcnt;
                                        } else {
                                            $valorcredito = $vcnt * -1;
                                        }
                                        ######Insertar Detalle Cnt Débito#######
                                        $insertcntd = "INSERT INTO gf_detalle_comprobante "
                                                . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                                . "cuenta, naturaleza, tercero,  proyecto) "
                                                . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vcnt, $idcxpcnt,  "
                                                . "$cuentac, $cdc[1],$tercer, 2147483647)";
                                        $insertcntd = $mysqli->query($insertcntd);
                                    }
                                }
                                #Acumulado
                            } else {

                                $vcnt = "SELECT SUM(n.valor) from gn_novedad n 
                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                            WHERE n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                                $vcnt = $mysqli->query($vcnt);
                                $vcnt = mysqli_fetch_row($vcnt);
                                $vcnt = $vcnt[0];
                                if ($vcnt > 0) {
                                    ##Valor por naturaleza
                                    $valorcredito = 0;
                                    #Crédito
                                    if ($cdc[3] == 2) {
                                        $valorcredito = $vcnt;
                                    } else {
                                        $valorcredito = $vcnt * -1;
                                    }

                                    ######Insertar Detalle Cnt Crédito#######
                                    $insertcntc = "INSERT INTO gf_detalle_comprobante "
                                            . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                                            . "cuenta, naturaleza, tercero,  proyecto) "
                                            . "VALUES ('$fechaCxp',$descripcion, $valorcredito,$vcnt, $idcxpcnt,  "
                                            . "$cuentac, $cdc[3],  $tercero ,2147483647)";
                                    $insertcntc = $mysqli->query($insertcntc);
                                }
                            }
                        }
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
            //Cerrar Periodo
            $sql = "UPDATE gn_periodo SET liquidado = 1 WHERE id_unico = $periodo";
            $res = $mysqli->query($sql);

            $result = "GN_GENERAR_INTERFAZ.php?periodo=" . md5($periodo) . "&gg=" . md5($grupogestion);
        } elseif ($result == 1) {
            $result = 1;
        } else {
            $result = 0;
        }
        echo ($result);


    break;
    #####Cargar Rubro Fuente#######
    case (8):
        $conceptoRubro = $_POST['concepto'];
        #*Buscar Rubro
        $rb = "SELECT rubro FROM gf_concepto_rubro WHERE id_unico = $conceptoRubro";
        $rb = $mysqli->query($rb);
        $rb = mysqli_fetch_row($rb);
        $rb = $rb[0];
        #*Buscar Rubro Fuente
        $rf = "SELECT rf.id_unico, CONCAT(rp.codi_presupuesto, ' ', LOWER(rp.nombre),' - ', f.id_unico, ' ',LOWER(f.nombre)) "
                . "FROM gf_rubro_fuente rf LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico "
                . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                . "WHERE rf.rubro = $rb";
        $query = $mysqli->query($rf);
        if (mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_row($query)) {
                echo '<option value="' . $row[0] . '">' . ucwords($row[1]) . '</option>';
            }
        } else {
            echo '<option value="">No hay rubro fuente</option>';
        }

    break;
    #Comprobar si todos los rubro fuente de las homologaciones tienen saldo disponible 
    case 9:
        $fechaDis   = fechaC($_POST['fecha']);
        $periodo    = $_POST['periodo'];
        $grupoG     = $_POST['gg'];
        $rta        = 0;
        $html       = "";
        $datos      = array();
        
        #Buscar conceptos nómina
        $rowc = $con->Listar("SELECT DISTINCT n.concepto, cn.descripcion, cn.codigo FROM  gn_novedad n 
            LEFT JOIN gn_concepto cn ON n.concepto = cn.id_unico 
            LEFT JOIN gn_empleado e 
            ON n.empleado = e.id_unico 
            WHERE n.periodo = $periodo 
            AND e.grupogestion = $grupoG  
            AND (cn.clase =1 OR cn.clase=7) 
            AND cn.unidadmedida = 1 
            AND n.valor !=0 
            order by CONCEPTO asc");
        for ($i = 0; $i < count($rowc); $i++) {
            #** Buscar Valor Concepto Nómina ***#
            $vcn    = $con->Listar("SELECT SUM(n.valor) from gn_novedad n 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE n.periodo = $periodo AND n.concepto = ".$rowc[$i][0]." 
                    AND e.grupogestion = $grupoG");
            $v_con = $vcn[0][0];
            if ($v_con > 0) {
                #*** Buscar Configuración ***#
                $rowcr = $con->Listar("SELECT DISTINCT cfn.rubro_fuente, 
                    CONCAT(rp.codi_presupuesto,' ', LOWER(rp.nombre), ' - ', f.id_unico, ' ', f.nombre ),
                    c.clase_concepto, cfn.tipo 
                    FROM gn_concepto_nomina_financiero cfn 
                    LEFT JOIN gf_rubro_fuente rf ON cfn.rubro_fuente = rf.id_unico 
                    LEFT JOIN gf_rubro_pptal rp ON rp.id_unico = rf.rubro 
                    LEFT JOIN gf_fuente f ON f.id_unico = rf.fuente 
                    LEFT JOIN gf_concepto_rubro cr ON cr.id_unico = cfn.concepto_financiero 
                    LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                    WHERE cfn.concepto_nomina = ".$rowc[$i][0]." 
                    AND cfn.parametrizacionanno = $anno 
                    AND cfn.grupo_gestion = $grupoG ");
                
                if(count($rowcr)>1){
                    $v_s = 0;
                    $crs = "";
                    for ($r = 0; $r < count($rowcr); $r++) {
                        $rf = $rowcr[$r][0]; #*Rubro fuente
                        $cr = $rowcr[$r][1]; #*Nombre
                        $crs .= $cr.'<br/>';
                        if(empty($rowcr[$r][3])){
                            $sd = apropiacionfecha($rf,$fechaDis) - disponibilidadesfecha($rf,$fechaDis);
                            $v_s +=$sd; 
                        } else {
                            if($rowcr[$r][3]==1){
                                $varb = 'publica';
                                $varb1 = 'pública';
                            } elseif($rowcr[$r][3]==2){
                                $varb = 'privada';
                                $varb1 = 'privada';
                            }
                            #** Buscar El Valor del concepto por tipo de entidad ***#
                            $vcne = $con->Listar("SELECT SUM(n.valor) from gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion a ON e.id_unico = a.empleado AND a.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON a.tercero = t.id_unico 
                            LEFT JOIN gf_tipo_empresa tc ON tc.id_unico = t.tipoempresa 
                            WHERE n.periodo = $periodo AND n.concepto = ".$rowc[$i][0]." 
                            AND e.grupogestion = $grupoG 
                            AND (LOWER(tc.nombre)='$varb' OR LOWER(tc.nombre)='$varb1')");
                            
                            $sd = apropiacionfecha($rf,$fechaDis) - disponibilidadesfecha($rf,$fechaDis);
                            if($sd >= $vcne[0][0]){
                                $v_s += $vcne[0][0]; 
                            } else {
                                $v_s += 0;
                            }
                        }
                    }
                    if($v_s >= $v_con){  
                    } else {
                        $rta +=1;
                        $html .=$crs."<br/>";
                    }
                } else {
                    $rf = $rowcr[0][0]; #*Rubro fuente
                    $cr = $rowcr[0][1]; #*Nombre
                    $sd = apropiacionfecha($rf,$fechaDis) - disponibilidadesfecha($rf,$fechaDis);
                    if($sd >= $v_con){
                        
                    } else {
                        $rta +=1;
                        $html .=$cr."<br/>";
                    }
                }
            }
        }
        
        $datos = array("msj"=>$html,"rta"=>$rta);
        echo json_encode($datos);
    break;
    #######Buscar si ya hay comprobantes guardados
    case 10:
        $periodo = $_POST['periodo'];
        $grupogestion = $_POST['gg'];
        $resp = 0;
        $sl = "SELECT * FROM gn_financiera_nomina WHERE periodo = $periodo AND grupo_gestion = $grupogestion";
        $sl = $mysqli->query($sl);
        if (mysqli_num_rows($sl) > 0) {
            $periodo = md5($periodo);
            $gg = md5($grupogestion);
            $resp = "GN_GENERAR_INTERFAZ.php?periodo=$periodo&gg=$gg";
        }
        echo $resp;

    break;
    #*****Validar la clase del concept Nómina seleccionado
    case 11:
        $concepto = $_POST['concepto'];
        $cc = "SELECT clase FROM gn_concepto WHERE id_unico = $concepto";
        $cc = $mysqli->query($cc);
        $cc = mysqli_fetch_row($cc);
        $clasec = $cc[0];
        echo $clasec;
    break;    
    #**** Guardar Comprobantes Encabezados***#
    case 12:
        $periodo        = $_POST['periodo'];
        $grupogestion   = $_POST['gg'];
        if (!empty($_POST['ter'])) {
           $tercero     = $_POST['ter'];
        }
        $tipoDis        = $_POST['tipodis'];
        $numeroDis      = trim($_POST['numdis']);
        $fechaDis       = fechaC($_POST['fechadis']);
        $tipoReg        = $_POST['tiporeg'];
        $numeroReg      = $_POST['numreg'];
        $fechaReg       = fechaC($_POST['fechareg']);
        $tipoCxp        = $_POST['tipocxp'];
        $numeroCxp      = $_POST['numcxp'];
        $fechaCxp       = fechaC($_POST['fechacxp']);
        $rta            = 0;
        $iddis=""; $idreg=""; $idcxp="";$idcxpcnt="";
        #****   Buscar la fecha del periodo para descripción ***#
        $fp = "SELECT DATE_FORMAT(p.fechainicio,'%d/%m/%Y'),
                    DATE_FORMAT(p.fechafin,'%d/%m/%Y'), 
                    tpn.nombre
                    FROM gn_periodo p 
                    LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico 
                    WHERE p.id_unico = $periodo";
        $fp = $mysqli->query($fp);
        if (mysqli_num_rows($fp) > 0) {
            $dp = mysqli_fetch_row($fp);
            #####Buscar el grpo gestion descripcion 
            $grupod = "select id_unico, LOWER(nombre)  from gn_grupo_gestion WHERE id_unico = $grupogestion";
            $grupod = $mysqli->query($grupod);
            $grupod = mysqli_fetch_row($grupod);
            $grupod = $grupod[1];
            $descripcion = "'" . $dp[2] . ' Del ' . $dp[0] . ' al ' . $dp[1] . ' ' . ucwords($grupod) . "'";
        } else {
            $descripcion = 'NULL';
        }
        $fechaVenDis = fechaSum($fechaDis);
        $fechaVenReg = fechaSum($fechaReg);
        $fechaVenCxp = fechaSum($fechaCxp);
        $guar = 0;
        #******** Guardar Los Comprobantes ********#
        #******** Disponibilidad ********#
        $insertdis = "INSERT INTO gf_comprobante_pptal  (numero, fecha, fechavencimiento, descripcion,  "
                . "parametrizacionanno, tipocomprobante, tercero, compania, usuario, fecha_elaboracion) "
                . "VALUES($numeroDis, '$fechaDis', '$fechaVenDis',$descripcion, "
                . "$anno,$tipoDis, $tercero, $compania, '$usuario', '$fechaE')";
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
        ######Si guardo los comprobantes########
        if ($guar == 3) { 
            #**Guardar en la tabla nómina financiera** ##
            $insertnf = "INSERT INTO gn_financiera_nomina (periodo, grupo_gestion, tercero, 
                disponibilidad, registro, cuenta_pagar) 
                VALUES($periodo, $grupogestion, $tercero, $iddis, $idreg, $idcxp)";
            $insertnf = $mysqli->query($insertnf);
            $insertnf = true;
            if ($insertnf == true) {
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
                $rta =1;
            }
        } else { $rta =1;}
        $datos = array();
        $datos = array("rta"=>$rta,"iddis"=>$iddis,"idreg"=>$idreg,"idcxp"=>$idcxp,"idcxpcnt"=>$idcxpcnt);
        echo json_encode($datos);

    break;
    
    #**** Guardar Detalles   ***#
    case 13:
        $rta = 0;
        $periodo        = $_POST['periodo'];
        $grupogestion   = $_POST['gg'];
        if (!empty($_POST['ter'])) {
           $tercero     = $_POST['ter'];
        }
        $iddis          = $_REQUEST['iddis'];
        $idreg          = $_REQUEST['idreg'];
        $idcxp          = $_REQUEST['idcxp'];
        $idcxpcnt       = $_REQUEST['idcxpcnt'];
        $fechaDis       = fechaC($_REQUEST['fechadis']);
        $fechaCxp       = $_REQUEST['fechaCxp'];
        $descripcion    = '"Comprobante Nómina"';
        if (!empty($iddis) && !empty($idreg) && !empty($idcxp) ) {
            if(empty($idcxpcnt)){
                #Buscr el tipo de comprobante cnt del pptal
                $tcn = "SELECT tc.id_unico, cp.numero, cp.fecha, cp.descripcion 
                    FROM gf_comprobante_pptal cp 
                    LEFT JOIN gf_tipo_comprobante tc ON cp.tipocomprobante = tc.comprobante_pptal 
                    WHERE cp.id_unico= $idcxp";
                $tcn = $mysqli->query($tcn);
                $tcn = mysqli_fetch_row($tcn);
                $tipoCxpCnt = $tcn[0];
                $numeroCxp  = $tcn[1];
                $fechaCxp   = $tcn[2];
                $descripcion= '"'.$tcn[3].'"';
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
            }
            #Buscar conceptos  
            $conn = "SELECT DISTINCT n.concepto,cn.tipofondo, cn.tipoentidadcredito , 
                cn.tipo_interfaz, cn.clase 
                FROM  gn_novedad n 
                                LEFT JOIN gn_concepto cn ON n.concepto = cn.id_unico 
                                LEFT JOIN gn_empleado e 
                                ON n.empleado = e.id_unico 
                                WHERE n.periodo = $periodo AND n.valor !=0 
                                AND e.grupogestion = $grupogestion  
                                AND (cn.clase =1  OR cn.clase =7)  
                                AND cn.unidadmedida = 1  
                                order by CONCEPTO asc";
            $conn = $mysqli->query($conn);
            while ($row1 = mysqli_fetch_row($conn)) {
                #*** Buscar Concepto Financiero, Rubro
                $rfa = "SELECT cfn.rubro_fuente, cfn.concepto_financiero , c.clase_concepto, 
                        cfn.tipo 
                        FROM gn_concepto_nomina_financiero cfn 
                        LEFT JOIN gf_concepto_rubro cr ON cfn.concepto_financiero = cr.id_unico 
                        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                        WHERE cfn.concepto_nomina = $row1[0] 
                        AND cfn.parametrizacionanno = $anno  
                        AND cfn.grupo_gestion = $grupogestion ";
                $rfa = $mysqli->query($rfa);
                $numr = mysqli_num_rows($rfa); 
                
                if($numr >1){
                    $v_s = 0;
                    $crs = "";
                    $rowcr = $con->Listar("SELECT cfn.rubro_fuente, 
                        cfn.concepto_financiero , c.clase_concepto, 
                        cfn.tipo 
                        FROM gn_concepto_nomina_financiero cfn 
                        LEFT JOIN gf_concepto_rubro cr ON cfn.concepto_financiero = cr.id_unico 
                        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                        WHERE cfn.concepto_nomina = $row1[0] 
                        AND cfn.parametrizacionanno = $anno  
                        AND cfn.grupo_gestion = $grupogestion ");    
                    #** Valor Concepto **#
                    $vcne = $con->Listar("SELECT SUM(n.valor) from gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion a ON e.id_unico = a.empleado AND a.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON a.tercero = t.id_unico 
                        LEFT JOIN gf_tipo_empresa tc ON tc.id_unico = t.tipoempresa 
                        WHERE n.periodo = $periodo AND n.concepto = ".$row1[0]." 
                        AND e.grupogestion = $grupogestion ");
                    $vcne   = $vcne[0][0]; 
                    $saldog = $vcne; 
                    $tgsd   = array();
                    $tgsi   = array();
                    $tgsif  = array();
                    $tgsie  = array();
                    $tgsdt  = array();
                    for ($r = 0; $r < count($rowcr); $r++) {
                        if($rowcr[$r][2]==2) {
                            $rf = $rowcr[$r][0]; #*Rubro fuente
                            $cr = $rowcr[$r][1]; #*Nombre 
                            if(empty($rowcr[$r][3])){
                                if($saldog > 0){ 
                                    $sd = apropiacionfecha($rf,$fechaDis) - disponibilidadesfecha($rf,$fechaDis);
                                    if($sd >= $saldog) {
                                        $vnc = $saldog;
                                    } else {
                                        if($sd>0){
                                            $vnc = $sd;
                                        } else {
                                            $vnc=0;
                                        }
                                    }
                                    $saldog -=$vnc;
                                    if ($vnc > 0) {
                                        # ** Detalles Disponibilidad 
                                        $ddis = detallespptal($descripcion, $vnc, $iddis, $rf, $cr, $tercero, $sd, 'NULL','NULL');
                                        #*******************************************#
                                        # ** Detalles Registro  
                                        $dreg = detallespptal($descripcion, $vnc, $idreg, $rf, $cr, $tercero, 'NULL',$ddis,'NULL');
                                        #*******************************************#
                                        # ** Detalles Cuenta Por pagar
                                        $clase = 'NULL';
                                        if ($row1[4] == 1) {
                                            $clase = 'devengo';
                                        } else {
                                            $clase = 'informativo';
                                        }
                                        $dcxp = detallespptal($descripcion, $vnc, $idcxp, $rf, $cr, $tercero, 'NULL',$dreg,"'".$clase."'");

                                        #**************************************************************************************************************#
                                        #*******************    INSERTAR DETALLES CNT   ***************#
                                        #Buscar cuenta debito y credito
                                        $cdc = "SELECT crc.cuenta_debito, cd.naturaleza,  crc.cuenta_credito, "
                                                . "cc.naturaleza "
                                                . "FROM gn_concepto_nomina_financiero cfn "
                                                . "LEFT JOIN gf_concepto_rubro cr ON cfn.concepto_financiero = cr.id_unico "
                                                . "LEFT JOIN gf_concepto_rubro_cuenta crc ON cr.id_unico = crc.concepto_rubro "
                                                . "LEFT JOIN gf_cuenta cd ON crc.cuenta_debito = cd.id_unico "
                                                . "LEFT JOIN gf_cuenta cc On crc.cuenta_credito = cc.id_unico "
                                                . "WHERE cfn.concepto_nomina = $row1[0] "
                                                . "AND cfn.parametrizacionanno = $anno "
                                                . "AND cfn.grupo_gestion = $grupogestion "
                                                . "AND cr.id_unico = $cr AND cfn.rubro_fuente=$rf ";
                                        $cdc = $mysqli->query($cdc);
                                        $cdc = mysqli_fetch_row($cdc);
                                        $cuentad = $cdc[0]; #*Cuenta Débito
                                        $cuentac = $cdc[2]; #*Cuenta Crédito
                                        #**********************************************************************************************************************************************
                                        #Si es clase Devengo 1
                                        if ($row1[4] == 1) {
                                            ####Si tiene  Entidad Credito
                                            if (!empty($row1[2]) && !empty($cuentac) && !empty($cuentad)) {
                                                $vcnt = $vnc;
                                                if ($vcnt > 0) {
                                                    ##Valor por naturaleza
                                                    $valordebito = 0;
                                                    $valorcredito = 0;
                                                    #Débito
                                                    if ($cdc[1] == 1) {
                                                        $valordebito = $vcnt;
                                                    } else {
                                                        $valordebito = $vcnt * -1;
                                                    }
                                                    detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1],$row1[2], $dcxp);
                                                }
                                            } else {
                                                #Si la interfaz es de tipo detallado o consolidado
                                                #Detallado
                                                if ($row1[3] == 1) {
                                                    $terval = "SELECT n.id_unico, e.tercero, n.valor "
                                                            . "FROM gn_novedad n "
                                                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                            . "WHERE  n.periodo = $periodo "
                                                            . "AND n.concepto = $row1[0]  "
                                                            . "AND e.grupogestion = $grupogestion ";
                                                    $terval = $mysqli->query($terval); 
                                                    $saldog2 = $vnc;
                                                    while ($row = mysqli_fetch_row($terval)) {
                                                        if($saldog2>0){
                                                            ##Valor por naturaleza
                                                            if (!empty($row[2]) && intval($row[2]) > 0) {
                                                                $vcnt = $row[2];
                                                                if(!empty($tgsd[$row[1]])){
                                                                    if($vcnt > $saldog2){
                                                                        $vg = $saldog2;
                                                                        $tgsd[$row[1]] += $vg;
                                                                    } else {
                                                                        $tgsd[$row[1]] += $vcnt;
                                                                        $vg = $vcnt;
                                                                    }
                                                                } else {
                                                                    if($vcnt>$saldog2){
                                                                        $vg = $saldog2;
                                                                        $tgsd[$row[1]] = $vg;
                                                                    } else {
                                                                        $tgsd[$row[1]] =$vcnt;
                                                                        $vg = $vcnt;
                                                                    }

                                                                }
                                                                $saldog2 -= $vg;
                                                                if($vg >0){
                                                                    $tercer = $row[1];
                                                                    $valordebito = 0;
                                                                    #Débito
                                                                    if ($cdc[1] == 1) {
                                                                        $valordebito = $vg;
                                                                    } else {
                                                                        $valordebito = $vg * -1;
                                                                    }
                                                                    detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1],$tercer, $dcxp);
                                                                }
                                                            }
                                                        }
                                                    }
                                                    #Acumulado
                                                } else {
                                                    #** Consolidado
                                                    $vcnt = $vnc;
                                                    if ($vcnt > 0) {
                                                        ##Valor por naturaleza
                                                        $valordebito = 0;
                                                        #Débito
                                                        if ($cdc[1] == 1) {
                                                            $valordebito = $vcnt;
                                                        } else {
                                                            $valordebito = $vcnt * -1;
                                                        }
                                                        detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1],$tercero, $dcxp);
                                                    }
                                                }
                                            }
                                        }
                                        #**********************************************************************************************************************************************
                                        #Si es clase Informativo E 7
                                        elseif ($row1[4] == 7) { 
                                            ####Si tiene  Entidad Credito
                                            if (!empty($row1[2]) && !empty($cuentac) && !empty($cuentad)) {
                                                $vcnt = $vnc;
                                                if ($vcnt > 0) {
                                                    ##Valor por naturaleza
                                                    $valordebito = 0;
                                                    $valorcredito = 0;
                                                    #Débito
                                                    if ($cdc[1] == 1) {
                                                        $valordebito = $vcnt;
                                                    } else {
                                                        $valordebito = $vcnt * -1;
                                                    }
                                                    #Crédito
                                                    if ($cdc[3] == 2) {
                                                        $valorcredito = $vcnt;
                                                    } else {
                                                        $valorcredito = $vcnt * -1;
                                                    }
                                                    
                                                    detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1],$row1[2], $dcxp);
                                                    detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[3],$row1[2], $dcxp);
                                                }
                                            #Si tiene tipo fondo
                                            } elseif (!empty($row1[1]) && !empty($cuentac) && !empty($cuentad)) {
                                                #Si la interfaz es de tipo detallado o consolidado
                                                #Detallado
                                                if ($row1[3] == 1) {
                                                    $terval = "SELECT DISTINCT n.id_unico, a.tercero, n.valor 
                                                                      FROM gn_novedad n 
                                                                         LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                                         LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                                         LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  
                                                                WHERE  n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion  
                                                                  AND a.tipo = $row1[1]";
                                                    $terval = $mysqli->query($terval);
                                                    $saldog2 = $vnc;
                                                    while ($row = mysqli_fetch_row($terval)) { 
                                                        if($saldog2>0){
                                                            ##Valor por naturaleza
                                                            if (!empty($row[2]) && intval($row[2]) > 0) {
                                                                $vcnt = $row[2];
                                                                if(!empty($tgsif[$row[1]])){
                                                                    if($vcnt > $saldog2){
                                                                        $vg = $saldog2;
                                                                        $tgsif[$row[1]] += $vg;
                                                                    } else {
                                                                        $tgsif[$row[1]] += $vcnt;
                                                                        $vg = $vcnt;
                                                                    }
                                                                } else {
                                                                    if($vcnt>$saldog2){
                                                                        $vg = $saldog2;
                                                                        $tgsif[$row[1]] = $vg;
                                                                    } else {
                                                                        $tgsif[$row[1]] =$vcnt;
                                                                        $vg = $vcnt;
                                                                    }

                                                                }
                                                                $saldog2 -= $vag;
                                                                if($vg >0){
                                                                    $tercer = $row[1];
                                                                    $valordebito = 0;
                                                                     ##Valor por naturaleza
                                                                    $valordebito = 0;
                                                                    $valorcredito = 0;
                                                                    #Débito
                                                                    if ($cdc[1] == 1) {
                                                                        $valordebito = $vg;
                                                                    } else {
                                                                        $valordebito = $vg * -1;
                                                                    }
                                                                    #Crédito
                                                                    if ($cdc[3] == 2) {
                                                                        $valorcredito = $vg;
                                                                    } else {
                                                                        $valorcredito = $vg * -1;
                                                                    }
                                                                    detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1],$tercer, $dcxp);
                                                                    detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[3],$tercer, $dcxp);
                                                                }
                                                            }
                                                        }
                                                    }
                                                #Acumulado
                                                } else {
                                                    ########Tercero#########
                                                    $ter = " SELECT DISTINCT a.tercero   
                                                                         FROM gn_novedad n 
                                                                         LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                                         LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                                         INNER  JOIN gn_afiliacion a ON a.empleado = e.id_unico  
                                                             WHERE  n.periodo = $periodo AND c.id_unico = $row1[0]  AND e.grupogestion = $grupogestion 
                                                                AND a.tipo = $row1[1]";
                                                    $ter = $mysqli->query($ter);
                                                    $saldog2 = $vnc; 
                                                    while ($row2 = mysqli_fetch_row($ter)) {
                                                        $terc = $row2[0];
                                                        ########Valor#########
                                                        $valcnt = "SELECT DISTINCT n.id_unico, n.valor , a.tercero
                                                                         FROM gn_novedad n 
                                                                         LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                                         LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                                         LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  AND c.tipofondo = a.tipo 
                                                                 WHERE n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion AND   a.tercero = $row2[0]";
                                                        $vcnt = $mysqli->query($valcnt);
                                                        $valor = 0;
                                                        if (mysqli_num_rows($vcnt) > 0) {
                                                            while ($valorcnt = mysqli_fetch_row($vcnt)) {
                                                                $valor += $valorcnt[1];
                                                            }
                                                        }
                                                        if($saldog2 > 0){
                                                            $vcnt = $valor;
                                                            if(!empty($tgsie[$terc])){
                                                                if($vcnt > $saldog2){
                                                                    $vg = $saldog2;
                                                                    $tgsie[$terc] += $vg;
                                                                } else {
                                                                    $tgsie[$terc] += $vcnt;
                                                                    $vg = $vcnt;
                                                                }
                                                            } else {
                                                                if($vcnt>$saldog2){
                                                                    $vg = $saldog2;
                                                                    $tgsie[$terc] = $vg;
                                                                } else {
                                                                    $tgsie[$terc] =$vcnt;
                                                                    $vg = $vcnt;
                                                                }
                                                                
                                                            }
                                                            $saldog2 -= $vg;
                                                            if($vg >0){
                                                                $valordebito = 0;
                                                                $valorcredito = 0;
                                                                #Débito
                                                                if ($cdc[1] == 1) {
                                                                    $valordebito = $vg;
                                                                } else {
                                                                    $valordebito = $vg * -1;
                                                                }
                                                                #Crédito
                                                                if ($cdc[3] == 2) {
                                                                    $valorcredito = $vg;
                                                                } else {
                                                                    $valorcredito = $vg * -1;
                                                                }
                                                                detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1],$terc, $dcxp);
                                                                detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[3],$terc, $dcxp);
                                                            }
                                                        }
                                                    }
                                                }
                                            } elseif (!empty($cuentac) && !empty($cuentad)) {
                                                #Si la interfaz es de tipo detallado o consolidado
                                                #Detallado
                                                if ($row1[3] == 1) {
                                                    $terval = "SELECT n.id_unico, e.tercero, n.valor "
                                                            . "FROM gn_novedad n "
                                                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                            . "WHERE  n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion";
                                                    $terval = $mysqli->query($terval);
                                                    $saldog2= $vnc;
                                                    while ($row = mysqli_fetch_row($terval)) {
                                                        if($saldog2>0){
                                                            $tercer = $row[1];
                                                            ##Valor por naturaleza
                                                            if (!empty($row[2]) && intval($row[2]) > 0) {
                                                                $vcnt = $row[2];
                                                                if(!empty($tgsi[$row[1]])){
                                                                    if($vcnt > $saldog2){
                                                                        $vg = $saldog2;
                                                                        $tgsi[$row[1]] += $vg;
                                                                    } else {
                                                                        $tgsi[$row[1]] += $vcnt;
                                                                        $vg = $vcnt;
                                                                    }
                                                                } else {
                                                                    if($vcnt>$saldog2){
                                                                        $vg = $saldog2;
                                                                        $tgsi[$row[1]] = $vg;
                                                                    } else {
                                                                        $tgsi[$row[1]] =$vcnt;
                                                                        $vg = $vcnt;
                                                                    }

                                                                }
                                                                $saldog2 -= $vg;
                                                                if($vg >0){
                                                                    
                                                                    $vcnt = $vg;
                                                                    $tercer = $row[1];
                                                                    $valorcredito = 0;
                                                                    ##Valor por naturaleza
                                                                    $valordebito = 0;
                                                                    $valorcredito = 0;
                                                                    #Débito
                                                                    if ($cdc[1] == 1) {
                                                                        $valordebito = $vcnt;
                                                                    } else {
                                                                        $valordebito = $vcnt * -1;
                                                                    }
                                                                    #Crédito
                                                                    if ($cdc[3] == 2) {
                                                                        $valorcredito = $vcnt;
                                                                    } else {
                                                                        $valorcredito = $vcnt * -1;
                                                                    }
                                                                    detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1],$tercer, $dcxp);
                                                                    detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[3],$tercer, $dcxp);
                                                                }
                                                            }
                                                        }
                                                    }
                                                    #Acumulado
                                                } else {
                                                    #Valor Detalle
                                                    $vcnt = $vnc;
                                                    if ($vcnt > 0) {
                                                        $valordebito = 0;
                                                        $valorcredito = 0;
                                                        #Débito
                                                        if ($cdc[1] == 1) {
                                                            $valordebito = $vcnt;
                                                        } else {
                                                            $valordebito = $vcnt * -1;
                                                        }
                                                        #Crédito
                                                        if ($cdc[3] == 2) {
                                                            $valorcredito = $vcnt;
                                                        } else {
                                                            $valorcredito = $vcnt * -1;
                                                        }
                                                        detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1],$tercero, $dcxp);
                                                        detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[3],$tercero, $dcxp);
                                                    }
                                                }
                                            }
                                        }
                                    } 
                                }
                            } else {
                                if($rowcr[$r][3]==1){
                                    $varb = 'publica';
                                    $varb1 = 'pública';
                                } elseif($rowcr[$r][3]==2){
                                    $varb = 'privada';
                                    $varb1 = 'privada';
                                }
                                #** Buscar El Valor del concepto por tipo de entidad ***#
                                $vcne = $con->Listar("SELECT SUM(n.valor) from gn_novedad n 
                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                LEFT JOIN gn_afiliacion a ON e.id_unico = a.empleado AND a.tipo = c.tipofondo 
                                LEFT JOIN gf_tercero t ON a.tercero = t.id_unico 
                                LEFT JOIN gf_tipo_empresa tc ON tc.id_unico = t.tipoempresa 
                                WHERE n.periodo = $periodo AND n.concepto = ".$row1[0]." 
                                AND e.grupogestion = $grupogestion  
                                AND (LOWER(tc.nombre)='$varb' OR LOWER(tc.nombre)='$varb1')");

                                $sd = apropiacionfecha($rf,$fechaDis) - disponibilidadesfecha($rf,$fechaDis);
                                $vnc = $vcne[0][0];
                                if($sd >= $vnc){
                                    if ($vnc > 0) {
                                        if($saldog>0){
                                            $saldog -=$vnc;
                                            # ** Detalles Disponibilidad 
                                            $ddis = detallespptal($descripcion, $vnc, $iddis, $rf, $cr, $tercero, $sd, 'NULL','NULL');
                                            #*******************************************#
                                            # ** Detalles Registro  
                                            $dreg = detallespptal($descripcion, $vnc, $idreg, $rf, $cr, $tercero, 'NULL',$ddis,'NULL');
                                            #*******************************************#
                                            # ** Detalles Cuenta Por pagar
                                            $clase = 'NULL';
                                            if ($row1[4] == 1) {
                                                $clase = 'devengo';
                                            } else {
                                                $clase = 'informativo';
                                            }
                                            $dcxp = detallespptal($descripcion, $vnc, $idcxp, $rf, $cr, $tercero, 'NULL',$dreg,"'".$clase."'");
                                        

                                            #**************************************************************************************************************#
                                            #*******************    INSERTAR DETALLES CNT   ***************#
                                            #Buscar cuenta debito y credito
                                            $cdc = "SELECT crc.cuenta_debito, cd.naturaleza,  crc.cuenta_credito, "
                                                    . "cc.naturaleza "
                                                    . "FROM gn_concepto_nomina_financiero cfn "
                                                    . "LEFT JOIN gf_concepto_rubro cr ON cfn.concepto_financiero = cr.id_unico "
                                                    . "LEFT JOIN gf_concepto_rubro_cuenta crc ON cr.id_unico = crc.concepto_rubro "
                                                    . "LEFT JOIN gf_cuenta cd ON crc.cuenta_debito = cd.id_unico "
                                                    . "LEFT JOIN gf_cuenta cc On crc.cuenta_credito = cc.id_unico "
                                                    . "WHERE cfn.concepto_nomina = $row1[0] "
                                                    . "AND cfn.parametrizacionanno = $anno "
                                                    . "AND cfn.grupo_gestion = $grupogestion "
                                                    . "AND cr.id_unico = $cr AND cfn.rubro_fuente=$rf ";
                                            $cdc = $mysqli->query($cdc);
                                            $cdc = mysqli_fetch_row($cdc);
                                            $cuentad = $cdc[0]; #*Cuenta Débito
                                            $cuentac = $cdc[2]; #*Cuenta Crédito
                                            $terval = "SELECT DISTINCT n.id_unico, a.tercero, SUM(n.valor)  
                                                        FROM gn_novedad n 
                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                        LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  
                                                        LEFT JOIN gf_tercero t ON a.tercero = t.id_unico 
                                                        LEFT JOIN gf_tipo_empresa tc ON tc.id_unico = t.tipoempresa 
                                                        WHERE  n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion  
                                                        AND a.tipo = $row1[1] 
                                                        AND (LOWER(tc.nombre)='$varb' OR LOWER(tc.nombre)='$varb1')
                                                        GROUP BY t.id_unico ";
                                            $terval = $mysqli->query($terval);
                                            $saldog2 = $vnc;
                                            while ($row = mysqli_fetch_row($terval)) {
                                                if($saldog2>0){
                                                    ##Valor por naturaleza
                                                    if (!empty($row[2]) && intval($row[2]) > 0) {
                                                        $vcnt = $row[2];
                                                        if(!empty($tgsdt[$row[1]])){
                                                            if($vcnt > $saldog2){
                                                                $vg = $saldog2;
                                                                $tgsdt[$row[1]] += $vg;
                                                            } else {
                                                                $tgsdt[$row[1]] += $vcnt;
                                                                $vg = $vcnt;
                                                            }
                                                        } else {
                                                            if($vcnt>$saldog2){
                                                                $vg = $saldog2;
                                                                $tgsdt[$row[1]] = $vg;
                                                            } else {
                                                                $tgsdt[$row[1]] =$vcnt;
                                                                $vg = $vcnt;
                                                            }

                                                        }
                                                        $saldog2 -= $vg;
                                                        if($vg >0){
                                                            $vcnt = $vg;
                                                            $tercer = $row[1];
                                                            ##Valor por naturaleza
                                                            $valordebito = 0;
                                                            $valorcredito = 0;
                                                            #Débito
                                                            if ($cdc[1] == 1) {
                                                                $valordebito = $vcnt;
                                                            } else {
                                                                $valordebito = $vcnt * -1;
                                                            }
                                                            #Crédito
                                                            if ($cdc[3] == 2) {
                                                                $valorcredito = $vcnt;
                                                            } else {
                                                                $valorcredito = $vcnt * -1;
                                                            }
                                                            detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1],$tercer, $dcxp);
                                                            detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[3],$tercer, $dcxp);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $rfa = mysqli_fetch_row($rfa);
                    $rf = $rfa[0]; #*Rubro fuente
                    $cr = $rfa[1]; #*Concepto Rubro 
                    if ($rfa[2] == 2) {
                        $sd = apropiacionfecha($rf,$fechaDis) - disponibilidadesfecha($rf,$fechaDis);
                        #Valor Detalle
                        $vnc = valorconcepto($periodo, $grupogestion, $row1[0]);
                        //var_dump('Concepto:'.$row1[0].' Saldo Rubro '.$sd.' Valor Concepto: '.$vnc);
                        
                        if($sd >= $vnc){
                            if ($vnc > 0) {
                                # ** Detalles Disponibilidad 
                                $ddis = detallespptal($descripcion, $vnc, $iddis, $rf, $cr, $tercero, $sd, 'NULL','NULL');
                                #*******************************************#
                                # ** Detalles Registro  
                                $dreg = detallespptal($descripcion, $vnc, $idreg, $rf, $cr, $tercero, 'NULL',$ddis,'NULL');
                                #*******************************************#
                                # ** Detalles Cuenta Por pagar
                                $clase = 'NULL';
                                if ($row1[4] == 1) {
                                    $clase = 'devengo';
                                } else {
                                    $clase = 'informativo';
                                }
                                $dcxp = detallespptal($descripcion, $vnc, $idcxp, $rf, $cr, $tercero, 'NULL',$dreg,"'".$clase."'");
                                #**************************************************************************************************************#
                                ####*******************INSERTAR DETALLES CNT ***************###########
                                #Buscar cuenta debito y credito
                                $cdc = "SELECT crc.cuenta_debito, cd.naturaleza,  crc.cuenta_credito, "
                                        . "cc.naturaleza "
                                        . "FROM gn_concepto_nomina_financiero cfn "
                                        . "LEFT JOIN gf_concepto_rubro cr ON cfn.concepto_financiero = cr.id_unico "
                                        . "LEFT JOIN gf_concepto_rubro_cuenta crc ON cr.id_unico = crc.concepto_rubro "
                                        . "LEFT JOIN gf_cuenta cd ON crc.cuenta_debito = cd.id_unico "
                                        . "LEFT JOIN gf_cuenta cc On crc.cuenta_credito = cc.id_unico "
                                        . "WHERE cfn.concepto_nomina = $row1[0] "
                                        . "AND cfn.parametrizacionanno = $anno "
                                        . "AND cfn.grupo_gestion = $grupogestion ";
                                $cdc = $mysqli->query($cdc);
                                $cdc = mysqli_fetch_row($cdc);
                                $cuentad = $cdc[0]; #*Cuenta Débito
                                $cuentac = $cdc[2]; #*Cuenta Crédito
                                #**********************************************************************************************************************************************
                                #Si es clase Devengo 1
                                if ($row1[4] == 1) {
                                    ####Si tiene  Entidad Credito
                                    if (!empty($row1[2]) && !empty($cuentac) && !empty($cuentad)) {
                                        #Valor Detalle
                                        $vcnt =  valorconcepto($periodo, $grupogestion, $row1[0]);
                                        if ($vcnt > 0) {
                                            ##Valor por naturaleza
                                            $valordebito = 0;
                                            $valorcredito = 0;
                                            #Débito
                                            if ($cdc[1] == 1) {
                                                $valordebito = $vcnt;
                                            } else {
                                                $valordebito = $vcnt * -1;
                                            }
                                            detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1], $row1[2], $dcxp);
                                        }
                                    } else {
                                        #Si la interfaz es de tipo detallado o consolidado
                                        #Detallado
                                        if ($row1[3] == 1) {
                                            $terval = "SELECT n.id_unico, e.tercero, n.valor "
                                                    . "FROM gn_novedad n "
                                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                    . "WHERE  n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion";
                                            $terval = $mysqli->query($terval);
                                            while ($row = mysqli_fetch_row($terval)) {
                                                ##Valor por naturaleza
                                                if (!empty($row[2]) && intval($row[2]) > 0) {
                                                    $vcnt = $row[2];
                                                    $tercer = $row[1];
                                                    $valordebito = 0;
                                                    #Débito
                                                    if ($cdc[1] == 1) {
                                                        $valordebito = $vcnt;
                                                    } else {
                                                        $valordebito = $vcnt * -1;
                                                    }
                                                    detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1], $tercer, $dcxp);
                                                }
                                            }
                                            #Acumulado
                                        } else {
                                            #Valor Detalle
                                            $vcnt =  valorconcepto($periodo, $grupogestion, $row1[0]);
                                            if ($vcnt > 0) {
                                                ##Valor por naturaleza
                                                $valordebito = 0;
                                                #Débito
                                                if ($cdc[1] == 1) {
                                                    $valordebito = $vcnt;
                                                } else {
                                                    $valordebito = $vcnt * -1;
                                                }
                                                detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1], $tercero, $dcxp);
                                            }
                                        }
                                    }
                                }
                                #**********************************************************************************************************************************************
                                #Si es clase Informativo E 7
                                elseif ($row1[4] == 7) {
                                    ####Si tiene  Entidad Credito
                                    if (!empty($row1[2]) && !empty($cuentac) && !empty($cuentad)) {
                                        #Valor Detalle
                                        $vcnt =  valorconcepto($periodo, $grupogestion, $row1[0]);
                                        if ($vcnt > 0) {
                                            ##Valor por naturaleza
                                            $valordebito = 0;
                                            $valorcredito = 0;
                                            #Débito
                                            if ($cdc[1] == 1) {
                                                $valordebito = $vcnt;
                                            } else {
                                                $valordebito = $vcnt * -1;
                                            }
                                            #Crédito
                                            if ($cdc[3] == 2) {
                                                $valorcredito = $vcnt;
                                            } else {
                                                $valorcredito = $vcnt * -1;
                                            }                                            
                                            detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1], $row1[2], $dcxp);
                                            detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentac, $cdc[3],  $row1[2], $dcxp);
                                        }


                                        #Si tiene tipo fondo
                                    } elseif (!empty($row1[1]) && !empty($cuentac) && !empty($cuentad)) {
                                        #Si la interfaz es de tipo detallado o consolidado
                                        #Detallado
                                        if ($row1[3] == 1) {
                                            $terval = "SELECT DISTINCT n.id_unico, a.tercero, n.valor 
                                                              FROM gn_novedad n 
                                                                 LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                                 LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                                 LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  
                                                        WHERE  n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion  
                                                          AND a.tipo = $row1[1]";
                                            $terval = $mysqli->query($terval);
                                            while ($row = mysqli_fetch_row($terval)) {
                                                ##Valor por naturaleza
                                                if (!empty($row[2]) || $row[2] != 0) {
                                                    $vcnt = $row[2];
                                                    $tercer = $row[1];
                                                    ##Valor por naturaleza
                                                    $valordebito = 0;
                                                    $valorcredito = 0;
                                                    #Débito
                                                    if ($cdc[1] == 1) {
                                                        $valordebito = $vcnt;
                                                    } else {
                                                        $valordebito = $vcnt * -1;
                                                    }
                                                    #Crédito
                                                    if ($cdc[3] == 2) {
                                                        $valorcredito = $vcnt;
                                                    } else {
                                                        $valorcredito = $vcnt * -1;
                                                    }
                                                    detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1], $tercer, $dcxp);
                                                    detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentac, $cdc[3], $row1[2], $dcxp);
                                                }
                                            }
                                            #Acumulado
                                        } else {
                                            ########Tercero#########
                                            $ter = " SELECT DISTINCT a.tercero   
                                                                 FROM gn_novedad n 
                                                                 LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                                 LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                                 INNER  JOIN gn_afiliacion a ON a.empleado = e.id_unico  
                                                     WHERE  n.periodo = $periodo AND c.id_unico = $row1[0]  AND e.grupogestion = $grupogestion 
                                                        AND a.tipo = $row1[1]";
                                            $ter = $mysqli->query($ter);
                                            while ($row2 = mysqli_fetch_row($ter)) {
                                                $terc = $row2[0];
                                                ########Valor#########
                                                $valcnt = "SELECT DISTINCT n.id_unico, n.valor , a.tercero
                                                                 FROM gn_novedad n 
                                                                 LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                                 LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                                 LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  AND c.tipofondo = a.tipo 
                                                         WHERE n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion AND   a.tercero = $row2[0]";
                                                $vcnt = $mysqli->query($valcnt);
                                                $valor = 0;
                                                if (mysqli_num_rows($vcnt) > 0) {
                                                    while ($valorcnt = mysqli_fetch_row($vcnt)) {
                                                        $valor += $valorcnt[1];
                                                    }
                                                }
                                                if ($valor > 0) {
                                                    ##Valor por naturaleza
                                                    ##Valor por naturaleza
                                                    $valordebito = 0;
                                                    $valorcredito = 0;
                                                    #Débito
                                                    if ($cdc[1] == 1) {
                                                        $valordebito = $valor;
                                                    } else {
                                                        $valordebito = $valor * -1;
                                                    }
                                                    #Crédito
                                                    if ($cdc[3] == 2) {
                                                        $valorcredito = $valor;
                                                    } else {
                                                        $valorcredito = $valor * -1;
                                                    }
                                                    detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1], $terc, $dcxp);
                                                    detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentac, $cdc[3], $terc, $dcxp);
                                                }
                                            }
                                        }
                                    } elseif (!empty($cuentac) && !empty($cuentad)) {
                                        #Si la interfaz es de tipo detallado o consolidado
                                        #Detallado
                                        if ($row1[3] == 1) {
                                            $terval = "SELECT n.id_unico, e.tercero, n.valor "
                                                    . "FROM gn_novedad n "
                                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                    . "WHERE  n.periodo = $periodo AND n.concepto = $row1[0]  AND e.grupogestion = $grupogestion";
                                            $terval = $mysqli->query($terval);
                                            while ($row = mysqli_fetch_row($terval)) {
                                                ##Valor por naturaleza
                                                if (!empty($row[2]) || $row[2] != 0) {
                                                    $vcnt = $row[2];
                                                    $tercer = $row[1];
                                                    $valorcredito = 0;
                                                    ##Valor por naturaleza
                                                    $valordebito = 0;
                                                    $valorcredito = 0;
                                                    #Débito
                                                    if ($cdc[1] == 1) {
                                                        $valordebito = $vcnt;
                                                    } else {
                                                        $valordebito = $vcnt * -1;
                                                    }
                                                    #Crédito
                                                    if ($cdc[3] == 2) {
                                                        $valorcredito = $vcnt;
                                                    } else {
                                                        $valorcredito = $vcnt * -1;
                                                    }
                                                    detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1], $tercer, $dcxp);
                                                    detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentac, $cdc[3], $tercer, $dcxp);
                                                }
                                            }
                                            #Acumulado
                                        } else {
                                            #Valor Detalle
                                            $vcnt =  valorconcepto($periodo, $grupogestion, $row1[0]);
                                            if ($vcnt > 0) {
                                                $valordebito = 0;
                                                $valorcredito = 0;
                                                #Débito
                                                if ($cdc[1] == 1) {
                                                    $valordebito = $vcnt;
                                                } else {
                                                    $valordebito = $vcnt * -1;
                                                }
                                                #Crédito
                                                if ($cdc[3] == 2) {
                                                    $valorcredito = $vcnt;
                                                } else {
                                                    $valorcredito = $vcnt * -1;
                                                }
                                                detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentad, $cdc[1], $tercero, $dcxp);
                                                detallecnt($fechaCxp,$descripcion,$valordebito,$idcxpcnt,$cuentac, $cdc[3], $tercero, $dcxp);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
            }
            ####################################################################################################################
            #Insertar Detalles CNT
            #Buscar conceptos 
            $concnt = "SELECT DISTINCT n.concepto , cn.tipofondo, cn.tipoentidadcredito , 
                cn.tipo_interfaz, cn.clase FROM  gn_novedad n 
                               LEFT JOIN gn_concepto cn ON n.concepto = cn.id_unico 
                               LEFT JOIN gn_empleado e 
                               ON n.empleado = e.id_unico 
                               WHERE n.periodo = $periodo AND n.valor !=0  
                               AND e.grupogestion = $grupogestion  
                               AND ( cn.clase =2  OR cn.clase =5) 
                               AND cn.unidadmedida = 1 
                               order by CONCEPTO asc";
            $concnt = $mysqli->query($concnt);
            while ($rowcnt = mysqli_fetch_row($concnt)) {
                #Buscar cuenta debito y credito
                $cdc = "SELECT crc.cuenta_debito, cd.naturaleza,  crc.cuenta_credito, cc.naturaleza 
                        FROM gn_concepto_nomina_financiero cfn 
                        LEFT JOIN gf_concepto_rubro cr ON cfn.concepto_financiero = cr.id_unico 
                        LEFT JOIN gf_concepto_rubro_cuenta crc ON cr.id_unico = crc.concepto_rubro 
                        LEFT JOIN gf_cuenta cd ON crc.cuenta_debito = cd.id_unico 
                        LEFT JOIN gf_cuenta cc On crc.cuenta_credito = cc.id_unico  
                        WHERE cfn.concepto_nomina = $rowcnt[0] 
                        AND cfn.parametrizacionanno = $anno 
                        AND cfn.grupo_gestion = $grupogestion ";
                $cdc = $mysqli->query($cdc);
                $cdc = mysqli_fetch_row($cdc);
                $cuentad = $cdc[0]; #*Cuenta Débito
                $cuentac = $cdc[2]; #*Cuenta Crédito
                #**********************************************************************************************************************************************
                #Si es clase Descuento 2
                if ($rowcnt[4] == 2) {
                    ####Si tiene  Entidad Credito
                    if (!empty($rowcnt[2]) && !empty($cuentac) && !empty($cuentad)) {
                        #Valor Detalle
                        $vcnt = valorconcepto($periodo, $grupogestion, $rowcnt[0]);
                        if ($vcnt > 0) {
                            ##Valor por naturaleza
                            $valorcredito = 0;
                            #Crédito
                            if ($cdc[3] == 2) {
                                $valorcredito = $vcnt;
                            } else {
                                $valorcredito = $vcnt * -1;
                            }
                            detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[3],  $rowcnt[2], 'NULL');
                        }
                        #***Si tiene tipo fondo
                    } elseif (!empty($rowcnt[1]) && !empty($cuentac) && !empty($cuentad)) {
                        #Si la interfaz es de tipo detallado o consolidado
                        #Detallado
                        if ($rowcnt[3] == 1) {
                            $terval = "SELECT DISTINCT n.id_unico, a.tercero, n.valor 
                                             FROM gn_novedad n 
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  AND c.tipofondo = a.tipo 
                                       WHERE  n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                            $terval = $mysqli->query($terval);
                            while ($row = mysqli_fetch_row($terval)) {
                                ##Valor por naturaleza
                                if (!empty($row[2]) || $row[2] != 0) {
                                    $vcnt = $row[2];
                                    $tercer = $row[1];
                                    $valorcredito = 0;
                                    #Crédito
                                    if ($cdc[3] == 2) {
                                        $valorcredito = $vcnt;
                                    } else {
                                        $valorcredito = $vcnt * -1;
                                    }
                                    detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[1],$tercer, 'NULL');
                                }
                            }
                        #Acumulado
                        } else {
                            ########Tercero#########
                            $ter = " SELECT DISTINCT a.tercero   
                                                FROM gn_novedad n 
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  AND c.tipofondo = a.tipo 
                                    WHERE  n.periodo = $periodo AND c.id_unico = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                            $ter = $mysqli->query($ter);
                            while ($row2 = mysqli_fetch_row($ter)) {
                                $terc = $row2[0];
                                ########Valor#########
                                $valcnt = "SELECT DISTINCT n.id_unico, n.valor , a.tercero
                                                FROM gn_novedad n 
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  AND c.tipofondo = a.tipo 
                                        WHERE n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion AND   a.tercero = $row2[0]";
                                $vcnt = $mysqli->query($valcnt);
                                $valor = 0;
                                if (mysqli_num_rows($vcnt) > 0) {
                                    while ($valorcnt = mysqli_fetch_row($vcnt)) {
                                        $valor += $valorcnt[1];
                                    }
                                }
                                if ($valor > 0) {
                                    ##Valor por naturaleza
                                    $valorcredito = 0;
                                    #Crédito
                                    if ($cdc[3] == 2) {
                                        $valorcredito = $valor;
                                    } else {
                                        $valorcredito = $valor * -1;
                                    }
                                    detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[3],  $terc, 'NULL');
                                }
                            }
                        }
                    } else {
                        #Si la interfaz es de tipo detallado o consolidado
                        #Detallado
                        if ($rowcnt[3] == 1) {
                            $terval = "SELECT DISTINCT n.id_unico, e.tercero, n.valor "
                                    . "FROM gn_novedad n "
                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                    . "WHERE  n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                            $terval = $mysqli->query($terval);
                            while ($row = mysqli_fetch_row($terval)) {
                                ##Valor por naturaleza
                                if (!empty($row[2]) || $row[2] != 0) {
                                    $vcnt = $row[2];
                                    $tercer = $row[1];
                                    $valorcredito = 0;
                                    #Crédito
                                    if ($cdc[3] == 2) {
                                        $valorcredito = $vcnt;
                                    } else {
                                        $valorcredito = $vcnt * -1;
                                    }
                                    detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[1],$tercer, 'NULL');
                                }
                            }
                        #Acumulado
                        } else {
                            $vcnt = valorconcepto($periodo, $grupogestion, $rowcnt[0]);
                            if ($vcnt > 0) {
                                ##Valor por naturaleza
                                $valorcredito = 0;
                                #Crédito
                                if ($cdc[3] == 2) {
                                    $valorcredito = $vcnt;
                                } else {
                                    $valorcredito = $vcnt * -1;
                                }
                                detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[3],$tercero, 'NULL');
                            }
                        }
                    }
                }

                #**********************************************************************************************************************************************
                #Si es clase 5 Neto a pagar
                elseif ($rowcnt[4] == 5) {
                    ####Si tiene  Entidad Credito
                    if (!empty($rowcnt[2]) && !empty($cuentac) && !empty($cuentad)) {
                        #Valor Detalle
                        $vcnt = valorconcepto($periodo, $grupogestion, $rowcnt[0]);
                        if ($vcnt > 0) {
                            ##Valor por naturaleza
                            $valorcredito = 0;
                            #Crédito
                            if ($cdc[3] == 2) {
                                $valorcredito = $vcnt;
                            } else {
                                $valorcredito = $vcnt * -1;
                            }
                            detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[3],$rowcnt[2], 'NULL');
                        }
                        #***Si tiene tipo fondo
                    } elseif (!empty($rowcnt[1]) && !empty($cuentac) && !empty($cuentad)) { {
                            #Si la interfaz es de tipo detallado o consolidado
                            #Detallado
                            if ($rowcnt[3] == 1) {
                                $terval = "SELECT DISTINCT n.id_unico, a.tercero, n.valor 
                                             FROM gn_novedad n 
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                INNER JOIN gn_afiliacion a ON a.empleado = e.id_unico  AND c.tipofondo = a.tipo 
                                       WHERE  n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                                $terval = $mysqli->query($terval);
                                while ($row = mysqli_fetch_row($terval)) {
                                    ##Valor por naturaleza
                                    if (!empty($row[2]) || $row[2] != 0) {
                                        $vcnt = $row[2];
                                        $tercer = $row[1];
                                        $valorcredito = 0;
                                        #Crédito
                                        if ($cdc[3] == 2) {
                                            $valorcredito = $vcnt;
                                        } else {
                                            $valorcredito = $vcnt * -1;
                                        }
                                        detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[1],$tercer, 'NULL');
                                    }
                                }
                                #Acumulado
                            } else {
                                ########Tercero#########
                                $ter = " SELECT DISTINCT a.tercero   
                                                FROM gn_novedad n 
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  AND c.tipofondo = a.tipo 
                                    WHERE  n.periodo = $periodo AND c.id_unico = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                                $ter = $mysqli->query($ter);
                                while ($row2 = mysqli_fetch_row($ter)) {
                                    $terc = $row2[0];
                                    ########Valor#########
                                    $valcnt = "SELECT DISTINCT n.id_unico, n.valor , a.tercero
                                                FROM gn_novedad n 
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                                LEFT JOIN gn_afiliacion a ON a.empleado = e.id_unico  AND c.tipofondo = a.tipo 
                                        WHERE n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion AND   a.tercero = $row2[0]";
                                    $vcnt = $mysqli->query($valcnt);
                                    $valor = 0;
                                    if (mysqli_num_rows($vcnt) > 0) {
                                        while ($valorcnt = mysqli_fetch_row($vcnt)) {
                                            $valor += $valorcnt[1];
                                        }
                                    }
                                    if ($valor > 0) {
                                        ##Valor por naturaleza
                                        $valorcredito = 0;
                                        #Crédito
                                        if ($cdc[3] == 2) {
                                            $valorcredito = $valor;
                                        } else {
                                            $valorcredito = $valor * -1;
                                        }
                                        detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[3],$terc, 'NULL');
                                    }
                                }
                            }
                        }
                    } else {
                        #Si la interfaz es de tipo detallado o consolidado
                        #Detallado
                        if ($rowcnt[3] == 1) {
                            $terval = "SELECT DISTINCT n.id_unico, e.tercero, n.valor "
                                    . "FROM gn_novedad n "
                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                    . "WHERE  n.periodo = $periodo AND n.concepto = $rowcnt[0]  AND e.grupogestion = $grupogestion";
                            $terval = $mysqli->query($terval);
                            while ($row = mysqli_fetch_row($terval)) {
                                ##Valor por naturaleza
                                if (!empty($row[2]) || $row[2] != 0) {
                                    $vcnt = $row[2];
                                    $tercer = $row[1];
                                    $valorcredito = 0;
                                    #Crédito
                                    if ($cdc[3] == 2) {
                                        $valorcredito = $vcnt;
                                    } else {
                                        $valorcredito = $vcnt * -1;
                                    }
                                    detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[1],$tercer, 'NULL');
                                }
                            }
                            #Acumulado
                        } else {
                            $vcnt = valorconcepto($periodo, $grupogestion, $rowcnt[0]);
                            if ($vcnt > 0) {
                                ##Valor por naturaleza
                                $valorcredito = 0;
                                #Crédito
                                if ($cdc[3] == 2) {
                                    $valorcredito = $vcnt;
                                } else {
                                    $valorcredito = $vcnt * -1;
                                }
                                detallecnt($fechaCxp,$descripcion,$valorcredito,$idcxpcnt,$cuentac, $cdc[3],$tercero, 'NULL');
                            }
                        }
                    }
                }
            }

        } 
        $datos = array();
        $result = "GN_GENERAR_INTERFAZ.php?periodo=" . md5($periodo) . "&gg=" . md5($grupogestion);
        $datos = array("rta"=>1,"html"=>$result);
        echo json_encode($datos);
        
    break;

}


function detallespptal($descripcion, $valor, $comprobante, $rf, $cr, $tercero, $saldo, $afectado, $clase){
    
    $insert = "INSERT INTO gf_detalle_comprobante_pptal "
            . "(descripcion, valor, comprobantepptal, rubrofuente, "
            . "conceptoRubro, tercero, proyecto,  saldo_disponible, comprobanteafectado,clasenom) "
            . "VALUES ($descripcion, $valor, $comprobante, $rf, "
            . "$cr, $tercero,  2147483647, $saldo, $afectado, $clase )";
    $insert = $GLOBALS['mysqli']->query($insert);
    $id_d = "SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $comprobante";
    $id_d = $GLOBALS['mysqli']->query($id_d);
    $id_d = mysqli_fetch_row($id_d);
    $id_d = $id_d[0];
    return ($id_d);
}
            
function detallecnt($fecha,$descripcion,$valor,$comprobante,$cuenta, $naturaleza,$tercero, $dpptal) {
    global $centro_costo;
    global $con;
    global $panno;
    #* Buscar si el tercero tiene cc asociado
    $row = $con->Listar("SELECT ecc.centro_costo FROM gn_empleado_centro_costo ecc
    LEFT JOIN gn_empleado e ON ecc.empleado = e.id_unico 
    LEFT JOIN gf_centro_costo cc ON ecc.centro_costo = cc.id_unico 
    WHERE e.tercero = $tercero and cc.parametrizacionanno = $panno");
   
    if(!empty($row[0][0])){
        $cc = $row[0][0];
    } else {
        $cc = $centro_costo;
    }

    $insertcntd = "INSERT INTO gf_detalle_comprobante "
            . "(fecha, descripcion, valor, valorejecucion, comprobante, "
            . "cuenta, naturaleza, tercero,  proyecto, detallecomprobantepptal, centrocosto) "
            . "VALUES ('$fecha',$descripcion, $valor,$valor, $comprobante,  "
            . "$cuenta, $naturaleza, $tercero, 2147483647,$dpptal, $cc)";
    $insertcntd = $GLOBALS['mysqli']->query($insertcntd);
	
}

function valorconcepto($periodo, $grupogestion, $concepto){
    $vcnt = "SELECT SUM(n.valor) from gn_novedad n 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            WHERE n.periodo = $periodo AND n.concepto = $concepto  
            AND e.grupogestion = $grupogestion";
    $vcnt = $GLOBALS['mysqli']->query($vcnt);
    $vcnt = mysqli_fetch_row($vcnt);
    $vcnt = $vcnt[0];
    return $vcnt;
}