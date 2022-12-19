<?php
################MODIFICACIONES######################
#07/03/2017 |ERICA G.|ARCHIVO CREADO
######################################
@session_start();
##FUNCIONES EJECUCION GASTOS##
function presupuestos($id_rubF, $tipoO, $fechaI, $fechaF)
{
        require'../../Conexion/conexion.php';
	$presu = 0;
	$query = "SELECT valor as value 
                    FROM
                      gf_detalle_comprobante_pptal dc
                    LEFT JOIN
                      gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                    WHERE
                      dc.rubrofuente = '$id_rubF' 
                      AND tcp.tipooperacion = '$tipoO' 
                      AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
                      AND (tcp.clasepptal = '13')";
	$ap = $mysqli->query($query);
        if(mysqli_num_rows($ap)>0){
            $sum=0;
            while ($sum1= mysqli_fetch_array($ap)) {
                $sum = $sum1['value']+$sum;
            }
        } else {
           $sum=0; 
        }
        $presu=$sum;
        
    return $presu;
}
function disponibilidades($id_rubFue, $clase, $fechaI, $fechaF)
{
    
        require'../../Conexion/conexion.php';
	
	$apropiacion_def = 0;
	 $queryApro = "SELECT   detComP.valor, 
                    tipComP.tipooperacion, 
                    tipComP.nombre, rubFue.id_unico, 
                    rubFue.rubro, rubP.id_unico,  
                    rubP.nombre  
                    from gf_detalle_comprobante_pptal detComP 
                    left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal 
                    left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante 
                    left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente 
                    left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro 
                    where tipComP.clasepptal = '$clase' 
                    and rubFue.id_unico =  $id_rubFue AND comP.fecha BETWEEN '$fechaI' AND '$fechaF'";
        
	$apropia = $mysqli->query($queryApro);
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
###FIN EJECUCION GASTOS###
function anno($id){
    $sql= "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $id";
    $sql = $GLOBALS['mysqli']->query($sql);
    $row = mysqli_fetch_row($sql);
    $anno = $row[0];
    return ($anno);
}

