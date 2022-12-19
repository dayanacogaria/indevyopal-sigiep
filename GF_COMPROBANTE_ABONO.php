<?php
require_once('Conexion/conexion.php');
require_once('Conexion/conexionsql.php');
require_once('./jsonPptal/funcionesPptal.php');
require_once 'head_listar.php';
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
?>
<title>Comprobante Abono Crédito</title>

<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="js/md5.pack.js"></script>
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script type="text/javascript">

    $(document).ready(function ()
    {
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        var fecha = new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth() + 1;
        if(dia < 10){
            dia = "0" + dia;
        }
        if(mes < 10){
            mes = "0" + mes;
        }
        var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
        <?php if(empty($_GET['c']) && empty($_GET['f'])&& empty($_GET['v'])) { ?>      
        $("#fecha").datepicker({changeMonth: true, minDate:fecAct}).val(fecAct);
      <?php } ?>

    });
</script>
<style>
    label #fecha-error, #credito-error, #tercero-error, #valor-error {
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;
}
body{
    font-size: 12px;
}
</style>
<script>

    $().ready(function () {
        var validator = $("#form").validate({
            ignore: "",

            errorPlacement: function (error, element) {

                $(element)
                        .closest("form")
                        .find("label[for='" + element.attr("id") + "']")
                        .append(error);
            }
        });

        $(".cancel").click(function () {
            validator.resetForm();
        });
    });
