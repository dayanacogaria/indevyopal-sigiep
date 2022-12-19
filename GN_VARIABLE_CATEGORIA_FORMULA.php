<?php
	require_once('Conexion/conexion.php');
	require_once('head_listar.php');
	$id_cat_form = " ";
	if (isset($_GET["id"])) { 
		$id_cat_form = (($_GET["id"]));
		$queryInf = "SELECT id_unico, nombre  
			FROM gn_categoria_formula 
			WHERE md5(id_unico) = '$id_cat_form'";
	}
	$resultado = $mysqli->query($queryInf);
	$row = mysqli_fetch_row($resultado);
	$idCategoria = $row[0];
	$nombreCategoria = $row[1];
	$queryInforme = "SELECT id_unico, nombre  
	FROM gn_variables 
	WHERE categoria = $idCategoria"; 
	$resultado = $mysqli->query($queryInforme);
?>
	<title>Variables</title>
	<style type="text/css">
		.acotado {white-space: normal;}
	</style>
</head>
<body>
	<input type="hidden" id="id_cat_form" value="<?php echo $id_cat_form;?>">
	<div class="container-fluid text-center">
		<div class="row content">    
			<?php require_once ('menu.php'); ?>
			<div class="col-sm-10 text-left">
				<h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;">Variables</h2>
				<a href="modificar_GN_CATEGORIA_FORMULA.php?id=<?php echo $id_cat_form;?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
				<h5 id="forma-titulo3a" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px" align="center"><?php echo utf8_encode(ucwords(strtolower($nombreCategoria))); ?></h5>
				<div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-sm-12">
					<p align="center" style="margin-bottom: 0px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
					<div class="col-sm-5" align="right" style="margin-top: 10px;">
						<label for="nombre" class=" control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
					</div>
					<div class="col-sm-2" style="margin-top: 10px;">
						<input type="text" name="nombre" id="nombre" class="form-control input-sm" maxlength="100" title="Ingrese el nombre" style="width: 180px;" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required>
					</div>
					<div class="col-sm-3" style="margin-top: 16px;">
						<button type="button" id="btnGuardar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1;">Guardar</button>
					</div>
				</div>
				<input type="hidden" id="idCategoria" value="<?php echo $idCategoria;?>"> 
				<script type="text/javascript">      
					$("#btnGuardar").click(function() {
						if($("#nombre").val() != "" && $("#nombre").val() != 0) {
							var nombre = $("#nombre").val();
							var idCategoria = $("#idCategoria").val();
							var form_data = { estruc: 3, nombre: nombre, idCategoria: idCategoria };  
							$.ajax({
								type: "POST",
								url: "estructura_gestor_categorias.php",
								data: form_data,
								success: function(response) {
									if(response == 1) {
										$("#mdlExitoGuar").modal('show');
									}
									else {
										$("#mdlErrorGuar").modal('show');
									}     
								}//Fin succes.
							}); //Fin ajax.
						} else {
							$("#mdlNombreVacio").modal('show');
						}
					});      
				</script>      
				<div class="col-sm-12" style="margin-top: 10px;">
					<div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
						<div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
							<table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
								<thead>
									<tr>
										<td style="display: none;">Identificador</td>
										<td width="30px" align="center"></td>
										<td><strong>Nombre</strong></td>                
									</tr>
									<tr>
										<th style="display: none;">Identificador</th>
										<th width="7%"></th>
										<th>Nombre</th>                
									</tr>
								</thead>
								<tbody>              
									<?php while($row = mysqli_fetch_row($resultado)) { ?>
								 	<tr>
										<td style="display: none;"><?php echo $row[0];?></td>
										<td>
											<a href="#<?php echo $row[0];?>" onclick="javascript:eliminarVariables(<?php echo $row[0];?>);"> <i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
											<a href="#<?php echo $row[0];?>" onclick="javascript:modificarVariable(<?php echo $row[0].",'".ucwords(strtolower(($row[1])))."'";?>);"> <i title="Modificar" class="glyphicon glyphicon-edit" ></i> </a>
										</td>
										<td><?php echo utf8_encode(ucwords(strtolower($row[1])));?></td>									
									</tr>
									<?php }  ?>
								</tbody>
							</table>
						</div>				
					</div> <!-- Cierra Clase table-responsive -->
				</div>				
			</div> <!-- Cierra Clase col-sm-10 text-left -->
		</div>
	</div>
	<div class="modal fade" id="myModalUpdate" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog">						
			<input type="hidden" name="idM" id="idM">
			<div class="modal-content client-form1">
				<div id="forma-modal" class="modal-header">       
					<h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
				</div>
				<div class="modal-body "  align="center">
					<div class="form-group" align="left">
						<label  style="margin-left:150px; display:inline-block;"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
						<input style="display:inline-block; width:250px; font-size: 0.9em; height: 30px; padding: 5px;" type="text" name="nombreM" id="nombreM" title="Ingrese el nombre" class="form-control input-sm" onkeypress="return txtValida(event,'car')" maxlength="100" placeholder="Nombre"  required>
					</div>
					<input type="hidden" id="id" name="id">  
				</div>
				<div id="forma-modal" class="modal-footer">
					<button type="button" onclick="javascript:modificarItem()" class="btn" style="color: #000; margin-top: 2px">Modificar</button>
					<button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="myModal" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div id="forma-modal" class="modal-header">
					<h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
				</div>
				<div class="modal-body" style="margin-top: 8px">
					<p>¿Desea eliminar el registro seleccionado de Variables?</p>
				</div>
				<div id="forma-modal" class="modal-footer">
					<button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
					<button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="myModal1" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div id="forma-modal" class="modal-header">
					<h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
				</div>
				<div class="modal-body" style="margin-top: 8px">
					<p>Información eliminada correctamente.</p>
				</div>
				<div id="forma-modal" class="modal-footer">
					<button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="myModal2" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div id="forma-modal" class="modal-header">
					<h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
				</div>
				<div class="modal-body" style="margin-top: 8px">
					<p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
				</div>
				<div id="forma-modal" class="modal-footer">
					<button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="mdlExitoGuar" role="dialog" align="center"   data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div id="forma-modal" class="modal-header">					
					<h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
				</div>
				<div class="modal-body" style="margin-top: 8px">
					<p>Información guardada correctamente.</p>
				</div>
				<div id="forma-modal" class="modal-footer">
					<button type="button" id="btnExitoGuar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="mdlErorGuar" role="dialog" align="center"   data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div id="forma-modal" class="modal-header">						
					<h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
				</div>
				<div class="modal-body" style="margin-top: 8px">
					<p>No se ha podido guardar la información.</p>
				</div>
				<div id="forma-modal" class="modal-footer">
					<button type="button" id="btnErorGuar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="mdlNombreVacio" role="dialog" align="center"   data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div id="forma-modal" class="modal-header">					
					<h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
				</div>
				<div class="modal-body" style="margin-top: 8px">
					<p>El campo Nombre se encuentra vacío o su valor no es válido. Verifique nuevamente.</p>
				</div>
				<div id="forma-modal" class="modal-footer">
					<button type="button" id="btnNombreVacio" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="mdlNombreVacioMod" role="dialog" align="center"   data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div id="forma-modal" class="modal-header">				
					<h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
				</div>
				<div class="modal-body" style="margin-top: 8px">
					<p>El campo Nombre se encuentra vacío o su valor no es válido. Verifique nuevamente.</p>
				</div>
				<div id="forma-modal" class="modal-footer">
					<button type="button" id="btnNombreVacioMod" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="mdlExitoModf" role="dialog" align="center"   data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div id="forma-modal" class="modal-header">					
					<h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
				</div>
				<div class="modal-body" style="margin-top: 8px">
					<p>Información modificada correctamente.</p>
				</div>
				<div id="forma-modal" class="modal-footer">
					<button type="button" id="btnExitoModf" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="mdlErorModf" role="dialog" align="center"   data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div id="forma-modal" class="modal-header">					
					<h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
				</div>
				<div class="modal-body" style="margin-top: 8px">
					<p>No se ha podido modificar la información.</p>
				</div>
				<div id="forma-modal" class="modal-footer">
					<button type="button" id="btnErorModf" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
				</div>
			</div>
		</div>
	</div>
	<?php require_once ('footer.php'); ?>
	<script type="text/javascript" src="js/menu.js"></script>
	<link rel="stylesheet" href="css/bootstrap-theme.min.css">
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript">
		function eliminarVariables(id) {
			var result = '';
			$("#myModal").modal('show');
			$("#ver").click(function(){
				$("#mymodal").modal('hide');
				$.ajax({
					type:"GET",
					url:"json/eliminar_GN_VARIABLESJson.php?id="+id,
					success: function (data) {
						result = JSON.parse(data);
						if(result==true)
							$("#myModal1").modal('show');
					 	else
							$("#myModal2").modal('show');
					}
				});
			});
		}
		
		function modificarVariable(id, nombre) {
			document.getElementById('idM').value = id;
			document.getElementById('nombreM').value = nombre;
			$("#myModalUpdate").modal('show');
		}

		function modificarItem() {
			var id = document.getElementById('idM').value;
			var nombre = document.getElementById('nombreM').value;
			if(nombre != "" && nombre != 0) {
				var form_data = { estruc: 4, id: id, nombre: nombre };  
				$.ajax({
					type: "POST",
					url: "estructura_gestor_categorias.php",
					data: form_data,
					success: function(response) {
						if(response == 1) {
							$("#myModalUpdate").modal('hide');
							$("#mdlExitoModf").modal('show');
						} else {
							$("#mdlErorModf").modal('show');
						}															
					}//Fin succes.
				}); //Fin ajax. 
			}else{
				$("#mdlNombreVacioMod").modal('show');
			}		
		}
		
		function modal() { $("#myModal").modal('show');}		
			
		$('#ver1').click(function() {
			var id = $("#id_cat_form").val();
			document.location = 'GN_VARIABLE_CATEGORIA_FORMULA.php?id=' + id;
		});
					
		$('#ver2').click(function() {
			var id = $("#id_cat_form").val();
			document.location = 'GN_VARIABLE_CATEGORIA_FORMULA.php?id=' + id;
		});
						
		$('#btnNombreVacio').click(function() {
			$("#nombre").focus();
		});			
		
		$('#btnExitoGuar').click(function() {
			document.location.reload();
		});
					
		$('#btnExitoModf').click(function() {
			document.location.reload();
		});
							
		$('#btnNombreVacioMod').click(function() {
			$("#nombreM").focus();
		});		
	</script>
</body>
</html>

