<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#14/08/2018 | Erica G. | Archivo Creado
####/################################################################################
require_once('./head_listar.php');
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
    
?>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/md5.pack.js"></script>
    <style>
        label #fechaI-error,#fechaF-error {
            display: block;
            color: #bd081c;
            font-weight: bold;
            font-style: italic;
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
            rules: {
            }
        });
        $(".cancel").click(function() {
            validator.resetForm();
        });
    });
    </script>
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
        $("#fechaI").datepicker({changeMonth: true,}).val(fecAct);     
        $("#fechaF").datepicker({changeMonth: true,}).val(fecAct);     
    });
    </script>
    <title>Buscar Registro de Gastos</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px; height: 40px; font-size: 25px;display:inline-block;width: 96%">Buscar Registro De Gastos</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:buscar()">
                        <p align="center" style="margin-bottom: 20px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline" style="margin-top: -5px; margin-left: 0px">
                            <div class="form-group form-inline  col-md-3 col-lg-3" text-aling="left">
                                <label for="fechaI" class="col-sm-12 control-label"><strong class="obligado">*</strong>Fecha:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:10px">
                                <input type="text" name="fechaI" id="fechaI" class="form-control col-md-1" style="width:100%" title="Seleccione Fecha" placeholder="Seleccione Fecha" required="required">
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" text-aling="left">
                                <label for="fechaF" class="col-sm-12 control-label"><strong class="obligado">*</strong>Fecha:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:10px">
                                <input type="text" name="fechaF" id="fechaF" class="form-control col-md-1" style="width:100%" title="Seleccione Fecha" placeholder="Seleccione Fecha" required="required">
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:5px; margin-top:0px">
                                <button type="submit"  style="margin-left:0px; margin-top: -5px" class="btn sombra btn-primary" title="Buscar"><i class="glyphicon glyphicon-search" artia-hidden="true"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <br/>
                <input style="margin-left: 460px; margin-right: 5px;margin-top:10px; margin-bottom: 10px;" type="checkbox" onclick="if (this.checked) marcar(true); else marcar(false);" /><strong style=" font-size: 12px;">Marcar/Desmarcar Todos</strong>
                <input type="hidden" name="ids_r" id="ids_r" value="0"/>
                <input type="hidden" name="valor_s" id="valor_s" value="0">
                <input type="hidden" name="valor_r" id="valor_r" value="0">
                <div id="tablaregistrosF">
                    
                </div>
                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:5px">
                    <label id="valorseleccionado" name="valorseleccionado"></label>
                </div>
                <div class="form-group form-inline  col-md-5 col-lg-5" style="margin-left:5px">
                    <label id="valorretencion" name="valorretencion"></label>
                </div>
                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:50px; margin-top:5px">
                    <a onclick="javaScript:guardarMovimiento()" style="margin-left:0px;margin-top: -5px" class="btn sombra btn-primary" title="Buscar"><i class="glyphicon glyphicon-floppy-disk" artia-hidden="true"></i> Registrar Movimientos</a>
                    <a onclick="javaScript:nuevo()" style="margin-left:0px;margin-top: -5px" class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" artia-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('footer.php'); 
    require_once './MODAL_GF_RETENCIONES_GASTOS.php';
    require_once './GF_MODIFICAR_RETENCIONES_MODAL.php';?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="js/md5.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript">
        function guardarMovimiento(){
            var ids = $("#ids_r").val();
            jsShowWindowLoad('Comprobando Configuración...');
            var form_data = {action:10, ids:ids};
            $.ajax({
                type: "POST",
                url: "jsonPptal/gf_planillaGastosJson.php",
                data: form_data,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response+'C');
                    var resultado = JSON.parse(response);
                    var rta = resultado["rta"];
                    var html = resultado["html"];
                    if (rta > 0) {
                        $("#mensaje").html(html);
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            $("#modalMensajes").modal("hide");
                        }) 
                    } else {
                        jsShowWindowLoad('Guardando Movimientos...');
                        var form_data = {action:11, ids:ids};
                        $.ajax({
                            type: "POST",
                            url: "jsonPptal/gf_planillaGastosJson.php",
                            data: form_data,
                            success: function(response)
                            {
                                jsRemoveWindowLoad();
                                console.log(response+'G');
                                if(response ==0){
                                    $("#mensaje").html('No Se Ha Podido Guardar Información');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        $("#modalMensajes").modal("hide");
                                    }) 
                                } else {
                                    $("#mensaje").html('Información Guardada Correctamente');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        cargar();
                                    })                    
                                    
                                }
                            } 
                        })
                    }
                }
            });
        }
        
        function eliminar(id){
            var form_data={ id:id,action:12};
             $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_planillaGastosJson.php",
                data:form_data,
                success: function (data) {
                    console.log(data);
                    if(data==0){ 
                        $("#mensajeEliminar").html("¿Desea Eliminar El Registro Seleccionado?");
                        $("#modalMensajesEliminar").modal("show");
                        $("#AceptarEliminar").click(function(){
                            jsShowWindowLoad('Eliminando');
                            $("#modalMensajesEliminar").modal("hide");
                            var form_data={id:id,action:13};
                            $.ajax({
                               type: 'POST',
                               url: "jsonPptal/gf_planillaGastosJson.php",
                               data:form_data,
                               success: function (data) {
                                   jsRemoveWindowLoad();
                                   if(data==0){
                                        $("#mensaje").html('Información Eliminada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                            buscar();
                                        })
                                   } else {
                                        $("#mensaje").html('No Se Ha Podido Eliminar La Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })
                                   }
                               }
                           })
                        });
                        $("#CancelarEliminar").click(function(){
                            $("#modalMensajesEliminar").modal("hide");
                        });  
                    } else {
                        jsShowWindowLoad('Validando...');
                        var form_data = { estruc: 12, id:data };
                        $.ajax({
                          type: "POST",
                          url: "jsonPptal/consultas.php",
                          data: form_data,
                          success: function(response)
                          { 
                                jsRemoveWindowLoad();
                                $("#mensajemodaleliminar").html(response);
                                $("#modaleliminartodo").modal("show");
                                $("#eliminartodo").click(function(){
                                    var form_data ={estruc:13, id:data };
                                    $.ajax({
                                        type: "POST",
                                        url: "jsonPptal/consultas.php",
                                        data: form_data,
                                        success: function(response)
                                        {
                                            //alert(response);
                                            if(response==1){ 
                                                $("#mensaje").html('Periodo está cerrado.Verifique Nuevamente');
                                                $("#modalMensajes").modal("show");
                                            }else {
                                                if(response==2){
                                                    $("#mensaje").html('Los detalles de algún comprobante estan conciliados, Verifique Nuevamente');
                                                    $("#modalMensajes").modal("show");
                                                } else {
                                                    if(response==0){
                                                        jsShowWindowLoad('Eliminando..');
                                                        var form_data ={estruc:14, id:data };
                                                        $.ajax({
                                                            type: "POST",
                                                            url: "jsonPptal/consultas.php",
                                                            data: form_data,
                                                            success: function(response)
                                                            {
                                                                console.log(response);
                                                                jsRemoveWindowLoad();
                                                                if(response==1){
                                                                    var form_data={id:id,action:13};
                                                                    $.ajax({
                                                                       type: 'POST',
                                                                       url: "jsonPptal/gf_planillaGastosJson.php",
                                                                       data:form_data,
                                                                       success: function (data) {
                                                                           jsRemoveWindowLoad();
                                                                           if(data==0){
                                                                                $("#mensaje").html('Información Eliminada Correctamente');
                                                                                $("#modalMensajes").modal("show");
                                                                                $("#Aceptar").click(function(){
                                                                                    buscar();
                                                                                });
                                                                           } else {
                                                                                $("#mensaje").html('No Se Ha Podido Eliminar La Información');
                                                                                $("#modalMensajes").modal("show");
                                                                           }
                                                                       }
                                                                   })
                                                                    
                                                                } else {
                                                                    $("#mensaje").html('No Se Ha Podido Eliminar La Información');
                                                                    $("#modalMensajes").modal("show");
                                                                }
                                                            }
                                                        });
                                                    } else {
                                                        $("#mensaje").html('Error de validación');
                                                        $("#modalMensajes").modal("show");
                                                    }
                                                }
                                            }

                                        }//Fin succes.
                                      }); 
                                })
                                $("#canEliTodo").click(function(){
                                   $("#modaleliminartodo").modal("hide"); 
                                })
                          }
                        });
                    }
                }
            })
        }
        function buscar(){
            $("#tablaregistrosF").html('');
             var formData = new FormData($("#form")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_planillaGastosJson.php?action=5",
                contentType: false,
                processData: false,
                data:formData,
                success: function(response)
                { 
                    $("#tablaregistrosF").html(response);
                }

            })
        }
        function cargarDis(id){
            var form_data={ id:id, action:14};
             $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_planillaGastosJson.php",
                data:form_data,
                success: function (response) {
                    console.log(response);
                    var resultado = JSON.parse(response);
                    var rta = resultado["rta"];
                    var html1 = resultado["html"];
                    var idm = resultado["idm"];
                    if(rta==0){
                        if(idm!=""){cargarcxp(idm);}
                    } else {
                        $("#mensaje").html(html1);
                        $("#modalMensajes").modal("show");
                    }
                }
            })
        }
        function cargarcxp(cxp){
            window.open('GENERAR_CUENTA_PAGAR.php?cxp='+md5(cxp));
        }
        function cargaregreso(id){
            var form_data={
                action :15,
                pptal :id,
            };
             $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_planillaGastosJson.php",
                data:form_data,
                success: function (data) {
                    console.log(data);
                    window.open('GENERAR_EGRESO.php');
                }
            })
        }
        function imprimir(id){
            window.open('informes/INF_GF_REGISTRO_GASTOS.php?id='+md5(id));
        }
        function nuevo(){
            window.location='GF_REGISTRO_GASTOS.php';
        }
    </script>
    <script>
        function retenciones() {
            var valor = $("#valor").val();
            if(valor==""){}else {
            valor = parseFloat(valor.replace(/\,/g, ''));
            var form_data={
              id:0,
              valorTotal :valor,
              pptal :0,
            };
             $.ajax({
                type: 'POST',
                url: "MODAL_GF_RETENCIONES_GASTOS.php#mdlModificarReteciones1",
                data:form_data,
                success: function (data) {
                    $("#mdlModificarReteciones1").html(data);
                    $(".movi1").modal("show");
                }
            }).error(function(data,textStatus,jqXHR){
                alert('data:'+data+'- estado:'+textStatus+'- jqXHR:'+jqXHR);
            })
        }
        }
        function verretenciones(id) {
            var form_data={
              id:id,
              valorTotal :$("#valor_seleccionado").val(),
              actualizar :1,
            };
             $.ajax({
                type: 'POST',
                url: "GF_MODIFICAR_RETENCIONES_MODAL.php#mdlModificarReteciones",
                data:form_data,
                success: function (data) {
                    $("#mdlModificarReteciones").html(data);
                    $(".movi").modal("show");
                }
            }).error(function(data,textStatus,jqXHR){
                alert('data:'+data+'- estado:'+textStatus+'- jqXHR:'+jqXHR);
            })
        }
        function verretencionessg(idsc) {
            //alert(idsc);
            var form_data={
              idsc:idsc,
              valorTotal :$("#valor_seleccionado").val(),
              actualizar :0,
            };
             $.ajax({
                type: 'POST',
                url: "GF_MODIFICAR_RETENCIONES_MODAL.php#mdlModificarReteciones",
                data:form_data,
                success: function (data) {
                    $("#mdlModificarReteciones").html(data);
                    $(".movi").modal("show");
                }
            }).error(function(data,textStatus,jqXHR){
                alert('data:'+data+'- estado:'+textStatus+'- jqXHR:'+jqXHR);
            })
        }
    </script>
    <script type="text/javascript">
        function marcar(status) 
        {
            var tabla1 = document.getElementById("tabla");
            var eleNodelist1 = tabla1.getElementsByTagName("input");
            console.log(eleNodelist1);
            var valorTotal =  parseFloat($("#valor_s").val());
            var valorTrete =  parseFloat($("#valor_r").val());
            var ids = $("#ids_r").val();
            for (i = 0; i < eleNodelist1.length; i++) {
                var valorc = 'val_t'+i;
                var valorr = 'val_r'+i;
                var valor_t  = $("#"+valorc).val();
                var valor_r  = $("#"+valorr).val();
                var id_r   = 'id'+i;
                var id_rg  = $("#"+id_r).val();
                vh = formatV(valorTotal) ;
                vr = formatV(valorTrete) ;
                if(typeof(valor_t) !== "undefined"){
                    if(status==true){                                                
                        valorTotal += parseFloat(valor_t);
                        valorTrete += parseFloat(valor_r);
                        ids +=','+id_rg;
                    } else {
                        valorTotal -= parseFloat(valor_t);
                        valorTrete -= parseFloat(valor_r);
                        ids = ids.replace(','+id_rg, "");
                    }
                }
                $("#ids_r").val(ids);
                var vh = formatV(valorTotal) ;
                var vr = formatV(valorTrete) ;
                $("#valorseleccionado").html('Valor Total: '+ vh);
                $("#valorretencion").html('Valor Retención: '+ vr);
                $("#valor_s").val(valorTotal);
                $("#valor_r").val(valorTrete);
                if (eleNodelist1[i].type == 'checkbox'){

                    if (status == null) {
                        eleNodelist1[i].checked = !eleNodelist1[i].checked;
                    }else {
                        eleNodelist1[i].checked = status;
                    }
                }

            }
        }
        function cambiarV(i){
            var valorc = 'val_t'+i;
            var valorr = 'val_r'+i;
            var valor_t  = $("#"+valorc).val();
            var valor_r  = $("#"+valorr).val();
            var id_r   = 'id'+i;
            var id_rg  = $("#"+id_r).val();

            var valorTotal =  parseFloat($("#valor_s").val());
            var valorTrete =  parseFloat($("#valor_r").val());
            var ids = $("#ids_r").val();
            vh = formatV(valorTotal) ;
            vr = formatV(valorTrete) ;
            var ncheck = 'seleccion'+i;
            if($("#"+ncheck).prop('checked')){
                valorTotal += parseFloat(valor_t);
                valorTrete += parseFloat(valor_r);
                ids +=','+id_rg;
            } else {
                valorTotal -= parseFloat(valor_t);
                valorTrete -= parseFloat(valor_r);
                ids = ids.replace(','+id_rg, "");
            }
            $("#ids_r").val(ids);
            var vh = formatV(valorTotal) ;
            var vr = formatV(valorTrete) ;
            $("#valorseleccionado").html('Valor Total: '+ vh);
            $("#valorretencion").html('Valor Retención: '+ vr);
            $("#valor_s").val(valorTotal);
            $("#valor_r").val(valorTrete);

        }
    </script>
    <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalMensajesEliminar" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensajeEliminar" name="mensajeEliminar" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptarEliminar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="CancelarEliminar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modaleliminartodo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label  style="font-weight: normal" id="mensajemodaleliminar" name="mensajemodaleliminar"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="eliminartodo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                    <button type="button" id="canEliTodo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>