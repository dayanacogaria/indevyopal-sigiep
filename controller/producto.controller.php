<?php
require_once ('./modelAlmacen/productoDpr.php');

class productoController{

    private $producto;

    public function __construct(){
        $this->producto = new productoDpr();
    }

    public function Index(){
        require_once ('modificar_GF_PRODUCTO.php');
    }

    public function actualirDatosD(){
        $pro = new productoDpr();
        $pro->id_unico            = $_REQUEST['sltProductos'];
        $pro->meses               = $_REQUEST['txtMeses'];
        $pro->vida_util_remanente = $_REQUEST['txtVidaUtil'];
        $ff = explode("/" ,$_REQUEST['txtFecha']);
        $fecha = "$ff[2]-$ff[1]-$ff[0]";
        $pro->fecha = $fecha;
        $this->producto->actDatosDepreciacion($pro);
        header('Location:jsonAlmacen/json_res_pro.php?action=registrado');
    }
}