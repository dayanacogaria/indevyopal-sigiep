<?php

require_once('Conexion/conexion.php');
    session_start();
    
    $uniE = "SELECT DISTINCT ue.id_unico, ue.nombre FROM gn_empleado e LEFT JOIN gn_unidad_ejecutora ue ON e.unidadejecutora = ue.id_unico "
            . "WHERE ue.id_unico IS NOT NULL";
    $Ueje = $mysqli->query($uniE);
    
    echo '<option value="">Unidad Ejecutora</option>';
	while ($row = mysqli_fetch_row($Ueje))
	{
		echo '<option value="'.$row[0].'">'.($row[1]).'</option>';
	}
?>

