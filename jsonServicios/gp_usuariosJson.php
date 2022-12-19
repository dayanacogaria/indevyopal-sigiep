<?php
###################################################################################
#   **********************      Modificaciones      ******************************#
###################################################################################
#11/02/2019 |Erica G. | Insertar, modificar, eliminar conceptos por financiación
#07/02/2019 |Erica G. | Casos para crear, modificar usuarios serviicos públicos
#09/01/2019 |Erica G. | Creado
###################################################################################
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
require_once '../jsonPptal/funcionesPptal.php';
require_once '../jsonServicios/funcionesServicios.php'; 
session_start();
$con = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$anno       = anno($panno);
$cc         = $con->Listar("SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' "
        . "AND parametrizacionanno = $panno");
$centroc    = $cc[0][0];
$usuario_t  = $_SESSION['usuario_tercero'];


switch ($_REQUEST['action']) {
    #** Buscar Código Máximo por sector
    case 1:
        $sector = $_REQUEST['sector'];
        #** Buscar Si Existen unidades con ese sector **#
        $bu = $con->Listar("SELECT * FROM gp_unidad_vivienda WHERE sector = $sector");
        if (count($bu)>0){
            $row = $con->Listar("SELECT 
                    MAX(cast(p.codigo_catastral as unsigned)) 
                FROM 
                    gp_unidad_vivienda uv
                LEFT JOIN 
                    gp_predio1 p ON uv.predio = p.id_unico 
                WHERE 
                    uv.sector = $sector");
            $codigo = '00'.($row[0][0]+1);
        } else {
            #** Buscar Código Sector **#
            $cs = $con->Listar("SELECT * FROM gp_sector WHERE id_unico = $sector");
            $cod = $cs[0][2];
            $codigo = $cod.'1';
        }
        echo $codigo;
    break;
    #*** Buscar Barrios por ciudad ***#
    case 2: 
        $ciudad = $_POST['ciudad'];
        $rowc   = $con->Listar("SELECT DISTINCT 
                id_unico, nombre 
                FROM gp_barrio 
                WHERE ciudad = $ciudad");
        if(count($rowc)){
            for ($b = 0; $b < count($rowc); $b++){ 
                echo '<option value="'.$rowc[$b][0].'">'.ucwords(mb_strtolower($rowc[$b][1])). '</option>';
            }
        }
    break;
    #** Buscar si código ya existe **#
    case 3:
        $codigo = $_REQUEST['codigo'];
        $be = $con->Listar("SELECT * FROM gp_predio1 WHERE codigo_catastral ='$codigo'");
        if(count($be)>0){
            echo 1;
        } else {
            echo 0;
        }
    break;
    
    #** Guardar Datos usuario **#
    case 4:
        $rta                = 0;
        $sector             = $_REQUEST['sector'];
        $codigo             = $_REQUEST['codigo'];
        $tercero            = $_REQUEST['tercero'];
        $direccion          = $_REQUEST['direccion'];
        $ciudad             = $_REQUEST['ciudad'];
        if(empty($_REQUEST['barrio'])){
            $barrio         = $_REQUEST['barrio'];
        } else {
            $barrio         = $_REQUEST['barrio'];
        } 
        $uso                = $_REQUEST['uso'];
        $estrato            = $_REQUEST['estrato'];
        $estado             = $_REQUEST['estado'];
        $medidor            = $_REQUEST['numero_m'];
        $fecha_m            = fechaC($_REQUEST['fechaI']);
        $estado_m           = $_REQUEST['estado_m'];
        $acueducto          = $_REQUEST['acueducto'];
        $alcantarillado     = $_REQUEST['alcantarillado'];
        $aseo               = $_REQUEST['aseo'];
        $codigo_i           = $_REQUEST['codigoI'];
        $codigo_ruta        = $_REQUEST['codigoR'];
        
        # ** Guardar Predio **#
        $sql_cons = "INSERT INTO `gp_predio1`  
            (`codigo_catastral`,
            `direccion`,
            `ciudad`,
            `nombre`,
            `estado`,`estrato`) 
            VALUES(:codigo_catastral,
            :direccion,
            :ciudad,
            :nombre,
            :estado,:estrato)";
        $sql_dato = array(
                array(":codigo_catastral",$codigo),
                array(":direccion",$direccion),
                array(":ciudad",$ciudad),
                array(":nombre",$codigo),
                array(":estado",$estado),
                array(":estrato",$estrato),
                
        );        
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $predio         = $con->Listar("SELECT MAX(id_unico) FROM gp_predio1 
                WHERE codigo_catastral = '$codigo'");
            $predio         = $predio[0][0];
            if(empty($_REQUEST['manzana'])){
                $manzana    = NULL;
            } else {
                $manzana    = $_REQUEST['manzana'];
            }
            #*** Insertar Unidad Vivienda ***#
            $sql_cons = "INSERT INTO `gp_unidad_vivienda`  
                (`predio`,`tercero`,
                `uso`,`estrato`,
                `sector`,`manzana`,
                `codigo_ruta`, `codigo_interno`) 
                VALUES(:predio,:tercero,
                :uso,:estrato,
                :sector,:manzana,
                :codigo_ruta, :codigo_interno)";
            $sql_dato = array(
                    array(":predio",$predio),
                    array(":tercero",$tercero),
                    array(":uso",$uso),
                    array(":estrato",$estrato),
                    array(":sector",$sector),
                    array(":manzana",$manzana),
                    array(":codigo_ruta",$codigo_ruta),
                    array(":codigo_interno",$codigo_i),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            if(empty($resp)){
                $unidad_v    = $con->Listar("SELECT MAX(id_unico) FROM gp_unidad_vivienda 
                                WHERE predio =$predio AND tercero =$tercero AND sector  = $sector");
                $unidad_v    = $unidad_v[0][0];
                #** Guardar datos medidor **#
                $sql_cons = "INSERT INTO `gp_medidor`  
                    (`referencia`,`fecha_instalacion`,
                    `estado_medidor`) 
                    VALUES(:referencia,:fecha_instalacion,
                    :estado_medidor)";
                $sql_dato = array(
                        array(":referencia",$medidor),
                        array(":fecha_instalacion",$fecha_m),
                        array(":estado_medidor",$estado_m),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato); 
                
                if(empty($resp)){
                    $medidor_id  = $con->Listar("SELECT MAX(id_unico) FROM gp_medidor 
                                WHERE referencia ='$medidor'");
                    $medidor_id  = $medidor_id[0][0];
                    #** Guardar Unidad Vivienda Servicio **#
                    if($acueducto ==1 ){
                        $sql_cons = "INSERT INTO `gp_unidad_vivienda_servicio`  
                            (`unidad_vivienda`,`tipo_servicio`,
                            `estado_servicio`,`aplica_interes`) 
                            VALUES(:unidad_vivienda,:tipo_servicio,
                            :estado_servicio,:aplica_interes)";
                        $sql_dato = array(
                                array(":unidad_vivienda",$unidad_v),
                                array(":tipo_servicio",1),
                                array(":estado_servicio",1),
                                array(":aplica_interes",$_REQUEST['interesAc']),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato); 
                        
                        $unidad_vs  = $con->Listar("SELECT MAX(id_unico) FROM gp_unidad_vivienda_servicio 
                                WHERE unidad_vivienda =$unidad_v AND tipo_servicio =1");
                        $unidad_vs  = $unidad_vs[0][0];
                        #*** Guardar Unidad Vivienda Medidor Servicio **#
                        $sql_cons = "INSERT INTO `gp_unidad_vivienda_medidor_servicio`  
                            (`unidad_vivienda_servicio`,`medidor`) 
                            VALUES(:unidad_vivienda_servicio,:medidor)";
                        $sql_dato = array(
                                array(":unidad_vivienda_servicio",$unidad_vs),
                                array(":medidor",$medidor_id),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato); 
                        if(empty($resp)){
                            $unidad_vms  = $con->Listar("SELECT MAX(id_unico) FROM gp_unidad_vivienda_medidor_servicio 
                                WHERE unidad_vivienda_servicio =$unidad_vs AND medidor =$medidor_id");
                            $unidad_vms  = $unidad_vms[0][0];
                            $rta = $unidad_vms;
                        }
                        
                    }
                    if($alcantarillado ==1 ){
                        $sql_cons = "INSERT INTO `gp_unidad_vivienda_servicio`  
                            (`unidad_vivienda`,`tipo_servicio`,
                            `estado_servicio`,`aplica_interes`) 
                            VALUES(:unidad_vivienda,:tipo_servicio,
                            :estado_servicio, :aplica_interes)";
                        $sql_dato = array(
                                array(":unidad_vivienda",$unidad_v),
                                array(":tipo_servicio",2),
                                array(":estado_servicio",1),
                                array(":aplica_interes",$_REQUEST['interesAl']),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato); 
                    }
                    if($aseo ==1 ){
                        $sql_cons = "INSERT INTO `gp_unidad_vivienda_servicio`  
                            (`unidad_vivienda`,`tipo_servicio`,
                            `estado_servicio`,`aplica_interes`) 
                            VALUES(:unidad_vivienda,:tipo_servicio,
                            :estado_servicio,:aplica_interes)";
                        $sql_dato = array(
                                array(":unidad_vivienda",$unidad_v),
                                array(":tipo_servicio",3),
                                array(":estado_servicio",1),
                                array(":aplica_interes",$_REQUEST['interesAs']),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato); 
                    }                    
                }
                
            } else {
                $rta = 0;
            }
        }
        else{
            $rta = 0;            
        }
        echo $rta;
    break;
    
    #** Cargar Usuarios Por Sector Slt ***#
    case 5:
        $sector = $_REQUEST['sector'];
        echo '<option value"">Usuario</option>';
        $rowu = $con->Listar("SELECT uvms.id_unico, 
            IF(CONCAT_WS(' ',
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
            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
            p.codigo_catastral 
        FROM gp_unidad_vivienda_medidor_servicio uvms 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico
        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
        LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
        LEFT JOIN gp_uso u ON uv.uso= u.id_unico 
        LEFT JOIN gp_estrato e ON uv.estrato = e.id_unico 
        LEFT JOIN gp_tipo_manzana tm ON uv.manzana = tm.id_unico 
        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
        LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
        WHERE uv.sector = ".$sector." AND m.estado_medidor != 3 
             ORDER BY cast(p.codigo_catastral as unsigned ) ASC");
        for ($i = 0; $i < count($rowu); $i++) {
            echo '<option value="'.$rowu[$i][0].'">'.$rowu[$i][3].' - '.ucwords(mb_strtolower($rowu[$i][1])).'</option>';
        }
    break;
    #** Modificar Datos usuario **#
    case 6:
        $rta                = 0;
        $sector             = $_REQUEST['sector'];
        $codigo             = $_REQUEST['codigo'];
        $tercero            = $_REQUEST['tercero'];
        $direccion          = $_REQUEST['direccion'];
        $ciudad             = $_REQUEST['ciudad'];
        if(empty($_REQUEST['barrio'])){
            $barrio         = $_REQUEST['barrio'];
        } else {
            $barrio         = $_REQUEST['barrio'];
        } 
        $uso                = $_REQUEST['uso'];
        $estrato            = $_REQUEST['estrato'];
        $estado             = $_REQUEST['estado'];
        $medidor            = $_REQUEST['numero_m'];
        $fecha_m            = fechaC($_REQUEST['fechaI']);
        $estado_m           = $_REQUEST['estado_m'];
        $acueducto          = $_REQUEST['acueducto'];
        $alcantarillado     = $_REQUEST['alcantarillado'];
        $aseo               = $_REQUEST['aseo'];
        
        $id_predio          = $_REQUEST['id_predio'];
        $id_uv              = $_REQUEST['id_uv'];
        $id_uvs             = $_REQUEST['id_uvs'];
        $id_uvms            = $_REQUEST['id_uvms'];
        $id_medidor         = $_REQUEST['id_medidor'];
        $codigo_i           = $_REQUEST['codigoI'];
        $codigo_ruta        = $_REQUEST['codigoR'];
                            
        # ** Modificar Predio **#
        $sql_cons = "UPDATE `gp_predio1`  
            SET `codigo_catastral`=:codigo_catastral,
            `direccion`=:direccion,
            `ciudad`=:ciudad,
            `nombre`=:nombre,
            `estado`=:estado,
            `estrato`=:estrato 
            WHERE `id_unico`=:id_unico" ;
        $sql_dato = array(
                array(":codigo_catastral",$codigo),
                array(":direccion",$direccion),
                array(":ciudad",$ciudad),
                array(":nombre",$codigo),
                array(":estado",$estado),
                array(":estrato",$estrato),
                array(":id_unico",$id_predio),
                
        );        
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $predio         = $id_predio;
            if(empty($_REQUEST['manzana'])){
                $manzana    = NULL;
            } else {
                $manzana    = $_REQUEST['manzana'];
            }
            #*** Actualizar Unidad Vivienda ***#
            $sql_cons = "UPDATE `gp_unidad_vivienda`  
                SET `tercero`=:tercero,
                `uso`=:uso,`estrato`=:estrato,
                `sector`=:sector,`manzana`=:manzana,
                `codigo_ruta`=:codigo_ruta, 
                `codigo_interno`=:codigo_interno 
                WHERE `id_unico`=:id_unico";
            $sql_dato = array(
                    array(":tercero",$tercero),
                    array(":uso",$uso),
                    array(":estrato",$estrato),
                    array(":sector",$sector),
                    array(":manzana",$manzana),
                    array(":codigo_ruta",$codigo_ruta),
                    array(":codigo_interno",$codigo_i),
                    array(":id_unico",$id_uv),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            if(empty($resp)){
                $unidad_v    = $id_uv;
                #** Modificar datos medidor **#
                $sql_cons = "UPDATE `gp_medidor`  
                    SET `referencia`=:referencia,
                    `fecha_instalacion`=:fecha_instalacion,
                    `estado_medidor`=:estado_medidor 
                    WHERE `id_unico`=:id_unico";
                $sql_dato = array(
                        array(":referencia",$medidor),
                        array(":fecha_instalacion",$fecha_m),
                        array(":estado_medidor",$estado_m),
                        array(":id_unico",$id_medidor),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato); 
                if(empty($resp)){
                    $medidor_id  = $id_medidor;
                    #** Modificar Unidad Vivienda Servicio **#
                    if($acueducto ==1 ){
                        $estado_s = 1;
                    } else {
                        $estado_s = 2;
                    }
                    $sql_cons = "UPDATE `gp_unidad_vivienda_servicio`  
                        SET `estado_servicio`=:estado_servicio , 
                        `aplica_interes` = :aplica_interes
                        WHERE `unidad_vivienda`=:unidad_vivienda 
                        AND `tipo_servicio`=:tipo_servicio ";
                    $sql_dato = array(
                            array(":estado_servicio",$estado_s),
                            array(":tipo_servicio",1),
                            array(":unidad_vivienda",$id_uv),
                            array(":aplica_interes",$_REQUEST['interesAc']),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato); 
                    if(empty($resp)){
                        $unidad_vms  = $id_uvms;
                        $rta = $unidad_vms;
                    }
                    if($alcantarillado ==1 ){
                        $estado_s = 1;
                    } else {
                        $estado_s = 2;
                    }
                    $sql_cons = "UPDATE `gp_unidad_vivienda_servicio`  
                        SET `estado_servicio`=:estado_servicio , 
                        `aplica_interes` = :aplica_interes
                        WHERE `unidad_vivienda`=:unidad_vivienda 
                        AND `tipo_servicio`=:tipo_servicio ";
                    $sql_dato = array(
                            array(":estado_servicio",$estado_s),
                            array(":tipo_servicio",2),
                            array(":unidad_vivienda",$id_uv),
                            array(":aplica_interes",$_REQUEST['interesAl']),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    if($aseo ==1 ){
                        $estado_s = 1;
                    } else {
                        $estado_s = 2;
                    }     
                    $sql_cons = "UPDATE `gp_unidad_vivienda_servicio`   
                        SET `estado_servicio`=:estado_servicio , 
                        `aplica_interes` = :aplica_interes 
                        WHERE `unidad_vivienda`=:unidad_vivienda 
                        AND `tipo_servicio`=:tipo_servicio ";
                    $sql_dato = array(
                            array(":estado_servicio",$estado_s),
                            array(":tipo_servicio",3),
                            array(":unidad_vivienda",$id_uv),
                            array(":aplica_interes",$_REQUEST['interesAs']),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato); 
                }
                
            } else {
                $rta = 0;
            }
        }
        else{
            $rta = 0;            
        }
        echo $rta;
    break;
    
    #** Guardar Otros Conceptos **#
    case 7:
        $concepto   = $_REQUEST['sltConcepto'];
        $fecha      = fechaC($_REQUEST['txtfecha']);
        $total_c    = $_REQUEST['txttotalc'];
        $valor_c    = $_REQUEST['txtvalorcuota'];
        $id_uvms    = $_REQUEST['iduvms'];
        $valor_t    = $valor_c * $total_c;
        $rta        = 0;
        $sql_cons = "INSERT INTO `gf_otros_conceptos`  
            (`concepto`,`unidad_vivienda_ms`,
            `total_cuotas`, `valor_cuota`, 
            `valor_total`, `cuotas_pagas`, 
            `cuotas_pendientes`,`fecha`) 
        VALUES(:concepto,:unidad_vivienda_ms,
            :total_cuotas,:valor_cuota, 
            :valor_total, :cuotas_pagas, 
            :cuotas_pendientes,:fecha )";
        $sql_dato = array(
            array(":concepto",$concepto),
            array(":unidad_vivienda_ms",$id_uvms),
            array(":total_cuotas",$total_c),
            array(":valor_cuota",$valor_c),
            array(":valor_total",$valor_t),
            array(":cuotas_pagas",0),
            array(":cuotas_pendientes",$total_c),
            array(":fecha",$fecha),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        $valot_ab = $valor_t;
        if(empty($resp)){
            $id_oc = $con->Listar("SELECT * FROM gf_otros_conceptos 
                WHERE concepto =$concepto AND unidad_vivienda_ms =$id_uvms AND fecha= '$fecha' ");
            
            #**** Buscar Si El concepto es de tipo operacion 4 financiacion ***#
            $rta = 1;
            $rowdc = $con->Listar("SELECT tipo_operacion FROM gp_concepto WHERE id_unico= $concepto");
            $tipo_o = $rowdc[0][0];
            if($tipo_o ==4){
                // Buscar Unidades de esa vivienda 
                $uds = $con->Listar("SELECT GROUP_CONCAT(uvms.id_unico) 
                    FROM gp_unidad_vivienda_medidor_servicio uvms 
                    WHERE uvms.unidad_vivienda_servicio 
                    IN ( SELECT uvmss.unidad_vivienda_servicio FROM gp_unidad_vivienda_medidor_servicio uvmss 
                    WHERE uvmss.id_unico = $id_uvms)");
                #** Buscar Facturas con saldo de la unidad de vivienda  y realizarles pago**#
                $deuda_anterior =0;
                $da = $con->Listar("SELECT GROUP_CONCAT(df.id_unico), 
                        SUM(df.valor_total_ajustado), f.id_unico 
                    FROM gp_detalle_factura df 
                    LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                    WHERE f.unidad_vivienda_servicio IN(".$uds[0][0].")  
                    GROUP BY f.id_unico ORDER BY f.fecha_factura ASC");
                if(count($da)>0){
                    $factura = $da[0][2];
                    #* Buscar Tipo Pago 
                    $tp         = $con->Listar("SELECT * FROM gp_tipo_pago WHERE financiacion = 1");
                    $tipoPago   = $tp[0][0];
                    #Buscar Datos Factura
                    $df = $con->Listar("SELECT f.id_unico,
                                f.numero_factura, tp.nombre,
                                f.tercero, f.descripcion, f.fecha_factura, f.centrocosto,
                                f.unidad_vivienda_servicio   
                            FROM gp_factura f LEFT JOIN gp_tipo_factura tp ON tp.id_unico = f.tipofactura 
                            WHERE f.id_unico = $factura");
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
                            estado, parametrizacionanno, usuario)
                            VALUES('$numeroPago',
                            $tipoPago,$responsable,
                            '$fecha',$estado, $panno, $usuario_t)";
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
                    for ($d = 0; $d < count($da); $d++) {
                        #*** Buscar Recaudo ***#
                        $id_df      = $da[$d][0];
                        $valor_f    = $da[$d][1];
                        $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                            LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                            WHERE dp.detalle_factura IN ($id_df)");
                        if(count(($rc))<=0){
                            $recaudo = 0;
                        }elseif(empty ($rc[0][0])){
                            $recaudo = 0;
                        } else {
                            $recaudo = $rc[0][0];
                        }
                        if($valot_ab>0){
                            $deuda_f = $valor_f -$recaudo;
                            if($deuda_f<=$valot_ab){
                                $deuda_anterior = $deuda_f;
                                $valot_ab -=$deuda_anterior;
                            } else {
                                $deuda_anterior = $valot_ab;
                                $valot_ab -=$deuda_anterior;
                            }
                        } else {
                            $deuda_anterior = 0;
                        }
                        if($deuda_anterior>0){
                            #** Guardar Pago ***#
                            $valor      = $deuda_anterior;
                            $factura    = $da[$d][2];

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
                                }
                            }
                        }
                    }
                    #** Actualizar Concepto para que se relacione a pago **#
                    $sql_cons = "UPDATE `gf_otros_conceptos`  
                        SET `recaudo`=:recaudo 
                        WHERE `id_unico`=:id_unico ";
                    $sql_dato = array(
                        array(":recaudo",$pago),
                        array(":id_unico",$id_oc[0][0]),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    #*** Actualizar otros conceptos donde la fecha sea menor al concepto de financiación
                    $sql_cons = "UPDATE `gf_otros_conceptos`  
                        SET `cuotas_pendientes`=:cuotas_pendientes, 
                        `asociado`=:asociado 
                        WHERE `unidad_vivienda_ms`=:unidad_vivienda_ms 
                        AND `fecha`<=:fecha AND `id_unico`!=:id_unico";
                    $sql_dato = array(
                        array(":cuotas_pendientes",0),
                        array(":asociado",$id_oc[0][0]),
                        array(":unidad_vivienda_ms",$id_uvms),
                        array(":fecha",$fecha),
                        array(":id_unico",$id_oc[0][0]),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                }                
            }
        }
        echo $rta;
    break;
    #** Modificar Otros Conceptos **#
    case 8:
        $concepto   = $_REQUEST['sltConcepto'];
        $fecha      = fechaC($_REQUEST['txtfecha']);
        $total_c    = $_REQUEST['txttotalc'];
        $valor_c    = $_REQUEST['txtvalorcuota'];
        $id_concepto= $_REQUEST['id_concepto'];
        $valor_t    = $valor_c * $total_c;
        $cuotas_pa  = $_REQUEST['txttotalcpagas'];
        $cuotas_pen = $_REQUEST['txttotalcpen'];
        $rta        = 0;
        $sql_cons = "UPDATE `gf_otros_conceptos`  
            SET `concepto`=:concepto,
            `total_cuotas`=:total_cuotas, 
            `valor_cuota`=:valor_cuota, 
            `valor_total`=:valor_total, 
            `cuotas_pagas`=:cuotas_pagas, 
            `cuotas_pendientes`=:cuotas_pendientes,
            `fecha`=:fecha 
            WHERE `id_unico`=:id_unico ";
        $sql_dato = array(
            array(":concepto",$concepto),
            array(":total_cuotas",$total_c),
            array(":valor_cuota",$valor_c),
            array(":valor_total",$valor_t),
            array(":cuotas_pagas",$cuotas_pa),
            array(":cuotas_pendientes",$cuotas_pen),
            array(":fecha",$fecha),
            array(":id_unico",$id_concepto),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $rta = 1;
        }
        echo $rta;
    break;
    #* html modal guardar concepto *#
    case 9:
        $html = "";
        $html .= '<div class="modal-header" id="forma-modal">';
        $html .= '<button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><span class="glyphicon glyphicon-remove"></span></button>';
        $html .= '<h4 class="modal-title" style="font-size: 24px; padding: 3px;">Registrar Concepto</h4>';
        $html .= '</div>';
        $html .= '<form action="javaScript:guardarConcepto()" method="post" class="form-horizontal" id="formConcepto" enctype="multipart/form-data" style="font-size: 10px !important;">';
        $html .= '<div class="modal-body">';
        $html .= '<div class="row">';
        $html .= '<input type="hidden" name="iduvms" id="iduvms">';
        $html .= '<p align="center" style="margin-bottom: 15px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>';
        $html .= '<div class="form-group">';
        $html .= '<label for="sltConcepto" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Concepto:</label>';
        $html .= '<div class="col-sm-3 col-md-3 col-lg-3">';
        $html .= '<select name="sltConcepto" id="sltConcepto" class="form-control select" title="Seleccione Concepto" required tabindex="7">';
        $html .= '<option value="">Concepto</option>';
        $rowti = $con->Listar("SELECT id_unico, LOWER(nombre) FROM gp_concepto WHERE tipo_concepto = 5 AND tipo_operacion != 1 AND nombre !='ajuste'");
        for ($ti = 0;$ti < count($rowti);$ti++) {
        $html .= '<option value="'.$rowti[$ti][0].'">'.ucwords($rowti[$ti][1]).'</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '<label for="txtfecha" class="col-sm-3 col-md-3 col-lg-3 control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>';
        $html .= '<div class="col-sm-3 col-md-3 col-lg-3">';
        $html .= '<input type="text" name="txtfecha" id="txtfecha" class="form-control" maxlength="100" title="Ingrese fecha"  placeholder="Fecha" required style="width: 100%; font-size: 10px !important;" tabindex="8" autocomplete="off">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<label for="txttotalc" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><span class="obligado">*</span>Total Cuotas:</label>';
        $html .= '<div class="col-sm-3 col-md-3 col-lg-3">';
        $html .= '<input type="text" name="txttotalc" id="txttotalc" class="form-control" maxlength="100" title="Ingrese el Total Cuotas" placeholder="Total Cuotas"  style="width: 100%;font-size: 10px !important;" tabindex="3" autocomplete="off" required="required">';
        $html .= '</div>';
        $html .= '<label for="txtvalorcuota" class="control-label col-sm-3 col-md-3 col-lg-3 text-right"><span class="obligado">*</span>Valor Cuota:</label>';
        $html .= '<div class="col-sm-3 col-md-3 col-lg-3">';
        $html .= '<input type="text" name="txtvalorcuota" id="txtvalorcuota" class="form-control" maxlength="100" title="Ingrese el Valor Cuota" placeholder="Valor Cuota" style="width: 100%; font-size: 10px !important;" tabindex="4" autocomplete="off" required="required">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="modal-footer" id="forma-modal">';
        $html .= '<div class="row">';
        $html .= '<div class="form-group">';
        $html .= '<label for="no" class="col-sm-11 col-md-11 col-lg-11 control-label"></label>';
        $html .= '<div class="col-sm-1 col-md-1 col-lg-1 text-right" style="margin-left:-20px">';
        $html .= '<button type="submit" class="btn btn-default" id="btnModalGuardarT"><span class="glyphicon glyphicon-floppy-disk"></span></button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</form>';
        echo $html;
    break;
    
    #* html modal modificar concepto *#
    case 10:
        $id_c = $_REQUEST['id_c'];
        $rowd = $con->Listar("SELECT o.id_unico, 
            c.id_unico, LOWER(c.nombre), 
            o.total_cuotas, o.valor_cuota, 
            o.valor_total, o.cuotas_pagas, 
            o.cuotas_pendientes, DATE_FORMAT(o.fecha, '%d/%m/%Y') 
        FROM gf_otros_conceptos o 
        LEFT JOIN gp_concepto c ON o.concepto = c.id_unico 
        WHERE o.id_unico = $id_c");
        $html = "";
        $html .= '<div class="modal-header" id="forma-modal">';
        $html .= '<button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><span class="glyphicon glyphicon-remove"></span></button>';
        $html .= '<h4 class="modal-title" style="font-size: 24px; padding: 3px;">Modificar Concepto</h4>';
        $html .= '</div>';
        $html .= '<form action="javaScript:modificarConcepto()" method="post" class="form-horizontal" id="formConcepto" enctype="multipart/form-data" style="font-size: 10px !important;">';
        $html .= '<div class="modal-body">';
        $html .= '<div class="row">';
        $html .= '<input type="hidden" name="id_concepto" id="id_concepto" value="'.$rowd[0][0].'">';
        $html .= '<p align="center" style="margin-bottom: 15px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>';
        $html .= '<div class="form-group">';
        $html .= '<label for="sltConcepto" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Concepto:</label>';
        $html .= '<div class="col-sm-3 col-md-3 col-lg-3">';
        $html .= '<select name="sltConcepto" id="sltConcepto" class="form-control select" title="Seleccione Concepto" required tabindex="7">';
        if(empty($rowd[0][1])){
            $html .= '<option value=""> - </option>';
        } else {
            $html .= '<option value="'.$rowd[0][1].'">'.ucwords($rowd[0][2]).'</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '<label for="txtfecha" class="col-sm-3 col-md-3 col-lg-3 control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>';
        $html .= '<div class="col-sm-3 col-md-3 col-lg-3">';
        $html .= '<input type="text" name="txtfecha" id="txtfecha" class="form-control"  value="'.$rowd[0][8].'"  maxlength="100" title="Ingrese fecha"  placeholder="Fecha" required style="width: 100%; font-size: 10px !important;" tabindex="8" autocomplete="off">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<label for="txttotalc" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><span class="obligado">*</span>Total Cuotas:</label>';
        $html .= '<div class="col-sm-3 col-md-3 col-lg-3">';
        $html .= '<input type="text" name="txttotalc" id="txttotalc" value="'.$rowd[0][3].'" class="form-control" maxlength="100" title="Ingrese el Total Cuotas" placeholder="Total Cuotas"  style="width: 100%;font-size: 10px !important;" tabindex="3" autocomplete="off" required="required">';
        $html .= '</div>';
        $html .= '<label for="txtvalorcuota" class="control-label col-sm-3 col-md-3 col-lg-3 text-right"><span class="obligado">*</span>Valor Cuota:</label>';
        $html .= '<div class="col-sm-3 col-md-3 col-lg-3">';
        $html .= '<input type="text" name="txtvalorcuota" id="txtvalorcuota" value="'.$rowd[0][4].'" class="form-control" maxlength="100" title="Ingrese el Valor Cuota" placeholder="Valor Cuota" style="width: 100%; font-size: 10px !important;" tabindex="4" autocomplete="off" required="required">';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="form-group">';
        $html .= '<label for="txttotalcpagas" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><span class="obligado">*</span>Total Cuotas Pagas:</label>';
        $html .= '<div class="col-sm-3 col-md-3 col-lg-3">';
        $html .= '<input type="text" name="txttotalcpagas" id="txttotalcpagas" value="'.$rowd[0][6].'" class="form-control" maxlength="100" title="Ingrese el Total Cuotas Pagas" placeholder="Total Cuotas Pagas"  style="width: 100%;font-size: 10px !important;" tabindex="3" autocomplete="off" required="required">';
        $html .= '</div>';
        $html .= '<label for="txttotalcpen" class="control-label col-sm-3 col-md-3 col-lg-3 text-right"><span class="obligado">*</span>Total Cuotas Pendientes:</label>';
        $html .= '<div class="col-sm-3 col-md-3 col-lg-3">';
        $html .= '<input type="text" name="txttotalcpen" id="txttotalcpen" value="'.$rowd[0][7].'" class="form-control" maxlength="100" title="Ingrese el Total Cuotas Pendientes" placeholder="Total Cuotas Pendientes" style="width: 100%; font-size: 10px !important;" tabindex="4" autocomplete="off" required="required">';
        $html .= '</div>';
        $html .= '</div>';        
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="modal-footer" id="forma-modal">';
        $html .= '<div class="row">';
        $html .= '<div class="form-group">';
        $html .= '<label for="no" class="col-sm-11 col-md-11 col-lg-11 control-label"></label>';
        $html .= '<div class="col-sm-1 col-md-1 col-lg-1 text-right" style="margin-left:-20px">';
        $html .= '<button type="submit" class="btn btn-default" id="btnModalGuardarT"><span class="glyphicon glyphicon-floppy-disk"></span></button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</form>';
        echo $html;
    break;
    
    #** Eliminar Concepto **#;
    case 11:
        $id = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gf_otros_conceptos`  
            WHERE `id_unico` =:id_unico";
        $sql_dato = array(
                array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $rta = true;
        } else {
            $rta = false;
        }
        echo $rta;
    break;
    #** Verificar si hay una factura con fecha mayor a la financiación a aliminar **#
    case 12:
        $id_c = $_REQUEST['id'];
        #* Buscar Recaudo *#
        $d_c = $con->Listar("SELECT o.recaudo, o.fecha, o.unidad_vivienda_ms FROM gf_otros_conceptos o WHERE o.id_unico = $id_c");
        $fecha_o = $d_c[0][1];
        #* Buscar si hay facturas con fechas posteriores *#
        $fac = $con->Listar("SELECT * FROM gp_factura WHERE unidad_vivienda_servicio =".$d_c[0][0]." AND fecha >'$fecha_o'");
        if(count($fac)>0){
            $rta = 0;
        } else {
            $rta = 1;
        }
        echo $rta;
    break;
    #* Eliminar Concepto_financiacion 
    case 13:
        $id_c = $_REQUEST['id'];
        $d_c  = $con->Listar("SELECT o.recaudo, o.fecha, o.unidad_vivienda_ms 
            FROM gf_otros_conceptos o WHERE o.id_unico = $id_c");
        #* Eliminar Detalles Pago *#
        $sql_cons ="DELETE FROM `gp_detalle_pago`  
            WHERE `pago` =:pago";
        $sql_dato = array(
                array(":pago",$d_c[0][0]),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            ##* Buscar Conceptos Relacionados
            $rowcc = $con->Listar("SELECT id_unico, 
                total_cuotas, cuotas_pagas  
                FROM gf_otros_conceptos 
                WHERE asociado = $id_c");
            if(count($rowcc)>0){
                for ($c = 0; $c < count($rowcc); $c++) {
                    $id_ca      = $rowcc[$c][0];
                    $cuotas_p   = $rowcc[$c][1]-$rowcc[$c][2];
                    #*** Actualizar otros conceptos donde la fecha sea menor al concepto de financiación
                    $sql_cons = "UPDATE `gf_otros_conceptos`  
                        SET `cuotas_pendientes`=:cuotas_pendientes, 
                        `asociado`=:asociado 
                        WHERE `id_unico`=:id_unico";
                    $sql_dato = array(
                        array(":cuotas_pendientes",$cuotas_p),
                        array(":asociado",NULL),
                        array(":id_unico",$id_ca),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                }
            }
            $sql_cons ="DELETE FROM `gf_otros_conceptos`  
                WHERE `id_unico` =:id_unico";
            $sql_dato = array(
                    array(":id_unico",$id_c),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            if(empty($resp)){
                $rta = true;
            } else {
                $rta = false;
            }
        } else {
            $rta= false;
        }
        echo $rta;
    break;
    
    #* Cambiar Medidor *#
    case 14:
        $rta = 0;
        $id_uv = $_REQUEST['id_u'];
        
        #** Guardar datos medidor **#
        $sql_cons = "INSERT INTO `gp_medidor`  
            (`referencia`,`fecha_instalacion`,
            `estado_medidor`) 
            VALUES(:referencia,:fecha_instalacion,
            :estado_medidor)";
        $sql_dato = array(
                array(":referencia",$_REQUEST['txtNumeroM']),
                array(":fecha_instalacion",fechaC($_REQUEST['txtFecham'])),
                array(":estado_medidor",$_REQUEST['sltEstadom']),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 

        if(empty($resp)){
            $medidor_id  = $con->Listar("SELECT MAX(id_unico) FROM gp_medidor 
                        WHERE referencia ='".$_REQUEST['txtNumeroM']."'");
            $medidor_id  = $medidor_id[0][0];
            #Buscar unidad vivienda
            $d_uv = $con->Listar("SELECT unidad_vivienda_servicio, medidor FROM gp_unidad_vivienda_medidor_servicio WHERE id_unico = $id_uv");
            #* Actualizar estado  medidor *#
            $sql_cons = "UPDATE `gp_medidor`  
                SET `estado_medidor`=:estado_medidor 
                WHERE `id_unico`=:id_unico";
            $sql_dato = array(
                array(":estado_medidor",3),
                array(":id_unico",$d_uv[0][1]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            #** Insertar Unidad vivienda ms con nuevo medidor **#
            $sql_cons = "INSERT INTO `gp_unidad_vivienda_medidor_servicio`  
                (`unidad_vivienda_servicio`,`medidor`) 
                VALUES(:unidad_vivienda_servicio,:medidor)";
            $sql_dato = array(
                    array(":unidad_vivienda_servicio",$d_uv[0][0]),
                    array(":medidor",$medidor_id),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            if(empty($resp)){
                $unidad_vms  = $con->Listar("SELECT MAX(id_unico) FROM gp_unidad_vivienda_medidor_servicio 
                    WHERE unidad_vivienda_servicio =".$d_uv[0][0]." AND medidor =$medidor_id");
                $unidad_vms  = $unidad_vms[0][0];
                $rta = $unidad_vms;
            }
        }
        echo $rta;
    break;
    
    #* html Reestructurar Cuota *#
    case 15:
        #* Buscar Valor Saldo cuotas 
        $id_cuota   = $_REQUEST['id'];
        $iduv       = $con->Listar("SELECT unidad_vivienda_ms, 
            valor_cuota, cuotas_pendientes 
            FROM gf_otros_conceptos WHERE id_unico = $id_cuota");
        
        $id_uvms    = $iduv[0][0];
        $saldo_c    = $iduv[0][1]*$iduv[0][2];
        $deuda      = deudaActual($id_uvms)+$saldo_c;
        $html       = "";
        $html .= '<div class="modal-header" id="forma-modal">';
        $html .= '<button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><span class="glyphicon glyphicon-remove"></span></button>';
        $html .= '<h4 class="modal-title" style="font-size: 24px; padding: 3px;">Reestructurar Concepto</h4>';
        $html .= '</div>';
        $html .= '<form action="javaScript:ReestructurarConcepto()" method="post" class="form-horizontal" id="formResConcepto" enctype="multipart/form-data" style="font-size: 10px !important;">';
        $html .= '<div class="modal-body">';
        $html .= '<input type="hidden" name="valor_r" id="valor_r" value="'.$deuda.'">';
        $html .= '<input type="hidden" name="valor_m" id="valor_m" value="'.$saldo_c.'">';
        $html .= '<div class="row">';
        $html .= '<input type="hidden" name="id_concepto" id="id_concepto">';
        $html .= '<p align="center" style="margin-bottom: 15px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>';
        $html .= '<h4 style="margin-left: 15px;"><strong>DEUDA: $'. number_format($deuda,2, '.', ',').'</strong></h4>';
        $html .= '<div class="form-group">';
        $html .= '<label for="txtfecha" class="col-sm-1 col-md-1 col-lg-1 control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>';
        $html .= '<div class="col-sm-2 col-md-2 col-lg-2">';
        $html .= '<input type="text" name="txtfecha" id="txtfecha" class="form-control" maxlength="100" title="Ingrese fecha"  placeholder="Fecha" required style="width: 100%; font-size: 10px !important;" tabindex="8" autocomplete="off" >';
        $html .= '</div>';
        $html .= '<label for="txttotalc" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><span class="obligado">*</span>Total Cuotas:</label>';
        $html .= '<div class="col-sm-2 col-md-2 col-lg-2">';
        $html .= '<input type="text" name="txttotalcR" id="txttotalcR" class="form-control" maxlength="100" title="Ingrese el Total Cuotas" placeholder="Total Cuotas"  style="width: 100%;font-size: 10px !important;" tabindex="3" autocomplete="off" required="required" onchange="javaScript:cuotas()">';
        $html .= '</div>';
        $html .= '<label for="txtvalorcuota" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><span class="obligado">*</span>Valor Cuota:</label>';
        $html .= '<div class="col-sm-2 col-md-2 col-lg-2">';
        $html .= '<input type="text" name="txtvalorcuotaR" id="txtvalorcuotaR" class="form-control" maxlength="100" title="Ingrese el Valor Cuota" placeholder="Valor Cuota" style="width: 100%; font-size: 10px !important;" tabindex="4" autocomplete="off" required="required" onchange="javaScript:valorcuotas()">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="modal-footer" id="forma-modal">';
        $html .= '<div class="row">';
        $html .= '<div class="form-group">';
        $html .= '<label for="no" class="col-sm-11 col-md-11 col-lg-11 control-label"></label>';
        $html .= '<div class="col-sm-1 col-md-1 col-lg-1 text-right" style="margin-left:-20px">';
        $html .= '<button type="submit" class="btn btn-default" id="btnModalGuardarT"><span class="glyphicon glyphicon-floppy-disk"></span></button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</form>';
        echo $html;
    break;
    
    #** Reestructurar Otros Conceptos **#
    case 16:
        $fecha      = fechaC($_REQUEST['txtfecha']);
        $total_c    = $_REQUEST['txttotalcR'];
        $valor_c    = $_REQUEST['txtvalorcuotaR'];
        $id_concept = $_REQUEST['id_concepto'];
        $valor_fin  = $_REQUEST['valor_m'];
        $valor_t    = $valor_c * $total_c;
        $rta        = 0;
        #** Actualizar Concepto para que se relacione a pago **#
        $sql_cons = "UPDATE `gf_otros_conceptos`  
            SET `cuotas_pendientes`=:cuotas_pendientes 
            WHERE `id_unico`=:id_unico ";
        $sql_dato = array(
            array(":cuotas_pendientes",0),
            array(":id_unico",$id_concept),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        var_dump($resp);
        #* Buscar Datos             
        $dc         = $con->Listar("SELECT concepto,
            unidad_vivienda_ms 
            FROM gf_otros_conceptos 
            WHERE id_unico =$id_concept");
        $sql_cons = "INSERT INTO `gf_otros_conceptos`  
            (`concepto`,`unidad_vivienda_ms`,
            `total_cuotas`, `valor_cuota`, 
            `valor_total`, `cuotas_pagas`, 
            `cuotas_pendientes`,`fecha`, `reestructurado`) 
        VALUES(:concepto,:unidad_vivienda_ms,
            :total_cuotas,:valor_cuota, 
            :valor_total, :cuotas_pagas, 
            :cuotas_pendientes,:fecha, :reestructurado )";
        $sql_dato = array(
            array(":concepto",$dc[0][0]),
            array(":unidad_vivienda_ms",$dc[0][1]),
            array(":total_cuotas",$total_c),
            array(":valor_cuota",$valor_c),
            array(":valor_total",$valor_t),
            array(":cuotas_pagas",0),
            array(":cuotas_pendientes",$total_c),
            array(":fecha",$fecha),
            array(":reestructurado",1),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        $valot_ab = $valor_t - $valor_fin;
        if(empty($resp)){
            #**** Buscar Si El concepto es de tipo operacion 4 financiacion ***#
            $rta = 1;
            $rowdc = $con->Listar("SELECT tipo_operacion FROM gp_concepto WHERE id_unico= ".$dc[0][0]);
            $tipo_o = $rowdc[0][0];
            if($tipo_o ==4){
                // Buscar Unidades de esa vivienda 
                $uds = $con->Listar("SELECT GROUP_CONCAT(uvms.id_unico) 
                    FROM gp_unidad_vivienda_medidor_servicio uvms 
                    WHERE uvms.unidad_vivienda_servicio 
                    IN ( SELECT uvmss.unidad_vivienda_servicio FROM gp_unidad_vivienda_medidor_servicio uvmss 
                    WHERE uvmss.id_unico =".$dc[0][1].")");
                #** Buscar Facturas con saldo de la unidad de vivienda  y realizarles pago**#
                $deuda_anterior =0;
                $da = $con->Listar("SELECT GROUP_CONCAT(df.id_unico), 
                        SUM(df.valor_total_ajustado), f.id_unico 
                    FROM gp_detalle_factura df 
                    LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                    WHERE f.unidad_vivienda_servicio IN(".$uds[0][0].")  
                    GROUP BY f.id_unico ORDER BY f.fecha_factura ASC");
                if(count($da)>0){
                    $factura = $da[0][2];
                    #* Buscar Tipo Pago 
                    $tp         = $con->Listar("SELECT * FROM gp_tipo_pago WHERE financiacion = 1");
                    $tipoPago   = $tp[0][0];
                    #Buscar Datos Factura
                    $df = $con->Listar("SELECT f.id_unico,
                                f.numero_factura, tp.nombre,
                                f.tercero, f.descripcion, f.fecha_factura, f.centrocosto,
                                f.unidad_vivienda_servicio   
                            FROM gp_factura f LEFT JOIN gp_tipo_factura tp ON tp.id_unico = f.tipofactura 
                            WHERE f.id_unico = $factura");
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
                            estado, parametrizacionanno, usuario)
                            VALUES('$numeroPago',
                            $tipoPago,$responsable,
                            '$fecha',$estado, $panno, $usuario_t)";
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
                    for ($d = 0; $d < count($da); $d++) {
                        #*** Buscar Recaudo ***#
                        $id_df      = $da[$d][0];
                        $valor_f    = $da[$d][1];
                        $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                            LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                            WHERE dp.detalle_factura IN ($id_df)");
                        if(count(($rc))<=0){
                            $recaudo = 0;
                        }elseif(empty ($rc[0][0])){
                            $recaudo = 0;
                        } else {
                            $recaudo = $rc[0][0];
                        }
                        if($valot_ab>0){
                            $deuda_f = $valor_f -$recaudo;
                            if($deuda_f<=$valot_ab){
                                $deuda_anterior = $deuda_f;
                                $valot_ab -=$deuda_anterior;
                            } else {
                                $deuda_anterior = $valot_ab;
                                $valot_ab -=$deuda_anterior;
                            }
                        } else {
                            $deuda_anterior = 0;
                        }
                        if($deuda_anterior>0){
                            #** Guardar Pago ***#
                            $valor      = $deuda_anterior;
                            $factura    = $da[$d][2];

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
                                }
                            }
                        }
                    }
                    #** Actualizar Concepto para que se relacione a pago **#
                    $sql_cons = "UPDATE `gf_otros_conceptos`  
                        SET `recaudo`=:recaudo 
                        WHERE `id_unico`=:id_unico ";
                    $sql_dato = array(
                        array(":recaudo",$pago),
                        array(":id_unico",$id_oc[0][0]),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    #*** Actualizar otros conceptos donde la fecha sea menor al concepto de financiación
                    $sql_cons = "UPDATE `gf_otros_conceptos`  
                        SET `cuotas_pendientes`=:cuotas_pendientes, 
                        `asociado`=:asociado 
                        WHERE `unidad_vivienda_ms`=:unidad_vivienda_ms 
                        AND `fecha`<=:fecha AND `id_unico`!=:id_unico";
                    $sql_dato = array(
                        array(":cuotas_pendientes",0),
                        array(":asociado",$id_oc[0][0]),
                        array(":unidad_vivienda_ms",$id_uvms),
                        array(":fecha",$fecha),
                        array(":id_unico",$id_oc[0][0]),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                }                
            }
        }
        echo $rta;
    break;
    
     #* html Ver Facturas*#
    case 17:
        #* Buscar Valor Saldo cuotas 
        $id_uv      = $_REQUEST['id_uvs'];
        $id_cuota   = $_REQUEST['id_cuota'];
        $iduv       = $con->Listar("SELECT DISTINCT f.id_unico, f.periodo, 
            f.numero_factura, DATE_FORMAT(f.fecha_factura, '%d/%m/%Y')
            FROM gp_factura f 
            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
            LEFT JOIN gp_detalle_factura df ON f.id_unico = df.factura 
            WHERE uvms.unidad_vivienda_servicio =$id_uv AND df.otros_conceptos = $id_cuota 
            ORDER BY f.fecha_factura");
        $html       = "";
        
        
        $html .= '<div class="modal-header" id="forma-modal">';
        $html .= '<button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><span class="glyphicon glyphicon-remove"></span></button>';
        $html .= '<h4 class="modal-title" style="font-size: 24px; padding: 3px;">Ver Facturas Concepto</h4>';
        $html .= '</div>';
        $html .= '<div class="modal-body" style="margin-top: 8px">';
        $html .= '<table border="1" style="width:100%">'; 
        $html .= '<tr>';
        $html .= '<td style="text-align:center"><strong><i>Ver</i></strong></td>';
        $html .= '<td style="text-align:center"><strong><i>Número</i></strong></td>';
        $html .= '<td style="text-align:center"><strong><i>Fecha</i></strong></td>';
        $html .= '</tr>';
        $tc    = 0;
        for ($fc = 0;$fc < count($iduv);$fc++) {
            $html .= '<tr>';
            $html .= '<td align="center">';
            $html .= '<a onclick="javaScript:imprimirF('.$iduv[$fc][0].','.$iduv[$fc][1].')" title="Imprimir Factura"><li class="glyphicon glyphicon-print"></li></a>';
            $html .= '</td>';
            $html .= '<td>'.$iduv[$fc][2].'</td>';
            $html .= '<td>'.$iduv[$fc][3].'</td>';
            $html .= '</tr>';
        }
        $html .= "</table>";
        $html .= '</div>';
        $html .= '<div id="forma-modal" class="modal-footer">';
        $html .= '<button type="button" id="btnCerrar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>';
        $html .= '</div>';
        echo $html;
    break;
    #* html Abonar Cuota *#
    case 18:
        #* Buscar Valor Saldo cuotas 
        $id_cuota   = $_REQUEST['id'];
        $iduv       = $con->Listar("SELECT unidad_vivienda_ms, 
            valor_cuota, cuotas_pendientes, cuotas_pagas, concepto   
            FROM gf_otros_conceptos WHERE id_unico = $id_cuota");
        
        $html       = "";
        $html .= '<div class="modal-header" id="forma-modal">';
        $html .= '<button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><span class="glyphicon glyphicon-remove"></span></button>';
        $html .= '<h4 class="modal-title" style="font-size: 24px; padding: 3px;">Abonar Concepto</h4>';
        $html .= '</div>';
        $html .= '<form action="javaScript:AbonarConcepto()" method="post" class="form-horizontal" id="formAbnConcepto" enctype="multipart/form-data" style="font-size: 10px !important;">';
        $html .= '<div class="modal-body">';
        $html .= '<input type="hidden" name="iduvms" id="iduvms" value="'.$iduv[0][0].'">';
        $html .= '<input type="hidden" name="cuotas_p" id="cuotas_p" value="'.$iduv[0][2].'">';
        $html .= '<input type="hidden" name="cuotas_pg" id="cuotas_pg" value="'.$iduv[0][3].'">';
        $html .= '<input type="hidden" name="valor_c" id="valor_c" value="'.$iduv[0][1].'">';
        $html .= '<input type="hidden" name="concepto" id="concepto" value="'.$iduv[0][4].'">';
        $html .= '<div class="row">';
        $html .= '<input type="hidden" name="id_concepto" id="id_concepto" value="'.$id_cuota.'">';
        $html .= '<p align="center" style="margin-bottom: 15px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>';
        $html .= '<div class="form-group">';
        $html .= '<label for="txtfecha" class="col-sm-1 col-md-1 col-lg-1 control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>';
        $html .= '<div class="col-sm-2 col-md-2 col-lg-2">';
        $html .= '<input type="text" name="txtfecha" id="txtfecha" class="form-control" maxlength="100" title="Ingrese fecha"  placeholder="Fecha" required style="width: 100%; font-size: 10px !important;" tabindex="8" autocomplete="off" >';
        $html .= '</div>';
        $html .= '<label for="txttotalc_a" class="control-label col-sm-1 col-md-1 col-lg-1 text-right"><span class="obligado">*</span>N° Cuotas Abono:</label>';
        $html .= '<div class="col-sm-2 col-md-2 col-lg-2">';
        $html .= '<input type="number" name="txttotalc_a" id="txttotalc_a" min="1" max="'.$iduv[0][2].'" class="form-control" maxlength="100" title="Ingrese N° Cuotas Abono" placeholder="N° Cuotas Abono"  style="width: 100%;font-size: 10px !important;" tabindex="3" autocomplete="off" required="required" >';
        $html .= '</div>';
        $html .= '<label for="sltTipoP" class="col-sm-1 col-md-1 col-lg-1 control-label"><strong class="obligado">*</strong>Tipo Pago:</label>';
        $html .= '<div class="col-sm-2 col-md-2 col-lg-2">';
        $html .= '<select name="sltTipoP" id="sltTipoP" class="form-control select" title="Seleccione Tipo Pago" required tabindex="7">';
        $html .= '<option value="">Tipo Pago</option>';
        $rowti = $con->Listar("SELECT  tp.id_unico, tp.nombre 
                    FROM gp_tipo_pago tp 
                    WHERE tp.compania ='".$compania."' AND (financiacion != 1  OR financiacion IS NULL) ");
        for ($ti = 0;$ti < count($rowti);$ti++) {
        $html .= '<option value="'.$rowti[$ti][0].'">'.ucwords($rowti[$ti][1]).'</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '<label for="sltBanco" class="col-sm-1 col-md-1 col-lg-1 control-label"><strong class="obligado">*</strong>Banco:</label>';
        $html .= '<div class="col-sm-2 col-md-2 col-lg-2">';
        $html .= '<select name="sltBanco" id="sltBanco" class="form-control select" title="Seleccione Banco" required tabindex="7">';
        $html .= '<option value="">Banco</option>';
        $rowti = $con->Listar("SELECT  ctb.id_unico,
                        CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')'),
                        c.id_unico 
                    FROM gf_cuenta_bancaria ctb
                    LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria 
                    LEFT JOIN gf_cuenta c ON ctb.cuenta = c.id_unico 
                    WHERE ctbt.tercero ='".$compania."' AND ctb.parametrizacionanno = $panno AND c.id_unico IS NOT NULL ORDER BY ctb.numerocuenta");
        for ($ti = 0;$ti < count($rowti);$ti++) {
        $html .= '<option value="'.$rowti[$ti][0].'">'.ucwords($rowti[$ti][1]).'</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="modal-footer" id="forma-modal">';
        $html .= '<div class="row">';
        $html .= '<div class="form-group">';
        $html .= '<label for="no" class="col-sm-11 col-md-11 col-lg-11 control-label"></label>';
        $html .= '<div class="col-sm-1 col-md-1 col-lg-1 text-right" style="margin-left:-20px">';
        $html .= '<button type="submit" class="btn btn-default" id="btnModalGuardarT"><span class="glyphicon glyphicon-floppy-disk"></span></button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</form>';
        echo $html;
    break;
    
    case 19:
        $id_concepto = $_REQUEST['id_concepto'];
        $fecha       = fechaC($_REQUEST['txtfecha']);
        $n_cuotas    = $_REQUEST['txttotalc_a'];
        $banco       = $_REQUEST['sltBanco'];
        $valor_cuota = $_REQUEST['valor_c'];
        $cuotas_pen  = $_REQUEST['cuotas_p'];
        $cuotas_pg   = $_REQUEST['cuotas_pg'];
        $id_uvms     = $_REQUEST['iduvms'];
        $tercero     = $_REQUEST['tercero'];
        $concepto    = $_REQUEST['concepto'];
        $tipoPago    = $_REQUEST['sltTipoP'];
        
        
        #*** Buscar Tipo Factura ***#
        $tf = $con->Listar("SELECT * FROM gp_tipo_factura WHERE servicio = 1");
        if(count($tf)>0){
            $tipo_factura = $tf[0][0];
            $numero     = numeroFactura($tipo_factura,$panno);
            $descripcion= 'Factura Unidad Vivienda Abono Cuota';
                    
            $sql_cons ="INSERT INTO `gp_factura` 
                    ( `numero_factura`, `tercero`, `tipofactura`,
                `unidad_vivienda_servicio`,`fecha_factura`,
                `fecha_vencimiento`,`descripcion`,
                `parametrizacionanno`,`estado_factura`,`centrocosto`) 
            VALUES  (:numero_factura,  :tercero, :tipofactura, 
                :unidad_vivienda_servicio,:fecha_factura,
                :fecha_vencimiento,:descripcion,
                :parametrizacionanno,:estado_factura,:centrocosto)";
            $sql_dato = array(
                    array(":numero_factura",$numero),
                    array(":tercero",$tercero),
                    array(":tipofactura",$tipo_factura),
                    array(":unidad_vivienda_servicio",$id_uvms),
                    array(":fecha_factura",$fecha),
                    array(":fecha_vencimiento",$fecha),
                    array(":descripcion",$descripcion),
                    array(":parametrizacionanno",$panno),
                    array(":estado_factura",4),
                    array(":centrocosto",$centroc),
            );
            $resp       = $con->InAcEl($sql_cons,$sql_dato);
            $fi         = $con->Listar("SELECT MAX(id_unico) FROM gp_factura 
                WHERE numero_factura = '$numero' AND tipofactura = $tipo_factura");
            $id_factura = $fi[0][0];
            
            for ($nc = 0; $nc < ($n_cuotas); $nc++) {
                #* Insertar Detalle
                $sql_cons ="INSERT INTO `gp_detalle_factura` 
                        ( `factura`, `concepto_tarifa`, `valor`,
                    `cantidad`,`iva`,`impoconsumo`,
                    `ajuste_peso`,`valor_total_ajustado`, `otros_conceptos`) 
                VALUES  (:factura,  :concepto_tarifa, :valor, 
                    :cantidad,:iva,:impoconsumo,
                    :ajuste_peso,:valor_total_ajustado, :otros_conceptos)";
                $sql_dato = array(
                        array(":factura",$id_factura),
                        array(":concepto_tarifa",$concepto),
                        array(":valor",$valor_cuota),
                        array(":cantidad",1),
                        array(":iva",0),
                        array(":impoconsumo",0),
                        array(":ajuste_peso",0),
                        array(":valor_total_ajustado",$valor_cuota),
                        array(":otros_conceptos",$id_concepto),
                );
                $resp       = $con->InAcEl($sql_cons,$sql_dato);   
                var_dump($resp);
            }
        }
        reconstruirComprobantesFactura($id_factura);
        #Buscar Datos Factura
        $df = $con->Listar("SELECT f.id_unico,
                        f.numero_factura, tp.nombre,
                        f.tercero, f.descripcion, f.fecha_factura, f.centrocosto
                    FROM gp_factura f LEFT JOIN gp_tipo_factura tp ON tp.id_unico = f.tipofactura
                    WHERE f.id_unico = $id_factura");
        $fecha          = $df[0][5];
        $responsable    = $df[0][3];
        $centrocosto    = $df[0][6];
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
                estado, parametrizacionanno)
                VALUES('$numeroPago',
                $tipoPago,$responsable,
                '$fecha',$banco,$estado, $panno)";
        $resultadoP = $mysqli->query($sql);

        if($resultadoP==true){
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
        }
        $dp = guardarPagoFactura('',$id_factura,$pago, ($n_cuotas*$valor_cuota));
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
        }
        
        $sql_cons = "UPDATE `gf_otros_conceptos`  
            SET `cuotas_pendientes`=:cuotas_pendientes, 
            `cuotas_pagas` =:cuotas_pagas
            WHERE `id_unico`=:id_unico ";
        $sql_dato = array(
            array(":cuotas_pendientes",$cuotas_pen-$n_cuotas),
            array(":cuotas_pagas",$cuotas_pg+$n_cuotas),
            array(":id_unico",$id_concepto),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $rta = 1;
        } else {
            $rta = 0;
        }
    break;
}