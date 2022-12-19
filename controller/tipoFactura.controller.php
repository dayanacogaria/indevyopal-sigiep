<?php
require_once './modelFactura/tipoFactura.php';

class tipoFacturaController{
    private $tipoFactura;
    public function __construct(){
        $this->tipoFactura = new tipoFactura();
    }

    public function registrar(){
        if(
            !empty($_REQUEST['txtNombre']) && !empty($_REQUEST['txtPrefijo']) && !empty($_REQUEST['OptServ'])
          ){

            $tpf = new tipoFactura();

            $nombre   = $_REQUEST['txtNombre'];
            $prefijo  = $_REQUEST['txtPrefijo'];
            $servicio = $_REQUEST['OptServ'];

            if(empty($_REQUEST['sltTipoC'])){
                $tipoCom = "NULL";
            }else{
                $tipoCom = $_REQUEST['sltTipoC'];
            }

            if(empty($_REQUEST['sltTipoMov'])){
                $tipoMov = "NULL";
            }else{
                $tipoMov = $_REQUEST['sltTipoMov'];
            }

            if(empty($_REQUEST['sltClaseF'])){
                $claseF = "NULL";
            }else{
                $claseF = $_REQUEST['sltClaseF'];
            }

            $tpf->nombre           = $nombre;
            $tpf->prefijo          = $prefijo;
            $tpf->servicio         = $servicio;
            $tpf->tipo_comprobante = $tipoCom;
            $tpf->tipo_movimiento  = $tipoMov;
            $tpf->clase_factura    = $claseF;

            $res = $this->tipoFactura->registrar($tpf);

            if($res == true){
                header('Location:json/registrarTipoFacturaJson.php?action=registrado');
            }else{
                header('Location:json/registrarTipoFacturaJson.php?action=noregistrado');
            }
        }
    }

    public function eliminar(){
        if(!empty($_REQUEST['id_unico'])){
            $res = $this->tipoFactura->eliminar($_REQUEST['id_unico']);
            echo json_encode($res);
        }
    }

    public function modificar(){
        if(
            !empty($_REQUEST['txtNombre']) && !empty($_REQUEST['txtPrefijo']) && !empty($_REQUEST['OptServ']) &&
            !empty($_REQUEST['id'])
          ){
            $tpf = new tipoFactura();

            $nombre   = $_REQUEST['txtNombre'];
            $prefijo  = $_REQUEST['txtPrefijo'];
            $servicio = $_REQUEST['OptServ'];
            $id_unico = $_REQUEST['id'];
            if(empty($_REQUEST['sltTipoC'])){
                $tipoCom = "NULL";
            }else{
                $tipoCom = $_REQUEST['sltTipoC'];
            }

            if(empty($_REQUEST['sltTipoMov'])){
                $tipoMov = "NULL";
            }else{
                $tipoMov = $_REQUEST['sltTipoMov'];
            }

            if(empty($_REQUEST['sltClaseF'])){
                $claseF = "NULL";
            }else{
                $claseF = $_REQUEST['sltClaseF'];
            }

            $tpf->nombre           = $nombre;
            $tpf->prefijo          = $prefijo;
            $tpf->servicio         = $servicio;
            $tpf->tipo_comprobante = $tipoCom;
            $tpf->tipo_movimiento  = $tipoMov;
            $tpf->clase_factura    = $claseF;
            $tpf->id_unico         = $id_unico;

            $res = $this->tipoFactura->modificar($tpf);

            if($res == true){
                header('Location:json/registrarTipoFacturaJson.php?action=modificado');
            }else{
                header('Location:json/registrarTipoFacturaJson.php?action=nomodificado');
            }
        }
    }
}