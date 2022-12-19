<?php 
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require_once('../Conexion/conexionsql.php');
require '../code128.php';
header("Content-Type: text/html;charset=utf-8");

ini_set('max_execution_time', 0);
session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$calendario = CAL_GREGORIAN;
$parmanno   = $_SESSION['anno'];
$anno       = anno($parmanno); 
$credito    = $_REQUEST['credito'];
$fecha      = $_REQUEST['fecha'];
//$tercero    = $_REQUEST['tercero'];
$fechal     = fechaC($fecha);
$fechaCt    = fechaC($fecha);
$valorsc    = str_replace(",", "", $_REQUEST['valor']);
$sql = " SELECT DISTINCT P.Numero_Documento Numero_Documento, CONCAT(P.Nombre_Completo, P.Razon_Social)  Deudor 
    FROM CREDITO C 
    LEFT JOIN PERSONA P ON C.Numero_Documento_Persona = P.Numero_Documento 
    WHERE c.Numero_Credito = '$credito' ";
$stmt = sqlsrv_query( $conn, $sql ); 
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
$ter = $row['Deudor'];
$nit = $row['Numero_Documento'];

#   ************   Datos Compañia   ************    #

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

$ac = 120;

ob_start();
class PDF extends FPDF
{
    function Header(){ 
        
    }      

    function Footer(){
    }
}
//$pdf = new PDF('P','mm',array(140,210));   
$pdf = new PDF_Code128('P','mm',array(140,210));   
$nb=$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','B',10);
if($ruta_logo != ''){
  $pdf->Image('../'.$ruta_logo,2,3,40);
}
$pdf->SetX(25);
$pdf->MultiCell($ac-10,4, utf8_decode($razonsocial),  0, 'C');
$pdf->SetX(20);
$pdf->Cell($ac,5,utf8_decode($nombreIdent.': '.$numeroIdent),0,0,'C'); 
$pdf->Ln(4);
$pdf->SetX(20);
$pdf->Cell($ac,5,utf8_decode('Dirección: '.$direccinTer.' Tel: '.$telefonoTer),0,0,'C');
$pdf->Ln(10);


$pdf->Cell($ac-20,5,utf8_decode('COMPROBANTE DE ABONO A CRÉDITO'),0,0,'L');
$pdf->Cell(20,5,utf8_decode($credito),0,0,'R');
$pdf->Ln(5);
$pdf->Cell($ac-20,5,utf8_decode('FECHA LIQUIDACIÓN'),0,0,'L');
$pdf->Cell(20,5,utf8_decode($fecha),0,0,'R');

$pdf->Ln(7);
$pdf->Cell($ac,5,utf8_decode('DATOS CRÉDITO'),0,0,'C');
$pdf->Ln(7);

$pdf->CellFitScale($ac-30,5,utf8_decode('CLIENTE: '.$ter),0,0,'L');
$pdf->CellFitScale(30,5,utf8_decode('C.C o NIT: '.$nit),0,0,'R');

$pdf->Ln(10);

//CREDITO 
#* Consultar Salario
$sal    = 0;
$sqls   = "SELECT Valor FROM PARAMETROS WHERE Identificador='2014P6'";
$stmts  = sqlsrv_query( $conn, $sqls ); 
$rows   = sqlsrv_fetch_array( $stmts, SQLSRV_FETCH_ASSOC);
$valor_sal    = $rows['Valor'];

$sqlc   = "SELECT * FROM  CREDITO WHERE Numero_Credito='".$credito."'";
$stmtc  = sqlsrv_query( $conn, $sqlc ); 
$rowc   = sqlsrv_fetch_array( $stmtc, SQLSRV_FETCH_ASSOC);
$IDEP   = $rowc["Id_Etapa_Procesal"];
$d_monto = $rowc["Monto"];
$porcn  = 0;
$f_sin  = $rowc["Fecha_Siniestro"];
            
