<?php
	require_once('Conexion/conexion.php');
	session_start();

	//Selecciona ID y nombre del departamento actual.
	$queryCiudadDepto ="SELECT Id_Unico, Nombre
		FROM gf_departamento
    	WHERE Id_Unico = "."'".$_REQUEST['id_ciudad_depto']."'";

	$ciudadDepto = $mysqli->query($queryCiudadDepto);
	$rowCD = mysqli_fetch_row($ciudadDepto);

	//Selecciona los departametos disponibles en la tabla gf_departamento.
	$sqlDepto = "SELECT Id_Unico, Nombre 
  		FROM gf_departamento
  		WHERE Id_Unico != "."'".$_REQUEST['id_ciudad_depto']."'"." 
  		ORDER BY Nombre ASC";
	$depto = $mysqli->query($sqlDepto);

	//Imprime en option el nombre del departamento actual.
	echo '<option value="'.$rowCD[0].'">'. ($rowCD[1]).'</option>';
	while($rowD = mysqli_fetch_row($depto))
	{	
		echo '<option value="'.$rowD[0].'">'. ($rowD[1]).'</option>'; //Imprime la lista de los departamentos.
	}

?>