</script>
</head>
<body>
    <div class="container-fluid text-center"  >
        <div class="row content">
            <?php require_once 'menu.php'; ?> 

          
            <div class="col-sm-10" style="margin-left: -10px;margin-top: 5px" >
                <h2 align="center" class="tituloform col-sm-12" style="margin-top: -5px; margin-bottom: 2px;" >Comprobante Abono Crédito</h2>
                <div class="col-sm-12">
                   <?php if(empty($_GET['c']) && empty($_GET['f'])&& empty($_GET['v'])) { ?>      
                    <div class="client-form contenedorForma"  style=""> 
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes/Inf_Comprobante_Abono.php" target="_blank">
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 50px;"> 
                                <div class="col-sm-2" align="left">  
                                    <label for="fecha" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                    <input name="fecha" id="fecha" class="form-control input-sm" title="Seleccione Fecha" style="width:200px; " required readonly="true" />
                                </div>
                                <div class="col-sm-5" align="left" style="margin-left:50px">  
                                    <label for="credito" class="control-label" ><strong style="color:#03C1FB;">*</strong>Crédito / Tercero:</label><br>
                                    <select name="credito" id="credito" class="select2_single form-control input-sm" title="Seleccione Crédito / Tercero" style="width:350px; " required>
                                        <option value="">Crédito / Tercero</option>
                                        <?php
                                        $sql = " SELECT DISTINCT C.Numero_Credito, P.Numero_Documento Numero_Documento, CONCAT(P.Nombre_Completo, P.Razon_Social)  Deudor 
                                        FROM CREDITO C 
                                        LEFT JOIN PERSONA P ON C.Numero_Documento_Persona = P.Numero_Documento 
                                        WHERE (SELECT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO dc 
                                               WHERE dc.Numero_Credito = C.Numero_Credito)>0
                                        ORDER BY C.Numero_Credito";
                                        $stmt = sqlsrv_query( $conn, $sql );  
                                        $n=0;
                                        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
                                            echo '<option value="'.$row['Numero_Credito'].'">'.utf8_decode($row['Numero_Credito'].' - '.$row['Deudor'].' - '.$row['Numero_Documento']).'</option>';
                                        }
                                        
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-2" align="left" style="margin-left:-50px">  
                                    <label for="valor" class="control-label" ><strong style="color:#03C1FB;">*</strong>Valor:</label><br>
                                    <input name="valor" id="valor" class="form-control input-sm" title="Ingrese Valor" style="width:200px; " required onkeypress="return txtValida(event,'dec', 'valor', '2');" onkeyup="formatC('valor');"/>
                                </div>
                              
                            </div>
                            <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;" align="right">
                                <div class="col-sm-12 " align="right"  style="margin-top: -20px; margin-left: -20px;">
                                </div>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 10px;">
                <input type="hidden" name="fechacredito" id="fechacredito" value="0">
                <input type="hidden" name="numcredito"  id="numcredito" value="0">
              <?php }  else{ 
                  
                $id_credito = $_REQUEST['c'];
                $fechacred  = fechaC($_REQUEST['f']);
                $valortot   = $_REQUEST['v'];     
                $sql        =" SELECT DISTINCT C.Numero_Credito, P.Numero_Documento Numero_Documento, CONCAT(P.Nombre_Completo, P.Razon_Social)  Deudor 
                    FROM CREDITO C 
                    LEFT JOIN PERSONA P ON C.Numero_Documento_Persona = P.Numero_Documento 
                    WHERE (SELECT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO dc 
                           WHERE dc.Numero_Credito = C.Numero_Credito)>0 
                    AND C.Numero_Credito = $id_credito 
                    ORDER BY C.Numero_Credito";
                $stmt = sqlsrv_query( $conn, $sql );  
                $n    =0;
                $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ; 
                $numeroCredito =  $row['Numero_Credito'].' - '.$row['Deudor'].' - '.$row['Numero_Documento'];
                
                
                #* Consultar Salario
                $sal    = 0;
                $sqls   = "SELECT Valor FROM PARAMETROS WHERE Identificador='2014P6'";
                $stmts  = sqlsrv_query( $conn, $sqls ); 
                $rows   = sqlsrv_fetch_array( $stmts, SQLSRV_FETCH_ASSOC);
                $valor_sal    = $rows['Valor'];

                $sqlc   = "SELECT * FROM  CREDITO WHERE Numero_Credito='".$id_credito."'";
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


                $sql = "DECLARE @Numero_Credito VARCHAR(15)= $id_credito;
                    DECLARE @FECHA_CORTE DATE ='$fechacred';
                    DECLARE @PORCN DECIMAL(18,2)='5';
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
                      (det_5.Numero_Cuota = det.Numero_Cuota) 
                      and det.Fecha_Posible_pago=det_5.Fecha_Posible_pago) AS SALDO_CAPITAL,

                    (SELECT DISTINCT sum(det_4.Saldo_Concepto)
                      FROM  DETALLE_CREDITO AS det_4 
                      LEFT JOIN CREDITO AS c_29 ON c_29.Numero_Credito = det_4.Numero_Credito 
                      LEFT JOIN CONCEPTO_LINEA AS cl_11 ON det_4.Id_Linea = cl_11.Id_Linea AND det_4.Tipo_Linea = cl_11.Tipo_Linea 
                      AND det_4.Id_Concepto = cl_11.Id_Concepto 
                      LEFT JOIN CLASE_CONCEPTO AS ccd ON cl_11.Id_Concepto = ccd.Identificador
                      WHERE  (ccd.Identificador = '3') AND (det_4.Numero_Credito = c.Numero_Credito) 
                      AND (det_4.Numero_Cuota = det.Numero_Cuota) 
                      and det.Fecha_Posible_pago=det_4.Fecha_Posible_pago) AS SALDO_INTERES,

                    (SELECT DISTINCT Sum(det_4.Saldo_Concepto)
                      FROM DETALLE_CREDITO AS det_4 
                      LEFT JOIN CREDITO AS c_29 ON c_29.Numero_Credito = det_4.Numero_Credito 
                      LEFT JOIN CONCEPTO_LINEA AS cl_11 ON det_4.Id_Linea = cl_11.Id_Linea AND det_4.Tipo_Linea = cl_11.Tipo_Linea 
                      AND det_4.Id_Concepto = cl_11.Id_Concepto 
                      LEFT JOIN CLASE_CONCEPTO AS ccd ON cl_11.Id_Concepto = ccd.Identificador
                      WHERE (ccd.Identificador = '21') AND (det_4.Numero_Credito = c.Numero_Credito) AND 
                      (det_4.Numero_Cuota = det.Numero_Cuota) 
                      and det.Fecha_Posible_pago=det_4.Fecha_Posible_pago) AS SALDO_INTERES_ACUERDO,

                    (SELECT DISTINCT Sum(dc_11.Saldo_Concepto)
                      FROM  DETALLE_CREDITO AS dc_11 
                      LEFT JOIN CREDITO AS c_28 ON c_28.Numero_Credito = dc_11.Numero_Credito 
                      LEFT JOIN CONCEPTO_LINEA AS cl_10 ON dc_11.Id_Linea = cl_10.Id_Linea AND 
                        dc_11.Tipo_Linea = cl_10.Tipo_Linea AND dc_11.Id_Concepto = cl_10.Id_Concepto 
                      LEFT JOIN CLASE_CONCEPTO AS ccd ON cl_10.Id_Concepto = ccd.Identificador
                      WHERE  (ccd.Identificador = '4') AND (dc_11.Numero_Credito = c.Numero_Credito) AND 
                        (dc_11.Numero_Cuota = det.Numero_Cuota) 
                        and det.Fecha_Posible_pago=dc_11.Fecha_Posible_pago) AS SALDO_RECARGO,

                    (SELECT DISTINCT Sum(dc_10.Saldo_Concepto)
                      FROM DETALLE_CREDITO AS dc_10 
                      LEFT JOIN CREDITO AS c_27 ON c_27.Numero_Credito = dc_10.Numero_Credito 
                      LEFT JOIN CONCEPTO_LINEA AS cl_9 ON dc_10.Id_Linea = cl_9.Id_Linea 
                        AND dc_10.Tipo_Linea = cl_9.Tipo_Linea AND dc_10.Id_Concepto = cl_9.Id_Concepto 
                      LEFT JOIN CLASE_CONCEPTO AS ccd ON cl_9.Id_Concepto = ccd.Identificador
                      WHERE (ccd.Identificador = '5') AND (dc_10.Numero_Credito = c.Numero_Credito) AND 
                        (dc_10.Numero_Cuota = det.Numero_Cuota)  and det.Fecha_Posible_pago=dc_10.Fecha_Posible_pago  
                        OR (ccd.Identificador = '22') AND (dc_10.Numero_Credito = c.Numero_Credito) AND 
                        (dc_10.Numero_Cuota = det.Numero_Cuota) 
                        and det.Fecha_Posible_pago=dc_10.Fecha_Posible_pago) AS SALDO_SEGUROS,

                    (SELECT DISTINCT Sum(dc_9.Saldo_Concepto)
                    FROM   DETALLE_CREDITO AS dc_9 LEFT JOIN
                    CREDITO AS c_26 ON c_26.Numero_Credito = dc_9.Numero_Credito LEFT JOIN
                    CONCEPTO_LINEA AS CONCEPTO_LINEA_8 ON dc_9.Id_Linea = CONCEPTO_LINEA_8.Id_Linea AND dc_9.Tipo_Linea = CONCEPTO_LINEA_8.Tipo_Linea AND 
                    dc_9.Id_Concepto = CONCEPTO_LINEA_8.Id_Concepto LEFT JOIN
                    CLASE_CONCEPTO AS ccd ON CONCEPTO_LINEA_8.Id_Concepto = ccd.Identificador
                    WHERE  (ccd.Identificador = '6') AND (dc_9.Numero_Credito = c.Numero_Credito) AND 
                    (dc_9.Numero_Cuota = det.Numero_Cuota) 
                    and det.Fecha_Posible_pago=dc_9.Fecha_Posible_pago) AS SALDO_HONORARIOS,

                    (SELECT DISTINCT Sum(dc_8.Saldo_Concepto)
                    FROM   DETALLE_CREDITO AS dc_8 LEFT JOIN
                    CREDITO AS CREDITO_25 ON CREDITO_25.Numero_Credito = dc_8.Numero_Credito LEFT JOIN
                    CONCEPTO_LINEA AS CONCEPTO_LINEA_7 ON dc_8.Id_Linea = CONCEPTO_LINEA_7.Id_Linea AND dc_8.Tipo_Linea = CONCEPTO_LINEA_7.Tipo_Linea AND 
                    dc_8.Id_Concepto = CONCEPTO_LINEA_7.Id_Concepto LEFT JOIN
                    CLASE_CONCEPTO AS ccd ON CONCEPTO_LINEA_7.Id_Concepto = ccd.Identificador
                    WHERE   (ccd.Identificador = '7') AND (dc_8.Numero_Credito = c.Numero_Credito) AND 
                    (dc_8.Numero_Cuota = det.Numero_Cuota) 
                    and det.Fecha_Posible_pago=dc_8.Fecha_Posible_pago or 
                    (ccd.Identificador = '8') AND (dc_8.Numero_Credito = c.Numero_Credito) AND 
                    (dc_8.Numero_Cuota = det.Numero_Cuota) 
                    and det.Fecha_Posible_pago=dc_8.Fecha_Posible_pago) AS SALDO_OTROS_C,




                    (SELECT DISTINCT sUM(dc_8.Saldo_Concepto)
                    FROM            DETALLE_CREDITO AS dc_8 LEFT OUTER JOIN
                    CREDITO AS c_30 ON c_30.Numero_Credito = dc_8.Numero_Credito LEFT JOIN
                    CONCEPTO_LINEA AS CONCEPTO_LINEA_12 ON dc_8.Id_Linea = CONCEPTO_LINEA_12.Id_Linea AND dc_8.Tipo_Linea = CONCEPTO_LINEA_12.Tipo_Linea AND 
                    dc_8.Id_Concepto = CONCEPTO_LINEA_12.Id_Concepto LEFT OUTER JOIN
                    CLASE_CONCEPTO AS ccd ON CONCEPTO_LINEA_12.Id_Concepto = ccd.Identificador
                    WHERE         (dc_8.Numero_Credito = c.Numero_Credito) AND 
                    (dc_8.Numero_Cuota = det.Numero_Cuota)  AND 
                    (CONCEPTO_LINEA_12.Indicador_Mora = 'true') and det.Fecha_Posible_pago=dc_8.Fecha_Posible_pago) AS SALDO_TODOS_V,


                    (SELECT DISTINCT sUM(dc_8.Saldo_Concepto)
                    FROM DETALLE_CREDITO AS dc_8 LEFT OUTER JOIN
                    CREDITO AS c_30 ON c_30.Numero_Credito = dc_8.Numero_Credito LEFT JOIN
                    CONCEPTO_LINEA AS CONCEPTO_LINEA_12 ON dc_8.Id_Linea = CONCEPTO_LINEA_12.Id_Linea AND dc_8.Tipo_Linea = CONCEPTO_LINEA_12.Tipo_Linea AND 
                    dc_8.Id_Concepto = CONCEPTO_LINEA_12.Id_Concepto LEFT OUTER JOIN
                    CLASE_CONCEPTO AS ccd ON CONCEPTO_LINEA_12.Id_Concepto = ccd.Identificador
                    WHERE (dc_8.Numero_Credito = c.Numero_Credito) AND 
                    (CONCEPTO_LINEA_12.Indicador_Mora = 'true') ) AS SALDO_TODOS_V_T,


                    (SELECT DISTINCT Sum(dc_8.Saldo_Concepto)
                    FROM            DETALLE_CREDITO AS dc_8 LEFT JOIN
                    CREDITO AS CREDITO_25 ON CREDITO_25.Numero_Credito = dc_8.Numero_Credito LEFT JOIN
                    CONCEPTO_LINEA AS CONCEPTO_LINEA_7 ON dc_8.Id_Linea = CONCEPTO_LINEA_7.Id_Linea AND dc_8.Tipo_Linea = CONCEPTO_LINEA_7.Tipo_Linea AND 
                    dc_8.Id_Concepto = CONCEPTO_LINEA_7.Id_Concepto LEFT JOIN
                    CLASE_CONCEPTO AS ccd ON CONCEPTO_LINEA_7.Id_Concepto = ccd.Identificador
                    WHERE        (ccd.Identificador = '18') AND (dc_8.Numero_Credito = c.Numero_Credito) AND 
                    (dc_8.Numero_Cuota = det.Numero_Cuota) 
                    and det.Fecha_Posible_pago=dc_8.Fecha_Posible_pago) AS SALDO_GASTOS_ADMIN,

                    (SELECT DISTINCT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO AS dc                  
                    WHERE dc.Id_Concepto = '19' AND dc.Numero_Credito = c.Numero_Credito 
                    AND dc.Numero_Cuota = det.Numero_Cuota and 
                    det.Fecha_Posible_pago=dc.Fecha_Posible_pago) AS SALDO_COSTOS,

                    (SELECT DISTINCT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO AS dc                  
                    WHERE dc.Id_Concepto = '23' AND dc.Numero_Credito = c.Numero_Credito 
                    AND dc.Numero_Cuota = det.Numero_Cuota
                    ) AS SALDO_GASTOS_VENCIDOS,

                    (SELECT DISTINCT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO AS dc                  
                    WHERE dc.Id_Concepto = '24' AND dc.Numero_Credito = c.Numero_Credito 
                    AND dc.Numero_Cuota = det.Numero_Cuota) AS SALDO_RECARGO_VENCIDOS,

                    (SELECT DISTINCT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO AS dc                  
                    WHERE dc.Id_Concepto = '25' AND dc.Numero_Credito = c.Numero_Credito 
                    AND dc.Numero_Cuota = det.Numero_Cuota or 
                    dc.Id_Concepto = '11' AND dc.Numero_Credito = c.Numero_Credito 
                    AND dc.Numero_Cuota = det.Numero_Cuota) AS SALDO_INTERES_VENCIDOS,

                    (SELECT DISTINCT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO AS dc                  
                    WHERE dc.Id_Concepto = '26' AND dc.Numero_Credito = c.Numero_Credito 
                    AND dc.Numero_Cuota = det.Numero_Cuota) AS SALDO_SEGUROS_VENCIDOS,

                    (SELECT DISTINCT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO AS dc                  
                    WHERE dc.Id_Concepto = '27' AND dc.Numero_Credito = c.Numero_Credito 
                    AND dc.Numero_Cuota = det.Numero_Cuota) AS SALDO_HONORARIOS_VEN, 

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

                    $stmt           = sqlsrv_query( $conn, $sql ); 
                    $fechaCt        = $fechacred;
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
                ?>      
                <div class="client-form contenedorForma"  style=" margin-left: -2px;"> 
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes/Inf_Comprobante_Abono.php" target="_blank">
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px;"> 
                            <div class="col-sm-2" align="left">  
                                <label for="fecham" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                <label name="fecham" id="fechm" class="form-control input-sm" title="Seleccione Fecha" style="width:200px; " ><?php echo $_REQUEST['f']  ?></label>
                                 <input name="fecha" id="fecha" type="hidden" value="<?php echo $_REQUEST['f'] ?>" />
                            </div>
                            <div class="col-sm-5" align="left" style="margin-left:15px">  
                                <label for="credito" class="control-label" ><strong style="color:#03C1FB;">*</strong>Crédito / Tercero:</label><br>
                                <label name="creditoM" id="creditoM" class="form-control input-sm"  style="width:500px; " ><?php echo $numeroCredito ?></label>
                                <input name="credito" id="credito" type="hidden" value="<?php echo $id_credito ?>" />
                            </div>
                            <div class="col-sm-2" align="left" style="    margin-left: 5px;">  
                               <label for="valor" class="control-label" ><strong style="color:#03C1FB;">*</strong>Valor:</label><br>                                  
                                <input name="valor" id="valor" type="text"  class="form-control input-sm" style="width:200px;" value="<?php echo $valortot ?>" />
                            </div>
                            <div class="col-sm-2" align="right" style="margin-top: 30px; margin-left: -40px;">  
                             <a href="GF_COMPROBANTE_ABONO.php" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></a>
                            </div>                                
                            <div class="col-sm-1" align="left"     style="margin-left: -20px;">  
                                <button type="submit"  id="btnGuardar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 0px; " title="Guardar"><li class="glyphicon glyphicon-floppy-disk"></li></button> 
                                <input type="hidden" name="MM_insert" >
                            </div>
                        </div>
                        <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;" align="right">
                            <div class="col-sm-12 " align="right"  style="margin-top: -20px; margin-left: -20px;">
                            </div>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>

                <div class="table-responsive contTabla" >

                        <table id="tabla" class=" col-sm-8 table table-striped table-condensed" class="display" cellspacing="0" width="100%" >
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="7%" class="cabeza"></td>
                                    <td class="cabeza">Total Cuota</td>
                                    <td class="cabeza">Acumulado Cuota</td>                                      
                                    <td class="cabeza">Nº Cuota</td>
                                    <td class="cabeza">Fecha Pago</td>
                                    <td class="cabeza">Saldo Capital</td>
                                    <td class="cabeza">Saldo Interés</td>
                                    <td class="cabeza">Saldo Interés Acuerdo</td>
                                    <td class="cabeza">Saldo Seguros</td>
                                    <td class="cabeza">Saldo Honorarios</td>
                                    <td class="cabeza">Saldo Otros C.</td>
                                    <td class="cabeza">Saldo Gastos Administrativos</td>
                                    <td class="cabeza">Saldo Costos</td>
                                    <td class="cabeza">Saldo Gastos Vencidos</td>
                                    <td class="cabeza">Saldo Recargo Vencido</td>
                                    <td class="cabeza">Saldo Interés vencido</td>
                                    <td class="cabeza">Saldo Seguros vencido</td>                                      
                                    <td class="cabeza">Saldo Honorarios Vencidos</td>
                                    <td class="cabeza">Mora</td>
                                    <td class="cabeza">Honorarios</td>
                                </tr>
                                <tr>
                                    <th class="cabeza" style="display: none;">Identificador</th>
                                    <th width="7%"></th> 
                                    <th class="cabeza">Total Cuota</th>
                                    <th class="cabeza">Acumulado Cuota</th>                                      
                                    <th class="cabeza">Nº Cuota</th>
                                    <th class="cabeza">Fecha Pago</th>
                                    <th class="cabeza">Saldo Capital</th>
                                    <th class="cabeza">Saldo Interés</th>
                                    <th class="cabeza">Saldo Interés Acuerdo</th>
                                    <th class="cabeza">Saldo Seguros</th>
                                    <th class="cabeza">Saldo Honorarios</th>
                                    <th class="cabeza">Saldo Otros C.</th>
                                    <th class="cabeza">Saldo Gastos Administrativos</th>
                                    <th class="cabeza">Saldo Costos</th>
                                    <th class="cabeza">Saldo Gastos Vencidos</th>
                                    <th class="cabeza">Saldo Recargo Vencido</th>
                                    <th class="cabeza">Saldo Interés vencido</th>
                                    <th class="cabeza">Saldo Seguros vencido</th>                                      
                                    <th class="cabeza">Saldo Honorarios Vencidos</th>
                                    <th class="cabeza">Mora</th>
                                    <th class="cabeza">Honorarios</th>

                                </tr>
                            </thead>    
                            <tbody>
                                <?php 
                                $tacumulado     = 0;
                                $num            =0;
                                while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
                                    $mora           = 0;
                                    $dias           = 0;
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

                                    if($etapa!='23'){
                                        $fecha_pp  = $row["Fecha_Posible_pago"];
                                        $fecha_pp  = date_format($fecha_pp, "Y-m-d");
                                        $fecha_dif = $fecha_pp ;
                                        $fecha_psp  = $row["Fecha_Posible_pago"];
                                        $fecha_psp = date_format($fecha_psp, "d/m/Y");
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
                                    $total_cuota   = ROUND($th + $vbh);
                                    $tacumulado  +=$total_cuota;



                               ?>

                                    <tr>
                                        <input name="<?= "acumulado".$num ?>" id="<?= "acumulado".$num ?>" type="hidden" value="<?= number_format($tacumulado, 2, '.', ',')?>" ></td>
                                        <td style="display: none;"></td>
                                        <td><input name="<?= "seleccion".$num ?>" id="<?= "seleccion".$num ?>" type="checkbox" onchange="cambiarV(<?= $num;?>)" ></td>
                                        <td class="campos" align="right"><b><?= number_format($total_cuota, 2, '.', ',')?></b></td>
                                        <td class="campos" align="right"><b><?= number_format($tacumulado, 2, '.', ',')?><b></td>
                                        <td class="campos" align="right"><?= number_format($row["Numero_Cuota"], 2, '.', ',')?></td> 
                                        <td class="campos" align="center"><?= $fecha_psp?></td>
                                        <td class="campos" align="right"><?= number_format($row["SALDO_CAPITAL"], 2, '.', ',')?></td> 
                                        <td class="campos" align="right"><?= number_format($row["SALDO_INTERES"], 2, '.', ',')?></td>
                                        <td class="campos" align="right"><?= number_format($row["SALDO_INTERES_ACUERDO"], 2, '.', ',')?></td>
                                        <td class="campos" align="right"><?= number_format($row["SALDO_SEGUROS"], 2, '.', ',')?></td>
                                        <td class="campos" align="right"><?= number_format($row["SALDO_HONORARIOS"], 2, '.', ',')?></td>
                                        <td class="campos" align="right"><?= number_format($row["SALDO_OTROS_C"], 2, '.', ',')?></td>
                                        <td class="campos" align="right"><?= number_format($row["SALDO_GASTOS_ADMIN"], 2, '.', ',')?></td>
                                        <td class="campos" align="right"><?= number_format($row["SALDO_GASTOS_ADMIN"], 2, '.', ',')?></td>
                                        <td class="campos" align="right"><?= number_format($row["SALDO_COSTOS"], 2, '.', ',')?></td>
                                        <td class="campos" align="right"><?= number_format($row["SALDO_GASTOS_VENCIDOS"], 2, '.', ',')?></td>
                                        <td class="campos" align="right"><?= number_format($row["SALDO_RECARGO_VENCIDOS"], 2, '.', ',')?></td>
                                        <td class="campos" align="right"><?= number_format($row["SALDO_INTERES_VENCIDOS"], 2, '.', ',')?></td>
                                        <td class="campos" align="right"><?= number_format($row["SALDO_HONORARIOS_VEN"], 2, '.', ',')?></td>
                                        <td class="campos" align="right"><?= number_format($mora, 2, '.', ',') ?></td> 
                                        <td class="campos" align="right"><?= number_format($th, 2, '.', ',') ?></td>                                     
                                    </tr>
                            <?php 
                                    $num +=1;

                                }
                                   $num = $num; 
                                ?>
                            </tbody>

                             <input type="hidden" name="casilla_seleccionada" id="casilla_seleccionada" value="0"/>
                             <input type="hidden" name="valor_seleccionado" id="valor_seleccionado" value="0"/>                                 
                             <input type="hidden" value="<?php echo $num;?>" id="num" name="num"/>

                        </table>                           
                </div>
                <script>
                        function cambiarV(i){                                      
                            var valorselec     = 'acumulado'+i;
                            var id      = $("#"+valorselec).val();
                            var ncheck = 'seleccion'+i;                                      
                            var contador = 0;                                       
                            var status =  $("#"+ncheck).prop('checked'); 
                            var x = $("#num").val();

                            $("#valor_seleccionado").val(id);
                            $("#valor").val(id); 

                            $("#casilla_seleccionada").val(i);                                      


                           if(status == true){                                         
                              for (j = i+1; j < x; j++) {
                                var ncheck = 'seleccion'+j;  
                               $("#"+ncheck).attr('checked', false);                                    
                             } 
                             for (j = 0; j < i; j++) {
                                var ncheck = 'seleccion'+j;  
                               $("#"+ncheck).attr('checked', false);

                             }                                      

                          }                        

                      }

                 </script>
            <?php } ?>
        </div>
    </div> 
    <script src="js/select/select2.full.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function ()
        {
            $(".select2_single").select2({
                allowClear: true
            });
        });
    </script> 

