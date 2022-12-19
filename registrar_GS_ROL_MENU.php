<?php
/**
 * Created by PhpStorm.
 * User: Alexander Numpaque
 * Date: 23/06/2017
 * Time: 9:38 AM
 */
include ('head_listar.php');
include ('Conexion/conexion.php');
$compania = $_SESSION['compania'];
$nit_ut  = "SELECT t.numeroidentificacion, u.rol
FROM gs_usuario u 
LEFT JOIN gf_tercero t ON u.tercero = t.id_unico 
WHERE t.id_unico =".$_SESSION['usuario_tercero'];
$r = $mysqli->query($nit_ut);
$r = mysqli_fetch_row($r);

#Función para obtener todos los padres principales
function obtener_padres(){
    #Declaramos una variable la cual retornara como string
    $html="";
    #Conexión a la base de datos
    require './Conexion/conexion.php';
    #Consulta para obtener los padres que en la tabla de relacion su valor sea nulo
    $sql="select main.id_unico,main.nombre from gs_menu main left join gs_menu_aso maso on maso.menuhijo=main.id_unico where maso.id_unico is null";
    $result=$mysqli->query($sql);
    #obtenemos los valores devueltos por la consulta
    while ($row= mysqli_fetch_row($result)){
        #Armado de html dinamico
        $html.= '<li><a><span style="font-size:11px">'.ucwords(mb_strtolower($row[1])).'</span></a>';
        if(!empty(obtener_hijos($row[0]))){
            $html.= '<ul>';
            $html.= obtener_hijos($row[0]);
            $html.= '</ul></li>';
        }
    }
    #Retorna un html
    return $html;
}
?>
<title>Registrar Rol Menú</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="js/jstree/src/themes/default/style.css">
<style>
    .parrafoO {
        margin-bottom: 5px;
    }

    .client-form input[type="text"] {
        width: 31%;
    }

    .shadow {
        box-shadow: 1px 1px 1px 1px gray;
        color: #fff;
        border-color: #1075C1;
    }

    table.dataTable thead th,
    table.dataTable thead td{
        padding: 1px 18px;
        font-size: 12px
    }

    table.dataTable tbody td,
    table.dataTable tbody td{
        padding: 1px
    }
    .dataTables_wrapper .ui-toolbar{
        padding: 2px
    }

    #tree {
        box-shadow: inset 1px 1px 2px 2px gray;
        border-radius: 5px;
        margin-right: 25px;
        margin-left: 25px;
        overflow-y: auto;
        margin-bottom: -10px;
        margin-top: -10px;
        height: 350px;
    }

    #CB {
        float: right;
    }

    li {
        list-style: none;
    }

    a:link {
        text-decoration:none;
    }
