<style>
    .valorLabel{
    font-size: 10px;
    }
    .valorLabel:hover{
        cursor: pointer;
        color:#1155CC;
    }
    /*td de la tabla*/
    .campos{
        padding: 0px;
        font-size: 10px
    }
</style>
<div class="modal fade" data-backdrop=”static” data-keyboard=”false” id="modalEntreMeses" role="dialog" align="center" aria-labelledby="mdlDetalleMovimiento" aria-hidden="true" >
    <div class="modal-dialog" style="height:600px;width:850px">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title"  style="font-size: 24; padding: 3px;">Movimientos Efectuados</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                    <button type="button" id="btnCerrar" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
            <div class="modal-body" style="margin-top: 8px">                                
                <div class="row">
                    <div class="col-sm-12">
                        <div class="client-form contenedorForma" style="margin-top:-10px;margin-right:-3px">
                            <form action="json/registrarDetalleComprobanteMovimientoJson.php" class="form-horizontal" style="margin-left:25px">
                                <p align="center" class="parrafoO" style="margin-bottom:-0.00005em"></p>
                                <div class="form-group form-inline" >                                    
                                    <label for="cuenta" class="col-sm-2 control-label">
                                        Cuenta:
                                    </label>
                                    <input type="text" name="txtCuenta" id="cuenta" class="form-control col-sm-2 input-sm" title="Número de cuenta" style="width:100px;height:26px;cursor: pointer" required="" tabindex="1" readonly=""/>
                                    <label for="txtFechaInicial" class="col-sm-2 control-label">
                                        Fecha Inicial:
                                    </label>
                                    <input class="col-sm-2 input-sm" value="<?php echo date('d/m/Y'); ?>" type="text" name="txtFechaInicial" id="txtFechaInicial" class="form-control" style="width:100px;height:26px" title="Ingrese la fecha incial" placeholder="Fecha" required tabindex="2">                                                                                                
                                    <label for="txtFechaInicial" class="col-sm-2 control-label">
                                        Fecha Final:
                                    </label>
                                    <input class="col-sm-2 input-sm" value="<?php echo date('d/m/Y'); ?>" type="text" name="txtFechaFinal" id="txtFechaFinal" class="form-control" style="width:100px;height:26px" title="Ingrese la fecha final" placeholder="Fecha" required tabindex="3">                                                                                                
                                </div><br/>
                                <div class="form-group form-inline" style="margin-top: -30px;margin-bottom: -1px">
                                    <label for="txtInicial" class="col-sm-2 control-label">
                                        Saldo Inicial:
                                    </label>
                                    <input type="text" name="txtInicial" id="txtInicial" class="form-control col-sm-2 input-sm" title="Valor inicial" style="width:100px;height:26px;padding:2px;" tabindex="4"/>  
                                    <label for="txtFinal" class="col-sm-2 control-label">
                                        Saldo Final:
                                    </label>
                                    <input type="text" name="txtFinal" id="txtFinal" class="form-control col-sm-2 input-sm" title="Valor final" style="width:100px;height:26px;padding:2px" tabindex="5"/>  
                                    <a href="javascript:void(0)" id="btnMovimientos" class="btn btn-primary sombra" style="margin-top:4px;margin-left: 100px;">
                                        <li class="glyphicon glyphicon-repeat" ></li>
                                    </a>
                                </div>
                            </form>
                        </div>    
                    </div>
                    <div class="col-sm-12" style="margin-top: 10px;">
                        <div class="table-responsive " >
                            <table id="tablaMovimientos" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">
                                <thead>
                                    <tr>                                        
                                        <td class="cabeza"><strong>Tipo</strong></td>
                                        <td class="cabeza"><strong>Número</strong></td>
                                        <td class="cabeza"><strong>Fecha</strong></td>
                                        <td class="cabeza"><strong>Valor Débito</strong></td>
                                        <td class="cabeza"><strong>Valor Crédito</strong></td>
                                        <td class="cabeza"><strong>Descripción</strong></td>
                                        <td class="cabeza"><strong>Tercero</strong></td>
                                        <td class="cabeza"><strong>Centro Costo</strong></td>
                                        <td class="cabeza"><strong>Proyecto</strong></td>
                                    </tr>
                                    <tr>                                        
                                        <th class="cabeza">Tipo</th>
                                        <th class="cabeza">Número</th>
                                        <th class="cabeza">Fecha</th>
                                        <th class="cabeza">Valor Débito</th>
                                        <th class="cabeza">Valor Crédito</th>
                                        <th class="cabeza">Descripción</th>
                                        <th class="cabeza">Tercero</th>
                                        <th class="cabeza">Centro Costo</th>
                                        <th class="cabeza">Proyecto</th>
                                    </tr>
                                </thead>                                 
                                <tbody id="cuerpo">                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> 
                <script type="text/javascript"> 
                    $("#btnMovimientos").click(function(){                                                                                                                                                              
                        $("#tablaMovimientos").dataTable().fnDestroy();                            
                        var table = $('#tablaMovimientos').DataTable( {
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
                               'orderable':false
                            }],
                            ajax: {
                                url: 'consultasBasicas/movimientos_efectuados.php?codigo='+$('#cuenta').val()+"&fechaI="+$('#txtFechaInicial').val()+"&fechaF="+$('#txtFechaFinal').val(),
                                dataSrc: 'data'
                            },
                            columns: [
                                {data:'Tipo'},
                                {data:'Número'},
                                {data:'Fecha'},
                                {data:'Valor Débito'},
                                {data:'Valor Crédito'},
                                {data:'Descripción'},
                                {data:'Tercero'},
                                {data:'Centro Costo'},
                                {data:'Proyecto'}
                            ]
                        } );
                        
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
                        
                        
                    });
                   
                    $("#btnMovimientos").click(function(){
                        var form_data = {
                            saldo:1,
                            cuenta:$('#cuenta').val(),
                            fecha:$('#txtFechaInicial').val()
                        };

                        $.ajax({
                            type: 'POST',
                            url: "consultasBasicas/consultaSaldos.php",
                            data: form_data,
                            success: function (data) {
                                $("#txtInicial").val(data);
                            }
                        });                       
                    });
                    
                    $("#btnMovimientos").click(function(){
                        var form_data = {
                            saldo:2,
                            cuenta:$('#cuenta').val(),
                            fechaFin:$('#txtFechaFinal').val()
                        };
                        $.ajax({
                            type: 'POST',
                            url: "consultasBasicas/consultaSaldos.php",
                            data: form_data,
                            success: function (data) {
                                $("#txtFinal").val(data);                                
                            }
                        });                       
                    });
                </script>                
            </div>                
            <div id="forma-modal" class="modal-footer"></div>
        </div>            
    </div>
</div>

