<div class="modal fade Xmod" id="modalMod" role="dialog" align="center" >
    <div class="modal-dialog">
      	<div class="modal-content">
	        <div id="forma-modal" class="modal-header">
	          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
    	    </div>
    	    <form name="form" method="POST" action="controller/controllerGSDetalleArchivo.php?action=modify">
	        	<div class="modal-body" style="margin-top: 8px">
					<?php
					@require ('Conexion/conexion.php');
					@session_start();
					$param     = $_SESSION['anno'];
					$id_detalle = "";
					$id_concepto_rubro_cuenta = "";
					$nom_concepto_rubro_cuenta = "";
					$n_columna = "";
					if(!empty($_POST['id_unico'])){
						$id_detalle = $_POST['id_unico'];
						$sqlD = "SELECT	dta.concepto_rubro_cuenta,CONCAT('Concepto : ',ct.nombre,' - Rubro : ',rb.codi_presupuesto,' - Cuenta Débito : ',ctd.codi_cuenta,' - Cuenta Crédito : ',ctc.codi_cuenta) as ConceptoRCT,dta.columna
									FROM	gs_detalle_archivo dta
									LEFT JOIN gf_concepto_rubro_cuenta crc ON crc.id_unico = dta.concepto_rubro_cuenta
									LEFT JOIN gf_concepto_rubro cbr ON cbr.id_unico = crc.concepto_rubro
									LEFT JOIN gf_concepto ct ON ct.id_unico = cbr.concepto
									LEFT JOIN gf_rubro_pptal rb ON rb.id_unico = cbr.rubro
									LEFT JOIN gf_cuenta ctd ON ctd.id_unico = crc.cuenta_debito
									LEFT JOIN gf_cuenta ctc ON ctc.id_unico = crc.cuenta_credito
									WHERE	dta.id_unico = $id_detalle
									AND     ct.parametrizacionanno = $param
									AND     rb.parametrizacionanno = $param";
						$resultD = $mysqli->query($sqlD);
						$rowD = mysqli_fetch_row($resultD);
						$id_concepto_rubro_cuenta = $rowD[0];
						$nom_concepto_rubro_cuenta = $rowD[1];
						$n_columna = $rowD[2];
					}
					 ?>
					<div class="form-horizontal">
						<div class="form-group">
							<input type="hidden" name="id" value="<?php echo $id_detalle ?>">
							<label for="sltConceptoRubro" class="col-sm-5 control-label"><strong class="obligado">*</strong>Concepto Rubro Cuenta:</label>
							<select name="sltConceptoRBCTA" id="sltConceptoRBCTA" title="Seleccione concepto rubro cuenta" class="form-control col-sm-1 selector" required style="width: 50%;font-size: 11px">
							<?php
							echo "<option value=\"".$id_concepto_rubro_cuenta."\">".ucwords(mb_strtolower($nom_concepto_rubro_cuenta))."</option>";
							$sql_CR1 = "SELECT DISTINCT	crc.id_unico as id, CONCAT('Concepto : ',ct.nombre,' - Rubro : ',rb.codi_presupuesto) as concepto_rubro,
														CONCAT('Cuenta Débito : ',ctd.codi_cuenta,' - Cuenta Crédito : ',ctc.codi_cuenta) as Cuentas
											FROM		gf_concepto_rubro_cuenta crc
											LEFT JOIN 	gf_concepto_rubro cbr ON cbr.id_unico = crc.concepto_rubro
											LEFT JOIN 	gf_concepto ct ON ct.id_unico = cbr.concepto
											LEFT JOIN 	gf_rubro_pptal rb ON rb.id_unico = cbr.rubro
											LEFT JOIN 	gf_cuenta ctd ON ctd.id_unico = crc.cuenta_debito
											LEFT JOIN 	gf_cuenta ctc ON ctc.id_unico = crc.cuenta_credito
											WHERE 		crc.id_unico != $id_concepto_rubro_cuenta
											AND         ct.parametrizacionanno = $param
											AND         rb.parametrizacionanno = $param";
							$result_CR1 = $mysqli->query($sql_CR1);
							while ($row_CR1 = mysqli_fetch_row($result_CR1)) {
								$concepto1 = ucwords(mb_strtolower($row_CR1[1]));
								echo "<option value=\"".$row_CR1[0]."\">".$concepto1." - ".ucwords(mb_strtolower($row_CR1[2]))."</option>";
							}
							?>
							</select>
						</div>
						<div class="form-group">
							<label for="txtCol" class="col-sm-5 control-label"><strong class="obligado">*</strong>Nª Columna:</label>
							<input type="text" name="txtColumna" id="txtColumna" placeholder="Nº Columna" title="Ingrese columna contando desde A como 0" class="form-control col-sm-1" required value="<?php echo $n_columna ?>" style="width: 50%">
						</div>
					</div>
	        	</div>
	        	<div id="forma-modal" class="modal-footer">
	          		<button type="submit" id="btnUpdate" class="btn" style="color: #000; margin-top: 2px" >Aceptar</button>
	        	</div>
        	</form>
      </div>
    </div>
</div>
<link rel="stylesheet" href="css/select2.css">
	<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script type="text/javascript" src="js/select2.js"></script>
<script>
	$(".selector").select2({allowClear:true});
</script>