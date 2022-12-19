<?php
##########MODIFICACIONES#############
#01/02/2017 | 10:30 ERICA GONZÁLEZ. //Archivo Creado
#####################################
require_once '../Conexion/conexion.php';
session_start();
$referencia =  $_GET['term'];
$query = "SELECT CONCAT(codi_cuenta,' - ', nombre) AS CUENTA "
        . "FROM gf_cuenta "
        . "WHERE codi_cuenta LIKE '%$referencia%'";
$result = $mysqli->query($query);
$data = array();
if($result->num_rows > 0){
    while ($row = $result->fetch_assoc()){
        $data[] = ucwords(mb_strtolower($row['CUENTA']));
    }
    echo json_encode($data);
}
?>