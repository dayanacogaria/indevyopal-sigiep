<?php
  require_once('../Conexion/conexion.php');
  session_start();

  //Captura de datos para su inserción en la tabla gf_tercero.
  $tipoIden = '"'.$mysqli->real_escape_string(''.$_POST['tipoIdent'].'').'"';
  $noIdent = '"'.$mysqli->real_escape_string(''.$_POST['noIdent'].'').'"';
  $digitVerif = '"'.$mysqli->real_escape_string(''.$_POST['digitVerif'].'').'"';
  #$sucursal = '"'.$mysqli->real_escape_string(''.$_POST['sucursal'].'').'"';
  $razoSoci = '"'.$mysqli->real_escape_string(''.$_POST['razoSoci'].'').'"';
  #$tipoReg = '"'.$mysqli->real_escape_string(''.$_POST['tipoReg'].'').'"';
  #$tipoEmp = '"'.$mysqli->real_escape_string(''.$_POST['tipoEmp'].'').'"';
  #$tipoEnt = '"'.$mysqli->real_escape_string(''.$_POST['tipoEnt'].'').'"';
  #$repreLegal = '"'.$mysqli->real_escape_string(''.$_POST['repreLegal'].'').'"';
  #$ciudad = '"'.$mysqli->real_escape_string(''.$_POST['ciudad'].'').'"';
  $codMat = '"'.$mysqli->real_escape_string(''.$_POST['txtCodMat'].'').'"';
  $fecha = ''.$mysqli->real_escape_string(''.$_POST['fechaini'].'').'';
  $direccion = '"'.$mysqli->real_escape_string(''.$_POST['txtDirC'].'').'"';
  $estado = '"'.$mysqli->real_escape_string(''.$_POST['sltEst'].'').'"';
   ///---->> vvv PROVISIONAL vvv <<---------
//Consulta del ID de la tabla Perfil Tercero.
  $queryUltimo = "SELECT MIN(Tercero) Tercero FROM gf_perfil_tercero WHERE Perfil = 1";
  $ultimo = $mysqli->query($queryUltimo);
  $row = mysqli_fetch_row($ultimo);
  $compania = $row[0];
///---->> PROVISIONAL <<---------

  $fecha_div = explode("/",$fecha);
  $anio = $fecha_div[2];
  $mes  = $fecha_div[1];
  $dia  = $fecha_div[0];

  $FecI = $anio.'-'.$mes.'-'.$dia;
  //Inserción en la tabla gf_tercero.
  $insertSQL = "INSERT INTO gf_tercero (razonsocial, numeroidentificacion, digitoverficacion, tipoidentificacion,compania)
                VALUES($razoSoci, $noIdent, $digitVerif, $tipoIden, $compania)";
  $resultado = $mysqli->query($insertSQL);
  echo "<br/>";
  if($resultado == true)
  {
    //Consulta del ID del registro insertado anteriormente.
    $queryUltimo = "SELECT MAX(Id_Unico) AS Id_Unico FROM gf_tercero";
    $ultimo = $mysqli->query($queryUltimo);
    $row = mysqli_fetch_row($ultimo);

    //Toma el ID del tercero registrado recientemente.
    $_SESSION['id_tercero'] = $row[0];

    //Inserción del ID en la tabla gf_perfil_tercero con perfil 4 correspondite a cliente jurídica.
    $insertSQL = "INSERT INTO gf_perfil_tercero (Perfil, Tercero) VALUES(4, $row[0])";
    $resultado = $mysqli->query($insertSQL);

    $insertSQL = "INSERT INTO gc_contribuyente(codigo_mat, tercero, estado, fechainscripcion, dir_correspondencia)
                  VALUES($codMat,$row[0],$estado,'$FecI',$direccion)";
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
<!-- Script que redirige a la página inicial de Cliente Jurídica. -->
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
</script>
<?php } ?>