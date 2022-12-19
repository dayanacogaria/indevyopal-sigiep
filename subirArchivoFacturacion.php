<?php 
/**
* subirArchivoFacturacion.php
* 
* Formulario para subir el archivo de recaudo predial el cual es un archivo de excel
* 
* @author Alexander Numpaque
* @package Subir Facturacion
* @version $Id: subirArchivoFacturacion.php 001 2017-05-23 Alexander Numpaque$
* */
require ('head.php');
require ('Conexion/conexion.php');
?>
	<title>Cargue Archivo Facturacion</title>
	<script src="dist/jquery.validate.js"></script>
	<link rel="stylesheet" href="css/jquery-ui.css">
	<script src="js/jquery-ui.js"></script>
	<link rel="stylesheet" href="css/select2.css">
	<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
	<script src="js/jquery-ui.js"></script>
	<style type="text/css" media="screen">
		label #sltBanco-error, #flPredial-error, #sltClaseA-error, #sltTipoC-error {
		    display: block;
		    color: #155180;
		    font-weight: normal;
		    font-style: italic;
		}
	</style>
	<script>
		$().ready(function() {
  			var validator = $("#form").validate({
        		ignore: "",
    			errorPlacement: function(error, element) {      
      				$( element )
        				.closest( "form" )
          					.find( "label[for='" + element.attr( "id" ) + "']" )
            					.append( error );
    			}
  			});
		  	$(".cancel").click(function() {
		    	validator.resetForm();
		  	});		  	
		});
	</script>
</head>
<body>
	<div class="container-fluid">
		<div class="row content">
			<?php include ('menu.php'); ?>
			<div class="col-sm-10 form-horizontal">
				<h2 id="forma-titulo3" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:0px" align="center">Cargue Archivo Facturacion</h2>
				<div class="client-form contenedorForma" style="margin-top:-7px;">
					<form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarArchivoFacturacion.php">
						<p align="center" class="parrafoO" style="margin-bottom:10px">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>
                        <div class="form-group">
                        	<label for="sltClaseA" class="control-label col-sm-5"><strong class="obligado">*</strong>Clase Archivo:</label>
                        	<select name="sltClaseA" id="sltClaseA" class="form-control col-sm-1 select2" style="width: 35%" title="Seleccione clase archivo" required>
                        		<?php 
                        		echo "<option value=\"\">Clase Archivo</option>"; 
                        		$sqlCA = "SELECT id_unico, nombre FROM gs_clase_archivo WHERE id_tipo_archivo = 1 ORDER BY nombre ASC";
                        		$resultCA = $mysqli->query($sqlCA);
                        		while($rowCA = mysqli_fetch_row($resultCA)){
                        			echo "<option value=\"".$rowCA[0]."\">".ucwords(mb_strtolower($rowCA[1]))."</option>";
                        		}
                        		?>
                        	</select>
                        </div>
                        <div class="form-group">
                        	<label for="sltTipoC" class="control-label col-sm-5"><strong class="obligado">*</strong>Tipo Factura:</label>
                        	<select name="sltTipoC" id="sltTipoC" class="form-control col-sm-1 select2" style="width: 35%" title="Seleccione tipo factura" required>
								<?php 
								echo "<option value=\"\">Tipo Factura</option>"; 
								$sqlTF = "SELECT id_unico,nombre FROM gp_tipo_factura ORDER BY nombre ASC";
								$resultTF = $mysqli->query($sqlTF);
								while($rowTF = mysqli_fetch_row($resultTF)){
									echo "<option value=\"".$rowTF[0]."\">".ucwords(mb_strtolower($rowTF[1]))."</option>";
								}
								?>
							</select> 
                        </div>						
						<div class="form-group">
							<label for="flFactura" class="control-label col-sm-5"><strong class="obligado">*</strong>Archivo Facturacion:</label>
							<input type="file" class="form-control" name="flFactura" id="flFactura" placeholder="Archivo Predial" style="width: 35%" accept=".xls" title="Cargue el archivo excel con extensión xls" required>
						</div>
						<div class="form-group">
							<div class="col-sm-5"></div>
							<button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-left: 0px;"><li class="glyphicon glyphicon-cloud-upload"></li></button>
							<input type="hidden" name="MM_insert" >
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
	</script>		
</body>
<?php require ('footer.php'); ?>
</html>