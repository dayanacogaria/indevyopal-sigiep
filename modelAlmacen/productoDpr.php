<?php
require_once ('./Conexion/db.php');
class productoDpr{
    public $id_unico;
    public $descripcion;
    public $valor;
    public $meses;
    public $vida_util_remanente;
    public $fecha;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function actDatosDepreciacion(productoDpr $data){
        try {
            $sql = "UPDATE gf_producto
                    SET    meses               = $data->meses,
                           vida_util_remanente = $data->vida_util_remanente,
                           fecha_adquisicion   = '$data->fecha'
                    WHERE  id_unico            = $data->id_unico";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}