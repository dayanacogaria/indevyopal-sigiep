<?php 
#MODIFICADO 03/03/2017 11:43 Ferney Pérez 
#MODIFICADO 27/01/2017 FERNEY
	require_once('Conexion/conexion.php');
	session_start();

	/* Creado Ferney Pérez 
27/01/2017 | 16:54
*/

	$estruc = $_POST['estruc'];
	
	switch ($estruc) 
	{
		case 1:
			$idCompCnt = $_POST['idCompCnt'];
			$_SESSION['idCompCnt'] = $idCompCnt;
			break;
	}

 ?>