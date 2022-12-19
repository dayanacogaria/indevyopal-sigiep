<?php


require_once('Conexion/conexion.php');
require_once 'head.php'; ?>
<title>Listado Predio</title>
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltTci-error, #sltTcf-error, #fechaini-error, #fechafin-error, #scid-error, #scfd-error, #scidic-error, #scfdic-error, #sti-error, #stf-error  {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;

    }

    body{
        font-size: 12px;
    }
    
</style>

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
            rules: {
                sltmes: {
                    required: true
                },
                sltcni: {
                    required: true
                },
                sltAnnio: {
                    required: true
                }
            }
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
        $("#fechaini").datepicker({changeMonth: true,}).val(fecAct);
        $("#fechafin").datepicker({changeMonth: true}).val(fecAct);


    });
</script>

<!-- contenedor principal -->
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once ('menu.php'); ?>

        <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px">
            <h2 align="center" class="tituloform">Listado Predio</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                

                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes/generar_INF_PREDIO_EXCEL.php" target=”_blank”>
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    <div class="form-group">
                        
                         <!--CONCEPTO INICIAL Y FINAL FACTURA-->

                        <?php

                            $sql= "SELECT p.id_unico,p.codigo_catastral,p.matricula_inmobiliaria,c.nombre FROM `gp_predio1` p
                                LEFT JOIN  gf_ciudad c ON c.id_unico=p.ciudad
                                 ORDER BY p.id_unico ASC";
                            $rsTcf = $mysqli->query($sql);

                        ?>
                        <div id="PredioInicial"  class="form-group" style="margin-top: -5px">
                            <!--predio inicial-->


                            <label for="sltTci" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Predio Inicial:</label>
                            <select  name="PredioInicial" id="sltTci" class="select2_single form-control" title=
                            "Seleccione Predio Inicial" style="height: 30px" required >
                                <option value="" >Predio Inicial</option>
                                <?php

                                $predioInicialOculto="";
                                $predioFinalOculto="";
                                while ($f=mysqli_fetch_row($rsTcf))
                                {
                                    $predioInicialOculto=ucwords(mb_strtolower($f[1]." - ".$f[2]." - ".$f[3]));

                                    ?>
                                    <option value="<?php echo $f[0];?>"><?php echo $predioInicialOculto; ?></option>

                                    <?php
                                }
                                ?>
                            </select>
                        </div>

                        <!--predio final-->
                        <?php
                        $tcf= "SELECT p.id_unico,p.codigo_catastral,p.matricula_inmobiliaria,c.nombre FROM `gp_predio1` p 
                           LEFT JOIN  gf_ciudad c ON c.id_unico=p.ciudad
                           ORDER BY p.id_unico DESC";
                        $rsTcf = $mysqli->query($tcf);
                        ?>
                        <div id="PredioFinal"  class="form-group" style="margin-top: -5px">
                            <label for="sltTcf" class="control-label col-sm-5">
                                <strong class="obligado">*</strong>Predio Final:
                            </label>
                            <select name="PredioFinal" class="select2_single form-control" id="sltTcf" title="Seleccione Predio Final" style="height: 30px"   required>
                                <option value="">Predio Final</option>
                                <?php
                                while ($filaTcf = mysqli_fetch_row($rsTcf)) { 
                                    $predioFinalOculto=ucwords(mb_strtolower($filaTcf[1]." - ".$filaTcf[2]." - ".$filaTcf[3]));
                                    ?>

                                    <option value="<?php echo $filaTcf[0];?>"><?php echo $predioFinalOculto; ?></option>
                             <?php
                                }
                                ?>
                            </select>
                        </div>

                     

                        
                        <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                                <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                         
                            <!-- Script para los botones  -->                
                        </div>
                    </div>


                </form>
            </div>
        </div>
        <!-- Fin del Contenedor principal -->
        <!--Información adicional -->
    </div>
<script src="js/select/select2.full.js"></script>
<script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
</script>
    <!-- Llamado al pie de pagina -->

</div>
<?php require_once 'footer.php' ?>
</body>
</html>