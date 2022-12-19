<?php
  require_once('../Conexion/conexion.php');
session_start();
  $id  = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
  $nombre  = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
  $viviendaI  = '"'.$mysqli->real_escape_string(''.$_POST['UnidadViviendaInicial'].'').'"';
  $viviendaF = '"'.$mysqli->real_escape_string(''.$_POST['UnidadViviendaFinal'].'').'"';
  $estadoF  = '"'.$mysqli->real_escape_string(''.$_POST['EstadoFacturacion'].'').'"';
  $formato  = '"'.$mysqli->real_escape_string(''.$_POST['FormatoFactura'].'').'"';
  
  if ($viviendaI=='""'){
     $viviendaI='NULL'; 
  }
  if ($viviendaF=='""'){
     $viviendaF='NULL'; 
  }
  if ($estadoF=='""'){
     $estadoF='NULL'; 
  }
  if ($formato=='""'){
     $formato='NULL'; 
  }
  
  
  $update = "UPDATE gp_ciclo SET nombre=$nombre, unidad_vivienda_inicial=$viviendaI,unidad_vivienda_final=$viviendaF,"
          . "estado_facturacion=$estadoF,formato_factura=$formato WHERE id_unico = $id";
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
            window.location='../LISTAR_GP_CICLO.php';
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