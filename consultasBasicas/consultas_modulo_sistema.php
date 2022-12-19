<?php
/**
 * Created by PhpStorm.
 * User: SERVIDOR
 * Date: 23/06/2017
 * Time: 12:02 PM 
 */

#Conexión a la base de datos
require ('../Conexion/conexion.php');
require ('../funciones/funciones_menu.php');
@session_start();
$compania = $_SESSION['compania'];
$nit_ut  = "SELECT t.numeroidentificacion, u.rol
FROM gs_usuario u 
LEFT JOIN gf_tercero t ON u.tercero = t.id_unico 
WHERE t.id_unico =".$_SESSION['usuario_tercero'];
$r = $mysqli->query($nit_ut);
$r = mysqli_fetch_row($r);

switch ($_POST['x']) {
    case 1:
        #Declaramos una variable la cual retornara como string
        $html="";
        $html.= "<ul class='autoCheckbox'>";
        #Consulta para obtener los padres que en la tabla de relacion su valor sea nulo
        $sql="select main.id_unico,main.nombre "
                . "from gs_menu main left join gs_menu_aso maso on maso.menuhijo=main.id_unico where maso.id_unico is null";
        $result=$mysqli->query($sql);
        #obtenemos los valores devueltos por la consulta
        $rol = NULL;
        while ($row= mysqli_fetch_row($result)){
            #Armado de html dinamico
            $html.= '<li>';
            $html.= "<input type='checkbox' value='$row[0]' id='father$row[0]'/>";
            if(!empty(obtener_hijos($row[0], $rol))){
                $html.= '<a href="javascript:void(0)" class="glyphicon glyphicon-plus" id="plus'.$row[0].'" onclick="show_son('.$row[0].')"></a>';
            }
            $html.= '<span style="font-size:11px">'.ucwords(mb_strtolower($row[1])).'</span>';
            if(!empty(obtener_hijos($row[0], $rol))){
                $html.= '<ul id="hijos'.$row[0].'" class="collapse">';
                $html.= obtener_hijos($row[0], $rol);
                $html.= '</ul>';
                $html.= '</li>';
            }
        }
        $html.= "</ul>";
        $html.= '<script>';
        $html.= '$(function () {
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
        $html.= '</script>';
        #Retorna un html
        echo $html;
        break;
    #Guardar Menu- Roles
    case 2:
        if(!empty($_POST['rol']) && !empty($_POST['seleccionados'])) {
            $x = 0; $y = 0;
            $rol = $_POST['rol']; $seleccionados = explode(",",$_POST['seleccionados']);
            for ($a = 0; $a < count($seleccionados);$a++) {
                $y++;
                $inserted = save_rol_m($rol, $seleccionados[$a]);
                if($inserted == true) {
                    $x++;
                }
            }
            if($x == $y) {
                echo json_encode(true);
            } else {
                echo json_encode(false);
            }
        }
        break;
    #* Cargar Menu *Rol
    case 3:
        if(!empty($_POST['rol'])) {
            $rol = $_POST['rol'];
            #Declaramos una variable la cual retornara como string
            $html="";
            $html.= "<ul class='autoCheckbox'>";
            #Consulta para obtener los padres que en la tabla de relacion su valor sea nulo
            if($r[0]=='900849655'){
                $sql="select main.id_unico,main.nombre 
                    from gs_menu main 
                    LEFT JOIN gs_menu_compania mc ON main.id_unico = mc.menu 
                    WHERE main.predecesor IS NULL  AND mc.compania =$compania 
                    ORDER BY cast(main.orden as unsigned), main.id_unico ASC";
               
            } else {
                $sql="select main.id_unico,main.nombre 
                from gs_menu main 
                LEFT JOIN gs_menu_compania mc ON main.id_unico = mc.menu 
                WHERE main.predecesor IS NULL AND main.estado = 1 
                AND mc.compania =$compania and main.estado = 1 
                ORDER BY cast(main.orden as unsigned), main.id_unico ASC";
            }
            $result=$mysqli->query($sql);
            #obtenemos los valores devueltos por la consulta
            while ($row= mysqli_fetch_row($result)){
                #Armado de html dinamico
                $html.= '<li>';
                
                if(!empty(obtener_hijos($row[0], $rol))){
                    
                    if(exist_privilige($rol, $row[0]) == 0) {
                        $html.= "<input type='checkbox' value='$row[0]' id='father$row[0]' />";
                    } else {
                        $html.= "<input type='checkbox' value='$row[0]' id='father$row[0]' checked/>";
                        $html.="<a href='javascript:void(0)' class='glyphicon glyphicon-trash eliminar' title='Eliminar' id='btnD$row[0]' style='margin-left: 5px;margin-right: 5px' onclick='eliminar($rol, $row[0])'></a>";
                    }
                    $html.= '<a href="javascript:void(0)" class="glyphicon glyphicon-plus" id="plus'.$row[0].'" onclick="show_son('.$row[0].')"></a>';
                }else{
                    if(exist_privilige($rol, $row[0]) == 0) {
                        $html.= "<input type='checkbox' value='$row[0]' id='father$row[0]' />";
                    } else {
                        $html.= "<input type='checkbox' value='$row[0]' id='father$row[0]' checked/>";
                        $html.="<a href='javascript:void(0)' class='glyphicon glyphicon-trash eliminar' title='Eliminar' id='btnD$row[0]' style='margin-left: 5px;margin-right: 5px' onclick='eliminar($rol, $row[0])'></a>";
                    }
                }
                $html.= '<span style="font-size:11px;margin-left: 5px">'.ucwords(mb_strtolower($row[1])).'</span>';
                if(!empty(obtener_hijos($row[0], $rol))){
                    $html.= '<ul id="hijos'.$row[0].'" class="collapse">';
                    $html.= obtener_hijos($row[0], $rol);
                    $html.= '</ul>';
                    $html.= '</li>';
                }
              }
            $html.= "</ul>";
            $html.= '<script>';
            $html.= '$(function () {
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
            $html.= '</script>';
            #Retorna un html
            echo $html;
            break;
        }
        break;
    case 4:
        if(!empty($_POST['rol'] && !empty($_POST['option']))) {
            $rol = $_POST['rol'];
            $option = $_POST['option'];
            $sons = is_father($option);
            if(!empty($sons)) {
                $sons = substr($sons,0,strlen($sons)-1);
                $opts = explode(",",$sons);
                for ($a = 0; $a < count($opts); $a++) {
                    $option_x = $opts[$a];
                    if(exist_privilige($rol, $option_x) > 0) {
                        delete_privilige($rol, $option_X);
                    }
                }
            }
            $deleted = delete_privilige($rol, $option);
            echo $deleted;
        }
        break;
    case 5:
        if(!empty($_POST['father'])) {
            echo get_son_main_rol($_POST['father'], $_POST['rol']);
        }
        break;
    case 6:
        echo get_fathers_sys($_POST['sistema']);
        break;
    case 7:
        if(!empty($_POST['seleccionados']) AND !empty($_POST['sistema'])) {
            $sys = $_POST['sistema']; $x = explode(",",$_POST['seleccionados']);
            $b = 0; $c = 0;
            for ($a = 0; $a < count($x); $a++) {
                $obj = $x[$a]; $b++;
                $data = explode("-", $obj);
                $w = save_cod_req($data[0], $data[1], $data[2], $data[3], $data[4]);
                if($w == true){
                    $cod = get_max_id_req();
                    if($cod !== 0) {
                        $save = save_obj_req(1, $cod, $sys);
                        if($save == '1') {
                            $c++;
                        }
                    }
                }
            }
            if($b == $c) {
                echo json_encode(true);
            }else{
                echo json_encode(false);
            }
        }
        break;
    case 8:
        if(!empty($_POST['id'])) {
            $id = $_POST['id'];
            $result = delete_obj_req($id);
            echo json_encode($result);
        }
        break;
    case 9:
        $id = $_POST['id_unico']; $valor = $_POST['valor'];
        $result = update_state_obj($id, $valor);
        echo json_encode($result);
        break;
    #* Cargar Menú Compañía
    case 10:
        if(!empty($_POST['rol'])) {
            $rol = $_POST['rol'];
            #Declaramos una variable la cual retornara como string
            $html="";
            $html.= "<ul class='autoCheckbox'>";
            #Consulta para obtener los padres que en la tabla de relacion su valor sea nulo
            if($_SESSION['num_usuario']=='900849655'){
               
                $sql="select DISTINCT main.id_unico,main.nombre 
                from gs_menu main 
                WHERE main.predecesor IS NULL ORDER BY cast(main.orden as unsigned),  main.id_unico ASC";
            }
            $result=$mysqli->query($sql);
            #obtenemos los valores devueltos por la consulta
            while ($row= mysqli_fetch_row($result)){
                #Armado de html dinamico
                $html.= '<li>';
                
                if(!empty(obtener_hijostodos($row[0], $rol))){
                    
                    if(exist_compania($rol, $row[0]) == 0) {
                        $html.= "<input type='checkbox' value='$row[0]' id='father$row[0]' />";
                    } else {
                        $html.= "<input type='checkbox' value='$row[0]' id='father$row[0]' checked/>";
                        $html.="<a href='javascript:void(0)' class='glyphicon glyphicon-trash eliminar' title='Eliminar' id='btnD$row[0]' style='margin-left: 5px;margin-right: 5px' onclick='eliminar($rol, $row[0])'></a>";
                    }
                    $html.= '<a href="javascript:void(0)" class="glyphicon glyphicon-plus" id="plus'.$row[0].'" onclick="show_son('.$row[0].')"></a>';
                }else{
                    if(exist_compania($rol, $row[0]) == 0) {
                        $html.= "<input type='checkbox' value='$row[0]' id='father$row[0]' />";
                    } else {
                        $html.= "<input type='checkbox' value='$row[0]' id='father$row[0]' checked/>";
                        $html.="<a href='javascript:void(0)' class='glyphicon glyphicon-trash eliminar' title='Eliminar' id='btnD$row[0]' style='margin-left: 5px;margin-right: 5px' onclick='eliminar($rol, $row[0])'></a>";
                    }
                }
                $html.= '<span style="font-size:11px;margin-left: 5px">'.ucwords(mb_strtolower($row[1])).'</span>';
                if(!empty(obtener_hijostodos($row[0], $rol))){
                    $html.= '<ul id="hijos'.$row[0].'" class="collapse">';
                    $html.= obtener_hijostodos($row[0], $rol);
                    $html.= '</ul>';
                    $html.= '</li>';
                }
              }
            $html.= "</ul>";
            $html.= '<script>';
            $html.= '$(function () {
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
            $html.= '</script>';
            #Retorna un html
            echo $html;
            break;
        }
        break;
    #* Guardar Menú Compáñía
    case 11:
        if(!empty($_POST['rol']) && !empty($_POST['seleccionados'])) {
            $x = 0; $y = 0;
            $rol = $_POST['rol']; $seleccionados = explode(",",$_POST['seleccionados']);
            for ($a = 0; $a < count($seleccionados);$a++) {
                $y++;
                $inserted = save_compania_m($rol, $seleccionados[$a]);
                if($inserted == true) {
                    $x++;
                }
            }
            if($x == $y) {
                echo json_encode(true);
            } else {
                echo json_encode(false);
            }
        }
    break;
    case 12:
        if(!empty($_POST['rol'] && !empty($_POST['option']))) {
            $rol = $_POST['rol'];
            $option = $_POST['option'];
            $sons = is_father($option);
            //var_dump($sons);
            if(!empty($sons)) {
                $sons = substr($sons,0,strlen($sons)-1);
                $opts = explode(",",$sons);
                for ($a = 0; $a < count($opts); $a++) {
                    $option_x = $opts[$a];
                    $deleted = delete_compania($rol, $option_x);
                }
            }
            $deleted = delete_compania($rol, $option);
            echo $deleted;
        }
    break;
}