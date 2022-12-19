<?php
##########################################################################################
# *********************************** Modificaciones *********************************** # 
##########################################################################################
#05/02/2018 | Erica G. | Equivalente Vigencia Anterior
#01/02/2017 | 10:30 ERICA GONZÁLEZ. //Cambiar redireccionamiento
##########################################################################################

#Llamado a la clase de conexión
require_once '../Conexion/conexion.php';
session_start();
#Definimos variables y datos para realizar la consulta de actualización
$id  = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
$codiC  = '"'.$mysqli->real_escape_string(''.$_POST['txtCodigoC'].'').'"';
$nombre  = '"'.$mysqli->real_escape_string(''.$_POST['txtNombre'].'').'"';
$natural  = '"'.$mysqli->real_escape_string(''.$_POST['sltNaturaleza'].'').'"';
$claseC  = '"'.$mysqli->real_escape_string(''.$_POST['sltClaseC'].'').'"';
$mov  = '"'.$mysqli->real_escape_string(''.$_POST['optMov'].'').'"';
$cen  = '"'.$mysqli->real_escape_string(''.$_POST['optCentro'].'').'"';
$auxT  = '"'.$mysqli->real_escape_string(''.$_POST['optAuxT'].'').'"';
$auxP  = '"'.$mysqli->real_escape_string(''.$_POST['optAuxP'].'').'"';
$activ  = '"'.$mysqli->real_escape_string(''.$_POST['optAct'].'').'"';
$din  = '"'.$mysqli->real_escape_string(''.$_POST['txtDinamica'].'').'"';

if(!empty($_POST['sltTipoCuentaCgn'])){
    $tipoCGN  = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoCuentaCgn'].'').'"';
}else{
    $tipoCGN  = 'NULL';
}


if (!empty($_POST['sltPredecesor'])) {
    $pre  = '"'.$mysqli->real_escape_string(''.$_POST['sltPredecesor'].'').'"';        
}else{
    $pre  = 'NULL';
}
if($_POST['sltEquivalente']=='""' || empty($_POST['sltEquivalente'])){
    $equivalente  = 'NULL';
}else{
    $equivalente  = '"'.$mysqli->real_escape_string(''.$_POST['sltEquivalente'].'').'"';
}
if($_POST['terceroR']=='""' || empty($_POST['terceroR'])){
    $terceroR  = 'NULL';
}else{
    $terceroR  = '"'.$mysqli->real_escape_string(''.$_POST['terceroR'].'').'"';
}
$sql = "UPDATE gf_cuenta SET codi_cuenta=$codiC,nombre=$nombre,naturaleza=$natural,"
        . "clasecuenta=$claseC,movimiento=$mov,centrocosto=$cen,auxiliartercero=$auxT,"
        . "auxiliarproyecto=$auxP,activa=$activ,dinamica=$din,tipocuentacgn=$tipoCGN,"
        . "predecesor=$pre, equivalente_va = $equivalente, tercero_reciproca=$terceroR WHERE id_unico = $id";
$rs=$mysqli->query($sql);
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
          <p>No se ha podido modificar la información.</p>
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
    window.location='../buscarCuenta.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.location='../buscarCuenta.php';
  });
</script>
<?php } ?>