<?php
session_start();
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Creación de archivo  : 26/04/2017
// Creado por           : Alexander Numpaque
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Archivos abjuntos
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
require ('../Conexion/conexion.php');
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Capturamos la variable action envia por get
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$action = $_GET['action'];
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Validamos los posibles valores que la Variable action tendra
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($action == 'insert') {              //Si la variable tiene el valor insert
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Capturamos la variable enviada por post
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $nombre = '"'.$mysqli->real_escape_string(''.$_POST['txtNombre'].'').'"';
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Realizamos el insertado
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $sql = "INSERT INTO gn_clase_informe(nombre) VALUES($nombre)";
  $result = $mysqli->query($sql);
  echo "<html>\n";
  echo "<head>\n";
  echo "<meta charset=\"utf-8\">\n";
  echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
  echo "<link rel=\"stylesheet\" href=\"../css/bootstrap.min.css\">\n";
  echo "<link rel=\"stylesheet\" href=\"../css/style.css\">\n";
  echo "<script src=\"../js/md5.pack.js\"></script>\n";
  echo "<script src=\"../js/jquery.min.js\"></script>\n";
  echo "<link rel=\"stylesheet\" href=\"../css/jquery-ui.css\" type=\"text/css\" media=\"screen\" title=\"default\" />\n";
  echo "<script type=\"text/javascript\" language=\"javascript\" src=\"../js/jquery-1.10.2.js\"></script>\n";
  echo "</head>\n";
  echo "<body>\n";
  echo "</body>\n";
  echo "</html>\n";
  echo "<div class=\"modal fade\" id=\"myModal1\" role=\"dialog\" align=\"center\" >\n";
  echo "<div class=\"modal-dialog\">\n";
  echo "<div class=\"modal-content\">\n";
  echo "<div id=\"forma-modal\" class=\"modal-header\">\n";
  echo "<h4 class=\"modal-title\" style=\"font-size: 24; padding: 3px;\">Información</h4>\n";
  echo "</div>\n";
  echo "<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
  echo "<p>Información guardada correctamente.</p>\n";
  echo "</div>\n";
  echo "<div id=\"forma-modal\" class=\"modal-footer\">\n";
  echo "<button type=\"button\" id=\"ver1\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\" >Aceptar</button>\n";
  echo "</div>\n";
  echo "</div>\n";
  echo "</div>\n";
  echo "</div>\n";
  echo "<div class=\"modal fade\" id=\"myModal2\" role=\"dialog\" align=\"center\" >\n";
  echo "<div class=\"modal-dialog\">\n";
  echo "<div class=\"modal-content\">\n";
  echo "<div id=\"forma-modal\" class=\"modal-header\">\n";
  echo "<h4 class=\"modal-title\" style=\"font-size: 24; padding: 3px;\">Información</h4>\n";
  echo "</div>\n";
  echo "<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
  echo "<p>No se ha podido guardar la información.</p>\n";
  echo "</div>\n";
  echo "<div id=\"forma-modal\" class=\"modal-footer\">\n";
  echo "<button type=\"button\" id=\"ver2\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\">Aceptar</button>\n";
  echo "</div>\n";
  echo "</div>\n";
  echo "</div>\n";
  echo "</div>\n";
  echo "<link rel=\"stylesheet\" href=\"../css/bootstrap-theme.min.css\">";
  echo "<script src=\"../js/bootstrap.min.js\"></script>";
  if($result==true){
    echo "<script type=\"text/javascript\">\n";
    echo "$(\"#myModal1\").modal('show');\n";
    echo "$(\"#ver1\").click(function(){\n";
    echo "$(\"#myModal1\").modal('hide');\n";
    echo "window.location='../LISTAR_GN_CLASE_INFORME.php';";
    echo "});";
    echo "</script>";
  }else{
    echo "<script type=\"text/javascript\">";
    echo "$(\"#myModal2\").modal('show');\n";
    echo "$(\"#ver2\").click(function(){\n";
    echo "$(\"#myModal2\").modal('hide');\n";
    echo "window.history.go(-1)";
    echo "});";
    echo "</script>";
  }
}else if($action == 'edit') {          //Si la variable tiene el valor edit
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Capturamos las variables enviadas por post
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $id = $_POST['id'];                                                           //Id de la clase contable
  $nombre = '"'.$mysqli->real_escape_string(''.$_POST['txtNombre'].'').'"';
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Consulta para modificacion de valores
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $sql = "UPDATE gn_clase_informe SET nombre = $nombre WHERE id_unico = $id";
  $result = $mysqli->query($sql);
  echo "<html>\n";
  echo "<head>\n";
  echo "<meta charset=\"utf-8\">\n";
  echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
  echo "<link rel=\"stylesheet\" href=\"../css/bootstrap.min.css\">\n";
  echo "<link rel=\"stylesheet\" href=\"../css/style.css\">\n";
  echo "<script src=\"../js/md5.pack.js\"></script>\n";
  echo "<script src=\"../js/jquery.min.js\"></script>\n";
  echo "<link rel=\"stylesheet\" href=\"../css/jquery-ui.css\" type=\"text/css\" media=\"screen\" title=\"default\" />\n";
  echo "<script type=\"text/javascript\" language=\"javascript\" src=\"../js/jquery-1.10.2.js\"></script>\n";
  echo "</head>\n";
  echo "<body>\n";
  echo "</body>\n";
  echo "</html>\n";
  echo "<div class=\"modal fade\" id=\"myModal1\" role=\"dialog\" align=\"center\" >\n";
  echo "<div class=\"modal-dialog\">\n";
  echo "<div class=\"modal-content\">\n";
  echo "<div id=\"forma-modal\" class=\"modal-header\">\n";
  echo "<h4 class=\"modal-title\" style=\"font-size: 24; padding: 3px;\">Información</h4>\n";
  echo "</div>\n";
  echo "<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
  echo "<p>Información modificada correctamente.</p>\n";
  echo "</div>\n";
  echo "<div id=\"forma-modal\" class=\"modal-footer\">\n";
  echo "<button type=\"button\" id=\"ver1\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\" >Aceptar</button>\n";
  echo "</div>\n";
  echo "</div>\n";
  echo "</div>\n";
  echo "</div>\n";
  echo "<div class=\"modal fade\" id=\"myModal2\" role=\"dialog\" align=\"center\" >\n";
  echo "<div class=\"modal-dialog\">\n";
  echo "<div class=\"modal-content\">\n";
  echo "<div id=\"forma-modal\" class=\"modal-header\">\n";
  echo "<h4 class=\"modal-title\" style=\"font-size: 24; padding: 3px;\">Información</h4>\n";
  echo "</div>\n";
  echo "<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
  echo "<p>No se ha podido modificar la información.</p>\n";
  echo "</div>\n";
  echo "<div id=\"forma-modal\" class=\"modal-footer\">\n";
  echo "<button type=\"button\" id=\"ver2\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\">Aceptar</button>\n";
  echo "</div>\n";
  echo "</div>\n";
  echo "</div>\n";
  echo "</div>\n";
  echo "<link rel=\"stylesheet\" href=\"../css/bootstrap-theme.min.css\">";
  echo "<script src=\"../js/bootstrap.min.js\"></script>";
  if($result==true){
    echo "<script type=\"text/javascript\">\n";
    echo "$(\"#myModal1\").modal('show');\n";
    echo "$(\"#ver1\").click(function(){\n";
    echo "$(\"#myModal1\").modal('hide');\n";
    echo "window.location='../LISTAR_GN_CLASE_INFORME.php';";
    echo "});";
    echo "</script>";
  }else{
    echo "<script type=\"text/javascript\">";
    echo "$(\"#myModal2\").modal('show');\n";
    echo "$(\"#ver2\").click(function(){\n";
    echo "$(\"#myModal2\").modal('hide');\n";
    echo "window.history.go(-1)";
    echo "});";
    echo "</script>";
  }
}else if($action == 'delete') {        //Si la variable tiene el valor delete
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Capturamos las variables enviadas por get
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $id = $_GET['id'];
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Consulta para eliminar la clase informe
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $sql = "DELETE FROM gn_clase_informe WHERE id_unico = $id";
  $result = $mysqli->query($sql);
  echo json_encode($result);
}
 ?>
