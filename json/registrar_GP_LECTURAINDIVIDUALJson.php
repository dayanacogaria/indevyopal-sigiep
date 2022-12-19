<?php
  require_once('../Conexion/conexion.php');
  session_start();
	
  setlocale(LC_ALL,"es_ES");
  date_default_timezone_set('America/Bogota');
 $fecha= date('Y-m-d H:m:s'); 
 $tercero = $_SESSION['compania'];
 $uvms= $mysqli->real_escape_string(''.$_POST['iduvms'].'');
 $periodo= $mysqli->real_escape_string(''.$_POST['periodo'].'');
 $valor= $mysqli->real_escape_string(''.$_POST['valor'].'');
 
 $nr= "SELECT * FROM gp_lectura WHERE unidad_vivienda_medidor_servicio='$uvms' AND periodo = '$periodo'";
 $nr = $mysqli->query($nr);
 if(mysqli_num_rows($nr)>0){
     $num='1';
     $resultado = false;
 } else { 
     $num=0;
 echo   $insert ="INSERT INTO gp_lectura (unidad_vivienda_medidor_servicio, periodo, valor, aforador, fecha)VALUES ($uvms, $periodo, $valor, $tercero, '$fecha')";
    $resultado = $mysqli->query($insert);
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
          <?php if($num>0){ ?>  
          <p>El registro ingresado ya existe.</p>
          <?php } else { ?>
          <p>No se ha podido guardar la información.</p>
          <?php } ?>
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
    window.location='../LISTAR_GP_LECTURA.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
     window.location=window.history.back(-1);
  });
 
</script>
<?php } ?>