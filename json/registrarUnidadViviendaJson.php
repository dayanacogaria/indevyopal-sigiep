<?php
require_once '../Conexion/conexion.php';
session_start();
$predio       = '"'.$mysqli->real_escape_string(''.$_POST['sltPredio'].'').'"';
$tipo_unidad  = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoUnidad'].'').'"';
$tercero      = '"'.$mysqli->real_escape_string(''.$_POST['tercero'].'').'"';
$uso          = '"'.$mysqli->real_escape_string(''.$_POST['sltUso'].'').'"';
$estrato      = '"'.$mysqli->real_escape_string(''.$_POST['sltEstrato'].'').'"';
$nro_familias = '"'.$mysqli->real_escape_string(''.$_POST['nro_familias'].'').'"';
$nro_personas = '"'.$mysqli->real_escape_string(''.$_POST['nro_personas'].'').'"';
$codRuta      = '"'.$mysqli->real_escape_string(''.$_POST['codRuta'].'').'"';
$codInterno   = '"'.$mysqli->real_escape_string(''.$_POST['codInterno'].'').'"';
$productor    = '"'.$mysqli->real_escape_string(''.$_POST['tipoProd'].'').'"';
$sector       = '"'.$mysqli->real_escape_string(''.$_POST['sector'].'').'"';
$seccion      = '"'.$mysqli->real_escape_string(''.$_POST['seccion'].'').'"';
$manzana      = '"'.$mysqli->real_escape_string(''.$_POST['manzana'].'').'"';
$ladoMan      = '"'.$mysqli->real_escape_string(''.$_POST['ladoM'].'').'"';
$sectorH      = '"'.$mysqli->real_escape_string(''.$_POST['sectorH'].'').'"';
$microS       = '"'.$mysqli->real_escape_string(''.$_POST['microS'].'').'"';
$desh         = '"'.$mysqli->real_escape_string(''.$_POST['desha'].'').'"';



$sql = "INSERT INTO gp_unidad_vivienda( predio,
                                        tipo_unidad,
                                        tercero, 
                                        uso, 
                                        estrato, 
                                        numero_familias, 
                                        numero_personas, 
                                        codigo_ruta,
                                        codigo_interno, 
                                        tipo_productor, 
                                        sector, 
                                        seccion, 
                                        manzana, 
                                        lado_manzana, 
                                        sector_hidraulico, 
                                        microsector, 
                                        deshabilitado) 
                                        VALUES
                                       ($predio,
                                        $tipo_unidad,
                                        $tercero, 
                                        $uso, 
                                        $estrato, 
                                        $nro_familias, 
                                        $nro_personas,
                                        $codRuta,
                                        $codInterno,
                                        $productor,
                                        $sector,
                                        $seccion,
                                        $manzana,
                                        $ladoMan,
                                        $sectorH, 
                                        $microS, 
                                        $desh) ";
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
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información guardada correctamente</p>
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
    window.location='../listar_GP_UNIDAD_VIVIENDA.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.location='../listar_GP_UNIDAD_VIVIENDA.php';
  });
</script>
<?php } ?>