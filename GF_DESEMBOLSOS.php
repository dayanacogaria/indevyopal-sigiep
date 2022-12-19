<?php 
##############################################################################################################################
#                                                                                                                                 MODIFICACIONES
##############################################################################################################################                                                                                                           
#28/10/2017 |Erica G. |Archivo Creado
##############################################################################################################################
require_once('Conexion/conexion.php');
require_once 'head_listar.php'; 
$anno     = $_SESSION['anno'];
$compania =$_SESSION['compania'];

?>
<title>Interfaz Desembolsos</title>

<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="js/md5.pack.js"></script>
<script type="text/javascript">

$(document).ready(function()
{
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
    changeYear:true
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
    $("#fechaDis").datepicker({changeMonth: true}).val();   
    $("#fechaReg").datepicker({changeMonth: true}).val();         
    $("#fechaCxp").datepicker({changeMonth: true}).val();         
    
    });
</script>

<style type="text/css">
    .area
    { 
    height: auto !important;  
    }  

    table.dataTable thead th,table.dataTable thead td
    {
    padding: 1px 18px;
    font-size: 10px;
    }

    table.dataTable tbody td,table.dataTable tbody td
    {
    padding: 1px;
    }
    .dataTables_wrapper .ui-toolbar
    {
    padding: 2px;
    font-size: 10px;
    }

    .control-label
    {
    font-size: 12px;
    }

    .itemListado
    {
    margin-left:5px;
    margin-top:5px;
    width:150px;
    cursor:pointer;
    }

    #listado 
    {
    width:150px;
    height:80px;
    overflow: auto;
    background-color: white;
    }
