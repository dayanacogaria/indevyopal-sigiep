<?php
################ MODIFICACIONES ####################
#05/06/2017  | Anderson Alarcon | cambie update de detalle alivio 
############################################

require_once('../Conexion/conexion.php');
session_start();

//obtiene los datos que se van a modificar

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
$id           = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';

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

 $insertSQL = "UPDATE gr_detalle_alivio SET fechainicial=$fecha1,fechafinal=$fecha2,anioinicial=$anioI,aniofinal=$anioF,poralivionormal=$poranor,poraliviofinanciero=$porafin,abono=$abono,pagototal=$pagototal,impcapital=$impcapital,impinteres=$impinteres,sobrecapital=$sobrecapital,sobreinteres=$sobreinteres,todocapital=$todocapital,todointeres=$todointeres,alivio=$alivio,tipopredio=$tipo WHERE id_unico = $id";

//modificar ne la base de datos  
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
    window.location='../listar_GR_DETALLE_ALIVIO.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>