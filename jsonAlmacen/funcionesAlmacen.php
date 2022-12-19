<?php 
function reconstruirSalida($id_factura){
    @session_start();
    global $con;
    $rta = 0;
    $panno      = $_SESSION['anno'];
    $compania   = $_SESSION['compania'];
    $bdtf = $con->Listar("SELECT tc.id_unico, tc.comprobante_pptal, tf.tipo_movimiento, 
        f.numero_factura,f.fecha_factura, f.descripcion, f.tercero, f.vendedor, 
        f.centrocosto, f.descuento, f.proyecto 
        FROM gp_factura f 
        LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
        LEFT JOIN gf_tipo_comprobante tc ON tf.tipo_comprobante = tc.id_unico 
        WHERE f.id_unico = $id_factura");
    $sql_cons ="UPDATE `gp_detalle_factura`
    SET `detallemovimiento`=:detallemovimiento 
    WHERE `factura`=:factura ";
    $sql_dato = array(
        array(":detallemovimiento",$iddm),
        array(":factura",$id_factura)
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    if(!empty($bdtf[0][2])){
        #* Buscar si existe 
        $mve = $con->Listar("SELECT * FROM gf_movimiento WHERE numero = '".$bdtf[0][3]."' "
                . "AND tipomovimiento  = ".$bdtf[0][2]);
        if(count($mve)>0){
            $id_movimiento = $mve[0][0];
            $sql_cons ="DELETE FROM  `gf_detalle_movimiento`
            WHERE `movimiento`=:movimiento ";
            $sql_dato = array(
                array(":movimiento",$id_movimiento)
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        } else { 
            $dpd = $con->Listar("SELECT dependencia FROM gf_dependencia_responsable 
                WHERE responsable = ".$_SESSION['usuario_tercero']);
            $sql_cons ="INSERT INTO `gf_movimiento`
            ( `numero`, `fecha`, `descripcion`, `tipomovimiento`, `parametrizacionanno`, 
            `compania`, `tercero`, `tercero2`, `dependencia`, `centrocosto`,  `estado`, 
            `descuento`, `fecha_hora`, `factura`, `proyecto`)
            VALUES (:numero, :fecha, :descripcion, :tipomovimiento, :parametrizacionanno,
            :compania, :tercero, :tercero2, :dependencia, :centrocosto, 
            :estado, :descuento, :fecha_hora, :factura, :proyecto)";
            $sql_dato = array(
                array(":numero",$bdtf[0][3]),
                array(":fecha",$bdtf[0][4]),
                array(":descripcion",$bdtf[0][5]),
                array(":tipomovimiento",$bdtf[0][2]),
                array(":parametrizacionanno",$panno),
                array(":compania",$compania),
                array(":tercero",$bdtf[0][6]),
                array(":tercero2",$bdtf[0][7]),
                array(":dependencia",$dpd[0][0]),
                array(":centrocosto",$bdtf[0][8]),
                array(":estado",2),
                array(":descuento",$bdtf[0][9]),
                array(":fecha_hora",date('Y-m-d H:i:s')),
                array(":factura",$id_factura),
                array(":proyecto",$bdtf[0][10]), 
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            $mve = $con->Listar("SELECT * FROM gf_movimiento WHERE numero = '".$bdtf[0][3]."' "
                . "AND tipomovimiento  = ".$bdtf[0][2]."");
            $id_movimiento = $mve[0][0];
        }
        if(!empty($id_movimiento)){
            $rowd       = $con->Listar("SELECT dp.id_unico, dp.concepto_tarifa, 
                    dp.valor, dp.cantidad, dp.unidad_origen 
                FROM gp_detalle_factura dp
                WHERE dp.factura =$id_factura");
            for ($d = 0; $d < count($rowd); $d++) {
                $id_detalle=$rowd[$d][0];
                $concepto = $rowd[$d][1];
                $cantidad = $rowd[$d][3];
                $unidad   = $rowd[$d][4];
                $elemento   = obtnerConceptoPlanI($concepto);
                if(!empty($elemento)){
                    $factor     = obtenerUnidadFactor($unidad, $concepto);
                    if(empty($factor) || $factor=='0.00'){
                        $xxx        = $cantidad;
                    } elseif($factor==0){
                        $xxx        = $cantidad;
                    }else { 
                        $xxx        = $cantidad * $factor;
                    }
                    $xsaldoV    = obtenerSaldoPlan($elemento);
                    $xsaldoC    = obtnerCantidadPlan($elemento);
                    $xCantE     = obtenerSaldoEntradaPlan($elemento);
                    $xvalor     = 0;

                    if(!empty($xsaldoV) || !empty($xsaldoC)){
                        $xvalor  = ((( $xsaldoV / $xsaldoC ) * 1 ) / 1 );
                    }

                    if($xsaldoV < 0){
                        $xvalor = buscarValorMaximoElemento($elemento);
                    }

                    if($xsaldoC < 0 || empty($xsaldoC)){
                        if(empty($xCantE)){
                            $xvalor = 0;
                        }else{
                            $xvalor = buscarValorMaximoElemento($elemento);
                        }
                    }

                    if($factor == 0){
                        $xvalor = 0;
                    }
                    date_default_timezone_set('America/Bogota');
                    $hora = date('H:i');
                    $sql_cons ="INSERT INTO `gf_detalle_movimiento`
                    ( `cantidad`, `valor`, `iva`,`movimiento`, `planmovimiento`, 
                    `hora`, `unidad_origen`, `cantidad_origen`)
                    VALUES (:cantidad, :valor, :iva, :movimiento, :planmovimiento, 
                    :hora, :unidad_origen, :cantidad_origen)";
                    $sql_dato = array(
                        array(":cantidad",$xxx),
                        array(":valor",$xvalor),
                        array(":iva",0),
                        array(":movimiento",$id_movimiento),
                        array(":planmovimiento",$elemento),
                        array(":hora",$hora),
                        array(":unidad_origen",$unidad),
                        array(":cantidad_origen",$cantidad),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    if(empty($resp)){
                        $bi = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_movimiento WHERE movimiento = $id_movimiento");
                        $iddm = $bi[0][0];
                        $sql_cons ="UPDATE `gp_detalle_factura`
                        SET `detallemovimiento`=:detallemovimiento 
                        WHERE `id_unico`=:id_unico ";
                        $sql_dato = array(
                            array(":detallemovimiento",$iddm),
                            array(":id_unico",$id_detalle)
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($resp)){
                            $rta+=1 ;
                        }
                    }
                }
            }
            
            reconstruirSalidaCnt($id_factura);
        }
    }
    return $rta;
    
}

function reconstruirSalidaDetalle($id_mov, $id_detalle,$concepto, $unidad, $cantidad){
    global $con;
    $elemento = obtnerConceptoPlanI($concepto);
    if(!empty($elemento)){
        $factor     = obtenerUnidadFactor($unidad, $concepto);
        if(empty($factor) || $factor=='0.00'){
            $xxx        = $cantidad;
        } elseif($factor==0){
            $xxx        = $cantidad;
        }else { 
            $xxx        = $cantidad * $factor;
        }
        $xsaldoV    = obtenerSaldoPlan($elemento);
        $xsaldoC    = obtnerCantidadPlan($elemento);
        $xCantE     = obtenerSaldoEntradaPlan($elemento);
        $xvalor     = 0;
        
        if(!empty($xsaldoV) || !empty($xsaldoC)){
            $xvalor  = ((( $xsaldoV / $xsaldoC ) * 1 ) / 1 );
        }

        if($xsaldoV < 0){
            $xvalor = buscarValorMaximoElemento($elemento);
        }

        if($xsaldoC < 0 || empty($xsaldoC)){
            if(empty($xCantE)){
                $xvalor = 0;
            }else{
                $xvalor = buscarValorMaximoElemento($elemento);
            }
        }

        if($factor == 0){
            $xvalor = 0;
        }
        date_default_timezone_set('America/Bogota');
        $hora = date('H:i');
        $sql_cons ="INSERT INTO `gf_detalle_movimiento`
        ( `cantidad`, `valor`, `iva`,`movimiento`, `planmovimiento`, 
        `hora`, `unidad_origen`, `cantidad_origen`)
        VALUES (:cantidad, :valor, :iva, :movimiento, :planmovimiento, 
        :hora, :unidad_origen, :cantidad_origen)";
        $sql_dato = array(
            array(":cantidad",$xxx),
            array(":valor",$xvalor),
            array(":iva",0),
            array(":movimiento",$id_mov),
            array(":planmovimiento",$elemento),
            array(":hora",$hora),
            array(":unidad_origen",$unidad),
            array(":cantidad_origen",$cantidad),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $bi = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_movimiento WHERE movimiento = $id_mov");
            $iddm = $bi[0][0];
            $sql_cons ="UPDATE `gp_detalle_factura`
            SET `detallemovimiento`=:detallemovimiento 
            WHERE `id_unico`=:id_unico ";
            $sql_dato = array(
                array(":detallemovimiento",$iddm),
                array(":id_unico",$id_detalle)
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            reconstruirSalidaCntDetalle($iddm,$id_detalle);
        }
    }
}

function obtnerConceptoPlanI($concepto){
    global $con;
    try{
        $xxx = 0;
        $row = $con->Listar("SELECT    con.plan_inventario
            FROM      gp_concepto  as con
            WHERE     con.id_unico = $concepto");
        if(!empty($row[0][0])){
            $xxx = $row[0][0];
        }
        return $xxx;
    }catch (Exception $e){
        die($e->getMessage());
    }
    
}
function obtenerUnidadFactor($unidad, $concepto){
    global $con;
    try {
        $xxx = 0;
        $row = $con->Listar("SELECT    geu.valor_conversion
                FROM      gp_concepto_tarifa AS gct
                LEFT JOIN gf_elemento_unidad AS geu ON gct.elemento_unidad = geu.id_unico
                WHERE     gct.concepto       = $concepto
                AND       geu.unidad_empaque = $unidad");
        if(!empty($row[0][0])){
            $xxx = $row[0][0];
        }
        return $xxx;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}
function obtenerSaldoPlan($plan){
    global $con;
    $xe = obtenerSaldoEntradaPlan($plan);
    $xs = obtenerSaldoSalidaPlan($plan);
    $xx = $xe - $xs;
    return $xx;
}
function obtenerSaldoEntradaPlan($id){
    global $con;
    try {
        $xxx = 0;
        $row = $con->Listar("SELECT    (dtm.valor) * dtm.cantidad
                FROM      gf_detalle_movimiento  dtm
                LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
                LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                WHERE (pln.id_unico = $id)
                AND   (pro.baja IS NULL OR pro.baja = 0)
                AND   (tpm.clase = 2)");
        if(count($row)> 0){
            for ($i = 0; $i < count($row); $i++) {
                $xxx += $row[$i][0];
            }
        }
        return $xxx;
    } catch (Exception $e) {
        die($e->getMessage());
    }
}
function obtenerSaldoSalidaPlan($id){
    global $con;
    try {
        $xxx = 0;
        $row = $con->Listar("SELECT    (dtm.valor) * dtm.cantidad
                FROM      gf_detalle_movimiento  dtm
                LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
                LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                WHERE (pln.id_unico = $id)
                AND   (pro.baja IS NULL OR pro.baja = 0)
                AND   (tpm.clase = 3)");
        if(count($row)> 0){
            for ($i = 0; $i < count($row); $i++) {
                $xxx += $row[$i][0];
            }
        }
        return $xxx;
    } catch (Exception $e) {
        die($e->getMessage());
    }
}
function obtnerCantidadPlan($plan){
    global $con;
    $xe = obtnerCantidadProductosPlan($plan);
    $xs = obtnerCantidadProductosPlanSalida($plan);
    $xx = $xe - $xs;
    return $xx;
}
function obtnerCantidadProductosPlan($id){
    global $con;
    try {
        $xxx = 0;
        $row = $con->Listar("SELECT    dtm.cantidad
            FROM      gf_detalle_movimiento  dtm
            LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
            LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
            LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
            LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
            LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
            WHERE (pln.id_unico = $id)
            AND   (pro.baja IS NULL OR pro.baja = 0)
            AND   (tpm.clase = 2)");
        if(count($row)> 0){
            for ($i = 0; $i < count($row); $i++) {
                $xxx += $row[$i][0];
            }
        }
        return $xxx;
    } catch (Exception $e) {
        die($e->getMessage());
    }
}
function obtnerCantidadProductosPlanSalida($id){
    global $con;
    try {
        $xxx = 0;
        $row = $con->Listar("SELECT    dtm.cantidad
            FROM      gf_detalle_movimiento  dtm
            LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
            LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
            LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
            LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
            LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
            WHERE (pln.id_unico = $id)
            AND   (pro.baja IS NULL OR pro.baja = 0)
            AND   (tpm.clase = 3)");
        if(count($row)> 0){
            for ($i = 0; $i < count($row); $i++) {
                $xxx += $row[$i][0];
            }
        }
        return $xxx;
    } catch (Exception $e) {
        die($e->getMessage());
    }
}
function buscarValorMaximoElemento($elemento){
    global $con;
    try {
        $xxx = 0;
        $row = $con->Listar("SELECT    gdm.valor
                FROM      gf_detalle_movimiento AS gdm
                LEFT JOIN gf_movimiento         AS gmv ON gdm.movimiento     = gmv.id_unico
                LEFT JOIN gf_tipo_movimiento    AS gtp ON gmv.tipomovimiento = gtp.id_unico
                WHERE     gdm.planmovimiento = $elemento
                AND       gdm.valor         != 0
                ORDER BY  gdm.id_unico DESC
                LIMIT     1");
        if(count($row)> 0){
            for ($i = 0; $i < count($row); $i++) {
                $xxx = $row[$i][0];
            }
        }
        return $xxx;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}
function reconstruirSalidaCnt($id_factura){
    global $con;
    try {
        $dconf = $con->Listar("SELECT DISTINCT ca.cuenta_debito, ca.cuenta_credito, 
                dm.valor, dm.cantidad, dc.comprobante, dc.fecha, 
                dc.tercero, dc.centrocosto ,cd.naturaleza, cc.naturaleza , 
                df.id_unico 
            FROM gf_detalle_movimiento dm 
            LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
            LEFT JOIN gf_configuracion_almacen ca ON m.tipomovimiento = ca.tipo_movimiento 
                AND ca.plan_inventario = dm.planmovimiento 
                AND ca.parametrizacion_anno = m.parametrizacionanno 
            LEFT JOIN gp_detalle_factura df ON df.detallemovimiento = dm.id_unico 
            LEFT JOIN gf_detalle_comprobante dc ON df.detallecomprobante = dc.id_unico 
            LEFT JOIN gf_cuenta cd ON ca.cuenta_debito = cd.id_unico 
            LEFT JOIN gf_cuenta cc ON ca.cuenta_credito = cc.id_unico 
            WHERE df.factura = $id_factura AND dc.comprobante IS NOT NULL AND ca.id_unico IS NOT NULL");
        if(count($dconf)>0){
            for ($i = 0; $i < count($dconf); $i++) {
                $valor = $dconf[$i][2] * $dconf[$i][3];
                if($dconf[$i][8]==1){ $valord = $valor;}else{$valord = $valor*-1;}
                if($dconf[$i][9]==2){ $valorc = $valor;}else{$valorc = $valor*-1;}
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto)";
                $sql_dato = array(
                        array(":fecha",$dconf[$i][5]),
                        array(":comprobante",$dconf[$i][4]),
                        array(":valor",($valord)),
                        array(":cuenta",$dconf[$i][0]),   
                        array(":naturaleza",$dconf[$i][8]),
                        array(":tercero",$dconf[$i][6]),
                        array(":centrocosto",$dconf[$i][7]),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto)";
                $sql_dato = array(
                        array(":fecha",$dconf[$i][5]),
                        array(":comprobante",$dconf[$i][4]),
                        array(":valor",($valorc)),
                        array(":cuenta",$dconf[$i][1]),   
                        array(":naturaleza",$dconf[$i][9]),
                        array(":tercero",$dconf[$i][6]),
                        array(":centrocosto",$dconf[$i][7]),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
        }
        
    } catch (Exception $ex) {

    }
}
function reconstruirSalidaCntDetalle($id_dmovimiento,$id_dfactura){
    global $con;
    try {
        $dconf = $con->Listar("SELECT ca.cuenta_debito, ca.cuenta_credito, 
                dm.valor, dm.cantidad, dc.comprobante, dc.fecha, 
                dc.tercero, dc.centrocosto ,cd.naturaleza, cc.naturaleza  
            FROM gf_detalle_movimiento dm 
            LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
            LEFT JOIN gf_configuracion_almacen ca ON m.tipomovimiento = ca.tipo_movimiento 
                AND ca.plan_inventario = dm.planmovimiento 
                AND ca.parametrizacion_anno = m.parametrizacionanno 
            LEFT JOIN gp_detalle_factura df ON df.detallemovimiento = dm.id_unico 
            LEFT JOIN gf_detalle_comprobante dc ON df.detallecomprobante = dc.id_unico 
            LEFT JOIN gf_cuenta cd ON ca.cuenta_debito = cd.id_unico 
            LEFT JOIN gf_cuenta cc ON ca.cuenta_credito = cc.id_unico 
            WHERE dm.id_unico = $id_dmovimiento AND dc.comprobante IS NOT NULL AND ca.id_unico IS NOT NULL");
        if(count($dconf)>0){
            $valor = $dconf[0][2] * $dconf[0][3];
            if($dconf[0][8]==1){ $valord = $valor;}else{$valord = $valor*-1;}
            if($dconf[0][9]==2){ $valorc = $valor;}else{$valorc = $valor*-1;}
            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                    ( `fecha`, `comprobante`,`valor`,
                    `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
            VALUES (:fecha,  :comprobante,:valor, 
                    :cuenta,:naturaleza, :tercero, :centrocosto)";
            $sql_dato = array(
                    array(":fecha",$dconf[0][5]),
                    array(":comprobante",$dconf[0][4]),
                    array(":valor",($valord)),
                    array(":cuenta",$dconf[0][0]),   
                    array(":naturaleza",$dconf[0][8]),
                    array(":tercero",$dconf[0][6]),
                    array(":centrocosto",$dconf[0][7]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                    ( `fecha`, `comprobante`,`valor`,
                    `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
            VALUES (:fecha,  :comprobante,:valor, 
                    :cuenta,:naturaleza, :tercero, :centrocosto)";
            $sql_dato = array(
                    array(":fecha",$dconf[0][5]),
                    array(":comprobante",$dconf[0][4]),
                    array(":valor",($valorc)),
                    array(":cuenta",$dconf[0][1]),   
                    array(":naturaleza",$dconf[0][9]),
                    array(":tercero",$dconf[0][6]),
                    array(":centrocosto",$dconf[0][7]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
        
    } catch (Exception $ex) {

    }
}
