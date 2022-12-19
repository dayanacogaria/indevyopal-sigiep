<?php 
####################################MODIFICACIONES#######################################
#16/06/2016 | ERICA G. | CAMBIO CODIGO, QUITAR VARIABLES DE SESSION, PARAM, AÑO Y CIERRE
##########################################################################################
require_once('Conexion/conexion.php');
require_once 'head_listar.php'; 
require_once('./jsonPptal/funcionesPptal.php');
require_once('./jsonSistema/funcionCierre.php');
$anno     = $_SESSION['anno'];
$compania =$_SESSION['compania'];
if(!empty($_GET['id'])){
    ###########BUSCAR LOS DATOS DE LA ADICION###########
    $adicion = $_GET['id'];
    $modif = "SELECT com.id_unico,
        com.numero,
        DATE_FORMAT(com.fecha, '%d-%m-%Y'),
        DATE_FORMAT(com.fechavencimiento, '%d-%m-%Y'),
        com.descripcion,
        tip.id_unico, 
        UPPER(tip.codigo), 
        LOWER(tip.nombre),
        (SELECT
            SUM(dcp.valor)
        FROM
            gf_detalle_comprobante_pptal dcp
        WHERE
            dcp.comprobantepptal = com.id_unico
            ) AS valor
    FROM
        gf_comprobante_pptal com
    LEFT JOIN gf_tipo_comprobante_pptal tip ON
        tip.id_unico = com.tipocomprobante
    WHERE
        md5(com.id_unico) = '$adicion'";
    $modif = $mysqli->query($modif);
    $modif = mysqli_fetch_row($modif);
    $idModificacion = $modif[0];
    $numeroMod      = $modif[1];
    $fecha          = $modif[2];
    $fechaVen       = $modif[3];
    $descripcion    = $modif[4];
    $idTipo         = $modif[5];
    $nombreTipo     = $modif[6].' - '.ucwords($modif[7]);
    
} else {
    $adicion="";
}
?>
<title>Adición Apropiación</title>

<!--FUNCION BALANCE-->
<script type="text/javascript">
    $(document).ready(function(){
        $("#accordion").mouseover(function()
        {
            var balanceo = document.getElementById("balanceo").value;
            if(balanceo == 1)
            {
                $("#modDesBal").modal('show');
                $("#btnDesBal").focus();
            }
        });
    });
</script>
<script type="text/javascript">
    function coordenadas(event) 
    {
        var y = event.clientY;
        var balanceo = document.getElementById("balanceo").value;
        if(balanceo == 1)
        {
            if(y >= 0 && y <= 20 )
            {
                $("#modDesBal").modal('show');
                $("#btnDesBal").focus();
            }
        }
    }
</script>

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
    <?PHP if(!empty($adicion)) { ?>
    var fecha = '<?php echo date("d/m/Y", strtotime($fecha));?>';
    var fechaVen = '<?php echo date("d/m/Y", strtotime($fechaVen));?>';
    $("#fecha").datepicker({changeMonth: true}).val(fecha);   
    $("#fechaVen").datepicker({changeMonth: true, minDate: fecha}).val(fechaVen);   
    <?php } else { ?>
    $("#fecha").datepicker({changeMonth: true}).val();   
    $("#fechaVen").datepicker({changeMonth: true}).val();       
    <?php } ?>    
    
    });
