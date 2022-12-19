<?php
/**
 * Created by Alexander.
 * User: Alexander
 * Date: 02/06/2017
 * Time: 11:15 AM
 *
 * registrarRecaudoViasoftJson.php
 * @author Alexander Numpaque
 * @package Subir Viasoft
 * @version $Id: registrarRecaudoViasoftJson.php 001 2017-05-16 Alexander Numpaque$
 * @version $Id: #2 registrarRecaudoViasoftJson.php 002 2017-05-16 Alexander Numpaque$
 * Se cambio registro de detalle cuando el codigo tiene mas de dos configuraciones
 */
@session_start();
ini_set('max_execution_time', 0);
require '../ExcelR/Classes/PHPExcel/IOFactory.php';                                       //Archivo para cargue y lectura de archivo excel
require '../Conexion/conexion.php';                                                       //Archivo de conexión
require ('../funciones/funciones_cargue_predial.php');                                    //Archivo con las función para obtener los valores
require ('../funciones/funciones_recaudo_viasoft.php');
error_reporting(E_ALL);
ini_set('display_errors', '1');
$inputFileName = $_FILES['flViasoft']['tmp_name'];                                        //Archivo temporal cargado
$tipo_cnt      = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoC'].'').'"';           //Tipo de comprobante
$fecha         = explode("/",$mysqli->real_escape_string(''.$_POST['txtFecha'].''));      //Fecha
$fecha         = "$fecha[2]-$fecha[1]-$fecha[0]";
$banco         = '"'.$mysqli->real_escape_string(''.$_POST['sltBanco'].'').'"';           //Banco
$tercero       = '"'.$mysqli->real_escape_string(''.$_POST['sltTercero'].'').'"';         //Tercero

$tipo_hom     = get_id_int_hom($tipo_cnt);
$cuenta_banco = get_id_bank($banco);                                                      //Obtenemos la cuenta relacionada la banco
$tipo_pptal   = get_id_int_pptal($tipo_cnt);                                              //Obtenemos el tipo de comprobante presupuestal
$fecha_v      = strtotime('+1 month', strtotime($fecha));                                 //Sumamos un mes a la fecha
$fecha_v      = date('Y-m-d', $fecha_v);                                                  //Formateamos la fecha de vencimiento yyyy-mm-dd
$param        = $_SESSION['anno'];                                                        //Parametro parametrizacionanno
$compania     = $_SESSION['compania'];                                                    //Parametro compania

$objReader      = new PHPExcel_Reader_Excel2007();                                        //Instanciamos la clase de lectura
$objPHPExcel    = PHPExcel_IOFactory::load($inputFileName);                               //Instanciamos la clase de carga de archivo
$objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);                                   //Instanciamos el objeto de area de trabajo
$total_filas    = $objWorksheet->getHighestRow();                                         //Número maximo de filas
$total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());//Número maximo de columnas
$c = 0;$x = 0;$y = 0;$z = 0;$sumVD = 0;                                                   //Variables de conteo y validación

$comprobante_hom   = 0;
$comprobante_hom_p = 0;
$comprobante_cnt   = 0;
$comprobante_pptal = 0;

$number = get_max_number($tipo_cnt);                                                    //Obtenemos el numero maximo por tipo
$v_number = validate_number($number, $param);                                           //Validamos que el numero no este vacio y le sumamos uno

$comprobante_cnt = save_head_cnt($v_number, $fecha, $tercero, "NULL",1,"NULL","NULL", $compania, $param, $tipo_cnt);
$comprobante_pptal = save_head_pptal($v_number, $fecha, $fecha_v, "NULL", $param, "NULL", $tipo_pptal, $tercero, 1, $tercero);

$suma_valor_recaudado = 0;

if(!empty($tipo_hom) || $tipo_hom != '0') {

    $comprobante_hom = save_head_cnt($v_number, $fecha, $tercero, "NULL",1,"NULL", "NULL", $compania, $param, $tipo_hom);
    $tipo_pptal_hom = get_id_int_pptal($tipo_hom);

    if(!empty($tipo_pptal_hom)) {
        $comprobante_hom_p = save_head_pptal($v_number, $fecha, $tercero, "NULL", $param, "NULL", $tipo_pptal_hom, $tercero, 1, $tercero);
    }

}

