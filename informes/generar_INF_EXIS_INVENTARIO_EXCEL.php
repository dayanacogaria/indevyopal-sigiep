<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Existencias_Inventario.xls");
require_once("../Conexion/conexion.php");
@session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
list($proI, $proF, $fechaini, $fecha1, $hoy, $compania, $usuario)
    = array($_POST["sltEin"], $_POST["sltEfn"], $_POST["fechaini"], $_POST["fechaini"], date("d/m/Y"), $_SESSION['compania'], $_SESSION['usuario']);
$consulta = "SELECT         t.razonsocial as traz,
                            t.tipoidentificacion as tide,
                            ti.id_unico as tid,
                            UPPER(ti.nombre) as tnom,
                            t.numeroidentificacion tnum
            FROM            gf_tercero t
            LEFT JOIN       gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE           t.id_unico = $compania";
$cmp      = $mysqli->query($consulta);
$fila     = mysqli_fetch_row($cmp);
$ff       = explode("/", $fechaini);
$fecha    = "$ff[2]-$ff[1]-$ff[0]";
list($nomcomp, $tipodoc, $numdoc) = array(utf8_decode($fila[0]), utf8_decode($fila[3]), utf8_decode($fila[4]));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html>
    <head>
        <title>Reporte de Existencias de Inventario</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th colspan="5" bgcolor="skyblue">
                    <?php echo "$nomcomp<br/>$tipodoc : $numdoc<br>EXISTENCIAS DE INVENTARIO <br/> HASTA: $fecha1"; ?>
                </th>
            </tr>
            <tr>
                <th align="center">CÓDIGO</th>
                <th align="center">ELEMENTO</th>
                <th align="center">UNIDAD</th>
                <th align="center">CANTIDAD</th>
                <th align="center">VALOR</th>
            </tr>
        </thead>
        <tbody>
        <?php
        list($xCant, $xValorT, $html) = array(0, 0, "");
        $str = "SELECT    gpl.id_unico, gpl.codi, UPPER(gpl.nombre), UPPER(gum.nombre)
        FROM      gf_plan_inventario AS gpl
        LEFT JOIN gf_unidad_factor   AS gum ON gpl.unidad = gum.id_unico
        WHERE     (gpl.id_unico BETWEEN $proI AND $proF)
        AND       (gpl.compania = $compania)";
        $res = $mysqli->query($str);
        $dat = $res->fetch_all(MYSQLI_NUM);
        foreach ($dat as $row){
            list($xsaldo, $xvalor) = array(0, 0);
            $str_x = "SELECT    gtm.clase, gdm.cantidad, gdm.valor
              FROM      gf_detalle_movimiento AS gdm
              LEFT JOIN gf_movimiento         AS gmv ON gdm.movimiento     = gmv.id_unico
              LEFT JOIN gf_tipo_movimiento    AS gtm ON gmv.tipomovimiento = gtm.id_unico
              WHERE     (gdm.planmovimiento = $row[0])
              AND       (gtm.clase IN (2,3))
              AND       (gmv.fecha <= '$fecha')
              AND       (gmv.compania = $compania)
              ORDER BY  gmv.fecha, gdm.hora, gtm.clase";
            $res_x = $mysqli->query($str_x);
            $dat_x = $res_x->fetch_all(MYSQLI_NUM);
            foreach ($dat_x as $rowX){
                switch ($rowX[0]) {
                    case 2:
                        $xsaldo += $rowX[1];
                        $xvalor += ($rowX[2] * $rowX[1]);
                        break;

                    case 3:
                        $xsaldo -= $rowX[1];
                        $xvalor -= ($rowX[2] * $rowX[1]);
                        break;
                }
            }
            if($xsaldo > 0){
                $html .= "<tr>";
                $html .= "<td style='text-align: left;'>$row[1]</td>";
                $html .= "<td style='text-align: left;'>".mb_strtoupper($row[2])."</td>";
                $html .= "<td style='text-align: left;'>$row[3]</td>";
                $html .= "<td style='text-align: right;'>$xsaldo</td>";
                $html .= "<td style='text-align: right;'>".number_format($xvalor, 2 , ',', '.')."</td>";
                $html .= "</tr>";
                $xCant   += $xsaldo;
                $xValorT += $xvalor;
            }
        }
        echo $html;
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align: center;">TOTALES:</th>
                <th align="right"><?php echo $xCant?></th>
                <th align="right"><?php echo number_format($xValorT,2,'.',',')?></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>