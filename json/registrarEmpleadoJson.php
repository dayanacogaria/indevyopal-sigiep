<?php
#03/03/2017 --- Nestor B --- se modificó la ruta para regrear "atrás"
#01/08/2017 --- Nestor B --- se agrego el campo de salario integral
require_once '../Conexion/conexion.php';
session_start();

$tercero            = '"'.$mysqli->real_escape_string(''.$_POST['sltTercero'].'').'"';
$codigointerno      = '"'.$mysqli->real_escape_string(''.$_POST['txtCodigoI'].'').'"';
$estado             =       $mysqli->real_escape_string(''.$_POST['sltEstado'].'');
$cesantias          =       $mysqli->real_escape_string(''.$_POST['sltCesantias'].'');
$mediopago          =       $mysqli->real_escape_string(''.$_POST['sltMedioP'].'');
$unidadejecutora    =       $mysqli->real_escape_string(''.$_POST['sltUnidadE'].'');
$grupogestion       =       $mysqli->real_escape_string(''.$_POST['sltGrupoG'].'');
$salInt             =       $mysqli->real_escape_string(''.$_POST['salaIn'].'');
$riesgo             =       $mysqli->real_escape_string(''.$_POST['sltRiesgos'].'');
$ter                = ''.$mysqli->real_escape_string(''.$_POST['sltTercero'].'').'';
$retro              =       $mysqli->real_escape_string(''.$_POST['Retro'].'');
$contrato           =       $mysqli->real_escape_string(''.$_POST['sltContrato'].'');

if($codigointerno=="")
    $codigo = "null";
else
    $codigo = $codigointerno;

if($estado=="")
    $est = "null";
else
    $est = $estado;

if($codigointerno=="")
    $codigo = "null";
else
    $codigo = $codigointerno;

if($cesantias=="")

    $ces = "null";
else
    $ces = $cesantias;

if($mediopago=="")
    $medio = "null";
else
    $medio = $mediopago;

if($unidadejecutora=="")
    $unidad = "null";
else
    $unidad = $unidadejecutora;

if($grupogestion=="")
    $grupo = "null";
else
    $grupo = $grupogestion;

$sql1 = "SELECT * FROM gn_empleado WHERE tercero = '$ter'";
$res2 = $mysqli->query($sql1);
$nres1 = mysqli_num_rows($res2);
echo "<br/>";
if($nres1 < 1){

  $x = 0;
  $sql = "INSERT INTO gn_empleado(tercero,codigointerno,estado,cesantias,mediopago,unidadejecutora,grupogestion,salInt,tipo_riesgo,equivalente_NE) VALUES 
          ($tercero,$codigo,$est,$ces,$medio,$unidad,$grupo,$salInt,$riesgo,$contrato)";
  $resultado = $mysqli->query($sql);


}  else{

  $x = 1;
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
                  <p>No se ha podido guardar la información. Debido a que  ya existe un empleado registrado con el mismo tercero.</p>   
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
    window.history.go(-2);
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
<?php } ?>