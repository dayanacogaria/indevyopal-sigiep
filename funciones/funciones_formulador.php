<?php
/**
 * Creado por :          Alexander Numpaque
 * Fecha de Creación:    05/05/2017
 * @author 	Alexander Numpaque
 * @package funciones_formulador
 * @version $Id: funciones_formualdor.php 004 2017-05-10 Alexander Numpaque$
*/

/**
 * evalute_expression
 *
 * Esta función permite descubrir los valores de las variables por medio de la categoria a la que se relaciona la categoria formula
 *
 * @author Alexander N
 * @param  int 		 $category   $id de la categoria que se relaciona a categoria formula
 * @param  string	 $expression Formula en la cual se reemplazaran los valores
 * @param  undefined $needle 	 Puede ser de tipo Int, String, Date, etc. Este valor es valor a reemplzar en la consulta para la obtneción de los valores
 * @return int|float $x 		 Resultado de la expresión
*/
function evalute_expression($expression,$needle){
	include_once ('doc.php');
	try {
		$names    = search_patterns($expression,FALSE);             //Cargamos el array $names con los valores retornados
		$variants = search_patterns($expression,TRUE);              //Cargamos el array con los valores retornados usando un patron
		$query    = search_variants($names);                        //Cargamos el array query con las consultas                
		$sus_n    = array();                                        //Iniciamos array vacio para capturar los nombres
		for ($i = 0; $i < count($query); $i++) {                    //Recorremos el array de query para desplegar los valores
			if(!empty($needle)){                                    //Validamos que la aguja no esta vacia
				$w_n   = array();
				//Buscamos usando un patron las palabra encerradas en %%
				$regex = preg_match_all('/%([^%]*)%/i',$query[$i], $exit);
				if($regex > 0){
					if(stripos($needle[$i],",") !== false){
						$y = cambio_query($query[$i],$needle[$i]);												
						$sus_n[] = (float) execute_query($y);			//Capturamos el valor de la consulta en un array
					}else{
						//Reemplazamos los valores en donde %%
						$q = preg_replace('/%([^%]*)%/i',$needle[$i],$query[$i]);
						$sus_n[] = (float) execute_query($q);			//Capturamos el valor de la consulta en un array
					}
				}else{
					$sus_n[] = (float) execute_query($query[$i]);	//Ejecuta la consulta sin hacer reemplazos
				}
			}else{													//Si el needle esta vacio
				$sus_n[] = (float) execute_query($query[$i]);		//Ejecutamos la consulta
			}
		}
		$expression = str_replace('&','',$expression);				//Quitamos &&
		$expression = preg_replace($variants,$sus_n,$expression);	//Reemplazamos los valores en la expresión
		$expression = str_replace('?',',',$expression);				//Reemplazamos  ? por ,
		$expression = str_replace('==','=',$expression);			//Reemplazamos  == por =
		$x = generator_doc::exc_exel($expression);
		return $x;		
	} catch (Exception $e) {
		die($e->getMessage());	
	}
}

/**
 * see_look
 *
 * Esta función obtiene primero el sql relacionado a la variable, y la ejecuta la cual la cual retorna el valor de la consulta
 *
 * @author Alexander N
 * @param  int 		  $concept   Nombre el cual se encuentra registrado en la tabla gn_variables
 * @return undefined  $expresion Retorna el valor que se encuentra en la tabla gn variables, valor retornado
*/
function see_look($concept,$expression = 0){
	require ('../Conexion/conexion.php');
	//Consulta para obtener el parametro consultasql
	$sql = "SELECT consultasql FROM gn_variables WHERE nombre = '$concept'";
	$result = $mysqli->query($sql);
	$consulta = mysqli_fetch_row($result);
	$rs = $mysqli->query($consulta[0]);							//Ejecutamos la consulta que se obtiene de la tabla
	$row = mysqli_fetch_row($rs);
	$expression = $row[0];
	return $expression;											//Retornamos el valor
}

