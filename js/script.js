$(".select2").select2();
$(".select").select2();

function ir($url){
    window.location = $url;
}

//Función para obtener las variables enviadas por get desde javascript
var QueryString = function(){
    var query_string = {};
    var query        = window.location.search.substring(1);
    var vars         = query.split("&");
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        if (typeof query_string[pair[0]] === "undefined") {
            query_string[pair[0]] = decodeURIComponent(pair[1]);
        } else if (typeof query_string[pair[0]] === "string") {
            var arr = [ query_string[pair[0]],decodeURIComponent(pair[1]) ];
            query_string[pair[0]] = arr;
        } else {
            query_string[pair[0]].push(decodeURIComponent(pair[1]));
        }
    }
    return query_string;
}();

$("#sltTipoE").change(function(e){
    var tipo  = e.target.value;
    var fechaI = $("#txtFechaI").val();
    var tpm   = $("#sltTipo").val();
    var ingreso   = $("#ingresoid").val();
    $.get('access.php?controller=Reserva&action=obtenerEspacios',{tipo: tipo, fechaI:fechaI, ingreso:ingreso, tpm}, function(data){
        $("#sltEspacio").empty();
        $("#sltEspacio").html(data);
    });
});

function comparaFechas(fechaI, fechaF){
    var fechaX = separarFecha($("#"+fechaI).val(), "H");
    var fechaY = separarFecha($("#"+fechaF).val(), "H");
    if(fechaX && fechaY){
        if(fechaY < fechaX){
            $("#"+fechaF).val(" ");
        }else if(fechaY === fechaX){
            var horaY = divirHora(fechaY);
            var horaX = divirHora(fechaX);

            if(horaY[0] > horaX[0]){
                $("#"+fechaF).val(" ");
            }
        }
    }
}

function separarFecha($fecha, $div){
    var fechaX = $fecha.split("/");
    if($div === "H"){
        var xfecha  = fechaX[2].split(" ");
        var xhora   = xfecha[1].split(":");
        var $fechaI = new Date(xfecha[0], fechaX[1] - 1, fechaX[0], xhora[0], xhora[1]);
    }else if("S"){
        var xfecha  = fechaX[2].split(" ");
        var $fechaI = new Date(xfecha[0], fechaX[1] - 1, fechaX[0]);
    }else if("F"){
        var $fechaI = new Date(fechaX[0], fechaX[1] - 1, fechaX[0]);
    }
    return $fechaI;
}

function divirHora(fechaX){
    var xfecha = fechaX[2].split(" ");
    var xhora  = xfecha[1].split(":");
    return xhora;
}

function compararData(fechaI, fechaF){
    var fechaX = separarFecha($("#"+fechaI).val(), "S");
    var fechaY = separarFecha($("#"+fechaF).val(), "F");
    if(fechaX && fechaY){
        if(fechaY < fechaX){
            $("#"+fechaF).val(" ");
        }
    }
}

function obtenerCiudades($departamento, $campo){
    $.get("access.php?controller=Tercero&action=ObtenerCiudades", {departamento: $departamento}, function (data) {
        $("#"+$campo).empty();
        $("#"+$campo).html(data);
    });
}

function CalcularDv($campo, $dig){
    var arreglo, x, y, z, i, nit1, dv1;
    nit1 = $("#"+$campo).val();
    if(isNaN(nit1)){
        //$("#"+$dig).val("X");
       // alert('Número del Nit no valido, ingrese un número sin puntos, ni comas, ni guiones, ni espacios');
    }else{
        arreglo = new Array(16);
        x = 0;
        y = 0;
        z = nit1.length;
        arreglo[1]  = 3;
        arreglo[2]  = 7;
        arreglo[3]  = 13;
        arreglo[4]  = 17;
        arreglo[5]  = 19;
        arreglo[6]  = 23;
        arreglo[7]  = 29;
        arreglo[8]  = 37;
        arreglo[9]  = 41;
        arreglo[10] = 43;
        arreglo[11] = 47;
        arreglo[12] = 53;
        arreglo[13] = 59;
        arreglo[14] = 67;
        arreglo[15] = 71;
        for (i = 0; i < z; i++){
            y = (nit1.substr(i, 1));
            x += (y * arreglo[z - i]);
        }
        y = x % 11;
        if(y > 1){
            dv1 = 11 - y;
        }else{
            dv1 = y;
        }
        $("#"+$dig).val(dv1);
    }
}

