<?php 
#23/03/2017 |ERICA G. | MODIFICACION EGRESO 
#08/03/2017 |ERICA G. | MODIFICACION EGRESO 
#MODIFICADO 27/01/2017 ERICA G.
require_once('Conexion/conexion.php');
session_start();

$estruc = $_REQUEST['estruc']; 
$sesion = $_REQUEST['sesion'];
$nuevo = $_REQUEST['nuevo'];


switch ($estruc) 
{
	case 1:
		$_SESSION[$sesion] = "";
		$_SESSION[$nuevo] = "";
                $_SESSION['terceroGuardado']="";
                $_SESSION['comprobanteGenerado']="";
                $_SESSION['cntEgreso']="";
                $_SESSION['cntcxp']="";
		break;
			
	case 2:
		$id_comp = $_REQUEST['id_comp'];
		$_SESSION[$sesion] = $id_comp;
		$_SESSION[$nuevo] = "";
                
		break;

}

?>
