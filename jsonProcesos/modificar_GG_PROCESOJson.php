<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $id= $mysqli->real_escape_string(''.$_POST['id'].'');
  $identificador= $mysqli->real_escape_string(''.$_POST['identificador'].'');
  $estado= $mysqli->real_escape_string(''.$_POST['estado'].'');
  $tipop= $mysqli->real_escape_string(''.$_POST['tipoProceso'].'');
  $tercero= $mysqli->real_escape_string(''.$_POST['responsable'].'');
  $proceso  = $mysqli->real_escape_string(''.$_POST['proceso'].'');
  $fechaI  = $mysqli->real_escape_string(''.$_POST['fecha'].'');
  $fechaI = DateTime::createFromFormat('d/m/Y', "$fechaI");
  $fechaI= $fechaI->format('Y/m/d');
  
  if ($proceso=='""'|| $proceso=='' || $proceso==NULL){
     
      $proceso='NULL'; 
      $procesoB='IS NULL'; 
  } else {
      
      $procesoB = '='.$proceso;
      
  }
 $queryUA="SELECT estado, tipo_proceso, tercero, proceso, identificador FROM gg_proceso "
          . "WHERE id_unico = '$id'";
  $carA = $mysqli->query($queryUA);
  $numA=  mysqli_fetch_row($carA);
  
 $queryU="SELECT * FROM gg_proceso "
          . "WHERE identificador=$identificador "
          . "AND tipo_proceso = '$tipop' "
          . "AND estado = '$estado' "
          . "AND proceso $procesoB "
          . "AND tercero = '$tercero' "
          . "AND fecha ='$fechaI'";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);
  if($numA[3]=='' || $numA[3]=='""'|| empty($numA[3])){ $numA[3]='NULL';}

  if($numA[0]==$estado && $numA[1]==$tipop && $numA[2]==$tercero && $numA[3]==$proceso && $numA[4]==$identificador){
        $insert = "UPDATE gg_proceso "
                  . "SET identificador = $identificador, "
                  . "tipo_proceso = $tipop, "
                  . "estado=$estado, "
                  . "proceso =$proceso,"
                  . "tercero=$tercero, "
                  . "fecha ='$fechaI' WHERE id_unico = $id";
         $resultado = $mysqli->query($insert);
  } else  { 
        if($num == 0)
        {
          $insert = "UPDATE gg_proceso "
                  . "SET identificador = $identificador, "
                  . "tipo_proceso = $tipop, "
                  . "estado=$estado, "
                  . "proceso =$proceso,"
                  . "tercero=$tercero,  "
                  . "fecha ='$fechaI' WHERE id_unico = $id";
         $resultado = $mysqli->query($insert);
         }
        else
        {
          $resultado = false;
        }
  }

//  
  
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