<?php
/**
 * registrarRecaudoPredialAcumuladoXLS.php
 *
 * @author  Alexander Numpaque
 * @package Subir Predial
 * @version $Id: registrarREcaudoPredialXLS.php 001 2017-09-18 Alexander Numpaque$
 **/
@session_start();
ini_set('max_execution_time', 0);
require '../ExcelR/Classes/PHPExcel/IOFactory.php';                                     //Archivo para cargue y lectura de archivo excel
require '../Conexion/conexion.php';                                                     //Archivo de conexión
require ('../funciones/funciones_cargue_predial.php');                                  //Archivo con las función para obtener los valores

$inputFileName = $_FILES['flPredial']['tmp_name'];                                      //Archivo temporal cargado
$claseArchivo  = '"'.$mysqli->real_escape_string(''.$_POST['sltClaseA'].'').'"';        //Clase de archivo
$tipoCnt       = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoC'].'').'"';         //Tipo de comprobante
$banco         = '"'.$mysqli->real_escape_string(''.$_POST['sltBanco'].'').'"';         //Cuenta bancaria
$param         = $_SESSION['anno'];                                                     //Parametro parametrizacionanno
$compania      = $_SESSION['compania'];                                                 //Parametro compania
$linea_i = start_rate($claseArchivo);                                                   //Obtenemos la linea de inicio de lectura del archivo
$coln_s  = get_columns($claseArchivo, $param);                                          //Obtenemos el numero de las columnas para obtener los valores
$cons_r  = get_concepts($claseArchivo);                                                 //Obtenemos los id de conceptos rubro
$tipo_p  = get_id_int_pptal($tipoCnt);                                                  //Obtenemos el id de tipo comprobante pptal relacioando al tipo cnt
$cnta_b  = get_id_bank($banco);                                                         //Obtenemos el id de la cuenta relacionada al banco
$tipo_h  = get_id_int_hom($tipoCnt);                                                    //Obtenemos el id del tipo de comprobante homologado
$number  = get_max_number($tipoCnt);                                                    //Obtenemos el número maximo donde el tipo sea el enviado

                                                    

