<?php
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
$compania = $_SESSION['compania'];
if(empty($_GET['id'])) {
    $titulo = "Listar ";
    $titulo2= ".";
    $row = $con->Listar("SELECT id_unico, sigla, nombre, resolucion,numero_resolucion,envio_doc_soporte
    FROM gf_tipo_documento_equivalente
    WHERE compania = $compania ");
} elseif(($_GET['id'])==1) {
    $titulo = "Registrar ";
    $titulo2= ".";
} elseif(($_GET['id'])==2) {
    $titulo = "Modificar ";
    $id     = $_GET['id_r'];
    $row    = $con->Listar("SELECT id_unico, sigla, nombre, resolucion,numero_resolucion,envio_doc_soporte
        FROM gf_tipo_documento_equivalente
        WHERE md5(id_unico)='$id'");
    $titulo2= $row[0][1].' - '.$row[0][2];
}
?>
<title>Tipo Documento Equivalente</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/select2.js"></script>
<script src="dist/jquery.validate.js"></script>
<script src="js/md5.pack.js"></script>
<style>
    label #sigla-error, #nombre-error, #resolucion-error{
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
<body>
    <div class="container-fluid text-center">
        <div class="row content">    
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left" style="margin-top:-20px">
                <h2 id="forma-titulo3" align="center" style=" margin-right: 4px; margin-left: 4px;"><?php echo $titulo.' Tipo Documento Equivalente'?></h2>
                <?php if(empty($_GET['id'])) { ?>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Sigla</strong></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Número Resolución</strong></td>
                                    <td><strong>Resolución</strong></td>
                                    <td><strong>Envio Documento Soporte</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Sigla</th>
                                    <th>Nombre</th>
                                    <th>Número Resolución</th>
                                    <th>Resolución</th>
                                    <th>Envio Documento Soporte</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($row); $i++) { ?>
                                    <tr>
                                        <td style="display: none;"></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[$i][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            <a href="GF_TIPO_DOCUMENTOE.php?id=2&id_r=<?php echo md5($row[$i][0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                        </td>
                                        <td><?php echo $row[$i][1]; ?></td>
                                        <td><?php echo $row[$i][2]; ?></td>
                                        <td><?php echo $row[$i][4]; ?></td>
                                        <td><?php echo $row[$i][3]; ?></td>
                                        <?php 
                                                if ($row[$i][5]==1) { ?>
                                                    <td>Si</td>
                                                <?php
                                                 }else{?>
                                                    <td>No</td>
                                                <?php
                                                 }
                                        ?>
                                      
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="GF_TIPO_DOCUMENTOE.php?id=1" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
                    </div>
                </div>
                <?php }  elseif(($_GET['id'])==1){ ?>
                    <a href="GF_TIPO_DOCUMENTOE.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                            <p align="center" style="margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: 10px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="sigla" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Sigla:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <input type="text" name="sigla" id="sigla" class="form-control " style=" width: 100%" required="required" placeholder="Sigla" title="Ingrese Sigla" onkeyup="javascript:this.value = this.value.toUpperCase();">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: 10px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -30px;" >
                                    <label for="nombre" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -30px;">
                                    <input type="text" name="nombre" id="nombre" class="form-control " style=" width: 100%" required="required" placeholder="Nombre" title="Ingrese Nombre" >
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -20px;" >
                                    <label for="txtnumeror" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Número Resolución:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -20px;">
                                    <input type="text" name="txtnumeror" id="txtnumeror" class="form-control " style=" width: 100%;" placeholder=" Número Resolución" title="Número Resolución">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -20px;" >
                                    <label for="resolucion" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Resolución:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -20px;">
                                    <textarea type="text" name="resolucion" id="resolucion" class="form-control " style=" width: 100%; height:80px" placeholder="Resolución" title="Resolución"></textarea>
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -20px;" >
                                    <label for="resolucion" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Envio Documento Soporte:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -20px;">
                                   <label for="optD" class="radio-inline"><input type="radio" name="optD" id="optD" value="1">Si</label>
                                <label for="optD" class="radio-inline"><input type="radio" name="optD" id="optD" value="2" checked>No</label>
                                </div>
                            </div> 

                
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                <?php } elseif(($_GET['id'])==2){  ?>
                    <a href="GF_TIPO_DOCUMENTOE.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
                            <p align="center" style="margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <input type="hidden" name="id" id="id" value="<?= $row[0][0]?>">
                            <div class="form-group" style="margin-top: 10px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="sigla" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Sigla:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <input type="text" name="sigla" id="sigla" class="form-control " style=" width: 100%" required="required" placeholder="Sigla" title="Ingrese Sigla" onkeyup="javascript:this.value = this.value.toUpperCase();" value="<?= $row[0][1]?>">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: 10px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -30px;" >
                                    <label for="nombre" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -30px;">
                                    <input type="text" name="nombre" id="nombre" class="form-control " style=" width: 100%" required="required" placeholder="Nombre" title="Ingrese Nombre" value="<?= $row[0][2]?>">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -20px;" >
                                    <label for="txtnumeror" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Número Resolución:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -20px;">
                                    <input type="text" name="txtnumeror" id="txtnumeror" class="form-control " style=" width: 100%;" placeholder="Número Resolución" title="Número Resolución" value="<?= $row[0][4]?>">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -20px;" >
                                    <label for="resolucion" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Resolución:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -20px;">
                                    <textarea type="text" name="resolucion" id="resolucion" class="form-control " style=" width: 100%; height:80px" placeholder="Resolución" title="Resolución"><?= $row[0][3]?></textarea>
                                </div>
                            </div> 
                              <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -20px;" >
                                    <label for="resolucion" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Envio Documento Soporte:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -20px;">
                                    <?php 
                                    if($row[0][5]==1){ ?>
                                    <label for="optD" class="radio-inline"><input type="radio" name="optD" id="optD"  value="1" checked>Sí</label>
                                    <label for="optD" class="radio-inline"><input type="radio" name="optD" id="optD" value="2" >No</label>
                                <?php } else { ?>
                                    <label for="optD" class="radio-inline"><input type="radio" name="optD" id="optD"  value="1" >Sí</label>
                                    <label for="optD" class="radio-inline"><input type="radio" name="optD" id="optD" value="2" checked>No</label>
                                <?php } ?>
                                </div>
                            </div> 

                         
                            <div class="form-group" style="margin-top: 20px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                <?php } ?>
                </div>
            </div>
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
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2").select2({
            allowClear:true
        });
    </script>
    <script>
        function registrar(){
            var formData = new FormData($("#form")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_TipoDocumentoEJson.php?action=1",
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
                            document.location='GF_TIPO_DOCUMENTOE.php';
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
    <script>
        function modificar(){
            var formData = new FormData($("#form")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_TipoDocumentoEJson.php?action=2",
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
                            document.location='GF_TIPO_DOCUMENTOE.php';
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
    <script>
        function eliminar(id){
            $("#modalEliminar").modal("show");
            $("#aceptarE").click(function(){
                $("#modalEliminar").modal("hide");
                var form_data = {action:3, id:id};  
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_TipoDocumentoEJson.php",
                    data: form_data, 
                    success: function(response) {
                        console.log(response);
                        if(response==1){
                            $("#mensaje").html('Información Eliminada Correctamente');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                document.location='GF_TIPO_DOCUMENTOE.php';
                            })
                        } else {
                            $("#mensaje").html('No Se Ha Podido Eliminar La Información');
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
</body>
</html>

