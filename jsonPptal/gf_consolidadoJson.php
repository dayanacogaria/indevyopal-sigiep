<?php
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
require_once('../jsonPptal/funcionesPptal.php');
//require '../informes/INFORMES_PPTAL/consultas.php';
session_start(); 
$action     = $_REQUEST['action'];
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$calendario = CAL_GREGORIAN;
$con        = new ConexionPDO(); 
switch ($action){
    #* Ejecución Gastos
    case 1:
        #**********Recepción Variables ****************#
        $nanno      = anno($anno);
        $mesf       = $_REQUEST['sltmesf'];
        $fechaI     = $nanno.'-01-01';
        $diaF       = cal_days_in_month($calendario, $mesf, $nanno); 
        $fechaF     = $nanno.'-'.$mesf.'-'.$diaF;
        $codigoI    = $_REQUEST['sltcodi'];
        $codigoF    = $_REQUEST['sltcodf'];
        gastosConsolidado($codigoI, $codigoF, $anno,$fechaI,$fechaF, $nanno);
        echo 1;
    break;
    #* Ejecución Ingresos
    case 2:
        #**********Recepción Variables ****************#
        $nanno      = anno($anno);
        $mesf       = $_REQUEST['sltmesf'];
        $fechaI     = $nanno.'-01-01';
        $diaF       = cal_days_in_month($calendario, $mesf, $nanno); 
        $fechaF     = $nanno.'-'.$mesf.'-'.$diaF;
        $codigoI    = $_REQUEST['sltcodi'];
        $codigoF    = $_REQUEST['sltcodf'];
        ingresosConsolidado($codigoI, $codigoF, $anno,$fechaI,$fechaF, $nanno);
        echo 1;
    break;
    #* Ejecución Gerencial Gastos
    case 3:
        #**********Recepción Variables ****************#
        $nanno      = anno($anno);
        $mesf       = $_REQUEST['sltmesf'];
        $fechaI     = $nanno.'-01-01';
        $diaF       = cal_days_in_month($calendario, $mesf, $nanno); 
        $fechaF     = $nanno.'-'.$mesf.'-'.$diaF;
        $codigoI    = $_REQUEST['sltcodi'];
        $codigoF    = $_REQUEST['sltcodf'];
        gastosGerencial($codigoI, $codigoF, $anno,$fechaI,$fechaF, $nanno);
        echo 1;
    break;
    #* Ejecución Gerencial Ingresos
    case 4:
        #**********Recepción Variables ****************#
        $nanno      = anno($anno);
        $mesf       = $_REQUEST['sltmesf'];
        $fechaI     = $nanno.'-01-01';
        $diaF       = cal_days_in_month($calendario, $mesf, $nanno); 
        $fechaF     = $nanno.'-'.$mesf.'-'.$diaF;
        $codigoI    = $_REQUEST['sltcodi'];
        $codigoF    = $_REQUEST['sltcodf'];
        ingresosGerencial($codigoI, $codigoF, $anno,$fechaI,$fechaF, $nanno);
        echo 1;
    break;
    #* Ejecución Gerencial Gastos
    case 5:
        #**********Recepción Variables ****************#
        $nanno      = anno($anno);
        $mesf       = $_REQUEST['sltmesf'];
        $fechaI     = $nanno.'-01-01';
        $diaF       = cal_days_in_month($calendario, $mesf, $nanno); 
        $fechaF     = $nanno.'-'.$mesf.'-'.$diaF;
        $codigoI    = $_REQUEST['sltcodi'];
        $codigoF    = $_REQUEST['sltcodf'];
        gastosGerencialIE($codigoI, $codigoF, $anno,$fechaI,$fechaF, $nanno);
        echo 1;
    break;
    #* Ejecución Gerencial Ingresos
    case 6:
        #**********Recepción Variables ****************#
        $nanno      = anno($anno);
        $mesf       = $_REQUEST['sltmesf'];
        $fechaI     = $nanno.'-01-01';
        $diaF       = cal_days_in_month($calendario, $mesf, $nanno); 
        $fechaF     = $nanno.'-'.$mesf.'-'.$diaF;
        $codigoI    = $_REQUEST['sltcodi'];
        $codigoF    = $_REQUEST['sltcodf'];
        ingresosGerencialIE($codigoI, $codigoF, $anno,$fechaI,$fechaF, $nanno);
        echo 1;
    break;
    #* Ejecución Gerencial Gastos
    case 7:
        #**********Recepción Variables ****************#
        $nanno      = anno($anno);
        $mesf       = $_REQUEST['sltmesf'];
        $fechaI     = $nanno.'-01-01';
        $diaF       = cal_days_in_month($calendario, $mesf, $nanno); 
        $fechaF     = $nanno.'-'.$mesf.'-'.$diaF;
        $codigoI    = $_REQUEST['sltcodi'];
        $codigoF    = $_REQUEST['sltcodf'];
        gastosGerencialIE($codigoI, $codigoF, $anno,$fechaI,$fechaF, $nanno);
        echo 1;
    break;
    #* Ejecución Gerencial Ingresos
    case 8:
        #**********Recepción Variables ****************#
        $nanno      = anno($anno);
        $mesf       = $_REQUEST['sltmesf'];
        $fechaI     = $nanno.'-01-01';
        $diaF       = cal_days_in_month($calendario, $mesf, $nanno); 
        $fechaF     = $nanno.'-'.$mesf.'-'.$diaF;
        $codigoI    = $_REQUEST['sltcodi'];
        $codigoF    = $_REQUEST['sltcodf'];
        ingresosGerencialIE($codigoI, $codigoF, $anno,$fechaI,$fechaF, $nanno);
        echo 1;
    break;
}
function gastosConsolidado($codigoI, $codigoF, $anno,$fechaI,$fechaF, $nanno){
    global $con;
    $con->Listar("TRUNCATE TABLE temporal_pptal_consolidada");
    #Buscar rubros 
    $rbs = $con->Listar("SELECT DISTINCT rb.codi_presupuesto, rb.nombre, 
        rba.codi_presupuesto 
        FROM gf_rubro_pptal rb
        LEFT JOIN gf_rubro_pptal rba ON rb.predecesor= rba.id_unico 
        WHERE rb.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF'  
        AND rb.parametrizacionanno = $anno 
        AND rb.tipoclase = 7 
        ORDER BY rb.codi_presupuesto DESC");
    for ($i = 0; $i < count($rbs); $i++) {
        $rw = $con->Listar("SELECT DISTINCT CONCAT_WS(' - ', f.equivalente, f.nombre) FROM gf_detalle_comprobante_pptal dc
            LEFT JOIN gf_rubro_fuente rf ON dc.rubrofuente = rf.id_unico 
            LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
            LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
            LEFT JOIN gf_parametrizacion_anno pa ON rb.parametrizacionanno = pa.id_unico 
            WHERE pa.anno = '$nanno' and rb.codi_presupuesto = '".$rbs[$i][0]."' ");
        if(count($rw)>0){
            for ($f=0; $f <count($rw) ; $f++) { 
                $sql_cons ="INSERT INTO `temporal_pptal_consolidada` 
                        ( `cod_rubro`, `nombre_rubro`, `cod_predecesor`,`nombre_fuente`) 
                VALUES (:cod_rubro, :nombre_rubro, :cod_predecesor, :nombre_fuente )";
                $sql_dato = array(
                    array(":cod_rubro",$rbs[$i][0]),
                    array(":nombre_rubro",$rbs[$i][1]),
                    array(":cod_predecesor",$rbs[$i][2]),
                    array(":nombre_fuente",$rw[$f][0]),                    
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
        } else {
            $sql_cons ="INSERT INTO `temporal_pptal_consolidada` 
                    ( `cod_rubro`, `nombre_rubro`, `cod_predecesor`) 
            VALUES (:cod_rubro, :nombre_rubro, :cod_predecesor)";
            $sql_dato = array(
                array(":cod_rubro",$rbs[$i][0]),
                array(":nombre_rubro",$rbs[$i][1]),
                array(":cod_predecesor",$rbs[$i][2]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
    }
    $row    = $con->Listar("SELECT DISTINCT cod_rubro, nombre_rubro, nombre_fuente FROM temporal_pptal_consolidada  
        WHERE  length(cod_rubro)=6 
        ORDER BY cod_rubro DESC");
    for ($i = 0; $i < count($row); $i++) {
        if(empty($row[$i][2])){
            $rowdm  = $con->Listar("SELECT DISTINCT rf.id_unico 
            FROM gf_rubro_fuente rf 
            LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
            LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcp.rubrofuente = rf.id_unico 
            LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
            LEFT JOIN gf_parametrizacion_anno pa ON cp.parametrizacionanno = pa.id_unico 
            WHERE rb.codi_presupuesto LIKE '".$row[$i][0]."%' 
            AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND pa.anno = '$nanno'");
        } else {
            $rowdm  = $con->Listar("SELECT DISTINCT rf.id_unico 
            FROM gf_rubro_fuente rf 
            LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
            LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcp.rubrofuente = rf.id_unico 
            LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
            LEFT JOIN gf_parametrizacion_anno pa ON cp.parametrizacionanno = pa.id_unico 
            LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
            WHERE rb.codi_presupuesto LIKE '".$row[$i][0]."%' 
            AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND CONCAT_WS(' - ', f.equivalente, f.nombre) = '".$row[$i][2]."'
            AND pa.anno = '$nanno'");
        }
        $pptoInicial                = 0;
        $adicion                    = 0;
        $reduccion                  = 0;
        $trasCredito                = 0;
        $trasCont                   = 0;
        $presupuestoDefinitivo      = 0;
        $disponibilidad             = 0;
        $saldo_disponible           = 0;
        $registros                  = 0;
        $disponibilidadesAbiertas   = 0;
        $registrosAbiertos          = 0;
        $total_pagos                = 0;
        $reservas                   = 0;
        $cuentasxpagar              = 0;
        $tras                       = 0;  
        $totalObligaciones          = 0;
        for ($d = 0; $d < count($rowdm); $d++) {
            #PRESUPUESTO INICIAL
            $pptoInicial	= $pptoInicial + presupuestos($rowdm[$d][0], 1, $fechaI, $fechaF);
            #ADICION
            $adicion 		= $adicion + presupuestos($rowdm[$d][0], 2, $fechaI, $fechaF);
            #REDUCCION
            $reduccion 		= $reduccion + presupuestos($rowdm[$d][0], 3, $fechaI, $fechaF);
            #TRAS.CRED Y CONT.
            $tras 		= presupuestos($rowdm[$d][0], 4, $fechaI, $fechaF);
                if($tras>0){
                    $trasCredito = $trasCredito + $tras;
                    $trasCont 	 = $trasCont + 0;
                }else {
                    $trasCredito = $trasCredito + 0;
                    $trasCont 	 = $trasCont + $tras;
                }

            
            #DISPONIBILIDAD
            $disponibilidad 		= $disponibilidad + disponibilidad_Informe($rowdm[$d][0], 14, $fechaI, $fechaF);
            #REGISTROS
            $registros                  = $registros + disponibilidad_Informe($rowdm[$d][0], 15, $fechaI, $fechaF);
            #TOTAL OBLIGACIONES
            $totalObligaciones 		= $totalObligaciones + disponibilidad_Informe($rowdm[$d][0], 16, $fechaI, $fechaF);
            #TOTAL PAGOS
            $total_pagos 		= $total_pagos + disponibilidad_Informe($rowdm[$d][0], 17, $fechaI, $fechaF);
            
        }
        #PRESUPUESTO DEFINITIVO
        $presupuestoDefinitivo 	= $pptoInicial + $adicion - $reduccion + $trasCredito + $trasCont;
        #SALDO DISPONIBLE
        $saldo_disponible 	= $presupuestoDefinitivo - $disponibilidad;
        #DISPONIBILIDADES ABIERTOS
        $disponibilidadesAbiertas = $disponibilidad - $registros;
        #REGISTROS ABIERTOS
        $registrosAbiertos 	= $registros - $totalObligaciones;
        #RESERVAS
        $reservas 		= $registros - $totalObligaciones;
        #CUENTAS POR PAGAR
        $cuentasxpagar 		= $totalObligaciones - $total_pagos;
        #ACTUALIZAR TABLA CON DATOS HALLADOS
        if(empty($row[$i][2])){
            $sql_cons ="UPDATE `temporal_pptal_consolidada`  
            SET `ptto_inicial`=:ptto_inicial , 
            `adicion`=:adicion , 
            `reduccion`=:reduccion , 
            `tras_credito`=:tras_credito , 
            `tras_cont`=:tras_cont , 
            `presupuesto_dfvo`=:presupuesto_dfvo , 
            `disponibilidades`=:disponibilidades , 
            `saldo_disponible`=:saldo_disponible , 
            `disponibilidad_abierta`=:disponibilidad_abierta , 
            `registros`=:registros , 
            `registros_abiertos`=:registros_abiertos , 
            `total_obligaciones`=:total_obligaciones , 
            `total_pagos`=:total_pagos , 
            `reservas`=:reservas , 
            `cuentas_x_pagar`=:cuentas_x_pagar 
            WHERE `cod_rubro`=:cod_rubro";
            $sql_dato = array(
                array(":ptto_inicial",$pptoInicial),
                array(":adicion",$adicion),
                array(":reduccion",$reduccion),
                array(":tras_credito",$trasCredito),
                array(":tras_cont",$trasCont),
                array(":presupuesto_dfvo",$presupuestoDefinitivo),
                array(":disponibilidades",$disponibilidad),
                array(":saldo_disponible",$saldo_disponible),
                array(":disponibilidad_abierta",$disponibilidadesAbiertas),
                array(":registros",$registros),
                array(":registros_abiertos",$registrosAbiertos),
                array(":total_obligaciones",$totalObligaciones),
                array(":total_pagos",$total_pagos),
                array(":reservas",$reservas),
                array(":cuentas_x_pagar",$cuentasxpagar),
                array(":cod_rubro",$row[$i][0]),   
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        } else {
            $sql_cons ="UPDATE `temporal_pptal_consolidada`  
            SET `ptto_inicial`=:ptto_inicial , 
            `adicion`=:adicion , 
            `reduccion`=:reduccion , 
            `tras_credito`=:tras_credito , 
            `tras_cont`=:tras_cont , 
            `presupuesto_dfvo`=:presupuesto_dfvo , 
            `disponibilidades`=:disponibilidades , 
            `saldo_disponible`=:saldo_disponible , 
            `disponibilidad_abierta`=:disponibilidad_abierta , 
            `registros`=:registros , 
            `registros_abiertos`=:registros_abiertos , 
            `total_obligaciones`=:total_obligaciones , 
            `total_pagos`=:total_pagos , 
            `reservas`=:reservas , 
            `cuentas_x_pagar`=:cuentas_x_pagar 
            WHERE `cod_rubro`=:cod_rubro and `nombre_fuente`=:nombre_fuente";
            $sql_dato = array(
                array(":ptto_inicial",$pptoInicial),
                array(":adicion",$adicion),
                array(":reduccion",$reduccion),
                array(":tras_credito",$trasCredito),
                array(":tras_cont",$trasCont),
                array(":presupuesto_dfvo",$presupuestoDefinitivo),
                array(":disponibilidades",$disponibilidad),
                array(":saldo_disponible",$saldo_disponible),
                array(":disponibilidad_abierta",$disponibilidadesAbiertas),
                array(":registros",$registros),
                array(":registros_abiertos",$registrosAbiertos),
                array(":total_obligaciones",$totalObligaciones),
                array(":total_pagos",$total_pagos),
                array(":reservas",$reservas),
                array(":cuentas_x_pagar",$cuentasxpagar),
                array(":cod_rubro",$row[$i][0]),   
                array(":nombre_fuente",$row[$i][2]),   
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        }
         
    }
    #CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
    $rowt = $con->Listar("SELECT DISTINCT cod_rubro 
        FROM temporal_pptal_consolidada  
        ORDER BY cod_rubro DESC ");
    for ($i = 0; $i < count($rowt); $i++) {
        $rowa = $con->Listar("SELECT cod_rubro,
        cod_rubro,
        cod_predecesor, 
        ptto_inicial, adicion, tras_credito, tras_cont, 
        presupuesto_dfvo, disponibilidades, 
        saldo_disponible,registros, 
        registros_abiertos,total_obligaciones, 
        total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta 
        FROM temporal_pptal_consolidada WHERE cod_rubro ='".$rowt[$i][0]."' 
        ORDER BY cod_rubro DESC ");

        for ($a = 0; $a < count($rowa); $a++) {
            if(!empty($rowa[$a][2])){
                $va   = $con->Listar("SELECT cod_rubro,cod_rubro,
                    cod_predecesor, 
                    ptto_inicial, adicion, tras_credito, tras_cont, 
                    presupuesto_dfvo, disponibilidades, 
                    saldo_disponible,registros, 
                    registros_abiertos,total_obligaciones, 
                    total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta 
                    FROM temporal_pptal_consolidada WHERE cod_rubro ='".$rowa[$a][2]."'");   
                $pptoInicialM       = $rowa[$a][3]+$va[0][3];
                $adicionM           = $rowa[$a][4]+$va[0][4];
                $trasCreditoM       = $rowa[$a][5]+$va[0][5];
                $trasContM          = $rowa[$a][6]+$va[0][6];
                $presupuestoDefinitivoM = $rowa[$a][7]+$va[0][7];
                $disponibilidadM    = $rowa[$a][8]+$va[0][8];
                $saldoDisponibleM   = $rowa[$a][9]+$va[0][9];
                $registrosM         = $rowa[$a][10]+$va[0][10];
                $registrosAbiertosM = $rowa[$a][11]+$va[0][11];
                $totalObligacionesM = $rowa[$a][12]+$va[0][12];
                $totalPagosM        = $rowa[$a][13]+$va[0][13];
                $reservasM          = $rowa[$a][14]+$va[0][14];
                $cuentasxpagarM     = $rowa[$a][15]+$va[0][15];
                $reduccionM         = $rowa[$a][16]+$va[0][16];
                $disponibilidadAbiertaM = $rowa[$a][17]+$va[0][17];
                #ACTUALIZAR TABLA CON DATOS HALLADOS
                $sql_cons ="UPDATE `temporal_pptal_consolidada`  
                SET `ptto_inicial`=:ptto_inicial , 
                `adicion`=:adicion , 
                `reduccion`=:reduccion , 
                `tras_credito`=:tras_credito , 
                `tras_cont`=:tras_cont , 
                `presupuesto_dfvo`=:presupuesto_dfvo , 
                `disponibilidades`=:disponibilidades , 
                `saldo_disponible`=:saldo_disponible , 
                `disponibilidad_abierta`=:disponibilidad_abierta , 
                `registros`=:registros , 
                `registros_abiertos`=:registros_abiertos , 
                `total_obligaciones`=:total_obligaciones , 
                `total_pagos`=:total_pagos , 
                `reservas`=:reservas , 
                `cuentas_x_pagar`=:cuentas_x_pagar 
                WHERE `cod_rubro`=:cod_rubro";
                $sql_dato = array(
                    array(":ptto_inicial",$pptoInicialM),
                    array(":adicion",$adicionM),
                    array(":reduccion",$reduccionM),
                    array(":tras_credito",$trasCreditoM),
                    array(":tras_cont",$trasContM),
                    array(":presupuesto_dfvo",$presupuestoDefinitivoM),
                    array(":disponibilidades",$disponibilidadM),
                    array(":saldo_disponible",$saldoDisponibleM),
                    array(":disponibilidad_abierta",$disponibilidadAbiertaM),
                    array(":registros",$registrosM),
                    array(":registros_abiertos",$registrosAbiertosM),
                    array(":total_obligaciones",$totalObligacionesM),
                    array(":total_pagos",$totalPagosM),
                    array(":reservas",$reservasM),
                    array(":cuentas_x_pagar",$cuentasxpagarM),
                    array(":cod_rubro",$rowa[$a][2]),   
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato); 
                //var_dump($rowa[$a][2],$sql_cons,$sql_dato);
            }
        }
    }

    return true;
}
function ingresosConsolidado($codigoI, $codigoF, $anno,$fechaI,$fechaF, $nanno){
    global $con;
    $con->Listar("TRUNCATE TABLE temporal_pptal_consolidada");
    #Buscar rubros 
    $rbs = $con->Listar("SELECT DISTINCT rb.codi_presupuesto, rb.nombre, 
        rba.codi_presupuesto 
        FROM gf_rubro_pptal rb
        LEFT JOIN gf_rubro_pptal rba ON rb.predecesor= rba.id_unico 
        WHERE rb.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF'  
        AND rb.parametrizacionanno = $anno 
        AND rb.tipoclase = 6 
        ORDER BY rb.codi_presupuesto DESC");
    for ($i = 0; $i < count($rbs); $i++) {
        $rw = $con->Listar("SELECT DISTINCT CONCAT_WS(' - ', f.equivalente, f.nombre) FROM gf_detalle_comprobante_pptal dc
            LEFT JOIN gf_rubro_fuente rf ON dc.rubrofuente = rf.id_unico 
            LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
            LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
            LEFT JOIN gf_parametrizacion_anno pa ON rb.parametrizacionanno = pa.id_unico 
            WHERE pa.anno = '$nanno' and rb.codi_presupuesto = '".$rbs[$i][0]."' ");
        if(count($rw)>0){
            for ($f=0; $f <count($rw) ; $f++) { 
                $sql_cons ="INSERT INTO `temporal_pptal_consolidada` 
                        ( `cod_rubro`, `nombre_rubro`, `cod_predecesor`, `nombre_fuente`) 
                VALUES (:cod_rubro, :nombre_rubro, :cod_predecesor, :nombre_fuente )";
                $sql_dato = array(
                    array(":cod_rubro",$rbs[$i][0]),
                    array(":nombre_rubro",$rbs[$i][1]),
                    array(":cod_predecesor",$rbs[$i][2]),
                    array(":nombre_fuente",$rw[$f][0]),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
        } else {
            $sql_cons ="INSERT INTO `temporal_pptal_consolidada` 
                    ( `cod_rubro`, `nombre_rubro`, `cod_predecesor`) 
            VALUES (:cod_rubro, :nombre_rubro, :cod_predecesor)";
            $sql_dato = array(
                array(":cod_rubro",$rbs[$i][0]),
                array(":nombre_rubro",$rbs[$i][1]),
                array(":cod_predecesor",$rbs[$i][2]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
    }
    $row    = $con->Listar("SELECT DISTINCT cod_rubro, nombre_rubro, nombre_fuente FROM temporal_pptal_consolidada  
        WHERE  length(cod_rubro)=6 
        ORDER BY cod_rubro DESC");
    for ($i = 0; $i < count($row); $i++) {
        if(empty($row[$i][2])){
            $rowdm  = $con->Listar("SELECT DISTINCT rf.id_unico 
            FROM gf_rubro_fuente rf 
            LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
            LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcp.rubrofuente = rf.id_unico 
            LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
            LEFT JOIN gf_parametrizacion_anno pa ON cp.parametrizacionanno = pa.id_unico  
            WHERE rb.codi_presupuesto LIKE '".$row[$i][0]."%' 
            AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND pa.anno = '$nanno'");
        } else {
            $rowdm  = $con->Listar("SELECT DISTINCT rf.id_unico 
            FROM gf_rubro_fuente rf 
            LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
            LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcp.rubrofuente = rf.id_unico 
            LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
            LEFT JOIN gf_parametrizacion_anno pa ON cp.parametrizacionanno = pa.id_unico  
            LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
            WHERE rb.codi_presupuesto LIKE '".$row[$i][0]."%' 
            AND CONCAT_WS(' - ', f.equivalente, f.nombre) = '".$row[$i][2]."'
            AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND pa.anno = '$nanno'");
        }
        
        $pptoInicial                = 0;
        $adicion                    = 0;
        $reduccion                  = 0;
        $recaudos                   = 0;
        $presupuestoDefinitivo      = 0;
        $saldos                     = 0;
        for ($d = 0; $d < count($rowdm); $d++) {
            #PRESUPUESTO INICIAL
            $pptoInicial	= $pptoInicial + presupuestos($rowdm[$d][0], 1, $fechaI, $fechaF);
            #ADICION
            $adicion 		= $adicion + presupuestos($rowdm[$d][0], 2, $fechaI, $fechaF);
            #REDUCCION
            $reduccion 		= $reduccion + presupuestos($rowdm[$d][0], 3, $fechaI, $fechaF);
            #RECAUDOS
            $recaudos       = $recaudos + disponibilidad_Informe($rowdm[$d][0], 18, $fechaI, $fechaF);
    
            
        }
        #PRESUPUESTO DEFINITIVO
        $presupuestoDefinitivo 	= $pptoInicial + $adicion - $reduccion ;
        #SALDOS POR RECAUDAR
        $saldos                 = $presupuestoDefinitivo - $recaudos;
        
        #ACTUALIZAR TABLA CON DATOS HALLADOS
        if(empty($row[$i][2])){
            $sql_cons ="UPDATE `temporal_pptal_consolidada`  
            SET `ptto_inicial`=:ptto_inicial , 
            `adicion`=:adicion , 
            `reduccion`=:reduccion , 
            `presupuesto_dfvo`=:presupuesto_dfvo , 
            `recaudos`=:recaudos , 
            `saldos_x_recaudar`=:saldos_x_recaudar 
            WHERE `cod_rubro`=:cod_rubro";
            $sql_dato = array(
                array(":ptto_inicial",$pptoInicial),
                array(":adicion",$adicion),
                array(":reduccion",$reduccion),
                array(":presupuesto_dfvo",$presupuestoDefinitivo),
                array(":recaudos",$recaudos),
                array(":saldos_x_recaudar",$saldos),
                array(":cod_rubro",$row[$i][0]),   
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        } else {
            $sql_cons ="UPDATE `temporal_pptal_consolidada`  
            SET `ptto_inicial`=:ptto_inicial , 
            `adicion`=:adicion , 
            `reduccion`=:reduccion , 
            `presupuesto_dfvo`=:presupuesto_dfvo , 
            `recaudos`=:recaudos , 
            `saldos_x_recaudar`=:saldos_x_recaudar 
            WHERE `cod_rubro`=:cod_rubro and `nombre_fuente`=:nombre_fuente";
            $sql_dato = array(
                array(":ptto_inicial",$pptoInicial),
                array(":adicion",$adicion),
                array(":reduccion",$reduccion),
                array(":presupuesto_dfvo",$presupuestoDefinitivo),
                array(":recaudos",$recaudos),
                array(":saldos_x_recaudar",$saldos),
                array(":cod_rubro",$row[$i][0]),   
                array(":nombre_fuente",$row[$i][2]),   
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        }
        
         
    }
    #CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
    $rowt = $con->Listar("SELECT DISTINCT cod_rubro 
        FROM temporal_pptal_consolidada  
        ORDER BY cod_rubro DESC ");
    for ($i = 0; $i < count($rowt); $i++) {
        $rowa = $con->Listar("SELECT cod_rubro, 
        cod_predecesor, 
        ptto_inicial, adicion, reduccion, 
        presupuesto_dfvo, recaudos, 
        saldos_x_recaudar  
        FROM temporal_pptal_consolidada WHERE cod_rubro ='".$rowt[$i][0]."' 
        ORDER BY cod_rubro DESC ");
        for ($a = 0; $a < count($rowa); $a++) {
            if(!empty($rowa[$a][2])){
            $va   = $con->Listar("SELECT cod_rubro,
                cod_predecesor, 
                ptto_inicial, adicion,reduccion, 
                presupuesto_dfvo, recaudos, 
                saldos_x_recaudar  
                FROM temporal_pptal_consolidada WHERE cod_rubro ='".$rowa[$a][1]."'");   
            $pptoInicialM       = $rowa[$a][2]+$va[0][2];
            $adicionM           = $rowa[$a][3]+$va[0][3];
            $reduccionM         = $rowa[$a][4]+$va[0][4];
            $presupuestoDefinitivoM = $rowa[$a][5]+$va[0][5];
            $recaudosM          = $rowa[$a][6]+$va[0][6];
            $saldos_x_recaudarM = $rowa[$a][7]+$va[0][7];
            #ACTUALIZAR TABLA CON DATOS HALLADOS
            $sql_cons ="UPDATE `temporal_pptal_consolidada`  
            SET `ptto_inicial`=:ptto_inicial , 
            `adicion`=:adicion , 
            `reduccion`=:reduccion , 
            `presupuesto_dfvo`=:presupuesto_dfvo , 
            `recaudos`=:recaudos , 
            `saldos_x_recaudar`=:saldos_x_recaudar 
            WHERE `cod_rubro`=:cod_rubro";
            $sql_dato = array(
                array(":ptto_inicial",$pptoInicialM),
                array(":adicion",$adicionM),
                array(":reduccion",$reduccionM),
                array(":presupuesto_dfvo",$presupuestoDefinitivoM),
                array(":recaudos",$recaudosM),
                array(":saldos_x_recaudar",$saldos_x_recaudarM),
                array(":cod_rubro",$rowa[$a][1]),   
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato); 
            //var_dump($rowa[$a][2],$sql_cons,$sql_dato);
        }
    }
}

    return true;
}
function gastosGerencial($codigoI, $codigoF, $anno,$fechaI,$fechaF, $nanno){
    global $con;
    $con->Listar("TRUNCATE TABLE temporal_pptal_consolidada");
    #** Buscar Tipos De Fuentes 
    $rowtf = $con->Listar("SELECT id_unico FROM gf_tipo_fuente ");
    for ($f = 0; $f < count($rowtf); $f++) {
        #Buscar rubros 
        $rbs = $con->Listar("SELECT DISTINCT rb.codi_presupuesto, rb.nombre, 
            rba.codi_presupuesto 
            FROM gf_rubro_pptal rb
            LEFT JOIN gf_rubro_pptal rba ON rb.predecesor= rba.id_unico 
            WHERE rb.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF'  
            AND rb.parametrizacionanno = $anno 
            AND rb.tipoclase = 7 
            ORDER BY rb.codi_presupuesto DESC");
        for ($i = 0; $i < count($rbs); $i++) {
            $sql_cons ="INSERT INTO `temporal_pptal_consolidada` 
                    ( `cod_rubro`, `nombre_rubro`, `cod_predecesor`,`tipo_fuente`) 
            VALUES (:cod_rubro, :nombre_rubro, :cod_predecesor, :tipo_fuente)";
            $sql_dato = array(
                array(":cod_rubro",$rbs[$i][0]),
                array(":nombre_rubro",$rbs[$i][1]),
                array(":cod_predecesor",$rbs[$i][2]),
                array(":tipo_fuente",$rowtf[$f][0]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
    }
        
    $row    = $con->Listar("SELECT DISTINCT cod_rubro, nombre_rubro, tipo_fuente 
        FROM temporal_pptal_consolidada 
        WHERE cod_rubro BETWEEN '$codigoI' AND '$codigoF'  
        AND length(cod_rubro)=6 
        ORDER BY cod_rubro DESC");
    for ($i = 0; $i < count($row); $i++) {
        $rowdm  = $con->Listar("SELECT DISTINCT rf.id_unico 
        FROM gf_rubro_fuente rf 
        LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
        LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcp.rubrofuente = rf.id_unico 
        LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
        LEFT JOIN gf_parametrizacion_anno pa ON cp.parametrizacionanno = pa.id_unico 
        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
        WHERE rb.codi_presupuesto LIKE '".$row[$i][0]."%' 
        AND f.tipofuente = '".$row[$i][2]."' 
        AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
        AND pa.anno = '$nanno'");
        $pptoInicial                = 0;
        $adicion                    = 0;
        $reduccion                  = 0;
        $trasCredito                = 0;
        $trasCont                   = 0;
        $presupuestoDefinitivo      = 0;
        $registros                  = 0;
        $tras                       = 0;  
        for ($d = 0; $d < count($rowdm); $d++) {
            #PRESUPUESTO INICIAL
            $pptoInicial	= $pptoInicial + presupuestos($rowdm[$d][0], 1, $fechaI, $fechaF);
            #ADICION
            $adicion 		= $adicion + presupuestos($rowdm[$d][0], 2, $fechaI, $fechaF);
            #REDUCCION
            $reduccion 		= $reduccion + presupuestos($rowdm[$d][0], 3, $fechaI, $fechaF);
            #TRAS.CRED Y CONT.
            $tras 		= presupuestos($rowdm[$d][0], 4, $fechaI, $fechaF);
                if($tras>0){
                    $trasCredito = $trasCredito + $tras;
                    $trasCont 	 = $trasCont + 0;
                }else {
                    $trasCredito = $trasCredito + 0;
                    $trasCont 	 = $trasCont + $tras;
                }
            #REGISTROS
            $registros           = $registros + disponibilidad_Informe($rowdm[$d][0], 15, $fechaI, $fechaF);
        }
        #PRESUPUESTO DEFINITIVO
        $presupuestoDefinitivo 	= $pptoInicial + $adicion - $reduccion + $trasCredito + $trasCont;
        
        $sql_cons ="UPDATE `temporal_pptal_consolidada`  
        SET `presupuesto_dfvo`=:presupuesto_dfvo , 
        `registros`=:registros  
        WHERE `cod_rubro`=:cod_rubro AND `tipo_fuente`=:tipo_fuente";
        $sql_dato = array(
            array(":presupuesto_dfvo",$presupuestoDefinitivo),
            array(":registros",$registros),
            array(":cod_rubro",$row[$i][0]),   
            array(":tipo_fuente",$row[$i][2]),   
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
    }
    

    return true;
}
function ingresosGerencial($codigoI, $codigoF, $anno,$fechaI,$fechaF, $nanno){
    global $con;
    $con->Listar("TRUNCATE TABLE temporal_pptal_consolidada");
    #** Buscar Tipos De Fuentes 
    $rowtf = $con->Listar("SELECT id_unico FROM gf_tipo_fuente ");
    for ($f = 0; $f < count($rowtf); $f++) {
        #Buscar rubros 
        $rbs = $con->Listar("SELECT DISTINCT rb.codi_presupuesto, rb.nombre, 
            rba.codi_presupuesto 
            FROM gf_rubro_pptal rb
            LEFT JOIN gf_rubro_pptal rba ON rb.predecesor= rba.id_unico 
            WHERE rb.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF'  
            AND rb.parametrizacionanno = $anno 
            AND rb.tipoclase = 6 
            ORDER BY rb.codi_presupuesto DESC");
        for ($i = 0; $i < count($rbs); $i++) {
            $sql_cons ="INSERT INTO `temporal_pptal_consolidada` 
                    ( `cod_rubro`, `nombre_rubro`, `cod_predecesor`,`tipo_fuente`) 
            VALUES (:cod_rubro, :nombre_rubro, :cod_predecesor, :tipo_fuente)";
            $sql_dato = array(
                array(":cod_rubro",$rbs[$i][0]),
                array(":nombre_rubro",$rbs[$i][1]),
                array(":cod_predecesor",$rbs[$i][2]),
                array(":tipo_fuente",$rowtf[$f][0]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
    }
    $row    = $con->Listar("SELECT DISTINCT cod_rubro, nombre_rubro, tipo_fuente 
        FROM temporal_pptal_consolidada 
        WHERE cod_rubro BETWEEN '$codigoI' AND '$codigoF'  
        AND length(cod_rubro)=6 
        ORDER BY cod_rubro DESC");
    for ($i = 0; $i < count($row); $i++) {
        $rowdm  = $con->Listar("SELECT DISTINCT rf.id_unico 
        FROM gf_rubro_fuente rf 
        LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
        LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcp.rubrofuente = rf.id_unico 
        LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
        LEFT JOIN gf_parametrizacion_anno pa ON cp.parametrizacionanno = pa.id_unico 
        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
        WHERE rb.codi_presupuesto LIKE '".$row[$i][0]."%' 
        AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
         AND f.tipofuente = '".$row[$i][2]."' 
        AND pa.anno = '$nanno'");
        $pptoInicial                = 0;
        $adicion                    = 0;
        $reduccion                  = 0;
        $recaudos                   = 0;
        $presupuestoDefinitivo      = 0;
        $saldos                     = 0;
        for ($d = 0; $d < count($rowdm); $d++) {
            #PRESUPUESTO INICIAL
            $pptoInicial	= $pptoInicial + presupuestos($rowdm[$d][0], 1, $fechaI, $fechaF);
            #ADICION
            $adicion 		= $adicion + presupuestos($rowdm[$d][0], 2, $fechaI, $fechaF);
            #REDUCCION
            $reduccion 		= $reduccion + presupuestos($rowdm[$d][0], 3, $fechaI, $fechaF);
            #RECAUDOS
            $recaudos           = $recaudos + disponibilidad_Informe($rowdm[$d][0], 18, $fechaI, $fechaF);
        }
        #PRESUPUESTO DEFINITIVO
        $presupuestoDefinitivo 	= $pptoInicial + $adicion - $reduccion ;
        #ACTUALIZAR TABLA CON DATOS HALLADOS
        $sql_cons ="UPDATE `temporal_pptal_consolidada`  
        SET `presupuesto_dfvo`=:presupuesto_dfvo , 
        `recaudos`=:recaudos 
        WHERE `cod_rubro`=:cod_rubro AND `tipo_fuente`=:tipo_fuente";
        $sql_dato = array(
            array(":presupuesto_dfvo",$presupuestoDefinitivo),
            array(":recaudos",$recaudos),
            array(":cod_rubro",$row[$i][0]),   
            array(":tipo_fuente",$row[$i][2]),   
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
         
    }
    return true;
}
function gastosGerencialIE($codigoI, $codigoF, $anno,$fechaI,$fechaF, $nanno){
    global $con;
    $con->Listar("TRUNCATE TABLE temporal_pptal_consolidada");
    #** Buscar Tipos De Fuentes 
    $rowtf = $con->Listar("SELECT DISTINCT t.id_unico 
        FROM gf_parametrizacion_anno pa 
        LEFT JOIN gf_tercero t ON pa.compania = t.id_unico 
        LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero 
        WHERE pt.perfil = 1  ");
    for ($f = 0; $f < count($rowtf); $f++) {
        #Buscar rubros 
        $rbs = $con->Listar("SELECT DISTINCT rb.codi_presupuesto, rb.nombre, 
            rba.codi_presupuesto 
            FROM gf_rubro_pptal rb
            LEFT JOIN gf_rubro_pptal rba ON rb.predecesor= rba.id_unico 
            WHERE rb.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF'  
            AND rb.parametrizacionanno = $anno 
            AND rb.tipoclase = 7 
            ORDER BY rb.codi_presupuesto DESC");
        for ($i = 0; $i < count($rbs); $i++) {
            $sql_cons ="INSERT INTO `temporal_pptal_consolidada` 
                    ( `cod_rubro`, `nombre_rubro`, `cod_predecesor`,`tipo_fuente`) 
            VALUES (:cod_rubro, :nombre_rubro, :cod_predecesor, :tipo_fuente)";
            $sql_dato = array(
                array(":cod_rubro",$rbs[$i][0]),
                array(":nombre_rubro",$rbs[$i][1]),
                array(":cod_predecesor",$rbs[$i][2]),
                array(":tipo_fuente",$rowtf[$f][0]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
    }
        
    $row    = $con->Listar("SELECT DISTINCT cod_rubro, nombre_rubro, tipo_fuente 
        FROM temporal_pptal_consolidada 
        WHERE cod_rubro BETWEEN '$codigoI' AND '$codigoF'  
        AND length(cod_rubro)=6 
        ORDER BY cod_rubro DESC");
    for ($i = 0; $i < count($row); $i++) {
        $rowdm  = $con->Listar("SELECT DISTINCT rf.id_unico 
        FROM gf_rubro_fuente rf 
        LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
        LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcp.rubrofuente = rf.id_unico 
        LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
        LEFT JOIN gf_parametrizacion_anno pa ON cp.parametrizacionanno = pa.id_unico 
        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
        WHERE rb.codi_presupuesto LIKE '".$row[$i][0]."%' 
        AND pa.compania = '".$row[$i][2]."' 
        AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
        AND pa.anno = '$nanno'");
        $pptoInicial                = 0;
        $adicion                    = 0;
        $reduccion                  = 0;
        $trasCredito                = 0;
        $trasCont                   = 0;
        $presupuestoDefinitivo      = 0;
        $registros                  = 0;
        $tras                       = 0;  
        for ($d = 0; $d < count($rowdm); $d++) {
            #PRESUPUESTO INICIAL
            $pptoInicial	= $pptoInicial + presupuestos($rowdm[$d][0], 1, $fechaI, $fechaF);
            #ADICION
            $adicion 		= $adicion + presupuestos($rowdm[$d][0], 2, $fechaI, $fechaF);
            #REDUCCION
            $reduccion 		= $reduccion + presupuestos($rowdm[$d][0], 3, $fechaI, $fechaF);
            #TRAS.CRED Y CONT.
            $tras 		= presupuestos($rowdm[$d][0], 4, $fechaI, $fechaF);
                if($tras>0){
                    $trasCredito = $trasCredito + $tras;
                    $trasCont 	 = $trasCont + 0;
                }else {
                    $trasCredito = $trasCredito + 0;
                    $trasCont 	 = $trasCont + $tras;
                }
            #REGISTROS
            $registros           = $registros + disponibilidad_Informe($rowdm[$d][0], 15, $fechaI, $fechaF);
        }
        #PRESUPUESTO DEFINITIVO
        $presupuestoDefinitivo 	= $pptoInicial + $adicion - $reduccion + $trasCredito + $trasCont;
        
        $sql_cons ="UPDATE `temporal_pptal_consolidada`  
        SET `presupuesto_dfvo`=:presupuesto_dfvo , 
        `registros`=:registros  
        WHERE `cod_rubro`=:cod_rubro AND `tipo_fuente`=:tipo_fuente";
        $sql_dato = array(
            array(":presupuesto_dfvo",$presupuestoDefinitivo),
            array(":registros",$registros),
            array(":cod_rubro",$row[$i][0]),   
            array(":tipo_fuente",$row[$i][2]),   
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
    }
    

    return true;
}
function ingresosGerencialIE($codigoI, $codigoF, $anno,$fechaI,$fechaF, $nanno){
    global $con;
    $con->Listar("TRUNCATE TABLE temporal_pptal_consolidada");
    #** Buscar Tipos De Fuentes 
    $rowtf = $con->Listar("SELECT DISTINCT t.id_unico 
        FROM gf_parametrizacion_anno pa 
        LEFT JOIN gf_tercero t ON pa.compania = t.id_unico 
        LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero 
        WHERE pt.perfil = 1  ");
    for ($f = 0; $f < count($rowtf); $f++) {
        #Buscar rubros 
        $rbs = $con->Listar("SELECT DISTINCT rb.codi_presupuesto, rb.nombre, 
            rba.codi_presupuesto 
            FROM gf_rubro_pptal rb
            LEFT JOIN gf_rubro_pptal rba ON rb.predecesor= rba.id_unico 
            WHERE rb.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF'  
            AND rb.parametrizacionanno = $anno 
            AND rb.tipoclase = 6 
            ORDER BY rb.codi_presupuesto DESC");
        for ($i = 0; $i < count($rbs); $i++) {
            $sql_cons ="INSERT INTO `temporal_pptal_consolidada` 
                    ( `cod_rubro`, `nombre_rubro`, `cod_predecesor`,`tipo_fuente`) 
            VALUES (:cod_rubro, :nombre_rubro, :cod_predecesor, :tipo_fuente)";
            $sql_dato = array(
                array(":cod_rubro",$rbs[$i][0]),
                array(":nombre_rubro",$rbs[$i][1]),
                array(":cod_predecesor",$rbs[$i][2]),
                array(":tipo_fuente",$rowtf[$f][0]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
    }
    $row    = $con->Listar("SELECT DISTINCT cod_rubro, nombre_rubro, tipo_fuente 
        FROM temporal_pptal_consolidada 
        WHERE cod_rubro BETWEEN '$codigoI' AND '$codigoF'  
        AND length(cod_rubro)=6 
        ORDER BY cod_rubro DESC");
    for ($i = 0; $i < count($row); $i++) {
        $rowdm  = $con->Listar("SELECT DISTINCT rf.id_unico 
        FROM gf_rubro_fuente rf 
        LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
        LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcp.rubrofuente = rf.id_unico 
        LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
        LEFT JOIN gf_parametrizacion_anno pa ON cp.parametrizacionanno = pa.id_unico 
        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
        WHERE rb.codi_presupuesto LIKE '".$row[$i][0]."%' 
        AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
         AND pa.compania  = '".$row[$i][2]."' 
        AND pa.anno = '$nanno'");
        $pptoInicial                = 0;
        $adicion                    = 0;
        $reduccion                  = 0;
        $recaudos                   = 0;
        $presupuestoDefinitivo      = 0;
        $saldos                     = 0;
        for ($d = 0; $d < count($rowdm); $d++) {
            #PRESUPUESTO INICIAL
            $pptoInicial	= $pptoInicial + presupuestos($rowdm[$d][0], 1, $fechaI, $fechaF);
            #ADICION
            $adicion 		= $adicion + presupuestos($rowdm[$d][0], 2, $fechaI, $fechaF);
            #REDUCCION
            $reduccion 		= $reduccion + presupuestos($rowdm[$d][0], 3, $fechaI, $fechaF);
            #RECAUDOS
            $recaudos           = $recaudos + disponibilidad_Informe($rowdm[$d][0], 18, $fechaI, $fechaF);
        }
        #PRESUPUESTO DEFINITIVO
        $presupuestoDefinitivo 	= $pptoInicial + $adicion - $reduccion ;
        #ACTUALIZAR TABLA CON DATOS HALLADOS
        $sql_cons ="UPDATE `temporal_pptal_consolidada`  
        SET `presupuesto_dfvo`=:presupuesto_dfvo , 
        `recaudos`=:recaudos 
        WHERE `cod_rubro`=:cod_rubro AND `tipo_fuente`=:tipo_fuente";
        $sql_dato = array(
            array(":presupuesto_dfvo",$presupuestoDefinitivo),
            array(":recaudos",$recaudos),
            array(":cod_rubro",$row[$i][0]),   
            array(":tipo_fuente",$row[$i][2]),   
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
         
    }
    return true;
}
function presupuestos($id_rubF, $tipoO, $fechaI, $fechaF)
{
        require'../Conexion/conexion.php';
	$presu = 0;
	$query = "SELECT valor as value 
                    FROM
                      gf_detalle_comprobante_pptal dc
                    LEFT JOIN
                      gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                    WHERE
                      dc.rubrofuente = '$id_rubF' 
                      AND tcp.tipooperacion = '$tipoO' 
                      AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
                      AND (tcp.clasepptal = '13')";
	$ap = $mysqli->query($query);
        if(mysqli_num_rows($ap)>0){
            $sum=0;
            while ($sum1= mysqli_fetch_array($ap)) {
                $sum = $sum1['value']+$sum;
            }
        } else {
           $sum=0; 
        }
        $presu=$sum;
        
    return $presu;
}
function disponibilidad_Informe($id_rubFue, $clase, $fechaI, $fechaF){
    require'../Conexion/conexion.php';
	
	 $apropiacion_def = 0;
	 $queryApro = "SELECT   detComP.valor, 
                    tipComP.tipooperacion, 
                    tipComP.nombre, rubFue.id_unico, 
                    rubFue.rubro, rubP.id_unico,  
                    rubP.nombre  
                    from gf_detalle_comprobante_pptal detComP 
                    left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal 
                    left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante 
                    left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente 
                    left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro 
                    where tipComP.clasepptal = '$clase' 
                    and rubFue.id_unico =  $id_rubFue AND comP.fecha BETWEEN '$fechaI' AND '$fechaF'";
        
	$apropia = $mysqli->query($queryApro);
	while($row = mysqli_fetch_row($apropia))
	{
		if(($row[1] == 2) || ($row[1] == 4) || ($row[1] == 1))
		{
			$apropiacion_def += $row[0];
		}
		elseif($row[1] == 3)
		{
                   
			$apropiacion_def -= $row[0];
		}
	}
	return $apropiacion_def;
}