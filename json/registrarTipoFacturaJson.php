<?php
require_once '../Conexion/conexion.php'; 
session_start();
$compania   = $_SESSION['compania'];
$nombre     = $_POST['nombre'];
if(empty($_POST['prefijo'])){
    $prefijo    = 'NULL';
} else {
    $prefijo    = $_POST['prefijo'];
}
if(empty($_POST['sltClase'])){
    $clase    = 'NULL';
} else {
    $clase    = $_POST['sltClase'];
}
if(!empty($_POST['tipoC'])){
  $tipoC= $_POST['tipoC'];  
}else{
  $tipoC= 'NULL';
}
if(!empty($_POST['tipoR'])){
  $tipoR= $_POST['tipoR'];  
}else{
  $tipoR= 'NULL';
}

if(!empty($_POST['sltMov'])){
  $tipoM = $_POST['sltMov'];  
}else{
  $tipoM = 'NULL';
}
$cons       = $_POST['consecutivo'];
$serv       = $_POST['serv'];

if(!empty($_POST['optXDescuento'])){
  $desc = $_POST['optXDescuento'];  
}else{
  $desc = 'NULL';
}
$automatico = $_POST['optAutomatico'];
if(!empty($_POST['sltTipoCambio'])){
  $tipo_c = $_POST['sltTipoCambio'];  
}else{
  $tipo_c = 'NULL';
}

$optfe    = $_POST['optfe'];


$sql = "INSERT INTO gp_tipo_factura 
    (nombre,prefijo,
    clase_factura, tipo_comprobante, 
    tipo_recaudo, tipo_movimiento, 
    sigue_consecutivo, servicio, 
    xDescuento, automatico, 
    tipo_cambio, compania, facturacion_e)  
VALUES ('$nombre', '$prefijo',
    $clase, $tipoC, 
    $tipoR, $tipoM, 
    $cons,   $serv,    
    $desc, $automatico, 
    $tipo_c, $compania,$optfe )";

$resultado = $mysqli->query($sql); 
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
    window.location='../listar_GP_TIPO_FACTURA.php';
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