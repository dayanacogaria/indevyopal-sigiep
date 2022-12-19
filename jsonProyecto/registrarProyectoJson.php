<?php
session_start();
require_once '../Conexion/conexion.php';
$compania   = $_SESSION['compania'];
$sqlC = "SELECT MAX(id_unico) FROM `gf_proyecto` WHERE id_unico<'2147483647'";
$resultC=$mysqli->query($sqlC);
$val = mysqli_fetch_row($resultC);
$nombre= '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
$id_unico=$val[0]+1;
$sql="insert into gf_proyecto(id_unico,nombre,compania) values('$id_unico',$nombre,$compania)";
$result=$mysqli->query($sql);
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
<!--Modal para informar al usuario que no se podido registrar la informacion-->
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
<!--links para el estilo de la pagina-->
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada -->
<?php if($result==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../listar_GF_PROYECTO.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.location='../listar_GF_PROYECTO.php';
  });
</script>
<?php } ?>