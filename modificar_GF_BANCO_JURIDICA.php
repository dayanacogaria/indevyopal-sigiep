<?php 
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#31/07/2018 |Erica G. | Correo Electrónico - Arreglar Código
#######################################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$id_bancoJur = " ";
$id_bancoJur = (($_GET["id_bancoJur"]));
$queryBancoJur ="SELECT t.Id_Unico, 
    t.RazonSocial, 
    t.NumeroIdentificacion,
    t.DigitoVerficacion, 
    ti.Id_Unico, 
    ti.Nombre, 
    s.Id_Unico, 
    s.nombre, 
    t.RepresentanteLegal, 
    ci.Id_Unico, 
    ci.Nombre, 
    tr.Id_Unico, 
    tr.Nombre, 
    t.Contacto,
    te.Id_Unico, 
    te.Nombre, 
    tipen.Id_Unico, 
    tipen.Nombre, 
    ci.Departamento,
    z.Id_Unico, 
    z.Nombre,
    DP.Id_Unico,
    DP.Nombre, 
    t.email, 
    ci.id_unico,
    DP.id_unico, 
    DP.nombre, ci.nombre  
FROM gf_tercero t
LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico
LEFT JOIN gf_sucursal s ON t.Sucursal = s.Id_Unico
LEFT JOIN gf_tipo_regimen tr ON t.TipoRegimen = tr.Id_Unico
LEFT JOIN gf_tipo_empresa te ON t.TipoEmpresa = te.Id_Unico
LEFT JOIN gf_tipo_entidad tipen ON t.TipoEntidad = tipen.Id_Unico
LEFT JOIN gf_ciudad ci ON t.CiudadIdentificacion = ci.Id_Unico
LEFT JOIN gf_zona z ON t.Zona = z.Id_Unico
LEFT JOIN gf_departamento DP ON ci.departamento = DP.Id_Unico
WHERE md5(t.Id_Unico) = '$id_bancoJur'";

$resultado = $mysqli->query($queryBancoJur);
$row = mysqli_fetch_row($resultado);
$_SESSION['id_tercero'] = $row[0];
$_SESSION['perfil'] = "J"; //Jurídica.
$_SESSION['url'] = "modificar_GF_BANCO_JURIDICA.php?id_bancoJur=".(($_GET["id_bancoJur"]));
$_SESSION['tipo_perfil']='Banco jurídica';

#****** Tipo Identificación *********#
$sqlTipoIden = "SELECT Id_Unico, Nombre 
FROM gf_tipo_identificacion
WHERE Id_Unico != $row[4]
ORDER BY Nombre ASC";
$tipoIden = $mysqli->query($sqlTipoIden);

#****** Sucursal *********#
if(empty($row[6])){ $ids = 0;} else { $ids = $row[6];}
$sqlSucursal = "SELECT Id_Unico, Nombre 
FROM gf_sucursal
WHERE Id_Unico != $ids 
ORDER BY Nombre ASC";
$sucursal = $mysqli->query($sqlSucursal);

#****** Tipo Régimen *********#
if(empty($row[11])){ $idtr = 0;} else { $idtr = $row[11];}
$sqlTipoReg = "SELECT Id_Unico, Nombre 
FROM gf_tipo_regimen
WHERE Id_Unico != $idtr 
ORDER BY Nombre ASC";
$tipoReg = $mysqli->query($sqlTipoReg);

#****** Tipo Empresa *********#
if(empty($row[14])){ $idemp = 0;} else { $idemp = $row[14];}
$sqlTipoEmp = "SELECT Id_Unico, Nombre 
FROM gf_tipo_empresa
WHERE Id_Unico != $idemp 
ORDER BY Nombre ASC";
$tipoEmp = $mysqli->query($sqlTipoEmp);

#****** Tipo Entidad *********#
if(empty($row[16])){ $idte = 0;} else { $idte = $row[16];}
$sqlTipoEnt = "SELECT Id_Unico, Nombre 
FROM gf_tipo_entidad
WHERE Id_Unico != $idte 
ORDER BY Nombre ASC";
$tipoEnt = $mysqli->query($sqlTipoEnt);

