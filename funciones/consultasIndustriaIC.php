
<?php 

class comercio{
	
	public static function vencimientos($year, $date, $tipo){
		@require ('../Conexion/conexion.php');
		$str = "SELECT fecha FROM gr_vencimiento WHERE anno = '$year' AND fecha <= '$date' AND tipo = $tipo";
		$res = $mysqli->query($str);
		$row = mysqli_fetch_row($res);
		return $row[0];
	}
}