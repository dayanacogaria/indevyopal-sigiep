<?php
/**
 * Created by Alexander.
 * User: Alexander
 * Date: 01/06/2017
 * Time: 2:42 PM
 */

require ('head_listar.php');
require ('Conexion/conexion.php');
$param = $_SESSION['anno'];
?>
    <title>Configuración Viasoft</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <style>
        body { font-size: 12px;}
        .shadow {box-shadow: 1px 1px 1px 1px gray;color:#fff; border-color:#1075C1;}
        table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:12px}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px}
        .dataTables_wrapper .ui-toolbar{padding:2px}
        .campoD {
            border-radius: 4px;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            line-height: 1.42857143;
            color: #555;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
            -webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
            height: 34px;
        }
        .campoD:focus {
            border-color: #66afe9;
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
        }
    </style>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require ('menu.php');?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 10px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Configuración Viasoft</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="controller/controllerGSConfiguracionViasoftSigiep.php?action=insert">
                        <p align="center" style="margin-bottom: 5px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="" class="control-label col-sm-2"><storng class="obligado">*</storng>Concepto Sigiep:</label>
                            <select name="sltConcepto" id="sltConcepto" class="form-control col-sm-1 select2" title="Seleccione un concepto" style="width: 25%;" required>
                                <?php
                                echo "<option value=\"\">Concepto</option>";
                                $sql = "SELECT id_unico, nombre FROM gf_concepto WHERE parametrizacionanno = $param ORDER BY nombre ASC";
                                $result = $mysqli->query($sql);
                                while($row = mysqli_fetch_row($result)) {
                                    echo "<option value=\"".$row[0]."\">".ucwords(mb_strtolower($row[1]))."</option>";
                                }
                                ?>
                            </select>
                            <label for="txtConceptoViaSoft" class="control-label col-sm-1"><strong class="obligado">*</strong>Código Viasoft:</label>
                            <input type="text" name="txtConceptoViaSoft" id="txtConceptoViaSoft" class="form-control col-sm-1" title="Ingrese el código de un concepto de viasoft" placeholder="Código" onkeypress="return txtValida(event,'num');" style="width: 15%" required/>
                            <label for="txtPorcentaje" class="control-label col-sm-1"><strong class="obligado">*</strong>Porcentaje: </label>
                            <input type="text" class="form-control col-sm-1" id="txtPorcentaje" name="txtPorcentaje" title="Ingrese el porcentaje" placeholder="Porcentaje" style="width:65px;" onkeypress="return txtValida(event,'num');" required/>
                            <button class="btn btn-primary shadow glyphicon glyphicon-floppy-disk" id="btnGuardar" name="btnGuardar" title="Guardar" style="margin-left: 10px"></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-10">
                <div class="table-responsive" style="margin-top: 10px">
                    <div class="table-responsive">
                        <input type="hidden" id="idPrevio" class="hidden" value="">
                        <input type="hidden" id="idActual" class="hidden" value="">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="oculto"></td>
                                    <td width="30px" align="center"></td>
                                    <td class="cabeza"><strong>Concepto</strong></td>
                                    <td class="cabeza"><strong>Código Viasoft</strong></td>
                                    <td class="cabeza"><strong>Porcentaje</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto"></th>
                                    <th width="7%"></th>
                                    <th class="cabeza"></th>
                                    <th class="cabeza"></th>
                                    <th class="cabeza"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT cons.id_unico, con.id_unico, con.nombre, cons.codigo_viasoft, cons.porcentaje FROM gs_configuracion_viasoft cons
                                        LEFT JOIN gf_concepto con ON cons.id_concepto = con.id_unico
                                        WHERE cons.parametrizacionanno = $param
                                        ORDER BY cons.id_concepto ASC";
                                $result = $mysqli->query($sql);
                                while ($row = mysqli_fetch_row($result)) {
                                    echo "\n\t<tr>";
                                    echo "\n\t\t<td class=\"oculto\"></td>";
                                    echo "\n\t\t<td class=\"campos\" width=\"7%\">";
                                    echo "\n\t\t\t<a href=\"#".$row[0]."\" class=\"campos glyphicon glyphicon-trash\" title=\"Eliminar\" onclick=\"delete_data(".$row[0].")\"></a>";
                                    echo "\n\t\t\t<a href=\"#".$row[0]."\" class=\"campos glyphicon glyphicon-edit\" title=\"Modificar\" onclick=\"show_input(".$row[0].")\"></a>";
                                    echo "\n\t\t</td>";
                                    echo "\n\t\t<td class=\"campos text-left\">".ucwords(mb_strtolower($row[2]))."</td>";
                                    echo "\n\t\t<td class=\"campos text-right\"><span id=\"lblCodigoV".$row[0]."\">".ucwords(mb_strtolower($row[3]))."</span>";
                                    echo "\n\t\t\t<input type=\"text\" id=\"txtCodigoV".$row[0]."\" name=\"txtCodigoV".$row[0]."\" style=\"display:none;padding:2px;\" class=\"form-control text-right\" value=\"".$row[3]."\" onkeypress=\"return txtValida(event,'num');\"/>";
                                    echo "\n\t\t</td>";
                                    echo "\n\t\t<td class=\"campos text-right\"><span id=\"lblPorcentaje".$row[0]."\">".ucwords(mb_strtolower($row[4]))."</span>";
                                    echo "\n\t\t\t<input type=\"text\" id=\"txtPorcentaje".$row[0]."\" name=\"txtPorcentaje".$row[0]."\" style=\"display:none;padding:2px;\" class=\"campoD col-sm-10 text-right\" value=\"".$row[4]."\" onkeypress=\"return txtValida(event,'num');\"/>";
                                    echo "\n\t\t\t<div>";
                                    echo "\n\t\t\t\t<table id=\"tab".$row[0]."\" style=\"padding: 0px;background-color: transparent;background: transparent;margin-top: 5px;\" class=\"col-sm-1\">";
                                    echo "\n\t\t\t\t\t\t<tbody>";
                                    echo "\n\t\t\t\t\t\t<tr style=\"background-color:transparent;\">";
                                    echo "\n\t\t\t\t\t\t\t<td style=\"background-color:transparent;\">";
                                    echo "\n\t\t\t\t\t\t\t\t<a href=\"#".$row[0]."\" title=\"Guardar\" id=\"guardar".$row[0]."\" style=\"display: none;\" class=\"glyphicon glyphicon-floppy-disk\" onclick=\"save_values_detail(".$row[0].")\"></a>";
                                    echo "\n\t\t\t\t\t\t\t</td>";
                                    echo "\n\t\t\t\t\t\t\t<td style=\"background-color:transparent;\">";
                                    echo "\n\t\t\t\t\t\t\t\t<a href=\"#".$row[0]."\" title=\"Cancelar\" id=\"cancelar".$row[0]."\" style=\"display: none\" class=\"glyphicon glyphicon-remove\" onclick=\"cancel_modify(".$row[0].")\" ></a>";
                                    echo "\n\t\t\t\t\t\t\t</td>";
                                    echo "\n\t\t\t\t\t\t</tr>";
                                    echo "\n\t\t\t\t\t\t</tbody>";
                                    echo "\n\t\t\t\t\t</table>";
                                    echo "\n\t\t\t</div>";
                                    echo "\n\t\t</td>";
                                    echo "\n\t</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require 'footer.php';?>
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
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlEliminado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información eliminada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" onclick="reload_page();" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlNoeliminado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlModificado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información modificada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModifico" class="btn" onclick="reload_page()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlNomodificado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha podido modificar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnNoModifico" class="btn" onclick="reload_page()" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2").select2();
        /**
         * Función para eliminar un registro seleccionado
         * @param id
         */
        function delete_data (id) {
            var form_data = {
                id_unico:id,
                action:'delete'
            };
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function () {
                $.ajax({
                    type:'POST',
                    url:'controller/controllerGSConfiguracionViasoftSigiep.php',
                    data:form_data,
                    success: function (data, textStatus, jqXHR) {
                        result = JSON.parse(data);
                        if(result == true) {
                            $("#mdlEliminado").modal('show');
                        }else{
                            $("#modalNoEliminado").modal('show');
                        }
                    }
                });
            });
        }
        /**
         * reload_page
         * Función para recargar la pagina
         */
        function reload_page() {
            window.location.reload();
        }

        /**
         * show_input
         * Función para mostrar los campos ocultos
         * @param id
         */
        function show_input(id) {
            if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
                //Labels
                var lblCodigoV1 = 'lblCodigoV'+$("#idPrevio").val();
                var lblPorcentaje1 = 'lblPorcentaje'+$("#idPrevio").val();
                //Campos
                var txtCodigoV1 = 'txtCodigoV'+$("#idPrevio").val();
                var txtPorcentaje1 = 'txtPorcentaje'+$("#idPrevio").val();
                //Campos para cancelar y guardar cambios
                var guardarC = 'guardar'+$("#idPrevio").val();
                var cancelarC = 'cancelar'+$("#idPrevio").val();
                var tablaC = 'tab'+$("#idPrevio").val();
                //Mostramos los labels
                $("#"+lblCodigoV1).css('display','block');
                $("#"+lblPorcentaje1).css('display','block');
                //Ocultamos los campos para modificar
                $("#"+txtCodigoV1).css('display','none');
                $("#"+txtPorcentaje1).css('display','none');
                //se mantienen ocultos
                $("#"+guardarC).css('display','none');
                $("#"+cancelarC).css('display','none');
                $("#"+tablaC).css('display','none');
            }
            //Labels
            var lblCodigoV = 'lblCodigoV'+id;
            var lblPorcentaje = 'lblPorcentaje'+id;
            //Campos
            var txtCodigoV = 'txtCodigoV'+id;
            var txtPorcentaje = 'txtPorcentaje'+id;
            //Campos para cancelar y guardar cambios
            var guardar = 'guardar'+id;
            var cancelar = 'cancelar'+id;
            var tabla = 'tab'+id;
            //Se ocultan los labels
            $("#"+lblCodigoV).css('display','none');
            $("#"+lblPorcentaje).css('display','none');
            //Se muestran los campos ocultos
            $("#"+txtCodigoV).css('display','block');
            $("#"+txtPorcentaje).css('display','block');
            //Se muestran los campos
            $("#"+guardar).css('display','block');
            $("#"+cancelar).css('display','block');
            $("#"+tabla).css('display','block');
            $("#idActual").val(id);
            //carga del campo oculto con la id anterior
            if($("#idPrevio").val() != id){
                $("#idPrevio").val(id);
            }
        }

        /**
         * cancel_modify
         * Función para ocultar los cambios, es decir ocultamos los botones y campos
         * @param id
         */
        function cancel_modify (id) {
            //Labels
            var lblCodigoV = 'lblCodigoV'+id;
            var lblPorcentaje = 'lblPorcentaje'+id;
            //Campos
            var txtCodigoV = 'txtCodigoV'+id;
            var txtPorcentaje = 'txtPorcentaje'+id;
            //Campos para cancelar y guardar cambios
            var guardar = 'guardar'+id;
            var cancelar = 'cancelar'+id;
            var tabla = 'tab'+id;
            //se muestran los labels
            $("#"+lblCodigoV).css('display','block');
            $("#"+lblPorcentaje).css('display','block');
            //Se ocultan los campos
            $("#"+txtCodigoV).css('display','none');
            $("#"+txtPorcentaje).css('display','none');
            //Se ocultan los campos
            $("#"+guardar).css('display','none');
            $("#"+cancelar).css('display','none');
            $("#"+tabla).css('display','none');
        }

        /**
         * save_values_detail
         * Función para guardar los valores modificados en los inputs
         * @param id
         */
        function save_values_detail(id) {
            var txtCodigoV = 'txtCodigoV'+id;
            var txtPorcentaje = 'txtPorcentaje'+id;
            var form_data = {
                action:'modify',
                id_unico:id,
                txtConceptoViaSotf:+$("#"+txtCodigoV).val(),
                txtPorcentaje:+$("#"+txtPorcentaje).val()
            };
            var result = '';
            $.ajax({
                type:'POST',
                url:'controller/controllerGSConfiguracionViasoftSigiep.php',
                data:form_data,
                success: function (data, textStatus, jqXHR) {
                    result = JSON.parse(data);
                    if(result == true) {
                        $("#mdlModificado").modal('show');
                    }else{
                        $("#mdlNomodificado").mocal('show');
                    }
                }
            });
        }
    </script>
</body>
</html>