$objReader      = new PHPExcel_Reader_Excel2007();                                      //Instanciamos la clase de lectura
$objPHPExcel    = PHPExcel_IOFactory::load($inputFileName);                             //Instanciamos la clase de carga de archivo
$objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);                                 //Instanciamos el objeto de area de trabajo
$total_filas    = $objWorksheet->getHighestRow();                                       //Número maximo de filas
$total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());//Número maximo de columnas
$c = 0;$x = 0;$y = 0;$z = 0;$w = 0;                                                     //Variables de conteo y validación
//Iniciamos ciclo en $a con valor en la variable $linea_inicial, es decir en la fila 7. Donde $a se menor o igual al total de filas e incremente $a
for ($a = $linea_i; $a <= $total_filas ; $a++) {
    $celda_1 = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();       //Celda de la columna A
    if(!is_null($celda_1) && is_numeric($celda_1)) {                                    //Validamos que la celda no esta vacia
        $des1 = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();      //Código en el archivo
        $des2 = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();      //Nombre del ro
        $descripcion = '"'.$des1." ".$des2.'"';                                         //Descripción
        if(empty($number)) {
            $numero = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();//Captura del numeor de la factura
            $numero = evalute_num($numero);                                             //Obtenemos el numero formateado 2017000001
        }else {
            $c = $c + 1;                                                                //Incrementamos en 1
            $numero = $number + $c;                                                     //Sumamos el número con el contador
        }
        $v_fecha = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();   //Valor de la fecha
        if(is_float($v_fecha)) {                                                        //Validamos si la fecha es float damos formato de fecha
            $cell_d = $objWorksheet->getCellByColumnAndRow(3, $a)->getValue();          //Obtenemos el valor de la celda
            $_fecha = PHPExcel_Shared_Date::ExcelToPHP($cell_d);                        //Formateamos la fecha
            $fecha1 = date("d/m/Y", $_fecha);                                           //La formateamos dd/mm/yyy
            $v_fecha = str_replace($v_fecha, $fecha1, $v_fecha);                        //Cambiamos el valor anterior por el de la fecha
        }
        $fecha = explode("/", $v_fecha);                                                //Divimos la fecha usando /
        $fecha = $fecha[2]."-".$fecha[1]."-".$fecha[0];                                 //Ordenamos y formatermoas la fecha yyyy-mm-dd
        $fecha_v = strtotime('+1 month', strtotime($fecha));                            //Sumamos un mes a la fecha
        $fecha_v = date('Y-m-d', $fecha_v);                                             //Formateamos la fecha de vencimiento yyyy-mm-dd
    }
    switch ($_REQUEST['chkAcum']) {
        case 1:
            $valorRR           = 0;
            $comprobante_pptal = get_comprobante_pptal($tipo_p, $fecha);                //Buscamos comprobante pptal
            $comprobante_cnt   = get_comprobante_cnt($tipoCnt, $fecha);                 //Buscamos el comprobante cnt
            $comprobante_hom   = get_comprobante_cnt($tipo_h, $fecha);                  //Buscamos comprobante de homologación
            //Validamos que el comprobante pptal este vacio para crearlo
            if(empty($comprobante_pptal)){
                //Registramos el comprobante pptal y obtenemos el id
                $comprobante_pptal = save_head_pptal($numero, $fecha, $fecha_v, $descripcion, $param, 'NULL', $tipo_p, 2, 1, 2);
                if(empty($comprobante_pptal)){
                    $c++;
                }
            }
            //Validamos que el comprobante cnt este vacio para crearlo
            if(empty($comprobante_cnt)){
                //Registramos el comprobante cnt y obtenemos el id el comprobante de recaudo
                $comprobante_cnt = save_head_cnt($numero, $fecha, 2, $descripcion, 1, 'NULL', 'NULL', $compania, $param, $tipoCnt);
            }
            //Validamos que el comprobante cnt homologación este vacio para crearlo
            if(empty($comprobante_hom)){
                //Registramos el comprobante cnt y obtenemos el id del comprobante de causación
                $comprobante_hom = save_head_cnt($numero, $fecha, 2, $descripcion, 1, 'NULL', 'NULL', $compania, $param, $tipo_h);
            }

            for ($b = 0; $b < count($coln_s); $b++) {                                   //Deplegamos el array de las columnas para obtener el valor
                $col = $coln_s[$b];                                                     //Obtenemos la columna en donde tomamos el valor de la misma
                $con = $cons_r[$b];                                                     //Obtenemos el id del concepto rubro cuenta
                $rx = exist_opertion($col);                                             //Verificamos si hay operador matematico
                if($rx > 0){                                                            //Si hay realiza el proceso para descubrirlo y obtener el resultado
                    $opr = get_operator($col);                                          //Obtenemos el operador matematico
                    $cols = explode($opr[0],$col);                                      //Obtenemos las columnas y las separamos por el operador
                    $value = "";                                                        //Inicializamos la variable value en vacio
                    for ($m=0; $m < count($cols); $m++) {                               //Desplegamos el array para obtener los valores en las consultas
                        //String con el valor de concepto y operador
                        $value .= $objWorksheet->getCellByColumnAndRow($cols[$m], $a)->getValue().$opr[0];
                    }
                    $value = substr($value,0,strlen($value)-1);                         //Quitamos el operador final
                    eval("\$lne=$value;");                                              //Evaluamos la expresión y asignamos su valor a la variable $lne
                }else{
                    $lne = $objWorksheet->getCellByColumnAndRow($col, $a)->getValue();  //Obtenemos el valor del concepto
                }

                if($lne != 0){                                                          //Validamos que el valor del concepto sea diferente de 0
                    $valor   = $lne;                                                    //Iniciamos la variable valor con el valor del concepto
                    $valorRD = abs($valor);                                             //Obtenemos el valor absoluto (-==+,+==+)
                    $values  = get_concept_rbc($con);                                   //Obtenemos id de conceptorubro, cuentas debito-credito y rubro fuente
                    $val     = explode(",", $values);                                   //Dividimos el String usando ,
                    $con_rub = $val[0];                                                 //Id de concepto rubro
                    $cuenta_debito  = $val[1];                                          //Id de cuenta débito
                    $cuenta_credito = $val[2];                                          //Id de cuenta crédito
                    $rubro_fuente   = get_rubro_fuente($con_rub);                       //Obtenemos el rubro fuente relacionado al rubro de concepto rubro
                    $tercero        = get_tercero($con);                                //Obtenemos el Id del tercero relacionado al concepto
                    if(!empty($comprobante_pptal)  || $comprobante_pptal != '0'){//Validamos que la variable no este vacia o tenga el valor 0
                        $detalle_pptal = get_detalle_pptal($rubro_fuente, $con_rub, $comprobante_pptal);
                        $valorRR += $valorRD;
                        if(empty($detalle_pptal)){
                            //Registramos el detalle de comprobante pptal y obtenemos el id del registro
                            $detalle_pptal = save_detail_pptal($descripcion, $valorRD, $comprobante_pptal, $rubro_fuente, $con_rub, $tercero, 2147483647);
                            if(!empty($detalle_pptal) || $detalle_pptal != '0'){        //Validamos si el proceso de registro retorna algun valor
                                $x++;
                            }
                        }else{
                            $xxx  = 0;
                            $xxx  = obtner_valor_detalle_pptal($detalle_pptal);
                            $vdtp = $valorRD + $xxx;
                            agregar_valor_detalle_pptal($detalle_pptal, $vdtp);
                        }
                    }

                    if(!empty($comprobante_cnt)  || $comprobante_cnt != '0'){//Validamos que la variable no este vacia o tenga el valor 0
                        $detalle_afectado = get_detalle_cnt($cuenta_debito, $comprobante_cnt);
                        $nat = get_naturaleza($cuenta_debito);                          //Obtenemos el valor de la naturaleza de la cuenta
                        if(empty($detalle_afectado)){
                            if($nat == 1){
                                $valorD     = $valorRD * -1;                            //Valor para registrar a la cuenta débito en el proceso de recaudo
                                $naturaleza = 1;
                            }elseif($nat == 2){
                                $valorD     = $valorRD;                                 //Valor para registrar al credito
                                $naturaleza = 2;
                            }
                            //Registramos el detalle de comprobante de recaduo cnt con cuenta debtio, valor negativo y obtenemos el id del registro
                            $detalle_afectado = save_detail_cnt($fecha, $descripcion, $valorD, $cuenta_debito, $naturaleza, $tercero, 2147483647, 12, $comprobante_cnt, $detalle_pptal, 'NULL');
                        }else{
                            $xxx  = 0;
                            $xxx  = obtner_valor_detalle_cnt($detalle_afectado);        //Obtenemos el valor del detalle
                            $xxx  = $valorRD + abs($xxx);                               //Sumamos el valor anterior del detalle, con el del concepto
                            if($nat == 1){
                                $valorD     = $xxx * -1;                                //Valor para registrar a la cuenta débito en el proceso de recaudo
                                $naturaleza = 1;
                            }elseif($nat == 2){
                                $valorD     = $xxx;                                     //Valor para registrar al credito
                                $naturaleza = 2;
                            }
                            agregar_valor_detalle_cnt($detalle_afectado, $valorD);      //Actualizamos el valor del detalle
                        }

                        $detalle_recaudo = get_detalle_cnt($cnta_b, $comprobante_cnt);
                        if(empty($detalle_recaudo)){
                            $naturaleza = get_naturaleza($cnta_b);
                            $valorXX    = abs($valorRR);
                            //Registramos el detalle recaudo de comprobante de recaduo cnt con cuenta debtio, valor negativo y obtenemos el id del registro
                            $detalle_recaudo = save_detail_cnt($fecha, $descripcion, $valorXX, $cnta_b, $naturaleza, $tercero, 2147483647, 12, $comprobante_cnt, 'NULL', 'NULL');
                            if(!empty($detalle_recaudo)){
                                $y++;
                            }
                        }else{
                            $xxx = 0;
                            $xxx = obtner_valor_detalle_cnt($detalle_recaudo);          //Obtenemos el valor del detalle
                            $xxx = abs($xxx);
                            $valorRC = $valorRD + $xxx;                                 //Sumamos el valor, con el valor anterior
                            agregar_valor_detalle_cnt($detalle_recaudo, $valorRC);      //Modificamos el valor del detalle
                        }
                    }

                    if(!empty($comprobante_hom)  || $comprobante_hom != '0'){//Validamos que la variable no este vacia o tenga el valor 0
                        if($cuenta_debito != $cuenta_credito){
                            $detalle_causa_r = get_detalle_cnt($cuenta_debito, $comprobante_hom);
                            if(empty($detalle_causa_r)){
                                //Registramos el detalle del comprobante de causación con cuenta debito y naturaleza debito
                                $detalle_causa_r = save_detail_cnt($fecha, $descripcion, $valorRD, $cuenta_debito, 1, $tercero, 2147483647, 12, $comprobante_hom,'NULL',$detalle_afectado);
                                if(!empty($detalle_causa_r)){
                                    $w++;
                                }
                            }else{
                                $xxx      = 0;
                                $xxx      = obtner_valor_detalle_cnt($detalle_causa_r);
                                $valorDCD = $valorRD + abs($xxx);
                                $udd_     = agregar_valor_detalle_cnt($detalle_causa_r, $valorDCD);
                            }

                            $detalle_causa_c = get_detalle_cnt($cuenta_credito, $comprobante_hom);
                            if(empty($detalle_causa_c)){
                                //Registramos el detalle del comprobante de causación con cuenta credito y naturaleza crédito
                                $detalle_causa_c = save_detail_cnt($fecha, $descripcion, $valorRD, $cuenta_credito, 2, $tercero, 2147483647, 12, $comprobante_hom, 'NULL', $detalle_afectado);
                            }else{
                                $xxx      = 0;
                                $xxx      = obtner_valor_detalle_cnt($detalle_causa_c);
                                $valorDCR = $valorRD + abs($xxx);
                                agregar_valor_detalle_cnt($detalle_causa_c, $valorDCR);
                            }
                        }
                    }
                }
            }
            break;
    }
}
$html = "";
$html .= "<!DOCTYPE html>";
$html .= "\n<html>";
$html .= "\n<head>";
$html .= "\n\t<meta charset=\"utf-8\">";
$html .= "\n\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">";
$html .= "\n\t<title>Registro de Reacudo Acumulación por Dia</title>";
$html .= "\n\t<link rel=\"stylesheet\" href=\"../css/bootstrap.min.css\">";
$html .= "\n\t<link rel=\"icon\" href=\"../img/AAA.ico\" />";
$html .= "\n</head>";
$html .= "\n<body>";
$html .= "\n\t<div class=\"row content\">";
$html .= "\n\t\t<div class=\"col-sm-12 col-md-12 col-lg-12\">";
$html .= "\n\t\t\t<h2 class=\"text-center\">Registro de Recaudo Acumulado por Día</h2>";
$html .= "\n\t\t\t<h3 class=\"text-center\">Información Registrada Correctamente.</h3>";
$html .= "\n\t\t\t<a href=\"../subirArchivoPredial.php\"><h4 class=\"text-center\">Volver</h4></a>";
$html .= "\n\t\t<div>";
$html .= "\n\t<div>";
$html .= "\n</body>";
$html .= "\n</html>";

echo $html;