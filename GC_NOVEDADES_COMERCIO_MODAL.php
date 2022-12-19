<?php

require 'Conexion/conexion.php';

 
if (isset($_POST['id'])){

    $id=$_POST['id'];
    $sql="
    SELECT nc.id_unico,
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
    tn.nombre,
    DATE_FORMAT(nc.fecha,'%d/%m/%Y') AS fechaFacConvertida,
    nc.num_acto,
    nc.observaciones,
    nc.tipo_novedad

    FROM gc_novedades_comercio nc
    LEFT JOIN gc_contribuyente c ON c.id_unico=nc.contribuyente
    LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
    LEFT JOIN gc_tipo_novedad tn ON tn.id_unico=nc.tipo_novedad
    WHERE nc.id_unico=$id";
        
    $resultado  = $mysqli->query($sql);
    $row=mysqli_fetch_row($resultado);

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

        $("#fff").datepicker({changeMonth: true,});


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
<div class="modal fade modalnc" id="mdlModificar" role="dialog" align="center" aria-labelledby="mdlModificar" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title"  style="font-size: 24; padding: 3px;">Modificar Novedades Comercio</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                    <button type="button" id="btnCerrarModalMov1" class="btn btn-xs" style="color: #000;margin-left: -25%;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                     <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/modificarNovedadComercioJson.php">

                         <div style="width: 500px;padding-left: -50%;" >

                                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                                    
                                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                                    <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                                        <?php
                                        $tipoNovedadS=$row[6];   
                                        $cuentaI = "SELECT * FROM gc_tipo_novedad tn WHERE tn.id_unico!=$tipoNovedadS ORDER BY nombre ASC";
                                        $rsctai = $mysqli->query($cuentaI);
                                        ?>
                                     <div class="form-group" style="margin-bottom: -10px;">
                                             <label for="slaccom" class="col-sm-3"><strong style="color:#03C1FB;">*</strong>Tipo Novedad:</label> 
                                            <select name="tipo" id="stn" required  class="select2 form-control" title="Seleccione Tipo Novedad" style="width:70%;margin-right: -25%;text-align: left;">
                                                <option value="<?php echo $row[6] ?>"><?php echo $row[2] ?></option>
                                                    <?php while($rowtn=mysqli_fetch_row($rsctai)){ ?>
                                                         <option value="<?php echo $rowtn[0] ?>"><?php echo $rowtn[1] ?></option>
                                                    <?php } ?>
                                            </select>
                                        </div><br>
                                       <div class="form-group" style="margin-bottom: 5px;">
                                             <label for="slaccom" class="col-sm-3" style="padding-left: 50px"><strong class="obligado">*</strong>Fecha:</label>
                                            <input style="width:70%;"  class="form-control" type="text" name="fecha" id="fff"  value="<?php echo $row[3]; ?>" required>
                                        </div>



                                       <div class="form-group" style="margin-bottom: 5px;">
                                             <label for="slaccom" class="col-sm-3">
                                                Número Acto: 
                                            </label>
                                            <input style="width:70%;" type="text" name="numeroActo"  id="cact" class="form-control" maxlength="20" title="Ingrese Número Acto" onkeypress="return txtValida(event,'num_car')" placeholder="Número Acto" value="<?php echo $row[4] ?>">
                                        </div>
 
                                       <div class="form-group" style="margin-bottom: -10px;">
                                             <label for="slaccom" class="col-sm-3" style="padding-left: 0px">
                                                    Observaciones:
                                                </label>    
                                                <textarea name="observaciones"  placeholder="Observaciones" id="Observaciones"  class="form-control" rows="3" style="width:70%;" title="Ingrese Observaciones" maxlength="100"><?php echo $row[5] ?></textarea> 
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
  $("#stn").select2(); 
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


