<?php
require_once '../Conexion/conexion.php';
require_once('../jsonPptal/gs_auditoria_acciones_nomina.php');
session_start();

$empleado               = '"'.$mysqli->real_escape_string(''.$_POST['sltEmpleado'].'').'"';

if($mysqli->real_escape_string(''.$_POST['sltTipo'].'')=="")
    $tiponovedad = "null";
else
    $tiponovedad            = '"'.$mysqli->real_escape_string(''.$_POST['sltTipo'].'').'"';

if($mysqli->real_escape_string(''.$_POST['txtNumeroA'].'')=="")
    $numeroacto = "null";
else
    $numeroacto             = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroA'].'').'"';

if(empty($_POST['sltFechaI'])||$mysqli->real_escape_string(''.$_POST['sltFechaI'].'')==""){
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

if(empty($_POST['sltFechaF'])||$mysqli->real_escape_string(''.$_POST['sltFechaF'].'')==""){
  $fechafin="null";
}else{
$fec2 = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaF'].'').'"';
$fecha2 = trim($fec2, '"');
$fecha_div = explode("/", $fecha2);
$anio2 = $fecha_div[2];
$mes2 = $fecha_div[1];
$dia2 = $fecha_div[0];
$fechafin = '"'.$anio2.'-'.$mes2.'-'.$dia2.'"';
}

if(empty($_POST['sltFechaID'])||$mysqli->real_escape_string(''.$_POST['sltFechaID'].'')==""){
  $fechainiciodisfrute="null";
}else{
$fec3 = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaID'].'').'"';
$fecha3 = trim($fec3, '"');
$fecha_div = explode("/", $fecha3);
$anio3 = $fecha_div[2];
$mes3 = $fecha_div[1];
$dia3 = $fecha_div[0];
$fechainiciodisfrute = '"'.$anio3.'-'.$mes3.'-'.$dia3.'"';
}

if(empty($_POST['sltFechaFD'])||$mysqli->real_escape_string(''.$_POST['sltFechaFD'].'')==""){
  $fechafindisfrute="null";
}else{
$fec4 = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaFD'].'').'"';
$fecha4 = trim($fec4, '"');
$fecha_div = explode("/", $fecha4);
$anio4 = $fecha_div[2];
$mes4 = $fecha_div[1];
$dia4 = $fecha_div[0];
$fechafindisfrute = '"'.$anio4.'-'.$mes4.'-'.$dia4.'"';
}

if(empty($_POST['sltFechaA'])||$mysqli->real_escape_string(''.$_POST['sltFechaA'].'')==""){
  $fechaacto="null";
}else{
$fec5 = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaA'].'').'"';
$fecha5 = trim($fec5, '"');
$fecha_div = explode("/", $fecha5);
$anio5 = $fecha_div[2];
$mes5 = $fecha_div[1];
$dia5 = $fecha_div[0];
$fechaacto = '"'.$anio5.'-'.$mes5.'-'.$dia5.'"';
}


$periodo  = '"'.$mysqli->real_escape_string(''.$_POST['sltPeriodo'].'').'"';
$dias = $_POST['txtDiasD'];


$sql = "INSERT INTO gn_vacaciones(empleado,fechainicio,fechafin,fechainiciodisfrute,fechafindisfrute,numeroacto,fechaacto,tiponovedad, periodo, dias_hab) VALUE ($empleado,$fechainicio,$fechafin,$fechainiciodisfrute,$fechafindisfrute,$numeroacto,$fechaacto,$tiponovedad, $periodo, $dias)";
$resultado = $mysqli->query($sql);
if ($resultado==true) {
  $sqlId="SELECT MAX(id_unico) FROM gn_vacaciones";
  $ultmId = $mysqli->query($sqlId);
  $rowId=mysqli_fetch_row($ultmId);
  $id_vacaciones=$rowId[0];
  $agr = agregarVacaciones($id_vacaciones);
}

if(empty($_POST['txtId']))
{
    $las = "SELECT MAX(id_unico) FROM gn_vacaciones";
    $resultado = $mysqli->query($las);
    $rw = mysqli_fetch_row($resultado);
    $id = $rw[0];
    #**** INSERTAR NOVEDAD 
    $cnp = "SELECT concepto FROM gn_tipo_novedad where id_unico = $tiponovedad";
    $cnp = $mysqli->query($cnp);
    $cnp = mysqli_fetch_row($cnp);
    $concepto = $cnp[0];

    $sql = "INSERT INTO gn_novedad ( valor, fecha, empleado, periodo, concepto, aplicabilidad ) 
    VALUE ($dias, '".date('Y-m-d')."',$empleado, $periodo, $concepto, 4)";
    $resultado = $mysqli->query($sql);

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
       window.location='../listar_GN_VACACIONES.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
    $("#ver2").click(function(){
    $("#myModal2").modal('hide');      
      window.location='../listar_GN_VACACIONES.php';
      //window.history.go(-2);
  });
</script>
<?php } 
?>