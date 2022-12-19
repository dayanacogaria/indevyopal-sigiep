
<?php 
require_once ('../Conexion/conexion.php');
session_start();
$anno = $_SESSION['anno'];
#BUSCAN TODAS LAS CUENTAS
$cuentas = "SELECT id_unico, codi_presupuesto "
        . "FROM gf_rubro_pptal  WHERE parametrizacionanno = $anno "
        . "ORDER BY codi_presupuesto ASC";
$cuentas = $mysqli->query($cuentas);
#VARIABLE CONTEO DE ACTUALIZACION
$n=0;
#SI HAY CUENTAS ENCONTRADASS
if(mysqli_num_rows($cuentas)>0){
    while ($row = mysqli_fetch_array($cuentas)) {
        #ASIGNAR EL CODIGO A UNA VARIABLE
        $codigo = str_replace(' ', '', $row[1]);
        #CONTAR LA CANTIDAD DE DIGITOS DEL CODIGO
        $cant = strlen($codigo);
        #SI LA CANTIDAD ES UNO NO SE HACE NADA
        if ($cant == 1) {

        }else{
        #SI NO 
        #SE DEFINE UNAS VARIABLES CONTEO
        $ctn = 0;
        $cant2=$cant;
        #CICLO PARA BUSCAR PREDECESOR
        for($i = 0;$i <=$cant;$i++){
            #SE LE QUITA UN DIGITO AL CODIGO
            $men = substr($codigo,0,-1);
            
            #CON EL CODIGO HALLADO BUSCAMOS UN CODIGO IGUAL EL CUAL SERIA EL PREDECESOR
            $sql = "SELECT DISTINCTROW
                           id_unico 
                    FROM
                           gf_rubro_pptal 
                    WHERE
                           codi_presupuesto = '$men' 
                    AND 
                          parametrizacionanno = $anno ";

                    $ctn = $ctn + 1;
                        
                $codigo = $men;
                $cant2=$cant2-1;
            $query = $mysqli->query($sql);
            if (mysqli_num_rows($query)>0) {
                
                #SI HAY ALGUNA COINCIDENCIA 
                #SE PASA A REALIZAR LA ACTUALIZACION
                $pred = mysqli_fetch_row($query);
                $predecesor = $pred[0];
                $update = "UPDATE gf_rubro_pptal SET predecesor ='$predecesor' WHERE id_unico = '$row[0]'";
                $result= $mysqli->query($update);
                if($result==true){
                #SE CUENTA LA ACTUALIZACION
                $n=$n+1;
                }
                #VARIABLE ROMPE EL CICLO
                $cant=0;
                $ctn=0;
            } else {
                
            }
                                   
        }          
       }
    }
    $mensaje= $n." Rubros Presupuestales Actualizados";
} else {
    $mensaje = 'No se encontraron Rubros Presupuestales';
}
echo json_encode($mensaje);
?>