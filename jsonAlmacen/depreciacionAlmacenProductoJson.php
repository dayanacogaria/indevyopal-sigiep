<?php
require      '../Conexion/conexion.php';
require_once '../modelAlmacen/producto.php';
require_once '../modelAlmacen/depreciacion.php';

$pro = new producto();
$dep = new depreciacion();
$pros  = array();
$tpro  = array();
$annos = array();
ini_set('max_execution_time', 0);
date_default_timezone_set('America/Bogota');

if(!empty($_POST['txtFechaFinal']) && !empty($_POST['sltProductoInicial']) && !empty($_POST['sltProductoFinal'])){
    $DteF = $mysqli->real_escape_string($_POST['txtFechaFinal']);
    $proI = $mysqli->real_escape_string($_POST['sltProductoInicial']);
    $proF = $mysqli->real_escape_string($_POST['sltProductoFinal']);

    $ult  = explode("/", $DteF);
    $prm  = explode("/", $DteF);

    $fechaFinal   = $dep->ultimoDia($ult[1], $ult[0]);
    $fechaInicial = $dep->primerDia($prm[1], $prm[0]);

    if($prm[1] != $ult[1]){
        $annos = $dep->obtenerAnos($prm[1], $ult[1]);
    }else{
        $annos[] = $prm[1];
    }

    $html = "";
    $html .= "<!DOCTYPE html>";
    $html .= "\n<html>";
    $html .= "\n<head>";
    $html .= "\n\t<meta charset=\"utf-8\">";
    $html .= "\n\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">";
    $html .= "\n\t<title>Proceso de Depreciación</title>";
    $html .= "\n\t<link rel=\"icon\" href=\"../img/AAA.ico\" />";
    $html .= "\n\t<link rel=\"stylesheet\" href=\"../css/bootstrap.min.css\">";
    $html .= "\n</head>";
    $html .= "\n<body>";
    $html .= "\n\t<div class=\"container\">";
    $html .= "\n\t\t<div class=\"row content\">";
    $html .= "\n\t\t\t<br/>";
    $html .= "\n\t\t\t<a href=\"../depreciacion_almacen_productos.php\">Volver</a>";
    $html .= "\n\t\t\t<table border=\"1\" class=\"display table-striped table-hover table-condensed table-bordered\" cellspacing=\"0\" width=\"100%\">";
    $html .= "\n\t\t\t\t<tbody>";
    $html .= "\n\t\t\t\t\t<tr>";
    $html .= "\n\t\t\t\t\t\t<td class=\"text-right\"><label>Fecha Inicial:</label></td>";
    $html .= "\n\t\t\t\t\t\t<td>";
    $html .= $fechaInicial;
    $html .= "</td>";
    $html .= "\n\t\t\t\t\t\t<td class=\"text-right\"><label>Fecha Final:</label></td>";
    $html .= "\n\t\t\t\t\t\t<td>";
    $html .= $fechaFinal;
    $html .= "</td>";
    $html .= "\n\t\t\t\t</tbody>";
    $html .= "\n\t\t\t</table>";
    $res_p = $dep->encontrarProductosPeriodo($proI, $proF);

    while($dataPro = $res_p->fetch_row()) {

        $id_unico = $dataPro[0]; $meses  = $dataPro[1]; $vidaUtil = $dataPro[2]; $fechaEda = $dataPro[3];
        $valorTpa = $dataPro[4]; $nombre = empty($dataPro[5])?"":$dataPro[5]; $fecha_a = $dataPro[6];

        $tipoPlan = $dep->obtenerPlanInventarioProducto($id_unico);
        if($tipoPlan == 2){
            $fa  = strtotime($fecha_a);
            $f_a = date("d/m/Y", $fa);

            if($_REQUEST['optDepIni'] == 0){
                $fc_parm   = $dep->obtnerValorParametroBasico();
                $ult_param = explode("-", $fc_parm);

                $fecha_param = $dep->primerDia($ult_param[0], $ult_param[1]);

                if($fecha_a < $fecha_param){
                    $fechaEda = $fecha_a;
                }else{
                    $fechaEda = $dataPro[3];
                }
            }else{
                $fechaEda = $fechaInicial;
            }

            if(empty($fecha_a)){
                $fechaEda = $dataPro[3];
            }

            $dataTime = $dep->obtnerMesA($fechaEda);
            $dateTme  = explode(",",$dataTime);

            $valorPro = $pro->obtnerValorProducto($id_unico);
            $serie_p  = $pro->obtnerCodigoProducto($id_unico);

            $html .= "\n\t\t\t<h3 class=\"text-center text-danger\">";
            $html .= "\n\t\t\t\t<label class='col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right'>Producto : </label>";
            $html .= "\n\t\t\t\t<label class='col-xs-6 col-sm-6 col-md-6 col-lg-6 text-left'>$serie_p - $nombre</label>";
            $html .= "\n\t\t\t</h3>";
            $html .= "\n\t\t\t<h4 class=\"text-center\">";
            $html .= "\n\t\t\t\t<label class='col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right'>Fecha Adquisición:</label>";
            $html .= "\n\t\t\t\t<label class='col-xs-6 col-sm-6 col-md-6 col-lg-6 text-left'>$f_a</label>";
            $html .= "\n\t\t\t</h4>";
            $html .= "\n\t\t\t<h4 class=\"text-center\">";
            $html .= "\n\t\t\t\t<label class='col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right'>Valor:</label>";
            $html .= "\n\t\t\t\t<label class='col-xs-6 col-sm-6 col-md-6 col-lg-6 text-left'>$".number_format($valorPro,2,',','.')."</label>";
            $html .= "\n\t\t\t</h4>";
            $html .= "\n\t\t\t<h4 class=\"text-center\">";
            $html .= "\n\t\t\t\t<label class='col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right'>Vida Util:</label>";
            $html .= "\n\t\t\t\t<label class='col-xs-6 col-sm-6 col-md-6 col-lg-6 text-left'>$valorTpa</label>";
            $html .= "\n\t\t\t</h4>";
            $fechaEda = strtotime($fechaEda);
            $fechaEda = date("Y-m-d", $fechaEda);
            $inicio = new DateTime($fechaEda);
            $fin    = new DateTime($fechaFinal);
            $nmes   = 0;

            while($inicio <= $fin){
                $periodo = $inicio->format('Y-m');
                $tiempo  = $inicio->format('Y-m-d');
                $per     = $dep->separarObjeto("-", $periodo);

                if($tiempo == $fechaEda){
                    $fechaI = $fechaEda;
                }else{
                    $fechaI = $dep->primerDia($per[0], $per[1]);
                }

                $fechaF      = $dep->ultimoDia($per[0], $per[1]);
                $fechaSalida = $dep->encontrarSalidas($id_unico, $fechaI, $fechaF);
                $dia         = $dep->obtenerDiaFecha($fechaI);
                $diasM       = $dep->diasFaltantes($fechaI, $fechaF);

                $valorE  = $dep->obtnerValorProductoEntrada($id_unico);
                $valorD  = $dep->obtnerValorTotalDepreciacionProducto($id_unico);
                $saldoD  = $valorE - $valorD;
                $valor   = 0;
                if(($saldoD <= $valorPro) && ($valorD <= $valorPro)){
                    $meses   = $valorTpa;
                    if($meses < 0 && $vidaUtil < 0){
                        $valor     = $valorUtil;
                    }

                    $valor         = $dep->depreciaicion_1($valorPro, $meses);
                    $ultimoDiaAnno = $dep->obtenerUltimodiaAnno($per[0]);
                    $param         = $dep->obtnerValorAnno($per[0]);
                    if(!empty($param)){
                        $objParam = $dep->separarObjeto(",", $param);

                        list($valorUVT, $slrioMin, $minCntia, $menCntia) = array(
                            $objParam[0], $objParam[1], $objParam[2], $objParam[3]
                        );

                        if(!empty($valorUVT) && !empty($slrioMin) && !empty($minCntia) && !empty($menCntia)){
                            if($valorPro <= $menCntia){
                                $valor = $valorPro;
                            }

                            if($valorPro > $menCntia && $valorPro <= $minCntia){
                                $dsFA  = $dep->diasFaltantes($fechaI, $ultimoDiaAnno);
                                $valor = $dep->depreciacion_3($valorPro, $dsFA);
                            }
                        }
                    }

                    $tieneSalida = $dep->tieneSalida($id_unico);

                    if(!empty($tieneSalida)){

                        if(!empty($fechaSalida) && ($fechaEda != $fecha_a)){

                            $dtaf      = $dep->separarObjeto("-",$fechaSalida);
                            $fechaFinS = $dep->ultimoDia($dtaf[0], $dtaf[1]);
                            $diasM     = $dep->diasFaltantes($fechaSalida, $fechaFinS);
                            $dia       = $dep->obtenerDiaFecha($fechaI);

                            $fechaI = $fechaSalida;
                            $fechaF = $fechaFinS;
                            $valor  = $dep->depreciacion_2($valorPro, $meses, $diasM);
                        }
                    }
                    if(empty($tieneSalida)){$valor = 0;}
                }else{
                    $valor = 0;
                }

                if($valorD > $valorPro){$valor = 0;}

                if(empty($saldoD)){$valor = 0;}

                if($saldoD < 0){$valor = 0;}

                if($valorE == $valorD){$valor = 0;}

                if($saldoD > $valorPro){$valor = 0;}

                if($valorD == $valorPro){$valor = 0;}

                if($valorD > $valorE){$valor = 0;}

                if($valorD < $valorPro){
                }else{
                    if($valor > $saldoD){$valor = 0;}
                }

                if($valor > $saldoD){
                    $valor = 0;
                }
                $v       = "$".number_format(round($valor), 2, ',', '.');
                $existeD = $dep->existeDepreciacion($fechaF, $id_unico);
                if(!empty($existeD)){
                    $red = $dep->eliminarDepreciacion($existeD);
                }

                $html .= "\n\t\t\t<div class='form-group producto'>";
                $html .= "\n\t\t\t\t<label class=\"col-xs-6 col-sm-3 col-md-2 col-lg-2 text-right\">Fecha Inicial: </label>";
                $html .= "\n\t\t\t\t<label class=\"col-xs-6 col-sm-3 col-md-2 col-lg-2\">";
                $html .= $fechaI;
                $html .= "</label>";
                $html .= "\n\t\t\t\t<label class=\"col-xs-6 col-sm-3 col-md-2 col-lg-2 text-right\">Fecha Final: </label>";
                $html .= "\n\t\t\t\t<label class=\"col-xs-6 col-sm-3 col-md-2 col-lg-2\">";
                $html .= $fechaF;
                $html .= "</label>";
                $html .= "\n\t\t\t\t<label class=\"col-xs-6 col-sm-3 col-md-2 col-lg-2 text-right\">Dias Mes: </label>";
                $html .= "\n\t\t\t\t<label class=\"col-xs-6 col-sm-3 col-md-2 col-lg-2\">";
                $html .= $diasM;
                $html .= "</label>";
                $html .= "\n\t\t\t\t<label class=\"col-xs-6 col-sm-3 col-md-2 col-lg-2 text-right\">Valor Depreciación: </label>";
                $html .= "\n\t\t\t\t<label class=\"col-xs-6 col-sm-3 col-md-10 col-lg-10 text-danger\">";
                $html .= $v;
                $html .= "</label>";
                $html .= "\n\t\t\t\t<label class=\"col-xs-6 col-sm-3 col-md-2 col-lg-2 text-right\">Saldo Depreciación: </label>";
                $html .= "\n\t\t\t\t<label class=\"col-xs-6 col-sm-3 col-md-10 col-lg-10 text-danger\">";
                $html .= $saldoD;
                $html .= "</label>";
                $html .= "\n\t\t\t</div>";

                $dep->producto  = $id_unico;
                $dep->fecha_dep = $fechaF;
                $dep->dias_dep  = $diasM;
                $dep->valor     = $valor;
                $dep->registrar($dep);

                $fecha_suma = strtotime('+1 month', strtotime($fechaI));
                $inicio     = date("Y-m-d", $fecha_suma);
                $inicio     = new DateTime($inicio);
                $nmes++;
            }
            $html .= "\n\t\t\t\t<label class=\"col-xs-6 col-sm-3 col-md-2 col-lg-2 text-right\">Meses Depreciados: </label>";
            $html .= "\n\t\t\t\t<label class=\"col-xs-6 col-sm-3 col-md-10 col-lg-10\">";
            $html .= $nmes;
            $html .= "</label>";
        }
    }
}
$html .= "\n\t\t</div>";
$html .= "\n\t</div>";
$html .= "\n</body>";
$html .= "\n</html>";
echo $html;