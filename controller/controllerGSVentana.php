<?php
/**
 * Created by PhpStorm.
 * User: Alexander Numpaque
 * Date: 29/06/2017
 * Time: 3:06 PM
 */
require ('../json/registrarGSVentana.php');

if(!empty($_POST['action'])) {
    $action = $_POST['action'];
}

if(!empty($_GET['action'])) {
    $action = $_GET['action'];
}

switch ($action) {
    case 'insert':
        $nombre = '"'.$_POST['txtNombre'].'"';
        $result = ventana::save_data($nombre);
        if($result == true) {
            $ventana = ventana::get_last_window();
            $menu = explode("/", $_POST['sltMenuAso']); $padre = $menu[0]; $hijo = $menu[1];
            $menu_aso = ventana::get_menu_aso($padre, $hijo);
            $menu_v = ventana::save_main_window($ventana, $menu_aso);
            if($menu_v == true) {
                $menu_vn = ventana::get_last_menu_ventana();
                $botones = $_POST['sltBoton'];
                for($b = 0; $b < count($botones); $b++) {
                    ventana::save_buttons($menu_vn, $botones[$b]);
                }
            }
        }
        echo "\n<html>";
        echo "\n<head>";
        echo "\n\t<meta charset=\"utf-8\">";
        echo "\n\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
        echo "\n\t<link rel=\"stylesheet\" href=\"../css/bootstrap.min.css\">";
        echo "\n\t<link rel=\"stylesheet\" href=\"../css/style.css\">";
        echo "\n\t<script src=\"../js/md5.pack.js\"></script>";
        echo "\n\t<script src=\"../js/jquery.min.js\"></script>";
        echo "\n\t<link rel=\"stylesheet\" href=\"../css/jquery-ui.css\" type=\"text/css\" media=\"screen\" title=\"default\" />";
        echo "\n\t<script type=\"text/javascript\" language=\"javascript\" src=\"../js/jquery-1.10.2.js\"></script>";
        echo "\n</head>";
        echo "\n<body>";
        echo "\n</body>";
        echo "\n</html>";
        echo "\n<div class=\"modal fade\" id=\"myModal1\" role=\"dialog\" align=\"center\" >";
        echo "\n\t<div class=\"modal-dialog\">";
        echo "\n\t\t<div class=\"modal-content\">";
        echo "\n\t\t\t<div id=\"forma-modal\" class=\"modal-header\">";
        echo "\n\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>";
        echo "\n\t\t\t</div>";
        echo "\n\t\t\t<div class=\"modal-body\" style=\"margin-top: 8px\">";
        echo "\n\t\t\t\t<p>Información guardada correctamente.</p>";
        echo "\n\t\t\t</div>";
        echo "\n\n\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">";
        echo "\n\t\t\t\t<button type=\"button\" id=\"ver1\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\" >Aceptar</button>";
        echo "\n\t\t\t</div>";
        echo "\n\t\t</div>";
        echo "\n\t</div>";
        echo "\n</div>";
        echo "\n<div class=\"modal fade\" id=\"myModal2\" role=\"dialog\" align=\"center\" >";
        echo "\n\n\t<div class=\"modal-dialog\">";
        echo "\n\t\t<div class=\"modal-content\">";
        echo "\n\t\t\t<div id=\"forma-modal\" class=\"modal-header\">";
        echo "\n\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>";
        echo "\n\t\t\t</div>";
        echo "\n\t\t\t<div class=\"modal-body\" style=\"margin-top: 8px\">";
        echo "\n\t\t\t\t<p>No se ha podido guardar la información.</p>";
        echo "\n\t\t\t</div>";
        echo "\n\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">";
        echo "\n\t\t\t\t<button type=\"button\" id=\"ver2\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\">Aceptar</button>";
        echo "\n\t\t\t</div>";
        echo "\n\t\t</div>";
        echo "\n\t</div>";
        echo "\n</div>";
        echo "\n<link rel=\"stylesheet\" href=\"../css/bootstrap-theme.min.css\">";
        echo "\n<script src=\"../js/bootstrap.min.js\"></script>";
        if($result==true){
            $ventana = ventana::get_last_window();
            echo "\n<script type=\"text/javascript\">";
            echo "\n\t$(\"#myModal1\").modal('show');";
            echo "\n\t$(\"#ver1\").click(function(){";
            echo "\n\t\t$(\"#myModal1\").modal('hide');";
            echo "\n\t\twindow.location='../registrar_GS_VENTANA.php'";
            echo "\n\t});";
            echo "\n</script>";
        }else{
            echo "\n<script type=\"text/javascript\">";
            echo "\n\t$(\"#myModal2\").modal('show');";
            echo "\n\t$(\"#ver2\").click(function(){";
            echo "\n\t\t$(\"#myModal2\").modal('hide');";
            echo "\n\t\twindow.history.go(-1)";
            echo "\n\t});";
            echo "</script>";
        }
        break;
    case 'modify':
        $idV = $_POST['id_V'];
        $menuV = $_POST['menuV'];
        $nombre = $_POST['txtNombre'];
        $sltMenuAso = $_POST['sltMenuAso'];
        $botones = $_POST['sltBoton'];
        $result = ventana::modify_data($idV, $nombre);
        for ($b = 0; $b < count($botones); $b++) {
            $ventana_boton = ventana::get_ventana_boton($menuV, $botones[$b]);
            if($ventana_boton == 0) {
                $ventana = ventana::save_buttons($menuV, $botones[$b]);
            }
        }
        echo json_encode($result);
        break;
    case 'delete':
        $mnV = $_POST['mnV'];
        $id = $_POST['id'];
        $botones = ventana::delete_buttons($mnV);
        $mvV = ventana::delete_main_window($mnV);
        $result = ventana::delete_data($id);
        echo json_encode($result);
        break;
    case 'insert_button':
        $nombre = '"'.$_POST['txtNombreB'].'"';
        $result = ventana::save_button($nombre);
        echo "\n<html>";
        echo "\n<head>";
        echo "\n\t<meta charset=\"utf-8\">";
        echo "\n\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
        echo "\n\t<link rel=\"stylesheet\" href=\"../css/bootstrap.min.css\">";
        echo "\n\t<link rel=\"stylesheet\" href=\"../css/style.css\">";
        echo "\n\t<script src=\"../js/md5.pack.js\"></script>";
        echo "\n\t<script src=\"../js/jquery.min.js\"></script>";
        echo "\n\t<link rel=\"stylesheet\" href=\"../css/jquery-ui.css\" type=\"text/css\" media=\"screen\" title=\"default\" />";
        echo "\n\t<script type=\"text/javascript\" language=\"javascript\" src=\"../js/jquery-1.10.2.js\"></script>";
        echo "\n</head>";
        echo "\n<body>";
        echo "\n</body>";
        echo "\n</html>";
        echo "\n<div class=\"modal fade\" id=\"myModal1\" role=\"dialog\" align=\"center\" >";
        echo "\n\t<div class=\"modal-dialog\">";
        echo "\n\t\t<div class=\"modal-content\">";
        echo "\n\t\t\t<div id=\"forma-modal\" class=\"modal-header\">";
        echo "\n\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>";
        echo "\n\t\t\t</div>";
        echo "\n\t\t\t<div class=\"modal-body\" style=\"margin-top: 8px\">";
        echo "\n\t\t\t\t<p>Información guardada correctamente.</p>";
        echo "\n\t\t\t</div>";
        echo "\n\n\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">";
        echo "\n\t\t\t\t<button type=\"button\" id=\"ver1\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\" >Aceptar</button>";
        echo "\n\t\t\t</div>";
        echo "\n\t\t</div>";
        echo "\n\t</div>";
        echo "\n</div>";
        echo "\n<div class=\"modal fade\" id=\"myModal2\" role=\"dialog\" align=\"center\" >";
        echo "\n\n\t<div class=\"modal-dialog\">";
        echo "\n\t\t<div class=\"modal-content\">";
        echo "\n\t\t\t<div id=\"forma-modal\" class=\"modal-header\">";
        echo "\n\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>";
        echo "\n\t\t\t</div>";
        echo "\n\t\t\t<div class=\"modal-body\" style=\"margin-top: 8px\">";
        echo "\n\t\t\t\t<p>No se ha podido guardar la información.</p>";
        echo "\n\t\t\t</div>";
        echo "\n\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">";
        echo "\n\t\t\t\t<button type=\"button\" id=\"ver2\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\">Aceptar</button>";
        echo "\n\t\t\t</div>";
        echo "\n\t\t</div>";
        echo "\n\t</div>";
        echo "\n</div>";
        echo "\n<link rel=\"stylesheet\" href=\"../css/bootstrap-theme.min.css\">";
        echo "\n<script src=\"../js/bootstrap.min.js\"></script>";
        if($result==true){
            echo "\n<script type=\"text/javascript\">";
            echo "\n\t$(\"#myModal1\").modal('show');";
            echo "\n\t$(\"#ver1\").click(function(){";
            echo "\n\t\t$(\"#myModal1\").modal('hide');";
            echo "\n\t\twindow.location='../registrar_GS_VENTANA.php'";
            echo "\n\t});";
            echo "\n</script>";
        }else{
            echo "\n<script type=\"text/javascript\">";
            echo "\n\t$(\"#myModal2\").modal('show');";
            echo "\n\t$(\"#ver2\").click(function(){";
            echo "\n\t\t$(\"#myModal2\").modal('hide');";
            echo "\n\t\twindow.history.go(-1)";
            echo "\n\t});";
            echo "</script>";
        }
        break;
    case 'modify_button':
        $nombre = $_POST['txtNombreB'];
        $id = $_POST['txtIdButton'];
        $result = ventana::modify_button($id, $nombre);
        echo json_encode($result);
        break;
    case 'delete_button':
        $id = $_POST['id'];
        $result = ventana::delete_button($id);
        echo json_encode($result);
        break;
    case 'insert_input':
        $nombre = '"'.$_POST['txtNombreC'].'"';
        $result = ventana::save_input($nombre);
        echo "\n<html>";
        echo "\n<head>";
        echo "\n\t<meta charset=\"utf-8\">";
        echo "\n\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
        echo "\n\t<link rel=\"stylesheet\" href=\"../css/bootstrap.min.css\">";
        echo "\n\t<link rel=\"stylesheet\" href=\"../css/style.css\">";
        echo "\n\t<script src=\"../js/md5.pack.js\"></script>";
        echo "\n\t<script src=\"../js/jquery.min.js\"></script>";
        echo "\n\t<link rel=\"stylesheet\" href=\"../css/jquery-ui.css\" type=\"text/css\" media=\"screen\" title=\"default\" />";
        echo "\n\t<script type=\"text/javascript\" language=\"javascript\" src=\"../js/jquery-1.10.2.js\"></script>";
        echo "\n</head>";
        echo "\n<body>";
        echo "\n</body>";
        echo "\n</html>";
        echo "\n<div class=\"modal fade\" id=\"myModal1\" role=\"dialog\" align=\"center\" >";
        echo "\n\t<div class=\"modal-dialog\">";
        echo "\n\t\t<div class=\"modal-content\">";
        echo "\n\t\t\t<div id=\"forma-modal\" class=\"modal-header\">";
        echo "\n\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>";
        echo "\n\t\t\t</div>";
        echo "\n\t\t\t<div class=\"modal-body\" style=\"margin-top: 8px\">";
        echo "\n\t\t\t\t<p>Información guardada correctamente.</p>";
        echo "\n\t\t\t</div>";
        echo "\n\n\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">";
        echo "\n\t\t\t\t<button type=\"button\" id=\"ver1\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\" >Aceptar</button>";
        echo "\n\t\t\t</div>";
        echo "\n\t\t</div>";
        echo "\n\t</div>";
        echo "\n</div>";
        echo "\n<div class=\"modal fade\" id=\"myModal2\" role=\"dialog\" align=\"center\" >";
        echo "\n\n\t<div class=\"modal-dialog\">";
        echo "\n\t\t<div class=\"modal-content\">";
        echo "\n\t\t\t<div id=\"forma-modal\" class=\"modal-header\">";
        echo "\n\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>";
        echo "\n\t\t\t</div>";
        echo "\n\t\t\t<div class=\"modal-body\" style=\"margin-top: 8px\">";
        echo "\n\t\t\t\t<p>No se ha podido guardar la información.</p>";
        echo "\n\t\t\t</div>";
        echo "\n\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">";
        echo "\n\t\t\t\t<button type=\"button\" id=\"ver2\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\">Aceptar</button>";
        echo "\n\t\t\t</div>";
        echo "\n\t\t</div>";
        echo "\n\t</div>";
        echo "\n</div>";
        echo "\n<link rel=\"stylesheet\" href=\"../css/bootstrap-theme.min.css\">";
        echo "\n<script src=\"../js/bootstrap.min.js\"></script>";
        if($result==true){
            echo "\n<script type=\"text/javascript\">";
            echo "\n\t$(\"#myModal1\").modal('show');";
            echo "\n\t$(\"#ver1\").click(function(){";
            echo "\n\t\t$(\"#myModal1\").modal('hide');";
            echo "\n\t\twindow.location='../registrar_GS_VENTANA.php'";
            echo "\n\t});";
            echo "\n</script>";
        }else{
            echo "\n<script type=\"text/javascript\">";
            echo "\n\t$(\"#myModal2\").modal('show');";
            echo "\n\t$(\"#ver2\").click(function(){";
            echo "\n\t\t$(\"#myModal2\").modal('hide');";
            echo "\n\t\twindow.history.go(-1)";
            echo "\n\t});";
            echo "</script>";
        }
        break;
    case 'modify_input':
        $nombre = $_POST['txtNombreC'];
        $id = $_POST['txtIdInput'];
        $result = ventana::modify_input($id, $nombre);
        echo json_encode($result);
        break;
    case 'delete_input':
        $id = $_POST['id'];
        $result = ventana::delete_input($id);
        echo json_encode($result);
        break;
}