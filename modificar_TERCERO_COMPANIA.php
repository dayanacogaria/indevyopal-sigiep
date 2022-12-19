<?php 
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#31/07/2018 |Erica G. | Correo Electrónico - Arreglar Código 
#21/09/2017 |Erica G. | Agregar Campo Tipo Compañia (1-Pública, 2- Privada)
#######################################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php';
$id_ter_comp = " ";
if (isset($_GET["id_ter_comp"])) {
    $id_ter_comp = (($_GET["id_ter_comp"]));
}
/*Consulta general*/
$queryTerceroComp = "SELECT 
    t.id_unico , 
    t.razonsocial , 
    t.numeroidentificacion , 
    t.digitoverficacion , 
    ti.nombre, 
    s.nombre, 
    t.representantelegal, 
    ci.nombre, 
    tr.nombre, 
    t.contacto , 
    te.nombre, 
    tipen.nombre, 
    t.codigo_dane , 
    t.tipo_compania, 
    IF(CONCAT_WS(' ',
     trl.nombreuno,
     trl.nombredos,
     trl.apellidouno,
     trl.apellidodos) 
     IS NULL OR CONCAT_WS(' ',
     trl.nombreuno,
     trl.nombredos,
     trl.apellidouno,
     trl.apellidodos) = '',
     (trl.razonsocial),
     CONCAT_WS(' ',
     trl.nombreuno,
     trl.nombredos,
     trl.apellidouno,
     trl.apellidodos)) AS repLeg, 
     IF(CONCAT_WS(' ',
     trc.nombreuno,
     trc.nombredos,
     trc.apellidouno,
     trc.apellidodos) 
     IS NULL OR CONCAT_WS(' ',
     trc.nombreuno,
     trc.nombredos,
     trc.apellidouno,
     trc.apellidodos) = '',
     (trc.razonsocial),
     CONCAT_WS(' ',
     trc.nombreuno,
     trc.nombredos,
     trc.apellidouno,
     trc.apellidodos)) AS contac , ti.id_unico , t.sucursal , 
     t.tiporegimen , t.tipoempresa, t.tipoentidad, t.ciudadidentificacion , 
     d.id_unico, d.nombre, 
     t.email, 
     ci.id_unico, ci.nombre,
     d.id_unico, d.nombre, 
     t.distribucion_costos  
  FROM gf_tercero t
  LEFT JOIN gf_tipo_identificacion ti on t.tipoidentificacion = ti.id_unico
  LEFT JOIN gf_sucursal s ON t.sucursal = s.id_unico
  LEFT JOIN gf_tipo_regimen tr ON t.tiporegimen = tr.id_unico
  LEFT JOIN gf_tipo_empresa te ON t.tipoempresa = te.id_unico
  LEFT JOIN gf_tipo_entidad tipen ON t.tipoentidad = tipen.id_unico
  LEFT JOIN gf_ciudad ci ON t.ciudadidentificacion = ci.id_unico 
  LEFT JOIN gf_departamento d ON ci.departamento = d.id_unico 
  LEFT JOIN gf_tercero trl ON trl.id_unico = t.representantelegal 
  LEFT JOIN gf_tercero trc ON trc.id_unico = t.contacto 
  LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero 
  LEFT JOIN gf_perfil p ON pt.perfil = p.id_unico 
  WHERE md5(t.id_unico) = '$id_ter_comp'";

$resultado = $mysqli->query($queryTerceroComp);
$row = mysqli_fetch_row($resultado);

$_SESSION['id_tercero'] = $row[0];
$_SESSION['perfil'] = "J"; //Jurídica.
$_SESSION['url'] = "modificar_TERCERO_COMPANIA.php?id_ter_comp=" . (($_GET["id_ter_comp"]));
$_SESSION['tipo_perfil'] = 'Compañía';

#****** Tipo Identificación  *********#
$sqlTipoIden = "SELECT DISTINCT id_Unico, nombre 
  FROM gf_tipo_identificacion
  WHERE id_unico != '$row[16]' 
  ORDER BY nombre ASC";
$tipoIden = $mysqli->query($sqlTipoIden);

#****** Sucursal *********#
if(empty($row[17])){ $ids = 0;} else { $ids = $row[17];}
$sqlSucursal = "SELECT DISTINCT id_unico, nombre 
  FROM gf_sucursal
  WHERE id_unico != $ids 
  ORDER BY nombre ASC";
