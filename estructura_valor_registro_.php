<?php 

function valorRegistro($id_comp)
{

	$apropiacion_def = 0;

	$queryApro = "SELECT  detComP.valor 
		from gf_detalle_comprobante_pptal detComP
		left join gf_comprobante_pptal comP on comP.id_unico = detComP.comprobantepptal
		left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
		where comP.id_unico = $id_comp"; 

	$apropia = $GLOBALS['mysqli']->query($queryApro);
	
	while($row = mysqli_fetch_row($apropia))
	{
		$apropiacion_def += $row[0];
	}
	return $apropiacion_def;
}

function modificacionRegistro()
{


	$apropiacion_def = 0;

	$queryApro = "SELECT   detComP.valor, tipComP.tipooperacion 
		from gf_detalle_comprobante_pptal detComP
		left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal
		left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
		left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
		left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
		left join gf_concepto_rubro conRub on conRub.rubro = rubP.id_unico
		left join gf_concepto con on con.id_unico = conRub.concepto
		where tipComP.clasepptal = 15
		and tipComP.tipooperacion != 1"; 

	$apropia = $GLOBALS['mysqli']->query($queryApro);
	
	while($row = mysqli_fetch_row($apropia))
	{
		if(($row[1] == 2) || ($row[1] == 4))
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


function afectacionRegistro($id_det_comp)
{

 

	$apropiacion_def = 0;

	$queryApro = "SELECT  detComP.valor, tipComP.tipooperacion
		from gf_comprobante_pptal comP
		left join gf_detalle_comprobante_pptal detComP on  comP.id_unico = detComP.comprobantepptal
		left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
		left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
		left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
		left join gf_concepto_rubro conRub on conRub.rubro = rubP.id_unico
		left join gf_concepto con on con.id_unico = conRub.concepto
		where detComP.comprobanteafectado = $id_det_comp 
		and tipComP.clasepptal != 15
		and tipComP.clasepptal != 20"; 

	$apropia = $GLOBALS['mysqli']->query($queryApro);
	
	while($row = mysqli_fetch_row($apropia))
	{
		if(($row[1] == 2) || ($row[1] == 4))
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

	

 ?>