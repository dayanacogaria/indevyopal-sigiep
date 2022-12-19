<?php
/*****************************************************************************************************************************************************************
* Modificaciones
 ******************************************************************************************************************************************************************
 * 19/07/2017  | Erica González. |Arreglo Ejecución de Ingresos
******************************************************************************************************************************************************************
* Fecha de Modificación	:	04/05/2017
* Modificado por 		:	Alexander Numpaque
* Descripción mod.		:	Se cambiaron los procesos de acumulados y ejecución de ingresos (start_process_execution_I), de gastos (start_process_execution_G) y 
*							de gastos e ingresos (start_process_execution_G_I) los cuales deben quedar similares a los procesos que llenan las tablas temporales
*							en los informes de ejecución de gastos y/o/u ejecución de ingresos. También se hizo similar la función de presupuestos 
*							y disponibilides a las que se encuentran en el archivo de consultas.php en la carpeta de informes/informes_pptal/
*****************************************************************************************************************************************************************/

/*
 * @$sql type="String"
 * Función basica para consultar combos en los que su primer valor a imprimir sea id_unico
 * y el el segundo valor sea el nombre o la descripción
 */
function cargar_combos($sql){
    require './Conexion/conexion.php';
    $result=$mysqli->query($sql);
    while ($row= mysqli_fetch_row($result)){
        echo '<option value="'.$row[0].'">'.ucwords(mb_strtolower($row[1])).'</option>';
    }
}
/*
 * @$sql String
 * Función basica de consulta la cual retornada un solo valor dependiendo de la consulta el cual
 * servira para cargar inputs de texto
 */
