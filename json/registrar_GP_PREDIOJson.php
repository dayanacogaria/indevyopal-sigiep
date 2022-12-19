<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $codigoC = '"'.$mysqli->real_escape_string(''.$_POST['codigo_catastral'].'').'"';
  $nombre  = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
  $matricula  = '"'.$mysqli->real_escape_string(''.$_POST['matricula_inmobiliaria'].'').'"';
  $annio  = '"'.$mysqli->real_escape_string(''.$_POST['annio'].'').'"';
  $codigo = '"'.$mysqli->real_escape_string(''.$_POST['codigo'].'').'"';
  $codigoIG = '"'.$mysqli->real_escape_string(''.$_POST['codigoIG'].'').'"';
  $participacion = '"'.$mysqli->real_escape_string(''.$_POST['participacion'].'').'"';
  $principal = '"'.$mysqli->real_escape_string(''.$_POST['principal'].'').'"';
  $direccion= '"'.$mysqli->real_escape_string(''.$_POST['direccion'].'').'"';
  $ciudad = '"'.$mysqli->real_escape_string(''.$_POST['Ciudad'].'').'"';
  $estrato = '"'.$mysqli->real_escape_string(''.$_POST['estrato'].'').'"';
  $barrio  = '"'.$mysqli->real_escape_string(''.$_POST['barrio'].'').'"';
  $estado = '"'.$mysqli->real_escape_string(''.$_POST['estado'].'').'"';
  $ruta = '"'.$mysqli->real_escape_string(''.$_POST['ruta'].'').'"';
  $tipoP = '"'.$mysqli->real_escape_string(''.$_POST['tipoPredio'].'').'"';
  $predio = '"'.$mysqli->real_escape_string(''.$_POST['predioa'].'').'"';

  if ($nombre=='""'){
     $nombre='NULL'; 
  }
  if ($matricula=='""'){
     $matricula='NULL'; 
  }
  if ($annio=='""'){
     $annio='NULL'; 
  }
  if ($codigo=='""'){
     $codigo='NULL'; 
  }
  if ($codigoIG=='""'){
     $codigoIG='NULL'; 
  }
  if ($estrato=='""'){
     $estrato='NULL'; 
  }
  if ($estado=='""'){
     $estado='NULL'; 
  }
  if ($predio=='""'){
     $predio='NULL'; 
  }
  
 $insert = "INSERT INTO gp_predio1 (codigo_catastral, nombre, aniocreacion, matricula_inmobiliaria, "
         . " codigo_sig, codigoigac, participacion, principal, "
         . "direccion,ciudad, estrato,barrio, "
         . "estado, ruta, tipo_predio, predioaso) "
         . "VALUES($codigoC, $nombre, $annio, $matricula, "
         . " $codigo, $codigoIG, $participacion, $principal, "
         . " $direccion,$ciudad, $estrato, $barrio, "
         . "$estado, $ruta, $tipoP, $predio )";
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
                <p>No se ha podido guardar la información.</p>
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
        window.location='../LISTAR_GP_PREDIO.php';
      });
    </script>
<?php }else{ ?>
    <script type="text/javascript">
      $("#myModal2").modal('show');
      $("#ver2").click(function(){
        $("#myModal2").modal('hide');
         window.location=window.history.back(-1);
      });
    </script>
<?php } ?>