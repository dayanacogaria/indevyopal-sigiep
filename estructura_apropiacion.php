<?php
#Modificacion 08-02-2017 Erica G. La consulta Repetia el valor.
?>

<?php 
function apropiacion($id_rubFue)
{

	

	$apropiacion_def = 0;

	 $queryApro = "SELECT   detComP.valor valorDetalleComprobantePPTAL, tipComP.tipooperacion  
        from gf_detalle_comprobante_pptal detComP
        left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal
        left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
        left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
        left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
        where tipComP.clasepptal = 13
        and rubFue.id_unico =  $id_rubFue"; 

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
	

function disponibilidades($id_rubFue)
{

	

	$apropiacion_def = 0;

	$queryApro = "SELECT   detComP.valor valorDetalleComprobantePPTAL, tipComP.tipooperacion 
        from gf_detalle_comprobante_pptal detComP
        left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal
        left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
        left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
        left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
        where tipComP.clasepptal = 14
        and rubFue.id_unico =  $id_rubFue"; 

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
	

 ?>