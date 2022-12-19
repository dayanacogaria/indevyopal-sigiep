<?php

/**
 * Created by PhpStorm.
 * User: SERVIDOR
 * Date: 12/06/2018
 * Time: 9:24
 */
require './clases/movimiento.php';
require './clases/tarifa.php';

class informesController {

    private $mov;

    public function __construct() {
        $this->mov = new movimiento();
    }

    public function index() {
        require_once './vistas/movimiento/listado.informe.php';
    }

    public function informeForm() {
        require_once './vistas/movimiento/listado.habitaciones.php';
    }

    public function formatearFecha($separador, $fecha) {
        $ff = explode($separador, $fecha);
        return "$ff[2]-$ff[1]-$ff[0]";
    }

//    public function formatearFecha($fecha) {
//        $ff = explode("/", $fecha);
//        return "$ff[2]-$ff[1]-$ff[0]";
//    }

    public function formatearFechaHora($fecha) {
        $ff = explode("/", $fecha);
        $xx = explode(" ", $ff[2]);
        $fecha = trim($xx[0]) . "-$ff[1]-$ff[0] $xx[1]:00";
        return $fecha;
    }

    public function calculoEntreFechas($fechaI, $fechaF) {
        date_default_timezone_set('America/Bogota');
        $fechaS = new DateTime(informesController::formatearFechaHora($fechaI));
        $fechaE = new DateTime(informesController::formatearFechaHora($fechaF));
        $diff = $fechaS->diff($fechaE);
        return $diff->days;
    }

    public function listadoLlegadaPdf() {
        ini_set('max_execution_time', 0);
        session_start();
        ob_start();
        $fechaI = informesController::formatearFecha("/", $_REQUEST['txtFechaI']);
        $fechaF = informesController::formatearFecha("/", $_REQUEST['txtFechaF']);
        $ente = $this->mov->obtenerCompania($_SESSION['compania']);
        list($nombreCompania, $nitcompania, $ruta) = array($ente[0], "$ente[1] - $ente[2]", $ente[3]);
        $data = $this->mov->listarDetallesPersonaMov(2, $fechaI, $fechaF);
        require ('./fpdf/fpdf.php');
        require_once './informes/ingresos/ingreso.listado.php';
    }

    public function listadoLlegadaExcel() {
        ini_set('max_execution_time', 0);
        session_start();
        $fechaI = informesController::formatearFecha("/", $_REQUEST['txtFechaI']);
        $fechaF = informesController::formatearFecha("/", $_REQUEST['txtFechaF']);
        $ente = $this->mov->obtenerCompania($_SESSION['compania']);
        $data = $this->mov->listarDetallesPersonaMov(2, $fechaI, $fechaF);
        list($nombreCompania, $nitcompania, $ruta) = array($ente[0], "$ente[1] - $ente[2]", $ente[3]);
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=ReporteIngreso-" . $fechaI . ".xls");
        $html = "";
        $html .= "<!doctype html>";
        $html .= "\n<html lang=\"en\">";
        $html .= "\n<head>";
        $html .= "\n\t<meta charset=\"UTF-8\">";
        $html .= "\n\t<meta name=\"viewport\" content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">";
        $html .= "\n\t<meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">";
        $html .= "\n\t<title>Reporte de Llegada</title>";
        $html .= "\n</head>";
        $html .= "\n<body>";
        $html .= "\n\t<table style='width: 100%; border-collapse: collapse;'>";
        $html .= "\n\t\t<thead>";
        $html .= "\n\t\t\t<tr>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000;' colspan='10'>$nombreCompania <br/>NIT : $nitcompania<br/>REPORTE DE INGRESOS DEL " . $_REQUEST['txtFechaI'] . " AL " . $_REQUEST['txtFechaF'] ."</th>";
        $html .= "\n\t\t\t</tr>";
        $html .= "\n\t\t\t<tr>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>NÚMERO DE <br/> INGRESO</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>TIPO DE <br/> HABITACIÓN</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>HABITACIÓN</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>NOMBRE DE<br/> HÚESPED</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>FECHA DE <br/> LLEGADA</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>FECHA DE<br/> SALIDA</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>CANTIDAD DE <br/> NOCHES</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>NÚMERO DE <br/> RESERVA</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>TIPO DE <br/> RESERVA</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>CONCEPTO <br/> CONSUMIBLE</th>";
        $html .= "\n\t\t\t</tr>";
        $html .= "\n\t\t</thead>";
        $html .= "\n\t\t<tbody>";
        foreach ($data as $row) {
            $noche = informesController::calculoEntreFechas($row[2], $row[3]);
            if ($noche == 0) {
                $noche = 1;
            }
            $movimiento = $row[6];
            $detallessph = $this->mov->obtenerDetallesespacios($movimiento);
            
            while ($row2 = mysqli_fetch_row($detallessph)) {
                $html .= "\n\t\t\t<tr>";
                $idter = $row2[0];
                $terceros = $this->mov->obtenerDetallestercero($idter);
                $countper = mysqli_num_rows($terceros);
                if ($countper > 0) {
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[0]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row2[1]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row2[2]</td>";
                    $html .= "<td style='border: solid 1px #000; text-align: left;'>";
                    while ($row3 = mysqli_fetch_row($terceros)) {
                        $html .= $row3[0] . "<br />";
                    }
                    $html .= "</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[2]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[3]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$noche</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[7]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'></td>";
                } else {
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[0]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row2[1]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row2[2]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: left; vertical-align: middle;'>$row[1]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[2]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[3]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$noche</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[7]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'></td>";
                }

                $consumible = '';
                $sqlconcp   = $this->mov->obtenerConsumibles($movimiento);
                $consumible .= $sqlconcp[0][0];
                $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>".$consumible."</td>";
                $html .= "\n\t\t\t</tr>";
            }
        }
        $html .= "\n\t\t</tbody"; 
        $html .= "\n\t</table>";
        $html .= "\n</body>";
        $html .= "\n</html>";
        echo $html;
    }

