<?php
  require_once('../Conexion/conexion.php');
  session_start();
  
  $identificador= $mysqli->real_escape_string(''.$_POST['identificador'].'');
  $estado= $mysqli->real_escape_string(''.$_POST['estado'].'');
  $tipop= $mysqli->real_escape_string(''.$_POST['tipoProceso'].'');
  $tercero= $mysqli->real_escape_string(''.$_POST['responsable'].'');
  $proceso  = $mysqli->real_escape_string(''.$_POST['proceso'].'');
  $fechaI  = $mysqli->real_escape_string(''.$_POST['fecha'].'');
  $fechaI = DateTime::createFromFormat('d/m/Y', "$fechaI");
  $fechaI= $fechaI->format('Y/m/d');
  $id = "SELECT MAX(id_unico) FROM gg_proceso";
  $id = $mysqli->query($id);
  $id = mysqli_fetch_row($id);
  $id = $id[0];
  $id= $id+1;
  if ($proceso=='""'|| $proceso=='' || $proceso==NULL){
     
      $proceso='NULL'; 
      $procesoB='IS NULL'; 
  } else {
      
      $procesoB = '='.$proceso;
      
  }
  
 $queryU="SELECT * FROM gg_proceso "
          . "WHERE identificador=$identificador "
          . "AND tipo_proceso = '$tipop' "
          . "AND estado = '$estado' "
          . "AND proceso $procesoB "
          . "AND tercero = '$tercero' "
          . "AND fecha ='$fechaI'";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);

  if($num == 0)
  {
    $insert = "INSERT INTO gg_proceso (id_unico, identificador, tipo_proceso, estado, proceso,tercero, fecha) "
          . "VALUES($id, $identificador, $tipop, $estado, $proceso, $tercero, '$fechaI' )";
    $resultado = $mysqli->query($insert);
   }
  else
  {
    $resultado = false;
  }
  #REGISTRAR DETALLE 0
  if($resultado ==true){
      #BUSCAR FLUJO PROCESAL FASE=0 Y TIPO PROCESO SEA EL ESCOGIDO
      $fp = "SELECT fp.id_unico, fp.estado FROM gg_flujo_procesal fp "
              . "LEFT JOIN gg_fase f ON fp.fase = f.id_unico "
              . "LEFT JOIN gg_tipo_proceso tp ON fp.tipo_proceso = tp.id_unico "
              . "WHERE f.id_unico = '0' AND tp.id_unico ='$tipop'";
      $fp = $mysqli->query($fp);
      $fp = mysqli_fetch_row($fp);
      $fpr= $fp[0];
      if(!empty($fp[1])){
          $estado = $fp[1];
          $act="UPDATE gg_proceso SET estado ='$estado' WHERE id_unico ='$id'";
          $act = $mysqli->query($act);
      }
            $insertDetalle = "INSERT INTO gg_detalle_proceso ( proceso, flujo_procesal, fecha_programada, tercero, estadoA) "
                 . "VALUES('$id', '$fpr', '$fechaI', '$tercero','$estado')";
            $insertDetalle=$mysqli->query($insertDetalle);
      
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
        window.location='../GG_PROCESO.php?id=<?php echo md5($id)?>';
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