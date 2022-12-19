//Solo Numeros
function justNumbers(e){
    var keynum = window.event ? window.event.keyCode : e.which;
    if ((keynum == 8) || (keynum == 46) || (keynum == 45))
        return true;
    return /\d/.test(String.fromCharCode(keynum));
}
//Fecha
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
        maxDate: $("#fechaMax").val(),
        minDate: $("#fechaMin").val(),
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
    $("#fechaF").datepicker({changeMonth: true}).val();
    $("#fechaV").datepicker({changeMonth: true}).val();
    $("#txtFechaC").datepicker({changeMonth: true}).val();


});
//Tipo Factura 
$("#sltTipoFactura").change(function(){
    let tipo = $("#sltTipoFactura").val();
    if(tipo.length > 0){
        let form_data = {
            tipo:$("#sltTipoFactura").val(),
            action:1
        };
        $.ajax({
            type: 'POST',
            url: "jsonPptal/gf_facturaJson.php",
            data: form_data,
            success: function (data) {
                $("#txtNumeroF").val(data);
                //Revisar Tipo Cambio
                let form_data = { tipo:$("#sltTipoFactura").val(),action:56};
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_facturaJson.php",
                    data: form_data,
                    success: function (data) {
                        console.log('DD'+data);
                        resultado = JSON.parse(data);
                        let rta = resultado["rta"];
                        if(rta==0){
                            $("#conversion").css("display", "none");
                        } else {
                            let msj = resultado["msj"];
                            let id  = resultado["id"];
                            $("#tipoc").html('Conversión en '+msj);
                            $("#tipo_cambio").val(id);
                            $("#conversion").css("display", "block");

                        }                        
                    }
                });

            }
        });
    }else{
        $("#txtNumeroF").val("");
        $(".herencia").fadeOut("fast");
    }
});
// Buscar Facturas Por Tipo
$("#sltTipoBuscar").change(function(){
    let form_data ={
        estruc:26,
        tipo: $("#sltTipoBuscar").val()
    }
    var option = '<option value="">Buscar Factura</option>';
    $.ajax({
        type:'POST',
        url:'jsonPptal/consultas.php',
        data:form_data,
        success: function(data){
            var option = option+data;
           $("#sltBuscar").html(option);
        }
    });
})
//SELECT2
$("#sltMetodo, #sltTercero,#sltBanco,#sltVendedor,#sltBuscar,#sltUnidad,#sltTipoFactura,#sltCentroCosto,#sltTipoBuscar, #sltIngreso,  #sltUsuario, #sltTipoCot, #sltNumeroC" ).select2({placeholder:"Tercero",allowClear: true});
//Función Guardar Encabezado Factura 
function guardarF(){
    $("#btnGuardar").attr('disabled',true).removeAttr('onclick').removeAttr("href");
    var formData = new FormData($("#form")[0]);  
    jsShowWindowLoad('Guardando Información...');
    var form_data = { action:1 };
    $.ajax({
        type: 'POST',
        url: "jsonPptal/gf_facturaJson.php?action=41",
        data:formData,
        contentType: false,
        processData: false,
        success: function(response)
        { 
            jsRemoveWindowLoad();
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
                    buscar(response);
                }) 
            }
        }
    })

}


//Funcion Buscar
$("#sltBuscar").change(function(e){
    let factura = $("#sltBuscar").val();
    if(!isNaN(factura)){
        buscar(factura);
    }
});
function buscar(factura){
    var form_data = { action:42, factura:factura };
    $.ajax({
        type:'POST',
        url: "jsonPptal/gf_facturaJson.php?action=42",
        data:form_data,
        success: function(response)
        { 
            if($("#htl").val()==1){
                document.location ='registrar_GF_FACTURAHT.php?t='+$("#tipo").val()+response;
            } else {
                if($("#fs").val()==1){
                    document.location ='registrar_GF_FACTURAPV.php?t='+$("#tipo").val()+response;

                } else {
                    document.location ='registrar_GF_FACTURAPV.php?t='+$("#tipo").val()+response;
                }
            }
            
        }
    })
}
//Funcion Validar Fecha
function validarFecha(id_factura){

    let tipoF = parseInt($("#sltTipoFactura").val());
    if(!isNaN(tipoF) || tipoF.length > 0){
            let form_data = {x:2,fecha:$("#fechaF").val(),tipo:tipoF,id_factura:id_factura
        };
        let result = '';
        $.ajax({
            type:'POST',
            url:'consultasBasicas/consultas_factura.php',
            data:form_data,
            success: function(data){
                result = data;
                if(result == true) {
                    $("#mensaje").html("<p>La fecha es mayor a la anterior factura</p>");
                    $("#modalMensajes").modal('show');   
                    $("#fechaF").val('');
                    $("#fechaV").val('');
                }else if(result == 5){
                    $("#mensaje").html("<p>La fecha es menor a la última factura</p>");
                    $("#modalMensajes").modal('show');
                    $("#fechaF").val('');
                    $("#fechaV").val('');
                }
            }
        });
    }
}
//Cambiar Fecha
function change_date(){
    var fecha = $("#fechaF").val();
    var fechaV = sumaFecha(30,fecha);
    $("#fechaV").val(fechaV);
}
// Fecha Vencimiento
function diferents_date(){
    let fecha1 = $("#fechaF").val();  
    let fecha2 = $("#fechaV").val(); 
    var inicial = fecha1.split("/"); 
    var final =  fecha2.split("/");  
    var dateStart = new Date(inicial[2],inicial[1],inicial[0]); 
    var dateEnd = new Date(final[2],final[1],final[0]);         
    if(dateEnd < dateStart){
        $("#mensaje").html("La fecha es menor");
        $("#modalMensajes").modal('show');
        var fv = sumaFecha(30,fecha1);
        $("#fechaV").val(fv);
    }
}
//Sumar Fechas
sumaFecha = function(d, fecha)
{
    var Fecha = new Date();
    var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
    var sep = sFecha.indexOf('/') != -1 ? '/' : '-';
    var aFecha = sFecha.split(sep);
    var fecha = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0];
    fecha= new Date(fecha);
    fecha.setDate(fecha.getDate()+parseInt(d));
    var anno=fecha.getFullYear();
    var mes= fecha.getMonth()+1;
    var dia= fecha.getDate();
    mes = (mes < 10) ? ("0" + mes) : mes;
    dia = (dia < 10) ? ("0" + dia) : dia;
    var fechaFinal = dia+sep+mes+sep+anno;
    return (fechaFinal);
 }
