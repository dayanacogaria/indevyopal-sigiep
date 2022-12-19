<?php
/**
* registrar_GS_DETALLE_ARCHIVO.php
*
* Formulario de Registro de gs_detalle_archivo
*
* @author  Alexander Numpaque
* @package Detalle Archivo
* @version $Id: registrar_GS_DETALLE_ARCHIVO.php 001 2017-05-18 Alexander Numpaque$
**/

require ('head_listar.php');
require ('Conexion/conexion.php');

$id_clase_a = "";
$nombre_a = "";
$param    = $_SESSION['anno'];
if(!empty($_GET['clase_a'])){
	$clase_a = '"'.$_GET['clase_a'].'"';
	$sqlC_A = "SELECT id_unico,nombre FROM gs_clase_archivo WHERE md5(id_unico) = $clase_a";
	$result = $mysqli->query($sqlC_A);
	$row = mysqli_fetch_row($result);
	$id_clase_a = $row[0];
	$nombre_a = $row[1];
}
?>
	<title>Registrar Detalle Archivo</title>
	<style type="text/css" media="screen">
		#txtColumna {
			width: 15%
		}

		.shadow{
			box-shadow: 1px 1px 1px 1px gray;
  			color:#fff; border-color:#1075C1;
		}

		label #sltConceptoRBCTA-error, #txtColumna-error {
		    display: block;
		    color: #155180;
		    font-weight: normal;
		    font-style: italic;
		    font-size: 11px
		}

		table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
	    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
	    .dataTables_wrapper .ui-toolbar{padding:2px}
	</style>
	<script src="dist/jquery.validate.js"></script>
	<link rel="stylesheet" href="css/jquery-ui.css">
	<script src="js/jquery-ui.js"></script>
	<link rel="stylesheet" href="css/select2.css">
	<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
	<script src="js/jquery-ui.js"></script>
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
<body onload="clean_inputs()">
	<div class="container-fluid" onload="return clean_inputs()">
		<div class="row content">
			<?php require ('menu.php'); ?>
			<div class="col-sm-10 text-left">
				<h2 class="tituloform text-center" style="margin-top: 0px">Detalle Archivo</h2>
				<a href="modificar_GS_CLASE_ARCHIVO.php?id=<?php echo md5($id_clase_a) ?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none;cursor: pointer;" title="Volver"></a>
				<h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Clase Archivo: <?php echo $nombre_a ?></h5>
				<div class="client-form contenedorForma">
					<form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="controller/controllerGSDetalleArchivo.php?action=insert">
						<input type="hidden" name="txtParam" value="<?php echo $param; ?>">
						<p align="center" style="margin-bottom: 15px; margin-left: 40px; font-size: 80%;margin-top: 5px">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
						<div class="form-group form-inline">
							<input type="hidden" name="sltClaseA" id="sltClaseA" value="<?php echo $id_clase_a ?>">
							<label for="sltConceptoRBCTA" class="control-label col-sm-3"><strong class="obligado">*</strong>Concepto Rubro Cuenta :</label>
							<select name="sltConceptoRBCTA" id="sltConceptoRBCTA" title="Seleccione concepto rubro cuenta" class="form-control col-sm-1 select2" required style="width: 30%;">
								<?php
								echo "<option value=\"\">Concepto Rubro Cuenta</option>";
								echo $sql_CR = "SELECT DISTINCT	crc.id_unico as id, CONCAT('Concepto : ',ct.nombre,' - Rubro : ',rb.codi_presupuesto) as concepto_rubro,
														CONCAT('Cuenta Débito : ',ctd.codi_cuenta,' - Cuenta Crédito : ',ctc.codi_cuenta) as Cuentas
											FROM		gf_concepto_rubro_cuenta crc
											LEFT JOIN 	gf_concepto_rubro cbr ON cbr.id_unico = crc.concepto_rubro
											LEFT JOIN 	gf_concepto ct ON ct.id_unico = cbr.concepto
											LEFT JOIN 	gf_rubro_pptal rb ON rb.id_unico = cbr.rubro
											LEFT JOIN 	gf_cuenta ctd ON ctd.id_unico = crc.cuenta_debito
											LEFT JOIN 	gf_cuenta ctc ON ctc.id_unico = crc.cuenta_credito
											WHERE       ct.parametrizacionanno = $param
											AND         rb.parametrizacionanno = $param";
								$result_CR = $mysqli->query($sql_CR);
								while ($row_CR = mysqli_fetch_row($result_CR)) {
									$concepto = ucwords(mb_strtolower($row_CR[1]));
									echo "<option value=\"".$row_CR[0]."\">".$concepto." - ".ucwords(mb_strtolower($row_CR[2]))."</option>";
								}
								?>
							</select>
							<label for="txtColumna" class="col-sm-2 control-label"><strong class="obligado">*</strong>Nº Columna:</label>
							<input type="text" name="txtColumna" id="txtColumna" placeholder="Nº Columna" title="Ingrese columna contando desde A como 0" class="form-control col-sm-1" required>
							<div class="col-sm-1">
								<button type="submit" class="btn btn-primary shadow"><li class="glyphicon glyphicon-floppy-disk"></li></button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="col-sm-10" style="margin-top: 10px">
				<div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
					<div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
						<table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
							<thead>
								<tr>
									<td class="oculto"></td>
									<td width="7%"></td>
									<td class="cabeza"><strong>Concepto Rubro Cuenta</strong></td>
									<td class="cabeza"><strong>Nº Columna</strong></td>
								</tr>
								<tr>
									<th class="oculto"></th>
									<th width="7%"></th>
									<th class="cabeza"></th>
									<th class="cabeza"></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$sqlRC = "SELECT dta.id_unico,dta.concepto_rubro_cuenta,dta.columna FROM gs_detalle_archivo dta WHERE dta.clase = $id_clase_a AND parametrizacionanno = $param";
								$resultRC =  $mysqli->query($sqlRC);
								while ($row = mysqli_fetch_row($resultRC)) {
									echo "\n\t<tr>";
									echo "\n\t\t<td class=\"oculto\"></td>";
									echo "\n\t\t<td class=\"campos\">";
									echo "\n\t\t\t<a onclick=\"eliminar(".$row[0].")\" title=\"Eliminar\" class=\"glyphicon glyphicon-trash\" style=\"cursor:pointer\"></a>";
									echo "\n\t\t\t<a onclick=\"modificar(".$row[0].")\" title=\"Eliminar\" class=\"glyphicon glyphicon-edit\" style=\"cursor:pointer\"></a>";
									echo "\n\t\t</td>";
									echo "\n\t\t<td class=\"campos\">";
								    	$sql_CR = "SELECT DISTINCT	crc.id_unico as id, CONCAT('Concepto : ',ct.nombre,' - Rubro : ',rb.codi_presupuesto) as concepto_rubro, CONCAT('Cuenta Débito : ',ctd.codi_cuenta,' - Cuenta Crédito : ',ctc.codi_cuenta) as Cuentas
											FROM		gf_concepto_rubro_cuenta crc
											LEFT JOIN 	gf_concepto_rubro cbr ON cbr.id_unico = crc.concepto_rubro
											LEFT JOIN 	gf_concepto ct ON ct.id_unico = cbr.concepto
											LEFT JOIN 	gf_rubro_pptal rb ON rb.id_unico = cbr.rubro
											LEFT JOIN 	gf_cuenta ctd ON ctd.id_unico = crc.cuenta_debito
											LEFT JOIN 	gf_cuenta ctc ON ctc.id_unico = crc.cuenta_credito
											WHERE crc.id_unico = $row[1]
											AND   ct.parametrizacionanno = $param
											AND   rb.parametrizacionanno = $param";
										$result_CR = $mysqli->query($sql_CR);
										$row_CR = mysqli_fetch_row($result_CR);
										echo ucwords(mb_strtolower($row_CR[1]." - ".$row_CR[2]));
									echo "\n\t\t</td>";
									echo "\n\t\t<td class=\"text-right\">".$row[2]."</td>";
									echo "\n\t</tr>";
								}
								 ?>
							</tbody>
						</table>
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
    <script type="text/javascript" src="js/select2.js"></script>
	<script>
		$(".select2").select2({allowClear:true});
		/**
		* return_page
		*
		* Función para devolver a la página anterior
		*/
		function return_page(){
			window.history.go(-1);
		}

		/**
		* eliminar
		*
		* función para captura y envio de la variable id para eliminar el registro seleccionado
		*
		* @author Alexander Numpaque
		* @package Clase Archivo
		*/
		function eliminar(id_unico){
			var result = '';
			$("#myModal").modal('show');
			$("#ver").click(function(){
				$("#mymodal").modal('hide');
				var form_data = {
					id:id_unico,
					action:'delete'
				};
				var result = '';
				$.ajax({
					type:'POST',
					url:'controller/controllerGSDetalleArchivo.php',
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
		* modificar
		*
		* Función para modificar los valores del detalle seleccionado
		* @param id_unico id del detalle seleccionado para modificar
		**/
		function modificar(id_unico){
			var form_data={
              id_unico:id_unico
          };
          $.ajax({
              type: 'POST',
              url: "modalUpdateGSDetalleArchivo.php",
              data: form_data,
              success: function (data, textStatus, jqXHR) {
                  $("#modalMod").html(data);
                  $(".Xmod").modal('show');
              }
          });
		}

		/**
		* reload_page
		*
		* Función para recargar la pagina
		**/
		function reload_page(){
			window.location.reload();
		}

		/**
		* clean_inputs
		*
		* Función para limpiar e inicializar los campos
		*/
		function clean_inputs(){
			$("#sltConceptoRBCTA").prop('selectedIndex',0);
			$("#sltConceptoRBCTA").select2("val", "");
			$("#txtColumna").val("");
		}
	</script>
	<?php require ('modalUpdateGSDetalleArchivo.php'); ?>
</body>
</html>