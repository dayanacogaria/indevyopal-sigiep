<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=InformeDevolutivosPorDependencia.xls");

session_start();
require_once ("../Conexion/conexion.php");
if(!empty($_POST['txtFechaInicial']) && !empty($_POST['txtFechaFinal']) && !empty($_POST['sltProductoInicial']) &&
   !empty($_POST['sltProductoFinal'] && !empty($_POST['sltDepInicial']) && !empty($_POST['sltDepFinal']))){

    function convertirFecha($fecha){
        $fecha = explode("/", $fecha);
        return $fecha[2]."-".$fecha[1]."-".$fecha[0];
    }

    $usuario = $_SESSION['usuario'];
    $compa   = $compania = $_SESSION['compania'];

    $fechaInicial = $_POST['txtFechaInicial'];
    $fechaFinal   = $_POST['txtFechaFinal'];

    $fechaI       = convertirFecha($fechaInicial);
    $fechaF       = convertirFecha($fechaFinal);

    $productoIni  = $_POST['sltProductoInicial'];
    $productoFin  = $_POST['sltProductoFinal'];

    $depIni       = $_POST['sltDepInicial'];
    $depFin       = $_POST['sltDepFinal'];

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
    
    $totalt = 0;

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
    $html .= "<th colspan=\"13\" align=\"center\">".$nombreCompania."</th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th colspan=\"13\" align=\"center\">NIT: $nitcompania</th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th rowspan=\"2\">CODIGO</th>";
    $html .= "<th rowspan=\"2\">NOMBRE</th>";
    $html .= "<th rowspan=\"2\">ESPECIFICACIONES</th>";
    $html .= "<th rowspan=\"2\">SERIE</th>";
    $html .= "<th rowspan=\"2\">VALOR</th>";
    $html .= "<th colspan=\"3\">ENTRADA</th>";
    $html .= "<th colspan=\"3\">SALIDA</th>";
    $html .= "<th rowspan=\"2\">FECHA<br/>ADQ.</th>";
    $html .= "<th rowspan=\"2\">RESPONSABLE</th>";
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
    $sql_dep = "SELECT d.id_unico, d.sigla, UPPER(d.nombre), CONCAT_WS(' - ',UPPER(dp.sigla) , UPPER(dp.nombre)) 
    FROM gf_dependencia d
    LEFT JOIN gf_dependencia dp ON d.predecesor = dp.id_unico 
     WHERE (d.id_unico BETWEEN $depIni AND $depFin) AND d.compania = $compania";
    $res_dep = $mysqli->query($sql_dep);
    $totaltotal = 0;
    while($row_dep = mysqli_fetch_row($res_dep)){
        $totald = 0;
        $sql_e = "SELECT    pro.id_unico,
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
                  WHERE     (mov.dependencia   = $row_dep[0])
                  AND       (mov.fecha    BETWEEN '$fechaI'    AND '$fechaF')
                  AND       (pro.id_unico BETWEEN $productoIni AND $productoFin)
                  AND       (tpm.clase          = 3)
                  AND       (pln.tipoinventario = 2)
                  AND       (pln.compania       = $compania)
                  AND       (mov.compania       = $compania)";
        $res_e = $mysqli->query($sql_e);
        if($res_e->num_rows > 0){
            $html .= "<tr>";
            $html .= "<td colspan=\"2\" align=\"right\"><strong>DEPENDENCIA $row_dep[1]</strong></td>";
            $html .= "<td colspan=\"5\" align=\"left\"><strong>$row_dep[2]</strong></td>";
            $html .= "<td colspan=\"6\" align=\"left\"><strong>SEDE: $row_dep[3]</strong></td>";
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
                          AND       tpm.clase           = 2
                          AND       (pln.tipoinventario = 2)
                          AND       (pln.compania       = $compania)
                          AND       (mov.compania       = $compania)";
                $rs_s  = $mysqli->query($sql_s);
                $row_s = mysqli_fetch_row($rs_s);
                $desc  = str_replace("\n",' ',$row_e[3]);

                $mov_s   = "";
                $num_s   = "";
                $fecha_s = "";

                if(mysqli_num_rows($rs_s) > 0){
                    $mov_s   = $row_s[0];
                    $num_s   = $row_s[1];
                    $fecha_s = $row_s[2];
                }

                if(!empty($row_e[11])){
                    $fecha_a = $row_e[11];
                }else{
                    $fecha_a = $row_e[9];
                }

                $html .= "<tr>";
                $html .= "<td align=\"center\">$row_e[1]</td>";
                $html .= "<td align=\"left\">".utf8_decode($row_e[2])."</td>";
                $html .= "<td align=\"left\">".$desc."</td>";
                $html .= "<td align=\"right\">".$rw_[0]."</td>";
                $html .= "<td align=\"right\">".number_format($row_e[4], 2, '.', ',')."</td>";
                $html .= "<td align=\"center\">$mov_s</td>";
                $html .= "<td align=\"right\">$num_s</td>";
                $html .= "<td align=\"center\">$fecha_s</td>";
                $html .= "<td align=\"center\">$row_e[6]</td>";
                $html .= "<td align=\"right\">$row_e[7]</td>";
                $html .= "<td align=\"center\">$row_e[8]</td>";
                $html .= "<td align=\"center\">$fecha_s</td>";
                $html .= "<td align=\"left\">$row_e[9]</td>";
                $html .= "</tr>";
                $totald += $row_e[4];
            }
            $html .= "<tr>";
            $html .= '<td colspan="4"><strong><i>Total: '.$row_dep[1].' - '.$row_dep[2] .'</i></strong></td>';
            $html .='<td align="right"><strong><i>'.number_format($totald, 2, '.', ',').'</i></strong></td>';
            $html .= '<td colspan="7"></td>';
            $html .= "</tr>";
            $totaltotal +=$totald;

            $sqlesm = "SELECT GROUP_CONCAT(dm.id_unico)
                FROM  gf_movimiento_producto mp 
                LEFT JOIN gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
                LEFT JOIN gf_movimiento mov ON dm.movimiento = mov.id_unico 
                LEFT JOIN gf_tipo_movimiento tm ON mov.tipomovimiento = tm.id_unico 
                LEFT JOIN gf_producto pro ON mp.producto = pro.id_unico
                LEFT JOIN gf_plan_inventario pln ON dm.planmovimiento = pln.id_unico
                WHERE tm.clase = 2 
                AND mov.dependencia   = $row_dep[0] 
                AND pln.tipoinventario = 2 
                AND mov.fecha BETWEEN '$fechaI'    AND '$fechaF'
                AND pro.id_unico BETWEEN $productoIni AND $productoFin 
                AND pln.compania       = $compania
                AND mov.compania       = $compania
                AND dm.id_unico  IN (SELECT dma.detalleasociado FROM gf_detalle_movimiento dma)";
            $res_sql = $mysqli->query($sqlesm);   
            $row_sql = mysqli_fetch_row($res_sql);
            $ids     = $row_sql[0];
            if(!empty($row_sql[0])){ 
                $sql_ess = "SELECT  DISTINCT  pro.id_unico,
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
                      WHERE  tpm.clase = 2 
                        AND mov.dependencia   = $row_dep[0] 
                        AND pln.tipoinventario = 2 
                        AND mov.fecha BETWEEN '$fechaI'    AND '$fechaF'
                        AND pro.id_unico BETWEEN $productoIni AND $productoFin 
                        AND pln.compania       = $compania
                        AND mov.compania       = $compania 
                        AND dtm.id_unico NOT IN ($ids)";
                $res_ess = $mysqli->query($sql_ess);   
                if(mysqli_num_rows($res_ess)>0){
                    $html .= "<tr>";
                    $html .= "<td colspan=\"2\" align=\"right\"><strong>DEPENDENCIA $row_dep[1]</strong></td>";
                    $html .= "<td colspan=\"11\" align=\"left\"><strong>$row_dep[2]</strong></td>";
                    $html .= "</tr>";
                    while($row_e = mysqli_fetch_row($res_ess)){
                        $str   = "SELECT valor FROM gf_producto_especificacion WHERE producto = $row_e[0] AND fichainventario = 6";
                        $res   = $mysqli->query($str);
                        $rw_   = mysqli_fetch_row($res);
                        $desc  = str_replace("\n",' ',$row_e[3]);
                        $mov_s   = "";
                        $num_s   = "";
                        $fecha_s = "";

                        if(!empty($row_e[11])){
                            $fecha_a = $row_e[11];
                        }else{
                            $fecha_a = $row_e[9];
                        }

                        $html .= "<tr>";
                        $html .= "<td align=\"center\">$row_e[1]</td>";
                        $html .= "<td align=\"left\">".utf8_decode($row_e[2])."</td>";
                        $html .= "<td align=\"left\">".$desc."</td>";
                        $html .= "<td align=\"right\">".$rw_[0]."</td>";
                        $html .= "<td align=\"right\">".number_format($row_e[4], 2, '.', ',')."</td>";
                        $html .= "<td align=\"center\">$row_e[6]</td>";
                        $html .= "<td align=\"right\">$row_e[7]</td>";
                        $html .= "<td align=\"center\">$row_e[8]</td>";
                        $html .= "<td align=\"center\">$mov_s</td>";
                        $html .= "<td align=\"right\">$num_s</td>";
                        $html .= "<td align=\"center\">$fecha_s</td>";
                        $html .= "<td align=\"center\">$fecha_a</td>";
                        $html .= "<td align=\"left\">$row_e[9]</td>";
                        $html .= "</tr>";
                        $totald +=$row_e[4];
                    }
                    $html .= "<tr>";
                    $html .= '<td colspan="4"><strong><i>Total: '.$row_dep[1].' - '.$row_dep[2] .'</i></strong></td>';
                    $html .='<td align="right"><strong><i>'.number_format($totald, 2, '.', ',').'</i></strong></td>';
                    $html .= '<td colspan="7"></td>';
                    $html .= "</tr>";
                    $totaltotal +=$totald;
                }
            }

        } else {
            $sqlesm = "SELECT GROUP_CONCAT(dm.id_unico)
                FROM  gf_movimiento_producto mp 
                LEFT JOIN gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
                LEFT JOIN gf_movimiento mov ON dm.movimiento = mov.id_unico 
                LEFT JOIN gf_tipo_movimiento tm ON mov.tipomovimiento = tm.id_unico 
                LEFT JOIN gf_producto pro ON mp.producto = pro.id_unico
                LEFT JOIN gf_plan_inventario pln ON dm.planmovimiento = pln.id_unico
                WHERE tm.clase = 2 
                AND mov.dependencia   = $row_dep[0] 
                AND pln.tipoinventario = 2 
                AND mov.fecha BETWEEN '$fechaI'    AND '$fechaF'
                AND pro.id_unico BETWEEN $productoIni AND $productoFin 
                AND pln.compania       = $compania
                AND mov.compania       = $compania
                AND dm.id_unico  IN (SELECT dma.detalleasociado FROM gf_detalle_movimiento dma)";
            $res_sql = $mysqli->query($sqlesm);   
            $row_sql = mysqli_fetch_row($res_sql);
            if(!empty($row_sql[0])){ 
                $ids     = $row_sql[0];
                $sql_ess = "SELECT   DISTINCT pro.id_unico,
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
                      WHERE  tpm.clase = 2 
                        AND mov.dependencia   = $row_dep[0] 
                        AND pln.tipoinventario = 2 
                        AND mov.fecha BETWEEN '$fechaI'    AND '$fechaF'
                        AND pro.id_unico BETWEEN $productoIni AND $productoFin 
                        AND pln.compania       = $compania
                        AND mov.compania       = $compania
                        AND dtm.id_unico NOT IN ($ids)";
                $res_ess = $mysqli->query($sql_ess);   
                if(mysqli_num_rows($res_ess)>0){
                    $html .= "<tr>";
                    $html .= "<td colspan=\"2\" align=\"right\"><strong>DEPENDENCIA $row_dep[1]</strong></td>";
                    $html .= "<td colspan=\"11\" align=\"left\"><strong>$row_dep[2]</strong></td>";
                    $html .= "</tr>";
                    while($row_e = mysqli_fetch_row($res_ess)){
                        $str   = "SELECT valor FROM gf_producto_especificacion WHERE producto = $row_e[0] AND fichainventario = 6";
                        $res   = $mysqli->query($str);
                        $rw_   = mysqli_fetch_row($res);
                        $desc  = str_replace("\n",' ',$row_e[3]);
                        $mov_s   = "";
                        $num_s   = "";
                        $fecha_s = "";

                        if(!empty($row_e[11])){
                            $fecha_a = $row_e[11];
                        }else{
                            $fecha_a = $row_e[9];
                        }

                        $html .= "<tr>";
                        $html .= "<td align=\"center\">$row_e[1]</td>";
                        $html .= "<td align=\"left\">".utf8_decode($row_e[2])."</td>";
                        $html .= "<td align=\"left\">".$desc."</td>";
                        $html .= "<td align=\"right\">".$rw_[0]."</td>";
                        $html .= "<td align=\"right\">".number_format($row_e[4], 2, '.', ',')."</td>";
                        $html .= "<td align=\"center\">$row_e[6]</td>";
                        $html .= "<td align=\"right\">$row_e[7]</td>";
                        $html .= "<td align=\"center\">$row_e[8]</td>";
                        $html .= "<td align=\"center\">$mov_s</td>";
                        $html .= "<td align=\"right\">$num_s</td>";
                        $html .= "<td align=\"center\">$fecha_s</td>";
                        $html .= "<td align=\"center\">$fecha_a</td>";
                        $html .= "<td align=\"left\">$row_e[9]</td>";
                        $html .= "</tr>";
                        $totald +=$row_e[4];
                    }
                    $html .= "<tr>";
                    $html .= '<td colspan="4"><strong><i>Total: '.$row_dep[1].' - '.$row_dep[2] .'</i></strong></td>';
                    $html .='<td align="right"><strong><i>'.number_format($totald, 2, '.', ',').'</i></strong></td>';
                    $html .= '<td colspan="7"></td>';
                    $html .= "</tr>";
                    $totaltotal +=$totald;
                }    
            } 

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
}?>