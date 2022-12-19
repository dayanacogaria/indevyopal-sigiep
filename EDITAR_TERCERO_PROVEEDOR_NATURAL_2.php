<?php 
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#02/08/2018 |Erica G. | Correo Electrónico - Arreglar Código
#######################################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$id_asoNat = "";
$queryAsociadoNat="";
if (isset($_GET["id"])){ 
    $id_asoNat = (($_GET["id"]));
    $queryAsociadoNat = "SELECT T.Id_Unico,
             TI.Id_Unico, 
             TI.Nombre,
             T.NumeroIdentificacion,
             T.NombreUno,
             T.NombreDos,
             T.ApellidoUno,
             T.ApellidoDos,         
             TR.Id_Unico, 
             TR.Nombre, 
             T.email, 
             z.id_unico, 
             z.nombre, 
             T.tarjeta_profesional,
             T.procedencia,
             md5(T.Id_Unico)
    FROM gf_tercero T
    LEFT JOIN gf_tipo_identificacion TI  ON T.TipoIdentificacion = TI.Id_Unico
    LEFT JOIN gf_tipo_regimen TR  ON T.TipoRegimen = TR.Id_Unico 
    LEFT JOIN gf_zona z ON T.zona = z.id_unico 
    WHERE md5(T.Id_Unico) = '$id_asoNat'";
}
$resultado = $mysqli->query($queryAsociadoNat);
$row = mysqli_fetch_row($resultado);
$_SESSION['id_tercero'] = $row[0];
$_SESSION['perfil'] = "N"; //Natural.
$_SESSION['url'] = "EDITAR_TERCERO_PROVEEDOR_NATURAL_2.php?id=".(($_GET["id"]));
$_SESSION['tipo_perfil']='Asociado natural';
#****** Tipo Identificación *********#
$idt = 0;
if(!empty($row[1])){$idt=$row[1];}
$idents = "SELECT Id_Unico, Nombre FROM gf_tipo_identificacion 
    WHERE Id_Unico != $idt  ORDER BY Nombre ASC";
$ident =   $mysqli->query($idents);
#****** Tipo Régimen  *********#
$idtr = 0;
if(!empty($row[8])){$idtr=$row[8];}
$regimenes = "SELECT Id_Unico, Nombre 
    FROM gf_tipo_regimen WHERE Id_Unico !=$idtr ORDER BY Nombre ASC";
