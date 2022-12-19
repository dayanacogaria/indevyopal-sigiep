<div class="modal fade" id="mdlvideo" data-keyboard="false" data-backdrop="static" style="width: 41%; margin-left: 32%; margin-top: 6%;">
    <button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" id="closevideo"><span class="glyphicon glyphicon-remove"></span></button>
    <div class="embed-responsive embed-responsive-16by9">
        <?php         
        if (!empty($_GET['rutarack'])){
            $url = $_GET['rutarack'];
            $ruta = $_GET['rutarack'];            
        }else if (!empty($_GET['rutasphindex'])){                        
            $url = $_GET['rutasphindex'];
            $ruta = $_GET['rutasphindex'];
        }else{
            $url = $ruta;            
        }                
        if (filter_var($url, FILTER_VALIDATE_URL)) {?>
            <iframe class="embed-responsive-item"
                src="<?php echo $ruta ?>" 
                webkitallowfullscreen mozallowfullscreen allowfullscreen id="vdruta">
            </iframe>
        <?php } else {?>
            <video src="<?php echo $ruta ?>" controls id="vdruta"></video>
        <?php }?>
    </div>
</div>
<script>
    $('#openvideo').on('click', function(e){
        $("#mdlvideo").modal("show");
    });
    
    $('#closevideo').on('click', function(e){
        $('#vdruta').trigger('pause');
        $('iframe').attr('src', $('iframe').attr('src'));
    });
</script>