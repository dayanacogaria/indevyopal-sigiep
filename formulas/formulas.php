<?php 
//RECIBE LA FORMULA
$formula = $_POST['formula'];
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once dirname(__FILE__) . './../ExcelR/Classes/PHPExcel.php';
include './../ExcelR/Classes/PHPExcel/IOFactory.php';

//CREA EL OBJETO EXCEL
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Grupo_AAA")
     ->setLastModifiedBy("Grupo_AAA")
     ->setTitle("Office 2007 XLSX")
     ->setSubject("Office 2007 XLSX")
     ->setDescription("For Office 2007 XLSX, generated using PHP.")
     ->setKeywords("office 2007 openxml php")
     ->setCategory("Test result file");

//ENVIA LOS DATOS QUE SE NECESITAN EN EL EXCEL
//EN ESTE CASO SE ENVIA LA FORMULA A LA CELDA A1, CON EL = ANTES PARA QUE RESUELVA
$objPHPExcel->getActiveSheet()->setCellValue('A1', "=".$formula."");


// Set header and footer. When no different headers for odd/even are used, odd header is assumed.
$callStartTime = microtime(true);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->setPreCalculateFormulas(true);
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

//#LEER
//NOMBRE DEL ARCHIVO
$inputFileName = 'formulas.xlsx';
#INSTANCIA LA CLASE READER PARA ABRIR Y LEER EL ARCHIVO
$objReader = new PHPExcel_Reader_Excel2007();
#CARGA EL ARCHIVO
$objPHPExcel = $objReader->load($inputFileName);
#PIDE QUE TRAIGA EL VALOR CALCULADO DE LA CELDA A1
$resultado=$objPHPExcel->getActiveSheet()->getCell('A1')->getCalculatedValue(); 
#DEVUELVE EL RESULTADO
echo json_encode($resultado);
?>   