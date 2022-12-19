<?php
require("./Conexion/ConexionPDO.php");
require './Conexion/conexion.php';
require './head.php';
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$con = new ConexionPDO();
if (!empty($_GET['tipo'])){
    if ($_GET['tipo'] == 1){
        $titulo = "Sin Salida";
    }else if ($_GET['tipo'] == 2){
        $titulo = "Salida";
    }
}else {
    $titulo = ".";
}

?>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<link rel="stylesheet" href="css/jquery.datetimepicker.css">
<link rel="stylesheet" href="css/desing.css">
<title>Listar Ingresos</title>
<style>
    #form>.form-group{
        margin-bottom: 5px !important;
    }
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;font-family: Arial;}
    .campos{padding: 0px;font-size: 10px}
</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 class="tituloform" align="center" style="margin-top: 0px;">Listar Ingresos </h2>
                <h5 id="forma-titulo3a" align="center" style="width:100%; display:inline-block; margin-bottom: 10px; margin-right: -1px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo;?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; height: 71px;" class="client-form col-sm-12 col-md-12 col-lg-12">
                    <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="">
                            <input type="hidden" value="obligacion" name="expedir">
                            <input type="hidden" name="txtamortizacion" id="txtamortizacion" value="<?php echo $amortizacion;?>">
                            <input type="hidden" name="txtconcepto" id="txtconcepto" value="<?php echo $concepto;?>">
                            <input type="hidden" name="txtdetallepptal" id="txtdetallepptal" value="<?php echo $detalle ;?>">
                            <div class="form-group" style="margin-top: 13px;">
                            <label for="fechaF" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Ingresos:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="stlingresos" id="stlingresos" class="select2_single form-control" style="padding-left: 0px; width: 34%" title="Seleccione el tipo del ingreso" tabindex="15" required="">
                                    <option value="">Tipo</option>
                                    <option value="1">Sin factura</option>
                                    <option value="2">Con factura</option>                                   
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 5px;">
                <div class="table-responsive contTabla" >
                    <table id="tabla212" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">
                        <thead style="position: relative;overflow: auto;width: 100%;">
                            <?php if (!empty($_GET['tipo']) && $_GET['tipo'] != 2){ ?>
                            <tr>
                                <td class="oculto">Identificador</td>
                                <td width="7%" class="cabeza"></td>
                                <td class="cabeza"><strong>Número</strong></td>
                                <td class="cabeza"><strong>Ingreso</strong></td>
                                <td class="cabeza"><strong>Placa</strong></td>
                                <td class="cabeza"><strong>Valor</strong></td>
                            </tr>
                            <tr>
                                <th class="oculto">Identificador</th>
                                <th width="7%" class="cabeza"></th>
                                <th class="cabeza">Número</th>
                                <th class="cabeza">Ingreso</th>
                                <th class="cabeza">Placa</th>
                                <th class="cabeza">Valor</th>
                            </tr>
                            <?php } else if (!empty($_GET['tipo']) && $_GET['tipo'] == 2) {?>
                            <tr>
                                <td class="oculto">Identificador</td>
                                <td width="7%" class="cabeza"></td>
                                <td class="cabeza"><strong>Número</strong></td>
                                <td class="cabeza"><strong>Placa</strong></td>
                                <td class="cabeza"><strong>Ingreso</strong></td>
                                <td class="cabeza"><strong>Salida</strong></td>
                                <td class="cabeza"><strong>Factura</strong></td>
                                <td class="cabeza"><strong>Recaudo</strong></td>
                            </tr>
                            <tr>
                                <th class="oculto">Identificador</th>
                                <th width="7%" class="cabeza"></th>
                                <th class="cabeza">Número</th>
                                <th class="cabeza">Placa</th>
                                <th class="cabeza">Ingreso</th>
                                <th class="cabeza">Salida</th>
                                <th class="cabeza">Factura</th>
                                <th class="cabeza">Recaudo</th>
                            </tr>
                            <?php } ?>
                        </thead>                            
                        <tbody>
                        <?php 
                        if (isset($_GET['tipo']) && $_GET['tipo'] == 1){ #Sin salida
                                $sqlingresos = "SELECT 
                                gq.id_unico, gq.numero,
                                CONCAT(DATE_FORMAT(gq.fecha,'%d/%m/%Y'),' ',gq.hora) as fecha,
                                placa, tpv.nombre
                                FROM gq_ingreso_parqueadero gq
                                LEFT JOIN gp_tipo_vehiculo tpv ON gq.tipo_vehiculo = tpv.id_unico
                                WHERE gq.factura IS NULL
                                ORDER BY 2";
                                $resingresos = $mysqli->query($sqlingresos);
                                while ($row = mysqli_fetch_row($resingresos)) {
                                    echo "<tr>";                                
                                    echo "<td class='oculto'></td>";
                                    echo "<td style='background-color:transparent;'>
                                        <a  href='javascript:void(0)' title='Eliminar' style='padding-left: 30px;' onclick='javascript:eliminar($row[0])'>
                                            <li class='glyphicon glyphicon-trash'></li>
                                        </a>
                                    </td>";
                                    echo "<td class='campos'>$row[1]</td>";
                                    echo "<td class='campos'>$row[2]</td>";
                                    $placa = strtoupper($row[3]);
                                    echo "<td class='campos'>$placa</td>";
                                    echo "<td class='campos'>$row[4]</td>";
                                    echo "</tr>";
                                }
                            }else if (isset($_GET['tipo']) && $_GET['tipo'] == 2){ #Con Salida
                                $sqlingresos = "SELECT 
                                ing.id_unico, ing.numero, ing.placa, 
                                DATE_FORMAT(CONCAT(ing.fecha,' ',ing.hora),'%d/%m/%Y %h:%i:%s %p') as fecha,
                                DATE_FORMAT(ing.salida,'%d/%m/%Y %h:%i:%s %p') as salida,
                                fac.id_unico as factura, fac.numero_factura,
                                pg.id_unico as pago, pg.numero_pago
                                FROM gq_ingreso_parqueadero ing
                                LEFT JOIN gp_factura fac ON ing.factura = fac.id_unico
                                LEFT JOIN gp_detalle_factura dtf ON ing.factura = dtf.factura
                                LEFT JOIN gp_detalle_pago dtp ON dtf.id_unico = detalle_factura
                                LEFT JOIN gp_pago pg ON dtp.pago = pg.id_unico
                                WHERE ing.factura IS NOT NULL
                                ORDER BY 4";
                                $resingresos = $mysqli->query($sqlingresos);
                                while ($row = mysqli_fetch_row($resingresos)) {
                                    echo "<tr>";                                
                                    echo "<td class='oculto'></td>";
                                    echo "<td style='background-color:transparent;'></td>";
                                    echo "<td class='campos'>$row[1]</td>";
                                    $placa = strtoupper($row[2]);
                                    echo "<td class='campos'>$placa</td>";
                                    echo "<td class='campos'>$row[3]</td>";
                                    echo "<td class='campos'>$row[4]</td>";
                                    echo "<td style='background-color:transparent;'>
                                        <a  href='registrar_GF_FACTURA.php?factura=".md5($row[5])."' title='Ver Factura' style='padding-left: 76px;' target='_blank'>
                                            <li class='glyphicon glyphicon-eye-open'></li>
                                        </a>
                                    </td>";
                                    echo "<td style='background-color:transparent;'>
                                        <a  href='registrar_GF_RECAUDO_FACTURACION_2.php?recaudo=".md5($row[7])."' title='Ver Recaudo' style='padding-left: 76px;'target='_blank'>
                                            <li class='glyphicon glyphicon-eye-open'></li>
                                        </a>
                                    </td>";
                                    echo "</tr>";
                                }
                            }
                        
                            ?>   
                        </tbody>
                    </table>

                </div>
                
            </div>
            
        </div>        
    </div>
