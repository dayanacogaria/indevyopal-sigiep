<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');
require_once 'head.php';
require_once('Conexion/conexion.php');
$id = " ";
$queryCond="";
if (isset($_GET["id"])){ 
  $id = (($_GET["id"]));
 $queryCond = "SELECT p.id_unico, p.ciclo, p.anno, c.nombre, pa.anno, p.fecha_inicial, p.fecha_final, "
            . "p.primera_fecha, p.segunda_fecha, p.fecha_cierre, p.descripcion, p.nombre, p.imagen "
            . "FROM gp_periodo p "
            . "LEFT JOIN gp_ciclo c ON p.ciclo= c.id_unico "
            . "LEFT JOIN gf_parametrizacion_anno pa ON p.anno = pa.id_unico "
            . "WHERE md5(p.id_unico)='$id'";  
  
$resul = $mysqli->query($queryCond);
$row = mysqli_fetch_row($resul);

$ruta = explode("/", $row[12]);
$imagen = $ruta[2];
  
//CICLO
$ciclo = "SELECT id_unico, nombre FROM gp_ciclo WHERE id_unico != $row[1] AND estado_facturacion !=1   ORDER BY nombre ASC";
$ciclos = $mysqli->query($ciclo);

//Año

$anno = "SELECT id_unico, anno FROM gf_parametrizacion_anno WHERE id_unico != $row[2] ORDER BY anno ASC";
$annop= $mysqli->query($anno);
}
?>


