<?php 
/**
* generator_doc (class generator_doc)
* 
* Esta clase se extiende es decir que hereda las funciones de la clase PHP_EXCEL.
* Lo cual le permite crear y leer un archivo de excel, el cual envia a la celda A1
* La expresión y la ejecuta retornando su valor
* 
* @author  Alexander Numpaque
* @package generator_doc
* @version $Id:doc.php 001 2017-05-17 Alexande Numpaque $
*/
require '../ExcelR/Classes/PHPExcel.php';
require '../ExcelR/Classes/PHPExcel/IOFactory.php';
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
class generator_doc extends PHPExcel{
	var $expression;

	function __construct($expression) {
		$this->expression=$expression;
	}
	/**
	 * exc_excel
	 * 
	 * Esta función recibe la expresión. Crea el archivo de excel lo escribe y ejecuta y lo lee
	 * 
	 * @author  Alexander Numpaque
	 * @package generator_doc
	 * @param   string           $expression Formula a ejecutar
	 * @return  string|int|float $value      Resultado de la expresión 
	*/
	public static function exc_exel($expression) {		
		error_reporting(E_ALL);                                                                //Error de reporte campos vacios
		ini_set('display_errors', TRUE);                                                       //Salida de errores
		ini_set('display_startup_errors', TRUE);                                               //Salida de erreres de inicio				
		$objPHPExcel = new PHPExcel();                                                         //Creamos el objeto excel
		$objPHPExcel->getProperties()->setCreator("Grupo_AAA")                                 //Propiedades de objeto
	     ->setLastModifiedBy("Grupo_AAA")
	     ->setTitle("Office 2007 XLSX")
	     ->setSubject("Office 2007 XLSX")
	     ->setDescription("For Office 2007 XLSX, generated using PHP.")
	     ->setKeywords("office 2007 openxml php")
	     ->setCategory("Test result file");			
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', "=$expression");	                   //Escritura de la formula en la celda A1		
		$callStartTime = microtime(true);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');              //Escrituta del objeto a excel 2017
		$objWriter->setPreCalculateFormulas(true);                                             //Ejecución de formulas
		$objWriter->save('doc.xlsx');                                                          //Lectura del archivo
		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;                                             		
		$inputFileName = 'doc.xlsx';                                                            //Nombre del archivo		
		$objReader = new PHPExcel_Reader_Excel2007();                                           //Inicializamos objeto de lectura		
		$objPHPExcel = $objReader->load($inputFileName);                                        //Cargamos el archivo		
		$value=$objPHPExcel->getActiveSheet()->getCell('A1')->getCalculatedValue();             //Tomamos el valor de la celda  A1		
		return $value;                                                                          //Retornamos el valor
	}
}
 ?>