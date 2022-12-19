<?php

require_once '../Conexion/conexion.php';
require '../Dias_Incapacidad.php';
require_once '../jsonPptal/funcionesPptal.php';
@session_start();

$tiponovedad      = $_POST['txtidTip'];
$empleado         = $_POST['sltEmpleado'];
$numerodias       = $_POST['txtNumeroD'];

$fechaI           = fechaC($_POST['sltFechaI']);
$fechaF           = fechaC($_POST['sltFechaF']);
if(empty($_POST['sltFechaA'])){
    $fechaA = 'NULL';
} else {
    $fechaA = "'".fechaC($_POST['sltFechaA'])."'";
}

if(empty($_POST['txtNumeroI'])){
    $numeroinc = 'NULL';
} else {
    $numeroinc = $_POST['txtNumeroI'];
}



if(empty($_POST['sltAccidente']))
    $accidente = "null";
else
    $accidente           = '"'.$mysqli->real_escape_string(''.$_POST['sltAccidente'].'').'"';

if($mysqli->real_escape_string(''.$_POST['txtNumeroA'].'')=="")
    $numeroaprobacion = "null";
else
    $numeroaprobacion = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroA'].'').'"';


if($mysqli->real_escape_string(''.$_POST['txtDiagnostico'].'')=="")
    $diagnostico = "null";
else
    $diagnostico      = '"'.$mysqli->real_escape_string(''.$_POST['txtDiagnostico'].'').'"';





$sql = "INSERT INTO gn_incapacidad (empleado,tiponovedad,accidente,numeroinc,fechainicio,numerodias,numeroaprobacion,fechaaprobacion,diagnostico,fechafinal) VALUES 
        ($empleado,$tiponovedad,$accidente,$numeroinc,'$fechaI',$numerodias,$numeroaprobacion,$fechaA,$diagnostico,'$fechaF')";
        $resultado = $mysqli->query($sql);
    

    
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
            <?php if($x == 0){ ?>
                        <p>No se ha podido guardar la información.</p>
            <?php }else{ ?>
                        <p>No se ha podido guardar la información, debido a que el periodo en el que intenta registrar la novedad se encuentra cerrado</p>
            <?php } ?>            
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
      <!--Modal para informar al usuario que no se ha podido registrar la incapacidad porque los dias de la misma sobrepasan los dias faltantes del periodo y se necesita crear uno -->
  <div class="modal fade" id="myModal3" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido guardar la información, debido a que los día de la incapacidad sobrepasan a los días faltantes del periodo. 
              Por favor cree otro periodo para poder registrar la incapacidad.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
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
        window.location='../GN_INCAPACIDAD.php?idE=<?php echo ($_POST['sltEmpleado'])?>&tipo=<?php echo $tiponovedad ?>';
      //window.history.go(-1);
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
    $("#ver2").click(function(){
    $("#myModal2").modal('hide');      
         window.location='../GN_INCAPACIDAD.php?idE=<?php echo ($_POST['sltEmpleado'])?>&tipo=<?php echo $tiponovedad ?>';
  });
</script>
<?php } 
?>