<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#17/05/2018 | Erica G. | Se Eliminaron Las Finciones Y Se pasaron a otro archivo
#16/05/2018 | Erica G. | Archivo Creado
####/################################################################################
require'../../Conexion/ConexionPDO.php';
#require'../../Conexion/conexion.php';
require '../../ExcelR/Classes/PHPExcel.php';
#require'consultas.php';
ini_set('max_execution_time', 0);
@session_start();
$con        = new ConexionPDO();
$panno      = $_SESSION['anno'];
$calendario = CAL_GREGORIAN;
#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6];
$tipoG      = $_REQUEST['tipoGrafico'];
#*************************************************************************************#
#Estilos Hoja
# *** Logotipo *** #
$gdImage = imagecreatefrompng('../../'.$ruta_logo); 
# *** Objeto de PHPExcel *** #
$objPHPExcel = new PHPExcel();
$estiloTituloReporte = array(
    'font' => array(
        'name' => 'Arial',
        'bold' => true,
        'italic' => false,
        'strike' => false,
        'size' => 12
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID
    ),
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_NONE
        )
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);

$estiloTituloColumnas = array(
    'font' => array(
        'name' => 'Arial',
        'bold' => true,
        'size' => 10,
        'color' => array(
            'rgb' => 'FFFFFF'
        )
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => '538DD5')
    ),
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);

