<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#16/04/2018 | Erica G. | Archivo Creado
####/################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];

$titulo = "";
$titulo2= "";
if(empty($_GET['f'])){
    $tipo   = "";
    $titulo = "Listar ";
    $row = $con->Listar("SELECT * FROM gf_formatos_exogenas WHERE parametrizacionanno = $anno");
} elseif($_GET['f']==2) {
    $tipo   = $_GET['f'];
    $titulo = "Registrar ";
    $titulo2= ".";
} elseif($_GET['f']==3) {
    $tipo   = $_GET['f'];
    $titulo = "Modificar ";
    $id     = $_GET['id'];
    $row    = $con->Listar("SELECT * FROM gf_formatos_exogenas 
            WHERE md5(id_unico)='$id'");
    $titulo2= $row[0][1].' - '.$row[0][2];
}
?>
<title>Formatos Exógenas</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #formato-error, #nombre-error, #cuantia-error {
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;
}
body{
    font-size: 12px;
}
</style>
<script>
$().ready(function() {
  var validator = $("#form").validate({
        ignore: "",
     
    errorPlacement: function(error, element) {
      
      $( element )
        .closest( "form" )
          .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    }
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<style>
    .form-control {font-size: 12px;}
</style>
<body>
    <div class="container-fluid text-center">
        <div class="row content">    
            <?php require_once ('menu.php'); ?>
            <?php  if(empty($tipo)){ ?>
             <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo.' Formatos Exógenas'?></h2>    
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Formato</strong></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Cuantía</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Formato</th>
                                    <th>Nombre</th>
                                    <th>Cuantía</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($row); $i++) { ?>
                                <tr>
                                    <td style="display: none;"><?php echo $row[$i][0]?></td>
                                    <td>
                                        <a  href="#" onclick="javascript:eliminar(<?php echo $row[$i][0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a href="GF_FORMATOS_EXOGENAS.php?f=3&id=<?php echo md5($row[$i][0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td><?php echo $row[$i][1];?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[$i][2]));?></td>
                                    <td><?php echo $row[$i][3];?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="GF_FORMATOS_EXOGENAS.php?f=2" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
                    </div>
                </div>
            </div>
            <script>
                function eliminar(id){
                    $("#modalEliminar").modal("show");
                    $("#aceptarE").click(function(){
                        var form_data = {action:3, id:id};  
                        $.ajax({
                            type: 'POST',
                            url: "jsonPptal/gf_exogenasJson.php",
                            data: form_data, 
                            success: function(response)
                            {
                                console.log(response);
                                $("#modalEliminar").modal("hide");
                                if(response==1){
                                    $("#mensaje").html('Información Eliminada Correctamente');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        document.location.reload();
                                    })
                                } else {
                                    $("#mensaje").html('No Se Ha Podido Eliminar Información');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        $("#modalMensajes").modal("hide");
                                    })

                                }
                            }
                        });
                    })
                    $("#cancelarE").click(function(){
                        $("#modalEliminar").modal("hide");
                    })
                }

            </script>
            <?php } elseif($tipo==2) { ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo.' Formatos Exógenas'?></h2>
                <a href="GF_FORMATOS_EXOGENAS.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="formato" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Formato:</label>
                            <input type="text" name="formato" id="formato" class="form-control" required="required" placeholder="Formato" title="Ingrese Formato">
                        </div>
                        <div class="form-group">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required="required" placeholder="Nombre" title="Ingrese Nombre">
                        </div>
                        <div class="form-group">
                            <label for="cuantia" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Cuantía:</label>
                            <input type="text" name="cuantia" id="cuantia" class="form-control" placeholder="Cuantía">
                        </div>
                        <div class="form-group" style="margin-top: 20px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                    <script>
                        function registrar(){
                            var formData = new FormData($("#form")[0]);  
                            $.ajax({
                                type: 'POST',
                                url: "jsonPptal/gf_exogenasJson.php?action=1",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                {
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje").html('Información Guardada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            document.location='GF_FORMATOS_EXOGENAS.php';
                                        })
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Guardar Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })

                                    }
                                }
                            });
                        }
                    </script>
                </div>
            </div>
            <?php } elseif($tipo==3){ ?>
            <div class="col-sm-8 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo.' Formatos Exógenas'?></h2>
                <a href="GF_FORMATOS_EXOGENAS.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group">
                        <input type="hidden" name="id" id="id" value="<?php echo $row[0][0]?>">
                        <label for="formato" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Formato:</label>
                        <input type="text" name="formato" id="formato" class="form-control" required="required" placeholder="Formato" title="Ingrese Formato" value="<?php echo $row[0][1]?>">
                    </div>
                    <div class="form-group">
                        <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required="required" placeholder="Nombre" title="Ingrese Nombre" value="<?php echo $row[0][2]?>">
                    </div>
                    <div class="form-group">
                        <label for="cuantia" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Cuantía:</label>
                        <input type="text" name="cuantia" id="cuantia" class="form-control" placeholder="Cuantía" value="<?php if(!empty($row[0][3])){echo $row[0][3];}?>">
                    </div>
                    <div class="form-group" style="margin-top: 20px;">
                        <label for="no" class="col-sm-5 control-label"></label>
                        <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                    </div>
                    <input type="hidden" name="MM_insert" >
                </form>
                <script>
                    function modificar(){
                        var formData = new FormData($("#form")[0]);  
                        $.ajax({
                            type: 'POST',
                            url: "jsonPptal/gf_exogenasJson.php?action=2",
                            data:formData,
                            contentType: false,
                            processData: false,
                            success: function(response)
                            {
                                console.log(response);
                                if(response==1){
                                    $("#mensaje").html('Información Modificada Correctamente');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        document.location='GF_FORMATOS_EXOGENAS.php';
                                    })
                                } else {
                                    $("#mensaje").html('No Se Ha Podido Modificar Información');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        $("#modalMensajes").modal("hide");
                                    })

                                }
                            }
                        });
                    }
                </script>
            </div>
            </div>
            <div class="col-sm-2 text-left">
                <h3 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Adicionales</h3>
                <a href="GF_CONCEPTO_EXOGENAS.php?id=<?php echo md5($row[0][0]);?>"><button type="button" class="btn btnInfo btn-primary" style=" margin-top: -10px; margin-bottom: 10px; margin-left:40px">CONCEPTO</button></a>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalEliminar" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea Eliminar El Registro Seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="aceptarE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="cancelarE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
   
    <?php require_once ('footer.php'); ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
</body>
</html>



