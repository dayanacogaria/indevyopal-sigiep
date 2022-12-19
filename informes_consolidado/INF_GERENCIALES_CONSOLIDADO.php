<?php 
require'../Conexion/ConexionPDO.php';
require '../ExcelR/Classes/PHPExcel.php';
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

#Estilos Hoja
# *** Logotipo *** #
$gdImage = imagecreatefrompng('../'.$ruta_logo); 
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

if($_REQUEST['c']==3){
$sql = $con->Listar("SELECT 
        tf.nombre, SUM(t.presupuesto_dfvo), SUM(t.registros) 
    FROM 
        temporal_pptal_consolidada t 
    LEFT JOIN 
    	gf_tipo_fuente tf ON t.tipo_fuente = tf.id_unico 
    GROUP BY 
        t.tipo_fuente");
    #*************************************************************************************#
    #   ****    Establecemos en que fila inciara a imprimir los datos   ****    #
    $fila = 7; 
    # *** Propiedades de Documento *** #
    $objPHPExcel->getProperties()->setCreator($razonsocial)->setDescription("Ejecución Consolidada Gastos Por Fuente");
    # *** Establecemos la pestaña activa y nombre a la pestaña *** #
    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->setTitle("Fuentes");  
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
      new PHPExcel_Chart_DataSeriesValues('String', 'Fuentes!$B$5', null, 1),
      new PHPExcel_Chart_DataSeriesValues('String', 'Fuentes!$C$5', null, 1), 
    );
    $categories = array(
      new PHPExcel_Chart_DataSeriesValues('String', 'Fuentes!$A$7:$A$'.$fila, null, 6),   
    );
    $values = array(
      new PHPExcel_Chart_DataSeriesValues('Number', 'Fuentes!$B$7:$B$'.$fila, null, 4),
      new PHPExcel_Chart_DataSeriesValues('Number', 'Fuentes!$C$7:$C$'.$fila, null, 4),  
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
    $title    = new PHPExcel_Chart_Title('Ejecución Gastos Fuentes');  
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
    header('Content-Disposition: attachment;filename="Ejecucion_Fuentes_Consolidada_Gastos.xls"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
}elseif($_REQUEST['c']==4){
$sql = $con->Listar("SELECT 
        tf.nombre, SUM(t.presupuesto_dfvo), SUM(t.recaudos) 
    FROM 
        temporal_pptal_consolidada t 
    LEFT JOIN 
    	gf_tipo_fuente tf ON t.tipo_fuente = tf.id_unico 
    GROUP BY 
        t.tipo_fuente");
    #*************************************************************************************#
    #   ****    Establecemos en que fila inciara a imprimir los datos   ****    #
    $fila = 7; 
    # *** Propiedades de Documento *** #
    $objPHPExcel->getProperties()->setCreator($razonsocial)->setDescription("Ejecución Consolidada Ingresos Por Fuente");
    # *** Establecemos la pestaña activa y nombre a la pestaña *** #
    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->setTitle("Fuentes");  
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
      new PHPExcel_Chart_DataSeriesValues('String', 'Fuentes!$B$5', null, 1),
      new PHPExcel_Chart_DataSeriesValues('String', 'Fuentes!$C$5', null, 1), 
    );
    $categories = array(
      new PHPExcel_Chart_DataSeriesValues('String', 'Fuentes!$A$7:$A$'.$fila, null, 6),   
    );
    $values = array(
      new PHPExcel_Chart_DataSeriesValues('Number', 'Fuentes!$B$7:$B$'.$fila, null, 4),
      new PHPExcel_Chart_DataSeriesValues('Number', 'Fuentes!$C$7:$C$'.$fila, null, 4),  
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
    $title    = new PHPExcel_Chart_Title('Ejecución Ingresos Fuentes');  
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
    header('Content-Disposition: attachment;filename="Ejecucion_Fuentes_Consolidada_Ingresos.xls"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
}elseif($_REQUEST['c']==5){
$sql = $con->Listar("SELECT 
        CONCAT_WS(' ', tr.razonsocial, tr.numeroidentificacion), SUM(t.presupuesto_dfvo), SUM(t.registros) 
    FROM 
        temporal_pptal_consolidada t 
    LEFT JOIN 
    	gf_tercero tr ON t.tipo_fuente = tr.id_unico 
    GROUP BY 
        t.tipo_fuente");
    #*************************************************************************************#
    #   ****    Establecemos en que fila inciara a imprimir los datos   ****    #
    $fila = 7; 
    # *** Propiedades de Documento *** #
    $objPHPExcel->getProperties()->setCreator($razonsocial)->setDescription("Ejecución Consolidada Gastos Por IE");
    # *** Establecemos la pestaña activa y nombre a la pestaña *** #
    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->setTitle("IE");  
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
    $sheet->setCellValue('A5', 'IE');
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
      new PHPExcel_Chart_DataSeriesValues('String', 'IE!$B$5', null, 1),
      new PHPExcel_Chart_DataSeriesValues('String', 'IE!$C$5', null, 1), 
    );
    $categories = array(
      new PHPExcel_Chart_DataSeriesValues('String', 'IE!$A$7:$A$'.$fila, null, 6),   
    );
    $values = array(
      new PHPExcel_Chart_DataSeriesValues('Number', 'IE!$B$7:$B$'.$fila, null, 4),
      new PHPExcel_Chart_DataSeriesValues('Number', 'IE!$C$7:$C$'.$fila, null, 4),  
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
    $title    = new PHPExcel_Chart_Title('Ejecución Consolidada Gastos Por IE');  
    $legend   = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
    $xTitle   = new PHPExcel_Chart_Title('Institución Educativa');
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
    $chart->setBottomRightPosition('X31');
    $sheet->addChart($chart);
    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $writer->setIncludeCharts(TRUE);
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Disposition: attachment;filename="Ejecucion_IE_Consolidada_Gastos.xls"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
}elseif($_REQUEST['c']==6){
$sql = $con->Listar("SELECT 
        CONCAT_WS(' ', tf.razonsocial, tf.numeroidentificacion), SUM(t.presupuesto_dfvo), SUM(t.recaudos) 
    FROM 
        temporal_pptal_consolidada t 
    LEFT JOIN 
    	gf_tercero tf ON t.tipo_fuente = tf.id_unico 
    GROUP BY 
        t.tipo_fuente");
    #*************************************************************************************#
    #   ****    Establecemos en que fila inciara a imprimir los datos   ****    #
    $fila = 7; 
    # *** Propiedades de Documento *** #
    $objPHPExcel->getProperties()->setCreator($razonsocial)->setDescription("Ejecución Consolidada Ingresos Por IE");
    # *** Establecemos la pestaña activa y nombre a la pestaña *** #
    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->setTitle("IE");  
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
    $sheet->setCellValue('A5', 'IE');
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
      new PHPExcel_Chart_DataSeriesValues('String', 'IE!$B$5', null, 1),
      new PHPExcel_Chart_DataSeriesValues('String', 'IE!$C$5', null, 1), 
    );
    $categories = array(
      new PHPExcel_Chart_DataSeriesValues('String', 'IE!$A$7:$A$'.$fila, null, 6),   
    );
    $values = array(
      new PHPExcel_Chart_DataSeriesValues('Number', 'IE!$B$7:$B$'.$fila, null, 4),
      new PHPExcel_Chart_DataSeriesValues('Number', 'IE!$C$7:$C$'.$fila, null, 4),  
    );
    $series = new PHPExcel_Chart_DataSeries(
      PHPExcel_Chart_DataSeries::TYPE_BARCHART_3D,     // plotType
      PHPExcel_Chart_DataSeries::GROUPING_STANDARD,  // plotGrouping
      array(0,1),                                     // plotOrder
      $labels,                                        // plotLabel
      $categories,                                    // plotCategory
      $values                                         // plotValues
    );  

    $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_VERTICAL);
    $layout1 = new PHPExcel_Chart_Layout();    // Create object of chart layout to set data label 
    $layout1->setShowVal(false);                   
    $layout1->setShowPercent(true);    
    $plotarea = new PHPExcel_Chart_PlotArea($layout1, array($series));
    $title    = new PHPExcel_Chart_Title('Ejecución Consolidada Ingresos Por IE');  
    $legend   = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
    $xTitle   = new PHPExcel_Chart_Title('Institución Educativa');
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
    $chart->setBottomRightPosition('X31');
    $sheet->addChart($chart);
    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $writer->setIncludeCharts(TRUE);
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Disposition: attachment;filename="Ejecucion_IE_Consolidada_Ingresos.xls"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
}
elseif($_REQUEST['c']==7){
$sql = $con->Listar("SELECT 
        c.nombre, SUM(t.presupuesto_dfvo), SUM(t.registros) 
    FROM 
        temporal_pptal_consolidada t 
    LEFT JOIN 
    	gf_tercero tf ON t.tipo_fuente = tf.id_unico 
    LEFT JOIN 
    	gf_ciudad c ON tf.ciudadresidencia = c.id_unico 
    LEFT JOIN 
    	gf_departamento d ON c.departamento = d.id_unico 
    WHERE 
        c.id_unico IS NOT NULL 
    GROUP BY 
        c.id_unico");
    #*************************************************************************************#
    #   ****    Establecemos en que fila inciara a imprimir los datos   ****    #
    $fila = 7; 
    # *** Propiedades de Documento *** #
    $objPHPExcel->getProperties()->setCreator($razonsocial)->setDescription("Ejecución Consolidada Gastos Por Ciudad");
    # *** Establecemos la pestaña activa y nombre a la pestaña *** #
    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->setTitle("CIUDAD");  
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
    $sheet->setCellValue('A5', 'CIUDAD');
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
      new PHPExcel_Chart_DataSeriesValues('String', 'CIUDAD!$B$5', null, 1),
      new PHPExcel_Chart_DataSeriesValues('String', 'CIUDAD!$C$5', null, 1), 
    );
    $categories = array(
      new PHPExcel_Chart_DataSeriesValues('String', 'CIUDAD!$A$7:$A$'.$fila, null, 6),   
    );
    $values = array(
      new PHPExcel_Chart_DataSeriesValues('Number', 'CIUDAD!$B$7:$B$'.$fila, null, 4),
      new PHPExcel_Chart_DataSeriesValues('Number', 'CIUDAD!$C$7:$C$'.$fila, null, 4),  
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
    $title    = new PHPExcel_Chart_Title('Ejecución Consolidada Gastos Por Ciudad');  
    $legend   = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
    $xTitle   = new PHPExcel_Chart_Title('Ciudad');
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
    $chart->setBottomRightPosition('X31');
    $sheet->addChart($chart);
    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $writer->setIncludeCharts(TRUE);
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Disposition: attachment;filename="Ejecucion_Consolidada_Gastos_Ciudad.xls"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
}elseif($_REQUEST['c']==8){
$sql = $con->Listar("SELECT 
        c.nombre, SUM(t.presupuesto_dfvo), SUM(t.recaudos) 
    FROM 
        temporal_pptal_consolidada t 
    LEFT JOIN 
    	gf_tercero tf ON t.tipo_fuente = tf.id_unico 
    LEFT JOIN 
    	gf_ciudad c ON tf.ciudadresidencia = c.id_unico 
    LEFT JOIN 
    	gf_departamento d ON c.departamento = d.id_unico 
    WHERE 
        c.id_unico IS NOT NULL 
    GROUP BY 
        c.id_unico ");
    #*************************************************************************************#
    #   ****    Establecemos en que fila inciara a imprimir los datos   ****    #
    $fila = 7; 
    # *** Propiedades de Documento *** #
    $objPHPExcel->getProperties()->setCreator($razonsocial)->setDescription("Ejecución Consolidada Ingresos Por Ciudad");
    # *** Establecemos la pestaña activa y nombre a la pestaña *** #
    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->setTitle("CIUDAD");  
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
    $sheet->setCellValue('A5', 'CIUDAD');
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
      new PHPExcel_Chart_DataSeriesValues('String', 'CIUDAD!$B$5', null, 1),
      new PHPExcel_Chart_DataSeriesValues('String', 'CIUDAD!$C$5', null, 1), 
    );
    $categories = array(
      new PHPExcel_Chart_DataSeriesValues('String', 'CIUDAD!$A$7:$A$'.$fila, null, 6),   
    );
    $values = array(
      new PHPExcel_Chart_DataSeriesValues('Number', 'CIUDAD!$B$7:$B$'.$fila, null, 4),
      new PHPExcel_Chart_DataSeriesValues('Number', 'CIUDAD!$C$7:$C$'.$fila, null, 4),  
    );
    $series = new PHPExcel_Chart_DataSeries(
      PHPExcel_Chart_DataSeries::TYPE_BARCHART_3D,     // plotType
      PHPExcel_Chart_DataSeries::GROUPING_STANDARD,  // plotGrouping
      array(0,1),                                     // plotOrder
      $labels,                                        // plotLabel
      $categories,                                    // plotCategory
      $values                                         // plotValues
    );  

    $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_VERTICAL);
    $layout1 = new PHPExcel_Chart_Layout();    // Create object of chart layout to set data label 
    $layout1->setShowVal(false);                   
    $layout1->setShowPercent(true);    
    $plotarea = new PHPExcel_Chart_PlotArea($layout1, array($series));
    $title    = new PHPExcel_Chart_Title('Ejecución Consolidada Ingresos Por Ciudad');  
    $legend   = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
    $xTitle   = new PHPExcel_Chart_Title('Ciudad');
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
    $chart->setBottomRightPosition('X31');
    $sheet->addChart($chart);
    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $writer->setIncludeCharts(TRUE);
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Disposition: attachment;filename="Ejecucion_Consolidada_Ingresos_Ciudad.xls"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
}