if ($IDEP != "0" || $IDEP != "5"){
    $sal = $d_monto / $valor_sal;
    $sal = Round($sal);
    $sqlct   = "SELECT * FROM ETAPA_PROCESAL_CUANTIA
        inner join ETAPA_PROCESAL on ETAPA_PROCESAL_CUANTIA.Id_Etapa= ETAPA_PROCESAL.Id_Etapa
        inner join CUANTIAS on  CUANTIAS.Id_Cuantias=ETAPA_PROCESAL_CUANTIA.Id_Cuantia 
        where ETAPA_PROCESAL.Id_Etapa='".$IDEP."'";
    $stmtct  = sqlsrv_query( $conn, $sqlct ); 
    $rowct   = sqlsrv_fetch_array( $stmtct, SQLSRV_FETCH_ASSOC);
    if (count($rowct) > 0){
        while( $rowepc = sqlsrv_fetch_array( $stmtct, SQLSRV_FETCH_ASSOC) ) { 
            $mo_max = $rowepc["Monto_Maximo"];
            if ($sal <= $mo_max){
                $porcn = $rowepc["Porcentaje"];
                break;
            }
        }
    }
    $porcn = $porcn / 100;
}

$sql = "DECLARE @Numero_Credito VARCHAR(15)='".$credito."';
DECLARE @FECHA_CORTE DATE ='".$fechaCt."';
DECLARE @PORCN DECIMAL(18,2)='".$porcn."';
SELECT DISTINCT c.Numero_Credito,det.Numero_Cuota, det.Fecha_Posible_pago,
(SELECT DISTINCT DATEDIFF(day, (SELECT DISTINCT MAX(dcf.Fecha_Posible_pago) 
  FROM DETALLE_CREDITO dcf WHERE dcf.Numero_Credito = c.Numero_Credito AND dcf.Numero_Cuota = det.Numero_Cuota),
	@FECHA_CORTE) WHERE  det.Fecha_Posible_pago<=@FECHA_CORTE ) as DIAS_VENCIDOS,
(SELECT DISTINCT sum(det_5.Saldo_Concepto)
  FROM  DETALLE_CREDITO AS det_5 
  LEFT JOIN CREDITO AS c_30 ON c_30.Numero_Credito = det_5.Numero_Credito 
  LEFT JOIN CONCEPTO_LINEA ON det_5.Id_Linea = CONCEPTO_LINEA.Id_Linea AND det_5.Tipo_Linea = CONCEPTO_LINEA.Tipo_Linea 
  AND det_5.Id_Concepto = CONCEPTO_LINEA.Id_Concepto 
  LEFT JOIN CLASE_CONCEPTO AS ccd ON CONCEPTO_LINEA.Id_Concepto = ccd.Identificador
  WHERE (ccd.Identificador = '2') AND (det_5.Numero_Credito = c.Numero_Credito) AND 
  (det_5.Numero_Cuota = det.Numero_Cuota) AND (det.Fecha_Posible_pago <= @FECHA_CORTE)
  and det.Fecha_Posible_pago=det_5.Fecha_Posible_pago) AS SALDO_CAPITAL,

(SELECT DISTINCT sum(det_4.Saldo_Concepto)
  FROM  DETALLE_CREDITO AS det_4 
  LEFT JOIN CREDITO AS c_29 ON c_29.Numero_Credito = det_4.Numero_Credito 
  LEFT JOIN CONCEPTO_LINEA AS cl_11 ON det_4.Id_Linea = cl_11.Id_Linea AND det_4.Tipo_Linea = cl_11.Tipo_Linea 
  AND det_4.Id_Concepto = cl_11.Id_Concepto 
  LEFT JOIN CLASE_CONCEPTO AS ccd ON cl_11.Id_Concepto = ccd.Identificador
  WHERE  (ccd.Identificador = '3') AND (det_4.Numero_Credito = c.Numero_Credito) 
  AND (det_4.Numero_Cuota = det.Numero_Cuota) AND (det.Fecha_Posible_pago <= @FECHA_CORTE)
  and det.Fecha_Posible_pago=det_4.Fecha_Posible_pago) AS SALDO_INTERES,

(SELECT DISTINCT Sum(det_4.Saldo_Concepto)
  FROM DETALLE_CREDITO AS det_4 
  LEFT JOIN CREDITO AS c_29 ON c_29.Numero_Credito = det_4.Numero_Credito 
  LEFT JOIN CONCEPTO_LINEA AS cl_11 ON det_4.Id_Linea = cl_11.Id_Linea AND det_4.Tipo_Linea = cl_11.Tipo_Linea 
  AND det_4.Id_Concepto = cl_11.Id_Concepto 
  LEFT JOIN CLASE_CONCEPTO AS ccd ON cl_11.Id_Concepto = ccd.Identificador
  WHERE (ccd.Identificador = '21') AND (det_4.Numero_Credito = c.Numero_Credito) AND 
  (det_4.Numero_Cuota = det.Numero_Cuota) AND (det.Fecha_Posible_pago <= @FECHA_CORTE)
  and det.Fecha_Posible_pago=det_4.Fecha_Posible_pago) AS SALDO_INTERES_ACUERDO,

