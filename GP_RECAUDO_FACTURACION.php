<?php
##########################################################################################
#   ****************************    Modificaciones      ****************************    #
##########################################################################################
#14/02/2019 |Erica G. |Creado
##########################################################################################
#Referencias de cabezera y conexión
require_once './head.php';
require_once './Conexion/conexion.php';
require_once './funciones/funciones_consulta.php';
$anno = $_SESSION['anno'];

?>
<!-- Link o llamados de archivos -->
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
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
    });
</script>
<!-- Script para diseño de tabla con la libreria Datatable -->
<script type="text/javascript">
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
                case 10:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 11:
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
<!-- Script para validar ingreso de valores númericos en los campos de texto en los que se invoque esta función -->
<script type="text/javascript">
    function justNumbers(e){
        var keynum = window.event ? window.event.keyCode : e.which;
        if ((keynum == 8) || (keynum == 46) || (keynum == 45))
        return true;
        return /\d/.test(String.fromCharCode(keynum));
    }
</script>
<!-- Titulo del formulario -->
<title>Recaudo de Facturación</title>
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
    /*Modificación a la libreria select2*/
    .select2-choice {min-height:30px; max-height:30px;}
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
    /*Estilos de cabeza y campos de la tabla
    */
    .cabeza{
        white-space:nowrap;
        padding: 20px;
    }
    .campos{
        padding:-20px;
    }