</style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php include ('menu.php');?> 
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 id="forma-titulo3" style="margin-top: 0px; text-align: center">Registrar Rol Menú</h2>
                <div class="client-form contenedorForma">
                    <form action="#?action=insert" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline text-left">
                            <label for="sltRol" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Rol:</label>
                            <select name="sltRol" id="sltRol" class="form-control col-sm-1 col-md-1 col-lg-1 select2" style="width: 89.5%;" title="Seleccione Rol" onchange="reload_main_rol($(this).val())" required>
                                <?php
                                echo "<option value=\"\">Rol</option>";
                                 if ($r[0]=='900849655'){ 
                                    $sql__R = "SELECT id_unico, nombre FROM gs_rol WHERE compania = $compania";
                                } else {
                                    $sql__R = "SELECT id_unico, nombre FROM gs_rol WHERE compania = $compania 
                                    AND nombre != 'Administrador Grupo AAA Asesores SAS'";
                                }
                                $result__R = $mysqli->query($sql__R);
                                while ($row_R = mysqli_fetch_row($result__R)) {
                                    echo "<option value='$row_R[0]'>".ucwords(mb_strtolower($row_R[1]))."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="text-left">
                                <div id="tree"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-1 col-md-1 col-lg-1" id="CB">
                                <a href="javascript:void(0)" id="btnGuardar" class="btn btn-primary glyphicon glyphicon-floppy-disk shadow guardar" onclick="send_ajax()"></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php include ('footer.php') ?>
        </div>
        <div class="modal fade" id="modal" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Informaci&oacute;n</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p id="mensaje">Informaci&oacute;n guardada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>¿Desea eliminar el registro seleccionado?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnN" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jstree/jstree.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2").select2();

        /**
         * Función para mostrar un gif mientras se obtiene respuesta de la base de datos
         */
        /*$(function () {
            var html = "";
            html+= '<div class="text-center">';
            html+= '<img src="img/loading.gif"/><br/>';
            html+= '<label class="control-label" style="font-size:20px;font-weight:bold;color:#1075C1">Cargando..</label>';
            html+= '</div>';
            $('#tree').html(html);
            $.ajax({
                type:"POST",
                url:"consultasBasicas/consultas_modulo_sistema.php",
                data: {x:1},
                success:function (data, textStatus, jqXHR) {
                    $("#tree").html(data);
                }
            }).error(function (jqXHR, textStatus, errorThrown) {
                alert('XHR :'+jqXHR+' - textStatus :'+textStatus+' - errorThrown :'+errorThrown);
            });
        });*/

        /**
         * show_son
         *
         * Función para mostrar los hijos
         * @param x
         */
        function show_son(x) {
            if($("#hijos"+x).css('display') == 'none') {
                $("#plus"+x).removeClass('glyphicon-plus');
                $("#plus"+x).addClass('glyphicon-minus');
                $("#hijos"+x).fadeToggle("fast");
            }else if($("#hijos"+x).is(":visible") == true){
                $("#plus"+x).removeClass('glyphicon-minus');
                $("#plus"+x).addClass('glyphicon-plus');
                $("#hijos"+x).fadeToggle("fast");
            }
        }

        function send_ajax() {
            var selected = '';                                                          //Inicializamos la variable selected
            //Capturamos los valores de los campos tipo checkbox donde este este marcado
            $('input[type=checkbox]').each(function(){
                if (this.checked) {
                    if (this.checked && $(this).is(':enabled')) {
                        selected += $(this).val() + ',';
                    }
                }
            });
            var rol = $("#sltRol").val();
            if(rol.length < 0 || $("#sltRol").val() == '') {
                $("#mensaje").html('<p>Seleccione un rol!!</p>');
                $("#modal").modal('show');
            } else {
                if(selected.length > 0) {
                    var select = selected.substr(0, (selected.length) - 1);                     //Al String le quitamos la ultima ,
                    var form_data = {
                        seleccionados:select,
                        x:2,
                        rol:rol
                    };
                    var result = '';
                    $.ajax({
                        type:"POST",
                        url:"consultasBasicas/consultas_modulo_sistema.php",
                        data:form_data,
                        success:function (data, textStatus, jqXHR) {
                            result = JSON.parse(data);
                            if(result == true) {
                                $("#mensaje").html('<p>Información guardada correctamente.</p>');
                                $("#modal").modal('show');
                                $("#btnModal").click(function () {
                                    window.location.reload();
                                });
                            } else {
                                $("#mensaje").html('<p>No se ha podido guardar la informaci&oacute;n.</p>');
                                $("#modal").modal('show');
                            }
                        }
                    }).error(function (jqXHR, textStatus, errorThrown) {
                        alert('XHR :'+jqXHR+' - textStatus :'+textStatus+' - errorThrown :'+errorThrown);
                    });
                } else {
                    $("#mensaje").html('<p>Seleccione una o más opciones de menú!!</p>');
                    $("#modal").modal('show');
                }
            }
        }

        function reload_main_rol(rol) {
            if(rol.length > 0) {
                var html = "";
                html+= '<div class="text-center">';
                html+= '<img src="img/loading.gif"/><br/>';
                html+= '<label class="control-label" style="font-size:20px;font-weight:bold;color:#1075C1">Cargando menú..</label>';
                html+= '</div>';
                $('#tree').html(html);
                $.ajax({
                    type:"POST",
                    url:"consultasBasicas/consultas_modulo_sistema.php",
                    data: {x:3, rol:rol},
                    success:function (data, textStatus, jqXHR) {
                        $("#tree").html(data);
                    }
                }).error(function (jqXHR, textStatus, errorThrown) {
                    alert('XHR :'+jqXHR+' - textStatus :'+textStatus+' - errorThrown :'+errorThrown);
                });
            }
        }

        function eliminar(rol, option) {
            $("#myModal").modal('show');
            $("#btnN").click(function () {
                var result = '';
                $.ajax({
                    type:"POST",
                    url:"consultasBasicas/consultas_modulo_sistema.php",
                    data: {x:4, rol:rol, option:option},
                    success:function (data, textStatus, jqXHR) {
                        result = JSON.parse(data);
                        if(result == true) {
                            $("#mensaje").html('<p>Información eliminada correctamente.</p>');
                            $("#modal").modal('show');
                            $("#btnModal").click(function () {
                                window.location.reload();
                            });
                        } else {
                            $("#mensaje").html('<p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>');
                            $("#modal").modal('show');
                        }
                    }
                }).error(function (jqXHR, textStatus, errorThrown) {
                    alert('XHR :'+jqXHR+' - textStatus :'+textStatus+' - errorThrown :'+errorThrown);
                });
            });
        }
    </script>
</body>
</html>