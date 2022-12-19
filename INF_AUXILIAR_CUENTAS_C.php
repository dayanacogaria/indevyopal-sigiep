<?php
########################################################################################
#       ***************    Modificaciones *************** #
########################################################################################
#04/04/2019 | Creado
########################################################################################
require_once('Conexion/ConexionPDO.php');
require_once('Conexion/ConexionPDO.php');
require 'jsonPptal/funcionesPptal.php';
require_once('head_listar.php');
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$nanno = anno($anno);
$n = 'Informe Auxiliar';
$action ='';
$n ='Auxiliar Contable Por Cuenta';
$action ='informes_consolidado/INF_AUXILIAR_CUENTA.php?t=1';
?>
<html>
    <head>
        <title><?php echo $n;?></title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script> 
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="js/md5.pack.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <script>
            $().ready(function () {
                var validator = $("#form").validate({
                    ignore: "",
                    errorPlacement: function (error, element) {

                        $(element)
                                .closest("form")
                                .find("label[for='" + element.attr("id") + "']")
                                .append(error);
                    },
                });

                $(".cancel").click(function () {
                    validator.resetForm();
                });
            });
        </script>
        <style>
            body{
                font-size: 12px;
            }       
            label#periodoF-error, #cuenta-error {
                display: block;
                color: #bd081c;
                font-weight: bold;
                font-style: italic;
            }
        </style>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 align="center" class="tituloform" style="margin-top:-3px"><?php echo $n;?></h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="<?php echo $action;?>" target=”_blank” >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="cuenta" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Cuenta:</label>
                                <select name="cuenta" id="cuenta" class="select2_single form-control" title="Seleccione Cuenta" style="height: auto " required>
                                    <?php 
                                    echo '<option value="">Cuenta</option>';
                                    $vg = $con->Listar("SELECT codi_cuenta, nombre 
                                        FROM gf_cuenta 
                                        WHERE parametrizacionanno = $anno 
                                           AND LENGTH(codi_cuenta)>=4
                                        ORDER BY codi_cuenta ASC");
                                    for ($i = 0; $i < count($vg); $i++) {
                                       echo '<option value="'.$vg[$i][0].'">'.$vg[$i][0].' - '.$vg[$i][1].'</option>'; 
                                    }                                    
                                    ?>
                                </select>
                            </div>
                            <input type="hidden" name="sltAnnio" id="sltAnnio" value="<?php echo $anno ?>"/>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="periodoF" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Periodo Final:</label>
                                <select required name="periodoF" id="periodoF" style="height: auto" class="select2_single form-control" title="Seleccione Periodo Final" >
                                    <option value="">Periodo Final</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="tipoa" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tipo:</label>
                                <select required name="tipoa" id="tipoa" style="height: auto" class="select2_single form-control" title="Seleccione Tipo" >
                                    <option value="">Tipo</option>
                                    <option value="1">Por Tercero</option>
                                    <option value="2">Por Institución Educativa</option>
                                </select>
                            </div>
                            <div align="center">
                                <button type="submit" class="btn btn-primary sombra" style="margin-top: 0px; margin-bottom: 10px; margin-left: -100px;" >Generar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script src="js/select/select2.full.js"></script>
        <script>
            $(document).ready(function () {
                $(".select2_single").select2({
                    allowClear: true,
                });
            });
        </script>
        <script>    
            $(document).ready(function (){
               var form_data={action: 2, annio :$("#sltAnnio").val()};
               var optionMI ="<option value=''>Periodo Final</option>";
               $.ajax({
                  type:'POST', 
                  url:'jsonPptal/consultasInformesCnt.php',
                  data: form_data,
                  success: function(response){
                      console.log($("#sltAnnio").val());
                      console.log(response);
                      optionMI =optionMI+response;
                      $("#periodoF").html(optionMI).focus();              
                  }
               });
            });
        </script>
    </body>
</html>
</html>