function abrirModalTercero(){
    $("#mdlTercero").modal("show");
}

$("#mdlDisponible").on('show.bs.modal', function(event){
    var btn   = $(event.relatedTarget);
    var esp   = btn.data('esp');
    var modal = $(this);

    modal.find(".modal-body #lnkReserva").attr("href", "access.php?controller=Reserva&espacio="+esp);
    modal.find(".modal-body #lnkIngreso").attr("href", "access.php?controller=Ingreso&espacio="+esp);
});

$("#mdlRelacionados").on('show.bs.modal', function (event) {
    var btn   = $(event.relatedTarget);
    var id    = btn.data('id');
    var modal = $(this);

    $.post("access.php?controller=Reserva&action=obtenerAsociadosDetallePersona", { detalle: id }, function (data) {
        console.log('asociados'+data);
        modal.find(".modal-body #html").html(data);
    });
});

$("#mdlTarifas").on('show.bs.modal', function (event) {
    var btn   = $(event.relatedTarget);
    var esp   = btn.data('esp');
    var modal = $(this);

    $.post("access.php?controller=Reserva&action=obtenerTarifasConceptos", { espacio: esp }, function (data) {
        modal.find(".modal-body #html").html(data);
    });
});

$("#mdlFacturas").on('show.bs.modal', function (event) {
    var btn   = $(event.relatedTarget);
    var id    = btn.data('ing');
    var modal = $(this);

    $.post("access.php?controller=Mapa&action=buscarFacturasIngreso", { mov: id }, function (data) {
        modal.find(".modal-body #html").html(data);
    });
});

$("#mdlPie").on('show.bs.modal', function(e){
    var fecha = $("#txtFechaI").val();
    $.get("access.php?controller=Mapa&action=obtenerCantidadMovimiento", {fecha: fecha}, function (data) {
        var x = data.split(",");
        total = parseFloat(x[0]) + parseFloat(x[1]) + parseFloat(x[2]) + parseFloat(x[3]);
        var dis = Math.round(100 * x[0] / total);
        var res = Math.round(100 * x[1] / total);
        var ocp = Math.round(100 * x[2] / total);
        var blo = Math.round(100 * x[3] / total);
        var chart = new CanvasJS.Chart("cnvC", {
            theme: "light2", // "light1", "light2", "dark1", "dark2"
            exportEnabled: true,
            animationEnabled: true,
            title: {
                text: "GRAFICO DE OCUPACIÓN"
            },
            data: [{
                type: "pie",
                startAngle: 50,
                toolTipContent: "<b>{label}</b>: {y}%",
                showInLegend: "true",
                legendText: "{label}",
                indexLabelFontSize: 13,
                indexLabel: "{label} {y}%",
                dataPoints: [
                    { y: dis, label: "Disponible", color: "#20db36"},
                    { y: res, label: "Reservado", color: "#efef13"},
                    { y: ocp, label: "Ocupado", color: "#f72525"},
                    { y: blo, label: "Bloqueado", color: "#2989D8"}
                ]
            }]
        });
        chart.render();
    });
});

$("#mdlBloqueo").on('show.bs.modal', function (event) {
    var btn   = $(event.relatedTarget);
    var esp   = btn.data('esp');
    var modal = $(this);
    modal.find(".modal-body #txtEspacio").val(esp);

    $.post("access.php?controller=Bloqueo&action=validarNumero", { clase: 4 }, function (data) {
        modal.find(".modal-body #txtNumero").val(data);
    });

    $.post("access.php?controller=Bloqueo&action=obtenerNombreMov", { clase: 4 }, function (data) {
        var res = JSON.parse(data);
        modal.find(".modal-body #txtTipoMov").val(res.tipo);
        modal.find(".modal-body #txtTipo").val(res.nombre);
    });
});