</style>
</head>
<body>
    <div class="container-fluid text-center"  >
        <div class="row content">
            <?php require_once 'menu.php'; ?> 
            <!-- Localización de los botones de información a la derecha. -->
            <div class="col-sm-10" style="margin-left: -10px;margin-top: 5px" >
                <h2 align="center" class="tituloform col-sm-12" style="margin-top: -5px; margin-bottom: 2px;" >Interfaz Desembolsos</h2>
                <div class="col-sm-12">
                    <div class="client-form contenedorForma"  style=""> 
                        <!------Validación Búsqueda---->
                       <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px;"> 
                            <div class="col-sm-4" align="left">  
                                <label for="buscar" class="control-label" ><strong style="color:#03C1FB;"></strong>Buscar:</label><br>
                                <select name="buscar" id="buscar" class="select2_single form-control input-sm" title="Seleccione" style="width:350px; ">
                                      <option value="">Buscar</option>
                                        <?php 
                                        $dese = "SELECT d.id_unico, d.credito, DATE_FORMAT(d.fecha,'%d/%m/%Y'),  
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
                                                    t.apellidodos)) AS NOMBRE
                                               FROM gf_desembolsos d 
                                               LEFT JOIN gf_tercero t ON d.tercero = t.id_unico 
                                               LEFT JOIN gf_comprobante_pptal cp ON d.numero_cdp = cp.id_unico 
                                               WHERE d.numero_cdp IS NOT NULL 
                                               AND d.numero_registro IS NOT NULL 
                                               AND d.obligacion IS NOT NULL 
                                               AND cp.parametrizacionanno = $anno 
                                               ORDER BY d.credito DESC ";
                                        $dese = $mysqli->query($dese);
                                        while ($rowD = mysqli_fetch_row($dese)) { ?>
                                      <option value="<?php echo $rowD[0]?>"><?php echo ucwords($rowD[1]).' - '.$rowD[2].' - ',$rowD[3];?></option>
                                        <?php }?> 
                                </select>
                            </div>
                           <!-------------*****ACTION BUSCAR DESEMBOLSOS******-------------------->
                           <script>
                               $("#buscar").change(function(){
                                  document.location = ('GF_DESEMBOLSOS.php?id='+md5($("#buscar").val()));  
                               })
                           </script>
                        </div>
                        
                        <?php if(empty($_GET['id']) ) {  
                            ?>
                        
                        <!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                        <!--*Formulario para cuando no hay registro de periodo y grupo de Gestión*-->
                        <!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="">
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <?php if(empty($_GET['des'])){ ?>
                                <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px;"> 
                                    <div class="col-sm-4" align="left">  
                                        <label for="fecha" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                        <select name="fecha" id="fecha" class="select2_single form-control input-sm" title="Seleccione una Fecha" style="width:250px; " required>
                                              <option value="">Fecha</option>
                                                <?php 
                                                $fech = "SELECT id_unico, DATE_FORMAT(fecha,'%d/%m/%Y') , fecha 
                                                    FROM gf_desembolsos 
                                                    WHERE numero_cdp IS  NULL 
                                                   AND numero_registro IS  NULL 
                                                   AND obligacion IS  NULL  ORDER BY fecha DESC";
                                                $fech = $mysqli->query($fech);
                                                while ($rowF = mysqli_fetch_row($fech)) { ?>
                                              <option value="<?php echo $rowF[2]?>"><?php echo ($rowF[1]);?></option>
                                                <?php }?>
                                        </select>
                                        <script>
                                            $("#fecha").change(function(){
                                               var option = '<option>Crédito</option>'; 
                                               var form_data ={action:1, fecha: $("#fecha").val() };

                                               $.ajax({
                                                   type:"POST",
                                                   url:"jsonPptal/gf_interfaz_desembolsosJson.php",
                                                   data:form_data,
                                                   success: function (data) {
                                                       option +=data;
                                                       $("#credito").html(option);
                                                   }
                                               });
                                            });
                                            </script>

                                    </div>
                                     <div class="col-sm-4" align="left">  
                                        <label for="credito" class="control-label" ><strong style="color:#03C1FB;">*</strong>Crédito:</label><br>
                                        <select name="credito" id="credito" class="form-control input-sm" title="Crédito:" style="width:250px; " required>
                                        </select>
                                    </div>
                                    <script>
                                        $("#credito").change(function(){
                                           var form_data ={action:2, credito: $("#credito").val() };

                                           $.ajax({
                                               type:"POST",
                                               url:"jsonPptal/gf_interfaz_desembolsosJson.php",
                                               data:form_data,
                                               success: function (data) {
                                                   console.log(data);
                                                   $("#tercero").html(data);
                                                   $("#descripcion").html("Desembolso Crédito N° "+$("#credito").val() );
                                               }
                                           });
                                        });
                                    </script>
                                    <div class="col-sm-4" align="left">  
                                        <label for="tercero" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tercero:</label><br>
                                        <select name="tercero" id="tercero" class="form-control input-sm" title="Tercero:" style="width:250px; " required>
                                        </select>
                                    </div>
                                </div>
                            <?php } else { 
                                #Buscar Datos Desembolso 
                                $des = "SELECT d.id_unico, d.credito, 
                                        d.fecha,
                                        DATE_FORMAT(d.fecha,'%d/%m/%Y'),  
                                        t.id_unico, 
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
                                        t.apellidodos)) AS NOMBRE,
                                        IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                        t.numeroidentificacion, 
                                        CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) , d.descripcion 
                                   FROM gf_desembolsos d 
                                   LEFT JOIN gf_tercero t ON d.tercero = t.id_unico 
                                   WHERE d.id_unico = ".$_GET['des'];
                                $des = $mysqli->query($des);
                                $rowdes = mysqli_fetch_row($des);
                                ?>
                                <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px;"> 
                                    <div class="col-sm-4" align="left">  
                                        <label for="fecha" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                        <select name="fecha" id="fecha" class="form-control input-sm" title="Seleccione una Fecha" style="width:250px; " required>
                                            <option value="<?php echo $rowdes[2]?>"><?php echo $rowdes[3]?></option>
                                        </select>

                                    </div>
                                     <div class="col-sm-4" align="left">  
                                        <label for="credito" class="control-label" ><strong style="color:#03C1FB;">*</strong>Crédito:</label><br>
                                        <select name="credito" id="credito" class="form-control input-sm" title="Crédito:" style="width:250px; " required>
                                            <option value="<?php echo $rowdes[1]?>"><?php echo $rowdes[1].' - '.$rowdes[7]?></option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3" align="left">  
                                        <label for="tercero" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tercero:</label><br>
                                        <select name="tercero" id="tercero" class="form-control input-sm" title="Tercero:" style="width:250px; " required>
                                            <option value="<?php echo $rowdes[4]?>"><?php echo ucwords(mb_strtolower($rowdes[5])).' - '.$rowdes[6]?></option>
                                        </select>
                                    </div>
                                    <div class="col-sm-1" align="left">  
                                        <a href="GF_DESEMBOLSOS.php" class="btn sombra btn-primary" style="margin-left:10px; margin-top: 20px" title="Nuevo"><i class="glyphicon glyphicon-plus"></i></a>
                                    </div>
                                </div>
                            <?php } ?>
                            
                            <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px;"> 
                                <div class="col-sm-1" align="left">  
                                    <label for="dis" class="control-label" ><strong style="color:#03C1FB;"></strong>Disponibilidad:</label><br>
                                </div>
                                <div class="col-sm-3" align="left" style="margin-left:20px">  
                                    <label for="tipoDis" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Disponibilidad:</label><br>
                                    <select name="tipoDis" id="tipoDis" class="form-control input-sm" title="Seleccione Tipo Disponibilidad" style="width:200px; " required>
                                          <option value="">Tipo Disponibilidad</option>
                                            <?php 
                                            $tipo = "SELECT id_unico, codigo, nombre 
                                                FROM gf_tipo_comprobante_pptal 
                                                WHERE clasepptal = 14 
                                                AND tipooperacion = 1 AND vigencia_actual =1 AND compania = $compania 
                                                ORDER BY codigo ASC";
                                            $tipo = $mysqli->query($tipo);
                                            while ($row2 = mysqli_fetch_row($tipo)) { ?>
                                            <option value="<?php echo $row2[0]?>"><?php echo mb_strtoupper($row2[1]).' - '.ucwords(mb_strtolower($row2[2]));?></option>
                                            <?php }?>
                                    </select>
                                </div>
                                <script>
                                $("#tipoDis").change(function()
                                       {
                                           if($("#fecha").val()=="" || $("#credito").val==""){
                                               $("#tipoDis").val("");
                                               $("#mensaje").html('Seleccione fecha y crédito');
                                               $("#myModalError").modal("show");
                                               
                                               
                                           } else {
                                           var form_data = { estruc:2, id_tip_comp:+$("#tipoDis").val() };
                                           $.ajax({
                                             type: "POST",
                                             url: "jsonPptal/consultas.php",
                                             data: form_data,
                                             success: function(response)
                                             { 
                                                 console.log(response);
                                               var numero = response.trim();
                                               $("#numdis").val(numero);
                                               $("#fechaDis").val("");
                                             }//Fin succes.
                                           }); 
                                       }
                                       });//Cierre change.
                                </script>
                                <div class="col-sm-4" align="left" style="margin-left:-20px">  
                                    <label for="numdis" class="control-label" ><strong style="color:#03C1FB;">*</strong>Número:</label><br>
                                    <input name="numdis" id="numdis" class="form-control input-sm" title="Número Disponibilidad" style="width:250px; " required readonly="true"/>
                                </div>
                                <div class="col-sm-4" align="left">  
                                    <label for="fechaDis" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                    <input name="fechaDis" id="fechaDis" class="form-control input-sm" title="Seleccione Fecha Disponibilidad" style="width:250px; " required readonly="true" />
                                </div>
                                <script type="text/javascript">
                                        $("#fechaDis").change(function()
                                        {
                                           if($("#fecha").val()=="" || $("#credito").val==""){
                                               $("#fechaDis").val("");
                                               $("#mensaje").html('Seleccione fecha y crédito');
                                               $("#myModalError").modal("show");
                                           } else {  
                                                if($("#tipoDis").val()==""){
                                                    $("#fechaDis").val("").focus();
                                                    $("#mensaje").html('Seleccione Tipo Disponibilidad');
                                                    $("#myModalError").modal("show");
                                                } else { 
                                                    var fecha = $("#fechaDis").val();
                                                    var combo = document.getElementById("fecha");
                                                    var fechac = combo.options[combo.selectedIndex].text;
                                                    var fecha1 = fechac.split('/').reverse().join('/');
                                                    var fecha2 = fecha.split('/').reverse().join('/');
                                                    if(fecha1>fecha2){
                                                        $("#mensaje").html('La fecha escogida no puede ser menor a la fecha del crédito');
                                                        $("#fechaDis").val("").focus();    
                                                        $("#myModalError").modal("show");
                                                        
                                                    } else {
                                                        var form_data = { case: 4, fecha:fecha};
                                                            $.ajax({
                                                              type: "POST",
                                                              url: "jsonSistema/consultas.php",
                                                              data: form_data,
                                                              success: function(response)
                                                              { 
                                                                  if(response ==1){
                                                                      $("#mensaje").html('El Periodo Escogido Ya Está Cerrado');
                                                                      $("#myModalError").modal("show");
                                                                      $("#fechaDis").val("").focus();

                                                                  } else {
                                                                      fechaDis();
                                                                  }
                                                              }
                                                            });  
                                                     } 
                                            }
                                        }
                                          
                                        });
                                    </script>
                                    <script type="text/javascript">
                                        function fechaDis()
                                        { 
                                            var tipComPal = $("#tipoDis").val();
                                            var fecha = $("#fechaDis").val();
                                            var num = $("#numdis").val();
                                            var form_data = { estruc: 7, tipComPal: tipComPal, fecha: fecha, num:num};
                                            $.ajax({
                                            type: "POST",
                                            url: "jsonPptal/validarFechas.php",
                                            data: form_data,
                                            success: function(response)
                                            {
                                              console.log(response);
                                              if(response == 1)
                                              {
                                                    $("#fechaDis").val("");
                                                    $("#mensaje").html('Fecha Inválida');
                                                    $("#myModalError").modal("show");
                                                
                                              }
                                              else
                                              {
                                                   var fechaReg = $("#fechaReg").val();
                                                  if(fechaReg==""){
                                                      
                                                  } else {
                                                        var fechaR = fechaReg.split('/').reverse().join('/');
                                                        var fechaD = fecha.split('/').reverse().join('/');
                                                        if(fechaR<fechaD){
                                                            $("#mensaje").html('Fecha Inválida');
                                                            $("#myModalError").modal("show");
                                                        } else {
                                                            
                                                       }
                                                 }
                                                  
                                                  
                                              }
                                            }
                                          }); 
                                        }
                                    </script>
                            </div>
                            
                            <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px;"> 
                                <div class="col-sm-1" align="left">  
                                    <label for="reg" class="control-label" ><strong style="color:#03C1FB;"></strong>Registro:</label><br>
                                </div>
                                <div class="col-sm-3" align="left" style="margin-left:20px">  
                                    <label for="tipoReg" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Registro:</label><br>
                                    <select name="tipoReg" id="tipoReg" class="form-control input-sm" title="Seleccione Tipo Registro" style="width:200px; " required>
                                          <option value="">Tipo Registro</option>
                                            <?php 
                                            $tipo = "SELECT id_unico, codigo, nombre 
                                                FROM gf_tipo_comprobante_pptal 
                                                WHERE clasepptal = 15
                                                AND tipooperacion = 1 AND vigencia_actual =1 AND compania = $compania 
                                                ORDER BY codigo ASC";
                                            $tipo = $mysqli->query($tipo);
                                            while ($row2 = mysqli_fetch_row($tipo)) { ?>
                                            <option value="<?php echo $row2[0]?>"><?php echo mb_strtoupper($row2[1]).' - '.ucwords(mb_strtolower($row2[2]));?></option>
                                            <?php }?>
                                    </select>
                                </div>
                                 <script>
                                $("#tipoReg").change(function()
                                       {
                                           if($("#fecha").val()=="" || $("#credito").val==""){
                                               $("#tipoReg").val("");
                                               $("#mensaje").html('Seleccione fecha y crédito');
                                               $("#myModalError").modal("show");
                                               
                                           } else {  
                                           var form_data = { estruc:2, id_tip_comp:+$("#tipoReg").val() };
                                           $.ajax({
                                             type: "POST",
                                             url: "jsonPptal/consultas.php",
                                             data: form_data,
                                             success: function(response)
                                             { 
                                                 console.log(response);
                                               var numero = response.trim();
                                               $("#numReg").val(numero);
                                               $("#fechaReg").val("");
                                             }//Fin succes.
                                           }); 
                                       }
                                       });//Cierre change.
                                </script>
                                <div class="col-sm-4" align="left" style="margin-left:-20px">  
                                    <label for="numReg" class="control-label" ><strong style="color:#03C1FB;">*</strong>Número:</label><br>
                                    <input name="numReg" id="numReg" class="form-control input-sm" title="Número Registro" style="width:250px; " required readonly="true"/>
                                </div>
                                <div class="col-sm-4" align="left">  
                                    <label for="fechaReg" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                    <input name="fechaReg" id="fechaReg" class="form-control input-sm" title="Seleccione Fecha Registro" style="width:250px; " required  readonly="true"/>
                                </div>
                                <script type="text/javascript">
                                        $("#fechaReg").change(function()
                                        {
                                           if($("#fecha").val()=="" || $("#credito").val==""){
                                               $("#fechaReg").val("");
                                               $("#mensaje").html('Seleccione fecha y crédito');
                                               $("#myModalError").modal("show");
                                               
                                           } else {  
                                               if($("#tipoReg").val()==""){
                                                   $("#fechaReg").val("");
                                                   $("#mensaje").html('Seleccione Tipo Registro');
                                                   $("#myModalError").modal("show");
                                               } else { 
                                          var fecha = $("#fechaReg").val();
                                            var form_data = { case: 4, fecha:fecha};
                                            $.ajax({
                                              type: "POST",
                                              url: "jsonSistema/consultas.php",
                                              data: form_data,
                                              success: function(response)
                                              { 
                                                  if(response ==1){
                                                      $("#mensaje").html('Periodo Escogido Ya ha Sido Cerrado');
                                                      $("#myModalError").modal("show");
                                                      $("#fechaReg").val("").focus();

                                                  } else {
                                                      fechaReg("");
                                                  }
                                              }
                                            });   
                                        }
                                    }
                                          
                                        });
                                    </script>
                                    <script type="text/javascript">
                                        function fechaReg()
                                        { 
                                            var tipComPal = $("#tipoReg").val();
                                            var fecha = $("#fechaReg").val();
                                            var num = $("#numReg").val();
                                            var form_data = { estruc: 10, tipComPal: tipComPal, fecha: fecha, num:num};
                                            $.ajax({
                                            type: "POST",
                                            url: "jsonPptal/validarFechas.php",
                                            data: form_data,
                                            success: function(response)
                                            {
                                              
                                              if(response == 1)
                                              {
                                                    $("#fechaReg").val("");
                                                    $("#mensaje").html('Fecha Inválida');
                                                    $("#myModalError").modal("show");
                                                
                                              }
                                              else
                                              {
                                                  var fechaDis = $("#fechaDis").val();
                                                  if(fechaDis==""){
                                                      $("#fechaReg").val("");
                                                      $("#mensaje").html('Fecha Inválida');
                                                      $("#myModalError").modal("show");
                                                  } else {
                                                        var fechaD = fechaDis.split('/').reverse().join('/');
                                                        var fechaR = fecha.split('/').reverse().join('/');
                                                        if(fechaR<fechaD){
                                                            $("#fechaReg").val("");
                                                            $("#mensaje").html('Fecha Inválida');
                                                            $("#myModalError").modal("show");
                                                        } else {
                                                            var fechacxp = $("#fechaCxp").val();
                                                            if(fechacxp==""){
                                                            } else {
                                                                var fechaC= fechacxp.split('/').reverse().join('/');
                                                                 if(fechaR>fechaC){
                                                                    $("#fechaReg").val("");
                                                                    $("#mensaje").html('Fecha Inválida');
                                                                    $("#myModalError").modal("show");
                                                                } else {
                                                                }
                                                           }
                                                       }
                                                 }
                                              }
                                            }
                                          }); 
                                        }
                                    </script>
                            </div>
                            <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px;"> 
                                <div class="col-sm-1" align="left">  
                                    <label for="Cxp" class="control-label" ><strong style="color:#03C1FB;"></strong>Cuenta Por Pagar:</label><br>
                                </div>
                                <div class="col-sm-3" align="left" style="margin-left:20px">  
                                    <label for="tipoCxp" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Cuenta Por Pagar:</label><br>
                                    <select name="tipoCxp" id="tipoCxp" class="form-control input-sm" title="Seleccione Tipo Cuenta Por Pagar" style="width:200px; " required>
                                          <option value="">Tipo Cuenta Por Pagar</option>
                                            <?php 
                                            $tipo = "SELECT id_unico, codigo, nombre 
                                                FROM gf_tipo_comprobante_pptal 
                                                WHERE clasepptal = 16 
                                                AND tipooperacion = 1 AND vigencia_actual =1 AND compania = $compania 
                                                ORDER BY codigo ASC";
                                            $tipo = $mysqli->query($tipo);
                                            while ($row2 = mysqli_fetch_row($tipo)) { ?>
                                            <option value="<?php echo $row2[0]?>"><?php echo mb_strtoupper($row2[1]).' - '.ucwords(mb_strtolower($row2[2]));?></option>
                                            <?php }?>
                                    </select>
                                </div>
                                <script>
                                $("#tipoCxp").change(function()
                                       {
                                           if($("#fecha").val()=="" || $("#credito").val==""){
                                               $("#tipoCxp").val("");
                                               $("#mensaje").html('Seleccione fecha y crédito');
                                               $("#myModalError").modal("show");
                                                
                                           } else {  
                                           var form_data = { estruc:2, id_tip_comp:+$("#tipoCxp").val() };
                                           $.ajax({
                                             type: "POST",
                                             url: "jsonPptal/consultas.php",
                                             data: form_data,
                                             success: function(response)
                                             { 
                                                 console.log(response);
                                               var numero = response.trim();
                                               $("#numCxp").val(numero);
                                               $("#fechaCxp").val("");
                                             }//Fin succes.
                                           }); 
                                       }
                                       });//Cierre change.
                                </script>
                                <div class="col-sm-4" align="left" style="margin-left:-20px">  
                                    <label for="numCxp" class="control-label" ><strong style="color:#03C1FB;">*</strong>Número:</label><br>
                                    <input name="numCxp" id="numCxp" class="form-control input-sm" title="Número Cuenta Por Pagar" style="width:250px; " required readonly="true"/>
                                </div>
                                <div class="col-sm-4" align="left">  
                                    <label for="fechaCxp" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                    <input name="fechaCxp" id="fechaCxp" class="form-control input-sm" title="Seleccione Fecha Cuenta Por Pagar" style="width:250px; " required  readonly/>
                                </div>
                                <script type="text/javascript">
                                        $("#fechaCxp").change(function()
                                        {
                                          if($("#fecha").val()=="" || $("#credito").val==""){
                                              $("#fechaCxp").val("");
                                               $("#mensaje").html('Seleccione fecha y crédito');
                                               $("#myModalError").modal("show");
                                              
                                           } else {  
                                               if($("#tipoCxp").val()==""){
                                                   $("#fechaCxp").val("");
                                                   $("#mensaje").html('Seleccione Tipo Cuenta Por Pagar');
                                                    $("#myModalError").modal("show");
                                               } else {  
                                          var fecha = $("#fechaCxp").val();
                                            var form_data = { case: 4, fecha:fecha};
                                            $.ajax({
                                              type: "POST",
                                              url: "jsonSistema/consultas.php",
                                              data: form_data,
                                              success: function(response)
                                              { 
                                                  if(response ==1){
                                                      $("#mensaje").html('El Periodo Seleccionado Ya ha Sido Cerrado');
                                                      $("#myModalError").modal("show");
                                                      $("#fechaCxp").val("").focus();

                                                  } else {
                                                      fechaCxp();
                                                  }
                                              }
                                            });   
                                        }
                                    }
                                          
                                        });
                                    </script>
                                    <script type="text/javascript">
                                        function fechaCxp()
                                        { 
                                            var tipComPal = $("#tipoCxp").val();
                                            var fecha = $("#fechaCxp").val();
                                            var num = $("#numCxp").val();
                                            var form_data = { estruc: 14, tipComPal: tipComPal, fecha: fecha, num:num};
                                            $.ajax({
                                            type: "POST",
                                            url: "jsonPptal/validarFechas.php",
                                            data: form_data,
                                            success: function(response)
                                            {
                                              console.log(response);
                                              if(response == 1)
                                              {
                                                    $("#fechaCxp").val("");
                                                    $("#mensaje").html('Fecha Inválida');
                                                    $("#myModalError").modal("show");
                                                
                                              }
                                              else
                                              {
                                                   var fechaReg = $("#fechaReg").val();
                                                  if(fechaReg==""){
                                                      $("#fechaCxp").val("");
                                                      $("#mensaje").html('Fecha Inválida');
                                                      $("#myModalError").modal("show");
                                                  } else {
                                                        var fechaR = fechaReg.split('/').reverse().join('/');
                                                        var fechaC = fecha.split('/').reverse().join('/');
                                                        if(fechaC<fechaR){
                                                            $("#fechaCxp").val("");
                                                            $("#mensaje").html('Fecha Inválida');
                                                            $("#myModalError").modal("show");
                                                        } else {
                                                            
                                                       }
                                                 }
                                                  
                                              }
                                            }
                                          }); 
                                        }
                                    </script>
                            </div>
                            <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;" align="right">
                                <div class="col-sm-2" align="left">  
                                    <label for="descripcion" class="control-label" ><strong style="color:#03C1FB;"></strong>Descripción:</label>
                                </div>
                                <div class="col-sm-4" align="left"> 
                                    <?php if(empty($_GET['des'])){ ?>
                                    <textarea name="descripcion" id="descripcion" class="form-control input-sm" title="Descripción" style="width:500px; height: 50px" required  placeholder="Descripción" ></textarea>
                                    <?php } else { ?>
                                    <textarea name="descripcion" id="descripcion" class="form-control input-sm" title="Descripción" style="width:500px; height: 50px" required  placeholder="Descripción" ><?php echo $rowdes[7]?></textarea>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-5 " align="right"  style="">
                                    <button type="button" onclick="guardar()"  id="btnGuardar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 0px; margin-bottom: 0px; margin-left: -10px;" title="Guardar"><li class="glyphicon glyphicon-floppy-disk"></li></button> <!--Guardar-->
                                    <input type="hidden" name="MM_insert" >
                                </div>
                            </div>
                            
                            <div class="modal fade" id="modalRubros" role="dialog" align="center" >
                                <div class="modal-dialog">
                                  <div class="modal-content">
                                    <div id="forma-modal" class="modal-header">

                                      <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                    </div>
                                    <div class="modal-body" style="margin-top: 8px">
                                        <p><strong><i></i></strong></p><br/>
                                        <p id="rubros" align="left" ></p> 
                                        
                                    </div>
                                    <div id="forma-modal" class="modal-footer">
                                      <button type="button" id="btnRubros" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            <div class="modal fade" id="mdlerror" role="dialog" align="center" >
                                <div class="modal-dialog">
                                  <div class="modal-content">
                                    <div id="forma-modal" class="modal-header">

                                      <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                    </div>
                                    <div class="modal-body" style="margin-top: 8px">
                                        <label name="mensaje2" id="mensaje2" style="font-weight:normal"></label>
                                        
                                    </div>
                                    <div id="forma-modal" class="modal-footer">
                                      <button type="button" id="btnerror" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                        <input type="hidden" name="MM_insert" >
                        <script>
                                 function guardar() {
                                     
                                     var fecha =$("#fecha").val();
                                     console.log(fecha);
                                     var credito =$("#credito").val();
                                     var tercero =$("#tercero").val();
                                     var tipodis=$("#tipoDis").val();
                                     var numdis=$("#numdis").val();
                                     var fechadis=$("#fechaDis").val();
                                     var tiporeg=$("#tipoReg").val();
                                     var numreg=$("#numReg").val();
                                     var fechareg=$("#fechaReg").val();
                                     var tipocxp=$("#tipoCxp").val();
                                     var numcxp=$("#numCxp").val();
                                     var fechacxp=$("#fechaCxp").val();
                                     var descripcion = $("#descripcion").val();
                                        if(fecha =="" || credito =="" || tipodis =="" || numdis =="" ||  fechadis ==""  || tiporeg =="" || numreg =="" || fechareg =="" || tipocxp =="" || numcxp =="" || fechacxp=="") {
                                             $("#mensaje2").html("Datos Incompletos");
                                             $("#mdlerror").modal("show");
                                        }  else {
                                            jsShowWindowLoad('Generando Interfaz..');
                                            //****Validar saldo de los rubros****//
                                            var form_data ={action:3, fecha:fechadis, credito:credito};
                                            $.ajax({
                                                    type: "POST",
                                                    url: "jsonPptal/gf_interfaz_desembolsosJson.php",
                                                    data: form_data,
                                                    success: function(response)
                                                    { 
                                                        console.log(response);
                                                        var resultado = JSON.parse(response);
                                                        if(resultado !=""){
                                                                var texto = "";
                                                                for (i=0; i<resultado.length; i++){
                                                                    texto += resultado[i];
                                                                    texto +='<br/>';
                                                                }
                                                                jsRemoveWindowLoad();
                                                                document.getElementById("rubros").innerHTML = texto;
                                                                $('#modalRubros').modal('show');
                                                        } else {
                                                            /*******Guardar**********/   
                                                             var form_data ={action:4, fecha:fecha,credito:credito,tercero:tercero,
                                                             tipodis:tipodis,numdis:numdis,fechadis:fechadis,
                                                             tiporeg:tiporeg,numreg:numreg,fechareg:fechareg,
                                                             tipocxp:tipocxp,numcxp:numcxp,fechacxp:fechacxp, descripcion:descripcion};
                                                            $.ajax({
                                                                    type: "POST",
                                                                    url: "jsonPptal/gf_interfaz_desembolsosJson.php",
                                                                    data: form_data,
                                                                    success: function(response)
                                                                    {
                                                                        jsRemoveWindowLoad();
                                                                        console.log(response);
                                                                        if(response==0){
                                                                            
                                                                             $("#Noguardo").modal("show");
                                                                        } else {
                                                                            
                                                                            if(response ==1){
                                                                               $("#mensaje2").html("Tipo De Comprobante Cuenta Por Pagar No Tiene Tipo CNT Asociado");
                                                                                $("#mdlerror").modal("show"); 
                                                                            } else {
                                                                                $("#guardo").modal("show");
                                                                                $("#btnGuardo").click(function(){
                                                                                    document.location=response;
                                                                                })
                                                                            }
                                                                        }
                                                                    }
                                                             });
                                                         }
                                                     }
                                                });
                                                         
                                                     
                                             
                                    }
                                 }
                        </script>
                    </form>
                        <?php } else { ?>
                        <!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                        <!--*Formulario para cuando hay registro de desembolso*-->
                        <!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                       <?php 
                       ####************Buscamos detalles desembolso***********##########
                       $id = $_GET['id'];
                       $sc ="SELECT d.id_unico, DATE_FORMAT( d.fecha,'%d/%m/%Y'), 
                                CONCAT_WS(' ', d.credito, d.descripcion), 
                                cdp.id_unico,tcdp.id_unico, UPPER(tcdp.codigo),LOWER(tcdp.nombre), cdp.numero, DATE_FORMAT(cdp.fecha,'%d/%m/%Y'), 
                                reg.id_unico,treg.id_unico, UPPER(treg.codigo),LOWER(treg.nombre), reg.numero, DATE_FORMAT(reg.fecha,'%d/%m/%Y'), 
                                cxp.id_unico,tcxp.id_unico, UPPER(tcxp.codigo),LOWER(tcxp.nombre), cxp.numero, DATE_FORMAT(cxp.fecha,'%d/%m/%Y'),
                                d.descripcion 
                            FROM 
                                gf_desembolsos d
                            LEFT JOIN 
                               gf_comprobante_pptal cdp ON cdp.id_unico = d.numero_cdp 
                            LEFT JOIN 
                                gf_tipo_comprobante_pptal tcdp ON cdp.tipocomprobante = tcdp.id_unico 
                            LEFT JOIN 
                               gf_comprobante_pptal reg ON reg.id_unico = d.numero_registro 
                            LEFT JOIN 
                                gf_tipo_comprobante_pptal treg ON reg.tipocomprobante = treg.id_unico 
                            LEFT JOIN 
                               gf_comprobante_pptal cxp ON cxp.id_unico = d.obligacion 
                            LEFT JOIN 
                                gf_tipo_comprobante_pptal tcxp ON cxp.tipocomprobante = tcxp.id_unico 
                            WHERE md5(d.id_unico) = '$id'";
                       $sc =$mysqli->query($sc);
                       $row = mysqli_fetch_row($sc);
                       ?>
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="">
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px;"> 
                                <div class="col-sm-4" align="left">  
                                    <label for="fecha" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                    <select name="fecha" id="fecha" class="select2_single form-control input-sm" title="Seleccione una Fecha" style="width:250px; " disabled="disabled">
                                        <option value="<?php echo $row[0]?>"><?php echo $row[1]?></option>
                                    </select>
                                    <script>
                                    </script>
                                </div>
                                <div class="col-sm-4" align="left">  
                                    <label for="credito" class="control-label" ><strong style="color:#03C1FB;">*</strong>Crédito:</label><br>
                                    <select name="credito" id="credito" class="form-control input-sm" title="Crédito" style="width:250px; " required disabled="true">
                                        <option value="<?php echo $row[0]?>"><?php echo $row[2]?></option>
                                    </select>
                                </div>
                                <div class="col-sm-1" align="left">  
                                    <a href="GF_DESEMBOLSOS.php" class="btn sombra btn-primary" style="margin-left:10px; margin-top: 20px" title="Nuevo"><i class="glyphicon glyphicon-plus"></i></a>
                                </div>
                            </div>
                            <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px;"> 
                                <div class="col-sm-1" align="left">  
                                    <label for="dis" class="control-label" ><strong style="color:#03C1FB;"></strong>Disponibilidad:</label><br>
                                </div>
                                <div class="col-sm-3" align="left" style="margin-left:20px">  
                                    <label for="tipoDis" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Disponibilidad:</label><br>
                                    <select name="tipoDis" id="tipoDis" class="form-control input-sm" title="Seleccione Tipo Disponibilidad" style="width:200px; " required>
                                        <option value="<?php echo $row[4]?>"><?php echo $row[5].' - '.ucwords($row[6])?></option>
                                    </select>
                                </div>
                                <div class="col-sm-4" align="left" style="margin-left:-20px">  
                                    <label for="numdis" class="control-label" ><strong style="color:#03C1FB;">*</strong>Número:</label><br>
                                    <input name="numdis" id="numdis" class="form-control input-sm" title="Número Disponibilidad" style="width:250px; " required readonly="true" value="<?php echo $row[7]?>"/>
                                </div>
                                <div class="col-sm-3" align="left">  
                                    <label for="fechaDis" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                    <input name="fechaDis" id="fechaDis" class="form-control input-sm" title="Seleccione Fecha Disponibilidad" style="width:250px; " disabled="true" value="<?php echo $row[8]?>" />
                                </div>
                                <div class="col-sm-1" align="left">  
                                    <a href="EXPEDIR_DISPONIBILIDAD_PPTAL.php?dis=<?php echo md5($row[3])?>" target="_blank" class="btn sombra btn-primary" style="margin-left:10px; margin-top: 20px"><i class="glyphicon glyphicon-eye-open"></i></a>
                                </div>
                            </div>
                            
                            <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px;"> 
                                <div class="col-sm-1" align="left">  
                                    <label for="reg" class="control-label" ><strong style="color:#03C1FB;"></strong>Registro:</label><br>
                                </div>
                                <div class="col-sm-3" align="left" style="margin-left:20px">  
                                    <label for="tipoReg" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Registro:</label><br>
                                    <select name="tipoReg" id="tipoReg" class="form-control input-sm" title="Seleccione Tipo Registro" style="width:200px; " required>
                                          <option value="<?php echo $row[10]?>"><?php echo $row[11].' - '.ucwords($row[12])?></option>
                                            
                                    </select>
                                </div>
                                <div class="col-sm-4" align="left" style="margin-left:-20px">  
                                    <label for="numReg" class="control-label" ><strong style="color:#03C1FB;">*</strong>Número:</label><br>
                                    <input name="numReg" id="numReg" class="form-control input-sm" title="Número Registro" style="width:250px; " required readonly="true" disabled="true" value="<?php echo $row[13]?>"/>
                                </div>
                                <div class="col-sm-3" align="left">  
                                    <label for="fechaReg" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                    <input name="fechaReg" id="fechaDis" class="form-control input-sm" title="Seleccione Fecha Registro" style="width:250px; " disabled="true" value="<?php echo $row[14]?>" />
                                </div>
                                <div class="col-sm-1" align="left">  
                                    <a href="EXPEDIR_REGISTRO_PPTAL.php?reg=<?php echo md5($row[9])?>" target="_blank" class="btn sombra btn-primary" style="margin-left:10px; margin-top: 20px"><i class="glyphicon glyphicon-eye-open"></i></a>
                                </div>
                            </div>
                            <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px;"> 
                                <div class="col-sm-1" align="left">  
                                    <label for="Cxp" class="control-label" ><strong style="color:#03C1FB;"></strong>Cuenta Por Pagar:</label><br>
                                </div>
                                <div class="col-sm-3" align="left" style="margin-left:20px">  
                                    <label for="tipoCxp" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Cuenta Por Pagar:</label><br>
                                    <select name="tipoCxp" id="tipoCxp" class="form-control input-sm" title="Seleccione Tipo Cuenta Por Pagar" style="width:200px; " required>
                                          <option value="<?php echo $row[16]?>"><?php echo $row[17].' - '.ucwords($row[18])?></option>
                                            
                                    </select>
                                </div>
                                <div class="col-sm-4" align="left" style="margin-left:-20px">  
                                    <label for="numCxp" class="control-label" ><strong style="color:#03C1FB;">*</strong>Número:</label><br>
                                    <input name="numCxp" id="numCxp" class="form-control input-sm" title="Número Cuenta Por Pagar" style="width:250px; " required readonly="true" disabled="true" value="<?php echo $row[19]?>"/>
                                </div>
                                <div class="col-sm-3" align="left">  
                                    <label for="fechaCxp" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                    <input name="fechaCxp" id="fechaCxp" class="form-control input-sm" title="Seleccione Fecha Cuenta Por Pagar" style="width:250px; " disabled="true" value="<?php echo $row[20]?>" />
                                </div>
                                <div class="col-sm-1" align="left">  
                                    <a href="GENERAR_CUENTA_PAGAR.php?cxp=<?php echo md5($row[15])?>" target="_blank"  class="btn sombra btn-primary" style="margin-left:10px; margin-top: 20px"><i class="glyphicon glyphicon-eye-open"></i></a>
                                </div>
                            </div>
                            <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;" align="right">
                                <div class="col-sm-2" align="left">  
                                    <label for="descripcion" class="control-label" ><strong style="color:#03C1FB;"></strong>Descripción:</label>
                                </div>
                                <div class="col-sm-4" align="left">  
                                    <textarea name="descripcion" id="descripcion" class="form-control input-sm" title="Descripción" style="width:500px; height: 50px" required  placeholder="Descripción" readonly="true"><?php echo $row[21]?></textarea>
                                </div> 
                            </div>
                        <input type="hidden" name="MM_insert" >
                    </form>

<!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                        <?php } ?>   
                        
                        
                        
                </div>
            </div>
                <div class="modal fade" id="guardo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="forma-modal" class="modal-header">
                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 8px">
                            <p>Interfáz Generada Correctamente</p>
                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="button" id="btnGuardo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                            Aceptar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
                <div class="modal fade" id="Noguardo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="forma-modal" class="modal-header">
                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 8px">
                            <p>Interfáz No Se Ha Podido Generar</p>
                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="button" id="btnNoGuardo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                            Aceptar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="myModalError" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content">
                      <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                      </div>
                      <div class="modal-body" style="margin-top: 8px">
                          <labe id="mensaje" name="mensaje" style="font-weight:light"></labe>
                      </div>
                      <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnErrorModal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                        </button>
                      </div>
                    </div>
                </div>
            </div>
            </div>
        </div> <!-- Cierra clase col-sm-10 text-left -->
    </div> <!-- Cierra clase row content -->

