
<?php
require_once ('head.php');
require_once ('./Conexion/conexion.php');
?>
<title>Registrar Tipo Comparendo</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top: -20px">
                <h2 id="forma-titulo3" align="center" style="margin-right: 4px; margin-left: 4px;">Registrar Tipo Comparendo</h2>
                <a href="listar_GU_TIPO_COMPARENDO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Tipo</h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarTipoComparendoJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                                            
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="anno" class="col-sm-5 control-label"><strong class="obligado">*</strong>Año:</label>
                            <input type="text" name="anno" id="anno" class="form-control" maxlength="4" title="Ingrese el valor" onkeypress="return txtValida(event, 'num')" placeholder="Año" required="required">
                        </div>  
                        <!----------Campo para llenar Codigo -->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="codigoadmin" class="col-sm-5 control-label"><strong class="obligado">*</strong>Código:</label>
                            <input type="text" name="txtCodigo" id="txtCodigo" class="form-control" maxlength="100" title="Ingrese el código" onkeypress="return txtValida(event,'num_car')" placeholder="Código" required>
                        </div>                                    
                        <!----------Fin Campo Codigo Interno-->
                        <!----------Campo para llenar Nombre-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="Nombre" class="col-sm-5 control-label"><strong class="obligado"></strong>Nombre:</label>
                            <input type="text" name="txtNombre" id="txtNombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre">
                        </div>                                    
                        <!----------Fin Campo Nombre-->
                        <!----------Campo para llenar Sigla Sancion-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="Sigla_Sancion" class="col-sm-5 control-label"><strong class="obligado"></strong>Sigla Sanción:</label>
                            <input type="text" name="txtSigla" id="txtSigla" class="form-control" maxlength="100" title="Ingrese la sigla sanción" onkeypress="return txtValida(event, 'car')" placeholder="Sigla Sanción">
                        </div>                                                  
                        <!----------Fin Campo Sigla Sancion-->
                        <!----------Campo para llenar Sancion-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="Sancion" class="col-sm-5 control-label"><strong class="obligado"></strong>Sanción (Detallada):</label>
                            <input type="text" name="txtSancion" id="txtSancion" class="form-control" maxlength="100" title="Ingrese la sanción" onkeypress="return txtValida(event, 'car')" placeholder="Sanción">
                        </div>                                                  
                        <!----------Fin Campo Sancion-->
                        <!----------Campo para llenar Valor Sancion-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="Valor" class="col-sm-5 control-label"><strong class="obligado">*</strong>Valor Sanción:</label>
                            <input type="text" name="txtValor" id="txtValor" class="form-control" maxlength="100" title="Ingrese el valor" onkeypress="return txtValida(event, 'num')" placeholder="Valor Sanción" required="">
                        </div>                                    
                        <!-------------------------Fin campo Gasto Representación-->

                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px;margin-left: 0px  ;">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>                  
            <div class="col-sm-8 col-sm-1" styl>        
            </div>
        </div>
    </div>        
    <?php require_once './footer.php'; ?>
</body>
</html>
