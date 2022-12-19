<?php 
@session_start();

function acumular_e($empleado, $tproceso, $n, $con){
	require ('../Conexion/conexion.php');
  global $mysqli;
	$data = array();
	$x = 0;
	$y = "";
        
        if(empty($con)){
         
            $sql = "SELECT    nov.periodo
	        FROM      gn_novedad nov
			LEFT JOIN gn_periodo per ON nov.periodo = per.id_unico
			WHERE     per.tipoprocesonomina = $tproceso
			AND       nov.empleado          = $empleado
			AND 	  per.acumulable        = 1
			GROUP BY  per.id_unico
			ORDER BY  per.id_unico DESC
			LIMIT     0, $n";
                $res = $mysqli->query($sql);
                while($row = mysqli_fetch_row($res)){
                        $y .= $row[0].",";
                }
                $y = substr($y, 0, strlen($y)-1);
                $sql_x = "SELECT    con.id_unico, SUM(nov.valor) FROM gn_novedad nov
                                  LEFT JOIN gn_periodo per  ON nov.periodo  = per.id_unico
                                  LEFT JOIN gn_concepto con ON nov.concepto = con.id_unico
                                  WHERE     (per.tipoprocesonomina = $tproceso)
                                  AND       (nov.empleado          = $empleado)
                                  AND       (per.acumulable        = 1)
                                  AND       (nov.periodo           IN ($y))
                                  GROUP BY  nov.concepto";
            $res_x = $mysqli->query($sql_x);
            while($row_x = mysqli_fetch_row($res_x)){
                $data[] = array($row_x[0]=>$row_x[1]);
            }
           
        }else{
 
          $sql = "SELECT    nov.periodo
	        FROM      gn_novedad nov
			LEFT JOIN gn_periodo per ON nov.periodo = per.id_unico
			WHERE     per.tipoprocesonomina = $tproceso
			AND       nov.empleado          = $empleado
			AND 	  per.acumulable        = 1
			GROUP BY  per.id_unico
			ORDER BY  per.id_unico DESC
			LIMIT     0, $n";
                $res = $mysqli->query($sql);
                while($row = mysqli_fetch_row($res)){
                        $y .= $row[0].",";
                }
                $y = substr($y, 0, strlen($y)-1);
                if(empty($y)){
                	$data[]=0;
                }else{
                $sql_x = "SELECT    con.id_unico, SUM(nov.valor) FROM gn_novedad nov
                                  LEFT JOIN gn_periodo per  ON nov.periodo  = per.id_unico
                                  LEFT JOIN gn_concepto con ON nov.concepto = con.id_unico
                                  WHERE     (per.tipoprocesonomina = $tproceso)
                                  AND       (nov.empleado          = $empleado)
                                  AND       (per.acumulable        = 1)
                                  AND       (nov.periodo           IN ($y))
                                  AND       (nov.concepto          = $con)
                                  GROUP BY  nov.concepto";
		            $res_x = $mysqli->query($sql_x);
		            while($row_x = mysqli_fetch_row($res_x)){
		                $data[$row_x[0]] = $row_x[1];
		            }
                }

        }
     return $data;    
}


function CalcularPrima($empleado,$concepto,$periodo,$proceso){
        
    require ('../Conexion/conexion.php');
    global $mysqli;
    $anno = $_SESSION['anno'];
    $Fperiodo = "SELECT fechafin, fechainicio FROM gn_periodo WHERE id_unico= '$periodo'";
    $Fecha = $mysqli->query($Fperiodo);
    $FechaP = mysqli_fetch_row($Fecha);

    $fecha = "SELECT * from gf_parametrizacion_anno where id_unico = '$anno'";
    $res = $mysqli->query($fecha);
    $row = mysqli_fetch_row($res);
    $FN = Annos($FechaP[1], 1);

    $mes = MesesFecha($FN, 1);
   
            
    $consulta = "SELECT SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
          . "WHERE n.concepto = '$concepto'  AND n.empleado = '$empleado' AND c.clase = 1 AND c.unidadmedida = 1 "
            . "AND n.fecha BETWEEN '$mes' AND '$FechaP[0]' ";
    $rescon = $mysqli->query($consulta);
    $valor = mysqli_fetch_row($rescon);
    
    $Total = $valor [0] / 12;
    
    return $Total;
}

