<?php

@session_start();
@ini_set('max_execution_time', 0);

require ('../Conexion/ConexionPDO.php');
require ('../ExcelR/Classes/PHPExcel/IOFactory.php');

require ('../modelAlmacen/almacen.php');
require ('../modelAlmacen/dependencia.php');
require ('../modelAlmacen/detallemov.php');
require ('../modelAlmacen/producto.php'); 
require ('../modelAlmacen/movPro.php');

$con = new ConexionPDO();
$alm = new almacen();
$det = new detallemov();
$pro = new producto();
$pEs = new productoEsp();
$mPr = new movPro();
$dep = new dependencia();

$total_m = 0;
$salidas = 0;
$entradas = 0;
$totalPro = 0;
$proespec = 0;
$xhtml = "";

//para crear el .txt de los terceros no registrados
$nombreI = "Terceros_No_Registrados";
$txtName = $nombreI . "_" . "fecha" . ".txt";
$sfile = '../documentos/almacen/txt/' . $txtName;
$espacio = "           ";
$espacioT = "        ";
//$lineas .= 'Identificacion' . "\r\n";
$action = $_REQUEST['action'];
if (!empty($_FILES['flDoc']['tmp_name'])) {
    $file = $_FILES['flDoc']['tmp_name'];
    $objReader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = PHPExcel_IOFactory::load($file);
    $param = $_SESSION['anno'];
    $compania = $_SESSION['compania'];
    switch ($action) {
        case 1:
            #*** Validar TERCERO ***#            
            #*** verifica que si el tercero no existe no realiza el cargue del archivo
            if ($objPHPExcel->setActiveSheetIndexByName('RESPONSABLE')) {
                $fila = 2;
                $html = array();
                $nr = 0;
                $objWorksheet = $objPHPExcel->setActiveSheetIndexByName('RESPONSABLE');
                $total_filas = $objWorksheet->getHighestRow();
                for ($a = 0; $a < $total_filas; $a++) {
                    $identificacion = $objWorksheet->getCellByColumnAndRow(0, $fila)->getCalculatedValue();
                    $rowter = $dep->obtenerTercero($identificacion, $compania);
                    $rowter = $rowter[0][0];
                    if (empty($identificacion)) {
                        break;
                    } else {
                        if (empty($rowter[0][0])) {
                            if (in_array($identificacion, $html)) {
                                
                            } else {
                                array_push($html, $identificacion);
                            }
                        }
                    }
                    $fila ++;
                }
                if (count($html) > 0) {
                    $nr = "Terceros no encontrados";
                } else {
                    $nr = 0;
                    array_push($html, 0);
                }
                $datos = array("html" => $html, "rta" => $nr);
                echo json_encode($datos);
            }
            break;
        case 2:
            #*** Validar ELEMENTO ***#
            if ($objPHPExcel->setActiveSheetIndexByName('MOVIMIENTO')) {
                $fila = 2;
                $html = array();
                $nr = 0;
                $objWorksheet = $objPHPExcel->setActiveSheetIndexByName('MOVIMIENTO');
                $total_filas = $objWorksheet->getHighestRow();
                for ($a = 0; $a < $total_filas; $a++) {
                    $codigo = $objWorksheet->getCellByColumnAndRow(7, $fila)->getCalculatedValue();
                    $rowelem = $dep->obtenerElemento($codigo, $compania);
                    $rowelem = $rowelem[0][0];
                    if (empty($codigo)) {
                        break;
                    } else {
                        if (empty($rowelem[0][0])) {
                            if (in_array($codigo, $html)) {
                                
                            } else {
                                array_push($html, $codigo);
                            }
                        }
                    }
                    $fila ++;
                }
                if (count($html) > 0) {
                    $nr = "Elementos no encontrados";
                } else {
                    $nr = 0;
                    array_push($html, 0);
                }
                $datos = array("html" => $html, "rta" => $nr);
                echo json_encode($datos);
            }
            break;
        case 3:
            #*** registrar DEPENDENCIA ***#
            if ($objPHPExcel->setActiveSheetIndexByName('DEPENDENCIA')) {
                $objWorksheet = $objPHPExcel->setActiveSheetIndexByName('DEPENDENCIA');
                $total_filas = $objWorksheet->getHighestRow();
                $fila = 2;
                $nombre = 'varios';
                $movimiento = 0;
                $activa = 0;
                $html = array();
                $nr = 0;
                $depinsert =0;
                $centro = $alm->obtener_centro($nombre, $param);
                for ($a = 0; $a < $total_filas; $a++) {
                    $sigla = $objWorksheet->getCellByColumnAndRow(0, $fila)->getCalculatedValue();
                    if (empty($sigla)) {
                        break;
                    } else {
                        $dependencia = $dep->obtenerDependencia($sigla, $compania);
                        #*** Si no existe busca el tipo de dependencia e inserta la dependencia
                        if (empty($dependencia[0])) {
                            $nombre = $objWorksheet->getCellByColumnAndRow(2, $fila)->getCalculatedValue();
                            $nomdep = strpos($nombre, 'bodega');
                            if ($nomdep === true) {
                                $nombred = 'bodega';
                            } else {
                                $nomdep = strpos($nombre, 'servicio');
                                if ($nomdep === true) {
                                    $nombred = 'servicio';
                                } else {
                                    $nombred = 'dependencia';
                                }
                            }
                            $tipodep = $dep->obtener_tipo_dep($nombred);
                            $dep->sigla = $sigla;
                            $dep->nombre = $nombre;
                            $dep->movimiento = $movimiento;
                            $dep->activa = $activa;
                            $dep->compania = $compania;
                            $dep->centro = $centro[0];
                            $dep->tipo = $tipodep[0];
                            $regdep = $dep->registrar_dep($dep);
                            if ($regdep) {
                                $depinsert++;
                            } else {
                                if (in_array($sigla, $html)) {
                                    
                                } else {
                                    array_push($html, $sigla);
                                }
                            }
                        }
                    }
                    $fila ++;
                }
                if (count($html) > 0) {
                    $nr = "Dependencias no registradas";
                } else {
                    $nr = 0;
                    array_push($html, 0);
                }
                $datos = array("html" => $html, "rta" => $nr, "dependencias" => $depinsert);
                echo json_encode($datos);
            }
            break;
        case 4:
            #*** registrar DEPENDENCIA_RESONSABLE***#
            if ($objPHPExcel->setActiveSheetIndexByName('DEPEDENCIA RESPONSABLE')) {
                $objWorksheet = $objPHPExcel->setActiveSheetIndexByName('DEPEDENCIA RESPONSABLE');
                $total_filas = $objWorksheet->getHighestRow();
                $fila = 2;
                $nombre = 'varios';
                $movimiento = 0;
                $activa = 0;
                $html = array();
                $nr = 0;
                $depresinsert = 0;
                for ($a = 0; $a < $total_filas; $a++) {
                    $dependencia = $objWorksheet->getCellByColumnAndRow(0, $fila)->getCalculatedValue();
                    $tercero = $objWorksheet->getCellByColumnAndRow(1, $fila)->getCalculatedValue();
                    $tercero = (int)$tercero;

                    if (empty($dependencia)) {
                        break;
                    } else {
                        $id_terc = $dep->obtenerTercero($tercero, $compania);
                        $responsable = $id_terc[0];
                        $sqldep = $dep->obtenerDependencia($dependencia, $compania);
                        $iddependencia = $sqldep[0];
                        if(!empty($responsable) && !empty($iddependencia)){ 
                            $sqldepres = $dep->obtnerDependenciaresponsable($iddependencia, $responsable);
                            $depres = $sqldepres[0];
                            //var_dump((empty($depres) && !empty($responsable) && !empty($iddependencia)));
                            if (empty($depres) && !empty($responsable) && !empty($iddependencia)) {
                                $dep->dependencia = $iddependencia;
                                $dep->responsable = $responsable;
                                $dep->movimiento = 1;
                                $dep->estado = 1;
                                $regdepres = $dep->registrar_dep_resp($dep);
                                if ($regdepres) {
                                    $depresinsert ++;
                                } else {
                                    if (in_array($dependencia, $html)) {
                                        
                                    } else {
                                        array_push($html, $dependencia);
                                    }
                                }
                            }
                        }
                    }
                    $fila ++;
                }
                if (count($html) > 0) {
                    $nr = "Dependencia_Responsable no registradas";
                } else {
                    $nr = 0;
                    array_push($html, 0);
                }
                $datos = array("html" => $html, "rta" => $nr, "dep_res_insert" => $depresinsert);
                echo json_encode($datos);
            }
            break;
        case 5:
            #*** registrar MOVIMIENTO***#
            if ($objPHPExcel->setActiveSheetIndexByName('MOVIMIENTO')) {
                $objWorksheet = $objPHPExcel->setActiveSheetIndexByName('MOVIMIENTO');
                $n = 1;
                $i = 2;
                $fl = 0;
                $insert = 500;
                $total_filas = $insert;
                $nombre = 'varios';
                $movimiento = 0;
                $activa = 0;
                $html = array();
                $nr = 0;
                $entradas = 0;
                $salidas = 0;
                while ($n > 0) {
                    $centro = $alm->obtener_centro($nombre, $param);
                    for ($fila = $i; $fila <= $total_filas; $fila++) {
                        $anno = $objWorksheet->getCellByColumnAndRow(0, $fila)->getCalculatedValue();
                        $tipo = $objWorksheet->getCellByColumnAndRow(1, $fila)->getCalculatedValue();
                        $tipoM = $alm->obtener_tipo($tipo);
                        $numero = $objWorksheet->getCellByColumnAndRow(2, $fila)->getCalculatedValue();
                        if (empty($anno)) {
                            $n = 0;
                            break;
                        } else {
                            $x = strpos("$numero", "$anno");
                            if ($x === false) {
                                $numero = $alm->valida_numero($numero, $anno);
                            }

                            $codigo  = $objWorksheet->getCellByColumnAndRow(7, $fila)->getCalculatedValue();
                            $fecha_a = $objWorksheet->getCellByColumnAndRow(8, $fila)->getCalculatedValue();
                            $c_placa = $objWorksheet->getCellByColumnAndRow(9, $fila)->getCalculatedValue();
                            $c_descp = $objWorksheet->getCellByColumnAndRow(10, $fila)->getCalculatedValue();
                            $c_cant  = $objWorksheet->getCellByColumnAndRow(11, $fila)->getCalculatedValue();
                            $c_valU  = $objWorksheet->getCellByColumnAndRow(12, $fila)->getCalculatedValue();
                            $c_marca = $objWorksheet->getCellByColumnAndRow(13, $fila)->getCalculatedValue();
                            $vida_u  = $objWorksheet->getCellByColumnAndRow(16, $fila)->getCalculatedValue();
                            if (empty($vida_u)) {
                                $vida_u = "NULL";
                            } else {
                                $vida_u = $vida_u;
                            }
                            if (is_float($fecha_a)) {
                                $celda = $objWorksheet->getCellByColumnAndRow(8, $fila)->getValue();
                                $fecha_ = PHPExcel_Shared_Date::ExcelToPHP($celda);
                                $fecha_ = date("Y-m-d", $fecha_);
                                $fecha_a = str_replace($fecha_a, $fecha_, $fecha_a);
                            } else {
                                if (!empty($fecha_a)) {
                                    $ff = explode("/", $fecha_a);
                                    $fecha_a = "$ff[2]-$ff[1]-$ff[0]";
                                }
                            }

                            $movimiento = $alm->obtener_mov($tipoM[0], $numero, $compania); #Validar si el movimiento ya existe en esa compañia
                            if (empty($movimiento[0])) { 
                                $v_fecha = $objWorksheet->getCellByColumnAndRow(3, $fila)->getCalculatedValue();

                                if (is_float($v_fecha)) {
                                    $cell = $objWorksheet->getCellByColumnAndRow(3, $fila)->getValue();
                                    $_fecha = PHPExcel_Shared_Date::ExcelToPHP($cell);
                                    $fecha1 = date("Y-m-d", $_fecha);
                                    $v_fecha = str_replace($v_fecha, $fecha1, $v_fecha);
                                } else {
                                    $fecha = explode("/", $v_fecha);
                                    $v_fecha = $fecha[2] . "-" . $fecha[1] . "-" . $fecha[0];
                                }
                                if (empty($fecha_a)) {
                                    $fecha_a = $v_fecha;
                                }

                                $desc = $objWorksheet->getCellByColumnAndRow(4, $fila)->getCalculatedValue();
                                $desc = mb_strtoupper(trim($desc));
                                $depnd = $objWorksheet->getCellByColumnAndRow(5, $fila)->getCalculatedValue();
                                $depende = $dep->obtenerDependencia($depnd, $compania);
                                $tercero = $objWorksheet->getCellByColumnAndRow(6, $fila)->getCalculatedValue();
                                
                                if (strlen($tercero) < 3) {
                                    $tercero = $alm->validar_codigo_t($tercero);
                                }
                                $tercero = (int)$tercero;
                                $id_terc = $dep->obtenerTercero($tercero, $compania);
                                $resp = $id_terc[0];

                                $alm->numero = $numero;
                                $alm->fecha = $v_fecha;
                                $alm->descripcion = $desc;
                                $alm->porcIvaGlobal = 0;
                                $alm->tipoMovimiento = $tipoM[0];
                                $alm->parametrizacionAnno = $param;
                                $alm->compania = $compania;
                                $alm->responsable = $resp;
                                $alm->tercero = $id_terc[0];
                                $alm->dependencia = $depende[0];
                                $alm->centrocosto = $centro[0];
                                $alm->proyecto = 2147483647;
                                $alm->estado = 2;
                                //var_dump($data);
                                $data = $alm->registrar($alm);
                                if ($data==1) {                                    
                                    if ($tipoM[1] == 2) {
                                        $entradas++;
                                    } elseif ($tipoM[1] == 3) {
                                        $salidas++;
                                    }
                                    # Reisgrar detalle
                                    $planMov = $det->buscar_plan_mov($codigo, $compania);
                                    $tipoInv = $det->buscar_tipo_inventario($planMov[0], $compania);
                                    $ficha = $det->buscarficha($planMov[0]);
                                    $asi = $det->asignar_ficha($planMov[0], $ficha[0]);
                                    $id_aso = "NULL";
                                    $movimiento = $alm->obtener_mov($tipoM[0], $numero);
                                    $det->vida_util = $vida_u;
                                    if ($tipoM[1] == "2") {
                                        $det->cantidad = $c_cant;
                                        $det->valor = $c_valU;
                                        $det->iva = 0;
                                        $det->movimiento = $movimiento[0];
                                        $det->planmovimiento = $planMov[0];
                                        $rest_d = $det->registrar($det);
                                        if ($tipoInv == 2) {
                                            if ($rest_d == 1) {
                                                $id_de = $det->buscar_detalle($movimiento[0]);
                                                $c_serie = $pro->obtnerUltimoConsecutivo();

                                                $x = 0;

                                                if (empty($c_serie)) {
                                                    $x = 0;
                                                } else {
                                                    $x = (int) $c_serie;
                                                }

                                                for ($w = 0; $w < $c_cant; $w++) {
                                                    $totalPro++;
                                                    $x++;

                                                    $pro->descripcion = $c_descp;
                                                    $pro->valor = $c_valU;
                                                    $pro->fecha = $fecha_a;
                                                    $res1 = $pro->registrar($pro);
                                                    $id_pro = $pro->ultimo_registro();

                                                    $mPr->detallemovimiento = $id_de;
                                                    $mPr->producto = $id_pro;
                                                    $mPr->registrar($mPr);

                                                    $pEs->valor = "NULL";
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 1;
                                                    $pEs->registrar($pEs);

                                                    if (empty($c_marca)) {
                                                        $c_marca = "NULL";
                                                    } else {
                                                        $c_marca = $c_marca;
                                                    }

                                                    $pEs->valor = $c_marca;
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 2;
                                                    $pEs->registrar($pEs);

                                                    $pEs->valor = "NULL";
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 3;
                                                    $pEs->registrar($pEs);

                                                    $pEs->valor = "NULL";
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 4;
                                                    $pEs->registrar($pEs);

                                                    $pEs->valor = "NULL";
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 5;
                                                    $pEs->registrar($pEs);

                                                    $pEs->valor = $c_placa;
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 6;
                                                    $pEs->registrar($pEs);

                                                    if (empty($c_descp)) {
                                                        $c_descp = "NULL";
                                                    } else {
                                                        $c_descp = $c_descp;
                                                    }

                                                    $pEs->valor = $c_descp;
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 7;
                                                    $pEs->registrar($pEs);

                                                    $pEs->valor = "NULL";
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 8;
                                                    $pEs->registrar($pEs);
                                                    $proespec++;
                                                }
                                            }
                                        }
                                    } else if ($tipoM[1] == 3) {
                                        $val = $pro->obtenerProductoEntrada($c_placa, $compania,$codigo);
                                        $id_aso = $val[0];
                                        $id_pro = $val[1];
                                        if (!empty($id_aso)) {
                                            $det->cantidad = (int) $c_cant;
                                            $det->valor = $c_valU;
                                            $det->iva = 0;
                                            $det->movimiento = $movimiento[0];
                                            $det->planmovimiento = $planMov[0];
                                            $det->detalleasociado = $id_aso;
                                            $rest_d = $det->registrar_aso($det);
                                            $id_de = $det->buscar_detalle($movimiento[0]);
                                            $datosd = $pro->obtenerProductosDetalle($id_aso);
                                            if (count($datosd > 0)) {
                                                for ($w = 0; $w < count($datosd); $w++) {
                                                    $mPr->detallemovimiento = $id_de;
                                                    $mPr->producto = $datosd[$w];
                                                    $mPr->registrar($mPr);
                                                }
                                            }
                                        } else {
	                                        $det->cantidad = (int) $c_cant;
	                                        $det->valor = $c_valU;
	                                        $det->iva = 0;
	                                        $det->movimiento = $movimiento[0];
	                                        $det->planmovimiento = $planMov[0];
	                                        $det->detalleasociado = "NULL";
	                                        $rest_d = $det->registrar_aso($det);
                                        }
                                    }
                                } else {                                    
                                    # movimientos no insetados
                                    if (in_array($numero, $html)) {
                                        
                                    } else {
                                        array_push($html, $numero);
                                    }
                                }
                            } else {
                                
                                # movimientos que ya existen se le inserta el detalle 
                                    # Reisgrar detalle
                                    $planMov = $det->buscar_plan_mov($codigo, $compania);
                                    $tipoInv = $det->buscar_tipo_inventario($planMov[0], $compania);
                                    $ficha = $det->buscarficha($planMov[0]);
                                    $asi = $det->asignar_ficha($planMov[0], $ficha[0]);
                                    $id_aso = "NULL";
                                    $movimiento = $alm->obtener_mov($tipoM[0], $numero);
                                    $det->vida_util = $vida_u;
                                    if ($tipoM[1] == "2") {
                                        $det->cantidad = $c_cant;
                                        $det->valor = $c_valU;
                                        $det->iva = 0;
                                        $det->movimiento = $movimiento[0];
                                        $det->planmovimiento = $planMov[0];
                                        $rest_d = $det->registrar($det);
                                        if ($tipoInv == 2) {
                                            if ($rest_d == 1) {
                                                $id_de = $det->buscar_detalle($movimiento[0]);
                                                $c_serie = $pro->obtnerUltimoConsecutivo();

                                                $x = 0;

                                                if (empty($c_serie)) {
                                                    $x = 0;
                                                } else {
                                                    $x = (int) $c_serie;
                                                }

                                                for ($w = 0; $w < $c_cant; $w++) {
                                                    $totalPro++;
                                                    $x++;

                                                    $pro->descripcion = $c_descp;
                                                    $pro->valor = $c_valU;
                                                    $pro->fecha = $fecha_a;
                                                    $res1 = $pro->registrar($pro);
                                                    $id_pro = $pro->ultimo_registro();

                                                    $mPr->detallemovimiento = $id_de;
                                                    $mPr->producto = $id_pro;
                                                    $mPr->registrar($mPr);

                                                    $pEs->valor = "NULL";
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 1;
                                                    $pEs->registrar($pEs);

                                                    if (empty($c_marca)) {
                                                        $c_marca = "NULL";
                                                    } else {
                                                        $c_marca = $c_marca;
                                                    }

                                                    $pEs->valor = $c_marca;
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 2;
                                                    $pEs->registrar($pEs);

                                                    $pEs->valor = "NULL";
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 3;
                                                    $pEs->registrar($pEs);

                                                    $pEs->valor = "NULL";
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 4;
                                                    $pEs->registrar($pEs);

                                                    $pEs->valor = "NULL";
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 5;
                                                    $pEs->registrar($pEs);

                                                    $pEs->valor = $c_placa;
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 6;
                                                    $pEs->registrar($pEs);

                                                    if (empty($c_descp)) {
                                                        $c_descp = "NULL";
                                                    } else {
                                                        $c_descp = $c_descp;
                                                    }

                                                    $pEs->valor = $c_descp;
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 7;
                                                    $pEs->registrar($pEs);

                                                    $pEs->valor = "NULL";
                                                    $pEs->producto = $id_pro;
                                                    $pEs->fichainventario = 8;
                                                    $pEs->registrar($pEs);
                                                    $proespec++;
                                                }
                                            }
                                        }
                                    } else if ($tipoM[1] == 3) {
                                        $val = $pro->obtenerProductoEntrada($c_placa, $compania,$codigo);
                                        $id_aso = $val[0];
                                        $id_pro = $val[1];
                                        if (!empty($id_aso)) {
                                            $det->cantidad = (int) $c_cant;
                                            $det->valor = $c_valU;
                                            $det->iva = 0;
                                            $det->movimiento = $movimiento[0];
                                            $det->planmovimiento = $planMov[0];
                                            $det->detalleasociado = $id_aso;
                                            $rest_d = $det->registrar_aso($det);
                                            $id_de = $det->buscar_detalle($movimiento[0]);
                                            $datosd = $pro->obtenerProductosDetalle($id_aso);
                                            if (count($datosd > 0)) {
                                                for ($w = 0; $w < count($datosd); $w++) {
                                                    $mPr->detallemovimiento = $id_de;
                                                    $mPr->producto = $datosd[$w];
                                                    $mPr->registrar($mPr);
                                                }
                                            }
                                        } else {
                                            $det->cantidad = (int) $c_cant;
                                            $det->valor = $c_valU;
                                            $det->iva = 0;
                                            $det->movimiento = $movimiento[0];
                                            $det->planmovimiento = $planMov[0];
                                            $det->detalleasociado = "NULL";
                                            $rest_d = $det->registrar_aso($det);
                                        }
                                    }
                                    
                            }
                        }
                        $fl = $fila;
                    }
                    $i = $fl + 1;
                    $total_filas += $insert;
                }
                if (count($html) > 0) {
                    $nr = "Movimientos no registradas";
                } else {
                    $nr = "Se ha registrado correctamente";
                    array_push($html, 0);
                }
                $inserto = "Se han registrado:";
                $datos = array("html" => $html, "rta" => $nr, "inserto" => $inserto, "entradas" => $entradas, "salidas" => $salidas);
                echo json_encode($datos);
            }
            break;
    }
}

