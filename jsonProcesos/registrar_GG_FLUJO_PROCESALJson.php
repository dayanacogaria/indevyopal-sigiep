<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $proceso  = $mysqli->real_escape_string(''.$_POST['proceso'].'');
  $fase= $mysqli->real_escape_string(''.$_POST['fase'].'');
  $duracion= $mysqli->real_escape_string(''.$_POST['duracion'].'');
  $tercero= $mysqli->real_escape_string(''.$_POST['tercero'].'');
  
  if(!empty($_POST['unidad'])){
     $unidad= $mysqli->real_escape_string(''.$_POST['unidad'].'');
  }else{
       $unidad='NULL';
  }
  if ($unidad=='""'|| $unidad=='' || $unidad==NULL || $unidad=='NULL'){
     
      $unidad='NULL'; 
      $unidadB='IS NULL'; 
  } else {
      
      $unidadB = '='.$unidad;
      
  }
  
  if(!empty($_POST['tipod'])){
     $tipod= '"'.$mysqli->real_escape_string(''.$_POST['tipod'].'').'"'; 
  }else{
       $tipod='NULL';
  }
  if ($tipod=='""'|| $tipod=='' || $tipod==NULL || $tipod=='NULL'){
     
      $tipod='NULL'; 
      $tipoB='IS NULL'; 
  } else {
      
      $tipoB = '='.$tipod;
      
  }
  if ($duracion=='""'|| $duracion=='' || $duracion==NULL){
    $duracion=0; 
  }
  if ($tercero=='""'|| $tercero=='' || $tercero==NULL){
    $tercero='NULL'; 
    $terceroB='IS NULL'; 
  } else {
     $terceroB='='.$tercero;
  }
  
  if(!empty($_POST['estado'])){
     $estado= $mysqli->real_escape_string(''.$_POST['estado'].'');
  }else{
      if(!empty($_POST['estado2'])){
       $estado= $mysqli->real_escape_string(''.$_POST['estado2'].'');
      } 
      else {
        $estado='NULL';  
      }
      
  }
  if ($estado=='""'|| $estado=='' || $estado==NULL || $estado=='NULL'){
    $estado='NULL'; 
    $estadoB='IS NULL'; 
  } else {
     $estadoB='='.$estado;
  }
 $queryU="SELECT * FROM gg_flujo_procesal "
          . "WHERE tipo_proceso = '$proceso' "
          . "AND fase = '$fase' "
          . "AND duracion='$duracion' "
          . "AND tipo_dia $tipoB "
          . "AND unidad_tiempo $unidadB "
          . "AND tercero $terceroB "
          . "AND estado $estadoB";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);

  if($num == 0)
  {
    $insert = "INSERT INTO gg_flujo_procesal (tipo_proceso, fase, duracion, tipo_dia, tercero, unidad_tiempo, estado) "
          . "VALUES($proceso,$fase, $duracion, $tipod, $tercero, $unidad, $estado)";
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
        window.location='../GG_FLUJO_PROCESAL.php';
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