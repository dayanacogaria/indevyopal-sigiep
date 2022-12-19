<?php
require_once('../Conexion/conexion.php');
session_start();

$codigointerno       = '"'.$mysqli->real_escape_string(''.$_POST['txtCodigoI'   ].'').'"';
$acumulable          =     $mysqli->real_escape_string(''.$_POST['es_acumulable'].'');
$estado              =     $mysqli->real_escape_string(''.$_POST['sltEstado'    ].'');
$tipoprocesonomina   =     $mysqli->real_escape_string(''.$_POST['sltTipoPN'    ].'');
$id                  = '"'.$mysqli->real_escape_string(''.$_POST['id'           ].'').'"';
$parametrizacionanno = '"'.$_SESSION['anno'].'"';
$diasnom             =''.$mysqli->real_escape_string(''.$_POST['dialiq'].'');
$periodoRetro        =     $mysqli->real_escape_string(''.$_POST['sltPeriodoR'].'');
if(empty($acumulable))
    $acum = "null";
else
    $acum = $acumulable;

if(empty($estado))
    $est = "null";
else
    $est = $estado;

if(empty($tipoprocesonomina))
    $tipo = "null";
else
    $tipo = $tipoprocesonomina;

if(empty($periodoRetro))
    $periodoRetro = "null";
else
    $periodoRetro = $periodoRetro;


#valida si la fecha inicial es vacía
if($mysqli->real_escape_string(''.$_POST['sltFechaI'].'')==""){
  $fechainicio='null';
}else{
  $fec1      = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaI'].'').'"';
  $fecha1    = trim($fec1,'"');
  $fecha_div = explode("/", $fecha1);
  $anio1 = $fecha_div[2];
  $mes1 = $fecha_div[1];
  $dia1 = $fecha_div[0];  
  $fechainicio = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';  
 
}

#valida si la fehca final es vacía
if($mysqli->real_escape_string(''.$_POST['sltFechaF'].'')==""){
  $fechafin='null';
}else{
  $fec2     = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaF'].'').'"';
  $fecha2    = trim($fec2,'"');
  $fecha_div = explode("/", $fecha2);
  $anio1 = $fecha_div[2];
  $mes1 = $fecha_div[1];
  $dia1 = $fecha_div[0];  
  $fechafin = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';  
 
}
   
//modificar ne la base de datos
$insertSQL = "UPDATE gn_periodo SET codigointerno=$codigointerno, fechainicio=$fechainicio, fechafin=$fechafin, "
         . "acumulable=$acum,estado=$est, tipoprocesonomina=$tipo, parametrizacionanno=$parametrizacionanno, dias_nomina = $diasnom, 
         periodo_retro = $periodoRetro 
          WHERE id_unico = $id";
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
    window.location='../listar_GN_PERIODO.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>