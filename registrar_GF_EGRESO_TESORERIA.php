<?php 
require_once ('head_listar.php'); 
require_once ('Conexion/conexion.php');
require_once('./jsonSistema/funcionCierre.php');
require_once './jsonPptal/funcionesPptal.php';
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$num_anno   = anno($_SESSION['anno']);
 ?>
<title>Registrar Egreso Tesoreria</title>
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
</style>
<script type="text/javascript">
/*Función para ejecutar el datapicker en en el campo fecha*/
$(function(){
    var fecha = new Date();
    var dia = fecha.getDate();
    var mes = fecha.getMonth() + 1;
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
        yearSuffix: '',
        yearRange: '<?php echo $num_anno.':'.$num_anno;?>', 
        maxDate: '31/12/<?php echo $num_anno?>',
        minDate: '01/01/<?php echo $num_anno?>'
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
    $("#txtFecha").datepicker({changeMonth: true}).val();    
});
</script>
 </head>
 <body>
 	<div class="container-fluid">
	 	<?php 
			##########################################################################################################
			# Creación de variables nulas
	 		$Egreso = "";
	 		$idEgreso = "";
	 		$fecha = "";
	 		$tipoCom = "";
	 		$nomtipoCom = "";
	 		$numCom = "";
	 		$tercero = "";
	 		$nomTercero = "";
	 		$banco = "";
	 		$descripcion = "";
	 		$estado = "";	 		
	 		$estadoCom="";
	 		$tipoContrato = "";
	 		$nomClaseCon = "";
	 		$numeroContrato = "";
	 		$CuentaPagar = "";
	 		##########################################################################################################
	 		# Cuando la variable egreso no es nula

	 		if(!empty($_GET['egreso'])){
	 			$idEgreso = $_GET['egreso'];
	 			##########################################################################################################
	 			# Consulta de comprobante Egreso
	 			$sqlE = "SELECT cnt.id_unico,date_format(cnt.fecha,'%d/%m/%Y'),cnt.tipocomprobante,tipoCom.nombre,cnt.numero, ter.id_unico,IF(CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' OR CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,(ter.razonsocial),                                            CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD',cnt.descripcion,cnt.estado,estcnt.nombre,cnt.clasecontrato,clcon.nombre,cnt.numerocontrato,tipoCom.sigla
	 			FROM gf_comprobante_cnt cnt
	 			LEFT JOIN gf_tipo_comprobante tipoCom ON cnt.tipocomprobante = tipoCom.id_unico
	 			LEFT JOIN gf_tercero ter ON ter.id_unico = cnt.tercero
	 			LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
	 			LEFT JOIN gf_estado_comprobante_cnt estcnt ON cnt.estado = estcnt.id_unico
	 			LEFT JOIN gf_clase_contrato clcon ON cnt.clasecontrato = clcon.id_unico
	 			WHERE md5(cnt.id_unico) = '$idEgreso'";
	 			$resultE = $mysqli->query($sqlE);
	 			$row = mysqli_fetch_row($resultE);
	 			##########################################################################################################
	 			# Carga de variables
	 			$Egreso = $row[0];
	 			$fecha = $row[1];
	 			$tipoCom = $row[2];
	 			$nomtipoCom = $row[3];
	 			$numCom = $row[4];
	 			$tercero = $row[5];
	 			$nomTercero = $row[6].' '.$row[7];
	 			$descripcion = $row[8];
	 			$estadoCom = $row[10];
	 			$tipoContrato = $row[11];
	 			$nomClaseCon = $row[12];
	 			$numeroContrato = $row[13];
                $siglaCon = $row[14];
	 			###########################################################################################################################################
	 			# Validación de variable banco
	 			if(!empty($_GET['banco'])){
	 				$banco = $_GET['banco'];
	 			}
	 			###########################################################################################################################################
	 			# Validación de variable CuentaPagar
	 			if(!empty($_GET['cuentaPagar'])){
	 				$cuentaPagar = $_GET['cuentaPagar'];
	 			}
	 		}
	 		######### Consulta de estado ##########################
	 		$sqlEs = "SELECT id_unico,nombre FROM gf_estado_comprobante_cnt WHERE id_unico = 1";
            $resultES = $mysqli->query($sqlEs);
            $rowEs = mysqli_fetch_row($resultES);
            $estado = $rowEs[1];
	 		#######################################################
	 	 ?>
 		<div class="row content">
 			<?php require_once ('menu.php'); ?>
 			<div class="col-sm-10 text-left" style="margin-top: -22px">
 				<h2 class="tituloform" align="center">Registrar Egreso Tesoreria</h2>
				<div class="client-form contenedorForma" style="margin-top: -7px;font-size: 10px">
					<form name="form" method="POST" action="json/registrarGFEgresoTesoreria.php" class="form-horizontal" enctype="multipart/form-data">
						<p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p><input type="hidden" id="txtId" name="txtId" value="<?php echo $Egreso; ?>">                        
                        <div class="form-group form-inline">
                        	<label for="txtFecha" class="col-sm-2 control-label">
                                <strong class="obligado">*</strong>Fecha:
                            </label>                                
                            <input type="text" id="txtFecha" name="txtFecha" placeholder="Fecha" title="Fecha" value="<?php echo $fecha==NULL?'':$fecha; ?>" class="form-control col-sm-1" style="width: 100px;height: 30px" required readonly="true">
                            <script type="text/javascript">
                                        $("#txtFecha").change(function()
                                        {
                                          var fecha = $("#txtFecha").val();
                                            var form_data = { case: 4, fecha:fecha};
                                            $.ajax({
                                              type: "POST",
                                              url: "jsonSistema/consultas.php",
                                              data: form_data,
                                              success: function(response)
                                              { 
                                                  if(response ==1){
                                                      $("#periodoC").modal('show');
                                                      $("#txtFecha").val("").focus();

                                                  } else {
                                                      fecha();
                                                  }
                                              }
                                            });   
                                            
                                          
                                        });
                                    </script>
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
                            <label for="sltTipoCom" class="col-sm-2 control-label">
                                <strong class="obligado">*</strong>Tipo Comprobante:
                            </label>                                
                            <select name="sltTipoCom" id="sltTipoCom" title="Seleccione tipo comprobante" style="width: 150px;height: 30px" class="form-control col-sm-1" required>
                            	<?php 
                            	if(!empty($tipoCom)){
                            		echo "<option value=".$tipoCom.">".ucwords(mb_strtolower($nomtipoCom))." ".$siglaCon."</option>";
                            		$sqlTC = "SELECT 	id_unico,nombre,sigla 
                            				FROM 		gf_tipo_comprobante 
                            				WHERE 		clasecontable	= 	14 AND (comprobante_pptal IS NULL) AND compania = $compania 
                            				AND 		id_unico		!=	$tipoCom";
                            		$resultTC = $mysqli->query($sqlTC);
                            		while($rowTC = mysqli_fetch_row($resultTC)){
                            			echo "<option value=".$rowTC[0].">".ucwords(mb_strtolower($rowTC[1]))." ".$rowTC[2]."</option>";
                            		}
                            	}else{
                            		echo "<option value=''>Tipo Comprobante</option>";
                            		$sqlTC = "SELECT 	id_unico,nombre,sigla 
                            				FROM 		gf_tipo_comprobante 
                            				WHERE 		clasecontable 	= 	14 AND compania = $compania  AND (comprobante_pptal IS NULL)";
                            		$resultTC = $mysqli->query($sqlTC);
                            		while($rowTC = mysqli_fetch_row($resultTC)){
                            			echo "<option value=".$rowTC[0].">".ucwords(mb_strtolower($rowTC[1]))." ".$rowTC[2]."</option>";
                            		}
                            	}
                            	 ?>                            	                            	
                            </select>
                            <label for="txtNumeroC" class="col-sm-2 control-label">
                            	<strong class="obligado">*</strong>No Comprobante:
                            </label>
                            <input type="text" name="txtNumeroCom" id="txtNumeroCom" title="Número de Comprobante" placeholder="Número de comprobante" class="form-control col-sm-1" style="width: 150px;height: 30px" value="<?php echo $numCom==""?'':$numCom ?>" onkeypress="return txtValida(event,'num')" required readonly>
                            <!--<a id="btnBuscar" class="btn " title="Buscar Comprobante" style="margin-top:-2px;padding:3px 3px 3px 3px" onclick="consultarE()"><li class="glyphicon glyphicon-search"></li></a>-->
                        </div>
                        <div class="form-group form-inline" style="margin-top: -15px">
                        	<label for="sltTercero" class="control-label col-sm-2">
                        		<strong class="obligado">*</strong>Tercero:
                        	</label>
                        	<select name="sltTercero" id="sltTercero" title="Seleccione tercero" class="form-control col-sm-1" style="width: 435px;height: 30px">
                        		<?php 
                        		if(!empty($tercero)){
                        			echo "<option value=".$tercero.">".ucwords(mb_strtolower($nomTercero))."</option>";
                        			$sqlTer = "SELECT	IF(	CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
			                        								IF(ter.nombredos 	IS NULL,'',ter.nombredos),' ',
			                        								IF(ter.apellidouno 	IS NULL,'',
			                        								IF(ter.apellidouno 	IS NULL,'',ter.apellidouno)),' ',
			                        								IF(ter.apellidodos 	IS NULL,'',ter.apellidodos))='' OR 
                        									CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
                        											IF(ter.nombredos 	IS NULL,'',ter.nombredos),' ',
                        											IF(ter.apellidouno 	IS NULL,'',
                        											IF(ter.apellidouno 	IS NULL,'',ter.apellidouno)),' ',
                        											IF(ter.apellidodos 	IS NULL,'',ter.apellidodos)) IS NULL ,
                        								(ter.razonsocial),
                        									CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
                        											IF(ter.nombredos 	IS NULL,'',ter.nombredos),' ',
                        											IF(ter.apellidouno 	IS NULL,'',
                        											IF(ter.apellidouno 	IS NULL,'',ter.apellidouno)),' ',
                        											IF(ter.apellidodos 	IS NULL,'',ter.apellidodos))) AS 'NOMBRE', 
                                            			ter.id_unico, 
														CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' 
										FROM 			gf_tercero ter
										LEFT JOIN 		gf_tipo_identificacion ti 	
										ON 				ti.id_unico 	= 	ter.tipoidentificacion
										WHERE 			ter.id_unico 	!= 	$tercero AND ter.compania = $compania";
									$resultTer = $mysqli->query($sqlTer);
									while ($rowTer = mysqli_fetch_row($resultTer)) {
										echo "<option value=".$rowTer[1].">".ucwords(mb_strtolower($rowTer[0].PHP_EOL.$rowTer[2]))."</option>";
									}
                        		}else{
                        			echo "<option value=''>Tercero</option>";
                        			$sqlTer = "SELECT 	IF(	CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
                        											IF(ter.nombredos 	IS NULL,'',ter.nombredos),' ',
                        											IF(ter.apellidouno 	IS NULL,'',
                        											IF(ter.apellidouno 	IS NULL,'',ter.apellidouno)),' ',
                        											IF(ter.apellidodos 	IS NULL,'',ter.apellidodos))='' OR 
                        									CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
                        											IF(ter.nombredos 	IS NULL,'',ter.nombredos),' ',
                        											IF(ter.apellidouno 	IS NULL,'',
                        											IF(ter.apellidouno 	IS NULL,'',ter.apellidouno)),' ',
                        											IF(ter.apellidodos 	IS NULL,'',ter.apellidodos)) IS NULL ,
                        								(ter.razonsocial),                                            
                        									CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
                        											IF(ter.nombredos 	IS NULL,'',ter.nombredos),' ',
                        											IF(ter.apellidouno 	IS NULL,'',
                        											IF(ter.apellidouno 	IS NULL,'',ter.apellidouno)),' ',
                        											IF(ter.apellidodos 	IS NULL,'',ter.apellidodos))) AS 'NOMBRE', 
                                            			ter.id_unico, 
														CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' 
										FROM 			gf_tercero ter
										LEFT JOIN 		gf_tipo_identificacion ti 
										ON 				ti.id_unico = ter.tipoidentificacion WHERE ter.compania = $compania";
									$resultTer = $mysqli->query($sqlTer);
									while ($rowTer = mysqli_fetch_row($resultTer)) {
										echo "<option value=".$rowTer[1].">".ucwords(mb_strtolower($rowTer[0].PHP_EOL.$rowTer[2]))."</option>";
									}
                        		}
                        		 ?>                        		                        		
                        	</select>
                        	<label for="sltCuentasPagar" class="control-label col-sm-2">
                        		Cuentas por pagar:
                        	</label>
                        	<select name="sltCuentasPagar" id="sltCuentasPagar" title="Cuentas por pagar" class="form-control col-sm-1" style="width: 150px;height: 30px">
                        		<?php 
                        		if(!empty($tercero)){
                        			#Consulta cuentas por pagar por tercero
                        			echo "<option value=' '>Cuentas por pagar</option>";
                        			$sqlTerC = "SELECT  DISTINCT    cnt.id_unico,
        												            cnt.numero,
        												            cnt.tipocomprobante,
        												            tpc.sigla
												FROM 		gf_detalle_comprobante dtc
												LEFT JOIN	gf_comprobante_cnt cnt 
												ON			cnt.id_unico = dtc.comprobante
												LEFT JOIN 	gf_detalle_comprobante dtcA 
												ON 			dtc.id_unico = dtcA.detalleAfectado
												LEFT JOIN   gf_tipo_comprobante tpc     
												ON 			tpc.id_unico     = cnt.tipocomprobante
												LEFT JOIN   gf_cuenta cta               
												ON 			dtc.cuenta       = cta.id_unico
												WHERE       cnt.tipocomprobante     =  	4 
												AND         cnt.tercero             =  	$tercero 
												AND         cta.clasecuenta         IN 	(4,8,9)
												AND			dtcA.detalleAfectado	IS NULL "
                                                        . "AND cnt.parametrizacionanno = $anno";
						            $resultTerC = $mysqli->query($sqlTerC);
						            $c = mysqli_num_rows($resultTerC);
						            if($c>0){                
						                while ($rowTerC = mysqli_fetch_row($resultTerC)) {
						                    echo "<option value=".$rowTerC[0].">".$rowTerC[1].' '.$rowTerC[3]."</option>";
						                }
						            }
                        		}else{
                        			echo "<option value=' '>Cuentas por pagar</option>";
                        		}
                        		?>
                        	</select>
                        </div>
                        <div class="form-group form-inline" style="margin-top: -5px">
                        	<label for="txtDescripcion" class="control-label col-sm-2">
                        		Descripción:
                        	</label>
                        	<textarea name="txtDescripcion" id="txtDescripcion" rows="2" maxlength="1000" style="height:30px;width:435px;margin-top: 0px" placeholder="Descripción" class="form-control area col-sm-1"><?php echo $descripcion; ?></textarea>
                        	<label for="sltBanco" class="control-label col-sm-2">
                        		Banco:
                        	</label>
                        	<select name="sltBanco" id="sltBanco" title="Seleccione banco" class="form-control col-sm-1" style="width: 150px;height: 30px">
                                    <?php  $anno = $_SESSION['anno'];
                                    if(!empty($banco)){
                                        $sqlB ="SELECT  	ctb.id_unico,
                                            CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')'),
                                            c.codi_cuenta , c.id_unico 
                                        FROM 	gf_cuenta_bancaria ctb
                                        LEFT JOIN   gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria
                                        LEFT JOIN   gf_cuenta c ON ctb.cuenta = c.id_unico                                             
                                        WHERE 	md5(ctb.id_unico) 	= '$banco'";
                                        $resultB = $mysqli->query($sqlB);
                                        $rowB = mysqli_fetch_row($resultB);
                                        $sum = "SELECT SUM(valor) FROM gf_detalle_comprobante dc 
                                        LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                        WHERE dc.cuenta = $rowB[3] AND cn.parametrizacionanno = $anno";
                                        $sum = $mysqli->query($sum);
                                        if(mysqli_num_rows($sum)>0) { 
                                            $val= mysqli_fetch_row($sum);
                                        if($val[0]==NULL){$val=0;}else{
                                            $val = $val[0];}
                                        } else {
                                            $val = 0;
                                        }
                                        echo "<option value=".$rowB[0].">".ucwords(mb_strtolower($rowB[1])).' Saldo: $'.number_format($val,2,'.',',')."</option>";
                                        $sqlBan = "SELECT  	ctb.id_unico,
                                            CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')'), 
                                            c.codi_cuenta , c.id_unico 
                                            FROM        gf_cuenta_bancaria ctb
                                            LEFT JOIN	gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria
                                            LEFT JOIN   gf_cuenta c ON ctb.cuenta = c.id_unico 
                                            WHERE 		ctbt.tercero 		= '". $_SESSION['compania']."' 
                                            AND 		md5(ctb.id_unico) 	!= '$banco' 
                                            and ctb.parametrizacionanno = $anno ORDER BY ctb.numerocuenta";
                                    } else {
                                        $sqlBan = "SELECT  	ctb.id_unico,
                                                CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')'),
                                                c.codi_cuenta , c.id_unico 
                                        FROM 		gf_cuenta_bancaria ctb
                                        LEFT JOIN	gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria
                                        LEFT JOIN   gf_cuenta c ON ctb.cuenta = c.id_unico 
                                        WHERE 		ctbt.tercero 	='". $_SESSION['compania']."' 
                                        and ctb.parametrizacionanno = $anno ORDER BY ctb.numerocuenta";
                                    }
                                    $resultBan = $mysqli->query($sqlBan);
                                    echo '<option value=""> Banco </option>';
                                    while($rowBan = mysqli_fetch_row($resultBan)){
                                        $sum = "SELECT SUM(valor) FROM gf_detalle_comprobante dc 
                                            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                            WHERE dc.cuenta = $rowBan[3] AND cn.parametrizacionanno = $anno";
                                        $sum = $mysqli->query($sum);
                                        if(mysqli_num_rows($sum)>0) { 
                                        $val= mysqli_fetch_row($sum);
                                        if($val[0]==NULL){$val=0;}else{
                                        $val = $val[0];}
                                        } else {
                                        $val = 0;
                                        }
                                        echo '<option value="'.$rowBan[0].'">'.ucwords(mb_strtolower($rowBan[1])).' Saldo: $'.number_format($val,2,'.',',').'</option>';
                                    }
                        		
                        		 ?>                        		                        		
                        	</select>
                        </div>
                        <div class="form-group form-inline" style="margin-top: -15px;">
                        	<label for="sltTipoDoc" class="control-label col-sm-2">
                        		Tipo Contrato:
                        	</label>
                        	<select name="sltTipoCon" id="sltTipoCon" title="Tipo contrato" style="width: 100px;height: 30px" class="form-control col-sm-1">
                        		<?php 
                        		if(!empty($tipoContrato)){
                        			echo "<option value=".$tipoContrato.">".ucwords(mb_strtolower($nomClaseCon))."</option>";
                        			$sqlTpC = "SELECT	id_unico,nombre 
                        					FROM 		gf_clase_contrato 
                        					WHERE 		id_unico 	!= 	$tipoContrato 
                        					ORDER BY 	id_unico ASC";
                        			$resultTpC = $mysqli->query($sqlTpC);
                        			while ($rowTpC = mysqli_fetch_row($resultTpC)) {
                        				echo "<option value=".$rowTpC[0].">".ucwords(mb_strtolower($rowTpC[1]))."</option>";
                        			}
                        		}else{
                        			echo "<option value=''>Tipo Contrato</option>";
                        			$sqlTpC = "SELECT 	id_unico,nombre 
                        					FROM 		gf_clase_contrato 
                        					ORDER BY 	id_unico ASC";
                        			$resultTpC = $mysqli->query($sqlTpC);
                        			while ($rowTpC = mysqli_fetch_row($resultTpC)) {
                        				echo "<option value=".$rowTpC[0].">".ucwords(mb_strtolower($rowTpC[1]))."</option>";
                        			}
                        		}
                        		 ?>                        		                        		
                        	</select>
                        	<label for="txtNumeroC" id="txtNumeroC" class="control-label col-sm-2">
                        		No Contrato:
                        	</label>
                        	<input type="text" title="Número contrato" id="txtNumeroC" name="txtNumeroC" title="Número Contrato" placeholder="Número contrato" style="width: 150px;height: 30px" class="form-control col-sm-1" value="<?php echo $numeroContrato==""?'':$numeroContrato ?>">
                        	<label for="txtEstado" class="control-label col-sm-2">
                        		Estado:
                        	</label>
                        	<input type="text" class="form-control col-sm-1" placeholder="Estado" title="Estado" value="<?php echo $estadoCom==""?$estado:$estadoCom; ?>" style="width: 150px;height: 30px" readonly>
                        </div>
                        <div class="col-sm-1 col-sm-offset-11" style="margin-top:-160px;width: 110px;right: 35px">
                    		<div class="col-sm-1" style="width: 40px;margin-bottom: 5px;left: -5px">
                    			<a class="btn btn-primary" id="btnNuevo" name="btnNuevo" onclick="return urlLimpia()" title="Nueva Conciliación" style="width: 40px;height: 34px;cursor: pointer;"><span class="glyphicon glyphicon-plus"></span></a>
                    		</div>
                    		<div class="col-sm-1" style="width: 40px;margin-bottom: 5px">
                        		<button type="submit" class="btn btn-primary" id="btnGuardar" name="btnGuardar" title="Guardar Conciliación" style="width: 40px;height:34px;"><span class="glyphicon glyphicon-floppy-disk"></span></button>                        	
                        	</div>	                        	
                        	<div class="col-sm-1" style="width: 40px;margin-bottom: 5px;left: -5px">
                                <a class="btn btn-primary" id="btnImprimir" onclick="return informe()" name="btnImprimir" style="width: 40px;height: 34px;"><span class="glyphicon glyphicon glyphicon-print"></span></a>
                            </div>
                        	<div class="col-sm-1" style="width: 40px;;margin-bottom: 5px">
                                    <button type="button" class="btn btn-primary" id="btnModificar" onclick="return actualizar()" name="btnModificar" style="width: 40px;height:34px;"><span class="glyphicon glyphicon-pencil"></span></button>
                        	</div>
                            <div class="col-sm-1" style="width: 40px;left: -5px">
                                <button class="btn btn-primary" id="btnAgregarC" onclick="return agregarCuentaP()" title="Agregar cuenta por pagar" name="btnAgregarC" style="width: 40px;height: 34px"><li class="glyphicon glyphicon-pushpin"></li></button>
                            </div>
                                
                    	</div>
                        <div class="form-inline form-group" style="margin-top: -20px;margin-bottom: 5px">
                            <label for="sltBuscar" class="control-label col-sm-2">Buscar :</label>
                            <select name="sltBuscar" id="sltBuscar" onchange="return consultarE()" title="Buscar egreso" class="form-control col-sm-1 detalle" style="width: 200px;height: 30px;">
                                <?php 
                                    echo "<option value=''>Buscar comprobante</option>";  
                                    ##########################################################################################################################
                                    # Consulta para datos de busqueda
                                    #
                                    ###########################################################################################################################
                                    $sqlCP = "SELECT        cnt.id_unico,
                                                            cnt.numero,
                                                            tpc.sigla,
                                                            IF( CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                                    IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                                    IF(ter.apellidouno IS NULL,'',
                                                                    IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                                                    IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' 
                                                            OR  CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                                        IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                                        IF(ter.apellidouno IS NULL,'',
                                                                        IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                                                        IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,
                                                            (ter.razonsocial),                                            
                                                                CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                                        IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                                        IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                                                        IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
                                                            ter.numeroidentificacion, DATE_FORMAT(cnt.fecha,'%d/%m/%Y')
                                                FROM        gf_comprobante_cnt cnt
                                                LEFT JOIN   gf_tipo_comprobante tpc     ON cnt.tipocomprobante      = tpc.id_unico
                                                LEFT JOIN   gf_tercero ter              ON cnt.tercero              = ter.id_unico
                                                LEFT JOIN   gf_tipo_identificacion ti   ON ter.tipoidentificacion   = ti.id_unico
                                                WHERE       tpc.clasecontable=14 AND tpc.comprobante_pptal IS NULL AND cnt.parametrizacionanno = $anno                                                 
                                                ORDER BY    cnt.numero desc";
                                    $resultCP = $mysqli->query($sqlCP);
                                    ##########################################################################################################################
                                    # Impresión de datos                                        
                                    #
                                    ###########################################################################################################################
                                    while ($rowCP = mysqli_fetch_row($resultCP)) {
                                        ######################################################################################################################
                                        # Consulta de valor de comprobante
                                        #
                                        ######################################################################################################################    
                                       $sqlVA = "SELECT    SUM(if(dtc.valor>0, dtc.valor, dtc.valor*-1) )  
                                                FROM      gf_detalle_comprobante dtc 
                                                LEFT JOIN gf_comprobante_cnt cnt ON dtc.comprobante = cnt.id_unico 
                                                LEFT JOIN gf_cuenta c ON dtc.cuenta = c.id_unico 
                                                WHERE     cnt.id_unico = $rowCP[0] AND ((c.naturaleza=2 and dtc.valor<0) OR  (c.naturaleza=1 and dtc.valor>0))
                                                ";
                                            $resultVA = $mysqli->query($sqlVA);
                                            $valorVA = mysqli_fetch_row($resultVA);
                                            ######################################################################################################################
                                            # Impresión de valores
                                            #
                                            ######################################################################################################################
                                            echo "<option value=".$rowCP[0].">".$rowCP[1]." ".$rowCP[2]." ".$rowCP[5]." ".$rowCP[3]." ".$rowCP[4]." $".number_format($valorVA[0],2,',','.')."</option>";
                                    }
                                ?>
                            </select>
                        </div>
					</form>
				</div>
				<div class="col-sm-12">
					<div class="client-form col-sm-12" style="margin-top:5px;border-radius: 5px;box-shadow: inset 1px 1px 1px 1px gray;">
						<form action="json/registrarGFDetalleEgresoTesoriaJson.php" class="form-horizontal" method="POST" class="form-horizontal" style="margin-top: 10px;">
							<div class="form-group form-inline" style="margin-bottom: -3px">
								<label for="sltCuentaE" class="control-label col-sm-1 detalle" style="width: 65px">
									<strong class="obligado">*</strong>Cuenta:
								</label>
								<select name="sltCuentaE" id="sltCuentaE" title="Seleccione cuenta contable" class="col-sm-1 form-control detalle" style="width: 100px;height: 30px">
									<?php 
									echo "<option value=' '>Cuenta</option>";
									$sqlC = "SELECT 	id_unico,CONCAT(codi_cuenta,' ',nombre) 
											FROM 		gf_cuenta  WHERE parametrizacionanno = $anno 
                                                                                        AND (movimiento =1 OR auxiliartercero =1 OR auxiliarproyecto =1 OR centrocosto =1)
											ORDER BY 	codi_cuenta ASC";
									$resultC = $mysqli->query($sqlC);
									while ($rowC = mysqli_fetch_row($resultC)) {
										echo "<option value=".$rowC[0].">".ucwords(mb_strtolower($rowC[1]))."</option>";
									}
									 ?>
								</select>
								<label for="sltTerceroE" class="control-label col-sm-1 detalle" style="width: 65px">
									Tercero:
								</label>
								<select name="sltTerceroE" id="sltTerceroE" title="Seleccione tercero" class="col-sm-1 form-control detalle" style="width: 100px;height: 30px" disabled>									
                                                                    <?php if (!empty($_GET['egreso'])){echo "<option value=".$tercero.">".ucwords(mb_strtolower($nomTercero))."</option>";}?>
								</select>
								<label for="sltCentroCE" class="col-sm-1 control-label detalle" style="width: 65px">
									Centro Costo:
								</label>
								<select name="sltCentroCE" id="sltCentroCE" title="Seleccione centro costo" class="col-sm-1 form-control detalle" style="width: 100px;height: 30px" disabled="">									
								</select>
								<label for="sltProyectoE" class="control-label col-sm-1 detalle" style="width: 75px">
									Proyecto:
								</label>
								<select name="sltProyectoE" id="sltProyectoE" title="Seleccione proyecto" class="col-sm-1 form-control detalle" style="width: 100px;height: 30px" disabled>				
								</select>
								<label for="txtValorE" class="control-label col-sm-1 detalle" style="width: 65px">
									Valor Debito:
								</label>
								<input type="text" id="txtValorE" name="txtValorE" title="Valor débito" class="col-sm-1 form-control detalle" style="width: 100px;height: 30px" onkeypress="return justNumbers(event)" placeholder="Valor Débito" required>
                                <label for="txtValorCE" class="control-label col-sm-1 detalle" style="width: 65px"> 
                                    Valor Crédito:
                                </label>
                                <input type="text" id="txtValorCE" name="txtValorCE" title="Valor crédito" class="col-sm-1 form-control detalle" style="width: 100px;height: 30px" onkeypress="return justNumbers(event)" placeholder="Valor Crédito" required>
								<div class="col-sm-1" style="width: 40px">
									<a class="btn btn-primary" onclick="return registrarDetalle()" id="btnGuardarDE" name="btnGuardarDE" title="Guardar detalle egreso" style="width: 40px;height: 34px;cursor: pointer;"><span class="glyphicon glyphicon-floppy-disk"></span></a>
									<?php if(empty($Egreso)){ ?>
										<script>
											$("#btnGuardarDE").attr('disabled',true);
										</script>
									<?php }else{ ?>
										<script>
											$("#btnGuardarDE").attr('disabled',false);
										</script>
									<?php } ?>																		
								</div>
							</div>
						</form>
					</div>
				</div>
				<?php $sumD =0; $sumC=0;?>
				<input type="hidden" id="idPrevio" value="">
                <input type="hidden" id="idActual" value="">
				<div class="col-sm-12">
				<?php
				if(!empty($Egreso)){
					$sqlD = "SELECT 	dte.id_unico, 
										dte.cuenta, 
										CONCAT(cta.codi_cuenta,' ',cta.nombre), 
										cta.naturaleza, 
										dte.valor, 
										IF(CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
													IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
													IF(ter.apellidouno IS NULL,'',
													IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
													IF(ter.apellidodos IS NULL,'',ter.apellidodos))		='' 
										OR CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
													IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
													IF(ter.apellidouno IS NULL,'',
													IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
													IF(ter.apellidodos IS NULL,'',ter.apellidodos)) 	IS NULL 
										,(ter.razonsocial),
										CONCAT(		IF(ter.nombreuno='','',ter.nombreuno),' ',
													IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
													IF(ter.apellidouno IS NULL,'',
													IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
													IF(ter.apellidodos IS NULL,'',ter.apellidodos))) 	AS 'NOMBRE', 
										dte.centrocosto, 
										ctc.nombre, 
										dte.proyecto, 
										pro.nombre
							FROM 		gf_detalle_comprobante dte 
							LEFT JOIN 	gf_cuenta cta 			ON 	cta.id_unico = dte.cuenta
							LEFT JOIN 	gf_tercero ter 			ON 	ter.id_unico = dte.tercero
							LEFT JOIN 	gf_centro_costo ctc 	ON 	ctc.id_unico = dte.centrocosto
							LEFT JOIN 	gf_proyecto pro 		ON 	pro.id_unico = dte.proyecto
							WHERE 		dte.comprobante=$Egreso";
					$resultC = $mysqli->query($sqlD);
				}
				  ?>
					<div class="table-responsive contTabla">
						<div class="table-responsive contTabla">
							<table id="tabla" class="table table-striped table-condensed display detalle" cellpadding="0" width="100%">
								<thead>
									<tr>
										<td class="oculto">Id</td>
										<td class="cabeza" width="7%"></td>
										<td class="cabeza"><strong>Cuenta</strong></td>
										<td class="cabeza"><strong>Débito</strong></td>
										<td class="cabeza"><strong>Crédito</strong></td>
										<td class="cuenta"><strong>Tercero</strong></td>
										<td class="cuenta"><strong>Centro Costo</strong></td>
										<td class="cabeza"><strong>Proyecto</strong></td>
                                                                                <td class="cabeza"><strong>Documento</strong></td>
									</tr>
									<tr>
										<th class="oculto">Id</th>
										<th class="cabeza" width="7%"></th>
										<th class="cabeza">Cuenta</th>
										<th class="cabeza">Débito</th>
										<th class="cabeza">Crédito</th>
										<th class="cabeza">Tercero</th>
										<th class="cabeza">Centro Costo</th>
										<th class="cabeza">Proyecto</th>
                                                                                <th class="cabeza">Documento</th>
									</tr>
								</thead>
								<tbody>
								<?php 								
								while ($rowC = mysqli_fetch_row($resultC)) { ?>
								<tr>
									<td class="oculto"></td>
									<td class="campos" width="7%">
                                                                               <?php if(!empty($_GET['egreso'])){
                                                                                    $cierre = cierrecnt($Egreso);
                                                                                    if($cierre ==0){ ?>
										<a href="#<?php echo $rowC[0] ?>" onclick="return eliminarDetalle(<?php echo $rowC[0] ?>)" title="Eliminar" class="campos"><li class="glyphicon glyphicon-trash"></li></a>
										<a href="#<?php echo $rowC[0] ?>" onclick="return mostrarCampos(<?php echo $rowC[0] ?>)" title="Modificar" class="campos"><li class="glyphicon glyphicon-edit"></li></a>
                                                                               <?php } } ?>
									</td>
									<td class="campos">
										<?php echo ucwords(mb_strtolower($rowC[2])); ?>
									</td>
									<td class="campos text-right">
										<?php 										
										#Validación de naturaleza débito, y que sea mayor que 0
										if($rowC[3] == 1){
											if($rowC[4] > 0){
												$sumD += $rowC[4];
												echo '<label class="control-label" style="font-weight:normal" id="lblDebito'.$rowC[0].'">'.number_format($rowC[4],2,'.',',').'</label>';
												echo '<input type="text" name="txtDebito'.$rowC[0].'" id="txtDebito'.$rowC[0].'" value="'.$rowC[4].'" placeholder="Debito" title="Valor Debito" class="form-control col-sm-12 text-right" style="height:19px;padding:2px;display:none" onkeypress="return justNumbers(event)">';
											}else{
												echo '<label class="control-label" style="font-weight:normal" id="lblDebito'.$rowC[0].'">0.00</label>';
												echo '<input type="text" name="txtDebito'.$rowC[0].'" id="txtDebito'.$rowC[0].'" value="0" placeholder="Debito" title="Valor Debito" class="form-control col-sm-12 text-right" style="height:19px;padding:2px;display:none" onkeypress="return justNumbers(event)">';
											}
										}elseif($rowC[3]==2){
											if($rowC[4] < 0){
												$x = (float) substr($rowC[4],'1');
												$sumD += $x;
												echo '<label class="control-label" style="font-weight:normal" id="lblDebito'.$rowC[0].'">'.number_format($x,2,'.',',').'</label>';
												echo '<input type="text" name="txtDebito'.$rowC[0].'" id="txtDebito'.$rowC[0].'" value="'.$x.'" placeholder="Debito" title="Valor Debito" class="form-control col-sm-12 text-right" style="height:19px;padding:2px;display:none" onkeypress="return justNumbers(event)">';
											}else{
												echo '<label class="control-label" style="font-weight:normal" id="lblDebito'.$rowC[0].'">0.00</label>';
												echo '<input type="text" name="txtDebito'.$rowC[0].'" id="txtDebito'.$rowC[0].'" value="0" placeholder="Debito" title="Valor Debito" class="form-control col-sm-12 text-right" style="height:19px;padding:2px;display:none" onkeypress="return justNumbers(event)">';
											}
										}

										 ?>
									</td>
									<td class="campos text-right">
										<?php 										
										#Validación de naturaleza crédito, y que sea mayor que 0
										if($rowC[3] == 2){
											if($rowC[4] > 0){
												$sumC += $rowC[4];
												echo '<label class="control-label" style="font-weight:normal" id="lblCredito'.$rowC[0].'">'.number_format($rowC[4],2,'.',',').'</label>';
												echo '<input type="text" name="txtCredito'.$rowC[0].'" id="txtCredito'.$rowC[0].'" value="'.$rowC[4].'" placeholder="Credito" title="Valor Credito" class="form-control col-sm-12 text-right" style="height:19px;padding:2px;display:none" onkeypress="return justNumbers(event)">';

											}else{
												echo '<label class="control-label" style="font-weight:normal" id="lblCredito'.$rowC[0].'">0.00</label>';
												echo '<input type="text" name="txtCredito'.$rowC[0].'" id="txtCredito'.$rowC[0].'" value="0" placeholder="Credito" title="Valor Credito" class="form-control col-sm-12 text-right" style="height:19px;padding:2px;display:none" onkeypress="return justNumbers(event)">';
											}
										}else{
											if($rowC[4] < 0){
												$x = (float) substr($rowC[4],'1');
												$sumC += $x;
												echo '<label class="control-label" style="font-weight:normal" id="lblCredito'.$rowC[0].'">'.number_format($x,2,'.',',').'</label>';
												echo '<input type="text" name="txtCredito'.$rowC[0].'" id="txtCredito'.$rowC[0].'"value="'.$x.'" placeholder="Credito" title="Valor Credito" class="form-control col-sm-12 text-right" style="height:19px;padding:2px;display:none" onkeypress="return justNumbers(event)">';
											}else{
												echo '<label class="control-label" style="font-weight:normal" id="lblCredito'.$rowC[0].'">0.00</label>';
												echo '<input type="text" name="txtCredito'.$rowC[0].'" id="txtCredito'.$rowC[0].'" value="0" placeholder="Credito" title="Valor Credito" class="form-control col-sm-12 text-right" style="height:19px;padding:2px;display:none" onkeypress="return justNumbers(event)">';
											}
										}

										 ?>
									</td>
									<td class="campos">
                                                                            <div id="divTercero<?php echo $rowC[0];?>" name="divTercero" style="display:none">
                                                                                <select name="sltTerceroM<?php echo $rowC[0];?>" id="sltTerceroM<?php echo $rowC[0];?>" title="Seleccione Tercero" class="select2_single col-sm-1 form-control detalle" style="width: 180px;height: 30px">
										    <?php if (!empty($_GET['egreso'])) { 
                                                                                        echo "<option value=".$tercero.">".ucwords(mb_strtolower($nomTercero))."</option>";
                                                                                    } else {
                                                                                        echo '<option value=""> - </option>';
                                                                                    } 
                                                                                    $rowter = "SELECT t.id_unico, 
                                                                                        IF(CONCAT_WS(' ',
                                                                                            t.nombreuno,
                                                                                            t.nombredos,
                                                                                            t.apellidouno,
                                                                                            t.apellidodos) 
                                                                                            IS NULL OR CONCAT_WS(' ',
                                                                                            t.nombreuno,
                                                                                            t.nombredos,
                                                                                            t.apellidouno,
                                                                                            t.apellidodos) = '',
                                                                                            (t.razonsocial),
                                                                                            CONCAT_WS(' ',
                                                                                            t.nombreuno,
                                                                                            t.nombredos,
                                                                                            t.apellidouno,
                                                                                            t.apellidodos)) AS NOMBRE,
                                                                                        IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                                                            t.numeroidentificacion, 
                                                                                        CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                                                                      FROM
                                                                                        gf_tercero t WHERE t.compania = $compania";
                                                                                    $rowter = $mysqli->query($rowter);
                                                                                    while ($rowT = mysqli_fetch_row($rowter)) {
                                                                                        echo "<option value=".$rowT[0].">".ucwords(mb_strtolower($rowT[1])).' - '.$rowT[2]."</option>";
                                                                                    }
                                                                                    ?>
                                                                                    
                                                                                </select>
                                                                            </div>
                                                                            <?php  echo '<label class="control-label" style="font-weight:normal" id="lblTercero'.$rowC[0].'">'.ucwords(mb_strtolower($rowC[5])).'</label>';?>
									</td>
									<td class="campos">
										<?php echo ucwords(mb_strtolower($rowC[7])) ?>
									</td>
									<td class="campos">
                                                                            <?php echo ucwords(mb_strtolower($rowC[9])) ?>
                                                                            <div class="col-sm-1">
                                                                                <table id="tab<?php echo $rowC[0] ?>" style="padding:0px;background-color:transparent;background:transparent;">
                                                                                    <tbody>
                                                                                        <tr style="background-color:transparent;">
                                                                                            <td style="background-color:transparent;">
                                                                                                <a  href="#<?php echo $rowC[0];?>" title="Guardar" id="guardar<?php echo $rowC[0]; ?>" style="display: none;" onclick="javascript:guardarDetalle(<?php echo $rowC[0]; ?>,<?php echo $rowC[3] ?>)">
                                                                                                    <li class="glyphicon glyphicon-floppy-disk" style="margin-left: -19px;"></li>
                                                                                                </a>
                                                                                            </td>
                                                                                            <td style="background-color:transparent;">
                                                                                                <a href="#<?php echo $rowC[0];?>" title="Cancelar" id="cancelar<?php echo $rowC[0] ?>" style="display: none" onclick="javascript:cancelar(<?php echo $rowC[0];?>)" >
                                                                                                    <i title="Cancelar" class="glyphicon glyphicon-remove" style="margin-left: -10px;" ></i>
                                                                                                </a>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
									</td>
                                                                        <td>
                                                                                <div style="display:inline">
                                                                                    <a id="btnDetalleMovimiento" onclick="javascript:abrirdetalleMov(<?php echo $rowC[0]?>,<?php echo $rowC[4]?>);" title="Comprobante detalle movimiento"><i class="glyphicon glyphicon-file"></i></a>                                        

                                                                                </div>
                                                                        </td>
								</tr>
								<?php } ?>									
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<!-- Totales -->					
					<div class="col-sm-12">
						<div class="col-sm-6 col-sm-offset-3">
							<div class="form-group form-inline form-horizontal">
								<label for="" class="control-label col-sm-1">
									<strong class="detalle">Totales:</strong>
								</label>
								<label for="" class="control-label col-sm-2 detalle text-right col-sm-offset-1" style="cursor: pointer;" title="Valor débito"><?php echo $sumD==""?'0.00':number_format($sumD,2,'.',',') ?></label>
								<label for="" class="control-label col-sm-2 detalle text-right col-sm-offset-1" style="cursor: pointer;" title="Valor crédito"><?php echo $sumC==""?'0.00':number_format($sumC,2,'.',',') ?></label>
								<?php $dif = $sumD - $sumC; ?>
								<label for="" class="control-label col-sm-2 detalle text-right col-sm-offset-1" style="cursor: pointer;" title="Diferencia"><?php echo $dif==""?'0.00':number_format($dif,2,'.','.'); ?></label>								
							</div>							
						</div>
						<a class="btn btn-primary col-sm-1 text-center col-sm-offset-2" id="btnGuardarPET" name="btnGuardarPET" style="width: 84px;font-size: 9px;margin-top: 5px" onclick="return guardarBanco()">Guardar Pago</a>
					</div>
 			</div>
 		</div>
 	</div>
 	<div>
 		<?php require_once ('footer.php'); ?>
 	</div>
        <script type="text/javascript" >
            function abrirdetalleMov(id,valor){                                                                                                   
                var form_data={                            
                id:id,
                valor:valor
                };
                $.ajax({
                    type: 'POST',
                    url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO.php#mdlDetalleMovimiento",
                    data:form_data,
                    success: function (data) { 
                        $("#mdlDetalleMovimiento").html(data);
                        $(".mov").modal('show');
                    }
                });

            }                                                                                        
        </script>
 	<script type="text/javascript" src="js/select2.js"></script>
	<link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(".select2_single").select2({
            allowClear:true
        }); 
    	//Tipo de comprobante
    	$("#sltTipoCom").select2({
    		allowClear:true
    	});
    	//Tercero
    	$("#sltTercero").select2({
    		allowClear:true
    	});
    	//Banco
    	$("#sltBanco").select2({
    		allowClear:true
    	});
    	//Tipo Contrato
    	$("#sltTipoCon").select2({
    		allowClear:true
    	});
    	//Cuenta
    	$("#sltCuentaE").select2({
    		allowClear:true,
    		placeholder:"Cuenta"
    	});
    	//Tercero detalle
    	$("#sltTerceroE").select2({
    		allowClear:true,
    		placeholder:'Tercero'
    	});
        $("#sltTerceroM").select2({
    		allowClear:true,
    		placeholder:'Tercero'
    	});
    	//Centro Costo detalle
    	$("#sltCentroCE").select2({
    		placeholder: "Centro Costo",
            allowClear: true
    	});
    	//Poryecto Detalle
    	$("#sltProyectoE").select2({
    		placeholder: "Proyecto",
    		allowClear: true
    	});
    	//Cuentas por pagar
    	$("#sltCuentasPagar").select2({
    		placeholder: "Cuentas por pagar",
    		allowClear: true
    	});
        //Buscar
        $("#sltBuscar").select2({
            placeholder: "Buscar",
            allowClear: true
        });
    	//Función de devolver url
    	function urlLimpia(){
    		window.location = 'registrar_GF_EGRESO_TESORERIA.php';
    	}
    	//Función recargar
    	function recargar(){
    		window.location.reload();
    	}
    	//Función generar nuevos al cambio de tipo en el campo tipo de comprobante
    	$("#sltTipoCom").change(function(){
    		//Array con valores
    		var form_data = {
    			id_tip_comp:$("#sltTipoCom").val(),
    			estruc:24
    		};
    		//Envio de ajax
    		$.ajax({
    			type:'POST',
    			url: "jsonPptal/consultas.php",
    			data:form_data,
    			success: function(data){
                            console.log(data);
    				//Devuele el valor encontrado + 1 para generar consecutivo
    				$("#txtNumeroCom").val(data);
    			}
    		});
    	});
    	//Función para modificar
    	function actualizar(){
    		//Captura de variables
    		var fechaE = $("#txtFecha").val();    		
    		var descrE = $("#txtDescripcion").val();    		
    		var id = $("#txtId").val();
                                           var tercero = $("#sltTercero").val();
    		//Array de datos
    		var form_data = {
    			txtFecha:fechaE,    			
    			txtDescripcion:descrE,    			
    			id:id  , 
                                                                tercero:tercero 
    		};
    		//Envio ajax
    		var result = '';
    		$.ajax({
    			type:'POST',
    			url:'json/modificarGFEgresoTesoreriaJson.php',
    			data:form_data,
    			success: function(data){    				
    				result = JSON.parse(data);
    				if(result==true){
    					$("#modalModificar").modal('show');
    				}else{
    					$("#modalNoMod").modal("show");
    				}
    			}
    		});
    	}
    	
        //Consultar tercero por por indicador de cuenta auxtercero
        function consultarTercero(cuenta){
            var form_data={
                is_ajax:1,
                cuenta:cuenta,
                ter:<?php echo $tercero==""?'0':$tercero; ?>
            };
            $.ajax({
                type: 'POST',
                url: "consultarComprobanteIngreso/consultarTercero.php",
                data:form_data,
                success: function (data) {
                    $("#sltTerceroE").html(data).fadeIn();
                    $("#sltTerceroE").css('display','none');
                }
            });
        }
        var padre = 0;        
        $("#sltCuentaE").change(function(){
	        if ($("#sltCuentaE").val()=="" || $("#sltCuentaE").val()==0) {
	            padre = 0;         
	            $("#sltTerceroE").prop('disabled',true);
	        }else{
	            padre = $("#sltCuentaE").val();
	        }
	        var form_data = {
	            is_ajax:1,
	            data:+padre
	        };                                        
	        $.ajax({
	            type:"POST",
	            url:"consultasDetalleComprobante/consultarTercero.php",
	            data:form_data,                                                    
	            success: function (data) {	                
	                if (data==1) {
	                    $("#sltTerceroE").attr('disabled',false);
	                    consultarTercero($("#sltCuentaE").val());
	                }else if(data==2){
	                    $("#sltTerceroE").attr('disabled',true);
	                }                                                       
	            }
	        });
	    });
        //Consultar centro de costo por indicador de cuenta auxcentroc
        function consultarCentro(cuenta){
	        var form_data={
	            is_ajax:1,
	            cuenta:cuenta
	        };
	        $.ajax({
	            type: 'POST',
	            url: "consultarComprobanteIngreso/centroCosto.php",
	            data:form_data,
	            success: function (data) {
	                $("#sltCentroCE").html(data).fadeIn();
	                $("#sltCentroCE").css('display','none');
	            }
	        });
	    }
	    var padre = 0;  	    
	    $("#sltCuentaE").change(function(){
	        if ($("#sltCuentaE").val()=="" || $("#sltcuenta").val()==0) {
	            padre = 0;
	            $("#sltCentroCE").attr('disabled',true);
	        }else{
	            padre = $("#sltCuentaE").val();
	        }
	        
	        var form_data = {
	            is_ajax:1,
	            data:+padre
	        };
	        $.ajax({
	            type:"POST",
	            url:"consultasDetalleComprobante/consultarCentroC.php",
	            data:form_data,                                         
	            success: function (data) {
	                if (data==1) {
	                    $("#sltCentroCE").attr('disabled',false);
	                    consultarCentro($("#sltCuentaE").val());
	                }else if(data==2){
	                    $("#sltCentroCE").attr('disabled',true);
	                }
	            }
	        });
	    });
	    //Consultar y cargar proyecto por indicador de cuenta auxproyecto
	    function consultarProyecto(cuenta){
            var form_data={
                is_ajax:1,
                cuenta:cuenta
            };
            $.ajax({
                type: 'POST',
                url: "consultarComprobanteIngreso/proyecto.php",
                data:form_data,
                success: function (data) {
                    $("#sltProyectoE").html(data).fadeIn();
                    $("#sltProyectoE").css('display','none');
                }
            });
        }
	    var padre = 0;
        $("#sltCuentaE").change(function(){
            if ($("#sltCuentaE").val()=="" || $("#sltCuentaE").val()==0) {
                padre = 0;         
                $("#sltProyectoE").prop('disabled',true);
            }else{
                padre = $("#sltCuentaE").val();
            }
            var form_data = {
                is_ajax:1,
                data:+padre
            };
            $.ajax({
                type:"POST",
                url:"consultasDetalleComprobante/consultaProyecto.php",
                data:form_data,                                         
                success: function (data) {
                    if (data==1) {
                        $("#sltProyectoE").prop('disabled',false);
                        consultarProyecto($("#sltCuentaE").val());
                    }else if(data==2){
                        $("#sltProyectoE").prop('disabled',true);
                    }
                }
            });
        });	
        //Función para registrar detalle
        function registrarDetalle(){
        	//Captura de valores
        	var fecha = $("#txtFecha").val();
        	var cuenta = $("#sltCuentaE").val();
        	var tercero = $("#sltTerceroE").val();
        	var centroC = $("#sltCentroCE").val();
        	var proyecto = $("#sltProyectoE").val();
        	var valorD = $("#txtValorE").val();
        	var descripcion = $("#txtDescripcion").val();
        	var id = $("#txtId").val();
        	var action = 'registrar';
            var txtValorCE = $("#txtValorCE").val();
        	//Array con los valores
        	var form_data = {
        		cuenta:cuenta,
        		tercero:tercero,
        		centrocosto:centroC,
        		proyecto:proyecto,
        		valorD:valorD,
        		action:action,
        		descripcion:descripcion,
        		id:id,
        		fecha:fecha,
                valorC:txtValorCE
        	};
        	var result = '';
        	//Envio ajax
        	$.ajax({
        		type:'POST',
        		url:'json/registrarGFDetalleEgresoTesoriaJson.php',
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
        //Eliminar Detalle 
        function eliminarDetalle(id){
        	//Captura de valores
        	var id = id;
        	var action = 'eliminar';
        	//Array de datos
        	var form_data = {
        		id:id,
        		action:action
        	};
        	var result = '';
        	//Envio ajax
        	$("#myModal").modal('show');
        	$("#ver").click(function(){
	        	$.ajax({
                        type:'POST',
                        url:'json/registrarGFDetalleEgresoTesoriaJson.php',
                        data:form_data,
                        success: function(data){
                                result = JSON.parse(data);
                                if(result == true){
                                        $("#myModal1").modal('show');
                                }else{
                                        $("#myModal2").modal('show');
                                }
                        }
	        	});
        	});
        }
        //Guardar Banco
        function guardarBanco(){
        	if($("#sltBanco").val()==""){
        		$("#mdlBanco").modal('show');
        	}else{
        		//Array con los valores
	        	var form_data = {
	                banco:$("#sltBanco").val(),
	                fecha:$("#txtFecha").val(),
	                descripcion:$("#txtDescripcion").val(),
	                valor:<?php echo $dif; ?>,
	                valorEjecucion:'0',
	                comprobante:$("#txtId").val(),
	                tercero:$("#sltTerceroE").val(),
	                proyecto:$("#sltProyectoE").val(),
	                centro:$("#sltCentroCE").val(),
                    existente:37
	            };
	            //Envio ajax
	            var result = '';
	            $.ajax({
	                type: 'POST',
	                url: "consultasBasicas/consultarNumeros.php",
	                data:form_data,
	                success: function (data) {
	                    result = JSON.parse(data);
	                    if(result==true){
	                        $("#modalGuardado").modal('show');
	                    }else{
	                        $("#modalNoGuardo").modal('show');
	                    }
	                }
	            });
        	}
        }
        //Guardar cambios de detalle
        function guardarDetalle(id,nat){
        	//Captura de valores
        	var action = 'modificar';
        	var Debito = $("#txtDebito"+id).val();
        	var Credito = $("#txtCredito"+id).val();
                var tercero = $("#sltTerceroM"+id).val();
        	var id = id;
        	var nat = nat;        	
        	//Array de envio de valores
        	var form_data = {
        		action:action,
        		txtDebito:Debito,
        		txtCredito:Credito,
                        tercero:tercero,
        		id:id,
        		naturaleza:nat
        	};
        	var result = '';
        	//Envio ajax
        	$.ajax({
        		type:'POST',
        		url:'json/registrarGFDetalleEgresoTesoriaJson.php',
        		data:form_data,
        		success: function(data){
        			result = JSON.parse(data);
        			if(result == true){
        				$("#modalModificar").modal('show');
        			}else{
        				$("#modalNoMod").modal('show');
        			}
        		}
        	});
        }
        //Función imprimir informe
        function informe(){
            window.open('informes/inf_com_egreso_tesoreria_cheque.php?idcom=<?php echo md5($Egreso)  ?>');
        }
        //Función mostrar campos ocultos
        function mostrarCampos(id){
        	if($("#idPrevio").val() !== 0 || $("#idPrevio").val() !== ""){
        		//Labels
        		var lblDebitoE = 'lblDebito'+$("#idPrevio").val();
        		var lblCreditoE = 'lblCredito'+$("#idPrevio").val();
                        var lblTercero = 'lblTercero'+$("#idPrevio").val();
        		//Mostrar labels
        		$("#"+lblDebitoE).css('display','block');
        		$("#"+lblCreditoE).css('display','block');
                        $("#"+lblTercero).css('display','block');
        		//input text
        		var txtDebitoE = 'txtDebito'+$("#idPrevio").val();
        		var txtCreditoE = 'txtCredito'+$("#idPrevio").val();
                        var divTercero  = 'divTercero'+$("#idPrevio").val();
        		//Se ocultan los input
        		$("#"+txtDebitoE).css('display','none');
        		$("#"+txtCreditoE).css('display','none');
                        $("#"+divTercero).css('display','none');
        		//Campos de tabla
        		var guardarE = 'guardar'+$("#idPrevio").val();
                var cancelarE = 'cancelar'+$("#idPrevio").val();
                var tablaE = 'tab'+$("#idPrevio").val();
                //Ocultamos los campos
                $("#"+guardarE).css('display','none');
                $("#"+cancelarE).css('display','none');
                $("#"+tablaE).css('display','none');
        	}
        	// labels
        	var lblDebito = 'lblDebito'+id;
        	var lblCredito = 'lblCredito'+id;
                var lblTercero = 'lblTercero'+id;
        	//Ocultar labels
        	$("#"+lblDebito).css('display','none');
        	$("#"+lblCredito).css('display','none');
                $("#"+lblTercero).css('display','none');
        	//input text
        	var txtDebito = 'txtDebito'+id;
        	var txtCredito = 'txtCredito'+id;
                var divTercero = 'divTercero'+id;
        	//Mostrar los inputs
        	$("#"+txtDebito).css('display','block');
        	$("#"+txtCredito).css('display','block');
                $("#"+divTercero).css('display','block');
        	//Campo de tabla
        	var guardar = 'guardar'+id;
            var cancelar = 'cancelar'+id;
            var tabla = 'tab'+id;
            //Mostrar los campos de tabla
            $("#"+guardar).css('display','block');
            $("#"+cancelar).css('display','block');
            $("#"+tabla).css('display','block');
        	//Validación para cargar inputs
        	$("#idActual").val(id);
            if($("#idPrevio").val() != id){
                $("#idPrevio").val(id);   
            }
        }
        //Función cancelar
        function cancelar(id){
        	// labels
        	var lblDebito = 'lblDebito'+id;
        	var lblCredito = 'lblCredito'+id;
                var lblTercero = 'lblTercero'+id;
        	//Mostrar labels
        	$("#"+lblDebito).css('display','block');
        	$("#"+lblCredito).css('display','block');
                $("#"+lblTercero).css('display','block');
        	//input text
        	var txtDebito = 'txtDebito'+id;
        	var txtCredito = 'txtCredito'+id;
                var divTercero = 'divTercero'+id;
        	//Ocultar los inputs
        	$("#"+txtDebito).css('display','none');
        	$("#"+txtCredito).css('display','none');
                $("#"+divTercero).css('display','none');
        	//input text
        	var txtDebito = 'txtDebito'+id;
        	var txtCredito = 'txtCredito'+id;
                var divTercero = 'divTercero'+id;
        	//Ocultar los inputs
        	$("#"+txtDebito).css('display','none');
        	$("#"+txtCredito).css('display','none');
                $("#"+divTercero).css('display','none');
        	//Campo de tabla
        	var guardar = 'guardar'+id;
            var cancelar = 'cancelar'+id;
            var tabla = 'tab'+id;
            //Mostrar los campos de tabla
            $("#"+guardar).css('display','none');
            $("#"+cancelar).css('display','none');
            $("#"+tabla).css('display','none');
        }
        //Función para validar ingreso de solo números
        function justNumbers(e){   
	    	var keynum = window.event ? window.event.keyCode : e.which;
	        if ((keynum == 8) || (keynum == 46) || (keynum == 45))
	        return true;
	        return /\d/.test(String.fromCharCode(keynum));
	    }
	    //Función de autocompletado para buscar
	    function autoCompletar(){
	    	//Tipo comprobante capturado
	    	var tipoC = $("#sltTipoCom").val();
	    	//Validamos que no sea vacio
	    	if(tipoC == "" || tipoC == 0){
	    		$("#mdltipocomprobante").modal('show');
	    	}else{
	    		$("#txtNumeroCom").autocomplete({
	    			source: "consultasComprobanteContable/consultaAutocompletado.php?tipo="+tipoC,
                    minLength:5
	    		});
	    	}
	    }
	    //Función de consulta y cargado del formulario
	    function consultarE(){	    	
    		//Array de envio 
    		var form_data = {
    			existente:32,	    			
    			comprobante:$("#sltBuscar").val()
    		};
    		//Petición ajax
    		$.ajax({
    			type:'POST',
    			url: 'consultasBasicas/consultarNumeros.php',
                data: form_data,
                success: function(data){
                	//Direccionamos la pagina a la url devuelta
                	window.location = data;
                }
    		});	    	
	    }
	    //Función para consultar cuentas por pagar
	    $("#sltTercero").change(function(){
			//Captura de variables
			var tercero = $("#sltTercero").val();
			//Array de cargar 
			var form_data = {
				existente:33,
				tercero:tercero
			};
			//Petición ajax
			$.ajax({
				type:'POST',
				url: 'consultasBasicas/consultarNumeros.php',
				data: form_data,
				success: function(data){
                                    //console.log(data);
					//Validamos que el data devuelto no sea nulo
					if(data!==" "){
						//Cargamos el html del combo
						$("#sltCuentasPagar").html(data).fadeIn();
						//ocultamos combo sin la libreria select2
						$("#sltCuentasPagar").css('display','none');
					}else{
						//Removemos las opciones cuando el data es nulo
						$("#sltCuentasPagar option").remove();
						//Agregamos un option para indicar que ese tercero no tiene cuentas por pagar
						$("#sltCuentasPagar").append('<option value=" ">No hay cuentas por pagar</option>');
					}
				}
			});
	    });
        //Función para agregar cuentas bancarias
        function agregarCuentaP(){
            //Validación en la seleccion del combo cuentas por pagar
            if(!isNaN($("#sltCuentasPagar").val())){
                //Captura de valores
                var sltCuentasPagar = $("#sltCuentasPagar").val();
                var fecha = $("#txtFecha").val();
                var descr = $("#txtDescripcion").val();            
                var id = $("#txtId").val();
                //Array de envio
                var form_data = {
                    existente:35,
                    sltCuentasPagar:sltCuentasPagar,
                    fecha:fecha,
                    descr:descr,
                    id:id
                };
                //Envio ajax
                var result = '';
                $.ajax({
                    type:'POST',
                    url: 'consultasBasicas/consultarNumeros.php',
                    data: form_data,
                    success : function(data){
                        //Captura de valor devuelto
                        result = JSON.parse(data);
                        //Validamos dependiendo del valor retornado
                        if(result == true){
                            $("#modalCuentaPR").modal('show');
                        }else{
                            $("#modalCuentaPNR").modal('show');
                        }
                    }
                });
            }
        }        
    </script>
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
    <!-- Modales de eliminado -->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Detalle Egreso?</p>
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
    <!-- Modal Carga de Datos -->
    <div class="modal fade" id="mdlBanco" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No hay un banco seleccionado</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdltipocomprobante" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Seleccione un tipo de comprobante.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="tbmtipoF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalCuentaPR" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Cuenta por pagar registrada.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnCuentaR" onclick="return recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalCuentaPNR" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha podido registrar cuenta por pagar.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnCuentaRN" onclick="return recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
<?php 
if(!empty($_GET['egreso']))
{ 
    $cierre = cierrecnt($Egreso);
    if($cierre ==1){  ?>
    <script>
    $("#txtFecha").prop("disabled", true) ;
    $("#sltTipoCom").prop("disabled", true) ;
    $("#txtNumeroCom").prop("disabled", true) ;
    $("#sltTercero").prop("disabled", true) ;
    $("#sltCuentasPagar").prop("disabled", true) ;
    $("#txtDescripcion").prop("disabled", true) ;
    $("#sltBanco").prop("disabled", true) ;
    $("#sltTipoCon").prop("disabled", true) ;
    $("#txtNumeroC").prop("disabled", true) ;
    $("#btnGuardar").prop("disabled", true) ;
    $("#btnModificar").prop("disabled", true) ;
    $("#btnAgregarC").prop("disabled", true) ;
    $("#sltCuentaE").prop("disabled", true) ;
    $("#sltTerceroE").prop("disabled", true) ;
    $("#sltCentroCE").prop("disabled", true) ;
    
    $("#txtValorE").prop("disabled", true) ;
    $("#txtValorCE").prop("disabled", true) ;
    $("#btnGuardarDE").attr("disabled", true) ;
    $("#btnGuardarPET").attr("disabled", true) ;
    
    
    
    </script>
<?php } else { ?>
    <?php if(!empty($Egreso)){ ?>
    <script>
    $("#btnGuardar").attr('disabled',true);
    $("#btnImprimir").attr('disabled',false);
    $("#btnModificar").attr('disabled',false);
    $("#btnAgregarC").attr('disabled',false);
    </script>
    <?php }else{ ?>
    <script>
            $("#btnGuardar").attr('disabled',false);
            $("#btnImprimir").attr('disabled',true);
            $("#btnModificar").attr('disabled',true);
    $("#btnAgregarC").attr('disabled',true);
    //Remover evento click
    $("#btnImprimir").removeAttr('onclick');
    $("#btnModificar").removeAttr('onclick');
    $("#btnAgregarC").removeAttr('onclick');
    </script>
    <?php } ?>
<?php } } else {  ?>  
 <script>
            $("#btnGuardar").attr('disabled',false);
            $("#btnImprimir").attr('disabled',true);
            $("#btnModificar").attr('disabled',true);
    $("#btnAgregarC").attr('disabled',true);
    //Remover evento click
    $("#btnImprimir").removeAttr('onclick');
    $("#btnModificar").removeAttr('onclick');
    $("#btnAgregarC").removeAttr('onclick');
    </script>   
<?php } ?>    
    <!-- Modales de guardado -->
<div class="modal fade" id="mdlGuardado" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
        </h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Información guardada correctamente.
        </p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnGuardado" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar
        </button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlNoGuardado" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
        </h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No se ha podido guardar la información.
        </p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnGuardado2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar
        </button>
      </div>
    </div>
  </div>
</div> 
<div class="modal fade" id="infoM" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
          </h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información modificada correctamente.
          </p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar
          </button>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="noModifico" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
          <div id="forma-modal" class="modal-header">          
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
            </h4>
          </div>
          <div class="modal-body" style="margin-top: 8px">
            <p>No se ha podido modificar la información.
            </p>
          </div>
          <div id="forma-modal" class="modal-footer">
            <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar
            </button>
          </div>
        </div>
    </div>
</div>
<div class="modal fade" id="sinsaldo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
          </h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>La cuenta no tiene saldo. </p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnsinsaldo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
            Aceptar
          </button>
        </div>
      </div>
    </div>
</div>
<script>
    $("#btnGuardado2").click(function(){
        document.location.reload();
    });
    $("#btnGuardado").click(function(){
        document.location.reload();
    })
    $("#btnModifico").click(function(){
        document.location.reload();
    });
    $("#btnNoModifico").click(function(){
        document.location.reload();
    }) 
</script>
<script>
    $("#sltBanco").change(function(){
        var cuenta = $("#sltBanco").val();
        var form_data={ estruc:8, cuenta:cuenta};
        $.ajax({
           type:"POST",
           url :"jsonPptal/consultas.php",
           data:form_data,
           success:function(response){
               response = parseInt(response);
               if(response<=0){
                   $("#sinsaldo").modal("show");
               }
           }
        }); 
    });
</script>
   <?php require_once './registrar_GF_DETALLE_EGRESO.php'; ?>
 </body>
 </html>