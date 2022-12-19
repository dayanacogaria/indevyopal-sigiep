<?php
################ MODIFICACIONES ####################
#25/05/2017 | Anderson Alarcon | Cambie la estructura del formulario
############################################

require_once('Conexion/conexion.php');
require_once 'head.php'; ?>
<title>Listado de Facturación</title>
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltTci-error, #sltTcf-error, #fechaini-error, #fechafin-error, #selectFacturaInicial-error, #selectFacturaFinal-error  {
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
<style>
    
    #divFacturaInicial,#divFacturaFinal,#divFechaInicial,#divFechaFinal{
        
        display:none;
        
    }
    
</style>

<script>

    function showFieldsFacturaInicialFinal(){
    
         var ti = document.getElementById("selectTipoInforme").value;
         
         if(ti==="general"){
             
            
                document.getElementById("divFacturaInicial").style.display='none';
                document.getElementById("divFacturaFinal").style.display='none';
                document.getElementById("divFechaInicial").style.display='block';
                document.getElementById("divFechaFinal").style.display='block';
             
                //desbloqueo botones pdf y excel
                $("#btnpdf,#btnexcel").attr('disabled',false);
                $('#selectFacturaInicial').removeAttr("required");
                $('#selectFacturaFinal').removeAttr("required");
                
         }else{
             
             if(ti==="detallado"){
                //tipo informe, detallado

                  //mostrar los div de factura Inicial y final

                  document.getElementById("divFacturaInicial").style.display='block';
                  document.getElementById("divFacturaFinal").style.display='block';
                  document.getElementById("divFechaInicial").style.display='block';
                  document.getElementById("divFechaFinal").style.display='block';
                  //agregar validaciones a los campos 
                  $('#selectFacturaInicial').prop("required", true);  
                  $('#selectFacturaFinal').prop("required", true);  
                  
                  //desbloqueo botones pdf y excel
                  $("#btnpdf,#btnexcel").attr('disabled',false);                  

             }else{
                 //no es ninguno tipo informe
                    document.getElementById("divFacturaInicial").style.display='none';
                    document.getElementById("divFacturaFinal").style.display='none';
                    document.getElementById("divFechaInicial").style.display='none';
                    document.getElementById("divFechaFinal").style.display='none';                  
                    //bloqueo botones pdf y excel
                    $("#btnpdf,#btnexcel").attr('disabled',true);
             }

         }
       
    
    }



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
            <h2 align="center" class="tituloform">Listado de Facturación Recaudo</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    <div class="form-group">

                        
                        <!--TIPO DE INFORME-->
                        <?php
                        $cuentaI = "SELECT id_unico,CONCAT(codi_cuenta,' - ',nombre) from gf_cuenta ORDER BY codi_cuenta ASC";
                        $rsctai = $mysqli->query($cuentaI);
                        ?>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="sltctai" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Informe</label>
                            <select name="tipoInforme" id="selectTipoInforme" onchange="showFieldsFacturaInicialFinal()" required style="height: auto" class="select2_single form-control" title="Seleccione Tipo Informe">
                                <option value="">Tipo Informe</option>
                                <option value="general">General</option>
                                <option value="detallado">Detallado</option>

                            </select>
                        </div>
                        
                        
                        <!--FECHA INICIAL-->
                        <div id="divFechaInicial"  class="form-group" style="margin-top: -5px;">
                            <label for="fechaini" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <input class="form-control" type="text" name="fechaInicial" id="fechaini"  value="<?php echo date("Y-m-d");?>" required>
                        </div>

                        <!--FECHA FINAL-->
                        <div id="divFechaFinal" class="form-group" style="margin-top: -10px;">
                            <label for="fechafin" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                            <input class="form-control" type="text" name="fechaFinal" id="fechafin"  value="<?php echo date("Y-m-d");?>" required>
                        </div>




             

 

                            <!--FACTURAS-->


                            <!--factura Inicial-->
                            <div id="divFacturaInicial" class="form-group" style="margin-top: -5px">   


                                <label for="selectFacturaInicial" class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Factura Inicial:
                                </label>


                                <select name="facturaInicial" id="selectFacturaInicial"  title="Seleccione Factura Inicial" class="select2_single form-control" style="height: 30px"  >
                                <option value="">Seleccione Factura Inicial</option>                                
                                <?php   
                                $sqlB = "SELECT     fat.id_unico,   
                                                    fat.numero_factura,
                                                    tpf.prefijo,
                                                    IF(CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' OR CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,(ter.razonsocial),                                            CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE', 
                                                    CONCAT(' - ',ter.numeroidentificacion) AS 'TipoD', 
                                                    DATE_FORMAT(fat.fecha_factura, '%d/%m/%Y')
                                        FROM        gp_factura fat
                                        LEFT JOIN   gp_tipo_factura tpf ON tpf.id_unico = fat.tipofactura
                                        LEFT JOIN   gf_tercero ter ON ter.id_unico = fat.tercero
                                        LEFT JOIN   gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                        ORDER BY fat.id_unico ASC";
                                $resultB = $mysqli->query($sqlB);
                                while ($rowB = mysqli_fetch_row($resultB)) {
                                    $sqlDF = "SELECT SUM(IF(dtf.valor<0,dtf.valor*-1,dtf.valor)) FROM gp_detalle_factura dtf WHERE factura = $rowB[0]";
                                    $resultDF = $mysqli->query($sqlDF);
                                    $valDF = mysqli_fetch_row($resultDF);
                                    echo "<option value=".$rowB[0].">".$rowB[1].' '.$rowB[2].' '.$rowB[5].' '.ucwords(mb_strtolower($rowB[3])).' '.($rowB[4]).' '."$".number_format($valDF[0],2,'.',',')."</option>";
                                }
                                 ?>
                                 </select>
                            </div>
                            
                            
                            <!--factura Final-->
                            <div id="divFacturaFinal" class="form-group" style="margin-top: -5px">   


                                <label for="selectFacturaFinal" class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Factura Final:
                                </label>


                                <select name="facturaFinal" id="selectFacturaFinal"  title="Seleccione Factura Final" class="select2_single form-control" style="height: 30px"  >
                                <option value="">Seleccione Factura Final</option>                                
                                <?php   
                                $sqlB = "SELECT     fat.id_unico,   
                                                    fat.numero_factura,
                                                    tpf.prefijo,
                                                    IF(CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' OR CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,(ter.razonsocial),                                            CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE', 
                                                    CONCAT(' - ',ter.numeroidentificacion) AS 'TipoD', 
                                                    DATE_FORMAT(fat.fecha_factura, '%d/%m/%Y')
                                        FROM        gp_factura fat
                                        LEFT JOIN   gp_tipo_factura tpf ON tpf.id_unico = fat.tipofactura
                                        LEFT JOIN   gf_tercero ter ON ter.id_unico = fat.tercero
                                        LEFT JOIN   gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                        ORDER BY fat.id_unico DESC";
                                $resultB = $mysqli->query($sqlB);
                                while ($rowB = mysqli_fetch_row($resultB)) {
                                    $sqlDF = "SELECT SUM(IF(dtf.valor<0,dtf.valor*-1,dtf.valor)) FROM gp_detalle_factura dtf WHERE factura = $rowB[0]";
                                    $resultDF = $mysqli->query($sqlDF);
                                    $valDF = mysqli_fetch_row($resultDF);
                                    echo "<option value=".$rowB[0].">".$rowB[1].' '.$rowB[2].' '.$rowB[5].' '.ucwords(mb_strtolower($rowB[3])).' '.($rowB[4]).' '."$".number_format($valDF[0],2,'.',',')."</option>";
                                }
                                 ?>
                                 </select>
                            </div>











                        <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >

                            <button id="btnpdf" onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>

                            <button id="btnexcel" style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                                 
                                <script type="text/javascript">
                                    /*Recarga la pagina, bloqueo botones por tipo de informe ninguno*/
                                        $("#btnpdf,#btnexcel").attr('disabled',true);
                               
                                </script> 
                        
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
    <script>
        function reporteExcel(){
            $('form').attr('action', 'informes/generar_INF_LIS_FAC_REC_EXCEL.php');
        }

        function reportePdf(){
            $('form').attr('action', 'informes/generar_INF_LIS_FAC_REC.php');
        }
    </script>
</div>
<?php require_once 'footer.php' ?>
</body>
</html>