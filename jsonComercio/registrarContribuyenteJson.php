<?php
################################################################################
#
#Modificado por: Nestor B |03/11/2017| se agregó la validación para que no registre dos o mas cotribuyentes con un mismo tercero
#
################################################################################

session_start();
require_once ('../Conexion/conexion.php');



$codigoActual=$_POST['codigoActual'];

$tercero=$_POST['tercero'];

if (!empty($_POST['codigoAnterior'])) {
    $codigoAnterior='"'.$mysqli->real_escape_string(''.$_POST['codigoAnterior'].'').'"';      

}else{
    $codigoAnterior="null";
}

if (!empty($_POST['codigoPostal'])) {
    $codigoPostal='"'.$mysqli->real_escape_string(''.$_POST['codigoPostal'].'').'"';      
}else{
    $codigoPostal="null";
}

if (!empty($_POST['representanteLegal'])) {
    $representanteLegal='"'.$mysqli->real_escape_string(''.$_POST['representanteLegal'].'').'"';      

}else{
    $representanteLegal="null";
}

if (!empty($_POST['sltEst'])) {
    $estado='"'.$mysqli->real_escape_string(''.$_POST['sltEst'].'').'"';      
  
}else{
   $estado="null";
}

if (!empty($_POST['fechaini'])) {
  $fechaI=''.$mysqli->real_escape_string(''.$_POST['fechaini'].'').'';     
  $fecha_div = explode("/",$fechaI);
  $anio = $fecha_div[2];
  $mes  = $fecha_div[1];
  $dia  = $fecha_div[0];
  
  $fecI = ''.$anio.'-'.$mes.'-'.$dia.'';

}else{
  $fecI="null";
}

if (!empty($_POST['txtDirC'])) {
  $dirC='"'.$mysqli->real_escape_string(''.$_POST['txtDirC'].'').'"';      

}else{
  $dirC="null";
}

$validacion = "SELECT * FROM gc_contribuyente WHERE tercero = '$tercero'";
$vali = $mysqli->query($validacion);
$nval = mysqli_num_rows($vali);
if($nval < 1){
    $sql="INSERT INTO gc_contribuyente(codigo_mat,codigo_mat_ant,tercero,cod_postal,repre_legal, estado, fechainscripcion, dir_correspondencia) 
            VALUES('$codigoActual',$codigoAnterior,$tercero,$codigoPostal,$representanteLegal,$estado,'$fecI',$dirC)";
    $resultado=$mysqli->query($sql); 
    $x = 0;
}else{
    $resultado = false;
    $x = 1;
}
?>
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
                <?php   if ($x == 1){ ?>
                            <p>No se ha podido guardar la información,debido a que ya existe un contribuyente registrado con este tercero</p>
                <?php   }else{ ?>        
                            <p>No se ha podido guardar la información.</p>
                <?php   } ?>         
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

<?php   if($resultado == true){ ?>
            <script type="text/javascript">
                $("#myModal1").modal('show');
                $("#ver1").click(function(){
                    $("#myModal1").modal('hide');
                    window.location='../listar_GC_CONTRIBUYENTE.php';
                });
            </script>
<?php   }else{ ?>
            <script type="text/javascript">
                $("#myModal2").modal('show');
                $("#ver2").click(function(){
                    window.history.go(-1);
                });
            </script>
<?php   }
?>