$("#btnGB").click(function (e) {
    e.preventDefault();
    var url    = $("#formBloqueo").attr('action');
    var fecha  = $("#txtFechaB").val();
    var esp    = $("#txtEspacio").val();
    var tipo   = $("#txtTipoMov").val();
    var numero = $("#txtNumero").val();
    $.post(url, { txtFecha:fecha, txtEspacio: esp, txtTipo: tipo, txtNumero: numero }, function (data) {
        var res = JSON.parse(data);
        if(res.res == true){
            window.location.reload();
        }
    });
});

function desbloquear(espacio, mov){
    $.post("access.php?controller=Bloqueo&action=modificarM", { espacio: espacio, mov: mov}, function (data) {
        var res = JSON.parse(data);
        if(res.res == true){
            window.location.reload();
        }
    });
}

$("#mdlCaracteristica").on('show.bs.modal', function (e) {
    var btn   = $(e.relatedTarget);
    var id    = btn.data('id');
    var num   = btn.data('codigo');
    var modal = $(this);
    modal.find('.modal-header #nEspacio').text(num);
    modal.find('.modal-body #txtEspacio').val(id);
    $.post("access.php?controller=EspacioHabitable&action=obtenerCaracteristicasEspacios", { espacio: id }, function (data) {
        modal.find('.modal-body #html').html(data);
    });

    $.post("access.php?controller=EspacioHabitable&action=obtenerEspaciosSinCaracterisiticas", function (data) {
        modal.find(".modal-body #sltEspacios").html(data).trigger('chosen:updated');
    })
});

$("#sltTipo").change(function (e) {
    let tipo  = e.target.value;
    let clase = QueryString.clase;
    switch (tipo){
        case 'general':
            $('.fechaX, .tipo').css('display', 'block');
            $('.concepto, .tercero').css('display', 'none');
            $("#txtFechaI, #txtFechaF, #sltTipoI, #sltTipoF").attr("required", true);
            $("#sltConceptoI, #sltConceptoF, #sltTerceroI, #sltTerceroF").attr("required", false);
            $("input[name='optArchivo']").attr('checked', false);
            $("#optExl").click(function () {
                if($("#optExl").is(':checked')){
                    $("#form").attr("action", "access.php?controller=factura&action=InformeGeneralExcel&clase="+clase);
                }
            });
            $("#optPdf").click(function () {
                if($("#optPdf").is(':checked')){
                    $("#form").attr("action", "access.php?controller=factura&action=InformeGeneralPdf&clase="+clase);
                }
            });
            break;
        case 'detallado':
            $('.fechaX, .concepto').css('display', 'block');
            $('.tipo, .tercero').css('display', 'none');
            $("#txtFechaI, #txtFechaF, #sltConceptoI, #sltConceptoF").attr("required", true);
            $("#sltTipoI, #sltTipoF, #sltTerceroI, #sltTerceroF").attr("required", false);
            $("input[name='optArchivo']").attr('checked', false);
            $("#optExl").click(function () {
                if($("#optExl").is(':checked')){
                    $("#form").attr("action", "access.php?controller=factura&action=InformeDetalladoExcel&clase="+clase);
                }
            });
            $("#optPdf").click(function () {
                if($("#optPdf").is(':checked')){
                    $("#form").attr("action", "access.php?controller=factura&action=InformeDetalladoPdf&clase="+clase);
                }
            });
            break;
        case 'concepto':
            $('.fechaX, .concepto').css('display', 'block');
            $('.tipo, .tercero').css('display', 'none');
            $("#txtFechaI, #txtFechaF, #sltConceptoI, #sltConceptoF").attr("required", true);
            $("#sltTipoI, #sltTipoF, #sltTerceroI, #sltTerceroF").attr("required", false);
            $("input[name='optArchivo']").attr('checked', false);
            $("#optExl").click(function () {
                if($("#optExl").is(':checked')){
                    $("#form").attr("action", "access.php?controller=factura&action=InformeConceptoExcel&clase="+clase);
                }
            });
            $("#optPdf").click(function () {
                if($("#optPdf").is(':checked')){
                    $("#form").attr("action", "access.php?controller=factura&action=InformeConceptoPdf&clase="+clase);
                }
            });
            break;
        case 'tercero':
            $('.fechaX, .tercero').css('display', 'block');
            $('.tipo, .concepto').css('display', 'none');
            $("#txtFechaI, #txtFechaF, #sltTerceroI, #sltTerceroF").attr("required", true);
            $("#sltTipoI, #sltTipoF, #sltConceptoI, #sltConceptoF").attr("required", false);
            $("input[name='optArchivo']").attr('checked', false);
            $("#optExl").click(function () {
                if($("#optExl").is(':checked')){
                    $("#form").attr("action", "access.php?controller=factura&action=InformeTerceroExcel&clase="+clase);
                }
            });
            $("#optPdf").click(function () {
                if($("#optPdf").is(':checked')){
                    $("#form").attr("action", "access.php?controller=factura&action=InformeTerceroPdf&clase="+clase);
                }
            });
            break;
        default:
            $('.fechaX, .tipo, .concepto, .tercero').css('display', 'none');
            $("#txtFechaI, #txtFechaF, #sltTipoI, #sltTipoF, #sltConceptoI, #sltConceptoF, #sltTerceroI, #sltTerceroF").attr("required", false);
            $("input[name='optArchivo']").attr('checked', false);
            $("#form").attr("action", "");
            break;
    }
});

