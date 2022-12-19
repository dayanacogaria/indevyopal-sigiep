<?php
########################################################################################
#       ***************    Modificaciones *************** #
########################################################################################
#04/04/2019 | Creado
########################################################################################
require_once('Conexion/ConexionPDO.php');
require_once('Conexion/ConexionPDO.php');
require 'jsonPptal/funcionesPptal.php';
require_once('head_listar.php');
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$nanno      = anno($anno);
$rowc = $con->Listar("SELECT id_unico, razonsocial, numeroidentificacion, digitoverficacion 
    FROM gf_tercero t 
    WHERE compania = $compania AND id_unico !=$compania");
?>
<html>
    <head>
        <title>Configuración Consolidación</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script> 
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="js/md5.pack.js"></script>
        <script>
        $(document).ready(function() {
            var i= 0;
            $('#tableO thead th').each( function () {
                if(i => 0) {
                    var title = $(this).text();
                    switch (i){
                        case 0:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 1:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 2:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 3:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 4:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 5:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 6:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                         case 7:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;    
                        
                    }
                    i = i+1;
                } else {
                    i = i+1;
                }
            });
            
            var i2= 0;
            $('#tableO2 thead th').each( function () {
                if(i2 => 0) {
                    var title = $(this).text();
                    switch (i2){
                        case 0:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 1:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 2:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 3:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 4:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 5:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 6:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 7:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;    
                            
                            
                            
                    }
                    i2 = i2+1;
                } else {
                    i2 = i2+1;
                }
            });
            
            
            
            // DataTable
            var table = $('#tableO').DataTable({
                "autoFill": true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                },
                scrollY: 220,
                "scrollX": true,
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
            
            // DataTable
            var table2 = $('#tableO2').DataTable({
                "autoFill": true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                },
                scrollY: 120,
                "scrollX": true,
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
                if(i!=0) {
                    $( 'input', this.header() ).on( 'keyup change', function () {
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
            
            var i2 = 0;
            table2.columns().every( function () {
                var that = this;
                if(i2!=0) {
                    $( 'input', this.header() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                    i2 = i2+1;
                } else {
                    i2 = i2+1;
                }
            });
        });
        </script>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Configuración Consolidación</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <div  style="text-align:right">
                            <input style="margin-top:10px; margin-bottom: 10px;" type="checkbox" onclick="if (this.checked) marcar(true); else marcar(false);" /><strong style=" font-size: 12px;">&nbsp;&nbsp;Marcar/Desmarcar Todos</strong>
                        </div>
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tableO" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="max-height: 200px">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="30px" align="center"></td>
                                        <td><strong>Compañia</strong></td>
                                        <td><strong>Consolidar</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Compañia</th>
                                        <th>Consolidar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 

                                        for ($i = 0; $i < count($rowc); $i++) {
                                            
                                        echo '<tr>';
                                        echo '<td></td>';
                                        echo '<td>';
                                        $cn = $con->Listar("SELECT consolidado FROM gf_consolidacion WHERE compania = ".$rowc[$i][0]);
                                        if(!empty($cn[0][0]) && $cn[0][0]==1){
                                            echo '<input type="checkbox" checked="checked" name="seleccion'.$rowc[$i][0].'" id="seleccion'.$rowc[$i][0].'" class="check_select" title="Seleccione" required style="width: 35%" onchange="cambiovalor('.$rowc[$i][0].')"/>';
                                        } else { 
                                            echo '<input type="checkbox" name="seleccion'.$rowc[$i][0].'" id="seleccion'.$rowc[$i][0].'" class="check_select" title="Seleccione" required style="width: 35%" onchange="cambiovalor('.$rowc[$i][0].')"/>';
                                        }
                                        echo '</td>';
                                        echo '<td>'.$rowc[$i][1].' '.$rowc[$i][2].' - '.$rowc[$i][3].'</td>';
                                        echo '<td></td>';
                                        echo '</tr>';
                                    }?>
                                </tbody>
                            </table>      
                            
                        </div>            
                    </div> 
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensaje" name="mensaje"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalEliminar" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensajeEliminar" name="mensajeEliminar"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" id="btnCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script src="js/select/select2.full.js"></script>
        <script>
            $(document).ready(function () {
                $(".select2_single").select2({
                    allowClear: true,
                });
            });
        </script>
        <script>
            function cambiovalor(id){
                var ncheck = "seleccion"+id;
                let tipo  = 0;
                if($("#"+ncheck).prop('checked')){
                    tipo = 1;
                } else {
                    tipo = 2;

                }
                var form_data={id:id,tipo:tipo,
                    action:17 }
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_distribucion_costosJson.php",
                    data:form_data,
                    success: function (data) {
                        console.log(data);
                        
                    }
                })
            }
        </script>
        <script type="text/javascript">
            function marcar(status) 
            {
                
                var tabla1 = document.getElementById("tableO");
                var eleNodelist1 = tabla1.getElementsByTagName("input");
                for (i = 0; i < eleNodelist1.length; i++) {
                    if (eleNodelist1[i].type == 'checkbox'){
                        if (status == null) {
                            eleNodelist1[i].checked = !eleNodelist1[i].checked;
                        }else {
                            eleNodelist1[i].checked = status;
                        }
                    }

                }
                jsShowWindowLoad('Actualizando...');
                let tipo  = 0;
                if(status==true){
                    tipo = 1;
                } else {
                    tipo = 2;
                }

                var form_data={id:i,tipo:tipo,
                    action:19 }
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_distribucion_costosJson.php",
                    data:form_data,
                    success: function (data) {
                        console.log(data);
                    }
                })
                jsRemoveWindowLoad();
            }
        </script>
    </body>
</html>
</html>

