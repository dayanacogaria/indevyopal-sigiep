<?php  
###################################################################################################################################
#                                  MODIFICACIONES
#                                  NORMALES TUNJA
###################################################################################################################################
# 06/10/2017 | Erica González | Saldo débito y ccrédito
# 26/07/2017 | Erica González | Cierre
# 14/02/2017 | Jhon Numpaque # Descripción: Se incluyo campo valor y descripción, y la consulta de clase cuenta se grago clase 11 las cuales son cuentas  de clase cuentas bancos
# # Fecha de Creación 	10|02|2017 Jhon Numpaque
#################################################################################################################################
require_once ('head_listar.php');
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
require_once('./jsonSistema/funcionCierre.php');
require_once('./jsonPptal/funcionesPptal.php');
$con = new ConexionPDO();
 ?> 
<title>Registrar Conciliación Bancaria</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<style>
	.detalle{
		font-size: 11px
	}
	.select2-container .select2-choice{
		height: 30px
	}
    /*Estilos tabla*/
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
        font-family: Arial;}
    /*Campos dinamicos*/
    .campoD:focus {
        border-color: #66afe9;
        outline: 0;            
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);            
    }
    .campoD:hover{
        cursor: pointer;
    }
    /*Campos dinamicos label*/
    .valorLabel{
        font-size: 10px;
    }
    .valorLabel:hover{
        cursor: pointer;
        color:#1155CC;
    }
    /*td de la tabla*/
    .campos{
        padding: 0px;
        font-size: 10px
    }
	/*Select2*/
    .select2-container .select2-choice{
      height: 30px;
      padding: 0px;
    }
    /*Estilos de campos de erros*/
	label #sltTipoComprobanteInicial-error, #sltTipoComprobanteFinal-error, #txtFechaInicial-error, #txtFechaFinal-error  {
	    display: block;
	    color: #155180;
	    font-weight: normal;
	    font-style: italic;

	}
