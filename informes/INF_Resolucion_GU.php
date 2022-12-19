<?php
session_start();
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/ConexionPDO.php';
require'../Conexion/conexion.php';
require'../jsonPptal/funcionesPptal.php';
require'../numeros_a_letras.php';
ini_set('max_execution_time', 0);
session_start();
$con = new ConexionPDO();
ob_start();

$res_log    = $_SESSION['usuario_tercero'];
$anno       = $_SESSION['anno'];

#***********RECEPCION VARIABLES***********#

$id_org = $_POST['organismo'];
$tipg   = $_POST['tipo'];

switch ($tipg):
    #** Mes **#
    case (1):
        $mes =$_REQUEST['mes'];
        $fecha_div = explode("-",$mes);
        $mesS = (int) $fecha_div[0];
        $anoS = $fecha_div[1];
        $sql_inform="Select
        com.resolucion,
        com.comparendo,
        com.fecha_comparendo,
        concat_ws(' ',com.nombres,com.apellidos) as conductor,
        com.cedula,
        com.infraccion as cod_infraccion,
        tc.nombre as nom_infraccion,
        tc.sigla_sancion,
        tc.sancion,
        tc.valor_sancion,
        org.nombre as nom_org,
        com.placa,
        com.fecha_res,
        com.fecha_aviso,
        com.tipo_documento,
        com.n_fecha_resolucion,
        com.valor

        from gu_resoluciones com
        left join gu_tipo_comparendo tc on tc.codigo=com.infraccion
        left join gf_sucursal org on org.id_unico=com.sucursal 
        where com.sucursal='$id_org' and  (MONTH(com.fecha_comparendo))='$mesS' 
        and (YEAR(com.fecha_comparendo))='$anoS'";
    break;
    #** Día **#
    case (2):
        $fecha_Res = fechaC($_REQUEST["dias"]);
        $sql_inform="Select
            com.resolucion,
            com.comparendo,
            com.fecha_comparendo,
            concat_ws(' ',com.nombres,com.apellidos) as conductor,
            com.cedula,
            com.infraccion as cod_infraccion,
            tc.nombre as nom_infraccion,
            tc.sigla_sancion,
            tc.sancion,
            tc.valor_sancion,
            org.nombre as nom_org,
            com.placa,
            com.fecha_res,
            com.fecha_aviso,
            com.tipo_documento,
            com.n_fecha_resolucion,
            com.valor 
            
            from gu_resoluciones com
            left join gu_tipo_comparendo tc on tc.codigo=com.infraccion
            left join gf_sucursal org on org.id_unico=com.sucursal 
            where com.sucursal='$id_org' and com.fecha_comparendo='$fecha_Res'";
    break;
    #** Comparendo **#
    case (3):
        $n_comp =$_POST['comparendo'];
        $sql_inform="Select
            com.resolucion,
            com.comparendo,
            com.fecha_comparendo,
            concat_ws(' ',com.nombres,com.apellidos) as conductor,
            com.cedula,
            com.infraccion as cod_infraccion,
            tc.nombre as nom_infraccion,
            tc.sigla_sancion,
            tc.sancion,
            tc.valor_sancion,
            org.nombre as nom_org,
            com.placa,
            com.fecha_res,
            com.fecha_aviso,
            com.tipo_documento,
            com.n_fecha_resolucion,
            com.valor
            
            from gu_resoluciones com
            left join gu_tipo_comparendo tc on tc.codigo=com.infraccion
            left join gf_sucursal org on org.id_unico=com.sucursal 
            where com.sucursal='$id_org' and com.comparendo='$n_comp'";
    break;
    default :
        $sql_inform="";
    break;
endswitch;
#echo $sql_inform;
##CONSULTA DATOS COMPAÑIA##
$compa=$_SESSION['compania'];
$comp="SELECT
  t.razonsocial,
  t.numeroidentificacion,
  t.digitoverficacion,
  t.ruta_logo,
  d.direccion,
  tel.valor
FROM
  gf_tercero t
LEFT JOIN
  gf_direccion d ON d.tercero = t.id_unico
LEFT JOIN
  gf_telefono tel ON tel.tercero = t.id_unico
WHERE
  t.id_unico =$compa";
$comp = $mysqli->query($comp);
$comp = mysqli_fetch_row($comp);
$nombreCompania = $comp[0];