#****** Representante Legal *********#
if(empty($row[8])){ $idrl = 0;} else { $idrl = $row[8];}
$sqlReprLeg = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt  
WHERE t.TipoIdentificacion = ti.Id_Unico
AND t.Id_Unico = pt.Tercero 
AND pt.Perfil = 10 
AND t.Id_Unico != $idrl 
ORDER BY t.NombreUno ASC";
$repreLegal = $mysqli->query($sqlReprLeg);

#****** Contacto *********#
if(empty($row[13])){ $idcn = 0;} else { $idcn = $row[13];}
$sqlContacto = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt     
WHERE t.TipoIdentificacion = ti.Id_Unico 
AND t.Id_Unico = pt.Tercero 
AND pt.Perfil = 10
AND t.Id_Unico != $idcn 
ORDER BY t.NombreUno ASC";
$contacto = $mysqli->query($sqlContacto);

#****** Zona *********#
if(empty($row[19])){ $idz = 0;} else { $idz = $row[19];}
$sqlZona = "SELECT Id_Unico, Nombre 
FROM gf_zona
WHERE Id_Unico != $idz 
ORDER BY Nombre ASC";
$zona = $mysqli->query($sqlZona);
#****** Departamento  *********#
if(empty($row[25])){ $de = 0; } else { $de = $row[25]; }
$sqlDep = "SELECT Id_Unico, Nombre 
  FROM gf_departamento  
  WHERE id_unico != $de 
  ORDER BY Nombre ASC";
$dep = $mysqli->query($sqlDep);
#****** Ciudad  *********#
if(empty($row[24]) && empty($row[25])){ $de = 0;$cd = 0; } else { $de = $row[25];$cd = $row[24]; }
$sqlCiu = "SELECT c.id_unico, c.nombre 
  FROM gf_ciudad c   
  LEFT JOIN gf_departamento d ON c.departamento = d.id_unico 
  WHERE c.id_unico != $cd AND c.departamento = $de  
  ORDER BY c.nombre ASC";
$ciu= $mysqli->query($sqlCiu);
?>

