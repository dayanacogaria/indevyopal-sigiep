<?php 
    #Cargamos la clase conexión
    require_once 'Conexion/conexion.php';
    #iniciamos la sesión
    session_start();	
    #validación de dato no nulo ni vació    
    $codi = (string) $_POST["code"]; 
    $IDpadre = 0;
    $tipCla = 0;
    $tipoClase = '';

    if($codi != 0)
    {
        $retirar = (strrpos($codi, '0', 0)); 
        $padre = substr($codi, 0, $retirar);

        $sqlPadre = "SELECT DISTINCT id_unico,                        
                    codi_presupuesto,
                    nombre,
                    tipoclase 
        FROM gf_rubro_pptal 
        WHERE codi_presupuesto = $padre ORDER BY codi_presupuesto ASC";

        $resultadoPadre = $mysqli->query($sqlPadre);
        $rowPadre = mysqli_fetch_row($resultadoPadre);
        $IDpadre = $rowPadre[0];
        $tipCla = $rowPadre[3];
    }

    if($IDpadre == 0)
        echo '<option value="0" selected="selected">Predecesor</option>';
    else
    {
        $tipoClase = " AND tipoclase = $tipCla ";
        echo '<option value="'.$rowPadre[0].'" selected="selected">'.$rowPadre[1] .' - '. ucwords((mb_strtolower($rowPadre[2]))) .'</option>';
        echo '<option value="0">Predecesor</option>'; 
    }
            
    echo $sql = "SELECT DISTINCT id_unico, codi_presupuesto, nombre 
        FROM gf_rubro_pptal 
        WHERE movimiento = 2 
        $tipoClase 
        AND id_unico != '$IDpadre' ORDER BY codi_presupuesto ASC";

    $resultado = $mysqli->query($sql);
    while ($row = mysqli_fetch_row($resultado))
    {
        echo '<option value="'.$row[0].'">'.$row[1] .' - '. ucwords((mb_strtolower($row[2]))) .'</option>';            
    }
    echo '<option value="0">Predecesor</option>';  
    
 ?>