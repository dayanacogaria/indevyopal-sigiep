<?php
require_once '../Conexion/conexion.php';
session_start();
if(!empty($_REQUEST['x'])){
    switch ($_REQUEST['x']) {
        case '1':
            if(!empty($_REQUEST['concepto'])){
                $html = "";
                $html .= "<option value=\"\">Rubros</option>";
                $concepto = $_REQUEST['concepto'];
                $sql_r = "SELECT rb.id_unico, CONCAT_WS(' ',rb.codi_presupuesto, rb.nombre) FROM gp_concepto gcn
                LEFT JOIN gf_concepto fcn         ON gcn.concepto_financiero = fcn.id_unico
                LEFT JOIN gf_concepto_rubro rubro ON fcn.id_unico = rubro.concepto
                LEFT JOIN gf_rubro_pptal rb       ON rubro.rubro = rb.id_unico
                WHERE gcn.id_unico = $concepto";
                $res_r = $mysqli->query($sql_r);
                while($row_r = mysqli_fetch_row($res_r)){
                    $html .= "<option value=\"$row_r[0]\">".ucwords(strtolower($row_r[1]))."</option>";
                }
                echo $html;
            }
            break;
    }
}