<?php
    require_once('Conexion/conexion.php');
    session_start();
	
	$fragSql = '';
	$signo = '';
	$num = 0;

	$natural = array(2, 3, 5, 7, 10); 
  	$juridica = array(1, 4, 6, 8, 9);

	$numero =  $_POST['numero'];  
	$clase =  $_POST['clase'];  

	if(!empty($_POST['tipoOp']))
	{
		$tipoOp = $_POST['tipoOp']; 
		$signo = $_POST['signo'];
		$fragSql = " AND tipoCom.tipooperacion $signo $tipoOp ";
	}
	
	//SELECT  com.id_unico 0, com.numero numero 1, tipoCom.codigo 2, com.fecha 3, com.tercero 4
	$queryComp ="SELECT  com.id_unico, com.numero numero, tipoCom.codigo, com.fecha, com.tercero, com.descripcion      
  		FROM gf_comprobante_pptal com
 		left join gf_tipo_comprobante_pptal tipoCom on tipoCom.id_unico = com.tipocomprobante
  		WHERE tipoCom.clasepptal = $clase 
  		$fragSql 
		and com.numero like '$numero%'";

	$resultado = $mysqli->query($queryComp);

    while ($row = mysqli_fetch_row($resultado))
    {
    	$fecha = $row[3];
		$fecha_div = explode("-", $fecha);
		$dia = $fecha_div[2];
		$mes = $fecha_div[1];
		$anio = $fecha_div[0];
		$fecha = $dia.'/'.$mes.'/'.$anio;

		$queryTerc = 'SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
			    FROM gf_tercero ter 
			    LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico 
			    WHERE ter.id_unico = \''.$row[4].'\'';
        $terc = $mysqli->query($queryTerc);
        $rowTer = mysqli_fetch_row($terc);

        if(in_array($rowTer[7], $natural))
        {
            $tercero = ucwords(strtolower($rowTer[1])).' '.ucwords(strtolower($rowTer[2])).' '.ucwords(strtolower($rowTer[3])).' '.ucwords(strtolower($rowTer[4]))/*.' '.$rowTer[6]*/;
        }
        elseif (in_array($rowTer[7], $juridica))
        {
            $tercero = ucwords(strtolower($rowTer[5]))/*.' '.$rowTer[6]*/; 
        }

        $sqlValor = 'SELECT SUM(valor) 
        	FROM gf_detalle_comprobante_pptal 
        	WHERE comprobantepptal = '.$row[0];
        $valor = $mysqli->query($sqlValor);
        $rowV = mysqli_fetch_row($valor);


        echo '<div class="itemLista" style="padding: 3px;" onmouseover="this.style.backgroundColor=\'#5499c7\'" onmouseout="this.style.backgroundColor=\'#fff\'" align="left"> <span style="cursor: default" data="'.$row[1].' '.utf8_encode($row[2]).'" id="'.$row[0].'" >'.$row[1].' '._encode($row[2]).' '.$fecha.' '.$tercero.' $'.number_format($rowV[0], 2, '.', ',').'</span></div>';
    }
  

?>