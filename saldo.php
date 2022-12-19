<?php

require_once('Conexion/conexion.php');
session_start();
$actualizados=0;
$dis = "SELECT cp.id_unico, cp.fecha FROM gf_comprobante_pptal cp LEFT JOIN gf_tipo_comprobante_pptal tc ON "
        . "cp.tipocomprobante = tc.id_unico "
        . "WHERE tc.clasepptal = 14 and tc.tipooperacion =1 "
        . "ORDER BY cp.fecha, cp.id_unico ASC";
$di = $mysqli->query($dis);
while ($row = mysqli_fetch_row($di)) {
    $idc = $row[0];
    $fechaC = $row[1];
    ######## DETALLES ###########
    $dt ="SELECT dc.id_unico, dc.rubrofuente FROM gf_detalle_comprobante_pptal dc "
            . "where comprobantepptal = $row[0]";
    $dt = $mysqli->query($dt);
    while ($row1 = mysqli_fetch_row($dt)) {
        ######BUSCAR LA APROPIACION#######
	$apropiacion_def = 0;
        $id_rubFue = $row1[1];
	$queryApro = "SELECT   detComP.valor valorDetalleComprobantePPTAL, tipComP.tipooperacion  
        from gf_detalle_comprobante_pptal detComP
        left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal
        left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
        left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
        left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
        where tipComP.clasepptal = 13
        and rubFue.id_unico =  $id_rubFue"; 

	$apropia =$mysqli->query($queryApro);
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
        ########## BUSCAR LA DISPONIBILIDAD ############
	$disd = 0;

	$queryApro = "SELECT   detComP.valor valorDetalleComprobantePPTAL, tipComP.tipooperacion 
        from gf_detalle_comprobante_pptal detComP
        left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal
        left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante
        left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente
        left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
        where tipComP.clasepptal = 14
        and rubFue.id_unico =  $id_rubFue and comP.fecha <='$fechaC' and comP.id_unico < $idc"; 

	$apropia = $mysqli->query($queryApro);
	if(mysqli_num_rows($apropia)>0){
            if(mysqli_num_rows($apropia)==1){
                $row=mysqli_fetch_row($apropia);
                
                if(($row[1] == 2) || ($row[1] == 4) || ($row[1] == 1))
                {
                        $disd += $row[0];
                }
                elseif($row[1] == 3)
                {
                        $disd -= $row[0];
                } 
            } else {
                while($row = mysqli_fetch_row($apropia))
                {
                        if(($row[1] == 2) || ($row[1] == 4) || ($row[1] == 1))
                        {
                                $disd += $row[0];
                        }
                        elseif($row[1] == 3)
                        {
                                $disd -= $row[0];
                        }
                }
            }
        
        } 
        #######ACTUALIZAR SALDO ###########
        $saldo = $apropiacion_def-$disd;
        $ud = "UPDATE gf_detalle_comprobante_pptal SET saldo_disponible = $saldo "
                . "WHERE id_unico = $row1[0]";
        $ud = $mysqli->query($ud);
        if($ud==false){
            echo 'false'.$row1[0];
            echo '<br/>';
        } else {
            $actualizados +=1;
        }
        
        
    }
    
}
echo 'Actualizados:'.$actualizados;

