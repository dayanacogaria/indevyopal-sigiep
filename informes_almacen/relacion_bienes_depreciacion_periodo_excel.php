<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=RelacionBienesDepreciacionPerido.xls");
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
        $id_unico = $row[0];
        $nombre   = $row[1];
        $codigo   = $row[2];
        $pros[]   = $id_unico.",".$nombre.",".$codigo;
    }

    $html = "";
    $html .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
    $html .= "\n<html xmlns= \"http://www.w3.org/1999/xhtml\">";
    $html .= "\n<head>";
    $html .= "\n\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
    $html .= "\n\t<title>Informe de Depreciacion</title>";
    $html .= "\n</head>";
    $html .= "\n<body>";
    $html .= "\n\t<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">";
    $html .= "\n\t\t<thead>";
    $html .= "\n\t\t\t<tr>";
    $html .= "\n\t\t\t\t<th colspan=\"7\" align=\"center\">".$nombreCompania."<br/>NIT: $nitCompania<br/>RELACIÓN DE BIENES DEPRECIADOS</th>";
    $html .= "\n\t\t\t</tr>";
    $html .= "\n\t\t\t<tr>";
    $html .= "\n\t\t\t\t<th>CODIGO<br/>PRODUCTO</th>";
    $html .= "\n\t\t\t\t<th>SERIE<br/>SISTEMA</th>";
    $html .= "\n\t\t\t\t<th>NOMBRE<br/>PRODUCTO</th>";
    $html .= "\n\t\t\t\t<th>FECHA<br/>ADQUISICIÓN</th>";
    $html .= "\n\t\t\t\t<th>VIDA<br/>UTIL</th>";
    $html .= "\n\t\t\t\t<th>COSTO<br/>HISTORICO</th>";
    $html .= "\n\t\t\t\t<th>DETERIRO<br/>ACUMULADO</th>";
    $html .= "\n\t\t\t</tr>";
    $html .= "\n\t\t</thead>";
    $html .= "\n\t\t<tbody>";
    $totalV = 0;
    $totalD = 0;
    for ($i = 0; $i < count($pros); $i++) {
        $objPro   = $dep->separarObjeto(",",$pros[$i]);
        $id_unico = $objPro[0]; $nombre = $objPro[1]; $codigo = $objPro[2]; $serie = $pro->obtnerCodigoProducto($id_unico);
        $placa    = $pro->obtnerCodigoProductoPlaca($id_unico);             $fecha = $dep->obtnerFechaAquisicion($id_unico);
        $vida     = (int) $pro->obtnerVidaUtil($id_unico); $valorPro = $pro->obtnerValorEntrada($id_unico);
        $valorD   = $dep->obtnerValorAcumuladoDrp($id_unico, $fechaInicial, $fechaFinal);
        if(empty($fecha)){
            $fecha    = $pro->obtnerFechaEntrada($id_unico);
        }
        $totalV += $valorPro; $totalD += $valorD;

        $html .= "\n\t\t\t<tr>";
        $html .= "\n\t\t\t\t<td align=\"right\">$codigo</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">$serie</td>";
        $html .= "\n\t\t\t\t<td align=\"left\">$nombre</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">$fecha</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">$vida</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">"."$".number_format($valorPro,2,',','.')."</td>";
        $html .= "\n\t\t\t\t<td align=\"right\">"."$".number_format($valorD,2,',','.')."</td>";
        $html .= "\n\t\t\t</tr>";
    }
    $html .= "\n\t\t</tbody>";
    $html .= "\n\t\t<tfoot>";
    $html .= "\n\t\t\t<tr>";
    $html .= "\n\t\t\t\t<td colspan=\"5\" align=\"right\">TOTAL</td>";
    $html .= "\n\t\t\t\t<td align=\"right\">"."$".number_format($totalV,2,',','.')."</td>";
    $html .= "\n\t\t\t\t<td align=\"right\">"."$".number_format($totalD,2,',','.')."</td>";
    $html .= "\n\t\t\t</tr>";
    $html .= "\n\t\t</tfoot>";
    $html .= "\n\t</table>";
    $html .= "\n</body>";
    $html .= "\n</html>";

    echo $html;
}