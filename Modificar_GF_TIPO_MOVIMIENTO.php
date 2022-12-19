<?php 
    require_once 'head.php';
    require_once('Conexion/conexion.php');
    $compania = $_SESSION['compania'];
    $id = $_GET['id'];
    $bus= "SELECT tm.id_unico, tm.nombre, tm.costea, c.id_unico, c.nombre, te.id_unico, te.nombre, tp.id_unico, tp.nombre, f.id_unico, f.nombre, tm.sigla
        FROM gf_tipo_movimiento tm 
        LEFT JOIN gf_clase c ON tm.clase=c.id_unico 
        LEFT JOIN gs_tipo_elemento te ON tm.tipoelemento = te.id_unico 
        LEFT JOIN gs_tipo_persona tp ON tm.tipopersona=tp.id_unico 
        LEFT JOIN gf_tipo_documento f ON tm.tipo_documento = f.id_unico 
        WHERE md5(tm.id_unico) = '$id'";
    $busqueda = $mysqli->query($bus);
    $rowB=  mysqli_fetch_row($busqueda);
    //Clase
    $claseB = "SELECT id_unico, nombre FROM gf_clase WHERE id_unico != $rowB[3] ORDER BY nombre ASC";
    $clase = $mysqli->query($claseB);
    //Elemento
    $elementoB = "SELECT id_unico, nombre FROM gs_tipo_elemento WHERE id_unico != $rowB[5] ORDER BY nombre ASC";
    $elemento = $mysqli->query($elementoB);
    //Persona
    $persB = "SELECT id_unico, nombre FROM gs_tipo_persona WHERE id_unico != $rowB[7] ORDER BY nombre ASC";
    $persona = $mysqli->query($persB);
    //Formato
    $formatoB = "SELECT id_unico, nombre FROM gf_tipo_documento WHERE id_unico != '$rowB[9]' AND compania = $compania ORDER BY nombre ASC";
    $formato = $mysqli->query($formatoB);
?>
    <title>Modificar Tipo Movimiento</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
        <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px; margin-top: 0px">Modificar Tipo Movimiento</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 0px" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GF_TIPO_MOVIMIENTOJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" value="<?php echo $rowB[0]?>" name="id" id="id">
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="txtSigla" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Sigla:</label>
                            <input type="text" name="txtSigla" id="txtSigla" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre"  placeholder="Nombre" required value="<?php echo mb_strtoupper($rowB[11])?>">
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre"  placeholder="Nombre" required value="<?php echo $rowB[1]?>">
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="costea" class="col-sm-5 control-label" style="margin-top: -7px"><strong style="color:#03C1FB;">*</strong>Costea:</label>
                            <?php if($rowB[2]=='1') { ?>
                            <input type="radio" name="costea" id="costea" value="1" checked>Sí
                            <input type="radio" name="costea" id="costea" value="2">No
                            <?php } else { ?>
                            <input type="radio" name="costea" id="costea" value="1" >Sí
                            <input type="radio" name="costea" id="costea" value="2" checked>No
                            <?php }?>
                        </div>
                        <div class="form-group">
                            <label for="clase" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase:</label>
                            <select name="clase" id="clase"  class="form-control col-sm-1" title="Seleccione clase" required="required">
                                <option value="<?php echo $rowB[3]?>"><?php echo ucwords(mb_strtolower($rowB[4]))?></option>
                                <?php while($rowClase = mysqli_fetch_row($clase)){?>
                                <option value="<?php echo $rowClase[0] ?>"><?php echo ucwords((mb_strtolower($rowClase[1])));}?></option>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="elemento" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Elemento:</label>
                            <select name="elemento" id="elemento"  class="form-control col-sm-1" title="Seleccione tipo elemento" required="required">
                                <option value="<?php echo $rowB[5]?>"><?php echo ucwords(mb_strtolower($rowB[6]))?></option>
                                <?php while($rowElem = mysqli_fetch_row($elemento)){?>
                                <option value="<?php echo $rowElem[0] ?>"><?php echo ucwords((mb_strtolower($rowElem[1])));}?></option>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="persona" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Persona:</label>
                            <select name="persona" id="persona"  class="form-control col-sm-1" title="Seleccione tipo persona" required="required">
                                <option value="<?php echo $rowB[7]?>"><?php echo ucwords(mb_strtolower($rowB[8]))?></option>
                                <?php while($rowPers = mysqli_fetch_row($persona)){?>
                                <option value="<?php echo $rowPers[0] ?>"><?php echo ucwords((mb_strtolower($rowPers[1])));}?></option>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="formato" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Documento:</label>
                            <select name="formato" id="formato"  class="form-control col-sm-1" title="Seleccione tipo documento" required="required">
                                <option value="<?php echo $rowB[9]?>"><?php echo ucwords(mb_strtolower($rowB[10]))?></option>
                                <?php while($rowForm = mysqli_fetch_row($formato)){?>
                                <option value="<?php echo $rowForm[0] ?>"><?php echo ucwords((strtolower($rowForm[1])));}?></option>
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

