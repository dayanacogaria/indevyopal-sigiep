<?php
	require_once('Conexion/conexion.php');
	session_start();

	$sqlDepto = "SELECT Id_Unico, Nombre 
  	FROM gf_departamento
  	ORDER BY Nombre ASC";
	$depto = $mysqli->query($sqlDepto);

	echo '<option value="">Departamento</option>';
	while ($row = mysqli_fetch_row($depto))
	{
		echo '<option value="'.$row[0].'">'.($row[1]).'</option>';
	}

?>
