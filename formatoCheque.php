<?php 	
	#####################################################################################################
	# Creación
	# 09-02-2017 | 05:02 | Jhon Numpaque
	#
	#####################################################################################################
	# Modificaciones
	#
	#####################################################################################################	
	# Fecha : 		09/03/2017
	# Modifico : 	Jhon Numpaque
	# Descripción : Se cambio validación de valor (+,-)
	#####################################################################################################	
	# Fecha : 10-02-2017
	# Modifico : Jhon Numpaque
	# Descripción : Modificación de consulta de impresión de valores
	#
	session_start();	

	require ('Conexion/conexion.php');
	require_once('numeros_a_letras.php');


	$idP = $_GET['idDP'];
	

	
	############################## Consulta de ruta para leer archivo ##################################
	if(!empty($_GET['cuenta'])){
		$cuenta = $_GET['cuenta'];
		$sqlCta = "SELECT 	fot.rutaFormatoCheque 
				FROM 		gf_cuenta_bancaria ctab 
				LEFT JOIN 	gf_formato fot ON fot.id_unico = ctab.formato
				WHERE 		ctab.id_unico = $cuenta";
		$resultCta = $mysqli->query($sqlCta);
		$valorF = mysqli_fetch_row($resultCta);
	}else{		
		$tipoF = $_GET['tipo'];
		$sqlFormato = "SELECT fot.rutaFormatoCheque 
		FROM gf_formato fot
		LEFT JOIN gf_tipo_documento tpd ON tpd.formato = fot.id_unico
		LEFT JOIN gf_cuenta_bancaria ctab ON fot.id_unico = ctab.formato
		WHERE tpd.id_unico=$tipoF";
		$resultF = $mysqli->query($sqlFormato);
		$valorF = mysqli_fetch_row($resultF);	
	}
	####################################################################################################
	###################################### Consulta de cargue de archivos ##############################
	$sqlC = "SELECT
	  cnt.fecha,
	  IF(
	    CONCAT(
	      IF(ter.nombreuno = '',
	      '',
	      ter.nombreuno),
	      ' ',
	      IF(
	        ter.nombredos IS NULL,
	        '',
	        ter.nombredos
	      ),
	      ' ',
	      IF(
	        ter.apellidouno IS NULL,
	        '',
	        IF(
	          ter.apellidouno IS NULL,
	          '',
	          ter.apellidouno
	        )
	      ),
	      ' ',
	      IF(
	        ter.apellidodos IS NULL,
	        '',
	        ter.apellidodos
	      )
	    ) = '' OR CONCAT(
	      IF(ter.nombreuno = '',
	      '',
	      ter.nombreuno),
	      ' ',
	      IF(
	        ter.nombredos IS NULL,
	        '',
	        ter.nombredos
	      ),
	      ' ',
	      IF(
	        ter.apellidouno IS NULL,
	        '',
	        IF(
	          ter.apellidouno IS NULL,
	          '',
	          ter.apellidouno
	        )
	      ),
	      ' ',
	      IF(
	        ter.apellidodos IS NULL,
	        '',
	        ter.apellidodos
	      )
	    ) IS NULL,
	    (ter.razonsocial),
	    CONCAT(
	      IF(ter.nombreuno = '',
	      '',
	      ter.nombreuno),
	      ' ',
	      IF(
	        ter.nombredos IS NULL,
	        '',
	        ter.nombredos
	      ),
	      ' ',
	      IF(
	        ter.apellidouno IS NULL,
	        '',
	        IF(
	          ter.apellidouno IS NULL,
	          '',
	          ter.apellidouno
	        )
	      ),
	      ' ',
	      IF(
	        ter.apellidodos IS NULL,
	        '',
	        ter.apellidodos
	      )
	    )
	  ) AS 'NOMBRE',
	  detalleC.valor
	FROM
	  gf_detalle_comprobante detalleC
	LEFT JOIN
	  gf_comprobante_cnt cnt ON cnt.id_unico = detalleC.comprobante
	LEFT JOIN
	  gf_tercero ter ON ter.id_unico = cnt.tercero
	WHERE MD5
	  (detalleC.id_unico) = '$idP'
	";
	$resultC = $mysqli->query($sqlC);
	$valor = mysqli_fetch_row($resultC);
	
	$fecha = $valor[0];
	$fecha = explode('-',$fecha);
	$diaV = $fecha[2];		#Dia
	$mesV = $fecha[1];		#Mes
	$annV = $fecha[0];		#Anio

	$terceroV = $valor[1];
	$valorNM = $valor[2];
	if($valorNM<0){
		$valorNM = $valorNM*-1;
	}else{
		$valorNM = $valorNM;
	}
	$valorNL = numtoletras($valorNM);
	###########################################################################################
	#Variables de x,y
	#Dia
	$x1 = "";
	$y1 = "";
	#Mes
	$x2 = "";
	$y2 = "";
	#Anno
	$x3 = "";
	$y3 = "";
	#Valor Numeros
	$x4 = "";
	$y4 = "";
	#Tercero
	$x5 = "";
	$y5 = "";
	#Valor letras
	$x6 = "";
	$y6 = "";
	##########
	#Valores para resta
	$h = 160; #Resta $Y altura
	$w = 274; #Resta ancho $x
	if(!empty($valorF[0])){
	#Se divide por \n
	$div = explode("\n",$valorF[0]);
	#######################################################################################
	foreach ($div as $key => $value) {
		#Buscamos la palabra dia linea por linea la cual usamos como needle o aguja
		$dia = stripos($value,'Dia');
		if($dia!==false){	    				    			
			#Linea Encontrada
			$valoresD = $value;
			#Separamos por , y creamos un array
			$valorD=explode(',',$valoresD);
			#Desplegamos el array
			foreach ($valorD as $key => $value) {
				#x
				$altD = stripos($value,'top');
				if($altD !== false){
					#Dividimos usando :
					$variable = explode(':',$value);
					foreach ($variable as $key => $value) {
						$y1 = $variable[2]-$h;
					}
				}
				#y
				$rigD = stripos($value,'left');
				if($rigD!==false){
					#Dividimos usando :
					$valor = explode(':',$value);
					foreach ($valor as $key => $value) {
						$x1 = $valor[1]-$w;	    						
					}
				}
			}
		}
		$mes = stripos($value,'Mes');
		if($mes!==false){
			#Linea Encontrada
			$valoresM = $value;
			#Separamos por , y creamos un array
			$valorM=explode(',',$valoresM);
			#Desplegamos el array
			foreach ($valorM as $key => $value) {
				#x
				$altD = stripos($value,'top');
				if($altD !== false){
					#Dividimos usando :
					$variable = explode(':',$value);
					foreach ($variable as $key => $value) {
						$y2 = $variable[2]-($h+30);
					}
				}
				#y
				$rigD = stripos($value,'left');
				if($rigD!==false){
					#Dividimos usando :
					$valor = explode(':',$value);
					foreach ($valor as $key => $value) {
						$x2 = $valor[1]-$w;	    						
					}
				}
			}	
		}
		$Anno = stripos($value,'Ano ');
		if($Anno!==false){
			#Linea Encontrada
			$valoresA = $value;
			#Separamos por , y creamos un array
			$valorA=explode(',',$valoresA);
			#Desplegamos el array
			foreach ($valorA as $key => $value) {
				#y
				$altD = stripos($value,'top');
				if($altD !== false){
					#Dividimos usando :
					$variable = explode(':',$value);
					foreach ($variable as $key => $value) {
						$y3 = $variable[2]-($h+60);
					}
				}
				#x
				$rigD = stripos($value,'left');
				if($rigD!==false){
					#Dividimos usando :
					$valor = explode(':',$value);
					foreach ($valor as $key => $value) {
						$x3 = $valor[1]-$w;	    						
					}
				}
			}
		}
		$ValN = stripos($value,'ValorNumero');				
		if($ValN!==false){
			#Linea Encontrada
			$valoresN = $value;
			#Separamos por , y creamos un array
			$valorN=explode(',',$valoresN);
			#Desplegamos el array
			foreach ($valorN as $key => $value) {
				#y
				$altD = stripos($value,'top');
				if($altD !== false){
					#Dividimos usando :
					$variable = explode(':',$value);
					foreach ($variable as $key => $value) {
						$y4 = $variable[2]-($h+90);
					}
				}
				#x
				$rigD = stripos($value,'left');
    			if($rigD!==false){
    				#Dividimos usando :
    				$valor = explode(':',$value);
    				foreach ($valor as $key => $value) {
    					$x4 = $valor[1]-$w;	    						
    				}
    			}
			}
		}
		$Tercero = stripos($value,'Tercero');
		if($Tercero!==false){
			#Linea Encontrada
			$valoresT = $value;
			#Separamos por , y creamos un array
			$valorT=explode(',',$valoresT);
			#Desplegamos el array
			foreach ($valorT as $key => $value) {
				#ydd
				$altD = stripos($value,'top');
				if($altD !== false){
					#Dividimos usando :
					$variable = explode(':',$value);
					foreach ($variable as $key => $value) {
						$y5 = $variable[2]-($h+120);
					}
				}
				#x
				$rigD = stripos($value,'left');
    			if($rigD!==false){
    				#Dividimos usando :
    				$valor = explode(':',$value);
    				foreach ($valor as $key => $value) {
    					$x5 = $valor[1]-$w;	    						
    				}
    			}
			}
		}
		$ValL = stripos($value,'ValorLetras');
		if($ValL!==false){
			#Linea Encontrada
			$valoresL = $value;
			#Separamos por , y creamos un array
			$valorL=explode(',',$valoresL);
			#Desplegamos el array
			foreach ($valorL as $key => $value) {
				#ydd
				$altD = stripos($value,'top');
				if($altD !== false){
					#Dividimos usando :
					$variable = explode(':',$value);
					foreach ($variable as $key => $value) {
						$y6 = $variable[2]-($h+150);
					}
				}
				#x
				$rigD = stripos($value,'left');
    			if($rigD!==false){
    				#Dividimos usando :
    				$valor = explode(':',$value);
    				foreach ($valor as $key => $value) {
    					$x6 = $valor[1]-$w;	    						
    				}
    			}
			}
		}
	}													    	
	######################################################################################
}
###########################################################################################
 ?>
