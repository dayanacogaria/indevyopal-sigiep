<?php  
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con = new ConexionPDO(); 
$compania = $_SESSION['compania'];
$anno     = $_SESSION['anno'];
$rtc = $con->Listar("SELECT id_unico, mes FROM gf_mes WHERE parametrizacionanno = $anno");
$cta = $con->Listar("SELECT DISTINCT c.id_unico, c.codi_cuenta, c.nombre 
    FROM  gf_detalle_comprobante dc 
    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
    LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
    WHERE cn.parametrizacionanno = $anno AND c.clasecuenta = 11 ");
?>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script src="js/md5.pack.js"></script>
<style>
    label #file-error, #cuenta-error, #mes-error, #saldo-error {
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;
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
    },
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<title>Subir Conciliación Archivo </title>
</head>
<body> 
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Subir Conciliación Por Archivo</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Archivo .xsl - .xlsx <a href="documentos/formatos/Formato_Conciliacion.xlsx" target="_blank"><i class="fa fa-file-excel-o"></i></a></p>
                        <div class="form-group" style="margin-left: -10px;" >
                            <label for="cuenta" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Cuenta:</label>
                            <select name="cuenta" id="cuenta" class="form-control select2_single" title="Seleccione Cuenta"  required="required" style="width:300px">
                                <option value="">Cuenta</option>
                                <?php for ($c = 0; $c < count($cta); $c++) {
                                   echo '<option value="'.$cta[$c][0].'">'.$cta[$c][1].' - '.$cta[$c][2].'</option>';
                                }?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-left: -10px;" >
                            <label for="mes" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Mes:</label>
                            <select name="mes" id="mes" class="form-control select2_single" title="Seleccione Mes"  required="required" style="width:300px">
                                <option value="">Mes</option>
                                <?php for ($i = 0; $i < count($rtc); $i++) {
                                   echo '<option value="'.$rtc[$i][0].'">'.$rtc[$i][1].'</option>';
                                }?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-left: -10px;" >
                            <label for="saldo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Saldo Extracto:</label>
                            <input name="saldo" id="saldo" class="form-control" title="Ingrese Saldo Extracto"  required="required" style="width:300px"  onkeyup="formatC('saldo');" autocomplete="off">
                        </div>
                        <div class="form-group" style="margin-top: -0px; ">
                            <label for="doc" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
                            <input required id="file" name="file" type="file" style="height: 35px;"  title="Seleccione un archivo" required="required" >
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" width: 100px; margin-top: -10px; margin-bottom: 10px;">Cargar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>      
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalRequerido" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Seleccione un archivo.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnRequerido" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalMensaje" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight:normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAceptal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="js/select/select2.full.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
      $(document).ready(function() {
        $(".select2_single").select2({
          allowClear: true
        });
      });
    </script>
    <script>
        function guardar(){
            var formData = new FormData($("#form")[0]);  
            //jsShowWindowLoad('Validando Datos ...');
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_conciliacionJson.php?action=1",
                data:formData,
                contentType: false,
                processData: false,
                success: function (data) { 
                    console.log(data);
                    jsRemoveWindowLoad();
                    console.log(data);
                    if(data==0) {
                        var formData = new FormData($("#form")[0]);  
                        jsShowWindowLoad('Guardando Datos ...');
                        $.ajax({
                            type: 'POST',
                            url: "jsonPptal/gf_conciliacionJson.php?action=2",
                            data:formData,
                            contentType: false,
                            processData: false,
                            success: function (data) {  
                                jsRemoveWindowLoad();
                                console.log(data);
                                if(data==0) {
                                    $("#mensaje").html('No Se Ha Podido Guardadar Información');
                                    $("#modalMensaje").modal("show");
                                } else {
                                    var formData = new FormData($("#form")[0]);  
                                    jsShowWindowLoad('Guardando Datos ...');
                                    $.ajax({
                                        type: 'POST',
                                        url: "jsonPptal/gf_conciliacionJson.php?action=3",
                                        data:formData,
                                        contentType: false,
                                        processData: false,
                                        success: function (data) {
                                            jsRemoveWindowLoad();
                                            
                                            data = data.trim();
                                            if(data==0) {
                                                $("#mensaje").html('No Se Ha Podido Guardadar Información');
                                                $("#modalMensaje").modal("show");
                                            } else {
                                                $("#mensaje").html('Información Guardada Correctamente');
                                                $("#modalMensaje").modal("show");
                                                window.open('registrar_GF_PARTIDA_CONCILIATORIA.php?idPartida='+md5(data));

                                            }
                                        }
                                    })
                                    
                                }
                            }
                        })
                    } else {
                        $("#mensaje").html('Esta Cuenta Ya Tiene Conciliación En El Mes Seleccionado');
                        $("#modalMensaje").modal("show");
                    }
                
                }
           });
        }
        $("#btnAceptal").click(function(){
            document.location.reload();
        })
    </script>
    <?php require_once 'footer.php'; ?>
</body>
</html>

