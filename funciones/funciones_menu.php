<?php
/**
 * Created by PhpStorm.
 * User: SERVIDOR
 * Date: 23/06/2017
 * Time: 11:59 AM
 */
/**
 * obtener_hijos
 *
 * Función para obtener los hijos del padre enviado
 *
 * @author  Alexander Numpaque
 * @package Menu
 * @param   int    $padre Id del padre
 * @return  string $html  Retorna la estructura de una lista con los hijos del padre es una función recursiva
 */
function obtener_hijos($padre, $rol){
    @session_start();
    $compania = $_SESSION['compania'];
    #Declaramos una variable la cual retornara como string
    $html = "";
    #Conexión a la base de datos
    require '../Conexion/conexion.php';
    #Consulta para obtener los hijos
    if($_SESSION['num_usuario']=='900849655'){
        $sql="SELECT main.id_unico, main.nombre, papa.nombre, papa.id_unico AS nom_papa 
        FROM gs_menu main 
        LEFT JOIN gs_menu papa ON main.predecesor = papa.id_unico 
        LEFT JOIN gs_menu_compania mc ON main.id_unico = mc.menu 
        WHERE main.predecesor = $padre  AND mc.compania =$compania 
        ORDER BY cast(main.orden as unsigned), main.id_unico ASC";
        
    } else {
        $sql="SELECT main.id_unico, main.nombre, papa.nombre, papa.id_unico AS nom_papa 
            FROM gs_menu main 
            LEFT JOIN gs_menu papa ON main.predecesor = papa.id_unico 
            LEFT JOIN gs_menu_compania mc ON main.id_unico = mc.menu 
            WHERE main.predecesor = $padre AND mc.compania =$compania AND main.estado = 1 
            ORDER BY cast(main.orden as unsigned), main.id_unico ASC";
    }
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    #obtenemos los datos devueltos
    while ($row = mysqli_fetch_row($result)){
        #Armado de html dinamico
        #Validamos que los valores retornados por la función no sean vacios
        #de esta forma obtenemos los hijos que son padres
        if(empty(obtener_hijos($row[0], $rol))){
            $html .= '<li>';
            if(exist_privilige($rol, $row[0]) > 0){
                $html .= "<input type='checkbox' value='$row[0]' id='son$row[0]' class='hijos$row[3]' checked />";
                $html .= "<a href='javascript:void(0)' class='glyphicon glyphicon-trash eliminar' title='Eliminar' id='btnD$row[0]' style='margin-left: 5px;margin-right: 5px' onclick='eliminar($rol, $row[0])'></a>";
            }else{
                $html .= "<input type='checkbox' value='$row[0]' id='son$row[0]' class='hijos$row[3]'/>";
            }
            $html .= '<span style="font-size:10px;">'.ucwords(mb_strtolower($row[1])).'</span>';
            $html .= "</li>";
        }else{
            $html .= '<li>';
            if(exist_privilige($rol, $row[0]) > 0){
                $html .= "<input type='checkbox' value='$row[0]' id='father$row[0]' checked />";
                $html .="<a href='javascript:void(0)' class='glyphicon glyphicon-trash eliminar' title='Eliminar' id='btnD$row[0]' style='margin-left: 5px;margin-right: 5px' onclick='eliminar($rol, $row[0])'></a>";
            } else {
                $html .= "<input type='checkbox' value='$row[0]' id='father$row[0]'/>";
            }
            $html .= '<a href="javascript:void(0)" class="glyphicon glyphicon-plus" id="plus'.$row[0].'" onclick="show_son('.$row[0].')"></a>';
            $html .= '<span style="font-size:10px">'.ucwords(mb_strtolower($row[1])).'</span>';
            $html .= '<ul id="hijos'.$row[0].'" class="collapse">';
            $html .=  obtener_hijos($row[0], $rol);
            $html .= '</ul>';
            $html .= '</li>';
        }
    }
    #Retorna html
    return $html;
}



/**
 * save_rol_m
 *
 * Función para registrar en la tabla gs_privilegios_rol
 *
 * @author  Alexander Numpaque
 * @package Menu
 * @param   int  $rol      Id del rol
 * @param   int  $menu     Id de la opción de menú
 * @return  bool $inserted Retornara verdadero cuando se haya insertdado correctamente
 */
function save_rol_m ($rol, $menu){
    require ('../Conexion/conexion.php');
    $inserted = false;
    #Buscar si existe 
    $cs = "SELECT * FROM gs_privilegios_rol WHERE rol=$rol AND menu= $menu";
    $result = $mysqli->query($cs);
    if(mysqli_num_rows($result) > 0){
        $result = true;
    } else {
        $sql = "INSERT INTO gs_privilegios_rol(rol, menu) VALUES ($rol, $menu)";
        $result = $mysqli->query($sql);
    }
    if($result == true){
        $inserted = true;
    }
    return $inserted;
    mysqli_close($mysqli);
}