/**
 * clean_str
 *
 * Esta función limpia la cadena o formula para quitarle los simbolos como ? o & en los cuales busca los valores
 *
 * @author Alexander N
 * @param  String $cadena Formula o valor que incluya simbolos como ?,&,"
 * @return String $cadena Formula
*/
function clean_str($cadena){
	$caracteres = '¿ ×  " \' & ';								//Posibles caracteres a reemplazar
	$caracteres = explode(' ',$caracteres);						//Divimos la cadena de caracteres en donde sea ' '
	$nchar      = count($caracteres);							//Contamos los caracteres en la cadena
	$base       = 0;											//Contador base inicializado en 0
	while($base<$nchar){										//Mentras base sea menor que cantidad de caracteres
		$cadena = str_replace($caracteres[$base],'',$cadena);	//Reemplazamos los caracteres en la consulta
		$base++;												//Aumentamos el contado de base
	}
	return $cadena;												//Retornamos la cadena
}

/**
 * search_patterns
 *
 * Esta función busca palabras clave o variables dentro de la expresión usando un regex con un patron de busqueda
 *
 * @author Alexander N
 * @param  String $expression Formula en la cual se incluye las variables que se van a reemplazar
 * @return array  $array Contiene los nombres de las variables dentro de la función
*/
function search_patterns($expression,$pattern){
	$array = array();											//Inicializamos un array
	$regex = preg_match_all('/&([^&]*)&/',$expression,$exit);	//Buscamos usando un patron las palabra encerradas en &
	for ($i=0; $i < $regex; $i++) { 							//Recorremos la salida del regex usando su propio tamaño para recorrer el array
		$x = $exit[1][$i];										//Obtenemos el valor en 1, ya que el regex genera dos array
		if($pattern == true) {
			$array[] = "/$x/";									//Cargamos el array con el valor obtenido usando //
		}else{
			$array[] = "$x";									//Cargamos el array con el valor obtenido
		}
	}
	return 	$array;												//Retornamos el array con los nombres de las variables
}

/**
 * search_variants
 *
 * Esta función busca las palabras claves dentro de la consulta, usando el regex
 *
 * @author Alexander N
 * @param  int 	 $category Id de la categoria relacionada
 * @param  array $names    Nombres de las variables
 * @return array $sql 	   Retorna un array con las sql
*/
function search_variants($names){
	$sql_x = array();															  //Inicializamos el array en 0
	require ('../Conexion/conexion.php');										  //Clase conexión
	for ($i=0; $i < count($names); $i++) { 										  //Recorremos el array y usamos como indicador el tamaño del array
		$name = $names[$i];		//Variable de captura del valor en el array
		$sql = "SELECT var.consultasql FROM gn_variables var  WHERE var.nombre = '$name'";
		$result = $mysqli->query($sql);
		$row = mysqli_fetch_row($result);
		$sql_x[] = $row[0];														  //Llenamos el array con el valor de la consulta encontrado
	}
	return $sql_x;																  //Retornamos el array
}

/**
 * execute_query
 *
 * Esta función permite ejecutar consultas de un unico valor el cual es retornado
 *
 * @author Alexander N
 * @param  String    $sql Consulta para ejecutar
 * @return undefined $row Valor retornado dependiendo de la consulta que se necesita evaluar
*/
function execute_query($sql){
	require ('../Conexion/conexion.php');										  //Clase conexión
	$result = $mysqli->query($sql);												  //Ejecutamos la consulta
	$row = mysqli_fetch_row($result);											  //Tomamos el valor retornado por la consulta
	return $row[0];																  //Retornamos el valor
}
/**************************************************************************************************************************
 * Funciones para nómina
 * 14-08-2017
 ***************************************************************************************************************************/

/**
 * acumular_e
 *
 * Función para acumunular los procesos respecto a los periodos n y al empleado dando acumulado por concepto
 *
 * @author Alexander N
 * @param  int $empleado Id del empleado
 * @param  int $tproceso Id del tipo de proceso
 * @param  int $n        Contador de la cantidad de periodos a acumular
 * */
