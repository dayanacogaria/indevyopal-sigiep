<?php
##########################################################################################
#   ****************************    Modificaciones      ****************************    #
##########################################################################################
# 01/06/2018 |Erica G. |Buscar Configuración Concepto Tabla Verficar Código 
# 22/03/2018 |Erica G. |Busqueda por tipo de comprobante 
# 24/08/2017 |Alexander|Se agrego registro y validación cuando el concepto no tiene relacionado rubro fuente, de manera que se ocultara el campo de rubros y mostrara
# dos nuevos campos uno para seleccionar los rubros relacionados al concepto en concepto rubro y otro para seleccionar la fuente que se va a relacionar.
# También se incluyo metodó de suma para cuando se modificá el campo de iva, este sume el valor al valor tota y lo ajuste al valor final
# 24/05/2017 |Alexander|Se valido proceso para modifcar comprobante cnt y pptal (cabezas)
# 17/05/2017 |Alexander|Se cambio validaciòn para procesar el valor de la tarifa
# 25/04/2017 |Alexander|Se agrego botón y envio ajax para eliminado en cascada
# 23/02/2017 |Jhon N   |Se agrego Validación para cuando tarifa es valor 0, y al ser valor 0 se cambia de forma automatica el select por un input y se habilita
# el proceso de escritura para los campos, en lo cual el usuario tan solo debe ingresar el porcentaje, para que el sistema haga de manera automatizada,
# la generación de valores
##################################################################################################################################################################
#Referencias de cabezera y conexión
require_once './head.php';
require_once './Conexion/conexion.php';
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
if(!empty($_GET['factura'])){
    $factura        = $_GET['factura'];
    $valoresFactura = $fat->obtnerFactura($_GET['factura']);
    $idFactura      = $valoresFactura[0];
    $tipofactura    = $valoresFactura[1];
    $numeroFactura  = $valoresFactura[2];
    $tercero        = $valoresFactura[3];
    $centroCosto    = $valoresFactura[4];
    $fecha          = $valoresFactura[5];
    $fechaVencimiento = $valoresFactura[6];
    $descripcion      = $valoresFactura[7];
    $estado           = $valoresFactura[8];
    #Validamos si la variable cnt se encuentra en la url si existe entonces que consulte el id referente a comprobante cnt y que inicialize la variable $idCnt
    if(!empty($_GET['cnt'])){
        $idCnt = $cnt->obtner($_GET['cnt']);
    } else {
        $idCnt =0;
    }
    #Validamos si la variable pptal se encuentra en la url si existe entonces que consulte el id referente a comprobante pptal y que inicialize la variable $idPptal
    if(!empty($_GET['pptal'])){
        $idPptal = $ptl->obtner($_GET['pptal']);
    } else {
        $idPptal =0;
    }

    $estFat  = ucwords(mb_strtolower($fat->obtnerEstado($estado)));
}

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
<!-- Cierre de cabezera del formulario -->
</head>
<!-- Inicio o apertura del body -->
<body onload="return limpiarCampos()">
    <!-- Inicio de contenedor principal del formulario -->
    <div class="container-fluid text-center">
        <!-- Inicio de grib de boostrap -->
        <div class="row content">
            <!-- Llamado o invocación del menú -->
            <?php require_once 'menu.php'; ?>
            <!-- Inicio de la cabezera del formulario -->
            <div class="col-sm-8 col-md-8 col-lg-8 text-left">
                <!-- Titulo del formulario -->
                <h2 align="center" style="margin-top:-2px" class="tituloform">Factura</h2>
                <!-- Inicio de contenedor de la cabezera -->
                <div style="margin-top:-7px; border:4px solid #020324;border-radius: 10px;" class="client-form">
                    <!-- Inicio del formulario -->
                    <form id="form" name="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="access.php?controller=Factura&action=Registrar">
                        <!-- Inicio de parrafo de texto de campos obligatorios -->
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>
                        <!-- Inicio de formulario en linea y agrupamiento de campos -->
                        <div class="form-group">
                            <label for="sltTipoFactura" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Tipo Factura:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltTipoFactura" id="sltTipoFactura" class="form-control"  title="Seleccione el tipo de factura" required="">
                                    <?php
                                    if(empty($tipofactura)){
                                        echo "<option value=\"\">Tipo Factura</option>";
                                        $sqlT = "SELECT id_unico, nombre FROM gp_tipo_factura  ORDER BY nombre ASC";
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
                            <label for="txtNumeroF" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Nro:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="txtNumeroF" id="txtNumeroF" class="form-control" style="cursor:pointer;padding:2px;" title="Número de factura" placeholder="Nro de Factura" value="<?php echo $numeroFactura; ?>" required="" readonly/>
                            </div>
                            <label for="fecha" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Fecha:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input class="form-control" value="<?php echo $fecha ?>" type="text" name="fecha" id="fecha" onchange="validarFecha();change_date()" title="Ingrese la fecha" placeholder="Fecha" readonly required>
                            </div>
                            <div class="herencia col-sm-1 col-md-1 col-lg-1">
                                <a class="btn btn-primary sombreado"><i class="glyphicon glyphicon-tags"></i></a>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -15px">
                            <label class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Fecha Vencimiento:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input class="form-control" value="<?php echo $fechaVencimiento ?>" type="text" name="fechaV" id="fechaV" onchange="diferents_date()" title="Ingrese la fecha" placeholder="Fecha Vencimiento" readonly required>
                            </div>
                            <label class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Centro Costo:</label>
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
                            <label class="control-label col-sm-1 col-sm-1 col-lg-1"><strong class="obligado">*</strong>Tercero:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <select class="form-control select2" name="sltTercero" id="sltTercero" id="single" title="Seleccione un tercero para consultar" required>
                                    <?php
                                    if(!empty($tercero)){
                                        $sqltercero="SELECT DISTINCT
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
                                                        t.id_unico, 
                                                        IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                            t.numeroidentificacion, 
                                                            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                                    FROM gf_tercero t 
                                                    WHERE t.id_unico = $tercero
                                                    ";
                                        $ter = $mysqli->query($sqltercero);
                                        $per = mysqli_fetch_row($ter);
                                        echo '<option value="'.$per[1].'">'.ucwords(mb_strtolower($per[0].' - '.$per[2])).'</option>';
                                        $tersql="SELECT DISTINCT
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
                                                        t.id_unico, 
                                                        IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                            t.numeroidentificacion, 
                                                            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                                    FROM gf_tercero t 
                                                    WHERE t.id_unico != $tercero
                                                    ";
                                        $tercer = $mysqli->query($tersql);
                                        while($per1 = mysqli_fetch_row($tercer)){
                                            echo '<option value="'.$per1[1].'">'.ucwords(mb_strtolower($per1[0].' - '.$per1[2])).'</option>';
                                        }
                                    }else{
                                        echo "<option value=\"\">Tercero</option>";
                                        $ter2="SELECT DISTINCT
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
                                                        t.id_unico, 
                                                        IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                            t.numeroidentificacion, 
                                                            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                               FROM gf_tercero t 
                                                    ";
                                        $tercero2 = $mysqli->query($ter2);
                                        while($per2 = mysqli_fetch_row($tercero2)){
                                            echo '<option value="'.$per2[1].'">'.ucwords(mb_strtolower($per2[0].' - '.$per2[2])).'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -15px">
                            <label class="control-label col-sm-2 col-md-2 col-lg-2">Estado:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="txtEstado" id="txtEstado" class="form-control" value="<?php echo $estFat ?>" title="Estado" placeholder="Estado" readonly=""/>
                            </div>
                            <label class="control-label col-sm-1 col-md-1 col-lg-1" for="txtDescripcion">Descripción:</label>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <textarea class="form-control" style="margin-top:0px;" rows="2" name="txtDescripcion" id="txtDescripcion"  maxlength="500" placeholder="Descripción" onkeypress="return txtValida(event,'num_car')" ><?php echo $descripcion ?></textarea>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -15px">
                            <label class="privada control-label col-sm-2 col-md-2 col-lg-2" for="sltBuscar">Vendedor:</label>
                            <div class="privada col-sm-2 col-md-2 col-lg-2">
                                <select class="form-control select2" name="sltVendedor" id="sltVendedor" title="Seleccione un tercero para consultar" required>
                                    <?php
                                    if(!empty($vendedor)){
                                        $sqltercero="SELECT   IF(CONCAT_WS(' ',
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
                                                        t.id_unico, 
                                                        IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                            t.numeroidentificacion, 
                                                            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                                        FROM gf_tercero t
                                                    WHERE     t.id_unico = $tercero";
                                        $ter = $mysqli->query($sqltercero);
                                        $per = mysqli_fetch_row($ter);
                                        echo '<option value="'.$per[1].'">'.ucwords(mb_strtolower($per[0].' - '.$per[2])).'</option>';
                                        $tersql="SELECT DISTINCT
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
                                                        t.id_unico, 
                                                        IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                            t.numeroidentificacion, 
                                                            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                               FROM gf_tercero t
                                               WHERE     tr.id_unico != $tercero
                                               ORDER BY  t.numeroidentificacion ASC";
                                        $tercer = $mysqli->query($tersql);
                                        while($per1 = mysqli_fetch_row($tercer)){
                                            echo '<option value="'.$per1[1].'">'.ucwords(mb_strtolower($per1[0].' - '.$per1[2])).'</option>';
                                        }
                                    }else{
                                        echo "<option value=\"\">Vendedor</option>";
                                        $ter2="SELECT DISTINCT
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
                                                        t.id_unico, 
                                                        IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                            t.numeroidentificacion, 
                                                            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                               FROM gf_tercero t 
                                               ORDER BY  t.numeroidentificacion ASC";
                                        $tercero2 = $mysqli->query($ter2);
                                        while($per2 = mysqli_fetch_row($tercero2)){
                                            echo '<option value="'.$per2[1].'">'.ucwords(mb_strtolower($per2[0].' - '.$per2[2])).'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <label class="cambio control-label col-sm-2 col-md-2 col-lg-2" for="sltBuscar">Buscar Factura:</label>
                            <div class="form-group form-inline col-sm-6" style="margin-left:5px" >      
                                <div class="form-group form-inline col-sm-4" >                                  
                                    <select name="sltTipoBuscar" id="sltTipoBuscar" title="Tipo Comprobante" class="select2_single form-control" style="width: 130px;">
                                        <option value="">Tipo Factura</option> 
                                        <?php $sqlT = "SELECT id_unico, nombre FROM gp_tipo_factura  ORDER BY nombre ASC";
                                        $rest = $mysqli->query($sqlT);
                                        while($rowt = mysqli_fetch_row($rest)){
                                            echo "<option value=\"$rowt[0]\">".ucwords(mb_strtolower($rowt[1]))."</option>";
                                        } ?>
                                    </select>                               
                                </div>
                                <div class="form-group form-inline col-sm-4" style="margin-left:23px">                                  
                                    <select name="sltBuscar" id="sltBuscar" title="Buscar comprobante" class="select2_single form-control" style="width:250px; " onchange="consultarF()">
                                        <option value="">Buscar Comprobante</option>
                                    </select>
                                </div>
                            </div>
                            <script>
                                 $("#sltTipoBuscar").change(function(){
                                    var form_data ={
                                        estruc:26,
                                        tipo: $("#sltTipoBuscar").val(),
                                    }
                                    var option = '<option value="">Buscar Comprobante</option>';
                                    $.ajax({
                                        type:'POST',
                                        url:'jsonPptal/consultas.php',
                                        data:form_data,
                                        success: function(data){
                                            //console.log(data);
                                            var option = option+data;
                                           $("#sltBuscar").html(option);
                                        }
                                    });
                                })
                            </script>
                            <input type="hidden" name="id" id="id" value="<?php echo $idFactura; ?>" />
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <a id="btnNuevo" onclick="javascript:nuevo()" class="btn btn-primary sombreado btn-group" title="Ingresar nueva factura"><li class="glyphicon glyphicon-plus"></li></a>
                                <button type="submit" id="btnGuardar" class="btn btn-primary sombreado btn-group" title="Guardar factura"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                                <a class="btn btn-primary sombreado btn-group" id="btnImprimir" onclick="informe()" title="Imprimir"><li class="glyphicon glyphicon glyphicon-print"></li></a>
                                <a class="btn btn-primary sombreado btn-group" id="btnModificar" onclick="modificarPago(<?php echo $idCnt ?>,<?php echo $idPptal ?>)" title="Editar"><li class="glyphicon glyphicon glyphicon-edit"></li></a>
                                <a class="btn btn-primary sombreado btn-group" id="btnEliminar" onclick="eliminarDatos(<?php echo $idFactura ?>,<?php echo $idCnt ?>,<?php echo $idPptal ?>)" title="Eliminar"><li class="glyphicon glyphicon-remove"></li></a>
                                <a class="btn btn-primary sombreado btn-group" id="btnRebuilt" onclick="reconstruirComprobantes(<?php echo $idFactura ?>,<?php echo $idCnt ?>,<?php echo $idPptal ?>)" title="Reconstruir comprobantes cnt y pptal"><i class="glyphicon glyphicon-retweet"></i></a>
                            </div>
                        </div>
                        <!-- Cierre del formulario -->
                    </form>
                    <!-- Cierre de contenedor de la cabezera -->
                </div>
                <!-- Cierre de cabezera de formulario -->
            </div>
            <!-- Inicio de tabla para información adicional -->
            <div class="col-sm-2 col-xs-2" style="margin-top:-25px">
                <!-- Inicio de tabla para información adicional -->
                <table class="tablaC table-condensed text-center" align="center">
                    <!-- Inicio de cabezera -->
                    <thead>
                        <tr>
                            <th>
                                <!-- Titulo de la rejilla azul -->
                                <h2 class="titulo" align="center" style=" font-size:17px;">Información<br/>adicional</h2>
                            </th>
                        </tr>
                        <!-- Fin de cabezera -->
                    </thead>
                    <!-- Inicio de cuerpo de la tabla -->
                    <tbody>
                        <tr>
                            <td>
                                <!-- Si la variable cnt existe entonces el botón estara habilitado -->
                                <?php if(!empty($_GET['cnt'])){ ?>
                                    <a class="btn btn-primary btnInfo" href="#" onclick="return cargarComprobante(<?php echo $idCnt; ?>)">COMPROBANTE<br/>CONTABLE</a>
                                <?php
                                }else{ ?>
                                    <a class="btn btn-primary btnInfo disabled" href="#" readonly>COMPROBANTE<br/>CONTABLE</a>
                                <?php
                                } ?>
                                    
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                <!-- Si la variable pptal existe entonces el botón estara habilitado -->
                                <?php if(!empty($_GET['pptal'])){?>
                                    <a class="btn btn-primary btnInfo" href="#" onclick="return cargarPresupuestal(<?php echo $idPptal; ?>)">COMPROBANTE<br/>PRESUPUESTAL</a>
                                <?php
                                }else{ ?>
                                    <a class="btn btn-primary btnInfo disabled" href="#" readonly>COMPROBANTE<br/>PRESUPUESTAL</a>
                                <?php
                                } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><div id="recaudo" style="display:none"><a class="btn btn-primary btnInfo" onclick="modalRecaudo()">REGISTRAR<br/>RECAUDO</a></div></td>
                        </tr>
                        <?php if(!empty($_GET['factura'])){
                            $rc = "SELECT DISTINCT dp.pago FROM gp_detalle_pago dp 
                                LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
                                WHERE md5(df.factura)='".$_GET['factura']."'";
                            $rc = $mysqli->query($rc);
                            $pg = mysqli_fetch_row($rc);
                            if(mysqli_num_rows($rc)>0 || !empty($pg[0])){ 
                                $n = mysqli_num_rows($rc);
                                ?>
                            <tr>
                                <td>
                                    <input type="hidden" name="numR" id="numR" value="<?php echo $n?>">
                                    <a class="btn btn-primary btnInfo" onclick="abrirRecaudos(<?php echo $pg[0]?>)">VER<br/>RECAUDO</a>
                                </td>
                            </tr>
                            <script>
                                function abrirRecaudos(pg){
                                    if($("#numR").val()>1){
                                        $("#mdlRecaudos").modal('show');
                                    } else {
                                        cargarR(pg);
                                    }
                                }
                            </script>                                
                            <div class="modal fade" id="mdlRecaudos" role="dialog" align="center" >
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div id="forma-modal" class="modal-header">
                                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                        </div>
                                        <div class="modal-body" style="margin-top: 8px">
                                            <?php 
                                             $rc = "SELECT DISTINCT dp.pago, pg.numero_pago FROM gp_detalle_pago dp 
                                                LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
                                                LEFT JOIN gp_pago pg ON dp.pago = pg.id_unico
                                                WHERE md5(df.factura)='".$_GET['factura']."'";
                                            $rc = $mysqli->query($rc);
                                            while ($row1 = mysqli_fetch_row($rc)) { 
                                            echo '<button onclick="cargarR('.$row1[0].')" class="btn btn-primary btnInfo">'.$row1[1].'</button><br/>';
                                            }?>
                                            
                                        </div>
                                        <div id="forma-modal" class="modal-footer">
                                            <button type="button" id="btnCerrar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                function cargarR(pg){
                                    var form_data = {
                                        action:9,
                                        pago:pg
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
                            </script>
                        <?php } } ?>
                        <!-- Fin de cuerpo de la tabla -->
                    </tbody>
                    <!-- Fin de tabla para menú de información adicional -->
                </table>
                <!-- Fin de la tabla para información adicional -->
            </div>
            <!-- Cierre de grib de boostrap -->
            <!-- Inicio de Ingreso de datos de detalle -->
            <div class="col-sm-10 text-center" style="margin-left:-20px" align="">
                <!-- Inicio de Contenedor de formulario -->
                <div class="client-form" style="margin-left:60px" class="col-sm-12 col-md-12 col-lg-12">
                    <!-- Inicio de Formulario -->
                    <form name="form" id="form-detalle" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="access.php?controller=DetalleFactura&action=Registrar"  style="margin-top:-5px">
                        <!-- Inicio de campos ocultos con las ids  -->
                        <input type="hidden" name="txtIdFactura" id="txtIdFactura" class="hidden" value="<?php echo $idFactura; ?>"/>
                        <input type="hidden" name="txtIdCnt" id="txtIdCnt" class="hidden" value="<?php echo $idCnt; ?>"/>
                        <input type="hidden" name="txtIdPptal" id="txtIdPptal" class="hidden" value="<?php echo $idPptal; ?>"/>
                        <!-- Fin de campos ocultos que contendran las ids de la fatura y de los comprobantes -->
                        <!-- Inicio de campo de concepto -->
                        <div class="col-sm-1" style="margin-right:15px;margin-left:-30px">
                            <!-- Inicio de contenedor de agrupamiento -->
                            <div class="form-group"  align="left">
                                <!-- Inicio de label -->
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Concepto:
                                    <!-- Fin de label -->
                                </label>
                                <select name="sltConcepto" id="sltConcepto" class="form-control" style="width:100px;" title="Seleccione tercero" required="">
                                    <?php
                                    echo '<option value="">Concepto</option>';
                                    $sqlConcepto = "SELECT DISTINCTROW cnp.id_unico,cnp.nombre 
                                            FROM gp_concepto_tarifa cont 
                                            LEFT JOIN gp_concepto cnp ON cont.concepto = cnp.id_unico  
                                            WHERE cnp.id_unico IS NOT NULL ";
                                    cargar_combos($sqlConcepto);
                                    ?>
                                </select>
                                <!-- Fin de contenedor de agrupamiento -->
                            </div>
                            <!-- Cierre de campo de concepto -->
                        </div>
                        <!-- Inicio de campo rubro fuente -->
                        <div class="col-sm-1" id="Rbro" style="margin-right:1px;width: 68px">
                            <!-- Inicio de contenedor de agrupamiento -->
                            <div class="form-group">
                                <label class="control-label" style="margin-right: 9px">
                                    <strong class="obligado">*</strong>Rubro Fte:
                                </label>
                                <select name="sltRubroFuente" id="sltRubroFuente" class="form-control" title="Seleccione rubro" style="width:68px;padding:2px">
                                    <?php echo '<option value="">Rubro Fuente</option>'; ?>
                                </select>
                            </div>
                            <!-- Cierre campo rubro fuente -->
                        </div>
                        <!-- Campos ocultos para registrar los datos de los comprobantes-->
                        <input type="hidden" name="txtConceptoRubro" id="txtConceptoRubro" class="hidden" value="" />
                        <input type="hidden" name="txtFecha" id="txtFecha" class="hidden" value="<?php echo $fecha; ?>"/>
                        <input type="hidden" name="txtTercero" id="txtTercero" class="hidden" value="<?php echo $tercero; ?>"/>
                        <input type="text" name="txtCentroCosto" id="txtCentroCosto" class="hidden" value="<?php echo $centroCosto; ?>"/>
                        <input type="text" name="txtDescr" id="txtDescr" class="hidden" value="<?php echo $descripcion; ?>" />
                        <!-- Campo oculto para concepto tarifa -->
                        <input type="hidden" name="txtConcepto" value="">
                        <!-- Cierre de contenedor de agrupamiento -->
                        <div class="col-sm-1" id="rubros" style="margin-right:1px;width: 68px;display: none">
                            <div class="form-group">
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Rubros:
                                </label>
                                <select name="sltRubros" id="sltRubros" class="select2" style="width:68px;"><option value="">Rubros</option></select>
                            </div>
                        </div>
                        <div class="col-sm-1" id="fuentes" style="display: none">
                            <div class="form-group">
                                <label class="control-label"><strong class="obligado">*</strong>Fuentes:</label>
                                <select name="sltFuentes" id="sltFuentes" class="form-control" style="width:85px;">
                                    <?php
                                    $html = "";
                                    $html .= "<option value=\"\">Fuentes</option>";
                                    $sql_f = "SELECT id_unico, nombre FROM gf_fuente ORDER BY nombre ASC";
                                    $res_f = $mysqli->query($sql_f);
                                    while($row_f = mysqli_fetch_row($res_f)){
                                        $html .= "<option value=\"$row_f[0]\">".ucwords(mb_strtolower($row_f[1]))."</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- Script en el que se valida si las variables cnt y pptal existan que el contenedor de rubro fuente sea visible de lo contrario que se oculte -->
                        <script type="text/javascript">
                            //Script para cargar rubro fuente cuando seleccione un concepto
                            <?php if(!empty($_GET['cnt']) || !empty($_GET['pptal'])){ ?>
                                $("#Rbro").css('display','block');
                                $("#sltRubroFuente").prop("required", true);
                                $("#sltConcepto").change(function(){
                                    var form_data={
                                        action:19,
                                        concepto:$("#sltConcepto").val()
                                    };
                                    var datos='';
                                    $.ajax({
                                        type: 'POST',
                                        url: "jsonPptal/gf_facturaJson.php",
                                        data: form_data,
                                        success: function (data, textStatus, jqXHR) {
                                            if(data.length > 0){
                                                datos=data.split(';');
                                                $("#sltRubroFuente").html(datos[0]).fadeIn();
                                                $("#txtConceptoRubro").val(datos[1]);
                                                $("#Rbro").css("display","block");
                                                $("#rubros").css("display", "none");
                                                $("#fuentes").css("display", "none");
                                            }
                                        }
                                    });
                                });
                            <?php }else{ ?>
                                $("#Rbro").css('display','none');
                            <?php } ?>
                        </script>
                        <div class="col-sm-1" style="margin-right:11px;width: 40px;">
                            <div class="form-group" align="left">
                                <label class="control-label">
                                    <strong class="obligado"></strong>Cantidad:
                                </label>
                                <input type="text" name="txtCantidad" class="form-control" placeholder="Cantidad" onkeypress="return justNumbers(event);" id="txtCantidad" maxlength="50" style="padding:2px;width:50px;" required="" />
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:15px;">
                            <div class="form-group" align="left">
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Valor Unit.:
                                </label>
                                <!--<input type="text" name="txtValor" placeholder="Valor" onkeypress="return justNumbers(event);" id="txtValor" maxlength="50" style="height:26px;padding:2px;width:100px" required=""/>-->
                                <select class="form-control" name="sltValor" id="sltValor" title="Seleccione valor" style="width:100px;padding:2px" required>
                                    <option value="">Valor Unitario</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:15px;">
                            <div class="form-group" align="left">
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Iva:
                                </label>
                                <input type="text" name="txtIva" class="form-control" placeholder="Iva" onkeypress="return justNumbers(event);" value="" id="txtIva" maxlength="50" style="padding:2px;width:100px" required="" readonly=""/>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:15px;">
                            <div class="form-group" align="left">
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Impoconsumo:
                                </label>
                                <input type="text" name="txtImpoconsumo" class="form-control" placeholder="Impoconsumo" onkeypress="return justNumbers(event);" value="" id="txtImpoconsumo" maxlength="50" style="padding:2px;width:100px" required="" readonly=""/>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:15px;">
                            <div class="form-group" align="left">
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Ajuste Peso:
                                </label>
                                <input type="text" name="txtAjustePeso" class="form-control" placeholder="Ajuste al Peso" onkeypress="return justNumbers(event);" value="" id="txtAjustePeso" maxlength="50" style="padding:2px;width:100px" required="" readonly=""/>
                                <?php
                                $sqlAjuste = "SELECT valor FROM gs_parametros_basicos WHERE id_unico = 4";
                                $rsAjuste = $mysqli->query($sqlAjuste);
                                $ajuste = mysqli_fetch_row($rsAjuste);
                                ?>
                                <script type="text/javascript" >
                                    var Impo = 0.00;
                                    var iva = 0.00;
                                    var valor = 0;
                                    var totalIva = 0;
                                    var totalImpo = 0;
                                    var ajuste = <?php echo $ajuste[0]; ?>;
                                    $(document).ready(function () {
                                        $("#sltValor").attr('disabled',true);
                                        $("#txtAjuste").attr('disabled',true);
                                    });
                                    $("#sltConcepto").change(function() {
                                        var form_data = {
                                            concepto:$("#sltConcepto").val(),
                                            proceso:1
                                        };
                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasFacturacion/consultarValor.php",
                                            data:form_data,
                                            success: function (data) {
                                                if(data!=""){
                                                    $("#sltValor").attr('disabled',false);
                                                    $("#txtAjuste").attr('disabled',false);
                                                    $("#sltValor").html(data).fadeIn();
                                                }else{
                                                    $("#sltValor").attr('disabled',true);
                                                    $("#txtAjuste").attr('disabled',true);
                                                }
                                            }
                                        });
                                    });

                                    $("#sltValor").change(function(){
                                        try{
                                            //Validamos que el valor sea !=0
                                            var sltValor = $("#sltValor").val();
                                            var dato = sltValor.split("/");
                                            //Validamos que el valor sea !=0
                                            if(dato[0]!=='0'){
                                                var tarifa = dato[1];
                                                var form_data={
                                                    tarifa:tarifa,
                                                    proceso:2
                                                };
                                                $.ajax({
                                                    type: 'POST',
                                                    url: "consultasFacturacion/consultarValor.php",
                                                    data:form_data,
                                                    success: function (data) {
                                                        iva = data;
                                                        var cantidad = $("#txtCantidad").val();
                                                        if(cantidad==0 || cantidad==''){
                                                        cantidad = 1;
                                                        }else{
                                                            cantidad = $("#txtCantidad").val();
                                                        }
                                                        valor = dato[0];
                                                        total = cantidad*valor;
                                                        totalIva = (iva*total)/100;
                                                        console.log(totalIva+' '+valor+' '+total);
                                                        $("#txtIva").val(totalIva);
                                                        valT = valor + totalIva;
                                                        $("#txtValorA").val(valT);
                                                    }
                                                });

                                                $("#txtIva").prop("readonly", true);
                                                $("#txtImpoconsumo").prop("readonly",true);
                                                $("#txtAjustePeso").prop("readonly",true);
                                            }else{
                                                //Declaracion de variables
                                                var can = 0;
                                                var valor = 0;
                                                var iva = 0.00;
                                                var operI = 0;
                                                var imp = 0.00;
                                                var operM = 0;
                                                var sumaS = 0;
                                                var ajuste = 0;
                                                var ajusteTs = 0;
                                                var redondeo = 0;
                                                var valT = 0;
                                                //Habilitamos los campos
                                                $("#txtIva, #txtImpoconsumo, #txtAjustePeso").prop("readonly", false);
                                                //Ponemos valores vacios
                                                $("#txtIva, #txtAjustePeso, #txtValorA").val('0');
                                                $("#txtImpoconsumo").val('0');
                                                //Cambiamos el select por un textbox
                                                $("#sltValor").replaceWith('<input type="text" id="txtValor" name="txtValor" class="form-control" style="width:100px;padding:2px" placeholder="Valor" title="Ingrese el valor" onkeypress="return justNumbers(event)"/>');
                                                //Foco al campo de valor
                                                $("#txtValor").focus();
                                                //Cambio de titulo
                                                $("#txtIva").prop("title",'Ingrese el porcentaje de iva');
                                                $("#txtImpoconsumo").prop("title",'Ingrese el porcentaje de impuesto al consumo');
                                                $("#txtAjustePeso").prop("title","Ajuste al peso");
                                                //Cambio de puntero
                                                $("#txtIva, #txtImpoconsumo, #txtAjustePeso, #txtValorA").css('cursor','pointer');
                                                //Validación para campo de cantidad
                                                if($("#txtCantidad").val()==''){
                                                    can = 1;
                                                }else{
                                                    can = ($("#txtCantidad").val());
                                                }
                                                //Validación para el campo valor
                                                $("#txtValor").blur(function(){
                                                    //Valor
                                                    if($("#txtValor").val()!==0){
                                                        //Operacion de valor
                                                        valor = ($("#txtValor").val()*can);
                                                        //Asiganción de valores
                                                        $("#txtValorA").val(valor);
                                                        //Validación de campos
                                                        if(isNaN($("#txtImpoconsumo").val())){
                                                            $("#txtImpoconsumo").val("0");
                                                        }
                                                        if(isNaN($("#txtAjustePeso").val())){
                                                            $("#txtAjustePeso").val('0');
                                                        }
                                                    }
                                                });
                                                //Validación para el campo de iva
                                                $("#txtIva").blur(function(){
                                                    //iva
                                                    var iv = $("#txtIva").val();
                                                    if( iv !==0){
                                                        //Captura de valores
                                                        valor = ($("#txtValor").val()*can);
                                                        iva = ($("#txtIva").val());
                                                        //Operaciones
                                                        operI = (valor * iva) /100;
                                                        valT = valor + operI;
                                                        //Asignación de valores
                                                        $("#txtIva").val(operI);
                                                        $("#txtValorA").val(valT);
                                                    }
                                                });
                                                //Validación para campo de impoconsumo
                                                $("#txtImpoconsumo").blur(function(){
                                                    //Impoconsumo
                                                    var im = $("#txtImpoconsumo").val();
                                                    if( im !==0){
                                                        //Captura de valores
                                                        valor = ($("#txtValor").val()*can);
                                                        imp = $("#txtImpoconsumo").val();
                                                        //Operaciones
                                                        operM = (valor * imp)/100;
                                                        valT = valor + operI + operM;
                                                        //Asignación de valores
                                                        $("#txtImpoconsumo").val(operM);
                                                        $("#txtValorA").val(valT);
                                                    }
                                                });
                                                //Validación de ajuste al peso
                                                $("#txtAjustePeso").blur(function(){
                                                    //Ajuste al peso
                                                    var aj =  $("#txtAjustePeso").val();
                                                    if(aj!=='0'){
                                                        //Captura de valores
                                                        ajuste = $("#txtAjustePeso").val();
                                                        valor = ($("#txtValor").val()*can);
                                                        imp = $("#txtImpoconsumo").val();
                                                        iva = ($("#txtIva").val());
                                                        //Operaciones
                                                        sumaS = valor + operI + operM;
                                                        redondeo = redondeaAlAlza(sumaS,ajuste);
                                                        ajusteTs = redondeoTotal(sumaS,ajuste);
                                                        //Asignación de valores
                                                        $("#txtAjustePeso").val(redondeo);
                                                        $("#txtValorA").val(ajusteTs);
                                                    }
                                                });
                                            }
                                        }catch($e){

                                        }
                                    });

                                    $("#sltValor").change(function(){
                                        try{
                                            var sltValor = $("#sltValor").val();
                                            var dato = sltValor.split("/");
                                            if(dato[0]!=='0'){
                                                var tarifa = dato[1];
                                                var form_data={
                                                    tarifa:tarifa,
                                                    proceso:3
                                                };


                                                $.ajax({
                                                    type: 'POST',
                                                    url: "consultasFacturacion/consultarValor.php",
                                                    data:form_data,
                                                    success: function (data) {
                                                        Impo = data;
                                                        var valor = dato[0];
                                                        var cantidad = $("#txtCantidad").val();
                                                        if(cantidad==0 || cantidad==''){
                                                        cantidad = 1;
                                                        }else{
                                                            cantidad = $("#txtCantidad").val();
                                                        }

                                                        var oper = (valor * cantidad);
                                                        var totalImpo = (Impo*oper)/100;
                                                        $("#txtImpoconsumo").val(totalImpo);
                                                        var suma = oper + totalIva + totalImpo;
                                                        var redondo = redondeaAlAlza(suma,ajuste) ;
                                                        var ajusteT = redondeoTotal(suma,ajuste);
                                                        $("#txtAjustePeso").val(redondo);
                                                        $("#txtValorA").val(ajusteT);
                                                    }
                                                });
                                            }
                                        }catch(err){}
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:10px;">
                            <div class="form-group" align="left">
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Valor Total:
                                </label>
                                <input type="text" name="txtValorA" class="form-control" placeholder="Valor Total" onkeypress="return justNumbers(event);" id="txtValorA" maxlength="50" style="padding:2px;width:95px" required="" readonly=""/>
                            </div>
                        </div>
                        <!-- Inicio de botón de guardado de detalle -->
                        <div class="col-sm-1" align="left" style="margin-top:31px;margin-left:-60px;margin-right:30px; ">
                            <button type="submit" id="btnGuardarDetalle" class="btn btn-primary sombra"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                            <input type="hidden" name="MM_insert" >
                            <!-- Script para bloquear el botón de guardado de detalle -->
                            <script type="text/javascript">
                                $(document).ready(function(){
                                    <?php if(empty($idFactura)){ ?>
                                        $("#btnGuardarDetalle").prop('disabled',true);
                                    <?php
                                    }else{ ?>
                                        $("#btnGuardarDetalle").prop('disabled',false);
                                    <?php }  ?>
                                });
                            </script>
                            <!-- Fin de botón de guardado de detalle -->
                        </div>
                        <!-- Cierre de formulario -->
                    </form>
                    <!-- Cierre de contenedor de formulario -->
                </div>
                <!-- Fin de ingreso de datos de detalle -->
            </div>
            <!-- Inicio de forma de tabla -->
            <div class="col-sm-8 col-md-8 col-lg8" style="margin-top:-25px">
                <!-- Campos ocultos en los que guardamos la id anterior y la nueva id -->
                <input type="hidden" id="idPrevio" value="">
                <input type="hidden" id="idActual" value="">
                <div class="table-responsive contTabla" >
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <td class="oculto">Identificador</td>
                                <td width="7%" class="cabeza"></td>
                                <td class="cabeza"><strong>Concepto</strong></td>
                                <td class="cabeza"><strong>Cantidad</strong></td>
                                <td class="cabeza"><strong>Valor</strong></td>
                                <td class="cabeza"><strong>Iva</strong></td>
                                <td class="cabeza"><strong>Impoconsumo</strong></td>
                                <td class="cabeza"><strong>Ajuste del peso</strong></td>
                                <td class="cabeza"><strong>Valor Total Ajustado</strong></td>
                            </tr>
                            <tr>
                                <th class="oculto">Identificador</th>
                                <th width="7%" class="cabeza"></th>
                                <th class="cabeza">Concepto</th>
                                <th class="cabeza">Cantidad</th>
                                <th class="cabeza">Valor</th>
                                <th class="cabeza">Iva</th>
                                <th class="cabeza">Impoconsumo</th>
                                <th class="cabeza">Ajuste del peso</th>
                                <th class="cabeza">Valor Total Ajustado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sumaCantidad   = 0;
                                $sumaValor      = 0;
                                $sumaIva        = 0;
                                $sumaImpo       = 0;
                                $sumaAjuste     = 0;
                                $sumaValortotal = 0;
                                if(!empty($_GET['factura'])){
                                    $result = $det->obtnerListados($idFactura);
                                    while($row=mysqli_fetch_row($result)){ ?>
                                    <tr>
                                        <td class="oculto"></td>
                                        <td class="campos" onloadstart="return inhabilitar(<?php echo $row[0] ?>)">
                                            <a class="delete" href="#<?php echo $row[0];?>" onclick="javascript:eliminar(<?php echo $row[0]; ?>)" id="btnDel<?php echo $row[0]; ?>" title="Eliminar">
                                                <li class="glyphicon glyphicon-trash"></li>
                                            </a>
                                            <a class="mod" href="#<?php echo $row[0];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>);javascript:cargarValor(<?php echo $row[0]; ?>);javascript:cambioValor(<?php echo $row[0]; ?>);javascript:calcularValores(<?php echo $row[0]; ?>);javascript:calcularValoresEscrito(<?php echo $row[0]; ?>)">
                                                <li class="glyphicon glyphicon-edit"></li>
                                            </a>
                                        </td>
                                        <td class="campos">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="concepto'.$row[0].'">'.ucwords(strtolower($row[2])).'</label>'; ?>
                                            <select class="col-sm-12 campoD form-control" name="sltconcepto<?php echo $row[0] ?>" id="sltconcepto<?php echo $row[0] ?>" title="Seleccione concepto" style="display:none;">
                                                <option value="<?php echo $row[1]; ?>"><?php echo $row[2]; ?></option>
                                                    <?php
                                                    $sqlCn = "SELECT cnp.id_unico,cnp.nombre FROM gf_concepto con
                                                    LEFT JOIN gp_concepto cnp ON cnp.concepto_financiero = con.id_unico
                                                    WHERE cnp.id_unico != $row[1]
                                                    ORDER BY cnp.nombre DESC";
                                                    $resc = $mysqli->query($sqlCn);
                                                    while($row2 = mysqli_fetch_row($resc)){
                                                        echo '<option value="'.$row2[0].'">'.$row2[1].'</option>';
                                                    }
                                                    ?>
                                            </select>
                                        </td>
                                        <td class="campos text-right">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblCantidad'.$row[0].'">'.$row[3].'</label>';
                                                  echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;" class="col-sm-12 campoD text-left form-control"  type="text" name="txtcantidad'.$row[0].'" id="txtcantidad'.$row[0].'" value="'.$row[3].'" />';
                                                  $sumaCantidad += $row[3];
                                            ?>
                                        </td>
                                        <td class="campos text-right">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblValor'.$row[0].'">'.number_format($row[4], 2, '.', ',').'</label>';
                                            //echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="text" name="txtValor'.$row[0].'" id="txtValor'.$row[0].'" value="'.$row[4].'" />';
                                            $sumaValor += $row[4];
                                            ?>
                                            <select class="col-sm-12 campoD form-control" name="txtValor<?php echo $row[0] ?>" id="txtValor<?php echo $row[0] ?>" title="Seleccione valor" style="display:none;">
                                            </select>
                                        </td>
                                        <td class="campos text-right">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblIva'.$row[0].'">'.number_format($row[5], 2, '.', ',').'</label>';
                                                  echo '<input maxlength="50" onkeypress="return justNumbers(event);" onkeyup="return sum_v('.$row[3].','.$row[4].',$(this).val(),'.$row[0].')" style="display:none;" class="col-sm-12 campoD text-left form-control" type="text" name="txtIva'.$row[0].'" id="txtIva'.$row[0].'" value="'.$row[5].'" />';
                                                  $sumaIva += $row[5];
                                            ?>
                                        </td>
                                        <td class="campos text-right">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblImpoconsumo'.$row[0].'">'.number_format($row[6], 2, '.', ',').'</label>';
                                                  echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;" class="col-sm-12 campoD text-left form-control" type="text" name="txtImpoconsumo'.$row[0].'" id="txtImpoconsumo'.$row[0].'" value="'.$row[6].'" />';
                                                  $sumaImpo += $row[6];
                                            ?>
                                        </td>
                                        <td class="campos text-right">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblAjustepeso'.$row[0].'">'.number_format($row[7], 2, '.', ',').'</label>';
                                                  echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;" class="col-sm-12 campoD text-left form-control" type="text" name="txtAjustepeso'.$row[0].'" id="txtAjustepeso'.$row[0].'" value="'.$row[7].'" />';
                                                  $sumaAjuste += $row[7];
                                            ?>
                                        </td>
                                        <td class="campos text-right">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblValorAjuste'.$row[0].'">'.number_format($row[9], 2, '.', ',').'</label>';
                                                  echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;width:100.5px" class="col-sm-9 campoD text-left form-control"  type="text" name="txtValorAjuste'.$row[0].'" id="txtValorAjuste'.$row[0].'" value="'.$row[9].'" readonly ="true"/>';
                                                  $sumaValortotal += $row[9];
                                            ?>
                                            <div >
                                                <table id="tab<?php echo $row[0] ?>" style="padding:0px;background-color:transparent;background:transparent;" class="col-sm-1">
                                                    <tbody>
                                                        <tr style="background-color:transparent;">
                                                            <td style="background-color:transparent;">
                                                                <a  href="#<?php echo $row[0];?>" title="Guardar" id="guardar<?php echo $row[0]; ?>" style="display: none;" onclick="javascript:guardarCambios(<?php echo $row[0]; ?>)">
                                                                    <li class="glyphicon glyphicon-floppy-disk"></li>
                                                                </a>
                                                            </td>
                                                            <td style="background-color:transparent;">
                                                                <a href="#<?php echo $row[0];?>" title="Cancelar" id="cancelar<?php echo $row[0] ?>" style="display: none" onclick="javascript:cancelar(<?php echo $row[0];?>)" >
                                                                    <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <!-- Fin de forma de tabla -->
            </div>
            <!-- Inicio de totales -->
            <div class="col-sm-8 col-md-8 col-lg-8 col-sm-offset-1 col-md-offset-1 col-lg-offset-1" style="margin-top:5px;">
                <div class="col-sm-1" style="margin-right:30px">
                    <div class="form-group" style="" align="left">
                        <label class="control-label">
                            <strong>Totales:</strong>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1" style="margin-right:20px">
                    <label class="control-label valorLabel" title="Total cantidad"><?php echo number_format($sumaCantidad, 2, '.', ','); ?></label>
                </div>
                <div class="col-sm-1" style="margin-right:30px">
                    <label class="control-label valorLabel" title="Total valor"><?php echo number_format($sumaValor, 2, '.', ','); ?></label>
                </div>
                <div class="col-sm-1" style="margin-right:50px">
                    <label class="control-label valorLabel" title="Total iva"><?php echo number_format($sumaIva, 2, '.', ','); ?></label>
                </div>
                <div class="col-sm-1" style="margin-right:30px">
                    <label class="control-label valorLabel" title="Total impuesto al consumo"><?php echo number_format($sumaImpo, 2, '.', ','); ?></label>
                </div>
                <div class="col-sm-1" style="margin-right:30px">
                    <label class="control-label valorLabel" title="Total ajsute al peso"><?php echo number_format($sumaAjuste, 2, '.', ','); ?></label>
                </div>
                <div class="col-sm-1" style="margin-right:30px">
                    <label class="control-label valorLabel" title="Total valor ajustado"><?php echo number_format($sumaValortotal, 2, '.', ','); ?></label>
                </div>
                <!-- Fin de totales -->
            </div>
        </div>
        <!-- Inicio de scripts -->
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
            /*
             *
             * @param {int} id
             */

            /*
             *
             * @param {type} id
             * @returns {undefined}
             *$("#txtValor"+id).change(function(){
             *      var valor = $("#txtValor"+id).val();
             *      var totali = valor * iva;
             *      var rta = totali.toFixed(2);
             *      $("#txtIva"+id).val(rta);
             *
             *      var totalImpuest = valor*impuesto;
             *      var rtai = totalImpuest.toFixed(2);
             *      $("#txtImpoconsumo"+id).val(rtai);
             *
             *      var cantidad = $("#txtcantidad"+id).val();
             *      if(cantidad==0 || cantidad==''){
             *          cantidad = 1;
             *      }else{
             *          cantidad = $("#txtcantidad"+id).val();
             *      }
             *      var ivaC = parseFloat($("#txtIva"+id).val());
             *       var impoC = *parseFloat($("#txtImpoconsumo"+id).val());
             *      var oper = (valor * cantidad);
             *      var suma = oper + ivaC + impoC;
             *      var ajusteP = redondeaAlAlza(suma,ajuste);
             *      var valorTotal = redondeoTotal(suma,ajuste);
             *      $("#txtAjustepeso"+id).val(ajusteP);
             *      $("#txtValorAjuste"+id).val(valorTotal);
             *  });
             */
            function calcularValores(id) {
                var ajuste = <?php echo $ajuste[0]; ?>;
                var Impo = 0.00;
                var iva = 0.00;
                var valor = 0;
                var totalIva = 0;
                var totalImpo = 0;

                $("#txtValor"+id).change(function(){
                    //Validación para el campo de valor no tome valores cero
                    if($("#txtValor"+id).val() !== '0'){

                        var form_data={
                            concepto:$("#sltconcepto"+id).val(),
                            proceso:2
                        };

                        $.ajax({
                            type: 'POST',
                            url: "consultasFacturacion/consultarValor.php",
                            data:form_data,
                            success: function (data) {
                                var cantidad = $("#txtcantidad"+id).val();
                                if(cantidad==0 || cantidad==''){
                                    cantidad = 1;
                                }else{
                                    cantidad = $("#txtcantidad"+id).val();
                                }

                                iva = data;
                                valor = $("#txtValor"+id).val() * cantidad;
                                totalIva = (iva*valor)/100;
                                if (isNaN(totalIva)) {
                                   totalIva = 0;
                                }
                                $("#txtIva"+id).val(totalIva);
                            }
                        });

                    }else{
                        var can = 0;
                        //Validación para campo cantidad
                        if(isNaN($("#txtcantidad"+id).val())){
                            can = 1;
                        }else{
                            can = ($("#txtcantidad"+id).val());
                          }
                        //Vaciamos los campos
                        $("#txtIva"+id).val('0');
                        $("#txtImpoconsumo"+id).val('0');
                        $("#txtAjustepeso"+id).val('0');
                        $("#txtValorAjuste"+id).val('0');
                        //Cambio de campo
                        $("#txtValor"+id).replaceWith('<input type="text" id="txtValor'+id+'" name="txtValor'+id+'" class="form-control campoD" placeholder="Valor" title="Ingrese el valor" onkeypress="return justNumbers(event)"/>');
                        $("#txtValor"+id).focus();
                        //Función de cambio por campo valor
                        $("#txtValor"+id).blur(function(){
                            //Validación de valor de campo valor
                            if($("#txtValor"+id).val() !==0 ){
                                //Operaciones de valor
                                var valor = $("#txtValor"+id).val()*can;
                                if (isNaN(valor)) {
                                   valor = 0;
                                }
                                //Asiganción de valores para el campo de total
                                $("#txtValorAjuste"+id).val(valor);
                            }
                        });
                        //Función de cambio para campo iva
                        $("#txtIva"+id).blur(function(){
                            //Validación de campo iva
                            if($("#txtIva"+id).val() !== 0){
                                //Captura de valores
                                var valor = $("#txtValor"+id).val()*can;
                                var iva = $("#txtIva"+id).val();
                                //Operación de iva
                                var totalI = (valor * iva) /100;
                                //Asiganción de valor de iva
                                if (isNaN(totalI)) {
                                   totalI = 0;
                                }
                                $("#txtIva"+id).val(totalI);
                                //Asiganción de valores para el campo de total
                                $("#txtValorAjuste"+id).val(valor+totalI);
                            }
                        });
                        //Función de campo para campo impoconsumo
                        $("#txtImpoconsumo"+id).blur(function(){
                            //Validación de campo impoconsumo
                            if($("#txtImpoconsumo"+id).val() !== 0){
                                //Captura de valores
                                var valor = $("#txtValor"+id).val()*can;
                                var impo = $("#txtImpoconsumo"+id).val();
                                var iva = $("#txtIva"+id).val();
                                //Opereación de impoconsumo
                                var totalM = (valor*impo) /100;
                                console.log('dd'+totalM);
                                if (isNaN(totalM)) {
                                   totalM = 0;
                                }
                                var t = parseFloat(valor) + parseFloat(iva) + parseFloat(totalM);
                                //ASiganción de valor de impo
                                
                                $("#txtImpoconsumo"+id).val(totalM);
                                //Asiganción de valores para el campo de total
                                $("#txtValorAjuste"+id).val(t);
                            }
                        });
                        //Función para ajuste al peso
                        $("#txtAjustepeso"+id).blur(function(){
                            //Valiación para ajuste al peso
                            if($("#txtAjustepeso"+id).val() !== 0){
                                //Captura de valores
                                var valor = $("#txtValor"+id).val()*can;
                                var impo = $("#txtImpoconsumo"+id).val();
                                var iva = $("#txtIva"+id).val();
                                var ajuste = $("#txtAjustepeso"+id).val();
                                //operaciones
                                var suma = parseFloat(valor) + parseFloat(impo) + parseFloat(iva);
                                var redondeo = redondeaAlAlza(suma,ajuste);
                                var aj = redondeoTotal(suma,ajuste);
                                //Asiganción de valores
                                if (isNaN(redondeo)) {
                                   redondeo = 0;
                                }
                                $("#txtAjustepeso"+id).val(redondeo);
                                if (isNaN(aj)) {
                                   aj = 0;
                                }
                                $("#txtValorAjuste"+id).val(aj);

                            }
                        });
                    }
                });

                $("#txtValor"+id).change(function(){
                    //Validación para el campo de valor no tome valores cero
                    if($("#txtValor"+id).val() !== '0'){

                        var form_data={
                            concepto:$("#sltconcepto"+id).val(),
                            proceso:3
                        };

                        $.ajax({
                            type: 'POST',
                            url: "consultasFacturacion/consultarValor.php",
                            data:form_data,
                            success: function (data) {
                                Impo = data;
                                valor = $("#txtValor"+id).val();

                                var cantidad = $("#txtcantidad"+id).val();
                                if(cantidad==0 || cantidad==''){
                                    cantidad = 1;
                                }else{
                                    cantidad = $("#txtcantidad"+id).val();
                                }
                                var oper = (valor * cantidad);
                                totalImpo = (Impo*oper)/100;

                                var suma = parseFloat(oper) + parseFloat(totalIva) + parseFloat(totalImpo);
                                var redondo = redondeaAlAlza(suma,ajuste) ;
                                var ajusteT = redondeoTotal(suma,ajuste);
                                if (isNaN(totalImpo)) {
                                   totalImpo = 0;
                                }
                                if (isNaN(redondo)) {
                                   redondo = 0;
                                }
                                if (isNaN(ajusteT)) {
                                   ajusteT = 0;
                                }
                                $("#txtImpoconsumo"+id).val(totalImpo);
                                $("#txtAjustepeso"+id).val(redondo);
                                $("#txtValorAjuste"+id).val(ajusteT);
                            }
                        });
                    }

                });
            }

            function calcularValoresEscrito(id) {
                var ajuste = <?php echo $ajuste[0]; ?>;
                var Impo = 0.00;
                var iva = 0.00;
                var valor = 0;
                var totalIva = 0;
                var totalImpo = 0;
                $("#txtcantidad"+id).keyup(function(){
                    var form_data={
                        concepto:$("#sltconcepto"+id).val(),
                        proceso:2
                    };

                    $.ajax({
                        type: 'POST',
                        url: "consultasFacturacion/consultarValor.php",
                        data:form_data,
                        success: function (data) {
                            iva = data;
                            valor = $("#txtValor"+id).val();
                            totalIva = (iva*valor)/100;
                            if (isNaN(totalIva)){ 
                                totalIva = 0;
                            }
                            $("#txtIva"+id).val(totalIva);
                        }
                    });

                    var form_data={
                        concepto:$("#sltconcepto"+id).val(),
                        proceso:3
                    };

                    $.ajax({
                        type: 'POST',
                        url: "consultasFacturacion/consultarValor.php",
                        data:form_data,
                        success: function (data) {
                            Impo = data;
                            valor = $("#txtValor"+id).val();
                            totalImpo = (Impo*valor)/100;
                             if (isNaN(totalImpo)){ 
                                 totalImpo =0;
                             }
                            $("#txtImpoconsumo"+id).val(totalImpo);

                            var cantidad = $("#txtcantidad"+id).val();
                            if(cantidad==0 || cantidad==''){
                                cantidad = 1;
                            }else{
                                cantidad = $("#txtcantidad"+id).val();
                            }

                            var oper = (valor * cantidad);
                            var suma = oper + totalIva + totalImpo;
                            var redondo = redondeaAlAlza(suma,ajuste);
                            var ajusteT = redondeoTotal(suma,ajuste);
                            $("#txtAjustepeso"+id).val(redondo);
                            $("#txtValorAjuste"+id).val(ajusteT);
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
                        console.log(data);
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
        <!-- Fin de scripts -->
        <!-- Cierre de contenedor principal del formulario -->
    </div>
    <!-- Cierre del body -->
</body>
    <!-- Llamado o invocación al pie de pagina -->
    <?php require_once 'footer.php' ?>
    <!-- Script para selects que tendran la clase select2 -->
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript">
        $(".select2").select2();
        $("#sltTercero").select2({placeholder:"Tercero",allowClear: true});
        //$("#sltCentroCosto").select2({placeholder:"Centro Costo"});
        $("#sltConcepto").select2({placeholder:"Concepto",allowClear: true});
        $("#sltBuscar").select2({placeholder:"Buscar Factura",allowClear: true});
        $("#sltTipoFactura").select2({placeholder:"Tipo Factura",allowClear: true});
        $("#sltCentroCosto").select2({placeholder:"Centro Costo",allowClear: true});
        $("#sltTipoBuscar").select2({placeholder:"Tipo Factura",allowClear: true});
        $("#sltRubros").select2({placeholder:"Rubros",allowClear: true});
        $("#sltFuentes").select2({placeholder:"Rubros",allowClear: true});
        $("#sltBanco").select2({placeholder:"Banco",allowClear: true});
    </script>
    <!-- link para tema y para javascript de la libreria de bootstrap -->
    <script src="js/bootstrap.min.js"></script>
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

        function mostrar_boton(tipo){
            if(typeof tipo != 'undefined' || tipo !== null || obj !== ''){
                var form_data = {
                    id_tipo:tipo
                };

                $.ajax({
                    url:"access.php?controller=Factura&action=obtenerClaseFactura",
                    data:form_data,
                    type:"POST",
                    success:function(data){
                        var datos = parseInt(data);
                        switch(datos){
                            case 1:
                                $(".herencia").fadeOut("fast");
                                break;
                            case 2:
                                $(".herencia").fadeIn("slow");
                                break;
                            case 3:
                                $(".herencia").fadeIn("slow");
                                break;
                            case 4:
                                $(".herencia").fadeIn("slow");
                                break;
                            default:
                                $(".herencia").fadeOut("fast");
                                break;
                        }
                    }
                });
            }
        }

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
                mostrar_boton($("#sltTipoFactura").val());
            }else{
                $("#txtNumeroF").val("");
                $(".herencia").fadeOut("fast");
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
                    url:"access.php?controller=detallefactura&action=reconstruirComprobantes",
                    data:form_data,
                    success: function(data){
                        console.log('Recons '+data);
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
                        console.log(data+'Recaudo ');
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
                jsShowWindowLoad('Registrando Recaudo...')
                if($("#sltBanco").val() !="") {
                    var form_data={action:4, recaudo:$("#tiporecaudo").val(), banco:$("#sltBanco").val(),id_factura  : $("#id").val()};
                    $.ajax({
                        type:"POST",
                        url:"jsonPptal/gf_facturaJson.php",
                        data:form_data,
                        success: function(data){
                            jsRemoveWindowLoad();
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
                }
                
            })
        </script>
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
        <script type="text/javascript">
            $("#sltBanco").select2({placeholder:"Banco",allowClear: true});
        </script>
        
    <?php }?>
        
    <!--********  Validar Si Ya Tiene Recaudo *******-->
    <?php if(!empty($_GET['factura'])){
        $rc = "SELECT DISTINCT dp.pago FROM gp_detalle_pago dp 
            LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
            WHERE md5(df.factura)='".$_GET['factura']."'";
        $rc = $mysqli->query($rc);
        if(mysqli_num_rows($rc)>0){ ?>
    <script>
        $(document).ready(function(){
            $("#btnModificar,#btnEliminar, #btnRebuilt,#btnGuardarDetalle").attr('disabled',true);
            $(".delete").css('display','none');
            $(".mod").css('display','none');
        })  
        
    </script>
    <?php } } ?>
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
</html>