$sucursal = $mysqli->query($sqlSucursal);

#****** Tipo Régimen *********#
if(empty($row[18])){ $idtr = 0;} else { $idtr = $row[18];}
$sqlTipoReg = "SELECT DISTINCT id_unico, nombre 
  FROM gf_tipo_regimen
  WHERE id_unico != $idtr
  ORDER BY nombre ASC";
$tipoReg = $mysqli->query($sqlTipoReg);

#****** Tipo Empresa *********#
if (empty($row[10])) {
    $sqlTipoEmp = "SELECT DISTINCT id_unico, nombre 
  FROM gf_tipo_empresa 
  ORDER BY nombre ASC";
    $tipoEmp = $mysqli->query($sqlTipoEmp);
} else {
    $sqlTipoEmp = "SELECT DISTINCT id_unico, nombre 
  FROM gf_tipo_empresa
  WHERE id_unico != '$row[19]'
  ORDER BY nombre ASC";
    $tipoEmp = $mysqli->query($sqlTipoEmp);
}
#****** Tipo Entidad *********#

if (empty($row[11])) {
    //Tipo Entidad.
    $sqlTipoEnt = "SELECT DISTINCT id_unico, nombre 
  FROM gf_tipo_entidad 
  ORDER BY nombre ASC";
    $tipoEnt = $mysqli->query($sqlTipoEnt);
} else {
    $sqlTipoEnt = "SELECT DISTINCT id_unico, nombre 
  FROM gf_tipo_entidad
  WHERE id_unico != '$row[20]'
  ORDER BY nombre ASC";
    $tipoEnt = $mysqli->query($sqlTipoEnt);
}
#****** Representante Legal *********#
if (empty($row[6])) { $idrl = 0; } else { $idrl = $row[6];}
    $sqlReprLeg = "SELECT DISTINCT 
    t.id_unico, 
    IF(CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) 
     IS NULL OR CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) = '',
     (t.razonsocial),
     CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos)) AS NOMBRE, t.NumeroIdentificacion, ti.nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt  
  WHERE t.TipoIdentificacion = ti.id_unico
  AND t.id_unico = pt.Tercero 
  AND pt.Perfil = 10 AND t.id_unico != $idrl  
  ORDER BY NOMBRE ASC";
  $repreLegal = $mysqli->query($sqlReprLeg);

#****** Contacto *********#
if (empty($row[9])) {
    $sqlContacto = "SELECT DISTINCT 
        t.id_unico, 
IF(CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) 
     IS NULL OR CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) = '',
     (t.razonsocial),
     CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos)) AS NOMBRE, t.NumeroIdentificacion, ti.nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt     
  WHERE t.TipoIdentificacion = ti.id_unico 
  AND t.id_unico = pt.Tercero 
  AND pt.Perfil = 10 
  ORDER BY NOMBRE ASC";
    $contacto = $mysqli->query($sqlContacto);
} else {
    //Contacto.
    $sqlContacto = "SELECT DISTINCT t.id_unico, 
        
IF(CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) 
     IS NULL OR CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) = '',
     (t.razonsocial),
     CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos)) AS NOMBRE, t.NumeroIdentificacion, ti.nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt     
  WHERE t.TipoIdentificacion = ti.id_unico 
  AND t.id_unico = pt.Tercero 
  AND pt.Perfil = 10
  AND t.id_unico != '$row[9]'
  ORDER BY NOMBRE ASC";
    $contacto = $mysqli->query($sqlContacto);
    //Fin de las consultas para combos.
}
#****** Departamento  *********#
if(empty($row[27])){ $de = 0; } else { $de = $row[27]; }
$sqlDep = "SELECT Id_Unico, Nombre 
  FROM gf_departamento  
  WHERE id_unico != $de 
  ORDER BY Nombre ASC";