/**
 * get_rol_user
 *
 * Función para buscar el rol del tercero logueado
 *
 * @author  Alexander Numpaque
 * @package Menu
 * @param   String $nuser   Nombre del usuario
 * @param   int    $tercero Id del tercero relacionado al usuario
 * @return  int    $rol     Id del rol del tercero
 */
function get_rol_user($nuser , $tercero){
    require ('./Conexion/conexion.php');
    $rol = 0;
    $sql = "SELECT rol FROM gs_usuario WHERE tercero = $tercero AND usuario = '$nuser'";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_row($result);
        $rol = $row[0];
    }
    return $rol;
    mysqli_close($mysqli);
}

/**
 * get_father_rol
 *
 * Función para obtener los padres que se ingresaron en la tabla rol
 *
$codigo
 * @param   int    $rol  Id del rol
 * @return  string $html Estructura de lista con los padres
 */
function get_father_rol ($rol){
    require ('./Conexion/conexion.php');
    $html = "";
    $html .= "<ul class=\"nav side-menu\">";
    $sql  = "SELECT main.id_unico,main.nombre, main.ruta FROM gs_menu main
    LEFT JOIN gs_menu_aso maso ON maso.menuhijo=main.id_unico
    LEFT JOIN gs_privilegios_rol prl ON main.id_unico = prl.menu
    WHERE maso.id_unico IS NULL AND prl.rol = $rol ORDER BY main.nombre";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    if(mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_row($result)){
            $html .= "<li>";
            if(get_n_son($row[0]) > 0){
                $html .= "<a class='option_main' id='padre_menu$row[0]' href=\"#\" onclick='seek_look_sons_main($row[0], $rol)'>".mb_strtoupper($row[1])."<span class=\"fa fa-chevron-right icons-main\"></span></a>";
                $html .= '<ul id="hijos_menu'.$row[0].'" class="nav child_menu" style="padding-left: 10px">';
                $html .= '</ul>';
            } else {
                if(!empty($row[2])){
                    $ruta = $row[2];
                }else{
                    $ruta = "#";
                }
                $html .= "<a class='option_main' id='padre_menu$row[0]' href='".$ruta."'>".mb_strtoupper($row[1])."</a>";
            }
            $html .= "</li>";
        }
    }
    $html .= "</ul>";
    return $html;
    mysqli_close($mysqli);
}

/**
 * get_son_main_rol
 *
 * Función para obtener los hijos respecto al padre enviado validando que el hijo este registrado en gs_privilegios_rol
 *
 * @author  Alexander Numpaque
 * @package Menu
 * @param   int    $father Id del padre
 * @param   int    $rol    Id del rol
 * @return  string $html   Estructura html de lista con los hijos esta función es recursiva
 */
function get_son_main_rol($father, $rol){
    $html = "";
    require ('../Conexion/conexion.php');
    $sql="SELECT  main.id_unico, main.nombre, papa.nombre, papa.id_unico, main.ruta, mv.ventana AS nom_papa, maso.id_unico AS menu_aso
        FROM      gs_menu main 
        LEFT JOIN gs_menu_aso maso   ON maso.menuhijo  = main.id_unico 
        LEFT JOIN gs_menu papa       ON maso.menupadre = papa.id_unico 
        LEFT JOIN gs_menu_ventana mv ON maso.id_unico  = mv.menuaso
        WHERE     maso.menupadre                       = $father
        ORDER BY  main.nombre ASC";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    #obtenemos los datos devueltos
    while ($row = mysqli_fetch_row($result)){
        #Armado de html dinamico
        #Validamos que los valores retornados por la función no sean vacios
        #de esta forma obtenemos los hijos que son padres
        if(exist_privilige_rol($rol, $row[0]) > 0){
            if(get_n_son_2($row[0]) > 0){
                $html .= '<li><a style="font-size:10px" id="padre_menu'.$row[0].'" onclick=\'seek_look_sons_main('.$row[0].','.$rol.');show_son_main('.$row[0].')\'>'.ucwords(mb_strtoupper($row[1])).'<span class="fa fa-chevron-right icons-main"></span></a>';
                $html .= '<ul id="hijos_menu'.$row[0].'" class="nav child_menu" style="padding-left: 10px;">';
                $html .= '</ul>';
                $html .= '</li>';
            } else {
                $html .= '<li><a href="'.$row[4].'?opt='.md5($row[0]).'&win='.md5($row[5]).'" style="font-size:10px">'.ucwords(mb_strtoupper($row[1])).'</a>';
                $html .= "</li>";
            }
        }
    }
    return $html;
}

