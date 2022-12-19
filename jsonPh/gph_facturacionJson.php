<?php
##*********************** Modificaciones *********************#
#14/09/2018 |Erica G. | Archivo Creado
#05/02/2019 |Erica G. | Arreglar la creación de la parte contable del recaudo, y de la facturación
#*************************************************************#
require '../Conexion/ConexionPDO.php';               
require '../Conexion/conexion.php';               
require './../jsonPptal/funcionesPptal.php';               
ini_set('max_execution_time', 0);
ini_set('memory_limit','160000M');
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$fechaE     = date('Y-m-d'); 
$anno       = $_SESSION['anno'];
$action     = $_REQUEST['action'];
$cc         = $con->Listar("SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno");
$centroc    = $cc[0][0];
$usuario_t  = $_SESSION['usuario_tercero'];

switch ($_REQUEST['action']){
    #** Guardar Facturación **#
    case 1:
        $html       ='';
        $rta        = 0;
        $tfr        = 0;
        $tipo_fac   = $_REQUEST["sltTipoFactura"];
        $ffac       = fechaC($_REQUEST["fecha"]);
        $fven       = fechaC($_REQUEST["fechaV"]);
        $centro     = $_REQUEST["sltCentroCosto"];
        $descripciont = $_REQUEST["txtDescripcion"];
        $estado     = $_REQUEST["txtEstado"];
        $metodo_pago= $_REQUEST["sltMetodo"];
        $forma_pago = $_REQUEST["txtFormaP"];
        $cod_apts   = $_REQUEST["cod_ap"];
        $tipo_factura = $tipo_fac;
        $cod_ap = array();
        $cod_ap = explode(",", $cod_apts);
        $acumulado  = $_REQUEST["acumulado"];
        if($acumulado==1){
            #**** Facturar Acumulado por tercero
            for ($i = 0; $i < count($cod_ap); $i++) {
                if(!empty($cod_ap[$i])){
                    $rowl = $con->Listar("SELECT DISTINCT eh.* 
                        FROM gh_espacios_habitables eh 
                        WHERE eh.id_unico =$cod_ap[$i] AND eh.estado = 1");
                    #************* Guardar Factura **************#
                    $numero     = numeroFactura($tipo_factura,$anno);
                    $espacioh   = $rowl[0][0];
                    $descripcion=$descripciont.' '. $rowl[0][2].' '.$rowl[0][3].' ';
                    #** Buscar Terceor Ppropietario 
                    $rowtp= $con->Listar("SELECT t.id_unico FROM gf_tercero t 
                        LEFT JOIN gph_espacio_habitable_tercero eht ON eht.id_tercero = t.id_unico 
                        LEFT JOIN gh_espacios_habitables eh ON eht.id_espacio_habitable = eh.id_unico 
                        LEFT JOIN gf_perfil p ON eht.id_perfil = p.id_unico 
                        WHERE eh.id_unico = $espacioh AND p.nombre = 'Propietario'");
                    if(count($rowtp)>0){
                        $tercero = $rowtp[0][0];
                    } else {
                        $rowtp= $con->Listar("SELECT t.id_unico FROM gf_tercero t 
                        LEFT JOIN gph_espacio_habitable_tercero eht ON eht.id_tercero = t.id_unico 
                        LEFT JOIN gh_espacios_habitables eh ON eht.id_espacio_habitable = eh.id_unico 
                        LEFT JOIN gf_perfil p ON eht.id_perfil = p.id_unico 
                        WHERE eh.id_unico = $espacioh");
                        if(count($rowtp)>0){
                            $tercero = $rowtp[0][0];
                        }
                    }
                    #** Buscar si existe una factura con la misma fecha el mismo tipo y tercero
                    $fi = $con->Listar("SELECT * FROM gp_factura WHERE fecha_factura = '$ffac' 
                        AND tipofactura = $tipo_factura AND tercero=$tercero");
                    
                    if(count($fi)>0){
                        $id_factura = $fi[0][0];
                        $sql = "UPDATE gp_factura 
                            SET descripcion = CONCAT(descripcion, ' - ','".$rowl[0][2].' '.$rowl[0][3]."' ) 
                            WHERE id_unico=$id_factura";
                        $resultadoP = $mysqli->query($sql);
                    } else {
                            
                        $sql_cons ="INSERT INTO `gp_factura` 
                                ( `numero_factura`, `tercero`, `tipofactura`,
                            `fecha_factura`,`id_espacio_habitable`,`responsable`,
                            `fecha_vencimiento`,`descripcion`,
                            `parametrizacionanno`,`estado_factura`,`centrocosto`,
                            `forma_pago`,`metodo_pago`) 
                        VALUES  (:numero_factura,  :tercero, :tipofactura, 
                            :fecha_factura,:id_espacio_habitable,:responsable,
                            :fecha_vencimiento,:descripcion,
                            :parametrizacionanno,:estado_factura,:centrocosto, 
                            :forma_pago, :metodo_pago)";
                        $sql_dato = array(
                                array(":numero_factura",$numero),
                                array(":tercero",$tercero),
                                array(":tipofactura",$tipo_factura),
                                array(":fecha_factura",$ffac),
                                array(":id_espacio_habitable",$espacioh),
                                array(":responsable",$usuario_t),
                                array(":fecha_vencimiento",$fven),
                                array(":descripcion",$descripcion),
                                array(":parametrizacionanno",$anno),
                                array(":estado_factura",4),
                                array(":centrocosto",$centroc),
                                array(":forma_pago",$forma_pago),
                                array(":metodo_pago",$metodo_pago),
                        );
                        $resp       = $con->InAcEl($sql_cons,$sql_dato);
                        $fi         = $con->Listar("SELECT * FROM gp_factura WHERE numero_factura = $numero 
                             AND tipofactura = $tipo_factura");
                        $id_factura = $fi[0][0];
                        
                    }
                    $sql_cons ="INSERT INTO `gph_espacio_habitable_factura` 
                            ( `espacio_habitable`, `factura`) 
                    VALUES  (:espacio_habitable,  :factura)";
                    $sql_dato = array(
                            array(":espacio_habitable",$espacioh),
                            array(":factura",$id_factura),
                    );
                    $resp       = $con->InAcEl($sql_cons,$sql_dato);
                    #********* Buscar Si existe deuda anterior **********#
                    $deuda_anterior = 0;
                    $da = $con->Listar("SELECT GROUP_CONCAT( DISTINCT df.id_unico ), SUM(df.valor) 
                        FROM gp_factura f 
                        LEFT JOIN gp_detalle_factura df ON df.factura = f.id_unico 
                        WHERE f.id_espacio_habitable = $espacioh AND f.fecha_factura < '$ffac'");
                    if(count($da)>0){
                        #*** Buscar Recaudo ***#
                        $id_df      = $da[0][0];
                        $valor_f    = $da[0][1];
                        $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                            LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                            WHERE dp.detalle_factura IN ($id_df)");
                        if(count(($rc))<0){
                            $recaudo = 0;
                        }elseif(empty ($rc[0][0])){
                            $recaudo = 0;
                        } else {
                            $recaudo = $rc[0][0];
                        }
                        $deuda_anterior = $valor_f -$recaudo;
                    }

                    #*** Buscar Conceptos Asociados Al Espacio Habitable ***#
                    $cnp = $con->Listar("SELECT 
                        ehc.id_unico,
                        ehc.id_espacio_habitable,
                        ehc.id_concepto,
                        cn.nombre, eht.valor , ehc.iva 
                    FROM gph_espacio_habitable_concepto ehc
                    LEFT JOIN gp_concepto cn on cn.id_unico=ehc.id_concepto 
                    LEFT JOIN gph_espacio_habitable_tarifa eht ON eht.id_espacio_habitable_concepto = ehc.id_unico 
                    WHERE ehc.id_espacio_habitable=$espacioh AND cn.tipo_concepto = 1 AND eht.ano = $anno ");
                    for ($c = 0; $c < count($cnp); $c++) { 
                        $id_concepto = $cnp[$c][2];
                        $valor       = $cnp[$c][4];   
                        if(!empty($cnp[$c][5])){
                            $ci = ROUND(($valor * $cnp[$c][5])/100);
                            $pe = intval($ci);
                            $dec = substr($pe, -2);
                            if ($dec >= 50) {
                                $iva = ceil($pe / 100) * 100;
                            } //$dec > 50
                            else {
                                $iva = floor($pe / 100) * 100;
                            }
                        } else {
                            $iva = 0;
                        }
                        
                        //Cálculos FE
                        $p_iva  = $cnp[$c][5];
                        if($iva!=0){
                            $vta    = $valor+$iva;
                            $bbr    = ROUND($vta/(1 +($p_iva/100)),2);
                            $vi     = ROUND(($vta/(1 +($p_iva/100)))*($p_iva/100),2);
                            
                        } else {
                            $bbr    = $valor;
                            $vi     = 0;
                        }
                        #****** Guardar Concepto En Factura ******#
                        $sql_cons ="INSERT INTO `gp_detalle_factura` 
                                ( `factura`, `concepto_tarifa`, `valor`,
                            `cantidad`,`iva`,`impoconsumo`,
                            `ajuste_peso`,`valor_total_ajustado`,
                            `unidad_origen`,`valor_origen`) 
                        VALUES  (:factura,  :concepto_tarifa, :valor, 
                            :cantidad,:iva,:impoconsumo,
                            :ajuste_peso,:valor_total_ajustado,
                            :unidad_origen, :valor_origen)";
                        $sql_dato = array(
                                array(":factura",$id_factura),
                                array(":concepto_tarifa",$id_concepto),
                                array(":valor",$bbr),
                                array(":cantidad",1),
                                array(":iva",$vi),
                                array(":impoconsumo",0),
                                array(":ajuste_peso",0),
                                array(":valor_total_ajustado",($valor+$iva)),
                                array(":unidad_origen",3),
                                array(":valor_origen",$valor),
                        );
                        $resp       = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    if($deuda_anterior>0){
                        $cnd = $con->Listar("SELECT 
                            ehc.id_unico, eht.valor, cn.id_unico 
                        FROM gph_espacio_habitable_concepto ehc
                        LEFT JOIN gp_concepto cn on cn.id_unico=ehc.id_concepto 
                        LEFT JOIN gph_espacio_habitable_tarifa eht ON eht.id_espacio_habitable_concepto = ehc.id_unico 
                        WHERE ehc.id_espacio_habitable=$espacioh AND cn.tipo_concepto =2 AND cn.nombre='Recargo Mes Anterior' AND eht.ano = $anno ");
                        if(count($cnd)>0){
                            $valor = $cnd[0][1]*20;
                            $id_concepto = $cnd[0][2];
                            #****** Guardar Concepto En Factura ******#
                            $sql_cons ="INSERT INTO `gp_detalle_factura` 
                                    ( `factura`, `concepto_tarifa`, `valor`,
                                `cantidad`,`iva`,`impoconsumo`,
                                `ajuste_peso`,`valor_total_ajustado`,
                                `unidad_origen`,`valor_origen`) 
                            VALUES  (:factura,  :concepto_tarifa, :valor, 
                                :cantidad,:iva,:impoconsumo,
                                :ajuste_peso,:valor_total_ajustado,
                                :unidad_origen, :valor_origen)";
                            $sql_dato = array(
                                    array(":factura",$id_factura),
                                    array(":concepto_tarifa",$id_concepto),
                                    array(":valor",$valor),
                                    array(":cantidad",1),
                                    array(":iva",$iva),
                                    array(":impoconsumo",0),
                                    array(":ajuste_peso",0),
                                    array(":valor_total_ajustado",($valor+$iva)),
                                    array(":unidad_origen",3),
                                    array(":valor_origen",$valor),
                            );
                            $resp       = $con->InAcEl($sql_cons,$sql_dato);
                        }

                    }
                    reconstruirComprobantesFactura($id_factura);
                    $rta +=1;
                }
            }
        } else {
            #**** Facturar Normal
            for ($i = 0; $i < count($cod_ap); $i++) {
                if(!empty($cod_ap[$i])){
                    $rowl = $con->Listar("SELECT DISTINCT eh.* 
                        FROM gh_espacios_habitables eh 
                        WHERE eh.id_unico =$cod_ap[$i] AND eh.estado = 1");
                    #************* Guardar Factura **************#
                    $numero     = numeroFactura($tipo_factura,$anno);
                    $espacioh   = $rowl[0][0];
                    $descripcion= $descripciont.' '.$rowl[0][2].' '.$rowl[0][3];
                    #** Buscar Terceor Ppropietario 
                    $rowtp= $con->Listar("SELECT t.id_unico FROM gf_tercero t 
                        LEFT JOIN gph_espacio_habitable_tercero eht ON eht.id_tercero = t.id_unico 
                        LEFT JOIN gh_espacios_habitables eh ON eht.id_espacio_habitable = eh.id_unico 
                        LEFT JOIN gf_perfil p ON eht.id_perfil = p.id_unico 
                        WHERE eh.id_unico = $espacioh AND p.nombre = 'Propietario'");
                    if(count($rowtp)>0){
                        $tercero = $rowtp[0][0];
                    } else {
                        $rowtp= $con->Listar("SELECT t.id_unico FROM gf_tercero t 
                        LEFT JOIN gph_espacio_habitable_tercero eht ON eht.id_tercero = t.id_unico 
                        LEFT JOIN gh_espacios_habitables eh ON eht.id_espacio_habitable = eh.id_unico 
                        LEFT JOIN gf_perfil p ON eht.id_perfil = p.id_unico 
                        WHERE eh.id_unico = $espacioh");
                        if(count($rowtp)>0){
                            $tercero = $rowtp[0][0];
                        }
                    }
                    #*** Buscar Si ya existe una factura del espacio habitable y de la misma fecha *****#
                    $fi = $con->Listar("SELECT * FROM gp_factura WHERE fecha_factura = '$ffac' 
                    AND tipofactura = $tipo_factura AND id_espacio_habitable=$espacioh");
                    if(count($fi)>0){
                        $id_factura = $fi[0][0];
                    } else {
                         $sql_cons ="INSERT INTO `gp_factura` 
                                    ( `numero_factura`, `tercero`, `tipofactura`,
                                `fecha_factura`,`id_espacio_habitable`,`responsable`,
                                `fecha_vencimiento`,`descripcion`,
                                `parametrizacionanno`,`estado_factura`,`centrocosto`,
                                `forma_pago`,`metodo_pago`) 
                            VALUES  (:numero_factura,  :tercero, :tipofactura, 
                                :fecha_factura,:id_espacio_habitable,:responsable,
                                :fecha_vencimiento,:descripcion,
                                :parametrizacionanno,:estado_factura,:centrocosto, 
                                :forma_pago, :metodo_pago)";
                            $sql_dato = array(
                                    array(":numero_factura",$numero),
                                    array(":tercero",$tercero),
                                    array(":tipofactura",$tipo_factura),
                                    array(":fecha_factura",$ffac),
                                    array(":id_espacio_habitable",$espacioh),
                                    array(":responsable",$usuario_t),
                                    array(":fecha_vencimiento",$fven),
                                    array(":descripcion",$descripcion),
                                    array(":parametrizacionanno",$anno),
                                    array(":estado_factura",4),
                                    array(":centrocosto",$centroc),
                                    array(":forma_pago",$forma_pago),
                                    array(":metodo_pago",$metodo_pago),
                            );
                        $resp       = $con->InAcEl($sql_cons,$sql_dato);
                        $fi         = $con->Listar("SELECT * FROM gp_factura WHERE numero_factura = $numero 
                             AND tipofactura = $tipo_factura");
                        $id_factura = $fi[0][0];
                    }
                    #********* Buscar Si existe deuda anterior **********#
                    $deuda_anterior = 0;
                    $da = $con->Listar("SELECT GROUP_CONCAT( DISTINCT df.id_unico ), SUM(df.valor) 
                        FROM gp_factura f 
                        LEFT JOIN gp_detalle_factura df ON df.factura = f.id_unico 
                        WHERE f.id_espacio_habitable = $espacioh AND f.fecha_factura < '$ffac'");
                    if(count($da)>0){
                        #*** Buscar Recaudo ***#
                        $id_df      = $da[0][0];
                        $valor_f    = $da[0][1];
                        $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                            LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                            WHERE dp.detalle_factura IN ($id_df)");
                        if(count(($rc))<0){
                            $recaudo = 0;
                        }elseif(empty ($rc[0][0])){
                            $recaudo = 0;
                        } else {
                            $recaudo = $rc[0][0];
                        }
                        $deuda_anterior = $valor_f -$recaudo;
                    }

                    #*** Buscar Conceptos Asociados Al Espacio Habitable ***#
                    #*** Buscar Conceptos Asociados Al Espacio Habitable ***#
                    $cnp = $con->Listar("SELECT 
                        ehc.id_unico,
                        ehc.id_espacio_habitable,
                        ehc.id_concepto,
                        cn.nombre, eht.valor , ehc.iva 
                    FROM gph_espacio_habitable_concepto ehc
                    LEFT JOIN gp_concepto cn on cn.id_unico=ehc.id_concepto 
                    LEFT JOIN gph_espacio_habitable_tarifa eht ON eht.id_espacio_habitable_concepto = ehc.id_unico 
                    WHERE ehc.id_espacio_habitable=$espacioh AND cn.tipo_concepto = 1 AND eht.ano = $anno ");
                    for ($c = 0; $c < count($cnp); $c++) { 
                        $id_concepto = $cnp[$c][2];
                        $valor       = $cnp[$c][4];   
                        if(!empty($cnp[$c][5])){
                            $ci = ROUND(($valor * $cnp[$c][5])/100);
                            $pe = intval($ci);
                            $dec = substr($pe, -2);
                            if ($dec >= 50) {
                                $iva = ceil($pe / 100) * 100;
                            } //$dec > 50
                            else {
                                $iva = floor($pe / 100) * 100;
                            }
                        } else {
                            $iva = 0;
                        }
                        
                        //Cálculos FE
                        $p_iva  = $cnp[$c][5];
                        if($iva!=0){
                            $vta    = $valor+$iva;
                            $bbr    = ROUND($vta/(1 +($p_iva/100)),2);
                            $vi     = ROUND(($vta/(1 +($p_iva/100)))*($p_iva/100),2);
                            
                        } else {
                            $bbr    = $valor;
                            $vi     = 0;
                        }
                        #****** Guardar Concepto En Factura ******#
                        $sql_cons ="INSERT INTO `gp_detalle_factura` 
                                ( `factura`, `concepto_tarifa`, `valor`,
                            `cantidad`,`iva`,`impoconsumo`,
                            `ajuste_peso`,`valor_total_ajustado`,
                            `unidad_origen`,`valor_origen`) 
                        VALUES  (:factura,  :concepto_tarifa, :valor, 
                            :cantidad,:iva,:impoconsumo,
                            :ajuste_peso,:valor_total_ajustado,
                            :unidad_origen, :valor_origen)";
                        $sql_dato = array(
                                array(":factura",$id_factura),
                                array(":concepto_tarifa",$id_concepto),
                                array(":valor",$bbr),
                                array(":cantidad",1),
                                array(":iva",$vi),
                                array(":impoconsumo",0),
                                array(":ajuste_peso",0),
                                array(":valor_total_ajustado",($valor+$iva)),
                                array(":unidad_origen",3),
                                array(":valor_origen",$valor),
                        );
                        $resp       = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    if($deuda_anterior>0){
                        $cnd = $con->Listar("SELECT 
                            ehc.id_unico, eht.valor, cn.id_unico 
                        FROM gph_espacio_habitable_concepto ehc
                        LEFT JOIN gp_concepto cn on cn.id_unico=ehc.id_concepto 
                        LEFT JOIN gph_espacio_habitable_tarifa eht ON eht.id_espacio_habitable_concepto = ehc.id_unico 
                        WHERE ehc.id_espacio_habitable=$espacioh AND cn.tipo_concepto =2 AND cn.nombre='Recargo Mes Anterior' AND eht.ano = $anno ");
                        if(count($cnd)>0){
                            $valor = $cnd[0][1]*20;
                            $id_concepto = $cnd[0][2];
                            #****** Guardar Concepto En Factura ******#
                            $sql_cons ="INSERT INTO `gp_detalle_factura` 
                                    ( `factura`, `concepto_tarifa`, `valor`,
                                `cantidad`,`iva`,`impoconsumo`,
                                `ajuste_peso`,`valor_total_ajustado`,
                                `unidad_origen`,`valor_origen`) 
                            VALUES  (:factura,  :concepto_tarifa, :valor, 
                                :cantidad,:iva,:impoconsumo,
                                :ajuste_peso,:valor_total_ajustado,
                                :unidad_origen, :valor_origen)";
                            $sql_dato = array(
                                    array(":factura",$id_factura),
                                    array(":concepto_tarifa",$id_concepto),
                                    array(":valor",$valor),
                                    array(":cantidad",1),
                                    array(":iva",$iva),
                                    array(":impoconsumo",0),
                                    array(":ajuste_peso",0),
                                    array(":valor_total_ajustado",($valor+$iva)),
                                    array(":unidad_origen",3),
                                    array(":valor_origen",$valor),
                            );
                            $resp       = $con->InAcEl($sql_cons,$sql_dato);
                        }

                    }
                    reconstruirComprobantesFactura($id_factura);
                    $rta +=1;
                }
            }
        }
        echo ($rta);
    break;
    #***** Guardar Recaudos *******#
    case 2:
        $numr = $_REQUEST['num'];
        $panno = $anno;
        $anno = anno($panno);
        $dr =0;
        $idt ="";
        $iduv ="";
        $id_causacion1   = "";
        $id_cnt1         = "";
        $id_pptal1       = "";
        $id_pago1        = "";
        $idnb            = "0,";
        $vf = $_REQUEST['facturas'];
        $arrayfacturas2 = array();
        $arrayfacturas2 = explode(",", $vf);
        for ($i = 0; $i < count($arrayfacturas2); $i++) {
            $df = explode("=>", $arrayfacturas2[$i]);
            $id_factura =$df[0];
            if(!empty($df[1])){
                $valor      = $df[1];
                $factura    = $id_factura;
                $tipoPago   = $_REQUEST['tipoRecaudo'];
                $banco      = $_REQUEST['banco'];
                #Buscar Datos Factura
                $df = $con->Listar("SELECT f.id_unico,
                            f.numero_factura, tp.nombre,
                            f.tercero, f.descripcion, f.fecha_factura, f.centrocosto,
                            f.id_espacio_habitable  
                        FROM gp_factura f LEFT JOIN gp_tipo_factura tp ON tp.id_unico = f.tipofactura
                        WHERE f.id_unico = $factura");
                $fecha      = fechaC($_REQUEST['fecha']);
                $responsable= $df[0][3];
                $centrocosto= $df[0][6];
                #** Buscar si hay un pago con la misma fecha y tercero y tipo 
                $bs_pago = $con->Listar("SELECT * FROM gp_pago 
                    WHERE tipo_pago= $tipoPago 
                        AND responsable = $responsable 
                        AND fecha_pago = '$fecha' 
                        AND banco = $banco");
                
                if(count($bs_pago)>0){
                    $pago = $bs_pago[0][0];
                    $sql_cons ="UPDATE `gp_detalle_pago`
                        SET `detallecomprobante`=:detallecomprobante 
                        WHERE `pago`=:pago";
                        $sql_dato = array(
                            array(":detallecomprobante",NULL),
                            array(":pago",$pago),
                        );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    //var_dump($resp);
                    if(!empty($id_causacion1)){
                        eliminardetallescnt($id_causacion1);
                    }
                    if(!empty($id_cnt1)){
                       $sql_cons ="DELETE FROM  `gf_detalle_comprobante`
                        WHERE `comprobante`=:comprobante 
                        AND `id_unico` NOT IN :ids";
                        $sql_dato = array(
                            array(":comprobante",$id_cnt1),
                            array(":ids",$idnb),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    if(!empty($id_pptal1)){
                        eliminardetallespptal($id_pptal1);
                    }

                } else {
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
                            '$fecha',$banco,$estado, $panno, $usuario_t)";
                    $resultadoP = $mysqli->query($sql);
                    //var_dump($resultadoP);
                    #********* Buscar el Registro Pago Realizado **************#
                    $idPago = $con->Listar("SELECT MAX(id_unico) FROM gp_pago WHERE numero_pago='$numeroPago' AND tipo_pago=$tipoPago");
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
                $vc = 'recargo'.$i;
                if($_REQUEST[$vc]>0){
                    #******* Buscar Concepto Recargo
                    $espacioh   = $df[0][7];
                    $cnd = $con->Listar("SELECT 
                            ehc.id_unico, eht.valor, cn.id_unico 
                        FROM gph_espacio_habitable_concepto ehc
                        LEFT JOIN gp_concepto cn on cn.id_unico=ehc.id_concepto 
                        LEFT JOIN gph_espacio_habitable_tarifa eht ON eht.id_espacio_habitable_concepto = ehc.id_unico 
                        WHERE ehc.id_espacio_habitable=$espacioh AND cn.tipo_concepto =2 AND cn.nombre='Recargo Mes Anterior' AND eht.ano = $panno ");
                    if(count($cnd)>0){
                        $valor_recargo  = $_REQUEST[$vc];
                        $id_concepto    = $cnd[0][2];
                        #****** Guardar Concepto En Factura ******#
                        $sql_cons ="INSERT INTO `gp_detalle_factura` 
                                ( `factura`, `concepto_tarifa`, `valor`,
                            `cantidad`,`iva`,`impoconsumo`,
                            `ajuste_peso`,`valor_total_ajustado`,
                            `unidad_origen`,`valor_origen`) 
                        VALUES  (:factura,  :concepto_tarifa, :valor, 
                            :cantidad,:iva,:impoconsumo,
                            :ajuste_peso,:valor_total_ajustado,
                            :unidad_origen, :valor_origen)";
                        $sql_dato = array(
                                array(":factura",$id_factura),
                                array(":concepto_tarifa",$id_concepto),
                                array(":valor",$valor),
                                array(":cantidad",1),
                                array(":iva",$iva),
                                array(":impoconsumo",0),
                                array(":ajuste_peso",0),
                                array(":valor_total_ajustado",($valor+$iva)),
                                array(":unidad_origen",3),
                                array(":valor_origen",$valor),
                        );
                        $resp       = $con->InAcEl($sql_cons,$sql_dato);
                        
                        #*********** Guardar En El Comprobante CNT Y PPTAL ************#
                        $dias       = (strtotime($fecha)-strtotime($fecha))/86400;
                        $dias       = abs($dias);
                        $dias       = floor($dias);
                        $tipo_c     = carteradia($dias);

                        $sqlc=$con->Listar("SELECT
                            cf.id_unico,
                            cf.concepto ,
                            cf.concepto_rubro,
                            cf.rubro_fuente,
                            crc.cuenta_debito,
                            cd.naturaleza,
                            crc.cuenta_credito,
                            cc.naturaleza,
                            crc.cuenta_iva,
                            civ.naturaleza,
                            crc.cuenta_impoconsumo,
                            ci.naturaleza
                        FROM gp_configuracion_concepto cf
                        LEFT JOIN gf_concepto_rubro cr ON cr.id_unico = cf.concepto_rubro
                        LEFT JOIN gf_concepto_rubro_cuenta crc ON cr.id_unico = crc.concepto_rubro
                        LEFT JOIN gf_cuenta cd ON crc.cuenta_debito = cd.id_unico
                        LEFT JOIN gf_cuenta cc ON crc.cuenta_credito = cc.id_unico
                        LEFT JOIN gf_cuenta civ ON civ.id_unico = crc.cuenta_iva
                        LEFT JOIN gf_cuenta ci ON ci.id_unico = crc.cuenta_impoconsumo
                        WHERE cf.concepto=$id_concepto and cf.tipo_cartera = $tipo_c
                        AND cf.parametrizacionanno = $panno");

                        if(count($sqlc)>0){
                            ##********** Detalle Cnt*****************#
                            #cuenta credito
                            $cc =$sqlc[0][6];
                            #cuenta debito
                            $cd =$sqlc[0][4];
                            $naturalezad = $sqlc[0][5];
                            #Verificar Naturaleza
                            $naturalezac = $sqlc[0][7];
                            $valord =$valor_recargo;
                            $valorc =$valor_recargo;
                            if($naturalezad==2){
                                $valord = ($valor_recargo*-1);
                            }
                            if($naturalezac==1){
                                $valorc = ($valor_recargo*-1);
                            }
                            if (!empty($id_cnt)){
                                #Insertar Detalle Cnt Debito
                                $insertD = "INSERT INTO gf_detalle_comprobante
                                        (fecha, valor,
                                        comprobante, naturaleza, cuenta,
                                        tercero, proyecto, centrocosto)
                                        VALUES('$fecha', $valord,
                                        $id_cnt, $naturalezad, $cd,
                                        $responsable,  2147483647, $centrocosto)";
                                $resultado = $GLOBALS['mysqli']->query($insertD);

                                $id_dc = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $id_cnt");
                                $id_dc = $id_dc[0][0];
                                $idnb .=','.$id_dc;
                                #Insertar Detalle Cnt Debito
                                $insertD = "INSERT INTO gf_detalle_comprobante
                                        (fecha, valor,
                                        comprobante, naturaleza, cuenta,
                                        tercero, proyecto, centrocosto)
                                        VALUES('$fecha', $valorc,
                                        $id_cnt, $naturalezac, $cc,
                                        $responsable,  2147483647, $centrocosto)";
                                $resultado = $GLOBALS['mysqli']->query($insertD);

                                $id_dc = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $id_cnt");
                                $id_dc = $id_dc[0][0];
                                $idnb .=','.$id_dc;
                            }
                        }
                        
                        
                    }
                }
                
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
                    #** Actualizar Detalels Pago **#
                    $sql_cons ="UPDATE  `gp_detalle_pago`
                    SET `detallecomprobante` =:detallecomprobante
                    WHERE `pago`=:pago ";
                    $sql_dato = array(
                        array(":detallecomprobante",NULL),
                        array(":pago",$pago),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $recon =0;
                    #*** Buscar Si Tiene Causación
                    $cs = causacion($id_cnt);
                    if(!empty($cs[0][0])){
                        $id_causacion =$cs;
                        $ec = eliminardetallescnt($id_causacion);
                        if($ec==1){
                            $ecn = eliminardetallescnt($id_cnt);
                            if($ecn==1){
                                $epp = eliminardetallespptal($id_pptal);
                                if($epp==1){
                                    $recon = 1;
                                }
                            }
                        }
                    } else {
                        #*** Buscar Si Existe Relacion Por Numero Y Tipo
                        $ccs = $con->Listar("SELECT cnc.id_unico
                            FROM gf_comprobante_cnt cn
                            LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                            LEFT JOIN gf_comprobante_cnt cnc ON cn.numero = cnc.numero AND cnc.tipocomprobante = tc.tipo_comp_hom
                            WHERE cn.id_unico =$id_cnt");
                        $id_causacion =$ccs[0][0];
                        $ecn = eliminardetallescnt($id_cnt);
                        if($ecn==1){
                            $epp = eliminardetallespptal($id_pptal);
                            #var_dump($epp);
                            if($epp==1){
                                $recon = 1;
                            }
                        }
                    }
                    $reg=registrarDetallesPago($pago,$id_cnt,$id_pptal,$id_causacion);
                    if($reg==true){
                        $dr +=1;
                    }
                    $idt = $responsable;
                    $iduv=$df[0][7];
                    $id_causacion1   = $id_causacion;
                    $id_cnt1         = $id_cnt;
                    $id_pptal1       = $id_pptal;
                    $id_pago1        = $pago;
                }                
                 
            }
        }
        echo $dr;
    break;
}