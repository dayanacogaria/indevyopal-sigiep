<?php 
	require_once('Conexion/conexion.php');
	session_start();

	$estruc = $_REQUEST['estruc'];
	$id_comp = $_REQUEST['id_comp'];


	switch ($estruc) 
	{
		case 1:
			$_SESSION['id_compr_pptal'] = "";
			$_SESSION['nuevo_pptal'] = "";
			break;
			
		case 2:
			$_SESSION['id_compr_pptal'] = $id_comp;
			$_SESSION['nuevo_pptal'] = "";
			break;

		case 3:
			$_SESSION['id_compr_pptal'] = $id_comp;
			$_SESSION['nuevo_pptal'] = 1;
			break;

	}

 ?>