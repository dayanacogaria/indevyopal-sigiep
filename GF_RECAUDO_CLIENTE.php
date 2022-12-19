<?php
#############################################################################
#       ******************     Modificaciones       ******************      #
#############################################################################
#26/03/2018 |Erica G. | ARCHIVO CREADO
#############################################################################
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
$con = new ConexionPDO();        
require './jsonPptal/funcionesPptal.php';
require_once 'head_listar.php';
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];

?>
<html>
    <head>
        <title>Recaudo Por Cliente</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script> 
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <script src="js/md5.pack.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <style>
            label #tercero-error, #banco-error, #tipoRecaudo-error, #numero-error, #fecha-error, #recaudo-error { 
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
            },
          });

          $(".cancel").click(function() {
            validator.resetForm();
          });
        });
        </script>

        <style>
         .form-control {font-size: 12px;}
        </style>
        <script>

                $(function(){
                $.datepicker.regional['es'] = {
                    closeText: 'Cerrar',
                    prevText: 'Anterior',
                    nextText: 'Siguiente',
                    currentText: 'Hoy',
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
                    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                    dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
                    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
                    weekHeader: 'Sm',
                    dateFormat: 'dd/mm/yy',
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: '',
                    changeYear: true
                };
                $.datepicker.setDefaults($.datepicker.regional['es']);
                $("#fecha").datepicker({changeMonth: true,}).val();


        });
        </script>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Recaudo Por Cliente</h2>
                    <?php if(empty($_GET['tercero']) && empty($_GET['id'])) { ?>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="tercero" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Cliente:</label>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                <select name="tercero" id="tercero" class="form-control select2" title="Seleccione Vigencia" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Cliente</option>';
                                        $tr = $con->Listar("SELECT DISTINCT t.id_unico, 
                                            IF(CONCAT_WS(' ',
                                            t.nombreuno,
                                            t.nombredos,
                                            t.apellidouno,
                                            t.apellidodos) 
                                            IS NULL OR CONCAT_WS(' ',
                                            t.nombreuno,
                                            t.nombredos,
                                            t.apellidouno,
                                            t.apellidodos) = '',
                                            (t.razonsocial),
                                            CONCAT_WS(' ',
                                            t.nombreuno,
                                            t.nombredos,
                                            t.apellidouno,
                                            t.apellidodos)) AS NOMBRE,
                                            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                 t.numeroidentificacion, 
                                            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                            FROM gp_factura f 
                                            LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
                                            ORDER BY NOMBRE");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).' - '.$tr[$i][2].'</option>'; 
                                        }
                                    ?>
                                </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php } elseif(!empty($_GET['tercero']) && empty($_GET['id'])) { ?>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group form-inline col-sm-12" style="margin-top: -5px; margin-left: -10px">
                                <div class="form-group form-inline  col-md-2 col-lg-2">
                                    <label for="tercero" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Cliente:</label>
                                    <input type="hidden" name="tercero_s" id="tercero_s" value="<?php echo $_GET['tercero'];?>">
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                    <select name="tercero" id="tercero" class="form-control select2" title="Seleccione Cliente" style="height: auto;" required>
                                            <?php 
                                                $trc = $con->Listar("SELECT DISTINCT t.id_unico, 
                                                    IF(CONCAT_WS(' ',
                                                    t.nombreuno,
                                                    t.nombredos,
                                                    t.apellidouno,
                                                    t.apellidodos) 
                                                    IS NULL OR CONCAT_WS(' ',
                                                    t.nombreuno,
                                                    t.nombredos,
                                                    t.apellidouno,
                                                    t.apellidodos) = '',
                                                    (t.razonsocial),
                                                    CONCAT_WS(' ',
                                                    t.nombreuno,
                                                    t.nombredos,
                                                    t.apellidouno,
                                                    t.apellidodos)) AS NOMBRE,
                                                    IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                         t.numeroidentificacion, 
                                                    CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                                    FROM gf_tercero t 
                                                    WHERE t.id_unico=".$_GET['tercero']);
                                                echo '<option value="'.$trc[0][0].'">'.ucwords(mb_strtolower($trc[0][1])).' - '.$trc[0][2].'</option>'; 
                                                $tr = $con->Listar("SELECT DISTINCT t.id_unico, 
                                                    IF(CONCAT_WS(' ',
                                                    t.nombreuno,
                                                    t.nombredos,
                                                    t.apellidouno,
                                                    t.apellidodos) 
                                                    IS NULL OR CONCAT_WS(' ',
                                                    t.nombreuno,
                                                    t.nombredos,
                                                    t.apellidouno,
                                                    t.apellidodos) = '',
                                                    (t.razonsocial),
                                                    CONCAT_WS(' ',
                                                    t.nombreuno,
                                                    t.nombredos,
                                                    t.apellidouno,
                                                    t.apellidodos)) AS NOMBRE,
                                                    IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                         t.numeroidentificacion, 
                                                    CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                                    FROM gp_factura f 
                                                    LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
                                                    WHERE t.id_unico != ".$_GET['tercero']."
                                                    ORDER BY NOMBRE");
                                                for ($i = 0; $i < count($tr); $i++) {
                                                   echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).' - '.$tr[$i][2].'</option>'; 
                                                }
                                            ?>
                                        </select>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2">
                                    <label for="buscarR" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado"></strong>Recaudo Por Cliente:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                    <select name="buscarR" id="buscarR" class="form-control select2" title="Seleccione Recaudo" style="height: auto;" required>
                                        <option>Recaudos Por Cliente</option>
                                        <?php
                                        $rowB = $con->Listar("SELECT DISTINCT 
                                                rc.id_unico,
                                                tp.nombre, 
                                                pg.numero_pago, 
                                                DATE_FORMAT(rc.fecha, '%d/%m/%Y')
                                            FROM gp_recaudos_cliente rc
                                            LEFT JOIN gp_pago pg ON rc.pago = pg.id_unico 
                                            LEFT JOIN gp_tipo_pago tp ON pg.tipo_pago = tp.id_unico 
                                            WHERE rc.tercero = ".$_GET['tercero']." 
                                            AND rc.parametrizacionanno = $anno");
                                        if(count($rowB)>0){
                                            $j=0;
                                            while ($j < count($rowB)) {
                                                echo '<option value="'.$rowB[$j][0].'">'.$rowB[$j][1].' - '.$rowB[$j][2].' '.$rowB[$j][3].'</option>';
                                                $j++;
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <script>
                                    $("#buscarR").change(function(){
                                        if($("#buscarR").val()!=""){
                                            window.location='GF_RECAUDO_CLIENTE.php?tercero='+$("#tercero_s").val()+'&id='+$("#buscarR").val();
                                        }
                                    })
                                </script>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-top:10px; float: right">
                                    <button style="margin-left:0px;" type="submit" class="btn sombra btn-primary" title="Guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i></button>
                                    <button onclick="location.href='GF_RECAUDO_CLIENTE.php'" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            <br/>
                            <div class="form-group form-inline " style="margin-top: -5px;margin-left: -10px">
                                <div class="form-group form-inline  col-md-2 col-lg-2">
                                    <label for="banco" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Banco:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2">
                                    <select name="banco" id="banco" class=" form-control select2" title="Seleccione Banco" style="height: auto;" required>
                                        <option value="">Banco</option>
                                        <?php 
                                        $rowB = $con->Listar("SELECT DISTINCT 
                                                ctb.id_unico,
                                                CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')'),
                                                c.id_unico 
                                            FROM gf_cuenta_bancaria ctb
                                            LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria 
                                            LEFT JOIN gf_cuenta c ON ctb.cuenta = c.id_unico 
                                            WHERE ctbt.tercero = $compania 
                                            AND ctb.parametrizacionanno = $anno 
                                            AND c.id_unico IS NOT NULL ORDER BY ctb.numerocuenta");
                                        if(count($rowB)>0){
                                            $j=0;
                                            while ($j < count($rowB)) {
                                                echo '<option value="'.$rowB[$j][0].'">'.$rowB[$j][1].'</option>';
                                                $j++;
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left: -30px;">
                                    <label for="tipoRecaudo" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tipo Recaudo:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left: -40px;">
                                    <select name="tipoRecaudo" id="tipoRecaudo" class="form-control select2" title="Seleccione Tipo Recaudo" style="height: auto; " required>
                                        <option value="">Tipo Recaudo</option>
                                        <?php 
                                        $tr = $con->Listar("SELECT * FROM gp_tipo_pago ORDER BY nombre ASC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                            echo '<option value="'.$tr[$i][0].'">'.$tr[$i][1].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <script>
                                    $("#tipoRecaudo").change(function(){
                                        var tipoR = $("#tipoRecaudo").val();
                                        if(tipoR!=""){
                                            var form_data = { action:7, tipo:tipoR };
                                            $.ajax({
                                                type: "POST",
                                                url: "jsonPptal/gf_facturaJson.php",
                                                data: form_data,
                                                success: function(response)
                                                { 
                                                    var numero = response.trim();
                                                    $("#numero").val(numero);
                                                }   
                                            }); 
                                        }                                        
                                    })
                                </script>
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  -60px;">
                                    <label for="numero" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Número:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  -30px;">
                                    <input name="numero" id="numero" class="col-sm-4 form-control" title="Seleccione Número" required style="margin-left: -10px; width: 95%"  readonly="true"/>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  -80px;">
                                    <label for="fecha" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Fecha:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  0px;">
                                    <input name="fecha" id="fecha" class="col-sm-4 form-control" title="Seleccione Fecha" required style="margin-left: -30px; width: 95%"/>
                                </div>
                               
                            </div>
                            <br/>
                            <input type="hidden" name="facturas" id="facturas" value="0">
                            <input type="hidden" name="valor_seleccionado" id="valor_seleccionado" value="0">
                            <input type="hidden" name="valor_retencion" id="valor_retencion" value="0">
                        </form>
                    </div>
                    <script>
                        function guardar(){
                            var formData = new FormData($("#form")[0]);  
                            jsShowWindowLoad('Generando Comprobante...');
                            var form_data = { action:1 };
                            $.ajax({
                                type: 'POST',
                                url: "jsonPptal/gf_facturaJson.php?action=23",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                { 
                                    jsRemoveWindowLoad();
                                    console.log(response);
                                    resultado = JSON.parse(response);
                                    var url = resultado["url"];
                                    var rta = resultado["rta"];
                                    //rta = 0;
                                    if(rta ==0){
                                        $("#mensaje").html('Información Guardada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            window.location=url;
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
                    <br/>
                    <?php }elseif(!empty($_GET['tercero']) && !empty($_GET['id'])){ ?>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardarDetalles()" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <?php 
                            #******** Consulta Datos Guardados ***********#
                            $row = $con->Listar("SELECT IF(CONCAT_WS(' ',
                                    t.nombreuno,
                                    t.nombredos,
                                    t.apellidouno,
                                    t.apellidodos) 
                                    IS NULL OR CONCAT_WS(' ',
                                    t.nombreuno,
                                    t.nombredos,
                                    t.apellidouno,
                                    t.apellidodos) = '',
                                    (t.razonsocial),
                                    CONCAT_WS(' ',
                                    t.nombreuno,
                                    t.nombredos,
                                    t.apellidouno,
                                    t.apellidodos)) AS NOMBRE,
                                    IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                    t.numeroidentificacion, 
                                        CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
                                    t.id_unico, 
                                    DATE_FORMAT(rc.fecha, '%d/%m/%Y'), 
                                    pg.id_unico, 
                                    pg.numero_pago, 
                                    tp.nombre, 
                                    rc.cnt, 
                                    rc.pptal,
                                    rc.causacion, 
                                    rc.facturas, 
                                    ctb.numerocuenta, 
                                    ctb.descripcion, rc.id_unico  
                                FROM gp_recaudos_cliente rc 
                                LEFT JOIN gf_tercero t ON rc.tercero = t.id_unico 
                                LEFT JOIN gp_pago pg ON rc.pago = pg.id_unico 
                                LEFT JOIN gp_tipo_pago tp ON pg.tipo_pago = tp.id_unico
                                LEFT JOIN gf_cuenta_bancaria ctb ON pg.banco = ctb.id_unico 
                                WHERE rc.id_unico = ".$_GET['id']);
                            ?>
                            <div class="form-group form-inline col-sm-12" style="margin-top: -5px; margin-left: -10px">
                                <div class="form-group form-inline  col-md-2 col-lg-2">
                                    <label for="tercero" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Cliente:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:-30px">
                                    <label for="tercero" class="col-sm-10 control-label" style="text-align: left; font-weight: normal"><?php echo ucwords(mb_strtolower($row[0][0])).' '.$row[0][1];?></label>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:-50px">
                                    <label for="banco" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Banco:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left: 0px;">
                                    <label for="banco" class="control-label text-left" style="text-align: left; font-weight: normal"><?php echo ucwords(mb_strtolower($row[0][11])).' '.$row[0][12];?></label>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left: 0px;">
                                    <label for="tipoRecaudo" class="col-sm-10 control-label" style="text-align: left;"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tipo Recaudo:</label>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left: 5px;">
                                    <label for="tipoRecaudo" class="control-label text-left " style="text-align: left; font-weight: normal"><?php echo ucwords(mb_strtolower($row[0][6]));?></label>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:-5px;">
                                    <label for="numero" class="col-sm-10 control-label" style="text-align: left;"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Número:</label>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left: -5px;">
                                    <label for="numero" class="col-sm-10 control-label" style="text-align: left; font-weight: normal"><?php echo $row[0][5];?></label>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left: 5px;">
                                    <label for="fecha" class="col-sm-10 control-label" style="text-align: left;"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Fecha:</label>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  -5px;">
                                    <label for="fecha" class="col-sm-10 control-label" style="text-align: left; font-weight: normal"><?php echo $row[0][3];?></label>
                                </div>
                            </div>
                            <br/>
                            <div class="form-group form-inline " style="margin-top: -5px;margin-left: -10px">
                                    <?php if(empty($row[0][10])){?>
                                    <div class="form-group form-inline  col-md-2 col-lg-2">
                                        <label for="clases" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Selección Facturas:</label>
                                    </div>
                                    <div class="form-group form-inline  col-md-2 col-lg-2" style="" id="divTipo" name="divTipo">
                                        <input type="radio" name="seleccion" id="seleccion" value="1" onclick="cambiar(1)">Selección<br/>
                                        <input type="radio" name="seleccion" id="seleccion" value="2" onclick="cambiar(2)">Valor
                                        <input type="hidden" name="tipo" id="tipo">
                                    </div>  
                                    <div class="form-group form-inline  col-md-3 col-lg-3" style="display:none" id="divValor" name="divValor">
                                        <input name="valor_i" onchange="cargarValores()" id="valor_i" class="col-sm-4 form-control" title="Seleccione Número" required style="margin-left: -10px; width: 95%"/>
                                    </div>
                                    <div class="form-group form-inline  col-md-5 col-lg-5" style="float:right">
                                        <button onclick="location.href='GF_RECAUDO_CLIENTE.php'" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
                                        <button onclick="guardarDetalles();" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Guardar"><i class="glyphicon glyphicon-floppy-save" aria-hidden="true"></i></button>
                                        <?php 
                                        if(!empty($row[0][7])){
                                            echo '<button onclick="retenciones()" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" aria-hidden="true">Registrar Retenciones</button>';
                                            # ** Buscar Si Hay Retenciones ** #
                                            $ret = $con->Listar("SELECT * FROM gf_retencion  WHERE comprobante = ".$row[0][7]);          
                                            if(count($ret)>0){
                                              echo '<button onclick="verretenciones()" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" aria-hidden="true">Ver Retenciones</button>';  
                                            }
                                        } ?>
                                    </div>
                                    <?php } else { ?>
                                    <div class="form-group form-inline  col-md-5 col-lg-5" style="float:left; margin-left: 50px; margin-top: 10px">
                                        <button onclick="location.href='GF_RECAUDO_CLIENTE.php'" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
                                        <button onclick="buscarRecaudo()" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Ver"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></button>
                                    </div>
                                    <?php }  ?>
                                    
                            </div>
                            <br/>
                            <input type="hidden" name="facturas" id="facturas" value="<?php if(!empty($row[0][10])){ echo $row[0][10];}else{ echo 0;}?>">
                            <input type="hidden" name="id_cnt" id="id_cnt" value="<?php echo $row[0][7]?>">
                            <input type="hidden" name="id_pptal" id="id_pptal" value="<?php echo $row[0][8]?>">
                            <input type="hidden" name="id_causacion" id="id_causacion" value="<?php echo $row[0][9]?>">
                            <input type="hidden" name="tercero" id="tercero" value="<?php echo $row[0][2];?>">
                            <input type="hidden" name="pago" id="pago" value="<?php echo $row[0][4];?>">
                            <input type="hidden" name="valor_seleccionado" id="valor_seleccionado" value="0">
                            <input type="hidden" name="rcliente" id="rcliente" value="<?php echo $row[0][13];?>">
                            <input type="hidden" name="facturasnm" id="facturasnm" value="0"/>
                        </form>
                    </div>
                    <script>
                        function cambiar(vl){
                            if(vl==1){
                                $("#facturas").val(0);
                                $("#valor_seleccionado").val(0);
                                $("#valorseleccionado").html('Valor Total: '+ 0);
                                $("#divValor").css('display','none');
                                $("#valor_i").val('');
                                 $("#body_table").html('');
                                var form_data={tipo:vl,tercero:$("#tercero").val(),
                                    valor_d:$("#valor_i").val(), action:10 }
                                $.ajax({
                                    type: 'POST',
                                    url: "jsonPptal/gf_facturaJson.php",
                                    data:form_data,
                                    success: function (data) {
                                        console.log(data);
                                        $("#body_table").html(data);
                                    }
                                })
                            }else{
                                $("#facturas").val(0);
                                $("#valor_seleccionado").val(0);
                                $("#valorseleccionado").html('Valor Total: '+ 0);
                                $("#divValor").css('display','none');
                                $("#valor_i").val('');
                                 $("#body_table").html('');
                                $("#divValor").css('display','inline-block')
                            }
                            
                        }
                        
                        function cargarValores(){
                            var form_data={tipo:2,tercero:$("#tercero").val(),
                                valor_d:$("#valor_i").val(), action:10 }
                            $.ajax({
                                type: 'POST',
                                url: "jsonPptal/gf_facturaJson.php",
                                data:form_data,
                                success: function (data) {
                                    console.log(data);
                                    $("#body_table").html(data);
                                    $("#facturas").val($("#facValor").val());
                                    var valorTotal = $("#SelValor").val();
                                    vh = formatV(valorTotal) ;
                                    $("#valor_seleccionado").val(valorTotal);
                                    $("#valorseleccionado").html('Valor Total: '+ vh);
                                    $("#facturasnm").html($("#facturasN").val());
                                }
                            })
                        }
                    </script>
                    <script>
                        function retenciones() {

                            var form_data={
                              id:$("#id_cnt").val(),
                              valorTotal :$("#valor_seleccionado").val(),
                              pptal :$("#id_pptal").val(),
                            };
                             $.ajax({
                                type: 'POST',
                                url: "MODAL_GF_RETENCIONES_FAC.php#mdlModificarReteciones1",
                                data:form_data,
                                success: function (data) {
                                    $("#mdlModificarReteciones1").html(data);
                                    $(".movi1").modal("show");
                                }
                            }).error(function(data,textStatus,jqXHR){
                                alert('data:'+data+'- estado:'+textStatus+'- jqXHR:'+jqXHR);
                            })
                        }
                    </script>
                    <script>
                        function verretenciones() {

                            var id = $("#id").val();
                            var form_data={
                              id:$("#id_cnt").val(),
                              valorTotal :$("#valor_seleccionado").val(),
                              pptal :$("#id_pptal").val(),
                            };
                             $.ajax({
                                type: 'POST',
                                url: "GF_MODIFICAR_RETENCIONES_MODAL.php#mdlModificarReteciones",
                                data:form_data,
                                success: function (data) {
                                    $("#mdlModificarReteciones").html(data);
                                    $(".movi").modal("show");
                                }
                            }).error(function(data,textStatus,jqXHR){
                                alert('data:'+data+'- estado:'+textStatus+'- jqXHR:'+jqXHR);
                            })
                        }
                    </script>
                    <script>
                        function buscarRecaudo(){
                            var pago = parseInt($("#pago").val());
                            //Validamos que no este vacio
                            if(!isNaN(pago)){
                                //Variable de envio para el envio ajax
                                var form_data = {
                                    action:9,
                                    pago:pago
                                };
                                //Envio ajax
                                $.ajax({
                                    type:'POST',
                                    url:'jsonPptal/gf_facturaJson.php',
                                    data:form_data,
                                    success: function(data,textStatus,jqXHR){
                                        console.log(data);
                                        window.open(data);
                                    },error : function(data,textStatus,jqXHR){
                                        alert('data : '+data+' , textStatus: '+textStatus+', jqXHR : '+jqXHR);
                                    }
                                });
                            }
                        }
                    </script>
                    <br/>
                    <div id="body_table"></div>  
                    <div class="col-md-2 col-lg-2">
                    <label id="valorseleccionado" name="valorseleccionado"></label>
                    </div>
                    <?php require_once './MODAL_GF_RETENCIONES_FAC.php';
                    require_once './GF_MODIFICAR_RETENCIONES_MODAL.php'; ?>
                    <!------Script Cambio De Check Y De Valor-->
                    <script>
                        function cambiovalor(id){
                            var valorTotal = $("#valor_seleccionado").val();
                            valorTotal = parseFloat(valorTotal);
                            var ncheck = "seleccion"+id;
                            var nvalor = "valort"+id;
                            var facturas = $("#facturas").val();
                            var nvalorr = "retencion"+id;
                            if($("#"+ncheck).prop('checked')){
                                valorTotal += parseFloat($("#"+nvalor).val());
                                facturas +=','+id;
                                $("#"+nvalorr).prop('disabled',false);
                            } else {
                                valorTotal -= parseFloat($("#"+nvalor).val());
                                facturas = facturas.replace(','+id, "");
                                $("#"+nvalorr).prop('disabled',true);
                                $("#"+nvalorr).val(0.00);
                            }
                            if(valorTotal<0){
                                valorTotal =0;
                            }
                            vh = formatV(valorTotal) ;
                            
                            $("#facturas").val(facturas)
                            $("#valor_seleccionado").val(valorTotal);
                            $("#valorseleccionado").html('Valor Total: '+ vh);
                        }
                    </script>
                    <script>
                        function cambiovalorVl(id){
                            var facturasnm = $("#facturasnm").val();
                            var ncheck = "seleccion"+id;
                            if($("#"+ncheck).prop('checked')){
                                console.log('checked');
                                facturasnm = facturasnm.replace(','+id, "");
                                $("#facturasnm").val(facturasnm);
                            } else {
                                facturasnm +=','+id;
                                $("#facturasnm").val(facturasnm);
                            }
                            var form_data={tipo:2,tercero:$("#tercero").val(),
                                valor_d:$("#valor_i").val(), 
                                facturasnm:$("#facturasnm").val(), 
                                action:10 }
                            $.ajax({
                                type: 'POST',
                                url: "jsonPptal/gf_facturaJson.php",
                                data:form_data,
                                success: function (data) {
                                    console.log(data);
                                    $("#body_table").html(data);
                                    $("#facturas").val($("#facValor").val());
                                    var valorTotal = $("#SelValor").val();
                                    vh = formatV(valorTotal) ;
                                    $("#valor_seleccionado").val(valorTotal);
                                    $("#valorseleccionado").html('Valor Total: '+ vh);
                                    $("#facturasnm").html($("#facturasN").val());
                                }
                            })
                        }
                    </script>
                    <script>
                        function guardarDetalles(){
                            if($("#valor_seleccionado").val()!=0){
                                //*** Validar Configuración ***///
                                var form_data={action:26, facturas:$("#facturas").val(),
                                pago:$("#pago").val()}
                                jsShowWindowLoad('Validando Configuración...');
                                $.ajax({
                                    type: 'POST',
                                    url: "jsonPptal/gf_facturaJson.php",
                                    data:form_data,
                                    success: function (data) {
                                        jsRemoveWindowLoad();
                                        console.log(data+'Cofigura');
                                        if(data ==0){
                                            var form_data={action:25, facturas:$("#facturas").val(),
                                            pago:$("#pago").val(),  cnt:$("#id_cnt").val(),pptal:$("#id_pptal").val(), 
                                            causacion:$("#id_causacion").val(), rcliente:$("#rcliente").val(), valor:$("#valor_seleccionado").val()}
                                            jsShowWindowLoad('Guardando Recaudo...');
                                            $.ajax({
                                                type: 'POST',
                                                url: "jsonPptal/gf_facturaJson.php",
                                                data:form_data,
                                                success: function (data) {
                                                    jsRemoveWindowLoad();
                                                    console.log(data+'detalels');
                                                    if(data ==1){
                                                        $("#mensaje").html('Información Guardada Correctamente');
                                                        $("#modalMensajes").modal("show");
                                                        $("#Aceptar").click(function(){
                                                            document.location.reload();
                                                        })
                                                    } else {
                                                        $("#mensaje").html('No Se Ha Podido Guardar Información');
                                                        $("#modalMensajes").modal("show");
                                                        $("#Aceptar").click(function(){
                                                            $("#modalMensajes").modal("hide");
                                                        })
                                                    }
                                                }
                                            })
                                        } else {
                                            $("#mensaje").html('Conceptos Sin Configurar');
                                            $("#modalMensajes").modal("show");
                                            $("#Aceptar").click(function(){
                                                $("#modalMensajes").modal("hide");
                                            })
                                        }
                                    }
                                })
                            }
                        }
                    </script>
                    <?php } ?>
                </div>
            </div>
        </div>
        <script>
            $("#tercero").change(function(){
                var tercero = $("#tercero").val();
                if(tercero !=""){
                    document.location='GF_RECAUDO_CLIENTE.php?tercero='+tercero;
                }
            })
        </script>
        <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensaje" name="mensaje"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal1" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información Eliminada Correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModal" onclick="cerrar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal1" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No Se ha Podido Eliminar La Información.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModal" onclick="cerrar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
         <div class="modal fade" id="infoM" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información Modificada Correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModal" onclick="cerrar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mdlModError" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No Se ha Podido Modificar La Información.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModal" onclick="cerrar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function cerrar(){
                $("#mdlModificarReteciones1").modal('hide');
                $(".modal-backdrop fade in").css('display','none');
                $(".modal-backdrop").css('display','none');
            }
        </script>
        <?php require_once 'footer.php'; ?>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
            $("#tercero").select2();
            $("#banco").select2();
            $("#tipoRecaudo").select2();
            $("#buscarR").select2();
            
        </script>
        
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
</html>
