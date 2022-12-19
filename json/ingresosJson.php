<?php

require_once('../Conexion/ConexionPDO.php');
require_once('../Conexion/conexion.php');
session_start();
$con     = new ConexionPDO();
$anno    = $_SESSION['anno'];
$usuario = $_SESSION['id_usuario'];
date_default_timezone_set('America/Bogota');
$getdate = date("Y-m-d H:i:s");
$action  = $_POST['action'];
switch ($action) {
    case 1:
        $id   = $_POST['id'];
        $ip   = $_SERVER['REMOTE_ADDR'];
        $host = gethostname();
        #Columns
        $sqlcol = $con->Listar("
        SELECT COLUMN_NAME
        FROM information_schema.columns 
        WHERE table_name = 'gq_ingreso_parqueadero'");
        $col0 = $sqlcol[0][0];  #id_unico
        $col1 = $sqlcol[1][0];  #numero
        $col2 = $sqlcol[3][0];  #fecha
        $col3 = $sqlcol[4][0];  #hora
        $col4 = $sqlcol[5][0];  #placa
        $col5 = $sqlcol[12][0]; #tipo_vehiculo
        $col6 = $sqlcol[13][0]; #observaciones

        #Datos
        $sqling = $con->Listar("
        SELECT *
        FROM gq_ingreso_parqueadero
        WHERE id_unico = $id");
        $ing0 = $sqling[0][0];  #id_unico
        $ing1 = $sqling[0][1];  #numero
        $ing2 = $sqling[0][3];  #fecha
        $ing3 = $sqling[0][4];  #hora
        $ing4 = $sqling[0][5];  #placa
        $ing5 = $sqling[0][12]; #tipo_vehiculo
        $ing6 = $sqling[0][13]; #observaciones
        #Insert id_unico
        $sqlinsert = " 
        INSERT INTO gs_auditoria_parqueadero VALUES (NULL,
        'gq_ingreso_parqueadero', '$col0', '$ing0', 
        '$host', '$getdate',
        'Eliminar', '$ing0', 'NA', '$ip', 
        '$host', $usuario, 'Eliminado por Sigiep')";
        $into = $mysqli->query($sqlinsert);
        #Insert numero
        $sqlinsert = " 
        INSERT INTO gs_auditoria_parqueadero VALUES (NULL,
        'gq_ingreso_parqueadero', '$col1', '$ing0', 
        '$host', '$getdate',
        'Eliminar', '$ing1', 'NA', '$ip', 
        '$host', $usuario, 'Eliminado por Sigiep')";
        $into = $mysqli->query($sqlinsert);
        #Insert fecha
        $sqlinsert = " 
        INSERT INTO gs_auditoria_parqueadero VALUES (NULL,
        'gq_ingreso_parqueadero', '$col2', '$ing0', 
        '$host', '$getdate',
        'Eliminar', '$ing2', 'NA', '$ip', 
        '$host', $usuario, 'Eliminado por Sigiep')";
        $into = $mysqli->query($sqlinsert);
        #Insert hora
        $sqlinsert = " 
        INSERT INTO gs_auditoria_parqueadero VALUES (NULL,
        'gq_ingreso_parqueadero', '$col3', '$ing0', 
        '$host', '$getdate',
        'Eliminar', '$ing3', 'NA', '$ip', 
        '$host', $usuario, 'Eliminado por Sigiep')";
        $into = $mysqli->query($sqlinsert);
        #Insert placa
        $sqlinsert = " 
        INSERT INTO gs_auditoria_parqueadero VALUES (NULL,
        'gq_ingreso_parqueadero', '$col4', '$ing0', 
        '$host', '$getdate',
        'Eliminar', '$ing4', 'NA', '$ip', 
        '$host', $usuario, 'Eliminado por Sigiep')";
        $into = $mysqli->query($sqlinsert);
        #Insert tipo_vehiculo
        $sqlinsert = " 
        INSERT INTO gs_auditoria_parqueadero VALUES (NULL,
        'gq_ingreso_parqueadero', '$col5', '$ing0', 
        '$host', '$getdate',
        'Eliminar', '$ing5', 'NA', '$ip', 
        '$host', $usuario, 'Eliminado por Sigiep')";
        $into = $mysqli->query($sqlinsert);
        #Insert observaciones
        $sqlinsert = " 
        INSERT INTO gs_auditoria_parqueadero VALUES (NULL,
        'gq_ingreso_parqueadero', '$col6', '$ing0', 
        '$host', '$getdate',
        'Eliminar', '$ing6', 'NA', '$ip', 
        '$host', $usuario, 'Eliminado por Sigiep')";
        $into = $mysqli->query($sqlinsert);
        $sql = "DELETE
        FROM gq_ingreso_parqueadero
        WHERE id_unico = $id";
        $resc = $mysqli->query($sql);
        if ($resc) {
            echo 1;
        } else {
            echo 0;
        }
        break;
    case 2:
        break;
}
?>