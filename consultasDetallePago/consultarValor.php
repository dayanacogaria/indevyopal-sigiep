<?php
/**************************************************************************
 * Actualizaciones
 **************************************************************************
 * Modificado por: Alexander Numpaque
 * Fecha de modificación: 06-07-2017
 * Se Cambio metodo de obtención del valor, por acumulación de valores por
 * detalle de cada factura y cuando el detalle de la factura esta relacionado
 * a detalle pago se suma y acumula aquel valor para restar.
 */
session_start();
require_once '../Conexion/conexion.php';
$factura = $_POST['factura']; $sumDF = 0; $sumDP = 0;
$sqlDF = "SELECT dtf.id_unico, dtf.valor_total_ajustado - dtf.ajuste_peso
        FROM     gp_detalle_factura dtf
        WHERE    dtf.factura = $factura";
$rsDF = $mysqli->query($sqlDF);
while($rowDF = mysqli_fetch_row($rsDF)) {
    $sqlDP = "SELECT valor + iva FROM gp_detalle_pago WHERE detalle_factura = $rowDF[0]";
    $rsDP = $mysqli->query($sqlDP);
    if(mysqli_num_rows($rsDP) > 0) {
        $rowDP = mysqli_fetch_row($rsDP);
        $sumDP += $rowDP[0];
    } else {
        $sumDP += 0;
    }
    $sumDF += $rowDF[1];
}
$valorD = $sumDF - $sumDP;
echo $valorD;