<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=InformeDevolutivosPorResponsable.xls");

session_start();
require_once ("../Conexion/conexion.php");
if(!empty($_POST['txtFechaInicial']) && !empty($_POST['txtFechaFinal']) && !empty($_POST['sltProductoInicial']) &&
   !empty($_POST['sltProductoFinal'] && !empty($_POST['sltResIni']) && !empty($_POST['sltResFinal']))){

    function convertirFecha($fecha){
        $fecha = explode("/", $fecha);
        return $fecha[2]."-".$fecha[1]."-".$fecha[0];
    }

    function obtenerDatosProducto($producto, $compania){
        require ('../Conexion/conexion.php');
        $sql = "SELECT     pln.nombre  AS NOM_PLAN,
                           UPPER(pes.valor)   AS SERIE
                FROM       gf_producto pr
                LEFT JOIN  gf_movimiento_producto     mpr ON mpr.producto          = pr.id_unico
                LEFT JOIN  gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                LEFT JOIN  gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                LEFT JOIN  gf_producto_especificacion pes ON pes.producto          = pr.id_unico
                LEFT JOIN  gf_ficha_inventario        fic ON pes.fichainventario   = fic.id_unico
                WHERE      fic.elementoficha   = 6
                AND        pr.id_unico         = $producto
                AND        pln.compania        = $compania
                ORDER BY   pr.id_unico DESC";
        $res = $mysqli->query($sql);
        $row = mysqli_fetch_row($res);
        return $row;
        $mysqli->close();
    }

    function obtenerResponsable($id_unico, $compania){
        require ('../Conexion/conexion.php');
        $sql = "SELECT    IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL
                          OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                          (ter.razonsocial),
                          CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE',
                          CONCAT_WS(' - ',tip.nombre, ter.numeroidentificacion, ter.digitoverficacion) AS IDENT
                FROM      gf_dependencia_responsable drs
                LEFT JOIN gf_tercero                 ter ON drs.responsable        = ter.id_unico
                LEFT JOIN gf_tipo_identificacion     tip ON ter.tipoidentificacion = tip.id_unico
                LEFT JOIN gf_dependencia             gdp ON drs.dependencia        = gdp.id_unico
                WHERE     ter.compania        = $compania
                AND       gdp.compania        = $compania
                WHERE     ter.id_unico = $id_unico";
        $res = $mysqli->query($sql);
        $row = mysqli_fetch_row($res);
        return $row;
        $mysqli->close();
    }

    $usuario = $_SESSION['usuario'];
    $compa   = $compania = $_SESSION['compania'];

    $fechaInicial = $_POST['txtFechaInicial'];
    $fechaFinal   = $_POST['txtFechaFinal'];

    $fechaI       = convertirFecha($fechaInicial);
    $fechaF       = convertirFecha($fechaFinal);

    $productoIni  = $_POST['sltProductoInicial'];
    $productoFin  = $_POST['sltProductoFinal'];

    $proI         = obtenerDatosProducto($productoIni, $compania);
    $proF         = obtenerDatosProducto($productoFin, $compania);

    $productoI    = $proI[1]." - ".$proI[0];
    $productoF    = $proF[1]." - ".$proF[0];

    $responInicial= $_POST['sltResIni'];
    $responFinal  = $_POST['sltResFinal'];

    $respondeI    = obtenerResponsable($responInicial, $compania);
    $respondeF    = obtenerResponsable($responFinal, $compania);

    $comp = "SELECT UPPER(t.razonsocial), t.numeroidentificacion, t.digitoverficacion, t.ruta_logo
             FROM gf_tercero t WHERE id_unico = $compa";
    $comp = $mysqli->query($comp);
    $comp = mysqli_fetch_row($comp);
    $nombreCompania = $comp[0];

    if(empty($comp[2])) {
        $nitcompania = $comp[1];
    } else {
        $nitcompania = $comp[1].' - '.$comp[2];
    }

    $html = "";
    $html .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
    $html .= "<html xmlns= \"http://www.w3.org/1999/xhtml\">";
    $html .= "<head>";
    $html .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
    $html .= "<title>Informe de Propiedad Planta y Equipo de Almacen por Dependencia</title>";
    $html .= "</head>";
    $html .= "<body>";
    $html .= "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">";
    $html .= "<thead>";
    $html .= "<tr>";
    $html .= "<th colspan=\"12\" align=\"center\">".$nombreCompania."</th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th colspan=\"12\" align=\"center\">NIT: $nitcompania</th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th rowspan=\"2\">CODIGO</th>";
    $html .= "<th rowspan=\"2\">NOMBRE</th>";
    $html .= "<th rowspan=\"2\">ESPECIFICACIONES</th>";
    $html .= "<th rowspan=\"2\">SERIE</th>";
    $html .= "<th rowspan=\"2\">VALOR</th>";
    $html .= "<th colspan=\"3\">ENTRADA</th>";
    $html .= "<th colspan=\"3\">SALIDA</th>";
    $html .= "<th rowspan=\"2\">FECHA<br/>ADQ.</th>";;
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th>MOV</th>";
    $html .= "<th>NUMERO</th>";
    $html .= "<th>FECHA</th>";
    $html .= "<th>MOV</th>";
    $html .= "<th>NUMERO</th>";
    $html .= "<th>FECHA</th>";
    $html .= "</tr>";
    $html .= "</thead>";
    $html .= "<tbody>";
    $sql_res = "SELECT    ter.id_unico,
                          IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL
                          OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                          ter.razonsocial,
                          CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)),
                          UPPER(tip.nombre),
                          CONCAT_WS(' - ', ter.numeroidentificacion, ter.digitoverficacion)
                FROM      gf_tercero ter
                LEFT JOIN gf_tipo_identificacion tip ON ter.tipoidentificacion = tip.id_unico
                WHERE     ter.id_unico BETWEEN $responInicial AND $responFinal
                ANd       ter.compania = $compania";
    $res_ter = $mysqli->query($sql_res);
    $totaltotal =0;
    while($row_ter = mysqli_fetch_row($res_ter)){
        $totalt =0;
        $sql_e = "SELECT
                            pro.id_unico,
                            pln.codi,
                            pln.nombre,
                            pro.descripcion,
                            pro.valor,
                            tpm.clase,
                            tpm.sigla,
                            mov.numero,
                            DATE_FORMAT(mov.fecha,'%d/%m/%Y'),
                            IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL
                            OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                               ter.razonsocial,
                               CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)) AS NOMBRE,
                            CONCAT_WS(' - ',tip.nombre, ter.numeroidentificacion, ter.digitoverficacion),
                            DATE_FORMAT(pro.fecha_adquisicion, '%d/%m/%Y')
                  FROM      gf_movimiento_producto     mpr
                  LEFT JOIN gf_producto                pro ON mpr.producto           = pro.id_unico
                  LEFT JOIN gf_detalle_movimiento      dtm ON mpr.detallemovimiento  = dtm.id_unico
                  LEFT JOIN gf_movimiento              mov ON dtm.movimiento         = mov.id_unico
                  LEFT JOIN gf_tipo_movimiento         tpm ON mov.tipomovimiento     = tpm.id_unico
                  LEFT JOIN gf_plan_inventario         pln ON dtm.planmovimiento     = pln.id_unico
                  LEFT JOIN gf_tercero                 ter ON mov.tercero            = ter.id_unico
                  LEFT JOIN gf_tipo_identificacion     tip ON ter.tipoidentificacion = tip.id_unico
                  WHERE     (mov.tercero        = $row_ter[0])
                  AND       (mov.fecha    BETWEEN '$fechaI'    AND '$fechaF')
                  AND       (pro.id_unico BETWEEN $productoIni AND $productoFin)
                  AND       (tpm.clase          = 2)
                  AND       (pln.tipoinventario = 2)
                  AND       (pln.compania       = $compania)
                  AND       (mov.compania       = $compania)";
        $res_e = $mysqli->query($sql_e);
        if($res_e->num_rows > 0){

            $html .= "<tr>";
            $html .= "<td colspan=\"12\" align=\"left\"><strong>$row_ter[1] ($row_ter[2] # $row_ter[3])</strong></td>";
            $html .= "</tr>";
            while($row_e = mysqli_fetch_row($res_e)){
                $str   = "SELECT valor FROM gf_producto_especificacion WHERE producto = $row_e[0] AND fichainventario = 6";
                $res   = $mysqli->query($str);
                $rw_   = mysqli_fetch_row($res);

                $sql_s = "SELECT    tpm.sigla,
                                    mov.numero,
                                    DATE_FORMAT(mov.fecha,'%d/%m/%Y'),
                                    IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL
                                    OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                    ter.razonsocial,
                                    CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)) AS NOMBRE,
                                    CONCAT_WS(' - ',tip.nombre, ter.numeroidentificacion, ter.digitoverficacion)
                          FROM      gf_movimiento_producto mpr
                          LEFT JOIN gf_producto            pro ON mpr.producto           = pro.id_unico
                          LEFT JOIN gf_detalle_movimiento  dtm ON mpr.detallemovimiento  = dtm.id_unico
                          LEFT JOIN gf_movimiento          mov ON dtm.movimiento         = mov.id_unico
                          LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento     = tpm.id_unico
                          LEFT JOIN gf_tercero             ter ON mov.tercero            = ter.id_unico
                          LEFT JOIN gf_tipo_identificacion tip ON ter.tipoidentificacion = tip.id_unico
                          LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento     = pln.id_unico
                          WHERE     pro.id_unico        = $row_e[0]
                          AND       tpm.clase           = 3
                          AND       (pln.tipoinventario = 2)
                          AND       (pln.compania       = $compania)
                          AND       (mov.compania       = $compania)";

                $res_s  = $mysqli->query($sql_s);
                $row_s = mysqli_fetch_row($res_s);

                $mov_s   = "";
                $num_s   = "";
                $fecha_s = "";

                if($res_e->num_rows > 0){
                    $mov_s   = $row_s[0];
                    $num_s   = $row_s[1];
                    $fecha_s = $row_s[2];
                }

                if(!empty($row_e[11])){
                    $fecha_a = $row_e[11];
                }else{
                    $fecha_a = $row_e[8];
                }

                $html .= "<tr>";
                $html .= "<td align=\"center\">$row_e[1]</td>";
                $html .= "<td align=\"left\">".$row_e[2]."</td>";
                $html .= "<td align=\"left\">".$row_e[3]."</td>";
                $html .= "<td align=\"right\">".$rw_[0]."</td>";
                $html .= "<td align=\"right\">".number_format($row_e[4], 2, '.', ',')."</td>";
                $html .= "<td align=\"center\">$row_e[6]</td>";
                $html .= "<td align=\"right\">$row_e[7]</td>";
                $html .= "<td align=\"center\">$row_e[8]</td>";
                $html .= "<td align=\"center\">$mov_s</td>";
                $html .= "<td align=\"right\">$num_s</td>";
                $html .= "<td align=\"center\">$fecha_s</td>";
                $html .= "<td align=\"center\">$fecha_a</td>";
                $html .= "</tr>";
                $totalt +=$row_e[4];
            }
            $html .= "<tr>";
            $html .= '<td colspan="4"><strong><i>Total: '.$row_ter[1].' - '.$row_ter[3] .'</i></strong></td>';
            $html .='<td align="right"><strong><i>'.number_format($totalt, 2, '.', ',').'</i></strong></td>';
            $html .= '<td colspan="7"></td>';
            $html .= "</tr>";
            $totaltotal +=$totalt;

        }

    }
    $html .= "<tr>";
    $html .= '<td colspan="4"><strong><br/>TOTALES<br/>&nbsp;</i></strong></td>';
    $html .='<td align="right"><strong><i>'.number_format($totaltotal, 2, '.', ',').'</i></strong></td>';
    $html .= '<td colspan="7"></td>';
    $html .= "</tr>";

    $html .= "</tbody>";
    $html .= "</table>";
    $html .= "</body>";
    $html .= "</html>";

    echo $html;
}