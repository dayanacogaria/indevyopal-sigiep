<?php
	require_once('Conexion/conexion.php');
	session_start();

	$sqlRiesgo = "SELECT id_unico, nombre	
        FROM gy_riesgo
        WHERE tipo_riesgo = ".$_REQUEST['id_TipoR']." ";

 	$riesgo = $mysqli->query($sqlRiesgo);
        echo '<option value="">Riesgo</option>';
	while ($row = mysqli_fetch_row($riesgo))
	{
    	echo '<option value="'.$row[0].'">'.($row[1]).'</option>';
	} 

?>