
<?php
  require_once('../Conexion/conexion.php');
  require_once("../Conexion/ConexionPDO.php");
  session_start();
  ini_set('max_execution_time', 0);
  session_start();        
  $con = new ConexionPDO();
  $compania = $_SESSION['compania'];
  $rowC = $con->Listar("SELECT    ter.id_unico,
                        ter.razonsocial,
                        UPPER(ti.nombre),
                        ter.numeroidentificacion,
                        dir.direccion,
                        tel.valor,
                        ter.ruta_logo,
                        IF(CONCAT_WS(' ',
             ter.nombreuno,
             ter.nombredos,
             ter.apellidouno,
             ter.apellidodos)
             IS NULL OR CONCAT_WS(' ',
             ter.nombreuno,
             ter.nombredos,
             ter.apellidouno,
             ter.apellidodos) = '',
             (ter.razonsocial),
             CONCAT_WS(' ',
             ter.nombreuno,
             ter.nombredos,
             ter.apellidouno,
             ter.apellidodos)) AS NOMBRE
        FROM gf_tercero ter
        LEFT JOIN   gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
        LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
        LEFT JOIN   gf_telefono  tel ON tel.tercero = ter.id_unico
        WHERE ter.id_unico = $compania");
        $razonsocial = $rowC[0][1];
        $nombreIdent = $rowC[0][2];
        $numeroIdent = $rowC[0][3];
        //$direccinTer = $rowC[0][7];
        $telefonoTer = $rowC[0][5];
        $ruta_logo = $rowC[0][6];
        $tipo = $_GET['tipo'];
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=ReporteIngreso-" . 23 . ".xls");
        $html = "";
        $html .= "<!doctype html>";
        $html .= "\n<html lang=\"en\">";
        $html .= "\n<head>";
        $html .= "\n\t<meta charset=\"UTF-8\">";
        $html .= "\n\t<meta name=\"viewport\" content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">";
        $html .= "\n\t<meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">";
        $html .= "\n\t<title>Reporte de Llegada</title>";
        $html .= "\n</head>";
        $html .= "\n<body>";
        $html .= "\n\t<table style='width: 100%; border-collapse: collapse;'>";
        $html .= "\n\t\t<thead>";
        $html .= "\n\t\t\t<tr>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000;' colspan='8'>$razonsocial <br/>NIT : $numeroIdent<br/></th>";
        $html .= "\n\t\t\t</tr>";
        $html .= "\n\t\t\t<tr>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>FASE</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>DURACIÓN</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>UNIDAD DE <br/>TIEMPO</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>TIPO DÍA</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>ESTADO</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>RESPONSABLE</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>ELEMENTO<br/> RELACIONAL</th>";
        $html .= "\n\t\t\t\t<th style='border: solid 1px #000; text-align: center;'>ELEMENTO RELACIONAL<br/> INCUMPLIMINETO</th>";
        $html .= "\n\t\t\t</tr>";
        $html .= "\n\t\t</thead>";        
        $html .= "\n\t\t<tbody>";
        #Listar
        $listar = "SELECT  IF(CONCAT(t.nombreuno,' ', t.nombredos,' ',t.apellidouno,' ',t.apellidodos)='', 
        (t.razonsocial),(CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos))) AS NOMBRE , 
        fp.id_unico, t.id_unico, t.numeroidentificacion, 
        fp.duracion, 
        fp.tipo_dia, td.nombre, 
        fp.tipo_proceso, tp.identificador, tp.nombre, 
        fp.fase, f.nombre,  
        fp.unidad_tiempo, ut.nombre, 
        fp.flujo_si, fps.duracion, 
        fp.flujo_no, fpn.duracion, 
        ef.id_unico, ef.nombre, 
        fp.estado, e.nombre
        FROM gg_flujo_procesal fp 
        LEFT JOIN gf_tercero t ON fp.tercero = t.id_unico 
        LEFT JOIN gg_tipo_dia td ON fp.tipo_dia = td.id_unico 
        LEFT JOIN gg_tipo_proceso tp ON fp.tipo_proceso = tp.id_unico 
        LEFT JOIN gg_fase f ON fp.fase = f.id_unico 
        LEFT JOIN gg_unidad_tiempo ut ON  fp.unidad_tiempo = ut.id_unico 
        LEFT JOIN gg_flujo_procesal fps ON fp.flujo_si = fps.id_unico 
        LEFT JOIN gg_flujo_procesal fpn ON fp.flujo_no = fpn.id_unico 
        LEFT JOIN gg_elemento_flujo ef ON ef.id_unico = f.elemento_flujo 
        LEFT JOIN gg_estado_proceso e ON fp.estado = e.id_unico 
        WHERE fp.tipo_proceso = $tipo";
        $resultado = $mysqli->query($listar);
        while($row = mysqli_fetch_row($resultado)){
          $html .= "\n\t\t\t<tr>";
          $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[11] - $row[19]</td>";
          $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[4]</td>";
          $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[13]</td>";
          $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[5]</td>";
          $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[21]</td>";
          $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$row[0]</td>";
          $compar = strtolower($row[19]);
          $es=  strtolower($row[21]);
          if (empty($row[14])){
            $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'></td>";
            if (empty($row[18])) { echo ''; } else {
              switch ($compar){ 
                  case ('etapa especial'):
                      echo '';
                  break;
                  case ('condicion'):
                  case ('condición'):
                      if($es=='cerrado' || $es=='anulado') { echo ''; } else {}
                  break;
              }
            }
          }else{
            if ($compar=='etapa especial'){} else {
              $flujo1 = "SELECT  fp.id_unico ,fp.tipo_proceso, tp.identificador, tp.nombre, fp.fase, f.nombre, "
              . "ef.id_unico, ef.nombre FROM gg_flujo_procesal fp "
              . "LEFT JOIN gg_tipo_proceso tp ON fp.tipo_proceso = tp.id_unico "
              . "LEFT JOIN gg_fase f ON fp.fase = f.id_unico "
              . "LEFT JOIN gg_elemento_flujo ef ON ef.id_unico = f.elemento_flujo "
              . "WHERE fp.id_unico = $row[14]";
              $flujo1 = $mysqli->query($flujo1);
              $flujo1=  mysqli_fetch_row($flujo1);
              $flj1 = ucwords(strtolower($flujo1[5]).' - '.$flujo1[7]);
              $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$flj1</td>";
            }
          }

           $compar = strtolower($row[19]);
            $es=  strtolower($row[21]);
            if (empty($row[16])){
              $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'></td>";
                if (empty($row[18])) { echo ''; } else {
                switch ($compar){
                    case ('etapa especial'):
                    break;
                    case ('condicion'):
                    case ('condición'):
                        if($es=='cerrado' || $es=='anulado') {} else {}
                    break;
                }
              }
            }else{
              $flujo2 = "SELECT  fp.id_unico ,fp.tipo_proceso, tp.identificador, tp.nombre, fp.fase, f.nombre, "
                      . "ef.id_unico, ef.nombre FROM gg_flujo_procesal fp "
                      . "LEFT JOIN gg_tipo_proceso tp ON fp.tipo_proceso = tp.id_unico "
                      . "LEFT JOIN gg_fase f ON fp.fase = f.id_unico "
                      . "LEFT JOIN gg_elemento_flujo ef ON ef.id_unico = f.elemento_flujo "
                      . "WHERE fp.id_unico = $row[16]";              
                      $flujo2 = $mysqli->query($flujo2);
                      $flujo2=  mysqli_fetch_row($flujo2);
                      $flj2 = ucwords(strtolower($flujo2[5]).' - '.$flujo2[7]);
                      $html .= "\n\t\t\t\t<td style='border: solid 1px #000; text-align: center; vertical-align: middle;'>$flj2</td>";
            }
          $html .= "\n\t\t\t</tr>";
        }
        $html .= "\n\t\t</tbody";
        $html .= "\n\t</table>";
        $html .= "\n</body>";
        $html .= "\n</html>";
        echo $html;
?>