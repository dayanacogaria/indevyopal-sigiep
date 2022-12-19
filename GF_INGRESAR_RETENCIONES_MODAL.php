<?php
#####################################################################################################################################################################
#                                               MODIFICACIONES
######################################################################################################################################################################
#28/09/2017 | ERICA G. | CALCULO AL INGRESAR RETENCIONES 
#15/09/2017 | ERICA G. | INGRESAR RETENCIONES SI NO LAS INGRESO EL SISTEMA ARCHIVO CREADO 
######################################################################################################################################################################}
require_once 'Conexion/conexion.php';
@session_start();

?>
<link rel="stylesheet" href="css/select2.css">
<style>
    #tabla22 table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    #tabla22 table.dataTable tbody td,table.dataTable tbody td{padding:1px}
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
<div class="modal fade movi1" id="mdlIngresarRetenciones" role="dialog" align="center" aria-labelledby="mdlIngresarRetenciones" aria-hidden="true">
    <div class="modal-dialog" style="height:600px;width:1200px">
        <div class="modal-content">
            
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title"  style="font-size: 24; padding: 3px;">Ingresar Retenciones</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                    <button type="button" id="btnCerrarModalMov1" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="row">
                    <input type="hidden" id="idPrevio1" value="">
                    <input type="hidden" id="idActual1" value="">
                    <input type="hidden" id="numcuentas" value="<?php echo $_POST['num'];?>">
                    <input type="hidden" id="compr" value="<?php echo $_SESSION['cntcxp'];?>">
                    <div class="col-sm-12" style="margin-top: 10px;margin-left: 4px;margin-right: 4px">                                                
                        
                        <div class="table-responsive contTabla" >
                            <table id="tabla22" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">
                                <thead style="position: relative;overflow: auto;width: 100%;">
                                    <tr>
                                        <td class="oculto">Identificador</td>
                                        <td class="cabeza"><strong>Clase Retención</strong></td>
                                        <td class="cabeza"><strong>Tipo Retención</strong></td>
                                        <td class="cabeza"><strong>Aplicar Sobre</strong></td>
                                        <td class="cabeza"><strong>Valor</strong></td>
                                        <td class="cabeza"><strong>Base Gravable</strong></td>
                                        <td class="cabeza"><strong>Valor Retención</strong></td>
                                        
                                    </tr>
                                    <tr>
                                        <th class="oculto">Identificador</th>
                                        <th class="cabeza">Clase Retención</th>
                                        <th class="cabeza">Tipo Retención</th>
                                        <th class="cabeza">Aplicar Sobre</th>
                                        <th class="cabeza">Valor</th>
                                        <th class="cabeza">Base Gravable</th>
                                        <th class="cabeza">Valor Retención</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($_POST['num'])) { 
                                        for($i=0; $i<$_POST['num']; $i++) { 
                                        $tipor = "SELECT id_unico, nombre FROM gf_clase_retencion ORDER BY nombre ASC";
                                        $tipor = $mysqli->query($tipor);?>
                                    <tr>
                                        <td class="oculto">Identificador</td>
                                        <td>
                                            <select name="claseretencion<?php echo $i?>" id="claseretencion<?php echo $i?>" class="col-sm-12 select2_single " style="width: 250px" onchange="tiporetencion(<?php echo $i?>)">
                                                <option>Clase Retención</option>
                                                <?php while ($row = mysqli_fetch_row($tipor)) {
                                                     echo '<option value="'.$row[0].'">'.$row[1].'</option>';
                                                 }?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="tiporetencion<?php echo $i?>" id="tiporetencion<?php echo $i?>" class="col-sm-12  select2_single" style="width: 250px;" >
                                              <option> Tipo Retención</option> 
                                            </select>
                                        </td>
                                        <td>
                                        <?php
                                        $aplicarS = "SELECT LOWER(nombre), id_unico
                                                FROM gf_tipo_base
                                                ORDER BY nombre ASC";
                                        $rsaS = $mysqli->query($aplicarS);
                                        $porIVA = "SELECT valor
                                        FROM gs_parametros_basicos
                                        WHERE nombre ='porcentaje iva' ";
                                        $rsPI = $mysqli->query($porIVA);
                                        $filaPI = mysqli_fetch_row($rsPI);
                                        ?>
                                        <input type="hidden" name="porIVA<?php echo $i?>" id="porIVA<?php echo $i?>" value="<?php echo $filaPI[0]; ?>"/>
                                        <select name="aplicar<?php echo $i?>" id="aplicar<?php echo $i?>"  onchange="calcularRetencion(<?php echo $i?>)" class="col-sm-12  select2_single" style="width: 250px;">
                                            <option value="" selected="selected">Aplicar sobre</option>
                                            <?php
                                            while ($filaaS = mysqli_fetch_row($rsaS)) {
                                                echo '<option value="' . $filaaS[1] . '">' . ucwords(($filaaS[0])) . '</option>';
                                            }
                                            ?>
                                        </select>
                                        </td>
                                        <td>
                                            <?php if(!empty($_POST['vald'])) { $vd = number_format($_POST['vald'],2,'.',','); } else { $vd =0;}?>
                                            <label id="valor<?php echo $i?>" name="valor<?php echo $i?>" value="<?php echo $vd; ?>"><?php echo $vd; ?></label>
                                        </td>
                                        <td>
                                            <?php if(!empty($_POST['vald'])) { $vd = number_format($_POST['vald'],2,'.',','); } else { $vd =0;}?>
                                            <input name="basegravable<?php echo $i?>" id="basegravable<?php echo $i?>" class="form-control input-sm" onkeypress="return txtValida(event,'dec', 'basegravable<?php echo $i?>', '2');" onkeyup="formatC('basegravable<?php echo $i?>');" value="<?php echo $vd; ?>"/>
                                        </td>
                                        <td>
                                            <input name="retencion<?php echo $i?>" id="retencion<?php echo $i?>" class="form-control input-sm" onkeypress="return txtValida(event,'dec', 'retencion<?php echo $i?>', '2');" onkeyup="formatC('retencion<?php echo $i?>');"/>
                                        </td>
                                    </tr>
                                    <?php } } ?>
                                    <?php 
                                    if(!empty($_SESSION['cntcxp'])) {
                                        $id = $_SESSION['cntcxp'];
                                        $ret = "SELECT r.id_unico, 
                                            r.valorretencion,
                                            r.retencionbase, 
                                           tr.porcentajeaplicar, 
                                            tr.nombre, cr.nombre 
                                            FROM gf_retencion r
                                            LEFT JOIN gf_tipo_retencion tr ON r.tiporetencion =tr.id_unico 
                                            LEFT JOIN gf_clase_retencion cr ON tr.claseretencion = cr.id_unico 
                                            WHERE r.comprobante = $id";
                                    $rete = $mysqli->query($ret);
                                    if(mysqli_num_rows($rete)>0) {
                                    while ($row = mysqli_fetch_row($rete)) { ?>
                                    <tr>
                                        <td class="campos oculto">
                                            <?php echo $row[0]; ?>
                                        </td> 
                                        <td class="campos">
                                            <?php echo ucwords(mb_strtolower($row[5])); ?>
                                        </td>
                                        <td class="campos">
                                            <?php echo ucwords(mb_strtolower($row[4])); ?>
                                        </td>
                                        <td class="campos">
                                           
                                        </td>
                                        <td></td>
                                        <td class="campos">
                                            <div id="lblBase<?php echo $row[0]?>">
                                                <?php echo number_format($row[2],2,'.',','); ?>
                                            </div>
                                        </td>
                                        <td class="campos">
                                            <div id="lblValor<?php echo $row[0]?>">
                                                <?php echo number_format($row[1],2,'.',','); ?>
                                            </div>
                                        </td>
                                        
                                        
                                    </tr>
                                    <?php } } }  ?>
                                </tbody>
                               
                            </table>
                            <div align ="right" class="col-sm-12" style="margin-top: 19px;">
                            <button type="button" id="btnGuardarRet" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Modificar" >
                                <li class="glyphicon glyphicon-floppy-disk"></li>Guardar Retenciones
                            </button>
                            </div>
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
    $("#btnGuardarRet").click(function(){
        var numc = $("#numcuentas").val();
        //***Validar****
        var numrg ='';
        var numnc =0;
        for( i = 0; i < numc; i++) {
            var tipo = 'tiporetencion'+i; 
            var clase = 'claseretencion'+i; 
            var base = 'basegravable'+i; 
            var valor = 'retencion'+i; 
            var tiporet = $("#"+tipo).val();
            var claseret = $("#"+clase).val();
            var baseret = $("#"+base).val();
            var valorret = $("#"+valor).val();
            if(tiporet=="" || claseret=="" || baseret=="" || baseret ==0 || valorret =="" || valorret==0){
                numrg +='La retención '+i + '<br/>';
                numnc =numnc+1;
            }
            
        }
        if(numnc>0){
            if(numnc ==1){
                var msg = numrg+' No esta calculada';
            } else {
                var msg = numrg+' No estan calculadas';
            }
            
            document.getElementById('mensaje').innerHTML = msg;
            $("#mdlRetenciones").modal("show");
        } else {
            guardar();
        }
    })
