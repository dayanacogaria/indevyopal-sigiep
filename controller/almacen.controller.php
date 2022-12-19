<?php
require_once './modelFactura/movimiento.php';
require_once './modelFactura/factura.php';
require_once './modelFactura/detallemovnto.php';
require_once './clases/almacen.php';
require_once './modelAlmacen/salida.php';
require_once './modelFactura/concepto.php';
require_once './modelAlmacen/inventario.php';
class almacenController{
    private $mov;
    private $fat;
    private $dtm;
    private $alm;
    private $sal;
    private $cpt;
    private $inv;

    public function __construct(){
        $this->mov = new movimiento();
        $this->fat = new factura();
        $this->dtm = new detallemovimiento();
        $this->alm = new almacen();
        $this->sal = new salida();
        $this->cpt = new concepto();
        $this->inv = new inventario();
    }

    public function index_entrada(){
        list(
                $idMov, $tipoMovimiento, $numero, $fecha, $centrocosto, $proyecto, $dependencia, $responsable,
                $descripcion, $porcIva, $tercero, $iva, $idasoc, $idaso, $tipoAsociado, $id_asoc, $tipoDocSoporte,
                $numDocSoporte, $descuento, $nomTipoAso, $nomTipoMov, $nomCentro, $nomProyecto, $nomDepencia,
                $nomResponsable, $nomTercero, $nomTipoDocSoporte, $id
            ) = array(
                0, 0, "", date("d/m/Y"), 0, 0, 0, 0, "", 0, 0, 0, "", 0, 0, "", 0, 0, 0, "", "", "", "", "", "",
                "", "", 0
        );

        if(!empty($_REQUEST['movimiento'])){
            $data  = $this->alm->obtenerDataMov($_REQUEST['movimiento']);
            $idMov = $_REQUEST['movimiento'];
            list(
                    $id, $tipoMovimiento, $nomTipoMov, $numero, $fecha, $centrocosto, $nomCentro, $proyecto, $nomProyecto,
                    $dependencia, $nomDepencia, $responsable, $nomResponsable, $descripcion, $tercero, $nomTercero, $porcIva,
                    $iva, $tipoDocSoporte, $nomTipoDocSoporte, $numDocSoporte, $descuento
                )
                = array(
                    $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9],
                    $data[10],$data[11],$data[12],$data[13],$data[14],$data[15],$data[16],$data[16],$data[17],$data[18],
                    $data[19],$data[20]
            );
        }
        $aso = $this->alm->obtenerAsociado(1);

        if(!empty($_REQUEST['asociado'])) {
            $id_asoc = $_REQUEST['asociado'];
            $data = $this->alm->obtenerDataMov($id_asoc);
            list(
                    $id_aso, $idaso, $tipoAsociado, $nomTipoAso, $fecha, $centrocosto, $nomCentro, $proyecto, $nomProyecto,
                    $iva, $porcIva, $tercero, $nomTercero, $descripcion, $tipoDocSoporte, $nomTipoDocSoporte, $numDocSoporte,
                    $descuento
                )
                = array(
                $data[0], $data[0], $data[1], $data[2], $data[4], $data[5], $data[6], $data[7], $data[8], $data[16], $data[16],
                $data[14],$data[15],$data[13],$data[17],$data[18],$data[19],$data[20]
            );
        }