$regimen = $mysqli->query($regimenes);
?>
    <title>Modificar Proveedor Natural</title>
    <link href="css/select/select2.min.css" rel="stylesheet">
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <style>
        label #tipoI-error,#numId-error,#correo-error, #primerN-error, #primerA-error{
            display: block;
            color: #bd081c;
            font-weight: bold;
            font-style: italic;
        }
    </style>
    <script>
        $().ready(function () {
            var validator = $("#form").validate({
                ignore: "",
                errorPlacement: function (error, element) {
                    $(element)
                        .closest("form")
                        .find("label[for='" + element.attr("id") + "']")
                        .append(error);
                },
            });
            $(".cancel").click(function () {
                validator.resetForm();
            });
        });
    </script>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="content row">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top:-20px"> 
                <h2 align="center" class="tituloform">Modificar Proveedor Natural</h2>
                <a href="LISTAR_TERCERO_PROVEEDOR_NATURAL_2.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo $row[4] . ' ' . $row[6] ?></h5>
                <div class="client-form contenedorForma">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTerceroProveedorNatural2Json.php">
                        <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" value="<?php echo $row[0 ]; ?>"/>
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="tipoI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Identificación:</label>
                            <select name="tipoI" id="tipoI" class="select2_single form-control" title="Seleccione el tipo identificación" required>
                                <option value="<?php echo $row[1]; ?>"><?php echo ($row[2]); ?></option>
                                <?php while ($rowI = mysqli_fetch_assoc($ident)) { ?>
                                    <option value="<?php echo $rowI["Id_Unico"] ?>"><?php echo ucwords((mb_strtolower($rowI["Nombre"])));
                                } ?></option>;
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="numId" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                            <input type="number" name="numId" id="numId" class="form-control col-sm-5" maxlength="20" title="Ingrese el número identificación" onkeypress="return txtValida(event,'num')" value="<?php echo $row[3]; ?>" placeholder="Número identificación"  required/>
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="primerN" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Primer Nombre:</label>
                            <input type="text" name="primerN" id="primerN" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese primer nombre" onkeypress="return txtValida(event,'car')" value="<?php echo $row[4]; ?>"   placeholder="Primer Nombre" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="segundoN" class="col-sm-5 control-label">Segundo Nombre:</label>
                            <input type="text" name="segundoN" id="segundoN" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese segundo nombre" onkeypress="return txtValida(event,'car')" value="<?php echo $row[5]; ?>"  placeholder="Segundo Nombre">
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="primerA" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Primer Apellido:</label>
                            <input type="text" name="primerA" id="primerA" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese primer apellido" onkeypress="return txtValida(event,'car')" value="<?php echo $row[6]; ?>"  placeholder="Primer Apellido" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="segundoA" class="col-sm-5 control-label">Segundo Apellido:</label>
                            <input type="text" name="segundoA" id="segundoA" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese segundo apellido" onkeypress="return txtValida(event,'car')" value="<?php echo $row[7]; ?>"  placeholder="Segundo Apellido">
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="regimen" class="col-sm-5 control-label">Tipo Régimen:</label>
                            <select name="regimen" id="regimen" class="select2_single form-control" title="Seleccione el tipo régimen">
                                <?php
                                if (!empty($row[8])) {
                                    echo '<option value="' . $row[8] . '">' . ucwords((mb_strtolower($row[9]))) . '</option>';
                                    echo '<option value=""> - </option>';
                                } else {
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowR = mysqli_fetch_assoc($regimen)) {
                                        echo '<option value="' . $rowR['Id_Unico'] . '">' . ucwords((mb_strtolower($rowR['Nombre']))) . '</option>';
                                    }
                                ?>                                                
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="correo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Correo Electrónico:</label>
                            <input type="email" name="correo" id="correo" class="form-control" maxlength="500" title="Ingrese Correo Electrónico" placeholder="Corrreo Electrónico" value="<?php echo $row[10]?>" >
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="procedencia" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Procedencia:</label>
                            <select name="procedencia" id="procedencia" class="select2_single form-control" title="Ingrese Procedencia" >
                            <?php
                                if(!empty($row[14])){
                                    echo '<option value="'.$row[14].'">'.ucwords(mb_strtolower($row[14])).'</option>';
                                    echo '<option value="Residente">Residente</option>';
                                    echo '<option value="No residente">No residente</option>';
                                    echo '<option value=""> - </option>';
                                } else { 
                                    echo '<option value=""> - </option>';
                                    echo '<option value="Residente">Residente</option>';
                                    echo '<option value="No residente">No residente</option>';
                                }
                            
                         
                            ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -80px; margin-bottom: -10px; margin-left: 650px;">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>				
            </div>
            <div class="col-sm-7 col-sm-3" style="margin-top:-22px">
                <table class="tablaC table-condensed" style="margin-left: -30px">
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
                                <a href="GF_DIRECCION_TERCERO.php"><button class="btn btn-primary btnInfo" style="margin-bottom:10px">DIRECCIÓN</button></a>
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
                             <a href="GF_CUENTA_BANCARIA_TERCERO.php"><button class="btn btnInfo btn-primary">CUENTA BANCARIA</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#"> 
                                        MOVIMIENTO<br/>ALMACEN
                                    </a>
                                </div>
                            </td>
                            <td>                     
                             <a href="GF_TIPO_ACTIVIDAD_TERCERO.php"><button class="btn btnInfo btn-primary">TIPO ACTIVIDAD</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#"> 
                                        TAREAS DE MANTENIMIENTO 
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="GF_TELEFONO.php"><button class="btn btn-primary btnInfo">TELÉFONO</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#"> 
                                        RETENCIONES EFECTUADAS
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="GF_CONDICION_TERCERO.php"><button class="btn btn-primary btnInfo">CONDICIÓN</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_PERFIL_CONDICION.php"><button class="btn btn-primary btnInfo">PERFIL CONDICIÓN</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_RESPONSABILIDAD_TERCERO.php?id=<?php echo $row[15];?>"><button style="margin-top:15px" class="btn btnInfo btn-primary">RESPONSABILIDADES</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_TERCERO_RETENCION.php"><button style="margin-top:15px" class="btn btnInfo btn-primary">RETENCIÓN</button></a>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_TERCERO_INGRESOS.php"><button style="margin-top:15px" class="btn btnInfo btn-primary">INGRESOS</button></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php require_once('footer.php'); ?>
        </div>
        <script src="js/select/select2.full.js"></script>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/md5.js"></script>
        <script>
          $(document).ready(function () {
              $(".select2_single").select2({
                  allowClear: true
              });
          });
        </script>
    </div>
    </body>
</html>
