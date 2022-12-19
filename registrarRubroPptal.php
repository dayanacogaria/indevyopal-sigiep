<?php
#Llamamos a la clase de conexión
require_once ('../Conexion/conexion.php');
#Iniciamos la sesion
session_start();
#Capturamos la variable enviadas
$nombre = '"'.$mysqli->real_escape_string(''.$_POST["txtNombre"].'').'"';
$codigp = '"'.$mysqli->real_escape_string(''.$_POST["txtCodigoP"].'').'"';
$mov = '"'.$mysqli->real_escape_string(''.$_POST["optMov"].'').'"';
$manpac = '"'.$mysqli->real_escape_string(''.$_POST["optManP"].'').'"';
$vigenc = '"'.$mysqli->real_escape_string(''.$_POST["sltVigencia"].'').'"';
$dinamc = '"'.$mysqli->real_escape_string(''.$_POST["txtDinamica"].'').'"';
$tipoCl = '"'.$mysqli->real_escape_string(''.$_POST["sltTipoClase"].'').'"';
$predec = '"'.$mysqli->real_escape_string(''.$_POST["sltPredecesor"].'').'"';

$destin = '"'.$mysqli->real_escape_string(''.$_POST["sltDestino"].'').'"';
$tipoVi = '"'.$mysqli->real_escape_string(''.$_POST["sltTipoVigencia"].'').'"';
$sector = '"'.$mysqli->real_escape_string(''.$_POST["sltSector"].'').'"';


#$paramA = '"'.$mysqli->real_escape_string(''.$_POST["pramA"].'').'"';
$paramA = '';
#$compania = '"'.$mysqli->real_escape_string(''.$_POST["compania"].'').'"';
$compania = '';

if (empty($paramA)) {
    $sql = "SELECT MAX(id_unico) FROM gf_parametrizacion_anno";
    $rs = $mysqli->query($sql);
    $row = mysqli_fetch_row($rs);
    $paramA = $row[0];
}  else {
    $paramA = '"'.$mysqli->real_escape_string(''.$_POST["pramA"].'').'"';
}

if (empty($compania)) {
    $sql = "SELECT MAX(tercero) FROM gf_perfil_tercero WHERE Perfil = 1";
    $rs = $mysqli->query($sql);
    $row = mysqli_fetch_row($rs);
    $compania = $row[0];
}  else {
    $compania = '"'.$mysqli->real_escape_string(''.$_POST["compania"].'').'"';
}

if ($predec == '""' || $sector == '""') {
    $sql = "INSERT INTO gf_rubro_pptal(nombre,codi_presupuesto,movimiento,manpac,vigencia,dinamica,parametrizacionanno,tipoclase,predecesor,destino,tipovigencia,sector,compania) VALUES($nombre,$codigp,$mov,$manpac,$vigenc,$dinamc,$paramA,$tipoCl,NULL,$destin,$tipoVi,NULL,$compania)";
    $rs = $mysqli->query($sql);
}else{
    $predec = '"'.$mysqli->real_escape_string(''.$_POST["sltPredecesor"].'').'"';
    $sector = '"'.$mysqli->real_escape_string(''.$_POST["sltSector"].'').'"';
  
    $sql = "INSERT INTO gf_rubro_pptal(nombre,codi_presupuesto,movimiento,manpac,vigencia,dinamica,parametrizacionanno,tipoclase,predecesor,destino,tipovigencia,sector,compania) VALUES($nombre,$codigp,$mov,$manpac,$vigenc,$dinamc,$paramA,$tipoCl,$predec,$destin,$tipoVi,$sector,$compania)";
    $rs = $mysqli->query($sql);
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

  <script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

<?php if($rs==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../listar_GF_RUBRO_PPTAL.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
      $("#myModal2").modal('hide');
  });
</script>
<?php } ?>