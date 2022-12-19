<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');
?>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #periodo-error, #iduvms-error, #valor-error  {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;

    }

    body{
        font-size: 12px;
    }

</style>

<script>


    $().ready(function () {
        var validator = $("#form").validate({
            ignore: "",
            errorPlacement: function (error, element) {

                $(element)
                        .closest("form")
                        .find("label[for='" + element.attr("id") + "']")
                        .append(error);
            },
        });

        $(".cancel").click(function () {
            validator.resetForm();
        });
    });
</script>

<style>
    .form-control {font-size: 12px;}

</style>

<script type="text/javascript">
    $(document).ready(function () {
        var i = 0;
        $('#tablaR thead th').each(function () {
            if (i >= 0) {
                var title = $(this).text();
                switch (i) {
                    case 0:
                        $(this).html('');
                        break;
                    case 1:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 2:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 3:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 4:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 5:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 6:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 7:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 8:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 9:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 10:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 11:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 12:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 13:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 14:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;

                }
                i = i + 1;
            } else {
                i = i + 1;
            }
        });

        // DataTable
        var table = $('#tablaR').DataTable({
            'clickToSelect': true,
            'select': true,
            "autoFill": true,
            "scrollX": true,
            "pageLength": 5,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No Existen Registros...",
                "info": "Página _PAGE_ de _PAGES_ ",
                "infoEmpty": "No existen datos",
                "infoFiltered": "(Filtrado de _MAX_ registros)",
                "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ registros", "sInfoEmpty": "Mostrando 0 - 0 de 0 registros"
            },
            'columnDefs': [{
                    'targets': 0,
                    'searchable': false,
                    'orderable': false,

                    'className': 'dt-body-center'

                }]
        });

        var i = 0;
        table.columns().every(function () {
            var that = this;
            if (i != 0) {
                $('input', this.header()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that
                                .search(this.value)
                                .draw();
                    }
                });
                i = i + 1;
            } else {
                i = i + 1;
            }
        });
    });
</script>
<style>
    .cabeza{
        white-space:nowrap;
        padding: 20px;
    }
    .campos{
        padding:-20px;
    }
    table.dataTable thead tr th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px;white-space: nowrap}
    .dataTables_wrapper .ui-toolbar{padding:2px}

    body{
        font-size: 10px
    }
</style>
<style>
    .valorLabel{
        font-size: 10px;
    }
    .valorLabel:hover{
        cursor: pointer;
        color:#1155CC;
    }
    /*td de la tabla*/
    .campos{
        padding: 0px;
        font-size: 10px;
        height: 10px;
    }