(SELECT DISTINCT Sum(dc_11.Saldo_Concepto)
  FROM  DETALLE_CREDITO AS dc_11 
  LEFT JOIN CREDITO AS c_28 ON c_28.Numero_Credito = dc_11.Numero_Credito 
  LEFT JOIN CONCEPTO_LINEA AS cl_10 ON dc_11.Id_Linea = cl_10.Id_Linea AND 
    dc_11.Tipo_Linea = cl_10.Tipo_Linea AND dc_11.Id_Concepto = cl_10.Id_Concepto 
  LEFT JOIN CLASE_CONCEPTO AS ccd ON cl_10.Id_Concepto = ccd.Identificador
  WHERE  (ccd.Identificador = '4') AND (dc_11.Numero_Credito = c.Numero_Credito) AND 
    (dc_11.Numero_Cuota = det.Numero_Cuota) AND (det.Fecha_Posible_pago <= @FECHA_CORTE)
    and det.Fecha_Posible_pago=dc_11.Fecha_Posible_pago) AS SALDO_RECARGO,

(SELECT DISTINCT Sum(dc_10.Saldo_Concepto)
  FROM DETALLE_CREDITO AS dc_10 
  LEFT JOIN CREDITO AS c_27 ON c_27.Numero_Credito = dc_10.Numero_Credito 
  LEFT JOIN CONCEPTO_LINEA AS cl_9 ON dc_10.Id_Linea = cl_9.Id_Linea 
    AND dc_10.Tipo_Linea = cl_9.Tipo_Linea AND dc_10.Id_Concepto = cl_9.Id_Concepto 
  LEFT JOIN CLASE_CONCEPTO AS ccd ON cl_9.Id_Concepto = ccd.Identificador
  WHERE (ccd.Identificador = '5') AND (dc_10.Numero_Credito = c.Numero_Credito) AND 
    (dc_10.Numero_Cuota = det.Numero_Cuota) AND (det.Fecha_Posible_pago <= @FECHA_CORTE) and det.Fecha_Posible_pago=dc_10.Fecha_Posible_pago  
    OR (ccd.Identificador = '22') AND (dc_10.Numero_Credito = c.Numero_Credito) AND 
    (dc_10.Numero_Cuota = det.Numero_Cuota) AND (det.Fecha_Posible_pago <= @FECHA_CORTE)
    and det.Fecha_Posible_pago=dc_10.Fecha_Posible_pago) AS SALDO_SEGUROS,

(SELECT DISTINCT Sum(dc_9.Saldo_Concepto)
FROM   DETALLE_CREDITO AS dc_9 LEFT JOIN
CREDITO AS c_26 ON c_26.Numero_Credito = dc_9.Numero_Credito LEFT JOIN
CONCEPTO_LINEA AS CONCEPTO_LINEA_8 ON dc_9.Id_Linea = CONCEPTO_LINEA_8.Id_Linea AND dc_9.Tipo_Linea = CONCEPTO_LINEA_8.Tipo_Linea AND 
dc_9.Id_Concepto = CONCEPTO_LINEA_8.Id_Concepto LEFT JOIN
CLASE_CONCEPTO AS ccd ON CONCEPTO_LINEA_8.Id_Concepto = ccd.Identificador
WHERE  (ccd.Identificador = '6') AND (dc_9.Numero_Credito = c.Numero_Credito) AND 
(dc_9.Numero_Cuota = det.Numero_Cuota) AND (det.Fecha_Posible_pago <= @FECHA_CORTE)
and det.Fecha_Posible_pago=dc_9.Fecha_Posible_pago) AS SALDO_HONORARIOS,