        require_once './RF_ENTRADA_ALMACEN.php';
    }

    public function BuscarUnidadElemento(){
        if(!empty($_REQUEST['elemento'])){
            $data = $this->fat->obtenerTarifasElementosD($_REQUEST['elemento']);
            $html = "<option value=''>Unidad</option>";
            foreach ($data as $row){
                $html .= "<option value='$row[4]'>$row[5]</option>";
            }
            echo $html;
        }
    }

    public function registrarDetalle(){
        @session_start();
        $mov       = $_REQUEST['txtIdMov'];
        $plan      = $_REQUEST['sltPlanInv'];
        $unidad    = $_REQUEST['sltUnidad'];
        $cantidad  = $_REQUEST['txtCantidad'];
        $valor     = $_REQUEST['txtValor'];
        $valorA    = $_REQUEST['txtAjuste'];
        $descuento = !empty($_REQUEST['txtXDescuento'])?$_REQUEST['txtXDescuento']:'0';
        $factor    = $this->mov->obtenerFactorElemento($plan, $unidad);
        $xxx       = $cantidad * $factor;
        $valorX    = ($valor / $factor);
        $iva       = ($_REQUEST['txtValorIva'] / $factor);
        $data      = $this->dtm->guardarData($xxx, $valorX, $iva, $mov, $plan, $unidad, $unidad, $valorA, $descuento, $valor);
        if($data == true){
            $dtm  = $this->dtm->obtenerUltimoRegistro($mov);
            $xant = $this->inv->obtenerDetalleAnteriorEntrada($plan, $unidad);
            if(!empty($xant[1])){
                $dataX = $this->fat->obtenerTarifasElementosD($plan);
                if(count($dataX) > 0){
                    foreach ($dataX as $rowX){
                        $xant = $this->inv->obtenerDetalleAnteriorEntrada($plan, $rowX[4]);
                        if($rowX[7] != NULL){
                            if($valorX > $xant[1]){
                                $precio_ant = $rowX[1];
                                $precio_act = $rowX[1] + (($rowX[1] * $rowX[7]) / 100);
                                $this->fat->actualizarValorTarifa($rowX[8], $precio_act);
                            }else{
                                $precio_ant = $xant[1];
                                $precio_act = $rowX[1];
                            }
                        }else{
                            if(empty($xant[1])){
                                $precio_ant = $rowX[1];
                                $precio_act = $rowX[1];
                            }else{
                                $precio_ant = $xant[1];
                                $precio_act = $rowX[1];
                            }
                        }
                        date_default_timezone_set('America/Bogota');
                        $this->fat->GuardarFacturaPrecio($dtm, $rowX[0], $rowX[4], $precio_ant, $precio_act, 1, date("Y-m-d"), $_SESSION['usuario_tercero']);
                    }
                }
            }
        }
        $url       = "access.php?controller=almacen&action=Index_Entrada&movimiento=".md5($mov);
        require_once './vistas/respuesta/index.php';
    }

    public function ventana_coste(){
        $tipo = $this->mov->obtenerTipoMovClase(3);
        require_once './vistas/almacen/coste.php';
    }

    public function validarCoste(){
        try {
            $fechaI = $this->mov->formatearFecha($_REQUEST['txtFechaI'], "/");
            $fechaF = $this->mov->formatearFecha($_REQUEST['txtFechaF'], "/");
            $xxD    = $this->mov->buscarElementosTF($_REQUEST['sltElementoI'], $_REQUEST['sltElementoF']);
            $x      = 0;
            foreach ($xxD as $rowX){
                $data   = $this->mov->buscarMovimientoFechaTipo($fechaI, $fechaF, $rowX[0]);
                $this->mov->actualizarData($rowX[0]);
                list($xsaldo, $xvalor) = array(0, 0);
                foreach($data as $row){
                    $xxx    = 0;
                    switch ($row[3]) {
                        case 2:
                        case 5:
                            $xsaldo += $row[1];
                            $xvalor += $row[2];
                            break;

                        case 3:
                        case 7:
                            $xvc = $xsaldo - $row[1];
                            if(!empty($xsaldo) || !empty($xvalor)){
                                $xxx = ((( $xvalor / $xsaldo ) * 1 ) / 1 );
                            }
                            if(empty($xsaldo)){
                                $xxx = $this->mov->buscarValorMaximoElemento($rowX[0]);
                            }
                            if($xvc < 0){
                                $xxx = $this->mov->buscarValorMaximoElemento($rowX[0]);
                            }
                            $resx    = $this->mov->actualizarValorDetalleMov($row[6], $xxx);
                            if($resx == true){ $x++; }
                            $xsaldo -= $row[1];
                            $xvalor -= ($xxx * $row[1]);
                            break;
                    }
                }
            }

            if(!empty($x)){
                $data = true;
            }else{
                $data = false;
            }
            $url = "access.php?controller=Almacen&action=ventana_coste";
            require_once './vistas/respuesta/index.php';
        } catch (Exception $e) {}
    }

    public function GuardarMov(){
        try {
            session_start();
            $ff = explode('/', $_REQUEST['txtFecha']);
            $fecha = "$ff[2]-$ff[1]-$ff[0]";
            $data = $this->alm->GuardarMov(
                $_REQUEST['sltTipoMovimiento'], $_REQUEST['txtNumeroMovimiento'], $fecha, $_REQUEST['sltCentroCosto'],
                $_REQUEST['sltProyecto'], $_REQUEST['sltDependencia'], $_REQUEST['sltResponsable'], $_REQUEST['sltTercero'], $_REQUEST['txtIva'],
                $_REQUEST['sltDocSoporte'], $_REQUEST['txtNumDocS'], $_REQUEST['txtDescuento'], $_REQUEST['txtDescripcion'],
                $_SESSION['compania'], $_SESSION['anno']
            );
            $id = $this->mov->obtnerUltimoRegistro($_REQUEST['sltTipoMovimiento']);
            if($data == true){
                if(!empty($_REQUEST['sltNumeroA'])){
                    $dta_i = $this->alm->getData($_REQUEST['sltNumeroA']);
                    if(count($dta_i) > 0){
                        foreach ($dta_i as $row){
                            $valores = $this->alm->get_values_detail($row[0]);
                            $dataAso = $this->alm->obtnerDataAsociado($row[0]);
                            $xc = 0;
                            foreach ($dataAso as $rc) {
                                $xc += $rc[0];
                            }
                            $xxx = $valores[1] - $xc;
                            if($xxx > 0){
                                $this->alm->save_detail_mov($valores[0], $xxx, $valores[2], $valores[3], $id, $row[0]);
                            }
                        }
                    }
                }
            }
            $url = "access.php?controller=almacen&action=Index_Entrada&movimiento=".md5($id);
            require_once './vistas/respuesta/index.php';
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function ObtenerValorCoste(){
        $elemento = $this->cpt->obtnerConceptoPlanI($_REQUEST['concepto']);
        if(!empty($elemento)){
            //$factor   = $this->factura->obtenerUnidadFactor($_REQUEST['sltUnidad'], $_REQUEST['sltConcepto']);
            $xsaldoV  = $this->dtm->obtenerSaldoPlan($elemento);
            $xsaldoC  = $this->dtm->obtnerCantidadPlan($elemento);

            if(empty($xsaldoC) && empty($xsaldoV)){
                $xvalor = 0;
            }
            if(!empty($xsaldoV) || !empty($xsaldoC)){
                $xvalor = ((( $xsaldoV / $xsaldoC ) * 1 ) / 1 );
            }

            if($xsaldoV < 0){
                $xvalor = 0;
            }

            echo round($xvalor, 0);
        }
    }

    public function vistaCambioPrecios(){
        if(!empty($_REQUEST['txtFechaI']) && !empty($_REQUEST['txtFechaF'])){
            $ff = explode('/', $_REQUEST['txtFechaI']); $fechaI = "$ff[2]-$ff[1]-$ff[0]";
            $fx = explode('/', $_REQUEST['txtFechaF']); $fechaF = "$fx[2]-$fx[1]-$fx[0]";
            $data = $this->fat->obtenerListadoPreciosFecha("1, 2, 3", $fechaI, $fechaF);
        }else{
            $data = $this->fat->obtenerListadoPrecios(1);
        }
        require './vistas/inventario/semaforo.php';
    }

    public function vistaValidarCantidad(){
        require './vistas/almacen/cantidad.php';
    }

    public function validarCantidad(){
        $fechaI = $this->mov->formatearFecha($_REQUEST['txtFechaI'], "/");
        $fechaF = $this->mov->formatearFecha($_REQUEST['txtFechaF'], "/");
        $dataX  = $this->mov->buscarElementosTF($_REQUEST['sltElementoI'], $_REQUEST['sltElementoF']);
        $x      = 0;
        foreach ($dataX as $rowX){
            $data = $this->mov->buscarMovimientoFechaTipoFactura($fechaI, $fechaF, $rowX[0]);
            foreach($data as $row){
                if(!empty($row[2])){
                    $xxx = $this->mov->obtenerFactorElemento($row[1], $row[2]);
                    if(!empty($xxx)){
                        $xf = $row[3] * $xxx;
                        $res = $this->mov->actualizarCantidadDetalleMov($row[0], $xf);
                        if($res == true){
                            $x++;
                        }
                    }
                }
            }
        }
        if(!empty($x)){
            $data = true;
        }else{
            $data = false;
        }
        $url = "access.php?controller=Almacen&action=vistaValidarCantidad";
        require_once './vistas/respuesta/index.php';
    }
}