    public function listadoReservasPdf() {
        ini_set('max_execution_time', 0);
        session_start();
        ob_start();
        $fechaI = informesController::formatearFecha("/", $_REQUEST['txtFechaI']);
        $fechaF = informesController::formatearFecha("/", $_REQUEST['txtFechaF']);
        $ente = $this->mov->obtenerCompania($_SESSION['compania']);
        list($nombreCompania, $nitcompania, $ruta) = array($ente[0], "$ente[1] - $ente[2]", $ente[3]);
        $data = $this->mov->listarDetallesPersonaMov(1, $fechaI, $fechaF);
        require ('./fpdf/fpdf.php');
        require_once './informes/reservas/reserva.listado.php';
    }

    public function listadoReservasExcel() {
        ini_set('max_execution_time', 0);
        session_start();
        $fechaI = informesController::formatearFecha("/", $_REQUEST['txtFechaI']);
        $fechaF = informesController::formatearFecha("/", $_REQUEST['txtFechaF']);
        $ente = $this->mov->obtenerCompania($_SESSION['compania']);
        $data = $this->mov->listarDetallesPersonaMov(1, $fechaI, $fechaF);
        list($nombreCompania, $nitcompania, $ruta) = array($ente[0], "$ente[1] - $ente[2]", $ente[3]);
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=ReporteReservas-" . $fechaI . ".xls");
        $html = "";
        $html .= "<!doctype html>";
        $html .= "\n<html lang=\"en\">";
        $html .= "\n<head>";
        $html .= "\n\t<meta charset=\"UTF-8\">";
        $html .= "\n\t<meta name=\"viewport\" content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">";
        $html .= "\n\t<meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">";
        $html .= "\n\t<title>Reporte de Llegada</title>";
        $html .= "\n</head>";
        $html .= "\n<body>";
        $html .= "\n\t<table style='width: 100%; border-collapse: collapse;'>";
        $html .= "\n\t\t<thead>";
        $html .= "\n\t\t\t<tr>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000;' colspan='9'>$nombreCompania <br/>NIT : $nitcompania<br/>REPORTE DE RESERVA DEL " . $_REQUEST['txtFechaI'] . " AL " . $_REQUEST['txtFechaF'] . "</th>";
        $html .= "\n\t\t\t</tr>";
        $html .= "\n\t\t\t<tr>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>NÚMERO DE <br/> RESERVA</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>TIPO DE <br/> HABITACIÓN</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>HABITACIÓN</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>NOMBRE DE<br/> HÚESPED</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>FECHA DE <br/> LLEGADA</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>FECHA DE<br/> SALIDA</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>CANTIDAD DE <br/> NOCHES</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>NÚMERO DE <br/> INGRESO</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>TIPO DE <br/> INGRESO</th>";
        $html .= "\n\t\t\t</tr>";
        $html .= "\n\t\t</thead>";
        $html .= "\n\t\t<tbody>";
        foreach ($data as $row) {
            $noche = informesController::calculoEntreFechas($row[2], $row[3]);
            if ($noche == 0) {
                $noche = 1;
            }
            $movimiento = $row[6];
            $detalleaso = $this->mov->obtenerMovasociado($movimiento);
            $asociado = $detalleaso[0];            
            $detallessph = $this->mov->obtenerDetallesespacios($movimiento);
            while ($row2 = mysqli_fetch_row($detallessph)) {
                $html .= "\n\t\t\t<tr>";
                $idter = $row2[0];
                $terceros = $this->mov->obtenerDetallestercero($idter);
                $countper = mysqli_num_rows($terceros);
                if ($countper > 0) {
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[0]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row2[1]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row2[2]</td>";
                    $html .= "<td style='border: solid 1px #000; text-align: left;'>";
                    while ($row3 = mysqli_fetch_row($terceros)) {
                        $html .= $row3[0] . "<br />";
                    }
                    $html .= "</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[2]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[3]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$noche</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[7]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'></td>";
                } else {
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[0]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row2[1]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row2[2]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: left; vertical-align: middle;'>$row[1]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[2]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[3]</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$noche</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$asociado</td>";
                    $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'></td>";
                }
                $html .= "\n\t\t\t</tr>";
            }
        }
        $html .= "\n\t\t</tbody";
        $html .= "\n\t</table>";
        $html .= "\n</body>";
        $html .= "\n</html>";
        echo $html;
    }

