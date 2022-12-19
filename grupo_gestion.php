<?php

require_once('Conexion/conexion.php');
	session_start();

        $sqlgrupo ="   SELECT DISTINCT gg.id_unico, gg.nombre
                        FROM gn_empleado e 
                        LEFT JOIN gn_unidad_ejecutora ue ON e.unidadejecutora  = ue.id_unico
                        LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico
                        WHERE  ue.id_unico = ".$_REQUEST['id_unidad']."  
                        ORDER BY gg.id_unico ASC";

 	$GruG = $mysqli->query($sqlgrupo);
	while ($row = mysqli_fetch_row($GruG))
	{
    	echo '<option value="'.$row[0].'">'.($row[1]).'</option>';
	}

?>


