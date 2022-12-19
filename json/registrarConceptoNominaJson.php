<?php
#########################################################################################################################
#                                                                                                       Modificaciones
#########################################################################################################################
#28/08/2017 | Erica G.  | Añadir campo tipo interfaz
#04/03/2014 --- Nestor B --- se modificó la ruta del botón aceptar para que se devuelva a donde fue llamado
#25/03/2017 --- Nestor B --- se agregó la validación del código del concepto para que no se permita registrar un concepto con un código ya existente
#01/06/2017 --- Nestor B --- se quito el campo de tercero y se se agrego el de compañia
#06/06/2017 --- Nestor B --- se quito la validacion del camp tercero
#11/09/2017 --- Nestor B --- se agregó el campo ibc
#########################################################################################################################


require_once '../Conexion/conexion.php';
session_start();
$x=0;
$compania = $_SESSION['compania'];
$codig            = $_POST['txtCodigo'];
$sql1 = "SELECT  id_unico,
                codigo,
                descripcion 
                FROM gn_concepto
                WHERE codigo = '$codig'";

$conc = $mysqli->query($sql1);
if(mysqli_num_rows($conc)>0){
    $resultado = false;
    $x =1;
} else { 

$codigo             = '"'.$mysqli->real_escape_string(''.$_POST['txtCodigo'].'').'"';
$descripcion        = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';
$tipoafiliacion          = $mysqli->real_escape_string(''.$_POST['sltTipoF'].'');
$unidadmedida       = $mysqli->real_escape_string(''.$_POST['sltUnidad'].'');
$clase              = $mysqli->real_escape_string(''.$_POST['sltClase'].'');
$codigocgr          = $mysqli->real_escape_string(''.$_POST['sltCCGR'].'');
$tipoentidadcredito = $mysqli->real_escape_string(''.$_POST['sltEntidadC'].'');
$codigodian         = $mysqli->real_escape_string(''.$_POST['sltCCD'].'');
$conceptorel        = $mysqli->real_escape_string(''.$_POST['sltConcepto'].'');
$interfaz           = $mysqli->real_escape_string(''.$_POST['interfaz'].'');
$ibc                = $mysqli->real_escape_string(''.$_POST['es_acumulable'].'');
$lf                 = $mysqli->real_escape_string(''.$_POST['acumulable_lf'].'');
$ibr                = $mysqli->real_escape_string(''.$_POST['acumulable_ibr'].'');
$retro              = $mysqli->real_escape_string(''.$_POST['liquida_retroactivo'].'');
$codigoE            = $mysqli->real_escape_string(''.$_POST['nominaE'].'');
$tipoNE            = $mysqli->real_escape_string(''.$_POST['tipoNE'].'');
$equivalenteSUI     = $mysqli->real_escape_string(''.$_POST['equivalenteSui'].'');


if($tipoafiliacion=="")
    $tipo = "null";
else
    $tipo = $tipoafiliacion;

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
  $equivalenteSUI = "null";
else
  $equivalenteSUI = $equivalenteSUI;  

  $sql = "INSERT INTO gn_concepto(compania,codigo,descripcion,tipofondo,unidadmedida,clase,codigocgr,tipoentidadcredito,conceptorel,codigodian, tipo_interfaz,acum_ibc, 
            aplica_liquidacion_final, ibr,liquida_retroactivo,equivalente_NE,tipo_novedad_nomina,equivalante_sui) "
          . "VALUES ($compania,$codigo,$descripcion,$tipo,$unidad,$cla,$cgr,$entidad,$concepto,$dian, $interfaz,$ibc, $lf, $ibr, $retro,'$codigoE',$tipoNE,'$equivalenteSUI')";

$resultado = $mysqli->query($sql);

}
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
          <?php
          if($x = 1){ ?>
          <p>Ya existe un concepto creado con el mismo código.</p>
          <?php
          }else{ ?>
          <p>No se ha podido guardar la información.</p>
          <?php
          }
          ?>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
 

<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>

<?php 
if($resultado==true){ ?>
<script type="text/javascript">
    $("#myModal1").modal('show');
    $("#ver1").click(function(){
        $("#myModal1").modal('hide');
        window.location = '../listar_GN_CONCEPTO.php';
    });
</script>
<?php } else { ?>
    <script type="text/javascript">
      $("#myModal2").modal('show');
      $("#ver2").click(function(){
        $("#myModal2").modal('hide');
        window.history.go(-1);
      });
    </script>
<?php } ?>