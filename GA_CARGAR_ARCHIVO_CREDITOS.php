<?php 
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$annio      = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
?>
<title>Cargar Archivo Créditos</title>
</head>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #sltAnnio-error, #sltmes-error, #file-error {
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
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top: -20px"> 
                <h2 align="center" class="tituloform">Cargar Archivo Créditos</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                        <input type="hidden" name="sltAnnio" id="sltAnnio" value="<?php echo $annio?>">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltmes" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes:</label>
                            <select required name="sltmes" id="sltmes" style="height: auto" class="select2_single form-control" title="Mes" >
                                <option value="">Mes</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="file" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
                            <input required id="flDoc" name="flDoc" type="file" style="height: 35px;"  title="Seleccione un archivo">
                        </div>
                        <div class="col-sm-10" style="margin-top:0px;margin-left:500px" >
                            <button id="btnGuardar" name="btnGuardar" class="btn sombra btn-primary" title="Generar"><i class="glyphicon glyphicon-save-file" aria-hidden="true"></i> Cargar Archivo</button>              
                        </div>
                    </div>
                </form>
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
    <div class="modal fade" id="modalMensajes2" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje2" name="mensaje2" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="Cancelar2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>    
    $(document).ready(function() {        
        var form_data={action:2, annio :$("#sltAnnio").val()};
        var optionMI ="<option value=''>Mes</option>";
        $.ajax({
           type:'POST', 
           url:'jsonPptal/consultasInformesCnt.php',
           data: form_data,
           success: function(response){
               optionMI =optionMI+response;
               $("#sltmes").html(optionMI).focus();              
           }
        });
    })
</script>
<script src="js/select/select2.full.js"></script>
<script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
</script>
<?php require_once 'footer.php' ?>  
<script>
    $("#btnGuardar").click( function( evt){            
        evt.preventDefault();
        let file = $("#flDoc").val();
        let num = file.length;
        let ext  = file.substring(num-4,num);
        let dependencias = 0;
        let depresponsable = 0;
        if (num > 0 && ext === "xlsx"){
            /***** Validar TERCERO *****/                
            jsShowWindowLoad('Validando Archivo...');
            var formData = new FormData($("#form")[0]);
            $.ajax({
                type: 'POST',
                url: "./jsonPptal/ga_control_cooperativo.php?action=6",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response+'G');
                    var resultado = JSON.parse(response);
                    var rta = resultado["rta"];
                    var data = resultado["html"];  
                    if(rta==0){
                        jsShowWindowLoad('Validando Terceros...');
                        var formData = new FormData($("#form")[0]);
                        $.ajax({
                            type: 'POST',
                            url: "./jsonPptal/ga_control_cooperativo.php?action=7",
                            data:formData,
                            contentType: false,
                            processData: false,
                            success: function(response)
                            { 
                                jsRemoveWindowLoad();
                                var resultado = JSON.parse(response);
                                var rta = resultado["rta"];
                                var data = resultado["html"];  
                                console.log(response+'G');
                                if(rta==0){
                                    jsShowWindowLoad('Validando Valores...');
                                    var formData = new FormData($("#form")[0]);
                                    $.ajax({
                                        type: 'POST',
                                        url: "./jsonPptal/ga_control_cooperativo.php?action=8",
                                        data:formData,
                                        contentType: false,
                                        processData: false,
                                        success: function(response)
                                        { 
                                            jsRemoveWindowLoad();
                                            var resultado = JSON.parse(response);
                                            var rta = resultado["rta"];
                                            var data = resultado["html"];  
                                            console.log(response+'GV');
                                            if(rta==0){
                                                guardar();
                                            } else {
                                                $("#mensaje2").html(data+' ¿Desea Cargar El Archivo?<br/> Recuerde Que Los Pagos Con Inconsistencias No Serán Cargados Al Sistema');
                                                $("#modalMensajes2").modal("show");
                                                $("#Aceptar2").click(function(){
                                                    guardar();
                                                });
                                                $("#Cancelar2").click(function(){
                                                    $("#modalMensajes2").modal("hide");
                                                })
                                            }
                                        }
                                    })
                                } else {
                                    $("#mensaje").html(data);
                                    $("#modalMensajes").modal("show");
                                }
                            }
                        })
                    } else {
                        $("#mensaje").html(data);
                        $("#modalMensajes").modal("show");
                    }

                }
            });
        }  
    });
</script>
<script>
    function guardar(){
        jsShowWindowLoad('Guardando...');
        var formData = new FormData($("#form")[0]);
        $.ajax({
            type: 'POST',
            url: "./jsonPptal/ga_control_cooperativo.php?action=9",
            data:formData,
            contentType: false,
            processData: false,
            success: function(response)
            { 
                jsRemoveWindowLoad();
                var resultado = JSON.parse(response);
                var rta = resultado["rta"];
                var data = resultado["html"]; 
                console.log(response+'GD');
                if(rta>0){
                    $("#mensaje").html(data);
                    $("#modalMensajes").modal("show");
                    $("#Aceptar").click(function(){
                        document.location.reload();
                    })
                } else {
                    $("#mensaje").html('No se ha podido guardar la información');
                    $("#modalMensajes").modal("show");
                }
            }
        })
    }
</script>
</body>
</html>