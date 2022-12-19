<?php 
/**
 * controllerGSClaseArchivo.php
 * 
 * Archivo de direcciónamiento para invocar la clase y realizar una acción dependiendo de la variable actión recibida
 * 
 * @author Alexander Numpaque
 * @package Tipo Archivo
 * @param String $action Variable para indicar que proceso se va a realizar
 * @param String $nombre Variable con el nombre a guardar o modificar dependiendo del action
 * @param int $id_unico Variable que contiene el id del registro la cual se usara para modificar o eliminar dependiendo del action
 * @param int $id_tipo Variable con el tipo de archivo
 * @param int|String $linea_inicial variable trae el valor el cual indica de que linea se inicia la lectura del archivo
 * @version $Id: controllerGSClaseArchivo.php 001 2017-05-17 Alexander Numpaque$
 **/
require ('../json/registrarGSClaseArchivo.php');			//Llamamos al archivo que contiene la clase
//Validamos si la variable action es recibida por post o por get
if(!empty($_POST['action'])){
	$action =  $_POST['action'];							//Captura de variable cuando se recibe por POST
}elseif ($_GET['action']) {
	$action =  $_GET['action'];								//Captura de varibale cuando se recibe por GET
}
if($action == 'insert'){									//Si action es insert
	$nombre = '"'.$_POST['txtNombre'].'"';					
	$id_tipo = '"'.$_POST['sltTipoA'].'"';
	$lineaI = '"'.$_POST['txtLineaI'].'"';
	$inserted = claseArchivo::save_class($nombre, $id_tipo, $lineaI);
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
	echo "\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24; padding: 3px;\">Información</h4>\n";
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
	echo "\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24; padding: 3px;\">Información</h4>\n";
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
	echo "<script src=\"../js/bootstrap.min.js\"></script>";
	if($inserted==true){  
	  echo "<script type=\"text/javascript\">\n";
	  echo "\t$(\"#myModal1\").modal('show');\n";
	  echo "\t$(\"#ver1\").click(function(){\n";
	  echo "\t\t$(\"#myModal1\").modal('hide');\n";
	  echo "\t\twindow.location='../listar_GS_CLASE_ARCHIVO.php';";
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
}else if($action == 'modify'){									//Si action es modify
	$nombre = '"'.$_POST['txtNombre'].'"';					
	$id_tipo = '"'.$_POST['sltTipoA'].'"';
	$lineaI = '"'.$_POST['txtLineaI'].'"';	
	$id = $_POST['id'];
	$edited = claseArchivo::modify_class($nombre, $id_tipo, $lineaI, $id);
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
	echo "\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24; padding: 3px;\">Información</h4>\n";
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
	echo "\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24; padding: 3px;\">Información</h4>\n";
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
	echo "<script src=\"../js/bootstrap.min.js\"></script>";
	if($edited==true){  
	  	echo "<script type=\"text/javascript\">\n";
	  	echo "\t$(\"#myModal1\").modal('show');\n";
	  	echo "\t$(\"#ver1\").click(function(){\n";
	  	echo "\t\t$(\"#myModal1\").modal('hide');\n";
	  	echo "\t\twindow.location='../listar_GS_CLASE_ARCHIVO.php';\n";
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
}else if($action == "delete"){									//Si action es delete
	$id_unico = $_POST['id'];
	$deleted = claseArchivo::delete_class($id_unico);
	echo json_encode($deleted);
}
 ?>