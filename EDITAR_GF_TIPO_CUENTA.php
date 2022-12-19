<?php
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes 
//llamado a la cabez del formulario
	require_once 'head.php'; 
	//Llamado a la clase de conexión
	require_once 'Conexion/conexion.php';
	//Variable de sessión
	
	
	//Declaración de la variable que recibe la id 
	$id_tipodoc = "";
	//validación preguntando si la variable enviada del listar viene vacia
	if(isset($_GET["id_tipoC"])){
		//Se carga la variable id con el valor traido de la url
		$id_tipodoc = $_GET["id_tipoC"];
		//Query o sql de consulta 
		$sql = "SELECT Id_Unico, Nombre FROM gf_tipo_cuenta WHERE md5(Id_Unico) = '$id_tipodoc'";
	}
	/*Variable y proceso en el que se llama de manera embebida con la conexión
	el cual pérmite realizar el proceso de consulta
	*/
	$rs = $mysqli->query($sql);
	/*Variable que convertimos en array ya que en esta cargos los resultados como
	  unarray o vector de forma enumerada
	 */
	$row = mysqli_fetch_row($rs);


?>
	<!-- Titulo del formulario -->
	<title>Modificar Tipo Cuenta</title>
</head>
<body>
	<!-- Division del contenedor principal -->
	<div class="container-fluid text-center">
		<!-- Inicion de la fila y contenido -->
		<div class="row content">
			<?php require_once 'menu.php'; ?>
			<!-- Inicio de contenido -->
			<div class="col-sm-10 text-left">
				
				<!-- Inicio de Titulo -->
				<h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"> Modificar Tipo Cuenta</h2>
				<!-- Fin de Titulo -->
				<!-- Inicio de  división o contenedor del formulario-->
				<div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
					<!-- Inicio del formulario -->
					<form name="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="json/modificarTipoCuentaJson.php">
						<!-- Campo oculto para la id -->
						<input type="hidden" name="id" value="<?php echo $row[0] ?>">
						<!-- Fin de campo oculto para la id -->
						<!-- Incio de párrafo de campos obligatorios -->
						<p align="center" style="margin-bottom: 25px; margin-top:25px; margin-left:30px; font-size:80%;">
							Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
						</p>
						<!-- Fin de párrafo de campos obligatorios -->
						<!-- División o Contenedor del campo Nombre -->
						<div class="form-group" style="margin-top: -10px;">
							<label for="nombre" class="col-sm-5 control-label">
								<strong style="color:#03C1FB;">*</strong>Nombre:
							</label>
							<input type="text" name="nombre" maxlength="100" id="nombre" class="form-control" title="Ingrese el nombre" value="<?php echo ucwords(mb_strtolower($row[1])); ?>" onkeypress="return txtValida(event,'car')" placeholder="Nombre">
						</div>
						<!-- Fin de contenedor de campo nombre -->
						<!-- Inicio de Bóton de Guardado -->
						<div class="form-group" style="margin-top: 10px;">
             				 <label for="no" class="col-sm-5 control-label"></label>
             				   <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
          			    </div>
						<!-- Fin de Bóton de Guardado -->
						<!-- Campo oculto-->
						<input type="hidden" name="MM_insert">
						<!-- Fin de Campo oculto-->
					<!-- Fin de Formulario -->
					</form>
				<!-- Fin de división y contenedor del formulario -->
				</div>
			<!-- Fin de Contenido -->
			</div>			
		<!--Fin de la fila y contenido -->	
		</div>		
		<!-- Fin del Contenedor principal -->
	</div>
	<!-- Llamado al pie de pagina -->
	<?php require_once 'footer.php'; ?>
</body>
</html>