function obtener_valor($sql){
    require './Conexion/conexion.php';
    $result=$mysqli->query($sql);
    $row=$result->fetch_row();
    return $row[0];
}
/*
* @id_cnt 	{type=int}
* Función para generar comprobante presupuestal y causaciones
*/
function crear_pptal_retencion($id_cnt){
	$concepto_rt  = 0;
	$h = 0;
	$con_tr = array();
	$valor_tr = array();
	$_SESSION['pptal_retencion'] = "";
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//Llamamos a la función de conexion
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	require './Conexion/conexion.php';
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//Consultamos que el comprobante tenga retenciones
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$sqlR = "SELECT rt.id_unico,rt.tiporetencion,rt.valorretencion,tr.concepto_ingreso_hom
			FROM gf_retencion rt
			LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = rt.tiporetencion
			WHERE rt.comprobante =  $id_cnt AND tr.concepto_ingreso_hom IS NOT NULL;";
	$resultRt = $mysqli->query($sqlR);
	$conteo_r = mysqli_num_rows($resultRt);
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//Validamos que tenga retenciones
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($conteo_r > 0){
		while ($rowR = mysqli_fetch_row($resultRt)){
			++$h;
			$valor_tr[] = $rowR[2];		#Valor de la retencion
			$con_tr[] = $rowR[3];		#Caonceptos relacionados
		}
 		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Validamos que $h sea mayor que 0
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if($h > 0){
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//Consultamos el comprobante homologado del comprobante enviado
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$sqlH = "SELECT 	tpc.tipo_comp_hom
					FROM 		gf_tipo_comprobante tpc
					LEFT JOIN 	gf_comprobante_cnt cnt
					ON 			cnt.tipocomprobante = tpc.id_unico
					WHERE 		cnt.id_unico = $id_cnt;";
			$resultH = $mysqli->query($sqlH);
			$conteo_hom = mysqli_num_rows($resultH);
			if($conteo_hom > 0) {
				$row_hom = mysqli_fetch_row($resultH);
				//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//Validamos que la consulta tenga algun valor
				//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				if(!empty($row_hom[0])){
					//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					//Consultamos que el tipo de comprobante tenga comprobante presupuestal relacionado
					//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					$tipo_com_hom = $row_hom[0];
					$sqlTP = "SELECT 	tpc.comprobante_pptal,
										tpc.tipo_comp_hom
							FROM 		gf_tipo_comprobante tpc
							WHERE 		tpc.id_unico = $tipo_com_hom";
					$resultP = $mysqli->query($sqlTP);
					$conteo_tp = mysqli_num_rows($resultP);
					//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					//Validamos que la consulta retorne algun valor
					//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					if($conteo_tp > 0){
						$row_tP = mysqli_fetch_row($resultP);
						$tipo_cp = $row_tP[0];						#Tipo de comprobante presupuestal
						$tipo_comp_hom_1 = $row_tP[1];				#Tipo de comprobante homologado
						//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						//Consultamos los datos del comprobante cnt, para crear el comprobante pptal
						//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						$sqlCnt = "SELECT 	cnt.numero,
											cnt.fecha,
											cnt.descripcion,
											cnt.numerocontrato,
											cnt.clasecontrato,
											cnt.parametrizacionanno,
											cnt.tercero,
											cnt.numero
								FROM 		gf_comprobante_cnt cnt
								WHERE 		cnt.id_unico = $id_cnt";
						$resultCnt = $mysqli->query($sqlCnt);
						$rowCnt = mysqli_fetch_row($resultCnt);
						//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						//Insertamos el comprobante pptal
						//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						if(empty($rowCnt[4])){
							$clasecontrato = 'NULL';
						}else{
							$clasecontrato = $rowCnt[4];
						}
						$sqlPptal = "INSERT INTO gf_comprobante_pptal(numero, fecha, fechavencimiento, descripcion, numerocontrato, parametrizacionanno, clasecontrato, tipocomprobante, tercero, estado, responsable) VALUES ('$rowCnt[0]', '$rowCnt[1]', '$rowCnt[1]', '$rowCnt[2]','$rowCnt[3]', $rowCnt[5],$clasecontrato,$tipo_cp,$rowCnt[6], 1, $rowCnt[6])";
						$resultPptal = $mysqli->query($sqlPptal);
						if($resultPptal == true) {
							//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
							//Consultamos el ultimo id_de comprobante pptal ingresado
							//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
							$sqlMaxIdPptal = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE tipocomprobante = $tipo_cp";
							$resultMaxIdPptal = $mysqli->query($sqlMaxIdPptal);
							$rowMaxIdPptal = mysqli_fetch_row($resultMaxIdPptal);
							$id_pptal = $rowMaxIdPptal[0];							#Ultimo id registrado de comprobante pptal
							//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
							//Desplagamos los array de conteo
							//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
							for($a = 0; $a < count($con_tr); $a++){
								if(!empty($con_tr[$a])){
									$concepto_rt = $con_tr[$a];
									$valorretencion = $valor_tr[$a];
									//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
									//Consultamos concepto rubro, y rubro fuente
									//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
									$sqlC_r = "SELECT 		crb.id_unico AS 'concepto_rubro',
															rbf.id_unico AS 'rubro_fuente'
												FROM 		gf_concepto_rubro crb
												LEFT JOIN 	gf_rubro_fuente rbf ON crb.rubro = rbf.rubro
												WHERE 		crb.concepto = $concepto_rt";
									$resultC_r = $mysqli->query($sqlC_r);
									$conteoC_R = mysqli_num_rows($resultC_r);
									//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
									//Validamos que la consulta retorne valores
									//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
									if($conteoC_R > 0){
										$rowC_R = mysqli_fetch_row($resultC_r);
										$conceptorubro = $rowC_R[0];						#Concepto rubro relacionado al concepto
										$rubrofuente = $rowC_R[1];							#Rubro fuente relacionado al concepto
										//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
										//Insertamos el detalle del comprobante pptal
										//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
										$sqlD_p = "INSERT INTO gf_detalle_comprobante_pptal (descripcion,valor,comprobantepptal,rubrofuente,conceptorubro,tercero, proyecto) VALUES('$rowCnt[2]',$valorretencion,$id_pptal,$rubrofuente,$conceptorubro,$rowCnt[6],'2147483647')";
										$resultD_p = $mysqli->query($sqlD_p);
									}
								}
							}
							if(!empty($tipo_comp_hom_1)) {
								causacion_retenciones($id_cnt,$tipo_comp_hom_1,$id_pptal);
							}
						}
					}
				}
			}
		}
	}
}
/*
* @id_cnt 		{type=int}
* @$id_pptal 	{type=int}
* @tipo_cnt 	{type=int}
* Función de registro de comprobante de causacion de retenciones
*/
function causacion_retenciones($id_cnt,$tipo_cnt,$id_com_ptal) {
	$_SESSION['pptal_causacion'] = "";
    $_SESSION['cnt_causacion'] = "";
	$h = 0;
	$x = array();
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//Llamamos a la función de conexion
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	require './Conexion/conexion.php';
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//Validamos que id de comprobante cnt no este vacio
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(!empty($id_cnt)) {
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Consultamos los detalles y obtenemos la cuentas y validamos si están registradas en concepto_rubro_cuenta como debito
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$sqlD = "SELECT DISTINCT dtc.id_unico, dtc.cuenta FROM gf_detalle_comprobante dtc WHERE dtc.comprobante = $id_cnt";
        $resultD = $mysqli->query($sqlD);
        while ($rowD = mysqli_fetch_row($resultD)) {
	        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//Consultamos las cuentas en los detalles, esten en concepto_rubro_cuenta como cuenta debito
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		    $sqlCR = "  SELECT DISTINCT crb.cuenta_credito
                    FROM            gf_concepto_rubro_cuenta crb
                    LEFT JOIN 		gf_concepto_rubro cr ON crb.concepto_rubro = cr.id_unico
                    LEFT JOIN 		gf_concepto cn ON cn.id_unico = cr.concepto
                    WHERE           crb.cuenta_debito = $rowD[1] AND cn.clase_concepto = 1";
          	$resultCR = $mysqli->query($sqlCR);
          	$rowCR = mysqli_fetch_row($resultCR);
          	$filasCR = mysqli_num_rows($resultCR);
          	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//Validamos que los ids de cuenta crediot y debito no sean iguales
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          	if($rowD[1] !== $rowCR[0]){
	        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			 	//Validamos que la consulta retorne valores mayores que 0
				//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				if($filasCR !== 0) {
	          	++$h;                           //Preincrementamos el contador
	          $x[]=$rowD[0];                  //Capturamos el id del detalle
	        }
        }
    }
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//Validamos que h sea mayor que 0
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($h > 0){
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Consultamos los datos del comprobante
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$sqlC = "SELECT cnt.fecha,
                  cnt.descripcion,
                  cnt.numerocontrato,
                  cnt.parametrizacionanno,
                  cnt.clasecontrato,
                  cnt.tercero,
                  cnt.tipocomprobante,
                  cnt.numero,
                  cnt.estado,
                  cnt.compania,
                  cnt.numero
          FROM    gf_comprobante_cnt cnt
          WHERE   cnt.id_unico = $id_cnt";
      	$resultC = $mysqli->query($sqlC);
      	$com = mysqli_fetch_row($resultC);
      	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Consultamos si el tipo de comprobante homologado tiene un comprobante pptal
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$sqlTipoP = "SELECT comprobante_pptal FROM gf_tipo_comprobante WHERE id_unico = $tipo_cnt";
      	$resultTipoP = $mysqli->query($sqlTipoP);
      	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Validamos que el tipo de comprobante presupuestal del comprobante homologado retorne algun valor
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$cantidadpp = mysqli_num_rows($resultTipoP);
		if($cantidadpp > 0){
			$numeroC = $com[10];
			$tipo_pptal = mysqli_fetch_row($resultTipoP);
        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        	//Realizamos insert a comprobante pptal con el tipo de comprobante de reconocimiento
        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        	if(empty($com[2])){
        		$numerocontrato = 'NULL';
        	}else{
        		$numerocontrato = $com[2];
        	}
        	if(empty($com[4])){
        		$clasecontrato = 'NULL';
        	}else{
        		$clasecontrato = $com[4];
        	}
        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        	//Insertamos el comprobante pptal
        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        	$sqlPptal = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, descripcion, numerocontrato, parametrizacionanno, claseContrato, tipocomprobante, tercero, estado, responsable) VALUES ('$numeroC', '$com[0]', '$com[0]', '$com[1]', $numerocontrato, $com[3], $clasecontrato, $tipo_pptal[0], $com[5], 1, $com[5])";
        	$resultPptal = $mysqli->query($sqlPptal);
        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        	//Consultamos el ultimo comprobante pptal insertado
        	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        	$sqlXT = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE tipocomprobante = $tipo_pptal[0]";
	      	$resultXT = $mysqli->query($sqlXT);
	      	$id_pptal = mysqli_fetch_row($resultXT);
        	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        	//Insertamos el comprobante cnt
        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        	$sqlCnt = "INSERT INTO gf_comprobante_cnt (fecha ,tipocomprobante ,numero ,tercero ,descripcion ,estado ,clasecontrato ,numerocontrato ,compania ,parametrizacionanno) VALUES ('$com[0]',$tipo_cnt,'$com[10]',$com[5],'$com[1]',1,$clasecontrato, $numerocontrato,$com[9],$com[3])";
	      	$resultCnt = $mysqli->query($sqlCnt);
		    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	      	//Consultamos el ultimo comprobante registrado por el tipo
	      	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	      	$sqlRCnt = "SELECT MAX(id_unico) FROM gf_comprobante_cnt WHERE tipocomprobante = $tipo_cnt ORDER BY id_unico ASC LIMIT 1";
	      	$resultRCnt = $mysqli->query($sqlRCnt);
	      	$id_com = mysqli_fetch_row($resultRCnt);
        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        	//Consultamos los valores del detalle del comprobante pptal enviado
        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        	$valorD = 0;
        	$sqlDetalle_pptal = "SELECT descripcion,
            							valor,
            							rubrofuente,
            							conceptorubro,
            							tercero,
            							proyecto
    					    FROM 	gf_detalle_comprobante_pptal
    					    WHERE 	comprobantepptal = $id_com_ptal";
	      	$resultDetalle_pptal = $mysqli->query($sqlDetalle_pptal);
	      	$cuentaDetalle = mysqli_num_rows($resultDetalle_pptal);
        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        	//Validamos que la consulta retorne valores
        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	      	if($cuentaDetalle > 0){
	      		while ($rowD_ptal = mysqli_fetch_row($resultDetalle_pptal)){
			      	$valorD = $rowD_ptal[1];
			      	$concepto_rt = $rowD_ptal[3];
		     		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			      	//Insertamos el detalle de comprobante pptal
			      	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			      	$sqlInsertDP = "INSERT INTO gf_detalle_comprobante_pptal (descripcion, valor, rubrofuente, conceptorubro, tercero, proyecto, comprobantepptal) VALUES ('$rowD_ptal[0]', $rowD_ptal[1], $rowD_ptal[2], $rowD_ptal[3], $rowD_ptal[4], $rowD_ptal[5], $id_pptal[0])";
			      	$resultInsertDP = $mysqli->query($sqlInsertDP);
        	  		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            		//Consultamos el ultimo detalle de comprobate presupuestal
            		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			      	$sqlULDetalle_pptal = "SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_pptal[0]";
			      	$resultULDetalle_pptal = $mysqli->query($sqlULDetalle_pptal);
			      	$detalle_pptal = mysqli_fetch_row($resultULDetalle_pptal);
            		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            		//Consultamos las cuentas relacionadas al concepto
            		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			      	$sqlC_RC = "SELECT    crbc.cuenta_debito, crbc.cuenta_credito
			                  FROM 		  gf_concepto_rubro_cuenta crbc
			                  LEFT JOIN	gf_concepto_rubro crb ON crb.id_unico = crbc.concepto_rubro
			                  WHERE 		crbc.concepto_rubro = $rowD_ptal[3]";
			      	$resultC_RC = $mysqli->query($sqlC_RC);
			      	$rowC_RC = mysqli_fetch_row($resultC_RC);
            		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            		//Desplegamos el array, y consultamos el detalle, y nuevamente obtenemos el id de la cuenta
            		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		        	$sqlDt = "SELECT    dtc.fecha,
		                                dtc.descripcion,
		                                dtc.valor,
		                                dtc.cuenta,
		                                dtc.tercero,
		                                dtc.proyecto,
		                                dtc.centrocosto
                    FROM        gf_detalle_comprobante dtc
                    WHERE       dtc.comprobante = $id_cnt";
            		$resultDt = $mysqli->query($sqlDt);
            		$rowDt = $resultDt->fetch_row();
            		$valor = abs($valorD);                    #Obtenemos el valor absoluto del valor en el detalle
            		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            		//Realizamos insertado de datos con cuenta debitto
            		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		        	$sqlDD = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, valor, cuenta, naturaleza, tercero, proyecto, centrocosto, comprobante, detallecomprobantepptal) VALUES ('$rowDt[0]', '$rowDt[1]', $valor, $rowC_RC[0], 1, $rowDt[4], $rowDt[5], $rowDt[6], $id_com[0],$detalle_pptal[0]);";
		        	$resultDD = $mysqli->query($sqlDD);
		        	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		        	//Realizamos insertado de datos a cuenta credito
		        	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		        	$sqlDC = "INSERT INTO gf_detalle_comprobante  (fecha, descripcion, valor, cuenta, naturaleza, tercero, proyecto, centrocosto, comprobante, detallecomprobantepptal)  VALUES ('$rowDt[0]', '$rowDt[1]', $valor, $rowC_RC[1], 2, $rowDt[4], $rowDt[5], $rowDt[6], $id_com[0], $detalle_pptal[0]);";
		        	$resultDC = $mysqli->query($sqlDC);
	          	}
	        }
        	$_SESSION['cnt_causacion'] = $id_com[0];
        	$_SESSION['pptal_causacion'] = $id_pptal[0];
			}
		}
	}
}
/*
*@comC {type=int}
*Funcion para generar comprobantes de causacion de ingresos (pptal y cnt)
*/
/*
*@comC {type=int}
*Funcion para generar comprobantes de causacion de ingresos (pptal y cnt)
*/
function causacion_ingresos($comC){
	@session_start();	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	//Declaramos un contador, para obtener la cantidad de cuentas en cuenta credito, y un array x para obtener los detalles que son cuentas debito
  	// y que tienen cuenta credito en concepto rubro cuenta
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	$h = 0;                     										//Contador
  	$x = array();               										//Array de captura de los de detalles
  	$param = $_SESSION['anno'];											//parametro de año
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//Llamamos a la función de conexion
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	require './Conexion/conexion.php';
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	//Consultamos que tenga un tipo de comprobante el cual este homologado
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 	$sql = "SELECT tp.tipo_comp_hom, cnt.numero FROM gf_comprobante_cnt cnt LEFT JOIN gf_tipo_comprobante tp ON cnt.tipocomprobante = tp.id_unico WHERE cnt.id_unico = $comC";
  	$result = $mysqli->query($sql);
  	$tipo_hom = $result->fetch_row();
  	$sqlExiste = "SELECT COUNT(id_unico) FROM gf_comprobante_cnt WHERE tipocomprobante = $tipo_hom[0] AND numero = '$numero'";
  	$resultExiste = $mysqli->query($sqlExiste);
  	$rowE = mysqli_fetch_row($resultExiste);
  	if($rowE[0] == 0) {
  		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  	//Validamos que el tipo de comprobante homologado retornado no este vacio
	  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  	if(!empty($tipo_hom[0])){
	    	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    	//Consultamos los detalles y obtenemos la cuentas y validamos si están registradas en concepto_rubro_cuenta como debito
	    	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    	$sqlD = "SELECT dtc.id_unico, dtc.cuenta FROM gf_detalle_comprobante dtc WHERE dtc.comprobante = $comC";
	    	$resultD = $mysqli->query($sqlD);
	    	while ($rowD = $resultD->fetch_row()) {
	    		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    		//Consultamos las cuentas en los detalles, esten en concepto_rubro_cuenta como cuenta debito relaciondas a los detalles del comprobante
	    		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	      		$sqlCR = "  SELECT DISTINCT crbc.cuenta_credito
		                  	FROM            gf_concepto_rubro_cuenta crbc
							LEFT JOIN 		gf_concepto_rubro crb	ON crbc.concepto_rubro 	= crb.id_unico
							LEFT JOIN 		gf_concepto cn 		 	ON crb.concepto 		= cn.id_unico
		                  	WHERE           cn.clase_concepto = 1 	AND cuenta_debito = $rowD[1];";
				$resultCR = $mysqli->query($sqlCR);
				$rowCR = $resultCR->fetch_row();
				$filasCR = $resultCR->num_rows;
	      		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	      		//Validamos que la consulta retorne valores mayores que 0
	      		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	      		if($filasCR !== 0){
	        		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	        		//Validamos que las ids de cuenta_credito y cuenta_debito no sean similares
	        		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	        		if($rowD[1] !== $rowCR[0]) {
	          			++$h;                           								//Preincrementamos el contador
	          			$x[]=$rowD[0];                  								//Capturamos el id del detalle
	        		}
	      		}
	    	}
	    	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    	//Validamos que h sea mayor que 0
	    	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    	if($h > 0){
	    		$id_pptal = '';
	     		$idDetallePp1 = '';
	     		$id_cnt = '';
	    		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	      		//Consultamos los datos del comprobante
	      		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	      		$sqlC = "SELECT cnt.fecha, cnt.tipocomprobante, cnt.numero, cnt.tercero, cnt.descripcion, cnt.estado, cnt.clasecontrato, cnt.numerocontrato, cnt.compania, cnt.parametrizacionanno
	              		FROM    gf_comprobante_cnt cnt
	              		WHERE   cnt.id_unico = $comC";
	      		$resultC = $mysqli->query($sqlC);
	      		$com = $resultC->fetch_row();      	      		
	            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	            //Consultamos si el tipo de comprobante homologado tiene un comprobante pptal
	            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	            $sqlTipoP = "SELECT comprobante_pptal FROM gf_tipo_comprobante WHERE id_unico = $tipo_hom[0]";
	            $resultTipoP = $mysqli->query($sqlTipoP);
	            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	            //Validamos que el tipo de comprobante presupuestal del comprobante homologado retorne algun valor
	            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	            $cantidadpp = mysqli_num_rows($resultTipoP);
	            $tipo_pptal = $resultTipoP->fetch_row();
	            if(!empty($tipo_pptal[0])){
	                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                //Realizamos insert a comprobante pptal con el tipo de comprobante de reconocimiento
	                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                $sqlPptal = "INSERT INTO gf_comprobante_pptal   (numero, fecha, fechavencimiento, descripcion, numerocontrato, parametrizacionanno, claseContrato, tipocomprobante, tercero, estado, responsable) VALUES ('$com[2]', '$com[0]', '$com[0]', '$com[4]', '$com[7]', $param, NULLIF('$com[6]',0), $tipo_pptal[0], $com[3], 1, $com[3])";
	                $resultPptal = $mysqli->query($sqlPptal);
	                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                //Consultamos el ultimo comprobante pptal insertado
	                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                $sqlXT = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE tipocomprobante = $tipo_pptal[0]";
	                $resultXT = $mysqli->query($sqlXT);
	                $row_idP = mysqli_fetch_row($resultXT);
	                $id_pptal = $row_idP[0];
	            }else{
	                $id_pptal = 'NULL';
	                $idDetallePp1 = 'NULL';
	            }
	            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	            //Realizamos insert a comprobante cnt con el tipo de comprobante de reconocimiento
	            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	            $sqlCnt = "INSERT INTO gf_comprobante_cnt  (fecha , tipocomprobante , numero , tercero , descripcion , estado , clasecontrato , numerocontrato , compania , parametrizacionanno) VALUES ('$com[0]', '$tipo_hom[0]' , '$com[2]' , '$com[3]' , '$com[4]' , '$com[5]' , NULLIF('$com[6]',0) , NULLIF('$com[7]',0) , '$com[8]' , '$com[9]');";
	            $resultCnt = $mysqli->query($sqlCnt);
	            if($resultCnt == true) {
	            	$inserted = true;
	                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                //Consultamos el ultimo comprobante registrado por el tipo
	                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                $sqlRCnt = "SELECT MAX(id_unico) FROM gf_comprobante_cnt WHERE tipocomprobante = $tipo_hom[0] AND numero = $com[2]";
	                $resultRCnt = $mysqli->query($sqlRCnt);
	                $id_com = $resultRCnt->fetch_row();
	                $id_cnt = $id_com[0];
	                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                //Desplegamos el array, y consultamos el detalle, y nuevamente obtenemos el id de la cuenta, la cual consultamos nuevamente a conceto rubro 
	                //cuenta yrelizamos el ingreso de datos al detalle
	                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                for ($a = 0;$a < count($x);$a++) {
	                    $id_d = $x[$a];
	                    $sqlDt = "SELECT dtc.fecha, dtc.descripcion, dtc.valor, dtc.cuenta, dtc.tercero, dtc.proyecto, dtc.centrocosto, dtc.detallecomprobantepptal
	                      FROM        gf_detalle_comprobante dtc
	                      WHERE       dtc.id_unico = $id_d";
	                    $resultDt = $mysqli->query($sqlDt);
	                    $rowDt = $resultDt->fetch_row();
	                    $valor = abs($rowDt[2]);                    			//Obtenemos el valor absoluto del valor en el detalle
	                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                    //Validamos que haya un comprobante pptal
	                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                    if(!empty($id_pptal)){
	                        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                        //validamos y consultamos los valores del detalle del comprobante pptal
	                        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                        if(!empty($rowDt[7]) && $id_pptal !== 'NULL'){
	                            $sqlDePtall = " SELECT  descripcion, rubroFuente, conceptoRubro, tercero, proyecto
	                                    FROM    gf_detalle_comprobante_pptal
	                                    WHERE   id_unico = $rowDt[7]";
	                            $resultDePtall = $mysqli->query($sqlDePtall);
	                            $rowDePtall = mysqli_fetch_row($resultDePtall);
	                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                            //Insertamos los valores
	                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                            $sqlIND = "INSERT INTO  gf_detalle_comprobante_pptal(descripcion, valor, rubroFuente, conceptoRubro, tercero, proyecto, comprobantepptal) VALUES  ('$rowDePtall[0]', $valor, $rowDePtall[1], $rowDePtall[2], $rowDePtall[3], $rowDePtall[4], $id_pptal)";
	                            $resultIND = $mysqli->query($sqlIND);
	                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                            //Consultamos el ultimo id detalle insertado
	                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                            $sqlULPPP = "SELECT MAX(id_unico) FROM  gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_pptal";
	                            $resultPPP = $mysqli->query($sqlULPPP);
	                            $rowDetallePp1 = mysqli_fetch_row($resultPPP);
	                            $idDetallePp1 = $rowDetallePp1[0];
	                        }
	                    }
	                    if(!empty($id_com[0])) {
	                        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                        //Consultamos el id de la cuenta credito
	                        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                        $sqlCR = "  SELECT  cuenta_credito FROM gf_concepto_rubro_cuenta WHERE cuenta_debito = $rowDt[3]";
	                        $resultCR = $mysqli->query($sqlCR);
	                        $rowCR = $resultCR->fetch_row();
	                        if(!empty($rowCR[0])) {
	                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                            //Realizamos insertado de datos con cuenta debito
	                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                            $sqlDD = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, valor, cuenta, naturaleza, tercero, proyecto, centrocosto, comprobante, detalleafectado, detallecomprobantepptal) VALUES ('$rowDt[0]', '$rowDt[1]', $valor, $rowDt[3], 1, $rowDt[4], $rowDt[5], $rowDt[6], $id_com[0], $id_d, $idDetallePp1);";
	                            $resultDD = $mysqli->query($sqlDD);
	                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                            //Realizamos insertado de datos a cuenta credito
	                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                            $sqlDC = "INSERT INTO gf_detalle_comprobante    (fecha, descripcion, valor, cuenta, naturaleza, tercero, proyecto, centrocosto, comprobante, detalleafectado, detallecomprobantepptal) VALUES ('$rowDt[0]', '$rowDt[1]', $valor, $rowCR[0], 2, $rowDt[4], $rowDt[5], $rowDt[6], $id_com[0], $id_d, $idDetallePp1)";
	                            $resultDC = $mysqli->query($sqlDC);                                
	                        }
	                    }
	                }
	            }      		
	      	}
	    }    
  	}  	
}

