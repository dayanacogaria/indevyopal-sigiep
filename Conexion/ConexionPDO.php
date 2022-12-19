<?php
class ConexionPDO
{

	public $db_serv ="localhost"; // ip del server
	public $db_nomb ="vivienda_yopal"; // Nombre de la base de datos
	public $db_usua ="sigiep"; // Usuario base de datos
	public $db_clav ="grupo3a@2020"; // Clave de la base de datos

	public $obj_resu; //Objeto que contiene el resultado

	/**********************************************
	 Inicializacion de variable de la base de datos
	***********************************************/
	public function MET_CONEXION(){
		try
		{
			$this->obj_resu = new PDO('mysql:host='.$this->db_serv.';dbname='.$this->db_nomb.';', $this->db_usua, $this->db_clav);
			$this->obj_resu->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);                
		}
		catch(Exception $e)
		{
			$this->obj_resu = $e->getMessage();
			die($e->getMessage());
		}
		return $this->obj_resu;
	}
	/**********************************************
	 METODO DE LISTAR
	***********************************************/
	public function Listar($arg_cons){
		$loc_cone = null; //Conexion
		$loc_coma = null; //Comandos
		$loc_rows = null; //Filas de la consulta
		$loc_resu = null; //Resultado

		try{
			$loc_cone = $this->MET_CONEXION();
			$loc_coma = $loc_cone->prepare($arg_cons);
			$loc_coma->execute();

			while ($loc_rows = $loc_coma->fetch()) {
				$loc_resu[] = $loc_rows; 
			}
		}
		catch(Exception $e)
		{
			$this->obj_resu = null;
			$this->obj_resu = $e->getMessage();
		}
		return $loc_resu;
	}
	/**********************************************
	 METODO DE INSERTAR, ACTUALIZAR Y ELIMINAR
	***********************************************/	
	public function InAcEl($arg_cons, $arg_data){
		$obj_resu ="";

		$loc_cone = null; //Conexion
		$loc_coma = null; //Comandos
		try {

			$loc_cone = $this->MET_CONEXION();
			$loc_coma = $loc_cone->prepare($arg_cons);
			
			for($i=0;$i<count($arg_data);$i++) {
				$loc_coma->bindParam($arg_data[$i][0],$arg_data[$i][1]);	
			}
			if (!$loc_coma) {
				$this->obj_resu ="Error al crear el registro";
			}else{
				$loc_coma->execute();
			}

		} catch (Exception $e) {
			$obj_resu = $e->getMessage();
		}

		return $obj_resu;
	}

}
 ?>