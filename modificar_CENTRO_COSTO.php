<?php 
########################################################################################
#       ***************    Modificaciones *************** #
########################################################################################
#14/12/2017 | Parametrización, tildes, diseño
########################################################################################
require_once 'head.php';
require_once('Conexion/conexion.php');
$id_cent_cost = " ";
$anno = $_SESSION['anno'];
if (isset($_GET["id_cent_cost"])) {
    $id_cent_cost = (($_GET["id_cent_cost"]));

    $queryCentroCosto = "SELECT cc.Id_Unico, cc.Nombre, cc.Movimiento, 
        cc.Sigla, cc.TipoCentroCosto, tcc.Nombre, cc.Predecesor, (select Nombre from gf_centro_costo where Id_Unico = cc.Predecesor) nombrePredecesor, 
        cc.ClaseServicio, cs.Nombre, cc.cantidad_distribucion 
  FROM gf_centro_costo cc 
  LEFT JOIN gf_tipo_centro_costo tcc ON cc.TipoCentroCosto = tcc.Id_Unico
  LEFT JOIN gf_clase_servicio cs ON cc.ClaseServicio = cs.Id_Unico
  WHERE md5(cc.Id_Unico) = '$id_cent_cost'";
}

$resultado = $mysqli->query($queryCentroCosto);
$row = mysqli_fetch_row($resultado);

//Hacer la consulta en la tabla gf_centro_costo para determinar que si el registro a modificar existe como un predecesor de otro registro.
$num = 0;
$queryPred = "SELECT Id_Unico FROM gf_centro_costo WHERE Predecesor = $row[0]";
$noHij = $mysqli->query($queryPred);
$num = $noHij->num_rows;

//Consultas para el listado de los diferentes combos correspondientes.
//Tipo Centro Costo.
$sqlTipoCentCost = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_centro_costo 
  WHERE Id_Unico != $row[4] 
  ORDER BY Nombre ASC";
$tipoCentCost = $mysqli->query($sqlTipoCentCost);

//Predecesor.
//Movimiento es un campo tipo bit, por tanto tres es No.
$sqlPredecesor = "SELECT Id_Unico, Nombre 
  FROM gf_centro_costo 
  WHERE Movimiento = 2
  AND Id_Unico != '$row[6]' 
  AND Id_Unico != $row[0] 
  AND parametrizacionanno = $anno
  ORDER BY Nombre ASC";
$predecesor = $mysqli->query($sqlPredecesor);

//Clase Servicio.
$sqlClaseServ = "SELECT Id_Unico, Nombre 
  FROM gf_clase_servicio 
  WHERE Id_Unico != $row[8]  
  ORDER BY Nombre ASC";
