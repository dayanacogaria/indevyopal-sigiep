<?php  
require_once 'head.php';
require_once('Conexion/conexion.php');
$id=$_GET['id'];
//Busqueda registro a modificar
$bus= "SELECT t.id_unico, 
    u.id_unico, 
    u.nombre, 
    p.id_unico, 
    pa.anno, 
    c.nombre, 
    p.fecha_inicial, 
    p.fecha_final, 
    e.id_unico, 
    e.nombre, 
    tt.id_unico, 
    tt.nombre, 
    t.valor, 
    t.porcentaje_iva, 
    t.porcentaje_impoconsumo 
    FROM gp_tarifa t 
    LEFT JOIN gp_uso u ON t.uso= u.id_unico 
    LEFT JOIN gp_periodo p ON t.periodo = p.id_unico 
    LEFT JOIN gp_estrato e ON t.estrato = e.id_unico 
    LEFT JOIN gp_tipo_tarifa tt ON t.tipo_tarifa = tt.id_unico 
    LEFT JOIN gf_parametrizacion_anno pa ON p.anno = pa.id_unico 
    LEFT JOIN gp_ciclo c ON p.ciclo = c.id_unico 
    WHERE md5(t.id_unico)='$id'";
$bus= $mysqli->query($bus);
$row= mysqli_fetch_row($bus);

//uso
$bUso= "SELECT id_unico, nombre FROM gp_uso WHERE id_unico != '$row[1]' ORDER BY nombre";
$bUso = $mysqli->query($bUso);
//PERIODO
$bPeriodo = "SELECT p.id_unico, pa.anno, c.nombre, p.fecha_inicial, p.fecha_final
        FROM gp_periodo p 
        LEFT JOIN gf_parametrizacion_anno pa ON p.anno = pa.id_unico 
        LEFT JOIN gp_ciclo c ON p.ciclo = c.id_unico 
        WHERE p.id_unico != '$row[3]' ORDER BY pa.anno ASC";
$bPeriodo = $mysqli->query($bPeriodo);

//Estrato
$bEstrato= "SELECT id_unico, nombre FROM gp_estrato WHERE id_unico != '$row[8]' ORDER BY nombre ASC ";
$bEstrato= $mysqli->query($bEstrato);

