<?php 
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#02/08/2018 |Erica G. | Correo Electrónico - Arreglar Código
#######################################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$id_ter_clie_jur = " ";
if (isset($_GET["id_ter_clie_jur"])){ 
    $id_ter_clie_jur = (($_GET["id_ter_clie_jur"]));
    $queryTerceroComp = "SELECT t.Id_Unico, t.RazonSocial, t.NumeroIdentificacion, 
        t.DigitoVerficacion, ti.Id_Unico, ti.Nombre, s.Id_Unico, s.nombre, t.RepresentanteLegal, 
        ci.Id_Unico, ci.Nombre, tr.Id_Unico, tr.Nombre, t.Contacto, te.Id_Unico, te.Nombre, 
        tipen.Id_Unico, tipen.Nombre, ci.Departamento, z.Id_Unico, z.Nombre,
        DP.Id_Unico,DP.Nombre , t.email ,t.procedencia 
      FROM gf_tercero t  
      LEFT JOIN gf_tipo_identificacion ti ON  t.TipoIdentificacion = ti.Id_Unico
      LEFT JOIN gf_sucursal s ON t.Sucursal = s.Id_Unico
      LEFT JOIN gf_tipo_regimen tr ON t.TipoRegimen = tr.Id_Unico
      LEFT JOIN gf_tipo_empresa te ON t.TipoEmpresa = te.Id_Unico
      LEFT JOIN gf_tipo_entidad tipen ON t.TipoEntidad = tipen.Id_Unico
      LEFT JOIN gf_ciudad ci ON t.CiudadIdentificacion = ci.Id_Unico
      LEFT JOIN gf_zona z ON t.Zona = z.Id_Unico
      LEFT JOIN gf_departamento DP ON ci.Departamento = DP.Id_Unico
      WHERE  md5(t.Id_Unico) = '$id_ter_clie_jur'";
}
$resultado = $mysqli->query($queryTerceroComp);
$row = mysqli_fetch_row($resultado);

$_SESSION['id_tercero'] = $row[0];
$_SESSION['perfil'] = "J"; //Jurídica.
$_SESSION['url'] = "modificar_TERCERO_CLIENTE_JURIDICA.php?id_ter_clie_jur=".(($_GET["id_ter_clie_jur"]));
$_SESSION['tipo_perfil']='Cliente jurídica';

#****** Tipo Identificación  *********#
$sqlTipoIden = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_identificacion
  WHERE Id_Unico != $row[4]
  ORDER BY Nombre ASC";
$tipoIden = $mysqli->query($sqlTipoIden);

#****** Sucursal *********#
if(empty($row[6])){$ids =0;}else{$ids =$row[6];}
$sqlSucursal = "SELECT Id_Unico, Nombre 
  FROM gf_sucursal
  WHERE Id_Unico != $ids 
  ORDER BY Nombre ASC";
$sucursal = $mysqli->query($sqlSucursal);

#****** Tipo Régimen *********#
if(empty($row[11])){$idtr =0;}else{$idtr =$row[11];}
$sqlTipoReg = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_regimen
  WHERE Id_Unico != $idtr
  ORDER BY Nombre ASC";
$tipoReg = $mysqli->query($sqlTipoReg);

#****** Tipo Empresa *********#
if(empty($row[14])){$idem =0;}else{$idem =$row[14];}
$sqlTipoEmp = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_empresa
  WHERE Id_Unico != $idem 
  ORDER BY Nombre ASC";
$tipoEmp = $mysqli->query($sqlTipoEmp);

#****** Tipo Entidad *********#
if(empty($row[16])){$idten =0;}else{$idten =$row[16];}
$sqlTipoEnt = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_entidad
  WHERE Id_Unico != $idten 
  ORDER BY Nombre ASC";
$tipoEnt = $mysqli->query($sqlTipoEnt);

