<?php
require './Conexion/conexion.php';
require_once './head.php';
?>
    <title>Espacios Habitables</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/jquery.datetimepicker.css">
    <link rel="stylesheet" href="css/desing.css">
    <style>
        .cabeza{
            font-weight: 700;
        }
    </style>
<body>
<div class="container-fluid">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 col-md-10 col-lg-10">
            <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Espacios Habitables</h2>
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                <table id="tablaX" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <td class="cabeza" style="width: 7%;"></td>
                        <td class="cabeza">Tipo Espacio</td>
                        <td class="cabeza">Codigo</td>
                        <td class="cabeza">Descripción</td>
                        <td class="cabeza">Dependencia</td>
                        <td class="cabeza">Predecesor</td>
                        <td class="cabeza">Estado</td>
                        <td class="cabeza">Ruta</td>
                        
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $html = "";
                    while($row = mysqli_fetch_row($data)) {
                        $html .= "\n\t<tr>";
                        $html .= "\n\t\t<td style='width: 8%;'>";
                        $html .= "\n\t\t\t<a href='javascript:eliminar(\"access.php?controller=EspacioHabitable&action=Eliminar&id=$row[0]\")' class='glyphicon glyphicon-trash' title='Eliminar'></a>";
                        $html .= "\n\t\t\t<a href='access.php?controller=EspacioHabitable&action=Actualizar&id=".md5($row[0])."' class='glyphicon glyphicon-pencil' title='Modificar'></a>";
                        $html .= "\n\t\t\t<a href='#' data-id='".md5($row[0])."' data-codigo='$row[2]' class='glyphicon glyphicon-th-list' title='Características de Espacio' data-toggle='modal' data-target='#mdlCaracteristica'></a>";
                        $html .= "\n\t\t\t<a href='access.php?controller=EspacioHabitable&action=novedadEspacio&id=".md5($row[0])."' class='glyphicon glyphicon-bell' title='Novedad Espacio' target='_blank'></a>";
                        $html .= "\n\t\t</td>";
                        $html .= "\n\t\t<td>$row[1]</td>";
                        $html .= "\n\t\t<td>$row[2]</td>";
                        $html .= "\n\t\t<td>$row[3]</td>";
                        $html .= "\n\t\t<td>$row[4]</td>";
                        $html .= "\n\t\t<td>$row[6]</td>";
                        if($row[8]==1){$estado = 'Activo';} else {$estado = 'Inactivo';}
                        $html .= "\n\t\t<td>$estado</td>";
                        if(!empty($row[7])){
                            $html .= "\n\t\t<td><a class='link' href='javascript:vervideo2(\"" . $row[7] . "\")' target='_blank'><span class='glyphicon glyphicon-download-alt'></span></a></td>";
                        }else{
                            $html .= "\n\t\t<td></td>";
                        }
                        $html .= "\n\t</tr>";
                    }
                    echo $html;
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 10px">
            <div class="col-sm-push-10 col-md-push-10 col-lg-push-10 col-sm-2 col-md-2 col-lg-2">
                <a href="access.php?controller=EspacioHabitable&action=Registrar" class="btn btn-primary col-sm-2 borde-sombra" style="color: #fff;border-color: #1075C1; width: 100%">Registrar Nuevo <span class="glyphicon glyphicon-plus"></a>
            </div>
        </div>
    </div>