(SELECT DISTINCT Sum(dc_8.Saldo_Concepto)
FROM   DETALLE_CREDITO AS dc_8 LEFT JOIN
CREDITO AS CREDITO_25 ON CREDITO_25.Numero_Credito = dc_8.Numero_Credito LEFT JOIN
CONCEPTO_LINEA AS CONCEPTO_LINEA_7 ON dc_8.Id_Linea = CONCEPTO_LINEA_7.Id_Linea AND dc_8.Tipo_Linea = CONCEPTO_LINEA_7.Tipo_Linea AND 
dc_8.Id_Concepto = CONCEPTO_LINEA_7.Id_Concepto LEFT JOIN
CLASE_CONCEPTO AS ccd ON CONCEPTO_LINEA_7.Id_Concepto = ccd.Identificador
WHERE   (ccd.Identificador = '7') AND (dc_8.Numero_Credito = c.Numero_Credito) AND 
(dc_8.Numero_Cuota = det.Numero_Cuota) AND (det.Fecha_Posible_pago <= @FECHA_CORTE)
and det.Fecha_Posible_pago=dc_8.Fecha_Posible_pago or 
(ccd.Identificador = '8') AND (dc_8.Numero_Credito = c.Numero_Credito) AND 
(dc_8.Numero_Cuota = det.Numero_Cuota) AND (det.Fecha_Posible_pago <= @FECHA_CORTE)
and det.Fecha_Posible_pago=dc_8.Fecha_Posible_pago) AS SALDO_OTROS_C,




(SELECT DISTINCT sUM(dc_8.Saldo_Concepto)
FROM            DETALLE_CREDITO AS dc_8 LEFT OUTER JOIN
CREDITO AS c_30 ON c_30.Numero_Credito = dc_8.Numero_Credito LEFT JOIN
CONCEPTO_LINEA AS CONCEPTO_LINEA_12 ON dc_8.Id_Linea = CONCEPTO_LINEA_12.Id_Linea AND dc_8.Tipo_Linea = CONCEPTO_LINEA_12.Tipo_Linea AND 
dc_8.Id_Concepto = CONCEPTO_LINEA_12.Id_Concepto LEFT OUTER JOIN
CLASE_CONCEPTO AS ccd ON CONCEPTO_LINEA_12.Id_Concepto = ccd.Identificador
WHERE         (dc_8.Numero_Credito = c.Numero_Credito) AND 
(dc_8.Numero_Cuota = det.Numero_Cuota) AND (det.Fecha_Posible_pago <= @FECHA_CORTE) AND 
(CONCEPTO_LINEA_12.Indicador_Mora = 'true') and det.Fecha_Posible_pago=dc_8.Fecha_Posible_pago) AS SALDO_TODOS_V,


(SELECT DISTINCT sUM(dc_8.Saldo_Concepto)
FROM DETALLE_CREDITO AS dc_8 LEFT OUTER JOIN
CREDITO AS c_30 ON c_30.Numero_Credito = dc_8.Numero_Credito LEFT JOIN
CONCEPTO_LINEA AS CONCEPTO_LINEA_12 ON dc_8.Id_Linea = CONCEPTO_LINEA_12.Id_Linea AND dc_8.Tipo_Linea = CONCEPTO_LINEA_12.Tipo_Linea AND 
dc_8.Id_Concepto = CONCEPTO_LINEA_12.Id_Concepto LEFT OUTER JOIN
CLASE_CONCEPTO AS ccd ON CONCEPTO_LINEA_12.Id_Concepto = ccd.Identificador
WHERE (dc_8.Numero_Credito = c.Numero_Credito) AND (dc_8.Fecha_Posible_pago <= @FECHA_CORTE) AND 
(CONCEPTO_LINEA_12.Indicador_Mora = 'true') ) AS SALDO_TODOS_V_T,


(SELECT DISTINCT Sum(dc_8.Saldo_Concepto)
FROM            DETALLE_CREDITO AS dc_8 LEFT JOIN
CREDITO AS CREDITO_25 ON CREDITO_25.Numero_Credito = dc_8.Numero_Credito LEFT JOIN
CONCEPTO_LINEA AS CONCEPTO_LINEA_7 ON dc_8.Id_Linea = CONCEPTO_LINEA_7.Id_Linea AND dc_8.Tipo_Linea = CONCEPTO_LINEA_7.Tipo_Linea AND 
dc_8.Id_Concepto = CONCEPTO_LINEA_7.Id_Concepto LEFT JOIN
CLASE_CONCEPTO AS ccd ON CONCEPTO_LINEA_7.Id_Concepto = ccd.Identificador
WHERE        (ccd.Identificador = '18') AND (dc_8.Numero_Credito = c.Numero_Credito) AND 
(dc_8.Numero_Cuota = det.Numero_Cuota) 
AND (dc_8.Fecha_Posible_pago <= @FECHA_CORTE) and det.Fecha_Posible_pago=dc_8.Fecha_Posible_pago) AS SALDO_GASTOS_ADMIN,