</style>
<!-- Cierre de cabezera del formulario -->
</head>
<!-- Inicio o apertura del body -->
<body >
    <!-- Inicio de contenedor principal del formulario -->
    <div class="container-fluid text-center">
        <!-- Inicio de grib de boostrap -->
        <div class="row content">
            <!-- Llamado o invocación del menú -->
            <?php require_once 'menu.php'; ?>
            <!-- Inicio de la cabezera del formulario -->
            <div class="col-sm-8 col-xs-12 text-left">
                <!-- Titulo del formulario -->
                <h2 align="center" style="margin-top:-2px" class="tituloform">Recaudo de Facturación</h2>
                <!-- Cierre de la cabeza del formulario -->
                <!-- Inicio de contenedor de la cabezera -->
                <div style="margin-top:-7px; border:4px solid #020324;border-radius: 10px;margin-left: 4px;margin-right: 4px;" class="client-form">
                    <!-- Inicio del formulario -->
                    <form name="form" class="form-horizontal" method="POST" style="margin-left:30px"  enctype="multipart/form-data" action="jsonServicios/registrarPagoFacturaJson.php">
                        <!-- Variables de consulta -->
                        <?php
                        #Inicializamos las variables
                        $idPago=0;
                        $fecha="";
                        $tipoPago="";
                        $nroPago="";
                        $banco="";
                        $cupones="";
                        $valor="";
                        $estado="";
                        $tercero="";
                        $idCnt="";
                        $idPptal="";
                        $recaudo = "";
                        #Validamos la existencia de la variable recuado si existe realizara la consulta y cargara las variables
                        if(!empty($_GET['recaudo'])){
                            $recaudo = $_GET['recaudo'];
                            $sqlRecuado="select id_unico,
                                                tipo_pago,
                                                fecha_pago,
                                                numero_pago,
                                                banco,
                                                estado,
                                                responsable
                                        from gp_pago
                                        where md5(id_unico)='$recaudo'";
                            $resultRecaudo = $mysqli->query($sqlRecuado);
                            $valoresRecaudo= mysqli_fetch_row($resultRecaudo);
                            #Cargamos las variables
                            $idPago=$valoresRecaudo[0];
                            $tipoPago=$valoresRecaudo[1];;
                            $fecha=$valoresRecaudo[2];
                            $nroPago=$valoresRecaudo[3];
                            $banco=$valoresRecaudo[4];
                            $estado=$valoresRecaudo[5];
                            $tercero=$valoresRecaudo[6];
                            #Consulta para el campo de estado
                            $sqlEstado = "SELECT id_unico,nombre FROM gp_estado_pago_factura WHERE id_unico = $estado";
                            $resultEstado = $mysqli->query($sqlEstado);
                            $valorEstado = mysqli_fetch_row($resultEstado);
                            #Validamos si existe la variable cupones entonces cargamos la variable cupones
                            if(!empty($_GET['cupones'])){
                                $cupones=$_GET['cupones'];
                            }
                            #Validamos si existe la variable valor entonces cargamos la variable valor
                            if(!empty($_GET['valor'])){
                                $valor=$_GET['valor'];
                            }
                            #Validamos que la variable cnt no este vacia y consultamos el id del comprobante cnt
                            if(!empty($_GET['cnt'])){
                                $cnt=$_GET['cnt'];
                                $sql6="select id_unico from gf_comprobante_cnt where md5(id_unico)='$cnt'";
                                $result6=$mysqli->query($sql6);
                                $c= mysqli_fetch_row($result6);
                                $idCnt=$c[0];
                                $_SESSION['cntcxp'] =$idCnt;
                            } else {
                                $idCnt=0;
                                $_SESSION['cntcxp'] =$idCnt;
                            }
                            #Validamos que la variable pptal no este vacia y consultamos el id del comprobante pptal
                            if(!empty($_GET['pptal'])){
                                $pptal=$_GET['pptal'];
                                $sql7="select id_unico from gf_comprobante_pptal where md5(id_unico)='$pptal'";
                                $result7=$mysqli->query($sql7);
                                $p= mysqli_fetch_row($result7);
                                $idPptal=$p[0];
                            } else {
                                $idPptal=0;
                            }
                        }
                        ?>
                        <!-- Inicio de parrafo de texto de campos obligatorios -->
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            <!-- Parrafo de campos obligatorios -->
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                            <!-- Cierre de parrafo de texto de campos obligatorios -->
                        </p>
                        <!-- Inicio de formulario en linea y agrupamiento de campos -->
                        <div class="form-inline form-group" style="margin-top:5px">
                            <!-- Inicio de campo de fecha -->
                            <label for="fecha" class="control-label col-sm-2">
                                <strong class="obligado">*</strong>Fecha:
                            </label>
                            <input class="col-sm-2 input-sm form-control" value="<?php if(!empty($fecha)){$fechaS = explode("-",$fecha); echo $fechaS[2].'/'.$fechaS[1].'/'.$fechaS[0];}else{echo date('d/m/Y');} ?>" type="text" name="fecha" id="fecha" class="form-control" style="width:100px;height:30px" title="Ingrese la fecha" placeholder="Fecha" required>
                            <!-- Cierre de campo de fecha -->
                            <!-- Inicio de campo tipo pago -->
                            <label for="sltTipoPago" class="control-label col-sm-2">
                                <strong class="obligado">*</strong>Tipo Recaudo:
                            </label>
                            <select name="sltTipoPago" id="sltTipoPago" class="form-control col-sm-2 input-sm" style="width:100px;cursor:pointer;height:30px" title="Seleccione el tipo de recaudo" required="required">
                                <?php
                                if(!empty($tipoPago)){
                                    $sqlTP = "SELECT id_unico,nombre FROM gp_tipo_pago WHERE id_unico = $tipoPago";
                                    cargar_combos($sqlTP);
                                    $sqltipopago = "SELECT id_unico,nombre FROM gp_tipo_pago WHERE id_unico != $tipoPago";
                                    cargar_combos($sqltipopago);
                                }else{
                                    echo '<option value="">Tipo Recaudo</option>';
                                    $sql1="select id_unico,nombre from gp_tipo_pago";
                                    cargar_combos($sql1);
                                } 
                                ?>
                                <!-- Script para generar numeros nuevos -->
                                <script type="text/javascript">
                                    $("#sltTipoPago").change(function(){
                                        var form_data={
                                            tipo:$("#sltTipoPago").val(),
                                            action :7,
                                        };
                                        $.ajax({
                                            type: 'POST',
                                            url: "jsonPptal/gf_facturaJson.php",
                                            data: form_data,
                                            success: function (data, textStatus, jqXHR) { 
                                                $("#txtNumeroR").val(data);
                                            }
                                        });
                                    });
                                </script>
                            </select>
                            <!-- Cierre de campo de tipo pago -->
                            <!-- Inicio de campo número de pago -->
                            <label for="txtNumeroR" class="control-label col-sm-2">
                                <strong class="obligado">*</strong>Nro Recaudo:
                            </label>
                            <input type="text" name="txtNumeroR" id="txtNumeroR" class="form-control col-sm-2 input-group" style="width:100px;cursor:pointer;padding:2px;height:30px" title="Número de factura" placeholder="Nro Recaudo" value="<?php if(!empty($nroPago)){echo $nroPago;}else{} ?>" required="required" readonly/>
                            <!-- Fin de campo de número de pago -->
                        </div>
                        <div class="form-group form-inline" style="margin-top:-15px">
                            <!-- Inicio de campo de banco -->
                            <label for="sltBanco" class="control-label col-sm-2">
                                <strong class="obligado">*</strong>Banco:
                            </label>
                            <select name="sltBanco" id="sltBanco" class="col-sm-2 form-control input-sm" style="width:100px;cursor:pointer;height:30px" title="Seleccione banco" required>
                                <?php
                                if(!empty($banco)){
                                    $sql2 = "SELECT  ctb.id_unico,CONCAT_WS(' ',ctb.numerocuenta,ctb.descripcion)
                                            FROM gf_cuenta_bancaria ctb
                                            LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria
                                            WHERE ctbt.tercero ='". $_SESSION['compania']."' AND ctb.id_unico=$banco AND ctb.parametrizacionanno = $anno ORDER BY ctb.numerocuenta";
                                    cargar_combos($sql2);
                                    $sql3 = "SELECT  ctb.id_unico,CONCAT_WS(' ',ctb.numerocuenta,ctb.descripcion)
                                            FROM gf_cuenta_bancaria ctb
                                            LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria
                                            WHERE ctbt.tercero ='". $_SESSION['compania']."' AND ctb.id_unico!=$banco AND ctb.parametrizacionanno = $anno ORDER BY ctb.numerocuenta";
                                    cargar_combos($sql3);
                                }else{
                                    echo '<option value="">Banco</option>';
                                    $sql4 = "SELECT  ctb.id_unico,CONCAT_WS(' ',ctb.numerocuenta,ctb.descripcion)
                                            FROM gf_cuenta_bancaria ctb
                                            LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria
                                            WHERE ctbt.tercero ='". $_SESSION['compania']."' AND ctb.parametrizacionanno = $anno ORDER BY ctb.numerocuenta";
                                    cargar_combos($sql4);
                                }
                                ?>
                            </select>
                            <!-- Fin de campo de banco -->
                            <!-- Campo de cupones,campo que captura una variable diamica (Posible control de cantidad de columnas a insertar)-->
                            <label class="control-label col-sm-2">
                                Nro Cupones:
                            </label>
                            <input type="text" name="txtCupones" id="txtCupones" class="form-control col-sm-2 input-sm" placeholder="Nro Cupones" title="Ingrese el número de cupones" style="width:100px;cursor:pointer;padding:2px;height:30px" value="<?php if(!empty($cupones)){echo $cupones;}else{} ?>"/>
                            <!-- Fin de campo de cupones -->
                            <!-- Inicio de campo de valor, este captura una variable dinamica(Posible control de $valor) -->
                            <label class="control-label col-sm-2">
                                Valor:
                            </label>
                            <input type="text" name="txtValor" id="txtValor" class="form-control col-sm-2 input-sm" placeholder="Valor" title="Ingrese el valor" style="width:100px;cursor:pointer;padding:2px;height:30px" value="<?php if(!empty($valor)){echo $valor;}else{} ?>"/>
                            <!-- Fin de campo de valor -->
                        </div>
                        <div class="form-group form-inline" style="margin-top:-15px">
                            <!-- Inicio de campo de estado -->
                            <label class="control-label col-sm-2">
                                Estado:
                            </label>
                            <?php
                            #Consulta para obtener el nombre del estado basico y mostralo en el campo de estado
                            $sqlE = "SELECT id_unico,nombre FROM gp_estado_pago_factura WHERE id_unico = 1";
                            $resultE = $mysqli->query($sqlE);
                            $estd = mysqli_fetch_row($resultE);
                            ?>
                            <input type="text" name="txtEstado" id="txtEstado" class="form-control col-sm-2 input-sm" placeholder="Estado" title="Estado del Recaudo" style="width:100px;cursor:pointer;padding:2px;height:30px" value="<?php if(!empty($estado)){echo $valorEstado[1];}else{echo $estd[1];} ?>" readonly/>
                            <!-- Fin e campo de estado -->
                            <!-- Inicio de campo de tercero -->
                            <label class="control-label col-sm-2"><strong class="obligado">*</strong>
                                Responsable:
                            </label>
                            <select name="sltTercero" id="sltTercero" class="form-control col-sm-2 input-sm" title="Seleccione un responsable" style="width:342px;cursor:pointer;height:30px" required="required">
                                <?php
                                if(!empty($tercero)){
                                    $sql18 = "SELECT  IF(CONCAT_WS(' ',
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
                                               CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
                                               p.codigo_catastral 
                                               FROM gp_unidad_vivienda_medidor_servicio uvms 
                                               LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                                               LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico
                                               LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
                                               LEFT JOIN gp_medidor m ON m.id_unico = uvms.medidor
                                               LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
                                            WHERE t.id_unico=$tercero";
                                    $rs18 = $mysqli->query($sql18);
                                    $row18 = mysqli_fetch_row($rs18);
                                    echo '<option value="'.$row18[1].'">'.$row18[3].' - '.ucwords(mb_strtolower($row18[0].' '.$row18[2])).'</option>';
                                    $sql19 = "SELECT  IF(CONCAT_WS(' ',
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
                                               CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
                                               p.codigo_catastral 
                                               FROM gp_unidad_vivienda_medidor_servicio uvms 
                                               LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                                               LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico
                                               LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
                                               LEFT JOIN gp_medidor m ON m.id_unico = uvms.medidor
                                               LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
                                               WHERE m.estado_medidor != 3";
                                    $rs19 = $mysqli->query($sql19);
                                    while($row19 = mysqli_fetch_row($rs19)){
                                        echo '<option value="'.$row19[1].'">'.$row19[3].' - '.ucwords(mb_strtolower($row19[0].' '.$row19[2])).'</option>';
                                    }
                                }else{
                                    echo '<option value="">Tercero</option>';
                                    $sql1 = "SELECT  IF(CONCAT_WS(' ',
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
                                               CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
                                               p.codigo_catastral 
                                               FROM gp_unidad_vivienda_medidor_servicio uvms 
                                               LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                                               LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico
                                               LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
                                               LEFT JOIN gp_medidor m ON m.id_unico = uvms.medidor
                                               LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
                                               WHERE m.estado_medidor != 3";
                                    $rs1 = $mysqli->query($sql1);
                                    while($row1 = mysqli_fetch_row($rs1)){
                                        echo '<option value="'.$row1[1].'">'.$row1[3].' - '.ucwords(mb_strtolower($row1[0].' '.$row1[2])).'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <!-- Fin de campo de tercero -->
                            <!-- Cierre de formulario en linea y agrupamiento de campos -->
                        </div>
                        <div class="form-group form-inline" style="margin-top:-15px;margin-bottom:5px;">
                            <label for="sltBuscar" class="control-label col-sm-2">Buscar Recaudo:</label>
                            <select name="sltBuscarT" id="sltBuscarT" onchange="buscarRecaudos()" class="form-control col-sm-1" style="width: 100px" title="Buscar recaudo">
                                <?php
                                $tp = "SELECT id_unico, nombre FROM gp_tipo_pago";
                                $resultB = $mysqli->query($tp);
                                echo "<option value>Tipo Recaudo</option>";
                                while ($rowB = mysqli_fetch_row($resultB)) {
                                    echo '<option value="'.$rowB[0].'">'.$rowB[1].'</option>';
                                }
                                 ?>
                            </select>
                            <select name="sltBuscar" id="sltBuscar" onchange="buscarPago()" class="form-control col-sm-1" style="width: 250px" title="Buscar recaudo">
                                <?php
                                echo "<option value=''>Recaudo</option>";
                                
                                 ?>
                            </select>
                            <div class="col-sm-4">
                                <a id="btnNuevo" onclick="javascript:nuevo()" class="btn  btn-primary" title="Ingresar nuevo recaudo" style="box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;"><li class="glyphicon glyphicon-plus"></li></a>
                                <button type="submit" id="btnGuardar" style="box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;" class="btn btn-primary" title="Guardar recaudo"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                <a class="btn btn-primary" id="btnImprimir" onclick="imprimir(<?php echo $idPago.",".$idCnt.",".$idPptal; ?>)"  style="box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;" title="Imprimir"><li class="glyphicon glyphicon glyphicon-print"></li></a>
                                <a class="btn btn-primary" id="btnEditar" onclick="modificarPago(<?php echo $idPago.",".$idCnt.",".$idPptal; ?>)" style="box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;" title="Editar"><li class="glyphicon glyphicon glyphicon-edit"></li></a>
                                <a class="btn btn-primary" id="btnEliminar" onclick="eliminar_datos(<?php echo $idPago.",".$idCnt.",".$idPptal; ?>)" title="Eliminar" style="box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;"><li class="glyphicon glyphicon-remove" ></li></a>
                                <a class="btn btn-primary" id="btnRecCon" title="Reconstruir contabilidad" onclick="reconstruirContablidad(<?php echo $idCnt.",".$idPptal.",".$idPago ?>)" style="box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;"><i class="glyphicon glyphicon-refresh"></i></a>
                                <!-- Script para los botones  -->
                                <script type="text/javascript">
                                    /*Recarga la pagina*/
                                    function nuevo(){
                                        window.location='GP_RECAUDO_FACTURACION.php';
                                    }
                                    <?php if(!empty($_GET['recaudo'])){ ?>
                                        $("#btnGuardar").attr('disabled',true);
                                        $("#btnImprimir,#btnEditar,#btnEliminar").attr('disabled',false);
                                    <?php }else{ ?>
                                        $("#btnGuardar").attr('disabled',false);
                                        $("#btnImprimir,#btnEditar,#btnEliminar").attr('disabled',true);
                                    <?php } ?>
                                </script>
                                <script>
                                    function imprimir(pago, cnt, pptal){
                                       window.open('informes/inf_recaudo.php?id='+pago+'&c='+cnt+'&p='+pptal);
                                    }
                                </script>
                            </div>
                        </div>
                        <!-- Cierre del formulario -->
                    </form>
                    <!-- Cierre de contenedor de la cabezera -->
                </div>
                <!-- Cierre de contenedor de cabezera-->
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
                            <td>
                                <a class="btn btn-primary btnInfo "  id="btnRet" onclick="retenciones()">REGISTRAR <br/>RETENCIONES</a>
                            </td>
                        </tr>
                        <?php if(!empty($_GET['cnt'])) {
                            $re = "SELECT * FROM gf_retencion WHERE comprobante = $idCnt";
                            $re = $mysqli->query($re);
                            if(mysqli_num_rows($re)>0) { ?>
                        <tr>
                            <td>
                                <a class="btn btn-primary btnInfo "  id="btnVerRet" onclick="verretenciones()">VER <br/>RETENCIONES</a>
                            </td>
                        </tr>
                        <?php } } ?>
                        <!-- Fin de cuerpo de la tabla -->
                    </tbody>
                    <!-- Fin de tabla para menú de información adicional -->
                </table>
                <!-- Fin de la tabla para información adicional -->
            </div>
            <!-- Inicio de contenedor de formulario de detalle -->
            <div class="col-sm-8 text-center" style="margin-top:5px">
                <!-- Inicio de contenedor de formulario -->
                <div class="client-form col-sm-12 col-sm-offset-1">
                    <!-- Inicio de formulario -->
                    <form name="formD" id="formD" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javaScript:guardarDetalles()" style="margin-top:-15px">
                        <!-- Campos ocultos con las id de los comprobantes-->
                        <input type="hidden" name="txtComprobanteCnt" id="txtComprobanteCnt" class="hidden" value="<?php echo $idCnt; ?>"/>
                        <input type="hidden" name="txtComprobantePptal" id="txtComprobantePptal" class="hidden" value="<?php echo $idPptal; ?>"/>
                        <!-- Campo oculto con la fecha -->
                        <input type="hidden" name="txtFecha" id="txtFecha" class="hidden" value=" <?php echo $fecha; ?>"/>
                        <!-- Campo oculto con el tercero -->
                        <input type="hidden" name="txtTercero" id="txtTercero" class="hidden" value="<?php echo $tercero ?>"/>
                        <!-- Campo oculto con la id del recaudo -->
                        <input type="text" name="txtIdRecaudo" id="txtIdRecaudo" class="hidden" value="<?php echo $idPago; ?>"/>
                        <input type="text" name="txtBanco" id="txtBanco" class="hidden" value="<?php echo $banco; ?>"/>
                        <!-- Campo oculto con el valor del recaudo para vaidación -->
                        <input type="hidden" name="txtValoConcepto" id="txtValorConcepto" class="hidden" val=""/>
                        <!-- Inicio de contenedor de campo de factura -->
                        <div class="col-sm-1">
                            <!-- Inicio de campo de factura -->
                            <div class="form-group" style="margin-top:5px" align="left">
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Factura:
                                </label>
                                <select name="sltFactura2" id="sltFactura2" class="form-control input-sm" style="width:170px;height:30px;" title="Seleccione  factura" required="" onclick="load_value_input('',$('#sltFactura2').val())">
                                    <?php
                                    #Inclusión nueva validamos que si existe un tercero que de ese tercero me traiga las facturas relacionadas que tengan saldo
                                        if(!empty($tercero)){
                                            echo '<option value="">Factura</option>';
                                            echo $sqlF = "SELECT DISTINCT    fat.id_unico,fat.numero_factura,tpf.prefijo,
                                                        DATE_FORMAT(fat.fecha_factura,'%d/%m/%Y'), p.codigo_catastral 
                                                    FROM gp_factura fat
                                                    LEFT JOIN           gp_detalle_factura dtf  ON dtf.factura      = fat.id_unico
                                                    LEFT JOIN           gp_detalle_pago dtp     ON dtf.id_unico     = dtp.detalle_factura
                                                    LEFT JOIN           gp_tipo_factura tpf     ON fat.tipofactura  = tpf.id_unico
                                                    LEFT JOIN           gp_unidad_vivienda_medidor_servicio uvms ON fat.unidad_vivienda_servicio = uvms.id_unico 
                                                    LEFT JOIN           gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                                                    LEFT JOIN           gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
                                                    LEFT JOIN           gp_predio1 p ON uv.predio = p.id_unico 
                                                    WHERE               fat.tercero = $tercero AND (SELECT SUM(dtf.valor_total_ajustado) FROM gp_detalle_factura dft WHERE dtf.factura = fat.id_unico)>0 AND fat.fecha_factura <= '$fecha'"
                                                    . "";
                                            $resultF = $mysqli->query($sqlF);
                                            $val = array();
                                            $v =0;
                                            while($fila = mysqli_fetch_row($resultF)){
                                                list($factura, $fat, $pag, $xxx) = array($fila[0], 0, 0, 0);
                                                $sql_f = "SELECT dtf.valor_total_ajustado, dtf.id_unico AS ULTIMO FROM gp_detalle_factura dtf WHERE dtf.factura = $fila[0]";
                                                $res_f = $mysqli->query($sql_f);
                                                while($row_f = mysqli_fetch_row($res_f)){
                                                    $fat += $row_f[0];
                                                    $sql_p = "  SELECT      DISTINCT ((dtp.valor) + dtp.iva + dtp.impoconsumo) + dtp.ajuste_peso AS ULTIMO
                                                                FROM        gp_detalle_pago dtp
                                                                LEFT JOIN   gp_detalle_factura dtf ON dtp.detalle_factura = dtf.id_unico
                                                                WHERE       dtf.id_unico = $row_f[1]";
                                                    $res_p = $mysqli->query($sql_p);
                                                    while($row5= mysqli_fetch_row($res_p)){
                                                        $pag += $row5[0];
                                                    }
                                                }
                                                $xxx = $fat - $pag;

                                                if($xxx != 0){
                                                    $val[$fila[0]]=$xxx;
                                                    echo '<option value="'.$fila[0].'">Cod:'.$fila[4].' - '.ucwords(mb_strtoupper($fila[2].' '.$fila[1].' Saldo: '.number_format($xxx,2, ',', '.').' '.$fila[3])).'</option>';
                                                }
                                            }
                                        }else{
//                                            #Consulta que trae las facturas pero distingue los valores de acuerdo asus afectaciones
                                            echo '<option value="">Factura</option>';
                                        }
                                        ?>
                                </select>
                                <!-- Cierre de campo de factura -->
                            </div>
                            <!-- Cierre de contendedor de campo de factura -->
                        </div>
                        <div class="col-sm-1 col-sm-offset-2">
                            <!-- Inicio de campo de factura -->
                            <div class="facturas form-group" style="margin-top:5px">
                                <label class="control-label" style="margin-left:-20px">
                                    Concepto:
                                </label>
                                <select name="sltConcepto" id="sltConcepto" class="form-control input-sm text-left" style="width:170px;height:30px;" title="Seleccione Concepto" onclick="load_value_input(this.value,$('#sltFactura2').val())">
                                <?php echo "<option value=\"\">Concepto</option>"; ?>
                                </select>
                                <!-- Cierre de campo de factura -->
                            </div>
                            <!-- Fin de contenedor de campo valor -->
                        </div>
                        <!-- Inicio de contenedor de campo valor -->
                        <div class="col-sm-1 col-sm-offset-2">
                            <!-- Inicio de campo de factura -->
                            <div class="facturas form-group" style="margin-top:5px">
                                <label class="control-label" style="margin-left:-50px">
                                    <strong class="obligado">*</strong>Valor:
                                </label>
                                <input type="text" name="txtValor" placeholder="Valor" onkeypress="return justNumbers(event);" onkeyup="validate_value(this.value)" id="txtValor2" maxlength="50" style="height:30px;padding:2px;width:150px" required=""/>
                                <!-- Script para cargar el valor de la factura seleccionada -->
                                <script type="text/javascript" >
                                    $("#sltFactura2").change(function(){
                                        var factura = $("#sltFactura2").val();
                                        if(factura=='""' || factura==0){
                                            $("#txtValor2").val('');
                                        }else {
                                            var nam = 'valorP'+factura;
                                            var valor = $("#"+nam).val();
                                            $("#txtValor2").val(valor);
                                        }
                                    });

                                    $("#sltFactura2").change(function(){
                                        var factura = $("#sltFactura2").val();
                                        if(factura!=='""' || factura!==0){
                                            var form_data = {
                                                existente:57,
                                                factura:factura
                                            };

                                            $.ajax({
                                                type: 'POST',
                                                url:"consultasBasicas/consultarNumeros.php",
                                                data:form_data,
                                                success: function (data) {
                                                    $("#sltConcepto").html(data).fadeIn();
                                                    $("#sltConcepto").css('display','none');
                                                    console.log(data);
                                                }
                                            });
                                        }
                                    });
                                </script>
                                <!-- Script para consultar por medio de la selección de un tercero las facturas relacionadas a ese tercero -->
                                <script type="text/javascript" >
                                    $("#sltTercero").change(function(){
                                        var form_data = {
                                            tercero:$("#sltTercero").val(),
                                            funcion:1
                                        };

                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasDetallePago/consultarFacturaTercero.php",
                                            data:form_data,
                                            success: function (data) {
                                                $("#sltFactura2").html(data).fadeIn();
                                                $("#sltFactura2").css('display','none');
                                            }
                                        });
                                    });
                                </script>
                                <!-- Cierre de campo de factura -->
                            </div>
                            <!-- Fin de contenedor de campo valor -->
                        </div>
                        <!-- Inicio de contenedor del botón de guardado del detalle -->
                        <div class="col-sm-1" align="left" style="margin-top:30px;margin-left:10px">
                            <!-- Inicio del botón de guardado -->
                            <div class="col-sm-1">
                                <button type="submit" id="btnDetalle" class="btn btn-primary sombra "><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                <!-- Script para inhabilitar el botón si existe alguna factura insetada -->
                                <script type="text/javascript">
                                    var idPago = <?php echo $idPago; ?>;
                                    if(idPago==0 || idPago=='""' ||  idPago.length === 0){
                                        $("#btnDetalle").attr('disabled',true);
                                    }else{
                                        $("#btnDetalle").attr('disabled',false);
                                    }
                                </script>
                            </div>
                            <!-- Cierre de contenedor del botón de guardado del detalle -->
                        </div>
                        <!-- Cierre de formulario -->
                    </form>
                    <!-- Cierre de contenedor de formulario -->
                </div>
                <script>
                 function guardarDetalles(){
                    var formData = new FormData($("#formD")[0]);  
                    jsShowWindowLoad('Guardando Detalles...');
                    var form_data = { action:1 };
                    $.ajax({
                        type: 'POST',
                        url: "jsonPptal/gf_facturaJson.php?action=24",
                        data:formData,
                        contentType: false,
                        processData: false,
                        success: function(response)
                        { 
                            jsRemoveWindowLoad();
                            console.log(response);
                            var rta = response;
                            if(rta == 0){
                                $("#mensaje").html('No Se Ha Podido Guardar Información');
                                $("#modalMensajes").modal("show");
                                $("#Aceptar").click(function(){
                                    $("#modalMensajes").modal("hide");
                                })
                            } else {
                                $("#mensaje").html('Información Guardada Correctamente');
                                $("#modalMensajes").modal("show");
                                $("#Aceptar").click(function(){
                                    window.location.reload();
                                })
                                
                            }

                        }
                    });
                 }                                       
                </script>
                <!-- Fin de contenedor de formulario de detalle -->
            </div>
            <!-- Inicio de contenedor de tabla -->
            <div class="col-sm-8" style="margin-top:-20px">
                <!-- Campos ocultos en los que guaramos la id anterior y la nueva id -->
                <input type="hidden" id="idPrevio" value="">
                <input type="hidden" id="idActual" value="">
                <!-- Inicio de contenedor de tabla -->
                <div class="table-responsive contTabla" >
                    <!-- Inicio de tabla -->
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <!-- Consulta para cargar la tabla -->
                        <?php
                        #Variables para operaciones de sumados totales
                        $cupones = 0;
                        $sumV = 0;
                        $sumIVa = 0;
                        $sumaImpo = 0;
                        $sumaAjuste = 0;
                        $sumaSaldo = 0;
                        #Consulta para cargar la tabla
                        $sqlPago = "SELECT  dtp.id_unico,
                                dtp.detalle_factura,
                                fat.numero_factura,
                                tfat.nombre,
                                dtp.valor,
                                dtp.pago,
                                ter.id_unico tercero,
                                fat.id_unico,
                                dtp.iva,
                                dtp.impoconsumo,
                                dtp.ajuste_peso,
                                dtp.saldo_credito
                        FROM gp_detalle_pago dtp
                        LEFT JOIN gp_detalle_factura dtf ON dtp.detalle_factura = dtf.id_unico
                        LEFT JOIN gp_factura fat ON dtf.factura = fat.id_unico
                        LEFT JOIN gp_tipo_factura tfat ON fat.tipofactura = tfat.id_unico
                        LEFT JOIN gp_pago pg ON dtp.pago = pg.id_unico
                        LEFT JOIN gf_tercero ter ON fat.tercero = ter.id_unico
                        WHERE pg.id_unico = $idPago";
                        $result = $mysqli->query($sqlPago);
                        ?>
                        <!-- Inicio de cabezera de la tabla -->
                        <thead>
                            <!-- Inicio de campos para mostrar los nombres en la tabla -->
                            <tr>
                                <td class="oculto" >Identificador</td>
                                <td width="7%" class="cabeza"></td>
                                <td class="cabeza"><strong>Tipo Factura</strong></td>
                                <td class="cabeza"><strong>Factura</strong></td>
                                <td class="cabeza"><strong>Tercero</strong></td>
                                <td class="cabeza"><strong>Valor</strong></td>
                                <td class="cabeza"><strong>Iva</strong></td>
                                <td class="cabeza"><strong>Impoconsumo</strong></td>
                                <td class="cabeza"><strong>Ajuste peso</strong></td>
                                <td class="cabeza"><strong>Saldo Crédito</strong></td>
                                <td class="cabeza"><strong>Saldo Factura</strong></td>
                                <!-- Fin o cierre de campos para titulos de la tabla -->
                            </tr>
                            <!-- Inicio de campos de filtrado de la tabla -->
                            <tr>
                                <th class="oculto">Identificador</th>
                                <th width="7%" class="cabeza"></th>
                                <th class="cabeza">Tipo Factura</th>
                                <th class="cabeza">Tercero</th>
                                <th class="cabeza">Factura</th>
                                <th class="cabeza">Valor</th>
                                <th class="cabeza">Iva</th>
                                <th class="cabeza">Impoconsumo</th>
                                <th class="cabeza">Ajuste peso</th>
                                <th class="cabeza">Saldo Crédito</th>
                                <th class="cabeza">Saldo Factura</th>
                                <!-- Cierre de campos de filtrado de la tabla -->
                            </tr>
                            <!-- Cierre de cabezera -->
                        </thead>
                        <!-- Inicio de cuerpo de la tabla -->
                        <tbody>
                            <?php
                            $sumaImpo =0;
                            while($row=$result->fetch_row()){ ?>
                            <tr>
                                <?php $cupones+=1; ?>
                                <td class="oculto"></td>
                                <td class="campos" width="7%">
                                    <a href="#<?php echo $row[0];?>" id="del" onclick="javascript:eliminar(<?php echo $row[0]; ?>)" title="Eliminar">
                                        <li class="glyphicon glyphicon-trash"></li>
                                    </a>
                                    <a href="#<?php echo $row[0];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>);">
                                        <li class="glyphicon glyphicon-edit"></li>
                                    </a>
                                </td>
                                <td class="campos text-right">
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblTipofactura'.$row[0].'">'.ucwords(mb_strtolower($row[3])).'</label>'; ?>
                                </td>
                                <td class="campos text-right">
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblFactura'.$row[0].'">'.ucwords(mb_strtolower($row[2])).'</label>'; ?>
                                </td>
                                <td class="campos text-right">
                                    <?php
                                    $sqltercero="SELECT  IF(CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' OR CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,(ter.razonsocial),                                            CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
                                                ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                                LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                                WHERE ter.id_unico = $row[6]
                                                ORDER BY NOMBRE ASC";
                                    $ter = $mysqli->query($sqltercero);
                                    $per = mysqli_fetch_row($ter);
                                    echo '<label class="valorLabel" style="font-weight:normal" title="'.$per[2].'" id="lblTercero'.$row[0].'">'.ucwords(mb_strtolower($per[0])).'</label>';
                                    ?>
                                </td>
                                <td class="campos text-right">
                                    <?php
                                          $sumV += $row[4];
                                          echo '<label class="valorLabel" style="font-weight:normal" id="lblValor'.$row[0].'">'.number_format($row[4], 2, '.', ',').'</label>';
                                          echo '<input maxlength="50" onkeypress="return txtValida(event,\'decimales\')" style="display:none;padding:2px;height:19px" class="col-sm-8 campoD text-left form-control"  type="text" name="txtValor'.$row[0].'" id="txtValor'.$row[0].'" value="'.$row[4].'" />';
                                    ?>
                                    <div >
                                        <table id="tab<?php echo $row[0] ?>" style="padding:0px;background-color:transparent;background:transparent;margin-left: -5px" class="col-sm-1">
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
                                <td class="campos text-right">
                                    <?php
                                    $sumIVa+=$row[8];
                                    echo '<label class="valorLabel" style="font-weight:normal" id="lblIva'.$row[0].'">'.number_format($row[8], 2, '.', ',').'</label>';
                                    ?>
                                </td>
                                <td class="campos text-right">
                                    <?php
                                    $sumaImpo += $row[9];
                                    echo '<label class="valorLabel" style="font-weight:normal" id="lblImpoconsumo'.$row[0].'">'.number_format($row[9], 2, '.', ',').'</label>';
                                    ?>
                                </td>
                                <td class="campos text-right">
                                    <?php
                                    $sumaAjuste +=$row[10];
                                    echo '<label class="valorLabel" style="font-weight:normal" id="lblAjustePeso'.$row[0].'">'.number_format($row[10], 2, '.', ',').'</label>';
                                    ?>
                                </td>
                                <td class="campos text-right">
                                    <?php
                                    $sumaSaldo +=$row[11];
                                    echo '<label class="valorLabel" style="font-weight:normal" id="lblSaldoCredito'.$row[0].'">'.number_format($row[11], 2, '.', ',').'</label>';
                                    ?>
                                </td>
                                <td class="campos text-right">
                                    <?php
                                        $xxx = 0;
                                        $xss = 0;
                                        $yyy = 0;
                                        $sql4 = "SELECT DISTINCT (dtf.valor_total_ajustado - dtf.iva - dtf.impoconsumo - dtf.ajuste_peso) AS ULTIMO
                                                FROM gp_detalle_factura dtf
                                                WHERE dtf.factura = $row[7]";
                                        $result4 = $mysqli->query($sql4);
                                        while($row4 = mysqli_fetch_row($result4)){
                                            $xss += $row4[0];
                                        }
                                        $sql100 = "SELECT dtp.valor
                                                FROM gp_detalle_pago dtp
                                                LEFT JOIN gp_detalle_factura dtf ON dtp.detalle_factura = dtf.id_unico
                                                WHERE dtf.factura = $row[7]";
                                        $result100 = $mysqli->query($sql100);
                                        while($row100 = mysqli_fetch_row($result100)){
                                            $yyy += $row100[0];
                                        }

                                        $xxx = $xss - $yyy;
                                        if($xxx < 0){
                                            $xxx = 0;
                                        }

                                    ?>
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="saldoFactura'.$row[0].'">'.number_format($xxx, 2, '.', ',').'</label>'; ?>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                            <!-- Cierre del cuerpo de la tabla -->
                        </tbody>
                        <!-- Fin de tabla -->
                    </table>
                    <!-- Fin de contenedor de tabla -->
                </div>
                <!-- Fin de contenedor de tabla -->
            </div>
            <!-- Inicio de contenedor de totales -->
            <div class="col-sm-8" style="margin-top:5px; margin-left: 150px">
                <!-- Inicio de contenedor de label de totales -->
                <div class="col-sm-1 col-sm-offset-1" style="margin-right:20px">
                    <!-- Inicio de label -->
                    <div class="form-group" style="" align="left">
                        <label class="control-label">
                            <strong>Totales:</strong>
                        </label>
                        <!-- Fin de label -->
                    </div>
                    <!-- Cierre de contenedor de label de totales -->
                </div>
                <!-- Inicio de contenedor de variable de cupones -->
                <div class="col-sm-1" style="margin-right:0px">
                    <label class="control-label valorLabel" title="Total cupones"><?php echo $cupones; ?></label>
                    <!-- Cierre de contenedor de variable de cupones -->
                </div>
                <!-- Inicio de contenedor de variable de valor -->
                <div class="col-sm-1" style="margin-right:0px">
                    <label class="control-label valorLabel" title="Total Valor"><?php echo "$".number_format($sumV, 2, '.', ','); ?></label>
                    <!-- Cierre de contenedor de variable de valor -->
                </div>
                <div class="col-sm-1" style="margin-right:0px">
                    <label class="control-label valorLabel" title="Total Iva"><?php echo "$".number_format($sumIVa, 2, '.', ','); ?></label>
                    <!-- Cierre de contenedor de variable de valor -->
                </div>
                <div class="col-sm-1" style="margin-right:0px">
                    <label class="control-label valorLabel" title="Total Impoconsumo"><?php echo "$".number_format($sumaImpo, 2, '.', ','); ?></label>
                    <!-- Cierre de contenedor de variable de valor -->
                </div>
                <div class="col-sm-1" style="margin-right:0px">
                    <label class="control-label valorLabel" title="Total Ajuste"><?php echo "$".number_format($sumaAjuste, 2, '.', ','); ?></label>
                    <!-- Cierre de contenedor de variable de valor -->
                </div>
                <div class="col-sm-1" style="margin-right:0px">
                    <label class="control-label valorLabel" title="Total"><?php echo "$".number_format($sumV+$sumIVa+$sumaImpo+$sumaAjuste, 2, '.', ','); ?></label>
                    <!-- Cierre de contenedor de variable de valor -->
                </div>
                <!-- Inicio de contenedor de label -->
                <div class="col-sm-1">
                    <!-- Inicio de label -->
                    <label class="control-label"><strong>Diferencias:</strong></label>
                    <!-- Cierre de contenedor de label -->
                </div>
                <div class="col-sm-1">
                    <label class="control-label valorLabel" title="Diferencia de cupones"><?php if(!empty($_GET['cupones'])){ $c = (int) $_GET['cupones']; echo $c- $cupones ;}else{echo 0;}?></label>
                    <!-- Cierre de contenedor de cupones -->
                </div>
                <div class="col-sm-1">
                    <label class="control-label valorLabel" title="Diferencia de valor"><?php if(!empty($_GET['valor'])){$va=(double) $_GET['valor']; $oper = $va-$sumV; echo number_format($oper, 2, '.', ',');}else{echo '0.00';} ?></label>
                    <!-- Cierre de contenedor de diferencia de valor -->
                </div>
                <div class="col-sm-1 col-sm-offset-1">
                    <a class="btn btn-primary" style="box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;" id="btnPay" onclick="realize_pay(<?php echo $idCnt ?>,<?php echo $idPptal ?>,<?php echo $idPago ?>,<?php echo $tercero ?>)">Registrar Pago</a>
                </div>
                <!-- Cierre de  contenedor de totales-->
            </div>
            <!-- Cierre de grib de boostrap -->
        </div>
        <!-- Cierre de contenedor principal del formulario -->
    </div>
    <!-- Scripts -->
    <script type="text/javascript" >
        //Script para eliminar
        var valorNuevo = 0;
        var valorAnt = 0;
        function eliminar(id){
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminarDetallePago.php?id="+id,
                    success: function (data) {
                    result = JSON.parse(data);
                    if(result==true)
                      $("#mdlEliminado").modal('show');
                    else
                      $("#mdlNoeliminado").modal('show');
                    }
                });
            });
        }
        //Script para modificar pago
        function modificarPago(pago, cnt, pptal){
            jsShowWindowLoad('Modificando Información');
            var id = pago;            
            var fecha=$("#fecha").val();
            var banco=$("#sltBanco").val();
            var tercero=$("#sltTercero").val();
            var form_data = {
                id:id,
                fecha:fecha,
                banco:banco,
                tercero:tercero, 
                cnt:cnt,
                pptal:pptal,
                action:21,
            };

            var result='';
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_facturaJson.php",
                data:form_data,
                success: function (data) {
                    jsRemoveWindowLoad();
                    console.log(data+'acc');
                    if (data==0) {
                        $("#mdlModificado").modal('show');
                    }else{
                        $("#mdlNomodificado").modal('show');
                    }
                }
            });
        }
        //Script para modificar el valor
        function modificar(id){
            valorAnt = $("#txtValor"+id).val();
            $("#txtValor"+id).keyup(function(){
                valorNuevo = $("#txtValor"+id).val();
            });

            if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
                var lblValorC = 'lblValor'+$("#idPrevio").val();
                var txtValorC = 'txtValor'+$("#idPrevio").val();
                var guardarC = 'guardar'+$("#idPrevio").val();
                var cancelarC = 'cancelar'+$("#idPrevio").val();
                var tablaC = 'tab'+$("#idPrevio").val();

                $("#"+lblValorC).css('display','block');
                $("#"+txtValorC).css('display','none');
                $("#"+guardarC).css('display','none');
                $("#"+cancelarC).css('display','none');
                $("#"+tablaC).css('display','none');
            }

            var lblValor = 'lblValor'+id;
            var txtValor = 'txtValor'+id;
            var guardar = 'guardar'+id;
            var cancelar = 'cancelar'+id;
            var tabla = 'tab'+id;

            $("#"+txtValor).css('display','block');
            $("#"+lblValor).css('display','none');
            $("#"+guardar).css('display','block');
            $("#"+cancelar).css('display','block');
            $("#"+tabla).css('display','block');

            $("#idActual").val(id);

            if($("#idPrevio").val() != id){
                $("#idPrevio").val(id);
            }
        }
        //Script para cancelar el proceso de modificar el valor
        function cancelar(id){
            var lblValor = 'lblValor'+id;
            var txtValor = 'txtValor'+id;
            var guardar = 'guardar'+id;
            var cancelar = 'cancelar'+id;
            var tabla = 'tab'+id;

            $("#"+lblValor).css('display','block');
            $("#"+txtValor).css('display','none');
            $("#"+guardar).css('display','none');
            $("#"+cancelar).css('display','none');
            $("#"+tabla).css('display','none');
            $("#"+txtValor).val(valorAnt);
        }
        //Script para guardar los datos
        function guardarCambios(id){
           if(valorNuevo>valorAnt){
                $("#mdlValor").modal('show');
            }else{
                var form_data = {
                    is_ajax:1,
                    id:id,
                    valor:valorNuevo = $("#txtValor"+id).val()
                };
                var result = '';
                $.ajax({
                    type: 'POST',
                    url: "json/modificarDetallePago.php",
                    data:form_data,
                    success: function (data) {
                        result = JSON.parse(data);
                        if(result==true){
                            $("#mdlModificado").modal('show');
                        }else{
                            $("#mdlNomodificado").modal('show');
                        }
                    }
                });
            }
        }
        //Funcion para buscar
        function buscarRecaudos() {
            //Capturamos el valor del campo de busqueda
            var tipo = parseInt($("#sltBuscarT").val());
            //Validamos que no este vacio
            if(!isNaN(tipo)){
                //Variable de envio para el envio ajax
                var form_data = {
                    action:8,
                    tipo:tipo
                };
                //Envio ajax
                var option = '<option value="">Recaudo</option>';
                $.ajax({
                    type:'POST',
                    url:'jsonPptal/gf_facturaJson.php',
                    data:form_data,
                    success: function(data,textStatus,jqXHR){
                        var option = option+data;
                         $("#sltBuscar").html(option);
                    },error : function(data,textStatus,jqXHR){
                        alert('data : '+data+' , textStatus: '+textStatus+', jqXHR : '+jqXHR);
                    }
                });
            }
        }
        </script>
        <script>
        function buscarPago() {
            //Capturamos el valor del campo de busqueda
            var pago = parseInt($("#sltBuscar").val());
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
                        data = data.replace('registrar_GF_RECAUDO_FACTURACION_2', 'GP_RECAUDO_FACTURACION');
                        window.location = data;
                    },error : function(data,textStatus,jqXHR){
                        alert('data : '+data+' , textStatus: '+textStatus+', jqXHR : '+jqXHR);
                    }
                });
            }
        }
        //Función para cargar el valor del inpute
        function load_value_input(concepto,factura){
            var form_data = {
                concepto:concepto,
                factura:factura,
                action: 20,
            };
            console.log(form_data); 
            $.ajax({
                type:'POST',
                url:'jsonPptal/gf_facturaJson.php',
                data:form_data,
                success: function(data,textStatus,jqXHR){
                    console.log('Valor Cagar '+data);
                    $("#txtValor2").val(data);
                    $("#txtValorConcepto").val(data);
                }
            }).error(function(data,textError,jqXHR) {
                alert('data :'+data+', error: '+textError+', jqXHR :'+jqXHR);
            });;
        }
        //Función para realizar pago para enviar los valores y crear los comprobantes detalles de los comprobantes pptal, y cnt
        function realize_pay(id_cnt,id_pptal,id_pago,id_tercero){
            jsShowWindowLoad('Realizando Pago...');
            //Array de envio
            var form_data = {
                action:17,
                idCnt:id_cnt,
                idPptal:id_pptal,
                idPago:id_pago,
                id_tercero:id_tercero
            };
            //Variable de captura de ajax
            var result = '';
            //Envio de ajax
            $.ajax({
                type:'POST',
                url:'jsonPptal/gf_facturaJson.php',
                data:form_data,
                success: function(data,textStatus,jqXHR) {
                    jsRemoveWindowLoad();
                    console.log(data);
                    result = JSON.parse(data);
                    if(result==true) {
                        $("#modalGuardado").modal('show');
                    }else{
                        $("#modalNoGuardado").modal('show');
                    }
                    //console.log(data);
                }
            }).error(function(data,textError,jqXHR) {
                alert('data :'+data+', error: '+textError+', jqXHR :'+jqXHR);
                console.log('data :'+data+', error: '+textError+', jqXHR :'+jqXHR);
            });
        }

        function validate_value(valorN) {
            var valorA = parseInt($("#txtValorConcepto").val());
            if(valorN > valorA) {
                $("#modalValor").modal('show');
                $("#txtValor2").val(valorA);
            }
        }
    </script>
    <!-- Script para limpiar los campos del detalle -->
    <script type="text/javascript">
        function limpiarCampos(){
            $("#sltFactura2").prop('selectedIndex',0);
            $("#txtValor2").val('');
            $("#sltFactura2").select2("val", "");
        }
    </script>
    <!-- Llamado a la libreria select2 para el campo sltTercero y factura -->
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript">
        $("#sltTercero").select2({placeholder:"Tercero",allowClear: true});
        $("#sltFactura2").select2({placeholder:"Factura",allowClear: true});
        $("#sltBuscar").select2({placeholder:"Tercero",allowClear: true});
        $("#sltBuscarT").select2({placeholder:"Tercero",allowClear: true});
        $("#sltBanco").select2({placeholder:"Banco",allowClear: true});
        $("#sltConcepto").select2({placeholder:"Concepto",allowClear: true});
    </script>
    <!-- Cierre del body -->