/*
* Función para calcular los presupuesto de rubro fuente por medio de loos detallese
* Desarrollado por Ferney Perez 
* @$id_rubF type{int} id de rubro fuente
* @$tipoO   type{int} id de tipo de operacion
* @$fechaI  type{int} valor de la fecha Inicial
* @fechaF   type{int} valor de la fecha final
* return    @$presu type{int} valor de presupuesto de rubro fuente
*/
function presupuestos($id_rubF, $tipoO, $fechaI, $fechaF) {
    require ('../Conexion/conexion.php');
	$presu = 0;
	$query = "SELECT valor as value 
			FROM gf_detalle_comprobante_pptal dc
			LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico
            LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
            WHERE dc.rubrofuente = '$id_rubF' 
            AND tcp.tipooperacion = '$tipoO' 
            AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND (tcp.clasepptal = '13')";
	$ap = $mysqli->query($query);
    if(mysqli_num_rows($ap)>0){
        $sum=0;
    	while ($sum1= mysqli_fetch_array($ap)) {
            $sum = $sum1['value']+$sum;
        }
    } else {
       $sum=0; 
    }
    $presu=$sum;       
    return $presu;
}
/*
* Función para calcular las disponibilidades de la cuenta
* Desarrollado por Ferney Perez
* @$id_rubF type{int} id de rubro fuente
* @$tipoO   type{int} id de tipo de operacion
* @$fechaI  type{int} valor de la fecha Inicial
* @fechaF   type{int} valor de la fecha final
* return    @$apropiacion_def type{int} valor de las disponibilidades del rubro fuente
*/
function disponibilidades($id_rubFue, $clase, $fechaI, $fechaF) {
  	require'../Conexion/conexion.php';
	$apropiacion_def = 0;
  	$queryApro = "SELECT  detComP.valor, tipComP.tipooperacion,  tipComP.nombre, rubFue.id_unico, rubFue.rubro, rubP.id_unico, rubP.nombre
                FROM gf_detalle_comprobante_pptal detComP
                LEFT JOIN gf_comprobante_pptal comP         ON comP.id_unico    = detComP.comprobantepptal
                LEFT JOIN gf_tipo_comprobante_pptal tipComP ON tipComP.id_unico = comP.tipocomprobante
                LEFT JOIN gf_rubro_fuente rubFue            ON rubFue.id_unico  = detComP.rubrofuente
                LEFT JOIN gf_rubro_pptal rubP               ON rubP.id_unico    = rubFue.rubro
                WHERE tipComP.clasepptal = '$clase'
                AND   rubFue.id_unico =  $id_rubFue
                AND       comP.fecha BETWEEN '$fechaI' AND '$fechaF'";
	$apropia = $mysqli->query($queryApro);
	while($row = mysqli_fetch_row($apropia)) {
		if(($row[1] == 2) || ($row[1] == 4) || ($row[1] == 1)){
			$apropiacion_def += $row[0];
		} elseif($row[1] == 3) {
			$apropiacion_def -= $row[0];
		}
	}
	return $apropiacion_def;
}
/*
* @$fechaInicial  type{date}  Fecha inicial
* @$fechaFinal    type{date}  Fecha final
* Función para ejecutar el proceso de ejecución de ingresos
* Desarrollado por Erica Gonzalez , Convertida en función por Alexander Numpaque
*/
function start_process_execution_I($fechaInicial, $fechaFinal,$i=0){
  	require ('../Conexion/conexion.php');
  	$vaciarTabla = 'TRUNCATE temporal_consulta_pptal_gastos';              // Limpiamos los valores registramos en la tabla temporarl
  	$mysqli->query($vaciarTabla);
        @session_start();
        $parmanno = $_SESSION['anno'];
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Obtenemos todas las cuentas
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	$ctas = "SELECT DISTINCT rpp.nombre, rpp.codi_presupuesto, f.id_unico, rpp2.codi_presupuesto, rf.id_unico 
	        FROM gf_rubro_pptal rpp
	        LEFT JOIN gf_rubro_fuente rf ON rf.rubro = rpp.id_unico
	        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico
	        LEFT JOIN gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
	        WHERE rpp.tipoclase = 6 AND rpp.parametrizacionanno = $parmanno 
	        ORDER BY rpp.codi_presupuesto ASC";
  	$ctass= $mysqli->query($ctas);
  	while ($row1 = mysqli_fetch_row($ctass)) {  // Guardamos los valores en la tabla temporarl
    	$insert= "INSERT INTO temporal_consulta_pptal_gastos "
            . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
            . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
    	$mysqli->query($insert);
  	}
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Obtenemos los detelles relacionados a las cuentas
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	$select ="SELECT DISTINCT rpp.nombre, rpp.codi_presupuesto, f.id_unico,  rpp2.codi_presupuesto, dcp.rubrofuente 
          FROM gf_detalle_comprobante_pptal dcp
          LEFT JOIN	gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
          LEFT JOIN	gf_rubro_pptal rpp ON rf.rubro = rpp.id_unico
          LEFT JOIN	gf_fuente f ON rf.fuente = f.id_unico
          LEFT JOIN	gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
          LEFT JOIN     gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
          WHERE rpp.tipoclase = 6 AND cp.parametrizacionanno = $parmanno  ";
	
  	$select1 = $mysqli->query($select);
  	while($row = mysqli_fetch_row($select1)) {
    	$pptoInicial=presupuestos($row[4], 1, $fechaInicial, $fechaFinal);       //Obtenemos el valor de presupuesto incial
    	$adicion=presupuestos($row[4], 2, $fechaInicial, $fechaFinal);       //Obtenemos el valor de las adiciones
        $reduccion=presupuestos($row[4], 3, $fechaInicial, $fechaFinal);       //Obtenemos el valor de las reducciones
        $presupuestoDefinitivo=$pptoInicial+$adicion-$reduccion;
        $recaudos=disponibilidades($row[4], 18, $fechaInicial, $fechaFinal);  //Obtenemos el valor de los rescaudos
        $saldos=$presupuestoDefinitivo-$recaudos;                                     //Obtenemos el valor de saldos por recaudar
    	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	// Actulizamos los datos hallados la tabla
    	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	$update="UPDATE temporal_consulta_pptal_gastos SET "
            . "ptto_inicial ='$pptoInicial', "
            . "adicion = '$adicion', "
            . "reduccion = '$reduccion', "
            . "presupuesto_dfvo = '$presupuestoDefinitivo', "
            . "recaudos = '$recaudos', "
            . "saldos_x_recaudar = '$saldos' "
            . "WHERE rubro_fuente = '$row[4]'";
    	$update = $mysqli->query($update);
  	}
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Consultamos la tabla temporal para hacer acumulado
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$acum = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, reduccion, "
        . "presupuesto_dfvo, recaudos, "
        . "saldos_x_recaudar "
        . "FROM temporal_consulta_pptal_gastos "
        . "ORDER BY cod_rubro DESC ";
	$acum = $mysqli->query($acum);
	while ($rowa1= mysqli_fetch_row($acum)){
	    $acumd = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, reduccion, "
        . "presupuesto_dfvo, recaudos, "
        . "saldos_x_recaudar "
        . "FROM temporal_consulta_pptal_gastos WHERE id_unico ='$rowa1[0]' "
        . "ORDER BY cod_rubro DESC ";
	    $acumd = $mysqli->query($acumd);
	    while ($rowa= mysqli_fetch_row($acumd)) {
	      	if(!empty($rowa[2])){
		        $va11= "SELECT id_unico, "
	            . "cod_rubro,"
	            . "cod_predecesor, "
	            . "ptto_inicial, adicion, reduccion, "
	            . "presupuesto_dfvo, recaudos, "
	            . "saldos_x_recaudar "
	            . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
		        $va1 = $mysqli->query($va11);
		        $va= mysqli_fetch_row($va1);
		        $pptoInicialM = $rowa[3]+$va[3];
		        $adicionM = $rowa[4]+$va[4];
		        $reduccionM = $rowa[5]+$va[5];
		        $presupuestoDefinitivoM = $rowa[6]+$va[6];
		        $recaudosM = $rowa[7]+$va[7];
		        $saldosM = $rowa[8]+$va[8];
	        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	        	// Actulizamos la tabla con los valores encontrados
	        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	        	$updateA="UPDATE temporal_consulta_pptal_gastos SET "
	                . "ptto_inicial ='$pptoInicialM', "
	                . "adicion = '$adicionM', "
	                . "reduccion = '$reduccionM', "
	                . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
	                . "recaudos = '$recaudosM', "
	                . "saldos_x_recaudar = '$saldosM' "
	                . "WHERE cod_rubro = '$rowa[2]'";
	        	$updateA = $mysqli->query($updateA);
	        	if($updateA == true){
	        		$i++;
	        	}
	      	}
	    }
	}
  	return $i;
}
/*
* @$fechaInicial  type{date}  Fecha inicial
* @$fechaFinal    type{date}  Fecha final
* Función para ejecutar el proceso de ejecución de gastos
* Desarrollado por Erica Gonzalez, Convertida en función por Alexander Numpaque
*/
function start_process_execution_G($fechaInicial, $fechaFinal,$i=0){
	require ('../Conexion/conexion.php');
	$vaciarTabla = 'TRUNCATE temporal_consulta_pptal_gastos';
	$mysqli->query($vaciarTabla);	
        @session_start();
        $parmanno = $_SESSION['anno'];
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Consultamos las cuentas por codigo de cuenta incial y final
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$ctas = "SELECT DISTINCT rpp.nombre, rpp.codi_presupuesto, f.id_unico, rpp2.codi_presupuesto, rf.id_unico 
        FROM gf_rubro_pptal rpp
        LEFT JOIN gf_rubro_fuente rf ON rf.rubro = rpp.id_unico
        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico
        LEFT JOIN gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
		WHERE rpp.parametrizacionanno = $parmanno AND 
                (rpp.tipoclase = 7
		OR rpp.tipoclase = 9
		OR rpp.tipoclase = 10 
                OR rpp.tipoclase = 15 
                OR rpp.tipoclase = 16)
		ORDER BY rpp.codi_presupuesto ASC";
	$ctass= $mysqli->query($ctas);
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Guardamos los valores en la tabla temporal
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	while ($row1 = mysqli_fetch_row($ctass)) {
      	$insert= "INSERT INTO temporal_consulta_pptal_gastos "
              . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
              . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
      	$mysqli->query($insert);
  	}
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	//Consultamos los detalles relacionados a las cuentas
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$select ="SELECT DISTINCT rpp.nombre, rpp.codi_presupuesto, f.id_unico, rpp2.codi_presupuesto, dcp.rubrofuente 
              FROM gf_detalle_comprobante_pptal dcp
              LEFT JOIN	gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
              LEFT JOIN	gf_rubro_pptal rpp ON rf.rubro = rpp.id_unico
              LEFT JOIN	gf_fuente f ON rf.fuente = f.id_unico
              LEFT JOIN	gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
              LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
              WHERE  cp.parametrizacionanno = $parmanno 
                  AND (rpp.tipoclase = 7
                      OR rpp.tipoclase = 9
                      OR rpp.tipoclase = 10 
                      OR rpp.tipoclase = 15 
                      OR rpp.tipoclase = 16 )
              ORDER BY rpp.codi_presupuesto ASC";
	
	$select1 = $mysqli->query($select);
  	while($row = mysqli_fetch_row($select1)){
	    $pptoInicial= presupuestos($row[4], 1, $fechaInicial, $fechaFinal);	    
	    $adicion = presupuestos($row[4], 2, $fechaInicial, $fechaFinal);	   
	    $reduccion = presupuestos($row[4], 3, $fechaInicial, $fechaFinal);	   
	    $tras = presupuestos($row[4], 4, $fechaInicial, $fechaFinal);
            
            $trasCredito = 0;
            $trasCont    = 0;
            $query = "SELECT valor as value 
                        FROM
                          gf_detalle_comprobante_pptal dc
                        LEFT JOIN
                          gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico
                        LEFT JOIN
                          gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                        WHERE
                          dc.rubrofuente = '$row[4]' 
                          AND tcp.tipooperacion = '4' 
                          AND cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                          AND (tcp.clasepptal = '13')";
            $ap = $GLOBALS['mysqli']->query($query);
            if(mysqli_num_rows($ap)>0){
                while ($sum1= mysqli_fetch_array($ap)) {
                    $tras = $sum1['value'];
                    if($tras>0){
                        $trasCredito += $tras;
                    }else {
                        $trasCont    += $tras;
                    }
                }
            }
	    $presupuestoDefinitivo=$pptoInicial+$adicion-$reduccion+$trasCredito+$trasCont;	    
	    $disponibilidad=disponibilidades($row[4], 14, $fechaInicial, $fechaFinal);	    
	    $saldoDisponible=$presupuestoDefinitivo-$disponibilidad;	    
	    $registros=disponibilidades($row[4], 15, $fechaInicial, $fechaFinal);	    
	    $disponibilidadesAbiertas=$disponibilidad-$registros;	    
	    $totalObligaciones=disponibilidades($row[4], 16, $fechaInicial, $fechaFinal);	    
	    $registrosAbiertos=$registros-$totalObligaciones;	    
	    $totalPagos=disponibilidades($row[4], 17, $fechaInicial, $fechaFinal);	    
	    $reservas=$registros-$totalObligaciones;	    
	    $cuentasxpagar=$totalObligaciones-$totalPagos;
    	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	// Actulizamos la tabla con los valores encontrados
    	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $update="UPDATE temporal_consulta_pptal_gastos SET "
            . "ptto_inicial ='$pptoInicial', "
            . "adicion = '$adicion', "
            . "reduccion = '$reduccion', "
            . "tras_credito = '$trasCredito', "
            . "tras_cont = '$trasCont', "
            . "presupuesto_dfvo = '$presupuestoDefinitivo', "
            . "disponibilidades = '$disponibilidad', "
            . "saldo_disponible = '$saldoDisponible', "
            . "disponibilidad_abierta = '$disponibilidadesAbiertas', "
            . "registros = '$registros', "
            . "registros_abiertos = '$registrosAbiertos', "
            . "total_obligaciones = '$totalObligaciones', "
            . "total_pagos = '$totalPagos', "
            . "reservas = '$reservas', "
            . "cuentas_x_pagar = '$cuentasxpagar' "
            . "WHERE rubro_fuente = '$row[4]'";
    	$update = $mysqli->query($update);
	}
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Consultamos la tabla temporal para hacer acumulado
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	$acum = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, tras_credito, tras_cont, "
        . "presupuesto_dfvo, disponibilidades, "
        . "saldo_disponible,registros, "
        . "registros_abiertos,total_obligaciones, "
        . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
        . "FROM temporal_consulta_pptal_gastos "
        . "ORDER BY cod_rubro DESC ";
	$acum = $mysqli->query($acum);
  	while ($rowa1= mysqli_fetch_row($acum)){
	    $acumd = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, tras_credito, tras_cont, "
        . "presupuesto_dfvo, disponibilidades, "
        . "saldo_disponible,registros, "
        . "registros_abiertos,total_obligaciones, "
        . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
        . "FROM temporal_consulta_pptal_gastos WHERE id_unico ='$rowa1[0]' "
        . "ORDER BY cod_rubro DESC ";
    	$acumd = $mysqli->query($acumd);
    	while ($rowa= mysqli_fetch_row($acumd)){
	      	if(!empty($rowa[2])){
		        $va11= "SELECT id_unico, "
	            . "cod_rubro,"
	            . "cod_predecesor, "
	            . "ptto_inicial, adicion, tras_credito, tras_cont, "
	            . "presupuesto_dfvo, disponibilidades, "
	            . "saldo_disponible,registros, "
	            . "registros_abiertos,total_obligaciones, "
	            . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
	            . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
		        $va1 = $mysqli->query($va11);
		        $va= mysqli_fetch_row($va1);
		        $pptoInicialM = $rowa[3]+$va[3];
		        $adicionM = $rowa[4]+$va[4];
		        $trasCreditoM = $rowa[5]+$va[5];
		        $trasContM = $rowa[6]+$va[6];
		        $presupuestoDefinitivoM = $rowa[7]+$va[7];
		        $disponibilidadM = $rowa[8]+$va[8];
		        $saldoDisponibleM = $rowa[9]+$va[9];
		        $registrosM = $rowa[10]+$va[10];
		        $registrosAbiertosM = $rowa[11]+$va[11];
		        $totalObligacionesM = $rowa[12]+$va[12];
		        $totalPagosM = $rowa[13]+$va[13];
		        $reservasM = $rowa[14]+$va[14];
		        $cuentasxpagarM = $rowa[15]+$va[15];
		        $reduccionM = $rowa[16]+$va[16];
		        $disponibilidadAbiertaM = $rowa[17]+$va[17];
	        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	        	// Actualizamos con los valores hallados
	        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		        $updateA="UPDATE temporal_consulta_pptal_gastos SET "
                . "ptto_inicial ='$pptoInicialM', "
                . "adicion = '$adicionM', "
                . "reduccion = '$reduccionM', "
                . "tras_credito = '$trasCreditoM', "
                . "tras_cont = '$trasContM', "
                . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
                . "disponibilidades = '$disponibilidadM', "
                . "saldo_disponible = '$saldoDisponibleM', "
                . "disponibilidad_abierta = '$disponibilidadAbiertaM', "
                . "registros = '$registrosM', "
                . "registros_abiertos = '$registrosAbiertosM', "
                . "total_obligaciones = '$totalObligacionesM', "
                . "total_pagos = '$totalPagosM', "
                . "reservas = '$reservasM', "
                . "cuentas_x_pagar = '$cuentasxpagarM' "
                . "WHERE cod_rubro = '$rowa[2]'";
        		$updateA = $mysqli->query($updateA);
        		if($updateA == true){
	        		$i++;        			
        		}
      		}
    	}
 	}
  	return $i;
}
/*
* @$fechaInicial  type{date}  Fecha inicial
* @$fechaFinal    type{date}  Fecha final
* Función para ejecutar el proceso de ejecución de gastos e ingresos en uno solo
* Desarrollado por Erica Gonzalez. Convertida en función por Alexander Numpaque
*/
function start_process_execution_G_I($fechaInicial, $fechaFinal,$i=0){
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Ejecución de ingresos
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	require ('../Conexion/conexion.php');
  	$vaciarTabla = 'TRUNCATE temporal_consulta_pptal_gastos';                     //Limpiamos los valores de la tabla
  	$mysqli->query($vaciarTabla);
        @session_start();
        $parmanno = $_SESSION['anno'];
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Obtenemos todas las cuentas
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	$ctas = "SELECT DISTINCT rpp.nombre, rpp.codi_presupuesto, f.id_unico, rpp2.codi_presupuesto,  rf.id_unico
          FROM gf_rubro_pptal rpp
          LEFT JOIN gf_rubro_fuente rf ON rf.rubro = rpp.id_unico
          LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico
          LEFT JOIN gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico
          WHERE rpp.parametrizacionanno = $parmanno AND rpp.tipoclase = 6
          ORDER BY rpp.codi_presupuesto ASC";
  	$ctass= $mysqli->query($ctas);
  	while ($row1 = mysqli_fetch_row($ctass)) {  // Guardamos los valores en la tabla temporarl
    	$insert= "INSERT INTO temporal_consulta_pptal_gastos "
            . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
            . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
    	$mysqli->query($insert);
  	}
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Obtenemos los detelles relacionados a las cuentas
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	$select ="SELECT DISTINCT rpp.nombre, rpp.codi_presupuesto, f.id_unico,  rpp2.codi_presupuesto, dcp.rubrofuente 
              FROM	gf_detalle_comprobante_pptal dcp
              LEFT JOIN	gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
              LEFT JOIN	gf_rubro_pptal rpp ON rf.rubro = rpp.id_unico
              LEFT JOIN	gf_fuente f ON rf.fuente = f.id_unico
              LEFT JOIN	gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
              LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
              WHERE rpp.tipoclase = 6 AND cp.parametrizacionanno = $parmanno";
	  	
  	$select1 = $mysqli->query($select);
  	while($row = mysqli_fetch_row($select1)) {
	    $pptoInicial=presupuestos($row[4], 1, $fechaInicial, $fechaFinal);       //Obtenemos el valor de presupuesto incial
	    $adicion=presupuestos($row[4], 2, $fechaInicial, $fechaFinal);       //Obtenemos el valor de las adiciones
	    $reduccion=presupuestos($row[4], 3, $fechaInicial, $fechaFinal);       //Obtenemos el valor de las reducciones
	    $presupuestoDefinitivo=($pptoInicial+$adicion)-$reduccion;
	    $recaudos=disponibilidades($row[4], 18, $fechaInicial, $fechaFinal);  //Obtenemos el valor de los rescaudos
	    $saldos= $presupuestoDefinitivo-$recaudos;                                     //Obtenemos el valor de saldos por recaudar
	    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    // Actulizamos los datos hallados la tabla
	    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $update="UPDATE temporal_consulta_pptal_gastos SET "
	            . "ptto_inicial ='$pptoInicial', "
	            . "adicion = '$adicion', "
	            . "reduccion = '$reduccion', "
	            . "presupuesto_dfvo = '$presupuestoDefinitivo', "
	            . "recaudos = '$recaudos', "
	            . "saldos_x_recaudar = '$saldos' "
	            . "WHERE rubro_fuente = '$row[4]'";
	    $update = $mysqli->query($update);
  	}
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Consultamos la tabla temporal para hacer acumulado
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	$acum = "SELECT tm.id_unico,tm.cod_rubro,tm.cod_predecesor, tm.ptto_inicial, tm.adicion, tm.reduccion,tm.presupuesto_dfvo, tm.recaudos, tm.saldos_x_recaudar 
			FROM temporal_consulta_pptal_gastos tm 
			LEFT JOIN gf_rubro_pptal rb ON rb.codi_presupuesto = tm.cod_rubro 
			WHERE rb.tipoclase = 6
			ORDER BY tm.cod_rubro DESC";
  	$acum = $mysqli->query($acum);
  	while ($rowa1= mysqli_fetch_row($acum)){
	    $acumd = "SELECT id_unico, "
	        . "cod_rubro,"
	        . "cod_predecesor, "
	        . "ptto_inicial, adicion, reduccion, "
	        . "presupuesto_dfvo, recaudos, "
	        . "saldos_x_recaudar "
	        . "FROM temporal_consulta_pptal_gastos WHERE id_unico ='$rowa1[0]' "
	        . "ORDER BY cod_rubro DESC ";
	    $acumd = $mysqli->query($acumd);
    	while ($rowa= mysqli_fetch_row($acumd)) {
      		if(!empty($rowa[2])){
		        $va11= "SELECT id_unico, "
		        . "cod_rubro,"
		        . "cod_predecesor, "
		        . "ptto_inicial, adicion, reduccion, "
		        . "presupuesto_dfvo, recaudos, "
		        . "saldos_x_recaudar "
		        . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
		        $va1 = $mysqli->query($va11);
		        $va= mysqli_fetch_row($va1);
		        $pptoInicialM = $rowa[3]+$va[3];
		        $adicionM = $rowa[4]+$va[4];
		        $reduccionM = $rowa[5]+$va[5];
		        $presupuestoDefinitivoM = $rowa[6]+$va[6];
		        $recaudosM = $rowa[7]+$va[7];
		        $saldosM = $rowa[8]+$va[8];
        		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        		// Actulizamos la tabla con los valores encontrados
        		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        		$updateA="UPDATE temporal_consulta_pptal_gastos SET "
                . "ptto_inicial ='$pptoInicialM', "
                . "adicion = '$adicionM', "
                . "reduccion = '$reduccionM', "
                . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
                . "recaudos = '$recaudosM', "
                . "saldos_x_recaudar = '$saldosM' "
                . "WHERE cod_rubro = '$rowa[2]'";
        		$updateA = $mysqli->query($updateA);
      		}
    	}
  	}
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Ejecución de gastos
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Consultamos las cuentas por codigo de cuenta incial y final
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	$ctas2 = "SELECT DISTINCT rpp.nombre, rpp.codi_presupuesto, f.id_unico, rpp2.codi_presupuesto, rf.id_unico
            FROM gf_rubro_pptal rpp
            LEFT JOIN gf_rubro_fuente rf ON rf.rubro = rpp.id_unico
            LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico
            LEFT JOIN gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico
            WHERE rpp.parametrizacionanno = $parmanno 
                AND(rpp.tipoclase = 7 OR rpp.tipoclase = 9 
                OR rpp.tipoclase = 10 OR rpp.tipoclase = 15 
                OR rpp.tipoclase = 16)
            ORDER BY rpp.codi_presupuesto ASC";
  	$ctass2= $mysqli->query($ctas2);
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Guardamos los valores en la tabla temporal
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	while ($row12 = mysqli_fetch_row($ctass2)) {
      	$insert2= "INSERT INTO temporal_consulta_pptal_gastos "
              . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
              . "VALUES ('$row12[1]','$row12[0]','$row12[3]','$row12[2]','$row12[4]' )";
      	$mysqli->query($insert2);
  	}
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	//Consultamos los detalles relacionados a las cuentas
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	$select2 ="SELECT DISTINCT rpp.nombre, rpp.codi_presupuesto, f.id_unico, rpp2.codi_presupuesto, dcp.rubrofuente 
              FROM gf_detalle_comprobante_pptal dcp
              LEFT JOIN	gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
              LEFT JOIN	gf_rubro_pptal rpp ON rf.rubro = rpp.id_unico
              LEFT JOIN	gf_fuente f ON rf.fuente = f.id_unico
              LEFT JOIN	gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
              LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
              WHERE  cp.parametrizacionanno = $parmanno 
                    AND (rpp.tipoclase = 7
                    OR rpp.tipoclase = 9
                    OR rpp.tipoclase = 10 
                    OR rpp.tipoclase = 15 
                    OR rpp.tipoclase = 16)
              ORDER BY rpp.codi_presupuesto ASC";
	
  	$select1 = $mysqli->query($select2);
  	while($row = mysqli_fetch_row($select1)){
	    $pptoInicial  = presupuestos($row[4], 1, $fechaInicial, $fechaFinal);            //Obtenemos el valor de presupuesto inicial
	    $adicion      = presupuestos($row[4], 2, $fechaInicial, $fechaFinal);            //Obtenemos el valor de las adiciones
	    $reduccion    = presupuestos($row[4], 3, $fechaInicial, $fechaFinal);            //Obtenemos el valore de ls reducciones
	    $trasCredito = 0;
            $trasCont    = 0;
            $query = "SELECT valor as value 
                        FROM
                          gf_detalle_comprobante_pptal dc
                        LEFT JOIN
                          gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico
                        LEFT JOIN
                          gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                        WHERE
                          dc.rubrofuente = '$row[4]' 
                          AND tcp.tipooperacion = '4' 
                          AND cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                          AND (tcp.clasepptal = '13')";
            $ap = $GLOBALS['mysqli']->query($query);
            if(mysqli_num_rows($ap)>0){
                while ($sum1= mysqli_fetch_array($ap)) {
                    $tras = $sum1['value'];
                    if($tras>0){
                        $trasCredito += $tras;
                    }else {
                        $trasCont    += $tras;
                    }
                }
            }
	    $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion+$trasCredito+$trasCont; //Obtenemos el valor del presupuesto definitivo
	    $disponibilidad   = disponibilidades($row[4], 14, $fechaInicial, $fechaFinal);   //Obtenemos el valor de las disponibilidades
	    $saldoDisponible  = $presupuestoDefinitivo-$disponibilidad;                 //Obtenemos el valor del saldo disponible
	    $registros          = disponibilidades($row[4], 15, $fechaInicial, $fechaFinal); //Obtenemos el valor de los registros
	    $disponibilidadesAbiertas = $disponibilidad-$registros;                           //Obtenemos el valor de los las disponibilidades abiertas
	    $totalObligaciones  = disponibilidades($row[4], 16, $fechaInicial, $fechaFinal); //Obtenemos el valor del total de las obligaciones
	    $registrosAbiertos = $registros-$totalObligaciones;                               //Obtenemos los registros abiertos
	    $totalPagos         = disponibilidades($row[4], 17, $fechaInicial, $fechaFinal); //Obtenemos el valor del total de los pagos
	    $reservas           = $registros-$totalObligaciones;                        //Obtenemos el valor de las reservas
	    $cuentasxpagar      = $totalObligaciones-$totalPagos;                       //Obtenemos el valor de las cuentas por pagar
    	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	// Actulizamos la tabla con los valores encontrados
    	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $update="UPDATE temporal_consulta_pptal_gastos SET "
	            . "ptto_inicial ='$pptoInicial', "
	            . "adicion = '$adicion', "
	            . "reduccion = '$reduccion', "
	            . "tras_credito = '$trasCredito', "
	            . "tras_cont = '$trasCont', "
	            . "presupuesto_dfvo = '$presupuestoDefinitivo', "
	            . "disponibilidades = '$disponibilidad', "
	            . "saldo_disponible = '$saldoDisponible', "
	            . "disponibilidad_abierta = '$disponibilidadesAbiertas', "
	            . "registros = '$registros', "
	            . "registros_abiertos = '$registrosAbiertos', "
	            . "total_obligaciones = '$totalObligaciones', "
	            . "total_pagos = '$totalPagos', "
	            . "reservas = '$reservas', "
	            . "cuentas_x_pagar = '$cuentasxpagar' "
	            . "WHERE rubro_fuente = '$row[4]'";
	    $update = $mysqli->query($update);
  	}
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Consultamos la tabla temporal para hacer acumulado
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	$acum = "SELECT tm.id_unico,tm.cod_rubro,tm.cod_predecesor, tm.ptto_inicial, tm.adicion, tm.reduccion,tm.presupuesto_dfvo, tm.recaudos, tm.saldos_x_recaudar 
			FROM temporal_consulta_pptal_gastos tm 
			LEFT JOIN gf_rubro_pptal rb ON rb.codi_presupuesto = tm.cod_rubro 
			WHERE rb.tipoclase = 7 OR rb.tipoclase = 9 OR rb.tipoclase = 10
			ORDER BY tm.cod_rubro DESC";
  	$acum = $mysqli->query($acum);
  	while ($rowa1= mysqli_fetch_row($acum)){
	    $acumd = "SELECT id_unico, "
	        . "cod_rubro,"
	        . "cod_predecesor, "
	        . "ptto_inicial, adicion, tras_credito, tras_cont, "
	        . "presupuesto_dfvo, disponibilidades, "
	        . "saldo_disponible,registros, "
	        . "registros_abiertos,total_obligaciones, "
	        . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
	        . "FROM temporal_consulta_pptal_gastos WHERE id_unico ='$rowa1[0]' "
	        . "ORDER BY cod_rubro DESC ";
	    $acumd = $mysqli->query($acumd);
    	while ($rowa= mysqli_fetch_row($acumd)){
	      	if(!empty($rowa[2])){
		        $va11= "SELECT id_unico, "
		        . "cod_rubro,"
		        . "cod_predecesor, "
		        . "ptto_inicial, adicion, tras_credito, tras_cont, "
		        . "presupuesto_dfvo, disponibilidades, "
		        . "saldo_disponible,registros, "
		        . "registros_abiertos,total_obligaciones, "
		        . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
		        . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
		        $va1 = $mysqli->query($va11);
		        $va= mysqli_fetch_row($va1);
		        $pptoInicialM   = $rowa[3]+$va[3];
		        $adicionM       = $rowa[4]+$va[4];
		        $trasCreditoM   = $rowa[5]+$va[5];
		        $trasContM      = $rowa[6]+$va[6];
		        $presupuestoDefinitivoM = $rowa[7]+$va[7];
		        $disponibilidadM  = $rowa[8]+$va[8];
		        $saldoDisponibleM = $rowa[9]+$va[9];
		        $registrosM       = $rowa[10]+$va[10];
		        $registrosAbiertosM = $rowa[11]+$va[11];
		        $totalObligacionesM = $rowa[12]+$va[12];
		        $totalPagosM  = $rowa[13]+$va[13];
		        $reservasM    = $rowa[14]+$va[14];
		        $cuentasxpagarM = $rowa[15]+$va[15];
		        $reduccionM     = $rowa[16]+$va[16];
		        $disponibilidadAbiertaM = $rowa[17]+$va[17];
	        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	        	// Actualizamos con los valores hallados
	        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		        $updateA="UPDATE temporal_consulta_pptal_gastos SET "
		                . "ptto_inicial ='$pptoInicialM', "
		                . "adicion = '$adicionM', "
		                . "reduccion = '$reduccionM', "
		                . "tras_credito = '$trasCreditoM', "
		                . "tras_cont = '$trasContM', "
		                . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
		                . "disponibilidades = '$disponibilidadM', "
		                . "saldo_disponible = '$saldoDisponibleM', "
		                . "disponibilidad_abierta = '$disponibilidadAbiertaM', "
		                . "registros = '$registrosM', "
		                . "registros_abiertos = '$registrosAbiertosM', "
		                . "total_obligaciones = '$totalObligacionesM', "
		                . "total_pagos = '$totalPagosM', "
		                . "reservas = '$reservasM', "
		                . "cuentas_x_pagar = '$cuentasxpagarM' "
		                . "WHERE cod_rubro = '$rowa[2]'";
		        $updateA = $mysqli->query($updateA);
		        if($updateA == true){
		        	$i++;
		        }
     		}
    	}
  	}
  	return $i;
}
/*
* Función para generar el balance de prueba
* Desarrollado por Erica Gonzalez, y  Convertida en función por Alexander Numpaque
*/
function start_test_balance($codigoI,$codigoF,$fechaInicial,$fechaFinal,$i=0) {  
  require ('../Conexion/conexion.php');
  @session_start();
  $parmanno = $_SESSION['anno'];
  $fecha_div = explode("-", $fechaInicial);
  $anno = $fecha_div[0];
  #VACIAR LA TABLA TEMPORAL
  $vaciarTabla = 'TRUNCATE temporal_consulta_tesoreria ';
  $mysqli->query($vaciarTabla);
  #CONSULTA CUENTAS SEGUN VARIABLES QUE RECIBE
  $select ="SELECT DISTINCT
              c.id_unico,
              c.codi_cuenta,
              c.nombre,
              c.naturaleza,
              ch.codi_cuenta
            FROM        gf_cuenta c
            LEFT JOIN   gf_cuenta ch ON c.predecesor = ch.id_unico
            WHERE       c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF' 
            AND c.parametrizacionanno = $parmanno 
            ORDER BY    c.codi_cuenta DESC";
  $select1 = $mysqli->query($select);
  while($row = mysqli_fetch_row($select1)){
    #GUARDA LOS DATOS EN LA TABLA TEMPORAL
    $insert= "INSERT INTO temporal_consulta_tesoreria "
            . "(id_cuenta, numero_cuenta, nombre,cod_predecesor, naturaleza) "
            . "VALUES ('$row[0]','$row[1]','$row[2]','$row[4]','$row[3]' )";
    $mysqli->query($insert);
  }
  //CONSULTO LAS CUENTAS QUE TENGAN MOVIMIENTO
  $mov = "SELECT DISTINCT c.id_unico, c.codi_cuenta, "
          . "c.nombre, c.naturaleza FROM gf_detalle_comprobante dc "
          . "LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico "
          . "WHERE c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF' "
          . "AND c.parametrizacionanno = $parmanno "
          . "ORDER BY c.codi_cuenta DESC";
  $mov= $mysqli->query($mov);
  $totaldeb=0;
  $totalcred=0;
  $totalsaldoI=0;
  $totalsaldoF=0;

  while($row = mysqli_fetch_row($mov)){
    #SI FECHA INICIAL =01 DE ENERO
    $fechaPrimera = $anno.'-01-01';
    if ($fechaInicial==$fechaPrimera){
      #CONSULTA EL SALDO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
      $fechaMax = $anno.'-12-31';
      $com= "SELECT SUM(valor)
            FROM      gf_detalle_comprobante dc
            LEFT JOIN gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
            LEFT JOIN gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
            LEFT JOIN gf_clase_contable cc ON tc.clasecontable = cc.id_unico
            WHERE     cp.fecha BETWEEN '$fechaInicial' AND '$fechaMax'
            AND       cc.id_unico = '5'
            AND       dc.cuenta = '$row[0]' AND cp.parametrizacionanno = $parmanno ";
            $com = $mysqli->query($com);
            if(mysqli_num_rows($com)>0) {
              $saldo = mysqli_fetch_row($com);
              $saldo = $saldo[0];
            } else {
              $saldo=0;
            }
      #DEBITOS
      $deb="SELECT    SUM(valor)
            FROM      gf_detalle_comprobante dc
            LEFT JOIN gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
            LEFT JOIN gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
            LEFT JOIN gf_clase_contable cc ON tc.clasecontable = cc.id_unico
            WHERE     valor>0
            AND       cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal'
            AND       cc.id_unico != '5' AND cp.parametrizacionanno = $parmanno  
            AND       dc.cuenta = '$row[0]'";
      $debt = $mysqli->query($deb);
      if(mysqli_num_rows($debt)>0){
        $debito = mysqli_fetch_row($debt);
        $debito = $debito[0];
      } else {
        $debito=0;
      }
      #CREDITOS
      $cr = "SELECT   SUM(valor)
            FROM      gf_detalle_comprobante dc
            LEFT JOIN gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
            LEFT JOIN gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
            LEFT JOIN gf_clase_contable cc ON tc.clasecontable = cc.id_unico
            WHERE     valor<0
            AND       cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal'
            AND       cc.id_unico != '5' AND cp.parametrizacionanno = $parmanno 
            AND       dc.cuenta = '$row[0]'";
      $cred = $mysqli->query($cr);
      if(mysqli_num_rows($cred)>0){
        $credito = mysqli_fetch_row($cred);
        $credito = $credito[0];
      } else {
        $credito=0;
      }
      #SI FECHA INICIAL !=01 DE ENERO
    } else {
      #TRAE EL SALDO INICIAL
      $sInicial = "SELECT SUM(dc.valor) "
                . "from gf_detalle_comprobante dc "
                . "LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico "
                . "WHERE dc.cuenta='$row[0]' AND cn.parametrizacionanno = $parmanno  "
                . "AND cn.fecha >='$fechaPrimera' AND cn.fecha < '$fechaInicial'";
      $sald = $mysqli->query($sInicial);
      if(mysqli_num_rows($sald)>0){
        $saldo = mysqli_fetch_row($sald);
        $saldo = $saldo[0];
      } else {
        $saldo=0;
      }
      #DEBITOS
      $deb = "SELECT SUM(dc.valor) "
          . "FROM gf_detalle_comprobante dc "
          . "LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico "
          . "WHERE dc.valor>0 AND dc.cuenta='$row[0]' AND cn.parametrizacionanno = $parmanno  AND
          cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' ";
      $debt = $mysqli->query($deb);
      if(mysqli_num_rows($debt)>0){
        $debito = mysqli_fetch_row($debt);
        $debito = $debito[0];
      } else {
        $debito=0;
      }
      #CREDITOS
      $cr = "SELECT SUM(dc.valor) "
          . "FROM gf_detalle_comprobante dc "
          . "LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico "
          . "WHERE dc.valor<0 AND dc.cuenta='$row[0]' AND cn.parametrizacionanno = $parmanno  AND
          cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' ";
      $cred = $mysqli->query($cr);
      if(mysqli_num_rows($cred)>0){
        $credito = mysqli_fetch_row($cred);
        $credito = $credito[0];
      } else {
        $credito=0;
      }
    }
    #SI LA NATURALEZA ES DEBITO
    if($row[3]=='1'){
      if($credito <0){
        $credito =(float) substr($credito, '1');
      }
      $saldoNuevo =$saldo+$debito-$credito;
      $update = "UPDATE temporal_consulta_tesoreria "
              . "SET saldo_inicial ='$saldo', "
              . "debito = '$debito', "
              . "credito ='$credito', "
              . "nuevo_saldo ='$saldoNuevo' "
              . "WHERE id_cuenta ='$row[0]'";
      $update = $mysqli->query($update);
      $d=$debito;
      $c = $credito;
    #SI LA NATURALEZA ES CREDITO
    }else{
      if($credito <0){
        $credito =(float) substr($credito, '1');
      }
      $saldoNuevo =$saldo-$credito+$debito;
      $update = "UPDATE temporal_consulta_tesoreria "
              . "SET saldo_inicial ='$saldo', "
              . "debito = '$credito', "
              . "credito ='$debito', "
              . "nuevo_saldo ='$saldoNuevo' "
              . "WHERE id_cuenta ='$row[0]'";
      $update = $mysqli->query($update);
      $d=$credito;
      $c = $debito;
    }
    //var_dump($row[1]>=$codigoI || $row[1]<=$codigoF);
    if($row[1]>=$codigoI || $row[1]<=$codigoF){
      $totaldeb=$totaldeb+$d;
      $totalcred=$totalcred+$c;
    }
  }
  #CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
  $acum = "SELECT id_cuenta,numero_cuenta, cod_predecesor, saldo_inicial, debito, credito, nuevo_saldo "
          . "FROM temporal_consulta_tesoreria "
          . "ORDER BY numero_cuenta DESC ";
  $acum = $mysqli->query($acum);
  while ($rowa1= mysqli_fetch_row($acum)){
    $acumd = "SELECT id_cuenta,numero_cuenta, cod_predecesor, saldo_inicial, debito, credito, nuevo_saldo "
          . "FROM temporal_consulta_tesoreria WHERE id_cuenta ='$rowa1[0]'"
          . "ORDER BY numero_cuenta DESC ";
    $acumd = $mysqli->query($acumd);
    while ($rowa= mysqli_fetch_row($acumd)){
      if(!empty($rowa[2])){
        $va11= "SELECT numero_cuenta,saldo_inicial, debito, credito, nuevo_saldo "
                  . "FROM temporal_consulta_tesoreria WHERE numero_cuenta ='$rowa[2]'";
        $va1 = $mysqli->query($va11);
        $va= mysqli_fetch_row($va1);
        $saldoIn= $rowa[3]+$va[1];
        $debitoN= $rowa[4]+$va[2];
        $creditoN= $rowa[5]+$va[3];
        $nuevoN=$rowa[6]+$va[4];
        $updateA = "UPDATE temporal_consulta_tesoreria "
                  . "SET saldo_inicial ='$saldoIn', "
                  . "debito = '$debitoN', "
                  . "credito ='$creditoN', "
                  . "nuevo_saldo ='$nuevoN' "
                  . "WHERE numero_cuenta ='$rowa[2]'";
        $updateA = $mysqli->query($updateA);
        if($updateA == true){
        	$i++;
        }
      }
    }
  }
  return $i;
}

