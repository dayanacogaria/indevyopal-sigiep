 <?php 
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#02/08/2018 |Erica G. | Correo Electrónico - Arreglar Código 
#######################################################################################################
require_once '../Conexion/conexion.php';
session_start();

$tipoIden   = '"'.$mysqli->real_escape_string(''.$_POST['tipoIdent'].'').'"';
$noIdent    = '"'.$mysqli->real_escape_string(''.$_POST['noIdent'].'').'"';
$digitVerif = '"'.$mysqli->real_escape_string(''.$_POST['digitVerif'].'').'"';
$razoSoci   = '"'.$mysqli->real_escape_string(''.$_POST['razoSoci'].'').'"';
$compania   = $_SESSION['compania'];
$ciudad     = '"'.$mysqli->real_escape_string(''.$_POST['ciudad'].'').'"';

if(empty($_POST['contacto'])){
    $contacto = 'NULL';
}else{
    $contacto = '"'.$mysqli->real_escape_string(''.$_POST['contacto'].'').'"';
}

if(empty($_POST['tipoReg'])){
    $tipoReg = 'NULL';
}else{
    $tipoReg    = '"'.$mysqli->real_escape_string(''.$_POST['tipoReg'].'').'"';
}

if(empty($_POST['repreLegal']) ){
    $repreLegal = 'NULL';
}else{
    $repreLegal = '"'.$mysqli->real_escape_string(''.$_POST['repreLegal'].'').'"';
}

if(empty($_POST['zona'])){
    $zona = 'NULL';
}else{
    $zona = '"'.$mysqli->real_escape_string(''.$_POST['zona'].'').'"';
}

if(empty($_POST['tipoEmp'])){
    $tipoEmp = 'NULL';
}else{
    $tipoEmp = '"'.$mysqli->real_escape_string(''.$_POST['tipoEmp'].'').'"';
}

if(empty($_POST['tipoEntidad'])){
    $tipoEnt = 'NULL';
}else{
    $tipoEnt = '"'.$mysqli->real_escape_string(''.$_POST['tipoEntidad'].'').'"';
}

if(empty($_POST['sucursal']) ){
    $sucursal = 'NULL';
}else{
    $sucursal = '"'.$mysqli->real_escape_string(''.$_POST['sucursal'].'').'"';
}

if(empty($_POST['correo']) ){
    $email = 'NULL';
}else{
    $email = '"'.$mysqli->real_escape_string(''.$_POST['correo'].'').'"';
}

$insertSQL = "INSERT INTO gf_tercero (RazonSocial, NumeroIdentificacion, 
    DigitoVerficacion, Compania, TipoIdentificacion, Sucursal, RepresentanteLegal, 
    CiudadIdentificacion, TipoRegimen, TipoEmpresa, TipoEntidad,Contacto,Zona, email)
VALUES($razoSoci, $noIdent, 
    $digitVerif, $compania, $tipoIden, $sucursal, $repreLegal, 
        $ciudad, $tipoReg, $tipoEmp, $tipoEnt,$contacto,$zona,$email)";
$resultado = $mysqli->query($insertSQL);

$ult = "SELECT MAX(Id_Unico) FROM gf_tercero";
$max = $mysqli->query($ult);
$row = mysqli_fetch_row($max);
$_SESSION['id_tercero'] = $row[0];

$consulta = "INSERT INTO gf_perfil_tercero(Perfil,Tercero) VALUES(6,$row[0])";
$rs = $mysqli->query($consulta);
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
    window.location='../LISTAR_TERCERO_PROVEEDOR_JURIDICA_2.php';
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