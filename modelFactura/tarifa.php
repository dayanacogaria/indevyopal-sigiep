<?php
require_once ('../Conexion/db.php');

class tarifa{
    public $id_unico;
    public $uso;
    public $periodo;
    public $estrato;
    public $tipo_taria;
    public $porcentaje_iva;
    public $porcentaje_impoconsumo;
    public $valor;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(tarifa $data){
        try {
            $sql = "INSERT INTO gp_tarifa(
                                    uso,
                                    periodo,
                                    estrato,
                                    tipo_taria,
                                    porcentaje_iva,
                                    porcentaje_impoconsumo,
                                    valor
                                ) VALUES(
                                    $data->uso,
                                    $data->periodo,
                                    $data->estrato,
                                    $data->tipo_taria,
                                    $data->porcentaje_iva,
                                    $data->porcentaje_impoconsumo,
                                    $data->valor
                                )";
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

    public function modificar(tarifa $data){
        try {
            $sql = "UPDATE  gp_tarifa SET
                            uso                    = $data->uso,
                            periodo                = $data->periodo,
                            estrato                = $data->estrato,
                            tipo_taria             = $data->tipo_taria,
                            porcentaje_iva         = $data->porcentaje_iva,
                            porcentaje_impoconsumo = $data->porcentaje_impoconsumo,
                            valor                  = $data->valor
                    WHERE   id_unico               = $data->id_unico";
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

    public function eliminar($id_unico){
        try {
            $sql = "DELETE FROM gp_tarifa WHERE id_unico = $id_unico";
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