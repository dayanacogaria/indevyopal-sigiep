<?php
#15/03/2017 --- Nestor B --- se agregó la validacion de las fechas para que no gener error si las fechas son vacías y la ruta del botón aceptar para que regrese a donde fue llamado
#16/03/2017 --- Nestor B --- se modficaron las validaciones de tipo vinculación causa retiro y vinculación para que no genere error cuando sean vacías
  require_once('../Conexion/conexion.php');
session_start();

//obtiene los datos que se van a modificar

$empleado          = '"'.$mysqli->real_escape_string(''.$_POST['sltEmpleado'].'').'"';
$numeroacto        = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroA'].'').'"';
$fechaacto         = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaA'].'').'"';
$fecha             = '"'.$mysqli->real_escape_string(''.$_POST['sltFecha'].'').'"';
#$tipovinculacion   = $mysqli->real_escape_string(''.$_POST['sltTipo'].'');
$estado            = $mysqli->real_escape_string(''.$_POST['sltEstado'].'');
#$causaretiro       = $mysqli->real_escape_string(''.$_POST['sltCausa'].'');
#$vinculacionretiro = $mysqli->real_escape_string(''.$_POST['sltVinculacion'].'');
$id                 = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';

/*if($tipovinculacion == "")
    $tipo="null";
else
    $tipo=$tipovinculacion;*/

if($estado == "")
    $est="null";
else
    $est=$estado;

/*if($causaretiro == "")
    $causa="null";
else
    $causa=$causaretiro;

if($vinculacionretiro == "")
    $vinc="null";
else
    $vinc=$vinculacionretiro;*/

#valida si la fecha de acto es vacía
if($mysqli->real_escape_string(''.$_POST['sltFechaA'].'')==""){
  $fechaacto = "null";
}else{    
$fec1 = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaA'].'').'"';
$fecha1 = trim($fec1, '"');
$fecha_div = explode("/", $fecha1);
$anio1 = $fecha_div[2];
$mes1 = $fecha_div[1];
$dia1 = $fecha_div[0];
$fechaacto = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';
}

#valida si la fecha  es vacía
if($mysqli->real_escape_string(''.$_POST['sltFecha'].'')==""){
  $fecha = "null";
}else{    
$fec1 = '"'.$mysqli->real_escape_string(''.$_POST['sltFecha'].'').'"';
$fecha1 = trim($fec1, '"');
$fecha_div = explode("/", $fecha1);
$anio1 = $fecha_div[2];
$mes1 = $fecha_div[1];
$dia1 = $fecha_div[0];
$fecha = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';
}

if(empty($_POST['sltTipo']) )
    $tipo = "null";
else
    $tipo = '"'.$mysqli->real_escape_string(''.$_POST['sltTipo'].'').'"';

if(empty($_POST['sltCausa']) )
    $causa = "null";
else
    $causa = '"'.$mysqli->real_escape_string(''.$_POST['sltCausa'].'').'"';

if(empty($_POST['sltVinculacion']) )
    $vinc = "null";
else
    $vinc = '"'.$mysqli->real_escape_string(''.$_POST['sltVinculacion'].'').'"';
   
//modificar ne la base de datos
$insertSQL = "UPDATE gn_vinculacion_retiro SET empleado=$empleado, numeroacto=$numeroacto, fechaacto=$fechaacto,
                        fecha=$fecha, tipovinculacion=$tipo, estado=$est, causaretiro=$causa,
                        vinculacionretiro=$vinc WHERE id_unico = $id";
  $resultado = $mysqli->query($insertSQL);
?>

<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/style.css">
 <script src="../js/md5.pack.js"></script>
 <script src="../js/jquery.min.js"></script>
 <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
 <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
</head>
<body>
</body>
</html>
<!--Modal para informar al usuario que se ha modificado-->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <!--Modal para informar al usuario que no se ha podido modificar la información-->
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido modificar la información.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--Links para dar estilos a la página-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Vuelve a carga la página de listar mostrando la informacion modificada-->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.history.go(-2);
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.history.go(-2);
  });
</script>
<?php } ?>