#****** Representante Legal *********#
if(empty($row[8])){$idrl =0;}else{$idrl =$row[8];}
$sqlReprLeg = "SELECT t.Id_Unico, 
    CONCAT_WS(' ',t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos),
    t.NumeroIdentificacion, ti.Nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt  
  WHERE t.TipoIdentificacion = ti.Id_Unico
  AND t.Id_Unico = pt.Tercero 
  AND pt.Perfil = 10
  AND t.Id_Unico != $idrl  
  ORDER BY t.NombreUno ASC";
$repreLegal = $mysqli->query($sqlReprLeg);

#****** Contacto *********#
if(empty($row[13])){$idctc =0;}else{$idctc =$row[13];}
$sqlContacto = "SELECT t.Id_Unico, 
    CONCAT_WS(' ',t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos), 
    t.NumeroIdentificacion, ti.Nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt     
  WHERE t.TipoIdentificacion = ti.Id_Unico 
  AND t.Id_Unico = pt.Tercero 
  AND pt.Perfil = 10
  AND t.Id_Unico != $idctc 
  ORDER BY t.NombreUno ASC";
$contacto = $mysqli->query($sqlContacto);

#****** Zona *********#
if(empty($row[19])){$idz =0;}else{$idz =$row[19];}
$sqlZona = "SELECT Id_Unico, Nombre 
    FROM gf_zona
    WHERE Id_Unico != $idz 
    ORDER BY Nombre ASC";
$zona = $mysqli->query($sqlZona);
  
#****** Departamento  *********#
if(empty($row[21])){ $de = 0; } else { $de = $row[21]; }
$sqlDep = "SELECT Id_Unico, Nombre 
  FROM gf_departamento  
  WHERE id_unico != $de 
  ORDER BY Nombre ASC";
