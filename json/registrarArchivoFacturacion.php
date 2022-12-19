<?php
/**
 * registrarArchivoFacturacion.php
 *
 * @author Alexander Numpaque
 * @package Subir Facturación
 * @version $Id: registrarArchivoFacturacion.php 001 2017-05-24 Alexander Numpaque$
 **/
@session_start();
ini_set('max_execution_time', 0);

require '../ExcelR/Classes/PHPExcel/IOFactory.php';									 	//Archivo para cargue y lectura de archivo excel
require '../Conexion/conexion.php';													 	//Archivo de conexión
require ('../funciones/funciones_cargue_predial.php');								 	//Archivo con las funciones de recaudo predial
require ('../funciones/funciones_cargue_facturacion.php');								//Archivo con las funciones de facturación
error_reporting(E_ALL);
ini_set('display_errors', '1');
$param    = $_SESSION['anno'];															//Parametro parametrizacionanno
$compania = $_SESSION['compania'];													 	//Parametro compania

$inputFileName = $_FILES['flFactura']['tmp_name'];									 	//Archivo temporal cargado
$claseArchivo = '"'.$mysqli->real_escape_string(''.$_POST['sltClaseA'].'').'"';		 	//Clase de archivo
$tipoF = $mysqli->real_escape_string(''.$_POST['sltTipoC'].'');					 		//Tipo de factura

$linea_i  = start_rate($claseArchivo);											 		//Obtenemos la linea de inicio de lectura del archivo
$coln_s   = get_columns($claseArchivo, $param);		 								//Obtenemos el numero de las columnas para obtener los valores
$cons_r   = get_concepts($claseArchivo, $param);									//Obtenemos los id de conceptos rubro
$tipo_cnt = get_id_type_cnt($tipoF);													//Obtenemos el id de tipo comprobante cnt
$tipo_p   = get_id_int_pptal($tipo_cnt);												//Obtenemos el id de tipo comprobante pptal relacioando al tipo cnt


$number = get_max_acount($tipoF, $param);												//Número maximo de facturas registradas

$objReader      = new PHPExcel_Reader_Excel2007();										 //Instanciamos la clase de lectura
$objPHPExcel    = PHPExcel_IOFactory::load($inputFileName); 							 //Instanciamos la clase de carga de archivo
$objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);								 	 //Instanciamos el objeto de area de trabajo
$total_filas    = $objWorksheet->getHighestRow();										 //Número maximo de filas
$total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());//Número maximo de columnas

$c = 0;$x = 0;$y = 0;$z = 0;															 //Variables de conteo y validación

