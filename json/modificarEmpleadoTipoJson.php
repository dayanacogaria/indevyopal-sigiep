<?php
#14/03/2017 --- Nestor B --- se agregó la validacion de las fechas para que no sea posible que la fecha final sea menor que la inicial y se agregron la validaciones por si las fechas son vacías
  require_once('../Conexion/conexion.php');
session_start();

//obtiene los datos que se van a modificar

$empleado           = '"'.$mysqli->real_escape_string(''.$_POST['sltEmpleado'].'').'"';
$fechainicio        = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaI'].'').'"';
$fechacancelacion   = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaC'].'').'"';
$observaciones      = '"'.$mysqli->real_escape_string(''.$_POST['txtObservaciones'].'').'"';
$tipo               = '"'.$mysqli->real_escape_string(''.$_POST['sltTipo'].'').'"';
$estado             = '"'.$mysqli->real_escape_string(''.$_POST['sltEstado'].'').'"';
$id             = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
  
if($tipo=="")
    $tip = "null";
else
    $tip = $tipo;

if($estado=="")
    $est = "null";
else
    $est = $estado;

#valida si la fecha de inicio es vacía  
if($mysqli->real_escape_string(''.$_POST['sltFechaI'].'')==""){
  $fechainicio="null";
}else{
$fec1 = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaI'].'').'"';
$fecha1 = trim($fec1, '"');
$fecha_div = explode("/", $fecha1);
$anio1 = $fecha_div[2];
$mes1 = $fecha_div[1];
$dia1 = $fecha_div[0];
$fechainicio = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';
}

#valida si la fecha de cancelación es vacía
if($mysqli->real_escape_string(''.$_POST['sltFechaC'].'')==""){
  $fechacancelacion="null";
}else{
$fec1 = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaC'].'').'"';
$fecha1 = trim($fec1, '"');
$fecha_div = explode("/", $fecha1);
$anio1 = $fecha_div[2];
$mes1 = $fecha_div[1];
$dia1 = $fecha_div[0];
$fechacancelacion = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';
}

if($fechainicio<=$fechacancelacion){
//modificar ne la base de datos
 $insertSQL = "UPDATE gn_empleado_tipo SET empleado = $empleado,fechainicio = $fechainicio,fechacancelacion = $fechacancelacion, observaciones = $observaciones, tipo = $tip,estado = $est WHERE id_unico = $id";
  $resultado = $mysqli->query($insertSQL);
}else{
  $resultado=false;
  $x=1;
}
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
        <?php if($x==1){ ?>
          <p>La fecha de inicio es menor que la fecha de cancelación</p>
         <?php }else{ ?>         
          <p>No se ha podido modificar la información.</p>
         <?php 
        }
        ?>
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
    window.history.go(-1);
  });
</script>
<?php } ?>