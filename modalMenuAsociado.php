<?php
/**
 * Created by PhpStorm.
 * User: Alexander Numpaque
 * Date: 22/06/2017
 * Time: 5:18 PM
 */
@require_once './Conexion/conexion.php';
echo "\n\t<div class=\"modal fade aso\" id=\"modalMenuAsociado\" role=\"dialog\" align=\"center\" aria-labelledby=\"myModalLabel\" aria-hidden=\"true\">";
echo "\n\t\t<div class=\"modal-dialog\">";
echo "\n\t\t\t<div class=\"modal-content\">";
echo "\n\t\t\t\t<div id=\"forma-modal\" class=\"modal-header\">";
echo "\n\t\t\t\t\t<button type=\"button\" class=\"btn btn-xs close\" aria-label=\"Close\" style=\"color: #fff;\" data-dismiss=\"modal\" ><li class=\"glyphicon glyphicon-remove\"></li></button>";
echo "\n\t\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Opciones Asociadas</h4>";
echo "\n\t\t\t\t</div>";
echo "\n\t\t\t\t<div class=\"modal-body row\">";
echo "\n\t\t\t\t\t<div id=\"cntT\" class=\"table-responsive\">";
echo "\n\t\t\t\t\t\t<table id=\"tablaAso\" class=\"table table-striped table-condensed\" class=\"display\" cellspacing=\"0\" width=\"100%\">";
echo "\n\t\t\t\t\t\t\t<thead>";
echo "\n\t\t\t\t\t\t\t\t<tr>";
echo "\n\t\t\t\t\t\t\t\t\t<td><strong>Opción de Menú</strong></td>";
echo "\n\t\t\t\t\t\t\t\t\t<td><strong>Relación</strong></td>";
echo "\n\t\t\t\t\t\t\t\t</tr>";
echo "\n\t\t\t\t\t\t\t\t<tr>";
echo "\n\t\t\t\t\t\t\t\t\t<th></th>";
echo "\n\t\t\t\t\t\t\t\t\t<th></th>";
echo "\n\t\t\t\t\t\t\t\t</tr>";
echo "\n\t\t\t\t\t\t\t</thead>";
echo "\n\t\t\t\t\t\t\t<tbody>";
if(!empty($_POST['opcion'])) {
    $opcion = $_POST['opcion'];
    $sqlH = "SELECT padre.id_unico, padre.nombre FROM gs_menu_aso familia LEFT JOIN gs_menu padre ON padre.id_unico = familia.menupadre WHERE familia.menuhijo = $opcion";
    $resultH = $mysqli->query($sqlH);
    while ($rowH = mysqli_fetch_row($resultH)) {
        echo "\n\t\t\t\t\t\t\t<tr>";
        echo "\n\t\t\t\t\t\t\t\t<td style='text-align: left'>".ucwords(mb_strtolower($rowH[1]))."</td>";
        echo "\n\t\t\t\t\t\t\t\t<td>Padre</td>";
        echo "\n\t\t\t\t\t\t\t</tr>";
    }

    $sqlS = "SELECT hijo.id_unico, hijo.nombre FROM gs_menu_aso familia LEFT JOIN gs_menu hijo ON familia.menuhijo = hijo.id_unico WHERE familia.menupadre = $opcion";
    $resultS = $mysqli->query($sqlS);
    while ($rowS = mysqli_fetch_row($resultS)) {
        echo "\n\t\t\t\t\t\t\t<tr>";
        echo "\n\t\t\t\t\t\t\t\t<td style='text-align: left'>".ucwords(mb_strtolower($rowS[1]))."</td>";
        echo "\n\t\t\t\t\t\t\t\t<td>Hijo</td>";
        echo "\n\t\t\t\t\t\t\t</tr>";
    }
}
echo "\n\t\t\t\t\t\t\t</tbody>";
echo "\n\t\t\t\t\t\t</table>";
echo "\n\t\t\t\t\t</div>";
echo "\n\t\t\t\t</div>";
echo "\n\t\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">";
echo "\n\t\t\t\t</div>";
echo "\n\t\t\t</div>";
echo "\n\t\t</div>";
echo "\n\t</div>";
?>
<script>
    $(document).ready(function() {
        var i= 1;
        $('#tablaAso thead th').each( function () {
            if(i >= 1){
                var title = $(this).text();
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
                }
                i = i+1;
            } else {
                i = i+1;
            }
        });
        // DataTable
        var table = $('#tablaAso').DataTable({
            "autoFill": true,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No Existen Registros...",
                "info": "Página _PAGE_ de _PAGES_ ",
                "infoEmpty": "No existen datos",
                "infoFiltered": "(Filtrado de _MAX_ registros)",
                "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
            },
            scrollY: 280,
            "scrollX": 100,
            scrollCollapse: true,
            paging: false,
            fixedColumns:   {
                leftColumns: 1
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
                $('input', this.header()).on('keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                });
                i = i+1;
            } else {
                i = i+1;
            }
        });
    });

    $("#modalMenuAsociado").on('shown.bs.modal',function(){
        try{
            var dataTable = $("#tablaAso").DataTable();
            dataTable.columns.adjust().responsive.recalc();
        }catch(err){}
    });
</script>
<style>
    #cntT {
        margin-top: -15px;
        margin-bottom: -15px;
    }
</style>