/**
 * exist_privilige_rol
 *
 * Función para validar si el privilegio existe es decir si la opción de menu esta registrada en gs_privilegios_rol
 *
 * @author  Alexander Numpaque
 * @package Menu
 * @param   int $rol    Id del rol
 * @param   int $option Id de la opción de menú
 * @return  int $n      SI existe retornara 1 o mayor que 0
 */
function exist_privilige_rol($rol, $option){
    require ('../Conexion/conexion.php');
    $n = 0;
    echo $sql = "SELECT id_unico FROM gs_privilegios_rol WHERE rol = $rol AND menu = $option";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    if(mysqli_num_rows($result) > 0){
        $n = mysqli_num_rows($result);
    }
    return $n;
    mysqli_close($mysqli);
}

/**
 * exist_privilige
 *
 * Función para validar si el rol y la opción registrada existe en la tabla gs_privilegio_rol
 *
 * @author  Alexander Numpaque
 * @package Menu
 * @param   int  $rol    Id del rol
 * @param   int  $option Id de la opción de menú
 * @return  int  $n      Retorna $n diferente de cero si existe la opción de menú con el rol
 */
function exist_privilige($rol, $option){
    require ('../Conexion/conexion.php');
    $n = 0;
    $sql = "SELECT id_unico FROM gs_privilegios_rol WHERE rol = '$rol' AND menu = $option";
    $result = $mysqli->query($sql);
    if($result == true){
        $n = mysqli_num_rows($result);
    }
    return $n;
    mysqli_close($mysqli);
}

function exist_compania($rol, $option){
    require ('../Conexion/conexion.php');
    $n = 0;
    $sql = "SELECT id_unico FROM gs_menu_compania WHERE compania = '$rol' AND menu = $option";
    $result = $mysqli->query($sql);
    if($result == true){
        $n = mysqli_num_rows($result);
    }
    return $n;
    mysqli_close($mysqli);
}

/**
 * is_father
 *
 * Función para determinar si la opción es padre
 *
 * @author  Alexander Numpaque
 * @package Menu
 * @param   int    $father Id del menu padre
 * @return  String $sons   Cadena con los hijos relacionados al padre
 */
function is_father($father){
    require ('../Conexion/conexion.php');
    $sons = "";
    $sql = "SELECT id_unico FROM gs_menu WHERE predecesor = $father";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    if(mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_row($result)){
            $sons .= $row[0];
            $sons .= ",";
            if(!empty(is_father($row[0]))) {
                $sons .= is_father($row[0]);
            }
        }
    }
    return $sons;
    mysqli_close($mysqli);
}

/**
 * delete_privilige
 *
 * Función para eliminar el valor registrado en la base de datos
 *
 * @author  Alexander Numpaque
 * @package Rol
 * @param   int  $rol     Id del rol registrado
 * @param   int  $option  Id de la opción de menú
 * @return  bool $deleted Retorna verdadero si el registrado es eliminado correctamente
 */
function delete_privilige($rol, $option){
    require ('../Conexion/conexion.php');
    $deleted = false;
    $sql = "DELETE FROM gs_privilegios_rol WHERE rol = $rol AND menu = $option";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    if($result == true){
        $deleted = true;
    }
    return $deleted;
    mysqli_close($mysqli);
}

/**
 * get_n_son
 *
 * Función para validar si la opción de menú es padre
 *
 * @author  Alexander Numpaque
 * @package Menu
 * @param   int $father Id del menu padre
 * @return  int $x      Cantidad de hijos
 */
function get_n_son ($father){
    require ('Conexion/conexion.php');
    $x = 0;
    $sql = "SELECT menuhijo FROM gs_menu_aso WHERE menupadre = $father";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    if(mysqli_num_rows($result) > 0) {
        $x = mysqli_num_rows($result);
    }
    return $x;
}

/**
 * get_n_son_2
 *
 * Función para validar si la opción de menú es padre
 *
 * @author  Alexander Numpaque
 * @package Menu
 * @param   int $father Id del menu padre
 * @return  int $x      Cantidad de hijos
 */
function get_n_son_2 ($father){
    require ('../Conexion/conexion.php');
    $x = 0;
    $sql = "SELECT menuhijo FROM gs_menu_aso WHERE menupadre = $father";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    if(mysqli_num_rows($result) > 0) {
        $x = mysqli_num_rows($result);
    }
    return $x;
}

