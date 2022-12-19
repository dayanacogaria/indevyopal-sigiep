<?php 
#####################################################################################################################################
# Fecha de Creación: 	24-03-2017
# Creado por:			Jhon Numpaque
# Descriptción:			Archivo de respuesta en el cual se generan los tipos de archivos para el generador de informes como salida
#####################################################################################################################################
session_start();
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Archivos externos
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
require '../Conexion/conexion.php';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Inicializamos las variables con vacio o nulo
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$nombreI = "";										//Nombre del informe
$consulta = "";										//Variable con la consulta a realizar
$num_filas = 0;										//Número de filas
$num_cols = 0;										//Número de columnas
$errores = "";										//Variable de captura de errores
$info_campo = "";									//variable para obtener los nombres de los campos
$cols_nom = array();								//Array para capturar los nombres de las columnas
$nom_cols = "";										//String de captura de los mombres de las columnas de manera lineal
$csv = "";											//Variable para generar csv
$shtml = "";										//Variable de armado de html
$separador = "";									//Variable para recibir el separador
$lineas = "";										//Variable para obtener las lineas del archivo txt
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Inicializamos la variable archivo con el valor enviado
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$archivo = $_REQUEST['tipoArchivo'];
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Capturamos los valores enviados por post
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$nombreI = $_POST["nombreI"];
$consulta = $_POST['consulta'];
$result = $mysqli->query($consulta);				//Ejecutamos la consulta
$errores = $mysqli->error;							//Capturamos los errores que tenga la consulta
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Validamos que la variable errores este vacia
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(empty($errores)) {
	$num_filas = $result->num_rows; 				//Obtenemos la cantidad de filas
	if($num_filas > 0) { 							//Validamos que el numero de filas sea mayor a 0
		$num_cols = $mysqli->field_count; 			//Obtenemos el número de columnas				
	}
	for($i = 0;$i < $num_cols;$i++){ 				//Ciclo para obtener el nombre de las columnas
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Cargamos la variable info_name, con el result retornado por la consulta y la posción
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$info_campo = mysqli_fetch_field_direct($result,$i);
		$cols_nom[$i] = $info_campo->name; 			//Obtenemos el nombre del campo
	}	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//Switch case para indicar que tipo de archivo sera devuelto a retornado por medio del valor enviado de la variable archivo
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	switch ($archivo) {
		case 1: //archivo .csv			
			$separador = $_POST['separador']; 		//Capturamos la variable seprador
			if($separador == 'tab') {				//Validamos cuando su valor es tab
				$separador = "\t";					//Le asignamos a la variable separador \t el cual es tab en un string
			}
			if(empty($separador)) {					//Valiamos que el tabulador se encuentre vacio, si esta vacio
				$separador = ",";					//asignamos , como separador
			}
			$csvName = $nombreI.'.csv';				//Nombre del archivo
			for($a = 0;$a < $num_cols;$a++){ 		//Desplegamos el array para obtener el nombre de los campos
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//Capturamos los nombres en un string con el separador
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$nom_cols .= $cols_nom[$a]."$separador";		
			}		
			$csv .= utf8_decode($nom_cols)."\n";					//Denominamos la variable csv con el el nombre de las columnas
			$resultCsv = $mysqli->query($consulta);	//Ejeuctamos la consulta para obtener los valores
			while($rowCsv = mysqli_fetch_row($resultCsv)){
				for ($b=0; $b < $num_cols; $b++) { 	//Ciclo para obtener una fila con los valores devueltos
					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					//Capturamos los valores de la fila y los separamos usando la variable $separador
					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					$csv .= $rowCsv[$b]."$separador";
				}
				//$csv .= substr($csv,0,strlen($csv)-1);
				$csv .= "\n";						//Hacemos un salto cuando la linea se complete
			}			
			if(!$handle = fopen('../documentos/generador_informes/csv/'.$csvName, "w")){	//Abrimos y validamos para abrir el archivo
				echo "No se puede abrir el archivo";
				exit;
			}
			//Validamos para escribir las lineas en el archivo			
			if(fwrite($handle,$csv) === FALSE) {
				echo "No se puede escribir el fichero";
				exit;
			}
			fclose($handle);						//Cerramos el archivo
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//Obtenemos la localización del archivo
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$path_file = '../documentos/generador_informes/csv/'.$csvName;
			if(file_exists($path_file)){			//Validamos que el archivo exista
				echo "1;$path_file";				//Imprimimos verdaderos y el archivo
			}
			break;
		
		case 2: //archivo .xls			
			$xlsName = $nombreI.'.xls';				//Nombre del archivo
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			//Creamos el bosquejo de una tabla html
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$shtml .= "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">";
			$shtml .= "<thead>";					//Cabeza de la tabla
			$shtml .= "<tr>";
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			//Nombre del informe
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$shtml .= "<th colspan=\"$num_cols\">".ucwords(mb_strtolower($nombreI))."</th>";
			$shtml .= "</tr>";
			$shtml .= "<tr>";						//Fila para el nombre de las columnas
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			//Desplegamos los nombres de las columnas
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			for($c = 0; $c < $num_cols; $c++) {
				$shtml .= "<th>".ucwords(strtolower(utf8_decode($cols_nom[$c])))."</th>";
			}
			$shtml .= "</tr>";	
			$shtml .= "</thead>";
			$shtml .= "<tbody>";					//Cuerpo de la tabla
			$result = $mysqli->query($consulta);
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			//Obtenemos los valores de la consulta y los asignamos a las filas de las columnas
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			while ($row = mysqli_fetch_row($result)) {
				$shtml .= "<tr>";
				for ($d=0; $d < $num_cols; $d++) {	//Ciclo para desplegar las columnas en el array row 
					$shtml .= "<td>".ucwords(strtolower($row[$d]))."</td>";
				}
				$shtml .= "</tr>";
			}
			$shtml .= "</tbody>";					//Cierre de cuerpo de la tabla
			$shtml .= "</table>";
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			//Obtenemos la localización del archivo
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
			$sfile = '../documentos/generador_informes/xls/'.$xlsName;
			$fp=fopen($sfile,"w" ); 				//Abrimos el archivo en modo de escritura
			fwrite($fp,$shtml); 					//Escribimos el html del archivo
			fclose($fp); 							//Cerramos el archivo
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			//Validamos que el archivo exista
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
			if(file_exists($sfile)){				
				echo "1;$sfile";					//Imprimimos verdaderos y el archivo
			}
			break;

		case 3: //archivo .txt
			$separador = $_POST['separador']; 		//Capturamos la variable seprador
			if($separador == 'tab') {				//Validamos cuando su valor es tab
				$separador = "\t";					//Le asignamos a la variable separador \t el cual es tab en un string
			}
			if(empty($separador)) {					//Valiamos que el tabulador se encuentre vacio, si esta vacio
				$separador = ",";					//asignamos , como separador
			}
			$txtName = $nombreI.".txt";				//Nombre de informe con la extención txt
			//Inicalizamos la variable lineas en vacio
			$lineas .= "";				
			$result = $mysqli->query($consulta);
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			//Obtenemos los valores de la consulta y los asignamos a las filas de las columnas
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			while ($row = mysqli_fetch_row($result)) {
				for ($e=0; $e < $num_cols; $e++) { 	//Ciclo para desplegar las columnas en el array row 				
					$lineas .= $row[$e]."$separador";
				}
				$lineas .= "\n";					//Cuando completemos la linea se realizara salto de linea
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			//Obtenemos la localización del archivo
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
			$sfile = '../documentos/generador_informes/txt/'.$txtName;
			$fp=fopen($sfile,"w" ); 				//Abrimos el archivo en modo de escritura
			fwrite($fp,$lineas); 					//Escribimos el html del archivo
			fclose($fp); 							//Cerramos el archivo
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			//Validamos que el archivo exista
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
			if(file_exists($sfile)){				
				echo "1;$sfile";					//Imprimimos verdaderos y el archivo
			}
			break;

		case 4: //archivo .xml
			/*$xmlName = $nombreI.'.xml';				//Nombre del archvio
			$xml = new DOMDocument('1.0','UTF-8');	//Creamos el archvo xml			
			$informe = $xml->createElement("Informe");
			$informe = $xml->appendChild($informe);
			$result = $mysqli->query($consulta);
			while($row = mysqli_fetch_row($result)){
				for ($f=0; $f < $num_cols; $f++) { 
					$columna = $xml->createElement($cols_nom[$f]);
					$columna = $informe->appendChild($columna);
					$val = (string) $row[$f];
					$valor = $xml->createElement("1");
					$columa = $appendChild($columna);
				}
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			//Ruta y nombre del archivo
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$sfile = '../documentos/generador_informes/xml/'.$xmlName;
			$xml->formatOutput = true;				//Aplicar formato xml
			$xml->saveXML();
			$xml->save($sfile);						//Salida y guardado del archivo xml
			break; En proceso, convertir valores numericos a texto o buscar metodos*/
	}
}else{
	echo $errores;
}
 ?>