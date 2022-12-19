<?php 
	//Llamado de la clase conexión
	require_once '../Conexion/conexion.php';
	//Creción de la sesión
	session_start();
	//Definición de la variable id con la captura del valor enviado por la 
	//url
	$id = $_GET['id'];
        #Eliminamos primero los datos existentes en la tabla perfil_tercero
    $perfil = "DELETE FROM gf_perfil_tercero WHERE tercero = $id and perfil=2";
        #Ejecutamos la consulta
    $resultado=$mysqli->query($perfil);
  	$sql1="select perfil from gf_perfil_tercero where tercero=$id";
  	$result1=$mysqli->query($sql1);
  	$cantidad = mysqli_num_rows($result1);
  	
  	if($cantidad==0 || empty($cantidad)){
		//Definición de la variable sql con la consulta y el valor de la variable
		//$id
		$sql = "DELETE FROM gf_tercero WHERE Id_Unico = $id";
		//Definición de la variable $sql con el valor devuelto de la consulta la
		//cual es cargada de forma embedida a la variable de conexión
		$resultado = $mysqli->query($sql);
	}
	//Imprimimos el valor retornado por la variable resultado como json
	echo json_encode($resultado);

 ?> 