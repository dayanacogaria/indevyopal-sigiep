<?php
session_start();    //Inicializamos session
require ('../Conexion/conexion.php');
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Capturamos la valores enviados por el formulario
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$fecha = explode("/",$_POST['txtFecha']);                                     //Capturamos la fecha y la dividimos
$fecha = "'$fecha[2]-$fecha[1]-$fecha[0]'";                                   //Formateamos la fecha (yyyy-mm-dd)
$tipo = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoC'].'').'"';        //Tipo de comprobante
$numero = '"'.$mysqli->real_escape_string(''.$_POST['txtNumero'].'').'"';     //Número de comprobante
$fechaV = explode("/",$_POST['txtFechaV']);                                   //Capturamos la fecha y la dividimos usando /
$fechaV = "'$fechaV[2]-$fechaV[1]-$fechaV[0]'";                               //Formateamos la fecha (yyyy-mm-dd)
$tercero = '"'.$mysqli->real_escape_string(''.$_POST['sltTercero'].'').'"';   //Tercero
$estado = 1;                                                                  //Estado del comprobante
$param = $_SESSION['anno'];                                                   //Variable de parametrización año
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Validmos los posibles campos que puede venir vacios
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(!empty($_POST['txtDescripcion'])){                                         //Descripción del comprobante
  $desn = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';
}else{
  $desn = 'NULL';
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Insertamos en la tabla comprobante pptal
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sql = "INSERT INTO gf_comprobante_pptal(fecha, tipocomprobante, numero, fechavencimiento, tercero, estado, descripcion, parametrizacionanno) VALUES($fecha, $tipo, $numero, $fechaV, $tercero, $estado, $desn, $param)";
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
  $sqlU = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE numero = $numero AND tipocomprobante = $tipo";
  $resultU = $mysqli->query($sqlU);
  $rowU = mysqli_fetch_row($resultU);
  echo "<script type=\"text/javascript\">\n";
  echo "$(\"#myModal1\").modal('show');\n";
  echo "$(\"#ver1\").click(function(){\n";
  echo "$(\"#myModal1\").modal('hide');\n";
  echo "window.location='../registrar_GF_RECAUDO_PPTAL.php?recaudo=".md5($rowU[0])."';";
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
 ?>
