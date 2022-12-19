<?php

/* 
 * ************
 * ***Autor*****
 * **DANIEL.NC***
 * ***************
 */

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
 $id = $_GET["id"];
 $queryCond = "SELECT   da.id_unico,
                        da.fechainicial,
                        da.fechafinal,
                        da.anioinicial,
                        da.aniofinal,
                        da.poralivionormal,
                        da.poraliviofinanciero,
                        da.abono,
                        da.pagototal,
                        da.impcapital,
                        da.impinteres,
                        da.sobrecapital,
                        da.sobreinteres,
                        da.todocapital,
                        da.todointeres,
                        da.alivio,
                        a.id_unico,
                        a.numacuerdo,
                        da.tipopredio,
                        tp.id_unico,
                        tp.nombre
    FROM gr_detalle_alivio da
    LEFT JOIN gr_alivio a       ON da.alivio     = a.id_unico
    LEFT JOIN gp_tipo_predio tp ON da.tipopredio = tp.id_unico
    WHERE md5(da.id_unico) = '$id'"; 
 $resul = $mysqli->query($queryCond);
 $row = mysqli_fetch_row($resul);
 
require_once './head.php';
?>
<title>Modificar Detalle Alivio</title>
    </head>
    <body>
        <link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script>
$().ready(function() {
  var validator = $("#form").validate({
        ignore: "",
    errorPlacement: function(error, element) {
      
      $( element )
        .closest( "form" )
          .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>

   <style>
    .form-control {font-size: 12px;}
    
</style>

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
       
        
        $("#sltFechaI").datepicker({changeMonth: true,}).val(fecAct);
        $("#sltFechaF").datepicker({changeMonth: true}).val(fecAct);
        
        
});
</script>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Detalle Alivio</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarDetalleAlivioPJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                              <!----------Script para invocar Date Picker-->
                            <script type="text/javascript">
                            $(document).ready(function() {
                               $("#datepicker").datepicker();
                            });
                            </script>
                            <!-- Campo para llenar Fecha Inicial -->
                            <div class="form-group">
                                <label class="control-label col-sm-2" >
                                    <strong class="obligado">*</strong>Fecha Inicial:
                                </label>
                                <input required="required" style="width:180px" class="col-sm-2 input-sm" type="text" name="sltFechaI" id="sltFechaI" value="<?php echo $row[1];?>" title="Ingrese Fecha Inicial">                                                  
                            <!-- Campo para llenar Abono-->
                                <label class="control-label col-sm-3" style="margin-top:-7px">
                                    <strong class="obligado">*</strong>Abono:
                                </label>
                                <?php
                                 if($row[7]==1)
                                 {
                                ?>
                                <input  type="radio" name="sltAbono" id="sltAbono"  value="1" checked>SI
                                <input  type="radio" name="sltAbono" id="sltAbono" value="2">NO
                                <?php
                                 }
                                 else
                                 {
                                ?>
                                <input  type="radio" name="sltAbono" id="sltAbono"  value="1">SI
                                <input  type="radio" name="sltAbono" id="sltAbono" value="2"checked>NO                                
                                <?php
                                 }
                                ?>
                                <!----------Fin Porcentajes-->
                            </div>
                            <!-- Campo para llenar Fecha Inicial y fecha final -->
                            <div class="form-group">
                                <label class="control-label col-sm-2">
                                    <strong class="obligado">*</strong>Fecha Final:
                                </label>
                                <input required="required" style="width:180px" class="col-sm-2 input-sm" type="text" name="sltFechaF" id="sltFechaF" value="<?php echo $row[2];?>" title="Ingrese Fecha Final">
                                <!----------Fin Fecha Final-->
                            <label class="control-label col-sm-3" style="margin-top:-8px">
                                    <strong class="obligado">*</strong>Pago Total:
                                </label>
                                <?php
                                 if($row[8]==1)
                                 {
                                ?>
                                <input  type="radio" name="sltPago" id="sltPago"  value="1" checked>SI
                                <input  type="radio" name="sltPago" id="sltPago" value="2">NO
                                
                                <?php 
                                 }
                                 else
                                 {?>
                                <input  type="radio" name="sltPago" id="sltPago"  value="1">SI
                                <input  type="radio" name="sltPago" id="sltPago" value="2" checked>NO
                                <!----------Fin Pago Total-->
                                 <?php 
                                 }?>
                            </div>
                            <!-- Campo para llenar Años Inicial y final -->
                            <div class="form-group">
                                <label class="control-label col-sm-2">
                                    <strong class="obligado">*</strong>Año Inicial:
                                </label>
                                <!-------Impuesto interes--------->
                                <input required="required" style="width:180px" class="col-sm-2 input-sm" type="text" name="txtAnioI" maxlength="4" id="txtAnioI" value="<?php echo $row[3]?>" title="Ingrese Año Inicial" onkeypress="return txtValida(event,'num')" placeholder="Año Inicial">
                                
                                <label class="control-label col-sm-3" style="margin-top:-7px">
                                    <strong class="obligado">*</strong>Imp. Interés:
                                </label>
                                <?php
                                 if($row[10]==1)
                                 {
                                ?>
                                <input  type="radio" name="sltImpint" id="sltImpint"  value="1" checked>SI
                                <input  type="radio" name="sltImpint" id="sltImpint" value="2">NO
                                
                                <?php
                                 }
                                 else
                                 {?>
                                <input  type="radio" name="sltImpint" id="sltImpint"  value="1">SI
                                <input  type="radio" name="sltImpint" id="sltImpint" value="2"checked>NO
                                 <?php }?>
                                <!----------Fin Impuesto Interes-->
                            </div>
                            <!-- Campo para llenar Año final -->
                            <div class="form-group">
                            <label class="control-label col-sm-2">
                                    <strong class="obligado">*</strong>Año Final:
                                </label>
                                <input required="required" style="width:180px" class="col-sm-2 input-sm" type="text" name="txtAnioF" maxlength="4" id="txtAnioF" value="<?php echo $row[4]?>" title="Ingrese Año Final" onkeypress="return txtValida(event,'num')" placeholder="Año Inicial">
                                <!----------Fin Año Final-->
                                <!----------Inicio Impuesto Capital-->
                            <label class="control-label col-sm-3" style="margin-top:-7px">
                                    <strong class="obligado">*</strong>Imp. Capital:
                                </label>
                                <?php
                                 if($row[9]==1)
                                 {
                                ?>
                                <input  type="radio" name="sltImpcap" id="sltImpcap"  value="1" checked>SI
                                <input  type="radio" name="sltImpcap" id="sltImpcap" value="2">NO
                                
                                <?php
                                 }
                                 else
                                 {?>
                                <input  type="radio" name="sltImpcap" id="sltImpcap"  value="1">SI
                                <input  type="radio" name="sltImpcap" id="sltImpcap" value="2"checked>NO
                                 <?php }?>
                                <!----------Fin Impuesto Capital-->
                            </div>
                            <!-- Campo para llenar Porcentajes Alivio Financiero y normal-->
                            <div class="form-group">
                                <label class="control-label col-sm-2" >
                                    <strong class="obligado">*</strong>% Alivio Normal:
                                </label>
                                <input required="required" style="width:180px" class="col-sm-2 input-sm" type="text" name="txtPoran" maxlength="4" id="txtPoran" value="<?php echo $row[5]?>" title="Ingrese % Alivio Normal Ej. 0.50" onkeypress="return txtValida(event,'decimales')" placeholder="% Alivio Normal">
                                <!---------- Inicio Sobre Interés------>
                                <label class="control-label col-sm-3" style="margin-top:-7px">
                                    <strong class="obligado">*</strong>Sobre Interés:
                                </label>
                                <?php
                                 if($row[12]==1)
                                 {
                                ?>
                                <input  type="radio" name="sltSobint" id="sltSobint"  value="1" checked>SI
                                <input  type="radio" name="sltSobint" id="sltSobint" value="2">NO
                                
                                <?php
                                 }else
                                 {?>
                                <input  type="radio" name="sltSobint" id="sltSobint"  value="1">SI
                                <input  type="radio" name="sltSobint" id="sltSobint" value="2" checked>NO
                                 <?php }?>
                                <!----------Fin Sobre Interés-->
                            </div>
                            <!-------- Porcentaje Alivio Financiero -------------->
                            <div class="form-group">
                                <label class="control-label col-sm-2">
                                    <strong class="obligado">*</strong>% Alivio Financiero:
                                </label>
                                <input required="required" style="width:180px" class="col-sm-2 input-sm" type="text" name="txtPoraf" maxlength="4" id="txtPoraf" value="<?php echo $row[6]?>" title="Ingrese % Alivio Financiero Ej. 0.50" onkeypress="return txtValida(event,'decimales')" placeholder="% Alivio Financiero">
                                <!----------Fin Alivio Financiero-->
                                <!----------Inicio Sobre Capital-->
                                <label class="control-label col-sm-3" style="margin-top:-7px">
                                    <strong class="obligado">*</strong>Sobre Capital:
                                </label>
                                <?php
                                 if($row[11]==1)
                                 {
                                ?>
                                <input  type="radio" name="sltSobcap" id="sltSobcap"  value="1" checked>SI
                                <input  type="radio" name="sltSobcap" id="sltSobcap" value="2">NO
                                
                                 <?php }
                                 else
                                 {?>
                                <input  type="radio" name="sltSobcap" id="sltSobcap"  value="1">SI
                                <input  type="radio" name="sltSobcap" id="sltSobcap" value="2" checked>NO
                                 <?php }?>
                            </div>
                                <!----------Fin Impuesto Capital-->
                                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td>
                                        <?php 
                                            $al = "SELECT id_unico, numacuerdo 
                                            FROM gr_alivio ORDER BY id_unico ASC";
                                            $ali = $mysqli->query($al);
                                        ?>
                                        <!----------Inicio Alivio-->
                                        <label style="margin-top:10px;margin-left:80px">
                                            <strong class="obligado">*</strong>Alivio:
                                        </label>
                                            <select name="sltAlivio" style="margin-top:-30px;margin-left:160px;width:180px" class="form-control" id="sltAlivio" title="Seleccione Alivio" style="height: 30px">
                                                <option value="<?php echo $row[16]?>"><?php echo $row[17]?></option>
                                            <?php 
                                                while ($filaA = mysqli_fetch_row($ali)) { ?>
                                                <option value="<?php echo $filaA[0]?>"><?php echo $filaA[1]?></option>
                                            <?php
                                                }
                                            ?>
                                            </select>
                                        </td>
                                        <td>
                                        <!-- Inicio Todo Capital -->
                                        <label style="margin-top:10px;margin-right:260px" >
                                            <strong class="obligado">*</strong>Todo Capital:
                                        </label>
                                        <?php
                                        if($row[13]==1)
                                        {
                                        ?>
                                        <input style="margin-left:-250px" type="radio" name="sltTodcap" id="sltTodcap"  value="1" checked>SI
                                        <input style="margin-left:0px" type="radio" name="sltTodcap" id="sltTodcap" value="2">NO
                                        <?php }
                                        else
                                        {
                                        ?>
                                        <input style="margin-left:-250px" type="radio" name="sltTodcap" id="sltTodcap"  value="1">SI
                                        <input style="margin-left:0px" type="radio" name="sltTodcap" id="sltTodcap" value="2" checked>NO
                                        <!----------Fin Todo Capital-->
                                        </td>
                                    </tr>
                                </thead>
                                </table>
                                <table class="display" cellspacing="0" width="100%">
                                    <tr>
                                        <td>
                                        <?php 
                                            $ti = "SELECT id_unico, nombre 
                                            FROM gp_tipo_predio ORDER BY id_unico ASC";
                                            $tip = $mysqli->query($ti);
                                        ?>
                                        <!----------Inicio Tipo Predio-->
                                            <label style="margin-left:40px;margin-top:-7px">
                                                <strong class="obligado">*</strong>Tipo Predio:
                                            </label>
                                            <select name="sltTipo" style="margin-top:-30px;margin-left:165px;width:180px" class="form-control" id="sltTipo" title="Seleccione Tipo Predio" style="height: 30px">
                                            <option value="<?php echo $row[19]?>"><?php echo $row[20]?></option>
                                            <?php 
                                                while ($filaTP = mysqli_fetch_row($tip)) { ?>
                                            <option value="<?php echo $filaTP[0]?>"><?php echo $filaTP[1]?></option>
                                            <?php
                                            }
                                            ?>
                                            </select>
                                        </td>
                                        <td>
                                        <!----------Inicio Todo Interes-->
                                        <label style="margin-bottom:30px;margin-right:250px">
                                            <strong class="obligado">*</strong>Todo Interés:
                                        </label>
                                        <?php
                                        if($row[8]==1)
                                        {
                                        ?>
                                        <input style="margin-left:-240px" type="radio" name="sltTodint" id="sltTodint"  value="1" checked>SI
                                        <input style="margin-left:0px" type="radio" name="sltTodint" id="sltTodint" value="2">NO
                                        <?php }
                                        else
                                        {?>
                                        <input style="margin-left:-240px" type="radio" name="sltTodint" id="sltTodint"  value="1">SI
                                        <input style="margin-left:0px" type="radio" name="sltTodint" id="sltTodint" value="2" checked>NO
                                        <?php }?>
                                        <!----------Fin Todo Interés-->
                                        </td>
                                    </tr>
                                </table>                                      
                            <div class="form-group" style="margin-top: 10px;">
                              <label for="no" class="col-sm-5 control-label"></label>
                              <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                            </div> 
                          </form>
                           </div>
              </div>
                  
              
        <?php require_once './footer.php'; ?>
    </body>
</html>