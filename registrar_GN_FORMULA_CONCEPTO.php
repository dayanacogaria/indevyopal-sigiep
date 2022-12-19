
<?php
require_once ('head.php');
require_once ('./Conexion/conexion.php');
#session_start();
$sqlCatFor = "SELECT id_unico, nombre 
  FROM gn_categoria_formula   
  ORDER BY nombre ASC";
$cateForm = $mysqli->query($sqlCatFor);
?>
    <title>Registrar Fórmula Concepto</title>
    <style type="text/css" media="screen">
        .client-form #txtEcuacion {
            height: 100px !important;
            width: 35%;
        }    
        .client-form textarea {
            height: 60px !important;
            width: 35%;
        }    
        .client-form select {
            width: 35%;
        }

        #btnFX { 
            width: 30px; 
            height: 30px; 
        }
        #btnFX:hover {
            cursor: pointer;
            border-radius: 5px;
        }

        .shadow {
            box-shadow: 1px 1px 1px 1px gray;
            border-color:#1075C1;
        }        
    </style>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
</head>
<body>
    <input type="hidden" id="id_cat_for">
    <input type="hidden" id="id_concepto">
    <div class="container-fluid text-center">
        <div class="row content">
        <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Registrar Fórmula Concepto</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarFormulaConceptoJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 15px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                               
                        <!-- Campo para llenar Ecuación-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="ecuacion" class="col-sm-4 control-label"><strong class="obligado">*</strong>Ecuación:</label>
                            <textarea type="text" name="formulaF" id="formulaF" class="form-control col-sm-1" maxlength="100" title="Ingrese la ecuación" placeholder="Ecuación" required style="margin-top: 0px" readonly=""></textarea>
                            <!-- Botón para abrir el formulador -->
                            <div class="col-sm-1">
                                <div id="btnFX">
                                    <a onclick="abrirCategoriaFormula()">                                
                                        <img src="images/formula.png" width="30px" height="30px" title="Fórmula">
                                    </a>                                                                    
                                </div>
                            </div><!-- ./Bóton para abrir el formulador -->
                        </div>
                        <!--Fin Campo para llenar Ecuación-->
                        <!--Campo para llenar Descripción-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="descripcion" class="col-sm-4 control-label">Descripción:</label>
                            <textarea type="text" name="txtDescripcion" id="txtDescripcion" class="form-control" maxlength="100" title="Ingrese la descripción" placeholder="Descripción" style="margin-top: 0px"></textarea>
                        </div>
                        <!--Fin Campo para llenar Descripción-->                              
                        <!--Consulta para llenar campo Tipo Proceso Nómina-->
                        <?php $tpn = "SELECT id_unico, nombre FROM gn_tipo_proceso_nomina ";$tipopr = $mysqli->query($tpn); ?>
                        <div class="form-group" style="margin-top: -10px">
                            <label class="control-label col-sm-4"><strong class="obligado">*</strong>Proceso Nómina:</label>
                            <select name="sltProceso" class="form-control" id="sltProceso" title="Seleccione proceso" required="">
                                <?php 
                                echo "<option value=\"\">Proceso Nómina</option>";
                                while ($filaTPN = mysqli_fetch_row($tipopr)) { 
                                    echo "<option value=\"$filaTPN[0]\">".ucwords(mb_strtolower($filaTPN[1]))."</option>";                    
                                } ?>
                            </select>   
                        </div>
                        <!--Fin Consulta Para llenar Empleado-->
                        <!--Consulta para llenar campo Concepto-->
                        <?php $con = "SELECT id_unico, CONCAT(codigo,' - ',descripcion) FROM gn_concepto";$concept = $mysqli->query($con); ?>
                        <div class="form-group" style="margin-top: -10px;margin-bottom: 30px">
                            <label class="control-label col-sm-4"><strong class="obligado">*</strong>Concepto:</label>
                            <select name="sltConcepto" class="form-control col-sm-1" id="sltConcepto" title="Seleccione concepto" style="width: 35%;" required="">
                                <?php 
                                echo "<option value=\"\">Concepto</option>";
                                while ($filaC = mysqli_fetch_row($concept)) { 
                                  echo "<option value=\"$filaC[0]\">".ucwords(mb_strtolower($filaC[1]))."</option>";
                                } ?>
                              </select>   
                          </div>
                        <!--Fin Consulta para llenar Concepto-->
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-4 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style="margin-bottom: 10px;margin-left: 0px  ;">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>                          
        </div>
    </div>        
    <?php require_once './footer.php'; ?>    
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" >
        function abrirCategoriaFormula() {$("#mdlCategoriaFor").modal('show');}                     
    </script>           
    <?php require_once './GP_FORMULADOR.php'; ?>
    <div class="modal fade" id="mdlCategoriaFor" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">            
            <input type="hidden" name="idM" id="idM">
            <div class="modal-content client-form1">
                <div id="forma-modal" class="modal-header">       
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Categoría Fórmula</h4>
                </div>
                <div class="modal-body "  align="center">
                    <div class="form-group" align="left">
                        <label  style="margin-left:150px; display:inline-block;"><strong style="color:#03C1FB;">*</strong>Categoría Fórmula:</label>
                        <select style="display:inline-block; width:250px; font-size: 0.9em; height: 30px; padding: 5px;" type="text" name="catFor" id="catFort" title="Ingrese la categoría" class="form-control input-sm"  required>
                            <option value="">Categoría Fórmula</option>
                            <?php 
                            while($row = mysqli_fetch_row($cateForm)) {
                                echo '<option value="'.$row[0].'">'.utf8_encode(ucwords(strtolower($row[1]))).'</option>';
                            } ?>
                        </select>
                    </div>
                    <input type="hidden" id="id" name="id">  
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnCatFor" class="btn" style="color: #000; margin-top: 2px">Aceptar</button>
                    <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript">    
        $("#sltConcepto").select2({allowClear:true});
        $("#catFort").change(function() {
            var id_cat_for = $("#catFort").val();
            $("#id_cat_for").val(id_cat_for);
        });
        
        $("#btnCatFor").click(function() {
            if($("#id_cat_for").val() != "") {
                $("#mdlCategoriaFor").modal('hide');
                var id_cat_for = document.getElementById('id_cat_for').value;
                var formula = document.getElementById('formulaF').value;
                var consulta = "SELECT nombre FROM gn_variables WHERE categoria = " + id_cat_for + " ORDER BY nombre ASC";
                var form_data = {                           
                    consulta : consulta, 
                    formula : formula
                };
                $.ajax({
                    type: 'POST',
                    url: "GP_FORMULADOR.php#mdlFormulador",
                    data:form_data,
                    success: function (data) { 
                        $("#mdlFormulador").html(data);
                        $(".mov").modal('show');
                    }
                });
            } else {
                $("#catFort").focus();
            }
        });         
    </script>
    <div class="modal fade" id="myModalFinalizacion" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Finalización Inválida</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="verFinalizacion" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalFaltaC" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Hace falta cerrar expresión</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="verFaltaC" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalCondicion" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Condición mal estructurada</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="verCondicion" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
    