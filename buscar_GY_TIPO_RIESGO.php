<?php
	require_once('Conexion/conexion.php');
	session_start();
        $compania = $_SESSION['compania'];
        $valor = $_REQUEST['valor'];
        $id    = $_REQUEST['id_TipoR'];
        
        if($valor == 2){
            $sqlTipoR= "SELECT id_unico, nombre 
            FROM gy_tipo_riesgo WHERE id_unico!= '$id' ";
            
            $TipoR = $mysqli->query($sqlTipoR);
            
            

        }else{
            $sqlTipoR= "SELECT id_unico, nombre 
            FROM gy_tipo_riesgo WHERE compania = '$compania'  ";
            
            $TipoR = $mysqli->query($sqlTipoR);
           echo '<option value="">Tipo Riesgo</option>';
        }
	
	
	
	while ($row = mysqli_fetch_row($TipoR))
	{
		echo '<option value="'.$row[0].'">'.($row[1]).'</option>';
	}

?>

