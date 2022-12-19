<?php 
###################MODIFICACIONES#############################  
#01/03/2017 |ERICA G. | AGREGAR VARIABLE DE SESION PARA VALIDAR MODIFICACION DETALLES MODIF.DIS
#10/02/2017 |ERICA G. | ASIGNAR VALOR VARIABLE PARA AGREGAR CUENTA POR PAGAR A EGRESO
################################################################


    require_once('Conexion/conexion.php');
    session_start();

	$sesion = $_REQUEST['sesion'];
	$id_com = $_REQUEST['numero'];
	$valN = 0; 
	$nuevo = '';

	if(!empty($_REQUEST['nuevo']))  
	{
		$nuevo = $_REQUEST['nuevo'];
                 if(!empty($_REQUEST['nuevo']))  
            {
            if($_POST['nuevo']=='nuevo_MD'){
                 $_SESSION['mod'] = $id_com;
            } else {
                 $_SESSION['mod'] = '';
            }


            if($_POST['nuevo']=='nuevo_GE'){
                $_SESSION['comprobanteGenerado'] = $id_com;
            } else {
                $_SESSION['comprobanteGenerado'] = '';
            }}
                
	}

	if(!empty($_REQUEST['valN']))  
	{
		$valN = $_REQUEST['valN'];
	}

	if($valN == 2)
	{
		$_SESSION[$nuevo] = '';
	}
	else
	{
		$_SESSION[$nuevo] = $valN;
	}
       
        $_SESSION[$sesion] = $id_com;
	echo 1;

?>
