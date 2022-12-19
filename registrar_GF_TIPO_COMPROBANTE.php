<?php
require_once('Conexion/conexion.php');
require_once 'head.php';
$compania = $_SESSION['compania'];
$clase = "SELECT id_unico, nombre FROM gf_clase_contable ORDER BY nombre ASC";
$claseC = $mysqli->query($clase);
$forma = "SELECT id_unico, nombre FROM gf_tipo_documento WHERE compania = $compania ORDER BY nombre ASC";
$formato = $mysqli->query($forma);
?>
<title>Registrar Tipo Comprobante</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
</head>
<body> 
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style=" margin-right: 4px; margin-left: 4px;margin-top: 0px">Registrar Tipo Comprobante Contable</h2>
                <a href="listar_GF_TIPO_COMPROBANTE.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">  Tipo</h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-top: -1px" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarTipoComprobanteJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>         
                        <div class="form-group" style="margin-top: -20px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Sigla:</label>
                            <input type="text" name="sigla" id="sigla" class="form-control" maxlength="100" title="Ingrese la sigla" onkeypress="return txtValida(event, 'car')" placeholder="Sigla" required>
                        </div>
                        <div class="form-group" style="margin-top: -20px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" maxlength="200" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required>
                        </div>
                        <div class="form-group" style="margin-top:-20px">
                            <label for="reten" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Retención:</label>
                            <input type="radio" name="reten" id="reten"  value="1" checked>SI
                            <input type="radio" name="reten" id="reten" value="2" checked>NO
                        </div>
                        <div class="form-group form-horizontal"  style="margin-top: -10px">  
                            <label for="inter" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Interfaz:</label>
                            <input type="radio" name="inter" id="inter"  value="1" checked>SI
                            <input type="radio" name="inter" id="inter" value="2" checked>NO
                        </div>
                        <div class="form-group form-horizontal"  style="margin-top: -10px">  
                            <label for="nif" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>NIIF:</label>
                            <input type="radio" name="nif" id="nif"  value="1" checked>SI
                            <input type="radio" name="nif" id="nif" value="2" checked>NO
                        </div>
                        <div class="form-group " style="margin-top: -10px">
                            <label for="claseC" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Contable:</label>
                            <select name="claseC" id="claseC" class="form-control col-sm-1" style="width: 300px;height: 34px" title="Seleccione la clase contable" required>
                                <option value="">Clase Contable</option>
                                <?php while ($rowC = mysqli_fetch_assoc($claseC)) { ?>
                                    <option value="<?php echo $rowC['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowC['nombre'])));
                            } ?></option>;
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="formato" class="col-sm-5 control-label">Tipo Documento:</label>
                            <select name="formato" id="formato" class="form-control col-sm-1" style="width: 300px;height: 34px" title="Seleccione el formato">
                                <option value="">Tipo Documento</option>
                                <?php while ($rowF = mysqli_fetch_assoc($formato)) { ?>
                                    <option value="<?php echo $rowF['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowF['nombre'])));
                            } ?></option>;
                            </select> 
                        </div>            
                        <div class="form-group" style="margin-top: -10px">
                            <label for="sltTipoC" class="control-label col-sm-5">
                                Tipo Comprobante Presupuestal:
                            </label>
                            <select name="sltTipoC" id="sltTipoC" class="form-control col-sm-1" style="width: 300px;height: 34px;" title="Seleccione tipo comprobante presupuestal">
                                <?php
                                echo "<option value=''>Tipo Comprobante Presupuestal</option>";
                                $sqlTP = "SELECT id_unico,codigo,nombre FROM gf_tipo_comprobante_pptal WHERE compania = $compania 
                                    AND id_unico  NOT IN (SELECT comprobante_pptal FROM gf_tipo_comprobante WHERE compania = $compania  AND comprobante_pptal IS NOT NULL)";
                                $resultTP = $mysqli->query($sqlTP);
                                while ($rowTP = mysqli_fetch_row($resultTP)) {
                                    echo "<option value=" . $rowTP[0] . ">" . ucwords(mb_strtolower($rowTP[2])) . PHP_EOL . $rowTP[1] . "</option>";
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
                                echo "<option value=''>Tipo Comprobante Homologado</option>";
                                $sqlTH = "SELECT id_unico,sigla,nombre FROM gf_tipo_comprobante 
                                    WHERE compania = $compania
                                    and id_unico NOT IN (SELECT tipo_comp_hom FROM gf_tipo_comprobante 
                                        WHERE compania = 1 AND tipo_comp_hom IS NOT NULL)";
                                $resultTH = $mysqli->query($sqlTH);
                                while ($rowTH = mysqli_fetch_row($resultTH)) {
                                    echo "<option value=" . $rowTH[0] . ">" . ucwords(mb_strtolower($rowTH[2])) . PHP_EOL . $rowTH[1] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group form-horizontal"  style="margin-top: -10px">  
                            <label for="predial" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Interfaz Predial:</label>
                            <input type="radio" name="predial" id="predial"  value="1" checked>SI
                            <input type="radio" name="predial" id="predial" value="2" checked>NO
                        </div>
                        <div class="form-group form-horizontal"  style="margin-top: -10px">  
                            <label for="comercio" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Interfaz Comercio:</label>
                            <input type="radio" name="comercio" id="comercio"  value="1" checked>SI
                            <input type="radio" name="comercio" id="comercio" value="2" checked>NO
                        </div>
                        <div class="form-group form-horizontal"  style="margin-top: -10px">  
                            <label for="reteica" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Interfaz Reteica:</label>
                            <input type="radio" name="reteica" id="reteica"  value="1" checked>SI
                            <input type="radio" name="reteica" id="reteica" value="2" checked>NO
                        </div>
                        <div class="form-group form-horizontal"  style="margin-top: -10px">  
                            <label for="amortizacion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Amortización:</label>
                            <input type="radio" name="amortizacion" id="amortizacion"  value="1" checked>SI
                            <input type="radio" name="amortizacion" id="amortizacion" value="2" checked>NO
                        </div>
                        <div class="form-group form-horizontal"  style="margin-top: -10px">  
                            <label for="traslado" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Traslado:</label>
                            <input type="radio" name="traslado" id="traslado"  value="1" checked>SI
                            <input type="radio" name="traslado" id="traslado" value="2" checked>NO
                        </div>
                        <div class="form-group form-horizontal"  style="margin-top: -10px">  
                            <label for="aportes" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Aportes:</label>
                            <input type="radio" name="aportes" id="aportes"  value="1" checked>SI
                            <input type="radio" name="aportes" id="aportes" value="2" checked>NO
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>     
            </div>
        </div>
    </div>
    <?php require_once 'footer.php' ?>  
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
        $("#sltTipoC,#sltTipoCompH").select2({
            placeholder: 'Tipo Comprobante Presupuestal',
            allowClear: true
        });
    </script>
</body>
</html>

