

<?php
    require_once('../Conexion/conexion.php');
    session_start();

    $id = $_GET['id'];
    
    $query = "DELETE FROM gs_actualizacion_archivo WHERE id_unico = $id";
    $resultado = $mysqli->query($query);


    echo json_encode($resultado);
?>