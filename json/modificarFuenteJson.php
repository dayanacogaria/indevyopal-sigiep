<?php
###########################################################################################################
#                           MODIFICACIONES
#05/07/2017 |ERICA G. | PARAMETRIZACION                            
###########################################################################################################
  require_once('../Conexion/conexion.php');
session_start();
    
      $nombre  = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
      $mov  = '"'.$mysqli->real_escape_string(''.$_POST['mov'].'').'"';
      $tipoF  = '"'.$mysqli->real_escape_string(''.$_POST['tipoF'].'').'"';
      $recurso  = '"'.$mysqli->real_escape_string(''.$_POST['recurso'].'').'"';
      $prede  = '"'.$mysqli->real_escape_string(''.$_POST['prede'].'').'"';
      $equivalencia  = '"'.$mysqli->real_escape_string(''.$_POST['equivalente'].'').'"';
  
      $id  = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
   
   $variable= ", predecesor=$prede, tipofuente=$tipoF, recursofinanciero=$recurso ";
    if ($tipoF == '""'){
        $tipoF='NULL';
    }
    if($recurso == '""'){
        $recurso='NULL';
    }
    if ($prede=='""'){
        $prede ='NULL';
    } 
    if($equivalencia =='""'){
        $equivalencia='NULL';
    };
      
  $insertSQL = "UPDATE gf_fuente SET nombre= $nombre, "
          . "movimiento= $mov, predecesor =$prede, tipofuente =$tipoF, "
                . "recursofinanciero=$recurso, equivalente=$equivalencia WHERE Id_Unico = $id ";
  $resultado = $mysqli->query($insertSQL);
  
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

<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../listar_GF_FUENTE.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.history.back(-1);
});
</script>
<?php } ?>