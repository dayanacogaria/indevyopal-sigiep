<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=RelacionBienesDeterioradosAcumulado.xls");
ini_set('max_execution_time', 0);
session_start();
require_once ("../Conexion/conexion.php");

require_once ('../modelAlmacen/producto.php');
require_once ('../modelAlmacen/depreciacion.php');

if(!empty($_POST['txtPeridoF'])){
    $dep = new depreciacion();
    $pro = new producto();
    $compania = $_SESSION['compania'];
    $usuario  = $_SESSION['usuario'];
    $datosC   = $dep->tercero_informe($compania);

    $nombreCompania = $datosC[0];
    $nitCompania    = $datosC[1]." - ".$datosC[3];
    $ruta           = $datosC[2];

    $DteF = $mysqli->real_escape_string($_REQUEST['txtPeridoF']);

    $ult = $dep->separarObjeto("/", $DteF);

    $fechaInicial = "1900-01-01";
    $fechaFinal   = $dep->ultimoDia($ult[1], $ult[0]);

    $perIni       = new DateTime($fechaFinal);

    $res_p = $dep->encontrarDepreciacionProductosPeriodo($fechaInicial, $fechaFinal);

    $pros = array();
    while($row = $res_p->fetch_row()){
        list($id_unico, $nombre, $codigo) = array($row[0], $row[1], $row[2]);
        $pros[]   = "$id_unico,$nombre,$codigo";
    }

    $html = "";
    $html .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
    $html .= "\n<html xmlns= \"http://www.w3.org/1999/xhtml\">";
    $html .= "\n<head>";
    $html .= "\n\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
    $html .= "\n\t<title>Informe de DEPRECIACION</title>";
    $html .= "\n</head>";
    $html .= "\n<body>";
    $html .= "\n\t<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">";
    $html .= "\n\t\t<thead>";
    $html .= "\n\t\t\t<tr>";
    $html .= "\n\t\t\t\t<th colspan=\"13\" align=\"center\">".$nombreCompania."<br/>NIT: $nitCompania<br/>RELACIÓN DE BIENES DEPRECIADOS ACUMULADO</th>";
    $html .= "\n\t\t\t</tr>";
    $html .= "\n\t\t\t<tr>";
    $html .= "\n\t\t\t\t<th>CODIGO<br/>PRODUCTO</th>";
    $html .= "\n\t\t\t\t<th>CODIGO<br/>CONTABLE</th>";
    $html .= "\n\t\t\t\t<th>NOMBRE<br/>PRODUCTO</th>";
    $html .= "\n\t\t\t\t<th>PLACA</th>";
    $html .= "\n\t\t\t\t<th>FECHA<br/>ADQUISICIÓN</th>";
    $html .= "\n\t\t\t\t<th>FECHA<br/>ENTRADA</th>";
    $html .= "\n\t\t\t\t<th>FECHA<br/>SALIDA</th>";
    $html .= "\n\t\t\t\t<th>VALOR<br/>COMPRA</th>";
    $html .= "\n\t\t\t\t<th>VIDA<br/>UTIL</th>";
    $html .= "\n\t\t\t\t<th>DRP<br/>MES</th>";
    $html .= "\n\t\t\t\t<th>MES<br/>ACUM</th>";
    $html .= "\n\t\t\t\t<th>DETERIORO<br/>ACUMULADO</th>";
    $html .= "\n\t\t\t\t<th>SALDO</th>";
    $html .= "\n\t\t\t</tr>";
    $html .= "\n\t\t</thead>";
    $html .= "\n\t\t<tbody>";
    $totalV = 0;
    $totalD = 0;
    $totalA = 0;
    for ($i = 0; $i < count($pros); $i++) {
        $objPro   = $dep->separarObjeto(",",$pros[$i]);
        list($id_unico, $nombre, $codigo) = array($objPro[0], $objPro[1], $objPro[2]);
        $cod_padre= substr($codigo, 0, 5);
        $ccnt     = $dep->obtner_cod_dep_cnt($cod_padre);
        $serie    = $pro->obtnerCodigoProducto($id_unico);
        $placa    = $pro->obtnerCodigoProductoPlaca($id_unico);
        $fecha    = $dep->obtnerFechaAquisicion($id_unico);
        if(empty($fecha)){
            $fecha = $pro->obtnerFechaEntrada($id_unico);
        }
        $fechaE   = $pro->obtnerFechaEntrada($id_unico);
        $fechaS   = $dep->obtnerFechaSalida($id_unico);
        $vidaU    = $pro->obtnerVidaUtil($id_unico);
        $vida     = $dep->obtner_vida_util($codigo);
        $valorPro = $pro->obtnerValorEntrada($id_unico);
        $valorD   = $dep->obtnerValorAcumuladoDrp($id_unico, $fechaInicial, $fechaFinal);
        if(empty($fecha)){
            $fecha    = $pro->obtnerFechaEntrada($id_unico);
        }
        $totalV += $valorPro;
        $totalD += $valorD;
        $saldo   = $valorPro - $valorD;
        $totalA += $saldo;
        $mes     = $dep->obnterDpreciacionMes($fechaFinal, $id_unico);
        $mesesA  = $vida - $vidaU;
        $html .= "\n\t\t\t<tr>";
        $html .= "\n\t\t\t\t<td align=\"right\">$codigo</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">$ccnt</td>";
        $html .= "\n\t\t\t\t<td align=\"left\">$nombre</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">$serie</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">$fecha</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">$fechaE</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">$fechaS</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">".number_format($valorPro,2,',','.')."</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">$vida</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">".number_format($mes,2,',','.')."</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">"."$mesesA</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">".number_format($valorD,2,',','.')."</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">".number_format($saldo,2,',','.')."</td>";
        $html .= "\n\t\t\t</tr>";
    }
    $html .= "\n\t\t</tbody>";
    $html .= "\n\t\t<tfoot>";
    $html .= "\n\t\t\t<tr>";
    $html .= "\n\t\t\t\t<td colspan=\"6\" align=\"right\"><b>TOTAL</b></td>";
    $html .= "\n\t\t\t\t<td align=\"right\"><b>".number_format($totalV,2,',','.')."</b></td>";
    $html .= "\n\t\t\t\t<td colspan=\"4\" align=\"right\"></td>";
    $html .= "\n\t\t\t\t<td align=\"right\"><b>".number_format($totalD,2,',','.')."</b></td>";
    $html .= "\n\t\t\t\t<td align=\"right\"><b>".number_format($totalA,2,',','.')."</b></td>";
    $html .= "\n\t\t\t</tr>";
    $html .= "\n\t\t</tfoot>";
    $html .= "\n\t</table>";
    $html .= "\n</body>";
    $html .= "\n</html>";

    echo $html;
}