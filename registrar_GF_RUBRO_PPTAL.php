<?php 
###########################################################################################################
#                           MODIFICACIONES
###########################################################################################################
#14/09/2017 | Erica G. | Crear Concepto Rubro                           
#06/07/2017 | ERICA G. | PARAMETRIZACION                            
#10/04/2017 | Erica G. | Diseño, tíldes, búsquedas 
###########################################################################################################
    #Creamos las sesion
    
    #Llamamos a la cabeza
    require_once ('head.php');
    #Llamamos la clase conexión
    require_once ('Conexion/conexion.php');    
?>
<link href="css/select/select2.min.css" rel="stylesheet">
<title>Registrar Rubro Presupuestal</title>
    </head>
    <body>    
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>   
                <div class="col-sm-7 text-left" style="margin-top: -22px;margin-left: 0px">
                    <h2 class="tituloform" align="center">Registrar Rubro Presupuestal</h2>
                    <a href="listar_GF_RUBRO_PPTAL.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Rubro Presupuestal</h5>
                    <div class="contenedorForma client-form" style="margin-top: -5px">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:guardar()" >  
                            <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>				
                            <div class="form-group" style="margin-top:-20px">
                                <label for="txtCodigoP" class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Código Presupuesto:
                                </label>
                                <input type="text" name="txtCodigoP" id="txtCodigoP" class="form-control" title="Ingrese código presupuestal" onkeypress="return txtValida(event,'num_car')"  placeholder="Código Presupuestal" style="height: 30px" onchange="return consultar();" required/>
                            </div>
                            <div class="form-group" style="margin-top:-20px">
                                <label for="txtNombre" class="control-label col-sm-5" >
                                    <strong class="obligado">*</strong>Nombre:                                   
                                </label>
                                <input type="text" name="txtNombre" onkeyup="javascript:this.value=this.value.toUpperCase();" id="txtNombre" class="form-control" title="Ingrese nombre" onkeypress="return txtValida(event,'num_car')" maxlength="500" placeholder="Nombre" required style="height: 30px"/>
                            </div>                            
                            <div class="form-group" style="margin-top:-20px">
                                <label for="sltTipoClase" class="control-label col-sm-5">
                                     <strong class="obligado">*</strong>Tipo Clase:
                                </label>
                                <?php 
                                #Consulta para cargar tipo Clase
                                $con = "SELECT id_unico,nombre FROM gf_tipo_clase_pptal ORDER BY nombre ASC";
                                #Ejecutamos la consulta cargandola en la conexión
                                $tipoC = $mysqli->query($con);
                                #Defimos la variable fila como array o vector númerico                                
                                ?>
                                <select name="sltTipoClase" id="sltTipoClase" class="select2_single form-control " title="Seleccione tipo clase" style="height: 30px; display: inline-block" required="">
                                    <option>Tipo Clase</option>
                                    <?php 
                                    while ($fila = mysqli_fetch_row($tipoC)) { ?>
                                    <option value="<?php echo $fila[0]; ?>"><?php echo ucwords(mb_strtolower($fila[1])); ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php 
                            $sql = "SELECT id_unico, nombre FROM gf_destino ORDER BY nombre ASC";
                            $destino = $mysqli->query($sql);
                            ?>
                            <div class="form-group" style="margin-top: -10px">
                                <label class="control-label col-sm-5">
                                     <strong class="obligado">*</strong>Destino:
                                </label>
                                <select name="sltDestino" class="select2_single form-control" id="sltDestino" title="Seleccione destino" style="height: 30px" required="">
                                    <option value="">Destino</option>
                                    <?php 
                                    while ($fila1 = mysqli_fetch_row($destino)) { ?>
                                        <option value="<?php echo $fila1[0];?>"><?php echo ucwords(mb_strtolower ($fila1[1])); ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-horizontal" style="margin-top:-10px">                                    
                                <label class="control-label col-sm-5" for="optMov">
                                     <strong class="obligado">*</strong>Movimiento:
                                </label>
                                <input type="radio" name="optMov" id="optMov1"  title="Indicar si hay movimiento" value="1" />SI
                                <input type="radio" name="optMov" id="optMov2"  title="Indicar no hay movimiento" value="2" />NO
                                <label for="optManP" class="control-label col-sm-offset-1">
                                     <strong class="obligado">*</strong>Maneja PAC:
                                </label>
                                <input type="radio" name="optManP" id="optManP1" title="Indicar si maneja PAC" value="1" />SI
                                <input type="radio" name="optManP" id="optManP2" title="Indicar no maneja PAC" value="2" />NO                                    
                            </div>
                            <div class="form-group" style="margin-top:-10px;">
                                <label for="txtVigencia" class="col-sm-5 control-label">
                                     <strong class="obligado">*</strong>Vigencia:
                                </label>
                                <?php 
                                $param=$_SESSION['anno'];
                                $compania = $_SESSION['compania'];
                                $sql = "SELECT id_unico,anno FROM gf_parametrizacion_anno WHERE id_unico=$param";
                                $rs = $mysqli->query($sql);
                                ?>
                                <select name="sltVigencia" id="sltVigencia" class="select2_single form-control" title="Seleccione predecesor" style="height: 30px">
                                    <option value="">Vigencia</option>
                                    <?php 
                                    $fila = mysqli_fetch_row($rs); ?>
                                    <option value="<?php echo $fila[1];?>"><?php echo ucwords( mb_strtolower($fila[1]));?></option>                                
                                    <?php 
                                    $sql10 = "SELECT id_unico,anno FROM gf_parametrizacion_anno WHERE id_unico!=$param AND compania = $compania";
                                    $result10=$mysqli->query($sql10);
                                    while ($row100=mysqli_fetch_row($result10)) {
                                        echo "<option value='$row100[0]'>$row100[1]</option>";
                                    }
                                    ?>                                                     
                                </select>                                
                            </div>
                            <div class="form-group" style="margin-top:-15px">
                                <label for="txtDinamica" class="col-sm-5 control-label">
                                     <strong class="obligado"></strong>Dinamica:
                                </label>
                                <textarea type="text" name="txtDinamica" id="txtDinamica" title="Ingrese dinamica" class="form-control" onkeypress="return txtValida(event,'num_car')" maxlength="5000" placeholder="Dinamica" style="height: 51px;resize: both;" ></textarea>
                            </div>
                            <div class="form-group texto" style="margin-top: -20px">
                                <label class="control-label col-sm-5">
                                    Predecesor:
                                </label>
                                <select name="sltPredecesor" id="sltPredecesor" class="form-control" title="Seleccione predecesor" style="height: 30px">
                                 </select>
                            </div>
                            <script type="text/javascript">
                                        $(document).ready(function(){
                                            var padre = 0;
                                            $("#sltPredecesor").change(function(){
                                                if(($("#sltPredecesor").val() == "") || ($("#sltPredecesor").val() == 0)){
                                                    padre = 0;
                                                }else{
                                                    padre = $("#sltPredecesor").val();                                                    
                                                }
                                                
                                                var form_data = {
                                                    is_ajax: 1,
                                                    id:+padre
                                                };
                                                
                                                $.ajax({
                                                    type:"POST",
                                                    url: "consultarPredecesor.php",
                                                    data: form_data,
                                                    success: function (data) {
                                                        console.log(data);
                                                        $("#sltSector").css('display', 'none');
                                                        $("#sltTipoVigencia").css('display', 'none');
                                                        $("#sltDestino").css('display', 'none');
                                                        $("#sltTipoClase").css('display', 'none');
                                                        $("#sltVigencia").css('display', 'none');
                                                        $("#sltVigencia").html(data).fadeIn();
                                                        
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            var padre = 0;
                                            $("#sltPredecesor").change(function(){
                                                if (($("#sltPredecesor").val() == "" || ($("#sltPredecesor").val() == 0))) {
                                                    padre = 0;
                                                }else{
                                                    padre = $("#sltPredecesor").val();
                                                }
                                                
                                                var form_data = {
                                                    is_ajax:1,
                                                    id:+padre
                                                };
                                                
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultarDinamica.php",
                                                    data:form_data,
                                                    success: function (data) {
                                                        
                                                        $("#sltSector").css('display', 'none');
                                                        $("#sltTipoVigencia").css('display', 'none');
                                                        $("#sltDestino").css('display', 'none');
                                                        $("#sltTipoClase").css('display', 'none');
                                                        $("#sltVigencia").css('display', 'none');
                                                        $('textarea[name=txtDinamica]').val(data);
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                    
                                    <script type="text/javascript">
                                        $(document).ready(function (){
                                            var padre = 0;
                                            $("#sltPredecesor").change(function (){
                                                if (($("#sltPredecesor").val() == "" || ($("#sltPredecesor").val() === 0))) {
                                                    padre = 0;
                                                }else{
                                                    padre = $("#sltPredecesor").val();                                                    
                                                }
                                                
                                                var form_data = {
                                                    is_ajax:1,
                                                    id:+padre
                                                };
                                                
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultarTipoClase.php",
                                                    data:form_data,
                                                    success: function (data) {
                                                        
                                                        $("#sltSector").css('display', 'none');
                                                        $("#sltTipoVigencia").css('display', 'none');
                                                        $("#sltDestino").css('display', 'none');
                                                        $("#sltTipoClase").css('display', 'none');
                                                        $("#sltVigencia").css('display', 'none');
                                                        $("#sltTipoClase").html(data).fadeIn(); 
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                    <script>
                                        $(document).ready(function(){
                                            var padre = 0;
                                            $("#sltPredecesor").change(function(){
                                                if (($("#sltPredecesor").val() == "" || ($("#sltPredecesor").val() === 0))) {
                                                    padre = 0;
                                                }else{
                                                    padre = $("#sltPredecesor").val();
                                                }
                                                
                                                var form_data = {
                                                    is_ajax:1,
                                                    id:+padre
                                                };
                                                
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultarDestino.php",
                                                    data:form_data,
                                                    success: function (data) {
                                                        
                                                        $("#sltSector").css('display', 'none');
                                                        $("#sltTipoVigencia").css('display', 'none');
                                                        $("#sltDestino").css('display', 'none');
                                                        $("#sltTipoClase").css('display', 'none');
                                                        $("#sltVigencia").css('display', 'none');
                                                        $("#sltDestino").html(data).fadeIn();
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            var padre = 0;
                                            $("#sltPredecesor").change(function(){
                                                if (($("#sltPredecesor").val() == "" || ($("#sltPredecesor").val() === 0))) {
                                                    padre = 0;
                                                }else{
                                                    padre = $("#sltPredecesor").val();
                                                }
                                                
                                                var form_data = {
                                                    is_ajax:1,
                                                    id:+padre
                                                };
                                                
                                                $.ajax({
                                                    type:"POST", 
                                                    url:"consultarTipoV.php",
                                                    data:form_data,
                                                    success: function (data) {
                                                        
                                                        $("#sltSector").css('display', 'none');
                                                        $("#sltTipoVigencia").css('display', 'none');
                                                        $("#sltDestino").css('display', 'none');
                                                        $("#sltTipoClase").css('display', 'none');
                                                        $("#sltVigencia").css('display', 'none');
                                                        $("#sltTipoVigencia").html(data).fadeIn();
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                    <script type="text/javascript">
                                        $(document).ready(function(){                                            
                                            var padre = 0;
                                            $("#sltPredecesor").change(function(){
                                                if (($("#sltPredecesor").val() == "" || ($("#sltPredecesor").val() === 0))) {
                                                    padre = 0;
                                                }else{
                                                    padre = $("#sltPredecesor").val();
                                                }
                                                
                                                var form_data = {
                                                    is_ajax:1,
                                                    id:+padre
                                                };
                                                
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultarSector.php",
                                                    data:form_data,
                                                    success: function (data) {
                                                        
                                                        $("#sltVigencia").css('display', 'none');
                                                        $("#sltSector").css('display', 'none');
                                                        $("#sltTipoVigencia").css('display', 'none');
                                                        $("#sltDestino").css('display', 'none');
                                                        $("#sltTipoClase").css('display', 'none');
                                                        $("#sltSector").html(data).fadeIn();
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                    <script type="text/javascript">
                                        $(document).ready(function (){
                                            //No
                                            $("#optMov1").prop('checked',false);
                                            $("#optMov2").prop('checked',true);
                                            var padre = 0;
                                            $("#sltPredecesor").change(function(){
                                              if (($("#sltPredecesor").val() == "" || ($("#sltPredecesor").val() === 0))) {
                                                    padre = 0;
                                                    //No
                                                    $("#sltSector").css('display', 'none');
                                                        $("#sltTipoVigencia").css('display', 'none');
                                                        $("#sltDestino").css('display', 'none');
                                                        $("#sltTipoClase").css('display', 'none');
                                                        $("#sltVigencia").css('display', 'none');
                                                    $("#optMov1").prop('checked',false);
                                                    $("#optMov2").prop('checked',true);
                                                }else{
                                                    $("#sltSector").css('display', 'none');
                                                        $("#sltTipoVigencia").css('display', 'none');
                                                        $("#sltDestino").css('display', 'none');
                                                        $("#sltTipoClase").css('display', 'none');
                                                        $("#sltVigencia").css('display', 'none');
                                                    padre = $("#sltPredecesor").val();
                                                } 
                                                
                                                var form_data = {
                                                    is_ajax:1,
                                                    id:+padre
                                                };
                                                
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultarCodigoP.php",
                                                    data:form_data,
                                                    success: function (data) {
                                                        //Si
                                                       if (data == 1) {
                                                        $("#sltSector").css('display', 'none');
                                                        $("#sltTipoVigencia").css('display', 'none');
                                                        $("#sltDestino").css('display', 'none');
                                                        $("#sltTipoClase").css('display', 'none');
                                                        $("#sltVigencia").css('display', 'none');
                                                        $("#optMov1").prop('checked',true);
                                                        $("#optMov2").prop('checked',false);
                                                       $("#noMov").modal('show');
                                                           inH();
                                                           zonaC();
                                                           
                                                       }else if (data == 2){
                                                           //No
                                                        $("#sltSector").css('display', 'none');
                                                        $("#sltTipoVigencia").css('display', 'none');
                                                        $("#sltDestino").css('display', 'none');
                                                        $("#sltTipoClase").css('display', 'none');
                                                        $("#sltVigencia").css('display', 'none');
                                                        $("#optMov1").prop('checked',false);
                                                        $("#optMov2").prop('checked',true);
                                                       }
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                    <script type="text/javascript">
                                        $(document).ready(function (){
                                            $("#optManP1").prop('checked',false);
                                            $("#optManP2").prop('checked',true);
                                            $("#sltSector").css('display', 'none');
                                                        $("#sltTipoVigencia").css('display', 'none');
                                                        $("#sltDestino").css('display', 'none');
                                                        $("#sltTipoClase").css('display', 'none');
                                                        $("#sltVigencia").css('display', 'none');
                                            var padre = 0;
                                            $("#sltPredecesor").change(function(){
                                              if (($("#sltPredecesor").val() == "" || ($("#sltPredecesor").val() === 0))) {
                                                    padre = 0;
                                                    $("#sltSector").css('display', 'none');
                                                    $("#sltTipoVigencia").css('display', 'none');
                                                    $("#sltDestino").css('display', 'none');
                                                    $("#sltTipoClase").css('display', 'none');
                                                    $("#sltVigencia").css('display', 'none');
                                                    $("#optManP1").prop('checked',false);
                                                    $("#optManP2").prop('checked',true);
                                                }else{
                                                    padre = $("#sltPredecesor").val();
                                                    $("#sltSector").css('display', 'none');
                                                    $("#sltTipoVigencia").css('display', 'none');
                                                    $("#sltDestino").css('display', 'none');
                                                    $("#sltTipoClase").css('display', 'none');
                                                    $("#sltVigencia").css('display', 'none');
                                                } 
                                                
                                                var form_data = {
                                                    is_ajax:1,
                                                    id:+padre
                                                };
                                                
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultarManPac.php",
                                                    data:form_data,
                                                    success: function (data) {
                                                       if (data == 1) {
                                                            $("#optManP1").prop('checked',true);
                                                            $("#optManP2").prop('checked',false);
                                                            $("#sltSector").css('display', 'none');
                                                            $("#sltSector").css('display', 'none');
                                                            $("#sltTipoVigencia").css('display', 'none');
                                                            $("#sltDestino").css('display', 'none');
                                                            $("#sltTipoClase").css('display', 'none');
                                                            $("#sltVigencia").css('display', 'none');
                                                       }else if (data == 2){
                                                            $("#optManP1").prop('checked',false);
                                                            $("#optManP2").prop('checked',true);   
                                                            $("#sltSector").css('display', 'none');
                                                            $("#sltSector").css('display', 'none');
                                                            $("#sltTipoVigencia").css('display', 'none');
                                                            $("#sltDestino").css('display', 'none');
                                                            $("#sltTipoClase").css('display', 'none');
                                                            $("#sltVigencia").css('display', 'none');
                                                       }
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                    
                            
                            <?php 
                            $sql = "SELECT id_unico,nombre FROM gf_tipo_vigencia ORDER BY nombre ASC"; 
                            $tipoV = $mysqli->query($sql);      
                            ?>
                            <div class="form-group" style="margin-top:-10px">
                                <label class="control-label col-sm-5">
                                     <strong class="obligado">*</strong>Tipo Vigencia:
                                </label>
                                <select name="sltTipoVigencia" id="sltTipoVigencia" class="select2_single form-control" title="Seleccione tipo vigencia" style="height: 30px" required>
                                    <option>Tipo Vigencia</option>
                                    <?php while ($fila2 = mysqli_fetch_array($tipoV)) { ?>
                                    <option value="<?php echo $fila2[0]; ?>"><?php echo ucwords( mb_strtolower($fila2[1])) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php 
                            $sql = "SELECT id_unico, nombre FROM gf_sector ORDER BY nombre ASC";
                            $sect = $mysqli->query($sql);
                            ?>
                            <div class="form-group" style="margin-top: -10px" style="height: 30px">
                                <label class="control-label col-sm-5">
                                    Sector:
                                </label>
                                <select class="select2_single form-control" name="sltSector" id="sltSector" name="Seleccione sector">
                                    <option value="">Sector</option>
                                    <?php while ($fila3 = mysqli_fetch_row($sect)) { ?>
                                    <option value="<?php echo $fila3[0]; ?>"><?php echo ucwords( mb_strtolower($fila3[1])); ?></option>
                                    <?php   } ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -10px" style="height: 30px">
                                <label class="control-label col-sm-5">
                                    Equivalente:
                                </label>
                                <input class="form-control" placeholder="equivalente" type="text" name="equivalente" id="equivalente" title="Ingrese el código equivalente" onkeypress="return txtValida(event, 'num')">
                            </div>
                            <div align="center">
                                <button type="submit"  class="btn btn-primary sombra" style="margin-top: -18px; margin-bottom: 10px; margin-left: -50px;" >Guardar</button>
                            </div>
                            
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>                    
                </div>
               
                <div class="col-sm-7 col-sm-3">
                        <table class="tablaC table-condensed" style="margin-left: -10px;margin-top: -22px">
                            <thead>
                                <tr>
                                    <tr>
                                        <th>
                                            <h2 class="titulo" align="center">Consultas</h2>
                                        </th>
                                        <th>
                                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                        </th>
                                    </tr>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="btnConsultas" style="margin-bottom: 1px;">
                                            <a href="#">
                                                MOVIMIENTO PRESUPUESTAL
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GF_TIPO_CLASE_PPTAL.php">TIPO CLASE<br/>PRESUPUESTAL</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btnConsultas" style="margin-bottom: 1px;">
                                            <a href="#">
                                                RESUMEN DE MOVIMIENTO
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <!-- onclick="return ventanaSecundaria('registrar_GF_DESTINO.php')" -->
                                        <a class="btn btn-primary btnInfo" href="registrar_GF_DESTINO.php">DESTINO</a>                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btnConsultas" style="margin-bottom: 1px;">
                                            <a href="#">
                                                GRAFICOS DE<br/> SALDOS
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GF_SECTOR.php">SECTORES</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btnConsultas" style="margin-bottom: 1px;">
                                            <a href="#">
                                               MOVIMIENTO DE PAC ENTRE MESES
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btnConsultas" style="margin-bottom: 1px;">
                                            <a href="#" style="margin-bottom: 6px">
                                                RESUMEN DE<br/>FUENTES
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            </div>
            
        </div>
        <?php require_once('footer.php'); ?>        
        <div class="modal fade" id="myModal2" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Ingrese un código presupuestal.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal1" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Este código presupuestal ya existe.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $("#ver2").click(function(){
                $("#txtCodigoP").val("");
                var option = "<option value=''>Predecesor</option>";
                $("#sltPredecesor").html(option);
            })
        </script>
        <div class="modal fade" id="noMov" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Movimiento esta activo. Código no puede ser utilizado</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="submit" id="btnNM" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" autofocus="">Volver</button>
                    </div>
                </div>
            </div>
        </div>
        
        
        <script type="text/javascript">
            function consultar(){
                var codi = document.form.txtCodigoP.value;                
                if (codi == null || codi == '') {
                    $("#myModal2").modal('show');                    
                }else{
                    $.ajax({
                        data: {code:codi, action:1},
                        type: "POST",
                        url: "jsonPptal/rubropptalJson.php",
                        success: function(data){    
                            
                            if(data ==1){
                                 $("#myModal1").modal('show');   
                            } else {
                                console.log(data);
                                $("#sltPredecesor").html(data).focus();
                                $("#sltPredecesor").select2({
                                    allowClear:true
                                });
                                if(($("#sltPredecesor").val() == "")){
                                    
                                }else{
                                   var pred = $("#sltPredecesor").val();  
                                    var form_data = {
                                        action: 1,
                                        id:pred
                                    };
                                    $.ajax({
                                        type:"POST",
                                        url: "consultarPredecesor.php",
                                        data: form_data,
                                        success: function (data) {
                                            console.log(data);
                                            $("#sltVigencia").html(data);
                                            $("#sltVigencia").css('display', 'none');

                                        }
                                    });
                                }
                            }
                        }
                    });
                }
            }                        
        </script>
        <script type="text/javascript">
            $("#ver1").click(function(){
                window.location.reload();
            });
        </script>
        <script type="text/javascript">
            $("#btnNM").click(function(){
                window.location = "registrar_GF_CUENTA_P.php";
            });
        </script>     
        <script type="text/javascript">
            function zonaC (){
                $(document).click(function(){
                    window.location.reload();
                });
            }
        </script>
        <script type="text/javascript">
            function inH(){
                $("#txtNombre").prop('disabled',true);
                $("#txtCodigoP").prop('disabled',true);
                $("#sltTipoClase").prop('disabled',true);
                $("#sltDestino").prop('disabled',true);
                $("input[type=radio]").attr('disabled', true);
                $("#sltVigencia").prop('disabled',true);
                $("#txtDinamica").prop('disabled',true);
                $("#sltPredecesor").prop('disabled',true);
                $("#sltTipoVigencia").prop('disabled',true);
                $("#sltSector").prop('disabled',true);  
                $("#sltVigencia").css('display', 'none');
                $("#sltSector").css('display', 'none');
                $("#sltTipoVigencia").css('display', 'none');
                $("#sltDestino").css('display', 'none');
                $("#sltTipoClase").css('display', 'none');
            }
        </script>
<script src="js/select/select2.full.js"></script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
  </script>
  <script>
      function guardar(){
          $("#lblmsj").html("¿Desea Crear Concepto Igual?");
          $("#mdlMensajes").modal("show");
      }
  </script>
    <div class="modal fade" id="mdlMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label style="font-weight: normal" id="lblmsj" name="lblmsj" ></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="submit" id="btnMsjAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" autofocus="">Si</button>
                    <button type="submit" id="btnMsjCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" autofocus="">No</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $("#btnMsjAceptar").click(function(){
            var formData = new FormData($("#form")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_rubro_pptalJson.php?action=1",
                data:formData,
                contentType: false,
                processData: false,
                success: function (response) { 
                    resultado = JSON.parse(response);
                    var data = resultado["respuesta"];
                    var id   = resultado["id"];
                    if(data==1){
                        var form_data ={id:id, action:2}
                        $.ajax({
                        type: 'POST',
                        url: "jsonPptal/gf_rubro_pptalJson.php",
                        data:form_data,
                        success: function (response) { 
                            resultado = JSON.parse(response);
                            var data = resultado["respuesta"];
                            var id   = resultado["id"];
                            if(data ==1){
                                $("#lblmsj1").html("Información Guardada Correctamente");
                                $("#mdlMensajes1").modal("show");
                                $("#btnMsjAceptar1").click(function(){
                                    window.location='modificar_GF_RUBRO_PPTAL.php?id='+id;
                                });
                            }else{
                                if(data==2){
                                    $("#lblmsj1").html("No Se Ha Podido Guardar Concepto");
                                    $("#mdlMensajes1").modal("show");
                                    $("#btnMsjAceptar1").click(function(){
                                        window.location='modificar_GF_RUBRO_PPTAL.php?id='+id;
                                    }); 
                                } else {
                                    if(data==3){
                                        $("#lblmsj1").html("No Se Ha Podido Guardar Concepto Rubro");
                                        $("#mdlMensajes1").modal("show");
                                        $("#btnMsjAceptar1").click(function(){
                                            window.location='modificar_GF_RUBRO_PPTAL.php?id='+id;
                                        });   
                                    } else {
                                        $("#lblmsj1").html("Error");
                                        $("#mdlMensajes1").modal("show");
                                        $("#btnMsjAceptar1").click(function(){
                                            window.location='modificar_GF_RUBRO_PPTAL.php?id='+id;
                                        });   
                                    }
                                } 
                            }
                        }
                    })  
                    }else{
                        if(data==2){
                            $("#lblmsj1").html("La Información No Se Ha Podido Guardar");
                            $("#mdlMensajes1").modal("show");
                            $("#btnMsjAceptar1").click(function(){
                                $("#mdlMensajes1").modal("hide");
                            }); 
                        } else {
                            if(data==3){
                                $("#lblmsj1").html("El Código Ya Está Siendo Utilizado");
                                $("#mdlMensajes1").modal("show");
                                $("#btnMsjAceptar1").click(function(){
                                    $("#mdlMensajes1").modal("hide");
                                }); 
                            } else {
                                $("#lblmsj1").html("Error");
                                $("#mdlMensajes1").modal("show");
                                $("#btnMsjAceptar1").click(function(){
                                    $("#mdlMensajes1").modal("hide");
                                });
                            }
                        }
                    }
                }
            });
        })
        $("#btnMsjCancelar").click(function(){
             var formData = new FormData($("#form")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_rubro_pptalJson.php?action=1",
                data:formData,
                contentType: false,
                processData: false,
                success: function (response) { 
                    resultado = JSON.parse(response);
                    var data = resultado["respuesta"];
                    if(data==1){
                        $("#lblmsj1").html("Información Guardada Correctamente");
                        $("#mdlMensajes1").modal("show");
                        $("#btnMsjAceptar1").click(function(){
                            window.location='listar_GF_RUBRO_PPTAL.php';
                        });
                    }else{
                        if(data==2){
                            $("#lblmsj1").html("La Información No Se Ha Podido Guardar");
                            $("#mdlMensajes1").modal("show");
                            $("#btnMsjAceptar1").click(function(){
                                $("#mdlMensajes1").modal("hide");
                            }); 
                        } else {
                            if(data==3){
                                $("#lblmsj1").html("El Código Ya Está Siendo Utilizado");
                                $("#mdlMensajes1").modal("show");
                                $("#btnMsjAceptar1").click(function(){
                                    $("#mdlMensajes1").modal("hide");
                                }); 
                            } else {
                                $("#lblmsj1").html("Error");
                                $("#mdlMensajes1").modal("show");
                                $("#btnMsjAceptar1").click(function(){
                                    $("#mdlMensajes1").modal("hide");
                                });
                            }
                        }
                    }
                }
            });
        })
    </script>
    <div class="modal fade" id="mdlMensajes1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label style="font-weight: normal" id="lblmsj1" name="lblmsj" ></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="submit" id="btnMsjAceptar1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" autofocus="">Aceptar</button>
                    <button type="submit" id="btnMsjCancelar1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" autofocus="">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    
    </body>
    
</html>