<script src="js/select/select2.full.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() 
    {
        $(".select2_single").select2(
        {
            allowClear: true
        });
    });
</script>
<script>
function jsRemoveWindowLoad() {
    // eliminamos el div que bloquea pantalla
    $("#WindowLoad").remove(); 
}
 
function jsShowWindowLoad(mensaje) {
    //eliminamos si existe un div ya bloqueando
    jsRemoveWindowLoad(); 
    //si no enviamos mensaje se pondra este por defecto
    if (mensaje === undefined) mensaje = "Procesando la información<br>Espere por favor"; 
    //centrar imagen gif
    height = 20;//El div del titulo, para que se vea mas arriba (H)
    var ancho = 0;
    var alto = 0; 
    //obtenemos el ancho y alto de la ventana de nuestro navegador, compatible con todos los navegadores
    if (window.innerWidth == undefined) ancho = window.screen.width;
    else ancho = window.innerWidth;
    if (window.innerHeight == undefined) alto = window.screen.height;
    else alto = window.innerHeight; 
    //operación necesaria para centrar el div que muestra el mensaje
    var heightdivsito = alto/2 - parseInt(height)/2;//Se utiliza en el margen superior, para centrar 
   //imagen que aparece mientras nuestro div es mostrado y da apariencia de cargando
    imgCentro = "<div style='text-align:center;height:" + alto + "px;'><div  style='color:#FFFFFF;margin-top:" + heightdivsito + "px; font-size:20px;font-weight:bold;color:#1075C1'>" + mensaje + "</div><img src='img/loading.gif'/></div>"; 
        //creamos el div que bloquea grande------------------------------------------
        div = document.createElement("div");
        div.id = "WindowLoad";
        div.style.width = ancho + "px";
        div.style.height = alto + "px";        
        $("body").append(div); 
        //creamos un input text para que el foco se plasme en este y el usuario no pueda escribir en nada de atras
        input = document.createElement("input");
        input.id = "focusInput";
        input.type = "text"; 
        //asignamos el div que bloquea
        $("#WindowLoad").append(input); 
        //asignamos el foco y ocultamos el input text
        $("#focusInput").focus();
        $("#focusInput").hide(); 
        //centramos el div del texto
        $("#WindowLoad").html(imgCentro);
 
}
</script>

<style>
#WindowLoad{
    position:fixed;
    top:0px;
    left:0px;
    z-index:3200;
    filter:alpha(opacity=80);
   -moz-opacity:80;
    opacity:0.80;
    background:#FFF;
}
</style>
<?php require_once 'footer.php'; ?>

</body>
</html>

