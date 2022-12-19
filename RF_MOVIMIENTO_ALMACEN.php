<?php 
//llamado a la clase de conexion
  require_once('Conexion/conexion.php');
  require_once('head_listar.php');  
 ?>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
 <script type="text/javascript">
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
    });
    function justNumbers(e){   
        var keynum = window.event ? window.event.keyCode : e.which;
        if ((keynum == 8) || (keynum == 46) || (keynum == 45))
        return true;
        return /\d/.test(String.fromCharCode(keynum));
    }
</script> 
<script type="text/javascript">
  $(document).ready(function() {
     var i= 1;
    $('#tablaMovimiento thead th').each( function () {
        if(i >= 1){ 
        var title = $(this).text();
        switch (i){
            case 2:
                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
            break;
            case 3:
                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
            break;
            case 4:
                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
            break;
            case 5:
                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
            break;
            case 6:
                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
            break;
            case 7:
                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
            break;
            case 8:
                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
            break;          
        }
        i = i+1;
      }else{
        i = i+1;
      }
    } );
 
    // DataTable
   var table = $('#tablaMovimiento').DataTable({
      "autoFill": true,
      "scrollX": true,
      "pageLength": 5,
        "language": {
          "lengthMenu": "Mostrar _MENU_ registros",
          "zeroRecords": "No Existen Registros...",
          "info": "Página _PAGE_ de _PAGES_ ",
          "infoEmpty": "No existen datos",
          "infoFiltered": "(Filtrado de _MAX_ registros)",
          "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
        },
        'columnDefs': [{
         'targets': 0,
         'searchable':false,
         'orderable':false,
         'className': 'dt-body-center'         
      }]
   });

    var i = 0;
    table.columns().every( function () {
        var that = this;
        if(i!=0){
        $( 'input', this.header() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
        i = i+1;
      }else{
        i = i+1;
      }
    } );
} );
</script>
<!-- Llamado a la cabecera del formulario -->
<title>Movimiento de Almacén</title>
<style>
    .campos{
        padding: 0px;
        font-size: 10px
    }
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px}
    
    body{
        font-family: "Arial";
        font-size: 10px;
    }        
    .select2-choice {min-height: 26px; max-height: 26px;}
