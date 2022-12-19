<?php

require_once('../Conexion/conexion.php');
session_start();

//obtiene los datos que se van a modificar

$codigo             = '"'.$mysqli->real_escape_string(''.$_POST['txtCodigo'].'').'"';
$compania           = $mysqli->real_escape_string(''.$_POST['idcom'].'');
$descripcion        = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';
$tipoafilaicion     = $mysqli->real_escape_string(''.$_POST['sltTipoF'].'');
$unidadmedida       = $mysqli->real_escape_string(''.$_POST['sltUnidad'].'');
$clase              = $mysqli->real_escape_string(''.$_POST['sltClase'].'');
$codigocgr          = $mysqli->real_escape_string(''.$_POST['sltCCGR'].'');
$tipoentidadcredito = $mysqli->real_escape_string(''.$_POST['sltEntidadC'].'');
$codigodian         = $mysqli->real_escape_string(''.$_POST['sltCCD'].'');
$conceptorel        = $mysqli->real_escape_string(''.$_POST['sltConcepto'].'');
$id                 = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
$interfaz           = $mysqli->real_escape_string(''.$_POST['interfaz'].'');
$ibc                = $mysqli->real_escape_string(''.$_POST['es_acumulable'].'');
$lf                 = $mysqli->real_escape_string(''.$_POST['acumulable_lf'].'');
$ibr                = $mysqli->real_escape_string(''.$_POST['acumulable_ibr'].'');
$retro              = $mysqli->real_escape_string(''.$_POST['liquida_retroactivo'].'');
$codigoE            = $mysqli->real_escape_string(''.$_POST['nominaE'].'');
$tipoNE             = $mysqli->real_escape_string(''.$_POST['tipoNE'].'');
$equivalenteSUI     = $mysqli->real_escape_string(''.$_POST['equivalenteSui'].'');
$equivalentePer     = $mysqli->real_escape_string(''.$_POST['equivalentePer'].'');



if($tipoafilaicion  =="")
    $tipo = "null";
else
    $tipo = $tipoafilaicion;

if($unidadmedida=="")
    $unidad = "null";
else
    $unidad = $unidadmedida;

if($clase=="")
    $cla = "null";
else
    $cla = $clase;

if($codigocgr=="")
    $cgr = "null";
else
    $cgr = $codigocgr;

if($tipoentidadcredito=="")
    $entidad = "null";
else
    $entidad = $tipoentidadcredito;

if($codigodian=="")
    $dian = "null";
else
    $dian = $codigodian;

if($conceptorel=="")
    $concepto = "null";
else
    $concepto = $conceptorel;
 

if($interfaz=="")
    $interfaz = "null";
else
    $interfaz = $interfaz;

    if($tipoNE=="")
    $tipoNE = "null";
else
  $tipoNE = $tipoNE;

  if($equivalenteSUI=="")
  $equivalenteSUI = "";
else
  $equivalenteSUI = $equivalenteSUI;  

  if($equivalentePer=="")
  $equivalentePer = "";
else
  $equivalentePer = $equivalentePer;  

//modificar ne la base de datos
  $insertSQL = "UPDATE gn_concepto SET  codigo=$codigo, descripcion=$descripcion, tipofondo=$tipo, unidadmedida=$unidad, "
          . "clase=$cla, codigocgr=$cgr, tipoentidadcredito=$entidad, conceptorel=$concepto, codigodian=$dian , tipo_interfaz =$interfaz, acum_ibc = $ibc, 
          aplica_liquidacion_final=  $lf, ibr = $ibr, liquida_retroactivo = $retro,equivalente_NE = '$codigoE', tipo_novedad_nomina= $tipoNE, equivalante_sui= '$equivalenteSUI',equivalente_personal_cos='$equivalentePer'"
          . "WHERE md5(id_unico) = $id";
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
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Vuelve a carga la página de listar mostrando la informacion modificada-->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../listar_GN_CONCEPTO.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>