$("#sltTipoX").change(function (e) {
    let tipo  = e.target.value;
    let clase = QueryString.clase;
    switch (tipo){
        case 'general':
            $(".fechaX, .tipo").css('display', 'block');
            $(".factura").css('display', 'none');
            $("#txtFechaI, #txtFechaF, #sltTipoI, #sltTipoF").attr('required', true);
            $("#sltFacturaI, #sltFacturaF").attr('required', false);
            $("input[name='optArchivo']").attr('checked', false);
            $("#optExl").click(function () {
                if($("#optExl").is(':checked')){
                    $("#form").attr("action", "access.php?controller=factura&action=InformeRecaudoGeneralExcel&clase="+clase);
                }
            });
            $("#optPdf").click(function () {
                if($("#optPdf").is(':checked')){
                    $("#form").attr("action", "access.php?controller=factura&action=InformeRecaudoGeneralPdf&clase="+clase);
                }
            });
            break;
        case 'detallado':
            $(".fechaX, .factura").css('display', 'block');
            $(".tipo").css('display', 'none');
            $("#txtFechaI, #txtFechaF, #sltFacturaI, #sltFacturaF").attr('required', true);
            $(" #sltTipoI, #sltTipoF").attr('required', false);
            $("input[name='optArchivo']").attr('checked', false);
            $("#optExl").click(function () {
                if($("#optExl").is(':checked')){
                    $("#form").attr("action", "access.php?controller=factura&action=InformeRecaudoDetalladoExcel&clase="+clase);
                }
            });
            $("#optPdf").click(function () {
                if($("#optPdf").is(':checked')){
                    $("#form").attr("action", "access.php?controller=factura&action=InformeRecaudoDetalladoPdf&clase="+clase);
                }
            });
            break;
        case 'fechas':
            $(".fechaX, .tipo").css('display', 'block');
            $(".factura").css('display', 'none');
            $("#txtFechaI, #txtFechaF, #sltTipoI, #sltTipoF").attr('required', true);
            $("#sltFacturaI, #sltFacturaF").attr('required', false);
            $("input[name='optArchivo']").attr('checked', false);
            $("#optExl").click(function () {
                if($("#optExl").is(':checked')){
                    $("#form").attr("action", "access.php?controller=Pago&action=listadoEntreFechasExcel&clase="+clase);
                }
            });
            $("#optPdf").click(function () {
                if($("#optPdf").is(':checked')){
                    $("#form").attr("action", "access.php?controller=Pago&action=listadoEntreFechasPdf&clase="+clase);
                }
            });
            break;
        default:
            $(".fechaX, .factura, .tipo").css('display', 'none');
            $("#txtFechaI, #txtFechaF, #sltFacturaI, #sltFacturaF,  #sltTipoI, #sltTipoF").attr('required', false);
            $("input[name='optArchivo']").attr('checked', false);
            $("#form").attr("action", "");
            break;
    }
});

