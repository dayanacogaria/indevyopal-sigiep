<?php
require_once './head_listar.php';
require_once './Conexion/conexion.php';
$ficha = $_GET['ficha'];
$sqlFicha = "select id_unico,descripcion from gf_ficha where md5(id_unico)='$ficha'";
$resultFicha = $mysqli->query($sqlFicha);
$ficha = mysqli_fetch_row($resultFicha);
$idFicha = $ficha[0];
$descripcion = $ficha[1];
?>
        <title>Registrar Ficha Inventario</title>
        <style>
            /*Estilos tabla*/
            table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
            table.dataTable tbody td,table.dataTable tbody td{padding:1px}
            .dataTables_wrapper .ui-toolbar{padding:2px}
            /*Campos dinamicos*/
            .campoD:focus {
                border-color: #66afe9;
                outline: 0;
                -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
                    box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);            
            }
            .campoD:hover{
                cursor: pointer;
            }
            /*Campos dinamicos label*/
            .valorLabel{
                font-size: 10px;
            }
            .valorLabel:hover{
                cursor: pointer;
                color:#1155CC;
            }
            /*td de la tabla*/
            .campos{
                padding: 0px;
                font-size: 10px
            }
            /*cuerpo*/
            body{
                font-size: 10px
            }
            .form-control{
                padding: 2px;
            }            
            .cabeza{
                white-space:nowrap;
                padding: 20px;
            }
            .campos{
                padding:-20px;
            }
        </style>
    </head>
    <body onload="limpiarCampos()">
        <div class="container-fluid text-left">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-center" style="margin-top:-22px;">
                    <h2 class="tituloform" align="center">Ficha Inventario</h2>       
                    <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((strtolower($descripcion)));?></h5>
                    <p align="center" style="margin-bottom: 25px; margin-top:-5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="col-sm-8 col-sm-offset-3 text-left" style=";margin-bottom:-16px" align="">
                        <div class="client-form">                            
                            <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarFichaInventarioJson.php" style="margin-top:-15px">
                                <input type="hidden" name="sltFicha" id="sltFicha" class="hidden" value="<?php echo $idFicha ?>"/>                                
                                <div class="col-sm-2" style="margin-right:70px">
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Elemento Ficha:
                                    </label>
                                    <select class="form-control input-sm" name="sltElementoFicha" id="sltElementoFicha" title="Seleccione elemento ficha" style="width:150px;height:26px;padding:2px;cursor: pointer" required>
                                        <option value="">Elemento Ficha</option>
                                        <?php 
                                        $sql = "SELECT id_unico,nombre FROM gf_elemento_ficha
                                                ";
                                        $result = $mysqli->query($sql);
                                        while($fila=mysqli_fetch_row($result)){
                                            echo '<option value="'.$fila[0].'">'.ucwords(strtolower($fila[1])).'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Obligatorio:
                                    </label>
                                    <div>
                                        <input type="radio" name="optObligatorio" value="1" id="optObl1" title="Indique si es obligatorio"/>SI
                                        <input type="radio" name="optObligatorio" value="2" id="optObl2" title="Indique si no es obligatorio" checked/>NO
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Autogenerado:
                                    </label>
                                    <div>
                                        <input type="radio" name="optAutoGenerado" value="1" id="optAutoG1" title="Indique si es obligatorio"/>SI
                                        <input type="radio" name="optAutoGenerado" value="2" id="optAutoG2" title="Indique si no es obligatorio" checked/>NO
                                    </div>
                                </div>                                    
                                <div class="col-sm-1" style="margin-top:30px">                                    
                                    <button type="submit" id="btnGuardar" class="btn sombra btn-primary" title="Guardar cierre contable"><li class="glyphicon glyphicon-floppy-disk"></li></button>                                    
                                </div>
                            </form>
                        </div>
                    </div>
                    <input type="hidden" id="idPrevio" value="">
                    <input type="hidden" id="idActual" value="">  
                    <div class="col-sm-12 table-responsive contTabla" style="margin-top:10px">                        
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>    
                                <tr>
                                    <td class="oculto"><strong>Identificador</strong></td>
                                    <td class="cabeza" width="7%"></td>
                                    <td class="cabeza"><strong>Elemento Ficha</strong></td>
                                    <td class="cabeza"><strong>Obligatorio</strong></td>
                                    <td class="cabeza"><strong>Autogenerado</strong></td>                                    
                                </tr>
                                <tr>
                                    <th class="oculto">Identificador</th>
                                    <th class="cabeza" width="7%"></th>
                                    <th class="cabeza">Elemento Ficha</th>
                                    <th class="cabeza">Obligatorio</th>
                                    <th class="cabeza">Autogenerado</th>                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sql = "SELECT    fi.id_unico,
                                                        fi.ficha,
                                                        fi.obligatorio,
                                                        fi.autogenerado,
                                                        elm.id_unico,
                                                        elm.nombre,                                                        
                                                        f.id_unico,
                                                        f.descripcion
                                        FROM gf_ficha_inventario fi
                                        LEFT JOIN gf_elemento_ficha elm ON fi.elementoficha = elm.id_unico
                                        LEFT JOIN gf_ficha f ON fi.ficha = f.id_unico
                                        WHERE md5(f.id_unico) = '".$_GET['ficha']."'"; 
                                $result = $mysqli->query($sql);
                                while($row = $result->fetch_row()){?>
                                <tr>
                                    <td class="oculto">
                                            <?php echo $row[0]; ?>
                                    </td>
                                    <td class="campos ">
                                        <a href="#<?php echo $row[0];?>" onclick="javascript:eliminar(<?php echo $row[0]; ?>)" title="Eliminar">
                                            <li class="glyphicon glyphicon-trash"></li>
                                        </a>
                                        <a href="#<?php echo $row[0];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>);">
                                            <li class="glyphicon glyphicon-edit"></li>
                                        </a>
                                    </td>
                                    <td class="campos text-left">
                                        <?php                                         
                                              echo '<label class="valorLabel col-sm-12" style="font-weight:normal"  id="lblElementoFicha'.$row[0].'">'.ucwords(strtolower($row[5])).'</label>';
                                        ?> 
                                        <select style="display: none;padding:2px" class="col-sm-10 campoD" id="sltelementoficha<?php echo $row[0]; ?>">
                                            <option value="<?php echo $row[4]; ?>"><?php echo ucwords(strtolower($row[5])); ?></option>
                                            <?php                                                                                     
                                            $sqlE="select elm.id_unico,elm.nombre from gf_elemento_ficha elm where elm.id_unico!=$row[4]";
                                            $resultE = $mysqli->query($sqlE);
                                            while ($rowE = mysqli_fetch_row($resultE)) {
                                                echo '<option value="'.$rowE[0].'">'.ucwords(strtolower($rowE[1])).'</option>';
                                            }
                                            ?>
                                        </select>                                      
                                    </td>
                                    <td class="campos text-left">                                        
                                        <?php 
                                        $obligatorio = (int) $row[2];                                                      
                                        switch ($obligatorio){
                                            case 1:
                                                echo '<label class="valorLabel col-sm-12" style="font-weight:normal"  id="lblObligatorio'.$row[0].'">SI</label>'; ?>                                                
                                                <script type="text/javascript">
                                                    $(document).ready(function(){
                                                       $("input:radio[name=optObligatorio<?php echo $row[0]; ?>]").filter("[value=1]").prop("checked", true);
                                                    });
                                                </script>
                                                <div class="col-sm-1 radios" id="radios<?php echo $row[0]; ?>" style="display: none">                                                    
                                                    <input type="radio" name="optObligatorio<?php echo $row[0]; ?>" value="1" id="optOblA<?php echo $row[0]; ?>" title="Indique si es obligatorio" checked>SI
                                                    <input type="radio" name="optObligatorio<?php echo $row[0]; ?>" value="2" id="optOblB<?php echo $row[0]; ?>" title="Indique si no es obligatorio">NO
                                                </div>                                                
                                        <?php
                                                break;
                                            case 2:
                                                echo '<label class="valorLabel col-sm-12" style="font-weight:normal"  id="lblObligatorio'.$row[0].'">NO</label>'; ?>
                                                <script type="text/javascript">
                                                    $(document).ready(function(){
                                                       $("input:radio[name=optObligatorio<?php echo $row[0]; ?>]").filter("[value=2]").prop("checked", true);
                                                    });
                                                </script>
                                                <div class="col-sm-1 " style="display: none" id="radios<?php echo $row[0]; ?>">
                                                    <input type="radio" name="optObligatorio<?php echo $row[0]; ?>" value="1" id="optOblA<?php echo $row[0]; ?>" title="Indique si es obligatorio"/>SI
                                                    <input type="radio" name="optObligatorio<?php echo $row[0]; ?>" value="2" id="optOblB<?php echo $row[0]; ?>" title="Indique si no es obligatorio" checked/>NO
                                                </div>
                                        <?php
                                                break;
                                        }                                             
                                        ?>                                        
                                    </td>
                                    <td class="campos text-left">
                                        <?php 
                                        switch ($row[3]){
                                            case 1:
                                                echo '<label class="valorLabel col-sm-12" style="font-weight:normal"  id="lblAutoG'.$row[0].'">SI</label>'; ?>
                                                <script type="text/javascript">
                                                    $(document).ready(function(){
                                                       $("input:radio[name=optAutoGenerado<?php echo $row[0]; ?>]").filter("[value=1]").prop("checked", true);
                                                    });
                                                </script>
                                                <div class="col-sm-10 radios" id="radiosGenerado<?php echo $row[0]; ?>" style="display: none">                                                    
                                                    <input type="radio" name="optAutoGenerado<?php echo $row[0]; ?>" value="1" id="optAutoGA<?php echo $row[0]; ?>" title="Indique si es obligatorio" checked/>SI
                                                    <input type="radio" name="optAutoGenerado<?php echo $row[0]; ?>" value="2" id="optAutoGB<?php echo $row[0]; ?>" title="Indique si no es obligatorio"/>NO
                                                </div>
                                        <?php
                                                break;
                                            case 2:
                                                echo '<label class="valorLabel col-sm-12" style="font-weight:normal"  id="lblAutoG'.$row[0].'">NO</label>'; ?>
                                                <script type="text/javascript">
                                                    $(document).ready(function(){
                                                       $("input:radio[name=optAutoGenerado<?php echo $row[0]; ?>]").filter("[value=2]").prop("checked", true);
                                                    });
                                                </script>
                                                <div class="col-sm-10 radios" id="radiosGenerado<?php echo $row[0]; ?>" style="display: none">                                                    
                                                    <input type="radio" name="optAutoGenerado<?php echo $row[0]; ?>" value="1" id="optAutoGA<?php echo $row[0]; ?>" title="Indique si es obligatorio"/>SI
                                                    <input type="radio" name="optAutoGenerado<?php echo $row[0]; ?>" value="2" id="optAutoGB<?php echo $row[0]; ?>" title="Indique si no es obligatorio" checked/>NO
                                                </div>
                                                <?php
                                                break;
                                        }                                             
                                        ?>
                                                <div >
                                            <table id="tab<?php echo $row[0] ?>" class="col-sm-1" style="padding:0px;background-color:transparent;background:transparent;">
                                                <tbody>
                                                    <tr style="background-color:transparent;">
                                                        <td style="background-color:transparent;">
                                                            <a  href="#<?php echo $row[0];?>" title="Guardar" id="guardar<?php echo $row[0]; ?>" style="display: none;" onclick="javascript:guardarCambios(<?php echo $row[0]; ?>)">
                                                                <li class="glyphicon glyphicon-floppy-disk"></li>
                                                            </a>
                                                        </td>
                                                        <td style="background-color:transparent;">
                                                            <a href="#<?php echo $row[0];?>" title="Cancelar" id="cancelar<?php echo $row[0] ?>" style="display: none" onclick="javascript:cancelar(<?php echo $row[0];?>)" >
                                                                <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>                                                                    
                                </tr>
                                <?php    
                                }
                                ?>
                            </tbody>
                        </table>                        
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            function modificar(id){
                if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
                    //labels
                    var lblObligatorioE = "lblObligatorio"+$("#idPrevio").val();
                    var lblAutoGE = "lblAutoG"+$("#idPrevio").val();                                        
                    
                    //Campos
                    var radiosG = "radios"+$("#idPrevio").val();
                    var radiosGn = "radiosGenerado"+$("#idPrevio").val();                    
                    
                     //Campos para cancelar y guardar cambios
                    var guardarC = 'guardar'+$("#idPrevio").val();
                    var cancelarC = 'cancelar'+$("#idPrevio").val();
                    var tablaC = 'tab'+$("#idPrevio").val();
                    
                    //Se muestran los label
                    $("#"+lblObligatorioE).css('display','block');
                    $("#"+lblAutoGE).css('display','block');                                        
                    
                    //Se ocultan los campos
                    $("#"+radiosG).css('display','none');
                    $("#"+radiosGn).css('display','none');                    
                    
                    //se mantienen ocultos
                    $("#"+guardarC).css('display','none');
                    $("#"+cancelarC).css('display','none');
                    $("#"+tablaC).css('display','none');
                }
                
                var lblObligatorio = "lblObligatorio"+id;
                var lblAutoG = "lblAutoG"+id;                
                
                //Se ocultan los labels
                $("#"+lblObligatorio).css('display','none');
                $("#"+lblAutoG).css('display','none');                
                
                //Campos
                var radios = "radios"+id;
                var radiosG = "radiosGenerado"+id;                
                
                //Se muestran los campos
                $("#"+radios).css('display','block');
                $("#"+radiosG).css('display','block');                
                
                //campos para cancelar y guardar cambios
                var guardar = 'guardar'+id;
                var cancelar = 'cancelar'+id;
                var tabla = 'tab'+id; 
                
                //Se muestran los campos
                $("#"+guardar).css('display','block');
                $("#"+cancelar).css('display','block');
                $("#"+tabla).css('display','block');
                
                $("#idActual").val(id);
                if($("#idPrevio").val() != id){
                    $("#idPrevio").val(id);   
                }                                
            }
            
            function eliminar(id){
                var result = '';
                $("#myModal").modal('show');
                $("#ver").click(function(){
                    $("#mymodal").modal('hide');
                    $.ajax({
                        type:"GET",
                        url:"json/eliminarFichaInventarioJson.php?id="+id,
                        success: function (data) {
                        result = JSON.parse(data);
                        if(result==true)
                          $("#mdlEliminado").modal('show');
                        else
                          $("#mdlNoeliminado").modal('show');
                        }
                    });
                });
            }
            
            function cancelar(id){                
                var lblObligatorio = "lblObligatorio"+id;
                var lblAutoG = "lblAutoG"+id;                
                var lblPlanInventario = "lblPlanInventario"+id; 
                
                $("#"+lblObligatorio).css('display','block');
                $("#"+lblAutoG).css('display','block');
                $("#"+lblPlanInventario).css('display','block');
                
                var radios = "radios"+id;
                var radiosG = "radiosGenerado"+id;                
                var sltPlanInventario = "sltplaninventario"+id;
                
                $("#"+radios).css('display','none');
                $("#"+radiosG).css('display','none');                
                $("#"+sltPlanInventario).css('display','none');
                
                var guardar = 'guardar'+id;
                var cancelar = 'cancelar'+id;
                var tabla = 'tab'+id;
                
                $("#"+guardar).css('display','none');
                $("#"+cancelar).css('display','none');
                $("#"+tabla).css('display','none');
            }
            
            function guardarCambios(id){
                var obligatorio = "input:radio[name=optObligatorio"+id+"]:checked";
                var autogenerado = "input:radio[name=optAutoGenerado"+id+"]:checked";                
                
                var form_data = {
                    id:id,
                    obligatorio:$(obligatorio).val(),
                    autogenerado:$(autogenerado).val()                    
                };                            
                
                var result = '';
                $.ajax({
                    type: 'POST',
                    url: "json/modificarFichaInventarioJson.php",
                    data: form_data,
                    success: function (data) {
                        result = JSON.parse(data);
                        console.log(data);
                        if(result===true){
                           $("#mdlModificado").modal('show'); 
                        }else{
                            $("#mdlNomodificado").modal('show'); 
                        }
                    }
                });
           }
        </script>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <div class="modal fade" id="myModal2" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mdlModificado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información modificada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mdlNomodificado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">          
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se ha podido modificar la información.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>¿Desea eliminar el registro seleccionado de Ficha Inventario?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mdlEliminado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información eliminada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mdlNoeliminado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $('#btnModifico').click(function(){
                document.location.reload();
            });
        </script>
        <script type="text/javascript">
            $('#btnNoModifico').click(function(){
                document.location.reload();
            });
        </script>
        <script type="text/javascript">
            $('#ver1').click(function(){
                document.location.reload();
            });
        </script>
        <script type="text/javascript">    
            $('#ver2').click(function(){  
                document.location.reload();
            });
        </script>
        <script type="text/javascript">
            $('#btnG').click(function(){
                document.location.reload();
            });
        </script>
        <script type="text/javascript">    
            $('#btnG2').click(function(){  
                document.location.reload();
            });
            
            function limpiarCampos(){
                $('#sltElementoFicha').prop('selectedIndex',0);                
                $("#optObl2").prop('checked', true);
                $("#optAutoG2").prop('checked', true);
            }
        </script>      
    </body>
    <?php require_once './footer.php'; ?>
</html>