//Nuevo
function nuevo() {
    if($("#htl").val()==1){
        window.location='registrar_GF_FACTURAHT.php?t='+$("#tipo").val();
    } else {
        if($("#fs").val()==1){
            document.location ='registrar_GF_FACTURAPV.php?t='+$("#tipo").val();
        } else { 
            window.location='registrar_GF_FACTURAPV.php?t='+$("#tipo").val();
        }
    }
    
}
//Modificar Encabezado
function modificar(){
    var formData = new FormData($("#form")[0]);  
    jsShowWindowLoad('Modificando Información...');
    var form_data = { action:1 };
    $.ajax({
        type: 'POST',
        url: "jsonPptal/gf_facturaJson.php?action=43",
        data:formData,
        contentType: false,
        processData: false,
        success: function(response)
        { 
            jsRemoveWindowLoad();
            console.log(response);
            if(response ==0){
                $("#mensaje").html('No Se Ha Podido Modificar Información');
                $("#modalMensajes").modal("show");
                $("#Aceptar").click(function(){
                    $("#modalMensajes").modal("hide");
                }) 
            } else {
                $("#mensaje").html('Información Modificada Correctamente');
                $("#modalMensajes").modal("show");
                $("#Aceptar").click(function(){
                   document.location.reload();
                }) 
            }
        }
    })
}
//Eliminar Todos los detalles
function eliminarDatos(){
    $("#mensajeE").html("¿Desea Eliminar los Datos de la Factura?");
    $("#myModalEliminar").modal("show");
    $("#btnEliminarModal").click(function(){
        var formData = new FormData($("#form")[0]);  
        jsShowWindowLoad('Eliminando Información...');
        var form_data = { action:1 };
        $.ajax({
            type: 'POST',
            url: "jsonPptal/gf_facturaJson.php?action=44",
            data:formData,
            contentType: false,
            processData: false,
            success: function(response)
            { 
                jsRemoveWindowLoad();
                if(response ==0){
                    $("#mensaje").html('No Se Ha Podido Eliminar Información');
                    $("#modalMensajes").modal("show");
                    $("#Aceptar").click(function(){
                        $("#modalMensajes").modal("hide");
                    }) 
                } else {
                    $("#mensaje").html('Información Eliminada Correctamente');
                    $("#modalMensajes").modal("show");
                    $("#Aceptar").click(function(){
                       document.location.reload();
                    }) 
                }
            }
        })
    })
}
//Ver Comprobante Cnt
function cargarComprobante(idCnt){
    let form_data={
        idC:idCnt
    };
    $.ajax({
        type: 'POST',
        url: "modalConsultaComprobanteC.php",
        data: form_data,
        success: function (data) {
            $("#modalComprobanteC").html(data);
            $(".comprobantec").modal('show');
        }
    });
}
$("#modalComprobanteC").on('shown.bs.modal',function(){
    try{
        var dataTable = $("#tablaDetalleC").DataTable();
        dataTable.columns.adjust().responsive.recalc();
    }catch(err){}
});
//Ver Comprobante PPtla
function cargarPresupuestal(idPptal){
    let form_data={
        idP:idPptal
    };
    $.ajax({
        type: 'POST',
        url: "modalConsultaComprobanteP.php",
        data: form_data,
        success: function (data, textStatus, jqXHR) {
            $("#modalComprobanteP").html(data);
            $(".comprobantep").modal('show');
        }
    });
}
$("#modalComprobanteP").on('shown.bs.modal',function(){
    try{
        var dataTable = $("#tablaDetalleP").DataTable();
        dataTable.columns.adjust().responsive.recalc();
    }catch(err){}
});
//Reconstruir COmprobantes
function reconstruirComprobantes(){
    let id_factura  = $("#id").val();
    let id_cnt      = $("#idcnt").val();
    let id_pptal      = $("#idpptal").val();
    if(!isNaN(id_factura) && !isNaN(id_cnt) || !isNaN(id_pptal)){
        jsShowWindowLoad('Guardando Información...');
        var form_data = {
            id_factura: id_factura,
            id_cnt:     id_cnt,
            id_pptal:   id_pptal, 
            action:     8,
        };
        $.ajax({
            type:"POST",
            url:"jsonPptal/gf_recaudoFacJson.php?action=8",
            data:form_data,
            success: function(data){
                jsRemoveWindowLoad();
                console.log(data+'Recons');
                if(data.length > 0){
                    $("#mensaje").html("Información no se reconstruyo correctamente");
                    $("#modalMensajes").modal("show");
                }else{

                    $("#mensaje").html("Información Reconstruida Correctamente");
                    $("#modalMensajes").modal("show");
                }
            }
        });
    }
}
//Funcion Abrir Recaudos
function abrirRecaudos(pg){
    if($("#numR").val()>1){
        $("#mdlRecaudos").modal('show');
    } else {
        cargarR(pg);
    }
}
//Cargar Recaudos
function cargarR(pg){
    let form_data = { action:9,pago:pg };
    $.ajax({
        type:'POST',
        url:'jsonPptal/gf_facturaJson.php',
        data:form_data,
        success: function(data,textStatus,jqXHR){
            window.open(data);
        },error : function(data,textStatus,jqXHR){
            alert('data : '+data+' , textStatus: '+textStatus+', jqXHR : '+jqXHR);
        }
    });
}
//Modal Recaudo
function modalRecaudo(){
    $("#mdlRecaudo").modal("show");
}
//Guardar Recaudo
$("#registrarRecaudo").click(function(){
    if($("#sltBanco").val() !="") {
        var form_data ={action:5, factura:$("#id").val() };
        var resultado = "";
        $.ajax({
            type:"POST",
            url:"jsonPptal/gf_facturaJson.php",
            data:form_data,
            success: function(data){
                console.log(data+'Val');
                resultado = JSON.parse(data);
                var msj = resultado["msj"];
                var rta = resultado["rta"];
                if(rta==0){
                    var form_data={action:4, recaudo:$("#tiporecaudo").val(), banco:$("#sltBanco").val(),id_factura  : $("#id").val()};
                    $.ajax({
                        type:"POST",
                        url:"jsonPptal/gf_facturaJson.php",
                        data:form_data,
                        success: function(data){
                            if(data ==0){
                                $("#mensaje").html("Recaudo Registrado Correctamente");
                                $("#modalMensajes").modal("show");
                                $("#Aceptar").click(function(){
                                    document.location.reload();
                                })
                            } else {
                                $("#mensaje").html("Error Al Registrar Recaudo");
                                $("#modalMensajes").modal("show");
                                $("#Aceptar").click(function(){
                                    document.location.reload();
                                })
                            }
                        }
                    });
                } else {
                    $("#mensaje").html(msj);
                    $("#myModalError").modal("show");
                    $("#btnErrorModal").click(function(){
                        $("#myModalError").modal("hide");
                    })
                }
            }
        });
    }
});
//Retenciones 
function retenciones() {
    let id = $("#id").val();
    let form_data={
      id:$("#idcnt").val() ,
      valorTotal :$("#valot").val(),
      pptal :$("#idpptal").val(), 
      facturacion :1
    };
     $.ajax({
        type: 'POST',
        url: "MODAL_GF_RETENCIONES_FAC.php#mdlModificarReteciones1",
        data:form_data,
        success: function (data) {
            $("#mdlModificarReteciones1").html(data);
            $(".movi1").modal("show");
        }
    }).error(function(data,textStatus,jqXHR){
        alert('data:'+data+'- estado:'+textStatus+'- jqXHR:'+jqXHR);
    })
}
function verretenciones() {
    let id = $("#id").val();
    let form_data={
      id: $("#idcnt").val() ,
      valorTotal :$("#valot").val(),
      pptal :$("#idpptal").val(), 
      facturacion :id 
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
//****DETALLES FACTURA 
//Cambiar Concepto
function cambioConcepto(){
    let concepto = $("#sltConcepto").val();
    if(concepto !== ''){        
        $("#txtCantidad").val('');
        $("#txtXDescuento").val($("#txtDescuento").val());
        $("#txtIva").val('');
        $("#txtImpoconsumo").val('');
        $("#txtAjustePeso").val('');
        $("#txtValorA").val('');
        $("#descripcion").val('');
        $("#txtValor").val('');
        $("#txtValorDescuento").val('');
        let form_data = {
            concepto:concepto,proceso:1
        };
        $.ajax({
        type: 'POST',
        url: "consultasFacturacion/consultarValor.php",
        data:form_data,
        success: function (data) {
            if(data!=""){
                $("#sltValor").attr('disabled',false);
                $("#txtAjuste").attr('disabled',false);
                $("#sltValor").html(data).fadeIn();
                $.post("access.php?controller=detallefactura&action=obtenerPlanIdConcepto", {concepto: concepto}, function (data) {
                    if(data != 0){
                        let elemento = data;
                        let form_data = {
                            concepto:concepto,action: 58,
                        };
                        $.ajax({
                            type: 'POST',
                            url: "jsonPptal/gf_facturaJson.php",
                            data:form_data,
                            success: function (data) {
                                $("#tipoInventario").val(data);
                                if(data !=5){
                                    $.post("access.php?controller=salida&action=obtnerCantidadPlan", {sltElemento: elemento}, function (data) {
                                        console.log(data+'Cantidad');
                                        if(data > 0){
                                            $("#txtCantidadE").val(data);
                                            $.get("access.php?controller=factura&action=obtenerUnidadesConcepto", { concepto: concepto }, function (data) {
                                                $("#sltUnidad").html(data);
                                                $("#sltUnidad").trigger("change");
                                            });
                                        }else{
                                            $("#mdlCantidad").modal("show");
                                            $("#btnCant").click(function(){
                                                $("#btnGuardarDetalle").attr('disabled', true).removeAttr('onclick').removeAttr("href");
                                                $("#txtCantidad").val('');
                                            });
                                            $("#btnCanApt").click(function(){
                                                $("#btnGuardarDetalle").attr('disabled', false);
                                                $.get("access.php?controller=factura&action=obtenerUnidadesConcepto", { concepto: concepto }, function (data) {
                                                    $("#sltUnidad").html(data);
                                                    $("#sltUnidad").trigger("change");
                                                });
                                            });
                                        }
                                    });
                                } else {

                                    $.get("access.php?controller=factura&action=obtenerUnidadesConcepto", { concepto: concepto }, function (data) {
                                        $("#sltUnidad").html(data);
                                        $("#sltUnidad").trigger("change");
                                    });
                                }
                            }
                        })

                    }
                });
            }else{
                $("#sltValor").attr('disabled',true);
                $("#txtAjuste").attr('disabled',true);
            }
        }
        });
    }
}  
//Cambiar Unidad
$("#sltUnidad").change(function (e) {
    let unidad   = e.target.value;
    let concepto = $("#sltConcepto").val();

    if(!isNaN(concepto)){
        $("#txtCantidad").val('');
        $("#txtIva").val('');
        $("#txtImpoconsumo").val('');
        $("#txtAjustePeso").val('');
        $("#txtValorA").val('');
        $("#descripcion").val('');
        $("#txtValor").val('');
        $("#txtXDescuento").val('');
        $("#txtValorDescuento").val('');
        let form_data={
            action:57, 
            unidad: unidad, 
            concepto: concepto
        };  
         $.ajax({
            type: "POST",
            url: "jsonPptal/gf_facturaJson.php",
            data: form_data, 
            success: function(data)
            { 
                
                if(data!=''){
                    $("#sltValor").html(data).fadeIn();
                    cambiarValor();
                } else {
                    if($("#trm").val()!=0){
                        $("#sltValor").replaceWith('<input type="text" id="txtValor" name="txtValor" class="form-control" style="width:100%; padding:2px;" placeholder="Valor" title="Ingrese el valor" onkeypress="return txtValida(event, \'dec\',\'txtValor\',\'2\' )" onchange="cambiarValorT()"/>');
                        $("#txtValor").val('');
                    } else { 
                        $("#sltValor").replaceWith('<input type="text" id="txtValor" name="txtValor" class="form-control" style="width:100%; padding:2px;" placeholder="Valor" title="Ingrese el valor" onkeypress="return txtValida(event, \'num\')" onchange="cambiarValorT()"/>');
                        $("#txtValor").val('');
                    }
                    cambiarValor();
                }
            }
        })

        //$.get("access.php?controller=Punto&action=ObtenerValorTarifaUnidad", { unidad: unidad, concepto: concepto }, function(data){});
    }
});
//Cambiar Cantidad
$("#txtCantidad").change(function (e) {  
    if($("#txtCantidad").val().indexOf("$", 0) == "0"){
        $.get("access.php?controller=Punto&action=obtenerValorUnidad", { concepto:  $("#sltConcepto").val(), unidad: $("#sltUnidad").val() }, function(data){
                let valor  = $("#txtCantidad").val().substr(1, $("#txtCantidad").val().length);
                valor      = parseFloat(valor).toFixed();
                let precio = parseFloat(data);
                let x = (1 * valor) / precio;
                $("#txtCantidad").val(x.toFixed(4));
                calcular();
        });
    }
    

    var xCan = parseFloat($("#txtCantidadE").val());
    var xCon = parseFloat($("#sltConcepto").val());
    var xCtd = parseFloat(e.target.value);
    if(!isNaN(xCon)){
        let form_data = {
            concepto:concepto,action: 58,
        };

        if($("#tipoInventario").val()!=5){
            $.post("access.php?controller=detallefactura&action=obtenerPlanIdConcepto", { concepto: xCon}, function(data){
                if(data != 0){
                    if(xCtd > xCan){
                        $("#mdlCantidad").modal("show");
                        $("#btnCant").click(function(){
                            $("#btnGuardarDetalle").attr('disabled', true).removeAttr('onclick').removeAttr("href");
                            $("#txtCantidad").val('');
                        });
                        $("#btnCanApt").click(function(){
                             calcular();
                        });
                    } else {
                        calcular();
                    }
                } else {
                    calcular();
                }
            });
        } else {
            calcular();
        }
        
    }
    
});
//Cambiar Descuento
function cambiarTD(){
    if($("#sltTipoDes").val()!==''){
        $("#txtXDescuento").attr('disabled',false);
    } else {
        $("#txtXDescuento").attr('disabled',true);
    }
    $("#txtXDescuento").val('');
    $("#txtValorDescuento").val('');
    calcular();
}
$("#txtXDescuento").change(function(){
    calcular();
})
//Cambiar Valor
$("#sltValor").change(function (e) { 
    cambiarValor();
});
//Cambiar Iva
$("#txtIva").change(function(){
    let iva = $("#txtIva").val();
    if(iva<100){
        $("#porcentajeIva").val(iva);
        calcular();
    }
})
//Cambiar Impoconsumo
$("#txtImpoconsumo").change(function(){
    let impo = $("#txtImpoconsumo").val();
    if(impo<100){
        $("#porcentajeImpoconsumo").val(impo);
        calcular();
    }
})
//Cambiar Ajuste
$("#txtAjustePeso").change(function(){
    let ajuste = $("#txtAjustePeso").val();
    if(ajuste!==''){
        let vb = $("#txtValorB").val();
        let valoriv  = $("#txtIva").val();
        let valorim  = $("#txtImpoconsumo").val();
        let cantidad  = $("#txtCantidad").val();
        let sm = parseFloat(vb) +parseFloat(valoriv)+parseFloat(valorim);
        let vt = parseFloat(sm)* parseFloat(cantidad);
        if(isNaN(vt)){}else {
            vt += parseFloat(ajuste);
            $("#txtValorA").val(vt.toFixed(2));
        }
        
    }
})
function cambiarValor(){
    $("#txtValorX,#txtIva, #txtImpoconsumo,#txtAjustePeso, #txtValorA,#porcentajeIva,#porcentajeImpoconsumo").val('0');
    let cantidad  = $("#txtCantidad").val();
    if(cantidad===0 || cantidad===''){
        cantidad = 1;
    }else{
        cantidad = $("#txtCantidad").val();
    }
    try{
        let sltValor = $("#sltValor").val();
        if($("#sltValor").val() !== undefined){
            let dato = sltValor.split("/");
            if(dato[0] !== '0'){
                $("#txtValorX").val($("#sltValor option:selected").text());
                let tarifa = dato[1];
                //IVA
                let form_data = { tarifa:tarifa, proceso:2 };
                $.ajax({
                    type: 'POST',
                    url: "consultasFacturacion/consultarValor.php",
                    data:form_data,
                    success: function (data) {
                        
                        let iva      = data;
                        $("#porcentajeIva").val(iva);
                        calcular();
                    }
                });
                //Impoconsumo
                let form_data2 = { tarifa:tarifa, proceso:3 };
                $.ajax({
                    type: 'POST',
                    url: "consultasFacturacion/consultarValor.php",
                    data:form_data2,
                    success: function (data) {
                        let impo      = data;
                        $("#porcentajeImpoconsumo").val(impo);
                        calcular();
                    }
                });
                $("#txtIva").prop("readonly", false);
                $("#txtImpoconsumo").prop("readonly",false);
                $("#txtAjustePeso").prop("readonly",true);
            } else {
                $("#txtIva, #txtImpoconsumo, #txtAjustePeso").prop("readonly", false);
                 $("#txtAjustePeso").prop("readonly",true);
                $("#txtValorX,#txtIva, #txtImpoconsumo,#txtAjustePeso, #txtValorA,#porcentajeIva,#porcentajeImpoconsumo").val('0');
                if($("#trm").val()!=0){
                    $("#sltValor").replaceWith('<input type="text" id="txtValor" name="txtValor" class="form-control" style="width:100%; padding:2px;" placeholder="Valor" title="Ingrese el valor" onkeypress="return txtValida(event, \'dec\',\'txtValor\',\'2\' )" onchange="cambiarValorT()"/>');
                } else {
                    $("#sltValor").replaceWith('<input type="text" id="txtValor" name="txtValor" class="form-control" style="width:100%; padding:2px;" placeholder="Valor" title="Ingrese el valor" onkeypress="return txtValida(event, \'num\')" onchange="cambiarValorT()"/>');
                }
                $("#txtValor").focus();
                let tarifa = dato[1];
                //IVA

                let form_data = { tarifa:tarifa, proceso:2 };
                $.ajax({
                    type: 'POST',
                    url: "consultasFacturacion/consultarValor.php",
                    data:form_data,
                    success: function (data) {
                        let iva      = data;
                        $("#porcentajeIva").val(iva);
                        calcular();
                    }
                });
                //Impoconsumo
                let form_data2 = { tarifa:tarifa, proceso:3 };
                $.ajax({
                    type: 'POST',
                    url: "consultasFacturacion/consultarValor.php",
                    data:form_data2,
                    success: function (data) {
                        let impo      = data;
                        $("#porcentajeImpoconsumo").val(impo);
                        calcular();
                    }
                });
            }
        } else {
            $("#txtIva, #txtImpoconsumo, #txtAjustePeso").prop("readonly", false);
             $("#txtAjustePeso").prop("readonly",true);
            $("#txtValorX,#txtIva, #txtImpoconsumo,#txtAjustePeso, #txtValorA,#porcentajeIva,#porcentajeImpoconsumo").val('0');
            if($("#trm").val()!=0){
                $("#sltValor").replaceWith('<input type="text" id="txtValor" name="txtValor" class="form-control" style="width:100%; padding:2px;" placeholder="Valor" title="Ingrese el valor" onkeypress="return txtValida(event, \'dec\',\'txtValor\',\'2\' )" onchange="cambiarValorT()"/>');
            } else {
                $("#sltValor").replaceWith('<input type="text" id="txtValor" name="txtValor" class="form-control" style="width:100%; padding:2px;" placeholder="Valor" title="Ingrese el valor" onkeypress="return txtValida(event, \'num\')" onchange="cambiarValorT()"/>');
            }
            
            $("#txtValor").focus();
            let tarifa = dato[1];
            //IVA

            let form_data = { tarifa:tarifa, proceso:2 };
            $.ajax({
                type: 'POST',
                url: "consultasFacturacion/consultarValor.php",
                data:form_data,
                success: function (data) {
                    
                    let iva      = data;
                    $("#porcentajeIva").val(iva);
                    calcular();
                }
            });
            //Impoconsumo
            let form_data2 = { tarifa:tarifa, proceso:3 };
            $.ajax({
                type: 'POST',
                url: "consultasFacturacion/consultarValor.php",
                data:form_data2,
                success: function (data) {
                    let impo      = data;
                    $("#porcentajeImpoconsumo").val(impo);
                    calcular();
                }
            });
            calcular();
        }
    }catch($e){

    }
}
function cambiarValorT(){
    $("#txtValorX,#txtIva, #txtImpoconsumo,#txtAjustePeso, #txtValorA").val('0');
    let cantidad  = $("#txtCantidad").val();
    if(cantidad==0 || cantidad==''){
        cantidad = 1;
    }else{
        cantidad = $("#txtCantidad").val();
    }
    try{
        let valor = $("#txtValor").val();
        $("#txtIva, #txtImpoconsumo, #txtAjustePeso").prop("readonly", false);
         $("#txtAjustePeso").prop("readonly",true);
        $("#txtValor").focus();
        $("#txtValorX").val(valor);
        calcular();
    }catch($e){}
}
function calcular(){
    let tipo_c    = $("#tipo_c").val();//1- Total 2- Iva Incluido
    let cantidad  = $("#txtCantidad").val();
    if(cantidad===0 || cantidad===''){
        cantidad = 1;
        $("#txtCantidad").val(1);
    }
    let tipo_dscto  = parseFloat($("#sltTipoDes").val());
    let descuento   = parseFloat($("#txtXDescuento").val());
    let vu          = parseFloat($("#txtValorX").val());
    let vureal      = parseFloat($("#txtValorX").val());
    let iva         = parseFloat($("#porcentajeIva").val());
    let impo        = parseFloat($("#porcentajeImpoconsumo").val());
    let ajuste      = 0;
    let valor_dscto = 0;
    if($("#txtAjustePeso").val()!==''){
        ajuste = parseFloat($("#txtAjustePeso").val())
    }
    let valoriv     = 0;
    let valorim     = 0;
    if(vu !==''){
        //CALCULAR VALOR BASE
        let vp = 0;
        if(tipo_c === '2'){
            
            if(iva > 0){
               vp += parseFloat(iva);     
            }
            if(impo > 0){
               vp += parseFloat(impo);
            }
            if(vp>0){
                let pi = parseFloat(1+(vp/100));
                vu     = parseFloat(vu /pi);
            }
        }        
        if(descuento > 0){
            //Porcentaje
            if(tipo_dscto===1){
                if(vp > 0){
                    valor_dscto = parseFloat(vureal) * parseFloat(cantidad);
                    valor_dscto = parseFloat(valor_dscto) /parseFloat(1+(vp/100)) ;
                    valor_dscto = (parseFloat(valor_dscto)*parseFloat(descuento))/100;    
                    
                } else {
                    valor_dscto = parseFloat(vureal) * parseFloat(cantidad);
                    valor_dscto = (parseFloat(valor_dscto)*parseFloat(descuento))/100;    
                }
                
                
            } else {
                //Cantidad
                if(tipo_dscto===2){
                    if(vp > 0){
                        valor_dscto = parseFloat(vureal) /parseFloat(1+(vp/100)) ;
                        valor_dscto = (parseFloat(valor_dscto)*parseFloat(descuento));    
                        
                    } else {
                        valor_dscto = parseFloat(vureal) * parseFloat(descuento);    
                    }

                    
                } else {
                    //Valor
                    if(tipo_dscto===3){ 
                        valor_dscto = parseFloat(descuento);
                    }
                }
            }
            if(valor_dscto>0){
                vu          = parseFloat(vureal) * parseFloat(cantidad);
                if(vp > 0){
                    vu      = parseFloat(vu) /parseFloat(1+(vp/100)) ;
                }
                vu          = parseFloat(vu) - parseFloat(valor_dscto);
                vu          = parseFloat(vu) /parseFloat(cantidad);

                console.log(vu+'VVUNITARIO');
            }

            $("#txtValorDescuento").val(valor_dscto.toFixed(2));
        }
        $("#txtValorB").val(vu.toFixed(2));
        if(iva > 0){
            valoriv     = vu * (iva/100);
            let enteroi = Math.round(valoriv + "e+2")  + "e-2";
            valoriv = parseFloat(enteroi);
        }
        if(impo > 0){
            valorim = vu * (impo/100);
            let enteroi = Math.round(valorim + "e+2")  + "e-2";
            valorim =  parseFloat(enteroi);
        }
        $("#txtIva").val(valoriv);
        $("#txtImpoconsumo").val(valorim);
        let sm = parseFloat(vu.toFixed(2)) +parseFloat(valoriv)+parseFloat(valorim);
        let vt = parseFloat(sm)* parseFloat(cantidad);
        let ajs = 0;
        if(isNaN(vt)){}else {
            
            
            let base = Math.pow(10, 0);
            let entero = Math.round(vt * base);
            /*ajs = parseFloat(entero)-parseFloat(vt.toFixed(2));
            $("#txtAjustePeso").val(ajs.toFixed(2));
            vt += parseFloat(ajs);*/
            
        }
        $("#txtValorA").val(vt.toFixed(2));
    }
}
//Guardar Detalles
function guardarDetalles(){
    var formData = new FormData($("#form-detalle")[0]);  
    jsShowWindowLoad('Guardando Información...');
    var form_data = { action:1 };
    $.ajax({
        type: 'POST',
        url: "jsonPptal/gf_facturaJson.php?action=45",
        data:formData,
        contentType: false,
        processData: false,
        success: function(response)
        { 
            jsRemoveWindowLoad();
            console.log(response);
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
                    document.location.reload();
                }) 
            }
        }
    })
}

