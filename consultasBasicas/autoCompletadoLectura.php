<?php
require_once '../Conexion/conexion.php';
session_start();
$referencia =  $_GET['term'];
$query = "SELECT referencia "
        . "FROM gp_unidad_vivienda_medidor_servicio uvms "
        . "LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico WHERE m.referencia LIKE '%$referencia%'";
$result = $mysqli->query($query);
$data = array();
if($result->num_rows > 0){
    while ($row = $result->fetch_assoc()){
        $data[] = $row['referencia'];
    }
    echo json_encode($data);
}
?>
