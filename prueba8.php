<?php
session_start();

//session_unset();

foreach ($_SESSION as $index => $value)
{
    echo " $index: $value<br>";
}

echo '<p></p>';


$debitoCP = $_SESSION['debitoCP'];
$debitoCP = stripslashes($debitoCP);
$debitoCP = unserialize($debitoCP);
var_dump($debitoCP);

echo '<p></p>';

echo number_format($_SESSION['valorTotCP'], 2, '.', ',');

echo '<p></p>';


/*
$arr_sesiones_presupuesto = array('id_compr_pptal', 'id_comprobante_pptal', 'id_comp_pptal_ED', 'id_comp_pptal_ER', 'id_comp_pptal_CP', 'idCompPtalCP', 'idCompCntV');

foreach ($_SESSION as $index => $value)
{
    foreach ($arr_sesiones_presupuesto as $index2 => $value2)
  	{
    	if($index == $value2)
    	{
    		echo " $index: $value<br>";
    	}
  	}
} */
  
 /*

foreach ($_SESSION as $index => $value)
{
    if($index != 'id_compr_pptal')
    {
    	unset($_SESSION[$index]);
    	//echo " $index: $value<br>";
    }
}

echo '<p></p>';

foreach ($_SESSION as $index => $value)
{
    echo " $index: $value<br>";
} */


?>
<html>
<head>
	<title>Prueba select</title>
	<script src="js/dataTables.jqueryui.min.js" type="text/javascript"></script>
  	<script src="js/jquery.min.js"></script>
  	<script src="js/jquery-ui.js"></script>
</head>
<body>

	<select id="seleciona">
		<option value="">Selecciona valores</option>
		<option value="1/A">Uno</option>
		<option value="2/B">Dos</option>
		<option value="3/C">Tres</option>
	</select>
	<p></p>
	<input id="valor1">
	<p></p>
	<input id="valor2">

	<script type="text/javascript">
		$(document).ready(function()
		{
    		$("#seleciona").change(function()
    		{
    			/*if($("#seleciona").val() != 0 && $("#seleciona").val() != '')
    			{*/
    				var valor1 = $("#seleciona").val(); //var nombres = cadena.split(",");
    				var valores = valor1.split("/");
    				$("#valor1").val(valores[0]);
    				$("#valor2").val(valores[1]);

    			//}
    			
    		});
    	});


	</script>
</body>
</html>
