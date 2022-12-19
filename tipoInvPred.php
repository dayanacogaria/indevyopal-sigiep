<?php
	require ('Conexion/conexion.php');
	session_start();
	if(empty($_POST['id_predecesor'])){
		echo '<option value="">Tipo Inventario</option>';
		$queryTipoAct = "SELECT id_Unico, nombre FROM gf_tipo_inventario ORDER BY nombre ASC";
		$tipoAct = $mysqli->query($queryTipoAct);
		while($row = mysqli_fetch_row($tipoAct)){
			echo '<option value="'.$row[0].'" >'.ucwords(mb_strtolower($row[1])).' </option>';
		}
	}else{
		$queryTipoAct1 = "SELECT    tin.id_unico, tin.nombre FROM gf_plan_inventario pl
						  LEFT JOIN gf_tipo_inventario tin ON pl.tipoinventario = tin.id_unico
						  WHERE     pl.id_unico = ".$_POST['id_predecesor'];
		$tipoAct1 = $mysqli->query($queryTipoAct1);
		$row1 = mysqli_fetch_row($tipoAct1);
		echo "<option value=\"$row1[0]\">".ucwords(mb_strtolower($row1[1]))."</option>";
		$queryTipoAct = "SELECT     tin.id_unico, tin.nombre FROM gf_tipo_inventario tin
						 WHERE      tin.id_unico != $row1[0]";
		$tipoAct = $mysqli->query($queryTipoAct);
		while($row = mysqli_fetch_row($tipoAct)){
			echo '<option value="'.$row[0].'" >'.ucwords(mb_strtolower($row[1])).' </option>';
		}
	}
 ?>