/**
 * get_fathers_sys
 *
 * Función para obtener los padres de las opciones de menu,
 * esta función se usa para obtener los botones, ventanas y campos de esta forma poder configurar el acceso y privilegios del usuario a nivel botón
 *
 * @author  Alexander Numpaque
 * @package Menu
 * @return  string $html Estructura html con los padres
 */
function get_fathers_sys($sys){
    require '../Conexion/conexion.php';
    $html = "";   #Declaramos una variable la cual retornara como string
    $html .= "<ul class='autoCheckbox'>";
    #Consulta para obtener los padres que en la tabla de relacion su valor sea nulo
    $sql = "SELECT main.id_unico,main.nombre FROM gs_menu main LEFT JOIN gs_menu_aso maso ON maso.menuhijo=main.id_unico WHERE maso.id_unico IS NULL";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    while ($row = mysqli_fetch_row($result)){#obtenemos los valores devueltos por la consulta
        $html .= '<li>';
        $html .= "<input type='checkbox' value='0' id='father$row[0]' style='margin-right: 5px'/>";
        if(get_n_son_2($row[0]) > 0) {#Validamos que sea padre
            $html .= '<a href="javascript:void(0)" class="glyphicon glyphicon-plus" id="plus'.$row[0].'" onclick="show_son('.$row[0].')" style="margin-right: 5px;"></a>';
        }
        $html .= '<span style="font-size:11px">'.ucwords(mb_strtolower($row[1])).'</span>';
        if(!empty(get_sons_sys($row[0], $sys))){#Valiamos que tenga hijos para imprimirlos
            $html .= '<ul class="collapse" id="listSons'.$row[0].'">';
            $html .= get_sons_sys($row[0], $sys);
            $html .= '</ul>';
            $html .= '</li>';
        }
    }
    $html .= "</ul>";
    $html .= '<script>';
    $html .= '$(function () {
            $(".autoCheckbox").on("click",function () {               
                var expr = "li input[type=checkbox]",$this=$(event.target);
                while ($this.length) {
                    $input=$this.closest("li").find(expr);
                    if ($input.length) {
                        if ($this[0]==event.target) {
                            checked = $this.prop("checked");
                            $input.prop("checked", checked).css("opacity","1.0");
                        }
                        checked=$input.is(":checked");
                        $this.prop("checked", checked).css("opacity",
                            (checked && $input.length!= $this.closest("li").find(expr+":checked").length)
                                ? "0.5" : "1.0");
                    }
                    $this=$this.closest("ul").closest("li").find(expr.substr(3)+":first");
                }
            });
        })';
    $html .= '</script>';
    return $html;
    mysqli_close($mysqli);
}

/**
 * get_sons_sys
 *
 * Función para obtener los hijos del padre enviado
 *
 * @author  Alexander Numpaque
 * @package Menu
 * @param   int    $padre Id del menú padre
 * @return  string $html  Con estructura para imprimir con los hijos del padre
 */
function get_sons_sys($padre, $sys){
    require '../Conexion/conexion.php';
    $html = ""; #Declaramos una variable la cual retornara como string
    #Consulta para obtener los hijos
    $sql = "SELECT  main.id_unico, main.nombre, papa.nombre AS nom_papa, maso.id_unico AS menu_aso
        FROM        gs_menu main 
        LEFT JOIN   gs_menu_aso maso ON maso.menuhijo  = main.id_unico 
        LEFT JOIN   gs_menu papa     ON maso.menupadre = papa.id_unico 
        WHERE       maso.menupadre = $padre
        ORDER BY    main.nombre ASC";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    while ($row = mysqli_fetch_row($result)){ #obtenemos los datos devueltos
        if(get_n_son_2($row[0]) == 0){
            $html .= '<li>';
            $html .= "<input type='checkbox' value='0' id='son$row[0]' style='margin-right: 5px'/>";
            if(!empty(get_window_sys($row[3], $sys))) {
                $html .= '<a href="javascript:void(0)" class="glyphicon glyphicon-plus" id="plus'.$row[0].'" onclick="show_window('.$row[3].')" style=\'margin-right: 5px\'></a>';
            }
            $html .= '<span style="font-size:11px">'.ucwords(mb_strtolower($row[1])).'</span>';
            $html .= get_window_sys($row[3], $sys);
            $html .= "</li>";
        }else{
            $html .= '<li>';
            $html .= "<input type='checkbox' value='0' id='father$row[0]' style='margin-right: 5px'/>";
            $html .= '<a href="javascript:void(0)" class="glyphicon glyphicon-plus" id="plus'.$row[0].'" onclick="show_son('.$row[0].')" style=\'margin-right: 5px\'></a>';
            $html .= '<span style="font-size:11px">'.ucwords(mb_strtolower($row[1])).'</span>';
            $html .= '<ul class="collapse" id="listSons'.$row[0].'">';
            $html .= get_sons_sys($row[0], $sys);
            $html .= '</ul>';
            $html .= '</li>';
        }
    }
    return $html;
}

