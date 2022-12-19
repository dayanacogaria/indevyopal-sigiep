<?php
require_once '../Conexion/conexion.php';
require '../Dias_Incapacidad.php';
session_start();

$codigointerno       = '"'.$mysqli->real_escape_string(''.$_POST['txtCodigoI'].'').'"';
$acumulable          =     $mysqli->real_escape_string(''.$_POST['es_acumulable'].'');
$estado              =     $mysqli->real_escape_string(''.$_POST['sltEstado' ].'');
$tipoprocesonomina   =     $mysqli->real_escape_string(''.$_POST['sltTipoPN'].'');
$parametrizacionanno = '"'.$_SESSION['anno'].'"';
$diasnom             =''.$mysqli->real_escape_string(''.$_POST['dialiq'].'');
$periodoRetro        =     $mysqli->real_escape_string(''.$_POST['sltPeriodoR'].'');

if(empty($acumulable))
    $acum = "null";
else
    $acum = $acumulable;    

if(empty($estado))
    $est = "null";
else
    $est = $estado;

if(empty($tipoprocesonomina))
    $tipo = "null";
else
    $tipo = $tipoprocesonomina;

if(empty($periodoRetro))
    $periodoRetro = "null";
else
    $periodoRetro = $periodoRetro;

#valida si la fecha inicial es vacía
if($mysqli->real_escape_string(''.$_POST['sltFechaI'].'')==""){
  $fechainicio='null';
}else{
  $fec1      = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaI'].'').'"';
  $fecha1    = trim($fec1,'"');
  $fecha_div = explode("/", $fecha1);
  $anio1 = $fecha_div[2];
  $mes1 = $fecha_div[1];
  $dia1 = $fecha_div[0];  
  $fechainicio = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';  
 
}

if($mysqli->real_escape_string(''.$_POST['sltFechaF'].'')==""){
  $fechafin='null';
}else{
  $fec2     = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaF'].'').'"';
  $fecha2    = trim($fec2,'"');
  $fecha_div = explode("/", $fecha2);
  $anio1 = $fecha_div[2];
  $mes1 = $fecha_div[1];
  $dia1 = $fecha_div[0];  
  $fechafin = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';  
 
}

$per = "SELECT * FROM gn_periodo WHERE tipoprocesonomina = '$tipo'";
$resp = $mysqli->query($per);
$nrw = mysqli_num_rows($resp);

if($nrw > 0){    
    
    $las = "SELECT MAX(id_unico) FROM gn_periodo WHERE tipoprocesonomina = '$tipo'";
    $resultado = $mysqli->query($las);
    $rw = mysqli_fetch_row($resultado);
    $id = $rw[0];

    $perant="SELECT id_unico,codigointerno,fechainicio,fechafin FROM gn_periodo  WHERE id_unico = $id";
    $res = $mysqli->query($perant);
    $nres = mysqli_num_rows($res);
    $pa = mysqli_fetch_row($res);

    if($nres >=1){
 
 	$fec3      = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaI'].'').'"';
 	$fecha3    = trim($fec1,'"');
  	$fecha_divi = explode("/", $fecha3);
 	$anio3 = $fecha_divi[2];
  	$mes3 = $fecha_divi[1];
  	$dia3 = $fecha_divi[0];  
  	$fechainic = ''.$anio3.'-'.$mes3.'-'.$dia3.'';  

	$fpa = DiasFecha($pa[3],2);
	$dias = (strtotime($fpa)-strtotime($fechainic))/86400;
	$dias = abs($dias);
	$dias = floor($dias);

	$x=1;
	//if($dias == 0){
  
  		$sql = "INSERT INTO gn_periodo (codigointerno,fechainicio,fechafin,acumulable,estado,tipoprocesonomina,parametrizacionanno,dias_nomina,liquidado,periodo_retro) VALUES "
        . "($codigointerno,$fechainicio,$fechafin,$acum,$est,$tipo,$parametrizacionanno,$diasnom,0,$periodoRetro)";
  		$resultado = $mysqli->query($sql);
  		$valor = 2;
	/*}else{
  
  		$resultado = false;
  		$x=2;
  		
	}*/
        
    }else{

	$sql = "INSERT INTO gn_periodo (codigointerno,fechainicio,fechafin,acumulable,estado,tipoprocesonomina,parametrizacionanno,dias_nomina,liquidado,periodo_retro) VALUES "
        . "($codigointerno,$fechainicio,$fechafin,$acum,$est,$tipo,$parametrizacionanno,$diasnom,0,$periodoRetro)";
  		$resultado = $mysqli->query($sql);
  		$valor = 2;
  		$x=1;
    }
}else{
    
    $sql = "INSERT INTO gn_periodo (codigointerno,fechainicio,fechafin,acumulable,estado,tipoprocesonomina,parametrizacionanno,dias_nomina,liquidado,periodo_retro) VALUES "
        . "($codigointerno,$fechainicio,$fechafin,$acum,$est,$tipo,$parametrizacionanno,$diasnom,0,$periodoRetro)";
  		$resultado = $mysqli->query($sql);
  		$valor = 2;
  		$x=1;
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
          <p>No se ha podido guardar la información.</p>
          <?php }else{ ?>
          <p>No se ha podido el registrar el periodo, debido a que la fecha inicial no coincide con el consecutivo de la fecha final del periodo anterior.</p>
          <?php } ?>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal3" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido el registrar el periodo, debido a que la fecha inicial no coincide con el consecutivo de la fecha final del periodo anterior.</p>
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