<?php 
require_once('Conexion/conexion.php');
require_once 'head.php';
$estadoAn = "SELECT Id_Unico, Nombre FROM gf_estado_anno ORDER BY Nombre ASC";
$estadoA =   $mysqli->query($estadoAn);
?>
  <title>Registrar Parametrizacion año</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top: -20px;">
                <h2 id="forma-titulo3" align="center" style=" margin-right: 4px; margin-left: 4px;">Registrar Parametrización Año</h2>
                <a href="listar_GF_PARAMETRIZACION_ANNO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Año</h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarParamAnnoJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="valor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Año:</label>
                            <input type="text" name="valor" id="valor"  class="form-control" maxlength="4" title="Ingrese el año" onkeypress="return txtValida(event, 'num')" placeholder="Año" required>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="salariom" class="col-sm-5 control-label"> Salario Mínimo:</label>
                            <input type="text" name="salariom" id="salariom"  class="form-control" maxlength="19" title="Ingrese el salario mínimo" onkeypress="return txtValida(event, 'dec', 'salariom', '2')" placeholder="Salario mínimo" >
                        </div>   
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="minimod" class="col-sm-5 control-label">Mínimo Depreciación:</label>
                            <input type="text" name="minimod" id="minimod"  class="form-control" maxlength="19" title="Ingrese el mínimo depreciación" onkeypress="return txtValida(event, 'dec', 'minimod', '2')" placeholder="Mínimo depreciación" >
                        </div>         
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="uvt" class="col-sm-5 control-label">UVT:</label>
                            <input type="text" name="uvt" id="uvt"  class="form-control" maxlength="19" title="Ingrese UVT" onkeypress="return txtValida(event, 'dec', 'uvt', '2')" placeholder="UVT">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="cajam" class="col-sm-5 control-label">Caja Menor:</label>
                            <input type="text" name="cajam" id="cajam"  class="form-control" maxlength="19" title="Ingrese caja menor" onkeypress="return txtValida(event, 'dec', 'cajam', '2')" placeholder="Caja menor">
                        </div>
                        <div class="form-group"  style="margin-top: -10px;">
                            <label for="estadoA" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Estado Año:</label>
                            <select name="estadoA" id="estadoA" class="form-control" title="Seleccione el estado año" required>
                                <option value="">Estado Año</option>
                                <?php while ($row = mysqli_fetch_assoc($estadoA)) { ?>
                                    <option value="<?php echo $row['Id_Unico'] ?>"><?php echo ucwords((strtolower($row['Nombre'])));
                            } ?></option>;
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="min_c" class="col-sm-5 control-label">Mínima Cuantía:</label>
                            <input type="text" name="min_c" id="cajam"  class="form-control"  title="Ingrese Mínima Cuantía" onkeypress="return txtValida(event, 'dec', 'min_c', '2')" placeholder="Mínima Cuantía">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="menor_c" class="col-sm-5 control-label">Menor Cuantía:</label>
                            <input type="text" name="menorc" id="menorc"  class="form-control" maxlength="19" title="Ingrese Menor Cuantía" onkeypress="return txtValida(event, 'dec', 'menorc', '2')" placeholder="Menor Cuantía Desde" style="width: 150px; display:inline-block">
                            <input type="text" name="menorcm" id="menorcm"  class="form-control" maxlength="19" title="Ingrese Menor Cuantía" onkeypress="return txtValida(event, 'dec', 'menorcm', '2')" placeholder="Menor Cuantía Hasta" style="width: 150px; display:inline-block">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="mayorc" class="col-sm-5 control-label">Mayor Cuantía:</label>
                            <input type="text" name="mayorc" id="mayorc"  class="form-control" maxlength="19" title="Ingrese Mayor Cuantía" onkeypress="return txtValida(event, 'dec','mayorc', '2')" placeholder="Mayor Cuantía">
                        </div>
                        <div align="center" style="margin-top: -10px;">
                            <button type="submit" class="btn btn-primary sombra" >Guardar</button>
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



