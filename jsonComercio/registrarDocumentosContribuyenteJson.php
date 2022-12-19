<?php


session_start();
require_once ('../Conexion/conexion.php');


$contribuyente=$_POST['contribuyente'];
$consultaId="SELECT id_unico FROM gc_contribuyente WHERE md5(id_unico)='$contribuyente'";
$rid=$mysqli->query($consultaId);
$id=mysqli_fetch_array($rid);
$idContri=$id[0];

if (!empty($_FILES['txtRuta']['name'])) {
    
    $direccion_subida='../documentos/documentosContribuyentes/';
    $documento=$_FILES['txtRuta']['tmp_name'];
    $archivo=$direccion_subida.basename($_FILES['txtRuta']['name']);
    @move_uploaded_file($documento,$archivo);
    
}else{
    $archivo='NULL';
}



if (!empty($_POST['tipoDocumento'])) {
  # code...
    $tipoDocumento=$_POST['tipoDocumento'];
}else{
    $tipoDocumento="NULL";
}

if (!empty($_POST['fechaExpedicion'])) {
  # code...
    $fechaExpedicion=$_POST['fechaExpedicion'];
    $fechaC = DateTime::createFromFormat('d/m/Y', "$fechaExpedicion");
    $fechaC= $fechaC->format('Y/m/d');  

     $sql="INSERT INTO gc_documento_contribuyente (contribuyente,tipo_doc,ruta,fecha_exp) VALUES($idContri,$tipoDocumento,'$archivo','$fechaC')";

}else{
    $fechaC="null";
    $sql="INSERT INTO gc_documento_contribuyente (contribuyente,tipo_doc,ruta,fecha_exp) VALUES($idContri,$tipoDocumento,'$archivo',$fechaC)";   
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