(SELECT DISTINCT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO AS dc 					
WHERE dc.Id_Concepto = '19' AND dc.Numero_Credito = c.Numero_Credito 
AND dc.Numero_Cuota = det.Numero_Cuota
and dc.Fecha_Posible_pago<= @FECHA_CORTE and 
det.Fecha_Posible_pago=dc.Fecha_Posible_pago) AS SALDO_COSTOS,

(SELECT DISTINCT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO AS dc 					
WHERE dc.Id_Concepto = '23' AND dc.Numero_Credito = c.Numero_Credito 
AND dc.Numero_Cuota = det.Numero_Cuota
and dc.Fecha_Posible_pago<= @FECHA_CORTE) AS SALDO_GASTOS_VENCIDOS,

(SELECT DISTINCT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO AS dc 					
WHERE dc.Id_Concepto = '24' AND dc.Numero_Credito = c.Numero_Credito 
AND dc.Numero_Cuota = det.Numero_Cuota
and dc.Fecha_Posible_pago<= @FECHA_CORTE) AS SALDO_RECARGO_VENCIDOS,

(SELECT DISTINCT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO AS dc 					
WHERE dc.Id_Concepto = '25' AND dc.Numero_Credito = c.Numero_Credito 
AND dc.Numero_Cuota = det.Numero_Cuota
and dc.Fecha_Posible_pago<= @FECHA_CORTE or 
dc.Id_Concepto = '11' AND dc.Numero_Credito = c.Numero_Credito 
AND dc.Numero_Cuota = det.Numero_Cuota
and dc.Fecha_Posible_pago<= @FECHA_CORTE) AS SALDO_INTERES_VENCIDOS,

(SELECT DISTINCT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO AS dc 					
WHERE dc.Id_Concepto = '26' AND dc.Numero_Credito = c.Numero_Credito 
AND dc.Numero_Cuota = det.Numero_Cuota
and dc.Fecha_Posible_pago<= @FECHA_CORTE) AS SALDO_SEGUROS_VENCIDOS,

(SELECT DISTINCT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO AS dc 					
WHERE dc.Id_Concepto = '27' AND dc.Numero_Credito = c.Numero_Credito 
AND dc.Numero_Cuota = det.Numero_Cuota
and dc.Fecha_Posible_pago<= @FECHA_CORTE) AS SALDO_HONORARIOS_VEN, 

(SELECT DISTINCT pg.Fecha_Pago  
    FROM   PAGOS AS pg
    WHERE  pg.Id_Estado_Pago = '3' AND pg.Numero_Credito = c.Numero_Credito) AS FECHA_PAGO_CONDONACION, 
c.Id_Etapa_Procesal as Etapa, c.Porcentaje_Recargo , 
(CASE when (select max(p.Fecha_Pago) as fecha_ultima from PAGOS as p  
    left join DETALLE_PAGO as dp on p.Numero_Recibo= dp.Numero_Recibo 
    where dp.Numero_Credito= c.Numero_Credito and dp.Id_Concepto='4' 
    and dp.Numero_Cuota=det.Numero_Cuota  
    and p.Id_Estado_Pago='1'   and p.Observaciones!='Pago Anulado') is not null then
                                    (select max(p.Fecha_Pago) as fecha_ultima from PAGOS as p  
    left join DETALLE_PAGO as dp on p.Numero_Recibo= dp.Numero_Recibo 
    where dp.Numero_Credito= c.Numero_Credito and dp.Id_Concepto='4' 
    and dp.Numero_Cuota=det.Numero_Cuota  
    and p.Id_Estado_Pago='1'   and p.Observaciones!='Pago Anulado') 
                                    else (det.Fecha_Posible_pago) end) as fecha_comp, 
    c.Id_Abogado as Abogado

