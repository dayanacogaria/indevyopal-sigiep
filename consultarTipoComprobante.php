<?php 

	require_once 'Conexion/conexion.php';

	session_start();	

	if(!empty($_POST["cod"]) || !isset($_POST["cod"])){
		
		
		$cod = $_POST["cod"];
			
			$sql = "SELECT DISTINCT tcp.id_unico, tcp.codigo, tcp.nombre, tcp.obligacionafectacion, tcp.terceroigual, tcp.clasepptal, cp.nombre, tcp.formato, f.nombre, tcp.tipooperacion, t.nombre   
				  FROM gf_tipo_comprobante_pptal tcp 
				  LEFT JOIN  gf_clase_pptal cp ON tcp.clasepptal=cp.id_unico 
				  LEFT JOIN gf_formato f ON tcp.formato=f.id_unico
				  LEFT JOIN gf_tipo_operacion t ON tcp.tipooperacion=t.id_unico
				  WHERE tcp.codigo = '$cod' AND parametrizacionanno = ".$_SESSION['anno'];


	    $resultado = $mysqli->query($sql);

	    $filas =  mysqli_num_rows($resultado);

	    if ($filas != 0) {
	    	while ($row = mysqli_fetch_row($resultado)) {
				echo '<input type="hidden" name="id" id="id" value="'.md5($row[0]).'"/>;true';
				print($filas);						
	  		}
	    }else{
	    	echo 'false;0';
	    }	   	
	}
 ?>