</body>
<!-- Llamado o invocación al pie de pagina -->
<?php require_once 'footer.php' ?>
<!-- Modal de validación de tercero -->
<div class="modal fade" id="mdlValTercero" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Seleccione un tercero para consultar los detalle de recaudo.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnTercero" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal de validación de valor -->
<div class="modal fade" id="mdlValor" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Ingrese un valor menor al actual</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnValor" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal de eliminado -->
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de Detalle Pago?</p>
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
<!-- modal de tipo factura -->
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
<!-- Modal de tipo pago -->
<div class="modal fade" id="mdlTPago" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Seleccione un tipo pago.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="tbmtipoF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modales de modificado -->
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
<!-- Modal de validación de fecha -->
<div class="modal fade" id="mdlfecha" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>La fecha es anterior al ultimo pago por favor ingrese una fecha diferente.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="tbmtipoF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal de ingreso de datos -->
<div class="modal fade" id="modalRegistro" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Información guardada correctamente.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalNoRegistro" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se ha podido guardar la información.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
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
                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modales de guardado -->
<div class="modal fade" id="modalGuardado" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Información guardada correctamente.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnG" class="btn" onclick="reload_page()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="noguardado" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se ha podido guardar la información.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnG2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!-- modal de validación de valor -->
<div class="modal fade" id="modalValor" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>El valor no puede ser mayor al valor del concepto registrado.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnValValor" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
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
                <button type="button" id="btnInfoM" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script>
    $("#btnInfoM").click(function(){
        document.location.reload();
    })
