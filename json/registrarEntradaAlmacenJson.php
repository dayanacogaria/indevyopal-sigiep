<?php
require_once '../Conexion/conexion.php';
session_start();
$estadoM       = '"2"';
$lugarE        = '"'.$mysqli->real_escape_string(''.$_POST['sltLE'].'').'"';
$unidadPE      = '"'.$mysqli->real_escape_string(''.$_POST['sltUPE'].'').'"';
$plazoE        = '"'.$mysqli->real_escape_string(''.$_POST['txtPlazoE'].'').'"';
#Validación de campos no obligatorios
if(!empty($_POST['txtObservacion'])){
    $observaciones = '"'.$mysqli->real_escape_string(''.$_POST['txtObservacion'].'').'"';
}else{
    $observaciones = 'NULL';
}
if(!empty($_POST['txtDescripcion'])){
    $descripcion   = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';
}else{
    $descripcion = 'NULL';
}

$proyecto      = '"'.$mysqli->real_escape_string(''.$_POST['sltProyecto'].'').'"';
$centrocosto   = '"'.$mysqli->real_escape_string(''.$_POST['sltCentroCosto'].'').'"';
$rubroP        = '"'.$mysqli->real_escape_string(''.$_POST['sltRubroP'].'').'"';
$tercero       = '"'.$mysqli->real_escape_string(''.$_POST['sltTercero'].'').'"';
$dependencia   = '"'.$mysqli->real_escape_string(''.$_POST['sltDependencia'].'').'"';
$responsable   = '"'.$mysqli->real_escape_string(''.$_POST['sltResponsable'].'').'"';
#Conversión de fecha
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fecha'].'').'';
$valorF = explode("/",$fechaT);
$fechaC =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
$paramA=$_SESSION['anno'];
$numeroC       = ''.$mysqli->real_escape_string(''.$_POST['txtNumeroMovimiento'].'').'';
$tipoM         = ''.$mysqli->real_escape_string(''.$_POST['sltTipoMovimiento'].'').'';
$iva = $_POST['txtIva'];
$sql = "INSERT INTO gf_movimiento(numero, fecha, descripcion, plazoentrega, observaciones, tipomovimiento, parametrizacionanno, tercero, tercero2, dependencia, centrocosto, rubropptal, proyecto, lugarentrega, unidadentrega, estado,porcivaglobal) VALUES ($numeroC,$fechaC,$descripcion,$plazoE,$observaciones,$tipoM,$paramA,$responsable,$tercero,$dependencia,$centrocosto,$rubroP,$proyecto,$lugarE,$unidadPE,$estadoM,$iva)";
$resultado = $mysqli->query($sql);
$var = "SELECT MAX(id_unico) FROM gf_movimiento WHERE numero=$numeroC AND tipomovimiento=$tipoM";
$resulta = $mysqli->query($var);
$fila = mysqli_fetch_row($resulta);
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
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->
<?php if($resultado==true){
    $data = $_SESSION['data'];
    $c = count($data);
    for ($index = 0; $index < count($data); $index++) {
        $planI = $data[$index]['id_planI'];
        $cantidad = $data[$index]['cantidad'];
        $valor =$data[$index]['valor'];
        $valorIva = $data[$index]['iva'];
        $sqlFila = "INSERT INTO gf_detalle_movimiento(planmovimiento,cantidad,valor,iva,movimiento) VALUES('$planI','$cantidad','$valor','$valorIva','$fila[0]');";                
        $datos = $mysqli->query($sqlFila);
    }
    ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location = '../RF_ENTRADA_ALMACEN.php'+'<?php echo '?entrada='.md5($fila[0])?>';    
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>