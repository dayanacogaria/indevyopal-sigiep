<?php

    require_once('Conexion/conexion.php');
    require_once 'head.php'; 

    $id=$_GET['id'];

    $sql="SELECT    cc.id_unico,
                    cc.codigo,  
                    cc.descripcion,
                    tc.nombre,
                    cc.formula,
                    con.nombre,
                    cc.tipo,
                    cc.concepto_rel,
                    cc.tipo_ope,
                    cc.apli_descu,
                    cc.apli_inte,
                    cc.anticipo,
                    cla.id_unico    

            FROM gc_concepto_comercial cc
            LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
            LEFT JOIN gf_concepto con ON con.id_unico=cc.concepto_rel
            LEFT JOIN gc_clase_concepto cla ON cc.clase = cla.id_unico
            WHERE md5(cc.id_unico)='$id'";

    $resultado=$mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);
?>
        <title>Modificar Concepto Comercial</title>
    </head>
    <body>
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>

        <style>
            label #cact-error,#descripcion-error,#ai-error,#af-error,#tipo-error,#cactf-error{
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
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-bottom: 10px;"> Modificar Concepto Comercial</h2>
                <a href="listar_GC_CONCEPTO_COMERCIAL.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:95%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-10px;  background-color: #0e315a; color: white; border-radius: 5px;"><?php echo "Concepto Comercial: ".$row[1]." - ".$row[2]; ?></h5> 
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/modificarConceptoComercialJson.php" >
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        
                        <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                        <div class="form-group" style="margin-top: -10px;">
                    
                            <label for="cact" class="control-label col-sm-4 col-md-4 col-lg-4" >
                                    <strong class="obligado">*</strong>Código: 
                            </label>
                            <input  type="text" name="codigo" required id="cact" class="form-control" maxlength="15" title="Ingrese Código" onkeypress="return txtValida(event,'num_car')" placeholder="Código" value="<?php echo $row[1] ?>">
                        </div>
                        
                        <div class="form-group" style="margin-top: -20px;">

                                <label for="descripcion" class="col-sm-4 col-md-4 col-lgl-5 control-label"><strong class="obligado">*</strong>Descripción:</label>    
                                <textarea name="descripcion" required placeholder="Descripción" id="descripcion" required="" class="form-control col-sm-1" rows="3" title="Ingrese Descripción" maxlength="100"><?php echo $row[2] ?></textarea>                                
                        </div>   
                        
                        <div class="form-group" style="margin-top: -10px;">

                            <label for="cactf" class="control-label col-sm-4 col-md-4 col-lg-4">Formula:</label>
                            <input  type="text" name="formula" id="cactf" class="form-control" maxlength="8000" title="Ingrese Formula" onkeypress="return txtValida(event,'num_car')" placeholder="Formula" value="<?php echo $row[4] ?>">
                        </div>


                        <?php
                            $idTipo=$row[6];
                            $st = "SELECT * FROM gc_tipo_comercio WHERE id_unico=$idTipo";
                            $resst = $mysqli->query($st);
                            $rowsst=mysqli_fetch_array($resst);

                            $cuentaI = "SELECT * FROM gc_tipo_comercio WHERE id_unico!=$idTipo ORDER BY nombre ASC";
                            $rsctai = $mysqli->query($cuentaI);
                        ?>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="tipo" class="col-sm-4 col-md-4 col-lg-4 control-label"><strong style="color:#03C1FB;">*</strong>Tipo:</label>
                            <select required name="tipo" id="tipo"  class="select2_single form-control" title="Seleccione Tipo">
                                <option value="<?php echo $rowsst['id_unico']?>"><?php echo ucwords(mb_strtolower($rowsst['nombre'])); ?></option>
                                <?php   while($rowt=mysqli_fetch_array($rsctai)){ ?>
                                            <option value="<?php echo $rowt['id_unico']?>"><?php echo ucwords(mb_strtolower($rowt['nombre'])); ?></option>
                                <?php   } ?>
                            </select>
                        </div>          

                        <?php
                            /*
                            $idConcepto=$row[7];
                            if(empty($idConcepto)){
                                   $sc = "SELECT * FROM gf_concepto c";          
                            }else{
                                $sc = "SELECT * FROM gf_concepto c WHERE id_unico=$idConcepto";
                            }
                            
                            $r = $mysqli->query($sc);
                            $rowConc=mysqli_fetch_array($r);

                            $cuentaI = "SELECT * FROM gf_concepto c WHERE id_unico!=$idConcepto ORDER BY nombre ASC";
                            $rsctai = $mysqli->query($cuentaI);
                            */
                        ?>
                        <!--<div class="form-group" style="margin-top: -5px;">
                            <label for="af" class="col-sm-4 col-md-4 col-lg-4 control-label"><strong style="color:#03C1FB;">*</strong>Concepto:</label>
                            <select name="concepto" id="af" required  class="select2_single form-control" title="Seleccione Concepto">
                                <option value="<?php# echo $rowConc['id_unico']?>"><?php #echo $rowConc['nombre'] ?></option>
                                <?php /*  while($row=mysqli_fetch_array($rsctai)){ ?>
                                            <option value="<?php echo $row['id_unico']?>"><?php echo $row['nombre'] ?></option>
                                <?php   }*/ ?>
                            </select>
                        </div>-->
                        <?php
                            $TipoP = $row[8];
                            if(!empty($TipoP)){
                                $TIOP = "SELECT id_unico, nombre FROM gf_tipo_operacion WHERE  id_unico != '$TipoP'";
                                $TOP = $mysqli->query($TIOP);
                            }else{
                                $TIOP = "SELECT id_unico, nombre FROM gf_tipo_operacion ";
                                $TOP = $mysqli->query($TIOP);
                            }
                            
                            $T2 = "SELECT id_unico, nombre FROM gf_tipo_operacion WHERE id_unico = '$TipoP' ";
                            $T3 = $mysqli->query($T2);
                            $T4 = mysqli_fetch_row($T3);
                        ?>
                        <div class="form-group" style="margin-top: -1px;" >
                            <label for="TO" class="col-sm-4 col-md-4 col-lg-4 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Operación:</label>
                            <select name="TO" id="TO" required  class="select2_single form-control" title="Seleccione Tipo Opereción">
                                <?php   if(!empty($TipoP)){ ?>
                                            <option value="<?php echo $T4[0] ?>"><?php echo $T4[1]?></option>
                                <?php   }else{ ?>      
                                            <option value=""> - </option>
                                <?php   }            
                                        while($TIO=mysqli_fetch_array($TOP)){ ?>
                                            <option value="<?php echo $TIO[0]?>"><?php echo $TIO[1] ?></option>
                                <?php   } ?>
                            </select>
                        </div>
                        <?php
                            $ClaseC = $row[12];
                            if(!empty($ClaseC)){
                                $ClaseCo = "SELECT id_unico, nombre FROM gc_clase_concepto WHERE  id_unico != '$ClaseC'";
                                $Cla = $mysqli->query($ClaseCo);
                            }else{
                                $ClaseCo = "SELECT id_unico, nombre FROM gc_clase_concepto ";
                                $Cla = $mysqli->query($ClaseCo);
                            }
                            
                            $T2 = "SELECT id_unico, nombre FROM gc_clase_concepto WHERE id_unico = '$ClaseC' ";
                            $T3 = $mysqli->query($T2);
                            $T4 = mysqli_fetch_row($T3);
                        ?>
                        <div class="form-group" style="margin-top: -1px;" >
                            <label for="ClaseC" class="col-sm-4 col-md-4 col-lg-4 control-label"><strong style="color:#03C1FB;">*</strong>Clase Concepto:</label>
                            <select name="ClaseC" id="ClaseC" required  class="select2_single form-control" title="Seleccione Clsew Concepto">
                                <?php   if(!empty($ClaseC)){ ?>
                                            <option value="<?php echo $T4[0] ?>"><?php echo $T4[1]?></option>
                                <?php   }else{ ?>      
                                            <option value=""> - </option>
                                <?php   }            
                                        while($CC=mysqli_fetch_array($Cla)){ ?>
                                            <option value="<?php echo $CC[0]?>"><?php echo $CC[1] ?></option>
                                <?php   } ?>
                            </select>
                        </div>

                        <div class="form-group" style="margin-top: -10px;">
                    
                            <label for="con_inf" class="control-label col-sm-4 col-md-4 col-lg-4" >
                                    <strong class="obligado">*</strong>Concepto Informe: 
                            </label>
                            <input  type="text" name="con_inf" required id="con_inf" class="form-control"  title="Ingrese Descripción" onkeypress="return txtValida(event,'num_car')" placeholder="Descripción" value="<?php echo $row[1] ?>">
                        </div>

                                       
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="txtDesc" class="control-label col-sm-4 col-md-4 col-lg-4" >Aplica Descuento? </label>
                            <?php
                                if(empty($row[9]) || $row[9] == 2){
                            ?>
                                    <input  type="radio" name="txtDesc"  id="txtDesc"  value="1">SI
                                    <input  type="radio" name="txtDesc"  id="txtDesc"  value="2" checked>NO
                            <?php

                                }else{
                            ?>
                                    <input  type="radio" name="txtDesc"  id="txtDesc"  value="1" checked>SI
                                    <input  type="radio" name="txtDesc"  id="txtDesc"  value="2" >NO
                            <?php } ?>
                        </div>

                        <div class="form-group" style="margin-top: -10px;">
                    
                            <label for="txtInt" class="control-label col-sm-4 col-md-4 col-lg-4" >Aplica Interés? </label>
                            <?php
                                if(empty($row[10]) || $row[10] == 2){
                            ?>
                                    <input  type="radio" name="txtInt"  id="txtInt"  value="1">SI
                                    <input  type="radio" name="txtInt"  id="txtInt"  value="2" checked>NO
                            <?php

                                }else{
                            ?>
                                    <input  type="radio" name="txtInt"  id="txtInt"  value="1" checked>SI
                                    <input  type="radio" name="txtInt"  id="txtInt"  value="2" >NO
                            <?php } ?>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                        
                                <label for="txtAnt" class="control-label col-sm-4 col-md-4 col-lg-4" >Tipo Anticipo? </label>
                                    <?php   if(empty($row[11]) || $row[11] == 2 ){ ?>
                                        <input  type="radio" name="txtAnt"  id="txtAnt"  value="1">SI
                                        <input  type="radio" name="txtAnt"  id="txtAnt"  value="2" checked>NO
                                    <?php   }else{ ?>
                                        <input  type="radio" name="txtAnt"  id="txtAnt"  value="1" checked>SI
                                        <input  type="radio" name="txtAnt"  id="txtAnt"  value="2">NO
                                    <?php   } ?>
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