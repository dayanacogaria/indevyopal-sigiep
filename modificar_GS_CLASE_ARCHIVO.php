<?php 
/**
* modificar_GS_CLASE_ARCHIVO.php
* 
* Formulario para modificar la clase de archivo
* 
* @author Alexander Numpaque
* @package Clase Archivo
* @version $Id: modificar_GS_CLASE_ARCHIVO.php 001 2017-05-17 Alexander Numpaque$
**/
require ('head.php');					//Cabeza del formulario
require ('Conexion/conexion.php');		//Conexión de base de datos
$id_unico = "";
$nombre = "";
$tipo_a = "";
$nombre_a = "";
$linea_I = "";
if(!empty($_GET['id'])){
	$id = '"'.$mysqli->real_escape_string(''.$_GET['id'].'').'"';
	$sql = "SELECT 		cl.id_unico, cl.nombre, cl.id_tipo_archivo, ta.nombre, cl.linea_inicial
			FROM 		gs_clase_archivo cl 
			LEFT JOIN 	gs_tipo_archivo ta ON cl.id_tipo_archivo = ta.id_unico
			WHERE 		md5(cl.id_unico) = $id";
	$result = $mysqli->query($sql);
	$row = mysqli_fetch_row($result);
	$id_unico = $row[0];
	$nombre = $row[1];
	$tipo_a = $row[2];
	$nombre_a = $row[3];
	$linea_I = $row[4];
}
?>
	<title>Modificar Clase Archivo</title>
	<style type="text/css" media="screen">
		body {font-size: 12px}
	</style>
</head>
<body>
	<div class="container-fluid text-left">
		<div class="row content">
			<?php require ('menu.php'); ?>
			<div class="col-sm-8 form-horizontal">
				<h2 id="forma-titulo3" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:0px" align="center">Registrar Clase Archivo</h2>
				<div class="client-form contenedorForma">
					<form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="controller/controllerGSClaseArchivo.php?action=modify">
						<p align="center" style="margin-bottom: 25px; margin-top: 15px; margin-left: 30px; font-size: 80%">
							Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
						</p>
						<input type="hidden" name="id" value="<?php echo $id_unico ?>">
						<div class="form-group">
							<label for="txtNombre" class="col-sm-5 control-label"><strong class="obligado">*</strong>Nombre:</label>
							<input type="text" name="txtNombre" id="txtNombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" class="form-control" style="width: 35%" maxlength="1000" required="" title="Ingrese nombre de tipo archivo" value="<?php echo $nombre ?>">
						</div>
						<div class="form-group" style="margin-top: -10px">
							<label for="sltTipoA" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tipo Archivo</label>
							<select class="form-control" name="sltTipoA" id="sltTipoA" title="Seleccione tipo archivo" required="" style="width: 35%">
								<?php 
								if(empty($tipo_a)){
									echo "<option value=\"\">Tipo Archivo</option>";
									$sql = "SELECT id_unico,nombre FROM gs_tipo_archivo ORDER BY nombre ASC";
									$result = $mysqli->query($sql);
									while ($row = mysqli_fetch_row($result)) {
										echo "<option value=\"".$row[0]."\">".ucwords(mb_strtolower($row[1]))."</option>";
									}									
								}else{
									echo "<option value=\"".$tipo_a."\">".ucwords(strtolower($nombre_a))."</option>";
									$sql = "SELECT id_unico,nombre FROM gs_tipo_archivo WHERE id_unico != $tipo_a ORDER BY nombre ASC";
									$result = $mysqli->query($sql);
									while ($row = mysqli_fetch_row($result)) {
										echo "<option value=\"".$row[0]."\">".ucwords(mb_strtolower($row[1]))."</option>";
									}									
								}
								 ?>
							</select>
						</div>
						<div class="form-group" style="margin-top: -10px">
							<label for="txtLineaI" class="col-sm-5 control-label"><strong class="obligado">*</strong>Linea Inicial:</label>
							<input type="text" name="txtLineaI" id="txtLineaI" onkeypress="return txtValida(event,'num')"  placeholder="Linea Inicial" class="form-control" style="width: 35%" maxlength="1000" required="" title="Ingrese el numero de la fila en donde se empezara a leer el archivo" value="<?php echo $linea_I ?>">
						</div>
						<div class="form-group">
							<div class="col-sm-5"></div>
							<button type="submit" class="btn btn-primary sombra" style=" margin-bottom: 10px; margin-left: 0px;">Guardar</button>
						</div>
					</form>					
				</div>
			</div>
			<div class="col-sm-8 col-sm-2 text-center">
				<table class="tablaC table-condensed" style="margin-left: -30px;margin-top: -22px">
                    <thead>
                        <tr>
                            <th>
                                <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                            </th>                                
                        </tr>
                    </thead>
                    <tbody>
                        <tr>                                
                            <td>
                                <a href="registrar_GS_DETALLE_ARCHIVO.php?clase_a=<?php echo md5($id_unico) ?>" class="btn btn-primary btnInfo">DETALLE ARCHIVO</a>
                            </td>
                        </tr>                                                       
                    </tbody>
                </table>
			</div>
		</div>
	</div>
	<?php require ('footer.php'); ?>
</body>
</html>
