<?php 
session_start();
require_once ('../Conexion/conexion.php');
####################################################################################################################################################################
# Creación: 14/02/2017
# Creado : Jhon Numpaque
###################################################################################################################################################################
# Modifiaciones
####################################################################################################################################################################
# Modificado por : Jhon Numpaque
# Fecha      : 23/02/2017
# Descripción  : Se cambio guardado de valor (+) como (-), en el guardado de cuentas por pagar
####################################################################################################################################################################
# Jhon Numpaque 15 | 02| 2017 
# Hora: 4:57
# Descripción : Se incluyo el campo cuentas por pagar el cual tiene los comprobantes tipo 4 y las clases cuentas que se traen son las 4,8,9. Y se valido si este
# tiene valor que realize el registro del detalle la cuenta por pagar seleccionada, y solo se registrar cuando el registro fue exitoso
###################################################################################################################################################################
# Captura de variables
$fecha = explode("/",$_POST['txtFecha']);
$fecha = "'"."$fecha[2]-$fecha[1]-$fecha[0]"."'";
$tipoC = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoCom'].'').'"';
$numC  =$_POST['txtNumeroCom'];
$terC  = '"'.$mysqli->real_escape_string(''.$_POST['sltTercero'].'').'"';
$banco = $_POST['sltBanco'];
$estado = "1";
$compania = $_SESSION['compania'];
$param = $_SESSION['anno'];
####################################################################################################################################################################
# Validación de valores vacios o nulos
if(!empty($_POST['txtDescripcion'])){
	$descC = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';
}else{
	$descC = 'NULL';
}
if(!empty($_POST['sltTipoCon'])){
	$tipoCon = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoCon'].'').'"';
}else{
	$tipoCon = 'NULL';
}
if(!empty($_POST['txtNumeroC'])){
	$numCom = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroC'].'').'"';
}else{
	$numCom = 'NULL';
}
$numC = trim($numC);
###################################################################################################################################################################
# Consulta de insertado
 $sqlI = "INSERT INTO gf_comprobante_cnt(numero,fecha,descripcion,numerocontrato,tipocomprobante,compania,parametrizacionanno,tercero,estado,clasecontrato) VALUES('$numC',$fecha,$descC,$numCom,$tipoC,$compania,$param,$terC,$estado,$tipoCon)";
$resultI = $mysqli->query($sqlI);
###################################################################################################################################################################
# Consulta de ultimo valor consultado
$sqlT = "SELECT MAX(id_unico) FROM gf_comprobante_cnt WHERE tipocomprobante=$tipoC AND numero=$numC";
$resultT = $mysqli->query($sqlT);
$val = mysqli_fetch_row($resultT);
$egreso = $val[0];
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
          		<p>No se ha podido guardar la información.</p>
        	</div>
        	<div id="forma-modal" class="modal-footer">
          		<button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        	</div>
      	</div>
    </div>
</div>
<script type="text/javascript" src="../js/md5.pack.js"></script>
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>
<?php if($resultI==true){ 
###################################################################################################################################################################
# Captura de parametro cuentas por pagar
if(!empty($_POST['sltCuentasPagar'])){
  $cuentasPagar = '"'.$mysqli->real_escape_string(''.$_POST['sltCuentasPagar'].'').'"';
  $sqlCP = "SELECT    dtc.valor,
                      dtc.cuenta,
                      dtc.naturaleza,
                      dtc.tercero,
                      dtc.centrocosto,
                      dtc.proyecto,
                      dtc.id_unico
            FROM      gf_comprobante_cnt cnt
            LEFT JOIN gf_tipo_comprobante tpc     ON tpc.id_unico     = cnt.tipocomprobante
            LEFT JOIN gf_detalle_comprobante dtc  ON dtc.comprobante  = cnt.id_unico
            LEFT JOIN gf_cuenta cta               ON dtc.cuenta       = cta.id_unico
            WHERE     cnt.id_unico      =   $cuentasPagar
            AND       cta.clasecuenta   IN  (4,8,9)";
  $resultCP = $mysqli->query($sqlCP);
  $c = mysqli_num_rows($resultCP);
  while ($rowCP = mysqli_fetch_row($resultCP)) {
    #$valor =$rowCP[0]>0?$rowCP[0]*-1:$rowCP[0];
    $html = "INSERT INTO  gf_detalle_comprobante(fecha, descripcion, valor, comprobante, cuenta, naturaleza, tercero, proyecto, centrocosto, detalleafectado) VALUES($fecha, $descC, $rowCP[0], $egreso, $rowCP[1], $rowCP[2], $rowCP[3], $rowCP[5], $rowCP[4], $rowCP[6]);";
    $resultDC = $mysqli->query($html);
  }
}
?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    <?php if(!empty($_POST['sltBanco']) || $_POST['sltBanco']=='""' || $_POST['sltBanco'] =="''"){ ?>
      window.location='../registrar_GF_EGRESO_TESORERIA.php?egreso=<?php echo md5($egreso) ?>&banco='+md5(<?php echo $banco; ?>);
    <?php }else{ ?>
      window.location='../registrar_GF_EGRESO_TESORERIA.php?egreso=<?php echo md5($egreso) ?>';
    <?php } ?>
    //
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
