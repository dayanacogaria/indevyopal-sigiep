<?php

################ MODIFICACIONES ####################
#12/06/2017 | Anderson Alarcon | cambie el insert de resolucion 
####################################################

require_once '../Conexion/conexion.php';
session_start();

$numero = '"'.$mysqli->real_escape_string(''.$_POST['txtNumero'].'').'"';
$fecha  = '"'.$mysqli->real_escape_string(''.$_POST['sltFecha'].'').'"';

#$date=date_create($fecha);
#$fec = date_format($date,"Y/m/d");

if($_POST['txtNombre']=="")
    $nom = 'null';
else
    $nom = '"'.$mysqli->real_escape_string(''.$_POST['txtNombre'].'').'"';

if($_POST['txtRuta']=="")
    $rut = 'null';
else
    $rut = '"'.$mysqli->real_escape_string(''.$_POST['txtRuta'].'').'"';

if($_POST['txtObservaciones']=="")
    $obs = 'null';
else
    $obs ='"'.$mysqli->real_escape_string(''.$_POST['txtObservaciones'].'').'"';

if($_POST['sltEstado']=="")
    $est = 'null';
else
    $est = $_POST['sltEstado'];

#Conversión Fecha para Cabecera
$fecha1 = $fecha;
$fecha1 = trim($fecha1,'"');
$fecha_div = explode("/", $fecha1);
$anio1 = $fecha_div[0];
$mes1 = $fecha_div[1];
$dia1 = $fecha_div[2];
$fecha1 = '"'.$dia1.'-'.$mes1.'-'.$anio1.'"';

 $sql = "INSERT INTO gr_resolucion(nombre,numero,fecha,ruta,observaciones,estado)
                    VALUES   ($nom,$numero,$fecha1,$rut,$obs,$est)";
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
    window.location='../listar_GR_RESOLUCION.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>