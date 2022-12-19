<?php 
#######################################################################################################
#                           Modificaciones
#######################################################################################################
#18/04/2017 |ELkin O. | Archivo Creado
#######################################################################################
#***********Agregar Movimiento*************#
function agregarMovimiento1($id_mov)
{    require ('../Conexion/conexion.php');
        session_start();
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $table   = 'gf_movimiento';
        $valor   =  'NA';
        
        if(!empty($id_mov)){ 
            $ret = "SELECT * FROM gf_movimiento WHERE id_unico = $id_mov";
            $cr  = $mysqli->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $row[0];
                #*******************************#
                $campo = 'id_unico';
                $datoA = $row[0];
                $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                $insert = $mysqli->query($insert);
                if(!empty($row[1])){ 
                    $campo = 'numero';
                    $datoA = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[2])){ 
                    $campo = 'fecha';
                    $datoA = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[3])){ 
                    $campo = 'descripcion';
                    $datoA = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[4])){ 
                    $campo = 'porcivaglobal';
                    $datoA = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[5])){ 
                    $campo = 'plazoentrega';
                    $datoA = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[6])){ 
                    $campo = 'observaciones';
                    $datoA = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[7])){ 
                    $campo = 'tipomovimiento';
                    $datoA = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }

                if(!empty($row[8])){ 
                    $campo = 'parametrizacionanno';
                    $datoA = $row[8];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[9])){ 
                    $campo = 'compania';
                    $datoA = $row[9];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[10])){ 
                    $campo = 'tercero';
                    $datoA = $row[10];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[11])){ 
                    $campo = 'tercero2';
                    $datoA = $row[11];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[12])){ 
                    $campo = 'dependencia';
                    $datoA = $row[12];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[13])){ 
                    $campo = 'centrocosto';
                    $datoA = $row[13];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[14])){ 
                    $campo = 'rubropptal';
                    $datoA = $row[14];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[15])){ 
                    $campo = 'proyecto';
                    $datoA = $row[15];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[16])){ 
                    $campo = 'formapa';
                    $datoA = $row[16];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[17])){ 
                    $campo = 'lugarentrega';
                    $datoA = $row[17];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[18])){ 
                    $campo = 'unidadentrega';
                    $datoA = $row[18];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[19])){ 
                    $campo = 'estado';
                    $datoA = $row[19];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[20])){ 
                    $campo = 'tipo_doc_sop';
                    $datoA = $row[20];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[21])){ 
                    $campo = 'numero_doc_sop';
                    $datoA = $row[21];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[22])){ 
                    $campo = 'afectado_contabilidad';
                    $datoA = $row[22];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[23])){ 
                    $campo = 'descuento';
                    $datoA = $row[23];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[24])){ 
                    $campo = 'fecha_hora';
                    $datoA = $row[24];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[25])){ 
                    $campo = 'factura';
                    $datoA = $row[25];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[26])){ 
                    $campo = 'fuente';
                    $datoA = $row[26];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[27])){ 
                    $campo = 'objeto';
                    $datoA = $row[27];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[28])){ 
                    $campo = 'forma_pago';
                    $datoA = $row[28];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[29])){ 
                    $campo = 'valor_contrato';
                    $datoA = $row[29];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[30])){ 
                    $campo = 'clausulas';
                    $datoA = $row[30];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[31])){ 
                    $campo = 'fecha_terminacion';
                    $datoA = $row[31];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[32])){ 
                    $campo = 'numero_actas';
                    $datoA = $row[32];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[33])){ 
                    $campo = 'forma_contratacion';
                    $datoA = $row[33];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[34])){ 
                    $campo = 'id_proceso';
                    $datoA = $row[34];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                
                
            }
        }
        }
    return (true);    
}
function agregarMovimiento2($id_mov)
{    require ('../Conexion/conexion.php');
        session_start();
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $table   = 'gf_movimiento';
        $valor   =  'NA';
        
        if(!empty($id_mov)){ 
            $ret = "SELECT * FROM gf_movimiento WHERE id_unico = $id_mov";
            $cr  = $mysqli->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $row[0];
                #*******************************#
                $campo = 'id_unico';
                $datoA = $row[0];
                $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                $insert = $mysqli->query($insert);
                if(!empty($row[1])){ 
                    $campo = 'numero';
                    $datoA = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[2])){ 
                    $campo = 'fecha';
                    $datoA = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[3])){ 
                    $campo = 'descripcion';
                    $datoA = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[4])){ 
                    $campo = 'porcivaglobal';
                    $datoA = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[5])){ 
                    $campo = 'plazoentrega';
                    $datoA = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[6])){ 
                    $campo = 'observaciones';
                    $datoA = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[7])){ 
                    $campo = 'tipomovimiento';
                    $datoA = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }

                if(!empty($row[8])){ 
                    $campo = 'parametrizacionanno';
                    $datoA = $row[8];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[9])){ 
                    $campo = 'compania';
                    $datoA = $row[9];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[10])){ 
                    $campo = 'tercero';
                    $datoA = $row[10];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[11])){ 
                    $campo = 'tercero2';
                    $datoA = $row[11];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[12])){ 
                    $campo = 'dependencia';
                    $datoA = $row[12];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[13])){ 
                    $campo = 'centrocosto';
                    $datoA = $row[13];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[14])){ 
                    $campo = 'rubropptal';
                    $datoA = $row[14];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[15])){ 
                    $campo = 'proyecto';
                    $datoA = $row[15];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[16])){ 
                    $campo = 'formapa';
                    $datoA = $row[16];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[17])){ 
                    $campo = 'lugarentrega';
                    $datoA = $row[17];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[18])){ 
                    $campo = 'unidadentrega';
                    $datoA = $row[18];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[19])){ 
                    $campo = 'estado';
                    $datoA = $row[19];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[20])){ 
                    $campo = 'tipo_doc_sop';
                    $datoA = $row[20];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[21])){ 
                    $campo = 'numero_doc_sop';
                    $datoA = $row[21];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[22])){ 
                    $campo = 'afectado_contabilidad';
                    $datoA = $row[22];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[23])){ 
                    $campo = 'descuento';
                    $datoA = $row[23];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[24])){ 
                    $campo = 'fecha_hora';
                    $datoA = $row[24];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[25])){ 
                    $campo = 'factura';
                    $datoA = $row[25];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[26])){ 
                    $campo = 'fuente';
                    $datoA = $row[26];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[27])){ 
                    $campo = 'objeto';
                    $datoA = $row[27];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[28])){ 
                    $campo = 'forma_pago';
                    $datoA = $row[28];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[29])){ 
                    $campo = 'valor_contrato';
                    $datoA = $row[29];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[30])){ 
                    $campo = 'clausulas';
                    $datoA = $row[30];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[31])){ 
                    $campo = 'fecha_terminacion';
                    $datoA = $row[31];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[32])){ 
                    $campo = 'numero_actas';
                    $datoA = $row[32];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[33])){ 
                    $campo = 'forma_contratacion';
                    $datoA = $row[33];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[34])){ 
                    $campo = 'id_proceso';
                    $datoA = $row[34];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                
                
            }
        }
        }
    return (true);    
}
function agregarMovimiento($id_mov)
{   
        session_start();
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $table   = 'gf_movimiento';
        $valor   =  'NA';
        
        if(!empty($id_mov)){ 
            $ret = "SELECT * FROM gf_movimiento WHERE id_unico = $id_mov";
            $cr  = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $row[0];
                #*******************************#
                $campo = 'id_unico';
                $datoA = $row[0];
                $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                $insert = $GLOBALS['mysqli']->query($insert);
                if(!empty($row[1])){ 
                    $campo = 'numero';
                    $datoA = $row[1];
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
                    $datoA = $row[2];
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
                    $datoA = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[4])){ 
                    $campo = 'porcivaglobal';
                    $datoA = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[5])){ 
                    $campo = 'plazoentrega';
                    $datoA = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[6])){ 
                    $campo = 'observaciones';
                    $datoA = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[7])){ 
                    $campo = 'tipomovimiento';
                    $datoA = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[8])){ 
                    $campo = 'parametrizacionanno';
                    $datoA = $row[8];
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
                    $datoA = $row[9];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[10])){ 
                    $campo = 'tercero';
                    $datoA = $row[10];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[11])){ 
                    $campo = 'tercero2';
                    $datoA = $row[11];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[12])){ 
                    $campo = 'dependencia';
                    $datoA = $row[12];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[13])){ 
                    $campo = 'centrocosto';
                    $datoA = $row[13];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[14])){ 
                    $campo = 'rubropptal';
                    $datoA = $row[14];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[15])){ 
                    $campo = 'proyecto';
                    $datoA = $row[15];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[16])){ 
                    $campo = 'formapa';
                    $datoA = $row[16];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[17])){ 
                    $campo = 'lugarentrega';
                    $datoA = $row[17];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[18])){ 
                    $campo = 'unidadentrega';
                    $datoA = $row[18];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[19])){ 
                    $campo = 'estado';
                    $datoA = $row[19];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[20])){ 
                    $campo = 'tipo_doc_sop';
                    $datoA = $row[20];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[21])){ 
                    $campo = 'numero_doc_sop';
                    $datoA = $row[21];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[22])){ 
                    $campo = 'afectado_contabilidad';
                    $datoA = $row[22];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[23])){ 
                    $campo = 'descuento';
                    $datoA = $row[23];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[24])){ 
                    $campo = 'fecha_hora';
                    $datoA = $row[24];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[25])){ 
                    $campo = 'factura';
                    $datoA = $row[25];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[26])){ 
                    $campo = 'fuente';
                    $datoA = $row[26];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[27])){ 
                    $campo = 'objeto';
                    $datoA = $row[27];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[28])){ 
                    $campo = 'forma_pago';
                    $datoA = $row[28];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[29])){ 
                    $campo = 'valor_contrato';
                    $datoA = $row[29];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[30])){ 
                    $campo = 'clausulas';
                    $datoA = $row[30];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[31])){ 
                    $campo = 'fecha_terminacion';
                    $datoA = $row[31];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[32])){ 
                    $campo = 'numero_actas';
                    $datoA = $row[32];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[33])){ 
                    $campo = 'forma_contratacion';
                    $datoA = $row[33];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[34])){ 
                    $campo = 'id_proceso';
                    $datoA = $row[34];
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

function agregarDetalleMovimiento($id_mov)
{  
        session_start();
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $table   = 'gf_detalle_movimiento';
        $valor   =  'NA';
        
        if(!empty($id_mov)){ 
            $ret = "SELECT * FROM gf_detalle_movimiento WHERE id_unico = $id_mov";
            $cr  = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $row[0];
                #*******************************#
                $campo = 'id_unico';
                $datoA = $row[0];
                $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                $insert = $GLOBALS['mysqli']->query($insert);
                if(!empty($row[1])){ 
                    $campo = 'cantidad';
                    $datoA = $row[1];
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
                    $datoA = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[3])){ 
                    $campo = 'iva';
                    $datoA = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[4])){ 
                    $campo = 'porcentajeneto';
                    $datoA = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[5])){ 
                    $campo = 'porcentajeiva';
                    $datoA = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[6])){ 
                    $campo = 'hora';
                    $datoA = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[7])){ 
                    $campo = 'movimiento';
                    $datoA = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[8])){ 
                    $campo = 'detalleasociado';
                    $datoA = $row[8];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[9])){ 
                    $campo = 'planmovimiento';
                    $datoA = $row[9];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[10])){ 
                    $campo = 'ajuste';
                    $datoA = $row[10];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[11])){ 
                    $campo = 'cantidad_origen';
                    $datoA = $row[11];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[12])){ 
                    $campo = 'unidad_origen ';
                    $datoA = $row[12];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[13])){ 
                    $campo = 'valor_origen';
                    $datoA = $row[13];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[14])){ 
                    $campo = 'xvalor_t';
                    $datoA = $row[14];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[15])){ 
                    $campo = 'descuento';
                    $datoA = $row[15];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[16])){ 
                    $campo = 'id_factura';
                    $datoA = $row[16];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[17])){ 
                    $campo = 'n_registro';
                    $datoA = $row[17];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[18])){ 
                    $campo = 'observaciones';
                    $datoA = $row[18];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[19])){ 
                    $campo = 'impoconsumo';
                    $datoA = $row[19];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[20])){ 
                    $campo = 'detalle_comprobante ';
                    $datoA = $row[20];
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


function agregarMovimientoProducto($producto,$detalleMov)
{  
        session_start();
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $table   = 'gf_movimiento_producto';
        $valor   =  'NA';
        $id_campo=  'NA';

        if(!empty($producto) ||!empty($detalleMov) ){ 
            $ret = "SELECT * FROM gf_movimiento_producto WHERE producto=$producto AND detallemovimiento=$detalleMov";
            $cr  = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                if(!empty($row[0])){ 
                    $campo = 'producto';
                    $datoA = $row[0];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[1])){ 
                    $campo = 'detallemovimiento';
                    $datoA = $row[1];
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

function agregarDepreciacion($indicador){  
        session_start();
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $table   = 'ga_depreciacion';
        $valor   =  'NA';
        $id_campo=  'NA';
        if(!empty($indicador)){ 
            $ret = "SELECT MAX(id_unico) FROM ga_depreciacion";
            $cr  = $GLOBALS['mysqli']->query($ret);
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $row[0];
                #*******************************#
                $campo = 'id_unico';
                $datoA = $row[0];
                $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                $insert = $GLOBALS['mysqli']->query($insert);
                if(!empty($row[1])){ 
                    $campo = 'producto';
                    $datoA = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[2])){ 
                    $campo = 'fecha_dep';
                    $datoA = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }  
                if(!empty($row[3])){ 
                    $campo = 'dias_dep';
                    $datoA = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                } 
                if(!empty($row[4])){ 
                    $campo = 'valor';
                    $datoA = $row[4];
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



 function modificarMovimiento($id_unico, $fechaM, $observaciones, $descripcion, $iva, 
 $responsable, $tercero, $fuente, $proyecto, $lugarE, $unidadPE, $plazoE, $centrocosto,
 $rubroP,$dependencia,$tipo_doc_sopt , $num_doc)
{ 
    require ('../Conexion/conexion.php');
    $sqlComp="SELECT fecha,observaciones,descripcion,porcivaglobal,tercero,tercero2,fuente, 
     proyecto,lugarentrega,unidadentrega ,plazoentrega,centrocosto,rubropptal,dependencia,tipo_doc_sop,
     numero_doc_sop FROM gf_movimiento WHERE id_unico = $id_unico";
    $crComp = $mysqli->query($sqlComp);
    $rowComp = mysqli_fetch_row($crComp);

        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Actualizar';
        $obs     =  'Actualizado C/U';
        $table   = 'gf_movimiento';
        
        if(!empty($id_unico)){ 
            $ret = "SELECT fecha,observaciones,descripcion,porcivaglobal,tercero,tercero2,fuente, 
            proyecto,lugarentrega,unidadentrega ,plazoentrega,centrocosto,rubropptal,dependencia,tipo_doc_sop,
            numero_doc_sop FROM gf_movimiento WHERE id_unico = $id_unico";
            $cr = $mysqli->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $id_unico;

                if( $rowComp[0]!=$fechaM){ 
                    $datoA   = $fechaM;
                    $campo = 'fecha';
                    $valor = $row[0];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if($rowComp[1]!=$observaciones){ 
                    $datoA   = $observaciones;
                    $campo = 'observaciones';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if($rowComp[2]!=$descripcion){
                    $datoA   = $descripcion;
                    $campo = 'descripcion';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if($rowComp[3]!=$iva){
                    $datoA   = $iva;
                    $campo = 'porcivaglobal';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if($rowComp[4]!=$responsable){
                    $datoA   = $responsable;
                    $campo = 'tercero';
                    $valor = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if($rowComp[5]!=$tercero){
                    $datoA   = $tercero;
                    $campo = 'tercero2';
                    $valor = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if($rowComp[6]!=$fuente){
                    $datoA   = $fuente;
                    $campo = 'fuente';
                    $valor = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }

                if($rowComp[7]!=$proyecto){
                    $datoA   = $proyecto;
                    $campo = 'proyecto';
                    $valor = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }

                if($rowComp[8]!=$lugarE){
                    $datoA   = $lugarE;
                    $campo = 'lugarentrega';
                    $valor = $row[8];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                
                if($rowComp[9]!=$unidadPE){
                    $datoA   = $unidadPE;
                    $campo = 'unidadentrega';
                    $valor = $row[9];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }

                if($rowComp[10]!=$plazoE){
                    $datoA   = $plazoE;
                    $campo = 'plazoentrega';
                    $valor = $row[10];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }

                if($rowComp[11]!=$centrocosto){
                    $datoA   = $centrocosto;
                    $campo = 'centrocosto';
                    $valor = $row[11];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }

                if($rowComp[12]!=$rubroP){
                    $datoA   = $rubroP;
                    $campo = 'rubropptal';
                    $valor = $row[12];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }

                if($rowComp[13]!=$dependencia){
                    $datoA   = $dependencia;
                    $campo = 'dependencia';
                    $valor = $row[13];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }

                if($rowComp[14]!=$tipo_doc_sopt){
                    $datoA   = $tipo_doc_sopt;
                    $campo = 'tipo_doc_sop';
                    $valor = $row[14];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }

                if($rowComp[15]!=$num_doc){
                    $datoA   = $num_doc;
                    $campo = 'numero_doc_sop';
                    $valor = $row[15];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                
                
            }
        }
        }
    return (true);    
}

function modificarMovimientoEn($id_unico, $fechaM, $observaciones, 
$descripcion, $iva, $responsable, $tercero, $fuente, $proyecto)
{ 
    require ('../Conexion/conexion.php');
    $sqlComp="SELECT fecha,observaciones,descripcion,porcivaglobal,tercero,tercero2,fuente, 
     proyecto FROM gf_movimiento WHERE id_unico = $id_unico";
    $crComp = $GLOBALS['mysqli']->query($sqlComp);
    $rowComp = mysqli_fetch_row($crComp);

        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Actualizar';
        $obs     =  'Actualizado C/U';
        $table   = 'gf_movimiento';
        
        if(!empty($id_unico)){ 
            $ret = "SELECT fecha,observaciones,descripcion,porcivaglobal,tercero,tercero2,fuente, 
            proyecto FROM gf_movimiento WHERE id_unico = $id_unico";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $id_unico;

                if( $rowComp[0]!=$fechaM){ 
                    $datoA   = $fechaM;
                    $campo = 'fecha';
                    $valor = $row[0];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[1]!=$observaciones){ 
                    $datoA   = $observaciones;
                    $campo = 'observaciones';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[2]!=$descripcion){
                    $datoA   = $descripcion;
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
                if($rowComp[3]!=$iva){
                    $datoA   = $iva;
                    $campo = 'porcivaglobal';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[4]!=$responsable){
                    $datoA   = $responsable;
                    $campo = 'tercero';
                    $valor = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[5]!=$tercero){
                    $datoA   = $tercero;
                    $campo = 'tercero2';
                    $valor = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[6]!=$fuente){
                    $datoA   = $fuente;
                    $campo = 'fuente';
                    $valor = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[7]!=$proyecto){
                    $datoA   = $proyecto;
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
                
            }
        }
        }
    return (true);    
}

function modificarDetalleMov($valor, $cantidad, $valorIva, $id_unico)
{ 
    require ('../Conexion/conexion.php');
    $sqlComp="SELECT valor,cantidad,iva FROM gf_detalle_movimiento WHERE id_unico = $id_unico";
    $crComp = $mysqli->query($sqlComp);
    $rowComp = mysqli_fetch_row($crComp);

        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Actualizar';
        $obs     =  'Actualizado C/U';
        $table   = 'gf_detalle_movimiento';
        
        if(!empty($id_unico)){ 
            $ret = "SELECT valor,cantidad,iva FROM gf_detalle_movimiento WHERE id_unico = $id_unico";
            $cr = $mysqli->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $id_unico;

                if( $rowComp[0]!=$valor){ 
                    $datoA   = $valor;
                    $campo = 'valor';
                    $valor = $row[0];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if($rowComp[1]!=$cantidad){ 
                    $datoA   = $cantidad;
                    $campo = 'cantidad';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if($rowComp[2]!=$valorIva){
                    $datoA   = $valorIva;
                    $campo = 'iva';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }                
            }
        }
        }
    return (true);    
}

function eliminarDetallesMovimiento($id_mov)
{  
        session_start();
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        $obs     =  'Eliminar C/U';
        $table   = 'gf_detalle_movimiento';
        $datoA   =  'NA';
        
        if(!empty($id_mov)){ 
            $ret = "SELECT * FROM gf_detalle_movimiento WHERE id_unico = $id_mov";
            $cr  = $GLOBALS['mysqli']->query($ret);
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
                    $campo = 'cantidad';
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
                    $campo = 'iva';
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
                    $campo = 'porcentajeneto';
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
                    $campo = 'porcentajeiva';
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
                    $campo = 'hora';
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
                    $campo = 'movimiento';
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
                    $campo = 'detalleasociado';
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
                    $campo = 'planmovimiento';
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
                    $campo = 'ajuste';
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
                    $campo = 'cantidad_origen';
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
                    $campo = 'unidad_origen ';
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
                    $campo = 'valor_origen';
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
                    $campo = 'xvalor_t';
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
                    $campo = 'descuento';
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
                    $campo = 'id_factura';
                    $valor = $row[16];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[17])){ 
                    $campo = 'n_registro';
                    $valor = $row[17];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[18])){ 
                    $campo = 'observaciones';
                    $valor = $row[18];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[19])){ 
                    $campo = 'impoconsumo';
                    $valor = $row[19];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[20])){ 
                    $campo = 'detalle_comprobante ';
                    $valor = $row[20];
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