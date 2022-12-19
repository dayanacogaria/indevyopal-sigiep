<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $unidadV = $mysqli->real_escape_string(''.$_POST['uv'].'');
  $tipoServicio  = '"'.$mysqli->real_escape_string(''.$_POST['tiposervicio'].'').'"';
  $estadoServicio= '"'.$mysqli->real_escape_string(''.$_POST['estadoservicio'].'').'"';
  $select1 = "SELECT * FROM gp_unidad_vivienda_servicio WHERE unidad_vivienda=$unidadV AND tipo_servicio=$tipoServicio AND estado_servicio=$estadoServicio";
  $select2= $mysqli->query($select1);
  $select = mysqli_fetch_row($select2);

if(isset($select)){
  $resultado='2';

}else {
  $insert = "INSERT INTO gp_unidad_vivienda_servicio (unidad_vivienda, tipo_servicio, estado_servicio) "
         . "VALUES('$unidadV', $tipoServicio,$estadoServicio )";
  $result = $mysqli->query($insert);
  $resultado= '1';
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
<div class="modal fade" id="myModal3" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>El registro ingresado ya existe.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="../js/menu.js"></script>
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>

<?php if($resultado=='1'){ ?>
    <script type="text/javascript">
      $("#myModal1").modal('show');
      $("#ver1").click(function(){
        $("#myModal1").modal('hide');
        window.location='../GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo md5($unidadV)?>';
      });
    </script>
<?php }else{
if($resultado='2'){?>
 <script type="text/javascript">
      $("#myModal3").modal('show');
      $("#ver3").click(function(){
        $("#myModal3").modal('hide');
         window.location='../GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo md5($unidadV)?>';
      });
    </script>
<?php } else { ?>
    <script type="text/javascript">
      $("#myModal2").modal('show');
      $("#ver2").click(function(){
        $("#myModal2").modal('hide');
         window.location=window.history.back(-1);
      });
    </script>
<?php } }?>