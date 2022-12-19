<?php 
    require_once 'head.php';
    require_once('Conexion/conexion.php');
    //Captura de ID y consulta del resgistro correspondiente.
    $id_elemento_ficha = " ";
    if (isset($_GET["id_elemento_ficha"])){ 
        $id_elemento_ficha = (($_GET["id_elemento_ficha"]));
        $queryElementoFicha = "SELECT ef.Id_Unico id, ef.Nombre Nombre, ef.TipoDato idTipoDato, td.Nombre tipoDato
                                FROM gf_elemento_ficha ef, gf_tipo_dato td 
                                WHERE ef.TipoDato=td.Id_Unico
                                AND md5(ef.Id_Unico)='$id_elemento_ficha'";
    }
    $resultado = $mysqli->query($queryElementoFicha);
    $row = mysqli_fetch_row($resultado);
    //Consulta para el listado del combo 'tipoDato' correspondiente a los datos de la tabla gf_tipo_cargo.
    $selectTipoDato = "SELECT Id_Unico, Nombre FROM gf_tipo_dato WHERE Id_Unico != $row[2] ORDER BY Nombre ASC";
    $tipoDato =   $mysqli->query($selectTipoDato);
?>
    <title>Modificar Elemento Ficha</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Modificar Elemento Ficha</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="controller/controllerGFElementoFicha.php?action=modify">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>           
                        <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" value="<?php echo $row[1] ?>" required>
                        </div>        
                        <div class="form-group">
                            <label for="tipoDato" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo de Dato:</label>
                            <select name="tipoDato" id="tipoDato" class="form-control" title="Seleccione el tipo de dato" required>
                                <option value="<?php echo $row[2] ?>"><?php echo $row[3] ?></option>
                                <?php while($rowTD = mysqli_fetch_assoc($tipoDato)){?>
                                <option value="<?php echo $rowTD['Id_Unico'] ?>"><?php echo ucwords(utf8_encode(strtolower($rowTD['Nombre'])));}?></option>;
                            </select> 
                        </div>                            
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                        </div>            
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>      
            </div>
        </div>
    </div>  
    <?php require_once 'footer.php'; ?>
</body>
</html>