FROM            CREDITO AS c 
LEFT JOIN DETALLE_CREDITO AS det ON c.Numero_Credito = det.Numero_Credito 
left join ETAPA_PROCESAL on ETAPA_PROCESAL.Id_Etapa=c.Id_Etapa_Procesal
WHERE        (c.Numero_Credito = @Numero_Credito and det.Id_Concepto!='6' and det.Saldo_Concepto>0
) order by det.Fecha_Posible_pago";
$stmt  = sqlsrv_query( $conn, $sql ); 
$ts_capital     = 0;
$ts_interes     = 0;
$ts_interesa    = 0;
$ts_recargo     = 0;
$ts_seguros     = 0;
$ts_honorarios  = 0;
$ts_otros_c     = 0;
$ts_gastosadmin = 0;
$ts_costos      = 0;
$ts_gvencidos   = 0;
$ts_rvencidos   = 0;
$ts_ivencidos   = 0;
$ts_svencidos   = 0;
$ts_hvencidos   = 0;
$ts_mora        = 0;
$ts_honor_cal   = 0;
$pdf->SetFont('Arial','',10);
while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
    $ts_capital     += $row["SALDO_CAPITAL"];
    $ts_interes     += $row["SALDO_INTERES"];
    $ts_interesa    += $row["SALDO_INTERES_ACUERDO"];
    $ts_seguros     += $row["SALDO_SEGUROS"];
    $ts_honorarios  += $row["SALDO_HONORARIOS"];
    $ts_otros_c     += $row["SALDO_OTROS_C"];
    $ts_gastosadmin += $row["SALDO_GASTOS_ADMIN"];
    $ts_costos      += $row["SALDO_COSTOS"];
    $ts_gvencidos   += $row["SALDO_GASTOS_VENCIDOS"];
    $ts_rvencidos   += $row["SALDO_RECARGO_VENCIDOS"];
    $ts_ivencidos   += $row["SALDO_INTERES_VENCIDOS"];
    $ts_svencidos   += $row["SALDO_SEGUROS_VENCIDOS"];
    $ts_hvencidos   += $row["SALDO_HONORARIOS_VEN"];
    
    #* Calculo Mora 
    if(empty($row["etapa"])){
        $etapa          = 0;
    } else {
        $etapa          = $row["etapa"];
    }
    $totalv         = $row["SALDO_TODOS_V"];
    $p_recargo      = ($row["Porcentaje_Recargo"]/100);
    $mora           = 0;
    $dias           = 0;
    if($etapa!='23'){
        $fecha_pp  = $row["Fecha_Posible_pago"];
        $fecha_pp  = date_format($fecha_pp, "Y-m-d");
        $fecha_dif = $fecha_pp ;
        #Calculo Dias 
        if($totalv>0){
            if($fecha_pp < $fechaCt){
                if(!empty($row["FECHA_PAGO_CONDONACION"])){
                    $fecha_pc = date_format($row["FECHA_PAGO_CONDONACION"],"Y-m-d");
                    if($fecha_pc >= $fecha_pp){
                        if(!empty($row["fecha_comp"])){
                            $fecha_com = date_format($row["fecha_comp"],"Y-m-d");
                            if($fecha_pc >= $fecha_com){
                                $fecha_dif = $fecha_pc;
                            } else {
                                $fecha_dif = $fecha_com;
                            }
                        }else {
                           $fecha_dif = $fecha_pc;
                        }
                    }
                } elseif(!empty($row["fecha_comp"])){
                    $fecha_com = date_format($row["fecha_comp"],"Y-m-d");
                    if($fecha_com >= $fecha_pp){
                        $fecha_dif = $fecha_com;
                    }
                }
                
                $date1  = date_create($fecha_dif);
                $date2  = date_create($fechaCt);
                $diff   = date_diff($date1,$date2);
                $dias   = $diff->format("%a");
            }
        } 
        
        #Calculo Mora
        if($dias >0 && $totalv>0 ){
            $mora = (($totalv * $p_recargo) *$dias)/360;
        }
    }
    
    $ts_recargo     += $row["SALDO_RECARGO"];
    $ts_mora        += $mora;
    
    $th = 0;
    $vbh = $mora + $row["SALDO_CAPITAL"] + $row["SALDO_INTERES"] + $row["SALDO_INTERES_ACUERDO"] + 
    + $row["SALDO_SEGUROS"] + $row["SALDO_HONORARIOS"] + $row["SALDO_OTROS_C"] + $row["SALDO_GASTOS_ADMIN"]
    + $row["SALDO_COSTOS"] + $row["SALDO_GASTOS_VENCIDOS"] + $row["SALDO_RECARGO_VENCIDOS"] + $row["SALDO_INTERES_VENCIDOS"] 
    + $row["SALDO_SEGUROS_VENCIDOS"] + $row["SALDO_HONORARIOS_VEN"];
    if($vbh > 0){
        if(!empty($row['Abogado'])){
            $th = ($vbh * $porcn);
        }
    }
    $ts_honor_cal += $th;
}


