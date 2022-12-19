$(document).ready(function(){
    var validator = $("#form").validate({
        ignore: "",
        errorElement: "em",
        errorPlacement: function(error, element){
            error.addClass('help-block');
        },
        highlight: function(element, errorClass, validClass){
            var elem = $(element);
            if(elem.hasClass('select2-offscreen')){
                $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
            }else{
                $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                $(element).parents(".col-lg-1").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-1").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-1").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-2").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-2").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-2").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-3").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-3").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-3").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-4").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-4").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-4").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-5").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-5").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-6").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-6").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-6").addClass("has-error").removeClass('has-success');
            }
            if($(element).attr('type') == 'radio'){
                $(element.form).find("input[type=radio]").each(function(which){
                    $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                    $(this).addClass("has-error");
                });
            } else {
                $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                $(element).addClass("has-error");
            }
        },
        unhighlight:function(element, errorClass, validClass){
            var elem = $(element);
            if(elem.hasClass('select2-offscreen')){
                $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
            }else{
                $(element.form).find("label[for=" + element.id + "]").removeClass("has-error");
                $(element).parents(".col-lg-1").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-1").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-1").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-2").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-2").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-2").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-3").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-3").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-3").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-4").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-4").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-4").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-5").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-5").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-5").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-6").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-6").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-6").addClass('has-success').removeClass('has-error');
            }
            if($(element).attr('type') == 'radio'){
                $(element.form).find("input[type=radio]").each(function(which){
                    $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                    $(this).addClass("has-success").removeClass("has-error");
                });
            } else {
                $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                $(element).addClass("has-success").removeClass("has-error");
            }
        }
    });
});

$(document).ready(function(){
    var validator = $("#formTercero").validate({
        ignore: "",
        errorElement: "em",
        errorPlacement: function(error, element){
            error.addClass('help-block');
        },
        highlight: function(element, errorClass, validClass){
            var elem = $(element);
            if(elem.hasClass('select2-offscreen')){
                $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
            }else{
                $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                $(element).parents(".col-lg-1").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-1").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-1").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-2").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-2").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-2").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-3").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-3").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-3").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-4").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-4").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-4").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-5").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-5").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-6").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-6").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-6").addClass("has-error").removeClass('has-success');
            }
            if($(element).attr('type') == 'radio'){
                $(element.form).find("input[type=radio]").each(function(which){
                    $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                    $(this).addClass("has-error");
                });
            } else {
                $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                $(element).addClass("has-error");
            }
        },
        unhighlight:function(element, errorClass, validClass){
            var elem = $(element);
            if(elem.hasClass('select2-offscreen')){
                $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
            }else{
                $(element.form).find("label[for=" + element.id + "]").removeClass("has-error");
                $(element).parents(".col-lg-1").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-1").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-1").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-2").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-2").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-2").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-3").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-3").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-3").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-4").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-4").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-4").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-5").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-5").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-5").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-6").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-6").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-6").addClass('has-success').removeClass('has-error');
            }
            if($(element).attr('type') == 'radio'){
                $(element.form).find("input[type=radio]").each(function(which){
                    $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                    $(this).addClass("has-success").removeClass("has-error");
                });
            } else {
                $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                $(element).addClass("has-success").removeClass("has-error");
            }
        }
    });
});

$(document).ready(function(){
    var validator = $("#formD").validate({
        ignore: "",
        errorElement: "em",
        errorPlacement: function(error, element){
            error.addClass('help-block');
        },
        highlight: function(element, errorClass, validClass){
            var elem = $(element);
            if(elem.hasClass('select2-offscreen')){
                $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
            }else{
                $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                $(element).parents(".col-lg-1").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-1").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-1").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-2").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-2").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-2").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-3").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-3").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-3").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-4").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-4").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-4").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-5").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-5").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-6").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-6").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-6").addClass("has-error").removeClass('has-success');
            }
            if($(element).attr('type') == 'radio'){
                $(element.form).find("input[type=radio]").each(function(which){
                    $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                    $(this).addClass("has-error");
                });
            } else {
                $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                $(element).addClass("has-error");
            }
        },
        unhighlight:function(element, errorClass, validClass){
            var elem = $(element);
            if(elem.hasClass('select2-offscreen')){
                $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
            }else{
                $(element.form).find("label[for=" + element.id + "]").removeClass("has-error");
                $(element).parents(".col-lg-1").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-1").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-1").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-2").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-2").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-2").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-3").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-3").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-3").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-4").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-4").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-4").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-5").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-5").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-5").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-6").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-6").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-6").addClass('has-success').removeClass('has-error');
            }
            if($(element).attr('type') == 'radio'){
                $(element.form).find("input[type=radio]").each(function(which){
                    $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                    $(this).addClass("has-success").removeClass("has-error");
                });
            } else {
                $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                $(element).addClass("has-success").removeClass("has-error");
            }
        }
    });
});

$(document).ready(function(){
    var validator = $("#formPersona").validate({
        ignore: "",
        errorElement: "em",
        errorPlacement: function(error, element){
            error.addClass('help-block');
        },
        highlight: function(element, errorClass, validClass){
            var elem = $(element);
            if(elem.hasClass('select2-offscreen')){
                $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
            }else{
                $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                $(element).parents(".col-lg-1").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-1").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-1").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-2").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-2").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-2").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-3").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-3").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-3").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-4").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-4").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-4").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-5").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-5").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-lg-6").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-md-6").addClass("has-error").removeClass('has-success');
                $(element).parents(".col-sm-6").addClass("has-error").removeClass('has-success');
            }
            if($(element).attr('type') == 'radio'){
                $(element.form).find("input[type=radio]").each(function(which){
                    $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                    $(this).addClass("has-error");
                });
            } else {
                $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                $(element).addClass("has-error");
            }
        },
        unhighlight:function(element, errorClass, validClass){
            var elem = $(element);
            if(elem.hasClass('select2-offscreen')){
                $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
            }else{
                $(element.form).find("label[for=" + element.id + "]").removeClass("has-error");
                $(element).parents(".col-lg-1").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-1").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-1").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-2").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-2").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-2").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-3").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-3").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-3").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-4").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-4").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-4").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-5").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-5").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-5").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-lg-6").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-md-6").addClass('has-success').removeClass('has-error');
                $(element).parents(".col-sm-6").addClass('has-success').removeClass('has-error');
            }
            if($(element).attr('type') == 'radio'){
                $(element.form).find("input[type=radio]").each(function(which){
                    $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                    $(this).addClass("has-success").removeClass("has-error");
                });
            } else {
                $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                $(element).addClass("has-success").removeClass("has-error");
            }
        }
    });
});