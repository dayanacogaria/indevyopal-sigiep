<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $id  = $mysqli->real_escape_string(''.$_POST['id'].'');
  $flujop  = $mysqli->real_escape_string(''.$_POST['flujoprocesal'].'');
  $estado= $mysqli->real_escape_string(''.$_POST['estado'].'');
  $proceso = $mysqli->real_escape_string(''.$_POST['proceso'].'');
  $fechaP  = $mysqli->real_escape_string(''.$_POST['fecha_programada'].'');
  $fechaP = DateTime::createFromFormat('d/m/Y', "$fechaP");
  $fechaP= $fechaP->format('Y/m/d');
  
  $tercero= $mysqli->real_escape_string(''.$_POST['responsable'].'');
  $formaN= $mysqli->real_escape_string(''.$_POST['formaN'].'');
  $observaciones= $mysqli->real_escape_string(''.$_POST['observaciones'].'');
  
  if(!empty($_POST['fecha_ejecutada'])){
  $fechaE  = $mysqli->real_escape_string(''.$_POST['fecha_ejecutada'].'');
  $fechaE = DateTime::createFromFormat('d/m/Y', "$fechaE");
  $fechaE= $fechaE->format('Y/m/d');
  } else {
      $fechaE='NULL';
  }
   
  if(!empty($_POST['condicion'])) { 
  $condicion= $mysqli->real_escape_string(''.$_POST['condicion'].'');
    if($condicion=='' || $condicion=='NULL'){
        $condicion=1;
    }
  } else {
      $condicion=1;
  }
  
 $queryU="SELECT * FROM gg_detalle_proceso "
          . "WHERE proceso='$proceso' "
          . "AND flujo_procesal = '$flujop' "
          . "AND fecha_programada = '$fechaP' "
          . "AND fecha_ejecutada = '$fechaE' "
          . "AND tercero = '$tercero' "
          . "AND forma_notificacion ='$formaN'";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);

  if($num == 0)
  {
    $update = "UPDATE gg_detalle_proceso SET "
        . "fecha_programada='$fechaP', "
        . "fecha_ejecutada ='$fechaE', "
        . "tercero='$tercero', "
        . "forma_notificacion='$formaN', "
        . "observaciones='$observaciones' "
        . "WHERE id_unico = '$id'";
    
    $resultado= $mysqli->query($update);
    
   }
  else
  {
    $resultado = false;
  }
  # SI SE GUARDA FECHA EJECUTADA GUARDA EL SIGUIENTE FLUJO
  if(!empty($_POST['fecha_ejecutada'])){
      if($resultado==true){
          if($condicion==1){
              $flujosi="SELECT
                        fp.flujo_si,
                        fps.estado
                      FROM
                        gg_flujo_procesal fp
                      LEFT JOIN
                        gg_flujo_procesal fps ON fp.flujo_si = fps.id_unico
                      WHERE
                        fp.id_unico = '$flujop'";
              $flujosi = $mysqli->query($flujosi);
              $flujosi= mysqli_fetch_row($flujosi);
              $fg= $flujosi[0];
              $estadog=$flujosi[1];
          } else {
              $flujono="SELECT
                        fp.flujo_no,
                        fps.estado
                      FROM
                        gg_flujo_procesal fp
                      LEFT JOIN
                        gg_flujo_procesal fps ON fp.flujo_no = fps.id_unico
                      WHERE
                        fp.id_unico = '$flujop'";
              $flujono = $mysqli->query($flujono);
              $fg= $flujono[0];
              $estadog=$flujono[1];
          }
          if(empty($estadog)){
              $estadog = $estado;
          } else {
                $estadog=$estadog;
                $act="UPDATE gg_proceso SET estado ='$estadog' WHERE id_unico ='$proceso'";
                $act = $mysqli->query($act);
          }
          #GUARDAR EL SIGUIENTE PROCESO
          $insertDetalle = "INSERT INTO gg_detalle_proceso ( proceso, flujo_procesal, fecha_programada, tercero, estadoA) "
                 . "VALUES('$proceso', '$fg', '$fechaE', '$tercero','$estadog')";
          $insertDetalle=$mysqli->query($insertDetalle);
      }
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
        window.location='../GG_DETALLE_PROCESO.php?id=<?php echo md5($proceso)?>';
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