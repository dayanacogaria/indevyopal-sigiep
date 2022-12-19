<?php 
	
	require_once('Conexion/conexion.php');
	session_start();
        $param = $_SESSION['anno'];
	$codigo = $_REQUEST['codigo'];
	$num = 0;
	
	$queryCodigo = "SELECT codi_presupuesto FROM gf_rubro_pptal WHERE codi_presupuesto = '".$codigo."' AND parametrizacionanno = $param";
	$codigo = $mysqli->query($queryCodigo);
	$num = $codigo->num_rows;

	echo $num;
	
 ?>