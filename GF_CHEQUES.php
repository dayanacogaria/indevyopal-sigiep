<?php 
	#####################################################################################################
	# Creación
	# 09-02-2017 | 05:00 | Jhon Numpaque
	#####################################################################################################
	# Modificaciones
	
	
	#Llamado a la librerias de cabeza y conexión
	require_once ('head.php');
	require ('Conexion/conexion.php');
	require_once('numeros_a_letras.php');
?>
	<title>Generador de Cheques</title>
	<style>
		/*Diseño de campos cuando están activos*/
		input:hover{
			border-style: solid black 1px; 				
		}
		/*Diseño de campos de tipo texto*/
		input{
			background-color:transparent;
			cursor: pointer;
		}
		/*Diseño de campos de textarea*/
		textarea{
			background-color:transparent;	
		}
		/*Diseño de división draggable*/
		.draggable{
			background-color:transparent;	
		}
	</style>
	<link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>	
<body style="font-size: 15px">
	<div class="container-fluid text-left">
		<div class="row content">
			<?php 
			#menu
			require_once ('menu.php');
			$dia = '';			#Dia
			$mes = '';			#Mes
			$anno = '';			#Año
			$tercero = '';		#Tercero a favor/beneficiario
			$numeroC = '';		#Número de cuenta
			$valorN = '';		#Valor en números
			$valorL = '';		#valor en letras
			###########################################################################################
			$idFormatoan = $_GET['idC'];
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
			################################## Consulta ###############################################
			$sql = "SELECT id_unico,rutaFormatoCheque FROM gf_formato WHERE md5(id_unico)='$idFormatoan'";
			$result = $mysqli->query($sql);
			$val = mysqli_fetch_row($result);
			###########################################################################################
			# Obtención de valores de pixeles por medio de un array
			$idFormato = $val[0];
			if(!empty($val[1])){
				#Se divide por \n
				$div = explode("\n",$val[1]);
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
			<div class="col-sm-10 text-left" style="margin-top:-22px">
				<h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Generador de Cheques</h2>
				<canvas class="print" id="canvas" style="border:1px solid black;width: 824px;height: 368px;float:left; display:block;border-radius:5px;margin-left:35px;box-shadow:2px 2px 1px gray;background-color:transparent;position: relative;">					
				</canvas>
				<div id="model" style="background-color:transparent;position: absolute;width: 824px;height: 368px;margin-left:35px;">						
					<div class="draggable" style="width:50px;top:<?php echo trim($y1).'px'?>;left:<?php echo trim($x1).'px'; ?>;bottom: auto;right: auto" id="posDia">
						<input type="text" name="txtDia" title="Dia" placeholder="Dia" id="txtDia" value="@Dia" style="width:50px;cursor:pointer;height: 30px;border-color: blue;" readonly="">
					</div>
					<div class="draggable" style="width:50px;top:<?php echo trim($y2).'px'?>;left:<?php echo trim($x2).'px'; ?>;bottom: auto;right: auto" id="posMes">
						<input type="text" name="txtMes" id="txtMes"  title="Mes" placeholder="Mes" value="@Mes" style="width:50px;cursor:pointer;height: 30px;border-color: blue;" readonly="">
					</div>
					<div class="draggable" style="width:50px;top:<?php echo trim($y3).'px'?>;left:<?php echo trim($x3).'px'; ?>;bottom: auto;right: auto" id="posAnio">
						<input type="text" name="txtAnno" id="txtAnno"  title="Año" placeholder="Año" value="@Anio" style="width:50px;cursor:pointer;height: 30px;border-color: blue;" readonly="">
					</div>
					<div class="draggable" style="width: 200px;top:<?php echo trim($y4).'px'?>;left:<?php echo trim($x4).'px'; ?>" id="posValorNumeros">
						<input type="text" name="txtValorN" id="txtValorN"  value="@ValorNumeros" title="Valor a pagar en números" placeholder="Valor a pagar en números" style="border-color: blue;cursor:pointer;width: 70px;width:200px;height: 30px" readonly="">
					</div>
					<div class="draggable" style="width: 500px;top:<?php echo trim($y5).'px'?>;left:<?php echo trim($x5).'px'; ?>" id="posTercero">
						<input type="text" name="txtTercero" id="txtTercero" value="@Tercero" readonly=""  title="Tercero" placeholder="Tercero" style="cursor:pointer;height: 30px;width: 500px;border-color: blue;" >
					</div>					
					
					<div class="draggable" style="width: 500px;top:<?php echo trim($y6).'px'?>;left:<?php echo trim($x6).'px'; ?>" id="posNumeroLetras">
						<input  style="height: 30px;width: 500px;border-color: blue" type="text" name="txtValorT" id="txtValorT" title="Valor a pagar en letras" placeholder="Valor a pagar en letras" readonly="" value="@ValorLetras">
					</div>	
				</div>
				<div class="form-group form-inline">
					<a class="btn btn-primary" id="btnImprimirT" style="margin-left:40px" title="Vista previa"><span class="glyphicon glyphicon-zoom-in"></span></a>
					<a class="btn btn-primary" style="margin-left:40px" id="btnGuardarT" title="Guardar"><span class="glyphicon glyphicon-floppy-disk"></span></a>
				</div>
				<div class="form-group" style="display: none">
					<textarea name="txtPixelex" id="txtPixelex" style="display: none"></textarea>
				</div>
				<script src="js/bootstrap.min.js"></script>
				<script type="text/javascript">		
					//Función para hacer desplazable un campo por medio de su clase
					$(".draggable").draggable({
					    start: function (event, ui) {
					        $(this).data('preventBehaviour', true);					        
					    }
					});
					$(".draggable").find(":input").on('mousedown', function (e) {
					    var mdown = new MouseEvent("mousedown", {
					        screenX: e.screenX,
					        screenY: e.screenY,
					        clientX: e.clientX,
					        clientY: e.clientY,
					        view: window
					    });
					    $(this).closest('.draggable')[0].dispatchEvent(mdown);
					}).on('click', function (e) {
					    var $draggable = $(this).closest('.draggable');
					    if ($draggable.data("preventBehaviour")) {
					        e.preventDefault();
					        $draggable.data("preventBehaviour", false)
					    }					    
					});									
					//Función para imprimir
					$("#btnImprimirT").bind("click",function(){
						$('#model').printArea();								
					});
					//Creación de canvas y cuadricula
					var canvas = document.getElementById("canvas");
					var ctx = canvas.getContext("2d");
					for (var x=0; x<=300; x=x+10){
					  ctx.moveTo(x,0);
					  ctx.lineTo(x,300);
					}
					for (var y=0; y<=300; y=y+10){
					  ctx.moveTo(0,y);
					  ctx.lineTo(300,y);
					}
					ctx.strokeStyle = "gray";
					ctx.stroke();										
					//Función para obtener los datos
					function obtenerDatos() {
						posD =$("#txtDia").offset();
						posM =$("#txtMes").offset();
						posA =$("#txtAnno").offset();
						posT =$("#txtTercero").offset();						
						posN =$("#txtValorN").offset();
						posL =$("#txtValorT").offset();				
					  return {					  	
					    dia: "Dia -> top:"+posD.top+" ,left: "+posD.left+" \n",
					    mes: "Mes -> top:"+posM.top+" ,left: "+posM.left+" \n",
					    ano: "Ano -> top:"+posA.top+" ,left: "+posA.left+" \n",
					    tercero: "Tercero -> top:"+posT.top+" ,left: "+posT.left+" \n",
					    ValorN: "ValorNumero -> top:"+posN.top+" ,left: "+posN.left+" \n",
					    ValorL: "ValorLetras -> top:"+posL.top+" ,left: "+posL.left+" \n",
					  };
					};
					$('#btnGuardarT').click(function(){
						var datos = obtenerDatos();
						console.log(obtenerDatos());
						$("#txtPixelex").val(JSON.stringify(datos));
					});					
					//Guardar
					$("#btnGuardarT").click(function(){
						var form_data= {							
							idFormato:<?php echo $idFormato ?>,
							txtPixelex:$("#txtPixelex").val()
						};
						var result = ' ';
						$.ajax({
							type:'POST',
							url:"json/GuardarFormatoChequeJson.php",
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
					
					$("#ver1").click(function(){
						window.history.go(-1);
					});					
				</script>
				<script src="js/jquery-1.10.2.js"></script>			
				<script src="js/PrintArea.js"></script>
			</div>
		</div>
	</div>
</body>	
<div>
	<?php 
		#pie de pagina
		require_once ('footer.php');
	 ?>
</div>
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      	<div class="modal-content">
        	<div id="forma-modal" class="modal-header">          
          		<h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        	</div>
        	<div class="modal-body" style="margin-top: 8px">
          		<p>Informaci&oacute;n guardada correctamente.</p>
    		</div>
        	<div id="forma-modal" class="modal-footer">
          		<button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        	</div>
      </div>
    </div>
</div>
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
        <p>No se ha podido guardar la informaci&oacute;n.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
</html>