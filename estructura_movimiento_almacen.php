<?php 
//require_once('Conexion/conexion.php');
function apropiacion($id_tipoMV)
    {
   
    $valorMV=0;
    mysqli_select_db($cone,"sigep");
    //$id_com = $_SESSION['id_comprobante_pptal']; 
        $queryMov = "SELECT id_unico
                     FROM       gf_tipo_movimiento
                     WHERE id_unico =  $id_tipoMV"; 

    $tmv = $GLOBALS['mysqli']->query($queryMov);
    while($rowMV = mysqli_fetch_row($tmv))
    {
        echo $valorMV = $rowMV[0]; 
    }
	return $valorMV;
    }

    function disponibilidades($id_rubFue)
    {
     
     mysqli_select_db($cone,"sigep");
            //$id_com = $_SESSION['id_comprobante_pptal']; 
            $apropiacion_def = 0;
	$queryApro = "SELECT        detComP.valor valorDetalleComprobantePPTAL, 
                                    tipComP.tipooperacion, 
                                    tipComP.nombre nombreTipoComprobante, 
                                    rubFue.id_unico IDRubroFuente , 
                                    rubFue.rubro rubroFuenteRubro, 
                                    rubP.id_unico IDRubro,  
                                    rubP.nombre nombreRubro, 
                                    con.id_unico IDConcepto, 
                                    con.nombre nombreConcepto, 
                                    conRub.id_unico IDConceptoRubro
                    from            gf_detalle_comprobante_pptal detComP
                    left join       gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal
                    left join       gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
                    left join       gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
                    left join       gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
                    left join       gf_concepto_rubro conRub on conRub.rubro = rubP.id_unico
                    left join       gf_concepto con on con.id_unico = conRub.concepto
                    where           tipComP.clasepptal = 14
                    and             rubFue.id_unico =  $id_rubFue"; 
	$apropia =  $GLOBALS['mysqli']->query($queryApro);
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