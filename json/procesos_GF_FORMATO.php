<?php 
session_start();
require_once('../Conexion/conexion.php');
######################################################################
  /*
  *Modificó: Jhon Numpaque
  *Fecha: 09-02-2017 | 10:30 a.m
  *Descripciión: Se incluyo campo de selección esCheque para verificar
  *que el formato seleccionado sea un formato de cheque
  */
  ######################################################################   
#Captura de actión validación de envio por post o por get
if(!empty($_GET['action'])){
	$action = $_GET['action'];
}
if(!empty($_POST['action'])){
	$action = $_POST['action'];
}
#Validación de variable para eliminar
if($action=="eliminar"){
	#Captura de Id
	$id=$_GET['id'];
	#Consulta de eliminación
	$queryDel = "DELETE FROM gf_formato WHERE id_unico=$id";
	$resultDel= $mysqli->query($queryDel);
	#Impresión de valor devuelto
	echo json_encode($resultDel);
}
if($action=="registrar"){
	$nombre = '"'.$mysqli->real_escape_string(''.$_POST['txtNombre'].'').'"';
	$version = '"'.$mysqli->real_escape_string(''.$_POST['txtVersion'].'').'"';
	$fecha = $_POST['txtFechaVersion'];
	$fecha = explode("/",$fecha);
	$fecha = '"'.$fecha[2].'-'.$fecha[1].'-'.$fecha[0].'"';
	$descripcion = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';
  $esCheque = '"'.$mysqli->real_escape_string(''.$_POST['optCheque'].'').'"';
	$sqlI="INSERT INTO gf_formato(nombre,version,fechaVersion,descripcion,esCheque) VALUES($nombre,$version,$fecha,$descripcion,$esCheque)";
	$resultI=$mysqli->query($sqlI);
	?>
	<html>
		<head>
 			<meta charset="utf-8">
 			<meta name="viewport" content="width=device-width, initial-scale=1">
 			<link rel="stylesheet" href="../css/bootstrap.min.css">
			<link rel="stylesheet" href="../css/style.css">			 
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
          			<p>No se ha podido guardar la información.</p>
        		</div>
        		<div id="forma-modal" class="modal-footer">
          			<button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        		</div>
      		</div>
		</div>
  	</div>
	<!--lnks para el estilo de la pagina-->	
  	<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  	<script src="../js/bootstrap.min.js"></script>
	<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->
	<?php if($resultI==true){ ?>
		<script type="text/javascript">
  			$("#myModal1").modal('show');
  			$("#ver1").click(function(){
    			$("#myModal1").modal('hide');
    			window.location='../listar_GF_FORMATO.php';
			});
		</script>
	<?php }else{ ?>
		<script type="text/javascript">
  			$("#myModal2").modal('show');
  			$("#ver2").click(function(){
  				$("#ver2").modal('hide');
  				window.location='../listar_GF_FORMATO.php';
  			});
		</script>
	<?php } ?>
<?php 	
}
if($action=="modificar"){
	$id='"'.$mysqli->real_escape_string(''.$_POST['txtId'].'').'"';
	$nombre = '"'.$mysqli->real_escape_string(''.$_POST['txtNombre'].'').'"';
	$version = '"'.$mysqli->real_escape_string(''.$_POST['txtVersion'].'').'"';
	$fecha = $_POST['txtFechaVersion'];
	$fecha = explode("/",$fecha);
	$fecha = '"'.$fecha[2].'-'.$fecha[1].'-'.$fecha[0].'"';
	$descripcion = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';
  $esCheque = '"'.$mysqli->real_escape_string(''.$_POST['optCheque'].'').'"';
	$sqlM="UPDATE gf_formato SET nombre=$nombre,version=$version,fechaversion=$fecha,descripcion=$descripcion,esCheque=$esCheque WHERE id_unico=$id";
	$resultM=$mysqli->query($sqlM);
	?>	
	<html>
		<head>
	 		<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="stylesheet" href="../css/bootstrap.min.css">
			<link rel="stylesheet" href="../css/style.css">			
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
          			<p>No se ha podido modificar la información.</p>
        		</div>
        		<div id="forma-modal" class="modal-footer">
          			<button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        		</div>
      		</div>
    	</div>
  	</div>	
  	<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  	<script src="../js/bootstrap.min.js"></script>
	<?php if($resultM==true){ ?>
	<script type="text/javascript">
  		$("#myModal1").modal('show');
  		$("#ver1").click(function(){
    		$("#myModal1").modal('hide');
    		window.location='../listar_GF_FORMATO.php';
  		});
	</script>
	<?php }else{ ?>
		<script type="text/javascript">
  			$("#myModal2").modal('show');
  			$("#ver2").click(function(){
    			$("#myModal2").modal('hide');
    			window.location='../listar_GF_FORMATO.php';
  			});
		</script>
	<?php } 
}
?>