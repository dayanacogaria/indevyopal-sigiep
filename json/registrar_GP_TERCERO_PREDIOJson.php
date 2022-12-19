<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $predio = $mysqli->real_escape_string(''.$_POST['predio'].'');
  $tercero  = '"'.$mysqli->real_escape_string(''.$_POST['Tercero'].'').'"';
  $propietario= '"'.$mysqli->real_escape_string(''.$_POST['Propietario'].'').'"';
  if(empty($_POST['porcentaje'])){
      $porcentaje = 'NULL';
      $por=0;
  } else {
      $porcentaje = $_POST['porcentaje'];
      $por=$porcentaje;
  }
$porcentajeB = "SELECT SUM(porcentaje) FROM gp_tercero_predio WHERE predio = '$predio'";
$porcentajeB = $mysqli->query($porcentajeB);
$porcentajeB = mysqli_fetch_row($porcentajeB);
$porcentajeB = $porcentajeB[0];

$total = $porcentajeB+$por;
if($total>100){
    $resultado=false;
    $var=1;
} else {
    $insert = "INSERT INTO gp_tercero_predio (predio, tercero, propietario, porcentaje) VALUES('$predio', $tercero,$propietario, $porcentaje )";
    $resultado = $mysqli->query($insert);
    $var=0;
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
                <?php if ($var==1) { ?>
                <p>El porcentaje asignado es superior a 100.</p>
                <?php }  else { ?>
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
        window.location='../GP_TERCERO_PREDIO.php?id=<?php echo md5($predio)?>';
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