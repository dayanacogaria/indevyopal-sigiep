<?php
require_once ('../Conexion/db.php');
class almacen{

    public $id_unico;
    public $numero;
    public $fecha;
    public $descripcion;
    public $porcIvaGlobal;
    public $plazonEntrega;
    public $observaciones;
    public $tipoMovimiento;
    public $parametrizacionAnno;
    public $compania;
    public $responsable;
    public $tercero;
    public $dependencia;
    public $centrocosto;
    public $rubroPptal;
    public $proyecto;
    public $lugarEntrega;
    public $unidadEntrega;
    public $estado;
    public $tipo_doc_sop;
    public $numero_doc_sop;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function obtener_tercero($numero){
        try {
            $sql = "SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = '$numero' ";
            $res = $this->mysqli->query($sql);
            return $res->fetch_row();
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtener_dependencia($sigla){
        try {
            $sql = "SELECT id_unico FROM gf_dependencia WHERE sigla = '$sigla' ";
            $res = $this->mysqli->query($sql);
            return $res->fetch_row();
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ejecutar_consulta($sql){
        try {
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

    public function obtener_tipo($sigla){
        try {
            $sql = "SELECT id_unico, clase FROM gf_tipo_movimiento WHERE sigla = '$sigla' ";
            $res = $this->mysqli->query($sql);
            return $res->fetch_row();
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function valida_numero($n, $anno){
        $numero = 0;

        if($n <= 9){
            $numero = $anno."00000".$n;
        }elseif($n <= 99){
            $numero = $anno."0000".$n;
        }elseif($n <= 999){
            $numero = $anno."000".$n;
        }elseif($n <= 9999){
            $numero = $anno."00".$n;
        }elseif($n <= 99999) {
            $numero = $anno."0".$n;
        }

        return $numero;
    }

    public function comprobar_e($numero, $anno){
        $x = strpos($numero, $anno);
        return $x;
    }

    public function obtener_mov($tipo, $numero){
        try {
            $sql = "SELECT id_unico FROM gf_movimiento WHERE tipomovimiento = $tipo AND numero = '$numero' ";
            $res = $this->mysqli->query($sql);
            return $res->fetch_row();
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtener_responsable($dependencia){
        try {
            $sql = "SELECT responsable FROM gf_dependencia_responsable WHERE dependencia = $dependencia";
            $res = $this->mysqli->query($sql);
            return $res->fetch_row();
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function validar_dependencia($dependencia, $responsable){
        try {
            $filas = 0;
            $sql = "SELECT * FROM gf_dependencia_responsable WHERE dependencia = $dependencia AND responsable = $responsable";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $filas = $res->num_rows;
            }
            return $filas;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function registrar(almacen $data){
        try {
            $sql = "INSERT INTO gf_movimiento(
                                    numero,
                                    fecha,
                                    descripcion,
                                    porcivaglobal,
                                    tipomovimiento,
                                    parametrizacionanno,
                                    compania,
                                    tercero,
                                    tercero2,
                                    dependencia,
                                    centrocosto,
                                    proyecto,
                                    estado
                                ) VALUES(
                                   \"$data->numero\",
                                   '$data->fecha',
                                   '$data->descripcion',
                                   \"$data->porcIvaGlobal\",
                                   \"$data->tipoMovimiento\",
                                   \"$data->parametrizacionAnno\",
                                   \"$data->compania\",
                                   \"$data->responsable\",
                                   \"$data->tercero\",
                                   \"$data->dependencia\",
                                   \"$data->centrocosto\",
                                   \"$data->proyecto\",
                                   \"$data->estado\"
                                );";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $x = 1;
            }else{
                $x = 0;
            }

            return $x;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function validar_codigo_t($codigo){
        if($codigo < 10){
            $codigo = "00".$codigo;
        }elseif ($codigo < 100) {
            $codigo = "0".$codigo;
        }

        return $codigo;
    }
}