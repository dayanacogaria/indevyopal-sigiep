<?php
session_start();
#

function rellenaArray(){
    require 'Conexion/conexion.php';  
   $tip=$_GET['tipo'];
   
   // var_dump($tip);
    $sql_tercer="";
    if($tip==1){
    $sql_tercer="SELECT DISTINCT 
                pd.codigo_catastral,
                CONCAT(pd.codigo_catastral,' - ',p.numero,' - ',p.nombres) as cont
                from gr_factura_predial fp
                left join gp_predio1 pd on pd.id_unico=fp.predio
                left join gp_tercero_predio t on t.predio=pd.id_unico
                left join gr_propietarios p on p.id_unico=t.tercero where t.orden='001'";    
    }
    else if($tip==2){
        $sql_tercer="SELECT DISTINCT c.codigo_mat,tr.numeroidentificacion, IF(CONCAT_WS(' ',
                     tr.nombreuno,tr.nombredos, tr.apellidouno,
                     tr.apellidodos) 
                     IS NULL OR CONCAT_WS(' ', tr.nombreuno,
                     tr.nombredos,tr.apellidouno,tr.apellidodos) = '',
                     (tr.razonsocial), CONCAT_WS(' ', tr.nombreuno,
                     tr.nombredos,tr.apellidouno,tr.apellidodos)) as nom from gc_contribuyente c
                     left join gf_tercero tr on tr.id_unico=c.tercero ";
    }
    if($tip==1 || $tip==2){
        $resul = $mysqli->query($sql_tercer);
        $array=array();    
        while($rowDF=mysqli_fetch_array($resul)){

            if($tip==1){
                $array[]= $rowDF['codigo_catastral'].",".$rowDF['cont'];
            }else if($tip==2){
                $array[]= $rowDF['codigo_mat'].",".$rowDF['codigo_mat']." - ".$rowDF['numeroidentificacion']." - ".$rowDF['nom'];        

            }

        }
    } else {
        $array=array("".","."Tercero");  
        
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