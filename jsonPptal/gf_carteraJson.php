<?php 
require_once('../Conexion/conexion.php');
require_once('../Conexion/conexionsql.php');
require_once './funcionesPptal.php';
session_start();
$action     = $_REQUEST['action'];
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$fechaE     = date('Y-m-d');
$nanno      = anno($anno);
switch ($action) {
    #Cargar el combo de credito
    case (1):
        $html = '';
        $tercero = $_REQUEST['tercero'];
        $credito = $_REQUEST['credito'];
        
        
        $html .= "<option value=''>Crédito</option>";
        $sql = " SELECT DISTINCT C.Numero_Credito 
        FROM CREDITO C 
        WHERE (SELECT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO dc 
               WHERE dc.Numero_Credito = C.Numero_Credito)>0
			   AND C.Numero_Documento_Persona = '$tercero'
        ORDER BY C.Numero_Credito  ";
        $stmt = sqlsrv_query( $conn, $sql ); 
        $n=0;
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
            $html .= '<option value="'.$row['Numero_Credito'].'">'.$row['Numero_Credito'].'</option>';
            $cred  = $row['Numero_Credito'];
        }
        
        $sql = " SELECT DISTINCT C.Numero_Credito 
        FROM CREDITO C 
        WHERE (SELECT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO dc 
               WHERE dc.Numero_Credito = C.Numero_Credito)>0
			   AND C.Numero_Documento_Persona != '$tercero'
        ORDER BY C.Numero_Credito  ";
        $stmt = sqlsrv_query( $conn, $sql ); 
        
        $n=0;
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
            $html .='<option value="'.$row['Numero_Credito'].'">'.$row['Numero_Credito'].'</option>';
        }
        if($credito == $cred){
            echo 1;
        } else { echo $html; };
    break;
    
    #Cargar el combo de tercero
    case (2):
        $credito = $_REQUEST['credito'];
        $tercero = $_REQUEST['tercero'];
        $html = '';
        $html .="<option value=''>Tercero</option>";
        $sql = " SELECT DISTINCT P.Numero_Documento Numero_Documento, CONCAT(P.Nombre_Completo, P.Razon_Social)  Deudor 
            FROM CREDITO C 
            LEFT JOIN PERSONA P ON C.Numero_Documento_Persona = P.Numero_Documento 
            WHERE C.Numero_Credito = '$credito' AND (SELECT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO dc 
                   WHERE dc.Numero_Credito = C.Numero_Credito)>0
            ORDER BY CONCAT(P.Nombre_Completo, P.Razon_Social)";
        $stmt = sqlsrv_query( $conn, $sql ); 
        
        $n=0;
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
            $ter = $row['Numero_Documento'];
            $html .='<option value="'.$row['Numero_Documento'].'">'.$row['Deudor'].' - '.$row['Numero_Documento'].'</option>';
        }
        
        $sql = " SELECT DISTINCT P.Numero_Documento Numero_Documento, CONCAT(P.Nombre_Completo, P.Razon_Social)  Deudor 
            FROM CREDITO C 
            LEFT JOIN PERSONA P ON C.Numero_Documento_Persona = P.Numero_Documento 
            WHERE C.Numero_Credito != '$credito' AND (SELECT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO dc 
                   WHERE dc.Numero_Credito = C.Numero_Credito)>0
            ORDER BY CONCAT(P.Nombre_Completo, P.Razon_Social)";
        $stmt = sqlsrv_query( $conn, $sql ); 
        
        $n=0;
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
            $html .='<option value="'.$row['Numero_Documento'].'">'.$row['Deudor'].' - '.$row['Numero_Documento'].'</option>';
        }
        if($ter == $tercero){
            echo 1;
        }else {echo $html;};
    break;
    
    #Calcular Saldo
    case 3:
        $credito = $_REQUEST['credito'];
        $fecha   = $_REQUEST['fecha'];
        $fechaCt = fechaC($fecha);
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

        $total_saldo = 0;
        $total_saldo = $ts_capital + $ts_interes + $ts_interesa + $ts_mora + $ts_seguros +
        $ts_honorarios + $ts_otros_c + $ts_gastosadmin + $ts_costos + $ts_gvencidos + $ts_rvencidos + 
        $ts_ivencidos + $ts_svencidos + $ts_hvencidos + $ts_honor_cal;
        $total_saldo = round($total_saldo);
        echo number_format($total_saldo, 2, '.', ','); 
    break;
    
    #* Eliminar Configuración
    case 4:
        $tipo       = $_REQUEST['tipo'];
        $concepto   = $_REQUEST['concepto'];
        $anno       = $_REQUEST['anno'];
        $sqlct   = "DELETE FROM CONFIGURACION_PAGOS WHERE Id_Tipo_Credito ='$tipo' AND Id_Clase_Concepto = '$concepto' AND ano = '$anno'";
        $stmtct  = sqlsrv_query( $conn, $sqlct ); 
        if( !$stmtct ) {
            echo 2;
        } else {
            $rowct   = sqlsrv_fetch_array( $stmtct, SQLSRV_FETCH_ASSOC);
            echo 1;
        }
        
        
    break;
    #* Registrar Configuración
    case 5:
        $tipo       = $_REQUEST['tipo_credito'];
        $concepto   = $_REQUEST['concepto'];
        $anno       = $_REQUEST['anno'];
        if(empty($_REQUEST['concepto'.$concepto])){
            $conceptof  = 'NULL';
        } else {
            $conceptof  = $_REQUEST['concepto'.$concepto];
        }
        if(empty($_REQUEST['conceptod'.$concepto])){
            $conceptod  = 'NULL';
        } else {
            $conceptod  = $_REQUEST['conceptod'.$concepto];
        }
        $descontable= $_REQUEST['descontable'.$concepto];
        
        $sqlct   = "INSERT INTO CONFIGURACION_PAGOS (Id_Tipo_Credito, Id_Clase_Concepto, 
           ano, gf_Concepto, gf_concepto_ds,descontable)
            VALUES('$tipo', '$concepto', '$anno', $conceptof,$conceptod,  $descontable)";
        $stmtct  = sqlsrv_query( $conn, $sqlct ); 
        if( !$stmtct ) {
            echo 2;
        } else {
            $rowct   = sqlsrv_fetch_array( $stmtct, SQLSRV_FETCH_ASSOC);
            echo 0;
        }
        
        
        
    break;
}