//consulta de nombre y firma de la persona logeada
$sql_ter_log="SELECT
concat_ws(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos) as nombre_ter_log,
t.firma
FROM  gf_tercero t
WHERE  t.id_unico =$res_log";
$ter_log = $mysqli->query($sql_ter_log);
$t_logeado = mysqli_fetch_row($ter_log);
$nombretercero_Logeado = $t_logeado[0];
$firma_Logeado = $t_logeado[1];

$inform_resol = $mysqli->query($sql_inform);
 class PDF extends FPDF
        {
        function Header()
        { 

            $date1=$fecha1;    
            $date2=$fecha2;    
            $numpaginas=$this->PageNo();   

            $this->SetY(10);

             // $this->Image('../'.$ruta,20,8,20);
                 $this->Image('../images/encabezado_GU.png',20,10,180);
            
            
            $this->Ln(45);

            //cuadro del acuerdo 
            }      
            
            function Footer()
            {
            // Posición: a 1,5 cm del final
               $this->Image('../images/pie_pagina_GU.png',0,265,210);
            }
            

        }
$pdf = new PDF('P','mm','Letter');      
$pdf->AliasNbPages();
while ($res = mysqli_fetch_row($inform_resol)) {
    if (empty($res[0])) {
        $resultado = 2;
    } else {

        $resultado = 1;
        $pre = $res[0];
        $n_res = '';

        $nresol = '';
        $compan = $res[1];
        $fcom = $res[2];
        $f_compan = DateTime::createFromFormat('Y-m-d', "$fcom");
        $f_compan = $f_compan->format('d/m/Y');
        $conductor = strtoupper($res[3]);
        $ced = $res[4];
        $cod_inf = $res[5];
        $nom_inf = $res[6];
        $sigla_san = $res[7];
        $san = $res[8];
        //$res[9]
        $vlr_san = number_format($res[16], 2, '.', ',');
        $nom_vlr_san = numtoletras($res[16]);
        $organ = $res[10];
        $placa = $res[11];
        $freso = $res[12];
        $fres = DateTime::createFromFormat('Y-m-d', "$freso");
        $fres = $fres->format('d/m/Y');
        $linc = '';
        $fav = $res[13];
        $td = $res[14];
        $resol_aviso = $res[15];
        //$ms = explode("/", $mes); 
        if(empty($resol_aviso)){
            $resol_aviso='';
        }else{
            $rsl=explode("DEL", $resol_aviso);
            //echo $resol_aviso=$rsl[0];
        }
        if (empty($fav)) {
            $f_aviso='';
        } else {
            $f_aviso = DateTime::createFromFormat('Y-m-d', "$fav");
            $f_aviso = $f_aviso->format('d/m/Y');
        }
        if (empty($comp[2])) {
            $nitcompania = $comp[1];
        } else {
            $nitcompania = $comp[1] . ' - ' . $comp[2];
        }
        $ruta = $comp[3];
        $direccion = $comp[4];
        $telefono = $comp[5];
        $usuario = $_SESSION['usuario'];
    }
    if ($resultado == 1) {
        //echo '  fecha aviso '.$f_aviso.'  resol  '.$pre.'  conductor '.$conductor;
        if (!empty($f_aviso)) {
            
            $pdf->SetFont('Arial', '', 10);
            $pdf->AddPage();
            
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(180, 1, utf8_decode('AUDIENCIA PÚBLICA'), 0, 0, 'C');
            $pdf->ln(2);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 10, utf8_decode('EXPEDIENTE:'), 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(95, 10, utf8_decode(ucwords(mb_strtoupper('                  ' . $pre . $nresol . ' DEL ' . $fres))), 0, 0, 'L');
            $pdf->ln(2);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 15, utf8_decode('COMPARENDO:'), 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(95, 15, utf8_decode(ucwords(mb_strtoupper('                  ' . $compan))), 0, 0, 'L');
            $pdf->ln(5);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(42, 15, utf8_decode('INFRACCIÓN:'), 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(85, 15, utf8_decode(ucwords(mb_strtoupper('                  ' . $cod_inf . ' (' . $nom_inf . ')'))), 0, 0, 'L');
            $pdf->ln(5);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 15, utf8_decode('SEÑOR(A):'), 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(95, 15, utf8_decode(ucwords(mb_strtoupper('                  ' . $conductor))), 0, 0, 'L');
            $pdf->ln(5);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 15, utf8_decode('DOCUMENTO DE IDENTIFICACIÓN:'), 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(95, 15, utf8_decode(ucwords(mb_strtoupper('                  ' . $ced))), 0, 0, 'L');
            
            $pdf->ln(5);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 15, utf8_decode('PLACA:'), 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(90, 15, utf8_decode(ucwords(mb_strtoupper('                   ' . $placa))), 0, 0, 'L');

            $pdf->ln(15);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(180, 5, utf8_decode('En el municipio de ' . $organ . ' a los ' . $fres . ' '
                            . ' en aplicación a los artículos 3, 134 y 135 de la Ley 769 de 2002 y cumplido el '
                            . 'término señalado en su artículo 136 -modificado por el artículo 24 de la Ley 1383 de 2010 '
                            . 'modificado este a su vez por el artículo 205 del Decreto Ley 019 de 2012- la Autoridad de '
                            . 'Tránsito, declara legalmente abierta la presente diligencia de audiencia pública para emitir '
                            . 'la decisión que en derecho corresponda, dejando constancia de la no comparecencia '
                            . 'del señor(a) ' . $conductor . ' identificado(a) con '.$td.' ' . $ced . '.'), 0, 'J');

            $pdf->ln(2);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(180, 5, utf8_decode('DESARROLLO PROCESAL'), 0, 0, 'C');
            $pdf->ln(10);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(180, 5, utf8_decode('En el Municipio de Soacha, el día ' . $f_compan . ' le fue impuesta la orden '
                            . 'de comparendo ' . $compan . ' captado por medios tecnológicos, al señor(a) ' . $conductor . ' '
                            . 'identificado(a) con '.$td.' ' . $ced . ', por la infracción ' . $cod_inf . ' - artículo '
                            . '131 de la Ley 769 de 2002, modificado por la Ley 1383 de 2010-, norma que reza: "' . $cod_inf . '. ' . $nom_inf . '" '), 0, 'J');
            $pdf->ln(2);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(180, 5, utf8_decode('Que, la Orden de Comparendo No. ' . $compan . ' del ' . $f_compan . ' fue enviada dentro de los términos '
                            . 'y a la dirección suministrada en el Registro Único Nacional de Transito RUNT por el propietario del vehículo de placa ' . $placa . ' conforme '
                            . 'lo establece el artículo 8 de la ley 1843 de 2017.   '), 0, 'J');
            $pdf->ln(2);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(180, 5, utf8_decode('Que, pese a enviada la orden de comparendo No. ' . $compan . ' del ' . $f_compan . ' conforme a la '
                            . 'ley, la empresa de mensajería reporta devolución de la misma, razón por la cual se procedió a notificar por aviso el día ' . $f_aviso . '  en '
                            . 'los términos establecidos en el artículo 8 de la Ley 1843 de 2017 en concordancia con los artículos 67 y 69 del Código de Procedimiento '
                            . 'Administrativo y de lo Contencioso Administrativo.'), 0, 'J');
            $pdf->ln(2);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(180, 5, utf8_decode('Que, en aras de garantizar los derechos de audiencia, defensa y contradicción, componentes del derecho '
                            . 'fundamental al debido proceso que le asiste al señor(a) ' . $conductor . ' identificado(a) con '.$td.' ' . $ced . ' , se dio '
                            . 'aplicación al artículo 205 del Decreto Ley 019 de 2012, que modificó el artículo 136 del Código Nacional de Tránsito Terrestre y que señala que: '), 0, 'J');
            $pdf->SetFont('Times', 'I');
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode('"(...) Si el contraventor no compareciere sin justa causa comprobada dentro de los cinco (5) días hábiles siguientes a la notificación del comparendo, la autoridad de tránsito, después de treinta (30) días calendario de ocurrida la presunta infracción, seguirá el proceso, entendiéndose que queda vinculado al mismo, fallándose en audiencia pública y notificándose en estrados."'), 0, 'J');
            $pdf->ln(1);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(180, 5, utf8_decode('PRUEBAS'), 0, 0, 'C');
            $pdf->ln(7);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(180, 5, utf8_decode("De acuerdo con el artículo 162 del C.N.T.  y con fundamento en el Código General del Proceso -Ley 1564 de 2012-, se DECRETA E INCORPORA las siguientes pruebas:"), 0, 'J');
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode(" -  Orden de Comparendo # $compan"), 0, 'J');
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode(" -  Guía de envío de la referida orden de comparendo. "), 0, 'J');
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode(" -  Constancia de desfijación resoluciones notificación por aviso No. $resol_aviso"), 0, 'J');
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode(" -  Consulta en el RUNT y SIMIT de los datos de $conductor."), 0, 'J');

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Ln(5);
            $pdf->SetX(20);
            $pdf->Cell(180, 3, utf8_decode('FUNDAMENTOS Y ANÁLISIS'), 0, 0, 'C');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Ln(5);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode('El artículo 136 del C.N.T. -modificado por la Ley 1383 de 2010 y el Decreto '
                            . 'Ley 019 de 2012- en concordancia con el procedimiento establecido en el artículo 8 de la ley '
                            . '1843 de 2017 establece que si el inculpado rechaza la comisión de la infracción deberá comparecer '
                            . 'ante la autoridad de tránsito para que éste, en audiencia pública, decrete las pruebas '
                            . 'conducentes que le sean solicitadas y las de oficio que considere útiles, lo cual supone '
                            . 'la imposición de una carga procesal y probatoria que debe asumir el presunto infractor. '
                            . 'En el presente caso, ante la ausencia del señor(a) ' . $conductor . '  identificado(a) con cédula '
                            . 'de ciudadanía ' . $ced . ' , éste deja al azar la decisión respecto a su responsabilidad '
                            . 'contravencional lo que conlleva a la administración a decidir sobre la situación jurídica '
                            . 'del procesado junto con los elementos probatorios que obran en el expediente.'), 0, 'J');
            $pdf->Ln(2);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode('Así las cosas, queda plenamente probado que el señor(a) ' . $conductor . '  identificado(a) con cédula '
                            . 'de ciudadanía ' . $ced . ' , infringió las normas de tránsito el día ' . $f_compan . ' con el '
                            . 'vehículo de  ' . $placa . ' , al momento de ser captado por medios tecnológicos para la '
                            . 'detección de infracciones de tránsito, por lo que fue impuesta la orden de comparendo ' . $compan . '.'), 0, 'J');
            $pdf->Ln(2);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(180, 10, utf8_decode('NORMAS INFRINGIDAS'), 0, 0, 'C');
            $pdf->ln(7);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(180, 5, utf8_decode('El actuar desplegado por el señor(a) se ajusta a las conductas descritas en los preceptos normativos '
                            . 'referidos y particularmente el contenido de la siguiente normatividad:'), 0, 'J');
            $pdf->ln(1);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->MultiCell(180, 5, utf8_decode("-	Ley 769 de 2002"), 0, 'J');
            $pdf->ln(1);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            //$pdf->SetFont('Times','I');
            $pdf->SetFont('Times', 'BIU');
            $pdf->MultiCell(180, 5, utf8_decode("Artículo 131. Multas"), 0, 'J');
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode('Los infractores de las normas de tránsito serán sancionados con la imposición de multas, '
                            . 'de acuerdo con el tipo de infracción, así: '), 0, 'J');
            $pdf->ln(1);
            $pdf->SetX(20);
            $pdf->SetFont('Times', 'I');
            $pdf->MultiCell(180, 5, utf8_decode('C. Será sancionado con ' . $san . ' el conductor y/o propietario de '
                            . 'un vehículo automotor que incurra en cualquiera de las siguientes infracciones:'), 0, 'J');
            $pdf->SetFont('Times', 'I');
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode('"' . $cod_inf . ' ' . $nom_inf . '"'), 0, 'J');
            $pdf->ln(1);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(180, 5, utf8_decode('Por otro lado, en virtud del artículo 139 del C.N.T., y '
                            . 'parágrafos 3 de artículo 8 de ley 1843 de 2017, todas las providencias que se dicten '
                            . 'dentro del proceso contravencional serán notificados en estrados, concordante con el '
                            . 'numeral 2 del artículo 67 de la ley 1437 de 2011, no sin antes informar que contra la '
                            . 'misma no procede ningún recurso según el artículo 142 del C.N.T., conforme a las cuantías '
                            . 'establecidas en el artículo 124 ibídem-, por lo que la presente decisión queda en firme.'), 0, 'J');
            $pdf->ln(1);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode('Por lo anterior, esta Dirección de Procesos Administrativos, '), 0, 'J');

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Ln(5);
            $pdf->SetX(20);
            $pdf->Cell(180, 3, utf8_decode('RESUELVE:'), 0, 0, 'C');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Ln(5);
            $pdf->SetX(20);
            //$html= '<b>'.'PRIMERO.'.'</b>'.'Declarar'.'<b>'.'CONTRAVENTOR de las normas de tránsito '.'</br>'.'a señor(a) '.$conductor.'identificado(a) con Cédula de Ciudadanía No.'.$ced.',  conforme a los cargos formulados en la Orden de Comparendo No. '.$compan.' de '.$f_compan.' (Código C29 del artículo 131 de la Ley 769 de 2002)';
            //$pdf->Write($html);
            //$pdf->Output();
            $pdf->MultiCell(180, 5, utf8_decode('PRIMERO: Declarar CONTRAVENTOR de las normas de tránsito a el(la) señor(a) ' . $conductor . ' identificado(a) con '.$td.' No. ' . $ced . ', por contravenir la infracción codificada con el literal ' . $cod_inf . ' que consiste en "' . $cod_inf . ' ' . $nom_inf . '" artículo 131 de la Ley 769 de 2002, modificado por la Ley 1383 de 2010.'), 0, 'J');
            $pdf->Ln(1);
            $pdf->SetX(20);
            //$nom_vlr_san
            $pdf->MultiCell(180, 5, utf8_decode('SEGUNDO: Imponer una multa al contraventor(a) de ' . $san . ', equivalentes a ' . $nom_vlr_san . '  $ ' . $vlr_san . ' pagaderos a favor de la Secretaría de Movilidad de Soacha de conformidad con la parte motiva de este proveído.'), 0, 'J');
            $pdf->Ln(1);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode("TERCERO: En firme la presente decisión, remítase el expediente al grupo de  Cobro Coactivo para lo de su competencia, o en caso de pago archívese el expediente."), 0, 'J');
            $pdf->Ln(1);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode("CUARTO: Remítase copia del expediente a la Unión Temporal - Servicios Especializados de Registro y Tránsito - Soacha -UT SERT SOACHA- a fin de que realice la inscripción de las sanciones en las plataformas SIMIT y RUNT."), 0, 'J');
            $pdf->Ln(1);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode("QUINTO: De conformidad con el artículo 142 del C.N.T., contra la presente providencia no procede recurso alguno en razón a las cuantías establecidas en el artículo 134 ibidem. En cumplimiento del artículo 161 del C.N.T., se deja constancia de la celebración efectiva de la audiencia. "), 0, 'J');
            $pdf->Ln(1);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode("No siendo otro el motivo de la presente, la misma se da por terminada y se notifica conforme lo establece el artículo 139 de la Ley 769 de 2002."), 0, 'J');
            $pdf->Ln(3);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(180, 3, utf8_decode('Notifíquese y cúmplase,'), 0, 0, 'L');
            $pdf->Ln(15);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
          //  $pdf->Image('../'.$firma_Logeado,20,8,15);
            if(empty($firma_Logeado)){

            }else{
                $pdf->Image('../'.$firma_Logeado, 90, 120, 35, 30);    
            }
            
            $pdf->Cell(180, 25, utf8_decode('RODRIGO SEBASTIÁN HERNÁNDEZ ALONSO'), 0, 0, 'C');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Ln(1);
            $pdf->SetX(20);
            $pdf->Cell(180, 30, utf8_decode('Director de Procesos Administrativos'), 0, 0, 'C');
            $pdf->Ln(1);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(180, 35, utf8_decode('SECRETARÍA DE MOVILIDAD DE SOACHA'), 0, 0, 'C');

            $pdf->SetFont('Arial', '', 7);
            $pdf->Ln(22);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode("Elaboró: Walter Aldana -Especialista legal- UT SERT"), 0, 'J');
            $pdf->Ln(0.05);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode("Revisó: Gina Gutiérrez - Profesional Universitaria - DPA SM"), 0, 'J');

        } else {

            $pdf->SetFont('Arial', '', 10);
            $pdf->AddPage();

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(180, 1, utf8_decode('AUDIENCIA PÚBLICA'), 0, 0, 'C');
            $pdf->ln(2);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 10, utf8_decode('EXPEDIENTE:'), 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(95, 10, utf8_decode(ucwords(mb_strtoupper('                  ' . $pre . $nresol . ' DEL ' . $fres))), 0, 0, 'L');
            $pdf->ln(2);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 15, utf8_decode('COMPARENDO:'), 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(95, 15, utf8_decode(ucwords(mb_strtoupper('                  ' . $compan))), 0, 0, 'L');
            $pdf->ln(5);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(42, 15, utf8_decode('INFRACCIÓN:'), 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(85, 15, utf8_decode(ucwords(mb_strtoupper('                  ' . $cod_inf . ' (' . $nom_inf . ')'))), 0, 0, 'L');
            $pdf->ln(5);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 15, utf8_decode('SEÑOR(A):'), 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(95, 15, utf8_decode(ucwords(mb_strtoupper('                  ' . $conductor))), 0, 0, 'L');
            $pdf->ln(5);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 15, utf8_decode('DOCUMENTO DE IDENTIFICACIÓN:'), 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(95, 15, utf8_decode(ucwords(mb_strtoupper('                  ' . $ced))), 0, 0, 'L');
            $pdf->ln(5);
            $pdf->SetX(20);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 15, utf8_decode('PLACA:'), 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(90, 15, utf8_decode(ucwords(mb_strtoupper('                   ' . $placa))), 0, 0, 'L');

            $pdf->ln(15);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(180, 5, utf8_decode('En el municipio de ' . $organ . ' a los ' . $fres . ' '
                            . ' en aplicación a los artículos 3, 134 y 135 de la Ley 769 de 2002 y cumplido el '
                            . 'término señalado en su artículo 136 -modificado por el artículo 24 de la Ley 1383 de 2010 '
                            . 'modificado este a su vez por el artículo 205 del Decreto Ley 019 de 2012- la Autoridad de '
                            . 'Tránsito, declara legalmente abierta la presente diligencia de audiencia pública para emitir '
                            . 'la decisión que en derecho corresponda, dejando constancia de la no comparecencia '
                            . 'del señor(a) ' . $conductor . ' identificado(a) con '.$td.' ' . $ced . '.'), 0, 'J');

            $pdf->ln(2);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(180, 5, utf8_decode('DESARROLLO PROCESAL'), 0, 0, 'C');
            $pdf->ln(10);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(180, 5, utf8_decode('En el Municipio de Soacha, el día ' . $f_compan . ' le fue impuesta la orden '
                            . 'de comparendo ' . $compan . ' captado por medios tecnológicos, al señor(a) ' . $conductor . ' '
                            . 'identificado(a) con '.$td.' ' . $ced . ', por la infracción ' . $cod_inf . ' - artículo '
                            . '131 de la Ley 769 de 2002, modificado por la Ley 1383 de 2010-, norma que reza: "' . $cod_inf . '. ' . $nom_inf . '" '), 0, 'J');
            $pdf->ln(2);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(180, 5, utf8_decode('Que, la Orden de Comparendo No. ' . $compan . ' del ' . $f_compan . ' fue enviada dentro de los términos '
                            . 'y a la dirección suministrada en el Registro Único Nacional de Transito RUNT por el propietario del vehículo de placa ' . $placa . ' conforme '
                            . 'lo establece el artículo 8 de la ley 1843 de 2017.   '), 0, 'J');
            $pdf->ln(2);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(180, 5, utf8_decode('Que, en aras de garantizar los derechos de audiencia, defensa y contradicción, componentes del derecho '
                            . 'fundamental al debido proceso que le asiste al señor(a) ' . $conductor . ' identificado(a) con '.$td.' ' . $ced . ' , se dio '
                            . 'aplicación al artículo 205 del Decreto Ley 019 de 2012, que modificó el artículo 136 del Código Nacional de Tránsito Terrestre y que señala que: '), 0, 'J');
            $pdf->SetFont('Times', 'I');
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode('"(...) Si el contraventor no compareciere sin justa causa comprobada dentro de los cinco (5) días hábiles siguientes a la notificación del comparendo, la autoridad de tránsito, después de treinta (30) días calendario de ocurrida la presunta infracción, seguirá el proceso, entendiéndose que queda vinculado al mismo, fallándose en audiencia pública y notificándose en estrados."'), 0, 'J');
            $pdf->ln(1);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(180, 5, utf8_decode('PRUEBAS'), 0, 0, 'C');
            $pdf->ln(7);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(180, 5, utf8_decode("De acuerdo con el artículo 162 del C.N.T.  y con fundamento en el Código General del Proceso -Ley 1564 de 2012-, se DECRETA E INCORPORA las siguientes pruebas:"), 0, 'J');
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode(" -  Orden de Comparendo # $compan"), 0, 'J');
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode(" -  Guía de envío de la referida orden de comparendo. "), 0, 'J');
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode(" -  Consulta en el RUNT y SIMIT de los datos de $conductor."), 0, 'J');

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Ln(5);
            $pdf->SetX(20);
            $pdf->Cell(180, 3, utf8_decode('FUNDAMENTOS Y ANÁLISIS'), 0, 0, 'C');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Ln(5);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode('El artículo 136 del C.N.T. -modificado por la Ley 1383 de 2010 y el Decreto '
                            . 'Ley 019 de 2012- en concordancia con el procedimiento establecido en el artículo 8 de la ley '
                            . '1843 de 2017 establece que si el inculpado rechaza la comisión de la infracción deberá comparecer '
                            . 'ante la autoridad de tránsito para que éste, en audiencia pública, decrete las pruebas '
                            . 'conducentes que le sean solicitadas y las de oficio que considere útiles, lo cual supone '
                            . 'la imposición de una carga procesal y probatoria que debe asumir el presunto infractor. '
                            . 'En el presente caso, ante la ausencia del señor(a) ' . $conductor . '  identificado(a) con cédula '
                            . 'de ciudadanía ' . $ced . ' , éste deja al azar la decisión respecto a su responsabilidad '
                            . 'contravencional lo que conlleva a la administración a decidir sobre la situación jurídica '
                            . 'del procesado junto con los elementos probatorios que obran en el expediente.'), 0, 'J');
            $pdf->Ln(2);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode('Así las cosas, queda plenamente probado que el señor(a) ' . $conductor . '  identificado(a) con cédula '
                            . 'de ciudadanía ' . $ced . ' , infringió las normas de tránsito el día ' . $f_compan . ' con el '
                            . 'vehículo de  ' . $placa . ' , al momento de ser captado por medios tecnológicos para la '
                            . 'detección de infracciones de tránsito, por lo que fue impuesta la orden de comparendo ' . $compan . '.'), 0, 'J');
            $pdf->Ln(2);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(180, 10, utf8_decode('NORMAS INFRINGIDAS'), 0, 0, 'C');
            $pdf->ln(7);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(180, 5, utf8_decode('El actuar desplegado por el señor(a) se ajusta a las conductas descritas en los preceptos normativos '
                            . 'referidos y particularmente el contenido de la siguiente normatividad:'), 0, 'J');
            $pdf->ln(1);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->MultiCell(180, 5, utf8_decode("-	Ley 769 de 2002"), 0, 'J');
            $pdf->ln(1);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            //$pdf->SetFont('Times','I');
            $pdf->SetFont('Times', 'BIU');
            $pdf->MultiCell(180, 5, utf8_decode("Artículo 131. Multas"), 0, 'J');
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode('Los infractores de las normas de tránsito serán sancionados con la imposición de multas, '
                            . 'de acuerdo con el tipo de infracción, así: '), 0, 'J');
            $pdf->ln(1);
            $pdf->SetX(20);
            $pdf->SetFont('Times', 'I');
            $pdf->MultiCell(180, 5, utf8_decode('C. Será sancionado con ' . $san . ' el conductor y/o propietario de '
                            . 'un vehículo automotor que incurra en cualquiera de las siguientes infracciones:'), 0, 'J');
            $pdf->SetFont('Times', 'I');
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode('"' . $cod_inf . ' ' . $nom_inf . '"'), 0, 'J');
            $pdf->ln(1);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(180, 5, utf8_decode('Por otro lado, en virtud del artículo 139 del C.N.T., y '
                            . 'parágrafos 3 de artículo 8 de ley 1843 de 2017, todas las providencias que se dicten '
                            . 'dentro del proceso contravencional serán notificados en estrados, concordante con el '
                            . 'numeral 2 del artículo 67 de la ley 1437 de 2011, no sin antes informar que contra la '
                            . 'misma no procede ningún recurso según el artículo 142 del C.N.T., conforme a las cuantías '
                            . 'establecidas en el artículo 124 ibídem-, por lo que la presente decisión queda en firme.'), 0, 'J');
            $pdf->ln(1);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode('Por lo anterior, esta Dirección de Procesos Administrativos, '), 0, 'J');

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Ln(5);
            $pdf->SetX(20);
            $pdf->Cell(180, 3, utf8_decode('RESUELVE:'), 0, 0, 'C');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Ln(5);
            $pdf->SetX(20);
            //$html= '<b>'.'PRIMERO.'.'</b>'.'Declarar'.'<b>'.'CONTRAVENTOR de las normas de tránsito '.'</br>'.'a señor(a) '.$conductor.'identificado(a) con Cédula de Ciudadanía No.'.$ced.',  conforme a los cargos formulados en la Orden de Comparendo No. '.$compan.' de '.$f_compan.' (Código C29 del artículo 131 de la Ley 769 de 2002)';
            //$pdf->Write($html);
            //$pdf->Output();
            $pdf->MultiCell(180, 5, utf8_decode('PRIMERO: Declarar CONTRAVENTOR de las normas de tránsito a el(la) señor(a) ' . $conductor . ' identificado(a) con '.$td.' No. ' . $ced . ', por contravenir la infracción codificada con el literal ' . $cod_inf . ' que consiste en "' . $cod_inf . ' ' . $nom_inf . '" artículo 131 de la Ley 769 de 2002, modificado por la Ley 1383 de 2010.'), 0, 'J');
            $pdf->Ln(1);
            $pdf->SetX(20);
            //$nom_vlr_san
            $pdf->MultiCell(180, 5, utf8_decode('SEGUNDO: Imponer una multa al contraventor(a) de ' . $san . ', equivalentes a ' . $nom_vlr_san . '  $ ' . $vlr_san . ' pagaderos a favor de la Secretaría de Movilidad de Soacha de conformidad con la parte motiva de este proveído.'), 0, 'J');
            $pdf->Ln(1);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode("TERCERO: En firme la presente decisión, remítase el expediente al grupo de  Cobro Coactivo para lo de su competencia, o en caso de pago archívese el expediente."), 0, 'J');
            $pdf->Ln(1);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode("CUARTO: Remítase copia del expediente a la Unión Temporal - Servicios Especializados de Registro y Tránsito - Soacha -UT SERT SOACHA- a fin de que realice la inscripción de las sanciones en las plataformas SIMIT y RUNT."), 0, 'J');
            $pdf->Ln(1);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode("QUINTO: De conformidad con el artículo 142 del C.N.T., contra la presente providencia no procede recurso alguno en razón a las cuantías establecidas en el artículo 134 ibidem. En cumplimiento del artículo 161 del C.N.T., se deja constancia de la celebración efectiva de la audiencia. "), 0, 'J');
            $pdf->Ln(1);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode("No siendo otro el motivo de la presente, la misma se da por terminada y se notifica conforme lo establece el artículo 139 de la Ley 769 de 2002."), 0, 'J');
            $pdf->Ln(3);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(180, 3, utf8_decode('Notifíquese y cúmplase,'), 0, 0, 'L');
            $pdf->Ln(15);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(180, 25, utf8_decode('RODRIGO SEBASTIÁN HERNÁNDEZ ALONSO'), 0, 0, 'C');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Ln(1);
            $pdf->SetX(20);
            $pdf->Cell(180, 30, utf8_decode('Director de Procesos Administrativos'), 0, 0, 'C');
            $pdf->Ln(1);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(180, 35, utf8_decode('SECRETARÍA DE MOVILIDAD DE SOACHA'), 0, 0, 'C');

            $pdf->SetFont('Arial', '', 7);
            $pdf->Ln(22);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode("Elaboró: Walter Aldana -Especialista legal- UT SERT"), 0, 'J');
            $pdf->Ln(0.05);
            $pdf->SetX(20);
            $pdf->MultiCell(180, 5, utf8_decode("Revisó: Gina Gutiérrez - Profesional Universitaria - DPA SM"), 0, 'J');

        }
    }
}
while (ob_get_length()) {
 ob_end_clean();
}

$pdf->Output(0,'Certificado_Resolucion ('.date('d/m/Y').').pdf',0);    

?>