for ($a = $linea_i; $a <= $total_filas ; $a++) {

	$celda_1 = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();	 	 //Celda de la columna A

	if(!is_null($celda_1) && is_numeric($celda_1)) {								 	 //Validamos que la celda no esta vacia

		$c = $c + 1;																	 //Incrementamos en 1
		$numero  = $number + $c;															 //Sumamos el número con el contador
		$v_fecha = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();	 //Valor de la fecha

		if(is_float($v_fecha)) {													 	 //Validamos si la fecha es float damos formato de fecha
			$cell_d  = $objWorksheet->getCellByColumnAndRow(0, $a)->getValue();	 		 //Obtenemos el valor de la celda
			$_fecha  = PHPExcel_Shared_Date::ExcelToPHP($cell_d);					 	 //Formateamos la fecha
			$fecha1  = date("d/m/Y", $_fecha);										 	 //La formateamos dd/mm/yyy
			$v_fecha = str_replace($v_fecha, $fecha1, $v_fecha);				 	 	 //Cambiamos el valor anterior por el de la fecha
		}

		$fecha       = explode("/", $v_fecha);											 	 //Divimos la fecha usando /
		$fecha       = $fecha[2]."-".$fecha[1]."-".$fecha[0];								 //Ordenamos y formatermoas la fecha yyyy-mm-dd
		$fecha_v     = strtotime('+1 month', strtotime($fecha));						 	 //Sumamos un mes a la fecha
		$fecha_v     = date('Y-m-d', $fecha_v);										     	 //Formateamos la fecha de vencimiento yyyy-mm-dd
		$des1        = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();	 //Tiquete Inicial
		$des2        = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();	 //Tiquete Final
		$descripcion = '"Tiquete Inicial :'.$des1." Tiquete Final :".$des2.'"';			 //Descripción
		$usuario     = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();	 //Obtenemos el usuario en la posicion 4 es decir columna E
		$tercero     = get_tercero_usuario($usuario);										 //Obtenemos el id del tercero

		//Registramos el comprobante pptal y obtenemos el id del comprobante potal
		$comprobante_pptal = save_head_pptal($numero, $fecha, $fecha_v, $descripcion, $param, 'NULL', $tipo_p, $tercero, 1, 2);
		//Registramos el comprobante cnt y obtenemos el id del comprobante cnt
		$comprobante = save_head_cnt($numero, $fecha, $tercero, $descripcion, 1, 'NULL', 'NULL', $compania, $param, $tipo_cnt);
		//Registramos la cabeza de la factura y obtenemos el id de la factura
		$factura = save_head_fac($fecha, $tipoF, $numero, 12, $descripcion, 4, $tercero, $fecha_v, $param);

		for ($b=0; $b < count($coln_s); $b++) { 										 //Deplegamos el array de las columnas para obtener el valor

			$col = $coln_s[$b];															 //Obtenemos la columna en donde tomamos el valor de la misma
			$con = $cons_r[$b];															 //Obtenemos el id del concepto rubro cuenta
			$rx  = exist_opertion($col);												 //Verificamos si hay operador matematico
			if($rx > 0){																 //Si hay realiza el proceso para descubrirlo y obtener el resultado

				$opr   = get_operator($col);											 //Obtenemos el operador matematico
				$cols  = explode("$opr[0]",$col);										 //Obtenemos las columnas y las separamos por el operador
				$value = "";															 //Inicializamos la variable value en vacio

				for ($m = 0; $m < count($cols); $m++) { 								 //Desplegamos el array para obtener los valores en las consultas
					$value .= $objWorksheet->getCellByColumnAndRow($cols[$m], $a)->getValue().$opr[0];//String con el valor de concepto y operador
				}

				$value = substr($value,0,strlen($value)-1);								 //Quitamos el operador final

				eval("\$lne=$value;");													 //Evaluamos la expresión y asignamos su valor a la variable $lne

			}else{
				$lne = $objWorksheet->getCellByColumnAndRow($col, $a)->getCalculatedValue();	     //Obtenemos el valor del concepto
			}

			echo "Valor :".$lne." - Columna: $col - Factura: $numero<br>";

			if($lne != '0' || !empty($lne)){											 //Validamos que el valor del concepto sea diferente de 0
				$valor   = $lne;													     //Iniciamos la variable valor con el valor del concepto
				if(!empty($valor)){
					$valorRD = abs($valor);												 //Obtenemos el valor absoluto (-==+,+==+)
					$values  = get_concept_rbc($con);									 //Obtenemos id de conceptorubro, cuentas debito-credito y rubro fuente
					$val     = explode(",",$values);									 //Dividimos el String usando ,
					$con_rub = $val[0];													 //Id de concepto rubro
					$cuenta_debito    = $val[1];									     //Id de cuenta débito
					$cuenta_credito   = $val[2];										 //Id de cuenta crédito
					$rubro_fuente     = get_rubro_fuente($con_rub);						 //Obtenemos rubro fuente relacionado al rubro de concepto rubro
					$concepto         = get_concept_fin($con);							 //Obtenemos id del concepto relacionado a conceptor rubro
					$concepto_factura = get_id_concept_fat($con_rub, $rubro_fuente, $param);   //Obtenemos id de concepto facturación
					$detalle_pptal = 'NULL'; $detalle_afectado = 'NULL';
					if(!empty($comprobante_pptal) || $comprobante_pptal != '0') {		 //Validamos que la variable no este vacia o tenga el valor 0
						//Registramos el detalle de comprobante pptal y obtenemos el id del registro
						$detalle_pptal = save_detail_pptal($descripcion, $valorRD, $comprobante_pptal, $rubro_fuente, $con_rub, $tercero, 2147483647);
						if(!empty($detalle_pptal) || $detalle_pptal != '0'){			 //Validamos si el proceso de registro retorna algun valor
							$x = $x + 1;												 //Incrementamos $x
						}
					}

					if(!empty($comprobante) || $comprobante != '0') {						 //Validamos que la variable no este vacia o tenga el valor 0
						//Registramos el detalle de comprobante cnt con cuenta debtio
						$detalle_afectado = save_detail_cnt($fecha, $descripcion, $valorRD, $cuenta_debito, 1, $tercero, 2147483647, 12, $comprobante, $detalle_pptal, 'NULL');
						//Registramos el detalle de comprobante cnt con cuenta credito
						$detalle_recaudo = save_detail_cnt($fecha, $descripcion, $valorRD, $cuenta_credito, 2, $tercero, 2147483647, 12, $comprobante, 'NULL', $detalle_afectado);
						if(!empty($detalle_recaudo) || $detalle_recaudo != '0'){			 //Validamos si el proceso de registro retorna algun valor
							$y = $y + 1;													 //Incrementamos $y
						}
					}

					if(!empty($factura)) {
						$detalle_factura = save_detail_fac($factura, $concepto_factura, $valorRD, 1, 0, 0, 0, $valorRD, $detalle_afectado);
						if(!empty($detalle_afectado) || $detalle_afectado != '0'){			 //Validamos si el proceso de registro retorna algun valor
							$z = $z + 1;													 //Incrementamos $z
						}
					}
				}
			}
		}
	}
}
//Imprimimos la pagina, y formateamos el codigo usando \n para saltos y \t para tab y sangria
echo "<html>\n";
echo "<head>\n";
echo "<meta charset=\"utf-8\">\n";
echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
echo "<link rel=\"stylesheet\" href=\"../css/bootstrap.min.css\">\n";
echo "<link rel=\"stylesheet\" href=\"../css/style.css\">\n";
echo "<script src=\"../js/jquery.min.js\"></script>\n";
echo "<link rel=\"stylesheet\" href=\"../css/jquery-ui.css\" type=\"text/css\" media=\"screen\" title=\"default\" />\n";
echo "<script type=\"text/javascript\" language=\"javascript\" src=\"../js/jquery-1.10.2.js\"></script>\n";
echo "</head>\n";
echo "<body>\n";
echo "</body>\n";
echo "</html>\n";
echo "<link rel=\"stylesheet\" href=\"../css/bootstrap-theme.min.css\">";
echo "<script src=\"../js/bootstrap.min.js\"></script>";
echo "<div class=\"modal fade\" id=\"myModal1\" role=\"dialog\" align=\"center\" >\n";
echo "<div class=\"modal-dialog\">\n";
echo "<div class=\"modal-content\">\n";
echo "<div id=\"forma-modal\" class=\"modal-header\">\n";
echo "<h4 class=\"modal-title\" style=\"font-size: 24; padding: 3px;\">Información</h4>\n";
echo "</div>\n";
echo "<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
echo "<p>Información guardada correctamente.</p>\n";
echo "</div>\n";
echo "<div id=\"forma-modal\" class=\"modal-footer\">\n";
echo "<button type=\"button\" id=\"ver1\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\" >Aceptar</button>\n";
echo "</div>\n";
echo "</div>\n";
echo "</div>\n";
echo "</div>\n";
if($x == $z) {									//Validamos que $x sea igual a $y, ya que son la cantidad de veces que ha registrado
echo "<script>";
echo "\n\t$('#myModal1').modal('show');";
echo "\n\t$('#ver1').click(function() {window.location='../subirArchivoFacturacion.php';});";
echo "\t</script>";
}
echo "\n</body>";
echo "\n</html>";
?>
