<!-- Diseño de modal de configuración -->
<style>
    #tree {
        overflow-y: auto;
        margin-bottom: -10px;
        margin-top: -10px;
        box-shadow: inset 1px 2px 1px 2px gray;
        height: 420px;
    }

    .modal-body {
        margin-top: 2px;
    }

    .modal-title {
        font-size: 24px;
        padding: 3px;
    }

    li {
        list-style: none;
    }

    a:link, a {
        text-decoration:none;
    }

    .alert {
        width: 200px;
        display: none;
        float: right;
        bottom:0px;
        right:0px;
    }
</style>
<div class="modal fade modalSistema" id="modalConfiguracionS" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                <?php
                $nameSys = "Configuración Sistema";
                if(!empty($_POST['nomSys'])) {
                    $nameSys = $_POST['nomSys'];
                    echo "<h4 class=\"modal-title\">".$nameSys."</h4>";
                }
                ?>
            </div>
            <div class="modal-body">
                <?php
                $sistema = 0;
                if(!empty($_POST['sistema'])) {
                    $sistema = $_POST['sistema'];
                    echo "\n\t<form action=\"#\" name=\"frmArbolS\" id=\"frmArbolS\">";
                    echo "\n\t\t<div id=\"tree\" class=\"text-left\">";
                    echo "\n\t\t\t<ul id=\"arbol\"></ul>";
                    echo "\n\t\t</div>";
                    echo "\n\t</form>";
                }
                ?>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnArbolS" class="btn glyphicon glyphicon-floppy-disk" style="color: #000; margin-top: 2px" onclick="get_values_checkbox()"></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    <?php if(!empty($_POST['sistema'])) { ?>
    $(function () {
        var html = "";
        html += '<div class="text-center">';
        html += '<img src="img/loading.gif"/><br/>';
        html += '<label class="control-label" style="font-size:20px;font-weight:bold;color:#1075C1">Cargando..</label>';
        html += '</div>';
        $('#tree').html(html);
        $.ajax({
            type:"POST",
            url:"consultasBasicas/consultas_modulo_sistema.php",
            data: {x:6, sistema: <?php echo $_POST['sistema']; ?>},
            success:function (data, textStatus, jqXHR) {
                if(data.length > 0) {
                    $("#tree").html(data);
                }
            }
        }).error(function (jqXHR, textStatus, errorThrown) {
            alert('XHR :'+jqXHR+' - textStatus :'+textStatus+' - errorThrown :'+errorThrown);
        });
    });
    <?php } ?>
    /**
     * show_son
     * Función para mostrar los hijos
     * @author Alexander Numpaque
     * @package Sistema_menu
     * @param int x Identificador de menu padre
     */
    function show_son(x) {
        if($("#listSons"+x).css('display') == 'none') {
            $("#plus"+x).removeClass('glyphicon-plus');
            $("#plus"+x).addClass('glyphicon-minus');
            $("#listSons"+x).fadeToggle("fast");
        }else if($("#listSons"+x).is(":visible") == true){
            $("#plus"+x).removeClass('glyphicon-minus');
            $("#plus"+x).addClass('glyphicon-plus');
            $("#listSons"+x).fadeToggle("fast");
        }
    }

    /**
     * show_window
     * Función para mostrar y ocultar un listado de ventanas
     * @author Alexander Numpaque
     * @package Sistema_menu
     * @param int x Identificador de menu ventana
     */
    function show_window(x) {
        if($("#listWindow"+x).css('display') == 'none') {
            $("#plus"+x).removeClass('glyphicon-plus');
            $("#plus"+x).addClass('glyphicon-minus');
            $("#listWindow"+x).fadeToggle("fast");
        }else if($("#listWindow"+x).is(":visible") == true){
            $("#plus"+x).removeClass('glyphicon-minus');
            $("#plus"+x).addClass('glyphicon-plus');
            $("#listWindow"+x).fadeToggle("fast");
        }
    }

    /**
     * show_info
     * Función para mostrar y ocultar un listado de campos y botones
     * @author Alexander Numpaque
     * @package Sistema_menu
     * @param int x Identificador de ventana
     */
    function show_info(x) {
        if($("#listData"+x).css('display') == 'none') {
            $("#plusd"+x).removeClass('glyphicon-plus');
            $("#plusd"+x).addClass('glyphicon-minus');
            $("#listData"+x).fadeToggle("fast");
        }else if($("#listData"+x).is(":visible") == true){
            $("#plusd"+x).removeClass('glyphicon-minus');
            $("#plusd"+x).addClass('glyphicon-plus');
            $("#listData"+x).fadeToggle("fast");
        }
    }

    /**
     * show_inputs
     * Función paa mostrar y ocultar el listado con los botones relacionados a la ventana
     * @author Alexander Numpaque
     * @package Sistema_menu
     * @param int x Identificador de menu ventana
     */
    function show_inputs(x) {
        if($("#listInputs"+x).css('display') == 'none') {
            $("#plusi"+x).removeClass('glyphicon-plus');
            $("#plusi"+x).addClass('glyphicon-minus');
            $("#listInputs"+x).fadeToggle("fast");
        }else if($("#listInputs"+x).is(":visible") == true){
            $("#plusi"+x).removeClass('glyphicon-minus');
            $("#plusi"+x).addClass('glyphicon-plus');
            $("#listInputs"+x).fadeToggle("fast");
        }
    }

    /**
     * show_buttons
     * Función para mostrar y ocultar el listado con los botones
     * @author Alexander Numpaque
     * @package Sistema_menu
     * @param int x Identificador de menu ventana
     */
    function show_buttons(x) {
        if($("#listButtons"+x).css('display') == 'none') {
            $("#plusb"+x).removeClass('glyphicon-plus');
            $("#plusb"+x).addClass('glyphicon-minus');
            $("#listButtons"+x).fadeToggle("fast");
        }else if($("#listButtons"+x).is(":visible") == true){
            $("#plusb"+x).removeClass('glyphicon-minus');
            $("#plusb"+x).addClass('glyphicon-plus');
            $("#listButtons"+x).fadeToggle("fast");
        }
    }

    function get_values_checkbox() {
        var selected = '';                                                          //Inicializamos la variable selected
        //Capturamos los valores de los campos tipo checkbox donde este este marcado
        $('input[type=checkbox]').each(function(){
            if (this.checked && $(this).val() != 0 && $(this).is(':enabled')) {
                selected += $(this).val()+',';
            }
        });

        if(selected.length > 0) {//Validamos que existan campos seleccionados
            var select = selected.substr(0, (selected.length) - 1);                     //Al String le quitamos la ultima ,
            var result = '';
            $.ajax({
                type: "POST",
                url: "consultasBasicas/consultas_modulo_sistema.php",
                data:{
                    x: 7,
                    seleccionados: select,
                    sistema: <?php echo $sistema; ?>
                },
                success: function (data, textStatus, jqXHR) {
                    result = JSON.parse(data);
                    if(result == true){
                        $("#mensajeT").html('<p id="mensajeT">Información guardada correctamente.</p>');
                        $("#modalConfiguracionS").modal('hide');
                        $("#modalT").modal('show');
                        $("#btnModalT").click(function () {
                            window.location.reload();
                        });
                    }else{
                        $("#mensajeT").html('<p id="mensajeT">Información guardada correctamente.</p>');
                        $("#modalT").modal('fast');
                    }
                    console.log(data);
                }
            }).error(function (data, textStatus, errorThrown) {
               alert('Datos :'+data+', textStatus'+textStatus+', Error:'+errorThrown);
            });
        }
    }
</script>
<?php
echo "\n\t\t<div id='alertH' class=\"alert alert-info alert-dismissible\" role=\"alert\">";
echo "\n\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
echo "\n\t\t\t<strong>Información!</strong><br/>Se habilito el objeto seleccionado";
echo "\n\t\t</div>";
echo "\n\t\t<div id='alertDes' class=\"alert alert-info alert-dismissible\" role=\"alert\">";
echo "\n\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
echo "\n\t\t\t<strong>Información!</strong><br/>Se deshabilito el objeto seleccionado";
echo "\n\t\t</div>";
echo "\n\t\t<div id='alertError' class=\"alert alert-info alert-dismissible\" role=\"alert\">";
echo "\n\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
echo "\n\t\t\t<strong>Información!</strong><br/>Error al actualizar la información en la tabla";
echo "\n\t\t</div>";
?>