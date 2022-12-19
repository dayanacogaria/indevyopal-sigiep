<?php 
/**
* listar_GS_CLASE_ARCHIVO.php
* 
* Formulario para listar la clases de archivo
* 
* @author Alexander Numpaque
* @package Clase Archivo
* @version $Id: listar_GS_CLASE_ARCHIVO.php 001 2017-05-17 Alexander Numpaque$
*/

require ('head_listar.php');					//Archivo anexo de la cabeza del formulario
require ('Conexion/conexion.php');				//Archivo anexo de la conexión
 ?>
	<title>Listar Clase Archivo</title>
</head>
<body>
	<div class="container-fluid">
		<div class="row content">
			<?php require ('menu.php'); ?>
			<div class="col-sm-10 text-left">
				<h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Listar Clase Archivo</h2>
				<div class="table-responsive">
					<div class="table-responsive">
						<table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
							<thead>
								<tr>
									<td class="oculto"></td>
									<td width="7%" class="cabeza"></td>
									<td class="cabeza"><strong>Nombre</strong></td>
									<td class="cabeza"><strong>Tipo Archivo</strong></td>
									<td class="cabeza"><strong>Linea Inicial</strong></td>
								</tr>
								<tr>
									<th class="oculto"></th>
									<th width="7%" class="cabeza"></th>
									<th class="cabeza">Nombre</th>
									<th class="cabeza">Tipo Archivo</th>
									<th class="cabeza">Linea Inicial</th>
								</tr> 
							</thead>
							<tbody>
								<?php 
								$sql = "SELECT 		cl.id_unico,cl.nombre,ta.nombre,cl.linea_inicial 
										FROM 		gs_clase_archivo cl 
										LEFT JOIN 	gs_tipo_archivo ta ON cl.id_tipo_archivo = ta.id_unico 
										ORDER BY 	cl.nombre ASC";
								$result = $mysqli->query($sql);
								while ($row = mysqli_fetch_row($result)) {
									echo "\n\t<tr>";
									echo "\n\t\t<td class=\"campos oculto\"></td>";
									echo "\n\t\t<td class=\"campos\">";
									echo "\n\t\t\t<a href=\"#?".md5($row[0])."\" onclick=\"eliminar(".$row[0].")\" title=\"Eliminar\" class=\"glyphicon glyphicon-trash\"></a>";
									echo "\n\t\t\t<a href=\"modificar_GS_CLASE_ARCHIVO.php?id=".md5($row[0])."\" title=\"Eliminar\" class=\"glyphicon glyphicon-edit\"></a>";
									echo "\n\t\t</td>";
									echo "\n\t\t<td class=\"campos\">".ucwords(mb_strtolower($row[1]))."</td>";
									echo "\n\t\t<td class=\"campos\">".ucwords(mb_strtolower($row[2]))."</td>";
									echo "\n\t\t<td class=\"campos text-right\">".ucwords(mb_strtolower($row[3]))."</td>";
									echo "\n\t\t</td>";
									echo "\n\t</tr>";
								}
								 ?>
							</tbody>
						</table>
						<div align="right">
							<a href="registrar_GS_CLASE_ARCHIVO.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo
							</a>
						</div>
					</div>
				</div>
			</div>
			<?php require ('footer.php'); ?>			
		</div>
	</div>
	<div class="modal fade" id="myModal" role="dialog" align="center" >
	    <div class="modal-dialog">
	      	<div class="modal-content">
	        	<div id="forma-modal" class="modal-header">
	          		<h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
	        	</div>
	        	<div class="modal-body" style="margin-top: 8px">
	          		<p>¿Desea eliminar el registro seleccionado?</p>
	        	</div>
	        	<div id="forma-modal" class="modal-footer">
	          		<button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
	          		<button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
	        	</div>
	      	</div>
	    </div>
	</div>
  	<div class="modal fade" id="myModal1" role="dialog" align="center" >
	    <div class="modal-dialog">
	      	<div class="modal-content">
	        	<div id="forma-modal" class="modal-header">
	          		<h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
	        	</div>
	        	<div class="modal-body" style="margin-top: 8px">
	          		<p>Información eliminada correctamente.</p>
	        	</div>
	        	<div id="forma-modal" class="modal-footer">
	          		<button type="button" id="ver1" onclick="reload_page()" class="btn" style="color: #000; margin-top: 2px" >Aceptar</button>
	        	</div>
	      	</div>
	    </div>
  	</div>
	<div class="modal fade" id="myModal2" role="dialog" align="center" >
	    <div class="modal-dialog">
	      	<div class="modal-content">
		        <div id="forma-modal" class="modal-header">
		          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
	    	    </div>
	        	<div class="modal-body" style="margin-top: 8px">
	          		<p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
	        	</div>
	        	<div id="forma-modal" class="modal-footer">
	          		<button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
	        	</div>
	      </div>
	    </div>
	</div>
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript" charset="utf-8">
		/**
		* eliminar
		*
		* función para captura y envio de la variable id para eliminar el registro seleccionado
		*
		* @author Alexander Numpaque
		* @package Clase Archivo
		*/
		function eliminar(id) {
			var result = '';
			$("#myModal").modal('show');
			$("#ver").click(function(){
				$("#mymodal").modal('hide');
				var form_data = {
					id:id,
					action:'delete'
				};
				var result = '';
				$.ajax({
					type:'POST',
					url:'controller/controllerGSClaseArchivo.php',
					data:form_data,
					success: function(data,textStatus,jqXHR){												
						result = JSON.parse(data);
						if(result == true){
							$("#myModal1").modal('show');
						}else{
							$("#myModal2").modal('show');
						}						
					}
				}).error(function(data,textError,jqXHR) {
					console.log('Error '+data+' error:'+textError);
				});
			});
		}
		/**
		* reload_page
		*
		* función para recargar la pagina
		**/
		function reload_page(){
			window.location.reload();
		}
	</script>
</body>
</html>