<?php
#Llamamos a la clase de conexión
require_once ('../Conexion/conexion.php');
#definimos la sesion
session_start();
$anno = $_SESSION['anno'];
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
            WHERE PADRE.parametrizacionanno = $anno ";
        $resultado = $mysqli->query($sql);
        echo '<option value="">Predecesor</option>';
        while ($row = mysqli_fetch_row($resultado)){
           echo '<option value="'.$row[0].'">'.$row[1] .'-'. ucwords((mb_strtolower($row[2]))) .'</option>';            
        }

    }else{
        $ctn = 0;
        $cant2=$cant;
        for($i = 0;$i <=$cant;$i++){
            if($cant==2){
                $men = substr($codigo,0,-1);
            } else {
                if(($cant2 % 2)==0){
                    $men = substr($codigo,0,-2);
                }else {
                    $men = substr($codigo,0,-1);
                }
            }
                 //echo $men."<br/>";
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
                               PADRE.codi_cuenta = '$men' AND PADRE.parametrizacionanno = $anno";
                
                        $ctn = $ctn + 1;
                        
                $codigo = $men;
                $cant2=$cant2-1;
            $query = $mysqli->query($sql);
            if (mysqli_num_rows($query)>0) {
                while ($fila= mysqli_fetch_row($query)){
                    echo '<option value="'.$fila[0].'">'.$fila[1].ucwords((mb_strtolower('- '.$fila[2]))).'</option>';
                }
                echo '<option value="">-</option>';
                $cant=0;
                $ctn=0;
            } else {
            $sql1 = "SELECT DISTINCTROW
                       PADRE.id_unico,                        
                       PADRE.codi_cuenta,
                       PADRE.nombre
                    FROM
                           gf_cuenta PADRE
                    LEFT JOIN   
                           gf_cuenta HIJO
                    ON
                           PADRE.id_unico = HIJO.predecesor AND PADRE.parametrizacionanno = $anno";
                $resultado1 = $mysqli->query($sql1);
                echo '<option value="">Predecesor</option>';
                while ($row1 = mysqli_fetch_row($resultado1)){
                   echo '<option value="'.$row1[0].'">'.$row1[1] .'-'. ucwords((mb_strtolower($row1[2]))) .'</option>';            
                }
                
            }
                                   
        }
              
    }
?>