</script>
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
                <button type="button" id="btnmyModal1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
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
                <label id="mensaje" name="mensaje"></label>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script>
    $("#btnmyModal1").click(function(){
        document.location.reload();
    })
</script>
<!-- Scripts para recargar los botones -->
<script type="text/javascript">
    $('#btnModifico').click(function(){
        document.location.reload();
    });
</script>
<script type="text/javascript">
    $('#btnNoModifico').click(function(){
        document.location.reload();
    });
</script>
<script type="text/javascript">
    $('#ver1').click(function(){
        document.location.reload();
    });
</script>
<script type="text/javascript">
    $('#ver2').click(function(){
        document.location.reload();
    });
</script>
<script type="text/javascript">
    $('#btnG').click(function(){
        document.location.reload();
    });
</script>
<script type="text/javascript">
    $('#btnG2').click(function(){
        document.location.reload();
    });
</script>
<!-- Script o función para recargar el html del modal -->
<script type="text/javascript">
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
    //Función para recargar la pagina
    function reload_page () {
        document.location.reload();
    }
    </script>
    <script>
    //Función para validar que las cuentas esten balanceadas
    $(document).ready(function(){

        var form_data = {
            existente:60,
            id_cnt : <?php echo $idCnt==''?0:$idCnt ?>
        };
        $.ajax({
            type:'POST',
            url:'consultasBasicas/consultarNumeros.php',
            data:form_data,
            success: function(data) {

                if(data == 1){

                    $("#btnEditar, #btnPay, #mod, #del, #btnDetalle,#btnRet").attr('disabled',true);
                    $("#btnEditar").removeAttr('onclick');
                    //$("#btnVerRet").removeAttr('onclick');
                    $("#btnPay").removeAttr('onclick');
                    $("#btnRet").removeAttr('onclick');
                    $("#mod").removeAttr('onclick');
                    $("#del").removeAttr('onclick');
                    $("#btnDetalle").removeAttr('onclick');
                }
            }
        }).error(function(data,textError,jqXHR) {
           console.log('Error :'+textError);
        });
    });
    $(document).ready(function(){
        var form_data = {
            existente:61,
            id_cnt : <?php echo $idCnt==''?0:$idCnt ?>
        };
        $.ajax({
            type:'POST',
            url:'consultasBasicas/consultarNumeros.php',
            data:form_data,
            success: function(data,textStatus,jqXHR) {
                console.log('data61'+data);
                if(data == 1){

                    $("#btnEditar, #btnPay, #mod, #del, #btnDetalle,#btnRet").attr('disabled',true);
                    $("#btnEditar").removeAttr('onclick');
                    $("#btnPay").removeAttr('onclick');
                    $("#btnRet").removeAttr('onclick');
                    //$("#btnVerRet").removeAttr('onclick');
                    $("#mod").removeAttr('onclick');
                    $("#del").removeAttr('onclick');
                    $("#btnDetalle").removeAttr('onclick');
                    $("#btnEditar, #btnPay, #mod, #del, #btnDetalle,#btnRet").removeAttr('title');
                    $("#mod, #del").removeAttr('href');
                    $("#mod, #del").removeClass('active');
                }
            }
        }).error(function(data,textError,jqXHR) {
           console.log('Error :'+textError);
        });

        var form_data = {
            existente:63,
            id_cnt : <?php echo $idCnt==''?0:$idCnt ?>
        };
        $.ajax({
            type:'POST',
            url:'consultasBasicas/consultarNumeros.php',
            data:form_data,
            success: function(data,textStatus,jqXHR) {
                if(data == true){
                    console.log('data63'+data);
                    $("#btnEditar, #btnPay, #mod, #del, #btnDetalle, #btnEliminar,#btnRet").attr('disabled',true);
                    $("#btnEditar, #btnPay, #mod, #del, #btnDetalle, #btnEliminar, #btnRet").removeAttr('onclick');
                    $("#btnEditar, #btnPay, #mod, #del, #btnDetalle, #btnEliminar, #btnRet").removeAttr('title');
                    $("#mod, #del").removeAttr('href');
                    $("#mod, #del").removeClass('active');
                }
            }
        }).error(function(data,textError,jqXHR) {
           console.log('Error :'+textError);
        });
    });

    function eliminar_datos(id_pago,id_cnt,id_pptal){
        var form_data = {idPago:id_pago,idCnt:id_cnt,idPptal:id_pptal,action:22};
        $("#myModal").modal('show');
        var result = ""
        $("#ver").click(function(){
            jsShowWindowLoad('Eliminando Datos...');
            $.ajax({
                type:'POST',
                url:'jsonPptal/gf_facturaJson.php',
                data:form_data,
                success: function(data){
                    jsRemoveWindowLoad();
                    if(data==true){
                      $("#mdlEliminado").modal('show');
                    }else{
                      $("#mdlNoeliminado").modal('show');
                    }
                }
            });
        });
    }

    function reconstruirContablidad(idCnt, idPptal, idPago){
        jsShowWindowLoad('Restaurando Comprobantes...');
        var form_data = {
            idCnt:idCnt,
            idPptal:idPptal,
            idPago:idPago, 
            action:17,
        };
        var result = "";
        $.ajax({
            type:"POST",
            url:"jsonPptal/gf_facturaJson.php",
            data:form_data,
            success:function(data){
                 jsRemoveWindowLoad();
                console.log('Restauracion'+data);
                result = JSON.parse(data);
                if(result == true){
                    $("#modalGuardado").modal("show");
                }else{
                    $("#modalNoGuardado").modal('show');
                }
            }
        });
    }
