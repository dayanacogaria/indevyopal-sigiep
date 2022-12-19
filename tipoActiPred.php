<?php

	require_once('Conexion/conexion.php');
	session_start();

	if(empty($_REQUEST['id_predecesor'])){
		$queryTipoAct = "SELECT id_Unico, nombre FROM gf_tipo_activo ORDER BY nombre ASC";
		$tipoAct = $mysqli->query($queryTipoAct);
		echo '<option value="">Tipo Actividad</option>';
		while($row = mysqli_fetch_row($tipoAct)){
			echo '<option value="'.$row[0].'" >'.ucwords(mb_strtolower($row[1])).' </option>';
		}
	}else{
		$queryTipoAct = "SELECT ta.id_unico, ta.nombre FROM gf_plan_inventario pi, gf_tipo_activo ta
		WHERE pi.tipoactivo = ta.id_unico AND pi.id_unico = ".$_REQUEST['id_predecesor'];
		$tipoAct = $mysqli->query($queryTipoAct);
		$row = mysqli_fetch_row($tipoAct);
		echo '<option value="'.$row[0].'" selected="selected">'.ucwords(mb_strtolower($row[1])).' </option>';
		$queryTipoAct = "SELECT id_Unico, nombre FROM gf_tipo_activo WHERE id_Unico != $row[0] ORDER BY nombre ASC";
		$tipoAct = $mysqli->query($queryTipoAct);
		echo '<option value="">Tipo Actividad</option>';
		while($row = mysqli_fetch_row($tipoAct)){
			echo '<option value="'.$row[0].'" >'.ucwords(mb_strtolower($row[1])).' </option>';
		}
	}

 ?>