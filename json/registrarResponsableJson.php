<?php
##################################################################################################################################################################
#                                                                                                                           Modificaciones
##################################################################################################################################################################
#24/08/2017 |Erica G. | Añadir campos orden y fechas
##################################################################################################################################################################

  //Llamado a la clase conexión
  require_once('../Conexion/conexion.php');
  require_once '../jsonPptal/funcionesPptal.php';
  //Creación de session
  session_start();
  //Variables de captura de datos enviados   
  $tipo_doc  = $mysqli->real_escape_string($_POST['TipoDocumento']);
  $tercero  = $mysqli->real_escape_string($_POST['tercero']);
  $tipo_res  = $mysqli->real_escape_string($_POST['TipoResponsable']);
  $tipo_rel  = $mysqli->real_escape_string($_POST['tipoRel']);
  $orden  = $mysqli->real_escape_string($_POST['orden']);
  $fechaI  = $mysqli->real_escape_string($_POST['fechaI']);
  $fechaI =fechaC( $fechaI);
  if(empty(($_POST['fechaF']))){
      $fechaF = 'NULL';
  } else {
      $fechaF =fechaC( $_POST['fechaF']);
      $fechaF = "'".$fechaF."'";
  }
  $queryU="SELECT * FROM gf_responsable_documento "
          . "WHERE tercero = '$tercero' "
          . "AND tipodocumento = '$tipo_doc' "
          . "AND tiporesponsable='$tipo_res' "
          . "AND tipo_relacion = '$tipo_rel' ";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);

  $insertSQL = "INSERT INTO gf_responsable_documento (tercero,tipodocumento,tiporesponsable, tipo_relacion, orden, fecha_inicio, fecha_fin) "
            . "VALUES($tercero,$tipo_doc,$tipo_res, $tipo_rel, $orden, '$fechaI', $fechaF)";
   $resultado = $mysqli->query($insertSQL);  

 
?>
<!-- Estructura de impresión de modales -->
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
            <p><?php
               if($num != 0) 
                 echo "El registro ingresado ya existe.";
               else
                 echo "No se ha podido guardar la informaci&oacuten.";
             ?>
             </p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

<?php
   if ($resultado ==true){ ?>
    <script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
   window.location='../GF_RESPONSABLE_DOCUMENTO.php?id1=<?php echo md5($tipo_doc);?>';
  });
</script>
  <?php } else {?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.location=window.history.back(-1);
  });
</script>
<?php } ?>