</script>
<div class="modal fade" id="mdlRetenciones" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <label name="mensaje" id="mensaje"></label>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnAceptarRet" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                
            </div>
        </div>
    </div>
</div>
<script>
function guardar(){
    var numc = $("#numcuentas").val();
    var compr = $("#compr").val();
    var valorRet = new Array();
    var retencionBas = new Array();
    var tipoRet = new Array();
    for( i = 0; i < numc; i++) {
        
        var tipo = 'tiporetencion'+i; 
        var base = 'basegravable'+i; 
        var valor = 'retencion'+i;
        
        
        var tiporet1 = $("#"+tipo).val();
        var baseret1 = $("#"+base).val();
        var valorret1 = $("#"+valor).val();
     
        
        var valorRetV = parseFloat(valorret1.replace(/\,/g, ''));
        valorRetV = valorRetV.toString();
        var baseretV = parseFloat(baseret1.replace(/\,/g, ''));
        baseretV = baseretV.toString();
        
        tipoRet[i] =tiporet1;
        valorRet[i]=valorRetV;
        retencionBas[i] = baseretV;
    }
    var form_data={action:2, tipo: tipoRet, valor:valorRet,base: retencionBas, numR :numc, compr:compr};
     $.ajax({
        type: "POST",
        url: "jsonPptal/gf_retencionesJson.php",
        data: form_data,
        success: function (response)
        {
            console.log(response);
            var msm ='';
           if(response==1){
               msm +='Información Guardada Correctamente';
           } else {
               msm +='La Información no se ha podido guardar';
           }
           document.getElementById('mensajeGuardado').innerHTML = msm;
           $("#modalGuardadoRet").modal("show");
        }
    })
}
</script>
<div class="modal fade" id="modalGuardadoRet" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <label name="mensajeGuardado" id="mensajeGuardado"></label>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnGuarRet" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script>
    $("#btnGuarRet").click(function(){
        document.location.reload();
    })
