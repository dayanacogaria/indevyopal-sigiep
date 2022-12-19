<?php 
	
	require_once('Conexion/conexion.php');
	session_start();
	$codigo = $_REQUEST['codigo'];
	$num = 0;
	
	$queryCodigo = "SELECT codi FROM gf_plan_inventario WHERE codi = ".$codigo;
	$codigo = $mysqli->query($queryCodigo);
	$num = $codigo->num_rows;

	echo $num;
	
 ?>