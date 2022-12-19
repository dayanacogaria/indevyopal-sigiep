<?php
require_once '../../Conexion/ConexionPDO.php';
require_once '../../Conexion/conexion.php';
require_once '../../funciones/funcionLiquidador.php';
$calendario = CAL_GREGORIAN;
$conexion = new ConexionPDO();
$liquidar = new Liquidador();
@session_start();
require_once 'PhpWord/Autoloader.php';
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Style\Cell;
Autoloader::register();
#*** Variables Que Recibe ***#
$predioI = trim($_REQUEST['predioI']);
$predioF = trim($_REQUEST['predioF']);
$annoI   = trim($_REQUEST['a1']);
$annoF   = trim($_REQUEST['a2']);
$monto   = trim($_REQUEST['m']);
#*************************#
$row = $conexion->Listar("SELECT gpr.* 
    FROM gp_predio1 gpr
    WHERE gpr.estado = 2 
        AND cast(gpr.codigo_catastral as unsigned) BETWEEN '$predioI' AND '$predioF'"); 

    $word       = new  PhpOffice\PhpWord\PhpWord();

    $section    = $word->AddSection();
    $word->addFontStyle('rStyle', array('bold' => false, 'align'=> 'both',));
    $word->addParagraphStyle('pStyle', array('align' => 'center', 'bold' => true,'spaceBefore' => 0, 'spaceAfter' => 0));
    $word->addParagraphStyle('jStyle', array('align' => 'both'));
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


    $mes = ['no', 'Enero', 'Febrero', 'Marzo', 'Abril',
        'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $xnmes = $mes[(int) date("m")];    
    for ($i = 0;$i < count($row);$i++) {
        $xxxA =obtnerultimoannopago($row[$i][0]);
        if($xxxA < date("Y") || empty($xxxA)){
            if(empty($xxxA)){$xanno=$annoI;} else{$xanno=$xxxA;}
            $avaluos = $conexion->Listar("SELECT ava.anno, 
                ava.valor, 
                tar.valor as tarifa,tar.tipobaseambiental,
                tar.porcentajeimpuestoambiental, tar.porcentajesobretasa
                FROM gr_avaluo as ava 
                LEFT JOIN gr_tarifa as tar ON ava.tarifa = tar.id_unico 
                WHERE ava.predio = ".$row[$i][0]." 
                    AND ava.anno >$xanno  
                    AND ava.anno <= $annoF  
                    ORDER BY ava.anno ASC ");
            $propros = $conexion->Listar("SELECT gpr.nombres,gpr.numero 
                    FROM gp_tercero_predio gtp 
                    LEFT JOIN gr_propietarios as gpr ON gtp.tercero = gpr.id_unico 
                    WHERE gtp.predio =".$row[$i][0]." 
                    AND gtp.propietario = 0 
                    AND gtp.orden ='001'");      
            $avaluoF = $conexion->Listar("SELECT valor FROM gr_avaluo 
                WHERE anno = $annoF and predio=".$row[$i][0]);
            $xhtml   = "";
            list ($ximp, $xdesc, $xint, $xcar, $xintc, $xbom, $xpap, $xdeb, $xto) = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
            $valorImp =0;$valorInt=0;
            for($a =0; $a < count($avaluos);$a++) {
                $avaluo = $avaluos[$a][1];
                $tarifa = $avaluos[$a][2];
                $annovr = $avaluos[$a][0];
                $tipo   = $avaluos[$a][3];
                $porcI  = $avaluos[$a][4];
                $porcS  = $avaluos[$a][5];
                $dateV   = $conexion->Listar("SELECT ven.fecha 
                    FROM gr_vencimiento as ven 
                    LEFT JOIN gf_mes as mes ON ven.mes = mes.id_unico 
                    WHERE ven.anno = $annovr 
                        AND tipo = 1 
                        AND mes.numero = ".date('m'));
                
                $dateD   = $conexion->Listar("SELECT ven.fecha 
                    FROM gr_vencimiento as ven 
                    LEFT JOIN gf_mes as mes ON ven.mes = mes.id_unico 
                    WHERE ven.anno = $annovr 
                        AND tipo = 2 
                        AND mes.numero = ".date('m'));
                if(count($dateV)>0){
                    $xvx = explode('-',$dateV[0][0]);
                    $xvf = cal_days_in_month($calendario, $xvx[1], $xvx[0]); 
                    $xxx = $xvx[0].'-'.$xvx[1].'-'.$xvf;
                    $xfecha = $xxx;
                } else {
                    $xfecha = $annovr.'-01-31';
                }
                
                $imp    = ($avaluo * $tarifa) / 1000;
                if(count($dateD)<=0){
                    $desc = 0;
                }else{
                    
                    $ff    = explode("-", $dateD[0][0]);
                    if($ff[0] == date("Y")){
                        if($ff[1]  == '01'){#Mes de Enero
                            $valor = ($imp * 20) / 100;
                        }elseif($ff[1] == '02'){#Mes de Febrero
                            $valor = ($imp * 15) / 100;
                        }elseif($ff[1] == '03'){#Marzo
                            $valor = ($imp * 10) / 100;
                        }
                    }else{
                        $valor = 0;
                    }
                    $desc = $valor  * -1;
                    
                }
                $desct  = $desc * -1;
                $intm   = $liquidar->liquidar_interesesP($imp, $xfecha, date("Y-m-d"),date("Y-m-d"));
                #$intm   = $liquidar->liquidar_intereses($imp, $xfecha, date("Y-m-d"));
                
                if($tipo == 6 || $tipo == 1){
                    $xbase = $avaluo.";1000";
                }else{
                    $xbase = $impuesto.";100";
                }
                $rbase  = explode(";", $xbase);
                $base   = $rbase[0];
                $por    = $rbase[1];
                if(empty($xbase)){
                    $car = 0;
                }else{
                    $car = ($base * $porcI) / $por;
                }
                #$intc  = $liquidar->liquidar_intereses($car, $xfecha, date("Y-m-d"));
                $intc   = $liquidar->liquidar_interesesP($car, $xfecha, date("Y-m-d"),date("Y-m-d"));
                if(empty($porcS)){
                    $bom = 0;
                }else{
                    $bom  = ($imp * $porcS) / 100;;
                }
                $papp   = $conexion->Listar("SELECT id_unico, formula FROM gr_concepto_predial 
                        WHERE id_concepto = 6 AND anno=$annovr");
                if($papp[0][1]){
                    $pap = 0;
                }else{
                    $pap = $papp[0][1];
                }
                $exdebc = $conexion->Listar("SELECT id_unico, formula FROM gr_concepto_predial 
                        WHERE id_concepto = 9 AND anno=$annovr");
                $exdeb =$exdebc[0][0];
                if(empty($exdeb)){
                    $deb = 0;
                }else{
                    $debc   = $conexion->Listar("SELECT id_unico, valor FROM gr_debido_valor 
                        WHERE predio = ".$row[$i][0]);
                    if(count($debc)>0){
                        $xxx = $debc[0][1];
                    } else{
                        $xxx = 0;
                    }
                    $deb   = $xxx;
                }
                $xxx = $imp + $intm + $car + $intc + $bom + $pap;
                if($xxx > $monto){
                    $xto   += $xxx;
                    $ximp  += $imp;
                    $xint  += $intm;
                    $xcar  += $car;
                    $xintc += $intc;
                    $xbom  += $bom;
                    $xpap  += $pap;
                }
                $valorD   = $xto;
                $valorImp = ($ximp + $xcar + $xbom + $xpap);
                $valorInt = ($xint + $xintc);
            }
            $valorL  = numtoletras($valorImp);
            $xValorI = numtoletras($valorInt);
            
            #***************************************************************************************#
            $section->addTextBreak();
            $section->addText("LIQUIDACION OFICIAL DEL IMPUESTO PREDIAL UNIFICADO LEY 44 DE 1990", array('bold' => true),'pStyle');
            $section->addText("RESOLUCION No. _______________ DEL ".date('d/m/Y'), array('bold' => true),'pStyle');
            $section->addTextBreak();
            $section->addText("La Secretaria de Hacienda Municipal de Paz de Rio Boyacá, "
                ."en uso de sus facultades constitucionales, legales y reglamentarias en especial "
                ."las que le confiere la Ley 1066 del 2006 y el Acuerdo No. 021 de 2008, y",array('bold' => false),'jStyle');
            $section->addTextBreak();
            $section->addText("CONSIDERANDO", array('bold' => true),'pStyle');
            $section->addTextBreak();
            $textrun = $section->addTextRun('jStyle');
            $textrun->addText("1.",array('bold' => true),'iStyle2');
            $textrun->addText('Que el artículo primero de la Ley 1066 establece lo siguiente: ',null,'iStyle2');
            $textrun->addText("“Artículo 1º. Gestión del recaudo de cartera pública. ",array('bold' => true, 'italic'=>true),'iStyle');
            $textrun->addText("Conforme a los principios que regulan la Administración Pública contenidos "
                ."en el artículo 209 de la Constitución Política, los servidores públicos que tengan a su "
                ."cargo el recaudo de obligaciones a favor del Tesoro Público deberán realizar su gestión "
                ."de manera ágil, eficaz, eficiente y oportuna, con el fin de obtener liquidez para el Tesoro "
                ."Público”.",array('italic' => true,'align' => 'both'),'iStyle2');
            $textrun->addTextBreak();
            $textrun = $section->addTextRun('jStyle');
            $textrun->addText("2.",array('bold' => true),'iStyle2');
            $textrun->addText("Que mediante Acuerdo 021 de 2008 fue reglamentado lo concerniente a las tarifas, "
                ."liquidación y/o pago del impuesto predial y demás cobros relacionados del MUNICIPIO DE PAZ DE "
                ."RIO.",array('bold' => false,'align' => 'both'),'iStyle2');
            $textrun->addTextBreak();
            $textrun = $section->addTextRun('jStyle');
            $textrun->addText("3.",array('bold' => true),'jStyle');
            $textrun->addText("Que, con base en la información suministrada por el Instituto Agustín Codazzi, "
                ."contenida en la base de datos del Municipio de Paz de Rio, se encuentra registrado el "
                ."inmueble que se identifica catastralmente con el Número ",array('bold' => false),'jStyle');
            $textrun->addText($row[$i][1],array('bold' => true),'jStyle');
            $textrun->addText(" ubicado en la ".$row[$i]['direccion']." a nombre de ",array('bold' => false),'jStyle');
            $textrun->addText($propros[0][0],array('bold' => true),'jStyle');
            
            $textrun->addTextBreak();
            $textrun = $section->addTextRun('jStyle');
            $textrun->addText("4.",array('bold' => true),'jStyle');
            $textrun->addText("Que el citado predio, a la fecha, presenta una deuda de ",array('bold' => false),'jStyle');
            $textrun->addText("$".mb_strtoupper($valorL)."",array('bold' => true),'jStyle');
            $textrun->addText(" correspondiente a capital y la suma de ",array('bold' => false),'jStyle');
            $textrun->addText("$".mb_strtoupper($xValorI),array('bold' => true),'jStyle');
            $textrun->addText(" correspondiente a intereses ",array('bold' => false),'jStyle');
            $section->addTextBreak();
            $textrun = $section->addTextRun('jStyle');
            $textrun->addText("5.",array('bold' => true),'iStyle2');
            $textrun->addText("Que la totalidad de intereses de Mora por retraso en el pago del Impuesto "
                ."Predial debe ser liquidada conforme a lo establecido en la ley 1066 de 2006, y de "
                ."conformidad con lo previsto por los artículos 634 y siguientes del Estatuto Tributario, "
                ."por lo cual es procedente reliquidación del mismo en el momento del pago.",array('align' => 'both','bold' => false),'iStyle2');
            $section->addTextBreak();
            $section->addTextBreak();
            $section->addText("RESUELVE", array('bold' => true),'pStyle');
            $section->addTextBreak();
            $textrun = $section->addTextRun('jStyle');
            $textrun->addText("ARTICULO PRIMERO. ",array('bold' => true),'jStyle');
            $textrun->addText("Practicar la siguiente Liquidación Oficial del Impuesto Predial Unificado "
                ."y Sobretasa Ambiental correspondiente al inmueble que se identifica catastralmente con "
                ."el código ",array('bold' => false),'jStyle');
            $textrun->addText($row[$i][1],array('bold' => true),'jStyle');
            $textrun->addText(" ubicado en la ",array('bold' => false),'jStyle');
            $textrun->addText($row[$i]['direccion'],array('bold' => true),'jStyle');
            $textrun->addText(" de conformidad con lo expuesto en la parte considerativa de este municipio.",array('bold' => false),'jStyle');
            $section->addTextBreak();
            $valorImp =0;$valorInt=0;
            $table   = $section->addTable(array('unit' => 'pct', 'align' => 'left'));
            $cell    = array('borderSize' => 6,'align' => 'both','spaceBefore' => 0, 'spaceAfter' => 0);
            $table->addRow();
            $c1 = $table->addCell(1000,$cell);
            $c1->addText("AÑO",array('bold' => true,'size'=>8),'pStyle');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText("AVALUO",array('bold' => true,'size'=>8),'pStyle');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText("% TARIFA",array('bold' => true,'size'=>8),'pStyle');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText("IMPUESTO PREDIAL",array('bold' => true,'size'=>8),'pStyle');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText("INTERES IMPUESTO",array('bold' => true,'size'=>8),'pStyle');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText("SOBRETASA AMBIENTAL",array('bold' => true,'size'=>8),'pStyle');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText("INTERES SOBRETASA",array('bold' => true,'size'=>8),'pStyle');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText("SOBRETASA BOMBERIL",array('bold' => true,'size'=>8),'pStyle');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText("PAPELERIA",array('bold' => true,'size'=>8),'pStyle');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText("TOTAL",array('bold' => true,'size'=>8),'pStyle');
            list ($ximp, $xdesc, $xint, $xcar, $xintc, $xbom, $xpap, $xdeb, $xto) = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
            $valorImp =0;$valorInt=0;
            for($a =0; $a < count($avaluos);$a++) {
                $avaluo = $avaluos[$a][1];
                $tarifa = $avaluos[$a][2];
                $annovr = $avaluos[$a][0];
                $tipo   = $avaluos[$a][3];
                $porcI  = $avaluos[$a][4];
                $porcS  = $avaluos[$a][5];
                $dateV   = $conexion->Listar("SELECT ven.fecha 
                    FROM gr_vencimiento as ven 
                    LEFT JOIN gf_mes as mes ON ven.mes = mes.id_unico 
                    WHERE ven.anno = $annovr 
                        AND tipo = 1 AND mes.numero = ".date('m'));
                
                $dateD   = $conexion->Listar("SELECT ven.fecha 
                    FROM gr_vencimiento as ven 
                    LEFT JOIN gf_mes as mes ON ven.mes = mes.id_unico 
                    WHERE ven.anno = $annovr 
                        AND tipo = 2 AND mes.numero = ".date('m'));
                if(count($dateV)>0){
                    $xvx = explode('-',$dateV[0][0]);
                    $xvf = cal_days_in_month($calendario, $xvx[1], $xvx[0]); 
                    $xxx = $xvx[0].'-'.$xvx[1].'-'.$xvf;
                    $xfecha = $xxx;
                } else {
                    $xfecha = $annovr.'-01-31';
                }
                $fecv = cal_days_in_month($calendario, date('m'), date('Y')); 
                $fechavv = date('Y').'-'.date('m').'-'.($fecv);
                $imp    = ($avaluo * $tarifa) / 1000;
                if(count($dateD)<=0){
                    $desc = 0;
                }else{
                    
                    $ff    = explode("-", $dateD[0][0]);
                    if($ff[0] == date("Y")){
                        if($ff[1]  == '01'){#Mes de Enero
                            $valor = ($imp * 20) / 100;
                        }elseif($ff[1] == '02'){#Mes de Febrero
                            $valor = ($imp * 15) / 100;
                        }elseif($ff[1] == '03'){#Marzo
                            $valor = ($imp * 10) / 100;
                        }
                    }else{
                        $valor = 0;
                    }
                    $desc = $valor  * -1;
                    
                }
                $desct  = $desc * -1;
                $intm   = $liquidar->liquidar_interesesP($imp, $xfecha, date("Y-m-d"),date("Y-m-d"));
                
                if($tipo == 6 || $tipo == 1){
                    $xbase = $avaluo.";1000";
                }else{
                    $xbase = $impuesto.";100";
                }
                $rbase  = explode(";", $xbase);
                $base   = $rbase[0];
                $por    = $rbase[1];
                if(empty($xbase)){
                    $car = 0;
                }else{
                    $car = ($base * $porcI) / $por;
                }
                $intc  = $liquidar->liquidar_interesesP($car, $xfecha, date("Y-m-d"),date("Y-m-d"));
                if(empty($porcS)){
                    $bom = 0;
                }else{
                    $bom  = ($imp * $porcS) / 100;;
                }
                $papp   = $conexion->Listar("SELECT id_unico, formula FROM gr_concepto_predial 
                        WHERE id_concepto = 6 AND anno=$annovr");
                if(empty($papp[0][1])){
                    $pap = 0;
                }else{
                    $pap = $papp[0][1];
                }
                $exdebc = $conexion->Listar("SELECT id_unico, formula FROM gr_concepto_predial 
                        WHERE id_concepto = 9 AND anno=$annovr");
                $exdeb =$exdebc[0][0];
                if(empty($exdeb)){
                    $deb = 0;
                }else{
                    $debc   = $conexion->Listar("SELECT id_unico, valor FROM gr_debido_valor 
                        WHERE predio = ".$row[$i][0]);
                    if(count($debc)>0){
                        $xxx = $debc[0][1];
                    } else{
                        $xxx = 0;
                    }
                    $deb   = $xxx;
                }
                $xxx = $imp + $intm + $car + $intc + $bom + $pap;
                if($xxx > $monto){
                    $xto   += $xxx;
                    $ximp  += $imp;
                    $xint  += $intm;
                    $xcar  += $car;
                    $xintc += $intc;
                    $xbom  += $bom;
                    $xpap  += $pap;
                    $table->addRow();
                    $c1 = $table->addCell(1000,$cell);
                    $c1->addText($annovr,array('bold' => false,'size'=>8),'iStyle2');
                    $c1 = $table->addCell(1000,$cell);
                    $c1->addText($avaluo,array('bold' => false,'size'=>8),'iStyle2');
                    $c1 = $table->addCell(1000,$cell);
                    $c1->addText($tarifa,array('bold' => false,'size'=>8),'iStyle2');
                    $c1 = $table->addCell(1000,$cell);
                    $c1->addText($imp,array('bold' => false,'size'=>8),'iStyle2');
                    $c1 = $table->addCell(1000,$cell);
                    $c1->addText($intm,array('bold' => false,'size'=>8),'iStyle2');
                    $c1 = $table->addCell(1000,$cell);
                    $c1->addText($car,array('bold' => false,'size'=>8),'iStyle2');
                    $c1 = $table->addCell(1000,$cell);
                    $c1->addText($intc,array('bold' => false,'size'=>8),'iStyle2');
                    $c1 = $table->addCell(1000,$cell);
                    $c1->addText($bom,array('bold' => false,'size'=>8),'iStyle2');
                    $c1 = $table->addCell(1000,$cell);
                    $c1->addText($pap,array('bold' => false,'size'=>8),'iStyle2');
                    $c1 = $table->addCell(1000,$cell);
                    $c1->addText($xxx,array('bold' => false,'size'=>8),'iStyle2');
                }
                $valorD   = $xto;
                $valorImp = ($ximp + $xcar + $xbom + $xpap);
                $valorInt = ($xint + $xintc);
            }
            $table->addRow();
            $cellColSpan     = array('gridSpan' => 3,'borderSize' => 6,'align' => 'center','spaceBefore' => 0, 'spaceAfter' => 0);
            $c1 = $table->addCell(3000,$cellColSpan);
            $c1->addText('TOTALES',array('bold' => true,'size'=>8,'spaceBefore' => 0, 'spaceAfter' => 0),'iStyle2');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText(number_format($ximp, 0, ',', '.'),array('bold' => true,'size'=>8),'iStyle2');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText(number_format($xint, 0, ',', '.'),array('bold' => true,'size'=>8),'iStyle2');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText(number_format($xcar, 0, ',', '.'),array('bold' => true,'size'=>8),'iStyle2');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText(number_format($xintc, 0, ',', '.'),array('bold' => true,'size'=>8),'iStyle2');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText(number_format($xbom, 0, ',', '.'),array('bold' => true,'size'=>8),'iStyle2');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText(number_format($xpap, 0, ',', '.'),array('bold' => true,'size'=>8),'iStyle2');
            $c1 = $table->addCell(1000,$cell);
            $c1->addText(number_format($xto, 0, ',', '.'),array('bold' => true,'size'=>8),'iStyle2');
            
            $section->addTextBreak();
            $textrun = $section->addTextRun('jStyle');
            $textrun->addText("PARAGRAFO. ",array('bold' => true),'jStyle');
            $textrun->addText("En virtud de que la fecha para el pago oportuno de la obligación se "
                ."encuentra vencida sin que se haya obtenido el recaudo de los recursos, la presente "
                ."liquidación oficial presta mérito ejecutivo; la cuantía establecida incluye intereses "
                ."de mora liquidados a la fecha, por lo cual en el momento del pago procede la reliquidación "
                ."de estos valores.",array('bold' => false),'jStyle');
            $section->addTextBreak();
            
            $textrun = $section->addTextRun('jStyle');
            $textrun->addText("ARTICULO SEGUNDO. ",array('bold' => true),'jStyle');
            $textrun->addText("Notificar la presente en los términos de los artículos 565 y siguientes del "
                ."Estatuto Tributario Nacional.",array('bold' => false),'jStyle');
            $section->addTextBreak();
            
            $textrun = $section->addTextRun('jStyle');
            $textrun->addText("ARTICULO TERCERO. ",array('bold' => true),'jStyle');
            $textrun->addText("Contra la presente resolución procede el Recurso de Reconsideración, "
                ."el cual debe ser interpuesto dentro de los dos meses siguientes a la fecha de "
                ."notificación de la misma ante la Secretaría de Hacienda Municipal, de conformidad "
                ."con lo dispuesto por el Art. 720 del Estatuto Tributario Nacional.",array('bold' => false),'jStyle');
            $section->addTextBreak();
            
            $textrun = $section->addTextRun('jStyle');
            $textrun->addText("ARTICULO CUARTO. ",array('bold' => true),'jStyle');
            $textrun->addText("Una vez ejecutoriada la presente Resolución, y solo de ser necesario, "
                ."iníciese el proceso COACTIVO.",array('bold' => false),'jStyle');
            $section->addTextBreak();
            $section->addTextBreak();
            $section->addText("NOTIFÍQUESE Y CÚMPLASE", array('bold' => true),'pStyle');
            $section->addTextBreak();
            $section->addTextBreak();
            $section->addText("LUDY MARISOL AVENADAÑO GARCIA",array('bold' => true),'pStyle');
            $section->addText("Secretaria de Hacienda Municipal",array('bold' => true),'pStyle');
            $section->addText("Funcionaria Ejecutora",array('bold' => true),'pStyle');

            $section->addTextBreak();
            $section->addTextBreak();

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


    $filename = "ResolucionesCobro.doc";
    $word->save($filename,"Word2007");

    header('Content-Description: File Transfer');
    header('Content-type: application/force-download');
    header('Content-Disposition: attachment; filename='.basename('ResolucionesCobro.doc'));
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: '.filesize('ResolucionesCobro.doc'));
    readfile('ResolucionesCobro.doc');
    unlink('ResolucionesCobro.doc');

#*************************************************************************#
function obtnerultimoannopago($predio){
    global $conexion;
    $ult = $conexion->Listar("SELECT max(anno) FROM gr_ultimo_ano_pago WHERE predio = $predio");
    return $ult[0][0];
}


function numtoletras($xcifra)
{
    $xarray = array(0 => "Cero",
        1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
        "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
        "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
        100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
    );
//
    $xcifra = trim($xcifra);
    $xlength = strlen($xcifra);
    $xpos_punto = strpos($xcifra, ".");
    $xaux_int = $xcifra;
    $xdecimales = "00";
    if (!($xpos_punto === false)) {
        if ($xpos_punto == 0) {
            $xcifra = "0" . $xcifra;
            $xpos_punto = strpos($xcifra, ".");
        }
        $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
        $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
    }

    $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
    $xcadena = "";
    for ($xz = 0; $xz < 3; $xz++) {
        $xaux = substr($XAUX, $xz * 6, 6);
        $xi = 0;
        $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
        $xexit = true; // bandera para controlar el ciclo del While
        while ($xexit) {
            if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
                break; // termina el ciclo
            }

            $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
            $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
            for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                switch ($xy) {
                    case 1: // checa las centenas
                        if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
                            
                        } else {
                            $key = (int) substr($xaux, 0, 3);
                            if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                                $xseek = $xarray[$key];
                                $xsub = subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                                if (substr($xaux, 0, 3) == 100)
                                    $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                            }
                            else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                $key = (int) substr($xaux, 0, 1) * 100;
                                $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                $xcadena = " " . $xcadena . " " . $xseek;
                            } // ENDIF ($xseek)
                        } // ENDIF (substr($xaux, 0, 3) < 100)
                        break;
                    case 2: // checa las decenas (con la misma lógica que las centenas)
                        if (substr($xaux, 1, 2) < 10) {
                            
                        } else {
                            $key = (int) substr($xaux, 1, 2);
                            if (TRUE === array_key_exists($key, $xarray)) {
                                $xseek = $xarray[$key];
                                $xsub = subfijo($xaux);
                                if (substr($xaux, 1, 2) == 20)
                                    $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                $xy = 3;
                            }
                            else {
                                $key = (int) substr($xaux, 1, 1) * 10;
                                $xseek = $xarray[$key];
                                if (20 == substr($xaux, 1, 1) * 10)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                            } // ENDIF ($xseek)
                        } // ENDIF (substr($xaux, 1, 2) < 10)
                        break;
                    case 3: // checa las unidades
                        if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada
                            
                        } else {
                            $key = (int) substr($xaux, 2, 1);
                            $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                            $xsub = subfijo($xaux);
                            $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                        } // ENDIF (substr($xaux, 2, 1) < 1)
                        break;
                } // END SWITCH
            } // END FOR
            $xi = $xi + 3;
        } // ENDDO

        if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
            $xcadena.= " DE";

        if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
            $xcadena.= " DE";

        // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
        if (trim($xaux) != "") {
            switch ($xz) {
                case 0:
                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                        $xcadena.= "UN BILLON ";
                    else
                        $xcadena.= " BILLONES ";
                    break;
                case 1:
                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                        $xcadena.= "UN MILLON ";
                    else
                        $xcadena.= " MILLONES ";
                    break;
                case 2:
                    if ($xcifra < 1) {
                        $xcadena = "CERO PESOS $xdecimales M.C.";
                    }
                    if ($xcifra >= 1 && $xcifra < 2) {
                        $xcadena = "UN PESO $xdecimales M.C.";
                    }
                    if ($xcifra >= 2) {
                        $xcadena.= " PESOS $xdecimales M.C.";
                    }
                    break;
            } // endswitch ($xz)
        } // ENDIF (trim($xaux) != "")
        // ------------------      en este caso, para México se usa esta leyenda     ----------------
        $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
        $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
        $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
        $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
        $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
        $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
        $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
    } // ENDFOR ($xz)
    return trim($xcadena);
}

// END FUNCTION

function subfijo($xx)
{ // esta función regresa un subfijo para la cifra
    $xx = trim($xx);
    $xstrlen = strlen($xx);
    if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
        $xsub = "";
    //
    if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
        $xsub = "MIL";
    //
    return $xsub;
}