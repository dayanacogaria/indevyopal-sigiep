<?php
#@Autor:Alexander
#Fomulario de facturación
require_once './Conexion/conexion.php';
require_once './head_listar.php';
?>
<title>Movimiento De Almacén</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script type="text/javascript">
/*Función para ejecutar el datapicker en en el campo fecha*/
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
//var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
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
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha").datepicker({changeMonth: true}).val();        
        $("#fechaV").datepicker({changeMonth: true}).val();  
    });
    </script>
        <style>
/*Estilos tabla*/
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px}
/*Campos dinamicos*/
    .campoD:focus {
        border-color: #66afe9;
        outline: 0;
        -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);            
    }
    .campoD:hover{
        cursor: pointer;
    }
/*Campos dinamicos label*/
    .valorLabel{
        font-size: 10px;
    }
    .valorLabel:hover{
        cursor: pointer;
        color:#1155CC;
    }
/*td de la tabla*/
    .campos{
        padding: 0px;
        font-size: 10px
    }
/*cuerpo*/
    body{
        font-size: 10px
    }
    .form-control{
        padding: 2px;
    }
    </style> 
    <style>
    .cabeza{
        white-space:nowrap;
        padding: 20px;
    }
    .campos{
        padding:-20px;
    }
    </style>
</head>
<body>
    <div class="container-fluid text-left">
        <div class="row content">
            <?php require_once './menu.php'; ?>
            <div class="col-sm-10 text-center" style="margin-top:-22px;">
                <h2 class="tituloform" align="center">Movimiento de Almacén</h2>
                <div class="client-form contenedorForma" style="margin-top:-7px;">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarMovimientoAlmacenJson.php" style="margin-bottom:-20px">
                    <?php 
                        $idMA = 0;   
                        $tipoMov = 0;
                        $numC = 0;
                        $fecC = '';
                        $terc = 0;
                        $depen = 0;
                        $respon = 0;
                        $cenCosto = 0;
                        $rubPP = 0;
                        $proy = 0;
                        $desc = '';
                        $obs = '';
                        $pIVA = 0;
                        $plEnt = 0;
                        $unEnt = 0;
                        $luEnt = 0;                        
                        if(!empty($_SESSION['movimiento'])){
                            $movimiento = $_SESSION['movimiento'];
                            $sqlmovimiento = "
                                        SELECT  m.id_unico,
                                                m.numero,
                                                m.fecha,
                                                m.descripcion,
                                                m.porcivaglobal,
                                                m.plazoentrega,
                                                m.observaciones,
                                                m.tipomovimiento,
                                                tm.id_unico,
                                                tm.nombre,
                                                m.tercero,
                                                t.id_unico, 
                                                t.nombreuno, 
                                                t.nombredos, 
                                                t.apellidouno, 
                                                t.apellidodos, 
                                                t.razonsocial, 
                                                ti.nombre, 
                                                t.numeroidentificacion,
                                                m.dependencia,
                                                d.id_unico,
                                                d.nombre,
                                                m.centrocosto,
                                                cc.id_unico,
                                                cc.nombre,
                                                m.rubropptal,
                                                rp.id_unico,
                                                rp.nombre,
                                                m.proyecto,
                                                p.id_unico,
                                                p.nombre,
                                                m.lugarentrega,
                                                ci.id_unico,
                                                ci.nombre,
                                                ci.departamento,
                                                dp.id_unico,
                                                dp.nombre,
                                                m.unidadentrega,
                                                upe.id_unico,
                                                upe.nombre,
                                                m.estado,
                                                em.id_unico,
                                                em.nombre
                                FROM            gf_movimiento m
                                        LEFT JOIN gf_tipo_movimiento tm             ON m.tipomovimiento = tm.id_unico
                                        LEFT JOIN gf_tercero t                      ON m.tercero = t.id_unico
                                        LEFT JOIN gf_tipo_identificacion ti         ON t.tipoidentificacion = ti.id_unico       
                                        LEFT JOIN gf_dependencia d                  ON m.dependencia = d.id_unico
                                        LEFT JOIN gf_centro_costo cc                ON m.centrocosto = cc.id_unico
                                        LEFT JOIN gf_rubro_pptal rp                 ON m.rubropptal = rp.id_unico
                                        LEFT JOIN gf_proyecto p                     ON m.proyecto = p.id_unico
                                        LEFT JOIN gf_ciudad ci                      ON m.lugarentrega = ci.id_unico
                                        LEFT JOIN gf_departamento dp                ON ci.departamento = dp.id_unico
                                        LEFT JOIN gf_unidad_plazo_entrega upe       ON m.unidadentrega = upe.id_unico
                                        LEFT JOIN gf_estado_movimiento em           ON m.estado = em.id_unico
                                        WHERE m.tipomovimiento = $movimiento";
                            $rsmovimiento= $mysqli->query($sqlmovimiento);
                            $valoresmovimiento = mysqli_fetch_row($rsmovimiento);
                            $idMA = valoresmovimiento[0];   
                            $tipoMov = valoresmovimiento[7];
                            $numC = valoresmovimiento[1];
                            $fecC = valoresmovimiento[2];
                            $terc = valoresmovimiento[10];
                            $depen = valoresmovimiento[19];
                            $respon = valoresmovimiento[11];
                            $cenCosto = valoresmovimiento[22];
                            $rubPP = valoresmovimiento[25];
                            $proy = valoresmovimiento[28];
                            $desc = valoresmovimiento[3];
                            $obs = valoresmovimiento[6];
                            $pIVA = valoresmovimiento[4];
                            $plEnt = valoresmovimiento[5];
                            $unEnt = valoresmovimiento[37];
                            $luEnt = valoresmovimiento[31];
                            $sqlEstado = "SELECT nombre FROM gp_estado_factura WHERE id_unico = $estado";
                            $resEstado = $mysqli->query($sqlEstado);
                            $estdo = mysqli_fetch_row($resEstado);
                            $idMA = 0;   
                        $tipoMov = 0;
                        $numC = 0;
                        $fecC = '';
                        $terc = 0;
                        $depen = 0;
                        $respon = 0;
                        $cenCosto = 0;
                        $rubPP = 0;
                        $proy = 0;
                        $desc = '';
                        $obs = '';
                        $pIVA = 0;
                        $plEnt = 0;
                        $unEnt = 0;
                        $luEnt = 0;
                        }
                        ?>
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>
                        <div class="form-group form-inline">  
                            <!-- combo de selección Tipo factura -->
                            <label class="col-sm-2 control-label">
                                <strong class="obligado">*</strong>Tipo Movimiento:
                            </label>
                            <select class="form-control input-sm col-sm-2" name="slttipofactura" id="slttipofactura" title="Seleccione tipo factura" style="width:100px;height:30%" required>
                                <?php 
                                if(!empty($tipoMov)){
                                    $sqlTM = "SELECT id_unico,nombre FROM gf_tipo_movimiento WHERE clase = 4";
                                    $rsTM = $mysqli->query($sqlTM);                                    
                                    $filaTM = mysqli_fetch_row($rsTM);
                                    echo '<option value="'.$filaTM[0].'">'.ucwords(strtolower($filaTM[1])).'</option>';
                                    $sqltipoMov = "SELECT id_unico,nombre FROM gf_tipo_movimiento WHERE clase = 4";
                                    $rstipoMov = $mysqli->query($sqltipoMov);
                                    while($filatipoMov = mysqli_fetch_row($rstipoMov)){
                                        echo '<option value="'.$filatipoMov[0].'">'.ucwords(strtolower($filatipoMov[1])).'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <!-- Texto en el que asignamos la fecha -->
                            <label for="fecha" class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Fecha:
                            </label>                                
                            <input class="col-sm-2 input-sm" value="<?php if(!empty($fecha)){$valorF = (String) $fecha;$fechaS = explode("-",$valorF); echo $fechaS[2].'/'.$fechaS[1].'/'.$fechaS[0];}else{echo date('d/m/Y');} ?>" type="text" name="fecha" id="fecha" class="form-control" style="width:100px;height:26px" title="Ingrese la fecha" placeholder="Fecha" required>
                            <!-- Número de factura-->
                            <label class="control-label col-sm-2">
                                <strong class="obligado">*</strong>Nro Factura:
                            </label>
                            <input class="form-control input-sm col-sm-2" name="txtNumeroF" id="txtNumeroF" type="text" title="Número factura" placeholder="Nro Factura" style="width:100px;height:26px" value="<?php if(!empty($factura)){echo $factura;}else{echo '';} ?>" required/>
                            <a id="btnBuscar" class="btn " title="Buscar Comprobante" style="margin-left:-110px;margin-top:-2px;padding:3px 3px 3px 3px"><li class="glyphicon glyphicon-search"></li></a>                            
                            <script>
                                $("#btnBuscar").click(function(){
                                    var tipofactura =$("#slttipofactura").val();
                                    if(tipofactura=='""' || tipofactura==0){
                                        $("#mdltipofactura").modal('show');
                                    }else{
                                        $("#btnBuscar").click(function(){            
                                            var form_data = {
                                                is_ajax:1,
                                                numero:+$("#txtNumeroF").val(),
                                                tipo:+$("#slttipofactura").val(),
                                                existente:2
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasBasicas/consultarNumeros.php",
                                                data:form_data,
                                                success: function (data) {
                                                    window.location.reload();                                                  
                                                }
                                            });            
                                        });
                                    }
                                });
                                    
                                $("#txtNumeroF").keyup(function(){ 
                                    var tipof = $("#slttipofactura").val();
                                    if(tipof==0 || tipof=='""'){
                                        $("#mdltipofactura").modal('show');
                                    }else{                                        
                                        $("#txtNumeroF").autocomplete({
                                            source: "consultasFacturacion/consultaAutocompletadoFactura.php?tipo="+tipof,
                                            minLength:5
                                        });
                                    }                                                                       
                                });
                            </script>
                        </div>

</body>
</html>