echo "<table class=\"table-condensed table-bordered table-responsive\">";
echo "<tbody>";
$detalle_cnt = "NULL";
for ($a = 8; $a <= $total_filas; $a++){
    $x++;
    $codigo = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();        //Celda de la columna A

    if(!is_null($codigo) && is_numeric($codigo)) {                                      //Validamos que la celda no esta vacia
        $vt = $objWorksheet->getCellByColumnAndRow(14, $a)->getCalculatedValue();        //Celda de la columna TERCERO
        if(!empty($vt)){
            #Buscar Tercero 
            $bt = get_tercero_ni ($vt);
            if($bt ==0){
                $tercero = $_POST['sltTercero'];
            } else {
                $tercero = $bt;
            }
        } else {
            $tercero = $_POST['sltTercero'];
        }
        
        
        $conceptos = get_concept_v($codigo);

        for ($b = 0; $b < count($conceptos); $b++) {

            $conceptos_1 = explode(",",$conceptos[$b]);
            $id_cgv = $conceptos_1[0];
            $concepto = $conceptos_1[1];

            if(!empty($concepto)) {

                $concepto_rubro  = get_concept_rbo($concepto);
                $rubro           = get_rubro($concepto_rubro);
                $rubro_fuente    = get_rubro_fuente_2($rubro);
                $valor           = $objWorksheet->getCellByColumnAndRow(13, $a)->getCalculatedValue();
                $x_acount        = get_acounts($concepto_rubro);
                $values          = explode(",", $x_acount);
                $cuenta_debito   = $values[0];
                $cuenta_credito  = $values[1];
                $porcentaje      = get_porcent($codigo, $id_cgv);
                $val             = calcule_porcent($valor, $porcentaje);
                echo "<tr>";
                echo "<td>Codigo:    ".PHP_EOL.$codigo."</td>";
                echo "<td>Concepto:  ".PHP_EOL.$concepto."</td>";
                echo "<td>Porcentaje:".PHP_EOL.$porcentaje."</td>";
                echo "<td>Valor:     ".PHP_EOL.number_format($valor,2,'.',',')."</td>";
                echo "<td>Valor T:   ".PHP_EOL.number_format($val,2,'.',',')."</td>";
                //echo "Codigo: ".$codigo.",Concepto: ".$concepto.",Porcentaje a obtener :".$porcentaje.",Valor a aplicar porcentaje :".$valor.",    Valor Obtenido :".$val."<br/>";
                echo "</tr>";
                $suma_valor_recaudado += $val;
                $detalle_pptal        = "NULL";
                if(!empty($comprobante_pptal) || $comprobante_pptal != '0') {

                    $detalle_pptal = save_detail_pptal("NULL", $val, $comprobante_pptal, $rubro_fuente, $concepto_rubro, $tercero, "2147483647");
                    $c++;

                }

                if(!empty($comprobante_cnt) || $comprobante_cnt != '0') {

                    $nat = get_nat_acount($cuenta_debito);

                    if($nat == 1) {
                        $v_l = $val * -1;
                    } else if ($nat == 2) {
                        $v_l = $val;
                    }

                    $detalle_cnt = save_detail_cnt($fecha, "NULL", $v_l, $cuenta_debito, $nat, $tercero, "2147483647", "12", $comprobante_cnt, $detalle_pptal, "NULL");

                    if(!empty($detalle_cnt) || $detalle_cnt != '0') {
                        $sumVD += $val;
                    }

                }
                if(!empty($comprobante_hom) || $comprobante_hom != '0') {

                    //$cuenta_credito = get_acount_cr($cuenta_debito, $concepto_rubro); //Obtenemos la cuenta credito

                    if($cuenta_credito != '0' || !empty($cuenta_credito)) { //Validamos que la cuenta credito no sean nula

                        if($cuenta_debito !== $cuenta_credito) { //Validamos que la cuenta debito y credito no sean iguales
                            $detalle_cnt_hom_d = save_detail_cnt($fecha, "NULL", $val, $cuenta_debito,1, $tercero, "2147483647", "12", $comprobante_hom, "NULL", "NULL");
                            $detalle_cnt_hom_c = save_detail_cnt($fecha, "NULL", $val, $cuenta_credito,2, $tercero,"2147483647", "12", $comprobante_hom, "NULL", $detalle_cnt);
                            $y++;

                        }
                    }
                }

                if(!empty($comprobante_hom_p) || $comprobante_hom_p != 0) {

                    $detalle_pptal_h = save_detail_pptal("NULL", $val, $comprobante_hom_p, $rubro_fuente, $concepto_rubro, $tercero, "2147483647");
                    $z++;

                }
            }
        }
    }
}
if($comprobante_cnt != '0' && $sumVD != 0 || empty($sumVD)) {
    $detalle_recaudo = save_detail_cnt($fecha, "NULL", $sumVD, $cuenta_banco, 1, $tercero, "2147483647", "12", $comprobante_cnt, "NULL", $detalle_cnt);
}

