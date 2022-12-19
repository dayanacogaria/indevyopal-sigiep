<?php 
#06/11/18 : Registrar cambios en datos basicos
require_once('../Conexion/conexion.php');
session_start();
$compania   = $_SESSION['compania'];
$id_usuario = $_SESSION['id_usuario'];
$usuario_tercero = $_SESSION['usuario_tercero'];
$fecha = '"'.date('Y-m-d').'"';

$modificado = "";

// Caja de texto de Razon Social
if(empty($_POST['razoSoci'])){
    $razoSoci = 'NULL';
} else {
    $razoSoci   = '"' . $mysqli->real_escape_string('' . $_POST['razoSoci'] . '') . '"';
}
// Caja de texto del Primer Nombre
if(empty($_POST['primerN'])){
    $primerN = 'NULL';
} else {
    $primerN   = '"' . $mysqli->real_escape_string('' . $_POST['primerN'] . '') . '"';
}
// Caja de texto del Segundo Nombre
if(empty($_POST['segundoN'])){
    $segundoN = 'NULL';
} else {
    $segundoN   = '"' . $mysqli->real_escape_string('' . $_POST['segundoN'] . '') . '"';
}
// Caja de texto del Primer Apellido
if(empty($_POST['primerA'])){
    $primerA = 'NULL';
} else {
    $primerA   = '"' . $mysqli->real_escape_string('' . $_POST['primerA'] . '') . '"';
}
// Caja de texto del Segundo Apellido
if(empty($_POST['segundoA'])){
    $segundoA = 'NULL';
} else {
    $segundoA   = '"' . $mysqli->real_escape_string('' . $_POST['segundoA'] . '') . '"';
}
// Caja de texto de la tarjeta profesional
if(empty($_POST['tarjetaP'])){
    $tarjetaP  = 'NULL';
} else {
    $tarjetaP   = '"' . $mysqli->real_escape_string('' . $_POST['tarjetaP'] . '') . '"';
}
// Caja de texto del Correo
if(empty($_POST['email'])){
    $email  = 'NULL';
} else {
    $email   = '"' . $mysqli->real_escape_string('' . $_POST['email'] . '') . '"';
}
// Caja de texto del Nombre de usuario
if(empty($_POST['txtUsuario'])){
    $txtUsuario  = 'NULL';
} else {
    $txtUsuario   = '"' . $mysqli->real_escape_string('' . $_POST['txtUsuario'] . '') . '"';
}
// Caja de texto de la contraseña antigua
if(empty($_POST['txtPassA'])){
    $txtPassA  = 'txtPassA';
} else {
    $txtPassA   = '"' . $mysqli->real_escape_string('' . $_POST['txtPassA'] . '') . '"';
}
// Caja de texto de la contraseña nueva
if(empty($_POST['txtPassN'])){
    $txtPassN  = 'NULL';
} else {
    $txtPassN   = '"' . $mysqli->real_escape_string('' . $_POST['txtPassN'] . '') . '"';
}
// Caja de texto de la verificacion de la contraseña nueva
if(empty($_POST['txtPassNV'])){
    $txtPassNV  = $txtPassA;
    //$txtPassNV  = 'NULL';
} else {
    $txtPassNV  = '"' . $mysqli->real_escape_string('' . $_POST['txtPassNV'] . '') . '"';
}
// Sentencia SQL para actualizar los datos del usuario
$updateTerceroSQL = "UPDATE gf_tercero T
                          SET    T.razonsocial = $razoSoci, 
                                 T.nombreuno = $primerN, 
                                 T.nombredos = $segundoN,
                                 T.apellidouno = $primerA, 
                                 T.apellidodos = $segundoA,
                                 T.tarjeta_profesional = $tarjetaP,
                                 T.email = $email
                          WHERE T.id_unico = $usuario_tercero
                          AND   T.compania = $compania";
$resultadoTercero = $mysqli->query($updateTerceroSQL);
//$ru = mysqli_fetch_row($resultadoTercero);

$updateUsuarioSQL = "UPDATE gs_usuario U
                          SET    U.usuario = $txtUsuario,
                                 U.contrasen = $txtPassNV,
                                 U.fechaactualizacion = $fecha
                          WHERE  U.tercero = $usuario_tercero";
$resultadoUsuario = $mysqli->query($updateUsuarioSQL);
//$rt = mysqli_fetch_row($resultadoUsuario);

?>
 <!-- Estructura de impresión de modales -->
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
  <div class="modal fade" id="verificacionPass" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Verifique su nueva contraseña</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="verpass" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
    </div>


<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

<?php 
    if(($resultadoTercero == true)){ ?>
        <script type="text/javascript">
             $("#myModal1").modal('show');
             $("#ver1").click(function(){
             $("#myModal1").modal('hide');
             window.location='../index.php';
    });
        </script>
<?php }else{ ?>
         <script type="text/javascript">
          $("#myModal2").modal('show');
          $("#ver2").click(function(){
          $("#myModal2").modal('hide');
          window.location='../DatosBasicos.php';
      });
        </script>
<?php } ?>