<?php

/**
 * Created by PhpStorm.
 * User: SERVIDOR
 * Date: 29/08/2018
 * Time: 10:50
 */
require_once './clases/tarifa.php';
require_once './clases/tipoEspacio.php';

class tarifaController {

    private $tar;
    private $spc;

    public function __construct() {
        $this->tar = new tarifa();
        $this->spc = new tipoEspacio();
    }

    public function index() {
        $tipo = $this->tar->listarTipoTarifa();
        if (!empty($_REQUEST['sltTipoFactura'])) {
            $data = $this->tar->listarDataXTipoTarifa($_REQUEST['sltTipoFactura']);
        } else {
            $data = $this->tar->listarData();
        }
        require_once './LISTAR_GP_TARIFA.php';
    }

    public function registrar() {
        $tipo = $this->tar->listarTipoTarifa();
        require_once './Registrar_GP_TARIFA.php';
    }

    public function guardar() {
        if (!empty($_REQUEST['txtIva'])) {
            $txtIva = $_REQUEST['txtIva'];
        } else {
            $txtIva = 0;
        }
        if (!empty($_REQUEST['txtImpo'])) {
            $txtImpo = $_REQUEST['txtImpo'];
        } else {
            $txtImpo = 0;
        }
        if (!empty($_REQUEST['txtValorI'])) {
            $txtValorI = $_REQUEST['txtValorI'];
        } else {
            $txtValorI = 0;
        }
        if (!empty($_REQUEST['txtValorS'])) {
            $txtValorS = $_REQUEST['txtValorS'];
        } else {
            $txtValorS = 0;
        }
        $data = $this->tar->registrarTarifa(
                $_REQUEST['txtNombre'], $_REQUEST['sltTipoTarifa'], $_REQUEST['valor'], $txtValorI, $txtValorS, $txtIva, $txtImpo);
        $url = "access.php?controller=Tarifa&action=Index";
        require_once './vistas/respuesta/index.php';
    }

    public function editar() {
        $id = $_GET['codigo'];
        $data = $this->tar->obtenerData($_GET['codigo']);
        $nombre = $id_tipo = $nom_tipo = $valor = $valor_inf = $valor_sup = $iva = $impo = '';
        if (count($data) > 0) {
            $nombre = $data[1];
            $id_tipo = $data[2];
            $nom_tipo = $data[3];
            $valor = $data[4];
            $valor_inf = $data[5];
            $valor_sup = $data[6];
            $iva = $data[7];
            $impo = $data[8];
        }
        $tipos = $this->tar->obtenerTiposDiferentes($id_tipo);
        require_once './Modificar_GP_TARIFA.php';
    }

    public function actualizar() {
        if (!empty($_REQUEST['txtIva'])) {
            $txtIva = $_REQUEST['txtIva'];
        } else {
            $txtIva = 0;
        }
        if (!empty($_REQUEST['txtImpo'])) {
            $txtImpo = $_REQUEST['txtImpo'];
        } else {
            $txtImpo = 0;
        }
        if (!empty($_REQUEST['txtValorI'])) {
            $txtValorI = $_REQUEST['txtValorI'];
        } else {
            $txtValorI = 0;
        }
        if (!empty($_REQUEST['txtValorS'])) {
            $txtValorS = $_REQUEST['txtValorS'];
        } else {
            $txtValorS = 0;
        }
        $data = $this->tar->modificarTarifa($_REQUEST['id'], $_REQUEST['txtNombre'], $_REQUEST['sltTipoTarifa'], $_REQUEST['valor'], $txtValorI, $txtValorS, $txtIva, $txtImpo);
        $url = "access.php?controller=Tarifa&action=Index";
        require_once './vistas/respuesta/edit.php';
    }

    public function eliminar() {
        echo json_encode($this->tar->eliminar($_REQUEST['id']));
    }
    
    public function guardarAsociado(){
        $id = $_REQUEST['idtarifa'];
        $tarifa = $_REQUEST['tarifa'];
        $cantidad = $_REQUEST['txtcantidad'];
        $data = $this->tar->registrarAsociado($id, $tarifa, $cantidad);
        $url = "GP_TARIFA_ASOCIADO.php?id=".md5($id);
        require_once './vistas/respuesta/index.php';   
    }    
    
    public function modificarAsociado(){
        $id = $_REQUEST['id'];
        $tarifa = $_REQUEST['tarifa'];
        $cantidad = $_REQUEST['cantidad'];
        echo json_encode($this->tar->modificarAsociado($id, $tarifa, $cantidad));
    }
    
