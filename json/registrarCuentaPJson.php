<?php
##########MODIFICACIONES#############
#01/02/2017 | 10:30 ERICA GONZÁLEZ.//Cambiar redireccionamiento
######################################


require_once '../Conexion/conexion.php';
session_start();

$codiC  = '"'.$mysqli->real_escape_string(''.$_POST['txtCodigoC'].'').'"';
$nombre  = '"'.$mysqli->real_escape_string(''.$_POST['txtNombre'].'').'"';
$natural  = '"'.$mysqli->real_escape_string(''.$_POST['sltNaturaleza'].'').'"';
$claseC  = '"'.$mysqli->real_escape_string(''.$_POST['sltClaseC'].'').'"';
$mov  = '"'.$mysqli->real_escape_string(''.$_POST['optMov'].'').'"';
$cen  = '"'.$mysqli->real_escape_string(''.$_POST['optCentro'].'').'"';
$auxT  = '"'.$mysqli->real_escape_string(''.$_POST['optAuxT'].'').'"';
$auxP  = '"'.$mysqli->real_escape_string(''.$_POST['optAuxP'].'').'"';
$activ  = '"'.$mysqli->real_escape_string(''.$_POST['optAct'].'').'"';
$din  = '"'.$mysqli->real_escape_string(''.$_POST['txtDinamica'].'').'"';

if(($_POST['sltTipoCuentaCgn'])!='""' || empty($_POST['sltTipoCuentaCgn']) || $_POST['sltTipoCuentaCgn']=='"Tipo Cuenta CGN"'){
    $tipoCGN  = 'NULL';
}else{    
    $tipoCGN  = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoCuentaCgn'].'').'"';
}
 
if($_POST['sltPredecesor']=='""' || empty($_POST['sltPredecesor'])){
    $pre  = 'NULL';
}else{
    $pre  = '"'.$mysqli->real_escape_string(''.$_POST['sltPredecesor'].'').'"';
}
$paramA = $_SESSION['anno'];

$sql = "INSERT INTO gf_cuenta(codi_cuenta,nombre,naturaleza,clasecuenta,movimiento,centrocosto,auxiliartercero,auxiliarproyecto,activa,dinamica,parametrizacionanno,tipocuentacgn,predecesor) VALUES ($codiC,$nombre,$natural,$claseC,$mov,$cen,$auxT,$auxP,$activ,$din,$paramA,$tipoCGN,$pre)";


$rs = $mysqli->query($sql); 
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
    window.location='../buscarCuenta.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.location='../buscarCuenta.php';
  });
</script>
<?php } ?>