<?php
session_start();
require_once '../Conexion/conexion.php';
#tipo comprobante
$tipoC = ''.$mysqli->real_escape_string(''.$_POST['sltTipoM'].'').'';
#fecha
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fecha'].'').'';
$valorF = explode("/",$fechaT);
$fecha =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
#numero comprobante
$numero = ''.$mysqli->real_escape_string(''.$_POST['txtNumero'].'').'';
#tercero
$tercero = '"'.$mysqli->real_escape_string(''.$_POST['sltTercero'].'').'"';
#banco
$banco = '"'.$mysqli->real_escape_string(''.$_POST['sltBanco'].'').'"';
#centro costo
#descripcion
$descripcion = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';
#estdo comprobante
$estado = "1";
#parametrizacion año
$paramA = '"'.$_SESSION['anno'].'"';
#compania
$compania = $_SESSION['compania'];
#Clase contrato
if(!empty($_POST['sltClaseContrato'])){
  $claseContrato = '"'.$mysqli->real_escape_string(''.$_POST['sltClaseContrato'].'').'"';
}else{
  $claseContrato = "NULL";
}
#Numero contrato
if(!empty($_POST['txtNumeroCT'])){
  $numerocontrato = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroCT'].'').'"';
}else{
  $numerocontrato = "NULL";
}
#Insertado a comprobante contable
$sql = "insert into gf_comprobante_cnt(numero,fecha,descripcion,valorbase,valorbaseiva,valorneto,numerocontrato,tipocomprobante,parametrizacionanno,clasecontrato,estado,compania,tercero) values($numero,$fecha,$descripcion,0,0,0,$numerocontrato,$tipoC,$paramA,$claseContrato,$estado,$compania,$tercero)";
$result = $mysqli->query($sql);
#consulta de tipo comprobante
$tipoComprobantepptal = "SELECT comprobante_pptal FROM gf_tipo_comprobante WHERE id_unico = $tipoC";
$rs = $mysqli->query($tipoComprobantepptal);
$tipoCP = mysqli_fetch_row($rs);
#Insertado a comprobante presupuestal
$responsable = $_SESSION['compania'];
$pptal = "INSERT INTO gf_comprobante_pptal(numero,fecha,fechavencimiento,descripcion,numerocontrato,parametrizacionanno,clasecontrato,tipocomprobante,tercero,estado,responsable) VALUES($numero,$fecha,$fecha,$descripcion,0,$paramA,NULL,$tipoCP[0],$tercero,3,$responsable)";
$presupuesto = $mysqli->query($pptal);

$sqlConsulta1 = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE numero=$numero AND tipocomprobante=$tipoCP[0]";
$resultado1 = $mysqli->query($sqlConsulta1);
$valor1 = mysqli_fetch_row($resultado1);
$_SESSION['idPptal']=$valor1[0];

$_SESSION['numeroCI']=$numero;
$sqlConsulta = "SELECT MAX(id_unico) FROM gf_comprobante_cnt WHERE numero=$numero AND tipocomprobante=$tipoC";
$resultado = $mysqli->query($sqlConsulta);
$valor = mysqli_fetch_row($resultado);
$_SESSION['idComprobanteI'] = $valor[0];
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

<script type="text/javascript" src="../js/md5.pack.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

<?php if($result==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../registrar_GF_COMPROBANTE_INGRESO.php?banco='+md5(<?php echo $banco; ?>);
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>
