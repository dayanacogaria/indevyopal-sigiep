<?php require_once('Conexion/conexion.php');
require_once 'head.php';
$id_param = " ";
if (isset($_GET["id_param"])) {
    $id_param = (($_GET["id_param"]));
    $queryParam = "SELECT P.Id_Unico, 
                    P.Anno, 
                    P.SalarioMinimo, 
                    P.MinDepreciacion, 
                    P.UVT, 
                    P.CajaMenor, 
                    E.Id_Unico, 
                    E.Nombre,
                    P.minimacuantia, 
                    P.menorcuantia, 
                    P.menorcuantia_m, 
                    P.mayorcuantia 
      FROM gf_parametrizacion_anno P 
      LEFT JOIN gf_estado_anno E ON P.EstadoAnno = E.Id_Unico 
      WHERE md5(P.Id_Unico) ='$id_param'";
}
$resultado = $mysqli->query($queryParam);
$row = mysqli_fetch_row($resultado);
$estadoAn = "SELECT Id_Unico, Nombre FROM gf_estado_anno ORDER BY Nombre ASC";
$estadoA = $mysqli->query($estadoAn);
?>
<title>Modificar Parametrizacion Año</title>
</head> 
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px;">
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Parametrización Año</h2>
            <a href="listar_GF_PARAMETRIZACION_ANNO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo $row[1] ?></h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class=" client-form">
                <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarParamAnnoJson.php">
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <input type="hidden" name="datoAnno" id="datoAnno" value="<?php echo $row[1]; ?>"/>
                    <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="valor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Año:</label>
                        <input type="number" name="valor" id="valor" onblur="return existente()"  class="form-control" maxlength="4" title="Ingrese el año" onkeypress="return txtValida(event, 'num')" placeholder="Año" value="<?php echo $row[1] ?>"  required>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="salariom" class="col-sm-5 control-label">Salario Mínimo:</label>
                        <input type="text" name="salariom" id="salariom"  class="form-control" maxlength="19" title="Ingrese el salario mínimo" onkeypress="return txtValida(event, 'dec', 'salariom', '2')" placeholder="Salario mínimo"  value="<?php echo $row[2] ?>">
                    </div>   
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="minimod" class="col-sm-5 control-label">Mínimo Depreciación:</label>
                        <input type="text" name="minimod" id="minimod"  class="form-control" maxlength="19" title="Ingrese el mínimo depreciación" onkeypress="return txtValida(event, 'dec', 'minimod', '2')" placeholder="Mínimo depreciación" value="<?php echo $row[3] ?>">
                    </div>   
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="uvt" class="col-sm-5 control-label">UVT:</label>
                        <input type="text" name="uvt" id="uvt" class="form-control" maxlength="19" title="Ingrese UVT" onkeypress="return txtValida(event, 'dec', 'uvt', '2')" placeholder="UVT" value="<?php echo $row[4] ?>">
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="cajam" class="col-sm-5 control-label">Caja Menor:</label>
                        <input type="text" name="cajam" id="cajam" class="form-control" maxlength="19" title="Ingrese caja menor" onkeypress="return txtValida(event, 'dec', 'cajam', '2')" placeholder="Caja menor"  value="<?php echo $row[5] ?>">
                    </div>
                    <div class="form-group">
                        <label for="estadoA" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Estado Año:</label>
                        <select name="estadoA" id="estadoA" class="form-control" title="Seleccione el estado año" required>                  
                            <?php
                            if (empty($row[7])) {
                                echo '<option value=""> - </option>';
                            }
                            while ($rowC = mysqli_fetch_assoc($estadoA)) {                
                                if ($row[7] == $rowC['Nombre']) {    ?>
                                    <option value="<?php echo $rowC['Id_Unico'] ?>"><?php echo ucwords((strtolower($rowC['Nombre']))); ?></option>
                                <?php } else {                              
                                     if (($rowC['Nombre']) == NULL) { ?>
                                        <option></option>
                                        <option value="<?php echo $rowC['Id_Unico'] ?>"><?php echo ucwords((strtolower($rowC['Nombre']))); ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $rowC['Id_Unico'] ?>"><?php echo ucwords((strtolower($rowC['Nombre']))); ?></option>
                                    <?php } 
                                 }                   
                             }
                            ?>
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="min_c" class="col-sm-5 control-label">Mínima Cuantía:</label>
                        <input type="text" name="min_c" id="cajam"  class="form-control"  title="Ingrese Mínima Cuantía" onkeypress="return txtValida(event, 'dec', 'min_c', '2')" placeholder="Mínima Cuantía" value="<?php echo $row[8] ?>">
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="menor_c" class="col-sm-5 control-label">Menor Cuantía:</label>
                        <input type="text" name="menorc" id="menorc"  class="form-control" maxlength="19" title="Ingrese Menor Cuantía" onkeypress="return txtValida(event, 'dec', 'menorc', '2')" placeholder="Menor Cuantía Desde" style="width: 150px; display:inline-block" value="<?php echo $row[9] ?>">
                        <input type="text" name="menorcm" id="menorcm"  class="form-control" maxlength="19" title="Ingrese Menor Cuantía" onkeypress="return txtValida(event, 'dec', 'menorcm', '2')" placeholder="Menor Cuantía Hasta" style="width: 150px; display:inline-block" value="<?php echo $row[10] ?>">
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="mayorc" class="col-sm-5 control-label">Mayor Cuantía:</label>
                        <input type="text" name="mayorc" id="mayorc"  class="form-control" maxlength="19" title="Ingrese Mayor Cuantía" onkeypress="return txtValida(event, 'dec','mayorc', '2')" placeholder="Mayor Cuantía" value="<?php echo $row[11] ?>">
                    </div>
                    <div align="center" style="margin-top: -10px;">
                        <button type="submit" class="btn btn-primary sombra" >Guardar</button>
                    </div>
                    <div class="texto" style="display:none"></div>
                    <input type="hidden" name="MM_insert" >
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>

<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Este Año ya existe.¿Desea actualizar la información?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px"  data-dismiss="modal" id="ver2">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function existente() {
        var anno = document.form.valor.value;
        var result = '';

        if (anno == null || anno == '' || anno == "Año") {

            $("#myModal2").modal('show');//consulta si el campo tiene algun valor, pero como es en el mdificar siempre va tener un dato, no se necesita

        } else { //se hace un envio por POST tomando el valor del camppo y consultando y como resultado me imprime un campo oculto con el ID y un modal preguntando si deseo cargar los datos.

            $.ajax({
                data: {"anio": anno},
                type: "POST",
                url: "consultarParametrizacion.php",
                success: function (data) {

                    var res = data.split(";");

                    if (res[1] == 'true1') {
                        $('.texto').html(data);
                        $("#myModal1").modal('show');

                    }
                }
            });
        }
    }
</script>

<script type="text/javascript">
    $('#ver1').click(function () {
        var id = document.getElementById("id").value;
        console.log(id);
        document.location = 'modificar_GF_PARAMETRIZACION_ANNO.php?id_param=' + id;
    });

</script>

<script type="text/javascript">
    $('#ver2').click(function () {
        var anio = document.form.datoAnno.value;
        $("#valor").val(anio)
    });

</script>

</body>
</html>



