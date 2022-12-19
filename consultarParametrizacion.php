<?php 

	require_once 'Conexion/conexion.php';

	session_start();	

	if(!empty($_POST["anio"]) || !isset($_POST["anio"])){
		
		
		$anio = $_POST["anio"];
			
			$sql = "SELECT DISTINCT p.Id_Unico , p.anno, p.salariominimo, p.mindepreciacion, p.uvt, p.cajamenor
				FROM gf_parametrizacion_anno p
				WHERE p.anno = '$anio'";


	    $resultado = $mysqli->query($sql);

	    $filas =  mysqli_num_rows($resultado);

	    if ($filas != 0) {
	    	while ($row = mysqli_fetch_row($resultado)) {
				echo '<input type="text" name="id" id="id" value="'.md5($row[0]).'"/>;true';
				print($filas);						
	  		}
	    }else{
	    	echo 'false;0';
	    }	   	
	}
 ?>