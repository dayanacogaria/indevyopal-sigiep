    <?php

        require_once('Conexion/conexion.php');
        require_once 'head.php'; 
    ?>
        <title>Registrar Concepto Comercial</title>
    </head>
    <body>

        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>

        <style>
            label #cact-error,#descripcion-error,#ai-error,#af-error,#tipo-error,#cactf-error, #TO-error, #ClaseC-error{
                display: block;
                color: #155180;
                font-weight: normal;
                font-style: italic;
            }
            body{
                font-size: 12px;
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
                        sltmes: {
                            required: true
                        },
                        sltcni: {
                            required: true
                        },
                        sltAnnio: {
                            required: true
                        }
                    }   
                });

                $(".cancel").click(function() {
                    validator.resetForm();
                });
            });
        </script>

        <style>
            .form-control {font-size: 12px;}

        </style>

        <!-- contenedor principal -->
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once ('menu.php'); ?>
                <div class="col-sm-7 text-left" style="margin-top:-10px">
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-bottom: 10px;"> Registrar Concepto Comercial</h2>
                    <!--Volver-->
                    <a href="listar_GC_CONCEPTO_COMERCIAL.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:95%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-10px;  background-color: #0e315a; color: white; border-radius: 5px;color:#0e315a;">.</h5> 
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/registrarConceptoComercialJson.php" >
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                           
                            <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                            
                            <div class="form-group" style="margin-top: -10px;">
                                
                                <label for="cact" class="control-label col-sm-5 col-md-5 col-lg-5" ><strong class="obligado">*</strong>Código:</label>
                                <input  type="text" name="codigo" required id="cact" class="form-control" maxlength="15" title="Ingrese Código" onkeypress="return txtValida(event,'num_car')" placeholder="Código">
                            </div>

                            <div class="form-group" style="margin-top: -15px;">    
                                
                                <label for="descripcion" class="col-sm-5 control-label"><strong class="obligado">*</strong>Descripción:</label>    
                                <textarea name="descripcion" required placeholder="Descripción" id="descripcion" required="" class="form-control col-sm-1" rows="3"  title="Ingrese Descripción" maxlength="100"></textarea>
                            </div>    
                                  
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="cactf" class="control-label col-sm-5">Formula:</label>
                                <input  type="text" name="formula"  id="cactf" class="form-control" maxlength="8000" title="Ingrese Formula"  placeholder="Formula">
                            </div>

                            <?php
                                $cuentaI = "SELECT * FROM gc_tipo_comercio ORDER BY nombre ASC";
                                $rsctai = $mysqli->query($cuentaI);
                            ?>
                            
                            <div class="form-group" style="margin-top: -1px;">    
                                <label for="tipo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo:</label>
                                <select required name="tipo" id="tipo"   class="select2_single form-control" title="Seleccione Tipo">
                                    <option value="">Tipo</option>
                                    <?php   while($row=mysqli_fetch_array($rsctai)){ ?>
                                                <option value="<?php echo $row['id_unico']?>"><?php echo ucwords(mb_strtolower($row['nombre'])); ?></option>
                                    <?php   } ?>
                                </select>
                            </div>

                            <?php /*
                                $cuentaI = "SELECT * FROM gf_concepto ORDER BY nombre ASC";
                                $rsctai = $mysqli->query($cuentaI);
                                */
                            ?>
                           <!-- <div class="form-group" style="margin-top: -1px;">
                                <label for="af" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Concepto:</label>
                                <select name="concepto" id="af" required  class="select2_single form-control" title="Seleccione Concepto">
                                    <option value="">Concepto</option>
                                    <?php /*  while($row=mysqli_fetch_array($rsctai)){ ?>
                                                <option value="<?php #echo $row['id_unico']?>"><?php #echo $row['nombre'] ?></option>
                                    <?php   }*/ ?>
                                </select>
                            </div>-->
                        
                            <?php
                                $TipoO = "SELECT id_unico, nombre FROM gf_tipo_operacion";
                                $TipOp= $mysqli->query($TipoO); 
                            ?>
                            
                            <div class="form-group" style="margin-top: -1px;" >
                                <label for="TO" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Operación:</label>
                                <select name="TO" id="TO" required  class="select2_single form-control" title="Seleccione Tipo de Operación">
                                    <option value="">Tipo Operación</option>
                                    <?php   while($TIO=mysqli_fetch_array($TipOp)){ ?>
                                                <option value="<?php echo $TIO[0]?>"><?php echo $TIO[1] ?></option>
                                    <?php   } ?>
                                </select>
                            </div> 
                           
                            <?php 
                                $claseC = "SELECT id_unico, nombre FROM gc_clase_concepto";
                                $ClaC   = $mysqli->query($claseC);
                                
                            ?>
                            
                            <div class="form-group" style="margin-top: -1px;" >
                                <label for="ClaseC" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Operación:</label>
                                <select name="ClaseC" id="ClaseC" required  class="select2_single form-control" title="Seleccione Clase Concepto">
                                    <option value="">Clase Concepto</option>
                                    <?php   while($CC = mysqli_fetch_row($ClaC)){ ?>
                                                <option value="<?php echo $CC[0]?>"><?php echo $CC[1] ?></option>
                                    <?php   } ?>
                                </select>
                            </div> 
                           

                            <div class="form-group" style="margin-top: -10px;">
                                <label for="txtDesc" class="control-label col-sm-5 col-md-5 col-lg-5" >Aplica Descuento? </label>
                               
                                <input  type="radio" name="txtDesc"  id="txtDesc"  value="1" >SI
                                <input  type="radio" name="txtDesc"  id="txtDesc"  value="2" checked >NO
                                
                            </div>

                            <div class="form-group" style="margin-top: -10px;">
                        
                                <label for="txtInt" class="control-label col-sm-5 col-md-5 col-lg-5" >Aplica Interés? </label>
                               
                                <input  type="radio" name="txtInt"  id="txtInt"  value="1">SI
                                <input  type="radio" name="txtInt"  id="txtInt"  value="2" checked>NO
                                
                            </div>  

                            <div class="form-group" style="margin-top: -10px;">
                        
                                <label for="txtAnt" class="control-label col-sm-5 col-md-5 col-lg-5" >Tipo Anticipo? </label>
                            
                                <input  type="radio" name="txtAnt"  id="txtAnt"  value="1">SI
                                <input  type="radio" name="txtAnt"  id="txtAnt"  value="2" checked>NO
                                
                            </div>  
                              
                            <div class="form-group" style="margin-top: 20px;">
                                <label for="no" class="col-sm-10 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;"><li class="glyphicon glyphicon-floppy-disk" ></button>
                            </div>
                        
                        </form>
                    </div>
                </div>

                <!-- Fin del Contenedor principal -->
                <!--Información adicional -->
                <div class="col-sm-3 col-sm-3" style="margin-top:-12px">
                    <table class="tablaC table-condensed" >
                        <thead>
                            <tr>
                                <th><h2 class="titulo" align="center" style=" font-size:17px;">Adicional</h2></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <a href="registrar_GC_TIPO_COMERCIO.php" class="btn btn-primary btnInfo">Tipo</a><br>
                                    <a href="registrar_GF_CONCEPTO.php" class="btn btn-primary btnInfo">Concepto</a>
                                </td>
                            </tr>
                            <tr>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>                
                </div>
            </div>
            <script src="js/select/select2.full.js"></script>
            <script>
                $(document).ready(function() {
                    $(".select2_single").select2({
                        allowClear: true
                    });
                });
            </script>
            <!-- Llamado al pie de pagina -->
            <script>
                function reporteExcel(){
                    $('form').attr('action', 'informes/generar_INF_LIS_FAC_EXCEL.php');
                }

                function reportePdf(){
                    $('form').attr('action', 'informes/generar_INF_LIS_FAC.php');
                }
            </script>
        </div>
        <?php require_once 'footer.php' ?>
    </body>
</html>