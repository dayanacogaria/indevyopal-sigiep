<?php
session_start();
header('Content-Type: application/json');

require_once '../Conexion/conexion.php';

$content = file_get_contents("php://input");
$decoded_json = json_decode($content);
$factura = $decoded_json->factura;
$tipo = $decoded_json->type;

setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
$hoy = date('Y-m-d');
$hora = date('h:i:s A'); 

switch ($tipo) {

	case 'invoice':
		$invoice_data = [];
		$invoice_lines = [];
		$tax_ttt = [];
		$customer_data = [];
		$payment_forms = [];
		$email =[];
		$emailentidad =[];
		$notas =[];
		$valor_iva = 0;
		$valor_impo = 0;
		$iv_porc = 0;
		$valor = 0;
		$iva = 0;
		$impo = 0;
		$tax_ex_amt = 0;
		$valorTotal = 0;
		$cant = 0;
		$cantiva = 0;
		$cantimpo = 0;
		$diff = 0;

		$sqlF = "SELECT fat.id_unico,
					tpf.nombre,
					fat.numero_factura,
					CONCAT(ELT(WEEKDAY(fat.fecha_factura) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS DIA_SEMANA,
					fat.fecha_factura,
					date_format(fat.fecha_vencimiento,'%d/%m/%Y'),
					IF(	CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
								IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
								IF(ter.apellidouno IS NULL,'',
								IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
								IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' 
					OR 	CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
								IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
								IF(ter.apellidouno IS NULL,'',
								IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
								IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,
						(ter.razonsocial),
					    CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
					    		IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
					    		IF(ter.apellidouno IS NULL,'',
					    		IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
					    		IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
					ter.numeroidentificacion, fat.descripcion, ter.email, tf.valor, dir.direccion, tip.codigo,fat.fecha_vencimiento, fat.forma_pago, fp.codigo_dian, pcom.tipo, ter.tiporegimen, dir.ciudad_direccion
		FROM		gp_factura fat
		LEFT JOIN	gp_tipo_factura tpf 	   ON tpf.id_unico = fat.tipofactura
		LEFT JOIN	gf_tercero ter 			   ON ter.id_unico = fat.tercero
        LEFT JOIN	gf_perfil_tercero pter 	   ON ter.id_unico = pter.tercero
        LEFT JOIN	gf_perfil per 	           ON per.id_unico = pter.perfil
        LEFT JOIN	gf_perfil_compania pcom    ON per.id_unico = pcom.perfil
		LEFT JOIN   gf_telefono tf             ON tf.tercero = ter.id_unico
		LEFT JOIN   gf_direccion dir           ON dir.tercero = ter.id_unico
		LEFT JOIN   gf_tipo_identificacion tip ON ter.tipoidentificacion = tip.id_unico
		LEFT JOIN   gf_forma_pago fp           ON fat.metodo_pago = fp.id_unico
		WHERE		fat.id_unico = '$factura'";
		$resultF = $mysqli->query($sqlF);
		$rowF = mysqli_fetch_row($resultF);   


         //parámetro del correo de la empresa para envíos de copias de la facturación electrónica
		 $sqlE = "SELECT valor FROM gs_parametros_basicos WHERE nombre like '%Email Facturación Electrónica%'";
         $resultE = $mysqli->query($sqlE);
		 $rowE = mysqli_fetch_row($resultE); 

		 //parámetro de las notas adicionales en la factura
		 $sqlN = "SELECT valor FROM gs_parametros_basicos WHERE nombre like '%Notas Factura Electrónica%'";
         $resultN = $mysqli->query($sqlN);
		 $rowN = mysqli_fetch_row($resultN);

         //ciudad  del tercero
		 $sqlC = "SELECT codigo_dian FROM gf_ciudad where id_unico = $rowF[18]";
         $resultC = $mysqli->query($sqlC);
		 $rowC = mysqli_fetch_row($resultC); 

  
		$sqlDetalleFactura = "SELECT conp.nombre,
								dtf.cantidad,
								dtf.valor,
								dtf.iva,
								dtf.impoconsumo,
								dtf.ajuste_peso,
								dtf.valor_total_ajustado,
								pln.codi,
								uf.codigo_dian, dtf.valor_descuento, dtf.descripcion 							
					FROM		gp_detalle_factura dtf					
					LEFT JOIN	gp_concepto conp ON conp.id_unico = dtf.concepto_tarifa					
			        LEFT JOIN   gf_plan_inventario pln  ON conp.plan_inventario = pln.id_unico
			        LEFT JOIN   gf_unidad_factor uf   ON dtf.unidad_origen = uf.id_unico 
					WHERE		dtf.factura = '$factura' and dtf.valor_total_ajustado > 0";
		$resultDT = $mysqli->query($sqlDetalleFactura);
		#detalles de la factura
		#********DETALLES DE LA FACTURA*************#
		$valor_base_iva 		= 0;
		$valor_base_impoconsumo = 0;
		$valor_iva_total		= 0;
		$valor_impoconsumo_total= 0;	
		$nro_items_iva			= 0;
		$nro_items_impo			= 0;
		$total_porcentaje_iva	= 0;
		$total_procentaje_impo 	= 0;
		$valor_total_factura 	= 0;
		while($rdf = mysqli_fetch_row($resultDT)){
			
		    $valor_total_factura 	+= $rdf[6];
		    $cant 		+= $rdf[1];
			$iva 		+= $rdf[3];
			$impo 		+= $rdf[4];
			$valorTotal += $rdf[6];
			$valorBase   = $rdf[2];
			$allowance_charges = [];
			$tax_totals = [];
			$invoice_dt = [];
			$iv_percentage 		= 0;
			$impo_percentage 	= 0;
			$valor_cantidad 	= (float)$rdf[1] > 1 ? (float)$rdf[2] * (float)$rdf[1] : (float)$rdf[2];

			if($rdf[3] > 0) {
				$valor_iva 		+= $valor_cantidad;		
			    $v_iva 			= $rdf[3] * $rdf[1];
				$iv_percentage 	= ROUND(($v_iva*100)/$valor_cantidad);
				$vv 			= $v_iva;
				$vbt 			= $valorBase * $rdf[1];
				array_push($tax_totals, [
					"tax_id" => 1,
		            "tax_amount" => number_format((float)$v_iva, 2, '.', ''),
		            "taxable_amount" => number_format($vbt, 2, '.', ''),
		            "percent" => $iv_percentage
				]);
				$valor_base_iva 		+= number_format($vbt, 2, '.', '');
				$valor_iva_total		+= number_format((float)$v_iva, 2, '.', '');
				$nro_items_iva			+= 1;
				$total_porcentaje_iva	+= $iv_percentage;
			} elseif($rdf[4] > 0){
				$valor_impo 	+= $valor_cantidad;
				$v_impo 		= $rdf[4] * $rdf[1];
				$i_per 			= ROUND(($v_impo*100)/$valor_cantidad);
				$vv 			= $v_impo;
				$vbt 			= $valorBase * $rdf[1];
				array_push($tax_totals, [
					"tax_id" => 4,
		            "tax_amount" => number_format((float)$v_impo, 2, '.', ''),
		            "taxable_amount" => number_format($vbt, 2, '.', ''),
		            "percent" => $i_per
				]);
				$valor_base_impoconsumo += number_format($vbt, 2, '.', '');
				$valor_impoconsumo_total+= number_format((float)$v_impo, 2, '.', '');	
				$nro_items_impo			+= 1;
				$total_procentaje_impo 	+= $i_per;
			} else {
				$valor_iva 		+= $valor_cantidad;		
			    $v_iva 			= $rdf[3] * $rdf[1];
				$iv_percentage 	= ROUND(($v_iva*100)/$valor_cantidad);
				$vv 			= $v_iva;
				$vbt 			= $valorBase * $rdf[1];
				array_push($tax_totals, [
					"tax_id" => 1,
		            "tax_amount" => number_format((float)0, 2, '.', ''),
		            "taxable_amount" => number_format($vbt, 2, '.', ''),
		            "percent" => 0
				]);
				$valor_base_iva 		+= number_format($vbt, 2, '.', '');
			}

			if((float)$rdf[3] >= 0){
				$tax_ex_amt += $valor_cantidad;
			}else if((float)$rdf[4] > 0){
				$tax_ex_amt += $valor_cantidad;
			}
            

            //DESCUETO Y RECARGOS : Para descuentos charge_indicator = false, para recargos charge_indicator = true
			array_push($allowance_charges, [
				"charge_indicator" => false,
		        "allowance_charge_reason" => "Discount",
		        "amount" =>  $rdf[9] > 0 ? $rdf[9] : "0.00",
		        "base_amount" => $rdf[9] > 0 ? number_format($valor_cantidad, 2, '.', '')  : "0.00"
			]);
            

            if((float)$rdf[3] >= 0 || (float)$rdf[4] > 0){
				$invoice_dt["tax_totals"] = $tax_totals;
			}

			if( !empty($rdf[0])){
			  $description =  $rdf[0]; 
			} else{
			  $description =  ""; 
			}

			if( !empty($rdf[10])){
			  $description = $description." - ".$rdf[10]; 
			}

			$invoice_dt["unit_measure_id"] = !empty($rdf[8]) ? $rdf[8] : '70';;
			$invoice_dt["invoiced_quantity"] = $rdf[1];
			$invoice_dt["line_extension_amount"] = number_format($valor_cantidad, 2, '.', '');
			$invoice_dt["free_of_charge_indicator"] = false;
			$invoice_dt["allowance_charges"] = $allowance_charges;
			$invoice_dt["description"] = $description;
			$invoice_dt["code"] = $rdf[7];
			$invoice_dt["type_item_identification_id"] = 3;
			$invoice_dt["price_amount"] = number_format((float)$rdf[2], 2, '.', '');
			$invoice_dt["base_quantity"] = $rdf[1];

			array_push($invoice_lines, $invoice_dt);

			$valor += $valor_cantidad;

            //para saber la cantidad de detalles con IVA y con Impoconsumo	    
			 if((float)$rdf[3] > 0 ){		    
			      $cantiva++;			    
			 }

			 if((float)$rdf[4] > 0 ){		    
			      $cantimpo++;			    
			 }


		} //fin while

		//contabiliza la cantidad de detalles que tienen iva e impoconsumo
        $cant_iva = $cantiva;
        $cant_impo = $cantimpo;
        
        // ID DIAN TIPO DOCUMENTO
		$tipo = $rowF[12];
		switch ($tipo) {
			case '11':
				$tipo_documento = 1;
				break;
			case '12':
				$tipo_documento = 2;
				break;
		    case '13':
				$tipo_documento = 3;
				break;		
			case '21':
				$tipo_documento = 4;
				break;
			case '22':
				$tipo_documento = 5;
				break;
			case '31':
				$tipo_documento = 6;
				break;
			case '41':
				$tipo_documento = 7;
				break;
			case '42':
				$tipo_documento = 8;
				break;
			case '50':
				$tipo_documento = 9;
				break;
			case '91':
				$tipo_documento = 10;
				break;						
        }
		     
        //perfil tercero 1-JURÍDICO 2- NATURAL
	     $perfil = $rowF[16];

		//TIPO RÉGIMEN
	     $regimen = $rowF[17];
			
        if($regimen == 1){             
          $tiporeg = 1;
        } else{
          $tiporeg = 2;
        }    
        
        #FORMAS DE PAGO      
        $datetime1 = date_create( $rowF[13]); 
		$datetime2 = date_create($rowF[4]); 
		// calculates the difference between DateTime objects 
		$interval = date_diff($datetime1, $datetime2); 
		$dias = $interval->format('%a');

         if( $rowF[14] == 2){ // Crédito
	      	array_push($payment_forms, [
					'payment_form_id'=> 2,
			        'payment_method_id' => (int)$rowF[15],
			        'payment_due_date' =>  $rowF[13] ,
			        'duration_measure' => (int)$dias
		  ]);

	      }else{
            array_push($payment_forms, [
					'payment_form_id'=> 1,
			        'payment_method_id' => (int)$rowF[15],
			        'payment_due_date' =>  '' ,
			        'duration_measure' => 0  //Medida de duración en días
			]);
	      }     
       
        #Datos CLiente
	    array_push($customer_data, [
	    	'type_document_identification_id' => $tipo_documento,
	    	'type_organization_id' => $perfil,
      		'identification_number' => $rowF[7],
      		'type_regime_id' =>  $tiporeg,
      		'municipality_id' => $rowC[0],
      		'name' => $rowF[6],
      		'email' => $rowF[9], //'desarrollo03@sigiep.com',
      		'phone' => empty($rowF[10]) ? 0 : $rowF[10],
      		'address' => empty($rowF[11]) ? 'No tiene' : $rowF[11],
      		'merchant_registration' => 'No tiene'
      	]);
        

        //ENVÍO DE LA FACTURA CON COPIA A LA ENTIDAD QUE LA GENERA
        array_push($emailentidad, [
			'email'=> $rowE[0]
		]);
        //ENVÍO CON COPIA OCULTA A fe@sigiep.com, para tener un respaldo representación gráfica cuando un cliente solicite nuevamente la factura
        array_push($email, [
			'email'=> 'fe@sigiep.com'
		]);

         //Notas adicionales en la factura
        array_push($notas, [
			'text'=> $rowN[0] //fe@sigiep.com
		]);  


        $invoice_data['number']= $rowF[2];
        $invoice_data['sync']= true;
        $invoice_data['send']= true;
        $invoice_data['cc'] = $emailentidad;  
        $invoice_data['bcc'] = $email;  
        
        if(!empty($rowN[0])){
           $invoice_data['notes'] = $notas; 
        }

      	$invoice_data['type_document_id']= 1;   
      	$invoice_data['date']= $rowF[4]; // si se comenta esta línea de la fecha de expedición será la del envío de la factura a la DIAN por la API, si se deja toma la fecha de expedición de la factura
      	$invoice_data['due_date']= $rowF[13];     //fecha de vencimiento      	   	   	
      	$invoice_data['customer']= $customer_data[0];
        $invoice_data['payment_forms'] = $payment_forms;   

        #####################  ENCABEZADO TOTALES DE LA FACTURA  ####################################
		if($total_porcentaje_iva > 0){
			$iv_porc = $total_porcentaje_iva/$nro_items_iva	;
		} else {
			$iv_porc = 0;
		}
		if($total_procentaje_impo>0){
			$i_per = $total_procentaje_impo/$nro_items_impo	;
		} else {
			$i_per = 0;
		}
		if($valor_base_iva >0){
	        array_push($tax_ttt, [
					"tax_id" => 1,
			        "tax_amount" => number_format((float)$valor_iva_total, 2, '.', ''),
			        "taxable_amount" => number_format((float)$valor_base_iva, 2, '.', ''),
			        "percent" => $iv_porc
			]);
	        $invoice_data['tax_totals'] = $tax_ttt;
    	} else {
    		array_push($tax_ttt, [
					"tax_id" => 1,
			        "tax_amount" => number_format((float)0, 2, '.', ''),
			        "taxable_amount" => number_format((float)$valor_base_iva, 2, '.', ''),
			        "percent" => 0
			]);
	        $invoice_data['tax_totals'] = $tax_ttt;
    	}

	
        //impoconsumo
		if($valor_base_impoconsumo > 0){
				array_push($tax_ttt, [
					"tax_id" => 4,
		            "tax_amount" => number_format((float)$valor_impoconsumo_total, 2, '.', ''),
		            "taxable_amount" => number_format($valor_base_impoconsumo, 2, '.', ''),
		            "percent" => $i_per
			]);
					  
			$invoice_data['tax_totals'] = $tax_ttt;
		}


        $valor_base_total = $valor_base_iva+$valor_base_impoconsumo;
        $invoice_data['legal_monetary_totals'] = [
  			'line_extension_amount' => number_format((float)$valor_base_total, 2, '.', ''),
  			'tax_exclusive_amount' => number_format($valor_base_total, 2, '.', ''),
  			'tax_inclusive_amount' => number_format((float)$valor_total_factura, 2, '.', ''),
  			'allowance_total_amount' => 0.0,
  			'charge_total_amount' => 0.0,
  			'payable_amount' => number_format((float)$valor_total_factura, 2, '.', '')
      	];
	    $invoice_data['invoice_lines'] = $invoice_lines;   

        echo json_encode($invoice_data);
	break;
	
	case 'credit_note':
		# code...
	break;
	case 'debit_note':

	break;

	case 'updateBill':
		$data = $decoded_json->data;
		$xml  = $decoded_json->xml;
		$pdf  = $decoded_json->pdf;
		$zip_name = $decoded_json->zip_name;
		$issueDate = $hoy;
		$issueTime = $hora;
		$cufe = $decoded_json->cufe;

		echo $updateQuery = "UPDATE gp_factura fac SET zip_id = '$zip_name', issue_date = '$issueDate', issue_time = '$issueTime', cufe = '$cufe' WHERE fac.id_unico = '$factura'";
		$mysqli->query($updateQuery);	

		if($mysqli->affected_rows > 0){
			echo json_encode(['status' => 200]);
		}else{
			echo json_encode(['status' => 500]);
		}



    //guardar respuesta de la DIAN en txt
    $sqlD = "SELECT numero_factura
				FROM gp_factura 
		        WHERE id_unico = '$factura'";

	$resultD = $mysqli->query($sqlD);
	$rowD = mysqli_fetch_row($resultD);   
    //echo  $rowD[0];


    if (!file_exists('../documentos/facturacion_electronica/')) {
        mkdir('../documentos/facturacion_electronica/', 0777, true);
    }
   
     $lineas = "";                               //Variable para obtener las lineas del archivo txt
	 $txtName = $rowD[0]."_".$hoy.".json";       //se concaqueta el Número de la factura 
	 $sfile = '../documentos/facturacion_electronica/'.$txtName;	     
	 $lineas .= 'QR_DATA :'.$data."\r\n\n";
	 $lineas .= 'XML :'.$xml."\r\n\n";
	 $lineas .= 'PDF :'.$pdf;
     $fp=fopen($sfile,"w" );                 //Abrimos el archivo en modo de escritura
	 fwrite($fp,$lineas);                    //Escribimos el html del archivo
	 fclose($fp); 


	break;
}