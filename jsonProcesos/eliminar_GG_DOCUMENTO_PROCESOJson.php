<?php 
    require_once('../Conexion/conexion.php');
    session_start();

    $id = $_GET['id'];
    $ruta = $_GET['ruta'];
    $archivo ="SELECT * FROM gg_documento_proceso WHERE ruta='$ruta'";
    $archivo = $mysqli->query($archivo);
    $num = mysqli_num_rows($archivo);
    if($num>1){
        $query = "DELETE FROM gg_documento_proceso WHERE id_unico = '$id'";
        $resultado = $mysqli->query($query);
    } else {
        $ruta = '../'.$ruta;
        var_dump(unlink($ruta));
        if(unlink($ruta)==true){
            $do = unlink($ruta);
            $query = "DELETE FROM gg_documento_proceso WHERE id_unico = '$id'";
            $resultado = $mysqli->query($query);
        } else {
            $query = "DELETE FROM gg_documento_proceso WHERE id_unico = '$id'";
            $resultado = $mysqli->query($query);
        }
    }
    echo json_encode($resultado);
?>