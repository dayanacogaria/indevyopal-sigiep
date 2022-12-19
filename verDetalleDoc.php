<?php
######## MODIFICACIONES ########
#31/01/2017 | 12:30 | ERICA GONZALEZ
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
<div class="modal fade mov1" id="mdlDetalleMovimiento1" role="dialog" align="center" aria-labelledby="mdlDetalleMovimiento1" aria-hidden="true">
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
                                        <td class="cabeza"><strong>Tipo Comprobante</strong></td>
                                        <td class="cabeza"><strong>N° Comprobante</strong></td>
                                        <td class="cabeza"><strong>Tipo Documento</strong></td>
                                        <td class="cabeza"><strong>Número</strong></td>
                                        <td class="cabeza"><strong>Fecha Vencimiento</strong></td>
                                        <td class="cabeza"><strong>Valor</strong></td>
                                        <td class="cabeza"><strong>Documento</strong></td>
                                        
                                    </tr>
                                    <tr>
                                        <th class="oculto">Identificador</th>
                                        <th class="cabeza">Tipo Comprobante</th>
                                        <th class="cabeza">N° Comprobante</th>
                                        <th class="cabeza">Tipo Documento</th>
                                        <th class="cabeza">Número</th>
                                        <th class="cabeza">Fecha Vencimiento</th>
                                        <th class="cabeza">Valor</th>
                                        <th class="cabeza">Documento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php   
                                        $id= $_POST['id'];
                                        $sql11 = "SELECT
                                                    dtm.id_unico,
                                                    dtm.tipodocumento,
                                                    tpd.id_unico,
                                                    tpd.nombre,
                                                    dtm.numero,
                                                    dtm.fechavencimiento,
                                                    dtm.valor,
                                                    dtm.ruta,
                                                    dc.detallecomprobantepptal,
                                                    cnt.numero,
                                                    tc.nombre
                                                  FROM
                                                    gf_detalle_comprobante_mov dtm
                                                  LEFT JOIN
                                                    gf_tipo_documento tpd ON dtm.tipodocumento = tpd.id_unico
                                                  LEFT JOIN
                                                    gf_detalle_comprobante dc ON dtm.comprobantecnt = dc.id_unico
                                                  LEFT JOIN
                                                    gf_comprobante_cnt cnt ON dc.comprobante = cnt.id_unico
                                                  LEFT JOIN
                                                    gf_tipo_comprobante tc ON cnt.tipocomprobante = tc.id_unico
                                         WHERE dtm.comprobantecnt=$id";

                                        $result11 = $mysqli->query($sql11);
                                    while($row11 = $result11->fetch_row()){ ?>
                                    <tr>
                                
                                        <td class="campos oculto">
                                            <?php echo $row11[0]; ?>
                                        </td>
                                        <td class="campos">
                                            <?php echo ucwords(strtolower($row11[10]))?>
                                        </td>
                                        <td class="campos">
                                            <?php echo ucwords(strtolower($row11[9]))?>
                                        </td>
                                        <td class="campos">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="lbltipodocumento'.$row11[0].'">'.(ucwords(strtolower($row11[3]))).'</label>'; ?>
                                            
                                        </td>
                                        <td class="campos">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblnumerodocumento'.$row11[0].'">'.(ucwords(strtolower($row11[4]))).'</label>'; ?>

                                        </td>
                                        <td class="campos">
                                            <?php 
                                                $valorF = (String) $row11[5];$fechaS = explode("-",$valorF); 
                                                  echo '<label class="valorLabel" style="font-weight:normal" id="lblfechamovimiento'.$row11[0].'">'.(ucwords(strtolower($fechaS[2].'/'.$fechaS[1].'/'.$fechaS[0]))).'</label>'; 
                                            ?>
                                        </td>
                                        <td class="campos text-right">
                                            <?php 
                                            echo '<label class="valorLabel" style="font-weight:normal" id="lblvalormovimiento'.$row11[0].'">'.(ucwords(strtolower($row11[6]))).'</label>';
                                            ?> 
                                            
                                        </td>
                                        <td class="campos text-center">
                                            <div id="docD<?php echo $row11[0]?>" name="docD<?php echo $row11[0]?>" style="display:inline">
                                                <?php if(empty($row11[7])) { echo "<i>No hay documento</i>"; } else { ?>
                                                <a href="<?php echo $row11[7];?>">
                                                    <i title="Ver" class="glyphicon glyphicon-search"></i>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <?php 
                                    $afec="SELECT dcp.comprobanteafectado "
                                            . "FROM gf_detalle_comprobante dc "
                                            . "LEFT JOIN gf_detalle_comprobante_pptal dcp ON dc.detallecomprobantepptal = dcp.id_unico "
                                            . "WHERE dc.id_unico ='$id'";
                                    $afec = $mysqli->query($afec);
                                    if(mysqli_num_rows($afec)>0){
                                        $afect= mysqli_fetch_row($afec);
                                        $afectado=$afect[0];
                                    } else {
                                        $afectado='';
                                    }
                                    while ($afectado !='') {
                                            
                                        
                                    $sql1 = "SELECT
                                                dtm.id_unico,
                                                dtm.tipodocumento,
                                                tpd.id_unico,
                                                tpd.nombre,
                                                dtm.numero,
                                                dtm.fechavencimiento,
                                                dtm.valor,
                                                dtm.ruta,
                                                dc.comprobanteafectado, 
                                                cnt.numero,
                                                tc.nombre
                                              FROM
                                                gf_detalle_comprobante_mov dtm
                                              LEFT JOIN
                                                gf_tipo_documento tpd ON dtm.tipodocumento = tpd.id_unico
                                              LEFT JOIN
                                                gf_detalle_comprobante_pptal dc ON dtm.comprobantepptal = dc.id_unico
                                              LEFT JOIN
                                                gf_comprobante_pptal cnt ON dc.comprobantepptal = cnt.id_unico
                                              LEFT JOIN
                                                gf_tipo_comprobante_pptal tc ON cnt.tipocomprobante = tc.id_unico
                                              WHERE
                                                dtm.comprobantepptal=$afectado";

                                        $result1 = $mysqli->query($sql1);  
                                        if(mysqli_num_rows($result1)>0){
                                    while($row1 = $result1->fetch_row()){ ?>
                                    <tr>
                                
                                        <td class="campos oculto">
                                            <?php echo $row1[0]; ?>
                                        </td>
                                
                                        <td class="campos">
                                            <?php echo ucwords(strtolower($row1[10]))?>
                                        </td>
                                        <td class="campos">
                                            <?php echo ucwords(strtolower($row1[9]))?>
                                        </td>
                                        <td class="campos">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="lbltipodocumento'.$row1[0].'">'.(ucwords(strtolower($row1[3]))).'</label>'; ?>
                                        </td>
                                        <td class="campos">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblnumerodocumento'.$row11[0].'">'.(ucwords(strtolower($row1[4]))).'</label>'; ?>

                                        </td>
                                        <td class="campos">
                                            <?php 
                                                $valorF1 = (String) $row1[5];$fechaS1 = explode("-",$valorF1); 
                                                  echo '<label class="valorLabel" style="font-weight:normal" id="lblfechamovimiento'.$row1[0].'">'.(ucwords(strtolower($fechaS1[2].'/'.$fechaS1[1].'/'.$fechaS1[0]))).'</label>'; 
                                            ?>
                                        </td>
                                        <td class="campos text-right">
                                            <?php 
                                            echo '<label class="valorLabel" style="font-weight:normal" id="lblvalormovimiento'.$row1[0].'">'.(ucwords(strtolower($row1[6]))).'</label>';
                                            ?> 
                                            
                                        </td>
                                        <td class="campos text-center">
                                            <div id="docD<?php echo $row1[0]?>" name="docD<?php echo $row1[0]?>" style="display:inline">
                                                <?php if(empty($row1[7])) { echo "<i>No hay documento</i>"; } else { ?>
                                                <a href="<?php echo $row1[7];?>">
                                                    <i title="Ver" class="glyphicon glyphicon-search"></i>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </td>
                                        
                                    </tr>
                                    <?php      
                                            } 
                                        }
                                    $afec="SELECT comprobanteafectado FROM gf_detalle_comprobante_pptal WHERE id_unico ='$afectado'";
                                    $afec = $mysqli->query($afec);
                                    if(mysqli_num_rows($afec)>0){
                                        $afect= mysqli_fetch_row($afec);
                                        $afectado=$afect[0];
                                    } else {
                                        $afectado='';
                                    }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>                                                                        
                    </div>                    
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">                
            </div>
        </div>
    </div>
</div>
 <script type="text/javascript" >
  $("#mdlDetalleMovimiento").draggable({
      handle: ".modal-header"
  });
</script>
<script type="text/javascript" >
    $("#btnCerrarModalMov1").click(function(){
       document.location.reload();
    });
    
    $("#mdlDetalleMovimiento1").on('shown.bs.modal',function(){
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