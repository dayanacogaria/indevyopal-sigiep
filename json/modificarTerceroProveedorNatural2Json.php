<?php  
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#02/08/2018 |Erica G. | Correo Electrónico - Arreglar Código
#######################################################################################################
require_once('../Conexion/conexion.php');
session_start();

$tipoI      = '"' . $mysqli->real_escape_string('' . $_POST['tipoI'] . '') . '"';
$numId      = '"' . $mysqli->real_escape_string('' . $_POST['numId'] . '') . '"';
$primerN    = '"' . $mysqli->real_escape_string('' . $_POST['primerN'] . '') . '"';
$primerA    = '"' . $mysqli->real_escape_string('' . $_POST['primerA'] . '') . '"';
$id         = '"' . $mysqli->real_escape_string('' . $_POST['id'] . '') . '"';

if(empty($_POST['segundoN'])){
    $segundoN = 'NULL';
} else {
    $segundoN   = '"' . $mysqli->real_escape_string('' . $_POST['segundoN'] . '') . '"';
}
if(empty($_POST['segundoA'])){
    $segundoA = 'NULL';
} else {
    $segundoA   = '"' . $mysqli->real_escape_string('' . $_POST['segundoA'] . '') . '"';
}
if(empty($_POST['correo'])){
    $email  = 'NULL';
} else {
    $email   = '"' . $mysqli->real_escape_string('' . $_POST['correo'] . '') . '"';
}
if(empty($_POST['procedencia']) ){
  $procedencia = 'NULL';
}else{
  $procedencia = '"'.$mysqli->real_escape_string(''.$_POST['procedencia'].'').'"';
}

if(empty($_POST['regimen'])){
    $regimen  = 'NULL';
} else {
    $regimen   = '"' . $mysqli->real_escape_string('' . $_POST['regimen'] . '') . '"';
}
$updateSQL = "UPDATE gf_tercero 
                SET TipoIdentificacion=$tipoI, 
                NumeroIdentificacion= $numId,
                NombreUno=$primerN,
                NombreDos=$segundoN,
                ApellidoUno=$primerA,
                ApellidoDos=$segundoA,
                email = $email,
                tiporegimen = $regimen,
                procedencia= $procedencia
                WHERE Id_Unico = $id ";
$resultado = $mysqli->query($updateSQL);
$sql2="select perfil from gf_perfil_tercero where perfil=5 and tercero=$id";
$result2=$mysqli->query($sql2);
$perfil=mysqli_fetch_row($result2);
if(empty($perfil[0])){
  $consulta = "INSERT INTO gf_perfil_tercero(perfil,tercero) VALUES(5,$id)";
  #Ejecutamos la consulta cargandola a la variable de conexión y retornamos sus valor
  $rs = $mysqli->query($consulta);
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
<!--Links para el estilo de la página-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

<!--Abre nuevamente la página de listar para mostrar la información modificada-->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../LISTAR_TERCERO_PROVEEDOR_NATURAL_2.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>