//TIPO TARIFA
$bTipoT= "SELECT id_unico, nombre FROM gp_tipo_tarifa WHERE id_unico != '$row[10]' ORDER BY nombre ASC";
$bTipoT= $mysqli->query($bTipoT);
?>
<title>Modificar tarifa</title>
</head>
<body>

 
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top:-20px">
            <!--Titulo del formulario-->
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar tarifa</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top:-10px" class="client-form">
                <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GP_TARIFAJson.php">
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <!--Ingresa la información-->
                    <input type="hidden" name="id" id="id" value="<?php echo $row[0]?>">
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="uso" class="col-sm-5 control-label">Uso:</label>
                        <select id="uso" name="uso" class="form-control" title="Seleccione Uso">
                            <?php if (empty($row[1])) { ?>
                            <option value="">Uso</option>
                            <?php } else { ?>
                            <option value="<?php echo $row[1]?>"><?php echo ucwords(strtolower($row[2]))?></option>
                            <?php } ?>
                            <?php while ($rowUso= mysqli_fetch_row($bUso)) { ?>
                            <option value="<?php echo $rowUso[0]?>"><?php echo ucwords(strtolower($rowUso[1]))?></option>
                            <?php } ?>
                            <?php if (empty($row[1])) { ?>
                            <?php } else { ?>
                             <option value=""></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="periodo" class="col-sm-5 control-label">Periodo:</label>
                        <select id="periodo" name="periodo" class="form-control" title="Seleccione periodo">
                            <?php if (empty($row[3])) { ?>
                            <option value="">Periodo</option>
                            <?php } else { ?>
                            <option value="<?php echo $row[3]?>"><?php echo $row[4].' - '.ucwords(strtolower($row[5])).' - '.date("d/m/Y", strtotime($row[6])).' - '.date("d/m/Y", strtotime($row[7]));?></option>
                            <?php } ?>
                            <?php while ($rowPer = mysqli_fetch_row($bPeriodo)) { ?>
                            <option value="<?php echo $rowPer[0]?>"><?php echo $rowPer[1].' - '.ucwords(strtolower($rowPer[2])).' - '.date("d/m/Y", strtotime($rowPer[3])).' - '.date("d/m/Y", strtotime($rowPer[4]));?></option>
                            <?php } ?>
                            <?php if (empty($row[3])) { ?>
                            <?php } else { ?>
                             <option value=""></option>
                            <?php } ?>
                            
                            
                        </select>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="estrato" class="col-sm-5 control-label">Estrato:</label>
                        <select id="estrato" name="estrato" class="form-control" title="Seleccione estrato">
                            <?php if (empty($row[8])) { ?>
                           <option value="">Estrato</option>
                            <?php } else { ?>
                            <option value="<?php echo $row[8]?>"><?php echo ucwords(strtolower($row[9]))?></option>
                            <?php } ?>
                            <?php while ($rowEst = mysqli_fetch_row($bEstrato)) { ?>
                            <option value="<?php echo $rowEst[0]?>"><?php echo ucwords(strtolower($rowEst[1]));?></option>
                            <?php } ?>
                            <?php if (empty($row[8])) { ?>
                            <?php } else { ?>
                             <option value=""></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="tipoT" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Tarifa:</label>
                        <select name="tipoT" id="tipoT" class=" form-control" title="Seleccione Tipo Tarifa" required="required">
                            <option value="<?php echo $row[10] ?>"><?php echo ucwords(strtolower($row[11]));?></option>
                            <?php while($rowTipo = mysqli_fetch_row($bTipoT)){?>
                            <option value="<?php echo $rowTipo[0] ?>"><?php echo ucwords(strtolower($rowTipo[1]));}?></option>
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="valor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                        <input type="text" name="valor" id="valor" value="<?php echo $row[12]?>" class="form-control"  maxlength="17" title="Ingrese el valor"  placeholder="Valor" onkeypress="return txtValida(event,'decimales')"required>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="porcIva" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Porcentaje IVA:</label>
                        <input type="text" name="porcIva" id="porcIva" value="<?php echo $row[13]?>" class="form-control"  maxlength="5" title="Ingrese el porcentaje Iva"  placeholder="Porcentaje IVA" onkeypress="return validarNum1(event, true)" required>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="porcIm" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Porcentaje Impoconsumo:</label>
                        <input type="text" name="porcIm" id="porcIm" value="<?php echo $row[14]?>" class="form-control"  maxlength="5" title="Ingrese el porcentaje impoconsumo"  placeholder="Porcentaje Impoconsumo" onkeypress="return validarNum2(event, true)"required>
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="no" class="col-sm-5 control-label"></label>
                        <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                    </div>
                    <input type="hidden" name="MM_insert" >
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php';?>
</body>
</html>

<script>
  var validarNum1 = function (event){
    event = event || window.event;
    var charCode = event.keyCode || event.which;
    var first = (charCode <= 57 && charCode >= 48);
    var numero = document.getElementById('porcIva').value;
    var char = parseFloat(String.fromCharCode(charCode));
    var num = parseFloat(numero+char);
    var com = parseFloat(100);
    
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
                if(num <=com){
                    return first;
                }else{
                    return false;
                }
            }
        } else { 
            return false;
        }
    
       
    
}
  </script> 
 <script>
  var validarNum2 = function (event){
    event = event || window.event;
    var charCode = event.keyCode || event.which;
    var first = (charCode <= 57 && charCode >= 48);
    var numero = document.getElementById('porcIm').value;
    var char = parseFloat(String.fromCharCode(charCode));
    var num = parseFloat(numero+char);
    var com = parseFloat(100);
    
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
                if(num <=com){
                    return first;
                }else{
                    return false;
                }
            }
        } else { 
            return false;
        }
    
       
    
}
  </script> 