function start_test_balancecng($codigoI,$codigoF,$fechaInicial,$fechaFinal,$i=0) {  
require ('../Conexion/conexion.php');
$fecha_div = explode("-", $fechaInicial);
$anno = $fecha_div[0];
#VACIAR LA TABLA TEMPORAL
$vaciarTabla = 'TRUNCATE temporal_consulta_tesoreria ';
$mysqli->query($vaciarTabla);
@session_start();
$parmanno = $_SESSION['anno'];
#CONSULTA CUENTAS SEGUN VARIABLES QUE RECIBE
$select = "SELECT DISTINCT
            c.id_unico, 
            c.codi_cuenta,
            c.nombre,
            c.naturaleza,
            ch.codi_cuenta 
          FROM
            gf_cuenta c
          LEFT JOIN
            gf_cuenta ch ON c.predecesor = ch.id_unico
          WHERE c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF'  
          AND c.parametrizacionanno = $parmanno 
          ORDER BY 
            c.codi_cuenta DESC";
$select1 = $mysqli->query($select);


while ($row = mysqli_fetch_row($select1)) {
    #GUARDA LOS DATOS EN LA TABLA TEMPORAL
    $insert = "INSERT INTO temporal_consulta_tesoreria "
            . "(id_cuenta, numero_cuenta, nombre,cod_predecesor, naturaleza) "
            . "VALUES ('$row[0]','$row[1]','$row[2]','$row[4]','$row[3]' )";
    $mysqli->query($insert);
}


//CONSULTO LAS CUENTAS QUE TENGAN MOVIMIENTO

$mov = "SELECT DISTINCT c.id_unico, c.codi_cuenta, "
        . "c.nombre, c.naturaleza FROM gf_detalle_comprobante dc "
        . "LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico "
        . "WHERE c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF' AND c.parametrizacionanno = $parmanno "
        . "ORDER BY c.codi_cuenta DESC";
$mov = $mysqli->query($mov);
$totaldeb = 0;
$totalcred = 0;
$totalsaldoI = 0;
$totalsaldoF = 0;

while ($row = mysqli_fetch_row($mov)) {
    #SI FECHA INICIAL =01 DE ENERO
    $fechaPrimera = $anno . '-01-01';
    if ($fechaInicial == $fechaPrimera) {
        #CONSULTA EL SALDO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
        $fechaMax = $anno . '-12-31';
        $com = "SELECT SUM(valor)
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE
                      cp.fecha BETWEEN '$fechaInicial' AND '$fechaMax' 
                      AND cp.parametrizacionanno = $parmanno 
                      AND cc.id_unico = '5' 
                      AND dc.cuenta = '$row[0]' ";
        $com = $mysqli->query($com);
        if (mysqli_num_rows($com) > 0) {
            $saldo = mysqli_fetch_row($com);
            if(($saldo[0]=="" || $saldo[0]=='NULL')){
                $saldo = 0;
            } else {
                $saldo = $saldo[0];
            }
        } else {
            $saldo = 0;
        }

        #DEBITOS
        $deb = "SELECT SUM(valor)
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE valor>0 AND 
                      cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                      AND cp.parametrizacionanno = $parmanno 
                      AND cc.id_unico != '5' AND cc.id_unico != '20'  
                      AND dc.cuenta = '$row[0]'";
        $debt = $mysqli->query($deb);
        if (mysqli_num_rows($debt) > 0) {
            $debito = mysqli_fetch_row($debt);
            if(($debito[0]=="" || $debito[0]=='NULL')){
                $debito = 0;
            } else {
                $debito = $debito[0];
            }
        } else {
            $debito = 0;
        }

        #CREDITOS
        $cr = "SELECT SUM(valor)
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE valor<0 AND 
                      cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                      AND cc.id_unico != '5' AND cc.id_unico != '20'  
                      AND cp.parametrizacionanno = $parmanno 
                      AND dc.cuenta = '$row[0]'";
        $cred = $mysqli->query($cr);
        if (mysqli_num_rows($cred) > 0) {
            $credito = mysqli_fetch_row($cred);
            if(($credito[0]=="" || $credito[0]=='NULL')){
                $credito = 0;
            } else {
                $credito = $credito[0];
            }
        } else {
            $credito = 0;
        }

#SI FECHA INICIAL !=01 DE ENERO
    } else {
        #TRAE EL SALDO INICIAL
        $sInicial = "SELECT SUM(dc.valor) "
                . "from gf_detalle_comprobante dc "
                . "LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico "
                . "WHERE dc.cuenta='$row[0]' AND cn.parametrizacionanno = $parmanno "
                . "AND cn.fecha >='$fechaPrimera' AND cn.fecha <'$fechaInicial'";
        $sald = $mysqli->query($sInicial);
        if (mysqli_num_rows($sald) > 0) {
            $saldo = mysqli_fetch_row($sald);
            if(($saldo[0]=="" || $saldo[0]=='NULL')){
                $saldo = 0;
            } else {
                $saldo = $saldo[0];
            }
        } else {
            $saldo = 0;
        }
        #DEBITOS
        $deb = "SELECT SUM(dc.valor) 
                FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN
                  gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                LEFT JOIN
                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                WHERE dc.valor>0 AND dc.cuenta='$row[0]' 
                    AND cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                    AND cc.id_unico != '5' AND cc.id_unico != '20'  
                AND cn.parametrizacionanno = $parmanno ";
        $debt = $mysqli->query($deb);
        if (mysqli_num_rows($debt) > 0) {
            $debito = mysqli_fetch_row($debt);
            if(($debito[0]=="" || $debito[0]=='NULL')){
                $debito = 0;
            } else {
                $debito = $debito[0];
            }
        } else {
            $debito = 0;
        }
        #CREDITOS
        $cr = "SELECT SUM(dc.valor) 
                FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN
                  gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                LEFT JOIN
                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                WHERE dc.valor<0 AND dc.cuenta='$row[0]' 
                    AND cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                    AND cc.id_unico != '5' AND cc.id_unico != '20'     
                AND cn.parametrizacionanno = $parmanno ";
        $cred = $mysqli->query($cr);

        if (mysqli_num_rows($cred) > 0) {
            $credito = mysqli_fetch_row($cred);
             if(($credito[0]=="" || $credito[0]=='NULL')){
                $credito = 0;
            } else {
                $credito = $credito[0];
            }
        } else {
            $credito = 0;
        }
    }
    #SI LA NATURALEZA ES DEBITO
    if ($row[3] == '1') {
        if ($credito < 0) {
            $credito = (float) substr($credito, '1');
        }
        $saldoNuevo = $saldo + $debito - $credito;
        
        $update = "UPDATE temporal_consulta_tesoreria "
                . "SET saldo_inicial ='".($saldo)."', "
                . "debito = '".($debito)."', "
                . "credito ='".($credito)."', "
                . "nuevo_saldo ='".($saldoNuevo)."' "
                . "WHERE id_cuenta ='$row[0]'";
        $update = $mysqli->query($update);

        $d = $debito;
        $c = $credito;
        #SI LA NATURALEZA ES CREDITO
    } else {
        if ($credito < 0) {
            $credito = (float) substr($credito, '1');
        }
        $saldoNuevo = $saldo - $credito + $debito;
        $update = "UPDATE temporal_consulta_tesoreria "
                . "SET saldo_inicial ='".($saldo)."', "
                . "debito = '".($credito)."', "
                . "credito ='".($debito)."', "
                . "nuevo_saldo ='".($saldoNuevo)."' "
                . "WHERE id_cuenta ='$row[0]'";
        $update = $mysqli->query($update);

        $d = $credito;
        $c = $debito;
    }

    //var_dump($row[1]>=$codigoI || $row[1]<=$codigoF);
    if ($row[1] >= $codigoI || $row[1] <= $codigoF) {

        $totaldeb = $totaldeb + $d;
        $totalcred = $totalcred + $c;
    }
}
#CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
$acum = "SELECT id_cuenta,numero_cuenta, cod_predecesor, (saldo_inicial), 
        (debito), (credito), (nuevo_saldo) 
        FROM temporal_consulta_tesoreria 
        ORDER BY numero_cuenta DESC ";
$acum = $mysqli->query($acum);

while ($rowa1 = mysqli_fetch_row($acum)) {
    $acumd = "SELECT id_cuenta,numero_cuenta, cod_predecesor, (saldo_inicial), (debito), 
            (credito), (nuevo_saldo) 
            FROM temporal_consulta_tesoreria WHERE id_cuenta ='$rowa1[0]'
            ORDER BY numero_cuenta DESC ";
    $acumd = $mysqli->query($acumd);
    
    while ($rowa = mysqli_fetch_row($acumd)) {
        if (!empty($rowa[2])) {
            
            $va11 = "SELECT numero_cuenta,(saldo_inicial), 
                    (debito), (credito), (nuevo_saldo) 
                    FROM temporal_consulta_tesoreria WHERE numero_cuenta ='$rowa[2]'";

            $va1 = $mysqli->query($va11);
            $va = mysqli_fetch_row($va1);
            
                $saldoIn  = ($rowa[3]+$va[1]);
                $debitoN  = ($rowa[4]+$va[2]);
                $creditoN = ($rowa[5]+$va[3]);
                $nuevoN   = ($rowa[6]+$va[4]);
            
            if(strlen($rowa1[1])<=6) {
                $saldoIn  = round($rowa[3]) + round($va[1]);
                $debitoN  = round($rowa[4]) + round($va[2]);
                $creditoN = round($rowa[5]) + round($va[3]);
                $nuevoN   = round($rowa[6]) + round($va[4]);
            }
            $updateA = "UPDATE temporal_consulta_tesoreria "
                    . "SET saldo_inicial ='$saldoIn', "
                    . "debito = '$debitoN', "
                    . "credito ='$creditoN', "
                    . "nuevo_saldo ='$nuevoN' "
                    . "WHERE numero_cuenta ='$rowa[2]'";
            $updateA = $mysqli->query($updateA);
        }
    }
}

  return $i;
}
function start_process_execution_RCP($fechaInicial, $fechaFinal,$i=0){
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Ejecución de ingresos
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	require ('../Conexion/conexion.php');
  	$vaciarTabla = 'TRUNCATE temporal_consulta_pptal_gastos';                     //Limpiamos los valores de la tabla
  	$mysqli->query($vaciarTabla);
        @session_start();
        $parmanno = $_SESSION['anno'];
  	
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Ejecución de gastos
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Consultamos las cuentas por codigo de cuenta incial y final
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	$ctas2 = "SELECT DISTINCT rpp.nombre, rpp.codi_presupuesto, f.id_unico, rpp2.codi_presupuesto, rf.id_unico
            FROM gf_rubro_pptal rpp
            LEFT JOIN gf_rubro_fuente rf ON rf.rubro = rpp.id_unico
            LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico
            LEFT JOIN gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico
            WHERE rpp.parametrizacionanno = $parmanno 
                AND(rpp.tipoclase = 16 OR rpp.tipoclase = 15)
            ORDER BY rpp.codi_presupuesto ASC";
  	$ctass2= $mysqli->query($ctas2);
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Guardamos los valores en la tabla temporal
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	while ($row12 = mysqli_fetch_row($ctass2)) {
      	$insert2= "INSERT INTO temporal_consulta_pptal_gastos "
              . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
              . "VALUES ('$row12[1]','$row12[0]','$row12[3]','$row12[2]','$row12[4]' )";
      	$mysqli->query($insert2);
  	}
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	//Consultamos los detalles relacionados a las cuentas
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	$select2 ="SELECT DISTINCT rpp.nombre, rpp.codi_presupuesto, f.id_unico, rpp2.codi_presupuesto, dcp.rubrofuente 
              FROM gf_detalle_comprobante_pptal dcp
              LEFT JOIN	gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
              LEFT JOIN	gf_rubro_pptal rpp ON rf.rubro = rpp.id_unico
              LEFT JOIN	gf_fuente f ON rf.fuente = f.id_unico
              LEFT JOIN	gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
              LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
              WHERE  cp.parametrizacionanno = $parmanno 
                    AND (rpp.tipoclase = 16 OR rpp.tipoclase = 15)
              ORDER BY rpp.codi_presupuesto ASC";
	
  	$select1 = $mysqli->query($select2);
  	while($row = mysqli_fetch_row($select1)){
	    $pptoInicial  = presupuestos($row[4], 1, $fechaInicial, $fechaFinal);            //Obtenemos el valor de presupuesto inicial
	    $adicion      = presupuestos($row[4], 2, $fechaInicial, $fechaFinal);            //Obtenemos el valor de las adiciones
	    $reduccion    = presupuestos($row[4], 3, $fechaInicial, $fechaFinal);            //Obtenemos el valore de ls reducciones
	    $trasCredito = 0;
            $trasCont    = 0;
            $query = "SELECT valor as value 
                        FROM
                          gf_detalle_comprobante_pptal dc
                        LEFT JOIN
                          gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico
                        LEFT JOIN
                          gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                        WHERE
                          dc.rubrofuente = '$row[4]' 
                          AND tcp.tipooperacion = '4' 
                          AND cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                          AND (tcp.clasepptal = '13')";
            $ap = $GLOBALS['mysqli']->query($query);
            if(mysqli_num_rows($ap)>0){
                while ($sum1= mysqli_fetch_array($ap)) {
                    $tras = $sum1['value'];
                    if($tras>0){
                        $trasCredito += $tras;
                    }else {
                        $trasCont    += $tras;
                    }
                }
            }
	    $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion+$trasCredito+$trasCont; //Obtenemos el valor del presupuesto definitivo
	    $disponibilidad   = disponibilidades($row[4], 14, $fechaInicial, $fechaFinal);   //Obtenemos el valor de las disponibilidades
	    $saldoDisponible  = $presupuestoDefinitivo-$disponibilidad;                 //Obtenemos el valor del saldo disponible
	    $registros          = disponibilidades($row[4], 15, $fechaInicial, $fechaFinal); //Obtenemos el valor de los registros
	    $disponibilidadesAbiertas = $disponibilidad-$registros;                           //Obtenemos el valor de los las disponibilidades abiertas
	    $totalObligaciones  = disponibilidades($row[4], 16, $fechaInicial, $fechaFinal); //Obtenemos el valor del total de las obligaciones
	    $registrosAbiertos = $registros-$totalObligaciones;                               //Obtenemos los registros abiertos
	    $totalPagos         = disponibilidades($row[4], 17, $fechaInicial, $fechaFinal); //Obtenemos el valor del total de los pagos
	    $reservas           = $registros-$totalObligaciones;                        //Obtenemos el valor de las reservas
	    $cuentasxpagar      = $totalObligaciones-$totalPagos;                       //Obtenemos el valor de las cuentas por pagar
    	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	// Actulizamos la tabla con los valores encontrados
    	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $update="UPDATE temporal_consulta_pptal_gastos SET "
	            . "ptto_inicial ='$pptoInicial', "
	            . "adicion = '$adicion', "
	            . "reduccion = '$reduccion', "
	            . "tras_credito = '$trasCredito', "
	            . "tras_cont = '$trasCont', "
	            . "presupuesto_dfvo = '$presupuestoDefinitivo', "
	            . "disponibilidades = '$disponibilidad', "
	            . "saldo_disponible = '$saldoDisponible', "
	            . "disponibilidad_abierta = '$disponibilidadesAbiertas', "
	            . "registros = '$registros', "
	            . "registros_abiertos = '$registrosAbiertos', "
	            . "total_obligaciones = '$totalObligaciones', "
	            . "total_pagos = '$totalPagos', "
	            . "reservas = '$reservas', "
	            . "cuentas_x_pagar = '$cuentasxpagar' "
	            . "WHERE rubro_fuente = '$row[4]'";
	    $update = $mysqli->query($update);
  	}
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	// Consultamos la tabla temporal para hacer acumulado
  	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	$acum = "SELECT tm.id_unico,tm.cod_rubro,tm.cod_predecesor, tm.ptto_inicial, tm.adicion, tm.reduccion,tm.presupuesto_dfvo, tm.recaudos, tm.saldos_x_recaudar 
			FROM temporal_consulta_pptal_gastos tm 
			LEFT JOIN gf_rubro_pptal rb ON rb.codi_presupuesto = tm.cod_rubro 
			WHERE (rb.tipoclase = 16 OR rb.tipoclase = 15) AND rb.parametrizacionanno = $parmanno 
			ORDER BY tm.cod_rubro DESC";
  	$acum = $mysqli->query($acum);
  	while ($rowa1= mysqli_fetch_row($acum)){
	    $acumd = "SELECT id_unico, "
	        . "cod_rubro,"
	        . "cod_predecesor, "
	        . "ptto_inicial, adicion, tras_credito, tras_cont, "
	        . "presupuesto_dfvo, disponibilidades, "
	        . "saldo_disponible,registros, "
	        . "registros_abiertos,total_obligaciones, "
	        . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
	        . "FROM temporal_consulta_pptal_gastos WHERE id_unico ='$rowa1[0]' "
	        . "ORDER BY cod_rubro DESC ";
	    $acumd = $mysqli->query($acumd);
    	while ($rowa= mysqli_fetch_row($acumd)){
	      	if(!empty($rowa[2])){
		        $va11= "SELECT id_unico, "
		        . "cod_rubro,"
		        . "cod_predecesor, "
		        . "ptto_inicial, adicion, tras_credito, tras_cont, "
		        . "presupuesto_dfvo, disponibilidades, "
		        . "saldo_disponible,registros, "
		        . "registros_abiertos,total_obligaciones, "
		        . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
		        . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
		        $va1 = $mysqli->query($va11);
		        $va= mysqli_fetch_row($va1);
		        $pptoInicialM   = $rowa[3]+$va[3];
		        $adicionM       = $rowa[4]+$va[4];
		        $trasCreditoM   = $rowa[5]+$va[5];
		        $trasContM      = $rowa[6]+$va[6];
		        $presupuestoDefinitivoM = $rowa[7]+$va[7];
		        $disponibilidadM  = $rowa[8]+$va[8];
		        $saldoDisponibleM = $rowa[9]+$va[9];
		        $registrosM       = $rowa[10]+$va[10];
		        $registrosAbiertosM = $rowa[11]+$va[11];
		        $totalObligacionesM = $rowa[12]+$va[12];
		        $totalPagosM  = $rowa[13]+$va[13];
		        $reservasM    = $rowa[14]+$va[14];
		        $cuentasxpagarM = $rowa[15]+$va[15];
		        $reduccionM     = $rowa[16]+$va[16];
		        $disponibilidadAbiertaM = $rowa[17]+$va[17];
	        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	        	// Actualizamos con los valores hallados
	        	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		        $updateA="UPDATE temporal_consulta_pptal_gastos SET "
		                . "ptto_inicial ='$pptoInicialM', "
		                . "adicion = '$adicionM', "
		                . "reduccion = '$reduccionM', "
		                . "tras_credito = '$trasCreditoM', "
		                . "tras_cont = '$trasContM', "
		                . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
		                . "disponibilidades = '$disponibilidadM', "
		                . "saldo_disponible = '$saldoDisponibleM', "
		                . "disponibilidad_abierta = '$disponibilidadAbiertaM', "
		                . "registros = '$registrosM', "
		                . "registros_abiertos = '$registrosAbiertosM', "
		                . "total_obligaciones = '$totalObligacionesM', "
		                . "total_pagos = '$totalPagosM', "
		                . "reservas = '$reservasM', "
		                . "cuentas_x_pagar = '$cuentasxpagarM' "
		                . "WHERE cod_rubro = '$rowa[2]'";
		        $updateA = $mysqli->query($updateA);
		        if($updateA == true){
		        	$i++;
		        }
     		}
    	}
  	}
  	return $i;
}