$("#sltTipoInforme").change(function (e) {
    let tipo  = e.target.value;
    let clase = QueryString.clase;
    switch (tipo){
        case 'resumen':
            $(".fechaX").css('display', 'block');
            $("#txtFechaI, #txtFechaF").attr('required', true);
            $("#sltTipoI, #sltTipoF, #sltProductoI, #sltProductoF, #sltVendendorI, #sltVendendorF").attr('required', false);
            $(".tipo, .producto, .vendedor").css('display', 'none');
            $("input[name='optArchivo']").attr('checked', false);
            $("#optExl").click(function () {
                if($("#optExl").is(':checked')){
                    $("#form").attr("action", "access.php?controller=Punto&action=ResumenVentasExcel&clase="+clase);
                }
            });
            $("#optPdf").click(function () {
                if($("#optPdf").is(':checked')){
                    $("#form").attr("action", "access.php?controller=Punto&action=ResumenVentasPdf&clase="+clase);
                }
            });
            break;
        case 'planilla':
            $(".fechaX, .producto").css('display', 'block');
            $("#txtFechaI, #txtFechaF, #sltProductoI, #sltProductoF").attr('required', true);
            $("#sltTipoI, #sltTipoF, #sltVendendorI, #sltVendendorF").attr('required', false);
            $(".tipo, .vendedor").css('display', 'none');
            $("input[name='optArchivo']").attr('checked', false);
            $("#optExl").click(function () {
                if($("#optExl").is(':checked')){
                    $("#form").attr("action", "access.php?controller=Punto&action=PlanillaVentasExcel&clase="+clase);
                }
            });
            $("#optPdf").click(function () {
                if($("#optPdf").is(':checked')){
                    $("#form").attr("action", "access.php?controller=Punto&action=PlanillaVentasPdf&clase="+clase);
                }
            });
            break;
        case 'costo':
            $(".fechaX, .tipo, .vendedor").css('display', 'block');
            $("#txtFechaI, #txtFechaF, #sltTipoI, #sltTipoF, #sltVendendorI, #sltVendendorF").attr('required', true);
            $(" #sltProductoI, #sltProductoF").attr('required', false);
            $(".producto").css('display', 'none');
            $("input[name='optArchivo']").attr('checked', false);
            $("#optExl").click(function () {
                if($("#optExl").is(':checked')){
                    $("#form").attr("action", "access.php?controller=Punto&action=PlanillaVentasCostoExcel&clase="+clase);
                }
            });
            $("#optPdf").click(function () {
                if($("#optPdf").is(':checked')){
                    $("#form").attr("action", "access.php?controller=Punto&action=PlanillaVentasCostoPdf&clase="+clase);
                }
            });
            break;
        case 'sincosto':
            $("#sltTipoI, #sltTipoF, #sltVendendorI, #sltVendendorF, #txtFechaI, #txtFechaF, #sltProductoI, #sltProductoF").attr('required', false);
            $(".tipo, .vendedor, .fechaX, .producto").css('display', 'none');
            $("input[name='optArchivo']").attr('checked', false);
            $("#optExl").click(function () {
                if($("#optExl").is(':checked')){
                    $("#form").attr("action", "access.php?controller=Punto&action=ListadoSinCosteXLS&clase="+clase);
                }
            });
            $("#optPdf").click(function () {
                if($("#optPdf").is(':checked')){
                    $("#form").attr("action", "access.php?controller=Punto&action=ListadoSinCostePDF&clase="+clase);
                }
            });
            break;
        case 'sinventa':
            $(".fechaX").css('display', 'block');
            $("#txtFechaI, #txtFechaF").attr('required', true);
            $("#sltTipoI, #sltTipoF, #sltVendendorI, #sltVendendorF, #sltProductoI, #sltProductoF").attr('required', false);
            $(".tipo, .vendedor, .producto").css('display', 'none');
            $("input[name='optArchivo']").attr('checked', false);
            $("#optExl").click(function () {
                if($("#optExl").is(':checked')){
                    $("#form").attr("action", "access.php?controller=Punto&action=ListadoProductosSinVentasXLS");
                }
            });
            $("#optPdf").click(function () {
                if($("#optPdf").is(':checked')){
                    $("#form").attr("action", "access.php?controller=Punto&action=ListadoProductosSinVentasPDF");
                }
            });
            break;
        default:
            $(".fechaX, .tipo, .vendedor, .tipo").css('display', 'none');
            $("#txtFechaI, #txtFechaF, #sltTipoI, #sltTipoF, #sltVendendorI, #sltVendendorF, #sltProductoI, #sltProductoF").attr('required', false);
            $("input[name='optArchivo']").attr('checked', false);
            $("#form").attr("action", "");
            break;
    }
});

