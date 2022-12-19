<?php
	
	function DiasFecha($fecha,$dias){
		$dias = $dias - 1; 
		$nuevafecha = strtotime ( $dias." day" , strtotime ( $fecha ) ); 
		$nuevafecha = date ( 'Y-m-j' , $nuevafecha ); //formatea nueva fecha 
		return $nuevafecha;
		
	} //retorna valor de la fecha 

	function MesesFecha($fecha,$meses){
		#$meses = $meses - 1; 
		$nuevafecha = strtotime ( $meses." month" , strtotime ( $fecha ) ); 
		$nuevafecha = date ( 'Y-m-j' , $nuevafecha );
				
		return $nuevafecha;
		
	} //retorna valor de la fecha 

	function DiasRestar($fecha){
		 
		$nuevafecha = strtotime ( '-2 day' , strtotime ( $fecha ) ); 
		$nuevafecha = date ( 'Y-m-j' , $nuevafecha ); //formatea nueva fecha 
		return $nuevafecha;
		
	} //retorna valor de la fecha 

	function FestivosFecha($fecha,$dias){

		require '/Conexion/conexion.php';
		
		$nuevafecha = strtotime ( $dias." day" , strtotime ( $fecha ) ); 
		$nuevafecha = date ( 'Y-m-j' , $nuevafecha );

		$sql1 = "SELECT id_unico, fecha , descripcion FROM gf_festivos WHERE fecha BETWEEN '$fecha' AND '$nuevafecha'";
		
		$fec2 = $mysqli->query($sql1);

		$fecha_div = explode("-", $nuevafecha);
        $anion = $fecha_div[0];
        $mesn = $fecha_div[1];
        $dian = $fecha_div[2];
		
		while($Dfes = mysqli_fetch_row($fec2)){
			            
            $dian = $dian + 1 ;
		}

		$nuevafecha = $anion.'-'.$mesn.'-'.$dian;

		
	}

	function PeriodosFaltantes($fecha,$dias){

		require '/Conexion/conexion.php';

		$nuevafecha = strtotime ( $dias." day" , strtotime ( $fecha ) ); 
		$nuevafecha = date ( 'Y-m-j' , $nuevafecha );

		#consulta los periodos que coincidan la fehca de inicio de la incapacidad  que no esten cerrados
		$sql = "SELECT id_unico, codigointerno, fechainicio, fechafin FROM gn_periodo WHERE fechainicio <= $fecha  AND liquidado !=1 AND id_unico !=1";
		$res = $mysqli->query($sql);
		$nres = mysqli_num_rows($res);
		
		#valida si existe uno o mas periodos cuya fecha de inicio de la incpacidad o la licencia sea posterior o igual a la fecha inicial del periodo 
		if($nres >=1){

			$resu = mysqli_fetch_row($res);

			# consulta los periodos que coincidan la fecha final de la incapacidad que no esten cerrados
			$sql1 = "SELECT id_unico, codigointerno, fechainicio, fechafin FROM gn_periodo WHERE fechafin >= $nuevafecha AND liquidado !=1 AND id_unico !=1";
			$res1 = $mysqli->query($sql1);
			$nres = mysqli_num_rows($res1);	

			#valida si existe uno o mas periodos cuya fecha fianl de la incapacidad o licencia sea anterior o igual a la fehca final del periodo
			if($nres >=1){
		
				$resu1 = mysqli_fetch_row($resu1);

				$nper = "SELECT id_unico, COUNT(id_unico) FROM gn_periodo WHERE  AND id_unico  BETWEEN $resu[0] AND resu1[0]";
				$res2 = $mysqli->query($nper);
				$resu2 = mysqli_fetch_row($res2);

				$numper = $resu2;
			}else {

				$numper = 0;
			}	
		}else{

			$numper = 0 ;
		}

		
		return $numper;
	}

	function Annos($fecha,$anios){
		$anios = $anios * -1; 
		$nuevafecha = strtotime ( $anios." year" , strtotime ( $fecha ) ); 
		$nuevafecha = date ( 'Y-m-j' , $nuevafecha ); //formatea nueva fecha 
		return $nuevafecha;
		
	}
?>