<div class="modal fade" id="modalMensaje" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <label id="mensaje" name="mensaje"></label>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnMsj" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                </button>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>
<script>
    
    $("#credito").change(function(){
        if($("#credito").val()!='' && $("#fecha").val()!=''){
            var numcredito = $("#credito").val();
            $("#numcredito").val(numcredito);
            var fechacredito = $("#fecha").val();            
            $("#fechacredito").val(fechacredito);
            var form_data = { action:3, credito:+$("#credito").val(), fecha:$("#fecha").val()  };
            $.ajax({
                type: "POST",
                url: "jsonPptal/gf_carteraJson.php",
                data: form_data,
                success: function(response)
                { 
                    console.log(response);
                    $("#valor").val(response.trim());                    
                    document.location ='GF_COMPROBANTE_ABONO.php?c='+numcredito+'&f='+fechacredito+'&v='+response;      
               }
            })
         


        }

       
    })
    $("#fecha").change(function(){
        if($("#credito").val()!='' && $("#fecha").val()!=''){ 
         var fechacredito = $("#fecha").val();            
            $("#fechacredito").val(fechacredito);           
            var form_data = { action:3, credito:+$("#credito").val(), fecha:$("#fecha").val()  };
            $.ajax({
                type: "POST",
                url: "jsonPptal/gf_carteraJson.php",
                data: form_data,
                success: function(response)
                { 
                    console.log(response);
                    $("#valor").val(response.trim());                    
                    document.location ='GF_COMPROBANTE_ABONO.php?c='+numcredito+'&f='+fechacredito+'&v='+response;                    
                }
            })

          
        }
       
    })
    
</script>
</body>
</html>

