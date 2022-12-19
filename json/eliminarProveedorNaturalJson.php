<?php
    //Llamado de la clase conexión
    require_once ('../Conexion/conexion.php');
    #Definición de la id
    $id = $_GET['id'];
    #Eliminamos primero el perfil existente en gf_perfil_tercero
    $per = "DELETE FROM gf_perfil_tercero WHERE tercero = $id and perfil=5";
    #Ejecutamos la consutla cargandola a la variable de conexión
    $resultado=$mysqli->query($per);

    $sql1="select perfil from gf_perfil_tercero where tercero=$id";
    $result1=$mysqli->query($sql1);
    $cantidad = mysqli_num_rows($result1);

    if($cantidad==0 || empty($cantidad)){
        #Consulta o query de eliminado
        $sql = "DELETE FROM gf_tercero WHERE id_unico = $id";
        #Cargamos la consulta a la variable conexión y el resultado a 
        #la variable $resultado
        $resultado = $mysqli->query($resultado);
        #Imprimimos el resultado de la consulta como json
    }
    echo json_encode($resultado);
?>