</script>
<!--ESTILOS-->
<style type="text/css">
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
    .contenedorForma2{
    /*border: 1px solid #020324; #E9E9E9*/
    border: 1px solid #E9E9E9;
    border-radius: 10px; 
    margin-left: 4px;
    margin-right: 4px;
    }
    .area
    { 
    height: auto !important;  
    }  
    .acotado
    {
    white-space: normal;
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
    body{
    font-size: 10px;
    }
</style>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<link href="css/select/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script src="js/md5.pack.js"></script>
</head>
<body onMouseMove="coordenadas(event);"> 
    <?php if(!empty($adicion)) { 
        $balance = balanceapropiacion($idModificacion);
     ?>
    <input type="hidden" id="balanceo" value="<?php echo $balance?>">
    <input type="hidden" id="id" value="<?php echo $idModificacion?>">
    <?php } else { ?>
    <input type="hidden" id="balanceo">
    <?php } ?>
    <div class="container-fluid text-center"  >
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" >
                <h2 align="center" class="tituloform col-sm-10" style="margin-top: -5px; margin-bottom: 2px;" >Adición Apropiación</h2>
                <div class="col-sm-10"> 
                    <div class="client-form contenedorForma form-inline col-sm-12" style="margin-bottom: 8px; padding-bottom: 8px;" >
                        <p align="center" class="parrafoO" style="margin-bottom: 5px">
                        Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
                        </p>
                        <form action="javascript: guardar();">
                            <div class="col-sm-12" align="left" style="padding-left: 0px;"> <!-- Fila Uno -->
                                <div class="col-sm-3" align="left" style="padding-left: 0px;">
                                    <label for="tipoComPtal" class="control-label " ><strong style="color:#03C1FB;">*</strong>Tipo Comprobante Pptal:</label><br/>
                                    <select name="tipoComPtal" id="tipoComPtal" class="select2_single form-control input-sm" title="Seleccione un tipo de comprobante" style="width: 180px;" required>
                                    <?php if(!empty($adicion)) { ?>
                                        <option value="<?php echo $idTipo; ?>"><?php echo $nombreTipo; ?></option>
                                    <?php }  else { ?>
                                        <?php $queryTipComPtal = "SELECT id_unico, UPPER(codigo), LOWER(nombre)        
                                                    FROM gf_tipo_comprobante_pptal 
                                                    WHERE clasepptal = 13 
                                                    AND tipooperacion = 2 
                                                    AND vigencia_actual = 1 
                                                    AND compania = $compania 
                                                    ORDER BY codigo";
                                        $tipoComPtal = $mysqli->query($queryTipComPtal);?>
                                       <option value="">Tipo Comprobante</option>
                                        <?php 
                                        while($rowTipComPtal = mysqli_fetch_row($tipoComPtal))
                                        {
                                            echo '<option value="'.$rowTipComPtal[0].'">'.$rowTipComPtal[1].' '.ucwords(($rowTipComPtal[2])).'</option>';
                                        }
                                        ?>
                                    <?php } ?>
                                    </select>
                            </div>
                            <!-- Número -->
                            <div class="col-sm-3" align="left" style="padding-left: 0px;">
                                <div style="width: 150px;">
                                    <label for="noDisponibilidad" class="control-label" style="">
                                    <strong style="color:#03C1FB;">*</strong>Número:
                                    </label>
                                </div>
                                <?php if(!empty($adicion)) { ?>
                                    <input class="input-sm" type="text" name="noDisponibilidad" id="noDisponibilidad" class="form-control" style="width: 150px;" title="Número de disponibilidad" placeholder="Número Disponibilidad"  readonly="readonly" value="<?php echo $numeroMod;?>" required>
                                <?php }  else { ?>
                                    <input class="input-sm" type="text" name="noDisponibilidad" id="noDisponibilidad" class="form-control" style="width: 150px;" title="Número de disponibilidad" placeholder="Número Disponibilidad"  readonly="readonly" value="" required>
                                <?php } ?>
                            </div>
                            <script>
                                $("#tipoComPtal").change(function(){
                                    var form_data = { estruc: 2, id_tip_comp:+$("#tipoComPtal").val() };
                                    $.ajax({
                                        type: "POST",
                                        url: "jsonPptal/consultas.php",
                                        data: form_data,
                                        success: function(response)
                                        {                             
                                        var numero = parseInt(response);
                                        $("#noDisponibilidad").val(numero);
                                        }
                                    })
                                })
                            </script>
                            <div class="col-sm-5" style=" margin-left: -30px" > <!-- Buscar disponibilidad -->
                                <label for="noDisponibilidad" class="control-label" style="">
                                    <strong style="color:#03C1FB;"></strong>Buscar Registro:
                                </label>
                                <select class="select2_single form-control" name="buscarDisp" id="buscarDisp" style="width:250px">
                                    <option value="">Registro</option>
                                    <?php $reg = "SELECT
                                    cp.id_unico,
                                    cp.numero,
                                    cp.fecha,
                                    tcp.codigo,
                                    IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                                    OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                                    (tr.razonsocial),
                                    CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
                                    tr.numeroidentificacion
                                    FROM
                                    gf_comprobante_pptal cp
                                    LEFT JOIN
                                    gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                                    LEFT JOIN
                                    gf_tercero tr ON cp.tercero = tr.id_unico 
                                    WHERE tcp.clasepptal = 13 AND tcp.tipooperacion=2 
                                    AND cp.parametrizacionanno = $anno 
                                    AND tcp.vigencia_actual = 1 ORDER BY cp.numero DESC";
                                    $reg = $mysqli->query($reg); 
                                    while ($row1 = mysqli_fetch_row($reg)) { 
                                    $date= new DateTime($row1[2]);
                                    $f= $date->format('d/m/Y');
                                    $sqlValor = 'SELECT SUM(dc.valor) 
                                    FROM gf_detalle_comprobante_pptal dc
                                    LEFT JOIN gf_rubro_fuente rf ON dc.rubrofuente = rf.id_unico 
                                    LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico 
                                    LEFT JOIN gf_tipo_clase_pptal tcp On rp.tipoclase = tcp.id_unico 
                                    WHERE comprobantepptal = '.$row1[0];
                                    $valor = $mysqli->query($sqlValor);
                                    $rowV = mysqli_fetch_row($valor);
                                    $v=' $'.number_format($rowV[0], 2, '.', ','); ?>
                                    <option value="<?php echo $row1[0]?>"><?php echo $row1[1].' '. mb_strtoupper($row1[3]).' '.$f.' '.$v?>
                                    <?php }?>
                                </select>
                            </div>
                            <!-- BUSQUEDAS --> 
                            <script type="text/javascript"> 
                            $("#buscarDisp").change(function(){ 
                                if(($("#buscarDisp").val() != "") && ($("#buscarDisp").val() != 0) ) {
                                   var id =$("#buscarDisp").val() ;
                                   document.location = 'ADICION_APROPIACION.php?id='+md5(id); 
                                } 
                           });

                            </script>
                            <!-- Botón Nuevo --> 
                            <div class="col-sm-2" style="margin-left:-60px">
                                <div class="col-sm-1" style="margin-top: -15px;">
                                    <a id="btnNuevoComp" class="btn sombra btn-primary" style="width: 40px; margin:  0 auto;" title="Nuevo"><li class="glyphicon glyphicon-plus"></li></a>
                                </div>
                                 <script type="text/javascript">
                                    $("#btnNuevoComp").click(function()
                                    {
                                        document.location = 'ADICION_APROPIACION.php'; 
                                    });
                                </script>
                                <div class="col-sm-1" style="margin-top: -15px; margin-left: 25px">
                                    <button type="submit" id="btnGuardarElComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1;  margin: 0 auto;" title="Guardar" >
                                    <li class="glyphicon glyphicon-floppy-disk"></li>
                                    </button> <!--Guardar-->
                                </div>
                                <script type="text/javascript">
                                    function guardar() {
                                        if(($("#fechaVen").val() != "" && $("#fechaVen").val() != "00/00/0000") && ( $("#fecha").val() != "" && $("#fecha").val() != "00/00/0000") ) {
                                            var numero  = $("#noDisponibilidad").val(); 
                                            var fecha  = $("#fecha").val(); 
                                            var fechaVen  = $("#fechaVen").val();
                                            var descripcion = $("#descripcion").val();
                                            var estado = $("#estado").val();
                                            var tipocomprobante = $("#tipoComPtal").val(); 
                                            var form_data = { action: 1, numero: numero, fecha: fecha, fechaVen: fechaVen, descripcion: descripcion, estado: estado, tipocomprobante: tipocomprobante, sesion: 'idComPtalAdic' };
                                            $.ajax({
                                                type: "POST",
                                                url: "jsonPptal/gf_adicion_apropiacionJson.php",
                                                data: form_data,
                                                success: function(response){  
                                                    console.log(response);
                                                    var response = parseInt(response);
                                                    if(response != 0){
                                                        $("#mdlExitoElComp").modal('show');
                                                        $('#btnExitoElComp').click(function()
                                                        {
                                                            document.location = 'ADICION_APROPIACION.php?id='+md5(response); 
                                                        });
                                                    } else {
                                                        $("#mdlErrorElComp").modal('show');
                                                    }
                                                }
                                            }); 
                                        } else {
                                            $("#mdlErrorFechVen").modal('show');
                                        }
                                    }
                                </script>
                            </div>
                            <div class="col-sm-2" style="margin-left:-60px">
                                <div class="col-sm-1" style="margin-top: 3px; margin-left: 0px">
                                    <button type="button" id="btnModificar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Imprimir">
                                    <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                                    </button> 
                                </div>
                                <script type="text/javascript">
                                    $("#btnModificar").click(function(){
                                        
                                        if(($("#fechaVen").val() != "" && $("#fechaVen").val() != "00/00/0000") && ( $("#fecha").val() != "" && $("#fecha").val() != "00/00/0000") ) {
                                            var fecha  = $("#fecha").val(); 
                                            var fechaVen  = $("#fechaVen").val();
                                            var descripcion = $("#descripcion").val();
                                            var comprobante = $("#id").val(); 
                                            var form_data = {  action: 2,fecha: fecha, fechaVen: fechaVen, 
                                                descripcion: descripcion, comprobante: comprobante};
                                            $.ajax({
                                                type: "POST",
                                                url: "jsonPptal/gf_adicion_apropiacionJson.php",
                                                data: form_data,
                                                success: function(response){      
                                                    console.log(response);
                                                    if(response == 1){
                                                         
                                                        $("#ModificacionConfirmada").modal('show');
                                                    } else {
                                                       $("#ModificacionFallida").modal('show');
                                                        
                                                    }
                                                }
                                            }); 
                                        } else {
                                            $("#mdlErrorFechVen").modal('show');
                                        }
                                    })
                                </script>
                                <div class="col-sm-1" style="margin-top: 3px; margin-left: 25px">
                                    <button type="button" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Firma Dactilar" id="firma" onclick="firma();">
                                    <img src="images/hb2.png" style="width: 14px; height: 14.28px;">
                                    </button> <!--Firma Dactilar-->
                                </div>
                            </div>
                        </div> <!-- Fin Fila Uno -->
                        <div class="col-sm-12" align="left" style="padding-left: 0px;"> <!-- Fila Dos -->
                            <div class="col-sm-3" align="left" style="padding-left: 0px;">
                                <label for="nombre" class=" control-label" style="margin-top: 0px;" >Descripción:</label>
                                <?php if(!empty($adicion)) { ?>
                                <textarea class="" style="margin-left: 0px; margin-top: 0px; margin-bottom: 0px; width:250px; height: 50px; width:180px" class="area" name="descripcion" id="descripcion"  maxlength="500" placeholder="Descripción"  onkeypress="return validarDes(event, true)"><?php echo $descripcion;?></textarea> 
                                <?php } else { ?>
                                <textarea class="" style="margin-left: 0px; margin-top: 0px; margin-bottom: 0px; width:250px; height: 50px; width:180px" class="area" name="descripcion" id="descripcion"  maxlength="500" placeholder="Descripción"  onkeypress="return validarDes(event, true)"></textarea> 
                                <?php } ?>
                            </div>
                            <div class="col-sm-3" align="left" style="padding-left: 0px;">
                                <label for="fecha" class=" control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>
                                <input class=" input-sm" type="text" name="fecha" id="fecha" class="form-control" style="width:150px;" title="Ingrese la fecha" placeholder="Fecha" value="" >
                            </div>
                            <div class="col-sm-3" align="left" style="padding-left: 0px; margin-left: -15px">
                                <label for="fechaVen" class=" control-label"><strong style="color:#03C1FB;">*</strong>Fecha Venc:</label>
                                <input class=" input-sm" type="text" name="fechaVen" id="fechaVen" class="form-control" style="width:150px;" title="Fecha de vencimiento" placeholder="Fecha de vencimiento" value=""   required >  <!--  -->
                            </div>
                            <!-- CAMBIO DE FECHAS -->
                            <script>
                                $("#fecha").change(function(){
                                    var tipComPal = $("#tipoComPtal").val();
                                    if(tipComPal==""){
                                        $("#tipoComprobante").modal('show');
                                    } else {
                                        //VALIDAR SI YA TUVO CIERRE LA FECHA
                                        var fecha = $("#fecha").val();
                                        var form_data = { case: 4, fecha: fecha };

                                        $.ajax({
                                        type: "POST",
                                        url: "jsonSistema/consultas.php",
                                        data: form_data,
                                        success: function(response)
                                        {
                                            console.log(response);
                                            if(response == 1){
                                                $("#periodoC").modal('show');
                                            } else {

                                              fecha1();
                                            }
                                        }
                                      }); 
                                    }
                                })
                            </script>
                            <script>
                                function fecha1(){
                                    var tipComPal = $("#tipoComPtal").val();
                                    var fecha = $("#fecha").val();
                                    var num = $("#noDisponibilidad").val();
                                    <?php if(!empty($adicion)) { ?>
                                    var idComPptal = $("#id").val();
                                    var form_data = { estruc: 2, tipComPal: tipComPal, fecha: fecha, num:num,idComPptal:idComPptal };
                                    <?php } else {  ?>
                                        var form_data = { estruc: 1, tipComPal: tipComPal, fecha: fecha, num:num };
                                    <?php } ?>
                                    $.ajax({
                                    type: "POST",
                                    url: "jsonPptal/validarFechas.php",
                                    data: form_data,
                                    success: function(response)
                                    {
                                        console.log(response); 
                                        if(response == 1){
                                            $("#myModalAlertErrFec").modal('show');
                                        } else {
                                          response = response.replace(" ","");
                                          response= $.trim( response );
                                          $("#fechaVen").val(response);
                                          var fechaAs = $("#fecha").val();
                                          $( "#fechaVen" ).datepicker( "destroy" );
                                          $( "#fechaVen" ).datepicker({ changeMonth: true, minDate: fechaAs}).val(response);

                                        }
                                    }
                                  }); 
                                }
                            </script>    
                            <div class="col-sm-2" align="left" style="padding-left: 0px; margin-left: -30px">
                                <label for="mostrarEstado" class="control-label" >Estado:</label>
                                <input class="input-sm " type="text" name="mostrarEstado" id="mostrarEstado" class="form-control" style="width:100px; " title="El estado es Solicitada" value="Solicitada" readonly="readonly" > 
                                <input type="hidden" value="3" name="estado" id="estado"> <!-- Estado 3, generada -->
                                <button type="button" id="btnmdlmov" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-left: 117px;"  onclick="javascript:abrirdetalleMov(<?php echo $idModificacion ?>);" title="Agregrar">
                                    <i class="glyphicon glyphicon-upload" aria-hidden="true"></i>
                                </button>  <!--New btn-->   
                                <div id="response"></div>
                                <script>                                    
                                    function abrirdetalleMov (id){
                                        var form_data = {
                                        id: id,
                                        valor: 0
                                        };
                                        $.ajax({
                                            type: 'POST',
                                            url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_3.php",
                                            data: form_data,
                                            success: function (data) {
                                                $('#response').html(data);
                                                $(".movi1").modal("show");
                                            }
                                        });
                                    }
                                </script>                          
                            </div>
                            <div class="col-sm-2" style="margin-left:-35px">
                                <div class="col-sm-1" style="margin-top: 3px; margin-left: -10px">
                                    <button type="button" id="btnImprimir" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Imprimir">
                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                    </button> <!--Imprimir-->
                                </div>
                                <div class="col-sm-1" style="margin-top: 3px; margin-left: 25px">
                                    <button type="button" id="btnImprimirExcel" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Imprimir">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                    </button> <!--Imprimir-->                                    
                                </div>
                            </div>                            
                            <?php if(!empty($adicion)) { ?>
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("#btnImprimir").click(function(){
                                        var id =$("#id").val() ;
                                        window.open('informesPptal/inf_Adic_Aprop.php?id='+md5(id));
                                    });
                                });
                                $(document).ready(function() {
                                    $("#btnImprimirExcel").click(function(){
                                        var id =$("#id").val() ;
                                        window.open('informesPptal/inf_Adic_ApropExcel.php?id='+md5(id));
                                    });
                                });
                                
                            </script>                            
                            <?php } ?>
                        </div> <!-- Fin Fila dos -->
                        <div class="col-sm-12" align="left" style="padding-left: 0px;"> <!-- Fila Tres -->
                        </div> <!-- Fin Fila Tres -->
                        </form>
                    </div>
                <div class="client-form contenedorForma2 form-inline col-sm-12" >
                    <!-- Formulario de detalle comprobante pptal -->
                    <form name="formConRub" id="orm" class="form-inline"   enctype="multipart/form-data" onsubmit="return validarValor()"action="javascript: guardarDetalle()">
                        <div class="row" style="margin-top: 0px;" >  
                            <!-- Combo-box Rubro -->
                            <div id="divRubro" class="form-group form-inline col-sm-3" style="margin-top: 5px; margin-left: 5px;" align="left">
                                <label for="rubro" class=" control-label"><strong class="obligado">*</strong>Rubro:</label><br/>
                                <select name="rubro" id="rubro" requiered="required" class=" form-control input-sm select2_single" title="Seleccione el rubro" style="width:150px;">
                                    <option value="" selected="selected" >Rubro</option>
                                    <?php
                                    $rubro ="SELECT id_unico, CONCAT(codi_presupuesto, ' ',nombre) rubro 
                                        FROM gf_rubro_pptal WHERE movimiento = 1 
                                        AND parametrizacionanno = $anno 
                                        ORDER BY codi_presupuesto ASC";
                                    $rubro = $mysqli->query($rubro);
                                    while($rowRub = mysqli_fetch_row($rubro)) { ?>
                                    <option value="<?php echo $rowRub[0]; ?>"><?php echo ucwords(mb_strtolower($rowRub[1])); ?></option>
                                    <?php }
                                    ?>
                                </select> 
                            </div>
                            <!-- Combo-box Fuente -->
                            <div class="form-group form-inline col-sm-3" style="margin-top: 5px;" align="left">
                                <label for="fuente" class="control-label"><strong class="obligado">*</strong>Fuente:</label><br/>
                                <select name="fuente" id="fuente" class="form-control input-sm" title="Seleccione la fuente" style="width:150px;" required>
                                   
                                </select> 
                            </div>
                            <script>
                                $("#rubro").change(function(){
                                   var form_data = { estruc: 5, rubro:$("#rubro").val() }
                                   $.ajax({
                                    type: "POST",
                                    url: "jsonPptal/consultas.php",
                                    data: form_data,
                                    success: function(response)
                                    { //console.log(response);
                                        $("#fuente").html(response).focus();
                                        $("#fuente").select2({
                                            allowClear:true
                                        });
                                    }
                                  }); 
                                });
                            </script>
                            <!-- Caja texto Valor -->
                            <div class="col-sm-3" >
                                <div class="form-group" style="margin-top: 5px; margin-left: 0px; " align="left">
                                    <label for="valor" class="control-label"><strong style="color:#03C1FB;">*</strong>Valor:</label><br/>
                                    <input type="text" name="valor" id="valor" class="form-control input-sm" maxlength="50" style="width:150px;" placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valor', '2');" title="Ingrese el valor" onkeyup="formatC('valor');" required>
                                    <input type="hidden" value="">
                                </div>
                            </div> 
                            <!-- Botón guardar -->
                            <div class="col-sm-1 " >
                                <button type="submit" id="btnGuardarComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-top: 20px;" title="Guardar" ><li class="glyphicon glyphicon-floppy-disk"></li></button> <!--Guardar-->
                                <input type="hidden" name="MM_insert" >
                            </div>
                        </div> <!-- Cierra clase row -->
                    </form>
                </div>  <!-- cierra clase client-form contenedorForma -->
                <input type="hidden" id="idPrevio" value="">
                <input type="hidden" id="idActual" value="">
            </div>
            <!-- Botones de consulta -->
            <div class="col-sm-2" style="margin-top:-60px">
                <table class="tablaC table-condensed" style="margin-left: 0px" >
                    <thead>
                        <th>
                            <h2 class="titulo" align="center">Consultas</h2>
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <td align="center">
                                <div class="btnConsultas">
                                    <a href="#">
                                        BALANCE POR <br>FUENTES
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <div class="btnConsultas">
                                    <a href="#"> 
                                        APROPIACIÓN POR RUBRO
                                    </a>
                                </div>
                            </td>
                        </tr> 
                    </tbody>
                </table>
            </div> <!-- Fin de botones de consulta -->
            <!-- Listado de registros -->
            <div class="table-responsive contTabla col-sm-10" style="margin-top: 10px;">
                <div class="table-responsive contTabla" >
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <td class="oculto">Identificador</td>
                            <td width="7%"></td>
                            <td class="cabeza"><strong>Rubro</strong></td>
                            <td class="cabeza"><strong>Fuente</strong></td>
                            <td class="cabeza"><strong>Valor</strong></td>
                        </tr>
                        <tr>
                            <th class="oculto">Identificador</th>
                            <th width="7%"></th>
                            <th>Rubro</th>
                            <th>Fuente</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(!empty($adicion)){ 
                            $queryGen = "SELECT detCompP.id_unico, 
                                CONCAT(rubP.codi_presupuesto,'- ',rubP.nombre), 
                                CONCAT(fue.id_unico,'- ',fue.nombre), 
                                detCompP.valor, detCompP.rubrofuente 
                            from gf_detalle_comprobante_pptal detCompP
                            left join gf_rubro_fuente rubFue on rubFue.id_unico = detCompP.rubrofuente
                            left join gf_fuente fue on fue.id_unico = rubFue.fuente 
                            left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
                            left join gf_comprobante_pptal compPtal on compPtal.id_unico = detCompP.comprobantepptal
                            where md5(compPtal.id_unico) = '".$_GET['id']."' 
                            order by detCompP.id_unico desc";
                            $resultado = $mysqli->query($queryGen);
                        } else {
                            $resultado ="";
                        }
                        if($resultado == true)
                        {
                        while($row = mysqli_fetch_row($resultado))
                        {
                        ?>
                        <tr>
                            <td class="oculto"><?php echo $row[0]?></td>
                            <td class="campos" >
                                <a class="eliminar" href="#<?php echo $row[0];?>" onclick="javascript:verificarValorEliminar(<?php echo $row[0].','.$row[4];?>);">
                                    <i title="Eliminar" class="glyphicon glyphicon-trash">
                                    </i>
                                </a>
                                <a class="modificar"  href="#<?php echo $row[0];?>" onclick="javascript:modificarDetComp(<?php echo $row[0];?>);" >
                                    <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                </a>
                            </td>
                            <td class="campos" align="left">
                                <?php echo ucwords(mb_strtolower($row[1]));?>
                            </td>
                            <td class="campos" align="left">
                                <?php echo ucwords(mb_strtolower($row[2]));?>
                            </td>
                            <td class="campos" align="right">
                                <input type="hidden" id="valOcul<?php echo $row[0];?>"  value="<?php echo number_format($row[3], 2, '.', ','); ?>">
                                <div id="divVal<?php echo $row[0];?>" >
                                    <?php  
                                    echo number_format($row[3], 2, '.', ',');
                                    ?>
                                </div>
                                <!-- Modificar los valores -->
                                <table id="tab<?php echo $row[0];?>" style="padding: 0px;  margin-top: -10px; margin-bottom: -10px;" >
                                    <tr>
                                        <td>
                                            <input type="hidden" name="valorMod" id="verificar<?php echo $row[0];?>" value="<?php echo $validar?>" >
                                            <input type="text" name="valorMod" id="valorMod<?php echo $row[0];?>" class="fo9rm-control in9put-sm" maxlength="50" style="width:150px; margin-top: -5px; margin-bottom: -5px; " placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valorMod<?php echo $row[0];?>', '2');" onkeyup="formatC('valorMod<?php echo $row[0];?>')" value="<?php echo number_format($row[3], 2, '.', ','); ?>" required>
                                        </td>
                                        <td>
                                            <a href="#<?php echo $row[0];?>" onclick="javascript:verificarValorModificar('<?php echo $row[0];?>','<?php echo $row[4]?>');" >
                                              <i title="Guardar Cambios" class="glyphicon glyphicon-floppy-disk" ></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="#<?php echo $row[0];?>" onclick="javascript:cancelarModificacion(<?php echo $row[0];?>);" >
                                              <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                                <script type="text/javascript">
                                    var id = "<?php echo $row[0];?>";  
                                    var idValorM = 'valorMod'+id;
                                    var idTab = 'tab'+id;
                                    $("#"+idTab).css("display", "none");
                                </script>
                            </td>
                        </tr>
                        <?php   } }?>
                    </tbody>
                    </table>
                </div>
            </div> <!-- Cierra clase table-responsive contTabla  -->
            </div>
        </div> 
    </div> 
    <script src="js/select/select2.full.js"></script>
    <script>
    $(document).ready(function() 
    {
        $(".select2_single").select2(
        {
            allowClear: true
        });
    });
    </script>
    <div class="modal fade" id="myModal" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="myModal1" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información eliminada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal2" role="dialog" align="center" data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="ModificacionConfirmada" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información modificada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModificarConf" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    $('#btnModificarConf').click(function()
    {
        document.location.reload();
    });
    </script>
    <!-- Error al modificar el valor al ser superior al saldo-->
    <div class="modal fade" id="myModalAlertMod" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>El valor ingresado es inválido. Verifique nuevamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptValMod" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalAlertModEliminar" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se puede Eliminar el Registro. Verifique nuevamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptValMod" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensaje de fallo en la modificación. -->
    <div class="modal fade" id="ModificacionFallida" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha podido modificar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModificarFall" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    $('#btnModificarFall').click(function()
    {
        $("#ModificacionFallida").modal('hide');
    });
    </script>
    
     <!-- Modal de alerta. Periodo para la fecha ya ha sido cerrado.  -->
    <div class="modal fade" id="periodoC" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Periodo ya ha sido cerrado, escoja nuevamente la fecha</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="periodoCA" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
      <!-- Modal de alerta. Escoger Tipo Comprobante .  -->
    <div class="modal fade" id="tipoComprobante" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Escoja Tipo de Comprobante</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="tipoComprobanteBtn" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $("#tipoComprobanteBtn").click(function(){
            $("#fechaVen").val("").focus;
            $("#fecha").val("").focus;
        })
    </script>
     
    <!-- Divs de clase Modal para las ventanillas de confirmación de inserción de registro. -->
    <div class="modal fade" id="modDesBal" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No puede abandonar este formulario ya que las fuentes no están balanceadas. Verifique nuevamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnDesBal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Error de fecha --> 
    <div class="modal fade" id="myModalAlertErrFec" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Fecha Inválida. Verifique nuevamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptErrFec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Exito al guardar el comprobante --> 
    <div class="modal fade" id="mdlExitoElComp" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información guardada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnExitoElComp" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Error al guardar el comprobante --> 
    <div class="modal fade" id="mdlErrorElComp" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha podido guardar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrorElComp" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Error al guardar el comprobante --> 
    <div class="modal fade" id="mdlErrorFechVen" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Ingrese todos los datos.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrorFechVen" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>

    <?php require_once 'footer.php'; ?>

    <script type="text/javascript">
    $('#AceptVal').click(function(){ 
    $("#valor").val('').focus();
    });
    </script>
    <script type="text/javascript">
    $('#periodoCA').click(function(){ 
        $("#fecha").val("").focus();
        $("#fechaVen").val("");
    });
    </script>

    <script type="text/javascript">
    $('#AceptCon').click(function(){ 
    $("#valor").val('');
    $("#concepto").focus();
    });
    </script>
    <!-- Función para la eliminación del registro. -->
    <script type="text/javascript">
    function eliminarDetComp(id)
    {
        var result = '';
        $("#myModal").modal('show');
        $("#ver").click(function(){
            $("#mymodal").modal('hide');
            var form_data = { action:4,id:id};
            $.ajax({
                type:"POST",
                url:"jsonPptal/gf_adicion_apropiacionJson.php",
                data: form_data,
                success: function (data) {
                result = JSON.parse(data);
                if(result==1)
                    $("#myModal1").modal('show');
                else
                    $("#myModal2").modal('show');
                }
            });
        });
    }
    </script>
    <script type="text/javascript">
        $('#ver1').click(function(){
            document.location.reload();
        });
    </script>
    <script type="text/javascript">
        $('#ver2').click(function(){
            document.location.reload();
        });
    </script>
    <!-- Función para la modificación del registro. -->
    <script type="text/javascript">
    function modificarDetComp(id)
    {
        if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != ""))
        {
        var cambiarTab = 'tab'+$("#idPrevio").val();
        var cambiarDiv = 'divVal'+$("#idPrevio").val();
        var cambiarOcul = 'valOcul'+$("#idPrevio").val();
        var cambiarMod = 'valorMod'+$("#idPrevio").val();

        if($("#"+cambiarTab).is(':visible'))
        {
            $("#"+cambiarTab).css("display", "none");
            $("#"+cambiarDiv).css("display", "block");
            $("#"+cambiarMod).val($("#"+cambiarOcul).val());
        }
        }
        var idValor = 'valorMod'+id;
        var idDiv = 'divVal'+id;
        var idModi = 'modif'+id;
        var idTabl = 'tab'+id;
        $("#"+idDiv).css("display", "none");
        $("#"+idTabl).css("display", "block");
        $("#idActual").val(id);
        if($("#idPrevio").val() != id)
        $("#idPrevio").val(id);
    }
    </script>
    <script type="text/javascript">
    function cancelarModificacion(id) 
    {
        var idDiv = 'divVal'+id;
        var idTabl = 'tab'+id;
        var idValorM = 'valorMod'+id;
        var idValOcul = 'valOcul'+id;
        $("#"+idDiv).css("display", "block");
        $("#"+idTabl).css("display", "none");
        $("#"+idValorM).val($("#"+idValOcul).val());
    }
    </script>
    <script type="text/javascript">
        function guardarModificacion(id) 
        {
            var idDiv = 'divVal'+id;
            var idTabl = 'tab'+id;
            var idCampoValor = 'valorMod'+id;
            var idValOcul = 'valOcul'+id;
            var valor = $("#"+idCampoValor).val();
            var valor = valor.replace(/\,/g,'');
            var form_data = { action:5,id: id, valor: valor};
            $.ajax({
                type: "POST",
                url: "jsonPptal/gf_adicion_apropiacionJson.php",
                data: form_data,
                success: function(response)
                {
                    if(response == 1)
                    {
                        $("#ModificacionConfirmada").modal('show');
                    }
                    else
                    {
                        $("#ModificacionFallida").modal('show');
                    }
                }
            });
        }
    </script>
    <!-------Guardar Detalle__------------------->
    <script>
            function guardarDetalle(){
                var id     = $("#id").val();
                var rubro  = $("#rubro").val();
                var fuente = $("#fuente").val();
                var valor  = $("#valor").val();
                var descripcion = $("#descripcion").val();
                if(id=="" || rubro =="" || fuente =="" || valor =="" || valor =='0'){
                    $("#mdlErrorFechVen").modal('show');
                } else {
                    var form_data = { action:3,id: id, rubro:rubro, fuente:fuente, valor:valor, descripcion:descripcion};
                    $.ajax({
                        type: "POST",
                        url: "jsonPptal/gf_adicion_apropiacionJson.php",
                        data: form_data,
                        success: function(response)
                        {
                            if(response == 1)
                            {
                                $("#mdlExitoElComp").modal('show');
                                $('#btnExitoElComp').click(function()
                                {
                                    document.location.reload(); 
                                });                        
                            }
                            else
                            {
                                $("#mdlErrorElComp").modal('show');
                            }
                        }
                    });
                }
            }
    </script>
    <!-- Evalúa que el valor no sea superior al saldo en modificar valor-->
    <script type="text/javascript">
    function verificarValorModificar(id_txt,id_rubFue)
    { 
        var idValMod = "valorMod"+id_txt;
        var validar = $("#"+idValMod).val();
        validar = validar.replace(/\,/g,'');
        if((isNaN(validar)) || (validar == 0) || (validar == ""))
        {
            $("#myModalAlertMod").modal('show');
        } else {
            
            var form_data={estruc:6, rubro:id_rubFue, valor:validar, id:id_txt};
            $.ajax({
                type: "POST",
                url: "jsonPptal/consultas.php",
                data: form_data,
                success: function(response)
                {
                    console.log('acaaa');
                    console.log(response);
                    response = parseInt(response)
                    if(response==1)
                    {
                        $("#myModalAlertMod").modal('show');
                    }
                    else
                    {
                        guardarModificacion(id_txt);
                    }
                }
            });
        }
    }

    </script>
    <script type="text/javascript">
    function verificarValorEliminar(id_txt,id_rubFue)
    { 
        var idValMod = "valorMod"+id_txt;
        var validar = $("#"+idValMod).val();
        validar = validar.replace(/\,/g,'');
        var form_data={estruc:7, rubro:id_rubFue, valor:validar, id:id_txt};
        $.ajax({
            type: "POST",
            url: "jsonPptal/consultas.php",
            data: form_data,
            success: function(response)
            {
                console.log('acaaa');
                console.log(response);
                response = parseInt(response)
                if(response==1)
                {
                    $("#myModalAlertModEliminar").modal('show');
                }
                else
                {
                    eliminarDetComp(id_txt);
                }
            }
        });
    }
    </script>
 <!-- Validar el campo valor al guardar el dato. -->
    <script type="text/javascript">

    function validarValor() 
    {
        if($("#rubro").val() != "")
        {
            var valor = $("#valor").val();
            valor = valor.replace(/\,/g,'');
            if((isNaN(valor)) || (valor == 0 ) || (valor == ""))
            {
                $("#myModalAlert").modal('show');
                return false;
            } else {
                return true; 
            }
        }else{
            $("#mdlFaltaRubro").modal('show');
            return false;
        }
    }


    </script>
    <script type="text/javascript">
    $('#AceptValModInval').click(function()
    {
        var id_mod = "valorMod"+$("#idActual").val();
        var id_ocul = "valOcul"+$("#idActual").val();
        $("#"+id_mod).val($("#"+id_ocul).val()).focus();
    });
    </script>
    <script type="text/javascript">
    $('#btnDesBal').click(function()
    {
        $("#rubro").focus();
    });
    </script>
    <script type="text/javascript">
    $('#btnFaltaRubro').click(function()
    {
        $("#rubro").focus();
    });
    </script>
    <script type="text/javascript">
    $('#AceptErrFec').click(function()
    {
        var fechaAct = $("#fechaAct").val();
        $("#fecha").val(fechaAct);
        $("#fechaVen").val("");
    });
    </script>
  
