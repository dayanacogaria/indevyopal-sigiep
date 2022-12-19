<?php 
####################MODIFICACIONES#########################
#11/05/2017 | ERICA G. | MODIFICACION Y CREACION DE FUNCION AFECTACION3 

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
		and tipComP.clasepptal != 20
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

//####FUNCION TRAER LA APROPIACION INICIAL #########
//function apropiacionfecha($id_rubFue, $fecha)
//{ 
//        @session_start();
//        $anno     = $_SESSION['anno'];
//	$apropiacion_def = 0;
//        $queryApro = "SELECT   detComP.valor valorDetalleComprobantePPTAL, tipComP.tipooperacion, 
//            comP.fecha 
//        from gf_detalle_comprobante_pptal detComP
//        left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal
//        left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
//        left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
//        left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
//        where tipComP.clasepptal = 13
//        and rubFue.id_unico =  $id_rubFue 
//        AND comP.parametrizacionanno = $anno AND comP.fecha <= '$fecha' "; 
//
//	$apropia = $GLOBALS['mysqli']->query($queryApro);
//	if(mysqli_num_rows($apropia)>0){
//            
//            while($row = mysqli_fetch_row($apropia))
//            {
//                     if(($row[1] == 1))
//                    {
//                            $apropiacion_def += $row[0];
//                    }
//                    elseif($row[1] == 3)
//                    {
//                            $apropiacion_def -= $row[0];
//                    } 
//                    elseif(($row[1] == 2) ){ 
//                            $apropiacion_def += $row[0];
//                    } elseif(($row[1] == 4)) {
//
//                        if($row[0]>0){            
//                                $apropiacion_def += $row[0];
//                        } else {
//                            $apropiacion_def += $row[0];
//                        }
//
//                    }
//            }
//            
//        
//        }
//	return $apropiacion_def;
//}
//	
//##############FUNCION TRAER EL VALOR MOVIMIENTO RUBRO###########
//function disponibilidadesfecha($id_rubFue, $fecha)
//{
//        $apropiacion_def = 0;
//        @session_start();
//        $anno     = $_SESSION['anno'];
//        $compania =$_SESSION['compania'];
//	$queryApro = "SELECT   detComP.valor valorDetalleComprobantePPTAL, tipComP.tipooperacion , 
//            comP.fecha 
//        from gf_detalle_comprobante_pptal detComP
//        left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal
//        left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
//        left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
//        left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
//        where tipComP.clasepptal = 14
//        and rubFue.id_unico =  $id_rubFue 
//        AND  comP.parametrizacionanno = $anno AND comP.fecha <= '$fecha'" ; 
//	$apropia = $GLOBALS['mysqli']->query($queryApro);
//	if(mysqli_num_rows($apropia)>0){
//            while($row = mysqli_fetch_row($apropia))
//            {
//                    if(($row[1] == 1))
//                    {
//                            $apropiacion_def += $row[0];
//                    }
//                    elseif($row[1] == 3)
//                    {
//                            $apropiacion_def -= $row[0];
//                    } 
//                    elseif(($row[1] == 2) ){ 
//                        $apropiacion_def += $row[0];
//                    } elseif(($row[1] == 4)) {
//
//                        if($row[0]>0){
//                                $apropiacion_def += $row[0];
//                            
//                        } else {
//                            $apropiacion_def += $row[0];
//                        }
//
//                    }
//            }
//            
//        
//        }
//
//	return $apropiacion_def;
//}
?>