<?php 
    require_once('../Conexion/conexion.php');
    session_start();

    $id = $_GET['id'];
    #BUSCAR CUANTOS DETALLES TIENE ASOCIADOS
    $detalles = "SELECT * FROM gg_detalle_proceso WHERE proceso ='$id'";
    $detalles = $mysqli->query($detalles);
    #SI TIENE MAS DE 1 DETALLE NO HACE NADA
    if(mysqli_num_rows($detalles)>1){
        $resultado =false;
    } else {
        #ELIMINAR EL DETALLE QUE TIENE ASOCIADO
        $delDetalle = "DELETE FROM gg_detalle_proceso WHERE proceso ='$id'";
        $delDetalle = $mysqli->query($delDetalle);
        if($delDetalle==true){
            $query = "DELETE FROM gg_proceso WHERE id_unico = $id";
            $resultado = $mysqli->query($query);
        } else {
            $resultado=false;
        }
    }
    

    echo json_encode($resultado);
?>