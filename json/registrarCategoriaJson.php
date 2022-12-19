<?php
#07/03/2017 --- Nestor B --- se modificó la ruta del botón acepatar para que regrese a donde fue llamado
require_once '../Conexion/conexion.php';
require_once('../jsonPptal/gs_auditoria_acciones_nomina.php');
session_start();

$codigointerno          = '"'.$mysqli->real_escape_string(''.$_POST['txtCodigoI'].'').'"';
$nombre                 = '"'.$mysqli->real_escape_string(''.$_POST['txtNombre'].'').'"';
$salarioactual          = '"'.$mysqli->real_escape_string(''.$_POST['txtSalarioAC'].'').'"';
$salarioanterior        = '"'.$mysqli->real_escape_string(''.$_POST['txtSalarioAN'].'').'"';
$gastorepresentacion    = '"'.$mysqli->real_escape_string(''.$_POST['txtGastoR'].'').'"';
$nivel                  = $mysqli->real_escape_string(''.$_POST['sltNivel'].'');
$estadocategoria        = $mysqli->real_escape_string(''.$_POST['sltEstado'].'');
$tipoSui        = $mysqli->real_escape_string(''.$_POST['tipoSui'].'');

if ($tipoSui=="") {
  $tipoSui="null";
}else{
  $tipoSui        = $mysqli->real_escape_string(''.$_POST['tipoSui'].'');
}
if($mysqli->real_escape_string(''.$_POST['sltFechaM'].'')==""){
  $fechamodificacion = "null";
}   
else{
    $fechamodificacion  = $mysqli->real_escape_string(''.$_POST['sltFechaM'].'');     
    $fecha_div = explode("/", $fechamodificacion);
    $anioi = $fecha_div[2];
    $mesi  = $fecha_div[1];
    $diai  = $fecha_div[0];
    $fechamodificacion = '"'.$anioi.'-'.$mesi.'-'.$diai.'"';
}
$parametrizacionanno    = '"'.$_SESSION['anno'].'"';

if($nivel=="")
    $niv = "null";
else
    $niv = $nivel;

if($estadocategoria=="")
    $estado = "null";
else
    $estado = $estadocategoria;


$sql = "INSERT INTO gn_categoria (codigointerno,nombre,salarioactual,salarioanterior,gastorepresentacion,nivel,estadocategoria,parametrizacion_anno,fecha_modificacion,tipo_persona_sui) VALUES
                          ($codigointerno,$nombre,$salarioactual,$salarioanterior,$gastorepresentacion,$niv,$estado,$parametrizacionanno,$fechamodificacion,'$tipoSui')";
$resultado = $mysqli->query($sql);
if ($resultado==true) {
  $sqlId="SELECT MAX(id_unico) FROM gn_categoria";
  $ultmId = $mysqli->query($sqlId);
  $rowId=mysqli_fetch_row($ultmId);
  $id_cat=$rowId[0];
  $agr = agregarCategoria($id_cat);
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
    window.history.go(-2);
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>