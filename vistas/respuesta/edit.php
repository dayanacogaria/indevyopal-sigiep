<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Respuesta</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/AAA.ico" />
</head>
<body>
    <div class="modal fade" id="mdlInsert" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p>Información modificada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnI" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlNInsert" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p>No se ha podido modidicar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnD" class="btn btn-default" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/jquery-ui.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" language="javascript" src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.js" type="text/javascript" charset="utf-8"></script>
    <?php
    $script = "";
    if(!empty($url)){
        $script .= "<script type=\"text/javascript\">";
        if($data == true){
            $script .= "\n\t\t$(\"#mdlInsert\").modal(\"show\");";
            $script .= "\n\t\t$(\"#btnI\").click(function(){";
            $script .= "\n\t\t\t$(\"#mdlInsert\").modal('hide');";
            $script .= "\n\t\t\twindow.location = '$url';";
            $script .= "\n\t\t});";
        }else{
            $script .= "\n\t\t$(\"#mdlNInsert\").modal(\"show\");";
            $script .= "\n\t\t$(\"#btnD\").click(function(){";
            $script .= "\n\t\t\t$(\"#mdlNInsert\").modal('hide');";
            $script .= "\n\t\t\twindow.history.go(-1)";
            $script .= "\n\t\t});";
        }
        $script .= "\n\t</script>\n";
        echo $script;
    }
    ?>
</body>
</html>
