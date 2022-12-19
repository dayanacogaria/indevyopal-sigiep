<?php 
#######################################################################################################
#                           Modificaciones
#######################################################################################################
#29/09/2017 |Erica G. | ARCHIVO CREADO
#######################################################################################################
require_once('../Conexion/conexion.php');
session_start(); 
$action= $_REQUEST['action'];
$anno     = $_SESSION['anno'];
$compania =$_SESSION['compania'];
switch ($action){
    #**********Cargar Meses Según Año Ordenado ASC****************#
    case 1:
        $annio = $_POST['anno'];
        $ms = "SELECT id_unico, numero, lower(mes) FROM gf_mes WHERE parametrizacionanno = $annio ORDER BY numero ASC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>". ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay meses </option>";
        }
    break;
     
}
