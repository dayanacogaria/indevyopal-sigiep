<?php
require_once ('./modelAlmacen/traslado.php');
require_once ('./modelAlmacen/detallemovimiento.php');
require_once ('./modelAlmacen/reintegro.php');
class trasladoController{
    private $traslado;
    private $detallemov;
    private $reintegro;

    public function __construct(){
        $this->traslado   = new traslado();
        $this->detallemov = new detallemov();
        $this->reintegro  = new reintegro();
    }

    public function consecutivo(){
        if(!empty($_REQUEST['tipo'])){
            @session_start();
            $data = $this->traslado->generarConsecutivo($_REQUEST['tipo'], $_SESSION['anno'], $_SESSION['compania']);
            echo json_encode($data);
        }
    }

    public function cargarBusqueda(){
        $html     = "";
        $html     .= "<option value=''>Buscar</option>";
        $data     = $this->traslado->obnterMovsB($_REQUEST['compania'], $_REQUEST['param']);
        foreach ($data as $row) {
            $valorDetalle = $this->traslado->valorAcumMov($row[0]);
            $html .= "<option value=\"".$row[0]."\">".$row[1]." ".$row[2]." $".number_format($valorDetalle,2,',','.')."</option><br/>";
        }
        echo $html;
    }

    public function registrar(){
        $tra = new reintegro();
        $dtm = new detallemov();

        $ff    = explode("/", $_REQUEST['fecha']);
        $fecha = "$ff[2]-$ff[1]-$ff[0]";

        $tra->tipomovimiento = $_REQUEST['tipoT'];
        $tra->numero         = $_REQUEST['numero'];
        $tra->compania       = $_REQUEST['compania'];
        $tra->param          = $_REQUEST['paramA'];
        $tra->dependencia    = $_REQUEST['dependencia'];
        $tra->responsable    = $_REQUEST['responsable'];
        $tra->centrocosto    = 12;
        $tra->proyecto       = 2147483647;
        $tra->estado         = 2;
        $tra->fecha          = $fecha;

        $res = $this->reintegro->registrar($tra);
        if($res == true){
            $mov = $this->reintegro->ObtenerUltimoMov($tra->tipomovimiento);
            $por = $this->reintegro->obtnerPorcentaje();
            $this->reintegro->actualizarReintegro($por, $mov);
            $xxx = explode(",",$_POST['markeds']);
            for ($a = 0; $a < count($xxx); $a++) {
                $x = 0;
                $data = explode("-", $xxx[$a]);
                list($dtmv, $pro) = array($data[0], $data[1]);
                $asoc = $this->reintegro->buscarAsociadoMov($dtmv, $mov);

                if(empty($asoc[0])){
                    $x++;
                    $values = $this->detallemov->obtnerDatosDetalle($dtmv);
                    $iva    = (($x * $values[3]) * $por) / 100;

                    $dtm->cantidad        = $x;
                    $dtm->valor           = $values[3];
                    $dtm->iva             = $iva;
                    $dtm->movimiento      = $mov;
                    $dtm->detalleasociado = $values[0];
                    $dtm->planmovimiento  = $values[1];

                    $rmov = $this->detallemov->registrar($dtm);

                    if($rmov == true){
                        $dtmvn = $this->detallemov->ultimoRegistro($mov);
                        $this->detallemov->mov_pro($pro, $dtmvn[0]);
                    }
                }else{
                    $cant = $asoc[2] + 1;
                    $iva  = (($asoc[1] * $cant) * $por) / 100;
                    $this->detallemov->modificar($cant, $iva, $asoc[0]);
                    $this->detallemov->mov_pro($pro, $asoc[0]);
                }
            }
        }
        echo json_encode($res);
    }

    public function eliminar(){
        if(!empty($_REQUEST['detalle'])){
            $res = $this->traslado->eliminarRelMovP($_REQUEST['detalle'], $_REQUEST['producto']);
            if($res == true){
                $de = $this->traslado->eliminar($_REQUEST['detalle']);
                echo json_encode($de);
            }else{
                echo json_encode(false);
            }
        }
    }
}