function acumular_e($empleado, $tproceso, $n, $con){
	require ('../Conexion/conexion.php');
	$data = 0;
	$x = 0;
	$y = "";
        
        if(empty($con)){
         
            $sql = "SELECT    nov.periodo
	        FROM      gn_novedad nov
			LEFT JOIN gn_periodo per ON nov.periodo = per.id_unico
			WHERE     per.tipoprocesonomina = $tproceso
			AND       nov.empleado          = $empleado
			AND 	  per.acumulable        = 1
			GROUP BY  per.id_unico
			ORDER BY  per.id_unico DESC
			LIMIT     0, $n";
                $res = $mysqli->query($sql);
                while($row = mysqli_fetch_row($res)){
                        $y .= $row[0].",";
                }
                $y = substr($y, 0, strlen($y)-1);
                $sql_x = "SELECT    con.id_unico, SUM(nov.valor) FROM gn_novedad nov
                                  LEFT JOIN gn_periodo per  ON nov.periodo  = per.id_unico
                                  LEFT JOIN gn_concepto con ON nov.concepto = con.id_unico
                                  WHERE     (per.tipoprocesonomina = $tproceso)
                                  AND       (nov.empleado          = $empleado)
                                  AND       (per.acumulable        = 1)
                                  AND       (nov.periodo           IN ($y))
                                  GROUP BY  nov.concepto";
            $res_x = $mysqli->query($sql_x);
            while($row_x = mysqli_fetch_row($res_x)){
                $data[] = array($row_x[0]=>$row_x[1]);
            }
           
        }else{
 
             $sql = "SELECT    nov.periodo
	        FROM      gn_novedad nov
			LEFT JOIN gn_periodo per ON nov.periodo = per.id_unico
			WHERE     nov.empleado          = $empleado
			GROUP BY  per.id_unico
			ORDER BY  per.id_unico DESC
			LIMIT     0, $n";
                $res = $mysqli->query($sql);
                while($row = mysqli_fetch_row($res)){
                        $y .= $row[0].",";
                }
                $y = substr($y, 0, strlen($y)-1);
                if(empty($y)){
                	$data[]=0;
                }else{
                	 $sql_x = "SELECT    con.id_unico, SUM(nov.valor) FROM gn_novedad nov
                                  LEFT JOIN gn_periodo per  ON nov.periodo  = per.id_unico
                                  LEFT JOIN gn_concepto con ON nov.concepto = con.id_unico
                                  WHERE     (nov.empleado          = $empleado)
                                  AND       (nov.periodo           IN ($y))
                                  AND       (nov.concepto          = $con)
                                  GROUP BY  nov.concepto";
		            $res_x = $mysqli->query($sql_x);
		            while($row_x = mysqli_fetch_row($res_x)){
		                $data += $row_x[1];
		            }
                }

        }
     return $data;    
}

