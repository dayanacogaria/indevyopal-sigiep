<?php

session_start();
require_once '../Conexion/conexion.php';
$tipoPago = '"'.$mysqli->real_escape_string(''.$_POST['slttipopago'].'').'"';
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fecha'].'').'';
$valorF = explode("/",$fechaT);
$fecha =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
$numeroPago = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroP'].'').'"';
$responsable = ''.$mysqli->real_escape_string(''.$_POST['sltTercero'].'').'';
$banco = '"'.$mysqli->real_escape_string(''.$_POST['sltBanco'].'').'"';
$estado = 1;
$_SESSION['cupones'] = $mysqli->real_escape_string($_POST['txtCupones']);
$_SESSION['valor'] = $mysqli->real_escape_string($_POST['txtValor']);
$sql = "INSERT INTO gp_pago(numero_pago,tipo_pago,responsable,fecha_pago,banco,estado) VALUES($numeroPago,$tipoPago,$responsable,$fecha,$banco,$estado)";
$resultadoP = $mysqli->query($sql);
$sqlConsulta = "SELECT MAX(id_unico) FROM gp_pago WHERE numero_pago=$numeroPago AND tipo_pago=$tipoPago";
$resultado = $mysqli->query($sqlConsulta);
$valor = mysqli_fetch_row($resultado);
$_SESSION['idpago'] = $valor[0];
$_SESSION['pago']=$numeroPago;
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
    window.location='../registrar_GF_RECAUDO_FACTURACION.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>