/**
 * get_window_sys
 *
 * Función para obtener la ventana relacionada a la opción de menu hijo enviada
 *
 * @author  Alexander Numpaque
 * @package Menu
 * @param   int      $men_aso Id de menu asociado
 * @return  string   $html Estructura de lista en la cual imprimimos los campos y botones de la ventana
 */
function get_window_sys($men_aso, $sys){
    require '../Conexion/conexion.php';
    $html = ""; #Declaramos una variable la cual retornara como string
    $sql = "SELECT  menv.id_unico,ven.nombre,ven.id_unico FROM gs_menu_ventana menv
        LEFT JOIN   gs_ventana ven ON menv.ventana = ven.id_unico                        
        WHERE       menv.menuaso                   = $men_aso";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    if(mysqli_num_rows($result) > 0){ #Validamos que la consulta retorne valores
        while ($row = mysqli_fetch_row($result)){ #Creamos un array númerico con los valores retornados por la consulta
            if(!empty($row[0])){
                #Armado de html dinamico
                $html .= "<ul class=\"collapse\" id=\"listWindow$men_aso\">";
                $html .= '<li>';
                $html .= "<input type='checkbox' value='0' id='window$row[2]' style='margin-right: 5px'/>";
                $html .= '<a href="javascript:void(0)" class="glyphicon glyphicon-plus" id="plusd'.$row[2].'" onclick="show_info('.$row[2].')" style=\'margin-right: 5px\'></a>';
                $html .= '<span style="font-size:11px">Ventana:'.PHP_EOL.ucwords(mb_strtolower($row[1])).'</span>';
                $html .= '<ul class="collapse" id="listData'.$row[2].'">';
                $html .= get_buttons_sys($row[0], $sys);
                $html .= '</ul>';
                $html .= '</li>';
                $html .= '</ul>';
            }
        }
    }
    return $html;
}

/**
 * get_inputs_sys
 *
 * Función para obtener los botones relacionados a la ventana enviada
 *
 * @author  Alexander Numpaque
 * @package Menu
 * @param   int    $menu_v Id de menú ventana
 * @return  string $html   Estructura de listado para obtener los botones
 */
