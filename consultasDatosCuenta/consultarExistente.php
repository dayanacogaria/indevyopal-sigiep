<?php
require_once ('../Conexion/conexion.php');
session_start();
$anno = $_SESSION['anno'];
$id = $_POST['id'];
#BUSQUEDA
$bus = "SELECT * FROM gf_cuenta WHERE codi_cuenta ='$id' AND parametrizacionanno = $anno";
$bus = $mysqli->query($bus);
if(mysqli_num_rows($bus)>0){
    echo '1';
} else {
    echo '0';
}

