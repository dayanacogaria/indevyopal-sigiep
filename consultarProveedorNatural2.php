<?php 

	require_once 'Conexion/conexion.php';

	session_start();	

	if(!empty($_POST["num"]) || !isset($_POST["num"])){
		
		
		$num = $_POST["num"];
			
			$sql = "SELECT DISTINCT
					   T.Id_Unico ,  
					   T.NumeroIdentificacion
				FROM gf_tercero T 
				LEFT JOIN gf_tipo_identificacion TI 
				ON T.TipoIdentificacion = TI.Id_Unico 
				LEFT JOIN gf_tipo_regimen TR 
				ON T.TipoRegimen = TR.Id_Unico	
				LEFT JOIN gf_zona Z 
				ON T.Zona = Z.Id_Unico 
				WHERE T.NumeroIdentificacion = '$num'";


	    $resultado = $mysqli->query($sql);

	    $filas =  mysqli_num_rows($resultado);

	    if ($filas != 0) {
	    	while ($row = mysqli_fetch_row($resultado)) {
				echo '<input type="hidden" name="id" value="'.md5($row[0]).'"/>;true';
				print($filas);						
	  		}
	    }else{
	    	echo 'false;0';
	    }	   	
	}
 ?>