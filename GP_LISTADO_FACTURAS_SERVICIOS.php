<?php
#############################################################################
#       ******************     Modificaciones       ******************      #
#############################################################################
#27/08/2018 |Erica G. | ARCHIVO CREADO
#############################################################################
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');   
require './jsonPptal/funcionesPptal.php';
require_once 'head_listar.php';
$con        = new ConexionPDO();     
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];

?>
<html>
    <head>
        <title>Facturas Servicios Públicos</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script> 
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <script src="js/md5.pack.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        
        <style>
            label #tercero-error, #banco-error, #tipoComprobante-error, #numero-error, #fechaF-error, #recaudo-error { 
            display: block;
            color: #bd081c;
            font-weight: bold;
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
                $("#fechaF").datepicker({changeMonth: true,}).val();


        });
        </script>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Facturación Servicios Públicos</h2>
                    <?php if(empty($_GET['p']) && empty($_GET['s1']) && empty($_GET['s2'])) { ?>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:buscar()" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="periodo" class="col-sm-1 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Periodo:</label>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                <select name="periodo" id="periodo" class="form-control select2" title="Seleccione Periodo" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Periodo</option>';
                                        $tr = $con->Listar("SELECT p.* FROM gp_periodo p 
                                            LEFT JOIN gp_ciclo c ON p.ciclo = c.id_unico 
                                            WHERE c.estado_facturacion NOT IN(7,9) 
                                            AND p.anno = $anno ORDER BY p.fecha_inicial DESC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).' - '.$tr[$i]['descripcion'].'</option>'; 
                                        }
                                    ?>
                                </select>
                                </div>
                                <label for="sector1" class="col-sm-1 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Sector Inicial:</label>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                <select name="sector1" id="sector1" class="form-control select2" title="Seleccione Sector Inicial" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Sector Inicial</option>';
                                        $tr = $con->Listar("SELECT * FROM gp_sector ORDER BY id_unico ASC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.$tr[$i][2].' - '.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                        }
                                    ?>
                                </select>
                                </div>
                                <label for="sector2" class="col-sm-1 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Sector Final:</label>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                <select name="sector2" id="sector2" class="form-control select2" title="Seleccione Sector Final" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Sector Final</option>';
                                        $tr = $con->Listar("SELECT * FROM gp_sector ORDER BY id_unico DESC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.$tr[$i][2].' - '.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                        }
                                    ?>
                                </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left: 10px; margin-top: 10px">
                                    <button type="submit" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-search" aria-hidden="true"></i></button>    
                                </div>
                            </div>
                        </form>
                    </div>
                    <script>
                        function buscar(){
                            var periodo = $("#periodo").val();
                            var sector1 = $("#sector1").val();
                            var sector2 = $("#sector2").val();
                            document.location ='GP_LISTADO_FACTURAS_SERVICIOS.php?p='+periodo+'&s1='+sector1+'&s2='+sector2;
                        }                                                                                        
                    </script>
                    <?php } else { ?>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -5px; margin-left:5px">
                                <label for="periodo" class="col-sm-1 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Periodo:</label>
                                <div class="form-group form-inline  col-md-3 col-lg-3">                                    
                                    <?php $tr = $con->Listar("SELECT p.* FROM gp_periodo p 
                                                WHERE p.id_unico = ".$_GET['p']);
                                    echo '<input type="hidden" name="periodo" id="periodo" value="'.$_GET['p'].'">';
                                    echo '<label for="periodo" class="control-label text-left" style="text-align: left; font-weight: normal">';
                                    echo  ucwords(mb_strtolower($tr[0][1].' '.$tr[0]['descripcion'])).'</label>';
                                    ?>
                                </div>
                                <label for="sector1" class="col-sm-1 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Sector Inicial:</label>
                                <div class="form-group form-inline  col-md-2 col-lg-2">
                                    <?php $tr = $con->Listar("SELECT * FROM gp_sector  
                                                WHERE id_unico = ".$_GET['s1']);
                                    echo '<input type="hidden" name="sector1" id="sector1" value="'.$_GET['s1'].'">';
                                    echo '<label for="sector1" class="control-label text-left" style="text-align: left; font-weight: normal">';
                                    echo  $tr[0][2].' - '.ucwords(mb_strtolower($tr[0][1])).'</label>';
                                    ?>
                                </div>
                                <label for="sector2" class="col-sm-1 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Sector Final:</label>
                                <div class="form-group form-inline  col-md-2 col-lg-2">                                    
                                    <?php $tr = $con->Listar("SELECT * FROM gp_sector  
                                                WHERE id_unico = ".$_GET['s2']);
                                    echo '<input type="hidden" name="sector2" id="sector2" value="'.$_GET['s2'].'">';
                                    echo '<label for="sector2" class="control-label text-left" style="text-align: left; font-weight: normal">';
                                    echo  $tr[0][2].' - '.ucwords(mb_strtolower($tr[0][1])).'</label>';
                                    ?>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-top: 10px; margin-left: 15px">
                                    <button onclick="location.href='GP_LISTADO_FACTURAS_SERVICIOS.php'" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-top: 10px; margin-left: 15px">
                                    <button onclick="javaScript:imprimir()" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Genera Informe"><i class="glyphicon glyphicon-print" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </form>
                        <script>
                            function imprimir(){
                                window.open('informes_servicios/INF_FACTURAS.php?p='+$("#periodo").val()+
                                '&s1='+$("#sector1").val()+'&s2='+$("#sector2").val());   
                                
                            }
                        </script>
                    </div>
                    <br/>
                    <div class="form-group" style="margin-top: -15px">
                        <div class="table-responsive" style="margin-left: 0px; margin-right: 0px;margin-top:0px;">
                            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <td class="oculto">Identificador</td>
                                            <td width="7%"></td>
                                            <td class="cabeza"><strong>Sector</strong></td>
                                            <td class="cabeza"><strong>Factura</strong></td>
                                            <td class="cabeza"><strong>Predio</strong></td>
                                            <td class="cabeza"><strong>Tercero</strong></td>
                                            <td class="cabeza"><strong>Total</strong></td>
                                            <td class="cabeza"><strong>Lectura</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="oculto">Identificador</th>
                                            <th width="7%"></th>
                                            <th>Sector</th>
                                            <th>Factura</th>
                                            <th>Predio</th>
                                            <th>Tercero</th>
                                            <th>Total</th>
                                            <th>Lectura</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $row = $con->Listar("SELECT f.id_unico, 
                                            s.nombre, s.codigo, p.codigo_catastral, 
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
                                            CONCAT_WS(' - ',p.codigo_catastral,p.nombre), f.numero_factura, uvms.id_unico, 
                                            DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.periodo , 
                                            f.fecha_factura, 
                                            uvs.id_unico 
                                            FROM gp_factura f
                                            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
                                            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                                            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
                                            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                                            LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
                                            LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
                                            WHERE s.id_unico BETWEEN ".$_GET['s1']." AND ".$_GET['s2']." 
                                            AND f.periodo =".$_GET['p']." ORDER BY f.numero_factura ASC ");
                                        $periodoa   = periodoA($_GET['p']);
                                        for ($i = 0; $i < count($row); $i++) { 
                                            $factura = $row[$i][0];
                                            $fecha_f = $row[$i][9];
                                            $periodo = $row[$i][10];
                                            $id_uvms = $row[$i][8];
                                            $fecha_fa= $row[$i][11];
                                            $uvs     = $row[$i][12];
                                            echo '<tr><td style="display: none;"></td>';
                                            echo '<td class="campos text-center">';
                                            echo '<a onclick="javaScript:imprimirF('.$factura.','.$periodo.')" title="Imprimir Factura"><li class="glyphicon glyphicon-print"></li></a>';
                                            if ($r[0]=='900849655'){ 
                                            echo '<a onclick="javaScript:consultarF('.$factura.')" title="Ver Factura"><li class="glyphicon glyphicon-eye-open"></li></a>';
                                            }
                                            #** Buscar Si La factura tiene recaudo **#
                                            $rowrf = $con->Listar("SELECT DISTINCT COUNT(dp.pago)
                                                FROM gp_factura f  
                                                LEFT JOIN gp_detalle_factura df ON f.id_unico = df.factura 
                                                LEFT JOIN gp_detalle_pago dp ON df.id_unico = dp.detalle_factura 
                                                WHERE f.id_unico = $factura");
                                            #** Buscar Lectura **#
                                            $l = $con->Listar("SELECT id_unico, valor FROM gp_lectura 
                                                WHERE unidad_vivienda_medidor_servicio = $id_uvms 
                                                    AND periodo = $periodo");
                                            $lectura = $l[0][0];
                                            IF($rowrf[0][0]>0){
                                            } else {
                                                echo '<a onclick="javaScript:cambiarL('.$factura.','."'".$fecha_f."'".','.$periodo.','.$lectura.','.$l[0][1].')" title="Cambiar Lectura"><li class="glyphicon glyphicon-refresh"></li></a>';
                                                echo '<a onclick="javaScript:generarDN('.$factura.','."'".$fecha_f."'".','.$periodo.','.$lectura.')" title="Ver Factura"><li class="glyphicon glyphicon-retweet"></li></a>';
                                            }   
                                            echo '</td>';
                                            echo '<td class="campos text-left">'.$row[$i][2].' - '.ucwords(mb_strtolower($row[$i][1])).'</td>';                  
                                            echo '<td class="campos text-right">'.$row[$i][7].'</td>';
                                            echo '<td class="campos text-left">'.$row[$i][6].'</td>';                   
                                            echo '<td class="campos text-left">'.ucwords(mb_strtolower($row[$i][4])).' - '.$row[$i][5].'</td>';  
                                            #** Buscar Valor Factura **#
                                            $vf = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_detalle_factura WHERE factura = $factura");
                                            if(empty($vf[0][0])){
                                                $valor =0;
                                            } else {
                                                $valor =$vf[0][0];
                                            }
                                            
                                            #********* Buscar Unidad¿_v con otros medidores ********#
                                            $ids_uv = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gp_unidad_vivienda_medidor_servicio 
                                                    WHERE unidad_vivienda_servicio = $uvs");
                                            $ids_uv = $ids_uv[0][0];
                                            #********* Buscar Si existe deuda anterior **********#
                                            $deuda_anterior = 0;
                                            $da = $con->Listar("SELECT GROUP_CONCAT(df.id_unico), SUM(df.valor_total_ajustado) 
                                                FROM gp_detalle_factura df 
                                                LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                                                WHERE f.unidad_vivienda_servicio IN($ids_uv) AND f.periodo <= $periodoa");
                                            if(count($da)>0){
                                                #*** Buscar Recaudo ***#
                                                $id_df      = $da[0][0];
                                                $valor_f    = $da[0][1];
                                                $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                                                    LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                                                    WHERE p.fecha_pago <= '$fecha_fa' AND dp.detalle_factura IN ($id_df)");
                                                
                                                if(count(($rc))<=0){
                                                    $recaudo = 0;
                                                }elseif(empty ($rc[0][0])){
                                                    $recaudo = 0;
                                                } else {
                                                    $recaudo = $rc[0][0];
                                                }
                                                $deuda_anterior = $valor_f -$recaudo;
                                            }
                                            $valor = $valor+$deuda_anterior;
                                            echo '<td class="campos text-right">'. number_format($valor, 2, '.', ',').'</td>';
                                            echo '<td class="campos text-right">'. $l[0][1].'</td>';                                            
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div> 
                    </div>
                    <script>
                        function consultarF(factura){
                            var form_data = {
                                action:2,
                                factura:factura
                            };
                            $.ajax({
                                type:'POST',
                                url:'jsonPptal/gf_facturaJson.php',
                                data:form_data,
                                success: function(data){
                                    console.log(data);
                                    data = data.replace('registrar_GF_FACTURA', 'GP_FACTURA');
                                    window.open(data);
                                }
                            });
                        }
                        function generarDN(factura, fecha, periodo, lectura ){
                            jsShowWindowLoad('Eliminando...');
                            var form_data = {
                                action:4,
                                factura:factura
                            };
                            $.ajax({
                                type:'POST',
                                url:'jsonServicios/gp_facturacionServiciosJson.php',
                                data:form_data,
                                success: function(data){
                                    jsRemoveWindowLoad();
                                    console.log(data);
                                    var rta =data;
                                    if(rta ==0){
                                        jsShowWindowLoad('Generando Factura...');
                                        var form_data = {
                                            action:1,
                                            fechaF:fecha, 
                                            periodo:periodo,
                                            lecturas:lectura, 
                                            dn:1
                                        };
                                        $.ajax({
                                            type:'POST',
                                            url:'jsonServicios/gp_facturacionServiciosJson.php',
                                            data:form_data,
                                            success: function(data){
                                                console.log(data+'G');
                                                jsRemoveWindowLoad();
                                                if(rta ==0){
                                                    $("#mensaje").html('Información Guardada Correctamente');
                                                    $("#modalMensajes").modal("show");
                                                    $("#Aceptar").click(function(){
                                                        window.location.reload();
                                                    })
                                                } else {
                                                    $("#mensaje").html('No Se Ha Podido Guardar Información');
                                                    $("#modalMensajes").modal("show");
                                                    $("#Aceptar").click(function(){
                                                        $("#modalMensajes").modal("hide");
                                                    })
                                                }
                                            }
                                        })
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Eliminar Factura');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })
                                    }
                                }
                            });
                        }
                    </script>
                    <br/>
                    <?php }?>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensaje" name="mensaje"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalCambiarL" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Cambiar Lectura</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label style="display:inline-block; width:140px"><strong class="obligado">*</strong>Lectura:</label>
                        <input style="display:inline-block; width:250px;"  name="valorl" id="valorl" class="form-control" title="Valor" required="required">
                        <br/>
                        <br/>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnAceptarL" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" id="btnCancelarL" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
            $("#periodo").select2();
            $("#sector1").select2();
            $("#sector2").select2();
            $("#buscarR").select2();
            function imprimirF(factura, periodo){
                window.open('informes_servicios/INF_FACTURAS_LOTE.php?factura='+factura+'&periodo='+periodo);
            }
            
        </script>
        <script>
        function cambiarL(factura, fecha, periodo, lectura, valor){
            $("#valorl").val(valor);
            $("#modalCambiarL").modal('show');
            $("#btnAceptarL").click(function(){
                $("#modalCambiarL").modal('hide');
                jsShowWindowLoad('Modificando...');
                var form_data = {
                    action:4,
                    lectura:lectura,
                    valor :$("#valorl").val(),
                };
                $.ajax({
                    type:'POST',
                    url:'jsonServicios/gp_LecturaJson.php',
                    data:form_data,
                    success: function(data){
                        jsRemoveWindowLoad();
                        var rta = data;
                        if(rta != 0){
                            generarDN(factura, fecha, periodo, lectura );
                        } else {
                            $("#mensaje").html('No Se Ha Podido Modificar Información');
                            $("#modalMensaje").modal("show");
                            $("#btnMsj").click(function(){
                                $("#modalMensajes").modal("hide");
                            })
                        }
                    }
                })
            });
            $("#btnCancelarL").click(function(){
                $("#modalCambiarL").modal('hide');
            })
                
        }
    </script>
    </body>
</html>
