<?php
require_once 'Conexion/conexion.php';
require_once 'Conexion/ConexionPDO.php';
require_once './jsonPptal/funcionesPptal.php';
require_once 'head_listar.php';
$con = new ConexionPDO();
$anno  = $_SESSION['anno'];
?>
<title>Listar Recaudos Aportes</title>	
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<link href="css/select/select2.min.css" rel="stylesheet">
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
       
        
        $("#fechaI").datepicker({changeMonth: true,}).val();
        $("#fechaF").datepicker({changeMonth: true}).val();
        
        
});
</script>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom:20px; margin-right:4px; margin-left:4px;">Recaudos Aportes</h2>
                <?php if(!empty($_REQUEST['t'])) { ?>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar2()">  
                        <div class="form-group">    
                            <div class="form-group" style="margin-top: 20px;">
                               <label for="fechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                               <input required="required" class="col-sm-2 input-sm" type="text" name="fechaI" id="fechaI" title="Ingrese Fecha Inicial" autocomplete="off">
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                               <label for="fechaF" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                               <input required="required" class="col-sm-2 input-sm" type="text" name="fechaF" id="fechaF" title="Ingrese Fecha Final" autocomplete="off">
                            </div>
                            <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                                <button style="margin-left:10px;" type="submit" class="btn sombra btn-primary" title="Guardar Pagos"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i> Guardar</button>
                            </div>
                        </div>
                    </form>
                    <a href="GA_PAGOS.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: -40px; margin-bottom: 20px; margin-left:10px; margin-right:4px"><i class="glyphicon glyphicon-arrow-left"></i> Pagos</a> </div>
                </div>  
                
                <?php }else { ?>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="30px" align="center"></td>
                                        <td><strong>Recaudo Aporte</strong></td>
                                        <td><strong>Comprobante De Ingreso</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Recaudo Aporte</th>
                                        <th>Comprobante De Ingreso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    #Buscar Recaudos 
                                    $rowr = $con->Listar("SELECT DISTINCT p.id_unico, 
                                        DATE_FORMAT(p.fecha,'%d/%m/%Y'), p.numero,
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
                                    CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
                                    p.fecha 
                                        FROM ga_pago p 
                                        LEFT JOIN ga_detalle_pago dp ON p.id_unico = dp.pago 
                                        LEFT JOIN ga_detalle_aporte da ON dp.detalle_aporte = da.id_unico 
                                        LEFT JOIN ga_aporte a ON da.aporte = a.id_unico 
                                        LEFT JOIN gf_tercero t ON p.tercero = t.id_unico
                                        WHERE p.parametrizacionanno = $anno 
                                        HAVING (SELECT COUNT(dp2.id_unico) FROM ga_detalle_pago dp2 WHERE dp2.pago = p.id_unico)>0      
                                        ORDER BY p.fecha DESC   ");                                 
                                    IF(count($rowr)>0){
                                        for ($z = 0; $z < count($rowr); $z++) {
                                            echo '<tr>';
                                            echo '<td style="display: none;">Identificador</td>';
                                            echo '<td width="30px" align="center"></td>';
                                            $rec = "";
                                            $ci  = "";
                                            echo '<td>';
                                            echo $rowr[$z][1].' '.$rowr[$z][2].' - '.ucwords(mb_strtolower($rowr[$z][3])).' '.$rowr[$z][4].'<br/>';
                                            echo '</td>';
                                            #Buscar Comprobante Ingreso
                                            $rowi = $con->Listar("SELECT DISTINCT cn.id_unico, dpp.comprobantepptal 
                                             FROM gf_comprobante_cnt cn 
                                             LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
                                             LEFT JOIN ga_detalle_pago dp ON dp.detalle_comprobante = dc.id_unico 
                                             LEFT JOIN gf_detalle_comprobante_pptal dpp ON dc.detallecomprobantepptal = dpp.id_unico 
                                             WHERE dp.pago = ".$rowr[$z][0]);
                                            if(count($rowi)>0){
                                                 for ($y = 0; $y < count($rowi); $y++) {
                                                     $ci  .= '<button type="button" onclick="ver('.$rowi[$y][0].','.$rowi[$y][1].')"><i class="glyphicon glyphicon-eye-open"></i> Ver </button><br/>';  
                                                 }
                                            } else {
                                               $ci  .= '<button type="button" onclick="guardar('.$rowr[$z][0].','."'".$rowr[$z][5]."'".')"><i class="glyphicon glyphicon-floppy-disk"> </i> Registar </button><br/>';  
                                            }
                                            echo '<td>'.$ci.'</td>';
                                            echo '</tr>';
                                        }
                                    }

                                    ?>
                                </tbody>	
                            </table>
                        </div>
                    </div>
                <a href="GA_PAGOS.php?t=1" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:10px; margin-right:4px">Registrar Interfáz Lote</a> </div>
                <?php }?>
            </div>
        </div>
    </div>
   <div class="modal fade" id="mdlMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
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
        function guardar(id, fecha){
            //Verficar Cierre Periodo 
            var form_data = {case: 4, fecha: fecha};
            $.ajax({
                type: "POST",
                url: "jsonSistema/consultas.php",
                data: form_data,
                success: function (response)
                {
                    console.log(response+'cierre');
                    if (response == 1) {
                        $("#mensaje").html('Periodo Cerrado');
                        $("#mdlMensajes").modal("show");

                    } else {
                        jsShowWindowLoad('Verificando..');
                        var form_data = { action: 3, id:id };
                        $.ajax({
                            type: "POST",
                            url: "jsonPptal/ga_control_cooperativo.php",
                            data: form_data,
                            success: function(response)
                            {
                                jsRemoveWindowLoad();
                                console.log(response);
                                var resultado = JSON.parse(response);
                                var rta = resultado["rta"];
                                var mensaje = resultado["msj"];
                                if(rta==1){
                                    jsShowWindowLoad('Verificando..');
                                    var form_data = { action: 4, id:id };
                                    $.ajax({
                                        type: "POST",
                                        url: "jsonPptal/ga_control_cooperativo.php",
                                        data: form_data,
                                        success: function(response)
                                        {
                                            console.log(response);
                                            jsRemoveWindowLoad();
                                            if(response==1){
                                                jsShowWindowLoad('Guardando..');
                                                var form_data = { action: 5, id:id };
                                                $.ajax({
                                                    type: "POST",
                                                    url: "jsonPptal/ga_control_cooperativo.php",
                                                    data: form_data,
                                                    success: function(response)
                                                    {
                                                        console.log(response);
                                                        jsRemoveWindowLoad();
                                                        if(response>0){
                                                            $("#mensaje").html('Comprobante Guardado Correctamente');
                                                            $("#mdlMensajes").modal("show");
                                                            $("#btnAceptar").click(function(){
                                                                document.location.reload();
                                                            });
                                                        } else {
                                                            $("#mensaje").html('No Se Ha Podido Registrar Comprobante');
                                                            $("#mdlMensajes").modal("show");
                                                            $("#btnAceptar").click(function(){
                                                                document.location.reload();
                                                            });
                                                        }
                                                    }
                                                })
                                            } else {
                                                $("#mensaje").html('No Se Ha Podido Encontrar Tipo Comprobante Con Indicador de Interfáz Aporte');
                                                $("#mdlMensajes").modal("show");
                                                $("#btnAceptar").click(function(){
                                                    document.location.reload();
                                                });
                                            }
                                        }
                                    })
                                } else {
                                    $("#mensaje").html(mensaje);
                                    $("#mdlMensajes").modal("show");
                                    $("#btnAceptar").click(function(){
                                        $("#mdlMensajes").modal("hide");
                                    });
                                }
                            }
                        })
                    }
                }
            })          
            
            
        }
        function guardar2(){
            //Verficar Cierre Periodo 
            let fecha = $("#fechaI").val();
            let fechaf= $("#fechaF").val();
            var form_data = {case: 4, fecha: fecha};
            $.ajax({
                type: "POST",
                url: "jsonSistema/consultas.php",
                data: form_data,
                success: function (response)
                {
                    console.log(response+'cierre');
                    if (response == 1) {
                        $("#mensaje").html('Periodo Cerrado');
                        $("#mdlMensajes").modal("show");

                    } else {
                        jsShowWindowLoad('Verificando..');
                        var form_data = { action: 3, fecha:fecha, fechaf:fechaf};
                        $.ajax({
                            type: "POST",
                            url: "jsonPptal/ga_control_cooperativo.php",
                            data: form_data,
                            success: function(response)
                            {
                                jsRemoveWindowLoad();
                                console.log(response);
                                var resultado = JSON.parse(response);
                                var rta = resultado["rta"];
                                var mensaje = resultado["msj"];
                                if(rta==1){
                                    jsShowWindowLoad('Verificando..');
                                    var form_data = { action: 4, fecha:fecha, fechaf:fechaf };
                                    $.ajax({
                                        type: "POST",
                                        url: "jsonPptal/ga_control_cooperativo.php",
                                        data: form_data,
                                        success: function(response)
                                        {
                                            console.log(response);
                                            jsRemoveWindowLoad();
                                            if(response==1){
                                                jsShowWindowLoad('Guardando..');
                                                var form_data = { action: 5, fecha:fecha, fechaf:fechaf };
                                                $.ajax({
                                                    type: "POST",
                                                    url: "jsonPptal/ga_control_cooperativo.php",
                                                    data: form_data,
                                                    success: function(response)
                                                    {
                                                        console.log(response);
                                                        jsRemoveWindowLoad();
                                                        if(response>0){
                                                            $("#mensaje").html('Comprobante Guardado Correctamente');
                                                            $("#mdlMensajes").modal("show");
                                                            $("#btnAceptar").click(function(){
                                                                document.location.reload();
                                                            });
                                                        } else {
                                                            $("#mensaje").html('No Se Ha Podido Registrar Comprobante');
                                                            $("#mdlMensajes").modal("show");
                                                            $("#btnAceptar").click(function(){
                                                                document.location.reload();
                                                            });
                                                        }
                                                    }
                                                })
                                            } else {
                                                $("#mensaje").html('No Se Ha Podido Encontrar Tipo Comprobante Con Indicador de Interfáz Aporte');
                                                $("#mdlMensajes").modal("show");
                                                $("#btnAceptar").click(function(){
                                                    document.location.reload();
                                                });
                                            }
                                        }
                                    })
                                } else {
                                    $("#mensaje").html(mensaje);
                                    $("#mdlMensajes").modal("show");
                                    $("#btnAceptar").click(function(){
                                        $("#mdlMensajes").modal("hide");
                                    });
                                }
                            }
                        })
                    }
                }
            })          
            
            
        }
    </script>
    <script>
        function ver(id, idp){
            var form_data = { action: 9, id:id, idp:idp };
            $.ajax({
                type: "POST",
                url: "jsonPptal/gf_interfaz_ComercioJson.php",
                data: form_data,
                success: function(response)
                {
                    window.open("registrar_GF_COMPROBANTE_INGRESO.php");
                }
            })
        }
    </script>
</body>
</html>
