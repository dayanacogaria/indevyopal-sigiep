<?php
require './Conexion/conexion.php';
require_once './head.php';
?>
    <title>Novedades</title>
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
            <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Novedad</h2>
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                <table id="tablaX" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <td class="cabeza" style="width: 7%;"></td>
                        <td class="cabeza">Nombre</td>
                        <td class="cabeza">Inactivo</td>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $html = "";
                    while($row = mysqli_fetch_row($data)) { ?>
                        <tr>
                            <td>
                                <a href="javascript:eliminar('access.php?controller=EspacioHabitable&action=eliminarNovedad&id=<?php echo $row[0] ?>')" ><i title="Eliminar" class="glyphicon glyphicon-trash" ></i></a>
                                <a onclick="modificarModal(<?php echo $row[0] . ',' . "'" . $row[1] . "'" . ',' . $row[2]?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                            </td>
                            <td><?php echo utf8_decode((ucwords(strtolower($row[1])))); ?></td> 
                            <td>
                                <?php 
                                    if($row[2] == 1){
                                        echo "Si";
                                    }else{
                                        echo "No";
                                    }
                                ?>
                            </td> 
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 10px">
            <div class="col-sm-push-10 col-md-push-10 col-lg-push-10 col-sm-2 col-md-2 col-lg-2">
                <a href="" class="btn btn-primary col-sm-2 borde-sombra" style="color: #fff;border-color: #1075C1; width: 100%" data-toggle='modal' data-target='#myModal'>Registrar Nuevo <span class="glyphicon glyphicon-plus"></a>
            </div>
        </div>
    </div>
</div>
    <!--  Modal registrar Novedad  -->  
<div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content client-form1">
                <div id="forma-modal" class="modal-header">       
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Novedad</h4>
                </div>
                <div class="modal-body ">
                    <form  id="form" name="form" method="POST" action="access.php?controller=EspacioHabitable&action=guardarNovedad">
                        <div class="form-group" style="margin-top: 13px;">
                            <label for="nombre" style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="nombre" id="nombre" class="form-control" title="Ingrese Nombre" required onkeypress="return txtValida(event, 'car')" >
                        </div>            
                        <div class="form-group" style="margin-top: 13px; margin-left: -177px">
                        <label for="estado"  style="margin-left: 10px;display:inline-block; width:140px;" ><strong style="color:#03C1FB;">*</strong>Inactivo:</label>
                        <label><input type="radio" id="estado" name="estado" value="1" checked="">Si</label> 
                        <label><input type="radio" id="estado" name="estado" value="2">No</label> 
                        </div>
                </div>

                <div id="forma-modal" class="modal-footer">
                    <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
                    <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
                </div>
                </form>
            </div>
        </div>
</div>
    
<!--  Modal Editar novedad -->  
<div class="modal fade" id="myModalEdit" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content client-form1">
                <div id="forma-modal" class="modal-header">       
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar Novedad</h4>
                </div>
                <div class="modal-body ">
                    <form  id="form" name="form" method="POST" action="access.php?controller=EspacioHabitable&action=actualizarNovedad">
                        <input type="hidden" id="idx" name="idx">
                        <div class="form-group" style="margin-top: 13px;">                            
                            <label for="nombrex" style="display:inline-block; width:140px; padding-left: 6px;"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="nombrex" id="nombrex" class="form-control" title="Ingrese Nombre" required onkeypress="return txtValida(event, 'car')" >
                        </div>            
                        <div class="form-group" style="margin-top: 13px; margin-left: -198px">
                        <label for="estado"  style="margin-left: 10px;display:inline-block; width:140px;" ><strong style="color:#03C1FB;">*</strong>Inactivo:</label>
                        <label><input type="radio" id="on" name="estadox" value="1">Si</label> 
                        <label><input type="radio" id="off" name="estadox" value="2">No</label> 
                        </div>
                </div>

                <div id="forma-modal" class="modal-footer">
                    <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
                    <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
                </div>
                </form>
            </div>
        </div>
</div>
<div class="modal fade" id="mdleliminar" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p id="shows"></p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnI" onClick="window.location.reload()" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
<?php require_once 'footer.php'; ?>
<?php require_once 'modales.php'; ?>
<?php require_once './vistas/espacioHabitable/caracteristicas.modal.php'; ?>
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
        $("#myModal").on('show.bs.modal', function (e) {
            
        });
        
        function modificarModal(id, nombre, estado){            
            $("#idx").val(id)
            $("#nombrex").val(nombre);
            if(estado == 1){
                $("#on").prop("checked", true);
            }else{
                $("#off").prop("checked", true);
            }
            $("#myModalEdit").modal('show');            
        }
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
</html>