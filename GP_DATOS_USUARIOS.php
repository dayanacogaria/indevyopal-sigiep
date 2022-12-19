<?php
require_once('head_listar.php');
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require './jsonPptal/funcionesPptal.php';
$anno   = $_SESSION['anno'];
$con    = new ConexionPDO();
$id     = $_GET['id'];
$t1     = '';
$t2     = '';
$t3     = '';
$rowdp  = $con->Listar("SELECT p.codigo_catastral, 
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
                 uvs.id_unico, t.id_unico 
            FROM  gp_unidad_vivienda_medidor_servicio uvms 
            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
            LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
            LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
            WHERE uvms.id_unico =$id");
$t2 = $rowdp[0][0].' - '. ucwords(mb_strtolower($rowdp[0][1]));
$ids = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gp_unidad_vivienda_medidor_servicio 
        WHERE unidad_vivienda_servicio = ".$rowdp[0][2]);
$ids = $ids[0][0];
if($_GET['t']==1){
    $t1 = 'Facturas Usuario';
    if(!empty($_GET['a'])){
       $row = $con->Listar("SELECT f.id_unico,  
        f.numero_factura, uvms.id_unico, 
        DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.periodo , 
        f.fecha_factura, 
        pr.nombre, DATE_FORMAT(pr.fecha_inicial, '%d/%m/%Y'), 
        DATE_FORMAT(pr.fecha_final, '%d/%m/%Y')
        FROM gp_factura f
        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
        LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
        LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
        LEFT JOIN gp_periodo pr ON f.periodo = pr.id_unico 
        WHERE uvms.id_unico IN ($ids) AND f.parametrizacionanno < $anno ORDER BY f.numero_factura DESC "); 
    } else { 
        $row = $con->Listar("SELECT f.id_unico,  
        f.numero_factura, uvms.id_unico, 
        DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.periodo , 
        f.fecha_factura, 
        pr.nombre, DATE_FORMAT(pr.fecha_inicial, '%d/%m/%Y'), 
        DATE_FORMAT(pr.fecha_final, '%d/%m/%Y')
        FROM gp_factura f
        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
        LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
        LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
        LEFT JOIN gp_periodo pr ON f.periodo = pr.id_unico 
        WHERE uvms.id_unico IN ($ids) AND f.parametrizacionanno = $anno ORDER BY f.numero_factura DESC ");
    } 
} elseif($_GET['t']==2){
    $t1 = 'Otros Conceptos Usuario';
    
    if(!empty($_GET['a'])){
        $row = $con->Listar("SELECT o.id_unico, 
                c.nombre, o.total_cuotas, o.valor_cuota, 
                o.cuotas_pagas, o.cuotas_pendientes, 
                DATE_FORMAT(o.fecha, '%d/%m/%Y'), 
                c.tipo_operacion, uvs.id_unico, o.reestructurado
            FROM gf_otros_conceptos o 
            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON o.unidad_vivienda_ms = uvms.id_unico 
            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
            LEFT JOIN gp_predio1 pr ON uv.predio = pr.id_unico 
            LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
            LEFT JOIN gp_concepto c ON o.concepto = c.id_unico 
            WHERE o.unidad_vivienda_ms IN ($ids) AND o.cuotas_pendientes <=0 ORDER BY o.id_unico DESC");
    } else {
        $row = $con->Listar("SELECT o.id_unico, 
            c.nombre, o.total_cuotas, o.valor_cuota, 
            o.cuotas_pagas, o.cuotas_pendientes, 
            DATE_FORMAT(o.fecha, '%d/%m/%Y'), 
            c.tipo_operacion , uvs.id_unico , o.reestructurado
        FROM gf_otros_conceptos o 
        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON o.unidad_vivienda_ms = uvms.id_unico 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_predio1 pr ON uv.predio = pr.id_unico 
        LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
        LEFT JOIN gp_concepto c ON o.concepto = c.id_unico 
        WHERE o.unidad_vivienda_ms IN ($ids) AND o.cuotas_pendientes >0 ORDER BY o.id_unico DESC");
    }
}
?>
<head>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script> 
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script src="js/md5.pack.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
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
            
        });
    </script>
</head>
<title>Datos Usuarios</title>
<body>
    <div class="container-fluid text-center">
        <div class="row content">    
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"><?php echo $t1;?></h2>
                <a href="GP_USUARIOS.php?id=<?php echo $_GET['id'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo $t2;?></h5>
                <input type="hidden" id="id" name="id" value="<?php echo $_GET['id'];?>">
                <input type="hidden" id="tercero" name="tercero" value="<?php echo $rowdp[0][3];?>">                
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <?php  
                            if($_GET['t']==1){
                                echo '<thead>';
                                echo '<tr>';
                                echo '<td style="display: none;">Identificador</td>';
                                echo '<td width="30px" align="center"></td>';
                                echo '<td><strong>Periodo</strong></td>';
                                echo '<td><strong>Fecha Factura</strong></td>';
                                echo '<td><strong>Número Factura</strong></td>';
                                echo '<td><strong>Valor Factura</strong></td>';
                                echo '<td><strong>Lectura</strong></td>';
                                echo '</tr>';
                                echo '<tr>';
                                echo '<th style="display: none;">Identificador</th>';
                                echo '<th width="7%"></th>';
                                echo '<th>Periodo</th>';
                                echo '<th>Fecha Factura</th>';
                                echo '<th>Número Factura</th>';
                                echo '<th>Valor Factura</th>';
                                echo '<th>Lectura</th>';
                                echo '</tr>';
                                echo '</thead>';
                                echo '<tbody>';
                                for ($f = 0; $f < count($row); $f++) {
                                    $factura = $row[$f][0];
                                    $id_uvms = $row[$f][2];
                                    $periodo = $row[$f][4];
                                    $fecha_fa= $row[$f][5];
                                    $fecha_f = $row[$f][3];
                                    echo '<tr><td style="display: none;"></td>';
                                    echo '<td class="campos text-center">';
                                    if ($r[0]=='900849655'){ 
                                        echo '<a onclick="javaScript:verF('.$factura.')" title="Ver Factura"><li class="glyphicon glyphicon-eye-open"></li></a>';
                                    }
                                    echo '<a onclick="javaScript:imprimirF('.$factura.','.$periodo.')" title="Imprimir Factura"><li class="glyphicon glyphicon-print"></li></a>';
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
                                        if(empty($_GET['a'])) {
                                            echo '<a onclick="javaScript:cambiarL('.$factura.','."'".$fecha_f."'".','.$periodo.','.$lectura.','.$l[0][1].')" title="Cambiar Lectura"><li class="glyphicon glyphicon-refresh"></li></a>';
                                            echo '<a onclick="javaScript:generarDN('.$factura.','."'".$fecha_f."'".','.$periodo.','.$lectura.')" title="Reliquidar Factura"><li class="glyphicon glyphicon-retweet"></li></a>';
                                        }
                                         
                                    }   
                                    echo '</td>';
                                    echo '<td class="campos text-left">';
                                    echo $row[$f][6].' ('.$row[$f][7].' - '.$row[$f][8].')';
                                    echo '</td>';
                                    echo '<td class="campos text-left">';
                                    echo $row[$f][3];
                                    echo '</td>';
                                    echo '<td class="campos text-left">';
                                    echo $row[$f][1];
                                    echo '</td>';
                                    #** Buscar Valor Factura **#
                                    $vf = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_detalle_factura WHERE factura = $factura");
                                    if(empty($vf[0][0])){
                                        $valor =0;
                                    } else {
                                        $valor =$vf[0][0];
                                    }
                                    #********* Buscar Si existe deuda anterior **********#
                                    $deuda_anterior = 0;
                                    $da = $con->Listar("SELECT GROUP_CONCAT(df.id_unico), SUM(df.valor_total_ajustado) 
                                        FROM gp_detalle_factura df 
                                        LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                                        WHERE f.unidad_vivienda_servicio IN ($ids)
                                        AND f.periodo < $periodo");
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
                                    echo '<td class="campos text-right">';
                                    echo number_format($valor, 2, '.', ',');
                                    echo '</td>';
                                    echo '<td class="campos text-right">'.$l[0][1].'</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody>';
                            } elseif($_GET['t']==2){
                                echo '<thead>';
                                echo '<tr>';
                                echo '<td style="display: none;">Identificador</td>';
                                echo '<td width="30px" align="center"></td>';
                                echo '<td><strong>Concepto</strong></td>';
                                echo '<td><strong>Total Cuotas</strong></td>';
                                echo '<td><strong>Valor Cuota</strong></td>';
                                echo '<td><strong>Valor Total</strong></td>';
                                echo '<td><strong>Cuotas Pagas</strong></td>';
                                echo '<td><strong>Cuotas Pendientes</strong></td>';
                                echo '<td><strong>Fecha</strong></td>';
                                echo '<td><strong>Ver Facturas</strong></td>';
                                echo '</tr>';
                                echo '<tr>';
                                echo '<th style="display: none;">Identificador</th>';
                                echo '<th width="7%"></th>';
                                echo '<th>Concepto</th>';
                                echo '<th>Total Cuotas</th>';
                                echo '<th>Valor Cuota</th>';
                                echo '<th>Valor Total</th>';
                                echo '<th>Cuotas Pagas</th>';
                                echo '<th>Cuotas Pendientes</th>';
                                echo '<th>Fecha</th>';
                                echo '<th>Ver Facturas</th>';
                                echo '</tr>';
                                echo '</thead>';
                                echo '<tbody>';
                                for ($c = 0; $c < count($row); $c++) {
                                    echo '<tr><td style="display: none;"></td>';
                                    echo '<td class="campos text-center">';
                                    if(empty($_GET['a'])){
                                        if($row[$c][7]!=4){
                                            echo '<a onclick="javaScript:abrirmodificar('.$row[$c][0].')" title="Modificar Cuota"><li class="glyphicon glyphicon-pencil"></li></a>';
                                        } else {
                                            echo '<a onclick="javaScript:reestructurar('.$row[$c][0].','.$row[$c][7].')" title="Ajustar Cuotas"><li class="glyphicon glyphicon-refresh"></li></a>';
                                        }
                                        if($row[$c][9]==1){}else {
                                        echo '<a onclick="javaScript:eliminarc('.$row[$c][0].','.$row[$c][7].')" title="Eliminar Cuota"><li class="glyphicon glyphicon-trash"></li></a>';
                                        }
                                        echo '<a onclick="javaScript:abonar('.$row[$c][0].','.$row[$c][7].')" title="Abonar Cuota"><li class="glyphicon glyphicon-usd"></li></a>';
                                    }
                                    echo '</td>';
                                    echo '<td class="campos text-left">';
                                    echo ucwords(mb_strtolower($row[$c][1]));
                                    echo '</td>';
                                    echo '<td class="campos text-left">'.$row[$c][2].'</td>';
                                    echo '<td class="campos text-right">';
                                    echo number_format($row[$c][3], 2, '.', ',');
                                    echo '</td>';
                                    echo '<td class="campos text-right">';
                                    echo number_format($row[$c][3]*$row[$c][2], 2, '.', ',');
                                    echo '</td>';
                                    echo '<td class="campos text-left">'.$row[$c][4].'</td>';
                                    echo '<td class="campos text-left">'.$row[$c][5].'</td>';
                                    echo '<td class="campos text-left">'.$row[$c][6].'</td>';
                                    echo '<td class="campos text-left"><a onclick="javaScript:VerFs('.$row[$c][8].','.$row[$c][0].')" title="Ver Facturas"><li class="glyphicon glyphicon-eye-open"></li></td>';
                                }
                                echo '</tbody>'; 
                            } 
                            ?>
                        </table>
                    </div>
                </div>
                <?php 
                if($_GET['t']==1){ 
                    if(empty($_GET['a'])) { 
                        echo '<div align="right"><a href="GP_DATOS_USUARIOS.php?t=1&id='.$_GET['id'].'&a=1" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Facturas años anteriores</a> </div>';
                    } else {
                        echo '<div align="right"><a href="GP_DATOS_USUARIOS.php?t=1&id='.$_GET['id'].'" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Facturas año actual</a> </div>';
                    }
                }elseif($_GET['t']==2){ 
                    echo '<div align="right">
                        <button onclick="registrarConcepto()" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-right:20px"><li class="glyphicon glyphicon-plus"></li>Nuevo</button>';
                    if(empty($_GET['a'])) { 
                        echo '<a href="GP_DATOS_USUARIOS.php?t=2&id='.$_GET['id'].'&a=1" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Conceptos cancelados</a> </div>';
                    } else {
                        echo '<a href="GP_DATOS_USUARIOS.php?t=2&id='.$_GET['id'].'" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Conceptos vigentes</a>';
                    }
                    echo '</div>';
                }?>
            </div>
        </div>
    </div>
    <?php require './footer.php'; ?>
    <script src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    
    <div class="modal fade" id="mdlConcepto" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" name="mdlcontenido" id="mdlcontenido">
                
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalMensaje" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnMsj" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalMensaje2" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje2" name="mensaje2"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnMsj2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnMsjC2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function verF(factura){
            var form_data = {
                action:2,
                factura:factura
            };
            $.ajax({
                type:'POST',
                url:'jsonPptal/gf_facturaJson.php',
                data:form_data,
                success: function(data){
                    data = data.replace("registrar_GF_FACTURA", "GP_FACTURA");
                    window.open(data);
                }
            });
        }
        function imprimirF(factura, periodo){
             window.open('informes_servicios/INF_FACTURAS_LOTE.php?factura='+factura+'&periodo='+periodo);
        }
    </script>
    <script>
        function registrarConcepto(){
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gp_usuariosJson.php?action=9",
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    $("#mdlcontenido").html(response);
                    var id = $("#id").val();
                    $("#iduvms").val(id);
                    $("#sltConcepto").select2();
                    $("#txtfecha").datepicker({changeMonth: true,}).val();
                    $("#mdlConcepto").modal('show');
                }
            });
            
        }
        function guardarConcepto(){
            var formData = new FormData($("#formConcepto")[0]);  
            jsShowWindowLoad('Guardando Información...');
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gp_usuariosJson.php?action=7",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response+'GC');
                    $("#mdlConcepto").modal('hide');                    
                    if(response !=0){
                        $("#mensaje").html('Información Guardada Correctamente');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                            document.location.reload();
                        })
                    } else {
                        $("#mensaje").html('No se ha podido guardar información');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                        })
                    }
                }
            })           
        }
        function abrirmodificar(id_c){
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gp_usuariosJson.php?action=10&id_c="+id_c,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    $("#mdlcontenido").html(response);
                    $("#sltConcepto").select2();
                    $("#txtfecha").datepicker({changeMonth: true,}).val();
                    $("#mdlConcepto").modal('show');
                }
            });
        }
        function modificarConcepto(){
            var formData = new FormData($("#formConcepto")[0]);  
            jsShowWindowLoad('Modificando Información...');
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gp_usuariosJson.php?action=8",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response+'GC');
                    $("#mdlConcepto").modal('hide');                    
                    if(response !=0){
                        $("#mensaje").html('Información Modificada Correctamente');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                            document.location.reload();
                        })
                    } else {
                        $("#mensaje").html('No se ha podido modificar información');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                        })
                    }
                }
            })   
        }
        function eliminarc(id, tipoo){
            if(tipoo==4){
                $("#mensaje2").html('Este concepto corresponde a una financiación, si lo elimina, el usuario quedará con deuda nuevamente<br/>¿Desea Eliminarlo?');
                $("#modalMensaje2").modal('show');
                $("#btnMsj2").click(function(){
                    var form_data = {action:12,id:id};
                    jsShowWindowLoad('Comprobando Información...');
                    $.ajax({
                        type: 'POST',
                        url: "jsonServicios/gp_usuariosJson.php",
                        data: form_data,
                        success: function(response)
                        { 
                            jsRemoveWindowLoad();
                            console.log(response);
                            if(response==1){
                                var form_data = {action:13,id:id};
                                jsShowWindowLoad('Eliminando Información...');
                                $.ajax({
                                    type: 'POST',
                                    url: "jsonServicios/gp_usuariosJson.php",
                                    data: form_data,
                                    success: function(response)
                                    {
                                        jsRemoveWindowLoad();
                                        console.log(response);
                                        if(response ==true){
                                            $("#mensaje").html('Información Eliminada Correctamente');
                                            $("#modalMensaje").modal('show');
                                            $("#btnMsj").click(function(){
                                                $("#modalMensaje").modal('hide');
                                                document.location.reload();
                                            })
                                        } else {
                                            $("#mensaje").html('No se ha podido eliminar información');
                                            $("#modalMensaje").modal('show');
                                            $("#btnMsj").click(function(){
                                                $("#modalMensaje").modal('hide');
                                            })
                                        }
                                    }
                                });
                            } else {
                                $("#mensaje").html('El concepto no se puede eliminar, ya hay una factura con fecha superior');
                                $("#modalMensaje").modal('show');
                                $("#btnMsj").click(function(){
                                    $("#modalMensaje").modal('hide');
                                    document.location.reload();
                                })
                            }
                        }
                    })
                })                
            } else {
                $("#mensaje2").html('¿Desea eliminar el registro seleccionado?');
                $("#modalMensaje2").modal('show');
                $("#btnMsj2").click(function(){
                    var form_data = {action:11,id:id};
                    jsShowWindowLoad('Eliminando Información...');
                    $.ajax({
                    type: 'POST',
                    url: "jsonServicios/gp_usuariosJson.php",
                    data: form_data,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        console.log(response+'GC');
                        if(response ==true){
                            $("#mensaje").html('Información Eliminada Correctamente');
                            $("#modalMensaje").modal('show');
                            $("#btnMsj").click(function(){
                                $("#modalMensaje").modal('hide');
                                document.location.reload();
                            })
                        } else {
                            $("#mensaje").html('No se ha podido eliminar información');
                            $("#modalMensaje").modal('show');
                            $("#btnMsj").click(function(){
                                $("#modalMensaje").modal('hide');
                            })
                        }
                    }
                })   
                });
            }
            $("#btnMsjC2").click(function(){
                $("#modalMensaje2").modal('hide');
            })
        }
        </script>
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
    <script>
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
                    var rta = data;
                    if(rta == 0){
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
                                if(rta ==0 ){
                                    $("#mensaje").html('Información Guardada Correctamente');
                                    $("#modalMensaje").modal("show");
                                    $("#btnMsj").click(function(){
                                        window.location.reload();
                                    })
                                } else {
                                    $("#mensaje").html('No Se Ha Podido Guardar Información');
                                    $("#modalMensaje").modal("show");
                                    $("#btnMsj").click(function(){
                                        $("#modalMensajes").modal("hide");
                                    })
                                }
                            }
                        })
                    } else {
                        $("#mensaje").html('No Se Ha Podido Eliminar Factura');
                        $("#modalMensaje").modal("show");
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal("hide");
                        })
                    }
                }
            });
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
    <script>
        function reestructurar(id, tipoo){
            let form_data ={
                action :15,
                id : id
            }
            $.ajax({
                type:'POST',
                url:'jsonServicios/gp_usuariosJson.php',
                data:form_data,
                success: function(data){
                    //console.log('Acaaaa'+data);//
                    $("#mdlcontenido").html(data);
                    $("#id_concepto").val(id);
                    $("#sltConcepto").select2();
                    $("#txtfecha").datepicker({changeMonth: true,}).val();
                    $("#mdlConcepto").modal('show');
                }
            });
        }
    </script>
    <script>
        function cuotas(){
            let totalc = $("#txttotalcR").val();
            let valorc = $("#txtvalorcuotaR").val();
            let valorm = $("#valor_m").val();
            let valorr = $("#valor_r").val();
            if(valorc!='' && totalc!=''){
                let tf = parseFloat(totalc)*parseFloat(valorc);
                if(valorm > tf){
                    $("#mensaje").html('No Se Puede Reestructurar Un Valor Menor al Saldo del Concepto');
                    $("#txttotalcR").val('');
                    $("#txtvalorcuotaR").val('');
                    $("#modalMensaje").modal("show");
                    $("#btnMsj").click(function(){
                        $("#modalMensajes").modal("hide");
                    })
                } else {
                    if(tf>valorr){
                        $("#mensaje").html('El Valor Excede el valor total de la deuda');
                        $("#txttotalcR").val('');
                        $("#txtvalorcuotaR").val('');
                        $("#modalMensaje").modal("show");
                        $("#btnMsj").click(function(){
                            $("#modalMensajes").modal("hide");
                        })
                    }
                }
            }
        }
        function valorcuotas(){
            let totalc = $("#txttotalcR").val();
            let valorc = $("#txtvalorcuotaR").val();
            let valorm = $("#valor_m").val();
            let valorr = $("#valor_r").val();
            if(valorc!='' && totalc!=''){
                let tf = parseFloat(totalc)*parseFloat(valorc);
                if(valorm > tf){
                    $("#mensaje").html('No Se Puede Reestructurar Un Valor Menor al Saldo del Concepto');
                    $("#txttotalcR").val('');
                    $("#txtvalorcuotaR").val('');
                    $("#modalMensaje").modal("show");
                    $("#btnMsj").click(function(){
                        $("#modalMensajes").modal("hide");
                    })
                } else {
                    if(tf>valorr){
                        $("#mensaje").html('El Valor Excede el valor total de la deuda');
                        $("#txttotalcR").val('');
                        $("#txtvalorcuotaR").val('');
                        $("#modalMensaje").modal("show");
                        $("#btnMsj").click(function(){
                            $("#modalMensajes").modal("hide");
                        })
                    }
                }
            }
        }
    </script>
    <script>
        function ReestructurarConcepto(){
            var formData = new FormData($("#formResConcepto")[0]);  
            jsShowWindowLoad('Guardando Información...');
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gp_usuariosJson.php?action=16",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response+'GRC');
                    $("#mdlConcepto").modal('hide');                    
                    if(response !=0){
                        $("#mensaje").html('Información Guardada Correctamente');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                            document.location.reload();
                        })
                    } else {
                        $("#mensaje").html('No se ha podido guardar información');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                        })
                    }
                }
            })           
        }
        function VerFs(id_uvs, id_cuota){
            let form_data ={
                action :17,
                id_uvs : id_uvs, 
                id_cuota:id_cuota
            }
            $.ajax({
                type:'POST',
                url:'jsonServicios/gp_usuariosJson.php',
                data:form_data,
                success: function(data){
                    //console.log('Acaaaa'+data);//
                    $("#mdlcontenido").html(data);
                    $("#mdlConcepto").modal('show');
                }
            });
        }
        
        function abonar(id, tipoo){
            let form_data ={
                action :18,
                id : id, 
            }
            $.ajax({
                type:'POST',
                url:'jsonServicios/gp_usuariosJson.php',
                data:form_data,
                success: function(data){
                    //console.log('Acaaaa'+data);//
                    $("#mdlcontenido").html(data);
                    $("#sltBanco").select2();
                    $("#sltTipoP").select2();
                    $("#txtfecha").datepicker({changeMonth: true,}).val();
                    $("#mdlConcepto").modal('show');
                }
            });
        }
        function AbonarConcepto(){
            var formData = new FormData($("#formAbnConcepto")[0]);  
            jsShowWindowLoad('Guardando Información...');
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gp_usuariosJson.php?action=19&tercero="+$("#tercero").val(),
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response+'GRC');
                    $("#mdlConcepto").modal('hide');                    
                    if(response !=0){
                        $("#mensaje").html('Información Guardada Correctamente');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                            document.location.reload();
                        })
                    } else {
                        $("#mensaje").html('No se ha podido guardar información');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                        })
                    }
                }
            })     
        }
    </script>
    
</body>
</html>


