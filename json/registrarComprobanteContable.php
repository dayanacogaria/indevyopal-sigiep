<?php
session_start();
require_once '../Conexion/conexion.php';
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fecha'].'').'';
$valorF = explode("/",$fechaT);
$fecha =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
$tipoComprobante = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoC'].'').'"';
$numeroComprobante = '"'.$mysqli->real_escape_string(''.$_POST['txtNumero'].'').'"';
$tercero = '"'.$mysqli->real_escape_string(''.$_POST['sltTercero'].'').'"';
$claseContrato = '"'.$mysqli->real_escape_string(''.$_POST['sltClaseCT'].'').'"';
$NContrato = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroCT'].'').'"';
$descripcion = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';
$_SESSION['centrocosto'] = $_POST['sltCentroC'];
$_SESSION['proyecto'] = $_POST['sltProyecto'];
$param = '"'.$_SESSION['anno'].'"';
$estado = "1";
$compania = $_SESSION['compania'];
$sql ="INSERT INTO gf_comprobante_cnt(numero,fecha,descripcion,numerocontrato,tipocomprobante,parametrizacionanno,tercero, estado,clasecontrato,compania) VALUES ($numeroComprobante,$fecha,$descripcion,$NContrato,$tipoComprobante,$param,$tercero,$estado,$claseContrato,$compania)";
$resultadoC = $mysqli->query($sql);
if($resultadoC==true){
$_SESSION['num'] = $_POST['txtNumero'];
$sqlConsulta = "SELECT MAX(id_unico) FROM gf_comprobante_cnt "
        . "WHERE numero=$numeroComprobante AND tipocomprobante=$tipoComprobante AND parametrizacionanno = $param";
$resultado = $mysqli->query($sqlConsulta);
$valor = mysqli_fetch_row($resultado);
$id = $valor[0];
$_SESSION['idNumeroC'] = $valor[0];

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


  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

<?php if($resultadoC==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    <?php if(!empty($_REQUEST['formulario'])){
        $var_formulario = $_REQUEST['formulario'];
         $url = '../'.$var_formulario.'?id='.md5($id);?>
        document.location='<?php echo $url;?>'
    <?php } else { ?>
    window.history.go(-1);
    <?php } ?>
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.history.go(-1);
  });
</script>
<?php } ?>