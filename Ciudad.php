<?php
	require_once('Conexion/conexion.php');
	session_start();

	$sqlCiudad = "SELECT Id_Unico, Nombre	
        FROM gf_ciudad 
        WHERE Departamento = ".$_REQUEST['id_depto']." 
        ORDER BY Nombre ASC";

 	$ciudad = $mysqli->query($sqlCiudad);
        echo '<option value="">Ciudad</option>';
	while ($row = mysqli_fetch_row($ciudad))
	{
    	echo '<option value="'.$row[0].'">'.($row[1]).'</option>';
	} 

?>
