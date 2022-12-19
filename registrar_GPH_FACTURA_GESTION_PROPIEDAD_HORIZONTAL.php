<?php
##*********************** Modificaciones *********************#
#14/09/2018 |Erica G. | Cambio Guardar
#*************************************************************#
require_once './head.php';
require_once './Conexion/conexion.php';
require_once './Conexion/ConexionPDO.php';
require_once './funciones/funciones_consulta.php';
require_once './modelFactura/factura.php';
require_once './modelFactura/comprobanteContable.php';
require_once './modelFactura/comprobantePptal.php';
require_once './modelFactura/detallefactura.php';
$anno =$_SESSION['anno'];
#Variables inicializadas en 0 o en vacio dependiendo el tipo de valor  que tendrán asignado
$fecha   = ""; $factura     = ""; $tipofactura      = ""; $numeroFactura = '';
$tercero = ""; $centroCosto = ""; $fechaVencimiento = ""; $descripcion   = "";
$estado  = ""; $idFactura   = ""; 
$estFat  = ""; $vendedor    = "";

$fat = new factura();
$cnt = new comprobanteContable();
$ptl = new comprobantePptal();
$det = new detalleFactura();
$con = new ConexionPDO();

if(empty($estado)){
    $estFat  = ucwords(mb_strtolower($fat->obtnerEstado(4)));
}

