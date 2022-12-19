<?php 
/**
 * registrar_GS_TIPO_ARCHIVO.php
 * 
 * Formulario de registro de clase archivo
 * 
 * @author Alexander Numpaque
 * @package Tipo Archivo
 * @version $Id: registrar_GS_TIPO_ARCHIVO.php 001 2017-05-17 Alexander Numpaque$
 * */
require ('head.php');					//Cabeza del formulario
require ('Conexion/conexion.php');		//Archivo de conexión
?>
	<title>Registrar Clase Archivo</title>	
	<style type="text/css" media="screen">
		body {font-size: 12px}
	</style>
</head>
<body>
	<div class="container-fluid text-left">
		<div class="row content">
			<?php require ('menu.php'); ?>
			<div class="col-sm-10 form-horizontal">
				<h2 id="forma-titulo3" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:0px" align="center">Registrar Clase Archivo</h2>
				<div class="client-form contenedorForma">
					<form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="controller/controllerGSClaseArchivo.php?action=insert">
						<p align="center" style="margin-bottom: 25px; margin-top: 15px; margin-left: 30px; font-size: 80%">
							Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
						</p>
						<div class="form-group">
							<label for="txtNombre" class="col-sm-5 control-label"><strong class="obligado">*</strong>Nombre:</label>
							<input type="text" name="txtNombre" id="txtNombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" class="form-control" style="width: 35%" maxlength="1000" required="" title="Ingrese nombre de tipo archivo">
						</div>
						<div class="form-group" style="margin-top: -10px">
							<label for="sltTipoA" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tipo Archivo</label>
							<select class="form-control" name="sltTipoA" id="sltTipoA" title="Seleccione tipo archivo" required="" style="width: 35%">
								<?php 
								echo "<option value=\"\">Tipo Archivo</option>";
								$sql = "SELECT id_unico,nombre FROM gs_tipo_archivo ORDER BY nombre ASC";
								$result = $mysqli->query($sql);
								while ($row = mysqli_fetch_row($result)) {
									echo "<option value=\"".$row[0]."\">".ucwords(mb_strtolower($row[1]))."</option>";
								}
								 ?>
							</select>
						</div>
						<div class="form-group" style="margin-top: -10px">
							<label for="txtLineaI" class="col-sm-5 control-label"><strong class="obligado">*</strong>Linea Inicial:</label>
							<input type="text" name="txtLineaI" id="txtLineaI" onkeypress="return txtValida(event,'num')"  placeholder="Linea Inicial" class="form-control" style="width: 35%" maxlength="1000" required="" title="Ingrese el numero de la fila en donde se empezara a leer el archivo">
						</div>
						<div class="form-group">
							<div class="col-sm-5"></div>
							<button type="submit" class="btn btn-primary sombra" style=" margin-bottom: 10px; margin-left: 0px;">Guardar</button>
						</div>
					</form>					
				</div>
			</div>
		</div>
	</div>
	<?php require ('footer.php'); ?>
</body>
</html>