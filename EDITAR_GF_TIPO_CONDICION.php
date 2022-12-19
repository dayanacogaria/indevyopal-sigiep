<?php 
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes
require_once 'head.php';
	require_once 'Conexion/conexion.php';


	$id = "";

	if (isset($_GET["id"])) {

		$id = $_GET["id"];

		$sql = "SELECT Id_Unico, Nombre FROM gf_tipo_condicion WHERE md5(Id_Unico) = '$id'";
	}

	$rs = $mysqli->query($sql);

	$row = mysqli_fetch_row($rs);

	require_once 'head.php';

?>

	<title>Modificar Tipo Condicion</title>
</head>
<body>
	
	<div class="container-fluid text-center">

		<div class="row content">
			
			<?php require_once 'menu.php'; ?>

			<div class="col-sm-10 text-left">
				
				<h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">
					Modificar Tipo Condición
				</h2>

				<div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
				
					<form name="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="json/modificarTipoCondicionJson.php">
				
						<input type="hidden" name="id" value="<?php echo $row[0] ?>">
						
						<p align="center" style="margin-bottom: 25px; margin-top:25px; margin-left:30px; font-size:80%;">
							Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
						</p>

						<div class="form-group" style="margin-top: -10px;">
							<label for="nombre" class="col-sm-5 control-label">
								<strong style="color:#03C1FB;">*</strong>Nombre:
							</label>
							<input type="text" name="nombre" maxlength="100" id="nombre" class="form-control" title="Ingrese el nombre" value="<?php echo ucwords(mb_strtolower($row[1])); ?>" onkeypress="return txtValida(event,'car')" placeholder="Nombre">
						</div>

						<div class="form-group" style="margin-top: 10px;">
             				 <label for="no" class="col-sm-5 control-label"></label>
             				   <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
          			    </div>

						<input type="hidden" name="MM_insert">

					</form>

				</div>

			</div>

		</div>
		
	</div>

	<?php require_once 'footer.php'; ?>

</body>
</html>