if(!empty($comprobante_hom)){
    $conteo = contar_detalles_cnt($comprobante_hom);
    if($conteo == 0 || empty($conteo)){
        eliminar_comprobante_cnt($comprobante_hom);
    }
}

if(!empty($comprobante_hom_p)){
    $conteo = contar_detalles_pptal($comprobante_hom_p);
    if($conteo == 0 || empty($conteo)){
        eliminar_comprobante_pptal($comprobante_hom_p);
    }
}
echo "</tbody>";
echo "<tfooter>";
echo "<tr>";
echo "<td colspan=\"6\" align=\"right\">Total Recaudado: ".number_format($suma_valor_recaudado,2,'.',',')."</td>";
echo "<tr>";
echo "<tr>";
echo "<td colspan=\"6\" align=\"right\"><a href=\"../subirArchivoViasoft.php\">Volver</a></td>";
echo "<tr/>";
echo "</tfooter>";
echo "</table>";
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
echo "<script src=\"../js/bootstrap.js\"></script>";
echo "<div class=\"modal fade\" id=\"myModal1\" role=\"dialog\" align=\"center\" >\n";
echo "<div class=\"modal-dialog\">\n";
echo "<div class=\"modal-content\">\n";
echo "<div id=\"forma-modal\" class=\"modal-header\">\n";
echo "<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>\n";
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
echo "<div class=\"modal fade\" id=\"myModal2\" role=\"dialog\" align=\"center\" >\n";
echo "\t<div class=\"modal-dialog\">\n";
echo "\t\t<div class=\"modal-content\">\n";
echo "\t\t\t<div id=\"forma-modal\" class=\"modal-header\">\n";
echo "\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>\n";
echo "\t\t\t</div>\n";
echo "\t\t\t<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
echo "\t\t\t\t<p>No se ha podido guardar la información.</p>\n";
echo "\t\t\t\n</div>";
echo "\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">\n";
echo "\t\t\t\t<button type=\"button\" id=\"ver2\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\">Aceptar</button>\n";
echo "\t\t\t</div>\n";
echo "\t\t</div>\n";
echo "\t</div>\n";
echo "</div>\n";
if($total_columnas == $x) {                                 //Validamos que $x sea igual a $y, ya que son la cantidad de veces que ha registrado
    echo "<script>";
    echo "\n\t$('#myModal1').modal('show');";
    echo "\n\t$('#ver1').click(function() {window.location='../subirArchivoViasoft.php';});";
    echo "\t</script>";
}else if($x == 0 || $y == 0 || $c == 0 || $x <> $y){
    echo "<script>";
    echo "\n\t$('#myModal2').modal('show');";
    echo "\n\t$('#ver2').click(function() {window.history.go(-1);});";
}
echo "\n</body>";
echo "\n</html>";