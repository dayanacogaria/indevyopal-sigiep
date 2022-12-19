<?php 
##############################################################################################################################
#                                 MODIFICACIONES
##############################################################################################################################                                                                                                           
#04/12/2017 |Erica G. |Validaciones Fechas
#30/11/2017 |Erica G. |Archivo Creado
##############################################################################################################################
require_once('./Conexion/ConexionPDO.php');
require_once 'head_listar.php'; 
$anno     = $_SESSION['anno'];
$compania =$_SESSION['compania'];
$con = new ConexionPDO();

?>
<title>Interfaz Depreciación</title>

<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="js/md5.pack.js"></script>

</head>
<body>
    <div class="container-fluid text-center"  >
        <div class="row content">
            <?php require_once 'menu.php'; ?> 
            <!-- Localización de los botones de información a la derecha. -->
            <div class="col-sm-10" style="margin-left: -10px;margin-top: 5px" >
                <h2 align="center" class="tituloform col-sm-12" style="margin-top: -5px; margin-bottom: 2px;" >Interfaz Depreciación</h2>
                <div class="col-sm-12">
                    <div class="client-form contenedorForma"  style=""> 
                        <!------Validación Búsqueda---->
                       <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px;"> 
                            <div class="col-sm-11" align="left">  
                                <label for="buscar" class="control-label" ><strong style="color:#03C1FB;"></strong>Buscar:</label><br>
                                <select name="buscar" id="buscar" class="select2_single form-control input-sm" title="Seleccione" style="width:350px; ">
                                      <option value="">Buscar</option>
                                        <?php 
                                        $det = $con->Listar("SELECT d.id_unico, 
                                                    d.comprobante, 
                                                    m.numero, 
                                                    m.mes, 
                                                    d.descripcion 
                                                FROM 
                                                    gf_interfaz_deterioro d 
                                                LEFT JOIN 
                                                    gf_mes m ON d.mes = m.id_unico WHERE m.parametrizacionanno = $anno");
                                        
                                        for($i=0; $i<count($det); $i++) { ?>
                                            <option value="<?php echo $det[$i][0]?>"><?php echo ucwords($det[$i][2]).' - '.$det[$i][3].' - ',$det[$i][4];?></option>
                                        <?php } ?>
                                </select>
                            </div>
                           <!-------------*****ACTION BUSCAR DETERIORO******-------------------->
                           <script>
                               $("#buscar").change(function(){
                                  document.location = ('GF_DETERIORO.php?id='+md5($("#buscar").val()));  
                               })
                           </script>
                            <div class="col-sm-1" align="left">  
                                <a href="GF_DETERIORO.php" class="btn sombra btn-primary" style="margin-left:10px; margin-top: 20px" title="Nuevo"><i class="glyphicon glyphicon-plus"></i></a>
                            </div>
                        </div>
                        
                        <?php if(empty($_GET['id']) ) {   ?>
                        
                        <!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                        <!--*Formulario para cuando no hay registro de deterioro Seleccionado*-->
                        <!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="">
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px;"> 
                                <div class="col-sm-4" align="left">  
                                    <label for="fecha" class="control-label" ><strong style="color:#03C1FB;">*</strong>Año:</label><br>
                                    <select name="annio" id="annio" class="select2_single form-control" title="Seleccione Año" style="width:250px;" required>
                                        <option value="">Año</option>
                                        <?php 
                                        $annio = $con->Listar("SELECT  id_unico, anno FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC");
                                        for($i=0; $i< count($annio); $i++){ ?>
                                             <option value="<?php echo $annio[$i][0];?>"><?php echo $annio[$i][1];?></option>                                
                                        <?php }?>                                    
                                    </select>
                                </div>
                                <div class="col-sm-4" align="left">  
                                     <label for="mes" class="control-label" ><strong style="color:#03C1FB;">*</strong>Mes:</label><br>
                                    <select required name="mes" id="mes" style="width:250px;" class="select2_single form-control" title="Mes" >
                                        <option value="">Mes</option>
                                    </select>
                                </div>                                    
                            </div>
                            <div class="form-group form-inline" id="datos" style=" display: none;margin-top: 5px; margin-left: 5px;" align="right">
                                <div class="col-sm-4" align="left">  
                                    <label for="comprobante" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Comprobante:</label><br>
                                    <select name="comprobante" id="comprobante" class="select2_single form-control input-sm" title="Tipo Comprobante" style="width:250px; " required>
                                        <option value="">Tipo Comprobante</option>
                                        <?php 
                                        $tcom = $con->Listar("SELECT  id_unico, UPPER(sigla), LOWER(nombre) FROM gf_tipo_comprobante WHERE compania = $compania AND clasecontable = 15 ORDER BY sigla");
                                        for($i=0; $i< count($tcom); $i++){ ?>
                                            <option value="<?php echo $tcom[$i][0];?>"><?php echo $tcom[$i][1].' - '.ucwords($tcom[$i][2]);?></option>                                
                                        <?php }?>   
                                    </select>
                                </div>
                                <div class="col-sm-4" align="left">  
                                    <label for="numero" class="control-label" ><strong style="color:#03C1FB;">*</strong>Número:</label><br>
                                    <input id="numero" name="numero" class="form-control input-sm" style="width:250px; " readonly="true"/>
                                </div>
                                <div class="col-sm-4" align="left">  
                                    <label for="fecha" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                    <input id="fecha" name="fecha" class="form-control input-sm" style="width:250px; " readonly="true"/>
                                </div>
                            </div>
                            <script>
                                $("#annio").change(function(){
                                    var form_data={action: 8, annio :$("#annio").val()};
                                    var optionMI ="<option value=''>Mes</option>";
                                    $.ajax({
                                       type:'POST', 
                                       url:'jsonPptal/gf_interfaz_almacen.php',
                                       data: form_data,
                                       success: function(response){
                                           console.log(response);
                                           optionMI =optionMI+response;
                                           $("#mes").html(optionMI).focus();              
                                       }
                                    });
                                });
                            </script>
                            <script>
                                $("#comprobante").change(function(){
                                    var form_data={action: 7, comprobante :$("#comprobante").val()};
                                    $.ajax({
                                       type:'POST', 
                                       url:'jsonPptal/gf_interfaz_almacen.php',
                                       data: form_data,
                                       success: function(response){
                                           console.log(response);
                                           $("#numero").val(response);  
                                           //Validar Fecha
                                            var form_data={estruc: 24, 
                                                           tipComPal :$("#comprobante").val(), 
                                                           fecha :$("#fecha").val(), num:$("#numero").val()};
                                            $.ajax({
                                               type:'POST', 
                                               url:'jsonPptal/validarFechas.php',
                                               data: form_data,
                                               success: function(response){
                                                   console.log(response);
                                                   if(response==1){
                                                        $("#mensaje").html("Fecha Inválida");
                                                        $("#myModalError").modal("show");
                                                        $("#btnErrorModal").click(function(){
                                                            $("#myModalError").modal("hide");
                                                            $("#fecha").val("");
                                                        })
                                                   }
                                               }
                                           });
                                           
                                           
                                       }
                                    });
                                });
                            </script>
                            <script>
                                $("#mes").change(function(){
                                    $("#numero").val(""); 
                                    if($("#mes").val()=="") {
                                        $("#fecha").val("");
                                        $("#datos").css("display","none");
                                        $("#btnGuardar").prop("disabled",true);
                                    } else {
                                        var form_data={action: 6, mes :$("#mes").val(),  annio :$("#annio").val()};
                                        $.ajax({
                                           type:'POST', 
                                           url:'jsonPptal/gf_interfaz_almacen.php',
                                           data: form_data,
                                           success: function(response){
                                               //#Validar SI Periodo Está Cerrado
                                               console.log(response);
                                               $("#fecha").val(response);
                                               $("#btnGuardar").prop("disabled",false);
                                               $("#datos").css("display","block");
                                                var form_data={case: 4, fecha:$("#fecha").val()};
                                                $.ajax({
                                                   type:'POST', 
                                                   url:'jsonSistema/consultas.php',
                                                   data: form_data,
                                                   success: function(response){
                                                       console.log(response);
                                                       if(response==1){
                                                            $("#mensaje").html("Periodo Ya Ha Sido Cerrado");
                                                            $("#myModalError").modal("show");
                                                            $("#btnErrorModal").click(function(){
                                                                $("#myModalError").modal("hide");
                                                                $("#fecha").val("");
                                                            });  
                                                       }
                                                   
                                                   }
                                               })
                                               
                                               
                                           }
                                        });
                                    }
                                });
                            </script>
                            <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;" align="right">
                                <div class="col-sm-12 " align="right"  style="margin-top: -20px; margin-left: -20px;">
                                    <button type="button" onclick="guardar()"  id="btnGuardar" class="btn btn-primary sombra" disabled="true" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 0px; margin-bottom: 0px; margin-left: -10px;" title="Guardar"><li class="glyphicon glyphicon-floppy-disk"></li></button> <!--Guardar-->
                                    <input type="hidden" name="MM_insert" >
                                </div>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                        <script>
                            function guardar(){
                                var anno = $("#annio").val();
                                var mes  = $("#mes").val();
                                var comp = $("#comprobante").val();
                                var num  = $("#numero").val();
                                var fech = $("#fecha").val();
                                if(anno =="" || mes =="" || comp=="" || num =="" || fech==""){
                                    $("#mensaje").html("Datos Incompletos");
                                    $("#myModalError").modal("show");
                                    $("#btnErrorModal").click(function(){
                                        $("#myModalError").modal("hide");
                                    })
                                } else {
                                    //#*****Validar Que Todo Esté Configurado****#//
                                    var form_data = { action: 9, anno:anno, mes:mes };
                                    $.ajax({
                                        type: "POST",
                                        url: "jsonPptal/gf_interfaz_almacen.php",
                                        data: form_data,
                                        success: function(response)
                                        { 
                                            console.log(response);
                                            resultado = JSON.parse(response);
                                            var msj = resultado["msj"];
                                            var rta = resultado["rta"];
                                            if(rta==1){
                                                //****Guardar Configuración***//
                                                jsShowWindowLoad('Generando Interfaz..');
                                                var form_data = { action: 10, anno:anno, mes:mes, comp:comp, num:num,fech:fech};
                                                $.ajax({
                                                type: "POST",
                                                url: "jsonPptal/gf_interfaz_almacen.php",
                                                data: form_data,
                                                success: function(response)
                                                { 
                                                    console.log(response);
                                                    jsRemoveWindowLoad();
                                                    if(response==1){
                                                        $("#mensaje").html("Interaz No Se Ha Podido Generar");
                                                        $("#myModalError").modal("show");
                                                        $("#btnErrorModal").click(function(){
                                                            $("#myModalError").modal("hide");
                                                        }) 
                                                    } else {
                                                        $("#mensaje").html("Interaz Se Ha Generarado Correctamente");
                                                        $("#myModalError").modal("show");
                                                        $("#btnErrorModal").click(function(){
                                                            document.location=response;
                                                        })
                                                        
                                                    }

                                                }
                                                });
                                            
                                            } else {
                                                //****Mostrar Errores Configuración***//
                                                $("#mensaje").html(msj);
                                                $("#myModalError").modal("show");
                                                $("#btnErrorModal").click(function(){
                                                    $("#myModalError").modal("hide");
                                                })
                                                
                                            }
                                        }
                                    }); 

                                    
                                }
                            }
                        </script>
                        <?php } else { ?>
                        <!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                        <!--*Formulario para cuando hay registro*-->
                        <!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                       <?php 
                       ####************Buscamos detalles ***********##########
                       $id = $_GET['id'];
                       $row = $con->Listar("SELECT 
                                        id.id_unico, 
                                        LOWER(m.mes),
                                        a.anno, 
                                        UPPER(tc.sigla), 
                                        LOWER(tc.nombre), 
                                        c.numero, 
                                        DATE_FORMAT(c.fecha, '%d/%m/%Y'), 
                                        c.id_unico 
                                    FROM 
                                        gf_interfaz_deterioro id 
                                    LEFT JOIN 
                                        gf_mes m ON m.id_unico = id.mes 
                                    LEFT JOIN 
                                        gf_parametrizacion_anno a ON a.id_unico = m.parametrizacionanno 
                                    LEFT JOIN 
                                        gf_comprobante_cnt c ON id.comprobante = c.id_unico 
                                    LEFT JOIN 
                                        gf_tipo_comprobante tc ON tc.id_unico = c.tipocomprobante 
                                    WHERE 
                                        md5(id.id_unico) = '$id'");
                       ?>

<!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="">
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px;"> 
                                <div class="col-sm-4" align="left">  
                                    <label for="fecha" class="control-label" ><strong style="color:#03C1FB;">*</strong>Año:</label><br>
                                    <select name="annio" id="annio" class="select2_single form-control" title="Seleccione Año" style="width:250px;" required>
                                        <option value=""><?php echo $row[0][2];?></option>                                   
                                    </select>
                                </div>
                                <div class="col-sm-4" align="left">  
                                     <label for="mes" class="control-label" ><strong style="color:#03C1FB;">*</strong>Mes:</label><br>
                                    <select required name="mes" id="mes" style="width:250px;" class="select2_single form-control" title="Mes" >
                                        <option value=""><?php echo ucwords($row[0][1]);?></option>
                                    </select>
                                </div>                                    
                            </div>
                            <div class="form-group form-inline" id="datos" style=" margin-top: 5px; margin-left: 5px;" align="right">
                                <div class="col-sm-4" align="left">  
                                    <label for="comprobante" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Comprobante:</label><br>
                                    <select name="comprobante" id="comprobante" class="select2_single form-control input-sm" title="Tipo Comprobante" style="width:250px; " required>
                                        <option value=""><?php echo $row[0][3].' - '.ucwords($row[0][4]);?></option>  
                                    </select>
                                </div>
                                <div class="col-sm-4" align="left">  
                                    <label for="numero" class="control-label" ><strong style="color:#03C1FB;">*</strong>Número:</label><br>
                                    <input id="numero" name="numero" class="form-control input-sm" style="width:250px; " readonly="true" value="<?php echo $row[0][5]?>"/>
                                </div>
                                <div class="col-sm-4" align="left">  
                                    <label for="fecha" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                    <input id="fecha" name="fecha" class="form-control input-sm" style="width:250px; " readonly="true" value="<?php echo $row[0][6]?>"/>
                                </div>
                            </div>
                            <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;" align="right">
                                <div class="col-sm-12 " align="right"  style="margin-top: -20px; margin-left: -20px;">
                                    <a href="registrar_GF_COMPROBANTE_CONTABLE.php?id=<?php echo md5($row[0][7])?>"  target="_blank"   class="btn btn-primary sombra"  style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 0px; margin-bottom: 0px; margin-left: -10px;" title="Ver"><li class="glyphicon glyphicon-eye-open"></li></a> 
                                    <input type="hidden" name="MM_insert" >
                                </div>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                        <?php } ?>   
                        
                        
                        
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

