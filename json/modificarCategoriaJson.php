<?php
  require_once('../Conexion/conexion.php');
  require_once('../jsonPptal/gs_auditoria_acciones_nomina.php');
session_start();

//obtiene los datos que se van a modificar

$codigointerno          = '"'.$mysqli->real_escape_string(''.$_POST['txtCodigoI'].'').'"';
$nombre                 = '"'.$mysqli->real_escape_string(''.$_POST['txtNombre'].'').'"';
$salarioactual          = '"'.$mysqli->real_escape_string(''.$_POST['txtSalarioAC'].'').'"';
$salarioanterior        = '"'.$mysqli->real_escape_string(''.$_POST['txtSalarioAN'].'').'"';
$gastorepresentacion    = '"'.$mysqli->real_escape_string(''.$_POST['txtGastoR'].'').'"';
$nivel                  = $mysqli->real_escape_string(''.$_POST['sltNivel'].'');
$estadocategoria        = $mysqli->real_escape_string(''.$_POST['sltEstado'].'');
$tipoSui        = $mysqli->real_escape_string(''.$_POST['tipoSui'].'');

if ($tipoSui=="") {
  $tipoSui="";
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

$id         = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';

if($nivel=="")
    $niv = "null";
else
    $niv = $nivel;

if($estadocategoria=="")
    $estado = "null";
else
    $estado = $estadocategoria;

if($estadocategoria=="")
    $estado = "null";
else
    $estado = $estadocategoria;

//modificar ne la base de datos
$elm = modificarCategoria($_POST['id'],$_POST['txtCodigoI'],$_POST['txtNombre'],$_POST['txtSalarioAC'],$_POST['txtSalarioAN'],$_POST['txtGastoR'],$_POST['sltNivel'],$_POST['sltEstado'],$_POST['tipoSui']);

$insertSQL = "UPDATE gn_categoria SET  codigointerno=$codigointerno, nombre=$nombre, salarioactual=$salarioactual,
         salarioanterior=$salarioanterior, gastorepresentacion=$gastorepresentacion, nivel=$niv, estadocategoria=$estado, parametrizacion_anno=$parametrizacionanno,fecha_modificacion=$fechamodificacion,tipo_persona_sui='$tipoSui' WHERE id_unico = $id";
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
    window.location='../listar_GN_CATEGORIA.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>