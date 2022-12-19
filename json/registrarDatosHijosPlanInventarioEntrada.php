<?php
session_start();
require_once '../Conexion/conexion.php';
$cantidadV = $_POST['txtCantidadV'];
$movimiento = $_POST['txtMovimiento'];
$elemento = $_POST['txtElemento'];
$detalle_p = $_POST['txtDetalleP'];
$valorT = $_POST['txtVTotal'];
$porcIva = $_POST['txtPorceIva'];
$sumD = "";
for ($index = 1; $index <=$cantidadV; $index++) {
    $planI = "txtPlan".$index;
    $vPlanI = $_POST["$planI"];
    $cantidad = "txtCantidad".$index;
    $vCantidad = $_POST["$cantidad"];
    $valor = "txtValor".$index;
    $vValor=$_POST["$valor"];
    $iva = "txtValorIva".$index;
    $vIva = $_POST["$iva"];
    $sumD += $vValor;
    $sql = "insert into gf_detalle_movimiento(planmovimiento,cantidad,valor,iva,movimiento) values ('$vPlanI','$vCantidad','$vValor','$vIva','$movimiento')";
    $result = $mysqli->query($sql);
    if($cantidadV==$index){
        $sqlD = "SELECT valor FROM gf_detalle_movimiento WHERE  id_unico = $detalle_p";
        $resultD = $mysqli->query($sqlD);
        $rowD = mysqli_fetch_row($resultD);
        $valorTotal = $rowD[0] - $sumD;
        $valorTC = $valorTotal * $cantidadV;
        $cvalI = ($valorTC * $porcIva) / 100;
        $sqlR = "update gf_detalle_movimiento set valor='$valorTotal', iva = $cvalI where id_unico = $detalle_p";
        $res = $mysqli->query($sqlR);
        echo json_encode($result);
    }
}
?>

