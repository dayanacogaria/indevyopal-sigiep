<?php

/* 
 * ************
 * ***Autor*****
 * **DANIEL.NC***
 * ***************
 */

require_once '../Conexion/conexion.php';
session_start();

$fechaI       = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaI'].'').'"';
$fechaF       = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaF'].'').'"';
$anioI        = '"'.$mysqli->real_escape_string(''.$_POST['txtAnioI' ].'').'"';
$anioF        = '"'.$mysqli->real_escape_string(''.$_POST['txtAnioF' ].'').'"';
$poranor      = '"'.$mysqli->real_escape_string(''.$_POST['txtPoran' ].'').'"';
$porafin      = '"'.$mysqli->real_escape_string(''.$_POST['txtPoraf' ].'').'"';
$abono        = '"'.$mysqli->real_escape_string(''.$_POST['sltAbono' ].'').'"';
$pagototal    = '"'.$mysqli->real_escape_string(''.$_POST['sltPago'  ].'').'"';
$impcapital   = '"'.$mysqli->real_escape_string(''.$_POST['sltImpcap'].'').'"';
$impinteres   = '"'.$mysqli->real_escape_string(''.$_POST['sltImpint'].'').'"';
$sobrecapital = '"'.$mysqli->real_escape_string(''.$_POST['sltSobcap'].'').'"';
$sobreinteres = '"'.$mysqli->real_escape_string(''.$_POST['sltSobint'].'').'"';
$todocapital  = '"'.$mysqli->real_escape_string(''.$_POST['sltTodcap'].'').'"';
$todointeres  = '"'.$mysqli->real_escape_string(''.$_POST['sltTodint'].'').'"';
$alivio       = '"'.$mysqli->real_escape_string(''.$_POST['sltAlivio'].'').'"';
$tipo         = '"'.$mysqli->real_escape_string(''.$_POST['sltTipo'  ].'').'"';

$fecha1 = $fechaI;
$fecha1 = trim($fecha1,'"');
$fecha_div = explode("/", $fecha1);
$anio1 = $fecha_div[0];
$mes1 = $fecha_div[1];
$dia1 = $fecha_div[2];
$fecha1 = '"'.$dia1.'-'.$mes1.'-'.$anio1.'"';

$fecha2 = $fechaF;
$fecha2 = trim($fecha2,'"');
$fecha_div = explode("/", $fecha2);
$anio2 = $fecha_div[0];
$mes2 = $fecha_div[1];
$dia2 = $fecha_div[2];
$fecha2 = '"'.$dia2.'-'.$mes2.'-'.$anio2.'"';

 $sql = "INSERT INTO gr_detalle_alivio(fechainicial,fechafinal,anioinicial,aniofinal,poralivionormal,poraliviofinanciero,abono,pagototal,impcapital,impinteres,sobrecapital,sobreinteres,todocapital,todointeres,alivio,tipopredio)
                    VALUES   ($fecha1,$fecha2,$anioI,$anioF,$poranor,$porafin,$abono,$pagototal,$impcapital,$impinteres,$sobrecapital,$sobreinteres,$todocapital,$todointeres,$alivio,$tipo)";

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
    window.location='../listar_GR_DETALLE_ALIVIO.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>