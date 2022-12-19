<?php
  require_once('../Conexion/conexion.php');
  session_start();

  //Captura de datos e instrucción SQL para su inserción en la tabla gf_perfil_tercero.
  $cargo  = '"'.$mysqli->real_escape_string(''.$_POST['cargo'].'').'"'; 
  $tercero = '"'.$mysqli->real_escape_string(''.$_POST['tercero'].'').'"';
  
  //Verificar cuantos cargos estan la ocupados
  $queryU = "SELECT COUNT(c.cargo) FROM gf_cargo_tercero c WHERE c.cargo= $cargo";
  $car = $mysqli->query($queryU);
  $rowCargo = mysqli_fetch_row($car);
  $numCa= $rowCargo[0];

  //Traer el numero de plazas que tiene el cargo
  $numP = "SELECT numero_plazas FROM gf_cargo WHERE id_unico = $cargo";
  $numCar = $mysqli->query($numP);
  $rownumCar = mysqli_fetch_row($numCar);
  $numPlazas= $rownumCar[0];

  // Preguntar si ya esta
  $query = "SELECT c.cargo FROM gf_cargo_tercero c WHERE c.cargo= $cargo AND c.tercero= $tercero";
  $ya = $mysqli->query($query);
  $rowya = mysqli_num_rows($ya);



  if($numCa<$numPlazas){
    if($rowya<=0){
      $insertSQL = "INSERT INTO gf_cargo_tercero (cargo, tercero) VALUES($cargo, $tercero)";
    $resultado = $mysqli->query($insertSQL);
    $resultado='1';
    } else {
      $resultado='2';
    }
    
  } else {
    $resultado = '3';
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
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Informaci&oacute;n guardada correctamente.</p>
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
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
        <p>No se ha podido guardar la informaci&oacute;n.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal3" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
        <p>Las plazas de este cargo ya estan ocupadas.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal4" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
        <p>El cargo ya ha sido asignado a esta persona.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver4" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
  
<!-- Script que redirige a la página inicial -->
<?php if($resultado=='1'){?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../GF_CARGO_TERCERO.php';
  });
</script>
<?php }else{ 
  if($resultado=='2'){ ?>
<script type="text/javascript">
  $("#myModal4").modal('show');
  $("#ver4").click(function(){
    $("#myModal4").modal('hide');
    window.location='../GF_CARGO_TERCERO.php';
  });
</script>
  <?php } else { 
  if($resultado=='3') { ?>
<script type="text/javascript">
  $("#myModal3").modal('show');
  $("#ver3").click(function(){
    $("#myModal3").modal('hide');
    window.location='../GF_CARGO_TERCERO.php';
  });
</script>
<?php } else { ?>
  <script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.location='../GF_CARGO_TERCERO.php';
  });
</script>
<?php } } } ?>