$estiloInformacionl = new PHPExcel_Style();
$estiloInformacionl->applyFromArray(array(
    'font' => array(
        'name' => 'Arial',
        'color' => array(
            'rgb' => '000000'
        )
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID
    ),
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
));
$estiloInformacionr = new PHPExcel_Style();
$estiloInformacionr->applyFromArray(array(
    'font' => array(
        'name' => 'Arial',
        'color' => array(
            'rgb' => '000000'
        )
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID
    ),
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
));
$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
$objDrawing->setName('Logotipo');
$objDrawing->setDescription('Logotipo');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight(70);
$objDrawing->setCoordinates('A1');
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
#**************************************************************************************************#    
if($tipoG==1){
    #   ****    Consulta   ****    #
    $sql = $con->Listar("SELECT 
            cod_fuente, SUM(presupuesto_dfvo), SUM(registros) 
        FROM 
            temporal_consulta_pptal_gastos 
        GROUP BY 
            cod_fuente, rubro_fuente");
    #   ****    Establecemos en que fila inciara a imprimir los datos   ****    #
    $fila = 7; 
    # *** Propiedades de Documento *** #
    $objPHPExcel->getProperties()->setCreator($razonsocial)->setDescription("Ejecución Por Sector");
    # *** Establecemos la pestaña activa y nombre a la pestaña *** #
    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->setTitle("Sector");  
    #**********************************************************************************************#
    $sheet->getStyle('A1:D4')->applyFromArray($estiloTituloReporte);
    $sheet->getStyle('A5:D6')->applyFromArray($estiloTituloColumnas);
    $sheet->setCellValue('A1', $razonsocial);
    $sheet->mergeCells('A1:D1');
    $sheet->setCellValue('A2', $nombreIdent.': '.$numeroIdent);
    $sheet->mergeCells('A2:D2');
    $sheet->setCellValue('A3', $direccinTer.' - '.$telefonoTer);
    $sheet->mergeCells('A3:D3');
    #   ***************     Titulos        ***************  #
    $sheet->getColumnDimension('A')->setWidth(40);
    $sheet->setCellValue('A5', 'SECTOR');
    $sheet->mergeCells('A5:A6');
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->setCellValue('B5', 'APROPIACIÓN DEFINITIVA');
    $sheet->mergeCells('B5:B6');
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->setCellValue('C5', 'EJECUCIÓN');
    $sheet->mergeCells('C5:C6');
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->setCellValue('D5', 'PORCENTAJE EJECUTADO');
    $sheet->mergeCells('D5:D6');
    $sheet->getStyle('A5:D6')->getAlignment()->setWrapText(true); 
    # ** Recorremos los resultados de la consulta y los imprimimos ** #
    for ($i = 0; $i < count($sql); $i++) {
        $sheet->setCellValue('A' . $fila, $sql[$i][0]);
        $sheet->setCellValue('B' . $fila, $sql[$i][1]);
        $sheet->setCellValue('C' . $fila, $sql[$i][2]);
        if($sql[$i][1]!=0){
        $sheet->setCellValue('D' . $fila, round(($sql[$i][2]*100)/$sql[$i][1],2).'%');
        } else {
        $sheet->setCellValue('D' . $fila,'0%');    
        }
        # ** Sumamos 1 para pasar a la siguiente fila ** #
        $fila++; 
    }
    $fila = $fila - 1;
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacionl, "A7:A" . $fila);
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacionr, "B7:D" . $fila);
    $objPHPExcel->getActiveSheet()->getStyle('B7:C' . $fila)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
    $filaGrafica = $fila + 2;
    $labels = array(
      new PHPExcel_Chart_DataSeriesValues('String', 'Sector!$B$5', null, 1),
      new PHPExcel_Chart_DataSeriesValues('String', 'Sector!$C$5', null, 1), 
    );
    $categories = array(
      new PHPExcel_Chart_DataSeriesValues('String', 'Sector!$A$7:$A$'.$fila, null, 6),   
    );
    $values = array(
      new PHPExcel_Chart_DataSeriesValues('Number', 'Sector!$B$7:$B$'.$fila, null, 4),
      new PHPExcel_Chart_DataSeriesValues('Number', 'Sector!$C$7:$C$'.$fila, null, 4),  
    );
    $series = new PHPExcel_Chart_DataSeries(
      PHPExcel_Chart_DataSeries::TYPE_BARCHART_3D,     // plotType
      PHPExcel_Chart_DataSeries::GROUPING_STANDARD,  // plotGrouping
      array(0,1),                                     // plotOrder
      $labels,                                        // plotLabel
      $categories,                                    // plotCategory
      $values                                         // plotValues
    );  

    $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COLUMN);
    $layout1 = new PHPExcel_Chart_Layout();    // Create object of chart layout to set data label 
    $layout1->setShowVal(false);                   
    $layout1->setShowPercent(true);    
    $plotarea = new PHPExcel_Chart_PlotArea($layout1, array($series));
    $title    = new PHPExcel_Chart_Title('Ejecución Sectores');  
    $legend   = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
    $xTitle   = new PHPExcel_Chart_Title('Sectores');
    $yTitle   = new PHPExcel_Chart_Title('Valor');
    $chart    = new PHPExcel_Chart(
      'chart1',                                       // name
      $title,                                         // title
      $legend,                                        // legend 
      $plotarea,                                      // plotArea
      true,                                           // plotVisibleOnly
      0,                                              // displayBlanksAs
      $xTitle,                                        // xAxisLabel
      $yTitle                                         // yAxisLabel
    );                      
    $chart->setTopLeftPosition('F2');
    $chart->setBottomRightPosition('Q20');
    $sheet->addChart($chart);
    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $writer->setIncludeCharts(TRUE);
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Disposition: attachment;filename="Ejecucion_Sectores.xls"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
} elseif($tipoG==2){
    #   ****    Consulta   ****    #
    $sql = $con->Listar("SELECT 
            cod_fuente, SUM(presupuesto_dfvo), SUM(registros) 
        FROM 
            temporal_consulta_pptal_gastos 
        GROUP BY 
            cod_fuente");
    #   ****    Establecemos en que fila inciara a imprimir los datos   ****    #
    $fila = 7; 
    # *** Propiedades de Documento *** #
    $objPHPExcel->getProperties()->setCreator($razonsocial)->setDescription("Ejecución Por Fuentes");
    # *** Establecemos la pestaña activa y nombre a la pestaña *** #
    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->setTitle("Fuente");  
    #**********************************************************************************************#
    $sheet->getStyle('A1:D4')->applyFromArray($estiloTituloReporte);
    $sheet->getStyle('A5:D6')->applyFromArray($estiloTituloColumnas);
    $sheet->setCellValue('A1', $razonsocial);
    $sheet->mergeCells('A1:D1');
    $sheet->setCellValue('A2', $nombreIdent.': '.$numeroIdent);
    $sheet->mergeCells('A2:D2');
    $sheet->setCellValue('A3', $direccinTer.' - '.$telefonoTer);
    $sheet->mergeCells('A3:D3');
    #   ***************     Titulos        ***************  #
    $sheet->getColumnDimension('A')->setWidth(40);
    $sheet->setCellValue('A5', 'FUENTE');
    $sheet->mergeCells('A5:A6');
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->setCellValue('B5', 'APROPIACIÓN DEFINITIVA');
    $sheet->mergeCells('B5:B6');
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->setCellValue('C5', 'EJECUCIÓN');
    $sheet->mergeCells('C5:C6');
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->setCellValue('D5', 'PORCENTAJE EJECUTADO');
    $sheet->mergeCells('D5:D6');
    $sheet->getStyle('A5:D6')->getAlignment()->setWrapText(true); 
    # ** Recorremos los resultados de la consulta y los imprimimos ** #
    for ($i = 0; $i < count($sql); $i++) {
        $sheet->setCellValue('A' . $fila, $sql[$i][0]);
        $sheet->setCellValue('B' . $fila, $sql[$i][1]);
        $sheet->setCellValue('C' . $fila, $sql[$i][2]);
        if($sql[$i][1]!=0){
        $sheet->setCellValue('D' . $fila, round(($sql[$i][2]*100)/$sql[$i][1],2).'%');
        } else {
        $sheet->setCellValue('D' . $fila,'0%');    
        }
        # ** Sumamos 1 para pasar a la siguiente fila ** #
        $fila++; 
    }
    $fila = $fila - 1;
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacionl, "A7:A" . $fila);
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacionr, "B7:D" . $fila);
    $objPHPExcel->getActiveSheet()->getStyle('B7:C' . $fila)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
    $filaGrafica = $fila + 2;
    $labels = array(
      new PHPExcel_Chart_DataSeriesValues('String', 'Fuente!$B$5', null, 1),
      new PHPExcel_Chart_DataSeriesValues('String', 'Fuente!$C$5', null, 1), 
    );
    $categories = array(
      new PHPExcel_Chart_DataSeriesValues('String', 'Fuente!$A$7:$A$'.$fila, null, 6),   
    );
    $values = array(
      new PHPExcel_Chart_DataSeriesValues('Number', 'Fuente!$B$7:$B$'.$fila, null, 4),
      new PHPExcel_Chart_DataSeriesValues('Number', 'Fuente!$C$7:$C$'.$fila, null, 4),  
    );
    $series = new PHPExcel_Chart_DataSeries(
      PHPExcel_Chart_DataSeries::TYPE_BARCHART_3D,     // plotType
      PHPExcel_Chart_DataSeries::GROUPING_STANDARD,  // plotGrouping
      array(0,1),                                     // plotOrder
      $labels,                                        // plotLabel
      $categories,                                    // plotCategory
      $values                                         // plotValues
    );  

    $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COLUMN);
    $layout1 = new PHPExcel_Chart_Layout();    // Create object of chart layout to set data label 
    $layout1->setShowVal(false);                   
    $layout1->setShowPercent(true);    
    $plotarea = new PHPExcel_Chart_PlotArea($layout1, array($series));
    $title    = new PHPExcel_Chart_Title('Ejecución Fuentes');  
    $legend   = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
    $xTitle   = new PHPExcel_Chart_Title('Fuentes');
    $yTitle   = new PHPExcel_Chart_Title('Valor');
    $chart    = new PHPExcel_Chart(
      'chart1',                                       // name
      $title,                                         // title
      $legend,                                        // legend 
      $plotarea,                                      // plotArea
      true,                                           // plotVisibleOnly
      0,                                              // displayBlanksAs
      $xTitle,                                        // xAxisLabel
      $yTitle                                         // yAxisLabel
    );                      
    $chart->setTopLeftPosition('F2');
    $chart->setBottomRightPosition('Q20');
    $sheet->addChart($chart);
    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $writer->setIncludeCharts(TRUE);
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Disposition: attachment;filename="Ejecucion_Fuentes.xls"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
}elseif($tipoG==3){
    $digitos = $_REQUEST['digitos'];
    #   ****    Consulta   ****    #
    $sql = $con->Listar("SELECT 
            cod_rubro,nombre_rubro, SUM(presupuesto_dfvo), SUM(recaudos) 
        FROM 
            temporal_consulta_pptal_gastos 
        WHERE 
            LENGTH(cod_rubro)=$digitos 
        GROUP BY 
            cod_rubro");
    #   ****    Establecemos en que fila inciara a imprimir los datos   ****    #
    $fila = 7; 
    # *** Propiedades de Documento *** #
    $objPHPExcel->getProperties()->setCreator($razonsocial)->setDescription("Ejecución De Ingresos");
    # *** Establecemos la pestaña activa y nombre a la pestaña *** #
    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->setTitle("Ingresos");  
    #**********************************************************************************************#
    $sheet->getStyle('A1:D4')->applyFromArray($estiloTituloReporte);
    $sheet->getStyle('A5:D6')->applyFromArray($estiloTituloColumnas);
    $sheet->setCellValue('A1', $razonsocial);
    $sheet->mergeCells('A1:D1');
    $sheet->setCellValue('A2', $nombreIdent.': '.$numeroIdent);
    $sheet->mergeCells('A2:D2');
    $sheet->setCellValue('A3', $direccinTer.' - '.$telefonoTer);
    $sheet->mergeCells('A3:D3');
    #   ***************     Titulos        ***************  #
    $sheet->getColumnDimension('A')->setWidth(40);
    $sheet->setCellValue('A5', 'RUBRO');
    $sheet->mergeCells('A5:A6');
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->setCellValue('B5', 'APROPIACIÓN DEFINITIVA');
    $sheet->mergeCells('B5:B6');
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->setCellValue('C5', 'EJECUCIÓN');
    $sheet->mergeCells('C5:C6');
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->setCellValue('D5', 'PORCENTAJE EJECUTADO');
    $sheet->mergeCells('D5:D6');
    $sheet->getStyle('A5:D6')->getAlignment()->setWrapText(true); 
    # ** Recorremos los resultados de la consulta y los imprimimos ** #
    for ($i = 0; $i < count($sql); $i++) {
        $sheet->setCellValue('A' . $fila, $sql[$i][0].' - '.$sql[$i][1]);
        $sheet->setCellValue('B' . $fila, $sql[$i][2]);
        $sheet->setCellValue('C' . $fila, $sql[$i][3]);
        if($sql[$i][2]<=0 ){
            $sheet->setCellValue('D' . $fila,'0%');    
            
        } else {
            $sheet->setCellValue('D' . $fila, round(($sql[$i][3]*100)/$sql[$i][2],2).'%');
        }
        # ** Sumamos 1 para pasar a la siguiente fila ** #
        $fila++; 
    }
    $fila = $fila - 1;
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacionl, "A7:A" . $fila);
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacionr, "B7:D" . $fila);
    $objPHPExcel->getActiveSheet()->getStyle('B7:C' . $fila)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
    $filaGrafica = $fila + 2;
    $labels = array(
      new PHPExcel_Chart_DataSeriesValues('String', 'Ingresos!$B$5', null, 1),
      new PHPExcel_Chart_DataSeriesValues('String', 'Ingresos!$C$5', null, 1), 
    );
    $categories = array(
      new PHPExcel_Chart_DataSeriesValues('String', 'Ingresos!$A$7:$A$'.$fila, null, 6),   
    );
    $values = array(
      new PHPExcel_Chart_DataSeriesValues('Number', 'Ingresos!$B$7:$B$'.$fila, null, 4),
      new PHPExcel_Chart_DataSeriesValues('Number', 'Ingresos!$C$7:$C$'.$fila, null, 4),  
    );
    $series = new PHPExcel_Chart_DataSeries(
      PHPExcel_Chart_DataSeries::TYPE_BARCHART_3D,     // plotType
      PHPExcel_Chart_DataSeries::GROUPING_STANDARD,  // plotGrouping
      array(0,1),                                     // plotOrder
      $labels,                                        // plotLabel
      $categories,                                    // plotCategory
      $values                                         // plotValues
    );  

    $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COLUMN);
    $layout1 = new PHPExcel_Chart_Layout();    // Create object of chart layout to set data label 
    $layout1->setShowVal(false);                   
    $layout1->setShowPercent(true);    
    $plotarea = new PHPExcel_Chart_PlotArea($layout1, array($series));
    $title    = new PHPExcel_Chart_Title('Ejecución De Ingresos');  
    $legend   = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
    $xTitle   = new PHPExcel_Chart_Title('Rubros');
    $yTitle   = new PHPExcel_Chart_Title('Valor');
    $chart    = new PHPExcel_Chart(
      'chart1',                                       // name
      $title,                                         // title
      $legend,                                        // legend 
      $plotarea,                                      // plotArea
      true,                                           // plotVisibleOnly
      0,                                              // displayBlanksAs
      $xTitle,                                        // xAxisLabel
      $yTitle                                         // yAxisLabel
    );                      
    $chart->setTopLeftPosition('F2');
    $chart->setBottomRightPosition('Q20');
    $sheet->addChart($chart);
    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $writer->setIncludeCharts(TRUE);
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Disposition: attachment;filename="Ejecucion_Ingresos.xls"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
}
