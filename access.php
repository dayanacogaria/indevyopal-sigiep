<?php
require_once 'Conexion/conexion.php';
$controller = "predio";
if(!isset($_REQUEST['controller'])){
    require_once "controller/$controller.controller.php";
    $controller = ucwords($controller).'controller';
    $controller = new $controller;
    $controller->Index();
}else{
    $controller = strtolower($_REQUEST['controller']);
    $action     = isset($_REQUEST['action'])? $_REQUEST['action'] : 'Index';
    require_once "controller/$controller.controller.php";
    $controller = ucwords($controller)."controller";
    $controller = new $controller;
    call_user_func(array($controller, $action));
}