<?php


################ MODIFICACIONES ####################
#07/06/2017 | Anderson Alarcon | mejore consultas de selects Tipo, predio y concepto predial  
############################################

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();

 $id = $_GET["id"];
 $queryCond = "SELECT   ep.id_unico,
                        ep.resolucion,
                        ep.fechainicial,
                        ep.fechafinal,
                        ep.porcentajeex,
                        ep.observaciones,
                        ep.tipo,
                        te.id_unico,
                        te.nombre,
                        ep.predio,
                        pr.id_unico,
                        pr.nombre,
                        ep.concepto,
                        cp.id_unico,
                        cp.nombre
    FROM gr_excensiones_predio ep
    LEFT JOIN gr_tipo_excension te      ON ep.tipo   = te.id_unico
    LEFT JOIN gp_predio1 pr             ON ep.predio = pr.id_unico
    LEFT JOIN gr_concepto_predial cp    ON ep.concepto = cp.id_unico
    WHERE md5(ep.id_unico) = '$id'"; 
 $resul = $mysqli->query($queryCond);
 $row = mysqli_fetch_row($resul);
 
 
 
$dat1=date_create($row[2]); 
$fec1 = date_format($dat1,"d/m/Y");
$dat2=date_create($row[3]); 
$fec2 = date_format($dat1,"d/m/Y");

require_once './head.php';
?>
<title>Modificar Excensiones Predio</title>
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
        $("#sltFechaF").datepicker({changeMonth: true,}).val(fecAct);
        
        
});
</script>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Excensiones Predio</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarExcensionesPredioPJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                              <!------------------------- Campo para llenar Resolución-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Resolución:
                            </label>
                            <input required="required" value="<?php echo $row[1]?>" type="text" name="txtResolucion" id="txtResolucion" class="form-control" maxlength="50" title="Ingrese la resolución" onkeypress="return txtValida(event,'num_car')" placeholder="Resolución" >
                        </div>
                        <!----------Fin Resolución-->
                        <!----------Script para invocar Date Picker-->
                        <script type="text/javascript">
                        $(document).ready(function() {
                           $("#datepicker").datepicker();
                        });
                        </script>
                        <!--Campo para captura de Fecha Inicial-->
                                                <div class="form-group" style="margin-top: 0px;">
                                                    <label for="sltFechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Inicial:</label>
                                                    <input style="width:auto" class="col-sm-2 input-sm" type="text" name="sltFechaI" id="sltFechaI"value="<?php echo $row[2];?>" title="Ingrese Fecha Inicial">
                                                </div>
                        <!----------Fin Captura de Fecha Inicial-->     
                        <!--Campo para captura de Fecha Final-->
                                                <div class="form-group" style="margin-top: 0px;">
                                                    <label for="sltFechaF" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Final:</label>
                                                    <input style="width:auto" class="col-sm-2 input-sm" type="text" name="sltFechaF" id="sltFechaF" value="<?php echo $row[3];?>" title="Ingrese Fecha Final">
                                                </div>
                        <!----------Fin Captura de Fecha Final-->     
                        <!------------------------- Campo para llenar Porcentaje Excensión-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Porc. Ex.:
                            </label>
                            <input required="required" type="text" value="<?php echo $row[4]?>" name="txtPorex" id="txtPorex" class="form-control" maxlength="5" title="Ingrese el porcentaje excensión" onkeypress="return txtValida(event,'decimales')" placeholder="Porcentaje Ex.">
                        </div>
                        <!----------Fin Porcentaje Excensión-->
                        <!------------------------- Campo para llenar Observaciones-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Observaciones:
                            </label>
                            <input type="text" name="txtObservaciones" value="<?php echo $row[5]?>" id="txtObservaciones" class="form-control" maxlength="500" title="Ingrese las observaciones" onkeypress="return txtValida(event,'todas')" placeholder="Observaciones">
                        </div>
                        <!----------Fin Observaciones-->
                        <!------------------------- Consulta para llenar campo Tipo-->
                            <?php 
                            $tip = "SELECT id_unico, nombre 
                                FROM gr_tipo_excension WHERE id_unico != $row[6] ORDER BY id_unico ASC";
                            $tipo = $mysqli->query($tip);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Tipo:
                            </label>
                            <select required="required" name="sltTipo" class="form-control" id="sltTipo" title="Seleccione Tipo Excensión" style="height: 30px">
                            <option value="<?php echo $row[7]?>"><?php echo $row[8]?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($tipo)) { ?>
                                <option value="<?php echo $filaT[0]?>"><?php echo $filaT[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Tipo-->
                        <!------------------------- Consulta para llenar campo Predio-->
                            <?php 
                            $pr = "SELECT id_unico, nombre 
                                FROM gp_predio1 WHERE nombre IS NOT NULL 
                                AND id_unico != $row[9] ORDER BY id_unico ASC";
                            $pred = $mysqli->query($pr);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Predio:
                            </label>
                            <select required="required" name="sltPredio" class="form-control" id="sltPredio" title="Seleccione Predio" style="height: 30px">
                            <option value="<?php echo $row[10]?>"><?php echo $row[11]?></option>
                                <?php 
                                while ($filaP = mysqli_fetch_row($pred)) { ?>
                                <option value="<?php echo $filaP[0]?>"><?php echo $filaP[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Predio-->
                        <!------------------------- Consulta para llenar campo Concepto Predial-->
                            <?php
                            if($row[12]=="")
                            $co = "SELECT id_unico, nombre 
                                FROM gr_concepto_predial ORDER BY id_unico ASC";
                            else
                            $co = "SELECT id_unico, nombre 
                                FROM gr_concepto_predial 
                                WHERE id_unico != $row[12] ORDER BY id_unico ASC";
                            
                            $con = $mysqli->query($co);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Concepto Predial:
                            </label>
                            <select name="sltConcepto" class="form-control" id="sltConcepto" title="Seleccione Concepto" style="height: 30px">
                            <option value="<?php echo $row[13]?>"><?php echo $row[14]?></option>
                                <?php 
                                while ($filaC = mysqli_fetch_row($con)) { ?>
                                <option value="<?php echo $filaC[0]?>"><?php echo $filaC[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Concepto Predial-->
                            <div class="form-group" style="margin-top: 10px;">
                              <label for="no" class="col-sm-5 control-label"></label>
                              <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                            </div>


                          </form>
                      </div>
                  </div>                  
              </div>
        </div>
        <?php require_once './footer.php'; ?>
    </body>
</html>