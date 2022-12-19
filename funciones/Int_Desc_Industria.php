<?php
	session_start();
	@require ('../Conexion/conexion.php');
	@require_once ('funcionLiquidador.php');
	$xxx = $_POST['bandera'];
	$zzz = new Liquidador();
	switch ($xxx) {
		case 'int':						
			 	$valor    = $_POST['valor'];
			 	$fDec     = $_POST['fechaD'];
			 	$fVenI 	  = ''.$_POST['fechaVen'].'';
				# $fVenI = "2017-07-01";
			$fde = explode("/",$fDec);
			$fan = $fde[2];
			$fme = $fde[1];
			$fdi = $fde[0];

			$FDcl = $fan.'-'.$fme.'-'.$fdi;
			$total = $zzz::liquidar_intereses($valor, $fVenI, $FDcl);

			echo trim($total);
		break;
		
		case 'des':
			$valor    = $_POST['valor'];
			$fDec     = $_POST['fechaD'];
			$tip 	  = $_POST['tipo'];
			$Pgra	  = $_POST['p'];

			$fde = explode("/",$fDec);
			$fan = $fde[2];
			$fme = $fde[1];
			$fdi = $fde[0];

			$FDcl = $fan.'-'.$fme.'-'.$fdi;
			$total  = $zzz::applyDes($FDcl, $valor, $tip,$Pgra);
			echo trim($total);
		break;		
		
		case 'san':

			$per 	= $_POST['per'];
			$vig 	= $_POST['vig']; 

			$AperG = "SELECT vigencia FROM gc_anno_comercial WHERE id_unico = '$per'";
			$APGr = $mysqli->query($AperG);
			$APG = mysqli_fetch_row($APGr);

			$AVComer = "SELECT vigencia FROM gc_vigencia_comercial WHERE id_unico = '$vig' ";
			$AnVi = $mysqli->query($AVComer);
			$AVC = mysqli_fetch_row($AnVi);

			$SANS = $AVC[0] - $APG[0];
		break;
	}	
?>