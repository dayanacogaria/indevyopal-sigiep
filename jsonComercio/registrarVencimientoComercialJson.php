<?php


session_start();
require_once ('../Conexion/conexion.php');

$idv=$_POST['vigenciaa'];

$sv="SELECT id_unico,vigencia FROM gc_anno_comercial WHERE md5(id_unico)='$idv'";
$rv=$mysqli->query($sv);
$fv=mysqli_fetch_row($rv);

$vigencia=$fv[0];

$fechaInicial=$_POST['fechaInicial'];
$fechaI = DateTime::createFromFormat('d/m/Y', "$fechaInicial");
$fechaI= $fechaI->format('Y/m/d');

$fechaFinal=$_POST['fechaFinal'];
$fechaF = DateTime::createFromFormat('d/m/Y', "$fechaFinal");
$fechaF= $fechaF->format('Y/m/d');

$fechaInicioInteres=$_POST['fechaInicioInteres'];
$fechaII = DateTime::createFromFormat('d/m/Y', "$fechaInicioInteres");
$fechaII= $fechaII->format('Y/m/d');

$fechaLimiteDeclaracion=$_POST['fechaLimiteDeclaracion'];
$fechaLD = DateTime::createFromFormat('d/m/Y', "$fechaLimiteDeclaracion");
$fechaLD= $fechaLD->format('Y/m/d');



$sql="INSERT INTO gc_vencimiento_comercial (fecha_inicial, fecha_final, fecha_inicio_inter, fecha_limite_decl, vigencia)  VALUES ('$fechaI', '$fechaF', '$fechaII', '$fechaLD',$vigencia)";

$resultado=$mysqli->query($sql); ?>
 <html>
  	<head>
   		<meta charset="utf-8">
   		<meta name="viewport" content="width=device-width,initial-scale=1">
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

  <?php if($resultado == true){ ?>
  <script type="text/javascript">
    	$("#myModal1").modal('show');
    	$("#ver1").click(function(){
      	$("#myModal1").modal('hide');
        window.history.go(-1);
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
 ?>

