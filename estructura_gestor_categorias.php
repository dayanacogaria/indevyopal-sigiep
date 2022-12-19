<?php
	require_once('Conexion/conexion.php');
	require_once ('nombreBaseDatos.php');
	session_start();
	
	//$baseDatos = 'u858942576_sigep';
	//$baseDatos = 'sigep';

	$estruc = $_POST['estruc'];
	
	switch ($estruc) 
	{
		case 1:
			$tabla = $_POST['tabla'];
			
			$sqlColDestino = "SELECT column_name 
				FROM INFORMATION_SCHEMA.COLUMNS 
				WHERE table_name = '$tabla' 
				AND table_schema = '$baseDatos' 
				AND column_key != 'PRI'";
            $colDestino = $mysqli->query($sqlColDestino);
            while($rowCD = mysqli_fetch_row($colDestino))
            {
            	echo '<option value="'.$rowCD[0].'">'.ucwords(strtolower($rowCD[0])).'</option>';
            }
			break;
		case 2:
			$id = $_POST['id'];
			$catFor = $_POST['catFor'];
			$consulta = $_POST['consulta']; 
			$errores = "";
			$num_filas = 0;
			$num_columnas = 0;

			$sqlConsulta = $consulta;
            $consultaF = $mysqli->query($sqlConsulta);
            $errores = $mysqli->error;
            
            if($errores == "")
            {
            	$num_filas = $consultaF->num_rows;
				if($num_filas > 0)
				{
					$num_columnas = $mysqli->field_count;
					$columnas_gi = array();
  					for ($i=0; $i < $num_columnas; $i++)
  					{ 
  						$info_campo = mysqli_fetch_field_direct($consultaF, $i);
    					$columnas_gi[$i] = $info_campo->name;;
  					}
  					$columnas_gi_ser = serialize($columnas_gi);

  					$_SESSION['columnas_gi'] = $columnas_gi_ser;
					$_SESSION['consulta_gi'] = $consulta;
					$_SESSION['id_gi'] = $id;
					$_SESSION['catFor_gi'] = $catFor;
					echo 1;
				}
				else
				{
					echo 0;
            	}
            }
            else
            {
            	echo $errores;
            } 
			break;
		case 3:
			$nombre  = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
			$idCategoria = '"'.$mysqli->real_escape_string(''.$_POST['idCategoria'].'').'"';
			$sqlInsert = "INSERT INTO gn_variables (nombre, categoria) 
    			VALUES($nombre, $idCategoria)";
  			$resultado = $mysqli->query($sqlInsert);
  			if($resultado == true)
  			{
  				echo 1;
  			}
  			else
  			{
  				echo 0;
  			}
			break;
		case 4:
			$nombre  = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
			$nombre  = strtolower($nombre);
			$id = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';

			$update = "UPDATE gn_variables 
    			SET nombre = $nombre 
    			WHERE id_unico = $id";
  			$resultado = $mysqli->query($update);
  			if($resultado == true)
  			{
  				echo 1;
  			}
  			else
  			{
  				echo 0;
  			}
			break;
		case 5:
			$catFor = $_POST['catFor'];

			$sqlVariables = "SELECT id_unico, nombre 
				FROM gn_variables 
				WHERE categoria = '$catFor'";
            $variables = $mysqli->query($sqlVariables);
            while($rowV = mysqli_fetch_row($variables))
            {
            	echo '<option value="'.$rowV[0].'">'.ucwords(strtolower($rowV[1])).'</option>';
            }
			break;
		case 6: 
			$id_variable = $_POST['variable'];

			$sqlConsultaE = "SELECT  consultasql
				FROM gn_variables 
				WHERE id_unico = '$id_variable'";
            $consultaE = $mysqli->query($sqlConsultaE);
            $rowCE = mysqli_fetch_row($consultaE);
            echo $rowCE[0];
			break;
		case 7:
			$consulta  = '"'.$mysqli->real_escape_string(''.$_POST['consulta'].'').'"';
			$consulta  = strtolower($consulta);
			$id = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';

			$update = "UPDATE gn_variables 
    			SET consultasql = $consulta 
    			WHERE id_unico = $id";
  			$resultado = $mysqli->query($update);
  			if($resultado == true)
  			{
  				echo 1;
  			}
  			else
  			{
  				echo 0;
  			}
			break;

	}

 ?>