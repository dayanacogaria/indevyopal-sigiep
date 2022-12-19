<?php 
##################################################################################################
#********************************** Modificaciones ¨*********************************************#
##################################################################################################
#23/01/2019 | Creación
##################################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('Conexion/conexionsql.php');
require_once 'head.php'; 
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$con        = new ConexionPDO();
?>
<title>Proyección Mensual por Tipo Crédito</title> 
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label  #fecha-error, #tipoF-error,  #terceroI-error,  #terceroF-error  {
        display: block;
        color: #bd081c;
        font-weight: bold;
        font-style: italic;
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
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<script>
        $(function(){
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
        $("#fecha").datepicker({changeMonth: true,}).val();
        
        
});
</script>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">INFORME RECAUDO Y PROYECCIÓN MENSUAL</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data"  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: -10px">
                            <label for="mesI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Mes Inicio:</label>
                            <select name="mesI" id="mesI"  style="height: auto" class="select2_single form-control" title="Seleccione Mes Inicial" required="required">
                                <option value="">Mes Inicio:</option>                                
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option> 
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option> 
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option> 
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>                               
                            </select>
                        </div>  
                          <div class="form-group" style="margin-top: -5px">
                            <label for="mesF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Mes Final:</label>
                            <select name="mesF" id="mesF"  style="height: auto" class="select2_single form-control" title="Seleccione Mes Final" required="required">
                                <option value="">Mes Final:</option>                                                            
                                <option value="12">Diciembre</option>
                                <option value="11">Noviembre</option>
                                <option value="10">Octubre</option> 
                                <option value="9">Septiembre</option>
                                <option value="8">Agosto</option>
                                <option value="7">Julio</option> 
                                <option value="6">Junio</option>
                                <option value="5">Mayo</option>
                                <option value="4">Abril</option> 
                                <option value="3">Marzo</option>
                                <option value="2">Febrero</option>
                                <option value="1">Enero</option>                                    
                            </select>
                        </div> 
                        <?php
                            $annio = "SELECT id_unico,anno FROM gf_parametrizacion_anno ORDER BY anno DESC";
                            $rsannio = $mysqli->query($annio);
                            ?> 
                        <div class="form-group" style="margin-top: -5px">
                            <label for="anioI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Año Inicial:</label>
                            <select name="anioI" id="anioI"  style="height: auto" class="select2_single form-control" title="Seleccione Año Inicial" required="required">
                                <option value="">Año Inicial</option>
                                <?php 
                                $sql = "SELECT DISTINCT year(d.Fecha_Posible_pago) as  yr 
                                        FROM DETALLE_CREDITO as d 
                                        ORDER BY year(d.Fecha_Posible_pago) ASC";
                                $stmt = sqlsrv_query( $conn, $sql ); 
                                while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
                                      echo '<option value="'.$row['yr'].'">'.utf8_decode($row['yr']).'</option>';
                                 }                                                                    
                                ?>              
                            </select>
                        </div>  
                        <div class="form-group" style="margin-top: -5px">
                            <label for="anioF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Año Final:</label>
                            <select name="anioF" id="anioF"  style="height: auto" class="select2_single form-control" title="Seleccione Año Final" required="required">
                                <option value="">Año Final</option>
                                <?php 
                                 $sql = "SELECT DISTINCT year(d.Fecha_Posible_pago) as  yr 
                                        FROM DETALLE_CREDITO as d 
                                        ORDER BY year(d.Fecha_Posible_pago) DESC";
                                $stmt = sqlsrv_query( $conn, $sql ); 
                                while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
                                      echo '<option value="'.$row['yr'].'">'.utf8_decode($row['yr']).'</option>';
                                 }                                       
                                ?>       
                            </select>
                        </div>  

                          <div id="creedito" class="form-group" style="margin-top: -5px">
                            <label for="tipoCredito" class="col-sm-5 control-label">Tipo Crédito:</label>
                            <select name="tipoCredito" id="tipoCredito"  style="height: auto" class="select2_single form-control"  >
                                <option value="">Tipo Crédito</option>
                                 <?php
                                        $sql = " SELECT Identificador, Nombre_Tipo_credito
                                                 FROM TIPO_CREDITO
                                                 WHERE Nombre_Tipo_credito not like '%NO APLICA%' 
                                                 ORDER BY Nombre_Tipo_credito";
                                        $stmt = sqlsrv_query( $conn, $sql );  
                                        $n=0;
                                        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
                                            echo '<option value="'.$row['Identificador'].'">'.utf8_decode($row['Nombre_Tipo_credito']).'</option>';
                                        }
                                        
                              ?>                               
                                                               
                            </select>
                        </div>  
                           <div class="form-group" style="margin-top: -5px">
                            <label for="tipoInforme" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Informe:</label>
                            <select name="tipoInforme" id="tipoInforme"  style="height: auto" class="select2_single form-control" title="Seleccione el Informe"  required="required">
                                <option value="">Informe</option>
                                <option value="1">Proyección Mensual por Tipo Crédito</option>
                                <option value="2">Consolidado Proyección Mensual</option>
                               
                                                               
                            </select>
                        </div>  
                      <!--  <div class="form-group" style="margin-top: -5px;">
                             <label for="num_fac"class="col-sm-5 control-label"><strong class="obligado"></strong>Mínimo de facturas con deuda:</label>
                             <input class="form-control" title="" type="text" name="num_fac" id="num_fac" placeholder="Mínimo de facturas con deuda">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                             <label for="acumulado"class="col-sm-5 control-label"><strong class="obligado"></strong>Acumulado Por Tercero:</label>
                             <input class="form-control" title="" type="checkbox" name="acumulado" id="acumulado" placeholder="Detallado" style="margin-left: -70px;width: 20px;margin-top: 0px;">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                             <label for="acumuladov"class="col-sm-5 control-label"><strong class="obligado"></strong>Acumulado Por Vivienda:</label>
                             <input class="form-control" title="" type="checkbox" name="acumuladov" id="acumuladov" placeholder="Acumulado Por Vivienda" style="margin-left: -70px;width: 20px;margin-top: 0px;">
                        </div>-->
                        <div class="col-sm-10" style="margin-top:10px;margin-left:500px" >
                            <button id="btnpdf" onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>
                            <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>

                            
                        </div>
                    </div>
                </form>
            </div>
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
    <?php require_once 'footer.php'?>  
    <script>
        
    </script>
    <script>

     function reporteExcel(){
        $('form').attr('action', 'informes/Inf_Proyeccion_Mensual_Tipo_Excel.php');
    }

    function reportePdf(){
        $('form').attr('action', 'informes/Inf_Proyeccion_Mensual_Tipo.php');
    }




          $("#tipoInforme").change(function(){

            var tipoinform =  $("#tipoInforme").val();
            if(tipoinform == 2) {                           
                $("#creedito").hide();
            }else{            
                $("#creedito").show();
            }
            var tipocred =  $("#tipoCredito").val();               
           
            
          });
          
    </script>
</div>
</body>
</html>