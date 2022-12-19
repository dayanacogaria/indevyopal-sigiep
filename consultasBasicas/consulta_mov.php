<?php
/**
 * Created by Alexander.
 * User: Alexander
 * Date: 27/05/2017
 * Time: 9:09 AM
 */
@session_start();
require ('../Conexion/conexion.php');
require ('../funciones/funciones_mov.php');
$mov = $_POST['mov'];
switch ($mov) {
    case 1:
        if(!empty($_POST['id'])) {  //Validamos que el id no este vacio
            $id = $_POST['id'];     //Capturamos el valor enviado por post
            echo "registrar_RF_MOVIMIENTO_ALMACEN.php?asociado=".md5($id);//Imprimimos la url con el id convertido en md5
        }
        break;
    case 2:
        if(!empty($_POST['tipo'])) {                                                        //Validamos que el valor enviado no este vacio
            $param  = $_SESSION['anno'];                                                    //Capturamos el valor de la session anno
            $compa  = $_SESSION['compania'];                                                //Capturamos el valor de la sesión compania
            $numero = 0;                                                                    //Inicializamos la variable numero
            $tipo   = $_POST['tipo'];                                                       //Capturamos el valor enviado
            $sql    = "SELECT MAX(cast(numero as unsigned)) 
                FROM gf_movimiento 
                WHERE tipomovimiento = $tipo 
                AND parametrizacionanno = $param";//Consultamos el numero
            $result = $mysqli->query($sql);
            $row    = mysqli_fetch_row($result);                                            //Obtenemos el valor retornado en un array
            if(!empty($row[0])) {                                                           //Si los registros obtenidos son mayor que 0
                $numero = $row[0]+1;                                                        //Sumamos uno al valor obtenido en la consulta
            }else{                                                                          //Si los registros son 0 o nulos
                $anno   = get_date_param($param);
                $numero = $anno.'000001';                                                   //Formateamos el valor 2017000001
            }
            echo $numero;                                                                   //Imprimimos el valor obtenido
        }
        break;
    case 3:
        if(!empty($_POST['id'])) {                                                          //Validamos que la variable id no esté vacia
            $id = $_POST['id'];                                                             //Capturamos el valor de la variable id
            echo "registrar_RF_MOVIMIENTO_ALMACEN.php?movimiento=".md5($id);                //Codificamos el valor retornando la url
        }
        break;
    case 4:
        if(!empty($_POST['type'])) {                                                        //Validamos que la variable type no este vacia
            $type = $_POST['type'];                                                         //Capturamos el valor de la variable type
            $newDate = explode("/",$_POST['newDate']);                                      //Dividimos la variable enviada usando /
            $newDate = "$newDate[2]-$newDate[1]-$newDate[0]";                               //Formateamos la fecha YYYY-MM-DD
            $date = get_max_date($type);                                                    //Capturamos el valor de la ultima fecha relacionada al tipo
            if($newDate < $date) {                                                          //Si la nueva fecha es menor que la fecha encontrada
                echo json_encode(true);
            }else {
                echo json_encode(false);
            }
        }
        break;
    case 5:
        if(!empty($_POST['id'])) {                                                          //Validamos que la variable id no esté vacia
            $id = $_POST['id'];                                                             //Capturamos el valor de la variable id
            echo "RF_REQUISICION_ALMACEN.php?movimiento=".md5($id);                //Codificamos el valor retornando la url
        }
        break;
    case 6:
        if(!empty($_POST['id'])) {
            $id = $_POST['id'];
            echo "registrar_RF_ORDEN_DE_COMPRA.php?movimiento=".md5($id);
        }
        break;
    case 7:
        if(!empty($_POST['id'])) {
            $id = $_POST['id'];
            echo "RF_ENTRADA_ALMACEN.php?movimiento=".md5($id);
        }
        break;
    case 8:
        if(!empty($_POST['id'])) {
            $id = $_POST['id'];
            echo "RF_ENTRADA_ALMACEN.php?asociado=".md5($id);
        }
        break;
    case 9:
        $html = "";
        if(!empty($_POST['tipo'])){
            $tipoA    = $_POST['tipo'];
            $html .= '<option value="">Nro Asociado</option>';
            $sql   = "SELECT    mv.id_unico, mv.numero, DATE_FORMAT(mv.fecha,'%d/%m/%Y'), tpm.sigla FROM gf_movimiento mv
                      LEFT JOIN gf_tipo_movimiento tpm ON tpm.id_unico = mv.tipomovimiento
                      WHERE     (mv.tipomovimiento = $tipoA)";
            $res = $mysqli->query($sql);
            $dta = $res->fetch_all(MYSQLI_NUM);
            foreach ($dta as $row) {
                list($totalX, $totalV, $xxx) = array(0, 0, 0);
                $sql_ = "SELECT id_unico, (valor + iva) * cantidad FROM gf_detalle_movimiento WHERE movimiento = $row[0]";
                $res_ = $mysqli->query($sql_);
                $dta_ = $res_->fetch_all(MYSQLI_NUM);

                foreach ($dta_ as $row_) {

                    $totalX += $row_[1];

                    $sq_ = "SELECT (valor + iva) * cantidad FROM gf_detalle_movimiento WHERE detalleasociado = $row_[0]";
                    $re_ = $mysqli->query($sq_);
                    $dt_ = $re_->fetch_all();

                    foreach ($dt_ as $row_) {
                        $totalV += $row_[0];
                    }
                }

                $xxx = $totalX - $totalV;
                if($xxx > 0){
                    $html .= "<option value=\"$row[0]\">$row[3] $row[1] $row[2] $".number_format($xxx, 2, ',', '.')."</option>";
                }
            }
        }
        echo $html;
        break;
    case 10:
        if(!empty($_POST['id'])) {
            $id = $_POST['id'];
            echo "registrar_GR_SALIDA_ALMACEN.php?movimiento=".md5($id);
        }
        break;
    case 11:
        if(!empty($_POST['id'])) {
            $id = $_POST['id'];
            echo "registrar_GR_SALIDA_ALMACEN.php?asociado=".md5($id);
        }
        break;
    case 12:
        if(!empty($_POST['id_Dept'])) {
            $id_dept = $_POST['id_Dept'];
            $compania = $_POST['compania'];
            $sql = "SELECT DISTINCT ter.id_unico,
                                    IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '' OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL, ter.razonsocial, CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)) AS 'NOMBRE_T',
                                    CONCAT_WS(' ',tpi.nombre, ' ', IF(ter.digitoverficacion = '' OR ter.digitoverficacion IS NULL, ter.numeroidentificacion, CONCAT_WS(' ', ter.numeroidentificacion, ' - ',ter.digitoverficacion))) AS 'NUM_IDENT'
                    FROM            gf_dependencia_responsable dpr
                    LEFT JOIN       gf_dependencia dpt          ON dpt.id_unico = dpr.dependencia
                    LEFT JOIN       gf_tercero ter              ON ter.id_unico = dpr.responsable
                    LEFT JOIN       gf_tipo_identificacion tpi  ON tpi.id_unico = ter.tipoidentificacion
                    WHERE           dpr.dependencia = $id_dept AND dpt.compania = $compania";
            $result = $mysqli->query($sql);
            while ($row = mysqli_fetch_row($result)) {
                echo "<option value=\"".$row[0]."\">".ucwords(mb_strtolower($row[1]))." (".$row[2].")</option>";
            }
        }
        break;
    case 13:
        if(!empty($_POST['markeds']) && !empty($_POST['tipoT'])) {      #Validamos que las variables enviadas por post no esten vacias
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /// Capturamos los valores enviados por post, e inicializamos las variables de las cuales conocemos su valor para registrar en la tabla gf_movimiento
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $tipoT = $_POST['tipoT'];   $compania   = $_POST['compania']; $param = $_POST['paramA'];  $numero     = get_max_number_type($tipoT); $dependencia = $_POST['dependencia'];
            $responsable = $_POST['responsable']; $centrocosto = "12"; $proyecto = "2147483647"; $estado = 2; $fecha = '"'.date('Y-m-d').'"';
            #Consulta de insertado de valores en la tabla gf_movimiento
            $sql = "INSERT INTO gf_movimiento (numero, fecha,tipomovimiento, parametrizacionanno, compania, tercero, dependencia, centrocosto, proyecto, estado)
                                      VALUES  ($numero, $fecha, $tipoT, $param, $compania, $responsable, $dependencia, $centrocosto, $proyecto, $estado)";
            $result = $mysqli->query($sql);
            if($result == true) {
                $movimiento = get_max_mov($tipoT);                      #Obtenemos el ultimo id registrado en la tabla de movimiento
                $markeds = explode(",",$_POST['markeds']);     //Dividimos el string convirtiendolo  en array usando como separador ,
                $objD = array(); $objP = array();                       //Inicializamos dos arrays en vacio
                for ($a = 0; $a < count($markeds); $a++) {              //Desplegamos el array marcados
                    $obj = explode("-",$markeds[$a]);          //Dividimos el valor del array marcado usando como separador -
                    $dta = $obj[0];
                    $producto = $obj[1];                                //Inicializamos variables con los valores encontrados
                    $sqlCD = "SELECT id_unico, valor, cantidad FROM gf_detalle_movimiento WHERE detalleasociado = $obj[0] AND movimiento = $movimiento";
                    $resultCD = $mysqli->query($sqlCD);
                    $c = 0;
                    $rowCD = mysqli_fetch_row($resultCD);
                    $porc = get_porcent_param_b();                      //Obtenemos el valor del porcentaje en el parametro basico con id 2
                    update_porce_mov($movimiento,$porc);                //Actulizamos el porcentaje en el movimiento
                    if(empty($rowCD[0])) {
                        $c++;
                        $values = get_values_detail($dta);              //Obtenemos los campos en la table gf_detalle_movimiento respecto al detalle enviado
                        $valI = (($c * $values[3]) * $porc) / 100;      //Calculamos el iva del producto con respecto a la cantidad y el porcentaje en el movimiento
                        #Insertamos en detalle movimiento
                        $sqlD = "INSERT INTO gf_detalle_movimiento (cantidad, valor, iva, movimiento, detalleasociado, planmovimiento)
                                                        VALUES ($c, $values[3], $valI, $movimiento, $values[0], $values[1])";
                        $resultD = $mysqli->query($sqlD);
                    }else{
                        $detalle = $rowCD[0]; $valor = $rowCD[1]; $cantidad = $rowCD[2] + 1;    //Inicializamos las variables para identificar los posibles valores a ingresar
                        $valI = (($cantidad * $values[3]) * $porc) / 100;    //Calculamos el iva del producto con respecto a la cantidad y el porcentaje en el movimiento
                        $sqlDD = "UPDATE gf_detalle_movimiento SET cantidad = $cantidad, iva = $valI WHERE id_unico = $detalle";
                        $resultDD = $mysqli->query($sqlDD);
                    }
                    $id_d = get_max_detail_mov($movimiento);    //Obtenemos el ultimo id del detalle registrado en el movimiento
                    insert_mov_pro($obj[1], $id_d);             //Insertamos en gf_movimiento_producto
                }
            }
            echo json_encode($result);
        }
        break;
    case 14:
        if(!empty($_POST['seleccionados'])) {
            $markeds = explode(",", $_POST['seleccionados']); $detalle = $_POST['detalle']; $b = 0; $porcentaje = 0;
            for ($a = 0; $a < count($markeds); $a++) {
                $insert = insert_mov_pro($markeds[$a], $detalle);
                if($insert == true) {
                    $b++;
                }
            }

            $sql = "SELECT mov.porcivaglobal FROM gf_detalle_movimiento dtm LEFT JOIN gf_movimiento mov ON dtm.movimiento = mov.id_unico WHERE dtm.id_unico = $detalle";
            $result = $mysqli->query($sql);
            if($result == true && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_row($result);
                $porcentaje = $row[0];
            }

            $values = get_values_detail($detalle);
            $cantidad = get_n_products_detail($detalle);
            $valorI = (($values[3] * $cantidad) * $porcentaje) / 100;
            $update = update_values_i_c($detalle, $cantidad, $valorI);
            echo json_encode($update);
        }
        break;
}