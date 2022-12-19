<?php
require_once '../Conexion/conexion.php';
session_start();
#comprobate detalle
$fechaD = '"'.$mysqli->real_escape_string(''.$_POST['txtFecha'].'').'"';
$descripcion = '"'.$mysqli->real_escape_string(''.$_POST['txtDesc'].'').'"';
#Llenar por el usuario
$valorEjec = "0";
//txtComprobante
$comprobante = '"'.$mysqli->real_escape_string(''.$_POST['txtComprobante'].'').'"';
$cuenta = '"'.$mysqli->real_escape_string(''.$_POST['sltcuenta'].'').'"';
$detalleA = "NULL";

$sql = "SELECT naturaleza FROM gf_cuenta WHERE id_unico = $cuenta";
$rs = $mysqli->query($sql);
$nat = mysqli_fetch_row($rs);
$natural = $nat[0];
if (empty($_POST['txtValorD'])) {
    if ($_POST['txtValorC'] != '""') {
        if ($nat[0] == 1) {
            $valor ='"'.$mysqli->real_escape_string('-'.$_POST['txtValorC'].'').'"';
        }else{
            $valor ='"'.$mysqli->real_escape_string(''.$_POST['txtValorC'].'').'"';
        }
        
    }
}
if (empty($_POST['txtValorC'])) {
    if($_POST['txtValorD'] != '""'){
        if ($nat[0]==2) {
            $valor =  '"'.$mysqli->real_escape_string('-'.$_POST['txtValorD'].'').'"';
        }else{
            $valor =  '"'.$mysqli->real_escape_string(''.$_POST['txtValorD'].'').'"';
        }        
    }
}
if(empty($_POST['slttercero'])){
    $tercero = '"2"';
}else{
    $tercero = '"'.$mysqli->real_escape_string(''.$_POST['slttercero'].'').'"';
}
if(empty($_POST['sltproyecto'])){
    if(!empty($_SESSION['proyecto'])){
        $proyecto = $_SESSION['proyecto'];
    }  else {
        $proyecto = '"2147483647"';
    }
}else{
    $proyecto = '"'.$mysqli->real_escape_string(''.$_POST['sltproyecto'].'').'"';
}
if(empty($_POST['sltcentroc'])){
    if(!empty($_SESSION['centrocosto'])){
        $centroCosto = $_SESSION['centrocosto'];
    }else{
        $centroCosto = "12";
    }
}else{
    $centroCosto = '"'.$mysqli->real_escape_string(''.$_POST['sltcentroc'].'').'"';
}
$sqli = "INSERT INTO gf_detalle_comprobante(fecha,descripcion,valor,valorejecucion,comprobante,cuenta,naturaleza,tercero,proyecto,centrocosto,detalleafectado) 
        VALUES ('$fechaD','$descripcion',$valor,$valorEjec,$comprobante,$cuenta,$natural,$tercero,$proyecto,$centroCosto,$detalleA)";
$res = $mysqli->query($sqli);
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
  <!--Modal para informar al usuario que no se ha podido registrar la informacion-->
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
<!--Links para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->
<?php if($rs==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location = "../registrar_GF_COMPROBANTE_CONTABLE.php";
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  window.history.go(-1);
</script>
<?php } ?>