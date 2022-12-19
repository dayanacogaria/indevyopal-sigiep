<?php
require_once('../Conexion/conexion.php');
session_start();
$id  = $_POST['id'];
$tipoC      = $_POST['TipoConcepto'];
$tipoO      = $_POST['TipoOperacion'];
$nombre     = $_POST['nombre'];
if(empty($_POST['planInventario'])){
    $planI  = 'NULL';
} else {
    $planI  = $_POST['planInventario'];
}
if(empty($_POST['factorBase'])){
    $factorB  = 'NULL';
} else {
    $factorB  = $_POST['factorBase'];
}

if(empty($_POST['alojamiento'])){
    $alojamiento  = 'NULL';
} else {
    $alojamiento  = $_POST['alojamiento'];
}

if(empty($_POST['concepto_asociado'])){
    $concepto_asociado  = 'NULL';
} else {
    $concepto_asociado  = $_POST['concepto_asociado'];
}

if(empty($_POST['ajuste'])){
    $ajuste  = NULL;
} else {
    $ajuste  = $_POST['ajuste'];
}

if(empty($_POST['traduccion'])){
    $traduccion = NULL;
} else {
    $traduccion = $_POST['traduccion'];
}

$update = "UPDATE gp_concepto SET 
    tipo_concepto=$tipoC, nombre='$nombre',tipo_operacion=$tipoO,
    plan_inventario=$planI, factor_base=$factorB , 
    alojamiento=$alojamiento, concepto_asociado=$concepto_asociado, 
    ajuste='$ajuste', traduccion ='$traduccion' 
    WHERE id_unico = $id";
$resultado = $mysqli->query($update);
  
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
    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
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
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
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
    <script src="../js/bootstrap.js"></script>
    
    <?php if($resultado==true){ ?>
        <script type="text/javascript">
          $("#myModal1").modal('show');
          $("#ver1").click(function(){
            $("#myModal1").modal('hide');
            window.location='../LISTAR_GP_CONCEPTO.php';
          });
        </script>
    <?php }else{ ?>
    <script type="text/javascript">
      $("#myModal2").modal('show');
     $("#ver2").click(function(){
        $("#myModal2").modal('hide');
         window.location=window.history.back(-1);
      });
    </script>
    <?php } ?>
</html>