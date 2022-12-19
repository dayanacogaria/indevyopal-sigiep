<?php
require './Conexion/conexion.php';
require './head.php';
?>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<link rel="stylesheet" href="css/jquery.datetimepicker.css">
<link rel="stylesheet" href="css/desing.css">
<title>Conceptos Plan Inventario</title>
<style>
    .cabeza{
        font-weight: 700;
    }

    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px;}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px; font-size: 10px; font-family: Arial;}
    .campos{padding: 0px;font-size: 10px;}

    #tblLsPro{
        display: block;
        overflow-y: auto;
        overflow-x: auto;
        height: 160px;
    }

    #tblLsPro>thead>tr>th{
        width: 100%;
    }
</style>
</head>
<body>
<div class="container-fluid">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 col-md-10 col-lg-10">
            <h2 class="tituloform" align="center" style="margin-top: 0px">Conceptos Plan Inventario</h2>
            <a href="modificar_GF_PLAN_INVENTARIO.php?id_plan_inv=<?php echo $_GET['plan'] ?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none;" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin: 5px 4px 10px;background-color: #0e315a; color: #ffffff; border-radius: 5px;"><?php echo ucwords((strtolower($info[0])));?></h5>
            <?php 
            echo "<input type='hidden' name='txtConcepto' id='txtConcepto' value='$xda'><input type='hidden' name='txtPlanI' id='txtPlanI' value='$info[1]'>";
            ?>
            <div class="table-responsive">
                <table id="tablaX" class="table table-striped table-condensed display detalle" cellpadding="0" width="100%">
                    <thead>
                    <tr>
                        <td class="cabeza oculto"></td>
                        <td class="cabeza" width="5%"></td>
                        <td class="cabeza">CLASE PRECIO</td>
                        <td class="cabeza">VALOR</td>
                        <td class="cabeza">% IVA</td>
                        <td class="cabeza">% IMPOCONSUMO</td>
                        <td class="cabeza">UNIDAD EMPAQUE</td>
                        <td class="cabeza">FACTOR CONVERSIÓN</td>
                        <td class="cabeza">% UTILIDAD</td>
                        <td class="cabeza" width="7%"></td>
                    </tr>
                    <tr>
                        <th class="cabeza oculto"></th>
                        <th class="cabeza"></th>
                        <th class="cabeza"></th>
                        <th class="cabeza"></th>
                        <th class="cabeza"></th>
                        <th class="cabeza"></th>
                        <th class="cabeza"></th>
                        <th class="cabeza"></th>
                        <th class="cabeza"></th>
                        <th class="cabeza" width="7%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $html  = "";
                    $html .= "\n<tr>";
                    $html .= "\n\t<td class='oculto'></td>";
                    $html .= "\n\t<td></td>";
                    $html .= "\n\t<td>";
                    $html .= "\n\t\t<select class='form-control select2' placeholder='Clase Precio' name='sltTipoT' id='sltTipoT' title='Seleccione clase precio'>";
                    $html .= "\n\t\t\t<option value=''></option>";
                    foreach ($tipo as $row){
                        $html .= "\n\t\t\t<option value='$row[0]'>$row[1]</option>";
                    }
                    $html .= "\n\t\t</select>";
                    $html .= "\n\t</td>";
                    $html .= "\n\t<td><input type='text' name='txtValor' id='txtValor' class='form-control' placeholder='Valor' style='width: 100%; font-size: 10px;' autocomplete='off'></td>";
                    $html .= "\n\t<td><input type='text' name='txtIva' id='txtIva' class='form-control' placeholder='Iva' style='width: 100%; font-size: 10px;' value='0' autocomplete='off'></td>";
                    $html .= "\n\t<td><input type='text' name='txtImpo' id='txtImpo' class='form-control' placeholder='Impoconsumo' style='width: 100%; font-size: 10px;' value='0' autocomplete='off'></td>";
                    $html .= "\n\t<td>";
                    $html .= "\n\t\t<select class='form-control select2' placeholder='Unidad de Empaque' name='sltUnidad' id='sltUnidad' title='Seleccione unidad de empaque'>";
                    $html .= "\n\t\t\t<option value=''></option>";
                    foreach ($xuni as $row) {
                        $html .= "\n\t\t\t<option value='$row[0]'>$row[1]</option>";
                    }
                    $html .= "\n\t\t</select>";
                    $html .= "\n\t</td>";
                    $html .= "\n\t<td><input type='text' name='txtFactor' id='txtFactor' class='form-control' placeholder='Factor Conversión' style='width: 100%; font-size: 10px;' value='0' autocomplete='off'></td>";
                    $html .= "\n\t<td><input type='text' name='txtPorcentajeI' id='txtPorcentajeI' class='form-control' placeholder='%' style='width: 100%; font-size: 10px;' value='0' autocomplete='off'></td>";
                    $html .= "\n\t<td class='text-center'><a id='btnGuardar' class='glyphicon glyphicon-floppy-disk'></a></td>";
                    $html .= "\n</tr>";
                    while ($row = mysqli_fetch_row($data)){
                        $html .= "\n<tr>";
                        $html .= "\n\t<td class='oculto'></td>";
                        $html .= "\n\t<td></td>";
                        $html .= "\n\t<td style='font-size: 10px;'>$row[6]</td>";
                        $html .= "\n\t<td style='text-align: right;'><span id='lblValor$row[3]' style='font-size: 10px;'>".number_format($row[2], 2)."</span></td>";
                        $html .= "\n\t<td style='text-align: right;'><span id='lblIva$row[3]' style='font-size: 10px;'>".number_format($row[4], 2)."</span></td>";
                        $html .= "\n\t<td style='text-align: right;'><span id='lblImpo$row[3]' style='font-size: 10px;'>".number_format($row[5], 2)."</span></td>";
                        $html .= "\n\t<input type='hidden' name='txtIdUnidad$row[3]' id='txtUnidadId$row[3]' value='$row[7]' />";
                        $html .= "\n\t<td style='font-size: 10px;'><span id='lblUnidad$row[3]' style='font-size: 10px;'>$row[8]</span></td>";
                        $html .= "\n\t<td style='text-align: right;'><span id='lblFactor$row[3]' style='font-size: 10px;'>".$row[9]."</span></td>";
                        $html .= "\n\t<td style='text-align: right;'><span id='lblPorcentaje$row[3]' style='font-size: 10px;'>".$row[11]."</span></td>";
                        $html .= "\n\t<td class='text-center'>";
                        $html .= "\n\t\t<a class='glyphicon glyphicon-edit' onclick='javascript:cambiarCampos($row[3])' id='btn$row[3]'></a>";
                        $html .= "\n\t\t<a class='glyphicon glyphicon-trash' href='javascript:eliminar($row[10])' id='btn$row[3]'></a>";
                        $html .= "\n\t\t<a class='glyphicon glyphicon-sort-by-alphabet' id='link$row[10]' onclick='enviarX($row[10])' data-toggle='modal' data-target='#mdlProductos' data-tarifa='$row[10]'></a>";
                        $html .= "\n\t</td>";
                        $html .= "\n</tr>";
                    }
                    echo $html;
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        require_once 'footer.php';
        require_once './modales.php';
        require_once './vistas/inventario/producto.modal.php';
        ?>
    </div>
