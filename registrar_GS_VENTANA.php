<?php
/**
 * Created by PhpStorm.
 * User: ALEXANDER
 * Date: 29/06/2017
 * Time: 9:46 AM
 *
 * Registro gs_ventana, gs_menu_ventana, gs_campo, gs_ventana_campo, gs_boton_ gs_ventana_botón
 */
require ('head_listar.php');
require ('Conexion/conexion.php');

$id_v = ""; $nombre = ""; $padre = ""; $hijo = ""; $nombre_hijo = ""; $menu_aso = ""; $familia = ""; $mn_v = ""; $campos = "";
$botones = "";
if(!empty($_GET['window'])) {
    $v = $_GET['window'];
    $sql_v = "SELECT    vnt.id_unico, vnt.nombre, mn_a.menupadre, hijo.id_unico, hijo.nombre, mn_a.id_unico, mn_v.id_unico
              FROM      gs_ventana vnt 
              LEFT JOIN gs_menu_ventana mn_v ON vnt.id_unico  = mn_v.ventana
              LEFT JOIN gs_menu_aso mn_a     ON mn_v.menuaso  = mn_a.id_unico
              LEFT JOIN gs_menu hijo         ON mn_a.menuhijo = hijo.id_unico
              WHERE     md5(vnt.id_unico) =  '$v'";
    $rs_v = $mysqli->query($sql_v);
    $rw_v = mysqli_fetch_row($rs_v);
    $id_v = $rw_v[0];
    $nombre = $rw_v[1];
    $padre = $rw_v[2];
    $hijo = $rw_v[3];
    $nombre_hijo = $rw_v[4];
    $menu_aso = $padre."/".$hijo;
    $familia = $rw_v[5];
    $mn_v = $rw_v[6];
    $query_b = "SELECT id_unico FROM gs_ventana_boton WHERE menuventana = $mn_v";
    $resultB = $mysqli->query($query_b);
    while ($rowB = mysqli_fetch_row($resultB)) {
        $botones .= $rowB[0].",";
    }
    $campos = substr($campos,0, strlen($campos) -1);
    $botones = substr($botones, 0, strlen($botones) -1);
}
?>
<title>Registro de Ventana</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script type="text/javascript" src="js/md5.js" ></script>
<script src="dist/jquery.validate.js"></script>
<style>
    .parrafoO {
        margin-bottom: 5px;
    }

    #table {
        margin: 0px;
        padding: 0px;
    }

    #btn_adic {
        margin: 0px;
        text-align: center;
    }

    .shadow {
        box-shadow: 1px 1px 1px 1px gray;
        color: #fff;
        border-color: #1075C1;
    }

    #botones {
        float: right;
        margin: -80px 10px -15px 5px;
        overflow: visible;
    }

    #form {
        margin-bottom: -10px;
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

    .table-responsive {
        margin-top: 5px;
    }

    .modal-title {
        font-size: 24px;
        padding: 3px;
    }

    label #txtNombre-error, #sltMenuAso-error, #sltCampo-error, #sltBoton-error {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;
        font-size: 11px;
    }

    .campo {
        font-size:12px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        var i= 1;
        $('#tableButton thead th').each( function () {
            if(i != 1){
                var title = $(this).text();
                switch (i){
                    case 2:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                    case 3:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                }
                i = i+1;
            }else{
                i = i+1;
            }
        });
        // DataTable
        var table = $('#tableButton').DataTable({
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

        var i= 1;
        $('#tableInput thead th').each( function () {
            if(i != 1){
                var title = $(this).text();
                switch (i){
                    case 2:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                    case 3:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                }
                i = i+1;
            }else{
                i = i+1;
            }
        });
        // DataTable
        var table = $('#tableInput').DataTable({
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

    $().ready(function() {
        var validator = $("#form").validate({
            ignore: "",
            errorPlacement: function(error, element) {
                $( element )
                    .closest( "form" )
                    .find( "label[for='" + element.attr( "id" ) + "']" )
                    .append( error );
                //var o = element.attr("id");
                //document.getElementById(o).style.border = "solid #0000FF";
            }
        });
        $(".cancel").click(function() {
            validator.resetForm();
        });
    });
</script>
</head>
<body onload="return clean_n_input(); return clean_n_button()">
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require ('menu.php');?>
            <div class="col-sm-8 col-md-8 col-lg-8">
                <h2 id="forma-titulo3" style="margin-top: 0px; text-align: center">Registrar Ventana</h2>
                <div class="client-form contenedorForma">
                    <form action="controller/controllerGSVentana.php?action=insert" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group text-left">
                            <label for="txtNombre" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Nombre Ventana:</label>
                            <input type="text" name="txtNombre" id="txtNombre" class="form-control col-sm-1 col-md-1 col-lg-1" placeholder="Nombre Ventana" title="Ingrese el nombre de la ventana" style="width: 30%" required onkeypress="return txtValida(event,'car')" value="<?php echo $nombre ?>">
                            <label for="sltMenuAso" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Menú asociado:</label>
                            <select name="sltMenuAso" id="sltMenuAso" class="form-control col-md-1 col-md-1 col-lg-1 select2" style="width: 33%" required title="Seleccione una opción de menu">
                                <?php
                                if(!empty($menu_aso)) {
                                    echo "<option value='$menu_aso'>".ucwords(mb_strtolower($nombre_hijo))."</option>";
                                    $sql_a = "SELECT    familia.menupadre, hijo.id_unico, hijo.nombre 
                                              FROM      gs_menu_aso familia 
                                              LEFT JOIN gs_menu hijo        ON  familia.menuhijo  =  hijo.id_unico 
                                              LEFT JOIN gs_menu padre       ON  familia.menupadre =  padre.id_unico 
                                              WHERE     hijo.ruta != 'NULL' AND familia.id_unico  != $familia 
                                              ORDER BY  hijo.nombre  ASC";
                                    $rs_a = $mysqli->query($sql_a);
                                    while ($row_a = mysqli_fetch_row($rs_a)) {
                                        echo "<option value=\"".$row_a[0].'/'.$row_a[1]."\">".ucwords(mb_strtolower($row_a[2]))."</option>";
                                    }
                                } else {
                                    echo "<option value=''>Menu</option>";
                                    $sql_a = "SELECT    familia.menupadre, hijo.id_unico, hijo.nombre 
                                              FROM      gs_menu_aso familia 
                                              LEFT JOIN gs_menu hijo  ON familia.menuhijo  = hijo.id_unico 
                                              LEFT JOIN gs_menu padre ON familia.menupadre = padre.id_unico 
                                              WHERE     hijo.ruta != 'NULL' 
                                              ORDER BY  hijo.nombre  ASC";
                                    $rs_a = $mysqli->query($sql_a);
                                    while ($row_a = mysqli_fetch_row($rs_a)) {
                                        echo "<option value=\"".$row_a[0].'/'.$row_a[1]."\">".ucwords(mb_strtolower($row_a[2]))."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group text-left" style="margin-top: -15px">
                            <label for="sltBoton" id="sltBoton" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Botones:</label>
                            <select name="sltBoton[]" id="sltBoton" class="form-control col-md-1 col-md-1 col-lg-1 select2" style="width: 30%;" multiple="100" required title="Seleccione uno o mas botones" placeholder="Botones">
                                <?php
                                if(!empty($botones)) {
                                    $sqlB = "SELECT id_unico, nombre FROM gs_boton WHERE id_unico IN ($botones)";
                                    $resultB = $mysqli->query($sqlB);
                                    while ($rowB = mysqli_fetch_row($resultB)) {
                                        echo "<option value='$rowB[0]' selected>".ucwords(mb_strtolower($rowB[1]))."</option>";
                                    }
                                    $sql_b = "SELECT id_unico,nombre FROM gs_boton WHERE id_unico NOT IN ($botones) ORDER BY nombre ASC";
                                    $rs_b = $mysqli->query($sql_b);
                                    while ($row_b = mysqli_fetch_row($rs_b)) {
                                        echo "<option value='$row_b[0]'>".ucwords(strtolower($row_b[1]))."</option>";
                                    }
                                }else{
                                    $sql_b = "SELECT id_unico,nombre FROM gs_boton ORDER BY nombre ASC";
                                    $rs_b = $mysqli->query($sql_b);
                                    while ($row_b = mysqli_fetch_row($rs_b)) {
                                        echo "<option value='$row_b[0]'>".ucwords(strtolower($row_b[1]))."</option>";
                                    }
                                }
                                ?>
                            </select>
                            <div class="col-sm-3 col-md-3 col-lg-3 col-sm-push-4 col-md-push-4 col-lg-push-4">
                                <a href="javascript:void(0)" class="btn btn-primary glyphicon glyphicon-plus shadow nuevo" id="btnNuevo" onclick="url_clean()" title="Registrar Ventana nueva"></a>
                                <button type="submit" class="btn btn-primary glyphicon glyphicon-floppy-disk shadow guardar" id="btnGuardar" title="Guardar Ventana"></button>
                                <a href="javascript:void(0)" id="btnModificar" title="Modificar ventana" class="btn btn-primary shadow glyphicon glyphicon-pencil modificar" onclick="save_changes(<?php echo $id_v.",".$mn_v ?>)"></a>
                                <?php
                                if(!empty($_GET['window'])) {
                                    echo "\n\t<script>";
                                    echo "\n\t\t$('#btnGuardar').attr(\"disabled\", true)";
                                    echo "\n\t</script>";
                                }else{
                                    echo "\n\t<script>";
                                    echo "\n\t\t$('#btnNuevo, #btnModificar').attr(\"disabled\", true)";
                                    echo "\n\t</script>";
                                }
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-1 col-md-2 col-lg-2" id="info_adic">
                <table class="tablaC table-condensed text-center" align="center" id="table">
                    <thead>
                    <tr>
                        <th><h2 class="titulo" align="center" style="font-size:17px;" id="btn_adic">Información<br/>adicional</h2></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a class="btn btn-primary btnInfo" href="javascript:void(0)" onclick="open_modal_insert_button()">BOTÓN</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-8 col-md-8 col-lg-8">
                <div class="table-responsive">
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <td class="oculto"></td>
                            <td width="7%"></td>
                            <td><strong>Ventana</strong></td>
                            <td><strong>Menú Asociado</strong></td>
                            <td><strong>Botones</strong></td>
                        </tr>
                        <tr>
                            <th class="oculto"></th>
                            <th width="7%"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sqlV = "SELECT vn.id_unico, vn.nombre, hijo.nombre, mnV.id_unico
                                 FROM gs_ventana vn
                                 LEFT JOIN gs_menu_ventana mnV ON vn.id_unico = mnV.ventana
                                 LEFT JOIN gs_menu_aso mnA ON mnV.menuaso = mnA.id_unico
                                 LEFT JOIN gs_menu hijo ON mnA.menuhijo = hijo.id_unico";
                        $resultV = $mysqli->query($sqlV);
                        while ($rowV = mysqli_fetch_row($resultV)) {
                            echo "\n\t<tr>";
                            echo "\n\t\t<td class='oculto'></td>";
                            echo "\n\t\t<td class='campos'>";
                            echo "\n\t\t\t<a class='glyphicon glyphicon-trash eliminar' id='btnDelV' href='javascript:void(0)' onclick='delete_window($rowV[0], $rowV[3])'></a>";
                            echo "\n\t\t\t<a class='glyphicon glyphicon-edit modificar' id='btnModV' href='javascript:void(0)' onclick='modify_window($rowV[0])'></a>";
                            echo "\n\t\t</td>";
                            echo "\n\t\t<td class='campo text-left'>".ucwords(mb_strtolower($rowV[1]))."</td>";
                            echo "\n\t\t<td class='campo text-left'>".ucwords(mb_strtolower($rowV[2]))."</td>";
                            $sqlBotones = "SELECT mvB.boton, boton.nombre FROM gs_ventana_boton mvB 
                                           LEFT JOIN gs_boton boton ON mvB.boton = boton.id_unico
                                           WHERE mvB.menuventana = $rowV[3] ORDER BY boton.nombre ASC";
                            $resultBotones = $mysqli->query($sqlBotones);
                            $botones = "";
                            while ($rowBotones = mysqli_fetch_row($resultBotones)) {
                                $botones .= ucwords(mb_strtolower($rowBotones[1])).", ";
                            }
                            $botones = substr($botones, 0, strlen($botones) - 2);
                            echo "\n\t\t<td class='campo text-left'>".$botones."</td>";
                            echo "\n\t</tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php require ('footer.php');?>
            <div class="modal fade" id="modal_boton" role="dialog" align="center" data-keyboard="false" data-backdrop="static" onload="return clean_n_button()">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="forma-modal" class="modal-header">
                            <button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                            <h4 class="modal-title">Registrar Botón</h4>
                        </div>
                        <div class="modal-body row">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="client-form contenedorForma">
                                    <form action="controller/controllerGSVentana.php?action=insert_button" id="frmButton" class="form-horizontal" method="POST" enctype="multipart/form-data">
                                        <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                                        <div class="form-group" style="margin-bottom: -15px">
                                            <label for="txtNombreB" class="control-label col-lg-3 col-md-3 col-sm-3"><strong class="obligado">*</strong>Nombre Botón:</label>
                                            <input type="text" name="txtNombreB" id="txtNombreB" class="form-control col-sm-1 col-md-1 col-lg-1" placeholder="Nombre Botón" title="Ingrese el nombre del bóton" required style="width: 405px;">
                                            <input type="hidden" name="txtIdButton" id="txtIdButton">
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-3 col-md-3 col-lg-3 col-sm-push-9 col-md-push-9 col-lg-push-9">
                                                <button type="submit" id="btnGuardarB" class="btn btn-primary glyphicon glyphicon-floppy-disk guardar shadow" title="Guardar"></button>
                                                <a id="btnModificarB" class="btn btn-primary glyphicon glyphicon-pencil modificar shadow" title="Modificar" onclick="update_button()" disabled></a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="table-responsive">
                                    <table id="tableButton" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <td width="10%"></td>
                                            <td><strong class="cabeza">Nombre</strong></td>
                                        </tr>
                                        <tr>
                                            <th width="10%"></th>
                                            <th class="cabeza"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $sql_b = "SELECT id_unico, nombre FROM gs_boton ORDER BY nombre ASC";
                                        $rs_b = $mysqli->query($sql_b);
                                        while ($rw_b = mysqli_fetch_row($rs_b)) {
                                            echo "\n\t<tr>";
                                            echo "<td class='campos'>";
                                            echo "<a href='javascript:void(0)' id='btnDelB' class='glyphicon glyphicon-trash eliminar campos' onclick='delete_button($rw_b[0])' style='margin-right: 5px;'></a>";
                                            echo "<a href='javascript:void(0)' id='btnEditB' class='glyphicon glyphicon-edit modificar campos' onclick=\"modify_button($rw_b[0],'".ucwords(mb_strtolower($rw_b[1]))."')\"></a>";
                                            echo "</td>";
                                            echo "<td class='campos'>".ucwords(mb_strtolower($rw_b[1]))."</td>";
                                            echo "\n\t</tr>";
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="forma-modal" class="modal-footer"></div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modal_campo" role="dialog" align="center" data-keyboard="false" data-backdrop="static" onload="return clean_n_input()">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="forma-modal" class="modal-header">
                            <button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                            <h4 class="modal-title">Registrar Campo</h4>
                        </div>
                        <div class="modal-body row">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="client-form contenedorForma">
                                    <form action="controller/controllerGSVentana.php?action=insert_input" id="frmInput" class="form-horizontal" method="POST" enctype="multipart/form-data">
                                        <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                                        <div class="form-group" style="margin-bottom: -15px">
                                            <label for="txtNombreC" class="control-label col-lg-3 col-md-3 col-sm-3"><strong class="obligado">*</strong>Nombre Campo:</label>
                                            <input type="text" name="txtNombreC" id="txtNombreC" class="form-control col-sm-1 col-md-1 col-lg-1" placeholder="Nombre Campo" title="Ingrese el nombre del campo" required style="width: 405px;">
                                            <input type="hidden" name="txtIdInput" id="txtIdInput">
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-3 col-md-3 col-lg-3 col-sm-push-9 col-md-push-9 col-lg-push-9">
                                                <button type="submit" id="btnGuardarC" class="btn btn-primary glyphicon glyphicon-floppy-disk guardar shadow" title="Guardar"></button>
                                                <a id="btnModificarC" class="btn btn-primary glyphicon glyphicon-pencil modificar shadow" title="Modificar" onclick="update_input()" disabled></a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="table-responsive">
                                    <table id="tableInput" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <td width="10%"></td>
                                            <td class="cabeza"><strong>Nombre</strong></td>
                                        </tr>
                                        <tr>
                                            <th width="10%"></th>
                                            <th class="cabeza"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $sql_c = "SELECT id_unico, nombre FROM gs_campo ORDER BY nombre ASC";
                                        $rs_c = $mysqli->query($sql_c);
                                        while ($rw_c = mysqli_fetch_row($rs_c)) {
                                            echo "\n\t<tr>";
                                            echo "<td class='campos'>";
                                            echo "<a href='javascript:void(0)' id='btnDelI' class='glyphicon glyphicon-trash eliminar' onclick='delete_input($rw_c[0])' style='margin-right: 5px;'></a>";
                                            echo "<a href='javascript:void(0)' id='btnEditI' class='glyphicon glyphicon-edit modificar' onclick=\"modify_input($rw_c[0],'".ucwords(mb_strtolower($rw_c[1]))."')\"></a>";
                                            echo "</td>";
                                            echo "<td class='campos'>".ucwords(mb_strtolower($rw_c[1]))."</td>";
                                            echo "\n\t</tr>";
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="forma-modal" class="modal-footer"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Informaci&oacute;n</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p id="mensaje"></p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalDel" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>¿Desea eliminar el registro seleccionado?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnDelA" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="js/select2.js"></script>
        <script>
            /**
             * Clase para asignar propiedades de select con campo de busqueda
             */
            $(".select2").select2();

            /**
             * open_modal_insert_button
             * Función para mostrar el modal de registro botón
             */
            function open_modal_insert_button() {
                $("#modal_boton").modal("show");
            }

            /**
             * open_modal_insert_input
             * Función para abrir el modal de registro de campos
             */
            function open_modal_insert_input() {
                $("#modal_campo").modal("show");
            }

            /**
             * reload_page
             * Función para recargar la pagina
             */
            function reload_page() {
                window.location.reload();
            }

            /**
             * url_clean
             * Función para limpiar la url
             */
            function url_clean() {
                window.location = 'registrar_GS_VENTANA.php';
            }

            /**
             * Validación cuando el modal se abra la cabeza de la tabla contenida en el mismo se adapte y tome el ancho de la tabla
             */
            $("#modal_campo").on('shown.bs.modal',function(){
                try{
                    var dataTable = $("#tableInput").DataTable();
                    dataTable.columns.adjust().responsive.recalc();
                } catch(err){}
            });

            /**
             * Validación cuando el modal se abra la cabeza de la tabla contenida en el mismo se adapte y tome el ancho de la tabla
             */
            $("#modal_boton").on('shown.bs.modal',function(){
                try{
                    var dataTable = $("#tableButton").DataTable();
                    dataTable.columns.adjust().responsive.recalc();
                } catch(err){}
            });

            /**
             * modify_button
             * @param int id Id del registro
             * @param string nombre Valor a reemplazar o modificar en la base de datos
             * Recargamos con los valores obtenidos por la consulta los campos e inhabilitamos el botón de guardado y
             * habilitamos el botón de modificado
             */
            function modify_button(id,nombre) {
                $("#txtNombreB").val(nombre);
                $("#txtIdButton").val(id);
                $('#btnGuardarB').attr("disabled", true);
                $('#btnModificarB').attr("disabled", false);
            }

            /**
             * update_button
             * Función para actualizar los valores en la tabla de gs_campo, por lo cual capturamos el valor del campo txtIdButton
             * y validamos que no este vacio, de esa forma evitamos posibles envios erroneos
             */
            function update_button() {
                var txtIdButton = $("#txtIdButton").val();
                if(txtIdButton.length > 0) {
                    var txtNombreB = $("#txtNombreB").val();
                    var result = '';
                    $.ajax({
                        type: 'POST',
                        url:'controller/controllerGSVentana.php',
                        data:{
                            action:'modify_button',
                            txtIdButton:txtIdButton,
                            txtNombreB:txtNombreB
                        },
                        success:function (data, textStatus, jqXHR) {
                            result = JSON.parse(data);
                            if(result == true) {
                                $("#modal_boton").modal('hide');
                                $("#mensaje").html('<p>Información modificada correctamente.</p>');
                                $("#modal").modal('show');
                                $("#btnModal").click(function () {
                                    reload_page();
                                });
                            }else{
                                $("#mensaje").html('<p>No se ha podido modificar la información.</p>');
                                $("#modal").modal('show');
                            }
                        }
                    }).error(function (data, textStatus, errorThrown) {
                        alert('Datos :'+data+', Estado:'+textStatus+' , Hilo del error'+errorThrown);
                    });
                }
            }

            /**
             * delete_button
             * Función para eliminar un registro de la base de datos
             * @param int id Identificador del registro a eliminar
             */
            function delete_button(id) {
                $("#modalDel").modal('show');
                $("#btnDelA").click(function () {
                    var result = '';
                    $.ajax({
                        type: "POST",
                        url: "controller/controllerGSVentana.php",
                        data: {
                            action: 'delete_button',
                            id: id
                        },
                        success: function (data, textStatus, jqXHR) {
                            result = JSON.parse(data);
                            if(result == true) {
                                $("#modal_boton").modal('hide');
                                $("#mensaje").html('<p>Información eliminada correctamente.</p>');
                                $("#modal").modal('show');
                                $("#btnModal").click(function () {
                                    reload_page();
                                });
                            }else{
                                $("#mensaje").html('<p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>');
                                $("#modal").modal('show');
                            }
                        }
                    }).error(function (data, textStatus, errorThrown) {
                        alert('Datos:'+data+', Estado'+textStatus+', Hilo de Error'+errorThrown);
                    });
                });
            }

            /**
             * modify_input
             * Función para llenar los campos con sus respectivos valores
             * @param int id Identificador del registro a modificar
             * @param string nombre Variable o valor a reemplazar en la base de datos
             */
            function modify_input(id, nombre) {
                $("#txtIdInput").val(id);
                $("#txtNombreC").val(nombre);
                $('#btnGuardarC').attr("disabled", true);
                $('#btnModificarC').attr("disabled", false);
            }

            /**
             * update_input
             * Función para actualizar los fatos en la table gs_campo
             */
            function update_input() {
                var txtIdInput = $("#txtIdInput").val();
                if(txtIdInput.length > 0) {
                    var txtNombreC = $("#txtNombreC").val();
                    var result = '';
                    $.ajax({
                        type: 'POST',
                        url:'controller/controllerGSVentana.php',
                        data:{
                            action:'modify_input',
                            txtIdInput:txtIdInput,
                            txtNombreC:txtNombreC
                        },
                        success:function (data, textStatus, jqXHR) {
                            result = JSON.parse(data);
                            if(result == true) {
                                $("#modal_boton").modal('hide');
                                $("#mensaje").html('<p>Información modificada correctamente.</p>');
                                $("#modal").modal('show');
                                $("#btnModal").click(function () {
                                    reload_page();
                                });
                            }else{
                                $("#mensaje").html('<p>No se ha podido modificar la información.</p>');
                                $("#modal").modal('show');
                            }
                        }
                    }).error(function (data, textStatus, errorThrown) {
                        alert('Datos :'+data+', Estado:'+textStatus+' , Hilo del error'+errorThrown);
                    });
                }
            }

            /**
             * Función para eliminar los registros de la tabla gs_campo
             * @param int id Identificador del registro a eliminar
             */
            function delete_input(id) {
                $("#modalDel").modal('show');
                $("#btnDelA").click(function () {
                    var result = '';
                    $.ajax({
                        type: "POST",
                        url: "controller/controllerGSVentana.php",
                        data: {
                            action: 'delete_input',
                            id: id
                        },
                        success: function (data, textStatus, jqXHR) {
                            result = JSON.parse(data);
                            if(result == true) {
                                $("#modal_boton").modal('hide');
                                $("#mensaje").html('<p>Información eliminada correctamente.</p>');
                                $("#modal").modal('show');
                                $("#btnModal").click(function () {
                                    reload_page();
                                });
                            }else{
                                $("#mensaje").html('<p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>');
                                $("#modal").modal('show');
                            }
                        }
                    }).error(function (data, textStatus, errorThrown) {
                        alert('Datos:'+data+', Estado'+textStatus+', Hilo de Error'+errorThrown);
                    });
                });
            }

            /**
             * clean_n_button
             * Función para limpiar los campos en el modal de botón
             */
            function clean_n_button() {
                $("#txtNombreB").val();
            }

            /**
             * clean_n_button
             * Función para limpiar los campos en el modal de campo
             */
            function clean_n_input() {
                $("#txtNombreC").val();
            }

            /**
             * modify_window
             * Función para redireccionar un id existente en la tabla para modificar
             * @param id
             */
            function modify_window(id) {
                window.location = 'registrar_GS_VENTANA.php?window='+md5(id);
            }

            /**
             * delete_window
             * Función para eliminar la ventana, menu ventana y la relacion en ventana_boton, ventana_campo
             * @param idV
             * @param mnV
             */
            function delete_window(idV, mnV) {
                $("#modalDel").modal('show');
                $("#btnDelA").click(function () {
                    var result = '';
                    $.ajax({
                        type: "POST",
                        url: "controller/controllerGSVentana.php",
                        data: {
                            action: 'delete',
                            id: idV, mnV: mnV
                        },
                        success: function (data, textStatus, jqXHR) {
                            result = JSON.parse(data);
                            if(result == true) {
                                $("#modal_boton").modal('hide');
                                $("#mensaje").html('<p>Información eliminada correctamente.</p>');
                                $("#modal").modal('show');
                                $("#btnModal").click(function () {
                                    url_clean();
                                });
                            }else{
                                $("#mensaje").html('<p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>');
                                $("#modal").modal('show');
                            }
                        }
                    }).error(function (data, textStatus, errorThrown) {
                        alert('Datos:'+data+', Estado'+textStatus+', Hilo de Error'+errorThrown);
                    });
                });
            }

            function save_changes(id, mnV) {
                var txtNombre  = $("#txtNombre").val();
                var sltMenuAso = $("#sltMenuAso").val();
                var sltCampo   = $("select[name='sltCampo[]']").val();
                var sltBoton   = $("select[name='sltBoton[]']").val();
                var result = '';
                $.ajax({
                    type: "POST",
                    url: "controller/controllerGSVentana.php",
                    data: {
                        action:'modify',
                        txtNombre:txtNombre,
                        sltMenuAso:sltMenuAso,
                        sltCampo:sltCampo,
                        sltBoton:sltBoton,
                        id_V:id,
                        menuV:mnV
                    }, success: function (data, textStatus, jqXHR) {
                        result = JSON.parse(data);
                        if(result == true) {
                            $("#modal_boton").modal('hide');
                            $("#mensaje").html('<p>Información modificada correctamente.</p>');
                            $("#modal").modal('show');
                            $("#btnModal").click(function () {
                                reload_page();
                            });
                        }else{
                            $("#mensaje").html('<p>No se ha podido modificar la información.</p>');
                            $("#modal").modal('show');
                        }
                    }
                }).error(function (data, textStatus, errorThrown) {
                    alert('data :'+data+', textStatus:'+textStatus+', errorThrown'+errorThrown);
                });
            }
        </script>
    </div>
</body>
</html>
