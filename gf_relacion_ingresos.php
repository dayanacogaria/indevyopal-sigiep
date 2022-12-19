<?php 
###############################################################################################################################################################
# Fecha de creación : 	25/03/2017
# Creado por :			Jhon Numpaque
# Descripción:			Informe de relación de ingresos
#
###############################################################################################################################################################
# Agregamos el archivo de conexión
#
###############################################################################################################################################################
require 'Conexion/conexion.php';
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Capturamos la variable window enviada por get para indicar si abrimos el informe o la ventana con el modal
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$window = $_GET['window'];
switch ($window) {
	case 'form':		
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Formulario de presentación cuando la variable $window tiene como valor form
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require 'head.php';
		echo "<title>Informe de relación de ingresos</title>\n";
		echo "<link rel=\"stylesheet\" href=\"css/select2.css\">\n";
		echo "<link rel=\"stylesheet\" href=\"css/select2-bootstrap.min.css\"/>\n";
		echo "<link rel=\"stylesheet\" href=\"css/jquery-ui.css\">\n";
		echo "<script src=\"js/jquery-ui.js\"></script>\n";
		echo "<style type=\"text/css\" media=\"screen\">\n";
		echo "body{font-size:12px}\n";
		echo "</style>\n";
		echo "<script type=\"text/javascript\">\n";
        echo "/*Función para ejecutar el datapicker en en el campo fecha*/ \n";
        echo "$(function(){\n";
        echo "var fecha = new Date();\n";
        echo "var dia = fecha.getDate();\n";
        echo "var mes = fecha.getMonth() + 1;\n";
        echo "if(dia < 10){\n";
        echo "dia = \"0\" + dia;\n";
        echo "}\n";
        echo "if(mes < 10){\n";
        echo "mes = \"0\" + mes;\n";
        echo "}\n";
        echo "var fecAct = dia + \"/\" + mes + \"/\" + fecha.getFullYear();\n";
        echo "$.datepicker.regional['es'] = {\n";
        echo "closeText: 'Cerrar',\n";
        echo "prevText: 'Anterior',\n";
        echo "nextText: 'Siguiente',\n";
        echo "currentText: 'Hoy',\n";
        echo "monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],\n";
        echo "monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],\n";
        echo "dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],\n";
        echo "dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],\n";
        echo "dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],\n";
        echo "weekHeader: 'Sm',\n";
        echo "dateFormat: 'dd/mm/yy',\n";
        echo "firstDay: 1,\n";
        echo "isRTL: false,\n";
        echo "showMonthAfterYear: false,\n";
        echo "yearSuffix: ''\n";
        echo "};\n";
        echo "$.datepicker.setDefaults($.datepicker.regional['es']);\n";
        echo "$(\"#txtFechaI\").datepicker({changeMonth: true}).val(fecAct);\n";            
        echo "$(\"#txtFechaF\").datepicker({changeMonth: true}).val(fecAct);\n";            
        echo "});\n";
        echo "function reporteExcel(){\n";
   		echo "$('form').attr('action', 'gf_relacion_ingresos.php?window=excel');\n";
		echo "}\n";
		echo "function reportePdf(){\n";
    	echo "$('form').attr('action', 'gf_relacion_ingresos.php?window=pdf');\n";
		echo "}\n";
        echo "</script>\n";
		echo "</head>\n";
		echo "<body>\n";
		echo "<div class=\"container-fluid text-center\">\n";
		echo "<div class=\"row content\">\n";
		require 'menu.php';
		echo "<div class=\"col-sm-10 text-left\" style=\"margin-left: -16px;margin-top: -20px\">\n";
		echo "<h2 align=\"center\" class=\"tituloform\">Relación de Ingresos</h2>\n";
		echo "<div style=\"border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;\" class=\"client-form\">\n";
		echo "<form name=\"form\" class=\"form-horizontal\" method=\"POST\"  enctype=\"multipart/form-data\" action=\"\" target=\"_blank\">\n";
		echo "<p align=\"center\" style=\"margin-bottom: 15px; margin-top: 5px; margin-left: 30px; font-size: 100%\">Los campos marcados con <strong class=\"obligado\">*</strong> son obligatorios</p>\n";
		echo "<div class=\"form-group\">\n";
		echo "<label class=\"control-label col-sm-5\"><strong class=\"obligado\">*</strong>Fecha Inicial:</label>\n";
		echo "<input type=\"text\" class=\"form-control col-sm-1\" id=\"txtFechaI\" name=\"txtFechaI\" value=\"\" title=\"Fecha Inicial\" placeholder=\"Fecha Inicial\"/>\n";
		echo "</div>\n";
		echo "<div class=\"form-group\" style=\"margin-bottom:35px\">\n";
		echo "<label class=\"control-label col-sm-5\"><strong class=\"obligado\">*</strong>Fecha Final:</label>\n";
		echo "<input type=\"text\" class=\"form-control col-sm-1\" id=\"txtFechaF\" name=\"txtFechaF\" value=\"\" title=\"Fecha Final\" placeholder=\"Fecha Final\"/>\n";
		echo "</div>\n";
		echo "<div class=\"col-sm-10 form-group\" style=\"margin-top:-30px;margin-left:600px\" >\n";
		echo "<button onclick=\"reportePdf()\" class=\"btn sombra btn-primary\" title=\"Generar reporte PDF\"><i class=\"fa fa-file-pdf-o\" aria-hidden=\"true\"></i></button>\n";
		echo "<button style=\"margin-left:10px;\" onclick=\"reporteExcel()\" class=\"btn sombra btn-primary\" title=\"Generar reporte Excel\"><i class=\"fa fa-file-excel-o\" aria-hidden=\"true\"></i></button>\n";
		echo "</div>";
		echo "</form>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "<div>\n";
		require 'footer.php';
		echo "</div>\n";
		echo "</body>\n";
		echo "<html>\n";		
		break;
	
	case 'excel':
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Impresión de formato cuando la variable $window tiene como valor excel
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		header("Content-type: application/vnd.ms-excel");									//Header para especificar el tipo de archivo
		header("Content-Disposition: attachment; filename=Relación de ingresos.xls");		//Header para indicar la descarga y el nombre del archivo
		@session_start();																	//Iniciamos session
		ini_set('max_execution_time', 0);													//Le quitamos el limite de tiempo
		$fechaini = explode("/",$_POST['txtFechaI']);										//Capturamos la fecha inicial, la dividimos usando /
		$fechaini = "$fechaini[2]-$fechaini[1]-$fechaini[0]";								//Armamos la fecha a la inversa para que quede al formato de mysql
		$fechafin = explode("/",$_POST['txtFechaF']);										//Capturamos la fecha final, la dividimos usando /
		$fechafin = "$fechafin[2]-$fechafin[1]-$fechafin[0]";								//Armamos la fecha a la inversa para que quede al formato de mysql
		$sumValor = 0;																		//Acumulable para suma de valores
		$comprobantes = "";																	//String para capturar los id de los comprobantes
		$sumContra = 0;																		//Acumulable de contrapartidas
		$compania = $_SESSION['compania'];													//Capturamos la variable de compañia		
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Imprimimos html de la tabla de cuentas de bancos y caja
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
		echo "<head>";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
		echo "<title>Informe de Relación de Ingresos</title>";
		echo "</head>";
		echo "<body>";			
		echo "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">";		
		echo "<thead>";
		echo "<tr>";
		echo "<th colspan=\"3\"><CENTER><strong>INFORME DE RELACIÓN DE INGRESOS</strong></CENTER></th>";
		echo "</tr>";
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Consulta de compañia y logo, y nit
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$sqlCom = "	SELECT 		ter.razonsocial,
								ti.nombre,
								ter.numeroidentificacion
					FROM 		gf_tercero ter
					LEFT JOIN 	gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
					WHERE		ter.id_unico = $compania";
		$resultT = $mysqli->query($sqlCom);
		$rowT = mysqli_fetch_row($resultT);
		echo "<tr>";
		echo "<th colspan=\"3\" align=\"center\"><strong>".$rowT[0]."<br/>".strtoupper($rowT[1])." ".$rowT[2]."</strong></th>";
		echo "</tr>";
		//Titulo del informe
		echo "<tr>";
		echo "<td><strong>CODIGO</strong></td>";
		echo "<td><strong>NOMBRE</strong></td>";
		echo "<td><strong>VALOR</strong></td>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Consulta para obtener los comprobantes
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$sqlC = "SELECT 									
							cnt.id_unico id_cnt
				FROM 		gf_detalle_comprobante dtc
				LEFT JOIN 	gf_cuenta cta 			ON cta.id_unico = dtc.cuenta
				LEFT JOIN 	gf_comprobante_cnt cnt 	ON cnt.id_unico = dtc.comprobante
				LEFT JOIN 	gf_tipo_comprobante tpc ON tpc.id_unico = cnt.tipocomprobante
				WHERE 		cta.clasecuenta IN (11,12)
				AND 		cnt.tipocomprobante != 1
				AND 		cnt.fecha >= '$fechaini'
				AND 		cnt.fecha <= '$fechafin'
				AND			tpc.clasecontable = 9				
				";
		$resultC = $mysqli->query($sqlC);
		while ($rowC = mysqli_fetch_row($resultC)) {
			$comprobantes .= $rowC[0].',';
		}	
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Consulta para obtener los comprobantes
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$sql = "SELECT 		cta.codi_cuenta,
							cta.nombre,
							SUM(dtc.valor),
							cnt.id_unico id_cnt
				FROM 		gf_detalle_comprobante dtc
				LEFT JOIN 	gf_cuenta cta 			ON cta.id_unico = dtc.cuenta
				LEFT JOIN 	gf_comprobante_cnt cnt 	ON cnt.id_unico = dtc.comprobante
				LEFT JOIN 	gf_tipo_comprobante tpc ON tpc.id_unico = cnt.tipocomprobante
				WHERE 		cta.clasecuenta IN (11,12)
				AND 		cnt.tipocomprobante != 1
				AND 		cnt.fecha >= '$fechaini'
				AND 		cnt.fecha <= '$fechafin'
				AND			tpc.clasecontable = 9
				GROUP BY 	cta.id_unico";
		$result = $mysqli->query($sql);	
		$cantidad = mysqli_num_rows($result);	
		while ($row = mysqli_fetch_row($result)) {											//Imprimimos los valores devueltos por la consulta
			$sumValor += $row[2];															//Captura de valor para suma como acumulable
			$comprobantes .= $row[3].',';													//Captura de comprobantes
			echo "<tr>";
			echo "<td>".$row[0]."</td>";
			echo "<td>".utf8_encode(ucwords(mb_strtolower($row[1])))."</td>";
			echo "<td>".number_format($row[2],2,',','.')."</td>";			
			echo "</tr>";
		}
		echo "</tbody>";
		echo "<tfoot>";
		echo "<tr>";
		echo "<td colspan=\"2\"><strong>TOTAL: </strong></td>";
		echo "<td><strong>".number_format($sumValor,2,',','.')."</strong></td>";			//Impresión de valor acumulado
		echo "</tr>";
		echo "</tfoot>";
		echo "</table>";
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Imprimimos html de la tabla de contrapartidas
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$comprobantes = substr($comprobantes,0,strlen($comprobantes)-1);					//Quitamos la ultima coma que queda en el string
		echo "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">";		//Tabla para contrapartidas
		echo "<thead>";
		echo "<tr>";
		echo "<th colspan=\"3\">CONTRAPARTIDAS</th>";
		echo "</tr>";
		echo "<tr>";
		echo "<td><strong>CODIGO</strong></td>";
		echo "<td><strong>NOMBRE</strong></td>";
		echo "<td><strong>VALOR</strong></td>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";	
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Consulta para imprimir los comprobantes
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$sql1 = "SELECT 	cta.codi_cuenta,
							cta.nombre,
							SUM(dtc.valor)
				FROM 		gf_detalle_comprobante dtc
				LEFT JOIN 	gf_cuenta cta 			ON cta.id_unico = dtc.cuenta
				LEFT JOIN 	gf_comprobante_cnt cnt 	ON cnt.id_unico = dtc.comprobante
				WHERE 		cta.clasecuenta <> 11
                AND 		cta.clasecuenta <> 12
				AND 		cnt.tipocomprobante != 1
				AND 		cnt.fecha >= '$fechaini'
				AND 		cnt.fecha <= '$fechafin'
                AND 		cnt.id_unico IN ($comprobantes)
                GROUP BY 	cta.id_unico ASC";;
        $result1 = $mysqli->query($sql1);
        if($cantidad > 0){
	        while ($row1 = mysqli_fetch_row($result1)) {
	        	$sumContra += $row1[2]>0?$row1[2]:$row1[2]*-1;									//Captura de valor para suma de acumulable
	            echo "<tr>";
	            echo "<td>".$row1[0]."</td>";
				echo "<td>".utf8_encode(ucwords(mb_strtolower($row1[1])))."</td>";
				echo "<td>".number_format($row1[2]>0?$row1[2]:$row1[2]*-1,2,',','.')."</td>";
	            echo "</tr>";
	        }        	
        }
		echo "</tbody>";
		echo "<tfoot>";
		echo "<tr>";
		echo "<td colspan=\"2\"><strong>TOTAL: </strong></td>";
		echo "<td><strong>".number_format($sumContra,2,',','.')."</strong></td>";			//Impresión de valor acumulado
		echo "</tr>";
		echo "</tfoot>";	
		echo "</table>";		
		echo "</body>";		
		echo "</html>";
		break;
	case 'pdf':
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Impresión de formato cuando la variable $window tiene como valor pdf
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		header("Content-Type: text/html;charset=utf-8");									//Conversión de la cabeza a tipo de contenido
		ini_set('max_execution_time', 0);													//Le quitamos el limite de tiempo
		@session_start();																	//Iniciamos session
		ob_start();																			//Inicializamos el objeto
		$fechaInicial = $_POST['txtFechaI'];
		$fechaini = explode("/",$_POST['txtFechaI']);										//Capturamos la fecha inicial, la dividimos usando /
		$fechaini = "$fechaini[2]-$fechaini[1]-$fechaini[0]";								//Armamos la fecha a la inversa para que quede al formato de mysql
		$fechafin = explode("/",$_POST['txtFechaF']);										//Capturamos la fecha final, la dividimos usando /
		$fechafin = "$fechafin[2]-$fechafin[1]-$fechafin[0]";								//Armamos la fecha a la inversa para que quede al formato de mysql
		$fechaFinal = $_POST['txtFechaF'];
		$sumValor = 0;																		//Acumulable para suma de valores
		$comprobantes = "";																	//String para capturar los id de los comprobantes
		$sumContra = 0;																		//Acumulable de contrapartidas
		$compania = $_SESSION['compania'];													//Capturamos la variable de compañia		
		require'fpdf/fpdf.php';																//Traemos el archvio fpdf
		class PDF_MC_Table extends FPDF{													//Clase MC_TABLE con herencia pfdf
			var $widths;
			var $aligns;
			function SetWidths($w){
				//Set the array of column widths
				$this->widths=$w;
			}
			function SetAligns($a){
				//Set the array of column alignments
				$this->aligns=$a;
			}
			function fill($f){
				//juego de arreglos de relleno
				$this->fill=$f;
			}
			function Row($data){
				//Calcula el alto de l afila
				$nb=0;
				for($i=0;$i<count($data);$i++)
				$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
				$h=6*$nb;
				//Realiza salto de pagina si es necesario
				$this->CheckPageBreak($h);
				//Pinta las celdas de la fila
				for($i=0;$i<count($data);$i++){
					$w=$this->widths[$i];
					$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
					//Guarda la posicion actual
					$x=$this->GetX();
					$y=$this->GetY();
					//Pinta el border
					$this->Rect($x,$y,$w,$h,$style);
					//Imprime el texto
					$this->MultiCell($w,4,$data[$i],'LTR',$a,$fill);
					//Put the position to the right of the cell
					$this->SetXY($x+$w,$y);
				}
				//Hace salto de la pagina
				$this->Ln($h-5);
			}
			function CheckPageBreak($h){
				//If the height h would cause an overflow, add a new page immediately
				if($this->GetY()+$h>$this->PageBreakTrigger)
					$this->AddPage($this->CurOrientation);
			}
			function NbLines($w,$txt){
				//Computes the number of lines a MultiCell of width w will take
				$cw=&$this->CurrentFont['cw'];
				if($w==0)
					$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				$s=str_replace('\r','',$txt);
				$nb=strlen($s);
				if($nb>0 and $s[$nb-1]=='\n')
					$nb–;
				$sep=-1;
				$i=0;
				$j=0;
				$l=0;
				$nl=1;
				while($i<$nb){
					$c=$s[$i];
					if($c=='\n'){
						$i++;
						$sep=-1;
						$j=$i;
						$l=0;
						$nl++;
						continue;
					}
					if($c=='')
						$sep=$i;
					$l+=$cw[$c];
					if($l>$wmax){
						if($sep==-1){
							if($i==$j)
								$i++;
						}else
							$i=$sep+1;
						$sep=-1;
						$j=$i;
						$l=0;
						$nl++;
					}else
						$i++;
					}
				return $nl;
			}
			
			#Funcón cabeza de la página
			function header(){		
			}	
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Declaración del objeto mc_table
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$pdf = new PDF_MC_Table('P','mm','Letter');											//Creación del objeto pdf
		$nb  = $pdf->AliasNbPages();														//Objeto de número de pagina
		$pdf->AddPage();																	//Agregar página
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Consulta de compañia y logo, y nit
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$sqlCom = "	SELECT 		ter.razonsocial,
								ti.nombre,
								ter.numeroidentificacion,
								ter.ruta_logo,
								dir.direccion,
								tel.valor
					FROM 		gf_tercero ter
					LEFT JOIN 	gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
					LEFT JOIN   gf_direccion dir 			ON	dir.tercero = ter.id_unico
					LEFT JOIN 	gf_telefono  tel 			ON 	tel.tercero = ter.id_unico
					WHERE		ter.id_unico = $compania";
		$resultT = $mysqli->query($sqlCom);
		$rowT = mysqli_fetch_row($resultT);
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Asignamos los valores
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$razonsocial = $rowT[0];
		$tipoidentificacion = $rowT[1];
		$numeroidentificacion = $rowT[2];
		$ruta_logo = $rowT[3];
		$direccion = $rowT[4];
		$telefono = $rowT[5];
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Validamos e imprimimos el logo
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if($ruta_logo !== ''){
			$pdf->Image($ruta_logo,10,8,15);
		}
		$pdf->SetX(35);
		$pdf->SetFont('Arial','B',12);
		$pdf->MultiCell(155,5,$razonsocial,0,'C');
		$pdf->SetX(35);
		$pdf->Cell(155,5,strtoupper($tipoidentificacion).':'.$numeroidentificacion,0,0,'C');
		$pdf->Ln(5);
		$pdf->SetX(35);
		$pdf->Cell(155,5,$direccion.PHP_EOL.'Tel:'.PHP_EOL.$telefono,0,0,'C');
		$pdf->Ln(5);
		$pdf->SetX(35);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(155,5,"Fecha Inicial :".PHP_EOL.$fechaInicial.PHP_EOL."y".PHP_EOL."Fecha Final :".PHP_EOL.$fechaFinal,0,0,'C');
		$pdf->Ln(10);
		//$pdf->Cell(190,5,utf8_decode('INFORME DE RELACIÓN DE INGRESOS'),1,0,'C');			//Titulo del informe
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Nombre de columnas
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(50,5,'CODIGO',1,0,'L');
		$pdf->Cell(80,5,'NOMBRE',1,0,'L');
		$pdf->Cell(60,5,'VALOR',1,0,'L');
		$pdf->Ln(5);
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Consulta para obtener los comprobantes
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$sqlC = "SELECT 									
							cnt.id_unico id_cnt
				FROM 		gf_detalle_comprobante dtc
				LEFT JOIN 	gf_cuenta cta 			ON cta.id_unico = dtc.cuenta
				LEFT JOIN 	gf_comprobante_cnt cnt 	ON cnt.id_unico = dtc.comprobante
				LEFT JOIN 	gf_tipo_comprobante tpc ON tpc.id_unico = cnt.tipocomprobante
				WHERE 		cta.clasecuenta IN (11,12)
				AND 		cnt.tipocomprobante != 1
				AND 		cnt.fecha >= '$fechaini'
				AND 		cnt.fecha <= '$fechafin'
				AND			tpc.clasecontable = 9				
				";
		$resultC = $mysqli->query($sqlC);
		while ($rowC = mysqli_fetch_row($resultC)) {
			$comprobantes .= $rowC[0].',';
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Consulta de comprobantes de relación de ingresos
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$sql = "SELECT 		cta.codi_cuenta,
							cta.nombre,
							SUM(dtc.valor),
							cnt.id_unico id_cnt
				FROM 		gf_detalle_comprobante dtc
				LEFT JOIN 	gf_cuenta cta 			ON cta.id_unico = dtc.cuenta
				LEFT JOIN 	gf_comprobante_cnt cnt 	ON cnt.id_unico = dtc.comprobante
				LEFT JOIN 	gf_tipo_comprobante tpc ON tpc.id_unico = cnt.tipocomprobante
				WHERE 		cta.clasecuenta IN (11,12)
				AND 		cnt.tipocomprobante != 1
				AND 		cnt.fecha >= '$fechaini'
				AND 		cnt.fecha <= '$fechafin'
				AND			tpc.clasecontable = 9
				GROUP BY 	cta.id_unico";
		$result = $mysqli->query($sql);
		$cantidad = mysqli_num_rows($result);
		while ($row = mysqli_fetch_row($result)) {											//Imprimimos los valores devueltos por la consulta			
			$sumValor += $row[2];															//Captura de valor para suma como acumulable			
			$pdf->SetFont('Arial','',10);													//Fuente
			$pdf->SetWidths(array(50,80,60));												//Anchos
			$pdf->SetAligns(array('R','L','R'));											//Alineaciones
			$pdf->Row(array($row[0],ucwords(strtolower($row[1])),number_format($row[2],2,',','.')));
			$pdf->Ln(5);			
		}			
		$pdf->SetFont('Arial','B',10);														//Fuente
		$pdf->Cell(130,5,'TOTAL',1,0,'L');													
		$pdf->Cell(60,5,number_format($sumValor,2,',','.'),1,0,'R');						//Valor total
		$pdf->Ln(5);
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Subinforme de contrapartidas
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$comprobantes = substr($comprobantes,0,strlen($comprobantes)-1);					//Quitamos la ultima coma que queda en el string
		$pdf->Cell(190,5,utf8_decode('CONTRAPARTIDAS'),1,0,'C');							//Titulo
		$pdf->Ln(5);
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Nombre de columnas
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$pdf->Cell(50,5,'CODIGO',1,0,'L');
		$pdf->Cell(80,5,'NOMBRE',1,0,'L');
		$pdf->Cell(60,5,'VALOR',1,0,'L');
		$pdf->Ln(5);
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Consulta de contrapartidas de relación de ingresos
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$sql1 = "SELECT 	cta.codi_cuenta,
							cta.nombre,
							SUM(dtc.valor)
				FROM 		gf_detalle_comprobante dtc
				LEFT JOIN 	gf_cuenta cta 			ON cta.id_unico = dtc.cuenta
				LEFT JOIN 	gf_comprobante_cnt cnt 	ON cnt.id_unico = dtc.comprobante
				WHERE 		cta.clasecuenta <> 11
                AND 		cta.clasecuenta <> 12
				AND 		cnt.tipocomprobante != 1
				AND 		cnt.fecha >= '$fechaini'
				AND 		cnt.fecha <= '$fechafin'
                AND 		cnt.id_unico IN ($comprobantes)
                GROUP BY 	cta.id_unico ASC";
        $result1 = $mysqli->query($sql1);
        if($cantidad > 0) {
	        while ($row1 = mysqli_fetch_row($result1)) {
	        	$sumContra += $row1[2]>0?$row1[2]:$row1[2]*-1;									//Captura de valor para suma de acumulable  
	        	$pdf->SetFont('Arial','',10);													//Fuente
				$pdf->SetWidths(array(50,80,60));												//Anchos
				$pdf->SetAligns(array('R','L','R'));											//Alineaciones
				$pdf->Row(array($row1[0],ucwords(strtolower($row1[1])),number_format($row1[2]>0?$row1[2]:$row1[2]*-1,2,',','.')));          
				$pdf->Ln(5);
	        }        	
        }
        $pdf->SetFont('Arial','B',10);														//Fuente
		$pdf->Cell(130,5,'TOTAL',1,0,'L');													
		$pdf->Cell(60,5,number_format($sumContra,2,',','.'),1,0,'R');						//Valor total
		$pdf->Ln(5);
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Salida y cierre de infrome
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		while (ob_get_length()) {	
		  ob_end_clean();
		}		
		$pdf->Output(0,'Informe_Relacion_de_ingresos.pdf',0);
		break;
}
 ?>
