<?php
#############################################################################################
#       ***************************     Modificaciones      ***************************     #
#############################################################################################
#19/04/2018 | Erica G.  | Parametrizacion
#21/02/2018 | Erica G. | Modificar Case 48 Consulta Relaciones 
####/################################################################################
/*
 * Modificado por: Erica González
 * Fecha Modificacion: 14/07/2017
 * Descripción:Se modificó el caso 59, Registrar pago en recaudo, verificar si tiene retenciones, para registrarlas
 * en el comprobante cnt y hacer los respectivos descuentos.
 *************************************************************************************************************************************************
 * Modificado por: Alexander Numpaque
 * Fecha de modificación: 10-07-2017
 * Descripción: Se agrego caso 64 para generar causación
 *************************************************************************************************************************************************
 * Modificado por: Alexander Numpaque
 * Fecha de modificación: 07-07-2017
 * Descripción: En el caso 47 se valido registro a presupuestal, cuando el tipo de comprobante de causación contable se relaciona con al tipo de
 * comprobante presupuestal realiza el registro, y se incluyo multiples validaciones preguntando si existe el tipo y su detalle relacionado a la
 * cuenta para insertar y mantener la relación contable y presupuestal por detalles y cabezas de comprobantes
 *************************************************************************************************************************************************
 * Modificado por: Alexander Numpaque
 * Fecha de modificación: 06-07-2017
 * Descripción: En el caso 57, se cambio la consulta para obtener los conceptos relacionados a la factura y se valida a la vez que si el detalle de
 * la factura ya fue recaudado y el valor es igual al del concepto no mostrara el concepto.
 * En el caso 58 se adiciono consulta para validar si el detalle de la factura, relacionada al concepto del cual se va obtener el valor si tiene a-
 * lgún recaudo, se resta y se imprime el valor del concepto.
 * Y en el caso 59 se agrego validación, la cual pregunta si el valor del detalle de pago es igual al total al valor total de la factura, se reali-
 * za el comprobante contable y presupuestal de recaudo con todos los conceptos de la factura. En case 47 se valido el registro a presupuestal por
 * medio de if que pregunta si el tipo de comprobante existe
 */
######################Modificaciones#################################################################################
# Modificado por  : Alexander Numpaque
# Fecha           : 01/06/2017
# Descripción     : Se agrego a la consulta TRIM del case 53 para validación de espacios que afecten la generación del autoincremento
#####################################################################################################################
# Modificado por  : Alexander Numpaque
# Fecha           : 22/05/2017
# Descripción     : Cambio de Validación registro en el case 59
#####################################################################################################################
# Modificado por  : Alexander Numpaque
# Fecha           : 17/05/2017
# Descripción     : Cambio de Validación de eliminiado de factura y los detalles
#####################################################################################################################
# Modificado por  : Alexander Numpaque
# Fecha           : 15/05/2017
# Descripción     : Cambio de metodo de validadación para busqueda de comprobantes y facturas
#####################################################################################################################
# Modificado por  : Alexander Numpaque
# Fecha           : 03/05/2017
# Descripción     : Cambio metodo de validaciòn para guardado del detalle banco
#####################################################################################################################
# Modificado por  : Alexander Numpaque
# Fecha           : 24/04/2017
# Descripción     : Se agrego case 56 para redireccionamiento en el formulario de recaudo presupuestal
#####################################################################################################################
# Modificado por  : Alexander Numpaque
# Fecha           : 21/04/2017
# Descripción     : Se agrego validación para comparar que la cuenta de credito y cuenta debito sean diferentes
#####################################################################################################################
# Modificado por  : Alexander Numpaque
# Fecha           : 20/04/2017
# Descripción     : Se cambio consulta para obtener los valores de forma dinamica y se valido si la consulta
#                  existio la variable o palabra order by, la cual se usa como divisor de la consulta
#####################################################################################################################
# Modificado por  : Alexander Numpaque
# Fecha           : 20/04/2017
# Descripción     : Se cambio metodo de actualización ubicando un campo oculto para llenar automaticamente cuando se
#                  registra, el cual se usa como indicador para actualizar
#####################################################################################################################
# Modificado por :  Jhon Numpaque
# Fecha          :  09/03/2017
# Hora Ter. Mod. :  10:04
# Descripción    :  Se modifico caso 5, para redireccionamiento en comprobante ingreso y se grego caso 40 para
#                   para redirecionamiento de comprobante cnt
#####################################################################################################################
# Modificado por :  Jhon Numpaque
# Fecha          :  23/02/2017
# Hora Ter. Mod. :  11:45
# Descripción    :  Se agrego caso 38 para redireccionamiento y consulta de la partida, caso 39 para modificar los d-
#                   etalles de las conciliaciones con conciliado verdadero
#####################################################################################################################
# Modificado    :   Jhon Numpaque
# Fecha         :   23/02/2017
# Hora          :   3:30 p.m
# Descripción   :   Se agrego case 36 para consulta de factura contable y presupuestal
#####################################################################################################################
# Modificado    :   Jhon Numpaque
# Fecha         :   23/02/2017
# Descripción   :   Se agrego case 35 para guardado de cuentas por pagar y se modifico guardado de valor para que sea
#                   negativo
#####################################################################################################################
# 17/02/2017 | Jhon Numpaque
# Descripción : Se agrego en la consulta del caso 32, el valor de afectaciones
#
#####################################################################################################################
# 15/02/2017 | Jhon Numpaque
# Descripción : Se agrego caso 32, para consultar ruta de comprobante egreso existente
#
#####################################################################################################################
# Jhon Numpaque | 9:00 | 03-02-2017
# Se modifico consulta en el caso 4 para corregir cargue de idPptal
#
#####################################################################################################################
# Jhon Numpaque | 09-02-2017 | 03:02 p.m
# Se incluyo caso 31 consultar tipo de comprobante para cheque
#
######################################################################################################################
require_once '../Conexion/conexion.php';
session_start();
ini_set('max_execution_time', 0);
$session = $_POST['existente'];
$anno = $_SESSION['anno'];
#@Autor:Alexander
#Este archivo consulta los numeros existentes de aquellos formularios para generar datos se inicia en dos porque ya existe el de comprobante

