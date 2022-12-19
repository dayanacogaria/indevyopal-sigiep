<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#04/07/2018 | Erica G. | Afectado
#27/02/2017 | Erica G. | Agregar campo vigencia actual
#######################################################################################################
  require_once('../Conexion/conexion.php'); 
session_start();
    
    $codigo  = '"'.$mysqli->real_escape_string(''.$_POST['codigo'].'').'"';
    $nombre  = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
    $obli  = '"'.$mysqli->real_escape_string(''.$_POST['obli'].'').'"';
    $ter  = '"'.$mysqli->real_escape_string(''.$_POST['ter'].'').'"';
    $claseP  = '"'.$mysqli->real_escape_string(''.$_POST['claseP'].'').'"';
    $tipDocumento  = '"'.$mysqli->real_escape_string(''.$_POST['tipDocumento'].'').'"';
    $tipoO  = '"'.$mysqli->real_escape_string(''.$_POST['tipoO'].'').'"';
    $vigenciaA  = '"'.$mysqli->real_escape_string(''.$_POST['vigenciaA'].'').'"';
    $id  = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
    $automatico  = '"'.$mysqli->real_escape_string(''.$_POST['automatico'].'').'"';      
    if(empty($_POST['tipDocumento'])){
        $tipDocumento = 'NULL';
    } else {
        $tipDocumento  = '"'.$mysqli->real_escape_string(''.$_POST['tipDocumento'].'').'"';
    }
    if(empty($_POST['afectado'])){
        $afectado = 'NULL';
    } else {
        $afectado  = '"'.$mysqli->real_escape_string(''.$_POST['afectado'].'').'"';
    }    
  $insertSQL = "UPDATE gf_tipo_comprobante_pptal 
            SET codigo=$codigo, nombre=$nombre, 
            obligacionafectacion=$obli, terceroigual=$ter, 
            clasepptal=$claseP, automatico=$automatico, 
            vigencia_actual = $vigenciaA, 
            tipooperacion=$tipoO , tipodocumento = $tipDocumento, 
            afectado = $afectado  WHERE Id_Unico = $id ";
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

  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../listar_GF_TIPO_COMPROBANTE_PPTAL.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    history.back(-1);
  });
</script>
<?php } ?>