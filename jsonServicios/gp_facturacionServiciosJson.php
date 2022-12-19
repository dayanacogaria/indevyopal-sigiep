<?php
require '../Conexion/ConexionPDO.php';               
require '../Conexion/conexion.php';               
require './../jsonPptal/funcionesPptal.php';               
require './../jsonServicios/funcionesServicios.php';               
require './calcular.php';               
ini_set('max_execution_time', 0);
ini_set('memory_limit','160000M');
require_once dirname(__FILE__) . './../ExcelR/Classes/PHPExcel.php';
include './../ExcelR/Classes/PHPExcel/IOFactory.php';
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$fechaE     = date('Y-m-d'); 
$anno       = $_SESSION['anno'];
$panno      = $_SESSION['anno'];
$action     = $_REQUEST['action'];
$cc         = $con->Listar("SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno");
$centroc    = $cc[0][0];
$usuario_t  = $_SESSION['usuario_tercero'];
$Cal        = new Field_calculate();
switch ($action){
    #** Guardar Facturación **#
    case 1:
    
        $html       ='';
        $rta        = 0;
        $lecturas   = $_REQUEST['lecturas'];
        $periodo    = $_REQUEST['periodo'];
        $fecha_f    = fechaC($_REQUEST['fechaF']);
        $tfr        = 0;
        #*** Buscar Perido Anterior ***#
        $periodoa   = periodoA($periodo);
        if($periodoa==""){
            $html = 'No Se Encontró Periodo Anterior';
            $rta  = 1;
        } else {
            #*** Buscar Tipo Factura ***#
            $tf = $con->Listar("SELECT * FROM gp_tipo_factura WHERE servicio = 1");
            if(count($tf)>0){
                $tipo_factura = $tf[0][0];
                $rowl = $con->Listar("SELECT l.id_unico, l.valor, ts.id_unico, uv.estrato, 
                    uvms.id_unico, pr.codigo_catastral, p.nombre, p.descripcion, t.id_unico, 
                    uvs.id_unico, p.id_unico , uv.id_unico , m.id_unico, m.estado_medidor , 
                    uv.uso, uv.deshabilitado 
                    FROM gp_lectura l 
                    LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON l.unidad_vivienda_medidor_servicio = uvms.id_unico 
                    LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                    LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico  
                    LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
                    LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                    LEFT JOIN gp_periodo p ON l.periodo = p.id_unico 
                    LEFT JOIN gp_tipo_servicio ts ON uvs.tipo_servicio = ts.id_unico 
                    LEFT JOIN gf_tercero t ON t.id_unico = uv.tercero 
                    LEFT JOIN gp_predio1 pr ON uv.predio = pr.id_unico 
                    WHERE l.id_unico IN ($lecturas) ORDER BY cast((replace(uv.codigo_ruta, '.','')) as unsigned)");
                for ($i = 0; $i < count($rowl); $i++) {
                    $id_l       = $rowl[$i][0];
                    $valor      = $rowl[$i][1];
                    $tipo_s     = $rowl[$i][2];
                    $estrato    = $rowl[$i][3];
                    $uvms       = $rowl[$i][4];
                    $valor_tf   = 0;
                    #************* Guardar Factura **************#
                    $numero     = numeroFactura($tipo_factura,$anno);
                    $tercero    = $rowl[$i][8];
                    $uvs        = $rowl[$i][9];
                    $periodo    = $rowl[$i][10];
                    $uv         = $rowl[$i][11];
                    $estado_m   = $rowl[$i][13];
                    $uso        = $rowl[$i][14];
                    $vacia      = $rowl[$i][15];
                    $descripcion= 'Factura Unidad Vivienda:'.$rowl[$i][5].' Periodo:'.$rowl[$i][6].' - '.$rowl[$i][7];
                    #********* Buscar Unidad_v con otros medidores ********#
                    $ids_uv = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gp_unidad_vivienda_medidor_servicio 
                            WHERE unidad_vivienda_servicio = $uvs");
                    $ids_uv = $ids_uv[0][0];
                    #*** Buscar Si ya existe una factura de la lectura *****#
                    $fi = $con->Listar("SELECT * FROM gp_factura WHERE unidad_vivienda_servicio = $uvms AND tipofactura = $tipo_factura AND periodo = $periodo");
                    if(count($fi)>0){
                       $id_factura = $fi[0][0];
                    } else {
                        $sql_cons ="INSERT INTO `gp_factura` 
                                ( `numero_factura`, `tercero`, `tipofactura`,
                            `unidad_vivienda_servicio`,`periodo`,`fecha_factura`,
                            `fecha_vencimiento`,`descripcion`,`lectura`,
                            `parametrizacionanno`,`estado_factura`,`centrocosto`) 
                        VALUES  (:numero_factura,  :tercero, :tipofactura, 
                            :unidad_vivienda_servicio,:periodo,:fecha_factura,
                            :fecha_vencimiento,:descripcion,:lectura,
                            :parametrizacionanno,:estado_factura,:centrocosto)";
                        $sql_dato = array(
                                array(":numero_factura",$numero),
                                array(":tercero",$tercero),
                                array(":tipofactura",$tipo_factura),
                                array(":unidad_vivienda_servicio",$uvms),
                                array(":periodo",$periodo),
                                array(":fecha_factura",$fecha_f),
                                array(":fecha_vencimiento",$fecha_f),
                                array(":descripcion",$descripcion),
                                array(":lectura",$id_l),
                                array(":parametrizacionanno",$anno),
                                array(":estado_factura",4),
                                array(":centrocosto",$centroc),
                        );
                        $resp       = $con->InAcEl($sql_cons,$sql_dato);
                        $fi         = $con->Listar("SELECT * FROM gp_factura WHERE lectura = $id_l AND tipofactura = $tipo_factura");
                        $id_factura = $fi[0][0];
                    }
                    
                    #********************************************#
                    #*** Buscar Lectura Anterior ***#
                    $la = $con->Listar("SELECT valor FROM gp_lectura 
                        WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $periodoa");
                    #if(count($la)>0 && !empty($la[0][0]) && $la[0][0]!=0){
                    $la = $la[0][0];
                    $cantidad = $valor-$la;
                    #echo $valor;
                    if($valor==0){
                        $cantidad = 0;
                    }    
                    #echo $cantidad;
                    
                    if($estado_m==1){
                        if($cantidad <0){
                            $cantidad = 16;
                        } else { 
                            #***Buscar Si tiene Periodos Anteriores para realizar promedio ***#
                            $bspa = $con->Listar("SELECT * FROM gp_lectura 
                            WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo < $periodo ORDER BY periodo DESC LIMIT 6");
                            if(count($bspa) >3){
                                $ba = $con->Listar("SELECT cantidad_facturada FROM gp_lectura 
                                    WHERE unidad_vivienda_medidor_servicio = $uvms 
                                        AND periodo < $periodo  ORDER BY periodo DESC");
                                $l1 = $ba[0][0];
                                $l2 = $ba[1][0];
                                $l3 = $ba[2][0];
                                $l4 = $ba[3][0];
                                $l5 = $ba[4][0];
                                $l6 = $ba[5][0];
                                
                                if($l1<0){ $l1 = 0; }
                                if($l2<0){ $l2 = 0; }
                                if($l3<0){ $l3 = 0; }
                                if($l4<0){ $l4 = 0; }
                                if($l5<0){ $l5 = 0; }
                                if($l6<0){ $l6 = 0; }

                                if($l1==0 && $l2==0 && $l3==0 && $l4==0 && $l5==0 && $l6==0){
                                    $cantidad = 16;
                                } else { 
                                    $cantidad = ($l1+$l2+$l3+$l4+$l5+$l6)/6;
                                    $cantidad = ROUND($cantidad,0);
                                    if($cantidad <1){
                                        $cantidad = 16;
                                    }
                                }
                            } else {
                                $cantidad = 16; 
                            }
                        } 
                        
                    } else { 
                        if($cantidad ==0){
                            #***Buscar Si tiene Periodos Anteriores para realizar promedio ***#
                            $bspa = $con->Listar("SELECT * FROM gp_lectura 
                            WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo < $periodo ORDER BY periodo DESC LIMIT 6");
                            if(count($bspa) >3){
                                $ba = $con->Listar("SELECT cantidad_facturada FROM gp_lectura 
                                    WHERE unidad_vivienda_medidor_servicio = $uvms 
                                        AND periodo < $periodo  ORDER BY periodo DESC");
                                $l1 = $ba[0][0];
                                $l2 = $ba[1][0];
                                $l3 = $ba[2][0];
                                $l4 = $ba[3][0];
                                $l5 = $ba[4][0];
                                $l6 = $ba[5][0];
                                
                                if($l1<0){ $l1 = 0; }
                                if($l2<0){ $l2 = 0; }
                                if($l3<0){ $l3 = 0; }
                                if($l4<0){ $l4 = 0; }
                                if($l5<0){ $l5 = 0; }
                                if($l6<0){ $l6 = 0; }

                                if($l1==0 && $l2==0 && $l3==0 && $l4==0 && $l5==0 && $l6==0){
                                    $cantidad = 16;
                                } else { 
                                    $cantidad = ($l1+$l2+$l3+$l4+$l5+$l6)/6;
                                    $cantidad = ROUND($cantidad,0);
                                    if($cantidad <1) {
                                        $cantidad = 16;
                                    }
                                }
                            } else {
                                $cantidad = 16; 
                            }
                        }elseif($cantidad < 0){
                            $cantidad = 16;
                            #Actualizar Estado de medidor 
                            $sql_cons ="UPDATE `gp_medidor`  
                                 SET `estado_medidor`=:estado_medidor 
                                 WHERE `id_unico`=:id_unico";
                            $sql_dato = array(
                                    array(":estado_medidor",1),
                                    array(":id_unico",$ca[0][0]),
                            );
                            $resp       = $con->InAcEl($sql_cons,$sql_dato);
                        } 
                    }
                    #*** Actualizar cantidad facturada 
                    $upd =$sql_cons ="UPDATE `gp_lectura` 
                            SET `cantidad_facturada`=:cantidad_facturada
                            WHERE `id_unico`=:id_unico";
                    $sql_dato = array(
                        array(":cantidad_facturada",$cantidad),
                        array(":id_unico",$id_l)
                    );
                    $resp       = $con->InAcEl($sql_cons,$sql_dato); ;
                    #********* Buscar Unidad¿_v con otros medidores ********#
                    $ids_uv = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gp_unidad_vivienda_medidor_servicio 
                            WHERE unidad_vivienda_servicio = $uvs");
                    $ids_uv = $ids_uv[0][0];
                    #********* Buscar Si existe deuda anterior **********#
                    $da = $con->Listar("SELECT GROUP_CONCAT(df.id_unico), SUM(df.valor_total_ajustado) 
                        FROM gp_detalle_factura df 
                        LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                        WHERE f.unidad_vivienda_servicio IN ($ids_uv) AND f.periodo <= $periodoa");
                    if(count($da)>0){
                        #*** Buscar Recaudo ***#
                        $id_df      = $da[0][0];
                        $valor_f    = $da[0][1];
                        $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                            LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                            WHERE p.fecha_pago <='$fecha_f' AND dp.detalle_factura IN ($id_df)");
                        if(count(($rc))<=0){
                            $recaudo = 0;
                        }elseif(empty ($rc[0][0])){
                            $recaudo = 0;
                        } else {
                            $recaudo = $rc[0][0];
                        }
                        $deuda_anterior = $valor_f -$recaudo;
                    }
                    
                    #*** Buscar Conceptos Asociados Al Tipo Servicio ***#
                    $cnp = $con->Listar("SELECT DISTINCT c.id_unico, ts.id_unico, c.tipo_operacion, 
                        c.nombre 
                        FROM gp_concepto_servicio cs 
                        LEFT JOIN gp_concepto c ON cs.concepto = c.id_unico 
                        LEFT JOIN gp_tipo_servicio ts ON cs.tipo_servicio = ts.id_unico 
                        WHERE cs.tipo_servicio = $tipo_s 
                        ORDER BY c.id_unico");
                    for ($c = 0; $c < count($cnp); $c++) { 
                        $g = 1;
                        if($vacia==1){
                            if (strpos($cnp[$c][3], 'Mora') == false) {
                                $g = 0;
                            }
                        } 
                        if($g==1) {
                            $tipo_servicio = $cnp[$c][1];
                            $id_concepto   = $cnp[$c][0];
                            $tipo_operacion= $cnp[$c][2];

                            ## Buscar si la unidad de vivienda tiene el servicio activo ##
                            $uts = $con->Listar("SELECT id_unico, aplica_interes FROM gp_unidad_vivienda_servicio 
                                WHERE unidad_vivienda = $uv 
                                AND tipo_servicio = $tipo_servicio AND estado_servicio = 1");
                            
                            if(count($uts)>0){ 
                                
                                if (strpos($cnp[$c][3], 'Mora') == true && $uts[0][1]==2) {
                                } else {
                                    $bt =0;
                                    $df = 0;
                                    $formula_e = 0;

                                    if($cantidad ==0 ){
                                        $bfm = buscarformula0($id_concepto, $uso, $estrato);
                                    } else {
                                        $bfm = buscarformula($id_concepto, $uso, $estrato);    
                                    }
                                    $bt  = $bfm[0];
                                    $df  = $bfm[1];
                                    $formula_e = $bfm[2];
                                    if($df==1){
                                        $formula = despejarformula($formula_e,$cantidad,$id_factura,$deuda_anterior, $estrato,$periodo, $uso,$id_concepto);
                                        $valor = $Cal->calculate($formula); // 12
                                        $valor = round($valor,0);
                                    }elseif($bt==1){
                                        $ids =$id_concepto;
                                        $valor = buscarvalort($ids,$uso,$estrato,$periodo, $panno);
                                    } else {
                                       $valor =0;
                                    }

                                    if($valor>0){
                                if($tipo_operacion==3){
                                    $valor = $valor*-1;
                                }
                                #****** Guardar Concepto En Factura ******#
                                $sql_cons ="INSERT INTO `gp_detalle_factura` 
                                        ( `factura`, `concepto_tarifa`, `valor`,
                                    `cantidad`,`iva`,`impoconsumo`,
                                    `ajuste_peso`,`valor_total_ajustado`) 
                                VALUES  (:factura,  :concepto_tarifa, :valor, 
                                    :cantidad,:iva,:impoconsumo,
                                    :ajuste_peso,:valor_total_ajustado)";
                                $sql_dato = array(
                                        array(":factura",$id_factura),
                                        array(":concepto_tarifa",$id_concepto),
                                        array(":valor",$valor),
                                        array(":cantidad",1),
                                        array(":iva",0),
                                        array(":impoconsumo",0),
                                        array(":ajuste_peso",0),
                                        array(":valor_total_ajustado",$valor),
                                );
                                $resp       = $con->InAcEl($sql_cons,$sql_dato);
                                $valor_tf += $valor;
                                
                                #* Buscar si tiene conceptos asociados distribucion costos
                                $cas = $con->Listar("SELECT c.id_unico, c.nombre , t.valor FROM gp_concepto c 
                                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto 
                                        AND ct.parametrizacionanno = $panno 
                                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                                    WHERE c.concepto_asociado 
                                        IN (SELECT GROUP_CONCAT(ca.id_unico) FROM gp_concepto ca 
                                            WHERE ca.concepto_asociado = $id_concepto)");
                                if(!empty($cas[0][0])){
                                    for ($ci = 0; $ci < count($cas); $ci++) {
                                        $cag = $cas[$ci][0];
                                        $vag = $cas[$ci][2] * $cantidad;
                                        #****** Guardar Concepto En Tabla Costos ******#
                                        $sql_cons ="INSERT INTO `gp_costos_consumo` 
                                                ( `factura`, `concepto`, `valor`) 
                                        VALUES  (:factura,  :concepto, :valor)";
                                        $sql_dato = array(
                                                array(":factura",$id_factura),
                                                array(":concepto",$cag),
                                                array(":valor",$vag),
                                        );
                                        $resp   = $con->InAcEl($sql_cons,$sql_dato);
                                    }
                                }
                            }
                                }
                            }
                        }
                    }
                    #*** Buscar Conceptos Asociados Al Tipo Servicio ***#
                    $cnp = $con->Listar("SELECT DISTINCT c.id_unico, ts.id_unico, 
                        c.tipo_operacion, c.nombre  
                        FROM gp_concepto_servicio cs 
                        LEFT JOIN gp_concepto c ON cs.concepto = c.id_unico 
                        LEFT JOIN gp_tipo_servicio ts ON cs.tipo_servicio = ts.id_unico 
                        WHERE ts.asociado = $tipo_s ORDER BY c.id_unico");
                    for ($c = 0; $c < count($cnp); $c++) {
                        $g = 1;
                        if($vacia==1){
                            if (strpos($cnp[$c][3], 'Mora') == false) {
                                $g = 0;
                            }
                        } 
                        if($g==1) {
                            $tipo_servicio = $cnp[$c][1];
                            $id_concepto   = $cnp[$c][0];
                            $tipo_operacion= $cnp[$c][2];
                            $uts = $con->Listar("SELECT id_unico, aplica_interes FROM gp_unidad_vivienda_servicio 
                                WHERE unidad_vivienda = $uv 
                                AND tipo_servicio = $tipo_servicio AND estado_servicio = 1");

                            if(count($uts)>0){ 
                                if (strpos($cnp[$c][3], 'Mora') == true && $uts[0][1]==2) {
                                } else {
                                    $bt =0;
                                    $df = 0;
                                    $formula_e = 0;
                                    if($cantidad ==0 ){
                                        $bfm = buscarformula0($id_concepto, $uso, $estrato);
                                    } else {
                                        $bfm = buscarformula($id_concepto, $uso, $estrato);    
                                    }
                                    $bt  = $bfm[0];
                                    $df  = $bfm[1];
                                    $formula_e = $bfm[2];
                                    #*** Si tiene formula, buscar si tiene tarifa con uso ***#
                                    if($df==1){
                                        $ids =$id_concepto;
                                        $tr = $con->Listar("SELECT t.valor 
                                            FROM gp_concepto_tarifa ct 
                                            LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
                                            WHERE ct.concepto IN ($ids) AND t.uso = '$uso'
                                            AND ct.parametrizacionanno = $panno ");
                                        if(count($tr)>0){
                                            $df=0;
                                            $bt=1;
                                        }                                
                                    }
                                    if($df==1){
                                        $formula = despejarformula($formula_e,$cantidad,$id_factura,$deuda_anterior, $estrato,$periodo, $uso,$id_concepto);
                                        $valor = $Cal->calculate($formula); // 12
                                        $valor = round($valor,0);
                                    }elseif($bt==1){
                                        $ids =$id_concepto;
                                        $valor = buscarvalort($ids,$uso,$estrato,$periodo, $panno);
                                    } else {
                                        $valor =0;
                                    }
                                    if($valor>0){
                                    if($tipo_operacion==3){
                                        $valor = $valor*-1;
                                    }
                                    #****** Guardar Concepto En Factura ******#
                                    $sql_cons ="INSERT INTO `gp_detalle_factura` 
                                            ( `factura`, `concepto_tarifa`, `valor`,
                                        `cantidad`,`iva`,`impoconsumo`,
                                        `ajuste_peso`,`valor_total_ajustado`) 
                                    VALUES  (:factura,  :concepto_tarifa, :valor, 
                                        :cantidad,:iva,:impoconsumo,
                                        :ajuste_peso,:valor_total_ajustado)";
                                    $sql_dato = array(
                                            array(":factura",$id_factura),
                                            array(":concepto_tarifa",$id_concepto),
                                            array(":valor",$valor),
                                            array(":cantidad",1),
                                            array(":iva",0),
                                            array(":impoconsumo",0),
                                            array(":ajuste_peso",0),
                                            array(":valor_total_ajustado",$valor),
                                    );
                                    $resp       = $con->InAcEl($sql_cons,$sql_dato);
                                    $valor_tf += $valor;

                                    #* Buscar si tiene conceptos asociados 
                                    $cas = $con->Listar("SELECT c.id_unico, c.nombre , t.valor FROM gp_concepto c 
                                        LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto 
                                            AND ct.parametrizacionanno = $panno 
                                        LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                                        WHERE c.concepto_asociado 
                                            IN (SELECT GROUP_CONCAT(ca.id_unico) FROM gp_concepto ca 
                                                WHERE ca.concepto_asociado = $id_concepto)");
                                    if(!empty($cas[0][0])){
                                        for ($ci = 0; $ci < count($cas); $ci++) {
                                            $cag = $cas[$ci][0];
                                            $vag = $cas[$ci][2] * $cantidad;
                                            #****** Guardar Concepto En Tabla Costos ******#
                                            $sql_cons ="INSERT INTO `gp_costos_consumo` 
                                                    ( `factura`, `concepto`, `valor`) 
                                            VALUES  (:factura,  :concepto, :valor)";
                                            $sql_dato = array(
                                                    array(":factura",$id_factura),
                                                    array(":concepto",$cag),
                                                    array(":valor",$vag),
                                            );
                                            $resp   = $con->InAcEl($sql_cons,$sql_dato);
                                        }
                                    }

                                }
                                }
                            } else {
                                #******** Buscar Conceptos Mora Del Tipo Servicio *****#
                                $uts = $con->Listar("SELECT c.id_unico  
                                        FROM gp_concepto_servicio cs 
                                        LEFT JOIN gp_concepto c ON cs.concepto = c.id_unico 
                                        WHERE tipo_servicio = $tipo_servicio AND c.id_unico = $id_concepto AND c.nombre LIKE '%Mora%'");
                                if(count($uts)>0 ){ 
                                    $uts2 = $con->Listar("SELECT id_unico, aplica_interes FROM gp_unidad_vivienda_servicio 
                                        WHERE unidad_vivienda = $uv 
                                        AND tipo_servicio = $tipo_servicio AND estado_servicio = 1");

                                    if(count($uts2)>0 && $uts2[0][1]==1){ 
                                        $bt =0;
                                        $df = 0;
                                        $formula_e = 0;
                                        if($cantidad ==0 ){
                                            $bfm = buscarformula0($id_concepto, $uso, $estrato);
                                        } else {
                                            $bfm = buscarformula($id_concepto, $uso, $estrato);    
                                        }
                                        $bt  = $bfm[0];
                                        $df  = $bfm[1];
                                        $formula_e = $bfm[2];
                                        #*** Si tiene formula, buscar si tiene tarifa con uso ***#
                                        if($df==1){
                                            $ids =$id_concepto;
                                            $tr = $con->Listar("SELECT t.valor 
                                                FROM gp_concepto_tarifa ct 
                                                LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
                                                WHERE ct.concepto IN ($ids) AND t.uso = '$uso' 
                                                AND ct.parametrizacionanno = $panno ");
                                            if(count($tr)>0){
                                                $df=0;
                                                $bt=1;
                                            }                                
                                        }

                                        if($df==1){
                                            $formula = despejarformula($formula_e,$cantidad,$id_factura,$deuda_anterior, $estrato,$periodo, $uso,$id_concepto);
                                            $valor = $Cal->calculate($formula); // 12
                                            $valor = round($valor,0);
                                        }elseif($bt==1){
                                            $ids =$id_concepto;
                                            $valor = buscarvalort($ids,$uso,$estrato,$periodo, $panno);
                                        } else {
                                            $valor =0;
                                        }
                                        if($valor>0){
                                            if($tipo_operacion==3){
                                                $valor = $valor*-1;
                                            }
                                            #****** Guardar Concepto En Factura ******#
                                            $sql_cons ="INSERT INTO `gp_detalle_factura` 
                                                    ( `factura`, `concepto_tarifa`, `valor`,
                                                `cantidad`,`iva`,`impoconsumo`,
                                                `ajuste_peso`,`valor_total_ajustado`) 
                                            VALUES  (:factura,  :concepto_tarifa, :valor, 
                                                :cantidad,:iva,:impoconsumo,
                                                :ajuste_peso,:valor_total_ajustado)";
                                            $sql_dato = array(
                                                    array(":factura",$id_factura),
                                                    array(":concepto_tarifa",$id_concepto),
                                                    array(":valor",$valor),
                                                    array(":cantidad",1),
                                                    array(":iva",0),
                                                    array(":impoconsumo",0),
                                                    array(":ajuste_peso",0),
                                                    array(":valor_total_ajustado",$valor),
                                            );
                                            $resp       = $con->InAcEl($sql_cons,$sql_dato);
                                            $valor_tf += $valor;
                                        }
                                    }
                                }
                            }
                        }                        
                    }   
                    
                    if(empty($_REQUEST['dn'])){
                        #**** Buscar Otros Conceptos ***#
                        $ot = $con->Listar("SELECT * FROM gf_otros_conceptos 
                            WHERE unidad_vivienda_ms IN($ids_uv) AND cuotas_pendientes>0");
                        if(count($ot)>0){
                            for ($o = 0; $o < count($ot); $o++) {
                                $id_concepto_o = $ot[$o][1];
                                #*** Buscar Cuotas ***#
                                $total_c = $ot[$o][5];
                                $valor_o = $ot[$o][4];

                                #****** Guardar Concepto En Factura ******#
                                $sql_cons ="INSERT INTO `gp_detalle_factura` 
                                        ( `factura`, `concepto_tarifa`, `valor`,
                                    `cantidad`,`iva`,`impoconsumo`,
                                    `ajuste_peso`,`valor_total_ajustado`, `otros_conceptos`) 
                                VALUES  (:factura,  :concepto_tarifa, :valor, 
                                    :cantidad,:iva,:impoconsumo,
                                    :ajuste_peso,:valor_total_ajustado, :otros_conceptos)";
                                $sql_dato = array(
                                        array(":factura",$id_factura),
                                        array(":concepto_tarifa",$id_concepto_o),
                                        array(":valor",$valor_o),
                                        array(":cantidad",1),
                                        array(":iva",0),
                                        array(":impoconsumo",0),
                                        array(":ajuste_peso",0),
                                        array(":valor_total_ajustado",$valor_o),
                                        array(":otros_conceptos",$ot[$o][0]),
                                );
                                $resp       = $con->InAcEl($sql_cons,$sql_dato);
                                $valor_tf  += $valor_o;
                                #**** Actualizar Otros Conceptos ***#
                                $upd = $sql_cons ="UPDATE `gf_otros_conceptos`  
                                    SET  `cuotas_pagas`=:cuotas_pagas, 
                                    `cuotas_pendientes` =:cuotas_pendientes 
                                    WHERE `id_unico` =:id_unico ";
                                $sql_dato = array(
                                        array(":cuotas_pagas",$ot[$o][6]+1),
                                        array(":cuotas_pendientes",$ot[$o][7]-1),
                                        array(":id_unico",$ot[$o][0]),
                                );
                                $resp       = $con->InAcEl($sql_cons,$sql_dato);
                            }
                        }
                    }
                    #*** Ajuste Factura***#
                    #*** Buscar Concepto Ajuste ****#
                    $ca = $con->Listar("SELECT * FROM gp_concepto WHERE nombre ='ajuste'");
                    $val_ajs = ROUND($valor_tf,-2);
                    $valor_ajuste = $val_ajs - $valor_tf;
                    #****** Guardar Concepto En Factura ******#
                    $sql_cons ="INSERT INTO `gp_detalle_factura` 
                            ( `factura`, `concepto_tarifa`, `valor`,
                        `cantidad`,`iva`,`impoconsumo`,
                        `ajuste_peso`,`valor_total_ajustado`) 
                    VALUES  (:factura,  :concepto_tarifa, :valor, 
                        :cantidad,:iva,:impoconsumo,
                        :ajuste_peso,:valor_total_ajustado)";
                    $sql_dato = array(
                            array(":factura",$id_factura),
                            array(":concepto_tarifa",$ca[0][0]),
                            array(":valor",$valor_ajuste),
                            array(":cantidad",1),
                            array(":iva",0),
                            array(":impoconsumo",0),
                            array(":ajuste_peso",0),
                            array(":valor_total_ajustado",$valor_ajuste),
                    );
                    $resp       = $con->InAcEl($sql_cons,$sql_dato);
                    if(empty($resp)){
                        $tfr += 1;
                    }
                     reconstruirComprobantesFactura($id_factura);
                       
                }
            } else {
                $html = 'No Se Encontró Tipo Factura';
                $rta  = 1;
            }
        }
       
        $datos = array(); 
        $datos = array("html"=>$html,"rta"=>$rta,"total"=>$tfr);
        echo json_encode($datos);
    break;
    
    #** Guardar Paquete **#
    case 2:
        
        $sql_cons ="INSERT INTO `gp_paquete` 
                ( `banco`, `fecha`, `numero`,
            `cupones`,`tipo_pago`,`valor`) 
        VALUES  (:banco,  :fecha, :numero, 
            :cupones,:tipo_pago,:valor)";
        $sql_dato = array(
                array(":banco",$_REQUEST['banco']),
                array(":fecha", fechaC($_REQUEST['fecha'])),
                array(":numero",$_REQUEST['paquete']),
                array(":cupones",$_REQUEST['cupones']),
                array(":tipo_pago",$_REQUEST['tipoRecaudo']),
                array(":valor",$_REQUEST['valor']),
        );
        $resp       = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $id = $con->Listar("SELECT * FROM gp_paquete WHERE tipo_pago =".$_REQUEST['tipoRecaudo']);
            $rta =$id[0][0];
        } else {
            $rta =0;
        }
        echo $rta;
    break;
    
    #*** Guardar Pago *****#
    case 3:
        $numr = $_REQUEST['num'];
        
        $facturas = $_REQUEST['f'];
        $arrayfacturas = array();
        $arrayfacturas = explode(",", $facturas);
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
        #*** Concepto 
        for ($i = 0; $i < ($numr); $i++) {
            $nc = 'factura'.$i;  
            #echo $i;
            #var_dump($_REQUEST[$nc]);            
            if(!empty($_REQUEST[$nc])){
                $id_factura = $_REQUEST[$nc];

                if(in_array($id_factura, $arrayfacturas)) {
                    $nc         = 'valor_rr'.$i;
                    $valor      = $_REQUEST[$nc];
                    $factura    = $id_factura;
                    $tipoPago   = $_REQUEST['tipoRecaudo'];
                    $banco      = $_REQUEST['banco'];
                    #Buscar Datos Factura
                    $df = $con->Listar("SELECT f.id_unico,
                                f.numero_factura, tp.nombre,
                                f.tercero, f.descripcion, f.fecha_factura, f.centrocosto,
                                f.unidad_vivienda_servicio   
                            FROM gp_factura f LEFT JOIN gp_tipo_factura tp ON tp.id_unico = f.tipofactura 
                            WHERE f.id_unico = $factura");
                    $fecha      = fechaC($_REQUEST['fecha']);
                    $responsable= $df[0][3];
                    $centrocosto= $df[0][6];
                    
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
        } 
        echo $dr;
    break;
    #** Generar Factura De Nuevo ***#
    case 4:
        $factura = $_REQUEST['factura'];
        #** Buscar Si Tiene Comprobantes Asociados ***#
        $cnt    = 0;
        $pptal  = 0;
        $rowD = $con->Listar("SELECT      cnt.id_unico as cnt,ptal.id_unico as ptal
                    FROM        gp_factura pg, gp_tipo_factura tpg, gf_tipo_comprobante tpc,gf_comprobante_cnt cnt, gf_tipo_comprobante_pptal tcp,gf_comprobante_pptal ptal
                    WHERE       pg.tipofactura = tpg.id_unico
                    AND         tpc.id_unico = tpg.tipo_comprobante
                    AND         cnt.tipocomprobante = tpc.id_unico
                    AND         tpc.comprobante_pptal = tcp.id_unico
                    AND         ptal.tipocomprobante = tcp.id_unico
                    AND         pg.numero_factura = ptal.numero
                    AND         pg.numero_factura = cnt.numero
                    AND         pg.id_unico =  $factura");
        if(count($rowD)>0){
            $cnt    = $rowD[0][0];
            $pptal  = $rowD[0][1];
        }else{
            $row = $con->Listar("SELECT dtc.comprobante FROM gp_detalle_factura dtf
            LEFT JOIN gf_detalle_comprobante dtc ON dtc.id_unico = dtf.detallecomprobante  WHERE dtf.factura = $factura");
            if(count($row)>0){
                $cnt    = $row[0][0];
                $pptal  = 0;
            }else{
                $cnt    = 0;
                $pptal  = 0;
            }
        }
        $sql_cons ="UPDATE  `gp_detalle_factura`
        SET `detallecomprobante` =:detallecomprobante 
        WHERE `factura`=:factura ";
        $sql_dato = array(
            array(":detallecomprobante",NULL),
            array(":factura",$factura),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        $sql_cons ="UPDATE  `gf_detalle_comprobante`
        SET `detalleafectado` =:detalleafectado 
        WHERE `comprobante`=:comprobante ";
        $sql_dato = array(
            array(":detalleafectado",NULL),
            array(":comprobante",$cnt),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        eliminardetallescnt($cnt);
        eliminardetallespptal($pptal);
        $sql_cons ="DELETE FROM  `gp_detalle_factura`
        WHERE `factura`=:factura AND otros_conceptos IS NULL";
        $sql_dato = array(
            array(":factura",$factura)
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        $sql_cons ="DELETE FROM  `gp_costos_consumo`
        WHERE `factura`=:factura ";
        $sql_dato = array(
            array(":factura",$factura)
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            echo 0;
        } else {
            echo 1;
        }
    break;

    #** Comprobar Acrvhivo recaudo efecty **#
    case 5:

        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();                  
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName);             
        $worksheetList  = $objReader->listWorksheetNames($inputFileName);
        $html           = "Facturas No Encontradas:".'<br/>';
        $t              = 0;
        #*** Validar Facturas ***#
        $objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);    
        $total_filas    = $objWorksheet->getHighestRow();                   
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        for ($f = 2; $f <= $total_filas; $f++) {
            $linea1 = $objWorksheet->getCellByColumnAndRow(10, $f)->getCalculatedValue();
            if(!empty($linea1) || $linea1 !=""){
                #** Buscar Código Factura **#
                $sc             = $con->Listar("SELECT * FROM gp_factura WHERE numero_factura =$linea1");
                if(count($sc)<=0){
                    $html .=$linea1.'<br/>';
                    $t+=1;
                }
            }
        }
        $datos = array();
        $datos = array("rta"=>$t,"html"=>$html);
        echo json_encode($datos);
    break;

    #** Guardar Archivo Recaudo Efecty **#
    case 6:
        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();                  
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName);             
        $worksheetList  = $objReader->listWorksheetNames($inputFileName);
        $html           = "Facturas No Encontradas:".'<br/>';
        $dr             = 0;
        $tipoPago       = $_REQUEST['tipo_recaudo'];
        $banco          = $_REQUEST['banco'];
        #*** Validar Facturas ***#
        $objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);    
        $total_filas    = $objWorksheet->getHighestRow();                   
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        for ($f = 2; $f <= $total_filas; $f++) {
            $linea1     = $objWorksheet->getCellByColumnAndRow(10, $f)->getCalculatedValue();
            $fecha      = $objWorksheet->getCellByColumnAndRow(6, $f)->getCalculatedValue();
            $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fecha);
            $fecha      = date("Y-m-d",$timestamp);
            $valor      = $objWorksheet->getCellByColumnAndRow(4, $f)->getCalculatedValue();
            if(!empty($linea1) || $linea1 !=""){
                #** Buscar Código Factura **#
                $df            = $con->Listar("SELECT f.id_unico,
                    f.numero_factura, tp.nombre,
                    f.tercero, f.descripcion, f.fecha_factura, 
                    f.centrocosto,f.unidad_vivienda_servicio, f.periodo 
                    FROM gp_factura f 
                    LEFT JOIN gp_tipo_factura tp ON f.tipofactura = tp.id_unico 
                    WHERE f.numero_factura =$linea1");
                if(count($df)>0){
                    $factura    = $df[0][0];
                    $fecha      = $fecha;
                    $responsable= $df[0][3];
                    $centrocosto= $df[0][6];
                    $iduv       = $df[0][7];
                    $periodo    = $df[0][8];
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
                    #*** Buscar el saldo de la factura ***#
                    $saldofactura = saldoFactura($factura);
                    #echo 'Factura'.$factura;' Saldo Factura'.$saldofactura;'Valor Inicial'.$valor;
                    if($valor>0){
                        if($valor>$saldofactura){
                            $abono = $saldofactura;
                        } else {$abono = $valor;}
                     #   echo 'Abono'.$abono;
                        $dp = guardarPagoFactura('',$factura,$pago, $abono);
                        $valor =$valor-$abono;
                    }
                    #echo 'Valor Final'.$valor;
                    if($valor>0){
                        #********* Buscar Si Hay Facturas de La unidad de vivienda con saldo *******#
                        $fcs = $con->Listar("SELECT * FROM gp_factura 
                            WHERE unidad_vivienda_servicio = $iduv 
                            AND id_unico != $factura AND periodo <$periodo ORDER BY fecha_factura ASC");
                        for ($if = 0; $if < count($fcs); $if++) {
                            if($valor>0){
                                $idf = $fcs[$if][0];
                                $saldof = saldoFactura($idf);
                                if($saldof>0){
                                    if($saldof>$valor){
                                        $saldof = $valor;
                                    }
                                    $dp = guardarPagoFactura('',$idf,$pago, $saldof);
                                    $valor = $valor-$saldof;
                                }
                            }
                        }
                    }
                    
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
                    if($reg==true){
                        $dr +=1;
                    }                  
                    
                }
            }
        }
        $datos = array();
        $datos = array("rta"=>$dr,"html"=>$html);
        echo json_encode($datos);
    break;
    
    #*** Cambiar Estado Medidor ***#
    case 7:
        $estado   = $_REQUEST['estado'];
        $medidor  = $_REQUEST['medidor'];
        $sql_cons ="UPDATE `gp_medidor` 
                SET `estado_medidor`=:estado_medidor
            WHERE `id_unico`=:id_unico";
        $sql_dato = array(
                array(":estado_medidor",$estado),
                array(":id_unico",$medidor),
        );
        $resp       = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
           echo 0;
        } else {
           echo 1;
        }

        
    break;
    #* Modal Ver distribución
    case 8:
        $id_factura = $_REQUEST['factura'];
        $rc = "SELECT  dp.id_unico, c.nombre, dp.valor  
            FROM gp_costos_consumo dp 
            LEFT JOIN gp_concepto c ON dp.concepto = c.id_unico 
           WHERE dp.factura=$id_factura";
       $rc = $mysqli->query($rc);
       $html   = ""; 
       $html  .= '<table border="1" style="width:100%">'; 
       $html .= '<tr>';
       $html .= '<td style="text-align:center"><strong><i>Concepto</i></strong></td>';
       $html .= '<td style="text-align:center"><strong><i>Valor</i></strong></td>';
       $html .= '</tr>';
       $tc    = 0;
       while ($row1 = mysqli_fetch_row($rc)) { 
           $html .= '<tr>';
           $html .= '<td>'.ucwords($row1[1]).'</td>';
           $html .= '<td style="text-align:right">'.number_format($row1[2],2,'.',',').'</td>';
           $html .= '</tr>';
           $tc   += $row1[2];
       }
       $html .= '<tr>';
       $html .= '<td style="text-align:left"><strong><i>TOTAL: </i></strong></td>';
       $html .= '<td style="text-align:right"><strong><i>'.number_format($tc,2,'.',',').'</i></strong></td>';
       $html .= '</tr>';
       $html .= "</table>";  
       echo $html;
    break;
        
}
