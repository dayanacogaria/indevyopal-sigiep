<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=ListadoElementosUnidad.xls");
@session_start();
$compania = $_SESSION['compania'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Listado Plan Inventario</title>
</head>
<body>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="border: 1px solid #000; text-align: center;">CODIGO</th>
                <th style="border: 1px solid #000; text-align: center;">NOMBRE</th>
                <th style="border: 1px solid #000; text-align: center;">UNIDAD MINIMA</th>
                <th style="border: 1px solid #000; text-align: center;">ULTIMO VALOR COSTO</th>
                <th style="border: 1px solid #000; text-align: center;">UNIDAD EMPAQUE</th>
                <th style="border: 1px solid #000; text-align: center;">PRECIO UNIDAD EMPAQUE</th>
                <th style="border: 1px solid #000; text-align: center;">FACTOR CONVERSIÓN</th>
                <th style="border: 1px solid #000; text-align: center;">% UTILIDAD</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require '../Conexion/conexion.php';
            $str  = "SELECT    DISTINCT gpl.id_unico, gpl.codi, UPPER(gpl.nombre), UPPER(gun.nombre)
                     FROM      gp_concepto        AS gct
                     LEFT JOIN gf_plan_inventario AS gpl ON gct.plan_inventario = gpl.id_unico
                     LEFT JOIN gf_unidad_factor   AS gun ON gpl.unidad          = gun.id_unico
                     WHERE gpl.id_unico IS NOT NULL AND gpl.compania = $compania";
            $res  = $mysqli->query($str);
            $data = $res->fetch_all(MYSQLI_NUM);
            $html = "";
            foreach ($data as $row){
                $xxx   = 0;
                $str_x = "SELECT    MAX(gdm.valor)
                          FROM      gf_detalle_movimiento AS gdm
                          LEFT JOIN gf_movimiento         AS gmv ON gdm.movimiento     = gmv.id_unico
                          LEFT JOIN gf_tipo_movimiento    AS gtm ON gmv.tipomovimiento = gtm.id_unico
                          WHERE     gdm.planmovimiento = $row[0]
                          #AND       gtm.clase          = 3";
                $res_x = $mysqli->query($str_x);
                if($res_x->num_rows > 0){
                    $row_x = $res_x->fetch_row();
                    $xxx   = $row_x[0];
                }
                $html .= "<tr>";
                $html .= "<td style='border: 1px solid #000; text-align: right;'>$row[1]</td>";
                $html .= "<td style='border: 1px solid #000; text-align: left;'>$row[2]</td>";
                $html .= "<td style='border: 1px solid #000; text-align: left;'>$row[3]</td>";
                $html .= "<td style='border: 1px solid #000; text-align: right;'>".number_format($xxx)."</td>";
                $html .= "<td style='border: 1px solid #000; text-align: center; font-weight: 600;' colspan='4'>UNIDAD(ES) PRODUCTO</td>";
                $str_u  = "SELECT   DISTINCT gun.nombre, gtr.valor, gel.valor_conversion, gct.porcentajeI
                          FROM      gp_concepto_tarifa AS gct
                          LEFT JOIN gf_elemento_unidad AS gel ON gct.elemento_unidad = gel.id_unico
                          LEFT JOIN gf_unidad_factor   AS gun ON gel.unidad_empaque  = gun.id_unico
                          LEFT JOIN gp_tarifa          AS gtr ON gct.tarifa          = gtr.id_unico
                          LEFT JOIN gp_concepto        AS gcn ON gct.concepto        = gcn.id_unico
                          LEFT JOIN gf_plan_inventario AS gpl ON gcn.plan_inventario = gpl.id_unico
                          WHERE     gpl.id_unico = $row[0]";
                $res_u  = $mysqli->query($str_u);
                $data_u = $res_u->fetch_all(MYSQLI_NUM);
                foreach ($data_u as $row_u){
                    $html .= "<tr>";
                    $html .= "<td style='border: 1px solid #000; text-align: right;' colspan='4'></td>";
                    $html .= "<td style='border: 1px solid #000; text-align: left;'>$row_u[0]</td>";
                    $html .= "<td style='border: 1px solid #000; text-align: right;'>$row_u[1]</td>";
                    $html .= "<td style='border: 1px solid #000; text-align: right;'>$row_u[2]</td>";
                    $html .= "<td style='border: 1px solid #000; text-align: right;'>$row_u[3]</td>";
                    $html .= "</tr>";
                }
                $html .= "</tr>";
            }
            echo $html;
            ?>
        </tbody>
    </table>
</body>
</html>