$tipo_co  = $fat->obtnerTipoCompania($_SESSION['compania']);
?>
<!-- Link o llamados de archivos -->
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<!-- select2 -->
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<!-- script para generar datapicker -->
<script type="text/javascript">
    $(function(){
        var fecha = new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth() + 1;
        if(dia < 10){
            dia = "0" + dia;
        }
        if(mes < 10){
            mes = "0" + mes;
        }
        //var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
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
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha").datepicker({changeMonth: true}).val();
        $("#fechaV").datepicker({changeMonth: true}).val();
        $("#txtFechaC").datepicker({changeMonth: true}).val();
    });
    /*
     * En esta función enviamos el valor el cual es número, esta función
     * redondea automaticamente los valores
     * @param {double} numero
     * @param {double} decimales
     * @returns {redo}
     */
    function redondeo(numero, decimales){
        var flotante = parseFloat(numero);
        var resultado = Math.round(flotante*Math.pow(10,decimales))/Math.pow(10,decimales);
        var falta = resultado - flotante;
        var redo = falta.toFixed(2);
        return redo;
    }

    /*
     * x = al número o valor decimal
     * r = al valor de redondeo puede ser 1,10,100.. etc
     * t = es el valor que hace falta para el redondeo
     * @param {double} x
     * @param {double} r
     * @returns {t}
     */
    function redondeaAlAlza(x,r) {
        xx = Math.floor(x/r)
        if (xx!=x/r) {xx++}
        var val = (xx*r);
        var rt = (val-x);
        var t = rt.toFixed(2);
        return t;
    }

    /*
     *
     * @param {type} id
     * @returns {undefined}
     */

    function redondeoTotal(valor,ajuste) {
        /*xx = Math.floor(valor/ajuste);
        if(xx!=valor/ajuste){xx++}
        var val = (xx*ajuste);
        return val;*/
        xx = Math.round(valor);
        return xx;
    }

    $(document).ready(function() {
        var i= 1;
        $('#tabla thead th').each( function () {
            if(i != 1){
                var title = $(this).text();
                switch (i){
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
                case 8:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 9:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
            }
            i = i+1;
        }else{
            i = i+1;
        }
    });
    // DataTable
    var table = $('#tabla').DataTable({
        "autoFill": true,
        "scrollX": true,
        "pageLength": 5,
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No Existen Registros...",
            "info": "Página _PAGE_ de _PAGES_ ",
            "infoEmpty": "No existen datos",
            "infoFiltered": "(Filtrado de _MAX_ registros)",
            "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
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
        if(i!=0){
        $( 'input', this.header() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
            i = i+1;
        }else{
            i = i+1;
        }
    } );
} );
</script>
<!-- Titulo del formulario -->
<title>Factura</title>
<!-- estilos secundarios -->
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
<script type="text/javascript">
$(document).ready(function() {
    $('#enviar').click(function(){
        var selected_ap = '';
        var tp_fac=$('#sltTipoFactura').val();
        var fecha_fac=$('#fecha').val();
        var fecha_ven=$('#fechaV').val();
        var centro_costo=$('#sltCentroCosto').val();
        var estado='4';
        var descrip=$('#txtDescripcion').val();
        var metodop=$('#sltMetodo').val();
        var formap=$('#txtFormaP').val();
        var tabla1 = document.getElementById("tableO");
        var elemtabla1 = tabla1.getElementsByTagName("input");
        var i=0;
        
            for (i = 0; i < elemtabla1.length; i++) {
                if (elemtabla1[i].type == 'checkbox')
                        if (elemtabla1[i].checked == true) {
                            selected_ap += elemtabla1[i].value+',';
                            
                        }                        
            }
        
        if(tp_fac === '' || fecha_fac === ''|| fecha_ven === ''|| centro_costo === '' || selected_ap===''){
             $("#myModalcomp").modal('show');
        }else{
            var fecha1 = $("#fecha").val();         //Fecha
            var fecha2 = $("#fechaV").val();        //Fecha de vencimiento
            //Dividimos las fechas
            var inicial = fecha1.split("/");        //Fecha
            var final =  fecha2.split("/");         //Fecha de vencimiento
            //creamos variables de fecha y la formateamos para año-mes-dia
            var dateStart = new Date(inicial[2],inicial[1],inicial[0]); //Fecha
            var dateEnd = new Date(final[2],final[1],final[0]);         //Fecha de vencimiento
            //Validamos que la fecha de vencimiento no sea mayor que la del campo fecha
            if(dateEnd < dateStart){
                $("#myModalFecha_Men").modal('show');  
            }
           else{
               var formData = new FormData($("#form")[0]);  
                jsShowWindowLoad('Guardando Datos...');
                var form_data = { action:1 };
                $.ajax({
                    type: 'POST',
                    url: "jsonPh/gph_facturacionJson.php?action=1&cod_ap="+selected_ap,
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        console.log(response);
                        var rta = response;
                        if(rta !=0){
                            $("#mensaje").html('Información Guardada Correctamente');
                            $("#myModalError").modal("show");
                            $("#btnErrorModal").click(function(){
                                document.location='listar_GPH_FACTURA_GESTION_PROPIEDAD_HORIZONTAL.php'
                            })
                        } else {
                            $("#mensaje").html('No Se Ha Podido Guardar Información');
                            $("#myModalError").modal("show");
                            $("#btnErrorModal").click(function(){
                                $("#myModalError").modal("hide");
                            })
                        } 

                    }
                });
           }
        }
        return false;
    });         
});    
</script>
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
<!-- Cierre de cabezera del formulario -->
</head>
<!-- Inicio o apertura del body -->
<body>
    <!-- Inicio de contenedor principal del formulario -->
    <div class="container-fluid text-center">
        <!-- Inicio de grib de boostrap -->
        <div class="row content">
            <!-- Llamado o invocación del menú -->
            <?php require_once 'menu.php'; ?>
            <!-- Inicio de la cabezera del formulario -->
            <div class="col-sm-8 col-md-8 col-lg-8 text-left">
                <!-- Titulo del formulario -->
                <h2 align="center" style="margin-top:-2px" class="tituloform">Factura Gestión Propiedad Horizontal</h2>
                <!-- Inicio de contenedor de la cabezera -->
                <div style="margin-top:-7px; border:4px solid #020324;border-radius: 10px;" class="client-form">
                    <!-- Inicio del formulario -->
                    <form id="form" name="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="">
                        <!-- Inicio de parrafo de texto de campos obligatorios -->
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>
                        <!-- Inicio de formulario en linea y agrupamiento de campos -->
                        <div class="form-group">
                            <label for="sltTipoFactura" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Tipo Factura:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltTipoFactura" id="sltTipoFactura" class="form-control select2"  title="Seleccione el tipo de factura" required="">
                                    <?php
                                    if(empty($tipofactura)){
                                        echo "<option value=\"\">Tipo Factura</option>";
                                        $sqlT = "SELECT id_unico, nombre FROM gp_tipo_factura WHERE servicio = 2 ORDER BY nombre ASC";
                                        $rest = $mysqli->query($sqlT);
                                        while($rowt = mysqli_fetch_row($rest)){
                                            echo "<option value=\"$rowt[0]\">".ucwords(mb_strtolower($rowt[1]))."</option>";
                                        }
                                    }else{
                                        $sqlT="SELECT id_unico, nombre FROM gp_tipo_factura WHERE id_unico = $tipofactura";
                                        $resultT=$mysqli->query($sqlT);
                                        $tpf= mysqli_fetch_row($resultT);
                                        echo '<option value="'.$tpf[0].'">'.ucwords(mb_strtolower($tpf[1])).'</option>';
                                        
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <label for="fecha" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Fecha:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input class="form-control" value="<?php echo $fecha ?>" type="text" name="fecha" id="fecha"  title="Ingrese la fecha" placeholder="Fecha" readonly required>
                            </div>
                            <div class="herencia col-sm-1 col-md-1 col-lg-1">
                                <a class="btn btn-primary sombreado"><i class="glyphicon glyphicon-tags"></i></a>
                            </div>
                            <label class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Fecha Vencimiento:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input class="form-control" value="<?php echo $fechaVencimiento ?>" type="text" name="fechaV" id="fechaV" title="Ingrese la fecha" placeholder="Fecha Vencimiento" readonly required>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -15px">
                            
                            <label class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Centro Costo:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltCentroCosto" id="sltCentroCosto" class="form-control select2" title="Seleccione centro de costo" style="padding:-2px" required>
                                    <?php
                                    if(!empty($centroCosto)){
                                        $sqlC="select id_unico,nombre from gf_centro_costo where id_unico=$centroCosto";
                                        cargar_combos($sqlC);
                                        $sqlD="select id_unico,nombre from gf_centro_costo where id_unico!=$centroCosto AND parametrizacionanno = $anno";
                                        cargar_combos($sqlD);
                                    }else{
                                        $sqlD = "SELECT id_unico, nombre FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno";
                                        cargar_combos($sqlD);
                                        $sqlC = "SELECT id_unico,nombre from gf_centro_costo WHERE id_unico != 'Varios' AND parametrizacionanno = $anno";
                                        cargar_combos($sqlC);
                                    }
                                    ?>
                                </select>
                            </div>
                            <label class="control-label col-sm-1 col-md-1 col-lg-1">Estado:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="txtEstado" id="txtEstado" class="form-control" value="<?php echo $estFat ?>" title="Estado" placeholder="Estado" readonly=""/>
                            </div>
                            <label class="control-label col-sm-2 col-md-2 col-lg-2">Acumulado por tercero:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="radio" name="acumulado" id="acumulado" value="1"/>Sí
                                <input type="radio" name="acumulado" id="acumulado" checked="checked" value="2"/>No
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -15px">
                            <label class="control-label col-sm-2 col-md-2 col-lg-2" for="txtDescripcion">Descripción:</label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input class="form-control" style="margin-top:0px;" type="textarea"name="txtDescripcion" id="txtDescripcion"  maxlength="900" placeholder="Descripción"  />
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -0px">
                            <label class="control-label col-sm-2 col-md-2 col-lg-2" for="sltMetodo">Método Pago:</label>
                            <div class="col-sm-2 col-md-2 c col-l-2">
                                <select name="sltMetodo" id="sltMetodo" title="Método Pago" class="form-control select2" required="required">
                                    <?php 
                                    $rowmp = $con->Listar("SELECT id_unico,nombre FROM gf_forma_pago ");
                                    $htmlmp='';
                                    for ($mp = 0; $mp< count($rowmp); $mp++) {
                                        $htmlmp .='<option value="'.$rowmp[$mp][0].'">'.$rowmp[$mp][1].'</option>';
                                    } echo $htmlmp;?>
                                </select>
                            </div>
                            <label class="control-label col-sm-2 col-md-2 col-lg-2" for="sltMetodo">Forma Pago:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <?php 
                                    if(!empty($idmp)){
                                        if($idmp==1){
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="1" checked="checked" style="margin-left:0px">Contado';
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="2" style="margin-left:10px">Crédito';
                                        } elseif($idmp==2) {
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="1" style="margin-left:0px">Contado';
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="2" style="margin-left:10px" checked="checked">Crédito';
                                        } else {
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="1" style="margin-left:0px" required="required">Contado';
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="2" style="margin-left:10px" required="required">Crédito';
                                        }
                                    } else {
                                        if(!empty($id_factura)){
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="1" style="margin-left:0px">Contado';
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="2" style="margin-left:10px">Crédito';
                                        } else {
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="1" checked="checked" style="margin-left:0px">Contado';
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="2" style="margin-left:10px">Crédito';
                                        }
                                    }
                                    ?>
                                    
                                </div>
                        </div>
                        <div class="form-group" style="margin-top: -15px">
                            <!--listado de apartamentos -->
                            <strong style=" font-size: 12px; margin-left: 52px; margin-right: 5px;margin-top:10px; margin-bottom: 10px;">ESPACIOS HABITABLES</strong><input style="margin-left: 460px; margin-right: 5px;margin-top:10px; margin-bottom: 10px;" type="checkbox" 
                                 onclick="if (this.checked) marcar(true); else marcar(false);" /><strong style=" font-size: 12px;">Marcar/Desmarcar Todos</strong>
                              <script type="text/javascript">
                                        function marcar(status) 
                                        {
                                           var tabla1 = document.getElementById("tableO");
                                           var eleNodelist1 = tabla1.getElementsByTagName("input");
                                           
                                            for (i = 0; i < eleNodelist1.length; i++) {
                                                if (eleNodelist1[i].type == 'checkbox')
                                                    if (status == null) {
                                                        eleNodelist1[i].checked = !eleNodelist1[i].checked;
                                                    }
                                                    else eleNodelist1[i].checked = status;
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
                                                    <!-- Actualización 24 / 02 16:43 No es necesario mostrar el nombre del empleado
                                                    <td class="cabeza"><strong>Empleado</strong></td>
                                                    -->
                                                    <td class="cabeza"><strong>Torre</strong></td>
                                                    <td class="cabeza"><strong>Piso</strong></td>
                                                    <td class="cabeza"><strong>Apartamento</strong></td>
                                                    <td class="cabeza"><strong>Seleccione</strong></td>
                                                    
                                                </tr>
                                                <tr>
                                                    <th class="cabeza" style="display: none;">Identificador</th>
                                                    <th class="cabeza" width="7%"></th>
                                                    <!-- Actualización 24 / 02 16:43 No es necesario mostrar el nombre del empleado
                                                    <th class="cabeza">Empleado</th>
                                                    -->
                                                    <th class="cabeza">Torre</th>
                                                    <th class="cabeza">Piso</th>
                                                    <th class="cabeza">Apartamento</th>
                                                    <th class="cabeza">Seleccione</th>                                                   
                                                    
                                                </tr>
                                            </thead>    
                                            <tbody>
                                                <?php 
                                               
                                                 $sql1="SELECT 
                                                        ehap.id_unico as id_apartamento,
                                                        ehap.codigo as num_apartamento,
                                                        ehp.id_unico as id_piso,
                                                        ehp.codigo as num_piso,
                                                        eht.id_unico as id_torre,
                                                        eht.codigo as num_torre,
                                                        CONCAT(eht.codigo,' - ',eht.descripcion) as descrip

                                                        from gh_espacios_habitables ehap
                                                        left join gh_espacios_habitables ehp on ehp.id_unico=ehap.asociado
                                                        left join gh_espacios_habitables eht on eht.id_unico=ehp.asociado
                                                        where ehap.estado = 1 AND (ehap.tipo='3' OR ehap.tipo='5' OR ehap.tipo='7') order by ehp.id_unico asc";
                                                
                                                  $re = $mysqli->query($sql1);
                                                while ($rowC = mysqli_fetch_row($re)) {  
                                                        $id_ap = $rowC[0];
                                                        $n_ap = $rowC[1];
                                                        $id_piso = $rowC[2];
                                                        $n_piso = $rowC[3];
                                                        $id_torre = $rowC[4];
                                                        $n_torre = $rowC[6];
                                                        
                                                        ?>
                                                 <tr>
                                                    <td style="display: none;"><?php echo $row[0]?></td>
                                                    <td>
                                                        
                                                    </td>                                        
                                                    <td class="campos text-center"><?php echo $n_torre;?></td>                   
                                                    <td class="campos text-center"><?php echo $n_piso;?></td>  
                                                    <td class="campos text-center"><?php echo $n_ap;?></td>  
                                                    <td class="campos text-center">
                                                        <input name="cod" id="sel" type="checkbox" value="<?php echo $id_ap ?>">
                                                    </td> 
                                                    
                                                </tr> 
                                                <?php }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>                        
                            
                                <button id="enviar" type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top:10px; width:40px; margin-bottom: -10px;margin-left: 800px ; "><li class="glyphicon glyphicon-floppy-disk"></li></button>   
                                
                            
                        </div>
                        <div class="form-group" style="margin-top: -25px"> 
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            //funcion para ihnabilitar el campo
            function inhabilitar(id){
                <?php
                if(!empty($idCnt) || !empty($idPptal)){
                    $sqlD100="select dtp.id_unico,dtc.id_unico from gf_detalle_comprobante dtc left join gf_detalle_comprobante_pptal dtp on dtc.detallecomprobantepptal=dtp.id_unico where dtc.comprobante=$idCnt and dtp.comprobantepptal=$idPptal";
                    $result100=$mysqli->query($sqlD100);
                    $conteo=mysqli_num_rows($result100);
                    if($conteo>0){ ?>
                       $("#btnDel"+id).prop('disabled', true);
                    <?php }else{ ?>
                       $("#btnDel"+id).prop('disabled',false);
                    <?php }
                }
                ?>
            }

            //Función para eliminar
            function eliminar(id){
                var result = '';
                var form_data = { action:6, 
                    id_unico:id
                };
                $("#myModal").modal('show');
                $("#ver").click(function(){
                    $("#mymodal").modal('hide');
                    $.ajax({
                        type:"POST",
                        data:form_data,
                        url: "jsonPptal/gf_facturaJson.php",
                        success: function (data) {
                            result = JSON.parse(data);
                            if(result==1) {
                                var form_data = { action:'Eliminar', 
                                    id_unico:id
                                };
                                $.ajax({
                                    type:"POST",
                                    data:form_data,
                                    url: "access.php?controller=DetalleFactura&action=Eliminar",
                                    success: function (data) {
                                    result = JSON.parse(data);
                                    if(result==true)
                                      $("#mdlEliminado").modal('show');
                                    else
                                      $("#mdlNoeliminado").modal('show');
                                    }
                                });
                            } else {
                              $("#mdlNoeliminado").modal('show');
                            }
                        }
                    });
                    
                });
            }
            //Función para guardar datos del detalle
            function guardarCambios(id){
                var sltConcepto = 'sltconcepto'+id;
                var txtCantidad = 'txtcantidad'+id;
                var txtValor = 'txtValor'+id;
                var txtIva = 'txtIva'+id;
                var txtImpoconsumo = 'txtImpoconsumo'+id;
                var txtAjustepeso = 'txtAjustepeso'+id;
                var txtValorAjuste = 'txtValorAjuste'+id
                var form_data = {
                    id:id,
                    concepto:$("#"+sltConcepto).val(),
                    cantidad:$("#"+txtCantidad).val(),
                    valor:$("#"+txtValor).val(),
                    iva:$("#"+txtIva).val(),
                    impoconsumo:$("#"+txtImpoconsumo).val(),
                    ajustepeso:$("#"+txtAjustepeso).val(),
                    valorAjuste:$("#"+txtValorAjuste).val()
                };
                var result = '';
                $.ajax({
                    type: 'POST',
                    url: "access.php?controller=DetalleFactura&action=Modificar",
                    data:form_data,
                    success: function (data) {
                        console.log(data);
                        result = JSON.parse(data);
                        if(result==true){
                            $("#mdlModificado").modal('show');
                        }else{
                            $("#mdlNomodificado").modal('show');
                        };
                    }
                });
            }

            //función para ocultar los label y mostrar los campos para modificar
            function modificar(id){
                //En el que valida si el campos idPrevio tiene un valor
                //en el que asignamos los nombres de los labels y campos
                //y el asignamos la idPrevio y a su vez solo mostramos los labels
                if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
                    var lblConceptoC = 'concepto'+$("#idPrevio").val();
                    var sltConceptoC = 'sltconcepto'+$("#idPrevio").val();
                    var lblCantidadC = 'lblCantidad'+$("#idPrevio").val();
                    var txtCantidadC = 'txtcantidad'+$("#idPrevio").val();
                    var lblValorC = 'lblValor'+$("#idPrevio").val();
                    var txtValorC = 'txtValor'+$("#idPrevio").val();
                    var lblIvaC = 'lblIva'+$("#idPrevio").val();
                    var txtIvaC = 'txtIva'+$("#idPrevio").val();
                    var lblImpoconsumoC = 'lblImpoconsumo'+$("#idPrevio").val();
                    var txtImpoconsumoC = 'txtImpoconsumo'+$("#idPrevio").val();
                    var lblAjustepesoC = 'lblAjustepeso'+$("#idPrevio").val();
                    var txtAjustepesoC = 'txtAjustepeso'+$("#idPrevio").val();
                    var guardarC = 'guardar'+$("#idPrevio").val();
                    var cancelarC = 'cancelar'+$("#idPrevio").val();
                    var tablaC = 'tab'+$("#idPrevio").val();
                    var lblValorAjusteC = 'lblValorAjuste'+$("#idPrevio").val();
                    var txtValorAjusteC = 'txtValorAjuste'+$("#idPrevio").val();

                    $("#"+lblConceptoC).css('display','block');
                    $("#"+sltConceptoC).css('display','none');
                    $("#"+lblCantidadC).css('display','block');
                    $("#"+txtCantidadC).css('display','none');
                    $("#"+lblValorC).css('display','block');
                    $("#"+txtValorC).css('display','none');
                    $("#"+lblIvaC).css('display','block');
                    $("#"+txtIvaC).css('display','none');
                    $("#"+lblImpoconsumoC).css('display','block');
                    $("#"+txtImpoconsumoC).css('display','none');
                    $("#"+lblAjustepesoC).css('display','block');
                    $("#"+txtAjustepesoC).css('display','none');
                    $("#"+guardarC).css('display','none');
                    $("#"+cancelarC).css('display','none');
                    $("#"+tablaC).css('display','none');
                    $("#"+lblValorAjusteC).css('display','block');
                    $("#"+txtValorAjusteC).css('display','none');
                }
                //aqui creamos las variables similares a las anteriores en la que asignamos el nombre y el id
                var lblConcepto = 'concepto'+id;
                var sltConcepto = 'sltconcepto'+id;
                var lblCantidad = 'lblCantidad'+id;
                var txtCantidad = 'txtcantidad'+id;
                var lblValor = 'lblValor'+id;
                var txtValor = 'txtValor'+id;
                var lblIva = 'lblIva'+id;
                var txtIva = 'txtIva'+id;
                var lblImpoconsumo = 'lblImpoconsumo'+id;
                var txtImpoconsumo = 'txtImpoconsumo'+id;
                var lblAjustepeso = 'lblAjustepeso'+id;
                var txtAjustepeso = 'txtAjustepeso'+id;
                var lblValorAjuste = 'lblValorAjuste'+id;
                var txtValorAjuste = 'txtValorAjuste'+id;
                var guardar = 'guardar'+id;
                var cancelar = 'cancelar'+id;
                var tabla = 'tab'+id;
                //ocultamos los labels y mostramos los campos ocultos
                $("#"+sltConcepto).css('display','block');
                $("#"+lblConcepto).css('display','none');
                $("#"+txtCantidad).css('display','block');
                $("#"+lblCantidad).css('display','none');
                $("#"+txtValor).css('display','block');
                $("#"+lblValor).css('display','none');
                $("#"+txtIva).css('display','block');
                $("#"+lblIva).css('display','none');
                $("#"+txtImpoconsumo).css('display','block');
                $("#"+lblImpoconsumo).css('display','none');
                $("#"+lblAjustepeso).css('display','none');
                $("#"+txtAjustepeso).css('display','block');
                $("#"+lblValorAjuste).css('display','none');
                $("#"+txtValorAjuste).css('display','block')
                $("#"+guardar).css('display','block');
                $("#"+cancelar).css('display','block');
                $("#"+tabla).css('display','block');
                //Asignamos el valor de la id al campo id actual
                $("#idActual").val(id);
                //Y preguntamos si el valor del idPrevio es diferente a la id
                //y se la asignamos
                if($("#idPrevio").val() != id){
                    $("#idPrevio").val(id);
                }
            }

            function cambioValor(id){
                $("#sltconcepto"+id).change(function() {
                    var form_data = {
                        concepto:$("#sltconcepto"+id).val(),
                        proceso:1
                    };
                    $.ajax({
                        type: 'POST',
                        url: "consultasFacturacion/consultarValor.php",
                        data:form_data,
                        success: function (data) {
                            if(data!=""){
                                $("#txtValor"+id).html(data).fadeIn();
                            }
                        }
                    });
                });
            }
            
            function cancelar(id){
                //Creamos las variables en la que cargamos los nombres de los campos y label y le concatenamos la id
                var lblConcepto = 'concepto'+id;
                var sltConcepto = 'sltconcepto'+id;
                var lblCantidad = 'lblCantidad'+id;
                var txtCantidad = 'txtcantidad'+id;
                var lblValor = 'lblValor'+id;
                var txtValor = 'txtValor'+id;
                var lblIva = 'lblIva'+id;
                var txtIva = 'txtIva'+id;
                var lblImpoconsumo = 'lblImpoconsumo'+id;
                var txtImpoconsumo = 'txtImpoconsumo'+id;
                var lblAjustepeso = 'lblAjustepeso'+id;
                var txtAjustepeso = 'txtAjustepeso'+id;
                var lblValorAjuste = 'lblValorAjuste'+id;
                var txtValorAjuste = 'txtValorAjuste'+id;
                var guardar = 'guardar'+id;
                var cancelar = 'cancelar'+id;
                var tabla = 'tab'+id;
                //ocultamos los campos y mostramos los labels
                $("#"+lblConcepto).css('display','block');
                $("#"+sltConcepto).css('display','none');
                $("#"+lblCantidad).css('display','block');
                $("#"+txtCantidad).css('display','none');
                $("#"+lblValor).css('display','block');
                $("#"+txtValor).css('display','none');
                $("#"+lblIva).css('display','block');
                $("#"+txtIva).css('display','none');
                $("#"+lblImpoconsumo).css('display','block');
                $("#"+txtImpoconsumo).css('display','none');
                $("#"+lblAjustepeso).css('display','block');
                $("#"+txtAjustepeso).css('display','none');
                $("#"+lblValorAjuste).css('display','block');
                $("#"+txtValorAjuste).css('display','none');
                $("#"+guardar).css('display','none');
                $("#"+cancelar).css('display','none');
                $("#"+tabla).css('display','none');
            }

            function modificarPago(id_cnt,id_pptal){
                var id = $("#id").val();
                var fecha = $("#fecha").val();
                var tercero = $("#sltTercero").val();
                var centroCosto = $("#sltCentroCosto").val();
                var fechavence = $("#fechaV").val();
                var descripcion = $("#txtDescripcion").val();

                var form_data = {
                    id:id,
                    fecha:fecha,
                    tercero:tercero,
                    centrocosto:centroCosto,
                    fechaVencimiento:fechavence,
                    descripcion:descripcion,
                    id_cnt:id_cnt,
                    id_pptal:id_pptal
                };

                var result='';
                $.ajax({
                    type: 'POST',
                    url: "access.php?controller=Factura&action=Modificar",
                    data:form_data,
                    success: function (data) {
                        result = JSON.parse(data);
                        if (result==true) {
                            $("#mdlModificado").modal('show');
                        }else{
                            $("#mdlNomodificado").modal('show');
                        }
                    }
                });
            }

            function cargarValor(id){
                $("#sltconcepto"+id).append(function(){

                    var form_data = {
                        is_ajax:1,
                        data:+id
                    };

                    $.ajax({
                        type: 'POST',
                        url: "consultasFacturacion/consultarValorT.php",
                        data:form_data,
                        success: function (data) {
                            $("#txtValor"+id).html(data).fadeIn();
                        }
                    });
                });
            }

            function limpiarCampos(){
                $('#sltConcepto').prop('selectedIndex',0);
                $("#sltRubroFuente").prop('selectedIndex',0);
                $("#txtCantidad").val('');
                $("#sltValor").prop('selectedIndex',0);
                $("#txtIva").val('');
                $("#txtImpoconsumo").val('');
                $("#txtAjustePeso").val('');
                $("#txtValorA").val('');
            }

            function consultarF(){
                //Captura de variables
                var factura = $("#sltBuscar").val();
                //Array de envio
                var form_data = {
                    action:2,
                    factura:factura
                };
                //Envio ajax
                $.ajax({
                    type:'POST',
                    url:'jsonPptal/gf_facturaJson.php',
                    data:form_data,
                    success: function(data){
                        console.log(data);
                        window.location = data;
                    }
                });
            }
            //Eliminar Datos de comprobante, detalle, pptal, detalle, factura
            function eliminarDatos(factura,cnt,pptal){
                //Validamos que factura no este vacio o sea igual a 0
                if(factura !== 0){
                    //Validamos que la factura cnt y pptal no esten vacias
                    $("#modalEliminarFactura").modal('show');
                    $("#btnEC").click(function(){
                        if(cnt !== 0 && pptal !== 0) {
                            //Variable de envio ajax
                            var form_data = {
                                existente   : 50,
                                factura     : factura,
                                pptal       : pptal,
                                cnt         : cnt
                            };
                            var result = '';
                            //Envio ajax
                            $.ajax({
                                type:'POST',
                                url: 'access.php?controller=DetalleFactura&action=EliminarTodos',
                                data: form_data,
                                success : function(data,textStatus,jqXHR) {
                                    result = JSON.parse(data);
                                    if(result==true) {
                                        $("#mdlEliminado").modal('show');
                                        $("#ver1").click(function(){
                                            window.location.reload();
                                        });
                                    } else{
                                        $("#mdlNoeliminado").modal('show');
                                    }
                                    console.log(data);
                                }
                            }).error(function(data,textError) {
                                console.log('Data :'+data+', Error:'+textError);
                            });
                        }
                    });
                }
            }
            /*Función de suma de dias de vencimiento*/
            sumaFecha = function(d, fecha){
                var Fecha = new Date();
                var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
                var sep = sFecha.indexOf('/') != -1 ? '/' : '-';
                var aFecha = sFecha.split(sep);
                var fecha = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0];
                fecha= new Date(fecha);
                fecha.setDate(fecha.getDate()+parseInt(d));
                var anno=fecha.getFullYear();
                var mes= fecha.getMonth()+1;
                var dia= fecha.getDate();
                mes = (mes < 10) ? ("0" + mes) : mes;
                dia = (dia < 10) ? ("0" + dia) : dia;
                var fechaFinal = dia+sep+mes+sep+anno;
                return (fechaFinal);
            }

            function sum_v(cantidad, valor, iva, x){
                var cantidad = parseFloat(cantidad);
                var valor = parseFloat(valor);
                var iva   = parseFloat(iva);
                var oper  = (cantidad * valor);
                $("#txtValorAjuste"+x).val(oper+iva);
            }
        </script>
    </div>
</body>
    <?php require_once 'footer.php' ?>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript">
        $(".select2").select2();
    </script>
    
    <!-- Inicio de modales de modificado -->
    <div class="modal fade" id="mdlModificado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información modificada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlNomodificado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha podido modificar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin de modales de modificado -->
    <!-- Inicio de modal para validación de tipo -->
    <div class="modal fade" id="mdltipofactura" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Seleccione un tipo de factura.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="tbmtipoF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalcomp" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Asegurese que los campos obligatorios esten diligenciados.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver8"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
    <div class="modal fade" id="myModalError" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                  <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                  </div>
                  <div class="modal-body" style="margin-top: 8px">
                      <labe id="mensaje" name="mensaje" style="font-weight:light"></labe>
                  </div>
                  <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrorModal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                    </button>
                  </div>
                </div>
            </div>
        </div>
    <div class="modal fade" id="myModalFecha_Men" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Asegurese que la fecha de la factura no sea menor que la fecha de vencimiento.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver8"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
    <!-- Fin de modal para validación de tipo -->
    <!-- Inicio de modal de validación de fecha -->
    <div class="modal fade" id="mdlfecha" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p id="mensaje_fecha"></p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin de modal de validación de fecha -->
    <!-- Inicio de modales para eliminado -->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Factura?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlEliminado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información eliminada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlNoeliminado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdltipoFactura" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Seleccione un tipo de factura.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnTipoF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin de modales para eliminado -->
    <!-- Inicio modal de validación de fecha vencimiento -->
    <div class="modal fade" id="modalValFechaV" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>La fecha debe no puede ser menor a la fecha de la factura.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnTipoF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin modal de validación de fecha vencimiento -->
    <!-- Inicio de modales para eliminar el registro de pptal.cnt y factura -->
    <div class="modal fade" id="modalEliminarFactura" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar la Factura seleccionada?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnEC" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlConstruir" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p id="mensaje_c"></p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnCons" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" onclick="reload()">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin de modales -->
    <!-- Scripts para recargar la pagina en los botones de los modales -->
    <script type="text/javascript">
        <?php
        if($tipo_co == 1){
            echo "\n$(\".privada\").show('fast');";
            echo "\n$(\".cambio\").removeClass('col-sm-2 col-md-2 col-lg-2')";
            echo "\n$(\".cambio\").addClass('col-sm-1 col-md-1 col-lg-1')";
            echo "\n$(\"#sltVendedor\").attr('required')";
        }else{
            echo "\n$(\"#sltVendedor\").removeAttr('required')";
            echo "\n$(\".privada\").hide('fast');";
        }
        ?>

        $("#fecha").change(function(){
            console.log('Entra');
            let form_data = {estruc: 27, fecha: $("#fecha").val(), dias:12};
            $.ajax({
                type: "POST",
                url: "jsonPptal/validarFechas.php",
                data: form_data,
                success: function (response)
                {
                    console.log('Devuelve'+response.trim());
                    $("#fechaV").val(response.trim());
                }
            })
        })

        $("#sltTipoFactura").change(function(){
            var tipo = $("#sltTipoFactura").val();
            if(tipo.length > 0){
                var form_data = {
                    tipo:$("#sltTipoFactura").val(),
                    action:1
                };
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_facturaJson.php",
                    data: form_data,
                    success: function (data, textStatus, jqXHR) {
                        console.log(data);
                        $("#txtNumeroF").val(data);
                    }
                });
            }else{
                $("#txtNumeroF").val("");
            }
        });
        $('#btnModifico').click(function(){
            document.location.reload();
        });

        $('#btnNoModifico').click(function(){
            document.location.reload();
        });

        $('#ver1').click(function(){
            document.location.reload();
        });

        $('#ver2').click(function(){
            document.location.reload();
        });

        $('#btnG').click(function(){
            document.location.reload();
        });

        $('#btnG2').click(function(){
            document.location.reload();
        });

        //Función para cargar modal de comprobante contable
        function cargarComprobante(idCnt){
            //Vector de envio con mi variable
            var form_data={
                idC:idCnt
            };
            $.ajax({
                type: 'POST',
                url: "modalConsultaComprobanteC.php",
                data: form_data,
                success: function (data, textStatus, jqXHR) {
                   
                    $("#modalComprobanteC").html(data);
                    $(".comprobantec").modal('show');
                }
            });
        }
        //Función para cargar modal del comprobante presupuestal
        function cargarPresupuestal(idPptal){
            //Vector de envio con mi variable
            var form_data={
                idP:idPptal
            };
            $.ajax({
                type: 'POST',
                url: "modalConsultaComprobanteP.php",
                data: form_data,
                success: function (data, textStatus, jqXHR) {
                    $("#modalComprobanteP").html(data);
                    $(".comprobantep").modal('show');
                }
            });
        }
        //Funcion solo nuemros
        function justNumbers(e){
            var keynum = window.event ? window.event.keyCode : e.which;
            if ((keynum == 8) || (keynum == 46) || (keynum == 45))
            return true;
            return /\d/.test(String.fromCharCode(keynum));
        }
        //Función informe
        function informe(){
            window.open('informes/inf_com_fac.php?factura=<?php if(!empty($factura)){echo $factura;}else{echo " ";} ?>');
        }
        //Función para validar fecha
        function validarFecha(){
            //Capturamos la variable del tipo factura
            var tipoF = parseInt($("#sltTipoFactura").val());
            //Validamos que no este vacia
            if(!isNaN(tipoF) || tipoF.length > 0){
                //Preparamos la variable de envio con los valores
                var form_data = {
                    x:2,
                    fecha:$("#fecha").val(),
                    tipo:tipoF,
                    id_factura:<?php echo $idFactura == ''? 0 : $idFactura; ?>
                };
                //Variable de conversion
                var result = '';
                //Envio de ajax
                $.ajax({
                    type:'POST',
                    url:'consultasBasicas/consultas_factura.php',
                    data:form_data,
                    success: function(data,textStatus,jqXHR){
                        //Capturamos el fata y lo convertimos a json
                        result = data;
                        //Validamos si el valor es true
                        if(result == true) {
                            $("#mensaje_fecha").html("<p>La fecha es mayor a la anterior factura</p>");
                            $("#mdlfecha").modal('show');   //Muestra modal
                            $("#fecha").val('');            //Campo fecha es vacia
                            $("#fechaV").val('');            //Campo fecha es vacia
                        }else if(result == 5){
                            $("#mensaje_fecha").html("<p>La fecha es menor a la ultima factura</p>");
                            $("#mdlfecha").modal('show');   //Muestra modal
                            $("#fecha").val('');            //Campo fecha es vacia
                            $("#fechaV").val('');            //Campo fecha es vacia
                        }
                    }
                });
            }
        }
        //Funcion para sumar 30 dias al cambio de fecha
        function change_date(){
            var fecha = $("#fecha").val();
            var fechaV = sumaFecha(30,fecha);
            $("#fechaV").val(fechaV);
        }
        //Función para validar que la fecha de vencimiento no sea menor a la de fecha
        function diferents_date(){
            var fecha1 = $("#fecha").val();         //Fecha
            var fecha2 = $("#fechaV").val();        //Fecha de vencimiento
            //Dividimos las fechas
            var inicial = fecha1.split("/");        //Fecha
            var final =  fecha2.split("/");         //Fecha de vencimiento
            //creamos variables de fecha y la formateamos para año-mes-dia
            var dateStart = new Date(inicial[2],inicial[1],inicial[0]); //Fecha
            var dateEnd = new Date(final[2],final[1],final[0]);         //Fecha de vencimiento
            //Validamos que la fecha de vencimiento no sea mayor que la del campo fecha
            if(dateEnd < dateStart){
                $("#mensaje_fecha").html("<p id=\"mensaje_fecha\">La fecha es menor</p>");
                $("#mdlfecha").modal('show');
                var fv = sumaFecha(30,fecha1);
                $("#fechaV").val(fv);
            }
        }

        $("#sltTipoFactura").change(function(){
            $("#fecha").val("");
            $("#fechaV").val("");
        });

        $("#sltTercero").change(function() {
            var form_data  = {tercero:$("#sltTercero").val()};
            $.ajax({
                type: 'POST',
                url: "consultasFacturacion/consultarFechav.php",
                data:form_data,
                success: function (data) {
                    if(data!==0){
                        var fechaV = data;
                        var fecha = sumaFecha(fechaV,$("#fechaV").val());
                        $("#fechaV").val(fecha);
                    }
                }
            });
        });
    </script>
    <!-- Inivocamos en la parte inferior el archivo que contendra el modal para evitar posibles errores -->
    <?php require_once './modalConsultaComprobanteC.php'; ?>
    <script type="text/javascript">
        //Función para ajustar la cabezera de la tabla
        $("#modalComprobanteC").on('shown.bs.modal',function(){
            try{
                var dataTable = $("#tablaDetalleC").DataTable();
                dataTable.columns.adjust().responsive.recalc();
            }catch(err){}
        });
    </script>
    <?php require_once './modalConsultaComprobanteP.php'; ?>
    <script src="dist/jquery.validate.js"></script>
    <script type="text/javascript">
        $("#modalComprobanteP").on('shown.bs.modal',function(){
            try{
                var dataTable = $("#tablaDetalleP").DataTable();
                dataTable.columns.adjust().responsive.recalc();
            }catch(err){}
        });

        $().ready(function() {
            var validator = $("#form-detalle").validate({
                ignore: "",
                rules:{
                    sltTipoPredio:"required",
                    txtCodigo:"required"
                },
                messages:{
                    sltTipoPredio: "Seleccione tipo de predio",
                },
                errorElement:"em",
                errorPlacement: function(error, element){
                    error.addClass('help-block');
                },
                highlight: function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                        $(elem).parents(".col-lg-2").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-md-2").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-sm-2").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-lg-1").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-md-1").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-sm-1").addClass("has-error").removeClass('has-success');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight:function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                    }else{
                        $(element).parents(".col-lg-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-lg-1").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-1").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-1").addClass('has-success').removeClass('has-error');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });

            var validator = $("#form").validate({
                ignore: "",
                rules:{
                    sltTipoPredio:"required",
                    txtCodigo:"required"
                },
                messages:{
                    sltTipoPredio: "Seleccione tipo de predio",
                },
                errorElement:"em",
                errorPlacement: function(error, element){
                    error.addClass('help-block');
                },
                highlight: function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                        $(element).parents(".col-lg-2").addClass("has-error").removeClass('has-success');
                        $(element).parents(".col-md-2").addClass("has-error").removeClass('has-success');
                        $(element).parents(".col-sm-2").addClass("has-error").removeClass('has-success');
                        $(element).parents(".col-lg-5").addClass("has-error").removeClass('has-success');
                        $(element).parents(".col-md-5").addClass("has-error").removeClass('has-success');
                        $(element).parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight:function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                    }else{
                        $(element).parents(".col-lg-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-lg-5").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-5").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-5").addClass('has-success').removeClass('has-error');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        });

        function nuevo() {
            window.location='registrar_GF_FACTURA.php';
        }

        function reconstruirComprobantes(id_factura, id_cnt, id_pptal){
            if(!isNaN(id_factura) && !isNaN(id_cnt) && !isNaN(id_pptal)){
                var form_data = {
                    id_factura: id_factura,
                    id_cnt:     id_cnt,
                    id_pptal:   id_pptal
                };

                var result = "";
                $.ajax({
                    type:"POST",
                    url:"access.php?controller=DetalleFactura&action=ReconstruirComprobantes",
                    data:form_data,
                    success: function(data){
                        if(data.length > 0){
                            $("#mensaje_c").html("<p id=\"mensaje_c\">Información Reconstruida Correctamente</p>");
                            $("#mdlConstruir").modal("show");
                        }else{
                            $("#mensaje_c").html("<p id=\"mensaje_c\">La información no se reconstruyo correctamente</p>");
                            $("#mdlConstruir").modal("show");
                        }
                    }
                });
            }
        }

        <?php if(!empty($idFactura)){ ?>
            $("#btnGuardar").attr('disabled',true);
            $("#btnImprimir, #btnRebuilt").attr('disabled',false);
            //$("#btnModificar").removeAttr('onclick');
        <?php }else{ ?>
            $("#btnGuardar").attr('disabled',false);
            $("#btnImprimir,#btnModificar,#btnEliminar, #btnRebuilt").attr('disabled',true);
            $("#btnImprimir,#btnModificar,#btnEliminar, #btnRebuilt").removeAttr('onclick');
        <?php } ?>

        function reload(){
            window.location.reload();
        }
    </script>
    
    <?php if(!empty($_GET['factura'])){ ?>
        <input type="hidden" id="tiporecaudo" name="tiporecaudo">
        <script>
            $(document).ready(function() {
               var form_data = {
                    action      :3,
                    id_factura  : $("#id").val(),
                };
                $.ajax({
                    type:"POST",
                    url:"jsonPptal/gf_facturaJson.php",
                    data:form_data,
                    success: function(data){
                        console.log(data);
                        if(data!=0){
                           $("#recaudo").css("display", "block");
                           $("#tiporecaudo").val(data);
                        }
                    }
                }); 
            });
        </script>
        <!--- ************ Recaudo ***************** --->
        <script>
            function modalRecaudo(){
                $("#mdlRecaudo").modal("show");
            }
        </script>
        <div class="modal fade" id="mdlRecaudo" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Banco</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <div class="form-group form-inline" style="margin-left:100px">
                            <!-- Inicio de campo de banco -->
                            <label for="sltBanco" class="control-label col-sm-2">
                                <strong class="obligado">*</strong>Banco:
                            </label>
                            <select name="sltBanco" id="sltBanco" class="select2_single col-sm-2 form-control input-sm" style="width:300px;cursor:pointer;height:30px" title="Seleccione banco" required>
                                <?php
                                    echo '<option value="">Banco</option>';
                                    $sql4 = "SELECT  ctb.id_unico,CONCAT(ctb.numerocuenta,' ',ctb.descripcion)
                                            FROM gf_cuenta_bancaria ctb
                                            LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria
                                            WHERE ctbt.tercero ='". $_SESSION['compania']."' ORDER BY ctb.numerocuenta";
                                    cargar_combos($sql4);
                                ?>
                            </select>
                            <br/>
                        </div>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="registrarRecaudo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Registrar</button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $("#registrarRecaudo").click(function(){
                if($("#sltBanco").val() !="") {
                    var form_data ={action:5, factura:$("#id").val() };
                    $.ajax({
                        type:"POST",
                        url:"jsonPptal/gf_facturaJson.php",
                        data:form_data,
                        success: function(data){
                            console.log(data);
                            resultado = JSON.parse(data);
                            var msj = resultado["msj"];
                            var rta = resultado["rta"];
                            if(rta==0){
                                var form_data={action:4, recaudo:$("#tiporecaudo").val(), banco:$("#sltBanco").val(),id_factura  : $("#id").val()};
                                $.ajax({
                                    type:"POST",
                                    url:"jsonPptal/gf_facturaJson.php",
                                    data:form_data,
                                    success: function(data){
                                        console.log(data);
                                        if(data ==0){
                                            $("#mensaje").html("Recudo Registrado Correctamente");
                                            $("#myModalError").modal("show");
                                            $("#btnErrorModal").click(function(){
                                                document.location.reload();
                                            })
                                        } else {
                                            $("#mensaje").html("Error Al Registrar Recaudo");
                                            $("#myModalError").modal("show");
                                            $("#btnErrorModal").click(function(){
                                                document.location.reload();
                                            })
                                        }
                                    }
                                });
                            } else {
                                $("#mensaje").html(msj);
                                $("#myModalError").modal("show");
                                $("#btnErrorModal").click(function(){
                                    $("#myModalError").modal("hide");
                                })
                            }
                        }
                    }); 
                }
                
            })
        </script>
        
        
        <script type="text/javascript">
            $("#sltBanco").select2({placeholder:"Banco",allowClear: true});
        </script>
    <?php }?>
    <!-- Cierre de html -->
</html>