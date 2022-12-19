<?php
require_once ('../Conexion/db.php');
class movPro{

    public $producto;
    public $detallemovimiento;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(movPro $data){
        try {
            $sql = "INSERT INTO gf_movimiento_producto(
                                    producto,
                                    detallemovimiento
                                ) VALUES(
                                    $data->producto,
                                    $data->detallemovimiento
                                )";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = 1;
            }else{
                $rest = 0;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}