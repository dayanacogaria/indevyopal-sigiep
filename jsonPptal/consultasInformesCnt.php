<?php 
###############################################################################
#       ******************       Modificaciones      ******************       #
###############################################################################
#13/04/2018 |Erica G.| Case 21 Informe Balance De Apertura
#21/12/2017 |Erica G.| Case 7 y 8 Balance Tesorería , 9 y 19 , Actividad Economica
#05/10/2017 |ERICA G. |CASE 5 Y 6 PARA INFORME ACTIVIDAD ECONOMICA SOCIAL Y FINANCIERA
#23/06/2017 |ERICA G. |ARCHIVO CREADO
###############################################################################
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
require_once('../jsonPptal/funcionesPptal.php');
ini_set('max_execution_time', 0);
session_start(); 
$con        = new ConexionPDO();
$calendario = CAL_GREGORIAN;
$action     = $_REQUEST['action'];
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
switch ($action){
    ############MES INICIAL BALANCE DE PRUEBA###################
    case 1:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, numero, lower(mes) FROM gf_mes WHERE parametrizacionanno = $annio ORDER BY numero ASC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>". ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay meses </option>";
        }
    break;
    ############MES FINAL BALANCE DE PRUEBA###################
    case 2:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, numero, lower(mes) "
                . "FROM gf_mes WHERE parametrizacionanno = $annio ORDER BY numero DESC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>". ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay meses </option>";
        }
    break;
    ############CÓDIGO INICIAL BALANCE DE PRUEBA###################
    case 3:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, codi_cuenta, lower(nombre) FROM gf_cuenta WHERE parametrizacionanno = $annio ORDER BY codi_cuenta ASC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay cuentas </option>";
        }
    break;
    ############CÓDIGO FINAL BALANCE DE PRUEBA###################
    case 4:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, codi_cuenta, lower(nombre) "
                . "FROM gf_cuenta WHERE parametrizacionanno = $annio ORDER BY codi_cuenta DESC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay cuentas </option>";
        }
    break;
    #************Código Inicial Estado Actividad Econimica ****************# 
    case 5: 
        $tipo = $_POST['tipo'];
        if($tipo ==1){
           $ms="SELECT
                id_unico,
                codi_cuenta,
                LOWER(nombre)
            FROM
                gf_cuenta
            WHERE
                parametrizacionanno = $anno AND 
                (codi_cuenta LIKE '1%' OR codi_cuenta LIKE '2%' OR codi_cuenta LIKE '3%') 
            ORDER BY
                codi_cuenta ASC"; 
        }elseif($tipo==2){
           $ms="SELECT
                id_unico,
                codi_cuenta,
                LOWER(nombre)
            FROM
                gf_cuenta
            WHERE
                parametrizacionanno = $anno AND 
                (codi_cuenta LIKE '4%' OR codi_cuenta LIKE '5%' OR codi_cuenta LIKE '6%'  OR codi_cuenta LIKE '7%' )
            ORDER BY
                codi_cuenta ASC"; 
        }
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay cuentas </option>";
        }
    break;
    #************Código Final Estado Actividad Econimica ****************# 
    case 6: 
        $tipo = $_POST['tipo'];
        if($tipo ==1){
           $ms="SELECT
                id_unico,
                codi_cuenta,
                LOWER(nombre)
            FROM
                gf_cuenta
            WHERE
                parametrizacionanno = $anno AND 
                (codi_cuenta LIKE '1%' OR codi_cuenta LIKE '2%' OR codi_cuenta LIKE '3%')
            ORDER BY
                codi_cuenta DESC"; 
        }elseif($tipo==2){
           $ms="SELECT
                id_unico,
                codi_cuenta,
                LOWER(nombre)
            FROM
                gf_cuenta
            WHERE
                parametrizacionanno = $anno AND 
                (codi_cuenta LIKE '4%' OR codi_cuenta LIKE '5%' OR codi_cuenta LIKE '6%'  OR codi_cuenta LIKE '7%' )
            ORDER BY
                codi_cuenta DESC"; 
        }
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay cuentas </option>";
        }
    break;
    #*****Código Inicial Estado Tesorería, Caja y Bancos 
    case 7:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, codi_cuenta, lower(nombre) FROM gf_cuenta 
               WHERE parametrizacionanno = $annio 
               AND (clasecuenta = 11 OR clasecuenta = 12)
               ORDER BY codi_cuenta ASC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay cuentas </option>";
        }
    break;
    #*****Código Final Estado Tesorería, Caja y Bancos 
    case 8:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, codi_cuenta, lower(nombre) FROM gf_cuenta 
               WHERE parametrizacionanno = $annio 
               AND (clasecuenta = 11 OR clasecuenta = 12) 
               ORDER BY codi_cuenta DESC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay cuentas </option>";
        }
    break;
    #*****Código Inicial Estado Actividad Económica, Social y Contable 
    case 9:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, codi_cuenta, lower(nombre) FROM gf_cuenta 
               WHERE parametrizacionanno = $annio 
               AND (clasecuenta = 7 OR clasecuenta = 13)
               ORDER BY codi_cuenta ASC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay cuentas </option>";
        }
    break;
    #*****Código Final Estado Actividad Económica, Social y Contable
    case 10:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, codi_cuenta, lower(nombre) FROM gf_cuenta 
               WHERE parametrizacionanno = $annio 
               AND (clasecuenta = 7 OR clasecuenta = 13)
               ORDER BY codi_cuenta DESC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay cuentas </option>";
        }
    break;
    #*****Código Inicial Informes Gastos Inversion Acumulado
    case 11:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, codi_presupuesto, lower(nombre) FROM gf_rubro_pptal
               WHERE parametrizacionanno = $annio 
               AND (tipoclase = 7 )
                #OR tipoclase = 9 OR tipoclase = 10) 
               AND tipovigencia=1
               ORDER BY codi_presupuesto ASC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay códigos</option>";
        }
    break;
    #*****Código Inicial Informes Gastos Inversion Acumulado
    case 12:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, codi_presupuesto, lower(nombre) FROM gf_rubro_pptal
               WHERE parametrizacionanno = $annio 
               AND tipoclase = 7  
               #OR tipoclase = 9 OR tipoclase = 10 
               AND tipovigencia=1
               ORDER BY codi_presupuesto DESC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay códigos</option>";
        }
    break;
    #*****Fuente
    case 13:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, lower(nombre) FROM gf_fuente 
               WHERE parametrizacionanno = $annio 
               ORDER BY nombre ";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[0]'>".ucwords($row[1])."</option>";
            }
        } else {
            echo "<option value=''>No hay códigos</option>";
        }
    break;
    #*****Código Inicial Informes Ingresos
    case 14:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, codi_presupuesto, lower(nombre) FROM gf_rubro_pptal
               WHERE parametrizacionanno = $annio 
               AND tipoclase = 6 
               ORDER BY codi_presupuesto ASC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay códigos</option>";
        }
    break;
    #*****Código Final Informes Ingresos
    case 15:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, codi_presupuesto, lower(nombre) FROM gf_rubro_pptal
               WHERE parametrizacionanno = $annio 
               AND tipoclase = 6 
               ORDER BY codi_presupuesto DESC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay códigos</option>";
        }
    break;  
    #*****Código Inicial Informes Ejecucion Reservas
    case 16:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, codi_presupuesto, lower(nombre) FROM gf_rubro_pptal
               WHERE parametrizacionanno = $annio 
               AND ((tipoclase = 16) OR (tipoclase=7 AND tipovigencia = 3))
               ORDER BY codi_presupuesto ASC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay códigos</option>";
        }
    break;
    #*****Código Final Informes Ejecucion Reservas
    case 17:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, codi_presupuesto, lower(nombre) FROM gf_rubro_pptal
               WHERE parametrizacionanno = $annio 
               AND ((tipoclase = 16) OR (tipoclase=7 AND tipovigencia = 3))
               ORDER BY codi_presupuesto DESC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay códigos</option>";
        }
    break;
    #*****Código Inicial Informes Ejecucion Cuentas Por Pagar Vigencia Anterior
    case 18:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, codi_presupuesto, lower(nombre) FROM gf_rubro_pptal
               WHERE parametrizacionanno = $annio 
               AND ((tipoclase = 15))
               ORDER BY codi_presupuesto ASC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay códigos</option>";
        }
    break;
    #*****Código Final Informes Ejecucion Cuentas Por Pagar Vigencia Anterior
    case 19:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, codi_presupuesto, lower(nombre) FROM gf_rubro_pptal
               WHERE parametrizacionanno = $annio 
               AND ((tipoclase = 15))
               ORDER BY codi_presupuesto DESC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".$row[1].' - '.ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay códigos</option>";
        }
    break;
    #************* Ciudad Informe Exógenas ************#
    case 20:
        $dep = $_REQUEST['departamento'];
        $ms = "SELECT DISTINCT c.id_unico, c.nombre 
                FROM gf_direccion dr  
                LEFT JOIN gf_ciudad c ON dr.ciudad_direccion = c.id_unico 
               WHERE c.departamento = $dep";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[0]'>".ucwords($row[1])."</option>";
            }
        } else {
            echo "<option value=''>No Hay Ciudades</option>";
        }
    break;
    #**************************************************************************#
    #       *********   Informe Estado de Apertura    *********   #
    case 21:
        $parmanno   = $mysqli->real_escape_string('' . $_POST['sltAnnio'] . '');
        $anno       = anno($parmanno);
        $codigoI    = $mysqli->real_escape_string('' . $_POST['sltcodi'] . '');
        $codigoF    = $mysqli->real_escape_string('' . $_POST['sltcodf'] . '');

        #   *** Vaciar Tabla Temporal   *** #
        $vaciarT    = $con->Listar("TRUNCATE temporal_estado_apertura");
        
        #   *** Consulta Cuentas Según Datos Recibidos *** #
        $row = $con->Listar("SELECT DISTINCT
                    c.id_unico, 
                    c.codi_cuenta,
                    c.nombre,
                    c.naturaleza,
                    ch.codi_cuenta 
                  FROM
                    gf_cuenta c
                  LEFT JOIN
                    gf_cuenta ch ON c.predecesor = ch.id_unico
                  WHERE c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF' 
                    AND c.parametrizacionanno = $parmanno   
                  ORDER BY 
                    c.codi_cuenta DESC");
        #   *** Guardar Datos De Las Cuentas *** #
        for ($i = 0;$i < count($row);$i++) {
            $sql_cons ="INSERT INTO `temporal_estado_apertura` 
            ( `id_cuenta`,  `codi_cuenta`, `nombre`,  
              `cod_predecesor`,`naturaleza`) 
            VALUES (:id_cuenta, :codi_cuenta, :nombre, 
              :cod_predecesor, :naturaleza)";
            $sql_dato = array(
                    array(":id_cuenta",$row[$i][0]),
                    array(":codi_cuenta",$row[$i][1]),
                    array(":nombre",$row[$i][2]),
                    array(":cod_predecesor",$row[$i][4]),
                    array(":naturaleza",$row[$i][3]),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            if(!empty($obj_resp)){
                #var_dump($obj_resp);
            }
        }
        #   *** Consultar Cuentas Ccomprobantes NIIF Y S.I   *** #
        
        $row = $con->Listar("SELECT DISTINCT 
                    c.id_unico, c.codi_cuenta, 
                    c.nombre, c.naturaleza 
                FROM 
                    gf_detalle_comprobante dc 
                LEFT JOIN 
                    gf_cuenta c ON dc.cuenta = c.id_unico 
                LEFT JOIN 
                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN 
                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                WHERE 
                    c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF' 
                    AND c.parametrizacionanno = $parmanno  
                    AND (tc.niif = 1 OR tc.clasecontable = 5)
                ORDER BY 
                    c.codi_cuenta DESC");
        
        for ($a = 0; $a < count($row); $a++) {
            $cuenta     =$row[$a][0];
            $naturaleza =$row[$a][3];
            #   *** Consultar Saldo Inicial *** #
            $saldoI = $con->Listar("SELECT SUM(valor)
                        FROM
                          gf_detalle_comprobante dc
                        LEFT JOIN
                          gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                        LEFT JOIN
                          gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                        LEFT JOIN
                          gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                        WHERE
                          cc.id_unico = '5' AND tc.niif=2 
                          AND dc.cuenta = '$cuenta' 
                          AND cp.parametrizacionanno =$parmanno");
            if($saldoI[0][0]=="" || $saldoI[0][0]=='NULL'){
                $saldoI = 0;
            } else {
                $saldoI = $saldoI[0][0];
            }
            
            #   *** Consultar Tipos De Comprobante NIIF  *** #
            $tcn = $con->Listar("SELECT DISTINCT id_unico FROM gf_tipo_comprobante WHERE niif=1 ORDER BY id_unico ASC");
            
            if(count($tcn)>0){
                $debito[0]   = 0;
                $credito[0]  = 0;
                $debito[1]   = 0;
                $credito[1]  = 0;
                $debito[2]   = 0;
                $credito[2]  = 0;
                $debito[3]   = 0;
                $credito[3]  = 0;
                for ($i2 = 0; $i2 < count($tcn); $i2++) {
                    $tipoComprobante = $tcn[$i2][0];
                    $valorp     = valorapertura($tipoComprobante, $cuenta, '+');
                    $valorn     = valorapertura($tipoComprobante, $cuenta, '-');

                    $debito[$i2]     = 0;
                    $credito[$i2]    = 0;
                    if($naturaleza==1){
                        $debito[$i2]     = $valorp;
                        $credito[$i2]    = $valorn *-1;
                    } else {
                        $credito[$i2]    = $valorp;
                        $debito[$i2]     = $valorn *-1;
                    }
                }
                #       ****    Insertar Valores    ****    #
                $sql_cons ="UPDATE `temporal_estado_apertura` 
                    SET `saldo_inicial` =:saldo_inicial, 
                        `uno_debito`    =:uno_debito, 
                        `uno_credito`   =:uno_credito,
                        `dos_debito`    =:dos_debito, 
                        `dos_credito`   =:dos_credito,
                        `tres_debito`    =:tres_debito, 
                        `tres_credito`   =:tres_credito,
                        `cuatro_debito`    =:cuatro_debito, 
                        `cuatro_credito`   =:cuatro_credito
                    WHERE id_cuenta     =:id_cuenta ";
                $sql_dato = array(
                        array(":saldo_inicial",$saldoI),
                        array(":uno_debito",$debito[0]),
                        array(":uno_credito",$credito[0]),
                        array(":dos_debito",$debito[1]),
                        array(":dos_credito",$credito[1]),
                        array(":tres_debito",$debito[2]),
                        array(":tres_credito",$credito[2]),
                        array(":cuatro_debito",$debito[3]),
                        array(":cuatro_credito",$credito[3]),
                        array(":id_cuenta",$cuenta),
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                if(!empty($obj_resp)){
                    #var_dump($obj_resp);
                }
            }
        }
        
        #   ***     Acumular    ***     #
        #CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
        $rowm = $con->Listar("SELECT 
                id_cuenta,
                codi_cuenta, 
                cod_predecesor, 
                saldo_inicial,
                uno_debito, 
                uno_credito,
                dos_debito, 
                dos_credito,
                tres_debito, 
                tres_credito,
                cuatro_debito, 
                cuatro_credito 
            FROM 
                temporal_estado_apertura 
            ORDER BY 
                codi_cuenta DESC ");
        for ($m = 0; $m < count($rowm); $m++) {
            $id_cuenta      = $rowm[$m][0];
            $row = $con->Listar("SELECT 
                id_cuenta,
                codi_cuenta, 
                cod_predecesor, 
                saldo_inicial,
                uno_debito, 
                uno_credito,
                dos_debito, 
                dos_credito,
                tres_debito, 
                tres_credito,
                cuatro_debito, 
                cuatro_credito 
            FROM 
                temporal_estado_apertura 
            WHERE  
                id_cuenta = $id_cuenta");
            for($i=0; $i < count($row); $i++){
            #   *** Buscar Si Hay Predecesor, Datos P   ***     #
                $cod_predecesor = $row[$i][2];
                if(!empty($cod_predecesor)){
                    $rowp = $con->Listar("SELECT 
                        id_cuenta,
                        codi_cuenta, 
                        cod_predecesor, 
                        saldo_inicial,
                        uno_debito, 
                        uno_credito,
                        dos_debito, 
                        dos_credito,
                        tres_debito, 
                        tres_credito,
                        cuatro_debito, 
                        cuatro_credito 
                    FROM 
                        temporal_estado_apertura 
                    WHERE 
                        codi_cuenta = '$cod_predecesor'
                    ORDER BY 
                        codi_cuenta DESC ");

                    if(count($rowp)>0){
                        $id_cuenta      = $rowp[0][0];
                        $saldo_inicial  = $row[$i][3] + $rowp[0][3];
                        $uno_debito     = $row[$i][4] + $rowp[0][4];
                        $uno_credito    = $row[$i][5] + $rowp[0][5];
                        $dos_debito     = $row[$i][6] + $rowp[0][6];
                        $dos_credito    = $row[$i][7] + $rowp[0][7];
                        $tres_debito    = $row[$i][8] + $rowp[0][8];
                        $tres_credito   = $row[$i][9] + $rowp[0][9];
                        $cuatro_debito  = $row[$i][10]+ $rowp[0][10];
                        $cuatro_credito = $row[$i][11]+ $rowp[0][11];


                        $sql_cons ="UPDATE `temporal_estado_apertura` 
                            SET `saldo_inicial` =:saldo_inicial, 
                                `uno_debito`    =:uno_debito, 
                                `uno_credito`   =:uno_credito,
                                `dos_debito`    =:dos_debito, 
                                `dos_credito`   =:dos_credito,
                                `tres_debito`    =:tres_debito, 
                                `tres_credito`   =:tres_credito,
                                `cuatro_debito`    =:cuatro_debito, 
                                `cuatro_credito`   =:cuatro_credito
                            WHERE id_cuenta     =:id_cuenta ";
                        $sql_dato = array(
                                array(":saldo_inicial",$saldo_inicial),
                                array(":uno_debito",$uno_debito),
                                array(":uno_credito",$uno_credito),
                                array(":dos_debito",$dos_debito),
                                array(":dos_credito",$dos_credito),
                                array(":tres_debito",$tres_debito),
                                array(":tres_credito",$tres_credito),
                                array(":cuatro_debito",$cuatro_debito),
                                array(":cuatro_credito",$cuatro_credito),
                                array(":id_cuenta",$id_cuenta),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        
                        if(!empty($obj_resp)){
                            var_dump($obj_resp);
                        }

                    }
                }
            }
        } 
    break;
    # *** Sector Rubros De Gastos ***# 
    case 22:
        $an  = $_REQUEST['annio'];
        $row = $con->Listar("SELECT DISTINCT s.* 
        FROM 
            gf_sector s 
        LEFT JOIN 
            gf_rubro_pptal rb ON rb.sector = s.id_unico 
        WHERE 
            rb.parametrizacionanno = $an AND (rb.tipoclase = 7 OR rb.tipoclase = 8 OR rb.tipoclase = 9)");
        if(count($row)>0){
            for ($i = 0; $i < count($row);$i++) {
                echo '<option value="'.$row[$i][0].'">'.$row[$i][1].'</option>';
            }
        } else {
            echo "<option value=''>No hay Sectores</option>";
        }
    break;
    # ******* Informe Gerencial Gastos Sector ********** #
    
    #*****Fuente Inicial 
    case 23:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, lower(nombre) FROM gf_fuente 
               WHERE parametrizacionanno = $annio 
               ORDER BY id_unico ASC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[0]'>".ucwords($row[1])."</option>";
            }
        } else {
            echo "<option value=''>No hay códigos</option>";
        }
    break;
    #*****Fuente Final
    case 24:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, lower(nombre) FROM gf_fuente 
               WHERE parametrizacionanno = $annio 
               ORDER BY id_unico DESC ";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[0]'>".ucwords($row[1])."</option>";
            }
        } else {
            echo "<option value=''>No hay códigos</option>";
        }
    break;
    #Consulta Proyectos
    case 25:
        $annio = $_POST['annio'];
        $orden = $_POST['orden'];
        $ms = "SELECT id_unico, lower(nombre) 
                FROM gf_proyecto
               WHERE compania = $compania 
               ORDER BY id_unico $orden ";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[0]'>".ucwords($row[1])."</option>";
            }
        } else {
            echo "<option value=''>No hay proyectos</option>";
        }
    break;
    #Consulta Proyectos FYP
    case 26:
        $ms = "SELECT pf.id_unico, lower(pf.nombre),  pr.id_unico 
            FROM gy_proyecto pr 
            LEFT JOIN gf_proyecto pf ON pf.id_unico = pr.id_proyecto 
        WHERE pr.compania = $compania
        ORDER BY pr.titulo ASC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo '<option value="'.$row[0].','.$row[2].'">'.ucwords($row[1]).'</option>';
            }
        } else {
            echo "<option value=''>No hay proyectos</option>";
        }
    break;
}

function valorapertura($tipoc, $cuenta, $s){
    @session_start();
    $anno       = $_SESSION['anno'];
    if($s=='+'){ 
        $vl     = "SELECT SUM(valor)
            FROM
              gf_detalle_comprobante dc
            LEFT JOIN
              gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
            LEFT JOIN
              gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
            WHERE valor>0 
              AND tc.id_unico = '$tipoc' 
              AND dc.cuenta = '$cuenta' AND cp.parametrizacionanno =$anno ";
    } else {
        $vl     = "SELECT SUM(valor)
            FROM
              gf_detalle_comprobante dc
            LEFT JOIN
              gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
            LEFT JOIN
              gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
            WHERE valor<0 
              AND tc.id_unico = '$tipoc' 
              AND dc.cuenta = '$cuenta' AND cp.parametrizacionanno =$anno ";
    }
    $vl = $GLOBALS['mysqli']->query($vl);
    $valor  = 0;
    if(mysqli_num_rows($vl)!=0){
        $vlr = mysqli_fetch_row($vl);
        if($vlr[0]=="" || $vlr[0]=='NULL'){
            $valor =  0;
        } else {
            $valor = $vlr[0];
        }
    }
    
    return $valor;
    
}