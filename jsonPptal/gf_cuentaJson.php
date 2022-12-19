<?php
######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#05/02/2018 | Erica G. | Equivalente Vigencia Anterior
#03/01/2017 | Erica G. | Parametrizacion Año Cuenta Bancaria
#14/10/2017 | Erica G. | Archivo Creado 
#######################################################################################################
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
require_once './funcionesPptal.php';
session_start();
$con = new ConexionPDO();
$anio= $_SESSION['anno'];
$com = $_SESSION['compania'];
ini_set('max_execution_time', 0);
switch ($_REQUEST['action']){
    #**********Registrar Cuenta************#
    case 1:
        $resultado=1;
        $id =0;
        $codiC  = '"'.$mysqli->real_escape_string(''.$_POST['txtCodigoC'].'').'"';
        $nombre  = '"'.$mysqli->real_escape_string(''.$_POST['txtNombre'].'').'"';
        $natural  = '"'.$mysqli->real_escape_string(''.$_POST['sltNaturaleza'].'').'"';
        $claseC  = '"'.$mysqli->real_escape_string(''.$_POST['sltClaseC'].'').'"';
        $mov  = '"'.$mysqli->real_escape_string(''.$_POST['optMov'].'').'"';
        $cen  = '"'.$mysqli->real_escape_string(''.$_POST['optCentro'].'').'"';
        $auxT  = '"'.$mysqli->real_escape_string(''.$_POST['optAuxT'].'').'"';
        $auxP  = '"'.$mysqli->real_escape_string(''.$_POST['optAuxP'].'').'"';
        $activ  = '"'.$mysqli->real_escape_string(''.$_POST['optAct'].'').'"';
        $din  = '"'.$mysqli->real_escape_string(''.$_POST['txtDinamica'].'').'"';

        if(($_POST['sltTipoCuentaCgn'])!='""' || empty($_POST['sltTipoCuentaCgn']) || $_POST['sltTipoCuentaCgn']=='"Tipo Cuenta CGN"'){
            $tipoCGN  = 'NULL';
        }else{    
            $tipoCGN  = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoCuentaCgn'].'').'"';
        }

        if($_POST['sltPredecesor']=='""' || empty($_POST['sltPredecesor'])){
            $pre  = 'NULL';
        }else{
            $pre  = '"'.$mysqli->real_escape_string(''.$_POST['sltPredecesor'].'').'"';
        }
        
        if($_POST['sltEquivalente']=='""' || empty($_POST['sltEquivalente'])){
            $equivalente  = 'NULL';
        }else{
            $equivalente  = '"'.$mysqli->real_escape_string(''.$_POST['sltEquivalente'].'').'"';
        }
        
        $paramA = $_SESSION['anno'];

        $sql = "INSERT INTO gf_cuenta(codi_cuenta,nombre,naturaleza,clasecuenta,movimiento,"
                . "centrocosto,auxiliartercero,auxiliarproyecto,activa,dinamica,parametrizacionanno,"
                . "tipocuentacgn,predecesor, equivalente_va) "
                . "VALUES ($codiC,$nombre,$natural,$claseC,$mov,$cen,$auxT,$auxP,"
                . "$activ,$din,$paramA,$tipoCGN,$pre, $equivalente)";
        $rs = $mysqli->query($sql);
        if($rs ==true){
            $resultado =1;
            $bI = "SELECT MAX(id_unico) FROM gf_cuenta WHERE codi_cuenta = $codiC";
            $bI = $mysqli->query($bI);
            $bI = mysqli_fetch_row($bI);
            $id = $bI[0];
            $_SESSION['cuenta']=$id;
        } else {
            $resultado =2;
        }
        $datos = array("respuesta"=>$resultado,"id"=>$id);

        echo json_encode($datos); 
    break;
    #************Guardar Cuenta Bancaria Tercero****#
    case 2:
        $resultado=0;
        $id =0;
        $id_cuenta = $_REQUEST['id'];
        $banco = $_POST['banco'];
        $numero = $_POST['numcuenta'];
        $tercero = $_POST['tercero'];
        $insert ="INSERT INTO gf_cuenta_bancaria (numerocuenta, banco, cuenta, parametrizacionanno) "
                . "VALUES('$numero', $banco, $id_cuenta, $anio)";
        $insert = $mysqli->query($insert);
        if($insert==true){
            $bcb = "SELECT MAX(id_unico) FROM gf_cuenta_bancaria "
                    . "WHERE numerocuenta = '$numero' AND banco = $banco AND cuenta = $id_cuenta";
            $bcb = $mysqli->query($bcb);
            $bcb = mysqli_fetch_row($bcb);
            $cuentaB = $bcb[0];
            #*******Insertar cuenta bancaria tercero***********#
            $insertct = "INSERT INTO gf_cuenta_bancaria_tercero (cuentabancaria, tercero)
                    VALUES($cuentaB, $tercero)  ";
            $insertct = $mysqli->query($insertct );
            if($insertct == true){
                $resultado=1;
                if($com==$tercero){
                    $_SESSION['tipo_perfil']='Compañía';
                } else {
                    $_SESSION['tipo_perfil']='Otros';
                }
                $_SESSION['id_tercero']=$tercero;
            } else {
                $resultado=3;
            }
        } else {
            $resultado=2;
        }
        $datos = array("respuesta"=>$resultado,"id"=>$id);

        echo json_encode($datos);
    break;
    #************Guardar y Modificar Equivalencia VA ************#
    case 3:
        $cuenta = $_POST['cuenta'];
        $cuentava = $_POST['cuentava'];
        
        $upd = "UPDATE gf_cuenta SET equivalente_va = $cuentava WHERE id_unico = $cuenta";
        $upd = $mysqli->query($upd);
        
        echo $upd;
    break;
    #************Eliminar Equivalencia VA************#
    case 4:
        $cuenta = $_POST['id'];
        
        $upd = "UPDATE gf_cuenta SET equivalente_va = NULL WHERE id_unico = $cuenta";
        $upd = $mysqli->query($upd);
        
        echo $upd;
    break;

    case 5:
        require_once './gf_style_tabla.php';
        $numa = anno($anio);
        $annova = $numa-1;
        $bsca = $con->Listar("SELECT id_unico FROM gf_parametrizacion_anno 
                WHERE anno = '$annova'");
        $annoa = $bsca[0][0];
        generar_balance_final($annoa, $annova);
        $row = $con->Listar("SELECT
            id_cuenta,
            numero_cuenta,
            cod_predecesor,
            nuevo_saldo_debito,
            nuevo_saldo_credito 
        FROM
            temporal_consulta_tesoreria 
        ORDER BY
            numero_cuenta
        ASC");
        $cuenta = array();
        $c = 0;
        for ($i = 0; $i < count($row); $i++) {
            $id_c      = $row[$i][0];
            $cod_c     = $row[$i][1];
            $nuevo_sd  = $row[$i][3];
            $nuevo_sc  = $row[$i][4];
            $and       =0;
            #******* Buscar Si Existe Cuenta Plan Año Actual ********#
            $cac = $con->Listar("SELECT  *
                    FROM gf_cuenta 
                    WHERE parametrizacionanno = $anio 
                    AND codi_cuenta = $cod_c");
            if(count($cac)>0){}else {
                if($nuevo_sd > 0 || $nuevo_sc > 0){
                    $and =1; 
                } else {
                    # Buscar Si Es Predecesor De Alguna Cuenta Con Saldo 
                    $pr = $con->Listar("SELECT
                        id_cuenta,
                        numero_cuenta,
                        cod_predecesor,
                        nuevo_saldo_debito,
                        nuevo_saldo_credito 
                    FROM
                        temporal_consulta_tesoreria  
                    WHERE cod_predecesor = $cod_c 
                    AND (
                       (nuevo_saldo_debito  !='' OR nuevo_saldo_debito !=0.00) OR  
                       (nuevo_saldo_credito !='' OR nuevo_saldo_credito !=0.00) 
                     )");
                    if(count($pr)>0){
                        $and =1;
                    }   
                }
                if($and==1){
                   $cuenta[$c]= $id_c;
                   $c++; 
                }
            }
        }
        $cuentas = implode(",",$cuenta);
        $ctas  =$con->Listar("SELECT id_unico, codi_cuenta, LOWER(nombre) FROM 
                gf_cuenta WHERE parametrizacionanno = $annoa 
                AND id_unico IN ($cuentas) ORDER BY codi_cuenta ASC ");
        $html  ="";
        $html .= '<div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">';
        $html .= '<div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">';
        $html .= '<table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<td style="display: none;">Identificador</td>';
        $html .= '<td width="30px" align="center"></td>';
        $html .= '<td><strong>Cuenta '.$annova.'</strong></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<th style="display: none;">Identificador</th>';
        $html .= '<th width="7%"></th>';
        $html .= '<th>Cuenta '.$annova.'</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        if(count($ctas)>0){
            for ($i = 0; $i < count($ctas); $i++) {
                $html .= '<tr>';
                $html .= '<td style="display: none;">'.$ctas[$i][1].'</td>';
                $html .= '<td>';
                $html .= '</td>';
                $html .= '<td>'.$ctas[$i][1].' - '.ucwords($ctas[$i][2]).'</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody>';
        $html .= '</table> ';
        $html .= '<input type="hidden" id="cuentas" name="cuentas" value="'.$cuentas.'">';
        $html .= '<div align="right"><button onclick="guardarCuentas()" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Cuentas</button> </div>';
        $html .= '</div> ';
        $html .= '</div> ';
        echo $html;
    break;    
    #Crear Cuentas Faltantes
    case 6:
        $cuenta = $_REQUEST['cuentas'];
        $c =0;
        $row = $con->Listar("SELECT * FROM gf_cuenta WHERE id_unico IN ($cuenta)");
        for ($i = 0; $i < count($row); $i++) {
            $cod    = $row[$i][1];
            $nomb   = $row[$i][2];
            $mov    = $row[$i][3];
            $cc     = $row[$i][4];
            $ter    = $row[$i][5];
            $pro    = $row[$i][6];
            $par    = $anio;
            $act    = $row[$i][8];
            $nat    = $row[$i][10];
            $cgn    = $row[$i][12];
            $clase  = $row[$i][13];

            $sql_cons ="INSERT INTO `gf_cuenta` 
            ( `codi_cuenta`, `nombre`,
            `movimiento`, `centrocosto`, 
            `auxiliartercero`,`auxiliarproyecto`,
            `parametrizacionanno`,`activa`,
            `naturaleza`,`tipocuentacgn`,
            `clasecuenta`) 
            VALUES (:codi_cuenta, :nombre, 
            :movimiento,:centrocosto,
            :auxiliartercero,:auxiliarproyecto,
            :parametrizacionanno,:activa,
            :naturaleza,:tipocuentacgn,
            :clasecuenta)";
            $sql_dato = array(
                array(":codi_cuenta",$cod),
                array(":nombre",$nomb),
                array(":movimiento",$mov),
                array(":centrocosto",$cc),
                array(":auxiliartercero",$ter),
                array(":auxiliarproyecto",$pro),
                array(":parametrizacionanno",$par),
                array(":activa",$act),
                array(":naturaleza",$nat),
                array(":tipocuentacgn",$cgn),
                array(":clasecuenta",$clase),
                
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                $c++;
            }
        }
        echo $c;
        
    break;
}

function generar_balance_final($anno, $numa){
    global $con;
    $parmanno = $anno;
    $anno = $numa; 
    $mesI = '01';
    $diaI = '01';
    $fechaInicial = $anno.'-'.$mesI.'-'.$diaI;
    $mesF = '12';
    $diaF = '31'; 
    $fechaFinal = $anno.'-'.$mesF.'-'.$diaF;
    $fechaComparar = $anno.'-'.'01-01';
    $codigoI = 1;
    $codigoF = 99;
    #VACIAR LA TABLA TEMPORAL
    $vaciarTabla = 'TRUNCATE temporal_consulta_tesoreria ';
    $GLOBALS['mysqli']->query($vaciarTabla);

    #CONSULTA CUENTAS SEGUN VARIABLES QUE RECIBE
     $select ="SELECT DISTINCT
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
                c.codi_cuenta DESC";
    $select1 = $GLOBALS['mysqli']->query($select);

    while($row = mysqli_fetch_row($select1)){
        #GUARDA LOS DATOS EN LA TABLA TEMPORAL
        $insert= "INSERT INTO temporal_consulta_tesoreria "
                . "(id_cuenta, numero_cuenta, nombre,cod_predecesor, naturaleza) "
                . "VALUES ('$row[0]','$row[1]','$row[2]','$row[4]','$row[3]' )";
        $GLOBALS['mysqli']->query($insert);
    } 
    #CONSULTO LAS CUENTAS QUE TENGAN MOVIMIENTO
    $mov = "SELECT DISTINCT c.id_unico, c.codi_cuenta, "
            . "c.nombre, c.naturaleza FROM gf_detalle_comprobante dc "
            . "LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico "
            . "WHERE c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF' AND c.parametrizacionanno = $parmanno "
            . "ORDER BY c.codi_cuenta DESC";
    $mov= $GLOBALS['mysqli']->query($mov);
    $totaldeb=0;
    $totalcred=0;
    $totalsaldoID =0;
    $totalsaldoIC =0;
    $totalsaldoFD =0;
    $totalsaldoFC =0;
    while($row = mysqli_fetch_row($mov)){
        #****************************** CALCULAR SALDO INICIAL **********************************#
        #SI FECHA INICIAL =01 DE ENERO
        $fechaPrimera = $anno.'-01-01';
        #CONSULTA EL SALDO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
        $fechaMax = $anno.'-12-31';
        ##############SALDO DEBITO###########
         $com= "SELECT SUM(valor)
                FROM
                  gf_detalle_comprobante dc
                LEFT JOIN
                  gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                LEFT JOIN
                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                WHERE valor>0 AND 
                  cp.fecha BETWEEN '$fechaInicial' AND '$fechaMax' 
                  AND cc.id_unico = '5' 
                  AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno";
        $com = $GLOBALS['mysqli']->query($com);
        if(mysqli_num_rows($com)>0) {
          $saldo = mysqli_fetch_row($com);
          if(($saldo[0]=="" || $saldo[0]=='NULL')){
              $saldodebito = 0;
          } else {
              $saldodebito = $saldo[0];
          }
        } else {
              $saldodebito=0;
        }
        ##############SALDO CREDITO###########
         $com= "SELECT SUM(valor)
                FROM
                  gf_detalle_comprobante dc
                LEFT JOIN
                  gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                LEFT JOIN
                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                WHERE valor<0 AND 
                  cp.fecha BETWEEN '$fechaInicial' AND '$fechaMax' 
                  AND cc.id_unico = '5' 
                  AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno";
        $com = $GLOBALS['mysqli']->query($com);
        if(mysqli_num_rows($com)>0) {
          $saldo = mysqli_fetch_row($com);
          if($saldo[0]=="" || $saldo[0]=='NULL'){
              $saldocredito = 0;
          } else {
              $saldocredito = $saldo[0];
          }

        } else {
            $saldocredito=0;
        }    

        #DEBITOS
         $deb="SELECT SUM(valor)
                FROM
                  gf_detalle_comprobante dc
                LEFT JOIN
                  gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                LEFT JOIN
                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                WHERE valor>0 AND 
                  cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                  AND cc.id_unico != '5' AND cc.id_unico !='20'  
                  AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno ";
        $debt = $GLOBALS['mysqli']->query($deb);
        if(mysqli_num_rows($debt)>0){
        $debito = mysqli_fetch_row($debt);
            if($debito[0]=="" || $debito[0]=='NULL'){
                $debito =  0;
            } else {
                $debito = $debito[0];
            }

        } else {
            $debito=0;
        }    
        #CREDITOS
        $cr = "SELECT SUM(valor)
                FROM
                  gf_detalle_comprobante dc
                LEFT JOIN
                  gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                LEFT JOIN
                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                WHERE valor<0 AND 
                  cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                  AND cc.id_unico != '5' AND cc.id_unico !='20' 
                  AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno";
        $cred = $GLOBALS['mysqli']->query($cr);
        if(mysqli_num_rows($cred)>0){
            $credito = mysqli_fetch_row($cred);
            if($credito[0]=="" || $credito[0]=='NULL'){
                $credito=0;
            } else {
                $credito = $credito[0];
            }

        } else {
            $credito=0;
        }
        if($debito<0){
            $debito = $debito *-1;
        }
        if($credito<0){
            $credito = $credito *-1;
        }
        if($saldodebito<0){
            $saldodebito = $saldodebito*-1;
        }
        if($saldocredito<0){
            $saldocredito = $saldocredito*-1;
        }

        #SI LA NATURALEZA ES DEBITO
        if($row[3]=='1'){

            $saldoNuevo =($saldodebito+$debito)-($saldocredito+$credito);

            if($saldoNuevo > 0){
                $nuevoSaldodebito = $saldoNuevo;
                $nuevoSaldoCredito = 0;
            } else {
                $nuevoSaldoCredito = $saldoNuevo*-1;
                $nuevoSaldodebito = 0;
            }
            $sid =$nuevoSaldodebito;
            $sic =$nuevoSaldoCredito;
        #SI LA NATURALEZA ES CREDITO
        }else{
            $saldoNuevo =($saldodebito+$debito)-($saldocredito+$credito);

            if($saldoNuevo > 0){
                $nuevoSaldodebito = $saldoNuevo;
                $nuevoSaldoCredito = 0;
            } else {
                $nuevoSaldoCredito = $saldoNuevo*-1;
                $nuevoSaldodebito = 0;
            }

            $sid =$nuevoSaldoCredito;
            $sic =$nuevoSaldodebito;

        }
        $saldodebitoInicial  =$sid;
        $saldocreditoInicial =$sic;
        #****************************** FIN CALCULAR SALDO INICIAL **********************************#
        #****************************** CALCULAR DEBITOS Y CREDITOS  **********************************#
        #DEBITOS
         $deb="SELECT SUM(valor)
                FROM
                  gf_detalle_comprobante dc
                LEFT JOIN
                  gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                LEFT JOIN
                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                WHERE valor>0 AND 
                  cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                  AND  cc.id_unico ='20'  
                  AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno ";
        $debt = $GLOBALS['mysqli']->query($deb);
        if(mysqli_num_rows($debt)>0){
        $debito = mysqli_fetch_row($debt);
            if($debito[0]=="" || $debito[0]=='NULL'){
                $debito =  0;
            } else {
                $debito = $debito[0];
            }

        } else {
            $debito=0;
        }    
        #CREDITOS
        $cr = "SELECT SUM(valor)
                FROM
                  gf_detalle_comprobante dc
                LEFT JOIN
                  gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                LEFT JOIN
                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                WHERE valor<0 AND 
                  cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                  AND cc.id_unico ='20' 
                  AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno";
        $cred = $GLOBALS['mysqli']->query($cr);
        if(mysqli_num_rows($cred)>0){
            $credito = mysqli_fetch_row($cred);
            if($credito[0]=="" || $credito[0]=='NULL'){
                $credito=0;
            } else {
                $credito = $credito[0];
            }

        } else {
            $credito=0;
        }
        if($debito<0){
            $debito = $debito *-1;
        }
        if($credito<0){
            $credito = $credito *-1;
        }
        #SI LA NATURALEZA ES DEBITO
        if($row[3]=='1'){
            $debitoFinal  = $debito;
            $creditoFinal = $credito;
        } ELSE {
            $debitoFinal  = $credito;
            $creditoFinal = $debito;
        }
        #****************************** FIN CALCULAR DEBITOS Y CREDITOS  **********************************#
        #******************************  CALCULAR SALDO FINAL  **********************************#
        $fechaPrimera = $anno.'-01-01';
        #CONSULTA EL SALDO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
        $fechaMax = $anno.'-12-31';
        #DEBITOS
         $deb="SELECT SUM(valor)
                FROM
                  gf_detalle_comprobante dc
                LEFT JOIN
                  gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                LEFT JOIN
                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                WHERE valor>0 AND 
                  cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                  AND cc.id_unico != '5'  
                  AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno ";
        $debt = $GLOBALS['mysqli']->query($deb);
        if(mysqli_num_rows($debt)>0){
        $debito = mysqli_fetch_row($debt);
            if($debito[0]=="" || $debito[0]=='NULL'){
                $debito =  0;
            } else {
                $debito = $debito[0];
            }

        } else {
            $debito=0;
        }

        #CREDITOS
       $cr = "SELECT SUM(valor)
                FROM
                  gf_detalle_comprobante dc
                LEFT JOIN
                  gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                LEFT JOIN
                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                WHERE valor<0 AND 
                  cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                  AND cc.id_unico != '5' 
                  AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno";
        $cred = $GLOBALS['mysqli']->query($cr);
        if(mysqli_num_rows($cred)>0){
            $credito = mysqli_fetch_row($cred);
            if($credito[0]=="" || $credito[0]=='NULL'){
                $credito=0;
            } else {
                $credito = $credito[0];
            }

        } else {
            $credito=0;
        }

        if($debito<0){
            $debito = $debito *-1;
        }
        if($credito<0){
            $credito = $credito *-1;
        }
        if($saldodebito<0){
            $saldodebito = $saldodebito*-1;
        }
        if($saldocredito<0){
            $saldocredito = $saldocredito*-1;
        }

        #SI LA NATURALEZA ES DEBITO
        if($row[3]=='1'){

            $saldoNuevo =($saldodebito+$debito)-($saldocredito+$credito);

            if($saldoNuevo > 0){
                $nuevoSaldodebito = $saldoNuevo;
                $nuevoSaldoCredito = 0;
            } else {
                $nuevoSaldoCredito = $saldoNuevo*-1;
                $nuevoSaldodebito = 0;
            }
            $saldoFinalDebito  =$nuevoSaldodebito;
            $saldoFinalCredito =$nuevoSaldoCredito;
        #SI LA NATURALEZA ES CREDITO
        }else{
            $saldoNuevo =($saldodebito+$debito)-($saldocredito+$credito);

            if($saldoNuevo > 0){
                $nuevoSaldodebito = $saldoNuevo;
                $nuevoSaldoCredito = 0;
            } else {
                $nuevoSaldoCredito = $saldoNuevo*-1;
                $nuevoSaldodebito = 0;
            }
            $saldoFinalDebito  =$nuevoSaldoCredito;
            $saldoFinalCredito =$nuevoSaldodebito;
        }


        #****************************** FIN CALCULAR SALDO FINAL  **********************************#
        #****************************** ACTUALIZAR DATOS **********************************#
        $update = "UPDATE temporal_consulta_tesoreria "
                . "SET saldo_inicial_debito ='$saldodebitoInicial', "
                . "saldo_inicial_credito = '$saldocreditoInicial', "
                . "debito = '$debitoFinal', "
                . "credito ='$creditoFinal', "
                . "nuevo_saldo_debito ='$saldoFinalDebito',"
                . "nuevo_saldo_credito = '$saldoFinalCredito' "
                . "WHERE id_cuenta ='$row[0]'";
        $update = $GLOBALS['mysqli']->query($update);



        if($row[1]>=$codigoI || $row[1]<=$codigoF){
            $totaldeb =$totaldeb+$debitoFinal;
            $totalcred=$totalcred+$creditoFinal;
            $totalsaldoID +=$saldodebitoInicial;
            $totalsaldoIC +=$saldocreditoInicial;
            $totalsaldoFD +=$saldoFinalDebito;
            $totalsaldoFC +=$saldoFinalCredito;        
        }     
        #****************************** FIN ACTUALIZAR DATOS **********************************#
    }     

    #CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
    $acum = "SELECT id_cuenta,numero_cuenta, cod_predecesor, "
            . "saldo_inicial_debito,saldo_inicial_credito,"
            . "debito, credito, "
            . "nuevo_saldo_debito, nuevo_saldo_credito "
            . "FROM temporal_consulta_tesoreria "
            . "ORDER BY numero_cuenta DESC ";
    $acum = $GLOBALS['mysqli']->query($acum);

    while ($rowa1= mysqli_fetch_row($acum)){
        $acumd = "SELECT id_cuenta,numero_cuenta, cod_predecesor, "
                . "saldo_inicial_debito, saldo_inicial_credito,"
                . "debito, credito, "
                . "nuevo_saldo_debito, nuevo_saldo_credito "
            . "FROM temporal_consulta_tesoreria WHERE id_cuenta ='$rowa1[0]'"
            . "ORDER BY numero_cuenta DESC ";
        $acumd = $GLOBALS['mysqli']->query($acumd);
        while ($rowa= mysqli_fetch_row($acumd)){
            if(!empty($rowa[2])){

            $va11= "SELECT numero_cuenta,"
                    . "saldo_inicial_debito, saldo_inicial_credito,"
                    . "debito, credito, "
                    . "nuevo_saldo_debito, nuevo_saldo_credito "
                    . "FROM temporal_consulta_tesoreria WHERE numero_cuenta ='$rowa[2]'";

            $va1 = $GLOBALS['mysqli']->query($va11);
            $va= mysqli_fetch_row($va1);
            $saldoInD= $rowa[3]+$va[1];
            $saldoInC= $rowa[4]+$va[2];
            $debitoN= $rowa[5]+$va[3];
            $creditoN= $rowa[6]+$va[4];
            $nuevoND=$rowa[7]+$va[5];
            $nuevoNC=$rowa[8]+$va[6];
            $updateA = "UPDATE temporal_consulta_tesoreria "
                    . "SET saldo_inicial_debito ='$saldoInD',"
                    . "saldo_inicial_credito ='$saldoInC', "
                    . "debito = '$debitoN', "
                    . "credito ='$creditoN', "
                    . "nuevo_saldo_debito ='$nuevoND', "
                    . "nuevo_saldo_credito ='$nuevoNC' "
                    . "WHERE numero_cuenta ='$rowa[2]'";
            $updateA = $GLOBALS['mysqli']->query($updateA);
        }
        }
    }
    
}