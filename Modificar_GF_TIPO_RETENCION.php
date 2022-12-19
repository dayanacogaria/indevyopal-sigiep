<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');
$id= $_GET['id'];
$anno = $_SESSION['anno'];
//Busqueda registro a modificar
$query = "SELECT 
        tr.id_unico, 
        tr.nombre, 
        tr.porcentajebase, 
        tr.limiteinferior, 
        tr.porcentajeaplicar, 
        tr.valoraplicar, 
        tr.factorredondeo, 
        tr.descripcion, 
        tr.modificarretencion, 
        tr.modificarbase, 
        tr.ley1450, 
        cr.id_unico, 
        cr.nombre, 
        fa.id_unico, 
        fa.nombre, 
        tb.id_unico, 
        tb.nombre, 
        c.id_unico, 
        c.codi_cuenta, 
        c.nombre, 
        cf.id_unico, 
        cf.nombre, 
        tr.cod_exogena,  
        cc.id_unico, 
        cc.codi_cuenta, 
        cc.nombre,
        tr.concepto_pago,
        cr.base_ingresos,
        tr.rango_min_ingresos, 
        tr.rango_max_ingresos
        FROM gf_tipo_retencion tr 
        LEFT JOIN gf_clase_retencion cr ON tr.claseretencion=cr.id_unico 
        LEFT JOIN gf_factor_aplicacion fa ON tr.factoraplicacion=fa.id_unico 
        LEFT JOIN gf_tipo_base tb ON tr.tipobase = tb.id_unico 
        LEFT JOIN gf_cuenta c ON tr.cuenta= c.id_unico 
        LEFT JOIN gf_concepto cf ON cf.id_unico= tr.concepto_ingreso_hom  
        LEFT JOIN gf_cuenta cc ON tr.cuenta_credito= cc.id_unico 
        WHERE md5(tr.id_unico) = '$id'";
$resultado = $mysqli->query($query);
$row= mysqli_fetch_row($resultado);

//Clase retención
$busClase= "SELECT id_unico, nombre FROM gf_clase_retencion WHERE id_unico !=$row[11] ORDER BY nombre ASC";
$clase = $mysqli->query($busClase);

//Factor Aplicación
$busFactor= "SELECT id_unico, nombre FROM gf_factor_aplicacion WHERE id_unico !=$row[13] ORDER BY nombre ASC";
$factor = $mysqli->query($busFactor);

//Tipo Base
$busBase= "SELECT id_unico, nombre FROM gf_tipo_base WHERE id_unico != '$row[15]' ORDER BY nombre ASC";
$base = $mysqli->query($busBase);
//Cuenta
$busCuenta= 'SELECT id_unico, codi_cuenta, nombre 
	FROM gf_cuenta WHERE movimiento =\'1\' 
	AND id_unico != \''.$row[17].'\' 
	AND parametrizacionanno = '.$anno.'
	AND clasecuenta = 16
	ORDER BY codi_cuenta ASC';
$cuenta = $mysqli->query($busCuenta);
?>
<link href="css/select/select2.min.css" rel="stylesheet">
<title>Modificar Tipo Retención</title>
</head>
<style type="text/css">
  label {
    width: 150px;
    margin-top: -15px;
  }
  .select2-container {
    margin-top: -11px;
  }
  select2-container {
  margin-top: -11px;
  }
