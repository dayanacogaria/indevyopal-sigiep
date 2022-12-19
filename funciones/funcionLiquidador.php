<?php
class Liquidador{

    /**
     * liquidar_intereses
     * Método para liquidar los intereses
     * @param Double $valor Valor a generear intereses
     * @param date $fechaI Fecha de inicio
     * @param date $fechaF Fecha Final
     * @return Double $xxx Valor acumulado de interes calculado mes por mes
     */
    public static function liquidar_intereses($valor, $fechaI, $fechaF){
        $inicio = new DateTime($fechaI);
        $fin    = new DateTime($fechaF);
        $ffi    = new DateTime($fechaI);
        $xxx = 0;
        while($inicio <= $fin){

            if($inicio->format("Y") < "2008"){
                $data_i  = $inicio;
                $inicio  = new DateTime("2008-01-01");
                $xdias   = $inicio->diff($data_i);
                $dias    = $xdias->format('%a');
            }else{
                $last   = Liquidador::data_last_month_day($inicio->format("d"), $inicio->format("m"), $inicio->format("Y"));
                $last   = new DateTime($last);
                //$last = $fin;
                if($inicio->format("m") > $ffi->format("m")){
                    $fi     = Liquidador::data_first_month_day($inicio->format("Y"), $inicio->format("m"));
                    $inicio = new DateTime($fi);
                }
                $xdias   = $inicio->diff($last);
                $dias    = $xdias->format('%a');
            }

            $int = Liquidador::getInteresDescuento($inicio->format("Y"), $inicio->format("m"));
           // echo "dias calcuados: ".$dias.'  fecha inicial  '.$fechaI.'  fecha final '.$fechaF;
            
            $opr  = (int) ((($valor * ($int / 100))  * $dias) / 366);

            $xxx += $opr;
            $inicio->modify('+1 month');
        }
        return $xxx;
    }

    public static function liquidar_intereses2($valor, $fechaI, $fechaF){
        $inicio = new DateTime($fechaI);
        $fin    = new DateTime($fechaF);
        $ffi    = new DateTime($fechaI);
        $xxx = 0;
        while($inicio <= $fin){

            if($inicio->format("Y") < "2008"){
                $data_i  = $inicio;
                $inicio  = new DateTime("2008-01-01");
                $xdias   = $inicio->diff($data_i);
                $dias    = $xdias->format('%a');
            }else{
                //$last   = Liquidador::data_last_month_day($inicio->format("d"), $inicio->format("m"), $inicio->format("Y"));
                //$last   = new DateTime($last);
                $last = $fin;
                if($inicio->format("m") > $ffi->format("m")){
                    $fi     = Liquidador::data_first_month_day($inicio->format("Y"), $inicio->format("m"));
                    $inicio = new DateTime($fi);
                }
                $xdias   = $inicio->diff($last);
                $dias    = $xdias->format('%a');
            }

            $int = Liquidador::getInteresDescuento($inicio->format("Y"), $inicio->format("m"));
            //echo "dias calcuados: ".$dias.'  fecha inicial  '.$fechaI.'  fecha final '.$fechaF;
            
            $opr  = (int) ((($valor * ($int / 100))  * $dias) / 366);

            $xxx += $opr;
            $inicio->modify('+1 month');
        }
        return $xxx;
    }
    /**
     * getInteresDescuento
     * función para obtener el valor del interes
     * @author Alexanader Numpaque
     * @package Predial
     * @param String $year Año
     * @param String $month Mes
     * @return Doyble $row Valor del interes
     */
    public static function getInteresDescuento($year, $month){
        //require('../Conexion/conexion.php');
        $str = "SELECT      gri.valor
                FROM        gr_interes_descuento as gri
                LEFT JOIN   gf_mes as mes ON gri.mes = mes.id_unico
                WHERE       gri.anio   = '$year'
                AND         mes.numero = '$month'
                ";
        $res = $GLOBALS['mysqli']->query($str);
        $row = mysqli_fetch_row($res);
        return $row[0];
    }

    /**
     * data_last_month_day
     * Metodo para obtener el ultimo dia del mes
     * @author Alexander Numpaque
     * @package Predial
     * @param String $day Dia
     * @param String $month Mes
     * @param String $year Año
     * @return String $date Fecha con el último dia del mes
     */
    public static function data_last_month_day($day, $month, $year) {
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));
        return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
    }

    /**
     * data_first_month_day
     * Función para obtener el primer dia del mes
     * @author Alexander Numpaque
     * @package Predial
     * @param String $year $año
     * @param String $month $mes
     * @return String $date Fecha con el primer dia del mes
     */
    public static function data_first_month_day($year, $month) {
        return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
    }

    /**
     * getValueDes
     * Función para obtener los valor para realizar el descuento
     * @author Alexander Numpaque
     * @package Predial
     * @param String $year Año
     * @param int $month Mes
     * @param int $type id de tipo
     * @return double $row Valor de interes descuento
     */
    public static function getValueDes($year, $month, $type){
        //require('../Conexion/conexion.php');
        $xxx = "";
        $str = "SELECT      gri.valor
                FROM        gr_interes_descuento as gri
                LEFT JOIN   gf_mes as mes ON gri.mes = mes.id_unico
                WHERE       gri.anio   = '$year'
                AND         mes.numero = '$month'
                AND         gri.tipo    = $type
                ";
        $res = $GLOBALS['mysqli']->query($str);
        $row = mysqli_fetch_row($res);
        if(!empty($row[0]))
            return $row[0];
        else{
            return $xxx;
        }
    }

    /**
     * getDateV
     * Función para obtener la fecha de vencimiento
     * @author Alexander Numpaque
     * @package Predial
     * @param String $year Año de vigencia
     * @param int $mount Número de mes
     * @param int $type Tipo de vencimiento
     * @return date $row Fecha de vencimiento
     */
    public static function getDateV($year, $mount, $type){
        //require ('../Conexion/conexion.php');
        $xxx = "";
        $str = "SELECT      ven.fecha
                FROM        gr_vencimiento as ven
                LEFT JOIN   gf_mes as mes ON ven.mes = mes.id_unico
                WHERE       ven.anno = '$year'
                AND         ven.mes  = '$mount'
                AND         ven.tipo = $type";
        $res = $GLOBALS['mysqli']->query($str);
        $row = mysqli_fetch_row($res);
        if(!empty($row[0])){
            return $row[0];
        }else{
            return $xxx;
        }
    }

    /**
     * applyDes
     * Función para aplicar Descuento
     * @author Alexander Numpaque
     * @package Predial
     * @param Date $fecha Fecha
     * @param Double $valor Valor a pagar
     * @param int $type Id de tipo
     * @return double $xxx Valor de descuentos
     */
    public static function applyDes($fecha, $valor, $type,$perG){
        //require('../Conexion/conexion.php');

        $sql = "SELECT mes, anno,tipo FROM gr_vencimiento WHERE anno = '$perG' AND tipo = '$type' AND fecha >= '$fecha'";
        $resu = $GLOBALS['mysqli']->query($sql);
        $nres = mysqli_num_rows($resu);
          
        if($nres > 0){

            $res = mysqli_fetch_row($resu);
            $sql2 = "SELECT valor FROM gr_interes_descuento WHERE anio = '$res[1]' AND mes = '$res[0]' AND tipo = '$res[2]'";
            $resu2 = $GLOBALS['mysqli']->query($sql2);
            $res2 = mysqli_fetch_row($resu2);

            $xxx = ($valor * $res2[0])/100;
        }
       
        return $xxx;
    }
}