</style>
<script src="dist/jquery.validate.js"></script>		
</head>
    <body>
	<div class="container-fluid">
		<?php 
		##########################################################################################################
		############ Variables de carga ##########################################################################
		$idPartida = "";			#idPArtida
		$partida = "";				#variante de partida
		$cuenta = "";				#IdCuenta
		$nomcta = "";				#nombre de cuenta
		$saldoE = "";				#Valor Saldo Extracto
		$mes = "";					#IdMes
		$nomMes = "";				#nombreMEs
		$ArchivoC = "";				#Archvio Ruta
		$descripcion = "";			#Descripción
		$mess = "0";
		##########################################################################################################
		#Captura de variable enviada por get
		if(!empty($_GET['idPartida'])){
			$partida = $_GET['idPartida'];
			#Consulta de datos
			$sqlPA = "SELECT partC.id_unico,
							 partC.id_cuenta,							 
							 partC.saldo_extracto,
							 partC.mes,
							 partC.archivo_extracto,
							 partC.descripcion,
							 ms.mes,
							 CONCAT(cta.codi_cuenta,' ',cta.nombre)
			FROM gf_partida_conciliatoria partC
			LEFT JOIN gf_cuenta cta ON partC.id_cuenta = cta.id_unico
			LEFT JOIN gf_mes ms ON ms.id_unico = partC.mes			
			WHERE md5(partC.id_unico) = '$partida'";
			$resultPTD = $mysqli->query($sqlPA);
			$rowPTD = mysqli_fetch_row($resultPTD);
			############################################# Cargue de datos ##########################################
			$idPartida = $rowPTD[0];	#id
			$cuenta = $rowPTD[1];		#id_cuenta
			$saldoE = $rowPTD[2];		#saldo
			$mes = $rowPTD[3];			#mes
			$descripcion = $rowPTD[5];	#descripción
			$ArchivoC = $rowPTD[4]; 	#Archivo
			$nomMes = $rowPTD[6]; 		#Nombre de mes
			$nomcta = $rowPTD[7];		#nombre de la cuenta
			##########################################################################################################################################
			# Array con los numeros de los meses
			#
			##########################################################################################################################################
			#
			$meses = array( "Enero" => '01', "Febrero" => '02', "Marzo" => '03',"Abril" => '04', "Mayo" => '05', "Junio" => '06', "Julio" => '07', "Agosto" => '08', "Septiembre" => '09', "Octubre" => '10', "Noviembre" => '11', "Diciembre" => '12'); 
			$mess = $meses[$nomMes];
		}		
		#Variable parametrización año
		$param = $_SESSION['anno'];
		 ?>
		<div class="row content">
			<?php 
			require_once('menu.php');
			 ?>
			<div class="col-sm-10 text-left" style="margin-top: -22px">
				<h2 class="tituloform" align="center">Registrar Conciliación Bancaria</h2>
				<div class="client-form contenedorForma" style="margin-top:-7px">
					<form name="form" id="form" method="POST" action="json/registrarGFPartidaConciliatoria.php" class="form-horizontal" enctype="multipart/form-data">
						<p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>
                        <!-- Campo oculto con la id de la partida -->
                        <input type="hidden" class="hidden" id="txtIdPartida" name="txtIdPartida" value="<?php echo $idPartida; ?>">
                        <div class="form-group form-inline">
                        	<label for="sltCuenta" class="control-label col-sm-2">
                        		<strong class="obligado">*</strong>Cuenta:
                        	</label>
							<select name="sltCuenta" id="sltCuenta" title="Seleccione cuenta" class="form-control col-sm-1 input-sm" style="width: 200px;height: 30px" required>
								<?php
								if(empty($cuenta)){
									echo "<option value=''>Cuenta</option>";
									#####################Consulta de cuenta ####################################
									$sqlC = "SELECT id_unico,CONCAT(codi_cuenta,' ',nombre) FROM gf_cuenta WHERE parametrizacionanno = $param AND clasecuenta=11 ORDER BY id_unico ASC";
									$resultC = $mysqli->query($sqlC);
									while ($rowC=mysqli_fetch_row($resultC)) {
										echo "<option value=".$rowC[0].">".ucwords(mb_strtolower(($rowC[1])))."</option>";
									}
								}else{
									echo "<option value=".$cuenta.">".ucwords(mb_strtolower($nomcta))."</option>";
									#####################Consulta de cuenta ####################################
									$sqlC = "SELECT id_unico,CONCAT(codi_cuenta,' ',nombre) FROM gf_cuenta WHERE parametrizacionanno = $param and id_unico != $cuenta AND clasecuenta=11 ORDER BY id_unico ASC";
									$resultC = $mysqli->query($sqlC);
									while ($rowC=mysqli_fetch_row($resultC)) {
										echo "<option value=".$rowC[0].">".ucwords(mb_strtolower(($rowC[1])))."</option>";
									}
								}
								 ?>
								}
							</select>
							<label for="sltSaldoE" class="control-label col-sm-2">
								<strong class="obligado">*</strong>Saldo Extracto:
							</label>
							<input type="text" id="txtSaldoE" name="txtSaldoE" value="<?php echo $saldoE; ?>" onkeypress="return txtValida(event,'decimales')" title="Ingrese saldo" placeholder="Saldo Extracto" class="form-control col-sm-1" style="width: 200px;height: 30px" required>
							<label for="sltMes" class="control-label col-sm-2">
								<strong class="obligado">*</strong>Mes:
							</label>
							<select name="sltMes" id="sltMes" title="Seleccione mes" class="form-control input-sm col-sm-1" required style="width: 100px;height: 30px">
								<?php 
								if(empty($mes)){
									echo "<option value=''>Mes</option>";									
									##############Consulta Mes #################################################
									$sqlM = "SELECT id_unico,Mes FROM gf_mes WHERE parametrizacionanno=$param  ORDER BY id_unico ASC";
									$resultM = $mysqli->query($sqlM);
									while ($rowM=mysqli_fetch_row($resultM)) {
										echo "<option value=".$rowM[0].">".ucwords(mb_strtolower($rowM[1]))."</option>";
									}
								}else{
									echo "<option value=".$mes.">".ucwords(mb_strtolower($nomMes))."</option>";
									##############Consulta Mes #################################################
									$sqlM = "SELECT id_unico,Mes FROM gf_mes WHERE parametrizacionanno=$param AND id_unico != $mes ORDER BY id_unico ASC";
									$resultM = $mysqli->query($sqlM);
									while ($rowM=mysqli_fetch_row($resultM)) {
										echo "<option value=".$rowM[0].">".ucwords(mb_strtolower($rowM[1]))."</option>";
									}
								}								
								 ?>
							</select>
							<a id="btnBuscar" class="btn " title="Buscar partida por cuenta y mes" onclick="return consultarPartida()" style="margin-top:-2px;padding:3px 3px 3px 3px"><li class="glyphicon glyphicon-search"></li></a>
                        </div>
                        <div class="form-group form-inline" style="margin-top: -10px">
                        	<label for="flArchivoC" class="control-label col-sm-2">
                        		Archivo<br/>Conciliación:
                        	</label>
                        	<input type="hidden" name="txtArchivoC" id="txtArchivoC" value="<?php echo $ArchivoC; ?>">
							<input type="file" class="form-control col-sm-1" name="flArchivoC" name="flArchivoC" title="Archivo de Conciliación" style="width: 200px;height: 30px">							
                        	<!--<div class="col-sm-1" style="width: 45px">
	                            glyphicon glyphicon-download-alt
	                            	<a class="btn btn-primary" id="btnMarcar" title="Marcar/Desmarcar todas las conciliaciones" name="btn"><span class="glyphicon glyphicon-ok"></span></a>
	                            </div>-->
							<label for="txtDescripcion" class="control-label col-sm-2">
								Descripción:
							</label>
							<textarea name="txtDescripcion" id="txtDescripcion" placeholder="Descripción" title="Descripción" rows="5" class="form-control col-sm-2 area" style="height: 40px;margin-top:-1px;width: 485px"><?php echo $descripcion; ?></textarea>
                        </div>
                        <div class="form-group form-inline" style="margin-top: -15px;margin-bottom: 5px">
                        	<div class="col-sm-6 col-sm-offset-6"> 
                                        <!---****Tipo Formato******--->
                                        <div class="col-sm-4"  >
                                            <div  style="margin-top:-7px;  display: none" id="divtipoformato" >
                                            <label >Tipo Formato:</label><br/>
                                            <input type="radio" id="tipoformato1"  name="tipoformato" value="1" > Formato 1<br/>
                                            <input type="radio" id="tipoformato2"  name="tipoformato" value="2" checked="checked"> Formato 2
                                            </div>
	                        	</div>
                                        <!---****Fin Formato******--->
                        		<!-- Botones -->
                        		<div class="col-sm-1">
	                        		<a class="btn btn-primary" id="btnNuevo" name="btnNuevo" onclick="return urlLimpia()" title="Nueva Conciliación" style="width: 40px;height: 34px;cursor: pointer;"><span class="glyphicon glyphicon-plus"></span></a>
	                        	</div>	                        	
	                        	<div class="col-sm-1">
	                        		<button type="submit" class="btn btn-primary" id="btnGuardar" name="btnGuardar" title="Guardar Conciliación" style="width: 40px;height:34px;cursor: pointer;"><span class="glyphicon glyphicon-floppy-disk"></span></button>                        	
	                        	</div>
	                        	<div class="col-sm-1">
                                            <button class="btn btn-primary" id="btnModificar" title="Modificar Conciliación" onclick="return actualizarPtd()" name="btnModificar" style="width: 40px;height:34px;"><span class="glyphicon glyphicon-pencil"></span></button>
	                        	</div>	                            
	                        	<div class="col-sm-1">
	                                <a class="btn btn-primary" id="btnConciliar" title="Conciliar" name="btnConciliar" onclick="return abrirmodalConciliado()" style="width: 40px;height: 34px;cursor: pointer;"><span class="glyphicon glyphicon-education"></span></a>
	                            </div>
	                        	<div class="col-sm-1">
	                        		<a class="btn btn-primary" id="btnDoc" name="btnDoc" onclick="abrirDoc('<?php echo substr($ArchivoC,3) ?>')" style="width: 40px;height: 34px" title="Imprimir documento"><span class="glyphicon glyphicon-download-alt"></span></a>
	                        	</div>
	                            <div class="col-sm-1">
	                            	<a class="btn btn-primary" id="btnImprimir" onclick="return imprimirCon()" title="Imprimir Conciliaciones" name="btnImprimir" style="height: 34px;width: 40px"><!--Imprimir Conciliaciones--><span class="glyphicon glyphicon-print"></span></a>
	                            </div>
	                            <div class="col-sm-1">
	                            	<a class="btn btn-primary" id="btnImprimirE" onclick="return imprimirConExcel()" title="Imprimir Conciliaciones" name="btnImprimir" style="height: 34px;width: 40px"><span class="fa fa-file-excel-o"></span></a>
	                            </div>	                           	                            
	                            <script type="text/javascript">
	                            //////////////////////////////////////////////////////////////////////////////////////////////////////////
	                            // Validación de botón de impresión de documento
	                            //
	                            //////////////////////////////////////////////////////////////////////////////////////////////////////////
	                            	if($("#txtArchivoC").val() !== ''){
	                            		$("#btnDoc").attr('disabled',false);
	                            	}else{
	                            		$("#btnDoc").attr('disabled',true);
	                            		$("#btnDoc").removeAttr('onclick');
	                            	}
	                            //////////////////////////////////////////////////////////////////////////////////////////////////////////
	                            // Función para abrir documento de forma externa
	                            //
	                            //////////////////////////////////////////////////////////////////////////////////////////////////////////
	                            	function abrirDoc(archivo){
	                            		window.open(archivo);
	                            	}
	                            </script>
		                        <?php		                        
		                        ##########################################################################################################
		                        # Validación de botón de guardado
		                        #
		                        ##########################################################################################################
	                        	if(!empty($partida)){ ?>
									<script>
										$("#btnGuardar").attr('disabled',true);
										$("#btnModificar,#btnMarcar,#btnImprimir,#btnImprimirE").attr('disabled',false);										
                                                                                $("#divtipoformato").css('display', 'block');
									</script>
	                        	<?php }else{ ?>
									<script>
										$("#btnGuardar").attr('disabled',false);
										$("#btnModificar,#btnMarcar,#btnImprimir,#btnImprimirE").attr('disabled',true);										
										$("#btnModificar,#btnMarcar,#btnImprimir,#btnImprimirE,#btnDoc").removeAttr('onclick');										
									</script>
	                        	<?php } 
	                        	##########################################################################################################
	                        	?>
                        	</div>
                        </div>                        
                        <!--<iframe src="<?php echo substr($ArchivoC,3) ?>" embedded="true" style="width:600px; height:500px;" frameborder="0"></iframe>-->
					</form>
				</div>
				<div class="col-sm-12 text-left">
					<!-- Detalle -->
					<div class="client-form col-sm-12" style="margin-top:5px;border-radius: 5px;box-shadow: inset 1px 1px 1px 1px gray;">
						<form id="formDetalle" method="POST" enctype="multipart/form-data" class="form-horizontal" style="margin-top: 10px;margin-left: -5px">
							<div class="form-group form-inline" style="margin-bottom:0px">
								<label for="sltTipoPartida" class="col-sm-1 control-label detalle">
									<strong class="obligado">*</strong>Tipo<br/>Partida:
								</label>
								<select name="sltTipoPartida" id="sltTipoPartida" class="form-control col-sm-1 detalle" style="width: 150px;height: 30px" title="Tipo Partida" required>
									<?php 
									echo "<option value=' '>Tipo Partida</option>";
									$sqlTipoP = "SELECT id_unico,nombre FROM gf_tipo_partida ORDER BY id_unico ASC";
									$resultTP = $mysqli->query($sqlTipoP);
									while ($rowTPR = mysqli_fetch_row($resultTP)) {
										echo "<option value=".$rowTPR[0].">".ucwords(mb_strtolower($rowTPR[1]))."</option>";
									}
									 ?>
								</select>
								<label for="txtFecha" class="control-label col-sm-1 detalle">
									<strong class="obligado">*</strong>Fecha<br/>Partida:
								</label>
								<input type="text" class="form-control col-sm-1 detalle" id="txtFechaP" name="txtFechaP" title="Fecha Partida" style="width: 80px;height: 30px" required>																
                                                                
                                                                
                                                                <script type="text/javascript">
                                                                        $("#txtFechaP").change(function()
                                                                        {
                                                                          var fecha = $("#txtFechaP").val();
                                                                            var form_data = { case: 4, fecha:fecha};
                                                                            $.ajax({
                                                                              type: "POST",
                                                                              url: "jsonSistema/consultas.php",
                                                                              data: form_data,
                                                                              success: function(response)
                                                                              { 
                                                                                  if(response ==1){
                                                                                      $("#periodoC").modal('show');
                                                                                      $("#txtFechaP").val("").focus();

                                                                                  } else {
                                                                                  }
                                                                              }
                                                                            });   


                                                                        });
                                                                    </script>
                                                                    
								<label for="sltTipoDoc" class="col-sm-1 detalle control-label">
									<strong class="obligado">*</strong>Tipo<br/>Documento:
								</label>
								<select name="sltTipoDoc" id="sltTipoDoc" class="form-control col-sm-1 detalle" title="Seleccione tipo documento" style="width: 200px;height: 30px" required>
									<?php 
										echo "<option value=' '>Tipo Documento</option>"; 
										$sqlTD = "SELECT id_unico,nombre FROM gf_tipo_documento ORDER BY id_unico ASC";
										$resultTD = $mysqli->query($sqlTD);
										while($rowTD = mysqli_fetch_row($resultTD)){
											echo "<option value=".$rowTD[0].">".ucwords(mb_strtolower($rowTD[1]))."</option>";
										}
									?>
								</select>
								<label for="txtDoc" class="control-label col-sm-1 detalle">
									<strong class="obligado">*</strong>No<br/>Documento:
								</label>
								<input type="text" id="txtNumDoc" name="txtNumDoc" class="form-control col-sm-1 detalle" title="Número de documento" style="width: 100px;height: 30px" placeholder="No documento" required>
							</div>
							<div class="form-group form-inline" style="margin-bottom:0px">							
								<label for="txtValor" class="control-label col-sm-1 detalle">
									<strong class="obligado">*</strong>Valor:
								</label>
								<input type="text" id="txtValor" name="txtValor" class="form-control col-sm-1 detalle" title="Valor" style="width: 150px;height: 30px" placeholder="Valor" required onkeypress="return txtValida(event,'decimales')">
								<label for="txtFechaConC" class="col-sm-1 detalle control-label">
									Fecha<br/>Conciliación:
								</label>
								<input type="text" class="form-control col-sm-1 detalle" id="txtFechaPCon" name="txtFechaPCon" title="Fecha conciliación" style="width: 80px;height: 30px" value="<?php if(empty($mess)){$month = date('m');$year = date('Y');$day = date("d", mktime(0,0,0, $month+1, 0, $year)); echo $day.date('/m/Y');}else{$month = $mess;$year = date('Y');$day = date("d", mktime(0,0,0, $month+1, 0, $year)); echo $day.'/'.$mess.date('/Y');} ?>">
                                                                <script type="text/javascript">
                                                                    $("#txtFechaPCon").change(function()
                                                                    {
                                                                      var fecha = $("#txtFechaPCon").val();
                                                                        var form_data = { case: 4, fecha:fecha};
                                                                        $.ajax({
                                                                          type: "POST",
                                                                          url: "jsonSistema/consultas.php",
                                                                          data: form_data,
                                                                          success: function(response)
                                                                          { 
                                                                              if(response ==1){
                                                                                  $("#periodoC").modal('show');
                                                                                  $("#txtFechaPCon").val("").focus();

                                                                              } else {
                                                                              }
                                                                          }
                                                                        });   


                                                                    });
                                                                </script>
                                                                
                                                                
                                                                
								<label for="txtDescripcion" class="control-label col-sm-1 detalle">
									<strong class="obligado">*</strong>Descripción:
								</label>
								<textarea name="txtDescripcionDetalle" id="txtDescripcionDetalle" title="Descripción" placeholder="Descripción" class="col-sm-1 form-control detalle" style="width: 200px;height: 30px;margin-top: 0px" rows="2"></textarea>
								<label for="optConciliado" class="control-label col-sm-1 detalle">
									<strong class="obligado">*</strong>Conciliado:
								</label>
								<div class="col-sm-2 detalle radio" >
									SI<input type="radio" class="detalle radio-inline" id="optCon1" name="optConciliado" title="Es conciliado" value="1">
									NO<input type="radio" class="detalle radio-inline" id="optCon2" name="optConciliado" title="No se concilió" value="NULL" checked>
								</div>
								<div class="col-sm-1" style="margin-top: -20px">
									<div class="col-sm-1" style="width: 40px">
                                                                            <button type="button" class="btn btn-primary" onclick="return registrarDetalle()" id="btnGuardarDP" name="btnGuardarDP" title="Guardar detalle conciliación" style="width: 40px;height: 34px;cursor: pointer;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                                                                    </div>
		                        	<?php
			                        ######################################################################################################
			                        # Validación de botón de guardado
		                        	if(empty($partida)){ ?>
										<script>
											$("#btnGuardarDP").attr('disabled',true);
										</script>
		                        	<?php }else{ ?>
										<script>
											$("#btnGuardarDP").attr('disabled',false);
										</script>
		                        	<?php } 
		                        	######################################################################################################
		                        	?> 
								</div>
							</div>							
						</form>
					</div>
				</div>
				<div class="col-sm-12" style="margin-left: 0px">
					<div class="table-responsive contTabla">
						<div class="table-responsive contTabla">
							<table id="tabla" class="table table-striped table-condensed display detalle" cellpadding="0" width="100%">
								<thead>
									<tr>
										<td class="oculto">Id</td>
										<td class="cabeza" width="7%"></td>
										<td class="cabeza"><strong>Tipo Partida</strong></td>
										<td class="cabeza"><strong>Fecha Partida</strong></td>
										<td class="cabeza"><strong>Conciliado</strong></td>
										<td class="cabeza"><strong>Fecha Conciliación</strong></td>
										<td class="cabeza"><strong>Tipo Documento</strong></td>
										<td class="cabeza"><strong>No Documento</strong></td>
										<!--<td class="cabeza"><strong>Estado</strong></td>-->
										<td class="cabeza"><strong>Descripción</strong></td>
										<td class="cabeza"><strong>Valor</strong></td>
									</tr>
									<tr>
										<th class="oculto">Id</th>
										<th class="cabeza" width="7%"></th>
										<th class="cabeza">Tipo Partida</th>
										<th class="cabeza">Fecha Partida</th>
										<th class="cabeza">Conciliado</th>
										<th class="cabeza">Fecha Conciliación</th>
										<th class="cabeza">Tipo Documento</th>
										<th class="cabeza">No Documento</th>
										<!--<th class="cabeza">Estado</th>-->
										<th class="cabeza">Descripción</th>
										<th class="cabeza">Valor</th>
									</tr>
								</thead>
								<tbody>
									<?php
                                                                        
									if(!empty($idPartida)){
									$calendario = CAL_GREGORIAN;
                                                                        $anno = $_SESSION['anno'];
                                                                        $sqlA = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $anno";
                                                                        $resultA = $mysqli->query($sqlA);
                                                                        $rowA = mysqli_fetch_row($resultA);
                                                                        $anno = $rowA[0];
                                                                        #Dia final del mes
                                                                        $diaF = cal_days_in_month($calendario, $mess , $anno); 
                                                                        $d = "'$anno-$mess-$diaF'";
                                                                        #Primer dia del mes
                                                                        $month = $mess;
                                                                        $year = $anno;
                                                                        $e = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
                                                                        $annov  = $_SESSION['anno'];
                                                                        $nannov = anno($annov);
                                                                        #Año Anterior
                                                                        $anno2 = $nannov-1;
                                                                        $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2'");
                                                                        if(count($an2)>0){
                                                                            $annova = $an2[0][0];
                                                                        } else {
                                                                            $annova = 0;
                                                                        }
                                                                        $cuentaA =0;
                                                                        $ca = $con->Listar("SELECT id_unico,codi_cuenta, equivalente_va FROM gf_cuenta WHERE id_unico = '$cuenta'");
                                                                        $id_cuenta = $ca[0][0];
                                                                        $codCuenta = $ca[0][1];
                                                                        $equivalente =$ca[0][2];
                                                                        if(!empty($equivalente)){
                                                                            #echo '1'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova";
                                                                            $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova");
                                                                            if(count($ctaa)>0){
                                                                                if(!empty($ctaa[0][0])){
                                                                                    $cuentaA = $ctaa[0][0];
                                                                                }
                                                                            } else {
                                                                                #echo '2'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova";
                                                                                $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova");
                                                                                if(!empty($ctaa[0][0])){
                                                                                    $cuentaA = $ctaa[0][0];
                                                                                }
                                                                            }
                                                                        } else {
                                                                            #echo '3'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova";
                                                                            $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova");
                                                                            if(!empty($ctaa[0][0])){
                                                                                    $cuentaA = $ctaa[0][0];
                                                                                }
                                                                        }
                                                                        $cuentas =($cuentaA.','.$id_cuenta);
                                                                        
										$sqlP = "SELECT DISTINCT dtpt.id_unico, 
                                                                                        dtpt.tipo_partida, 
                                                                                        tpp.nombre, 
                                                                                        date_format(dtpt.fecha_partida,'%d/%m/%Y'), 
                                                                                        dtpt.conciliado, 
                                                                                        dtpt.tipo_documento, 
                                                                                        tpd.nombre, dtpt.numero_documento, 
                                                                                        dtpt.descripcion_detalle_partida, 
                                                                                        dtpt.valor, 
                                                                                        date_format(dtpt.fecha_conciliacion,'%d/%m/%Y')
										FROM gf_detalle_partida dtpt 
										LEFT JOIN gf_tipo_partida tpp ON dtpt.tipo_partida = tpp.id_unico 
										LEFT JOIN gf_tipo_documento tpd ON tpd.id_unico = dtpt.tipo_documento 
										LEFT JOIN gf_partida_conciliatoria prt ON dtpt.id_partida = prt.id_unico
										WHERE	prt.id_cuenta IN ($cuentas) 
										AND (prt.mes = $mes)";
									$resultP = $mysqli->query($sqlP);
									while ($rowP = mysqli_fetch_row($resultP)) { ?>
									<tr>
										<td class="oculto"></td>
										<td class="campos" width="7%">
											<a href="#<?php echo $rowP[0] ?>"  class="eliminar" title="Eliminar" onclick="return eliminarDetalle(<?php echo $rowP[0] ?>)" class="campos"><li class="glyphicon glyphicon-trash"></li></a>
											<a href="#<?php echo $rowP[0] ?>" class="modificar" title="Modificar" onclick="return modificarDetalle(<?php echo $rowP[0]; ?>,<?php echo $rowP[1]; ?>,'<?php echo $rowP[3]; ?>',<?php echo $rowP[5]; ?>,'<?php echo $rowP[7]; ?>','<?php echo $rowP[8]; ?>',<?php echo $rowP[9] ?>,<?php echo $rowP[4] ?>,'<?php echo $rowP[10] ?>')" class="campos"><li class="glyphicon glyphicon-edit"></li></a>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[2])) ?>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[3])) ?>
										</td>
										<td class="campos text-center">
											<?php switch ($rowP[4]) {
												case 1:
													echo "SI";
													break;
												case 2:
													echo "NO";
													break;
												default :
													echo "NO";
													break;
											}
											 ?>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[10])) ?>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[6])) ?>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[7])); ?>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[8])) ?>
										</td>
										<td class="campos text-right">
											<?php echo number_format($rowP[9],2,',','.') ?>
										</td>
									</tr>
									<?php }
                                                                        $sqlP = "SELECT DISTINCT dtpt.id_unico, 
                                                                                        dtpt.tipo_partida, 
                                                                                        tpp.nombre, 
                                                                                        date_format(dtpt.fecha_partida,'%d/%m/%Y'), 
                                                                                        dtpt.conciliado, 
                                                                                        dtpt.tipo_documento, 
                                                                                        tpd.nombre, dtpt.numero_documento, 
                                                                                        dtpt.descripcion_detalle_partida, 
                                                                                        dtpt.valor, 
                                                                                        date_format(dtpt.fecha_conciliacion,'%d/%m/%Y')
										FROM gf_detalle_partida dtpt 
										LEFT JOIN gf_tipo_partida tpp ON dtpt.tipo_partida = tpp.id_unico 
										LEFT JOIN gf_tipo_documento tpd ON tpd.id_unico = dtpt.tipo_documento 
										LEFT JOIN gf_partida_conciliatoria prt ON dtpt.id_partida = prt.id_unico
										WHERE	prt.id_cuenta IN ($cuentas) AND prt.mes !=$mes 
                                                                                AND (dtpt.fecha_partida <'$e' AND (dtpt.conciliado IS NULL OR dtpt.conciliado !=1) )	
										";
									$resultP = $mysqli->query($sqlP);
									while ($rowP = mysqli_fetch_row($resultP)) { ?>
									<tr>
										<td class="oculto"></td>
										<td class="campos" width="7%">
											<a href="#<?php echo $rowP[0] ?>" class="eliminar" title="Eliminar" onclick="return eliminarDetalle(<?php echo $rowP[0] ?>)" class="campos"><li class="glyphicon glyphicon-trash"></li></a>
											<a href="#<?php echo $rowP[0] ?>" class="modificar" title="Modificar" onclick="return modificarDetalle(<?php echo $rowP[0]; ?>,<?php echo $rowP[1]; ?>,'<?php echo $rowP[3]; ?>',<?php echo $rowP[5]; ?>,'<?php echo $rowP[7]; ?>','<?php echo $rowP[8]; ?>',<?php echo $rowP[9] ?>,<?php echo $rowP[4] ?>,'<?php echo $rowP[10] ?>')" class="campos"><li class="glyphicon glyphicon-edit"></li></a>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[2])) ?>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[3])) ?>
										</td>
										<td class="campos text-center">
											<?php switch ($rowP[4]) {
												case 1:
													echo "SI";
													break;
												case 2:
													echo "NO";
													break;
												default :
													echo "NO";
													break;
											}
											 ?>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[10])) ?>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[6])) ?>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[7])); ?>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[8])) ?>
										</td>
										<td class="campos text-right">
											<?php echo number_format($rowP[9],2,',','.') ?>
										</td>
									</tr>
									<?php }
                                                                        $sqlP = "SELECT DISTINCT dtpt.id_unico, 
                                                                                        dtpt.tipo_partida, 
                                                                                        tpp.nombre, 
                                                                                        date_format(dtpt.fecha_partida,'%d/%m/%Y'), 
                                                                                        dtpt.conciliado, 
                                                                                        dtpt.tipo_documento, 
                                                                                        tpd.nombre, dtpt.numero_documento, 
                                                                                        dtpt.descripcion_detalle_partida, 
                                                                                        dtpt.valor, 
                                                                                        date_format(dtpt.fecha_conciliacion,'%d/%m/%Y')
										FROM gf_detalle_partida dtpt 
										LEFT JOIN gf_tipo_partida tpp ON dtpt.tipo_partida = tpp.id_unico 
										LEFT JOIN gf_tipo_documento tpd ON tpd.id_unico = dtpt.tipo_documento 
										LEFT JOIN gf_partida_conciliatoria prt ON dtpt.id_partida = prt.id_unico
										WHERE	prt.id_cuenta IN ($cuentas) AND prt.mes !=$mes 
                                                                                AND (dtpt.fecha_conciliacion >'$e' AND (dtpt.conciliado =1) )	
										";
									$resultP = $mysqli->query($sqlP);
									while ($rowP = mysqli_fetch_row($resultP)) { ?>
									<tr>
										<td class="oculto"></td>
										<td class="campos" width="7%">
											<a href="#<?php echo $rowP[0] ?>" class="eliminar" title="Eliminar" onclick="return eliminarDetalle(<?php echo $rowP[0] ?>)" class="campos"><li class="glyphicon glyphicon-trash"></li></a>
											<a href="#<?php echo $rowP[0] ?>" class="modificar" title="Modificar" onclick="return modificarDetalle(<?php echo $rowP[0]; ?>,<?php echo $rowP[1]; ?>,'<?php echo $rowP[3]; ?>',<?php echo $rowP[5]; ?>,'<?php echo $rowP[7]; ?>','<?php echo $rowP[8]; ?>',<?php echo $rowP[9] ?>,<?php echo $rowP[4] ?>,'<?php echo $rowP[10] ?>')" class="campos"><li class="glyphicon glyphicon-edit"></li></a>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[2])) ?>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[3])) ?>
										</td>
										<td class="campos text-center">
											<?php switch ($rowP[4]) {
												case 1:
													echo "SI";
													break;
												case 2:
													echo "NO";
													break;
												default :
													echo "NO";
													break;
											}
											 ?>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[10])) ?>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[6])) ?>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[7])); ?>
										</td>
										<td class="campos text-left">
											<?php echo ucwords(mb_strtolower($rowP[8])) ?>
										</td>
										<td class="campos text-right">
											<?php echo number_format($rowP[9],2,',','.') ?>
										</td>
									</tr>
									<?php }
									} ?>
								</tbody>
							</table>
						</div>
					</div>					
				</div>				
			</div>	
		</div> 	
	</div>
	<script type="text/javascript" src="js/select2.js"></script>
	<link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
    	/*Función para ejecutar el datapicker en en el campo fecha*/
		$(function(){
		    var fecha = new Date();
		    var dia = fecha.getDate();
		    var mes = <?php if(empty($mess)){echo date('m');}else{echo $mess;} ?>
		    //var mes = fecha.getMonth() + 1;
		    if(dia < 10){
		        dia = "0" + dia;
		    }
		    if(mes < 10){
		        mes = "0" + mes;
		    }
		    var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
		    $.datepicker.regional['es'] = {
		        closeText: 'Cerrar',
		        prevText: 'Anterior',
		        nextText: 'Siguiente',
		        currentText: 'Hoy',
		        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
		        monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
		        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
		        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
		        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
		        weekHeader: 'Sm',
		        dateFormat: 'dd/mm/yy',
		        firstDay: 1,
		        isRTL: false,
		        showMonthAfterYear: false,
		        yearSuffix: ''
		    };
		    $.datepicker.setDefaults($.datepicker.regional['es']);
		    $("#txtFechaP").datepicker({changeMonth: true}).val(fecAct);		      
		    $("#txtFechaPCon").datepicker({changeMonth: true}).val();
		});
    	//Cuenta
    	$("#sltCuenta").select2({    		
    		placeholder: "Cuenta",
    		allowClear: true 
    	});
    	//Mes
    	$("#sltMes").select2({    		
    		placeholder: "Mes",
    		allowClear: true 
    	});
    	//Tipo Partida
    	$("#sltTipoPartida").select2({    		
    		placeholder: "Tipo Partida",
    		allowClear: true 
    	});
    	//Tipo documento
    	$("#sltTipoDoc").select2({    		
    		placeholder: "Tipo Documento",
    		allowClear: true 
    	});
    	//Estado
    	$("#sltEstado").select2({    		
    		placeholder: "Estado",
    		allowClear: true 
    	});
    	//Función para partida conciliatoria,sin id,ni variables
    	function urlLimpia(){
    		window.location = 'registrar_GF_PARTIDA_CONCILIATORIA.php';
    	}
    	//Función recargar
    	function recargar(){
    		window.location.reload();
    	}    	
    	//Funcion actualizar partida conciliatoria
    	function actualizarPtd(){
    		//Captura de variables
    		var idPartida = $("#txtIdPartida").val();
    		var sltCuenta = $("#sltCuenta").val();
    		var saldoE = $("#txtSaldoE").val();
    		var sltMes = $("#sltMes").val();    		
			var flArchivoC = $("#flArchivoC").val();
    		var txtArchivoC = $("#txtArchivoC").val();
    		var txtDescripcion = $("#txtDescripcion").val();
    		//Array con los valores
    		var form_data = new FormData($("#form")[0]);
    		form_data.append('idPartida',$("#txtIdPartida").val());
    		form_data.append('sltCuenta',$("#sltCuenta").val());
    		form_data.append('saldoE', $("#txtSaldoE").val());
    		form_data.append('sltMes',$("#sltMes").val());
    		form_data.append('txtArchivoC',$("#txtArchivoC").val());
    		form_data.append('txtDescripcion',$("#txtDescripcion").val());
    		//Envio ajax
    		var result = '';
    		$.ajax({
    			type:'POST',
    			url:'json/modificarGFPartidaConciliatoriaJson.php',
    			data:form_data,
    			contentType: false,
                processData: false,
    			success: function(data){
    				result = JSON.parse(data);
    				if(result==true){
    					$("#modalModificar").modal('show');
    				}else{
    					$("#modalNoMod").modal('show');
    				}
    			}
    		});
    	}
    	//Registrar Detalle
    	function registrarDetalle(){
            
    		//Captura de variables
    		var action = 'ingresar';    		
    		var idPartida = $("#txtIdPartida").val();
    		var sltTipoPartida = $("#sltTipoPartida").val();
    		var txtFechaP = $("#txtFechaP").val();
    		var sltTipoDoc = $("#sltTipoDoc").val();
    		var txtNumDoc = $("#txtNumDoc").val();
    		var txtDescripcion = $("#txtDescripcionDetalle").val();
    		var txtValor = $("#txtValor").val();
    		//inicializamos la variable en vacio
    		var optConciliado = "";
    		//Validamos por medios de las id cuando unos de los opt sea seleccionado, y tomamos su valor
    		if($("#optCon1").is(':checked')){
    			var optConciliado = $("#optCon1").val();
    		}else if($("#optCon2").is(':checked')){
    			var optConciliado = $("#optCon2").val();
    		}
    		var txtFechaPCon = $("#txtFechaPCon").val();
    		//Array con los valores
    		var form_data = {
    			idPartida:idPartida,
    			sltTipoPartida:sltTipoPartida,
    			txtFechaP:txtFechaP,
    			sltTipoDoc:sltTipoDoc,
    			txtNumDoc:txtNumDoc,
    			txtDescripcion:txtDescripcion,
    			txtValor:txtValor,
    			action:action,
    			optConciliado:optConciliado,
    			txtFechaPCon:txtFechaPCon
    		};
    		//ajax
    		var result = '';
    		$.ajax({
    			type:'POST',
    			url:'json/registrarGFDetallePartidaC.php',
    			data:form_data,
    			success: function(data){
                            console.log(data);
    				result = JSON.parse(data);
    				if(result==true){
    					$("#modalGuardado").modal('show');
    				}else{
    					$("#modalNoGuardo").modal('show');
    				}
    			}
    		});
    	}
    	//Eliminar detalle
    	function eliminarDetalle(id){
    		//Captura de variables
    		var idDetalle = id;
    		var action = 'eliminar';
    		//Array con los valores
    		var form_data = {
    			id:idDetalle,
    			action:action
    		};    		
    		//Ajax
    		var result = '';
    		$("#myModal").modal('show');
    		$("#ver").click(function(){    			
    			$.ajax({
	    			type:'POST',
	    			url:'json/registrarGFDetallePartidaC.php',
	    			data:form_data,
	    			success: function(data){
	    				result = JSON.parse(data);
	    				if(result==true){
	    					$("#myModal1").modal('show');
	    				}else{
	    					$("#myModal2").modal('show');
	    				}
	    			}
	    		});
    		});    		
    	}
    	//Envio a modal de edición de detalle
    	function modificarDetalle(id,tipop,fechap,tipodoc,ndoc,desc,valor,conciliado,fechac){
    		//Captura valores
    		var id = id;
    		var tipoP = tipop;
    		var fechaP = fechap;
    		var tipoDoc = tipodoc;
    		var numDoc = ndoc;
    		var desc = desc;
    		var valor = valor;
    		var conciliado = conciliado;
    		var fechac = fechac;
    		//Creación de array
    		var form_data = {
    			id:id,
    			tipoP:tipoP,
    			fechaP:fechaP,
    			tipoDoc:tipoDoc,
    			numDoc:numDoc,
    			desc:desc,
    			valor:valor,
    			conciliado:conciliado,
    			fechac:fechac
    		};
    		//Envio por ajax y recarga de html
    		$.ajax({
    			type:'POST',
    			url:'modalModificarDetallePartida.php#modalEditD',
    			data:form_data,
    			success: function(data){
    				$("#modalEditD").html(data);
    				$(".modalEditar").modal('show');
    			}
    		});    		
    	}
    	//Función para abrir modal con listado de conciliaciones
    	function abrirmodalConciliado(){
    		//Captura de variables
    		var cuenta = parseInt($("#sltCuenta").val());
    		var mes = parseInt($("#sltMes").val());
    		//Validación de variables
    		if(!isNaN(cuenta) && !isNaN(mes)){
    			//Creación de array de envio
    			var form_data = {
    				cuenta:cuenta,
    				mes:mes
    			};
    			//Envio por ajax y recarga de html de ventana modal
    			$.ajax({
    				type:'POST',
    				url:'modalListadoConciliaciones.php#modalConciliado',
    				data:form_data,
    				success: function(data){
                                    
    					//Carga de html
    					$("#modalConciliado").html(data);
    					//Invocación de modal por medio de la clase mdlConciliado
    					$(".mdlConciliado").modal('show');
    				}
    			});
    		}else{
    			$("#modalNoConS").modal('show');
    		}
    	}
    	
    	//Función para consultar partida conciliatoria
    	function consultarPartida(){
	    	//Capturamos las variables
	    	var cuenta = parseInt($("#sltCuenta").val());
	    	var mes = parseInt($("#sltMes").val());
	    	//Validamos que no estén vacias
	    	if(!isNaN(cuenta) && !isNaN(mes)){
	    		//Variable de envio
	    		var form_data = {
	    			existente:38,
	    			cuenta:cuenta,
	    			mes:mes
	    		};		    		
	    		//Envio ajax
	    		$.ajax({
	    			type:'POST',
	    			url:'consultasBasicas/consultarNumeros.php',
	    			data:form_data,
	    			success: function(data){		    				
	    				if(data !== ''){
	    					window.location = data;
	    				}
	    			}
	    		});
	    	}
	    }
	    //Función para imprimir
	    function imprimirCon(){
                
               
                if($('input:radio[name=tipoformato]:checked').val() ==1){
                    window.open('informes/inf_conc_doc.php?mes=<?php echo md5($mes) ?>&cuenta=<?php echo md5($cuenta) ?>&partida=<?php echo $partida ?>');
                } else {
                    window.open('informes/inf_conc_doc2.php?mes=<?php echo md5($mes) ?>&cuenta=<?php echo md5($cuenta) ?>&partida=<?php echo $partida ?>');
                }
	    }
	    //Función para imprimir en excel
	    function imprimirConExcel(){
            if($('input:radio[name=tipoformato]:checked').val() ==1){
                    window.open('informes/inf_conc_doc_excel.php?mes=<?php echo md5($mes) ?>&cuenta=<?php echo md5($cuenta) ?>&partida=<?php echo $partida ?>');
                } else {
                    window.open('informes/inf_conc_doc2_excel.php?mes=<?php echo md5($mes) ?>&cuenta=<?php echo md5($cuenta) ?>&partida=<?php echo $partida ?>');
                }
	        
	    }	
    	//Función de validación
		$().ready(function() {
		  var validator = $("#formDetalle").validate({
		        ignore: "",
		    errorPlacement: function(error, element) {
		      
		      $( element )
		        .closest( "form" )
		          .find( "label[for='" + element.attr( "id" ) + "']" )
		            .append( error );
		    },
		  });

		  $(".cancel").click(function() {
		    validator.resetForm();
		  });
		});
	</script>
	<div>
		<?php 
		require_once('footer.php');
		 ?>
	</div>
	<!-- Modales -->
	<!-- Modales de guardado -->
    <div class="modal fade" id="modalGuardado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información guardada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnG" onclick="return recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalNoGuardo" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">          
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha podido guardar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnG2" onclick="return recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Moidales de eliminado -->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Detalle Partida Conciliatoria?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" onclick="return recargar()" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
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
                    <button type="button" id="ver1" onclick="return recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                    <button type="button" id="ver2" onclick="return recargar()" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modales de modificado -->
    <div class="modal fade" id="modalModificar" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información modificada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModifico" onclick="return recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalNoMod" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">          
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha podido modificar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnNoModifico" onclick="return recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de validación de apertura de modal para listado -->
    <div class="modal fade" id="modalNoConS" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">          
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                	<p>Seleccione mes y cuenta</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnNo"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <?php if(!empty($_GET['idPartida'])) { 
        $cierre = cierrepartida(  $idPartida);
  if($cierre ==1){  ?>
  <script>
    $("#txtSaldoE").prop("disabled", true) ;
    $("#btnModificar").prop("disabled", true) ;
    $("#sltTipoPartida").prop("disabled", true) ;
    $("#txtValor").prop("disabled", true) ;
    $("#txtFechaP").prop("disabled", true) ;
    $("#txtFechaPCon").prop("disabled", true) ;
    $("#sltTipoDoc").prop("disabled", true) ;
    $("#txtDescripcionDetalle").prop("disabled", true);
    $("#txtNumDoc").prop("disabled", true) ;
    $("#optCon1").prop("disabled", true) ;
    $("#optCon2").prop("disabled", true) ;
    $("#btnGuardarDP").prop("disabled", true) ;
    $(".eliminar").css('display','none');
      $(".modificar").css('display','none');
    
    
  </script>   
    <?php } } ?>
   <div class="modal fade" id="periodoC" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Periodo ya ha sido cerrado, escoja nuevamente la fecha</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="periodoCA" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div> 
    <!-- Modal de editado -->
    <?php require_once('modalModificarDetallePartida.php') ?>    
    <!-- Modal de listado de conciliaciones -->
    <?php require_once('modalListadoConciliaciones.php'); ?>
</body>
</html>

