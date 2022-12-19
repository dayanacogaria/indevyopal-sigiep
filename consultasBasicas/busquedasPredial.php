<?php
require_once '../Conexion/conexion.php';
session_start();
$case = $_POST['case'];
switch ($case){
   case 1:
        if(!empty($_POST['codigo'])){
        $id = $_POST['codigo'];
        $idcom = strtolower($id);
        $sql="SELECT id_unico FROM gf_cuenta "
                . "WHERE codi_cuenta= '$idcom'";
        $sql= $mysqli->query($sql);
        $datos = mysqli_fetch_row($sql);
        $datos = $datos[0];
        } else {
            $datos ='';
        }
        echo json_encode($datos);
   break;
}
?>