<?php
	header("Content-Type: text/html;charset=utf-8");
	require_once('Conexion/conexion.php');    
	session_start();

	$estruc = $_POST['estruc'];

	switch ($estruc)  {
        case 1: // Permite crear una tabla en la base de datos a partir de los campos ingresados por el usuario desde el archivo crear_tabla.php.
            $n = $_POST['n'];
            $nombre = $_POST['nombre'];
            $nombre = strtolower($nombre);
            $errores = "";

            $campos = '';
            $attrCampos = '';

            $tipo = array(0 => '', 1 => 'int', 2 => 'varchar');
            $indice = array(0 =>'', 1 => 'primary key', 2 => 'unique key', 3 => 'key' );

            $completo = unserialize(stripcslashes($_POST['completo']));
            for($i = 0; $i < $n; $i++) {
                $cadena = $completo[$i];
                $fila = unserialize(stripcslashes($cadena));
                for($y = 0; $y < 5; $y++) {
                    $matriz[$i][$y] = $fila[$y];
                }
			}

			for($i = 0; $i < $n; $i++) {
				$campos .= ' '.$matriz[$i][0];
				$tip = $matriz[$i][1];
				$campos .= ' '.$tipo[$tip];

				if($matriz[$i][2] != 0) {
					$campos .= '('.$matriz[$i][2].')';
				}

				if($matriz[$i][3] == 1) {
					$campos .= ' not null';
				}

				if($i != $n -1 ) {
					$campos .= ',';
				}

				if($matriz[$i][4] == 2) {
					$attrCampos .= ', unique key '.$matriz[$i][0].' ('.$matriz[$i][0].')';
				}
			}

			$sql = 'create table if not exists '.$nombre.' (id_unico int(11) not null auto_increment, '.$campos.', primary key(id_unico) '.$attrCampos.')';

  		 	$resultado = $mysqli->query($sql);
  			$errores = $mysqli->error;

           	if($errores == "") {
            	if($resultado == true) {
  					$_SESSION['tabla_creada'] = $nombre;
  					echo 2; //Sí se creó la tabla.
  				} else {
  					echo 1; //No se creó la tabla.
  				}
            } else {
            	echo $errores; //Errores de consulta al crear la tabla.
            }
		break;
        case 2: // Valida si el nombre de la tabla que llega por parámetro [nombre] ya existe en la base de datos.
			$nombre = $_POST['nombre'];
			$nombre = strtolower($nombre);
			$num = 0;

			$sql = "show tables like '$nombre'";
			$resultado = $mysqli->query($sql);
			$num = $resultado->num_rows;
			if($num == 0) {
				echo 2;
			}  else {
				echo 1;
			}
		break;
        case 3:  //Valida su el número digitado como fila inicial o final es menor que el núemro de líneas o renglones que tienen el archivo.
            $archivo =  $_FILES['archivo']['tmp_name'];
            $fila = $_POST['fila'];

            $leeArchivo = fopen($archivo, "r");
    		$cont = 0;
    		while(!feof($leeArchivo)) {
                if ($linea = fgets($leeArchivo)) {
                    $cont ++;
                }
    		}
            //$cont --;
            echo $cont;
		break;
        case 4: //Valida que el separador se encuentre dentro de las líneas dadas por el usuario.
            $archivo =  $_FILES['archivo']['tmp_name'];
            $tabla = $_POST['tabla'];
            $separador = $_POST['separador'];
            $filaIni = $_POST['filaIni'];
            $filaFin = $_POST['filaFin'];

            $cont = 0;
			$file = file($archivo);

            foreach ($file as $indicador => $linea) {
                if($indicador >= $filaIni && $indicador <= $filaFin) {
                    $pos = strpos($linea, $separador);
                    if ($pos === false) {
                        echo 2;
				        exit();
                    }
                }
            }

            $tipoTabla = array();
            $tipoArchivo = array('int' => 'integer');
            //Valida que el archivo tenga el mismo número de columnas que la tabla.
            $sql = "SELECT data_type
                    FROM INFORMATION_SCHEMA.COLUMNS
				WHERE table_name = '$tabla'
                AND column_key != 'PRI'";
            $resultado = $mysqli->query($sql);
            $num = $resultado->num_rows;
            while($row = mysqli_fetch_row($resultado)) {
                $tipoTabla[] = $row[0];
            }
            $numTabla = $num; //count($tipoTabla);
			$file = file($archivo);
			foreach ($file as $indicador => $linea) {
                if($indicador >= $filaIni && $indicador <= $filaFin) {
        			$var = $linea;
    			    $va = explode($separador, $var);
    				$n = count($va);
    				if($n != $numTabla) {
                        echo 1;
                        exit();
    				}

                    for($i = 0; $i < $n; $i ++) {
                        $tipo = gettype($va[$i]);
                         /**/
                        if($tipo == 'integer') {
                            $tipoArchivo[] = 'int';
                        } elseif ($tipo == 'string') {
                            $tipoArchivo[] = 'varchar';
                        }

                        if($tipoTabla[$i] != $tipoArchivo[$i]) {
                            echo 3;
                            exit();
                        }
                    }
			    }
            }
		break;
		case 5:
			$archivo = $_FILES['archivo']['tmp_name'];
			$separador = $_POST['separador'];
			$cont = 0;

			$leeArchivo = fopen($archivo, "r");
			while(!feof($leeArchivo)) {
				$linea = fgets($leeArchivo);
				$pos = strpos($linea, $separador);
				if ($pos === false) {
					$cont =  1;
					break;
    			}
			}
			echo $cont;
		break;
	}
 ?>
