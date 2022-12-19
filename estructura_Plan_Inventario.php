<?php
	require_once('Conexion/conexion.php');
	session_start();

	//ID del padre.
	$id_predecesor = $_REQUEST['id_predecesor'];
	$idP = $id_predecesor;

	$row[0] = 1;
	while(($row[0] != NULL) || ($row[0] != 0))
	{
		$query = "SELECT predecesor, codi FROM gf_plan_inventario WHERE id_unico = ".$idP;
		$resultado = $mysqli->query($query);
		$row = mysqli_fetch_row($resultado);
		if(($row[0] != NULL) || ($row[0] != 0))
			$idP = $row[0];
	}
	$digitosGranPadre = strlen($row[1]);

	$num = 0;
	$evalua = 0;
	$codHijo = 0;
	$cuan = 0;
	$nivel = 0;

	//Consultar el código del padre.
	$queryCodPred = "SELECT codi from gf_plan_inventario WHERE id_unico =".$id_predecesor;
	$codPred = $mysqli->query($queryCodPred);
	$row = mysqli_fetch_row($codPred);//Código del padre.
	$codPadre = $row[0];
	$codPadre = (int)$codPadre;
	$cuan = strlen($codPadre);

	$totalDig = $digitosGranPadre + 7;
	if($cuan == $totalDig)
		$codHijo = 0; //Indica que no puede tener más hijos.
	else
	{
		//Consultar si el padre tiene hijos.
		$queryNoHij = "SELECT id_unico FROM gf_plan_inventario WHERE predecesor = ".$id_predecesor;
		$noHij = $mysqli->query($queryNoHij);
		$num = $noHij->num_rows; //Número de hijos.

		if($num != 0) //Si tiene hijos
		{	//Código del último hijo.
			$queryUltHij = "SELECT MAX(codi)  FROM gf_plan_inventario WHERE predecesor = ".$id_predecesor;
			$ultHij = $mysqli->query($queryUltHij);
			$rowUH = mysqli_fetch_row($ultHij);
			$codUltHij = $rowUH[0];
			$codHijo = $codUltHij + 1; //Código del nuevo hijo.

			$totalDig = $digitosGranPadre + 4;
			if($cuan == $totalDig)//Evaluar el codigo del nuevo hijo. Si está en el nivel 8.
			{
				$nivel = 8;
				$maxCod = ($codPadre * 1000) + 1000;
				if($maxCod == $codHijo)
				$codHijo = 0; //No puede tener más de 999 hijos.
			}
			else
			{
				$maxCod = ($codPadre * 100) + 100;
				if($maxCod == $codHijo)
				$codHijo = 0; //¿No puede tener más de 99 hijos?.
			}
			
		}
		else //Si no tiene hijos, será su primer hijo.
		{
			$totalDig = $digitosGranPadre + 4;
			if($cuan == $totalDig)//Evaluar el codigo del nuevo hijo.
			{
				$codHijo = $codPadre."001"; //Código del nuevo hijo.
				$nivel = 8;
			}
				
			else
				$codHijo = $codPadre."01"; //Código del nuevo hijo.
		}
	
	}

	echo $codHijo;
	echo $nivel;
?>
