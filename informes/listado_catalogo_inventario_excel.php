<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Listado_Catalogo.xls");
require_once("../Conexion/conexion.php");
session_start();
$html = "";
$html .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
$html .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
$html .= "<head>";
$html .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
$html .= "<title>Listado de catalogo inventario</title>";
$html .= "</head>";
$compania = $_SESSION['compania'];
$sqlC = "SELECT     ter.id_unico,
                    ter.razonsocial,
                    UPPER(ti.nombre),
                    ter.numeroidentificacion,
                    dir.direccion,
                    tel.valor,
                    ter.ruta_logo
        FROM        gf_tercero ter
        LEFT JOIN   gf_tipo_identificacion ti   ON  ter.tipoidentificacion = ti.id_unico
        LEFT JOIN   gf_direccion dir            ON  dir.tercero = ter.id_unico
        LEFT JOIN   gf_telefono  tel            ON  tel.tercero = ter.id_unico
        WHERE       ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowC = mysqli_fetch_row($resultC);
$razonsocial = $rowC[1];
$nombreIdent = $rowC[2];
$numeroIdent = $rowC[3];
$direccinTer = $rowC[4];
$telefonoTer = $rowC[5];
$ruta_logo   = $rowC[6];

$usuario = $_SESSION['usuario'];

$sql = "SELECT    pln.id_unico,
                  pln.codi,
                  pln.nombre,
                  pln.tienemovimiento,
                  ti.nombre,
                  pre.codi,
                  uni.nombre,
                  tpa.nombre,
                  fch.descripcion,
                  padre.codi
        FROM      gf_plan_inventario pln
        LEFT JOIN gf_tipo_inventario ti  ON pln.tipoinventario = ti.id_unico
        LEFT JOIN gf_unidad_factor uni   ON pln.unidad     = uni.id_unico
        LEFT JOIN gf_plan_inventario pre ON pln.predecesor = pre.id_unico
        LEFT JOIN gf_tipo_activo tpa     ON pln.tipoactivo = tpa.id_unico
        LEFT JOIN gf_ficha fch           ON pln.ficha      = fch.id_unico
        LEFT JOIN gf_plan_inventario_asociado aso ON pln.id_unico = aso.plan_hijo
        LEFT JOIN gf_plan_inventario padre ON aso.plan_padre = padre.id_unico
        WHERE     pln.compania = $compania
        ORDER BY  pln.codi ASC";

$res = $mysqli->query($sql);

$html .= "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">";
$html .= "\n\t<thead>";
$html .= "\n\t\t<tr>";
$html .= "\n\t\t\t<td colspan=\"9\" align=\"center\"><strong>$razonsocial</strong><br/>$nombreIdent : $numeroIdent <br/>$direccinTer Tel: $telefonoTer </td>";
$html .= "\n\t\t</tr>";
$html .= "\n\t\t\t<td width=\"10%\"><strong>CODIGO</strong></td>";
$html .= "\n\t\t\t<td width=\"10%\"><strong>NOMBRE</strong></td>";
$html .= "\n\t\t\t<td width=\"10%\"><strong>MOVIMIENTO ?</strong></td>";
$html .= "\n\t\t\t<td width=\"10%\"><strong>TIPO MOVIMIENTO</strong></td>";
$html .= "\n\t\t\t<td width=\"10%\"><strong>PREDECESOR</strong></td>";
$html .= "\n\t\t\t<td width=\"10%\"><strong>UNIDAD FACTOR</strong></td>";
$html .= "\n\t\t\t<td width=\"10%\"><strong>TIPO ACTIVO</strong></td>";
$html .= "\n\t\t\t<td width=\"10%\"><strong>FICHA</strong></td>";
$html .= "\n\t\t\t<td width=\"10%\"><strong>PLAN INVENTARIO PADRE</strong></td>";
$html .= "\n\t\t<tr>";
$html .= "\n\t\t</tr>";
$html .= "\n\t</thead>";
$html .= "\n\t<tbody>";
while($row = mysqli_fetch_row($res)){
    $mov = "";
    switch ($row[3]) {
        case '1':
            $mov = "NO";
            break;

        case '2':
            $mov = "SI";
            break;
    }
    $html .= "\n\t\t<tr>";
    $html .= "\n\t\t\t<td style=\"text-align: right;\">".$row[1]."</td>";
    $html .= "\n\t\t\t<td style=\"text-align:left\">".ucwords(mb_strtolower($row[2]))."</td>";
    $html .= "\n\t\t\t<td style=\"text-align:center\">$mov</td>";
    $html .= "\n\t\t\t<td style=\"text-align:left\">".ucwords(mb_strtolower($row[4]))."</td>";
    $html .= "\n\t\t\t<td style=\"text-align: right;\">$row[5]</td>";
    $html .= "\n\t\t\t<td style=\"text-align: left;\">".ucwords(mb_strtolower($row[6]))."</td>";
    $html .= "\n\t\t\t<td style=\"text-align: left;\">".ucwords(mb_strtolower($row[7]))."</td>";
    $html .= "\n\t\t\t<td style=\"text-align: left;\">".ucwords(mb_strtolower($row[8]))."</td>";
    $html .= "\n\t\t\t<td style=\"text-align: right;\">$row[9]</td>";
    $html .= "\n\t\t</tr>";
}
$html .= "\n\t</tbody>";
$html .= "<table>";
echo $html;