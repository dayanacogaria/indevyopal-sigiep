<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');
//CICLO
$ciclo = "SELECT id_unico, nombre FROM gp_ciclo WHERE estado_facturacion !=1 ORDER BY nombre ASC";
$ciclos = $mysqli->query($ciclo);

//Año
$anno = "SELECT id_unico, anno FROM gf_parametrizacion_anno ORDER BY anno DESC";
$annop= $mysqli->query($anno);

?>
<script src="lib/jquery.js"></script>
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<link href="css/select/select2.min.css" rel="stylesheet">
<style>
label#fecha_inicial-error,#nombre-error, #fecha_final-error, #primera_fecha-error, #segunda_fecha-error, #fecha_cierre-error, #ciclo-error, #anno-error{
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

<title>Registrar Periodo</title>
</head>
<body>
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
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
       
        
        $("#fecha_inicial").datepicker({changeMonth: true,}).val(fecAct);
        $("#fecha_final").datepicker({changeMonth: true}).val(fecAct);
        $("#primera_fecha").datepicker({changeMonth: true}).val(fecAct);
        $("#segunda_fecha").datepicker({changeMonth: true}).val(fecAct);
        $("#fecha_cierre").datepicker({changeMonth: true}).val(fecAct);
        
        
});
</script>

    <script type="text/javascript">
    function cambiarAnno(){
        
        
        $(function(){
        var fecha = new Date();
        var annoA = fecha.getFullYear();
        var valorA = $("#anno option:selected").html();
        if(annoA-valorA){
            var signo="+";
            var aum= valorA-annoA;
        }else {
           var signo="-";  
           var aum= annoA-valorA;
       }
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
            defaultDate: signo+aum+"y",
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha_inicial").datepicker('setDate', null);
        $("#fecha_final").datepicker('setDate', null);
        $("#primera_fecha").datepicker('setDate', null);
        $("#segunda_fecha").datepicker('setDate', null);
        $("#fecha_cierre").datepicker('setDate', null);
        
        var fi = document.getElementById("fecha_inicial");
        fi.disabled=false;
        $("#fecha_inicial").datepicker({changeMonth: true,}).val();
        $("#fecha_final").datepicker({changeMonth: true}).val();
        $("#primera_fecha").datepicker({changeMonth: true}).val();
        $("#segunda_fecha").datepicker({changeMonth: true}).val();
        $("#fecha_cierre").datepicker({changeMonth: true}).val();
        
});
}
 
    </script>

 
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -22px; ">
            <h2 class="tituloform" align="center" >Registrar Periodo</h2>
            <a href="LISTAR_GP_PERIODO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Periodo</h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GP_PERIODOJson.php">
                <p align="center" style="margin-bottom: 20px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control col-sm-1" title="Ingrese nombre" placeholder="Nombre" onkeypress="return txtValida(event, 'num_car')" maxlength="100" required="required">
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="ciclo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ciclo:</label>
                        <select name="ciclo" id="ciclo"  class="select2_single form-control col-sm-1" title="Seleccione Ciclo" required="required">
                            <option value="">Ciclo</option>
                            <?php while($row2 = mysqli_fetch_row($ciclos)){?>
                            <option value="<?php echo $row2[0] ?>"><?php echo ucwords((mb_strtolower($row2[1])));}?></option>;
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="anno" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Año:</label>
                        <select name="anno" id="anno"  class="select2_single form-control col-sm-1" title="Seleccione Año" required="required" onchange="cambiarAnno();">
                            <option value="">Año</option>
                            <?php while($row2 = mysqli_fetch_row($annop)){?>
                            <option value="<?php echo $row2[0] ?>"><?php echo ucwords((mb_strtolower($row2[1])));}?></option>;
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="fecha_inicial" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha Inicial:</label>
                        <input class="form-control col-sm-1" type="text" name="fecha_inicial" id="fecha_inicial" style="width:300px;" title="Ingrese fecha inicial" placeholder="Fecha Inicial" readonly="readonly" required="required" onchange="javaScript:fechaInicial();" disabled="true">
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="fecha_final" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha Final:</label>
                        <input class="form-control col-sm-1" type="text" name="fecha_final" id="fecha_final" style="width:300px;" title="Ingrese fecha final" readonly="readonly" placeholder="Fecha Final" required="required" onchange="javaScript:fechaFinal();" disabled="true">
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="primera_fecha" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Primera Fecha:</label>
                        <input class="form-control col-sm-1" type="text" name="primera_fecha" id="primera_fecha" style="width:300px;" title="Ingrese primera fecha" placeholder="Primera Fecha" readonly="readonly" required="required" onchange="javaScript:fechaPrimera();" disabled="true">
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="segunda_fecha" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Segunda Fecha:</label>
                        <input class="form-control col-sm-1" type="text" name="segunda_fecha" id="segunda_fecha" style="width:300px;" title="Ingrese segunda fecha" placeholder="Segunda Fecha" readonly="readonly" required="required" onchange="javaScript:fechaSegunda();" disabled="true">
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="fecha_cierre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha Cierre:</label>
                        <input class="form-control col-sm-1" type="text" name="fecha_cierre" id="fecha_cierre" style="width:300px;" title="Ingrese fecha cierre" placeholder="Fecha Cierre" readonly="readonly" required="required" onchange="javaScript:fechaCierre();" disabled="true">
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="descripcion" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Descripción:</label>
                        <textarea type="text" name="descripcion" id="descripcion" class="form-control col-sm-1" onkeypress="return txtValida(event,'num_car')" maxlength="500" title="Ingrese descripción"  placeholder="Descripción" style="margin-top:0.1em; height: 65px;" ></textarea>
                    </div>
                     <div class="form-group" style="margin-top: -10px;">
                            <label for="file" class="col-sm-5 control-label" >Subir Imagen Factura:</label>
                            <input type="hidden"  title="Seleccione Documento" id="archivosMod" name="archivosMod" >
                            <input id="file" name="file" type="file" style="display: inline; height: 35px;  width: 300px" >
                            <label id="labelErrorModificar" name="labelErrorModificar" style="display: inline; color: #155180;font-weight: normal; font-style: italic;"></label>
                     </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="no" class="col-sm-5 control-label"></label>
                        <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                    </div>
                    <input type="hidden" name="MM_insert" >
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php';?>
<script src="js/select/select2.full.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
</script>
</body>
<script>
function fechaInicial(){
        var fechain= document.getElementById('fecha_inicial').value;
       var fechafi= document.getElementById('fecha_final').value;
       var fechapr= document.getElementById('primera_fecha').value;
       var fechase= document.getElementById('segunda_fecha').value;
       var fechaci= document.getElementById('fecha_cierre').value;
       var fi = document.getElementById("fecha_final");
        fi.disabled=false;
            $( "#fecha_final" ).datepicker( "destroy" );
            $( "#fecha_final" ).datepicker({ changeMonth: true, minDate: fechain, maxDate:fechapr});
            $( "#fecha_inicial" ).datepicker( "destroy" );
            $( "#fecha_inicial" ).datepicker({ changeMonth: true, maxDate:fechafi});
    
            $( "#primera_fecha" ).datepicker( "destroy" );
            $( "#primera_fecha" ).datepicker({ changeMonth: true, minDate: fechafi, maxDate:fechase});
       
            $( "#segunda_fecha" ).datepicker( "destroy" );
            $( "#segunda_fecha" ).datepicker({ changeMonth: true, minDate: fechapr,maxDate:fechaci});
       
            $( "#fecha_cierre" ).datepicker( "destroy" );
            $( "#fecha_cierre" ).datepicker({ changeMonth: true, minDate: fechase});
        
}
</script>
<script>
function fechaFinal(){
        var fechain= document.getElementById('fecha_inicial').value;
       var fechafi= document.getElementById('fecha_final').value;
       var fechapr= document.getElementById('primera_fecha').value;
       var fechase= document.getElementById('segunda_fecha').value;
       var fechaci= document.getElementById('fecha_cierre').value;
         var fi = document.getElementById("primera_fecha");
        fi.disabled=false; 
            $( "#fecha_final" ).datepicker( "destroy" );
            $( "#fecha_final" ).datepicker({ changeMonth: true, minDate: fechain, maxDate:fechapr});
            $( "#fecha_inicial" ).datepicker( "destroy" );
            $( "#fecha_inicial" ).datepicker({ changeMonth: true, maxDate:fechafi});
    
            $( "#primera_fecha" ).datepicker( "destroy" );
            $( "#primera_fecha" ).datepicker({ changeMonth: true, minDate: fechain, maxDate:fechase});
       
            $( "#segunda_fecha" ).datepicker( "destroy" );
            $( "#segunda_fecha" ).datepicker({ changeMonth: true, minDate: fechain,maxDate:fechaci});
       
            $( "#fecha_cierre" ).datepicker( "destroy" );
            $( "#fecha_cierre" ).datepicker({ changeMonth: true, minDate: fechase});
        
}
</script>
<script>
function fechaPrimera(){
        var fechain= document.getElementById('fecha_inicial').value;
       var fechafi= document.getElementById('fecha_final').value;
       var fechapr= document.getElementById('primera_fecha').value;
       var fechase= document.getElementById('segunda_fecha').value;
       var fechaci= document.getElementById('fecha_cierre').value;
        var fi = document.getElementById("segunda_fecha");
        fi.disabled=false;    
            $( "#fecha_final" ).datepicker( "destroy" );
            $( "#fecha_final" ).datepicker({ changeMonth: true, minDate: fechain, maxDate:fechapr});
            $( "#fecha_inicial" ).datepicker( "destroy" );
            $( "#fecha_inicial" ).datepicker({ changeMonth: true, maxDate:fechafi});
    
            $( "#primera_fecha" ).datepicker( "destroy" );
            $( "#primera_fecha" ).datepicker({ changeMonth: true, minDate: fechafi, maxDate:fechase});
       
            $( "#segunda_fecha" ).datepicker( "destroy" );
            $( "#segunda_fecha" ).datepicker({ changeMonth: true, minDate: fechapr,maxDate:fechaci});
       
            $( "#fecha_cierre" ).datepicker( "destroy" );
            $( "#fecha_cierre" ).datepicker({ changeMonth: true, minDate: fechase});
        
}
</script>
<script>
function fechaSegunda(){
        var fechain= document.getElementById('fecha_inicial').value;
       var fechafi= document.getElementById('fecha_final').value;
       var fechapr= document.getElementById('primera_fecha').value;
       var fechase= document.getElementById('segunda_fecha').value;
       var fechaci= document.getElementById('fecha_cierre').value;
       var fi = document.getElementById("fecha_cierre");
        fi.disabled=false; 
            $( "#fecha_final" ).datepicker( "destroy" );
            $( "#fecha_final" ).datepicker({ changeMonth: true, minDate: fechain, maxDate:fechapr});
            $( "#fecha_inicial" ).datepicker( "destroy" );
            $( "#fecha_inicial" ).datepicker({ changeMonth: true, maxDate:fechafi});
    
            $( "#primera_fecha" ).datepicker( "destroy" );
            $( "#primera_fecha" ).datepicker({ changeMonth: true, minDate: fechafi, maxDate:fechase});
       
            $( "#segunda_fecha" ).datepicker( "destroy" );
            $( "#segunda_fecha" ).datepicker({ changeMonth: true, minDate: fechapr,maxDate:fechaci});
       
            $( "#fecha_cierre" ).datepicker( "destroy" );
            $( "#fecha_cierre" ).datepicker({ changeMonth: true, minDate: fechase});
        
}
</script>
<script>
function fechaCierre(){
        var fechain= document.getElementById('fecha_inicial').value;
       var fechafi= document.getElementById('fecha_final').value;
       var fechapr= document.getElementById('primera_fecha').value;
       var fechase= document.getElementById('segunda_fecha').value;
       var fechaci= document.getElementById('fecha_cierre').value;
          
            $( "#fecha_final" ).datepicker( "destroy" );
            $( "#fecha_final" ).datepicker({ changeMonth: true, minDate: fechain, maxDate:fechapr});
            $( "#fecha_inicial" ).datepicker( "destroy" );
            $( "#fecha_inicial" ).datepicker({ changeMonth: true, maxDate:fechafi});
    
            $( "#primera_fecha" ).datepicker( "destroy" );
            $( "#primera_fecha" ).datepicker({ changeMonth: true, minDate: fechafi, maxDate:fechase});
       
            $( "#segunda_fecha" ).datepicker( "destroy" );
            $( "#segunda_fecha" ).datepicker({ changeMonth: true, minDate: fechapr,maxDate:fechaci});
       
            $( "#fecha_cierre" ).datepicker( "destroy" );
            $( "#fecha_cierre" ).datepicker({ changeMonth: true, minDate: fechase});
        
}
</script>
