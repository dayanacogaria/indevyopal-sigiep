<?php
	require_once('Conexion/conexion.php');
	session_start();

	$sqlProyecto = "SELECT id_unico, nombre	
        FROM gy_tipo_pregunta
        WHERE id_clase_pregunta = ".$_REQUEST['id_TipoP']." ";

 	$Proyecto = $mysqli->query($sqlProyecto);
        echo '<option value="">Tipo Pregunta</option>';
	while ($row = mysqli_fetch_row($Proyecto))
	{
    	echo '<option value="'.$row[0].'">'.($row[1]).'</option>';
	} 

?>