$claseServ = $mysqli->query($sqlClaseServ);
?>
<html>
    <head>
        <link href="css/select/select2.min.css" rel="stylesheet">
        <title>Modificar Centro Costo</title>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-7 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Centro Costo</h2>
                    <a href="CENTRO_COSTO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Centro Costo: <?php echo ucwords(mb_strtolower($row[1])) ?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_CENTRO_COSTOJson.php">
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <input type="hidden" name="id" value="<?php echo $row[0]; ?>">
                            <div class="form-group" style="margin-top: -20px; ">
                                <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car');"  placeholder="Nombre" value="<?php echo ucwords((mb_strtolower($row[1]))); ?>" required>   
                            </div>
                            <div class="form-group" style="margin-top: -20px; ">
                                <label for="movimiento" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Movimiento:</label>
                                <div class="form-inline" >
                                    <?php if($row[2]==1){ ?>
                                    
                                        <input type="radio" name="movimiento" value="1" onchange="javaScript:cambio(1);" checked="checked" title="Seleccione si tiene movimiento o no." /> Sí&nbsp &nbsp
                                        <input type="radio" name="movimiento" value="2" onchange="javaScript:cambio(2);" title="Seleccione si tiene movimiento o no." /> No
                                    <?php } else { ?>
                                        <input type="radio" name="movimiento" value="1" onchange="javaScript:cambio(1);" title="Seleccione si tiene movimiento o no." /> Sí&nbsp &nbsp
                                        <input type="radio" name="movimiento" value="2" onchange="javaScript:cambio(2);" checked="checked" title="Seleccione si tiene movimiento o no." /> No
                                    <?php } ?>
                                </div>
                                <br/>
                            </div>
                            <div class="form-group" style="margin-top: -20px; ">
                                <label for="sigla" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Sigla:</label>
                                <input type="text" name="sigla" id="sigla" class="form-control" maxlength="10" title="Ingrese la sigla" onkeypress="return txtValida(event,'num_car')"  value="<?php echo utf8_encode($row[3]); ?>" placeholder="Sigla" required> 
                            </div>
                            <div class="form-group" style="margin-top: -20px; ">
                                <label for="tipoCentCost" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Centro Costo:</label>
                                <select name="tipoCentCost" id="tipoCentCost" class="select2_single form-control" title="Ingrese el tipo de centro de costo" required>
                                    <option value="<?php echo $row[4]; ?>"><?php echo ucwords((mb_strtolower($row[5]))); ?></option>
                                    <?php while ($rowTCC = mysqli_fetch_row($tipoCentCost)) {  ?>
                                        <option value="<?php echo $rowTCC[0]; ?>"><?php echo ucwords((mb_strtolower($rowTCC[1]))); ?></option>
                                        <?php }
                                        ?>
                                </select> 
                            </div>
                            <div class="form-group" style="margin-top: -10px; ">
                                <label for="predecesor" class="col-sm-5 control-label">Predecesor:</label>
                                <select name="predecesor" id="predecesor" class="select2_single form-control" title="Ingrese el predecesor" >
                                    <option value="<?php echo $row[6]; ?>">
                                    <?php
                                    if (($row[6] == '') || ($row[6] == 0))
                                        echo "No hay predecesor";
                                    else
                                        echo ucwords((mb_strtolower($row[7])));
                                    ?>
                                    </option>
                                        <?php while ($rowP = mysqli_fetch_row($predecesor)) { ?>
                                        <option value="<?php echo $rowP[0]; ?>"><?php echo ucwords((mb_strtolower($rowP[1]))); ?></option>
                                            <?php }?>
                                </select> 
                            </div>
                            <div class="form-group" style="margin-top: -10px; ">
                                <label for="claseServ" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Servicio:</label>
                                <select name="claseServ" id="claseServ" class="select2_single form-control" title="Ingrese la clase de servicio" required>
                                    <option value="<?php echo $row[8]; ?>"><?php echo ucwords((mb_strtolower($row[9]))); ?></option>
                                        <?php while ($rowCS = mysqli_fetch_row($claseServ)) {    ?>
                                        <option value="<?php echo $rowCS[0]; ?>"><?php echo ucwords((mb_strtolower($rowCS[1]))); ?></option>
                                            <?php } ?>
                                </select> 
                            </div>
                            <div class="form-group" id="divcantidad" 
                                <?php if($row[2]==1){?>
                                 style="display: block; margin-top: -10px; "
                                <?php } else { ?>
                                  style="display: none; margin-top: -10px; "
                                <?php } ?> >
                                <label for="cantidad" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Cantidad Distribución:</label>
                                <input type="text" name="cantidad" id="cantidad" class="form-control" value="<?php echo $row[10];?>"  title="Ingrese la cantidad" onkeypress="return txtValida(event,'num');"  placeholder="Cantidad Distribución">
                            </div>
                            <?php if($row[2]==1){$row[10];}?>
                            <div align="center">
                                <button type="submit" class="btn btn-primary sombra" >Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                </div> <!-- Cierre col-sm-7 text-left -->
                <!-- Botones de consulta -->
                <div class="col-sm-7 col-sm-3">
                    <table class="tablaC table-condensed" style="margin-left: -10px">
                        <thead>
                        <th>
                            <h2 class="titulo" align="center">Consultas</h2>
                        </th>
                        <th>
                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                        </th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="btnConsultas">
                                        <a href="#">
                                            MOVIMIENTO CONTABLE 
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-primary btnInfo">CLASE SERVICIO</button> 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="btnConsultas">
                                        <a href="#"> 
                                            MOVIMIENTO PRESUPUESTAL 
                                        </a>
                                    </div>
                                </td>
                                <td>

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="btnConsultas">
                                        <a href="#"> 
                                            MOVIMIENTO<br/>ALMACÉN 
                                        </a>
                                    </div>
                                </td>
                                <td>

                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div> <!-- Cierre row content -->
        </div> <!-- Cierre container-fluid text-center -->
       <br/>
        <?php require_once 'footer.php'; ?>
        <script src="js/select/select2.full.js"></script>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function() {
              $(".select2_single").select2({
                allowClear: true
              });
            });
        </script>
        <script>
            function cambio(valor){
                console.log(valor);
                if(valor==1){
                    $("#divcantidad").css('display', 'block');
                } else {
                    console.log('asc');
                    $("#divcantidad").css('display', 'none');
                    $("#cantidad").val('');
                }
            }
        </script>
    </body>
</html>