function get_inputs_sys($menu_v, $sys){
    require '../Conexion/conexion.php';
    $html = ""; #Declaramos una variable la cual retornara como string
    $sql1 = "SELECT cmp.id_unico, cmp.nombre, mv.ventana, familia.id_unico, familia.menuhijo 
        FROM        gs_ventana_campo ven_campo
        LEFT JOIN   gs_campo cmp            ON   ven_campo.campo       = cmp.id_unico
        LEFT JOIN   gs_menu_ventana mv      ON   ven_campo.menuventana = mv.id_unico
        LEFT JOIN   gs_menu_aso familia     ON   mv.menuaso            = familia.id_unico
        WHERE       ven_campo.menuventana   =    $menu_v
        ORDER BY    cmp.nombre ASC";
    /** @var Connection $mysqli */
    $result1=$mysqli->query($sql1);
    if(mysqli_num_rows($result1) > 0){
        $html .= '<li>';
        $html .= "<input type='checkbox' value='0' id='input$menu_v' style='margin-right: 5px'/>";
        $html .= '<a href="javascript:void(0)" class="glyphicon glyphicon-plus" id="plusi'.$menu_v.'" onclick="show_inputs('.$menu_v.')" style=\'margin-right: 5px\'></a>';
        $html .= '<span style="font-size:11px">Campos:</span>';
        $html .= "<ul class=\"collapse\" id=\"listInputs$menu_v\">";
        while($row1 = mysqli_fetch_row($result1)){
            $html .= '<li>';
            $x = "$row1[3]-$row1[4]-$row1[2]-$row1[0]-NULL";
            $codigo = get_id_cod_req($row1[3], $row1[4], $row1[2], $row1[0], NULL);
            if(count(get_obj_req($codigo, $sys)) > 0){
                $data = get_obj_req($codigo, $sys);
                $html .= "<input type='checkbox' value='$x' id='ipt$row1[0]' checked disabled readonly style='margin-right: 5px'/>";
                $html .= "<a href='javascript:delete_obj($data[0])' class='glyphicon glyphicon-trash' style='margin-right: 5px' title='Eliminar'></a>";
                if($data[1] == 1){
                    $html .= "<a href='javascript:modify_obj($data[0], $data[1])' id='lnkA$data[0]' class='glyphicon glyphicon-remove' data-value='0' style='margin-right: 5px;' title='Desactivar'></a>";
                }else if($data[1] == 0){
                    $html .= "<a href='javascript:modify_obj($data[0], $data[1])' id='lnkD$data[0]' class='glyphicon glyphicon-ok' data-value='1' style='margin-right: 5px;' title='Activar'></a>";
                }
                $html .= "<script>$('#input$menu_v').prop(\"checked\", true)</script>";
            }else{
                $html .= "<input type='checkbox' value='$x' id='ipt$row1[0]' style='margin-right: 5px'/>";
            }
            $html .= '<span style="font-size:11px">'.ucwords(mb_strtolower($row1[1])).'</span>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        $html .= '</li>';
    }
    return $html; #Retornamos el html
}

/**
 * get_buttons_sys
 *
 * Función para obtener los botones relacionados a la ventana
 *
 * @author  Alexander Numpaque
 * @package Ventana
 * @param   int    $menu_v Identificador de menu ventana
 * @return  string $html   Estructura con el listado de botones
 */
function get_buttons_sys($menu_v, $sys){
    require '../Conexion/conexion.php'; #Declaramos una variable la cual retornara como string
    $html = "";
    $sql = "SELECT  btn.id_unico, btn.nombre, mv.ventana, familia.id_unico, familia.menuhijo 
        FROM        gs_ventana_boton ven_btn
        LEFT JOIN   gs_boton btn            ON   ven_btn.boton       = btn.id_unico
        LEFT JOIN   gs_menu_ventana mv      ON   ven_btn.menuventana = mv.id_unico
        LEFT JOIN   gs_menu_aso familia     ON mv.menuaso = familia.id_unico
        WHERE       ven_btn.menuventana                              = $menu_v
        ORDER BY    btn.nombre ASC";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    if(mysqli_num_rows($result) > 0){ #Validamos que la consulta retorne valores mayores que 0
        $html .= '<li>';
        $html .= "<input type='checkbox' value='0' id='buttons$menu_v' style='margin-right: 5px'/>";
        $html .= '<a href="javascript:void(0)" class="glyphicon glyphicon-plus" id="plusb'.$menu_v.'" onclick="show_buttons('.$menu_v.')" style=\'margin-right: 5px\'></a>';
        $html .= '<span style="font-size:11px">Botones:</span>';
        $html .= '<ul class="collapse" id="listButtons'.$menu_v.'">';
        while ($row = mysqli_fetch_row($result)){
            $html .= '<li>';
            $x = "$row[3]-$row[4]-$row[2]-NULL-$row[0]";
            $codigo = get_id_cod_req($row[3], $row[4], $row[2], NULL, $row[0]);
            if(count(get_obj_req($codigo, $sys)) > 0){
                $data = get_obj_req($codigo, $sys);
                $html .= "<input type='checkbox' value='$x' id='btn$row[0]' disabled readonly checked style='margin-right: 5px'/>";
                $html .= "<a href='javascript:delete_obj($data[0])' class='glyphicon glyphicon-trash' style='margin-right: 5px' title='Eliminar'></a>";
                if($data[1] == 1){
                    $html .= "<a href='javascript:modify_obj($data[0], $data[1])' class='glyphicon glyphicon-remove' data-value='0' id='lnkA$data[0]' style='margin-right: 5px' title='Desactivar'></a>";
                }else if($data[1] == 0){
                    $html .= "<a href='javascript:modify_obj($data[0], $data[1])' class='glyphicon glyphicon-ok' data-value='1' id='lnkD$data[0]' style='margin-right: 5px' title='Activar'></a>";
                }
                $html .= "<script>$('#buttons$menu_v').prop(\"checked\", true)</script>";
            }else{
                $html .= "<input type='checkbox' value='$x' id='btn$row[0]' style='margin-right: 5px'/>";
            }
            $html .= '<span style="font-size:11px">'.ucwords(mb_strtolower($row[1])).'</span>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        $html .= '</li>';
    }
    #retornamos el html
    return $html;
}

/**
 * get_data_button
 *
 * Función para Obtener el nombre del boton
 *
 * @author  Alexander Numpaque
 * @package Ventana
 * @param   int    $id_unico Id del botón
 * @return  string $html     Nombre del botón
 */
function get_data_button($id_unico){
    require ('../Conexion/conexion.php');
    $nombre = "";
    $sql = "SELECT nombre FROM gs_boton WHERE id_unico = $id_unico";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_row($result);
        $nombre = $row[0];
    }
    mysqli_close($mysqli);
    return $nombre;
}

