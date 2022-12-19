<?php
require_once ('./Conexion/db.php');
/**
 * Modelo de rubro fuente
 */
class rubroFuente{
    public $rubro;
    public $fuente;

    private $mysqli;
    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function obtnerConceptoR($rubro, $concepto){
        try {
            $id  = 0;
            $sql = "SELECT id_unico FROM gf_concepto_rubro WHERE rubro = $rubro AND concepto = $concepto";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id  = $row[0];
            }
            return $id;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function registrar(rubroFuente $data){
        try {
            $sql = "INSERT INTO gf_rubro_fuente(rubro, fuente) VALUES($data->rubro, $data->fuente)";
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

    public function obtenerIdRubroFuente($rubro, $fuente){
        try {
            $id  = 0;
            $sql = "SELECT id_unico FROM gf_rubro_fuente WHERE rubro = $rubro AND fuente = $fuente";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id  = $row[0];
            }
            return $id;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerConfiguracionCtas($concepto_rubro){
        try {
            $sql = "SELECT  cnrbcta.cuenta_debito,
                            cnrbcta.cuenta_credito,
                            cnrbcta.cuenta_iva,
                            cnrbcta.cuenta_impoconsumo
                    FROM    gf_concepto_rubro_cuenta cnrbcta
                    WHERE   cnrbcta.concepto_rubro = $concepto_rubro
                    AND     cnrbcta.cuenta_debito  IS NOT NULL
                    AND     cnrbcta.cuenta_credito IS NOT NULL";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerNaturalezaCta($id_unico){
        try {
            $id  = 0;
            $sql = "SELECT naturaleza FROM gf_cuenta WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id  = $row[0];
            }
            return $id;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}