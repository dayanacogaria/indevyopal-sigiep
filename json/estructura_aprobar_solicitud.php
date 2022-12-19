<?php 
	require_once('Conexion/conexion.php');
	//require_once('estructura_apropiacion.php');
	session_start();

	$estruc = $_REQUEST['estruc'];
	$id_comp = $_REQUEST['id_comp'];


	switch ($estruc) 
	{
		case 1:
			$_SESSION['id_compr_pptal'] = "";
			break;
			
		case 2:
			$_SESSION['id_compr_pptal'] = $id_comp;
			break;

	}

 ?>