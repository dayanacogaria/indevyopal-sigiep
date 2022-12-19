<?php
require_once ('./head.php');
require_once ('./Conexion/conexion.php');
?>
        <title>Registrar comprobante contable</title>
    </head>
    <body>
        <div class="container-fluid text-left">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 text-left" style="margin-top: -22px;margin-left: -20px">
                    <h2 class="tituloform" align="center">Comprobante Contable</h2>
                    <div class="contenedorForma client-form" style="margin-top: -5px">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="#">
                            <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top:-20px">
                                <label for="txtNumero" class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Número:
                                </label>
                                <?php 
                                $sql = "SELECT COUNT(id_unico) FROM gf_comprobante_cnt";
                                $res = $mysqli->query($sql);
                                $row = mysqli_fetch_row($res);
                                ?>
                                <input type="text" name="txtNumero" id="txtNumero" class="form-control" title="Ingrese número" onkeypress="return txtValida(event,'num')" maxlength="50" placeholder="Número" required style="height: 30px" autofocus=""/>
                            </div>
                            <div class="form-group" style="margin-top:-20px">
                                <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Fecha:
                                </label>
                                <input type="date" name="txtFecha" id="txtFecha" class="form-control" title="Fecha" style="width:138px" />
                            </div>
                            <div class="form-group" style="margin-top:-30px">
                                <label class="col-sm-5 control-label">
                                    Descripción:
                                </label>
                                <textarea name="txtDescripcion" id="txtDescripcion" title="Ingrese descripción" class="form-control" onkeypress="return txtValida(event,'num_car')" maxlength="5000" placeholder="Descripción" style="height: 51px;resize: both;" required=""></textarea>                            
                            </div>
                            <div class="form-group" style="margin-top:-20px">
                                <label class="col-sm-5 control-label">
                                    <strong class="obligado">*</strong>Valor Base:
                                </label>
                                <input type="number" name="txtValorBase" id="txtValorBase" title="Ingrese valor base" placeholder="Valor Base" class="form-control" maxlength="100" onkeypress="return txtValida(event,'num')" required=""/>
                            </div>
                            <div class="form-group" style="margin-top:-20px">
                                <label class="col-sm-5 control-label">
                                    <strong class="obligado">*</strong>Valor Base Iva:
                                </label>
                                <input type="number" name="txtValorBaseI" id="txtValorBaseI" title="Ingrese valor base iva" placeholder="Valor Base" class="form-control" maxlength="100" onkeypress="return txtValida(event,'num')" required=""/>
                            </div>
                            <div class="form-group" style="margin-top:-20px">
                                <label class="col-sm-5 control-label">
                                    <strong class="obligado">*</strong>Valor Neto:
                                </label>
                                <input type="number" name="txtValorNeto" id="txtValorNeto" title="Ingrese valor neto" placeholder="Valor Neto" class="form-control" maxlength="100" onkeypress="return txtValida(event,'num')" required=""/>
                            </div>
                            <div class="form-group" style="margin-top:-20px">
                                <label class="col-sm-5 control-label">
                                    <strong class="obligado">*</strong>Número Contrato:
                                </label>
                                <input type="number" name="txtVNúmeroC" id="txtNúmeroC" title="Ingrese número contrato" placeholder="Número Contrato" class="form-control" maxlength="100" onkeypress="return txtValida(event,'num')" required=""/>
                            </div>
                            <div class="form-group" style="margin-top:-20px">
                                <?php 
                                $query = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_comprobante WHERE id_unico = 1 ORDER BY nombre ASC";
                                $res = $mysqli->query($query);
                                $row = mysqli_fetch_row($res);
                                $sql = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_comprobante WHERE id_unico != $row[0] ORDER BY nombre ASC";
                                $rs = $mysqli->query($sql);
                                ?>
                                <label class="col-sm-5 control-label">
                                    <strong class="obligado">*</strong>Tipo Comprobante:
                                </label>
                                <select class="form-control" name="sltTipoComprobante" id="sltTipoComprobante" title="Seleccione tipo comprobante" style="height:30px">
                                    <option value="<?php echo $row[0]; ?>"><?php echo ucwords(utf8_encode(strtolower($row[1]))); ?></option>
                                    <?php 
                                    while ($row = mysqli_fetch_row($rs)){ ?>
                                        <option value="<?php echo $row[0]; ?>"><?php echo $row[1]; ?></option>
                                    <?php }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top:-20px">
                                <?php 
                                $query = "SELECT DISTINCT T.id_unico,CONCAT(T.nombreuno),
                                          CONCAT(TI.nombre,' - ',T.numeroidentificacion)
                                          FROM gf_tercero T 
                                          LEFT JOIN gf_tipo_identificacion TI ON TI.id_unico = T.tipoidentificacion
                                          WHERE T.id_unico = 2";
                                $res = $mysqli->query($query);
                                $row = mysqli_fetch_row($res);
                                $sql = "SELECT DISTINCT T.id_unico,CONCAT(T.nombreuno),
                                          CONCAT(TI.nombre,' - ',T.numeroidentificacion)
                                          FROM gf_tercero T 
                                          LEFT JOIN gf_tipo_identificacion TI ON TI.id_unico = T.tipoidentificacion
                                          WHERE T.id_unico = 2";
                                $rs = $mysqli->query($sql);
                                ?>
                                <label class="col-sm-5 control-label">
                                    <strong class="obligado">*</strong>Tercero:                                    
                                </label>
                                <select class="form-control" name="sltTercero" id="sltTercero" title="Seleccione tercero" style="height:30px">
                                    <?php 
                                    while($fila = mysqli_fetch_row($rs)){ ?>
                                    <option value="<?php echo $row[0]; ?>"><?php echo ucwords(utf8_encode(strtolower($row[1].' ('.$row[2].')'))); ?></option>
                                    <?php }
                                    ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once './footer.php'; ?>
    </body>
    <script>
        $(document).ready(function(){
            var f = new Date();            
            $("#txtFecha").val(f.getFullYear()+ "-0" + (f.getMonth() +1) + "-" + f.getDate());   
            
            var f = new Date();
            $("#txtNumero").val(f.getFullYear()+'0000'+<?php echo $row[0]+1;?>);
            
            $("#txtValorBase").val(0);
            $("#txtValorBaseI").val(0);
            $("#txtValorNeto").val(0);
        });
    </script>
</html>
