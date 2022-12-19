<?php 
#######################################################################################################
#                           Modificaciones
#######################################################################################################
#18/04/2017 |ELkin O. | Archivo Creado
#######################################################################################}
@session_start();
#***********Eliminar Categoria*************#
function eliminarCategoria($id_categoria)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        $obs     =  'Eliminado C/U';
        $datoA   =  'NA';
        $table   = 'gn_categoria';
        
        if(!empty($id_categoria)){ 
            $ret = "SELECT * FROM gn_categoria WHERE id_unico = $id_categoria";
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
                    $campo = 'codigointerno';
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
                    $campo = 'nombre';
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
                    $campo = 'salarioactual';
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
                    $campo = 'salarioanterior';
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
                    $campo = 'gastorepresentacion';
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
                    $campo = 'nivel';
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
                    $campo = 'parametrizacion_anno';
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
                    $campo = 'estadocategoria';
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
                    $campo = 'fecha_modificacion';
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
                    $campo = 'tipo_persona_sui';
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


function modificarCategoria($id_categoria,$codigointerno,$nombreCategoria,$salarioactual,$salarioanterior,$gastorepresentacion,$niv,$estado,$tipoSui)
{ 
    $sqlComp="SELECT codigointerno,nombre,salarioactual,salarioanterior,gastorepresentacion,nivel,
    estadocategoria,tipo_persona_sui FROM gn_categoria WHERE id_unico = $id_categoria";
    $crComp = $GLOBALS['mysqli']->query($sqlComp);
    $rowComp = mysqli_fetch_row($crComp);

        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Actualizar';
        $obs     =  'Actualizado C/U';
        $table   = 'gn_categoria';
        
        if(!empty($id_categoria)){ 
            $ret = "SELECT codigointerno,nombre,salarioactual,salarioanterior,gastorepresentacion,nivel,
            estadocategoria,tipo_persona_sui FROM gn_categoria WHERE id_unico = $id_categoria";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $id_categoria;

                if( $rowComp[0]!=$codigointerno){ 
                    $datoA   = $codigointerno;
                    $campo = 'codigointerno';
                    $valor = $row[0];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[1]!=$nombreCategoria){ 
                    $datoA   = $nombreCategoria;
                    $campo = 'nombre';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[2]!=$salarioactual){
                    $datoA   = $salarioactual;
                    $campo = 'salarioactual';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[3]!=$salarioanterior){
                    $datoA   = $salarioanterior;
                    $campo = 'salarioanterior';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[4]!=$gastorepresentacion){
                    $datoA   = $gastorepresentacion;
                    $campo = 'gastorepresentacion';
                    $valor = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[5]!=$niv){
                    $datoA   = $niv;
                    $campo = 'nivel';
                    $valor = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[6]!=$estado){
                    $datoA   = $estado;
                    $campo = 'estadocategoria';
                    $valor = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[7]!=$tipoSui){
                    $datoA   = $tipoSui;
                    $campo = 'tipo_persona_sui';
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

function agregarCategoria($id_categoria)
{ 

        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $table   = 'gn_categoria';
        $valor   =  'NA';
        
        if(!empty($id_categoria)){ 
            $ret = "SELECT * FROM gn_categoria WHERE id_unico = $id_categoria";
            $cr = $GLOBALS['mysqli']->query($ret);
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
                    $campo = 'codigointerno';
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
                    $campo = 'nombre';
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
                    $campo = 'salarioactual';
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
                    $campo = 'salarioanterior';
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
                    $campo = 'gastorepresentacion';
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
                    $campo = 'nivel';
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
                    $campo = 'parametrizacion_anno';
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
                    $campo = 'estadocategoria';
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
                    $campo = 'fecha_modificacion';
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
                    $campo = 'tipo_persona_sui';
                    $datoA = $row[10];
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

#***********Eliminar Parametros liquidacion*************#
function eliminarParemtrosLiq($id_parametro)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        $obs     =  'Eliminado C/U';
        $datoA   =  'NA';
        $table   = 'gn_parametros_liquidacion';
        
        if(!empty($id_parametro)){ 
            $ret = "SELECT * FROM gn_parametros_liquidacion WHERE id_unico = $id_parametro";
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
                    $campo = 'vigencia ';
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
                    $campo = 'salmin';
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
                    $campo = 'auxt';
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
                    $campo = 'primaA';
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
                    $campo = 'primaM';
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
                    $campo = 'asaludemple';
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
                    $campo = 'asaludempre';
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
                    $campo = 'apensionemple';
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
                    $campo = 'apensionempre';
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
                    $campo = 'fodosol';
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
                    $campo = 'excentoret';
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
                    $campo = 'acajacomp';
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
                    $campo = 'asena';
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
                    $campo = 'aicbf';
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
                    $campo = 'aesap';
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
                    $campo = 'aministerio';
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
                    $campo = 'valoruvt';
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
                    $campo = 'talimentacion';
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
                    $campo = 'talimendoc';
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
                    $campo = 'porce_inca';
                    $valor = $row[20];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[21])){ 
                    $campo = 'excento';
                    $valor = $row[21];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[22])){ 
                    $campo = 'rec_noc';
                    $valor = $row[22];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[23])){ 
                    $campo = 'rec_dom';
                    $valor = $row[23];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[24])){ 
                    $campo = 'hext_do';
                    $valor = $row[24];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[25])){ 
                    $campo = 'hext_ddf';
                    $valor = $row[25];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[26])){ 
                    $campo = 'hext_no';
                    $valor = $row[26];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[27])){ 
                    $campo = 'hext_ndf';
                    $valor = $row[27];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[28])){ 
                    $campo = 'redondeo';
                    $valor = $row[28];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[29])){ 
                    $campo = 'saludsena';
                    $valor = $row[29];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[30])){ 
                    $campo = 'tipo_provision';
                    $valor = $row[30];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[31])){ 
                    $campo = 'grupo_gestion';
                    $valor = $row[31];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[32])){ 
                    $campo = 'tipo_empleado';
                    $valor = $row[32];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[33])){ 
                    $campo = 'hora_extra_no';
                    $valor = $row[33];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[34])){ 
                    $campo = 'tope_aux_transporte';
                    $valor = $row[34];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[35])){ 
                    $campo = 'dias_primav';
                    $valor = $row[35];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[36])){ 
                    $campo = 'aplica_bonificacion';
                    $valor = $row[36];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[37])){ 
                    $campo = 'tipo_prima_servicio';
                    $valor = $row[37];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[38])){ 
                    $campo = 'dias_prima_servicio';
                    $valor = $row[38];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[39])){ 
                    $campo = 'dias_prima_navidad';
                    $valor = $row[39];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[40])){ 
                    $campo = 'tipo_liquidaciond';
                    $valor = $row[40];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[41])){ 
                    $campo = 'dias_prima_servicio_navidad';
                    $valor = $row[41];
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

function modificarParametrosLiq($id_parametro,$tipo_empleado,$tipo_provision,$salmin,$auxt,$primaA,$primaM,$asaludemple,
$asaludempre,$apensionemple,$apensionempre,$fodosol,$excentoret,$acajacomp,$asena,$aicbf,$aesap,$aministerio,
$valoruvt,$talimentacion,$talimendoc,$tope_aux_transporte,$porce_inca,$rec_noc,$rec_dom,$hext_do,$hext_ddf,
$hext_no,$hext_ndf,$hora_extra_no,$redondeo,$saludsena,$excento,$dias_primav)
{ 
    $sqlComp="SELECT tipo_empleado,tipo_provision,salmin,auxt,primaA,primaM,asaludemple,asaludempre,apensionemple,apensionempre,fodosol,excentoret,acajacomp,
    asena,aicbf,aesap,aministerio,valoruvt,talimentacion,talimendoc,tope_aux_transporte,porce_inca,rec_noc,rec_dom,hext_do,hext_ddf,hext_no,hext_ndf,
    hora_extra_no,redondeo,saludsena,excento,dias_primav FROM gn_parametros_liquidacion
    WHERE id_unico= $id_parametro";
    $crComp = $GLOBALS['mysqli']->query($sqlComp);
    $rowComp = mysqli_fetch_row($crComp);

        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Actualizar';
        $obs     =  'Actualizado C/U';
       
        $table   = 'gn_parametros_liquidacion';
        
        if(!empty($id_parametro)){ 
            $ret = "SELECT tipo_empleado,tipo_provision,salmin,auxt,primaA,primaM,asaludemple,asaludempre,apensionemple,apensionempre,fodosol,excentoret,acajacomp,
            asena,aicbf,aesap,aministerio,valoruvt,talimentacion,talimendoc,tope_aux_transporte,porce_inca,rec_noc,rec_dom,hext_do,hext_ddf,hext_no,hext_ndf,
            hora_extra_no,redondeo,saludsena,excento,dias_primav FROM gn_parametros_liquidacion
            WHERE id_unico= $id_parametro";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $id_parametro;

                if( $rowComp[0]!=$tipo_empleado){ 
                    $datoA   = $tipo_empleado;
                    $campo = 'tipo_empleado';
                    $valor = $row[0];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[1]!=$tipo_provision){ 
                    $datoA   = $tipo_provision;
                    $campo = 'tipo_provision';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[2]!=$salmin){
                    $datoA   = $salmin;
                    $campo = 'salmin';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[3]!=$auxt){
                    $datoA   = $auxt;
                    $campo = 'auxt';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[4]!=$primaA){
                    $datoA   = $primaA;
                    $campo = 'primaA';
                    $valor = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[5]!=$primaM){
                    $datoA   = $primaM;
                    $campo = 'primaM';
                    $valor = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[6]!=$asaludemple){
                    $datoA   = $asaludemple;
                    $campo = 'asaludemple';
                    $valor = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[7]!=$asaludempre){
                    $datoA   = $asaludempre;
                    $campo = 'asaludempre';
                    $valor = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[8]!=$apensionemple){
                    $datoA   = $apensionemple;
                    $campo = 'apensionemple';
                    $valor = $row[8];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[9]!=$apensionempre){
                    $datoA   = $apensionempre;
                    $campo = 'apensionempre';
                    $valor = $row[9];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[10]!=$fodosol){
                    $datoA   = $fodosol;
                    $campo = 'fodosol';
                    $valor = $row[10];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[11]!=$excentoret){
                    $datoA   = $excentoret;
                    $campo = 'excentoret';
                    $valor = $row[11];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[12]!=$acajacomp){
                    $datoA   = $acajacomp;
                    $campo = 'acajacomp';
                    $valor = $row[12];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[13]!=$asena){
                    $datoA   = $asena;
                    $campo = 'asena';
                    $valor = $row[13];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[14]!=$aicbf){
                    $datoA   = $aicbf;
                    $campo = 'aicbf';
                    $valor = $row[14];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[15]!=$aesap){
                    $datoA   = $aesap;
                    $campo = 'aesap';
                    $valor = $row[15];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[16]!=$aministerio){
                    $datoA   = $aministerio;
                    $campo = 'aministerio';
                    $valor = $row[16];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[17]!=$valoruvt){
                    $datoA   = $valoruvt;
                    $campo = 'valoruvt';
                    $valor = $row[17];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[18]!=$talimentacion){
                    $datoA   = $talimentacion;
                    $campo = 'talimentacion';
                    $valor = $row[18];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[19]!=$talimendoc){
                    $datoA   = $talimendoc;
                    $campo = 'talimendoc';
                    $valor = $row[19];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[20]!=$tope_aux_transporte){
                    $datoA   = $tope_aux_transporte;
                    $campo = 'tope_aux_transporte';
                    $valor = $row[20];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[21]!=$porce_inca){
                    $datoA   = $porce_inca;
                    $campo = 'porce_inca';
                    $valor = $row[21];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[22]!=$rec_noc){
                    $datoA   = $rec_noc;
                    $campo = 'rec_noc';
                    $valor = $row[22];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[23]!=$rec_dom){
                    $datoA   = $rec_dom;
                    $campo = 'rec_dom';
                    $valor = $row[23];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[24]!=$hext_do){
                    $datoA   = $hext_do;
                    $campo = 'hext_do';
                    $valor = $row[24];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[25]!=$hext_ddf){
                    $datoA   = $hext_ddf;
                    $campo = 'hext_ddf';
                    $valor = $row[25];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[26]!=$hext_no){
                    $datoA   = $hext_no;
                    $campo = 'hext_no';
                    $valor = $row[26];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[27]!=$hext_ndf){
                    $datoA   = $hext_ndf;
                    $campo = 'hext_ndf';
                    $valor = $row[27];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[28]!=$hora_extra_no){
                    $datoA   = $hora_extra_no;
                    $campo = 'hora_extra_no';
                    $valor = $row[28];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[29]!=$redondeo){
                    $datoA   = $redondeo;
                    $campo = 'redondeo';
                    $valor = $row[29];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[30]!=$saludsena){
                    $datoA   = $saludsena;
                    $campo = 'saludsena';
                    $valor = $row[30];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[31]!=$excento){
                    $datoA   = $excento;
                    $campo = 'excento';
                    $valor = $row[31];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[32]!=$dias_primav){
                    $datoA   = $dias_primav;
                    $campo = 'dias_primav';
                    $valor = $row[32];
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


#***********Agregar Parametros liquidacion*************#
function agregarParametro($id_parametro)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $valor   =  'NA';
        $table   = 'gn_parametros_liquidacion';
        
        if(!empty($id_parametro)){ 
            $ret = "SELECT * FROM gn_parametros_liquidacion WHERE id_unico = $id_parametro";
            $cr = $GLOBALS['mysqli']->query($ret);
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
                    $campo = 'vigencia ';
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
                    $campo = 'salmin';
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
                    $campo = 'auxt';
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
                    $campo = 'primaA';
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
                    $campo = 'primaM';
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
                    $campo = 'asaludemple';
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
                    $campo = 'asaludempre';
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
                    $campo = 'apensionemple';
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
                    $campo = 'apensionempre';
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
                    $campo = 'fodosol';
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
                    $campo = 'excentoret';
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
                    $campo = 'acajacomp';
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
                    $campo = 'asena';
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
                    $campo = 'aicbf';
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
                    $campo = 'aesap';
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
                    $campo = 'aministerio';
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
                    $campo = 'valoruvt';
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
                    $campo = 'talimentacion';
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
                    $campo = 'talimendoc';
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
                    $campo = 'porce_inca';
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
                    $campo = 'excento';
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
                    $campo = 'rec_noc';
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
                    $campo = 'rec_dom';
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
                    $campo = 'hext_do';
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
                    $campo = 'hext_ddf';
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
                    $campo = 'hext_no';
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
                    $campo = 'hext_ndf';
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
                    $campo = 'redondeo';
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
                    $campo = 'saludsena';
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
                    $campo = 'tipo_provision';
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
                    $campo = 'grupo_gestion';
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
                    $campo = 'tipo_empleado';
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
                    $campo = 'hora_extra_no';
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
                    $campo = 'tope_aux_transporte';
                    $datoA = $row[34];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[35])){ 
                    $campo = 'dias_primav';
                    $datoA = $row[35];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[36])){ 
                    $campo = 'aplica_bonificacion';
                    $datoA = $row[36];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[37])){ 
                    $campo = 'tipo_prima_servicio';
                    $datoA = $row[37];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[38])){ 
                    $campo = 'dias_prima_servicio';
                    $datoA = $row[38];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[39])){ 
                    $campo = 'dias_prima_navidad';
                    $datoA = $row[39];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[40])){ 
                    $campo = 'tipo_liquidaciond';
                    $datoA = $row[40];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[41])){ 
                    $campo = 'dias_prima_servicio_navidad';
                    $datoA = $row[41];
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

function eliminarEmpleado($id_empleado)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        $obs     =  'Eliminar C/U';
        $datoA   =  'NA';
        $table   = 'gn_empleado';
        
        if(!empty($id_empleado)){ 
            $ret = "SELECT * FROM gn_empleado WHERE id_unico = $id_empleado";
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
                    $campo = 'codigointerno';
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
                    $campo = 'tercero';
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
                    $campo = 'estado';
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
                    $campo = 'cesantias';
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
                    $campo = 'mediopago';
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
                    $campo = 'unidadejecutora';
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
                    $campo = 'grupogestion';
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
                    $campo = 'salInt';
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
                    $campo = 'tipo_riesgo';
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
                    $campo = 'reg_retro';
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
                    $campo = 'empresa';
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
                    $campo = 'equivalente_NE';
                    $valor = $row[12];
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

function modificarEmpleado($id_empleado,$tercero,$codigointerno,$estado,$cesantias,$mediopago,$unidadejecutora,$grupogestion,$salInt ,$tipo_riesgo,$equivalente_NE )
{ 
    $sqlComp="SELECT tercero , codigointerno,estado , cesantias ,mediopago ,unidadejecutora, 
    grupogestion , salInt , tipo_riesgo ,equivalente_NE FROM gn_empleado WHERE id_unico = $id_empleado";
    $crComp = $GLOBALS['mysqli']->query($sqlComp);
    $rowComp = mysqli_fetch_row($crComp);

        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Actualizar';
        $obs     =  'Actualizado C/U';
        $table   = 'gn_empleado';
        
        if(!empty($id_empleado)){ 
            $ret = "SELECT tercero , codigointerno,estado , cesantias ,mediopago ,unidadejecutora, 
            grupogestion , salInt , tipo_riesgo ,equivalente_NE FROM gn_empleado WHERE id_unico = $id_empleado";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $id_empleado;

                if( $rowComp[0]!=$tercero){ 
                    $datoA   = $tercero;
                    $campo = 'tercero';
                    $valor = $row[0];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[1]!=$codigointerno){ 
                    $datoA   = $codigointerno;
                    $campo = 'codigointerno';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[2]!=$estado){
                    $datoA   = $estado;
                    $campo = 'estado';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[3]!=$cesantias){
                    $datoA   = $cesantias;
                    $campo = 'cesantias';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[4]!=$mediopago){
                    $datoA   = $mediopago;
                    $campo = 'mediopago';
                    $valor = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[5]!=$unidadejecutora){
                    $datoA   = $unidadejecutora;
                    $campo = 'unidadejecutora';
                    $valor = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[6]!=$grupogestion){
                    $datoA   = $grupogestion;
                    $campo = 'grupogestion';
                    $valor = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[7]!=$salInt){
                    $datoA   = $salInt;
                    $campo = 'salInt';
                    $valor = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[8]!=$tipo_riesgo){
                    $datoA   = $tipo_riesgo;
                    $campo = 'tipo_riesgo';
                    $valor = $row[8];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[9]!=$equivalente_NE){
                    $datoA   = $equivalente_NE;
                    $campo = 'equivalente_NE';
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
    return (true);    
}

function agregarEmpleado($id_empleado)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $valor   =  'NA';
        $table   = 'gn_empleado';
        
        if(!empty($id_empleado)){ 
            $ret = "SELECT * FROM gn_empleado WHERE id_unico = $id_empleado";
            $cr = $GLOBALS['mysqli']->query($ret);
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
                    $campo = 'codigointerno';
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
                    $campo = 'tercero';
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
                    $campo = 'estado';
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
                    $campo = 'cesantias';
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
                    $campo = 'mediopago';
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
                    $campo = 'unidadejecutora';
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
                    $campo = 'grupogestion';
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
                    $campo = 'salInt';
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
                    $campo = 'tipo_riesgo';
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
                    $campo = 'reg_retro';
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
                    $campo = 'empresa';
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
                    $campo = 'equivalente_NE';
                    $datoA = $row[12];
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


#***********Eliminar Afiliacion*************#
function eliminarAfiliacion($id_afiliacion)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        $obs     =  'Eliminado C/U';
        $datoA   =  'NA';
        $table   = 'gn_afiliacion';
        
        if(!empty($id_afiliacion)){ 
            $ret = "SELECT * FROM gn_afiliacion WHERE id_unico = $id_afiliacion";
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
                    $campo = 'codigoadmin';
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
                    $campo = 'fechaafiliacion';
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
                    $campo = 'fecharetiro';
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
                    $campo = 'observaciones';
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
                    $campo = 'empleado';
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
                    $campo = 'tipo';
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
                    $campo = 'tercero';
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
                    $campo = 'estado ';
                    $valor = $row[8];
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


function modificarAfiliacion($id_afiliacion,$codigoadmin,$fechaafiliacion,$fecharetiro,$observaciones,$empleado,$tipo,$tercero,$estado )
{ 
    $sqlComp="SELECT codigoadmin,fechaafiliacion,fecharetiro,observaciones,empleado,tipo,tercero,estado 
             FROM gn_afiliacion WHERE id_unico = $id_afiliacion";
    $crComp = $GLOBALS['mysqli']->query($sqlComp);
    $rowComp = mysqli_fetch_row($crComp);
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Actualizar';
        $obs     =  'Actualizado C/U';
        $table   = 'gn_afiliacion';
        
        if(!empty($id_afiliacion)){ 
            $ret = "SELECT codigoadmin,fechaafiliacion,fecharetiro,observaciones,empleado,tipo,tercero,estado 
             FROM gn_afiliacion WHERE id_unico = $id_afiliacion";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $id_afiliacion;

                if( $rowComp[0]!=$codigoadmin){ 
                    $datoA   = $codigoadmin;
                    $campo = 'codigoadmin';
                    $valor = $row[0];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[1]!=$fechaafiliacion){ 
                    $datoA   = $fechaafiliacion;
                    $campo = 'fechaafiliacion';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[2]!=$fecharetiro){
                    $datoA   = $fecharetiro;
                    $campo = 'fecharetiro';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[3]!=$observaciones){
                    $datoA   = $observaciones;
                    $campo = 'observaciones';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[4]!=$empleado){
                    $datoA   = $empleado;
                    $campo = 'empleado';
                    $valor = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[5]!=$tipo){
                    $datoA   = $tipo;
                    $campo = 'tipo';
                    $valor = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[6]!=$tercero){
                    $datoA   = $tercero;
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

                if($rowComp[7]!=$estado){
                    $datoA   = $estado;
                    $campo = 'estado';
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

function agregarAfiliacion($id_afiliacion)
{ 

        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $table   = 'gn_afiliacion';
        $valor   =  'NA';
        if(!empty($id_afiliacion)){ 
            $ret = "SELECT * FROM gn_afiliacion WHERE id_unico = $id_afiliacion";
            $cr = $GLOBALS['mysqli']->query($ret);
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
                    $campo = 'codigoadmin';
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
                    $campo = 'fechaafiliacion';
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
                    $campo = 'fecharetiro';
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
                    $campo = 'observaciones';
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
                    $campo = 'empleado';
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
                    $campo = 'tipo';
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
                    $campo = 'tercero';
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
                    $campo = 'estado ';
                    $datoA = $row[8];
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

#***********Eliminar Tercero Categoria*************#
function eliminarTerceroCat($id_terceroCat)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        $obs     =  'Eliminado C/U';
        $datoA   =  'NA';
        $table   = 'gn_tercero_categoria';
        
        if(!empty($id_terceroCat)){ 
            $ret = "SELECT * FROM gn_tercero_categoria WHERE id_unico = $id_terceroCat";
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
                    $campo = 'fechamodificacion';
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
                    $campo = 'fechacancelacion';
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
                    $campo = 'empleado';
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
                    $campo = 'categoria';
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
                    $campo = 'estado';
                    $valor = $row[5];
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


function modificarTerceroCat($id_terceroCat,$fechamodificacion,$fechacancelacion,$empleado ,$categoria ,$estado)
{ 
    $sqlComp="SELECT fechamodificacion,fechacancelacion,empleado,categoria,estado
             FROM gn_tercero_categoria WHERE id_unico = $id_terceroCat";
    $crComp = $GLOBALS['mysqli']->query($sqlComp);
    $rowComp = mysqli_fetch_row($crComp);
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Actualizar';
        $obs     =  'Actualizado C/U';
        $table   = 'gn_tercero_categoria';
        
        if(!empty($id_terceroCat)){ 
            $ret = "SELECT fechamodificacion,fechacancelacion,empleado,categoria,estado
            FROM gn_tercero_categoria WHERE id_unico = $id_terceroCat";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $id_terceroCat;

                if( $rowComp[0]!=$fechamodificacion){ 
                    $datoA   = $fechamodificacion;
                    $campo = 'fechamodificacion';
                    $valor = $row[0];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[1]!=$fechacancelacion){ 
                    $datoA   = $fechacancelacion;
                    $campo = 'fechacancelacion';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[2]!=$empleado){
                    $datoA   = $empleado;
                    $campo = 'empleado';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[3]!=$categoria){
                    $datoA   = $categoria;
                    $campo = 'categoria';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[4]!=$estado){
                    $datoA   = $estado;
                    $campo = 'estado';
                    $valor = $row[4];
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


function agregarTerceroC($id_terceroCat)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $valor   =  'NA';
        $table   = 'gn_tercero_categoria';
        
        if(!empty($id_terceroCat)){ 
            $ret = "SELECT * FROM gn_tercero_categoria WHERE id_unico = $id_terceroCat";
            $cr = $GLOBALS['mysqli']->query($ret);
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
                    $campo = 'fechamodificacion';
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
                    $campo = 'fechacancelacion';
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
                    $campo = 'empleado';
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
                    $campo = 'categoria';
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
                    $campo = 'estado';
                    $datoA = $row[5];
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



#***********Eliminar Vinculacion Retiro*************#
function eliminarVinculacionR($id_vinculacion)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        $obs     =  'Eliminado C/U';
        $datoA   =  'NA';
        $table   = 'gn_vinculacion_retiro';
        
        if(!empty($id_vinculacion)){ 
            $ret = "SELECT * FROM gn_vinculacion_retiro WHERE id_unico = $id_vinculacion";
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
                    $campo = 'numeroacto';
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
                    $campo = 'fechaacto';
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
                    $campo = 'fecha';
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
                    $campo = 'empleado';
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
                    $campo = 'tipovinculacion';
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
                    $campo = 'estado';
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
                    $campo = 'causaretiro';
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
                    $campo = 'vinculacionretiro';
                    $valor = $row[8];
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


function modificarVinculacionR($id_vinculacionR,$numeroacto,$fechaacto,$fecha ,$empleado ,$tipovinculacion,$estado,$causaretiro,$vinculacionretiro)
{ 
    $sqlComp="SELECT numeroacto,fechaacto,fecha,empleado,tipovinculacion,estado,causaretiro,vinculacionretiro
             FROM gn_vinculacion_retiro WHERE id_unico = $id_vinculacionR";
    $crComp = $GLOBALS['mysqli']->query($sqlComp);
    $rowComp = mysqli_fetch_row($crComp);
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Actualizar';
        $obs     =  'Actualizado C/U';
        $table   = 'gn_vinculacion_retiro';
        
        if(!empty($id_vinculacionR)){ 
            $ret = "SELECT numeroacto,fechaacto,fecha,empleado,tipovinculacion,estado,causaretiro,vinculacionretiro
            FROM gn_vinculacion_retiro WHERE id_unico = $id_vinculacionR";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $id_vinculacionR;

                if( $rowComp[0]!=$numeroacto){ 
                    $datoA   = $numeroacto;
                    $campo = 'numeroacto';
                    $valor = $row[0];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[1]!=$fechaacto){ 
                    $datoA   = $fechaacto;
                    $campo = 'fechaacto';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[2]!=$fecha){
                    $datoA   = $fecha;
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

                if($rowComp[3]!=$empleado){
                    $datoA   = $empleado;
                    $campo = 'empleado';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[4]!=$tipovinculacion){
                    $datoA   = $tipovinculacion;
                    $campo = 'tipovinculacion';
                    $valor = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[5]!=$estado){
                    $datoA   = $estado;
                    $campo = 'estado';
                    $valor = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }  
                
                if($rowComp[6]!=$causaretiro){
                    $datoA   = $causaretiro;
                    $campo = 'causaretiro';
                    $valor = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }  

                if($rowComp[7]!=$vinculacionretiro){
                    $datoA   = $vinculacionretiro;
                    $campo = 'vinculacionretiro';
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

function agregarVinculacion($id_vinculacion)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $valor   =  'NA';
        $table   = 'gn_vinculacion_retiro';
        
        if(!empty($id_vinculacion)){ 
            $ret = "SELECT * FROM gn_vinculacion_retiro WHERE id_unico = $id_vinculacion";
            $cr = $GLOBALS['mysqli']->query($ret);
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
                    $campo = 'numeroacto';
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
                    $campo = 'fechaacto';
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
                    $campo = 'fecha';
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
                    $campo = 'empleado';
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
                    $campo = 'tipovinculacion';
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
                    $campo = 'estado';
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
                    $campo = 'causaretiro';
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
                    $campo = 'vinculacionretiro';
                    $datoA = $row[8];
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


#***********Eliminar Incapacidad*************#
function eliminarIncapacidad($id_incapacidad)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        $obs     =  'Eliminado C/U';
        $datoA   =  'NA';
        $table   = 'gn_incapacidad';
        
        if(!empty($id_incapacidad)){ 
            $ret = "SELECT * FROM gn_incapacidad WHERE id_unico = $id_incapacidad";
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
                    $campo = 'numeroinc';
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
                    $campo = 'fechainicio';
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
                    $campo = 'numerodias';
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
                    $campo = 'numeroaprobacion';
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
                    $campo = 'fechaaprobacion';
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
                    $campo = 'diagnostico';
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
                    $campo = 'empleado';
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
                    $campo = 'tiponovedad ';
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
                    $campo = 'accidente';
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
                    $campo = 'dias_faltantes';
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
                    $campo = 'concepto';
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
                    $campo = 'fechafinal';
                    $valor = $row[12];
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

function modificarIncapacidad($id_incapacidad,$numeroinc,$fechainicio,$numerodias ,$numeroaprobacion,$fechaaprobacion,$diagnostico,$empleado ,$tiponovedad,$accidente,$dias_faltantes,$concepto,$fechafinal)
{ 
    $sqlComp="SELECT numeroinc,fechainicio,numerodias ,numeroaprobacion,fechaaprobacion,diagnostico,empleado ,tiponovedad,accidente,dias_faltantes,concepto,fechafinal
             FROM gn_incapacidad WHERE id_unico = $id_incapacidad";
    $crComp = $GLOBALS['mysqli']->query($sqlComp);
    $rowComp = mysqli_fetch_row($crComp);
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Actualizar';
        $obs     =  'Actualizado C/U';
        $table   = 'gn_incapacidad';
        
        if(!empty($id_incapacidad)){ 
            $ret = "SELECT numeroinc,fechainicio,numerodias ,numeroaprobacion,fechaaprobacion,diagnostico,empleado ,tiponovedad,accidente,dias_faltantes,concepto,fechafinal
            FROM gn_incapacidad WHERE id_unico = $id_incapacidad";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $id_incapacidad;

                if( $rowComp[0]!=$numeroinc){ 
                    $datoA   = $numeroinc;
                    $campo = 'numeroinc';
                    $valor = $row[0];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[1]!=$fechainicio){ 
                    $datoA   = $fechainicio;
                    $campo = 'fechainicio';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[2]!=$numerodias){
                    $datoA   = $numerodias;
                    $campo = 'numerodias';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[3]!=$numeroaprobacion){
                    $datoA   = $numeroaprobacion;
                    $campo = 'numeroaprobacion';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[4]!=$fechaaprobacion){
                    $datoA   = $fechaaprobacion;
                    $campo = 'tipovinculacion';
                    $valor = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[5]!=$diagnostico){
                    $datoA   = $diagnostico;
                    $campo = 'diagnostico';
                    $valor = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }  
                
                if($rowComp[6]!=$empleado ){
                    $datoA   = $empleado ;
                    $campo = 'empleado';
                    $valor = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }  

                if($rowComp[7]!=$tiponovedad ){
                    $datoA   = $tiponovedad ;
                    $campo = 'tiponovedad';
                    $valor = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[8]!=$accidente){
                    $datoA   = $accidente ;
                    $campo = 'accidente';
                    $valor = $row[8];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[9]!=$dias_faltantes ){
                    $datoA   = $dias_faltantes ;
                    $campo = 'dias_faltantes';
                    $valor = $row[9];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[10]!=$concepto){
                    $datoA   = $concepto;
                    $campo = 'concepto';
                    $valor = $row[10];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[11]!=$fechafinal){
                    $datoA   = $fechafinal;
                    $campo = 'fechafinal';
                    $valor = $row[11];
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


function agregarIncapacidad($id_incapacidad)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $valor   =  'NA';
        $table   = 'gn_incapacidad';
        
        if(!empty($id_incapacidad)){ 
            $ret = "SELECT * FROM gn_incapacidad WHERE id_unico = $id_incapacidad";
            $cr = $GLOBALS['mysqli']->query($ret);
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
                    $campo = 'numeroinc';
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
                    $campo = 'fechainicio';
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
                    $campo = 'numerodias';
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
                    $campo = 'numeroaprobacion';
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
                    $campo = 'fechaaprobacion';
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
                    $campo = 'diagnostico';
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
                    $campo = 'empleado';
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
                    $campo = 'tiponovedad ';
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
                    $campo = 'accidente';
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
                    $campo = 'dias_faltantes';
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
                    $campo = 'concepto';
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
                    $campo = 'fechafinal';
                    $datoA = $row[12];
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



#***********Eliminar Vacaciones*************#
function eliminarVacaciones($id_vacacion)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        $obs     =  'Eliminado C/U';
        $datoA   =  'NA';
        $table   = 'gn_vacaciones';
        
        if(!empty($id_vacacion)){ 
            $ret = "SELECT * FROM gn_vacaciones WHERE id_unico = $id_vacacion";
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
                    $campo = 'fechainicio';
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
                    $campo = 'fechafin';
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
                    $campo = 'fechainiciodisfrute';
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
                    $campo = 'fechafindisfrute';
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
                    $campo = 'numeroacto';
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
                    $campo = 'fechaacto';
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
                    $campo = 'empleado';
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
                    $campo = 'tiponovedad ';
                    $valor = $row[8];
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



function modificarVacaciones($id_vacacion,$fechainicio,$fechafin,$fechainiciodisfrute ,$fechafindisfrute,$numeroacto,$fechaacto,$empleado ,$tiponovedad)
{ 
    $sqlComp="SELECT fechainicio,fechafin,fechainiciodisfrute ,fechafindisfrute,numeroacto,fechaacto,empleado ,tiponovedad
             FROM gn_vacaciones WHERE id_unico = $id_vacacion";
    $crComp = $GLOBALS['mysqli']->query($sqlComp);
    $rowComp = mysqli_fetch_row($crComp);
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Actualizar';
        $obs     =  'Actualizado C/U';
        $table   = 'gn_vacaciones';
        
        if(!empty($id_vacacion)){ 
            $ret = "SELECT fechainicio,fechafin,fechainiciodisfrute ,fechafindisfrute,numeroacto,fechaacto,empleado ,tiponovedad
            FROM gn_vacaciones WHERE id_unico = $id_vacacion";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $id_vacacion;

                if( $rowComp[0]!=$fechainicio){ 
                    $datoA   = $fechainicio;
                    $campo = 'fechainicio';
                    $valor = $row[0];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[1]!=$fechafin){ 
                    $datoA   = $fechafin;
                    $campo = 'fechafin';
                    $valor = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[2]!=$fechainiciodisfrute){
                    $datoA   = $fechainiciodisfrute;
                    $campo = 'fechainiciodisfrute';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[3]!=$fechafindisfrute){
                    $datoA   = $fechafindisfrute;
                    $campo = 'fechafindisfrute';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[4]!=$numeroacto){
                    $datoA   = $numeroacto;
                    $campo = 'numeroacto';
                    $valor = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[5]!=$fechaacto){
                    $datoA   = $fechaacto;
                    $campo = 'fechaacto';
                    $valor = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }  
                
                if($rowComp[6]!=$empleado ){
                    $datoA   = $empleado ;
                    $campo = 'empleado';
                    $valor = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }  

                if($rowComp[7]!=$tiponovedad ){
                    $datoA   = $tiponovedad ;
                    $campo = 'tiponovedad';
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


function agregarVacaciones($id_vacacion)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $valor   =  'NA';
        $table   = 'gn_vacaciones';
        
        if(!empty($id_vacacion)){ 
            $ret = "SELECT * FROM gn_vacaciones WHERE id_unico = $id_vacacion";
            $cr = $GLOBALS['mysqli']->query($ret);
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
                    $campo = 'fechainicio';
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
                    $campo = 'fechafin';
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
                    $campo = 'fechainiciodisfrute';
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
                    $campo = 'fechafindisfrute';
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
                    $campo = 'numeroacto';
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
                    $campo = 'fechaacto';
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
                    $campo = 'empleado';
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
                    $campo = 'tiponovedad ';
                    $datoA = $row[8];
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


#***********Eliminar Novedades*************#
function eliminarNovedades($id_empleado,$periodo,$opcion,$id_novedad)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        if ($opcion==2) {
            $obs     =  'Eliminado en Cascada';
        }else{
            $obs     =  'Eliminado C/U';
        }
        $datoA   =  'NA';
        $table   = 'gn_novedad';
        
        if(!empty($id_empleado) || !empty($id_novedad)){ 
            if ($opcion==2) {
                $ret = "SELECT * FROM gn_novedad WHERE empleado= $id_empleado AND periodo=$periodo";
            }else{
                $ret = "SELECT * FROM gn_novedad WHERE id_unico =$id_novedad";
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
                    $campo = 'valor';
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
                    $campo = 'empleado';
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
                    $campo = 'periodo';
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
                    $campo = 'concepto';
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
                    $campo = 'aplicabilidad';
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
                    $campo = 'incapacidad';
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
                    $campo = 'periodo_prin';
                    $valor = $row[8];
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


function modificarNovedad($id_novedad,$valor,$fecha,$empleado,$periodo ,$concepto)
{ 
    $sqlComp="SELECT valor,fecha,empleado,periodo ,concepto
             FROM gn_novedad WHERE id_unico = $id_novedad";
    $crComp = $GLOBALS['mysqli']->query($sqlComp);
    $rowComp = mysqli_fetch_row($crComp);
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Actualizar';
        $obs     =  'Actualizado C/U';
        $table   = 'gn_novedad';
        
        if(!empty($id_novedad)){ 
            $ret = "SELECT valor,fecha,empleado,periodo ,concepto
            FROM gn_novedad WHERE id_unico = $id_novedad";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $id_novedad;

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
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[1]!=$fecha){ 
                    $datoA   = $fecha;
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
                if($rowComp[2]!=$empleado){
                    $datoA   = $empleado;
                    $campo = 'empleado';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[3]!=$periodo){
                    $datoA   = $periodo;
                    $campo = 'periodo';
                    $valor = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[4]!=$concepto){
                    $datoA   = $concepto;
                    $campo = 'concepto';
                    $valor = $row[4];
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


function agregarNovedad($id_novedad,$opcion)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        if ($opcion==2) {
            $obs     =  'Eliminado en Cascada';
        }else{
            $obs     =  'Agregar C/U';
        }
        $valor   =  'NA';
        $table   = 'gn_novedad';
        
        if( !empty($id_novedad)){ 
            if ($opcion==2) {
                $ret = "SELECT * FROM gn_novedad WHERE id_unico =$id_novedad";
            }else{
                $ret = "SELECT * FROM gn_novedad WHERE id_unico =$id_novedad";
            }
           
            $cr = $GLOBALS['mysqli']->query($ret);
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
                    $campo = 'valor';
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
                    $campo = 'empleado';
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
                    $campo = 'periodo';
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
                    $campo = 'concepto';
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
                    $campo = 'aplicabilidad';
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
                    $campo = 'incapacidad';
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
                    $campo = 'periodo_prin';
                    $datoA = $row[8];
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


function agregarNovedadCascada($periodo,$empleado)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregado en Cascada';
        $valor   =  'NA';
        $table   = 'gn_novedad';
        
        if( !empty($empleado)){ 
            $ret = "SELECT * FROM gn_novedad WHERE empleado =$empleado AND periodo=$periodo";
            $cr = $GLOBALS['mysqli']->query($ret);
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
                    $campo = 'valor';
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
                    $campo = 'empleado';
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
                    $campo = 'periodo';
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
                    $campo = 'concepto';
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
                    $campo = 'aplicabilidad';
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
                    $campo = 'incapacidad';
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
                    $campo = 'periodo_prin';
                    $datoA = $row[8];
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


#***********Eliminar Novedades*************#
function eliminarHorasE($id_horaE)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Eliminar';
        $obs     =  'Eliminado C/U';
        $datoA   =  'NA';
        $table   = 'gn_horas_extras';
        
        if(!empty($id_horaE)){ 
            $ret = "SELECT * FROM gn_horas_extras WHERE id_unico =$id_horaE";
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
                    $campo = 'numerohoras';
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
                    $campo = 'empleado';
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
                    $campo = 'concepto';
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
                    $campo = 'tipo_novedad';
                    $valor = $row[5];
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


function modificarHorasE($id_horaE,$numerohoras,$fecha,$empleado,$concepto)
{ 
    $sqlComp="SELECT numerohoras,fecha,empleado,concepto
             FROM gn_horas_extras WHERE id_unico = $id_horaE";
    $crComp = $GLOBALS['mysqli']->query($sqlComp);
    $rowComp = mysqli_fetch_row($crComp);
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Actualizar';
        $obs     =  'Actualizado C/U';
        $table   = 'gn_horas_extras';
        
        if(!empty($id_horaE)){ 
            $ret = "SELECT numerohoras,fecha,empleado,concepto
            FROM gn_horas_extras WHERE id_unico = $id_horaE";
            $cr = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $id_horaE;
                if( $rowComp[0]!=$numerohoras){ 
                    $datoA   = $numerohoras;
                    $campo = 'numerohoras';
                    $valor = $row[0];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if($rowComp[1]!=$fecha){ 
                    $datoA   = $fecha;
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
                if($rowComp[2]!=$empleado){
                    $datoA   = $empleado;
                    $campo = 'empleado';
                    $valor = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if($rowComp[3]!=$concepto){
                    $datoA   = $concepto;
                    $campo = 'concepto';
                    $valor = $row[3];
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

function agregarHoraE($id_horaE)
{ 
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $valor   =  'NA';
        $table   = 'gn_horas_extras';
        
        if(!empty($id_horaE)){ 
            $ret = "SELECT * FROM gn_horas_extras WHERE id_unico =$id_horaE";
            $cr = $GLOBALS['mysqli']->query($ret);
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
                    $campo = 'numerohoras';
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
                    $campo = 'empleado';
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
                    $campo = 'concepto';
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
                    $campo = 'tipo_novedad';
                    $datoA = $row[5];
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


function agregarNominaE($id_nomina)
{ 

        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $table   = 'gn_nomina_electronica';
        $valor   =  'NA';
        
        if(!empty($id_nomina)){ 
            $ret = "SELECT * FROM gn_nomina_electronica WHERE id_unico = $id_nomina";
            $cr = $GLOBALS['mysqli']->query($ret);
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
                    $campo = 'prefijo';
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
                    $campo = 'consecutivo';
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
                    $campo = 'mes';
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
                    $campo = 'anno';
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
                    $campo = 'tercero';
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
                    $campo = 'fecha_envio';
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
                    $campo = 'usuario_envio';
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
                    $campo = 'cune';
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
                    $campo = 'json';
                    $datoA = $row[9];
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