function nomina(){
    require ('../Conexion/conexion.php');
    @session_start();
    $parmanno = $_SESSION['anno'];
    #* Eliminar Vista Si Existe
    $vaciarTabla = 'DROP VIEW IF EXISTS nomina';
    $mysqli->query($vaciarTabla);
    #* Crear Vista
    $createv = "CREATE VIEW nomina AS 
            SELECT DISTINCT e.id_unico as empleado, 
                    SUM(n.valor) as valor, 
                    n.concepto as id_concepto, 
                    con.codigo as codigo_concepto, 
            cp.id_unico as Concepto, 
            c.id_unico as Consecutivo, 
            ca.codigointerno as Denominacion, 
            ca.nombre as Grado, 
            tv.id_unico Tipo_vinculacion
             FROM gn_novedad n 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gn_concepto con ON n.concepto = con.id_unico 
            LEFT JOIN gn_homologaciones h1 ON h1.id_origen = e.id_unico 
            LEFT JOIN codigo_personal cp ON h1.id_destino = cp.id_unico AND cp.parametrizacionanno =$parmanno 
            LEFT JOIN gn_homologaciones h2 ON h2.id_origen = e.id_unico 
            LEFT JOIN consecutivo c ON h2.id_destino = c.id_unico 
            LEFT JOIN gn_homologaciones h3 ON h3.id_origen = e.id_unico 
            LEFT JOIN tipo_vinculacion tv ON h3.id_destino = tv.id_unico AND tv.parametrizacionanno = $parmanno 
            LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
            LEFT JOIN gn_tabla_homologable th1 on h1.origen = th1.id
            LEFT JOIN gn_informe i on th1.informe = i.id 
            LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico 
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico

            WHERE cp.parametrizacionanno = $parmanno AND p.parametrizacionanno = $parmanno 
            AND tv.parametrizacionanno = $parmanno 
            AND h1.origen= 43 
            AND h2.origen= 44 
            AND h3.origen= 45
            GROUP BY cp.id_unico,  c.id_unico, tv.id_unico, ca.id_unico, n.empleado, n.concepto 
            HAVING SUM(n.valor)>0";
    $mysqli->query($createv);
    #* Eliminar Vista Si Existe
    $vaciarTabla = 'DROP VIEW IF EXISTS nomina_consolido';
    $createv = "CREATE VIEW nomina_consolido AS 
             SELECT SUM(valor) as valor, codigo_concepto, Concepto, Consecutivo, Denominacion, Grado, Tipo_Vinculacion, 
            GROUP_CONCAT(empleado) as empleado 
            FROM nomina 
            WHERE codigo_concepto IN (061,120,968,079,080,159,158,161,152,175,170,999)
            GROUP BY codigo_concepto, Concepto, Consecutivo, Denominacion, Grado, Tipo_Vinculacion ";
    $mysqli->query($createv);
    return 1;    
}