function DiasFecha($fecha,$dias){
  $dias = $dias - 1; 
  $nuevafecha = strtotime ( $dias." day" , strtotime ( $fecha ) ); 
  $nuevafecha = date ( 'Y-m-j' , $nuevafecha ); //formatea nueva fecha 
  return $nuevafecha;
  
} //retorna valor de la fecha 

function MesesFecha($fecha,$meses){
  #$meses = $meses - 1; 
  $nuevafecha = strtotime ( $meses." month" , strtotime ( $fecha ) ); 
  $nuevafecha = date ( 'Y-m-j' , $nuevafecha );
      
  return $nuevafecha;
  
} //retorna valor de la fecha 

function DiasRestar($fecha){
   
  $nuevafecha = strtotime ( '-2 day' , strtotime ( $fecha ) ); 
  $nuevafecha = date ( 'Y-m-j' , $nuevafecha ); //formatea nueva fecha 
  return $nuevafecha;
  
} //retorna valor de la fecha 

function FestivosFecha($fecha,$dias){

  require ('../Conexion/conexion.php');
  global $mysqli;
  $nuevafecha = strtotime ( $dias." day" , strtotime ( $fecha ) ); 
  $nuevafecha = date ( 'Y-m-j' , $nuevafecha );

  $sql1 = "SELECT id_unico, fecha , descripcion FROM gf_festivos WHERE fecha BETWEEN '$fecha' AND '$nuevafecha'";
  
  $fec2 = $mysqli->query($sql1);

  $fecha_div = explode("-", $nuevafecha);
      $anion = $fecha_div[0];
      $mesn = $fecha_div[1];
      $dian = $fecha_div[2];
  
  while($Dfes = mysqli_fetch_row($fec2)){
                
          $dian = $dian + 1 ;
  }

  $nuevafecha = $anion.'-'.$mesn.'-'.$dian;

  
}

function PeriodosFaltantes($fecha,$dias){

  require ('../Conexion/conexion.php');
  global $mysqli;
  $nuevafecha = strtotime ( $dias." day" , strtotime ( $fecha ) ); 
  $nuevafecha = date ( 'Y-m-j' , $nuevafecha );

  #consulta los periodos que coincidan la fehca de inicio de la incapacidad  que no esten cerrados
  $sql = "SELECT id_unico, codigointerno, fechainicio, fechafin FROM gn_periodo WHERE fechainicio <= $fecha  AND liquidado !=1 AND id_unico !=1";
  $res = $mysqli->query($sql);
  $nres = mysqli_num_rows($res);
  
  #valida si existe uno o mas periodos cuya fecha de inicio de la incpacidad o la licencia sea posterior o igual a la fecha inicial del periodo 
  if($nres >=1){

    $resu = mysqli_fetch_row($res);

    # consulta los periodos que coincidan la fecha final de la incapacidad que no esten cerrados
    $sql1 = "SELECT id_unico, codigointerno, fechainicio, fechafin FROM gn_periodo WHERE fechafin >= $nuevafecha AND liquidado !=1 AND id_unico !=1";
    $res1 = $mysqli->query($sql1);
    $nres = mysqli_num_rows($res1); 

    #valida si existe uno o mas periodos cuya fecha fianl de la incapacidad o licencia sea anterior o igual a la fehca final del periodo
    if($nres >=1){
  
      $resu1 = mysqli_fetch_row($resu1);

      $nper = "SELECT id_unico, COUNT(id_unico) FROM gn_periodo WHERE  AND id_unico  BETWEEN $resu[0] AND resu1[0]";
      $res2 = $mysqli->query($nper);
      $resu2 = mysqli_fetch_row($res2);

      $numper = $resu2;
    }else {

      $numper = 0;
    } 
  }else{

    $numper = 0 ;
  }

  
  return $numper;
}

function Annos($fecha,$anios){
  $anios = $anios * -1; 
  $nuevafecha = strtotime ( $anios." year" , strtotime ( $fecha ) ); 
  $nuevafecha = date ( 'Y-m-j' , $nuevafecha ); //formatea nueva fecha 
  return $nuevafecha;
  
}