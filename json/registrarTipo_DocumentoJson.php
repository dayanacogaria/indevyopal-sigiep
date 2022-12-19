<?php 
  require_once('../Conexion/conexion.php');
session_start();
$compania       = $_SESSION['compania'];
$nombre         = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
$formato        = $mysqli->real_escape_string(''.$_POST['formato'].'');
$obligatorio    = $mysqli->real_escape_string(''.$_POST['obligatorio'].'');
$consecutivo    = $mysqli->real_escape_string(''.$_POST['consecutivo'].'');
$clase          = $mysqli->real_escape_string(''.$_POST['clase'].'');
$dependencia    = $mysqli->real_escape_string(''.$_POST['dependencia'].'');
if ($formato=='""'|| $formato=='' || $formato==NULL){
     
      $formato='NULL'; 
  }
if ($clase=='""'|| $clase=='' || $clase==NULL){
     
      $clase='NULL'; 
  }
if ($dependencia=='""'|| $dependencia=='' || $dependencia==NULL){
     
      $dependencia='NULL'; 
  }
  $insert = "INSERT INTO gf_tipo_documento (nombre,es_obligatorio, "
          . "consecutivo_unico, formato, clase_informe, dependencia, compania) "
          . "VALUES($nombre, $obligatorio, $consecutivo, $formato, $clase, $dependencia,$compania)";
  $resultado = $mysqli->query($insert);
   
  
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
</html>
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
<div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p><?php
                    echo "No se ha podido guardar la informaci&oacuten.";
                  ?>
                  </p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="../js/menu.js"></script>
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>

<?php if($resultado==true){ ?>
    <script type="text/javascript">
      $("#myModal1").modal('show');
      $("#ver1").click(function(){
        $("#myModal1").modal('hide');
        window.location='../listar_GF_TIPO_DOCUMENTO.php';
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