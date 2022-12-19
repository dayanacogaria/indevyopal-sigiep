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
            . "p.primera_fecha, p.segunda_fecha, p.fecha_cierre, p.descripcion "
            . "FROM gp_periodo p "
            . "LEFT JOIN gp_ciclo c ON p.ciclo= c.id_unico "
            . "LEFT JOIN gf_parametrizacion_anno pa ON p.anno = pa.id_unico "
            . "WHERE md5(p.id_unico)='$id'";  
  
$resul = $mysqli->query($queryCond);
$row = mysqli_fetch_row($resul);
  
//CICLO
$ciclo = "SELECT id_unico, nombre FROM gp_ciclo WHERE id_unico != $row[1] ORDER BY nombre ASC";
$ciclos = $mysqli->query($ciclo);

//Año

$anno = "SELECT id_unico, anno FROM gf_parametrizacion_anno WHERE id_unico != $row[2] ORDER BY anno ASC";
$annop= $mysqli->query($anno);
}
?>


<title>Modificar Periodo</title>
</head>
<body>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
    
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

 
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="">
        <h2 id="forma-titulo3" align="center" style="margin-bottom: 10px; margin-right: 4px; margin-left: 4px;">Modificar Periodo</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GP_PERIODOJson.php">
                   <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="ciclo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ciclo:</label>
                        <select name="ciclo" id="ciclo"  class="form-control col-sm-1" title="Seleccione Ciclo" required="required">
                            <?php   
                            if (empty($row[1])) {
                                echo '<option value=""></option>';
                                $ciclo = "SELECT id_unico, nombre FROM gp_ciclo ORDER BY nombre ASC";
                                $ciclos = $mysqli->query($ciclo);
                                 while($row1 = mysqli_fetch_row($ciclos)){?>
                                <option value="<?php echo $row1[0] ?>"><?php echo ucwords((strtolower($row1[1])));}?></option>;
                            <?php } else { ?>
                                <option value="<?php echo $row[1] ?>"><?php echo ucwords(strtolower($row[3]));?></option>
                                 <?php while($row1 = mysqli_fetch_row($ciclos)) { ?>
                                <option value="<?php echo $row1[0] ?>"> <?php echo ucwords((strtolower($row1[1])));}?></option>;
                            <?php } ?>
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="anno" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Año:</label>
                        <select name="anno" id="anno"  class="form-control col-sm-1" title="Seleccione Año" required="required">
                            <<?php   
                            if (empty($row[2])) {
                                echo '<option value=""></option>';
                                $anno = "SELECT id_unico, anno FROM gf_parametrizacion_anno ORDER BY anno ASC";
                                $annop= $mysqli->query($anno);
                                 while($row2 = mysqli_fetch_row($annop)){?>
                                <option value="<?php echo $row2[0] ?>"><?php echo ucwords((strtolower($row2[1])));}?></option>;
                            <?php } else {   ?>
                                <option value="<?php echo $row[2] ?>"><?php echo ucwords(strtolower($row[4]));?></option>
                                 <?php while($row2 = mysqli_fetch_row($annop)) { ?>
                                <option value="<?php echo $row2[0] ?>"> <?php echo ucwords((strtolower($row2[1])));}?></option>;
                            <?php } ?>
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="fecha_inicial" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha Inicial:</label>
                        <input class="form-control col-sm-1" type="text" name="fecha_inicial" id="fecha_inicial" style="width:300px;" title="Ingrese fecha inicial" onchange="javaScript:fechaInicial();" placeholder="Fecha Inicial"  required="required" >
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="fecha_final" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha Final:</label>
                        <input class="form-control col-sm-1" type="text" name="fecha_final" id="fecha_final" style="width:300px;" title="Ingrese fecha final" onchange="javaScript:fechaFinal();"placeholder="Fecha Final" required="required" >
                    </div>
                    
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="primera_fecha" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Primera Fecha:</label>
                        <input class="form-control col-sm-1" type="text" name="primera_fecha" id="primera_fecha" style="width:300px;" title="Ingrese primera fecha" onchange="javaScript:fechaPrimera();"placeholder="Primera Fecha"  required="required" >
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="segunda_fecha" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Segunda Fecha:</label>
                        <input class="form-control col-sm-1" type="text" name="segunda_fecha" id="segunda_fecha" style="width:300px;" title="Ingrese segunda fecha" onchange="javaScript:fechaSegunda();"placeholder="Segunda Fecha"  required="required" >
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="fecha_cierre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha Cierre:</label>
                        <input class="form-control col-sm-1" type="text" name="fecha_cierre" id="fecha_cierre" style="width:300px;" title="Ingrese fecha cierre" placeholder="Fecha Cierre" readonly="readonly" required="required" onchange="javaScript:fechaCierre();">
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="descripcion" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Descripción</label>
                        <input type="text" name="descripcion" id="descripcion" class="form-control col-sm-1"  onkeypress="return txtValida(event,'num_car')" maxlength="500" title="Ingrese descripción" placeholder="Descripción" value="<?php  echo ucwords(strtolower($row[10]))?>">
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
</body>
<script>
function modificacionInicial(){
        var fechain= document.getElementById('fecha_inicial').value;
       var fechafi= document.getElementById('fecha_final').value;
       var fechapr= document.getElementById('primera_fecha').value;
       var fechase= document.getElementById('segunda_fecha').value;
       var fechaci= document.getElementById('fecha_cierre').value;
       var fechai= Date.parse(fechain);
       var fechaf= Date.parse(fechafi);
       var fechap= Date.parse(fechapr);
       var fechas= Date.parse(fechase);
       var fechac= Date.parse(fechaci);

       if (fechai > fechaf) {
            document.getElementById('fecha_final').value=fechain;
            $( "#fecha_final" ).datepicker( "destroy" );
            $( "#fecha_final" ).datepicker({ changeMonth: true, minDate: fechain, maxDate:fechapr});
             $( "#fecha_inicial" ).datepicker( "destroy" );
            $( "#fecha_inicial" ).datepicker({ changeMonth: true, maxDate:fechafi});

        } else {
            $( "#fecha_final" ).datepicker( "destroy" );
            $( "#fecha_final" ).datepicker({ changeMonth: true, minDate: fechain, maxDate:fechapr});
            $( "#fecha_inicial" ).datepicker( "destroy" );
            $( "#fecha_inicial" ).datepicker({ changeMonth: true, maxDate:fechafi});
        }
        if(fechaf > fechap){
            document.getElementById('primera_fecha').value=fechafi;
            $( "#primera_fecha" ).datepicker( "destroy" );
            $( "#primera_fecha" ).datepicker({ changeMonth: true, minDate: fechafi, maxDate:fechase});
            $( "#fecha_final" ).datepicker( "destroy" );
            $( "#fecha_final" ).datepicker({ changeMonth: true,minDate:fechain, maxDate:fechapr});

        } else {
            $( "#primera_fecha" ).datepicker( "destroy" );
            $( "#primera_fecha" ).datepicker({ changeMonth: true, minDate: fechafi, maxDate:fechase});
        }
        if(fechap > fechas){
            document.getElementById('segunda_fecha').value=fechapr;
            $( "#segunda_fecha" ).datepicker( "destroy" );
            $( "#segunda_fecha" ).datepicker({ changeMonth: true, minDate: fechapr});
            $( "#primera_fecha" ).datepicker({ changeMonth: true,minDate:fechafi, maxDate:fechase});

        } else {
            $( "#segunda_fecha" ).datepicker( "destroy" );
            $( "#segunda_fecha" ).datepicker({ changeMonth: true, minDate: fechapr,maxDate:fechaci});
        }
        if(fechas > fechac){
            document.getElementById('fecha_cierre').value=fechase;
            $( "#fecha_cierre" ).datepicker( "destroy" );
            $( "#fecha_cierre" ).datepicker({ changeMonth: true, minDate: fechase});
            $( "#segunda_fecha" ).datepicker({ changeMonth: true,minDate:fechapr, maxDate:fechaci});

        } else {
            $( "#fecha_cierre" ).datepicker( "destroy" );
            $( "#fecha_cierre" ).datepicker({ changeMonth: true, minDate: fechase});
        }
}
</script>
<script>
function fechaInicial(){
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
function fechaFinal(){
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
function fechaPrimera(){
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
function fechaSegunda(){
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