var $factura = QueryString.factura;

if($factura){
    $("#btnGuardar").attr('disabled',true);
    $("#btnImprimir, #btnRebuilt").attr('disabled',false);
}else{
    $("#btnGuardar").attr('disabled',false);
    $("#btnImprimir,#btnModificar,#btnEliminar, #btnRebuilt").attr('disabled',true);
    $("#btnImprimir,#btnModificar,#btnEliminar, #btnRebuilt").removeAttr('onclick');
}

$cnt   = QueryString.cnt;
$pptal = QueryString.pptal;
if(!$cnt && !$pptal){
    $("#btnEliminar, #btnRebuilt").attr('disabled', true).removeAttr('onclick').css('display', 'none');
}

if(!$cnt){
    $("#btnCnt").css('display', 'none');
}

if(!$pptal){
    $("#btnPto").css('display', 'none');
}

$salida = QueryString.mov;
if(!$salida){
    $("#btnSalida").css('display', 'none');
}

if($factura){
    $("#btnPos").css("display", "block");
}else{
    $("#btnPos").css("display", "none");
}

$("#sltConcepto").change(function(e){
    let concepto = e.target.value;
    $.get("access.php?controller=factura&action=obtenerUnidadesConcepto", { concepto: concepto }, function (data) {
        $("#sltUnidad").html(data);
        $("#sltUnidad").trigger("change");
    });
});

$("#txtNumeroI").blur(function (e) {
    var num = $("#txtNumeroI").val();
    $.post("access.php?controller=Tercero&action=buscarTercero", { num : num }, function (data) {
        if(data == 1){
            //$("#btnModalGuardarT").css("display", "none");
            toastr.warning("El tercero ya fue registrado...");
            $.post("jsonPptal/gf_tercerosJson.php?action=7", { num : num }, function (data) {
                console.log('DataPersona'+data);
                let resultado   = JSON.parse(data);
                let razonsocial = resultado["razonsocial"];
                let id_tipoiden = resultado["idt"];
                let tipo_ident  = resultado["tid"];
                let numerodoc   = resultado["nmi"];
                let digitover   = resultado["div"];
                let direccion   = resultado["dir"];
                let id_depart   = resultado["did"];
                let nombre_dep  = resultado["dnb"];
                let id_ciudad   = resultado["cid"];
                let nomb_ciudad = resultado["cnm"];
                let telefono    = resultado["tel"];
                let email       = resultado["emi"];
                
                let nombreuno   = resultado["nun"];
                let nombredos   = resultado["nds"];
                let apellidouno = resultado["apu"];
                let apellidodos = resultado["apd"];
                let fechanacto  = resultado["fnc"];
                let ciudadiden  = resultado["cdr"];
                let departiden  = resultado["dpr"];
                let representan = resultado["rpl"];
                let nomciuident = resultado["ncr"];
                let nomdepident = resultado["dcr"];
                if(razonsocial !== null && razonsocial !=='' ){
                    $("#btnModalGuardarT").css("display", "none");                    
                    toastr.warning("El tercero no pertenece a un huesped...");
                   document.location.reload();
                } else {
                    $("#txtPrimerNombre").val(nombreuno);
                    $("#txtSegundoNombre").val(nombredos);
                    $("#txtPrimerApellido").val(apellidouno);
                    $("#txtSegundoApellido").val(apellidodos);
                    $("#sltTipoIdent").val(id_tipoiden);
                    $("#sltTipoIdent option[value="+ id_tipoiden +"]").attr("selected",true);
                    $("#txtDigito").val(digitover);
                    $("#txtFecha").val(fechanacto);
                    console.log(fechanacto);
                    $( "#txtFecha" ).datepicker( "destroy" );
                    $("#txtFecha").datepicker({changeMonth: true}).val(fechanacto);
                    $("#txtDireccion").val(direccion);
                    
                    $("#txtNumeroC").val(telefono);
                    $("#txtEmail").val(email);
                    
                    $("#sltEmpresa").val(representan);
                    if(ciudadiden !== null ){
                        $("#sltCiudadIdent").html('<option value="'+ciudadiden+'">'+nomciuident+'</option>');
                        $("#sltCiudadIdent option[value="+ ciudadiden +"]").attr("selected",true);
                    }
                    if(departiden!== null){
                        //$("#sltDepartamentoIdent").html('<option value="'+departiden+'">'+nomdepident+'</option>');
                        $("#sltDepartamentoIdent option[value="+ departiden +"]").attr("selected",true);
                    }
                    if(id_ciudad!== null){
                        $("#sltCiudadResidencia").html('<option value="'+id_ciudad+'">'+nomb_ciudad+'</option>');
                        $("#sltCiudadResidencia option[value="+ id_ciudad +"]").attr("selected",true);
                    }
                    if(id_depart!== null){
                        //$("#sltDepartamentoRes").html('<option value="'+id_depart+'">'+nombre_dep+'</option>');
                        $("#sltDepartamentoRes option[value="+ id_depart +"]").attr("selected",true);
                    }
                 
                    
                    
                }
            })
        }else{
            $("#btnModalGuardarT").css("display", "block");
        }
    });
});