$dep = $mysqli->query($sqlDep);
#****** Ciudad  *********#
if(empty($row[25]) && empty($row[27])){ $de = 0;$cd = 0; } else { $de = $row[27];$cd = $row[25]; }
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
        nit1 = document.form.noIdent.value;
        if (isNaN(nit1))
        {
            document.form.digitVerif.value = "X";
            alert('Número del Nit no valido, ingrese un número sin puntos, ni comas, ni guiones, ni espacios');
        } else {
            arreglo = new Array(16);
            x = 0;
            y = 0;
            z = nit1.length;
            arreglo[1] = 3;
            arreglo[2] = 7;
            arreglo[3] = 13;
            arreglo[4] = 17;
            arreglo[5] = 19;
            arreglo[6] = 23;
            arreglo[7] = 29;
            arreglo[8] = 37;
            arreglo[9] = 41;
            arreglo[10] = 43;
            arreglo[11] = 47;
            arreglo[12] = 53;
            arreglo[13] = 59;
            arreglo[14] = 67;
            arreglo[15] = 71;
            for (i = 0; i < z; i++)
            {
                y = (nit1.substr(i, 1));
                x += (y * arreglo[z - i]);
            }
            y = x % 11
            if (y > 1) {
                dv1 = 11 - y;
            } else {
                dv1 = y;
            }
            document.form.digitVerif.value = dv1;
        }
    }
