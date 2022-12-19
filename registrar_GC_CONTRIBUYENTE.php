<?php
################################################################################
#
#Modificado por: Nestor B |03/11/2017| se modificó la consulta que trae el tercero
#
################################################################################

require_once('Conexion/conexion.php');
require_once 'head.php'; ?>
<title>Registrar Contribuyente</title>
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>

    label #cact-error,#sltctai-error,#cante-error,#codp-error, #sltEst-error, #fechaini-error, #txtDirC-error{
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


        $("#fechaini").datepicker({changeMonth: true,})
        


    });
</script>



<!-- contenedor principal -->
<div class="container-fluid text-center">

    <div class="row content">
        <?php require_once ('menu.php'); ?>

<div class="col-sm-7 text-left" style="margin-top:-10px">
      
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-bottom: 10px;">Registrar  Contribuyente</h2>

      <!--Volver-->
      <a href="listar_GC_CONTRIBUYENTE.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>


      <h5 id="forma-titulo3a" align="center" style="width:95%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-10px;  background-color: #0e315a; color: white; border-radius: 5px;color:#0e315a;">.</h5> 
      <!---->
     <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

     <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/registrarContribuyenteJson.php" >

                  <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

                  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    <div class="form-group">
                    
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="cact" class="control-label col-sm-5" >
                                    <strong class="obligado">*</strong>Código Matricula Actual:
                            </label>
                            <input  type="text" name="codigoActual" required id="cact" class="form-control" maxlength="15" title="Código Actual" onkeypress="return txtValida(event,'num_car')" placeholder="Código Actual " style="height: 30px">
                        </div>

                        <div class="form-group" style="margin-top: -10px;">

                            <label for="cante" class="control-label col-sm-5" >
                                    Código Matricula Anterior:
                            </label>
                            <input  type="text" name="codigoAnterior" id="cante" class="form-control" maxlength="15" title="Código Anterior" onkeypress="return txtValida(event,'num_car')" placeholder="Código Anterior" style="height: 30px">
                        </div>


                        <?php
                        $cuentaI = "SELECT t.id_unico,
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
                                        t.numeroidentificacion
                                FROM gf_tercero t LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero 
                                WHERE pt.perfil = 3 OR pt.perfil = 4  ORDER BY NOMBRETERCERO ASC ";
                        $rsctai = $mysqli->query($cuentaI);
                        ?>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="sltctai" class="control-label col-sm-5 "><strong style="color:#03C1FB;">*</strong>Tercero:</label>
                            <select name="tercero" id="sltctai" required style="height: 30px" class="select2_single form-control" title="Seleccione Tercero">

                                <option value="">Tercero</option>

                                    <?php while($row=mysqli_fetch_array($rsctai)){ ?>
                                                 <option value="<?php echo $row['id_unico']?>"><?php echo $row['numeroidentificacion']." - ".ucwords(mb_strtolower($row['NOMBRETERCERO'] )) ?></option>
                                    <?php } ?>
                            </select>
                        </div><br>


                        <div class="form-group" style="margin-top: -13px;margin-bottom:17.5px">

                            <label for="codp" class="control-label col-sm-5" >
                                    Código Postal:
                            </label>
                            <input type="text" name="codigoPostal" id="codp" class="form-control" maxlength="15" title="Código Postal" onkeypress="return txtValida(event,'num_car')" placeholder="Código Postal" style="height: 30px" >
                        </div>


                        <?php
                        $cuentaI = "SELECT t.id_unico,
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
                                        t.numeroidentificacion
                         FROM gf_tercero t ORDER BY NOMBRETERCERO ASC ";
                        $rsctai = $mysqli->query($cuentaI);
                        ?>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="slaccom" class="control-label col-sm-5 ">Representante Legal:</label>
                            <select name="representanteLegal" id="slaccom"  style="height: 30px" class="select2_single form-control" title="Seleccione Representante Legal">
                                <option value="">Representante Legal</option>
                     
                                    <?php while($row=mysqli_fetch_row($rsctai)){ ?>

                                         <option value="<?php echo $row[0] ?>"><?php echo $row[2]." - ".ucwords(mb_strtolower($row[1] )) ?></option>
                                    
                                    <?php } ?>
                            </select>
                        </div><br>
                        
                        <script type="text/javascript">
                            $(document).ready(function() {
                              $("#datepicker").datepicker();
                            });
                        </script>
                        
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="fechaini" class="control-label col-sm-5 col-md-5 col-lg-5" ><strong style="color:#03C1FB;">*</strong>Fecha Inscripción:</label>
                            <input  type="text" name="fechaini"  id="fechaini" class="form-control" title="Ingrese la Fecha de Inscripción" placeholder="Fecha de Inscripción" style="height: 30px" required >
                        </div><br>
                        
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="txtDirC" class="control-label col-sm-5 col-md-5 col-lg-5" ><strong style="color:#03C1FB;">*</strong>Dirección Correspondencia:</label>
                            <input  type="text" name="txtDirC"  id="txtDirC" class="form-control" title="Ingrese la Dirección" placeholder="Dirección de Correspondencia" style="height: 30px" required >
                        </div><br>
                        <?php 
                            $estado = "SELECT id_unico , nombre FROM gc_estado_contribuyente ";
                            $esta = $mysqli->query($estado);
                        ?>
                    
                        <div class="form-group" style="margin-top: -10px">
                            <label for="sltEst" class="control-label col-sm-5 "><strong style="color:#03C1FB;">*</strong>Estado:</label>
                            <select name="sltEst" id="sltEst"  style="height: 30px" class="select2_single form-control" title="Seleccione Estado" required>
                                <option value="">Estado</option>
                     
                                    <?php while($EST=mysqli_fetch_row($esta)){ ?>

                                         <option value="<?php echo $EST[0] ?>"><?php echo ucwords(mb_strtolower($EST[1] )) ?></option>
                                    
                                    <?php } ?>
                            </select>
                        </div><br>
                        

                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="control-label col-sm-5 "></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Fin del Contenedor principal -->
        <!--Información adicional -->
            <div class="col-sm-3 col-sm-3" style="margin-top:-12px">
                <table class="tablaC table-condensed" >
                    <thead>
                      <tr>
                        <th><h2 class="titulo" align="center" style=" font-size:17px;">Adicional</h2></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                    
                        <td>
                            <a onclick="javascript:abrirMT()" class="btn btn-primary btnInfo">Tercero</a>
                        </td>

                      </tr>
                      <tr>
                
                    
                      </tr>
                      <tr>
                  
                        <td></td>
                      </tr>
                    </tbody>
                </table>                
            </div>
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
    <script>
        function reporteExcel(){
            $('form').attr('action', 'informes/generar_INF_LIS_FAC_EXCEL.php');
        }

        function reportePdf(){
            $('form').attr('action', 'informes/generar_INF_LIS_FAC.php');
        }
    </script>
</div>
<?php require_once 'footer.php' ?>
<script>
    function abrirMT(){
        $("#terCont").modal('show');
    }
</script>
<div class="modal fade" id="terCont" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Perfil Tercero</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="terceroC" id="terceroC" class="form-control" title="Tipo Identificación" required>
                        <option >Perfil Tercero</option>
                        <option value="1">Cliente Juridica</option>
                        <option value="2">Cliente Natural</option>
                                                        
                    </select>
                </div>                           
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="men" class="btn" onclick="return enviar()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>	
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>	
        </div>
    </div>
</div>
<script>
    function enviar(){                
        var form = document.getElementById('terceroC').value;
        if(form == 1){
            window.location='registrar_TERCERO_CLIENTE_JURIDICA.php';
        }else{
            window.location='registrar_TERCERO_CLIENTE_NATURAL.php';
        }
        ///window.location="terceros.php?tercero="+form;
    }
</script>
</body>
</html>