<div class="modal fade" id="mdleliminar" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Está seguro que desea eliminar la información?</p>
            </div>
            <div id="forma-modal" class="modal-footer">                
                <button type="button" id="brnconfirmdel" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" id="brncanceldel" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
            </div>
        </div>
    </div>
</div>
    <div class="modal fade" id="mdlinfo" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p id="pinfo"></p>
            </div>
            <div id="forma-modal" class="modal-footer">                
                <button type="button" id="brnconfirm" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
    <?php require_once 'footer.php'; ?>
    <script src="js/script_modal.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/php-date-formatter.min.js"></script>
    <script src="js/jquery.datetimepicker.js"></script>
    <script src="js/script_date.js"></script>
    <script src="js/script_table.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_validation.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="js/select/select2.full.js"></script>
    <script src="js/script.js"></script>    
    <script>
        
        function eliminar(id){
            $("#mdleliminar").modal("show");
            $("#brnconfirmdel").click(function(){
                var form_data = {
                id: id,
                action: 1
                };            
                $.ajax({
                    type: 'POST',
                    url: "json/ingresosJson.php",
                    data: form_data,
                    success: function (data) {
                        if (data == 1){
                            $("#pinfo").html("Información eliminada correctamente");
                            $("#mdlinfo").modal("show");
                            $("#brnconfirm").click(function(){
                                location.reload();
                            });
                        }else{
                            $("#pinfo").html("No se ha pidido eliminar la información");
                            $("#mdlinfo").modal("show");
                            $("#brnconfirm").click(function(){
                                location.reload();
                            });
                        }
                    }
                });
            });            
        }
        
        $("#stlingresos").change(function(){
            let tp = $(this).val();
            window.location.href = "listar_INGRESOS_FACTURA.php?tipo="+tp;
        });
        $(document).ready(function () {
            $(".select2_single").select2({
                allowClear: true
            });            
        });
        
        $(document).ready(function () {
        var i = 1;
        $('#tabla212 thead th').each(function () {
            if (i != 1) {
                var title = $(this).text();
                switch (i) {
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
                    case 15:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 16:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 17:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 18:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 19:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                }
                i = i + 1;
            } else {
                i = i + 1;
            }
        });
        // DataTable
        var table = $('#tabla212').DataTable({
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
</body>
</html>
