<?php
####################################################################################
#
#Creado por: Nestor B |18/10/2017|
#
####################################################################################

require_once '../Conexion/conexion.php';
session_start();

$_SESSION['usuario'] = $user;

if(!empty($_POST['sltSede'])){
    $sede[0] = '"'.$mysqli->real_escape_string(''.$_POST['sltSede'].'').'"';
}else{
    $res = "SELECT t.id_unico FROM gf_tercero t LEFT JOIN gs_usuario u ON u.tercero = t.id_unico WHERE u.usuario = '$user'";
    $respo = $mysqli->query($res);
    $RE = mysqli_fetch_row($respo);

    $sed = "SELECT sede FROM ge_tercero_sede WHERE tercero = '$RE[0]'";
    $se = $mysqli->query($sed);
    $sede = mysqli_fetch_row($se);
}

$votante = '"'.$mysqli->real_escape_string(''.$_POST['sltVotante'].'').'"';
$tipo = '"'.$mysqli->real_escape_string(''.$_POST['sltTipo'].'').'"';

if(!empty($_POST['sltTer'])){
    $ter = '"'.$mysqli->real_escape_string(''.$_POST['sltTer'].'').'"';
}else{
    $ter = "null";
}

$sql = "INSERT INTO ge_tercero_sede(tercero, sede, tipo_relacion,tercero_rel) VALUES ($votante,$sede[0],$tipo,$ter)";
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
    window.history.go(-2);
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>

