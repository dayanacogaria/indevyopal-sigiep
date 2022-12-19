<?php
require_once('Conexion/conexion.php');
require_once 'head.php';
$id = " ";
$compania = $_SESSION['compania'];
if (isset($_GET["id"])) {
    $id_tipo = (($_GET["id"]));
    $query = "SELECT      tc.id_unico, 
                        tc.nombre, 
                        tc.retencion, 
                        tc.interface, 
                        tc.niif, 
                        tc.clasecontable, 
                        cc.nombre,
                        tc.tipodocumento, 
                        f.nombre,
                        tc.sigla,
                        tc.comprobante_pptal,
                        tcp.nombre,
                        tcp.codigo,
                        tc.tipo_comp_hom,
                        tpc2.nombre,
                        tpc2.sigla , 
                        tc.interfaz_predial, 
                        tc.interfaz_comercio , 
                        tc.interfaz_reteica, 
                        tc.amortizacion, 
                        tc.traslado, 
                        tc.interfaz_aportes 
            FROM        gf_tipo_comprobante tc 
            LEFT JOIN   gf_clase_contable cc          ON  tc.clasecontable  = cc.id_unico 
            LEFT JOIN   gf_tipo_documento f           ON  tc.tipodocumento  = f.id_unico
            LEFT JOIN   gf_tipo_comprobante_pptal tcp ON  tcp.id_unico      = tc.comprobante_pptal
            LEFT JOIN   gf_tipo_comprobante tpc2      ON  tc.tipo_comp_hom  = tpc2.id_unico
            WHERE       md5(tc.Id_Unico)  = '$id_tipo'";
}
$resultado = $mysqli->query($query);
$row = mysqli_fetch_row($resultado);
$clase = "SELECT id_unico, nombre FROM gf_clase_contable WHERE id_unico != '$row[5]' ORDER BY nombre ASC";
$claseC = $mysqli->query($clase);
?>
<title>Modificar Tipo Comprobante</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
</head>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left">
            <h2 id="forma-titulo3" align="center" style=" margin-right: 4px; margin-left: 4px;margin-top: 0px">Modificar Tipo Comprobante Contable</h2>
            <a href="listar_GF_TIPO_COMPROBANTE.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Tipo:<?php echo mb_strtoupper($row[9]) . ' - ' . ucwords(mb_strtolower($row[1])); ?></h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipoComprobanteJson.php">
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Sigla:</label>
                        <input type="text" name="sigla" id="sigla" class="form-control" maxlength="100" title="Ingrese la sigla" onkeypress="return txtValida(event, 'car')" placeholder="Sigla" required value="<?php echo $row[9]; ?>">
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" value="<?php echo $row[1] ?>" required>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="reten" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Retención:</label>
                        <?php if ($row[2] == 1) { ?>
                            <input type="radio" name="reten" id="reten"  value="1" checked>SI
                            <input type="radio" name="reten" id="reten" value="2" >NO                  
                            <?php } else {
                            ?>
                            <input type="radio" name="reten" id="reten"  value="1" >SI
                            <input type="radio" name="reten" id="reten" value="2" checked>NO
                            <?php }
                        ?>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="inter" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Interfaz:</label>
                        <?php if ($row[3] == 1) { ?>
                            <input type="radio" name="inter" id="inter"  value="1" checked>SI
                            <input type="radio" name="inter" id="inter" value="2" >NO                  
                        <?php } else { ?>
                            <input type="radio" name="inter" id="inter"  value="1" >SI
                            <input type="radio" name="inter" id="inter" value="2" checked>NO
                            <?php }
                        ?>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="nif" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>NIIF:</label>
                        <?php if ($row[4] == 1) { ?>
                            <input type="radio" name="nif" id="nif"  value="1" checked>SI
                            <input type="radio" name="nif" id="nif" value="2" >NO                  
                        <?php } else { ?>
                            <input type="radio" name="nif" id="nif"  value="1" >SI
                            <input type="radio" name="nif" id="nif" value="2" checked>NO
                            <?php }
                        ?>
                    </div>
                    <div class="form-group " style="margin-top: -10px">
                        <label for="claseC" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Contable:</label>
                        <select name="claseC" id="claseC" class="form-control col-sm-1" style="width: 300px;height: 34px" title="Seleccione la clase contable" required>
                            <option value="<?php echo $row[5] ?>"><?php echo ucwords(mb_strtolower($row[6])) ?></option>
                            <?php while ($rowC = mysqli_fetch_assoc($claseC)) { ?>
                                <option value="<?php echo $rowC['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowC['nombre'])));
                        } ?></option>;
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="formato" class="col-sm-5 control-label">Tipo Documento:</label>
                        <select name="formato" id="formato" class="form-control col-sm-1" style="width: 300px; height: 34px" title="Seleccione el formato">
                            <?php
                            if (!empty($row[7])) {
                                echo "<option value='" . $row[7] . "'>" . (ucwords(mb_strtolower($row[8]))) . "</option>";
                                $forma = "SELECT id_unico, nombre FROM gf_tipo_documento WHERE "
                                        . "id_unico != '$row[7]' AND compania = $compania ORDER BY nombre ASC";
                                $formato = $mysqli->query($forma);
                                while ($td = mysqli_fetch_row($formato)) {
                                    echo "<option value='" . $td[0] . "'>" . (ucwords(mb_strtolower($td[1]))) . "</option>";
                                }
                                echo "<option value=''>-</option>";
                            } else {
                                echo "<option value=''>-</option>";
                                $sqlTD = "SELECT id_unico,nombre FROM gf_tipo_documento WHERE compania = $compania";
                                $resultTD = $mysqli->query($sqlTD);
                                while ($td = mysqli_fetch_row($resultTD)) {
                                    echo "<option value='" . $td[0] . "'>" . (ucwords(mb_strtolower($td[1]))) . "</option>";
                                }
                            }
                            ?>                
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -10px">
                        <label for="sltTipoC" class="control-label col-sm-5">
                            Tipo Comprobante Presupuestal:
                        </label>
                        <select name="sltTipoC" id="sltTipoC" class="form-control col-sm-1" style="width: 300px;height: 34px;" title="Seleccione tipo comprobante presupuestal">
                            <?php
                            if (empty($row[10])) {
                                echo "<option value=''>-</option>";
                                $sqlTP = "SELECT id_unico,codigo,nombre FROM gf_tipo_comprobante_pptal WHERE compania = $compania";
                                $resultTP = $mysqli->query($sqlTP);
                                while ($rowTP = mysqli_fetch_row($resultTP)) {
                                    echo "<option value=" . $rowTP[0] . ">" . ucwords(mb_strtolower($rowTP[2])) . PHP_EOL . $rowTP[1] . "</option>";
                                }
                            } else {
                                echo "<option value=" . $row[10] . ">" . ucwords(mb_strtolower($row[11])) . PHP_EOL . $row[12] . "</option>";
                                $sqlTP = "SELECT id_unico,codigo,nombre FROM gf_tipo_comprobante_pptal WHERE id_unico != $row[10] and compania = $compania";
                                $resultTP = $mysqli->query($sqlTP);
                                while ($rowTP = mysqli_fetch_row($resultTP)) {
                                    echo "<option value=" . $rowTP[0] . ">" . ucwords(mb_strtolower($rowTP[2])) . PHP_EOL . $rowTP[1] . "</option>";
                                }
                                echo "<option value=''>-</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-top: -10px">
                        <label for="sltTipoC" class="control-label col-sm-5">
                            Tipo Comprobante Homologado:
                        </label>
                        <select name="sltTipoCompH" id="sltTipoCompH" class="form-control col-sm-1" style="width: 300px;height: 34px;" title="Seleccione tipo comprobante con el que se va a homologar">
                            <?php
                            if (empty($row[13])) {
                                echo "<option value=''>-</option>";
                                 $sqlTH = "SELECT id_unico,sigla,nombre FROM gf_tipo_comprobante WHERE compania = $compania";
                                $resultTH = $mysqli->query($sqlTH);
                                while ($rowTH = mysqli_fetch_row($resultTH)) {
                                      echo "<option value=" . $rowTH[0] . ">" . ucwords(mb_strtolower($rowTH[2])) . PHP_EOL . $rowTH[1] . "</option>";
                                }
                            } else {
                                echo "<option value='" . $row[13] . "'>" . ucwords(mb_strtolower($row[14])) . PHP_EOL . $row[15] . "</option>";
                                $sqlTH = "SELECT id_unico,sigla,nombre FROM gf_tipo_comprobante WHERE id_unico != $row[13] AND compania = $compania";
                                $resultTH = $mysqli->query($sqlTH);
                                while ($rowTH = mysqli_fetch_row($resultTH)) {
                                    echo "<option value=" . $rowTH[0] . ">" . ucwords(mb_strtolower($rowTH[2])) . PHP_EOL . $rowTH[1] . "</option>";
                                }
                                echo "<option value=''>-</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="predial" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Interfaz Predial:</label>
                        <?php if ($row[16] == 1) { ?>
                            <input type="radio" name="predial" id="predial"  value="1" checked>SI
                            <input type="radio" name="predial" id="predial" value="2" >NO                  
                        <?php } else { ?>
                            <input type="radio" name="predial" id="predial"  value="1" >SI
                            <input type="radio" name="predial" id="predial" value="2" checked>NO
                            <?php }
                        ?>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="comercio" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Interfaz Comercio:</label>
                        <?php if ($row[17] == 1) { ?>
                            <input type="radio" name="comercio" id="comercio"  value="1" checked>SI
                            <input type="radio" name="comercio" id="comercio" value="2" >NO                  
                        <?php } else { ?>
                            <input type="radio" name="comercio" id="comercio"  value="1" >SI
                            <input type="radio" name="comercio" id="comercio" value="2" checked>NO
                            <?php }
                        ?>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="reteica" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Interfaz Reteica:</label>
                        <?php if ($row[18] == 1) { ?>
                            <input type="radio" name="reteica" id="reteica"  value="1" checked>SI
                            <input type="radio" name="reteica" id="reteica" value="2" >NO                  
                        <?php } else { ?>
                            <input type="radio" name="reteica" id="reteica"  value="1" >SI
                            <input type="radio" name="reteica" id="reteica" value="2" checked>NO
                        <?php } ?>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="amortizacion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Amortización:</label>
                        <?php if ($row[19] == 1) { ?>
                            <input type="radio" name="amortizacion" id="amortizacion"  value="1" checked>SI
                            <input type="radio" name="amortizacion" id="amortizacion" value="2" >NO                  
                        <?php } else { ?>
                            <input type="radio" name="amortizacion" id="amortizacion"  value="1" >SI
                            <input type="radio" name="amortizacion" id="amortizacion" value="2" checked>NO
                        <?php } ?>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="traslado" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Traslado:</label>
                        <?php if ($row[20] == 1) { ?>
                            <input type="radio" name="traslado" id="traslado"  value="1" checked>SI
                            <input type="radio" name="traslado" id="traslado" value="2" >NO                  
                        <?php } else { ?>
                            <input type="radio" name="traslado" id="traslado"  value="1" >SI
                            <input type="radio" name="traslado" id="traslado" value="2" checked>NO
                        <?php } ?>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="aportes" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Aportes:</label>
                        <?php if ($row[21] == 1) { ?>
                            <input type="radio" name="aportes" id="aportes"  value="1" checked>SI
                            <input type="radio" name="aportes" id="aportes" value="2" >NO                  
                        <?php } else { ?>
                            <input type="radio" name="aportes" id="aportes"  value="1" >SI
                            <input type="radio" name="aportes" id="aportes" value="2" checked>NO
                        <?php } ?>
                    </div>
                    <div class="form-group" style="">
                        <label for="no" class="col-sm-5 control-label"></label>
                        <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                    </div>
                    <input type="hidden" name="MM_insert" >
                </form>
            </div>      
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>
<script type="text/javascript" src="js/select2.js"></script>
<script>
    //Clase contable
    $("#claseC").select2({
        placeholder: 'Clase contable',
        allowClear: true
    });
    //Tipo documento
    $("#formato").select2({
        placeholder: 'Tipo Documento',
        allowClear: true
    });
    //Tipo Comprobante
    $("#sltTipoC").select2({
        placeholder: 'Tipo Comprobante Presupuestal',
        allowClear: true
    });
    $("#sltTipoCompH").select2({
        placeholder: 'Tipo Comprobante Homologado',
        allowClear: true
    });
</script>  

</body>
</html>
