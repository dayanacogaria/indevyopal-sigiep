<?php
require_once('../Conexion/conexion.php');
session_start();
$nombre  = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
if(empty($_POST['valor'])){
    $valor   = 'NULL';
} else {
    $valor   = '"'.$mysqli->real_escape_string(''.$_POST['valor'].'').'"';    
}
$codigo  = '"'.$mysqli->real_escape_string(''.$_POST['codigo'].'').'"';

$insertSQL = "INSERT INTO gf_unidad_factor(nombre, valor, codigo_fe) VALUES($nombre, $valor,$codigo)";
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
    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
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
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
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
    <script type="text/javascript" src="../js/menu.js"></script>
    <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
    <script src="../js/bootstrap.js"></script>
    <!-- Script que redirige a la página inicial de Unidad Factor. -->
    <?php if($resultado==true){ ?>
        <script type="text/javascript">
            $("#myModal1").modal('show');
            $("#ver1").click(function(){
                $("#myModal1").modal('hide');
                window.location='../GF_UNIDAD_FACTOR.php';
            });
        </script>
    <?php }else{ ?>
        <script type="text/javascript">
            $("#myModal2").modal('show');
        </script>
    <?php } ?>
</body>
</html>