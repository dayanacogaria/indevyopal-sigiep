<?php 
//llamado a la clase de conexion
  require_once('Conexion/conexion.php');
  require_once('head_listar.php');  
 ?>
 <link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script type="text/javascript" src="js/md5.js" ></script>
<script type="text/javascript">
    $(document).ready(function(){
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
/*
 * @param type{string} or {number} e
 * @returns {/\d/.test(String.fromCharCode(keynum))}
 */
    function justNumbers(e){   
        var keynum = window.event ? window.event.keyCode : e.which;
        if ((keynum == 8) || (keynum == 46) || (keynum == 45))
        return true;
        return /\d/.test(String.fromCharCode(keynum));
    }
    
    $(document).ready(function(){
        var i= 1;
    $('#tablaRequisiciones thead th').each( function () {
        if(i != 0){ 
        var title = $(this).text();
        switch (i){
            case 0:
                $(this).html( '<input type="text" style="width:110%;" placeholder="Filtrar" class="campos"/>' );
            break;
            case 1:
                $(this).html( '<input type="text" style="width:110%;" placeholder="Filtrar" class="campos"/>' );
            break;
            case 2:
                $(this).html( '<input type="text" style="width:110%;" placeholder="Filtrar" class="campos"/>' );
            break;
            case 3:
                $(this).html( '<input type="text" style="width:110%;" placeholder="Filtrar" class="campos"/>' );
            break;
            case 4:
                $(this).html( '<input type="text" style="width:110%;" placeholder="Filtrar" class="campos"/>' );
            break;          
        }
        i = i+1;
      }else{
        i = i+1;
      }
    } ); 
        var tabla = $("#tablaRequisiciones").DataTable({
            "autoFill": true,
            "scrollX": true,
            "processing": true,            
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
        tabla.columns().every( function () {
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
    });     
</script>
<style>
table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px}
    .cabeza{
        white-space:nowrap;
        padding: 20px;
    }
    .campos{
        padding: 0px;
        font-size: 10px
    }
    .valorLabel{
        font-size: 10px;
    }
    .valorLabel:hover{
        cursor: pointer;
        color:#1155CC;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        white-space:nowrap;
    }
    body,.select2-container.form-control,.select2-container .select2-choice{
        font-size: 10px;
        height:30px
    }
</style>

<!-- Llamado a la cabecera del formulario -->
<title>Orden de Compra</title>
<style>
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
table.dataTable tbody td,table.dataTable tbody td{padding:1px}
.dataTables_wrapper .ui-toolbar{padding:2px}
</style>
</head>
<body onload="limpiarCampos()"> 
<!-- contenedor principal -->  
<div class="container-fluid text-center">
    <div class="row content">
      <?php require_once 'menu.php'; ?>
<!-- Llamado al menu del formulario -->    
        <div class="col-sm-10 text-left" style="font-size:9px;margin-left: -16px; margin-right:4px;">         
            <h2 align="center" style="margin-top: -5px" class="tituloform">Orden de Compra</h2>
            <div style="margin-top:-7px; border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">        
<!-- inicio del formulario --> 
            <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarOrdenCompraRFMOVJson.php">            
                    <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                        Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                    </p>            
                    <input class="hidden" name="url" type="hidden" value="<?php echo basename($_SERVER['PHP_SELF']).'?movimiento='.$_GET['movimiento']; ?>"/>                    
                    <?php                    
                    $id=0;
                    $idMov='';
                    $tipoMovimiento=0;
                    $numeroMovimiento=0;
                    $fecha='';
                    $dependencia=0;
                    $responsable=0;
                    $centroCosto=0;
                    $proyecto=0;
                    $estado=0;
                    $descripcion = '';
                    $observaciones = '';
                    $tercero = 0;
                    $rubroPresupuestal = 0;
                    $porcIva = "";
                    $PlazoEntrega = 0;
                    $unidadPlazo=0;
                    $lugarEntrega=0;
                    $idR = 0;                    
                    $idReq = 0;
                    if(!empty($_GET['movimiento'])){
                        if(!empty($_GET['orden'])){
                            $idMov = $_GET['orden'];
                            $sql = "SELECT mv.id_unico,
                                        mv.tipomovimiento,
                                        mv.numero,                                        
                                        mv.tercero2,
                                        mv.fecha
                                 FROM gf_movimiento mv
                                 WHERE md5(mv.id_unico)='$idMov'";
                            $result=$mysqli->query($sql);
                            $rowO = mysqli_fetch_row($result);
                            $id = $rowO[0];
                            $tipoMovimiento = $rowO[1];
                            $numeroMovimiento = $rowO[2];                            
                            $fecha=$rowO[4];
                        }
                        
                        $idReq = $_GET['movimiento'];
                        $sqlOrd="SELECT mv.id_unico,
                                        mv.tipomovimiento,
                                        mv.numero,
                                        mv.fecha,
                                        mv.dependencia,
                                        mv.tercero,
                                        mv.centrocosto,
                                        mv.proyecto,
                                        mv.estado,
                                        mv.rubropptal,
                                        mv.tercero2,
                                        mv.plazoentrega,
                                        mv.unidadentrega,
                                        mv.lugarentrega,
                                        mv.descripcion,
                                        mv.observaciones,
                                        mv.porcivaglobal
                                 FROM gf_movimiento mv
                                 WHERE md5(mv.id_unico)='$idReq'";
                        $resultOrd=$mysqli->query($sqlOrd);
                        $rowOrd=  mysqli_fetch_row($resultOrd);                        
                            
                        $dependencia=$rowOrd[4];
                        $responsable=$rowOrd[5];
                        $centroCosto=$rowOrd[6];
                        $proyecto=$rowOrd[7];
                        $estado=$rowOrd[8];
                        $rubroPresupuestal=$rowOrd[9];
                        $tercero = $rowOrd[10];
                        $PlazoEntrega=$rowOrd[11];
                        $unidadPlazo=$rowOrd[12];
                        $lugarEntrega=$rowOrd[13];
                        $descripcion=$rowOrd[14];
                        $observaciones=$rowOrd[15];
                        $porcIva=$rowOrd[16];
                        $sqlEstado = "SELECT nombre FROM gf_estado_movimiento WHERE id_unico = $estado";
                        $resEstado = $mysqli->query($sqlEstado);
                        $estdo = mysqli_fetch_row($resEstado);                                                
                    }
                    
                    if(!empty($_GET['orden'])){
                        $idMov = $_GET['orden'];                        
                        $sqlOrd="SELECT mv.id_unico,
                                        mv.tipomovimiento,
                                        mv.numero,
                                        mv.fecha,
                                        mv.dependencia,
                                        mv.tercero,
                                        mv.centrocosto,
                                        mv.proyecto,
                                        mv.estado,
                                        mv.rubropptal,
                                        mv.tercero2,
                                        mv.plazoentrega,
                                        mv.unidadentrega,
                                        mv.lugarentrega,
                                        mv.descripcion,
                                        mv.observaciones,
                                        mv.porcivaglobal
                                 FROM gf_movimiento mv
                                 WHERE md5(mv.id_unico)='$idMov'";
                        $resultOrd=$mysqli->query($sqlOrd);
                        $rowOrd=  mysqli_fetch_row($resultOrd);
                        
                        $id=$rowOrd[0];
                        $tipoMovimiento=$rowOrd[1];
                        $numeroMovimiento=$rowOrd[2];
                        $fecha=$rowOrd[3];
                        $dependencia=$rowOrd[4];
                        $responsable=$rowOrd[5];
                        $centroCosto=$rowOrd[6];
                        $proyecto=$rowOrd[7];
                        $estado=$rowOrd[8];
                        $rubroPresupuestal=$rowOrd[9];
                        $tercero=$rowOrd[10];
                        $PlazoEntrega=$rowOrd[11];
                        $unidadPlazo=$rowOrd[12];
                        $lugarEntrega=$rowOrd[13];
                        $descripcion=$rowOrd[14];
                        $observaciones=$rowOrd[15];
                        $porcIva=$rowOrd[16];
                        $sqlEstado = "SELECT nombre FROM gf_estado_movimiento WHERE id_unico = $estado";
                        $resEstado = $mysqli->query($sqlEstado);
                        $estdo = mysqli_fetch_row($resEstado);                        
                    }                                        
                    ?>
                    <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;">
                        <label for="fecha" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>Tipo Clase Asocida:
                        </label>
                        <a href="#" onclick="return requisiciones()" class="col-sm-1 btn btn-primary" data-toggle="modal" title="Requisiciones" style="width:100px;height:30px;box-shadow: 1px 1px 1px 1px gray;"><li class="glyphicon glyphicon-tags"></li></a>
                        <script type="text/javascript" >
                            function requisiciones(){
                                $("#modalRequisicones").modal('show');
                            }
                        </script>
                        <label for="sltTipoMovimiento" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>Tipo Movimiento:
                        </label>                                
                        <select class="col-sm-1 input-sm"  name="sltTipoMovimiento" id="sltTipoMovimiento" class="form-control" style="width:100px;height:30px;cursor: pointer;" title="Seleccione tipo de movimiento" required>
                            <?php
                            if (!empty($tipoMovimiento)) {
                                $sql1 = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_movimiento WHERE id_unico = $tipoMovimiento AND clase=1";
                                $result1 = $mysqli->query($sql1);
                                $fila1 = mysqli_fetch_row($result1);
                                echo '<option value="' . $fila1[0] . '">' . ucwords(strtolower($fila1[1])) . '</option>';
                                $sql2 = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_movimiento WHERE id_unico != $tipoMovimiento AND clase=1";
                                $result2 = $mysqli->query($sql2);
                                while ($fila2 = mysqli_fetch_row($result2)) {
                                    echo '<option value="' . $fila2[0] . '">' . ucwords(strtolower($fila2[1])) . '</option>';
                                }
                            } else {
                                ?>
                                <option value="">Tipo Movimiento</option>
                                <?php
                                $sql3 = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_movimiento WHERE clase=1";
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
                        <input class="col-sm-1 input-sm" type="text" name="txtNumeroMovimiento" id="txtNumeroMovimiento" class="form-control" maxlength="50" style="width:100px;height:30px" placeholder="N° movimiento" title="Número de movimiento" value="<?php if (!empty($numeroMovimiento)) {echo $numeroMovimiento;} ?>" required/>
                        <!--<a id="btnBuscar" class="btn " title="Buscar Comprobante" style="margin-left:-385px;margin-top:-2px;padding:3px 3px 3px 3px"><li class="glyphicon glyphicon-search"></li></a>-->
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
                            
                            $("#txtNumeroMovimiento").keyup(function(){
                                $("#txtNumeroMovimiento").autocomplete({
                                    source:"consultasBasicas/autocompletadoOrdenC.php",
                                    minlength:5
                                });
                            });
                            
                            $("#btnBuscar").click(function(){    
                                var tipoF = $("#txtNumeroMovimiento").val();
                                if(tipoF=='""' || tipoF==0 ){
                                    window.location.reload();
                                }else {
                                    var form_data = {                                            
                                        numero:+$("#txtNumeroMovimiento").val(),                                            
                                        existente:7
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
                        <label class="col-sm-1 control-label" for="txtDescripcion">
                            <strong class="obligado">*</strong>Tercero:
                        </label>
                        <select class="form-control col-sm-1 input-sm select2" name="sltTercero" id="sltTercero" id="single" title="Seleccione un tercero para consultar" style="width:292px;height:30px;" required>
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
                    <div class="form-group form-inline" style="margin-left: 5px;margin-top:-15px">
                        <label for="txtIva" class="col-sm-1 control-label" style="margin-top:-5px">
                            <strong class="obligado">*</strong>% Iva:
                        </label>
                        <input class="col-sm-1" type="number" onkeypress="return txtValida(event,'num')" name="txtIva" id="txtIva" class="form-control" style="width:100px;height:30px;margin-top:-5px" value="<?php if (!empty($porcIva)) {echo ucwords(strtolower($porcIva));} else {echo '0';} ?>" title="Ingrese porcentaje de iva" placeholder="% Iva" />                        
                        <label for="fecha" class="col-sm-1 control-label" style="margin-top:-5px">
                            <strong class="obligado">*</strong>Fecha:
                        </label>                                
                        <input class="col-sm-1 input-sm" value="<?php if (!empty($fecha)) {$fechaS = explode("-", $fecha);echo $fechaS[2] . '/' . $fechaS[1] . '/' . $fechaS[0];} else {echo date('d/m/Y');} ?>" type="text" name="fecha" id="fecha" class="form-control" style="width:100px;height:30px;margin-top:-5px" title="Ingrese la fecha" placeholder="Fecha" required>                                                                                                                        
                        <label for="sltDependencia" class="col-sm-1 control-label" style="margin-top:-5px">
                            <strong class="obligado">*</strong>Dependencia:
                        </label>                         
                        <select class="col-sm-1 input-sm"  name="sltDependencia" id="sltDependencia" class="form-control" style="width:100px;height:30px;cursor: pointer;margin-top:-5px" title="Seleccione dependecia " required>
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
                        }
                        ?>                        
                        </select>
                        <label for="sltResponsable" class="col-sm-1 control-label" style="margin-top:-5px">
                            <strong class="obligado">*</strong>Responsable:
                        </label>                                
                        <select class="col-sm-1 input-sm"  name="sltResponsable" id="sltResponsable" class="form-control" style="width:100px;height:30px;cursor: pointer;margin-top:-5px" title="Seleccione responsable" required>                        
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
                            echo '<option value="">Responsable</option>';
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
                        <label for="sltCentroCosto" class="col-sm-1 control-label" style="margin-top:-5px">
                            <strong class="obligado">*</strong>Centro Costo:
                        </label>  
                        <select class="col-sm-1 input-sm"  name="sltCentroCosto" id="sltCentroCosto" class="form-control" style="width:100px;height:30px;cursor: pointer;margin-top:-5px" title="Seleccione centro costo" required>
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
                    </div>
                    <div class="form-group form-inline" style="margin-left: 5px;margin-top:-15px">
                        <label for="sltRubroP" class="col-sm-1 control-label" style="margin-top:-5px">
                            <strong class="obligado">*</strong>Rubro Presupuestal:
                        </label>              
                        <select class="col-sm-1 input-sm"  name="sltRubroP" id="sltRubroP" class="form-control" style="width:100px;height:30px;cursor: pointer;margin-top:-5px" title="Seleccione rubro presupuestal " required>
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
                                    ?>
                                <?php
                            }
                            ?>                        
                        </select>                                             
                        <label for="txtEstado" class="col-sm-1 control-label" style="margin-top:-5px">
                            <strong class="obligado">*</strong>Plazo Entrega:
                        </label>      
                        <input class="col-sm-2" type="number" onkeypress="return txtValida(event,'num')" name="txtPlazoE" id="txtPlazoE" class="form-control" style="margin-top:-5px;width:100px;height:30px" value="<?php if (!empty($PlazoEntrega)) {echo ucwords(strtolower($PlazoEntrega));} else {echo '0';} ?>" title="Ingrese plazo de entrega" placeholder="Plazo Entrega"/>
                        <label for="sltUPE" class="col-sm-1 control-label" style="margin-top:-5px">
                            <strong class="obligado">*</strong>U. Plazo Entrega:
                        </label>
                        <select class="col-sm-1 input-sm"  name="sltUPE" id="sltUPE" class="form-control" style="width:100px;height:30px;cursor: pointer;margin-top:-5px" title="Seleccione rubro presupuestal " required>
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
                        <label for="sltProyecto" class="col-sm-1 control-label" style="margin-top:-5px">
                            <strong class="obligado">*</strong>Proyecto:                        
                        </label>  
                        <select class="col-sm-1 input-sm"  name="sltProyecto" id="sltProyecto" class="form-control" style="width:100px;height:30px;cursor: pointer;margin-top:-5px" title="Seleccione proyecto" required >
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
                        }
                        ?>                        
                        </select>
                         <label for="txtEstado" class="col-sm-1 control-label" style="margin-top:-5px">
                            Estado:
                        </label>      
                        <?php
                        $sql = "SELECT id_unico,nombre FROM gf_estado_movimiento WHERE id_unico = 2";
                        $result = $mysqli->query($sql);
                        $row = mysqli_fetch_row($result);
                        ?>
                        <input class="col-sm-2" type="text" name="txtEstado" id="txtEstado" class="form-control" style="width:100px;height:30px;margin-top:-5px" value="<?php if (!empty($estado)) {echo ucwords(strtolower($estdo[0]));} else {echo ucwords(strtolower($row[1]));} ?>" title="Estado" placeholder="Estado" readonly/>
                    </div>
                    <div class="form-group form-inline" style="margin-left: 5px;margin-top:-15px">
                        <label for="sltLE" class="col-sm-1 control-label" style="margin-top:-5px">
                            <strong class="obligado">*</strong>Lugar Entrega:
                        </label>
                        <select class="col-sm-1 input-sm"  name="sltLE" id="sltLE" class="form-control" style="width:100px;height:30px;cursor: pointer;margin-top:-5px" title="Seleccione rubro presupuestal " required>
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
                                ?>
                                <?php
                            }
                            ?>                        
                        </select>
                        <label class="col-sm-1 control-label" for="txtDescripcion" style="margin-top:-5px">
                            Descripción:
                        </label>
                        <textarea class="col-sm-1" style="height:30px;width:292px;margin-top:-1px;margin-top:-5px" class="area" rows="2" name="txtDescripcion" id="txtDescripcion"  maxlength="500" placeholder="Descripción" onkeypress="return txtValida(event,'num_car')"><?php if (!empty($descripcion)) {echo $descripcion;} else {echo '';} ?></textarea>  
                        <label class="col-sm-1 control-label" for="txtDescripcion" style="margin-top:-5px">
                            Observaciones:
                        </label>
                        <textarea class="col-sm-1" style="height:30px;width:292px;margin-top:-1px;margin-top:-5px" class="area" rows="2" name="txtObservacion" id="txtObservacion"  maxlength="500" placeholder="Observaciónes" onkeypress="return txtValida(event,'num_car')"><?php if (!empty($observaciones)) {echo $observaciones;} else {echo '';} ?></textarea>                                                                                        
                    </div>
                    <div class="form-group form-inline" style="margin-top:-10px">                            
                        <div class="col-sm-offset-8 col-sm-8" style="margin-bottom:-22px">     
                            <div class="col-sm-1" style="">
                            <?php if (!empty($idMov) && $estado== 2) { ?>
                                <a onclick="javascript:modificarOrden()" id="btnModificar" class="btn sombra btn-primary" title="Modificar orden de compra"><li class="glyphicon glyphicon-floppy-disk"></li></a>
                            <?php } else {
                                ?>
                                <button type="submit" id="btnGuardar" class="btn sombra btn-primary" title="Guardar orden de compra"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                <?php }
                            ?>                                                                               
                            </div>
                            <div class="col-sm-1">
                                <a class="btn sombra btn-primary" id="btnImprimir" title="Imprimir"><li class="glyphicon glyphicon glyphicon-print"></li></a>                                                                              
                            </div> 
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
                                    window.location="RF_ORDEN_DE_COMPRA.php";
                                }
                                function nuevo() {                                    
                                    window.location="RF_ORDEN_DE_COMPRA.php";
                                }
                                                                
                            </script>                            
                        </div>
                    </div>
                </form>
<!-- Fin de división y contenedor del formulario -->           
            </div>     
        </div>
        <div class="col-sm-7 text-left col-sm-offset-1" style="margin-top:10px;" align="">                    
        <div class="client-form">
            <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarDetalleOrdenCompra.php" style="margin-top:-15px">
                <input name="id" id="id" type="hidden" value="<?php echo $id; ?>"/>
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
                    <script>
                        <?php if(!empty($_GET['orden'])){ ?>
                            $("#btnGuardarDetalle").prop('disabled',false);
                        <?php
                        }else{ ?>
                            $("#btnGuardarDetalle").prop('disabled',true);
                        <?php
                        } ?>
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
                valor = parseFloat($("#txtValor").val());
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

    $sqlIva = "SELECT valor FROM gs_parametros_basicos WHERE id_unico=2";
    $resultIva = $mysqli->query($sqlIva);
    $iva = mysqli_fetch_row($resultIva);
    ?>
    <input type="hidden" id="idPrevio" value="">
    <input type="hidden" id="idActual" value="">  
    <div class="col-sm-9">
        <div class="table-responsive col-sm-offset-1" style="margin-right: 0px; margin-top:-5px;">
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">         
                <?php 
                $item=1;
                $totalmov=0;
                if(!empty($_GET['orden'])){ 
                ?>
                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">                    
                    <thead>
                        <tr>                                        
                            <td width="7%"  style="display: none;">Identificador</td>
                            <td width="7%" class="cabeza"></td>
                            <td class="cabeza"><strong>Item</strong></td>
                            <td class="cabeza"><strong>Plan Inventario</strong></td>
                            <td class="cabeza"><strong>Cantidad</strong></td>
                            <td class="cabeza"><strong>Valor Aproximado</strong></td>
                            <td class="cabeza"><strong>Iva</strong></td>
                            <td class="cabeza"><strong>Valor Total</strong></td>
                        </tr>
                        <tr>                                        
                            <th width="7%"  style="display: none;">Identificador</th>
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
                            $m=$_GET['orden'];
                            $det = "SELECT 
                                        dtm.id_unico,
                                        dtm.planmovimiento,
                                        dtm.cantidad,
                                        dtm.valor,
                                        pl.id_unico,
                                        CONCAT(pl.codi,' - ',pl.nombre),
                                        dtm.iva,
                                        pl.codi,
                                        dtm.valor*dtm.cantidad
                            FROM gf_detalle_movimiento dtm
                            LEFT JOIN gf_movimiento mv ON dtm.movimiento = mv.id_unico
                            LEFT JOIN gf_plan_inventario pl ON dtm.planmovimiento = pl.id_unico
                            WHERE md5(mv.id_unico)='$m'";
                            $resultado2 = $mysqli->query($det);                                                                                
                        while ($row2 = mysqli_fetch_row($resultado2)) { ?>
                        <tr>
                            <td style="display: none;"><?php echo $row2[0]?>
                            </td>
                            <td>
                                <a href="#<?php echo $row2[0];?>" title="Eliminar" onclick="javascript:eliminar(<?php echo $row2[0];?>);">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </a>
                                <a href="#<?php echo $row2[0];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row2[0]; ?>);return calcular(<?php echo $row2[0]; ?>)">
                                    <li class="glyphicon glyphicon-edit"></li>
                                </a>
                            </td>
                            <td class="text-right" width="7%">                                                                            
                                <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblItem'.$row2[0].'">'.$item++.'</label>'; ?>
                            </td>
                            <td class="text-right">                             
                              <?php                               
                              echo '<label  class="valorLabel" style="font-weight:normal" id="lblCodigoE'.$row2[0].'">'.$row2[5].'</label>'; ?>
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
                            <td class="text-right">                              
                                <?php 
                                $sumC+=$row2[2];
                                echo '<label class="valorLabel" style="font-weight:normal" id="lblCantidad'.$row2[0].'">'.$row2[2].'</label>'; 
                                echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="number" name="txtcantidad'.$row2[0].'" id="txtcantidad'.$row2[0].'" value="'.$row2[2].'" />';
                                ?>
                            </td>
                            <td class="text-right">                           
                                <?php 
                                $sumV+=$row2[3];
                                echo '<label class="valorLabel" style="font-weight:normal" id="ValorT'.$row2[0].'">'.number_format($row2[3],2,'.',',').'</label>'; 
                                echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="number" name="txtvalor'.$row2[0].'" id="txtvalor'.$row2[0].'" value="'.$row2[3].'" />';
                                ?>
                            </td>
                            <td class="text-right">
                                <?php 
                                $ivaV = $row2[6];
                                $sumIva +=$row2[6];
                                echo '<label class="valorLabel" style="font-weight:normal" id="lblIva'.$row2[0].'">'.number_format($ivaV,2,'.',',').'</label>'; 
                                echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="number" name="txtiva'.$row2[0].'" id="txtiva'.$row2[0].'" value="'.$ivaV.'" readonly/>';
                                ?>
                            </td>
                            <td style="height:10px;font-size:10px"class="text-right">
                                <?php                                 
                                $total = $row2[8]+$row2[6];
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
                <?php }else{ ?>
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">                    
                    <thead>
                        <tr>                                        
                            <td width="7%"  style="display: none;">Identificador</td>
                            <td width="7%" class="cabeza oculto"></td>
                            <td class="cabeza"><strong>Item</strong></td>
                            <td class="cabeza"><strong>Plan Inventario</strong></td>
                            <td class="cabeza"><strong>Cantidad</strong></td>
                            <td class="cabeza"><strong>Valor Aproximado</strong></td>
                            <td class="cabeza"><strong>Iva</strong></td>
                            <td class="cabeza"><strong>Valor Total</strong></td>
                        </tr>
                        <tr>                                        
                            <th width="7%"  style="display: none;">Identificador</th>
                            <th width="7%"  class="cabeza oculto"></th>
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
                            if(!empty($_GET['values'])){
                                $values = $_GET['values'];
                                $det = "SELECT 
                                        dtm.id_unico,
                                        dtm.planmovimiento,
                                        SUM(dtm.cantidad) as cantidad,
                                        (dtm.valor) as valor,
                                        pl.id_unico,
                                        CONCAT(pl.codi,' - ',pl.nombre),
                                        dtm.iva,
                                        pl.codi,
                                        cantidad*valor
                                FROM gf_detalle_movimiento dtm
                                LEFT JOIN gf_movimiento mv ON dtm.movimiento = mv.id_unico
                                LEFT JOIN gf_plan_inventario pl ON dtm.planmovimiento = pl.id_unico
                                WHERE (mv.id_unico) IN ($values) GROUP BY pl.id_unico ASC";
                                $resultado2 = $mysqli->query($det);
                                while ($row2 = mysqli_fetch_row($resultado2)) { ?>
                        <tr>
                            <td style="display: none;"><?php echo $row2[0]?>
                            </td>                            
                            <td class="text-right" width="7%">                                                                            
                                <?php echo $item++; ?>
                            </td>
                            <td class="text-right">                             
                              <?php                               
                                echo $row2[5]; ?>                                
                            </td>
                            <td class="text-right">                              
                                <?php 
                                $sumC+=$row2[2];
                                echo $row2[2];                                 
                                ?>
                            </td>
                            <td class="text-right">                           
                                <?php 
                                $sumV+=$row2[8];
                                echo number_format($row2[8],2,'.',',');                                 
                                ?>
                            </td>
                            <td class="text-right">
                                <?php   
                                $to = $row2[2]*$row2[3];                              
                                $ivaV = ($to*$iva[0])/100;
                                $sumIva +=$ivaV;
                                echo number_format($ivaV,2,'.',',');                                 
                                ?>
                            </td>
                            <td style="height:10px;font-size:10px"class="text-right">
                                <?php                                 
                                $total = ($row2[2]*$row2[3])+$ivaV;
                                $totalmov+=$total;
                                echo number_format($total, 2, '.', ',');                                 
                                 ?>                                
                            </td>    
                            <td>                                
                            </td>
                        </tr>
                        <?php }
                        }
                        ?>
                                                                                                                                                                
                    </tbody>
                </table>
                <?php    
                } ?>
            </div>            
        </div>
    </div>
    <div class="col-sm-9 col-sm-offset-1" style="margin-bottom: -20px"> 
        <div class="col-sm-1 col-sm-offset-2" style="margin-right:30px">
            <div class="form-group" style="" align="left">                                    
                <label class="control-label valorLabel">
                    <strong class="valorLabel">Totales:</strong>
                </label>                                
            </div>
        </div> 
        <div class="col-sm-1" style="margin-right:70px">
            <label class="control-label valorLabel" style="font-size:10px;" title="Total cantidad"><?php echo number_format($sumC, 2, '.', ','); ?></label>
        </div>  
        <div class="col-sm-1" style="margin-right:70px">
            <label class="control-label valorLabel" style="font-size:10px;" title="Total valor"><?php echo number_format($sumV, 2, '.', ','); ?></label>
        </div>  
        <div class="col-sm-1" style="margin-right:70px">
            <label class="control-label valorLabel" style="font-size:10px;" title="Total iva"><?php echo number_format($sumIva, 2, '.', ','); ?></label>
        </div>  
        <div class="col-sm-1" >
            <label class="control-label valorLabel" style="font-size:10px;" title="Total valor total"><?php echo number_format($totalmov, 2, '.', ','); ?></label>
        </div>  
    </div>
    </div>
</div>  
 <script type="text/javascript">                           
    function modificar(id){
        if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
            //Labels             
            var lblCantidadE = 'lblCantidad'+$("#idPrevio").val();                        
            var ValorTE = 'ValorT'+$("#idPrevio").val();                        
            var lblIvaE = 'lblIva'+$("#idPrevio").val();                        
            var lblValorTotalE = 'lblValorTotal'+$("#idPrevio").val();                        
            
            //Campos para cancelar y guardar cambios
            var guardarC = 'guardar'+$("#idPrevio").val();
            var cancelarC = 'cancelar'+$("#idPrevio").val();
            var tablaC = 'tab'+$("#idPrevio").val();
            
            //Campos ocultos            
            var txtCantidadE = 'txtcantidad'+$("#idPrevio").val();                        
            var txtValorE = 'txtvalor'+$("#idPrevio").val();
            var txtIvaE = 'txtiva'+$("#idPrevio").val();
            var txtValorTE = 'txttotal'+$("#idPrevio").val();
            
            //Se mustran los labels            
            $("#"+lblCantidadE).css('display','block');
            $("#"+ValorTE).css('display','block');
            $("#"+lblIvaE).css('display','block');
            $("#"+lblValorTotalE).css('display','block');
            
            //Se ocultan los campos
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
        var lblCantidad  = 'lblCantidad'+id;
        var ValorT  = 'ValorT'+id;
        var lblIva  = 'lblIva'+id;
        var lblValorTotal  = 'lblValorTotal'+id;
        
        //campos        
        var txtCantidad = 'txtcantidad'+id;  
        var txtValor = 'txtvalor'+id; 
        var txtIva = 'txtiva'+id;
        var txtValorT = 'txttotal'+id;
        
        //campos para cancelar y guardar cambios
        var guardar = 'guardar'+id;
        var cancelar = 'cancelar'+id;
        var tabla = 'tab'+id; 
        
        //Se ocultan los labels        
        $("#"+lblCantidad).css('display','none');
        $("#"+ValorT).css('display','none');
        $("#"+lblIva).css('display','none');
        $("#"+lblValorTotal).css('display','none');
        
        //Se muestran los campos ocultos
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
        var lblCantidad  = 'lblCantidad'+id;
        var ValorT  = 'ValorT'+id;
        var lblIva  = 'lblIva'+id;
        var lblValorTotal  = 'lblValorTotal'+id;
        
        //campos        
        var txtCantidad = 'txtcantidad'+id;  
        var txtValor = 'txtvalor'+id; 
        var txtIva = 'txtiva'+id;
        var txtValorT = 'txttotal'+id;
        
        //campos para cancelar y guardar cambios
        var guardar = 'guardar'+id;
        var cancelar = 'cancelar'+id;
        var tabla = 'tab'+id; 
        
        //se muestran los labels        
        $("#"+lblCantidad).css('display','block');
        $("#"+ValorT).css('display','block');
        $("#"+lblIva).css('display','block');
        $("#"+lblValorTotal).css('display','block');
        
        //Se ocultan los campos        
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
        var txtCantidad = 'txtcantidad'+id;  
        var txtValor = 'txtvalor'+id; 
        var txtIva = 'txtiva'+id;
        

        var form_data = {
            is_ajax:1,
            id:+id,            
            cantidad:$("#"+txtCantidad).val(),
            valor:$("#"+txtValor).val(),                    
            iva:$("#"+txtIva).val()
        };
        var result='';
        $.ajax({
            type: 'POST',
            url: "json/modificarDetalleOrdenCompra.php",
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
                url:"json/eliminarDetalleOrdenCompra.php?id="+id,
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
    
    function modificarOrden(){
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
            observacion:$("#txtObservacion").val(),
            iva:$("#txtIva").val()
        };
                        
        var result = '';
        $.ajax({
            type: 'POST',
            url: "json/modificarOrdenCompra.php",
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
    </script>        
    <script src="js/bootstrap.min.js"></script>
    <!-- Inicio de modales -->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de Orden de compra?</p>
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
    <!-- Modal para requisiciones -->
    <div class="modal fade" id="modalRequisicones" role="dialog">
        <div class="modal-dialog" style="max-width:500px">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Requisiciones</h4>
                    <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                        <button type="button" id="btnCerrarModalMov" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                    </div>
                </div>
                <div class="modal-body contTabla table-responsive" style="margin-top:-5px" align="left">
                    <table id="tablaRequisiciones" class="table table-striped table-condensed table-checkable" class="display" cellspacing="0" width="100%">
                        <thead>                   
                            <tr>
                                <td class="cabeza"><strong>Tipo</strong></td>
                                <td class="cabeza"><strong>Número</strong></td>
                                <td class="cabeza"><strong>Cantidad</strong></td>
                                <td class="cabeza"><strong>Valor</strong></td>
                                <td class="cabeza" width="1%"><strong></strong></td>
                            </tr>
                            <tr>
                                <th class="cabeza">Tipo</th>
                                <th class="cabeza">Número</th>
                                <th class="cabeza">Cantidad</th>
                                <th class="cabeza">Valor</th>
                                <th class="cabeza" width="1%"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $Req = 0;                       
                        $sqlReq = "SELECT DISTINCT
                                                    mv.id_unico,
                                                    tpm.nombre,
                                                    mv.numero,
                                                    SUM(cantidad) as cantidad,
                                                    SUM(valor)*cantidad
                                    FROM gf_movimiento mv
                                    LEFT JOIN gf_tipo_movimiento tpm ON tpm.id_unico = mv.tipomovimiento
                                    INNER JOIN gf_detalle_movimiento dtm ON dtm.movimiento = mv.id_unico
                                    WHERE mv.tipomovimiento = 4 AND (SELECT SUM(dtm.valor) FROM gf_detalle_movimiento dtm WHERE dtm.movimiento=mv.id_unico)>0
                                    GROUP BY mv.id_unico";
                        $resultReq = $mysqli->query($sqlReq);                         
                        while ($rowReq = mysqli_fetch_row($resultReq)){                             
                            ?>
                        <tr >
                            <td class="campos text-left">
                                <?php echo $rowReq[1]; ?>
                            </td>
                            <td class="campos text-right">
                                <?php echo $rowReq[2]; ?>
                            </td>
                            <td class="campos text-right">
                                <?php                                                                 
                                echo $rowReq[3];
                                ?>
                            </td>
                            <td class="campos text-right">
                                <?php                                                                
                                echo $rowReq[4];
                                ?>
                            </td>
                            <td class="campos text-right" width="1%">   
                                <input name="chkActivar[]" id="chkActivar<?php echo $rowReq[0]?>" value="<?php echo $rowReq[0]?>" type="checkbox"/>
                                <?php $Req=$rowReq[0];?>
                            </td>
                        </tr>
                        <?php    
                        }                        
                        ?>                        
                    </tbody>
                    </table>                    
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnReq" class="btn" onclick="return  marcados()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>                    
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2, .select2-multiple" ).select2();             
        $("#modalRequisicones").on('shown.bs.modal',function(){
            try{
                var dataTable = $("#tablaRequisiciones").DataTable();
                dataTable.columns.adjust().responsive.recalc();
            }catch(err){}            
        });
  
        function marcados(){                          
            var data = localStorage.getItem("data");
            $("#txtData").val(data);
            console.log(data);
            var c = 0;
            var checkboxValues = "";
            $('input[name="chkActivar[]"]:checked').each(function() {
                checkboxValues += $(this).val() + ",";
                c = c+1;
            });
            //eliminamos la última coma.
            checkboxValues = checkboxValues.substring(0, checkboxValues.length-1);
            var form_data = {
                values:checkboxValues,
                saldo:3
            };
            $.ajax({
                type: 'POST',
                url: "consultasBasicas/consultaSaldos.php",
                data: form_data,
                success: function (data) {                    
                    console.log(data);
                }
            });            
            //si todos los checkbox están seleccionados devuelve 1,2,3,4,5        
            var valor = checkboxValues.split(",");
            if(c>1){                
                window.location = 'RF_ORDEN_DE_COMPRA.php?movimiento='+md5(valor[0])+'&values='+checkboxValues;                 
            }else{
                window.location = 'RF_ORDEN_DE_COMPRA.php?movimiento='+md5(checkboxValues)+'&values='+checkboxValues;                                
            }
        }                                
    </script>        
<!-- Llamado al pie de pagina -->
<script type="text/javascript">
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
<?php require_once 'footer.php' ?>  
</body>
</html>
