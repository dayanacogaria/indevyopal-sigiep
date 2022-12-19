<?php
require_once ('./head_listar.php');
require_once ('./Conexion/conexion.php');
?>
<!-- Inicio Ventana / Título Ventana-->
    <title>Aprobación Cuenta por Pagar</title>
    <link rel="stylesheet" href="css/jquery-ui_1.css">
    <script src="js/jquery-ui.js"></script>
    <link href="css/select/select2.min.css" rel="stylesheet">
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
    </script>
    <style>
        table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px}
        .dataTables_wrapper .ui-toolbar{padding:5px}
        .campoD:focus {
            border-color: #66afe9;
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
                box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);            
        }
        .campoD:hover{
            cursor: pointer;
        }
        
        .valorLabel{
            font-size: 10px;
        }
        
        .campos{
            padding: 0px;
            font-size: 10px
        }
        
        body{
            font-size: 10px
        }
    </style>      
    </head>
    <body >
        <div class="container-fluid text-left">
            <div class="row content">
                <?php require_once('menu.php'); ?>
                <div class="col-sm-8 text-center" style="margin-top:-22px;margin-left:-15px">
<!----------------- Título del formulario -->
                    <h2 class="tituloform" align="center">Aprobación Orden de Pago</h2>
                    <div class="client-form contenedorForma" style="margin-top:-7px;">
<!----------------------RegistrarJson.php -->
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarComprobanteContable.php" style="margin-bottom:-20px">
<!--------------------------Consulta Principal -->
                            <?php 
                                if (!empty($_GET['num'])) {
                                    $nume = $_GET['num'];
                                    $sql="  
                                    SELECT	
          dcp.id_unico,
	  dcp.descripcion,
          dcp.valor,
          dcp.comprobantepptal,
          cp.id_unico,
          cp.numero,
          cp.descripcion,
          dcp.rubrofuente,
          rp.id_unico,
          rp.codi_presupuesto,
          rp.nombre,
          dcp.tercero,
          tr.id_unico,
          tr.nombreuno,
          tr.nombredos,
          tr.apellidouno,
          tr.apellidodos,
          ti.nombre,
          tr.numeroidentificacion,
          tr.razonsocial,
          dcp.proyecto,
          pr.id_unico,
          pr.nombre,
          dcp.comprobanteafectado
          from gf_detalle_comprobante_pptal dcp
          left join gf_comprobante_pptal cp on dcp.comprobantepptal = cp.id_unico
          left join gf_rubro_fuente rft on dcp.rubrofuente = rft.id_unico
          left join gf_rubro_pptal rp on rft.rubro = rp.id_unico
          left join gf_tercero tr on dcp.tercero = tr.id_unico
          left join gf_tipo_identificacion ti on tr.tipoidentificacion = ti.id_unico
          left join gf_proyecto pr on dcp.proyecto = pr.id_unico
           left join gf_detalle_comprobante_pptal hijo ON dcp.comprobanteafectado = hijo.id_unico
           WHERE dcp.comprobantepptal != 'null'
                                            WHERE md5(cn.numero) = '$nume'";
                                            $rs = $mysqli->query($sql);
                                            $cn = mysqli_fetch_row($rs);
                                }                    
                            ?>
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                                Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                            </p>
                            <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;">
                                                              <!-- Tercero -->
                                <label class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Tercero:
                                </label>
                                <select class="col-sm-2 input-sm" name="sltTercero" id="sltTercero" class="form-control" style="width:100px;height:30%" title="Seleccione tercero" required>
                                    <?php 
                                    if(!empty($_GET['num'])){ 
                                        if($cn[10]!='""'){
                                            if(empty($cn[6]) || empty($cn[7]) || empty($cn[8]) || empty($cn[9]) || $cn[6]=='""' || $cn[7]=='""' || $cn[8]=='""' ||$cn[9]=='""'){
                                                echo '<option value="'.$cn[5].'">'.ucwords(strtolower($cn[10].'('.$cn[11].' - '.$cn[12].')')).'</option>';
                                            }else{
                                                echo '<option value="'.$cn[5].'">'.ucwords(strtolower($cn[6].' '.$cn[7].' '.$cn[8].' '.$cn[9].'('.$cn[11].' - '.$cn[12].')')).'</option>';
                                            }
                                        }
                                    }else{ ?>
                                        <option>Tercero</option>
                                        <?php 
                                        $sql = "SELECT DISTINCT
                                                   T.id_unico,
                                                   T.nombreuno,
                                                   T.nombredos,
                                                   T.apellidouno,
                                                   T.apellidodos,
                                                   T.razonsocial,
                                                   TI.nombre,
                                                   T.numeroidentificacion
                                            FROM gf_tercero T 
                                            LEFT JOIN gf_tipo_identificacion TI 
                                            ON T.tipoidentificacion = TI.id_unico";
                                        $query = $mysqli->query($sql);
                                        while($res = mysqli_fetch_row($query)){
                                            if ($res[5]!='""') {                                            
                                                if (empty($res[1]) || empty($res[2]) || empty($res[3]) || empty($res[4])) {
                                                    echo '<option value="'.$res[0].'">'.$res[5].' '.'('.$res[6].' - '.$res[7].')'.'</option>';
                                                }else{
                                                    echo '<option value="'.$res[0].'">'.$res[1].' '.$res[2].' '.$res[3].' '.$res[4].' '.'('.$res[6].' - '.$res[7].')'.'</option>';
                                                }
                                            }else{
                                                echo '<option value="'.$res[0].'">'.$res[1].' '.$res[2].' '.$res[3].' '.$res[4].' '.'('.$res[6].' - '.$res[7].')'.'</option>';
                                            }
                                        }                                        
                                        ?>
                                    <?php    
                                    }
                                    ?>
                                    
                                    <?php 
                                    $sql = "SELECT DISTINCT
                                                   T.id_unico,
                                                   T.nombreuno,
                                                   T.nombredos,
                                                   T.apellidouno,
                                                   T.apellidodos,
                                                   T.razonsocial,
                                                   TI.nombre,
                                                   T.numeroidentificacion
                                            FROM gf_tercero T 
                                            LEFT JOIN gf_tipo_identificacion TI 
                                            ON T.tipoidentificacion = TI.id_unico
                                            WHERE T.id_unico != $cn[5]";
                                    $result = $mysqli->query($sql);
                                    while($row=  mysqli_fetch_row($result)){ ?>
                                        <?php 
                                        if ($row[5]!='""') {                                            
                                            if (empty($row[1]) && empty($row[2]) && empty($row[3]) && empty($row[4])) {
                                                echo '<option value="'.$row[0].'">'.$row[5].' '.'('.$row[6].' - '.$row[7].')'.'</option>';
                                            }else{
                                                if (($row[1]=='NULL') ) {
                                                    echo '<option value="'.$row[0].'">'.$row[5].' '.'('.$row[6].' - '.$row[7].')'.'</option>';
                                                    if ($row[2]=='NULL') {
                                                        echo '<option value="'.$row[0].'">'.$row[5].' '.'('.$row[6].' - '.$row[7].')'.'</option>';
                                                        if ($row[3]=='NULL') {
                                                            echo '<option value="'.$row[0].'">'.$row[5].' '.'('.$row[6].' - '.$row[7].')'.'</option>';
                                                            if ($row[4]=='NULL') {
                                                                echo '<option value="'.$row[0].'">'.$row[5].' '.'('.$row[6].' - '.$row[7].')'.'</option>';
                                                            }
                                                        }
                                                    }
                                                }else{
                                                    echo '<option value="'.$row[0].'">'.$row[1].' '.$row[2].' '.$row[3].' '.$row[4].' '.'('.$row[6].' - '.$row[7].')'.'</option>';
                                                }
                                            }
                                        }else{
                                           echo '<option value="'.$row[0].'">'.$row[1].' '.$row[2].' '.$row[3].' '.$row[4].' '.'('.$row[6].' - '.$row[7].')'.'</option>';
                                        }
                                        ?>                                        
                                    <?php
                                    }
                                    ?>
                                </select>
                                                                                           
