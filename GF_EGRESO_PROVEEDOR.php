<?php
#############################################################################
#       ******************     Modificaciones       ******************      #
#############################################################################
#27/06/2018 |Erica G. | ARCHIVO CREADO
#############################################################################
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
$con = new ConexionPDO();        
require './jsonPptal/funcionesPptal.php';
require './jsonSistema/funcionCierre.php';
require_once 'head_listar.php';
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];

?>
<html>
    <head>
        <title>Egreso Por Proveedor</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script> 
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <script src="js/md5.pack.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script>
        $(document).ready(function() {
            var i= 0;
            $('#tableO thead th').each( function () {
                if(i => 0) {
                    var title = $(this).text();
                    switch (i){
                        case 0:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 1:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 2:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 3:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 4:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 5:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 6:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                         case 7:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;    
                        
                    }
                    i = i+1;
                } else {
                    i = i+1;
                }
            });
            
            var i2= 0;
            $('#tableO2 thead th').each( function () {
                if(i2 => 0) {
                    var title = $(this).text();
                    switch (i2){
                        case 0:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 1:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 2:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 3:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 4:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 5:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 6:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 7:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;    
                            
                            
                            
                    }
                    i2 = i2+1;
                } else {
                    i2 = i2+1;
                }
            });
            
            
            
            // DataTable
            var table = $('#tableO').DataTable({
                "autoFill": true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                },
                scrollY: 120,
                "scrollX": true,
                scrollCollapse: true,
                paging: false,
                fixedColumns:   {
                    leftColumns: 1
                },
                'columnDefs': [{
                    'targets': 0,
                    'searchable':false,
                    'orderable':false,
                    'className': 'dt-body-center'
                }]
            });
            
            // DataTable
            var table2 = $('#tableO2').DataTable({
                "autoFill": true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                },
                scrollY: 120,
                "scrollX": true,
                scrollCollapse: true,
                paging: false,
                fixedColumns:   {
                    leftColumns: 1
                },
                'columnDefs': [{
                    'targets': 0,
                    'searchable':false,
                    'orderable':false,
                    'className': 'dt-body-center'
                }]
            });
            
            
            
            var i = 0;
            table.columns().every( function () {
                var that = this;
                if(i!=0) {
                    $( 'input', this.header() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                    i = i+1;
                } else {
                    i = i+1;
                }
            });
            
            var i2 = 0;
            table2.columns().every( function () {
                var that = this;
                if(i2!=0) {
                    $( 'input', this.header() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                    i2 = i2+1;
                } else {
                    i2 = i2+1;
                }
            });
        });
        </script>
        <style>
            label #tercero-error, #banco-error, #tipoComprobante-error, #numero-error, #fecha-error, #recaudo-error { 
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
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Egreso Por Proveedor</h2>
                    <?php if(empty($_GET['tercero']) && empty($_GET['id'])) { ?>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="tercero" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Proveedor:</label>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                <select name="tercero" id="tercero" class="form-control select2" title="Seleccione Vigencia" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Proveedor</option>';
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
                                            FROM gf_comprobante_pptal cp 
                                            LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                                            LEFT JOIN gf_tercero t ON cp.tercero = t.id_unico 
                                            WHERE tc.clasepptal = 16  AND t.compania = $compania AND tc.compania =  $compania
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
                                    <label for="tercero" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Proveedor:</label>
                                    <input type="hidden" name="tercero_s" id="tercero_s" value="<?php echo $_GET['tercero'];?>">
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                    <select name="tercero" id="tercero" class="form-control select2" title="Seleccione Proveedor" style="height: auto;" required>
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
                                                    FROM gf_comprobante_pptal cp 
                                                    LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                                                    LEFT JOIN gf_tercero t ON cp.tercero = t.id_unico 
                                                    WHERE tc.clasepptal = 16  
                                                    AND t.id_unico != ".$_GET['tercero']."
                                                    ORDER BY NOMBRE");
                                                for ($i = 0; $i < count($tr); $i++) {
                                                   echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).' - '.$tr[$i][2].'</option>'; 
                                                }
                                            ?>
                                        </select>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2">
                                    <label for="buscarR" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado"></strong>Egreso Por Proveedor:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                    <select name="buscarR" id="buscarR" class="form-control select2" title="Seleccione Egreso" style="height: auto;" required>
                                        <option>Egresos Por Proveedor</option>
                                        <?php
                                        $rowB = $con->Listar("SELECT DISTINCT 
                                                eg.id_unico,
                                                UPPER(tc.codigo), 
                                                cp.numero, 
                                                DATE_FORMAT(cp.fecha, '%d/%m/%Y')
                                            FROM gf_egreso_proveedor eg 
                                            LEFT JOIN gf_comprobante_pptal cp ON eg.pptal = cp.id_unico 
                                            LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                                            WHERE eg.tercero = ".$_GET['tercero']." 
                                            AND eg.parametrizacionanno = $anno");
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
                                            window.location='GF_EGRESO_PROVEEDOR.php?tercero='+$("#tercero_s").val()+'&id='+$("#buscarR").val();
                                        }
                                    })
                                </script>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-top:10px; float: right">
                                    <button style="margin-left:0px;" type="submit" class="btn sombra btn-primary" title="Guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i></button>
                                    <button onclick="location.href='GF_EGRESO_PROVEEDOR.php'" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
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
                                    <label for="tipoComprobante" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tipo Comprobante:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left: -40px;">
                                    <select name="tipoComprobante" id="tipoComprobante" class="form-control select2" title="Seleccione Tipo Comprobante" style="height: auto; " required>
                                        <option value="">Tipo Comprobante</option>
                                        <?php 
                                        $tr = $con->Listar("SELECT id_unico, UPPER(codigo), LOWER(nombre)        
                                            FROM gf_tipo_comprobante_pptal 
                                            WHERE clasepptal = 17
                                            AND tipooperacion = 1 
                                            AND vigencia_actual=1 
                                            AND compania = $compania 
                                            ORDER BY codigo");
                                        for ($i = 0; $i < count($tr); $i++) {
                                            echo '<option value="'.$tr[$i][0].'">'.$tr[$i][1].' - '.ucwords($tr[$i][2]).'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <script>
                                    $("#tipoComprobante").change(function(){
                                        var tipoc = $("#tipoComprobante").val();
                                        if(tipoc!=""){
                                        var form_data = {case: 17, tipocomprobante: tipoc}; //Estructura Uno 
                                            $.ajax({
                                                type: "POST",
                                                url: "consultasBasicas/busquedas.php",
                                                data: form_data,
                                                success: function (response)
                                                {
                                                    response = JSON.parse(response);
                                                    response = parseInt(response);
                                                    $("#numero").val(response);
                                                    $("#fecha").val("");
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
                                    <input name="fecha" id="fecha" class="col-sm-4 form-control" title="Seleccione Fecha" required style="margin-left: -30px; width: 95%" readonly="true"/>
                                </div>
                                <script>
                                $("#fecha").change(function (){
                                    var fecha = $("#fecha").val();
                                    var form_data = {case: 4, fecha: fecha};
                                    $.ajax({
                                        type: "POST",
                                        url: "jsonSistema/consultas.php",
                                        data: form_data,
                                        success: function (response)
                                        {
                                            console.log(response);
                                            if (response == 1) {
                                                $("#fecha").val('');
                                                $("#mensaje").html('Periodo cerrado');
                                                $("#modalMensajes").modal('show');
                                            } else {
                                                var tipo  = $("#tipoComprobante").val();
                                                var fecha = $("#fecha").val();
                                                var num   = $("#numero").val();
                                                if (tipo == '' || tipo == '') {
                                                    $("#fecha").val('');
                                                    $("#mensaje").html('Seleccione Tipo De Comprobante');
                                                    $("#modalMensajes").modal('show');
                                                    $("#Aceptar").click(function () {
                                                        $("#fecha").val("");
                                                        $("#modalMensajes").modal('hide');
                                                    })
                                                } else {
                                                    var comp ="";
                                                    var form_data = {estruc: 9, tipComPal: tipo, fecha: fecha, comp: comp, num: num};
                                                    $.ajax({
                                                        type: "POST",
                                                        url: "consultasBasicas/validarFechas.php",
                                                        data: form_data,
                                                        success: function (response)
                                                        {
                                                            console.log(response);
                                                            if (response == 1)
                                                            {
                                                                $("#fecha").val('');
                                                                $("#mensaje").html('Fecha Inválida');
                                                                $("#modalMensajes").modal('show');
                                                                $("#Aceptar").click(function () {
                                                                    $("#fecha").val("");
                                                                    $("#modalMensajes").modal('hide');
                                                                })
                                                            }
                                                        }
                                                    });
                                                }
                                            }
                                        }
                                    });
                                });
                                </script>
                            </div>
                            <br/>
                        </form>
                    </div>
                    <script>
                        function guardar(){
                            var formData = new FormData($("#form")[0]);  
                            jsShowWindowLoad('Generando Comprobante...');
                            var form_data = { action:1 };
                            $.ajax({
                                type: 'POST',
                                url: "jsonPptal/gf_egresoJson.php?action=8",
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
                                    t.id_unico as id_tercero, 
                                    DATE_FORMAT(eg.fecha, '%d/%m/%Y'), 
                                    cp.id_unico, 
                                    cp.numero, 
                                    tc.codigo, 
                                    eg.cnt as cnt, 
                                    eg.pptal as pptal,
                                    tc.nombre, 
                                    eg.cxp, 
                                    ctb.numerocuenta, 
                                    ctb.descripcion, eg.id_unico, 
                                    eg.fecha as fecha, 
                                    eg.parametrizacionanno as anno, 
                                    eg.banco as banco, 
                                    eg.id_unico as id_eg, 
                                    tcc.retencion as retencion, 
                                    cn.id_unico, 
                                    eg.fecha 
                                FROM gf_egreso_proveedor eg  
                                LEFT JOIN gf_tercero t ON eg.tercero = t.id_unico 
                                LEFT JOIN gf_comprobante_pptal cp ON eg.pptal = cp.id_unico 
                                LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                                LEFT JOIN gf_cuenta_bancaria ctb ON eg.banco = ctb.id_unico 
                                LEFT JOIN gf_comprobante_cnt cn ON eg.cnt = cn.id_unico 
                                LEFT JOIN gf_tipo_comprobante tcc ON cn.tipocomprobante = tcc.id_unico 
                                WHERE eg.id_unico = ".$_GET['id']);
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
                                    <label for="tipoComprobante" class="col-sm-10 control-label" style="text-align: left;"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tipo Comprobante:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left: 25px;">
                                    <label for="tipoComprobante" class="control-label text-left " style="text-align: left; font-weight: normal"><?php echo $row[0][6].' - '.ucwords(mb_strtolower($row[0][9]));?></label>
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
                                    <div class="form-group form-inline  col-md-5 col-lg-5" style="float:right">
                                        <button onclick="location.href='GF_EGRESO_PROVEEDOR.php'" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
                                        <button onclick="guardarDetalles();" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Guardar"><i class="glyphicon glyphicon-floppy-save" aria-hidden="true"></i></button>
                                        <?php 
                                        if($row[0]['retencion']==1){
                                        if(!empty($row[0][7])){
                                            echo '<button onclick="retenciones()" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" aria-hidden="true">Registrar Retenciones</button>';
                                            # ** Buscar Si Hay Retenciones ** #
                                            $ret = $con->Listar("SELECT * FROM gf_retencion  WHERE comprobante = ".$row[0][7]);          
                                            if(count($ret)>0){
                                              echo '<button onclick="verretenciones()" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" aria-hidden="true">Ver Retenciones</button>';  
                                            }
                                        } }?>
                                    </div>
                                    <input type="hidden" name="id_cnt" id="id_cnt" value="<?php echo $row[0]['cnt']?>">
                                    <input type="hidden" name="id_pptal" id="id_pptal" value="<?php echo $row[0]['pptal']?>">
                                    <input type="hidden" name="tercero" id="tercero" value="<?php echo $row[0][2];?>">
                                    <input type="hidden" name="banco" id="banco" value="<?php echo $row[0]['banco'];?>">
                                    <input type="hidden" name="valor_seleccionado" id="valor_seleccionado" value="0">
                                    <input type="hidden" name="cxp" id="cxp" value="0"/>
                                    <input type="hidden" name="id_eg" id="id_eg" value="<?php echo $row[0]['id_eg'];?>">
                                    <?php } else { ?>
                                    <input type="hidden" name="id_cnt" id="id_cnt" value="<?php echo $row[0]['cnt']?>">
                                    <input type="hidden" name="id_pptal" id="id_pptal" value="<?php echo $row[0]['pptal']?>">
                                    <input type="hidden" name="id_eg" id="id_eg" value="<?php echo $row[0]['id_eg'];?>">
                                    <div class="form-group form-inline  col-md-5 col-lg-5" style="float:left; margin-left: 50px; margin-top: 10px">
                                        <button onclick="location.href='GF_EGRESO_PROVEEDOR.php'" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
                                        <button onclick="buscar()" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Ver"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></button>
                                        <?php 
                                        $concilado = conciliadocnt($row[0]['cnt']);
                                        $cierre = cierrecnt($row[0]['cnt']);
                                        //var_dump(($concilidado ==1 || $cierre==1));
                                        if($concilidado ==1 || $cierre==1) { } else { ?>
                                        <button onclick="eliminar()" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Eliminar"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></button>
                                        <?php } ?>
                                    </div>
                                    <?php }  ?>
                                    
                            </div>
                            <br/>
                        </form>
                    </div>
                    <br/>
                    <script>
                        function buscar(){
                            var form_data={
                                action :11,
                                cnt :$("#id_cnt").val(),
                                pptal :$("#id_pptal").val(),
                            };
                             $.ajax({
                                type: 'POST',
                                url: "jsonPptal/gf_egresoJson.php",
                                data:form_data,
                                success: function (data) {
                                    console.log(data);
                                    window.open('GENERAR_EGRESO.php');
                                }
                            })
                            
                        }
                    </script>
                    <script>
                        function eliminar(){
                            
                            $("#modalMensajesEliminar").modal("show");
                            $("#AceptarEliminar").click(function(){
                            jsShowWindowLoad('Eliminando...');
                            var form_data={
                                action :12,
                                cnt :$("#id_cnt").val(),
                                pptal :$("#id_pptal").val(),
                                id_eg :$("#id_eg").val(),
                            };
                             $.ajax({
                                type: 'POST',
                                url: "jsonPptal/gf_egresoJson.php",
                                data:form_data,
                                success: function (data) {
                                    console.log(data);
                                    jsRemoveWindowLoad();
                                    if(data ==0){
                                        $("#mensaje").html('Información Eliminada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            window.location.reload();
                                        })
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Eliminar Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })
                                    }
                                }
                            })
                            });
                            $("#CancelarEliminar").click(function(){
                                $("#modalMensajesEliminar").modal("hide");
                            })
                        }
                    </script>
                    <div class="form-group" style="margin-top: -15px">
                            <?php if(empty($row[0][10])){ ?>
                            <strong style=" font-size: 12px; margin-left: 52px; margin-right: 5px;margin-top:10px; margin-bottom: 10px;">CUENTAS POR PAGAR</strong><input style="margin-left: 460px; margin-right: 5px;margin-top:10px; margin-bottom: 10px;" type="checkbox" onclick="if (this.checked) marcar(true); else marcar(false);" /><strong style=" font-size: 12px;">Marcar/Desmarcar Todos</strong>
                            <?php } ?>
                            <script type="text/javascript">
                                function marcar(status) 
                                {
                                    var tabla1 = document.getElementById("tableO");
                                    var eleNodelist1 = tabla1.getElementsByTagName("input");
                                    var valorTotal =  parseFloat($("#valor_seleccionado").val());
                                    var cxp = $("#cxp").val();
                                    for (i = 0; i < eleNodelist1.length; i++) {
                                        var valorc = 'valorct'+i;
                                        var valor  = $("#"+valorc).val();
                                        var cxs  = 'id_cxp'+i;
                                        var id  = $("#"+cxs).val();
                                        vh = formatV(valorTotal) ;
                                        if(typeof(valor) !== "undefined"){
                                            if(status==true){                                                
                                                valorTotal += parseFloat(valor);
                                                cxp +=','+id;
                                            } else {
                                                valorTotal -= parseFloat(valor);
                                                cxp = cxp.replace(','+id, "");
                                            }
                                        }
                                        $("#cxp").val(cxp);
                                        var vh = formatV(valorTotal) ;
                                        $("#valorseleccionado").html('Valor Total: '+ vh);
                                        $("#valor_seleccionado").val(valorTotal);
                                        if (eleNodelist1[i].type == 'checkbox'){
                                            
                                            if (status == null) {
                                                eleNodelist1[i].checked = !eleNodelist1[i].checked;
                                            }else {
                                                eleNodelist1[i].checked = status;
                                            }
                                        }
                                            
                                    }
                                }
                            </script>
                            <div class="table-responsive" style="margin-left: 50px; margin-right: 50px;margin-top:0px;">
                                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                                    <table id="tableO" class="table table-striped table-condensed display" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <td style="display: none;">Identificador</td>
                                                <td width="7%" class="cabeza"></td>   
                                                <td class="cabeza"><strong>Fecha</strong></td>
                                                <td class="cabeza"><strong>Tipo Comprobante</strong></td>
                                                <td class="cabeza"><strong>Número</strong></td>
                                                <td class="cabeza"><strong>Valor Total</strong></td>
                                                <td class="cabeza"><strong>Saldo</strong></td>
                                            </tr>
                                            <tr>
                                                <th class="cabeza" style="display: none;">Identificador</th>
                                                <th class="cabeza" width="7%"></th>
                                                <th class="cabeza">Fecha</th>
                                                <th class="cabeza">Tipo Comprobante</th>
                                                <th class="cabeza">Número</th>
                                                <th class="cabeza">Valor Total</th>
                                                <th class="cabeza">Saldo</th>                                                   
                                            </tr>
                                        </thead>    
                                        <tbody>
                                            <?php 
                                            if(empty($row[0][10])){
                                                $rowc = $con->Listar("SELECT DISTINCT 
                                                cp.id_unico, tc.id_unico, UPPER(tc.codigo), 
                                                cp.numero,
                                                LOWER(tc.nombre), 
                                                DATE_FORMAT(cp.fecha,'%d/%m/%Y'), 
                                                GROUP_CONCAT(dc.id_unico), SUM(dc.valor), 
                                                cp.fecha
                                                FROM gf_comprobante_pptal cp 
                                                LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                                                LEFT JOIN gf_detalle_comprobante_pptal dc ON dc.comprobantepptal =cp.id_unico 
                                                WHERE tc.clasepptal = 16 AND tc.tipooperacion=1 AND tc.vigencia_actual =1  
                                                AND cp.tercero = ".$_GET['tercero']."  
                                                AND cp.parametrizacionanno = ".$row[0]['anno']." 
                                                AND cp.fecha <='".$row[0]['fecha']."' 
                                                GROUP BY cp.id_unico  
                                                ORDER BY cp.fecha");
                                                for ($j = 0; $j < count($rowc); $j++) {
                                                    #*** Buscar Si CXP ha tenido afectacion ***#
                                                    $af =$con->Listar("SELECT SUM(dca.valor) FROM gf_detalle_comprobante_pptal dc 
                                                        LEFT JOIN gf_detalle_comprobante_pptal dca ON dc.id_unico = dca.comprobanteafectado 
                                                        WHERE dc.comprobantepptal=".$rowc[$j][0]);
                                                    $valora = $af[0][0];
                                                    $valor  = $rowc[$j][7];
                                                    $saldo  = $valor-$valora;
                                                    if($saldo>0){
                                                        echo '<tr><td style="display: none;"></td>';
                                                        echo '<td class="campos text-center">';
                                                        echo '<input name="seleccion'.$j.'" id="seleccion'.$j.'" type="checkbox" onchange="cambiarV('.$j.')">';
                                                        echo '<input type="hidden" name="valorct'.$j.'" id="valorct'.$j.'" value="'.$saldo.'"/>';
                                                        echo '<input type="hidden" name="id_cxp'.$j.'" id="id_cxp'.$j.'" value="'.$rowc[$j][0].'"/>';
                                                        echo '</td>';
                                                        echo '<td class="campos text-left">'.$rowc[$j][5].'</td>';                  
                                                        echo '<td class="campos text-left">'.$rowc[$j][2].' - '. ucwords($rowc[$j][4]).'</td>';                   
                                                        echo '<td class="campos text-left">'.$rowc[$j][3].'</td>';  
                                                        echo '<td class="campos text-right">'.number_format($valor,2,',','.').'</td>';  
                                                        echo '<td class="campos text-right">'.number_format($saldo,2,',','.').'</td>';
                                                        echo '</tr>';
                                                    }
                                                }
                                            } else {
                                                $rowc = $con->Listar("SELECT DISTINCT 
                                                cp.id_unico, tc.id_unico, UPPER(tc.codigo), 
                                                cp.numero,
                                                LOWER(tc.nombre), 
                                                DATE_FORMAT(cp.fecha,'%d/%m/%Y'), 
                                                GROUP_CONCAT(dc.id_unico), SUM(dc.valor), 
                                                cp.fecha
                                                FROM gf_comprobante_pptal cp 
                                                LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                                                LEFT JOIN gf_detalle_comprobante_pptal dc ON dc.comprobantepptal =cp.id_unico 
                                                WHERE cp.id_unico IN (".$row[0][10].")
                                                GROUP BY cp.id_unico  
                                                ORDER BY cp.fecha");
                                                for ($j = 0; $j < count($rowc); $j++) {
                                                    #*** Buscar Si CXP ha tenido afectacion ***#
                                                    $af =$con->Listar("SELECT SUM(dca.valor) FROM gf_detalle_comprobante_pptal dc 
                                                        LEFT JOIN gf_detalle_comprobante_pptal dca ON dc.id_unico = dca.comprobanteafectado 
                                                        WHERE dc.comprobantepptal=".$rowc[$j][0]." AND dca.comprobantepptal !=".$row[0]['pptal']);
                                                    $valora = $af[0][0];
                                                    $valor  = $rowc[$j][7];
                                                    $saldo  = $valor-$valora;
                                                    echo '<tr><td style="display: none;"></td>';
                                                    echo '<td class="campos text-center">';
                                                    echo '<input type="hidden" name="valorct'.$j.'" id="valorct'.$j.'" value="'.$saldo.'"/>';
                                                    echo '<input type="hidden" name="id_cxp'.$j.'" id="id_cxp'.$j.'" value="'.$rowc[$j][0].'"/>';
                                                    echo '</td>';
                                                    echo '<td class="campos text-left">'.$rowc[$j][5].'</td>';                  
                                                    echo '<td class="campos text-left">'.$rowc[$j][2].' - '. ucwords($rowc[$j][4]).'</td>';                   
                                                    echo '<td class="campos text-left">'.$rowc[$j][3].'</td>';  
                                                    echo '<td class="campos text-right">'.number_format($valor,2,',','.').'</td>';  
                                                    echo '<td class="campos text-right">'.number_format($saldo,2,',','.').'</td>';
                                                    echo '</tr>';
                                                }
                                            }
                                            
                                            ?>
                                            </tbody>
                                        </table>
                                    <div class="col-md-2 col-lg-2">
                                        <label id="valorseleccionado" name="valorseleccionado"></label>
                                    </div>
                                    </div>
                                </div>   
                        </div>
                    <script>
                        function cambiarV(i){
                            var valorc  = 'valorct'+i;
                            var valor   = $("#"+valorc).val();
                            var cxs     = 'id_cxp'+i;
                            var id      = $("#"+cxs).val();
                            var valorTotal =  parseFloat($("#valor_seleccionado").val());
                            vh = formatV(valorTotal) ;
                            var ncheck = 'seleccion'+i;
                            var cxp = $("#cxp").val();
                            if($("#"+ncheck).prop('checked')){
                                valorTotal += parseFloat(valor);
                                cxp +=','+id;
                            } else {
                                valorTotal -= parseFloat(valor);
                                cxp = cxp.replace(','+id, "");
                            }
                            $("#cxp").val(cxp);
                            var vh = formatV(valorTotal) ;
                            $("#valorseleccionado").html('Valor Total: '+ vh);
                            $("#valor_seleccionado").val(valorTotal);
                            
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
                    <br/>
                    <div id="body_table"></div>  
                    <div class="col-md-2 col-lg-2">
                    <label id="valorseleccionado" name="valorseleccionado"></label>
                    </div>
                    <?php require_once './MODAL_GF_RETENCIONES_FAC.php';
                    require_once './GF_MODIFICAR_RETENCIONES_MODAL.php'; ?>
                    <script>
                        function guardarDetalles(){
                            if($("#valor_seleccionado").val()!=0){
                                //*** Validar Configuración ***///
                                var form_data={action:9, cxp:$("#cxp").val(),
                                pago:$("#pago").val()}
                                jsShowWindowLoad('Validando Configuración...');
                                $.ajax({
                                    type: 'POST',
                                    url: "jsonPptal/gf_egresoJson.php",
                                    data:form_data,
                                    success: function (data) {
                                        jsRemoveWindowLoad();
                                        var resultado = JSON.parse(data);
                                        var rta = resultado["rta"];
                                        var mensaje = resultado["msj"];
                                        console.log(data+'Cofigura');
                                        if(rta ==0){
                                            var form_data={action:10, 
                                            cxp:$("#cxp").val(),
                                            cnt:$("#id_cnt").val(),
                                            pptal:$("#id_pptal").val(), 
                                            valor:$("#valor_seleccionado").val(),
                                            banco:$("#banco").val(),
                                            id_eg:$("#id_eg").val()}
                                            jsShowWindowLoad('Guardando Egreso...');
                                            $.ajax({
                                                type: 'POST',
                                                url: "jsonPptal/gf_egresoJson.php",
                                                data:form_data,
                                                success: function (data) {
                                                    jsRemoveWindowLoad();
                                                    console.log(data+'detalels');
                                                    if(data ==0){
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
                                            $("#mensaje").html(mensaje);
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
                    document.location='GF_EGRESO_PROVEEDOR.php?tercero='+tercero;
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
        <div class="modal fade" id="modalMensajesEliminar" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label>¿Desea Eliminar El Comprobante De Egreso?</label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="AceptarEliminar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" id="CancelarEliminar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
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
            $("#tipoComprobante").select2();
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
