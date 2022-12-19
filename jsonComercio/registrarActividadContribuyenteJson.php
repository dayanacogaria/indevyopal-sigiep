<?php


session_start();
require_once ('../Conexion/conexion.php');


$contribuyente=$_POST['contribuyente'];
$actividadComercial=$_POST['actividadComercial'];
$fechaInicio=$_POST['fechaInicio'];



$fechaI = DateTime::createFromFormat('d/m/Y', "$fechaInicio");
$fechaI= $fechaI->format('Y/m/d');

if (!empty($_POST['fechaCierre'])) {

    $fechaCierre=$_POST['fechaCierre'];    
    $fechaC = DateTime::createFromFormat('d/m/Y', "$fechaCierre");
    $fechaC= $fechaC->format('Y/m/d');  

    $sql="INSERT INTO gc_actividad_contribuyente (contribuyente,actividad,fechainicio,fechacierre) VALUES($contribuyente,$actividadComercial,'$fechaI','$fechaC')";

}else{
    $fechaC="null";

    $sql="INSERT INTO gc_actividad_contribuyente (contribuyente,actividad,fechainicio,fechacierre) VALUES($contribuyente,$actividadComercial,'$fechaI',$fechaC)";  
}






$resultado=$mysqli->query($sql); ?>
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

  <?php if($resultado == true){ ?>
  <script type="text/javascript">
    	$("#myModal1").modal('show');
    	$("#ver1").click(function(){
      	$("#myModal1").modal('hide');
      	window.location='../listar_GC_ACTIVIDAD_CONTRIBUYENTE.php';
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

