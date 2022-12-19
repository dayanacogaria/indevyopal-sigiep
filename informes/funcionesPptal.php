<?php 
############################################################################################
#*************************************** MODIFICACIONES ***********************************#
############################################################################################
#14/08/2018 |Erica G. | Funcion Validar Comprobantes Con Detalles Conciliados
#23/01/2018 |ERICA G. | Funciones Registros Y Cuentas Por Pagar Vigencia Anterior
#03/11/2017 |Erica G. | Funciones apropiacion y disponibilidad con fecha
#24/07/2017 |Erica G. | Funcion FechaC Convierte la fecha de formato d/m/Y a formato Y/m/d
#09/06/2017 |ERICA G. |ARCHIVO CREADO
############################################################################################
####FUNCION TRAER LA APROPIACION INICIAL #########
function apropiacion($id_rubFue)
{ 
        @session_start();
        $anno     = $_SESSION['anno'];
	$apropiacion_def = 0;
        $queryApro = "SELECT   detComP.valor valorDetalleComprobantePPTAL, tipComP.tipooperacion  
        from gf_detalle_comprobante_pptal detComP
        left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal
        left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
        left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
        left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
        where tipComP.clasepptal = 13
        and rubFue.id_unico =  $id_rubFue 
        AND comP.parametrizacionanno = $anno "; 

	$apropia = $GLOBALS['mysqli']->query($queryApro);
	if(mysqli_num_rows($apropia)>0){
            if(mysqli_num_rows($apropia)==1){
                $row=mysqli_fetch_row($apropia);
                
                if(($row[1] == 2) || ($row[1] == 4) || ($row[1] == 1))
                {
                        $apropiacion_def += $row[0];
                }
                elseif($row[1] == 3)
                {
                        $apropiacion_def -= $row[0];
                } 
            } else {
                while($row = mysqli_fetch_row($apropia))
                {
                        if(($row[1] == 2) || ($row[1] == 4) || ($row[1] == 1))
                        {
                                $apropiacion_def += $row[0];
                        }
                        elseif($row[1] == 3)
                        {
                                $apropiacion_def -= $row[0];
                        }
                }
            }
        
        }
	return $apropiacion_def;
}	
##############FUNCION TRAER EL VALOR MOVIMIENTO RUBRO###########
function disponibilidades($id_rubFue)
{
        $apropiacion_def = 0;
        @session_start();
        $anno     = $_SESSION['anno'];
        $compania =$_SESSION['compania'];
	$queryApro = "SELECT   detComP.valor valorDetalleComprobantePPTAL, tipComP.tipooperacion 
        from gf_detalle_comprobante_pptal detComP
        left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal
        left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
        left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
        left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
        where tipComP.clasepptal = 14
        and rubFue.id_unico =  $id_rubFue 
        AND  comP.parametrizacionanno = $anno " ; 

	$apropia = $GLOBALS['mysqli']->query($queryApro);
	if(mysqli_num_rows($apropia)>0){
            if(mysqli_num_rows($apropia)==1){
                $row=mysqli_fetch_row($apropia);
                
                if(($row[1] == 2) || ($row[1] == 4) || ($row[1] == 1))
                {
                        $apropiacion_def += $row[0];
                }
                elseif($row[1] == 3)
                {
                        $apropiacion_def -= $row[0];
                } 
            } else {
                while($row = mysqli_fetch_row($apropia))
                {
                        if(($row[1] == 2) || ($row[1] == 4) || ($row[1] == 1))
                        {
                                $apropiacion_def += $row[0];
                        }
                        elseif($row[1] == 3)
                        {
                                $apropiacion_def -= $row[0];
                        }
                }
            }
        
        }

	return $apropiacion_def;
}
########DETALLES PPTAL#########
function detallesnumpptal($id){
    $num = "SELECT COUNT(id_unico) FROM gf_detalle_comprobante_pptal  WHERE comprobantepptal = $id";
    $num =  $GLOBALS['mysqli']->query($num);
    $num = mysqli_fetch_row($num);
    $num = $num[0];
    return ($num);
}
########DETALLES CNT#########
function detallesnumcnt($id){
    $num = "SELECT COUNT(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $id";
    $num =  $GLOBALS['mysqli']->query($num);
    $num = mysqli_fetch_row($num);
    $num = $num[0];
    return ($num);
}
function balanceapropiacion($id){
    $ingresos = 0;
    $gastos = 0; 
    $querySQL = "SELECT det.valor, tipclap.id_unico
    from gf_detalle_comprobante_pptal det
    left join gf_comprobante_pptal com on  com.id_unico = det.comprobantepptal 
    left join gf_rubro_fuente rubf on rubf.id_unico = det.rubrofuente 
    left join gf_fuente fue on fue.id_unico = rubf.fuente 
    left join gf_tipo_comprobante_pptal tipc on tipc.id_unico = com.tipocomprobante 
    left JOIN gf_rubro_pptal rub on rub.id_unico = rubf.rubro 
    left join gf_tipo_clase_pptal tipclap on tipclap.id_unico = rub.tipoclase 
    where (com.id_unico = $id 
    and rub.tipoclase = 7) or (com.id_unico = $id 
    and rub.tipoclase = 6)";
    $resultado = $GLOBALS['mysqli']->query($querySQL);
    while($row = mysqli_fetch_row($resultado))
    {
        if($row[1] == 6)
            $ingresos += $row[0];
        elseif($row[1] == 7)
            $gastos += $row[0];
    }

    if($ingresos == $gastos)
        $balance= 2; 
    else
        $balance= 1;
    
    return $balance;

}
####FUNCION TRAER LA APROPIACION INICIAL #########
function apropiacionidd($id_rubFue, $iddetalle)
{ 
        @session_start();
        $anno     = $_SESSION['anno'];
	$apropiacion_def = 0;
        $queryApro = "SELECT   detComP.valor valorDetalleComprobantePPTAL, tipComP.tipooperacion  
        from gf_detalle_comprobante_pptal detComP
        left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal
        left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
        left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
        left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
        where tipComP.clasepptal = 13
        and rubFue.id_unico =  $id_rubFue 
        AND comP.parametrizacionanno = $anno AND detComP.id_unico !=$iddetalle"; 

	$apropia = $GLOBALS['mysqli']->query($queryApro);
	if(mysqli_num_rows($apropia)>0){
            if(mysqli_num_rows($apropia)==1){
                $row=mysqli_fetch_row($apropia);
                
                if(($row[1] == 2) || ($row[1] == 4) || ($row[1] == 1))
                {
                        $apropiacion_def += $row[0];
                }
                elseif($row[1] == 3)
                {
                        $apropiacion_def -= $row[0];
                } 
            } else {
                while($row = mysqli_fetch_row($apropia))
                {
                        if(($row[1] == 2) || ($row[1] == 4) || ($row[1] == 1))
                        {
                                $apropiacion_def += $row[0];
                        }
                        elseif($row[1] == 3)
                        {
                                $apropiacion_def -= $row[0];
                        }
                }
            }
        
        }
	return $apropiacion_def;
}
###################FUNCION CONVERTIR FECHA DE DATAPICKER#############
function fechaC ($fecha){
        $fecha_div = explode("/", $fecha);
        $dia = trim($fecha_div[0]);
        $mes = trim($fecha_div[1]);
        $anio = trim($fecha_div[2]);
        $fechaC = $anio."-".$mes."-".$dia;
    return ($fechaC);
}
###################Funcion Sumar Dias#############
function fechaSum ($fecha){
        $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
        $sumDias = $GLOBALS['mysqli']->query($querySum);
        if(mysqli_num_rows($sumDias)>0) {
        $rowS = mysqli_fetch_row($sumDias);
        $sumarDias = $rowS[0];
        } else {
            $sumarDias=30;
        }
            $fecha = new DateTime($fecha);
            $fecha->modify('+'.$sumarDias.' day');
            $nuevaFecha = (string)$fecha->format('Y-m-d');
    return ($nuevaFecha);
}
#**********Funcion para saber si el detalle tiene afectación Detalles pptal*************#
function afect($idpptal){
    $af = "SELECT * FROM gf_detalle_comprobante_pptal WHERE comprobanteafectado = $idpptal";
    $af = $GLOBALS['mysqli']->query($af);
    if(mysqli_num_rows($af)>0){
        $res =1;
    } else {
        $res = 0;
    }
    return ($res);
}


####FUNCION TRAER LA APROPIACION INICIAL #########
function apropiacionfecha($id_rubFue, $fecha)
{ 
        @session_start();
        $anno     = $_SESSION['anno'];
	$apropiacion_def = 0;
        $queryApro = "SELECT   detComP.valor valorDetalleComprobantePPTAL, tipComP.tipooperacion, 
            comP.fecha 
        from gf_detalle_comprobante_pptal detComP
        left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal
        left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
        left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
        left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
        where tipComP.clasepptal = 13
        and rubFue.id_unico =  $id_rubFue 
        AND comP.parametrizacionanno = $anno AND comP.fecha <= '$fecha' "; 

	$apropia = $GLOBALS['mysqli']->query($queryApro);
	if(mysqli_num_rows($apropia)>0){
            
            while($row = mysqli_fetch_row($apropia))
            {
                     if(($row[1] == 1))
                    {
                            $apropiacion_def += $row[0];
                    }
                    elseif($row[1] == 3)
                    {
                            $apropiacion_def -= $row[0];
                    } 
                    elseif(($row[1] == 2) ){ 
                            $apropiacion_def += $row[0];
                    } elseif(($row[1] == 4)) {

                        if($row[0]>0){            
                                $apropiacion_def += $row[0];
                        } else {
                            $apropiacion_def += $row[0];
                        }

                    }
            }
            
        
        }
	return $apropiacion_def;
}
	
