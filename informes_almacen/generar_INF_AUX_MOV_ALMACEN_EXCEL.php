<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
require_once("../Conexion/conexion.php");
$compania = $_SESSION['compania'];
$elini          = $mysqli->real_escape_string(''.$_POST["sltEin"].'');
$elfin          = $mysqli->real_escape_string(''.$_POST["sltEfn"].'');
$movini         = $mysqli->real_escape_string(''.$_POST["sltmovi"].'');
$movfin         = $mysqli->real_escape_string(''.$_POST["sltmovf"].'');
$fechafin       = $mysqli->real_escape_string(''.$_POST["fechafin"].'');
$str_ini        = "SELECT    MIN(gmv.fecha), DATE_FORMAT(MIN(gmv.fecha), '%d/%m/%Y')
                   FROM      gf_movimiento AS gmv
                   LEFT JOIN gf_tipo_movimiento AS gtm ON gmv.tipomovimiento = gtm.id_unico
                   WHERE     gtm.clase    = 2
                   AND       gmv.compania = $compania";
$res_ini        = $mysqli->query($str_ini);
$row_ini        = mysqli_fetch_row($res_ini);
$fecha1         = $row_ini[0];
$fechaini       = $row_ini[1];
$mov1           = "SELECT nombre FROM gf_clase WHERE id_unico = $movini";
$movi1          = $mysqli->query($mov1);
$filaM1         = mysqli_fetch_row($movi1);
$mv1            = utf8_decode($filaM1[0]);

$mov2           = "SELECT nombre FROM gf_clase WHERE id_unico = $movfin";
$movi2          = $mysqli->query($mov2);
$filaM2         = mysqli_fetch_row($movi2);
$mv2            = utf8_decode($filaM2[0]);

$strei          = "SELECT codi FROM gf_plan_inventario WHERE id_unico = '$elini'";
$resi           = $mysqli->query($strei);
$rowei          = mysqli_fetch_row($resi);
$nomei          = $rowei[0];

$stref          = "SELECT codi FROM gf_plan_inventario WHERE id_unico = '$elfin'";
$resf           = $mysqli->query($stref);
$rowef          = mysqli_fetch_row($resf);
$nomef          = $rowef[0];

$fecha_div = explode("/", $fechafin);
$anio2     = $fecha_div[2];
$mes2      = $fecha_div[1];
$dia2      = $fecha_div[0];
$fecha2    = "$anio2-$mes2-$dia2";

