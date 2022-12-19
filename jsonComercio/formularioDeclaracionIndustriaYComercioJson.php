<?php 

require_once '../Conexion/conexion.php';
session_start();


$identificacion=$_POST['identific'];
$valor=$_POST['numero'];  //nunmero identificacion
$sql="";



	# code...
		$sql="SELECT c.id_unico,
		    IF(CONCAT_WS(' ',
		    t.nombreuno,
		    t.nombredos,
		    t.apellidouno,
		    t.apellidodos) 
		    IS NULL OR CONCAT_WS(' ',
		    t.nombreuno,
		    t.nombredos,
		    t.apellidouno,
		    t.apellidodos) = '',
		    (t.razonsocial),
		    CONCAT_WS(' ',
		    t.nombreuno,
		    t.nombredos,
		    t.apellidouno,
		    t.apellidodos)) AS nt, 
		    c.tercero
		 	FROM gc_contribuyente c 
			LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
			LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico=t.tipoidentificacion
			WHERE t.numeroidentificacion='$valor' AND ti.nombre='$identificacion'";


			//contribuyente
			$rta=false;
			$resultado=$mysqli->query($sql);
			if($resultado->num_rows > 0){
				$rta=true;
				//header('Content-Type: application/json');
				echo json_encode($rta);
exit();

			}else{
				$rta=false;
				//header('Content-Type: application/json');
				echo json_encode($rta);
exit();
				
			}






 ?>