<?php 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Multtiformulario para generar lote de causación de ingresos,formulario para el envio para generar la descripción
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Creado por: Jhon Numpaque
// Fecha: 12/04/2017
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Archivos adjuntos
require ('Conexion/conexion.php');					//Archivo de conexion
ini_set('max_execution_time', 0);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Capturamos el parametro enviado por GET, el cual siempre sera enviado desde el menu
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$formularios = $_GET['action'];
switch ($formularios) {
	case 'lote_ingresos_causacion':
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Formulario para envio de valores, al caso generar lotes de comprobantes de recaudo de cuasacion
		//
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require ('head.php');
                $compania = $_SESSION['compania'];
		echo "<link rel=\"stylesheet\" href=\"css/jquery-ui.css\">\n";
        echo "<script src=\"js/jquery-ui.js\"></script>\n";
        echo "<!-- select2 -->\n";
        echo "<link rel=\"stylesheet\" href=\"css/select2.css\">\n";
        echo "<link rel=\"stylesheet\" href=\"css/select2-bootstrap.min.css\"/>\n";
		echo "<title>Generar Lote de Causación Ingresos</title>\n";
		echo "<script type=\"text/javascript\" src=\"js/select2.js\"></script>\n";
		echo "<script type=\"text/javascript\">\n";
        echo "/*Función para ejecutar el datapicker en en el campo fecha*/\n";
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
        echo "var fecAct = dia + \"/\" + mes + \"/\" + fecha.getFullYear();";
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
        echo "};";
        echo "$.datepicker.setDefaults($.datepicker.regional['es']);\n";
        echo "$(\"#txtFechaI\").datepicker({changeMonth: true}).val(fecAct);\n";
        echo "$(\"#txtFechaF\").datepicker({changeMonth: true}).val(fecAct);\n";
        echo "});\n";        
        echo "</script>\n";
		echo "</head>\n";
		echo "<body>\n";
		echo "<div class=\"container-fluid\">\n";
		echo "<div class=\"row content\">\n";
		require ('menu.php');
		echo "<div class=\"col-sm-10\" >\n";
		echo "<h2 id=\"forma-titulo3\" style=\"margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:0px\" align=\"center\">GENERAR CAUSACIONES POR LOTE DE INGRESOS</h2>\n";
		echo "<div style=\"border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-top: -15px\" class=\"client-form\">\n";
		echo "<form name=\"form\" id=\"form\" class=\"form-horizontal\" method=\"POST\"  enctype=\"multipart/form-data\"  action=\"javaScript:generar_causacion()\">\n";
		echo "<p align=\"center\" style=\"margin-bottom: 15px; margin-top:5px; margin-left: 30px; font-size: 80%\"></p>\n";
		echo "<div class=\"form-group\">\n";
		echo "<label for=\"sltTipoComI\" class=\"col-sm-5 control-label\">\n";
		echo "<strong class=\"obligado\">*</strong>Tipo Comprobante:\n";
		echo "</label>\n";
		echo "<select name=\"sltTipoComI\" id=\"sltTipoComI\" title=\"Seleccione Tipo Comprobante\" style=\"width: 400px;\" class=\"col-sm-1 form-control select2\" required>\n";
		echo "<option value=''>Tipo Comprobante Inicial</option>\n";
		$sqlTI = "SELECT 	ti.id_unico,
	                       	UPPER(ti.nombre),
	                       	ti.sigla
	            FROM 		gf_tipo_comprobante ti 
	            LEFT JOIN 	gf_clase_contable cc 
	            ON 			ti.clasecontable = cc.id_unico
	            WHERE 		ti.clasecontable = 9 AND ti.compania = $compania 
	            ORDER BY 	ti.id_unico ASC";
	    $resultTI = $mysqli->query($sqlTI);
	    while($rowTI = mysqli_fetch_row($resultTI)){
	    	echo "<option value=\"$rowTI[0]\">".$rowTI[1]." ".$rowTI[2]."</option>\n";
	    }
		echo "</select>\n";
		echo "</div>\n";		
		echo "<div class=\"form-group\" style=\"margin-top:-5px\">\n";
		echo "<label for=\"sltTipoComF\" class=\"col-sm-5 control-label\">\n";
		echo "<strong class=\"obligado\">*</strong>Fecha Inicial:\n";
		echo "</label>\n";
		echo "<input name=\"txtFechaI\" id=\"txtFechaI\" title=\"Seleccione Fecha Inicial\" style=\"width: 400px;\" class=\"col-sm-1 form-control\" placeholder=\"Fecha Inicial\" required>\n";		
		echo "</div>\n";
		echo "<div class=\"form-group\" style=\"margin-top:-5px\">\n";
		echo "<label for=\"sltTipoComF\" class=\"col-sm-5 control-label\">\n";
		echo "<strong class=\"obligado\">*</strong>Fecha Final:\n";
		echo "</label>\n";
		echo "<input name=\"txtFechaF\" id=\"txtFechaF\" title=\"Seleccione Fecha Final\" style=\"width: 400px;\" class=\"col-sm-1 form-control\" placeholder=\"Fecha Final\" required>\n";		
		echo "</div>\n";
		echo "<div class=\"form-group\">\n";
		echo "<labeGF_CHEQUESl class=\"col-sm-9\"></labeGF_CHEQUESl>\n";
		echo "<button type=\"submit\" class=\"btn btn-primary\" style=\"box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;\">\n";
		echo "<li class=\"glyphicon glyphicon-play\"></li>\n";
		echo "</button>\n";
		echo "</div>\n";
		echo "</form>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</body>\n";
		echo "<div>\n";
		require ('footer.php');
		echo "</div>\n";
		echo "<style>\n";
		echo "body{font-size:12px}\n";
		echo "</style>\n";
		echo "<script>\n";
		echo "/*Implementamos en una clase select2, la funcion para combo dinamico*/\n";
        echo "$(\".select2\").select2();\n";        
		echo "</script>\n";
		echo "</html>";
		break;
	case 'generar_lote_ingresos':
		session_start();
		require ('funciones/funciones_consulta.php');			//Archivo de funciones
		echo "<html>\n";
		echo "<head>\n";
		echo "<meta charset=\"utf-8\">\n";
		echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
		echo "<link rel=\"stylesheet\" href=\"css/bootstrap.min.css\">\n";
		echo "<link rel=\"stylesheet\" href=\"css/style.css\">\n";		
		echo "<script src=\"js/jquery.min.js\"></script>\n";
		echo "<link rel=\"stylesheet\" href=\"css/jquery-ui.css\" type=\"text/css\" media=\"screen\" title=\"default\" />\n";
		echo "<script type=\"text/javascript\" language=\"javascript\" src=\"js/jquery-1.10.2.js\"></script>\n";
		echo "</head>\n";
		echo "<body>\n";
		echo "</body>\n";
		echo "</html>\n";
		echo "<link rel=\"stylesheet\" href=\"css/bootstrap-theme.min.css\">";
		echo "<script src=\"js/bootstrap.min.js\"></script>";
		echo "<div class=\"modal fade\" id=\"myModal1\" role=\"dialog\" align=\"center\" >\n";
		echo "<div class=\"modal-dialog\">\n";
		echo "<div class=\"modal-content\">\n";
		echo "<div id=\"forma-modal\" class=\"modal-header\">\n";
		echo "<h4 class=\"modal-title\" style=\"font-size: 24; padding: 3px;\">Información</h4>\n";
		echo "</div>\n";
		echo "<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
		echo "<p>Información guardada correctamente.</p>\n";
		echo "</div>\n";
		echo "<div id=\"forma-modal\" class=\"modal-footer\">\n";
		echo "<button type=\"button\" id=\"ver1\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\" >Aceptar</button>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Declaración de variables
		//
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$tp = array();									//Array para capturar los tipo de comprobantes con clase homologable		
		$x = 0;											//Variable de conteo de registro de comprobantes
		$y = 0;											//Variable para capturar la cantidad de comprobantes
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Proceso para generar lotes de comprobantes de causacion de ingresos
		//
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$tipoI = $_POST['sltTipoComI']; 				//Tipo de comprobante inicial		
		$fechaI = $_POST['txtFechaI'];					//Fecha Inicial
		$fechaF = $_POST['txtFechaF'];					//Fecha Final
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Formateamos las fechas recibidas para usarlas en la consulta
		//
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Fehca Inicial
		$fechaI = explode("/",$fechaI);					//Dividimos la fecha Inicial usando /
		$fechaI = "'$fechaI[2]-$fechaI[1]-$fechaI[0]'";	//Formateamos la fecha Y-m-d
		//Fecha Final
		$fechaF = explode("/",$fechaF);					//Dividimos la fecha Final usando /
		$fechaF = "'$fechaF[2]-$fechaF[1]-$fechaF[0]'";	//Formateamos la fecha Y-m-d
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Recorremos todos los tipos de comprobantes, que tienen clase homologable causacion de ingresos, que los tipos son de clase contable 9, entre un rango 
		// de tipos comprobantes y un rango de fechas de comprobante contables a los que se relacionan
		//
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$sqlComp = "SELECT cnt.id_unico, cnt.fecha  FROM gf_comprobante_cnt cnt 						
					WHERE  cnt.tipocomprobante = $tipoI
					AND    (cnt.fecha BETWEEN $fechaI AND $fechaF)";
        $resultComp = $mysqli->query($sqlComp);
        while ($rowComp = mysqli_fetch_row($resultComp)) {
            $rowComp[0].';'.$rowComp[1].'<br/>';
            $c = causacion_ingresos($rowComp[0]);           
            $x++; //Contamos todos los comprobantes
        }        
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Contamos los comprobantes relacionados al tipo, (cambiar metodo de validación)
		//
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$sqlC = "SELECT DISTINCT 	COUNT(cnt.id_unico)
				FROM				gf_tipo_comprobante tpc
				LEFT JOIN 			gf_tipo_comprobante tch
				ON 					tpc.tipo_comp_hom 	= tch.id_unico
				LEFT JOIN 			gf_comprobante_cnt cnt 
				ON 					cnt.tipocomprobante = tpc.id_unico								
				AND 				(tpc.id_unico = $tipoI)
				AND 				(cnt.fecha BETWEEN $fechaI AND $fechaF) ";
		$resultC = $mysqli->query($sqlC);
		$cantidad = mysqli_num_rows($resultC);
		if($cantidad > 0){
			$rowC = mysqli_fetch_row($resultC);
			$y = $rowC[0];
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Validamos que ambas variables tengan el mismo valor para cerrar la ventana
		//
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if($x <= $y){
			echo "<script lenguaje=\"JavaScript\">\n";
			echo "$('#myModal1').modal('show');\n";
			echo "$('#ver1').click(function() {window.close();});\n";
			echo "</script>\n";
		}		
		break;
	case 'lote_retenciones';
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Formulario para envio de valores, al caso generar lotes de comprobantes de recaudo de retenciones
		//
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require ('head.php');		
		echo "<link rel=\"stylesheet\" href=\"css/jquery-ui.css\">\n";
        echo "<script src=\"js/jquery-ui.js\"></script>\n";
        echo "<!-- select2 -->\n";
        echo "<link rel=\"stylesheet\" href=\"css/select2.css\">\n";
        echo "<link rel=\"stylesheet\" href=\"css/select2-bootstrap.min.css\"/>\n";
		echo "<title>Generar Lote de Comprobantes de Ingreso de Retención</title>\n";
		echo "<script type=\"text/javascript\" src=\"js/select2.js\"></script>\n";
		echo "<script type=\"text/javascript\">\n";
        echo "/*Función para ejecutar el datapicker en en el campo fecha*/\n";
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
        echo "var fecAct = dia + \"/\" + mes + \"/\" + fecha.getFullYear();";
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
        echo "};";
        echo "$.datepicker.setDefaults($.datepicker.regional['es']);\n";
        echo "$(\"#txtFechaI\").datepicker({changeMonth: true}).val(fecAct);\n";
        echo "$(\"#txtFechaF\").datepicker({changeMonth: true}).val(fecAct);\n";
        echo "});\n";        
        echo "</script>\n";
		echo "</head>\n";
		echo "<body>\n";
		echo "<div class=\"container-fluid\">\n";
		echo "<div class=\"row content\">\n";
		require ('menu.php');
		echo "<div class=\"col-sm-10\" >\n";
		echo "<h2 id=\"forma-titulo3\" style=\"margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:0px\" align=\"center\">GENERAR LOTE DE COMPROBANTES DE INGRESO DE RETENCION</h2>\n";
		echo "<div style=\"border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-top: -15px\" class=\"client-form\">\n";
		echo "<form name=\"form\" id=\"form\" class=\"form-horizontal\" method=\"POST\"  enctype=\"multipart/form-data\" target=\"_blank\" action=\"registrar_lote_causacion_ingresos.php?action=generar_retenciones\">\n";
		echo "<p align=\"center\" style=\"margin-bottom: 15px; margin-top:5px; margin-left: 30px; font-size: 80%\"></p>\n";			
		echo "<div class=\"form-group\" style=\"margin-top:-5px\">\n";
		echo "<label for=\"sltTipoComF\" class=\"col-sm-5 control-label\">\n";
		echo "<strong class=\"obligado\">*</strong>Fecha Inicial:\n";
		echo "</label>\n";
		echo "<input name=\"txtFechaI\" id=\"txtFechaI\" title=\"Seleccione Fecha Inicial\" style=\"width: 400px;\" class=\"col-sm-1 form-control\" placeholder=\"Fecha Inicial\" required>\n";		
		echo "</div>\n";
		echo "<div class=\"form-group\" style=\"margin-top:-5px\">\n";
		echo "<label for=\"sltTipoComF\" class=\"col-sm-5 control-label\">\n";
		echo "<strong class=\"obligado\">*</strong>Fecha Final:\n";
		echo "</label>\n";
		echo "<input name=\"txtFechaF\" id=\"txtFechaF\" title=\"Seleccione Fecha Final\" style=\"width: 400px;\" class=\"col-sm-1 form-control\" placeholder=\"Fecha Final\" required>\n";		
		echo "</div>\n";
		echo "<div class=\"form-group\">\n";
		echo "<label class=\"col-sm-9\"></label>";
		echo "<button type=\"submit\" class=\"btn btn-primary\" style=\"box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;\">\n";
		echo "<li class=\"glyphicon glyphicon-play\"></li>\n";
		echo "</button>\n";
		echo "</div>\n";
		echo "</form>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</body>\n";
		echo "<div>\n";
		require ('footer.php');
		echo "</div>\n";
		echo "<style>\n";
		echo "body{font-size:12px}\n";
		echo "</style>\n";
		echo "<script>\n";
		echo "/*Implementamos en una clase select2, la funcion para combo dinamico*/\n";
        echo "$(\".select2\").select2();\n";
		echo "</script>\n";
		echo "</html>";
		break;
	case 'generar_retenciones':
		session_start();
		require ('funciones/funciones_consulta.php');			//Archivo de funciones	
		echo "<html>\n";
		echo "<head>\n";
		echo "<meta charset=\"utf-8\">\n";
		echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
		echo "<link rel=\"stylesheet\" href=\"css/bootstrap.min.css\">\n";
		echo "<link rel=\"stylesheet\" href=\"css/style.css\">\n";		
		echo "<script src=\"js/jquery.min.js\"></script>\n";
		echo "<link rel=\"stylesheet\" href=\"css/jquery-ui.css\" type=\"text/css\" media=\"screen\" title=\"default\" />\n";
		echo "<script type=\"text/javascript\" language=\"javascript\" src=\"js/jquery-1.10.2.js\"></script>\n";
		echo "</head>\n";
		echo "<body>\n";
		echo "</body>\n";
		echo "</html>\n";
		echo "<link rel=\"stylesheet\" href=\"css/bootstrap-theme.min.css\">";
		echo "<script src=\"js/bootstrap.min.js\"></script>";
		echo "<div class=\"modal fade\" id=\"myModal1\" role=\"dialog\" align=\"center\" >\n";
		echo "<div class=\"modal-dialog\">\n";
		echo "<div class=\"modal-content\">\n";
		echo "<div id=\"forma-modal\" class=\"modal-header\">\n";
		echo "<h4 class=\"modal-title\" style=\"font-size: 24; padding: 3px;\">Información</h4>\n";
		echo "</div>\n";
		echo "<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
		echo "<p>Información guardada correctamente.</p>\n";
		echo "</div>\n";
		echo "<div id=\"forma-modal\" class=\"modal-footer\">\n";
		echo "<button type=\"button\" id=\"ver1\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\" >Aceptar</button>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Declaración de variables
		//
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$x = 0;											//Variable de conteo de registro de comprobantes
		$y = 0;											//Variable para capturar la cantidad de comprobantes
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Proceso para generar lotes de comprobantes de retención de ingresos
		//
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$fechaI = $_POST['txtFechaI'];					//Fecha Inicial
		$fechaF = $_POST['txtFechaF'];					//Fecha Final
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Formateamos las fechas recibidas para usarlas en la consulta
		//
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//Fehca Inicial
		$fechaI = explode("/",$fechaI);					//Dividimos la fecha Inicial usando /
		$fechaI = "'$fechaI[2]-$fechaI[1]-$fechaI[0]'";	//Formateamos la fecha Y-m-d
		//Fecha Final
		$fechaF = explode("/",$fechaF);					//Dividimos la fecha Final usando /
		$fechaF = "'$fechaF[2]-$fechaF[1]-$fechaF[0]'";	//Formateamos la fecha Y-m-d
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Recorremos las tablas de retenciones obteniendo los comprobantes, y usamos un rango de fecha
		//
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$sqlRT = "SELECT DISTINCT cnt.id_unico
				FROM 		gf_retencion rt
				LEFT JOIN 	gf_comprobante_cnt cnt ON cnt.id_unico = rt.comprobante
				LEFT JOIN 	gf_tipo_retencion tpr ON tpr.id_unico = rt.tiporetencion
				WHERE 		(cnt.fecha BETWEEN $fechaI AND $fechaF)
				AND 		(tpr.concepto_ingreso_hom IS NOT NULL)";
		$resultRT = $mysqli->query($sqlRT);
		while($rowRT = mysqli_fetch_row($resultRT)){
			crear_pptal_retencion($rowRT[0]);			
			$x++;
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Contamos los comprobantes relacionados al tipo, (cambiar metodo de validación)
		//
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$sqlC = "SELECT DISTINCT COUNT(cnt.id_unico)
				FROM 		gf_retencion rt
				LEFT JOIN 	gf_comprobante_cnt cnt ON cnt.id_unico = rt.comprobante
				WHERE 		(cnt.fecha BETWEEN $fechaI AND $fechaF)";
		$resultC = $mysqli->query($sqlC);
		$cantidad = mysqli_num_rows($resultC);
		if($cantidad > 0){
			$rowC = mysqli_fetch_row($resultC);
			$y = $rowC[0];
		}		
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Validamos que ambas variables tengan el mismo valor para cerrar la ventana
		//
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if($x < $y){
			echo "<script lenguaje=\"JavaScript\">\n";
			echo "$('#myModal1').modal('show');\n";
			echo "$('#ver1').click(function() {window.close();});\n";
			echo "</script>\n";
		}		
		break;
}
 ?>
<script>
    function generar_causacion(){
        let tipo   = $("#sltTipoComI").val();
        let fechai = $("#txtFechaI").val();
        let fechaf = $("#txtFechaF").val();
        if(tipo!=''){
            jsShowWindowLoad('Guardando Información');
            $.ajax({
                type: "POST",
                url: "jsonPptal/comprobantesIngresoJson.php",
                data: {
                    action: 22,
                    tipo  : tipo,
                    fechai: fechai,
                    fechaf: fechaf
                },
                success: function (data, textStatus, jqXHR) {
                    jsRemoveWindowLoad();
                    console.log('Causacion' + data)
                    if (data == 1) {
                        $("#msj").html('Información Guardada Correctamente');
                        $("#mdlMsj").modal('show');
                    } else {
                        $("#msj").html('No Se Ha Podido Guardar la Información');
                        $("#mdlMsj").modal('show');
                    }
                    $("#btnMsj").click(function(){
                        document.location.reload();
                    })
                }
            })
        }
    }
</script>

<div class="modal fade" id="mdlMsj" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p><label id="msj" name="msj"></label></p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnMsj" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>