<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#28/09/2017 |Erica G. | Archivo Creado 
#######################################################################################################
require_once '../Conexion/conexion.php';
session_start();
$parm_anno = $_SESSION['anno'];
switch ($_POST['action']){
    #***************Guardar Vigencias Interfaz Predial************************#
    case(1):
        $nombre         = "'".$_POST['nombre']."'";
        $valor          = "'".$_POST['valor']."'";
        $vigencias      = "'".$_POST['vigencias_anteriores']."'";
        $sql = "INSERT INTO gf_vigencias_interfaz_predial 
               (nombre, valor, vigencias_anteriores, parametrizacionanno) 
               VALUES ($nombre, $valor, $vigencias, $parm_anno)";
        $res = $mysqli->query($sql);
        echo json_decode($res);
    break;
    #***************Modificar Vigencias Interfaz Predial************************#
    case(2):
        $id        = $_POST['id'];
        $nombre     = "'".$_POST['nombre']."'";
        $valor      = "'".$_POST['valor']."'";
        $vigencias  = "'".$_POST['vigencias_anteriores']."'";
         $sql = "UPDATE gf_vigencias_interfaz_predial 
               SET nombre = $nombre, 
               valor = $valor, 
               vigencias_anteriores = $vigencias
               WHERE id_unico = $id";
        $res = $mysqli->query($sql);
        echo json_decode($res);
    break;
    #***************Eliminar Vigencias Interfaz Predial************************#
    case(3):
        $id         = $_POST['id'];
        $sql = "DELETE FROM gf_vigencias_interfaz_predial WHERE id_unico = $id ";
        $res = $mysqli->query($sql);
        echo json_decode($res);
    break;
}
