<?php 
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#31/07/2018 |Erica G. | Correo Electrónico - Arreglar Código
#22/09/2017 |Erica G. | Agregar Campo Tipo Compañia (1-Pública, 2- Privada)
# 21/09/2017   |Erica G. | Campo tipo compañia
# 27/07/2017   |ERICA G. | Eliminaba el logo cuando no traia nada
#######################################################################################################
require_once('../Conexion/conexion.php');
session_start();
$id         = '"' . $mysqli->real_escape_string('' . $_POST['id'] . '') . '"';
$tipoIden   = '"' . $mysqli->real_escape_string('' . $_POST['tipoIdent'] . '') . '"';
$noIdent    = '"' . $mysqli->real_escape_string('' . $_POST['noIdent'] . '') . '"';
$razoSoci   = '"' . $mysqli->real_escape_string('' . $_POST['razoSoci'] . '') . '"';
$ciudad     = '"' . $mysqli->real_escape_string('' . $_POST['ciudad'] . '') . '"';

if (empty($_POST['tipoReg'])) {
    $tipoReg = 'NULL';
} else {
    $tipoReg    = '"' . $mysqli->real_escape_string('' . $_POST['tipoReg'] . '') . '"';
}

if ($mysqli->real_escape_string('' . $_POST['sucursal'] . '') == "")
    $sucursal = "null";
else
    $sucursal = '"' . $mysqli->real_escape_string('' . $_POST['sucursal'] . '') . '"';

if ($mysqli->real_escape_string('' . $_POST['digitVerif'] . '') == "")
    $digitVerif = "null";
else
    $digitVerif = '"' . $mysqli->real_escape_string('' . $_POST['digitVerif'] . '') . '"';

if ($mysqli->real_escape_string('' . $_POST['tipoEmp'] . '') == "")
    $tipoEmp = "null";
else
    $tipoEmp = '"' . $mysqli->real_escape_string('' . $_POST['tipoEmp'] . '') . '"';

if ($mysqli->real_escape_string('' . $_POST['tipoEntidad'] . '') == "")
    $tipoEnt = "null";
else
    $tipoEnt = '"' . $mysqli->real_escape_string('' . $_POST['tipoEntidad'] . '') . '"';

if ($mysqli->real_escape_string('' . $_POST['codigo'] . '') == "")
    $codigo = "null";
else
    $codigo = '"' . $mysqli->real_escape_string('' . $_POST['codigo'] . '') . '"';


if ($mysqli->real_escape_string('' . $_POST['repreLegal'] . '') == "")
    $repreLegal = "null";
else
    $repreLegal = '"' . $mysqli->real_escape_string('' . $_POST['repreLegal'] . '') . '"';


if (empty($_POST['contacto']) || $_POST['contacto'] == '""' || $_POST['contacto'] == 0) {
    $contacto = 'NULL';
} else {
    $contacto = '"' . $mysqli->real_escape_string('' . $_POST['contacto'] . '') . '"';
}

if (empty($_POST['tipocomp']) || $_POST['tipocomp'] == '""' || $_POST['tipocomp'] == 0) {
    $tipocomp = 'NULL';
} else {
    $tipocomp = '"' . $mysqli->real_escape_string('' . $_POST['tipocomp'] . '') . '"';
}
if (empty($_POST['correo'])) {
    $email = 'NULL';
} else {
    $email = '"' . $mysqli->real_escape_string('' . $_POST['correo'] . '') . '"';
}
$costos = '"'.$mysqli->real_escape_string(''.$_POST['costos'].'').'"';
if (empty($_FILES['flLogo']['name'])) {
    $updateSQL = "UPDATE gf_tercero 
        SET RazonSocial = $razoSoci, NumeroIdentificacion = $noIdent, DigitoVerficacion = $digitVerif, 
        TipoIdentificacion = $tipoIden, Sucursal = $sucursal, 
        RepresentanteLegal = $repreLegal, CiudadIdentificacion = $ciudad, 
        TipoRegimen = $tipoReg, Contacto=$contacto,TipoEmpresa = $tipoEmp, 
        TipoEntidad =  $tipoEnt, codigo_dane = $codigo , tipo_compania = $tipocomp , email = $email, 
        distribucion_costos =$costos 
    WHERE Id_Unico = $id";
} else {
    if (!empty($_POST['txtlogo'])) {
        unlink('../' . $_POST['txtlogo']);
    }
    //Nombre del documento
    $logo = '"logo/' . $_FILES['flLogo']['name'] . '"';
    $ruta = '../logo/' . $_FILES['flLogo']['name'];
    @move_uploaded_file($_FILES["flLogo"]["tmp_name"], $ruta);
    $updateSQL = "UPDATE gf_tercero 
    SET RazonSocial = $razoSoci, NumeroIdentificacion = $noIdent, DigitoVerficacion = $digitVerif, "
            . "TipoIdentificacion = $tipoIden, Sucursal = $sucursal, "
            . "RepresentanteLegal = $repreLegal, CiudadIdentificacion = $ciudad, "
            . "TipoRegimen = $tipoReg, Contacto=$contacto,TipoEmpresa = $tipoEmp, "
            . "TipoEntidad =  $tipoEnt, codigo_dane = $codigo, ruta_logo=$logo, "
            . "tipo_compania = $tipocomp, email = $email , distribucion_costos = $costos  
    WHERE Id_Unico = $id";
}
$resultado = $mysqli->query($updateSQL);
$sql = "select * from gf_perfil_tercero where perfil=1 and tercero=$id";
$result = $mysqli->query($sql);
if (mysqli_num_rows($result)>0) {
} else {
    $insertSQL = "INSERT INTO gf_perfil_tercero (Perfil, Tercero) VALUES(1, $id)";
    $resultado1 = $mysqli->query($insertSQL);
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
<!-- Divs de clase Modal para las ventanillas de confirmación de modificación de registro. -->
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

<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!-- Script que redirige a la página inicial de Compañia. -->
<?php if ($resultado == true) { ?>
    <script type="text/javascript">
      $("#myModal1").modal('show');
      $("#ver1").click(function(){
        $("#myModal1").modal('hide');
        window.location='../TERCERO_COMPANIA.php';
      });
    </script>
<?php } else { ?>
    <script type="text/javascript">
      $("#myModal2").modal('show');
      $("#ver2").click(function(){
        $("#myModal2").modal('hide');
        window.history.go(-1);
      });
    </script>
<?php } ?>