function acumular_eN($empleado, $tproceso, $n, $con, $periodo){
	require ('../Conexion/conexion.php');

	$data = 0;
	$x = 0;
	$y = "";
        
        if(empty($con)){
         
            $sql = "SELECT    nov.periodo
	        FROM      gn_novedad nov
			LEFT JOIN gn_periodo per ON nov.periodo = per.id_unico
			WHERE     per.tipoprocesonomina = $tproceso
			AND       nov.empleado          = $empleado
			AND 	  per.acumulable        = 1
			GROUP BY  per.id_unico
			ORDER BY  per.id_unico DESC
			LIMIT     0, $n";
                $res = $mysqli->query($sql);
                while($row = mysqli_fetch_row($res)){
                        $y .= $row[0].",";
                }
                $y = substr($y, 0, strlen($y)-1);
                $sql_x = "SELECT    con.id_unico, SUM(nov.valor) FROM gn_novedad nov
                                  LEFT JOIN gn_periodo per  ON nov.periodo  = per.id_unico
                                  LEFT JOIN gn_concepto con ON nov.concepto = con.id_unico
                                  WHERE     (per.tipoprocesonomina = $tproceso)
                                  AND       (nov.empleado          = $empleado)
                                  AND       (per.acumulable        = 1)
                                  AND       (nov.periodo           IN ($y))
                                  GROUP BY  nov.concepto";
            $res_x = $mysqli->query($sql_x);
            while($row_x = mysqli_fetch_row($res_x)){
                $data[] = array($row_x[0]=>$row_x[1]);
            }
           
        }else{
        if ($con==110) {
			$valCon=$con.',109';
		}else{
			$valCon=$con;
		}
             $sql = "SELECT    nov.periodo
	        FROM      gn_novedad nov
			LEFT JOIN gn_periodo per ON nov.periodo = per.id_unico 
			LEFT JOIN gn_periodo pera ON pera.id_unico = $periodo
			WHERE     nov.empleado          = $empleado 
			and nov.concepto IN ($valCon)
			AND (per.fechainicio BETWEEN  (CONCAT(YEAR(pera.fechainicio)-1, '-', if(MONTH(pera.fechainicio)<10, CONCAT('0',MONTH(pera.fechainicio)),MONTH(pera.fechainicio) ), '-01') ) and pera.fechafin OR per.fechafin BETWEEN  (CONCAT(YEAR(pera.fechainicio)-1, '-', if(MONTH(pera.fechainicio)<10, CONCAT('0',MONTH(pera.fechainicio)),MONTH(pera.fechainicio) ), '-01') ) and pera.fechafin) 
			GROUP BY  per.id_unico
			ORDER BY  per.id_unico DESC
			LIMIT     0, $n";
                $res = $mysqli->query($sql);
                while($row = mysqli_fetch_row($res)){
                        $y .= $row[0].",";
                }
                $y = substr($y, 0, strlen($y)-1);
                if(empty($y)){
                	$data[]=0;
                }else{
                	 $sql_x = "SELECT    con.id_unico, SUM(nov.valor) FROM gn_novedad nov
                                  LEFT JOIN gn_periodo per  ON nov.periodo  = per.id_unico
                                  LEFT JOIN gn_concepto con ON nov.concepto = con.id_unico
                                  WHERE     (nov.empleado          = $empleado)
                                  AND       (nov.periodo           IN ($y))
                                  AND       (nov.concepto          IN ($valCon))
                                  GROUP BY  nov.concepto";
		            $res_x = $mysqli->query($sql_x);
		            while($row_x = mysqli_fetch_row($res_x)){
		                $data += $row_x[1];
		            }
                }

        }

        if($tproceso==2 && $con ==110){
        	#BUSCAR PERIODO RETROACTIVO 
	        $pretro = "SELECT SUM(nov.valor)
	            FROM      gn_novedad nov
	            LEFT JOIN gn_periodo per ON nov.periodo = per.id_unico 
	            LEFT JOIN gn_periodo pera ON pera.id_unico = $periodo 
	            WHERE     nov.empleado          = $empleado
	            and nov.concepto IN ($valCon) AND per.tipoprocesonomina = 12 
	            AND year(per.fechainicio) =  year(pera.fechafin)
	            AND per.fechafin > concat(year(pera.fechafin),'-01-01')
	            ORDER BY per.fechainicio DESC
	            limit 1";
	        $pretro = $mysqli->query($pretro);
            $rowpt = mysqli_fetch_row($pretro);

	        if(!empty($rowpt[0])){
	            $data += ROUND($rowpt[0],0);    
	        }
        }

     return $data;    
}

/**
 * cambio_query
 *
 * Función para reemplazar la variable %x%
 *
 * @author Alexander N
 * @param  string $str String de la query para reemplzar las variables
 * @param  string $w   Agujas
 * @return string $str Consulta con los valores a reemplazar
 */
function cambio_query($str, $w){
	$w = explode(",", $w);
	for ($i = 0; $i < count($w); $i++) {
		$str = str_replace("%x$i%", $w[$i], $str);
	}
	return $str;
}