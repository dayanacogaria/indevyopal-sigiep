<?php

    require_once './Conexion/conexion.php';
    require_once('Conexion/ConexionPDO.php');
    require_once './head_listar.php';

    $con        = new ConexionPDO();
    $anno       = $_SESSION['anno'];
    $compania   = $_SESSION['compania'];
    #session_start();
    @$idPQR = $_GET['id'];
    @$mod   = $_GET['mod'];
    @$mod1  = $_GET['mod1'];
    
    $id_peticion = $idPQR; 

    if(!empty($mod)){
        $modificarPQR = "SELECT id_unico, id_unidad_vivienda, id_tercero, fecha_hora, id_estado_pqr, id_factura, observaciones, id_afavor FROM gpqr_pqr WHERE id_unico = '$idPQR'";
            
        $modiPQR = $mysqli->query($modificarPQR);
        $resmod  = mysqli_fetch_row($modiPQR);

        $uni = $resmod[1];
        $est = $resmod[4];
        $fec = $resmod[3];

        $fec =  date("d/m/Y", strtotime($fec));
        $afa = $resmod[7];
        $fac = $resmod[5];
        $ter = $resmod[2];
        $Obs = $resmod[6];

        $IndicaCierre = "SELECT dtp.* FROM gpqr_detalle_pqr dtp LEFT JOIN gpqr_clase c ON dtp.id_clase = c.id_unico WHERE c.indicador_cierre = 1 AND dtp.id_pqr = '$idPQR'";
        $resCierre = $mysqli->query($IndicaCierre);
        $nresC = mysqli_fetch_row($resCierre);     
        
    }else{
        @$uni = $_GET['uni'];
        @$est = $_GET['est'];
        @$fec = $_GET['fecha'];
        @$afa = $_GET['afavor'];
        @$fac = $_GET['fac'];
        @$ter = $_GET['tercero'];
        @$Obs = $_GET['obs'];
    
    }
    
    $existDetalle = "SELECT * FROM gpqr_detalle_pqr WHERE id_pqr = '$idPQR'";
    $resexi = $mysqli->query($existDetalle);
    $nresD = mysqli_num_rows($resexi);
    #echo "num: ".$nresD;
    
    #consulta el tecero que selecciono el usuario
    $Terceros  = "SELECT tr.id_unico,
                        tr.numeroidentificacion,
                        IF(CONCAT_WS(' ',
                            tr.nombreuno,
                            tr.nombredos,
                            tr.apellidouno,
                            tr.apellidodos) 
                        IS NULL OR CONCAT_WS(' ',
                            tr.nombreuno,
                            tr.nombredos,
                            tr.apellidouno,
                            tr.apellidodos) = '',
                            (tr.razonsocial),
                        CONCAT_WS(' ',
                            tr.nombreuno,
                            tr.nombredos,
                            tr.apellidouno,
                            tr.apellidodos)) AS NOMBRE
                FROM gf_tercero tr
                LEFT JOIN gf_perfil_tercero pt ON pt.tercero = tr.id_unico
                WHERE tr.id_unico = '$ter'";
                                            
    $terc = $mysqli->query($Terceros);
    $ter_e = mysqli_fetch_row($terc);

    if(empty($ter_e[0])){
        $id_T = "";
        $nomT = "Tercero";
    }else{
        $id_T = $ter_e[0];
        $nomT = $ter_e[1].' - '.$ter_e[2];
    }

    #consulta la unidad de vivenda que selecciono el usuario

    $Codigo  = "SELECT uvms.id_unico,
                        p.codigo_catastral,
                        IF(CONCAT_WS(' ',
                            tr.nombreuno,
                            tr.nombredos,
                            tr.apellidouno,
                            tr.apellidodos) 
                        IS NULL OR CONCAT_WS(' ',
                            tr.nombreuno,
                            tr.nombredos,
                            tr.apellidouno,
                            tr.apellidodos) = '',
                            (tr.razonsocial),
                        CONCAT_WS(' ',
                            tr.nombreuno,
                            tr.nombredos,
                            tr.apellidouno,
                            tr.apellidodos)) AS NOMBRE,
                        s.nombre
                FROM gp_unidad_vivienda_medidor_servicio uvms
                LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico
                 LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico
                LEFT JOIN  gp_predio1 p ON uv.predio = p.id_unico
                LEFT JOIN  gp_sector s ON uv.sector = s.id_unico
                LEFT JOIN  gf_tercero tr ON uv.tercero = tr.id_unico
                WHERE uvms.id_unico = '$uni'";
                                           
    $CodCa = $mysqli->query($Codigo);
    $rowC = mysqli_fetch_row($CodCa);

    if(empty($rowC[0])){
        $id_C = "";
        $unidaV = "Unidad de Vivienda";
    }else{
        $id_C = $rowC[0];
        $unidaV = $rowC[1].' - '.$rowC[2].' - '.$rowC[3];
    }
    
    #consulta e estado que selecciono el usuario
    $esta   = "SELECT id_unico, nombre FROM gpqr_estado WHERE compania = '$compania' AND id_unico = '$est'";
    $estado = $mysqli->query($esta);
    $Estad = mysqli_fetch_row($estado);

    if(empty($Estad[0])){
        $id_E = "";
        $nomE = "Estado";
    }else{
        $id_E = $Estad[0];
        $nomE = $Estad[1];
    } 

    #consulta la opcion de a favor que selecciono el ususario
    $afavor = "SELECT id_unico, nombre FROM  gpqr_afavor WHERE id_unico = '$afa'";
    $resafa  = $mysqli->query($afavor);
    $rowAF = mysqli_fetch_row($resafa);

    if(empty($rowAF)){
        $AFAV = "A Favor";
    }else{
        $AFAV = $rowAF[1];
    }

    
   
