<?php
require_once('../Conexion/conexion.php');
session_start();
   
  $anno  = '"'.$mysqli->real_escape_string(''.$_POST['valor'].'').'"';
  $salariom  = '"'.$mysqli->real_escape_string(''.$_POST['salariom'].'').'"';
  $minimod  = '"'.$mysqli->real_escape_string(''.$_POST['minimod'].'').'"';
  $uvt  = '"'.$mysqli->real_escape_string(''.$_POST['uvt'].'').'"';
  $cajam  = '"'.$mysqli->real_escape_string(''.$_POST['cajam'].'').'"'; 
  $estadoA = '"'.$mysqli->real_escape_string(''.$_POST['estadoA'].'').'"'; 
  $id  = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
  $min_c        = '"'.$mysqli->real_escape_string(''.$_POST['min_c'].'').'"'; 
  $menorc       = '"'.$mysqli->real_escape_string(''.$_POST['menorc'].'').'"'; 
  $menorcm      = '"'.$mysqli->real_escape_string(''.$_POST['menorcm'].'').'"'; 
  $mayorc       = '"'.$mysqli->real_escape_string(''.$_POST['mayorc'].'').'"'; 

  $insertSQL = "UPDATE gf_parametrizacion_anno SET Anno=$anno, SalarioMinimo=$salariom, 
        MinDepreciacion=$minimod, UVT=$uvt, CajaMenor=$cajam, EstadoAnno=$estadoA  ,
        minimacuantia= $min_c, menorcuantia= $menorc, 
        menorcuantia_m= $menorcm, mayorcuantia = $mayorc   
        WHERE Id_Unico = $id";
  $resultado = $mysqli->query($insertSQL);

  
?>

  
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
          <p><?php
                echo "No se ha podido modificar la información.";
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

<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../listar_GF_PARAMETRIZACION_ANNO.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>