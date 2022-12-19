<?php
require_once './Conexion/conexion.php';
$asociado = ""; $ficha = ""; $detalle = "0"; $cantidad1 = ""; $nombre_p = ""; $cant = 0; $producto = 0;
if(!empty($_POST['producto'])){
    $producto   = $_POST['producto'];
}

if(!empty($_POST['detalle'])){
    $detalle    = $_POST['detalle'];
}

if(!empty($_POST['canitdad'])){
    $cantidad1  = $_POST['cantidad'];
}

if(!empty($_POST['ficha'])){
    $ficha      = $_POST['ficha'];
    $sqlF = "SELECT descripcion FROM gf_ficha WHERE id_unico = $ficha";
    $resultF = $mysqli->query($sqlF);
    $rowF = mysqli_fetch_row($resultF);
    $nombre_p = $rowF[0];
}

if(!empty($_POST['asociado']) ) {
    $asociado   = $_POST['asociado'];

    $sql = "SELECT COUNT(producto) FROM gf_movimiento_producto WHERE detallemovimiento = $asociado";
    $result = $mysqli->query($sql);
    if($result == true && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_row($result);
        $cant = $row[0];
    }
}
echo "\n\t<div class=\"modal fade salida\" id=\"modalSalidaProducto\" role=\"dialog\" align=\"center\" aria-labelledby=\"myModalLabel\" aria-hidden=\"true\">";
echo "\n\t\t<div class=\"modal-dialog\">";
echo "\n\t\t\t<div class=\"modal-content\">";
echo "\n\t\t\t\t<div id=\"forma-modal\" class=\"modal-header\">";
echo "\n\t\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">$nombre_p</h4>";
echo "\n\t\t\t\t</div>";
echo "\n\t\t\t\t<div class=\"modal-body row\">";
echo "\n\t\t\t\t\t<div id=\"cntT\" class=\"table-responsive\">";
echo "\n\t\t\t\t\t\t<table id=\"tablaSalida\" class=\"table table-striped table-condensed\" class=\"display\" cellspacing=\"0\" width=\"100%\">";
echo "\n\t\t\t\t\t\t\t<thead>";
echo "\n\t\t\t\t\t\t\t\t<tr>";
echo "\n\t\t\t\t\t\t\t\t\t<td><strong>Serie</strong></td>";
echo "\n\t\t\t\t\t\t\t\t\t<td><strong>Descripción</strong></td>";
echo "\n\t\t\t\t\t\t\t\t\t<td><strong>Valor</strong></td>";
echo "\n\t\t\t\t\t\t\t\t\t<td style=\"7%\"><strong></strong></td>";
echo "\n\t\t\t\t\t\t\t\t</tr>";
echo "\n\t\t\t\t\t\t\t\t<tr>";
echo "\n\t\t\t\t\t\t\t\t\t<th></th>";
echo "\n\t\t\t\t\t\t\t\t\t<th></th>";
echo "\n\t\t\t\t\t\t\t\t\t<th></th>";
echo "\n\t\t\t\t\t\t\t\t\t<th style=\"7%\"></th>";
echo "\n\t\t\t\t\t\t\t\t</tr>";
echo "\n\t\t\t\t\t\t\t</thead>";
echo "\n\t\t\t\t\t\t\t<tbody>";
$e = 0; $g = 0;
$sqlUL = "SELECT MAX(detallemovimiento) FROM gf_movimiento_producto WHERE detallemovimiento =$asociado"; #Obtenemos el ultimo detalle relacionado al producto
$resultUL = $mysqli->query($sqlUL);
while ($rowUL = mysqli_fetch_row($resultUL)) {
    $sqlDD = "SELECT    mop.producto, pes.valor 'SERIE', pro.descripcion, dtm.valor
     FROM               gf_movimiento_producto mop
     LEFT JOIN          gf_producto pro                 ON mop.producto           = pro.id_unico
     LEFT JOIN          gf_producto_especificacion pes  ON pro.id_unico           = pes.producto
     LEFT JOIN          gf_ficha_inventario fin         ON pes.fichainventario    = fin.id_unico
     LEFT JOIN          gf_detalle_movimiento dtm       ON mop.detallemovimiento  = dtm.id_unico
     LEFT JOIN          gf_movimiento mov               ON dtm.movimiento         = mov.id_unico
     LEFT JOIN          gf_dependencia dep              ON mov.dependencia        = dep.id_unico
     LEFT JOIN          gf_tipo_movimiento tpm          ON mov.tipomovimiento     = tpm.id_unico
     WHERE              fin.elementoficha     = 6
     AND                mop.detallemovimiento = /*$asociado#*/$rowUL[0] 
     AND (mop.producto NOT IN (SELECT mpr.producto FROM gf_movimiento_producto mpr 
        LEFT JOIN gf_detalle_movimiento dma ON mpr.detallemovimiento = dma.id_unico 
        LEFT JOIN gf_movimiento m ON dma.movimiento = m.id_unico 
        LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
        WHERE tm.clase =3) OR mop.producto IN (SELECT mpr.producto FROM gf_movimiento_producto mpr WHERE mpr.detallemovimiento = $detalle))";
    $resultDD = $mysqli->query($sqlDD);
    while ($rowDD = mysqli_fetch_row($resultDD)) {
        $sqlEE = "SELECT producto FROM gf_movimiento_producto WHERE detallemovimiento = $detalle AND producto = $rowDD[0]";
        $resultEE = $mysqli->query($sqlEE);
        $rows = mysqli_num_rows($resultEE);
        echo "\n\t\t\t\t\t\t\t\t<tr id='row$rowDD[0]'>";
        echo "\n\t\t\t\t\t\t\t\t\t<td>$rowDD[1]</td>";
        echo "\n\t\t\t\t\t\t\t\t\t<td class=\"text-left\">".ucwords(mb_strtolower($rowDD[2]))."</td>";
        echo "\n\t\t\t\t\t\t\t\t\t<td class=\"text-right\">".number_format($rowDD[3],2,',','.')."</td>";
        echo "\n\t\t\t\t\t\t\t\t\t<td class=\"text-center\">";
        if($rows > 0) {
            echo "\n\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"chkSalida\" id=\"chkSalida".$rowDD[0]."\" value=\"".$rowDD[0]."\" disabled checked readonly/>";
            $e++;
        }else{
            $g++;
            echo "\n\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"chkSalida\" id=\"chkSalida".$rowDD[0]."\" value=\"".$rowDD[0]."\"/>";
        }
        echo "\n\t\t\t\t\t\t\t\t\t</td>";
        echo "\n\t\t\t\t\t\t\t\t</tr>";
    }
}
echo "\n\t\t\t\t\t\t\t</tbody>";
echo "\n\t\t\t\t\t\t</table>";
echo "\n\t\t\t\t\t</div>";
echo "\n\t\t\t\t</div>";
echo "\n\t\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">";
if($cant == $e) {
    echo "\n\t\t\t\t\t<a href=\"javascript:void(0)\" class=\"btn btn-default glyphicon glyphicon-remove\" data-dismiss=\"modal\"></a>";
}else{
    echo "\n\t\t\t\t\t<a href=\"javascript:void(0)\" onclick=\"get_markeds_all_enabled()\" class=\"btn btn-default glyphicon glyphicon-ok\" title='Marcar y desmarcar todos los habilitados'></a>";
    echo "\n\t\t\t\t\t<a href=\"javascript:void(0)\" id=\"btnGuardarSalida\" onclick=\"get_inputs_chekeds()\" class=\"btn btn-default glyphicon glyphicon-floppy-disk\"></a>";
    echo "\n\t\t\t\t\t<a href=\"javascript:void(0)\" class=\"btn btn-default glyphicon glyphicon-remove\" data-dismiss=\"modal\" title='Salir'></a>";
}
echo "\n\t\t\t\t</div>";
echo "\n\t\t\t</div>";
echo "\n\t\t</div>";
echo "\n\t</div>";
?>
<script>
    $(document).ready(function() {
        var i= 1;
        $('#tablaSalida thead th').each( function () {
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
        var table = $('#tablaSalida').DataTable({
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

    $("#modalSalidaProducto").on('shown.bs.modal',function(){
        try{
            var dataTable = $("#tablaSalida").DataTable();
            dataTable.columns.adjust().responsive.recalc();
        }catch(err){}
    });

    /**
     * get_inputs_chekeds
     *
     * Función para obtener le valor de los checkboxs marcados que no estan deshabilitados para registrar por medio de un
     * envio ajax
     */
    function get_inputs_chekeds() {
        var selected = '';                                                          //Inicializamos la variable selected
        //Capturamos los valores de los campos tipo checkbox donde este este marcado
        $('input[type=checkbox]').each(function(){
            if (this.checked && $(this).is(':enabled')) {
                selected += $(this).val()+',';
            }
        });
        if(selected.length > 0) {
            var select = selected.substr(0, (selected.length) - 1);                     //Al String le quitamos la ultima ,
            var form_data = {
                mov:14,
                seleccionados:select,
                detalle:<?php echo $detalle ?>
            };
            var result = '';
            $.ajax({
                type:'POST',
                url:"consultasBasicas/consulta_mov.php",
                data:form_data,
                success: function (data, textStatus, jqXHR) {
                    result = JSON.parse(data);
                    if(result == true) {
                        $("#modalSalidaProducto").modal('hide');
                        $("#modalGuardar").modal('show');
                    } else {
                        $("#modalSalidaProducto").modal('hide');
                        $("#modalNoGuardo").modal('show');
                    }
                    console.log(data);
                }
            }).error(function (jqXHR, textStatus, errorThrown) {
                alert('XHR :'+jqXHR+' - textStatus :'+textStatus+' - errorThrown :'+errorThrown);
            });
        }
    }

    /**
     * selected
     *
     * Función para seleccionar la fila
     *
     * @param int x Id del registro
     */
    function selected(x) {
        if($("#chkSalida"+x).is(':checked')) {                          //Validamos que el checkbox este seleccionado
            $('#row'+x).css('background-color', 'rgb(51,122,183)');     //Cambiamos el color de la fila por azul
            $('#row'+x).css('color', '#fff');                           //Cambiamos el colo de la letra a blanco
        }else {
            $('#row'+x).css('background-color', 'transparent');
            $('#row'+x).css('color', '#000');
        }
    }

    /**
     * get_markeds_all_enabled()
     *
     * Función para marcar y desmarcar todos los checkbox que estén habilitados
     */
    function get_markeds_all_enabled() {
        $('input[type=checkbox]').each(function(){
            if (this.checked && $(this).is(':enabled')) {
                this.checked = false;
            } else {
                this.checked = true;
            }
        });
    }
</script>
<style>
    #cntT {
        margin-top: -15px;
        margin-bottom: -15px;
    }
</style>