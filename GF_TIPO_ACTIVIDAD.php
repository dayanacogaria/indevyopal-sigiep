	<?php 
		require_once 'Conexion/conexion.php';

		//session_start();

		require_once 'head.php';
	 ?>
	<title>Registrar Tipo Actividad</title>
</head>
<body>

	<?php require_once 'menu.php'; ?>

	<div class="container-fluid text-center">
		<div class="row content">
			<?php require_once 'menu.php'; ?>

			<!-- Inicio de Formulario -->
			<div class="col-sm-10 text-left">
				
				<!-- Inicio de Titulo -->
				<h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"">Registrar Tipo Actividad</h2>
				<!-- Fin de Titulo -->

				<div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
					
					<form name="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="json/registrarTipoActividadJson.php">
						
						<p align="center" style="margin-bottom: 25px; margin-top:25px; margin-left:30px; font-size:80%;">
							Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
						</p>
                                                
                                                <div class="form-group" style="margin-top: -10px;">
							<label for="codigoActividad" class="col-sm-5 control-label">
								<strong style="color:#03C1FB;">*</strong>Codigo Actividad:
							</label>
							<input type="number" name="codigoActividad" maxlength="100" id="codigoActividad" class="form-control" title="Ingrese código Actividad" onkeypress="return txtValida(event,'num')" placeholder="Código Actividad">
						</div>
                                                
						<div class="form-group" style="margin-top: -10px;">
							<label for="nombre" class="col-sm-5 control-label">
								<strong style="color:#03C1FB;">*</strong>Nombre:
							</label>
							<input type="text" maxlength="100" name="nombre" id="nombre" class="form-control" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre">
						</div>
<div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
            </div>
						<input type="hidden" name="MM_insert">

					</form>

				</div>
			</div>
			<!-- Fin de Formulario -->

		</div>
	</div>

	<?php require_once 'footer.php'; ?>

</body>
</html>


