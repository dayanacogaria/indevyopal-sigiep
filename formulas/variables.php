<style>
    #tabla2 table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    #tabla2 table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px}
    #btnCerrarModalMov1:hover {
        border: 1px solid #020324;         
    }
    
    #btnCerrarModalMov1{
        box-shadow: 1px 1px 1px 1px #424852;
    }
</style>
<style>
.cabeza{
    white-space:nowrap;
    padding: 20px;
}
.campos{
    padding:-20px;
}
</style> 
<div class="modal fade mov" id="mdlVariables" role="dialog" align="center" aria-labelledby="mdlVariables" aria-hidden="true">
    <div class="modal-dialog" style="height:600px;width:900px">
        <div class="modal-content">
            
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title"  style="font-size: 24; padding: 3px;">Documento</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                    <button type="button" id="btnCerrarModalMov1" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="row">
                    <input type="hidden" id="idPrevio1" value="">
                    <input type="hidden" id="idActual1" value="">
                    <div class="col-sm-12" style="margin-top: 10px;margin-left: 4px;margin-right: 4px">                                                
                        <?php 
                        $totalValor = 0;
                        ?>
                        <div class="table-responsive contTabla" >
                            <table id="tabla2" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">
                                <thead style="position: relative;overflow: auto;width: 100%;">
                                    <tr>
                                        <td class="oculto">Identificador</td>
                                        <td class="cabeza"><strong>Variable</strong></td>
                                        <td class="cabeza"><strong>Valor</strong></td>
                                        
                                    </tr>
                                    <tr>
                                        <th class="oculto">Identificador</th>
                                        <th class="cabeza">Variable</th>
                                        <th class="cabeza">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $variables = $_POST['resultado'];
                                    $numVariables = $_POST['variables'];
                                    ?>
                                    <?php for($i=0; $i<$numVariables;$i++){ ?>
                                    <tr>
                                        <td class="oculto"></td>
                                        <td>
                                           <?php echo $variables[$i];?>
                                        </td>
                                        <td><input name="<?php echo $variables[$i];?>" id="<?php echo $variables[$i];?>"></td>
                                    </tr>
                                    <?php } ?>   
                                </tbody>
                            </table>
                            <a href="#" onclick="calcular()" class="btn btn-primary sombra" style=" margin-top: 10px; margin-bottom: 10px; margin-left:0px">Calcular</a>
                        </div>                                                                        
                    </div>                    
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">                
            </div>
        </div>
    </div>
</div>
<script>
 function calcular(){
    <?php if(!empty($_POST['variables'])) { ?> 
    resultado = [],
    numVariables= <?php echo $numVariables ?>;
    <?php for ( $i=0; $i<$numVariables; $i++){ ?>
        resultado1=document.getElementById('<?php echo $variables[$i]?>').value;
        resultado.push(resultado1);
    <?php } ?>
        variables2(resultado);
        $(".mdlVariables").modal('hide');
        $(".mov").modal('hide');
    <?php } ?>
 }
</script>
<script type="text/javascript" >
    $("#btnCerrarModalMov1").click(function(){
       document.location.reload();
    });
    
    $("#mdlVariables").on('shown.bs.modal',function(){
        var dataTable = $("#tabla2").DataTable();
        dataTable.columns.adjust().responsive.recalc();
    });
</script>
<script type="text/javascript">
  $(document).ready(function() {
     var i= 1;
    $('#tabla2 thead th').each( function () {
        if(i != 1){ 
        var title = $(this).text();
        switch (i){
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
          case 6:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 7:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 8:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 9:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 10:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 11:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 12:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 13:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 14:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 15:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 16:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 17:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 18:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 19:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
  
        }
        i = i+1;
      }else{
        i = i+1;
      }
    } );
 
    // DataTable
   var table = $('#tabla2').DataTable({
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