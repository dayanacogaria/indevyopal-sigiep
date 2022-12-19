<?php 
#21/03/2017 | ERICA G. | TILDES
#MODIFICADO 27/01/2017 ERICA G.
	require_once('Conexion/conexion.php');
	require_once('estructura_valor_registro.php');
    session_start();
$anno = $_SESSION['anno'];
    
   
	$id_tercero = $_POST['id_tercero']; 
	$clase = $_POST['clase'];    

  $fragSql = '';
  $signo = '';
  $num = 0;

  if(!empty($_POST['tipoOp']))
  {
    $tipoOp = $_POST['tipoOp']; 
    $signo = $_POST['signo'];
    $fragSql = " AND tipoCom.tipooperacion $signo $tipoOp ";
  }

	$queryComp ="SELECT  com.id_unico, com.numero, com.fecha, com.descripcion
  		FROM gf_comprobante_pptal com
 		left join gf_tipo_comprobante_pptal tipoCom on tipoCom.id_unico = com.tipocomprobante 
                
  		WHERE tipoCom.clasepptal = $clase  and com.parametrizacionanno = $anno 
      $fragSql 
		and com.tercero =  $id_tercero ORDER BY com.numero ASC";

	$comprobanteP = $mysqli->query($queryComp);
	while ($row = mysqli_fetch_row($comprobanteP))
	{
	 	$queryDetCompro = "SELECT detComp.id_unico, detComp.valor   
            FROM gf_detalle_comprobante_pptal detComp, gf_comprobante_pptal comP 
            WHERE comP.id_unico = detComp.comprobantepptal 
            AND comP.id_unico = ".$row[0];

        $saldDispo = 0;
        $totalSaldDispo = 0;
        $detCompro = $mysqli->query($queryDetCompro);
        while($rowDetComp = mysqli_fetch_row($detCompro))
        {
        	$rowDetComp[1];
        	$queryDetAfetc = "SELECT valor   
          		FROM gf_detalle_comprobante_pptal   
          		WHERE comprobanteafectado = ".$rowDetComp[0];
          	$detAfect = $mysqli->query($queryDetAfetc);
          	$totalAfec = 0;
          	while($rowDetAf = mysqli_fetch_row($detAfect))
          	{
            	$totalAfec += $rowDetAf[0];
          	}
            
        	$saldDispo = $rowDetComp[1] - $totalAfec;
        	$totalSaldDispo += $saldDispo;
    	}
    	$saldo = $totalSaldDispo;
		
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
