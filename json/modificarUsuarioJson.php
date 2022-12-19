<?php
session_start();
#Llamado a la clase de conexión
require_once '../Conexion/conexion.php';
#Captura de la variables enviadas por post
#Campo nombre de usuario
$nomusu = '"'.$mysqli->real_escape_string(''.$_POST['txtUsuario'].'').'"';
#Campo de contraseña
$pass = '"'.$mysqli->real_escape_string(''.$_POST['txtPass'].'').'"';
#Campo de selección Rol
$rol = '"'.$mysqli->real_escape_string(''.$_POST['sltRol'].'').'"';
#Campo de selección de Tercero
$tercero = '"'.$mysqli->real_escape_string(''.$_POST['sltTercero']).'"';
#Campo de selección de Estado
$estado = '"'.$mysqli->real_escape_string(''.$_POST['sltEstado'].'').'"';
#Campo de  observaciones
$observaciones = '"'.$mysqli->real_escape_string(''.$_POST['txtObservaciones'].'').'"';
#Fecha de modificación
$fecha = '"'.date('Y-m-d').'"';
#Id de usuario
$id='"'.$mysqli->real_escape_string(''.$_POST['txtId'].'').'"';
#Consulta de actualización de datos
$sql="update gs_usuario set usuario=$nomusu,contrasen=$pass,rol=$rol,tercero=$tercero,estado=$estado,observaciones=$observaciones,fechaactualizacion=$fecha where id_unico=$id";
$result=$mysqli->query($sql);
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
<!--Modal para informar al usuario que no se ha podido modificar la información-->
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
<!--Links para el estilo de la página-->  
<script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la página de listar para mostrar la información modificada-->
<?php if($result==true){ ?>
    <script type="text/javascript">
        $("#myModal1").modal('show');
            $("#ver1").click(function(){
                $("#myModal1").modal('hide');
                window.location='../listar_GS_USUARIO.php';
            });
    </script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>

