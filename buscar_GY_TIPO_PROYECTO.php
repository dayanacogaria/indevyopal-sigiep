<?php
	require_once('Conexion/conexion.php');
	session_start();

	$sqlTipoP= "SELECT id_unico, nombre 
  	FROM gy_tipo_proyecto";
	$TipoP = $mysqli->query($sqlTipoP);

	echo '<option value="">Tipo Proyecto</option>';
	while ($row = mysqli_fetch_row($TipoP))
	{
		echo '<option value="'.$row[0].'">'.($row[1]).'</option>';
	}

?>
