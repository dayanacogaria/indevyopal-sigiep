<?php

require_once '../Conexion/conexion.php';
session_start();
#@Autor:Alexander
#Este formulario generara la consulta que permitira generar nuevos consecutivos para los archivos
#Se inicia en dos porque ya existe comprobante
##############################################################################################################################################################
# Modificaciones
# 16/02/2017 | Jhon Numpaque
# Descripción : Se agrego validación en los casos 4 y 5 para asegurar impresión del valor retornado
##############################################################################################################################################################
# 15/02/2017 | Jhon Numpaque
# Descripción : Se cambio consulta en el caso 5 para obtener el ultimo valor
$nuevos = $_POST['nuevos'];
switch ($nuevos) {
    case 2:
        $numero = $_POST['numero'];
        if(!empty($numero)){
            if ($numero != '""') {
                $sqlC = "SELECT COUNT(id_unico) FROM gp_factura where tipofactura = $numero";
                $rs = $mysqli->query($sqlC);
                $n = mysqli_fetch_row($rs);
                if ($n[0] == 0) {
                    $numero = date('Y') . '000001';
                } else {
                    $sql = "SELECT MAX(numero_factura) FROM gp_factura where tipofactura = $numero";
                    $result = $mysqli->query($sql);
                    $num = mysqli_fetch_row($result);
                    $numero = $num[0] + 1;
                }

                echo $numero;
            }
        }
        break;
    case 3:
        $numero = $_POST['numero'];
        if(!empty($numero)){
            if($numero != '""'){
                $sqlP = "SELECT COUNT(id_unico) FROM gp_pago WHERE tipo_pago = $numero";
                $rs = $mysqli->query($sqlP);
                $num = mysqli_fetch_row($rs);
                if($num[0]==0){
                    $numero = date('Y') . '000001';
                }else{
                    $sql = "SELECT MAX(numero_pago) FROM gp_pago where tipo_pago=$numero";
                    $result = $mysqli->query($sql);
                    $nro = mysqli_fetch_row($result);
                    $numero = $nro[0] + 1;
                }

                echo $numero;
            }
        }
        break;
    case 4:
        $numero1 = 0;
        $tipoD = $_POST['tipoDocumento'];
        if(!empty($tipoD)){
            $sql1 = "SELECT COUNT(id_unico) FROM gf_detalle_comprobante_mov WHERE tipodocumento=$tipoD";
            $rs1= $mysqli->query($sql1);
            $num1 = mysqli_fetch_row($rs1);
            $filas1 =  mysqli_num_rows($rs1);
            if($filas1==0){
                $numero1 = 0;
            }else{
                if($num1[0]==0){
                    $numero1 = date('Y').'000001';
                }else{
                    $sql2 = "SELECT MAX(numero) FROM gf_detalle_comprobante_mov WHERE tipodocumento=$tipoD";
                    $result1 = $mysqli->query($sql2);
                    $num2 = $result1->fetch_row();
                    $numero1 = $num2[0]+1;
                }
            }
        }
        echo $numero1;
        break;
    case 5:
        $tipo = $_POST['tipo'];
        if(!empty($tipo)){
            $ano = date('Y');
            $sqlC="SELECT COUNT(id_unico) FROM gf_comprobante_cnt WHERE tipocomprobante=$tipo";
            $rs = $mysqli->query($sqlC);
            $n = mysqli_fetch_row($rs);
            if ($n[0]==0) {
                $numero = date('Y').'000001';
            }else{
                $sql = "SELECT (numero) FROM gf_comprobante_cnt WHERE tipocomprobante=$tipo ORDER BY id_unico DESC LIMIT 1";
                $result =$mysqli->query($sql);
                $num = mysqli_fetch_row($result);
                $numero = $num[0]+1;
            }
            echo json_encode($numero);
        }
        break;
    case 6:
        $numero   = 0;
        $tipo     = $_POST['tipo'];
        $param    = $_SESSION['anno'];
        $compania = $_SESSION['compania'];
        if(!empty($tipo)){
            $str     = "SELECT MAX(numero) FROM gf_movimiento WHERE tipomovimiento = $tipo AND parametrizacionanno = $param AND compania = $compania";
            $result2 = $mysqli->query($str);
            $num2    = mysqli_fetch_row($result2);
            if(!empty($num2[0])){
                $numero = $num2[0] + 1;
            }else{
                $strX   = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $param";
                $resX   = $mysqli->query($strX);
                $rowX   = mysqli_fetch_row($resX);
                $numero = $rowX[0]."000001";
            }
            echo $numero;
        }else{
            echo '';
        }
        break;
}
?>