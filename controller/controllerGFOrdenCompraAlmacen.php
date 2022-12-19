<?php
/**
 * Created by Alexander.
 * User: Alexander
 * Date: 06/06/2017
 * Time: 10:59 AM
 *
 * controllerGFOrdenCompraAlmacen.php
 *
 * Archivo de direcciónamiento para invocar la clase y realizar una acción dependiendo de la variable action recibida
 * @author Alexander Numpaque
 * @package Movimiento
 * @param String $action Variable para indicar que proceso se va a realizar
 * @version $Id: controllerGFOrdenCompraAlmacen.php 001 2017-06-06 Alexander Numpaque$
 */
@session_start();
require ('../json/registrarMovimientoAlmacenJson.php');
if(!empty($_POST['action'])){
    $action =  $_POST['action'];
}elseif (!empty($_GET['action'])) {
    $action =  $_GET['action'];
}
if($action == 'insert'){
    $estadoM       = '"2"';
    $lugarE        = '"'.$_POST['sltLE'].'"';
    $unidadPE      = '"'.$_POST['sltUPE'].'"';
    $plazoE        = '"'.$_POST['txtPlazoE'].'"';
    #Validación de campos no obligatorios
    if(!empty($_POST['txtObservacion'])){
        $observaciones = '"'.$_POST['txtObservacion'].'"';
    }else{
        $observaciones = 'NULL';
    }
    if(!empty($_POST['txtDescripcion'])){
        $descripcion   = '"'.$_POST['txtDescripcion'].'"';
    }else{
        $descripcion = 'NULL';
    }

    $proyecto      = '"'.$_POST['sltProyecto'].'"';
    $centrocosto   = '"'.$_POST['sltCentroCosto'].'"';
    $rubroP        = !empty($_POST['sltRubroP'])?'"'.$_POST['sltRubroP'].'"':"NULL";
    $tercero       = '"'.$_POST['sltTercero'].'"';
    $dependencia   = '"'.$_POST['sltDependencia'].'"';
    $responsable   = '"'.$_POST['sltResponsable'].'"';
    #Conversión de fecha
    $fechaT = ''.$_POST['txtFecha'].'';
    $valorF = explode("/",$fechaT);
    $fechaC =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
    $paramA = $_SESSION['anno'];
    $compania = $_SESSION['compania'];
    $numeroC = ''.$_POST['txtNumero'].'';
    $tipoM  = ''.$_POST['sltTipoMov'].'';
    $iva = $_POST['txtIva'];
    $id_asoc = $_POST['txtAsociado'];
    $tipo_doc_sopt = $_POST['sltDocSoporte'];
    $num_doc = '"'.$_POST['txtNumDocS'].'"';
    $result = movimiento::save_data($numeroC, $fechaC, $descripcion, $plazoE, $observaciones, $tipoM, $paramA, $responsable, $tercero, $dependencia, $centrocosto, $rubroP, $proyecto, $lugarE, $unidadPE, $estadoM, $iva, $compania, $tipo_doc_sopt, $num_doc);
    echo "<html>\n";
    echo "<head>\n";
    echo "\t<meta charset=\"utf-8\">\n";
    echo "\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
    echo "\t<link rel=\"stylesheet\" href=\"../css/bootstrap.min.css\">\n";
    echo "\t<link rel=\"stylesheet\" href=\"../css/style.css\">\n";
    echo "\t<script src=\"../js/md5.pack.js\"></script>\n";
    echo "\t<script src=\"../js/jquery.min.js\"></script>\n";
    echo "\t<link rel=\"stylesheet\" href=\"../css/jquery-ui.css\" type=\"text/css\" media=\"screen\" title=\"default\" />\n";
    echo "\t<script type=\"text/javascript\" language=\"javascript\" src=\"../js/jquery-1.10.2.js\"></script>\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "</body>\n";
    echo "</html>\n";
    echo "<div class=\"modal fade\" id=\"myModal1\" role=\"dialog\" align=\"center\" >\n";
    echo "\t<div class=\"modal-dialog\">\n";
    echo "\t\t<div class=\"modal-content\">\n";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-header\">\n";
    echo "\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t\t<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
    echo "\t\t\t\t<p>Información guardada correctamente.</p>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">\n";
    echo "\t\t\t\t<button type=\"button\" id=\"ver1\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\" >Aceptar</button>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t</div>\n";
    echo "\t</div>\n";
    echo "</div>\n";
    echo "<div class=\"modal fade\" id=\"myModal2\" role=\"dialog\" align=\"center\" >\n";
    echo "\t<div class=\"modal-dialog\">\n";
    echo "\t\t<div class=\"modal-content\">\n";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-header\">\n";
    echo "\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t\t<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
    echo "\t\t\t\t<p>No se ha podido guardar la información.</p>\n";
    echo "\t\t\t\n</div>";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">\n";
    echo "\t\t\t\t<button type=\"button\" id=\"ver2\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\">Aceptar</button>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t</div>\n";
    echo "\t</div>\n";
    echo "</div>\n";
    echo "<link rel=\"stylesheet\" href=\"../css/bootstrap-theme.min.css\">";
    echo "<script src=\"../js/bootstrap.js\"></script>";
    if($result == true){
        $movimiento = movimiento::get_last_id($tipoM, $numeroC);
        if(!empty($id_asoc)){
            $nedle = strpos($id_asoc,",");
            if($nedle > 0) {
                $id_aso = explode(",",$id_asoc);
                for ($b = 0; $b <= count($id_asoc); $b++) {
                    $details = movimiento::get_detail_mov($id_aso[$b]);
                    $movimiento = movimiento::get_last_id($tipoM, $numeroC);
                    for ($a = 0;$a < count($details); $a++) {
                        $values = movimiento::get_values_detail($details[$a]);
                        $dataAso = movimiento::obtnerDataAsociado($details[$a]);
                        $xc = 0;
                        foreach ($dataAso as $rc) {
                            $xc += $rc[0];
                        }
                        $xxx = $values[1] - $xc;
                        if($xxx > 0){
                            movimiento::save_detail_mov($values[0], $values[1], $values[2], $values[3], $movimiento, $details[$a]);
                        }
                    }
                }
            }else{
                $details = movimiento::get_detail_mov($id_asoc);
                $movimiento = movimiento::get_last_id($tipoM, $numeroC);
                for ($a = 0;$a < count($details); $a++) {
                    $values = movimiento::get_values_detail($details[$a]);
                    $dataAso = movimiento::obtnerDataAsociado($details[$a]);
                    $xc = 0;
                    foreach ($dataAso as $rc) {
                        $xc += $rc[0];
                    }

                    $xxx = $values[1] - $xc;
                    if($xxx > 0){
                        movimiento::save_detail_mov($values[0], $xxx, $values[2], $values[3], $movimiento, $details[$a]);
                    }

                }
            }
        }
        echo "\n<script type=\"text/javascript\">";
        echo "\t$(\"#myModal1\").modal('show');\n";
        echo "\t$(\"#ver1\").click(function(){\n";
        echo "\t\t$(\"#myModal1\").modal('hide');\n";
        echo "\t\twindow.location='../registrar_RF_ORDEN_DE_COMPRA.php?movimiento=".md5($movimiento)."';\n";
        echo "\t});";
        echo "</script>";
    }else{
        echo "<script type=\"text/javascript\">";
        echo "\t$(\"#myModal2\").modal('show');\n";
        echo "\t$(\"#ver2\").click(function(){\n";
        echo "\t\t$(\"#myModal2\").modal('hide');\n";
        echo "\t\twindow.history.go(-1)";
        echo "\t});";
        echo "</script>";
    }
}