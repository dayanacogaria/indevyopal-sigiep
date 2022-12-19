<?php 
    require_once 'head.php';
    require_once('Conexion/conexion.php');
    //Consulta para el listado del combo 'tipoDato' correspondiente a los datos de la tabla gf_tipo_dato.
    $selectTipoDato = "SELECT Id_Unico, Nombre FROM gf_tipo_dato ORDER BY Nombre ASC";
    $tipoDato =   $mysqli->query($selectTipoDato);
?>
    <title>Registrar Elemento Ficha</title>
</head>
<body>  
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Registrar Elemento Ficha</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="controller/controllerGFElementoFicha.php?action=insert">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>        
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="tipoDato" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Dato:</label>
                            <select name="tipoDato" id="tipoDato" class="form-control" title="Seleccione el tipo de dato" required>
                                <option value="">Tipo Dato</option>
                                <?php while($row = mysqli_fetch_assoc($tipoDato)){?>
                                <option value="<?php echo $row['Id_Unico'] ?>"><?php echo ucwords(utf8_encode(strtolower($row['Nombre'])));}?></option>;
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
    <?php require_once 'footer.php';  ?>
</body>
</html>