</style>
<title>Registrar Lectura</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <!--Titulo del formulario-->
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Lectura</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GP_LECTURAINDIVIDUALJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <!--Ingresa la información-->
                        <div class="form-group" style="margin-top: -10px;">
                            <input type="hidden" id="iduvms" name="iduvms" required title="Ingrese Referencia" />
                            <label for="iduvms" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Referencia:</label>
                            <input type="text" name="uvms" id="uvms" class="form-control"  title="Ingrese la referencia"  placeholder="Referencia" required style="display: inline; width: 250px" onchange="valorCambio();">
                            <a id="btnBuscar" class="btn" title="Buscar Referencia" style="display: inline" onclick="buscar();"><li class="glyphicon glyphicon-search"></li></a>
                        </div>
                        <script>
                            $("#uvms").keyup(function () {
                                $("#uvms").autocomplete({
                                    source: "consultasBasicas/autoCompletadoLectura.php",
                                    minlength: 5,
                                    select: function (event, ui) {
                                        var referencia = ui.item;
                                        var ref = referencia.value;
                                        var form_data = {
                                            case: 11,
                                            referencia: ref,
                                        };
                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasBasicas/busquedas.php",
                                            data: form_data,
                                            success: function (data) {
                                                var resultado = JSON.parse(data);
                                                if (resultado == 'null' || resultado == null || resultado == '' || resultado == "") {
                                                    document.getElementById('iduvms').value = '';
                                                } else {

                                                    document.getElementById('iduvms').value = resultado;
                                                    var id = document.getElementById('iduvms').value;
                                                    var form_data = {
                                                        case: 7,
                                                        id: id
                                                    };
                                                    $.ajax({
                                                        type: 'POST',
                                                        url: "consultasBasicas/busquedas.php",
                                                        data: form_data,
                                                        success: function (data) {
                                                            $("#periodo1").html(data).fadeIn();
                                                            $("#periodo1").css('display', 'none');
                                                        }
                                                    });
                                                }
                                            }
                                        });
                                    },
                                });
                            });
                        </script>
                        <script>
                            function valorCambio() {

                                var ref = document.getElementById('uvms').value;
                                var form_data = {
                                    case: 11,
                                    referencia: ref,
                                };
                                $.ajax({
                                    type: 'POST',
                                    url: "consultasBasicas/busquedas.php",
                                    data: form_data,
                                    success: function (data) {
                                        var resultado = JSON.parse(data);
                                        if (resultado == 'null' || resultado == null || resultado == '' || resultado == "") {
                                            document.getElementById('iduvms').value = '';
                                            document.getElementById('periodo1').disabled = true;

                                        } else {
                                            document.getElementById('iduvms').value = resultado;
                                            document.getElementById('periodo1').disabled = false;

                                            var id = document.getElementById('iduvms').value;
                                            var form_data = {
                                                case: 7,
                                                id: id
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasBasicas/busquedas.php",
                                                data: form_data,
                                                success: function (data) {
                                                    $("#periodo1").html(data).fadeIn();
                                                    $("#periodo1").css('display', 'none');
                                                }
                                            });
                                        }
                                    }
                                });
                            }
                        </script>
                        <input type="hidden" name="periodo" value="<?php echo $_GET['p'] ?>" id="periodo"  title="Seleccione periodo">
                        <div class="form-group" style="margin-top: 0px;">
                            <label for="valor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                            <input type="text" name="valor" id="valor" class="form-control" title="Ingrese el valor"  placeholder="Valor" required style="width: 250px" onkeypress="return txtValida(event, 'num')">
                            <label class="text-center" id="labelError" name="labelError" style="margin-left: 500px; color: #155180; font-weight: normal; font-style: italic;"></label>
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>      
            </div>
        </div>
    </div>
    <script src="js/select/select2.full.js"></script>
    <script>
                                $(document).ready(function () {
                                    $(".select2_single").select2({
                                        allowClear: true
                                    });
                                });
                                $("#periodo1").change(function () {
                                    var periodo = document.getElementById('periodo1').value;
                                    document.getElementById('periodo').value = periodo;
                                    document.getElementById('valor1').value = '';
                                    document.getElementById('valor').value = '';
                                    if (periodo != '') {
                                        document.getElementById('valor1').disabled = false;
                                    } else {

                                        document.getElementById('valor1').disabled = true;
                                    }
                                });
                                $("#valor1").change(function () {

                                    var id = document.getElementById('iduvms').value;
                                    var valor = document.getElementById('valor').value;
                                    var periodo = document.getElementById('periodo').value;
                                    var form_data = {
                                        case: 6,
                                        id: id,
                                        valor: valor,
                                        periodo: periodo
                                    };
                                    $.ajax({
                                        type: 'POST',
                                        url: "consultasBasicas/busquedas.php",
                                        data: form_data,
                                        success: function (data) {

                                            var resultado = JSON.parse(data);
                                            if (resultado == 'null' || resultado == null || resultado == '' || resultado == "") {
                                                resultado = 0;
                                            }
                                            var valor1 = (parseInt(valor));
                                            var resultado1 = parseInt(resultado);
                                            if (valor1 >= resultado1) {
                                                document.getElementById('valor').value = valor;
                                                document.getElementById('labelError').innerHTML = '';
                                            } else {
                                                document.getElementById('valor').value = '';
                                                document.getElementById('labelError').innerHTML = 'Valor Inválido';

                                            }
                                        }
                                    });
                                });
    </script>   
    <div class="modal fade" id="myModalBuscar" role="dialog" align="center" >
        <div class="modal-dialog" style="height:600px;width:90%">
            <div class="modal-content client-form1">
                <div id="forma-modal" class="modal-header">       
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Buscar Medidor</h4>
                    <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                        <button type="button" id="btnCerrar" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                    </div>
                </div>
                <div class="modal-body" style="margin-top: 8px">                                
                    <div class="row">
                        <div class="col-sm-12" style="margin-top: 10px;">
                            <div class="table-responsive " >
                                <table id="tablaR" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">
                                    <thead>
                                        <tr>    
                                            <td class="cabeza" ></td>
                                            <td class="cabeza"><strong>Referencia</strong></td>
                                            <td class="cabeza"><strong>Marca</strong></td>
                                            <td class="cabeza"><strong>Tipo Medidor</strong></td>
                                            <td class="cabeza"><strong>Tipo Servicio</strong></td>
                                            <td class="cabeza"><strong>Tipo Unidad</strong></td>
                                            <td class="cabeza"><strong>Tercero</strong></td>
                                            <td class="cabeza"><strong>Uso</strong></td>
                                            <td class="cabeza"><strong>Estrato</strong></td>
                                            <td class="cabeza"><strong>Código Ruta</strong></td>
                                            <td class="cabeza"><strong>Código Interno</strong></td>
                                            <td class="cabeza"><strong>Código Catastral</strong></td>
                                            <td class="cabeza"><strong>Matrícula Inmobiliaria</strong></td>
                                            <td class="cabeza"><strong>Dirección</strong></td>
                                        </tr>
                                        <tr>  
                                            <th class="cabeza"></th>
                                            <th class="cabeza">Referencia</th>
                                            <th class="cabeza">Marca</th>
                                            <th class="cabeza">Tipo Medidor</th>
                                            <th class="cabeza">Tipo Servicio</th>
                                            <th class="cabeza">Tipo Unidad</th>
                                            <th class="cabeza">Tercero</th>
                                            <th class="cabeza">Uso</th>
                                            <th class="cabeza">Estrato</th>
                                            <th class="cabeza">Código Ruta</th>
                                            <th class="cabeza">Código Interno</th>
                                            <th class="cabeza">Código Catastral</th>
                                            <th class="cabeza">Matrícula Inmobiliaria</th>
                                            <th class="cabeza">Dirección</th>

                                        </tr>
                                    </thead>                                 
                                    <tbody id="cuerpo" class="text-center"> 
                                        <?php $resultado = "SELECT
                                                        uvms.id_unico,
                                                        m.referencia,
                                                        mr.nombre,
                                                        tm.nombre,
                                                        ts.nombre,
                                                        tuv.nombre,
                                                        IF(
                                                          CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos) = '',
                                                          CONCAT(t.razonsocial,'(',t.numeroidentificacion,')'),
                                                          CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos,'(',t.numeroidentificacion,')')) AS 'NOMBRE',
                                                        u.nombre,
                                                        et.nombre,
                                                        uv.codigo_ruta,
                                                        uv.codigo_interno,
                                                        p.codigo_catastral,
                                                        p.matricula_inmobiliaria,
                                                        p.direccion
                                                      FROM
                                                        gp_unidad_vivienda_medidor_servicio uvms
                                                      LEFT JOIN
                                                        gp_medidor m ON uvms.medidor = m.id_unico
                                                      LEFT JOIN
                                                        gp_marca mr ON mr.id_unico = m.marca
                                                      LEFT JOIN
                                                        gp_tipo_medidor tm ON m.tipo_medidor = tm.id_unico
                                                      LEFT JOIN
                                                        gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico
                                                      LEFT JOIN
                                                        gp_tipo_servicio ts ON uvs.tipo_servicio = ts.id_unico
                                                      LEFT JOIN
                                                        gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico
                                                      LEFT JOIN
                                                        gp_tipo_unidad_vivienda tuv ON uv.tipo_unidad = tuv.id_unico
                                                      LEFT JOIN
                                                        gf_tercero t ON uv.tercero = t.id_unico
                                                      LEFT JOIN
                                                        gp_uso u ON uv.uso = u.id_unico
                                                      LEFT JOIN
                                                        gp_estrato et ON et.id_unico = uv.estrato
                                                      LEFT JOIN
                                                        gp_predio1 p ON uv.predio = p.id_unico";
                                        $resultado = $mysqli->query($resultado);
                                        ?>
