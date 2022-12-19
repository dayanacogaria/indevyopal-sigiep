<?php
    
    session_start();
   
    
    function CalcularPrima($empleado,$concepto,$periodo,$proceso){
        
        require '../Conexion/conexion.php';
       
        $anno = $_SESSION['anno'];
        $Fperiodo = "SELECT fechafin, fechainicio FROM gn_periodo WHERE id_unico= '$periodo'";
        $Fecha = $mysqli->query($Fperiodo);
        $FechaP = mysqli_fetch_row($Fecha);
  
        $fecha = "SELECT * from gf_parametrizacion_anno where id_unico = '$anno'";
        $res = $mysqli->query($fecha);
        $row = mysqli_fetch_row($res);
        $FN = Annos($FechaP[1], 1);
  
        $mes = MesesFecha($FN, 1);
       
                
        $consulta = "SELECT SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
              . "WHERE n.concepto = '$concepto'  AND n.empleado = '$empleado' AND c.clase = 1 AND c.unidadmedida = 1 "
                . "AND n.fecha BETWEEN '$mes' AND '$FechaP[0]' ";
        $rescon = $mysqli->query($consulta);
        $valor = mysqli_fetch_row($rescon);
        
        $Total = $valor [0] / 12;
        
        return $Total;
    }
    
    
    
?>