/**
 * save_obj_req
 *
 * Función para guardar en la tabla gs_objeto_requerido
 *
 * @author  Alexander Numpaque
 * @package Ventana
 * @param   tinyint $active   Guardará 1 ó 0, es decir, 1 si es activo ó 0 si no esta activo
 * @param   int     $cod_r    String Código del objeto requerido BTN+ numero si el objeto es botón. Si el objeto es un input INP+numero
 * @param   int     $sys      Identificador del sistema al que se van a relacionar los campos
 * @return  int     $inserted si es insertado retornara 1 y si no es insertado retornara 0
 */
function save_obj_req($active, $cod_r, $sys){
    require ('../Conexion/conexion.php');
    $inserted = 0;
    $sql = "INSERT INTO gs_objeto_requerido(activo, codi_relacionado, sistema) VALUES ($active, '$cod_r', $sys)";
    /** @var TYPE_NAME $mysqli */
    $result = $mysqli->query($sql);
    if($result == true){
        $inserted = 1;
    }
    return $inserted;
}

/*
 * get_obj_req
 *
 * Función para obtener el id y si el objeto esta activo
 *
 * @author Alexander Numpaque
 * @package Ventana
 * @param  String $cor_r Código de objeto requerido
 * @param  int    $sys   Identificador del sistema con el que se registro
 * @return array  $data  Array con el identificador del objeto y si esta activo
 */
function get_obj_req($cor_r, $sys){
    require ('../Conexion/conexion.php');
    $data = array();
    $sql = "SELECT id_unico, activo FROM gs_objeto_requerido WHERE codi_relacionado = '$cor_r' AND sistema = $sys";
    /** @var TYPE_NAME $mysqli */
    $rs = $mysqli->query($sql);
    if(mysqli_num_rows($rs) > 0){
        $row = mysqli_fetch_row($rs);
        $data[0] = $row[0];
        $data[1] = $row[1];
    }
    return $data;
}

/**
 * delete_obj_req
 *
 * Función para eliminar objeto requerido
 *
 * @author  Alexander Numpaque
 * @package Ventana
 * @param   int  $id_unico Identificador del registro a eliminar
 * @return  bool $deleted  Retorna verdadero cuando el registro es eliminado
 */
function delete_obj_req($id_unico){
    require ('../Conexion/conexion.php');
    $deleted = false;
    $sql = "DELETE FROM gs_objeto_requerido WHERE id_unico = $id_unico";
    /** @var TYPE_NAME $mysqli */
    $rs = $mysqli->query($sql);
    if($rs == true){
        $deleted = true;
    }
    return $deleted;
}

/**
 * update_state_obj
 *
 * Función para cambiar el estado del objeto
 *
 * @author  Alexander Numpaque
 * @package Ventana
 * @param   int  $id_unico Id del registro a cambiar el estado
 * @param   int  $valor    0 si esta inactivo ó 1 si es esta activo
 * @return  bool $update   Retorna verdadero si el registro es modificado
 */
function update_state_obj($id_unico, $valor){
    require ('../Conexion/conexion.php');
    $updated = false;
    $sql = "UPDATE gs_objeto_requerido SET activo = '$valor' WHERE id_unico = $id_unico";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    if($result == true){
        $updated = true;
    }
    return $updated;
}

/**
 * save_cod_req
 *
 * Función para guardar el codigo relacionado
 *
 * @author  Alexander Numpaque
 * @package Ventana
 * @param   int   $family   Id de menu asociado
 * @param   int   $window   Id de ventana
 * @param   int   $input    Id de campo
 * @param   int   $button   Id de botón
 * @return  bool  $inserted Si el valor es registrado retornara verdadero
 */
function save_cod_req($family, $menu, $window, $input, $button){
    require ('../Conexion/conexion.php');
    $inserted = false;
    $sql = "INSERT INTO gs_codigo_relacionado(menuAsociado, menu, ventana, campo, boton) VALUES ($family, $menu, $window, $input, $button)";
    /** @var Connection $mysqli */
    $rs = $mysqli->query($sql);
    if($rs == true) {
        $inserted = true;
    }
    return $inserted;
}

/**
 * get_id_cod_req
 *
 * Función para obtener el id del codigo relacionado
 *
 * @author  Alexander Numpaque
 * @package Ventana
 * @param   int $family Id de menú asociado
 * @param   int $window Id de la ventana
 * @param   int $input  Id del campo
 * @param   int $button Id del botón
 * @return  int $id     Id del codigo relacionado
 */
