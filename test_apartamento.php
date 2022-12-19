<?php
session_start();
#

function rellenaArray(){
    require 'Conexion/conexion.php';  
   // var_dump($tip);
    
    $sql_tercer="SELECT eh.id_unico, 
                    concat(eh.codigo,' - ',eh.descripcion) as descripcion

                    from gh_espacios_habitables  as eh where 
                    concat(eh.codigo,' - ',eh.descripcion) is not null 
                    and eh.tipo=3 ";    
    
    $resul = $mysqli->query($sql_tercer);
    $array=array();    
    while($rowDF=mysqli_fetch_array($resul)){
        $ter=$rowDF['descripcion'];
        if(empty($ter)){
            $array=array("".","."Apartamento");  
        }else{
            $array[]= $rowDF['id_unico'].",".$ter;
            
        }
    }
   
    return $array;
}
$aUsers = rellenaArray();
$aInfo = rellenaArray();
 
	$input = strtolower( $_GET['input'] );
	$len = strlen($input);
	$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 0;
	
	$aResults = array();
	$count = 0;
	
	if ($len)
	{
		for ($i=0;$i<count($aUsers);$i++)
		{
                    $xxx = explode(",", $aUsers[$i]);
			// had to use utf_decode, here
			// not necessary if the results are coming from mysql
			//
			if (strtolower(substr(utf8_decode($xxx[1]),0,$len)) == $input)
			{
				$count++;
				$aResults[] = array( "id"=>($xxx[0]) ,"value"=>htmlspecialchars($xxx[1]), "info"=>htmlspecialchars($xxx[1]) );
			}
			
			if ($limit && $count==$limit)
				break;
		}
	}
	
	if (isset($_REQUEST['json']))
	{
		header("Content-Type: application/json");
	
		echo "{\"results\": [";
		$arr = array();
		for ($i=0;$i<count($aResults);$i++)
		{
			$arr[] = "{\"id\": \"".$aResults[$i]['id']."\", \"value\": \"".$aResults[$i]['value']."\", \"info\": \"\"}";
		}
		echo implode(", ", $arr);
		echo "]}";
	}
	else
	{
		header("Content-Type: text/xml");

		echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?><results>";
		for ($i=0;$i<count($aResults);$i++)
		{
			echo "<rs id=\"".$aResults[$i]['id']."\" info=\"".$aResults[$i]['info']."\">".$aResults[$i]['value']."</rs>";
		}
		echo "</results>";
	}
?>