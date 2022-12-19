<?php require '../Conexion/ConexionPDO.php';                                                  
require '../Conexion/conexion.php';                    
require '../jsonPptal/funcionesPptal.php';
ini_set('max_execution_time', 0);
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$anno       = anno($panno);
$action     = $_REQUEST['action'];
$fechaa     = date('Y-m-d');
$calendario = CAL_GREGORIAN;
##******** Buscar Centro De Costo ********#
$cc         = $con->Listar("SELECT * FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $panno");
$c_costo    = $cc[0][0];
$pro        = $con->Listar("SELECT * FROM gf_proyecto WHERE nombre='Varios' AND compania = $compania");
$proyecto   = $pro[0][0]; 
switch ($action){
    #* Buscar Conceptos
    case 1:
        $html  = '';
        $t     = ''; 
        $orden =$_REQUEST['orden'];
        IF(!empty($_REQUEST['clase'])){
            $t .=' AND c.clase = '.$_REQUEST['clase'];
        }
        $rowcI = $con->Listar("SELECT DISTINCT c.id_unico, c.codigo, c.descripcion
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
            LEFT JOIN gn_periodo p on n.periodo = p.id_unico 
            WHERE p.parametrizacionanno = $panno  $t
            ORDER BY cast(c.id_unico as unsigned) $orden");
        for ($i = 0; $i < count($rowcI); $i++) {
            $html .= '<option value="'.$rowcI[$i][0].'">'.$rowcI[$i][1].' - '.$rowcI[$i][2].'</option>';
        }
        echo $html;
    break;
    
    #Añadir Fecha
    case 2:
        $fechaI = fechaC($_REQUEST['fid']);
        $fechaF = fechaC($_REQUEST['ffd']);

        $fechaI = date_create($fechaI);
        $fechaF = date_create($fechaF);

        $interval = date_diff($fechaI, $fechaF);
        echo $interval->format('%a');
    break;

    #Actualizar Orden

    case 3:
        $id = $_REQUEST['id'];
        $or = $_REQUEST['val'];
        if ($or=="") {
           $or='NULL';
        }else{
              $or=$or;
        }
        $updt = "UPDATE gn_concepto SET orden = $or
         WHERE id_unico = $id"; 
        $updt = $mysqli->query($updt);
        echo 1;
    break;

    #Cargar Periodos Por Tipo 
    case 4:
        $html = '';
        $tipo = $_REQUEST['tipo'];
        $row = $con->Listar("SELECT id_unico, codigointerno FROM gn_periodo WHERE parametrizacionanno = $panno AND tipoprocesonomina = $tipo ORDER BY id_unico DESC");
        for ($i = 0; $i < count($row); $i++) {
            $html .= '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
        }
        echo $html;
    break;
}

