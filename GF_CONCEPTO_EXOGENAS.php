<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#16/04/2018 | Erica G. | Archivo Creado
####/################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$formato    = $_GET['id'];
$rowt        = $con->Listar("SELECT * FROM gf_formatos_exogenas 
            WHERE md5(id_unico)='$formato'");
$titulo2    = $rowt[0][1].' - '.$rowt[0][2];
$row        = $con->Listar("SELECT * FROM gf_concepto_exogenas 
            WHERE formato = ".$rowt[0][0]." ORDER BY codigo");
?>
<title>Formatos Exógenas</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #codigo-error, #nombre-error, #archivo-error {
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
$().ready(function() {
  var validator = $("#forms").validate({
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
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-right: 4px; margin-left: 4px;">Concepto Exógenas</h2>
                <a href="GF_FORMATOS_EXOGENAS.php?f=3&id=<?php echo $_GET['id']?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <div class="form-group form-inline" style="margin-top:0px; margin-left:0px">
                        <div class="form-group form-inline" style="width: 100%; margin-top:20px; margin-left:0px">
                            <form name="form" id="form" class="form-horizontal col-sm-8" method="POST"  enctype="multipart/form-data" action="javascript:guardar()">
                                <div class="form-group form-inline col-sm-12" style="margin-top:0px; margin-left:0px">
                                    <input type="hidden" name="formato" id="formato" value="<?php echo $rowt[0][0]?>">
                                    <label for="codigo" class="control-label col-sm-2" ><strong style="color:#03C1FB;">*</strong>Código :</label>
                                    <input type="text" name="codigo" id="codigo" class="form-control col-sm-1" style="width:150px" required="required" placeholder="Código" title="Ingrese Código" value="">
                                    <label for="nombre" class="control-label col-sm-2"><strong style="color:#03C1FB;">*</strong>Nombre :</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control col-sm-1" style="width:150px" required="required" placeholder="Nombre" title="Ingrese Nombre" value="">
                                    <button type="submit" class="btn btn-primary sombra" style="margin-top:1px" ><span class="glyphicon glyphicon-floppy-disk"></span></button>

                                </div>
                            </form>
                            <form name="forms" id="forms" class="form-horizontal col-sm-4"  method="POST"  enctype="multipart/form-data" action="javascript:subir()">
                               <div class="form-group form-inline" style="margin-top:0px; margin-left:0px">
                                    <input type="hidden" name="formato" id="formato" value="<?php echo $rowt[0][0]?>">
                                    <label for="file" class="control-label col-sm-2" ><strong style="color:#03C1FB;">*</strong>Subir Archivo (*xls):</label>
                                    <input type="file" name="file" id="file" class="form-control col-sm-1" style="width:150px" required="required" placeholder="Archivo" title="Seleccione" value="">
                                    <button type="submit" style="margin-top:1px" class="btn btn-primary sombra" ><span class="glyphicon glyphicon-floppy-disk"></span></button>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <script>
                    function guardar(){
                        var formData = new FormData($("#form")[0]);  
                        $.ajax({
                            type: 'POST',
                            url: "jsonPptal/gf_exogenasJson.php?action=4",
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
                                        document.location.reload();
                                    })
                                } else {
                                    $("#mensaje").html('No Se Ha Podido Guardar La Información');
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
                    function subir(){
                        jsShowWindowLoad('Guardando Datos ...');
                        var formData = new FormData($("#forms")[0]);  
                        $.ajax({
                            type: 'POST',
                            url: "jsonPptal/gf_exogenasJson.php?action=5",
                            data:formData,
                            contentType: false,
                            processData: false,
                            success: function(response)
                            {
                               jsRemoveWindowLoad();
                                console.log(response);
                                if(response>0){
                                    $("#mensaje").html('Información Guardada Correctamente <br/>'+response+' Registros Guardados');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        document.location.reload();
                                    })
                                } else {
                                    $("#mensaje").html('No Se Ha Podido Guardar La Información');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        $("#modalMensajes").modal("hide");
                                    })

                                }
                            }
                        });
                    }
                </script>
                <br/>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Código</strong></td>
                                    <td><strong>Nombre</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($row); $i++) { ?>
                                <tr>
                                    <td style="display: none;"><?php echo $row[$i][2]?></td>
                                    <td>
                                        <a href="#" onclick="javascript:eliminar(<?php echo $row[$i][0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a href="#" onclick="javascript:modificar(<?php echo $row[$i][0].",'".$row[$i][2]."','".$row[$i][3]."'";?>);"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td><?php echo $row[$i][2];?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[$i][3]));?></td>
                                </tr>
                                <?php } ?>
                            </tbody> 
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function eliminar(id){
            $("#modalEliminar").modal("show");
            $("#aceptarE").click(function(){
                var form_data = {action:6, id:id};  
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
    <script>
        function modificar(id, cod, nom){
            $("#idm").val(id);
            $("#codigom").val(cod);
            $("#nombrem").val(nom);
            $("#modalModificar").modal('show');
        }
    </script>
    <script>
        function modificarItem(){
            var formData = new FormData($("#formod")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_exogenasJson.php?action=7",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                {
                   $("#modalModificar").modal('hide');                  
                    console.log(response);
                    if(response==1){
                        $("#mensaje").html('Información Modificada Correctamente');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            document.location.reload();
                        })
                    } else {
                        $("#mensaje").html('No Se Ha Podido Modificar La Información');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            $("#modalMensajes").modal("hide");
                        })

                    }
                }
            });
        }
    </script>
    
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
    <div class="modal fade" id="modalModificar" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
                </div>
                <div class="modal-body ">
                    <form  name="formod" id="formod" method="POST" action="javascript:modificarItem()">
                        <input type="hidden" name="idm" id="idm">
                        
                        <div class="form-group" style="margin-top: 13px;">
                            <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Código:</label>
                            <input  style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="codigom" id="codigom" class="form-control" title="Código" required/>
                        </div>
                        <div class="form-group" style="margin-top: 13px;">
                            <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input  style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="nombrem" id="nombrem" class="form-control" title="Nombre" required/>
                        </div>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
                        <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
   
    <?php require_once ('footer.php'); ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
        function jsRemoveWindowLoad() {
            // eliminamos el div que bloquea pantalla
            $("#WindowLoad").remove(); 
        }

        function jsShowWindowLoad(mensaje) {
            //eliminamos si existe un div ya bloqueando
            jsRemoveWindowLoad(); 
            //si no enviamos mensaje se pondra este por defecto
            if (mensaje === undefined) mensaje = "Procesando la información<br>Espere por favor"; 
            //centrar imagen gif
            height = 20;//El div del titulo, para que se vea mas arriba (H)
            var ancho = 0;
            var alto = 0; 
            //obtenemos el ancho y alto de la ventana de nuestro navegador, compatible con todos los navegadores
            if (window.innerWidth == undefined) ancho = window.screen.width;
            else ancho = window.innerWidth;
            if (window.innerHeight == undefined) alto = window.screen.height;
            else alto = window.innerHeight; 
            //operación necesaria para centrar el div que muestra el mensaje
            var heightdivsito = alto/2 - parseInt(height)/2;//Se utiliza en el margen superior, para centrar 
           //imagen que aparece mientras nuestro div es mostrado y da apariencia de cargando
            imgCentro = "<div style='text-align:center;height:" + alto + "px;'><div  style='color:#FFFFFF;margin-top:" + heightdivsito + "px; font-size:20px;font-weight:bold;color:#1075C1'>" + mensaje + "</div><img src='img/loading.gif'/></div>"; 
                //creamos el div que bloquea grande------------------------------------------
                div = document.createElement("div");
                div.id = "WindowLoad";
                div.style.width = ancho + "px";
                div.style.height = alto + "px";        
                $("body").append(div); 
                //creamos un input text para que el foco se plasme en este y el usuario no pueda escribir en nada de atras
                input = document.createElement("input");
                input.id = "focusInput";
                input.type = "text"; 
                //asignamos el div que bloquea
                $("#WindowLoad").append(input); 
                //asignamos el foco y ocultamos el input text
                $("#focusInput").focus();
                $("#focusInput").hide(); 
                //centramos el div del texto
                $("#WindowLoad").html(imgCentro);

        }
        </script>

        <style>
        #WindowLoad{
            position:fixed;
            top:0px;
            left:0px;
            z-index:3200;
            filter:alpha(opacity=80);
           -moz-opacity:80;
            opacity:0.80;
            background:#FFF;
        }
        </style>
</body>