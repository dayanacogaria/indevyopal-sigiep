<!-- Historial de Actualizaciones  (ctl+F con la fecha para acceso rápido)
##################MODIFICACIONES#########################
#04/05/2017 | Erica G. | Validar Registro
     08/02/2017 - Daniel N: Se validó la no entrada de cuentas con código ya creado.
     09/02/2017 - Alexander N: Si rechaza la inserción, regresa a la pantalla anterior.
     -->
<?php
#Llamamos a la clase de conexión
require_once ('../Conexion/conexion.php');
#Iniciamos la sesion
session_start();
#Capturamos la variable enviadas
$nombre = '"'.$mysqli->real_escape_string(''.$_POST["txtNombre"].'').'"';
$mov = '"'.$mysqli->real_escape_string(''.$_POST["optMov"].'').'"';
$manpac = '"'.$mysqli->real_escape_string(''.$_POST["optManP"].'').'"';
$vigenc = '"'.$mysqli->real_escape_string(''.$_POST["sltVigencia"].'').'"';

$tipoCl = '"'.$mysqli->real_escape_string(''.$_POST["sltTipoClase"].'').'"';
$destin = '"'.$mysqli->real_escape_string(''.$_POST["sltDestino"].'').'"';
$tipoVi = '"'.$mysqli->real_escape_string(''.$_POST["sltTipoVigencia"].'').'"';
$paramA = $_SESSION['anno'];


if(empty($mysqli->real_escape_string(''.$_POST["txtCodigoP"].'')))
    $codigp = "null";
else
    $codigp = '"'.$mysqli->real_escape_string(''.$_POST["txtCodigoP"].'').'"';
  if (empty($_POST["sltPredecesor"])){
    $predec='NULL'; 
  }else{
    $predec = '"'.$mysqli->real_escape_string(''.$_POST["sltPredecesor"].'').'"';
  }
  if (empty($_POST['sltSector'])){
    $sector='NULL'; 
  }else{
    $sector = '"'.$mysqli->real_escape_string(''.$_POST["sltSector"].'').'"';
  }
  if (empty($_POST["equivalente"])){
    $equivalencia='NULL'; 
  }else{
    $equivalencia = '"'.$mysqli->real_escape_string(''.$_POST["equivalente"].'').'"';
  }
   if (empty($_POST["txtDinamica"])){
    $dinamc='NULL'; 
  }else{
    $dinamc = '"'.$mysqli->real_escape_string(''.$_POST["txtDinamica"].'').'"';
  }
    
  $con="SELECT codi_presupuesto FROM gf_rubro_pptal where codi_presupuesto = $codigp";
  $rc = $mysqli->query($con);
  $rowcount=mysqli_num_rows($rc);
  $rs = "";

  if($rowcount<=0)      
 {     
  $sql = "INSERT INTO gf_rubro_pptal (nombre,codi_presupuesto,movimiento,manpac,vigencia,"
          . "dinamica,parametrizacionanno,tipoclase,predecesor,destino,"
          . "tipovigencia,sector, equivalente) VALUES($nombre,$codigp,$mov,$manpac,$vigenc,$dinamc,$paramA,$tipoCl,$predec,$destin,$tipoVi,$sector, $equivalencia)";
  $rs = $mysqli->query($sql); 
 }
else
{
    $error = " El código ingresado ya está en uso";
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
          <p>No se ha podido guardar la información.<?php echo $error?></p>
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
<!-- Actualización 09/02/2017) -->
<script type="text/javascript">
  $("#myModal2").modal('show');
    $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.history.go(-1);
  });
</script>
<!-- Fin Actualización 09/02/2017) -->
<?php } ?>