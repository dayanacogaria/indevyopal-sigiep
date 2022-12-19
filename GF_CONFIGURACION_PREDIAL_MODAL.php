<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#28/02/2018 |Erica G. | Modificar Cuando No se Han Guardado Vigencias 
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
                    <?php $id= $_POST['id'];
                    @session_start();
                    require_once 'Conexion/ConexionPDO.php';
                    $con = new ConexionPDO();
                    $parm_anno = $_SESSION['anno'];
                    $cr = $con->Listar("SELECT codigo, LOWER(nombre) FROM gr_concepto WHERE id_unico = ".$id);
                    echo '<label style="display:inline-block; width:140px">'.$cr[0][0].' - '. ucwords($cr[0][1]).'</label>';
                    $vg = $con->Listar("SELECT id_unico, nombre FROM gf_vigencias_interfaz_predial WHERE parametrizacionanno = $parm_anno");
                    for ($i = 0; $i < count($vg); $i++) {
                        echo '<div style="margin-top: 13px;">';
                        echo '<input type ="hidden" name="conceptop" id ="conceptop" value="'.$id.'">';
                        echo '<label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>'.$vg[$i][1].'</label>';
                        echo '<select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="concepto'.$id.''.$vg[$i][0].'" id="concepto'.$id.''.$vg[$i][0].'" class="select2_single form-control" required>';
                        #Buscar Concepto Configurado
                        $cncf = $con->Listar("SELECT cr.id_unico , 
                        rf.id_unico, LOWER(c.nombre), rb.codi_presupuesto, LOWER(rb.nombre), LOWER(f.nombre) 
                        FROM gf_configuracion_predial cf 
                        LEFT JOIN gf_concepto_rubro cr ON cf.concepto_financiero = cr.id_unico 
                        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                        LEFT JOIN gf_rubro_fuente rf ON cr.rubro = rf.rubro 
                        LEFT JOIN gf_rubro_pptal rb On rf.rubro = rb.id_unico 
                        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
                        WHERE cf.concepto_predial = $id AND cf.vigencia = ".$vg[$i][0]);
                        if(count($cncf)>0) {
                        echo '<option value ="'.$cncf[0][0].','.$cncf[0][1].'">'.ucwords($cncf[0][2]).' - '.$cncf[0][3].' '.ucwords($cncf[0][4]).' - '.ucwords($cncf[0][5]).'</option>';
                        } else {
                            echo '<option value=""> - </option>';
                            $cncf[0][0] =0;   
                            $cncf[0][1] =0;
                        }
                        $cfvm = $con->Listar("SELECT cr.id_unico , 
                        rf.id_unico, LOWER(c.nombre), rb.codi_presupuesto, LOWER(rb.nombre), LOWER(f.nombre) 
                            FROM gf_concepto_rubro cr 
                            LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                            LEFT JOIN gf_rubro_fuente rf ON cr.rubro = rf.rubro 
                            LEFT JOIN gf_rubro_pptal rb On rf.rubro = rb.id_unico 
                            LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
                        WHERE c.parametrizacionanno = $parm_anno AND rf.id_unico IS NOT NULL 
                        AND (rb.tipoclase = 6 OR rb.tipoclase = 8) AND cr.id_unico !=".$cncf[0][0]." AND rf.id_unico !=".$cncf[0][1]);
                        for($z =0; $z < count($cfvm); $z++){
                            echo '<option value ="'.$cfvm[$z][0].','.$cfvm[$z][1].'">'.ucwords($cfvm[$z][2]).' - '.$cfvm[$z][3].' '.ucwords($cfvm[$z][4]).' - '.ucwords($cfvm[$z][5]).'</option>';
                        }
                        echo '</select>';
                        echo '</div>';
                    }?>  
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
  <script>
        function modificarItem(){
            var formData = new FormData($("#formm")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_interfaz_PredialJson.php?action=7",
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
  

