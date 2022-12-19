<?php 
/*******************************************************************************************************************************************************************
* Creado por : 			Alexander Numpaque
* Fecha de Creación : 	05/05/2017
*******************************************************************************************************************************************************************/
@session_start();													//Llamado de session variante
$estruc = $_POST['estruc'];											//Recibimos el valor de la variable estruc, enviada por POST
require ('../funciones/funciones_formulador.php');					//Llamamos al archivo de funciones poara el generador
switch ($estruc) {													//Estructura switch case para indicar que proceso realize por medio del envio ajax
	case '1':
		$sus = array();												//Array con los valores retornados despues de consultarlos
		$name = array();											//Array vacio para los nombres para sustituir
		$formula = $_POST['formula'];								//Formula enviada
		$regex = preg_match_all('/&([^&]*)&/',$formula,$exit);		//Regex para obtención de los paramtros entre &&
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Ciclo en el que usamos al regex como conteo, y obtenemos los valores de salida para consultarlos y llenar los arrays
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		for ($i = 0; $i < $regex; $i++){			
			$n = $exit[1][$i];										//Salida del regex con el nombre de la variable
			$sus[] = trim(see_look($exit[1][$i],0));				//Consultamos usando la el valor retornado por el regex y vamos llenando el array sus
			$name[] = "/$n/";										//Llenamos el array name
		}										
		$formula = clean_str($formula);								//Limpiamos los signos como ? o & de la formula
		echo preg_replace ($name,$sus,$formula);					//Reemplamos las variables con sus respectivos valores e imprimimos el resultado
		break;	
}
 ?>