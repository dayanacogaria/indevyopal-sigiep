<?php

require 'Conexion/conexion.php';

 
if (isset($_POST['id'])){

    $id=$_POST['id'];

    $sql = "
    SELECT dc.id_unico,
    IF(CONCAT_WS(' ',
    t.nombreuno,
    t.nombredos,
    t.apellidouno,
    t.apellidodos) 
    IS NULL OR CONCAT_WS(' ',
    t.nombreuno,
    t.nombredos,
    t.apellidouno,
    t.apellidodos) = '',
    (t.razonsocial),
    CONCAT_WS(' ',
    t.nombreuno,
    t.nombredos,
    t.apellidouno,
    t.apellidodos)) AS NOMBRETERCERO,
    tda.descripcion,
    cd.nombre, 
    dc.ruta,
    dc.contribuyente,
    dc.tipo_doc,
    t.numeroidentificacion,
    DATE_FORMAT(dc.fecha_exp,'%d/%m/%Y') AS fechaFacConvertida



    FROM gc_documento_contribuyente  dc
    LEFT JOIN gc_contribuyente c ON c.id_unico=dc.contribuyente
    LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
    LEFT JOIN gc_tipo_documento_adjunto tda ON tda.id_unico=dc.tipo_doc
    LEFT JOIN gc_clase_documento cd ON cd.id_unico=tda.clase_doc
    WHERE dc.id_unico=$id";
        
    $resultado  = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);

}

 

?>


<script>

    $(function(){
        var fecha = new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth() + 1;
        if(dia < 10){
            dia = "0" + dia;
        }
        if(mes < 10){
            mes = "0" + mes;
        }
        var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);

        $("#fcci").datepicker({changeMonth: true});


    });
</script>

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
<div class="modal fade modaldoc" id="mdlModificar" role="dialog" align="center" aria-labelledby="mdlModificar" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title"  style="font-size: 24; padding: 3px;">Modificar Documento Contribuyente</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                    <button type="button" id="btnCerrarModalMov1" class="btn btn-xs" style="color: #000;margin-left: -25%;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                  <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/modificarDocumentosContribuyentes.php" >                 <!--n <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarFuenteJson.php">-->

                         <div style="width: 500px;padding-left: -50%;" >

                             <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                           
                             <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                                        <?php
                                        $idTipoDocumento=$row[6];

                                        $cuentaI = "SELECT da.id_unico,cd.nombre,da.descripcion
                                        FROM gc_tipo_documento_adjunto da 
                                        LEFT JOIN gc_clase_documento cd ON cd.id_unico=da.clase_doc
                                        WHERE da.id_unico!=$idTipoDocumento;

                                        ";
                                        $rsctai = $mysqli->query($cuentaI);
                                        ?>
                                          <div class="form-group" style="margin-bottom: 5px;">
                                             <label for="slaccom" class="col-sm-3"><strong style="color:#03C1FB;">*</strong>Tipo Documento:</label>
                                            <select name="tipoDocumento" id="std" required="true" style="width:70%;margin-right: -25%;text-align: left;" class="select2 form-control" title="Seleccione Tipo Documento" required>
                                                    <option value="<?php echo $row[6]?>"><?php echo $row[2]." - ".ucwords(mb_strtolower($row[3] )) ?></option>
                                            
                                                <?php while($rowtd=mysqli_fetch_row($rsctai)){ ?> 
                                                    <option value="<?php echo $rowtd[0]?>"><?php echo $rowtd[1]." - ".ucwords(mb_strtolower($rowtd[2] )) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <input type="hidden" name="archivoOculto" value="<?php echo $row[4] ?>">
                                
                                          <div class="form-group" style="margin-bottom: 5px;">
                                             <label for="s" class="col-sm-3" style="padding-left: 55px;">
                                              Archivo:
                                        </label>
                                        <input  type="file" name="txtRuta"  id="ruta" class="form-control" style="width: 70%"  title="Cargue un Archivo"  />
                                        </div>
                                          <div class="form-group" style="margin-bottom: -10px;">
                                             <label for="slaccom" class="col-sm-3">Fecha Expedición:</label>
                                            <input class="form-control" type="text" name="fechaExpedicion" id="fcci" style="width: 70%;" value="<?php echo $row[8]; ?>">
                                        </div><br><br><br>
                                        
                                      
                                      <div class="form-group" style="margin-top: 10px;">
                                            <label for="no" class="col-sm-1 control-label"></label>
                                             <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                                     </div>
                        </div>
                  </form>
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

  $("#std").select2(); 
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


