<?php 

	require_once('Conexion/conexion.php');

	$id_fuente = $_REQUEST['id_fuente']; 
	$id_rubro = $_REQUEST['id_rubro'];

	$queryRubFue = "SELECT id_unico FROM gf_rubro_fuente WHERE rubro = $id_rubro AND fuente = $id_fuente";
	$rubroFuente = $mysqli->query($queryRubFue);
	$row = mysqli_fetch_row($rubroFuente);
	$id_rub_fue = $row[0];

	if($id_rub_fue == 0)
	{
		echo 2; //Sí. No existe este registro así que puede guardarse.
	}
	else
	{
		echo 1; //No. Ya existe un registro repetido por tanto no puede guardarse.
	}

 ?>