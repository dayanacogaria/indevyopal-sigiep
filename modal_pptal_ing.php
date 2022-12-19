<div class="modal fade pptal_ing" id="modal_pptal_ing" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog" style="width: 900px">
	    <div class="modal-content">
	    	<div id="forma-modal" class="modal-header">
	    		<button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
	    		<h4 class="modal-title" style="font-size: 24px; padding: 3px;">Comprobante de Ingreso Presupuestal</h4>
	    	</div>
	    	<div class="modal-body">
	    	    <?php
	    	    require_once './Conexion/conexion.php';
	    	    $fecha_p = ""; $tipo_p = ""; $nom_tipo_p = ""; $numero_p = ""; $fecha_vp = ""; $tercero_p = ""; $nombreE_P = ""; $descripcion_pp = ""; $nombre_EP = "";
	    	    if(!empty($_POST['id_pptal'])) {
	    	    	$id_pptal = $_POST['id_pptal'];
	    	    	$sqlComprobante_P="SELECT pptal.numero, tpp.nombre, tpp.codigo, date_format(pptal.fecha,'%d/%m/%Y'), date_format(pptal.fechavencimiento,'%d/%m/%Y'), pptal.descripcion, pptal.tercero, pptal.estado FROM gf_comprobante_pptal pptal LEFT JOIN gf_tipo_comprobante_pptal tpp ON pptal.tipocomprobante = tpp.id_unico WHERE pptal.id_unico=$id_pptal";
					$rs_p = $mysqli->query($sqlComprobante_P);
					$rw_p = mysqli_fetch_row($rs_p);
					$numero_p = $rw_p[0]; $tipo_p = $rw_p[1]; $nom_tipo_p = $rw_p[2]; $fecha_p = $rw_p[3]; $fecha_vp = $rw_p[4]; $descripcion_pp = $rw_p[5];
					$tercero_p = $rw_p[6];           
                    if(!empty($rw_p[7])){
                    $sql__E="select nombre from gf_estado_comprobante_pptal where id_unico=$rw_p[7]";
                    $rs_e = $mysqli->query($sql__E); 
                    $rw_E = mysqli_fetch_row($rs_e); 
                    $nombre_EP=$rw_E[0];
                    } else {
                        $nombre_EP = "";
                    }
	    	    }
	    	    ?>	    
	    	    <div class="row">
	    	    	<div class="client-form col-sm-12 col-md-12 col-lg-12">
		    	    	<div class="form-group">
			    	    	<label class="control-label col-sm-2"><strong class="obligado">*</strong>Fecha:</label>
			    	    	<input type="text" name="txtFechaC" id="txtFechaC" style="width:150px;padding:-2px;height:30px;font-size: 10px" class="col-sm-1 form-control input-sm" value="<?php echo $fecha_p; ?>" title="Fecha" placeholder="Fecha" readonly/>
			    	    	<label class="control-label col-sm-1"><strong class="obligado">*</strong>Tipo:</label>
			    	    	<select name="sltTipoComprobante" id="slTipoComprobante" style="width:200px;padding:-2px;font-size:10px;height:30px" class="col-sm-1 form-control input-sm" title="Seleccione el tipo de comprobante" readonly>                                
		                        <?php if(!empty($tipo_p)){echo "<option value=\"\">".ucwords(mb_strtolower($tipo_p)).PHP_EOL.$nom_tipo_p."</option>";}?>
		                    </select>
		                    <label class="control-label col-sm-1"><strong class="obligado">*</strong>Número:</label>
		                    <input type="text" name="txtNumeroC" id="txtNumeroC" style="width:150px;padding:-2px;height:30px;font-size: 10px" class="col-sm-1 form-control input-sm" value="<?php echo $numero_p; ?>" title="Número del comprobante" placeholder="Número" readonly/>
			    	    </div>
			    	    <div class="form-group">
			    	    	<label class="control-label col-sm-2"><strong class="obligado">*</strong>Fecha V:</label>
			    	    	<input type="text" name="txtFechaVP" id="txtFechaVP" style="width:150px;padding:-2px;height:30px;font-size: 10px" class="col-sm-1 form-control input-sm" value="<?php echo $fecha_vp; ?>" title="Fecha vencimiento" placeholder="Fecha" required readonly/>
			    	    	<label class="control-label col-sm-1"><strong class="obligado">*</strong>Tercero:</label>
			    	    	<select name="sltTerceroP" id="sltTerceroP" style="width: 425px; padding: -2px; font-size: 10px; height: 30px" class="col-sm-1 form-control input-sm" title="Seleccione tercero" required readonly>
		                        <?php 
		                        if(!empty($tercero_p)) {
		                        	#Consulta para traer el tercero dependiendo del id de tercero que tiene el comprobante
			                        $sqltercero="SELECT  IF(CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
			                                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
			                                                IF(ter.apellidouno IS NULL,'',
			                                                IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
			                                                IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' 
			                                    OR  CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
			                                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
			                                                IF(ter.apellidouno IS NULL,'',
			                                                IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
			                                                IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL 
			                                    ,(ter.razonsocial),                                            
			                                        CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
			                                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
			                                                IF(ter.apellidouno IS NULL,'',
			                                                IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
			                                                IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE', 
			                                    ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
			                                    LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
			                                    WHERE ter.id_unico = $tercero_p
			                                    ORDER BY NOMBRE ASC";
			                        $ter = $mysqli->query($sqltercero);                                    
			                        #Impresión de los valores consultados
			                        $per = mysqli_fetch_row($ter);
			                        echo '<option value="'.$per[1].'">'.ucwords(mb_strtolower($per[0].'    '.$per[2])).'</option>';
		                        }                        
		                        ?>
		                    </select>
			    	    </div>
			    	    <div class="form-group">
			    	    	<label class="control-label col-sm-2"><strong class="obligado">*</strong>Estado:</label>
			    	    	<input type="text" name="txtEstadoP" id="txtEstadoP" style="width:150px;padding:-2px;height:30px;font-size: 10px" class="col-sm-1 form-control input-sm" value="<?php echo $nombre_EP; ?>" title="Estado del comprobante presupuestal" placeholder="Estado" required readonly/>
			    	    	<label class="control-label col-sm-1">Descrip<br/>ción:</label>
			    	    	<textarea name="txtDescriptor" id="txtDescriptor" class="form-control col-sm-1 area" maxlength="500" rows="4" cols="30" title="Descripción del comprobante presupuestal" style="width: 425px; margin-top: -1px; max-height: 40px; font-size: 10px" placeholder="Descripción" readonly><?php echo $descripcion_pp; ?></textarea>
			    	    </div>
		    	    </div>	  
		    	    <div class="col-sm-12 col-md-12 col-lg-12">
		    	    	<div class="form-group table-responsive">
		                    <table id="tablaDetalle_P" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">
		                        <thead>
		                            <tr>                                      
		                              <td width="7%"></td>
		                              <td class="cabeza"><strong>Concepto</strong></td>
		                              <td class="cabeza"><strong>Rubro</strong></td>
		                              <td class="cabeza"><strong>Valor</strong></td>                                      
		                            </tr>
		                            <tr>                                      
		                              <th width="7%"></th>
		                              <th>Nombre</th>
		                              <th>Rubro</th>
		                              <th>Valor</th>                                      
		                            </tr>
		                        </thead>
		                        <tbody>
		                            <?php
		                            if(!empty($id_pptal)) {
		                            	$queryGen = "SELECT DISTINCT detComP.id_unico, detComP.conceptoRubro, detComP.rubroFuente, detComP.valor
		                            	FROM gf_detalle_comprobante_pptal detComP                                         
		                            	WHERE detComP.comprobantepptal = $id_pptal";
		                            	$resultP=$mysqli->query($queryGen);
		                            	while ($row = mysqli_fetch_row($resultP)) { ?>
			                                <tr>
			                                    <td></td>
			                                    <td class="campos" align="left">
			                                        <?php 
			                                        $sqlC = "SELECT     cn.nombre
			                                                FROM        gf_concepto_rubro cntr
			                                                LEFT JOIN   gf_concepto cn ON cntr.concepto = cn.id_unico
			                                                WHERE       cntr.id_unico = $row[1]";
			                                        $resultC = $mysqli->query($sqlC);
			                                        $rC = mysqli_fetch_row($resultC);
			                                        echo ucwords(mb_strtolower($rC[0]));
			                                        ?>
			                                    </td>
			                                    <td class="campos" align="left">
			                                        <?php 
			                                        $sqlR = "SELECT     rb.nombre
			                                                FROM        gf_rubro_fuente rbf 
			                                                LEFT JOIN   gf_rubro_pptal rb ON rbf.rubro = rb.id_unico
			                                                WHERE       rbf.id_unico = $row[2]";
			                                        $resultR = $mysqli->query($sqlR);
			                                        $rR = mysqli_fetch_row($resultR);
			                                        echo ucwords(mb_strtolower($rR[0]));
			                                        ?>
			                                    </td>
			                                    <td class="campos text-right"><?php echo number_format($row[3], 2, '.', ','); ?></td>                                            
			                                </tr>
		                            	<?php } 
		                            } ?>
		                        </tbody>
		                    </table>                    
		                </div>
		    	    </div>	 
	    	    </div>
	    	</div>
	    	<div id="forma-modal" class="modal-footer">
	    	</div>
	    </div>
	</div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var i= 1;
        $('#tablaDetalle_P thead th').each( function () {
            if(i != 1){ 
                var title = $(this).text();
                switch (i){
                case 2:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 3:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 4:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 5:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 6:
                  $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 7:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;            
                case 8:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 9:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
            }
            i = i+1;
        }else{
            i = i+1;
        }        
    });
    // DataTable
    var table = $('#tablaDetalle_P').DataTable({
        "autoFill": true,
        "scrollX": true,
        "pageLength": 5,
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No Existen Registros...",
            "info": "Página _PAGE_ de _PAGES_ ",
            "infoEmpty": "No existen datos",
            "infoFiltered": "(Filtrado de _MAX_ registros)",
            "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
        },
        'columnDefs': [{
            'targets': 0,
            'searchable':false,
            'orderable':false,
            'className': 'dt-body-center'         
        }]
    });

    var i = 0;
    table.columns().every( function () {
        var that = this;
        if(i!=0){
        $( 'input', this.header() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            	}
        	} );
            	i = i+1;
        	}else{
            	i = i+1;
        	}
    	} );
	});	
</script>