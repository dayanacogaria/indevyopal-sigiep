<?php

require 'Conexion/conexion.php';

 
if (isset($_POST['id'])){

    $id=$_POST['id'];
    $sql="SELECT e.id_unico,
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
            t.apellidodos)) AS NOMBRETERCEROCONTRIBUYENTE, 
            e.nombre,
            DATE_FORMAT(e.fechainicioAct,'%d/%m/%Y') AS fechaFacConvertida,
            est.nombre,
            e.direccion,
            e.cod_catastral,
            ciu.nombre,
            b.nombre,
            l.nombre,
            te.nombre,
            tame.nombre,
            e.estrato,
            e.ciudad,
            e.barrio,
            e.localizacion,
            e.tipo_entidad,
            e.tamanno_entidad
             
    FROM gc_establecimiento e
    LEFT JOIN gc_contribuyente c ON c.id_unico=e.contribuyente
    LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
    LEFT JOIN gc_estrato est ON est.id_unico=e.estrato
    LEFT JOIN gf_ciudad ciu ON ciu.id_unico=e.ciudad
    LEFT JOIN gp_barrio b ON b.id_unico=e.barrio
    LEFT JOIN gc_localizacion l ON l.id_unico=e.localizacion
    LEFT JOIN gf_tipo_entidad te ON te.id_unico=e.tipo_entidad
    LEFT JOIN gc_tamanno_entidad tame ON tame.id_unico=e.tamanno_entidad
    WHERE e.id_unico=$id";

    $resultado = $mysqli->query($sql);
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

        $("#fii").datepicker({changeMonth: true,});
        $("#fcc").datepicker({changeMonth: true});


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
<div class="modal fade modale" id="mdlModificar" role="dialog" align="center" aria-labelledby="mdlModificar" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title"  style="font-size: 24; padding: 3px;">Modificar Establecimiento</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                    <button type="button" id="btnCerrarModalMov1" class="btn btn-xs" style="color: #000;margin-left: -25%;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
          <div class="modal-body" style="margin-top: 8px">

     <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/modificarEstablecimientosJson.php">
             <div style="width: 500px;padding-left: -50%;" >
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    
                        <div class="form-group" style="margin-bottom: 5px;">
                            <label for="inombre" class="col-sm-3" style="padding-left:55px">   
                                Nombre: 
                            </label>
                            <input  style="width: 70%;" type="text" name="nombre"  id="inombre" class="form-control" maxlength="100" title="Ingrese Nombre" onkeypress="return txtValida(event,'num_car')" placeholder="Nombre" value="<?php echo $row[2] ?>" >
                        </div>


                        <div  class="form-group" style="margin-bottom: 5px;">
                            <label for="fechaini" type = "date" class="col-sm-3" style="padding-left: 25px"><strong class="obligado">*</strong>Fecha Inicio:</label>
                            <input style="width: 70%;"  class="form-control" type="text" name="fecha" id="fii"  value="<?php echo $row[3] ?>" required>
                        </div>
                        <?php

                        $idEstratoS=$row[12];
                        $cuentaI = "SELECT * FROM gp_estrato e WHERE e.id_unico!=$idEstratoS ORDER BY nombre ASC";
                        $rsctai = $mysqli->query($cuentaI);
                        ?>
                        <div class="form-group" style="margin-bottom: -10px;">
                            <label for="sestrato" class="control-label col-sm-3" style="padding-right:21px"><strong style="color:#03C1FB;">*</strong>Estrato:</label>
                            <select name="estrato" id="se" required style="width:70%;margin-right: -25%;text-align: left;" class="select2 form-control" title="Seleccione Estrato">
                                <option value="<?php echo $row[12] ?>"><?php echo $row[4] ?></option>
                                    <?php while($rowE=mysqli_fetch_row($rsctai)){ ?>
                                         <option value="<?php echo $rowE[0] ?>"><?php echo $rowE[1] ?></option>
                                    <?php } ?>
                            </select>
                        </div><br>

                        <div class="form-group" style="margin-bottom: 5px;">

                            <label for="cact" class="col-sm-3" style="padding-left:45px">
                                Dirección: 
                            </label>
                            <input  style="width:70%;" type="text" name="direccion"  id="cact" class="form-control" maxlength="50" title="Dirección" onkeypress="return txtValida(event,'num_car')" placeholder="Dirección" value="<?php echo $row[5] ?>">
                        </div>



                        <div class="form-group" style="margin-bottom: 5px;">

                            <label for="icod" class="control-label col-sm-3" style="padding-right:25px">
                                Código Catastral: 
                            </label>
                            <input style="width:70%;" type="text" name="codigo"  id="icod" class="form-control" maxlength="15" title=" Código Catastral" onkeypress="return txtValida(event,'num_car')" placeholder="Código Catastral" value="<?php echo $row[6] ?>">
                        </div>



                        <?php

                        $idCiudadS=$row[13];
                        $cuentaI = "SELECT * FROM gf_ciudad c WHERE c.id_unico!=$idCiudadS ORDER BY nombre ASC";
                        $rsctai = $mysqli->query($cuentaI);
                        ?>
                        <div class="form-group" style="margin-bottom: -10px;">
                            <label for="sciudad" class="col-sm-3" style="padding-left: 49px;">Ciudad:</label>
                            <select  name="ciudad" id="sc"  style="width:70%;margin-right: -25%;text-align: left;" class="select2 form-control" title="Seleccione Ciudad">
                                        <option value="<?php echo $row[13] ?>"><?php echo $row[7] ?></option>
                                    <?php while($rowC=mysqli_fetch_row($rsctai)){ ?>
                                         <option value="<?php echo $rowC[0] ?>"><?php echo $rowC[1] ?></option>
                                    <?php } ?>
                            </select>
                        </div><br>


                        <?php
                        $idBarrioS=$row[14];

                        if($idBarrioS!=""){ 

                            $cuentaI = "SELECT * FROM gp_barrio b WHERE b.id_unico!=$idBarrioS ORDER BY nombre ASC";
                            $rsctai = $mysqli->query($cuentaI);
                            ?>

                            <div class="form-group" style="margin-bottom: -10px;">
                                <label for="slaccom" class="col-sm-3" style="padding-left: 60px">Barrio:</label>
                                <select  name="barrio" id="sb"  style="width:70%;margin-right: -25%;text-align: left;" class="select2 form-control" title="Seleccione Barrio">
                                    <option value="<?php echo $row[14] ?>"><?php echo $row[8] ?></option>
                                        <?php while($rowB=mysqli_fetch_row($rsctai)){ ?>
                                             <option value="<?php echo $rowB[0] ?>"><?php echo $rowB[1] ?></option>
                                        <?php } ?>
                                </select>
                            </div><br>

                       <?php  }else{

                            $cuentaI = "SELECT * FROM gp_barrio b  ORDER BY nombre ASC";
                            $rsctai = $mysqli->query($cuentaI);
                            ?>
                            <div class="form-group" style="margin-bottom: -10px;">
                                <label for="slaccom" class="col-sm-3" style="padding-left: 60px">Barrio:</label>
                                <select name="barrio" id="sb"  style="width:70%;margin-right: -25%;text-align: left;" class="select2 form-control" title="Seleccione Barrio">
                                    <option value="">Barrio</option>
                                        <?php while($rowB=mysqli_fetch_row($rsctai)){ ?>
                                             <option value="<?php echo $rowB[0] ?>"><?php echo $rowB[1] ?></option>
                                        <?php } ?>
                                </select>
                            </div><br>

                        <?php } ?>


                        <?php
                        $idLocalizacionS=$row[15]; 

                        if($idLocalizacionS!=""){
                
                            $cuentaI = "SELECT * FROM gc_localizacion tn WHERE tn.id_unico!=$idLocalizacionS ORDER BY nombre ASC";
                            $rsctai = $mysqli->query($cuentaI);
                            ?>
                            <div class="form-group" style="margin-bottom: -10px;">
                                <label for="slaccom" class="col-sm-3" style="padding-left:25px">Localización:</label>
                                <select  name="localizacion" id="sl"  style="width:70%;margin-right: -25%;text-align: left;" class="select2 form-control" title="Seleccione Localización">
                                    <option value="<?php echo $row[15] ?>"><?php echo $row[9] ?></option>
                         
                                        <?php while($rowLTE=mysqli_fetch_row($rsctai)){ ?>
                                             <option value="<?php echo $rowLTE[0] ?>"><?php echo $rowLTE[1] ?></option>
                                        <?php } ?>
                                </select>
                            </div><br>

                       <?php  }else{  

                            $cuentaI = "SELECT * FROM gc_localizacion tn ORDER BY nombre ASC";
                            $rsctai = $mysqli->query($cuentaI);
                            ?>
                            <div class="form-group" style="margin-bottom: -10px;">
                                <label for="slaccom" class="col-sm-3" style="padding-left:25px">Localización:</label>
                                <select  name="localizacion" id="sl"  style="width:70%;margin-right: -25%;text-align: left;" class="select2 form-control" title="Seleccione Localización">
                                    <option value="">Localización</option>
                         
                                        <?php while($rowLTE=mysqli_fetch_row($rsctai)){ ?>
                                             <option value="<?php echo $rowLTE[0] ?>"><?php echo $rowLTE[1] ?></option>
                                        <?php } ?>
                                </select>
                            </div><br>
                        <?php } ?>
                        

                        <?php
                        $idTipoEntidadS=$row[16];

                        if($idTipoEntidadS!=""){

                            $cuentaI = "SELECT * FROM gf_tipo_entidad tn WHERE tn.id_unico!=$idTipoEntidadS ORDER BY nombre ASC";
                            $rsctai = $mysqli->query($cuentaI);
                            ?>
                            <div class="form-group" style="margin-bottom: -10px;">
                                <label for="slaccom" class="col-sm-3" style="padding-left:55px">Tipo Entidad:</label>
                                <select  name="tipoEntidad" id="sen"  style="width:70%;margin-right: -25%;text-align: left;" class="select2 form-control" title="Seleccione Tipo Entidad">
                                    <option value="<?php echo $row[16] ?>"><?php echo $row[10] ?></option>
                         
                                        <?php while($rowTE=mysqli_fetch_row($rsctai)){ ?>
                                             <option value="<?php echo $rowTE[0] ?>"><?php echo $rowTE[1] ?></option>
                                        <?php } ?>
                                </select>
                            </div><br>

                        <?php }else{ 

                            $cuentaI = "SELECT * FROM gf_tipo_entidad tn ORDER BY nombre ASC";
                            $rsctai = $mysqli->query($cuentaI);
                            ?>
                            <div class="form-group" style="margin-bottom: -10px;">
                                <label for="slaccom" class="col-sm-3" style="padding-left:55px">Tipo Entidad:</label>
                                <select  name="tipoEntidad" id="sen"  style="width:70%;margin-right: -25%;text-align: left;" class="select2 form-control" title="Seleccione Tipo Entidad">
                                    <option value="">Tipo Entidad</option>
                         
                                        <?php while($rowTE=mysqli_fetch_row($rsctai)){ ?>
                                             <option value="<?php echo $rowTE[0] ?>"><?php echo $rowTE[1] ?></option>
                                        <?php } ?>
                                </select>
                            </div><br>
                        <?php } ?>

                        <?php
                        $idTamannoEntidadS=$row[17];
                        if($idTamannoEntidadS!=""){

                            $cuentaI = "SELECT * FROM gc_tamanno_entidad te WHERE te.id_unico!=$idTamannoEntidadS ORDER BY nombre ASC";
                            $rsctai = $mysqli->query($cuentaI);
                            ?>
                            <div class="form-group" style="margin-bottom: -10px;">
                                <label for="slaccom" class="col-sm-3">Tamaño Entidad:</label>
                                <select style="width:70%;margin-right: -25%;text-align: left;" name="tamannoEntidad" id="sac"   class="select2 form-control" title="Seleccione Tamaño Entidad">
                                    <option value="<?php echo $row[17] ?>"><?php echo $row[11] ?></option>
                                        <?php while($rowTAE=mysqli_fetch_row($rsctai)){ ?>
                                             <option value="<?php echo $rowTAE[0] ?>"><?php echo $rowTAE[1] ?></option>
                                        <?php } ?>
                                </select>
                            </div>

                        <?php }else{ 
                            $cuentaI = "SELECT * FROM gc_tamanno_entidad te ORDER BY nombre ASC";
                            $rsctai = $mysqli->query($cuentaI);
                            ?>
                            <div class="form-group" style="margin-bottom: -10px;">
                                <label for="slaccom" class="col-sm-3">Tamaño Entidad:</label>
                                <select style="width:70%;margin-right: -25%;text-align: left;" name="tamannoEntidad" id="sac"   class="select2_single form-control" title="Seleccione Tamaño Entidad">
                                    <option value="">Tamaño Entidad</option>
                                        <?php while($rowTAE=mysqli_fetch_row($rsctai)){ ?>
                                             <option value="<?php echo $rowTAE[0] ?>"><?php echo $rowTAE[1] ?></option>
                                        <?php } ?>
                                </select>
                            </div>

                        <?php } ?>
                     <br><br><br>
                     <div class="form-group" style="margin-top: 7px;">
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
  $("#sac").select2(); 
  $("#mdlModificar").draggable({
      handle: ".modal-header"
  });
  $("#se").select2(); 
  $("#mdlModificar").draggable({
      handle: ".modal-header"
  });
  $("#sc").select2(); 
  $("#mdlModificar").draggable({
      handle: ".modal-header"
  });
  $("#sb").select2(); 
  $("#mdlModificar").draggable({
      handle: ".modal-header"
  });
  $("#sl").select2(); 
  $("#mdlModificar").draggable({
      handle: ".modal-header"
  });
  $("#sen").select2(); 
  $("#mdlModificar").draggable({
      handle: ".modal-header"
  });
</script>

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


