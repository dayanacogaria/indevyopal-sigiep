<?php
require_once('../Conexion/conexion.php');
session_start();
setlocale(LC_ALL,"es_ES");
date_default_timezone_set('America/Bogota');
$actualizados=0;
$guardados=0;
$noguardados=0;
$total=0;
$mensaje=1;
$doc=1;

$documento = $_FILES['file'];
$name = $_FILES['file']['name'];
$ext = pathinfo($name, PATHINFO_EXTENSION);
$directorio ='../documentos/lecturas/';
if(empty($_POST['nombre'])){
    $nombre=$name;
    $nombreArchivo= pathinfo($name, PATHINFO_FILENAME);
} else {
    $nombreArchivo=$mysqli->real_escape_string(''.$_POST['nombre'].'');
    $nom  = $mysqli->real_escape_string(''.$_POST['nombre'].'');
    $no= str_replace(" ", "_", $nom);
    $nombre= $no.'.'.$ext;
}
#BUSCAR SI EL NOMBRE YA EXISTE
$buscNombre = "SELECT * FROM gp_lectura_archivos WHERE nombre = '$nombreArchivo'";
$buscNombre = $mysqli->query($buscNombre);
if(mysqli_num_rows($buscNombre)>0){
    $idn= "SELECT max(id_unico) FROM gp_lectura_archivos";
    $idn=$mysqli->query($idn);
    $idn= mysqli_fetch_row($idn);
    $idn=$idn[0];
    $nombreArchivo=$nombreArchivo.$idn;
    $nombre=$nombreArchivo.'.'.$ext;
}
$subir=move_uploaded_file($_FILES['file']['tmp_name'],$directorio.$nombre); 
if($subir ==true){
$ruta = $directorio.$nombre;


try {
    $archivo = fopen($ruta, "r");
    while(!feof($archivo)){
        $traer = fgets($archivo);
       $cadena = explode(";", $traer);
       for($i=0;$i<count($cadena)-1; $i++){
            $referencia= $cadena[0];
            $periodo=$cadena[1];
            $valor=$cadena[2];
            $aforador=$cadena[3];
            $fecha =$cadena[4];
            
       }
       

if(empty($referencia)|| empty($periodo) || empty($valor) || empty($aforador) || empty($fecha)){
        $guardados=$guardados;
        $noguardados=$noguardados+1;
        $total=$total+1; 
    } else { 
$date = str_replace('/', '-', $fecha);
$fecha2= date("Y-m-d H:m:s", strtotime($date) );
    
    
    #BUSCAR REFERENCIA
    $ref= "SELECT uvms.id_unico "
            . "FROM gp_unidad_vivienda_medidor_servicio uvms "
            . "LEFT JOIN gp_medidor m ON uvms.medidor= m.id_unico "
            . "WHERE m.referencia ='$referencia'";
    $ref=$mysqli->query($ref);
    if(mysqli_num_rows($ref)>0){
        
        $refern= mysqli_fetch_row($ref);
        $refern = $refern[0];
        #BUSCAR PERIODO
        $periodo = strtolower($periodo);
        $per="SELECT id_unico FROM gp_periodo WHERE LOWER(nombre) ='$periodo'";
        $per= $mysqli->query($per);
        if(mysqli_num_rows($per)>0){
            
            $perio= mysqli_fetch_row($per);
            $perio=$perio[0];
            #BUSCAR TERCERO
            $ter="SELECT id_unico FROM gf_tercero WHERE numeroidentificacion='$aforador'";
            $ter=$mysqli->query($ter);
            if(mysqli_num_rows($ter)>0){
                $terc= mysqli_fetch_row($ter);
                $terc=$terc[0];
                #BUSCAR SI EL REGISTRO EXISTE
                $bEx= "SELECT id_unico FROM gp_lectura "
                        . "WHERE unidad_vivienda_medidor_servicio = '$refern' "
                        . "AND periodo='$perio'";
                $bEx=$mysqli->query($bEx);
                if(mysqli_num_rows($bEx)>0){
                    $id= mysqli_fetch_row($bEx);
                    $id=$id[0];
                    #BUSCAR FECHA INICIAL PERIODO A REGISTRAR
                    $per = "SELECT fecha_inicial FROM gp_periodo WHERE id_unico ='$perio'";
                    $per = $mysqli->query($per);
                    $per = mysqli_fetch_row($per);
                    $fecha = $per[0];
                    $sql="SELECT MAX(valor) "
                            . "FROM gp_lectura l "
                            . "LEFT JOIN gp_periodo p ON l.periodo = p.id_unico "
                            . "WHERE l.unidad_vivienda_medidor_servicio='$refern' AND p.fecha_final<'$fecha' ";
                    $sql= $mysqli->query($sql);
                    $datos = mysqli_fetch_row($sql);
                    $datos = $datos[0];
                    if($valor >=$datos){
                    $update="UPDATE gp_lectura SET "
                            . "unidad_vivienda_medidor_servicio='$refern',"
                            . "periodo='$perio', valor='$valor', aforador='$terc', fecha= '$fecha2' "
                            . "WHERE id_unico='$id'";
                    $update=$mysqli->query($update);
                    } else {
                        $update=false;
                    }
                    if($update==true){
                        $actualizados=$actualizados+1;
                        $guardados=$guardados;
                        $noguardados=$noguardados;
                        $total=$total+1;
                    } else {
                        $actualizados=$actualizados;
                        $guardados=$guardados;
                        $noguardados=$noguardados+1;
                        $total=$total+1;
                    }
                    
                } else {
                    #BUSCAR FECHA INICIAL PERIODO A REGISTRAR
                    $per = "SELECT fecha_inicial FROM gp_periodo WHERE id_unico ='$perio'";
                    $per = $mysqli->query($per);
                    $per = mysqli_fetch_row($per);
                    $fecha = $per[0];
                    $sql="SELECT MAX(valor) "
                            . "FROM gp_lectura l "
                            . "LEFT JOIN gp_periodo p ON l.periodo = p.id_unico "
                            . "WHERE l.unidad_vivienda_medidor_servicio='$refern' AND p.fecha_final<'$fecha' ";
                    $sql= $mysqli->query($sql);
                    $datos = mysqli_fetch_row($sql);
                    $datos = $datos[0];
                    if($valor >=$datos){
                     $insert="INSERT INTO gp_lectura (unidad_vivienda_medidor_servicio,"
                            . "periodo, valor, aforador, fecha) VALUES ('$refern', '$perio', '$valor', '$terc', '$fecha2')";
                    $insert=$mysqli->query($insert);
                    } else {
                        $insert=false;
                    }
                 
                    if($insert==true){
                        $actualizados=$actualizados;
                        $guardados=$guardados+1;
                        $noguardados=$noguardados;
                        $total=$total+1;
                    } else {
                        $actualizados=$actualizados;
                        $guardados=$guardados;
                        $noguardados=$noguardados+1;
                        $total=$total+1;
                    }
                }
            } else {
                $actualizados=$actualizados;
                $guardados=$guardados;
                $noguardados=$noguardados+1;
                $total=$total+1;
            }
        } else {
            $actualizados=$actualizados;
            $guardados=$guardados;
            $noguardados=$noguardados+1;
            $total=$total+1;
        }
    } else {
        $actualizados=$actualizados;
        $guardados=$guardados;
        $noguardados=$noguardados+1;
        $total=$total+1;
    }
}
}
fclose($archivo);
if($guardados==0 && $actualizados==0){
    $do = unlink($ruta);
}
setlocale(LC_ALL,"es_ES");
  date_default_timezone_set('America/Bogota');
 $fecha= date('Y-m-d H:m:s'); 
 
 if(empty($_POST['descripcion'])){
     $descripcion=NULL;
 } else {
     $descripcion=$mysqli->real_escape_string(''.$_POST['descripcion'].'');
 }
 $rutaarchivo="./documentos/lecturas/".$nombre;
 $insert= "INSERT INTO gp_lectura_archivos (nombre, descripcion, fecha, ruta) "
         . "VALUES ('$nombreArchivo', '$descripcion', '$fecha', '$rutaarchivo')";
 $insert=$mysqli->query($insert);
 if($insert==true){
     $doc=1;
 }
} catch (Exception $exc) {
    $mensaje=2;
    
}

  
} else {
    $mensaje=2;
    $doc=0;
}
$datos = array("guardados"=>$guardados,"noguardados"=>$noguardados, 
    "total"=>$total, "mensaje"=>$mensaje, 
    "actualizados"=>$actualizados, "doc"=>$doc);

  echo json_encode($datos);
?>