$consulta = "SELECT         t.razonsocial          AS traz,
                            t.tipoidentificacion   AS tide,
                            ti.id_unico            AS tid,
                            ti.nombre              AS tnom,
                            t.numeroidentificacion AS tnum
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";
$cmp     = $mysqli->query($consulta);
$fila    = mysqli_fetch_array($cmp);
list($nomcomp, $tipodoc, $numdoc) = array(utf8_decode($fila[0]), utf8_decode($fila[3]), utf8_decode($fila[4]));

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=InformeMovimientoAlmacen.xls");
$html = "";
$html .= "<!doctype html>";
$html .= "<html lang=\"en\">";
$html .= "<head>";
$html .= "\n\t<meta charset=\"UTF-8\">";
$html .= "\n\t<meta name=\"viewport\" content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">";
$html .= "\n\t<meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">";
$html .= "\n\t<title>Informe auxiliar de Almacén</title>";
$html .= "</head>";
$html .= "<body>";
$html .= "\n\t<table style='width: 100%; border-collapse: collapse;'>";
$html .= "\n\t\t<thead>";
$html .= "\n\t\t\t<tr>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;' colspan='12'>$nomcomp<br/>NIT: $numdoc<br/>AUXILIAR DE MOVIMIENTO DE ALMACEN<br/>Entre Elementos $nomei y $nomef<br/>y Fechas $fechaini a $fechafin</th>";
$html .= "\n\t\t\t</tr>";
$html .= "\n\t\t\t<tr>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;' rowspan='2'>FECHA</th>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;' colspan='2'>COMPROBANTE</th>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;' rowspan='2'>TERCERO</th>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;' rowspan='2'>DESCRIPCIÓN</th>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;' rowspan='2'>VALOR UNITARIO</th>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;' colspan='3'>CANTIDAD</th>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;' colspan='3'>VALOR</th>";
$html .= "\n\t\t\t</tr>";
$html .= "\n\t\t\t<tr>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;'>NUMERO</th>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;'>TIPO</th>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;'>ENTRADA</th>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;'>SALIDA</th>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;'>SALDO</th>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;'>ENTRADA</th>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;'>SALIDA</th>";
$html .= "\n\t\t\t\t<th style='text-align: center; border: 1px solid #000;'>SALDO</th>";
$html .= "\n\t\t\t</tr>";
$html .= "\n\t\t</thead>";
$html .= "\n\t\t<tbody>";
list($codd, $totales, $valorA, $entrada, $salida, $totalent, $totalsal, $saldoT, $saldoTT, $ele) = array(0, 0, 0, "", "", 0, 0, 0, 0, 0);
$elementos = "SELECT DISTINCT dm.planmovimiento AS dmplan, CONCAT( pi.codi, ' - ', pi.nombre ) AS codele, pi.tipoinventario as tipoI
              FROM      gf_detalle_movimiento dm
              LEFT JOIN gf_movimiento m       ON dm.movimiento     = m.id_unico
              LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico
              LEFT JOIN gf_tercero t          ON pi.compania       = t.id_unico
              LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento  = tm.id_unico
              LEFT JOIN gf_clase cl           ON tm.clase          = cl.id_unico
              WHERE     (dm.valor IS NOT NULL)
              AND       (tm.clase BETWEEN $movini AND $movfin)
              AND       (pi.id_unico BETWEEN $elini AND $elfin)
              AND       (tm.clase BETWEEN $movini AND $movfin)
              AND       (m.fecha BETWEEN '$fecha1' AND '$fecha2')
              AND       (m.compania  = $compania)
              AND       (pi.compania = $compania)
              ORDER BY  pi.codi ASC";
