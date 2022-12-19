<?php
##########################################################################################
# *********************************** Modificaciones *********************************** # 
##########################################################################################
#06/04/2018 | Erica G. | Pasar Saldos Si Todas Las cuentas no estan configuradas
#06/03/2018 | Erica G. | Equivalente separado por coma
#01/02/2018 | Erica G. | Archivo Creado
##########################################################################################
require_once './../Conexion/ConexionPDO.php';
require_once './../Conexion/conexion.php';
require_once './funcionesPptal.php';
ini_set('max_execution_time', 0);
$con = new ConexionPDO(); 
@session_start();
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$nanno = anno($anno);
$action = $_REQUEST['action'];
##******** Buscar Centro De Costo ********#
$cc         = $con->Listar("SELECT * FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno");
$cent       = $cc[0][0];
$pro        = $con->Listar("SELECT * FROM gf_proyecto WHERE nombre='Varios' AND compania = $compania");
$proy       = $pro[0][0]; 
$ter        = $con->Listar("SELECT * FROM gf_tercero WHERE numeroidentificacion = '9999999999' AND compania = $compania");
$terc       = $ter[0][0]; 
switch ($action) {
    #Validar Si Ya Hay Año Siguiente Creado
    case 1:
        $nanno2 = $nanno+1;
        $cann2 = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = $nanno2 AND compania = $compania");
        if(count($cann2)>0){
            echo $anno2 = $cann2[0][0];
        } else {
            echo 0;
        }

    break;
    #Validar Si Ya Hay Comprobante Siguiente Creado
    case 2:
        $anno2 = $_POST['anno2'];
        $be = $con->Listar("SELECT cn.* FROM gf_comprobante_cnt cn 
        LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
        WHERE tc.clasecontable = 5 AND cn.parametrizacionanno = $anno2");
        echo count($be);
    break;
    #Validar Dependiendo El Tipo Si Todas Las Cuentas Se Encuentran
    case 3:
        $tipo   = $_POST['tipo'];
        $anno2  = $_POST['anno2'];
        $nanno2 = anno($anno2);
        $msj    = "Cuentas No Encontradas En Plan Contable ".$nanno2."<br/>";
        $rta    = 0;
        $rowc   = $con->Listar("SELECT DISTINCT dc.cuenta, c.codi_cuenta, c.nombre  
                FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico = dc.comprobante 
                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico
                WHERE cn.parametrizacionanno = $anno 
                AND c.codi_cuenta BETWEEN '1' AND '4' 
                ORDER BY c.codi_cuenta ASC ");
        if(count($rowc)>0){
            switch ($tipo){
                #* Cuentas Iguales *#
                case 1:
                    for ($i = 0; $i < count($rowc); $i++) {
                        $codigo = $rowc[$i][1];
                        $nombre = $rowc[$i][2];
                        $c2 = $con->Listar("SELECT * FROM gf_cuenta  
                                WHERE codi_cuenta = $codigo AND parametrizacionanno = $anno2 ");
                        if(count($c2)>0){
                            if(empty($c2[0][0])){
                                $msj.=$codigo.' - '.$nombre.'<br/>';
                                $rta +=1;
                            }
                        } else {
                            $msj.=$codigo.' - '.$nombre.'<br/>';
                            $rta +=1;
                        }
                    }
                break;
                #*¨Cuentas Homologadas *#
                case 2:
                    for ($i = 0; $i < count($rowc); $i++) {
                        $codigo = $rowc[$i][1];
                        $nombre = $rowc[$i][2];
                        $c2 = $con->Listar("SELECT * FROM gf_cuenta  
                                WHERE FIND_IN_SET($codigo, equivalente_va ) AND parametrizacionanno = $anno2 ");
                        
                        if(count($c2)>0){
                            if(empty($c2[0][0])){
//                                echo "SELECT * FROM gf_cuenta  
//                                WHERE FIND_IN_SET($codigo, equivalente_va ) AND parametrizacionanno = $anno2 ";
                                
                                $msj.=$codigo.' - '.$nombre.'<br/>';
                                $rta +=1;
                            }
                        } else {
//                            echo "SELECT * FROM gf_cuenta  
//                                WHERE FIND_IN_SET($codigo, equivalente_va ) AND parametrizacionanno = $anno2 ";
                            $msj.=$codigo.' - '.$nombre.'<br/>';
                            $rta +=1;
                        }
                    }
                break;

            }
        } else {
            $rta = 'NA';
        }
        $datos = array("msj"=>'', "rta"=>$rta);
        echo json_encode($datos); 
    break;
    #Descargar txt Con Datos
    case 4:
        $tipo   = $_POST['tipo'];
        $anno2  = $_POST['anno2'];
        $nanno2 = anno($anno2);
        $msj    = "Cuentas No Encontradas En Plan Contable ".$nanno2."<br/>";
        $rta    = 0;
        $rowc   = $con->Listar("SELECT DISTINCT dc.cuenta, c.codi_cuenta, c.nombre  
                FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico = dc.comprobante 
                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico
                WHERE cn.parametrizacionanno = $anno 
                AND c.codi_cuenta BETWEEN '1' AND '4' 
                ORDER BY c.codi_cuenta ASC ");
        if(count($rowc)>0){
            switch ($tipo){
                #* Cuentas Iguales *#
                case 1:
                    for ($i = 0; $i < count($rowc); $i++) {
                        $codigo = $rowc[$i][1];
                        $nombre = $rowc[$i][2];
                        $c2 = $con->Listar("SELECT * FROM gf_cuenta  
                                WHERE codi_cuenta = $codigo AND parametrizacionanno = $anno2 ");
                        if(count($c2)>0){
                            if(empty($c2[0][0])){
                                $msj.=$codigo.' - '.$nombre.'<br/>';
                                $rta +=1;
                            }
                        } else {
                            $msj.=$codigo.' - '.$nombre.'<br/>';
                            $rta +=1;
                        }
                    }
                break;
                #*¨Cuentas Homologadas *#
                case 2:
                    for ($i = 0; $i < count($rowc); $i++) {
                        $codigo = $rowc[$i][1];
                        $nombre = $rowc[$i][2];
                        $c2 = $con->Listar("SELECT * FROM gf_cuenta  
                                WHERE FIND_IN_SET($codigo, equivalente_va ) AND parametrizacionanno = $anno2 ");
                        
                        if(count($c2)>0){
                            if(empty($c2[0][0])){
//                                echo "SELECT * FROM gf_cuenta  
//                                WHERE FIND_IN_SET($codigo, equivalente_va ) AND parametrizacionanno = $anno2 ";
                                
                                $msj.=$codigo.' - '.$nombre.'<br/>';
                                $rta +=1;
                            }
                        } else {
//                            echo "SELECT * FROM gf_cuenta  
//                                WHERE FIND_IN_SET($codigo, equivalente_va ) AND parametrizacionanno = $anno2 ";
                            $msj.=$codigo.' - '.$nombre.'<br/>';
                            $rta +=1;
                        }
                    }
                break;

            }
        }
        $html = str_replace("<br/>", "\n", $msj);
        $data = $html;
        $fileName = '../documentos/Cuentas_Sin_Configurar.txt';
        $file = fopen($fileName,"w");
        if ($file === false) {
           echo "opening '$fileName' failed";
           exit;
        }
        if (fwrite($file, $data)){       
           echo '';
        }
        echo 'documentos/Cuentas_Sin_Configurar.txt';
    break;
    #Guardar Saldos Iniciales
    case 5:
        $fechaInicial   = $nanno.'-01-01'; 
        $fechaFinal     = $nanno.'-12-31';
        $tipo   = $_POST['tipo'];
        $anno2  = $_POST['anno2'];
        $nanno2 = anno($anno2);
        $descripcion = 'Comprobante Saldos Iniciales '.$nanno2;
        $fecha = $nanno2.'-01-01';
        # ** Validar Si Ya Hay Comprobante Creado *#
        $be = $con->Listar("SELECT cn.* FROM gf_comprobante_cnt cn 
        LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
        WHERE tc.clasecontable = 5 
        AND tc.compania = $compania 
        AND cn.parametrizacionanno = $anno2");
        if(count($be)>0){
            $comprobante = $be[0][0];
            $sql_cons ="DELETE FROM `gf_detalle_comprobante` WHERE `comprobante`=:comprobante";
            $sql_dato = array(
                    array(":comprobante",$comprobante),	
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        } else {
            #** Crear Comprobante **#
            $tc = $con->Listar("SELECT * FROM gf_tipo_comprobante WHERE compania = $compania AND clasecontable = 5");
            $tipocomprobante = $tc[0][0];
            $numero = $nanno2.'000001';
            $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                    ( `numero`, `fecha`, 
                    `descripcion`, `tercero` , 
                    `parametrizacionanno`,`tipocomprobante`, `compania`) 
            VALUES (:numero, :fecha, 
                    :descripcion,:tercero,
                    :parametrizacionanno,:tipocomprobante, :compania)";
            $sql_dato = array(
                    array(":numero",$numero),
                    array(":fecha",$fecha),
                    array(":descripcion",$descripcion),
                    array(":tercero",$terc),
                    array(":parametrizacionanno",$anno2),
                    array(":tipocomprobante",$tipocomprobante),
                    array(":compania",$compania),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            if(empty($resp)>0){
                $be = $con->Listar("SELECT cn.* FROM gf_comprobante_cnt cn 
                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                WHERE tc.clasecontable = 5 AND cn.parametrizacionanno = $anno2");
                $comprobante = $be[0][0];
            }
            
        }
        #******** Buscar Centro De Costo ********#
        $cc = $con->Listar("SELECT * FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno2");
        $centrocosto = $cc[0][0];
        
        #Consultar Cuentas Que Tengan Movimiento
        $row = $con->Listar("SELECT DISTINCT 
            c.id_unico, c.codi_cuenta, 
            c.nombre, c.naturaleza , c.auxiliartercero 
          FROM gf_detalle_comprobante dc 
          LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
          WHERE c.codi_cuenta BETWEEN '1' AND '4' 
          AND c.parametrizacionanno = $anno 
          ORDER BY c.codi_cuenta "); 
        for ($i = 0; $i < count($row); $i++) {
            $codigo = $row[$i][1];
            $aux    = $row[$i][4];
            $idcuenta_va = $row[$i][0];
            # ********Validar Si El Tipo Es Homologado O Plan Contable Igual **********#
            if($tipo == 1){
                $c2 = $con->Listar("SELECT id_unico, naturaleza FROM gf_cuenta  
                    WHERE codi_cuenta = $codigo AND parametrizacionanno = $anno2 ");
                
            } else {
                $c2 = $con->Listar("SELECT id_unico, naturaleza FROM gf_cuenta  
                    WHERE FIND_IN_SET($codigo, equivalente_va) AND parametrizacionanno = $anno2 ");
            }
            if(count($c2)>0){
                $cuenta = $c2[0][0];
                $naturaleza =$c2[0][1];
                if($aux==1){
                    #Actualizar Auxiliar Tercero De La Cuenta
                    $sql_cons ="UPDATE `gf_cuenta` 
                        SET `auxiliartercero`=:auxiliartercero 
                            WHERE id_unico=:id_unico ";
                    $sql_dato = array(
                        array(":auxiliartercero",1),
                        array(":id_unico",$cuenta),
                    );
                    $obj_resp = $con->InAcEl($sql_cons,$sql_dato);

                    #************ Buscar Terceros **************#
                    $rowt = $con->Listar("SELECT  DISTINCT  dc.tercero, dc.cuenta,  
                                   c.naturaleza 
                                FROM  gf_detalle_comprobante dc   
                                LEFT JOIN gf_cuenta c ON dc.cuenta    = c.id_unico
                                LEFT JOIN   gf_comprobante_cnt cn       ON cn.id_unico  = dc.comprobante
                                LEFT JOIN   gf_tercero tr              ON tr.id_unico = dc.tercero                        
                                LEFT JOIN
                                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                WHERE       c.auxiliartercero   = 1 
                                AND         c.id_unico       = '$idcuenta_va' 
                                AND         cn.parametrizacionanno = $anno   
                                ORDER BY    tr.numeroidentificacion ASC");
                    if(count($rowt)>0){
                        for ($j = 0; $j < count($rowt); $j++) {
                            $terceroA = $rowt[$j][0];
                            $nat_ctava = $rowt[$j][2];
                            $saldoIT  =0;
                            $debitoT  =0;
                            $creditoT =0;
                            # ** Movimientos Por Tercero ** #
                            $saldo = $con->Listar("SELECT SUM(valor)
                                          FROM
                                            gf_detalle_comprobante dc
                                          LEFT JOIN
                                            gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                          LEFT JOIN
                                            gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                          LEFT JOIN
                                            gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                          WHERE
                                            cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                                            AND cc.id_unico = '5' 
                                            AND dc.cuenta = '$idcuenta_va' 
                                            AND cp.parametrizacionanno =$anno    
                                            AND dc.tercero = $terceroA ");
                            if (count($saldo) > 0) {
                                $saldoIT = $saldo[0][0];
                            } else {
                                $saldoIT = 0;
                            }
                            #** DEBITOS **#
                            $debito = $con->Listar("SELECT SUM(valor)
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
                                          AND dc.cuenta = '$idcuenta_va' 
                                          AND cp.parametrizacionanno =$anno    
                                          AND dc.tercero = $terceroA");
                            if (count($debito) > 0) {
                                $debitoT = $debito[0][0];
                            } else {
                                $debitoT = 0;
                            }

                            #CREDITOS
                            $credito = $con->Listar("SELECT SUM(valor)
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
                                          AND dc.cuenta = '$idcuenta_va' 
                                          AND cp.parametrizacionanno =$anno    
                                          AND dc.tercero = $terceroA");
                            if (count($credito) > 0) {
                                $creditoT = $credito[0][0];
                            } else {
                                $creditoT = 0;
                            }

                            #SI LA NATURALEZA ES DEBITO
                            if ($nat_ctava == '1') {
                                if ($creditoT < 0) {
                                    $creditoT = (float) substr($creditoT, '1');
                                }
                                $saldoNuevoT = $saldoIT + $debitoT - $creditoT;
                                #SI LA NATURALEZA ES CREDITO
                            } else {
                                if ($creditoT < 0) {
                                    $creditoT = (float) substr($creditoT, '1');
                                }
                                $saldoNuevoT = $saldoIT - $creditoT + $debitoT;
                            }

                            $nsaldT     = (float) ($saldoNuevoT);

                            if ($nsaldT ==0 || $nsaldT =='' ) {

                            } else {


                           $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`,`descripcion`,`valor`,`valorejecucion`, `comprobante`,`cuenta`, `naturaleza`,`tercero`, `proyecto`, `centrocosto`  ) 
                            VALUES (:fecha, :descripcion, :valor,:valorejecucion, :comprobante, :cuenta, :naturaleza, :tercero, :proyecto, :centrocosto)";
                            $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":descripcion",$descripcion),
                                array(":valor",$nsaldT),
                                array(":valorejecucion",$nsaldT),
                                array(":comprobante",$comprobante),
                                array(":cuenta",$cuenta),
                                array(":naturaleza",$naturaleza),
                                array(":tercero",$terceroA),
                                array(":proyecto",$proy),
                                array(":centrocosto",$centrocosto),
                             );
                            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);

                            }
                        }
                    }
                    } else {

                        $saldo = $con->Listar("SELECT SUM(valor)
                                      FROM
                                        gf_detalle_comprobante dc
                                      LEFT JOIN
                                        gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                      LEFT JOIN
                                        gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                      LEFT JOIN
                                        gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                      WHERE
                                        cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                                        AND cc.id_unico = '5' 
                                        AND dc.cuenta = '$idcuenta_va' 
                                        AND cp.parametrizacionanno =$anno    ");
                        if (count($saldo) > 0) {
                            $saldoIT = $saldo[0][0];
                        } else {
                            $saldoIT = 0;
                        }
                        #** DEBITOS **#
                        $debito = $con->Listar("SELECT SUM(valor)
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
                                      AND dc.cuenta = '$idcuenta_va' 
                                      AND cp.parametrizacionanno =$anno    ");
                        if (count($debito) > 0) {
                            $debitoT = $debito[0][0];
                        } else {
                            $debitoT = 0;
                        }

                        #CREDITOS
                        $credito = $con->Listar("SELECT SUM(valor)
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
                                      AND dc.cuenta = '$idcuenta_va' 
                                      AND cp.parametrizacionanno =$anno    ");
                        if (count($credito) > 0) {
                            $creditoT = $credito[0][0];
                        } else {
                            $creditoT = 0;
                        }

                        #SI LA NATURALEZA ES DEBITO
                        if ($naturaleza == '1') {
                            if ($creditoT < 0) {
                                $creditoT = (float) substr($creditoT, '1');
                            }
                            $saldoNuevoT = $saldoIT + $debitoT - $creditoT;
                            #SI LA NATURALEZA ES CREDITO
                        } else {
                            if ($creditoT < 0) {
                                $creditoT = (float) substr($creditoT, '1');
                            }
                            $saldoNuevoT = $saldoIT - $creditoT + $debitoT;
                        }

                        $nsaldT     = (float) ($saldoNuevoT);

                        if ($nsaldT ==0 || $nsaldT =='' ) {

                        } else {


                       $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`,`descripcion`,`valor`,`valorejecucion`, `comprobante`,`cuenta`, `naturaleza`,`tercero`, `proyecto`, `centrocosto`  ) 
                        VALUES (:fecha, :descripcion, :valor,:valorejecucion, :comprobante, :cuenta, :naturaleza, :tercero, :proyecto, :centrocosto)";
                        $sql_dato = array(
                            array(":fecha",$fecha),
                            array(":descripcion",$descripcion),
                            array(":valor",$nsaldT),
                            array(":valorejecucion",$nsaldT),
                            array(":comprobante",$comprobante),
                            array(":cuenta",$cuenta),
                            array(":naturaleza",$naturaleza),
                            array(":tercero",$terc),
                            array(":proyecto",$proy),
                            array(":centrocosto",$centrocosto),
                         );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        }
                    }
            }
        }
        echo '1';
                    
    break;
}