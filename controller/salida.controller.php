<?php
require_once ('./modelAlmacen/salida.php');
/**
 * Control de Salida
 */
class salidaController{

    private $salida;

    public function __construct(){
        $this->salida = new salida();
    }

    public function obtnerCantidadPlan(){
        if($_REQUEST['sltElemento']){
            $xe = $this->salida->obtnerCantidadProductosPlan($_REQUEST['sltElemento']);
            $xs = $this->salida->obtnerCantidadProductosPlanSalida($_REQUEST['sltElemento']);
            $cantidad = $xe - $xs;
            echo json_encode($cantidad);
        }else{
            echo json_encode(0);
        }
    }

    public function obnterValorU(){
        if($_REQUEST['sltElemento']){
            $valor = $this->salida->obtnerValorProductoPlan($_REQUEST['sltElemento']);
            echo json_encode($valor);
        }else{
            echo json_encode(0);
        }
    }

    public function guardar_detalle(){
        $elemento  = $_REQUEST['sltElemento'];
        $cantidad  = $_REQUEST['txtCantI'];
        $data_sld  = $this->salida->buscarDatosSalida($_REQUEST['id_mov']);
        $res       = $this->salida->guardarDetalleSalida($cantidad, $_REQUEST['txtValorU'], 0, $data_sld[0], 'NULL', $_REQUEST['sltElemento']);
        if($res == true){
            header('location:./jsonAlmacen/json_mov_almacen.php?action=registrado&mov='.$_REQUEST['id_mov']);
        }else{
            header('location:./jsonAlmacen/json_mov_almacen.php?action=noregistrado&mov='.$_REQUEST['id_mov']);
        }
    }
}