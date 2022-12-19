<?php 
#######################################################################################################
#                           Modificaciones
#######################################################################################################
#13/10/2017 |Erica G. | Archivo Creado
#######################################################################################}
@session_start();
#***********Eliminar retencion*************#
function eliminarRetencion($id_comprobante)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        $obs     =  'Eliminados en cascada';
        $datoA   =  'NA';
        $table   = 'gf_retencion';
        
        if(!empty($id_comprobante)){ 
            $ret = "SELECT * FROM gf_retencion WHERE comprobante = $id_comprobante";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $row[0];
                #*******************************#
                $campo = 'id_unico';
                $valor = $row[0];
                $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                $insert = $GLOBALS['mysqli']->query($insert);
                if(!empty($row[1])){ 
                    $campo = 'valorretencion';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[2])){ 
                    $campo = 'retencionbase';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[3])){ 
                    $campo = 'porcentajeretencion';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[4])){ 
                    $campo = 'comprobanteretencion';
                    $valor = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[5])){ 
                    $campo = 'cuentadescuentoretencion';
                    $valor = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[6])){ 
                    $campo = 'comprobante';
                    $valor = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[7])){ 
                    $campo = 'tiporetencion';
                    $valor = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                
                
            }
        }
        }
    return (true);    
}
#***********Eliminar detalle movimiento *************#
function eliminardetmov($tipo, $id_detalle){
    $anno    = $_SESSION['anno'];
    $fecha   =  date('Y-m-d');
    $equipo  =  gethostname();
    $usuario =  $_SESSION['id_usuario'];
    $ip      =  $_SERVER['REMOTE_ADDR'];
    $accion  =  'Eliminar';
    $obs     =  'Eliminados en cascada';
    $datoA   =  'NA';
    $table   = 'gf_detalle_comprobante_mov';
    if(!empty($id_detalle)){
        if($tipo =='cnt'){
            $ret = "SELECT * FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $id_detalle";
        }elseif($tipo=='pptal'){
            $ret = "SELECT * FROM gf_detalle_comprobante_mov WHERE comprobantepptal = $id_detalle";
        }
        $cr = $GLOBALS['mysqli']->query($ret);
        if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $row[0];
                #*******************************#
                $campo = 'id_unico';
                $valor = $row[0];
                $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                $insert = $GLOBALS['mysqli']->query($insert);
                if(!empty($row[1])){ 
                    $campo = 'numero';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[2])){ 
                    $campo = 'fechavencimiento';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[3])){ 
                    $campo = 'valor';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[4])){ 
                    $campo = 'pagadobanco';
                    $valor = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[5])){ 
                    $campo = 'periodocon';
                    $valor = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[6])){ 
                    $campo = 'tipodocumento';
                    $valor = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[7])){ 
                    $campo = 'comprobantecnt';
                    $valor = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[8])){ 
                    $campo = 'comprobantepptal';
                    $valor = $row[8];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[9])){ 
                    $campo = 'ruta';
                    $valor = $row[9];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }


            }
        }
    }
}
#***********Eliminar detalle cnt x id comprobante*********#
function eliminardetcnt($id_comprobante)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        $obs     =  'Eliminados en cascada';
        $datoA   =  'NA';
        $table   = 'gf_detalle_comprobante';
        
        if(!empty($id_comprobante)){
            $ret = "SELECT * FROM gf_detalle_comprobante WHERE comprobante = $id_comprobante";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $row[0];
                #*******************************#
                $campo = 'id_unico';
                $valor = $row[0];
                $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                $insert = $GLOBALS['mysqli']->query($insert);
                if(!empty($row[1])){ 
                    $campo = 'fecha';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[2])){ 
                    $campo = 'descripcion';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[3])){ 
                    $campo = 'valor';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[4])){ 
                    $campo = 'valorejecucion';
                    $valor = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[5])){ 
                    $campo = 'comprobante';
                    $valor = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[6])){ 
                    $campo = 'cuenta';
                    $valor = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[7])){ 
                    $campo = 'naturaleza';
                    $valor = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[8])){ 
                    $campo = 'tercero';
                    $valor = $row[8];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[9])){ 
                    $campo = 'proyecto';
                    $valor = $row[9];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[10])){ 
                    $campo = 'centrocosto';
                    $valor = $row[10];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[11])){ 
                    $campo = 'detalleafectado';
                    $valor = $row[11];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[12])){ 
                    $campo = 'detallecomprobantepptal';
                    $valor = $row[12];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[13])){ 
                    $campo = 'revelacion';
                    $valor = $row[13];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[14])){ 
                    $campo = 'conciliado';
                    $valor = $row[14];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[15])){ 
                    $campo = 'periodo_conciliado';
                    $valor = $row[15];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                
                
            }
        }
        }
    return (true);    
}
#***********Eliminar comprobante cnt************#
function eliminarcnt($id_comprobante)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        $obs     =  'Eliminados en cascada';
        $datoA   =  'NA';
        $table   = 'gf_comprobante_cnt';
        
        if(!empty($id_comprobante)){
            $ret = "SELECT * FROM gf_comprobante_cnt WHERE id_unico = $id_comprobante";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $row[0];
                #*******************************#
                $campo = 'id_unico';
                $valor = $row[0];
                $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                $insert = $GLOBALS['mysqli']->query($insert);
                if(!empty($row[1])){ 
                    $campo = 'numero';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[2])){ 
                    $campo = 'fecha';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[3])){ 
                    $campo = 'descripcion';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[4])){ 
                    $campo = 'valorbase';
                    $valor = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[5])){ 
                    $campo = 'valorbaseiva';
                    $valor = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[6])){ 
                    $campo = 'valorneto';
                    $valor = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[7])){ 
                    $campo = 'numerocontrato';
                    $valor = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[8])){ 
                    $campo = 'tipocomprobante';
                    $valor = $row[8];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[9])){ 
                    $campo = 'compania';
                    $valor = $row[9];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[10])){ 
                    $campo = 'parametrizacionanno';
                    $valor = $row[10];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[11])){ 
                    $campo = 'tercero';
                    $valor = $row[11];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[12])){ 
                    $campo = 'estado';
                    $valor = $row[12];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[13])){ 
                    $campo = 'clasecontrato';
                    $valor = $row[13];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[14])){ 
                    $campo = 'formapago';
                    $valor = $row[14];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[15])){ 
                    $campo = 'usuario';
                    $valor = $row[15];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[16])){ 
                    $campo = 'fecha_elaboracion';
                    $valor = $row[16];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                
                
            }
        }
        }
    return (true);    
}
#***********Eliminar detalle pptal x id comprobante*********#
function eliminardetpptal($id_comprobante)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        $obs     =  'Eliminados en cascada';
        $datoA   =  'NA';
        $table   = 'gf_detalle_comprobante_pptal';
        
        if(!empty($id_comprobante)){
            $ret = "SELECT * FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_comprobante";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $row[0];
                #*******************************#
                $campo = 'id_unico';
                $valor = $row[0];
                $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                $insert = $GLOBALS['mysqli']->query($insert);
                if(!empty($row[1])){ 
                    $campo = 'descripcion';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[2])){ 
                    $campo = 'valor';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[3])){ 
                    $campo = 'comprobantepptal';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[4])){ 
                    $campo = 'rubrofuente';
                    $valor = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[5])){ 
                    $campo = 'conceptoRubro';
                    $valor = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[6])){ 
                    $campo = 'tercero';
                    $valor = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[7])){ 
                    $campo = 'proyecto';
                    $valor = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[8])){ 
                    $campo = 'comprobanteafectado';
                    $valor = $row[8];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[9])){ 
                    $campo = 'saldo_disponible';
                    $valor = $row[9];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[10])){ 
                    $campo = 'clase_nom';
                    $valor = $row[10];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                
                
            }
        }
        }
    return (true);    
}
#***********Eliminar comprobante cnt************#
function eliminarpptal($id_comprobante)
{ 
    
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        $obs     =  'Eliminados en cascada';
        $datoA   =  'NA';
        $table   = 'gf_comprobante_pptal';
        
        if(!empty($id_comprobante)){
        $ret = "SELECT * FROM gf_comprobante_pptal WHERE id_unico = $id_comprobante";
        $cr = $GLOBALS['mysqli']->query($ret);
        if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $row[0];
                #*******************************#
                $campo = 'id_unico';
                $valor = $row[0];
                $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                $insert = $GLOBALS['mysqli']->query($insert);
                if(!empty($row[1])){ 
                    $campo = 'numero';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[2])){ 
                    $campo = 'fecha';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[3])){ 
                    $campo = 'fecha_vencimiento';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[4])){ 
                    $campo = 'descripcion';
                    $valor = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[5])){ 
                    $campo = 'numerocontrato';
                    $valor = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[6])){ 
                    $campo = 'parametrizacionanno';
                    $valor = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[7])){ 
                    $campo = 'clasecontrato';
                    $valor = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[8])){ 
                    $campo = 'tipocomprobante';
                    $valor = $row[8];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[9])){ 
                    $campo = 'tercero';
                    $valor = $row[9];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[10])){ 
                    $campo = 'estado';
                    $valor = $row[10];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[11])){ 
                    $campo = 'responsable';
                    $valor = $row[11];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[12])){ 
                    $campo = 'compania';
                    $valor = $row[12];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[13])){ 
                    $campo = 'usuario';
                    $valor = $row[13];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[14])){ 
                    $campo = 'fecha_elaboracion';
                    $valor = $row[14];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                
                
            }
        }
        }
    return (true);    
}

#***********Eliminar detalle cnt x id comprobante*********#
function UpdatecntAlmacen($id_comprobante)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Actualizar';
        $obs     =  'Eliminados en cascada';
        $datoA   =  'NULL';
        $table   = 'gf_movimiento';
        $campo   = 'afectado_contabilidad';
        $valor   = $id_comprobante;
        if(!empty($id_comprobante)){
            $ret = "SELECT id_unico, afectado_contabilidad FROM gf_movimiento WHERE afectado_contabilidad = $id_comprobante";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
                while ($row = mysqli_fetch_row($cr)){
                    $id_campo   = $row[0];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                            id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                            direccionip, sistema, usuario, observacion ) 
                            VALUES ('$table', '$campo', 
                            '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                            '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
            }
        }
    return (true);    
}

