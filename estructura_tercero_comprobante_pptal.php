<?php 
###########MODIFICACIONES################# 
#13/03/2017 |ERICA G. | TILDES
##########################################
	require_once('Conexion/conexion.php');
	require_once('estructura_valor_registro.php');
    session_start();
    $anno = $_SESSION['anno'];

	$id_tercero = $_REQUEST['id_tercero']; 
	$clase = $_REQUEST['clase'];    

    $queryComp ="SELECT  com.id_unico, com.numero, com.fecha, com.descripcion
  		FROM gf_comprobante_pptal com
 		left join gf_tipo_comprobante_pptal tipoCom on tipoCom.id_unico = com.tipocomprobante
  		WHERE tipoCom.clasepptal = $clase 
 		and tipoCom.tipooperacion = 1
		and com.tercero =  $id_tercero 
                AND com.parametrizacionanno = $anno";

	$comprobanteP = $mysqli->query($queryComp);
        
	while ($row = mysqli_fetch_row($comprobanteP))
	{
    $saldDisp = 0;
    $totalSaldDispo = 0;
 	  $queryDetCompro = "SELECT detComp.id_unico, detComp.valor   
            FROM gf_detalle_comprobante_pptal detComp, gf_comprobante_pptal comP 
            WHERE comP.id_unico = detComp.comprobantepptal 
            AND comP.id_unico = ".$row[0];

        
        $detCompro = $mysqli->query($queryDetCompro);
        while($rowDetComp = mysqli_fetch_row($detCompro))
        {
        	                
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
                  dcp.comprobanteafectado =".$rowDetComp[0];
                $detAfec = $mysqli->query($queryDetAfe);
               
                while($rowDtAf = mysqli_fetch_row($detAfec))
                {
                    if($rowDtAf[1]==3){
                          $saldDisp = $saldDisp - $rowDtAf[0];
                    } else {
                        if(($rowDtAf[1] == 2) || ($rowDtAf[1] == 4)){
                            $saldDisp = $saldDisp + $rowDtAf[0];
                        } else {
                            $saldDisp = $saldDisp - $rowDtAf[0];
                        }
                    }
                }
               
                
                
    	}
    	 $saldo = $saldDisp;
       
		
		if($saldo > 0)
		{
			$fecha_div = explode("-", $row[2]);
		    $anio = $fecha_div[0];
		    $mes = $fecha_div[1];
		    $dia = $fecha_div[2];
		    $fecha = $dia."/".$mes."/".$anio;

			echo '<option value="'.$row[0].'">'.$row[1].' '.$fecha.' '.ucwords(mb_strtolower($row[3])).' $'.number_format($saldo, 2, '.', ',').'</option>';
		}
	}


 ?>
