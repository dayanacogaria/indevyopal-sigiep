<?php
require_once '../Conexion/conexion.php';
session_start();

$titulo                 = '"'.$mysqli->real_escape_string(''.$_POST['txtTitulo'].'').'"';
$empleado               = '"'.$mysqli->real_escape_string(''.$_POST['sltEmpleado'].'').'"';
$tipo                   = $mysqli->real_escape_string(''.$_POST['sltTipo'].'');
$institucioneducativa   = $mysqli->real_escape_string(''.$_POST['sltInstE'].'');


if($mysqli->real_escape_string(''.$_POST['sltFechaT'].'')=="")
    $fechaterminacion = "null";
else
{    
    $fec1 = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaT'].'').'"';
    $fecha1 = trim($fec1, '"');
    $fecha_div = explode("/", $fecha1);
    $anio1 = $fecha_div[2];
    $mes1 = $fecha_div[1];
    $dia1 = $fecha_div[0];
    $fechaterminacion = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';    
}
if($mysqli->real_escape_string(''.$_POST['txtNumeroS'].'')=="")
    $numerosemestres = "null";
else
    $numerosemestres        = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroS'].'').'"';
if($mysqli->real_escape_string(''.$_POST['es_graduado'].'')=="")
    $graduado = "null";
else
    $graduado               = '"'.$mysqli->real_escape_string(''.$_POST['es_graduado'].'').'"';
if($mysqli->real_escape_string(''.$_POST['txtTarjetaP'].'')=="")
    $tarjetaprofesional = "null";
else
    $tarjetaprofesional     = '"'.$mysqli->real_escape_string(''.$_POST['txtTarjetaP'].'').'"';

$sql = "INSERT INTO gn_estudio(titulo,fechaterminacion,numerosemestres,graduado,tarjetaprofesional,empleado,tipo,institucioneducativa) VALUES ($titulo,$fechaterminacion,$numerosemestres,$graduado,$tarjetaprofesional,$empleado,$tipo,$institucioneducativa)";
$resultado = $mysqli->query($sql);
if(empty($_POST['txtId']))
{
    $las = "SELECT MAX(id_unico) FROM gn_estudio";
    $resultado = $mysqli->query($las);
    $rw = mysqli_fetch_row($resultado);
    $id = $rw[0];
}else{
    $id = '"'.$mysqli->real_escape_string(''.$_POST['txtId'].'').'"';    
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
<!--Modal para informar al usuario que se ha registrado-->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información guardada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <!--Modal para informar al usuario que no se ha podido registrar -->
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido guardar la información.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--lnks para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');      
        window.location='../registrar_GN_ESTUDIO.php?idE=<?php echo md5($_POST['sltEmpleado'])?>';
      //window.history.go(-1);
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
    $("#ver2").click(function(){
    $("#myModal2").modal('hide');      
        //window.location='../registrar_GN_ACCIDENTE.php?id=<?php echo md5($id);?>';
      window.history.go(-1);
  });
</script>
<?php } 
?>