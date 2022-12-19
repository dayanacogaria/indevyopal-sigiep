<?php
require_once './Conexion/ConexionPDO.php';
require_once './jsonPptal/funcionesPptal.php';
require_once './head_listar.php';
$con = new ConexionPDO();
#**********Buscar Si Hay Comprobante O No*************#
$annio      = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
#Clase Contable = 20
$cs = $con->Listar("SELECT cn.id_unico, 
                        DATE_FORMAT(cn.fecha, '%d/%m/%Y'),
                        tc.id_unico , CONCAT(UPPER(tc.sigla),' - ', LOWER(tc.nombre)), 
                        cn.numero , 
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
                        CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) ,
                        cn.descripcion 
                    FROM gf_comprobante_cnt cn  
                    LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                    LEFT JOIN gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                    LEFT JOIN gf_tercero t ON t.id_unico =cn.tercero 
                    WHERE cc.id_unico = 20 AND cn.parametrizacionanno =$annio");
?>
<title>Cierre Contable</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="js/md5.js"></script>
</head>
<body>
    <div class="container-fluid text-left">
        <div class="row content">
            <?php require_once './menu.php'; ?>
            <div class="col-sm-10 text-center" style="margin-top:-22px;">
                <h2 class="tituloform" align="center">Cierre Contable</h2>
                <div class="client-form contenedorForma" style="margin-top:-7px;">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="#" style="margin-bottom:-10px">
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>  
                        <?php 
                        #*****Si Hay Comprobante***#
                        if(count($cs)>0) { ?>
                        <div class="form-group form-inline" style="margin-top: 5px; margin-left: 0px;">
                            <?php 
                            #Tipo
                            echo '<label class="col-sm-2 control-label"><strong class="obligado">*</strong>Tipo Comprobante:</label>';
                            echo '<label class="col-sm-2 control-label" style="font-weight:normal;text-align:left">'.ucwords($cs[0][3]).'</label>'; 
                            #Numero
                            echo '<label class="col-sm-2 control-label"><strong class="obligado">*</strong>Número:</label>';
                            echo '<label class="col-sm-1 control-label" style="font-weight:normal;text-align:left">'.$cs[0][4].'</label>'; 
                            #Fecha
                            echo '<label class="col-sm-2 control-label"><strong class="obligado">*</strong>Fecha:</label>';
                            echo '<label class="col-sm-1 control-label" style="font-weight:normal;text-align:left">'.$cs[0][1].'</label>'; 
                            ?>
                            <div class="col-sm-1">
                                <a class="btn sombra btn-primary" title="Imprimir" id="btnImprimir" onclick="return informepdf(<?php echo $cs[0][0];?>);">
                                    <li class="fa fa-file-pdf-o" ></li>
                                </a>
                            </div>  
                        </div>
                        <div class="form-group form-inline" style="margin-top: 5px; margin-left: 0px;">
                            <?php 
                            #Tercero
                            echo '<label class="col-sm-2 control-label"><strong class="obligado">*</strong>Tercero:</label>';
                            echo '<label class="col-sm-8 control-label" style="font-weight:normal;text-align:left">'.ucwords(mb_strtolower($cs[0][5])).' - '.$cs[0][6].'</label>'; 
                            ?>
                            <div class="col-sm-1" >
                                <a class="btn sombra btn-primary" title="Imprimir" id="btnImprimirExcel" onclick="return informeExcel(<?php echo $cs[0][0];?>);" >
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                </a> <!--Imprimir-->
                            </div>
                        </div>
                        <div class="form-group form-inline" style="margin-top: 5px; margin-left: 0px;">
                            <?php 
                            #Descripcion
                            echo '<label class="col-sm-2 control-label"><strong class="obligado">*</strong>Descripción:</label>';
                            echo '<label class="col-sm-8 control-label" style="font-weight:normal;text-align:left">'.($cs[0][7]).'</label>'; 
                            ?>
                            <!-----------Eliminar--------------->
                            <div class="col-sm-1" >
                                <a class="btn sombra btn-primary" title="Eliminar" id="btnEliminar" onclick="eliminar(<?php echo $cs[0][0];?>)" >
                                    <i class="glyphicon glyphicon-remove" aria-hidden="true"></i>
                                </a> 
                            </div>
                            <script>
                                function eliminar(id){
                                    $("#mensaje2").html("¿Desea Eliminar El Comprobante Seleccionado?");
                                    $("#mdlBtns").modal("show");
                                    $("#btnAceptar").click(function(){
                                        jsShowWindowLoad('Eliminando Comprobante..');
                                        var form_data ={action:2, id:id};
                                        $.ajax({
                                            type: "POST",
                                            url: "jsonPptal/gf_cierre_contableJson.php",
                                            data: form_data,
                                            success: function(response)
                                            { 
                                                console.log(response);
                                                jsRemoveWindowLoad();
                                                if(response==1){
                                                    $("#mensaje").html("Comprobante Eliminado Correctamente");
                                                    $("#mdlMensaje").modal("show");
                                                } else {
                                                    $("#mensaje").html("No Se Ha Podido Eliminar Comprobante");
                                                    $("#mdlMensaje").modal("show");
                                                }
                                                $("#btnOk").click(function(){
                                                    document.location.reload();
                                                })
                                            }
                                        })
                                    })
                                    $("#btnCancelar").click(function(){
                                        $("#mdlBtns").modal("hide");
                                    }) 
                                }
                            </script>
                            <script>
                                function informepdf(id){
                                    window.open('informes/inf_com_cierre.php?t=1&id='+md5(id));
                                } 
                                function informeExcel(id){
                                    window.open('informes/inf_com_cierre.php?t=2&id='+md5(id));
                                }
                            </script>
                            <!-----------Fin Eliminar--------------->
                        </div>
                        <?php 
                        #*****Si No Hay Comprobante***#
                        } else { ?>
                        <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;">
                            <?php 
                            #Tipo
                            $tipo = $con->Listar("SELECT tc.id_unico, 
                                                    CONCAT(UPPER(tc.sigla),' - ',LOWER(tc.nombre))
                                                FROM gf_tipo_comprobante tc 
                                                WHERE tc.clasecontable = 20 AND tc.compania = $compania");
                            echo '<input type="hidden" id="tipo" name ="tipo" value="'.$tipo[0][0].'"/>';
                            echo '<label class="col-sm-2 control-label"><strong class="obligado">*</strong>Tipo Comprobante:</label>';
                            echo '<label class="col-sm-2 control-label" style="font-weight:normal;text-align:left">'.ucwords($tipo[0][1]).'</label>'; 
                            #Numero
                            $num = anno($annio).'000001';
                            echo '<input type="hidden" id="num" name ="num" value="'.$num.'"/>';
                            echo '<label class="col-sm-2 control-label"><strong class="obligado">*</strong>Número:</label>';
                            echo '<label class="col-sm-1 control-label" style="font-weight:normal;text-align:left">'.$num.'</label>'; 
                            #Fecha
                            $fech = '31/12/'.anno($annio);
                            echo '<input type="hidden" id="fecha" name ="fecha" value="'.anno($annio).'-12-31'.'"/>';
                            echo '<label class="col-sm-2 control-label"><strong class="obligado">*</strong>Fecha:</label>';
                            echo '<label class="col-sm-1 control-label" style="font-weight:normal;text-align:left">'.$fech.'</label>'; 
                            ?>
                            <div class="form-group form-inline" style="margin-top: 5px; margin-left: -50px;">
                                <div class="col-sm-1">
                                    <a class="btn sombra btn-primary" title="Generar" id="btnGenerar">
                                        Generar Cierre
                                    </a>
                                </div>  
                            </div>  
                        </div>
                        <script>
                            $("#btnGenerar").click(function(){
                                jsShowWindowLoad('Generando Comprobante..');
                                var fecha = $("#fecha").val();
                                var tipo  = $("#tipo").val();
                                var num   = $("#num").val();
                                
                                var form_data ={action:3};
                                $.ajax({
                                    type: "POST",
                                    url: "jsonPptal/gf_cierre_contableJson.php",
                                    data: form_data,
                                    success: function(response)
                                    { 
                                        resultado = JSON.parse(response);
                                        var msj = resultado["msj"];
                                        var rta = resultado["rta"];
                                        if(rta==0){
                                            var form_data ={action:1, fecha:fecha, tipo:tipo,num:num};
                                            $.ajax({
                                                type: "POST",
                                                url: "jsonPptal/gf_cierre_contableJson.php",
                                                data: form_data,
                                                success: function(response)
                                                { 
                                                    console.log(response);
                                                    jsRemoveWindowLoad();
                                                    if(response==1){
                                                        $("#mensaje").html("Cierre Generado Correctamente");
                                                        $("#mdlMensaje").modal("show");
                                                    } else {
                                                        $("#mensaje").html("No Se Ha Podido Generar Cierre");
                                                        $("#mdlMensaje").modal("show");
                                                    }
                                                    $("#btnOk").click(function(){
                                                        document.location.reload();
                                                    })
                                                }

                                            });
                                        } else {
                                           jsRemoveWindowLoad();
                                           $("#mensaje").html(msj);
                                           $("#mdlMensaje").modal("show"); 
                                           $("#btnOk").click(function(){
                                                document.location.reload();
                                            })
                                        }
                                        
                                    }
                                    
                                });
                                
                                
                                
                                
                            })
                        </script>
                        <?php }?>
                    </form>
                </div>
            </div>
            <?php 
            #*****Si Hay Comprobante***#
            if(count($cs)>0) { 
                $id=$cs[0][0];
                $dt = $con->Listar("SELECT 
                                        dc.id_unico, 
                                        CONCAT(c.codi_cuenta,' - ', LOWER(c.nombre)), 
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
                                        CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) , 
                                        c.naturaleza, 
                                        dc.valor 
                                    FROM gf_detalle_comprobante dc 
                                    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                    LEFT JOIN gf_tercero t ON dc.tercero = t.id_unico 
                                    LEFT JOIN gf_proyecto p ON dc.proyecto =p.id_unico
                                    LEFT JOIN gf_centro_costo cc ON dc.centrocosto = cc.id_unico 
                                    WHERE dc.comprobante = $id");
            ?>
            <div class="col-sm-10" style="margin-top:10px">
                <div class="table-responsive contTabla" >
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>    
                            <tr>
                                <td class="oculto"><strong>Identificador</strong></td>
                                <td class="cabeza" width="7%"></td>
                                <td class="cabeza"><strong>Cuenta</strong></td>
                                <td class="cabeza"><strong>Tercero</strong></td>
                                <td class="cabeza"><strong>Débito</strong></td>
                                <td class="cabeza"><strong>Crédito</strong></td>
                            </tr>
                            <tr>
                                <th class="oculto">Identificador</th>
                                <th class="cabeza" width="7%"></th>
                                <th class="cabeza">Cuenta</th>
                                <th class="cabeza">Tercero</th>
                                <th class="cabeza">Débito</th>
                                <th class="cabeza">Crédito</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                for ($i=0; $i < count($dt); $i++){
                                    echo '<tr>';
                                    echo '<td class="oculto"></td>';
                                    echo '<td class="campos" width="7%"></td>';
                                    echo '<td class="campos">'.ucwords($dt[$i][1]).'</td>';
                                    echo '<td class="campos">'.ucwords(mb_strtolower($dt[$i][2])).' - '.$dt[$i][3].'</td>';
                                    $debito  =0;
                                    $credito =0;
                                    switch (($dt[$i][4])){
                                        case 1:
                                            if($dt[$i][5]>0){
                                               $debito  = $dt[$i][5];
                                            } else {
                                               $credito = ($dt[$i][5]*-1); 
                                            }
                                        break;
                                        case 2:
                                            if($dt[$i][5]>0){
                                               $credito = $dt[$i][5]; 
                                            } else {
                                               $debito  = ($dt[$i][5]*-1); 
                                            }
                                        break;                                    
                                    }
                                    echo '<td class="campos">'.number_format($debito,2,'.',',').'</td>';
                                    echo '<td class="campos">'.number_format($credito,2,'.',',').'</td>';
                                    echo '</tr>';
                                }
                            ?>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php require_once './footer.php'; ?>
    <script src="js/select/select2.full.js"></script>
    <script>
        $(document).ready(function () {
            $(".select2_single").select2({
                allowClear: true
            });
        });
    </script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <div class="modal fade" id="mdlMensaje" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight:normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnOk" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlBtns" role="dialog" align="center" > 
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje2" name="mensaje" style="font-weight:normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAceptar" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnCancelar" class="btn" style="" data-dismiss="modal" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
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
</body>
</html>