$("#txtNumeroIE").blur(function (e) {
    var num = $("#txtNumeroIE").val();
    $.post("access.php?controller=Tercero&action=buscarTercero", { num : num }, function (data) {
        if(data == 1){
            toastr.warning("El tercero ya fue registrado...");
            $.post("jsonPptal/gf_tercerosJson.php?action=6", { num : num }, function (data) {
                console.log('DataE'+data);
                let resultado   = JSON.parse(data);
                let razonsocial = resultado["razonsocial"];
                let id_tipoiden = resultado["idt"];
                let tipo_ident  = resultado["tid"];
                let numerodoc   = resultado["nmi"];
                let digitover   = resultado["div"];
                let direccion   = resultado["dir"];
                let id_depart   = resultado["did"];
                let nombre_dep  = resultado["dnb"];
                let id_ciudad   = resultado["cid"];
                let nomb_ciudad = resultado["cnm"];
                let telefono    = resultado["tel"];
                let email       = resultado["emi"];
                
                if(razonsocial !== null && razonsocial !=='' ){
                    $("#txtRazonSocial").val(razonsocial);
                    $("#txtDigitoE").val(digitover);
                    $("#txtDireccionE").val(direccion);
                    $("#txtNumeroCE").val(telefono);
                    $("#txtEmailE").val(email);
                    $("#sltTipoIdentE").val(id_tipoiden);
                    if(id_depart!== null){
                        $("#sltDepartamentoResE").val(id_depart);
                    }
                    if(id_ciudad!== null){
                        $("#sltCiudadResidenciaE").val(id_ciudad);
                    }
                    if(id_ciudad!== null){
                        $("#sltCiudadResidenciaE").html('<option value="'+id_ciudad+'">'+nomb_ciudad+'</option>');
                        $("#sltCiudadResidenciaE option[value="+ id_ciudad +"]").attr("selected",true);
                    }
                    
                    $("#sltTipoIdentE option[value="+ id_tipoiden +"]").attr("selected",true);
                } else {
                    $("#btnModalGuardarE").css("display", "none");                    
                    toastr.warning("El tercero no pertenece a una empresa...");
                    document.location.reload();
                }
            })
        }else{
            $("#btnModalGuardarT").css("display", "block");
        }
    });
});