function get_id_cod_req($family, $menu, $window, $input, $button){
    require ('../Conexion/conexion.php');
    $id = 0;
    $sql = "";
    if(empty($button)){
        $sql = "SELECT id_unico FROM gs_codigo_relacionado WHERE menuAsociado = $family AND menu = $menu AND ventana = $window AND campo = $input";
    }
    if(empty($input)){
        $sql = "SELECT id_unico FROM gs_codigo_relacionado WHERE menuAsociado = $family AND menu = $menu AND ventana = $window AND boton = $button";
    }
    $rs = $mysqli->query($sql);
    if(mysqli_num_rows($rs) > 0) {
        $row = mysqli_fetch_row($rs);
        $id = $row[0];
    }
    return $id;
}

function get_max_id_req(){
    require ('../Conexion/conexion.php');
    $id = 0;
    $sql = "SELECT MAX(id_unico) FROM gs_codigo_relacionado";
    $rs = $mysqli->query($sql);
    if(mysqli_num_rows($rs) > 0){
        $row = mysqli_fetch_row($rs);
        $id = $row[0];
    }
    return $id;
}

function obtener_hijostodos($padre, $rol){
    @session_start();
    $compania = $_SESSION['compania'];
    #Declaramos una variable la cual retornara como string
    $html = "";
    #Conexión a la base de datos
    require '../Conexion/conexion.php';
    #Consulta para obtener los hijos
    $sql="SELECT DISTINCT main.id_unico, main.nombre, papa.nombre, papa.id_unico AS nom_papa 
    FROM gs_menu main 
    LEFT JOIN gs_menu papa ON main.predecesor = papa.id_unico 
    WHERE main.predecesor = $padre ORDER BY cast(main.orden as unsigned), main.id_unico ASC";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    #obtenemos los datos devueltos
    while ($row = mysqli_fetch_row($result)){
        #Armado de html dinamico
        #Validamos que los valores retornados por la función no sean vacios
        #de esta forma obtenemos los hijos que son padres
        if(empty(obtener_hijostodos($row[0], $rol))){
            $html .= '<li>';
            if(exist_compania($rol, $row[0]) > 0){
                $html .= "<input type='checkbox' value='$row[0]' id='son$row[0]' class='hijos$row[3]' checked />";
                $html .= "<a href='javascript:void(0)' class='glyphicon glyphicon-trash eliminar' title='Eliminar' id='btnD$row[0]' style='margin-left: 5px;margin-right: 5px' onclick='eliminar($rol, $row[0])'></a>";
            }else{
                $html .= "<input type='checkbox' value='$row[0]' id='son$row[0]' class='hijos$row[3]'/>";
            }
            $html .= '<span style="font-size:10px;">'.ucwords(mb_strtolower($row[1])).'</span>';
            $html .= "</li>";
        }else{
            $html .= '<li>';
            if(exist_compania($rol, $row[0]) > 0){
                $html .= "<input type='checkbox' value='$row[0]' id='father$row[0]' checked />";
                $html .="<a href='javascript:void(0)' class='glyphicon glyphicon-trash eliminar' title='Eliminar' id='btnD$row[0]' style='margin-left: 5px;margin-right: 5px' onclick='eliminar($rol, $row[0])'></a>";
            } else {
                $html .= "<input type='checkbox' value='$row[0]' id='father$row[0]'/>";
            }
            $html .= '<a href="javascript:void(0)" class="glyphicon glyphicon-plus" id="plus'.$row[0].'" onclick="show_son('.$row[0].')"></a>';
            $html .= '<span style="font-size:10px">'.ucwords(mb_strtolower($row[1])).'</span>';
            $html .= '<ul id="hijos'.$row[0].'" class="collapse">';
            $html .=  obtener_hijostodos($row[0], $rol);
            $html .= '</ul>';
            $html .= '</li>';
        }
    }
    #Retorna html
    return $html;
}
function save_compania_m ($rol, $menu){
    require ('../Conexion/conexion.php');
    $inserted = false;
    #Buscar si existe 
    $cs = "SELECT * FROM gs_menu_compania WHERE compania=$rol AND menu= $menu";
    $result = $mysqli->query($cs);
    if(mysqli_num_rows($result) > 0){
        $result = true;
    } else {
        $sql = "INSERT INTO gs_menu_compania(compania, menu) VALUES ($rol, $menu)";
        $result = $mysqli->query($sql);
    }
    if($result == true){
        $inserted = true;
    }
    return $inserted;
    mysqli_close($mysqli);
}

function delete_compania($rol, $option){
    require ('../Conexion/conexion.php');
    $deleted = false;
    
    $sql = "DELETE FROM gs_menu_compania WHERE compania = $rol AND menu = $option";
    /** @var Connection $mysqli */
    $result = $mysqli->query($sql);
    if($result == true){
        $deleted = true;
    }
    return $deleted;
    mysqli_close($mysqli);
}