<!----------------------------- Tipo Comprobante -->
                                <label class="col-sm-2 control-label" for="sltRegistroPPTAL">
                                    <strong class="obligado">*</strong>Registro Presupuestal:
                                </label>                                
                                <select class="col-sm-2 input-sm"  name="sltRegistroPPTAL" id="sltRegistroPPTAL" class="form-control" style="width:100px;height:30% " title="Seleccione registro presupuestal" required>
                                    <?php 
                                 /*  if(!empty($_GET['num'])){
                                        if($cn[10]!=0){
                                            echo '<option value="'.$cn[10].'">'.  ucwords(strtolower($cn[11])).'</option>';
                                        }else{
                                            echo '<option value="">Registro Presupuestal</option>'; */
                                            $sqlTC = "SELECT        cpp.id_unico,       
                                                                    cpp.numero,
                                                                    cpp.tipocomprobante,
                                                                    tcp.id_unico,
                                                                    tcp.nombre,
                                                                    cpp.tercero
                                                        FROM gf_comprobante_pptal cpp
                                                        LEFT JOIN gf_tipo_comprobante_pptal tcp ON cpp.tipocomprobante = tcp.id_unico
                                                        WHERE cpp.tercero = $rs[10]";
                                            $m = $mysqli->query($sqlTC);
                                            while($resc = mysqli_fetch_row($m)){
                                                echo '<option value="'.$resc[0].'">'.$resc[1].'</option>';
                                            }
                                        //}                                        
                                  /*  }else{?>
                                        <option>Tipo Combrobante</option>
                                        <?php 
                                        $sqlTC = "SELECT id_unico,nombre FROM gf_tipo_comprobante";
                                        $m = $mysqli->query($sqlTC);
                                        while($resc = mysqli_fetch_row($m)){
                                            echo '<option value="'.$resc[0].'">'.$resc[1].'</option>';
                                        }*/
                                        
                                        ?>
                                    <?php    
                                    //}
                                    ?>
                                    
                                    <?php 
                                    //$sqlX = "SELECT id_unico,nombre FROM gf_tipo_comprobante WHERE id_unico != $cn[13]";
                                    //$resulta = $mysqli->query($sqlX); 
                                    //while($d = mysqli_fetch_row($resulta)){
                                     //   echo '<option value="'.$d[0].'">'.ucwords(strtolower($d[1])).'</option>';
                                   // }
                                    ?>
                                </select>  
                                <!-- Fecha -->
                                <label for="fecha" class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Fecha:
                                </label>
                                <?php 
                                if (!empty($_GET['num'])) {
                                    $valorF = (String) $cn[1];
                                    $fechaS = explode("-",$valorF);
                                }                                
                                ?>
                                <input class="col-sm-2 input-sm" value="<?php if(!empty($_GET['num'])){echo $fechaS[2].'/'.$fechaS[1].'/'.$fechaS[0];}else{echo date('d/m/Y');} ?>" type="text" name="fecha" id="fecha" class="form-control" style="width:100px;height:26px" title="Ingrese la fecha" placeholder="Fecha" required>    


                                <!-- Centro de costo -->
                                <!-- Proyecto -->
                               
                                <!-- Clase contrato -->
                               
                                <div class="col-sm-offset-7 col-sm-8" style="margin-top:20px;margin-left:522px">                                    
                                    <div class="col-sm-1">
                                        <?php if(!empty($_GET['num']) ){ ?>
                                            <a id="btnCancelarM" onclick="javascript:cancelarM()" class="btn sombra btn-primary" style="width: 40px" title="Cancelar modificación"><li class="glyphicon glyphicon glyphicon-remove"></li></a>
                                        <?php    
                                        }else{ ?>
                                            <a id="btnNuevo" onclick="javascript:nuevo()" class="btn sombra btn-primary" style="width: 40px" title="Ingresar nuevo comprobante"><li class="glyphicon glyphicon-plus"></li></a>
                                            <a id="btnCancelarP" onclick="javascript:cancelarN()" class="btn sombra btn-primary" style="display: none;width: 40px" title="Cancelar ingreso de datos"><li class="glyphicon glyphicon glyphicon-remove"></li></a>
                                        <?php    
                                        } ?>                                        
                                    </div>
                                    <div class="col-sm-1" style="">
                                        <?php if(!empty($_GET['num'])){ ?>
                                        <a onclick="javascript:modificarComprobante()" id="btnModificar" class="btn sombra btn-primary" title="Modificar comprobante"><li class="glyphicon glyphicon-floppy-disk"></li></a>
                                        <?php    
                                        }else{ ?>
                                            <button type="submit" id="btnGuardar" class="btn sombra btn-primary" title="Guardar comprobante"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                        <?php    
                                        } ?>                                                                               
                                    </div>
                                    <div class="col-sm-1">
                                        <button class="btn sombra btn-primary" title="Imprimir"><li class="glyphicon glyphicon glyphicon-print"></li></button>
                                    </div> 
                                    <script>
                                        function cancelarM(){
                                            window.location = "registrar_GF_COMPROBANTE_CONTABLE.php";
                                        }
                                        function nuevo(){
                                            var form_data = {
                                                is_ajax:1,
                                                numero:$("#txtNumero").val()
                                            };

                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasComprobanteContable/generarNuevo.php",
                                                data: form_data,
                                                success: function (data) {
                                                    var datos = data.split(";");                        
                                                    $("#txtNumero").val(datos[1]);
                                                    $("#btnGuardar").attr('disabled',false);
                                                    $("#btnNuevo").css('display','none');
                                                    $("#btnCancelarP").css('display','block');
                                                }
                                            });
                                        }
                                        $(document).ready(function(){
                                            $("#btnGuardar").attr('disabled',true);                                             
                                        });
                                        function cancelarN(){
                                            $("#btnCancelarP").css('display','none');
                                            $("#btnNuevo").css('display','block');
                                            $("#btnGuardar").attr('disabled',true);
                                            $("#txtNumero").val("");
                                        }
                                    </script>
                                </div>
                            </div>                            
                        </form>
                    </div>                    
                </div>
                <div class="col-sm-10 text-center " style="margin-top:5px;" align="">                    
                    <div class="client-form" style="margin-left:60px" class="col-sm-12">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarDetalleComprobante.php" style="margin-top:-15px">
                            <div class="col-sm-1" style="margin-right:30px;">
                                                               
                            </div>    
                            
                            <div class="col-sm-1" align="left" style="margin-top:26px;margin-left:600px;margin-right:30px; ">
                                <button type="submit" class="btn btn-primary sombra"><li class="glyphicon glyphicon-floppy-disk"></li></button>                                
                                <input type="hidden" name="MM_insert" >
                            </div>
                                                                                   
                        </form>                        
                    </div>
                </div>
                <div class=" contTabla col-sm-8" style="margin-left:-15px;margin-top:-20px">
                    <div class="table-responsive contTabla" >
                        <?php 
                            if (!empty($_GET['idNumeroC'])) {
                                $sql="  
                                SELECT
                                   DT.id_unico,
                                   CT.id_unico,
                                   CT.nombre,
                                   CT.codi_cuenta,
                                   DT.naturaleza,
                                   N.id_unico,
                                   N.nombre,
                                   T.id_unico,
                                   T.nombreuno,
                                   T.nombredos,
                                   T.apellidouno,
                                   T.apellidodos,
                                   T.numeroidentificacion,
                                   TI.id_unico,
                                   TI.nombre,
                                   CC.id_unico,
                                   CC.nombre,
                                   PR.id_unico,
                                   PR.nombre,
                                   DT.valor
                                FROM
                                  gf_detalle_comprobante DT
                                LEFT JOIN
                                  gf_cuenta CT ON DT.cuenta = CT.id_unico
                                LEFT JOIN
                                  gf_naturaleza N ON N.id_unico = DT.naturaleza
                                LEFT JOIN
                                  gf_tercero T ON DT.tercero = T.id_unico
                                LEFT JOIN
                                  gf_tipo_identificacion TI ON T.tipoidentificacion = TI.id_unico
                                LEFT JOIN
                                  gf_centro_costo CC ON DT.centrocosto = CC.id_unico
                                LEFT JOIN
                                  gf_proyecto PR ON DT.proyecto = PR.id_unico
                                WHERE md5(DT.comprobante) = '".$_GET['idNumeroC']."'";
                                $rs = $mysqli->query($sql);
                            }
                    
                    ?>
                    <input type="hidden" id="idPrevio" value="">
                    <input type="hidden" id="idActual" value="">
                    <?php 
                    $sumar = 0;
                    $sumaT = 0;
                    ?>
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">                            
                        <thead>
                            <tr>
                                <td class="oculto">Identificador</td>
                                <td width="7%" class="cabeza"></td>
                                <td class="cabeza"><strong>Rubro Presupuestal</strong></td>
                                <td class="cabeza"><strong>Saldo por Pagar</strong></td>
                                <td class="cabeza"><strong>Valor Aprobado</strong></td>
                            </tr>
                            <tr>
                                <th class="oculto">Identificador</th>
                                <th width="7%"></th>
                                <th class="cabeza">Rubro Presupuestal</th>
                                <th class="cabeza">Saldo por Pagar</th>
                                <th class="cabeza">Valor Aprobado</th>
                            </tr>
                        </thead>
                        <tbody>  
                    <!--        <?php 
                            while ($row = mysqli_fetch_row($rs)) { ?>
                            <tr>
                                <td class="campos oculto">
                                    <?php echo $row[0]; ?>
                                </td>
                                <td class="campos">
                                    <a href="#<?php echo $row[0];?>" onclick="javascript:eliminar(<?php echo $row[0]; ?>)" title="Eliminar">
                                        <li class="glyphicon glyphicon-trash"></li>
                                    </a>
                                    <a href="#<?php echo $row[0];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>);javascript:cargarT(<?php echo $row[0]; ?>);javascript:cargarT2(<?php echo $row[0]; ?>);javascript:cargarCentro(<?php echo $row[0]; ?>);javascript:cargarCentro2(<?php echo $row[0]; ?>);javascript:cargarProyecto(<?php echo $row[0]; ?>);javascript:cargarProyecto2(<?php echo $row[0]; ?>)">
                                        <li class="glyphicon glyphicon-edit"></li>
                                    </a>                                            
                                </td>
                                <!-- Código de cuenta y nombre de la cuenta -->
                                <td class="campos text-left" >
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="cuenta'.$row[0].'">'. (ucwords(strtolower($row[3].' - '.$row[2]))).'</label>'; ?>
                                    <select style="display: none;padding:2px" class="col-sm-12 campoD" id="sltC<?php echo $row[0]; ?>">
                                        <option value="<?php echo $row[1];?>"><?php echo $row[3].'-'.$row[2]; ?></option>
                                            <?php 
                                            $sqlCTN = "SELECT DISTINCT id_unico,codi_cuenta,nombre FROM gf_cuenta WHERE id_unico != $row[1]";
                                            $result = $mysqli->query($sqlCTN);
                                            while ($s = mysqli_fetch_row($result)){
                                                echo '<option value="'.$s[0].'">'.$s[1].' - '.$s[2].'</option>';
                                            }
                                            ?>                                                
                                    </select>
                                </td> 
                                <!-- Datos de tercero -->
                                <td class="campos text-left">
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="tercero'.$row[0].'">'. (ucwords(strtolower($row[8].' '.$row[9].' '.$row[10].' '.$row[11]))).'('.$row[13].' - '.$row[12].')'.'</label>'; ?>
                                    <select id="sltTercero<?php echo $row[0]; ?>" style="display: none;padding: 2px;height:18" class="col-sm-12 campoD">
                                        <option value="<?php echo $row[7] ?>"><?php echo  (ucwords(strtolower($row[8].' '.$row[9].' '.$row[10].' '.$row[11].'('.$row[14].' - '.$row[12].')'))) ?></option>
                                        <?php 
                                        $sqlTR = "SELECT DISTINCT
                                                                    T.id_unico,
                                                                    T.nombreuno,
                                                                    T.nombredos,
                                                                    T.apellidouno,
                                                                    T.apellidodos,
                                                                    T.razonsocial,
                                                                    TI.nombre,
                                                                    T.numeroidentificacion
                                                    FROM gf_tercero T 
                                                    LEFT JOIN gf_tipo_identificacion TI 
                                                    ON T.tipoidentificacion = TI.id_unico
                                                    WHERE
                                                    T.id_unico != $row[7]";
                                        $resulta = $mysqli->query($sqlTR);
                                        while($e = mysqli_fetch_row($resulta)){  
                                            if ($e[5]!='""') {
                                                if (empty($e[1]) && empty($e[2]) && empty($e[3]) && empty($e[4]) || $e[1] == NULL  || $e[1] == NULL || $row[2] == NULL || $e[3] == NULL || $e[4] == NULL) {
                                                    echo '<option value="'.$e[0].'">'.$e[5].' ('.$e[6].' - '.$e[7].')'.'</option>';
                                                }else{
                                                    if (($e[1]=='NA') ) {
                                                        echo '<option value="'.$e[0].'">'.$e[5].' ('.$e[6].' - '.$e[7].')'.'</option>';
                                                            if ($e[2]=='NA') {
                                                                echo '<option value="'.$e[0].'">'.$e[5].' ('.$e[6].' - '.$e[7].')'.'</option>';
                                                                if ($e[3]=='NA') {
                                                                    echo '<option value="'.$e[0].'">'.$e[5].' ('.$e[6].' - '.$e[7].')'.'</option>';
                                                                    if ($e[4]=='NA') {
                                                                        echo '<option value="'.$e[0].'">'.$e[5].' ('.$e[6].' - '.$e[7].')'.'</option>';
                                                                    }
                                                                }
                                                            }
                                                    }else{
                                                        echo '<option value="'.$e[0].'">'. ($e[1].' '.$e[2].' '.$e[3]).' '.$e[4].' ('.$e[6].' - '.$e[7].')'.'</option>';
                                                    }  
                                                }         
                                            }else{
                                                echo '<option value="'.$e[0].'">'. ($e[1].' '.$e[2].' '.$e[3]).' '.$e[4].' ('.$e[6].' - '.$e[7].')'.'</option>';
                                            }                                                   
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td class="campos text-left">
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="centroC'.$row[0].'">'. (ucwords(strtolower($row[16]))).'</label>'; ?>
                                    <select id="sltcentroC<?php echo $row[0]; ?>" style="display: none;padding:2px;height:19px" class="col-sm-12 campoD">
                                        <option value="<?php echo $row[15]; ?>"><?php echo $row[16]; ?></option>
                                        <?php
                                        $sqlCCT = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico != '$row[15]'";
                                        $g = $mysqli->query($sqlCCT);
                                        while($f = mysqli_fetch_row($g)){
                                            echo '<option value="'.$f[0].'">'.$f[1].'</option>';
                                        }
                                        ?> 
                                    </select>
                                </td>
                                <td class="campos text-left">
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="proyecto'.$row[0].'">'. (ucwords(strtolower($row[18]))).'</label>'; ?>
                                    <select style="display: none;padding:2px;height:19px" class="col-sm-12 campoD" id="sltProyecto<?php echo $row[0]; ?>">
                                        <option value="<?php echo $row[17]; ?>"><?php echo $row[18]; ?></option>
                                        <?php 
                                        $sqlCP = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico != $row[17]";
                                        $result = $mysqli->query($sqlCP);
                                        while ($y = mysqli_fetch_row($result)){
                                            echo '<option value="'.$y[0].'">'.$y[1].'</option>';
                                        }
                                        ?>
                                        <!-- Validación de campos en la tabla -->                                                                                                                                              
                                    </select>
                                </td>
                                <!-- Campo de valor debito y credito. Validación para imprimir valor -->
                                <td class="campos text-right" align="center">

                                    <?php 

                                    if ($row[4] == 1) {
                                        if($row[19] >= 0){
                                            $sumar += $row[19];
                                            echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">'.number_format($row[19], 2, '.', ',').'</label>';
                                            echo '<input maxlength="50" align="center" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px;" class="col-sm-12 text-left campoD" type="text" name="txtDebito'.$row[0].'" id="txtDebito'.$row[0].'" value="'.$row[19].'" />';
                                        }else{
                                            echo '<label style="font-weight:normal" id="debitoP'.$row[0].'">0</label>';
                                            echo '<input maxlength="50" type="text" onkeypress="return justNumbers(event)" align="center" style="display:none;padding:2px;height:19px;" class="col-sm-12 campoD text-left" name="txtDebito'.$row[0].'"  id="txtDebito'.$row[0].'" value="0"/>';
                                        }  
                                    }else if($row[4] == 2){
                                        if($row[19] <= 0){
                                            $x = (float) substr($row[19],'1');
                                            $sumar += $x;
                                            echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">'.number_format($x, 2,'.', ',').'</label>';
                                            echo '<input maxlength="50" align="center" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px;" class="col-sm-12 campoD text-left" type="text" name="txtDebito'.$row[0].'" id="txtDebito'.$row[0].'" value="'.$x.'" />';
                                        }else{
                                            echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">0</label>';
                                            echo '<input maxlength="50" align="center" onkeypress="return justNumbers(event)" type="text" style="display:none;padding:2px;height:19px;" class="col-sm-12 campoD text-left" name="txtDebito'.$row[0].'"  id="txtDebito'.$row[0].'" value="0"/>';
                                        }
                                    }

                                   ?>                                            
                                </td>
                                <td class="campos text-right">
                                    <?php
                                    if ($row[4] == 2) {
                                        if($row[19] >= 0){
                                            $sumaT += $row[19];
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">'.number_format($row[19], 2, '.', ',').'</label>';
                                            echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="text" name="txtCredito'.$row[0].'" id="txtCredito'.$row[0].'" value="'.$row[19].'" />';                                                                                                
                                        }else{
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">0</label>';
                                            echo '<input maxlength="50" type="text" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  name="txtCredito'.$row[0].'"  id="txtCredito'.$row[0].'" value="0"/>';
                                        }
                                    }else if($row[4] == 1){
                                       if($row[19] <= 0){
                                            $x = (float) substr($row[19],'1');
                                            $sumaT += $x;
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">'.number_format($x, 2, '.', ',').'</label>';
                                            echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px;" class="col-sm-12 text-left campoD"  type="text" name="txtCredito'.$row[0].'" id="txtCredito'.$row[0].'" value="'.$x.'" />';                                                                                                
                                    }else{
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">0</label>';
                                            echo '<input maxlength="50" type="text" onkeypress="return justNumbers(event)" class="col-sm-12 text-left campoD" style="display:none;padding:2px;height:19px" name="txtCredito'.$row[0].'" id="txtCredito'.$row[0].'" value="0"/>';
                                        }
                                    }?>                                    
                                </td>
                                <td>
                                    <a href="#" class="col-sm-6">Mov</a>
                                    <div >
                                        <table id="tab<?php echo $row[0] ?>" style="padding:0px;background-color:transparent;background:transparent;">
                                                <tbody>
                                                    <tr style="background-color:transparent;">
                                                        <td style="background-color:transparent;">
                                                            <a  href="#<?php echo $row[0];?>" title="Guardar" id="guardar<?php echo $row[0]; ?>" style="display: none;" onclick="javascript:guardarCambios(<?php echo $row[0]; ?>)">
                                                                <li class="glyphicon glyphicon-floppy-disk"></li>
                                                            </a>
                                                        </td>
                                                        <td style="background-color:transparent;">
                                                            <a href="#<?php echo $row[0];?>" title="Cancelar" id="cancelar<?php echo $row[0] ?>" style="display: none" onclick="javascript:cancelar(<?php echo $row[0];?>)" >
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
                    <?php 
                    $valorD = $sumar;
                    $valorC = $sumaT;
                    #Diferencia
                    $diferencia = $valorD - $valorC;
                    ?>
                    <style>
                        .valores:hover{
                            cursor: pointer;
                            color:#1155CC;
                        }
                    </style>
                    <div class="col-sm-offset-6  col-sm-6 text-left">
                        <div class="col-sm-2">
                            <div class="form-group" style="margin-top:5px;margin-bottom:-10px" align="left">                                    
                                <label class="control-label">
                                    <strong>Totales:</strong>
                                </label>                                
                            </div>
                        </div>                        
                        <div class="col-sm-2 text-right" style="margin-top:5px;" align="left">
                            <?php 
                            if (($valorD) === NULL) { ?>
                                 <label class="control-label valores" title="Suma débito">0</label>                   
                            <?php
                            }else { ?>
                                 <label class="control-label valores" title="Suma débito"><?php echo number_format($valorD, 2, '.', ',') ?></label>
                            <?php }
                            ?>
                        </div>                        
                        <div class="col-sm-2 text-right col-sm-offset-1" style="margin-top:5px;" align="left">
                            <?php 
                            if ($valorC === NULL) { ?>
                                <label class="control-label valores" title="Suma crédito">0</label>
                            <?php
                            }else{ ?>
                                <label class="control-label valores" title="Suma crédito"><?php echo number_format($valorC, 2, '.', ','); ?></label>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="col-sm-2 text-right" style="margin-top:5px;" align="left">
                            <?php 
                            if ($diferencia === 0) { ?>
                                  <label class="control-label text-right valores" title="Diferencia">0.00</label>                          
                            <?php }else{ ?>
                                  <label class="control-label text-right valores" title="Diferencia"><?php echo number_format($diferencia, 2, '.', ',') ; ?></label>
                            <?php    
                            }
                            ?>
                        </div> 
                    </div>                                       
                </div>
                                   <!-- onclick="return ventanaSecundaria('registrar_GF_DESTINO.php')" -->
                                        
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
         <div class="modal fade" id="myModal" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>¿Desea eliminar el registro seleccionado de Detalle Comprobante?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal1" role="dialog" align="center" >
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
        <div class="modal fade" id="myModal2" role="dialog" align="center" >
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
        <div class="modal fade" id="infoM" role="dialog" align="center" >
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
        <div class="modal fade" id="noModifico" role="dialog" align="center" >
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
        <script type="text/javascript" src="js/menu.js"></script>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        
        <script type="text/javascript">           
            function eliminar(id){
                var result = '';
                $("#myModal").modal('show');
                $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminarDetalleComprobanteJson.php?id="+id,
                    success: function (data) {
                    result = JSON.parse(data);
                    if(result==true)
                      $("#myModal1").modal('show');
                    else
                      $("#myModal2").modal('show');
                    }
                });
            });
        }
        function cargarT2(id){
            $("#sltTercero"+id).prop('disabled',true);
            var padre = 0;
            $("#sltC"+id).change(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    $("#sltTercero"+id).prop('disabled',true);
                }else{
                    padre = $("#sltC"+id).val();
                }

                var form_data = {
                    is_ajax:1,
                    data:+padre
                };

                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultarTercero.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var tercero = document.getElementById('sltTercero'+id);
                        if (data==1) {
                             tercero.disabled=false;
                        }else if(data==2){
                            $("#sltTercero"+id).prop('disabled',true);
                        }                                                       
                    }
                });
            });
        }
        function cargarT(id){
            $("#sltTercero"+id).prop('disabled',true);
            var padre = 0;
            $("#sltC"+id).append(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    $("#sltTercero"+id).prop('disabled',true);
                }else{
                    padre = $("#sltC"+id).val();
                }

                var form_data = {
                    is_ajax:1,
                    data:+padre
                };

                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultarTercero.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var tercero = document.getElementById('sltTercero'+id);
                        if (data==1) {
                             tercero.disabled=false;
                        }else if(data==2){
                            $("#sltTercero"+id).prop('disabled',true);
                        }                                                       
                    }
                });
            });
        }
        
        function cargarCentro(id){
            $("#sltcentroC"+id).prop('disabled',true);
            var padre = 0;
            $("#sltC"+id).append(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    $("#sltcentroC"+id).prop('disabled',true);
                }else{
                    padre = $("#sltC"+id).val();
                }
                var form_data = {
                    is_ajax:1,
                    data:+padre
                };                                        
                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultarCentroC.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var centro = document.getElementById('sltcentroC'+id);
                        if (data==1) {
                            centro.disabled=false; 
                        }else if(data==2){
                            centro.disabled=true; 
                        }                                                       
                    }
                });
            });
        }
        
        function cargarCentro2(id){
            $("#sltcentroC"+id).prop('disabled',true);
            var padre = 0;
            $("#sltC"+id).append(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    $("#sltcentroC"+id).prop('disabled',true);
                }else{
                    padre = $("#sltC"+id).val();
                }
                var form_data = {
                    is_ajax:1,
                    data:+padre
                };                                        
                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultarCentroC.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var centro = document.getElementById('sltcentroC'+id);
                        if (data==1) {
                            centro.disabled=false; 
                        }else if(data==2){
                            centro.disabled=true; 
                        }                                                       
                    }
                });
            });
        }
        
        function cargarProyecto(id){
            var padre = 0;
            $("#sltProyecto"+id).prop('disabled',true);
            $("#sltC"+id).append(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    $("#sltProyecto"+id).prop('disabled',true);
                }else{
                    padre = $("#sltC"+id).val();
                }
                var form_data = {
                    is_ajax:1,
                    data:+padre
                };                                        
                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultaProyecto.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var proyecto = document.getElementById('sltProyecto'+id);
                        if (data==1) {
                            proyecto.disabled=false; 
                        }else if(data==2){
                            $("#sltProyecto"+id).prop('disabled',true);
                        }                                                       
                    }
                });
            });
        }
        function cargarProyecto2(id){
            var padre = 0;
            $("#sltProyecto"+id).prop('disabled',true);
            $("#sltC"+id).change(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    $("#sltProyecto"+id).prop('disabled',true);
                }else{
                    padre = $("#sltC"+id).val();
                }
                var form_data = {
                    is_ajax:1,
                    data:+padre
                };                                        
                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultaProyecto.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var proyecto = document.getElementById('sltProyecto'+id);
                        if (data==1) {
                            proyecto.disabled=false; 
                        }else if(data==2){
                            $("#sltProyecto"+id).prop('disabled',true);
                        }                                                       
                    }
                });
            });
        }
        </script>            
        <script>
            function modificarComprobante(){
                var fecha = $("#fecha").val();
                var tipoComprobante = $("#sltTipoC").val();
                var numeroComprobante = $("#txtNumero").val();
                var tercero = $("#sltTercero").val();
                var centroCosto = $("#sltCentroC").val();
                var proyecto = $("#sltProyecto").val();
                var claseContrato = $("#sltClaseCT").val();
                var numeroContrato = $("#txtNumeroCT").val();
                var estado = $("#txtEstado").val();
                var descripcion = $("#txtDescripcion").val();
                
                var form_data = {
                    is_ajax:1,
                    fecha:fecha,
                    tipoCmbnt:tipoComprobante,
                    numCmbnt:numeroComprobante,
                    tercero:tercero,
                    centroC:centroCosto,
                    proycto:proyecto,
                    claseCC:claseContrato,
                    numCont:numeroContrato,
                    estado:estado,
                    descpt:descripcion
                };
                
                $.ajax({
                    
                });
            }
        </script>
        <script type="text/javascript">
            function modificar(id){
                if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
                    var sltcuentaC = 'sltC'+$("#idPrevio").val();
                    var lblCuentaC = 'cuenta'+$("#idPrevio").val();
                    var sltTerceroC = 'sltTercero'+$("#idPrevio").val();
                    var lblTerceroC = 'tercero'+$("#idPrevio").val();
                    var sltCentroCC = 'sltcentroC'+$("#idPrevio").val();
                    var lblCentroCC = 'centroC'+$("#idPrevio").val();
                    var sltProyectoC = 'sltProyecto'+$("#idPrevio").val();
                    var lblProyectoC = 'proyecto'+$("#idPrevio").val();
                    var txtDebitoC = 'txtDebito'+$("#idPrevio").val();
                    var lblDebitoC = 'debitoP'+$("#idPrevio").val();
                    var txtCreditoC = 'txtCredito'+$("#idPrevio").val();
                    var lblCreditoC = 'creditoP'+$("#idPrevio").val();
                    var guardarC = 'guardar'+$("#idPrevio").val();
                    var cancelarC = 'cancelar'+$("#idPrevio").val();
                    var tablaC = 'tab'+$("#idPrevio").val();
                    
                    $("#"+sltcuentaC).css('display','none');                               
                    $("#"+lblCuentaC).css('display','block');
                    $("#"+sltTerceroC).css('display','none');
                    $("#"+lblTerceroC).css('display','block');
                    $("#"+sltCentroCC).css('display','none');
                    $("#"+lblCentroCC).css('display','block');
                    $("#"+sltProyectoC).css('display','none');
                    $("#"+lblProyectoC).css('display','block');
                    $("#"+txtDebitoC).css('display','none');
                    $("#"+lblDebitoC).css('display','block');
                    $("#"+txtCreditoC).css('display','none');
                    $("#"+lblCreditoC).css('display','block');                
                    $("#"+guardarC).css('display','none');
                    $("#"+cancelarC).css('display','none');
                    $("#"+tablaC).css('display','none');
                }
                
                var sltcuenta = 'sltC'+id;
                var lblCuenta = 'cuenta'+id;
                var sltTercero = 'sltTercero'+id;
                var lblTercero = 'tercero'+id;
                var sltCentroC = 'sltcentroC'+id;
                var lblCentroC = 'centroC'+id;
                var sltProyecto = 'sltProyecto'+id;
                var lblProyecto = 'proyecto'+id;
                var txtDebito = 'txtDebito'+id;
                var lblDebito = 'debitoP'+id;
                var txtCredito = 'txtCredito'+id;
                var lblCredito = 'creditoP'+id;
                var guardar = 'guardar'+id;
                var cancelar = 'cancelar'+id;
                var tabla = 'tab'+id;
                
                $("#"+sltcuenta).css('display','block');                               
                $("#"+lblCuenta).css('display','none');
                $("#"+sltTercero).css('display','block');
                $("#"+lblTercero).css('display','none');
                $("#"+sltCentroC).css('display','block');
                $("#"+lblCentroC).css('display','none');
                $("#"+sltProyecto).css('display','block');
                $("#"+lblProyecto).css('display','none');
                $("#"+txtDebito).css('display','block');
                $("#"+lblDebito).css('display','none');
                $("#"+txtCredito).css('display','block');
                $("#"+lblCredito).css('display','none');                
                $("#"+guardar).css('display','block');
                $("#"+cancelar).css('display','block');
                $("#"+tabla).css('display','block');
                $("#idActual").val(id);
                if($("#idPrevio").val() != id){
                    $("#idPrevio").val(id);   
                }
               }
        </script>
        <script type="text/javascript">
            function cancelar(id){
                var sltcuenta = 'sltC'+id;
                var lblCuenta = 'cuenta'+id;
                var sltTercero = 'sltTercero'+id;
                var lblTercero = 'tercero'+id;
                var sltCentroC = 'sltcentroC'+id;
                var lblCentroC = 'centroC'+id;
                var sltProyecto = 'sltProyecto'+id;
                var lblProyecto = 'proyecto'+id;
                var txtDebito = 'txtDebito'+id;
                var lblDebito = 'debitoP'+id;
                var txtCredito = 'txtCredito'+id;
                var lblCredito = 'creditoP'+id;
                var guardar = 'guardar'+id;
                var cancelar = 'cancelar'+id;
                var tabla = 'tab'+id;
                
                $("#"+sltcuenta).css('display','none');                               
                $("#"+lblCuenta).css('display','block');
                $("#"+sltTercero).css('display','none');
                $("#"+lblTercero).css('display','block');
                $("#"+sltCentroC).css('display','none');
                $("#"+lblCentroC).css('display','block');
                $("#"+sltProyecto).css('display','none');
                $("#"+lblProyecto).css('display','block');
                $("#"+txtDebito).css('display','none');
                $("#"+lblDebito).css('display','block');
                $("#"+txtCredito).css('display','none');
                $("#"+lblCredito).css('display','block');                
                $("#"+guardar).css('display','none');
                $("#"+cancelar).css('display','none');
                $("#"+tabla).css('display','none');
            }
        </script>
        <script type="text/javascript">
            function guardarCambios(id){
                var sltTercero = 'sltTercero'+id;
                var sltRegP = 'sltRegP'+id;
                var sltProyecto = 'sltProyecto'+id;
                var txtDebito = 'txtDebito'+id;
                var txtCredito = 'txtCredito'+id;
                
                var form_data = {
                    is_ajax:1,
                    id:+id,
                    tercero:$("#"+sltRegP).val(),
                    centroC:$("#"+sltCentroC).val(),
                    proyecto:$("#"+sltProyecto).val(),
                    debito:$("#"+txtDebito).val(),
                    credito:$("#"+txtCredito).val()
                };
                var result='';
                $.ajax({
                    type: 'POST',
                    url: "json/modificarDetalleComprobante.php",
                    data:form_data,
                    success: function (data) {
                        result = JSON.parse(data);
                        if (result==true) {
                            $("#infoM").modal('show');
                        }else{
                            $("#noModifico").modal('show');
                        }
                        console.log(data);
                    }
                });
            }
        </script>        
        <script type="text/javascript">
            $('#btnModifico').click(function(){
                document.location = 'registrar_GF_COMPROBANTE_CONTABLE.php';
            });
        </script>
        <script type="text/javascript">
            $('#btnNoModifico').click(function(){
                document.location = 'registrar_GF_COMPROBANTE_CONTABLE.php';
            });
        </script>
        <script type="text/javascript">
            $('#ver1').click(function(){
                document.location = 'registrar_GF_COMPROBANTE_CONTABLE.php';
            });
        </script>
        <script type="text/javascript">    
            $('#ver2').click(function(){  
                document.location = 'registrar_GF_COMPROBANTE_CONTABLE.php';
            });
        </script>
        <script src="js/select/select2.full.js"></script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        placeholder: "Seleccione 1",
        allowClear: true
      });
     
      
    });
  </script>
        <?php require_once('footer.php'); ?>       
    </body>
</html>
