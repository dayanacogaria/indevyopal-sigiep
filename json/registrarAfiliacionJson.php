<?php
#03/03/2017 --- Nestor B --- se agregaron condicionales en las validaciones de las fechas
#31/07/2017 --- Nestor B --- se agrego el registro de las afiliaciones en la tabla gn_historico_afiliacion 
require_once '../Conexion/conexion.php';
session_start();

$empleado        = '"'.$mysqli->real_escape_string(''.$_POST['sltEmpleado'].'').'"';
$emp        = ''.$mysqli->real_escape_string(''.$_POST['sltEmpleado'].'').'';
if($mysqli->real_escape_string(''.$_POST['sltTipo'].'')=="")
    $tipo = "null";
else
    $tipo            = $mysqli->real_escape_string(''.$_POST['sltTipo'].'');
if($mysqli->real_escape_string(''.$_POST['sltTercero'].'')=="")
    $tercero = "null";
else
    $tercero         = $mysqli->real_escape_string(''.$_POST['sltTercero'].'');
if($mysqli->real_escape_string(''.$_POST['txtCodigoA'].'')=="")
    $codigoadmin = "null";
else
    $codigoadmin     = $mysqli->real_escape_string(''.$_POST['txtCodigoA'].'');

#valida si la fecha de afiliación es vacía
if($mysqli->real_escape_string(''.$_POST['sltFechaA'].'')=="")
    $fechaafiliacion = "null";
else
{
    $fechaA = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaA'].'').'"';
    $fecha1 = trim($fechaA, '"');
    $fecha_div = explode("/", $fecha1);
    $anio1 = $fecha_div[2];
    $mes1 = $fecha_div[1];
    $dia1 = $fecha_div[0];
    $fechaafiliacion = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';
}

#valida si la fecha de retiro es vacía 
if(empty($_POST['sltFechaR'])||$mysqli->real_escape_string(''.$_POST['sltFechaR'].'')=="")
    $fecharetiro = "null";
else
{
    $fechaR = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaR'].'').'"';
    $fecha2 = trim($fechaR, '"');
    $fecha_div = explode("/", $fecha2);
    $anio2 = $fecha_div[2];
    $mes2 = $fecha_div[1];
    $dia2 = $fecha_div[0];
    $fecharetiro = '"'.$anio2.'-'.$mes2.'-'.$dia2.'"';    
}
if($mysqli->real_escape_string(''.$_POST['txtObservaciones'].'')=="")
    $observaciones = "null";
else
    $observaciones   = '"'.$mysqli->real_escape_string(''.$_POST['txtObservaciones'].'').'"';
if($mysqli->real_escape_string(''.$_POST['sltEstado'].'')=="")
    $estado = "null";
else
    $estado          = $mysqli->real_escape_string(''.$_POST['sltEstado'].'');


$retiro = "SELECT empleado, fecharetiro FROM gn_afiliacion WHERE empleado = '$emp' AND tipo = '$tipo'  AND fecharetiro IS NULL";
$reti = $mysqli->query($retiro);
$nret = mysqli_num_rows($reti);

if($nret > 0){
    
    $ret = mysqli_fetch_row($reti);
    
    if(empty($ret[1]) || $ret[1] == "" || $ret[1] == NULL ){
        $x = 1;
        $resultado = false;
    }else{
        
        $x = 0;
        
        $sql1 = "INSERT INTO gn_afiliacion(empleado,tipo,tercero,codigoadmin,fechaafiliacion,fecharetiro,observaciones,estado) VALUES
        ($empleado,$tipo,$tercero,$codigoadmin,$fechaafiliacion,$fecharetiro,$observaciones,$estado)";
        $resultado = $mysqli->query($sql1);

        $sql2 = "INSERT INTO gn_historico_afiliacion(tipo,tercero,empleado,fechaafiliacion) VALUES
                ($tipo,$tercero,$empleado,$fechaafiliacion)";
        $resultado = $mysqli->query($sql2);
        
        $resultado = true;
    }
}else{
    $x = 0;
    
    $sql1 = "INSERT INTO gn_afiliacion(empleado,tipo,tercero,codigoadmin,fechaafiliacion,fecharetiro,observaciones,estado) VALUES
            ($empleado,$tipo,$tercero,$codigoadmin,$fechaafiliacion,$fecharetiro,$observaciones,$estado)";
    $resultado = $mysqli->query($sql1);

    $sql2 = "INSERT INTO gn_historico_afiliacion(tipo,tercero,empleado,fechaafiliacion) VALUES
            ($tipo,$tercero,$empleado,$fechaafiliacion)";
    $resultado = $mysqli->query($sql2);
    
    if(empty($_POST['txtId']))
    {
        $las = "SELECT MAX(id_unico) FROM gn_afiliacion";
        $resultado = $mysqli->query($las);
        $rw = mysqli_fetch_row($resultado);
        $id = $rw[0];
    }else{
        $id = '"'.$mysqli->real_escape_string(''.$_POST['txtId'].'').'"';    
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
<body>
</body>
</html>
<!--Modal para informar al usuario que se ha registrado-->
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
  <!--Modal para informar al usuario que no se ha podido registrar -->
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <?php if($x == 1){ ?>
                    <p>No se ha podido guardar la información, debido que la anterior afiliacón no posee fecha de retiro.</p>
            <?php }else{ ?>       
                    <p>No se ha podido guardar la información.</p>
            <?php } ?>        
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--lnks para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');      
        window.location='../registrar_GN_AFILIACION.php?idE=<?php echo md5($_POST['sltEmpleado'])?>';
      //window.history.go(-1);
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
    $("#ver2").click(function(){
    $("#myModal2").modal('hide');      
       
      window.history.go(-1);
  });
</script>
<?php } 
?>