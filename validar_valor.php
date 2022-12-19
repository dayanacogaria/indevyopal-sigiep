<?php
	require_once('estructura_apropiacion.php');
	session_start();


	$IDRubroFuente = $_REQUEST['id_rubFue'];

	echo $saldoDisponible = apropiacion($IDRubroFuente) - disponibilidades($IDRubroFuente);

?>