$(document).ready(function() {
    let idf = $("#id").val();
    var coti = $("#cot").val();
    let el  = 0;
    if(idf!==''){
        if($("#cufe").val()!==''){
            el  += 1;
        } else {
            if($("#nr").val()>0) {
                el  += 1;
            } else {
                if(coti==1){ 
                    $("#btnGuardarDetalle,#btnImprimir,#btnModificar,#btnEliminar, #btnRebuilt").attr('disabled',true).removeAttr('onclick').removeAttr("href");
                }else{
                    $("#btnGuardar").attr('disabled',true).removeAttr('onclick').removeAttr("href");
                 }
            }
        }
        let form_data1 = {action: 34, factura: idf};
        $.ajax({
            type: "POST",
            url: "jsonPptal/gf_facturaJson.php",
            data: form_data1,
            success: function (response)
            {
                
                if (response == 1 && $('#tipo_cb').prop('checked')) {
                    document.location = 'Registrar_TRM.php'
                }
            }
        });
        if($("#trm").val()==1){
            if($("#detalles").val()>0){
                $( "#fechaF" ).datepicker( "destroy" );
                 $("#tipo_cb").click(function(){
                    return false;
                }) 
            }
        }  
        
        let form_data2 = {
            action      :3,
            id_factura  : idf,
        };
        $.ajax({
            type:"POST",
            url:"jsonPptal/gf_facturaJson.php",
            data:form_data2,
            success: function(data){
                if(data!=0){
                    if($("#cufe").val()!==''){} else { 
                        $("#recaudo").css("display", "block");
                        $("#tiporecaudo").val(data);
                    }
                }
            }
        });
        let form_data3 = {
            case   : 4,
            fecha  : $("#fechaF").val(),
        };
        $.ajax({
            type:"POST",
            url:"jsonSistema/consultas.php",
            data:form_data3,
            success: function(data){
                if(data===1){
                   el +=1;

                }
            }
        });
        let form_data = { tipo:$("#sltTipoFactura").val(),action:56};
        $.ajax({
            type: 'POST',
            url: "jsonPptal/gf_facturaJson.php",
            data: form_data,
            success: function (data) {
                resultado = JSON.parse(data);
                let rta = resultado["rta"];
                if(rta==0){
                    $("#conversion").css("display", "none");
                } else {
                    let msj = resultado["msj"];
                    let id  = resultado["id"];
                    $("#tipoc").html('Conversión en '+msj);
                    $("#tipo_cambio").val(id);
                    $("#conversion").css("display", "block");

                    if($("#tipo_cambio").val()!=''){
                        if($("#detalles").val()>0){
                            $("#tipo_cb").click(function(){
                                return false;
                            }) 
                        }
                    }  

                }                        
            }
        });
    } else {
            $("#btnGuardarDetalle,#btnImprimir,#btnModificar,#btnEliminar, #btnRebuilt").attr('disabled',true).removeAttr('onclick').removeAttr("href");
 
        let form_data4 = {action: 33};
        $.ajax({
            type: "POST",
            url: "jsonPptal/gf_facturaJson.php",
            data: form_data4,
            success: function (response)
            {
                
                if (response == 1 && $('#tipo_cb').prop('checked')) {
                    document.location = 'Registrar_TRM.php'
                }
            } 
        });
    }
    if(el>0){
        $("#btnGuardarDetalle,#btnGuardar,#btnModificar,#btnEliminar, #btnRebuilt,.eliminar, #btnRet").attr('disabled',true).removeAttr('onclick').removeAttr("href");
        $(".eliminar").css('display', 'none');
        $("#recaudo").css('display','none');
        $("#btnRet").css('display','none');
        $("#el").val(el);

    }
})
//Elimianr Detalles
function eliminar(id){
    if($("#el").val()>0){
        $("#tdEliminar"+id).css('display','none');
    }else {
        $("#mensajeE").html("¿Desea Eliminar El Registro Seleccionado?");
        $("#myModalEliminar").modal("show");
        $("#btnEliminarModal").click(function(){
        jsShowWindowLoad('Eliminando Información...');
        var form_data = { action:46, id:id};
        $.ajax({
            type:'POST',
            url:'jsonPptal/gf_facturaJson.php',
            data:form_data,
            success: function(response)
            { 
                jsRemoveWindowLoad();
                if(response ==0){
                    $("#mensaje").html('No Se Ha Podido Eliminar Información');
                    $("#modalMensajes").modal("show");
                    $("#Aceptar").click(function(){
                        $("#modalMensajes").modal("hide");
                    }) 
                } else {
                    $("#mensaje").html('Información Eliminada Correctamente');
                    $("#modalMensajes").modal("show");
                    $("#Aceptar").click(function(){
                       document.location.reload();
                    }) 
                }
            }
        })
    })
    }
}
/*#Autocompletar Concepto*/
$("#concepto").keyup(function () {
    $("#concepto").autocomplete({
        source: "jsonPptal/gf_facturaJson.php?action=48 ",
        minlength: 5,
        select: function (event, ui) {
            var referencia = ui.item;
            var ref = referencia.value;
        },
    });
});
function valorCambio() {
    let codigo = $('#concepto').val();
    let form_data = { action: 49, codigo: codigo};
    $.ajax({
        type: 'POST',
        url: "jsonPptal/gf_facturaJson.php",
        data: form_data,
        success: function (data) {
            console.log(data+'Concepto');
            if(data==='0'){
                buscarcodigo(codigo);
            } else { 
                $('#sltConcepto').val(data);
                cambioConcepto();
            }
        }
    });
}
function buscarcodigo(codigo){
    let form_data1 = { action: 50,codigo: codigo };
    $.ajax({
        type: 'POST',
        url: "jsonPptal/gf_facturaJson.php",
        data: form_data1,
        success: function (data) {
            if(data!=='0'){
                $("#concepto").val(data);
                valorCambio();
            } else {
                $("#concepto").val('');
                $("#mensaje").html('No Se Ha Encontrado Concepto');
                $("#modalMensajes").modal("show");
                $("#Aceptar").click(function(){
                    $("#modalMensajes").modal("hide");
                }) 
            }
        }
    })
}

$("#sltTercero").change(function(){
    let tipo_f = $("#sltTipoFactura").val();
    let tercero = $("#sltTercero").val();
    if(tercero!=='' && tipo_f!==''){
        let form_data4 = {action: 51, tercero: tercero, tipo_f:tipo_f };
        $.ajax({
            type:"POST",
            url:"jsonPptal/gf_facturaJson.php",
            data:form_data4,
            success: function(data){
                let resultado = JSON.parse(data);
                let rta = resultado["rta"];
                let html1 = resultado["html"];
                if(rta>0){
                    $("#mensaje").html(html1);
                    $("#modalMensajes").modal("show");
                    $("#Aceptar").click(function(){
                        $("#modalMensajes").modal("hide");
                    }) 
                }
            }
        });
    }
})

/*Hotel*/
$("#sltIngreso").change(function(){
    let ingreso = $("#sltIngreso").val();
    if(ingreso != ''){
        document.location = 'registrar_GF_FACTURAHT.php?ingreso='+ingreso;
    }
})


 
