<?php
/**
* subirArchivoPredial.php
*
* Formulario para subir el archivo de recaudo predial el cual es un archivo de excel
*
* @author Alexander Numpaque
* @package Subir Predial
* @version $Id: subirArchivoPredial.php 001 2017-05-16 Alexander Numpaque$
* */
require ('head.php');
require ('Conexion/conexion.php');
?>
	<title>Cargue Archivo de Recaudo Predial</title>
	<script src="dist/jquery.validate.js"></script>
	<link rel="stylesheet" href="css/jquery-ui.css">
	<script src="js/jquery-ui.js"></script>
	<link rel="stylesheet" href="css/select2.css">
	<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
	<script src="js/jquery-ui.js"></script>
	<style type="text/css" media="screen">
		.client-form input[type="file"]{
            width: 100%;
        }
	</style>
</head>
<body>
	<div class="container-fluid">
		<div class="row content">
			<?php include ('menu.php'); ?>
			<div class="col-sm-10 col-md-10 col-lg-10 form-horizontal">
				<h2 id="forma-titulo3" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:0px" align="center">Cargue Archivo de Recaudo Predial</h2>
				<div class="client-form contenedorForma" style="margin-top:-7px;">
					<form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarRecaudoPredialXLS.php">
						<p align="center" class="parrafoO" style="margin-bottom:10px">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>
                        <div class="form-group">
                        	<label for="sltClaseA" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Clase Archivo:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <select name="sltClaseA" id="sltClaseA" class="form-control select2" title="Seleccione clase archivo" required>
                                    <?php
                                    echo "<option value=\"\">Clase Archivo</option>";
                                    $sqlCA = "SELECT id_unico, nombre FROM gs_clase_archivo WHERE id_tipo_archivo = 2 ORDER BY nombre ASC";
                                    $resultCA = $mysqli->query($sqlCA);
                                    while($rowCA = mysqli_fetch_row($resultCA)){
                                        echo "<option value=\"".$rowCA[0]."\">".ucwords(mb_strtolower($rowCA[1]))."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                        	<label for="sltTipoC" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Tipo Comprobante:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                            	<select name="sltTipoC" id="sltTipoC" class="form-control select2" title="Seleccione tipo comprobante de recaudo" required>
    								<?php
    								echo "<option value=\"\">Tipo Comprobante</option>";
    								$sqlTC = "SELECT tpc.id_unico,CONCAT(tpc.sigla,' ',tpc.nombre) FROM gf_tipo_comprobante tpc  LEFT JOIN gf_clase_contable cls ON cls.id_unico = tpc.clasecontable WHERE tpc.comprobante_pptal IS NOT NULL AND tpc.clasecontable = 9";
                                    $resultC = $mysqli->query($sqlTC);
                                    while($rowTC = mysqli_fetch_row($resultC)){
                                    	echo "<option value=\"".$rowTC[0]."\">".$rowTC[1]."</option>";
                                    }
    								?>
    							</select>
                            </div>
                        </div>
						<div class="form-group">
							<label for="sltBanco" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Banco:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
    							<select name="sltBanco" id="sltBanco" class="form-control select2" title="Seleccione Banco" required>
    								<?php
    								echo "<option value=\"\">Banco</option>";
    								$compania = $_SESSION['compania'];
                                    $param    = $_SESSION['anno'];
    								$sqlB = "SELECT DISTINCT ctb.id_unico, CONCAT_WS(' ',ctb.numerocuenta, ctb.descripcion), ctb.descripcion
                                            FROM      gf_cuenta_bancaria ctb
                                            LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria
                                            WHERE     ctbt.tercero            = $compania
                                            AND       ctb.parametrizacionanno = $param
                                            ORDER BY  ctb.numerocuenta";
                                    $resultB = $mysqli->query($sqlB);
                                    while($rowB = mysqli_fetch_row($resultB)){
                                        if(!empty($rowB[1])){
                                    	   echo "<option value=\"$rowB[0]\">".utf8_encode(ucwords(strtolower($rowB[1])))."</option>";
                                        }
                                    }
    								?>
    							</select>
                            </div>
						</div>
						<div class="form-group">
							<label for="flPredial" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Archivo Predial:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
							    <input type="file" class="form-control" name="flPredial" id="flPredial" placeholder="Archivo Predial" accept=".xls" title="Cargue el archivo excel con extensión xls" required>
                            </div>
						</div>
                        <div class="form-group">
                            <label for="flPredial" class="control-label col-sm-5 col-md-5 col-lg-5">Acumulado:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4 checkbox">
                                <label for="chkAcumDia"><input type="checkbox" name="chkAcum" id="chkAcumDia" value="1">Por día?</label>
                            </div>
                        </div>
						<div class="form-group">
							<div class="col-sm-5 col-md-5 col-lg-5"></div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
							    <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-left: 0px;"><li class="glyphicon glyphicon-cloud-upload"></li></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
	<script>
		$(".select2").select2({allowClear:true});

        $().ready(function() {
            var validator = $("#form").validate({
                ignore: "",
                rules:{
                    sltTipoPredio:"required",
                    txtCodigo:"required"
                },
                messages:{
                    sltTipoPredio: "Seleccione tipo de predio",
                },
                errorElement:"em",
                errorPlacement: function(error, element){
                    error.addClass('help-block');
                },
                highlight: function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                        $(elem).parents(".form-group").addClass("has-error").removeClass('has-success');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight:function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                    }else{
                        $(element).parents(".form-group").addClass('has-success').removeClass('has-error');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        });

        $("#chkAcumDia").click(function(){
            if($("#chkAcumDia").is(':checked')){
                $("#form").attr("action","json/registrarRecaudoPredialAcumuladoXLS.php")
            }else{
                $("#form").attr("action","json/registrarRecaudoPredialXLS.php")
            }
        });
	</script>
</body>
<?php require ('footer.php'); ?>
</html>