<?php 
/**
* modificar_GS_TIPO_ARCHIVO.php
* 
* Formulario para modificar el tipo de archivo
* 
* @author Alexander Numpaque
* @package Tipo Archivo
* @version $Id: modificar_GS_TIPO_ARCHIVO.php 001 2017-05-17 Alexander Numpaque$
**/
require ('head.php');					//Cabeza del formulario
require ('Conexion/conexion.php');		//Conexión de base de datos
$id_unico = "";
$nombre = "";
if(!empty($_GET['id'])){
	$id = '"'.$_GET['id'].'"';
	$sql = "SELECT id_unico,nombre FROM gs_tipo_archivo WHERE md5(id_unico) = $id";
	$result = $mysqli->query($sql);
	$row = mysqli_fetch_row($result);
	$id_unico = $row[0];
	$nombre = ucwords(mb_strtolower($row[1]));
}
?>
	<title>Modificar Tipo Archivo</title>
</head>
<body>
	<div class="container-fluid text-left">
		<div class="row content">
			<?php require ('menu.php'); ?>			
			<div class="col-sm-10 form-horizontal">
				<h2 id="forma-titulo3" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:0px" align="center">Modificar Tipo Archivo</h2>
				<div class="client-form contenedorForma">
					<form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="controller/controllerGSTipoArchivo.php?action=modify">
						<input type="hidden" name="id" value="<?php echo $id_unico ?>">
						<p align="center" style="margin-bottom: 25px; margin-top: 15px; margin-left: 30px; font-size: 80%">
							Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
						</p>
						<div class="form-group">
							<label for="txtNombre" class="col-sm-5 control-label">
								<strong class="obligado">*</strong>Nombre:
							</label>
							<input type="text" name="txtNombre" id="txtNombre" onkeyup="txtValida(event,'car')" placeholder="Nombre" class="form-control" style="width: 35%" maxlength="1000" required="" title="Ingrese nombre de tipo archivo" value="<?php echo $nombre ?>">
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