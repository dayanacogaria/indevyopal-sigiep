<?php 
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php'); 
$con = new ConexionPDO(); 
$compania = $_SESSION['compania'];
$rtc = $con->Listar("SELECT DISTINCT t.id_unico, t.razonsocial, t.numeroidentificacion 
    FROM gf_parametrizacion_anno pa 
    LEFT JOIN gf_tercero t ON pa.compania = t.id_unico 
    LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero 
    WHERE pt.perfil = 1 ");
?>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<style>
    label #file-error {
    display: block;
    color: #155180;
    font-weight: normal;
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

<title>Subir Datas</title>
</head>
<body> 
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Subir Archivo Almacén</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Archivo xls</p>
                        <?php if($compania ==1) { ?>
                        <div class="form-group">
                            <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-left:10px">
                                <label for="compania" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Compañia:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: -10px;" >
                                <select name="compania" id="compania" class="form-control select2" title="Seleccione Compañia"  required="required" style="width:300px">
                                        <option value="">Compañia</option>
                                        <?php for ($i = 0; $i < count($rtc); $i++) {
                                           echo '<option value="'.$rtc[$i][0].'">'.$rtc[$i][1].' - '.$rtc[$i][2].'</option>';
                                        }?>
                                </select>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="file" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
                            <input required id="file" name="file" type="file" style="height: 35px;"  title="Seleccione un archivo">
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

<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/select2.js"></script>
<script>
    $(".select2").select2({
        allowClear:true
    });
</script>
<script>
    function guardar(){
        var formData = new FormData($("#form")[0]);  
        jsShowWindowLoad('Guardando Datos ...');
        $.ajax({
            type: 'POST',
            url: "jsonPptal/gf_datas2Json.php?action=1",
            data:formData,
            contentType: false,
            processData: false,
            success: function (data) {  
                jsRemoveWindowLoad();
                console.log(data);
                
                resultado = JSON.parse(data);
                var rta = resultado["rta"];
                var txt = resultado["msj"];
                var pr = resultado["pr"];
                if(rta>0) {
                    $("#mensaje").html(rta+' Registros Cargados Correctamente'+'<br/>'+pr+' Productos Creados Correctamente');
                    $("#modalMensaje").modal("show");
                } else {
                    $("#mensaje").html('No se ha podido Cargar la información <br/>'+txt);
                    $("#modalMensaje").modal("show");
                }
                 
            }
       });
    }
</script>
<?php require_once 'footer.php';?>
</body>
</html>

