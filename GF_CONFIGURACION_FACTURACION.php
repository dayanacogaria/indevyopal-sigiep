<?php
#############################################################################
#       ******************     Modificaciones       ******************      #
#############################################################################
#08/05/2019 |Erica G. | ARCHIVO CREADO
#############################################################################
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
$con = new ConexionPDO();        
require './jsonPptal/funcionesPptal.php';
require_once 'head_listar.php';
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];
if(!empty($_GET['tp'])){
$row_cf     = $con->Listar("SELECT c.id_unico, LOWER(c.nombre) 
                FROM gp_concepto c 
                WHERE c.id_unico NOT IN 
                    (SELECT cf.concepto FROM gf_configuracion_facturacion cf 
                        WHERE c.id_unico = cf.concepto)
                AND  c.compania = $compania ORDER BY c.nombre");
$row_ca     = $con->Listar("SELECT c.id_unico, LOWER(c.nombre) 
                FROM gp_concepto c 
                WHERE c.id_unico IN 
                    (SELECT cf.concepto FROM gf_configuracion_facturacion cf 
                        WHERE c.id_unico = cf.concepto AND cf.principal = 1)
                AND  c.compania = $compania ORDER BY c.nombre");
$row_en     = $con->Listar("SELECT DISTINCT t.id_unico,IF(CONCAT_WS(' ',
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
                CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                FROM gf_tercero t 
                WHERE t.compania =  $compania 
                ORDER BY t.id_unico ASC " );
$row_cb = $con->Listar("SELECT  ctb.id_unico,
        CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')'),
        c.id_unico 
    FROM 
        gf_cuenta_bancaria ctb
    LEFT JOIN 
        gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria 
    LEFT JOIN 
        gf_cuenta c ON ctb.cuenta = c.id_unico 
    WHERE 
        ctbt.tercero =$compania  
        AND ctb.parametrizacionanno = $anno 
        AND c.id_unico IS NOT NULL ORDER BY ctb.id_unico ASC"); 
$row = $con->Listar("SELECT cf.id_unico, cp.id_unico, LOWER(cp.nombre), 
        ca.id_unico, ca.nombre, 
        t.id_unico,IF(CONCAT_WS(' ',
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
        ctb.id_unico,
                CONCAT_WS('',CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')'),
         IF(principal=1, 'Sí', 'No'),IF(validacion=1, 'Sí', 'No') 
        FROM gf_configuracion_facturacion cf
        LEFT JOIN gp_concepto cp ON cf.concepto = cp.id_unico 
        LEFT JOIN gp_concepto ca ON cf.concepto_asociado = ca.id_unico 
        LEFT JOIN gf_tercero t ON cf.entidad = t.id_unico 
        LEFT JOIN gf_cuenta_bancaria ctb ON cf.cuenta_bancaria = ctb.id_unico 
        LEFT JOIN gf_cuenta c ON ctb.cuenta = c.id_unico 
        WHERE cf.tipo_factura = ".$_GET['tp']);
}
?>
<html>
    <head>
        <title>Configuración Conceptos Facturación</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="js/md5.pack.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <script src="js/jquery-ui.js"></script> 
        <style>
            label #concepto-error, #entidad-error, #cuentab-error{
                display: block;
                color: #bd081c;
                font-weight: bold;
                font-style: italic;
            }
        </style>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Configuración Conceptos  Facturación</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="formns" id="formns" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="tipof" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tipo Factura:</label>
                                <select name="tipof" id="tipof" class="select2_single form-control" title="Seleccione Tipo Factura" style="height: auto " required>
                                    <?php 
                                    if(empty($_GET['tp'])) { 
                                        echo '<option value="">Tipo Factura</option>';
                                        $vg = $con->Listar("SELECT id_unico, prefijo, nombre FROM gp_tipo_factura 
                                            WHERE compania = $compania");
                                        for ($i = 0; $i < count($vg); $i++) {
                                           echo '<option value="'.$vg[$i][0].'">'.$vg[$i][1].' - '.$vg[$i][2].'</option>'; 
                                        }
                                    } else {
                                        $vg1 = $con->Listar("SELECT id_unico, prefijo, nombre FROM gp_tipo_factura WHERE id_unico =".$_GET['tp']);
                                        echo '<option value="'.$vg1[0][0].'">'.$vg1[0][1].' - '.$vg1[0][2].'</option>';
                                        $vg = $con->Listar("SELECT id_unico, prefijo, nombre 
                                            FROM gp_tipo_factura 
                                            WHERE compania = $compania AND id_unico !=".$_GET['tp']);
                                        for ($i = 0; $i < count($vg); $i++) {
                                           echo '<option value="'.$vg[$i][0].'">'.$vg[$i][1].' - '.$vg[$i][2].'</option>'; 
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </form>
                    </div>
                    <br/>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="30px" align="center"></td>
                                        <td><strong>Concepto Facturación</strong></td>
                                        <td><strong>Concepto Asociado</strong></td>
                                        <td><strong>Entidad</strong></td>
                                        <td><strong>Cuenta Bancaria</strong></td>
                                        <td><strong>Principal</strong></td>
                                        <td><strong>Validación</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Concepto Facturación</th>
                                        <th>Concepto Asociado</th>
                                        <th>Entidad</th>
                                        <th>Cuenta Bancaria</th>
                                        <th>Principal</th>
                                        <th>Validación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($_GET['tp'])) { 
                                        
                                        echo '<tr>';
                                        echo '<form name="formd" id="formd" method="POST" action="javaScript:guardar()" enctype="multipart/form-data">';
                                        echo '<td style="display: none;"></td>';
                                        echo '<td><button type="submit"><i title="Guardar" class="glyphicon glyphicon-floppy-disk" ></i></button></td>';
                                        echo '<td>';
                                        echo '<input type="hidden" name="tipo_factura" id="tipo_factura" value="'.$_GET['tp'].'">';
                                        echo '<select name="concepto" id="concepto" class="select2_single form-control" required="required">';
                                        echo '<option value="">Concepto Facturación*</option>';
                                        for($z =0; $z < count($row_cf); $z++){
                                            echo '<option value ="'.$row_cf[$z][0].'">'.ucwords($row_cf[$z][1]).'</option>';
                                        }
                                        echo '</select>';
                                        echo '</td>';
                                        echo '<td>';
                                        echo '<select name="conceptoa" id="conceptoa" class="select2_single form-control" >';
                                        echo '<option value =""> Concepto Asociado </option>';
                                        for($z =0; $z < count($row_ca); $z++){
                                            echo '<option value ="'.$row_ca[$z][0].'">'.ucwords($row_ca[$z][1]).'</option>';
                                        }
                                        echo '</select>';
                                        echo '</td>';
                                        echo '<td>';
                                        echo '<select name="entidad" id="entidad" class="select2_single form-control" required>';
                                        echo '<option value ="">Entidad*</option>';
                                        for($z =0; $z < count($row_en); $z++){
                                            echo '<option value ="'.$row_en[$z][0].'">'.ucwords(mb_strtolower($row_en[$z][1])).' - '.$row_en[$z][2].'</option>';
                                        }
                                        echo '</select>';
                                        echo '</td>';
                                        echo '<td>';
                                        echo '<select name="cuentab" id="cuentab" class="select2_single form-control" required>';
                                        echo '<option value ="">Cuenta Bancaria*</option>';
                                        for($z =0; $z < count($row_cb); $z++){
                                            echo '<option value ="'.$row_cb[$z][0].'">'.ucwords($row_cb[$z][1]).'</option>';
                                        }
                                        echo '</select>';
                                        echo '</td>';
                                        echo '<td>';
                                        echo '<input type="radio" name="principal" id="principal" value="1"/>Sí&nbsp';
                                        echo '<input type="radio" name="principal" id="principal" value="2" checked="checked"/>No';
                                        echo '</td>';
                                        echo '<td>';
                                        echo '<input type="radio" name="validacion" id="validacion" value="1"/>Sí&nbsp';
                                        echo '<input type="radio" name="validacion" id="validacion" value="2" checked="checked"/>No';
                                        echo '</td>';
                                        echo '</form>';
                                        echo '</tr>';
                                        for ($j = 0; $j < count($row); $j++) {
                                            echo '<tr>';
                                            echo '<form name="formm" id="formm" method="POST" action="javascript:modificar()">';
                                            echo '<td style="display: none;">'.$row[$j][0].'</td>';
                                            echo '<td>'; 
                                            echo '<a  onclick="javascript:eliminar('.$row[$j][0].')"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>';
                                            //echo '<a  href="#" onclick="javascript:open_modal_r('.$row[$j][0].')"><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>';
                                            echo '</td>';
                                            echo '<td>'.ucwords($row[$j][2]).'</td>'; 
                                            echo '<td>'.ucwords($row[$j][4]).'</td>'; 
                                            echo '<td>'.ucwords(mb_strtolower($row[$j][6])).' - '.$row[$j][7].'</td>';
                                            echo '<td>'.$row[$j][9].'</td>'; 
                                            echo '<td>'.$row[$j][10].'</td>'; 
                                            echo '<td>'.$row[$j][11].'</td>'; 
                                            echo '</form>';
                                            echo '</tr>';
                                        }
                                    } ?>
                                </tbody>

                            </table>       
                        </div>            
                    </div>     
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
        <div class="modal fade" id="modalEliminar" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensajeEliminar" name="mensaje"></label>
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
            $("#tipof").change(function(){
                var tipof = $("#tipof").val();
                if(tipof!=""){
                    document.location ='GF_CONFIGURACION_FACTURACION.php?tp='+tipof;
                }
            })
        </script>
        <!----**Funcion Validar Porcentaje**---->
        <script>
            function validarNum1(id){
            event = event || window.event;
            var charCode = event.keyCode || event.which;
            var first = (charCode <= 57 && charCode >= 48);
            var numero = $("#porcentaje"+id).val();
            var char = parseFloat(String.fromCharCode(charCode));
            var num = parseFloat(numero+char);
            var com = parseFloat($("#max"+id).val());
            var match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
            var dec = match[0].length;
            if(dec<=3){
                if(num <= com){
                    if (charCode ==46){
                        var element = event.srcElement || event.target;
                        if(element.value.indexOf('.') == -1){
                        return (charCode =46);
                        }else{
                           return first; 
                        }
                        } else {
                        return first;
                    }
                } else {
                    if(num <=com){
                        return first;
                    }else{
                        return false;
                    }
                }
            } else { 
                return false;
            }
        }
        </script> 
        <!----**Funcion Guardar Configuracion**---->
        <script>
            function guardar(){
                var formData = new FormData($("#formd")[0]);  

               $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_facturaJson.php?action=30",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    console.log(response);
                    if(response==0){
                        $("#mensaje").html('Información Guardada Correctamente');  
                        $("#modalMensajes").modal('show'); 

                    } else {
                        $("#mensaje").html('No Se Ha Podido Guardar La Información');  
                        $("#modalMensajes").modal('show'); 
                    }

                }
               })
            }                                                                                                                                                                                                    
        </script>
        <!----**Funcion Eliminar Configuración**---->
        <script>
            function eliminar(id){
                $("#mensajeEliminar").html('¿Desea Eliminar El Registro De Configuración?');  
                $("#modalEliminar").modal('show'); 
                $("#btnAceptar").click(function(){
                    $("#modalEliminar").modal('hide');  
                    var form_data ={id:id,action:31}
                    $.ajax({
                        type: "POST",
                        url: "jsonPptal/gf_facturaJson.php",
                        data: form_data,
                        success: function(response)
                        { 
                            console.log(response);
                            if(response==0){
                                $("#mensaje").html('Información Eliminada Correctamente');  
                                $("#modalMensajes").modal('show'); 

                            } else {
                                $("#mensaje").html('No Se Ha Podido Eliminar La Información');  
                                $("#modalMensajes").modal('show'); 
                            }
                        }
                    });
                });
                $("#btnAceptar").click(function(){
                   $("#modalEliminar").modal('hide');  
                });
            }                                                                                                                                                                                                 
        </script>
        <script>
            function open_modal_r(id) {  

               var form_data={                            
                  id:id 
                };
                 $.ajax({
                    type: 'POST',
                    url: "GF_CONFIGURACION_COMERCIO_MODAL.php#mdlModificar",
                    data:form_data,
                    success: function (data) { 
                        $("#mdlModificar").html(data);
                        $(".movi").modal("show");
                    }
                }).error(function(data,textStatus,jqXHR){
                    alert('data:'+data+'- estado:'+textStatus+'- jqXHR:'+jqXHR);
                })            
            } 
        </script>
        <script>
            $("#Aceptar").click(function(){
               document.location.reload();
            });
        </script> 
        <?php require_once './GF_CONFIGURACION_COMERCIO_MODAL.php'; ?>
    </body>
</html>