<title>Modificar Periodo</title>
</head>
<body>
<script src="lib/jquery.js"></script>
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<link href="css/select/select2.min.css" rel="stylesheet">
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
    rules:{
        Ciudad:"required",
        barrio:"required",
    }
            
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<style>
label#fecha_inicial-error, #nombre-error, #fecha_final-error, #primera_fecha-error, #segunda_fecha-error, #fecha_cierre-error, #ciclo-error, #anno-error{
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;

}
</style>
    
    <script type="text/javascript">
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
        var fechaI = '<?php echo date("d/m/Y", strtotime($row[5]));?>';
        $("#fecha_inicial").datepicker({changeMonth: true}).val(fechaI);
        
         var fechaF = '<?php echo date("d/m/Y", strtotime($row[6]));?>';
        $("#fecha_final").datepicker({changeMonth: true}).val(fechaF);
        
        var primeraF = '<?php echo date("d/m/Y", strtotime($row[7]));?>';
        $("#primera_fecha").datepicker({changeMonth: true}).val(primeraF);
        
        var segundaF = '<?php echo date("d/m/Y", strtotime($row[8]));?>';
        $("#segunda_fecha").datepicker({changeMonth: true}).val(segundaF);

        var fechaC = '<?php echo date("d/m/Y", strtotime($row[9]));?>';
        $("#fecha_cierre").datepicker({changeMonth: true}).val(fechaC);
         modificacionInicial();
        
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
        var ff = document.getElementById("fecha_final");
        ff.disabled=true;
        var fp = document.getElementById("primera_fecha");
        fp.disabled=true;
        var sf = document.getElementById("segunda_fecha");
        sf.disabled=true;
        var fc = document.getElementById("fecha_cierre");
        fc.disabled=true;
        $( "#fecha_inicial" ).datepicker( "destroy" );
        $( "#fecha_final" ).datepicker( "destroy" );
        $( "#primera_fecha" ).datepicker( "destroy" );
        $( "#segunda_fecha" ).datepicker( "destroy" );
        $( "#fecha_cierre" ).datepicker( "destroy" );
        
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
            <h2 class="tituloform" align="center" >Modificar Periodo</h2>
            <a href="LISTAR_GP_PERIODO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords(mb_strtolower($row[11]))?></h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GP_PERIODOJson.php">
                <p align="center" style="margin-bottom: 20px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                   <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control col-sm-1" title="Ingrese nombre" placeholder="Nombre" onkeypress="return txtValida(event, 'num_car')" maxlength="100" required="required" value="<?php echo ucwords(mb_strtolower($row[11]))?>">
                    </div> 
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="ciclo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ciclo:</label>
                        <select name="ciclo" id="ciclo"  class="select2_single form-control col-sm-1" title="Seleccione Ciclo" required="required">
                            <?php   
                            if (empty($row[1])) {
                                echo '<option value=""> - </option>';
                                $ciclo = "SELECT id_unico, nombre FROM gp_ciclo WHERE estado_facturacion !=1 ORDER BY nombre ASC";
                                $ciclos = $mysqli->query($ciclo);
                                 while($row1 = mysqli_fetch_row($ciclos)){?>
                                <option value="<?php echo $row1[0] ?>"><?php echo ucwords((mb_strtolower($row1[1])));}?></option>;
                            <?php } else { ?>
                                <option value="<?php echo $row[1] ?>"><?php echo ucwords(mb_strtolower($row[3]));?></option>
                                 <?php while($row1 = mysqli_fetch_row($ciclos)) { ?>
                                <option value="<?php echo $row1[0] ?>"> <?php echo ucwords((mb_strtolower($row1[1])));}?></option>;
                            <?php } ?>
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="anno" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Año:</label>
                        <select name="anno" id="anno"  class="select2_single form-control col-sm-1" title="Seleccione Año" required="required" onchange="javaScript:cambiarAnno()">
                            <<?php   
                            if (empty($row[2])) {
                                echo '<option value=""></option>';
                                $anno = "SELECT id_unico, anno FROM gf_parametrizacion_anno ORDER BY anno ASC";
                                $annop= $mysqli->query($anno);
                                 while($row2 = mysqli_fetch_row($annop)){?>
                                <option value="<?php echo $row2[0] ?>"><?php echo ucwords((mb_strtolower($row2[1])));}?></option>;
                            <?php } else {   ?>
                                <option value="<?php echo $row[2] ?>"><?php echo ucwords(mb_strtolower($row[4]));?></option>
                                 <?php while($row2 = mysqli_fetch_row($annop)) { ?>
                                <option value="<?php echo $row2[0] ?>"> <?php echo ucwords((mb_strtolower($row2[1])));}?></option>;
                            <?php } ?>
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="fecha_inicial" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha Inicial:</label>
                        <input class="form-control col-sm-1" type="text" name="fecha_inicial" id="fecha_inicial" style="width:300px;" title="Ingrese fecha inicial" placeholder="Fecha Inicial" readonly="readonly" required="required" onchange="javaScript:fechaInicial();" >
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="fecha_final" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha Final:</label>
                        <input class="form-control col-sm-1" type="text" name="fecha_final" id="fecha_final" style="width:300px;" title="Ingrese fecha final" readonly="readonly" placeholder="Fecha Final" required="required" onchange="javaScript:fechaFinal();" >
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="primera_fecha" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Primera Fecha:</label>
                        <input class="form-control col-sm-1" type="text" name="primera_fecha" id="primera_fecha" style="width:300px;" title="Ingrese primera fecha" placeholder="Primera Fecha" readonly="readonly" required="required" onchange="javaScript:fechaPrimera();" >
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="segunda_fecha" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Segunda Fecha:</label>
                        <input class="form-control col-sm-1" type="text" name="segunda_fecha" id="segunda_fecha" style="width:300px;" title="Ingrese segunda fecha" placeholder="Segunda Fecha" readonly="readonly" required="required" onchange="javaScript:fechaSegunda();" >
                    </div>
                 
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="fecha_cierre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha Cierre:</label>
                        <input class="form-control col-sm-1" type="text" name="fecha_cierre" id="fecha_cierre" style="width:300px;" title="Ingrese fecha cierre" placeholder="Fecha Cierre" readonly="readonly" required="required" onchange="javaScript:fechaCierre();">
                    </div>
              
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="descripcion" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Descripción:</label>
                        <textarea type="text" name="descripcion" id="descripcion" class="form-control col-sm-1"  onkeypress="return txtValida(event,'num_car')" maxlength="500" title="Ingrese descripción" placeholder="Descripción"  style="margin-top:0.1em; height: 65px;" ><?php  echo ucwords(mb_strtolower($row[10]))?></textarea>
                    </div>
                   <div class="form-group" style="margin-top: -20px;">
                        <label for="fecha_cierre" class="col-sm-5 control-label">Achivo Imagen:</label>
                        <input  class="form-control col-sm-1" type="text" name="imagen" id="imagen" style="width:300px;background: lightgrey;"  value="<?php  echo ucwords(mb_strtolower($imagen))?>" disabled>

                        <?php   
                         if (!empty($row[12])) { ?> 
                            <a style="padding-left: 5px;" href="<?php echo $row[12];?>" target="_blank" ><i title="Ver Imagen" class="glyphicon glyphicon-search" style="font-size: 2em;padding-top: 10px;"  ></i></a>                          
                         <?php } ?>
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
  function archivoModificar(){
       var formData = new FormData($("#form")[0]);  
        $.ajax({
            type: 'POST',
            url: "consultasBasicas/validacionDocumentos.php",
            data:formData,
            contentType: false,
             processData: false,
            success: function (data) { 
                resultado = JSON.parse(data);
                var mensaje = resultado["mensaje"];
                var valor = resultado["valor"];
                document.getElementById('labelErrorModificar').innerHTML = mensaje;
                console.log(resultado);
                
                if(valor==1){
                    document.getElementById('archivosMod').value='1';
                } else {
                    document.getElementById('archivosMod').value='2';
                }
            }
        });
}          
</script>
<script>
function modificacionInicial(){
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
            $( "#primera_fecha" ).datepicker({ changeMonth: true, minDate: fechafi, maxDate:fechase});
       
            $( "#segunda_fecha" ).datepicker( "destroy" );
            $( "#segunda_fecha" ).datepicker({ changeMonth: true, minDate: fechapr,maxDate:fechaci});
       
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
</script>
