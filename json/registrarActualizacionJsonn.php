<?php

session_start();

require_once '../Conexion/conexion.php';



$fechaI = DateTime::createFromFormat('d/m/Y', $_POST['fecha']);
$fechaI= $fechaI->format('Y/m/d');
$fechaI=$fechaI;

$gestion=$mysqli->real_escape_string(''.$_POST['gestion'].'');
$observaciones=$mysqli->real_escape_string(''.$_POST['observaciones'].'');

$sql="INSERT INTO gs_actualizacion  (fecha,observaciones,gestion) VALUES('$fechaI','$observaciones','$gestion')";
//echo "$sql";
$resultado=$mysqli->query($sql);




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

</body>
</html>

<script type="text/javascript" src="../js/md5.pack.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

<?php
if($resultado==true){ 
    $sql = "SELECT MAX(id_unico) FROM gs_actualizacion";
    $rs = $mysqli->query($sql);
    $row = mysqli_fetch_row( $rs);
    
    ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../GS_REGISTRO_ACTUALIZACION.php?actualizacion_archivo=<?php echo md5($row[0]); ?>';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
    $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.location='../GS_REGISTRO_ACTUALIZACION.php';
  });
</script>
<?php } ?>