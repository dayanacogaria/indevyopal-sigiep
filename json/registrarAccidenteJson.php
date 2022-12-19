<?php
require_once '../Conexion/conexion.php';
session_start();

$empleado            = '"'.$mysqli->real_escape_string(''.$_POST['sltEmpleado'].'').'"';

if($mysqli->real_escape_string(''.$_POST['txtLugar'].'')=="")
    $lugaraccidente  = "null";
else
    $lugaraccidente  = '"'.$mysqli->real_escape_string(''.$_POST['txtLugar'].'').'"';

if($mysqli->real_escape_string(''.$_POST['txtDiagnostico'].'')=="")
    $diagnostico     = "null";
else
    $diagnostico     = '"'.$mysqli->real_escape_string(''.$_POST['txtDiagnostico'].'').'"';

if($mysqli->real_escape_string(''.$_POST['txtNumeroR'].'')=="")
    $numradicado     = "null";
else
    $numradicado     = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroR'].'').'"';

if($mysqli->real_escape_string(''.$_POST['sltFechaR'].'')=="")
    $fechareporte    = "null";
else
{
    $fechaR = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaR'].'').'"';
    $fecha2 = trim($fechaR, '"');
    $fecha_div = explode("/", $fecha2);
    $anio2 = $fecha_div[2];
    $mes2 = $fecha_div[1];
    $dia2 = $fecha_div[0];
    $fechareporte = '"'.$anio2.'-'.$mes2.'-'.$dia2.'"'; 
}

if($mysqli->real_escape_string(''.$_POST['txtDescripcion'].'')=="")
    $descripcion     = "null";
else
    $descripcion     = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';

if($mysqli->real_escape_string(''.$_POST['txtRuta'].'')=="")
    $rutareporte     = "null";
else
    $rutareporte     = '"'.$mysqli->real_escape_string(''.$_POST['txtRuta'].'').'"';

if($mysqli->real_escape_string(''.$_POST['sltTipo'].'')=="")
    $tipoaccidente   = "null";
else
    $tipoaccidente   = '"'.$mysqli->real_escape_string(''.$_POST['sltTipo'].'').'"';

if($mysqli->real_escape_string(''.$_POST['sltEstado'].'')=="")
    $estado          = "null";
else
    $estado          = $mysqli->real_escape_string(''.$_POST['sltEstado'].'');

$sql = "INSERT INTO gn_accidente(empleado,lugaraccidente,diagnostico,numradicado,fechareporte,descripcion,rutareporte,tipoaccidente,estado) VALUES ($empleado,$lugaraccidente,$diagnostico,$numradicado,$fechareporte,$descripcion,$rutareporte,$tipoaccidente,$estado)";
$resultado = $mysqli->query($sql);

if(empty($_POST['txtId']))
{
    $las = "SELECT MAX(id_unico) FROM gn_accidente";
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
        window.location='../registrar_GN_ACCIDENTE.php?idE=<?php echo md5($_POST['sltEmpleado'])?>';
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