    public function RelacionPdf() {
        ini_set('max_execution_time', 0);
        session_start();
        ob_start();
        $fechaI = informesController::formatearFecha("/", $_REQUEST['txtFechaI']);
        $fechaF = informesController::formatearFecha("/", $_REQUEST['txtFechaF']);
        $ente = $this->mov->obtenerCompania($_SESSION['compania']);
        list($nombreCompania, $nitcompania, $ruta) = array($ente[0], "$ente[1] - $ente[2]", $ente[3]);
        $data = $this->mov->obtenerMovFecha(2, $fechaI, $fechaF);
        require ('./fpdf/fpdf.php');
        require_once './informes/ingresos/factura.listado.php';
    }

    public function RelacionExcel() {
        ini_set('max_execution_time', 0);
        session_start();
        $fechaI = informesController::formatearFecha("/", $_REQUEST['txtFechaI']);
        $fechaF = informesController::formatearFecha("/", $_REQUEST['txtFechaF']);
        $ente = $this->mov->obtenerCompania($_SESSION['compania']);
        $data = $this->mov->obtenerMovFecha(2, $fechaI, $fechaF);
        list($nombreCompania, $nitcompania, $ruta) = array($ente[0], "$ente[1] - $ente[2]", $ente[3]);
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=RelacionFacturacion-" . $fechaI . ".xls");
        $html = "";
        $html .= "<!doctype html>";
        $html .= "\n<html lang=\"en\">";
        $html .= "\n<head>";
        $html .= "\n\t<meta charset=\"UTF-8\">";
        $html .= "\n\t<meta name=\"viewport\" content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">";
        $html .= "\n\t<meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">";
        $html .= "\n\t<title>Relación de Facturación</title>";
        $html .= "\n</head>";
        $html .= "\n<body>";
        $html .= "\n\t<table style='width: 100%; border-collapse: collapse;'>";
        $html .= "\n\t\t<thead>";
        $html .= "\n\t\t\t<tr>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000;' colspan='9'>$nombreCompania <br/>NIT : $nitcompania<br/>INFORME DE RELACIÓN DE FACTURACIÓN ENTRE " . $_REQUEST['txtFechaI'] . " AL " . $_REQUEST['txtFechaF'] . "</th>";
        $html .= "\n\t\t\t</tr>";
        $html .= "\n\t\t\t<tr>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>TIPO <br/> FACTURA</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>NÚMERO DE <br/> FACTURA</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>FECHA <br/> FACTURA</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>TERCERO / REPRESENTANTE<br/> LEGAL</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>CONCEPTO</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>VALOR</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>VALOR IVA</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>VALOR IMPOCONSUMO</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>VALOR TOTAL</th>";
        $html .= "\n\t\t\t</tr>";
        $html .= "\n\t\t</thead>";
        $html .= "\n\t\t<tbody>";
        list($xxv, $xxi, $xxm, $xxt) = array(0, 0, 0, 0);

        foreach ($data as $row){
            $xdata = $this->mov->buscarFacturasMovimiento($row[4]);
            if(count($xdata) > 0){
                $html .= "<tr>";
                $html .= '<th style="border: solid 1px #000; text-align: left" colspan="9"><i>NÚMERO DE INGRESO: '.$row[1].' - '.$row[3].'FECHA DE INGRESO:'.$row[2].'</i></th>';
                $html .= "</tr>";
                list($xv, $xi, $xm, $xt) = array(0, 0, 0, 0);
                foreach ($xdata as $item){    
                    $xxxx = $this->mov->obtenerDetallesFactura($item[0]);
                    if(count($xxxx) > 0){
                        $rsp = count($xxxx)+1;
                        $html .= "<tr>";
                        $html .= '<td rowspan ="'.$rsp.'">'.$item[1].'</td>';
                        $html .= '<td rowspan ="'.$rsp.'">'.$item[2].'</td>';
                        $html .= '<td rowspan ="'.$rsp.'">'.$item[3].'</td>';
                        $html .= '<td rowspan ="'.$rsp.'">'.$item[4].'</td>';
                        $html .= "</tr>";
                        foreach ($xxxx as $xitem){
                            $html .= "<tr>";
                            $xxx = $xitem[2] + $xitem[3] + $xitem[4];
                            $html .= '<td>'.$xitem[1].'</td>';
                            $html .= '<td>'.number_format($xitem[2], 2).'</td>';
                            $html .= '<td>'.number_format($xitem[3], 2).'</td>';
                            $html .= '<td>'.number_format($xitem[4], 2).'</td>';
                            $html .= '<td>'.number_format($xxx, 2).'</td>';
                            $html .= "</tr>";
                            $xv += $xitem[2]; $xi += $xitem[3]; $xm += $xitem[4]; $xt += $xxx;
                        }
                        
                    }
                }
                 $html .= "\n\t\t\t<tr>";
                $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;' colspan='5'>TOTAL FACTURAS X INGRESO</th>";
                $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: right;'>".number_format($xv, 2)."</th>";
                $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: right;'>".number_format($xi, 2)."</th>";
                $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: right;'>".number_format($xm, 2)."</th>";
                $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: right;'>".number_format($xt, 2)."</th>";
                $html .= "\n\t\t\t</tr>";
                $xxv += $xv; $xxi += $xi; $xxm += $xm; $xxt += $xt;
            }
        }

        $html .= "\n\t\t\t<tr>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;' colspan='5'>TOTAL</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: right;'>".number_format($xxv, 2)."</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: right;'>".number_format($xxi, 2)."</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: right;'>".number_format($xxm, 2)."</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: right;'>".number_format($xxt, 2)."</th>";
        $html .= "\n\t\t\t</tr>";

  
        $html .= "\n\t\t</tbody";
        $html .= "\n\t</table>";
        $html .= "\n</body>";
        $html .= "\n</html>";
        echo $html;
    }

    public function rntPdf() {
        ini_set('max_execution_time', 0);
        session_start();
        ob_start();
        $fecha = informesController::formatearFecha("/", $_REQUEST['txtFecha']);
        $ente = $this->mov->obtenerCompania($_SESSION['compania']);
        list($nombreCompania, $nitcompania, $ruta) = array($ente[0], "$ente[1] - $ente[2]", $ente[3]);
        $data = $this->mov->listarDetallesPersonaMov(2, $fecha);
        $datax = $this->mov->obtenerrntEhd();
        require ('./fpdf/fpdf.php');
        require_once './informes/rnt/rnt.listado.php';
    }
}
