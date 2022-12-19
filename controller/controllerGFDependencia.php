<?php
/**
 * controlleGFDependencia.php
 *
 * Archivo de direcciónamiento para redirigir a un proceso por medio de la variable $action
 *
 * @author Alexander Numpaque
 * @package Tipo Archivo
 * @version $Id: controlleGFDependencia.php 001 2017-05-17 Alexander Numpaque$
 **/
require ('../json/registrar_GF_DEPENDENCIAJson.php');
@session_start();
$compania = $_SESSION['compania'];
$action   = $_REQUEST['action'];
error_reporting(E_ALL);
if($action == 'insert'){
  	$nombre           = '"'.$_POST['nombre'].'"';
  	$sigla            = !empty('"'.$_POST['sigla'].'"')?'"'.$_POST['sigla'].'"':'NULL';
  	$movimiento       = '"'.$_POST['movimiento'].'"';
  	$activa           = '"'.$_POST['activa'].'"';
 	$predecesor       = !empty($_POST['predecesor'])?$_POST['predecesor']:'NULL';
  	$centroCosto      = !empty($_POST['centroC'])?$_POST['centroC']:'NULL';
  	$tipoDependencia  = '"'.$_POST['tipo'].'"';
  	$factura          = !empty($_POST['optFactura'])?$_POST['optFactura']:'NULL';
   	$result= gf_dependencia::save_data($nombre, $sigla, $movimiento, $activa, $predecesor, $centroCosto, $tipoDependencia, $compania, $factura);

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
	if($result==true){
	  echo "<script type=\"text/javascript\">\n";
	  echo "\t$(\"#myModal1\").modal('show');\n";
	  echo "\t$(\"#ver1\").click(function(){\n";
	  echo "\t\t$(\"#myModal1\").modal('hide');\n";
	  echo "\t\twindow.location='../LISTAR_GF_DEPENDENCIA.php';";
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
}else if($action == 'modify'){
	$id = '"'.$_POST['id'].'"';
  	$nombre  = '"'.$_POST['nombre'].'"';
    $sigla            = !empty('"'.$_POST['sigla'].'"')?'"'.$_POST['sigla'].'"':'NULL';
    $movimiento       = '"'.$_POST['movimiento'].'"';
    $activa           = '"'.$_POST['activa'].'"';
    $predecesor       = !empty($_POST['predecesor'])?$_POST['predecesor']:'NULL';
    $centroCosto      = !empty($_POST['centroC'])?$_POST['centroC']:'NULL';
    $tipoDependencia  = '"'.$_POST['tipo'].'"';
    $factura          = !empty($_POST['optFactura'])?$_POST['optFactura']:'NULL';
  	$result = gf_dependencia::modify_data($nombre, $sigla, $movimiento, $activa, $predecesor, $centroCosto, $tipoDependencia, $id, $factura);
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
	echo "\t\t\t\t<p>Información modificada correctamente.</p>\n";
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
	echo "\t\t\t\t<p>No se ha podido modificar la información.</p>\n";
	echo "\t\t\t</div>\n";
	echo "\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">\n";
	echo "\t\t\t\t<button type=\"button\" id=\"ver2\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\">Aceptar</button>\n";
	echo "\t\t\t</div>\n";
	echo "\t\t</div>\n";
	echo "\t</div>\n";
	echo "</div>\n";
	echo "<link rel=\"stylesheet\" href=\"../css/bootstrap-theme.min.css\">";
	echo "<script src=\"../js/bootstrap.js\"></script>";
	if($result==true){
	  	echo "<script type=\"text/javascript\">\n";
	  	echo "\t$(\"#myModal1\").modal('show');\n";
	  	echo "\t$(\"#ver1\").click(function(){\n";
	  	echo "\t\t$(\"#myModal1\").modal('hide');\n";
	  	echo "\t\twindow.location='../LISTAR_GF_DEPENDENCIA.php';\n";
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
}else if($action === "delete"){
	$id_unico = $_POST['id'];
	$result = gf_dependencia::delete_data($id_unico);
	echo json_encode($result);
}
 ?>