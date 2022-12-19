<?php
#Mantiene la sessión enviada
session_start();
#Llamado al archivo de conexión
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
#Insertado de datos en la tabla
$sql="insert into gs_usuario(usuario,contrasen,rol,tercero,estado,observaciones,fechaactualizacion) values($nomusu,$pass,$rol,$tercero,$estado,$observaciones,$fecha)";
$result=$mysqli->query($sql);
?>
<!-- Html estatico para crear los modales y mostrarlos dependiendo sea el resultado de la variable $result -->
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
<script src="../js/bootstrap.min.js"></script>
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
        $("#ver2").click(function(){
            $("#myModal2").modal('hide');
            window.history.go(-1);
        });        
    </script>
<?php } ?>