</script>
    <title>Modificar Compañía</title>
    <link href="css/select/select2.min.css" rel="stylesheet">
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <style>
        label #tipoIdent-error,#tipocomp-error,#razoSoci-error,#tipoEntidad-error,#tipoReg-error, #tipoEmp-error, #depto-error, #ciudad-error, #correo-error{
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
        <div class="row content" style="margin-bottom:-20px">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top: -20px">
                <h2 align="center" class="tituloform">Modificar Compañía</h2>
                <a href="TERCERO_COMPANIA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Compañia: <?php echo ucwords(mb_strtolower($row[1])) ?></h5>
                <div class="client-form contenedorForma" style="margin-top:-5px">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_TERCERO_COMPANIAJson.php">
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" value="<?php echo $row[0]; ?>">
                        <div class="form-group form-inline " style="">
                            <label for="tipoIdent" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                            <div class="form-group form-inline col-sm-3" style="margin-left:-10px">
                                <select name="tipoIdent" id="tipoIdent" class="select2_single form-control col-sm-5" style="height: 33px;width:150px" title="Tipo Identificación" required>
                                    <?php
                                    echo '<option value="'.$row[16].'">'.ucwords(mb_strtolower($row[4])).'</option>';
                                    while ($ma = mysqli_fetch_assoc($tipoIden)) {
                                        echo '<option value="'.$ma["id_unico"].'">'.ucwords((mb_strtolower($ma["nombre"]))).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline col-sm-3" style="">
                                <input type="text" name="noIdent" id="noIdent" value="<?php echo $row[2]; ?>" class="form-control col-sm-5" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event, 'num')" placeholder="Número" style="width:95px" style="height: 30px" required onblur="CalcularDv()" />
                                <span class="col-sm-1" style="width:1px; margin-top:8px;"><strong> - </strong></span>
                                <input type="text" name="digitVerif" id="digitVerif" value="<?php echo $row[3]; ?>" class="form-control " style="width:30px" maxlength="1" placeholder="0" title="Dígito de verificación" onkeypress="return txtValida(event, 'num')" placeholder="" readonly="" style="height: 30px"/>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -22px; ">
                            <label for="sucursal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Sucursal:</label>
                            <select name="sucursal" id="sucursal" class="select2_single form-control" title="Seleccione Sucursal" >
                                <?php 
                                if(!empty($row[17])){
                                    echo '<option value="'.$row[17].'">'.ucwords(mb_strtolower($row[5])).'</option>';
                                    echo '<option value=""> - </option>';
                                } else { 
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowS = mysqli_fetch_row($sucursal)) { 
                                    echo '<option value="'.$rowS[0].'">'.ucwords(mb_strtolower($rowS[1])).'</option>';
                                } ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="razoSoci" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Razón Social:</label>
                            <input type="text" name="razoSoci" id="razoSoci" class="form-control" value="<?php echo ($row[1]); ?>" maxlength="500" title="Ingrese la razón social" onkeypress="return txtValida(event)" onkeyup="javascript:this.value = this.value.toUpperCase();" placeholder="Razón Social" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px; ">
                            <label for="tipoReg" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Régimen:</label>
                            <select name="tipoReg" id="tipoReg" class="select2_single form-control" title="Ingrese el tipo de régimen" >
                                <?php 
                                if(!empty($row[18])){
                                    echo '<option value="'.$row[18].'">'.ucwords(mb_strtolower($row[8])).'</option>';
                                } else { 
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowTR = mysqli_fetch_row($tipoReg)) { 
                                    echo '<option value="'.$rowTR[0].'">'.ucwords(mb_strtolower($rowTR[1])).'</option>';
                                } ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="tipoEmp" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Empresa:</label>
                            <select name="tipoEmp" id="tipoEmp" class="select2_single form-control" title="Ingrese el tipo de empresa" >
                                <?php 
                                if(!empty($row[19])){
                                    echo '<option value="'.$row[19].'">'.ucwords(mb_strtolower($row[10])).'</option>';
                                } else { 
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowTE = mysqli_fetch_row($tipoEmp)) { 
                                    echo '<option value="'.$rowTE[0].'">'.ucwords(mb_strtolower($rowTE[1])).'</option>';
                                } ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="tipoEntidad" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Entidad:</label>
                            <select name="tipoEntidad" id="tipoEntidad" class="select2_single form-control" title="Ingrese el tipo  Entidad" >
                                <?php 
                                if(!empty($row[20])){
                                    echo '<option value="'.$row[20].'">'.ucwords(mb_strtolower($row[11])).'</option>';
                                } else { 
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowTEn = mysqli_fetch_row($tipoEnt)) { 
                                    echo '<option value="'.$rowTEn[0].'">'.ucwords(mb_strtolower($rowTEn[1])).'</option>';
                                } ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="repreLegal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Representante Legal:</label>
                            <select name="repreLegal" id="repreLegal" class="select2_single form-control" title="Ingrese el representante legal">
                                 <?php 
                                if(!empty($row[6])){
                                    echo '<option value="'.$row[6].'">'.ucwords(mb_strtolower($row[14])).'</option>';
                                } else { 
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowRL = mysqli_fetch_row($repreLegal)) { 
                                    echo '<option value="'.$rowRL[0].'">'.ucwords(mb_strtolower($rowRL[1])). ' - ' . $rowRL[2].'</option>';
                                } ?>
                            </select> 
                        </div>
                        <div class="form-group form-inline" style="margin-top: -10px">
                            <label for="depto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ubicación:</label>     
                            <div class="form-group form-inline col-sm-3"  style="margin-left:-10px">
                                <select name="depto" id="depto" class="select2_single form-control col-sm-5" style="height: 20%;width:170px" title="Seleccione Departamento" required>
                                    <?php 
                                    if(!empty($row[27])){
                                        echo '<option value="'.$row[27].'">'.ucwords(mb_strtolower($row[28])).'</option>';
                                    } else { 
                                        echo '<option value=""> - </option>';
                                    }
                                    while ($rowD = mysqli_fetch_row($dep)) { 
                                        echo '<option value="'.$rowD[0].'">'.ucwords(mb_strtolower($rowD[1])).'</option>';
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group form-inline col-sm-1" style=""></div>
                            <div class="form-group form-inline col-sm-3" style="">
                                <select name="ciudad" style="height: 24%;width:100px" id="ciudad" class="select2_single form-control" title="Seleccione Ciudad" required>
                                    <?php 
                                    if(!empty($row[25])){
                                        echo '<option value="'.$row[25].'">'.ucwords(mb_strtolower($row[26])).'</option>';
                                    } else { 
                                        echo '<option value=""> - </option>';
                                    }
                                    while ($rowC = mysqli_fetch_row($ciu)) { 
                                        echo '<option value="'.$rowC[0].'">'.ucwords(mb_strtolower($rowC[1])).'</option>';
                                    } ?>
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
                                if(!empty($row[9])){
                                    echo '<option value="'.$row[9].'">'.ucwords(mb_strtolower($row[15])).'</option>';
                                    echo '<option value=""> - </option>';
                                } else { 
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowCont = mysqli_fetch_row($contacto)) { 
                                    echo '<option value="'.$rowCont[0].'">'.ucwords(mb_strtolower($rowCont[1])). ' - ' . $rowCont[2].'</option>';
                                } ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="codigo" class="col-sm-5 control-label"><strong class="obligado"></strong>Código DANE:</label>
                            <?php if (empty($row[12])) { ?>
                                <input type="text" name="codigo" id="codigo" class="form-control" maxlength="500" title="Ingrese el código DANE" onkeypress="return txtValida(event, 'num_car')"  placeholder="Código DANE" onkeyup="javascript:this.value = this.value.toUpperCase();">
                            <?php } else { ?>
                                <input type="text" name="codigo" id="codigo" class="form-control" maxlength="500" title="Ingrese el código DANE" onkeypress="return txtValida(event, 'num_car')"  placeholder="Código DANE" onkeyup="javascript:this.value = this.value.toUpperCase();"  value="<?php echo $row[12]; ?>">
                            <?php } ?>
                        </div>
                        <div class="form-group" style="margin-top: -20px; ">
                            <label for="tipocomp" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tipo Compañia:</label>
                            <select name="tipocomp" id="tipocomp" class="form-control select2_single" title="Ingrese el Tipo Compañia" required>
                                <?php
                                if (!empty($row[13])) {
                                    if ($row[13] == 1) {
                                        echo '<option value="1">Pública</option>';
                                        echo '<option value="2">Privada</option>';
                                    } elseif ($row[13] == 2) {
                                        echo '<option value="2">Privada</option>';
                                        echo '<option value="1">Pública</option>';
                                    }
                                    echo '<option value="">-</option>';
                                } else {
                                    echo '<option value="">Tipo Compañia</option>';
                                    echo '<option value="1">Pública</option>';
                                    echo '<option value="2">Privada</option>';
                                }
                                ?>
                                <?php while ($rowCont = mysqli_fetch_row($contacto)) { ?>
                                    <option value="<?php echo $rowCont[0] ?>"><?php echo ucwords((mb_strtolower($rowCont[1]))) . ' - ' . $rowCont[2]; ?></option>
                                <?php } ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="correo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Correo Electrónico:</label>
                            <input type="email" name="correo" id="correo" value="<?php echo $row[24]?>" class="form-control" maxlength="500" title="Ingrese Correo Electrónico" placeholder="Corrreo Electrónico" >
                        </div>
                        <div class="form-group" style="margin-top: -15px">
                            <label for="flLogo" class="col-sm-5 control-label">Logo:</label>
                            <input type="file" name="flLogo" id="flLogo" class="form-control" accept="image/*">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="costos" class="col-sm-5 control-label" style="margin-top: -5px;"><strong style="color:#03C1FB;">*</strong>Distribución de Costos:</label>
                            <?php if($row[29]==1) { ?>
                            <input type="radio" name="costos" id="costos" value="1" title="Distribución de Costos" checked="checked"> SI
                            <input type="radio" name="costos" id="costos" value="2" title="Distribución de Costos" > NO
                            <?php }  else { ?>
                            <input type="radio" name="costos" id="costos" value="1" title="Distribución de Costos" > SI
                            <input type="radio" name="costos" id="costos" value="2" title="Distribución de Costos" checked="checked"> NO
                            <?php } ?>
                        </div>
                        <input type="hidden"  name="txtLogo" id="txtLogo" value="<?php echo $row[20]; ?>"/>
                        <div class="form-group" style="margin-top:5px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: -10px; margin-left: 0px;">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>
            </div> 
            <div class="col-sm-7 col-sm-3" style="margin-top:-20px">
                <h2 class="titulo" align="center">Información Adicional</h2>
                <div align="center">
                    <a href="GF_DIRECCION_TERCERO.php"><button class="btn btnInfo btn-primary">DIRECCIÓN</button></a><br/>
                    <a href="GF_CUENTA_BANCARIA_TERCERO.php"><button class="btn btnInfo btn-primary">CUENTA BANCARIA</button></a><br/>
                    <a href="GF_TIPO_ACTIVIDAD_TERCERO.php"><button class="btn btnInfo btn-primary">TIPO ACTIVIDAD</button></a><br/>
                    <a href="GF_TELEFONO.php"><button class="btn btnInfo btn-primary">TELÉFONO</button></a><br/>
                    <a href="GF_CONDICION_TERCERO.php"><button class="btn btnInfo btn-primary">CONDICIÓN</button></a><br/>
                    <a href="registrar_TERCERO_CONTACTO_NATURAL.php" class="btn btnInfo btn-primary">CONTACTO</a><br/>
                    <a href="GF_PERFIL_CONDICION.php" class="btn btnInfo btn-primary">PERFIL CONDICIÓN</a><br/>
                    <a href="GF_TERCERO_RETENCION.php" class="btn btnInfo btn-primary">RETENCIÓN</a><br/>
                    <a href="GF_TERCERO_INGRESOS.php" class="btn btnInfo btn-primary">INGRESOS</a>
                </div>
            </div>
        </div>
    </div>
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
</body>
</html>