<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#23/07/2018 |Erica G. | Archivo Creado
#######################################################################################################
?>
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
<div class="modal fade movi" id="mdlModificar" role="dialog" align="center" aria-labelledby="mdlModificar" aria-hidden="true">
    <div class="modal-dialog" style="height:600px;width:900px">
        <div class="modal-content">
            
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title"  style="font-size: 24; padding: 3px;">Modificar Configuración</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                    <button type="button" id="btnCerrarModalMov1" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="row">
                    <form name="formm" id="formm" method="POST" action="Javascript:modificarItem()">
                    <div class="col-sm-12" style="margin-top: 10px;margin-left: 4px;margin-right: 4px">                                                
                    <?php 
                    @session_start();
                    require_once 'Conexion/ConexionPDO.php';
                    $con = new ConexionPDO();
                    $parm_anno = $_SESSION['anno'];
                    # Consultar configuración Guardada
                    $id = $_REQUEST['id'];
                    
                    $cr = $con->Listar("SELECT cf.id_unico, 
                                cf.concepto_comercio, 
                                cc.codigo, LOWER(cc.descripcion), 
                                cr.id_unico, LOWER(c.nombre), 
                                rf.id_unico, 
                                rb.codi_presupuesto, LOWER(rb.nombre), LOWER(f.nombre), 
                                LOWER(vg.nombre), vg.valor, cf.porcentaje, vg.id_unico 
                                FROM gf_configuracion_comercio cf 
                                LEFT JOIN gc_concepto_comercial cc ON cf.concepto_comercio =cc.id_unico 
                                LEFT JOIN gf_concepto_rubro cr ON cf.concepto_financiero = cr.id_unico 
                                LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                                LEFT JOIN gf_rubro_fuente rf ON cf.rubro_fuente = rf.id_unico 
                                LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
                                LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
                                LEFT JOIN gf_vigencias_interfaz_reteica vg ON cf.vigencia_ica = vg.id_unico  
                                WHERE cf.id_unico = ".$id);
                    #****** Buscar Si El Porcentaje ==100*****#
                    $p = $con->Listar("SELECT SUM(cf.porcentaje) FROM gf_configuracion_comercio cf 
                            LEFT JOIN gf_vigencias_interfaz_reteica vg ON cf.vigencia_ica = vg.id_unico 
                            WHERE vg.parametrizacionanno = $parm_anno AND cf.vigencia_ica = ".$cr[0][13]."
                            AND cf.concepto_comercio = ".$cr[0][1]." AND cf.id_unico !=$id");
                    $por = $p[0][0];
                    $max = 100;
                    if($por < 100){
                        $max = 100 -$por;
                    }
                    echo '<label style="display:inline-block;">'.$cr[0][2].' - '. ucwords($cr[0][3]).'</label><br/>';
                    echo '<label style="display:inline-block;">'.$cr[0][11].' - '. ucwords($cr[0][10]).'</label>';
                    echo '<div style="margin-top: 13px;">';
                    echo '<input type ="hidden" name="id" id ="id" value="'.$id.'">';
                    echo '<input type ="hidden" name="maxm'.$id.'" id ="maxm'.$id.'" value="'.$max.'">';
                    echo '<label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Concepto Financiero</label>';
                    echo '<select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="concepto'.$id.'" id="concepto'.$id.'" class="select2_single form-control" required>';
                    echo '<option value ="'.$cr[0][4].','.$cr[0][6].'">'.ucwords($cr[0][5]).' - '.$cr[0][7].' '.ucwords($cr[0][8]).' - '.ucwords($cr[0][9]).'</option>';
                    $cfvm = $con->Listar("SELECT cr.id_unico , 
                    rf.id_unico, LOWER(c.nombre), rb.codi_presupuesto, LOWER(rb.nombre), LOWER(f.nombre) 
                        FROM gf_concepto_rubro cr 
                        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                        LEFT JOIN gf_rubro_fuente rf ON cr.rubro = rf.rubro 
                        LEFT JOIN gf_rubro_pptal rb On rf.rubro = rb.id_unico 
                        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
                    WHERE c.parametrizacionanno = $parm_anno AND rf.id_unico IS NOT NULL 
                    AND (rb.tipoclase = 6 OR rb.tipoclase = 8) AND rf.id_unico !=".$cr[0][6]);
                    for($z =0; $z < count($cfvm); $z++){
                        echo '<option value ="'.$cfvm[$z][0].','.$cfvm[$z][1].'">'.ucwords($cfvm[$z][2]).' - '.$cfvm[$z][3].' '.ucwords($cfvm[$z][4]).' - '.ucwords($cfvm[$z][5]).'</option>';
                    }
                    echo '</select>';
                    echo '</div>';
                    echo '<div style="margin-top: 13px;">';
                    echo '<label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Porcentaje</label>';
                    echo '<input style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="porcentajem'.$id.'" id="porcentajem'.$id.'" class="form-control" onkeypress="return validarNumM('.$id.')"  required value="'.$cr[0][12].'"/>';
                    echo '</div>';
                    
                    ?>  
                    <br/>
                    <button type="submit" id="btnGuardar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Guardar" >
                        <li class="glyphicon glyphicon-floppy-disk"> Guardar
                        </li>
                    </button>
                    </div>   
                    </form>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">                
            </div>
        </div>
    </div>
</div>
 <script type="text/javascript" >
  $("#mdlModificar").draggable({
      handle: ".modal-header"
  });
</script>
<script type="text/javascript" >
    $("#btnCerrarModalMov1").click(function(){
       document.location.reload();
    });
    
    $("#mdlModificar").on('shown.bs.modal',function(){
        try{
            var dataTable = $("#tabla21").DataTable();
            dataTable.columns.adjust().responsive.recalc();   
        }catch(err){}        
    });
</script>
<script type="text/javascript">
  $(document).ready(function() {
     var i= 1;
    $('#tabla21 thead th').each( function () {
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
   var table = $('#tabla21').DataTable({
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
<script src="js/select/select2.full.js"></script>
<script>
      $(document).ready(function () {
          $(".select2_single").select2({

              allowClear: true
          });


      });
  </script>
   <!----**Funcion Validar Porcentaje**---->
    <script>
        function validarNumM(id){
        event = event || window.event;
        var charCode = event.keyCode || event.which;
        var first = (charCode <= 57 && charCode >= 48);
        var numero = $("#porcentajem"+id).val();
        console.log('ND'+numero);
        var char = parseFloat(String.fromCharCode(charCode));
        var num = parseFloat(numero+char);
        var com = parseFloat($("#maxm"+id).val());
        var match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
        var dec = match[0].length;
        console.log('NC'+com);
        if(dec<=3){
            if(num <= com){
                if (charCode ==46){
                    var element = event.srcElement || event.target;
                    if(element.value.indexOf('.') == -1){
                    return (charCode =46);
                    }else{
                       return first; 
                    }
                    } else {
                    return first;
                }
            } else {
                if(num <= com){
                    return first;
                }else{
                    return false;
                }
            }
        } else { 
            return false;
        }
    }
    </script> 
  <script>
        function modificarItem(){
            var formData = new FormData($("#formm")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_interfaz_ComercioJson.php?action=6",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    console.log(response);
                    if(response==0){
                        $("#mensaje1").html('Información Modificada Correctamente');  
                        $("#modalMensajes1").modal('show'); 

                    } else {
                        $("#mensaje1").html('No Se Ha Podido Modificar La Información');  
                        $("#modalMensajes1").modal('show'); 
                    }
                }
            })
        }   
    </script> 
    <div class="modal fade" id="modalMensajes1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje1" name="mensaje"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $("#Aceptar1").click(function(){
           document.location.reload();
        });
    </script> 
  

