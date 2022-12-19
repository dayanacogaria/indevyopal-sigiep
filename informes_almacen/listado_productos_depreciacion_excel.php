<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=InformeDetalladoDeDepreciacion.xls");

session_start();
require_once ("../Conexion/conexion.php");
require_once ('../modelAlmacen/producto.php');
require_once ('../modelAlmacen/depreciacion.php');
if(!empty($_REQUEST['txtPeridoF']) && !empty($_REQUEST['sltProductoInicial']) && !empty($_REQUEST['sltProductoFinal'])){
    $dep = new depreciacion();
    $pro = new producto();
    $compania = $_SESSION['compania'];
    $usuario  = $_SESSION['usuario'];
    $datosC   = $dep->tercero_informe($compania);

    $nombreCompania = $datosC[0];
    $nitCompania    = $datosC[1];
    $ruta           = $datosC[2];

    $DteF = $mysqli->real_escape_string($_REQUEST['txtPeridoF']);

    $proI = $mysqli->real_escape_string($_REQUEST['sltProductoInicial']);
    $proF = $mysqli->real_escape_string($_REQUEST['sltProductoFinal']);

    $ult = $dep->separarObjeto("/", $DteF);

    $fechaInicial = "1900-01-01";
    $fechaFinal   = $dep->ultimoDia($ult[1], $ult[0]);

    $res_p = $dep->encontrarDepreciacionProductos("1990-01-01", $fechaFinal, $proI, $proF);

    $pros = array();
    while($row = $res_p->fetch_row()){
        $id_unico = $row[0];
        $nombre   = $row[1];
        $pros[]   = $id_unico.",".$nombre;
    }

    $html = "";
    $html .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
    $html .= "<html xmlns= \"http://www.w3.org/1999/xhtml\">";
    $html .= "<head>";
    $html .= "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">";
    $html .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
    $html .= "<title>INFORME DETALLADO DE DEPRECIACION</title>";
    $html .= "<style>";
    $html .= ".text-center {";
    $html .= "text-align: center;";
    $html .= "}\n";
    $html .= "</style>";
    $html .= "</head>";
    $html .= "<body>";
    $html .= "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"1\">";
    $html .= "<thead>";
    $html .= "<tr>";
    $html .= "<th colspan=\"4\" class=\"text-center\">".$nombreCompania."<br/>$nitCompania<br/>INFORME DETALLADO DE DEPRECIACION</th>";
    $html .= "</tr>";
    $html .= "</thead>";
    $html .= "<tbody>";
    for ($i = 0; $i < count($pros); $i++) {
        $objPro   = $dep->separarObjeto(",", $pros[$i]);
        $id_unico = $objPro[0]; $nombre = $objPro[1];
        $serie    = $pro->obtnerCodigoProducto($id_unico);
        $valorP   = $pro->obtnerValorProducto($id_unico);
        $fechaA   = $dep->obtnerFechaAquisicion($id_unico);
        if(empty($fechaA)){
            $fechaA = $dep->fechaEntrada($id_unico);
        }
        $vidau = $pro->obtnerVidaUtil($id_unico);
        
        $html .= "<tr>";
        $html .= "<td colspan=\"2\" align=\"right\"><strong>SERIE: </strong></td>";
        $html .= "<td colspan=\"2\" align=\"left\">".$serie."</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td colspan=\"2\" align=\"right\"><strong>NOMBRE: </strong></td>";
        $html .= "<td colspan=\"2\" align=\"left\">".$nombre."</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td colspan=\"2\" align=\"right\"><strong>FECHA ADQUISICIÓN: </strong></td>";
        $html .= "<td colspan=\"2\" align=\"left\">".$fechaA."</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td colspan=\"2\" align=\"right\"><strong>VALOR: </strong></td>";
        $html .= "<td colspan=\"2\" align=\"left\">".number_format($valorP,2,',','.')."</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td colspan=\"2\" align=\"right\"><strong>VIDA ÚTIL: </strong></td>";
        $html .= "<td colspan=\"2\" align=\"left\">".$vidau."</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td colspan=\"2\" align=\"center\"><strong>"."FECHA DETERIORO"."</strong></td>";
        $html .= "<td colspan=\"2\" align=\"center\"><strong>"."VALOR DETERIORO"."</strong></td>";
        $html .= "</tr>";
        $dataPro = $dep->obtnerDepreciacionesProducto($id_unico);
        $totalD  = 0;
        for ($x = 0; $x < count($dataPro); $x++) {
            $datosPro = $dep->separarObjeto("/", $dataPro[$x]);
            $fecha    = new DateTime($datosPro[0]);
            $dias_dep = $datosPro[1];
            $valorDep = $datosPro[2];
            $totalD  += $valorDep;
            $fecha    = $fecha->format('d/m/Y');
            $html .= "<tr>";
            $html .= "<td colspan=\"2\" align=\"center\">".$fecha."</td>";
            $html .= "<td colspan=\"2\" align=\"right\">"."$".number_format($valorDep,2,',','.')."</td>";
            $html .= "</tr>";
        }
        $html .= "<tr>";
        $html .= "<td colspan=\"2\" align=\"right\">TOTAL</td>";
        $html .= "<td colspan=\"2\" align=\"right\">"."$".number_format($totalD,2,',','.')."</td>";
        $html .= "</tr>";
    }
    $html .= "</tbody>";
    $html .= "</table>";
    $html .= "</body>";
    $html .= "</html>";

    echo $html;
}