if($ts_capital > 0){    
    $pdf->Cell($ac-30, 5, 'SALDO CAPITAL', 0, 0, 'L');          
    $pdf->Cell(30, 5,number_format($ts_capital, 2, '.', ','), 0, 0, 'R'); 
    $pdf->Ln(5);
}
if($ts_interes > 0){    
    $pdf->Cell($ac-30, 5, 'SALDO INTERESES', 0, 0, 'L');        
    $pdf->Cell(30, 5,number_format($ts_interes, 2, '.', ','), 0, 0, 'R'); 
    $pdf->Ln(5);
}
if($ts_interesa> 0){    
    $pdf->Cell($ac-30, 5, 'SALDO INTERESES ACUERDO', 0, 0, 'L');
    $pdf->Cell(30, 5,number_format($ts_interesa, 2, '.', ','), 0, 0, 'R'); 
    $pdf->Ln(5);
}

if($ts_seguros > 0){    
    $pdf->Cell($ac-30, 5, 'SALDO SEGURO', 0, 0, 'L');           
    $pdf->Cell(30, 5,number_format($ts_seguros, 2, '.', ','), 0, 0, 'R'); 
    $pdf->Ln(5);
}
if($ts_honorarios> 0){  
    $pdf->Cell($ac-30, 5, 'SALDO HONORARIOS', 0, 0, 'L');       
    $pdf->Cell(30, 5,number_format($ts_honorarios, 2, '.', ','), 0, 0, 'R'); 
    $pdf->Ln(5);
}
if($ts_otros_c > 0){    
    $pdf->Cell($ac-30, 5, 'SALDO OTROS', 0, 0, 'L');            
    $pdf->Cell(30, 5,number_format($ts_otros_c, 2, '.', ','), 0, 0, 'R'); 
    $pdf->Ln(5);
}
if($ts_gastosadmin > 0){
    $pdf->Cell($ac-30, 5, 'SALDO GASTOS ADMINISTRACIÓN', 0, 0, 'L'); 
    $pdf->Cell(30, 5,number_format($ts_gastosadmin, 2, '.', ','), 0, 0, 'R'); 
    $pdf->Ln(5);
}
if($ts_costos  > 0){    
    $pdf->Cell($ac-30, 5, 'SALDO COSTOS', 0, 0, 'L'); 
    $pdf->Cell(30, 5,number_format($ts_costos, 2, '.', ','), 0, 0, 'R'); 
    $pdf->Ln(5);
}
if($ts_gvencidos > 0){  
    $pdf->Cell($ac-30, 5, 'SALDO GASTOS VENCIDOS', 0, 0, 'L'); 
    $pdf->Cell(30, 5,number_format($ts_gvencidos, 2, '.', ','), 0, 0, 'R'); 
    $pdf->Ln(5);
}
if($ts_rvencidos > 0){  
    $pdf->Cell($ac-30, 5, 'SALDO RECARGO VENCIDO', 0, 0, 'L'); 
    $pdf->Cell(30, 5,number_format($ts_rvencidos, 2, '.', ','), 0, 0, 'R'); 
    $pdf->Ln(5);
}
if($ts_ivencidos > 0){  
    $pdf->Cell($ac-30, 5, 'SALDO INTERESES VENCIDOS', 0, 0, 'L'); 
    $pdf->Cell(30, 5,number_format($ts_ivencidos, 2, '.', ','), 0, 0, 'R'); 
    $pdf->Ln(5);
}
if($ts_svencidos > 0){  
    $pdf->Cell($ac-30, 5, 'SALDO SEGURO VENCIDO', 0, 0, 'L'); 
    $pdf->Cell(30, 5,number_format($ts_svencidos, 2, '.', ','), 0, 0, 'R'); 
    $pdf->Ln(5);
}
if($ts_hvencidos > 0){  
    $pdf->Cell($ac-30, 5, 'SALDO HONORARIOS VENCIDOS', 0, 0, 'L'); 
    $pdf->Cell(30, 5,number_format($ts_hvencidos, 2, '.', ','), 0, 0, 'R'); 
    $pdf->Ln(5);
}
if($ts_mora > 0){    
    $pdf->Cell($ac-30, 5, 'VALOR MORA', 0, 0, 'L');          
    $pdf->Cell(30, 5,number_format($ts_mora, 2, '.', ','), 0, 0, 'R'); 
    $pdf->Ln(5);
}
if($ts_honor_cal>0){
    $pdf->Cell($ac-30, 5, 'TOTAL HONORARIOS', 0, 0, 'L');          
    $pdf->Cell(30, 5,number_format($ts_honor_cal, 2, '.', ','), 0, 0, 'R'); 
    $pdf->Ln(5);
}


