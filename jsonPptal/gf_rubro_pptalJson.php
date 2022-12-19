<?php 
#######################################################################################################
#                           Modificaciones
#######################################################################################################
#14/10/2017 |Erica G. | Archivo Creado 
#######################################################################################################
require_once '../Conexion/conexion.php';
require_once './funcionesPptal.php';
session_start();
$anio= $_SESSION['anno'];
switch ($_REQUEST['action']){
    #******Crear Rubro******#
    case 1:
        $respuesta=2;
        $id=0;
        $nombre = '"'.$mysqli->real_escape_string(''.$_POST["txtNombre"].'').'"';
        $mov    = '"'.$mysqli->real_escape_string(''.$_POST["optMov"].'').'"';
        $manpac = '"'.$mysqli->real_escape_string(''.$_POST["optManP"].'').'"';
        $vigenc = '"'.$mysqli->real_escape_string(''.$_POST["sltVigencia"].'').'"';
        $tipoCl = '"'.$mysqli->real_escape_string(''.$_POST["sltTipoClase"].'').'"';
        $destin = '"'.$mysqli->real_escape_string(''.$_POST["sltDestino"].'').'"';
        $tipoVi = '"'.$mysqli->real_escape_string(''.$_POST["sltTipoVigencia"].'').'"';
        
        if(empty($mysqli->real_escape_string(''.$_POST["txtCodigoP"].''))){
            $codigp = "null";
        } else{
            $codigp = '"'.$mysqli->real_escape_string(''.$_POST["txtCodigoP"].'').'"';
        }        
        if (empty($_POST["sltPredecesor"])){
          $predec='NULL'; 
        }else{
          $predec = '"'.$mysqli->real_escape_string(''.$_POST["sltPredecesor"].'').'"';
        }
        if (empty($_POST['sltSector'])){
          $sector='NULL'; 
        }else{
          $sector = '"'.$mysqli->real_escape_string(''.$_POST["sltSector"].'').'"';
        }
        if (empty($_POST["equivalente"])){
          $equivalencia='NULL'; 
        }else{
          $equivalencia = '"'.$mysqli->real_escape_string(''.$_POST["equivalente"].'').'"';
        }
        if (empty($_POST["txtDinamica"])){
          $dinamc='NULL'; 
        }else{
          $dinamc = '"'.$mysqli->real_escape_string(''.$_POST["txtDinamica"].'').'"';
        }

        $con="SELECT * FROM gf_rubro_pptal where codi_presupuesto = $codigp AND parametrizacionanno = $anio";
        $rc = $mysqli->query($con);
        if(mysqli_num_rows($rc)>0)      
        {     
             $respuesta =3;
        } else{
            $sql = "INSERT INTO gf_rubro_pptal (nombre,codi_presupuesto,movimiento,manpac,vigencia,"
                    . "dinamica,parametrizacionanno,tipoclase,predecesor,destino,"
                    . "tipovigencia,sector, equivalente) VALUES($nombre,$codigp,$mov,$manpac,$vigenc,$dinamc,"
                    . "$anio,$tipoCl,$predec,$destin,$tipoVi,$sector, $equivalencia)";
            $rs = $mysqli->query($sql); 
            if($rs ==true){
                $respuesta =1;
                $bI = "SELECT MAX(id_unico) FROM gf_rubro_pptal WHERE codi_presupuesto = $codigp";
                $bI = $mysqli->query($bI);
                $bI = mysqli_fetch_row($bI);
                $id = $bI[0];
            } else {
                $respuesta =2;
            }
        }
        $datos = array("respuesta"=>$respuesta,"id"=>$id);

        echo json_encode($datos);
    break;
    #********Crear Concepto y Concepto Rubro*******#
    case 2:
        $id_rubro = $_POST['id'];
        $respuesta = 0;
        $id = md5($id_rubro);
        
        #*******Buscar Datos Rubro*******#
        $datoR = "SELECT id_unico, codi_presupuesto, LOWER(nombre), tipoclase "
                . "FROM gf_rubro_pptal WHERE id_unico = $id_rubro";
        $datoR = $mysqli->query($datoR);
        $dR = mysqli_fetch_row($datoR);
        $nombre = $dR[1].' - '.ucwords($dR[2]);
        switch ($dR[3]){
            case 6:
                $clase =1;
            break;
            case 7:
                $clase =2;
            break;
            case 9:
                $clase =3;
            break;
            default :
                $clase =3;
            break;
        }
        #***Guardar Concepto***#
        $insCon = "INSERT INTO gf_concepto (nombre, clase_concepto, parametrizacionanno) VALUES ('$nombre', $clase, $anio);";
        $insCon = $mysqli->query($insCon);
        if($insCon==true){
            $bR = "SELECT MAX(id_unico) "
                . "FROM gf_concepto WHERE clase_concepto = $clase";
            $bR = $mysqli->query($bR);
            $bR = mysqli_fetch_row($bR);
            $idC = $bR[0];
            #*****Guardar Concepto Rubro**#
            $insCR = "INSERT INTO gf_concepto_rubro (rubro, concepto) VALUES ($id_rubro, $idC);";
            $insCR = $mysqli->query($insCR);
            if($insCR==true){
                $respuesta = 1;
            } else {
                $respuesta =3;
            }
        } else {
            $respuesta=2;
        }
        
        $datos = array("respuesta"=>$respuesta,"id"=>$id);
        echo json_encode($datos);
    break;
    
    #*******Buscar Configuración Concepto Rubro***#
    case 3:
        $id_rubro = $_POST['id'];
        $bCR = "SELECT * FROM gf_concepto_rubro WHERE rubro = $id_rubro";
        $bCR = $mysqli->query($bCR);
        $num = mysqli_num_rows($bCR);
        $respuesta = 0;
        $id =0;
        if($num==1){
            $respuesta = 1;
            $cr = mysqli_fetch_row($bCR);
            $id = md5($cr[0]);
        } elseif($num>1){
            $respuesta = 2;
        } else {
            $respuesta = 0;
        }
        $datos = array("respuesta"=>$respuesta,"id"=>$id);
        echo json_encode($datos);
    break;
}