</script>
<!-- Inivocamos en la parte inferior el archivo que contendra el modal para evitar posibles errores -->
<?php require_once './modalConsultaComprobanteC.php'; ?>
<script type="text/javascript">
    //Función para ajustar la cabezera de la tabla
    $("#modalComprobanteC").on('shown.bs.modal',function(){
        var dataTable = $("#tablaDetalleC").DataTable();
        dataTable.columns.adjust().responsive.recalc();
    });
</script>
<?php require_once './modalConsultaComprobanteP.php'; ?>
<script type="text/javascript">
    $("#modalComprobanteP").on('shown.bs.modal',function(){
        var dataTable = $("#tablaDetalleP").DataTable();
        dataTable.columns.adjust().responsive.recalc();
    });
</script>
<!-- Cierre de html -->
<script>
        function retenciones() {

            var id = $("#id").val();
            var form_data={
              id:<?php echo $idCnt;?> ,
              valorTotal :<?php echo $sumV; ?>,
              pptal :<?php echo $idPptal;?>
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
              id:<?php echo $idCnt;?> ,
              valorTotal :<?php echo $sumV; ?>,
              pptal :<?php echo $idPptal;?>
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
    <?php require_once './MODAL_GF_RETENCIONES_FAC.php'; ?>
    <?php require_once './GF_MODIFICAR_RETENCIONES_MODAL.php'; ?>
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