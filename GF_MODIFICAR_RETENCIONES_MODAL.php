<?php

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
<div class="modal fade movi" id="mdlModificarReteciones" role="dialog" align="center" aria-labelledby="mdlModificarReteciones" aria-hidden="true">
    <div class="modal-dialog" style="height:600px;width:900px">
        <div class="modal-content">
            
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title"  style="font-size: 24; padding: 3px;">Modificar Retenciones</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                    <button type="button" id="btnCerrarModalMov1" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="row">
                    <input type="hidden" id="idPrevio1" value="">
                    <input type="hidden" id="cnt" name="cnt" value="">
                    <div class="col-sm-12" style="margin-top: 10px;margin-left: 4px;margin-right: 4px">                                                
                        
                        <div class="table-responsive contTabla" >
                            <table id="tabla21" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">
                                <thead style="position: relative;overflow: auto;width: 100%;">
                                    <tr>
                                        <td class="oculto">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        <td class="cabeza"><strong>Tipo Retención</strong></td>
                                        <td class="cabeza"><strong>Porcentaje Retención</strong></td>
                                        <td class="cabeza"><strong>Retención Base</strong></td>
                                        <td class="cabeza"><strong>Valor Retención</strong></td>
                                        
                                    </tr>
                                    <tr>
                                        <th class="oculto">Identificador</th>
                                        <th width="7%" class="cabeza"></th>
                                        <th class="cabeza">Tipo Retención</th>
                                        <th class="cabeza">Porcentaje Retención</th>
                                        <th class="cabeza">Retención Base</th>
                                        <th class="cabeza">Valor Retención</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    require_once 'Conexion/conexion.php';
                                    @session_start();
                                    $ret ="";
                                    $id = "";
                                    $rete ="";
                                    if(!empty($_REQUEST['id'])){
                                        $id =$_REQUEST['id'];
                                    } elseif(!empty ($_SESSION['cntcxp'])) {
                                        $id = $_SESSION['cntcxp'];
                                    }
                                    if(!empty($_REQUEST['id'])) {
                                        
                                        $ret = "SELECT r.id_unico, 
                                            r.valorretencion,
                                            r.retencionbase, 
                                           tr.porcentajeaplicar, 
                                            tr.nombre
                                            FROM gf_retencion r
                                            LEFT JOIN gf_tipo_retencion tr ON r.tiporetencion =tr.id_unico
                                            WHERE r.comprobante = $id";
                                    
                                    } else {
                                        if(!empty($_REQUEST['idsc'])){
                                            $ids_r = str_replace('.', ',', $_REQUEST['idsc']);
                                            $ids_r = str_replace("'", "", $ids_r);
                                            $ret = "SELECT r.id_unico, 
                                            r.valorretencion,
                                            r.retencionbase, 
                                           tr.porcentajeaplicar, 
                                            tr.nombre
                                            FROM gf_retencion r
                                            LEFT JOIN gf_tipo_retencion tr ON r.tiporetencion =tr.id_unico
                                            WHERE r.id_unico IN ($ids_r)";
                                            
                                        } else {
                                            $ret = "SELECT r.id_unico, 
                                            r.valorretencion,
                                            r.retencionbase, 
                                           tr.porcentajeaplicar, 
                                            tr.nombre
                                            FROM gf_retencion r
                                            LEFT JOIN gf_tipo_retencion tr ON r.tiporetencion =tr.id_unico
                                            WHERE r.id_unico=0";
                                        }
                                    }
                                   
                                    $rete = $mysqli->query($ret);
                                    if(mysqli_num_rows($rete)>0) {
                                    while ($row = mysqli_fetch_row($rete)) { ?>
                                    <tr>
                                        <td class="campos oculto">
                                            <?php echo $row[0]; ?>
                                        </td>     
                                        <td class="campos">
                                            <?php if(!empty($_REQUEST['actualizar'])) { 
                                                if($_REQUEST['actualizar']==1){  
                                                } else { ?>
                                                <a href="#<?php echo $row[0];?>" class="eliminar" onclick="javascript:eliminarDetalle(<?php echo $row[0]; ?>)" title="Eliminar">
                                                    <li class="glyphicon glyphicon-trash"></li>
                                                </a> 
                                                <?php }
                                            } else { ?>
                                            <a href="#<?php echo $row[0];?>" class="eliminar" onclick="javascript:eliminarDetalle(<?php echo $row[0]; ?>)" title="Eliminar">
                                                    <li class="glyphicon glyphicon-trash"></li>
                                                </a>
                                                <?php } ?>
                                        </td>
                                        <td class="campos">
                                            <?php echo ucwords(mb_strtolower($row[4])); ?>
                                        </td>
                                        <td class="campos">
                                            <?php echo $row[3].'%'; ?>
                                        </td>
                                        <td class="campos">
                                            <div id="lblBase<?php echo $row[0]?>">
                                                <?php echo number_format($row[2],2,'.',','); ?>
                                            </div>
                                            <div id="txtBase<?php echo $row[0]?>" style="display: none;">
                                                <?php echo '<input onkeypress="return justNumbers(event)" style="padding:2px;height:19px;" class="col-sm-12 text-left campoD" type="text" name="valorbase'.$row[0].'" id="valorbase'.$row[0].'" value="'.$row[2].'" />';?>
                                            </div>
                                        </td>
                                        <td class="campos">
                                            <div id="lblValor<?php echo $row[0]?>">
                                                <?php echo number_format($row[1],2,'.',','); ?>
                                            </div>
                                            <div id="txtValor<?php echo $row[0]?>" style="display: none;">
                                                <?php echo '<input onkeypress="return justNumbers(event)" style="padding:2px;height:19px;" class="col-sm-10 text-left campoD" type="text" name="valor'.$row[0].'" id="valor'.$row[0].'" value="'.$row[1].'" />';?>
                                            </div>
                                            <table id="tab<?php echo $row[0] ?>" style="display: none; padding:0px;background-color:transparent;background:transparent;">
                                                <tbody>
                                                    <tr style="background-color:transparent;">
                                                        <td style="background-color:transparent;">
                                                            <a  href="#<?php echo $row[0];?>" title="Guardar" id="guardar<?php echo $row[0]; ?>" onclick="javascript:guardarCambiosRetencion(<?php echo $row[0]; ?>)">
                                                                <li class="glyphicon glyphicon-floppy-disk"></li>
                                                            </a>
                                                        </td>
                                                        <td style="background-color:transparent;">
                                                            <a href="#<?php echo $row[0];?>" title="Cancelar" id="cancelar<?php echo $row[0] ?>" onclick="javascript:cancelar(<?php echo $row[0];?>)" >
                                                                <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    <?php }
                                    } ?>                                    
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
    $("#btnCerrarModalMov1").click(function(){
       $("#mdlModificarReteciones1").modal('hide');
       $(".modal-backdrop fade in").css('display','none');
       $(".modal-backdrop").css('display','none');
    });
    
    $("#mdlModificarReteciones").on('shown.bs.modal',function(){
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
<?php 
if(!empty($_POST['id'])) { 
##BUSCAR FECHA COMPROBANTE 
    $fc = "SELECT fecha FROM gf_comprobante_cnt WHERE id_unico = $id";
    $fc = $mysqli->query($fc);
    $fc = mysqli_fetch_row($fc);
    $fc = $fc[0];
    ##DIVIDIR FECHA
    $fecha_div = explode("-", $fc);
    $anio = $fecha_div[0];
    $mes = $fecha_div[1];
    $dia = $fecha_div[2];

    ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
    $ci="SELECT
    cp.id_unico
    FROM
    gs_cierre_periodo cp
    LEFT JOIN
    gf_parametrizacion_anno pa ON pa.id_unico = cp.anno
    LEFT JOIN
    gf_mes m ON cp.mes = m.id_unico
    WHERE
    pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2";
    $ci =$mysqli->query($ci);
    if(mysqli_num_rows($ci)>0){ ?>
    <script>
    $(document).ready(function()
    {
    $(".eliminar").css('display','none');
     $(".modificar").css('display','none');
    });
    </script>
    <?php } }  ?>
<script>
function justNumbers(e){   
        var keynum = window.event ? window.event.keyCode : e.which;
        if ((keynum == 8) || (keynum == 46) || (keynum == 45))
        return true;
        return /\d/.test(String.fromCharCode(keynum));
    }
</script>
<script>
function modificarDetalle(id){
        if(($("#idPrevio1").val() != 0)||($("#idPrevio1").val() != "")){
            var lblBase = 'lblBase'+$("#idPrevio1").val();
            var txtBase = 'txtBase'+$("#idPrevio1").val();
            var lblValor = 'lblnumerodocumento'+$("#idPrevio1").val();
            var txtValor = 'txtnumerodocumento'+$("#idPrevio1").val();
            var tabla = 'tab'+$("#idPrevio1").val();
            
            
            $("#"+lblBase).css('display','block');
            $("#"+txtBase).css('display','none');                               
            $("#"+lblValor).css('display','block');
            $("#"+txtValor).css('display','none');   
            $("#"+tabla).css('display','none');
        }
        
        var lblBase = 'lblBase'+id;
        var txtBase = 'txtBase'+id;
        var lblValor = 'lblValor'+id;
        var txtValor = 'txtValor'+id;
        var tabla = 'tab'+id;
        
        $("#"+lblBase).css('display','none');
        $("#"+txtBase).css('display','block');                               
        $("#"+lblValor).css('display','none');
        $("#"+txtValor).css('display','block');   
        $("#"+tabla).css('display','block');
        
        $("#idActual1").val(id);
        if($("#idPrevio1").val() != id){
            $("#idPrevio1").val(id);   
        }
    }
    
    function cancelar(id){
        var lblBase = 'lblBase'+id;
        var txtBase = 'txtBase'+id;
        var lblValor = 'lblValor'+id;
        var txtValor = 'txtValor'+id;
        var tabla = 'tab'+id;
        
        $("#"+lblBase).css('display','block');
        $("#"+txtBase).css('display','none');                               
        $("#"+lblValor).css('display','block');
        $("#"+txtValor).css('display','none');  
        $("#"+tabla).css('display','none');
        
    }
    
    function guardarCambiosRetencion(id){   
           
            var valorbaset = 'valorbase'+id;        
            var valort = 'valor'+id;        
            
            var valorbase = $("#"+valorbaset).val();
            var valor = $("#"+valort).val();
           
            var form_data={ action:1, id:id, valorbase:valorbase, valor:valor};
            var result = '';
            $.ajax({
                type: 'POST',
                url: "jsonPptal/retencionesJson.php",
                data:form_data,
                success: function (data) {
                    console.log(data);
                    result = JSON.parse(data);
                    if(result==1){
                        $("#mdlModificarReteciones").modal('hide');
                        $("#infoM").modal('show');
                    }else{
                        $("#mdlModificarReteciones").modal('hide');
                        $("#mdlModError").modal('show');
                    }
                }
            });
        
    }
   </script>
   <script>
    function eliminarDetalle(id){
        <?php if(!empty($_REQUEST['facturacion'])){?>
           let factura = <?php echo $_REQUEST['facturacion'];?>;
        <?php } else  { ?>
            let factura = '';
        <?php } ?>        
        var result='';
        
        $("#mdlPEliminiadoR").modal('show');
        $("#btnEliminarR").click(function(){
            $("#mdlPEliminiadoR").modal('hide');
            var form_data={action:9,id:id,factura:factura };
            $.ajax({
                type    :'POST',
                url     :"jsonPptal/gf_recaudoFacJson.php",  
                data    :form_data,
                success: function (data) {
                    console.log('ELR'+data);
                    result = JSON.parse(data);
                    if(result==1){
                        $("#mdlModificarReteciones").modal('hide');
                        $("#myModal1").modal('show');
                    }else{
                        $("#myModal2").modal('show');
                    }
                }
            });
        });
    }
    
</script>
<div class="modal fade" id="mdlPEliminiadoR" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro de retención?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnEliminarR" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" id="btnCancelar"style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
