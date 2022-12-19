<?php
    
    
    function primaVac ($empleado){
        
        
        require 'Conexion/conexion.php';
        require_once 'funciones/prima_navidad.php'; 
        @session_start();
        global $mysqli;
        $anno = $_SESSION['anno'];
        $proceso = 2;
        
        $hoy = date('d-m-Y');
        $hoy = trim($hoy, '"');
        $fecha_div = explode("-", $hoy);
        $anio1 = $fecha_div[2];
        $mes1 = $fecha_div[1];
        $dia1 = $fecha_div[0];
        $hoy = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';

        
        $AcumPrima = acumular_e($empleado, $proceso, 12,109);
        $AcumBonif = acumular_e($empleado, $proceso, 12,111);
        
        $x = $AcumBonif[111]/12;
        $y = $AcumPrima[109]/12;
        
        $sql1 = "SELECT c.salarioactual FROM gn_categoria c LEFT JOIN gn_tercero_categoria tc ON tc.categoria = c.id_unico LEFT JOIN gn_empleado e ON tc.empleado = e.id_unico "
                . "WHERE e.id_unico = '$empleado'";
        
        $res1 = $mysqli->query($sql1);
        $result1 = mysqli_fetch_row($res1);
        
        $sql2 = "SELECT primaA, talimentacion FROM gn_parametros_liquidacion WHERE vigencia = '$anno'";
        $res2 = $mysqli->query($sql2);
        $result2 = mysqli_fetch_row($res2);
        
        if($result1[0] > $result2[1]){
            
            $result2[0] = 0;
        }
        
        $SUMP = ((($x + $y + $result1[0] + $result2[0]) * 15) / 30);
        $SUMP = intval($SUMP);
        $SUMB = (( $result1[0]  * 2) / 30);
        $SUMB = intval($SUMB);

        return $SUMB;
    }

    function bon_serv ($empleado){
        require_once '../Conexion/conexion.php';
        require_once 'funciones/prima_navidad.php'; 
        @session_start();
        global $mysqli;

        $anno = $_SESSION['anno'];
        $sql1 = "SELECT e.id_unico, 
                            e.tercero, 
                            CONCAT( t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ), 
                            tc.categoria, 
                            c.id_unico, 
                            c.nombre,   
                            c.salarioactual
        
                    FROM gn_empleado e 
                    LEFT JOIN gf_tercero t on e.tercero = t.id_unico
                    LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                    LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
                    WHERE e.id_unico = $empleado"; 
    
        $res = $mysqli->query($sql1);
        //$resultado = $this->mysqli->query($sql1);
        
        //$resultado = $mysqli->query($sql1);
        
        $rowO = mysqli_fetch_row($res);
        $salmin = "SELECT salmin FROM gn_parametros_liquidacion ";
        $salM = $mysqli->query($salmin);
        $slm = mysqli_fetch_row($salM);
            
        if($rowO[6] <= ($slm[0] * 2)){
            $porc = 50;
        }else{
            $porc = 35;
        }
            
        $BON = ($rowO[6] * $porc)/100;
        $BON = round($BON / 10);
        $BON = $BON * 10;
        
        
        return $BON;
        
    }

    function prima_serv($empleado){
        require_once '../Conexion/conexion.php';
        require_once 'funciones/prima_navidad.php'; 
        session_start();
        global $mysqli;
        $anno = $_SESSION['anno'];
        $sql1 = "SELECT e.id_unico, 
                        e.tercero, 
                        CONCAT( t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ), 
                        tc.categoria, 
                        c.id_unico, 
                        c.nombre, 
                        c.salarioactual

                FROM gn_empleado e 
                LEFT JOIN gf_tercero t on e.tercero = t.id_unico
                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
                WHERE e.id_unico = $empleado"; 

        $re = $mysqli->query($sql1);
        #$rowO = mysqli_num_rows($resultado);
        
        $Fperiodo = "SELECT fechafin, fechainicio FROM gn_periodo WHERE id_unico= '$periodo'";
        $Fecha = $mysqli->query($Fperiodo);
        $FechaP = mysqli_fetch_row($Fecha);
         
        $fecha = "SELECT * from gf_parametrizacion_anno where id_unico = '$anno'";
        $res = $mysqli->query($fecha);
        $row = mysqli_fetch_row($res);
        
        $FN = Annos($FechaP[1], 1);
           
        $mes = MesesFecha($FN, 1);
        
        $PL = "SELECT  * FROM gn_parametros_liquidacion WHERE vigencia = $anno";
        
        
        $res1 = $mysqli->query($PL);
        $rowP = mysqli_fetch_row($res1);
            
            $pid = $rowP[0]; // id de los parametros
            $pvi = $rowP[1]; // vigencia 
            $psm = $rowP[2]; // salario minimo de la vigencia
            $pat = $rowP[3]; // auxilio de transporte de la vigencia 
            $ppa = $rowP[4]; // prima de alimentacion
            $ppm = $rowP[5]; // prima de movilidad
            $pse = $rowP[6]; // aporte salud empleado
            $psp = $rowP[7]; // aporte salud empresa
            $ppe = $rowP[8]; // aporte pension empleado
            $ppp = $rowP[9]; // aporte pension empresa
            $pfs = $rowP[10]; // aporte fondo de solidaridad
            $per = $rowP[11]; // Encento de retencion
            $pcc = $rowP[12];// aporte caja de compensacion
            $psen = $rowP[13];// aporte SENA
            $pic = $rowP[14];// aporte ICBF
            $pes = $rowP[15];// aporte ESAP
            $pmi = $rowP[16];// aporte ministrerio
            $puv = $rowP[17];// valor UVT
            $pta = $rowP[18];// total alimetnacion
            $pad = $rowP[19];// total alimentacion docente
            
        
        $hoy = date('d-m-Y');
        $hoy = trim($hoy, '"');
        $fecha_div = explode("-", $hoy);
        $anio2 = $fecha_div[2];
        $mes2 = $fecha_div[1];
        $dia2 = $fecha_div[0];
        $hoy = '"'.$anio2.'-'.$mes2.'-'.$dia2.'"';   
        
        $FF = explode("-", $FechaP[0]);
        
        $borrar = "DELETE FROM gn_novedad WHERE periodo = '$periodo' AND aplicabilidad = 1";
        $resultado1 = $mysqli->query($borrar);
        
        
        while($rowE = mysqli_fetch_row($re)){
            
            if($FF[1] == 06){
                
                $sql2 = "SELECT SUM(n.valor) FROM gn_novedad n WHERE n.concepto = 7 AND n.empleado = '$rowE[0]' AND n.fecha BETWEEN '2017-01-01' AND '2017-06-30'";
                $res2 = $mysqli->query($sql2);
                $DT = mysqli_fetch_row($res2);
            }else{
                
                $sql2 = "SELECT SUM(n.valor) FROM gn_novedad n WHERE n.concepto = 7 AND n.empleado = '$rowE[0]' AND n.fecha BETWEEN '2017-07-01' AND '2017-12-31'";
                $res2 = $mysqli->query($sql2);
                $DT = mysqli_fetch_row($res2);
            }
            
            $Sal = "SELECT c.salarioactual FROM gn_categoria c "
                    . "LEFT JOIN gn_tercero_categoria tc ON tc.categoria = c.id_unico "
                    . "LEFT JOIN gn_empleado e ON tc.empleado = e.id_unico "
                    . "WHERE e.id_unico = '$rowE[0]' ";
            
            $SA = $mysqli->query($Sal);
            $S = mysqli_fetch_row($SA);
            
            $prima = (($S[0] + $pat) * $DT[0]) / 360;
            $prima = $prima/12;
        }
        
        return $prima;
            
    }

?>