$dep = $mysqli->query($sqlDep);
#****** Ciudad  *********#
if(empty($row[9]) && empty($row[21])){ $de = 0;$cd = 0; } else { $de = $row[21];$cd = $row[9]; }
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
    <title>Modificar Cliente Jurídica</title>
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
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top: -20px; margin-bottom: -15px">
                <h2 class="tituloform" align="center" >Modificar Cliente Jurídica</h2>
                <a href="TERCERO_CLIENTE_JURIDICA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo $row[1] ?></h5>
                <div  class="client-form contenedorForma">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_TERCERO_CLIENTE_JURIDICAJson.php">
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em;">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" value="<?php echo $row[0]; ?>">
                        <div class="form-group form-inline " style="">
                            <label for="tipoIdent" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                            <div class="form-group form-inline col-sm-3" style="margin-left:-10px">
                                <select name="tipoIdent" id="tipoIdent" class="select2_single form-control col-sm-5" style="height: 33px;width:150px" title="Tipo Identificación" required>
                                    <option value="<?php echo $row[4]; ?>"><?php echo ucwords(mb_strtolower($row[5])); ?></option>
                                    <?php while ($ma = mysqli_fetch_assoc($tipoIden)) { ?>
                                    <option value="<?php echo $ma["Id_Unico"]; ?>">
                                    <?php echo ucwords((mb_strtolower($ma["Nombre"]))); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group form-inline col-sm-3" style="">
                                <input type="text" name="noIdent" id="noIdent" value="<?php echo $row[2];?>" class="form-control col-sm-5" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event, 'num')" placeholder="Número" style="width:95px" style="height: 30px" required onblur="CalcularDv();    return existente()" />
                                <span class="col-sm-1" style="width:1px; margin-top:8px;"><strong> - </strong></span>
                                <input type="text" name="digitVerif" id="digitVerif" value="<?php echo $row[3];?>"class="form-control " style="width:30px" maxlength="1" placeholder="0" title="Dígito de verificación" onkeypress="return txtValida(event, 'num')" placeholder="" readonly="" style="height: 30px"/>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -22px; ">
                            <label for="sucursal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Sucursal:</label>
                            <select name="sucursal" id="sucursal" class="select2_single form-control" title="Seleccione Sucursal" >
                                <?php if(!empty($row[6])){
                                echo '<option value="'.$row[6].'">'.ucwords(mb_strtolower($row[7])).'</option>'; 
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
                            <input type="text" name="razoSoci" id="razoSoci" value="<?php echo $row[1]?>" class="form-control" maxlength="500" title="Ingrese la razón social" onkeypress="return txtValida(event)" onkeyup="javascript:this.value = this.value.toUpperCase();" placeholder="Razón Social" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px; ">
                            <label for="tipoReg" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Régimen:</label>
                            <select name="tipoReg" id="tipoReg" class="select2_single form-control" title="Ingrese el tipo de régimen" >
                                <?php
                                if(!empty( $row[11])){
                                    echo '<option value="'.$row[11].'">'.ucwords(mb_strtolower($row[12])).'</option>'; 
                                    echo '<option value=""> - </option>'; 
                                } else {
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowTR = mysqli_fetch_row($tipoReg)) {  ?>
                                <option value="<?php echo $rowTR[0] ?>"><?php echo ucwords((mb_strtolower($rowTR[1]))); ?></option>
                                <?php }  ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="tipoEmp" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Empresa:</label>
                            <select name="tipoEmp" id="tipoEmp" class="select2_single form-control" title="Ingrese el tipo de empresa" >
                                <?php
                                if(!empty( $row[14])){
                                    echo '<option value="'.$row[14].'">'.ucwords(mb_strtolower($row[15])).'</option>'; 
                                    echo '<option value=""> - </option>'; 
                                } else {
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowTE = mysqli_fetch_row($tipoEmp)) {?>
                                <option value="<?php echo $rowTE[0] ?>"><?php echo ucwords((mb_strtolower($rowTE[1]))); ?></option>
                                <?php } ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="tipoEntidad" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Entidad:</label>
                            <select name="tipoEntidad" id="tipoEntidad" class="select2_single form-control" title="Ingrese el tipo  Entidad" >
                                <?php
                                if(!empty( $row[16])){
                                    echo '<option value="'.$row[16].'">'.ucwords(mb_strtolower($row[17])).'</option>'; 
                                    echo '<option value=""> - </option>'; 
                                } else {
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowTEn = mysqli_fetch_row($tipoEnt)) {?>
                                <option value="<?php echo $rowTEn[0] ?>"><?php echo ucwords((mb_strtolower($rowTEn[1]))); ?></option>
                                <?php } ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="repreLegal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Representante Legal:</label>
                            <select name="repreLegal" id="repreLegal" class="select2_single form-control" title="Ingrese el representante legal">
                                <?php
                                if(!empty( $row[8])){
                                    $sqlElReprLeg = "SELECT t.Id_Unico, 
                                        CONCAT_WS(' ',t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos),
                                        t.NumeroIdentificacion, ti.Nombre 
                                    FROM gf_tercero t, gf_tipo_identificacion ti  
                                    WHERE t.TipoIdentificacion = ti.Id_Unico
                                    AND t.Id_Unico = '$row[8]'";
                                    $elReprLeg = $mysqli->query($sqlElReprLeg);
                                    $rowElReprLeg = mysqli_fetch_row($elReprLeg);
                                    echo '<option value="'.$sqlElReprLeg[0].'">'.ucwords(mb_strtolower($sqlElReprLeg[1])).' - '.$sqlElReprLeg[2].'</option>'; 
                                    echo '<option value=""> - </option>'; 
                                } else {
                                    echo '<option value=""> - </option>';
                                }
                                 while ($rowRL = mysqli_fetch_row($repreLegal)) { ?>
                               <option value="<?php echo $rowRL[0] ?>">
                                <?php echo ucwords((mb_strtolower($rowRL[1]. " - " . $rowRL[2] ))); ?>
                               </option>
                                <?php } ?>
                            </select> 
                        </div>
                        <div class="form-group form-inline" style="margin-top: -10px">
                            <label for="depto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ubicación:</label>     
                            <div class="form-group form-inline col-sm-3"  style="margin-left:-10px">
                                <select name="depto" id="depto" class="select2_single form-control col-sm-5" style="height: 20%;width:170px" title="Seleccione Departamento" required>
                                    <?php 
                                    if(!empty($row[21])){
                                        echo '<option value="'.$row[21].'">'.ucwords(mb_strtolower($row[22])).'</option>';
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
                                    if(!empty($row[9])){
                                        echo '<option value="'.$row[9].'">'.ucwords(mb_strtolower($row[10])).'</option>';
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
                                <?php if(!empty( $row[13])){
                                    $sqlElContacto = "SELECT t.Id_Unico, 
                                        CONCAT_WS(' ',t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos),
                                        t.NumeroIdentificacion, ti.Nombre 
                                    FROM gf_tercero t, gf_tipo_identificacion ti  
                                    WHERE t.TipoIdentificacion = ti.Id_Unico
                                    AND t.Id_Unico = $row[13]";
                                    $elContacto = $mysqli->query($sqlElContacto);
                                    $rowElCon = mysqli_fetch_row($elContacto);
                                    echo '<option value="'.$rowElCon[0].'">'.ucwords(mb_strtolower($rowElCon[1])).' - '.$rowElCon[2].'</option>';
                                } else { 
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowCon = mysqli_fetch_row($contacto)) { ?>
                                <option value="<?php echo $rowCon[0] ?>"><?php echo ucwords((mb_strtolower($rowCon[1]))).' - '.$rowCon[2]; ?></option>
                                <?php } ?>
                            </select> 
                        </div>
                         <div class="form-group" style="margin-top: -10px; ">
                            <label for="zona" class="col-sm-5 control-label">Zona:</label>
                            <select name="zona" id="zona" class="select2_single form-control" title="Ingrese la zona" >
                                <?php 
                                if(!empty($row[19])){
                                    echo '<option value="'.$row[19].'">'.ucwords(mb_strtolower($row[20])).'</option>';
                                } else { 
                                    echo '<option value=""> - </option>';
                                }
                                while ($rowZ = mysqli_fetch_row($zona)) { ?>
                                    <option value="<?php echo $rowZ[0] ?>"><?php echo ucwords((strtolower($rowZ[1]))); ?></option>
                                <?php }  ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="correo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Correo Electrónico:</label>
                            <input type="email" name="correo" id="correo" value="<?php echo $row[23]?>" class="form-control" maxlength="500" title="Ingrese Correo Electrónico" placeholder="Corrreo Electrónico" >
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="procedencia" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Procedencia:</label>
                            <select name="procedencia" id="procedencia" class="select2_single form-control" title="Ingrese Procedencia" >
                            <?php
                                if(!empty($row[24])){
                                    echo '<option value="'.$row[24].'">'.ucwords(mb_strtolower($row[24])).'</option>';
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
                      
                       
                        <div class="form-group" style="margin-top:-12px;" >
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -80px; margin-bottom: -10px; margin-left: 650px;">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>
            </div>
            <div class="col-sm-7 col-sm-3" align="center" style="margin-top:-22px">
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
                                    <a href="#" style="">
                                        MOVIMIENTO CONTABLE
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="GF_DIRECCION_TERCERO.php" > <button style="margin-bottom:10px"class="btn btn-primary btnInfo">DIRECCIÓN</button></a><BR/>
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
                                <a href="registrar_TERCERO_CONTACTO_NATURAL.php" <?php if (!isset($_SESSION['id_tercero'])) {
                                    echo ' disabled title="Debe primero ingresar una compañía."';
                                } ?> class="btn btnInfo btn-primary">CONTACTO</a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <a href="GF_PERFIL_CONDICION.php"><button style="margin-top:15px" class="btn btn-primary btnInfo">PERFIL CONDICIÓN</button></a><br/>
                            </td>
                        </tr>
                        <br/>
                        <tr>
                            <td><br/></td>
                            <td>
                                <a href="GF_RESPONSABILIDAD_TERCERO.php?id=<?php echo $_GET["id_ter_clie_jur"];?>"><button style="margin-top:15px" class="btn btnInfo btn-primary">RESPONSABILIDADES</button></a><br/>
                            </td>
                        </tr>
                        <br/>
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
    <?php require_once 'footer.php'; ?>
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

</body>
</html>