switch ($session) {
    case 2:
        $numero = $_REQUEST['numero'];
        $tipo = $_POST['tipo'];
        if (!(empty($numero))) {
            $sql = "SELECT  numero_factura,
                            id_unico
                    FROM    gp_factura
                    WHERE   numero_factura  =   '$numero'
                    AND     tipofactura     =   '$tipo'";
            $result = $mysqli->query($sql);
            $fila = $result->num_rows;
            $row = mysqli_fetch_row($result);
            if (empty($fila)) {
                $_SESSION['factura'] = "";
                $_SESSION['idFactura'] = "";
            } else {
                $_SESSION['factura'] = $row[0];
                $_SESSION['idFactura'] = $row[1];
            }
        } else {
            $_SESSION['factura'] = "";
            $_SESSION['idFactura'] = "";
        }
        break;
    case 3:
        $numero = $_REQUEST['numero'];
        $tipo = $_POST['tipo'];
        if (!empty($numero)) {
            $sql1 = "SELECT     id_unico,
                                numero_pago
                    FROM        gp_pago
                    WHERE       tipo_pago   =   '$tipo'
                    AND         numero_pago =   '$numero'";
            $result1 = $mysqli->query($sql1);
            $fila1 = $result1->num_rows;
            $row1 = mysqli_fetch_row($result1);
            if (($fila1 == 0)) {
                $_SESSION['idpago'] = "";
                $_SESSION['pago'] = "";
            } else {
                $_SESSION['idpago'] = $row1[0];
                $_SESSION['pago'] = $row1[1];
            }
        } else {
            $_SESSION['idpago'] = "";
            $_SESSION['pago'] = "";
        }
        break;
    case 4:
    ###########################################################################################################################
    # Captura de variable
    #
    ###########################################################################################################################
        $comprobante = $_REQUEST['comprobante'];
        #######################################################################################################################
        # Validación de variable no vacia
        #
        #######################################################################################################################
        if(!empty($comprobante)){
            ###################################################################################################################
            # Consulta para obtener el comprobante cnt
            #
            ###################################################################################################################
            $sql = "SELECT      dtp.comprobantepptal
                    FROM        gf_detalle_comprobante dtc
                    LEFT JOIN   gf_detalle_comprobante_pptal dtp    ON  dtc.detallecomprobantepptal = dtp.id_unico
                    WHERE       dtc.comprobante = $comprobante";
            $result = $mysqli->query($sql);
            $fila = mysqli_num_rows($result);
            ###################################################################################################################
            # Validación de consulta
            #
            ###################################################################################################################
            if($fila>0){
                $row = mysqli_fetch_row($result);
                ###############################################################################################################
                # Cargue de variables
                #
                ###############################################################################################################
                $_SESSION['idComprobanteI'] = $comprobante;
                $_SESSION['idPptal'] = $row[0];
            }else{
                ###############################################################################################################
                # Cargue de variables
                #
                ###############################################################################################################
                $_SESSION['idComprobanteI'] = $comprobante;
                $_SESSION['idPptal'] = "";
            }
        }else{
            ###################################################################################################################
            # Variables vacias
            #
            ###################################################################################################################
            $_SESSION['idComprobanteI'] = "";
            $_SESSION['idPptal'] = "";
        }
        break;
    case 5:
        $depencia = $_POST['dependencia'];
        $sql = "SELECT      IF(CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) 
            IS NULL OR CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) = '',
            (tr.razonsocial),
            CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos)) AS NOMBRE,
            tr.id_unico,
            CONCAT(ti.nombre,' - ',tr.numeroidentificacion) AS 'TipoD'
        FROM        gf_dependencia_responsable dpr
        LEFT JOIN   gf_tercero tr               ON  dpr.responsable     = tr.id_unico
        LEFT JOIN   gf_tipo_identificacion ti   ON  ti.id_unico         = tr.tipoidentificacion
        WHERE       dpr.dependencia     =   $depencia";
        $result = $mysqli->query($sql);
        $fila = mysqli_num_rows($result);
        if (!empty($fila)) {
            while ($fila = mysqli_fetch_row($result)) {
                echo '<option value="' . $fila[1] . '">' . ucwords(mb_strtolower($fila[0])) . '</option>';
            }
        } else {
            echo '<option value="">Responsable</option>';
        }
        break;
    case 6:
        $numero = $_POST['numero'];
        $sql = "SELECT  id_unico
                FROM    gf_movimiento
                WHERE   numero          =   $numero
                AND     tipomovimiento  =   '4'";
        $result = $mysqli->query($sql);
        $id = mysqli_fetch_row($result);
        $fila = mysqli_num_rows($result);
        if ($fila == 0) {
            echo 'RF_REQUISICION_ALMACEN.php';
        } else {
            echo 'RF_REQUISICION_ALMACEN.php?movimiento=' . md5($id[0]);
        }
        break;
    case 7:
        $numero = $_POST['numero'];
        $sql = "SELECT  id_unico
                FROM    gf_movimiento
                WHERE   numero          =   $numero
                AND     tipomovimiento  =   '1'";
        $result = $mysqli->query($sql);
        $id = mysqli_fetch_row($result);
        $fila = mysqli_num_rows($result);
        if ($fila == 0) {
            echo 'RF_ORDEN_DE_COMPRA.php';
        } else {
            echo 'RF_ORDEN_DE_COMPRA.php?orden=' . md5($id[0]);
        }
        break;
    //Consultar tercero con perfil 2, diferente al tercero que trae
    case 8:
        $id = $_POST['tercero'];
        $sql = "SELECT      IF( CONCAT(t.nombreuno,' ', t.nombredos,' ',t.apellidouno,' ',t.apellidodos)='',
                            (t.razonsocial),
                            (CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos))) AS NOMBRE ,
                            t.id_unico,
                            t.numeroidentificacion
                FROM        gf_tercero t
                LEFT JOIN   gf_perfil_tercero pt ON t.id_unico = pt.tercero
                WHERE       pt.perfil   =   '2'
                AND         t.id_unico  !=  '$id'
                ORDER BY NOMBRE ASC";
        $result = $mysqli->query($sql);
        $fila = mysqli_num_rows($result);
        if (!empty($fila)) {
            echo '<option value="">Responsable 2</option>';
            while ($fila = mysqli_fetch_row($result)) {
                echo '<option value="' . $fila[1] . '">' . ucwords(mb_strtolower($fila[0] . '(' . $fila[2])) . ')' . '</option>';
            }
        } else {
            echo '<option value="">Responsable 2</option>';
        }
        break;
    case 9:
        $numero = $_POST['numero'];
        $tipo = $_POST['tipo'];
        $sql = "SELECT      id_unico
                FROM        gf_movimiento
                WHERE       numero          =   $numero
                AND         tipomovimiento  =   $tipo";
        $result = $mysqli->query($sql);
        $id = mysqli_fetch_row($result);
        $fila = mysqli_num_rows($result);
        if (empty($fila)) {
            echo 'RF_MOVIMIENTO_ALMACEN.php';
        } else {
            echo 'RF_MOVIMIENTO_ALMACEN.php?movimiento=' . md5($id[0]);
        }
        break;
    case 10:
        $tipoA = $_POST['tipo'];
        $sqlAsociado = "SELECT DISTINCT mv.id_unico,
                                        mv.numero,
                                        DATE_FORMAT(mv.fecha,'%d/%m/%Y'),
                                        tmv.nombre
                        FROM        gf_movimiento mv
                        LEFT JOIN   gf_detalle_movimiento dtm       ON  dtm.movimiento      = mv.id_unico
                        LEFT JOIN   gf_tipo_movimiento tmv          ON  mv.tipomovimiento   = tmv.id_unico
                        WHERE       mv.tipomovimiento=$tipoA
                        AND         (dtm.valor)>0
                        ORDER BY    mv.id_unico";
        $resultado = $mysqli->query($sqlAsociado);
        $fila = mysqli_num_rows($resultado);
        if ($fila != 0) {
            echo '<option value="0">Nro Asociado</option>';
            while ($row = mysqli_fetch_row($resultado)) {
                $sqlAM = "SELECT DISTINCT   (dtm.valor) as valor
                        FROM                gf_movimiento mv
                        LEFT JOIN           gf_detalle_movimiento dtm ON  dtm.movimiento = mv.id_unico
                        WHERE               mv.id_unico=$row[0]";
                $resultAM = $mysqli->query($sqlAM);
                $valAM = mysqli_fetch_row($resultAM);
                $sqlAF = "SELECT DISTINCT   IF(IFNULL((dtm.valor) - $valAM[0],$valAM[0])=0,$valAM[0],IFNULL((dtm.valor) - $valAM[0],$valAM[0])) as total
                        FROM                gf_movimiento mv
                        LEFT JOIN           gf_detalle_movimiento dtm   ON  dtm.movimiento = mv.id_unico
                        WHERE               mv.id_unico     =   $row[0]";
                $resultAF = $mysqli->query($sqlAF);
                $valAF = mysqli_fetch_row($resultAF);
                echo '<option value="' . $row[0] . '">' . ucwords(mb_strtolower('Nro:' . $row[1])) . ' -Saldo:' . number_format($valAF[0], 0, ',', '.') . ' -Fecha:' . $row[2] . '</option>';
            }
        } else {
            echo '<option value="0">Nro Asociado</option>';
        }
        break;
    //Llena una sesión con un tipo proceso para formulario flujo procesal
    case 11:
        $tipop = $_POST['proceso'];
        $_SESSION['tipoProceso'] = $tipop;
        break;
    //Llena combo modal flujo procesal si
    case 12:
        $flujo = $_POST['id'];
        $tipo = $_POST['tipo'];
        $sql = "SELECT  fp.id_unico ,fp.tipo_proceso, tp.identificador, tp.nombre, fp.fase, f.nombre, "
                . "ef.id_unico, ef.nombre FROM gg_flujo_procesal fp "
                . "LEFT JOIN gg_tipo_proceso tp ON fp.tipo_proceso = tp.id_unico "
                . "LEFT JOIN gg_fase f ON fp.fase = f.id_unico "
                . "LEFT JOIN gg_elemento_flujo ef ON ef.id_unico = f.elemento_flujo "
                . "WHERE fp.tipo_proceso ='$tipo' AND fp.fase !='0' AND fp.id_unico !='$flujo'";
        $result = $mysqli->query($sql);
        $fila = mysqli_num_rows($result);
        if (!empty($fila)) {
            echo '<option value="">Flujo Procesal</option>';
            while ($fila = mysqli_fetch_row($result)) {
                echo '<option value="' . $fila[0] . '">' . ucwords(mb_strtolower($fila[5] . ' - ' . $fila[7])) . '</option>';
            }
        } else {
            echo '<option value="">Flujo Procesal</option>';
        }
        break;
    case 13:

        $medidor = $_POST['medidor'];
        $sql = "SELECT m.id_unico, m.referencia FROM gp_medidor m "
                . "WHERE NOT EXISTS(SELECT * FROM gp_unidad_vivienda_medidor_servicio "
                . "WHERE medidor = m.id_unico AND m.id_unico !='$medidor') "
                . "ORDER BY m.referencia ASC";
        $result = $mysqli->query($sql);
        $fila = mysqli_num_rows($result);
        if (!empty($fila)) {
            echo '<option value="">Medidor</option>';
            while ($fila = mysqli_fetch_row($result)) {
                echo '<option value="' . $fila[0] . '">' . mb_strtoupper($fila[1]) . '</option>';
            }
        } else {
            echo '<option value="">Medidor</option>';
        }
        break;
    case 14:

        $tipo_proceso = $_POST['tipo_proceso'];
        $sql = "SELECT
                        fp.id_unico,
                        f.nombre,
                        ef.nombre
                      FROM
                        gg_flujo_procesal fp
                      LEFT JOIN
                        gg_fase f ON fp.fase = f.id_unico
                      LEFT JOIN
                        gg_elemento_flujo ef ON f.elemento_flujo = ef.id_unico
                      WHERE
                      fp.tipo_proceso = '$tipo_proceso' AND LOWER(ef.nombre) = 'etapa especial'";
        $result = $mysqli->query($sql);
        $fila = mysqli_num_rows($result);
        if (!empty($fila)) {
            echo '<option value="">Etapa Especial</option>';
            while ($fila = mysqli_fetch_row($result)) {
                echo '<option value="' . $fila[0] . '">' . ucwords(mb_strtolower($fila[1])) . '</option>';
            }
        } else {
            echo '<option value="">Etapa Especial</option>';
        }

        break;
    case 15:
        $tipo_proceso = $_POST['tipo_proceso'];
        $sql = "SELECT
                        fp.id_unico,
                        f.nombre,
                        ef.nombre
                      FROM
                        gg_flujo_procesal fp
                      LEFT JOIN
                        gg_fase f
                        ON fp.fase = f.id_unico
                      LEFT JOIN
                        gg_elemento_flujo ef
                        ON f.elemento_flujo = ef.id_unico
                      WHERE
                          fp.tipo_proceso = '$tipo_proceso' ";
        $result = $mysqli->query($sql);
        $fila = mysqli_num_rows($result);
        if (!empty($fila)) {
            echo '<option value="">Elemento Relacional</option>';
            while ($fila = mysqli_fetch_row($result)) {
                echo '<option value="' . $fila[0] . '">' . ucwords(mb_strtolower($fila[1] . ' - ' . $fila[2])) . '</option>';
            }
        } else {
            echo '<option value="">Elemento Relacional</option>';
        }
        break;
    case 16:

        $id = $_POST['id'];
        $tipo_proceso = $_POST['tipo'];
        $sql = "SELECT
                        fp.id_unico,
                        f.nombre,
                        ef.nombre
                      FROM
                        gg_flujo_procesal fp
                      LEFT JOIN
                        gg_fase f ON fp.fase = f.id_unico
                      LEFT JOIN
                        gg_elemento_flujo ef ON f.elemento_flujo = ef.id_unico
                      WHERE
                      fp.tipo_proceso = '$tipo_proceso' AND fp.id_unico != '$id'";
        $result = $mysqli->query($sql);
        $fila = mysqli_num_rows($result);
        if (!empty($fila)) {
            echo '<option value="">Elemento Relacional</option>';
            while ($fila = mysqli_fetch_row($result)) {
                echo '<option value="' . $fila[0] . '">' . ucwords(mb_strtolower($fila[1])) . '</option>';
            }
        } else {
            echo '<option value="">Elemento Relacional</option>';
        }
        break;
    case 17:
        $mov = $_POST['mov'];
        if (!empty($mov)) {
            $sql = "select cn.id_unico,cn.nombre from gf_movimiento mov left join gf_centro_costo cn on cn.id_unico =mov.centrocosto where mov.id_unico=$mov";
            $result = $mysqli->query($sql);
            $centro = mysqli_fetch_row($result);
            echo '<option value="' . $centro[0] . '">' . trim(ucwords(mb_strtolower($centro[1]))) . '</option>';
            $sqlCentroCosto = "select id_unico,nombre from gf_centro_costo where id_unico!=$centro[0]";
            $resultCentro = $mysqli->query($sqlCentroCosto);
            while ($centros = mysqli_fetch_row($resultCentro)) {
                echo '<option value="' . $centros[0] . '">' . $centros[1] . '</option>';
            }
        }
        break;
    case 18:
        $mov = $_POST['mov'];
        if (!empty($mov)) {
            $sql = "select pr.id_unico,pr.nombre from gf_movimiento mov left join gf_proyecto pr on pr.id_unico =mov.proyecto where mov.id_unico=$mov";
            $result = $mysqli->query($sql);
            $pro = mysqli_fetch_row($result);
            echo '<option value="' . $pro[0] . '">' . trim(ucwords(mb_strtolower($pro[1]))) . '</option>';
            $sqlPro = "select id_unico,nombre from gf_proyecto where id_unico!=$pro[0]";
            $resultPro = $mysqli->query($sqlPro);
            while ($proys = mysqli_fetch_row($resultPro)) {
                echo '<option value="' . $proys[0] . '">' . $proys[1] . '</option>';
            }
        }
        break;
    case 19:
        $mov = $_POST['mov'];
        if (!empty($mov)) {
            $sql = "select dtp.id_unico,dtp.nombre from gf_movimiento mov left join gf_dependencia dtp on dtp.id_unico =mov.dependencia where mov.id_unico=$mov";
            $result = $mysqli->query($sql);
            $dept = mysqli_fetch_row($result);
            echo '<option value="'.$dept[0].'">'.trim(ucwords(mb_strtolower($dept[1]))).'</option>';
            $sqlDept = "select id_unico,nombre from gf_dependencia where id_unico!=$dept[0]";
            $resultDpt = $mysqli->query($sqlDept);
            while ($dpts = mysqli_fetch_row($resultDpt)) {
                echo '<option value="' . $dpts[0] . '">' . $dpts[1] . '</option>';
            }
        }
        break;
    case 20:
        $mov = $_POST['mov'];
        if (!empty($mov)) {
            $sql = "SELECT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                    (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE',
                    ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD',mov.dependencia
                    FROM gf_dependencia_responsable dpr
                    LEFT JOIN gf_tercero ter ON dpr.responsable = ter.id_unico
                    LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                    LEFT join gf_movimiento mov ON mov.tercero=ter.id_unico
                    WHERE  mov.id_unico=$mov";
            $result = $mysqli->query($sql);
            $ter = mysqli_fetch_row($result);
            echo '<option value="'.$ter[1].'">'.trim(ucwords(mb_strtolower($ter[0]))).'</option>';
            $sqlTercero = "SELECT DISTINCT IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                        (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE',
                        ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_dependencia_responsable dpr
                        LEFT JOIN gf_tercero ter ON dpr.responsable = ter.id_unico
                        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                        LEFT join gf_movimiento mov ON mov.tercero=ter.id_unico
                        WHERE  ter.id_unico!=$ter[1] AND dpr.dependencia=$ter[3]";
            $resultTercero = $mysqli->query($sqlTercero);
            while ($ters = mysqli_fetch_row($resultTercero)) {
                echo '<option value="'.$ters[1].'">'.$ters[0].'</option>';
            }
        }
        break;
    case 21:
        $mov = $_POST['mov'];
        if (!empty($mov)) {
            $sql = "select rpptal.id_unico,CONCAT(rpptal.codi_presupuesto,' - ',rpptal.nombre) from gf_movimiento mov left join gf_rubro_pptal rpptal on rpptal.id_unico =mov.rubropptal where mov.id_unico=$mov";
            $result = $mysqli->query($sql);
            $rpptal = mysqli_fetch_row($result);
            echo '<option selected="selected" value="'.$rpptal[0].'">'.trim(ucwords(mb_strtolower($rpptal[1]))).'</option>';
            $sqlRpptal = "select id_unico,CONCAT(codi_presupuesto,' - ',nombre) from gf_rubro_pptal where id_unico!=$rpptal[0]";
            $resultPptal = $mysqli->query($sqlRpptal);
            while ($rpptals = mysqli_fetch_row($resultPptal)) {
                echo '<option value="'.$rpptals[0].'">'.$rpptals[1].'</option>';
            }
        }
        break;
    case 22:
        $mov = $_POST['mov'];
        if (!empty($mov)) {
            $sql = "select mov.plazoentrega from gf_movimiento mov where mov.id_unico=$mov";
            $result = $mysqli->query($sql);
            $plz = mysqli_fetch_row($result);
            echo json_encode(trim($plz[0]));
        }
        break;
    case 23:
        $mov = $_POST['mov'];
        if (!empty($mov)) {
            $sql = "select uplz.id_unico,uplz.nombre from gf_movimiento mov left join gf_unidad_plazo_entrega uplz on uplz.id_unico =mov.unidadentrega where mov.id_unico=$mov";
            $result = $mysqli->query($sql);
            $plazoE = mysqli_fetch_row($result);
            echo '<option value="'.$plazoE[0].'">'.trim(ucwords(mb_strtolower($plazoE[1]))).'</option>';
            $sqlPlz = "select id_unico,nombre from gf_unidad_plazo_entrega where id_unico!=$plazoE[0]";
            $resultPlz = $mysqli->query($sqlPlz);
            while ($plzs = mysqli_fetch_row($resultPlz)) {
                echo '<option value="'.$plzs[0].'">'.$plzs[1].'</option>';
            }
        }
        break;
    case 24:
        $mov = $_POST['mov'];
        if (!empty($mov)) {
            $sql = "SELECT DISTINCT ci.id_unico,ci.nombre,dt.nombre FROM gf_ciudad ci LEFT JOIN gf_departamento dt ON ci.departamento = dt.id_unico LEFT JOIN gf_movimiento mov ON mov.lugarentrega = ci.id_unico WHERE mov.id_unico=$mov";
            $result = $mysqli->query($sql);
            $lugarE = mysqli_fetch_row($result);
            echo '<option value="'.$lugarE[0].'">'.trim(ucwords(mb_strtolower($lugarE[1].PHP_EOL.'-'.PHP_EOL.$lugarE[2]))).'</option>';
            $sqlLugarE = "SELECT DISTINCT ci.id_unico,ci.nombre,dt.nombre FROM gf_ciudad ci LEFT JOIN gf_departamento dt ON ci.departamento = dt.id_unico LEFT JOIN gf_movimiento mov ON mov.lugarentrega = ci.id_unico WHERE ci.id_unico!=$lugarE[0]";
            $resultLE = $mysqli->query($sqlLugarE);
            while ($lgS = mysqli_fetch_row($resultLE)) {
                echo '<option value="'.$lgS[0].'">'.$lgS[1].PHP_EOL.'-'.PHP_EOL.$lgS[2].'</option>';
            }
        }
        break;
    case 25:
        $mov = $_POST['mov'];
        if (!empty($mov)) {
            $sql = "select mov.porcivaglobal from gf_movimiento mov where mov.id_unico=$mov";
            $result = $mysqli->query($sql);
            $porcIva = mysqli_fetch_row($result);
            echo trim((($porcIva[0])));
        }
        break;
    case 26:
        $mov = $_POST['mov'];
        if (!empty($mov)) {
            $sql = "select stm.nombre from gf_movimiento mov left join gf_estado_movimiento stm on mov.estado=stm.id_unico where mov.id_unico=$mov";
            $result = $mysqli->query($sql);
            $estado = mysqli_fetch_row($result);
            echo trim((($estado[0])));
        }
        break;
    case 27:
        $mov = $_POST['mov'];
        if (!empty($mov)) {
            $sql = "select DATE_FORMAT(mov.fecha,'%d/%m/%Y') from gf_movimiento mov where mov.id_unico=$mov";
            $result = $mysqli->query($sql);
            $porcIva = mysqli_fetch_row($result);
            echo trim((($porcIva[0])));
        }
        break;
    case 28:
        $mov = $_POST['mov'];
        if (!empty($mov)) {
            $sql = "SELECT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                    (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE',
                    ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                    LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                    LEFT JOIN gf_perfil_tercero prt ON ter.id_unico = prt.tercero
                    LEFT JOIN gf_movimiento mov ON mov.tercero2 = ter.id_unico
                    WHERE prt.perfil BETWEEN 5 AND 6 AND mov.id_unico=$mov";
            $resultT = $mysqli->query($sql);
            $ter = mysqli_fetch_row($resultT);
            echo '<option value="'.$ter[1].'">'.trim(ucwords(mb_strtolower($ter[0].PHP_EOL.$ter[2]))).'</option>';
            $sqlLugarTer = "SELECT DISTINCT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                    (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE',
                    ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                    LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                    LEFT JOIN gf_perfil_tercero prt ON ter.id_unico = prt.tercero
                    LEFT JOIN gf_movimiento mov ON mov.tercero2 = ter.id_unico
                    WHERE prt.perfil BETWEEN 5 AND 6 AND ter.id_unico!=$ter[1]";
            $resultTer = $mysqli->query($sqlLugarTer);
            while ($terss = mysqli_fetch_row($resultTer)) {
                echo '<option value="'.$terss[1].'">'.$terss[0].PHP_EOL.$terss[2].'</option>';
            }
        }
        break;
    case 29:
        $mov = $_POST['mov'];
        if (!empty($mov)) {
            $sql = "select mov.descripcion from gf_movimiento mov where mov.id_unico=$mov";
            $result = $mysqli->query($sql);
            $descripcion = mysqli_fetch_row($result);
            echo trim((($descripcion[0])));
        }
        break;
    case 30:
        $mov = $_POST['mov'];
        if (!empty($mov)) {
            $sql = "select mov.observaciones from gf_movimiento mov where mov.id_unico=$mov";
            $result = $mysqli->query($sql);
            $observacio = mysqli_fetch_row($result);
            echo trim((($observacio[0])));
        }
        break;
    case 31:
        $tipoD = $_POST['tipoDocumento'];
        if(!empty($tipoD)){
            $sql = "SELECT fto.esCheque
            FROM gf_tipo_documento tdoc
            LEFT JOIN gf_formato fto ON tdoc.formato = fto.id_unico
            WHERE tdoc.id_unico = $tipoD";
            $result = $mysqli->query($sql);
            $esCheque = mysqli_fetch_row($result);
            echo $esCheque[0];
        }else{
            echo 2;
        }
        break;
    case 32:
        $comprobante = $_POST['comprobante'];
        if(!empty($comprobante)){
            echo 'registrar_GF_EGRESO_TESORERIA.php?egreso='.md5($comprobante);
        }else{
            echo 'registrar_GF_EGRESO_TESORERIA.php';
        }
    break;
    case 33:
        $tercero = $_POST['tercero'];
        if(!empty($tercero)){
            echo "<option value=' '>Cuentas por pagar</option>";
            #Consulta parametrizada con ajax para consultar ordenes de pago por tercero
            $sql = "SELECT    DISTINCT  cnt.id_unico,
                                cnt.numero,
                                cnt.tipocomprobante,
                                tpc.sigla
                    FROM        gf_detalle_comprobante dtc
                    LEFT JOIN   gf_comprobante_cnt cnt
                    ON          cnt.id_unico = dtc.comprobante
                    LEFT JOIN   gf_detalle_comprobante dtcA
                    ON          dtc.id_unico = dtcA.detalleAfectado
                    LEFT JOIN   gf_tipo_comprobante tpc
                    ON          tpc.id_unico     = cnt.tipocomprobante
                    LEFT JOIN   gf_cuenta cta
                    ON          dtc.cuenta       = cta.id_unico
                    WHERE       cnt.tipocomprobante     =   4
                    AND         cnt.tercero             =   $tercero
                    AND         cta.clasecuenta         IN  (4,8,9)
                    AND         dtcA.detalleAfectado    IS NULL";
            $result = $mysqli->query($sql);
            $c = mysqli_num_rows($result);
            if($c>0){
                while ($row = mysqli_fetch_row($result)) {
                    echo "<option value=".$row[0].">".$row[1].' '.$row[3]."</option>";
                }
            }
        }
    break;
    case 34:
        #La primera consulta es para homologar el concepto enviado y obtener el id la segunda consulta cargamos los datos de rubro fuente
        $concepto = $_POST['concepto'];
        if(!empty($concepto)){
            $sqlFinanciero="select gf_con.id_unico,gf_con.nombre from gp_concepto gp_con left join gf_concepto gf_con on gf_con.id_unico=gp_con.concepto_financiero where gp_con.id_unico=$concepto";
            $resultFinanciero=$mysqli->query($sqlFinanciero);
            $financiero = mysqli_fetch_row($resultFinanciero);
            $sqlConceptoRubro="select distinct gf_rbroF.id_unico,concat(gf_rbroP.codi_presupuesto,' ',gf_rbroP.nombre,'-',gf_fte.nombre),gf_conrubro.id_unico  from gf_concepto_rubro gf_conrubro
                    left join gf_rubro_fuente gf_rbroF on gf_conrubro.rubro=gf_rbroF.rubro
                    left join gf_rubro_pptal gf_rbroP on gf_conrubro.rubro=gf_rbroP.id_unico
                    left join gf_fuente gf_fte on  gf_rbroF.fuente=gf_fte.id_unico
                    where gf_conrubro.concepto=$financiero[0] AND gf_rbroF.id_unico IS NOT NULL";
            $resultConceptoRubro=$mysqli->query($sqlConceptoRubro);
            //echo '<option value=" ">'.$sqlConceptoRubro.'</option>';
            while ($rubros= mysqli_fetch_row($resultConceptoRubro)){
                echo '<option value="'.$rubros[0].'">'.ucwords(mb_strtolower($rubros[1])).'</option>;'.$rubros[2];
            }
        }
        break;
    break;
    case 35:
        ###########################################################################################################################################################
        # Validación de cuentas por pagar
        #
        ###########################################################################################################################################################
        #
        if(!empty($_POST['sltCuentasPagar'])){
            #######################################################################################################################################################
            # Captura de valores
            #
            #######################################################################################################################################################
            #
            $cuentasP = '"'.$mysqli->real_escape_string(''.$_POST['sltCuentasPagar'].'').'"';
            $descC = '"'.$mysqli->real_escape_string(''.$_POST['descr'].'').'"';
            $fecha = explode("/",$_POST['fecha']);
            $fecha = "'"."$fecha[2]-$fecha[1]-$fecha[0]"."'";
            $id  = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
            #######################################################################################################################################################
            # Consulta de obtención de valores
            #
            #######################################################################################################################################################
            #
            $sqlCP = "SELECT  dtc.valor,
                              dtc.cuenta,
                              dtc.naturaleza,
                              dtc.tercero,
                              dtc.centrocosto,
                              dtc.proyecto,
                              dtc.id_unico
                    FROM      gf_comprobante_cnt cnt
                    LEFT JOIN gf_tipo_comprobante tpc     ON tpc.id_unico     = cnt.tipocomprobante
                    LEFT JOIN gf_detalle_comprobante dtc  ON dtc.comprobante  = cnt.id_unico
                    LEFT JOIN gf_cuenta cta               ON dtc.cuenta       = cta.id_unico
                    WHERE     cnt.id_unico      =  $cuentasP
                    AND       cta.clasecuenta   IN (4,8,9)";
          $resultCP = $mysqli->query($sqlCP);
          $c = mysqli_num_rows($resultCP);
          while ($rowCP = mysqli_fetch_row($resultCP)) {
            #######################################################################################################################################################
            # Consulta de insertado
            #
            #######################################################################################################################################################
            #
            $html = "INSERT INTO  gf_detalle_comprobante(fecha, descripcion, valor, comprobante, cuenta, naturaleza, tercero, proyecto, centrocosto, detalleafectado) VALUES($fecha, $descC, $rowCP[0], $id, $rowCP[1], $rowCP[2], $rowCP[3], $rowCP[5], $rowCP[4], $rowCP[6]);";
            $resultDC = $mysqli->query($html);
          }
          echo json_encode($resultDC);
        }
    break;
    case 36:
        ###########################################################################################################################################################
        #Captura de variable enviada
        ##########################################################################################################################################################
        $factura = $_POST['factura'];
        ##########################################################################################################################################################
        #Consultamos si la factura tiene detalles y buscamos los comporbantes cnt y pptal
        ##########################################################################################################################################################
        $sqlD = "SELECT      cnt.id_unico as cnt,ptal.id_unico as ptal
                    FROM        gp_factura pg, gp_tipo_factura tpg, gf_tipo_comprobante tpc,gf_comprobante_cnt cnt, gf_tipo_comprobante_pptal tcp,gf_comprobante_pptal ptal
                    WHERE       pg.tipofactura = tpg.id_unico
                    AND         tpc.id_unico = tpg.tipo_comprobante
                    AND         cnt.tipocomprobante = tpc.id_unico
                    AND         tpc.comprobante_pptal = tcp.id_unico
                    AND         ptal.tipocomprobante = tcp.id_unico
                    AND         pg.numero_factura = ptal.numero
                    AND         pg.numero_factura = cnt.numero
                    AND         pg.id_unico =  $factura";
        $resultD = $mysqli->query($sqlD);
        $filas = mysqli_num_rows($resultD);
        $rowD = mysqli_fetch_row($resultD);
        ######################################################################################################################################################
        #Validamos que la consulta retorne valores
        ######################################################################################################################################################
        if($filas>0){
            echo "registrar_GF_FACTURA.php?factura=".md5($factura)."&cnt=".md5($rowD[0])."&pptal=".md5($rowD[1]);
        }else{
            $sql = "SELECT dtc.comprobante,dtp.comprobantepptal FROM gp_detalle_factura dtf
            LEFT JOIN gf_detalle_comprobante dtc ON dtc.id_unico = dtf.detallecomprobante
            LEFT JOIN gf_detalle_comprobante_pptal dtp ON dtc.detallecomprobantepptal = dtp.id_unico WHERE dtf.factura = $factura";
            $result = $mysqli->query($sql);
            $filas2 = mysqli_num_rows($result);
            $row = mysqli_fetch_row($result);
            if($filas2>0){
                echo "registrar_GF_FACTURA.php?factura=".md5($factura)."&cnt=".md5($row[0])."&pptal=".md5($row[1]);
            }else{
                echo "registrar_GF_FACTURA.php?factura=".md5($factura);
            }
        }
    break;
    case 37:
        $banco = $_POST['banco'];
        $fechaT = ''.$mysqli->real_escape_string(''.$_POST['fecha'].'').'';
        $valorF = explode("/",$fechaT);
        $fecha =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
        $descripcion = $_POST['descripcion'];
        $valor = $_POST['valor'];
        $valorEjecucion = $_POST['valorEjecucion'];
        $comprobante = $_POST['comprobante'];
        #Consulta de cuenta bancaria para tomar la cuenta, la naturaleza
        $sqlcuentaBancaria = "SELECT ctb.cuenta,ct.naturaleza FROM gf_cuenta_bancaria ctb
        LEFT JOIN gf_cuenta ct ON ct.id_unico = ctb.cuenta
        WHERE ctb.id_unico = $banco";
        $rsCuentaB = $mysqli->query($sqlcuentaBancaria);
        $row = mysqli_fetch_row($rsCuentaB);
        if(empty($_POST['tercero'])){
            $tercero = '"2"';
        }else{
            $tercero = '"'.$mysqli->real_escape_string(''.$_POST['tercero'].'').'"';
        }
        if(empty($_POST['proyecto'])){
            $proyecto = '"2147483647"';
        }else{
            $proyecto = '"'.$mysqli->real_escape_string(''.$_POST['proyecto'].'').'"';
        }
        if(empty($_POST['centro'])){
            $centro = "12";
        }else{
            $centro = '"'.$mysqli->real_escape_string(''.$_POST['centro'].'').'"';
        }
        if($row[1]==1){
            $valor = $valor *-1;
        }
        $sql = "INSERT INTO gf_detalle_comprobante(fecha,descripcion,valor,valorejecucion,comprobante,cuenta,naturaleza,tercero,proyecto,centrocosto,detalleafectado) VALUES ($fecha,'$descripcion',$valor,$valorEjecucion,$comprobante,$row[0],$row[1],$tercero,$proyecto,$centro,NULL)";
        $result = $mysqli->query($sql);
        echo json_encode($result);
    break;
    case 38:
    ############################################################################################################################################
    # Validación de variables
    #
    ############################################################################################################################################
        if(!empty($_POST['mes']) && !empty($_POST['cuenta'])){
            ####################################################################################################################################
            # Captura de variables
            #
            #####################################################################################################################################
            $mes = $_POST['mes'];
            $cuenta = $_POST['cuenta'];
            ####################################################################################################################################
            # Consulta de conciliacion bancaria
            #
            #####################################################################################################################################
            $sql = "SELECT  ptc.id_unico
                    FROM    gf_partida_conciliatoria ptc
                    WHERE   ptc.id_cuenta = $cuenta AND ptc.mes = $mes";
            $result = $mysqli->query($sql);
            $idP = mysqli_fetch_row($result);
            $cantidad = mysqli_num_rows($result);
            if($cantidad == 1){
                echo 'registrar_GF_PARTIDA_CONCILIATORIA.php?idPartida='.md5($idP[0]);
            }else{
                echo '';
            }
        }
    break;
    case 39:
        ############################################################################################################################################
        # Captura de variables
        #
        ############################################################################################################################################
        $id = $_POST['id'];
        #$form = $_POST['form'];
        $mes = $_POST['mes'];
        #$cuenta = $_POST['cuenta'];
        ############################################################################################################################################
        # Consultamos si la el detalle ya tiene el indicador
        #
        ############################################################################################################################################
        $sql = "SELECT conciliado FROM gf_detalle_comprobante WHERE id_unico = $id";
        $result = $mysqli->query($sql);
        $conciliado = mysqli_fetch_row($result);
        ############################################################################################################################################
        # Validación de valor obtenido en la consulta
        #
        ############################################################################################################################################
        if(empty($conciliado[0])){
            ########################################################################################################################################
            # Actualizamos cuando el valor es vacio, entonces se coloca conciliado y mes
            #
            ########################################################################################################################################
            $sqlC = "UPDATE gf_detalle_comprobante SET conciliado = 1, periodo_conciliado = $mes WHERE id_unico = $id";
            $resultC = $mysqli->query($sqlC);
        }else{
            ########################################################################################################################################
            # Actualizamos cuando el valor no es vacio y se cambia por null
            #
            ########################################################################################################################################
            $sqlC = "UPDATE gf_detalle_comprobante SET conciliado = NULL, periodo_conciliado = NULL WHERE id_unico = $id";
            $resultC = $mysqli->query($sqlC);
        }
    break;
    case 40:
    ############################################################################################################################################
    # Captura de variables
    #
    ############################################################################################################################################
    $comprobante = $_POST['comprobante'];
    ############################################################################################################################################
    # Validación de variable no vacia
    #
    ############################################################################################################################################
    if(!empty($comprobante)){
        ########################################################################################################################################
        # Cargamos la variable de session
        #
        ########################################################################################################################################
        $_SESSION['idNumeroC'] = $comprobante;
    }else{
        ########################################################################################################################################
        # Vaciamos la variable de session
        #
        ########################################################################################################################################
        $_SESSION['idNumeroC'] = "";
    }
    break;
    case 41:
    ############################################################################################################################################
    # Captura de variables
    #
    ############################################################################################################################################
    $tipo = $_POST['tipo'];
    ############################################################################################################################################
    # Impresión de opcion vacia
    #
    ############################################################################################################################################
    echo "<option value=''>Informe</option>";
    ############################################################################################################################################
    # Consulta de informes por tipo
    #
    ############################################################################################################################################
    $sql = "SELECT id,nombre FROM gn_informe WHERE tipo_informe = $tipo";
    $result = $mysqli->query($sql);
    while ($row = mysqli_fetch_row($result)) {
        ########################################################################################################################################
        # Impresión de valores
        #
        ########################################################################################################################################
        echo "<option value=".$row[0].">".ucwords(mb_strtolower($row[1]))."</option>";
    }
    break;
    case 42:
        ############################################################################################################################################
        # Captura de variables
        #
        ############################################################################################################################################
        $informe = $_POST['informe'];
        ############################################################################################################################################
        # Validación de no vacio
        #
        ############################################################################################################################################
        if(!empty($informe)){
            echo 'GN_HOMOLOGACIONES.php?report='.md5($informe);
        }
    break;
    case 43:
        /* Contenido ajax */
        if(!empty($_POST['report'])){      //Validamos que si la variable report no viene vacia
            $idReport = $_POST['report'];  //id del report
            $limit    = $_POST['limit'];   //Limite de consulta
            $offset   = $_POST['offset'];  //Final de consulta
            //Inicializamos las variables en 0 o vacio
            $x        = 0;
            $html     = "";
            $colO = "";                       //Nombre de columna origen
            $colD = 0;                        //Contador de columnas destino
            $columnasDestino = "";            //Nombres de columnas Destino
            $tablaOrigen     = "";            //Nombres de tabla Origen
            $tablasDestino   = "";            //Nombres de tablas Destino
            $consultasTablaD = "";            //Consultas de tabla destino
            $idTH            = "";            //Id de las tablas homologables
            $consultaTablaO  = "";            //Consulta de la tabla de origen
            //Impresión de cabeza de la tabla
            $html.= "<thead>";
            $html.= "<tr>";
            //Consulta para obtener columna de origen por informe
            $sqlColO = "SELECT tbH.columna_origen,tbH.tabla_origen,tbH.select_table_origen
                        FROM   gn_tabla_homologable tbH
                        WHERE  tbH.informe = $idReport";
            $resultColO = $mysqli->query($sqlColO);
            $rowColO    = mysqli_fetch_row($resultColO);
            //Asiganción de valores devueltos por consulta
            $colO           = $rowColO[0];    //Captura de columna origen
            $tablaOrigen    = $rowColO[1];    //Captura del nombre de la tabla
            $consultaTablaO = $rowColO[2];    //Captura de select de la tabla origen
            //Impresión de valores devueltos por la consulta
            $html.= "<th class=\"cabeza cursor\" title=\"Tabla de Origen : ".ucwords($tablaOrigen)."\">".ucfirst(ucwords($colO))."</th>";
            //Consulta para obtener columnas de destino por informe
            $sqlTableH = "SELECT  tbH.columna_destino,tbH.tabla_destino,tbH.select_table_destino,tbH.id
                          FROM    gn_tabla_homologable tbH
                          WHERE   tbH.informe = $idReport";
            $resultTableH = $mysqli->query($sqlTableH);
            while ($rowTH = mysqli_fetch_row($resultTableH)) {  //Impresión de valores devueltos por la consulta
                $colD++;  //Contador de columnas de destino
                #Impresión de Nombres de columna destino
                $html.= "<th class=\"cabeza cursor danger\" style='width: 100px'>".ucfirst(ucwords($rowTH[0].PHP_EOL.'(Tabla: '.$rowTH[1].')'))."</th>";
                $columnasDestino.= $rowTH[0].",";   //Captura de columnas destino
                $tablasDestino  .=   $rowTH[1].","; //Captura de tablas destino
                $consultasTablaD.= $rowTH[2].";";   //Captura de consultas de tabla destino
                $idTH.=$rowTH[3].",";               //Captura de ids de tabla homologable
            }
            $html.= "<tr>";
            $html.= "</thead>";
            #Impresión de cuerpo de tabla
            $html.= "<tbody>";
            $columnasDestino = substr($columnasDestino,0,strlen($columnasDestino)-1);   //Quitamos la ultima coma
            $tablasDestino   = substr($tablasDestino,0,strlen($tablasDestino)-1);       //Quitamos la ultima coma
            $idTH            = substr($idTH,0,strlen($idTH)-1);                         //Quitamos la ultima coma
            $columnD         = explode(",",$columnasDestino);                           //Array de columnas destino
            $tbD             = explode(",", $tablasDestino);                            //Array de tablas destino
            $selectDestino   = explode(";",$consultasTablaD);                           //Array de selects destino
            $idTablaH        = explode(",", $idTH);                                     //Array de las id de tabla homologable
            //Consulta de tabla origen
            if(!empty($consultaTablaO)){
                $sqlT = "$consultaTablaO LIMIT $limit OFFSET $offset";
                $resultT = $mysqli->query($sqlT);
                $cantidad = mysqli_num_rows($resultT);
                $y = 0; //Contador de filas
                while($rowT = mysqli_fetch_row($resultT)){  //Impresión de valores devueltos por la consulta
                    ++$y; //Contamos las filas
                    #Impresión de filas de la tabla
                    $html.= "<tr>";
                    $html.= "<td class='info' style=\"width:250px\"><span name=\"Origen$rowT[0]\" id=\"Origen$rowT[0]\">".(ucwords(mb_strtolower($rowT[1])))."</span></td>";       //Impresión de campo conocido, de lta tabla origen
                    for ($a=0; $a <= $colD-1; ++$a) {                   //Ciclo de impresión para select
                        $html.= "<td style='width:150px'>";
                        $x++;
                        $html.= "<input type=\"hidden\" id=\"txt$columnD[$a]$x\" value=\"\">";
                        $html.= "<select class=\"select2 form-control col-sm-1\" onchange=\"guardarHomologacion($rowT[0],this.value,".$idTablaH[$a].",".$idTablaH[$a].","."$('#txt".$columnD[$a].$x."').val()".",'txt".$columnD[$a].$x."');\" name=\"$columnD[$a]$y\" id=\"$columnD[$a]$y\" style=\"width:150px;align:center\">";     //Campo select generado de manera dinamica
                        $html.= "<option value=''>".ucfirst(ucwords($columnD[$a]))."</option>"; //opción con el nombre del campo
                        $sqlTD       = $selectDestino[$a];             //Consulta de la tabla destino
                        $resultTD    = $mysqli->query($sqlTD);
                        while($rowTD = mysqli_fetch_row($resultTD)){        //Impresión de valores
                            #Consulta para saber cuales registros o valores estan en gn_homologaciones
                            if(!empty($rowTD[0])){
                                $sqlHom = "SELECT    hom.id FROM gn_homologaciones hom
                                           LEFT JOIN gn_tabla_homologable th1 on hom.origen = th1.id
                                           LEFT JOIN gn_informe i on th1.informe = i.id
                                           WHERE hom.id_origen  = '$rowT[0]'
                                           AND   hom.id_destino = '$rowTD[0]'
                                           AND   hom.origen     = $idTablaH[$a]
                                           AND   hom.destino    = $idTablaH[$a]
                                           AND   th1.informe    = $idReport";
                                $resultHom = $mysqli->query($sqlHom);
                                $c = mysqli_fetch_row($resultHom);                  //Se carga el valor de la consulta
                                if(!empty($c[0])){                             //Se valida que es diferente de vacio
                                    $pos = strpos($selectDestino[$a],"order"); //Buscamos la palabra order by en la Consulta
                                    if(!empty($pos)) {//Validamos que no venga vacia
                                        $str1 = substr($selectDestino[$a],0,$pos);  //Tomamos la Consulta desde la posición 0 hasta la posicion en la que se encontro la palabra
                                        $str2 = substr($selectDestino[$a],$pos);    //Tomamos la consulta desde la posición en que se hayo la palbra
                                        $sqlTD1 = $str1." WHERE id_unico = '$rowTD[0]' $str2"; //Armamos nuestra query para obtener el valor especifico
                                    }else{
                                        $sqlTD1 = $selectDestino[$a]." WHERE id_unico = '$rowTD[0]'";    //Consulta de la tabla destino cuando existe valor en la tabla gn_homologaciones
                                    }
                                    $resultTD1 = $mysqli->query($sqlTD1);
                                    $rowTD1 = mysqli_fetch_row($resultTD1);//Carga de valores
                                    $html.= "<option value=".$rowTD1[0]." selected>".ucwords(mb_strtolower($rowTD1[1]))."</option>";//Option con el valor optenido cuando exsite en la base de datos
                                    $html.= "<script>\n";
                                    $html.= "$(document).ready(function(){\n";
                                    $html.= "var fila$x = $c[0];\n";
                                    $html.= "$(\"#txt$columnD[$a]$x\").val(fila$x);\n";
                                    $html.= "});\n";
                                    $html.= "</script>";
                                }
                            }
                            $html.= "<option value=".$rowTD[0].">".ucwords(mb_strtolower($rowTD[1]))."</option>"; //Opción impresa
                        }
                        $html.= "</select>\n";   //Fin del select
                        $html.= "</td>\n";       //Fin de la celda
                    }
                    $html.= "</tr>\n";           //Fin de la fila
                }
            }else{
                $html .= "<h1>No ha configuración</h1>";
            }
            $html.= "</tbody>\n";            //Fin del cuerpo de la tabla
            $html.= "<script>\n";            //Script para carga la libreria select2 en los combos o campos de seleccion
            $html.= "$('.select2').select2({";
            $html.= "allowClear:true";
            $html.= "});";
            $html.= "</script>\n";
            echo $html;
        }
    break;
    case 44:
        if(!empty($_REQUEST['report'])){ //Validamos que si la variable report no viene vacia
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //colO para capturar en un string el nombre de la columna de origen y tablaOrigen para capturar el nombre de tabla de origen
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $idReport = $_REQUEST['report'];
            $colO = "";                 //Nombre de columna origen
            $tablaOrigen = "";          //Nombres de tabla Origen
            $jsondata = array();
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Consulta para obtener columna de origen por informe
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $sqlColO = "SELECT  select_table_origen
                        FROM    gn_tabla_homologable tbH
                        WHERE   tbH.informe = $idReport";
            $resultColO = $mysqli->query($sqlColO);
            $rowColO = $resultColO->fetch_row();
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Captura de valores devueltos en la consulta
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $colO = $rowColO[0];        //Captura de columna origen
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Consulta de tabla origen
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $sqlT    = $colO;
            $resultT = $mysqli->query($sqlT);
            $fila    = $resultT->num_rows;
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Impresión de valores retornados
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            echo ($fila);
        }
        break;
    case 45:
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Capturamos los valores para registrar
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $idDestino = $_POST['idDestino'];
        $idOrigen  = $_POST['idOrigen'];
        $origen    = $_POST['origen'];
        $destino   = $_POST['destino'];
        $id_hom    = $_POST['id_hom'];
        if(!empty($id_hom)){
            if(empty($idDestino)){
                $sql = "DELETE FROM gn_homologaciones WHERE id = '$id_hom'";
                $result = $mysqli->query($sql);
                echo json_encode($result.";".'');
            }else{
                $sql = "UPDATE gn_homologaciones SET id_destino = '$idDestino' WHERE id = '$id_hom'";
                $result = $mysqli->query($sql);
                echo json_encode($result.";".$id_hom);
            }
        }else{
            $insert = "INSERT INTO gn_homologaciones(id_origen,id_destino,origen,destino) 
                    VALUES('$idOrigen','$idDestino','$origen','$destino')";
            $result = $mysqli->query($insert);
            $existe = "SELECT MAX(id) FROM gn_homologaciones
                       WHERE id_origen  = '$idOrigen'
                       AND   id_destino = '$idDestino'
                       AND   origen     = '$origen'
                       AND   destino    = '$destino' ";
            $resultE = $mysqli->query($existe);
            $rowE = mysqli_fetch_row($resultE);
            echo json_encode($result.";".$rowE[0]);
        }
        break;
    case 46:
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Capturamos los valores para modificar el detalle de banco en ingreso contable
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $id = $_POST['id'];
        $debito = $_POST['debito'];
        $credito = $_POST['credito'];
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Consultamos la naturaleza que se relaciona al detalle
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $sqlN = "SELECT dtc.naturaleza FROM gf_detalle_comprobante dtc WHERE dtc.id_unico = $id";
        $resultN = $mysqli->query($sqlN);
        $nat = mysqli_fetch_row($resultN);
        $naturaleza = $nat[0];
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Validamos el ingreso de valores
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if (empty($_POST['debito']) || $_POST['debito']=='0') {
            if(!empty($_POST['credito'])) {
                if($naturaleza == 1) {
                    $valor = $_POST['credito']*-1;
                }else{
                    $valor = $_POST['credito'];
                }
            }
        }

        if (empty($_POST['credito']) || $_POST['credito']=='0') {
            if(!empty($_POST['debito'])) {
                if($naturaleza == 2) {
                    $valor = $_POST['debito']*-1;
                }else{
                    $valor = $_POST['debito'];
                }
            }
        }
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Actualizamos
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $sql = "UPDATE gf_detalle_comprobante SET valor = $valor WHERE id_unico = $id";
        $result = $mysqli->query($sql);
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Imprimos el valor retornado por la consulta
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        echo json_encode($result);
        break;
    case 47:
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Declaramos un contador, para obtener la cantidad de cuentas en cuenta credito, y un array x para obtener los detalles que son cuentas debito
        // y que tienen cuenta credito en concepto rubro cuenta
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $h = 0;                     //Contador
        $x = array();               //Array de captura de los de detalles
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Función para generar causación, Validamos que el valor recibido no este vacio
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if(!empty($_POST['comprobante'])) {
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Capturamos el id del comprobante
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $comC = $_POST['comprobante'];
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Consultamos que tenga un tipo de comprobante el cual este homologado
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $sql = "SELECT      tp.tipo_comp_hom
                    FROM        gf_comprobante_cnt cnt
                    LEFT JOIN   gf_tipo_comprobante tp
                    ON          cnt.tipocomprobante = tp.id_unico
                    WHERE       cnt.id_unico        = $comC";
            $result = $mysqli->query($sql);
            $tipo_hom = $result->fetch_row();
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Validamos que el tipo de comprobante homologado retornado no este vacio
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if(!empty($tipo_hom[0])){
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //Consultamos si existe algun comprobante relacionado al detalle
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $sqlRC = "  SELECT DISTINCT cnt1.id_unico as 'crn',
                                        dtpp.comprobantepptal
                            FROM        gf_detalle_comprobante dtc
                            LEFT JOIN   gf_comprobante_cnt cnt              ON dtc.comprobante      = cnt.id_unico
                            LEFT JOIN   gf_detalle_comprobante dtc1         ON dtc1.detalleafectado = dtc.id_unico
                            LEFT JOIN   gf_comprobante_cnt cnt1             ON dtc1.comprobante     = cnt1.id_unico
                            LEFT JOIN   gf_detalle_comprobante_pptal dtpp   ON dtpp.id_unico        = dtc1.detallecomprobantepptal
                            WHERE       cnt.id_unico = $comC
                            AND         cnt1.tipocomprobante = $tipo_hom[0]";
                $resultRc = $mysqli->query($sqlRC);
                $cantidad = mysqli_num_rows($resultRc);
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //Si existe realizara registro en detalle
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                if($cantidad > 0){
                    $id_crn = $resultRc->fetch_row();
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    //Consultamos los detalles y obtenemos la cuentas y validamos si están registradas en concepto_rubro_cuenta como debito
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $sqlD = "SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $comC AND detallecomprobantepptal IS NOT NULL";
                    $resultD = $mysqli->query($sqlD);
                    while ($rowD = $resultD->fetch_row()) {
                        $sqlV = "SELECT valor FROM gf_detalle_comprobante WHERE id_unico = $rowD[0]";
                        $resV = $mysqli->query($sqlV);
                        $rowV = mysqli_fetch_row($resV);
                        $valorDtc = abs($rowV[0]);
                        $sqlDDD = "SELECT dtc.id_unico, dtc.valor FROM gf_detalle_comprobante dtc
                                    LEFT JOIN gf_comprobante_cnt cnt ON dtc.comprobante = cnt.id_unico
                                    WHERE dtc.detalleafectado = $rowD[0] AND cnt.tipocomprobante = $tipo_hom[0]";
                        $resultDDD = $mysqli->query($sqlDDD);
                        if(mysqli_num_rows($resultDDD)  == 0) {
                            $sqlDCT = "SELECT cuenta FROM gf_detalle_comprobante WHERE id_unico = $rowD[0]";
                            $resultDTC = $mysqli->query($sqlDCT);
                            $rowDTC = mysqli_fetch_row($resultDTC);
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            //Consultamos las cuentas en los detalles, esten en concepto_rubro_cuenta como cuenta debito
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            $sqlCR = "  SELECT  cuenta_credito
                                        FROM    gf_concepto_rubro_cuenta
                                        WHERE   cuenta_debito = $rowDTC[0]";
                            $resultCR = $mysqli->query($sqlCR);
                            $rowCR = $resultCR->fetch_row();
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            //Validamos que los id de cuenta_debito y cuenta_credito sean diferentes
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            if($rowDTC[0] !== $rowCR[0]){
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                //Validamos que la consulta retorne valores mayores que 0
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                 if(($filasCR = $resultCR->num_rows) > 0){
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    //Consultamos los valores del detalle
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    $sqlDePtall = " SELECT      dtp.descripcion,
                                                                dtp.rubroFuente,
                                                                dtp.conceptoRubro,
                                                                dtp.tercero,
                                                                dtp.proyecto,
                                                                dtp.valor,
                                                                dtp.id_unico
                                                    FROM        gf_detalle_comprobante_pptal dtp
                                                    LEFT JOIN   gf_detalle_comprobante dtc ON dtc.detallecomprobantepptal = dtp.id_unico
                                                    WHERE       dtc.id_unico = $rowD[0]";
                                    $resultDePtall = $mysqli->query($sqlDePtall);
                                    $rowDePtall = mysqli_fetch_row($resultDePtall);

                                    $id_d = $rowD[0];
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    //Validamos que el valor del detalle sea negativo y se convierta a positivo
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    if($rowDePtall[5]<0){
                                        $valorDP = $rowDePtall[5]*-1;
                                    }else{
                                        $valorDP = $rowDePtall[5];
                                    }
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    //Insertamos los valores en el detalle comprobante pptal
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    $sqlIND = "INSERT INTO gf_detalle_comprobante_pptal(descripcion,
                                                            valor,
                                                            rubroFuente,
                                                            conceptoRubro,
                                                            tercero,
                                                            proyecto,
                                                            comprobantepptal)
                                                    VALUES  ('$rowDePtall[0]',
                                                            $valorDP,
                                                            $rowDePtall[1],
                                                            $rowDePtall[2],
                                                            $rowDePtall[3],
                                                            $rowDePtall[4],
                                                            $rowDePtall[6]);";
                                    $resultIND = $mysqli->query($sqlIND);
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    //Consultamos el ultimo id detalle insertado
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    $idDPT = "NULL";
                                    if(!empty($id_crn[1])){
                                        $sqlULPPP = "SELECT MAX(id_unico)
                                                    FROM    gf_detalle_comprobante_pptal
                                                    WHERE   comprobantepptal = $id_crn[1]";
                                        $resultPPP = $mysqli->query($sqlULPPP);
                                        $idDetallePp = mysqli_fetch_row($resultPPP);
                                        $idDPT = $idDetallePp[0];
                                    }
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    //Consultamos los valores del detalle cnt
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    $sqlDt = "SELECT    dtc.fecha,
                                                        dtc.descripcion,
                                                        dtc.valor,
                                                        dtc.cuenta,
                                                        dtc.tercero,
                                                        dtc.proyecto,
                                                        dtc.centrocosto
                                            FROM        gf_detalle_comprobante dtc
                                            WHERE       dtc.id_unico = $id_d";
                                    $resultDt = $mysqli->query($sqlDt);
                                    $rowDt = $resultDt->fetch_row();
                                    $valor = abs($rowDt[2]);                    #Obtenemos el valor absoluto del valor en el detalle
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    //Realizamos insertado de datos con cuenta debitto
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    $sqlDD = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, valor, cuenta, naturaleza, tercero, proyecto, centrocosto, comprobante, detalleafectado, detallecomprobantepptal)  VALUES ('$rowDt[0]', '$rowDt[1]', $valorDtc, $rowDt[3], 1, $rowDt[4], $rowDt[5], $rowDt[6], $id_crn[0], $id_d, $idDPT);";
                                    $resultDD = $mysqli->query($sqlDD);
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    //Consultamos el id de la cuenta credito
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    $sqlCR = "  SELECT  cuenta_credito
                                                FROM    gf_concepto_rubro_cuenta
                                                WHERE   cuenta_debito = $rowDt[3]";
                                    $resultCR = $mysqli->query($sqlCR);
                                    $rowCR = $resultCR->fetch_row();
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    //Realizamos insertado de datos a cuenta credito
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    $sqlDC = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, valor, cuenta, naturaleza, tercero, proyecto, centrocosto, comprobante, detalleafectado, detallecomprobantepptal)  VALUES ('$rowDt[0]', '$rowDt[1]', $valorDtc, $rowCR[0], 2, $rowDt[4], $rowDt[5], $rowDt[6], $id_crn[0], $id_d, $idDPT)";
                                    $resultDC = $mysqli->query($sqlDC);
                                }
                            }
                        }
                    }
                } else{
                    $param = $_SESSION['anno'];
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    //Consultamos los detalles y obtenemos la cuentas y validamos si están registradas en concepto_rubro_cuenta como debito
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $sqlD = "SELECT dtc.id_unico,
                                    dtc.cuenta
                            FROM    gf_detalle_comprobante dtc
                            WHERE   dtc.comprobante = $comC";
                    $resultD = $mysqli->query($sqlD);
                    while ($rowD = $resultD->fetch_row()) {
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //Consultamos las cuentas en los detalles, esten en concepto_rubro_cuenta como cuenta debito relaciondas a los detalles
                        //del comprobante
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $sqlCR = "SELECT DISTINCT cuenta_credito FROM gf_concepto_rubro_cuenta WHERE cuenta_debito = $rowD[1]";
                        $resultCR = $mysqli->query($sqlCR);
                        $rowCR = mysqli_fetch_row($resultCR);
                        $filasCR = mysqli_num_rows($resultCR);
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //Validamos que la cuenta_debito y credito no sean iguales
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        if($rowD[1] !== $rowCR[0]){
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            //Validamos que la consulta retorne valores mayores que 0
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            if($filasCR !== 0) {
                                ++$h;                           //Preincrementamos el contador
                                $x[]=$rowD[0];                  //Capturamos el id del detalle
                            }
                        }
                    }
                    // /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    // //Validamos que h sea mayor que 0
                    // /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    if($h > 0){
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //Consultamos los datos del comprobante
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $sqlC = "SELECT cnt.fecha,
                                        cnt.tipocomprobante,
                                        cnt.numero,
                                        cnt.tercero,
                                        cnt.descripcion,
                                        cnt.estado,
                                        cnt.clasecontrato,
                                        cnt.numerocontrato,
                                        cnt.compania,
                                        cnt.parametrizacionanno
                                FROM    gf_comprobante_cnt cnt
                                WHERE   cnt.id_unico = $comC";
                        $resultC = $mysqli->query($sqlC);
                        $com = $resultC->fetch_row();
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //Consultamos si el tipo de comprobante homologado tiene un comprobante pptal
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $sqlTipoP = "SELECT comprobante_pptal FROM gf_tipo_comprobante WHERE id_unico = $tipo_hom[0]";
                        $resultTipoP = $mysqli->query($sqlTipoP);
                        $tipo_pptal = $resultTipoP->fetch_row();
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //Validamos que el tipo de comprobante presupuestal del comprobante homologado retorne algun valor
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //Se comento el registro presupuestal ya que la causaciòn no se esta realizando a presupuesto
                        if(!empty($tipo_pptal[0])){
                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            //Realizamos insert a comprobante pptal con el tipo de comprobante de reconocimiento
                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            $sqlPptal = "INSERT INTO gf_comprobante_pptal   (numero,
                                                                            fecha,
                                                                            fechavencimiento,
                                                                            descripcion,
                                                                            numerocontrato,
                                                                            parametrizacionanno,
                                                                            claseContrato,
                                                                            tipocomprobante,
                                                                            tercero,
                                                                            estado,
                                                                            responsable)
                                                    VALUES                  ('$com[2]',
                                                                            '$com[0]',
                                                                            '$com[0]',
                                                                            '$com[4]',
                                                                            '$com[7]',
                                                                            $param,
                                                                            NULLIF('$com[6]',0),
                                                                            $tipo_pptal[0],
                                                                            $com[3],
                                                                            1,
                                                                            $com[3])";
                            $resultPptal = $mysqli->query($sqlPptal);
                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            //Consultamos el ultimo comprobante pptal insertado
                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            $sqlXT = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE tipocomprobante = $tipo_pptal[0]";
                            $resultXT = $mysqli->query($sqlXT);
                            $id_pptal = $resultXT->fetch_row();
                        }
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //Verificamos que si existe algun comprobante que tenga el tipo
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $sqlR = "SELECT count(id_unico) FROM gf_comprobante_cnt WHERE tipocomprobante = $tipo_hom[0]";
                        $resultR = $mysqli->query($sqlR);
                        $cant = $resultR->fetch_row();
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //Validamos que el valor retornado sea 0, si es sero el contador de numero del tipo se inicializara en 0,de lo contrario se
                        //incrementara
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        if($cant[0]==0){
                            $num = date('Y').'000001';
                        }else{
                            $num = $cant[0]+1;
                        }
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //Realizamos insert a comprobante cnt con el tipo de comprobante de reconocimiento
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $sqlCnt = "INSERT INTO gf_comprobante_cnt  (fecha ,
                                                                tipocomprobante ,
                                                                numero ,
                                                                tercero ,
                                                                descripcion ,
                                                                estado ,
                                                                clasecontrato ,
                                                                numerocontrato ,
                                                                compania ,
                                                                parametrizacionanno)
                                                    VALUES      ('$com[0]',
                                                                '$tipo_hom[0]' ,
                                                                '$com[2]' ,
                                                                '$com[3]' ,
                                                                '$com[4]' ,
                                                                '$com[5]' ,
                                                                NULLIF('$com[6]',0) ,
                                                                NULLIF('$com[7]',0) ,
                                                                '$com[8]' ,
                                                                '$com[9]')";
                        $resultCnt = $mysqli->query($sqlCnt);
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //Consultamos el ultimo comprobante registrado por el tipo
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $sqlRCnt = "SELECT MAX(id_unico) FROM gf_comprobante_cnt WHERE tipocomprobante = $tipo_hom[0] AND numero = $com[2]";
                        $resultRCnt = $mysqli->query($sqlRCnt);
                        $id_com = $resultRCnt->fetch_row();
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //Desplegamos el array, y consultamos el detalle, y nuevamente obtenemos el id de la cuenta, la cual consultamos nuevamente a
                        //conceto rubro cuenta y relizamos el ingreso de datos al detalle
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        for ($a = 0;$a < count($x);$a++) {
                            $id_d = $x[$a];
                            $sqlDt = "SELECT    dtc.fecha,
                                                dtc.descripcion,
                                                dtc.valor,
                                                dtc.cuenta,
                                                dtc.tercero,
                                                dtc.proyecto,
                                                dtc.centrocosto,
                                                dtc.detallecomprobantepptal
                                    FROM        gf_detalle_comprobante dtc
                                    WHERE       dtc.id_unico = $id_d";
                            $resultDt = $mysqli->query($sqlDt);
                            $rowDt = $resultDt->fetch_row();
                            $valor = abs($rowDt[2]);                    #Obtenemos el valor absoluto del valor en el detalle
                            $idDpp = "NULL";
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            //Validamos que haya un comprobante pptal
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            if(!empty($id_pptal[0])){
                                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                //validamos y consultamos los valores del detalle del comprobante pptal
                                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                if(!empty($rowDt[7])){
                                    $sqlDePtall = " SELECT  descripcion,
                                                            rubroFuente,
                                                            conceptoRubro,
                                                            tercero,
                                                            proyecto
                                                    FROM    gf_detalle_comprobante_pptal
                                                    WHERE   id_unico = $rowDt[7]";
                                    $resultDePtall = $mysqli->query($sqlDePtall);
                                    $rowDePtall = mysqli_fetch_row($resultDePtall);
                                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    //Insertamos los valores
                                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    $sqlIND = "INSERT INTO  gf_detalle_comprobante_pptal(descripcion,
                                                            valor,
                                                            rubroFuente,
                                                            conceptoRubro,
                                                            tercero,
                                                            proyecto,
                                                            comprobantepptal)
                                                    VALUES  ('$rowDePtall[0]',
                                                            $valor,
                                                            $rowDePtall[1],
                                                            $rowDePtall[2],
                                                            $rowDePtall[3],
                                                            $rowDePtall[4],
                                                            $id_pptal[0])";
                                    $resultIND = $mysqli->query($sqlIND);
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    //Consultamos el ultimo id detalle insertado
                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    $sqlULPPP = "SELECT MAX(id_unico)
                                                FROM    gf_detalle_comprobante_pptal
                                                WHERE   comprobantepptal = $id_pptal[0]";
                                    $resultPPP = $mysqli->query($sqlULPPP);
                                    $idDetallePp1 = mysqli_fetch_row($resultPPP);
                                    $idDpp = $idDetallePp1[0];
                                }
                            }
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            //Realizamos insertado de datos con cuenta debitto
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            $sqlDD = "INSERT INTO gf_detalle_comprobante    (fecha,
                                                                            descripcion,
                                                                            valor,
                                                                            cuenta,
                                                                            naturaleza,
                                                                            tercero,
                                                                            proyecto,
                                                                            centrocosto,
                                                                            comprobante,
                                                                            detalleafectado,
                                                                            detallecomprobantepptal)
                                                                VALUES      ('$rowDt[0]',
                                                                            '$rowDt[1]',
                                                                            $valor,
                                                                            $rowDt[3],
                                                                            1,
                                                                            $rowDt[4],
                                                                            $rowDt[5],
                                                                            $rowDt[6],
                                                                            $id_com[0],
                                                                            $id_d,
                                                                            $idDpp)";
                            $resultDD = $mysqli->query($sqlDD);
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            //Consultamos el id de la cuenta credito
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            $sqlCR = "  SELECT  cuenta_credito
                                        FROM    gf_concepto_rubro_cuenta
                                        WHERE   cuenta_debito = $rowDt[3]";
                            $resultCR = $mysqli->query($sqlCR);
                            $rowCR = $resultCR->fetch_row();
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            //Realizamos insertado de datos a cuenta credito
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            $sqlDC = "INSERT INTO gf_detalle_comprobante    (fecha,
                                                                            descripcion,
                                                                            valor,
                                                                            cuenta,
                                                                            naturaleza,
                                                                            tercero,
                                                                            proyecto,
                                                                            centrocosto,
                                                                            comprobante,
                                                                            detalleafectado,
                                                                            detallecomprobantepptal)
                                                                VALUES      ('$rowDt[0]',
                                                                            '$rowDt[1]',
                                                                            $valor,
                                                                            $rowCR[0],
                                                                            2,
                                                                            $rowDt[4],
                                                                            $rowDt[5],
                                                                            $rowDt[6],
                                                                            $id_com[0],
                                                                            $id_d,
                                                                            $idDpp)";
                            $resultDC = $mysqli->query($sqlDC);
                        }
                    }
                }
            }
        }
        echo "OK";
        break;
    case 48:
        $pago = $_POST['pago'];
        //Consultamos su relación con cnt y pptal
        $sql = "SELECT cn.id_unico as cnt, cp.id_unico as ptal 
                FROM gp_pago p 
                LEFT JOIN gp_detalle_pago dp ON p.id_unico = dp.pago 
                LEFT JOIN gf_detalle_comprobante dc ON dc.id_unico = dp.detallecomprobante 
                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN gf_detalle_comprobante_pptal dpt ON dc.detallecomprobantepptal = dpt.id_unico 
                LEFT JOIN gf_comprobante_pptal cp ON dpt.comprobantepptal = cp.id_unico 
                WHERE p.id_unico = $pago";
        $result = $mysqli->query($sql);
        $row = $result->fetch_row();
        if(!empty($row[0])){
            //Imprimimos la url para que se redireccione la pagina
            echo "registrar_GF_RECAUDO_FACTURACION_2.php?recaudo=".md5($pago)."&cnt=".md5($row[0])."&pptal=".md5($row[1]);
        } else {
            #Buscar Por Número Y Tipo 
            $sql2 = "SELECT cn.id_unico as cnt, cp.id_unico as ptal 
                FROM gp_pago p 
                LEFT JOIN gp_detalle_pago dp ON p.id_unico = dp.pago 
                LEFT JOIN gp_tipo_pago tp ON p.tipo_pago = tp.id_unico 
                LEFT JOIN gf_tipo_comprobante tc ON tp.tipo_comprobante = tc.id_unico 
                LEFT JOIN gf_comprobante_cnt cn ON cn.tipocomprobante = tc.id_unico AND cn.numero = p.numero_pago 
                LEFT JOIN gf_comprobante_pptal cp ON cp.tipocomprobante = tc.comprobante_pptal AND cp.numero = p.numero_pago 
                WHERE p.id_unico =$pago";
            $result2 = $mysqli->query($sql2);
            $row2 = $result2->fetch_row();
            if(!empty($row2[0])){
                //Imprimimos la url para que se redireccione la pagina
                echo "registrar_GF_RECAUDO_FACTURACION_2.php?recaudo=".md5($pago)."&cnt=".md5($row2[0])."&pptal=".md5($row2[1]);
            }else{
                echo "registrar_GF_RECAUDO_FACTURACION_2.php?recaudo=".md5($pago);
            }
            
        }
        break;
    case 49:
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Valiamos que la variable conceptos no se encuentre vacia
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if(!empty($_POST['conceptos'])) {
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Capturamos en una variable el array de condeptos envia
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $conceptos = $_POST['conceptos'];
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Hacemos un ciclo for para desplegar el array consultando el nombre, id del los conceptos que se seleccionaros
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            for ($a = 0;$a < count($conceptos); $a++) {
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //Consulta para obtener el nombre y el id del concepto recibido
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $sqlC = "SELECT id_unico,nombre FROM gp_concepto WHERE id_unico = $conceptos[$a]";
                $resultC = $mysqli->query($sqlC);
                $rowC = $resultC->fetch_row();
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //Imprimimos codigo html con el label que del concepto y el campo para especificarf que columnas toma el valor el concepto y su posible formula
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                echo "<div class=\"form-group\">";
                echo "<label class=\"col-sm-4 control-label\">".ucwords(mb_strtolower($rowC[1]))." :</label>";
                echo "<input type=\"text\" name=\"columna".$rowC[0]."\" id=\"columna".$rowC[0]."\" placeholder=\"Columna\" title=\"Columnas o formula de columnas para obtener el valor del concepto\" class=\"form-control col-sm-1\" style=\"width:250px\" required>";
                echo "</div>";
            }
        }
        break;
    case 50:
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Valiamos que la variable factura, cnt y pptal no se encuentre vacia
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if(!empty($_POST['factura']) && !empty($_POST['cnt']) && !empty($_POST['pptal'])) {
            $factura = $_POST['factura'];                   //id de factura
            $cnt = $_POST['cnt'];                           //cnt
            $pptal = $_POST['pptal'];                       //pptal
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Eliminamos los detalles de la factura
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $delf       =   "DELETE FROM gp_detalle_factura WHERE factura = $factura";
            $resultf    =   $mysqli->query($delf);
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Actualizamos los detalles del comprobante con detalleafectado = NULL
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $sqlDD = "UPDATE gf_detalle_comprobante SET detalleafectado = NULL WHERE comprobante = $cnt";
            $resultDD = $mysqli->query($sqlDD);
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Eliminamos los detalles del comprobante cnt
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $sqlC       =   "DELETE FROM gf_detalle_comprobante WHERE comprobante = $cnt";
            $resultc    =   $mysqli->query($sqlC);
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Eliminamos los detalles del comprobante pptal
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $delp       =   "DELETE FROM gf_detalle_comprobante_pptal WHERE comprobantepptal  = $pptal";
            $resultd    =   $mysqli->query($delp);
            echo json_encode($resultd);
        }
        break;
    case 51:
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Validamos que la variable fecha no este vacia
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if(!empty($_POST['tipo'])){
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Capturamos las variables enviadas
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $tipo = $_POST['tipo'];                                      //Tipo de comprobante
            $fecha1 = explode("/",$_POST['fecha']);                      //fecha
            $fecha1 = strtotime("$fecha1[2]-$fecha1[1]-$fecha1[0]");
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Consultamos las la fecha maxima del comprobante
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $sql = "SELECT MAX(fecha_factura) FROM gp_factura WHERE tipofactura = $tipo";
            $result = $mysqli->query($sql);
            $fecha = mysqli_fetch_row($result);
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Validamos que la consulta retorne algun valor
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if(!empty($fecha[0])){
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //Capturamos la fecha consultada
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $fecha2 = strtotime($fecha[0]);
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //Validamos que la fecha 1 no sea mayor a fecha 2
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                // if($fecha1 == $fecha2){
                //     echo json_encode(true);
                // }else{
                //     echo json_encode(false);
                // }
                echo json_encode(false);
            }
        }
        break;
    case 52:
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Validaamos que las variables id_cnt y id_pptal no esten vacias
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if(!empty($_POST['id_cnt']) && !empty($_POST['id_pptal'])){
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Capturamos las variables enviadas
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $id_cnt = $_POST['id_cnt'];
            $id_pptal = $_POST['id_pptal'];
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Consultamos el tipo de comprobante homologado del comprobante cnt
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $sqlTH = "SELECT    tp.tipo_comp_hom
                    FROM        gf_comprobante_cnt cnt
                    LEFT JOIN   gf_tipo_comprobante tp
                    ON          cnt.tipocomprobante = tp.id_unico
                    WHERE       cnt.id_unico        = $id_cnt";
            $resultTH = $mysqli->query($sqlTH);
            $c_th = mysqli_num_rows($resultTH);
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Validamos que la consulta retorne algún valor
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($c_th > 0){
                $tipo_hom = mysqli_fetch_row($resultTH);
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //Validamos que la variable $tipo_hom no este vacia
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                if(!empty($tipo_hom[0])){
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    //Consultamos si existe algun comprobante relacionado al detalle
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $sqlRC = "  SELECT      cnt1.id_unico as 'crn',
                                            dtp.comprobantepptal as 'pptal'
                                FROM        gf_detalle_comprobante dtc
                                LEFT JOIN   gf_detalle_comprobante dtc1       ON dtc1.detalleafectado         = dtc.id_unico
                                LEFT JOIN   gf_comprobante_cnt cnt1           ON dtc1.comprobante             = cnt1.id_unico
                                LEFT JOIN   gf_detalle_comprobante_pptal dtp  ON dtc1.detallecomprobantepptal = dtp.id_unico
                                WHERE       dtc.comprobante = $id_cnt
                                AND         cnt1.tipocomprobante = $tipo_hom[0]";
                    $resultRc = $mysqli->query($sqlRC);
                    $cantidad = mysqli_num_rows($resultRc);
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    //Validamos que exista un comprobante cnt y pptal relacionado
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    if($cantidad > 0){
                        $comp = $resultRc->fetch_row();
                        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //Validamos que realmente la consulta retorne un valor en la posicion 0, el cual es el id del comprobante cnt
                        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        if(!empty($comp[0])){
                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            //Eliminamos el detalle y el comprobante cnt
                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            $sqlDELCC = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $comp[0]";
                            $a = $mysqli->query($sqlDELCC);
                            $sqlDELCCN = "DELETE FROM gf_comprobante_cnt WHERE id_unico = $comp[0]";
                            $b = $mysqli->query($sqlDELCCN);
                            echo json_encode($b);
                        }
                        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        //Validamos que realmente la consulta retorne un valor en la posicion 1, el cual es el id del comprobante pptal
                        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        if(!empty($comp[1])){
                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            //Eliminamos el detalle y el comprobante pptal
                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            $sqlDELDPC ="DELETE FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $comp[1]";
                            $c = $mysqli->query($sqlDELDPC);
                            $sqlDELCPC = "DELETE FROM gf_comprobante_pptal WHERE id_unico = $comp[1]";
                            $result = $mysqli->query($sqlDELCPC);
                            echo json_encode($result);
                        }
                    }
                }
            }
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Eliminamos los detalles de los comprobantes de ingreso
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        break;
    case 53:
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //Inicializamos la variable num
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      $num = 0;
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //validamos que el tipo no esta vacio
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      if(!empty($_POST['tipo'])){
        $tipo = $_POST['tipo'];     //Capturamos el valor de tipo
        $sql = "SELECT MAX(TRIM(numero)) FROM gf_comprobante_pptal WHERE tipocomprobante = $tipo"; //Consulta para obtener el ultimo numero respecto al tipo
        $result = $mysqli->query($sql);
        $row = $result->fetch_row();
        if(empty($row[0])){
          $num = date('Y').'0000001';
        }else{
          $num = $row[0]+1;
        }
        echo trim($num);
      }
      break;
    case 54:
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //Validamos que el concepto no esta vacio
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      if(!empty($_POST['concepto'])) {
        $concepto = $_POST['concepto'];
        echo "<option value=\"\">Rubro</option>";
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Consultamos los rubros relacionados al rubro
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $sql = "SELECT    rb.id_unico, rb.codi_presupuesto, rb.nombre
                FROM      gf_concepto_rubro crb
                LEFT JOIN gf_rubro_pptal rb ON rb.id_unico = crb.rubro
                WHERE     crb.concepto = $concepto";
        $result = $mysqli->query($sql);
        while($row = mysqli_fetch_row($result)){
          echo "<option value=\"".$row[0]."\">".$row[1]." - ".$row[2]."</option>"; //Valores obtenidos
        }
      }
      break;
    case 55:
    $fuentes = "";
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //Validamos que el concepto no esta vacio
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      if(!empty($_POST['rubro'])) {
        $rubro = $_POST['rubro'];
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Consultamos los rubros relacionados al rubro
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $sql = "SELECT  fte.id_unico, fte.nombre
                FROM gf_rubro_fuente rb
                LEFT JOIN gf_fuente fte ON rb.fuente = fte.id_unico
                WHERE rb.rubro = $rubro";
        $result = $mysqli->query($sql);
        $conteo_r = mysqli_num_rows($result);
        if($conteo > 0){
          while($row = mysqli_fetch_row($result)){
            echo "<option value=\"".$row[0]."\">".$row[1]."</option>"; //Valores obtenidos
            $fuentes.=$row[0].",";
          }
          $fuentes = substr($fuentes,0,strlen($fuentes)-1);
          $sqlF = "SELECT id_unico,LOWER(nombre) FROM gf_fuente WHERE id_unico NOT IN ($fuentes) AND parametrizacionanno = $anno ";
          $resultF = $mysqli->query($sqlF);
          while($rowF = mysqli_fetch_row($resultF)){
            echo "<option value=\"".$rowF[0]."\">".ucwords($rowF[1])."</option>";
          }
        }else{
          echo "<option value=\"\">Fuente</option>";
          $sqlF = "SELECT id_unico,LOWER(nombre) FROM gf_fuente WHERE parametrizacionanno = $anno ";
          $resultF = $mysqli->query($sqlF);
          while($rowF = mysqli_fetch_row($resultF)){
            echo "<option value=\"".$rowF[0]."\">".ucwords($rowF[1])."</option>";
          }
        }
      }
      break;
    case 56:
      if(!empty($_POST['id_p'])) {
        $id = $_POST['id_p'];
        echo 'registrar_GF_RECAUDO_PPTAL.php?recaudo='.md5($id);
      }
      break;
    case 57:
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Validamos que la factura no este vacia
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if(!empty($_POST['factura'])) {
            $html = "";
            $factura = $_POST['factura'];
            $html .= "<option value=\"\">Concepto</option>";        //Imprimimos el campo vacio de concepto
            $sql = "SELECT    DISTINCT  
                    GROUP_CONCAT(DISTINCT (dtf.id_unico)), 
                    GROUP_CONCAT(DISTINCT(dtf.detallecomprobante)), 
                    dtp.conceptoRubro, cpt.nombre, cpt.id_unico 
                    FROM      gp_detalle_factura dtf
                    LEFT JOIN gf_detalle_comprobante dtc       ON dtf.detallecomprobante      = dtc.id_unico
                    LEFT JOIN gf_detalle_comprobante_pptal dtp ON dtc.detallecomprobantepptal = dtp.id_unico
                    LEFT JOIN gp_concepto                  cpt ON dtf.concepto_tarifa         = cpt.id_unico
                    WHERE     dtf.factura = $factura GROUP BY cpt.id_unico";
            $result = $mysqli->query($sql);
            while ($row = mysqli_fetch_row($result)) {
                $sqlP = "SELECT    (dtf.valor + dtf.iva + dtf.impoconsumo) - (dtp.valor + dtp.iva + dtp.impoconsumo), dtf.iva, dtp.iva
                         FROM      gp_detalle_pago dtp
                         LEFT JOIN gp_detalle_factura dtf ON dtp.detalle_factura = dtf.id_unico
                         WHERE     dtf.id_unico IN($row[0])";
                $rsP = $mysqli->query($sqlP);
                if(mysqli_num_rows($rsP) > 0) {
                    $rowP = mysqli_fetch_row($rsP);
                    if($rowP[0] > 0) {
                        $html .= "<option value='$row[4]'>".ucwords(mb_strtolower($row[3]))."</option>";
                    }
                } else {
                    $html .= "<option value='$row[4]'>".ucwords(mb_strtolower($row[3]))."</option>";
                }
            }
            echo $html;
        }
        break;
    case 58:
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Validamos que la factura no este vacia
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if(!empty($_POST['factura'])) {
            $factura = $_POST['factura'];
            $concepto = $_POST['concepto'];
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // Validamos que el concepto no este vacio
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if(!empty($_POST['concepto'])){
                $sql = "SELECT    dtf.valor + dtf.iva + dtf.impoconsumo, dtf.id_unico
                        FROM      gp_factura fat
                        LEFT JOIN gp_detalle_factura dtf           ON dtf.factura = fat.id_unico
                        LEFT JOIN gf_detalle_comprobante dtc       ON dtf.detallecomprobante = dtc.id_unico
                        LEFT JOIN gf_detalle_comprobante_pptal dtp ON dtc.detallecomprobantepptal = dtp.id_unico
                        WHERE     fat.id_unico         = $factura
                        AND       dtf.concepto_tarifa = $concepto";
                $result = $mysqli->query($sql);
                $row = mysqli_fetch_row($result);
                $conteo = mysqli_num_rows($result);
                if($conteo > 0){
                    $sqlDP = "SELECT valor + iva + impoconsumo FROM gp_detalle_pago WHERE detalle_factura = $row[1]";
                    $rsDP = $mysqli->query($sqlDP);
                    if(mysqli_num_rows($rsDP) > 0){
                        $rowDP = mysqli_fetch_row($rsDP);
                        echo $row[0] - $rowDP[0];
                    }else{
                        echo round($row[0]);
                    }
                }
            }else{
                $sumDF = 0; $sumDP = 0;
                $sqlDF = "SELECT dtf.id_unico, dtf.valor_total_ajustado
                          FROM   gp_detalle_factura dtf
                          WHERE  dtf.factura = $factura";
                $rsDF = $mysqli->query($sqlDF);
                while($rowDF = mysqli_fetch_row($rsDF)) {
                    $sqlDP = "SELECT valor+iva FROM gp_detalle_pago WHERE detalle_factura = $rowDF[0]";
                    $rsDP = $mysqli->query($sqlDP);
                    if(mysqli_num_rows($rsDP) > 0) {
                        $rowDP = mysqli_fetch_row($rsDP);
                        $sumDP += $rowDP[0];
                    } else {
                        $sumDP += 0;
                    }
                    $sumDF += $rowDF[1];
                }
                $valorD = $sumDF - $sumDP;
                echo round($valorD);
            }
        }
        break;
    case 59:
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Validamos que las variables de id_pago este vacia
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if(!empty($_POST['id_pago'])){
            $id_pago    = $_POST['id_pago'];           //Capturamos la varible del pago
            $id_tercero = $_POST['id_tercero'];     //Capturamos el valor del tercero
            $id_cnt     = $_POST['id_cnt'];             //Variable para capturar el id cnt
            $id_pptal   = $_POST['id_pptal'];         //id del comprobante presupuestal
            $sumD       = 0;                              //Variable para acumular el valor de los datos registrados
            $sqlCnt     = "SELECT fecha FROM gf_comprobante_cnt WHERE id_unico = $id_cnt";
            $resultCnt  = $mysqli->query($sqlCnt);
            $fecha      = mysqli_fetch_row($resultCnt);
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // Consultamos la cuenta que esta relacionada el banco del pago
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $sqlCuenta = "SELECT    cb.cuenta
                        FROM        gp_pago pg
                        LEFT JOIN   gf_cuenta_bancaria cb   ON cb.id_unico = pg.banco
                        WHERE       pg.id_unico = $id_pago";
            $resultCuenta = $mysqli->query($sqlCuenta);
            $rowCuenta    = mysqli_fetch_row($resultCuenta);
            $banco        = $rowCuenta[0];             //Variable con la cuenta banco
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // Consultamos el valor del detalle en el pago
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $sqlVal = "SELECT DISTINCT
                              dtp.detalle_factura,
                              dtp.valor,
                              dtf.valor_total_ajustado,
                              dtl.conceptorubro,
                              dtc.fecha,
                              dtl.rubrofuente,
                              dtp.id_unico,
                              dtf.factura,
                              dtp.iva,
                              dtp.impoconsumo,
                              dtf.ajuste_peso,
                              dtf.cantidad,
                              dtf.valor,
                              dtc.id_unico
                    FROM      gp_detalle_pago dtp
                    LEFT JOIN gp_detalle_factura dtf           ON dtf.id_unico = dtp.detalle_factura
                    LEFT JOIN gf_detalle_comprobante dtc       ON dtc.id_unico = dtf.detallecomprobante
                    LEFT JOIN gf_detalle_comprobante_pptal dtl ON dtl.id_unico = dtc.detallecomprobantepptal
                    WHERE pago = $id_pago";
            $resultVal = $mysqli->query($sqlVal);
            while($rowVal = mysqli_fetch_row($resultVal)){
                $valP = 0; $valor = abs($rowVal[1]); $conceptorubro = $rowVal[3]; $rubrofuente = $rowVal[5]; $id_depago = $rowVal[6];
                    $sql_dec = "SELECT cuenta FROM gf_detalle_comprobante WHERE id_unico = $rowVal[13]";
                    $res_dec = $mysqli->query($sql_dec);
                    $row_dec = $res_dec->fetch_row();
                    if( $rowVal[8] != 0 || $rowVal[8] != "" || $rowVal[8] != "0" ){
                        if($rowVal[9] < 0){
                            $ajp = $rowVal[9] * -1;
                        } else {
                            $ajp = $rowVal[9];
                        }
                    }

                    if($rowVal[8] > 0){
                        $valor = $valor + $rowVal[8];
                    }

                    if($valor[9] > 0){
                        $valor = $valor + $rowVal[9];
                    }

                    if($valor[10] > 0){
                        $valor = $valor + $rowVal[10];
                    }

                    $valP = ($valor - $rowVal[8]) + $rowVal[10];

                    $valor = round($valor);
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    // Insertamos el detalle presupuestal
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $insertP = "INSERT INTO gf_detalle_comprobante_pptal (valor, comprobantepptal, conceptorubro, tercero, proyecto, rubrofuente) VALUES($valP, $id_pptal, $conceptorubro, $id_tercero, 2147483647, $rubrofuente)";
                    $resultP = $mysqli->query($insertP);
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    // Consultamos el ultimo detalle registrado en el comprobante pptal
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $sqlPPP = "SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_pptal";
                    $resultPP = $mysqli->query($sqlPPP);
                    $detp = mysqli_fetch_row($resultPP);
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    // consultamos la cuenta debtio relacionada al concepto rubro
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $sqlCt = "SELECT DISTINCT crbc.cuenta_debito FROM gf_concepto_rubro_cuenta crbc WHERE crbc.concepto_rubro = $conceptorubro";
                    $resultCD = $mysqli->query($sqlCt);
                    $rowC = mysqli_fetch_row($resultCD);
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    // Insertamos el detalle contable
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                     $insertD = "INSERT INTO gf_detalle_comprobante (fecha, valor, comprobante, naturaleza, cuenta, tercero, proyecto, centrocosto, detallecomprobantepptal) VALUES('$fecha[0]', $valor*-1, $id_cnt, 1, $row_dec[0], $id_tercero, 2147483647, 12, $detp[0]);";
                    $resultC = $mysqli->query($insertD);
                    $sumD += $valor;                            //Acumulamos los valores

                    //
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    // Consultamos el ultimo detalle registrado en el comprobante contable
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $sqlDDD = "SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $id_cnt";
                    $resultDD = $mysqli->query($sqlDDD);
                    $det = mysqli_fetch_row($resultDD);
                    //Actualizamos el campo de detalle comprobante en detalle pago
                    $update_p = "UPDATE gp_detalle_pago SET detallecomprobante = $det[0] WHERE id_unico = $id_depago";
                    $r = $mysqli->query($update_p);

            }
            #################################################################################################################
            #SE VERIFICA SI EL COMPROBANTE CNT TIENE RETENCIONES
            $ret = "SELECT r.id_unico, r.valorretencion, tr.cuenta , c.naturaleza FROM gf_retencion r
                    LEFT JOIN gf_tipo_retencion tr ON r.tiporetencion = tr.id_unico
                    LEFT JOIN gf_cuenta c ON tr.cuenta = c.id_unico
                    WHERE r.comprobante  = $id_cnt";
            $ret = $mysqli->query($ret);
            if(mysqli_num_rows($ret)>0){

                while ($row2 = mysqli_fetch_row($ret)) {
                    if($row2[3]==1){
                        $valorret = $row2[1];
                    } else {
                        $valorret = $row2[1]*-1;
                    }
                    $insertDR = "INSERT INTO gf_detalle_comprobante (fecha, valor, comprobante, "
                            . "naturaleza, cuenta, "
                            . "tercero, proyecto, centrocosto ) "
                            . "VALUES('$fecha[0]', $valorret, $id_cnt, "
                            . "$row2[3], $row2[2], "
                            . "$id_tercero, 2147483647, 12);";
                    $resultCR = $mysqli->query($insertDR);
                    $sumD -= $row2[1];
                }

            }
            #Actualizar Encabezado Del Comprobante Pptal
            $insertP = "UPDATE gf_comprobante_pptal SET tercero=$id_tercero  WHERE id_unico = $id_pptal";
            $resultP = $mysqli->query($insertP);
            #Actualizar Encabezado Del Comprobante  CNT
            $insertP = "UPDATE gf_comprobante_cnt SET tercero=$id_tercero  WHERE id_unico = $id_cnt";
            $resultP = $mysqli->query($insertP);
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // Insertamos la cuenta de recaudo con la sumatoria de los valores
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $insertR = "INSERT INTO gf_detalle_comprobante (fecha, valor, comprobante, naturaleza, cuenta, tercero, proyecto, centrocosto) VALUES ('$fecha[0]', $sumD, $id_cnt, 1, $banco, $id_tercero, 2147483647, 12);";
            $resultR = $mysqli->query($insertR);
            echo json_encode($resultR);
        }
    break;
    case 60:
        if(!empty($_POST['id_cnt'])) {
            $id_cnt = $_POST['id_cnt'];
            $sql = "SELECT conciliado FROM gf_detalle_comprobante WHERE comprobante = $id_cnt AND conciliado IS NOT NULL";
            $result = $mysqli->query($sql);
            $conteo = mysqli_fetch_row($result);
            if(!empty($conteo[0])){
                echo 1;
            }  else {
                echo 2;
            }
        }
    break;
    case 61:
        if(!empty($_POST['id_cnt'])) {
            $id_cnt = $_POST['id_cnt'];
            $sql = "SELECT valor,naturaleza FROM gf_detalle_comprobante WHERE comprobante = $id_cnt";
            $result = $mysqli->query($sql);
            $conteo = mysqli_num_rows($result);
            if($conteo > 0){
                $deb = 0;
                $cre = 0;
                while ($row = mysqli_fetch_row($result)) {
                    $naturaleza = $row[1];
                    $valor = $row[0];
                    if($naturaleza == 1) {
                        if($valor > 0){
                            $deb += $valor;
                        }else{
                            $cre += $valor*-1;
                        }
                    }else if($naturaleza == 2){
                        if($valor > 0) {
                            $cre += $valor;
                        }else{
                            $deb += $valor*-1;
                        }
                    }

                }
                if($deb < 0){
                    $deb = $deb *-1;
                }
                if($cre < 0) {
                    $cre = $cre *-1;
                }
                if($deb == $cre){
                    echo 1;
                }
            }
        }
        break;
    case 62:
        if(!empty($_POST['id_pago'])){
            $id_pago = $_POST['id_pago'];
            $sql = "DELETE FROM gp_detalle_pago WHERE pago = $id_pago";
            $result = $mysqli->query($sql);
        }

        if(!empty($_POST['id_cnt'])){
            $id_cnt = $_POST['id_cnt'];
            $sql = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $id_cnt";
            $result = $mysqli->query($sql);
            ######ELIMINAR RETENCIONES######
            $sql = "DELETE FROM gf_retencion WHERE comprobante = $id_cnt";
            $result = $mysqli->query($sql);
        }

        if(!empty($_POST['id_pptal'])){
            $id_pptal = $_POST['id_pptal'];
            $sql = "DELETE FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_pptal";
            $result = $mysqli->query($sql);
            echo json_encode($result);
        }
        break;
    case 63:
        if(!empty($_POST['id_cnt'])) {
            $x = false;
            $id_cnt = $_POST['id_cnt'];
            $sql = "SELECT conciliado FROM gf_detalle_comprobante WHERE comprobante = $id_cnt AND conciliado NOT NULL";
            $result = $mysqli->query($sql);
            if($result == true){
                $row = mysqli_fetch_row($result);
                $x = true;
            }
            echo ($x);
        }
    break;
    case 64:
        if(!empty($_POST['id_cnt']) && !empty($_POST['id_pptal'])){               #Validamos que la variable no venga vacio
            # Capturamos los valores enviados por post
            $idcnt    = $_POST['id_cnt']; $idpptal = $_POST['id_pptal'];
            # Inicializamos un contador en 0 y un array en vacio para obtener los id de los detalles
            $h = 0; $x = array();
            # Capturamos las variables de session
            $compania = $_SESSION['compania']; $param = $_SESSION['anno'];
            # Consultamos que tenga un tipo de comprobante el cual este homologado
            $sql_c = "SELECT    tp.tipo_comp_hom, cnt.numero
                      FROM      gf_comprobante_cnt cnt
                      LEFT JOIN gf_tipo_comprobante tp ON cnt.tipocomprobante = tp.id_unico
                      WHERE     cnt.id_unico = $idcnt;";
            $result_c = $mysqli->query($sql_c);
            $tipo_hom1 = mysqli_fetch_row($result_c);
            # Consultamos si existe un tipo de comprobante con el tipo homologado y el tipo
            $sqlExiste = "SELECT  id_unico FROM gf_comprobante_cnt
                          WHERE   tipocomprobante = $tipo_hom1[0]
                          AND     numero = $tipo_hom1[1];";
            $resultExiste = $mysqli->query($sqlExiste);
            $rowE = mysqli_fetch_row($resultExiste);
            if(empty($rowE[0])){ # Si no existe comprobante con tipo homologado similar se realiza el proceso de causación
                # Consultamos los detalles y obtenemos la cuentas y validamos si están registradas en concepto_rubro_cuenta como debito
                $sqlD = "SELECT DISTINCT dtc.id_unico, dtc.cuenta
                         FROM            gf_detalle_comprobante dtc
                         WHERE           dtc.comprobante = $idcnt;";
                $resultD = $mysqli->query($sqlD);
                while($rowD = mysqli_fetch_row($resultD)){
                    # Consultamos las cuentas en los detalles, esten en concepto_rubro_cuenta como cuenta debito
                    $sqlCR = "SELECT DISTINCT crb.cuenta_credito
                              FROM            gf_concepto_rubro_cuenta crb
                              LEFT JOIN       gf_concepto_rubro cr ON crb.concepto_rubro = cr.id_unico
                              LEFT JOIN       gf_concepto cn       ON cn.id_unico        = cr.concepto
                              WHERE           crb.cuenta_debito = $rowD[1]
                              AND             cn.clase_concepto = 1;";
                    $resultCR = $mysqli->query($sqlCR);
                    $filasCR = mysqli_num_rows($resultCR);
                    # Validamos que los ids de cuenta credito y debito no sean iguales
                    while ($rowCR = mysqli_fetch_row($resultCR)) {
                        if ($rowD[1] !== $rowCR[0]) {
                            # Validamos que la consulta retorne valores mayores que 0
                            if ($filasCR !== 0) {
                                ++$h;                                   # Preincrementamos el contador
                                $x[] = $rowD[0];                        # Capturamos el id del detalle
                            }
                        }
                    }
                    
                }
                if ($h > 0){# Validamos que h sea mayor que 0
                    # Consultamos los valores del comprobante padre
                    $sqlCnt = "SELECT    cnt.numero, cnt.fecha, cnt.tercero, cnt.descripcion, cnt.estado, cnt.clasecontrato, cnt.numerocontrato,
                                         tpc.tipo_comp_hom
                               FROM      gf_comprobante_cnt cnt
                               LEFT JOIN gf_tipo_comprobante tpc ON cnt.tipocomprobante = tpc.id_unico
                               WHERE     cnt.id_unico = $idcnt;";
                    $rsCnt = $mysqli->query($sqlCnt);
                    $rwCnt = mysqli_fetch_row($rsCnt);
                    if (mysqli_num_rows($rsCnt) > 0){               # Validamos que la consulta retorna algún valor
                        $tipoHom = $rwCnt[7];
                        if (!empty($tipoHom)){                      # Validamos que el valor devuelto por la base de datos no este vació
                            # Capturamos los valores devultos por la consulta
                            $numero = $rwCnt[0]; $fecha = $rwCnt[1]; $tercero = $rwCnt[2]; $estado = $rwCnt[4];
                            # Validamos los posibles campos que tendran valores vacios
                            if(empty($rwCnt[3])){
                                $descripcion = "NULL";
                            }else{
                                $descripcion = $rwCnt[3];
                            }
                            if(empty($rwCnt[5])){
                                $clasecontrato = "NULL";
                            }else{
                                $clasecontrato = $rwCnt[5];
                            }
                            if(empty($rwCnt[6])){
                                $numerocontrato = "NULL";
                            }else{
                                $numerocontrato = $rwCnt[6];
                            }
                            $sqlTP = "SELECT comprobante_pptal FROM gf_tipo_comprobante WHERE id_unico = $tipoHom;";
                            $rsTP = $mysqli->query($sqlTP);
                            if (mysqli_num_rows($rsTP) > 0){         # Validamos que la consulta retorne valores
                                $tipopptal = mysqli_fetch_row($rsTP);# Capturamos el valor retornado por la consulta
                                if (!empty($tipopptal[0])){          # Validamos que el tipo de comprobante pptal no este vacio
                                    $fecha_v = strtotime('+1 month', strtotime($fecha));# Sumamos un mes a la fecha para obtener la fecha de vencimiento
                                    # Consulta de insertado de comprobante pptal
                                    $sqlInPptal = "INSERT INTO gf_comprobante_pptal(tipocomprobante, numero, fecha, fechavencimiento, descripcion, parametrizacionanno,
                                                               clasecontrato, numerocontrato, tercero, responsable, estado)
                                                   VALUES      ($tipopptal[0], $numero, '$fecha', '$fecha_v', '$descripcion', $param, $clasecontrato, $numerocontrato, $tercero,
                                                               $compania, 1);";
                                    $rsInPptal = $mysqli->query($sqlInPptal);
                                    if($rsInPptal == true){                     # Validamos que si se realiza el registro
                                        # Consultamos el ultimo comprobante presupuestal registrado en la base de datos
                                        $sqlMaxIdPptal = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE tipocomprobante = $tipopptal[0];";
                                        $rsMaxIdPptal = $mysqli->query($sqlMaxIdPptal);
                                        if(mysqli_num_rows($rsMaxIdPptal) > 0){ # Validamos que la consulta retorne valores
                                            $rwMaxIdPtal = mysqli_fetch_row($rsMaxIdPptal);
                                            $id_pptal = $rwMaxIdPtal[0];         # Asignamos el valor obtenido a la variable
                                        }
                                    }
                                }
                            }
                            # Insertamos a cnt
                            $sqlInCnt = "INSERT INTO gf_comprobante_cnt(tipocomprobante, numero, fecha, tercero, descripcion, estado, clasecontrato, numerocontrato, compania,
                                                     parametrizacionanno)
                                         VALUES      ($tipoHom, $numero, '$fecha', $tercero, '$descripcion', $estado, $clasecontrato, $numerocontrato, $compania, $param);";
                            $rsInCnt = $mysqli->query($sqlInCnt);
                            if($rsInCnt == true){                          # Validamos que se realizo el registro a comprobante cnt
                                # Consulta para obtener el último registro en la base de datos
                                $sqlMaxIdCnt = "SELECT MAX(id_unico) FROM  gf_comprobante_cnt WHERE tipocomprobante = $tipoHom;";
                                $rsMaxIdCnt = $mysqli->query($sqlMaxIdCnt);
                                if(mysqli_num_rows($rsMaxIdCnt) > 0){      # Validamos que la consulta retorne valores en la base de datos
                                    $rwMaxIdCnt = mysqli_fetch_row($rsMaxIdCnt);
                                    $id_cnt = $rwMaxIdCnt[0];               # Asignamos el valor encontrado en la variable id_cnt
                                }
                                if(!empty($id_cnt)){                       # Validamos que la variable $id_cnt no este vacia
                                    # Desplegamos el array, y consultamos el detalle, y nuevamente obtenemos el id de la cuenta, la cual consultamos
                                    # nuevamente a conceto rubro cuenta y relizamos el ingreso de datos al detalle
                                    for($a = 0; $a < count($x); $a++){
                                        $id_detalle = $x[$a]; $id_dpptal = "NULL";
                                        $sqlDt = "SELECT dtc.fecha, dtc.descripcion, dtc.valor, dtc.cuenta, dtc.tercero, dtc.proyecto, dtc.centrocosto, dtc.detallecomprobantepptal
                                                  FROM   gf_detalle_comprobante dtc
                                                  WHERE  dtc.id_unico = $id_detalle;";
                                        $resultDt = $mysqli->query($sqlDt);
                                        $rowDt = mysqli_fetch_row($resultDt);
                                        $valor = abs($rowDt[2]);            # Obtenemos el valor absoluto del valor en el detalle
                                        $sqlDl = "SELECT  conceptoRubro
                                                  FROM    gf_detalle_comprobante_pptal
                                                  WHERE   id_unico = $rowDt[7];";
                                        $reDl = $mysqli->query($sqlDl);
                                        $rowDl    = mysqli_fetch_row($reDl);
                                        if (!empty($id_pptal)){             # Validamos que el id pptal no este vacio
                                            if(!empty($rowDt[7]) && $id_pptal !== 'NULL'){
                                                # Consultamos los valores del detalle pptal relacionado
                                                $sqlDePtall = "SELECT  descripcion, rubroFuente, conceptoRubro, tercero, proyecto
                                                               FROM    gf_detalle_comprobante_pptal
                                                               WHERE   id_unico = $rowDt[7];";
                                                $resultDePtall = $mysqli->query($sqlDePtall);
                                                $rowDePtall    = mysqli_fetch_row($resultDePtall);
                                                # Insertamos los valores
                                                $sqlIND = "INSERT INTO  gf_detalle_comprobante_pptal(descripcion, valor, rubroFuente, conceptoRubro, tercero, proyecto,
                                                                        comprobantepptal)
                                                           VALUES       ('$rowDePtall[0]', $valor, $rowDePtall[1], $rowDePtall[2], $rowDePtall[3], $rowDePtall[4], $id_pptal);";
                                                $resultIND = $mysqli->query($sqlIND);
                                                if($resultIND == true){
                                                    # Consultamos el ultimo id detalle insertado
                                                    $sqlULPPP  = "SELECT MAX(id_unico) FROM  gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_pptal;";
                                                    $resultPPP = $mysqli->query($sqlULPPP);
                                                    $rowDetallePp1 = mysqli_fetch_row($resultPPP);
                                                    $id_dpptal     = $rowDetallePp1[0];
                                                }
                                            }
                                            # Consultamos el id de la cuenta credito
                                            $sqlCR    = "SELECT  cuenta_credito FROM gf_concepto_rubro_cuenta WHERE cuenta_debito = $rowDt[3] AND concepto_rubro = $rowDl[0];";
                                            $resultCR = $mysqli->query($sqlCR);
                                            $rowCR = mysqli_fetch_row($resultCR);
                                            if(!empty($rowCR[0])){
                                                # Realizamos insertado de datos con cuenta debito
                                                $sqlDD = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, valor, cuenta, naturaleza, tercero, proyecto, centrocosto, comprobante,
                                                                      detalleafectado, detallecomprobantepptal)
                                                          VALUES      ('$rowDt[0]', '$rowDt[1]', $valor, $rowDt[3], 1, $rowDt[4], $rowDt[5], $rowDt[6], $id_cnt, $id_detalle, $id_dpptal);";
                                                $resultDD = $mysqli->query($sqlDD);
                                                # Realizamos insertado de datos a cuenta credito
                                                $sqlDC = "INSERT INTO gf_detalle_comprobante  (fecha, descripcion, valor, cuenta, naturaleza, tercero, proyecto, centrocosto, comprobante,
                                                                      detalleafectado, detallecomprobantepptal)
                                                          VALUES      ('$rowDt[0]', '$rowDt[1]', $valor, $rowCR[0], 2, $rowDt[4], $rowDt[5], $rowDt[6], $id_cnt, $id_detalle, $id_dpptal);";
                                                $resultDC = $mysqli->query($sqlDC);
                                            }
                                        }
                                    }
                                    echo json_encode(true);
                                }
                            }
                        }

                    }
                } else {
                    echo json_encode(false); 
                }
            }else{
                $id_hom = $rowE[0];
                # Consultamos los detalles y obtenemos la cuentas y validamos si están registradas en concepto_rubro_cuenta como debito
                $sqlD = "SELECT DISTINCT dtc.id_unico, dtc.cuenta
                         FROM            gf_detalle_comprobante dtc
                         WHERE           dtc.comprobante = $idcnt;";
                $resultD = $mysqli->query($sqlD);
                while($rowD = mysqli_fetch_row($resultD)){
                    # Consultamos las cuentas en los detalles, esten en concepto_rubro_cuenta como cuenta debito
                    $sqlCR = "SELECT DISTINCT crb.cuenta_credito
                              FROM            gf_concepto_rubro_cuenta crb
                              LEFT JOIN       gf_concepto_rubro cr ON crb.concepto_rubro = cr.id_unico
                              LEFT JOIN       gf_concepto cn       ON cn.id_unico        = cr.concepto
                              WHERE           crb.cuenta_debito = $rowD[1]
                              AND             cn.clase_concepto = 1;";
                    $resultCR = $mysqli->query($sqlCR);
                    $filasCR = mysqli_num_rows($resultCR);
                    while ($rowCR = mysqli_fetch_row($resultCR)) {
                        if ($rowD[1] !== $rowCR[0]) {
                            # Validamos que la consulta retorne valores mayores que 0
                            if ($filasCR !== 0) {
                                ++$h;                                   # Preincrementamos el contador
                                $x[] = $rowD[0];                        # Capturamos el id del detalle
                            }
                        }
                    }
                }
                if ($h > 0){# Validamos que h sea mayor que 0
                    for($a = 0; $a < count($x); $a++){
                        $id_detalle = $x[$a]; $id_dpptal = "NULL";
                        $sqlDt = "SELECT dtc.fecha, dtc.descripcion, dtc.valor, dtc.cuenta, dtc.tercero, dtc.proyecto, dtc.centrocosto, dtc.detallecomprobantepptal
                                  FROM   gf_detalle_comprobante dtc
                                  WHERE  dtc.id_unico = $id_detalle;";
                        $resultDt = $mysqli->query($sqlDt);
                        $rowDt = mysqli_fetch_row($resultDt);
                        $valor = abs($rowDt[2]);            # Obtenemos el valor absoluto del valor en el detalle
                        $sqlDl = "SELECT  conceptoRubro
                                  FROM    gf_detalle_comprobante_pptal
                                  WHERE   id_unico = $rowDt[7];";
                        $reDl = $mysqli->query($sqlDl);
                        $rowDl    = mysqli_fetch_row($reDl);
                        if (!empty($id_pptal)){             # Validamos que el id pptal no este vacio
                            if(!empty($rowDt[7]) && $id_pptal !== 'NULL'){
                                # Consultamos los valores del detalle pptal relacionado
                                $sqlDePtall = "SELECT  descripcion, rubroFuente, conceptoRubro, tercero, proyecto
                                               FROM    gf_detalle_comprobante_pptal
                                               WHERE   id_unico = $rowDt[7];";
                                $resultDePtall = $mysqli->query($sqlDePtall);
                                $rowDePtall    = mysqli_fetch_row($resultDePtall);
                                # Insertamos los valores
                                $sqlIND = "INSERT INTO  gf_detalle_comprobante_pptal(descripcion, valor, rubroFuente, conceptoRubro, tercero, proyecto,
                                                        comprobantepptal)
                                           VALUES       ('$rowDePtall[0]', $valor, $rowDePtall[1], $rowDePtall[2], $rowDePtall[3], $rowDePtall[4], $id_pptal);";
                                $resultIND = $mysqli->query($sqlIND);
                                if($resultIND == true){
                                    # Consultamos el ultimo id detalle insertado
                                    $sqlULPPP  = "SELECT MAX(id_unico) FROM  gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_pptal;";
                                    $resultPPP = $mysqli->query($sqlULPPP);
                                    $rowDetallePp1 = mysqli_fetch_row($resultPPP);
                                    $id_dpptal     = $rowDetallePp1[0];
                                }
                            }
                        }

                        # Consultamos el id de la cuenta credito
                        $sqlCR    = "SELECT  cuenta_credito FROM gf_concepto_rubro_cuenta WHERE cuenta_debito = $rowDt[3] AND concepto_rubro = $rowDl[0];";
                        $resultCR = $mysqli->query($sqlCR);
                        $rowCR = mysqli_fetch_row($resultCR);
                        if(!empty($rowCR[0])){
                            # Realizamos insertado de datos con cuenta debito
                            $sqlDD = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, valor, cuenta, naturaleza, tercero, proyecto, centrocosto, comprobante,
                                                  detalleafectado)
                                      VALUES      ('$rowDt[0]', '$rowDt[1]', $valor, $rowDt[3], 1, $rowDt[4], $rowDt[5], $rowDt[6], $id_hom, $id_detalle);";
                            $resultDD = $mysqli->query($sqlDD);
                            # Realizamos insertado de datos a cuenta credito
                            $sqlDC = "INSERT INTO gf_detalle_comprobante  (fecha, descripcion, valor, cuenta, naturaleza, tercero, proyecto, centrocosto, comprobante,
                                                  detalleafectado)
                                      VALUES      ('$rowDt[0]', '$rowDt[1]', $valor, $rowCR[0], 2, $rowDt[4], $rowDt[5], $rowDt[6], $id_hom, $id_detalle);";
                            $resultDC = $mysqli->query($sqlDC);
                        }
                    }
                    echo json_encode(true);
                }
            }
        } else {
           echo json_encode(false); 
        }
        break;
}