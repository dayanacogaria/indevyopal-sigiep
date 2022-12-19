<?php
#######################################################################################################
#                           Modificaciones
#######################################################################################################
#28/09/2017 |Erica G. | Archivo Creado 
#######################################################################################################
require_once '../Conexion/conexion.php';
session_start();
$parm_anno = $_SESSION['anno'];
switch ($_POST['action']){
    #***************Guardar Libro************************#
    case(1):
        $nombre         = "'".$_POST['nombre']."'";
        $codigo         = "'".$_POST['codigo']."'";
        $sql = "INSERT INTO gf_libros (nombre_libro, codigo_libro) "
             . "VALUES ($nombre, $codigo)";
        $res = $mysqli->query($sql);
        echo json_decode($res);
    break;
    #***************Modificar Libro************************#
    case(2):
        $id        = $_POST['id'];
        $nombre    = "'".$_POST['nombre']."'";
        $codigo    = "'".$_POST['codigo']."'";
        $sql = "UPDATE gf_libros "
             . "SET nombre_libro =$nombre, codigo_libro=$codigo "
             . "WHERE id_unico = $id";
        $res = $mysqli->query($sql);
        echo json_decode($res);
    break;
    #***************Eliminar Libro************************#
    case(3):
        $id         = $_POST['id'];
        $sql = "DELETE FROM gf_libros WHERE id_unico = $id ";
        $res = $mysqli->query($sql);
        echo json_decode($res);
    break;
}