<?php while ($row = mysqli_fetch_row($resultado)) { ?>
                                            <tr>

                                                <td class="campos"><a onclick="referencia(<?php echo $row[0] . ',' . "'" . $row[1] . "'" ?>)" class="btn"><i class="glyphicon glyphicon-download-alt"></i></a></td>
                                                <td class="campos"><?php echo mb_strtoupper(($row[1])); ?></td>
                                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[2]))); ?></td>
                                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[3]))); ?></td>
                                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[4]))); ?></td>
                                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[5]))); ?></td>
                                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[6]))); ?></td>
                                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[7]))); ?></td>
                                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[8]))); ?></td>
                                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[9]))); ?></td>
                                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[10]))); ?></td>
                                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[11]))); ?></td>
                                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[12]))); ?></td>
                                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[13]))); ?></td>
                                            </tr>
<?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="forma-modal" class="modal-footer"></div>
            </div>

        </div>
    </div>

<?php require_once 'footer.php'; ?>

    <script>
        function buscar() {

            $("#myModalBuscar").modal('show');
        }
        $("#myModalBuscar").on('shown.bs.modal', function () {
            var dataTable = $("#tablaR").DataTable();
            dataTable.columns.adjust().responsive.recalc();
        });
    </script>
    <script>
        function referencia(id, referencia) {

            document.getElementById('uvms').value = referencia;
            document.getElementById('iduvms').value = id;
            document.getElementById('periodo1').disabled = false;
            document.getElementById('valor1').disabled = false;
            var form_data = {
                case: 7,
                id: id
            };
            $.ajax({
                type: 'POST',
                url: "consultasBasicas/busquedas.php",
                data: form_data,
                success: function (data) {
                    $("#periodo1").html(data).fadeIn();
                    $("#periodo1").css('display', 'none');
                }
            });
            $("#myModalBuscar").modal('hide');
        }
    </script>

</body>
</html>

