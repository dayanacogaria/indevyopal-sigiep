<?php 

	//Llamado a la clase conexión
	require_once '../Conexion/conexion.php';
	//Obtenemos el parametro de eliminado
	$id = $_GET['id'];
	//Eliminamos el perfil en perfil_tercero
	$del = "DELETE FROM gf_perfil_tercero WHERE Tercero = $id";
	//Cargado de consulta para eliminar perfil tercero
	$resultado=$mysqli->query($del);

	$sql1="select perfil from gf_perfil_tercero where tercero=$id and perfil=6";
    $result1=$mysqli->query($sql1);
    $cantidad = mysqli_num_rows($result1);

    if($cantidad==0 || empty($cantidad)){
		//Consulta para eliminar tercero
		$query = "DELETE FROM gf_tercero WHERE Id_Unico = $id";
		//Cargado de la query en la variable de resultado 
		$resultado = $mysqli->query($query);
	}

	//Imprimimos el resultado como json
	echo json_encode($resultado);
 ?>