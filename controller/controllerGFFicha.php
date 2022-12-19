<?php
/**
 * controllerGFFicha.php
 *
 * Archivo de direcciónamiento para invocar la clase y realizar una acción dependiendo de la variable actión recibida
 *
 * @author Alexander Numpaque
 * @package Tipo Archivo
 * @param String $action Variable para indicar que proceso se va a realizar
 * @version $Id: controllerGFFicha.php 001 2017-05-26 Alexander Numpaque$
 **/

require ('../json/registrarFichaJson.php');
if(!empty($_POST['action'])){
    $action =  $_POST['action'];
}elseif ($_GET['action']) {
    $action =  $_GET['action'];
}
if($action == 'insert'){
    $nombre = '"'.$_POST['txtDescripcion'].'"';
    $result = gf_ficha::save_data($nombre);
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
    if($result==true){
        echo "<script type=\"text/javascript\">\n";
        echo "\t$(\"#myModal1\").modal('show');\n";
        echo "\t$(\"#ver1\").click(function(){\n";
        echo "\t\t$(\"#myModal1\").modal('hide');\n";
        echo "\t\twindow.location='../listar_GF_FICHA.php';";
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
    $nombre = '"'.$_POST['txtDescripcion'].'"';
    $id_unico = $_POST['id'];
    $result = gf_ficha::modify_data($nombre, $id_unico);
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
    if($result==true){
        echo "<script type=\"text/javascript\">\n";
        echo "\t$(\"#myModal1\").modal('show');\n";
        echo "\t$(\"#ver1\").click(function(){\n";
        echo "\t\t$(\"#myModal1\").modal('hide');\n";
        echo "\t\twindow.location='../listar_GF_FICHA.php';\n";
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
    $id_unico = $_POST['id_unico'];
    $result = gf_ficha::delete_data($id_unico);
    echo json_encode($result);
}

?>