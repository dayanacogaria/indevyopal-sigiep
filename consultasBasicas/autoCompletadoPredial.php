<?php
require_once '../Conexion/conexion.php';
session_start();
$referencia =  $_GET['term'];
$query = "SELECT codigo_catastral "
        . "FROM gp_predio1 "
        . "WHERE codigo_catastral LIKE '%$referencia%'";
$result = $mysqli->query($query);
$data = array();
if($result->num_rows > 0){
    while ($row = $result->fetch_assoc()){
        $data[] = $row['codigo_catastral'];
    }
    echo json_encode($data);
}
?>

