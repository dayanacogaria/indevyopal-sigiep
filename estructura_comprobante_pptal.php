<?php
#######################################################################################################
#                           Modificaciones
#######################################################################################################
#22/09/2017 |Erica G. | Arreglarlo para empresa privada
#11/05/2017 |ERICA G. |MODIFICACION SOLICITUD DIS
#08/05/2017 | ERICA G. | TILDES
#######################################################################################################
require_once('Conexion/conexion.php');
#require_once('estructura_apropiacion.php');
#require_once('estructura_apropiacion_modf.php');
require_once('./jsonPptal/funcionesPptal.php');
session_start();
$anno = $_SESSION['anno'];
$proc = $_REQUEST['proc'];
#****************Consulta Tipo de Compañia*********####
$com = $_SESSION['compania'];
$tcom = "SELECT tipo_compania FROM gf_tercero WHERE id_unico = $com";
$tcom = $mysqli->query($tcom);
if (mysqli_num_rows($tcom) > 0) {
    $tcom = mysqli_fetch_row($tcom);
    $tipocomp = $tcom[0];
} else {
    $tipocomp = 0;
}
switch ($proc) {
    case 1:
        $numero = 0;

        $queryMaxNum = "SELECT MAX(compPtal.numero) 
            	FROM gf_comprobante_pptal compPtal
                left join gf_tipo_comprobante_pptal tipComPtal on tipComPtal.id_unico = compPtal.tipocomprobante 
                left join gf_clase_pptal claPtal on claPtal.id_unico = tipComPtal.clasepptal
                where claPtal.id_unico = 11 AND compPtal.parametrizacionanno = $anno "; //Clase pptal 11 Solicitud de Disponibilidad.

        $maxNum = $mysqli->query($queryMaxNum);
        $row = mysqli_fetch_row($maxNum);

        if ($row[0] == 0) {

            if (!empty($_SESSION['anno'])) {
                $parametroAnno = $_SESSION['anno'];
                $sqlAnno = 'SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = ' . $parametroAnno;
                $paramAnno = $mysqli->query($sqlAnno);
                $rowPA = mysqli_fetch_row($paramAnno);
                $numero = $rowPA[0];
                $numero .= "000001";
            }
        } else {
            $numero = $row[0] + 1;
        }

        echo $numero;
        break;

    case 2:
        $id_con = $_REQUEST['id_con']; //Toma el ID del concepto.
        
        $queryRub = "SELECT rubFue.id_unico, CONCAT(rub.codi_presupuesto, ' ', rub.nombre, ' ', fue.nombre) rubro, rub.id_unico, conRub.id_unico     
				FROM gf_rubro_fuente rubFue
				LEFT JOIN gf_rubro_pptal rub ON rub.id_unico = rubFue.rubro
				LEFT JOIN gf_concepto_rubro conRub ON rub.id_unico = conRub.rubro 
                LEFT JOIN gf_fuente fue ON fue.id_unico = rubFue.fuente
				WHERE conRub.concepto = " . $id_con;
        $rubro = $mysqli->query($queryRub);
        if ($rubro == true) {
            while ($row = mysqli_fetch_row($rubro)) {
                if(empty($_REQUEST['fecha'])){
                    $saldoDisponible = apropiacion($row[0]) - disponibilidades($row[0]);
                } else {
                    $fecha = fechaC($_REQUEST['fecha']);
                    $saldoDisponible = apropiacionfecha($row[0],$fecha) - disponibilidadesfecha($row[0],$fecha);
                }
                if($tipocomp==2){
                    echo '<option value="' . $row[0] . '/' . $row[3] . '">' . ucwords(mb_strtolower($row[1])) . ' $' . number_format($saldoDisponible, 2, '.', ',') . '</option>';
                } else {
                    if ($saldoDisponible > 0){
                        echo '<option value="' . $row[0] . '/' . $row[3] . '">' . ucwords(mb_strtolower($row[1])) . ' $' . number_format($saldoDisponible, 2, '.', ',') . '</option>';
                    }
                }
            }
        }
        else {
            echo 0;
        }



        break;

    case 3:
        $IDRubroFuente = $_REQUEST['id_rubFue'];

        $saldoDisponible = apropiacion($IDRubroFuente) - disponibilidades($IDRubroFuente);
        echo $saldoDisponible;
        break;

    case 4:
        $IDRubroFuente = $_REQUEST['id_rubFue']; //ID de la tabla gf_rubro_fuente.
        $IDCompPtal = $_REQUEST['id_comp']; //ID tabla gf_comprobante_pptal.
        $clase = $_REQUEST['clase']; //Clase del campo clasepptal de la tabla gf_detalle_comprobante_pptal.

        if ($clase == 14) { //Saldo para Expedir registro Pptal
            echo 'ACA1';
            $saldoDisponible = valorRegistro($IDCompPtal, $IDRubroFuente) + modificacionRegistro($IDCompPtal, $IDRubroFuente, $clase) - afectacionRegistro2($IDCompPtal, $IDRubroFuente, $clase);
        } else { //Saldo para Expedir obligación Pptal y registrar Pago Pptal.
            echo 'ACA';
            $saldoDisponible = valorRegistro($IDCompPtal, $IDRubroFuente) + modificacionRegistro($IDCompPtal, $IDRubroFuente, $clase) - afectacionRegistro($IDCompPtal, $IDRubroFuente, $clase);
        }

        echo $saldoDisponible;
        break;

    case 5:
        $idRubroFuente = $_REQUEST['id_rubFue'];
        $idDetalleComp = $_REQUEST['id_det_comp'];

        $saldoDisponible = apropiacion_mod($idRubroFuente) - disponibilidades_mod($idRubroFuente, $idDetalleComp);
        echo $saldoDisponible;
        break;
    ###MODIFICACAR SOLICITUD DIS
    case 6:
        $id = $_REQUEST['id'];
        $fecha = $_REQUEST['fecha'];
        $des = $_REQUEST['desc'];

        if (empty($id) || empty($fecha)) {
            $resultado = 2;
        } else {
            $fecha_div = explode("/", $fecha);
            $dia = $fecha_div[0];
            $mes = $fecha_div[1];
            $anio = $fecha_div[2];

            $fc = $anio . '/' . $mes . '/' . $dia;
            if ($des == "" || empty($des)) {
                $descrp = 'NULL';
            } else {
                $descrp = '"' . $des . '"';
            }
            $upd = "UPDATE gf_comprobante_pptal "
                    . "SET fecha = '$fc', descripcion = $descrp "
                    . "WHERE id_unico =$id";
            $upd = $mysqli->query($upd);
            if ($upd == true) {
                $resultado = 1;
            } else {
                $resultado = 2;
            }
        }
        echo $resultado;
        break;
}
?>