$elemento = $mysqli->query($elementos);
while ($filaELS = mysqli_fetch_row($elemento)){
    list($entA, $cantEA, $totalEA, $entradaA, $salA, $cantSA, $totalSA, $totalC, $totalV, $planm, $codele) = array(0, 0, 0, 0, 0, 0, 0, 0, 0, $filaELS[0], $filaELS[1]);
    $html .= "\n\t\t\t<tr>";
    $html .= "\n\t\t\t\t<th colspan='2' style='text-align: left; border: 1px solid #000;'>ELEMENTO:</th>";
    $html .= "\n\t\t\t\t<th colspan='10' style='text-align: left; border: 1px solid #000;'>$codele</th>";
    $html .= "\n\t\t\t</tr>";
    switch ($filaELS[2]) {
        case 1:
        case 3:
            $str = "SELECT    DATE_FORMAT(mov.fecha, '%d/%m/%Y'), mov.numero, tpm.sigla,
                              IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                              (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE',
                              mov.descripcion, dtm.cantidad, ((dtm.valor) * dtm.cantidad), tpm.clase, dtm.hora, dtm.valor, dtm.valor
                    FROM      gf_detalle_movimiento as dtm
                    LEFT JOIN gf_movimiento         as mov ON dtm.movimiento     = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento    as tpm ON mov.tipomovimiento = tpm.id_unico
                    LEFT JOIN gf_tercero            as ter ON mov.tercero        = ter.id_unico
                    WHERE     (dtm.planmovimiento = $filaELS[0])
                    AND       (tpm.clase BETWEEN  $movini   AND $movfin)
                    AND       (mov.fecha BETWEEN  '$fecha1' AND '$fecha2')
                    AND       (mov.compania = $compania)
                    GROUP BY  dtm.id_unico
                    ORDER BY  mov.fecha, dtm.hora, tpm.clase";
            $res    = $mysqli->query($str);
            $xsaldo = 0;
            $xvalor = 0;
            while($row = mysqli_fetch_row($res)){
                $xcantS = 0;
                $xcantE = 0;
                $valorE = 0;
                $valorS = 0;
                switch ($row[7]) {
                    case 2:
                    case 5:
                        $xcantE  = $row[5];
                        $valorE  = $row[6];
                        $xsaldo += $xcantE;
                        $xvalor += $valorE;
                        break;

                    case 3:
                    case 7:
                        $xcantS  = $row[5];
                        $valorS  = $row[6];
                        $xsaldo -= $xcantS;
                        $xvalor -= $valorS;
                        break;
                }
                $html .= "\n\t\t\t<tr>";
                $html .= "\n\t\t\t\t<td>$row[0] $row[8]</td>";
                $html .= "\n\t\t\t\t<td>$row[1]</td>";
                $html .= "\n\t\t\t\t<td>$row[2]</td>";
                $html .= "\n\t\t\t\t<td>$row[3]</td>";
                $html .= "\n\t\t\t\t<td>".ucwords(mb_strtolower($row[4]))."</td>";
                $html .= "\n\t\t\t\t<td>$row[9]</td>";
                $html .= "\n\t\t\t\t<td style='text-align: right;'>$xcantE</td>";
                $html .= "\n\t\t\t\t<td style='text-align: right;'>$xcantS</td>";
                $html .= "\n\t\t\t\t<td style='text-align: right;'>$xsaldo</td>";
                $html .= "\n\t\t\t\t<td style='text-align: right;'>".number_format($valorE, 2, '.', ',')."</td>";
                $html .= "\n\t\t\t\t<td style='text-align: right;'>".number_format($valorS, 2, '.', ',')."</td>";
                $html .= "\n\t\t\t\t<td style='text-align: right;'>".number_format($xvalor, 2, '.', ',')."</td>";
                $html .= "\n\t\t\t</tr>";
            }
            break;
        case 2:
        case 4:
            $str = "SELECT   GROUP_CONCAT(mpr.producto), DATE_FORMAT(mov.fecha, '%d/%m/%Y') as fecha, mov.numero as num, tpm.sigla,
                              (IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR
                              CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                              (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos))) as nomter,
                              mov.descripcion as des, dtm.cantidad as cant, ((dtm.valor) * dtm.cantidad) as valor, dtm.hora, dtm.valor
                    FROM      gf_movimiento_producto as mpr
                    LEFT JOIN gf_detalle_movimiento  as dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_plan_inventario     as pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_movimiento          as mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     as tpm ON mov.tipomovimiento    = tpm.id_unico
                    LEFT JOIN gf_tercero             as ter ON mov.tercero           = ter.id_unico
                    WHERE     (dtm.planmovimiento = $filaELS[0])
                    AND       (tpm.clase          IN(2, 5))
                    AND       (mov.fecha BETWEEN '$fecha1' AND '$fecha2')
                    AND       (mov.compania = $compania)
                    AND       (pln.compania = $compania)
                    GROUP BY  dtm.id_unico
                    ORDER BY  tpm.clase, mov.numero, mov.fecha, mpr.producto, dtm.hora";
            $res = $mysqli->query($str);
            while($row = mysqli_fetch_row($res)){
                $xcsaldo  = 0;
                $xvsaldo  = 0;
                $xcantE   = $row[6];
                $xvalE    = $row[7];
                $xcsaldo += $xcantE;
                $xvsaldo += $xvalE;
                $html .= "\n\t\t\t<tr>";
                $html .= "\n\t\t\t\t<td>$row[1] $row[8]</td>";
                $html .= "\n\t\t\t\t<td>$row[2]</td>";
                $html .= "\n\t\t\t\t<td>$row[3]</td>";
                $html .= "\n\t\t\t\t<td>$row[4]</td>";
                $html .= "\n\t\t\t\t<td>".ucwords(mb_strtolower($row[5]))."</td>";
                $html .= "\n\t\t\t\t<td>$row[9]</td>";
                $html .= "\n\t\t\t\t<td style='text-align: right;'>$xcantE</td>";
                $html .= "\n\t\t\t\t<td style='text-align: right;'>0</td>";
                $html .= "\n\t\t\t\t<td style='text-align: right;'>$xcsaldo</td>";
                $html .= "\n\t\t\t\t<td style='text-align: right;'>".number_format($xvalE, 2, '.', ',')."</td>";
                $html .= "\n\t\t\t\t<td style='text-align: right;'>0</td>";
                $html .= "\n\t\t\t\t<td style='text-align: right;'>".number_format($xvsaldo, 2, '.', ',')."</td>";
                $html .= "\n\t\t\t</tr>";
                $xstr = "SELECT   DATE_FORMAT(mov.fecha, '%d/%m/%Y') as fecha, mov.numero as num, tpm.sigla,
                                  (IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR
                                  CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                  (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos))) as nomter,
                                  mov.descripcion as des, dtm.cantidad as cant, ((dtm.valor) * dtm.cantidad) as valor, dtm.hora, dtm.valor
                        FROM      gf_movimiento_producto as mpr
                        LEFT JOIN gf_detalle_movimiento  as dtm ON mpr.detallemovimiento = dtm.id_unico
                        LEFT JOIN gf_plan_inventario     as pln ON dtm.planmovimiento    = pln.id_unico
                        LEFT JOIN gf_movimiento          as mov ON dtm.movimiento        = mov.id_unico
                        LEFT JOIN gf_tipo_movimiento     as tpm ON mov.tipomovimiento    = tpm.id_unico
                        LEFT JOIN gf_tercero             as ter ON mov.tercero           = ter.id_unico
                        WHERE     (mpr.producto IN($row[0]))
                        AND       (tpm.clase    IN(3, 7))
                        AND       (pln.id_unico = $filaELS[0])
                        AND       (mov.fecha    BETWEEN '$fecha1' AND '$fecha2')
                        AND       (mov.compania = $compania)
                        AND       (pln.compania = $compania)
                        GROUP BY  dtm.id_unico
                        ORDER BY  tpm.clase, mov.numero, mov.fecha, dtm.hora";
                $xsres = $mysqli->query($xstr);
                $xssl    = $xcsaldo;
                $xvalsd  = $xvsaldo ;
                while($xsrow = mysqli_fetch_row($xsres)){
                    $xcantS  = $xsrow[5];
                    $xssl   -= $xcantS;
                    $xsaldo  = $xssl;
                    $xsval   = $xsrow[6];
                    $xvalsd -=  $xsval;
                    $html .= "\n\t\t\t<tr>";
                    $html .= "\n\t\t\t\t<td>$xsrow[0] $xsrow[7]</td>";
                    $html .= "\n\t\t\t\t<td>$xsrow[1]</td>";
                    $html .= "\n\t\t\t\t<td>$xsrow[2]</td>";
                    $html .= "\n\t\t\t\t<td>$xsrow[3]</td>";
                    $html .= "\n\t\t\t\t<td>".ucwords(mb_strtolower($xsrow[4]))."</td>";
                    $html .= "\n\t\t\t\t<td>$xsrow[8]</td>";
                    $html .= "\n\t\t\t\t<td style='text-align: right;'>0</td>";
                    $html .= "\n\t\t\t\t<td style='text-align: right;'>$xcantS</td>";
                    $html .= "\n\t\t\t\t<td style='text-align: right;'>$xssl</td>";
                    $html .= "\n\t\t\t\t<td style='text-align: right;'>0</td>";
                    $html .= "\n\t\t\t\t<td style='text-align: right;'>".number_format($xsval, 2, '.', ',')."</td>";
                    IF(empty($xvalsd)){
                        $vi = 0;
                    } else {
                         $vi =number_format($xvalsd, 2, '.', ',');
                    }
                    $html .= "\n\t\t\t\t<td style='text-align: right;'>".$vi."</td>";
                    $html .= "\n\t\t\t</tr>";
                }
            }
            break;
    }
}
$html .= "\n\t\t</tbody>";
$html .= "\n\t</table>";
$html .= "</body>";
$html .= "</html>";
echo $html;