<!-- Script para calcular el dígito de verificación. -->
<script type="text/javascript">
    function CalcularDv()
{ 
 var arreglo, x, y, z, i, nit1, dv1;
 nit1=document.form.noIdent.value;
  if (isNaN(nit1))
  {
  document.form.digitVerif.value="X";
      alert('Número del Nit no valido, ingrese un número sin puntos, ni comas, ni guiones, ni espacios');   
  } else {
  arreglo = new Array(16); 
  x=0 ; y=0 ; z=nit1.length ;
  arreglo[1]=3;   arreglo[2]=7;   arreglo[3]=13; 
  arreglo[4]=17;  arreglo[5]=19;  arreglo[6]=23;
  arreglo[7]=29;  arreglo[8]=37;  arreglo[9]=41;
  arreglo[10]=43; arreglo[11]=47; arreglo[12]=53;  
  arreglo[13]=59; arreglo[14]=67; arreglo[15]=71;
  for(i=0 ; i<z ; i++)
  { 
   y=(nit1.substr(i,1));
     x+=(y*arreglo[z-i]);
  } 
  y=x%11
  if (y > 1){ dv1=11-y; } else { dv1=y; }
  document.form.digitVerif.value=dv1;
  }
}
</script>
    <title>Modificar Banco Jurídica</title>
    <link href="css/select/select2.min.css" rel="stylesheet">
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <style>
        label #tipoIdent-error,#razoSoci-error,#tipoEntidad-error,#tipoReg-error, #tipoEmp-error, #depto-error, #ciudad-error, #correo-error{
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
            <div class="col-sm-7 text-left" style="margin-left:-16px;margin-top: -20px"> 
                <h2 align="center" class="tituloform">Modificar Banco Jurídica</h2>
                <a href="listar_GF_BANCO_JURIDICA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"> <?php echo ($row[1]); ?></h5>
                <div class="client-form contenedorForma" >
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarBancoJurJson.php">
                        <p align="center" class="parrafoO" style="margin-bottom: -1px" >Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" value="<?php echo $row[0]; ?>">
                        <div class="form-group form-inline " style="">
                            <label for="tipoIdent" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                            <div class="form-group form-inline col-sm-3" style="margin-left:-10px">
                                <select name="tipoIdent" id="tipoIdent" class="select2_single form-control col-sm-5" style="height: 33px;width:150px" title="Tipo Identificación" required>
                                    <option value="<?php echo $row[4]; ?>"><?php echo $row[5]; ?></option>
                                    <?php while ($ma = mysqli_fetch_assoc($tipoIden)) { ?>
                                    <option value="<?php echo $ma["Id_Unico"]; ?>">
                                    <?php echo ucwords((mb_strtolower($ma["Nombre"]))); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group form-inline col-sm-3" style="">
                                <input type="text" name="noIdent" id="noIdent" class="form-control col-sm-5" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event, 'num')" placeholder="Número" style="width:95px" style="height: 30px" required onblur="CalcularDv(); " value="<?php echo $row[2]; ?>" />
                                <span class="col-sm-1" style="width:1px; margin-top:8px;"><strong> - </strong></span>
                                <input type="text" name="digitVerif" id="digitVerif" class="form-control " style="width:30px" maxlength="1" placeholder="0" title="Dígito de verificación" onkeypress="return txtValida(event, 'num')" placeholder="" readonly="" style="height: 30px" value="<?php echo $row[3]; ?>"/>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -22px; ">
                            <label for="sucursal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Sucursal:</label>
                            <select name="sucursal" id="sucursal" class="select2_single form-control" title="Seleccione Sucursal" >
                                <?php
                                if (!empty($row[6])) {
                                    echo "<option value='" . $row[6] . "'>" . (ucwords(mb_strtolower($row[7]))) . "</option>";
                                    echo '<option value=""> - </option>';
                                } else {
                                    echo '<option value=""> - </option>';
                                } 
                                while ($rowS = mysqli_fetch_row($sucursal)) { ?>
                                <option value="<?php echo $rowS[0] ?>"><?php echo ucwords((mb_strtolower($rowS[1]))); ?></option>
                                <?php } ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="razoSoci" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Razón Social:</label>
                            <input type="text" name="razoSoci" id="razoSoci" class="form-control" maxlength="500" title="Ingrese la razón social" value="<?php echo ($row[1]); ?>"  onkeypress="return txtValida(event)" onkeyup="javascript:this.value = this.value.toUpperCase();" placeholder="Razón Social" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px; ">
                            <label for="tipoReg" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Régimen:</label>
                            <select name="tipoReg" id="tipoReg" class="select2_single form-control" title="Ingrese el tipo de régimen" >
                                <?php 
                                if (!empty($row[11])) {
                                    echo "<option value='" . $row[11] . "'>" . (ucwords(mb_strtolower($row[12]))) . "</option>";
                                } else {
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowTR = mysqli_fetch_row($tipoReg)) {
                                    echo "<option value='" . $rowTR[0] . "'>" . (ucwords(mb_strtolower($rowTR[1]))) . "</option>";
                                } ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="tipoEmp" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Empresa:</label>
                            <select name="tipoEmp" id="tipoEmp" class="select2_single form-control" title="Ingrese el tipo de empresa" >
                                <?php 
                                if (!empty($row[14])) {
                                    echo "<option value='" . $row[14] . "'>" . ucwords(mb_strtolower($row[15])) . "</option>";
                                } else {
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowTE = mysqli_fetch_row($tipoEmp)) {
                                    echo "<option value='" . $rowTE[0] . "'>" . ucwords((mb_strtolower($rowTE[1]))) . "</option>";
                                }?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="tipoEntidad" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Entidad:</label>
                            <select name="tipoEntidad" id="tipoEntidad" class="select2_single form-control" title="Ingrese el tipo  Entidad" >
                                <?php
                                if (!empty($row[16])) {
                                    echo "<option value='" . $row[16] . "'>" . ucwords(mb_strtolower($row[17])) . "</option>";
                                } else {
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowTEn = mysqli_fetch_row($tipoEnt)) {
                                    echo "<option value='" . $rowTEn[0] . "'>" . ucwords(mb_strtolower($rowTEn[1])) . "</option>";
                                }
                                ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="repreLegal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Representante Legal:</label>
                            <select name="repreLegal" id="repreLegal" class="select2_single form-control" title="Ingrese el representante legal">
                                <?php 
                                if (!empty($row[8])) {
                                    $sqlElReprLeg = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                                    FROM gf_tercero t, gf_tipo_identificacion ti  
                                    WHERE t.TipoIdentificacion = ti.Id_Unico
                                    AND t.Id_Unico = $row[8]";
                                    $elReprLeg = $mysqli->query($sqlElReprLeg);
                                    $rowElReprLeg = mysqli_fetch_row($elReprLeg);
                                    echo "<option value='" . $rowElReprLeg[0] . "'>" . ucwords((mb_strtolower($rowElReprLeg[1] . " " . $rowElReprLeg[2] . " " . $rowElReprLeg[3] . " " . $rowElReprLeg[4] . " (" . $rowElReprLeg[6] . ", " . $rowElReprLeg[5] . ")"))) . "</option>";
                                    echo '<option value=""> - </option>';
                                } else {
                                    echo '<option value=""> - </option>';                                    
                                }
                                while ($rowRL = mysqli_fetch_row($repreLegal)) {
                                    echo "<option value='" . $rowRL[0] . "'>" . ucwords((mb_strtolower($rowRL[1] . " " . $rowRL[2] . " " . $rowRL[3] . " " . $rowRL[4] . " (" . $rowRL[6] . " - " . $rowRL[5] . ")"))) . "</option>";
                                }
                                ?>
                            </select> 
                        </div>
                        <div class="form-group form-inline" style="margin-top: -10px">
                            <label for="depto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ubicación:</label>     
                            <div class="form-group form-inline col-sm-3"  style="margin-left:-10px">
                                <select name="depto" id="depto" class="select2_single form-control col-sm-5" style="height: 20%;width:170px" title="Seleccione Departamento" required>
                                    <?php
                                    if (!empty($row[25])) {
                                        echo '<option value="' . $row[25] . '">' . ucwords(mb_strtolower($row[26])). '</option>';
                                    } else {
                                        echo '<option value=""> - </option>';
                                    }?>
                                    <?php while ($rowD = mysqli_fetch_row($dep)) {?>
                                    <option value="<?php echo $rowD[0] ?>"><?php echo ucwords((mb_strtolower($rowD[1]))); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group form-inline col-sm-1" style=""></div>
                            <div class="form-group form-inline col-sm-3" style="">
                                <select name="ciudad" style="height: 24%;width:100px" id="ciudad" class="select2_single form-control" title="Seleccione Ciudad" required>
                                    <?php
                                    if (!empty($row[24])) {
                                        echo '<option value="' . $row[24] . '">' . ucwords(mb_strtolower($row[27])). '</option>';
                                    } else {
                                        echo '<option value=""> - </option>';
                                    }?>
                                    <?php while ($rowC = mysqli_fetch_row($ciu)) {?>
                                    <option value="<?php echo $rowC[0] ?>"><?php echo ucwords((mb_strtolower($rowC[1]))); ?></option>
                                    <?php } ?>
                                </select>
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        $("#depto").change(function () {
                                            var form_data = {
                                                is_ajax: 1,
                                                id_depto: +$("#depto").val()
                                            };
                                            $.ajax({
                                                type: "POST",
                                                url: "Ciudad.php",
                                                data: form_data,
                                                success: function (response) {
                                                    $('#ciudad').html(response).fadeIn();
                                                    $('#ciudad').select2();
                                                }
                                            });
                                        });
                                    });
                                </script>
                                <label for="ciudad" class=""></label>     
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="contacto" class="col-sm-5 control-label">Contacto:</label>
                            <select name="contacto" id="contacto" class="select2_single form-control" title="Ingrese el contacto">
                                <?php 
                                if (!empty($row[13])) {
                                    $sqlElContacto = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                                    FROM gf_tercero t, gf_tipo_identificacion ti  
                                    WHERE t.TipoIdentificacion = ti.Id_Unico
                                    AND t.Id_Unico = $row[13]";
                                    $elContacto = $mysqli->query($sqlElContacto);
                                    $rowElCon = mysqli_fetch_row($elContacto);
                                    echo '<option value="' . $row[13] . '">' . ucwords(mb_strtolower($rowElCon[1] . " " . $rowElCon[2] . " " . $rowElCon[3] . " " . $rowElCon[4] . " (" . $rowElCon[6] . ", " . $rowElCon[5] . ")")) . '</option>';
                                    echo '<option value=""> - </option>';
                                } else {
                                    echo '<option value=""> - </option>';
                                    
                                }
                                while ($cont = mysqli_fetch_row($contacto)) {
                                    echo '<option value="' . $cont[0] . '">' . ucwords(mb_strtolower($cont[1] . ' ' . $cont[2] . ' ' . $cont[3] . ' ' . $cont[4] )). '(' . $cont[5] . ' - ' . $cont[6] . ')' . '</option>';
                                }
                                ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="zona" class="col-sm-5 control-label">Zona:</label>
                            <select name="zona" id="zona" class="select2_single form-control" title="Ingrese la zona">
                                <?php 
                                if (!empty($row[19])) {
                                    echo '<option value="' .$row[19]. '">' .$row[20]. '</option>';
                                    echo '<option value=""> - </option>';
                                } else {
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowZ = mysqli_fetch_row($zona)) {
                                    echo '<option value="' .$rowZ[0]. '">' .$rowZ[1]. '</option>';
                                }
                                ?>
                          </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="correo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Correo Electrónico:</label>
                            <input type="email" name="correo" id="correo" class="form-control" maxlength="500" title="Ingrese Correo Electrónico" placeholder="Corrreo Electrónico" value="<?php echo $row[23]?>">
                        </div>
                        <div class="form-group" style="margin-top:-5px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -20px; margin-bottom: -10px; margin-left: 0px;">Guardar</button>
                        </div>


                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>      
            </div> <!-- Cierra clase col-sm-7 text-left -->

            <!-- Botones de consulta -->
            <div class="col-sm-7 col-sm-3" style="margin-top:-22px">
                <table class="tablaC table-condensed" style="margin-left: -30px">
                    <thead>
                    <th>
                        <h2></h2>
                    </th>
                    <th>
                        <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                    </th>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_DIRECCION_TERCERO.php"><button class="btn btn-primary btnInfo">DIRECCIÓN</button></a>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_TIPO_ACTIVIDAD_TERCERO.php"><button class="btn btnInfo btn-primary">TIPO ACTIVIDAD</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_TELEFONO.php"><button class="btn btn-primary btnInfo">TELEFONO</button></a>
                                <br/>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_CONDICION_TERCERO.php"><button class="btn btn-primary btnInfo">CONDICIÓN</button></a>
                                <br/>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="registrar_TERCERO_CONTACTO_NATURAL.php" class="btn btnInfo btn-primary">CONTACTO</a>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_PERFIL_CONDICION.php" class="btn btnInfo btn-primary">PERFIL CONDICIÓN</a>
                            </td>
                        </tr>
                        <tr>
                            <td><br/></td>
                            <td>
                                <a href="GF_TERCERO_RETENCION.php"><button style="margin-top:15px" class="btn btnInfo btn-primary">RETENCIÓN</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td><br/></td>
                            <td>
                                <a href="GF_TERCERO_INGRESOS.php"><button style="margin-top:15px" class="btn btnInfo btn-primary">INGRESOS</button></a><br/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Fin de Contenedor Principal -->
    <?php require_once('footer.php'); ?>
    <script src="js/select/select2.full.js"></script>
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">
            <script src="js/bootstrap.min.js"></script>
            <script>
              $(document).ready(function () {
                  $(".select2_single").select2({
                      allowClear: true
                  });
              });
            </script>
</body>
</html>