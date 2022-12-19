<?php

require 'Conexion/conexion.php';

 
if (isset($_POST['id'])){

    $id=$_POST['id'];

    $sql = "
    SELECT v.id_unico,

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
    t.apellidodos)) AS NOMBRECONTRIBUYENTE ,  

    tv.nombre,

    IF(CONCAT_WS(' ',
    terv.nombreuno,
    terv.nombredos,
    terv.apellidouno,
    terv.apellidodos) 
    IS NULL OR CONCAT_WS(' ',
    terv.nombreuno,
    terv.nombredos,
    terv.apellidouno,
    terv.apellidodos) = '',
    (terv.razonsocial),
    CONCAT_WS(' ',
    terv.nombreuno,
    terv.nombredos,
    terv.apellidouno,
    terv.apellidodos)) AS NOMBRETERCERO,  


    v.cod_inter,
    tser.nombre AS nombreServicio,
    v.placa,
    v.porc_propiedad,
    v.tipo_vehiculo,
    v.tercero,
    terv.numeroidentificacion,
    v.tipo_serv

    FROM gc_vehiculo v

    LEFT JOIN gc_contribuyente c ON c.id_unico=v.contribuyente
    LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
    LEFT JOIN gc_tipo_vehiculo tv ON tv.id_unico=v.tipo_vehiculo
    LEFT JOIN gf_tercero terv ON terv.id_unico=v.tercero
    LEFT JOIN gc_tipo_servicio tser ON tser.id_unico=v.tipo_serv
    WHERE v.id_unico=$id";
        
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

        $("#fechaini").datepicker({changeMonth: true,});
        $("#fechaCierre").datepicker({changeMonth: true});


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
<div class="modal fade modalv" id="mdlModificar" role="dialog" align="center" aria-labelledby="mdlModificar" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title"  style="font-size: 24; padding: 3px;">Modificar Vehiculo</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                    <button type="button" id="btnCerrarModalMov1" class="btn btn-xs" style="color: #000;margin-left: -25%;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
          <div class="modal-body" style="margin-top: 8px">

                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/modificarVehiculosJson.php">
                    <div style="width: 500px;padding-left: -50%;" >

                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                        <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                        
                            <?php

                            $idTipoVehiculo=$row[8];
                            $cuentaI = "SELECT * FROM gc_tipo_vehiculo tv WHERE tv.id_unico!=$idTipoVehiculo  ORDER BY nombre ASC";
                            $rsctai = $mysqli->query($cuentaI);
                            ?>
                            <div class="form-group" style="margin-bottom: -10px;">
                                <label for="slaccom" class="col-sm-3" style="padding-left:55px">Tipo Vehiculo:</label>
                                <select style="width:70%;margin-right: -25%;text-align: left;" name="tipo" id="stv" required class="select2 form-control" title="Seleccione Tipo Vehiculo">
                                    <option value="<?php echo $row[8] ?>"><?php echo $row[2] ?></option>
                                        <?php while($rowtv=mysqli_fetch_row($rsctai)){ ?>
                                             <option value="<?php echo $rowtv[0] ?>"><?php echo $rowtv[1] ?></option>
                                        <?php } ?>
                                </select>
                            </div><br>

                            <?php

                            $idTerceroS=$row[9];
                            if($idTerceroS!=""){

                                $cuentaI = "SELECT t.id_unico,
                                            t.numeroidentificacion,
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
                                            t.apellidodos)) AS nombreTercero
                                 FROM gf_tercero t 
                                 WHERE t.id_unico!=$idTerceroS
                                 ORDER BY nombreTercero ASC";
                                $rsctai = $mysqli->query($cuentaI);
                                ?>
                            <div class="form-group" style="margin-bottom: -10px;">
                                <label for="ster" class="col-sm-3" style="padding-left:55px">Tercero:</label>
                                    <select  name="tercero" id="ster" class="select2_single form-control" title="Seleccione Tercero" style="width:70%;margin-right: -25%;text-align: left;">
                                        <option value="<?php echo $row[9] ?>"><?php echo ucwords(mb_strtolower($row[10]."-".$row[3])) ?></option>
                                            <?php while($rowtertser=mysqli_fetch_row($rsctai)){ ?>
                                                 <option value="<?php echo $rowtertser[0] ?>"><?php echo $rowtertser[1]." - ".ucwords(mb_strtolower($rowtertser[2])) ?></option>
                                            <?php } ?>
                                    </select>
                                </div><br>

                            <?php }else{ 
                                 $cuentaI = "SELECT t.id_unico,
                                                t.numeroidentificacion,
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
                                                t.apellidodos)) AS nombreTercero
                                     FROM gf_tercero t 
                                     ORDER BY nombreTercero ASC";
                                    $rsctai = $mysqli->query($cuentaI);
                                    ?>
                                 <div class="form-group" style="margin-bottom: -10px;">
                                       <label for="ster" class="col-sm-3" style="padding-left:55px">Tercero:</label>
                                        <select name="tercero" id="ster"  class="select2_single form-control" title="Seleccione Tercero" style="width:70%;margin-right: -25%;text-align: left;">
                                                  <option value="">Tercero</option>
                                                <?php while($rowtertser=mysqli_fetch_row($rsctai)){ ?>
                                                     <option value="<?php echo $rowtertser[0] ?>"><?php echo $rowtertser[1]." - ".ucwords(mb_strtolower($rowtertser[2])) ?></option>
                                                <?php } ?>
                                        </select>
                                    </div><br>
                            <?php } ?>

                            <div class="form-group" style="margin-bottom: 5px;">

                                <label for="icod" class="control-label col-sm-3" style="padding-right:25px">
                                    Código Interno: 
                                </label>
                                <input style="width:70%;"  type="text" name="codigo"  id="icodigo" class="form-control" maxlength="10" title=" Código" onkeypress="return txtValida(event,'num_car')" placeholder="Código Interno" value="<?php echo $row[4] ?>">
                            </div>

                            <?php
                            $idTipoServicioS=$row[11];

                            if($idTipoServicioS!=""){
                                $cuentaI = "SELECT * FROM gc_tipo_servicio tc WHERE tc.id_unico!=$idTipoServicioS ORDER BY nombre ASC";
                                $rsctai = $mysqli->query($cuentaI);
                                ?>
                                 <div class="form-group" style="margin-bottom: -10px;">
                                       <label for="stser" class="col-sm-3" style="padding-left:55px">Tipo Servicio:</label>
                                    <select name="tipoServicio" id="stser"  class="select2_single form-control" title="Seleccione Tipo Servicio" style="width:70%;margin-right: -25%;text-align: left;">
                                        <option value="<?php echo $row[11] ?>"><?php echo $row[5] ?></option>
                             
                                            <?php while($rowtser=mysqli_fetch_row($rsctai)){ ?>
                                                 <option value="<?php echo $rowtser[0] ?>"><?php echo $rowtser[1] ?></option>
                                            <?php } ?>
                                    </select>
                                </div><br>
                            <?php }else{ 
                                $cuentaI = "SELECT * FROM gc_tipo_servicio tc ORDER BY nombre ASC";
                                $rsctai = $mysqli->query($cuentaI);
                                ?>
                                 <div class="form-group" style="margin-bottom: -10px;">
                                  <label for="stser" class="col-sm-3" style="padding-left:55px">Tipo Servicio:</label>
                                    <select name="tipoServicio" id="stser"  class="select2_single form-control" title="Seleccione Tipo Servicio" style="width:70%;margin-right: -25%;text-align: left;">
                                        <option value="">Tipo Servicio</option>
                                            <?php while($rowtser=mysqli_fetch_row($rsctai)){ ?>
                                                 <option value="<?php echo $rowtser[0] ?>"><?php echo $rowtser[1] ?></option>
                                            <?php } ?>
                                    </select>
                                </div><br>
                            <?php } ?>

                            <div class="form-group" style="margin-bottom: 5px;">

                                <label for="icod" class="control-label col-sm-3" style="padding-right:25px"><strong style="color:#03C1FB;">*</strong>
                                    Placa: 
                                </label>
                                <input  style="width:70%;" type="text" name="placa"  id="iplaca" class="form-control" maxlength="10" title="Ingrese Placa" onkeypress="return txtValida(event,'num_car')" placeholder="Placa" required value="<?php echo $row[6] ?>">
                            </div>

                            <div class="form-group" style="margin-bottom: 5px;">
                                <label for="icod" class="control-label col-sm-3" style="padding-right:25px"><strong style="color:#03C1FB;">*</strong>
                                    Porcentaje Propiedad: 
                                </label>
                                <input  value="<?php echo $row[7] ?>"  type="text" name="porcentajePropiedad"  id="ipropiedad" class="form-control" maxlength="16" title="Ingrese Porcentaje" onkeypress="return txtValida(event,'decimales')" placeholder="Porcentaje Propiedad" required style="width:70%;">
                            </div><br>
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
  $("#stv").select2(); 
  $("#mdlModificar").draggable({
      handle: ".modal-header"
  });
  $("#ster").select2(); 
  $("#mdlModificar").draggable({
      handle: ".modal-header"
  });
  $("#stser").select2(); 
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