</div>
<div id="response"></div>
<?php require_once 'footer.php'; ?>
<script src="js/script_modal.js" type="text/javascript" charset="utf-8"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/php-date-formatter.min.js"></script>
<script src="js/jquery.datetimepicker.js"></script>
<script src="js/script_date.js"></script>
<script src="dist/jquery.validate.js"></script>
<script src="js/script_validation.js"></script>
<script type="text/javascript" src="js/select2.js"></script>
<script src="js/script.js"></script>
</body>
</head>
    <script type="text/javascript" charset="utf-8">
        $(document).ready(function () {
            var i = 1;
            $('#tablaX thead th').each(function (){
                if (i != 1){
                    var title = $(this).text();
                    switch (i){
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
    <?php require_once 'modales.php'; ?>
    <?php require_once './vistas/espacioHabitable/caracteristicas.modal.php'; ?>
    <?php require_once './vistas/espacioHabitable/video.modal.php'; ?>
    <script type="text/javascript">
        var i = 1;
        $("#btnAdd").click(function () {
            let x    = (i++);
            var element = $(".clone-data").clone().appendTo("#campos").attr('class', 'clone-data' + x).addClass('form-group');
            element.children().find("#sltTipoDato").attr('id', 'sltTipoDato' + x).attr('name', 'sltTipoDato' + x).attr('onchange', 'return cambiarCampoValor('+x+')');
            element.children().find("#txtNombre").attr('id', 'txtNombre' + x).val('').attr('name', 'txtNombre' + x);
            element.children().find("#txtValor").attr('id', 'txtValor' + x).val('').attr('name', 'txtValor' + x);
            element.children().find(".btn").css('display', 'none');
            $(".modal-body #txtContador").val(x);
        });

        $("#chkTodos").click(function () {
            var chk = $(this);
            if(chk.is(':checked')){
                $("#sltEspacios").attr('disabled', true);
                $("label[for='sltEspacios']").addClass('text-muted');
                chk.val(1);
            }else{
                $("#sltEspacios").attr('disabled', false);
                $("label[for='sltEspacios']").removeClass('text-muted');
                chk.val(0);
            }
        });

        $("#btnGuardarCar").click(function (e) {
            e.preventDefault();
            var todos = $("#chkTodos").val();
            var space = $("#sltEspacios").val();
            var id    = $("#txtEspacio").val();
            var data  = [
                { "tipo" : $("#sltTipoDato").val(), "nombre" : $("#txtNombre").val(), "valor" : $("#txtValor").val() }
            ];
            var x     = $("#txtContador").val();
            if(x > 0){
                for (var y = 1; y <= x; y++){
                    data.push({ "tipo" : $("#sltTipoDato" + y).val(), "nombre" : $("#txtNombre" + y).val(), "valor" : $("#txtValor" + y).val()});
                }
            }

            $.post("access.php?controller=EspacioHabitable&action=GuardarCaracteristicas",
                { espacio : id, data : data, espacios : space, xTodos : todos, contador : x },
                function (data) {
                    solicitudCargaEspacios(id);
                }
            );
        });

        function solicitudCargaEspacios(id) {
            $.post("access.php?controller=EspacioHabitable&action=obtenerCaracteristicasEspacios", { espacio: id }, function (data) {
                $('.modal-body #html').html(data);
            });
        }

        function cambiarCampoValor(x){
            if(x){
                var tipo = $("#sltTipoDato" + x).val();
            }else{
                var tipo = $("#sltTipoDato").val()
            }
            var html = "";
            switch (tipo){
                case "1":
                    if(x){
                        html += "<input type='text' class='form-control' name='txtValor"+x+"' id='txtValor"+x+"' style='width: 100%; font-size: 10px;' title='Valor de Característica' placeholder='Valor' autocomplete='off' onkeyup='return txtValida(event, "+'"car"'+" )' required>";
                    }else{
                        html += "<input type='text' class='form-control' name='txtValor' id='txtValor' style='width: 100%; font-size: 10px;' title='Valor de Característica' placeholder='Valor' autocomplete='off' onkeyup='return txtValida(event, "+'"car"'+" )' required>";
                    }
                    break;
                case "2":
                    if(x){
                        html += "<input type='text' class='form-control' name='txtValor"+x+"' id='txtValor"+x+"' style='width: 100%; font-size: 10px;' title='Valor de Característica' placeholder='Valor' autocomplete='off' onkeyup='return txtValida(event, "+'"num_car"'+")' required>";
                    }else{
                        html += "<input type='text' class='form-control' name='txtValor' id='txtValor' style='width: 100%; font-size: 10px;' title='Valor de Característica' placeholder='Valor' autocomplete='off' onkeyup='return txtValida(event, "+'"num_car"'+")' required>";
                    }
                    break;
                case "3":
                    break;
                    if(x){
                        html += "<input type='text' class='form-control' name='txtValor"+x+"' id='txtValor"+x+"' style='width: 100%; font-size: 10px;' title='Valor de Característica' placeholder='Valor' autocomplete='off' required>";
                    }else{
                        html += "<input type='text' class='form-control' name='txtValor' id='txtValor' style='width: 100%; font-size: 10px;' title='Valor de Característica' placeholder='Valor' autocomplete='off' required>";
                    }
                case "4":
                    if(x){
                        html += "<input type='text' class='form-control' name='txtValor"+x+"' id='txtValor"+x+"' style='width: 100%; font-size: 10px;' title='Valor de Característica' placeholder='Valor' autocomplete='off' onkeyup='return txtValida(event, "+'"num"'+")' required>";
                    }else{
                        html += "<input type='text' class='form-control' name='txtValor' id='txtValor' style='width: 100%; font-size: 10px;' title='Valor de Característica' placeholder='Valor' autocomplete='off' onkeyup='return txtValida(event, "+'"num"'+")' required>";
                    }
                    break;
                case "5":
                    if(x){
                        html += "<label class='radio-inline'><input type='radio' name='txtValor"+x+"' id='txtV1' value='0' required>SI</label>";
                        html += "<label class='radio-inline'><input type='radio' name='txtValor"+x+"' id='txtV1' value='1'>NO</label>";
                    }else{
                        html += "<label class='radio-inline'><input type='radio' name='txtValor' id='txtV1' value='0' required>SI</label>";
                        html += "<label class='radio-inline'><input type='radio' name='txtValor' id='txtV1' value='1'>NO</label>";
                    }
                    break;
                case "6":
                    if(x){
                        html += "<input type='text' class='form-control fecha' name='txtValor"+x+"' id='txtValor"+x+"' style='width: 100%; font-size: 10px;' title='Valor de Característica' placeholder='Valor' autocomplete='off' onkeyup='return txtValida(event, "+'"num_car"'+")' required>";
                    }else{
                        html += "<input type='text' class='form-control fecha' name='txtValor"+x+"' id='txtValor"+x+"' style='width: 100%; font-size: 10px;' title='Valor de Característica' placeholder='Valor' autocomplete='off' onkeyup='return txtValida(event, "+'"num_car"'+")' required>";
                    }
                    break;
            }
            if(x){
                $("#txtValor" + x).replaceWith(html);
            }else{
                $("#txtValor").replaceWith(html);
            }
        }

        function eliminarCaracteristica($id, espacio) {
            $.post("access.php?controller=EspacioHabitable&action=EliminarCaracteristica", { id : $id}, function (data) {
                solicitudCargaEspacios(espacio);
            });
        }
        
        function vervideo2(ruta){
            $.ajax({
                type: 'GET',
                url: 'vistas/espacioHabitable/video.modal.php?rutasphindex='+ruta,
                success: function(data){
                    $('#response').html(data);
                    $("#mdlvideo").modal("show");
                }
            })
        }
    </script>

</html>