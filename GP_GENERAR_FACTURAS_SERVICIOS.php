<?php
#############################################################################
#       ******************     Modificaciones       ******************      #
#############################################################################
#27/08/2018 |Erica G. | ARCHIVO CREADO
#############################################################################
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');   
require './jsonPptal/funcionesPptal.php';
require_once 'head_listar.php';
$con        = new ConexionPDO();     
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];

?>
<html>
    <head>
        <title>Generar Facturas Servicios Públicos</title>
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
            label #tercero-error, #banco-error, #tipoComprobante-error, #numero-error, #fechaF-error, #recaudo-error { 
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
         /*Modificación al diseño del la tabla Datatable*/
        table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px}
        .dataTables_wrapper .ui-toolbar{padding:2px}
        /*Modificación al body*/
        body{
            font-family: Arial;
            font-size: 10px;
        }

        /*Campos dinamicos label incrustados en la tabla*/
        .valorLabel{
            font-size: 10px;
            white-space:nowrap
        }
        .valorLabel:hover{
            cursor: pointer;
            color:#1155CC;
        }
        /*td de la tabla*/
        .campos{
            padding: 0px;
            font-size: 10px
        }
        .campoD{
            font-size: 12px;
            height: 19px;
            padding: 2px;
        }
        /*Estilos de cabeza y campos de la tabla
        */
        .cabeza{
            white-space:nowrap;
            padding: 20px;
        }
        .campos{
            padding:-20px;
        }

        .client-form input[type="text"]{
            width: 100%;
        }

        .client-form textarea{
            width: 100%;
            height: 34px;
        }

        .sombreado{
            box-shadow: 1px 1px 1px 1px gray;
            color:#fff;
            border-color:#1075C1;
        }

        .privada, .herencia{
            display: none;
        }

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
                $("#fechaF").datepicker({changeMonth: true,}).val();


        });
        </script>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Generar Facturación</h2>
                    <?php if(empty($_GET['p']) && empty($_GET['s1']) && empty($_GET['s2'])) { ?>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:buscar()" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="periodo" class="col-sm-1 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Periodo:</label>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                <select name="periodo" id="periodo" class="form-control select2" title="Seleccione Periodo" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Periodo</option>';
                                        $tr = $con->Listar("SELECT p.* FROM gp_periodo p 
                                            LEFT JOIN gp_ciclo c ON p.ciclo = c.id_unico 
                                            WHERE c.estado_facturacion NOT IN(7,9) 
                                            AND p.anno = $anno ORDER BY p.fecha_inicial DESC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).' - '.$tr[$i]['descripcion'].'</option>'; 
                                        }
                                    ?>
                                </select>
                                </div>
                                <label for="sector1" class="col-sm-1 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Sector Inicial:</label>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                <select name="sector1" id="sector1" class="form-control select2" title="Seleccione Sector Inicial" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Sector Inicial</option>';
                                        $tr = $con->Listar("SELECT * FROM gp_sector ORDER BY id_unico ASC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.$tr[$i][2].' - '.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                        }
                                    ?>
                                </select>
                                </div>
                                <label for="sector2" class="col-sm-1 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Sector Final:</label>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                <select name="sector2" id="sector2" class="form-control select2" title="Seleccione Sector Final" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Sector Final</option>';
                                        $tr = $con->Listar("SELECT * FROM gp_sector ORDER BY id_unico DESC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.$tr[$i][2].' - '.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                        }
                                    ?>
                                </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left: 10px; margin-top: 10px">
                                    <button type="submit" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-search" aria-hidden="true"></i></button>    
                                </div>
                            </div>
                        </form>
                    </div>
                    <script>
                        function buscar(){
                            var periodo = $("#periodo").val();
                            var sector1 = $("#sector1").val();
                            var sector2 = $("#sector2").val();
                            document.location ='GP_GENERAR_FACTURAS_SERVICIOS.php?p='+periodo+'&s1='+sector1+'&s2='+sector2;
                        }                                                                                        
                    </script>
                    <?php } else { ?>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -5px; margin-left:5px">
                                <label for="periodo" class="col-sm-1 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Periodo:</label>
                                <div class="form-group form-inline  col-md-2 col-lg-2">                                    
                                    <?php $tr = $con->Listar("SELECT p.* FROM gp_periodo p 
                                                WHERE p.id_unico = ".$_GET['p']);
                                    echo '<input type="hidden" name="periodo" id="periodo" value="'.$_GET['p'].'">';
                                    echo '<label for="periodo" class="control-label text-left" style="text-align: left; font-weight: normal">';
                                    echo  ucwords(mb_strtolower($tr[0][1].' '.$tr[0]['descripcion'])).'</label>';
                                    ?>
                                </div>
                                <label for="sector1" class="col-sm-1 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Sector Inicial:</label>
                                <div class="form-group form-inline  col-md-1 col-lg-1">
                                    <?php $tr = $con->Listar("SELECT * FROM gp_sector  
                                                WHERE id_unico = ".$_GET['s1']);
                                    echo '<input type="hidden" name="sector1" id="sector1" value="'.$_GET['s1'].'">';
                                    echo '<label for="sector1" class="control-label text-left" style="text-align: left; font-weight: normal">';
                                    echo  $tr[0][2].' - '.ucwords(mb_strtolower($tr[0][1])).'</label>';
                                    ?>
                                </div>
                                <label for="sector2" class="col-sm-1 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Sector Final:</label>
                                <div class="form-group form-inline  col-md-1 col-lg-1">                                    
                                    <?php $tr = $con->Listar("SELECT * FROM gp_sector  
                                                WHERE id_unico = ".$_GET['s2']);
                                    echo '<input type="hidden" name="sector2" id="sector2" value="'.$_GET['s2'].'">';
                                    echo '<label for="sector2" class="control-label text-left" style="text-align: left; font-weight: normal">';
                                    echo  $tr[0][2].' - '.ucwords(mb_strtolower($tr[0][1])).'</label>';
                                    ?>
                                </div>
                                <label for="fechaF" class="col-sm-1 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Fecha Facturación:</label>
                                <div class="form-group form-inline  col-md-2 col-lg-2">
                                    <input type="text" class="form-control" value="<?php echo date('d/m/Y')?>"style="width:150px;" id="fechaF" name="fechaF" required/>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-top: 10px; margin-left: 15px">
                                    <button type="submit" style="margin-left:5px;" type="button"  class="btn sombra btn-primary" title="Guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i></button>
                                    <button onclick="lecturasc()" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Informe Lecturas Críticas"><i class="glyphicon glyphicon-print" aria-hidden="true"></i></button>
                                    <button onclick="buscar1()" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Ver Facturas Periodo"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></button>
                                    <button onclick="buscar2()" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Buscar Facturas"><i class="glyphicon glyphicon-search" aria-hidden="true"></i></button>
                                    <button onclick="location.href='GP_GENERAR_FACTURAS_SERVICIOS.php'" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            <input type="hidden" name="lecturas" id="lecturas" value="0" >
                        </form>
                        <script>
                            function buscar1(){
                                var periodo = $("#periodo").val();
                                var sector1 = $("#sector1").val();
                                var sector2 = $("#sector2").val();
                                window.open('GP_LISTADO_FACTURAS_SERVICIOS.php?p='+periodo+'&s1='+sector1+'&s2='+sector2);
                            }
                            function buscar2(){
                                var periodo = $("#periodo").val();
                                var sector1 = $("#sector1").val();
                                var sector2 = $("#sector2").val();
                                window.open('GP_LISTADO_FACTURAS_SERVICIOS.php');
                            }
                            function lecturasc(){
                                var periodo = $("#periodo").val();
                                var sector1 = $("#sector1").val();
                                var sector2 = $("#sector2").val();
                                window.open('informes_servicios/INF_LECTURA.php?p='+periodo+'&s='+sector1+'&s2='+sector2+'&t=1');
                            }
                        </script>
                    </div>
                    <br/>
                    <div class="form-group" style="margin-top: -15px">
                        <div  style="text-align:right">
                            <input style="margin-top:10px; margin-bottom: 10px;" type="checkbox" onclick="if (this.checked) marcar(true); else marcar(false);" /><strong style=" font-size: 12px;">&nbsp;&nbsp;Marcar/Desmarcar Todos</strong>
                        </div>
                        <script type="text/javascript">
                                function marcar(status) 
                                {
                                    var tabla1 = document.getElementById("tableO");
                                    var eleNodelist1 = tabla1.getElementsByTagName("input");
                                    var lecturas = $("#lecturas").val();
                                    for (i = 0; i < eleNodelist1.length; i++) {
                                        var valorc = 'lectura'+i;
                                        var valor  = $("#"+valorc).val(); 
                                        if(typeof(valor) !== "undefined"){
                                            if(status==true){       
                                                lecturas +=','+valor;
                                            } else {
                                                lecturas = lecturas.replace(','+valor, "");
                                            }
                                        }
                                        $("#lecturas").val(lecturas);
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
                        <div class="table-responsive" style="margin-left: 0px; margin-right: 0px;margin-top:0px;">
                            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                                <table id="tableO" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <td class="oculto">Identificador</td>
                                            <td width="7%"></td>
                                            <td class="cabeza"><strong>Sector</strong></td>
                                            <td class="cabeza"><strong>Código Ruta</strong></td>
                                            <td class="cabeza"><strong>Predio</strong></td>
                                            <td class="cabeza"><strong>Tercero</strong></td>
                                            <td class="cabeza"><strong>Lectura Anterior</strong></td>
                                            <td class="cabeza"><strong>Lectura Actual</strong></td>
                                            <td class="cabeza"><strong>Total</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="oculto">Identificador</th>
                                            <th width="7%"></th>
                                            <th>Sector</th>
                                            <th>Código Ruta</th>
                                            <th>Predio</th>
                                            <th>Tercero</th>
                                            <th>Lectura Anterior</th>
                                            <th>Lectuara Actual</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $row = $con->Listar("SELECT l.id_unico, 
                                            s.nombre, s.codigo, p.codigo_catastral, 
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
                                            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
                                            p.direccion, l.valor, uvms.id_unico, 
                                            uv.codigo_ruta 
                                            FROM gp_lectura l 
                                            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON l.unidad_vivienda_medidor_servicio = uvms.id_unico 
                                            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                                            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
                                            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                                            LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
                                            LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
                                            LEFT JOIN gp_factura f ON f.unidad_vivienda_servicio = uvms.id_unico AND  f.periodo = ".$_GET['p']." 
                                            WHERE p.estado = 4 AND s.id_unico BETWEEN ".$_GET['s1']." AND ".$_GET['s2']." 
                                            AND l.periodo =".$_GET['p']." AND f.id_unico IS NULL 
                                            ORDER BY cast(s.codigo as unsigned),cast((replace(uv.codigo_ruta, '.','')) as unsigned) ASC");
                                        #** Buscar Lectura Anterior ***#
                                            $rowpa = $con->Listar("SELECT DISTINCT pa.* 
                                                FROM gp_periodo p 
                                                LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
                                                WHERE p.id_unico = ".$_GET['p']." ORDER BY pa.fecha_inicial DESC ");
                                            
                                            $periodoa =  $rowpa[0][0];
                                        for ($i = 0; $i < count($row); $i++) {                                            
                                            echo '<tr><td style="display: none;"></td>';
                                            echo '<td class="campos text-center">';
                                            echo '<input name="seleccion'.$i.'" id="seleccion'.$i.'" type="checkbox" onchange="cambiarV('.$i.')">';
                                            echo '<input name="lectura'.$i.'" id="lectura'.$i.'" type="hidden" value="'.$row[$i][0].'">';
                                            echo '</td>';
                                            echo '<td class="campos text-left">'.$row[$i][2].' - '.ucwords(mb_strtolower($row[$i][1])).'</td>';                  
                                            echo '<td class="campos text-left">'.$row[$i][9].'</td>';                  
                                            echo '<td class="campos text-left">'.$row[$i][3].' - '.$row[$i][6].'</td>';                   
                                            echo '<td class="campos text-left">'.ucwords(mb_strtolower($row[$i][4])).' - '.$row[$i][5].'</td>';  
                                            
                                            $la = $con->Listar("SELECT valor FROM gp_lectura 
                                                WHERE unidad_vivienda_medidor_servicio = ".$row[$i][8]." AND periodo = $periodoa");
                                            $la = $la[0][0];
                                            echo '<td class="campos text-right">'.$la.'</td>';
                                            $total =$row[$i][7]-$la;
                                            echo '<td class="campos text-right">'.$row[$i][7].'</td>';
                                            echo '<td class="campos text-right">'.$total.'</td>';
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <script>
                                    function cambiarV(i){
                                        var valor     = 'lectura'+i;
                                        var id      = $("#"+valor).val();
                                        var ncheck = 'seleccion'+i;
                                        var lecturas = $("#lecturas").val();
                                        if($("#"+ncheck).prop('checked')){
                                            lecturas +=','+id;
                                        } else {
                                            lecturas = lecturas.replace(','+id, "");
                                        }
                                        $("#lecturas").val(lecturas);

                                    }                                                                 
                                </script>
                            </div>
                        </div> 
                    </div>
                    <script>
                        function guardar(){
                            var formData = new FormData($("#form")[0]);
                            if($("#lecturas").val()=='0,' || $("#lecturas").val()=='0'){
                                $("#mensaje").html('Seleccione Registros');
                                $("#modalMensajes").modal("show");
                                $("#Aceptar").click(function(){
                                    $("#modalMensajes").modal("hide");
                                })
                            } else { 
                                jsShowWindowLoad('Generando Facturas...');
                                var form_data = { action:1 };
                                $.ajax({
                                type: 'POST',
                                url: "jsonServicios/gp_facturacionServiciosJson.php?action=1",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                { 
                                    jsRemoveWindowLoad();
                                    console.log(response);
                                    resultado = JSON.parse(response);
                                    var total = resultado["total"];
                                    var rta   = resultado["rta"];
                                    var html  = resultado["html"];
                                    if(rta ==0){
                                        $("#mensaje").html('Información Guardada Correctamente<br/>'+total+' Facturas Creadas');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            window.location.reload();
                                        })
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Guardar Información<br/>'+html);
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })
                                    }

                                }
                            });
                            }
                        }
                    </script>
                    <br/>
                    <?php }?>
                </div>
            </div>
        </div>
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
            $("#periodo").select2();
            $("#sector1").select2();
            $("#sector2").select2();
            $("#buscarR").select2();
            
        </script>
    </body>
</html>
