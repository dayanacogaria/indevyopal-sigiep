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
$compania   = $_SESSION['compania'];

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
$insertSQL = "INSERT INTO gf_tercero (TipoIdentificacion, 
    NumeroIdentificacion, NombreUno, NombreDos, ApellidoUno, 
    ApellidoDos, Compania,email) 
    VALUES( $tipoI, $numId,  $primerN, $segundoN, 
    $primerA, $segundoA, $compania,$email)";
$resultado = $mysqli->query($insertSQL);
if($resultado == true)
{
    $queryUltimo = "SELECT MAX(Id_Unico) AS Id_Unico FROM gf_tercero";
    $ultimo = $mysqli->query($queryUltimo);
    $row = mysqli_fetch_row($ultimo);
    $_SESSION['id_tercero'] = $row[0];

    $insertSQL = "INSERT INTO gf_perfil_tercero (Perfil, Tercero) VALUES(10, $row[0])";
    $resultado = $mysqli->query($insertSQL);
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
<!-- Divs de clase Modal para las ventanillas de confirmación de inserción de registro. -->
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
<!-- Script que redirige a la página inicial de Contacto Natural. -->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.history.go(-2);
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