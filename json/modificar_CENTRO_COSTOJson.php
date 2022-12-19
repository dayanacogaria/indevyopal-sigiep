<?php
########################################################################################
#       ***************    Modificaciones *************** #
########################################################################################
#14/12/2017 | Parametrizacion y compañia- Redireccionamiento
########################################################################################
  require_once('../Conexion/conexion.php');
  session_start();

  //Captura de datos e instrucción SQL para su modificación en la tabla gf_centro_cosoto.
  $id  = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
  $nombre = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
  $movimiento = $_POST['movimiento'];
  $sigla = '"'.$mysqli->real_escape_string(''.$_POST['sigla'].'').'"';
  $tipoCentCost = '"'.$mysqli->real_escape_string(''.$_POST['tipoCentCost'].'').'"';
  $predecesor = '"'.$mysqli->real_escape_string(''.$_POST['predecesor'].'').'"';
  $claseServ = '"'.$mysqli->real_escape_string(''.$_POST['claseServ'].'').'"';
  
  $cantidad = "NULL";
  if($movimiento == 1){
     $cantidad = '"'.$mysqli->real_escape_string(''.$_POST['cantidad'].'').'"';
  }  
  if($predecesor == '""')//Consulta si predecesor es vacío.
  {$predecesor ='NULL';
  }
    $updateSQL = "UPDATE gf_centro_costo
    SET Nombre = $nombre, Movimiento = $movimiento, Sigla = $sigla, 
       TipoCentroCosto = $tipoCentCost, Predecesor = $predecesor, 
        ClaseServicio = $claseServ, cantidad_distribucion = $cantidad 
    WHERE Id_Unico = $id";
    

  $resultado = $mysqli->query($updateSQL);  
  
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
          <p>Información modificada correctamente.</p>
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
          <p>No se ha podido modificar la información.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!-- Script que redirige a la página inicial de Centro Costo. -->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../CENTRO_COSTO.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.history.go(-1);
  });
</script>
<?php } ?>