</script>
<script>
function tiporetencion(id){
    $("#claseretencion"+id).select2({placeholder: "", allowClear: true});
    $("#aplicar"+id).select2({placeholder: "", allowClear: true});
    var tipo = 'tiporetencion'+id; 
    var tiporet = $("#"+tipo).html("<option> Tipo Retención</option> ");
    
    var clase = 'claseretencion'+id;        
    var claseret = $("#"+clase).val();
    if(claseret!=""){
    var form_data={action:1, ret:claseret};
    $.ajax({
        type: "POST",
        url: "jsonPptal/gf_retencionesJson.php",
        data: form_data,
        success: function(response)
        { 
            console.log(response);
            var tipo = 'tiporetencion'+id; 
            $("#"+tipo).select2({placeholder: "", allowClear: true});
            //$(".select2_single, #" + tipo).select2();
            $("#"+tipo).html(response);
        }
   }); 
    }

}
</script>
 <script type="text/javascript" >
  $("#mdlIngresarRetenciones").draggable({
      handle: ".modal-header"
  });
</script>
<script type="text/javascript" >
    $("#btnCerrarModalMov1").click(function(){
       document.location.reload();
    });
    
    $("#mdlIngresarRetenciones").on('shown.bs.modal',function(){
        try{
            var dataTable = $("#tabla22").DataTable();
            dataTable.columns.adjust().responsive.recalc();   
        }catch(err){}        
    });
</script>
<script type="text/javascript">
  $(document).ready(function() {
     var i= 1;
    $('#tabla22 thead th').each( function () {
        if(i != 1){ 
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
   var table = $('#tabla22').DataTable({
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

<script>
function justNumbers(e){   
        var keynum = window.event ? window.event.keyCode : e.which;
        if ((keynum == 8) || (keynum == 46) || (keynum == 45))
        return true;
        return /\d/.test(String.fromCharCode(keynum));
    }
</script>

  <script>
  function calcularRetencion(id){
    var numeral = id;
    if (($("#tiporetencion" + numeral).val() != "") && ($("#tiporetencion" + numeral).val() != 0))
    {
        if ($("#porIVA" + numeral).val() == 0 || $("#porIVA" + numeral).val() == "")
        {
        } else if (($("#sltAplicarS" + numeral).val() != "") && ($("#sltAplicarS" + numeral).val() != 0))  //acá
        {
            var tipoRete = $("#tiporetencion" + numeral).val();
            var aplicar = $("#aplicar" + numeral).val();
            var valor = $("#valor" + numeral).text();
            valor = parseFloat(valor.replace(/\,/g, ''));
            var iva = $("#porIVA" + numeral).val();
            var form_data = {estruc: 2, aplicar: aplicar, valor: valor, iva: iva, tipoRete: tipoRete};
            $.ajax({
                type: "POST",
                url: "estructura_aplicar_retenciones.php",
                data: form_data,
                success: function (response)
                {
                    
                    var valorBase = parseFloat(response).toFixed(2);
                    $("#basegravable" + numeral).val(valorBase);

                    var form_data = {estruc: 3, valorBas: response, idTipRet: tipoRete};

                    $.ajax({
                        type: "POST",
                        url: "estructura_aplicar_retenciones.php",
                        data: form_data,
                        success: function (response)
                        {
                            var retApl = parseFloat(response).toFixed(2);
                            $("#retencion" + numeral).val(retApl);

                        }//Fin succes.
                    }); //Fin ajax.
                }//Fin succes.
            }); //Fin ajax.
        } else
        {
        }
    } else
    {
    }
  }
  </script>

