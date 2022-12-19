<?php
	require_once('Conexion/conexion.php');
	session_start();

	$sqlProyecto = "SELECT id_unico, titulo	
        FROM gy_proyecto
        WHERE id_tipo_proyecto = ".$_REQUEST['id_TipoP']." ";

 	$Proyecto = $mysqli->query($sqlProyecto);
        echo '<option value="">Proyecto</option>';
	while ($row = mysqli_fetch_row($Proyecto))
	{
    	echo '<option value="'.$row[0].'">'.($row[1]).'</option>';
	} 

?>
