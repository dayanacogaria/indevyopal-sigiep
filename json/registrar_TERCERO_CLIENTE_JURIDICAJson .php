<?php
  require_once('../Conexion/conexion.php');
  session_start();

  $tipoIden = '"'.$mysqli->real_escape_string(''.$_POST['tipoIden'].'').'"';
  $noIdent = '"'.$mysqli->real_escape_string(''.$_POST['noIdent'].'').'"';
  $digitVerif = '"'.$mysqli->real_escape_string(''.$_POST['digitVerif'].'').'"';
  $sucursal = '"'.$mysqli->real_escape_string(''.$_POST['sucursal'].'').'"';
  $razoSoci = '"'.$mysqli->real_escape_string(''.$_POST['razoSoci'].'').'"';
  $tipoReg = '"'.$mysqli->real_escape_string(''.$_POST['tipoReg'].'').'"';
  $tipoEmp = '"'.$mysqli->real_escape_string(''.$_POST['tipoEmp'].'').'"';
  $tipoEnt = '"'.$mysqli->real_escape_string(''.$_POST['tipoEnt'].'').'"';
  $repreLegal = '"'.$mysqli->real_escape_string(''.$_POST['repreLegal'].'').'"';
  
  if(empty($_POST['contacto'])){
    $contacto = 'NULL';
  }else{
    $contacto = '"'.$mysqli->real_escape_string(''.$_POST['contacto'].'').'"';  
  }
  $ciudad = '"'.$mysqli->real_escape_string(''.$_POST['ciudad'].'').'"';
  $zona = '"'.$mysqli->real_escape_string(''.$_POST['zona'].'').'"';
  //Compañia
  $compania = 1;

  $insertSQL = "INSERT INTO gf_tercero (RazonSocial, NumeroIdentificacion, DigitoVerficacion, Compania, TipoIdentificacion, Sucursal, RepresentanteLegal, CiudadIdentificacion, TipoRegimen, Contacto, TipoEmpresa, TipoEntidad)
    VALUES($razoSoci, $noIdent, $digitVerif, $compania, $tipoIden, $sucursal, $repreLegal, $ciudad, $tipoReg, $contacto, $tipoEmp, $tipoEnt)";
  $resultado = $mysqli->query($insertSQL);

  $queryUltimo = "SELECT MAX(Id_Unico) AS Id_Unico FROM gf_tercero";
  $ultimo = $mysqli->query($queryUltimo);
  $row = mysqli_fetch_row($ultimo);

  $insertSQL = "INSERT INTO gf_perfil_tercero (Perfil, Tercero) VALUES(4, $row[0])";
  $resultado = $mysqli->query($insertSQL);

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
    window.location='../TERCERO_COMPANIA.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>