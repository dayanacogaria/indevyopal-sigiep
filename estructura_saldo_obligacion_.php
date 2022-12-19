<?php 

function valorRegistro($id_comp, $id_rub_fue)
{

	//Conexión en Hostinger.
	/*
	$cone = mysqli_connect("mysql.hostinger.co","u858942576_aaa","cG0laRuRWV");
	mysqli_select_db($cone,"u858942576_sigep");*/

	//Conexión en local. 
	/**/
	$cone = mysqli_connect("localhost","root","");
	mysqli_select_db($cone,"sigep");

	$queryValor = "SELECT  detComP.valor 
	from gf_detalle_comprobante_pptal detComP
	left join gf_comprobante_pptal comP on comP.id_unico = detComP.comprobantepptal
	left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
	where comP.id_unico = $id_comp
    AND detComP.rubrofuente = $id_rub_fue";

	$valorReg = mysqli_query($cone,$queryValor);
	$row = mysqli_fetch_row($valorReg);
	return $row[0];
}

function modificacionRegistro($id_rub_fue, $clase)
{
	//Conexión en Hostinger.
	/*
	$cone = mysqli_connect("mysql.hostinger.co","u858942576_aaa","cG0laRuRWV");
	mysqli_select_db($cone,"u858942576_sigep");*/

	//Conexión en local. 
	/**/
	$cone = mysqli_connect("localhost","root","");
 	mysqli_select_db($cone,"sigep");

	$apropiacion_def = 0;

    $queryModificacion = "SELECT   detComP.valor, tipComP.tipooperacion
		from gf_detalle_comprobante_pptal detComP
		left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal
		left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
		left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
		left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
		left join gf_concepto_rubro conRub on conRub.rubro = rubP.id_unico
		left join gf_concepto con on con.id_unico = conRub.concepto
		where tipComP.clasepptal = $clase 
		and tipComP.tipooperacion != 1
        and rubFue.id_unico = $id_rub_fue";

	$modificacion = mysqli_query($cone,$queryModificacion);
	
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
	//Conexión en Hostinger.
	/*
	$cone = mysqli_connect("mysql.hostinger.co","u858942576_aaa","cG0laRuRWV");
	mysqli_select_db($cone,"u858942576_sigep");*/

	//Conexión en local. 
	/**/
 	$cone = mysqli_connect("localhost","root","");
 	mysqli_select_db($cone,"sigep");

 	$afectacion_reg = 0;

    $queryAfectacion = "SELECT  detComP.valor, tipComP.tipooperacion
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

	$afectacion = mysqli_query($cone,$queryAfectacion);
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
	//Conexión en Hostinger.
	/*
	$cone = mysqli_connect("mysql.hostinger.co","u858942576_aaa","cG0laRuRWV");
	mysqli_select_db($cone,"u858942576_sigep");*/

	//Conexión en local. 
	/**/
 	$cone = mysqli_connect("localhost","root","");
 	mysqli_select_db($cone,"sigep");

    $queryAfectacion = "SELECT  detComP.valor, tipComP.tipooperacion
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

	$afectacion = mysqli_query($cone,$queryAfectacion);
	$row = mysqli_fetch_row($afectacion);
	return $row[0];

}


?>