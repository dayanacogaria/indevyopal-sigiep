<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 22/06/2017
 * Time: 11:57 AM
 */
session_start();
include "../json/registrar_GS_MenuJson.php";
$action = "";
if(!empty($_GET['action'])) {
    $action = $_GET['action'];
}elseif (!empty($_POST['action'])) {
    $action = $_POST['action'];
}

if($action == 'insert') {
    $nombre     = $_POST['txtNombreM'];
    $orden      = $_POST['txtOrden'];
    $estado     = $_POST['sltEstado'];

    if(empty($_POST['txtRutaM'])) {
        $ruta = "NULL";
    } else {
        $ruta = '"'.$_POST['txtRutaM'].'"';
    }
    if(empty($_POST['sltMenuPadre'])) {
        $padre = "NULL";
    } else {
        $padre = $_POST['sltMenuPadre'];
    }

    $result = menu::save_data_main($nombre, $ruta,  $orden, $estado, $padre);
    

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
    echo "<script src=\"../js/bootstrap.min.js\"></script>";
    if($result == true){
        echo "\n<script type=\"text/javascript\">";
        echo "\t$(\"#myModal1\").modal('show');\n";
        echo "\t$(\"#ver1\").click(function(){\n";
        echo "\t\t$(\"#myModal1\").modal('hide');\n";
        echo "\t\twindow.location='../registrar_GS_MENU.php';\n";
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
}else if($action == 'modify') {
    require ('../Conexion/conexion.php');
    $id_unico   = $_POST['id_unico'];
    $nombre     = $_POST['nombre'];
    $orden      = $_POST['orden'];
    $estado     = $_POST['estado'];

    if(empty($_POST['ruta'])) {
        $ruta = "NULL";
    } else {
        $ruta = '"'.$_POST['ruta'].'"';
    }
    if(empty($_POST['npadre'])) {
        $padre = "NULL";
    } else {
        $padre = $_POST['npadre'];
    }
    $edited = false;
    $sql = "UPDATE gs_menu 
        SET nombre = \"$nombre\", ruta =$ruta, 
            predecesor = $padre, orden = $orden, 
        estado = $estado WHERE id_unico = $id_unico";
    $result = $mysqli->query($sql);
    if($result == true) {
        $edited = true;
    }
    echo json_encode($edited);
}else if($action == 'delete') {
    $son = $_POST['son'];
    $result = menu::delete_option($son);
    echo json_encode($result);
}