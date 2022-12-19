<?php 
    require_once 'head.php';
    require_once('Conexion/conexion.php');
    $compania = $_SESSION['compania'];
    //Clase
    $claseB = "SELECT id_unico, nombre FROM gf_clase ORDER BY nombre ASC";
    $clase = $mysqli->query($claseB);
    //Elemento
    $elementoB = "SELECT id_unico, nombre FROM gs_tipo_elemento ORDER BY nombre ASC";
    $elemento = $mysqli->query($elementoB);
    //Persona
    $persB = "SELECT id_unico, nombre FROM gs_tipo_persona ORDER BY nombre ASC";
    $persona = $mysqli->query($persB);
    //Formato
    $formatoB = "SELECT id_unico, nombre FROM gf_tipo_documento WHERE compania = $compania ORDER BY nombre ASC";
    $formato = $mysqli->query($formatoB);
?>
    <title>Registrar Tipo Movimiento</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px; margin-top: 0px;">Registrar Tipo Movimiento</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 0px" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GF_TIPO_MOVIMIENTOJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="txtSigla" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Sigla:</label>
                            <input type="text" name="txtSigla" id="txtSigla" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese sigla"  placeholder="Sigla" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre"  placeholder="Nombre" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="costea" class="col-sm-5 control-label" style="margin-top: -7px"><strong style="color:#03C1FB;">*</strong>Costea:</label>
                            <input type="radio" name="costea" id="costea" value="1">Sí
                            <input type="radio" name="costea" id="costea" checked value="2">No
                        </div>
                        <div class="form-group">
                            <label for="clase" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase:</label>
                            <select name="clase" id="clase"  class="form-control col-sm-1" title="Seleccione clase" required="required">
                                <option value="">Clase</option>
                                <?php while($rowClase = mysqli_fetch_row($clase)){?>
                                <option value="<?php echo $rowClase[0] ?>"><?php echo ucwords((mb_strtolower($rowClase[1])));}?></option>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="elemento" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Elemento:</label>
                            <select name="elemento" id="elemento"  class="form-control col-sm-1" title="Seleccione tipo elemento" required="required">
                                <option value="">Tipo Elemento</option>
                                <?php while($rowElem = mysqli_fetch_row($elemento)){?>
                                <option value="<?php echo $rowElem[0] ?>"><?php echo ucwords((mb_strtolower($rowElem[1])));}?></option>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="persona" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Persona:</label>
                            <select name="persona" id="persona"  class="form-control col-sm-1" title="Seleccione tipo persona" required="required">
                                <option value="">Tipo Persona</option>
                                <?php while($rowPers = mysqli_fetch_row($persona)){?>
                                <option value="<?php echo $rowPers[0] ?>"><?php echo ucwords((mb_strtolower($rowPers[1])));}?></option>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="formato" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Documento:</label>
                            <select name="formato" id="formato"  class="form-control col-sm-1" title="Seleccione documento" required="required">
                                <option value="">Tipo Documento</option>
                                <?php while($rowForm = mysqli_fetch_row($formato)){?>
                                <option value="<?php echo $rowForm[0] ?>"><?php echo ucwords((mb_strtolower($rowForm[1])));}?></option>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php';?>
</body>
</html>