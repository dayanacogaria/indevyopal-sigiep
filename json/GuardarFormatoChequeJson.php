<?php 
	require ('../Conexion/conexion.php');	
	session_start();
		
	$idFormato = $_POST['idFormato'];	
	$rutaValoresP = $_POST['txtPixelex'];
	
	$sql = "UPDATE gf_formato SET rutaFormatoCheque= '$rutaValoresP' WHERE id_unico =$idFormato";	
	$result = $mysqli->query($sql);
	echo json_encode($result);
 ?>