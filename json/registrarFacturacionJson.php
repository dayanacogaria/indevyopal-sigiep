<?php
session_start();
require_once '../Conexion/conexion.php';
$tipoFactura  = '"'.$mysqli->real_escape_string(''.$_POST['slttipofactura'].'').'"';
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fecha'].'').'';
$valorF = explode("/",$fechaT);
$fecha =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
$numeroFactura = ''.$mysqli->real_escape_string(''.$_POST['txtNumeroF'].'').'';
$tercero = ''.$mysqli->real_escape_string(''.$_POST['sltTercero'].'').'';
$centroCosto = ''.$mysqli->real_escape_string(''.$_POST['sltCentroCosto'].'').'';
$fechaVT = ''.$mysqli->real_escape_string(''.$_POST['fechaV'].'').'';
$valorV = explode("/",$fechaVT);
$fechaV =  '"'.$valorV[2].'-'.$valorV[1].'-'.$valorV[0].'"';
$estado = "4";
$descripcion = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';
$responsable = $tercero;
$sql = "INSERT INTO gp_factura(numero_factura,tipofactura,tercero,unidad_vivienda_servicio,periodo,fecha_factura,fecha_vencimiento,centrocosto,descripcion,estado_factura,lectura,responsable) VALUES($numeroFactura,$tipoFactura,$tercero,NULL,NULL,$fecha,$fechaV,$centroCosto,$descripcion,$estado,NULL,$responsable)";
$resultadoF = $mysqli->query($sql);

$_SESSION['factura'] = $_POST['txtNumeroF'];
$sqlConsulta = "SELECT MAX(id_unico) FROM gp_factura WHERE numero_factura=$numeroFactura AND tipofactura=$tipoFactura";
$resultado = $mysqli->query($sqlConsulta);
$valor = mysqli_fetch_row($resultado);
$_SESSION['idFactura'] = $valor[0];
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
    window.location='../registrar_GF_FACTURACION.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>