function generarBalance($anno, $fechaI, $fechaF, $codigoI, $codigoF,  $tipo){
	require ('../Conexion/conexion.php');
	require ('../Conexion/ConexionPDO.php');
	$con = new ConexionPDO();
    @session_start();
    $id_par = $_SESSION['anno'];
    $compania = $_SESSION['compania'];

    global $con;
         
    $create  = $con->Listar("CREATE TABLE IF NOT EXISTS temporal_balance$compania (
        `id_unico` int(11) NOT NULL,
        `id_cuenta` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
        `numero_cuenta` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
        `nombre` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
        `cod_predecesor` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
        `saldo_inicial` double(20,2) DEFAULT NULL,
        `debito` double(20,2) DEFAULT NULL,
        `credito` double(20,2) DEFAULT NULL,
        `nuevo_saldo` double(20,2) DEFAULT NULL,
        `naturaleza` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
        `saldo_inicial_debito` double(20,2) DEFAULT NULL,
        `saldo_inicial_credito` double(20,2) DEFAULT NULL,
        `nuevo_saldo_debito` double(20,2) DEFAULT NULL,
        `nuevo_saldo_credito` double(20,2) DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
      ALTER TABLE temporal_balance$compania 
        ADD PRIMARY KEY IF NOT EXISTS(`id_unico`);
      ALTER TABLE temporal_balance$compania 
        MODIFY `id_unico` int(11) NOT NULL AUTO_INCREMENT;");
    $vaciarTabla = $con->Listar("TRUNCATE temporal_balance$compania");
    #CONSULTA CUENTAS 
    $rowc = $con->Listar(" SELECT DISTINCT c.id_unico, c.codi_cuenta, 
        c.nombre, p.codi_cuenta, c.naturaleza 
        FROM gf_detalle_comprobante dc 
        LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
        LEFT JOIN gf_cuenta p ON c.predecesor = p.id_unico 
        WHERE c.parametrizacionanno = $id_par
        AND  c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF'  
        ORDER BY c.codi_cuenta DESC ");
   
    for ($i=0; $i <count($rowc) ; $i++) {  
        #GUARDA LOS DATOS EN LA TABLA TEMPORAL
        $sql_cons ="INSERT INTO `temporal_balance$compania` 
                    ( `id_cuenta`, `numero_cuenta`,`nombre`,
                    `cod_predecesor`,`naturaleza`) 
            VALUES (:id_cuenta, :numero_cuenta, :nombre, 
                    :cod_predecesor,:naturaleza)";
            $sql_dato = array(
                    array(":id_cuenta",$rowc[$i][0]),
                    array(":numero_cuenta",$rowc[$i][1]),
                    array(":nombre",$rowc[$i][2]),
                    array(":cod_predecesor",$rowc[$i][3]),   
                    array(":naturaleza",$rowc[$i][4]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
    }
    $cc = 0;
    while($cc==0){
        $rowcf = $con->Listar("SELECT DISTINCT c.id_unico, c.codi_cuenta, 
            c.nombre, p.codi_cuenta, c.naturaleza 
        FROM temporal_balance$compania t 
        LEFT JOIN gf_cuenta c ON t.cod_predecesor = c.codi_cuenta 
        LEFT JOIN gf_cuenta p ON c.predecesor = p.id_unico 
        WHERE c.parametrizacionanno = $id_par 
            AND c.id_unico NOT IN (SELECT id_cuenta FROM temporal_balance$compania) ");
        if(count($rowcf)>0){
            for ($i=0; $i <count($rowcf) ; $i++) {  
                $sql_cons ="INSERT INTO `temporal_balance$compania` 
                        ( `id_cuenta`, `numero_cuenta`,`nombre`,
                        `cod_predecesor`,`naturaleza`) 
                VALUES (:id_cuenta, :numero_cuenta, :nombre, 
                        :cod_predecesor,:naturaleza)";
                $sql_dato = array(
                        array(":id_cuenta",$rowcf[$i][0]),
                        array(":numero_cuenta",$rowcf[$i][1]),
                        array(":nombre",$rowcf[$i][2]),
                        array(":cod_predecesor",$rowcf[$i][3]),   
                        array(":naturaleza",$rowcf[$i][4]),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
        } else {
            $cc = 1;
            $rowcf = $con->Listar("SELECT DISTINCT c.id_unico, c.codi_cuenta, c.nombre, p.codi_cuenta, c.naturaleza 
                FROM gf_cuenta c 
                LEFT JOIN gf_cuenta p ON c.predecesor = p.id_unico 
                WHERE c.parametrizacionanno = $id_par AND length(c.codi_cuenta)= 1
                AND c.id_unico NOT IN (SELECT id_cuenta FROM temporal_balance$compania )");
            if(count($rowcf)>0){
                for ($i=0; $i <count($rowcf) ; $i++) {  
                    $sql_cons ="INSERT INTO `temporal_balance$compania` 
                            ( `id_cuenta`, `numero_cuenta`,`nombre`,
                            `cod_predecesor`,`naturaleza`) 
                    VALUES (:id_cuenta, :numero_cuenta, :nombre, 
                            :cod_predecesor,:naturaleza)";
                    $sql_dato = array(
                            array(":id_cuenta",$rowcf[$i][0]),
                            array(":numero_cuenta",$rowcf[$i][1]),
                            array(":nombre",$rowcf[$i][2]),
                            array(":cod_predecesor",$rowcf[$i][3]),   
                            array(":naturaleza",$rowcf[$i][4]),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                }
            }
        }
    }
    
    #* Consultar Cuentas Movimiento 
    $mov = $con->Listar("SELECT DISTINCT 
                c.id_unico, c.codi_cuenta, 
                c.nombre, c.naturaleza 
            FROM gf_detalle_comprobante dc 
            LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
            WHERE  c.parametrizacionanno = $id_par 
            AND c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF'  
            ORDER BY c.codi_cuenta DESC");
    $totaldeb       = 0;
    $totalcred      = 0;
    $totalsaldoID   = 0;
    $totalsaldoIC   = 0;
    $totalsaldoFD   = 0;
    $totalsaldoFC   = 0;
    $totalsaldo     = 0;
                    
    #SI FECHA INICIAL =01 DE ENERO
    $fechaPrimera   = $anno . '-01-01';
    $fechaMax       = $anno . '-12-31';
    for ($m =0; $m < count($mov) ; $m++) { 
        $wsi = "";
        $wsc = "";
        $c   = "AND dc.cuenta = '".$mov[$m][0]."' AND cp.parametrizacionanno =$id_par ";
        
        if ($fechaI == $fechaPrimera) {
            $wsi .= $c." AND cp.fecha BETWEEN '$fechaI' AND '$fechaMax' AND cc.id_unico = '5' ";
            $wsc .= $c." AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' AND cc.id_unico != '5' AND cc.id_unico != '20' ";
        } else {
            $wsi .= $c." AND cp.fecha >='$fechaPrimera' AND cp.fecha <'$fechaI' AND cc.id_unico !='20'";
            $wsc .= $c." AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' AND cc.id_unico != '5' AND cc.id_unico != '20' ";
        }
        
        
        #CONSULTA EL SALDO DEBITO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
        $com = $con->Listar("SELECT IF(SUM(dc.valor)<0,SUM(dc.valor)*-1,SUM(dc.valor))
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE dc.valor >0 $wsi ");
        
        if (!empty($com[0][0])) {
            $saldodebito = $com[0][0];
        } else {
            $saldodebito = 0;
        }
        #CONSULTA EL SALDO CREDITO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
        $com = $con->Listar("SELECT SUM(IF(dc.valor<0, dc.valor*-1, dc.valor))
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE dc.valor <0 $wsi ");
        if (!empty($com[0][0])) {
            $saldocredito = $com[0][0];
        } else {
            $saldocredito = 0;
        }
        
        #DEBITOS
        $deb = $con->Listar("SELECT SUM(IF(dc.valor<0, dc.valor*-1, dc.valor))
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE dc.valor>0 $wsc");
        if (!empty($deb[0][0])) {
            $debito = $deb[0][0];
        } else {
            $debito = 0;
        }
        #CREDITOS
        $cr = $con->Listar("SELECT SUM(IF(dc.valor<0, dc.valor*-1, dc.valor))
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE dc.valor<0 $wsc");
        if (!empty($cr[0][0])) {
            $credito =$cr[0][0];
        } else {
            $credito = 0;
        }
        
        if($mov[$m][3]=='1'){
            $saldoNuevo     = ($saldodebito+$debito)-($saldocredito+$credito);
            $saldoInicial   = ($saldodebito - $saldocredito);
            if($saldoNuevo > 0){
                $nuevoSaldodebito = $saldoNuevo;
                $nuevoSaldoCredito = 0;
            } else {
                $nuevoSaldoCredito = $saldoNuevo*-1;
                $nuevoSaldodebito = 0;
            }
            $sql_cons ="UPDATE `temporal_balance$compania` 
                SET `saldo_inicial_debito` =:saldo_inicial_debito , 
                `saldo_inicial_credito` =:saldo_inicial_credito , 
                `debito`=:debito, 
                `credito`=:credito, 
                `nuevo_saldo_debito`=:nuevo_saldo_debito ,
                `nuevo_saldo_credito`=:nuevo_saldo_credito,
                `nuevo_saldo`=:nuevo_saldo, 
                `saldo_inicial`=:saldo_inicial  
                WHERE `id_cuenta` =:id_cuenta ";
            $sql_dato = array(
                array(":saldo_inicial_debito",$saldodebito),
                array(":saldo_inicial_credito",$saldocredito),
                array(":debito",$debito),
                array(":credito",$credito),
                array(":nuevo_saldo_debito",$nuevoSaldodebito),
                array(":nuevo_saldo_credito",$nuevoSaldoCredito),
                array(":nuevo_saldo",$saldoNuevo),
                array(":saldo_inicial",$saldoInicial),
                array(":id_cuenta",$mov[$m][0]),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            $totaldeb       += $debito;
            $totalcred      += $credito;
            $totalsaldoID   += $saldodebito;
            $totalsaldoIC   += $saldocredito;
            $totalsaldoFD   += $nuevoSaldodebito;
            $totalsaldoFC   += $nuevoSaldoCredito;
            $totalsaldo     += $saldoNuevo;
            
        #SI LA NATURALEZA ES CREDITO
        }else{
            $saldoNuevo     = ($saldodebito+$debito)-($saldocredito+$credito);
            $saldoInicial   = ($saldodebito - $saldocredito);
            if($saldoNuevo > 0){
                $nuevoSaldodebito = $saldoNuevo;
                $nuevoSaldoCredito = 0;
            } else {
                $nuevoSaldoCredito = $saldoNuevo*-1;
                $nuevoSaldodebito = 0;
            }
            $sql_cons ="UPDATE `temporal_balance$compania` 
                SET `saldo_inicial_debito` =:saldo_inicial_debito , 
                `saldo_inicial_credito` =:saldo_inicial_credito , 
                `debito`=:debito, 
                `credito`=:credito, 
                `nuevo_saldo_debito`=:nuevo_saldo_debito, 
                `nuevo_saldo_credito`=:nuevo_saldo_credito,
                `saldo_inicial`=:saldo_inicial, 
                `nuevo_saldo`=:nuevo_saldo  
                WHERE `id_cuenta` =:id_cuenta ";
            $sql_dato = array(
                array(":saldo_inicial_debito",$saldocredito),
                array(":saldo_inicial_credito",$saldodebito),
                array(":debito",$credito),
                array(":credito",$debito),
                array(":nuevo_saldo_debito",$nuevoSaldoCredito),
                array(":nuevo_saldo_credito",$nuevoSaldodebito),
                array(":nuevo_saldo",$saldoNuevo),
                array(":saldo_inicial",$saldoInicial),
                array(":id_cuenta",$mov[$m][0]),
            );
            $obj_resp       = $con->InAcEl($sql_cons,$sql_dato);
            $totaldeb       += $credito;
            $totalcred      += $debito;
            $totalsaldoID   += $saldocredito;
            $totalsaldoIC   += $saldodebito;
            $totalsaldoFD   += $nuevoSaldoCredito;
            $totalsaldoFC   += $nuevoSaldodebito;
            $totalsaldo     += $saldoNuevo;
        }
        
        
    }
    $totalc = $con->Listar("SELECT count(*) 
        FROM temporal_balance$compania 
        ORDER BY numero_cuenta DESC ");
    $i = 0;
    $v = 500;
    $valorreg = $totalc[0][0];
    while($valorreg > 0){
        #CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
        $rowa1 = $con->Listar("SELECT id_cuenta,numero_cuenta, cod_predecesor, 
                saldo_inicial_debito,saldo_inicial_credito,
                debito, credito, 
                nuevo_saldo_debito, nuevo_saldo_credito, 
                nuevo_saldo, saldo_inicial 
                FROM temporal_balance$compania 
                WHERE id_unico BETWEEN $i AND $v 
                ORDER BY numero_cuenta DESC ");
        for ($ra = 0; $ra < count($rowa1); $ra++) {
            $rowa = $con->Listar("SELECT id_cuenta,numero_cuenta, cod_predecesor, 
                    saldo_inicial_debito, saldo_inicial_credito,
                    debito, credito, 
                    nuevo_saldo_debito, nuevo_saldo_credito, 
                    nuevo_saldo , saldo_inicial 
                FROM temporal_balance$compania WHERE id_cuenta ='".$rowa1[$ra][0]."'
                ORDER BY numero_cuenta DESC ");
            for ($rl = 0; $rl < count($rowa); $rl++) { 
                if(!empty($rowa[$rl][2])){
                    $va = $con->Listar("SELECT numero_cuenta,
                        saldo_inicial_debito, saldo_inicial_credito,
                        debito, credito, 
                        nuevo_saldo_debito, nuevo_saldo_credito, 
                        nuevo_saldo , saldo_inicial 
                        FROM temporal_balance$compania WHERE numero_cuenta ='".$rowa[$rl][2]."'");
                    $saldoInD   = $rowa[$rl][3] + $va[0][1];
                    $saldoInC   = $rowa[$rl][4] + $va[0][2];
                    $debitoN    = $rowa[$rl][5] + $va[0][3];
                    $creditoN   = $rowa[$rl][6] + $va[0][4];
                    $nuevoND    = $rowa[$rl][7] + $va[0][5];
                    $nuevoNC    = $rowa[$rl][8] + $va[0][6];
                    $nuevo      = $rowa[$rl][9] + $va[0][7];
                    $saldoI     = $rowa[$rl][10] + $va[0][8];
                    
                    $sql_cons ="UPDATE `temporal_balance$compania` 
                        SET `saldo_inicial_debito` =:saldo_inicial_debito , 
                        `saldo_inicial_credito` =:saldo_inicial_credito , 
                        `debito`=:debito, 
                        `credito`=:credito, 
                        `nuevo_saldo_debito`=:nuevo_saldo_debito, 
                        `nuevo_saldo_credito`=:nuevo_saldo_credito, 
                        `nuevo_saldo`=:nuevo_saldo, 
                        `saldo_inicial`=:saldo_inicial 
                        WHERE `numero_cuenta` =:numero_cuenta ";
                    $sql_dato = array(
                        array(":saldo_inicial_debito",$saldoInD),
                        array(":saldo_inicial_credito",$saldoInC),
                        array(":debito",$debitoN),
                        array(":credito",$creditoN),
                        array(":nuevo_saldo_debito",$nuevoND),
                        array(":nuevo_saldo_credito",$nuevoNC),
                        array(":nuevo_saldo",$nuevo),
                        array(":saldo_inicial",$saldoI),
                        array(":numero_cuenta",$rowa[$rl][2]),
                    );
                    $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                }
            }
        }
        $i =$v+1;
        $v +=500;
        $valorreg -=500; 
    }
    return 0;
}
?>