</style>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left" style="margin-top: -20px">
                <!--Titulo del formulario-->
                <h2 align="center" class="tituloform">Modificar Tipo Retención</h2>
                <a href="LISTAR_GF_TIPO_RETENCION.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords(mb_strtolower($row[1]));?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px" class="client-form">
                    <form name="form" id="form" style="margin-top:-20px; margin-left: 15px" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GF_TIPO_RETENCIONJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" id="id" value="<?php echo $row[0];?>">
                        <input type="hidden" name="base_ing" id="base_ing" value="<?php echo $row[27];?>">
                        <div class="form-group form-inline" style="margin-top: -15px;">
                            <!--NOMBRE-->
                            <label for="nombre" class="control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" style="display: inline;  width: 200px; height: 35px" class="form-inline" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre"  placeholder="Nombre" required value="<?php echo ucwords(mb_strtolower($row[1]));?>">
                            <!--PORCENTAJE BASE--> 
                            <label for="porcentajeB" class="control-label"><strong style="color:#03C1FB;">*</strong>Porcentaje Base:</label>
                            <input type="text" name="porcentajeB" id="porcentajeB" style="display: inline;  width: 200px; height: 35px" class="form-inline" placeholder="Porcentaje Base" onkeypress="return validarNum1(event, true)"  maxlength="5" title="Ingrese porcentaje base"  required value="<?php echo $row[2]?>">
                        </div>
                        <div class="form-group form-inline" style="margin-top: -15px;">
                            <!--LIMITE INFERIOR-->
                            <label for="limiteI" class="control-label"><strong style="color:#03C1FB;">*</strong>Límite Inferior:</label>
                            <input type="text" name="limiteI" id="limiteI" style="display: inline;  width: 200px; height: 35px" class="form-inline" placeholder="Límite Inferior" onkeypress="return txtValida(event,'decimales')"  maxlength="18" title="Ingrese límite inferior"  required value="<?php echo $row[3]?>">
                             <!--PORCENTAJE APLICAR--> 
                            <label for="porcentajeA" class="control-label"><strong style="color:#03C1FB;">*</strong>Porcentaje Aplicar:</label>
                            <input type="text" name="porcentajeA" id="porcentajeA" style="display: inline;  width: 200px; height: 35px" class="form-inline" placeholder="Porcentaje Aplicar" onkeypress="return validarNum2(event, true)" maxlength="5" title="Ingrese porcentaje aplicar"  required value="<?php echo $row[4]?>">
                        </div>
                        <div class="form-group form-inline" style="margin-top: -15px;">
                            <!--VALOR APLICAR-->
                            <label for="valorA" class="control-label"><strong style="color:#03C1FB;">*</strong>Valor Aplicar:</label>
                            <input type="text" name="valorA" id="valorA" style="display: inline;  width: 200px; height: 35px" class="form-inline" placeholder="Valor Aplicar" onkeypress="return txtValida(event,'decimales')"  maxlength="18" title="Ingrese valor aplicar"  required value="<?php echo $row[5]?>">
                            <!--FACTOR REDONDEO-->
                            <label for="factorR" class="control-label"><strong style="color:#03C1FB;">*</strong>Factor Redondeo:</label>
                            <input type="text" name="factorR" id="factorR" style="display: inline;  width: 200px; height: 35px" class="form-inline" placeholder="Factor Redondeo" onkeypress="return txtValida(event,'decimales')"  maxlength="5" title="Ingrese factor redondeo"  required value="<?php echo $row[6]?>">
                            </div>
                        <div class="form-group form-inline" style="margin-top: -25px;">
                            <!--DESCRIPCION--> 
                            <label for="descripcion" class="control-label" style="margin-top:-100px"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                            <textarea type="text" name="descripcion" id="descripcion" style="display: inline;  width: 200px; height: 50px;" class="form-inline" placeholder="Descripción" onkeypress="return txtValida(event,'num_car')"  maxlength="500" title="Ingrese Descripción"  required style="margin-top:0px; height: 35px" ><?php echo ucwords(mb_strtolower($row[7]));?></textarea>
                            <!--MODIFICAR RETENCION-->
                            <div class="form-group form-inline" style="margin-top: -83px; margin-left: 0px;">
                                <label for="retencion" class="control-label" style="margin-top:-4px;"><strong style="color:#03C1FB; ">*</strong>¿Permite Modificar Retención?:</label>
                                <?php if ($row[8]=='1') { ?>
                                <input type="radio" name="retencion" value="1" title="Seleccione si permite modificar manualmente el valor de la retención" style="margin-left:15px" checked>Sí
                                <input type="radio" name="retencion" value="2" title="Seleccione si No permite modificar manualmente el valor de la retención">No
                                <?php } else { ?>
                                <input type="radio" name="retencion" value="1" title="Seleccione si permite modificar manualmente el valor de la retención" style="margin-left:15px">Sí
                                <input type="radio" name="retencion" value="2" title="Seleccione si No permite modificar manualmente el valor de la retención" checked>No
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group form-inline" style="margin-top:-10px">
                             <!--MODIFICAR BASE--> 
                            <div class="form-group form-inline" style="margin-left:0px">
                                <label for="baseR" class="control-label" style="margin-top:-10px;"><strong style="color:#03C1FB; ">*</strong>¿Permite Modificar Base de Retención?:</label>
                                <?php if ($row[9]=='1') { ?>
                                <input type="radio" name="baseR" value="1" title="Seleccione si permite modificar manualmente el valor de la base" style="margin-left:15px" checked>Sí
                                <input type="radio" name="baseR" value="2" title="Seleccione si No permite modificar manualmente el valor de la base" >No
                                <?php } else { ?>
                                <input type="radio" name="baseR" value="1" title="Seleccione si permite modificar manualmente el valor de la base" style="margin-left:15px">Sí
                                <input type="radio" name="baseR" value="2" title="Seleccione si No permite modificar manualmente el valor de la base" checked>No
                                <?php } ?>
                            </div>
                            <!--LEY 1450-->
                            <div class="form-group form-inline" style="margin-left:143px">
                                <label for="ley" class="control-label" style="margin-top:-15px;"><strong style="color:#03C1FB; ">*</strong>¿Aplica la ley 1450?:</label>
                                <?php if ($row[10]=='1') { ?>
                                <input type="radio" name="ley" value="1" title="Seleccione si aplica la ley 1450" style="margin-left:15px" checked>Sí
                                <input type="radio" name="ley" value="2" title="Seleccione si No aplica la ley 1450" >No
                                <?php } else { ?>
                                <input type="radio" name="ley" value="1" title="Seleccione si aplica la ley 1450" style="margin-left:15px">Sí
                                <input type="radio" name="ley" value="2" title="Seleccione si No aplica la ley 1450" checked>No
                                <?php } ?>
                                
                            </div>
                        </div>
                        <div class="form-group form-inline" style="margin-top: 0px;">
                            <!--CLASE RETENCION-->
                            <label for="clase" class="control-label"><strong style="color:#03C1FB;">*</strong>Clase Retención:</label>
                            <select name="clase" id="clase" title="Seleccione clase retención" required style="height: 35px; width: 200px; " class="form-control">
                                <option value="<?php echo $row[11]?>"><?php echo ucwords(mb_strtolower($row[12]))?></option>
                                <?php while ($rowClase = mysqli_fetch_row($clase)){ ?>
                                <option value="<?php echo $rowClase[0];?>"><?php echo ucwords(mb_strtolower($rowClase[1]));?></option>
                                <?php } ?>
                            </select>
                            <!--FACTOR APLICACION-->
                            <label for="factor" class="control-label"><strong style="color:#03C1FB;">*</strong>Factor Aplicación:</label>
                            <select name="factor" id="factor" title="Seleccione factor base" required style="height: 35px; width: 200px; " class="form-control">
                                <option value="<?php echo $row[13]?>"><?php echo ucwords(mb_strtolower($row[14]))?></option>
                                <?php while ($rowFactor = mysqli_fetch_row($factor)){ ?>
                                <option value="<?php echo $rowFactor[0];?>"><?php echo ucwords(mb_strtolower($rowFactor[1]));?></option>
                                <?php } ?>
                            </select>
                            </div>
                        <div class="form-group form-inline" style="margin-top: -15px;">
                            <!--TIPO BASE-->
                            <label for="base" class="control-label"><strong style="color:#03C1FB;">*</strong>Tipo Base:</label>
                            <select name="base" id="base" title="Seleccione tipo base" required style="height: 35px; width: 200px; " class="form-control">
                                <option value="<?php echo $row[15]?>"><?php echo ucwords(mb_strtolower($row[16]))?></option>
                                <?php while ($rowBase = mysqli_fetch_row($base)){ ?>
                                <option value="<?php echo $rowBase[0];?>"><?php echo ucwords(mb_strtolower($rowBase[1]));?></option>
                                <?php } ?>
                            </select>
                            <!--CUENTA-->
                            <label for="cuenta" class="control-label"><strong style="color:#03C1FB;">*</strong>Cuenta:</label>
                            <select name="cuenta" id="cuenta" title="Seleccione cuenta" required style="height: 35px; width: 200px; " class="select2_single form-control">
                                <option value="<?php echo $row[17]?>"><?php echo ucwords(mb_strtolower($row[18].' - '.$row[19]))?></option>
                                <?php while ($rowCuenta = mysqli_fetch_row($cuenta)){ ?>
                                <option value="<?php echo $rowCuenta[0];?>"><?php echo ucwords(mb_strtolower($rowCuenta[1].' - '.$rowCuenta[2]));?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group form-inline" style="margin-top: -15px">
                            <label for="sltHomC" class="control-label">Concepto Ingreso Homologado:</label>
                            <select name="sltHomC" id="sltHomC" title="Seleccione concepto ingreso homologado" style="height: 35px; width: 200px; " class="select2_single form-control">
                                <?php 
                                if(!empty($row[20])){
                                    echo "<option value=\"$row[20]\">".ucwords(mb_strtolower($row[21]))."</option>";
                                    $sqlC = "SELECT id_unico,nombre FROM gf_concepto WHERE clase_concepto=1 AND id_unico != $row[20] AND parametrizacionanno =$anno  ORDER BY nombre ASC";
                                    $resultC = $mysqli->query($sqlC);
                                    while($rowC = mysqli_fetch_row($resultC)){
                                        echo "<option value=\"$rowC[0]\">".ucwords(mb_strtolower($rowC[1]))."</option>";
                                    }                                    
                                    echo "<option value=''>Concepto Ingreso Homologado</option>";
                                }else{
                                    echo "<option value=''>Concepto Ingreso Homologado</option>";
                                    $sqlC = "SELECT id_unico,nombre FROM gf_concepto WHERE clase_concepto=1 AND parametrizacionanno =$anno   ORDER BY nombre ASC";
                                    $resultC = $mysqli->query($sqlC);
                                    while($rowC = mysqli_fetch_row($resultC)){
                                        echo "<option value=\"$rowC[0]\">".ucwords(mb_strtolower($rowC[1]))."</option>";
                                    }                                    
                                }
                                 ?>
                            </select>
                            <!--COD EXOGENA-->
                            <div class="form-group form-inline" id="divCodigo" style="margin-left: 3px">
                                <label for="codE" class="control-label"><strong style="color:#03C1FB;"></strong>Código Exógenas:</label>
                                <input type="text" name="codE" id="codE" style="display: inline;  width: 200px; height: 35px" class="form-inline" placeholder="Código Exógenas" onkeypress="return txtValida(event,'num')"  title="Ingrese Código Exógenas" value="<?php echo $row[22]?>"> 
                            </div>   
                        </div>
                        <div class="form-group form-inline" style="margin-top: -5px;display:none" id="cuentac">
                            <label for="sltcc" class="control-label">Cuenta Crédito:</label>
                            <select name="sltcc" id="sltcc" title="Seleccione Cuenta Crédito" style="height: 35px; width: 200px; " class=" select2_single form-control">
                                <?php 
                                $wh = '';
                                if(!empty($row[23])){
                                    echo '<option value="'.$row[23].'">'.ucwords(mb_strtolower($row[24].' - '.$row[25])).'</option>';
                                    $wh .= ' AND id_unico != '.$row[23];
                                } else { 
                                    echo "<option value=''> - </option>";
                                }
                                $busCuenta2= "SELECT id_unico, codi_cuenta, nombre 
                                        FROM gf_cuenta 
                                        WHERE (movimiento =1 
                                        OR auxiliartercero = 1 
                                        OR centrocosto = 1
                                        OR auxiliarproyecto =1 )        
                                        AND parametrizacionanno = $anno 
                                        AND	clasecuenta = 16  $wh 
                                        ORDER BY codi_cuenta ASC";
                                $cuenta2 = $mysqli->query($busCuenta2);                                
                                while ($rowCuentac = mysqli_fetch_row($cuenta2)){ ?>
                                <option value="<?php echo $rowCuentac[0];?>"><?php echo ucwords(mb_strtolower($rowCuentac[1].' - '.$rowCuentac[2]));?></option>
                                <?php } 
                                 ?>
                            </select>
                        </div>
                        <div class="form-group form-inline" style="margin-top: 0px;">
                            <!--CONCEPTO PAGO-->
                            <label for="conceptoP" class="control-label"><strong style="color:#03C1FB;"></strong>Concepto Pago:</label>
                            <select name="conceptoP" id="conceptoP" title="Seleccione Concepto Pago"  style="height: 35px; width: 200px; " class="select2_single form-control">
                                <?php
                                if(!empty($row[26])){
                                    echo '<option value="'.$row[26].'">'.$row[26].'</option>';
                                } else { 
                                    echo "<option value=''> - </option>";
                                }
                                 ?>

                               <option value="PAGO NOMINA">PAGO NOMINA</option>
                               <option value="INDUSTRIA">INDUSTRIA</option>
                               <option value="COMERCIAL">COMERCIAL</option>
                               <option value="SERVICIOS">SERVICIOS</option>
                               <option value="FINANCIERO">FINANCIERO</option>
                               <option value="">-</option>
                            </select>
                        </div>
                        <div class="form-group form-inline" id="divRmax" style="margin-left: 345px;margin-top: -55px;">
                           <label for="ingM" class="control-label" style="margin-top:-10px;"><strong style="color:#03C1FB; ">   </strong>Rango Mínimo Ingresos:</label>
                           <input type="text" name="rangMin" id="rangMin" style="display: inline;  width: 200px; height: 35px" class="form-inline" placeholder="Rango Mínimo Ingresos" onkeypress="return txtValida(event,'decimales')"  maxlength="18" title="Ingrese Rango Mínimo Ingresos"   value="<?php echo $row[28]?>">
                        </div> 
                        <div class="form-group form-inline" id="divRmin" style="margin-left: -15px;margin-top: -5px;">
                           <label for="ingMax" class="control-label" style="margin-top:-10px;"><strong style="color:#03C1FB; ">  </strong>Rango Máximo  Ingresos:</label>
                           <input type="text" name="rangMax" id="rangMax" style="display: inline;  width: 200px; height: 35px" class="form-inline" placeholder="Rango Máximo Ingresos" onkeypress="return txtValida(event,'decimales')"  maxlength="18" title="Ingrese Rango Máximo Ingresos"   value="<?php echo $row[29]?>">
                        </div>  

                        <div class="form-group" style="margin-top: 80px;" align="center"> 
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px;">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>
            </div>
            <div class="col-sm-3 col-sm-3" style="margin-top:-22px">
                <table class="tablaC table-condensed" >
                    <thead>
                      <tr>
                        <th><h2 class="tituloform" align="center" style=" font-size:17px;">Consultas</h2></th>
                        <th><h2 class="tituloform" align="center" style=" font-size:17px;">Adicional</h2></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td align="center">
                            <div class="btnConsultas" style="margin-bottom: 1px;"><a href="<?php echo "informes/INF_RETENCIONES_APLICADAS.php?id=".$id?>">RETENCIONES <br/>PRACTICADAS</a></div>
                        </td>
                        <td>
                            <a href="GF_CLASE_RETENCION.php"><button class="btn btn-primary btnInfo" >CLASE RETENCIÓN</button></a>
                        </td>
                      </tr>
                      <tr>
                        <td align="center"></td>
                        <td>
                          <a href="registrar_GF_CUENTA_P.php" class="btn btn-primary btnInfo">CUENTA</a>
                        </td>
                      </tr>
                      <tr>
                        <td align="center"></td>
                        <td>
                          <a href="registrar_GF_FACTOR_APLICACION.php" class="btn btn-primary btnInfo">FACTOR APLICACIÓN</a>
                        </td>
                      </tr>
                      <tr>
                        <td align="center"></td>
                        <td>
                          <a href="registrar_GF_TIPO_BASE.php" class="btn btn-primary btnInfo">TIPO BASE</a>
                        </td>
                      </tr>
                    </tbody>
                </table>                
              </div>
  </div>
</div>
<?php require_once 'footer.php';?>
    <script src="js/select/select2.full.js"></script>
    <script>
        $(document).ready(function() {
          $(".select2_single").select2({
            allowClear: true
          });
        if($("#clase").val()==1){
            $("#cuentac").css('display','block');
        }
        if($("#base_ing").val()==1){
                $("#divRmin").css('display', 'block');
                $("#divRmax").css('display', 'block');
        }else{
                $("#divRmin").css('display', 'none');
                $("#divRmax").css('display', 'none');
                $("#divRmin").val('');
                $("#divRmax").val('');
        }
        });
    </script>
<script>
    var validarNum1 = function (event){
    event = event || window.event;
    var charCode = event.keyCode || event.which;
    var first = (charCode <= 57 && charCode >= 48);
    var numero = document.getElementById('porcentajeB').value;
    var char = parseFloat(String.fromCharCode(charCode));
    var num = parseFloat(numero+char);
    var com = parseFloat('100');
    if(numero == ''){ 
        return first; 
    } else {
        var match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
        var dec = match[0].length;
        if(dec<=3){
            if(num < com){
                if (charCode ==46){
                    var element = event.srcElement || event.target;
                    if(element.value.indexOf('.') == -1){
                    return (charCode =46);
                    }else{
                       return first; 
                    }
                    } else {
                    return first;
                }
            } else {
                if(num <=100){
                    return first;
                }else{
                    return false;
                }
            }
        } else { 
            return false;
        }
    }
       
    
}
</script> 
<script>
    var validarNum2 = function (event){
    event = event || window.event;
    var charCode = event.keyCode || event.which;
    var first = (charCode <= 57 && charCode >= 48);
    var numero = document.getElementById('porcentajeA').value;
    var char = parseFloat(String.fromCharCode(charCode));
    var num = parseFloat(numero+char);
    var com = parseFloat('100');
    if(numero == ''){ 
        return first; 
    } else {
        var match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
        var dec = match[0].length;
        if(dec<=3){
            if(num < com){
                if (charCode ==46){
                    var element = event.srcElement || event.target;
                    if(element.value.indexOf('.') == -1){
                    return (charCode =46);
                    }else{
                       return first; 
                    }
                    } else {
                    return first;
                }
            } else {
                if(num <=100){
                    return first;
                }else{
                    return false;
                }
            }
        } else { 
            return false;
        }
    }
       
    
}
$("#clase").change(function(){
    if($("#clase").val()==1){
        $("#cuentac").css('display','block');
        $("#sltcc").val('');
    } else {
        $("#cuentac").css('display','none');
        $("#sltcc").val('');
    }
    var id=$("#clase").val();
    let form_data4 = {estruc: 39, id: id};
            $.ajax({
                type:"POST",
                url:"jsonPptal/consultas.php",
                data:form_data4,
                success: function(data){
                    if(data==1) {
                        $("#divRmin").css('display', 'block');
                        $("#divRmax").css('display', 'block');
                        $("#divRmin").val('');
                        $("#divRmax").val('');
                        $("#rangMin").val('');
                        $("#rangMax").val('');
                    }else{
                        $("#rangMin").val('');
                        $("#rangMax").val('');
                        $("#divRmin").css('display', 'none');
                        $("#divRmax").css('display', 'none');
                        $("#divRmin").val('');
                        $("#divRmax").val('');
                    }

                }
            });
})
</script> 
</body>
</html>

