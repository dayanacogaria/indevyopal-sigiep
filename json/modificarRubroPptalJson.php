<!-- Historial de Actualizaciones  (ctl+F con la fecha para acceso rápido)
##################MODIFICACIONES#########################
#04/05/2017 | Erica G. | Validar Modificacion
    08/02/2017 - Daniel N: Se impide modificación de código ya ingresado, difernente al del registro consultado.
    09/02/2017 - Alexander N: Cuando la inserción es rechazada, regresa a la pantalla anterior.
-->
<?php
#Llamamos a la clase conexión
require_once ('../Conexion/conexion.php');
#Iniciamos la sesion
session_start();
#Capturamos los parametros 
$id     = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
$nombre = '"'.$mysqli->real_escape_string(''.$_POST["txtNombre"].'').'"';
$codigp = '"'.$mysqli->real_escape_string(''.$_POST["txtCodigoP"].'').'"';
$mov    = '"'.$mysqli->real_escape_string(''.$_POST["optMov"].'').'"';
$manpac = '"'.$mysqli->real_escape_string(''.$_POST["optManP"].'').'"';
$vigenc = '"'.$mysqli->real_escape_string(''.$_POST["sltVigencia"].'').'"';

$tipoCl = '"'.$mysqli->real_escape_string(''.$_POST["sltTipoClase"].'').'"';
$destin = '"'.$mysqli->real_escape_string(''.$_POST["sltDestino"].'').'"';
$tipoVi = '"'.$mysqli->real_escape_string(''.$_POST["stlTipoVigencia"].'').'"';

#Consulta de edición
if($_POST["sltPredecesor"]==""){
  $predec="NULL"; 
}else{  
  $predec = '"'.$mysqli->real_escape_string(''.$_POST["sltPredecesor"].'').'"';
}

if (empty($_POST["stlSector"])){
   $sector="NULL"; 
}else{
  $sector = '"'.$mysqli->real_escape_string(''.$_POST["stlSector"].'').'"';
}
if (empty($_POST["equivalente"])){
  $equivalencia="NULL"; 
}else{
  $equivalencia = '"'.$mysqli->real_escape_string(''.$_POST["equivalente"].'').'"';
}

if (empty($_POST["txtDinamica"])){
  $dinamc="NULL"; 
}else{
  $dinamc = '"'.$mysqli->real_escape_string(''.$_POST["txtDinamica"].'').'"';
}


 
$con="SELECT codi_presupuesto FROM gf_rubro_pptal where id_unico = $id";
  $rc = $mysqli->query($con);  
  $rowc=mysqli_fetch_row($rc);
  $rs = "";
  $error = "";
  
  $t = '"'.$rowc[0].'"';

  echo $t;
  echo $codigp;
  if($t==$codigp)      
 {    
      $sql = "UPDATE gf_rubro_pptal 
              SET nombre=$nombre, codi_presupuesto=$codigp, movimiento=$mov, manpac=$manpac, vigencia=$vigenc, dinamica=$dinamc, tipoclase=$tipoCl, predecesor=$predec, destino=$destin, tipovigencia=$tipoVi, sector=$sector, equivalente= $equivalencia WHERE id_unico=$id";
  $rs = $mysqli->query($sql); 
      
 }
else
{
 $con="SELECT codi_presupuesto FROM gf_rubro_pptal where codi_presupuesto = $codigp";
  $rc = $mysqli->query($con);
  $rowcount=mysqli_num_rows($rc);
  $rs = "";

  if($rowcount<=0)      
 {     
  $sql = "UPDATE gf_rubro_pptal 
              SET nombre=$nombre, codi_presupuesto=$codigp, movimiento=$mov, manpac=$manpac, vigencia=$vigenc, dinamica=$dinamc, tipoclase=$tipoCl, predecesor=$predec, destino=$destin, tipovigencia=$tipoVi, sector=$sector, equivalente= $equivalencia WHERE id_unico=$id"; 
  $rs = $mysqli->query($sql);
 }
else
    {
        $error = " El código ingresado ya está en uso";
        $rs ="";
    }
      
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
<!--Modal para informar al usuario que se ha modificado-->
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
  <!--Modal para informar al usuario que no se pudo modificar la información-->
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido modificar la información.<?php echo $error?></p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--Links para darle estilo a la página-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Muestra nuevamente la página de listar para mostrar la información modificada-->
<?php if($rs==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../listar_GF_RUBRO_PPTAL.php';
  });
</script>
<?php }else{ ?>
<!-- Actualización 09/02/2017) -->
<script type="text/javascript">
  $("#myModal2").modal('show');
    $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.history.go(-1);
  });
</script>
<!-- Fin Actualización 09/02/2017) -->
<?php } ?>