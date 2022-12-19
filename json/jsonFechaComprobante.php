<?php

#########################NUEVO####################################
#25/04/2019 | DAVID P. | CREAR EL ARCHIVO PARA VALIDAR LA MODIFICAIÓN DE LA FECHA DEL COMPROBANTE
#############################################################################
require_once('../Conexion/conexion.php');
session_start();

$estruc = $_REQUEST['estruc'];
switch ($estruc) {
    case 1:
        $response = 0;
        $anno = $_SESSION['anno'];
        $tipComPal = $_POST['tipComPal'];
        $numero = $_POST['numero'];
        $nummenor = $numero - 1;
        $nummayor = $numero + 1;
        $fecha = $_POST['fecha'];
        //fecha
        $sqlfecha1 = "SELECT fecha
                    FROM gf_comprobante_pptal
                    WHERE parametrizacionanno = $anno
                    AND tipocomprobante = $tipComPal AND numero = $nummenor";
        $fechComp1 = $mysqli->query($sqlfecha1);
        $row1 = mysqli_fetch_row($fechComp1);
        $fechaPrev1 = $row1[0];
        if ($fechaPrev1 != 0) {
            $fecha_prev1 = new DateTime($fechaPrev1);
        }

        // fecha 2
        $sqlfecha2 = "SELECT fecha
	                FROM gf_comprobante_pptal
	                WHERE parametrizacionanno = $anno
	                AND tipocomprobante = $tipComPal AND numero = $nummayor";
        $fechComp2 = $mysqli->query($sqlfecha2);
        $row2 = mysqli_fetch_row($fechComp2);
        $fechaPrev2 = $row2[0];
        if ($fechaPrev2 != 0) {
            $fecha_prev2 = new DateTime($fechaPrev2);
        }

        //fecha formulario
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio . "-" . $mes . "-" . $dia;
        $fecha_ = new DateTime($fecha);
        //comparar fecha1
        if (!empty($fechaPrev1) && $fecha < $fechaPrev1) {
            $response = 1;
        } else {
            //comparar fecha2
            if (!empty($fechaPrev2) && $fecha > $fechaPrev2) {
                $response = 2;
            }
        }

        if ($response == 0) {
            $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
            $sumDias = $mysqli->query($querySum);
            if (mysqli_num_rows($sumDias) > 0) {
                $rowS = mysqli_fetch_row($sumDias);
                $sumarDias = $rowS[0];
            } else {
                $sumarDias = 30;
            }
            $fecha_->modify('+' . $sumarDias . ' day');
            $nuevaFecha = (string) $fecha_->format('Y-m-d');
            $fecha_div = explode("-", $nuevaFecha);
            $anio = $fecha_div[0];
            $mes = $fecha_div[1];
            $dia = $fecha_div[2];

            $response = $dia . "/" . $mes . "/" . $anio;
        } else {
            $response;
        }
        echo $response;
        break;
}
?>