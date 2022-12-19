<?php
require_once ('./Conexion/db.php');

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

    public function getIdUnico(){
        return $this->id_unico;
    }

    public function setIdUnico($id_unico){
        $this->id_unico = $id_unico;
    }

    public function getUso(){
        return $this->uso;
    }

    public function setUso($uso){
        $this->uso = $uso;
    }

    public function getPeriodo(){
        return $this->periodo;
    }

    public function setPeriodo($periodo){
        $this->periodo = $periodo;
    }

    public function getEstrato(){
        return $this->estrato;
    }

    public function setEstrato($estrato){
        $this->estrato = $estrato;
    }

    public function getTipoTaria(){
        return $this->tipo_taria;
    }

    public function setTipoTaria($tipo_taria){
        $this->tipo_taria = $tipo_taria;
    }

    public function getPorcentajeIva(){
        return $this->porcentaje_iva;
    }

    public function setPorcentajeIva($porcentaje_iva){
        $this->porcentaje_iva = $porcentaje_iva;
    }

    public function getPorcentajeImpoconsumo(){
        return $this->porcentaje_impoconsumo;
    }

    public function setPorcentajeImpoconsumo($porcentaje_impoconsumo){
        $this->porcentaje_impoconsumo = $porcentaje_impoconsumo;
    }

    public function getValor(){
        return $this->valor;
    }

    public function setValor($valor){
        $this->valor = $valor;
    }

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(tarifa $data){
        try {
            $sql = "INSERT INTO gp_tarifa(
                                    uso,
                                    periodo,
                                    estrato,
                                    tipo_tarifa,
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
                            tipo_tarifa            = $data->tipo_taria,
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

    public function obtenerListado(){
        try{
            $str = "SELECT id_unico, nombre, valor FROM gp_tarifa";
            return $this->mysqli->query($str);
        }catch (Exception $e){
            die($e->getMessage());
        }
    }

    public function obtenerUltimoRegistro($tipo){
        $xxx = "";
        $str = "SELECT MAX(id_unico) FROM gp_tarifa WHERE tipo_tarifa = $tipo";
        $res = $this->mysqli->query($str);
        if($res->num_rows > 0){
            $row = $res->fetch_row();
            $xxx = $row[0];
        }
        return $xxx;
    }

    public function editarTarifa($id, $valor, $iva, $impo){
        try {
            $str = "UPDATE gp_tarifa SET valor = $valor, porcentaje_iva = $iva, porcentaje_impoconsumo = $impo WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}