?>


        <title>PQR</title>
        <script src="dist/jquery.validate.js"></script>
        <!-- Librerias de carga para el datapicker -->
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <!-- select2 -->
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <style>
            /*Estilos tabla*/
            table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
            table.dataTable tbody td,table.dataTable tbody td{padding:1px}
            .dataTables_wrapper .ui-toolbar{padding:2px}
            /*Campos dinamicos*/
            .campoD:focus {
                border-color: #66afe9;
                outline: 0;            
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
                font-size: 10px;
                font-family: Arial;
            }

            .client-form input[type="text"]{
                width: 100%;
            }
            .client-form select{
                width: 100%;
            }

            .client-form input[type="file"]{
                width: 100%;
            }

        </style>  
        <style >
    
            label #sltEstado-error, #sltFechaS-error, #sltFac-error, #sltTipoS-error, #sltDesc-error,#sltfecha-error, #sltClase-error, #sltAfavor-error {
                display: block;
                color: #155180;
                font-weight: normal;
                font-style: italic;
                font-size: 10px
            }

            body{
                font-size: 11px;
            } 
            table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
            table.dataTable tbody td,table.dataTable tbody td{padding:1px}
            .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
            font-family: Arial;}
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
              });

              $(".cancel").click(function() {
                validator.resetForm();
              });
            });
        </script>

        <script>

            $().ready(function() {
              var validator = $("#Detalle").validate({
                    ignore: "",
                errorPlacement: function(error, element) {

                  $( element )
                    .closest( "form" )
                      .find( "label[for='" + element.attr( "id" ) + "']" )
                        .append( error );
                },
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


                $("#sltFechaS").datepicker({changeMonth: true,});
                $("#sltfecha").datepicker({changeMonth: true,});
                


            });
        </script>
    </head>
    
    <body >   

        <div class="container-fluid text-left">
            <div class="row content">
                <?php require_once ('menu.php'); ?>
                <div class="col-sm-10" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">PQR</h2>
                    <a href="listar_GPQR_PQR.php" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-13px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:85%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 10px; margin-left: 6%;  background-color: #0e315a; color: white; border-radius: 5px">.</h5> 
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         

                            <!--Input que guarda el id del PQR  -->
                            <input type="hidden" id="txtIdPQR" name="txtIdPQR" value="<?php echo $idPQR ?>">
                            <!--Input que guarda si el formulario fue llamado del listar  -->
                            <input type="hidden" id="txtMOD" name="txtMOD" value="<?php echo $mod ?>">
                             <!--Input que guarda si el formulario fue llamado del listar y al modficar no esta bloqueados los selects  -->
                            <input type="hidden" id="txtMOD1" name="txtMOD1" value="<?php echo $mod1 ?>">
                            <div class="col-sm-12 col-md-12 col-lg-12">  
                                <div class="form-group form-inline" style="margin-top:-20px;">
                                    <label for="sltTer" class="col-sm-2 col-md-2 col-lg-2 control-label" style="margin-left: -3%">Tercero:</label>
                                    <select name="sltTer" id="sltTer" title="Seleccione el Tercero" style="width: 20%;height: 30px" class=" form-control col-sm-2 col-md-2 col-lg-2" >
                                        <option value='<?php echo $id_T ?>'><?php echo $nomT ?></option>
                                        <?php 
                                            $Tercero  = "SELECT DISTINCT(tr.id_unico),
                                                            tr.numeroidentificacion,
                                                            IF(CONCAT_WS(' ',
                                                             tr.nombreuno,
                                                             tr.nombredos,
                                                             tr.apellidouno,
                                                             tr.apellidodos) 
                                                             IS NULL OR CONCAT_WS(' ',
                                                             tr.nombreuno,
                                                             tr.nombredos,
                                                             tr.apellidouno,
                                                             tr.apellidodos) = '',
                                                             (tr.razonsocial),
                                                             CONCAT_WS(' ',
                                                             tr.nombreuno,
                                                             tr.nombredos,
                                                             tr.apellidouno,
                                                             tr.apellidodos)) AS NOMBRE
                                                    FROM gf_tercero tr
                                                    LEFT JOIN gf_perfil_tercero pt ON pt.tercero = tr.id_unico
                                                    WHERE tr.id_unico != '$id_T'";
                                            
                                            $ter = $mysqli->query($Tercero);
                                                                               
                                            while($rowT = mysqli_fetch_row($ter)){
                                        ?>
                                               <option value="<?php echo $rowT[0] ?>"><?php echo $rowT[1].' - '.$rowT[2] ?></option>

                                        <?php
                                            }                                       
                                        ?>
                                    </select>

                                    <label for="sltUV" class="col-sm-2 col-md-2 col-lg-2 control-label" style="margin-left: -3%">Unidad Viv:</label>
                                    <select name="sltUV" id="sltUV" title="Seleccione el Tercero" style="width: 20%;height: 30px" class=" form-control col-sm-2 col-md-2 col-lg-2" onchange="javascript:encuentraFac();">
                                        <option value='<?php echo $id_C ?>'><?php echo $unidaV ?></option>
                                        <?php 
                                            $Cod_Cat  = "SELECT uvms.id_unico,
                                                        p.codigo_catastral,
                                                        IF(CONCAT_WS(' ',
                                                             tr.nombreuno,
                                                             tr.nombredos,
                                                             tr.apellidouno,
                                                             tr.apellidodos) 
                                                             IS NULL OR CONCAT_WS(' ',
                                                             tr.nombreuno,
                                                             tr.nombredos,
                                                             tr.apellidouno,
                                                             tr.apellidodos) = '',
                                                             (tr.razonsocial),
                                                             CONCAT_WS(' ',
                                                             tr.nombreuno,
                                                             tr.nombredos,
                                                             tr.apellidouno,
                                                             tr.apellidodos)) AS NOMBRE,
                                                             s.nombre

                                                    FROM gp_unidad_vivienda_medidor_servicio uvms
                                                    LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico
                                                    LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico
                                                    LEFT JOIN  gp_predio1 p ON uv.predio = p.id_unico
                                                    LEFT JOIN  gp_sector s ON uv.sector = s.id_unico
                                                    LEFT JOIN  gf_tercero tr ON uv.tercero = tr.id_unico
                                                    WHERE uvms.id_unico != '$id_C'";
                                            
                                            $CodC = $mysqli->query($Cod_Cat);
                                                                               
                                            while($rowCC = mysqli_fetch_row($CodC)){
                                        ?>
                                               <option value="<?php echo $rowCC[0] ?>"><?php echo $rowCC[1].' - '.$rowCC[2].' - '.$rowCC[3] ?></option>

                                        <?php
                                            }                                       
                                        ?>
                                    </select>
                                    <!---Script para invocar Date Picker-->
                                                              
                                        
                                    <label for="sltEstado" class="col-sm-2 col-md-2 col-lg-2 control-label" style="margin-left: -4%"><strong class="obligado">*</strong>Estado:</label>
                                    <select name="sltEstado" id="sltEstado" title="Seleccione el Estado" style="width: 15%;height: 30px" class=" form-control col-sm-2 col-md-2 col-lg-2" required>
                                        <option value='<?php echo $id_E  ?>'><?php echo  $nomE ?></option>
                                        <?php 
                                            $es   = "SELECT id_unico, nombre FROM gpqr_estado WHERE compania = '$compania' AND id_unico != '$id_E' AND id_unico != 2";
                                            $esta = $mysqli->query($es);
                                                                  		
                            		    while($rowES = mysqli_fetch_row($esta)){
                            		        echo "<option value=".$rowES[0].">".$rowES[1]."</option>";
                            		    }     	                                
                                        ?>
                                    </select>
                                        
                                </div>

                                <div class="form-group form-inline">

                                    <script type="text/javascript">
                          
                                        $(document).ready(function() {
                                            $("#datepicker").datepicker();
                                        });
                                    </script>
                            
                                    <label for="sltFechaS" class="col-sm-2 col-md-2 col-lg-2 control-label" style="margin-left: -3%"><strong class="obligado">*</strong>Fecha:</label>
                                    <input style="width:20%;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="sltFechaS" id="sltFechaS"    title="Ingrese la fecha del PQR" readonly required value="<?php echo $fec ?>" >  

                                    <label for="txtAfavor" class="col-sm-2 col-md-2 col-lg-2 control-label" style="margin-left: -3%">A Favor:</label>
                                    <input style="width:20%" class="col-sm-2 input-sm" type="text" name="txtAfavor" id="txtAfavor" placeholder="A Favor" value="<?php echo $AFAV ?>" readonly>
                                     
                            
                                    <label for="sltFac" class="col-sm-2 control-label" style="margin-left: -4%">Últimas Facturas:</label>
                                    <select name="sltFac" id="sltFac" title="Seleccione una Factura" style="width: 15%;height: 30px" class=" form-control col-sm-2 col-md-2 col-lg-2" >
                                        
                                        <?php 
                                            echo $ValF = "SELECT f.id_unico, f.numero_factura, f.fecha_factura FROM gp_factura f WHERE f.id_unico = '$fac' ";
                                            $resF = $mysqli->query($ValF);
                                            $rowFA = mysqli_fetch_row($resF);
                                            if(empty($rowFA)){
                                                $idF = "";
                                                $NF  = "Número de Factura";
                                                $fechaFA = "";
                                            }else{
                                                $idF = $rowFA[0];
                                                $NF  = $rowFA[1];
                                                $fechaFA = date('d/m/Y', strtotime($rowFA[2]));
                                            }

                                            
                                            
                                        ?>
                                        <option value="<?php echo $idF ?>"><?php echo $NF.' - '.$fechaFA ?></option>
                                        <?php 
                                            echo $sql = "SELECT f.id_unico, f.numero_factura, f.fecha_factura FROM gp_factura f WHERE f.unidad_vivienda_servicio = '$uni' ORDER BY f.id_unico DESC LIMIT 6";
                                            $resultado = $mysqli->query($sql);

                                            while ($row = mysqli_fetch_row($resultado))
                                            {
                                                $fechaF = date('d/m/Y', strtotime($row[2]));
                                                echo '<option value="'.$row[0].'">'.($row[1]).' - '.$fechaF.'</option>';
                                            } 
                                        ?> 
                                    </select>       
                                    
                                    
                            
                                    
                                </div>  

                                <div class="form-group form-inline">
                                    <label for="txtObservaci" class="col-sm-2 control-label" style="margin-left: -3%">Observaciones:</label>
                                    <input style="width:35%" class="col-sm-2 input-sm" type="text" name="txtObservaci" id="txtObservaci" title="Ingrese las Observaciones" placeholder="Observaciones" value="<?php echo $Obs  ?>">
                            
                                    <label for="No" class="col-sm-2 control-label"></label>
                                    <button type="submit" id="btnGuardar" class="btn btn-primary sombra col-sm-1" style="width:40px; margin-left: 73% ; margin-top: -4%" title="GUARDAR"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                    
                                    <button type="button" id="btnEditar" class="btn btn-primary sombra col-sm-1" style="width:40px;margin-left: 78%;  margin-top: -4%" title="MODIFICAR" onclick="javascript:modificarPQR();" disabled><li class="glyphicon glyphicon-pencil"></li></button>

                                    <button type="button" id="btnTercero" class="btn btn-primary sombra col-sm-1" style="width:40px;margin-left: 83%;  margin-top: -4%" title="REGISTRAR TERCERO" onclick="javascript:RegTer();"><li class="glyphicon glyphicon-user"></li></button>

                                    <button type="button" id="btnNuevo" class="btn btn-primary sombra col-sm-1" style="width:40px; margin-left: 88%;  margin-top: -4%" title="REGISTRAR NUEVO PQR" disabled onclick="javascript:registrarNuevo();"><li class="glyphicon glyphicon-plus"></li></button>

                                </div>

                            </div>
                            <input type="hidden" id="txtidP" name="txtidP" value="<?php echo $id_pro ?>">
                            <input type="hidden" id="txtiP" name="txtiP" value="<?php echo $valor ?>">
                        </form>
                    </div>
                </div>
                <?php   if(!empty($mod)){
                            if($nresC < 1){
                ?>
                                <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 10px;">
                                    <div class="client-form">
                                        <form id="Detalle" name="Detalle" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrarDET()">
                                            <div class="form-group form-inline">
                                                <input type="hidden" name="txtIdP" id="txtIdP" value="<?php echo $idPQR ?>">
                                                <input type="hidden" name="fechaPQR" id="fechaPQR" value="<?php echo $fec ?>">
                                                <label for="sltTipoS" class="col-sm-1 col-md-1 col-lg-1 control-label" style="margin-left: 2%"><strong style="color:#03C1FB;">*</strong>Tipo servicio:</label>
                                                <select name="sltTipoS" id="sltTipoS" title="Seleccione el Tipo de Servicio" style="width: 15%;height: 30px" class=" form-control col-sm-2 col-md-2 col-lg-2" required>
                                                    <option value="">Tipo Servicio</option>
                                                    <?php
                                                        $unidadV = "SELECT id_unidad_vivienda FROM gpqr_pqr WHERE id_unico = '$id_PQR'";

                                                        $resuni = $mysqli->query($unidadV);
                                                        $rowU = mysqli_fetch_row($resuni);
                                                        if(!empty($rowU[0])){
                                                            $uniVi  =   "SELECT uvs.unidad_vivienda 
                                                                        FROM gp_unidad_vivienda_servicio uvs 
                                                                        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                                                                                    WHERE uvms.id_unico = '$rowU[0]'";
                                                            $resuniV = $mysqli->query($uniVi);
                                                            $rowUV = mysqli_fetch_row($resuniV); 

                                                            $row = "SELECT  ts.id_unico, ts.nombre
                                                                    FROM gp_tipo_servicio ts
                                                                    LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvs.tipo_servicio =ts.id_unico
                                                                                      
                                                                    WHERE uvs.unidad_vivienda = '$rowUV[0]'";
                                                        }else{
                                                            $row = "SELECT  ts.id_unico, ts.nombre
                                                                    FROM gp_tipo_servicio ts";
                                                        }
                                                                    
                                                        $resS = $mysqli->query($row);
                                                        while($i = mysqli_fetch_row($resS)) {
                                                            echo '<option value="'.$i[0].'">'.ucwords(mb_strtolower($i[1])).'</option>';
                                                        }
                                                    ?>
                                                </select>

                                                <label for="sltDesc" class="col-sm-1 col-md-1 col-lg-1 control-label" style="margin-left: 2%" ><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                                                <select name="sltDesc" id="sltDesc" title="Seleccione una Opción" style="width:15%;height: 30px" class=" form-control col-sm-2 col-md-2 col-lg-2" required=>
                                                    <option value="">Descripción</option>
                                                    <?php
                                                        $row = $con->Listar("SELECT  id_unico, descripcion
                                                                            FROM gpqr_descripcion
                                                                            WHERE compania = '$compania'
                                                                            ORDER BY descripcion ASC ");
                                                        for ($i = 0; $i < count($row); $i++) {
                                                            echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                                                        }
                                                    ?>
                                                </select>
                                                <!---Script para invocar Date Picker-->
                                                                                  
                                                            
                                                <label for="sltfecha" class="col-sm-1 control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>
                                                <input class="col-sm-1 col-md-1 col-lg-1" type="text" name="sltfecha" id="sltfecha" style="width:12%;height: 30px" title="Ingrese la fecha del PQR" readonly required >  
                                               
                                                <label for="sltDetA" class="col-sm-2 col-md-2 col-lg-2 control-label" style="margin-left: -5%;" >Detalle Asociado:</label>
                                                <select name="sltDetA" id="sltDetA" title="Seleccione una Opción" style="width:15%;height: 30px" class=" form-control col-sm-2 col-md-2 col-lg-2">
                                                    <option value="">Detalle Asociado</option>
                                                    <?php

                                                        $row = $con->Listar("SELECT det.id_unico, des.descripcion FROM gpqr_detalle_pqr det left join gpqr_descripcion des on des.id_unico=det.id_descripcion where det.id_pqr= '$id_peticion'");
                                                           
                                                        for ($i = 0; $i < count($row); $i++) {
                                                            echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                                                        }
                                                    ?>
                                                </select>
                                                
                                            </div>
                                            <div class="form-group form-inline" style="margin-top: -15px;">
                                                <label for="sltClase" class="col-sm-1 col-md-1 col-lg-1 control-label" style="margin-left: 2%"><strong style="color:#03C1FB;">*</strong>Clase:</label>
                                                <select name="sltClase" id="sltClase" title="Seleccione la Clase" style="width: 15%;height: 30px" class=" form-control col-sm-2 col-md-2 col-lg-2" required>
                                                    <option value="">Clase</option>
                                                    <?php
                                                                $row = $con->Listar("SELECT id_unico, nombre FROM gpqr_clase  WHERE compania= '$compania'");
                                                           
                                                                for ($i = 0; $i < count($row); $i++) {

                                                                    echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                                                                }
                                                           ?>
                                                </select>

                                                <label for="sltAfavor" class="col-sm-1 col-md-1 col-lg-1 control-label" style="margin-left: 2%" ><strong style="color:#03C1FB;">*</strong>A Favor:</label>
                                                <select name="sltAfavor" id="sltAfavor" title="Seleccione una Opción" style="width:15%;height: 30px" class=" form-control col-sm-2 col-md-2 col-lg-2" disabled>
                                                    <option value="">A Favor</option>
                                                    <?php
                                                        $row = $con->Listar("SELECT  id_unico, nombre
                                                                            FROM gpqr_afavor 
                                                                            WHERE compania = '$compania'
                                                                            ORDER BY nombre ASC ");
                                                        for ($i = 0; $i < count($row); $i++) {
                                                            echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                                                        }
                                                   ?>
                                                </select>
                                                <!---Script para invocar Date Picker-->
                                                                                  
                                               
                                               
                                                <label for="txtruta" class="col-sm-1 control-label">Ruta:</label>
                                                <input class="col-sm-4 col-md-4 col-lg-4" type="file" name="txtruta" id="txtruta" style="width:39%;height: 30px" placeholder="Observaciones">  
                                                
                                            </div>
                                            <div class="form-group form-inline" style="margin-top: -15px;">
                                                <label for="txtObser" class="col-sm-1 control-label" style=" margin-left: 2%;">Observaciones:</label>
                                                <input class="col-sm-9 col-md-9 col-lg-9" type="text" name="txtObser" id="txtObser" style="width:41%;height: 30px" placeholder="Observaciones"> 

                                                <label for="No" class="col-sm-2 control-label"></label>
                                                <button type="submit" id="btnDetalle" class="btn btn-primary sombra col-sm-1" style="width:40px; margin-left: 95% ; margin-top: -3%" title="GUARDAR DETALLE"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                            </div>    
                                        </form>
                                    </div>
                                    
                                </div>    
                    <?php
                            }
                        }     
                    ?>
                
                <script>

                    function RegTer(){
                        window.location = 'registrar_TERCERO_CONTACTO_NATURAL.php';
                    }
                    function registrarNuevo(){
                        window.location = 'GPQR_PQR.php';
                    }
                </script>
                <script>
                    function registrar(){
                        
                        var mod    = 1;
                        var unidad = $("#sltUV").val();
                        var estado = $("#sltEstado").val();
                        var fecha  = $("#sltFechaS").val();  
                        var afavor = $("#sltAfavor").val();
                        var fac    = $("#sltFac").val();
                        var ter    = $("#sltTer").val();
                        var Obs    = $("#txtObservaci").val(); 
                        var uni    = $("#sltUV").val();

                        if(ter == "" && uni == ""){
                            $("#mensaje").html('Selecione un Tercero o una Unidad de Vivienda');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                $("#modalMensajes").modal("hide");
                            })
                        }else{
                            //jsShowWindowLoad('Guardando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "jsonPQR/gpqr_pqrJson.php?action=2",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                {
                                    //jsRemoveWindowLoad();
                                    console.log(response);
                                    if(response!=2){
                                        $("#mensaje").html('Información Guardada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#txtIdPQR").val(response);
                                            var idP = $("#txtIdPQR").val();
                                            window.location = 'GPQR_PQR.php?uni='+unidad+'&est='+estado+'&fecha='+fecha+'&afavor='+afavor+'&fac='+fac+'&tercero='+ter+'&obs='+Obs+'&mod='+mod+'&id='+idP;
                                        })
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Guardar Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })
                                    }
                                }
                            });
                        }         
                        
                    }
                    
                    function eliminarD(id) {
                        $("#myModal").modal('show');
                        $("#ver").click(function(){
                            //jsShowWindowLoad('Eliminando Datos ...');
                            $("#mymodal").modal('hide');
                            var form_data = {action:1, id:id};
                            $.ajax({
                                type: 'POST',
                                url: "jsonPQR/gpqr_detalle_pqrJson.php?action=1",
                                data: form_data,
                                success: function(response) {
                                    //jsRemoveWindowLoad();
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje").html('Información Eliminada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            document.location.reload();
                                        })
                                    } else if(response == 2){
                                        $("#mensaje").html('No Se Ha Podido Eliminar La Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                             $("#modalMensajes").modal("hide");
                                        })
                                    } else {
                                        $("#mensaje").html('No se puede eliminar la información, ya que el seguimiento posee Seguimiento(s)');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                             $("#modalMensajes").modal("hide");
                                        })
                                    }
                                }
                            });
                        });
                    }
                </script>
                <script type="text/javascript">
                    function registrarDET(){
                        jsShowWindowLoad('Guardando Datos ...');
                        var formData = new FormData($("#Detalle")[0]);
                        $.ajax({
                            type: 'POST',
                            url: "jsonPQR/gpqr_detalle_pqrJson.php?action=2",
                            data:formData,
                            contentType: false,
                            processData: false,
                            success: function(response)
                            {
                                jsRemoveWindowLoad();
                                console.log(response);
                                if(response==1){
                                    $("#mensaje").html('Información Guardada Correctamente');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        location.reload(); 
                                    })
                                } else {
                                    $("#mensaje").html('No Se Ha Podido Guardar Información');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        $("#modalMensajes").modal("hide");
                                    })
                                }
                            }
                        });
                    }
                </script>
                <script>
                    function modificarPQR(id){
                        var idP = $("#txtIdPQR").val();
                        var mod    = 1;
                        var unidad = $("#sltUV").val();
                        var estado = $("#sltEstado").val();
                        var fecha  = $("#sltFechaS").val();  
                        var afavor = $("#sltAfavor").val();
                        var fac    = $("#sltFac").val();
                        var ter    = $("#sltTer").val();
                        var Obs    = $("#txtObservaci").val(); 
                        var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "jsonPQR/gpqr_pqrJson.php?action=3",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                {
                                    //jsRemoveWindowLoad();
                                    console.log(response);
                                    if(response == 1){
                                        $("#mensaje").html('Información Modificada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                           window.location = 'GPQR_PQR.php?uni='+unidad+'&est='+estado+'&fecha='+fecha+'&afavor='+afavor+'&fac='+fac+'&tercero='+ter+'&obs='+Obs+'&mod='+mod+'&id='+idP;
                                        })
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Modificar Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })
                                    }
                                }
                            });
                    }    

                </script>
                
                <!--listado actividad contribuyente-->
                <div class="form-group form-inline" style="margin-top:-10px;">
                    <?php require_once './menu.php'; 
                        $sql = "SELECT  dtp.id_unico,
                                        ts.nombre,
                                        d.descripcion,
                                        dtp.ruta_archivo,
                                        dtp.fecha,
                                        c.nombre,
                                        dtp.observaciones
                                FROM gpqr_detalle_pqr dtp 
                                LEFT JOIN gpqr_descripcion d ON dtp.id_descripcion = d.id_unico
                                LEFT JOIN gp_tipo_servicio ts ON dtp.id_servicio = ts.id_unico
                                LEFT JOIN gpqr_clase c ON dtp.id_clase = c.id_unico
                                WHERE dtp.id_pqr = '$idPQR' ORDER BY dtp.fecha DESC";
                    
                        $resultado = $mysqli->query($sql);
                    ?>
                    <?php   if(!empty($mod)){
                                if($nresC < 1){
                    ?>
                                    <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: -10px;">
                    <?php       }else{ ?>
                                    <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 10px;">   
                    <?php   
                                }
                            }else{ ?>
                                <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 10px;">
                    <?php   } ?>
                        <div class="table-responsive contTabla" >
                            <table id="tabla" class=" col-sm-8 table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Servicio</strong></td>
                                        <td class="cabeza"><strong>Descripción</strong></td>
                                        <td class="cabeza"><strong>Fecha</strong></td>
                                        <td class="cabeza"><strong>Clase</strong></td>
                                        <td class="cabeza"><strong>Observaciones</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza">Servicio</th>
                                        <th class="cabeza">Descripción</th>
                                        <th class="cabeza">Fecha</th>
                                        <th class="cabeza">Clase</th>
                                        <th class="cabeza">Observaciones</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                        while ($row = mysqli_fetch_row($resultado)) { 
                                       
                                            $FI = $row[4];

                                            $FI = trim($FI, '"');
                                            $fecha_div = explode("-", $FI);
                                            $anioa = $fecha_div[0];
                                            $mesa = $fecha_div[1];
                                            $diaa = $fecha_div[2];
                                            $fecha = $diaa.'/'.$mesa.'/'.$anioa;
                                    ?>
                                            <tr>
                                                <td style="display: none;"></td>
                                                <td >
                                                    <?php   if($nresC > 0){ ?>
                                                                <a href="#">
                                                                    <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                                </a>
                                                                <a href="#" >
                                                                    <i title="Modificar" class="glyphicon glyphicon-edit"></i>
                                                                </a>
                                                    <?php   }else{ ?>
                                                                <a href="#" onclick="javascript:eliminarD(<?php echo $row[0];?>);">
                                                                    <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                                </a>
                                                                <a href="GPQR_DETALLE_PQR.php?idD=<?php echo $row[0] ?>&idP=<?php echo $idPQR ?>" >
                                                                    <i title="Modificar" class="glyphicon glyphicon-edit"></i>
                                                                </a>
                                                    <?php   }?>
                                                    <?php   if(empty($row[3]) || $row[3] == 'NULL' || $row[3] == ""){ ?>
                                                                <a href="#">
                                                                        <i title="Visualizar Documento" class="glyphicon glyphicon-send"></i>
                                                                </a>
                                                    <?php   }else{ ?>
                                                                <a href="#" onclick="javascript:window.open(<?php echo "'".$row[3]."'";?>);" >
                                                                        <i title="Visualizar Documento" class="glyphicon glyphicon-send"></i>
                                                                </a>
                                                    <?php   } ?>    
                                                      
                                                </td>
                                                <td class="campos" align="left" ><?php echo $row[1]?></td>
                                                <td class="campos" align="left" ><?php echo $row[2]?></td>
                                                <td class="campos" align="center"><?php echo $fecha?></td>
                                                <td class="campos" align="left"><?php echo $row[5]?></td>
                                                <td class="campos" align="left"><?php echo $row[6]?></td>                
                                            </tr>
                                    <?php 
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div> 
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
                                <p>¿Desea eliminar el registro seleccionado?</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                 <?php require_once ('footer.php'); ?>
            <!--Script que dan estilo al formulario-->

            <!-- <script type="text/javascript" src="js/menu.js"></script>
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">
            <script src="js/bootstrap.min.js"></script>
            <!--Scrip que envia los datos para la eliminación-->
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">
                <script src="js/bootstrap.min.js"></script>
            <script type="text/javascript">
                function eliminar(id)
                {
                    var result = '';
                    $("#myModal").modal('show');
                    $("#ver").click(function(){
                        $("#mymodal").modal('hide');
                        $.ajax({
                            type:"GET",
                            url:"json/eliminarAfiliacionJson.php?id="+id,
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
                
                
            </script>

            <script type="text/javascript">
                function modal()
                {
                    $("#myModal").modal('show');
                }
            </script>
       
            <script type="text/javascript">
                function recargar()
                {
                    window.location.reload();     
                }
            </script>     
       
            <!--Actualiza la página-->
            <script type="text/javascript">
    
                $('#ver1').click(function(){ 
                    reload();
                    //window.location= '../registrar_GN_ACCIDENTE.php?idE=<?php #echo md5($_POST['sltEmpleado'])?>';
                    //window.location='../listar_GN_ACCIDENTE.php';
                    window.history.go(-1);        
                });
    
            </script>

            <script type="text/javascript">    
                $('#ver2').click(function(){
                    window.history.go(-1);
                });    
            </script>
        </div>
        <script>
            $("#sltUV").change(function(){
                var idP = $("#txtIdPQR").val();
                var mod = $("#txtMOD").val();
                var unidad = $("#sltUV").val();
                var estado = $("#sltEstado").val();
                var fecha  = $("#sltFechaS").val();  
                var afavor = $("#sltAfavor").val();
                var fac    = $("#sltFac").val();
                var ter    = "";
                var Obs    = $("#txtObservaci").val();
                

                if(mod != ""){
                    var mod1   = 1;
                   
                    window.location = 'GPQR_PQR.php?uni='+unidad+'&est='+estado+'&fecha='+fecha+'&afavor='+afavor+'&fac='+fac+'&tercero='+ter+'&obs='+Obs+'&mod1='+mod1+'&id='+idP;

                    
                }else{
                    var lleno = $("#txtMOD1").val();
                    if(lleno != ""){
                        window.location = 'GPQR_PQR.php?uni='+unidad+'&est='+estado+'&fecha='+fecha+'&afavor='+afavor+'&fac='+fac+'&tercero='+ter+'&obs='+Obs+'&mod1='+lleno+'&id='+idP;
                    }else{
                        window.location = 'GPQR_PQR.php?uni='+unidad+'&est='+estado+'&fecha='+fecha+'&afavor='+afavor+'&fac='+fac+'&tercero='+ter+'&obs='+Obs+'&mod='+mod+'&id='+idP;
                    }
                    
                }
                console.log("obser: "+Obs);
                

            });
        </script>
        <script type="text/javascript">
            var unidad_V = $("#sltUV").val();
            if(unidad_V != ""){
                $("#sltFac").prop('required',true);
            }
        </script>
        <script>
            $("#sltTer").change(function(){
                var idP = $("#txtIdPQR").val();
                var mod = $("#txtMOD").val();
                var unidad = "";
                var estado = $("#sltEstado").val();
                var fecha  = $("#sltFechaS").val();  
                var afavor = $("#sltAfavor").val();
                var fac    = "";
                var ter    = $("#sltTer").val();
                var Obs    = $("#txtObservaci").val();
                if(mod != ""){
                    var mod1   = 1;
                   
                    window.location = 'GPQR_PQR.php?uni='+unidad+'&est='+estado+'&fecha='+fecha+'&afavor='+afavor+'&fac='+fac+'&tercero='+ter+'&obs='+Obs+'&mod1='+mod1+'&id='+idP;

                    
                }else{
                    var lleno = $("#txtMOD1").val();
                    if(lleno != ""){
                        window.location = 'GPQR_PQR.php?uni='+unidad+'&est='+estado+'&fecha='+fecha+'&afavor='+afavor+'&fac='+fac+'&tercero='+ter+'&obs='+Obs+'&mod1='+lleno+'&id='+idP;
                    }else{
                        window.location = 'GPQR_PQR.php?uni='+unidad+'&est='+estado+'&fecha='+fecha+'&afavor='+afavor+'&fac='+fac+'&tercero='+ter+'&obs='+Obs+'&mod='+mod+'&id='+idP;
                    }
                    
                }

            });
        </script>
        <script>
            function encuentraFac(){

                var unidad = $("#sltUV").val();
                 $.ajax({
                    type:"GET",
                    url:"traerNumeroFactura.php?id="+unidad,
                    success: function (data) {
                        result = JSON.parse(data);
                        $("#sltFac").val(result);
                    }
                });
            }
        </script>
        
        <script>
            function fechaInicial(){
                var fechain= document.getElementById('sltFechaA').value;
                var fechafi= document.getElementById('sltFechaR').value;
                var fi = document.getElementById("sltFechaR");
                fi.disabled=false;
       
                $( "#sltFechaR" ).datepicker( "destroy" );
                $( "#sltFechaR" ).datepicker({ changeMonth: true, minDate: fechain});
           
            }
        </script>

        <?php   if(!empty($mod)){?>
                    <script>
                        
                        $("#btnGuardar").prop('disabled',true);
                        $("#btnEditar").prop('disabled',false);
                        $("#btnDetalle").prop('disabled',false);
                        $("#btnNuevo").prop('disabled',false);
                    </script>
        <?php   } 

                if(!empty($mod1)){ ?>
                   
        <?php   } 
        
        if($nresD > 0){
?>
            <script>
                console.log("entro2");
                //$("#sltEstado").prop('disabled',true);
                $("#sltUV").prop('disabled',true);
                $("#sltTer").prop('disabled',true);
                $("#sltFechaS").prop('disabled',true);
                $("#sltFac").prop('disabled',true);

            </script>
<?php
        }
        if(!empty($mod)){
            if($nresC > 0){
?>
                <script>
                    $("#btnGuardar").prop('disabled',true);
                    $("#btnEditar").prop('disabled',true);
                    $("#btnDetalle").prop('disabled',true);
                    $("#btnNuevo").prop('disabled',false);
                    $("#sltEstado").prop('disabled',true);
                    $("#txtObservaci").prop('disabled',true);
                    $("#sltFac").prop('disabled',true);
                </script>
<?php

            } 
        }
        
?>      
        <script>
            $("#sltClase").change(function(){
                var idC = $("#sltClase").val();
                $.ajax({
                    type:"GET",
                    url:"traerIndicadorCierre.php?id="+idC,
                    success: function (data) {
                        result = JSON.parse(data);
                        if(result == 1){
                            $("#sltAfavor").prop('disabled', false);
                            $("#sltAfavor").prop('required', true);
                        }else{
                            $("#sltAfavor").prop('disabled', true);
                        }
                    }
                });
            });
        </script>
        <script> 
            //$("#sltActi").click(function(){
            var fechaIA  = document.getElementById('sltfecha').value;
            var fechaIP  = document.getElementById('fechaPQR').value;
            console.log("fecha pro: "+fechaIP);
            var fia = document.getElementById("sltfecha");
            fia.disabled=false;

            $("#sltfecha").datepicker("destroy");
            $("#sltfecha").datepicker({changeMonth: true, minDate: fechaIP});
            //});
        </script>
        <script>
            var md1 = $("#txtMOD1").val();
            if(md1 != ""){
                $("#btnGuardar").prop('disabled',true);
                    $("#btnEditar").prop('disabled',false);
                    $("#btnDetalle").prop('disabled',false);
                    $("#btnNuevo").prop('disabled',false);
            }
        </script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script>
           $("#sltUV").select2();
           $("#sltEstado").select2();
           $("#sltFac").select2();
           $("#sltTer").select2();
           $("#sltTipoS").select2();
           $("#sltDesc").select2();
           $("#sltClase").select2();
           $("#sltAfavor").select2();
           $("#sltDetA").select2();
        </script>
    </body>
</html>