$total_saldo = 0;
$total_saldo = $ts_capital + $ts_interes + $ts_interesa + $ts_mora + $ts_seguros +
$ts_honorarios + $ts_otros_c + $ts_gastosadmin + $ts_costos + $ts_gvencidos + $ts_rvencidos + 
$ts_ivencidos + $ts_svencidos + $ts_hvencidos + $ts_honor_cal;


$total_saldo = round($total_saldo);
$pdf->SetFont('Arial','B',10);
$pdf->Cell($ac-30, 5, 'TOTAL A PAGAR', 0, 0, 'L'); 
$pdf->Cell(30, 5,number_format($total_saldo, 2, '.', ','), 0, 0, 'R'); 
$pdf->Ln(8);

$valorsc = round($valorsc);
$pdf->SetFont('Arial','B',12);
$y= $pdf->GetY();
$pdf->MultiCell($ac-30, 5, 'VALOR A PAGAR SOLICITADO POR EL CLIENTE',  0, 'L'); 
$pdf->SetY($y);
$pdf->Cell($ac-30, 5,'', 0, 0, 'R'); 
$pdf->Cell(30, 5,number_format($valorsc, 2, '.', ','), 0, 0, 'R'); 
$pdf->Ln(15);

$yc = $pdf->GetY();

#** Información código de barras
 $numcredito =str_replace('.','',$credito);  
$xt = str_replace(',','',$valorsc);
  $ct = strlen($xt);       
  if($ct < 8){
      $xt = str_pad($xt, 8, "0", STR_PAD_LEFT);
  }                    
  $fechart = explode('/',$fecha);
  $dia=  $fechart[0];
  $mes=  $fechart[1];
  $anio= $fechart[2];
  $fechart = $anio.$mes.$dia;       
  $ref =  $numcredito.$nit;
  $cr = strlen($ref);
  if($cr < 20){
      $ref = str_pad($ref, 20, "0", STR_PAD_LEFT);
  }
  $codigoEAN = "0000000023700";

 //Código de barras
 // $format_barcode = chr(241). "415". $codigoEAN. "8020". $ref. chr(241). "3900". $xt. chr(241). "96". $fechart;
  $format_barcode = "415".$codigoEAN."8020".$ref."3900".$xt."96".$fechart;
  $barcode = "(415)$codigoEAN(8020)$ref(3900)$xt(96)$fechart";

  $pdf->setFillColor(0, 0, 0);            
  $pdf->Code128(5,$yc,$format_barcode,130,25); 
  $pdf->SetXY(20, $yc+23);
  $pdf->SetFont('Arial','B',7);       
  $pdf->Cell(110,9, utf8_decode($barcode),'',0,'C');

//fin código de barras 
$pdf->Ln(15);
$pdf->SetFont('Arial','B',10);
$pdf->Cell($ac, 5, utf8_decode('PAGAR ÚNICAMENTE EN BANCO BBVA'), 0, 0, 'L'); 
$pdf->Ln(5);
$pdf->Cell($ac, 5, utf8_decode('EMITIDO POR: '.mb_strtoupper($_SESSION['usuario'])), 0, 0, 'L'); 
$pdf->Ln(15);
$pdf->SetFont('Arial','',7);
$pdf->Cell($ac, 5, utf8_decode('Nota: Este documento no es comprobante de pago, Conserve el comprobante emitido por el Banco'), 0, 0, 'L'); 


ob_end_clean();		
$pdf->Output(0,'Comprobante_Abono_Credito_'.$credito.'.pdf',0);

?>