</div>
<script src="js/jquery-ui.js"></script>
<script src="js/php-date-formatter.min.js"></script>
<script src="js/jquery.datetimepicker.js"></script>
<script src="js/script_date.js"></script>
<script src="js/script_table.js"></script>
<script src="dist/jquery.validate.js"></script>
<script src="js/script_validation.js"></script>
<script type="text/javascript" src="js/select2.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.1.0/bootstrap-modal.pack.js"></script>
<script type="text/javascript" charset="utf-8">
    $(".select2").select2();

    $(document).ready(function () {
        var i = 1;
        $('#tablaX thead th').each(function (){
            if (i !== 1){
                let title = $(this).text();
                switch (i){
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
                }
                i = i + 1;
            } else {
                i = i + 1;
            }
        });

        var table = $('#tablaX').DataTable({
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
            let that = this;
            if (i !== 0) {
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

    $("#btnGuardar").click(function () {
        let tipo    = $("#sltTipoT").val();
        let valor   = $("#txtValor").val();
        let iva     = $("#txtIva").val();
        let impo    = $("#txtImpo").val();
        let conpto  = $("#txtConcepto").val();
        let xuni    = $("#sltUnidad").val();
        let xfactor = $("#txtFactor").val();
        let xPor    = $("#txtPorcentajeI").val();

        $.post("access.php?controller=ConceptoTarifa&action=GuardarDataTarifa",
            {
                tipo: tipo, txtConcepto: conpto, valor: valor, iva: iva, impo: impo, unidad: xuni,
                factor: xfactor, porcentaje: xPor
            },
            function (data) {
            let res = JSON.parse(data);
            if(res === true){
                $("#mdlSave").modal("show");
                $("#btnSave").click(function(){
                    window.location.reload();
                });
            }else{
                $("#mdlNotSave").modal("show");
            }
        });
    });

    function eliminar($x){
        $("#mdlConfirmarDel").modal("show");
        $("#btnDel").click(function () {
            $.post("access.php?controller=ConceptoTarifa&action=Eliminar", { concepto: $x }, function (data) {
                let res = JSON.parse(data);
                if(res === true){
                    $("#mdlAceptarDel").modal("show");
                    $("#btnAcepts").click(function () {
                        window.location.reload();
                    });
                }else{
                    $("#mdlNoConf").modal("show");
                }
            });
        });
    }

    function cambiarCampos($x){
        let valor = $("#lblValor"+$x).text();
        let iva   = $("#lblIva"+$x).text();
        let impo  = $("#lblImpo"+$x).text();
        let xuni  = $("#txtUnidadId"+$x).val();
        let xfat  = $("#lblFactor"+$x).text();
        let xPor  = $("#lblPorcentaje"+$x).text();
        let id_p  = $("#txtPlanI").val();
        $("#lblValor"+$x).replaceWith("<input type='text' value='"+valor+"' id='txtValor"+$x+"' name='txtValor"+$x+"' class='form-control' style='font-size: 10px;' placeholder='Valor'>")
        $("#lblIva"+$x).replaceWith("<input type='text' value='"+iva+"' id='txtIva"+$x+"' name='txtIva"+$x+"' class='form-control' style='font-size: 10px;' placeholder='Iva'>");
        $("#lblImpo"+$x).replaceWith("<input type='text' value='"+impo+"' id='txtImpo"+$x+"' name='txtImpo"+$x+"' class='form-control' style='font-size: 10px;' placeholder='Impoconsumo'>");
        $("#lblUnidad"+$x).replaceWith("<select name='sltXUnidad"+$x+"' id='sltXUnidad"+$x+"' class='form-control select2' placeholder='Unidad de Empaque'><select>");
        $("#lblFactor"+$x).replaceWith("<input type='text' value='"+xfat+"' id='txtFactor"+$x+"' name='txtFactor"+$x+"' class='form-control' style='font-size: 10px;' placeholder='Factor'>");
        $("#lblPorcentaje"+$x).replaceWith("<input type='text' value='"+xPor+"' id='txtPorcentajeI"+$x+"' name='txtPorcentajeI"+$x+"' class='form-control' style='font-size: 10px;' placeholder='%'>");
        $("#btn"+$x).replaceWith("<a class='glyphicon glyphicon-floppy-disk' id='btnS"+$x+"' title='Guardar cambios' href='javascript:guardarCambios("+$x+")'></a> <a class='glyphicon glyphicon-remove' href='javascript:removerCampos("+$x+")' id='btnR"+$x+"' title='Remover campos'></a>");
        $.get("access.php?controller=Punto&action=obtenerCantidadElementos", { elemento : id_p }, function (data) {
            $("#sltXUnidad"+$x).html(data);
            console.log(id_p);
            $("#sltXUnidad"+$x).select2({
                containerCssClass : "show-hide"
            });
            $("#sltXUnidad"+$x).val(xuni);
            $("#sltXUnidad"+$x).trigger("change");
        });
    }

    function removerCampos($x) {
        let valor = $("#txtValor"+$x).val();
        let iva   = $("#txtIva"+$x).val();
        let impo  = $("#txtImpo"+$x).val();
        let xuni  = $("#sltXUnidad"+$x).val();
        let xfat  = $("#txtFactor"+$x).val();
        let xPor  = $("#txtPorcentajeI"+$x).val();

        $("#txtValor"+$x).replaceWith("<span id='lblValor"+$x+"' style='font-size: 10px;'>"+valor+"</span>")
        $("#txtIva"+$x).replaceWith("<span id='lblIva"+$x+"' style='font-size: 10px;'>"+iva+"</span>");
        $("#txtImpo"+$x).replaceWith("<span id='lblImpo"+$x+"' style='font-size: 10px;'>"+impo+"</span>");
        $("#sltXUnidad"+$x).replaceWith("<span id='lblUnidad"+$x+"' style='font-size: 10px;'>"+xuni+"</span>");
        $("#txtFactor"+$x).replaceWith("<span id='lblFactor"+$x+"' style='font-size: 10px;'>"+xfat+"</span>");
        $("#txtPorcentajeI"+$x).replaceWith("<span id='lblPorcentaje"+$x+"' style='font-size: 10px;'>"+xPor+"</span>");
        $("#btnS"+$x).replaceWith("<a class='glyphicon glyphicon-edit' href='javascript:cambiarCampos("+$x+")' id='btn"+$x+"'></a>");
        $("#btnR"+$x).css("display", "none");

        $("#sltXUnidad"+$x).select2("container").hide();
        $("#sltXUnidad"+$x).css('display', 'none');
        $(".show-hide").hide();
    }

    function guardarCambios($x) {
        let valor    = $("#txtValor"+$x).val();
        let iva      = $("#txtIva"+$x).val();
        let impo     = $("#txtImpo"+$x).val();
        let concepto = $("#txtConcepto").val();
        let xunidad  = $("#txtUnidadId"+$x).val();
        let unidad   = $("#sltXUnidad"+$x).val();
        let factor   = $("#txtFactor"+$x).val();
        let xPor     = $("#txtPorcentajeI"+$x).val();
        $.post("access.php?controller=ConceptoTarifa&action=ModificarTarifa",
            { id : $x, valor : valor, iva : iva, impo: impo, concepto : concepto, xunidad : xunidad, unidad : unidad,
                factor: factor, porcentaje: xPor
            },
            function (data) {
                let res = JSON.parse(data);
                if(res.res === true){
                    $("#mdlSave").modal("show");
                }
            });
    }

    $("#btnSave").click(function () {
        window.location.reload();
    });

    function enviarX($x){
        let tar = $("#link" + $x).data('tarifa');
        let elm = $("#txtPlanI").val();
        let modal = $("#mdlProductos");
        modal.find(".modal-body>.client-form>#txtTarifa").val(tar);
        $.post("access.php?controller=Inventario&action=obtenerListadoD", { elemento:elm }, function (data) {
            modal.find(".modal-body>.client-form>.row>.form-group>.col-sm-4.col-md-4.col-lg-4>#sltElementos").html(data).trigger("change");
        });

        $.post("access.php?controller=Inventario&action=obtenerDataAsociados", { elemento:elm, tarifa:tar }, function (data) {
            modal.find(".modal-body>.form-group>.table-responsive>#tblLsPro>tbody").html(data);
        });
    }

    $("#btnRegistroElementos").click(function (e) {
        let xSelecionados = '';
        let elemento      = $("#txtPlanI").val();
        let tarifa        = $("#txtTarifa").val();
        let cantidad      = $("#txtCantidad").val();
        let modal         = $("#mdlProductos");
        $("#sltElementos option:selected").each(function () {
            xSelecionados += $(this).val() + ',';
        });
        xSelecionados = xSelecionados.substr(0, xSelecionados.length - 1);
        $.post("access.php?controller=Inventario&action=registroDataHerencia",
            { padre: elemento, hijos: xSelecionados, tarifa: tarifa, cantidad: cantidad }
            , function (data) {
            let res = JSON.parse(data);
            if(res.data > 0){
                $.post("access.php?controller=Inventario&action=obtenerDataAsociados",
                    { elemento: elemento, tarifa:tarifa }
                    , function (data) {
                    modal.find(".modal-body>.form-group>.table-responsive>#tblLsPro>tbody").html(data);
                });
            }
        });
    });

    function eliminarHerencia($x) {
        let modal    = $("#mdlProductos");
        let elemento = $("#txtPlanI").val();
        let tar      = $("#linkD" + $x).data('tarifa');
        $.post("access.php?controller=Inventario&action=eliminarRelacion", { id:$x }, function (data) {
            let res = JSON.parse(data);
            if(res.data === true){
                $.post("access.php?controller=Inventario&action=obtenerDataAsociados", { elemento: elemento, tarifa:tar }, function (data) {
                    modal.find(".modal-body>.form-group>.table-responsive>#tblLsPro>tbody").html(data);
                });
            }
        });
    }
</script>
</body>
</html>