<html>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta class="viewport" content="width=device-width, initial-scale=1.0, minimun-scalable=1.0"></meta>
	<link rel="icon" href="img/AAA.ico" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="screen" title="default" />
	<link rel="stylesheet" href="css/normalize.css"/>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery-ui.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery-1.10.2.js"></script>
	<link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
	<script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
	<script src="js/dataTables.jqueryui.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
	<title>Formato Cheque</title>
</html>
<body onload="imprimir()">
	<div class="container-fluid text-left">
		<div class="row content">			
			<div style="background-color:transparent;position: absolute;width: 824px;height: 368px;" id="cheque">
				<div class="draggable" style="width:50px;height: 30px;top:<?php echo trim($y1).'px'?>;left:<?php echo trim($x1).'px'; ?>;bottom: auto;right: auto;position: relative;background-color: transparent;">
					<?php echo $diaV; ?>
				</div>
				<div class="draggable" style="width:50px;height: 30px;top:<?php echo trim($y2).'px'?>;left:<?php echo trim($x2).'px'; ?>;bottom: auto;right: auto;position: relative;background-color: transparent;">
					<?php echo $mesV; ?>
				</div>
				<div class="draggable" style="width:50px;height: 30px;top:<?php echo trim($y3).'px'?>;left:<?php echo trim($x3).'px'; ?>;bottom: auto;right: auto;position: relative;">
					<?php echo $annV; ?>
				</div>
				<div class="draggable" style="width:200px;height: 30px;top:<?php echo trim($y4).'px'?>;left:<?php echo trim($x4).'px'; ?>;bottom: auto;right: auto;position:relative;background-color: transparent;">
					<?php echo '$'.number_format($valorNM,2,',','.'); ?>
				</div>
				<div class="draggable" style="width:500px;height: 30px;top:<?php echo trim($y5).'px'?>;left:<?php echo trim($x5).'px'; ?>;bottom: auto;right: auto;position:relative;background-color: transparent;">
					<?php echo '<input style="border:none;width:500px;" value="'.$terceroV.'" readonly>'; ?>
				</div>
				<div class="draggable" style="width:500px;height: 30px;top:<?php echo trim($y6).'px'?>;left:<?php echo trim($x6).'px'; ?>;bottom: auto;right: auto;position:relative;background-color: transparent;">
					<?php echo '<textarera style="border:none;width:500px;" readonly>'.$valorNL.'</textarea>'; ?>
				</div>
			</div>
		</div>
	</div>		
	<script>
		function imprimir(){
			$("#cheque").printArea();
		}		
	</script>
	<script src="js/jquery-1.10.2.js"></script>
	<script src="js/jquery-ui.js"></script>				
	<script src="js/PrintArea.js"></script>
</body>
