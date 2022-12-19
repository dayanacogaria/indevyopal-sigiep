<?php
	require_once('Conexion/conexion.php');
	session_start();

	//Selecciona ID y nombre de la departamento actual.
	$sqlCiudad = "SELECT Id_Unico, Nombre FROM gf_ciudad 
		WHERE Departamento = ".$_REQUEST['id_ciudad_depto']." 
		AND Id_Unico != ".$_REQUEST['id_ciudad']." 
		ORDER BY Nombre ASC";
 	$ciudad = $mysqli->query($sqlCiudad);

 	//Selecciona ID y nombre de la ciudad actual.
 	$sqlCiudadAct = "SELECT Id_Unico, Nombre	
    	FROM gf_ciudad 
    	WHERE Id_Unico = ".$_REQUEST['id_ciudad'];

    $ciudadAct = $mysqli->query($sqlCiudadAct);
    $rowAct = mysqli_fetch_row($ciudadAct);


    echo '<option value="'.$rowAct[0].'" >'. ($rowAct[1]).'</option>'; 
	while ($rowC = mysqli_fetch_row($ciudad))
	{
    	echo '<option value="'.$rowC[0].'">'. ($rowC[1]).'</option>';
	}

?>