    public function eliminarAsociado(){
        $id = $_REQUEST['id'];
        echo json_encode($this->tar->eliminarAsociado($id));
    }
    
    public function VistaTipoEspacioTarifa() {
        $xtipoE = $this->spc->obtenerAll();
        require_once './vistas/tarifa/index.php';
    }

    public function RegistrarTipoEspacioTarifa() {
        $xtipoE = $this->spc->obtenerListado();
        $xtipoE = $xtipoE->fetch_all(MYSQLI_NUM);
        $xtar = $this->tar->obtenerListado();
        require './Conexion/conexion.php';
        require './head.php';
        require_once './vistas/tarifa/asignar.tipo.php';
    }

    public function registrarRelacionTarifa() {
        $tipo = ($_REQUEST['tipo']);
        $tarifas = ($_REQUEST['tarifas']);
        $reponse = "";
        foreach ($tarifas as $value) {
            $this->tar->registrarEspacioTarifa($tipo, $value);
            $reponse ++;
        }
        if ($reponse > 0) {
            echo 1;
        } else {
            echo 2;
        }
    }

    public function actualizarTipoEspacioTarifa() {

        $tipoEid = $this->spc->obtenertp($_REQUEST['id']);
        $tipoEid = $tipoEid->fetch_all(MYSQLI_NUM);
        $idEspacio = $tipoEid[0][0];
        $tipoEDiff = $this->spc->obtenerDiferentestf($_REQUEST['id']);
        $tipoEDiff = $tipoEDiff->fetch_all(MYSQLI_NUM);
        $tpetfid = $this->tar->obtenerListadoTipoETarifa($_REQUEST['id']);
        $tpetfid = $tpetfid->fetch_all(MYSQLI_NUM);
        $idin = "";
        foreach ($tpetfid as $row) {
            $idin = $idin . "," . $row[0];
        }
        $idin = substr($idin, 1);
        $tpetfdiff = $this->tar->obtenerListadoTarifaDiff($idin);
        $tpetfdiff = $tpetfdiff->fetch_all(MYSQLI_NUM);

        require_once './vistas/tarifa/editarasignar.tipo.php';
    }

    public function editarRelacionTarifa() {
        $tipo = ($_REQUEST['tipo']);
        $tarifas = ($_REQUEST['tarifas']);
        $espacio = $_REQUEST['espacio'];
        $idin = "";
        foreach ($tarifas as $row) {
            $idin = $idin . "," . $row;
        }
        $idin = substr($idin, 1);
        $response = "";
        foreach ($tarifas as $row) {
            $listeditar = $this->tar->obtenerListadoeditar($espacio, $row);
            if ($listeditar) {
                
            } else {
                $this->tar->registrarEspacioTarifa($espacio, $row);
            }
            $response++;
        }
        $this->tar->eliminarTipoETarifa($espacio, $idin);
        if ($response > 0) {
            echo 1;
        } else {
            echo 2;
        }
    }

    public function Eliminartpt() {
        echo json_encode($this->tar->eliminartpt($_REQUEST['id']));
    }
    
    public function obtenerTarifaConepto(){
        $response = $this->tar->obtenerTarifaConepto($_REQUEST['concepto']);
        echo json_encode($response);
    }           
    
    public function obtenerTarunico(){
        $response = $this->tar->obtenerTarunico($_POST['id']);
        echo json_encode($response);
    }
    
    public function obtenerTarifaTipoEspacio(){
        $response = $this->tar->obtenerTarifaTipoEspacio($_POST['id']);
        echo json_encode($response);
    }
    
    public function validarSuma(){
        $id = $_GET["id"];
        $nueva  = $_GET["nueva"];
        $cantidad  = $_GET["cantidad"];
        $asociado = $this->tar->validarSuma($id);
        $ntar = $this->tar->validarSumaNueva($nueva);
        $suma = ($ntar[0] * $cantidad) + $asociado[0];
        if($suma > $asociado[1]){
            // no registra
            echo 0;
        }else{
            // registra
            echo 1;
        }
    }
    
    public function validarSumamodificar(){
        $id = $_GET["id"];
        $nueva  = $_GET["nueva"];
        $cantidad  = $_GET["cantidad"];
        $aso  = $_GET["aso"];
        $asociado = $this->tar->validarSumamodificar($id, $aso);
        $ntar = $this->tar->validarSumaNueva($nueva);
        $suma = ($ntar[0] * $cantidad) + $asociado[0];
        if($suma > $asociado[1]){
            // no registra
            echo 0;
        }else{
            // registra
            echo 1;
        }
    }    
}
