<?php
require './Conexion/conexion.php';
require './head.php';
$plan_inv = $this->alm->obtenerPlanInventario($_SESSION['compania']);
?>
    <title>Porcentaje Incremento</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/jquery.datetimepicker.css">
    <link rel="stylesheet" href="css/desing.css">
    <style>
        .client-form input[type='text'], input[type='number']{
            width:  100%;
            font-size: 11px !important;
        }

        .client-form textarea{
            width: 100%;
            height: 34px;
        }

        form, input{
            font-family: Arial;
            font-size: 11px !important;
        }

        input:read-only{
            background-color: #fff !important;
        }

        table.dataTable thead th,table.dataTable thead td{padding:1px 18px; font-size:10px;}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px;}
        .dataTables_wrapper .ui-toolbar{padding:2px;}
    </style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require './menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left">
                <h2 align="center" style="margin-top:-2px;" class="tituloform">Porcentaje Incremento</h2>
                <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-7px; border:4px solid #020324; border-radius: 10px;">
                    <div class="client-form">
                        <form id="form" name="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="<?php echo "access.php?controller=Inventario&action=listadoProductos"; ?>">
                            <p align="center" class="parrafoO" style="margin-bottom: -0.00005em;">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <div class="form-group">
                                <label for="sltProducto" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Productos:</label>
                                <div class="col-sm-9 col-md-9 col-lg-9">
                                    <select name="sltProducto" id="sltProducto" class="select form-control" multiple data-placeholder="Productos">
                                        <?php
                                        $html = "<option value=''></option>";
                                        foreach ($plan_inv as $row){
                                            $html .= "<option value='$row[0]'>$row[1]</option>";
                                        }
                                        echo $html;
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-1 col-md-1 col-lg-1">
                                    <a class="btn btn-primary borde-sombra" id="btnEnviar"><span class="glyphicon glyphicon-send"></span></a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 10px;">
                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <td class="oculto">UNIDAD</td>
                            <td style="font-weight: 600;">CODIGO</td>
                            <td style="font-weight: 600;">NOMBRE</td>
                            <td style="font-weight: 600;">UNIDAD</td>
                            <td style="font-weight: 600;">PORCENTAJE INCREMENTO</td>
                        </tr>
                        <tr>
                            <th class="oculto"></th>
                            <th>CODIGO</th>
                            <th>NOMBRe</th>
                            <th>UNIDAD</th>
                            <th>PORCENTAJE INCREMENTO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(!empty($_REQUEST['productos'])){
                            $html = "";
                            $data = $this->alm->obtenerUnidadesElemento($_REQUEST['productos']);
                            foreach ($data as $row){
                                $html .= "<tr>";
                                $html .= "<td class='oculto'></td>";
                                $html .= "<td class='text-left'>$row[1]</td>";
                                $html .= "<td class='text-left'>$row[2]</td>";
                                $html .= "<td class='text-left'>$row[3]</td>";
                                $html .= "<td><input type='text' id='txtPorcentajeI$row[0]' name='txtPorcentajeI$row[0]' class='form-control' value='$row[4]' title='Ingrese porcentaje de incremento' placeholder='Porcentaje Incremento' onblur='actualizarData($row[0])' /></td>";
                                $html .= "</tr>";
                            }
                            echo $html;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php require './footer.php'; ?>
        </div>
    </div>
    <script src="js/jquery-ui.js"></script>
    <script src="js/php-date-formatter.min.js"></script>
    <script src="js/jquery.datetimepicker.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_date.js"></script>
    <script src="js/script_validation.js"></script>
    <script src="js/script.js"></script>
    <script>
        $("#btnEnviar").click(function (e) {
            var producto = $("#sltProducto").val();
            let ids      = "";
            Object.keys(producto).forEach(function (key) {
                ids += producto[key] + ",";
            });
            ids = ids.substr(0, ids.length - 1);
            window.location = "access.php?controller=Inventario&action=listadoProductos&productos="+ids;
        });

        $(document).ready(function() {
            var i= 1;
            $('#tabla thead th').each( function () {
                if(i != 0){
                    var title = $(this).text();
                    switch (i){
                        case 2:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 3:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 4:
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
                    'className': 'dt-body-left'
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
        
        function actualizarData($x) {
            let porcentaje = $("#txtPorcentajeI"+$x).val();
            $.get("access.php?controller=Inventario&action=ActualizarPorcentajeIncremento", { porcentaje : porcentaje, id:$x }, function (e) {});
        }
    </script>
</body>
</html>
