<?php 
session_start();
require_once ('../Conexion/conexion.php');
################################################################################################################
#Creación ########################
#Fecha: 10-02-2017
#Creado por: Jhon Numpaque
#Descripción: Archivo de procesos de registro
################################################################################################################
#Modificaciones
#
################################################################################################################
# Fecha: 14/03/2017
# Descripción: Se incluyo consulta de validación para  vretificar si ya habia un registro de partida, 
# el cual si existe se redirecciona a la partida 
################################################################################################################
# Fecha : 14/02/2017 | Jhon Numpaque
# Descripción: Cambio en la ruta de guardado de documento
#
################################################################################################################
#Captura de variables
#
################################################################################################################
$cuenta = '"'.$mysqli->real_escape_string(''.$_POST['sltCuenta'].'').'"';
$mes = '"'.$mysqli->real_escape_string(''.$_POST['sltMes'].'').'"';
################################################################################################################
# Consulta de validación de registro
# 
################################################################################################################
$sqlP = "SELECT id_unico
        FROM    gf_partida_conciliatoria 
        WHERE   id_cuenta = $cuenta AND mes= $mes";
$resultP = $mysqli->query($sqlP);
$cantidad = mysqli_num_rows($resultP);
$rowP = mysqli_fetch_row($resultP);
if($cantidad > 0) {  
  ################################################################################################################
  # Redireccionamiento de pagina
  #
  ################################################################################################################
  header("Location:../registrar_GF_PARTIDA_CONCILIATORIA.php?idPartida=".md5($rowP[0])."");
}else{
  ################################################################################################################
  # Captura de variables
  #
  ################################################################################################################
  $saldoE = '"'.$mysqli->real_escape_string(''.$_POST['txtSaldoE'].'').'"';
  ################################################################################################################
  # Validación de registro y desplazamiento de documento
  #
  ################################################################################################################
  if(!empty($_FILES['flArchivoC']['name'])){
  	$dir_subida = '../documentos/partidasConciliatorias/';
  	$doc = $_FILES['flArchivoC']['tmp_name'];	
  	$archivo = $dir_subida.basename($_FILES['flArchivoC']['name']);
  	@move_uploaded_file($doc,$archivo);
  }else{
  	$archivo = 'NULL';
  }
  ################################################################################################################
  # Validación de registro de campos vacios
  #
  ################################################################################################################
  if(!empty($_POST['txtDescripcion'])){
  	$descripcion = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';
  }else{
  	$descripcion = 'NULL';
  }
  ####################################################################################################################################################
  #Consulta de insertado
  $sqlI = "INSERT INTO gf_partida_conciliatoria(id_cuenta,descripcion,saldo_extracto,mes,archivo_extracto) VALUES($cuenta,$descripcion,$saldoE,$mes,'$archivo')";
  $resultI = $mysqli->query($sqlI);
  ####################################################################################################################################################
  #Consulta para obtener el último registro
  $sqlT = "SELECT MAX(id_unico) FROM gf_partida_conciliatoria";
  $resultT = $mysqli->query($sqlT);
  $idPartida = mysqli_fetch_row($resultT);
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

  <?php if($resultI==true){ ?>
  <script type="text/javascript">
    	$("#myModal1").modal('show');
    	$("#ver1").click(function(){
      	$("#myModal1").modal('hide');
      	window.location='../registrar_GF_PARTIDA_CONCILIATORIA.php?idPartida='+md5(<?php echo $idPartida[0]; ?>);
    	});
  </script>
  <?php }else{ ?>
  <script type="text/javascript">
    	$("#myModal2").modal('show');
    	$("#ver2").click(function(){
    		window.history.go(-1);
    	});
  </script>
<?php }
} ?>


