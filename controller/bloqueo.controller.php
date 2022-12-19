<?php
require './clases/movimiento.php';
require './clases/espacioHabitables.php';
require './clases/tercero.php';
require './clases/tipoMovimiento.php';
class bloqueoController{

    private $bloqueo;
    private $espacio;
    private $tercero;
    private $tipo;

    public function __construct(){
        $this->bloqueo = new movimiento();
        $this->espacio = new espacioHabitables();
        $this->tercero = new tercero();
        $this->tipo    = new tipoMovimiento();
    }

    public function guardar(){
        try {
            @session_start();
            $espacio = $this->espacio->obtener($_REQUEST['txtEspacio']);
            $bloqueo = new movimiento();
            $fecha   = bloqueoController::fechaSinHora($_REQUEST['txtFecha']);
            echo substr($fecha,0,4)."-12-31 00:00:00";
            $this->bloqueo->asociado            = 'NULL';
            $this->bloqueo->numero              = htmlspecialchars(filter_input(INPUT_POST, 'txtNumero'), ENT_QUOTES, 'UTF-8');
            $this->bloqueo->tipo                = htmlspecialchars(filter_input(INPUT_POST, 'txtTipo'), ENT_QUOTES, 'UTF-8');
            $this->bloqueo->fechaInicio         = $fecha." 00:00:00";
            $this->bloqueo->fechaFinal          = substr($fecha,0,4)."-12-31 00:00:00";
            $this->bloqueo->fecha               = bloqueoController::fechaSinHora($_REQUEST['txtFecha']);
            $this->bloqueo->fechaCancelacion    = 'NULL';
            $this->bloqueo->tercero             = $_SESSION['usuario_tercero'];
            $this->bloqueo->estado              = 1;
            $this->bloqueo->formaPago           = 'NULL';
            $this->bloqueo->responsable         = $_SESSION['usuario_tercero'];
            $this->bloqueo->numAdultos          = 'NULL';
            $this->bloqueo->numPeques           = 'NULL';
            $this->bloqueo->numHabitaciones     = 'NULL';
            $this->bloqueo->observaciones       = "BLOQUEO DE HABITACIÓN $espacio[4]";
            $this->bloqueo->parametrizacionanno = $_SESSION['anno'];
            $this->bloqueo->tipo_tarifa         = 'NULL';
            $this->bloqueo->descuento           = 'NULL';
            $this->bloqueo->motivoViaje         = 'NULL';
            $this->bloqueo->proximo_destino     = 'NULL';
            $this->bloqueo->proximo_destino     = 'NULL';
            $this->bloqueo->placa               = 'NULL';
            $this->bloqueo->modoreserva         = 'NULL';
            
            $data = $bloqueo->registrar($this->bloqueo);
            if($data == true){
                $id                      = $this->bloqueo->obtenerUltimo($this->bloqueo->tipo);
                $this->bloqueo->id_unico = $id[0];
                $id                      = md5($id[0]);
                if(!empty($_REQUEST['txtEspacio'])){
                    $this->bloqueo->guardarDetalle($this->bloqueo->id_unico, $espacio[0], "NULL", "NULL", "NULL");
                }
            }
            echo json_encode(["res" => $data]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function validarNumero(){
        if(!empty($_REQUEST['clase'])){
            @session_start();
            $xTipo  = $this->bloqueo->obtenerPrimerTipoClase($_REQUEST['clase']);
            $numero = $this->bloqueo->validarConsecutivo($xTipo);
            echo $numero;
        }
    }

    public function obtenerNombreMov(){
        if(!empty($_REQUEST['clase'])){
            $xTipo      = $this->bloqueo->obtenerPrimerTipoClase($_REQUEST['clase']);
            $nombreTipo = $this->tipo->obtener(md5($xTipo));
            echo json_encode(["tipo" => $xTipo, "nombre" => $nombreTipo[0]]);
        }
    }

    public function formatearFechaHora($fecha){
        $ff = explode("/", $fecha);
        $xx = explode(" ", $ff[2]);
        $fecha = trim($xx[0])."-$ff[1]-$ff[0] $xx[1]";
        return $fecha;
    }

    public function fechaSinHora($fecha){
        $ff = explode("/", $fecha);
        $xx = explode(" ", $ff[2]);
        $fecha = trim($xx[0])."-$ff[1]-$ff[0]";
        return $fecha;
    }

    public function sumarDia($dia, $fecha){
        $date = strtotime("$dia", strtotime($fecha));
        return date("Y-m-d H:i", $date);
    }

    public function modificarM(){
        if(!empty($_REQUEST['mov'])){
            date_default_timezone_set('America/Bogota');
            $data = $this->bloqueo->modificarFecha( date("Y-m-d H:i") ,$_REQUEST['mov']);
            echo json_encode(["res" => $data]);
        }
    }
}
