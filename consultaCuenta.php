<?php
#Llamamos a la clase de conexión
require_once ('../Conexion/conexion.php');
#definimos la sesion
session_start();
$param = $_SESSION['anno'];
#Captura de dato y conversion a string para conteo de caracteres
$codigo = (string) $_POST["codigo"];
#usamos la funcion strlen para contar los datos
$cant = strlen($codigo);
#intento uno de cambio para generar un conteo de datos
#Se usa un ciclo el cual toma la cantidad de caracteres 
#como avance y generamos la consulta por similitud
if ($cant == 1) {
    $sql = "SELECT DISTINCTROW
                   PADRE.id_unico,                        
                   PADRE.codi_cuenta,
                   PADRE.nombre
            FROM
                   gf_cuenta PADRE
            LEFT JOIN   
                   gf_cuenta HIJO
            ON
                   PADRE.id_unico = HIJO.predecesor 
            WHERE PADRE.parametrizacionanno = $param";
        $resultado = $mysqli->query($sql);
        echo '<option value="">Predecesor</option>';
        while ($row = mysqli_fetch_row($resultado)){
           echo '<option value="'.$row[0].'">'.$row[1] .'-'. ucwords((mb_strtolower($row[2]))) .'</option>';            
        }
    }else{
        $ctn = 0;
        for($i = 0;$i <=$cant;$i++){
            for($a = 0;$a<=$i;$a++){
                $men = substr($codigo,0, $a-1);
                $sql = "SELECT DISTINCTROW
                               PADRE.id_unico,                        
                               PADRE.codi_cuenta,
                               PADRE.nombre 
                        FROM
                               gf_cuenta PADRE
                        LEFT JOIN   
                               gf_cuenta HIJO
                        ON
                               PADRE.id_unico = HIJO.predecesor
                        WHERE
                               PADRE.codi_cuenta LIKE '%$men%' AND PADRE.parametrizacionanno = $param";
                
                        $ctn = $ctn + 1;
                        
                                  
            }
            if ($ctn == 1) {
                $query = $mysqli->query($sql);
                while ($fila= mysqli_fetch_row($query)){
                    echo '<option value="'.$fila[0].'">'.$fila[1].ucwords((mb_strtolower('- '.$fila[2]))).'</option>';
                }
            }
                                   
        }
              
    }
?>