##############FUNCION TRAER EL VALOR MOVIMIENTO RUBRO###########
function disponibilidadesfecha($id_rubFue, $fecha)
{
        $apropiacion_def = 0;
        @session_start();
        $anno     = $_SESSION['anno'];
        $compania =$_SESSION['compania'];
	$queryApro = "SELECT   detComP.valor valorDetalleComprobantePPTAL, tipComP.tipooperacion , 
            comP.fecha 
        from gf_detalle_comprobante_pptal detComP
        left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal
        left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
        left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
        left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
        where tipComP.clasepptal = 14
        and rubFue.id_unico =  $id_rubFue 
        AND  comP.parametrizacionanno = $anno AND comP.fecha <= '$fecha'" ; 
	$apropia = $GLOBALS['mysqli']->query($queryApro);
	if(mysqli_num_rows($apropia)>0){
            while($row = mysqli_fetch_row($apropia))
            {
                    if(($row[1] == 1))
                    {
                            $apropiacion_def += $row[0];
                    }
                    elseif($row[1] == 3)
                    {
                            $apropiacion_def -= $row[0];
                    } 
                    elseif(($row[1] == 2) ){ 
                        $apropiacion_def += $row[0];
                    } elseif(($row[1] == 4)) {

                        if($row[0]>0){
                                $apropiacion_def += $row[0];
                            
                        } else {
                            $apropiacion_def += $row[0];
                        }

                    }
            }
            
        
        }

	return $apropiacion_def;
}
function egresosva(){ 
    @session_start();
    $anno       = $_SESSION['anno'];
    $nannoa     = anno($anno);
    $nannoan    = $nannoa-1;
    $busc       = $GLOBALS['mysqli']->query("SELECT GROUP_CONCAT(id_unico) 
        FROM gf_parametrizacion_anno WHERE anno <= $nannoan");
    $cv =0;
    if(mysqli_num_rows($busc) >0){ 
        $bs = mysqli_fetch_row($busc);
        $annoan = $bs[0];
        if(!empty($annoan)){ 
	        $queryComp = "SELECT  com.id_unico, com.numero, com.fecha, com.descripcion
	                        FROM gf_comprobante_pptal com
	                        left join gf_tipo_comprobante_pptal tipoCom on tipoCom.id_unico = com.tipocomprobante
	                        WHERE tipoCom.clasepptal = 16 
	                        and tipoCom.tipooperacion = 1 
	                        AND com.parametrizacionanno in($annoan)";
	        $comprobanteP = $GLOBALS['mysqli']->query($queryComp);
	        while ($row = mysqli_fetch_row($comprobanteP)) {
	            $saldDisp = 0;
	            $totalSaldDispo = 0;
	            $queryDetCompro = "SELECT detComp.id_unico, detComp.valor   
	                    FROM gf_detalle_comprobante_pptal detComp, gf_comprobante_pptal comP 
	                    WHERE comP.id_unico = detComp.comprobantepptal 
	                    AND comP.id_unico = " . $row[0];
	            $detCompro = $GLOBALS['mysqli']->query($queryDetCompro);
	            while ($rowDetComp = mysqli_fetch_row($detCompro)) {
	                $saldDisp += $rowDetComp[1];
	                $queryDetAfe = "SELECT
	                          dcp.valor,
	                          tc.tipooperacion
	                        FROM
	                          gf_detalle_comprobante_pptal dcp
	                        LEFT JOIN
	                          gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
	                        LEFT JOIN
	                          gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico
	                        WHERE
	                          dcp.comprobanteafectado =" . $rowDetComp[0];
	                $detAfec = $GLOBALS['mysqli']->query($queryDetAfe);
	                while ($rowDtAf = mysqli_fetch_row($detAfec)) {
	                    if ($rowDtAf[1] == 3) {
	                        $saldDisp = $saldDisp - $rowDtAf[0];
	                    } else {
	                        if (($rowDtAf[1] == 2) || ($rowDtAf[1] == 4)) {
	                            $saldDisp = $saldDisp + $rowDtAf[0];
	                        } else {
	                            $saldDisp = $saldDisp - $rowDtAf[0];
	                        }
	                    }
	                }
	            }
	            $saldo = $saldDisp;
	            if ($saldo > 0) {
	                $cv +=1;
	            }
	        }
	    }
    }
    return $cv;
}

function reservasva(){ 
    @session_start();
    $anno = $_SESSION['anno'];
    $nannoa = anno($anno);
    $nannoan = $nannoa-1;
    $busc = $GLOBALS['mysqli']->query("SELECT GROUP_CONCAT(id_unico) 
       FROM gf_parametrizacion_anno WHERE anno <= $nannoan");
    $cv =0;
    if(mysqli_num_rows($busc) >0){ 
        $bs = mysqli_fetch_row($busc);
        $annoan = $bs[0];
        if(!empty($annoan)){
	        $queryComp = "SELECT  com.id_unico, com.numero, com.fecha, com.descripcion
	                        FROM gf_comprobante_pptal com
	                        left join gf_tipo_comprobante_pptal tipoCom on tipoCom.id_unico = com.tipocomprobante
	                        WHERE tipoCom.clasepptal = 15  
	                        and tipoCom.tipooperacion = 1 
	                        AND com.parametrizacionanno in ($annoan) ";
	        $comprobanteP = $GLOBALS['mysqli']->query($queryComp);
	        while ($row = mysqli_fetch_row($comprobanteP)) {
	            $saldDisp = 0;
	            $totalSaldDispo = 0;
	            $queryDetCompro = "SELECT detComp.id_unico, detComp.valor   
	                    FROM gf_detalle_comprobante_pptal detComp, gf_comprobante_pptal comP 
	                    WHERE comP.id_unico = detComp.comprobantepptal 
	                    AND comP.id_unico = " . $row[0];
	            $detCompro = $GLOBALS['mysqli']->query($queryDetCompro);
	            while ($rowDetComp = mysqli_fetch_row($detCompro)) {
	                $saldDisp += $rowDetComp[1];
	                $queryDetAfe = "SELECT
	                          dcp.valor,
	                          tc.tipooperacion
	                        FROM
	                          gf_detalle_comprobante_pptal dcp
	                        LEFT JOIN
	                          gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
	                        LEFT JOIN
	                          gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico
	                        WHERE
	                          dcp.comprobanteafectado =" . $rowDetComp[0];
	                $detAfec = $GLOBALS['mysqli']->query($queryDetAfe);
	                while ($rowDtAf = mysqli_fetch_row($detAfec)) {
	                    if ($rowDtAf[1] == 3) {
	                        $saldDisp = $saldDisp - $rowDtAf[0];
	                    } else {
	                        if (($rowDtAf[1] == 2) || ($rowDtAf[1] == 4)) {
	                            $saldDisp = $saldDisp + $rowDtAf[0];
	                        } else {
	                            $saldDisp = $saldDisp - $rowDtAf[0];
	                        }
	                    }
	                }
	            }
	            $saldo = $saldDisp;
	            if ($saldo > 0) {
	                $cv +=1;
	            }
	        }
    	}
    }
    return $cv;
}

#********* Funcion Para Traer el Numero Max Comprobantees CNT y Pptal*********#
function numero ($tabla, $tipo, $anno){
    $numeroC = "";
    $nanno   = anno($anno);
    $sql     = "SELECT MAX(numero)
                FROM $tabla WHERE tipocomprobante = $tipo 
                AND parametrizacionanno=$anno";
    $sql     = $GLOBALS['mysqli']->query($sql);
    if(mysqli_num_rows($sql) >0){ 
        $numeroCnt= mysqli_fetch_row($sql);
        if(!empty($numeroCnt[0])){
            $numeroC=$numeroCnt[0]+1;
        }else{
            $numeroC=$nanno.'000001';
        }
    } else {
        $numeroC=$nanno.'000001';
    }
    return $numeroC;
}
# ** Funcion Calcular Valor Disponible ** #
function valorDisponible($IDRubroFuente,$fecha){
    $saldoFecha  = apropiacionfecha($IDRubroFuente, $fecha) - disponibilidadesfecha($IDRubroFuente, $fecha) ;
    $saldoActual = apropiacion($IDRubroFuente) - disponibilidades($IDRubroFuente) ;
    $saldDispo = min($saldoFecha,$saldoActual);
    return $saldDispo;
}

function conciliado ($id_pptal){
   #Validar que no este conciliado
    $p =0;
    $ccnt = "SELECT 
                    DISTINCT dc.*
            FROM 
                    gf_detalle_comprobante_pptal dcp  
            LEFT JOIN 
                    gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
            LEFT JOIN  
            		gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico 
            LEFT JOIN 
            		gf_tipo_comprobante tc ON tcp.id_unico = tc.comprobante_pptal
            LEFT JOIN 
                    gf_comprobante_cnt cn ON cp.numero = cn.numero AND tc.id_unico = cn.tipocomprobante 
            LEFT JOIN 
                    gf_detalle_comprobante dc ON dc.comprobante =cn.id_unico 
            WHERE 
                    cp.id_unico = $id_pptal 
            AND 
                    dc.conciliado =1 AND dc.periodo_conciliado IS NOT NULL";
    $ccnt = $GLOBALS['mysqli']->query($ccnt);
    if(mysqli_num_rows($ccnt)>0){
        $p +=1;
    }
    return $p;
}

# ******* Función para buscar periodo anterior ******* #
function periodoA ($periodo){
    global $con;
    $row = $con->Listar("SELECT DISTINCT pa.* 
        FROM gp_periodo p 
        LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
        WHERE p.id_unico = $periodo AND pa.id_unico !=$periodo  
        ORDER BY pa.fecha_inicial DESC ");
    return $row[0][0];
}
# ******* Función para calcular número de factura ******* #
function numeroFactura ($tipo, $anno){
    $numero     = "";
    $nanno      = anno($anno);
    $sql        = "SELECT MAX(numero_factura)
                FROM gp_factura WHERE tipofactura = $tipo 
                AND parametrizacionanno=$anno";
    $sql     = $GLOBALS['mysqli']->query($sql);
    if(mysqli_num_rows($sql) >0){ 
        $numero = mysqli_fetch_row($sql);
        if(!empty($numero[0])){
            $numero = $numero[0]+1;
        }else{
            $numero = $nanno.'000001';
        }
    } else {
        $numero = $nanno.'000001';
    } 
    return $numero;
}
#******* Formulas Funciones
function resolver($expression){
    
    error_reporting(E_ALL);                                                                //Error de reporte campos vacios
        ini_set('display_errors', TRUE);                                                       //Salida de errores
        ini_set('display_startup_errors', TRUE);                                               //Salida de erreres de inicio				
        $objPHPExcel = new PHPExcel();                                                         //Creamos el objeto excel
        $objPHPExcel->getProperties()->setCreator("Grupo_AAA")                                 //Propiedades de objeto
     ->setLastModifiedBy("Grupo_AAA")
     ->setTitle("Office 2007 XLSX")
     ->setSubject("Office 2007 XLSX")
     ->setDescription("For Office 2007 XLSX, generated using PHP.")
     ->setKeywords("office 2007 openxml php")
     ->setCategory("Test result file");			
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', "=$expression");	                   //Escritura de la formula en la celda A1		
        $callStartTime = microtime(true);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');              //Escrituta del objeto a excel 2017
        $objWriter->setPreCalculateFormulas(true);                                             //Ejecución de formulas
        $objWriter->save('funcionesPptal.xlsx');                                                          //Lectura del archivo
        $callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;                                             		
        $inputFileName = 'funcionesPptal.xlsx';                                                            //Nombre del archivo		
        $objReader = new PHPExcel_Reader_Excel2007();                                           //Inicializamos objeto de lectura		
        $objPHPExcel = $objReader->load($inputFileName);                                        //Cargamos el archivo		
        $value=$objPHPExcel->getActiveSheet()->getCell('A1')->getCalculatedValue();             //Tomamos el valor de la celda  A1		
        return $value; 
    
}

#**********Funcion para traer el año según id*******#
function anno($id){
    $sql= "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $id";
    $sql = $GLOBALS['mysqli']->query($sql);
    $row = mysqli_fetch_row($sql);
    $anno = $row[0];
    return ($anno);
}

function consultarC ($tipo){
    $cn = $con->Listar("SELECT * FROM gp_concepto WHERE tipo_concepto = $tipo");
    return $cn[0][0];
}
function reconstruirComprobantesFactura($id_factura) { 
    global $con;
    @session_start();
    $anno    = $_SESSION['anno'];
    $usuario = $_SESSION['usuario'];
    $compania = $_SESSION['compania'];
    #** Buscar Detalles Factura
    $fc = $con->Listar("SELECT f.*, tc.id_unico as cnt, tcp.id_unico as pptal, 
        tc.tipo_comp_hom as csc 
        FROM gp_factura f 
        LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
        LEFT JOIN gf_tipo_comprobante tc ON tf.tipo_comprobante = tc.id_unico 
        LEFT JOIN gf_tipo_comprobante_pptal tcp ON tc.comprobante_pptal = tcp.id_unico 
        WHERE f.id_unico = $id_factura");
    $numero             = $fc[0]['numero_factura'];
    $tipo               = $fc[0]['tipofactura'];
    $tercero            = $fc[0]['tercero'];
    $fecha              = $fc[0]['fecha_factura'];
    $fechavencimiento   = $fc[0]['fecha_vencimiento'];
    $centrocosto        = $fc[0]['centrocosto'];
    $descripcion        = $fc[0]['descripcion'];
    $estado_factura     = 4;
    $parametrizacionano = $anno;
    $tipocnt            = $fc[0]['cnt'];
    $tipopptal          = $fc[0]['pptal'];
    $tipocausacion      = $fc[0]['csc'];
    #** Buscar si existe pptal
    $cnt        = "";
    $pptal      = "";
    $causacion  = "";
    if(!empty($tipopptal)){
        #** Buscar Si existe **#
        $idpp = $con->Listar("SELECT * FROM gf_comprobante_pptal 
            WHERE tipocomprobante = $tipopptal AND numero = $numero 
            AND parametrizacionanno = $parametrizacionano");
        if(count($idpp)>0){
            $pptal =$idpp[0][0];
        }else {
            $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
                    ( `numero`, `fecha`, 
                    `fechavencimiento`,`descripcion`, 
                    `parametrizacionanno`,`tipocomprobante`,
                    `tercero`,`usuario`, `fecha_elaboracion`) 
            VALUES (:numero, :fecha, 
                    :fechavencimiento,:descripcion,
                    :parametrizacionanno,:tipocomprobante,
                    :tercero, :usuario, :fecha_elaboracion)";
            $sql_dato = array(
                    array(":numero",$numero),
                    array(":fecha",$fecha),
                    array(":fechavencimiento",$fechavencimiento),
                    array(":descripcion",$descripcion),
                    array(":parametrizacionanno",$parametrizacionano),
                    array(":tipocomprobante",$tipopptal),
                    array(":tercero",$tercero),
                    array(":usuario",$usuario),
                    array(":fecha_elaboracion",date('Y-m-d')),

            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            $idpp = $con->Listar("SELECT * FROM gf_comprobante_pptal 
            WHERE tipocomprobante = $tipopptal AND numero = $numero");
            $pptal =$idpp[0][0];
        }
    }
    if(!empty($tipocnt)){
        #** Buscar Si existe **#
        $idpp = $con->Listar("SELECT * FROM gf_comprobante_cnt 
            WHERE tipocomprobante = $tipocnt AND numero = $numero 
            AND parametrizacionanno = $parametrizacionano");
        if(count($idpp)>0){
            $cnt = $idpp[0][0];
        }else {
            #Guardar Comprobante 
            $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                    ( `numero`, `fecha`, 
                    `descripcion`, 
                    `parametrizacionanno`,`tipocomprobante`,
                    `tercero`,`usuario`, `fecha_elaboracion`,
                    `compania`,`estado`) 
            VALUES (:numero, :fecha, 
                    :descripcion,
                    :parametrizacionanno,:tipocomprobante,
                    :tercero,:usuario, :fecha_elaboracion, 
                    :compania, :estado )";
            $sql_dato = array(
                    array(":numero",$numero),
                    array(":fecha",$fecha),
                    array(":descripcion",$descripcion),
                    array(":parametrizacionanno",$parametrizacionano),
                    array(":tipocomprobante",$tipocnt),
                    array(":tercero",$tercero),
                    array(":usuario",$usuario),
                    array(":fecha_elaboracion",date('Y-m-d')),
                    array(":compania",$compania),
                    array(":estado",2),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            $idpp = $con->Listar("SELECT * FROM gf_comprobante_cnt 
            WHERE tipocomprobante = $tipocnt AND numero = $numero");
            $cnt = $idpp[0][0];
        }
    }
    if(!empty($tipocausacion)){
        #** Buscar Si existe **#
        $idpp = $con->Listar("SELECT * FROM gf_comprobante_cnt 
            WHERE tipocomprobante = $tipocausacion 
            AND numero = $numero AND parametrizacionanno = $parametrizacionano");
        if(count($idpp)>0){
            $causacion = $idpp[0][0];
        }else {
            #Guardar Comprobante 
            $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                    ( `numero`, `fecha`, 
                    `descripcion`, 
                    `parametrizacionanno`,`tipocomprobante`,
                    `tercero`,`usuario`, `fecha_elaboracion`,
                    `compania`,`estado`) 
            VALUES (:numero, :fecha, 
                    :descripcion,
                    :parametrizacionanno,:tipocomprobante,
                    :tercero,:usuario, :fecha_elaboracion, 
                    :compania, :estado )";
            $sql_dato = array(
                    array(":numero",$numero),
                    array(":fecha",$fecha),
                    array(":descripcion",$descripcion),
                    array(":parametrizacionanno",$parametrizacionano),
                    array(":tipocomprobante",$tipocausacion),
                    array(":tercero",$tercero),
                    array(":usuario",$usuario),
                    array(":fecha_elaboracion",date('Y-m-d')),
                    array(":compania",$compania),
                    array(":estado",2),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            $idpp = $con->Listar("SELECT * FROM gf_comprobante_cnt 
            WHERE tipocomprobante = $tipocausacion AND numero = $numero");
            $causacion = $idpp[0][0];
        }
    }
    if(!empty($cnt) || !empty($pptal)){
        #** Buscar Detalles Factura ***#
        $rowd = $con->Listar("SELECT * FROM gp_detalle_factura WHERE factura =$id_factura ");
        for ($i = 0; $i < count($rowd); $i++) {
            $id_det     = $rowd[$i][0]; 
            $concepto   = $rowd[$i][2]; 
            $cantidad   = $rowd[$i][4];
            $valor      = $rowd[$i][3]; 
            $iva        = $rowd[$i][5]; 
            $impo       = $rowd[$i][6];
            $ajuste     = $rowd[$i][7];              
            $valor_a    = $rowd[$i][8];
            #** Buscar Configuracion Concepto Rubro Cuenta **#}
            $tipo_cartera = carteradia(0);
            $sqlc=$con->Listar("SELECT
                cf.id_unico,
                cf.concepto ,
                cf.concepto_rubro,
                cf.rubro_fuente,
                crc.cuenta_debito,
                cd.naturaleza,
                crc.cuenta_credito,
                cc.naturaleza,
                crc.cuenta_iva,
                civ.naturaleza,
                crc.cuenta_impoconsumo,
                ci.naturaleza
            FROM gp_configuracion_concepto cf
            LEFT JOIN gf_concepto_rubro cr ON cr.id_unico = cf.concepto_rubro
            LEFT JOIN gf_concepto_rubro_cuenta crc ON cr.id_unico = crc.concepto_rubro
            LEFT JOIN gf_cuenta cd ON crc.cuenta_debito = cd.id_unico
            LEFT JOIN gf_cuenta cc ON crc.cuenta_credito = cc.id_unico
            LEFT JOIN gf_cuenta civ ON civ.id_unico = crc.cuenta_iva
            LEFT JOIN gf_cuenta ci ON ci.id_unico = crc.cuenta_impoconsumo
            WHERE cf.concepto=$concepto and cf.tipo_cartera = $tipo_cartera 
            AND cf.parametrizacionanno = $anno");
            if(count($sqlc)>0){

                $conceptorubro  = $sqlc[0][2];
                $rubrofuente    = $sqlc[0][3];
                $cuenta_debito  = $sqlc[0][4];
                $nat_debito     = $sqlc[0][5];
                $cuenta_credito = $sqlc[0][6];
                $nat_credito    = $sqlc[0][7];
                $cuenta_iva     = $sqlc[0][8];
                $nat_iva        = $sqlc[0][9];
                $cuenta_impo    = $sqlc[0][10];
                $nat_impo       = $sqlc[0][11];
                $id_dpptal      = NULL;
                $valordp = ($valor * $cantidad) + $ajuste;
                $valortc = $valordp+$iva+$impo;
                $mul =0;
                if($valortc!=$valor_a){
                    $mul =1;
                }
                
                if(!empty($pptal)){
                    #*** Insertar Detalles Pptal 
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                          ( `descripcion`,`valor`,
                          `comprobantepptal`,`rubrofuente`, `conceptoRubro`,
                          `tercero`, `proyecto`) 
                    VALUES (:descripcion, :valor, :comprobantepptal, :rubrofuente, 
                    :conceptoRubro, :tercero, :proyecto)";
                    $sql_dato = array(
                        array(":descripcion",$descripcion),
                        array(":valor",$valordp),
                        array(":comprobantepptal",$pptal),
                        array(":rubrofuente",$rubrofuente),
                        array(":conceptoRubro",$conceptorubro),
                        array(":tercero",$tercero),
                        array(":proyecto",2147483647),
                    );
                    $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                    $utl = $con->Listar("SELECT MAX(id_unico) FROM gp_detalle_comprobante_pptal WHERE comprobantepptal = $pptal");
                    $id_dpptal = $utl[0][0];
                }
                if(!empty($cnt)) {
                    if(!empty($cuenta_debito) && !empty($cuenta_credito)){
                        #******** Valor Débito ***********#
                        $valor_debito = 0;
                        if($nat_debito==1){
                            $valor_debito = $valordp;
                        } else {
                            $valor_debito = $valordp*-1;
                        }
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                ( `fecha`, `comprobante`,`valor`,
                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`,`detallecomprobantepptal`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto,:detallecomprobantepptal)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$cnt),
                                array(":valor",($valor_debito)),
                                array(":cuenta",$cuenta_debito),   
                                array(":naturaleza",$nat_debito),
                                array(":tercero",$tercero),
                                array(":centrocosto",$centrocosto),
                                array(":detallecomprobantepptal",$id_dpptal),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        
                        #******* Valor Crédito **********#
                        $valor_credito = 0;
                        if($nat_credito==1){
                            $valor_credito = $valordp *-1;
                        } else {
                            $valor_credito = $valordp;
                        }
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                ( `fecha`, `comprobante`,`valor`,
                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`,`detallecomprobantepptal`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto,:detallecomprobantepptal)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$cnt),
                                array(":valor",($valor_credito)),
                                array(":cuenta",$cuenta_credito),   
                                array(":naturaleza",$nat_credito),
                                array(":tercero",$tercero),
                                array(":centrocosto",$centrocosto),
                                array(":detallecomprobantepptal",$id_dpptal),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        $id_dc = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $cnt");
                        $id_dc = $id_dc[0][0];
                        
                        if($iva>0){
                            if($mul ==1){
                                $valor_iva = ($iva * $cantidad);
                            } else {
                                $valor_iva = ($iva);
                            }
                            if($nat_iva==1){
                                $valor_iva = $valor_iva*-1;
                            } else {
                                $valor_iva = $valor_iva;
                            }
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                    ( `fecha`, `comprobante`,`valor`,
                                    `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                            VALUES (:fecha,  :comprobante,:valor, 
                                    :cuenta,:naturaleza, :tercero, :centrocosto)";
                            $sql_dato = array(
                                    array(":fecha",$fecha),
                                    array(":comprobante",$cnt),
                                    array(":valor",($valor_iva)),
                                    array(":cuenta",$cuenta_iva),   
                                    array(":naturaleza",$nat_credito),
                                    array(":tercero",$tercero),
                                    array(":centrocosto",$centrocosto),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            $valor_iva = ($iva * $cantidad);
                            if($nat_debito==1){
                                $valor_iva = $valor_iva;
                            } else {
                                $valor_iva = $valor_iva*-1;
                            }
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                    ( `fecha`, `comprobante`,`valor`,
                                    `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                            VALUES (:fecha,  :comprobante,:valor, 
                                    :cuenta,:naturaleza, :tercero, :centrocosto)";
                            $sql_dato = array(
                                    array(":fecha",$fecha),
                                    array(":comprobante",$cnt),
                                    array(":valor",($valor_iva)),
                                    array(":cuenta",$cuenta_debito),   
                                    array(":naturaleza",$nat_debito),
                                    array(":tercero",$tercero),
                                    array(":centrocosto",$centrocosto),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                        }
                        if($impo>0){
                            if($mul ==1){
                                $valor_impo = ($impo * $cantidad);
                            } else {
                                $valor_impo = ($impo);
                            }
                            if($nat_impo==1){
                                $valor_impo = $valor_impo*-1;
                            } else {
                                $valor_impo = $valor_impo;
                            }
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                    ( `fecha`, `comprobante`,`valor`,
                                    `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                            VALUES (:fecha,  :comprobante,:valor, 
                                    :cuenta,:naturaleza, :tercero, :centrocosto)";
                            $sql_dato = array(
                                    array(":fecha",$fecha),
                                    array(":comprobante",$cnt),
                                    array(":valor",($valor_impo)),
                                    array(":cuenta",$cuenta_impo),   
                                    array(":naturaleza",$nat_impo),
                                    array(":tercero",$tercero),
                                    array(":centrocosto",$centrocosto),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            if($nat_debito==1){
                                $valor_impo = $valor_impo;
                            } else {
                                $valor_impo = $valor_impo*-1;
                            }
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                    ( `fecha`, `comprobante`,`valor`,
                                    `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                            VALUES (:fecha,  :comprobante,:valor, 
                                    :cuenta,:naturaleza, :tercero, :centrocosto)";
                            $sql_dato = array(
                                    array(":fecha",$fecha),
                                    array(":comprobante",$cnt),
                                    array(":valor",($valor_impo)),
                                    array(":cuenta",$cuenta_debito),   
                                    array(":naturaleza",$nat_debito),
                                    array(":tercero",$tercero),
                                    array(":centrocosto",$centrocosto),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                        }
                         #****** Actualizar id factura ***#
                        $sql_cons ="UPDATE `gp_detalle_factura` 
                                SET `detallecomprobante`=:detallecomprobante 
                                WHERE `id_unico`=:id_unico";
                        $sql_dato = array(
                                array(":detallecomprobante",$id_dc),
                                array(":id_unico",$id_det),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                }
            }
        }
        
    }
}
function reconstruirComprobantesFacturaDetalle($id_factura, $id_detalle) { 
    global $con;
    @session_start();
    $anno    = $_SESSION['anno'];
    $usuario = $_SESSION['usuario'];
    $compania = $_SESSION['compania'];
    #** Buscar Detalles Factura
    $fc = $con->Listar("SELECT f.*, tc.id_unico as cnt, tcp.id_unico as pptal, 
        tc.tipo_comp_hom as csc 
        FROM gp_factura f 
        LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
        LEFT JOIN gf_tipo_comprobante tc ON tf.tipo_comprobante = tc.id_unico 
        LEFT JOIN gf_tipo_comprobante_pptal tcp ON tc.comprobante_pptal = tcp.id_unico 
        WHERE f.id_unico = $id_factura");
    $numero             = $fc[0]['numero_factura'];
    $tipo               = $fc[0]['tipofactura'];
    $tercero            = $fc[0]['tercero'];
    $fecha              = $fc[0]['fecha_factura'];
    $fechavencimiento   = $fc[0]['fecha_vencimiento'];
    $centrocosto        = $fc[0]['centrocosto'];
    $descripcion        = $fc[0]['descripcion'];
    $estado_factura     = 4;
    $parametrizacionano = $anno;
    $tipocnt            = $fc[0]['cnt'];
    $tipopptal          = $fc[0]['pptal'];
    $tipocausacion      = $fc[0]['csc'];
    #** Buscar si existe pptal
    $cnt        = "";
    $pptal      = "";
    $causacion  = "";
    if(!empty($tipopptal)){
        #** Buscar Si existe **#
        $idpp = $con->Listar("SELECT * FROM gf_comprobante_pptal 
            WHERE tipocomprobante = $tipopptal AND numero = $numero 
            AND parametrizacionanno = $parametrizacionano");
        if(count($idpp)>0){
            $pptal =$idpp[0][0];
        }else {
            $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
                    ( `numero`, `fecha`, 
                    `fechavencimiento`,`descripcion`, 
                    `parametrizacionanno`,`tipocomprobante`,
                    `tercero`,`usuario`, `fecha_elaboracion`) 
            VALUES (:numero, :fecha, 
                    :fechavencimiento,:descripcion,
                    :parametrizacionanno,:tipocomprobante,
                    :tercero, :usuario, :fecha_elaboracion)";
            $sql_dato = array(
                    array(":numero",$numero),
                    array(":fecha",$fecha),
                    array(":fechavencimiento",$fechavencimiento),
                    array(":descripcion",$descripcion),
                    array(":parametrizacionanno",$parametrizacionano),
                    array(":tipocomprobante",$tipopptal),
                    array(":tercero",$tercero),
                    array(":usuario",$usuario),
                    array(":fecha_elaboracion",date('Y-m-d')),

            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            $idpp = $con->Listar("SELECT * FROM gf_comprobante_pptal 
            WHERE tipocomprobante = $tipopptal AND numero = $numero");
            $pptal =$idpp[0][0];
        }
    }
    if(!empty($tipocnt)){
        #** Buscar Si existe **#
        $idpp = $con->Listar("SELECT * FROM gf_comprobante_cnt 
            WHERE tipocomprobante = $tipocnt AND numero = $numero 
            AND parametrizacionanno = $parametrizacionano");
        if(count($idpp)>0){
            $cnt = $idpp[0][0];
        }else {
            #Guardar Comprobante 
            $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                    ( `numero`, `fecha`, 
                    `descripcion`, 
                    `parametrizacionanno`,`tipocomprobante`,
                    `tercero`,`usuario`, `fecha_elaboracion`,
                    `compania`,`estado`) 
            VALUES (:numero, :fecha, 
                    :descripcion,
                    :parametrizacionanno,:tipocomprobante,
                    :tercero,:usuario, :fecha_elaboracion, 
                    :compania, :estado )";
            $sql_dato = array(
                    array(":numero",$numero),
                    array(":fecha",$fecha),
                    array(":descripcion",$descripcion),
                    array(":parametrizacionanno",$parametrizacionano),
                    array(":tipocomprobante",$tipocnt),
                    array(":tercero",$tercero),
                    array(":usuario",$usuario),
                    array(":fecha_elaboracion",date('Y-m-d')),
                    array(":compania",$compania),
                    array(":estado",2),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            $idpp = $con->Listar("SELECT * FROM gf_comprobante_cnt 
            WHERE tipocomprobante = $tipocnt AND numero = $numero");
            $cnt = $idpp[0][0];
        }
    }
    if(!empty($tipocausacion)){
        #** Buscar Si existe **#
        $idpp = $con->Listar("SELECT * FROM gf_comprobante_cnt 
            WHERE tipocomprobante = $tipocausacion 
            AND numero = $numero AND parametrizacionanno = $parametrizacionano");
        if(count($idpp)>0){
            $causacion = $idpp[0][0];
        }else {
            #Guardar Comprobante 
            $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                    ( `numero`, `fecha`, 
                    `descripcion`, 
                    `parametrizacionanno`,`tipocomprobante`,
                    `tercero`,`usuario`, `fecha_elaboracion`,
                    `compania`,`estado`) 
            VALUES (:numero, :fecha, 
                    :descripcion,
                    :parametrizacionanno,:tipocomprobante,
                    :tercero,:usuario, :fecha_elaboracion, 
                    :compania, :estado )";
            $sql_dato = array(
                    array(":numero",$numero),
                    array(":fecha",$fecha),
                    array(":descripcion",$descripcion),
                    array(":parametrizacionanno",$parametrizacionano),
                    array(":tipocomprobante",$tipocausacion),
                    array(":tercero",$tercero),
                    array(":usuario",$usuario),
                    array(":fecha_elaboracion",date('Y-m-d')),
                    array(":compania",$compania),
                    array(":estado",2),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            $idpp = $con->Listar("SELECT * FROM gf_comprobante_cnt 
            WHERE tipocomprobante = $tipocausacion AND numero = $numero");
            $causacion = $idpp[0][0];
        }
    }
    if(!empty($cnt) || !empty($pptal)){
        #** Buscar Detalles Factura ***#
        $rowd = $con->Listar("SELECT * FROM gp_detalle_factura WHERE id_unico = $id_detalle ");
        
        for ($i = 0; $i < count($rowd); $i++) {
            $id_det     = $rowd[$i][0]; 
            $concepto   = $rowd[$i][2]; 
            $cantidad   = $rowd[$i][4];
            $valor      = $rowd[$i][3]; 
            $iva        = $rowd[$i][5]; 
            $impo       = $rowd[$i][6];
            $ajuste     = $rowd[$i][7];              
            $valor_a    = $rowd[$i][8];
            #** Buscar Configuracion Concepto Rubro Cuenta **#}
            $tipo_cartera = carteradia(0);
            $sqlc=$con->Listar("SELECT
                cf.id_unico,
                cf.concepto ,
                cf.concepto_rubro,
                cf.rubro_fuente,
                crc.cuenta_debito,
                cd.naturaleza,
                crc.cuenta_credito,
                cc.naturaleza,
                crc.cuenta_iva,
                civ.naturaleza,
                crc.cuenta_impoconsumo,
                ci.naturaleza
            FROM gp_configuracion_concepto cf
            LEFT JOIN gf_concepto_rubro cr ON cr.id_unico = cf.concepto_rubro
            LEFT JOIN gf_concepto_rubro_cuenta crc ON cr.id_unico = crc.concepto_rubro
            LEFT JOIN gf_cuenta cd ON crc.cuenta_debito = cd.id_unico
            LEFT JOIN gf_cuenta cc ON crc.cuenta_credito = cc.id_unico
            LEFT JOIN gf_cuenta civ ON civ.id_unico = crc.cuenta_iva
            LEFT JOIN gf_cuenta ci ON ci.id_unico = crc.cuenta_impoconsumo
            WHERE cf.concepto=$concepto and cf.tipo_cartera = $tipo_cartera 
            AND cf.parametrizacionanno = $anno");
            if(count($sqlc)>0){

                $conceptorubro  = $sqlc[0][2];
                $rubrofuente    = $sqlc[0][3];
                $cuenta_debito  = $sqlc[0][4];
                $nat_debito     = $sqlc[0][5];
                $cuenta_credito = $sqlc[0][6];
                $nat_credito    = $sqlc[0][7];
                $cuenta_iva     = $sqlc[0][8];
                $nat_iva        = $sqlc[0][9];
                $cuenta_impo    = $sqlc[0][10];
                $nat_impo       = $sqlc[0][11];
                $id_dpptal      = NULL;
                $valordp = ($valor * $cantidad) + $ajuste;
                $valortc = $valordp+$iva+$impo;
                $mul =0;
                if($valortc!=$valor_a){
                    $mul =1;
                }
                
                if(!empty($pptal)){
                    #*** Insertar Detalles Pptal 
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                          ( `descripcion`,`valor`,
                          `comprobantepptal`,`rubrofuente`, `conceptoRubro`,
                          `tercero`, `proyecto`) 
                    VALUES (:descripcion, :valor, :comprobantepptal, :rubrofuente, 
                    :conceptoRubro, :tercero, :proyecto)";
                    $sql_dato = array(
                        array(":descripcion",$descripcion),
                        array(":valor",$valordp),
                        array(":comprobantepptal",$pptal),
                        array(":rubrofuente",$rubrofuente),
                        array(":conceptoRubro",$conceptorubro),
                        array(":tercero",$tercero),
                        array(":proyecto",2147483647),
                    );
                    $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                    $utl = $con->Listar("SELECT MAX(id_unico) FROM gp_detalle_comprobante_pptal WHERE comprobantepptal = $pptal");
                    $id_dpptal = $utl[0][0];
                }
                if(!empty($cnt)) {
                    if(!empty($cuenta_debito) && !empty($cuenta_credito)){
                        #******** Valor Débito ***********#
                        $valor_debito = 0;
                        if($nat_debito==1){
                            $valor_debito = $valordp;
                        } else {
                            $valor_debito = $valordp*-1;
                        }
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                ( `fecha`, `comprobante`,`valor`,
                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`,`detallecomprobantepptal`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto,:detallecomprobantepptal)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$cnt),
                                array(":valor",($valor_debito)),
                                array(":cuenta",$cuenta_debito),   
                                array(":naturaleza",$nat_debito),
                                array(":tercero",$tercero),
                                array(":centrocosto",$centrocosto),
                                array(":detallecomprobantepptal",$id_dpptal),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        
                        #******* Valor Crédito **********#
                        $valor_credito = 0;
                        if($nat_credito==1){
                            $valor_credito = $valordp *-1;
                        } else {
                            $valor_credito = $valordp;
                        }
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                ( `fecha`, `comprobante`,`valor`,
                                `cuenta`,`naturaleza`,`tercero`, `centrocosto`,`detallecomprobantepptal`) 
                        VALUES (:fecha,  :comprobante,:valor, 
                                :cuenta,:naturaleza, :tercero, :centrocosto,:detallecomprobantepptal)";
                        $sql_dato = array(
                                array(":fecha",$fecha),
                                array(":comprobante",$cnt),
                                array(":valor",($valor_credito)),
                                array(":cuenta",$cuenta_credito),   
                                array(":naturaleza",$nat_credito),
                                array(":tercero",$tercero),
                                array(":centrocosto",$centrocosto),
                                array(":detallecomprobantepptal",$id_dpptal),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        $id_dc = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $cnt");
                        $id_dc = $id_dc[0][0];
                        
                        if($iva>0){
                            if($mul ==1){
                                $valor_iva = ($iva * $cantidad);
                            } else {
                                $valor_iva = ($iva);
                            }
                            if($nat_iva==1){
                                $valor_iva = $valor_iva*-1;
                            } else {
                                $valor_iva = $valor_iva;
                            }
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                    ( `fecha`, `comprobante`,`valor`,
                                    `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                            VALUES (:fecha,  :comprobante,:valor, 
                                    :cuenta,:naturaleza, :tercero, :centrocosto)";
                            $sql_dato = array(
                                    array(":fecha",$fecha),
                                    array(":comprobante",$cnt),
                                    array(":valor",($valor_iva)),
                                    array(":cuenta",$cuenta_iva),   
                                    array(":naturaleza",$nat_credito),
                                    array(":tercero",$tercero),
                                    array(":centrocosto",$centrocosto),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            $valor_iva = ($iva * $cantidad);
                            if($nat_debito==1){
                                $valor_iva = $valor_iva;
                            } else {
                                $valor_iva = $valor_iva*-1;
                            }
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                    ( `fecha`, `comprobante`,`valor`,
                                    `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                            VALUES (:fecha,  :comprobante,:valor, 
                                    :cuenta,:naturaleza, :tercero, :centrocosto)";
                            $sql_dato = array(
                                    array(":fecha",$fecha),
                                    array(":comprobante",$cnt),
                                    array(":valor",($valor_iva)),
                                    array(":cuenta",$cuenta_debito),   
                                    array(":naturaleza",$nat_debito),
                                    array(":tercero",$tercero),
                                    array(":centrocosto",$centrocosto),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                        }
                        if($impo>0){
                            if($mul ==1){
                                $valor_impo = ($impo * $cantidad);
                            } else {
                                $valor_impo = ($impo);
                            }
                            if($nat_impo==1){
                                $valor_impo = $valor_impo*-1;
                            } else {
                                $valor_impo = $valor_impo;
                            }
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                    ( `fecha`, `comprobante`,`valor`,
                                    `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                            VALUES (:fecha,  :comprobante,:valor, 
                                    :cuenta,:naturaleza, :tercero, :centrocosto)";
                            $sql_dato = array(
                                    array(":fecha",$fecha),
                                    array(":comprobante",$cnt),
                                    array(":valor",($valor_impo)),
                                    array(":cuenta",$cuenta_impo),   
                                    array(":naturaleza",$nat_impo),
                                    array(":tercero",$tercero),
                                    array(":centrocosto",$centrocosto),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            if($nat_debito==1){
                                $valor_impo = $valor_impo;
                            } else {
                                $valor_impo = $valor_impo*-1;
                            }
                            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                    ( `fecha`, `comprobante`,`valor`,
                                    `cuenta`,`naturaleza`,`tercero`, `centrocosto`) 
                            VALUES (:fecha,  :comprobante,:valor, 
                                    :cuenta,:naturaleza, :tercero, :centrocosto)";
                            $sql_dato = array(
                                    array(":fecha",$fecha),
                                    array(":comprobante",$cnt),
                                    array(":valor",($valor_impo)),
                                    array(":cuenta",$cuenta_debito),   
                                    array(":naturaleza",$nat_debito),
                                    array(":tercero",$tercero),
                                    array(":centrocosto",$centrocosto),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                        }
                         #****** Actualizar id factura ***#
                        $sql_cons ="UPDATE `gp_detalle_factura` 
                                SET `detallecomprobante`=:detallecomprobante 
                                WHERE `id_unico`=:id_unico";
                        $sql_dato = array(
                                array(":detallecomprobante",$id_dc),
                                array(":id_unico",$id_det),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        
                    }
                }
            }
        }
        
    }
}
#*** Eliminar Detalles Cnt Por comprobante
function eliminardetallescnt($cnt){
    global $con;
    #*** Eliminar Detalles Cnt
    $sql_cons ="DELETE FROM  `gf_detalle_comprobante`
    WHERE `comprobante`=:comprobante ";
    $sql_dato = array(
        array(":comprobante",$cnt)
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #var_dump($sql_cons,$sql_dato);
    if(empty($resp)){
        $rta = 1;
    } else {
        $rta = 2;
    }
    return $rta;
}
function eliminarDetallesRetencion($cnt){
    global $con;
    #*** Eliminar Detalles Cnt
    $sql_cons ="DELETE FROM  `gf_retencion`
    WHERE `comprobante`=:comprobante ";
    $sql_dato = array(
        array(":comprobante",$cnt)
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #var_dump($resp);
    if(empty($resp)){
        $rta = 1;
    } else {
        $rta = 2;
    }
    return $rta;
}
#*** Eliminar Detalles Pptal Por comprobante
function eliminardetallespptal($pptal){
    global $con;
    #*** Eliminar Detalles Cnt
    $sql_cons ="DELETE FROM  `gf_detalle_comprobante_pptal`
    WHERE `comprobantepptal`=:comprobantepptal ";
    $sql_dato = array(
        array(":comprobantepptal",$pptal)
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #var_dump($resp);
    if(empty($resp)){
        $rta = 1;
    } else {
        $rta = 2;
    }
    return $rta;
}
#*** Funcion Generar Detalles Comprobantes Relacionados Al Pago
function registrarDetallesPago($idpago, $id_cnt, $id_pptal,$id_causacion){
    global $con;
    global $panno;
    #Buscar Datos Factura
    $df = $con->Listar("SELECT DISTINCT
                    f.id_unico as id_factura,
                    f.numero_factura as numero_factura,
                    tp.nombre as tipo_nombre,
                    f.tercero as tercero,
                    f.descripcion as descripcion,
                    f.fecha_factura as fecha_factura,
                    f.centrocosto as centro_costo,
                    pg.banco as banco,
                    pg.fecha_pago as fecha_pago,
                    df.id_unico as detalle_factura,
                    dp.valor as valor,
                    dp.iva as iva,
                    dp.impoconsumo as impoconsumo,
                    dp.ajuste_peso as ajuste,
                    c.id_unico as cuenta,
                    c.naturaleza as n_cuenta,
                    dp.id_unico as detalle_pago,
                    df.concepto_tarifa as concepto
                FROM  gp_detalle_pago dp
                LEFT JOIN gp_pago pg ON pg.id_unico = dp.pago
                LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico
                LEFT JOIN gp_factura f ON f.id_unico = df.factura
                LEFT JOIN gp_tipo_factura tp ON tp.id_unico = f.tipofactura
                LEFT JOIN gf_cuenta_bancaria cb on pg.banco = cb.id_unico
            	LEFT JOIN gf_cuenta c ON cb.cuenta = c.id_unico
                WHERE dp.pago = $idpago");

    $vpt = 0;
    $cuentaB    = $df[0]['cuenta'];
    $Ncuenta    = $df[0]['n_cuenta'];
    if(count($df)>0){
        for ($i = 0; $i < count($df); $i++) {

            $id_d_pg        = $df[$i]['detalle_pago'];
            $valor          = $df[$i]['valor'];
            $iva            = (double) $df[$i]['iva'];
            $impo           = (double) $df[$i]['impoconsumo'];
            $ajuste         = (double) $df[$i]['ajuste'];
            $factura        = $df[$i]['id_factura'];
            $fecha_factura  = $df[$i]['fecha_factura'];
            $responsable    = $df[$i]['tercero'];
            $centrocosto    = $df[$i]['centro_costo'];
            $fecha_pago     = $df[$i]['fecha_pago'];
            $detalle_f      = $df[$i]['detalle_factura'];
            $concepto       = $df[$i]['concepto'];

            #**** Buscar Diferencia Dias Cartera *******#
            $dias	= (strtotime($fecha_factura)-strtotime($fecha_pago))/86400;
            $dias 	= abs($dias);
            $dias       = floor($dias);
            #**** Buscar Configuración Concepto Por Tipo Cartera *******#
            $tipo_c     = carteradia($dias);

            $sqlc=$con->Listar("SELECT
                cf.id_unico,
                cf.concepto ,
                cf.concepto_rubro,
                cf.rubro_fuente,
                crc.cuenta_debito,
                cd.naturaleza,
                crc.cuenta_credito,
                cc.naturaleza,
                crc.cuenta_iva,
                civ.naturaleza,
                crc.cuenta_impoconsumo,
                ci.naturaleza
            FROM gp_configuracion_concepto cf
            LEFT JOIN gf_concepto_rubro cr ON cr.id_unico = cf.concepto_rubro
            LEFT JOIN gf_concepto_rubro_cuenta crc ON cr.id_unico = crc.concepto_rubro
            LEFT JOIN gf_cuenta cd ON crc.cuenta_debito = cd.id_unico
            LEFT JOIN gf_cuenta cc ON crc.cuenta_credito = cc.id_unico
            LEFT JOIN gf_cuenta civ ON civ.id_unico = crc.cuenta_iva
            LEFT JOIN gf_cuenta ci ON ci.id_unico = crc.cuenta_impoconsumo
            WHERE cf.concepto=$concepto and cf.tipo_cartera = $tipo_c
            AND cf.parametrizacionanno = $panno");

            if(count($sqlc)>0){

                $conceptorubro  = $sqlc[0][2];
                $rubrofuente    = $sqlc[0][3];
                #********** Detalle Pptal*****************#
                $insertP = "INSERT INTO gf_detalle_comprobante_pptal
                        (valor, comprobantepptal, conceptorubro,
                        tercero, proyecto, rubrofuente)
                        VALUES(($valor+$ajuste), $id_pptal, $conceptorubro,
                        $responsable, 2147483647, $rubrofuente)";
                $resultP = $GLOBALS['mysqli']->query($insertP);
                $id_dp = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_pptal");
                $id_dp = $id_dp[0][0];
                if(empty($id_dp)){
                   $id_dp = 'NULL';
                }
                ##********** Detalle Cnt*****************#
                #cuenta credito
                $cc =$sqlc[0][6];
                #cuenta debito
                $cd =$sqlc[0][4];
                $naturalezad = $sqlc[0][5];
                #Verificar Naturaleza
                $naturalezac = $sqlc[0][7];
                $vpt += $valor+$ajuste;
                if($naturalezad==1){
                    $valorc = ($valor+$ajuste)*-1;

                } else {
                    $valorc = ($valor+$ajuste);
                }
                #Insertar Detalle Cnt
                $insertD = "INSERT INTO gf_detalle_comprobante
                        (fecha, valor,
                        comprobante, naturaleza, cuenta,
                        tercero, proyecto, centrocosto,
                        detallecomprobantepptal)
                        VALUES('$fecha_pago', $valorc,
                        $id_cnt, $naturalezad, $cd,
                        $responsable,  2147483647, $centrocosto, $id_dp)";
                $resultado = $GLOBALS['mysqli']->query($insertD);


                $id_dc = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $id_cnt");
                $id_dc = $id_dc[0][0];

                # Actualizar Detalle Pago
                $UpdateD = "UPDATE gp_detalle_pago SET detallecomprobante = $id_dc WHERE id_unico = $id_d_pg";
                $UpdateD = $GLOBALS['mysqli']->query($UpdateD);

                #Insertar Detalle Causacion
                ##Debito
                if($naturalezad==1){
                    $valord = ($valor+$ajuste);
                } else {
                    $valord = ($valor+$ajuste)*-1;
                }
                if($naturalezac==2){
                    $valorc = ($valor+$ajuste);
                } else {
                    $valorc = ($valor+$ajuste)*-1;
                }
                if($cd == $cc){

                } else {
                    $insertD = "INSERT INTO gf_detalle_comprobante
                            (fecha, valor,
                            comprobante, naturaleza, cuenta,
                            tercero, proyecto, centrocosto,
                            detalleafectado)
                            VALUES('$fecha_pago', $valord,
                            $id_causacion, $naturalezad, $cd,
                            $responsable,  2147483647, $centrocosto, $id_dc)";
                    $resultado = $GLOBALS['mysqli']->query($insertD);
                    #** Credito

                    $insertD = "INSERT INTO gf_detalle_comprobante
                            (fecha, valor,
                            comprobante, naturaleza, cuenta,
                            tercero, proyecto, centrocosto,
                            detalleafectado)
                            VALUES('$fecha_pago', $valorc,
                            $id_causacion, $naturalezac, $cc,
                            $responsable,  2147483647, $centrocosto, $id_dc)";
                    $resultado = $GLOBALS['mysqli']->query($insertD);

                }

                #Registrar Cuenta Iva
                if($iva !="" || $iva !=0){
                    
                    #Verificar Naturaleza
                    $cd =$sqlc[0][4];
                    $naturalezad = $sqlc[0][5];
                    #Verificar Naturaleza
                    $naturalezac = $sqlc[0][7];
                    $vpt += $iva;
                    if($naturalezad==1){
                        $valorc = $iva*-1;

                    } else {
                        $valorc = $iva;
                    }
                    $insertD = "INSERT INTO gf_detalle_comprobante
                        (fecha, valor,
                        comprobante, naturaleza, cuenta,
                        tercero, proyecto, centrocosto)
                        VALUES('$fecha_pago', $valorc,
                        $id_cnt, $naturalezad, $cd,
                        $responsable,  2147483647, $centrocosto)";
                    $resultado = $GLOBALS['mysqli']->query($insertD);

                }
                #Registrar Cuenta Impoconsumo
                if($impo !="" || $impo !=0){
                    #Verificar Naturaleza
                    $cimpo           = $sqlc[0][10];
                    $naturalezacim   = $sqlc[0][11];
                    $vpt += $impo;
                    if($naturalezacim==1){
                        $valorcim = $impo*-1;
                    } else {
                        $valorcim = $impo;
                    }
                    $insertD = "INSERT INTO gf_detalle_comprobante
                        (fecha, valor,
                        comprobante, naturaleza, cuenta,
                        tercero, proyecto, centrocosto)
                        VALUES('$fecha_pago', $valorcim,
                        $id_cnt, $naturalezacim, $cimpo,
                        $responsable,  2147483647, $centrocosto)";
                    $resultado = $GLOBALS['mysqli']->query($insertD);
                }

            }
        }
    }
    #SE VERIFICA SI EL COMPROBANTE CNT TIENE RETENCIONES
    $row2 = $con->Listar("SELECT r.id_unico, r.valorretencion, tr.cuenta , c.naturaleza FROM gf_retencion r
            LEFT JOIN gf_tipo_retencion tr ON r.tiporetencion = tr.id_unico
            LEFT JOIN gf_cuenta c ON tr.cuenta = c.id_unico
            WHERE r.comprobante  = $id_cnt");
    if(count($row2)>0){
        for ($i = 0; $i < count($row2); $i++) {
            if($row2[$i][3]==1){
                $valorret = $row2[$i][1];
            } else {
                $valorret = $row2[$i][1]*-1;
            }
            $ccuenta = $row2[$i][3];
            $nnatur  = $row2[$i][2];
            $insertD = "INSERT INTO gf_detalle_comprobante
                (fecha, valor,
                comprobante, naturaleza, cuenta,
                tercero, proyecto, centrocosto)
                VALUES('$fecha_pago', $valorret,
                $id_cnt, $ccuenta, $nnatur,
                $responsable,  2147483647, $centrocosto)";
            $resultado = $GLOBALS['mysqli']->query($insertD);
            $vpt -= $row2[$i][1];
        }

    }
    if($vpt !=0){
        #Registrar Cuenta de Banco
        if($Ncuenta ==1){
            $vpt = $vpt;
        } else {
            $vpt = $vpt*-1;
        }
        $insertD = "INSERT INTO gf_detalle_comprobante
            (fecha, valor,
            comprobante, naturaleza, cuenta,
            tercero, proyecto, centrocosto)
            VALUES('$fecha_pago', $vpt,
            $id_cnt, $Ncuenta, $cuentaB,
            $responsable,  2147483647, $centrocosto)";
        $resultado = $GLOBALS['mysqli']->query($insertD);
    }
    return true;
}
#*** Buscar Tipo Cartera
function carteradia($dia){
    global $con;
    $crt = $con->Listar("SELECT * FROM `gp_tipo_cartera` where dia_final >= $dia and dia_inicial <= $dia");
    return $crt[0][0];
}
#*** Buscar Si Hay Tipo Causación
function causacion($cnt){
    global $con;

    $id_c = $con->Listar("SELECT DISTINCT dca.comprobante
        FROM gf_comprobante_cnt cn
        LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante
        LEFT JOIN gf_detalle_comprobante dca ON dc.id_unico = dca.detalleafectado
        where cn.id_unico =$cnt");
    return $id_c[0][0];
}
# *** Funcion Registrar Detalle Recaudo ** #
function guardarPagoFactura($concepto,$factura,$pago,$valor){
    @session_start();
    $tipo_compania = $_SESSION['tipo_compania'];
    global $con;
    $saldo_factura = saldoFactura($factura);
    
    #Buscar Detalles Factura
    if($concepto!=""){
        $row = $con->Listar("SELECT id_unico, iva, impoconsumo,
            ajuste_peso, valor_total_ajustado, cantidad 
            FROM gp_detalle_factura
            WHERE factura = $factura AND concepto_tarifa = $concepto AND valor_total_ajustado !=0 
            ORDER BY valor_total_ajustado DESC");
    } else {
        $row = $con->Listar("SELECT id_unico, iva, impoconsumo,
            ajuste_peso, valor_total_ajustado, cantidad 
            FROM gp_detalle_factura
            WHERE factura = $factura AND valor_total_ajustado !=0 
            ORDER BY valor_total_ajustado ASC");
    }
    $saldo_final = $saldo_factura;
    $dr     = 0;    
    for ($i = 0; $i < count($row); $i++) {
        $total_recaudo =0;    
        if($valor>0){
            $reg            = 0;
            $id_detalle     = $row[$i][0];
            $valor_ajustad1 = $row[$i][4];
            $cantidad       = $row[$i][5];
            $valor_ajustado = $row[$i][4];
            if($tipo_compania==2){
                $iva            = ($row[$i][1]);
                $impoconsumo    = ($row[$i][2]);
            } else {
                $iva            = ($row[$i][1] * $cantidad);
                $impoconsumo    = ($row[$i][2] * $cantidad);
            }            
            $ajuste         = $row[$i][3];
            $valor_recaud   = $valor_ajustado - ($iva + $impoconsumo + $ajuste);
            $saldo_credito  = 0;
            #Buscar Afectaciones
            $dtp = afectadoDetalleF($id_detalle);
            $valor_rc =0;
            
            if(!empty($dtp)|| $dtp!=NULL){
                #Buscar Valores Recaudo
                $vlr = $con->Listar("SELECT SUM(valor), SUM(iva), SUM(impoconsumo), SUM(ajuste_peso)
                    FROM gp_detalle_pago WHERE id_unico IN ($dtp)");
                $valor_r = $vlr[0][0];
                $iva_r   = $vlr[0][1];
                $impo_r  = $vlr[0][2];
                $ajuste_r= $vlr[0][3];
                # *** Valor Recaudado ***#
                $valor_rc = $valor_r + $iva_r + $impo_r + $ajuste_r;

                $diferencia = $valor_ajustad1 - $valor_rc;
                if($diferencia > 0){
                    $iva            -= $iva_r;
                    $impoconsumo    -= $impo_r;
                    $ajuste         -= $ajuste_r;
                    $valor_ajustado -= $valor_r + $iva + $impoconsumo + $ajuste;
                    $valor_recaud   -= $valor_r;
                    if($saldo_final >= 0){
                        $reg =1;
                        $saldo_credito = $valor_ajustad1- $valor_ajustado;
                        $saldo_final    -= $valor_ajustado;
                    }
                }
            } else {
                $saldo_final -=$valor_ajustad1;
                if(round($saldo_final)>=0 || round($saldo_final)=='-0'){
                   $reg =1;
                   $saldo_credito = $valor_ajustado -($valor_recaud+$iva+$impoconsumo+$ajuste);
                } else {
                    $reg =1;
                    $saldo_credito =0;
                }
            }
            #*** Insertamos Detalle Pago
            if($reg ==1){
            #************ Validamos Total Recaudo Por El Valor **********#
            $total_recaudo += $valor_recaud+$iva+$impoconsumo+$ajuste;
            $crs =0;
            if($valor < $total_recaudo){
                # **** Validamos Iva Primero ***** #
                if($iva <= $valor){
                    $valor -=$iva;
                    if($valor > 0){
                        #*** Validamos Impoconsumo **#
                        if($impoconsumo <= $valor){
                            $valor -=$impoconsumo;
                            if($valor>0){
                                if($ajuste <= $valor){
                                    $valor -=$ajuste;
                                    if($valor > 0){
                                        if($valor_recaud <= $valor){
                                            $valor -=$valor_recaud;
                                        } else {
                                            $valor_recaud = $valor;
                                            $valor -=$valor_recaud;
                                        }
                                    } else {
                                        $valor_recaud = 0;
                                    }
                                } else {
                                    $ajuste       = $valor;
                                    $valor -=$ajuste;
                                    $valor_recaud = 0;
                                }
                            } else {
                                $ajuste       = 0;
                                $valor_recaud = 0;
                            }
                        } else {
                            $impoconsumo  = $valor;
                            $valor -=$impoconsumo;
                            $ajuste       = 0;
                            $valor_recaud = 0;
                        }
                    } else {
                        $impoconsumo  = 0;
                        $ajuste       = 0;
                        $valor_recaud = 0;
                    }
                } else {
                    $iva          = $valor;
                    $valor -=$iva;
                    $impoconsumo  = 0;
                    $ajuste       = 0;
                    $valor_recaud = 0;
                }
            } else {
                $valor -=$total_recaudo;
            }
            $saldo_credito = $valor_ajustad1-$valor_rc-($valor_recaud + $iva + $impoconsumo + $ajuste);
            $sql_cons ="INSERT INTO `gp_detalle_pago`
            ( `detalle_factura`, `valor`,
            `iva`,`impoconsumo`,
            `ajuste_peso`,`saldo_credito`,
            `pago`)
            VALUES (:detalle_factura, :valor,
            :iva, :impoconsumo,
            :ajuste_peso,:saldo_credito,
            :pago)";
            $sql_dato = array(
                array(":detalle_factura",$id_detalle),
                array(":valor",$valor_recaud),
                array(":iva",$iva),
                array(":impoconsumo",$impoconsumo),
                array(":ajuste_peso",$ajuste),
                array(":saldo_credito",$saldo_credito),
                array(":pago",$pago),

            );
            
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                $dr +=1;
            }
        }
        }
    }
    return $dr;

}
function saldoFactura($factura){
    global $con;
    $sld    = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_detalle_factura WHERE factura = $factura");
    $sld    = $sld[0][0];
    $saldo  = 0;
    if(!empty($sld)){
        # Buscar Recaudos
        $rc = $con->Listar("SELECT SUM(dp.valor + dp.iva + dp.impoconsumo + dp.ajuste_peso)
            FROM gp_detalle_pago dp
            LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico
            WHERE df.factura =$factura");
        $rc = $rc[0][0];
        if(!empty($rc)){
            $saldo = $sld-$rc;
        } else {
            $saldo = $sld;
        }
    }
    
    return $saldo;
}
function afectadoDetalleF($id_detalle_f){
    global $con;
    $afec    = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gp_detalle_pago WHERE detalle_factura = $id_detalle_f");
    $afec    = $afec[0][0];
    return $afec;
}

function validarConfiguracion($factura, $fecha_factura, $fecha_pago){
    global $con;
    global $panno;
    #**** Buscar Diferencia Dias Cartera *******#
    $dias	= (strtotime($fecha_factura)-strtotime($fecha_pago))/86400;
    $dias 	= abs($dias);
    $dias       = floor($dias);
    #**** Buscar Configuración Concepto Por Tipo Cartera *******#
    $tipo_c     = carteradia($dias);
    $conc = "0";
    $row = $con->Listar("SELECT DISTINCT concepto_tarifa
            FROM gp_detalle_factura WHERE factura =$factura");
    for ($i = 0; $i < count($row); $i++) {
        $concepto = $row[$i][0];
        # ** Buscar Configuración ** #
        $cf = $con->Listar("SELECT * FROM gp_configuracion_concepto
                WHERE concepto = $concepto AND tipo_cartera = $tipo_c
                AND parametrizacionanno = $panno");
        if(count($cf)>0){
        } else {
            $conc .=$concepto;
        }
    }
    return $conc;

}

function ValorTarifa($ids,$uso,$periodo, $estrato){
    $valor =0;
    $tr = $con->Listar("SELECT t.valor 
        FROM gp_concepto_tarifa ct 
        LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
        WHERE ct.concepto IN ($ids) AND t.uso = '$uso' 
        AND t.estrato = '$estrato' AND t.periodo= '$periodo'");
   
    if(count($tr)>0){
        $valor = $tr[0][0];
    } else {
        $tr = $con->Listar("SELECT t.valor 
        FROM gp_concepto_tarifa ct 
        LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
        WHERE ct.concepto IN ($ids) AND t.uso = '$uso' 
        AND t.periodo= '$periodo'");        
        if(count($tr)>0){
            $valor = $tr[0][0];
        } else {
           $tr = $con->Listar("SELECT t.valor 
            FROM gp_concepto_tarifa ct 
            LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
            WHERE ct.concepto IN ($ids) AND t.uso = '$uso'
            AND t.estrato = '$estrato'"); 
            if(count($tr)>0){
                $valor = $tr[0][0];
            } else {
                $tr = $con->Listar("SELECT t.valor 
                FROM gp_concepto_tarifa ct 
                LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
                WHERE ct.concepto IN ($ids) AND t.uso = '$uso'");
                if(count($tr)>0){
                    $valor = $tr[0][0];
                } else {
                    $tr = $con->Listar("SELECT t.valor 
                    FROM gp_concepto_tarifa ct 
                    LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
                    WHERE ct.concepto IN ($ids) 
                    AND t.estrato = '$estrato' AND t.periodo= '$periodo'");
                    if(count($tr)>0){
                        $valor = $tr[0][0];
                    } else {
                        $tr = $con->Listar("SELECT t.valor 
                        FROM gp_concepto_tarifa ct 
                        LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
                        WHERE ct.concepto IN ($ids) AND t.periodo= '$periodo'");
                        if(count($tr)>0){
                            $valor = $tr[0][0];
                        } else {
                            $tr = $con->Listar("SELECT t.valor 
                            FROM gp_concepto_tarifa ct 
                            LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
                            WHERE ct.concepto IN ($ids) AND t.estrato = '$estrato'");
                            if(count($tr)>0){
                                $valor = $tr[0][0];
                            } else {
                                $tr = $con->Listar("SELECT t.valor 
                                FROM gp_concepto_tarifa ct 
                                LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
                                WHERE ct.concepto IN ($ids)");
                                if(count($tr)>0){
                                    $valor = $tr[0][0];
                                } else {
                                    $valor =0;
                                }
                            }
                        }
                    }
                }
            }
        }                            
    }
    return $valor;
}

function valorRegistro($id_comp, $id_rub_fue)
{

	$queryValor = "SELECT DISTINCT detComP.valor , detComP.id_unico 
	from gf_detalle_comprobante_pptal detComP
	left join gf_comprobante_pptal comP on comP.id_unico = detComP.comprobantepptal
	left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
	where detComP.id_unico = $id_comp
    AND detComP.rubrofuente = $id_rub_fue";

	$valorReg = $GLOBALS['mysqli']->query($queryValor);

	$row = mysqli_fetch_row($valorReg);
	return $row[0];
}

function modificacionRegistro($id,$id_rub_fue, $clase)
{
	
	$apropiacion_def = 0;

    $queryModificacion = "SELECT DISTINCT detComP.valor, tipComP.tipooperacion,detComP.id_unico  
		from gf_detalle_comprobante_pptal detComP
		left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal
		left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
		left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
		left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
		left join gf_concepto_rubro conRub on conRub.rubro = rubP.id_unico
		left join gf_concepto con on con.id_unico = conRub.concepto
		where tipComP.clasepptal = $clase 
		and tipComP.tipooperacion != 1 and detComP.comprobanteafectado = $id 
        and rubFue.id_unico = $id_rub_fue";

	$modificacion = $GLOBALS['mysqli']->query($queryModificacion);
	
	while($row = mysqli_fetch_row($modificacion))
	{
		if(($row[1] == 2) || ($row[1] == 4)|| ($row[1] == 1))
		{
			$apropiacion_def += $row[0];
		}
		elseif($row[1] == 3)
		{
			$apropiacion_def -= $row[0];
		}
	}

	return $apropiacion_def;
}


function afectacionRegistro($id_det_comp, $id_rub_fue, $clase)
{
	
    $afectacion_reg = 0;
    $queryAfectacion = "SELECT DISTINCT  detComP.valor, tipComP.tipooperacion, detComP.id_unico 
		from gf_comprobante_pptal comP
		left join gf_detalle_comprobante_pptal detComP on  comP.id_unico = detComP.comprobantepptal
		left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
		left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
		left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
		left join gf_concepto_rubro conRub on conRub.rubro = rubP.id_unico
		left join gf_concepto con on con.id_unico = conRub.concepto
		where detComP.comprobanteafectado = $id_det_comp 
		and tipComP.clasepptal != $clase 
        and detComP.rubrofuente = $id_rub_fue";

	$afectacion = $GLOBALS['mysqli']->query($queryAfectacion);
	while($row = mysqli_fetch_row($afectacion))
	{
		if(($row[1] == 1))
		{
			$afectacion_reg += $row[0];
		}
		elseif($row[1] == 3)
		{
			$afectacion_reg += $row[0];
		} elseif(($row[1] == 2) || ($row[1] == 4)){
            $afectacion_reg -= $row[0];
        }
	}

	return $afectacion_reg;

}

function afectacionRegistro2($id_det_comp, $id_rub_fue, $clase)
{


    $queryAfectacion = "SELECT DISTINCT detComP.valor, tipComP.tipooperacion, detComP.id_unico 
		from gf_comprobante_pptal comP
		left join gf_detalle_comprobante_pptal detComP on  comP.id_unico = detComP.comprobantepptal
		left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
		left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
		left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
		left join gf_concepto_rubro conRub on conRub.rubro = rubP.id_unico
		left join gf_concepto con on con.id_unico = conRub.concepto
		where detComP.comprobanteafectado = $id_det_comp 
		and tipComP.clasepptal = $clase 
		and tipComP.tipooperacion = 1 
        and detComP.rubrofuente = $id_rub_fue";


$afectacion = $GLOBALS['mysqli']->query($queryAfectacion);
	while($row = mysqli_fetch_row($afectacion))
	{
		if(($row[1] == 2) || ($row[1] == 4)|| ($row[1] == 1))
		{
			$afectacion_reg += $row[0];
		}
		elseif($row[1] == 3)
		{
			$afectacion_reg -= $row[0];
		}
	}

	return $afectacion_reg;

}

function afectacionRegistro3($id_det_comp, $id_rub_fue, $clase)
{

$afectacion_reg=0;
    $queryAfectacion = "SELECT DISTINCT  detComP.valor, tipComP.tipooperacion,detComP.id_unico 
		from gf_comprobante_pptal comP
		left join gf_detalle_comprobante_pptal detComP on  comP.id_unico = detComP.comprobantepptal
		left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
		left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
		left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
		left join gf_concepto_rubro conRub on conRub.rubro = rubP.id_unico
		left join gf_concepto con on con.id_unico = conRub.concepto
		where detComP.comprobanteafectado = $id_det_comp 
		and tipComP.clasepptal != $clase 
		and tipComP.tipooperacion = 1 
        and detComP.rubrofuente = $id_rub_fue";


$afectacion = $GLOBALS['mysqli']->query($queryAfectacion);
	while($row = mysqli_fetch_row($afectacion))
	{
		if(($row[1] == 2) || ($row[1] == 4)|| ($row[1] == 1))
		{
			$afectacion_reg += $row[0];
		}
		elseif($row[1] == 3)
		{
			$afectacion_reg -= $row[0];
		}
	}

	return $afectacion_reg;

}

function apropiacion_mod($id_rubFue)
{

	$apropiacion_def = 0;

	$queryApro = "SELECT   detComP.valor valorDetalleComprobantePPTAL, tipComP.tipooperacion, tipComP.nombre nombreTipoComprobante, rubFue.id_unico IDRubroFuente , rubFue.rubro rubroFuenteRubro, rubP.id_unico IDRubro,  rubP.nombre nombreRubro, con.id_unico IDConcepto, con.nombre nombreConcepto, conRub.id_unico IDConceptoRubro

from gf_detalle_comprobante_pptal detComP

left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal

left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante

left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente

left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro

left join gf_concepto_rubro conRub on conRub.rubro = rubP.id_unico

left join gf_concepto con on con.id_unico = conRub.concepto

where tipComP.clasepptal = 13

and rubFue.id_unico =  $id_rubFue"; 

	$apropia = $GLOBALS['mysqli']->query($queryApro);

	
	while($row = mysqli_fetch_row($apropia))
	{
		if(($row[1] == 2) || ($row[1] == 4) || ($row[1] == 1))
		{
			$apropiacion_def += $row[0];
		}
		elseif($row[1] == 3)
		{
			$apropiacion_def -= $row[0];
		}
	}
	return $apropiacion_def;
}
	

function disponibilidades_mod($id_rubFue, $id_det_comp)
{

	$apropiacion_def = 0;

	$queryApro = "SELECT   detComP.valor valorDetalleComprobantePPTAL, tipComP.tipooperacion, tipComP.nombre nombreTipoComprobante, rubFue.id_unico IDRubroFuente , rubFue.rubro rubroFuenteRubro, rubP.id_unico IDRubro,  rubP.nombre nombreRubro, con.id_unico IDConcepto, con.nombre nombreConcepto, conRub.id_unico IDConceptoRubro

from gf_detalle_comprobante_pptal detComP

left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal

left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante

left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente

left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro

left join gf_concepto_rubro conRub on conRub.rubro = rubP.id_unico

left join gf_concepto con on con.id_unico = conRub.concepto

where tipComP.clasepptal = 14

and rubFue.id_unico =  $id_rubFue

and detComP.id_unico != $id_det_comp"; 

	$apropia = $GLOBALS['mysqli']->query($queryApro);
	
	while($row = mysqli_fetch_row($apropia))
	{
		if(($row[1] == 2) || ($row[1] == 4) || ($row[1] == 1))
		{
			$apropiacion_def += $row[0];
		}
		elseif($row[1] == 3)
		{
			$apropiacion_def -= $row[0];
		}
	}

	return $apropiacion_def;
}
function eliminarPago($pago){
    global $con;
    $cnt    = 0;
    $pptal  = 0;
    $row = $con->Listar("SELECT cn.id_unico as cnt, cp.id_unico as ptal
            FROM gp_pago p
            LEFT JOIN gp_detalle_pago dp ON p.id_unico = dp.pago
            LEFT JOIN gf_detalle_comprobante dc ON dc.id_unico = dp.detallecomprobante
            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico
            LEFT JOIN gf_detalle_comprobante_pptal dpt ON dc.detallecomprobantepptal = dpt.id_unico
            LEFT JOIN gf_comprobante_pptal cp ON dpt.comprobantepptal = cp.id_unico
            WHERE p.id_unico = $pago");
    if(count($row)>0){
        $cnt    = $row[0][0];
        $pptal  = $row[0][1];
    } else {
        #Buscar Por Número Y Tipo
        $row2 = $con->Listar("SELECT cn.id_unico as cnt, cp.id_unico as ptal
            FROM gp_pago p
            LEFT JOIN gp_detalle_pago dp ON p.id_unico = dp.pago
            LEFT JOIN gp_tipo_pago tp ON p.tipo_pago = tp.id_unico
            LEFT JOIN gf_tipo_comprobante tc ON tp.tipo_comprobante = tc.id_unico
            LEFT JOIN gf_comprobante_cnt cn ON cn.tipocomprobante = tc.id_unico AND cn.numero = p.numero_pago
            LEFT JOIN gf_comprobante_pptal cp ON cp.tipocomprobante = tc.comprobante_pptal AND cp.numero = p.numero_pago
            WHERE p.id_unico =$pago");
        if(count($row2)>0){
            $cnt    = $row2[0][0];
            $pptal  = $row2[0][1];
        }

    }
    #*** Buscar Si Tiene Causación
    $cs = causacion($cnt);
    if(!empty($cs[0][0])){
        $id_causacion =$cs;
        $ec = eliminardetallescnt($id_causacion);
    }
    if(!empty($pago)){
        $sql_cons ="DELETE FROM  `gp_detalle_pago`
        WHERE `pago`=:pago ";
        $sql_dato = array(
            array(":pago",$pago)
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        #var_dump($resp);
        if(empty($resp)){
            if(!empty($cnt)){
                $ecn = eliminardetallescnt($cnt);
                $ecc = eliminarDetallesRetencion($cnt);
            }
            if(!empty($pptal)){
                $epp = eliminardetallespptal($pptal);
            }
            
        }
        $upd = $sql_cons ="UPDATE `gp_recaudos_cliente`
            SET `facturas`=:facturas
            WHERE `pago`=:pago ";
            $sql_dato = array(
                array(":facturas",NULL),
                array(":pago",$pago)
            );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
    }
    return true ;
}	

function validarConfiguracionDistribucion($id_pptal){
    global $con;
    #Validar Que todos los conceptos estén configurados
    $rowd = $con->Listar("SELECT dc.id_unico, c.id_unico, c.nombre 
        FROM gf_detalle_comprobante_pptal dc 
        LEFT JOIN gf_concepto_rubro cr ON cr.id_unico = dc.conceptoRubro 
        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
        WHERE dc.comprobantepptal =$id_pptal");
    $html   = "Conceptos Sin Configurar<br/>";
    $c      = 0;
    for ($i = 0; $i < count($rowd); $i++) {
        $concepto = $rowd[$i][1];
        $config = configuracionDistribucion($concepto);
        if($config==true){
            
        } else{
            $html .=$rowd[$i][2]."<br/>";
            $c++;
        }
    }
    $datos = array("html"=>$html, "rta"=>$c);
    return $datos;
}

function configuracionDistribucion($concepto){
    global $con;
    $row = $con->Listar("SELECT * FROM gf_configuracion_distribucion where concepto = $concepto");
    if(!empty($row[0][0])){
        return true;
    } else {
        return false;
    }
}

function guardarDistribucionCostos($id_pptal, $id_cnt){
    global $con;
    #Validar Que todos los conceptos estén configurados
    $rowd = $con->Listar("SELECT dc.id_unico, c.id_unico, c.nombre, dc.valor 
        FROM gf_detalle_comprobante_pptal dc 
        LEFT JOIN gf_concepto_rubro cr ON cr.id_unico = dc.conceptoRubro 
        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
        WHERE dc.comprobantepptal =$id_pptal");
    $distribucion = array();
    $cd  = 0;
    for ($i = 0; $i < count($rowd); $i++) {
        $id_dp    = $rowd[$i][0];
        $concepto = $rowd[$i][1];
        $valor    = $rowd[$i][3];
        $row = $con->Listar("SELECT SUM(porcentaje) 
            FROM gf_configuracion_distribucion 
            WHERE concepto = $concepto");
        if($row[0][0]==100){
            #*** Generar Distribución por porcentaje
            $cant = distribucionporcentaje($id_pptal,$id_cnt,$id_dp,$concepto, $valor);
        } else {
            #*** Generar Distribución por cantidad
            $cant = distribucioncantidad($id_pptal, $id_cnt,$id_dp,$concepto, $valor);
        }
    }
    echo $cant ;
}

function distribucionporcentaje($idpptal,$id_cnt, $id_dp,$concepto, $valor){
    global  $con;
    $rta  = 0;
    #* Datos Detalle *#
    $dd = $con->Listar("SELECT cn.fecha, 
        cn.tercero, cn.descripcion, 
        dc.proyecto, 
        dc.concepto
        FROM gf_detalle_comprobante_pptal dc
        LEFT JOIN gf_comprobante_pptal cn ON dc.comprobantepptal = cn.id_unico
        WHERE  dc.id_unico = $id_dp");
    $fecha      = $dd[0][0];
    $descrip    = $dd[0][2];
    $idda = $con->Listar("SELECT dc.id_unico, dc.tercero, dc.proyecto 
        FROM gf_detalle_comprobante  dc
        LEFT JOIN gf_detalle_comprobante_pptal dcp ON dc.detallecomprobantepptal = dcp.id_unico 
        LEFT JOIN gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico 
        LEFT JOIN gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico 
        LEFT JOIN gf_cuenta cta ON dc.cuenta= cta.id_unico 
        WHERE dc.detallecomprobantepptal =$id_dp  
        AND (IF(cta.naturaleza =1, dc.valor>0, IF(cta.naturaleza =2,dc.valor<0, dc.valor>0 ))) AND dc.distribucion is null 
            AND tc.clasecontable = 13");
    $id_dcnta    = $idda[0][0];
    #** Actualizar detalle relacionado al detallepptal
    if(empty($id_dcnta)){
        $id_dcnta =0;
    }
    $act = actualizardetalledistribuir($id_dcnta);
    
    #id_detalle_cnt a actualizar
    $idd = $con->Listar("SELECT dc.id_unico, dc.tercero, dc.proyecto FROM gf_detalle_comprobante  dc
        LEFT JOIN gf_detalle_comprobante_pptal dcp ON dc.detallecomprobantepptal = dcp.id_unico 
        LEFT JOIN gf_cuenta cta ON dc.cuenta= cta.id_unico 
        WHERE dc.detallecomprobantepptal =$id_dp  ");
    $id_dcnt    = $idd[0][0];
    $tercero    = $idd[0][1];
    $proyecto   = $idd[0][2];

    $rowc = $con->Listar("SELECT cf.centro_costo, cf.cuenta, 
            cf.porcentaje, c.naturaleza 
        FROM gf_configuracion_distribucion cf 
        LEFT JOIN gf_cuenta c On cf.cuenta = c.id_unico 
        WHERE concepto = $concepto");
    $vt =0;
    for ($c = 0; $c < count($rowc); $c++) {
        $c_costo    = $rowc[$c][0];
        $cuenta     = $rowc[$c][1];
        $naturaleza = $rowc[$c][3];
        $porcentajeaplicar   = $rowc[$c][2];
        $valor_a = ROUND(($valor *$porcentajeaplicar/100),0);
        $valor_r = $valor_a;
        $vt +=$valor_r;
        if($c==count($rowc)-1){
            if($valor>$vt){
                $valor_a = $valor_a+($valor-$vt);
            }elseif($valor<$vt){
                $valor_a = $valor_a-($vt-$valor);
            }
        }
        if($naturaleza ==2){
            $valor_a = $valor_a*-1;
        }
        if(empty($act)){
            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                    ( `fecha`, `comprobante`,`valor`,
                    `cuenta`,`naturaleza`,`tercero`, 
                    `centrocosto`,`detallecomprobantepptal`, 
                    `descripcion`, `proyecto`) 
            VALUES (:fecha,  :comprobante,:valor, 
                    :cuenta,:naturaleza, :tercero, 
                    :centrocosto,:detallecomprobantepptal, 
                    :descripcion,:proyecto)";
            $sql_dato = array(
                    array(":fecha",$fecha),
                    array(":comprobante",$id_cnt),
                    array(":valor",$valor_a),
                    array(":cuenta",$cuenta),   
                    array(":naturaleza",$naturaleza),
                    array(":tercero",$tercero),
                    array(":centrocosto",$c_costo),
                    array(":detallecomprobantepptal",$id_dp),
                    array(":descripcion",$descrip),
                    array(":proyecto",$proyecto),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                $sql_cons ="INSERT INTO `gf_distribucion_costos` 
                        ( `cnt`, `pptal`,`concepto`,
                        `centro_costo`,`cuenta`,`valor`) 
                VALUES (:cnt,  :pptal,:concepto, 
                        :centro_costo,:cuenta, :valor)";
                $sql_dato = array(
                        array(":cnt",$id_cnt),
                        array(":pptal",$idpptal),
                        array(":concepto",$concepto),
                        array(":centro_costo",$c_costo),   
                        array(":cuenta",$cuenta),
                        array(":valor",$valor_r),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                $rta +=1;
            }
        }        
    }
    return $rta;
}

function distribucioncantidad($idpptal, $id_cnt,$id_dp,$concepto, $valor){
    global $con;
    $rta  = 0;
    #* Datos Detalle *#
    $dd = $con->Listar("SELECT cn.fecha, 
        cn.tercero, cn.descripcion, 
        dc.proyecto, 
        dc.concepto
        FROM gf_detalle_comprobante_pptal dc
        LEFT JOIN gf_comprobante_pptal cn ON dc.comprobantepptal = cn.id_unico
        WHERE  dc.id_unico = $id_dp");
    $fecha      = $dd[0][0];
    $descrip    = $dd[0][2];
    
    #*****************************************#
    $idda = $con->Listar("SELECT dc.id_unico, dc.tercero, dc.proyecto 
        FROM gf_detalle_comprobante  dc
        LEFT JOIN gf_detalle_comprobante_pptal dcp ON dc.detallecomprobantepptal = dcp.id_unico 
        LEFT JOIN gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico 
        LEFT JOIN gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico 
        LEFT JOIN gf_cuenta cta ON dc.cuenta= cta.id_unico 
        WHERE dc.detallecomprobantepptal =$id_dp  
        AND (IF(cta.naturaleza =1, dc.valor>0, IF(cta.naturaleza =2,dc.valor<0, dc.valor>0 ))) AND dc.distribucion is null 
            AND tc.clasecontable = 13");
    $id_dcnta    = $idda[0][0];
    #** Actualizar detalle relacionado al detallepptal
    if(empty($id_dcnta)){
        $id_dcnta =0;
    }
    $act = actualizardetalledistribuir($id_dcnta);
    
    #id_detalle_cnt a actualizar
    $idd = $con->Listar("SELECT dc.id_unico, dc.tercero, dc.proyecto FROM gf_detalle_comprobante  dc
        LEFT JOIN gf_detalle_comprobante_pptal dcp ON dc.detallecomprobantepptal = dcp.id_unico 
        LEFT JOIN gf_cuenta cta ON dc.cuenta= cta.id_unico 
        WHERE dc.detallecomprobantepptal =$id_dp  ");
    $id_dcnt    = $idd[0][0];
    $tercero    = $idd[0][1];
    $proyecto   = $idd[0][2];
    #** Definir la cantidad total a distribuir
    $cd = $con->Listar("SELECT SUM(cc.cantidad_distribucion) 
        FROM gf_configuracion_distribucion cf 
        LEFT JOIN gf_centro_costo cc ON cf.centro_costo =cc.id_unico  
        WHERE concepto = $concepto");
    $cd = $cd[0][0];
    
    $rowc = $con->Listar("SELECT cf.centro_costo, cf.cuenta, 
            cc.cantidad_distribucion, c.naturaleza 
        FROM gf_configuracion_distribucion cf 
        LEFT JOIN gf_centro_costo cc ON cf.centro_costo =cc.id_unico 
        LEFT JOIN gf_cuenta c On cf.cuenta = c.id_unico 
        WHERE concepto = $concepto");
    $tp = 0;
    $vt = 0;
    for ($c = 0; $c < count($rowc); $c++) {
        $c_costo    = $rowc[$c][0];
        $cuenta     = $rowc[$c][1];
        $cantidad   = $rowc[$c][2];
        $naturaleza = $rowc[$c][3];
        $porcentajeaplicar = ROUND(($cantidad *100/$cd),1);
        $tp += $porcentajeaplicar;
        #** Validar Porcentaje 
        if($c==(count($rowc)-1)){
            if($tp!=100){
                $pf = 100-$tp;
                $porcentajeaplicar = $porcentajeaplicar+$pf;
            }
        }
        $valor_a = ($valor*$porcentajeaplicar)/100;
        $valor_a = ROUND($valor_a);
        $valor_r = $valor_a;
        $vt +=$valor_r;
        if($c==count($rowc)-1){
            if($valor>$vt){
                $valor_a = $valor_a+($valor-$vt);
            }elseif($valor<$vt){
                $valor_a = $valor_a-($vt-$valor);
            }
        }
        if($naturaleza ==2){
            $valor_a = $valor_a*-1;
        }
        if(empty($act)){
            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                    ( `fecha`, `comprobante`,`valor`,
                    `cuenta`,`naturaleza`,`tercero`, 
                    `centrocosto`,`detallecomprobantepptal`, 
                    `descripcion`, `proyecto`,`distribucion`) 
            VALUES (:fecha,  :comprobante,:valor, 
                    :cuenta,:naturaleza, :tercero, 
                    :centrocosto,:detallecomprobantepptal, 
                    :descripcion,:proyecto,:distribucion)";
            $sql_dato = array(
                    array(":fecha",$fecha),
                    array(":comprobante",$id_cnt),
                    array(":valor",$valor_a),
                    array(":cuenta",$cuenta),   
                    array(":naturaleza",$naturaleza),
                    array(":tercero",$tercero),
                    array(":centrocosto",$c_costo),
                    array(":detallecomprobantepptal",$id_dp),
                    array(":descripcion",$descrip),
                    array(":proyecto",$proyecto),
                    array(":distribucion",1),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                $sql_cons ="INSERT INTO `gf_distribucion_costos` 
                        ( `cnt`, `pptal`,`concepto`,
                        `centro_costo`,`cuenta`,`valor`) 
                VALUES (:cnt,  :pptal,:concepto, 
                        :centro_costo,:cuenta, :valor)";
                $sql_dato = array(
                        array(":cnt",$id_cnt),
                        array(":pptal",$idpptal),
                        array(":concepto",$concepto),
                        array(":centro_costo",$c_costo),   
                        array(":cuenta",$cuenta),
                        array(":valor",$valor_r),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                $rta +=1;
            }
        }        
    }
    return $rta;
}

function actualizardetalledistribuir($id_dt){
    global $con;
    $sql_cons ="UPDATE `gf_detalle_comprobante` 
        SET valor =0 
        WHERE `id_unico` =:id_unico ";
    $sql_dato = array(
            array(":id_unico",$id_dt),
    );
    $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
    return $obj_resp;
    
}
function guardarDistribucionCostospc($id_pptal, $id_cnt, $concepto, $cc,$cta, $naturaleza, $porcentaje,$id_det){
    global $con;
    $rta  = 0;
    #* Datos Detalle *#
    $dd = $con->Listar("SELECT cn.fecha, 
        cn.tercero, cn.descripcion, 
        dc.proyecto, 
        cr.concepto, 
        dc.id_unico, dc.valor
        FROM gf_detalle_comprobante_pptal dc
        LEFT JOIN gf_comprobante_pptal cn ON dc.comprobantepptal = cn.id_unico
        LEFT JOIN gf_concepto_rubro cr ON dc.conceptoRubro = cr.id_unico 
        WHERE  dc.id_unico = $id_det AND cr.concepto = $concepto");
    for ($i = 0; $i < count($dd); $i++) {
        $fecha      = $dd[$i][0];
        $descrip    = $dd[$i][2];
        $id_dp      = $dd[$i][5];
        $valor      = $dd[$i][6];
        $idda = $con->Listar("SELECT dc.id_unico, dc.tercero, dc.proyecto 
            FROM gf_detalle_comprobante  dc
            LEFT JOIN gf_detalle_comprobante_pptal dcp ON dc.detallecomprobantepptal = dcp.id_unico 
            LEFT JOIN gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico 
            LEFT JOIN gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico 
            LEFT JOIN gf_cuenta cta ON dc.cuenta= cta.id_unico 
            WHERE dc.detallecomprobantepptal =$id_dp  
            AND (IF(cta.naturaleza =1, dc.valor>0, IF(cta.naturaleza =2,dc.valor<0, dc.valor>0 ))) AND dc.distribucion is null 
                AND tc.clasecontable = 13");
        $id_dcnta    = $idda[0][0];
        #** Actualizar detalle relacionado al detallepptal
        if(empty($id_dcnta)){
            $id_dcnta =0;
        }
        $act = actualizardetalledistribuir($id_dcnta);
        
        #id_detalle_cnt a actualizar
        $idd = $con->Listar("SELECT dc.id_unico, dc.tercero, dc.proyecto FROM gf_detalle_comprobante  dc
            LEFT JOIN gf_detalle_comprobante_pptal dcp ON dc.detallecomprobantepptal = dcp.id_unico 
            LEFT JOIN gf_cuenta cta ON dc.cuenta= cta.id_unico 
            WHERE dc.detallecomprobantepptal =$id_dp  ");
        
        $id_dcnt    = $idd[0][0];
        $tercero    = $idd[0][1];
        $proyecto   = $idd[0][2];
        
        $c_costo    = $cc;
        $cuenta     = $cta;
        $naturaleza = $naturaleza;
        $valor_a = ($valor*$porcentaje)/100;
        $valor_r = round($valor_a);
        if($naturaleza ==2){
            $valor_a = $valor_a*-1;
        }
        
        if(empty($act)){
            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                    ( `fecha`, `comprobante`,`valor`,
                    `cuenta`,`naturaleza`,`tercero`, 
                    `centrocosto`,`detallecomprobantepptal`, 
                    `descripcion`, `proyecto`, `distribucion`) 
            VALUES (:fecha,  :comprobante,:valor, 
                    :cuenta,:naturaleza, :tercero, 
                    :centrocosto,:detallecomprobantepptal, 
                    :descripcion,:proyecto, :distribucion)";
            $sql_dato = array(
                    array(":fecha",$fecha),
                    array(":comprobante",$id_cnt),
                    array(":valor",$valor_a),
                    array(":cuenta",$cuenta),   
                    array(":naturaleza",$naturaleza),
                    array(":tercero",$tercero),
                    array(":centrocosto",$c_costo),
                    array(":detallecomprobantepptal",$id_dp),
                    array(":descripcion",$descrip),
                    array(":proyecto",$proyecto),
                    array(":distribucion",1),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                $sql_cons ="INSERT INTO `gf_distribucion_costos` 
                        ( `cnt`, `pptal`,`concepto`,
                        `centro_costo`,`cuenta`,`valor`) 
                VALUES (:cnt,  :pptal,:concepto, 
                        :centro_costo,:cuenta, :valor)";
                $sql_dato = array(
                        array(":cnt",$id_cnt),
                        array(":pptal",$id_pptal),
                        array(":concepto",$concepto),
                        array(":centro_costo",$c_costo),   
                        array(":cuenta",$cuenta),
                        array(":valor",$valor_r),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                $rta +=1;
            }    
        }
    }
    return $rta;    
    
}
function totalmovimientocc($idc, $idcc, $nm,$nanno, $parmanno, $nc){
    global  $con;
    $valor  = 0;
    $diaF   = diaf($nm,$nanno);
    $fechaI = $nanno.'-'.$nm.'-01';
    $fechaF = $nanno.'-'.$nm.'-'.$diaF;
    #DEBITOS
    $deb = $con->Listar("SELECT SUM(valor)
           FROM
             gf_detalle_comprobante dc
           LEFT JOIN
             gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
           LEFT JOIN
             gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
           LEFT JOIN
             gf_clase_contable cc ON tc.clasecontable = cc.id_unico
           WHERE valor>0 AND 
            cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND cc.id_unico != '5' AND cc.id_unico !='20'  
            AND dc.cuenta = '$idc' AND dc.centrocosto = $idcc 
            AND cp.parametrizacionanno =$parmanno ");
    if(!empty($deb[0][0])){
        $debito = $deb[0][0];
    } else {
        $debito  = 0;
    }
   #CREDITOS
    $cr = $con->Listar("SELECT SUM(valor)
           FROM
             gf_detalle_comprobante dc
           LEFT JOIN
             gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
           LEFT JOIN
             gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
           LEFT JOIN
             gf_clase_contable cc ON tc.clasecontable = cc.id_unico
           WHERE valor<0 AND 
            cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND cc.id_unico != '5' AND cc.id_unico !='20'  
            AND dc.cuenta = '$idc' AND dc.centrocosto = $idcc 
            AND cp.parametrizacionanno =$parmanno ");
    if(!empty($cr[0][0])){
        $credito = $cr[0][0];
    } else {
        $credito=0;
    }
    $valor = $debito -($credito*-1);
    return $valor;
    
}

function diaf($nm,$nanno){
    $calendario = CAL_GREGORIAN;
    $diaF = cal_days_in_month($calendario, $nm, $nanno); 
    return $diaF;
}

function generarB($anno, $id_par, $fechaI, $fechaF){
    global $con;
    #VACIAR LA TABLA TEMPORAL
    $vaciarTabla = $con->Listar('TRUNCATE temporal_consolidado');
    #CONSULTA CUENTAS 
    $rowc = $con->Listar("SELECT DISTINCT
                c.id_unico, 
                c.codi_cuenta,
                c.nombre,
                c.naturaleza,
                ch.codi_cuenta 
              FROM
                gf_cuenta c
              LEFT JOIN
                gf_cuenta ch ON c.predecesor = ch.id_unico
              WHERE  
                c.parametrizacionanno = $id_par  
                    AND length(c.codi_cuenta)  = 6 
              ORDER BY 
                c.codi_cuenta DESC ");
    for ($i=0; $i <count($rowc) ; $i++) {  
        #GUARDA LOS DATOS EN LA TABLA TEMPORAL
        $sql_cons ="INSERT INTO `temporal_consolidado` 
                    ( `id_cuenta`, `numero_cuenta`,`nombre`,
                    `cod_predecesor`,`naturaleza`) 
            VALUES (:id_cuenta, :numero_cuenta, :nombre, 
                    :cod_predecesor,:naturaleza)";
            $sql_dato = array(
                    array(":id_cuenta",$rowc[$i][0]),
                    array(":numero_cuenta",$rowc[$i][1]),
                    array(":nombre",$rowc[$i][2]),
                    array(":cod_predecesor",$rowc[$i][4]),   
                    array(":naturaleza",$rowc[$i][3]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
    }
    $rowc = $con->Listar("SELECT numero_cuenta, naturaleza, id_cuenta FROM temporal_consolidado");
    for ($i = 0; $i < count($rowc); $i++) {
        $cod_cuenta = $rowc[$i][0];
        $id_cuenta  = $rowc[$i][2];
        $nt_cuenta  = $rowc[$i][1];
        #* Debitos 
        $db =$con->Listar("SELECT SUM(dc.valor) 
                FROM 
                    gf_detalle_comprobante dc 
                LEFT JOIN 
                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN
                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                LEFT JOIN
                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                LEFT JOIN 
                    gf_cuenta cta ON dc.cuenta = cta.id_unico 
                WHERE dc.valor>0 AND cta.codi_cuenta LIKE '$cod_cuenta%' 
                    AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                    AND cn.parametrizacionanno =$id_par 
                    AND cc.id_unico != '5' AND cc.id_unico !='20' ");
        if(!empty($db[0][0])){
            $saldoNuevod = $db[0][0];
                         
            $sql_cons ="UPDATE `temporal_consolidado` 
                SET 
                `nuevo_saldo_debito`=:nuevo_saldo_debito 
                WHERE `id_cuenta` =:id_cuenta ";
            $sql_dato = array(
                array(":nuevo_saldo_debito",$saldoNuevod),
                array(":id_cuenta",$id_cuenta),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        }
        #* Creditos 
        $cr =$con->Listar("SELECT SUM(dc.valor) 
                FROM 
                    gf_detalle_comprobante dc 
                LEFT JOIN 
                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN
                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                LEFT JOIN
                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                LEFT JOIN 
                    gf_cuenta cta ON dc.cuenta = cta.id_unico 
                WHERE dc.valor<0 AND cta.codi_cuenta LIKE '$cod_cuenta%' 
                    AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                    AND cn.parametrizacionanno =$id_par 
                    AND cc.id_unico != '5' AND cc.id_unico !='20' ");
        if(!empty($cr[0][0])){
            $saldoNuevoc = $cr[0][0];
            $sql_cons ="UPDATE `temporal_consolidado` 
                SET 
                `nuevo_saldo_credito`=:nuevo_saldo_credito
                WHERE `id_cuenta` =:id_cuenta ";
            $sql_dato = array(
                array(":nuevo_saldo_credito",$saldoNuevoc),
                array(":id_cuenta",$id_cuenta),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        }
        
    }
}

function guardarConsolidado($comp, $tipoc, $fechaF, $param, $compania){ 
    global $con;
    $totalc = $con->Listar("SELECT count(*) 
        FROM temporal_consolidado   
        WHERE length(numero_cuenta)='6' 
        AND  (nuevo_saldo_debito !=0 OR nuevo_saldo_credito !=0 )
        ORDER BY numero_cuenta DESC ");
    $i = 0;
    $v = 500;
    $valorreg = $totalc[0][0];
    #* cREAR COMPROBANTE 
    $bs = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE tercero = $comp AND tipocomprobante = $tipoc AND fecha = '$fechaF'");
    if(empty($bs[0][0])){
        $numero = numero ('gf_comprobante_cnt', $tipoc, $param);
        $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                ( `numero`, `fecha`, 
                `descripcion`, 
                `parametrizacionanno`,`tipocomprobante`,
                `tercero`,
                `compania`,`estado`) 
        VALUES (:numero, :fecha, 
                :descripcion,
                :parametrizacionanno,:tipocomprobante,
                :tercero, 
                :compania, :estado )";
        $sql_dato = array(
                array(":numero",$numero),
                array(":fecha",$fechaF),
                array(":descripcion",'Comprobante de consolidación'),
                array(":parametrizacionanno",$param),
                array(":tipocomprobante",$tipoc),
                array(":tercero",$comp),
                array(":compania",$compania),
                array(":estado",2),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        $bs = $con->Listar("SELECT * FROM gf_comprobante_cnt  
            WHERE numero = $numero AND tipocomprobante =$tipoc ");
        $id_comprobante = $bs[0][0];
        

    } else {
        #eliminar detalles
        $id_comprobante = $bs[0][0];
        eliminardetallescnt($bs[0][0]);

    }
    while($valorreg > 0){
        $rowa1 = $con->Listar("SELECT DISTINCT id_cuenta,numero_cuenta, nuevo_saldo_debito,nuevo_saldo_credito , naturaleza 
                FROM temporal_consolidado    
                WHERE length(numero_cuenta)='6' 
                AND  (nuevo_saldo_debito !=0 OR nuevo_saldo_credito !=0 ) 
                ORDER BY numero_cuenta ASC 
                LIMIT $i ,$v 
                ");

        for ($a=0; $a < count($rowa1); $a++) { 
            $cod_cuenta = $rowa1[$a][1];
            $cc = $con->Listar("SELECT id_unico, naturaleza FROM gf_cuenta WHERE codi_cuenta = $cod_cuenta AND parametrizacionanno = $param");
            $id_cuenta  = $cc[0][0];
            $naturaleza = $cc[0][1];
            
            $valor1      = $rowa1[$a][2];
            $valor2      = $rowa1[$a][3];
            if(!empty($valor1)){
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero)";
                $sql_dato = array(
                        array(":fecha",$fechaF),
                        array(":comprobante",$id_comprobante),
                        array(":valor",($valor1)),
                        array(":cuenta",$id_cuenta),   
                        array(":naturaleza",$naturaleza),
                        array(":tercero",$comp),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            if(!empty($valor2)){
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero)";
                $sql_dato = array(
                        array(":fecha",$fechaF),
                        array(":comprobante",$id_comprobante),
                        array(":valor",($valor2)),
                        array(":cuenta",$id_cuenta),   
                        array(":naturaleza",$naturaleza),
                        array(":tercero",$comp),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
        } 
        $i =$v+1;
        $v +=500; 
        $valorreg -=500; 
    }
}
function generarBalanceGeneral($anno, $id_par, $fechaI, $fechaF){
    global $con;
    #VACIAR LA TABLA TEMPORAL
    $create  = $con->Listar("CREATE TABLE IF NOT EXISTS temporal_balance$id_par (
        `id_unico` int(11) NOT NULL,
        `id_cuenta` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
        `numero_cuenta` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
        `nombre` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
        `cod_predecesor` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
        `saldo_inicial` double(20,2) DEFAULT NULL,
        `debito` double(20,2) DEFAULT NULL,
        `credito` double(20,2) DEFAULT NULL,
        `nuevo_saldo` double(20,2) DEFAULT NULL,
        `naturaleza` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
        `saldo_inicial_debito` double(20,2) DEFAULT NULL,
        `saldo_inicial_credito` double(20,2) DEFAULT NULL,
        `nuevo_saldo_debito` double(20,2) DEFAULT NULL,
        `nuevo_saldo_credito` double(20,2) DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
      ALTER TABLE temporal_balance$id_par 
        ADD PRIMARY KEY IF NOT EXISTS(`id_unico`);
      ALTER TABLE temporal_balance$id_par 
        MODIFY `id_unico` int(11) NOT NULL AUTO_INCREMENT;");
    $vaciarTabla = $con->Listar('TRUNCATE temporal_balance'.$id_par);
    #CONSULTA CUENTAS 
    $rowc = $con->Listar("SELECT DISTINCT
                c.id_unico, 
                c.codi_cuenta,
                c.nombre,
                c.naturaleza,
                ch.codi_cuenta 
              FROM
                gf_cuenta c
              LEFT JOIN
                gf_cuenta ch ON c.predecesor = ch.id_unico
              WHERE  c.parametrizacionanno = $id_par   
              ORDER BY 
                c.codi_cuenta DESC ");
    for ($i=0; $i <count($rowc) ; $i++) {  
        #GUARDA LOS DATOS EN LA TABLA TEMPORAL
        $sql_cons ="INSERT INTO `temporal_balance$id_par` 
                    ( `id_cuenta`, `numero_cuenta`,`nombre`,
                    `cod_predecesor`,`naturaleza`) 
            VALUES (:id_cuenta, :numero_cuenta, :nombre, 
                    :cod_predecesor,:naturaleza)";
            $sql_dato = array(
                    array(":id_cuenta",$rowc[$i][0]),
                    array(":numero_cuenta",$rowc[$i][1]),
                    array(":nombre",$rowc[$i][2]),
                    array(":cod_predecesor",$rowc[$i][4]),   
                    array(":naturaleza",$rowc[$i][3]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
    }
    //CONSULTO LAS CUENTAS QUE TENGAN MOVIMIENTO
    
    $mov = $con->Listar("SELECT DISTINCT c.id_unico, c.codi_cuenta, 
            c.nombre, c.naturaleza FROM gf_detalle_comprobante dc 
            LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
            WHERE  c.parametrizacionanno = $id_par 
            ORDER BY c.codi_cuenta DESC");
    $totaldeb   = 0;
    $totalcred  = 0;
    $totalsaldoI = 0;
    $totalsaldoF = 0;

    for ($m=0; $m < count($mov) ; $m++) { 
        #SI FECHA INICIAL =01 DE ENERO
        $fechaPrimera = $anno . '-01-01';
        if ($fechaI == $fechaPrimera) {
            #CONSULTA EL SALDO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
            $fechaMax = $anno . '-12-31';
            $com = $con->Listar("SELECT SUM(valor)
                        FROM
                          gf_detalle_comprobante dc
                        LEFT JOIN
                          gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                        LEFT JOIN
                          gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                        LEFT JOIN
                          gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                        WHERE
                          cp.fecha BETWEEN '$fechaI' AND '$fechaMax' 
                          AND cc.id_unico = '5' 
                          AND dc.cuenta = '".$mov[$m][0]."' AND cp.parametrizacionanno =$id_par");
            if (!empty($com[0][0])) {
                $saldo = $com[0][0];
            } else {
                $saldo = 0;
            }

            #DEBITOS
            $deb = $con->Listar("SELECT SUM(valor)
                        FROM
                          gf_detalle_comprobante dc
                        LEFT JOIN
                          gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                        LEFT JOIN
                          gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                        LEFT JOIN
                          gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                        WHERE valor>0 AND 
                          cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
                          AND cc.id_unico != '5' AND cc.id_unico != '20'  
                          AND dc.cuenta = '".$mov[$m][0]."' AND cp.parametrizacionanno =$id_par");
            if (!empty($deb[0][0])) {
                $debito = $deb[0][0];
            } else {
                $debito = 0;
            }

            #CREDITOS
            $cr = $con->Listar("SELECT SUM(valor)
                        FROM
                          gf_detalle_comprobante dc
                        LEFT JOIN
                          gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                        LEFT JOIN
                          gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                        LEFT JOIN
                          gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                        WHERE valor<0 AND 
                          cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
                          AND cc.id_unico != '5' AND cc.id_unico != '20' 
                          AND dc.cuenta = '".$mov[$m][0]."' AND cp.parametrizacionanno =$id_par");
            if (!empty($cr[0][0])) {
                $credito =$cr[0][0];
            } else {
                $credito = 0;
            }

    #SI FECHA INICIAL !=01 DE ENERO
        } else {
            #TRAE EL SALDO INICIAL
            $sInicial = $con->Listar("SELECT SUM(dc.valor) 
                    FROM 
                        gf_detalle_comprobante dc 
                    LEFT JOIN 
                        gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                    LEFT JOIN
                        gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                    LEFT JOIN
                        gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                    WHERE dc.cuenta='".$mov[$m][0]."' 
                    AND cn.fecha >='$fechaPrimera' AND cn.fecha <'$fechaI' 
                    AND cn.parametrizacionanno =$id_par AND cc.id_unico !='20'");
            if (!empty($sInicial[0][0])) {
                $saldo =$sInicial[0][0];
            } else {
                $saldo = 0;
            }
            #DEBITOS
            $deb = $con->Listar("SELECT SUM(dc.valor) 
                    FROM 
                        gf_detalle_comprobante dc 
                    LEFT JOIN 
                        gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                    LEFT JOIN
                        gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                    LEFT JOIN
                        gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                    WHERE dc.valor>0 AND dc.cuenta='".$mov[$m][0]."' AND 
                        cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                        AND cn.parametrizacionanno =$id_par AND cc.id_unico !='20'");
            if (!empty($deb[0][0])) {
                $debito = $deb[0][0];
            } else {
                $debito = 0;
            }
            #CREDITOS
            $cr = $con->Listar("SELECT SUM(dc.valor) 
                    FROM 
                        gf_detalle_comprobante dc 
                    LEFT JOIN 
                        gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                    LEFT JOIN
                        gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                    LEFT JOIN
                        gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                    WHERE dc.valor<0 AND dc.cuenta='".$mov[$m][0]."' AND 
                    cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                    AND cn.parametrizacionanno =$id_par AND cc.id_unico !='20'");
            if (!empty($cr[0][0])) {
                $credito =$cr[0][0];
            } else {
                $credito = 0;
            }
        }
        #SI LA NATURALEZA ES DEBITO
        if ($mov[$m][3] == '1') {
            if ($credito < 0) {
                $credito = (float) substr($credito, '1');
            }
            $saldoNuevo = $saldo + $debito - $credito;
            #SI LA NATURALEZA ES CREDITO
        } else {
            if ($credito < 0) {
                $credito = (float) substr($credito, '1');
            }
            $saldoNuevo = $saldo - $credito + $debito;
        }

        $sql_cons ="UPDATE `temporal_balance$id_par` 
            SET `saldo_inicial` =:saldo_inicial , 
            `debito`=:debito, 
            `credito`=:credito, 
            `nuevo_saldo`=:nuevo_saldo 
            WHERE `id_cuenta` =:id_cuenta ";
        $sql_dato = array(
            array(":saldo_inicial",$saldo),
            array(":debito",$debito),
            array(":credito",$credito),
            array(":nuevo_saldo",$saldoNuevo),
            array(":id_cuenta",$mov[$m][0]),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
    }
    $totalc = $con->Listar("SELECT count(*) 
        FROM temporal_balance$id_par   
        ORDER BY numero_cuenta DESC ");
    $i = 0;
    $v = 500;
    $valorreg = $totalc[0][0];
    while($valorreg > 0){
        #CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
        $rowa1 = $con->Listar("SELECT id_cuenta,numero_cuenta, cod_predecesor, (saldo_inicial), 
                (debito), (credito), (nuevo_saldo) 
                FROM temporal_balance$id_par 
                ORDER BY numero_cuenta DESC 
                LIMIT $i, $v ");
        
        for ($a=0; $a < count($rowa1); $a++) { 
            $rowa = $con->Listar("SELECT id_cuenta,numero_cuenta, cod_predecesor, (saldo_inicial), (debito), 
                (credito), (nuevo_saldo) 
                FROM temporal_balance$id_par 
                WHERE id_cuenta ='".$rowa1[$a][0]."'
                ORDER BY numero_cuenta DESC ");
            for ($a2=0; $a2 < count($rowa); $a2++) { 
                if(!empty($rowa[$a2][3]) && !empty($rowa[$a2][4]) && !empty($rowa[$a2][5])&& !empty($rowa[$a2][6])) {
                    if (!empty($rowa[$a2][2])) {
                        $va = $con->Listar("SELECT numero_cuenta,(saldo_inicial), 
                            (debito), (credito), (nuevo_saldo) 
                            FROM temporal_balance$id_par WHERE numero_cuenta ='".$rowa[$a2][2]."'");

                        $saldoIn  = ($rowa[$a2][3]+$va[0][1]);
                        $debitoN  = ($rowa[$a2][4]+$va[0][2]);
                        $creditoN = ($rowa[$a2][5]+$va[0][3]);
                        $nuevoN   = ($rowa[$a2][6]+$va[0][4]);
                        $sql_cons ="UPDATE `temporal_balance$id_par` 
                            SET `saldo_inicial` =:saldo_inicial , 
                            `debito`=:debito, 
                            `credito`=:credito, 
                            `nuevo_saldo`=:nuevo_saldo 
                            WHERE `numero_cuenta` =:numero_cuenta ";
                        $sql_dato = array(
                            array(":saldo_inicial",$saldoIn),
                            array(":debito",$debitoN),
                            array(":credito",$creditoN),
                            array(":nuevo_saldo",$nuevoN),
                            array(":numero_cuenta",$rowa[$a2][2]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                }
            }
        }
        $i =$v+1;
        $v +=500;
        $valorreg -=500; 

    }
    echo 1;
}
function trmA($tipoc){
    global $con;
    $fechaA = date('Y-m-d');
    $totalc = $con->Listar("SELECT valor FROM gf_trm WHERE fecha = '$fechaA' AND tipo_cambio = $tipoc ");
    return $totalc[0][0];
}
function trmF($factura){
    global $con;
    $fechaA = date('Y-m-d');
    $totalc = $con->Listar("SELECT DISTINCT valor_trm FROM gp_detalle_factura 
        WHERE factura = $factura  ");
    return $totalc[0][0];
}

#* Funcion Generar Balances
function generarBalance($anno, $id_par, $fechaI, $fechaF, $codigoI, $codigoF, $compania, $tipo){
    global $con;
    #VACIAR LA TABLA TEMPORAL
    try {        
    $create  = $con->Listar("CREATE TABLE IF NOT EXISTS temporal_balance$compania (
        `id_unico` int(11) NOT NULL,
        `id_cuenta` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
        `numero_cuenta` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
        `nombre` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
        `cod_predecesor` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
        `saldo_inicial` double(20,2) DEFAULT NULL,
        `debito` double(20,2) DEFAULT NULL,
        `credito` double(20,2) DEFAULT NULL,
        `nuevo_saldo` double(20,2) DEFAULT NULL,
        `naturaleza` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
        `saldo_inicial_debito` double(20,2) DEFAULT NULL,
        `saldo_inicial_credito` double(20,2) DEFAULT NULL,
        `nuevo_saldo_debito` double(20,2) DEFAULT NULL,
        `nuevo_saldo_credito` double(20,2) DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
      ALTER TABLE temporal_balance$compania 
        ADD PRIMARY KEY IF NOT EXISTS(`id_unico`);
      ALTER TABLE temporal_balance$compania 
        MODIFY `id_unico` int(11) NOT NULL AUTO_INCREMENT;");
    $vaciarTabla = $con->Listar("TRUNCATE temporal_balance$compania");
    #CONSULTA CUENTAS 
    $rowc = $con->Listar("SELECT DISTINCT
                c.id_unico, 
                c.codi_cuenta,
                c.nombre,
                c.naturaleza,
                ch.codi_cuenta 
              FROM
                gf_cuenta c
              LEFT JOIN
                gf_cuenta ch ON c.predecesor = ch.id_unico
              WHERE  c.parametrizacionanno = $id_par   
                  AND  c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF'  
              ORDER BY 
                c.codi_cuenta DESC ");
    for ($i=0; $i <count($rowc) ; $i++) {  
        #GUARDA LOS DATOS EN LA TABLA TEMPORAL
        $sql_cons ="INSERT INTO `temporal_balance$compania` 
                    ( `id_cuenta`, `numero_cuenta`,`nombre`,
                    `cod_predecesor`,`naturaleza`) 
            VALUES (:id_cuenta, :numero_cuenta, :nombre, 
                    :cod_predecesor,:naturaleza)";
            $sql_dato = array(
                    array(":id_cuenta",$rowc[$i][0]),
                    array(":numero_cuenta",$rowc[$i][1]),
                    array(":nombre",$rowc[$i][2]),
                    array(":cod_predecesor",$rowc[$i][4]),   
                    array(":naturaleza",$rowc[$i][3]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
    }
    #* Consultar Cuentas Movimiento 
    $mov = $con->Listar("SELECT DISTINCT 
                c.id_unico, c.codi_cuenta, 
                c.nombre, c.naturaleza 
            FROM gf_detalle_comprobante dc 
            LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
            WHERE  c.parametrizacionanno = $id_par 
            AND c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF'  
            ORDER BY c.codi_cuenta DESC");
    $totaldeb       = 0;
    $totalcred      = 0;
    $totalsaldoID   = 0;
    $totalsaldoIC   = 0;
    $totalsaldoFD   = 0;
    $totalsaldoFC   = 0;
    $totalsaldo     = 0;
                    
    #SI FECHA INICIAL =01 DE ENERO
    $fechaPrimera   = $anno . '-01-01';
    $fechaMax       = $anno . '-12-31';
    for ($m =0; $m < count($mov) ; $m++) { 
        $wsi = "";
        $wsc = "";
        $c   = "AND dc.cuenta = '".$mov[$m][0]."' AND cp.parametrizacionanno =$id_par ";
        
        if ($fechaI == $fechaPrimera) {
            $wsi .= $c." AND cp.fecha BETWEEN '$fechaI' AND '$fechaMax' AND cc.id_unico = '5' ";
            $wsc .= $c." AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' AND cc.id_unico != '5' AND cc.id_unico != '20' ";
        } else {
            $wsi .= $c." AND cp.fecha >='$fechaPrimera' AND cp.fecha <'$fechaI' AND cc.id_unico !='20'";
            $wsc .= $c." AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' AND cc.id_unico != '5' AND cc.id_unico != '20' ";
        }
        
        
        #CONSULTA EL SALDO DEBITO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
        $com = $con->Listar("SELECT IF(SUM(dc.valor)<0,SUM(dc.valor)*-1,SUM(dc.valor))
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE dc.valor >0 $wsi ");
        
        if (!empty($com[0][0])) {
            $saldodebito = $com[0][0];
        } else {
            $saldodebito = 0;
        }
        #CONSULTA EL SALDO CREDITO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
        $com = $con->Listar("SELECT SUM(IF(dc.valor<0, dc.valor*-1, dc.valor))
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE dc.valor <0 $wsi ");
        if (!empty($com[0][0])) {
            $saldocredito = $com[0][0];
        } else {
            $saldocredito = 0;
        }
        
        #DEBITOS
        $deb = $con->Listar("SELECT SUM(IF(dc.valor<0, dc.valor*-1, dc.valor))
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE dc.valor>0 $wsc");
        if (!empty($deb[0][0])) {
            $debito = $deb[0][0];
        } else {
            $debito = 0;
        }
        #CREDITOS
        $cr = $con->Listar("SELECT SUM(IF(dc.valor<0, dc.valor*-1, dc.valor))
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE dc.valor<0 $wsc");
        if (!empty($cr[0][0])) {
            $credito =$cr[0][0];
        } else {
            $credito = 0;
        }
        
        if($mov[$m][3]=='1'){
            $saldoNuevo     = ($saldodebito+$debito)-($saldocredito+$credito);
            $saldoInicial   = ($saldodebito - $saldocredito);
            if($saldoNuevo > 0){
                $nuevoSaldodebito = $saldoNuevo;
                $nuevoSaldoCredito = 0;
            } else {
                $nuevoSaldoCredito = $saldoNuevo*-1;
                $nuevoSaldodebito = 0;
            }
            $sql_cons ="UPDATE `temporal_balance$compania` 
                SET `saldo_inicial_debito` =:saldo_inicial_debito , 
                `saldo_inicial_credito` =:saldo_inicial_credito , 
                `debito`=:debito, 
                `credito`=:credito, 
                `nuevo_saldo_debito`=:nuevo_saldo_debito ,
                `nuevo_saldo_credito`=:nuevo_saldo_credito,
                `nuevo_saldo`=:nuevo_saldo, 
                `saldo_inicial`=:saldo_inicial  
                WHERE `id_cuenta` =:id_cuenta ";
            $sql_dato = array(
                array(":saldo_inicial_debito",$saldodebito),
                array(":saldo_inicial_credito",$saldocredito),
                array(":debito",$debito),
                array(":credito",$credito),
                array(":nuevo_saldo_debito",$nuevoSaldodebito),
                array(":nuevo_saldo_credito",$nuevoSaldoCredito),
                array(":nuevo_saldo",$saldoNuevo),
                array(":saldo_inicial",$saldoInicial),
                array(":id_cuenta",$mov[$m][0]),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            $totaldeb       += $debito;
            $totalcred      += $credito;
            $totalsaldoID   += $saldodebito;
            $totalsaldoIC   += $saldocredito;
            $totalsaldoFD   += $nuevoSaldodebito;
            $totalsaldoFC   += $nuevoSaldoCredito;
            $totalsaldo     += $saldoNuevo;
            
        #SI LA NATURALEZA ES CREDITO
        }else{
            $saldoNuevo     = ($saldodebito+$debito)-($saldocredito+$credito);
            $saldoInicial   = ($saldodebito - $saldocredito);
            if($saldoNuevo > 0){
                $nuevoSaldodebito = $saldoNuevo;
                $nuevoSaldoCredito = 0;
            } else {
                $nuevoSaldoCredito = $saldoNuevo*-1;
                $nuevoSaldodebito = 0;
            }
            $sql_cons ="UPDATE `temporal_balance$compania` 
                SET `saldo_inicial_debito` =:saldo_inicial_debito , 
                `saldo_inicial_credito` =:saldo_inicial_credito , 
                `debito`=:debito, 
                `credito`=:credito, 
                `nuevo_saldo_debito`=:nuevo_saldo_debito, 
                `nuevo_saldo_credito`=:nuevo_saldo_credito,
                `saldo_inicial`=:saldo_inicial, 
                `nuevo_saldo`=:nuevo_saldo  
                WHERE `id_cuenta` =:id_cuenta ";
            $sql_dato = array(
                array(":saldo_inicial_debito",$saldocredito),
                array(":saldo_inicial_credito",$saldodebito),
                array(":debito",$credito),
                array(":credito",$debito),
                array(":nuevo_saldo_debito",$nuevoSaldoCredito),
                array(":nuevo_saldo_credito",$nuevoSaldodebito),
                array(":nuevo_saldo",$saldoNuevo),
                array(":saldo_inicial",$saldoInicial),
                array(":id_cuenta",$mov[$m][0]),
            );
            $obj_resp       = $con->InAcEl($sql_cons,$sql_dato);
            $totaldeb       += $credito;
            $totalcred      += $debito;
            $totalsaldoID   += $saldocredito;
            $totalsaldoIC   += $saldodebito;
            $totalsaldoFD   += $nuevoSaldoCredito;
            $totalsaldoFC   += $nuevoSaldodebito;
            $totalsaldo     += $saldoNuevo;
        }
        
        
    }
    $totalc = $con->Listar("SELECT count(*) 
        FROM temporal_balance$compania 
        ORDER BY numero_cuenta DESC ");
    $i = 0;
    $v = 500;
    $valorreg = $totalc[0][0];
    while($valorreg > 0){
        #CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
        $rowa1 = $con->Listar("SELECT id_cuenta,numero_cuenta, cod_predecesor, 
                saldo_inicial_debito,saldo_inicial_credito,
                debito, credito, 
                nuevo_saldo_debito, nuevo_saldo_credito, 
                nuevo_saldo, saldo_inicial 
                FROM temporal_balance$compania 
                WHERE id_unico BETWEEN $i AND $v 
                ORDER BY numero_cuenta DESC ");
        for ($ra = 0; $ra < count($rowa1); $ra++) {
            $rowa = $con->Listar("SELECT id_cuenta,numero_cuenta, cod_predecesor, 
                    saldo_inicial_debito, saldo_inicial_credito,
                    debito, credito, 
                    nuevo_saldo_debito, nuevo_saldo_credito, 
                    nuevo_saldo , saldo_inicial 
                FROM temporal_balance$compania WHERE id_cuenta ='".$rowa1[$ra][0]."'
                ORDER BY numero_cuenta DESC ");
            for ($rl = 0; $rl < count($rowa); $rl++) { 
                if(!empty($rowa[$rl][2])){
                    $va = $con->Listar("SELECT numero_cuenta,
                        saldo_inicial_debito, saldo_inicial_credito,
                        debito, credito, 
                        nuevo_saldo_debito, nuevo_saldo_credito, 
                        nuevo_saldo , saldo_inicial 
                        FROM temporal_balance$compania WHERE numero_cuenta ='".$rowa[$rl][2]."'");
                    $saldoInD   = $rowa[$rl][3] + $va[0][1];
                    $saldoInC   = $rowa[$rl][4] + $va[0][2];
                    $debitoN    = $rowa[$rl][5] + $va[0][3];
                    $creditoN   = $rowa[$rl][6] + $va[0][4];
                    $nuevoND    = $rowa[$rl][7] + $va[0][5];
                    $nuevoNC    = $rowa[$rl][8] + $va[0][6];
                    $nuevo      = $rowa[$rl][9] + $va[0][7];
                    $saldoI     = $rowa[$rl][10] + $va[0][8];
                    
                    $sql_cons ="UPDATE `temporal_balance$compania` 
                        SET `saldo_inicial_debito` =:saldo_inicial_debito , 
                        `saldo_inicial_credito` =:saldo_inicial_credito , 
                        `debito`=:debito, 
                        `credito`=:credito, 
                        `nuevo_saldo_debito`=:nuevo_saldo_debito, 
                        `nuevo_saldo_credito`=:nuevo_saldo_credito, 
                        `nuevo_saldo`=:nuevo_saldo, 
                        `saldo_inicial`=:saldo_inicial 
                        WHERE `numero_cuenta` =:numero_cuenta ";
                    $sql_dato = array(
                        array(":saldo_inicial_debito",$saldoInD),
                        array(":saldo_inicial_credito",$saldoInC),
                        array(":debito",$debitoN),
                        array(":credito",$creditoN),
                        array(":nuevo_saldo_debito",$nuevoND),
                        array(":nuevo_saldo_credito",$nuevoNC),
                        array(":nuevo_saldo",$nuevo),
                        array(":saldo_inicial",$saldoI),
                        array(":numero_cuenta",$rowa[$rl][2]),
                    );
                    $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                }
            }
        }
        $i =$v+1;
        $v +=500;
        $valorreg -=500; 
    }
    
    
    } catch (Exception $exc) {
        echo $exc->getTraceAsString();
    }
    $datos = array();
    $datos = array(
        "totalsaldoID"=>$totalsaldoID, "totalsaldoIC"=>$totalsaldoIC,
        "totaldeb"=>$totaldeb,"totalcred"=>$totalcred,
        "totalsaldoFD"=>$totalsaldoFD,"totalsaldoFC"=>$totalsaldoFC,
        "totalsaldo"=>$totalsaldo);
    return $datos;
}

#Funcion Sumar Días
function sumar_dias ($fecha, $dias){
    $fecha = new DateTime($fecha);
    $fecha->modify('+'.$dias.' day');
    $nuevaFecha = (string)$fecha->format('Y-m-d');
    return ($nuevaFecha);
}
#Funcion Convertir Fecha d/m/Y
function c_fecha ($fecha){
    $fecha_div  = explode("-", $fecha);
    $anio       = trim($fecha_div[0]);
    $mes        = trim($fecha_div[1]);
    $dia        = trim($fecha_div[2]);
    $fechaC = $dia."/".$mes."/".$anio;
    return ($fechaC);
}

function tipo_cambio($compania){
    $rta = 0;
    global $con;
    $totalc = $con->Listar("SELECT * FROM gp_tipo_factura WHERE compania = $compania AND tipo_cambio IS NOT NULL  ");
    if(!empty($totalc[0][0])){
       $rta = 1; 
    }
    return $rta;
}
?>