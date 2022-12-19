<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $id = $mysqli->real_escape_string(''.$_POST['id'].'');
  $concepto = '"'.$mysqli->real_escape_string(''.$_POST['concepto'].'').'"';
  $servicio  = '"'.$mysqli->real_escape_string(''.$_POST['servicio'].'').'"';
  
    $queryU="SELECT * FROM gp_concepto_servicio "
          . "WHERE concepto = $concepto "
          . "AND tipo_servicio  =$servicio ";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);

  if($num == 0)
  {
     $insert = "INSERT INTO gp_concepto_servicio (concepto, tipo_servicio) "
          . "VALUES($concepto, $servicio)";
  $resultado = $mysqli->query($insert);
   }
  else
  {
    $resultado = false;
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
                <p><?php
                    if($num != 0) 
                      echo "El registro ingresado ya existe.";
                    else
                      echo "No se ha podido guardar la informaci&oacuten.";
                  ?>
                  </p>
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
        window.location='../GP_CONCEPTO_SERVICIO.php?id=<?php echo md5($id)?>';
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