</style>
</head>
<body onload="limpiarCampos()"> 
<!-- contenedor principal -->  
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
    <!-- Llamado al menu del formulario -->    
        <div class="col-sm-10 text-left" style="font-size:9px;">         
            <h2 align="center" style="margin-top: -5px" class="tituloform">Movimiento Almacén</h2>
            <div style="margin-top:-7px; border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <!-- inicio del formulario --> 
                <form name="form" class="form-horizontal" style="margin-left: 30px" method="POST"  enctype="multipart/form-data" action="json/registrarMovimientoAlmacenJson.php">                                     
                    <div class="form-inline form-group">
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>         
                        <?php 
                        $idMov =0;
                        $tipoMovimiento = 0;
                        $numeroMovimiento = 0;
                        $fecha='';
                        $centroCosto = 0;
                        $proyecto = 0;
                        $dependencia = 0;
                        $responsable = 0;
                        $rubroPresupuestal = 0;
                        $PlazoEntrega = 0;
                        $estado = 0;
                        $unidadPlazo = 0;
                        $lugarEntrega = 0;
                        $descripcion = '';
                        $observaciones = '';
                        $id=0;
                        $porcIva = '';
                        $tercero = 0;
                        $tipoAsociado=0;
                        $numeroAsoacido=0;
                        if(!empty($_GET['movimiento'])){
                            $idMov = $_GET['movimiento'];
                            $sql = "SELECT mv.tipomovimiento,
                                           mv.numero,
                                           mv.fecha,
                                           mv.centrocosto,
                                           mv.proyecto,
                                           mv.dependencia,
                                           mv.tercero,
                                           mv.rubropptal,
                                           mv.estado,
                                           mv.plazoentrega,
                                           mv.unidadentrega,
                                           mv.lugarentrega,
                                           mv.descripcion,
                                           mv.observaciones,
                                           mv.id_unico,
                                           mv.tercero2
                                    FROM gf_movimiento mv 
                                    WHERE md5(id_unico)='$idMov'";
                            $result = $mysqli->query($sql);
                            $rowPrimaria = mysqli_fetch_row($result);

                            $tipoMovimiento=$rowPrimaria[0];
                            $numeroMovimiento=$rowPrimaria[1];
                            $fecha=$rowPrimaria[2];
                            $centroCosto=$rowPrimaria[3];
                            $proyecto=$rowPrimaria[4];
                            $dependencia=$rowPrimaria[5];
                            $responsable=$rowPrimaria[6];
                            $rubroPresupuestal=$rowPrimaria[7];
                            $estado=$rowPrimaria[8];
                            $PlazoEntrega=$rowPrimaria[9];
                            $unidadPlazo=$rowPrimaria[10];
                            $lugarEntrega=$rowPrimaria[11];
                            $descripcion=$rowPrimaria[12];
                            $observaciones=$rowPrimaria[13];
                            $id=$rowPrimaria[14];
                            $tercero = $rowPrimaria[15];
                            $tipoAsociado;
                            $sqlEstado = "SELECT nombre FROM gf_estado_movimiento WHERE id_unico = $estado";
                            $resEstado = $mysqli->query($sqlEstado);
                            $estdo = mysqli_fetch_row($resEstado);
                        }   

                        if(!empty($_GET['valorI'])){
                            $porcIva=$_GET['valorI'];
                        }
                        ?>
                        <label for="sltTipoMovimiento" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>Tipo Movimiento:
                        </label>                                
                        <select class="col-sm-1 input-sm"  name="sltTipoMovimiento" id="sltTipoMovimiento" class="form-control" style="width:100px;height:26px;cursor: pointer;" title="Seleccione tipo de movimiento" required>
                            <?php
                            if (!empty($tipoMovimiento)) {
                                $sql1 = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_movimiento WHERE id_unico = $tipoMovimiento AND  clase IN (2,3)";
                                $result1 = $mysqli->query($sql1);
                                $fila1 = mysqli_fetch_row($result1);
                                echo '<option value="' . $fila1[0] . '">' . ucwords(strtolower($fila1[1])) . '</option>';
                                $sql2 = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_movimiento WHERE id_unico != $tipoMovimiento AND  clase IN (2,3)";
                                $result2 = $mysqli->query($sql2);
                                while ($fila2 = mysqli_fetch_row($result2)) {
                                    echo '<option value="' . $fila2[0] . '">' . ucwords(strtolower($fila2[1])) . '</option>';
                                }
                            } else {
                                ?>
                                <option value="">Tipo Movimiento</option>
                                <?php
                                $sql3 = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_movimiento WHERE  clase IN (2,3)";
                                $result3 = $mysqli->query($sql3);
                                while ($fila3 = mysqli_fetch_row($result3)) {
                                    echo '<option value="' . $fila3[0] . '">' . $fila3[1] . '</option>';
                                }
                                ?>
                                <?php
                            }
                            ?>                        
                        </select>
                        <label for="txtNumeroMovimiento" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>Nro Movimiento:
                        </label>                                
                        <input class="col-sm-1 input-sm" type="text" name="txtNumeroMovimiento" id="txtNumeroMovimiento" class="form-control" maxlength="50" style="width:100px;height:26px" placeholder="N° movimiento" title="Número de movimiento" value="<?php if (!empty($numeroMovimiento)) {echo $numeroMovimiento;} ?>" required />
                        <a id="btnBuscar" class="btn " title="Buscar" style="margin-left:-570px;margin-top:-2px;padding:3px 3px 3px 3px"><li class="glyphicon glyphicon-search"></li></a>
                        <script type="text/javascript" >
                            $("#sltTipoMovimiento").change(function(){
                                var form_data = {
                                    tipo:$("#sltTipoMovimiento").val(),
                                    nuevos:6
                                };
                                $.ajax({
                                    type: 'POST',
                                    url: "consultasBasicas/generarNuevos.php",
                                    data:form_data,
                                    success: function (data) {
                                        $("#txtNumeroMovimiento").val(data);
                                    }
                                });
                            }); 
                            
                            var mov = <?php echo $id?>;
                            if(mov==0){                                                                                                                                                                                
                                $("#txtNumeroMovimiento").keyup(function(){
                                    var tipo = $("#sltTipoMovimiento").val();
                                    if(tipo==0){
                                        $("#modalTipo").modal('show');
                                    }else{
                                        $("#txtNumeroMovimiento").autocomplete({
                                            source:"consultasBasicas/autocompletadoMovimiento.php?tipo="+tipo,                                            
                                            minlength:5
                                        });
                                    }
                                });                            
                            }
                            
                            $("#btnBuscar").click(function(){                                  
                                if($("#btnNuevo").is(':hidden')){
                                    $("#mdltipocomprobante").modal('show');
                                }else{
                                    var form_data = {
                                        existente:9,
                                        numero:+$("#txtNumeroMovimiento").val(),
                                        tipo:+$("#sltTipoMovimiento").val()
                                    };

                                    $.ajax({
                                        type: 'POST',
                                        url: "consultasBasicas/consultarNumeros.php",
                                        data:form_data,
                                        success: function (data) {
                                            window.location = data;                                                  
                                        }
                                    });                                    
                                }                                                                                                    
                            });
                        </script>
                        <label for="fecha" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>Fecha:
                        </label>                                
                        <input class="col-sm-1 input-sm" value="<?php if (!empty($fecha)) {$fechaS = explode("-", $fecha);echo $fechaS[2] . '/' . $fechaS[1] . '/' . $fechaS[0];} else {echo date('d/m/Y');} ?>" type="text" name="fecha" id="fecha" class="form-control" style="width:100px;height:26px" title="Ingrese la fecha" placeholder="Fecha" required>                                                                                                
                        <label class="col-sm-1 control-label" for="txtDescripcion">
                            <strong class="obligado">*</strong>Tercero:
                        </label>
                        <select class="form-control col-sm-1 input-sm select2 text-left" name="sltTercero" id="sltTercero" id="single" title="Seleccione un tercero para consultar" style="width:292px;height:26px;" required>
                        <?php
                        if(!empty($tercero)){                                   
                            $sql18 = "SELECT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                                    (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE', 
                                    ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                    LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                    LEFT JOIN gf_perfil_tercero prt ON ter.id_unico = prt.tercero
                                    WHERE prt.perfil BETWEEN 5 AND 6 AND ter.id_unico=$tercero";
                            $rs18 = $mysqli->query($sql18);
                            $row18 = mysqli_fetch_row($rs18);
                            echo '<option value="'.$row18[1].'">'.ucwords(strtolower($row18[0].PHP_EOL.$row18[2])).'</option>';                                    
                            $sql19 = "
                                SELECT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                                    (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE', 
                                    ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                    LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                    LEFT JOIN gf_perfil_tercero prt ON ter.id_unico = prt.tercero
                                    WHERE prt.perfil BETWEEN 5 AND 6 AND ter.id_unico!=$tercero";
                            $rs19 = $mysqli->query($sql19);
                            while($row19 = mysqli_fetch_row($rs19)){
                                echo '<option value="'.$row19[1].'">'.ucwords(strtolower($row19[0].PHP_EOL.$row19[2])).'</option>';
                            }
                        }else{
                            echo '<option value="">Tercero</option>';
                            $sql1 = "SELECT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                                    (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE', 
                                    ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                    LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                    LEFT JOIN gf_perfil_tercero prt ON ter.id_unico = prt.tercero
                                    WHERE prt.perfil BETWEEN 5 AND 6";
                            $rs1 = $mysqli->query($sql1);
                            while($row1 = mysqli_fetch_row($rs1)){
                                echo '<option value="'.$row1[1].'">'.ucwords(strtolower($row1[0].PHP_EOL.$row1[2])).'</option>';
                            }
                        }                                
                        ?>                      
                        </select> 
                    </div>
                    <div class="form-inline form-group" style="margin-top:-15px"> 
                        <label for="sltCentroCosto" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>Centro Costo:
                        </label>                                
                        <select class="col-sm-1 input-sm"  name="sltCentroCosto" id="sltCentroCosto" class="form-control" style="width:100px;height:26px;cursor: pointer;" title="Seleccione centro costo" required>
                            <?php
                            if (!empty($centroCosto)) {
                                $sql4 = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico = $centroCosto";
                                $result4 = $mysqli->query($sql4);
                                $fila4 = mysqli_fetch_row($result4);
                                echo '<option value="' . $fila4[0] . '">' . ucwords(strtolower($fila4[1])) . '</option>';
                                $sql5 = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico != $centroCosto";
                                $result5 = $mysqli->query($sql5);
                                while ($fila5 = mysqli_fetch_row($result5)) {
                                    echo '<option value="' . $fila5[0] . '">' . ucwords(strtolower($fila5[1])) . '</option>';
                                }
                            } else {
                                ?>
                                <option value="">Centro Costo</option>
                                <?php
                                $sql6 = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo";
                                $result6 = $mysqli->query($sql6);
                                while ($fila6 = mysqli_fetch_row($result6)) {
                                    echo '<option value="' . $fila6[0] . '">' . $fila6[1] . '</option>';
                                }
                                ?>
                                <?php
                            }
                            ?>                        
                        </select>
                        <label for="sltProyecto" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>Proyecto:                        
                        </label>  
                        <select class="col-sm-1 input-sm"  name="sltProyecto" id="sltProyecto" class="form-control" style="width:100px;height:26px;cursor: pointer;" title="Seleccione proyecto" required>
                            <?php
                            if (!empty($proyecto)) {
                                $sql7 = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico = $proyecto";
                                $result7 = $mysqli->query($sql7);
                                $fila7 = mysqli_fetch_row($result7);
                                echo '<option value="' . $fila7[0] . '">' . ucwords(strtolower($fila7[1])) . '</option>';
                                $sql8 = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico != $proyecto";
                                $result8 = $mysqli->query($sql8);
                                while ($fila8 = mysqli_fetch_row($result8)) {
                                    echo '<option value="' . $fila8[0] . '">' . ucwords(strtolower($fila8[1])) . '</option>';
                                }
                            } else {
                                ?>
                                <option value="">Proyecto</option>
                                <?php
                                $sql9 = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto";
                                $result9 = $mysqli->query($sql9);
                                while ($fila9 = mysqli_fetch_row($result9)) {
                                    echo '<option value="' . $fila9[0] . '">' . ucwords(strtolower($fila9[1])) . '</option>';
                                }
                                ?>
                                <?php
                            }
                            ?>                        
                        </select>
                        <label for="sltDependencia" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>Dependencia:
                        </label>                            
                        <select class="col-sm-1 input-sm"  name="sltDependencia" id="sltDependencia" class="form-control" style="width:100px;height:26px;cursor: pointer;" title="Seleccione dependecia " required>
                            <?php
                            if (!empty($dependencia)) {
                                $sql9 = "SELECT DISTINCT id_unico,nombre FROM gf_dependencia WHERE id_unico = $dependencia";
                                $result9 = $mysqli->query($sql9);
                                $fila9 = mysqli_fetch_row($result9);
                                echo '<option value="' . $fila9[0] . '">' . ucwords(strtolower($fila9[1])) . '</option>';
                                $sql10 = "SELECT DISTINCT id_unico,nombre FROM gf_dependencia WHERE id_unico != $dependencia";
                                $result10 = $mysqli->query($sql10);
                                while ($fila10 = mysqli_fetch_row($result10)) {
                                    echo '<option value="' . $fila10[0] . '">' . ucwords(strtolower($fila10[1])) . '</option>';
                                }
                            } else {
                                ?>
                                <option value="">Dependencia</option>
                                <?php
                                $sql11 = "SELECT DISTINCT id_unico,nombre FROM gf_dependencia";
                                $result11 = $mysqli->query($sql11);
                                while ($fila11 = mysqli_fetch_row($result11)) {
                                    echo '<option value="' . $fila11[0] . '">' . $fila11[1] . '</option>';
                                }
                                ?>
                                <?php
                            }
                            ?>                        
                        </select>
                        <label for="sltResponsable" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>Responsable:
                        </label>                                
                        <select class="col-sm-1 input-sm"  name="sltResponsable" id="sltResponsable" class="form-control" style="width:100px;height:26px;cursor: pointer;" title="Seleccione responsable" required>                        
                            <?php
                            if (!empty($responsable)) {
                                $sql12 = "SELECT IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                                                        (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE', 
                                                        ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_dependencia_responsable dpr
                                                        LEFT JOIN gf_tercero ter ON dpr.responsable = ter.id_unico
                                                        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion 
                                                        WHERE  ter.id_unico = $responsable";
                                $result12 = $mysqli->query($sql12);
                                $fila12 = mysqli_fetch_row($result12);
                                echo '<option value="' . $fila12[1] . '">' . ucwords(strtolower($fila12[0])) . '</option>';
                                $sql5 = "SELECT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                                                        (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE', 
                                                        ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_dependencia_responsable dpr
                                                        LEFT JOIN gf_tercero ter ON dpr.responsable = ter.id_unico
                                                        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                                        WHERE  ter.id_unico != $responsable";
                                $result5 = $mysqli->query($sql5);
                                while ($fila5 = mysqli_fetch_row($result5)) {
                                    echo '<option value="' . $fila5[1] . '">' . ucwords(strtolower($fila5[0])) . '</option>';
                                }
                            } else {
                                ?>
                                <option value="">Responsable</option>                            
                                <?php
                            }
                            ?>  
                            <script type="text/javascript" >
                                $("#sltDependencia").change(function(){
                                    var form_data={
                                        existente:5,
                                        dependencia:$("#sltDependencia").val()
                                    };

                                    $.ajax({
                                        type: 'POST',
                                        url: "consultasBasicas/consultarNumeros.php",
                                        data:form_data,
                                        success: function (data) {
                                            $("#sltResponsable").html(data).fadeIn();                                        
                                        }
                                    });
                                });
                            </script>
                        </select>
                        <label for="sltRubroP" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>Rubro Presupuestal:
                        </label>              
                        <select class="col-sm-1 input-sm"  name="sltRubroP" id="sltRubroP" class="form-control" style="width:100px;height:26px;cursor: pointer;" title="Seleccione rubro presupuestal " required>
                            <?php
                            if (!empty($rubroPresupuestal)) {
                                $sql9 = "SELECT DISTINCT id_unico,codi_presupuesto,nombre FROM gf_rubro_pptal WHERE id_unico = $rubroPresupuestal";
                                $result9 = $mysqli->query($sql9);
                                $fila9 = mysqli_fetch_row($result9);
                                echo '<option value="' . $fila9[0] . '">' . ucwords(strtolower($fila9[1].'- '.$fila9[2])) . '</option>';
                                $sql10 = "SELECT DISTINCT id_unico,codi_presupuesto,nombre FROM gf_rubro_pptal WHERE id_unico != $rubroPresupuestal";
                                $result10 = $mysqli->query($sql10);
                                while ($fila10 = mysqli_fetch_row($result10)) {
                                    echo '<option value="' . $fila10[0] . '">' . ucwords(strtolower($fila10[1] . '- ' . $fila10[2])) . '</option>';
                                }
                            } else {
                                ?>
                                <option value="">Rubro Presuuestal</option>
                            <?php
                            $sql11 = "SELECT DISTINCT id_unico,codi_presupuesto,nombre FROM gf_rubro_pptal";
                            $result11 = $mysqli->query($sql11);
                            while ($fila11 = mysqli_fetch_row($result11)) {
                                echo '<option value="' . $fila11[0] . '">' . ucwords(strtolower($fila11[1] . ': ' . $fila11[2])) . '</option>';
                            }              
                        }
                        ?>                        
                        </select>
                    </div>
                    <div class="form-inline form-group" style="margin-top:-15px">
                        <label for="txtEstado" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>Plazo Entrega:
                        </label>      
                        <input class="col-sm-2" type="number" onkeypress="return txtValida(event,'num')" name="txtPlazoE" id="txtPlazoE" class="form-control" style="width:100px;height:26px;" value="<?php if (!empty($PlazoEntrega)) {echo ucwords(strtolower($PlazoEntrega));} else {echo '0';} ?>" title="Ingrese plazo de entrega" placeholder="Plazo Entrega"/>                        
                        <label for="sltUPE" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>U. Plazo Entrega:
                        </label>
                        <select class="col-sm-1 input-sm"  name="sltUPE" id="sltUPE" class="form-control" style="width:100px;height:26px;cursor: pointer;" title="Seleccione rubro presupuestal " required>
                            <?php
                            if (!empty($unidadPlazo)) {
                                $sql9 = "SELECT DISTINCT id_unico,nombre FROM gf_unidad_plazo_entrega WHERE id_unico = $unidadPlazo";
                                $result9 = $mysqli->query($sql9);
                                $fila9 = mysqli_fetch_row($result9);
                                echo '<option value="' . $fila9[0] . '">' . ucwords(strtolower($fila9[1])) . '</option>';
                                $sql10 = "SELECT DISTINCT id_unico,nombre FROM gf_unidad_plazo_entrega WHERE id_unico!= $unidadPlazo";
                                $result10 = $mysqli->query($sql10);
                                while ($fila10 = mysqli_fetch_row($result10)) {
                                    echo '<option value="' . $fila10[0] . '">' . ucwords(strtolower($fila10[1])) . '</option>';
                                }
                            } else {
                                ?>
                                <option value="">Unidad Plazo Entrega</option>
                                <?php
                                $sql11 = "SELECT DISTINCT id_unico,nombre FROM gf_unidad_plazo_entrega";
                                $result11 = $mysqli->query($sql11);
                                while ($fila11 = mysqli_fetch_row($result11)) {
                                    echo '<option value="' . $fila11[0] . '">' . ucwords(strtolower($fila11[1])) . '</option>';
                                }
                                ?>
                                <?php
                            }
                            ?>                        
                        </select>
                        <label for="sltLE" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>Lugar Entrega:
                        </label>
                        <select class="col-sm-1 input-sm"  name="sltLE" id="sltLE" class="form-control" style="width:100px;height:26px;cursor: pointer;" title="Seleccione rubro presupuestal " required>
                            <?php
                            if (!empty($lugarEntrega)) {
                                $sql9 = "SELECT DISTINCT ci.id_unico,ci.nombre,dt.nombre FROM gf_ciudad ci LEFT JOIN gf_departamento dt ON ci.departamento = dt.id_unico WHERE ci.id_unico = $lugarEntrega";
                                $result9 = $mysqli->query($sql9);
                                $fila9 = mysqli_fetch_row($result9);
                                echo '<option value="' . $fila9[0] . '">' . ucwords(strtolower($fila9[1] . ' - ' . $fila9[2])) . '</option>';
                                $sql10 = "SELECT DISTINCT ci.id_unico,ci.nombre,dt.nombre FROM gf_ciudad ci LEFT JOIN gf_departamento dt ON ci.departamento = dt.id_unico WHERE ci.id_unico != $lugarEntrega";
                                $result10 = $mysqli->query($sql10);
                                while ($fila10 = mysqli_fetch_row($result10)) {
                                    echo '<option value="' . $fila10[0] . '">' . ucwords(strtolower($fila10[1] . ' - ' . $fila10[2])) . '</option>';
                                }
                            } else {
                                ?>
                                <option value="">Lugar Entrega</option>
                                <?php
                                $sql11 = "SELECT DISTINCT ci.id_unico,ci.nombre,dt.nombre FROM gf_ciudad ci LEFT JOIN gf_departamento dt ON ci.departamento = dt.id_unico";
                                $result11 = $mysqli->query($sql11);
                                while ($fila11 = mysqli_fetch_row($result11)) {
                                    echo '<option value="' . $fila11[0] . '">' . ucwords(strtolower($fila11[1] . ' - ' . $fila11[2])) . '</option>';
                                }                
                            }
                            ?>                        
                        </select>
                        <label for="txtEstado" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>% Iva:
                        </label>
                        <input class="col-sm-1" type="number" onkeypress="return txtValida(event,'num')" name="txtIva" id="txtIva" class="form-control" style="width:100px;height:26px;" value="<?php if (!empty($PlazoEntrega)) {echo ucwords(strtolower($porcIva));} else {echo '0';} ?>" title="Ingrese porcentaje de iva" placeholder="% Iva" />
                        <label for="txtEstado" class="col-sm-1 control-label">
                            Estado:
                        </label>      
                        <?php
                        $sql = "SELECT id_unico,nombre FROM gf_estado_movimiento WHERE id_unico = 2";
                        $result = $mysqli->query($sql);
                        $row = mysqli_fetch_row($result);
                        ?>
                        <input class="col-sm-2" type="text" name="txtEstado" id="txtEstado" class="form-control" style="width:100px;height:26px;" value="<?php if (!empty($estado)) {echo ucwords(strtolower($estdo[0]));} else {echo ucwords(strtolower($row[1]));} ?>" title="Estado" placeholder="Estado" readonly/>
                    </div>
                    <div class="form-group form-group" style="margin-top:-15px">
                        <label class="col-sm-1 control-label" for="txtDescripcion">
                            <strong class="obligado">*</strong>Tipo Asociado:    
                        </label>
                        <select class="col-sm-1 input-sm"  name="sltTipoAsociado" id="sltTipoAsociado" class="form-control" style="width:100px;height:26px;cursor: pointer;" title="Seleccione tipo de asociado" required>
                            <?php
                            if (!empty($tipoAsociado)) {
                                $sql1 = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_movimiento WHERE id_unico = $tipoMovimiento AND  clase=4";
                                $result1 = $mysqli->query($sql1);
                                $fila1 = mysqli_fetch_row($result1);
                                echo '<option value="' . $fila1[0] . '">' . ucwords(strtolower($fila1[1])) . '</option>';
                                $sql2 = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_movimiento WHERE id_unico != $tipoMovimiento AND  clase=4";
                                $result2 = $mysqli->query($sql2);
                                while ($fila2 = mysqli_fetch_row($result2)) {
                                    echo '<option value="' . $fila2[0] . '">' . ucwords(strtolower($fila2[1])) . '</option>';
                                }
                            } else {
                                ?>
                                <option value="">Tipo Asociado</option>
                                <?php
                                $sql3 = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_movimiento WHERE  clase=4";
                                $result3 = $mysqli->query($sql3);
                                while ($fila3 = mysqli_fetch_row($result3)) {
                                    echo '<option value="' . $fila3[0] . '">' . $fila3[1] . '</option>';
                                }
                                ?>
                                <?php
                            }
                            ?>                                              
                        </select>
                        <label class="col-sm-1 control-label" for="txtDescripcion">
                            <strong class="obligado">*</strong>Nro Asociado:    
                        </label>
                        <select class="col-sm-1 input-sm select2 form-control"  name="sltNumeroA" id="sltNumeroA" class="form-control" style="width:292px;height:26px;cursor: pointer;" title="Seleccione numero de asociado" required>
                            <option value="">Nro Asociado</option>
                        </select>
                        <script type="text/javascript">
                            $("#sltTipoAsociado").change(function(){
                                var form_data = {
                                    existente:10,
                                    tipo:+$("#sltTipoAsociado").val()
                                };                                                                   
                                $.ajax({
                                    type: 'POST',
                                    url: "consultasBasicas/consultarNumeros.php",
                                    data:form_data,
                                    success: function (data) {                                            
                                        $("#sltNumeroA").html(data).fadeIn();
                                        $("#sltNumeroA").css('display','none');
                                    }
                                });
                            });
                            
                            $("#sltNumeroA").change(function(){                                                                
                                $("#tablaMovimiento").dataTable().fnDestroy();
                                var table = $('#tablaMovimiento').DataTable({
                                    ajax: {
                                        url: 'consultasBasicas/consultaDetalles.php?detalle=1&values='+$('#sltNumeroA').val(),
                                        dataSrc: 'data'
                                    },
                                    columns: [                                        
                                        {data:''},
                                        {data:'Item'},
                                        {data:'Plan Inventario'},
                                        {data:'Cantidad'},
                                        {data:'Valor Aproximado'},
                                        {data:'Iva'},
                                        {data:'Valor Total'}
                                    ] , 
                                    "autoFill": true,
                                    "scrollX": true,
                                    "pageLength": 5,                                        
                                        "language": {
                                            "lengthMenu": "Mostrar _MENU_ registros",
                                            "zeroRecords": "No Existen Registros...",
                                            "info": "Página _PAGE_ de _PAGES_ ",
                                            "infoEmpty": "No existen datos",
                                            "infoFiltered": "(Filtrado de _MAX_ registros)",
                                            "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                                        },
                                    'columnDefs': [{
                                       'targets': 0,
                                       'searchable':false,
                                       'orderable':false
                                    }]                                   
                                });   
                                
                                var i = 0;
                                table.columns().every( function () {
                                    var that = this;
                                    if(i!=0){
                                    $( 'input', this.header() ).on( 'keyup change', function () {
                                        if ( that.search() !== this.value ) {
                                            that
                                                .search( this.value )
                                                .draw();
                                        }
                                    } );
                                    i = i+1;
                                  }else{
                                    i = i+1;
                                  }
                                } );
                                
                                var form_data = {
                                    values:$("#sltNumeroA").val(),
                                    saldo:3
                                };
                                $.ajax({
                                    type: 'POST',
                                    url: "consultasBasicas/consultaSaldos.php",
                                    data: form_data,
                                    success: function (data) {                    
                                        localStorage.setItem("data",data);
                                    }
                                }); 
                            });
                        </script>                        
                        <label class="col-sm-1 control-label" for="txtDescripcion">
                            Descripción:    
                        </label>
                        <textarea class="col-sm-1" style="margin-top:-1px;height:26px;width:292px;" class="area" rows="2" name="txtDescripcion" id="txtDescripcion"  maxlength="500" placeholder="Descripción" onkeypress="return txtValida(event,'num_car')"><?php if (!empty($descripcion)) {echo $descripcion;} else {echo '';} ?></textarea>                                                                                        
                    </div>
                    <div class="form-inline form-group" style="margin-top:-15px">                        
                        <label class="col-sm-1 control-label" for="txtDescripcion">
                            Observaciones:
                        </label>
                        <textarea class="col-sm-1" style="height:26px;width:292px;margin-top:-1px;margin-bottom: -30px;" class="area" rows="2" name="txtObservacion" id="txtObservacion"  maxlength="500" placeholder="Observaciónes" onkeypress="return txtValida(event,'num_car')"><?php if (!empty($observaciones)) {echo $observaciones;} else {echo '';} ?></textarea>     
                        <div class="col-sm-offset-8 col-sm-7" style="margin-bottom: -20px;margin-top:-12px">                                    
                            <div class="col-sm-1">
                            <?php if (!empty($idMov) && $estado== 2) { ?>
                                <a id="btnCancelarM" onclick="javascript:cancelarM()" class="btn sombra btn-primary" style="width: 40px" title="Cancelar modificación"><li class="glyphicon glyphicon glyphicon-remove"></li></a>
                            <?php } else {
                            ?>
                                <a id="btnNuevo" onclick="javascript:nuevo()" class="btn sombra btn-primary" style="width: 40px" title="Ingresar nuevo"><li class="glyphicon glyphicon-plus"></li></a>
                                <a id="btnCancelarP" onclick="javascript:cancelarN()" class="btn sombra btn-primary" style="display: none;width: 40px" title="Cancelar ingreso de datos"><li class="glyphicon glyphicon glyphicon-remove"></li></a>
                            <?php
                            }
                            ?>                                        
                            </div>
                            <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
                            <div class="col-sm-1" style="">
                            <?php if (!empty($idMov) && $estado== 2) { ?>
                                <a onclick="javascript:modificarMovimiento()" id="btnModificar" class="btn sombra btn-primary" title="Modificar movimiento"><li class="glyphicon glyphicon-floppy-disk"></li></a>
                            <?php } else {
                                ?>
                                <button type="submit" id="btnGuardar" class="btn sombra btn-primary" title="Guardar movimiento"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                <?php }
                            ?>                                                                               
                            </div>
                            <div class="col-sm-1">
                                <a class="btn sombra btn-primary" id="btnImprimir" title="Imprimir"><li class="glyphicon glyphicon glyphicon-print"></li></a>                                                                              
                            </div> 
                            <script>                                
                                /*$(document).ready(function(){
                                   $("#btnImprimir").click(function(){
                                       var form_data = {
                                           is_ajax:1,
                                           numero:$("#txtNumero").val()
                                       };
                                       var result='';
                                       $.ajax({
                                           type: 'POST',
                                           url:"consultarComprobanteIngreso/impreso.php",
                                           data:form_data,
                                           success: function (data) {
                                               result = JSON.parse(data);
                                               if(result==true){
                                                   window.location.reload();
                                               }
                                               console.log(data);
                                           }
                                       });
                                   }); 
                                });*/

                                function cancelarM(){
                                    var form_data = {
                                        is_ajax: 1,
                                        session: 6
                                    };

                                    $.ajax({
                                        type: 'POST',
                                        url: "consultasBasicas/vaciarSessiones.php",
                                        data: form_data,
                                        success: function (data) {
                                            window.location=data;
                                        }
                                    });
                                    
                                    var form_data = {
                                        is_ajax: 1,
                                        session: 9
                                    };

                                    $.ajax({
                                        type: 'POST',
                                        url: "consultasBasicas/vaciarSessiones.php",
                                        data: form_data,
                                        success: function (data) {
                                            window.location.reload();
                                        }
                                    });
                                }
                                function nuevo() {
                                    $("#btnGuardar").attr('disabled', false);
                                    $("#btnNuevo").css('display', 'none');
                                    $("#btnCancelarP").css('display', 'block');

                                }
                                $(document).ready(function () {
                                    $("#btnGuardar").attr('disabled', true);
                                });
                                function cancelarN() {
                                    $("#btnCancelarP").css('display', 'none');
                                    $("#btnNuevo").css('display', 'block');
                                    $("#btnGuardar").attr('disabled', true);
                                    $("#txtNumeroP").val("");
                                }
                            </script>
                        </div>
                    </div>                    
                    <div class="form-group form-inline" style="margin-top:-40px">                                                    
                    </div>
                </form>
                <!-- Fin de división y contenedor del formulario -->           
            </div>  
            <div class="col-sm-9 text-left col-sm-offset-1" style="margin-top:10px;" align="">                    
                <div class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarDetalleReqAlmacenJson.php" style="margin-top:-15px">
                        <input name="id" id="id" type="hidden" value="<?php echo $id; ?>"/>
                        <input class="hidden" name="asociado" id="asociado" type="text" title=""/>
                        <div class="col-sm-2">
                            <label class="control-label">
                                <strong class="obligado">*</strong>Plan Inventario:
                            </label>
                            <select name="sltPlanInv" id="sltPlanInv" class="form-control" style="width:100px;height:26px;padding:2px;cursor: pointer" title="Seleccione elemento de plan inventario" required="">
                                <?php 
                                echo '<option value="">Plan Inventario</option>';
                                $sql119 = "SELECT id_unico,nombre,codi FROM gf_plan_inventario WHERE tienemovimiento=2";
                                $result119 = $mysqli->query($sql119);    
                                while ($fila119=  mysqli_fetch_row($result119)){
                                    echo '<option value="'.$fila119[0].'">'.$fila119[2].' - '.$fila119[1].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label class="control-label">
                                <strong class="obligado">*</strong>Cantidad:
                            </label>
                            <input type="number" name="txtCantidad" onkeypress="return txtValida(event,'num');" id="txtCantidad" title="Cantidad" maxlength="50" placeholder="Cantidad" style="height:26px;padding:2px;width:100px"/>                                    
                        </div>
                        <div class="col-sm-2">
                            <label class="control-label">
                                <strong class="obligado">*</strong>Valor:
                            </label>
                            <input type="number" name="txtValor" onkeypress="return txtValida(event,'num');" id="txtValor" title="Valor aproximado" maxlength="50" style="height:26px;padding:2px;width:100px" placeholder="Valor"/>                                    
                        </div>
                        <div class="col-sm-2" style="margin-right:30px;">
                            <label class="control-label">
                                <strong class="obligado">*</strong>Iva:
                            </label>
                            <input type="number" name="txtValorIva" onkeypress="return txtValida(event,'num');"  placeholder="Iva" id="txtValorIva" title="Iva" maxlength="50" style="height:26px;padding:2px;width:100px" readonly/>                                    
                        </div>
                        <div class="col-sm-2" style="margin-right:30px;">
                            <label class="control-label">
                                <strong class="obligado">*</strong>Valor Total:
                            </label>
                            <input type="number" name="txtValorTotal" placeholder="Valor Total" onkeypress="return txtValida(event,'num');" id="txtValorTotal" title="Valor total" maxlength="50" style="height:26px;padding:2px;width:100px" required="" readonly/>                                    
                        </div>
                        <div class="col-sm-1" align="left" style="margin-top:20px;margin-left:-80px;margin-right:30px; ">
                            <button  type="submit" class="btn btn-primary sombra" id="btnGuardarDetalle"><li class="glyphicon glyphicon-floppy-disk"></li></button>                                
                            <script type="text/javascript" >
                            $("#sltNumeroA").change(function(){
                                $("#asociado").val($("#sltNumeroA").val());
                            });
                            $(document).ready(function(){                        
                                var indicador = '<?php echo $idMov?>';
                                if(indicador==0 || indicador=="" || indicador.length==0){
                                    $("#btnGuardarDetalle").prop('disabled',true);
                                }else{
                                    $("#btnGuardarDetalle").prop('disabled',false);
                                }
                            });
                            </script>
                            <input type="hidden" name="MM_insert" >
                        </div>
                    </form>
                </div>
                <script type="text/javascript" >
                    var valor = 0.00;
                    var iva = 0.00;
                    var totalP = 0.00;
                    var totalIva = 0.00;
                    var total = 0.00;
                    $("#txtValor").keyup(function(){
                        var cantidad = $("#txtCantidad").val();
                        if(cantidad ===0 || cantidad ==="" || cantidad.length === 0 || cantidad===null){
                            cantidad = 1;
                        }else{
                            cantidad = parseFloat($("#txtCantidad").val());
                        }
                        valor = ($("#txtValor").val());
                        iva = parseFloat(<?php echo $porcIva; ?>);
                        total = cantidad*valor;               
                        totalIva = (total*iva)/100;
                        $("#txtValorIva").val(totalIva.toFixed(2));
                        total = total+totalIva;
                        $("#txtValorTotal").val(total.toFixed(2));
                    });
                </script>
            </div>
            <?php 
    $sumC = 0;
    $sumV = 0;
    $sumIva = 0;
    ?>
    <input type="hidden" id="idPrevio" value="">
    <input type="hidden" id="idActual" value="">  
    <div class="col-sm-11">
        <div class="table-responsive col-sm-offset-1" style="margin-right: 0px; margin-top:-15px;">
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">                            
                <table id="tablaMovimiento" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                    <label style="font-size:10px">
                    <thead>
                        <tr>                                                                    
                            <td width="7%" class="cabeza"></td>
                            <td class="cabeza"><strong>Item</strong></td>
                            <td class="cabeza"><strong>Plan Inventario</strong></td>
                            <td class="cabeza"><strong>Cantidad</strong></td>
                            <td class="cabeza"><strong>Valor Aproximado</strong></td>
                            <td class="cabeza"><strong>Iva</strong></td>
                            <td class="cabeza"><strong>Valor Total</strong></td>
                        </tr>
                        <tr>                                                                    
                            <th width="7%"  class="cabeza"></th>
                            <th class="cabeza"><strong>Item</strong></th>
                            <th class="cabeza"><strong>Plan Inventario</strong></th>
                            <th class="cabeza"><strong>Cantidad</strong></th>
                            <th class="cabeza"><strong>ValorAproximado</strong></th>
                            <th class="cabeza"><strong>Iva</strong></th>
                            <th class="cabeza"><strong>Valor Total</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $item=1;
                        $totalmov=0;
                        
                            $det = "SELECT  dtm.id_unico,
                                            dtm.planmovimiento,
                                            dtm.cantidad,
                                            dtm.valor,
                                            pl.id_unico,
                                            pl.nombre,
                                            dtm.iva,
                                            pl.codi
                            FROM gf_detalle_movimiento dtm
                            LEFT JOIN gf_movimiento mv ON dtm.movimiento = mv.id_unico
                            LEFT JOIN gf_plan_inventario pl ON dtm.planmovimiento = pl.id_unico
                            WHERE md5(mv.id_unico)='$idMov'";
                            $resultado2 = $mysqli->query($det);
                        
                        while ($row2 = mysqli_fetch_row($resultado2)) { ?>
                        <tr>                            
                            <td class="campos">
                                <a href="#<?php echo $row2[0];?>" title="Eliminar" onclick="javascript:eliminar(<?php echo $row2[0];?>);">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </a>
                                <a href="#<?php echo $row2[0];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row2[0]; ?>);return calcular(<?php echo $row2[0]; ?>)">
                                    <li class="glyphicon glyphicon-edit"></li>
                                </a>
                            </td>
                            <td class="campos" class="text-left" width="7%">                                                                            
                                <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblItem'.$row2[0].'">'.$item++.'</label>'; ?>
                            </td>
                            <td class="campos" class="text-left">                             
                              <?php                               
                              echo '<label  class="valorLabel" style="font-weight:normal" id="lblCodigoE'.$row2[0].'">'.$row2[7].' - '.$row2[5].'</label>'; ?>
                                <select class="col-sm-12 campoD" name="sltPlanInventario<?php echo $row2[0] ?>" id="sltPlanInventario<?php echo $row2[0] ?>" title="Seleccione elemento de plan inventario" style="display:none;padding:2px">
                                    <?php 
                                     echo '<option value="'.$row2[4].'">'.$row2[7].' - '.$row2[5].'</option>';
                                     $sqlPL = "SELECT id_unico,nombre,codi FROM gf_plan_inventario WHERE tienemovimiento=2 AND id_unico!=$row2[4]";
                                     $resultPL = $mysqli->query($sqlPL);
                                     while ($filaPL = mysqli_fetch_row($resultPL)){
                                         echo '<option value="'.$filaPL[0].'">'.$filaPL[2].' - '.$filaPL[1].'</option>';
                                     }
                                    ?>
                                </select>
                            </td>
                            <td class="campos" class="text-right">                              
                                <?php 
                                $sumC+=$row2[2];
                                echo '<label class="valorLabel" style="font-weight:normal" id="lblCantidad'.$row2[0].'">'.$row2[2].'</label>'; 
                                echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="number" name="txtcantidad'.$row2[0].'" id="txtcantidad'.$row2[0].'" value="'.$row2[2].'" />';
                                ?>
                            </td>
                            <td class="campos" class="text-right">                           
                                <?php 
                                $sumV+=$row2[3];
                                echo '<label class="valorLabel" style="font-weight:normal" id="ValorT'.$row2[0].'">'.$row2[3].'</label>'; 
                                echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="number" name="txtvalor'.$row2[0].'" id="txtvalor'.$row2[0].'" value="'.$row2[3].'" />';
                                ?>
                            </td>
                            <td class="campos" class="text-right">
                                <?php 
                                $sumIva +=$row2[6];
                                echo '<label class="valorLabel" style="font-weight:normal" id="lblIva'.$row2[0].'">'.$row2[6].'</label>'; 
                                echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="number" name="txtiva'.$row2[0].'" id="txtiva'.$row2[0].'" value="'.$row2[6].'" readonly/>';
                                ?>
                            </td>
                            <td class="campos" style="height:10px;font-size:10px"class="text-right">
                                <?php 
                                $mov=(($row2[2]*$row2[3]));                              
                                $total = $mov+$row2[6];
                                $totalmov+=$total;
                                echo '<label class="valorLabel" style="font-weight:normal" id="lblValorTotal'.$row2[0].'">'.number_format($total, 2, '.', ',').'</label>'; 
                                echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-9 campoD text-left"  type="number" name="txttotal'.$row2[0].'" id="txttotal'.$row2[0].'" value="'.$total.'" readonly/>';
                                 ?>
                                <div >
                                    <table id="tab<?php echo $row2[0] ?>" style="padding:0px;background-color:transparent;background:transparent;" class="col-sm-1">
                                        <tbody>
                                            <tr style="background-color:transparent;">
                                                <td style="background-color:transparent;">
                                                    <a  href="#<?php echo $row2[0];?>" title="Guardar" id="guardar<?php echo $row2[0]; ?>" style="display: none;" onclick="javascript:guardarCambios(<?php echo $row2[0]; ?>)">
                                                        <li class="glyphicon glyphicon-floppy-disk"></li>
                                                    </a>
                                                </td>
                                                <td style="background-color:transparent;">
                                                    <a href="#<?php echo $row2[0];?>" title="Cancelar" id="cancelar<?php echo $row2[0] ?>" style="display: none" onclick="javascript:cancelar(<?php echo $row2[0];?>)" >
                                                        <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>                
                        </tr>
                        <?php }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
        </div>
    <div class="col-sm-9 col-sm-offset-1" id="totales" style="margin-bottom:-20px"> 
    <div class="col-sm-1 col-sm-offset-3" style="margin-right:30px">
        <div class="form-group" style="" align="left">                                    
            <label class="control-label valorLabel">
                <strong class="valorLabel">Totales:</strong>
            </label>                                
        </div>
    </div> 
    <div class="col-sm-1" style="margin-right:40px">
        <label class="control-label valorLabel" style="font-size:10px;" title="Total cantidad"><?php echo number_format($sumC, 2, '.', ','); ?></label>
    </div>  
    <div class="col-sm-1" style="margin-right:40px">
        <label class="control-label valorLabel" style="font-size:10px;" title="Total valor"><?php echo number_format($sumV, 2, '.', ','); ?></label>
    </div>  
    <div class="col-sm-1" style="margin-right:40px">
        <label class="control-label valorLabel" style="font-size:10px;" title="Total iva"><?php echo number_format($sumIva, 2, '.', ','); ?></label>
    </div>  
    <div class="col-sm-1" >
        <label class="control-label valorLabel" style="font-size:10px;" title="Total valor total"><?php echo number_format($totalmov, 2, '.', ','); ?></label>
    </div>  
</div>
    <div>             
</div>                            
  </div>  
 <script type="text/javascript">     
    function modificar(id){
        if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
            //Labels 
            var lblCodigoE = 'lblCodigoE'+$("#idPrevio").val();                        
            var lblCantidadE = 'lblCantidad'+$("#idPrevio").val();                        
            var ValorTE = 'ValorT'+$("#idPrevio").val();                        
            var lblIvaE = 'lblIva'+$("#idPrevio").val();                        
            var lblValorTotalE = 'lblValorTotal'+$("#idPrevio").val();                        
            
            //Campos para cancelar y guardar cambios
            var guardarC = 'guardar'+$("#idPrevio").val();
            var cancelarC = 'cancelar'+$("#idPrevio").val();
            var tablaC = 'tab'+$("#idPrevio").val();
            
            //Campos ocultos
            var sltPlanInventarioE = 'sltPlanInventario'+$("#idPrevio").val();  
            var txtCantidadE = 'txtcantidad'+$("#idPrevio").val();                        
            var txtValorE = 'txtvalor'+$("#idPrevio").val();
            var txtIvaE = 'txtiva'+$("#idPrevio").val();
            var txtValorTE = 'txttotal'+$("#idPrevio").val();
            //Se mustran los labels
            $("#"+lblCodigoE).css('display','block');
            $("#"+lblCantidadE).css('display','block');
            $("#"+ValorTE).css('display','block');
            $("#"+lblIvaE).css('display','block');
            $("#"+lblValorTotalE).css('display','block');
            
            //Se ocultan los campos
            $("#"+sltPlanInventarioE).css('display','none');
            $("#"+txtCantidadE).css('display','none');
            $("#"+txtValorE).css('display','none');
            $("#"+txtIvaE).css('display','none');
            $("#"+txtValorTE).css('display','none');
             
            //se mantienen ocultos
            $("#"+guardarC).css('display','none');
            $("#"+cancelarC).css('display','none');
            $("#"+tablaC).css('display','none'); 
        }
        //Labels
        var lblCodigo  = 'lblCodigoE'+id;
        var lblCantidad  = 'lblCantidad'+id;
        var ValorT  = 'ValorT'+id;
        var lblIva  = 'lblIva'+id;
        var lblValorTotal  = 'lblValorTotal'+id;
        
        //campos
        var sltPlanInventario = 'sltPlanInventario'+id;  
        var txtCantidad = 'txtcantidad'+id;  
        var txtValor = 'txtvalor'+id; 
        var txtIva = 'txtiva'+id;
        var txtValorT = 'txttotal'+id;
        
        //campos para cancelar y guardar cambios
        var guardar = 'guardar'+id;
        var cancelar = 'cancelar'+id;
        var tabla = 'tab'+id; 
        
        //Se ocultan los labels
        $("#"+lblCodigo).css('display','none');
        $("#"+lblCantidad).css('display','none');
        $("#"+ValorT).css('display','none');
        $("#"+lblIva).css('display','none');
        $("#"+lblValorTotal).css('display','none');
        
        //Se muestran los campos ocultos
        $("#"+sltPlanInventario).css('display','block');
        $("#"+txtCantidad).css('display','block');
        $("#"+txtValor).css('display','block');
        $("#"+txtIva).css('display','block');
        $("#"+txtValorT).css('display','block');
               
        //Se muestran los campos
        $("#"+guardar).css('display','block');
        $("#"+cancelar).css('display','block');
        $("#"+tabla).css('display','block');
        
        //Carga de la id actual
        $("#idActual").val(id);
        
        //carga del campo oculto con la id anterior
        if($("#idPrevio").val() != id){
            $("#idPrevio").val(id);   
        }
       }

    function cancelar(id){
        //labels
        var lblCodigo  = 'lblCodigoE'+id;
        var lblCantidad  = 'lblCantidad'+id;
        var ValorT  = 'ValorT'+id;
        var lblIva  = 'lblIva'+id;
        var lblValorTotal  = 'lblValorTotal'+id;
        
        //campos
        var sltPlanInventario = 'sltPlanInventario'+id;  
        var txtCantidad = 'txtcantidad'+id;  
        var txtValor = 'txtvalor'+id; 
        var txtIva = 'txtiva'+id;
        var txtValorT = 'txttotal'+id;
        
        //campos para cancelar y guardar cambios
        var guardar = 'guardar'+id;
        var cancelar = 'cancelar'+id;
        var tabla = 'tab'+id; 
        
        //se muestran los labels
        $("#"+lblCodigo).css('display','block');
        $("#"+lblCantidad).css('display','block');
        $("#"+ValorT).css('display','block');
        $("#"+lblIva).css('display','block');
        $("#"+lblValorTotal).css('display','block');
        
        //Se ocultan los campos
        $("#"+sltPlanInventario).css('display','none');
        $("#"+txtCantidad).css('display','none');
        $("#"+txtValor).css('display','none');
        $("#"+txtIva).css('display','none');
        $("#"+txtValorT).css('display','none');
        
        //Se ocultan los campos
        $("#"+guardar).css('display','none');
        $("#"+cancelar).css('display','none');
        $("#"+tabla).css('display','none');               
    }
    function guardarCambios(id){
        var sltPlanInventario = 'sltPlanInventario'+id;  
        var txtCantidad = 'txtcantidad'+id;  
        var txtValor = 'txtvalor'+id; 
        var txtIva = 'txtiva'+id;
        

        var form_data = {
            is_ajax:1,
            id:+id,
            planI:$("#"+sltPlanInventario).val(),
            cantidad:$("#"+txtCantidad).val(),
            valor:$("#"+txtValor).val(),                    
            iva:$("#"+txtIva).val(),
        };
        var result='';
        $.ajax({
            type: 'POST',
            url: "json/modificarDetalleReqAlmacenJson.php",
            data:form_data,
            success: function (data) {
                result = JSON.parse(data);                        
                if (result==true) {
                    $("#mdlModificado").modal('show');
                }else {                                
                    $("#mdlNomodificado").modal('show');
                }                                                                        
            }
        });
    }
    
    function eliminar(id){
        var result = '';
        $("#myModal").modal('show');
        $("#ver").click(function(){
            $("#mymodal").modal('hide');
            $.ajax({
                type:"GET",
                url:"json/eliminarDetalleReqAlmacen.php?id="+id,
                success: function (data) {
                result = JSON.parse(data);
                if(result==true)
                  $("#mdlEliminado").modal('show');
                else
                  $("#mdlNoeliminado").modal('show');
                }
            });
        });
    }
    
    function calcular(id){
        var txtCantidad = 'txtcantidad'+id;  
        var txtValor = 'txtvalor'+id; 
        var txtIva = 'txtiva'+id;
        var txtValorT = 'txttotal'+id;
        
        $("#"+txtValor).keyup(function(){
            var cantidad = $("#"+txtCantidad).val();
            if(cantidad ===0 || cantidad ==="" || cantidad.length === 0 || cantidad===null){
                cantidad = 1;
            }else{
                cantidad = parseFloat($("#"+txtCantidad).val());
            }
            valor = parseFloat($("#"+txtValor).val());
            iva = parseFloat(<?php echo $porcIva; ?>);
            total = cantidad*valor;               
            totalIva = (total*iva)/100;
            $("#"+txtIva).val(totalIva.toFixed(2));
            total = total+totalIva;
            $("#"+txtValorT).val(total.toFixed(2));
        });
    }
    
    function modificarMovimiento(){
        var form_data = {
            id:$("#id").val(),
            movimiento:$("#sltTipoMovimiento").val(),
            numero:$("#txtNumeroMovimiento").val(),
            tercero:$("#sltTercero").val(),
            fecha:$("#fecha").val(),
            dependecia:$("#sltDependencia").val(),
            responsable:$("#sltResponsable").val(),
            centroCosto:$("#sltCentroCosto").val(),
            rubroPP:$("#sltRubroP").val(),
            plazoE:$("#txtPlazoE").val(),
            unidadP:$("#sltUPE").val(),
            proyecto:$("#sltProyecto").val(),
            lugarE:$("#sltLE").val(),
            descripcion:$("#txtDescripcion").val(),
            observacion:$("#txtObservacion").val()
        };
        var result = '';
        $.ajax({
            type: 'POST',
            url: "json/modificarMovimientoAlmacen.php",
            data:form_data,
            success: function (data) {
                result = JSON.parse(data);
                console.log(data);
                if (result==true) {
                    $("#mdlModificado").modal('show');
                }else {                                
                    $("#mdlNomodificado").modal('show');
                }
            }
        });
    }
    </script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2").select2();         			
    </script>
</div>
<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php' ?>  
</body>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de Movimiento de Almacén?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mdlEliminado" role="dialog" align="center" >
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
<div class="modal fade" id="mdlNoeliminado" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modales de modificado -->
<div class="modal fade" id="mdlModificado" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Información modificada correctamente.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mdlNomodificado" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">          
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se ha podido modificar la información.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalTipo" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">          
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Seleccione tipo de movimiento.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnD" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("#btnD").click(function(){
        $("#sltTipoMovimiento").focus();
    });
    
    $('#btnModifico').click(function(){
        document.location.reload();
    });
</script>
<script type="text/javascript">
    $('#btnNoModifico').click(function(){
        document.location.reload();
    });
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
<script type="text/javascript">
    $('#btnG').click(function(){
        document.location.reload();
    });
</script>
<script type="text/javascript">    
    $('#btnG2').click(function(){  
        document.location.reload();
    });
    
    function limpiarCampos(){
        $('#sltPlanInv').prop('selectedIndex',0);
        $("#txtCantidad").val('');
        $("#txtValor").val('');
        $("#txtValorIva").val('');
        $("#txtValorTotal").val('');
    }
</script>
</html>