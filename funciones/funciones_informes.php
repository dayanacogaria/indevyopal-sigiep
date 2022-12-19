<?php
function obtener_informe($id_report){
	require ('../Conexion/conexion.php');
	try {
        $sql = "SELECT id, nombre, select_table FROM gn_informe WHERE md5(id) = '$id_report'";
        $res = $mysqli->query($sql);
        $rows = mysqli_fetch_row($res);
        $mysqli->close();
        return $rows;
    } catch (Exception $e) {
        die($e->getMessage());
    }
}

function obtener_origen($id_report){
    require ('../Conexion/conexion.php');
    try {
        $sql = "SELECT id, tabla_origen, columna_origen, select_table_origen FROM gn_tabla_homologable WHERE informe = '$id_report'";
        $res = $mysqli->query($sql);
        $row = mysqli_fetch_row($res);
        $mysqli->close();
        return $row;
    } catch (Exception $e) {
        die($e->getMessage());
    }
}

function obtener_destino($id_report){
    require ('../Conexion/conexion.php');
    try {
        $sql = "SELECT id, columna_destino, tabla_destino, select_table_destino FROM gn_tabla_homologable WHERE informe = $id_report";
        $res = $mysqli->query($sql);
        return $res;
    } catch (Exception $e) {
        die($e->getMessage());
    }
}

function ejecutar_consulta($sql){
    require ('../Conexion/conexion.php');
    try{
        $res = $mysqli->query($sql);
        return $res;
    }catch(Exception $e){
        die($e->getMessage());
    }
}