<?php ###########################################################
####################VALIDACION CIERRE######################
 if(!empty($adicion)){
    $cierre = cierre($idModificacion);
    if($cierre ==1){ ?> 
        <script>
            $("#btnGuardarElComp").prop("disabled", true);
            $("#firma").prop("disabled", true);     
            $("#btnModificar").prop("disabled", true);
            $("#btnImprimir").prop("disabled", false);
            $("#btnImprimirExcel").prop("disabled", false);
            $("#rubro").prop("disabled", true);
            $("#fuente").prop("disabled", true);
            $("#valor").prop("disabled", true);
            $("#btnGuardarComp").prop("disabled", true);
            
            $(".eliminar").css('display','none');
            $(".modificar").css('display','none');
            
        </script>
    <?php } else { ?>
        <script>
            $("#btnGuardarElComp").prop("disabled", true);
            $("#firma").prop("disabled", false);     
            $("#btnModificar").prop("disabled", false);
            $("#btnImprimir").prop("disabled", false);
            $("#btnImprimirExcel").prop("disabled", false);
            $("#rubro").prop("disabled", false);
            $("#fuente").prop("disabled", false);
            $("#valor").prop("disabled", false);
            $("#btnGuardarComp").prop("disabled", false);
            
        </script>
    <?php  }
} else { ?>
    <script>
       $("#btnGuardarElComp").prop("disabled", false);
        $("#firma").prop("disabled", true);     
        $("#btnModificar").prop("disabled", true);
        $("#btnImprimir").prop("disabled", true);
        $("#btnImprimirExcel").prop("disabled", true);
        $("#btnmdlmov").prop("disabled", true);
        $("#rubro").prop("disabled", true);
        $("#fuente").prop("disabled", true);
        $("#valor").prop("disabled", true);
        $("#btnGuardarComp").prop("disabled", true);

        $(".eliminar").css('display','none');
        $(".modificar").css('display','none');
    </script>
       
        
    <?php  } ?>
</body>
</html>