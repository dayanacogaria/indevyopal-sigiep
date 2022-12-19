<?php 
    require_once('../Conexion/conexion.php');
    session_start();

    $id = $_GET['id'];
    $ruta = $_GET['ruta'];
    $archivo ="SELECT * FROM gg_documento_detalle_proceso WHERE ruta='$ruta'";
    $archivo = $mysqli->query($archivo);
    $num = mysqli_num_rows($archivo);
    if($num>1){
        $query = "DELETE FROM gg_documento_detalle_proceso WHERE id_unico = '$id'";
        $resultado = $mysqli->query($query);
    } else {
        $ruta = '../'.$ruta;
        $do = unlink($ruta);
        if($do == true){
            $query = "DELETE FROM gg_documento_detalle_proceso WHERE id_unico = '$id'";
            $resultado = $mysqli->query($query);
        } else {
            $resultado=false;
        }
    }
    echo json_encode($resultado);
?>