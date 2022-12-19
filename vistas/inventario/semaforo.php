<?php
require './Conexion/conexion.php';
require './head.php';
?>
    <title>Cambio de Precios</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery.datetimepicker.css">
    <link rel="stylesheet" href="css/desing.css">
<style>
        table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px;}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px;}
        .dataTables_wrapper .ui-toolbar{padding:2px;}

        body{
            font-family: Arial;
            font-size: 10px;
        }

        .valorLabel{
            font-size: 10px;
            white-space:nowrap;
        }

        .valorLabel:hover{
            cursor: pointer;
            color: #1155cc;
        }

        .campos{
            padding: 0px;
            font-size: 10px;
        }

        .cabeza{
            white-space: nowrap;
            padding: 20px;
        }
        .campos{
            padding: -20px;
        }

        .client-form input[type="text"]{
            width: 100%;
        }

        .client-form textarea{
            width: 100%;
            height: 34px;
        }

        #form>.form-group{
            margin-bottom: 0 !important;
        }

        .bajo{
            background-color: rgba(196,40,32,0.94) !important;
            color: #fff !important;
        }

        .subio{
            background-color: rgba(60,192,44,0.94) !important;
            color: #fff;
        }

        .igual{
            background-color: rgba(40,92,192,0.94) !important;
            color: #fff !important;
        }

        .bajo>td.campos>a>span,
        .subio>td.campos>a>span,
        .igual>td.campos>a>span{
            color: #fff !important;
        }

        .sorting_1{
            color: #000 !important;
        }

        td.campos.sorting_1>a>span{
            color: #23527c !important;
        }
    </style>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require './menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left">
                <h2 align="center" style="
                margin-top: -2px;" class="tituloform">Listado de Cambio de Precios</h2>
                <div style="margin-top: -7px; border:4px solid #020324; border-radius: 10px;" class="client-form col-sm-12 col-lg-12 col-md-12">
                    <form id="form" name="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="<?php echo "access.php?controller=Almacen&action=vistaCambioPrecios"; ?>">
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em;">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="txtFechaI" id="txtFechaI" class="form-control fecha" placeholder="Fecha Inicial" title="Fecha Inicial" required>
                            </div>
                            <label for="" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Fecha Final:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="txtFechaF" id="txtFechaF" class="form-control fecha" placeholder="Fecha Final" title="Fecha Final" required>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1 text-right">
                                <button type="submit" class="btn btn-primary borde-sombra" title="Buscar o consultar registros"><span class="glyphicon glyphicon-send"></span></button>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1 text-left">
                                <a onclick="window.location = 'access.php?controller=Almacen&action=vistaCambioPrecios';" class="btn btn-primary borde-sombra" title="Ver listado inicial de cambio de precios"><span class="glyphicon glyphicon-plus"></span></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left" style="margin-top: 5px;">
                <input type="hidden" id="idPrevio" value="">
                <input type="hidden" id="idActual" value="">
                <table id="tabla" class="table table-striped table-condensed table-bordered display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <td class="oculto"></td>
                            <td class="cabeza">Fecha</td>
                            <td class="cabeza">Producto</td>
                            <td class="cabeza">Nombre</td>
                            <td class="cabeza">Unidad</td>
                            <td class="cabeza">Precio Actual</td>
                            <td class="cabeza">Precio Anterior</td>
                            <td class="cabeza">Precio Compra</td>
                            <td class="cabeza" style="width: 5%;"></td>
                        </tr>
                        <tr>
                            <th class="oculto"></th>
                            <th class="cabeza"></th>
                            <th class="cabeza"></th>
                            <th class="cabeza"></th>
                            <th class="cabeza"></th>
                            <th class="cabeza"></th>
                            <th class="cabeza"></th>
                            <th class="cabeza"></th>
                            <th class="cabeza"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(count($data) > 0){
                            $html = "";
                            foreach ($data as $row){
                                $xxx = $this->fat->obtenerValorAnterior($row[11], $row[0], $row[13]);
                                $xvl = $this->fat->obtenerValorConversionUnidadElemento($row[13], $row[11]);
                                if(count($xxx)){
                                    if($row[7] > $xxx[0]){
                                        $html .= "<tr class='subio'>";
                                    }

                                    if($row[7] < $xxx[0]){
                                        $html .= "<tr class='bajo'>";
                                    }

                                    if($row[7] == $xxx[0]){
                                        $html .= "<tr class='igual'>";
                                    }

                                }else{
                                    $html .= "<tr>";
                                }

                                $html .= "<td class='oculto'></td>";
                                $html .= "<td class='campos'>$row[1]</td>";
                                $html .= "<td class='campos text-right'>$row[2]</td>";
                                $html .= "<td class='campos'>$row[3]</td>";
                                $html .= "<td class='campos'>$row[4]</td>";
                                $html .= "<td class='campos text-right'>";
                                $html .= "<span id='lblValor$row[0]'>".number_format($row[5], 0)."</span>";
                                $html .= "<input type='text' value='$row[5]' class='form-control campos oculto' name='txtValorAct$row[0]' id='txtValorAct$row[0]'/>";
                                $html .= "<input type='hidden' value='$row[12]' id='txtTarifa$row[0]' />";
                                $html .= "</td>";
                                $html .= "<td class='campos text-right'>".number_format($row[6], 0)."</td>";
                                $html .= "<td class='campos text-right'>".number_format($row[7] * $xvl, 0)."</td>";
                                $html .= "<td class='campos text-center'>";
                                if($row[8] == 1){
                                    $html .= "<a id='btnX$row[0]' style='cursor: pointer;' onclick='aceptarValor($row[0])' title='Aceptar Valor'><span class='glyphicon glyphicon-check'></span></a>";
                                    $html .= "<a id='btnG$row[0]' style='cursor: pointer;' onclick='cambiarCampos($row[0])' title='Modificar'><span class='glyphicon glyphicon-edit'></span></a>";
                                }
                                $html .= "</td>";
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
    <script src="js/script_date.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            let i = 1;
            $('#tabla thead th').each( function () {
                if(i !== 0){
                    switch (i){
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
                        case 8:
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
                "pageLength": 10,
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
            i = 0;
            table.columns().every( function () {
                var that = this;
                if(i != 0){
                    $('input', this.header()).on( 'keyup change', function () {
                        if (that.search() !== this.value){
                            that
                                .search(this.value)
                                .draw();
                        }
                    });
                    i = i+1;
                }else{
                    i = i+1;
                }
            });
        });

        function cambiarCampos($x) {
            if( $("#idPrevio").val() !== 0 || $("#idPrevio").val() !== "" ){
                $("#lblValor"+$("#idPrevio").val()).css('display', 'block');
                $("#txtValorAct"+$("#idPrevio").val()).css('display', 'none');
                $("#btnR"+$("#idPrevio").val()).remove();
                $("#btnX"+$("#idPrevio").val()).css('display', 'block');
                $("#btnS"+$("#idPrevio").val()).replaceWith("<a id='btnG"+$("#idPrevio").val()+"' title='Modificar' onclick='cambiarCampos("+$("#idPrevio").val()+")'><span class='glyphicon glyphicon-edit'></span></a>");
            }

            $("#lblValor"+$x).css('display', 'none');
            $("#txtValorAct"+$x).css('display', 'block');
            $("#btnX"+$x).css('display', 'none');
            $("#btnG"+$x).replaceWith("<a class='btns"+$x+"' id='btnS"+$x+"' style='cursor: pointer;' title='Guardar Cambios' onclick='guardarCambios("+$x+")'><span class='glyphicon glyphicon-floppy-disk'></span></a> <a class='btns"+$x+"' id='btnR"+$x+"' style='cursor: pointer;' title='Remover Campos' onclick='removerCampos()'><span class='glyphicon glyphicon-floppy-remove'></span></a>");

            $("#idActual").val($x);

            if($("#idPrevio").val() !== $x){
                $("#idPrevio").val($x);
            }
        }

        function aceptarValor($x) {
            $.get("access.php?controller=Factura&action=CambioEstadoPrecio",
                { id: $x, estado:2 },
                function (data) {
                    let res = JSON.parse(data);
                    if(res.res = true){
                        window.location.reload();
                    }
                }
            );
        }

        function removerCampos() {
            window.location.reload();
        }

        function guardarCambios($x) {
            let tarifa = $("#txtTarifa" + $x).val();
            let valor  = $("#txtValorAct"+ $x).val();

            $.get("access.php?controller=Factura&action=actualizarDatosPrecio",
                { id_precio: $x, precio: valor, estado: 3, id_tarifa:tarifa},
                function (data) {
                    let res = JSON.parse(data);
                    if(res.res = true){
                        window.location.reload();
                    }
                }
            );
        }
    </script>
    <script src="js/script_validation.js"></script>
</body>
</html>
