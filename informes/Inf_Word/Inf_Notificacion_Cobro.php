<?php
require_once '../../Conexion/ConexionPDO.php';
require_once '../../Conexion/conexion.php';
$conexion = new ConexionPDO();
@session_start();
require_once 'PhpWord/Autoloader.php';
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Style\Cell;
Autoloader::register();
#*** Variables Que Recibe ***#
    $predioI = trim($_REQUEST['predioI']);
    $predioF = trim($_REQUEST['predioF']);
#*************************#
$row = $conexion->Listar("SELECT gpr.id_unico, 
    gps.nombres as nom, gpr.direccion, 
    gpr.codigo_catastral as cod, 
    gps.numero as doc 
    FROM gp_predio1 gpr
    LEFT JOIN gp_tercero_predio gpt ON gpt.predio=gpr.id_unico
    LEFT JOIN gr_propietarios as gps ON gpt.tercero = gps.id_unico
    WHERE gpr.estado = 2 
        AND gpt.orden='001'
        AND cast(gpr.codigo_catastral as unsigned) BETWEEN '$predioI' AND '$predioF'"); 

    $word       = new  PhpOffice\PhpWord\PhpWord();

    $section    = $word->AddSection();
    $word->addFontStyle('rStyle', array('bold' => false, 'align'=> 'both',));
    $word->addParagraphStyle('pStyle', array('align' => 'center', 'bold' => true,'spaceBefore' => 0, 'spaceAfter' => 0));
    $word->addParagraphStyle('jStyle', array('align' => 'both',));
    $word->addParagraphStyle('iStyle', array('align' => 'center', 'italic' => true,'spaceBefore' => 0, 'spaceAfter' => 0));
    $word->addParagraphStyle('iStyle2', array('bold' => true,'spaceBefore' => 0, 'spaceAfter' => 0));
    $word->addTitleStyle(1, array('bold' => true), array('spaceAfter' => 240));
    
    #   ************   Datos Compañia   ************    #
    $compania = $_SESSION['compania'];
    $rowC = $conexion->Listar("SELECT 	ter.id_unico,
                    ter.razonsocial,
                    UPPER(ti.nombre),
                    ter.numeroidentificacion,
                    dir.direccion,
                    tel.valor,
                    ter.ruta_logo, ter.email 
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
    $email       = $rowC[0][7];
    #****************************#

    #********************************* Encabezado *****************************#
    $header     = $section->addHeader();
    $styleTable = array('borderSize' => 6,);
    $styleCell  = array('valign' => 'center',);
    $table      = $header->addTable(); 
    $cellRowSpan     = array('vMerge' => 'restart','borderSize' => 6,'align' => 'center');
    $cellRowContinue = array('vMerge' => 'continue','borderSize' => 6,'align' => 'center');
    $cellColSpan     = array('gridSpan' => 3,'borderSize' => 6,'align' => 'center');
    $cell            = array('borderSize' => 6,'align' => 'center');

    $table->addRow();
    $table->addCell(2000, $cellRowSpan)->addImage(
        '../../'.$ruta_logo,
        array('width' => 80, 'height' => 80, 'align' => 'center'));;
    $table->addCell(8000, $cellColSpan)->addText("ALCALDÍA MUNICIPAL DE PAZ DE RÍO", null, 'pStyle');

    $table->addRow();
    $table->addCell(null, $cellRowContinue);
    $table->addCell(8000,$cellColSpan)->addText("MODELO ESTANDAR DE CONTROL INTERNO", null, 'pStyle');

    $table->addRow();
    $table->addCell(null, $cellRowContinue);
    $table->addCell(8000,$cellColSpan)->addText("FORMATO EXPEDICIÓN DE COMUNICACIONES OFICIALES", null, 'pStyle');

    $table->addRow();
    $table->addCell(null, $cellRowContinue);
    $table->addCell(8000,$cellColSpan)->addText("FORMATO DE COMUNICACIONES ", null, 'pStyle');
    $table->addRow();
    $table->addCell(null, $cellRowContinue);
    $table->addCell(2000,$cell)->addText("VERSION: 01", null, 'pStyle');
    $table->addCell(4000,$cell)->addText("CODIGO: M1-P1-PT15-F01", null, 'pStyle');
    $table->addCell(2000,$cell)->addPreserveText('Página {PAGE} de {NUMPAGES}', null, 'pStyle');


    $mes = ['no', 'Enero', 'Febrero', 'Abril',
        'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre',   'Diciembre'];
    $xnmes = $mes[(int) date("m")];    
    for ($i = 0;$i < count($row);$i++) {
        $xxxA =obtnerultimoannopago($row[$i][0]);
        if($xxxA < date("Y") || empty($xxxA)){
            $section->addTextBreak();
            $section->addText("NOTIFICACION OFICIAL DE IMPUESTO PREDIAL Y COMPLEMENTARIOS", array('bold' => true),'pStyle');
            $section->addTextBreak();
            $section->addText("Número de expediente:",null,'iStyle2');
            $textrun = $section->addTextRun();
            $textrun->addText("Fecha de Apertura:               ",null,'iStyle2');
            $textrun->addText(date("d/m/Y"),array('bold' => true),null,'iStyle2');
            $textrun->addTextBreak(); 
            $textrun->addText("Código Predial:                   ",null,'iStyle2');
            $textrun->addText($row[$i]['cod'],array('bold' => true),null,'iStyle2');
            $section->addTextBreak(); 
            $section->addText($row[$i]['nom'],array('bold' => true));
            $section->addText($row[$i]['direccion'],array('bold' => true));
            $section->addTextBreak(); 
            $section->addText("La presente con el fin de comunicarle que la Secretaría de "
                ."Hacienda Municipal de Paz de Rio, expidió la Liquidación Oficial de Impuesto "
                ."de predial en referencia; la cual una vez en firme presentará merito ejecutivo.",array('bold' => false),'jStyle');
            $section->addText("En consecuencia, le solicito se sirva acercarse a la Secretaría de "
                ."Hacienda dentro de los diez (10) días siguientes a la fecha de recibido de la presente " 
                ."comunicación, con el fin de aclarar su situación y/o efectuar el pago correspondiente, "
                ."evitando el trámite COACTIVO en su contra, el cual conlleva decreto de medidas cautelares, "  
                ."resultando más gravoso por el incremento porcentual de la deuda y el valor de las costas procesales.",array('bold' => false),'jStyle');
            $section->addText("La cuantía establecida incluye intereses liquidados a la fecha de "
                ."generación de la presente, los cuales serán re liquidados en el momento del pago.",array('bold' => false),'jStyle');
            $section->addTextBreak();
            $section->addTextBreak();
            $section->addTextBreak();
            $section->addText("LUDY MARISOL AVENADAÑO GARCIA",array('bold' => true),'iStyle2');
            $section->addText("Secretaria de Hacienda",array('bold' => true),'iStyle2');
            $section->addText("Municipio de Paz de Rio",array('bold' => true),'iStyle2');

            $section->addTextBreak();
            $section->addTextBreak();

            $table           = $section->addTable(array('width' => 70 * 50, 'unit' => 'pct', 'align' => 'right'));
            $cell            = array('borderSize' => 6,'align' => 'both');
            $table->addRow();
            $c1=$table->addCell(3000,$cell);
            $c1->addText("NOTIFICACION PERSONAL DE DOCUMENTO",array('bold' => true),'pStyle');
            $c1->addText("En Paz de Rio (Boyacá) a los ______ días del "
                    ."mes de ______________de _____, "
                    ."se notificó en forma a: ____________________________________, "
                    ."quien se identifica con C.C. No. ____________________________ "
                    ."Expedida en la ciudad de ___________________________________ "
                    ."de la presente liquidación oficial",array('bold' => false),'jStyle');
            $c1->addText("El Notificado, ",array('bold' => true));
            $c1->addTextBreak();

            if(count($row)-1!=$i){
                $section->addPageBreak();
            }
        }
    }
    #************* Footer ***************************#
    $footer = $section->addFooter();
    $footer->addImage(
        '../../logo/logop.png',
        array('width' => 180, 'height' => 50, 'align' => 'center'));
    $footer->addText('Dirección: '.$direccinTer.' '.$telefonoTer,array('italic' => true),'iStyle');
    $footer->addText('E-mail: '.$email,array('italic' => true),'iStyle');


    $filename = "NotificacionCobro.docx";
    $word->save($filename,"Word2007");

    header('Content-Description: File Transfer');
    header('Content-type: application/force-download');
    header('Content-Disposition: attachment; filename='.basename('NotificacionCobro.docx'));
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: '.filesize('NotificacionCobro.docx'));
    readfile('NotificacionCobro.docx');
    unlink('NotificacionCobro.docx');

#*************************************************************************#
function obtnerultimoannopago($predio){
    global $conexion;
    $ult = $conexion->Listar("SELECT max(anno) FROM gr_ultimo_ano_pago WHERE predio = $predio");
    return $ult[0][0];
}