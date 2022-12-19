<?php
require_once './head.php';
require_once('Conexion/conexion.php');
?>
    <script type="text/javascript">
        $(document).ready(function() {
           var i= 1;
            $('#tabla thead th').each( function () {
                if(i != 1){
                    var title = $(this).text();
                    switch (i){
                        case 3:
                            $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                        case 4:
                            $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
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
    <title>Listar Sistema</title>
</head>
<body>
    <!-- Contenedor principal -->
    <div class="container-fluid text-center">
        <div class="row content">
        <!-- Lllamado al menu -->
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Sistema</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top: -5px">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <!-- Inicio de tabla de listar -->
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <!-- Inicio de campos de filtrado-->
                                <tr>
                                    <td class="cabeza" style="display: none;">Identificador</td>
                                    <td class="cabeza" width="30px" align="center"></td>
                                    <td class="cabeza" ><strong>Nombre</strong></td>
                                    <td class="cabeza" width="10%"><strong>Configuración</strong></td>
                                </tr>
                                <!-- Fin de campos de filtrado -->
                                <!-- Inicio de titulos de la tabla-->
                                <tr>
                                    <th class="cabeza" style="display: none;">Identificador</th>
                                    <th class="cabeza" width="7%"></th>
                                    <th class="cabeza"></th>
                                    <th class="cabeza" width="10%"></th>
                                </tr>
                                <!-- Fin de titulos de la tabla -->
                                <!-- Fin de cabeza de tabla-->
                            </thead>
                            <tbody>
                                <?php
                                $sql_s = "SELECT sys.id_unico, sys.nombre FROM gs_sistema sys ORDER BY sys.nombre ASC";
                                $res_s = $mysqli->query($sql_s);
                                //Ciclo de impresión de registros existentes
                                while($row = mysqli_fetch_row($res_s)){
                                    echo "\n\t<tr>";
                                    echo "\n\t\t<td class=\"campos\" style=\"display: none;\">$row[0]</td>";
                                    echo "\n\t\t<td class=\"campos\">";
                                    echo "\n\t\t\t<a href=\"#\" class=\"campos\" onclick=\"javascript:eliminarSistema($row[0]);\"><i title=\"Eliminar\" class=\"glyphicon glyphicon-trash\"></i></a>";
                                    echo "\n\t\t\t<a class=\"campos\" href=\"modificar_GS_SISTEMA.php?id_sistema=".md5($row[0])."\"><i title=\"Modificar\" class=\"glyphicon glyphicon-edit\" ></i></a>";
                                    echo "\n\t\t</td>";
                                    echo "\n\t\t<td class=\"campos\">".ucwords(mb_strtolower($row[1]))."</td>";
                                    echo "\n\t\t<td class=\"campos text-center\">";
                                    echo "\n\t\t\t<a href=\"javascript:void(0)\" title=\"Configurar ventanas al sistema\" onclick=\"return openmodalConfiguration($row[0],'".ucwords(mb_strtolower($row[1]))."')\" class=\"glyphicon glyphicon-cog\"></a>";
                                    echo "\n\t\t</td>";
                                    echo "\n\t</tr>";
                                }
                                //Fin ciclo de impresión de registros existentes
                                ?>
                            </tbody>
                        </table>
                        <!-- Inicio de bóton de nuevo registro -->
                        <div align="right">
                            <a href="registrar_GS_SISTEMA.php" class="btn btn-primary sombra" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 10px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
                        </div>
                        <!-- Fin de bóton de nuevo registro -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Inicio de Modal-->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Sistema?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información eliminada correctamente</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal2" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" onclick="get_values_checkbox()">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalT" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Informaci&oacute;n</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p id="mensajeT">Informaci&oacute;n guardada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModalT" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <?php require_once './modalMenuConfiguracion.php'; ?>
    <?php require_once 'footer.php'; ?>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        /**
         * eliminarSistema
         * Función para eliminar un registro seleccionado
         * @param id
         */
        function eliminarSistema(id){
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminarSistema.php?id="+id,
                    success: function (data) {
                        result = JSON.parse(data);
                        if(result==true){
                            $("#myModal1").modal('show');
                        }else{
                            $("#myModal2").modal('show');
                        }
                    }
                });
            });
        }

        //Función para mostrar modal
        function modal(){
            $("#myModal").modal('show');
        }

        //Función para redirigir modal 1 al formulario listar
        $('#ver1').click(function(){
            reload_page();
        });

        $('#ver2').click(function(){
            reload_page();
        });

        /**
         * openmodalConfiguration
         * @param int sistema Id del sistema del cual se abrio la ventana
         * @param String namSys Nombre del sistema del cual se abrio la ventana
         */
        function openmodalConfiguration(sistema, namSys){
            var form_data = {
                sistema:sistema,
                nomSys:namSys
            };

            $.ajax({
                type:"POST",
                url:"modalMenuConfiguracion.php/#modalConfiguracionS",
                data: form_data,
                success:function (data, textStatus, jqXHR) {
                    $("#modalConfiguracionS").html(data);
                    $(".modalSistema").modal({ backdrop: 'static', keyboard: false,show:true });
                }
            }).error(function (data, textError, jqXHR) {
                alert('data :'+data+' - textError'+textError+' - jqXHR'+jqXHR);
            });
        }

        function reload_page () {
            window.location.reload();
        }

        function delete_obj(x) {
            var result = '';
            $("#modalConfiguracionS").modal('hide');
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"POST",
                    url:"consultasBasicas/consultas_modulo_sistema.php",
                    data: {
                        x:8,
                        id:x
                    },
                    success: function (data) {
                        result = JSON.parse(data);
                        if(result==true){
                            $("#myModal1").modal('show');
                        }else{
                            $("#myModal2").modal('show');
                        }
                    }
                });
            });
        }

        function modify_obj(id_unico, activo) {
            var result = '';

            if($("#lnkA"+id_unico).is(':visible')) {
                $.ajax({
                    type:"POST",
                    url:"consultasBasicas/consultas_modulo_sistema.php",
                    data:{
                        x:9,
                        id_unico:id_unico,
                        valor:$("#lnkA"+id_unico).attr('data-value')
                    },
                    success: function (data, textStatus, jqXHR) {
                        result = JSON.parse(data);
                        if(result == true) {
                            if($("#lnkA"+id_unico).attr('data-value') == 1) {
                                $("#lnkA"+id_unico).attr('data-value',0);
                                $("#lnkA"+id_unico).attr('title','Desactivar');
                                $("#lnkA"+id_unico).removeClass('glyphicon-ok');
                                $("#lnkA"+id_unico).addClass('glyphicon-remove');
                                $("#alertH").fadeIn("slow");
                                setTimeout(function() {
                                    $("#alertH").fadeOut(1700);
                                },5000);
                            }else if($("#lnkA"+id_unico).attr('data-value') == 0){
                                $("#lnkA"+id_unico).attr('data-value',1);
                                $("#lnkA"+id_unico).attr('title','Activar');
                                $("#lnkA"+id_unico).removeClass('glyphicon-remove');
                                $("#lnkA"+id_unico).addClass('glyphicon-ok');
                                $("#alertDes").fadeIn("slow");
                                setTimeout(function() {
                                    $("#alertDes").fadeOut(1700);
                                },5000);
                            }
                        }else{
                            $("#alertError").fadeIn("slow");
                            setTimeout(function() {
                                $("#alertError").fadeOut(1700);
                            },5000);
                        }
                    }
                });
            } else if($("#lnkD"+id_unico).is(':visible')) {
                $.ajax({
                    type:"POST",
                    url:"consultasBasicas/consultas_modulo_sistema.php",
                    data:{
                        x:9,
                        id_unico:id_unico,
                        valor:$("#lnkD"+id_unico).attr('data-value')
                    },
                    success: function (data, textStatus, jqXHR) {
                        result = JSON.parse(data);
                        if(result == true) {
                            if($("#lnkD"+id_unico).attr('data-value') == 1) {
                                $("#lnkA"+id_unico).attr('title','Desactivar');
                                $("#lnkD"+id_unico).attr('data-value',0);
                                $("#lnkD"+id_unico).removeClass('glyphicon-ok');
                                $("#lnkD"+id_unico).addClass('glyphicon-remove');
                                $("#alertH").fadeIn("slow");
                                setTimeout(function() {
                                    $("#alertH").fadeOut(1700);
                                },5000);
                            }else if($("#lnkD"+id_unico).attr('data-value') == 0){
                                $("#lnkD"+id_unico).attr('data-value',1);
                                $("#lnkA"+id_unico).attr('title','Activar');
                                $("#lnkD"+id_unico).removeClass('glyphicon-remove');
                                $("#lnkD"+id_unico).addClass('glyphicon-ok');
                                $("#alertDes").fadeIn("slow");
                                setTimeout(function() {
                                    $("#alertDes").fadeOut(1700);
                                },5000);
                            }
                        }else{
                            $("#alertError").fadeIn("slow");
                            setTimeout(function() {
                                $("#alertError").fadeOut(1700);
                            },5000);
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>

