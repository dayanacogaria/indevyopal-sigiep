<?php

################ MODIFICACIONES ####################
#12/06/2017 | Anderson Alarcon | cambie el select de estado
####################################################

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();

 $id = $_GET["id"];
 $queryCond = "SELECT          r.id_unico,
                        r.nombre,
                        r.numero,
                        r.fecha,
                        r.ruta,
                        r.observaciones,
                        r.estado,
                        e.id_unico,
                        e.nombre
    FROM gr_resolucion r
    LEFT JOIN gr_estado_resolucion e ON r.estado  = e.id_unico
    WHERE md5(r.id_unico) = '$id'"; 
 $resul = $mysqli->query($queryCond);
 $row = mysqli_fetch_row($resul);
 
 $rid  = $row[0];
 $rnom = $row[1];
 $rnum = $row[2];
 $rfec = $row[3];
 $rrut = $row[4];
 $robs = $row[5];
 $rest = $row[6];
 $eid  = $row[7];
 $enom = $row[8];
 
$date=date_create($rfec); 
$fec = date_format($date,"d/m/Y");

require_once './head.php';
?>
<title>Modificar Resolución</title>
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
       
        
        $("#sltFecha").datepicker({changeMonth: true,}).val(fecAct);
        
        
});
</script>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Resolución</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarResolucionPJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <!------------------------- Campo para llenar Nombre-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Nombre:
                            </label>
                            <input type="text" name="txtNombre" id="txtNombre" class="form-control" maxlength="100" title="Ingrese el Nombre" onkeypress="return txtValida(event,'car')" value="<?php echo $rnom?>" placeholder="Nombre" >
                        </div>
                        <!----------Fin Nombre-->
                            <!------------------------- Campo para llenar Número-->
                        <div class="form-group" style="margin-top: -10px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Número:
                            </label>
                            <input required="required" type="text" name="txtNumero" id="txtNumero" class="form-control" maxlength="500" title="Ingrese el número" value="<?php echo $rnum?>" onkeypress="return txtValida(event,'num_car')" placeholder="Número">
                           </div>
                        <!----------Fin Número-->
                        <!----------Script para invocar Date Picker-->
                        <script type="text/javascript">
                        $(document).ready(function() {
                           $("#datepicker").datepicker();
                        });
                        </script>
                        <!--Campo para captura de Fecha-->
                                                <div class="form-group" style="margin-top: 0px;">
                                                    <label for="sltFecha" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha:</label>
                                                    <input style="width:auto" class="col-sm-2 input-sm" type="text" name="sltFecha" id="sltFecha" value="<?php echo $fec?>" title="Ingrese Fecha">
                                                </div>
                        <!----------Fin Captura de Fecha-->     
                        <!------------------------- Campo para llenar Ruta-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Ruta:
                            </label>
                            <input type="text" name="txtRuta" id="txtRuta" value="<?php echo $rrut?>" class="form-control" maxlength="500" title="Ingrese la ruta" onkeypress="return txtValida(event,'todas')" placeholder="Ruta">
                        </div>
                        <!----------Fin Ruta-->
                        <!------------------------- Campo para llenar Observaciones-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Observaciones:
                            </label>
                            <input type="text" name="txtObservaciones" value="<?php echo $robs?>" id="txtObservaciones" class="form-control" maxlength="500" title="Ingrese las observaciones" onkeypress="return txtValida(event,'todas')" placeholder="Observaciones">
                        </div>
                        <!----------Fin Observaciones-->
                        <!------------------------- Consulta para llenar campo Tipo-->
                            <?php 
                            
                            //validacion, si la resolucion a editar no tiene estado
                            if($rest==""){
                                $rest=0;
                            }
                            
                            $es = "SELECT id_unico, nombre 
                                FROM gr_estado_resolucion WHERE id_unico != $rest ORDER BY id_unico ASC";
                            $est = $mysqli->query($es);
                            

                             ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Estado:
                            </label>
                            <select name="sltEstado" class="form-control" id="sltTipo" title="Seleccione Estado" style="height: 30px">
                            <option value="<?php echo $rest?>"><?php echo $enom?></option>
                                <?php 
                                while ($filaE = mysqli_fetch_row($est)) { ?>
                                <option value="<?php echo $filaE[0]?>"><?php echo $filaE[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Tipo-->
                                      
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